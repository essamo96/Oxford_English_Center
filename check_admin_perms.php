<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$admin = User::where('role', 1)->first();
if (!$admin) { die("No admin found\n"); }

echo "=== Admin: " . $admin->email . " ===\n\n";

echo "-- Roles:\n";
foreach ($admin->getRoleNames() as $role) {
    echo "  " . $role . "\n";
}

echo "\n-- All Permissions (" . $admin->getAllPermissions()->count() . " total):\n";
foreach ($admin->getAllPermissions() as $p) {
    echo "  " . $p->name . "\n";
}

echo "\n-- Key Checks:\n";
$checks = [
    'admin.students.view',
    'admin.students.add',
    'admin.students.edit',
    'admin.students.delete',
    'admin.students.status',
    'admin.dashboard.view',
    'admin.groups.view',
    'admin.teachers.view',
];
foreach ($checks as $perm) {
    $has = $admin->hasPermissionTo($perm, 'admin');
    echo "  " . ($has ? "✓" : "✗") . " " . $perm . "\n";
}
