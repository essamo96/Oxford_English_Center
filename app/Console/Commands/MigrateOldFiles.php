<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateOldFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filemanager:migrate-old-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate files from legacy Laravel FileManager to modern Metronic FileManager';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting File Migration Process...');

        // Verify Storage Disk
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        
        // Define standard new locations
        $newImagesPath = 'uploads/images';
        $newDocsPath = 'uploads/documents';
        
        if (!$disk->exists($newImagesPath)) { $disk->makeDirectory($newImagesPath); }
        if (!$disk->exists($newDocsPath)) { $disk->makeDirectory($newDocsPath); }

        // Define potential old locations
        $potentialLegacyPaths = [
            public_path('photos'),
            public_path('files'),
            public_path('file_manager'),
            storage_path('app/public/photos'),
        ];

        $movedCount = 0;

        foreach ($potentialLegacyPaths as $legacyPath) {
            if (\Illuminate\Support\Facades\File::exists($legacyPath) && \Illuminate\Support\Facades\File::isDirectory($legacyPath)) {
                $this->info("Scanning legacy path: {$legacyPath}");
                
                $files = \Illuminate\Support\Facades\File::allFiles($legacyPath);
                foreach ($files as $file) {
                    $ext = strtolower($file->getExtension());
                    $filename = $file->getFilename();
                    
                    // Categorize based on ext
                    $isImage = in_array($ext, ['jpg','jpeg','png','gif','svg','webp']);
                    $targetFolder = $isImage ? $newImagesPath : $newDocsPath;
                    $targetPath = $targetFolder . '/' . time() . '_' . preg_replace('/[^A-Za-z0-9.\-]/', '_', $filename);
                    
                    if (! $disk->exists($targetPath)) {
                        $disk->put($targetPath, file_get_contents($file->getPathname()));
                        $movedCount++;
                    }
                }
            }
        }

        $this->info("Migration completed! Organized {$movedCount} files into the new structure.");
        return \Illuminate\Console\Command::SUCCESS;
    }
}
