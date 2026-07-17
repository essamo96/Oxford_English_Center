<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['settings', 'socials', 'categories', 'news', 'pages', 'partner', 'photo', 'photos_images'];
$imageColumnsPattern = '/image|photo|icon|banner|logo|thumbnail|cover|background|attachment|file|path/i';

$publicPath = 'C:\\laragon\\www\\oxford\\public';

$report = [
    'db_images_count' => 0,
    'found_in_old' => 0, // In this case, copied over already
    'copied_count' => 0,
    'missing_count' => 0,
    'modified_paths_count' => 0,
    'missing_list' => []
];

// Read the migration report to get modified paths
$json = file_exists('image_migration_report.json') ? json_decode(file_get_contents('image_migration_report.json'), true) : [];
$report['modified_paths_count'] = isset($json['paths_needing_update']) ? count($json['paths_needing_update']) : 0;
$report['copied_count'] = isset($json['copied_images']) ? $json['copied_images'] : 0;
$report['found_in_old'] = $report['copied_count'];

foreach ($tables as $table) {
    $columns = Schema::getColumnListing($table);
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
            if (strpos($dbValue, 'fa-') === 0) continue; // Skip FontAwesome
            
            $report['db_images_count']++;
            
            $cleanPath = ltrim($dbValue, '/\\');
            $fullPath = rtrim($publicPath, '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cleanPath);
            
            if (!file_exists($fullPath)) {
                $report['missing_count']++;
                $report['missing_list'][] = [
                    'table' => $table,
                    'record_id' => $row->id ?? 'unknown',
                    'column' => $col,
                    'file_name' => basename($cleanPath),
                    'expected_path' => $fullPath,
                    'reason' => 'Image does not exist in the old project, or path mismatch'
                ];
            }
        }
    }
}

file_put_contents('final_phase4_report.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "Phase 4 verification completed.";
