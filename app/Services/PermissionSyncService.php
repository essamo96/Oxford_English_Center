<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSyncService
{
    const GUARD = 'admin';

    // ─── Arabic labels for each action ────────────────────────────────────────
    protected array $actionAr = [
        'view'   => 'عرض',
        'add'    => 'إضافة',
        'edit'   => 'تعديل',
        'delete' => 'حذف',
        'status' => 'تغيير حالة',
        'verify' => 'تحقق من',
        'refund' => 'استرداد',
        'reply'  => 'رد على',
    ];

    // ─── Permission tree ───────────────────────────────────────────────────────
    //
    //  type = 'parent'  → group + admin.{name}.view only
    //  type = 'child'   → group + admin.{name}.view/add/edit/delete/status
    //  type = 'custom'  → group + exactly the listed actions
    //
    //  'parent' entries must precede any child that references them via 'parent'.
    // ──────────────────────────────────────────────────────────────────────────
    protected function tree(): array
    {
        return [

            // ══════════════════════════════════════════════════════════════════
            //  التقويم الدراسي  (standalone)
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'custom',
                'name'    => 'calendar',
                'name_ar' => 'التقويم الدراسي',
                'name_en' => 'Academic Calendar',
                'icon'    => 'ki-duotone ki-calendar-8',
                'sort'    => 5,
                'actions' => ['view'],
            ],

            // ══════════════════════════════════════════════════════════════════
            //  ادارة الاكاديمية
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'parent',
                'name'    => 'academy_management',
                'name_ar' => 'ادارة الاكاديمية',
                'name_en' => 'Academy Management',
                'icon'    => 'ki-duotone ki-book-open',
                'sort'    => 10,
            ],
            ['type' => 'child', 'name' => 'programs', 'name_ar' => 'البرامج',           'parent' => 'academy_management', 'sort' => 11],
            ['type' => 'child', 'name' => 'branches', 'name_ar' => 'الفروع',            'parent' => 'academy_management', 'sort' => 12],
            ['type' => 'child', 'name' => 'groups',   'name_ar' => 'المجموعات',         'parent' => 'academy_management', 'sort' => 13],
            ['type' => 'child', 'name' => 'teachers', 'name_ar' => 'المعلمون',          'parent' => 'academy_management', 'sort' => 14],
            ['type' => 'child', 'name' => 'students', 'name_ar' => 'الطلاب',            'parent' => 'academy_management', 'sort' => 15],
            ['type' => 'child', 'name' => 'times',    'name_ar' => 'المواعيد والاوقات', 'parent' => 'academy_management', 'sort' => 16],
            ['type' => 'child', 'name' => 'fees',     'name_ar' => 'الرسوم',            'parent' => 'academy_management', 'sort' => 17],

            // ══════════════════════════════════════════════════════════════════
            //  الإدارة المالية
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'parent',
                'name'    => 'financial_management',
                'name_ar' => 'الإدارة المالية',
                'name_en' => 'Financial Management',
                'icon'    => 'ki-duotone ki-financial-schedule',
                'sort'    => 20,
            ],
            [
                'type'    => 'custom',
                'name'    => 'financial',
                'name_ar' => 'المالية',
                'parent'  => 'financial_management',
                'sort'    => 21,
                'actions' => ['view', 'verify', 'refund'],
            ],

            // ══════════════════════════════════════════════════════════════════
            //  الطلبات العالقة
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'parent',
                'name'    => 'pending_requests',
                'name_ar' => 'الطلبات العالقة',
                'name_en' => 'Pending Requests',
                'icon'    => 'ki-duotone ki-time',
                'sort'    => 30,
            ],
            ['type' => 'child', 'name' => 'memberships',    'name_ar' => 'العضوية',               'parent' => 'pending_requests', 'sort' => 31],
            ['type' => 'child', 'name' => 'closed_classes', 'name_ar' => 'المجموعات المنتهية',    'parent' => 'pending_requests', 'sort' => 32],
            ['type' => 'child', 'name' => 'ask_update',     'name_ar' => 'طلبات تحديث البيانات', 'parent' => 'pending_requests', 'sort' => 33],

            // ══════════════════════════════════════════════════════════════════
            //  ادارة التسجيل
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'parent',
                'name'    => 'registration_management',
                'name_ar' => 'ادارة التسجيل',
                'name_en' => 'Registration Management',
                'icon'    => 'ki-duotone ki-briefcase',
                'sort'    => 40,
            ],
            ['type' => 'child', 'name' => 'placement_tests', 'name_ar' => 'اختبارات تحديد المستوى', 'parent' => 'registration_management', 'sort' => 41],
            ['type' => 'child', 'name' => 'parents',          'name_ar' => 'أولياء الأمور',          'parent' => 'registration_management', 'sort' => 42],
            ['type' => 'child', 'name' => 'payment_methods',  'name_ar' => 'طرق الدفع',              'parent' => 'registration_management', 'sort' => 43],
            ['type' => 'child', 'name' => 'relationships',    'name_ar' => 'صلات القرابة',            'parent' => 'registration_management', 'sort' => 44],

            // ══════════════════════════════════════════════════════════════════
            //  ادارة جهات الاتصال  (standalone)
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'custom',
                'name'    => 'contact',
                'name_ar' => 'جهات الاتصال',
                'name_en' => 'Contact',
                'icon'    => 'ki-duotone ki-sms',
                'sort'    => 50,
                'actions' => ['view', 'reply', 'delete', 'status'],
            ],

            // ══════════════════════════════════════════════════════════════════
            //  ادارة الاشعارات
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'parent',
                'name'    => 'notifications_management',
                'name_ar' => 'ادارة الاشعارات',
                'name_en' => 'Notifications Management',
                'icon'    => 'ki-duotone ki-notification-on',
                'sort'    => 60,
            ],
            ['type' => 'child', 'name' => 'messages_students', 'name_ar' => 'رسائل الطلاب',  'parent' => 'notifications_management', 'sort' => 61],
            ['type' => 'child', 'name' => 'messages_teachers', 'name_ar' => 'رسائل المعلمين','parent' => 'notifications_management', 'sort' => 62],

            // ══════════════════════════════════════════════════════════════════
            //  حملات البريد  (standalone)
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'custom',
                'name'    => 'email_campaigns',
                'name_ar' => 'حملات البريد',
                'name_en' => 'Email Campaigns',
                'icon'    => 'ki-duotone ki-sms',
                'sort'    => 70,
                'actions' => ['view', 'add', 'edit', 'delete'],
            ],

            // ══════════════════════════════════════════════════════════════════
            //  ادارة التقيمات
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'parent',
                'name'    => 'evaluations_management',
                'name_ar' => 'ادارة التقيمات',
                'name_en' => 'Evaluations Management',
                'icon'    => 'ki-duotone ki-medal-star',
                'sort'    => 80,
            ],
            ['type' => 'child', 'name' => 'evaluate_items', 'name_ar' => 'اسئلة تقييم الطلاب',  'parent' => 'evaluations_management', 'sort' => 81],
            ['type' => 'child', 'name' => 'questions',      'name_ar' => 'اسئلة تقييم المعلمين','parent' => 'evaluations_management', 'sort' => 82],

            // ══════════════════════════════════════════════════════════════════
            //  شؤون المدرّسين (HR)
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'parent',
                'name'    => 'hr_teachers',
                'name_ar' => 'شؤون المدرّسين (HR)',
                'name_en' => 'Teachers HR',
                'icon'    => 'ki-duotone ki-people',
                'sort'    => 90,
            ],
            ['type' => 'child', 'name' => 'absent_teacher',    'name_ar' => 'سجل حضور المدرّسين',   'parent' => 'hr_teachers', 'sort' => 91],
            ['type' => 'child', 'name' => 'teacher_salaries',  'name_ar' => 'رواتب المعلّمين',       'parent' => 'hr_teachers', 'sort' => 92],
            [
                'type'    => 'custom',
                'name'    => 'teacher_attendance',
                'name_ar' => 'حضور وغياب المدرّسين',
                'parent'  => 'hr_teachers',
                'sort'    => 93,
                'actions' => ['view', 'edit'],
            ],

            // ══════════════════════════════════════════════════════════════════
            //  إدارة الموقع الخارجي
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'parent',
                'name'    => 'site_management',
                'name_ar' => 'إدارة الموقع الخارجي',
                'name_en' => 'Site Management',
                'icon'    => 'ki-duotone ki-mouse-square',
                'sort'    => 100,
            ],
            ['type' => 'child', 'name' => 'news',      'name_ar' => 'السلايدر والأخبار', 'parent' => 'site_management', 'sort' => 101],
            ['type' => 'child', 'name' => 'pages',     'name_ar' => 'الصفحات الثابتة',  'parent' => 'site_management', 'sort' => 102],
            ['type' => 'child', 'name' => 'videos',    'name_ar' => 'الفيديوهات',        'parent' => 'site_management', 'sort' => 103],
            ['type' => 'child', 'name' => 'categories','name_ar' => 'التصنيفات',          'parent' => 'site_management', 'sort' => 104],
            ['type' => 'child', 'name' => 'partners',  'name_ar' => 'الشركاء',            'parent' => 'site_management', 'sort' => 105],
            ['type' => 'child', 'name' => 'photos',    'name_ar' => 'الصور',              'parent' => 'site_management', 'sort' => 106],

            // ══════════════════════════════════════════════════════════════════
            //  ادارة الوسائط
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'parent',
                'name'    => 'media_management',
                'name_ar' => 'ادارة الوسائط',
                'name_en' => 'Media Management',
                'icon'    => 'ki-duotone ki-picture',
                'sort'    => 110,
            ],
            ['type' => 'child', 'name' => 'file_manager', 'name_ar' => 'مدير الملفات', 'parent' => 'media_management', 'sort' => 111],

            // ══════════════════════════════════════════════════════════════════
            //  ادارة الموقع  (settings / users / roles)
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'parent',
                'name'    => 'site_admin',
                'name_ar' => 'ادارة الموقع',
                'name_en' => 'Site Admin',
                'icon'    => 'ki-duotone ki-setting-2',
                'sort'    => 120,
            ],
            ['type' => 'child', 'name' => 'settings', 'name_ar' => 'الإعدادات',          'parent' => 'site_admin', 'sort' => 121],
            ['type' => 'child', 'name' => 'social',   'name_ar' => 'الشبكات الإجتماعية', 'parent' => 'site_admin', 'sort' => 122],
            ['type' => 'child', 'name' => 'users',    'name_ar' => 'المستخدمين',          'parent' => 'site_admin', 'sort' => 123],
            ['type' => 'child', 'name' => 'roles',    'name_ar' => 'الصلاحيات',           'parent' => 'site_admin', 'sort' => 124],

            // ══════════════════════════════════════════════════════════════════
            //  صفحات مستقلة
            // ══════════════════════════════════════════════════════════════════
            [
                'type'    => 'custom',
                'name'    => 'students_report',
                'name_ar' => 'استعلامات الطلاب',
                'name_en' => 'Students Report',
                'icon'    => 'ki-duotone ki-user-square',
                'sort'    => 130,
                'actions' => ['view'],
            ],
            [
                'type'    => 'custom',
                'name'    => 'certificates',
                'name_ar' => 'الشهادات',
                'name_en' => 'Certificates',
                'icon'    => 'ki-duotone ki-shield-tick',
                'sort'    => 131,
                'actions' => ['view', 'add', 'edit', 'delete'],
            ],
            [
                'type'    => 'custom',
                'name'    => 'delayed_students',
                'name_ar' => 'الطلاب المؤجلين',
                'name_en' => 'Delayed Students',
                'icon'    => 'ki-duotone ki-timer',
                'sort'    => 132,
                'actions' => ['view'],
            ],
            [
                'type'    => 'custom',
                'name'    => 'birthdayes',
                'name_ar' => 'أعياد الميلاد',
                'name_en' => 'Birthdays',
                'icon'    => 'ki-duotone ki-gift',
                'sort'    => 133,
                'actions' => ['view'],
            ],
            [
                'type'    => 'custom',
                'name'    => 'progress_menu',
                'name_ar' => 'مستوى التقدم',
                'name_en' => 'Progress Menu',
                'icon'    => 'ki-duotone ki-graph-up',
                'sort'    => 134,
                'actions' => ['view'],
            ],
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Public API
    // ──────────────────────────────────────────────────────────────────────────

    public function sync(bool $verbose = false): array
    {
        $stats = ['groups' => 0, 'permissions' => 0, 'skipped' => 0, 'updated' => 0];

        $groupMap = $this->syncGroups($stats, $verbose);
        $this->syncPermissions($groupMap, $stats, $verbose);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return $stats;
    }

    public function syncRolePermissions(string $roleName = 'Admin'): bool
    {
        $role = Role::where('name', $roleName)->first();
        if (!$role) return false;

        $all = Permission::where('guard_name', self::GUARD)->pluck('name')->toArray();
        $role->syncPermissions($all);

        return true;
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Internal helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function syncGroups(array &$stats, bool $verbose): array
    {
        $groupMap = [];

        foreach ($this->tree() as $entry) {
            $name     = $entry['name'];
            $nameAr   = $entry['name_ar'];
            $nameEn   = $entry['name_en'] ?? $nameAr;
            $icon     = $entry['icon'] ?? '';
            $sort     = $entry['sort'] ?? 0;
            $parent   = $entry['parent'] ?? null;
            $parentId = $parent ? ($groupMap[$parent] ?? 0) : 0;

            $existing = DB::table('permissions_group')->where('name', $name)->first();

            if (!$existing) {
                $id = DB::table('permissions_group')->insertGetId([
                    'name'      => $name,
                    'name_ar'   => $nameAr,
                    'name_en'   => $nameEn,
                    'icon'      => $icon,
                    'sort'      => $sort,
                    'status'    => 1,
                    'parent_id' => $parentId,
                ]);
                $stats['groups']++;
                if ($verbose) echo "  [GROUP+] {$name}\n";
            } else {
                $id = $existing->id;
                // Always sync: name_ar, name_en, sort, parent_id, icon
                DB::table('permissions_group')->where('id', $id)->update([
                    'name_ar'   => $nameAr,
                    'name_en'   => $nameEn,
                    'sort'      => $sort,
                    'parent_id' => $parentId,
                    'icon'      => $icon ?: $existing->icon,
                ]);
                if ($verbose) echo "  [GROUP=] {$name}\n";
            }

            $groupMap[$name] = $id;
        }

        return $groupMap;
    }

    private function syncPermissions(array $groupMap, array &$stats, bool $verbose): void
    {
        foreach ($this->tree() as $entry) {
            $name    = $entry['name'];
            $nameAr  = $entry['name_ar'];
            $groupId = $groupMap[$name] ?? null;
            if (!$groupId) continue;

            foreach ($this->resolveActions($entry) as $action) {
                $permName   = "admin.{$name}.{$action}";
                $permNameAr = $this->buildNameAr($nameAr, $action);

                $existing = DB::table('permissions')
                    ->where('name', $permName)
                    ->where('guard_name', self::GUARD)
                    ->first();

                if (!$existing) {
                    DB::table('permissions')->insert([
                        'name'       => $permName,
                        'name_ar'    => $permNameAr,
                        'guard_name' => self::GUARD,
                        'group_id'   => $groupId,
                    ]);
                    $stats['permissions']++;
                    if ($verbose) echo "  [PERM+] {$permName} ({$permNameAr})\n";
                } else {
                    // Back-fill name_ar if missing
                    if (empty($existing->name_ar)) {
                        DB::table('permissions')->where('id', $existing->id)
                            ->update(['name_ar' => $permNameAr]);
                        $stats['updated']++;
                        if ($verbose) echo "  [PERM~] {$permName} name_ar filled\n";
                    } else {
                        $stats['skipped']++;
                        if ($verbose) echo "  [PERM=] {$permName}\n";
                    }
                }
            }
        }
    }

    private function resolveActions(array $entry): array
    {
        return match ($entry['type']) {
            'parent' => ['view'],
            'child'  => ['view', 'add', 'edit', 'delete', 'status'],
            'custom' => $entry['actions'],
            default  => [],
        };
    }

    private function buildNameAr(string $moduleAr, string $action): string
    {
        $actionAr = $this->actionAr[$action] ?? $action;
        return "{$actionAr} {$moduleAr}";
    }
}
