@extends('frontend.layouts.master')
@section('title', 'Teacher Area')
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/gallary.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Teacher Area</h1>
            <ul>
                <li><a href="{{ url('/')}}">Home</a> -</li>
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
                    <li class="active"><a href="#Profile" data-toggle="tab" aria-expanded="false">Profile</a></li>
                    <li><a href="#Groups" data-toggle="tab" aria-expanded="false">Courses</a></li>
                    <li><a href="#Password" data-toggle="tab" aria-expanded="false">Change Password</a></li>
                    <li><a href="{{ url('/logout')}}">Logout</a></li>
                </ul>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
                <div class="profile-details tab-content">
                    <div class="tab-pane fade active in" id="Profile">
                        <h3 class="title-section title-bar-high mb-40">Personal Information</h3>
                        <div class="form-horizontal" id="checkout-form">
                            <div class="personal-info">
                                <form class="form-horizontal" id="checkout-form" method="post" enctype="multipart/form-data" action="/teacher/editProfile">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Avatar</label>
                                        <div class="col-sm-9 public-profile-content">
                                            @if($teacher_info->image!='')
                                            <img src="{{ url($teacher_info->image)}}" alt=""/>
                                            @else
                                            <img src="{{ url('assets/oxford/img/teachers/avatar.png')}}" alt=""/>
                                            @endif
                                            <div class="file-title">Upload a new avatar:</div>
                                            <div class="file-upload-area"><input type="file" name="fileToUpload" id="fileToUpload"></div>
                                            <div class="file-size">JPEG 80x80 px</div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Name</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" id="name" value="<?= $teacher_info->name ?>" type="text" readonly="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Mobile No.</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" id="mobile" value="<?= $teacher_info->mobile ?>" type="text" readonly="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Date Of Birth</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" style="background: #fff" id="dob" name="dob" value="<?= $teacher_info->dob ?>" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Email</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" style="background: #fff" id="email" name="email"  value="<?= $teacher_info->email ?>" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Join Date</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" id="join_date"  value="<?= $teacher_info->join_date ?>" type="text" readonly="">
                                        </div>
                                    </div>
                                    <div class="form-group mb-none">
                                        <div class="col-sm-offset-3 col-sm-9">
                                            <button class="view-all-accent-btn disabled col-sm-9" type="submit" value="">Update</button>
                                        </div>
                                    </div>  
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="Groups">
                        <h3 class="title-section title-bar-high mb-40">Courses</h3>
                        <div class="orders-info">
                            <div class="table-responsive">
                                <table class="table table-bordered table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Course Name</th>
                                            <th>students No.</th>
                                            <th>Start date</th>
                                            <th>End date</th>
                                            <th>Degrees</th>
                                            <th>Exam Dates</th>
                                            <th>Chat</th>
                                            <th>Teacher Library</th>
                                        </tr> 
                                    </thead>
                                    <tbody>
                                        @foreach($groups as $group)
                                        <tr>
                                            <th><a data-toggle="modal" data-target=".bd-example-modal-lg" id="show-std" href="{{ url('groupStd/' . $group->id)}}">{{ $group->name}}</a></th>
                                            {{-- <td>{{ $group->studentsCount}}</td> --}}
                                            <td>{{ $group->start_date}}</td>
                                            <td>{{ $group->end_date}}</td>
                                            <td><a href="{{ url('group/' . $group->id)}}" title="Enter Degrees" class="btn-view">Enter Degrees</a></td>
                                            <td><a href="{{ url('examDate/' . $group->id)}}" title="Exam Dates" class="btn-view">Exam Dates</a></td>
                                            <td><a href="javascript:void(0);" data-id="{{$group->id}}" data-user="{{$group->name}}" title="Open course Chat" class="btn-view chat-toggle">Open</a></td>                                            
                                            <td><a href="{{ url('group/library/' . $group->id)}}" title="Upload" class="btn-view">Upload</a></td>                                            
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @include('frontend.chat.chat-box')
                        <input type="hidden" id="current_user" value="{{ \Auth::user()->id }}" />
                        <input type="hidden" id="user_type" value="teacher" />
                        {{-- <input type="hidden" id="current_group" value="{{$groups_array}}" /> --}}
                        <input type="hidden" id="pusher_app_key" value="{{ env('PUSHER_APP_KEY') }}" />
                        <input type="hidden" id="pusher_cluster" value="{{ env('PUSHER_APP_CLUSTER') }}" />
                    </div>
                    <div class="tab-pane fade" id="Password">
                        <h3 class="title-section title-bar-high mb-40">Change Password</h3>
                        <form class="form-horizontal" id="checkout-form" method="post" enctype="multipart/form-data">
                            <div class="personal-info">                               
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Change Password</label>
                                    <div class="col-sm-9">
<!--                                        <input class="form-control mb-10" id="last-name" type="password" name="cpassword" placeholder="Current Password">-->
                                        <input class="form-control mb-10" id="last-name" type="password" name="npassword" placeholder="New Password">
                                        <input class="form-control mb-10" id="last-name" type="password" name="rpassword" placeholder="Repeat Password">
                                    </div>
                                </div>
                                <div class="form-group mb-none">
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <button class="view-all-accent-btn disabled col-sm-9" type="submit" value="Login">Save</button>
                                    </div>
                                </div>
                            </div>
                            {{ csrf_field() }}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->


<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
<link href="{{ asset('assets/admin/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css"/>
<style>
    .input-group-btn {
        background-color: #f0f0f0;
        border-radius: 0 5px 5px 0;
    }
</style>
@stop
@section('js')
<script src="{{asset('assets/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
<script type="text/javascript">
$(function () {
    $('.date-picker').datepicker();
});
</script>
<script>
    var base_url = '{{ url("/") }}';
</script>
<script src="https://js.pusher.com/4.1/pusher.min.js"></script>
<script src="{{url('assets/oxford/js/chat.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $('.userinfo').click(function () {
            var url = $(this).attr('href');
            $.ajax({
                url: url,
                type: 'get',
                success: function (response) {
                    $('.modal-content').html(response);
                }
            });
        });
        $('#show-std').click(function () {
            //alert("");
            $group_data = null;
            $group_id = 1;
            var url = $(this).attr('href');
            $.ajax({
                url: url,
                type: 'get',
                success: function (response) {
                    $('.modal-content').html(response);
                }
            });
        });
    });
</script>
@stop