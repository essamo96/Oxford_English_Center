<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use App\Models\User;

$role = Role::where('name', 'Admin')->first();
if (!$role) {
    die("Admin role not found\n");
}

$admins = User::where('role', 1)->get();
foreach ($admins as $admin) {
    $admin->assignRole($role);
    echo "Assigned Admin role to: " . $admin->email . "\n";
}

echo "Done syncing " . $admins->count() . " admins.\n";
