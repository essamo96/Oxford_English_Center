<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tableName = 'categories';
        $oldData = DB::connection('mysql_old')->table($tableName)->get();
        
        DB::table($tableName)->delete(); // use delete instead of truncate to handle foreign keys if disabled

        $insertData = [];
        foreach ($oldData as $row) {
            $insertData[] = (array) $row;
        }

        foreach (array_chunk($insertData, 500) as $chunk) {
            DB::table($tableName)->insert($chunk);
        }
    }
}