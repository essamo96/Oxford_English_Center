<?php

$seeders = [
    'SettingSeeder' => 'settings',
    'SocialSeeder' => 'socials',
    'CategorySeeder' => 'categories',
    'PageSeeder' => 'pages',
    'PartnerSeeder' => 'partner',
    'NewsSeeder' => 'news',
    'PhotoSeeder' => 'photo',
    'PhotosImageSeeder' => 'photos_images',
];

$dir = __DIR__ . '/database/seeders';
if (!is_dir($dir)) mkdir($dir, 0755, true);

foreach ($seeders as $className => $table) {
    $content = <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class $className extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \$tableName = '$table';
        \$oldData = DB::connection('mysql_old')->table(\$tableName)->get();
        
        DB::table(\$tableName)->delete(); // use delete instead of truncate to handle foreign keys if disabled

        \$insertData = [];
        foreach (\$oldData as \$row) {
            \$insertData[] = (array) \$row;
        }

        foreach (array_chunk(\$insertData, 500) as \$chunk) {
            DB::table(\$tableName)->insert(\$chunk);
        }
    }
}
PHP;
    file_put_contents("$dir/$className.php", $content);
}

// FrontendDatabaseSeeder
$mainSeeder = <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class FrontendDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Setup old database connection dynamically
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

        Schema::disableForeignKeyConstraints();

        \$this->call([
            SettingSeeder::class,
            SocialSeeder::class,
            CategorySeeder::class,
            PageSeeder::class,
            PartnerSeeder::class,
            NewsSeeder::class,
            PhotoSeeder::class,
            PhotosImageSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
PHP;

file_put_contents("$dir/FrontendDatabaseSeeder.php", $mainSeeder);
echo "Seeders generated successfully!";
