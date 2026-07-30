@php
    // \Auth::user()->id relies on the DEFAULT guard (students) — on a teacher's session that
    // guard's user() is null, which would fatal-error here ("member function on null").
    // Resolve the id from whichever guard is actually authenticated instead.
    $currentId = \Auth::guard('students')->check()
        ? \Auth::guard('students')->id()
        : (\Auth::guard('teachers')->check() ? \Auth::guard('teachers')->id() : null);
    $type = (int) ($message->user_type ?? 0);   // 0 = student, 1 = teacher, 2 = admin
    $isTeacherMsg = $type === 1;
    $isAdminMsg   = $type === 2;
    // An admin's id comes from `users` and can collide with a student/teacher id, so a
    // message is only "mine" when it also came from my own role.
    $isMine = !$isAdminMsg && $message->from_user == $currentId;
    // The three sender tables store `image` in three incompatible shapes (bare
    // filename for admins, absolute URL for teachers, relative path for students),
    // so resolving it needs the shared helper, not a bare asset() call.
    $avatarUrl = \App\Support\ChatAvatar::url($message->image ?? null, $type);
    // gender: 1 = male, 2 (or legacy 0) = female — only ever set for students (teachers and
    // admins have no gender column), so their messages simply show no emoji.
    $genderEmoji = ((int) ($message->gender ?? 0) === 1) ? '🦁' : (in_array((int) ($message->gender ?? -1), [2, 0], true) ? '🦋' : '');

    // The group's own teacher may moderate its students, so their avatars become
    // moderation handles. Students never see the handle on anyone.
    // The owning teacher is a property of the group, not the message, so it is
    // resolved once per group per request — this partial renders once per message.
    $canModerate = false;
    if ($type === 0 && \Auth::guard('teachers')->check()) {
        $ownerId = \Illuminate\Support\Facades\Cache::store('array')->rememberForever(
            'chat_group_teacher_' . $message->group_id,
            fn() => (int) (\App\Models\Groups::withoutGlobalScopes()
                ->where('id', $message->group_id)->value('teacher_id') ?? 0)
        );
        $canModerate = $ownerId === (int) \Auth::guard('teachers')->id();
    }
@endphp
<div class="ox-msg msg_container {{ $isMine ? 'ox-msg--mine base_sent' : 'ox-msg--theirs base_receive' }} {{ $isTeacherMsg ? 'ox-msg--teacher' : '' }} {{ $isAdminMsg ? 'ox-msg--admin' : '' }}" data-message-id="{{ $message->id }}">
    <div class="ox-msg__avatar-wrap"
         @if($canModerate)
             data-moderate-student="{{ $message->from_user }}"
             title="إدارة الطالب: {{ $message->name }}"
         @endif>
        <img class="ox-msg__avatar" src="{{ $avatarUrl }}" alt="{{ $message->name }}"
             onerror="this.onerror=null;this.src='{{ \App\Support\ChatAvatar::default() }}';">
        @if($isTeacherMsg)<span class="ox-msg__crown" title="المعلم">👑</span>@endif
        @if($isAdminMsg)<span class="ox-msg__crown ox-msg__shield" title="الإدارة">🛡️</span>@endif
    </div>
    <div class="ox-msg__col">
        <div class="ox-msg__meta">
            <span class="ox-msg__name">{{ $message->name }}</span>
            @if($isTeacherMsg)<span class="ox-msg__role">المعلم</span>@endif
            @if($isAdminMsg)<span class="ox-msg__role ox-msg__role--admin">الإدارة</span>@endif
            @if($genderEmoji)<span class="ox-msg__gender">{{ $genderEmoji }}</span>@endif
        </div>
        <div class="ox-msg__bubble">
            @if(!empty($message->content))
                <span class="ox-msg__text">{!! nl2br(e($message->content)) !!}</span>
            @endif
            @if(!empty($message->attachment))
                @php $attUrl = asset($message->attachment); @endphp
                <span class="ox-msg__attachment">
                    @if(($message->attachment_type ?? '') === 'image')
                        <a href="{{ $attUrl }}" target="_blank" rel="noopener">
                            <img class="ox-msg__image" src="{{ $attUrl }}" alt="{{ $message->attachment_name }}">
                        </a>
                    @elseif(($message->attachment_type ?? '') === 'audio')
                        {{-- Same WhatsApp-style player the admin monitor uses, so a voice
                             note looks identical on both sides. voice-player.js enhances it;
                             the <audio> element remains the no-JS fallback. --}}
                        <span class="ox-voice" data-voice-player>
                            <button type="button" class="ox-voice__btn" data-voice-toggle aria-label="تشغيل">
                                <i class="fa fa-play" data-voice-icon data-icon-play="fa-play" data-icon-pause="fa-pause"></i>
                            </button>
                            <span class="ox-voice__body">
                                <span class="ox-voice__wave" data-voice-seek>
                                    <span class="ox-voice__progress" data-voice-progress></span>
                                    @for($i = 0; $i < 24; $i++)
                                        <span class="ox-voice__bar" style="height: {{ 25 + (($message->id * ($i + 3)) % 70) }}%"></span>
                                    @endfor
                                </span>
                                <span class="ox-voice__meta"><span data-voice-time>0:00</span></span>
                            </span>
                            <audio preload="metadata" src="{{ $attUrl }}" data-voice-audio style="display:none"></audio>
                        </span>
                    @else
                        <a class="ox-msg__file" href="{{ $attUrl }}" target="_blank" rel="noopener">
                            <i class="fa fa-paperclip"></i>
                            {{ \Illuminate\Support\Str::limit($message->attachment_name ?: 'مرفق', 30) }}
                        </a>
                    @endif
                </span>
            @endif
            <time class="ox-msg__time" datetime="{{ date("Y-m-d\TH:i", strtotime($message->created_at->toDateTimeString())) }}">
                {{ $message->created_at->diffForHumans() }}
            </time>
        </div>
    </div>
</div>
