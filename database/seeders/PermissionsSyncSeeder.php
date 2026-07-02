<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class PermissionsSyncSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groupsJson = file_get_contents(database_path('seeders/data/groups_dump.json'));
        $permsJson = file_get_contents(database_path('seeders/data/perms_dump.json'));

        $groups = json_decode($groupsJson, true);
        $perms = json_decode($permsJson, true);

        // 1. Sync Groups
        foreach ($groups as $group) {
            DB::table('permissions_group')->updateOrInsert(
                ['id' => $group['id']],
                [
                    'name' => $group['name'],
                    'name_ar' => $group['name_ar'],
                    'name_en' => $group['name_en'] ?? null,
                    'icon' => $group['icon'] ?? null,
                    'color' => $group['color'] ?? null,
                    'sort' => $group['sort'] ?? null,
                    'status' => $group['status'] ?? 1,
                    'parent_id' => $group['parent_id'] ?? 0,
                    'created_at' => $group['created_at'] ?? now(),
                    'updated_at' => $group['updated_at'] ?? now(),
                ]
            );
        }

        // 2. Sync Permissions
        foreach ($perms as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['id' => $perm['id']],
                [
                    'name' => $perm['name'],
                    'name_ar' => $perm['name_ar'] ?? null,
                    'guard_name' => $perm['guard_name'] ?? 'admin',
                    'group_id' => $perm['group_id'] ?? null,
                    'created_at' => $perm['created_at'] ?? now(),
                    'updated_at' => $perm['updated_at'] ?? now(),
                ]
            );
        }

        // 3. Sync all permissions to Role 1 (Admin)
        $allPermIds = collect($perms)->pluck('id')->toArray();
        $existingRolePerms = DB::table('role_has_permissions')->where('role_id', 1)->pluck('permission_id')->toArray();
        
        $toInsert = array_diff($allPermIds, $existingRolePerms);
        $insertData = [];
        foreach ($toInsert as $pid) {
            $insertData[] = [
                'permission_id' => $pid,
                'role_id' => 1
            ];
        }

        if (!empty($insertData)) {
            foreach (array_chunk($insertData, 500) as $chunk) {
                DB::table('role_has_permissions')->insert($chunk);
            }
        }

        // 4. Reset Cache
        Artisan::call('permission:cache-reset');
        
        $this->command->info('Permissions and Groups successfully synced with local dump!');
    }
}
