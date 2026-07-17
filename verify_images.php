<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['settings', 'socials', 'categories', 'news', 'pages', 'partner', 'photo', 'photos_images'];
$imageColumnsPattern = '/image|photo|icon|banner|logo|thumbnail|cover|background|attachment|file|path/i';
$publicPath = 'C:\\laragon\\www\\oxford\\public';

$report = [
    'total_db_images' => 0,
    'found_images' => 0,
    'missing_images' => [],
];

foreach ($tables as $table) {
    if (!DB::getSchemaBuilder()->hasTable($table)) {
        continue;
    }
    
    $columns = DB::getSchemaBuilder()->getColumnListing($table);
    $imgCols = [];
    foreach ($columns as $col) {
        if (preg_match($imageColumnsPattern, $col)) {
            $imgCols[] = $col;
        }
    }
    
    if (empty($imgCols)) continue;
    
    $rows = DB::table($table)->get();
    foreach ($rows as $row) {
        foreach ($imgCols as $col) {
            $dbValue = $row->{$col};
            if (empty($dbValue)) continue;
            
            // Ignore FontAwesome icons
            if (strpos($dbValue, 'fa-') === 0) continue;
            
            $report['total_db_images']++;
            
            // Expected file path in public
            // Remove leading slashes if any
            $cleanPath = ltrim($dbValue, '/\\');
            // Normalize path separators
            $cleanPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $cleanPath);
            
            $expectedFilePath = $publicPath . DIRECTORY_SEPARATOR . $cleanPath;
            
            if (file_exists($expectedFilePath) && is_file($expectedFilePath)) {
                $report['found_images']++;
            } else {
                $report['missing_images'][] = [
                    'table' => $table,
                    'record_id' => $row->id ?? 'unknown',
                    'column' => $col,
                    'file_name' => basename($dbValue),
                    'expected_path' => $cleanPath,
                    'reason' => 'File not found in new project directory after extraction'
                ];
            }
        }
    }
}

file_put_contents('verification_report.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Verification completed.";
