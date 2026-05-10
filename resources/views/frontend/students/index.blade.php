@extends('frontend.layouts.master')
@section('title', 'Student Area')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/student-dashboard.css') }}">
<link href="{{ asset('assets/admin/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .input-group-btn {
        background-color: #f0f0f0;
        border-radius: 0 5px 5px 0;
    }
</style>
@endsection

@section('content')
<div class="student-dashboard-wrapper">
    <div class="container">
        <!-- Dashboard Header Card -->
        <div class="dashboard-header">
            <div id="particles-js"></div>
            <div class="header-content">
                @if ($student_info->image != '')
                    <img src="{{ asset($student_info->image) }}" class="student-avatar-large" alt="{{ $student_info->name }}" />
                @else
                    <img src="{{ url('assets/oxford/img/students/avatar.png') }}" class="student-avatar-large" alt="Avatar" />
                @endif
                
                <div class="header-info">
                    <p>Welcome back,</p>
                    <h1>{{ $student_info->name ?: 'Oxford Student' }}</h1>
                    <div class="status-badge {{ $student_info->delaying != 0 ? 'status-delayed' : 'status-active' }}">
                        {{ $student_info->delaying != 0 ? 'Delayed' : 'Active Student' }}
                    </div>
                </div>

                <div class="header-actions" style="margin-left: auto; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <!-- Print Dashboard -->
                    <button onclick="window.print()" class="header-action-btn btn-print-dashboard" title="Print Dashboard">
                        <i class="fa fa-print fa-lg"></i>
                        <span class="action-label">Print</span>
                    </button>

                    <!-- Marks Quick Link -->
                    <a href="#StudentGroupsMarks" data-student_id="{{ Crypt::encrypt($student_info->id) }}" class="Markstudent header-action-btn" data-toggle="tab" title="My Marks">
                        <i class="fa fa-check-square-o fa-lg"></i>
                        <span class="action-label">Marks</span>
                    </a>
                    
                    <!-- Progress Quick Link -->
                    <a href="#StudentGroupsProgress" data-student_id="{{ Crypt::encrypt($student_info->id) }}" class="StudentGroupsProgress header-action-btn" data-toggle="tab" title="My Progress">
                        <i class="fa fa-line-chart fa-lg"></i>
                        <span class="action-label">Progress</span>
                    </a>

                    <!-- Exam Dates Quick Link -->
                    <a href="#Exam" data-toggle="tab" class="header-action-btn" title="Exam Dates">
                        <i class="fa fa-calendar fa-lg"></i>
                        <span class="action-label">Exams</span>
                    </a>

                    <!-- Notifications -->
                    <a href="#AdminNotify" class="AdminNotify header-action-btn" data-toggle="tab" title="Messages" style="position: relative;">
                        <i class="fa fa-envelope-o fa-lg"></i>
                        @if($count > 0)
                            <span class="badge badge-danger unread-badge animate__animated animate__heartBeat animate__infinite">{{ $count }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            @include('frontend.layouts.error')
            @include('frontend.chat.chat-box')
            <input type="hidden" id="current_user" value="{{ \Auth::user()->id }}" />
            <input type="hidden" id="current_group" value="{{ $groups_array }}" />
            <input type="hidden" id="user_type" value="student" />
            <input type="hidden" id="pusher_app_key" value="{{ env('PUSHER_APP_KEY') }}" />
            <input type="hidden" id="pusher_cluster" value="{{ env('PUSHER_APP_CLUSTER') }}" />
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="dashboard-content tab-content">
                    <div class="tab-pane fade active in" id="Welcome">
                        <div class="row">
                            <!-- Academic Profile Tiles -->
                            <div class="col-md-12 mb-30 animate-up delay-1">
                                <h3 class="mb-20"><i class="fa fa-graduation-cap"></i> Academic Profile</h3>
                                <div class="welcome-grid">
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-user"></i></div>
                                        <div class="tile-content">
                                            <h4>Full Name</h4>
                                            <p>{{ $student_info->name }}</p>
                                        </div>
                                    </div>
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-id-card"></i></div>
                                        <div class="tile-content">
                                            <h4>Student ID</h4>
                                            <p>#{{ $student_info->id }}</p>
                                        </div>
                                    </div>
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-calendar"></i></div>
                                        <div class="tile-content">
                                            <h4>Join Date</h4>
                                            <p>{{ $student_info->join_date ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-check-circle"></i></div>
                                        <div class="tile-content">
                                            <h4>Status</h4>
                                            <p><span class="badge {{ $student_info->delaying != 0 ? 'status-delayed' : 'status-active' }}">{{ $student_info->delaying != 0 ? 'Delayed' : 'Active' }}</span></p>
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
                                            <p>{{ $student_info->email }}</p>
                                        </div>
                                    </div>
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-mobile-phone"></i></div>
                                        <div class="tile-content">
                                            <h4>Phone</h4>
                                            <p>{{ $student_info->mobile }}</p>
                                        </div>
                                    </div>
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-birthday-cake"></i></div>
                                        <div class="tile-content">
                                            <h4>Date of Birth</h4>
                                            <p>{{ $student_info->dob }}</p>
                                        </div>
                                    </div>
                                    <div class="info-tile">
                                        <div class="tile-icon"><i class="fa fa-briefcase"></i></div>
                                        <div class="tile-content">
                                            <h4>Major / Job</h4>
                                            <p>{{ $student_info->job }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Access Section -->
                        <div class="row mt-30 animate-up delay-3">
                            <div class="col-md-12">
                                <div class="info-card" style="background: rgba(245, 197, 24, 0.05); border-left: 5px solid var(--accent);">
                                    <h3 class="mb-20"><i class="fa fa-rocket"></i> Quick Access</h3>
                                    <div class="quick-access-grid">
                                        <a href="#StudentGroupsMarks" data-student_id="{{ Crypt::encrypt($student_info->id) }}" class="Markstudent btn-modern btn-modern-primary" data-toggle="tab">
                                            <i class="fa fa-check-square-o"></i> View My Marks
                                        </a>
                                        <a href="#StudentGroupsProgress" data-student_id="{{ Crypt::encrypt($student_info->id) }}" class="StudentGroupsProgress btn-modern btn-modern-accent" data-toggle="tab">
                                            <i class="fa fa-bar-chart"></i> Track Progress
                                        </a>
                                        <button onclick="window.print()" class="btn-modern btn-modern-primary" style="background: #1a2744; color: white;">
                                            <i class="fa fa-print"></i> Print Academic Record
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="Profile">
                        <div class="info-card">
                            <h3 class="mb-30"><i class="fa fa-user-circle"></i> Edit Profile Information</h3>
                            <form method="post" enctype="multipart/form-data" action="/student/editProfile">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Modern Avatar Upload -->
                                        <div class="profile-avatar-edit">
                                            <div class="avatar-preview-wrapper">
                                                @if ($student_info->image != '')
                                                    <img id="profilePreview" src="{{ url($student_info->image) }}" alt="Profile" />
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
                                            <p class="text-muted small">Click the camera icon to upload a new profile picture.</p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="modern-form-grid">
                                            <div class="form-group-modern">
                                                <label>Full Name</label>
                                                <input class="form-control-modern" name="name" value="{{ $student_info->name }}" type="text" placeholder="Enter your full name">
                                            </div>
                                            
                                            <div class="form-group-modern">
                                                <label>Mobile Number</label>
                                                <input class="form-control-modern" name="mobile" value="{{ $student_info->mobile }}" type="text" placeholder="Enter your phone number">
                                            </div>
                                            
                                            <div class="form-group-modern">
                                                <label>Birth Date</label>
                                                <input type="date" class="form-control-modern" name="dob" value="{{ $student_info->dob }}">
                                            </div>
                                            
                                            <div class="form-group-modern">
                                                <label>Job / Field of Study</label>
                                                <input class="form-control-modern" name="job" value="{{ $student_info->job }}" type="text" placeholder="e.g. Engineer, Student">
                                            </div>
                                            
                                            <div class="form-group-modern" style="grid-column: span 2;">
                                                <label>Email Address</label>
                                                <input class="form-control-modern" name="email" value="{{ $student_info->email }}" type="email" placeholder="yourname@example.com">
                                            </div>
                                        </div>

                                        <div class="mt-30 text-right">
                                            @if($student_info->ask_update == 0)
                                                <a class="btn btn-warning btn-lg" data-href="{{ route('ask.update.profile') }}" id="ask_updte" data-id="{{ Crypt::encrypt($student_info->id) }}">
                                                    <i class="fa fa-paper-plane"></i> Request Profile Update
                                                </a>
                                            @elseif($student_info->ask_update == 1)
                                                <button class="btn btn-info btn-lg disabled" disabled>
                                                    <i class="fa fa-clock-o"></i> Waiting for Admin Approval
                                                </button>
                                            @elseif($student_info->ask_update == 2)
                                                <button class="btn btn-success btn-lg" type="submit">
                                                    <i class="fa fa-save"></i> Update My Profile
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="Courses">
                        <div class="d-flex justify-content-between align-items-center mb-30">
                            <h3 class="m-0"><i class="fa fa-book"></i> My Registered Courses</h3>
                            <button class="btn btn-modern btn-modern-accent" id="alert-code" value="{{ Crypt::encrypt($student_info->id) }}">
                                <i class="fa fa-plus-circle"></i> Join Group
                            </button>
                        </div>
                        
                        <div class="course-grid">
                            @foreach ($student_groups as $group)
                            <div class="course-card">
                                <div class="course-img-wrapper">
                                    <img src="{{ $group->group->image ? url($group->group->image) : url('assets/oxford/img/logo.png') }}" class="course-img" alt="Course">
                                    <div class="course-overlay">
                                        <a href="#Info" data-toggle="tab" class="joinG btn-join-circle" 
                                           data-group_id="{{ Crypt::encrypt($group->group_id) }}" 
                                           data-student_id="{{ Crypt::encrypt($group->student_id) }}">
                                            <i class="fa fa-external-link"></i>
                                            <span>Join</span>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="course-body">
                                    <div class="d-flex justify-content-between align-items-start mb-10">
                                        <h4 class="course-title">{{ $group->group->program->title }}</h4>
                                        <span class="badge {{ $group->group->status == 1 ? 'status-active' : 'status-delayed' }}">
                                            {{ $group->group->status == 1 ? 'Active' : 'Finished' }}
                                        </span>
                                    </div>
                                    
                                    <div class="course-meta">
                                        <div class="meta-item" title="Lecturer">
                                            <i class="fa fa-user"></i> {{ $group->group->teacher->name }}
                                        </div>
                                        <div class="meta-item" title="Group Name">
                                            <i class="fa fa-tag"></i> {{ $group->group->name }}
                                        </div>
                                        @if($group->group->ctime)
                                        <div class="meta-item" title="Class Days">
                                            <i class="fa fa-calendar-check-o"></i> {{ $group->group->ctime->days }}
                                        </div>
                                        @endif
                                        <div class="meta-item" title="Start Date">
                                            <i class="fa fa-clock-o"></i> {{ date('d M Y', strtotime($group->group->start_date)) }}
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <div class="progress-info d-flex justify-content-between small">
                                            <span>Progress</span>
                                            <span>{{ $group->progress ?: 0 }}%</span>
                                        </div>
                                        <div class="custom-progress">
                                            <div class="progress-fill" style="width: {{ $group->progress ?: 0 }}%"></div>
                                        </div>

                                        <div class="d-flex gap-10">
                                            <a href="#Info" data-toggle="tab" class="joinG btn btn-sm btn-primary flex-grow-1" 
                                               data-group_id="{{ Crypt::encrypt($group->group_id) }}" 
                                               data-student_id="{{ Crypt::encrypt($group->student_id) }}">
                                                <i class="fa fa-info-circle"></i> Details
                                            </a>
                                            @if($group->cer_code)
                                            <a href="{{ route('student.certificate.download', Crypt::encrypt($group->id)) }}" 
                                               class="btn btn-sm btn-success" title="Download Certificate">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            @endif
                                            <a href="javascript:void(0);" data-id="{{ $group->group->id }}" data-user="{{ $group->group->name }}" 
                                               class="chat-toggle btn btn-sm btn-outline-light" title="Course Chat">
                                                <i class="fa fa-comments"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade" id="Exam">
                        <div class="info-card">
                            <h3><i class="fa fa-file-text-o"></i> Exam Records</h3>
                            <div class="table-responsive">
                                <table class="table-modern">
                                    <thead>
                                        <tr>
                                            <th>Group Name</th>
                                            <th>Progress Test 1</th>
                                            <th>Progress Test 2</th>
                                            <th>Final Exam</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($studentGropesExamday as $group)
                                        <tr>
                                            <td><strong>{{ $group->group->name }}</strong></td>
                                            <td>{!! $group->progress_test1 ? '<span class="text-primary">'.$group->progress_test1.'</span>' : '<span class="text-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                                            <td>{!! $group->progress_test2 ? '<span class="text-primary">'.$group->progress_test2.'</span>' : '<span class="text-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                                            <td>{!! $group->final_exam ? '<span class="text-success" style="font-weight:700">'.$group->final_exam.'</span>' : '<span class="text-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="Teacher_Evaluations">
                        <div class="info-card">
                            <h3><i class="fa fa-star-o"></i> Teacher Evaluations</h3>
                            <div class="table-responsive">
                                <table class="table-modern">
                                    <thead>
                                        <tr>
                                            <th>Teacher</th>
                                            <th>Name</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($teacherStudentEvaluation as $group)
                                        <tr>
                                            <td style="width: 80px;">
                                                <img src="{{ $group->image ? url($group->image) : url('assets/oxford/img/students/avatar.png') }}" 
                                                     style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent);">
                                            </td>
                                            <td><strong>{{ $group->name }}</strong></td>
                                            <td class="text-center">
                                                <a href="{{ route('student.evaluate', Crypt::encrypt($group->id)) }}" class="btn btn-sm btn-warning">
                                                    <i class="fa fa-star"></i> Rate Teacher
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="Password">
                        <div class="info-card">
                            <h3 class="mb-30" style="border-bottom: 2px solid var(--bg-light); padding-bottom: 15px;">
                                <i class="fa fa-lock" style="color: var(--accent); margin-right: 10px;"></i>Change Password
                            </h3>
                            <form class="form-horizontal modern-form" id="checkout-form" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-8 col-md-offset-2">
                                        <div class="form-group mb-20">
                                            <label class="control-label" style="text-align: left; margin-bottom: 10px; display: block; color: var(--primary); font-weight: 600;">New Password</label>
                                            <input class="form-control-modern" type="password" name="npassword" placeholder="Enter new password">
                                        </div>
                                        <div class="form-group mb-30">
                                            <label class="control-label" style="text-align: left; margin-bottom: 10px; display: block; color: var(--primary); font-weight: 600;">Repeat Password</label>
                                            <input class="form-control-modern" type="password" name="rpassword" placeholder="Confirm new password">
                                        </div>
                                        <div class="form-group mb-none text-center">
                                            <button class="btn-modern btn-modern-accent" style="width: 100%; padding: 12px; font-size: 16px;" type="submit" value="Login">
                                                <i class="fa fa-save"></i> Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="Info">

                    </div>
                    <div class="tab-pane fade" id="AdminNotify">

                    </div>
                    <div class="tab-pane fade" id="StudentGroupsMarks">

                    </div>
                    <div class="tab-pane fade" id="StudentGroupsProgress">

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="SubjectsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content files">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Course Downloads</h5>
                <button type="button" class="close btn-view" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container container1">
                    <div class="spn-load">
                        <img src="<?= url('assets/oxford/img/preloader.gif') ?>" alt="" class="loading"
                             width="100">
                    </div>
                    <iframe id="subjects" class="responsive-iframe" src=""></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <a type="button" class="btn btn-view" data-dismiss="modal">Close</a>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="ajax1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content fees">
            <div class="modal-body">
                <img src="<?= url('assets/oxford/img/preloader.gif') ?>" alt="" class="loading">
                <span> &nbsp;&nbsp;Loading... </span>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/jquery.autocomplete/1.0.7/jquery.autocomplete.min.js"></script>

<script src="{{ asset('assets/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"
type="text/javascript"></script>
<script type="text/javascript">
$(function () {
$('.date-picker').datepicker();
});
</script>
<script>
    var base_url = '{{ url(' / ') }}';
</script>
<script src="https://js.pusher.com/4.1/pusher.min.js"></script>
<script src="{{ url('assets/oxford/js/chat.js') }}" type="text/javascript"></script>
<script>
    $(document).on('click', ".joinG", function () {
        var student_id = $(this).data("student_id");
        var group_id = $(this).data("group_id");
        $.ajax({
            type: "POST",
            url: "{{ route('students.showGroue_info') }}",
            data: {
                'student_id': student_id,
                'group_id': group_id,
                "_token": "{{ csrf_token() }}"
            }

        }).success(function (data) {
            console.log(data);
            $('#Info').html(data);
        });
    });
</script>
{{-- get notification by auth student --}}
<script>
    $(document).on('click', ".AdminNotify", function () {
        var $container = $('#AdminNotify');
        $container.html('<div class="text-center p-50"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-10">Loading messages...</p></div>');
        
        $.ajax({
            type: "GET",
            url: "{{ route('student.notifications') }}",
        }).done(function (data) {
            $container.html(data);
            // Hide badge after opening
            $('.unread-badge').fadeOut();
        }).fail(function() {
            $container.html('<div class="alert alert-danger">Error loading messages. Please check your connection.</div>');
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('.userinfo').click(function () {
            var url = $(this).attr('href');
            $.ajax({
                url: url,
                type: 'get',
                success: function (response) {
                    $('.modal-content.fees').html(response);
                }

            });
        });
        $('.show-files').click(function () {
            var url = $(this).attr('href');
            var subject = '<span style="font-weight: bold;">' + $(this).attr(
                    'data-subject') +
                    '</span>' + ' - Course files';
            $('.modal-title').html(subject);
            var src = window.location.origin + '/uploads/files/index.php?base=' + url;
            $("#subjects").attr('src', src);
        });
    });
    $("#alert-code").click(function () {
        var student_id = $("#alert-code").val();
        Swal.fire({
            title: '<div class="animate__animated animate__pulse animate__infinite"><i class="fa fa-shield fa-2x" style="color: #f5c518"></i></div><br>Enter Group Code',
            text: 'Unlock your course materials by entering your unique group access code.',
            input: 'text',
            background: '#1a2744',
            color: '#fff',
            confirmButtonColor: '#f5c518',
            confirmButtonText: 'Verify & Join',
            showCancelButton: true,
            cancelButtonColor: '#d33',
            customClass: {
                input: 'swal-modern-input',
                title: 'swal-modern-title',
                confirmButton: 'btn-modern-accent',
                cancelButton: 'btn-modern-danger'
            },
            inputAttributes: {
                autocapitalize: 'off',
                placeholder: 'XXXX-XXXX',
                style: 'text-align: center; font-size: 24px; letter-spacing: 2px;'
            },
            showLoaderOnConfirm: true,
            preConfirm: (inputValue) => {
                if (!inputValue) {
                    Swal.showValidationMessage('<i class="fa fa-info-circle"></i> Please enter your group code');
                    return false;
                }
                return $.ajax({
                    url: 'grope/code/check',
                    type: 'POST',
                    data: {
                        input: inputValue,
                        student_id: student_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Handle case where server returns 200 but body says 404/error
                        if (response.status !== 200) {
                            Swal.showValidationMessage(`<i class="fa fa-exclamation-triangle"></i> ${response.message || 'Incorrect Code'}`);
                        }
                        return response;
                    },
                    error: function(xhr) {
                        let errorMsg = 'System Error: Please contact support.';
                        try {
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseText && xhr.responseText.includes('SQLSTATE')) {
                                errorMsg = 'Database Error: Please ensure you enter valid characters.';
                            }
                        } catch(e) {
                            console.error('Error parsing response', e);
                        }
                        
                        Swal.showValidationMessage(`<i class="fa fa-exclamation-triangle"></i> ${errorMsg}`);
                        return false;
                    }
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.value && result.value.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Access Granted!',
                    text: 'You have been added to the group successfully.',
                    background: '#1a2744',
                    color: '#fff',
                    showConfirmButton: false,
                    timer: 2000
                });
                
                // Refresh courses section via AJAX
                $.ajax({
                    url: "{{ route('student.courses.partial') }}",
                    type: 'GET',
                    success: function(html) {
                        $('.course-grid').fadeOut(400, function() {
                            $(this).html(html).fadeIn(400);
                        });
                    }
                });
            }
        });
    });
</script>
<!-- JavaScript code to handle dialog and send message with Ajax -->
<script>
    // Attach click event listener to dialog button
    document.getElementById('dialog-btn').addEventListener('click', function () {
        Swal.fire({
            title: '<i class="fa fa-envelope-o"></i> Send Message',
            background: '#1a2744',
            color: '#fff',
            html: `
                <div class="text-left" style="padding: 10px;">
                    <div class="form-group">
                        <label for="message-title" style="color:#f5c518; font-weight: 700;">Message Title:</label>
                        <input type="text" class="form-control" id="message-title" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <label for="message-body" style="color:#f5c518; font-weight: 700;">Message Body:</label>
                        <textarea class="form-control" id="message-body" rows="4" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px;"></textarea>
                    </div>
                </div>`,
            confirmButtonColor: '#f5c518',
            confirmButtonText: 'Send Message Now',
            showCancelButton: true,
            cancelButtonColor: '#d33',
            focusConfirm: false,
            preConfirm: function () {
                var title = Swal.getPopup().querySelector('#message-title').value;
                var body = Swal.getPopup().querySelector('#message-body').value;
                if (!title || !body) {
                    Swal.showValidationMessage('Please fill in both fields');
                    return false;
                }
                return $.ajax({
                    url: "{{ route('student.admin.messages') }}",
                    method: 'POST',
                    data: JSON.stringify({
                        title: title,
                        body: body,
                        _token: "{{ csrf_token() }}"
                    }),
                    contentType: 'application/json',
                    dataType: 'json'
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(function (result) {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Message Sent!',
                    text: 'Your message will be answered within 24 hours.',
                    background: '#1a2744',
                    color: '#fff',
                    confirmButtonColor: '#f5c518'
                });
            }
        }).catch(function (error) {
            Swal.fire({
                title: 'Error!',
                text: 'Something went wrong while sending the message.',
                icon: 'error',
                background: '#1a2744',
                color: '#fff'
            });
        });
    });
</script>
<script>
    $(document).on('click', ".Markstudent", function () {
        var $container = $('#StudentGroupsMarks');
        $container.html('<div class="text-center p-50"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-10">Loading marks...</p></div>');
        
        $.ajax({
            type: "POST",
            url: "{{ route('student.showGroueMarks') }}",
            data: {
                "_token": "{{ csrf_token() }}"
            }
        }).done(function (data) {
            $container.html(data);
        }).fail(function() {
            $container.html('<div class="alert alert-danger">Error loading marks. Please try again.</div>');
        });
    });

    $(document).on('click', ".StudentGroupsProgress", function () {
        var $container = $('#StudentGroupsProgress');
        $container.html('<div class="text-center p-50"><i class="fa fa-spinner fa-spin fa-3x text-accent"></i><p class="mt-10">Loading progress...</p></div>');
        
        $.ajax({
            type: "POST",
            url: "{{ route('student.showGroueProgress') }}",
            data: {
                "_token": "{{ csrf_token() }}"
            }
        }).done(function (data) {
            $container.html(data);
        }).fail(function() {
            $container.html('<div class="alert alert-danger">Error loading progress. Please try again.</div>');
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('#ask_updte').on('click', function () {
            var url = $(this).data('href');
            var id = $(this).data('id');

            Swal.fire({
                title: 'Confirm Update',
                text: 'Are you sure you want to update?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, make the AJAX request
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            id: id,
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            if (response.success) {
                                var newHTML = '<div class="form-group mb-none">' +
                                        '<div class="col-sm-offset-3 col-sm-9">' +
                                        '<button class="view-all-accent-btn disabled btn-info col-sm-9" ' +
                                        'value="">Waiting for approval</button></div></div>';

                                $('#ask_updte').replaceWith(newHTML);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Request Send successful!'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Update failed!'
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong!'
                            });
                        }
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
            particlesJS('particles-js', {
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
    // Auto-activate tab based on URL hash (e.g. /student#Exam)
    $(document).ready(function() {
        var hash = window.location.hash;
        if (hash) {
            var tabId = hash.replace('#', '');
            var $tabLink = $('[href="#' + tabId + '"][data-toggle="tab"]');
            if ($tabLink.length) {
                $tabLink.tab('show');
                $('html, body').animate({ scrollTop: 0 }, 300);
            }
        }
        $('[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr('href');
            if (target && target.startsWith('#')) {
                history.replaceState(null, null, target);
            }
        });
    });
</script>

@stop
