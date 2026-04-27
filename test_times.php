<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Groups;
use App\Models\Teachers;
use App\Models\Times;
use App\Models\GroupStudents;
use App\Models\Students;
use App\Models\Programs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('groups')->truncate();
    DB::table('teachers')->truncate();
    DB::table('times')->truncate();
    DB::table('group_students')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // 1. Create Times (using 24-hour format)
    $time1 = Times::create([
        'days' => 'Saturday - Monday - Wednesday',
        'times' => '14:00 - 16:00',
        'status' => 1
    ]);
    $time2 = Times::create([
        'days' => 'Sunday - Tuesday - Thursday',
        'times' => '16:00 - 18:00',
        'status' => 1
    ]);
    $time3 = Times::create([
        'days' => 'Saturday - Monday - Wednesday',
        'times' => '18:00 - 20:00',
        'status' => 1
    ]);

    // 2. Create Teachers
    $teacher1 = Teachers::create([
        'name' => 'John Doe',
        'mobile' => '0590000001',
        'email' => 'john@oxford.com',
        'username' => '000001',
        'password' => Hash::make('123456'),
        'status' => 1
    ]);
    $teacher2 = Teachers::create([
        'name' => 'Jane Smith',
        'mobile' => '0590000002',
        'email' => 'jane@oxford.com',
        'username' => '000002',
        'password' => Hash::make('123456'),
        'status' => 1
    ]);

    // 3. Get first program
    $program = Programs::first();
    $program_id = $program ? $program->id : 1;

    // 4. Create Groups
    $group1 = Groups::create([
        'name' => 'Beginners English A1',
        'program_id' => $program_id,
        'teacher_id' => $teacher1->id,
        'date_id' => $time1->id,
        'start_date' => Carbon::now()->subDays(5)->toDateTimeString(),
        'end_date' => Carbon::now()->addMonths(2)->toDateTimeString(),
        'status' => 1,
        'created_at' => Carbon::now()
    ]);

    $group2 = Groups::create([
        'name' => 'Intermediate English B1',
        'program_id' => $program_id,
        'teacher_id' => $teacher2->id,
        'date_id' => $time2->id,
        'start_date' => Carbon::now()->subDays(10)->toDateTimeString(),
        'end_date' => Carbon::now()->addMonths(2)->toDateTimeString(),
        'status' => 1,
        'created_at' => Carbon::now()
    ]);

    // Conflicting Group for Jane Smith (Same time and days as group 2 but named differently)
    $group3 = Groups::create([
        'name' => 'Advanced English C1 (Conflict Test)',
        'program_id' => $program_id,
        'teacher_id' => $teacher2->id, // Jane Smith
        'date_id' => $time2->id,       // Same time!
        'start_date' => Carbon::now()->subDays(2)->toDateTimeString(),
        'end_date' => Carbon::now()->addMonths(1)->toDateTimeString(),
        'status' => 1,
        'created_at' => Carbon::now()
    ]);

    // 5. Attach Students to Groups
    $students = Students::take(10)->get();
    if ($students->count() > 0) {
        foreach ($students->take(4) as $s) {
            GroupStudents::create([
                'student_id' => $s->id,
                'group_id' => $group1->id
            ]);
        }
        // For group 2
        foreach ($students->slice(4, 3) as $s) {
            GroupStudents::create([
                'student_id' => $s->id,
                'group_id' => $group2->id
            ]);
        }
        // For group 3
        foreach ($students->slice(7, 3) as $s) {
            GroupStudents::create([
                'student_id' => $s->id,
                'group_id' => $group3->id
            ]);
        }
    }
    
    echo "Calendar test data seeded successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
