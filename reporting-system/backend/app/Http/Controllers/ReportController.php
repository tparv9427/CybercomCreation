<?php

namespace App\Http\Controllers;

use App\Services\SolrClient;
use App\Services\SolrQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private SolrClient $solr,
        private SolrQueryBuilder $qb
    ) {}

    private function formatDate(?string $val, bool $isEnd = false): string
    {
        if (!$val || $val === 'NOW') return 'NOW';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
            return $val . ($isEnd ? 'T23:59:59Z' : 'T00:00:00Z');
        }
        return $val;
    }

    private function applyTenantFilter(array &$fqList): void
    {
        $user = auth()->user();
        if ($user && $user->tenant_id) {
            $fqList[] = "tenant_id_s:\"{$user->tenant_id}\"";
        }
    }

    public function fields(): JsonResponse
    {
        // Sample the first 100 docs to find all active fields from your 16 CSVs
        $result = $this->solr->query([
            'q'    => '*:*',
            'rows' => 100,
        ]);

        $docs = $result['response']['docs'] ?? [];
        $uniqueFields = [];

        foreach ($docs as $doc) {
            foreach (array_keys($doc) as $name) {
                // Skip internal Solr and metadata fields
                if (in_array($name, ['id', '_version_', '_root_', 'source_s'])) continue;
                $uniqueFields[$name] = true;
            }
        }

        $fields = [];
        $stringFields = array_filter(array_keys($uniqueFields), fn($f) => str_ends_with($f, '_s'));
        
        // Single pass facet for all string fields to detect categorical options
        $facetConfig = [];
        foreach ($stringFields as $sf) {
            $facetConfig[$sf] = ['type' => 'terms', 'field' => $sf, 'limit' => 51];
        }
        $facetResult = $this->solr->query(['q' => '*:*', 'rows' => 0, 'json.facet' => json_encode($facetConfig)]);
        $facets = $facetResult['facets'] ?? [];

        foreach (array_keys($uniqueFields) as $name) {
            $type = 'text';
            $options = null;

            if (str_ends_with($name, '_f') || str_ends_with($name, '_i') || str_ends_with($name, '_l')) {
                $type = 'number';
            } elseif (str_ends_with($name, '_dt')) {
                $type = 'date';
            } elseif (str_ends_with($name, '_b')) {
                $type = 'boolean';
            } elseif (str_ends_with($name, '_s')) {
                // If it's a string with limited unique values, it's categorical
                $buckets = $facets[$name]['buckets'] ?? [];
                if (!empty($buckets) && count($buckets) <= 50) {
                    $type = 'select';
                    $options = array_map(fn($b) => $b['val'], $buckets);
                    sort($options);
                }
            }

            $label = ucwords(str_replace('_', ' ', preg_replace('/_(s|f|i|l|dt|b|t)$/', '', $name)));

            $fields[] = [
                'name'    => $name,
                'label'   => $label,
                'type'    => $type,
                'options' => $options
            ];
        }

        usort($fields, fn($a, $b) => strcmp($a['label'], $b['label']));
        return response()->json($fields);
    }
    public function data(Request $request): JsonResponse
    {
        $rows   = min((int) $request->get('rows', 50), 500);
        $cursor = $request->get('cursor', '*'); // Default to '*' for the first page
        $sort   = $request->get('sort', 'id asc');

        // cursorMark requires a unique field in the sort. Let's ensure 'id' is always there.
        if (!str_contains($sort, 'id')) {
            $sort .= ', id asc';
        }

        $params = [
            'q'          => '*:*',
            'rows'       => $rows,
            'sort'       => $sort,
            'cursorMark' => $cursor,
            'wt'         => 'json',
        ];

        // Apply filters from react-querybuilder
        $fqList = [];
        $hasFilters = false;

        if ($filterJson = $request->get('filters')) {
            $filterGroup = json_decode($filterJson, true);
            if (!empty($filterGroup['rules'])) {
                $fq = $this->qb->build($filterGroup);
                if ($fq) {
                    $fqList[] = $fq;
                    $hasFilters = true;
                    
                    // Only highlight if there's an actual filter
                    $params['hl'] = 'true';
                    $params['hl.q'] = $fq;
                    $params['hl.fl'] = '*_s,*_t'; // Only highlight strings and text
                    $params['hl.simple.pre']  = '<mark>';
                    $params['hl.simple.post' ] = '</mark>';
                }
            }
        }

        // Date range filter
        if ($from = $request->get('date_from')) {
            $dateField = $request->get('date_field', 'Date_dt');
            $fmtFrom   = $this->formatDate($from, false);
            $fmtTo     = $this->formatDate($request->get('date_to', 'NOW'), true);
            $fqList[]  = "{$dateField}:[$fmtFrom TO $fmtTo]";
        }

        $this->applyTenantFilter($fqList);

        if (!empty($fqList)) {
            $params['fq'] = implode(' AND ', $fqList);
        }

        $result = $this->solr->query($params);

        $docs = $result['response']['docs'] ?? [];
        $highlights = $result['highlighting'] ?? [];

        if ($hasFilters && !empty($highlights)) {
            foreach ($docs as &$doc) {
                $id = $doc['id'] ?? null;
                if ($id && isset($highlights[$id])) {
                    foreach ($highlights[$id] as $field => $snippets) {
                        if (!empty($snippets)) {
                            $doc[$field] = $snippets[0];
                        }
                    }
                }
            }
        }

        return response()->json([
            'total'      => $result['response']['numFound']    ?? 0,
            'docs'       => $docs,
            'nextCursor' => $result['nextCursorMark']          ?? null,
        ]);
    }

    public function facets(Request $request): JsonResponse
    {
        $metric  = $request->get('metric', 'Price_f');
        $groupBy = $request->get('group_by', 'Brand_Name_s');
        $groups  = is_array($groupBy) ? $groupBy : explode(',', $groupBy);
        $groups  = array_map('trim', array_filter($groups));
        $limit   = (int) $request->get('limit', 15);
        $dateField = $request->get('date_field', 'Date_dt');

        // Build Recursive JSON Facet
        $jsonFacet = $this->buildPivotFacet($groups, $metric, $limit);

        $params = [
            'q'          => '*:*',
            'rows'       => 0,
            'json.facet' => json_encode($jsonFacet['categories'] ?? []),
        ];

        // Apply filters & Date ranges
        $fqList = [];
        if ($filterJson = $request->get('filters')) {
            $filterGroup = json_decode($filterJson, true);
            if (!empty($filterGroup['rules'])) {
                $fq = $this->qb->build($filterGroup);
                if ($fq) $fqList[] = $fq;
            }
        }

        if ($from = $request->get('date_from')) {
            $fmtFrom   = $this->formatDate($from, false);
            $fmtTo     = $this->formatDate($request->get('date_to', 'NOW'), true);
            $fqList[]  = "{$dateField}:[$fmtFrom TO $fmtTo]";
        }

        $this->applyTenantFilter($fqList);

        if (!empty($fqList)) {
            $params['fq'] = implode(' AND ', $fqList);
        }

        try {
            $result  = $this->solr->query($params);
            $buckets = $result['facets']['buckets'] ?? [];
            
            $data = $this->mapBuckets($buckets);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function suggest(Request $request): JsonResponse
    {
        $field = $request->get('field');
        $query = $request->get('q', '');
        
        if (!$field) {
            return response()->json([]);
        }

        $jsonFacet = [
            'suggestions' => [
                'type'   => 'terms',
                'field'  => $field,
                'limit'  => 10,
            ]
        ];

        if ($query !== '') {
            $jsonFacet['suggestions']['prefix'] = $query;
        }

        $params = [
            'q'          => '*:*',
            'rows'       => 0,
            'json.facet' => json_encode($jsonFacet),
        ];

        $fqList = [];
        $this->applyTenantFilter($fqList);
        if (!empty($fqList)) {
            $params['fq'] = implode(' AND ', $fqList);
        }

        try {
            $result  = $this->solr->query($params);
            $buckets = $result['facets']['suggestions']['buckets'] ?? [];
            $data    = array_map(fn($b) => $b['val'], $buckets);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    /**
     * Recursive Facet Mapping
     */
    private function mapBuckets(array $buckets): array
    {
        return array_map(function ($b) {
            $data = [
                'label' => $b['val'],
                'value' => round($b['y_val'] ?? 0, 2),
            ];
            
            if (isset($b['sub_pivot']['buckets'])) {
                $data['subs'] = $this->mapBuckets($b['sub_pivot']['buckets']);
            }
            
            return $data;
        }, $buckets);
    }

    /**
     * Recursive Pivot Facet Builder
     */
    private function buildPivotFacet(array $fields, string $metric, int $limit): array
    {
        if (empty($fields)) return [];
        $field = array_shift($fields);
        
        $facet = [
            'type'   => 'terms',
            'field'  => $field,
            'limit'  => $limit,
            'facet'  => [
                'y_val' => "avg($metric)"
            ]
        ];
        
        if (!empty($fields)) {
            // Further levels have smaller limits to avoid JSON bloat
            $facet['facet']['sub_pivot'] = $this->buildPivotFacet($fields, $metric, 5)['categories'];
        }
        
        return ['categories' => $facet];
    }

    public function compare(Request $request): JsonResponse
    {
        $field   = $request->get('field', 'Price_f');
        $groupBy = $request->get('group_by', 'Brand_Name_s');
        $dateField = $request->get('date_field', 'Date_dt');

        $rangeA = [
            'from' => $this->formatDate($request->get('date_from_a')),
            'to'   => $this->formatDate($request->get('date_to_a'), true)
        ];
        $rangeB = [
            'from' => $this->formatDate($request->get('date_from_b')),
            'to'   => $this->formatDate($request->get('date_to_b'), true)
        ];

        if ($rangeA['from'] === 'NOW' || $rangeB['from'] === 'NOW') {
            return response()->json(['error' => 'Start dates are required for both periods.'], 400);
        }

        // We use "filter" sub-facets to compare two different time slices
        $jsonFacet = [
            'categories' => [
                'type'   => 'terms',
                'field'  => $groupBy,
                'limit'  => 50,
                'facet'  => [
                    'period_a' => [
                        'type'   => 'filter',
                        'filter' => "{$dateField}:[{$rangeA['from']} TO {$rangeA['to']}]",
                        'facet'  => [ 'avg_val' => "avg($field)" ]
                    ],
                    'period_b' => [
                        'type'   => 'filter',
                        'filter' => "{$dateField}:[{$rangeB['from']} TO {$rangeB['to']}]",
                        'facet'  => [ 'avg_val' => "avg($field)" ]
                    ]
                ]
            ]
        ];

        $params = [
            'q'          => '*:*',
            'rows'       => 0,
            'json.facet' => json_encode($jsonFacet),
        ];

        $fqList = [];

        if ($filterJson = $request->get('filters')) {
            $filterGroup = json_decode($filterJson, true);
            if (!empty($filterGroup['rules'])) {
                $fq = $this->qb->build($filterGroup);
                if ($fq) $fqList[] = $fq;
            }
        }

        $this->applyTenantFilter($fqList);
        if (!empty($fqList)) $params['fq'] = implode(' AND ', $fqList);

        $result = $this->solr->query($params);
        $buckets = $result['facets']['categories']['buckets'] ?? [];

        $data = [];
        foreach ($buckets as $bucket) {
            $valA = $bucket['period_a']['avg_val'] ?? 0;
            $valB = $bucket['period_b']['avg_val'] ?? 0;
            
            // Skip categories that have no data in either period
            if ($valA == 0 && $valB == 0) continue;

            $diff   = $valB - $valA;
            $change = $valA != 0 ? ($diff / $valA) * 100 : 0;

            $data[] = [
                'group'      => $bucket['val'],
                'period_a'   => round($valA, 2),
                'period_b'   => round($valB, 2),
                'diff'       => round($diff, 2),
                'pct_change' => round($change, 2)
            ];
        }

        return response()->json($data);
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $params = [
            'q'    => '*:*',
            'rows' => 20000,
            'start'=> 0,
            'wt'   => 'json',
        ];

        $fqList = [];
        if ($filterJson = $request->get('filters')) {
            $filterGroup = json_decode($filterJson, true);
            if (!empty($filterGroup['rules'])) {
                $fq = $this->qb->build($filterGroup);
                if ($fq) $fqList[] = $fq;
            }
        }
        
        if ($from = $request->get('date_from')) {
            $dateField = $request->get('date_field', 'Date_dt');
            $fmtFrom   = $this->formatDate($from, false);
            $fmtTo     = $this->formatDate($request->get('date_to', 'NOW'), true);
            $fqList[]  = "{$dateField}:[$fmtFrom TO $fmtTo]";
        }

        $this->applyTenantFilter($fqList);

        if (!empty($fqList)) {
            $params['fq'] = implode(' AND ', $fqList);
        }

        $result = $this->solr->query($params);
        $docs   = $result['response']['docs'] ?? [];

        $response = response()->streamDownload(function () use ($docs) {
            $out = fopen('php://output', 'w');
            if (!empty($docs)) {
                fputcsv($out, array_keys($docs[0]));
            }
            foreach ($docs as $doc) {
                $flatDoc = [];
                foreach ($doc as $key => $val) {
                    $flatDoc[$key] = is_array($val) ? implode(', ', $val) : (string)$val;
                }
                fputcsv($out, $flatDoc);
            }
            fclose($out);
        }, 'report_export.csv', [
            'Content-Type' => 'text/csv',
        ]);

        \App\Models\AuditLog::create([
            'action' => 'export_report',
            'ip_address' => $request->ip(),
            'details' => ['filters' => $request->get('filters')]
        ]);

        return $response;
    }
}