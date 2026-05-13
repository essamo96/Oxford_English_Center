<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Standardize Group Names and Translations
        $groupsToUpdate = [
            'educational_materials' => ['ar' => 'المواد التعليمية', 'en' => 'Educational Materials'],
            'assessments' => ['ar' => 'التقييمات', 'en' => 'Assessments'],
            'questions' => ['new_name' => 'teacher_evaluations', 'ar' => 'أسئلة تقييمات المعلمين', 'en' => 'Teacher Evaluations'],
            'closed_groups_notifications' => ['new_name' => 'closed_groups', 'ar' => 'إشعارات المجموعات المغلقة', 'en' => 'Closed Groups Notifications'],
            'students_report' => ['new_name' => 'student_inquiries', 'ar' => 'استعلامات الطلاب', 'en' => 'Student Inquiries'],
            'groups.student' => ['new_name' => 'membership', 'ar' => 'العضوية', 'en' => 'Membership'],
            'academy_management' => ['new_name' => 'academy', 'ar' => 'إدارة الأكاديمية', 'en' => 'Academy Management'],
            'absent_teacher' => ['new_name' => 'teacher_attendance', 'ar' => 'كشف حضور المدرسين', 'en' => 'Teacher Attendance'],
        ];

        foreach ($groupsToUpdate as $oldName => $data) {
            $update = [
                'name_ar' => $data['ar'],
                'name_en' => $data['en'],
            ];
            
            if (isset($data['new_name'])) {
                $update['name'] = $data['new_name'];
            }

            DB::table('permissions_group')
                ->where('name', $oldName)
                ->orWhere('name_ar', 'like', '%' . $data['ar'] . '%')
                ->update($update);
        }

        // 2. Run the refactor command
        // This will automatically rename permissions based on the updated group names
        $exitCode = Artisan::call('permissions:refactor');
        
        if ($exitCode !== 0) {
            throw new \Exception('Permissions refactor failed. Check logs.');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback is complex because we don't store the exact old names in the DB.
        // However, we can attempt a reverse rename if we have a way to track it.
        // Since this is a standardization, usually a rollback is not desired.
        // But for safety, we log the changes in 'up'.
        Log::warning('Rollback for permissions refactor migration is not fully automated. Please check logs for previous names.');
    }
};
