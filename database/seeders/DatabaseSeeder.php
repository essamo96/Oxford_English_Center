<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(FeeTypeSeeder::class);
        $this->call(SidebarSeeder::class);
        // 1. Create Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'admin']);

        // 2. Create Admin User
        $adminUser = User::updateOrCreate(
            ['email' => 'moh12@mdit.ps'],
            [
                'username' => 'admin',
                'name' => 'Main Admin',
                'password' => Hash::make('password'),
                'role' => 1,
                'status' => 1,
            ]
        );

        // 3. Permission Groups
        $groups = array (
  0 => 
  array (
    'id' => 1,
    'name' => 'settings',
    'name_ar' => 'الإعدادات',
    'name_en' => 'Settings',
  ),
  1 => 
  array (
    'id' => 2,
    'name' => 'users',
    'name_ar' => 'إدارة المستخدمين',
    'name_en' => 'User management',
  ),
  2 => 
  array (
    'id' => 3,
    'name' => 'roles',
    'name_ar' => 'مجموعات المستخدمين',
    'name_en' => 'Roles Mangement',
  ),
  3 => 
  array (
    'id' => 4,
    'name' => 'categories',
    'name_ar' => 'التصنيفات',
    'name_en' => 'Categories',
  ),
  4 => 
  array (
    'id' => 5,
    'name' => 'news',
    'name_ar' => 'الأخبار',
    'name_en' => 'News',
  ),
  5 => 
  array (
    'id' => 6,
    'name' => 'photos',
    'name_ar' => 'الصور',
    'name_en' => 'Photos',
  ),
  6 => 
  array (
    'id' => 7,
    'name' => 'videos',
    'name_ar' => 'الفيديو',
    'name_en' => 'Videos',
  ),
  7 => 
  array (
    'id' => 8,
    'name' => 'social',
    'name_ar' => 'الشبكات الإجتماعية',
    'name_en' => 'Social Media',
  ),
  8 => 
  array (
    'id' => 9,
    'name' => 'contact',
    'name_ar' => 'إتصل بنا',
    'name_en' => 'Contact Us',
  ),
  9 => 
  array (
    'id' => 10,
    'name' => 'pages',
    'name_ar' => 'الصفحات الثابته',
    'name_en' => 'Static Pages',
  ),
  10 => 
  array (
    'id' => 11,
    'name' => 'partners',
    'name_ar' => 'الشركاء',
    'name_en' => 'Partners',
  ),
  11 => 
  array (
    'id' => 12,
    'name' => 'programs',
    'name_ar' => 'البرامج',
    'name_en' => 'Programs',
  ),
  12 => 
  array (
    'id' => 13,
    'name' => 'groups',
    'name_ar' => 'المجموعات التعليمية',
    'name_en' => 'Groups',
  ),
  13 => 
  array (
    'id' => 14,
    'name' => 'students',
    'name_ar' => 'الطلاب',
    'name_en' => 'Students',
  ),
  14 => 
  array (
    'id' => 15,
    'name' => 'times',
    'name_ar' => 'ادارة الاوقات',
    'name_en' => 'Time Management',
  ),
  15 => 
  array (
    'id' => 16,
    'name' => 'fees',
    'name_ar' => 'الرسوم',
    'name_en' => 'Fees',
  ),
  16 => 
  array (
    'id' => 17,
    'name' => 'evaluations',
    'name_ar' => 'التقييمات',
    'name_en' => 'Evaluations',
  ),
  17 => 
  array (
    'id' => 18,
    'name' => 'teachers',
    'name_ar' => 'المعلمون',
    'name_en' => 'Teachers',
  ),
  18 => 
  array (
    'id' => 19,
    'name' => 'educational_materials',
    'name_ar' => 'المواد التعليمية',
    'name_en' => 'Educational Materials',
  ),
  19 => 
  array (
    'id' => 20,
    'name' => 'assessments',
    'name_ar' => 'التقييمات',
    'name_en' => 'Assessments',
  ),
  20 => 
  array (
    'id' => 21,
    'name' => 'teacher_evaluations',
    'name_ar' => 'أسئلة تقييمات المعلمين',
    'name_en' => 'Teacher Evaluations',
  ),
  21 => 
  array (
    'id' => 22,
    'name' => 'closed_groups',
    'name_ar' => 'إشعارات المجموعات المغلقة',
    'name_en' => 'Closed Groups',
  ),
  22 => 
  array (
    'id' => 23,
    'name' => 'progress_menu',
    'name_ar' => 'مستوى التقدم',
    'name_en' => 'Progress Level',
  ),
  23 => 
  array (
    'id' => 24,
    'name' => 'student_inquiries',
    'name_ar' => 'استعلامات الطلاب',
    'name_en' => 'Student Inquiries',
  ),
  24 => 
  array (
    'id' => 25,
    'name' => 'certificates',
    'name_ar' => 'الشهادات',
    'name_en' => 'Certificates',
  ),
  25 => 
  array (
    'id' => 26,
    'name' => 'permissions_group',
    'name_ar' => 'القائمة الجانبية',
    'name_en' => 'sidebar role',
  ),
  26 => 
  array (
    'id' => 27,
    'name' => 'dashboard',
    'name_ar' => 'الرئيسية',
    'name_en' => 'Dashboard',
  ),
  27 => 
  array (
    'id' => 28,
    'name' => 'permissions',
    'name_ar' => 'صلاحيات القائمة الجانبية',
    'name_en' => 'Permissions',
  ),
  28 => 
  array (
    'id' => 29,
    'name' => 'pending_orders',
    'name_ar' => 'الطلبات العالقة',
    'name_en' => 'Pending Orders',
  ),
  29 => 
  array (
    'id' => 30,
    'name' => 'membership',
    'name_ar' => 'العضوية',
    'name_en' => 'Membership',
  ),
  30 => 
  array (
    'id' => 31,
    'name' => 'academy',
    'name_ar' => 'إدارة الأكاديمية',
    'name_en' => 'Academy Management',
  ),
  31 => 
  array (
    'id' => 34,
    'name' => 'teacher_attendance',
    'name_ar' => 'كشف حضور المدرسين',
    'name_en' => 'Teacher Attendance',
  ),
  32 => 
  array (
    'id' => 35,
    'name' => 'ask_update',
    'name_ar' => 'طلبات تحديث الملف الشخصي',
    'name_en' => 'Profile update requests',
  ),
  33 => 
  array (
    'id' => 36,
    'name' => 'email_campaigns',
    'name_ar' => 'حملات البريد',
    'name_en' => 'Email Campaigns',
  ),
  34 => 
  array (
    'id' => 37,
    'name' => 'courses',
    'name_ar' => 'الدورات',
    'name_en' => 'Courses',
  ),
  35 => 
  array (
    'id' => 38,
    'name' => 'main_dashboard',
    'name_ar' => 'لوحة التحكم الرئيسية',
    'name_en' => 'Main Dashboard',
  ),
  36 => 
  array (
    'id' => 40,
    'name' => 'financial',
    'name_ar' => 'الإدارة المالية',
    'name_en' => 'Financial Management',
  ),
);


        foreach ($groups as $group) {
            DB::table('permissions_group')->updateOrInsert(['id' => $group['id']], [
                'name' => $group['name'],
                'name_ar' => $group['name_ar'],
                'name_en' => $group['name_en'],
                'status' => 1,
            ]);
        }

        // 4. Permissions
        $permissions = array (
  0 => 
  array (
    'name' => 'admin.settings.view',
    'group_id' => '1',
  ),
  1 => 
  array (
    'name' => 'admin.users.view',
    'group_id' => '2',
  ),
  2 => 
  array (
    'name' => 'admin.users.add',
    'group_id' => '2',
  ),
  3 => 
  array (
    'name' => 'admin.users.edit',
    'group_id' => '2',
  ),
  4 => 
  array (
    'name' => 'admin.users.delete',
    'group_id' => '2',
  ),
  5 => 
  array (
    'name' => 'admin.users.status',
    'group_id' => '2',
  ),
  6 => 
  array (
    'name' => 'admin.users.password',
    'group_id' => '2',
  ),
  7 => 
  array (
    'name' => 'admin.roles.view',
    'group_id' => '3',
  ),
  8 => 
  array (
    'name' => 'admin.roles.add',
    'group_id' => '3',
  ),
  9 => 
  array (
    'name' => 'admin.roles.edit',
    'group_id' => '3',
  ),
  10 => 
  array (
    'name' => 'admin.roles.delete',
    'group_id' => '3',
  ),
  11 => 
  array (
    'name' => 'admin.roles.status',
    'group_id' => '3',
  ),
  12 => 
  array (
    'name' => 'admin.roles.permissions',
    'group_id' => '3',
  ),
  13 => 
  array (
    'name' => 'admin.categories.view',
    'group_id' => '4',
  ),
  14 => 
  array (
    'name' => 'admin.categories.add',
    'group_id' => '4',
  ),
  15 => 
  array (
    'name' => 'admin.categories.edit',
    'group_id' => '4',
  ),
  16 => 
  array (
    'name' => 'admin.categories.delete',
    'group_id' => '4',
  ),
  17 => 
  array (
    'name' => 'admin.categories.status',
    'group_id' => '4',
  ),
  18 => 
  array (
    'name' => 'admin.news.view',
    'group_id' => '5',
  ),
  19 => 
  array (
    'name' => 'admin.news.add',
    'group_id' => '5',
  ),
  20 => 
  array (
    'name' => 'admin.news.edit',
    'group_id' => '5',
  ),
  21 => 
  array (
    'name' => 'admin.news.delete',
    'group_id' => '5',
  ),
  22 => 
  array (
    'name' => 'admin.news.publish',
    'group_id' => '5',
  ),
  23 => 
  array (
    'name' => 'admin.photos.view',
    'group_id' => '6',
  ),
  24 => 
  array (
    'name' => 'admin.photos.delete',
    'group_id' => '6',
  ),
  25 => 
  array (
    'name' => 'admin.photos.status',
    'group_id' => '6',
  ),
  26 => 
  array (
    'name' => 'admin.videos.view',
    'group_id' => '7',
  ),
  27 => 
  array (
    'name' => 'admin.videos.add',
    'group_id' => '7',
  ),
  28 => 
  array (
    'name' => 'admin.videos.edit',
    'group_id' => '7',
  ),
  29 => 
  array (
    'name' => 'admin.videos.delete',
    'group_id' => '7',
  ),
  30 => 
  array (
    'name' => 'admin.videos.status',
    'group_id' => '7',
  ),
  31 => 
  array (
    'name' => 'admin.social.view',
    'group_id' => '8',
  ),
  32 => 
  array (
    'name' => 'admin.contact.view',
    'group_id' => '9',
  ),
  33 => 
  array (
    'name' => 'admin.contact.status',
    'group_id' => '9',
  ),
  34 => 
  array (
    'name' => 'admin.contact.delete',
    'group_id' => '9',
  ),
  35 => 
  array (
    'name' => 'admin.contact.reply',
    'group_id' => '9',
  ),
  36 => 
  array (
    'name' => 'admin.photos.edit',
    'group_id' => '6',
  ),
  37 => 
  array (
    'name' => 'admin.photos.add',
    'group_id' => '6',
  ),
  38 => 
  array (
    'name' => 'admin.pages.view',
    'group_id' => '10',
  ),
  39 => 
  array (
    'name' => 'admin.pages.edit',
    'group_id' => '10',
  ),
  40 => 
  array (
    'name' => 'admin.partners.view',
    'group_id' => '11',
  ),
  41 => 
  array (
    'name' => 'admin.partners.delete',
    'group_id' => '11',
  ),
  42 => 
  array (
    'name' => 'admin.partners.status',
    'group_id' => '11',
  ),
  43 => 
  array (
    'name' => 'admin.partners.edit',
    'group_id' => '11',
  ),
  44 => 
  array (
    'name' => 'admin.partners.add',
    'group_id' => '11',
  ),
  45 => 
  array (
    'name' => 'admin.programs.view',
    'group_id' => '12',
  ),
  46 => 
  array (
    'name' => 'admin.programs.delete',
    'group_id' => '12',
  ),
  47 => 
  array (
    'name' => 'admin.programs.status',
    'group_id' => '12',
  ),
  48 => 
  array (
    'name' => 'admin.programs.edit',
    'group_id' => '12',
  ),
  49 => 
  array (
    'name' => 'admin.programs.add',
    'group_id' => '12',
  ),
  50 => 
  array (
    'name' => 'admin.groups.view',
    'group_id' => '13',
  ),
  51 => 
  array (
    'name' => 'admin.groups.delete',
    'group_id' => '13',
  ),
  52 => 
  array (
    'name' => 'admin.groups.status',
    'group_id' => '13',
  ),
  53 => 
  array (
    'name' => 'admin.groups.edit',
    'group_id' => '13',
  ),
  54 => 
  array (
    'name' => 'admin.groups.add',
    'group_id' => '13',
  ),
  55 => 
  array (
    'name' => 'admin.students.view',
    'group_id' => '14',
  ),
  56 => 
  array (
    'name' => 'admin.students.delete',
    'group_id' => '14',
  ),
  57 => 
  array (
    'name' => 'admin.students.status',
    'group_id' => '14',
  ),
  58 => 
  array (
    'name' => 'admin.students.edit',
    'group_id' => '14',
  ),
  59 => 
  array (
    'name' => 'admin.students.add',
    'group_id' => '14',
  ),
  60 => 
  array (
    'name' => 'admin.times.view',
    'group_id' => '15',
  ),
  61 => 
  array (
    'name' => 'admin.times.delete',
    'group_id' => '15',
  ),
  62 => 
  array (
    'name' => 'admin.times.status',
    'group_id' => '15',
  ),
  63 => 
  array (
    'name' => 'admin.times.edit',
    'group_id' => '15',
  ),
  64 => 
  array (
    'name' => 'admin.times.add',
    'group_id' => '15',
  ),
  65 => 
  array (
    'name' => 'admin.fees.view',
    'group_id' => '16',
  ),
  66 => 
  array (
    'name' => 'admin.fees.delete',
    'group_id' => '16',
  ),
  67 => 
  array (
    'name' => 'admin.fees.status',
    'group_id' => '16',
  ),
  68 => 
  array (
    'name' => 'admin.fees.edit',
    'group_id' => '16',
  ),
  69 => 
  array (
    'name' => 'admin.fees.add',
    'group_id' => '16',
  ),
  70 => 
  array (
    'name' => 'admin.evaluations.view',
    'group_id' => '17',
  ),
  71 => 
  array (
    'name' => 'admin.evaluations.delete',
    'group_id' => '17',
  ),
  72 => 
  array (
    'name' => 'admin.evaluations.status',
    'group_id' => '17',
  ),
  73 => 
  array (
    'name' => 'admin.evaluations.edit',
    'group_id' => '17',
  ),
  74 => 
  array (
    'name' => 'admin.evaluations.add',
    'group_id' => '17',
  ),
  75 => 
  array (
    'name' => 'admin.teachers.view',
    'group_id' => '18',
  ),
  76 => 
  array (
    'name' => 'admin.teachers.delete',
    'group_id' => '18',
  ),
  77 => 
  array (
    'name' => 'admin.teachers.status',
    'group_id' => '18',
  ),
  78 => 
  array (
    'name' => 'admin.teachers.edit',
    'group_id' => '18',
  ),
  79 => 
  array (
    'name' => 'admin.teachers.add',
    'group_id' => '18',
  ),
  80 => 
  array (
    'name' => 'admin.groups.student.view',
    'group_id' => '13',
  ),
  81 => 
  array (
    'name' => 'admin.groups.student.delete',
    'group_id' => '13',
  ),
  82 => 
  array (
    'name' => 'admin.groups.student.add',
    'group_id' => '13',
  ),
  83 => 
  array (
    'name' => 'admin.educational_materials.view',
    'group_id' => '19',
  ),
  84 => 
  array (
    'name' => 'admin.educational_materials.delete',
    'group_id' => '19',
  ),
  85 => 
  array (
    'name' => 'admin.educational_materials.edit',
    'group_id' => '19',
  ),
  86 => 
  array (
    'name' => 'admin.educational_materials.add',
    'group_id' => '19',
  ),
  87 => 
  array (
    'name' => 'admin.educational_materials.status',
    'group_id' => '19',
  ),
  88 => 
  array (
    'name' => 'admin.assessments.view',
    'group_id' => '20',
  ),
  89 => 
  array (
    'name' => 'admin.assessments.delete',
    'group_id' => '20',
  ),
  90 => 
  array (
    'name' => 'admin.assessments.edit',
    'group_id' => '20',
  ),
  91 => 
  array (
    'name' => 'admin.assessments.add',
    'group_id' => '20',
  ),
  92 => 
  array (
    'name' => 'admin.assessments.status',
    'group_id' => '20',
  ),
  93 => 
  array (
    'name' => 'admin.teacher_evaluations.view',
    'group_id' => '21',
  ),
  94 => 
  array (
    'name' => 'admin.teacher_evaluations.delete',
    'group_id' => '21',
  ),
  95 => 
  array (
    'name' => 'admin.teacher_evaluations.edit',
    'group_id' => '21',
  ),
  96 => 
  array (
    'name' => 'admin.teacher_evaluations.add',
    'group_id' => '21',
  ),
  97 => 
  array (
    'name' => 'admin.teacher_evaluations.status',
    'group_id' => '21',
  ),
  98 => 
  array (
    'name' => 'admin.closed_groups.view',
    'group_id' => '22',
  ),
  99 => 
  array (
    'name' => 'admin.closed_groups.delete',
    'group_id' => '22',
  ),
  100 => 
  array (
    'name' => 'admin.progress_menu.view',
    'group_id' => '23',
  ),
  101 => 
  array (
    'name' => 'admin.student_inquiries.view',
    'group_id' => '24',
  ),
  102 => 
  array (
    'name' => 'admin.certificates.view',
    'group_id' => '25',
  ),
  103 => 
  array (
    'name' => 'admin.certificates.delete',
    'group_id' => '25',
  ),
  104 => 
  array (
    'name' => 'admin.certificates.status',
    'group_id' => '25',
  ),
  105 => 
  array (
    'name' => 'admin.certificates.edit',
    'group_id' => '25',
  ),
  106 => 
  array (
    'name' => 'admin.certificates.add',
    'group_id' => '25',
  ),
  107 => 
  array (
    'name' => 'admin.permissions_group.view',
    'group_id' => '26',
  ),
  108 => 
  array (
    'name' => 'admin.permissions_group.delete',
    'group_id' => '26',
  ),
  109 => 
  array (
    'name' => 'admin.permissions_group.status',
    'group_id' => '26',
  ),
  110 => 
  array (
    'name' => 'admin.permissions_group.edit',
    'group_id' => '26',
  ),
  111 => 
  array (
    'name' => 'admin.permissions_group.add',
    'group_id' => '26',
  ),
  112 => 
  array (
    'name' => 'admin.permissions.view',
    'group_id' => '28',
  ),
  113 => 
  array (
    'name' => 'admin.permissions.delete',
    'group_id' => '28',
  ),
  114 => 
  array (
    'name' => 'admin.permissions.status',
    'group_id' => '28',
  ),
  115 => 
  array (
    'name' => 'admin.permissions.edit',
    'group_id' => '28',
  ),
  116 => 
  array (
    'name' => 'admin.permissions.add',
    'group_id' => '28',
  ),
  117 => 
  array (
    'name' => 'admin.dashboard.view',
    'group_id' => '27',
  ),
  118 => 
  array (
    'name' => 'admin.pending_orders.view',
    'group_id' => '29',
  ),
  119 => 
  array (
    'name' => 'admin.membership.view',
    'group_id' => '30',
  ),
  120 => 
  array (
    'name' => 'admin.membership.delete',
    'group_id' => '30',
  ),
  121 => 
  array (
    'name' => 'admin.membership.edit',
    'group_id' => '30',
  ),
  122 => 
  array (
    'name' => 'admin.membership.sendmesage',
    'group_id' => '30',
  ),
  123 => 
  array (
    'name' => 'admin.membership.sendmail',
    'group_id' => '30',
  ),
  124 => 
  array (
    'name' => 'admin.membership.status',
    'group_id' => '30',
  ),
  125 => 
  array (
    'name' => 'admin.academy.view',
    'group_id' => '31',
  ),
  126 => 
  array (
    'name' => 'admin.teacher_attendance.view',
    'group_id' => '34',
  ),
  127 => 
  array (
    'name' => 'admin.teacher_attendance.add',
    'group_id' => '34',
  ),
  128 => 
  array (
    'name' => 'admin.teacher_attendance.edit',
    'group_id' => '34',
  ),
  129 => 
  array (
    'name' => 'admin.teacher_attendance.delete',
    'group_id' => '34',
  ),
  130 => 
  array (
    'name' => 'admin.ask_update.view',
    'group_id' => '35',
  ),
  131 => 
  array (
    'name' => 'admin.ask_update.edit',
    'group_id' => '35',
  ),
  132 => 
  array (
    'name' => 'admin.ask_update.delete',
    'group_id' => '35',
  ),
  133 => 
  array (
    'name' => 'admin.ask_update.status',
    'group_id' => '35',
  ),
  134 => 
  array (
    'name' => 'admin.ask_update.refuse',
    'group_id' => '35',
  ),
  135 => 
  array (
    'name' => 'admin.ask_update.accept',
    'group_id' => '35',
  ),
  136 => 
  array (
    'name' => 'admin.email_campaigns.send',
    'group_id' => '36',
  ),
  137 => 
  array (
    'name' => 'admin.email_campaigns.manage',
    'group_id' => '36',
  ),
  138 => 
  array (
    'name' => 'admin.email_campaigns.view',
    'group_id' => '36',
  ),
  140 => 
  array (
    'name' => 'admin.financial.view',
    'group_id' => '40',
  ),
  141 => 
  array (
    'name' => 'admin.financial.verify',
    'group_id' => '40',
  ),
  142 => 
  array (
    'name' => 'admin.financial.refund',
    'group_id' => '40',
  ),
);


        $allPermissionNames = [];
        foreach ($permissions as $p) {
            Permission::firstOrCreate(
                ['name' => $p['name'], 'guard_name' => 'admin'],
                ['group_id' => $p['group_id']]
            );
            $allPermissionNames[] = $p['name'];
        }

        // Sync to Admin role
        $adminRole->syncPermissions(array_unique($allPermissionNames));
        
        // Ensure Admin user has the role
        $adminUser->assignRole($adminRole);

        // 5. Seed other tables from exported JSON if available
        $this->seedFromDataFiles();
    }

    private function seedFromDataFiles()
    {
        $tables = ['pages', 'vedio', 'categories', 'settings', 'partner', 'socials', 'sliders', 'hero_sliders', 'photo'];
        $dataDir = database_path('seeders/data');

        if (!\Illuminate\Support\Facades\File::exists($dataDir)) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $table) {
            $file = $dataDir . "/{$table}.json";
            if (\Illuminate\Support\Facades\File::exists($file)) {
                $data = json_decode(\Illuminate\Support\Facades\File::get($file), true);
                if ($data) {
                    $this->command->info("Seeding {$table} from JSON...");
                    // Truncate first to ensure clean state
                    DB::table($table)->truncate();
                    
                    // Chunk insertion
                    foreach (array_chunk($data, 100) as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                    $this->command->info("Seeded {$table} successfully.");
                }
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}