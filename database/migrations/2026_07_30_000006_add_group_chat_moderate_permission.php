<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds the moderation permission: clearing a conversation, freezing a group's
 * chat, and banning students. Kept separate from `admin.group_chat.send` so a
 * role can be allowed to comment without being allowed to wipe history.
 *
 * Granted to every role that can already delete chat messages — that is the
 * closest existing equivalent of "moderator".
 */
return new class extends Migration
{
    private const PERMISSION = 'admin.group_chat.moderate';

    public function up(): void
    {
        $groupId = DB::table('permissions_group')->where('name', 'group_chat')->value('id');
        if (!$groupId) {
            return;
        }

        if (!DB::table('permissions')->where('name', self::PERMISSION)->exists()) {
            DB::table('permissions')->insert([
                'name'       => self::PERMISSION,
                'group_id'   => $groupId,
                'name_ar'    => 'إدارة المحادثات (إيقاف/حذف/حظر)',
                'guard_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $newId    = DB::table('permissions')->where('name', self::PERMISSION)->value('id');
        $deleteId = DB::table('permissions')->where('name', 'admin.group_chat.delete')->value('id');

        if ($newId && $deleteId) {
            $roleIds = DB::table('role_has_permissions')
                ->where('permission_id', $deleteId)->distinct()->pluck('role_id');

            foreach ($roleIds as $roleId) {
                $exists = DB::table('role_has_permissions')
                    ->where('role_id', $roleId)->where('permission_id', $newId)->exists();
                if (!$exists) {
                    DB::table('role_has_permissions')->insert([
                        'role_id'       => $roleId,
                        'permission_id' => $newId,
                    ]);
                }
            }
        }

        app()['cache']->forget('spatie.permission.cache');
    }

    public function down(): void
    {
        $id = DB::table('permissions')->where('name', self::PERMISSION)->value('id');
        if ($id) {
            DB::table('role_has_permissions')->where('permission_id', $id)->delete();
            DB::table('permissions')->where('id', $id)->delete();
        }
        app()['cache']->forget('spatie.permission.cache');
    }
};
