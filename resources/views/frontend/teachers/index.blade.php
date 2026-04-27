@extends('frontend.layouts.master')
@section('title', 'Teacher Area')
@section('css')
    <style>
        .bg-textPrimary2 {
            /* background: #FFFFFF; */
            color: #fdc800;

        }

        .user-name {
            position: relative;
            left: 68px;
        }

        .img-responsive2 {
            border-radius: 30px;
            box-shadow: 0px 0px 10px rgb(223 162 249), 0px 3px 3px rgb(227 248 38 / 33%);
            margin-bottom: -41px;
        }

        .lecturers-social2 {
            position: relative;
        }

        .row2 {
            position: relative;
            top: -167px;
        }

        .course-details-inner,
        .course-details-inner {
            padding: 25px;
        }

        .sidebar-course-price {
            width: 300px
        }

        .sidebar-course-price span {
            font-size: 16px;
        }

        .swal2-show {
            background-color: #00142ba9;
            border-radius: 20px;
            color: white;
        }

        .swal2-title {
            color: #fdc800;
        }

        .swal2-success-circular-line-left,
        .swal2-success-circular-line-right,
        .swal2-success-fix {
            visibility: hidden;
        }

        .swal2-popup {
            width: 450px;
            height: 375px;
        }

        #alert-code,
        #go-back {
            border-radius: 10px;
            font-size: 15px;
            direction: rtl;
            background-color: #002147;
            color: #fdc800;
            margin-left: 512px;
        }

        .swal2-html-container {
            font-size: 15px;
        }

        #profileImg {
            margin-inline: 47px;
            margin-bottom: 17px;
            box-shadow: rgb(0 33 71) 0px 13px 27px -5px, rgb(0 0 0 / 30%) 0px 8px 16px -8px;
            margin-left: 52px;
        }

        .fa-2x {
            font-size: 1.5rem;
        }

        .fa-camera:before {
            content: "\f030";
            color: #fdc800;
            font-size: 20px;
            padding: 8px;
        }

        .courses-box1 .single-item-wrapper .courses-img-wrapper img {
            width: 35%;
        }

        .courses-box1 {
            width: 268px;
            margin-right: 5px;
        }

        .courses-page-area1 {
            padding: 38px 0;
        }

        .enroll-btn2 {
            color: #000000;
            padding: 18px 0;
            background: #005bc499;
            text-transform: uppercase;
            font-size: 14px;
            font-weight: 700;
            display: inline-block;
            border: none;
            width: 60%;
            border: 2px solid #002147;
        }

        .enroll-btn2:hover {
            background: #fdc80000;
            color: #002147;
        }

        .enroll-btn22 {
            color: #000000;
            padding: 18px 0;
            background: #ff7d7de0;
            text-transform: uppercase;
            font-size: 14px;
            font-weight: 700;
            display: inline-block;
            border: none;
            width: 60%;
            border: 2px solid #ff0a00;
        }

        .enroll-btn22:hover {
            background: #fdc80000;
            color: #fd0000;
        }

        #count {
            display: inline;
            padding: 3px;
            font-family: 'Roboto';
            color: white;
        }

        .course-details-comments .media .media-body h3 a {
            background-color: #002147;
            color: white;
            padding: 8px;
            border-radius: 50px;
            font-size: 15px;


        }

        .course-details-comments .media .media-body h3 a:hover {
            padding: 8px;
            color: #fdc800;
            padding-inline: 25px;
            transform: translateX(10px);

        }

        .course-details-comments .media .media-body p {
            color: #000000;
            text-align: justify;
            margin: 0 0 6px 0;
            font-size: 16px;
            transition: font-size 0.3s ease;
        }

        .course-details-comments .media .media-body p:hover {
            color: #000000;
            font-size: x-large;
            font-family: math;
            transform: translateX(10px);
        }

        .lecturers-contact-info {
            text-align: center;
            margin-left: 32px;
            padding-left: 6px;
        }

        .lecturers-contact-info h2 {
            right: 0px;
        }

        .lecturers-page-area {
            padding: 71px 0;
        }

        #Home {
            margin-top: 58px;
        }

        .tab-content>.active {
            display: block;
            margin-top: 58px;
        }

        #staus_course {
            text-align: center;
            color: #FFFFFF;
            padding: 1px 10px;
            background: #002147;
            text-transform: uppercase;
            font-size: 14px;
            display: inline-block;
            border: none;
            font-weight: 500;
        }
    </style>
