@extends('frontend.layouts.master')
@section('title', 'Group Exam')
@section('css')
    <style>
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
    <div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/gallary.jpg') }}');">
        <div class="container">
            <div class="pagination-area">
                <h1>Courses Area</h1>
                <ul>
                    <li><a href="{{ url('/') }}">Home</a> -</li>
                    <li><a href="{{ url('/teacher') }}">Teacher Area</a> -</li>
                    <li>Group Exam</li>
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
                        <h3 class="title-section title-bar-high mb-40">Group  Exam<a id="go-back"
                                href="/teacher"  class="btn btn-success btn-sm"> back
                                <i class="bi bi-skip-backward-fill"></i></a></h3>
                        <div class="orders-info">
                            <div class="table-responsive">
                                <table class="table table-bordered table-responsive">
                                    <thead>
                                        <tr>
                                            <th style="width: 120px;">Group Name</th>
                                            <th>Progress Test 1</th>
                                            <th>Progress Test 2</th>
                                            <th>Final Exam</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $group)
                                            <tr>
                                               
                                                <td>{{ $group->group->name }}</td>
                                                @if ($group->progress_test1 != null)
                                                    <td><span style="color:#002147">{{ $group->progress_test1 }}</span></td>
                                                @else
                                                    <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                                                @endif

                                                @if ($group->progress_test2 != null)
                                                    <td><span style="color:#002147">{{ $group->progress_test2 }}</span></td>
                                                @else
                                                    <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                                                @endif

                                                @if ($group->final_exam != null)
                                                    <td><span style="color:#002147">{{ $group->final_exam }}</span></td>
                                                @else
                                                    <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                                                @endif
                                                
                                                    <td><a style="background-color:#ffae00"
                                                           href="{{ url('examDate/' . $group->group_id) }}"
                                                            title="Exam Dates" class="btn btn-primary btn-sm">
                                                            Edit / Add </a></td>
                                               

                                            </tr>
                                        @endforeach
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
    <style>
        .save-tbl-btn {
            text-align: center;
        }

        /* .view-all-accent-btn {
            width: 15%;
        } */
    </style>
@stop
@section('js')

@stop
