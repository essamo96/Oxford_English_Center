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

        $this->call([
            SettingSeeder::class,
            SocialSeeder::class,
            CategorySeeder::class,
            PageSeeder::class,
            PartnerSeeder::class,
            NewsSeeder::class,
            PhotoSeeder::class,
            PhotosImageSeeder::class,
            UpdateImagePathsSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}