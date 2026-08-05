<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamSkill;

class ExamSkillsSeeder extends Seeder
{
    /**
     * Seeds the Examination Center's default question skills/categories
     * (Grammar, Vocabulary, Reading, Listening, Writing, Speaking).
     */
    public function run(): void
    {
        $skills = [
            ['slug' => 'grammar',    'name_ar' => 'قواعد',    'name_en' => 'Grammar'],
            ['slug' => 'vocabulary', 'name_ar' => 'مفردات',    'name_en' => 'Vocabulary'],
            ['slug' => 'reading',    'name_ar' => 'قراءة',     'name_en' => 'Reading'],
            ['slug' => 'listening',  'name_ar' => 'استماع',    'name_en' => 'Listening'],
            ['slug' => 'writing',    'name_ar' => 'كتابة',     'name_en' => 'Writing'],
            ['slug' => 'speaking',   'name_ar' => 'محادثة',    'name_en' => 'Speaking'],
        ];

        foreach ($skills as $skill) {
            ExamSkill::updateOrCreate(
                ['slug' => $skill['slug']],
                ['name_ar' => $skill['name_ar'], 'name_en' => $skill['name_en'], 'status' => 1]
            );
        }

        $this->command?->info('Seeded ' . count($skills) . ' exam skills.');
    }
}
