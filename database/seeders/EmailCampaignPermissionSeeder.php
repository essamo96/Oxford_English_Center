<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class EmailCampaignPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Define required permissions
        $permissions = [
            'view_email_campaigns',
            'send_emails',
            'manage_email_campaigns'
        ];

        // 2. Create permissions if they don't exist (Safe Mode)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        // 3. Find or Create roles
        $rolesToSync = ['super_admin', 'admin', 'email_manager'];
        
        foreach ($rolesToSync as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'admin']);
            
            // Safe sync without detaching previous permissions
            foreach ($permissions as $permission) {
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }

        // 4. Ensure current Users (Admins) have these permissions if needed
        // $admins = User::all();
        // For safety, we can just ensure the Roles have them as Spatie handles the cascade.
        
        echo "Permissions synced successfully without data loss.\n";
    }
}