@endsection
@section('content')
    <div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/gallary.jpg') }}');">
        <div class="container">
            <div class="pagination-area">
                <h1>Teacher Area</h1>
                <ul>
                    <li><a href="{{ url('/') }}">Home</a> -</li>
                    <li>Teacher Area</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="section-space accent-bg">
        <div class="container">
            <div class="row">
                @include('frontend.layouts.error')
                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                    <ul class="profile-title">
                        <li class="active"><a href="#Welcome" data-toggle="tab" aria-expanded="false">Welcome</a></li>
                        <li><a href="#Courses" data-toggle="tab" aria-expanded="false">Courses</a></li>
                        <li><a href="{{ route('teacher.ExamDates', $teacher_id) }}">Exam Data</a></li>
                        <li><a href="#Profile" data-toggle="tab" aria-expanded="false">Profile</a></li>
                        <li><a href="#Password" data-toggle="tab" aria-expanded="false">Change Password</a></li>
                        <li><a href="{{ url('/logout') }}">Logout </a></li>
                    </ul>
                </div>

                <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
                    <div class="Home-details tab-content">
                        <div class="tab-pane fade active in" id="Welcome">
                            <div class="tab-pane fade active in" id="Home">
                                <h3 class="title-section title-bar-high mb-40">WELCOME TO OXFORD FAMILY</h3>
                                <div class="form-horizontal" id="checkout-form">
                                    <div class="lecturers-page-area">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                                                    <div class="lecturers-contact-info">
                                                        @if ($teacher_info->image != '')
                                                            <img src="{{ asset($teacher_info->image) }}"
                                                                class="img-responsive" alt="team" />
                                                        @else
                                                            <img src="{{ url('assets/oxford/img/students/avatar.png') }}"
                                                                class="img-responsive" alt="team" />
                                                        @endif
                                                        {{-- <img src="img/team/13.jpg" class="img-responsive" alt="team"> --}}
                                                        @if ($teacher_info->name != '')
                                                            <h2>{{ $teacher_info->name }}<br>
                                                                <span href="#" style=""
                                                                    class="<?= $teacher_info->status != 0 ? 'enroll-btn2' : 'enroll-btn22' ?> m-2"><?= $teacher_info->status != 0 ? 'Active' : 'Delayed' ?></span>

                                                            </h2>
                                                        @else
                                                            <h2>NO Name</h2>
                                                        @endif

                                                        <ul class="lecturers-social2">
                                                            <li><a href="#AdminNotify" class="AdminNotify" data-toggle="tab"
                                                                    aria-expanded="false">
                                                                    <i class="fa fa-envelope-o"
                                                                        aria-hidden="true"></i><strong id="count"
                                                                        style="">{{ $count }}</strong></a>

                                                            </li>
                                                            <li alt="incoming messages"><a href="#"><i id="dialog-btn"
                                                                        class="bi bi-chat " aria-hidden="true"></i></a>
                                                            </li>
                                                            <li><a href="#" data-toggle="tab" aria-expanded="false"><i
                                                                        class="bi bi-bell-fill" aria-hidden="true"></i></a>
                                                            </li>
                                                            <li><a href="{{ route('student.notifications') }}"><i
                                                                        class="bi bi-chat-heart"aria-hidden="true"></i></a>
                                                            </li>
                                                        </ul>

                                                    </div>
                                                </div>
                                                <div class="col-xl-8 col-lg-8 col-md-6 col-sm-12">

                                                    <h3 class="title-default-left title-bar-big-left-close">Qualifications
                                                    </h3>
                                                    <ul class="course-feature2">
                                                        @if ($teacher_info->name != '')
                                                            <li>{{ $teacher_info->name }}</li>
                                                        @endif
                                                        @if ($teacher_info->mobile != '')
                                                            <li>{{ $teacher_info->mobile }}</li>
                                                        @endif
                                                        @if ($teacher_info->email != '')
                                                            <li>{{ $teacher_info->email }}</li>
                                                        @endif
                                                        @if ($teacher_info->dob != '')
                                                            <li>{{ $teacher_info->dob }}</li>
                                                        @endif
                                                        @if ($teacher_info->join_date != '')
                                                            <li>{{ $teacher_info->join_date }}</li>
                                                        @endif
                                                        {{-- 
                                                        @if ($teacher_info->jop != '')
                                                            <li>{{ $teacher_info->jop }}</li>
                                                        @endif --}}
                                                    </ul>
                                                    {{-- @if ($grope_name != '') --}}
                                                    <div class="col-xl-8 col-lg-8 col-md-6 col-sm-12">
                                                        <div class="lecturers-contact-info">

                                                            <ul class="lecturers-contact">
                                                                <li><i class="bi bi-check" aria-hidden="true"></i>Weekly
                                                                    Evaluation
                                                                    {{-- {{ $grope_name }} --}}
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        {{-- @endif --}}
                                                        <div class="lecturers-contact-info">

                                                            <ul class="lecturers-contact">
                                                                <a
                                                                    href="{{ route('teacher.progress', $teacher_info->id) }}">
                                                                    <li><i class="bi bi-graph-up-arrow"
                                                                            aria-hidden="true"></i>progress</li>
                                                                </a>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="Courses">
                            <h3 class="title-section title-bar-high mb-40">Coursess
                                {{-- <button class="btn btn-success btn-sm"
                                    id="alert-code" value=" "> Join Grope <i
                                        class="bi bi-grid-fill"></i></button> --}}
                            </h3>
                            {{-- Crypt::encrypt($student_info->id) --}}
                            <div class="courses-page-area1">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12 order-md-2"
                                            style="margin-top: 49px;">
                                            <div class="tab-content">
                                                <div class="tab-pane tab-item animated fadeIn active" id="menu-1"
                                                    role="tabpanel" aria-labelledby="menu-1-tab">
                                                    <div class="row " style="display: flex; flex-wrap: wrap;">
                                                        @foreach ($groups as $group)
                                                            <div class="col-xl-3 col-lg-4 col-md-4 col-sm-12"
                                                                style="display: flex; flex-direction: column">

                                                                <div class="courses-box1 ">
                                                                    <div class="single-item-wrapper">
                                                                        <div
                                                                            class="courses-img-wrapper hvr-bounce-to-bottom">
                                                                            <img class="img-responsive"
                                                                                @if ($group->image != null) src="{{ url($group->image) }}" 
                                                                            @else
                                                                            src="{{ url('assets/oxford/img/logo.png') }}" @endif
                                                                                alt="courses" style=" width: 100%;">
                                                                            <a href="#Info"
                                                                                data-toggle="tab"aria-expanded="false"
                                                                                id="joinG" class="joinG"
                                                                                data-group_id="{{ Crypt::encrypt($group->id) }}"
                                                                                data-teacher_id="{{ Crypt::encrypt($group->teacher_id) }}">
                                                                                {{-- <i class="fa fa-link getinfo" data-group_id="" aria-hidden="true"></i>  --}}
                                                                                Join </a>
                                                                        </div>
                                                                        <div class="courses-content-wrapper">
                                                                            <a href="javascript:void(0)">
                                                                                <h3 class="item-title">
                                                                                    {{ $group->program->title }} <span
                                                                                        class="view-all-accent-btn btn-sm staus_course"
                                                                                        id="staus_course"
                                                                                        data-end-date="{{ $group->end_date }}"
                                                                                        data-group-id="{{ $group->id }}"
                                                                                        href="javascript:void(0)">CLOSE IT
                                                                                        <i class="bi bi-stop-circle"></i>
                                                                                    </span>
                                                                                </h3>
                                                                            </a>
                                                                            <p class="item-content"
                                                                                style="color:#002147; font-size:15px; margin:3px;">
                                                                                {{ $group->name }}
                                                                                ||
                                                                                {{ $group->teacher->name }} </p>
                                                                            <p
                                                                                style="color:#002147; font-size:15px; margin:2px;">
                                                                                progress level || @if ($group->progress == 30 || $group->progress == null)
                                                                                    Units 1 to 3
                                                                                @elseif ($group->progress == 60)
                                                                                    Units 4 to 6
                                                                                @elseif ($group->progress == 90)
                                                                                    Units 7 to 9
                                                                                @else
                                                                                    Units 10
                                                                                @endif
                                                                            </p>
                                                                            <div class="progress"
                                                                                style="color: orange ;height: 16px;"
                                                                                role="progressbar"
                                                                                aria-valuenow="{{ $group->progress }}"
                                                                                aria-valuemin="0" aria-valuemax="100">
                                                                                <div class="progress-bar bg-warning"
                                                                                    style="width: {{ $group->progress }}%; background-color: orange ;">
                                                                                    <span
                                                                                        style="padding-left: 120px; color :#002147;    font-size: 10px;">{{ $group->progress != null ? $group->progress : 0 }}%</span>
                                                                                </div>
                                                                            </div>
                                                                            <p class="item-content">
                                                                                {{ $group->program->short }}</p>
                                                                            <ul class="courses-info">
                                                                                <li>Course
                                                                                    <br><span> 3 Months</span>
                                                                                </li>
                                                                                <li>No.Hours
                                                                                    <br><span>72</span>
                                                                                </li>
                                                                                <li>times
                                                                                    <br><span>{{ $group->ctime->times }}
                                                                                    </span>
                                                                                </li>
                                                                                <li
                                                                                    style="padding-top: 20px; display: flex; justify-content: center; ">
                                                                                    Chat Teacher <a
                                                                                        style="padding-left: 26px;"
                                                                                        href="javascript:void(0);"
                                                                                        data-id="{{ $group->id }}"
                                                                                        data-user="{{ $group->name }}"
                                                                                        title="Open course Chat"
                                                                                        class="btn-view chat-toggle bi bi-send-fill">
                                                                                        Open Chat</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {{-- <button class="btn btn-success btn-sm"id="alert-code" href="#Courses2" data-toggle="tab" aria-expanded="false"> Grope <i class="bi bi-grid-fill"></i></button> --}}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @include('frontend.chat.chat-box')
                                                    <input type="hidden" id="current_user"
                                                        value="{{ \Auth::user()->id }}" />
                                                    <input type="hidden" id="current_group"
                                                        value="{{ $groups_array }}" />
                                                    <input type="hidden" id="pusher_app_key"
                                                        value="{{ env('PUSHER_APP_KEY') }}" />
                                                    <input type="hidden" id="pusher_cluster"
                                                        value="{{ env('PUSHER_APP_CLUSTER') }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="Profile">
                            {{-- <div class="tab-pane fade active in" id="Home"> --}}
                            <h3 class="title-section title-bar-high mb-40">Personal Information</h3>
                            <div class="form-horizontal" id="checkout-form">
                                <div class="personal-info">
                                    <form class="form-horizontal" id="checkout-form" method="post"
                                        enctype="multipart/form-data" action="/student/editProfile">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Avatar</label>
                                            <div class="col-sm-9 public-profile-content">
                                                @if ($teacher_info->image != '')
                                                    <img id="profileImg" src="{{ url($teacher_info->image) }}"
                                                        alt="" style="margin-left: 52px;" />
                                                @else
                                                    <img src="{{ url('assets/oxford/img/students/avatar.png') }}"
                                                        style="box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;"
                                                        alt="" />
                                                @endif
                                                <div class="col-sm-4 public-profile-content">
                                                    <div class="file-title"
                                                        style="    color: #002147;margin-bottom: 20px;">new avatar JPEG
                                                        80x80 px</div>
                                                    <div class="file-upload-area">
                                                        {{-- <span class="fa fa-2x fa-camera"></span> --}}
                                                        <input class="fa fa-2x fa-camera" type="file"
                                                            name="fileToUpload" id="fileToUpload">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Name</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" id="name"
                                                    value="<?= $teacher_info->name ?>" type="text" readonly="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Mobile No.</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" id="mobile"
                                                    value="<?= $teacher_info->mobile ?>" type="text" readonly="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Date Of Birth</label>
                                            <div class="col-sm-9">
                                                <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                                    <input type="text" class="form-control" readonly name="dob"
                                                        value="<?= $teacher_info->dob ?>">
                                                    <span class="input-group-btn">
                                                        <button class="btn default" type="button">
                                                            <i class="fa fa-calendar"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="form-group">
                                            <label class="col-sm-3 control-label">Jobs</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" style="background: #fff" id="job"
                                                    name="job" value="" type="text">
                                            </div>
                                        </div> --}}
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Email</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" style="background: #fff" id="email"
                                                    name="email" value="<?= $teacher_info->email ?>" type="text">
                                            </div>
                                        </div>
                                        <div class="form-group mb-none">
                                            <div class="col-sm-offset-3 col-sm-9">
                                                <button class="view-all-accent-btn disabled col-sm-9" type="submit"
                                                    value="">Update</button>
                                            </div>
                                        </div>
                                        {{ csrf_field() }}
                                    </form>
                                </div>
                            </div>
                            {{-- </div> --}}
                        </div>

                        <div class="tab-pane fade" id="Password">
                            <h3 class="title-section title-bar-high mb-40">Change Password</h3>
                            <form class="form-horizontal" id="checkout-form" method="post"
                                enctype="multipart/form-data">
                                <div class="personal-info">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Change Password</label>
                                        <div class="col-sm-9">
                                            <!-- <input class="form-control mb-10" id="last-name" type="password" name="cpassword" placeholder="Current Password">-->
                                            <input class="form-control mb-10" id="last-name" type="password"
                                                name="npassword" placeholder="New Password">
                                            <input class="form-control mb-10" id="last-name" type="password"
                                                name="rpassword" placeholder="Repeat Password">
                                        </div>
                                    </div>
                                    <div class="form-group mb-none">
                                        <div class="col-sm-offset-3 col-sm-9">
                                            <button class="view-all-accent-btn disabled col-sm-9" type="submit"
                                                value="Login">Save</button>
                                        </div>
                                    </div>
                                </div>
                                {{ csrf_field() }}
                            </form>
                        </div>
                        <div class="tab-pane fade" id="Info">
                        </div>
                        <div class="tab-pane fade" id="AdminNotify">
                        </div>
                        <div class="tab-pane fade" id="GroupsStudents">

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->


    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

            </div>
        </div>
    </div>


    <div class="modal fade" id="ajax1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-body">
                    <img src="<?= url('assets/oxford/img/preloader.gif') ?>" alt="" class="loading">
                    <span>
                        &nbsp;&nbsp;Loading... </span>
                </div>
            </div>
        </div>
    </div>
