<?php

use Illuminate\Database\Seeder;
use App\Services\PermissionSyncService;
use Spatie\Permission\Models\Role;

class PermissionsSyncSeeder extends Seeder
{
    public function run()
    {
        $service = new PermissionSyncService();

        $stats = $service->sync(verbose: false);

        $this->command->info("Groups created  : {$stats['groups']}");
        $this->command->info("Permissions added: {$stats['permissions']}");
        $this->command->info("Already existed : {$stats['skipped']}");

        // Assign all permissions to super-admin / Admin role
        $synced = $service->syncRolePermissions('Admin');
        if (!$synced) {
            $synced = $service->syncRolePermissions('admin');
        }
        if (!$synced) {
            $this->command->warn('Admin role not found — skipping role sync.');
        } else {
            $this->command->info('Admin role synced with all permissions.');
        }
    }
}
