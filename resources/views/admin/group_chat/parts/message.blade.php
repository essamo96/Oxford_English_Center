{{--
    A single chat bubble in the admin monitor.

    Props:
      $message — row from Message::getGroupMessages() (carries name/image/gender)
      $meId    — id of the logged-in admin, so their own comments render "out"
--}}
@php
    $type = (int) ($message->user_type ?? 0);   // 0 student, 1 teacher, 2 admin
    $isAdmin   = $type === 2;
    $isTeacher = $type === 1;
    // "Mine" = this admin's own comment. Another admin's comment still renders
    // incoming, so a monitor with several admins reads as a real conversation.
    $isMine = $isAdmin && (int) $message->from_user === (int) $meId;

    // The three sender tables store `image` in three incompatible shapes, so the
    // path must go through the resolver rather than asset() directly.
    $avatar = \App\Support\ChatAvatar::url($message->image ?? null, $type);

    $roleLabel = $isAdmin ? 'الإدارة' : ($isTeacher ? 'المعلم' : 'طالب');
    $roleClass = $isAdmin ? 'badge-light-danger' : ($isTeacher ? 'badge-light-warning' : 'badge-light-info');
    $ringClass = $isAdmin ? 'gc-ring-admin' : ($isTeacher ? 'gc-ring-teacher' : 'gc-ring-student');
    $bubbleBg  = $isMine ? 'bg-light-primary' : ($isAdmin ? 'bg-light-danger' : ($isTeacher ? 'bg-light-warning' : 'bg-light-info'));

    $created = $message->created_at instanceof \Carbon\Carbon
        ? $message->created_at
        : \Carbon\Carbon::parse($message->created_at);
@endphp

<div class="d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }} mb-8 gc-msg"
     data-message-id="{{ $message->id }}">
    <div class="d-flex flex-column {{ $isMine ? 'align-items-end' : 'align-items-start' }} mw-lg-500px">

        {{-- sender line --}}
        <div class="d-flex align-items-center mb-2">
            @if(!$isMine)
                {{-- A student's avatar is a moderation handle: clicking it opens the
                     mute/ban menu. Only students can be restricted, so teacher and
                     admin avatars stay inert. --}}
                <div class="symbol symbol-35px symbol-circle {{ $ringClass }}"
                     @if($type === 0 && auth('admin')->user()?->can('admin.group_chat.moderate'))
                         data-moderate-student="{{ $message->from_user }}"
                         title="إدارة الطالب: {{ $message->name }}"
                     @endif>
                    <img alt="{{ $message->name }}" src="{{ $avatar }}"
                         onerror="this.onerror=null;this.src='{{ \App\Support\ChatAvatar::default() }}';" />
                </div>
                <div class="ms-3 d-flex align-items-center flex-wrap gap-2">
                    <span class="fs-6 fw-bold text-gray-900">{{ $message->name ?? 'مستخدم محذوف' }}</span>
                    <span class="badge {{ $roleClass }} fs-8">{{ $roleLabel }}</span>
                    <span class="text-muted fs-7">{{ $created->diffForHumans() }}</span>
                </div>
            @else
                <div class="me-3 d-flex align-items-center flex-wrap gap-2">
                    <span class="text-muted fs-7">{{ $created->diffForHumans() }}</span>
                    <span class="badge badge-light-danger fs-8">الإدارة</span>
                    <span class="fs-6 fw-bold text-gray-900">{{ $message->name ?? 'أنا' }}</span>
                </div>
                <div class="symbol symbol-35px symbol-circle gc-ring-admin">
                    <img alt="{{ $message->name }}" src="{{ $avatar }}"
                         onerror="this.onerror=null;this.src='{{ \App\Support\ChatAvatar::default() }}';" />
                </div>
            @endif
        </div>

        {{-- bubble --}}
        <div class="p-5 rounded {{ $bubbleBg }} text-gray-900 fw-semibold {{ $isMine ? 'text-end' : 'text-start' }} position-relative gc-bubble"
             data-kt-element="message-text">

            @if(!empty($message->content))
                <div class="gc-text">{!! nl2br(e($message->content)) !!}</div>
            @endif

            @if(!empty($message->attachment))
                @php $url = asset($message->attachment); @endphp
                <div class="{{ !empty($message->content) ? 'mt-3' : '' }}">
                    @if($message->attachment_type === 'image')
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="d-block">
                            <img src="{{ $url }}" alt="{{ $message->attachment_name }}"
                                 class="rounded gc-img" />
                        </a>
                    @elseif($message->attachment_type === 'audio')
                        {{-- WhatsApp-style player: play/pause, seekable waveform, elapsed time.
                             Enhanced by voice-player.js; the <audio> stays as the fallback. --}}
                        <div class="gc-voice" data-voice-player>
                            <button type="button" class="gc-voice__btn" data-voice-toggle aria-label="تشغيل">
                                <i class="bi bi-play-fill" data-voice-icon></i>
                            </button>
                            <div class="gc-voice__body">
                                <div class="gc-voice__wave" data-voice-seek>
                                    <div class="gc-voice__progress" data-voice-progress></div>
                                    @for($i = 0; $i < 28; $i++)
                                        <span class="gc-voice__bar" style="height: {{ 25 + (($message->id * ($i + 3)) % 70) }}%"></span>
                                    @endfor
                                </div>
                                <div class="gc-voice__meta">
                                    <span data-voice-time>0:00</span>
                                    <i class="bi bi-mic-fill gc-voice__mic"></i>
                                </div>
                            </div>
                            <audio preload="metadata" src="{{ $url }}" data-voice-audio class="d-none"></audio>
                        </div>
                    @else
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="gc-file">
                            <span class="gc-file__icon"><i class="bi bi-file-earmark-arrow-down"></i></span>
                            <span class="gc-file__name">{{ \Illuminate\Support\Str::limit($message->attachment_name ?: 'مرفق', 38) }}</span>
                        </a>
                    @endif
                </div>
            @endif

            <span class="text-muted fs-8 d-block mt-2">{{ $created->format('Y-m-d H:i') }}</span>

            <button type="button" class="btn btn-icon btn-sm btn-active-light-danger gc-delete position-absolute top-0 {{ $isMine ? 'start-0' : 'end-0' }} m-1"
                    data-id="{{ $message->id }}" title="حذف الرسالة">
                <i class="bi bi-trash3 fs-7"></i>
            </button>
        </div>
    </div>
</div>
