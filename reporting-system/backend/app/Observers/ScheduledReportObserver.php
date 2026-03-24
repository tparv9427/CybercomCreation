<?php

namespace App\Observers;

use App\Events\ModelUpdated;
use App\Models\ScheduledReport;

class ScheduledReportObserver
{
    public function created(ScheduledReport $report): void
    {
        $this->broadcast($report, 'created');
    }

    public function updated(ScheduledReport $report): void
    {
        $this->broadcast($report, 'updated');
    }

    public function deleted(ScheduledReport $report): void
    {
        $this->broadcast($report, 'deleted');
    }

    private function broadcast(ScheduledReport $report, string $action): void
    {
        $tenantId = $report->user?->tenant_id;
        if ($tenantId) {
            ModelUpdated::dispatch($tenantId, 'ScheduledReport', $action);
        }
    }
}
