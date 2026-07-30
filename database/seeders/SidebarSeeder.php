<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * SidebarSeeder
 *
 * Seeds the complete sidebar structure (permissions_group hierarchy + permissions)
 * exactly as it currently exists in production, then creates a super-admin user
 * with every permission so all sidebar menus are visible.
 *
 * Run:  php artisan db:seed --class=SidebarSeeder
 * Or include via DatabaseSeeder: $this->call(SidebarSeeder::class);
 *
 * Safe to re-run — uses updateOrInsert / firstOrCreate everywhere.
 */
class SidebarSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────────────────────────────
        // 1.  PERMISSIONS GROUP (full hierarchy)
        // ──────────────────────────────────────────────────────────────────────
        //
        // Each entry: [id, parent_id, sort, name, name_ar, icon, color]
        //
        // parent_id = 0  →  top-level parent (shown as a section in the sidebar)
        // parent_id > 0  →  child item rendered under that parent section
        //
        $groups = [

            // ── TOP-LEVEL PARENTS (parent_id = 0) ─────────────────────────────

            // Dashboard — hardcoded at top of sidebar_menu.blade.php, kept here
            // so the cache lookup (dashboardGroup) and color-setting still works.
            ['id' => 27,  'parent_id' => 0,   'sort' => 0,   'name' => 'dashboard',               'name_ar' => 'الرئيسية',                       'icon' => 'ki-duotone ki-home',                  'color' => '#c474a0'],

            ['id' => 64,  'parent_id' => 0,   'sort' => 10,  'name' => 'calendar',                'name_ar' => 'التقويم الدراسي',                  'icon' => 'ki-duotone ki-calendar-8',            'color' => '#c474a0'],
            ['id' => 41,  'parent_id' => 0,   'sort' => 20,  'name' => 'academy_management',      'name_ar' => 'ادارة الاكاديمية',                 'icon' => 'ki-duotone ki-book-open',             'color' => '#c474a0'],
            ['id' => 43,  'parent_id' => 0,   'sort' => 30,  'name' => 'financial_management',   'name_ar' => 'الإدارة المالية',                  'icon' => 'ki-duotone ki-financial-schedule',    'color' => '#c474a0'],
            ['id' => 44,  'parent_id' => 0,   'sort' => 40,  'name' => 'pending_requests',       'name_ar' => 'الطلبات العالقة',                  'icon' => 'ki-duotone ki-time',                  'color' => '#c474a0'],
            ['id' => 47,  'parent_id' => 0,   'sort' => 50,  'name' => 'registration_management','name_ar' => 'ادارة التسجيل',                    'icon' => 'ki-duotone ki-briefcase',             'color' => '#c474a0'],
            ['id' => 9,   'parent_id' => 0,   'sort' => 60,  'name' => 'contact',                'name_ar' => 'جهات الاتصال',                     'icon' => 'ki-duotone ki-sms',                   'color' => '#c474a0'],
            ['id' => 52,  'parent_id' => 0,   'sort' => 70,  'name' => 'notifications_management','name_ar' => 'ادارة الاشعارات',                 'icon' => 'ki-duotone ki-notification-on',       'color' => '#c474a0'],
            ['id' => 36,  'parent_id' => 0,   'sort' => 80,  'name' => 'email_campaigns',        'name_ar' => 'حملات البريد',                     'icon' => 'ki-duotone ki-sms',                   'color' => '#c474a0'],
            ['id' => 55,  'parent_id' => 0,   'sort' => 90,  'name' => 'evaluations_management', 'name_ar' => 'ادارة التقيمات',                   'icon' => 'ki-duotone ki-medal-star',            'color' => '#c474a0'],
            ['id' => 58,  'parent_id' => 0,   'sort' => 100, 'name' => 'hr_teachers',            'name_ar' => 'شؤون المدرّسين (HR)',               'icon' => 'ki-duotone ki-people',                'color' => '#c474a0'],
            ['id' => 59,  'parent_id' => 0,   'sort' => 110, 'name' => 'site_management',        'name_ar' => 'إدارة الموقع الخارجي',             'icon' => 'ki-duotone ki-mouse-square',          'color' => '#c474a0'],
            ['id' => 60,  'parent_id' => 0,   'sort' => 120, 'name' => 'media_management',       'name_ar' => 'ادارة الوسائط',                    'icon' => 'ki-duotone ki-picture',               'color' => '#c474a0'],
            ['id' => 62,  'parent_id' => 0,   'sort' => 130, 'name' => 'site_admin',             'name_ar' => 'ادارة الموقع',                     'icon' => 'ki-duotone ki-setting-2',             'color' => '#c474a0'],
            ['id' => 63,  'parent_id' => 0,   'sort' => 140, 'name' => 'students_report',        'name_ar' => 'استعلامات الطلاب',                 'icon' => 'ki-duotone ki-user-square',           'color' => '#c474a0'],
            ['id' => 25,  'parent_id' => 0,   'sort' => 150, 'name' => 'certificates',           'name_ar' => 'الشهادات',                         'icon' => 'ki-duotone ki-shield-tick',           'color' => '#c474a0'],
            ['id' => 67,  'parent_id' => 0,   'sort' => 160, 'name' => 'delayed_students',       'name_ar' => 'الطلاب المؤجلين',                  'icon' => 'ki-duotone ki-timer',                 'color' => '#c474a0'],
            ['id' => 68,  'parent_id' => 0,   'sort' => 170, 'name' => 'birthdayes',             'name_ar' => 'أعياد الميلاد',                    'icon' => 'ki-duotone ki-gift',                  'color' => '#c474a0'],
            ['id' => 23,  'parent_id' => 0,   'sort' => 180, 'name' => 'progress_menu',          'name_ar' => 'مستوى التقدم',                     'icon' => 'ki-duotone ki-graph-up',              'color' => '#c474a0'],

            // Admin oversight of the student/teacher group chat (route: group_chat.view)
            ['id' => 76,  'parent_id' => 0,   'sort' => 65,  'name' => 'group_chat',             'name_ar' => 'مراقبة المحادثات',                  'icon' => 'ki-duotone ki-messages',              'color' => '#50cd89'],

            // ── CHILDREN OF 41: academy_management ────────────────────────────
            ['id' => 42,  'parent_id' => 41,  'sort' => 1,   'name' => 'branches',               'name_ar' => 'الفروع',                           'icon' => '',  'color' => '#c474a0'],
            ['id' => 12,  'parent_id' => 41,  'sort' => 2,   'name' => 'programs',               'name_ar' => 'البرامج',                          'icon' => '',  'color' => '#c474a0'],
            ['id' => 13,  'parent_id' => 41,  'sort' => 3,   'name' => 'groups',                 'name_ar' => 'المجموعات',                        'icon' => '',  'color' => '#c474a0'],
            ['id' => 18,  'parent_id' => 41,  'sort' => 4,   'name' => 'teachers',               'name_ar' => 'المعلمون',                         'icon' => '',  'color' => '#c474a0'],
            ['id' => 14,  'parent_id' => 41,  'sort' => 5,   'name' => 'students',               'name_ar' => 'الطلاب',                           'icon' => '',  'color' => '#c474a0'],
            ['id' => 15,  'parent_id' => 41,  'sort' => 6,   'name' => 'times',                  'name_ar' => 'المواعيد والاوقات',                 'icon' => '',  'color' => '#c474a0'],
            ['id' => 16,  'parent_id' => 41,  'sort' => 7,   'name' => 'fees',                   'name_ar' => 'الرسوم',                           'icon' => '',  'color' => '#c474a0'],

            // ── CHILDREN OF 43: financial_management ──────────────────────────
            ['id' => 70,  'parent_id' => 43,  'sort' => 1,   'name' => 'financial_dashboard',    'name_ar' => 'السجل المالي',                     'icon' => '',  'color' => '#c474a0'],
            ['id' => 71,  'parent_id' => 43,  'sort' => 2,   'name' => 'financial_pending',      'name_ar' => 'الطلبات المالية العالقة',           'icon' => '',  'color' => '#c474a0'],
            ['id' => 72,  'parent_id' => 43,  'sort' => 3,   'name' => 'financial_expenses',     'name_ar' => 'المصروفات',                        'icon' => '',  'color' => '#c474a0'],
            ['id' => 40,  'parent_id' => 43,  'sort' => 4,   'name' => 'financial',              'name_ar' => 'كشف حساب الطلاب',                  'icon' => 'ki-duotone ki-bill', 'color' => '#c474a0'],
            // student_payments — NEW entry for دفعات الطلاب
            // Route: admin.financial.student-payments  →  registered as student_payments.view alias below
            ['id' => 75,  'parent_id' => 43,  'sort' => 5,   'name' => 'student_payments',       'name_ar' => 'دفعات الطلاب',                     'icon' => '',  'color' => '#c474a0'],

            // ── CHILDREN OF 44: pending_requests ──────────────────────────────
            ['id' => 45,  'parent_id' => 44,  'sort' => 1,   'name' => 'memberships',            'name_ar' => 'العضوية',                          'icon' => '',  'color' => '#c474a0'],
            ['id' => 46,  'parent_id' => 44,  'sort' => 2,   'name' => 'closed_classes',         'name_ar' => 'المجموعات المنتهية',               'icon' => '',  'color' => '#c474a0'],
            ['id' => 35,  'parent_id' => 44,  'sort' => 3,   'name' => 'ask_update',             'name_ar' => 'طلبات تحديث البيانات',              'icon' => '',  'color' => '#c474a0'],
            ['id' => 51,  'parent_id' => 44,  'sort' => 4,   'name' => 'relationships',          'name_ar' => 'صلات القرابة',                     'icon' => '',  'color' => '#c474a0'],

            // ── CHILDREN OF 47: registration_management ───────────────────────
            ['id' => 48,  'parent_id' => 47,  'sort' => 1,   'name' => 'placement_tests',        'name_ar' => 'اختبارات تحديد المستوى',            'icon' => '',  'color' => '#c474a0'],
            ['id' => 49,  'parent_id' => 47,  'sort' => 2,   'name' => 'parents',                'name_ar' => 'أولياء الأمور',                    'icon' => '',  'color' => '#c474a0'],
            ['id' => 50,  'parent_id' => 47,  'sort' => 3,   'name' => 'payment_methods',        'name_ar' => 'طرق الدفع',                        'icon' => '',  'color' => '#c474a0'],

            // ── CHILDREN OF 52: notifications_management ──────────────────────
            ['id' => 53,  'parent_id' => 52,  'sort' => 1,   'name' => 'messages_students',      'name_ar' => 'رسائل الطلاب',                     'icon' => '',  'color' => '#c474a0'],
            ['id' => 54,  'parent_id' => 52,  'sort' => 2,   'name' => 'messages_teachers',      'name_ar' => 'رسائل المعلمين',                   'icon' => '',  'color' => '#c474a0'],

            // ── CHILDREN OF 55: evaluations_management ────────────────────────
            ['id' => 56,  'parent_id' => 55,  'sort' => 1,   'name' => 'evaluate_items',         'name_ar' => 'اسئلة تقييم الطلاب',               'icon' => '',  'color' => '#c474a0'],
            ['id' => 57,  'parent_id' => 55,  'sort' => 2,   'name' => 'questions',              'name_ar' => 'اسئلة تقييم المعلمين',              'icon' => '',  'color' => '#c474a0'],

            // ── CHILDREN OF 58: hr_teachers ───────────────────────────────────
            ['id' => 65,  'parent_id' => 58,  'sort' => 1,   'name' => 'absent_teacher',         'name_ar' => 'سجل حضور المدرّسين',               'icon' => '',  'color' => '#c474a0'],
            ['id' => 66,  'parent_id' => 58,  'sort' => 2,   'name' => 'teacher_salaries',       'name_ar' => 'رواتب المعلّمين',                  'icon' => '',  'color' => '#c474a0'],
            ['id' => 34,  'parent_id' => 58,  'sort' => 3,   'name' => 'teacher_attendance',     'name_ar' => 'حضور وغياب المدرّسين',              'icon' => '',  'color' => '#c474a0'],
            ['id' => 73,  'parent_id' => 58,  'sort' => 4,   'name' => 'attendance_settings',    'name_ar' => 'إعدادات الحضور والغياب',            'icon' => '',  'color' => '#c474a0'],

            // ── CHILDREN OF 59: site_management ───────────────────────────────
            ['id' => 5,   'parent_id' => 59,  'sort' => 1,   'name' => 'news',                   'name_ar' => 'السلايدر والأخبار',                 'icon' => '',  'color' => '#c474a0'],
            ['id' => 10,  'parent_id' => 59,  'sort' => 2,   'name' => 'pages',                  'name_ar' => 'الصفحات الثابتة',                  'icon' => '',  'color' => '#c474a0'],
            ['id' => 7,   'parent_id' => 59,  'sort' => 3,   'name' => 'videos',                 'name_ar' => 'الفيديوهات',                       'icon' => '',  'color' => '#c474a0'],
            ['id' => 4,   'parent_id' => 59,  'sort' => 4,   'name' => 'categories',             'name_ar' => 'التصنيفات',                        'icon' => '',  'color' => '#c474a0'],
            ['id' => 11,  'parent_id' => 59,  'sort' => 5,   'name' => 'partners',               'name_ar' => 'الشركاء',                          'icon' => '',  'color' => '#c474a0'],
            ['id' => 6,   'parent_id' => 59,  'sort' => 6,   'name' => 'photos',                 'name_ar' => 'الصور',                            'icon' => '',  'color' => '#c474a0'],

            // ── CHILDREN OF 60: media_management ──────────────────────────────
            ['id' => 61,  'parent_id' => 60,  'sort' => 1,   'name' => 'file_manager',           'name_ar' => 'مدير الملفات',                     'icon' => '',  'color' => '#c474a0'],
            ['id' => 74,  'parent_id' => 60,  'sort' => 2,   'name' => 'files',                  'name_ar' => 'مكتبة الملفات',                    'icon' => '',  'color' => '#c474a0'],

            // ── CHILDREN OF 62: site_admin ────────────────────────────────────
            ['id' => 1,   'parent_id' => 62,  'sort' => 1,   'name' => 'settings',               'name_ar' => 'الإعدادات',                        'icon' => '',  'color' => '#c474a0'],
            ['id' => 8,   'parent_id' => 62,  'sort' => 2,   'name' => 'social',                 'name_ar' => 'الشبكات الإجتماعية',               'icon' => '',  'color' => '#c474a0'],
            ['id' => 2,   'parent_id' => 62,  'sort' => 3,   'name' => 'users',                  'name_ar' => 'المستخدمين',                       'icon' => '',  'color' => '#c474a0'],
            ['id' => 3,   'parent_id' => 62,  'sort' => 4,   'name' => 'roles',                  'name_ar' => 'الصلاحيات',                        'icon' => '',  'color' => '#c474a0'],
            ['id' => 69,  'parent_id' => 62,  'sort' => 5,   'name' => 'sidebar_manager',        'name_ar' => 'إدارة القائمة الجانبية',            'icon' => 'ki-duotone ki-menu', 'color' => '#c474a0'],
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($groups as $g) {
            DB::table('permissions_group')->updateOrInsert(
                ['id' => $g['id']],
                [
                    'name'      => $g['name'],
                    'name_ar'   => $g['name_ar'],
                    'name_en'   => $g['name_ar'],   // fallback — update manually if needed
                    'icon'      => $g['icon'],
                    'color'     => $g['color'],
                    'sort'      => $g['sort'],
                    'parent_id' => $g['parent_id'],
                    'status'    => 1,
                    'deleted_at'=> null,
                ]
            );
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ──────────────────────────────────────────────────────────────────────
        // 2.  PERMISSIONS
        // ──────────────────────────────────────────────────────────────────────
        //
        // Format: ['name' => 'admin.xxx.yyy', 'group_id' => N]
        //
        $permissions = [

            // settings (group 1)
            ['name' => 'admin.settings.view',   'group_id' => 1],
            ['name' => 'admin.settings.add',    'group_id' => 1],
            ['name' => 'admin.settings.edit',   'group_id' => 1],
            ['name' => 'admin.settings.delete', 'group_id' => 1],
            ['name' => 'admin.settings.status', 'group_id' => 1],

            // users (group 2)
            ['name' => 'admin.users.view',     'group_id' => 2],
            ['name' => 'admin.users.add',      'group_id' => 2],
            ['name' => 'admin.users.edit',     'group_id' => 2],
            ['name' => 'admin.users.delete',   'group_id' => 2],
            ['name' => 'admin.users.status',   'group_id' => 2],
            ['name' => 'admin.users.password', 'group_id' => 2],

            // roles (group 3)
            ['name' => 'admin.roles.view',        'group_id' => 3],
            ['name' => 'admin.roles.add',         'group_id' => 3],
            ['name' => 'admin.roles.edit',        'group_id' => 3],
            ['name' => 'admin.roles.delete',      'group_id' => 3],
            ['name' => 'admin.roles.status',      'group_id' => 3],
            ['name' => 'admin.roles.permissions', 'group_id' => 3],

            // categories (group 4)
            ['name' => 'admin.categories.view',   'group_id' => 4],
            ['name' => 'admin.categories.add',    'group_id' => 4],
            ['name' => 'admin.categories.edit',   'group_id' => 4],
            ['name' => 'admin.categories.delete', 'group_id' => 4],
            ['name' => 'admin.categories.status', 'group_id' => 4],

            // news (group 5)
            ['name' => 'admin.news.view',    'group_id' => 5],
            ['name' => 'admin.news.add',     'group_id' => 5],
            ['name' => 'admin.news.edit',    'group_id' => 5],
            ['name' => 'admin.news.delete',  'group_id' => 5],
            ['name' => 'admin.news.publish', 'group_id' => 5],
            ['name' => 'admin.news.status',  'group_id' => 5],

            // photos (group 6)
            ['name' => 'admin.photos.view',   'group_id' => 6],
            ['name' => 'admin.photos.add',    'group_id' => 6],
            ['name' => 'admin.photos.edit',   'group_id' => 6],
            ['name' => 'admin.photos.delete', 'group_id' => 6],
            ['name' => 'admin.photos.status', 'group_id' => 6],

            // videos (group 7)
            ['name' => 'admin.videos.view',   'group_id' => 7],
            ['name' => 'admin.videos.add',    'group_id' => 7],
            ['name' => 'admin.videos.edit',   'group_id' => 7],
            ['name' => 'admin.videos.delete', 'group_id' => 7],
            ['name' => 'admin.videos.status', 'group_id' => 7],

            // social (group 8)
            ['name' => 'admin.social.view',   'group_id' => 8],
            ['name' => 'admin.social.add',    'group_id' => 8],
            ['name' => 'admin.social.edit',   'group_id' => 8],
            ['name' => 'admin.social.delete', 'group_id' => 8],
            ['name' => 'admin.social.status', 'group_id' => 8],

            // contact (group 9)
            ['name' => 'admin.contact.view',   'group_id' => 9],
            ['name' => 'admin.contact.status', 'group_id' => 9],
            ['name' => 'admin.contact.delete', 'group_id' => 9],
            ['name' => 'admin.contact.reply',  'group_id' => 9],

            // pages (group 10)
            ['name' => 'admin.pages.view',   'group_id' => 10],
            ['name' => 'admin.pages.add',    'group_id' => 10],
            ['name' => 'admin.pages.edit',   'group_id' => 10],
            ['name' => 'admin.pages.delete', 'group_id' => 10],
            ['name' => 'admin.pages.status', 'group_id' => 10],

            // partners (group 11)
            ['name' => 'admin.partners.view',   'group_id' => 11],
            ['name' => 'admin.partners.add',    'group_id' => 11],
            ['name' => 'admin.partners.edit',   'group_id' => 11],
            ['name' => 'admin.partners.delete', 'group_id' => 11],
            ['name' => 'admin.partners.status', 'group_id' => 11],

            // programs (group 12)
            ['name' => 'admin.programs.view',   'group_id' => 12],
            ['name' => 'admin.programs.add',    'group_id' => 12],
            ['name' => 'admin.programs.edit',   'group_id' => 12],
            ['name' => 'admin.programs.delete', 'group_id' => 12],
            ['name' => 'admin.programs.status', 'group_id' => 12],

            // groups (group 13)
            ['name' => 'admin.groups.view',          'group_id' => 13],
            ['name' => 'admin.groups.add',           'group_id' => 13],
            ['name' => 'admin.groups.edit',          'group_id' => 13],
            ['name' => 'admin.groups.delete',        'group_id' => 13],
            ['name' => 'admin.groups.status',        'group_id' => 13],
            // group chat monitor (group 76)
            ['name' => 'admin.group_chat.view',   'group_id' => 76],
            ['name' => 'admin.group_chat.send',   'group_id' => 76],
            ['name' => 'admin.group_chat.delete', 'group_id' => 76],

            ['name' => 'admin.groups.student.view',  'group_id' => 13],
            ['name' => 'admin.groups.student.add',   'group_id' => 13],
            ['name' => 'admin.groups.student.delete','group_id' => 13],

            // students (group 14)
            ['name' => 'admin.students.view',   'group_id' => 14],
            ['name' => 'admin.students.add',    'group_id' => 14],
            ['name' => 'admin.students.edit',   'group_id' => 14],
            ['name' => 'admin.students.delete', 'group_id' => 14],
            ['name' => 'admin.students.status', 'group_id' => 14],

            // times (group 15)
            ['name' => 'admin.times.view',   'group_id' => 15],
            ['name' => 'admin.times.add',    'group_id' => 15],
            ['name' => 'admin.times.edit',   'group_id' => 15],
            ['name' => 'admin.times.delete', 'group_id' => 15],
            ['name' => 'admin.times.status', 'group_id' => 15],

            // fees (group 16)
            ['name' => 'admin.fees.view',   'group_id' => 16],
            ['name' => 'admin.fees.add',    'group_id' => 16],
            ['name' => 'admin.fees.edit',   'group_id' => 16],
            ['name' => 'admin.fees.delete', 'group_id' => 16],
            ['name' => 'admin.fees.status', 'group_id' => 16],

            // evaluations (group 17 — legacy top-level kept for backward compat)
            ['name' => 'admin.evaluations.view',   'group_id' => 17],
            ['name' => 'admin.evaluations.add',    'group_id' => 17],
            ['name' => 'admin.evaluations.edit',   'group_id' => 17],
            ['name' => 'admin.evaluations.delete', 'group_id' => 17],
            ['name' => 'admin.evaluations.status', 'group_id' => 17],

            // teachers (group 18)
            ['name' => 'admin.teachers.view',   'group_id' => 18],
            ['name' => 'admin.teachers.add',    'group_id' => 18],
            ['name' => 'admin.teachers.edit',   'group_id' => 18],
            ['name' => 'admin.teachers.delete', 'group_id' => 18],
            ['name' => 'admin.teachers.status', 'group_id' => 18],

            // educational_materials (group 19)
            ['name' => 'admin.educational_materials.view',   'group_id' => 19],
            ['name' => 'admin.educational_materials.add',    'group_id' => 19],
            ['name' => 'admin.educational_materials.edit',   'group_id' => 19],
            ['name' => 'admin.educational_materials.delete', 'group_id' => 19],
            ['name' => 'admin.educational_materials.status', 'group_id' => 19],

            // assessments (group 20)
            ['name' => 'admin.assessments.view',   'group_id' => 20],
            ['name' => 'admin.assessments.add',    'group_id' => 20],
            ['name' => 'admin.assessments.edit',   'group_id' => 20],
            ['name' => 'admin.assessments.delete', 'group_id' => 20],
            ['name' => 'admin.assessments.status', 'group_id' => 20],

            // teacher_evaluations (group 21)
            ['name' => 'admin.teacher_evaluations.view',   'group_id' => 21],
            ['name' => 'admin.teacher_evaluations.add',    'group_id' => 21],
            ['name' => 'admin.teacher_evaluations.edit',   'group_id' => 21],
            ['name' => 'admin.teacher_evaluations.delete', 'group_id' => 21],
            ['name' => 'admin.teacher_evaluations.status', 'group_id' => 21],

            // closed_groups (group 22)
            ['name' => 'admin.closed_groups.view',   'group_id' => 22],
            ['name' => 'admin.closed_groups.delete', 'group_id' => 22],

            // progress_menu (group 23)
            ['name' => 'admin.progress_menu.view', 'group_id' => 23],

            // student_inquiries (group 24)
            ['name' => 'admin.student_inquiries.view', 'group_id' => 24],

            // certificates (group 25)
            ['name' => 'admin.certificates.view',   'group_id' => 25],
            ['name' => 'admin.certificates.add',    'group_id' => 25],
            ['name' => 'admin.certificates.edit',   'group_id' => 25],
            ['name' => 'admin.certificates.delete', 'group_id' => 25],
            ['name' => 'admin.certificates.status', 'group_id' => 25],

            // permissions_group (group 26)
            ['name' => 'admin.permissions_group.view',   'group_id' => 26],
            ['name' => 'admin.permissions_group.add',    'group_id' => 26],
            ['name' => 'admin.permissions_group.edit',   'group_id' => 26],
            ['name' => 'admin.permissions_group.delete', 'group_id' => 26],
            ['name' => 'admin.permissions_group.status', 'group_id' => 26],

            // dashboard (group 27)
            ['name' => 'admin.dashboard.view', 'group_id' => 27],

            // permissions (group 28)
            ['name' => 'admin.permissions.view',   'group_id' => 28],
            ['name' => 'admin.permissions.add',    'group_id' => 28],
            ['name' => 'admin.permissions.edit',   'group_id' => 28],
            ['name' => 'admin.permissions.delete', 'group_id' => 28],
            ['name' => 'admin.permissions.status', 'group_id' => 28],

            // pending_orders (group 29 — legacy)
            ['name' => 'admin.pending_orders.view', 'group_id' => 29],

            // membership (group 30 — legacy)
            ['name' => 'admin.membership.view',        'group_id' => 30],
            ['name' => 'admin.membership.add',         'group_id' => 30],
            ['name' => 'admin.membership.edit',        'group_id' => 30],
            ['name' => 'admin.membership.delete',      'group_id' => 30],
            ['name' => 'admin.membership.status',      'group_id' => 30],
            ['name' => 'admin.membership.sendmesage',  'group_id' => 30],
            ['name' => 'admin.membership.sendmail',    'group_id' => 30],

            // teacher_attendance (group 34)
            ['name' => 'admin.teacher_attendance.view',   'group_id' => 34],
            ['name' => 'admin.teacher_attendance.add',    'group_id' => 34],
            ['name' => 'admin.teacher_attendance.edit',   'group_id' => 34],
            ['name' => 'admin.teacher_attendance.delete', 'group_id' => 34],

            // ask_update (group 35)
            ['name' => 'admin.ask_update.view',   'group_id' => 35],
            ['name' => 'admin.ask_update.add',    'group_id' => 35],
            ['name' => 'admin.ask_update.edit',   'group_id' => 35],
            ['name' => 'admin.ask_update.delete', 'group_id' => 35],
            ['name' => 'admin.ask_update.status', 'group_id' => 35],
            ['name' => 'admin.ask_update.refuse', 'group_id' => 35],
            ['name' => 'admin.ask_update.accept', 'group_id' => 35],

            // email_campaigns (group 36)
            ['name' => 'admin.email_campaigns.view',   'group_id' => 36],
            ['name' => 'admin.email_campaigns.add',    'group_id' => 36],
            ['name' => 'admin.email_campaigns.edit',   'group_id' => 36],
            ['name' => 'admin.email_campaigns.delete', 'group_id' => 36],
            ['name' => 'admin.email_campaigns.send',   'group_id' => 36],
            ['name' => 'admin.email_campaigns.manage', 'group_id' => 36],

            // financial / كشف حساب الطلاب (group 40)
            ['name' => 'admin.financial.view',   'group_id' => 40],
            ['name' => 'admin.financial.verify', 'group_id' => 40],
            ['name' => 'admin.financial.refund', 'group_id' => 40],

            // academy_management parent (group 41)
            ['name' => 'admin.academy_management.view', 'group_id' => 41],

            // branches (group 42)
            ['name' => 'admin.branches.view',   'group_id' => 42],
            ['name' => 'admin.branches.add',    'group_id' => 42],
            ['name' => 'admin.branches.edit',   'group_id' => 42],
            ['name' => 'admin.branches.delete', 'group_id' => 42],
            ['name' => 'admin.branches.status', 'group_id' => 42],

            // financial_management parent (group 43)
            ['name' => 'admin.financial_management.view', 'group_id' => 43],

            // pending_requests parent (group 44)
            ['name' => 'admin.pending_requests.view', 'group_id' => 44],

            // memberships (group 45)
            ['name' => 'admin.memberships.view',   'group_id' => 45],
            ['name' => 'admin.memberships.add',    'group_id' => 45],
            ['name' => 'admin.memberships.edit',   'group_id' => 45],
            ['name' => 'admin.memberships.delete', 'group_id' => 45],
            ['name' => 'admin.memberships.status', 'group_id' => 45],

            // closed_classes (group 46)
            ['name' => 'admin.closed_classes.view',   'group_id' => 46],
            ['name' => 'admin.closed_classes.add',    'group_id' => 46],
            ['name' => 'admin.closed_classes.edit',   'group_id' => 46],
            ['name' => 'admin.closed_classes.delete', 'group_id' => 46],
            ['name' => 'admin.closed_classes.status', 'group_id' => 46],

            // registration_management parent (group 47)
            ['name' => 'admin.registration_management.view', 'group_id' => 47],

            // placement_tests (group 48)
            ['name' => 'admin.placement_tests.view',   'group_id' => 48],
            ['name' => 'admin.placement_tests.add',    'group_id' => 48],
            ['name' => 'admin.placement_tests.edit',   'group_id' => 48],
            ['name' => 'admin.placement_tests.delete', 'group_id' => 48],
            ['name' => 'admin.placement_tests.status', 'group_id' => 48],

            // parents (group 49)
            ['name' => 'admin.parents.view',   'group_id' => 49],
            ['name' => 'admin.parents.add',    'group_id' => 49],
            ['name' => 'admin.parents.edit',   'group_id' => 49],
            ['name' => 'admin.parents.delete', 'group_id' => 49],
            ['name' => 'admin.parents.status', 'group_id' => 49],

            // payment_methods (group 50)
            ['name' => 'admin.payment_methods.view',   'group_id' => 50],
            ['name' => 'admin.payment_methods.add',    'group_id' => 50],
            ['name' => 'admin.payment_methods.edit',   'group_id' => 50],
            ['name' => 'admin.payment_methods.delete', 'group_id' => 50],
            ['name' => 'admin.payment_methods.status', 'group_id' => 50],

            // relationships (group 51)
            ['name' => 'admin.relationships.view',   'group_id' => 51],
            ['name' => 'admin.relationships.add',    'group_id' => 51],
            ['name' => 'admin.relationships.edit',   'group_id' => 51],
            ['name' => 'admin.relationships.delete', 'group_id' => 51],
            ['name' => 'admin.relationships.status', 'group_id' => 51],

            // notifications_management parent (group 52)
            ['name' => 'admin.notifications_management.view', 'group_id' => 52],

            // messages_students (group 53)
            ['name' => 'admin.messages_students.view',   'group_id' => 53],
            ['name' => 'admin.messages_students.add',    'group_id' => 53],
            ['name' => 'admin.messages_students.edit',   'group_id' => 53],
            ['name' => 'admin.messages_students.delete', 'group_id' => 53],
            ['name' => 'admin.messages_students.status', 'group_id' => 53],

            // messages_teachers (group 54)
            ['name' => 'admin.messages_teachers.view',   'group_id' => 54],
            ['name' => 'admin.messages_teachers.add',    'group_id' => 54],
            ['name' => 'admin.messages_teachers.edit',   'group_id' => 54],
            ['name' => 'admin.messages_teachers.delete', 'group_id' => 54],
            ['name' => 'admin.messages_teachers.status', 'group_id' => 54],

            // evaluations_management parent (group 55)
            ['name' => 'admin.evaluations_management.view', 'group_id' => 55],

            // evaluate_items (group 56)
            ['name' => 'admin.evaluate_items.view',   'group_id' => 56],
            ['name' => 'admin.evaluate_items.add',    'group_id' => 56],
            ['name' => 'admin.evaluate_items.edit',   'group_id' => 56],
            ['name' => 'admin.evaluate_items.delete', 'group_id' => 56],
            ['name' => 'admin.evaluate_items.status', 'group_id' => 56],

            // questions (group 57)
            ['name' => 'admin.questions.view',   'group_id' => 57],
            ['name' => 'admin.questions.add',    'group_id' => 57],
            ['name' => 'admin.questions.edit',   'group_id' => 57],
            ['name' => 'admin.questions.delete', 'group_id' => 57],
            ['name' => 'admin.questions.status', 'group_id' => 57],

            // hr_teachers parent (group 58)
            ['name' => 'admin.hr_teachers.view', 'group_id' => 58],

            // site_management parent (group 59)
            ['name' => 'admin.site_management.view', 'group_id' => 59],

            // media_management parent (group 60)
            ['name' => 'admin.media_management.view', 'group_id' => 60],

            // file_manager (group 61)
            ['name' => 'admin.file_manager.view',   'group_id' => 61],
            ['name' => 'admin.file_manager.add',    'group_id' => 61],
            ['name' => 'admin.file_manager.edit',   'group_id' => 61],
            ['name' => 'admin.file_manager.delete', 'group_id' => 61],
            ['name' => 'admin.file_manager.status', 'group_id' => 61],

            // site_admin parent (group 62)
            ['name' => 'admin.site_admin.view', 'group_id' => 62],

            // students_report (group 63)
            ['name' => 'admin.students_report.view', 'group_id' => 63],

            // calendar (group 64)
            ['name' => 'admin.calendar.view', 'group_id' => 64],

            // absent_teacher (group 65)
            ['name' => 'admin.absent_teacher.view',   'group_id' => 65],
            ['name' => 'admin.absent_teacher.add',    'group_id' => 65],
            ['name' => 'admin.absent_teacher.edit',   'group_id' => 65],
            ['name' => 'admin.absent_teacher.delete', 'group_id' => 65],
            ['name' => 'admin.absent_teacher.status', 'group_id' => 65],

            // teacher_salaries (group 66)
            ['name' => 'admin.teacher_salaries.view',   'group_id' => 66],
            ['name' => 'admin.teacher_salaries.add',    'group_id' => 66],
            ['name' => 'admin.teacher_salaries.edit',   'group_id' => 66],
            ['name' => 'admin.teacher_salaries.delete', 'group_id' => 66],
            ['name' => 'admin.teacher_salaries.status', 'group_id' => 66],

            // delayed_students (group 67)
            ['name' => 'admin.delayed_students.view', 'group_id' => 67],

            // birthdayes (group 68)
            ['name' => 'admin.birthdayes.view', 'group_id' => 68],

            // sidebar_manager (group 69)
            ['name' => 'admin.sidebar_manager.view', 'group_id' => 69],

            // financial_dashboard (group 70)
            ['name' => 'admin.financial_dashboard.view',   'group_id' => 70],
            ['name' => 'admin.financial_dashboard.verify', 'group_id' => 70],
            ['name' => 'admin.financial_dashboard.refund', 'group_id' => 70],

            // financial_pending (group 71)
            ['name' => 'admin.financial_pending.view',   'group_id' => 71],
            ['name' => 'admin.financial_pending.verify', 'group_id' => 71],
            ['name' => 'admin.financial_pending.refund', 'group_id' => 71],

            // financial_expenses (group 72)
            ['name' => 'admin.financial_expenses.view',   'group_id' => 72],
            ['name' => 'admin.financial_expenses.add',    'group_id' => 72],
            ['name' => 'admin.financial_expenses.edit',   'group_id' => 72],
            ['name' => 'admin.financial_expenses.delete', 'group_id' => 72],

            // attendance_settings (group 73)
            ['name' => 'admin.attendance_settings.view', 'group_id' => 73],
            ['name' => 'admin.attendance_settings.edit', 'group_id' => 73],

            // files (group 74)
            ['name' => 'admin.files.view',   'group_id' => 74],
            ['name' => 'admin.files.add',    'group_id' => 74],
            ['name' => 'admin.files.edit',   'group_id' => 74],
            ['name' => 'admin.files.delete', 'group_id' => 74],
            ['name' => 'admin.files.status', 'group_id' => 74],

            // student_payments (group 75 — NEW)
            ['name' => 'admin.student_payments.view',    'group_id' => 75],
            ['name' => 'admin.student_payments.approve', 'group_id' => 75],
            ['name' => 'admin.student_payments.reject',  'group_id' => 75],
        ];

        $allPermissionNames = [];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(
                ['name' => $p['name'], 'guard_name' => 'admin'],
                ['group_id' => $p['group_id']]
            );
            $allPermissionNames[] = $p['name'];
        }

        // ──────────────────────────────────────────────────────────────────────
        // 3.  ADMIN ROLE + SUPER-ADMIN USER
        // ──────────────────────────────────────────────────────────────────────

        // Role
        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => 'admin']
        );

        // Sync ALL permissions to Admin role so the super-admin sees every menu
        $adminRole->syncPermissions(array_unique($allPermissionNames));

        // Super-admin user
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@oxford.ps'],
            [
                'username' => 'superadmin',
                'name'     => 'Super Admin',
                'password' => Hash::make('Admin@1234'),
                'role'     => 1,
                'status'   => 1,
            ]
        );
        $superAdmin->assignRole($adminRole);

        // Also assign all permissions to the existing DatabaseSeeder user
        $existingAdmin = User::where('email', 'moh12@mdit.ps')->first();
        if ($existingAdmin) {
            $existingAdmin->syncRoles([$adminRole]);
        }

        $this->command->info('✓ SidebarSeeder: ' . count($groups) . ' groups, ' . count($allPermissionNames) . ' permissions seeded.');
        $this->command->info('✓ Super-admin: admin@oxford.ps / Admin@1234');
    }
}
