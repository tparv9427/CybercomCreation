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
        foreach (array_keys($uniqueFields) as $name) {
            
            // Map Solr suffixes to frontend types
            $type = 'text';
            if (str_ends_with($name, '_f') || str_ends_with($name, '_i') || str_ends_with($name, '_l')) {
                $type = 'number';
            } elseif (str_ends_with($name, '_dt')) {
                $type = 'date';
            } elseif (str_ends_with($name, '_b')) {
                $type = 'boolean';
            } elseif (str_ends_with($name, '_s')) {
                $type = 'text'; // Keep as text or select
            }

            // Cleanup label for humans (remove suffixes and underscores)
            $label = $name;
            $label = preg_replace('/_(s|f|i|l|dt|b|t)$/', '', $label);
            $label = ucwords(str_replace('_', ' ', $label));

            $fields[] = [
                'name'  => $name,
                'label' => $label,
                'type'  => $type,
            ];
        }

        // Sort fields alphabetically by label
        usort($fields, fn($a, $b) => strcmp($a['label'], $b['label']));

        return response()->json($fields);
    }
    public function data(Request $request): JsonResponse
    {
        $rows  = min((int) $request->get('rows', 50), 500);
        $start = (int) $request->get('start', 0);
        $sort  = $request->get('sort', 'id asc');

        $params = [
            'q'    => '*:*',
            'rows' => $rows,
            'start'=> $start,
            'sort' => $sort,
            'wt'   => 'json',
        ];

        // Apply filters from react-querybuilder
        $fqList = [];

        if ($filterJson = $request->get('filters')) {
            $filterGroup = json_decode($filterJson, true);
            if (!empty($filterGroup['rules'])) {
                $fq = $this->qb->build($filterGroup);
                if ($fq) $fqList[] = $fq;
            }
        }

        // Date range filter
        if ($from = $request->get('date_from')) {
            $dateField = $request->get('date_field', 'Date_dt');
            $to        = $request->get('date_to', 'NOW');
            $fqList[]  = "{$dateField}:[$from TO $to]";
        }

        if (!empty($fqList)) {
            $params['fq'] = implode(' AND ', $fqList);
        }

        $result = $this->solr->query($params);

        return response()->json([
            'total' => $result['response']['numFound'] ?? 0,
            'docs'  => $result['response']['docs']     ?? [],
            'start' => $result['response']['start']    ?? 0,
        ]);
    }

    public function facets(Request $request): JsonResponse
    {
        $field = $request->get('field', 'Brand_Name_s');
        $limit = (int) $request->get('limit', 50);

        $params = [
            'q'             => '*:*',
            'facet.field'   => $field,
            'facet.limit'   => $limit,
            'facet.mincount'=> 1,
            'wt'            => 'json',
        ];

        // Apply same filters if provided
        if ($filterJson = $request->get('filters')) {
            $filterGroup = json_decode($filterJson, true);
            if (!empty($filterGroup['rules'])) {
                $fq = $this->qb->build($filterGroup);
                if ($fq) $params['fq'] = $fq;
            }
        }

        $result = $this->solr->facet($params);
        $facets = $result['facet_counts']['facet_fields'][$field] ?? [];
        
        // Convert flat array [key, count, key, count] to assoc
        $data = [];
        for ($i = 0; $i < count($facets); $i += 2) {
            $data[] = [
                'value' => $facets[$i],
                'count' => $facets[$i+1],
            ];
        }

        return response()->json($data);
    }

    public function compare(Request $request): JsonResponse
    {
        $field   = $request->get('field', 'Price_f');
        $groupBy = $request->get('group_by', 'Brand_Name_s');
        $dateField = $request->get('date_field', 'Date_dt');

        // Helper to ensure dates are in Solr ISO format (YYYY-MM-DDTHH:MM:SSZ)
        $formatDate = function($val, $isEnd = false) {
            if (!$val || $val === 'NOW') return 'NOW';
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
                return $val . ($isEnd ? 'T23:59:59Z' : 'T00:00:00Z');
            }
            return $val;
        };

        $rangeA = [
            'from' => $formatDate($request->get('date_from_a')),
            'to'   => $formatDate($request->get('date_to_a'), true)
        ];
        $rangeB = [
            'from' => $formatDate($request->get('date_from_b')),
            'to'   => $formatDate($request->get('date_to_b'), true)
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

        if ($filterJson = $request->get('filters')) {
            $filterGroup = json_decode($filterJson, true);
            if (!empty($filterGroup['rules'])) {
                $fq = $this->qb->build($filterGroup);
                if ($fq) $params['fq'] = $fq;
            }
        }

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
            'rows' => 100000,
            'start'=> 0,
            'wt'   => 'json',
        ];

        if ($filterJson = $request->get('filters')) {
            $filterGroup = json_decode($filterJson, true);
            if (!empty($filterGroup['rules'])) {
                $fq = $this->qb->build($filterGroup);
                if ($fq) $params['fq'] = $fq;
            }
        }

        $result = $this->solr->query($params);
        $docs   = $result['response']['docs'] ?? [];

        return response()->streamDownload(function () use ($docs) {
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
    }
}