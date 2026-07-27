@php
    // \Auth::user()->id relies on the DEFAULT guard (students) — on a teacher's session that
    // guard's user() is null, which would fatal-error here ("member function on null").
    // Resolve the id from whichever guard is actually authenticated instead.
    $currentId = \Auth::guard('students')->check()
        ? \Auth::guard('students')->id()
        : (\Auth::guard('teachers')->check() ? \Auth::guard('teachers')->id() : null);
    $isMine = $message->from_user == $currentId;
    $avatarUrl = ($message->image ?? null) && file_exists(public_path($message->image))
        ? asset($message->image)
        : asset('assets/oxford/images/user-avatar.png');
@endphp
<div class="ox-msg msg_container {{ $isMine ? 'ox-msg--mine base_sent' : 'ox-msg--theirs base_receive' }}" data-message-id="{{ $message->id }}">
    <img class="ox-msg__avatar" src="{{ $avatarUrl }}" alt="{{ $message->name }}">
    <div class="ox-msg__col">
        <div class="ox-msg__meta">
            <span class="ox-msg__name">{{ $message->name }}</span>
        </div>
        <div class="ox-msg__bubble">{!! nl2br(e($message->content)) !!}</div>
        <time class="ox-msg__time" datetime="{{ date("Y-m-dTH:i", strtotime($message->created_at->toDateTimeString())) }}">
            {{ $message->created_at->diffForHumans() }}
        </time>
    </div>
</div>
