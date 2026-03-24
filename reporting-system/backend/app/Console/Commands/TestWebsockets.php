<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\SolrClient;
use App\Events\SolrDataUpdated;

class TestWebsockets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:test-ws';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger a test Solr update and WebSocket event';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::first();
        if (!$user) {
            $this->error('No user found in the database. Please seed or create a user first.');
            return 1;
        }

        // Ensure the user has a tenant_id for the test to work
        if (!$user->tenant_id) {
            $user->tenant_id = 'tenant-1';
            $user->save();
            $this->warn('No tenant_id found for this user. Assigned "tenant-1" for this test.');
        }

        $solr = app(SolrClient::class);
        $id = 'ws_test_' . time();

        $this->info("Adding document $id to Solr (Tenant: {$user->tenant_id})...");

        try {
            $solr->add([[
                'id' => $id,
                'tenant_id_s' => $user->tenant_id,
                'source_s' => 'AF',
                'Name_s' => '🚀 Real-time WebSocket Test!',
                'Price_f' => (float)rand(100, 999),
                'SKU_s' => 'TEST-' . rand(1000, 9999),
                'Brand_Name_s' => 'DemoBrand',
                'Date_dt' => now()->format('Y-m-d\TH:i:s\Z')
            ]]);

            $this->info("Dispatching SolrDataUpdated event...");
            SolrDataUpdated::dispatch($user->tenant_id);

            $this->info("Test completed! Check your browser dashboard for the new record.");
        } catch (\Exception $e) {
            $this->error("Solr update failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