@stop
@section('css')
    <link href="{{ asset('assets/admin/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}"
        rel="stylesheet" type="text/css" />
    <style>
        .input-group-btn {
            background-color: #f0f0f0;
            border-radius: 0 5px 5px 0;
        }
    </style>
@stop
@section('js')
    <script src="{{ asset('assets/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"
        type="text/javascript"></script>
    <script type="text/javascript">
        $(function() {
            $('.date-picker').datepicker();
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
            $('#show-std').click(function() {
                //alert("");
                $group_data = null;
                $group_id = 1;
                var url = $(this).attr('href');
                $.ajax({
                    url: url,
                    type: 'get',
                    success: function(response) {
                        $('.modal-content').html(response);
                    }
                });
            });
        });
    </script>
    <script>
        $(document).on('click', ".joinG", function() {
            var teacher_id = $(this).data("teacher_id");
            var group_id = $(this).data("group_id");
            $.ajax({
                type: "POST",
                url: "{{ route('teacher.showGroue_info') }}",
                data: {
                    'teacher_id': teacher_id,
                    'group_id': group_id,
                    "_token": "{{ csrf_token() }}"
                }

            }).success(function(data) {
                console.log(data);
                $('#Info').html(data);
            });
        });
    </script>

    {{-- get notification by auth teacher --}}
    <script>
        $(document).on('click', ".AdminNotify", function() {
            $.ajax({
                type: "POST",
                url: "{{ route('teacher.notifications') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                }

            }).success(function(data) {
                $('#AdminNotify').html(data);
            });
        });
    </script>
    <!-- JavaScript code to handle dialog and send message with Ajax -->
    <script>
        document.getElementById('dialog-btn').addEventListener('click', function() {
            Swal.fire({
                title: 'Send Message',
                html: `
        <div class="form-group">
          <label for="message-title" style="color:#fdc800">Message Title:</label>
          <input type="text" class="form-control" id="message-title">
        </div>
        <div class="form-group">
          <label for="message-body" style="color:#fdc800">Message Body:</label>
          <textarea class="form-control" id="message-body"></textarea>
        </div>`,
                confirmButtonText: 'Send Message',
                focusConfirm: false,
                preConfirm: function() {
                    var title = Swal.getPopup().querySelector('#message-title').value;
                    var body = Swal.getPopup().querySelector('#message-body').value;
                    return $.ajax({
                        url: 'teachers/admin/messages',
                        // url: 'teachers.admin.messages', 
                        method: 'POST',
                        data: JSON.stringify({
                            title: title,
                            body: body,
                            //  somefield: "Some field value", _token: '{{ csrf_token() }}'
                        }),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        contentType: 'application/json',
                        dataType: 'json'
                    });
                },
                allowOutsideClick: function() {
                    return !Swal.isLoading();
                }
            }).then(function(result) {
                Swal.fire({

                    html: `Your message will be answered within 24 hours.`,
                    title: 'Message sent!',
                    icon: 'success',
                });
            }).catch(function(error) {
                Swal.fire({
                    title: 'Something error!',
                    icon: 'error'
                });
            });
        });
    </script>
    <script>
        $('.staus_course').on('click', function() {
            var endDate = $(this).data('end-date');
            var groupId = $(this).data('group-id');
            // alert(endDate + groupId);
            Swal.fire({
                title: 'Are you sure?',
                text: 'Are you sure you want to stop this group?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
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

                                html: `success CLOSED`,
                                title: 'Message sent!',
                                icon: 'success',
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: 'Something error!',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
    </script>
    <script>
        document.getElementById('saveEvaluation').addEventListener('click', function() {
            document.getElementById('GroupsStudents').classList.remove('fade');
            document.getElementById('GroupsStudents').classList.add('fade active in');

        });
    </script>
@stop
