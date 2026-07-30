<?php

namespace Tests\Feature;

use App\Models\Groups;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Covers the admin group-chat monitor end to end: the sidebar entry resolves,
 * both screens render, and an admin comment lands in the shared `messages`
 * table with user_type = 2 so students and teachers receive it.
 */
class GroupChatMonitorTest extends TestCase
{
    /**
     * A group that has both a teacher and at least one enrolled student — the
     * first group with a teacher may have no members, which would skip every
     * teacher-moderation test and leave that path unverified.
     */
    private function teachableGroup(): ?Groups
    {
        $withMembers = \App\Models\GroupStudents::whereNull('deleted_at')
            ->distinct()->pluck('group_id');

        return Groups::withoutGlobalScopes()
            ->whereIn('id', $withMembers)
            ->whereNotNull('teacher_id')
            ->first();
    }

    private function admin(): ?User
    {
        return User::query()
            ->whereIn('id', function ($q) {
                $q->select('model_id')->from('model_has_roles');
            })
            ->first() ?: User::first();
    }

    public function test_permissions_and_sidebar_entry_exist(): void
    {
        $this->assertNotNull(
            DB::table('permissions_group')->where('name', 'group_chat')->first(),
            'sidebar group `group_chat` is missing'
        );

        foreach (['admin.group_chat.view', 'admin.group_chat.send', 'admin.group_chat.delete'] as $perm) {
            $this->assertNotNull(
                DB::table('permissions')->where('name', $perm)->first(),
                "permission {$perm} is missing"
            );
        }

        // The sidebar component resolves `<name>.view` as the route name.
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('group_chat.view'));
    }

    public function test_monitor_index_renders(): void
    {
        $admin = $this->admin();
        if (!$admin) { $this->markTestSkipped('no admin user in this database'); }

        $response = $this->actingAs($admin, 'admin')->get('/admin/group-chat');
        $response->assertStatus(200);
        $response->assertSee('مراقبة محادثات المجموعات', false);
    }

    public function test_sidebar_shows_the_monitor_link(): void
    {
        $admin = $this->admin();
        if (!$admin) { $this->markTestSkipped('no admin user in this database'); }

        // Any admin page renders the DB-driven sidebar; the entry must appear there,
        // not just be reachable by typing the URL.
        $response = $this->actingAs($admin, 'admin')->get('/admin/group-chat');
        $response->assertStatus(200);
        $response->assertSee(route('group_chat.view'), false);
        $response->assertSee('مراقبة المحادثات', false);
    }

    public function test_group_chat_screen_renders_metronic_messenger(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('no admin user or group in this database'); }

        $response = $this->actingAs($admin, 'admin')->get('/admin/group-chat/' . $group->id);
        $response->assertStatus(200);
        $response->assertSee('kt_chat_messenger', false);   // Metronic messenger card
        $response->assertSee('symbol-group', false);        // member avatar strip
        $response->assertSee('gc_record', false);           // voice note button
        $response->assertSee('gc_attach', false);           // attachment button
    }

    public function test_admin_can_post_a_comment_into_a_group(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('no admin user or group in this database'); }

        $text = 'رسالة اختبار من الإدارة ' . uniqid();

        $response = $this->actingAs($admin, 'admin')
            ->post('/admin/group-chat/' . $group->id . '/send', ['message' => $text]);

        $response->assertStatus(200);
        $response->assertJsonPath('state', 1);

        $stored = Message::where('group_id', $group->id)->latest('id')->first();
        $this->assertSame($text, $stored->content);
        $this->assertSame(Message::TYPE_ADMIN, (int) $stored->user_type);
        $this->assertSame((int) $admin->id, (int) $stored->from_user);

        // The frontend renderer must resolve the admin's name from `users`.
        $withSender = (new Message())->getOneWithSender($stored->id);
        $this->assertSame($admin->name, $withSender->name);

        // The same row must come back through the student/teacher read path.
        $latest = (new Message())->getLastMessages($group->id, 'student');
        $this->assertTrue($latest->contains('id', $stored->id));

        $stored->forceDelete();
    }

    public function test_admin_can_post_a_voice_note(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('no admin user or group in this database'); }

        // MediaRecorder blobs arrive with no extension, so the controller has to fall
        // back to attachment_kind to classify them.
        $blob = \Illuminate\Http\UploadedFile::fake()->createWithContent('voice-note', 'fake-audio-bytes');

        $response = $this->actingAs($admin, 'admin')->post(
            '/admin/group-chat/' . $group->id . '/send',
            ['message' => '', 'attachment' => $blob, 'attachment_kind' => 'audio']
        );

        $response->assertStatus(200)->assertJsonPath('state', 1);

        $stored = Message::where('group_id', $group->id)->latest('id')->first();
        $this->assertSame('audio', $stored->attachment_type);
        $this->assertNotEmpty($stored->attachment);
        $this->assertFileExists(public_path($stored->attachment));

        @unlink(public_path($stored->attachment));
        $stored->forceDelete();
    }

    public function test_student_side_renders_an_admin_message(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('no admin user or group in this database'); }

        $message = new Message();
        $message->from_user = $admin->id;
        $message->user_type = Message::TYPE_ADMIN;
        $message->content   = 'تنبيه من الإدارة';
        $message->group_id  = $group->id;
        $message->save();

        // This is the exact view the student/teacher chat box renders.
        $html = view('frontend.chat.message-line', [
            'message' => (new Message())->getOneWithSender($message->id),
        ])->render();

        $this->assertStringContainsString('ox-msg--admin', $html);
        $this->assertStringContainsString('الإدارة', $html);
        $this->assertStringContainsString($admin->name, $html);
        // An admin id can collide with a student id — it must never render as "mine".
        $this->assertStringNotContainsString('ox-msg--mine', $html);

        $message->forceDelete();
    }

    public function test_avatar_resolver_handles_every_column_shape(): void
    {
        $default = \App\Support\ChatAvatar::default();

        // Admin: bare filename living in public/assets/media/avatars.
        $adminFile = collect(glob(public_path('assets/media/avatars/*.jpg')))->first();
        if ($adminFile) {
            $name = basename($adminFile);
            $this->assertStringContainsString('assets/media/avatars/' . $name,
                \App\Support\ChatAvatar::url($name, 2));
        }

        // Teacher: absolute URL — the path survives, re-pointed at the current host.
        $this->assertStringContainsString('/uploads/image/x.png',
            \App\Support\ChatAvatar::url('http://some-old-host:8000/uploads/image/x.png', 1));

        // Nothing usable falls back to the shared placeholder, never a broken src.
        $this->assertSame($default, \App\Support\ChatAvatar::url(null, 0));
        $this->assertSame($default, \App\Support\ChatAvatar::url('', 0));
        $this->assertSame($default, \App\Support\ChatAvatar::url('does-not-exist.jpg', 2));
    }

    public function test_admin_avatar_is_included_in_the_live_payload(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('no admin user or group in this database'); }

        $response = $this->actingAs($admin, 'admin')
            ->post('/admin/group-chat/' . $group->id . '/send', ['message' => 'صورة الأدمن']);

        $response->assertStatus(200);

        // Students and teachers render live messages straight from this payload, so a
        // missing/unresolved avatar_url is exactly the broken-avatar bug.
        $avatar = $response->json('data.avatar_url');
        $this->assertNotEmpty($avatar);
        $this->assertStringStartsWith('http', $avatar);

        Message::where('group_id', $group->id)->latest('id')->first()->forceDelete();
    }

    public function test_unread_counts_track_per_admin_and_clear_on_open(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('no admin user or group in this database'); }

        // A student message in the group is unread until the admin opens it.
        $msg = new Message();
        $msg->from_user = 999999;
        $msg->user_type = Message::TYPE_STUDENT;
        $msg->content   = 'رسالة طالب';
        $msg->group_id  = $group->id;
        $msg->save();

        $before = \App\Models\GroupChatRead::unreadCountsFor($admin->id);
        $this->assertGreaterThan(0, $before[$group->id] ?? 0, 'student message should count as unread');

        // Opening the conversation clears it.
        $this->actingAs($admin, 'admin')->get('/admin/group-chat/' . $group->id)->assertStatus(200);

        $after = \App\Models\GroupChatRead::unreadCountsFor($admin->id);
        $this->assertSame(0, $after[$group->id] ?? 0, 'opening the group should clear its unread badge');

        $msg->forceDelete();
    }

    public function test_index_shows_the_unread_badge(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('no admin user or group in this database'); }

        // Read everything first, then let one new student message arrive.
        $this->actingAs($admin, 'admin')->get('/admin/group-chat/' . $group->id);

        $msg = new Message();
        $msg->from_user = 999999;
        $msg->user_type = Message::TYPE_STUDENT;
        $msg->content   = 'رسالة جديدة';
        $msg->group_id  = $group->id;
        $msg->save();

        $response = $this->actingAs($admin, 'admin')->get('/admin/group-chat');
        $response->assertStatus(200);
        $response->assertSee('data-unread="' . $group->id . '"', false);
        $response->assertSee('رسائل غير مقروءة', false);

        $msg->forceDelete();
    }

    public function test_admin_own_messages_never_count_as_unread(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('no admin user or group in this database'); }

        $this->actingAs($admin, 'admin')->get('/admin/group-chat/' . $group->id);

        $msg = new Message();
        $msg->from_user = $admin->id;
        $msg->user_type = Message::TYPE_ADMIN;
        $msg->content   = 'تعليق الإدارة';
        $msg->group_id  = $group->id;
        $msg->save();

        $counts = \App\Models\GroupChatRead::unreadCountsFor($admin->id);
        $this->assertSame(0, $counts[$group->id] ?? 0, "an admin's own comment must not mark their own badge");

        $msg->forceDelete();
    }

    public function test_voice_note_renders_a_player_on_both_sides(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('no admin user or group in this database'); }

        $msg = new Message();
        $msg->from_user       = $admin->id;
        $msg->user_type       = Message::TYPE_ADMIN;
        $msg->content         = '';
        $msg->group_id        = $group->id;
        $msg->attachment      = 'uploads/chat/test-note.webm';
        $msg->attachment_name = 'voice-note.webm';
        $msg->attachment_type = 'audio';
        $msg->save();

        $withSender = (new Message())->getOneWithSender($msg->id);

        // Admin monitor bubble.
        $adminHtml = view('admin.group_chat.parts.message', ['message' => $withSender, 'meId' => $admin->id])->render();
        $this->assertStringContainsString('data-voice-player', $adminHtml);
        $this->assertStringContainsString('data-voice-toggle', $adminHtml);
        $this->assertStringContainsString('bi-play-fill', $adminHtml);
        $this->assertStringNotContainsString('ki-microphone', $adminHtml, 'keenicons has no microphone glyph in this build');

        // Student/teacher bubble.
        $frontHtml = view('frontend.chat.message-line', ['message' => $withSender])->render();
        $this->assertStringContainsString('data-voice-player', $frontHtml);
        $this->assertStringContainsString('fa-play', $frontHtml);

        $msg->forceDelete();
    }

    public function test_chat_screen_uses_a_real_microphone_icon_and_alert_sound(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('no admin user or group in this database'); }

        $response = $this->actingAs($admin, 'admin')->get('/admin/group-chat/' . $group->id);
        $response->assertStatus(200);

        // ki-microphone-* does not exist in this Metronic build and renders blank.
        $response->assertDontSee('ki-microphone', false);
        $response->assertSee('bi-mic-fill', false);

        // Same alert tone the students/teachers hear.
        $response->assertSee('chat-alert-sound', false);
        $response->assertSee('facebook_chat.mp3', false);

        // Recording composer + voice player asset.
        $response->assertSee('gc_recording_bar', false);
        $response->assertSee('voice-player.js', false);
    }

    public function test_admin_can_clear_a_whole_conversation(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('no admin user or group in this database'); }

        // Attachment on disk must go too, or clearing orphans the file forever.
        $dir = public_path('uploads/chat');
        if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
        $file = 'uploads/chat/clear-test-' . uniqid() . '.txt';
        file_put_contents(public_path($file), 'x');

        $msg = new Message();
        $msg->from_user       = $admin->id;
        $msg->user_type       = Message::TYPE_ADMIN;
        $msg->content         = 'سيتم حذفها';
        $msg->group_id        = $group->id;
        $msg->attachment      = $file;
        $msg->attachment_type = 'file';
        $msg->save();

        $this->actingAs($admin, 'admin')
            ->post('/admin/group-chat/' . $group->id . '/clear')
            ->assertStatus(200)
            ->assertJsonPath('state', 1);

        $this->assertSame(0, Message::where('group_id', $group->id)->count());
        $this->assertFileDoesNotExist(public_path($file));

        // Read marks pointing at deleted ids would leave the group unread forever.
        $counts = \App\Models\GroupChatRead::unreadCountsFor($admin->id);
        $this->assertSame(0, $counts[$group->id] ?? 0);
    }

    public function test_locking_a_group_blocks_student_sends_but_not_reads(): void
    {
        $admin   = $this->admin();
        $group   = Groups::withoutGlobalScopes()->first();
        $student = \App\Models\Students::first();
        if (!$admin || !$group || !$student) { $this->markTestSkipped('missing fixtures'); }

        $this->actingAs($admin, 'admin')
            ->post('/admin/group-chat/' . $group->id . '/toggle-lock')
            ->assertStatus(200)
            ->assertJsonPath('locked', true);

        $this->assertSame(1, (int) Groups::withoutGlobalScopes()->find($group->id)->chat_locked);

        // Sending is refused server-side, not merely hidden in the UI.
        $this->actingAs($student, 'students')
            ->post('/send_student', ['to_user' => $group->id, 'message' => 'محاولة إرسال'])
            ->assertStatus(403)
            ->assertJsonPath('blocked', 'locked');

        // Reading still works, and reports the composer should be disabled.
        $this->actingAs($student, 'students')
            ->get('/load-latest-messages_student?group_id=' . $group->id)
            ->assertStatus(200)
            ->assertJsonPath('state', 1)
            ->assertJsonPath('can_send', false);

        // Unlock restores posting.
        $this->actingAs($admin, 'admin')
            ->post('/admin/group-chat/' . $group->id . '/toggle-lock')
            ->assertJsonPath('locked', false);

        $this->actingAs($student, 'students')
            ->get('/load-latest-messages_student?group_id=' . $group->id)
            ->assertJsonPath('can_send', true);

        Message::where('group_id', $group->id)->where('from_user', $student->id)->forceDelete();
    }

    public function test_restriction_without_a_type_defaults_to_the_milder_mute(): void
    {
        $admin   = $this->admin();
        $group   = Groups::withoutGlobalScopes()->first();
        $student = \App\Models\Students::first();
        if (!$admin || !$group || !$student) { $this->markTestSkipped('missing fixtures'); }

        // No `type` in the request: the default must be the less severe option,
        // so an omitted field can never silently cut a student off entirely.
        $this->actingAs($admin, 'admin')->post('/admin/group-chat/' . $group->id . '/ban', [
            'student_id' => $student->id,
            'reason'     => 'مخالفة قواعد المجموعة',
        ])->assertStatus(200)->assertJsonPath('state', 1)->assertJsonPath('type', 'mute');

        $this->assertTrue(\App\Models\GroupChatBan::isBanned($student->id, $group->id));
        $this->assertFalse(\App\Models\GroupChatBan::isBlockedFromViewing($student->id, $group->id));

        $send = $this->actingAs($student, 'students')
            ->post('/send_student', ['to_user' => $group->id, 'message' => 'محاولة']);
        $send->assertStatus(403)->assertJsonPath('blocked', 'muted');
        // The stated reason reaches the student.
        $this->assertStringContainsString('مخالفة قواعد المجموعة', $send->json('message'));

        // Reading is untouched.
        $this->actingAs($student, 'students')
            ->get('/load-latest-messages_student?group_id=' . $group->id)
            ->assertStatus(200)
            ->assertJsonPath('can_send', false);

        // Unban restores posting and keeps the row as history.
        $this->actingAs($admin, 'admin')->post('/admin/group-chat/' . $group->id . '/unban', [
            'student_id' => $student->id,
        ])->assertStatus(200)->assertJsonPath('state', 1);

        $this->assertFalse(\App\Models\GroupChatBan::isBanned($student->id, $group->id));
        $this->assertNotNull(
            \App\Models\GroupChatBan::where('student_id', $student->id)->where('group_id', $group->id)->first(),
            'the ban row should survive an unban as moderation history'
        );

        \App\Models\GroupChatBan::where('student_id', $student->id)->where('group_id', $group->id)->delete();
    }

    public function test_a_silent_ban_has_no_stated_reason(): void
    {
        $admin   = $this->admin();
        $group   = Groups::withoutGlobalScopes()->first();
        $student = \App\Models\Students::first();
        if (!$admin || !$group || !$student) { $this->markTestSkipped('missing fixtures'); }

        $this->actingAs($admin, 'admin')->post('/admin/group-chat/' . $group->id . '/ban', [
            'student_id' => $student->id,
            'reason'     => '',
        ])->assertStatus(200);

        $ban = \App\Models\GroupChatBan::activeBan($student->id, $group->id);
        $this->assertNotNull($ban);
        $this->assertNull($ban->reason, 'an empty reason must be stored as NULL, not ""');

        $send = $this->actingAs($student, 'students')
            ->post('/send_student', ['to_user' => $group->id, 'message' => 'محاولة']);
        $send->assertStatus(403);
        $this->assertStringNotContainsString('السبب', $send->json('message'));

        \App\Models\GroupChatBan::where('student_id', $student->id)->where('group_id', $group->id)->delete();
    }

    public function test_ban_and_unban_notify_the_student_live(): void
    {
        $admin   = $this->admin();
        $group   = Groups::withoutGlobalScopes()->first();
        $student = \App\Models\Students::first();
        if (!$admin || !$group || !$student) { $this->markTestSkipped('missing fixtures'); }

        \Illuminate\Support\Facades\Event::fake([\App\Events\StudentNotificationBroadcast::class]);

        $this->actingAs($admin, 'admin')->post('/admin/group-chat/' . $group->id . '/ban', [
            'student_id' => $student->id, 'type' => 'ban', 'reason' => 'سبب الاختبار',
        ]);

        \Illuminate\Support\Facades\Event::assertDispatched(
            \App\Events\StudentNotificationBroadcast::class,
            function ($event) use ($student) {
                return $event->studentId === (int) $student->id
                    && $event->data['type'] === 'group_chat_banned'
                    && str_contains($event->data['message'], 'سبب الاختبار');
            }
        );

        $this->actingAs($admin, 'admin')->post('/admin/group-chat/' . $group->id . '/unban', [
            'student_id' => $student->id,
        ]);

        \Illuminate\Support\Facades\Event::assertDispatched(
            \App\Events\StudentNotificationBroadcast::class,
            fn($event) => $event->data['type'] === 'group_chat_unbanned'
        );

        \App\Models\GroupChatBan::where('student_id', $student->id)->where('group_id', $group->id)->delete();
    }

    public function test_search_finds_messages_and_escapes_the_term(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('missing fixtures'); }

        $needle = 'كلمة' . uniqid();

        $msg = new Message();
        $msg->from_user = $admin->id;
        $msg->user_type = Message::TYPE_ADMIN;
        $msg->content   = 'رسالة تحتوي على ' . $needle . ' بالداخل';
        $msg->group_id  = $group->id;
        $msg->save();

        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/group-chat/' . $group->id . '/search?q=' . urlencode($needle));

        $response->assertStatus(200)->assertJsonPath('state', 1);
        $this->assertSame(1, $response->json('total'));
        $this->assertStringContainsString('gc-hl', $response->json('messages.0'));

        // A term containing markup must not be able to inject into the results.
        $xss = $this->actingAs($admin, 'admin')
            ->get('/admin/group-chat/' . $group->id . '/search?q=' . urlencode('<script>'));
        $xss->assertStatus(200);
        foreach (($xss->json('messages') ?? []) as $html) {
            $this->assertStringNotContainsString('<script>', $html);
        }

        $msg->forceDelete();
    }

    public function test_search_matches_attachment_filenames(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('missing fixtures'); }

        $msg = new Message();
        $msg->from_user       = $admin->id;
        $msg->user_type       = Message::TYPE_ADMIN;
        $msg->content         = '';                    // sent with no caption
        $msg->group_id        = $group->id;
        $msg->attachment      = 'uploads/chat/x.pdf';
        $msg->attachment_name = 'التقرير-النهائي.pdf';
        $msg->attachment_type = 'file';
        $msg->save();

        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/group-chat/' . $group->id . '/search?q=' . urlencode('التقرير-النهائي'));

        $response->assertStatus(200);
        $this->assertSame(1, $response->json('total'), 'a caption-less attachment should be findable by filename');

        $msg->forceDelete();
    }

    public function test_attachments_panel_and_moderation_controls_render(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('missing fixtures'); }

        $response = $this->actingAs($admin, 'admin')->get('/admin/group-chat/' . $group->id);
        $response->assertStatus(200);

        $response->assertSee('gc_modal_media', false);   // attachments panel
        $response->assertSee('gc_search', false);        // in-group search
        $response->assertSee('gc_modal_bans', false);    // ban management
        $response->assertSee('gc_lock_toggle', false);   // freeze switch
        $response->assertSee('gc_clear_btn', false);     // clear-all
    }

    public function test_unread_endpoint_returns_live_counts(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('missing fixtures'); }

        $this->actingAs($admin, 'admin')->get('/admin/group-chat/' . $group->id);

        $msg = new Message();
        $msg->from_user = 999999;
        $msg->user_type = Message::TYPE_STUDENT;
        $msg->content   = 'رسالة لحظية';
        $msg->group_id  = $group->id;
        $msg->save();

        $response = $this->actingAs($admin, 'admin')->get('/admin/group-chat-unread');
        $response->assertStatus(200)->assertJsonPath('state', 1);
        $this->assertSame(1, $response->json('counts.' . $group->id));
        $this->assertGreaterThanOrEqual(1, $response->json('total'));

        $msg->forceDelete();
    }

    public function test_deleting_a_message_broadcasts_so_it_disappears_everywhere(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('missing fixtures'); }

        $msg = new Message();
        $msg->from_user = $admin->id;
        $msg->user_type = Message::TYPE_ADMIN;
        $msg->content   = 'ستُحذف لحظياً';
        $msg->group_id  = $group->id;
        $msg->save();

        $response = $this->actingAs($admin, 'admin')
            ->post('/admin/group-chat/message/delete', ['id' => $msg->id]);

        // The id and group must come back so every open client knows what to drop.
        $response->assertStatus(200)
            ->assertJsonPath('state', 1)
            ->assertJsonPath('id', $msg->id)
            ->assertJsonPath('group_id', $group->id);

        $this->assertNull(Message::find($msg->id));
    }

    public function test_mute_blocks_sending_but_keeps_reading(): void
    {
        $admin   = $this->admin();
        $group   = Groups::withoutGlobalScopes()->first();
        $student = \App\Models\Students::first();
        if (!$admin || !$group || !$student) { $this->markTestSkipped('missing fixtures'); }

        $this->actingAs($admin, 'admin')->post('/admin/group-chat/' . $group->id . '/ban', [
            'student_id' => $student->id,
            'type'       => 'mute',
            'reason'     => 'إزعاج',
        ])->assertStatus(200)->assertJsonPath('type', 'mute');

        $this->assertFalse(\App\Models\GroupChatBan::isBlockedFromViewing($student->id, $group->id));

        $this->actingAs($student, 'students')
            ->post('/send_student', ['to_user' => $group->id, 'message' => 'محاولة'])
            ->assertStatus(403)
            ->assertJsonPath('blocked', 'muted')
            ->assertJsonPath('can_view', true);

        // A mute leaves the history readable.
        $this->actingAs($student, 'students')
            ->get('/load-latest-messages_student?group_id=' . $group->id)
            ->assertStatus(200)
            ->assertJsonPath('can_send', false)
            ->assertJsonPath('can_view', true);

        \App\Models\GroupChatBan::where('student_id', $student->id)->where('group_id', $group->id)->delete();
    }

    public function test_full_ban_also_withholds_the_conversation(): void
    {
        $admin   = $this->admin();
        $group   = Groups::withoutGlobalScopes()->first();
        $student = \App\Models\Students::first();
        if (!$admin || !$group || !$student) { $this->markTestSkipped('missing fixtures'); }

        // Something to read, so an empty result proves it was withheld.
        $seed = new Message();
        $seed->from_user = $admin->id;
        $seed->user_type = Message::TYPE_ADMIN;
        $seed->content   = 'محتوى سري';
        $seed->group_id  = $group->id;
        $seed->save();

        $this->actingAs($admin, 'admin')->post('/admin/group-chat/' . $group->id . '/ban', [
            'student_id' => $student->id,
            'type'       => 'ban',
        ])->assertStatus(200)->assertJsonPath('type', 'ban');

        $this->assertTrue(\App\Models\GroupChatBan::isBlockedFromViewing($student->id, $group->id));

        // The history must not be in the response at all — hiding it client-side
        // would still ship it to a banned student.
        $read = $this->actingAs($student, 'students')
            ->get('/load-latest-messages_student?group_id=' . $group->id);

        $read->assertStatus(200)
            ->assertJsonPath('can_view', false)
            ->assertJsonPath('can_send', false);
        $this->assertSame([], $read->json('messages'));
        $read->assertDontSee('محتوى سري', false);

        $this->actingAs($student, 'students')
            ->post('/send_student', ['to_user' => $group->id, 'message' => 'محاولة'])
            ->assertStatus(403)
            ->assertJsonPath('blocked', 'banned')
            ->assertJsonPath('can_view', false);

        $seed->forceDelete();
        \App\Models\GroupChatBan::where('student_id', $student->id)->where('group_id', $group->id)->delete();
    }

    public function test_mute_and_ban_notify_the_student_with_distinct_events(): void
    {
        $admin   = $this->admin();
        $group   = Groups::withoutGlobalScopes()->first();
        $student = \App\Models\Students::first();
        if (!$admin || !$group || !$student) { $this->markTestSkipped('missing fixtures'); }

        \Illuminate\Support\Facades\Event::fake([\App\Events\StudentNotificationBroadcast::class]);

        $this->actingAs($admin, 'admin')->post('/admin/group-chat/' . $group->id . '/ban', [
            'student_id' => $student->id, 'type' => 'mute',
        ]);
        \Illuminate\Support\Facades\Event::assertDispatched(
            \App\Events\StudentNotificationBroadcast::class,
            fn($e) => $e->data['type'] === 'group_chat_muted' && $e->data['can_view'] === true
        );

        $this->actingAs($admin, 'admin')->post('/admin/group-chat/' . $group->id . '/ban', [
            'student_id' => $student->id, 'type' => 'ban',
        ]);
        \Illuminate\Support\Facades\Event::assertDispatched(
            \App\Events\StudentNotificationBroadcast::class,
            fn($e) => $e->data['type'] === 'group_chat_banned' && $e->data['can_view'] === false
        );

        \App\Models\GroupChatBan::where('student_id', $student->id)->where('group_id', $group->id)->delete();
    }

    public function test_teacher_can_moderate_students_in_their_own_group(): void
    {
        $group = $this->teachableGroup();
        if (!$group) { $this->markTestSkipped('no group with a teacher and members'); }

        $teacher = \App\Models\Teachers::find($group->teacher_id);
        $member  = \App\Models\GroupStudents::where('group_id', $group->id)->whereNull('deleted_at')->first();
        if (!$teacher || !$member) { $this->markTestSkipped('no teacher or group member'); }

        $studentId = $member->student_id;

        // State lookup for the avatar menu.
        $this->actingAs($teacher, 'teachers')
            ->get('/teacher/chat/student-state?group_id=' . $group->id . '&student_id=' . $studentId)
            ->assertStatus(200)
            ->assertJsonPath('state', 1)
            ->assertJsonPath('restricted', false);

        // Mute.
        $this->actingAs($teacher, 'teachers')->post('/teacher/chat/restrict', [
            'group_id' => $group->id, 'student_id' => $studentId,
            'type' => 'mute', 'reason' => 'إزعاج في الحصة',
        ])->assertStatus(200)->assertJsonPath('type', 'mute');

        $this->assertTrue(\App\Models\GroupChatBan::isBanned($studentId, $group->id));

        // A teacher-applied restriction is recorded as such.
        $ban = \App\Models\GroupChatBan::activeBan($studentId, $group->id);
        $this->assertSame('teacher', $ban->restricted_by_type);
        $this->assertSame((int) $teacher->id, (int) $ban->banned_by);

        // Lift.
        $this->actingAs($teacher, 'teachers')->post('/teacher/chat/lift', [
            'group_id' => $group->id, 'student_id' => $studentId,
        ])->assertStatus(200)->assertJsonPath('state', 1);

        $this->assertFalse(\App\Models\GroupChatBan::isBanned($studentId, $group->id));

        \App\Models\GroupChatBan::where('student_id', $studentId)->where('group_id', $group->id)->delete();
    }

    public function test_teacher_cannot_moderate_a_group_they_do_not_teach(): void
    {
        $group = $this->teachableGroup();
        if (!$group) { $this->markTestSkipped('no group with a teacher and members'); }

        // Any teacher who is NOT this group's teacher.
        $outsider = \App\Models\Teachers::where('id', '!=', $group->teacher_id)->first();
        $student  = \App\Models\Students::first();
        if (!$outsider || !$student) { $this->markTestSkipped('no second teacher'); }

        // The group id comes from the browser, so ownership must be re-checked.
        $this->actingAs($outsider, 'teachers')->post('/teacher/chat/restrict', [
            'group_id' => $group->id, 'student_id' => $student->id, 'type' => 'ban',
        ])->assertStatus(403);

        $this->actingAs($outsider, 'teachers')
            ->get('/teacher/chat/student-state?group_id=' . $group->id . '&student_id=' . $student->id)
            ->assertStatus(403);

        $this->assertFalse(\App\Models\GroupChatBan::isBanned($student->id, $group->id));
    }

    public function test_teacher_cannot_restrict_a_non_member(): void
    {
        $group = $this->teachableGroup();
        if (!$group) { $this->markTestSkipped('no group with a teacher and members'); }

        $teacher = \App\Models\Teachers::find($group->teacher_id);
        if (!$teacher) { $this->markTestSkipped('teacher missing'); }

        $memberIds = \App\Models\GroupStudents::where('group_id', $group->id)->pluck('student_id');
        $outsider  = \App\Models\Students::whereNotIn('id', $memberIds)->first();
        if (!$outsider) { $this->markTestSkipped('no student outside the group'); }

        $this->actingAs($teacher, 'teachers')->post('/teacher/chat/restrict', [
            'group_id' => $group->id, 'student_id' => $outsider->id, 'type' => 'mute',
        ])->assertStatus(422);

        $this->assertFalse(\App\Models\GroupChatBan::isBanned($outsider->id, $group->id));
    }

    public function test_avatar_moderation_handle_renders_for_admin_and_teacher(): void
    {
        $admin = $this->admin();
        $group = $this->teachableGroup();
        if (!$admin || !$group) { $this->markTestSkipped('missing fixtures'); }

        $member = \App\Models\GroupStudents::where('group_id', $group->id)->whereNull('deleted_at')->first();
        if (!$member) { $this->markTestSkipped('no group member'); }

        $msg = new Message();
        $msg->from_user = $member->student_id;
        $msg->user_type = Message::TYPE_STUDENT;
        $msg->content   = 'رسالة طالب';
        $msg->group_id  = $group->id;
        $msg->save();

        $withSender = (new Message())->getOneWithSender($msg->id);

        // Admin monitor: the student's avatar is a moderation handle.
        $this->actingAs($admin, 'admin');
        $adminHtml = view('admin.group_chat.parts.message', ['message' => $withSender, 'meId' => $admin->id])->render();
        $this->assertStringContainsString('data-moderate-student="' . $member->student_id . '"', $adminHtml);

        // Teacher of this group: same handle in their chat box.
        $teacher = \App\Models\Teachers::find($group->teacher_id);
        if ($teacher) {
            \Illuminate\Support\Facades\Cache::store('array')->flush();
            $this->actingAs($teacher, 'teachers');
            $teacherHtml = view('frontend.chat.message-line', ['message' => $withSender])->render();
            $this->assertStringContainsString('data-moderate-student="' . $member->student_id . '"', $teacherHtml);
        }

        $msg->forceDelete();
    }

    public function test_students_never_see_the_moderation_handle(): void
    {
        $group = $this->teachableGroup();
        if (!$group) { $this->markTestSkipped('no group with a teacher and members'); }

        $member = \App\Models\GroupStudents::where('group_id', $group->id)->whereNull('deleted_at')->first();
        if (!$member) { $this->markTestSkipped('no group member'); }

        $msg = new Message();
        $msg->from_user = $member->student_id;
        $msg->user_type = Message::TYPE_STUDENT;
        $msg->content   = 'رسالة طالب';
        $msg->group_id  = $group->id;
        $msg->save();

        $withSender = (new Message())->getOneWithSender($msg->id);

        \Illuminate\Support\Facades\Cache::store('array')->flush();
        $student = \App\Models\Students::find($member->student_id);
        if ($student) {
            $this->actingAs($student, 'students');
            $html = view('frontend.chat.message-line', ['message' => $withSender])->render();
            $this->assertStringNotContainsString('data-moderate-student', $html,
                'a student must never get a moderation handle on anyone');
        }

        $msg->forceDelete();
    }

    public function test_empty_message_is_rejected(): void
    {
        $admin = $this->admin();
        $group = Groups::withoutGlobalScopes()->first();
        if (!$admin || !$group) { $this->markTestSkipped('no admin user or group in this database'); }

        $this->actingAs($admin, 'admin')
            ->post('/admin/group-chat/' . $group->id . '/send', ['message' => '   '])
            ->assertStatus(422);
    }
}
