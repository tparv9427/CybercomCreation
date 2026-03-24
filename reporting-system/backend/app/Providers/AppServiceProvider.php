<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SavedView;
use App\Models\ScheduledReport;
use App\Observers\SavedViewObserver;
use App\Observers\ScheduledReportObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SavedView::observe(SavedViewObserver::class);
        ScheduledReport::observe(ScheduledReportObserver::class);
    }
}
