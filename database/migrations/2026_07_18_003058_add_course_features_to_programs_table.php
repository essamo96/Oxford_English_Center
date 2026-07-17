<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('age', 191)->nullable();
            $table->string('level', 191)->nullable();
            $table->string('weeks', 191)->nullable();
            $table->string('hours', 191)->nullable();
            $table->string('mock', 191)->nullable();
            $table->string('duration', 191)->nullable();
            $table->string('class_size', 191)->nullable();
            $table->string('start', 191)->nullable();
            $table->string('days', 191)->nullable();
            $table->string('time', 191)->nullable();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id')->nullable();
        });

        // Seed default values for existing programs
        DB::table('programs')->update([
            'age' => '16 years old and older',
            'level' => 'Intermediate and above',
            'weeks' => '5 weeks',
            'hours' => '30 hours',
            'mock' => '3 (hours 10)',
            'duration' => '15 sessions (2 hours each)',
            'class_size' => 'Average 10, Maximum 15',
            'start' => 'Monthly',
            'days' => 'Saturday-Monday-Wednesday / Sunday-Tuesday-Thursday',
        ]);

        // Map existing Pages to existing or new Programs
        $pageMappings = [
            'ielts' => ['page_title' => 'IELTS Preparation Course', 'program_title' => 'IELTS  Course'],
            'general' => ['page_title' => 'General English Levels (CEFRL)', 'program_title' => 'English Levels (CEFRL)'],
            'speaking' => ['page_title' => 'Speaking Course', 'program_title' => 'Speaking Course'],
            'writing' => ['page_title' => 'Academic WritIng Course', 'program_title' => 'WritIng Course'],
            'business' => ['page_title' => 'Business English Course', 'program_title' => 'Business English Course'],
            'esp' => ['page_title' => 'English for Specific Purposes - ESP', 'program_title' => 'English for Specific Purposes - ESP'],
        ];

        foreach ($pageMappings as $slug => $mapping) {
            $page = DB::table('pages')->where('slug', $slug)->first();
            if ($page) {
                // Find or create the corresponding program
                $program = DB::table('programs')->where('title', $mapping['program_title'])->first();
                if (!$program) {
                    $programId = DB::table('programs')->insertGetId([
                        'title' => $mapping['program_title'],
                        'short' => $page->details ? substr(strip_tags($page->details), 0, 190) : 'Course',
                        'image' => $page->image ?? 'assets/oxford/img/logo.png',
                        'exam' => 0,
                        'status' => 1,
                        'program_type' => 'adults', // default
                        'age' => '16 years old and older',
                        'level' => 'Intermediate and above',
                        'weeks' => '5 weeks',
                        'hours' => '30 hours',
                        'mock' => '3 (hours 10)',
                        'duration' => '15 sessions (2 hours each)',
                        'class_size' => 'Average 10, Maximum 15',
                        'start' => 'Monthly',
                        'days' => 'Saturday-Monday-Wednesday / Sunday-Tuesday-Thursday',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $programId = $program->id;
                }

                // Link page to program
                DB::table('pages')->where('id', $page->id)->update(['program_id' => $programId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn([
                'age', 'level', 'weeks', 'hours', 'mock', 'duration', 'class_size', 'start', 'days', 'time'
            ]);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('program_id');
        });
    }
};
