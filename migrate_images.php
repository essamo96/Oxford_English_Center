<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

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

$tables = ['settings', 'socials', 'categories', 'news', 'pages', 'partner', 'photo', 'photos_images'];
$imageColumnsPattern = '/image|photo|icon|banner|logo|thumbnail|cover|background|attachment|file|path/i';

$oldPublicPath = 'C:\\laragon\\www\\main main ox\\public';
$newPublicPath = 'C:\\laragon\\www\\oxford\\public';

// Build file map
$fileMap = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($oldPublicPath));
foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    $basename = $file->getFilename();
    $relativePath = str_replace($oldPublicPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
    $relativePath = str_replace('\\', '/', $relativePath); // normalize slashes
    
    // In case of duplicate basenames, prefer 'uploads'
    if (!isset($fileMap[$basename]) || strpos($relativePath, 'uploads') !== false) {
        $fileMap[$basename] = $relativePath;
    }
}

$report = [
    'total_images' => 0,
    'copied_images' => 0,
    'missing_images_count' => 0,
    'missing_images_list' => [],
    'paths_needing_update' => []
];

foreach ($tables as $table) {
    $columns = DB::connection('mysql_old')->getSchemaBuilder()->getColumnListing($table);
    $imgCols = [];
    foreach ($columns as $col) {
        if (preg_match($imageColumnsPattern, $col)) {
            $imgCols[] = $col;
        }
    }
    
    if (empty($imgCols)) continue;
    
    $rows = DB::connection('mysql_old')->table($table)->get();
    foreach ($rows as $row) {
        foreach ($imgCols as $col) {
            $dbValue = $row->{$col};
            if (empty($dbValue)) continue;
            
            // Ignore FontAwesome icons
            if (strpos($dbValue, 'fa-') === 0) continue;
            
            $report['total_images']++;
            
            $basename = basename($dbValue);
            
            // Fix some weird paths like '/uploads/...'
            $cleanDbValue = ltrim($dbValue, '/\\');
            
            $foundRelativePath = null;
            
            // Direct check first
            $directCheck = str_replace('\\', '/', $cleanDbValue);
            if (file_exists($oldPublicPath . '/' . $directCheck)) {
                $foundRelativePath = $directCheck;
            } elseif (isset($fileMap[$basename])) {
                $foundRelativePath = $fileMap[$basename];
            }
            
            if ($foundRelativePath) {
                // Copy file
                $oldFile = $oldPublicPath . '/' . $foundRelativePath;
                $newFile = $newPublicPath . '/' . $foundRelativePath;
                
                $dir = dirname($newFile);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                
                if (!file_exists($newFile)) {
                    copy($oldFile, $newFile);
                }
                $report['copied_images']++;
                
                // Check if DB path needs update
                if ($cleanDbValue !== $foundRelativePath) {
                    $report['paths_needing_update'][] = [
                        'table' => $table,
                        'record_id' => $row->id ?? 'unknown',
                        'column' => $col,
                        'old_db_value' => $dbValue,
                        'new_db_value' => $foundRelativePath
                    ];
                }
            } else {
                $report['missing_images_count']++;
                $report['missing_images_list'][] = [
                    'table' => $table,
                    'record_id' => $row->id ?? 'unknown',
                    'column' => $col,
                    'file_name' => $basename,
                    'db_value' => $dbValue
                ];
            }
        }
    }
}

file_put_contents('image_migration_report.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "Intelligent image migration completed. Report saved to image_migration_report.json.";
