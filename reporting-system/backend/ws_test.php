<?php
require 'vendor/autoload.php';
\ = require_once 'bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Console\Kernel::class);
\->bootstrap();

use App\Models\User;
use App\Services\SolrClient;
use App\Events\SolrDataUpdated;

\ = User::first();
if (!\) {
    echo 'Error: No user found in database.\n';
    exit(1);
}

\ = app(SolrClient::class);

// 1. Add record to Solr
\ = 'ws_demo_' . time();
\->add([[
    'id' => \,
    'tenant_id_s' => \->tenant_id,
    'product_name_s' => '🎉 WebSocket Live Update!',
    'sales_amount_d' => rand(100, 999),
    'created_at_dt' => now()->format('Y-m-d\TH:i:s\Z')
]]);

// 2. Broadcast the event
event(new SolrDataUpdated(\->tenant_id));

echo 'Successfully added record ' . \ . ' and broadcast update for tenant: ' . \->tenant_id . '\n';
