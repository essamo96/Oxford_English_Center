<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RefactorPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:refactor {--dry-run : Run without making changes} {--rollback : Attempt to rollback based on logs (experimental)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refactor and standardize permissions naming convention to admin.{group}.{action}';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('--- DRY RUN MODE ---');
        }

        DB::beginTransaction();

        try {
            $this->info('Starting permissions refactor...');

            // 1. Fetch all permissions with their group names
            $permissions = DB::table('permissions')
                ->join('permissions_group', 'permissions.group_id', '=', 'permissions_group.id')
                ->select('permissions.*', 'permissions_group.name as group_name')
                ->get();

            $updatedCount = 0;
            $skippedCount = 0;
            $changes = [];

            foreach ($permissions as $permission) {
                $groupName = $permission->group_name;
                $currentName = $permission->name;

                // 2. Identify the action
                $action = $this->extractAction($currentName, $groupName);

                // 3. Construct new name: admin.{group}.{action}
                $newName = "admin.{$groupName}.{$action}";

                if ($currentName === $newName) {
                    $skippedCount++;
                    continue;
                }

                $this->line("Renaming: <fg=yellow>{$currentName}</> -> <fg=green>{$newName}</>");

                if (!$dryRun) {
                    // Check for duplicate before updating
                    $exists = DB::table('permissions')
                        ->where('name', $newName)
                        ->where('guard_name', $permission->guard_name)
                        ->where('id', '!=', $permission->id)
                        ->exists();

                    if ($exists) {
                        $this->warn("Duplicate detected for '{$newName}'. Skipping ID {$permission->id}.");
                        continue;
                    }

                    DB::table('permissions')
                        ->where('id', $permission->id)
                        ->update(['name' => $newName]);
                    
                    $changes[] = [
                        'id' => $permission->id,
                        'old' => $currentName,
                        'new' => $newName
                    ];
                }

                $updatedCount++;
            }

            // 4. Admin Role Handling
            $this->info('Synchronizing Admin role permissions...');
            
            if (!$dryRun) {
                // Clear Spatie Cache
                if (app()->bound(\Spatie\Permission\PermissionRegistrar::class)) {
                    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                }
            }

            $adminRole = Role::where('name', 'Admin')
                ->orWhere('name', 'admin')
                ->orWhere('name', 'super_admin')
                ->first();

            if ($adminRole) {
                $this->info("Found Admin role: {$adminRole->name}");
                
                if (!$dryRun) {
                    $allPermissionNames = Permission::where('guard_name', $adminRole->guard_name)->pluck('name')->toArray();
                    $adminRole->syncPermissions($allPermissionNames);
                    $this->info('Admin role synchronized with all permissions.');
                }
            } else {
                $this->warn('Admin role not found. Skipping synchronization.');
            }

            if ($dryRun) {
                DB::rollBack();
                $this->info("Dry run completed. Would have updated {$updatedCount} permissions.");
            } else {
                DB::commit();
                $this->info("Refactor completed successfully. Updated {$updatedCount} permissions.");
                
                if (count($changes) > 0) {
                    Log::info('Permissions Refactor Logs', ['changes' => $changes]);
                    $this->info('Changes have been logged to storage/logs/laravel.log');
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Refactor failed: ' . $e->getMessage());
            Log::error('Permissions Refactor Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return 1;
        }

        return 0;
    }

    /**
     * Extract the action from the permission name.
     */
    private function extractAction($fullName, $groupName)
    {
        // 1. Remove 'admin.' if present
        $name = str_replace('admin.', '', $fullName);
        
        // 2. Try to remove the group name prefix (and variations like old names)
        // We look for the last part after any dots if it's a standard action
        $standardActions = ['view', 'add', 'edit', 'delete', 'status', 'create', 'update', 'destroy', 'show', 'reply', 'password', 'permissions'];
        
        if (strpos($name, '.') !== false) {
            $parts = explode('.', $name);
            $lastPart = strtolower(end($parts));
            
            // If the last part is a standard action, we return it
            if (in_array($lastPart, $standardActions)) {
                return $lastPart;
            }
            
            // If the first part is NOT the group name, it's likely a mis-prefixed permission
            // e.g. admin.old_group.action in new_group
            // We still want to extract the action
            if ($parts[0] !== $groupName) {
                return $lastPart;
            }
        }

        // Fallback: remove group name if it's at the start
        $pattern = '/^' . preg_quote($groupName, '/') . '[\.\-_]/';
        $action = preg_replace($pattern, '', $name);

        return strtolower($action);
    }
}
