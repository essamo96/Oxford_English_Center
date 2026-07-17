<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateImagePathsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Load the array of paths needing update
        require base_path('updates_array.php');

        // $updates is defined in updates_array.php
        if (isset($updates) && is_array($updates)) {
            foreach ($updates as $u) {
                DB::table($u['table'])
                    ->where('id', $u['id'])
                    ->update([$u['column'] => $u['value']]);
            }
        }
    }
}
