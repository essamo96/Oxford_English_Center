<?php
// fix_permissions.php
$menuGroup = \DB::table('permissions_group')->where('name', 'email_campaigns')->first();

if (!$menuGroup) {
    $groupId = \DB::table('permissions_group')->insertGetId([
        'name' => 'email_campaigns',
        'name_ar' => 'حملات البريد',
        'parent_id' => 0,
        'sort' => 30, // Position it nicely
        'status' => 1,
    ]);
} else {
    $groupId = $menuGroup->id;
}

$permissions = [
    'admin.email_campaigns.view' => 'عرض حملات البريد',
];

foreach ($permissions as $name => $displayName) {
    $exists = \DB::table('permissions')->where('name', $name)->exists();
    if (!$exists) {
        \DB::table('permissions')->insert([
            'name' => $name,
            'guard_name' => 'admin',
            'group_id' => $groupId,
        ]);
    }
}

app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

$roleExists = \DB::table('roles')->where('id', 1)->first();
if ($roleExists) {
    $permission = \DB::table('permissions')->where('name', 'admin.email_campaigns.view')->first();
    if ($permission) {
        $rolePermissionExists = \DB::table('role_has_permissions')
            ->where('permission_id', $permission->id)
            ->where('role_id', 1)
            ->exists();

        if (!$rolePermissionExists) {
            \DB::table('role_has_permissions')->insert([
                'permission_id' => $permission->id,
                'role_id' => 1
            ]);
        }
    }
}
echo "Permissions inserted and assigned mapped to group ID: " . $groupId . "\n";
