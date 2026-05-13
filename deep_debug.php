<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== 1. Permissions guard_name sample ===\n";
$perms = DB::table('permissions')->limit(5)->get(['id','name','guard_name']);
foreach ($perms as $p) {
    echo "  [{$p->id}] {$p->name} | guard: {$p->guard_name}\n";
}

echo "\n=== 2. Roles ===\n";
$roles = DB::table('roles')->get();
foreach ($roles as $r) {
    echo "  [{$r->id}] {$r->name} | guard: {$r->guard_name}\n";
}

echo "\n=== 3. model_has_roles (admin user) ===\n";
$user = DB::table('users')->where('role',1)->first();
echo "  Admin user id: {$user->id}\n";
$mhr = DB::table('model_has_roles')->where('model_id', $user->id)->get();
foreach ($mhr as $r) {
    echo "  role_id: {$r->role_id} | model_type: {$r->model_type}\n";
}

echo "\n=== 4. role_has_permissions count ===\n";
$rhp = DB::table('role_has_permissions')->count();
echo "  Total role_has_permissions: {$rhp}\n";

echo "\n=== 5. Spatie config guard ===\n";
$cfg = config('permission.guard_name', 'NOT SET');
echo "  Default guard from config: {$cfg}\n";

echo "\n=== 6. Check DataTables AJAX response simulation ===\n";
// Simulate what the students list query returns
$students = DB::table('students')->whereNull('deleted_at')->count();
echo "  Students in DB (no filters): {$students}\n";
$activeStudents = DB::table('students')->whereNull('deleted_at')->where('status',1)->count();
echo "  Active students (status=1): {$activeStudents}\n";
$pendingStudents = DB::table('students')->whereNull('deleted_at')->where('status',0)->count();
echo "  Pending students (status=0, for membership): {$pendingStudents}\n";

echo "\n=== 7. Session driver ===\n";
$driver = config('session.driver');
echo "  Session driver: {$driver}\n";
