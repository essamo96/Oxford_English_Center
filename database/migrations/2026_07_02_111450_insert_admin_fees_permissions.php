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
        $groupId = DB::table('permissions_group')->where('name', 'Financial')->value('id');
        if (!$groupId) {
            $groupId = DB::table('permissions_group')->insertGetId([
                'name' => 'Financial',
                'name_ar' => 'المالية',
                'color' => 'primary',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $permissions = [
            [
                'name' => 'admin.fees.view',
                'name_ar' => 'عرض إدارة الرسوم',
                'guard_name' => 'web',
                'group_id' => $groupId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin.fees.add',
                'name_ar' => 'إضافة رسوم',
                'guard_name' => 'web',
                'group_id' => $groupId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin.fees.edit',
                'name_ar' => 'تعديل الرسوم',
                'guard_name' => 'web',
                'group_id' => $groupId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin.fees.delete',
                'name_ar' => 'حذف الرسوم',
                'guard_name' => 'web',
                'group_id' => $groupId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin.fees.status',
                'name_ar' => 'حالة الرسوم',
                'guard_name' => 'web',
                'group_id' => $groupId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($permissions as $perm) {
            $exists = DB::table('permissions')->where('name', $perm['name'])->exists();
            if (!$exists) {
                $permissionId = DB::table('permissions')->insertGetId($perm);
                
                $roleExists = DB::table('roles')->where('id', 1)->exists();
                if ($roleExists) {
                    $hasPerm = DB::table('role_has_permissions')->where('permission_id', $permissionId)->where('role_id', 1)->exists();
                    if (!$hasPerm) {
                        DB::table('role_has_permissions')->insert([
                            'permission_id' => $permissionId,
                            'role_id' => 1
                        ]);
                    }
                }
            } else {
                $permissionId = DB::table('permissions')->where('name', $perm['name'])->value('id');
                $roleExists = DB::table('roles')->where('id', 1)->exists();
                if ($roleExists) {
                    $hasPerm = DB::table('role_has_permissions')->where('permission_id', $permissionId)->where('role_id', 1)->exists();
                    if (!$hasPerm) {
                        DB::table('role_has_permissions')->insert([
                            'permission_id' => $permissionId,
                            'role_id' => 1
                        ]);
                    }
                }
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
                'admin.fees.view',
                'admin.fees.add',
                'admin.fees.edit',
                'admin.fees.delete',
                'admin.fees.status'
            ])
            ->delete();
    }
};
