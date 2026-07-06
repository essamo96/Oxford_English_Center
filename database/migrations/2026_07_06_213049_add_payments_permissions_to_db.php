<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PermissionsGroup;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Get the parent combo_requests
        $parent = PermissionsGroup::where('name', 'combo_requests')->first();

        if ($parent) {
            // 2. Create the new child group
            $newGroup = PermissionsGroup::firstOrCreate(
                ['name' => 'standalone_registrations.payments'],
                ['name_ar' => 'متابعة الدفعات (Oxford)', 'parent_id' => $parent->id, 'status' => 1]
            );

            // 3. Find or Create the permission
            $perm = Permission::firstOrCreate(
                ['name' => 'admin.standalone_registrations.payments.view', 'guard_name' => 'admin'],
                ['name_ar' => 'عرض الدفعات', 'group_id' => $newGroup->id]
            );
            
            $perm->group_id = $newGroup->id;
            $perm->save();

            $role = Role::findById(1, 'admin');
            if ($role) {
                $role->givePermissionTo($perm);
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Cache::forget('spatie.permission.cache');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('db', function (Blueprint $table) {
            //
        });
    }
};
