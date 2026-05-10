@extends('frontend.layouts.master')
@section('title', 'Teacher Area')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/student-dashboard.css') }}">
<link href="{{ asset('assets/admin/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .input-group-btn {
        background-color: #f0f0f0;
        border-radius: 0 5px 5px 0;
    }
    /* Teacher specific overrides if any */
    .status-badge.status-active { background: #28a745; color: white; }
    .status-badge.status-delayed { background: #f5222d; color: white; }
    
    .btn-stop-group {
        background: rgba(245, 34, 45, 0.1);
        color: #f5222d;
        border: 1px solid rgba(245, 34, 45, 0.2);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        transition: all 0.3s;
    }
    .btn-stop-group:hover {
        background: #f5222d;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="student-dashboard-wrapper">
    <div id="bg-particles"></div>
    <div class="container" style="position: relative; z-index: 1;">
        <!-- Dashboard Header Card -->
        <div class="dashboard-header">
            <div class="header-content">
                @if ($teacher_info->image != '')
                    <img src="{{ asset($teacher_info->image) }}" class="student-avatar-large" alt="{{ $teacher_info->name }}" />
                @else
                    <img src="{{ url('assets/oxford/img/students/avatar.png') }}" class="student-avatar-large" alt="Avatar" />
                @endif
                
                <div class="header-info">
                    <p>Welcome back, Teacher</p>
                    <h1>{{ $teacher_info->name ?: 'Oxford Instructor' }}</h1>
                    <div class="status-badge {{ $teacher_info->status != 0 ? 'status-active' : 'status-delayed' }}">
                        {{ $teacher_info->status != 0 ? 'Active Instructor' : 'Inactive' }}
                    </div>
                </div>

                <div class="header-actions" style="margin-left: auto; display: flex; gap: 10px; align-items: center;">
                    <!-- Exam Data Link -->
                    <a href="{{ route('teacher.ExamDates', $teacher_id) }}" class="header-action-btn" title="Exam Data">
                        <i class="fa fa-file-text-o fa-lg"></i>
                        <span class="action-label">Exam Data</span>
                    </a>
                    
                    <!-- Progress Quick Link -->
                    <a href="{{ route('teacher.progress', $teacher_info->id) }}" class="header-action-btn" title="My Progress">
                        <i class="fa fa-line-chart fa-lg"></i>
                        <span class="action-label">Progress</span>
                    </a>

                    <!-- Notifications -->
                    <a href="#AdminNotify" class="AdminNotify header-action-btn" data-toggle="tab" title="Messages" style="position: relative;">
                        <i class="fa fa-envelope-o fa-lg"></i>
                        @if($count > 0)
                            <span class="badge badge-danger unread-badge animate__animated animate__heartBeat animate__infinite" style="position: absolute; top: -5px; right: -5px; font-size: 10px;">{{ $count }}</span>
                        @endif
                    </a>

                    <!-- Quick Message -->
                    <a href="javascript:void(0)" id="dialog-btn" class="header-action-btn" title="Send Message to Admin">
                        <i class="fa fa-paper-plane-o fa-lg"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            @include('frontend.layouts.error')
            @include('frontend.chat.chat-box')
            <input type="hidden" id="current_user" value="{{ \Auth::user()->id }}" />
            <input type="hidden" id="current_group" value="{{ $groups_array }}" />
            <input type="hidden" id="user_type" value="teacher" />
            <input type="hidden" id="pusher_app_key" value="{{ env('PUSHER_APP_KEY') }}" />
            <input type="hidden" id="pusher_cluster" value="{{ env('PUSHER_APP_CLUSTER') }}" />
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="dashboard-content tab-content">
                    <!-- Welcome Tab -->
                    <div class="tab-pane fade active in" id="Welcome">
                        <div class="row">
                            <!-- Professional Profile Tiles -->
                            <div class="col-md-12 mb-30 animate-up delay-1">
                                <h3 class="mb-20"><i class="fa fa-graduation-cap"></i> Professional Profile</h3>
                                <div class="welcome-grid">
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-user"></i></div>
                                        <div class="tile-content">
                                            <h4>Full Name</h4>
                                            <p>{{ $teacher_info->name }}</p>
                                        </div>
                                    </div>
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-id-card"></i></div>
                                        <div class="tile-content">
                                            <h4>Instructor ID</h4>
                                            <p>#{{ $teacher_info->id }}</p>
                                        </div>
                                    </div>
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-calendar"></i></div>
                                        <div class="tile-content">
                                            <h4>Join Date</h4>
                                            <p>{{ $teacher_info->join_date ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-check-circle"></i></div>
                                        <div class="tile-content">
                                            <h4>Status</h4>
                                            <p><span class="badge {{ $teacher_info->status != 0 ? 'status-active' : 'status-delayed' }}">{{ $teacher_info->status != 0 ? 'Active' : 'Inactive' }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Details Tiles -->
                            <div class="col-md-12 mb-30 animate-up delay-2">
                                <h3 class="mb-20"><i class="fa fa-phone"></i> Contact Details</h3>
                                <div class="welcome-grid">
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-envelope"></i></div>
                                        <div class="tile-content">
                                            <h4>Email Address</h4>
                                            <p>{{ $teacher_info->email }}</p>
                                        </div>
                                    </div>
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-mobile-phone"></i></div>
                                        <div class="tile-content">
                                            <h4>Phone</h4>
                                            <p>{{ $teacher_info->mobile }}</p>
                                        </div>
                                    </div>
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-birthday-cake"></i></div>
                                        <div class="tile-content">
                                            <h4>Date of Birth</h4>
                                            <p>{{ $teacher_info->dob }}</p>
                                        </div>
                                    </div>
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-briefcase"></i></div>
                                        <div class="tile-content">
                                            <h4>Role</h4>
                                            <p>Senior Instructor</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Access & Pending Actions Section -->
                        <div class="row mt-30 animate-up delay-3">
                            <div class="col-md-7">
                                <div class="info-card" style="background: var(--bg-light); border-left: 5px solid var(--accent);">
                                    <h3 class="mb-20"><i class="fa fa-rocket"></i> Quick Actions</h3>
                                    <div class="quick-access-grid">
                                        <a href="#Courses" data-toggle="tab" class="btn-modern btn-modern-primary">
                                            <i class="fa fa-book"></i> My Courses
                                        </a>
                                        <a href="{{ route('teacher.progress', $teacher_info->id) }}" class="btn-modern btn-modern-accent">
                                            <i class="fa fa-bar-chart"></i> View Progress
                                        </a>
                                        <a href="{{ route('teacher.ExamDates', $teacher_id) }}" class="btn-modern btn-modern-primary" style="background: #2d3748;">
                                            <i class="fa fa-calendar"></i> Exam Schedule
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Proactive To-Do Widget -->
                            <div class="col-md-5 mt-xs-30">
                                <div class="info-card" style="border-top: 4px solid var(--danger); padding: 20px;">
                                    <h3 class="mb-15" style="font-size: 16px;"><i class="fa fa-bell-o text-danger"></i> Pending Actions</h3>
                                    <ul style="list-style: none; padding: 0; margin: 0; max-height: 180px; overflow-y: auto;">
                                        @if($count > 0)
                                        <li style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <i class="fa fa-envelope-o text-warning" style="margin-right: 8px;"></i>
                                            <span style="font-size: 13px;">You have <strong>{{ $count }}</strong> unread messages</span>
                                            <a href="#AdminNotify" data-toggle="tab" class="pull-right" style="font-size: 12px; color: var(--primary);">View</a>
                                        </li>
                                        @endif
                                        <li style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <i class="fa fa-exclamation-circle text-danger" style="margin-right: 8px;"></i>
                                            <span style="font-size: 13px;">Update attendance for active groups</span>
                                            <a href="#Courses" data-toggle="tab" class="pull-right" style="font-size: 12px; color: var(--primary);">Review</a>
                                        </li>
                                        <li style="padding: 10px 0;">
                                            <i class="fa fa-calendar-times-o text-danger" style="margin-right: 8px;"></i>
                                            <span style="font-size: 13px;">Check missing exam schedules</span>
                                            <a href="{{ route('teacher.ExamDates', $teacher_id) }}" class="pull-right" style="font-size: 12px; color: var(--primary);">Fix</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Courses Tab -->
                    <div class="tab-pane fade" id="Courses">
                        <div class="d-flex justify-content-between align-items-center mb-30">
                            <h3 class="m-0"><i class="fa fa-book"></i> Assigned Courses & Groups</h3>
                        </div>
                        
                        <div class="course-grid">
                            @foreach ($groups as $group)
                            <div class="course-card">
                                <div class="course-img-wrapper">
                                    <img src="{{ $group->image ? url($group->image) : url('assets/oxford/img/logo.png') }}" class="course-img" alt="Course">
                                    <div class="course-overlay">
                                        <a href="#Info" data-toggle="tab" class="joinG btn-join-circle" 
                                           data-group_id="{{ Crypt::encrypt($group->id) }}" 
                                           data-teacher_id="{{ Crypt::encrypt($group->teacher_id) }}">
                                            <i class="fa fa-external-link"></i>
                                            <span>Manage</span>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="course-body">
                                    <div class="d-flex justify-content-between align-items-start mb-10">
                                        <h4 class="course-title">{{ $group->program->title }}</h4>
                                        <button class="btn-stop-group staus_course" 
                                                data-end-date="{{ $group->end_date }}" 
                                                data-group-id="{{ $group->id }}">
                                            <i class="fa fa-stop-circle"></i> Close
                                        </button>
                                    </div>
                                    
                                    <div class="course-meta">
                                        <div class="meta-item" title="Group Name">
                                            <i class="fa fa-tag"></i> {{ $group->name }}
                                        </div>
                                        <div class="meta-item" title="Duration">
                                            <i class="fa fa-clock-o"></i> 3 Months
                                        </div>
                                        @if($group->ctime)
                                        <div class="meta-item" title="Schedule">
                                            <i class="fa fa-calendar-check-o"></i> {{ $group->ctime->times }}
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <div class="progress-info d-flex justify-content-between small">
                                            <span>Group Progress</span>
                                            <span>
                                                @if ($group->progress == 30 || $group->progress == null) Units 1-3
                                                @elseif ($group->progress == 60) Units 4-6
                                                @elseif ($group->progress == 90) Units 7-9
                                                @else Final Units @endif
                                                ({{ $group->progress ?: 0 }}%)
                                            </span>
                                        </div>
                                        <div class="custom-progress">
                                            <div class="progress-fill" style="width: {{ $group->progress ?: 0 }}%"></div>
                                        </div>

                                        <div class="d-flex gap-10">
                                            <a href="#Info" data-toggle="tab" class="joinG btn btn-sm btn-primary flex-grow-1" 
                                               data-group_id="{{ Crypt::encrypt($group->id) }}" 
                                               data-teacher_id="{{ Crypt::encrypt($group->teacher_id) }}">
                                                <i class="fa fa-users"></i> Students & Marks
                                            </a>
                                            <a href="javascript:void(0);" data-id="{{ $group->id }}" data-user="{{ $group->name }}" 
                                               class="chat-toggle btn btn-sm btn-outline-light" title="Group Chat">
                                                <i class="fa fa-comments"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Profile Tab -->
                    <div class="tab-pane fade" id="Profile">
                        <div class="info-card">
                            <h3 class="mb-30"><i class="fa fa-user-circle"></i> Professional Profile Settings</h3>
                            <form method="post" enctype="multipart/form-data" action="/student/editProfile">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Modern Avatar Upload -->
                                        <div class="profile-avatar-edit">
                                            <div class="avatar-preview-wrapper">
                                                @if ($teacher_info->image != '')
                                                    <img id="profilePreview" src="{{ url($teacher_info->image) }}" alt="Profile" />
                                                @else
                                                    <img id="profilePreview" src="{{ url('assets/oxford/img/students/avatar.png') }}" alt="Avatar" />
                                                @endif
                                            </div>
                                            <label for="fileToUpload" class="avatar-edit-overlay" title="Change Photo">
                                                <i class="fa fa-camera"></i>
                                            </label>
                                            <input type="file" name="fileToUpload" id="fileToUpload" style="display: none;" onchange="document.getElementById('profilePreview').src = window.URL.createObjectURL(this.files[0])">
                                        </div>
                                        
                                        <div class="text-center mb-30">
                                            <p class="text-muted small">Click the camera icon to update your professional headshot.</p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="modern-form-grid">
                                            <div class="form-group-modern">
                                                <label>Full Name</label>
                                                <input class="form-control-modern" id="name" value="{{ $teacher_info->name }}" type="text" readonly style="background: #f1f3f5;">
                                            </div>
                                            
                                            <div class="form-group-modern">
                                                <label>Mobile Number</label>
                                                <input class="form-control-modern" id="mobile" value="{{ $teacher_info->mobile }}" type="text" readonly style="background: #f1f3f5;">
                                            </div>
                                            
                                            <div class="form-group-modern">
                                                <label>Birth Date</label>
                                                <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                                    <input type="text" class="form-control-modern" name="dob" value="{{ $teacher_info->dob }}">
                                                    <span class="input-group-btn">
                                                        <button class="btn default" type="button" style="border:none; background:transparent;"><i class="fa fa-calendar"></i></button>
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group-modern">
                                                <label>Email Address</label>
                                                <input class="form-control-modern" name="email" value="{{ $teacher_info->email }}" type="email" placeholder="yourname@example.com">
                                            </div>
                                        </div>

                                        <div class="mt-30 text-right">
                                            <button class="btn btn-success btn-lg" type="submit">
                                                <i class="fa fa-save"></i> Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Password Tab -->
                    <div class="tab-pane fade" id="Password">
                        <div class="info-card">
                            <h3 class="mb-30"><i class="fa fa-lock"></i> Security & Password</h3>
                            <form method="post" action="">
                                {{ csrf_field() }}
                                <div class="modern-form-grid">
                                    <div class="form-group-modern" style="grid-column: span 2;">
                                        <label>New Password</label>
                                        <input class="form-control-modern" type="password" name="npassword" placeholder="Enter new password">
                                    </div>
                                    <div class="form-group-modern" style="grid-column: span 2;">
                                        <label>Repeat New Password</label>
                                        <input class="form-control-modern" type="password" name="rpassword" placeholder="Confirm new password">
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <button class="btn btn-primary btn-lg" type="submit">
                                        <i class="fa fa-shield"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Dynamic Content Panes -->
                    <div class="tab-pane fade" id="Info"></div>
                    <div class="tab-pane fade" id="AdminNotify"></div>
                    <div class="tab-pane fade" id="GroupsStudents"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal fade" id="ajax1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-body">
                <img src="{{ url('assets/oxford/img/preloader.gif') }}" alt="" class="loading">
                <span> &nbsp;&nbsp;Loading content... </span>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('assets/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        $('.date-picker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });
</script>
<script>
    var base_url = '{{ url('/') }}';
</script>
<script src="https://js.pusher.com/4.1/pusher.min.js"></script>
<script src="{{ url('assets/oxford/js/chat.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('.userinfo').click(function() {
            var url = $(this).attr('href');
            $.ajax({
                url: url,
                type: 'get',
                success: function(response) {
                    $('.modal-content').html(response);
                }
            });
        });
        
        $(document).on('click', ".joinG", function() {
            var teacher_id = $(this).data("teacher_id");
            var group_id = $(this).data("group_id");
            var $container = $('#Info');
            $container.html('<div class="text-center p-50"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-10">Loading group details...</p></div>');
            
            $.ajax({
                type: "POST",
                url: "{{ route('teacher.showGroue_info') }}",
                data: {
                    'teacher_id': teacher_id,
                    'group_id': group_id,
                    "_token": "{{ csrf_token() }}"
                }
            }).success(function(data) {
                $container.html(data);
            });
        });

        $(document).on('click', ".AdminNotify", function() {
            var $container = $('#AdminNotify');
            $container.html('<div class="text-center p-50"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-10">Loading messages...</p></div>');
            
            $.ajax({
                type: "POST",
                url: "{{ route('teacher.notifications') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                }
            }).success(function(data) {
                $container.html(data);
                $('.unread-badge').fadeOut();
            });
        });

        // Stop Group Logic
        $(document).on('click', '.staus_course', function() {
            var endDate = $(this).data('end-date');
            var groupId = $(this).data('group-id');
            
            Swal.fire({
                title: 'Confirm Group Closure',
                text: 'Are you sure you want to stop this group? This action will mark the course as finished.',
                icon: 'warning',
                background: '#1a2744',
                color: '#fff',
                showCancelButton: true,
                confirmButtonColor: '#f5222d',
                cancelButtonColor: '#2d3748',
                confirmButtonText: 'Yes, Close Group',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'teacher/change/status',
                        type: 'POST',
                        data: {
                            end_date: endDate,
                            group_id: groupId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success',
                                text: 'Group has been successfully closed.',
                                icon: 'success',
                                background: '#1a2744',
                                color: '#fff'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'Something went wrong. Please try again.',
                                icon: 'error',
                                background: '#1a2744',
                                color: '#fff'
                            });
                        }
                    });
                }
            });
        });

        // Message to Admin
        document.getElementById('dialog-btn').addEventListener('click', function() {
            Swal.fire({
                title: '<i class="fa fa-envelope-o"></i> Send Message to Admin',
                background: '#1a2744',
                color: '#fff',
                html: `
                    <div class="text-left" style="padding: 10px;">
                        <div class="form-group">
                            <label for="message-title" style="color:#f5c518; font-weight: 700;">Subject:</label>
                            <input type="text" class="form-control" id="message-title" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px;">
                        </div>
                        <div class="form-group">
                            <label for="message-body" style="color:#f5c518; font-weight: 700;">Message Content:</label>
                            <textarea class="form-control" id="message-body" rows="4" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px;"></textarea>
                        </div>
                    </div>`,
                confirmButtonColor: '#f5c518',
                confirmButtonText: 'Send Message',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                preConfirm: function() {
                    var title = Swal.getPopup().querySelector('#message-title').value;
                    var body = Swal.getPopup().querySelector('#message-body').value;
                    if (!title || !body) {
                        Swal.showValidationMessage('Please fill in both fields');
                        return false;
                    }
                    return $.ajax({
                        url: 'teachers/admin/messages',
                        method: 'POST',
                        data: JSON.stringify({
                            title: title,
                            body: body,
                        }),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        contentType: 'application/json',
                        dataType: 'json'
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(function(result) {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Message Sent!',
                        text: 'Admin will review your message shortly.',
                        background: '#1a2744',
                        color: '#fff'
                    });
                }
            });
        });
    });
