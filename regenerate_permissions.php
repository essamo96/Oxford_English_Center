<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

$groups = DB::table('permissions_group')->get();
$actions = ['add', 'edit', 'delete', 'view', 'status', 'permissions', 'password', 'reply'];
$guard = 'admin';

echo "Generating permissions for " . count($groups) . " groups...\n";

foreach ($groups as $group) {
    $slug = $group->name;
    foreach ($actions as $action) {
        $permissionName = "admin.$slug.$action";
        
        // Use Spatie Permission model to ensure guard is set correctly
        Permission::firstOrCreate([
            'name' => $permissionName,
            'guard_name' => $guard,
            'group_id' => $group->id
        ]);
    }
}

echo "Permissions generated. Syncing to Admin role...\n";

$adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => $guard]);
$allPermissions = Permission::where('guard_name', $guard)->get();
$adminRole->syncPermissions($allPermissions);

echo "Admin role synced. Syncing to user moh12@mdit.ps...\n";

$user = User::where('email', 'moh12@mdit.ps')->first();
if ($user) {
    $user->syncRoles([$adminRole->name]);
    echo "User moh12@mdit.ps synced with Admin role.\n";
} else {
    echo "User moh12@mdit.ps not found.\n";
}

echo "Done.\n";
