@extends('frontend.layouts.dashboard')
@section('title', 'Student Degrees')
@section('page-title', 'Exam Degrees')
@section('breadcrumb')
    <a href="{{ url('/student') }}">Student Area</a>
    <span class="sep">/</span>
    <span>{{ $student_group->group->name }} exams degrees</span>
@endsection
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/gallary.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Student Degrees</h1>
            <ul>
                <li><a href="{{ url('/')}}">Home</a> -</li>
                <li><a href="{{ url('/student')}}">Student Area</a> -</li>
                <li>{{$student_group->group->name}} exams degrees</li>
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
                    <h3 class="title-section title-bar-high mb-40">{{$student_group->group->name}} Exams - Student degrees</h3>
                    <div class="orders-info">
                        <div class="table-responsive">
                            <table class="table table-bordered table-responsive">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th >Course name</th>
                                        <th class="deg-fld">Progress Test 1</th>
                                        <th class="deg-fld">Progress Test 2</th>
                                        <th class="deg-fld">Final Exam</th>
                                        <th class="deg-fld">Activity</th>
                                        <th class="deg-fld">Workbook</th>
                                        <th class="deg-fld">Overall</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>{{$student_group->group->id}}</th>
                                        <td>{{$student_group->group->name}}</td>
                                        <td>{{$student_group->exam1_degree}}</td>
                                        <td>{{$student_group->exam2_degree}}</td>
                                        <td>{{$student_group->exam3_degree}}</td>
                                        <td>{{$student_group->activity_degree}}</td>
                                        <td>{{$student_group->workbook_degree}}</td>
                                        <td>{{$student_group->total_degree}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('css')
@stop
@section('js')
@stop