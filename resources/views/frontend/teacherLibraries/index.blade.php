@extends('frontend.layouts.dashboard')
@section('title', 'Teacher Library')
@section('page-title', 'Library')
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/gallary.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Teacher Library Area</h1>
            <ul>
                <li><a href="{{ url('/')}}">Home</a> -</li>
                <li><a href="{{ url('/teacher')}}">Teacher Area</a> -</li>
                <li>{{$info->name}} Library</li>
            </ul>
        </div>
    </div>
</div>
<div class="section-space accent-bg">
    <div class="container">
        <div class="row">
            @include('frontend.layouts.error')
            <div class="profile-details tab-content">
                <div class="" id="Courses">
                    <h3 class="title-section title-bar-high mb-40">{{$info->name}} Course - Teacher Library</h3>
                    <div class="orders-info" >
                        <div class="table-responsive">
                            
                            <form class="form-horizontal" id="checkout-form" method="post" enctype="multipart/form-data" action="">
                                <div class="form-group" style="margin-right: 0">
                                        <label class="col-sm-3 control-label">File</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" id="name" value="{{$info->teacher_lib}}" type="text" readonly="">
                                        </div>
                                    </div>
                                <div class="form-group" style="margin-right: 0">
                                    <div class="col-sm-offset-3 col-sm-6 public-profile-content">
                                        <div class="file-title">Upload a new File:</div>
                                        <div class="file-upload-area"><input type="file" name="file" id="fileToUpload"></div>
                                    </div>
                                </div>
                                <div class="form-group mb-none" style="margin-right: 0">
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <button class="view-all-accent-btn disabled col-sm-9" type="submit" value="">Save</button>
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
    @stop
    @section('css')
    <style>
        .save-tbl-btn{
            text-align: center;
        }
        .view-all-accent-btn {
            width: 15%;
        }
    </style>
    @stop
    @section('js')
    <script>
        $('.deg-input').keyup(function () {
            var id = this.getAttribute("data_id");

            exam1_degree = parseFloat($('#exam1_degree_' + id).val());
            if (!exam1_degree)
                exam1_degree = 0;
            exam2_degree = parseFloat($('#exam2_degree_' + id).val());
            if (!exam2_degree)
                exam2_degree = 0;
            exam3_degree = parseFloat($('#exam3_degree_' + id).val());
            if (!exam3_degree)
                exam3_degree = 0;
            activity_degree = parseFloat($('#activity_degree_' + id).val());
            if (!activity_degree)
                activity_degree = 0;
            workbook_degree = parseFloat($('#workbook_degree_' + id).val());
            if (!workbook_degree)
                workbook_degree = 0;
            var total = exam1_degree + exam2_degree + exam3_degree + activity_degree + workbook_degree;
            if (total <= 0)
                total = $('#total_lbl_' + id).text(null)
            else
                $('#total_lbl_' + id).text(total)

        });
        $('.deg-input').trigger('keyup');
    </script>
    @stop