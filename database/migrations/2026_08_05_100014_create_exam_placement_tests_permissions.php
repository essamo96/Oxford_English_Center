<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the "exam_placement_tests" sidebar entry + permissions for the new Examination
     * Center's Placement Test bank (distinct from the pre-existing "placement_tests" group, which
     * is the unrelated placement-test appointment/payment booking feature — see
     * 2026_08_05_100013_fix_placement_tests_group_collision.php for that mix-up and its fix).
     */
    public function up(): void
    {
        $parentId = DB::table('permissions_group')->where('name', 'examination_center')->value('id');

        $groupId = DB::table('permissions_group')->where('name', 'exam_placement_tests')->value('id');
        if (!$groupId) {
            $groupId = DB::table('permissions_group')->insertGetId([
                'name' => 'exam_placement_tests',
                'name_ar' => 'بنك اختبارات تحديد المستوى',
                'name_en' => 'Placement Tests',
                'icon' => null,
                'color' => null,
                'sort' => 2,
                'parent_id' => $parentId,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $actions = ['view', 'add', 'edit', 'delete', 'status', 'schedule', 'publish'];
        $actionLabels = [
            'view' => 'عرض', 'add' => 'إضافة', 'edit' => 'تعديل', 'delete' => 'حذف',
            'status' => 'تغيير الحالة', 'schedule' => 'جدولة', 'publish' => 'نشر',
        ];

        foreach ($actions as $action) {
            $permName = "admin.exam_placement_tests.{$action}";
            $permId = DB::table('permissions')->where('name', $permName)->value('id');
            if (!$permId) {
                $permId = DB::table('permissions')->insertGetId([
                    'name' => $permName,
                    'name_ar' => $actionLabels[$action] . ' - بنك اختبارات تحديد المستوى',
                    'guard_name' => 'admin',
                    'group_id' => $groupId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $hasRole = DB::table('role_has_permissions')->where('permission_id', $permId)->where('role_id', 1)->exists();
            if (!$hasRole) {
                DB::table('role_has_permissions')->insert(['permission_id' => $permId, 'role_id' => 1]);
            }
        }
    }

    public function down(): void
    {
        $groupId = DB::table('permissions_group')->where('name', 'exam_placement_tests')->value('id');
        DB::table('permissions')->where('group_id', $groupId)->delete();
        DB::table('permissions_group')->where('id', $groupId)->delete();
    }
};
