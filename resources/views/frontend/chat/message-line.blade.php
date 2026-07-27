@php
    // \Auth::user()->id relies on the DEFAULT guard (students) — on a teacher's session that
    // guard's user() is null, which would fatal-error here ("member function on null").
    // Resolve the id from whichever guard is actually authenticated instead.
    $currentId = \Auth::guard('students')->check()
        ? \Auth::guard('students')->id()
        : (\Auth::guard('teachers')->check() ? \Auth::guard('teachers')->id() : null);
    $isMine = $message->from_user == $currentId;
    $isTeacherMsg = ((int) ($message->user_type ?? 0) === 1);
    $avatarUrl = ($message->image ?? null) && file_exists(public_path($message->image))
        ? asset($message->image)
        : asset('assets/oxford/images/user-avatar.png');
    // gender: 1 = male, 2 (or legacy 0) = female — only ever set for students (teachers have no
    // gender column), so a teacher's message simply shows no emoji.
    $genderEmoji = ((int) ($message->gender ?? 0) === 1) ? '🦁' : (in_array((int) ($message->gender ?? -1), [2, 0], true) ? '🦋' : '');
@endphp
<div class="ox-msg msg_container {{ $isMine ? 'ox-msg--mine base_sent' : 'ox-msg--theirs base_receive' }} {{ $isTeacherMsg ? 'ox-msg--teacher' : '' }}" data-message-id="{{ $message->id }}">
    <div class="ox-msg__avatar-wrap">
        <img class="ox-msg__avatar" src="{{ $avatarUrl }}" alt="{{ $message->name }}">
        @if($isTeacherMsg)<span class="ox-msg__crown" title="المعلم">👑</span>@endif
    </div>
    <div class="ox-msg__col">
        <div class="ox-msg__meta">
            <span class="ox-msg__name">{{ $message->name }}</span>
            @if($isTeacherMsg)<span class="ox-msg__role">المعلم</span>@endif
            @if($genderEmoji)<span class="ox-msg__gender">{{ $genderEmoji }}</span>@endif
        </div>
        <div class="ox-msg__bubble">
            <span class="ox-msg__text">{!! nl2br(e($message->content)) !!}</span>
            <time class="ox-msg__time" datetime="{{ date("Y-m-dTH:i", strtotime($message->created_at->toDateTimeString())) }}">
                {{ $message->created_at->diffForHumans() }}
            </time>
        </div>
    </div>
</div>
