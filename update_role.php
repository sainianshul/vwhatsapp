<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'admin@admin.com')->first();
if ($user) {
    $user->role = 'admin';
    $user->save();
    echo "Role updated successfully\n";
} else {
    echo "User not found\n";
}
