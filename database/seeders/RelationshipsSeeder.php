<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Relationship;

class RelationshipsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name_ar' => 'الأب',    'name_en' => 'Father',      'slug' => 'father',      'sort_order' => 1],
            ['name_ar' => 'الأم',    'name_en' => 'Mother',      'slug' => 'mother',      'sort_order' => 2],
            ['name_ar' => 'الأخ',    'name_en' => 'Brother',     'slug' => 'brother',     'sort_order' => 3],
            ['name_ar' => 'الأخت',   'name_en' => 'Sister',      'slug' => 'sister',      'sort_order' => 4],
            ['name_ar' => 'الجد',    'name_en' => 'Grandfather', 'slug' => 'grandfather', 'sort_order' => 5],
            ['name_ar' => 'الجدة',   'name_en' => 'Grandmother', 'slug' => 'grandmother', 'sort_order' => 6],
            ['name_ar' => 'العم',    'name_en' => 'Uncle',       'slug' => 'uncle',       'sort_order' => 7],
            ['name_ar' => 'الخال',   'name_en' => 'Maternal Uncle', 'slug' => 'maternal-uncle', 'sort_order' => 8],
            ['name_ar' => 'العمة',   'name_en' => 'Aunt',        'slug' => 'aunt',        'sort_order' => 9],
            ['name_ar' => 'الخالة',  'name_en' => 'Maternal Aunt', 'slug' => 'maternal-aunt', 'sort_order' => 10],
            ['name_ar' => 'الوصي القانوني', 'name_en' => 'Legal Guardian', 'slug' => 'legal-guardian', 'sort_order' => 11],
            ['name_ar' => 'أخرى',    'name_en' => 'Other',       'slug' => 'other',       'sort_order' => 99, 'is_other' => true],
        ];

        foreach ($items as $item) {
            Relationship::updateOrCreate(
                ['slug' => $item['slug']],
                array_merge(['is_active' => true, 'is_other' => $item['is_other'] ?? false], $item)
            );
        }
    }
}
