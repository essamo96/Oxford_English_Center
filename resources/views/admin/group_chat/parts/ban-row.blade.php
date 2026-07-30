{{--
    One row in the group's ban list.

    Props:
      $ban — GroupChatBan with `student` loaded. status true = ban in force,
             false = lifted (kept as moderation history).
--}}
@php
    $student = $ban->student;
    $avatar  = \App\Support\ChatAvatar::url($student->image ?? null, 0);
    $active  = (bool) $ban->status;
@endphp

<div class="d-flex align-items-center py-3 border-bottom border-gray-200 gc-ban-row"
     data-ban-student="{{ $ban->student_id }}">

    <div class="symbol symbol-40px symbol-circle me-3 {{ $active ? 'gc-ring-banned' : 'gc-ring-student' }}">
        <img src="{{ $avatar }}" alt="{{ $student->name ?? '' }}"
             onerror="this.onerror=null;this.src='{{ \App\Support\ChatAvatar::default() }}';" />
    </div>

    <div class="flex-grow-1 min-w-0 me-3">
        <span class="fs-6 fw-semibold text-gray-900 d-block">
            {{ $student->name ?? 'طالب محذوف' }}
            @if($active)
                {{-- ban and mute differ in severity, so the list must say which --}}
                <span class="badge {{ $ban->isFullBan() ? 'badge-light-danger' : 'badge-light-warning' }} fs-8 ms-1">
                    {{ $ban->isFullBan() ? '⛔ حظر كامل' : '🔇 إسكات' }}
                </span>
            @endif
        </span>
        @if($ban->reason)
            <span class="fs-8 text-muted d-block">السبب: {{ $ban->reason }}</span>
        @else
            <span class="fs-8 text-muted d-block fst-italic">بدون ذكر سبب</span>
        @endif
        <span class="fs-8 text-muted">
            @if($active)
                مُنذ {{ $ban->updated_at?->diffForHumans() }}
            @else
                فُك الحظر {{ $ban->unbanned_at?->diffForHumans() ?? $ban->updated_at?->diffForHumans() }}
            @endif
        </span>
    </div>

    {{-- status switch: on = banned, off = allowed. Mirrors the status field used
         across the rest of the admin, so the interaction is already familiar. --}}
    <div class="form-check form-switch form-check-custom form-check-solid">
        {{-- data-type carries the severity so re-enabling the switch restores the
             same restriction, instead of silently downgrading a ban to a mute --}}
        <input class="form-check-input gc-ban-toggle" type="checkbox"
               data-student-id="{{ $ban->student_id }}"
               data-type="{{ $ban->type ?? 'mute' }}"
               {{ $active ? 'checked' : '' }} />
    </div>
    <span class="badge {{ $active ? 'badge-light-danger' : 'badge-light-success' }} ms-3 gc-ban-badge">
        {{ $active ? $ban->typeLabel() : 'مسموح' }}
    </span>
</div>
