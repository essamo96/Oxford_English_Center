<?php

namespace App\Console\Commands;

use App\Services\PermissionSyncService;
use Illuminate\Console\Command;

class SyncPermissions extends Command
{
    protected $signature = 'permissions:sync
                            {--role=Admin : Role name to assign all permissions to}
                            {--no-role    : Skip role sync}
                            {--details    : Print every permission being processed}';

    protected $description = 'Sync permissions_group and permissions tables from PermissionSyncService definition';

    public function handle(PermissionSyncService $service): int
    {
        $verbose = (bool) $this->option('details');

        $this->info('Syncing permissions…');
        $stats = $service->sync($verbose);

        $this->info("Groups created   : {$stats['groups']}");
        $this->info("Permissions added: {$stats['permissions']}");
        $this->info("Already existed  : {$stats['skipped']}");

        if (!$this->option('no-role')) {
            $role = $this->option('role');
            $synced = $service->syncRolePermissions($role);
            if ($synced) {
                $this->info("Role [{$role}] synced with all permissions.");
            } else {
                $this->warn("Role [{$role}] not found — skipping.");
            }
        }

        return 0;
    }
}
