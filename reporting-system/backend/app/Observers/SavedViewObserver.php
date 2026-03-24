<?php

namespace App\Observers;

use App\Events\ModelUpdated;
use App\Models\SavedView;

class SavedViewObserver
{
    public function created(SavedView $view): void
    {
        $this->broadcast($view, 'created');
    }

    public function updated(SavedView $view): void
    {
        $this->broadcast($view, 'updated');
    }

    public function deleted(SavedView $view): void
    {
        $this->broadcast($view, 'deleted');
    }

    private function broadcast(SavedView $view, string $action): void
    {
        $tenantId = $view->user?->tenant_id;
        if ($tenantId) {
            ModelUpdated::dispatch($tenantId, 'SavedView', $action);
        }
    }
}
