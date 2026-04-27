@extends('admin.layout.master')
@section('title', 'مركز المحادثات - مراسلات المعلمين')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">مركز المحادثات (المعلمين)</li>
@stop

@section('css')
    <style>
        .contact-item {
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        .contact-item:hover {
            cursor: pointer;
            background-color: #f4f6fa !important;
        }

        .contact-item.active {
            background-color: #f4f6fa !important;
            border-color: #e4e6ea;
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #kt_chat_messenger_body {
            height: 300px;
        }

        .empty-chat-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #a1a5b7;
        }

        /* Message Actions Side Positioning */
        .chat-bubble-container {
            position: relative;
        }

        .message-actions {
            display: none;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 80px;
        }

        .template-outgoing .message-actions {
            left: -90px;
            flex-direction: row-reverse;
        }

        .template-incoming .message-actions {
            right: -90px;
        }

        .chat-bubble-container:hover .message-actions {
            display: flex;
        }

        .action-btn {
            cursor: pointer;
            padding: 5px;
            border-radius: 5px;
            background: #f8f9fa;
            border: 1px solid #eee;
            margin: 0 2px;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .bg-light-info { background-color: #f1faff !important; }
        .bg-light-primary { background-color: #e1f0ff !important; }
    </style>
@stop

@section('page-content')
    <div class="d-flex flex-column flex-lg-row">
        <!-- Sidebar: Contacts List -->
        <div class="flex-column flex-lg-row-auto w-100 w-lg-300px w-xl-400px mb-10 mb-lg-0">
            <div class="card card-flush">
                <div class="card-header pt-7" id="kt_chat_contacts_header">
                    <div class="d-flex flex-stack w-100 mb-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">المحادثات</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="javascript:void(0)" class="btn btn-sm btn-light-primary fw-bold" title="قريباً">
                                <i class="ki-duotone ki-archive fs-3 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> الأرشيف
                            </a>
                        </div>
                    </div>
                    <form class="w-100 position-relative" id="searchForm" autocomplete="off">
                        <i
                            class="ki-duotone ki-magnifier fs-3 text-gray-500 position-absolute top-50 ms-5 translate-middle-y">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                        <input type="text" class="form-control form-control-solid px-13" id="contactSearch"
                            name="search" value="" placeholder="البحث عن اسم أو كلمة..." />
                        <div class="position-absolute top-50 end-0 translate-middle-y me-5 d-none" id="searchLoader">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                        </div>
                    </form>
                </div>
                <div class="card-body pt-5" id="kt_chat_contacts_body">
                    <div class="scroll-y me-n5 pe-5 h-500px h-lg-auto" id="contacts-container" data-kt-scroll="true"
                        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_header, #kt_app_header, #kt_toolbar, #kt_app_toolbar, #kt_footer, #kt_app_footer, #kt_chat_contacts_header"
                        data-kt-scroll_wrappers="#kt_content, #kt_app_content, #kt_chat_contacts_body"
                        data-kt-scroll-offset="5px">
                        @include('admin.dashboard.parts.teacher_contact_list', ['teachers' => $teachers])
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content: Messenger -->
        <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10">
            <div class="card" id="kt_chat_messenger">
                <!-- Header for teacher info -->
                <div class="card-header d-none" id="kt_chat_messenger_header">
                    <div class="card-title">
                        <div class="d-flex justify-content-center flex-column me-3">
                            <a href="#" class="fs-4 fw-bold text-gray-900 text-hover-info me-1 mb-2 lh-1"
                                id="active-teacher-name">Name</a>
                            <div class="mb-0 lh-1">
                                <span class="badge badge-circle w-10px h-10px me-1"
                                    id="active-teacher-status-indicator"></span>
                                <span class="fs-7 fw-semibold text-muted" id="active-teacher-status-text">Status</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <button class="btn btn-sm btn-icon btn-active-light-primary me-2" id="refreshChat"
                            data-bs-toggle="tooltip" title="تحديث المحادثة">
                            <i class="ki-duotone ki-arrows-loop fs-2"><span class="path1"></span><span
                                    class="path2"></span></i>
                        </button>

                        <div class="me-n3">
                            <button class="btn btn-sm btn-icon btn-active-light-primary" data-kt-menu-trigger="click"
                                data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-dots-square fs-2">
                                    <span class="path1"></span><span class="path2"></span><span
                                        class="path3"></span><span class="path4"></span>
                                </i>
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">أدوات المعلم</div>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3" id="tool-edit-teacher" target="_blank">تعديل
                                        البيانات</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3" id="tool-change-password" target="_blank">تغيير كلمة المرور</a>
                                </div>
                                <div class="separator my-2"></div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 text-danger" id="tool-delete-all-msgs">حذف أرشيف
                                        الرسائل</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Body for messages -->
                <div class="card-body" id="kt_chat_messenger_body">
                    <div id="chat-content-area" class="h-100">
                        <div class="empty-chat-state">
                            <img src="{{ asset('assets/media/illustrations/sigma-1/20.png') }}" class="mw-100 mh-200px mb-10" alt="" />
                            <h3 class="fw-bold text-gray-900">اختر محادثة للبدء</h3>
                            <p class="text-muted fw-semibold fs-6">قم باختيار معلم من القائمة الجانبية لبدء المراسلة الفورية ومتابعة سجل المحادثات</p>
                        </div>

                        <div id="messages-container" class="scroll-y me-n5 pe-5 d-none h-100" data-kt-scroll="true"
                            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto">
                            <!-- Messages will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Footer for input -->
                <div class="card-footer pt-4 d-none" id="kt_chat_messenger_footer">
                    <textarea class="form-control form-control-flush mb-3" rows="2" id="replyTextarea"
                        placeholder="اكتب ردك هنا..."></textarea>
                    <div class="d-flex flex-stack">
                        <div class="d-flex align-items-center me-2">
                            <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button"
                                data-bs-toggle="tooltip" title="قريباً: إرفاق ملفات">
                                <i class="ki-duotone ki-paper-clip fs-3"></i>
                            </button>
                        </div>
                        <button class="btn btn-primary px-8" type="button" id="sendReplyBtn">إرسال الرد</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates for Chat Bubbles -->
    <div id="message-templates" class="d-none">
        <!-- Incoming (from teacher) -->
        <div class="template-incoming d-flex justify-content-start mb-10">
            <div class="d-flex flex-column align-items-start w-100">
                <div class="d-flex align-items-center mb-2">
                    <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" class="teacher-avatar" src="" />
                    </div>
                    <div class="ms-3">
                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-info me-1 teacher-name-label">Name</a>
                        <span class="text-muted fs-7 mb-1 msg-time">Time</span>
                    </div>
                </div>
                <div class="d-flex flex-row align-items-center chat-bubble-container">
                    <div class="p-5 rounded bg-light-info text-dark fw-semibold mw-lg-400px text-start position-relative bubble-content-wrapper" data-kt-element="message-text">
                        <div class="msg-title-header d-none">
                            <span class="fw-bold text-primary msg-title"></span>
                        </div>
                        <div class="msg-content"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Outgoing (from admin) -->
        <div class="template-outgoing d-flex justify-content-end mb-10">
            <div class="d-flex flex-column align-items-end w-100">
                <div class="d-flex align-items-center mb-2">
                    <div class="me-3 text-end">
                        <span class="text-muted fs-7 mb-1 msg-time">Time</span>
                        <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-info ms-1">
                            <span class="admin-name">Admin</span>
                            <span class="badge badge-light-primary admin-role-badge">Admin</span>
                        </a>
                    </div>
                    <div class="symbol symbol-35px symbol-circle">
                        <img alt="Pic" class="admin-avatar" src="" />
                    </div>
                </div>
                <div class="d-flex flex-row align-items-center chat-bubble-container">
                    <div class="p-5 rounded bg-light-primary text-dark fw-semibold mw-lg-400px text-end position-relative bubble-content-wrapper" data-kt-element="message-text">
                        <div class="msg-title-header d-none">
                            <span class="fw-bold text-info msg-title"></span>
                        </div>
                        <div class="msg-content mb-2" style="word-wrap: break-word;"></div>
                        <!-- Actions Inside the Bubble -->
                        <div class="mt-2 pt-2 border-top border-primary border-opacity-25 d-flex justify-content-end align-items-center gap-3">
                            <a href="javascript:void(0)" class="text-hover-danger fs-8 fw-bold text-gray-700 delete-msg-link"><i class="ki-duotone ki-trash pe-1 fs-7"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>حذف</a>
                            <a href="javascript:void(0)" class="text-hover-info fs-8 fw-bold text-gray-700 edit-msg-link"><i class="ki-duotone ki-pencil pe-1 fs-7"><span class="path1"></span><span class="path2"></span></i>تعديل</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        var currentTeacherId = null;
        var currentEncryptedId = null;
        var searchTimer = null;

        $(document).ready(function() {
            // Contact click handler
            $(document).on('click', '.contact-item', function(e) {
                if ($(e.target).closest('.contact-tools-btn, .menu').length) return;
                var id = $(this).data('id');
                loadChat(id, $(this));
            });

            // Send reply
            $('#sendReplyBtn').on('click', function() {
                sendReply();
            });

            // Search
            $('#contactSearch').on('keyup', function() {
                clearTimeout(searchTimer);
                var query = $(this).val();
                $('#searchLoader').removeClass('d-none');
                searchTimer = setTimeout(function() {
                    $.ajax({
                        url: '{{ route('teachers.messages') }}',
                        type: 'GET',
                        data: { search: query },
                        success: function(html) {
                            $('#contacts-container').html(html);
                            if (currentTeacherId) $('#contact-' + currentTeacherId).addClass('active');
                        },
                        complete: function() { $('#searchLoader').addClass('d-none'); }
                    });
                }, 500);
            });

            // Refresh
            $('#refreshChat').on('click', function() {
                if (currentTeacherId) loadChat(currentTeacherId, $('#contact-' + currentTeacherId));
            });

            // Delete message link
            $(document).on('click', '.delete-msg-link', function() {
                var container = $(this).closest('.template-incoming, .template-outgoing').find('.bubble-content-wrapper');
                var msgId = container.attr('data-id') || $(this).closest('[data-id]').attr('data-id');
                var type = $(this).closest('.template-incoming').length ? 'incoming' : 'outgoing';
                deleteMessage(msgId, type, $(this).closest('.mb-10'));
            });

            // Edit message link
            $(document).on('click', '.edit-msg-link', function() {
                var row = $(this).closest('.template-incoming, .template-outgoing');
                var container = row.find('.bubble-content-wrapper');
                var msgId = container.attr('data-id') || $(this).closest('[data-id]').attr('data-id');
                var type = row.hasClass('template-incoming') ? 'incoming' : 'outgoing';
                var contentElem = container.find('.msg-content');
                var currentTxt = contentElem.text();

                Swal.fire({
                    title: 'تعديل الرسالة',
                    input: 'textarea',
                    inputValue: currentTxt,
                    showCancelButton: true,
                    confirmButtonText: 'حفظ التعديل',
                    cancelButtonText: 'إلغاء',
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        $.post('{{ route('chat.edit.message') }}', {
                            id: msgId,
                            type: type,
                            content: result.value,
                            _token: '{{ csrf_token() }}'
                        }, function(res) {
                            if (res.status === 'success') {
                                contentElem.html(result.value.replace(/\n/g, '<br>'));
                                toastr.success(res.message);
                            } else {
                                toastr.error(res.message);
                            }
                        });
                    }
                });
            });

            // Archive deletion
            $('#tool-delete-all-msgs').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'حذف أرشيف الرسائل؟',
                    text: "لن تتمكن من التراجع عن هذه الخطوة!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f1416c',
                    confirmButtonText: 'نعم، احذف الكل',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('{{ route('teachers.messages.deleted') }}', {
                            id: currentTeacherId,
                            _token: '{{ csrf_token() }}'
                        }, function() {
                            toastr.success('تم حذف الأرشيف');
                            loadChat(currentTeacherId, $('.contact-item.active'));
                        });
                    }
                })
            });
        });

        function loadChat(id, element) {
            currentTeacherId = id;
            $('.contact-item').removeClass('active');
            element.addClass('active');
            element.find('.badge-light-success').removeClass('badge-light-success').addClass('badge-light-primary').text('✔'); // indicate read
            element.find('.symbol-badge').addClass('d-none'); // Hide unread dot

            $('#messages-container').empty().addClass('d-none');
            $('.empty-chat-state').find('h3').text('جارِ تحميل المحادثة...');
            $('.empty-chat-state').find('img').addClass('opacity-50');
            $('.empty-chat-state').show();
            $('#kt_chat_messenger_header').addClass('d-none');
            $('#kt_chat_messenger_footer').addClass('d-none');

            $.ajax({
                url: '{{ url('admin/teachers/chat/history') }}/' + id,
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        currentEncryptedId = response.teacher.encrypted_id;
                        renderChat(response);
                        updateTools(response.teacher);
                    } else { toastr.error('فشل في تحميل المحادثة'); }
                },
                error: function() { toastr.error('حدث خطأ أثناء تحميل البيانات'); }
            });
        }

        function renderChat(data) {
            var teacher = data.teacher;
            var history = data.history;

            $('#active-teacher-name').text(teacher.name);
            $('#active-teacher-status-text').text(teacher.status);
            $('#active-teacher-status-indicator').removeClass('bg-success bg-danger').addClass('bg-' + teacher.status_class);

            $('#kt_chat_messenger_header').removeClass('d-none');
            $('#kt_chat_messenger_footer').removeClass('d-none');
            $('.empty-chat-state').hide();
            $('#messages-container').removeClass('d-none');

            var container = $('#messages-container');
            container.empty();

            history.forEach(function(msg) {
                var template;
                if (msg.type === 'incoming') {
                    template = $('#message-templates .template-incoming').clone();
                    template.find('.teacher-name-label').text(teacher.name);
                    if (teacher.image) {
                        template.find('.teacher-avatar').attr('src', teacher.image);
                    } else {
                        template.find('.symbol').html('<span class="symbol-label bg-light-success text-success fs-6 fw-bold">' + teacher.initial + '</span>');
                    }
                } else {
                    template = $('#message-templates .template-outgoing').clone();
                    template.find('.admin-name').text(msg.sender || 'Admin');
                    template.find('.admin-role-badge').text(msg.admin_role || 'إدارة');
                    if (msg.admin_image) {
                        template.find('.admin-avatar').attr('src', msg.admin_image);
                    } else {
                        template.find('.symbol').html('<span class="symbol-label bg-light-info text-info fs-6 fw-bold">' + (msg.admin_initial || 'A') + '</span>');
                    }
                }

                template.find('.bubble-content-wrapper').attr('data-id', msg.id);
                template.find('.msg-time').text(msg.human_date).attr('title', msg.created_at);
                if (msg.title) {
                    template.find('.msg-title-header').removeClass('d-none');
                    template.find('.msg-title').text(msg.title);
                }
                template.find('.msg-content').html(msg.content.replace(/\n/g, '<br>'));
                container.append(template);
            });

            setTimeout(function() {
                container.scrollTop(container[0].scrollHeight);
                KTMenu.createInstances();
            }, 100);
        }

        function updateTools(teacher) {
            $('#tool-edit-teacher').attr('href', '{{ url('admin/teachers/edit') }}/' + teacher.encrypted_id);
            $('#tool-change-password').attr('href', '{{ url('admin/teachers/password') }}/' + teacher.encrypted_id);
        }

        function deleteMessage(id, type, element) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سوف يتم حذف هذه الرسالة نهائياً!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذفها',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('{{ route('chat.delete.message') }}', { id: id, type: type, _token: '{{ csrf_token() }}' }, function(res) {
                        if (res.status === 'success') {
                            element.fadeOut(300, function() { $(this).remove(); });
                            toastr.success(res.message);
                        } else { toastr.error(res.message); }
                    });
                }
            });
        }

        function sendReply() {
            var message = $('#replyTextarea').val();
            if (!message.trim()) { toastr.warning('يرجى كتابة نص الرد'); return; }

            var btn = $('#sendReplyBtn');
            btn.attr('disabled', true).text('جارِ الإرسال...');

            $.ajax({
                url: '{{ route('send.teacher.message') }}',
                type: 'POST',
                data: {
                    id: currentTeacherId,
                    title: 'رد من الإدارة',
                    message: message,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success('تم إرسال الرد بنجاح');
                        $('#replyTextarea').val('');
                        loadChat(currentTeacherId, $('.contact-item.active'));
                    } else { toastr.error(response.message); }
                },
                error: function() { toastr.error('حدث خطأ أثناء الإرسال'); },
                complete: function() { btn.attr('disabled', false).text('إرسال الرد'); }
            });
        }
    </script>
@stop
