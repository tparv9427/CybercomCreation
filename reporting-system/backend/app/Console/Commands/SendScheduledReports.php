<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:send';

    protected $description = 'Generate and send scheduled reports via email';

    public function handle()
    {
        $this->info('Checking for due scheduled reports...');

        $scheduledReports = \App\Models\ScheduledReport::due()->with(['user', 'savedView'])->get();

        if ($scheduledReports->isEmpty()) {
            $this->info('No reports are due at this time.');
            return 0;
        }

        /** @var \App\Services\SolrClient $solr */
        $solr = app(\App\Services\SolrClient::class);
        /** @var \App\Services\SolrQueryBuilder $qb */
        $qb = app(\App\Services\SolrQueryBuilder::class);

        /** @var \App\Models\ScheduledReport $schedule */
        foreach ($scheduledReports as $schedule) {
            $user = $schedule->user;
            /** @var \App\Models\SavedView $view */
            $view = $schedule->savedView;

            if (!$view || !$user) {
                $this->warn("Skipping schedule #{$schedule->id}: Missing user or view.");
                continue;
            }

            $this->info("Processing report: {$view->name} (v{$view->version}) for {$user->email}");

            $config = $view->config ?? [];

            $params = [
                'q' => '*:*',
                'rows' => 100, // Limit for email content
                'wt' => 'json',
            ];

            // Build FQ filters (Global + User + Custom View)
            $fqList = [];
            if ($user->tenant_id) {
                $fqList[] = "tenant_id_s:\"{$user->tenant_id}\"";
            }
            if (!empty($config['filters']['rules'])) {
                $fq = $qb->build($config['filters']);
                if ($fq)
                    $fqList[] = $fq;
            }

            if (!empty($fqList)) {
                $params['fq'] = implode(' AND ', $fqList);
            }

            if (!empty($config['sort'])) {
                $params['sort'] = $config['sort'];
            }

            try {
                $results = $solr->query($params);
                $docs = $results['response']['docs'] ?? [];

                \Illuminate\Support\Facades\Mail::to($user->email)->send(
                    new \App\Mail\ReportMail($view, $docs)
                );

                $schedule->update(['last_sent_at' => now()]);

                \App\Models\AuditLog::log('send_scheduled_report', [
                    'user_id' => $user->id,
                    'view_id' => $view->id,
                    'frequency' => $schedule->frequency
                ]);

                $this->info("Successfully sent report to {$user->email}");

            } catch (\Exception $e) {
                $this->error("Failed to process report '{$view->name}': " . $e->getMessage());
            }
        }

        return 0;
    }
}
