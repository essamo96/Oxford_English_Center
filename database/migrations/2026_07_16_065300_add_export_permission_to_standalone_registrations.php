<?php

use Illuminate\Database\Migrations\Migration;
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
        $groupId = DB::table('permissions_group')->where('name', 'standalone_registrations')->value('id');

        $permName = 'admin.standalone_registrations.export';
        $permId = DB::table('permissions')->where('name', $permName)->value('id');

        if (!$permId) {
            $permId = DB::table('permissions')->insertGetId([
                'name' => $permName,
                'name_ar' => 'تصدير طلبات التسجيل (اكسل)',
                'guard_name' => 'web',
                'group_id' => $groupId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Attach to Admin role (role ID 1)
        $roleExists = DB::table('roles')->where('id', 1)->exists();
        if ($roleExists) {
            $hasPerm = DB::table('role_has_permissions')
                ->where('permission_id', $permId)
                ->where('role_id', 1)
                ->exists();
            if (!$hasPerm) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permId,
                    'role_id' => 1,
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
        $permId = DB::table('permissions')->where('name', 'admin.standalone_registrations.export')->value('id');
        if ($permId) {
            DB::table('role_has_permissions')->where('permission_id', $permId)->delete();
            DB::table('permissions')->where('id', $permId)->delete();
        }
    }
};
