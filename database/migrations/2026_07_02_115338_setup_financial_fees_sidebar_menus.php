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
        // 1. Ensure 'Financial' parent group exists
        $financialGroupId = DB::table('permissions_group')->where('name', 'Financial')->value('id');
        if (!$financialGroupId) {
            $financialGroupId = DB::table('permissions_group')->insertGetId([
                'name' => 'Financial',
                'name_ar' => 'المالية',
                'color' => 'primary',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Remove the old `financial_fee_types` group and its permissions to avoid the JSON page bug
        $oldGroupId = DB::table('permissions_group')->where('name', 'financial_fee_types')->value('id');
        if ($oldGroupId) {
            $oldPermIds = DB::table('permissions')->where('group_id', $oldGroupId)->pluck('id');
            DB::table('role_has_permissions')->whereIn('permission_id', $oldPermIds)->delete();
            DB::table('permissions')->where('group_id', $oldGroupId)->delete();
            DB::table('permissions_group')->where('id', $oldGroupId)->delete();
        }

        // 3. Ensure 'financial_fees' group exists
        $feesGroupId = DB::table('permissions_group')->where('name', 'financial_fees')->value('id');
        if (!$feesGroupId) {
            $feesGroupId = DB::table('permissions_group')->insertGetId([
                'name' => 'financial_fees',
                'name_ar' => 'إعدادات أنواع الرسوم',
                'name_en' => 'Fee Types Settings',
                'icon' => 'bi bi-sliders',
                'color' => '#c474a0',
                'sort' => 8,
                'status' => 1,
                'parent_id' => $financialGroupId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Ensure 'admin.financial_fees.view' exists
        $viewPermId = DB::table('permissions')->where('name', 'admin.financial_fees.view')->value('id');
        if (!$viewPermId) {
            $viewPermId = DB::table('permissions')->insertGetId([
                'name' => 'admin.financial_fees.view',
                'name_ar' => 'عرض إعدادات الرسوم',
                'guard_name' => 'web',
                'group_id' => $feesGroupId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Assign to Admin Role (Role 1)
        $roleExists = DB::table('roles')->where('id', 1)->exists();
        if ($roleExists) {
            $hasPerm = DB::table('role_has_permissions')
                ->where('permission_id', $viewPermId)
                ->where('role_id', 1)
                ->exists();
                
            if (!$hasPerm) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $viewPermId,
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
        // Remove the permission and group
        $viewPermId = DB::table('permissions')->where('name', 'admin.financial_fees.view')->value('id');
        if ($viewPermId) {
            DB::table('role_has_permissions')->where('permission_id', $viewPermId)->delete();
            DB::table('permissions')->where('id', $viewPermId)->delete();
        }

        DB::table('permissions_group')->where('name', 'financial_fees')->delete();
    }
};
