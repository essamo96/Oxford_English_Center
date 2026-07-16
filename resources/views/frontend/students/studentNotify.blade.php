<div class="info-card animate__animated animate__fadeIn">
    <div class="comm-header">
        <h3 class="m-0"><i class="fa fa-comments-o"></i> Communication Center</h3>
        <div class="header-pills">
            <ul class="nav nav-pills modern-pills">
                <li class="active"><a href="#AdminChat" data-toggle="tab"><i class="fa fa-user-shield"></i> Admin Inbox</a></li>
                <li><a href="#GroupChatCenter" data-toggle="tab"><i class="fa fa-users"></i> Group Chat</a></li>
                <li><a href="#SystemNotices" data-toggle="tab"><i class="fa fa-bullhorn"></i> Notices</a></li>
            </ul>
        </div>
    </div>

    <div class="tab-content no-bg no-padding">
        <!-- Admin Correspondence Tab -->
        <div class="tab-pane fade active in" id="AdminChat">
            <div class="d-flex justify-content-between align-items-center mb-20">
                <h4 class="text-muted m-0">Direct Support Tickets</h4>
                <button class="btn new-inquiry-btn" onclick="openNewInquiryModal()">
                    <i class="fa fa-plus-circle"></i> New Inquiry
                </button>
            </div>

            <div class="admin-chat-history custom-scrollbar" style="max-height: 550px; overflow-y: auto;">
                @forelse ($admin_messages as $msg)
                    <div class="ticket-item animate-up">
                        <div class="ticket-header">
                            <div>
                                <span class="ticket-status {{ $msg->note ? 'status-replied' : 'status-pending' }}">
                                    {{ $msg->note ? 'Replied' : 'Pending' }}
                                </span>
                                <h5 class="mt-10 mb-5" style="font-weight: 700;">{{ $msg->title }}</h5>
                            </div>
                            <span class="msg-time">{{ $msg->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="ticket-content">
                            {{ $msg->content }}
                        </div>
                        @if($msg->note)
                            <div class="admin-reply-box">
                                <h6><i class="fa fa-reply"></i> Official Response</h6>
                                <p class="m-0 text-white">{{ $msg->note }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center p-50">
                        <div class="empty-state-icon">
                            <i class="fa fa-envelope-open-o fa-4x text-muted mb-20"></i>
                        </div>
                        <h4 class="text-muted">No support tickets found.</h4>
                        <p class="text-muted small">Need help? Click 'New Inquiry' to contact administration.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Group Chat Center Tab -->
        <div class="tab-pane fade" id="GroupChatCenter">
            <div class="chat-window">
                <div class="chat-header p-15 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-10">
                        <i class="fa fa-users text-accent"></i>
                        <select id="chat-group-id" class="form-control modern-input" style="width: 250px; height: 35px; font-size: 16.2px;">
                            @foreach($student_groups as $sg)
                                @if($sg->group)
                                    <option value="{{ $sg->group_id }}">{{ optional($sg->group->program)->title ?? 'N/A' }} - {{ $sg->group->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <span class="small text-muted">Real-time collaboration</span>
                </div>

                <div id="group-chat-messages" class="chat-messages-area custom-scrollbar">
                    @forelse ($group_messages as $message)
                        @php
                            $is_teacher = $message->user_type == 1;
                            $is_me = ($message->user_type == 0 && $message->from_user == Auth::guard('students')->id());
                        @endphp
                        <div class="chat-message {{ $is_me ? 'me' : ($is_teacher ? 'teacher' : 'other') }}">
                            <div class="message-bubble">
                                <div class="sender-name-label">
                                    {{ $is_teacher ? ($message->chatGroup ? ($message->chatGroup->teacher->name ?? 'Teacher') : 'Teacher') : $message->name }}
                                </div>
                                <p>{{ $message->content }}</p>
                                <span class="message-time">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-50">
                            <i class="fa fa-comments-o fa-3x text-muted mb-15"></i>
                            <p class="text-muted">Start the conversation!</p>
                        </div>
                    @endforelse
                </div>

                <div class="chat-input-wrapper">
                    <div class="input-group">
                        <textarea id="group-msg-input" class="form-control modern-input" rows="1" placeholder="Type a message to your classmates..."></textarea>
                        <span class="input-group-btn">
                            <button id="btn-send-group-msg" class="btn btn-accent" style="height: 45px; border-radius: 0 10px 10px 0;">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Notices Tab -->
        <div class="tab-pane fade" id="SystemNotices">
            <div class="notices-grid">
                @forelse ($notifys as $notify)
                    <div class="modern-notice {{ ($notify->read_at) ? '' : 'unread' }} animate-up">
                        <div class="notice-icon-box">
                            <i class="fa {{ ($notify->data['type'] ?? '') == 'exam' ? 'fa-file-text' : 'fa-bell-o' }}"></i>
                        </div>
                        <div class="notice-body flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="m-0 text-white" style="font-weight: 700;">{{ $notify->data['title'] ?? 'System Update' }}</h5>
                                <span class="small text-muted">{{ $notify->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-5 mb-10 text-muted">{{ $notify->data['body'] ?? '' }}</p>
                            <div class="notice-footer d-flex justify-content-between align-items-center">
                                <span class="small text-accent"><i class="fa fa-user-circle"></i> {{ $notify->data['sender_name'] ?? 'Admin' }}</span>
                                <button class="btn btn-link btn-xs p-0 text-muted">Mark as read</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-50">
                        <i class="fa fa-bell-slash-o fa-4x text-muted mb-20"></i>
                        <p class="text-muted">No important notifications today.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function openNewInquiryModal() {
    Swal.fire({
        title: '<h3 style="color:white; margin-bottom:10px;">Contact Administration</h3>',
        html: `
            <div style="text-align: left;">
                <label style="color:#a0aec0; font-size: 15.2px; margin-bottom:5px; display:block;">Subject</label>
                <input id="swal-title" class="swal2-input swal2-modern-input" style="margin-top:0; width:100%; box-sizing:border-box;" placeholder="Enter message subject...">
                <label style="color:#a0aec0; font-size: 15.2px; margin-top:15px; margin-bottom:5px; display:block;">Message Content</label>
                <textarea id="swal-content" class="swal2-textarea swal2-modern-textarea" style="margin-top:0; width:100%; box-sizing:border-box;" rows="5" placeholder="Type your detailed inquiry here..."></textarea>
            </div>
        `,
        background: '#1a2744',
        showCancelButton: true,
        confirmButtonText: 'Send Message',
        confirmButtonColor: '#f5c518',
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#4a5568',
        focusConfirm: false,
        preConfirm: () => {
            const title = Swal.getPopup().querySelector('#swal-title').value;
            const content = Swal.getPopup().querySelector('#swal-content').value;
            if (!title || !content) {
                Swal.showValidationMessage(`Please fill in both subject and content`);
            }
            return { title: title, content: content }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sending...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ route('student.send_admin_message') }}",
                type: "POST",
                data: {
                    title: result.value.title,
                    content: result.value.content,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        background: '#1a2744',
                        color: '#fff',
                        timer: 2000
                    });
                    // Reload content
                    $('.AdminNotify').click();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Failed to send message. Please try again.',
                        background: '#1a2744',
                        color: '#fff'
                    });
                }
            });
        }
    });
}

$(document).ready(function() {
    // Group Message Sending
    $('#btn-send-group-msg').on('click', function() {
        var msg = $('#group-msg-input').val();
        var groupId = $('#chat-group-id').val();
        
        if(!msg.trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Message',
                text: 'Please type something before sending!',
                background: '#1a2744',
                color: '#fff',
                timer: 2000
            });
            return;
        }
        
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('student.postSendMessage') }}",
            type: "POST",
            data: {
                message: msg,
                to_user: groupId,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                $('#group-msg-input').val('');
                $('.AdminNotify').click();
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i>');
            }
        });
    });

    // Enter key to send
    $('#group-msg-input').on('keypress', function(e) {
        if(e.which == 13 && !e.shiftKey) {
            e.preventDefault();
            $('#btn-send-group-msg').click();
        }
    });

    var chatBox = document.getElementById('group-chat-messages');
    if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
});
</script>
