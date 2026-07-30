<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Registers "مراقبة محادثات المجموعات" as a top-level sidebar entry.
 *
 * The sidebar is DB-driven: a permissions_group row named `group_chat` renders
 * via admin.components.sidebar-item-single, which resolves the route as
 * `group_chat.view` and gates it on the `admin.group_chat.view` permission.
 *
 * Every role that can already see groups gets the new permissions, so existing
 * admins do not have to be re-granted by hand.
 */
return new class extends Migration
{
    private const PERMISSIONS = [
        'admin.group_chat.view'   => 'مراقبة محادثات المجموعات',
        'admin.group_chat.send'   => 'التعليق داخل محادثات المجموعات',
        'admin.group_chat.delete' => 'حذف رسائل المجموعات',
    ];

    public function up(): void
    {
        $group = DB::table('permissions_group')->where('name', 'group_chat')->first();

        if (!$group) {
            $groupId = DB::table('permissions_group')->insertGetId([
                'name'       => 'group_chat',
                'name_ar'    => 'مراقبة المحادثات',
                'name_en'    => 'Group Chat Monitor',
                'icon'       => 'ki-duotone ki-messages',
                'color'      => '#50cd89',
                'sort'       => 65,
                'status'     => 1,
                'parent_id'  => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $groupId = $group->id;
        }

        foreach (self::PERMISSIONS as $name => $nameAr) {
            if (!DB::table('permissions')->where('name', $name)->exists()) {
                DB::table('permissions')->insert([
                    'name'       => $name,
                    'group_id'   => $groupId,
                    'name_ar'    => $nameAr,
                    'guard_name' => 'admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Grant to every role that already manages groups in any way. Anchoring on a
        // single permission is not safe here: the built-in "Admin" role holds
        // admin.groups.edit / .status but not admin.groups.view, so any *one* of the
        // admin.groups.* permissions has to count as "manages groups".
        $newIds = DB::table('permissions')->whereIn('name', array_keys(self::PERMISSIONS))->pluck('id');
        $groupPermIds = DB::table('permissions')
            ->where('name', 'LIKE', 'admin.groups.%')
            ->pluck('id');

        if ($groupPermIds->count() && $newIds->count()) {
            $roleIds = DB::table('role_has_permissions')
                ->whereIn('permission_id', $groupPermIds)
                ->distinct()
                ->pluck('role_id');

            foreach ($roleIds as $roleId) {
                foreach ($newIds as $permId) {
                    $exists = DB::table('role_has_permissions')
                        ->where('role_id', $roleId)->where('permission_id', $permId)->exists();
                    if (!$exists) {
                        DB::table('role_has_permissions')->insert([
                            'role_id'       => $roleId,
                            'permission_id' => $permId,
                        ]);
                    }
                }
            }
        }

        app()['cache']->forget('spatie.permission.cache');
    }

    public function down(): void
    {
        $group = DB::table('permissions_group')->where('name', 'group_chat')->first();
        if ($group) {
            $permIds = DB::table('permissions')->whereIn('name', array_keys(self::PERMISSIONS))->pluck('id');
            DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
            DB::table('permissions')->whereIn('id', $permIds)->delete();
            DB::table('permissions_group')->where('id', $group->id)->delete();
        }
        app()['cache']->forget('spatie.permission.cache');
    }
};
