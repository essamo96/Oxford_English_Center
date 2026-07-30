{{--
    One hit in the in-group message search.

    Props:
      $message — row from Message::searchInGroup() (carries name/image)
      $term    — the search term, highlighted in the body
--}}
@php
    $type   = (int) ($message->user_type ?? 0);
    $avatar = \App\Support\ChatAvatar::url($message->image ?? null, $type);
    $roleAr = $type === 2 ? 'الإدارة' : ($type === 1 ? 'المعلم' : 'طالب');
    $roleCls = $type === 2 ? 'badge-light-danger' : ($type === 1 ? 'badge-light-warning' : 'badge-light-info');
    $ringCls = $type === 2 ? 'gc-ring-admin' : ($type === 1 ? 'gc-ring-teacher' : 'gc-ring-student');

    $created = $message->created_at instanceof \Carbon\Carbon
        ? $message->created_at
        : \Carbon\Carbon::parse($message->created_at);

    // Escape first, then wrap the term — highlighting raw input would let a search
    // for "<script>" inject markup into the results panel.
    $body = e((string) $message->content);
    if ($term !== '') {
        $body = preg_replace(
            '/(' . preg_quote(e($term), '/') . ')/iu',
            '<mark class="gc-hl">$1</mark>',
            $body
        );
    }
@endphp

<div class="gc-search-hit d-flex align-items-start p-3 rounded mb-2" data-jump-to="{{ $message->id }}">
    <div class="symbol symbol-35px symbol-circle me-3 {{ $ringCls }}">
        <img src="{{ $avatar }}" alt="{{ $message->name }}"
             onerror="this.onerror=null;this.src='{{ \App\Support\ChatAvatar::default() }}';" />
    </div>
    <div class="flex-grow-1 min-w-0">
        <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
            <span class="fw-bold text-gray-900 fs-7">{{ $message->name ?? 'مستخدم محذوف' }}</span>
            <span class="badge {{ $roleCls }} fs-8">{{ $roleAr }}</span>
            <span class="text-muted fs-8">{{ $created->format('Y-m-d H:i') }}</span>
        </div>
        <div class="fs-7 text-gray-700 gc-search-hit__body">
            @if(!empty($message->content))
                {!! $body !!}
            @endif
            @if(!empty($message->attachment))
                <span class="badge badge-light-primary fs-8">
                    <i class="bi bi-paperclip"></i> {{ $message->attachment_name ?: 'مرفق' }}
                </span>
            @endif
        </div>
    </div>
</div>
