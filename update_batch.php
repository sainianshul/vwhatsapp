<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\BulkCampaign::whereNotIn('status', ['completed', 'failed', 'scheduled'])
    ->update([
        'batch_size' => 100000,
        'cooldown_minutes' => 0
    ]);

echo "Active campaign batch_size and cooldown updated to prevent 5-min idle crash.\n";
