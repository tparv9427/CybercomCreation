<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\SolrClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function __construct(private SolrClient $solr) {}

    public function index(): JsonResponse
    {
        // Total documents indexed in Solr
        $solrResult   = $this->solr->query(['q' => '*:*', 'rows' => 0]);
        $totalDocs    = $solrResult['response']['numFound'] ?? 0;

        // Total fields available
        $fieldsResult = $this->solr->query(['q' => '*:*', 'rows' => 1]);
        $sampleDoc    = $fieldsResult['response']['docs'][0] ?? [];
        $totalFields  = count(array_filter(
            array_keys($sampleDoc),
            fn($k) => !in_array($k, ['id', '_version_', '_root_'])
        ));

        // Saved views count
        $savedViews = DB::table('saved_views')->count();

        // Total exports logged
        $totalExports = AuditLog::where('action', 'export_report')->count();

        // Last 6 audit events
        $recentActivity = AuditLog::latest()
            ->limit(6)
            ->get(['action', 'details', 'ip_address', 'created_at'])
            ->map(function ($log) {
                return [
                    'action'     => $log->action,
                    'details'    => $log->details,
                    'ip_address' => $log->ip_address,
                    'time'       => $log->created_at?->diffForHumans() ?? 'just now',
                ];
            });

        // Field type breakdown for mini chart
        $fields = [];
        foreach (array_keys($sampleDoc) as $key) {
            if (in_array($key, ['id', '_version_', '_root_'])) continue;
            if (str_ends_with($key, '_dt'))      { $fields['date'][]   = $key; }
            elseif (str_ends_with($key, '_f') || str_ends_with($key, '_i') || str_ends_with($key, '_l')) { $fields['number'][] = $key; }
            elseif (str_ends_with($key, '_b'))   { $fields['boolean'][]= $key; }
            else                                  { $fields['text'][]   = $key; }
        }

        return response()->json([
            'total_docs'      => $totalDocs,
            'total_fields'    => $totalFields,
            'saved_views'     => $savedViews,
            'total_exports'   => $totalExports,
            'recent_activity' => $recentActivity,
            'field_types'     => [
                'text'    => count($fields['text']    ?? []),
                'number'  => count($fields['number']  ?? []),
                'date'    => count($fields['date']    ?? []),
                'boolean' => count($fields['boolean'] ?? []),
            ],
        ]);
    }
}
