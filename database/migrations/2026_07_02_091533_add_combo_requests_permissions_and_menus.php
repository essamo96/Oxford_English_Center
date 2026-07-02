<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Create Parent Group: طلبات كومبو
        $parentGroupId = DB::table('permissions_group')->where('name', 'combo_requests')->value('id');
        if (!$parentGroupId) {
            $parentGroupId = DB::table('permissions_group')->insertGetId([
                'name' => 'combo_requests',
                'name_ar' => 'طلبات كومبو',
                'icon' => 'ki-duotone ki-briefcase',
                'color' => 'primary',
                'sort' => 10, // Adjust sort to place it nicely in the sidebar
                'parent_id' => 0, // 0 for root
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Create Child Group 1: طلبات التسجيل
        $child1Id = DB::table('permissions_group')->where('name', 'standalone_registrations')->value('id');
        if (!$child1Id) {
            $child1Id = DB::table('permissions_group')->insertGetId([
                'name' => 'standalone_registrations',
                'name_ar' => 'طلبات التسجيل',
                'icon' => 'ki-duotone ki-document',
                'color' => 'success',
                'sort' => 1,
                'parent_id' => $parentGroupId,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
             // If it already exists, make sure it's under the parent group
             DB::table('permissions_group')->where('id', $child1Id)->update(['parent_id' => $parentGroupId]);
        }

        // 3. Create Child Group 2: أولياء الأمور
        $child2Id = DB::table('permissions_group')->where('name', 'combo_parents')->value('id');
        if (!$child2Id) {
            $child2Id = DB::table('permissions_group')->insertGetId([
                'name' => 'combo_parents',
                'name_ar' => 'أولياء الأمور',
                'icon' => 'ki-duotone ki-profile-user',
                'color' => 'info',
                'sort' => 2,
                'parent_id' => $parentGroupId,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
             DB::table('permissions_group')->where('id', $child2Id)->update(['parent_id' => $parentGroupId]);
        }

        // 4. Create Permissions for these groups
        $permissions = [
            [
                'name' => 'admin.standalone_registrations.view',
                'name_ar' => 'عرض طلبات التسجيل',
                'guard_name' => 'web',
                'group_id' => $child1Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin.combo_parents.view',
                'name_ar' => 'عرض أولياء الأمور (كومبو)',
                'guard_name' => 'web',
                'group_id' => $child2Id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($permissions as $perm) {
            $permId = DB::table('permissions')->where('name', $perm['name'])->value('id');
            if (!$permId) {
                $permId = DB::table('permissions')->insertGetId($perm);
            }
            
            // Attach to Admin role (role ID 1)
            $roleHasPerm = DB::table('role_has_permissions')
                             ->where('permission_id', $permId)
                             ->where('role_id', 1)
                             ->exists();
            if (!$roleHasPerm) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permId,
                    'role_id' => 1
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')
            ->whereIn('name', [
                'admin.standalone_registrations.view',
                'admin.combo_parents.view'
            ])
            ->delete();

        $parentGroupId = DB::table('permissions_group')->where('name', 'combo_requests')->value('id');
        if($parentGroupId) {
            DB::table('permissions_group')->where('parent_id', $parentGroupId)->delete();
            DB::table('permissions_group')->where('id', $parentGroupId)->delete();
        }
    }
};