</script>

<!-- Particles.js Library -->
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof particlesJS !== 'undefined') {
            particlesJS('bg-particles', {
                "particles": {
                    "number": { "value": 40, "density": { "enable": true, "value_area": 800 } },
                    "color": { "value": ["#ffffff", "#f5c518", "#3182ce"] },
                    "shape": { "type": "circle" },
                    "opacity": { "value": 0.5, "random": true },
                    "size": { "value": 6, "random": true, "anim": { "enable": true, "speed": 2, "size_min": 2, "sync": false } },
                    "line_linked": { "enable": false },
                    "move": { "enable": true, "speed": 1.2, "direction": "none", "random": true, "straight": false, "out_mode": "out" }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": { "onhover": { "enable": true, "mode": "bubble" }, "onclick": { "enable": true, "mode": "push" }, "resize": true },
                    "modes": { "bubble": { "distance": 200, "size": 12, "duration": 2, "opacity": 0.8, "speed": 3 }, "push": { "particles_nb": 4 } }
                },
                "retina_detect": true
            });
        }
    });
</script>

<script>
    // Auto-activate tab based on URL hash (e.g. /teacher#Courses)
    $(document).ready(function() {
        var hash = window.location.hash;
        if (hash) {
            // Remove the '#' and activate the matching tab
            var tabId = hash.replace('#', '');
            var $tabLink = $('[href="#' + tabId + '"][data-toggle="tab"]');
            if ($tabLink.length) {
                $tabLink.tab('show');
                // Smooth scroll to top so the tab content is visible
                $('html, body').animate({ scrollTop: 0 }, 300);
            }
        }

        // Update URL hash when tab changes (for bookmarking)
        $('[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr('href');
            if (target && target.startsWith('#')) {
                history.replaceState(null, null, target);
            }
        });
    });
</script>
@endsection
