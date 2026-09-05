<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\BulkCampaign::whereNotIn('status', ['completed', 'failed', 'scheduled'])
    ->update([
        'delay_min' => 15,
        'delay_max' => 20
    ]);

echo "Active campaign delays updated to 15-20 seconds.\n";
