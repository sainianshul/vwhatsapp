<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Revert Campaign #5 back to Schotech account #24
$campaign = \App\Models\BulkCampaign::find(5);
if ($campaign) {
    $campaign->update([
        'whatsapp_account_id' => 24,
    ]);
    echo "Campaign #5 reverted back to Account #24 (Schotech / 919217714451).\n";
}
