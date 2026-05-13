<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportDataFromOldDb extends Command
{
    protected $signature = 'data:export';
    protected $description = 'Export data from the old database to JSON files';

    public function handle()
    {
        $tables = [
            'pages',
            'vedio',
            'categories',
            'settings',
            'partner',
            'socials'
        ];

        // Sliders is tricky because it might be photo or something else
        // We'll check if sliders table exists on remote
        try {
            DB::connection('old_db')->getPdo();
            $this->info('Connected to old_db.');
        } catch (\Exception $e) {
            $this->error('Could not connect to old_db: ' . $e->getMessage());
            return 1;
        }

        $remoteTables = DB::connection('old_db')->select('SHOW TABLES');
        $remoteTableNames = array_map(function($t) {
            return array_values((array)$t)[0];
        }, $remoteTables);

        if (in_array('sliders', $remoteTableNames)) {
            $tables[] = 'sliders';
        } elseif (in_array('hero_sliders', $remoteTableNames)) {
            $tables[] = 'hero_sliders';
        } elseif (in_array('photo', $remoteTableNames)) {
            $tables[] = 'photo';
        }

        $exportDir = database_path('seeders/data');
        if (!File::exists($exportDir)) {
            File::makeDirectory($exportDir, 0755, true);
        }

        foreach ($tables as $table) {
            if (!in_array($table, $remoteTableNames)) {
                $this->warn("Table '{$table}' does not exist on remote. Skipping.");
                continue;
            }

            $this->info("Exporting '{$table}'...");
            $data = DB::connection('old_db')->table($table)->get();
            
            File::put($exportDir . "/{$table}.json", $data->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info("Exported " . $data->count() . " rows to '{$table}.json'.");
        }

        $this->info('Data export completed.');
        return 0;
    }
}
