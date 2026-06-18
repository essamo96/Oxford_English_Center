<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) إضافة المجموعة الأب "إدارة خدمة tweetSMS"
        $parentExists = DB::table('permissions_group')->where('name', 'sms')->exists();
        if (!$parentExists) {
            $parentId = DB::table('permissions_group')->insertGetId([
                'name'      => 'sms',
                'name_ar'   => 'إدارة خدمة tweetSMS',
                'name_en'   => 'SMS Management',
                'icon'      => 'ki-duotone ki-sms',
                'color'     => '#009ef7',
                'sort'      => 90,
                'status'    => 1,
                'parent_id' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2) إضافة العنصر الابن "أرشيف الرسائل"
            $childId = DB::table('permissions_group')->insertGetId([
                'name'      => 'sms_archive',
                'name_ar'   => 'أرشيف الرسائل',
                'name_en'   => 'SMS Archive',
                'sort'      => 1,
                'status'    => 1,
                'parent_id' => $parentId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3) إضافة الصلاحية الخاصة بالأرشيف
            if (!DB::table('permissions')->where('name', 'admin.sms_archive.view')->exists()) {
                DB::table('permissions')->insert([
                    'name'       => 'admin.sms_archive.view',
                    'group_id'   => $childId,
                    'name_ar'    => 'عرض أرشيف الرسائل',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $parent = DB::table('permissions_group')->where('name', 'sms')->first();
        if ($parent) {
            $child = DB::table('permissions_group')->where('name', 'sms_archive')->first();
            if ($child) {
                DB::table('permissions')->where('group_id', $child->id)->delete();
                DB::table('permissions_group')->where('id', $child->id)->delete();
            }
            DB::table('permissions_group')->where('id', $parent->id)->delete();
        }
    }
};
