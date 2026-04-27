<?php

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailCampaignAdminMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert the menu group if it doesn't exist
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

        // Insert the required permissions linked to this group
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

        // Clear Spatie Permission Cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Assign basic permission to Role ID 1 (Usually Super Admin)
        $roleExists = \DB::table('roles')->where('id', 1)->first();
        if ($roleExists) {
             $permission = \DB::table('permissions')->where('name', 'admin.email_campaigns.view')->first();
             if($permission){
                 $rolePermissionExists = \DB::table('role_has_permissions')
                        ->where('permission_id', $permission->id)
                        ->where('role_id', 1)
                        ->exists();

                 if(!$rolePermissionExists){
                     \DB::table('role_has_permissions')->insert([
                         'permission_id' => $permission->id,
                         'role_id' => 1
                     ]);
                 }
             }
        }
    }
}
