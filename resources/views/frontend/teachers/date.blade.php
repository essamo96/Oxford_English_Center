@extends('frontend.layouts.master')
@section('title', 'Teacher Area')
@section('css')
    <style>
        #alert-code,
        #go-back {
            border-radius: 10px;
            font-size: 15px;
            direction: rtl;
            background-color: #002147;
            color: #fdc800;
            margin-left: 512px;
        }
    </style>
@endsection
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/gallary.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Teacher Area</h1>
            <ul>
                <li><a href="{{ url('/')}}">Home</a> -</li>
                <li>Teacher Area -</li>
                <li>{{$group->name}} -</li>
                <li>Exam Dates</li>
            </ul>
        </div>
    </div>
</div>
<div class="section-space accent-bg">
    <div class="container">
        <div class="row">
            @include('frontend.layouts.error')
            <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
                <div class="profile-details">
                    <h3 class="title-section title-bar-high mb-40">Exam Date< <a id="go-back" href="/teacher"
                                class="btn btn-success btn-sm"> back
                                <i class="bi bi-skip-backward-fill"></i></a></h3>
                    <form class="form-horizontal" method="post" action="{{ route('teacher.group.examDate',['id' =>$group->id]) }}">
                        <div class="personal-info">                               
                            <div class="form-group">
                                <label class="col-sm-3 control-label"> Progress Test 1 </label>
                                <div class="col-sm-9">
                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control" readonly name="progress_test1" value="<?= $exam_dates ? $exam_dates->progress_test1 : '' ?>">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"> Progress Test 2 </label>
                                <div class="col-sm-9">
                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control" readonly name="progress_test2" value="<?= $exam_dates ? $exam_dates->progress_test2 : '' ?>">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label"> Progress Test 3 </label>
                                <div class="col-sm-9">
                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control" readonly name="progress_test3" value="<?= $exam_dates ? $exam_dates->progress_test3 : '' ?>">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">  Final Exam </label>
                                <div class="col-sm-9">
                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control" readonly name="final_exam" value="<?= $exam_dates ? $exam_dates->final_exam : '' ?>">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
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
<!-- Modal -->
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
@stop