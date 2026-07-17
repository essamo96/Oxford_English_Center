<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

// Configure old DB
Config::set('database.connections.mysql_old', [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => 'oxford_15_old_ahmad',
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => false,
    'engine' => null,
]);

$tables = ['settings', 'socials', 'categories', 'news', 'pages', 'partner', 'photo', 'photos_images', 'vedio'];
$report = [];
$hasDiff = false;

foreach ($tables as $table) {
    try {
        $oldDesc = DB::connection('mysql_old')->select("DESCRIBE $table");
        $newDesc = DB::connection('mysql')->select("DESCRIBE $table");
        
        $oldCols = [];
        foreach ($oldDesc as $col) {
            $oldCols[$col->Field] = $col->Type;
        }
        
        $newCols = [];
        foreach ($newDesc as $col) {
            $newCols[$col->Field] = $col->Type;
        }
        
        $oldKeys = array_keys($oldCols);
        $newKeys = array_keys($newCols);
        
        $missingInNew = array_diff($oldKeys, $newKeys);
        $missingInOld = array_diff($newKeys, $oldKeys);
        
        $diffTypes = [];
        $common = array_intersect($oldKeys, $newKeys);
        foreach($common as $c) {
            if ($oldCols[$c] !== $newCols[$c]) {
                $diffTypes[$c] = "Old: {$oldCols[$c]}, New: {$newCols[$c]}";
            }
        }
        
        if (count($missingInNew) > 0 || count($missingInOld) > 0 || count($diffTypes) > 0) {
            $hasDiff = true;
            $report[$table] = [
                'missing_in_new' => array_values($missingInNew),
                'missing_in_old' => array_values($missingInOld),
                'type_differences' => $diffTypes
            ];
        } else {
            $report[$table] = "Match";
        }
    } catch (\Exception $e) {
        $report[$table] = "Error: " . $e->getMessage();
        $hasDiff = true;
    }
}

echo json_encode(['hasDiff' => $hasDiff, 'report' => $report], JSON_PRETTY_PRINT);
