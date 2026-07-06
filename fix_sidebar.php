<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 1. Get the parent combo_requests
$parent = \App\Models\PermissionsGroup::where('name', 'combo_requests')->first();

// 2. Create the new child group
$newGroup = \App\Models\PermissionsGroup::firstOrCreate(
    ['name' => 'standalone_registrations.payments'],
    ['name_ar' => 'متابعة الدفعات (Oxford)', 'parent_id' => $parent->id, 'status' => 1]
);

// 3. Find the permission we created earlier
$perm = \Spatie\Permission\Models\Permission::where('name', 'admin.standalone_registrations.payments.view')->first();
if ($perm) {
    // Update its group_id
    $perm->group_id = $newGroup->id;
    $perm->save();
}

// 4. Delete the wrong group I created previously (admin.standalone_registrations)
\App\Models\PermissionsGroup::where('name', 'admin.standalone_registrations')->delete();

app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
\Illuminate\Support\Facades\Cache::forget('spatie.permission.cache');
\Illuminate\Support\Facades\Artisan::call('optimize:clear');

echo "Done fixing the group and permission structure!\n";
