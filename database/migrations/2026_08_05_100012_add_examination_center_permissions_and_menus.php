<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the "Examination Center" sidebar section (permissions_group) with one child group
     * per menu page, plus the admin.examination_center.* permissions used to gate each page.
     * All permissions are attached to the Admin role (role_id = 1). Teachers are NOT managed
     * through this table — teacher access to exams is guard-scoped (auth:teachers) and enforced
     * in controllers by ownership (teacher_id), not through the admin permission system.
     */
    public function up(): void
    {
        $parentId = DB::table('permissions_group')->where('name', 'examination_center')->value('id');
        if (!$parentId) {
            $parentId = DB::table('permissions_group')->insertGetId([
                'name' => 'examination_center',
                'name_ar' => 'مركز الامتحانات',
                'name_en' => 'Examination Center',
                'icon' => 'ki-duotone ki-abstract-26',
                'color' => 'warning',
                'sort' => 70,
                'parent_id' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // child menu pages under Examination Center
        $children = [
            ['name' => 'exam_dashboard', 'name_ar' => 'لوحة الامتحانات', 'name_en' => 'Dashboard', 'sort' => 1],
            ['name' => 'exam_placement_tests', 'name_ar' => 'بنك اختبارات تحديد المستوى', 'name_en' => 'Placement Tests', 'sort' => 2],
            ['name' => 'group_exams', 'name_ar' => 'امتحانات المجموعات', 'name_en' => 'Group Exams', 'sort' => 3],
            ['name' => 'exam_questions', 'name_ar' => 'بنك الأسئلة', 'name_en' => 'Question Bank', 'sort' => 4],
            ['name' => 'exam_skills', 'name_ar' => 'تصنيفات الأسئلة', 'name_en' => 'Question Categories', 'sort' => 5],
            ['name' => 'exam_attempts', 'name_ar' => 'محاولات الطلاب', 'name_en' => 'Attempts', 'sort' => 6],
            ['name' => 'exam_reviews', 'name_ar' => 'مراجعة الإجابات', 'name_en' => 'Reviews', 'sort' => 7],
            ['name' => 'exam_reports', 'name_ar' => 'التقارير والتحليلات', 'name_en' => 'Reports & Analytics', 'sort' => 8],
            ['name' => 'exam_settings', 'name_ar' => 'إعدادات الامتحانات', 'name_en' => 'Settings', 'sort' => 9],
        ];

        $childIds = [];
        foreach ($children as $child) {
            $id = DB::table('permissions_group')->where('name', $child['name'])->value('id');
            if (!$id) {
                $id = DB::table('permissions_group')->insertGetId([
                    'name' => $child['name'],
                    'name_ar' => $child['name_ar'],
                    'name_en' => $child['name_en'],
                    'icon' => null,
                    'color' => null,
                    'sort' => $child['sort'],
                    'parent_id' => $parentId,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('permissions_group')->where('id', $id)->update(['parent_id' => $parentId]);
            }
            $childIds[$child['name']] = $id;
        }

        // one permission set per child page: view/add/edit/delete/status; a couple of pages get extra actions
        $permissionMap = [
            'exam_dashboard'  => ['view'],
            'exam_placement_tests' => ['view', 'add', 'edit', 'delete', 'status', 'schedule', 'publish'],
            'group_exams'     => ['view', 'add', 'edit', 'delete', 'status', 'schedule', 'publish'],
            'exam_questions'  => ['view', 'add', 'edit', 'delete', 'status', 'import', 'export'],
            'exam_skills'     => ['view', 'add', 'edit', 'delete', 'status'],
            'exam_attempts'   => ['view'],
            'exam_reviews'    => ['view', 'grade', 'approve'],
            'exam_reports'    => ['view'],
            'exam_settings'   => ['view', 'edit'],
        ];

        $actionLabels = [
            'view' => 'عرض', 'add' => 'إضافة', 'edit' => 'تعديل', 'delete' => 'حذف', 'status' => 'تغيير الحالة',
            'schedule' => 'جدولة', 'publish' => 'نشر', 'import' => 'استيراد', 'export' => 'تصدير',
            'grade' => 'تصحيح', 'approve' => 'اعتماد المراجعات',
        ];

        foreach ($permissionMap as $group => $actions) {
            foreach ($actions as $action) {
                $permName = "admin.{$group}.{$action}";
                $permId = DB::table('permissions')->where('name', $permName)->value('id');
                if (!$permId) {
                    $permId = DB::table('permissions')->insertGetId([
                        'name' => $permName,
                        'name_ar' => $actionLabels[$action] . ' - ' . $children[array_search($group, array_column($children, 'name'))]['name_ar'],
                        'guard_name' => 'admin',
                        'group_id' => $childIds[$group],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $hasRole = DB::table('role_has_permissions')
                    ->where('permission_id', $permId)
                    ->where('role_id', 1) // Admin role
                    ->exists();
                if (!$hasRole) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permId,
                        'role_id' => 1,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $groupNames = [
            'examination_center', 'exam_dashboard', 'exam_placement_tests', 'group_exams', 'exam_questions',
            'exam_skills', 'exam_attempts', 'exam_reviews', 'exam_reports', 'exam_settings',
        ];

        $groupIds = DB::table('permissions_group')->whereIn('name', $groupNames)->pluck('id');

        DB::table('permissions')->whereIn('group_id', $groupIds)->delete();
        DB::table('permissions_group')->whereIn('id', $groupIds)->delete();
    }
};
