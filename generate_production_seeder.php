<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['settings', 'socials', 'categories', 'news', 'pages', 'partner', 'photo', 'photos_images'];

$output = "<?php\n\nnamespace Database\\Seeders;\n\nuse Illuminate\\Database\\Seeder;\nuse Illuminate\\Support\\Facades\\DB;\n\nclass ProductionFrontendSeeder extends Seeder\n{\n    public function run()\n    {\n        // إيقاف فحص القيود مؤقتاً\n        DB::statement('SET FOREIGN_KEY_CHECKS=0;');\n\n";

foreach ($tables as $table) {
    $rows = DB::table($table)->get();
    if ($rows->isEmpty()) continue;
    
    $output .= "        // Table: {$table}\n";
    $output .= "        \$data_{$table} = [\n";
    foreach ($rows as $row) {
        $output .= "            [";
        foreach ((array)$row as $col => $val) {
            if (is_null($val)) {
                $output .= "'{$col}' => null, ";
            } else {
                $val = addslashes($val);
                // Fix newlines for string representation
                $val = str_replace(["\r", "\n"], ["\\r", "\\n"], $val);
                $output .= "'{$col}' => '{$val}', ";
            }
        }
        $output .= "],\n";
    }
    $output .= "        ];\n";
    
    // Use insertOrIgnore or updateOrInsert
    // For settings, usually it's update
    if ($table === 'settings') {
        $output .= "        foreach (\$data_{$table} as \$row) {\n";
        $output .= "            DB::table('{$table}')->updateOrInsert(['id' => \$row['id']], \$row);\n";
        $output .= "        }\n\n";
    } else {
        // For others, we can use insertOrIgnore to not overwrite new data on server
        $output .= "        foreach (\$data_{$table} as \$row) {\n";
        $output .= "            \$exists = DB::table('{$table}')->where('id', \$row['id'])->exists();\n";
        $output .= "            if (!\$exists) {\n";
        $output .= "                DB::table('{$table}')->insert(\$row);\n";
        $output .= "            }\n";
        $output .= "        }\n\n";
    }
}

$output .= "        // إعادة تشغيل فحص القيود\n        DB::statement('SET FOREIGN_KEY_CHECKS=1;');\n";
$output .= "    }\n}\n";

file_put_contents('database/seeders/ProductionFrontendSeeder.php', $output);
echo "ProductionFrontendSeeder created successfully.\n";
