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
        // First check if the permissions group "Financial" exists, or create it.
        // Assuming there is a permissions_group table based on my previous explorations.
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
                'name' => 'admin.financial.fee_settings.list',
                'name_ar' => 'عرض إعدادات الرسوم',
                'guard_name' => 'web',
                'permissions_group_id' => $groupId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin.financial.fee_types.list',
                'name_ar' => 'عرض أنواع الرسوم',
                'guard_name' => 'web',
                'permissions_group_id' => $groupId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($permissions as $perm) {
            $exists = DB::table('permissions')->where('name', $perm['name'])->exists();
            if (!$exists) {
                $permissionId = DB::table('permissions')->insertGetId($perm);
                // Attach to Admin role (usually role ID 1)
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permissionId,
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
                'admin.financial.fee_settings.list',
                'admin.financial.fee_types.list'
            ])
            ->delete();
    }
};
