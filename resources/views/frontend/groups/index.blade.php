@extends('frontend.layouts.master')
@section('title', 'Courses')
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/gallary.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Courses Area</h1>
            <ul>
                <li><a href="{{ url('/')}}">Home</a> -</li>
                <li><a href="{{ url('/teacher')}}">Teacher Area</a> -</li>
                <li>{{$group_info->name}} Course</li>
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
                    <h3 class="title-section title-bar-high mb-40">{{$group_info->name}} Course - Students degrees</h3>
                    <div class="orders-info">
                        <form class="form-horizontal" id="checkout-form" method="post" enctype="multipart/form-data">
                            <div class="table-responsive">
                                <table class="table table-bordered table-responsive">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px">#</th>
                                            <th>Name</th>
                                            <th class="deg-fld">
                                                Progress Test 1 <br>(Units 1-3) 
                                                {{-- out of 15 Marks --}}
                                            </th>
                                            <th class="deg-fld">
                                                Progress Test 2 <br>(Units 4-6)
                                                {{-- out of 15 Marks --}}
                                            </th>
                                            <th class="deg-fld">
                                                Progress Test 3 <br>(Units 7-9)
                                                {{-- out of 60 Marks --}}
                                            </th>
                                            <th class="deg-fld">
                                                End of Course Test <br>(Units 1-12)
                                                {{-- out of 60 Marks --}}
                                            </th>
                                            <th class="deg-fld">
                                                Coursework<br>
                                                {{-- out of 5 Marks --}}
                                            </th>
                                            <th class="deg-fld">
                                                Workbook<br>
                                                {{-- out of 5 Marks --}}
                                            </th>
                                            <th class="deg-fld text-center">Overall</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $counter = 1 ?>
                                        @foreach($group_students as $group)
                                        <tr>
                                            <th class="text-center">{{$counter}}</th>
                                            <td>{{$group->student ? $group->student->name : "-"}}</td>
                                            <td><input type="text" value="{{$group->exam1_degree}}" name="exam1_degree[{{$group->student_id}}]" id="exam1_degree_{{$group->student_id}}" data_id="{{$group->student_id}}" placeholder="15" class="deg-input tes"></td>
                                            <td><input type="text" value="{{$group->exam2_degree}}" name="exam2_degree[{{$group->student_id}}]" id="exam2_degree_{{$group->student_id}}" data_id="{{$group->student_id}}" placeholder="15" class="deg-input"></td>
                                            <td><input type="text" value="{{$group->exam3_degree}}" name="exam3_degree[{{$group->student_id}}]" id="exam3_degree_{{$group->student_id}}" data_id="{{$group->student_id}}" placeholder="60" class="deg-input"></td>
                                            <td><input type="text" value="{{$group->exam4_degree}}" name="exam4_degree[{{$group->student_id}}]" id="exam4_degree_{{$group->student_id}}" data_id="{{$group->student_id}}" placeholder="60" class="deg-input"></td>
                                            <td><input type="text" value="{{$group->activity_degree}}" name="activity_degree[{{$group->student_id}}]" id="activity_degree_{{$group->student_id}}" data_id="{{$group->student_id}}" placeholder="Activity" class="deg-input"></td>
                                            <td><input type="text" value="{{$group->workbook_degree}}" name="workbook_degree[{{$group->student_id}}]" id="workbook_degree_{{$group->student_id}}" data_id="{{$group->student_id}}" placeholder="Workbook" class="deg-input"></td>
                                            <td class="text-center"><label id="total_lbl_{{$group->student_id}}">{{$group->total_degree}}</label></td>
                                        </tr>
                                        <?php $counter++ ?>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="save-tbl-btn">
                                    <button class="view-all-accent-btn" type="submit" value="Login">Save</button>
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
        exam4_degree = parseFloat($('#exam4_degree_' + id).val());
        if (!exam4_degree)
            exam4_degree = 0;
        activity_degree = parseFloat($('#activity_degree_' + id).val());
        if (!activity_degree)
            activity_degree = 0;
        workbook_degree = parseFloat($('#workbook_degree_' + id).val());
        if (!workbook_degree)
            workbook_degree = 0;
        var total = exam1_degree + exam2_degree + exam3_degree + exam4_degree + activity_degree + workbook_degree;
        if (total <= 0)
            total = $('#total_lbl_' + id).text(null)
        else
            $('#total_lbl_' + id).text(total)

    });
    $('.deg-input').trigger('keyup');
</script>
@stop