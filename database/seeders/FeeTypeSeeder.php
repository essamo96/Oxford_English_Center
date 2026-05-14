<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            [
                'slug'    => 'registration',
                'name_ar' => 'رسوم التسجيل',
                'name_en' => 'Registration Fee',
                'icon'    => 'bi-person-plus',
                'class'   => 'badge-light-primary',
            ],
            [
                'slug'    => 'placement_test',
                'name_ar' => 'رسوم اختبار المستوى',
                'name_en' => 'Placement Test Fee',
                'icon'    => 'bi-clipboard-check',
                'class'   => 'badge-light-info',
            ],
            [
                'slug'    => 'course',
                'name_ar' => 'رسوم الدورة/المستوى',
                'name_en' => 'Course/Level Fee',
                'icon'    => 'bi-journal-bookmark',
                'class'   => 'badge-light-success',
            ],
            [
                'slug'    => 'books',
                'name_ar' => 'رسوم الكتب',
                'name_en' => 'Books Fee',
                'icon'    => 'bi-book',
                'class'   => 'badge-light-warning',
            ],
            [
                'slug'    => 'certificate',
                'name_ar' => 'رسوم الشهادة',
                'name_en' => 'Certificate Fee',
                'icon'    => 'bi-patch-check',
                'class'   => 'badge-light-danger',
            ],
            [
                'slug'    => 'other',
                'name_ar' => 'رسوم أخرى',
                'name_en' => 'Other Fees',
                'icon'    => 'bi-tag',
                'class'   => 'badge-light-secondary',
            ],
        ];

        foreach ($types as $type) {
            DB::table('fee_types')->updateOrInsert(['slug' => $type['slug']], $type);
        }
    }
}
