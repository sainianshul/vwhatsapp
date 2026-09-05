<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$accounts = \App\Models\WhatsAppAccount::all();
foreach ($accounts as $a) {
    echo "Account #{$a->id} session={$a->session_id} phone={$a->phone_number} push_name={$a->push_name} status={$a->status} deleted=" . ($a->trashed() ? 'yes' : 'no') . "\n";
}
