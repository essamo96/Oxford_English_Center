@extends('admin.layout.master')

@section('title')
    محادثة: {{ $group->name }}
@stop

@section('page-title')
    محادثة: {{ $group->name }}
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('group_chat.view') }}" class="text-muted text-hover-info">مراقبة المحادثات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">{{ $group->name }}</li>
@stop

@section('css')
    <link href="{{ asset('assets/oxford/css/group-chat.css') }}?v={{ filemtime(public_path('assets/oxford/css/group-chat.css')) }}" rel="stylesheet" />
    <link href="{{ asset('assets/oxford/css/chat-moderation.css') }}?v={{ filemtime(public_path('assets/oxford/css/chat-moderation.css')) }}" rel="stylesheet" />
    <style>
        #gc_messages { min-height: 420px; }
    </style>
@stop

@section('page-content')

{{-- Same alert tone the students and teachers hear, so an admin watching the
     monitor gets the identical audible cue on every live message. --}}
<audio id="chat-alert-sound" preload="auto" style="display:none">
    <source src="{{ asset('assets/oxford/sound/facebook_chat.mp3') }}" type="audio/mpeg">
</audio>

<div class="d-flex flex-column flex-lg-row">

    {{-- ── Sidebar: group + members ─────────────────────────────────── --}}
    <div class="flex-column flex-lg-row-auto w-lg-300px w-xl-350px mb-10 mb-lg-0 me-lg-7">
        <div class="card card-flush">
            <div class="card-header pt-7">
                <div class="card-title">
                    <div class="symbol symbol-45px symbol-circle me-3">
                        <span class="symbol-label bg-light-success text-success fw-bold fs-3">{{ mb_substr($group->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <span class="fs-4 fw-bold text-gray-900 d-block">{{ $group->name }}</span>
                        <span class="text-muted fs-7">{{ $group->program->name ?? 'بدون برنامج' }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body pt-5">
                <div class="d-flex align-items-center mb-5">
                    <span class="gc-live-dot me-3" id="gc_status_dot"></span>
                    <span class="fs-7 text-muted" id="gc_status_text">جاري الاتصال بالبث اللحظي...</span>
                </div>

                <div class="d-flex align-items-center mb-5">
                    <div class="form-check form-switch form-check-custom form-check-solid me-3">
                        <input class="form-check-input h-20px w-30px" type="checkbox" id="gc_sound_toggle" checked />
                    </div>
                    <label for="gc_sound_toggle" class="fs-7 text-muted cursor-pointer">تنبيه صوتي مع كل رسالة</label>
                </div>

                <div class="separator separator-dashed mb-5"></div>

                <h4 class="fs-6 fw-bold text-gray-900 mb-4">أعضاء المجموعة ({{ $members->count() }})</h4>
                <div class="scroll-y mh-350px pe-3">
                    @forelse($members as $member)
                        @php
                            $mAvatar = \App\Support\ChatAvatar::url($member->image ?? null, $member->role === 'teacher' ? 1 : 0);
                        @endphp
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-35px symbol-circle me-3 {{ $member->role === 'teacher' ? 'gc-ring-teacher' : 'gc-ring-student' }}">
                                <img src="{{ $mAvatar }}" alt="{{ $member->name }}"
                                     onerror="this.onerror=null;this.src='{{ \App\Support\ChatAvatar::default() }}';" />
                            </div>
                            <div class="flex-grow-1">
                                <span class="fs-7 fw-semibold text-gray-900 d-block">{{ $member->name }}</span>
                            </div>
                            <span class="badge {{ $member->role === 'teacher' ? 'badge-light-warning' : 'badge-light-info' }} fs-8">
                                {{ $member->role_ar }}
                            </span>
                        </div>
                    @empty
                        <div class="text-muted fs-7">لا يوجد أعضاء في هذه المجموعة.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ── Messenger ─────────────────────────────────────────────────── --}}
    <div class="flex-lg-row-fluid">
        <div class="card" id="kt_chat_messenger">

            <div class="card-header" id="kt_chat_messenger_header">
                <div class="card-title">
                    {{-- avatar strip of group members, exactly as the Metronic group chat --}}
                    <div class="symbol-group symbol-hover">
                        @foreach($members->take(7) as $member)
                            @php
                                $hasImg  = !empty($member->image);
                                $mAvatar = \App\Support\ChatAvatar::url($member->image ?? null, $member->role === 'teacher' ? 1 : 0);
                                $isDefault = $mAvatar === \App\Support\ChatAvatar::default();
                            @endphp
                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                 title="{{ $member->name }} — {{ $member->role_ar }}">
                                @if($hasImg && !$isDefault)
                                    <img alt="{{ $member->name }}" src="{{ $mAvatar }}"
                                         onerror="this.onerror=null;this.src='{{ \App\Support\ChatAvatar::default() }}';" />
                                @else
                                    {{-- Metronic falls back to a coloured letter tile rather than a
                                         generic silhouette when a member has no photo. --}}
                                    <span class="symbol-label {{ $member->role === 'teacher' ? 'bg-light-warning text-warning' : 'bg-light-info text-info' }} fw-bold">
                                        {{ \App\Support\ChatAvatar::initial($member->name) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                        @if($members->count() > 7)
                            <a href="#" class="symbol symbol-35px symbol-circle" data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                                <span class="symbol-label fs-8 fw-bold" data-bs-toggle="tooltip" title="عرض كل الأعضاء">+{{ $members->count() - 7 }}</span>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-toolbar flex-wrap gap-2">

                    {{-- in-group message search --}}
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="gc_search" class="form-control form-control-sm form-control-solid w-200px ps-10"
                               placeholder="بحث في الرسائل..." autocomplete="off" />
                    </div>

                    {{-- attachments panel --}}
                    <button type="button" class="btn btn-sm btn-light-primary" id="gc_media_btn"
                            data-bs-toggle="modal" data-bs-target="#gc_modal_media">
                        <i class="bi bi-paperclip fs-5 me-1"></i>
                        المرفقات
                        <span class="badge badge-circle badge-primary ms-1" id="gc_media_count">{{ $attachments->count() }}</span>
                    </button>

                    @can('admin.group_chat.moderate')
                        {{-- ban list --}}
                        <button type="button" class="btn btn-sm btn-light-danger"
                                data-bs-toggle="modal" data-bs-target="#gc_modal_bans">
                            <i class="bi bi-person-slash fs-5 me-1"></i>
                            الحظر
                            <span class="badge badge-circle badge-danger ms-1" id="gc_ban_count">{{ count($activeBanIds) }}</span>
                        </button>

                        {{-- freeze the whole conversation: a status switch, like the rest
                             of the admin. Off = students and teachers cannot post. --}}
                        <div class="d-flex align-items-center bg-light rounded px-3 py-1">
                            <div class="form-check form-switch form-check-custom form-check-solid me-2">
                                <input class="form-check-input h-20px w-30px" type="checkbox" id="gc_lock_toggle"
                                       {{ (int) ($group->chat_locked ?? 0) === 1 ? '' : 'checked' }} />
                            </div>
                            <label for="gc_lock_toggle" class="fs-8 fw-semibold cursor-pointer"
                                   id="gc_lock_label">{{ (int) ($group->chat_locked ?? 0) === 1 ? 'المراسلة موقوفة' : 'المراسلة مفعّلة' }}</label>
                        </div>

                        <button type="button" class="btn btn-sm btn-light-danger" id="gc_clear_btn"
                                title="حذف كافة رسائل هذه المجموعة">
                            <i class="bi bi-trash3 fs-5 me-1"></i>
                            حذف الكل
                        </button>
                    @endcan

                    <a href="{{ route('group_chat.view') }}" class="btn btn-sm btn-light">
                        <i class="bi bi-arrow-right fs-5 me-1"></i>
                        كل المجموعات
                    </a>
                </div>
            </div>

            {{-- banner shown while the conversation is frozen --}}
            <div class="px-9 pt-4 {{ (int) ($group->chat_locked ?? 0) === 1 ? '' : 'd-none' }}" id="gc_locked_banner">
                <div class="alert alert-warning d-flex align-items-center mb-0 py-3">
                    <i class="bi bi-lock-fill fs-4 me-3"></i>
                    <span class="fs-7">المراسلة موقوفة في هذه المجموعة — الطلاب والمعلم يمكنهم القراءة فقط. تعليقات الإدارة ما زالت تُرسل.</span>
                </div>
            </div>

            {{-- search results overlay the message list while a term is active --}}
            <div class="card-body pb-0 d-none" id="gc_search_panel">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fs-7 fw-bold text-gray-800" id="gc_search_summary"></span>
                    <button type="button" class="btn btn-sm btn-light" id="gc_search_close">
                        <i class="bi bi-x-lg me-1"></i> إغلاق البحث
                    </button>
                </div>
                <div class="scroll-y mh-400px pe-3" id="gc_search_results"></div>
            </div>

            <div class="card-body" id="kt_chat_messenger_body">
                <div class="scroll-y me-n5 pe-5 h-400px h-lg-auto" id="gc_messages" data-kt-element="messages"
                     data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                     data-kt-scroll-max-height="auto"
                     data-kt-scroll-dependencies="#kt_app_header, #kt_app_toolbar, #kt_app_footer, #kt_chat_messenger_header, #kt_chat_messenger_footer"
                     data-kt-scroll-wrappers="#kt_app_content, #kt_chat_messenger_body" data-kt-scroll-offset="5px">

                    @forelse($messages as $message)
                        @include('admin.group_chat.parts.message', ['message' => $message, 'meId' => $me->id])
                    @empty
                        <div class="text-center text-muted py-20" id="gc_empty">
                            <i class="bi bi-chat-dots fs-3x text-gray-400 mb-3 d-block"></i>
                            لم تبدأ المحادثة في هذه المجموعة بعد — يمكنك أن تبدأها.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="card-footer pt-4" id="kt_chat_messenger_footer">

                {{-- attachment staged for sending (file, image, or a finished recording) --}}
                <div id="gc_attachment_preview" class="mb-3">
                    <div class="gc-preview">
                        <div class="gc-preview__thumb" id="gc_preview_thumb"><i class="bi bi-paperclip"></i></div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="gc-preview__name" id="gc_attachment_name"></div>
                            <div class="gc-preview__size" id="gc_attachment_size"></div>
                        </div>
                        <button type="button" class="btn btn-icon btn-sm btn-active-light-danger" id="gc_attachment_clear" title="إزالة المرفق">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>

                {{-- live recording bar — replaces the composer while the mic is open --}}
                <div id="gc_recording_bar" class="mb-3">
                    <div class="gc-rec">
                        <span class="gc-rec__dot"></span>
                        <span class="gc-rec__time" id="gc_record_timer">0:00</span>
                        <span class="gc-rec__eq"><span></span><span></span><span></span><span></span><span></span></span>
                        <span class="gc-rec__hint">جارٍ التسجيل… اضغط ✓ للإنهاء أو 🗑 للإلغاء</span>
                        <button type="button" class="btn btn-icon btn-sm btn-light-danger" id="gc_record_cancel" title="إلغاء التسجيل">
                            <i class="bi bi-trash3"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-success" id="gc_record_stop" title="إنهاء التسجيل">
                            <i class="bi bi-check-lg"></i>
                        </button>
                    </div>
                </div>

                <div id="gc_composer">
                    <textarea class="form-control form-control-flush mb-3" rows="1" id="gc_input"
                              data-kt-element="input" placeholder="اكتب تعليقك للمجموعة..."></textarea>

                    <div class="d-flex flex-stack">
                        <div class="d-flex align-items-center me-2">
                            <input type="file" id="gc_file" class="d-none"
                                   accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.mp3,.wav,.ogg,.m4a" />

                            <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button"
                                    id="gc_attach" data-bs-toggle="tooltip" title="إرفاق ملف">
                                <i class="ki-duotone ki-paper-clip fs-3"></i>
                            </button>

                            {{-- Bootstrap Icons, not keenicons: this Metronic build ships no
                                 microphone glyph, so ki-microphone-* renders as an empty box. --}}
                            <button class="btn btn-sm btn-icon btn-active-light-danger me-1" type="button"
                                    id="gc_record" data-bs-toggle="tooltip" title="تسجيل رسالة صوتية">
                                <i class="bi bi-mic-fill fs-4"></i>
                            </button>
                        </div>

                        <button class="btn btn-primary" type="button" id="gc_send" data-kt-element="send">
                            <span class="indicator-label">
                                إرسال
                                <i class="bi bi-send ms-1"></i>
                            </span>
                            <span class="indicator-progress">جاري الإرسال...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('modal')

{{-- ── every attachment ever sent in this group ───────────────────────── --}}
<div class="modal fade" id="gc_modal_media" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header pb-3">
                <h3 class="mb-0">مرفقات المجموعة</h3>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg fs-4"></i>
                </div>
            </div>
            <div class="modal-body">
                @php
                    $images = $attachments->where('attachment_type', 'image');
                    $audios = $attachments->where('attachment_type', 'audio');
                    $files  = $attachments->filter(fn($a) => !in_array($a->attachment_type, ['image', 'audio'], true));
                @endphp

                <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#gc_tab_images">الصور ({{ $images->count() }})</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#gc_tab_audio">التسجيلات ({{ $audios->count() }})</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#gc_tab_files">الملفات ({{ $files->count() }})</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="gc_tab_images">
                        @if($images->count())
                            <div class="row g-3">
                                @foreach($images as $item)
                                    <div class="col-4 col-md-3">
                                        <a href="{{ asset($item->attachment) }}" target="_blank" rel="noopener" class="d-block">
                                            <img src="{{ asset($item->attachment) }}" class="w-100 rounded gc-media-thumb" alt="">
                                        </a>
                                        <div class="fs-8 text-muted mt-1 text-truncate">{{ $item->name }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted text-center py-10">لا توجد صور في هذه المجموعة.</div>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="gc_tab_audio">
                        @forelse($audios as $item)
                            <div class="d-flex align-items-center justify-content-between border-bottom border-gray-200 py-3">
                                <div class="me-3 min-w-0">
                                    <div class="fs-7 fw-semibold text-gray-900">{{ $item->name ?? 'مستخدم' }}</div>
                                    <div class="fs-8 text-muted">{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i') }}</div>
                                </div>
                                <audio controls preload="none" src="{{ asset($item->attachment) }}" style="max-width:240px"></audio>
                            </div>
                        @empty
                            <div class="text-muted text-center py-10">لا توجد تسجيلات صوتية في هذه المجموعة.</div>
                        @endforelse
                    </div>

                    <div class="tab-pane fade" id="gc_tab_files">
                        @forelse($files as $item)
                            <a href="{{ asset($item->attachment) }}" target="_blank" rel="noopener"
                               class="d-flex align-items-center border-bottom border-gray-200 py-3 text-hover-primary">
                                <span class="gc-file__icon me-3"><i class="bi bi-file-earmark-arrow-down"></i></span>
                                <span class="flex-grow-1 min-w-0">
                                    <span class="fs-7 fw-semibold d-block text-truncate">{{ $item->attachment_name ?: 'مرفق' }}</span>
                                    <span class="fs-8 text-muted">{{ $item->name }} — {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i') }}</span>
                                </span>
                            </a>
                        @empty
                            <div class="text-muted text-center py-10">لا توجد ملفات في هذه المجموعة.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@can('admin.group_chat.moderate')
{{-- ── ban management ─────────────────────────────────────────────────── --}}
<div class="modal fade" id="gc_modal_bans" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header pb-3">
                <h3 class="mb-0">حظر الطلاب من المراسلة</h3>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg fs-4"></i>
                </div>
            </div>
            <div class="modal-body">

                <div class="alert alert-light-primary d-flex align-items-center py-3 mb-5">
                    <i class="bi bi-info-circle fs-4 me-3"></i>
                    <span class="fs-8">الطالب المحظور يستطيع قراءة المحادثة، لكنه لا يستطيع إرسال رسائل. يصله إشعار لحظي عند الحظر وعند فكّه.</span>
                </div>

                {{-- ban a student --}}
                <h5 class="fs-6 fw-bold mb-3">حظر طالب</h5>
                <div class="row g-3 mb-8">
                    <div class="col-md-5">
                        <select class="form-select form-select-sm" id="gc_ban_student">
                            <option value="">اختر الطالب...</option>
                            @foreach($members->where('role', 'student') as $member)
                                <option value="{{ $member->id }}"
                                        @if(in_array($member->id, $activeBanIds)) disabled @endif>
                                    {{ $member->name }}@if(in_array($member->id, $activeBanIds)) (محظور){{ '' }}@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" id="gc_ban_type">
                            <option value="mute">🔇 إسكات — يقرأ ولا يرسل</option>
                            <option value="ban">⛔ حظر كامل — لا يقرأ ولا يرسل</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        {{-- optional: leaving this blank restricts silently --}}
                        <input type="text" class="form-control form-control-sm" id="gc_ban_reason"
                               placeholder="السبب (اختياري)" />
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger w-100" id="gc_ban_submit">تطبيق</button>
                    </div>
                </div>

                <h5 class="fs-6 fw-bold mb-3">قائمة الحظر</h5>
                <div id="gc_ban_list">
                    @forelse($bans as $ban)
                        @include('admin.group_chat.parts.ban-row', ['ban' => $ban])
                    @empty
                        <div class="text-muted text-center py-8" id="gc_ban_empty">لا يوجد طلاب محظورون في هذه المجموعة.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

<div class="modal fade" id="kt_modal_view_users" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog mw-650px">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-between">
                <h3 class="mb-0">أعضاء المجموعة</h3>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg fs-4"></i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                @foreach($members as $member)
                    @php
                        $mAvatar = \App\Support\ChatAvatar::url($member->image ?? null, $member->role === 'teacher' ? 1 : 0);
                    @endphp
                    <div class="d-flex align-items-center py-3 border-bottom border-gray-200">
                        <div class="symbol symbol-40px symbol-circle me-4 {{ $member->role === 'teacher' ? 'gc-ring-teacher' : 'gc-ring-student' }}">
                            <img src="{{ $mAvatar }}" alt="{{ $member->name }}"
                                 onerror="this.onerror=null;this.src='{{ \App\Support\ChatAvatar::default() }}';" />
                        </div>
                        <span class="fs-6 fw-semibold text-gray-900 flex-grow-1">{{ $member->name }}</span>
                        <span class="badge {{ $member->role === 'teacher' ? 'badge-light-warning' : 'badge-light-info' }}">{{ $member->role_ar }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('assets/oxford/js/voice-player.js') }}?v={{ filemtime(public_path('assets/oxford/js/voice-player.js')) }}"></script>
<script src="{{ asset('assets/oxford/js/chat-moderation.js') }}?v={{ filemtime(public_path('assets/oxford/js/chat-moderation.js')) }}"></script>
<script>
(function () {
    var GROUP_ID  = {{ (int) $group->id }};
    var CSRF      = $('meta[name="csrf-token"]').attr('content');
    var URL_SEND  = "{{ route('group_chat.send', $group->id) }}";
    var URL_FETCH = "{{ route('group_chat.messages', $group->id) }}";
    var URL_DEL   = "{{ route('group_chat.delete') }}";
    var URL_SEARCH = "{{ route('group_chat.search', $group->id) }}";
    @can('admin.group_chat.moderate')
    var URL_CLEAR = "{{ route('group_chat.clear', $group->id) }}";
    var URL_LOCK  = "{{ route('group_chat.toggle_lock', $group->id) }}";
    var URL_BAN   = "{{ route('group_chat.ban', $group->id) }}";
    var URL_UNBAN = "{{ route('group_chat.unban', $group->id) }}";
    @endcan
    var lastId    = {{ (int) $lastId }};

    var $box = $('#gc_messages');
    var pendingFile = null;   // File or recorded Blob awaiting send
    var pendingKind = null;   // 'audio' when it came from the recorder
    var previewUrl  = null;   // object URL for an image/voice preview, revoked on clear

    function scrollToBottom() {
        $box.stop().animate({ scrollTop: $box[0].scrollHeight }, 300);
    }

    function humanSize(bytes) {
        if (bytes < 1024) return bytes + ' بايت';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' ك.ب';
        return (bytes / 1048576).toFixed(1) + ' م.ب';
    }

    // Newly injected bubbles carry voice players that need enhancing.
    function enhance(scope) {
        if (window.OxVoicePlayer) { window.OxVoicePlayer.initAll(scope); }
    }

    function appendHtml(html) {
        $('#gc_empty').remove();
        var $node = $(html);
        $box.append($node);
        enhance($node[0]);
        scrollToBottom();
    }

    enhance(document);
    scrollToBottom();

    // ── alert sound ───────────────────────────────────────────────────
    var soundOn = true;
    try { soundOn = localStorage.getItem('gc_sound') !== '0'; } catch (e) {}
    $('#gc_sound_toggle').prop('checked', soundOn).on('change', function () {
        soundOn = this.checked;
        try { localStorage.setItem('gc_sound', soundOn ? '1' : '0'); } catch (e) {}
    });

    function ding() {
        if (!soundOn) return;
        var el = document.getElementById('chat-alert-sound');
        if (!el) return;
        try {
            el.currentTime = 0;
            var p = el.play();
            // Browsers refuse audio before the first user gesture — harmless here.
            if (p && p.catch) { p.catch(function () {}); }
        } catch (e) {}
    }

    // ── send ──────────────────────────────────────────────────────────
    function send() {
        var text = $('#gc_input').val().trim();
        if (!text && !pendingFile) { return; }

        var fd = new FormData();
        fd.append('message', text);
        fd.append('_token', CSRF);
        if (pendingFile) {
            // A recorded blob has no filename of its own — give it one so the
            // server can read an extension off it.
            fd.append('attachment', pendingFile, pendingFile.name || 'voice-note.webm');
            if (pendingKind) fd.append('attachment_kind', pendingKind);
        }

        var $btn = $('#gc_send');
        $btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        $.ajax({
            url: URL_SEND, method: 'POST', data: fd,
            processData: false, contentType: false, dataType: 'json',
            success: function (res) {
                if (res.state === 1) {
                    // Render locally; the Pusher echo of our own message is ignored
                    // by lastId so it never double-posts.
                    appendHtml(res.html);
                    lastId = Math.max(lastId, res.last_id);
                    $('#gc_input').val('');
                    clearAttachment();
                } else {
                    alert(res.message || 'تعذر إرسال الرسالة');
                }
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'تعذر إرسال الرسالة';
                alert(msg);
            },
            complete: function () {
                $btn.removeAttr('data-kt-indicator').prop('disabled', false);
            }
        });
    }

    $('#gc_send').on('click', send);
    $('#gc_input').on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
    });

    // ── attachment staging ────────────────────────────────────────────
    function stageFile(file, kind) {
        pendingFile = file;
        pendingKind = kind || null;

        $('#gc_attachment_name').text(kind === 'audio' ? 'رسالة صوتية' : (file.name || 'مرفق'));
        $('#gc_attachment_size').text(humanSize(file.size));

        if (previewUrl) { URL.revokeObjectURL(previewUrl); previewUrl = null; }

        var $thumb = $('#gc_preview_thumb');
        if (kind === 'audio') {
            $thumb.html('<i class="bi bi-mic-fill"></i>');
        } else if (file.type && file.type.indexOf('image/') === 0) {
            previewUrl = URL.createObjectURL(file);
            $thumb.html('<img src="' + previewUrl + '" class="w-100 h-100 rounded" style="object-fit:cover" alt="">');
        } else {
            $thumb.html('<i class="bi bi-file-earmark"></i>');
        }

        $('#gc_attachment_preview').show();
    }

    function clearAttachment() {
        pendingFile = null; pendingKind = null;
        if (previewUrl) { URL.revokeObjectURL(previewUrl); previewUrl = null; }
        $('#gc_file').val('');
        $('#gc_attachment_preview').hide();
        $('#gc_attachment_name').text('');
        $('#gc_attachment_size').text('');
    }

    $('#gc_attach').on('click', function () { $('#gc_file').click(); });
    $('#gc_file').on('change', function () {
        if (this.files && this.files[0]) { stageFile(this.files[0], null); }
    });
    $('#gc_attachment_clear').on('click', clearAttachment);

    // drag & drop onto the messenger card
    var $card = $('#kt_chat_messenger');
    $card.on('dragover', function (e) { e.preventDefault(); $card.addClass('gc-dragover'); })
         .on('dragleave drop', function () { $card.removeClass('gc-dragover'); })
         .on('drop', function (e) {
             e.preventDefault();
             var dt = e.originalEvent.dataTransfer;
             if (dt && dt.files && dt.files[0]) { stageFile(dt.files[0], null); }
         });

    // ── voice notes (MediaRecorder) ───────────────────────────────────
    var recorder = null, chunks = [], timerHandle = null, seconds = 0, cancelled = false, micStream = null;

    function showComposer(recording) {
        $('#gc_recording_bar').toggle(recording);
        $('#gc_composer').toggle(!recording);
        $('#gc_record').toggleClass('recording', recording);
    }

    function stopTimer() {
        clearInterval(timerHandle); timerHandle = null; seconds = 0;
        $('#gc_record_timer').text('0:00');
    }

    function releaseMic() {
        if (micStream) {
            micStream.getTracks().forEach(function (t) { t.stop(); });
            micStream = null;
        }
    }

    /**
     * Pick the smallest codec/bitrate the browser will actually honour.
     *
     * Speech does not need music-grade audio: mono Opus at 24 kbps is clear for
     * voice and roughly a tenth the size of the browser default (~128 kbps
     * stereo) — a 60-second note drops from about 1 MB to under 200 KB. The
     * candidates are tried in order because Safari has no Opus/WebM support and
     * falls back to MP4/AAC.
     */
    function pickRecorderOptions() {
        var candidates = [
            'audio/webm;codecs=opus',
            'audio/ogg;codecs=opus',
            'audio/webm',
            'audio/mp4'
        ];
        for (var i = 0; i < candidates.length; i++) {
            if (window.MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported(candidates[i])) {
                return { mimeType: candidates[i], audioBitsPerSecond: 24000 };
            }
        }
        return { audioBitsPerSecond: 24000 };
    }

    function extFor(mime) {
        if (!mime) return 'webm';
        if (mime.indexOf('ogg') !== -1) return 'ogg';
        if (mime.indexOf('mp4') !== -1) return 'm4a';
        return 'webm';
    }

    function startRecording() {
        if (!navigator.mediaDevices || !window.MediaRecorder) {
            alert('التسجيل الصوتي غير مدعوم في هذا المتصفح.');
            return;
        }
        // Mono at 16 kHz is the standard voice-note capture profile: it halves the
        // data before encoding even starts, and the browser's noise suppression
        // and echo cancellation keep speech intelligible at that rate.
        var constraints = {
            audio: {
                channelCount: 1,
                sampleRate: 16000,
                echoCancellation: true,
                noiseSuppression: true,
                autoGainControl: true
            }
        };

        navigator.mediaDevices.getUserMedia(constraints).then(function (stream) {
            micStream = stream;
            chunks = [];
            cancelled = false;

            var opts = pickRecorderOptions();
            try {
                recorder = new MediaRecorder(stream, opts);
            } catch (e) {
                // A browser that rejects the options still records at its default.
                recorder = new MediaRecorder(stream);
                opts = {};
            }

            recorder.ondataavailable = function (e) { if (e.data.size > 0) chunks.push(e.data); };
            recorder.onstop = function () {
                releaseMic();
                showComposer(false);
                stopTimer();

                if (cancelled) { chunks = []; return; }

                var mime = recorder.mimeType || opts.mimeType || 'audio/webm';
                var blob = new Blob(chunks, { type: mime });
                blob.name = 'voice-note.' + extFor(mime);
                stageFile(blob, 'audio');
            };

            recorder.start();
            showComposer(true);
            timerHandle = setInterval(function () {
                seconds++;
                var m = Math.floor(seconds / 60);
                var s = seconds % 60;
                $('#gc_record_timer').text(m + ':' + (s < 10 ? '0' + s : s));
                if (seconds >= 300) { recorder.stop(); }   // hard cap at 5 minutes
            }, 1000);
        }).catch(function () {
            alert('تعذر الوصول إلى الميكروفون. تأكد من منح الإذن للمتصفح.');
        });
    }

    $('#gc_record').on('click', function () {
        if (recorder && recorder.state === 'recording') { recorder.stop(); return; }
        startRecording();
    });
    $('#gc_record_stop').on('click', function () {
        if (recorder && recorder.state === 'recording') { recorder.stop(); }
    });
    $('#gc_record_cancel').on('click', function () {
        if (recorder && recorder.state === 'recording') { cancelled = true; recorder.stop(); }
    });

    // ── delete a message ──────────────────────────────────────────────
    $box.on('click', '.gc-delete', function () {
        var id = $(this).data('id');
        if (!confirm('هل تريد حذف هذه الرسالة نهائياً؟')) return;
        $.post(URL_DEL, { id: id, _token: CSRF }, function (res) {
            if (res.state === 1) {
                $('.gc-msg[data-message-id="' + id + '"]').fadeOut(200, function () { $(this).remove(); });
            }
        }, 'json');
    });

    // ── in-group message search ───────────────────────────────────────
    var searchTimer = null;

    function closeSearch() {
        $('#gc_search').val('');
        $('#gc_search_panel').addClass('d-none');
        $('#kt_chat_messenger_body').removeClass('d-none');
    }

    function runSearch(term) {
        if (!term) { closeSearch(); return; }

        $.getJSON(URL_SEARCH, { q: term }, function (res) {
            if (res.state !== 1) return;
            var $results = $('#gc_search_results').empty();

            if (!res.total) {
                $results.html('<div class="text-muted text-center py-10">لا توجد رسائل مطابقة.</div>');
            } else {
                res.messages.forEach(function (html) { $results.append(html); });
            }

            $('#gc_search_summary').text(res.total + ' نتيجة للبحث عن "' + term + '"');
            $('#gc_search_panel').removeClass('d-none');
            $('#kt_chat_messenger_body').addClass('d-none');
        });
    }

    $('#gc_search').on('input', function () {
        var term = $(this).val().trim();
        clearTimeout(searchTimer);
        // Debounced so a fast typist does not fire a query per keystroke.
        searchTimer = setTimeout(function () { runSearch(term); }, 350);
    });
    $('#gc_search_close').on('click', closeSearch);

    // Clicking a hit returns to the conversation and flashes the message.
    $('#gc_search_results').on('click', '[data-jump-to]', function () {
        var id = $(this).data('jump-to');
        closeSearch();
        var $target = $('.gc-msg[data-message-id="' + id + '"]');
        if ($target.length) {
            $box.animate({ scrollTop: $box.scrollTop() + $target.position().top - 60 }, 300);
            $target.addClass('gc-flash');
            setTimeout(function () { $target.removeClass('gc-flash'); }, 1600);
        }
    });

@can('admin.group_chat.moderate')
    // ── avatar click → mute / ban that student ────────────────────────
    OxChatModeration.init({
        container: '#gc_messages',
        avatar: '[data-moderate-student]',
        token: CSRF,
        groupId: GROUP_ID,
        urls: {
            state:    "{{ route('group_chat.student_state', $group->id) }}",
            restrict: URL_BAN,
            lift:     URL_UNBAN
        },
        // The ban/unban response already carries the rendered row, so the modal
        // list is refreshed from it. Re-posting to "sync" would fire a second
        // restriction and send the student a duplicate notification.
        onChange: function (studentId, type, data) {
            if (!data || !data.html) return;
            $('#gc_ban_empty').remove();
            var $row = $('.gc-ban-row[data-ban-student="' + studentId + '"]');
            if ($row.length) { $row.replaceWith(data.html); }
            else { $('#gc_ban_list').prepend(data.html); }
            $('#gc_ban_student option[value="' + studentId + '"]').prop('disabled', !!type);
            refreshBanCount();
        }
    });

    // ── freeze / unfreeze the conversation ────────────────────────────
    function paintLockState(locked) {
        $('#gc_lock_toggle').prop('checked', !locked);
        $('#gc_lock_label').text(locked ? 'المراسلة موقوفة' : 'المراسلة مفعّلة');
        $('#gc_locked_banner').toggleClass('d-none', !locked);
    }

    $('#gc_lock_toggle').on('change', function () {
        var $el = $(this);
        $el.prop('disabled', true);
        $.post(URL_LOCK, { _token: CSRF }, function (res) {
            if (res.state === 1) {
                paintLockState(res.locked);
            } else {
                // Put the switch back where it was; the server is the authority.
                $el.prop('checked', !$el.prop('checked'));
            }
        }, 'json').fail(function () {
            $el.prop('checked', !$el.prop('checked'));
            alert('تعذر تغيير حالة المراسلة');
        }).always(function () { $el.prop('disabled', false); });
    });

    // ── wipe the whole conversation ───────────────────────────────────
    $('#gc_clear_btn').on('click', function () {
        if (!confirm('سيتم حذف كافة رسائل هذه المجموعة ومرفقاتها نهائياً. هل أنت متأكد؟')) return;
        if (!confirm('تأكيد أخير: لا يمكن التراجع عن هذه العملية.')) return;

        var $btn = $(this).prop('disabled', true);
        $.post(URL_CLEAR, { _token: CSRF }, function (res) {
            if (res.state === 1) {
                $box.html('<div class="text-center text-muted py-20" id="gc_empty">'
                    + '<i class="bi bi-chat-dots fs-3x text-gray-400 mb-3 d-block"></i>'
                    + 'تم حذف كافة الرسائل.</div>');
                lastId = 0;
                $('#gc_media_count').text('0');
            } else {
                alert(res.message || 'تعذر حذف الرسائل');
            }
        }, 'json').fail(function () {
            alert('تعذر حذف الرسائل');
        }).always(function () { $btn.prop('disabled', false); });
    });

    // ── bans ──────────────────────────────────────────────────────────
    function refreshBanCount() {
        $('#gc_ban_count').text($('#gc_ban_list .gc-ban-toggle:checked').length);
    }

    $('#gc_ban_submit').on('click', function () {
        var studentId = $('#gc_ban_student').val();
        if (!studentId) { alert('اختر الطالب أولاً'); return; }

        var $btn = $(this).prop('disabled', true);
        var type = $('#gc_ban_type').val();
        if (type === 'ban' && !confirm('الحظر الكامل يمنع الطالب من إرسال الرسائل ومن رؤية المحادثة نهائياً. هل تريد المتابعة؟')) {
            $btn.prop('disabled', false);
            return;
        }

        $.post(URL_BAN, {
            _token: CSRF,
            student_id: studentId,
            type: type,
            reason: $('#gc_ban_reason').val().trim()   // blank = applied silently
        }, function (res) {
            if (res.state === 1) {
                $('#gc_ban_empty').remove();
                var $existing = $('.gc-ban-row[data-ban-student="' + res.student_id + '"]');
                if ($existing.length) { $existing.replaceWith(res.html); }
                else { $('#gc_ban_list').prepend(res.html); }

                $('#gc_ban_student option[value="' + res.student_id + '"]').prop('disabled', true);
                $('#gc_ban_student').val('');
                $('#gc_ban_reason').val('');
                refreshBanCount();
            } else {
                alert(res.message || 'تعذر حظر الطالب');
            }
        }, 'json').fail(function () {
            alert('تعذر حظر الطالب');
        }).always(function () { $btn.prop('disabled', false); });
    });

    // status switch on each row: checked = banned, unchecked = allowed
    $('#gc_ban_list').on('change', '.gc-ban-toggle', function () {
        var $el = $(this);
        var studentId = $el.data('student-id');
        var banning = $el.prop('checked');
        var url = banning ? URL_BAN : URL_UNBAN;

        $el.prop('disabled', true);
        $.post(url, { _token: CSRF, student_id: studentId, type: $el.data('type') || 'mute' }, function (res) {
            if (res.state === 1) {
                $('.gc-ban-row[data-ban-student="' + studentId + '"]').replaceWith(res.html);
                $('#gc_ban_student option[value="' + studentId + '"]').prop('disabled', banning);
                refreshBanCount();
            } else {
                $el.prop('checked', !banning);
            }
        }, 'json').fail(function () {
            $el.prop('checked', !banning);
            alert('تعذر تغيير حالة الحظر');
        }).always(function () { $el.prop('disabled', false); });
    });
@endcan

    // ── live delivery: pull anything newer than lastId ────────────────
    var fetching = false;
    function fetchNew() {
        if (fetching) return;
        fetching = true;
        $.getJSON(URL_FETCH, { after_id: lastId }, function (res) {
            if (res.state === 1 && res.messages.length) {
                var added = 0;
                res.messages.forEach(function (html) {
                    // Guard against a Pusher push and a poll racing for the same row.
                    var $html = $(html);
                    var id = $html.data('message-id');
                    if ($('.gc-msg[data-message-id="' + id + '"]').length === 0) {
                        appendHtml($html);
                        added++;
                    }
                });
                lastId = res.last_id;
                if (added > 0 && res.has_incoming) { ding(); }
            }
        }).always(function () { fetching = false; });
    }

    // Pusher is the live path; the interval is the fallback for a dropped socket.
    window.addEventListener('load', function () {
        var rt = window.OXFORD_RT || {};
        function offline(text) {
            $('#gc_status_dot').addClass('gc-offline');
            $('#gc_status_text').text(text);
        }

        if (rt.key && typeof Pusher !== 'undefined') {
            var opts = { cluster: rt.cluster || 'mt1', encrypted: true, forceTLS: (rt.scheme !== 'http') };
            if (rt.host) {
                opts.wsHost = rt.host;
                opts.wsPort = rt.port;
                opts.wssPort = rt.port;
                opts.enabledTransports = ['ws', 'wss'];
            }
            try {
                var pusher = new Pusher(rt.key, opts);
                pusher.connection.bind('connected', function () {
                    $('#gc_status_dot').removeClass('gc-offline');
                    $('#gc_status_text').text('متصل — المراقبة اللحظية تعمل');
                });
                pusher.connection.bind('disconnected', function () { offline('انقطع الاتصال — يتم التحديث كل 10 ثوانٍ'); });
                pusher.connection.bind('error', function () { offline('تعذر البث اللحظي — يتم التحديث كل 10 ثوانٍ'); });
                var ch = pusher.subscribe('chat');
                ch.bind('send', function (data) {
                    // Every group shares the single 'chat' channel, so filter here.
                    if (!data || !data.data) return;
                    if (parseInt(data.data.group_id, 10) !== GROUP_ID) return;
                    fetchNew();
                });
                // Another admin freezing or wiping this group must be reflected here
                // too, otherwise two open tabs disagree about the group's state.
                // A message deleted from another admin tab disappears here too.
                ch.bind('message-deleted', function (data) {
                    if (!data || !data.data) return;
                    if (parseInt(data.data.group_id, 10) !== GROUP_ID) return;
                    $('.gc-msg[data-message-id="' + data.data.message_id + '"]')
                        .fadeOut(200, function () { $(this).remove(); });
                });
                ch.bind('group-state', function (data) {
                    if (!data || !data.data) return;
                    if (parseInt(data.data.group_id, 10) !== GROUP_ID) return;
                    if (data.data.state === 'cleared') {
                        $('.gc-msg').remove();
                        lastId = 0;
                    } else if (typeof paintLockState === 'function') {
                        paintLockState(!!data.data.locked);
                    }
                });
            } catch (e) {
                offline('تعذر البث اللحظي — يتم التحديث كل 10 ثوانٍ');
            }
        } else {
            offline('البث اللحظي غير مُهيأ — يتم التحديث كل 10 ثوانٍ');
        }

        setInterval(fetchNew, 10000);
    });
})();
</script>
@stop
