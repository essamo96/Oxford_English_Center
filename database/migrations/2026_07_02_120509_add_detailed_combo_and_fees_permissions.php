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
        $groups = [
            'combo_requests' => 'طلبات كومبو',
            'standalone_registrations' => 'طلبات التسجيل',
            'combo_parents' => 'أولياء الأمور',
            'financial_fees' => 'إعدادات أنواع الرسوم',
        ];

        $actions = [
            'view' => 'عرض',
            'add' => 'إضافة',
            'edit' => 'تعديل',
            'delete' => 'حذف',
            'status' => 'تغيير الحالة',
        ];

        foreach ($groups as $groupName => $groupTitle) {
            $groupId = DB::table('permissions_group')->where('name', $groupName)->value('id');
            if ($groupId) {
                foreach ($actions as $action => $actionAr) {
                    $permName = "admin.{$groupName}.{$action}";
                    
                    $exists = DB::table('permissions')->where('name', $permName)->exists();
                    if (!$exists) {
                        $permId = DB::table('permissions')->insertGetId([
                            'name' => $permName,
                            'name_ar' => "{$actionAr} ({$groupTitle})",
                            'guard_name' => 'web',
                            'group_id' => $groupId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Assign to Admin role
                        $roleExists = DB::table('roles')->where('id', 1)->exists();
                        if ($roleExists) {
                            $hasPerm = DB::table('role_has_permissions')
                                ->where('permission_id', $permId)
                                ->where('role_id', 1)
                                ->exists();
                            if (!$hasPerm) {
                                DB::table('role_has_permissions')->insert([
                                    'permission_id' => $permId,
                                    'role_id' => 1
                                ]);
                            }
                        }
                    } else {
                        // Already exists, just ensure Admin has it
                        $permId = DB::table('permissions')->where('name', $permName)->value('id');
                        $roleExists = DB::table('roles')->where('id', 1)->exists();
                        if ($roleExists) {
                            $hasPerm = DB::table('role_has_permissions')
                                ->where('permission_id', $permId)
                                ->where('role_id', 1)
                                ->exists();
                            if (!$hasPerm) {
                                DB::table('role_has_permissions')->insert([
                                    'permission_id' => $permId,
                                    'role_id' => 1
                                ]);
                            }
                        }
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
        $groups = [
            'combo_requests',
            'standalone_registrations',
            'combo_parents',
            'financial_fees',
        ];

        $actions = ['view', 'add', 'edit', 'delete', 'status'];

        $permNames = [];
        foreach ($groups as $group) {
            foreach ($actions as $action) {
                // Don't delete view permissions that were there before, 
                // just delete add, edit, delete, status. 
                // But it's safer to just let them be, or delete specific ones.
                if ($action !== 'view') {
                    $permNames[] = "admin.{$group}.{$action}";
                }
            }
        }

        $permIds = DB::table('permissions')->whereIn('name', $permNames)->pluck('id');
        if ($permIds->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
            DB::table('permissions')->whereIn('id', $permIds)->delete();
        }
    }
};
