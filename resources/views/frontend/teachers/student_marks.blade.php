@extends('frontend.layouts.master')
@section('title', 'Courses')
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
    <div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/gallary.jpg') }}');">
        <div class="container">
            <div class="pagination-area">
                <h1>Courses Area</h1>
                <ul>
                    <li><a href="{{ url('/') }}">Home</a> -</li>
                    <li><a href="{{ url('/teacher') }}">Teacher Area</a> -</li>
                    <li>Course</li>
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
                        <h3 class="title-section title-bar-high mb-40">Courses Students <a id="go-back"
                                href="/teacher"  class="btn btn-success btn-sm"> back
                                <i class="bi bi-skip-backward-fill"></i></a></h3>
                        <div class="orders-info">
                            <div class="table-responsive">
                                <table class="table table-bordered table-responsive">
                                    <thead>
                                        <tr>
                                            <th style="width: 120px;">Student image</th>
                                            <th>Student Name</th>
                                            <th>Progress Test 1 (Units 1-3)</th>
                                            <th>Progress Test 2 (Units 4-6)</th>
                                            <th>Progress Test 3 (Units 7-9)</th>
                                            <th>End of Course Test (Units 1-12)</th>
                                            <th>Activity Degree</th>
                                            <th>Workbook Degree</th>
                                            <th>Total Degree </th>
                                            <th>Actions</th>
                                            {{-- <th>students No.</th>
                                                <th>Exam Dates</th>
                                                <th>Chat</th>
                                                <th>Teacher Library</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $group)
                                            <tr>
                                                <td>
                                                    @if ($group->student->image != null)
                                                        <img src="<?= url($group->student->image) ?>"
                                                            style="margin-left: 26px; width: 50%; border-radius: 50%;">
                                                    @else
                                                        <img src="<?= url('assets/oxford/img/students/avatar.png') ?>"
                                                            style="margin-left: 26px; width: 50%; border-radius: 50%;">
                                                    @endif
                                                </td>
                                                <td>{{ $group->student->name }}</td>
                                                @if ($group->exam1_degree != null)
                                                    <td><span style="color:#002147">{{ $group->exam1_degree }}</span></td>
                                                @else
                                                    <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                                                @endif

                                                @if ($group->exam2_degree != null)
                                                    <td><span style="color:#002147">{{ $group->exam2_degree }}</span></td>
                                                @else
                                                    <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                                                @endif

                                                @if ($group->exam3_degree != null)
                                                    <td><span style="color:#002147">{{ $group->exam3_degree }}</span></td>
                                                @else
                                                    <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                                                @endif

                                                @if ($group->exam4_degree != null)
                                                    <td><span style="color:#002147">{{ $group->exam4_degree }}</span></td>
                                                @else
                                                    <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                                                @endif

                                                @if ($group->activity_degree != null)
                                                    <td><span style="color:#002147">{{ $group->activity_degree }}</span>
                                                    </td>
                                                @else
                                                    <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                                                @endif

                                                @if ($group->workbook_degree != null)
                                                    <td><span style="color:#002147">{{ $group->workbook_degree }}</span>
                                                    </td>
                                                @else
                                                    <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                                                @endif

                                                @if ($group->workbook_degree != null)
                                                    <td><span style="color:#002147">{{ $group->total_degree }}</span></td>
                                                @else
                                                    <td><span style="color:red" class="bi bi-dash-circle-fill"></span></td>
                                                @endif

                                                <td style="min-width: 200px;">
                                                        @if ($group->has_evaluation == 0)
                                                        <a style="background-color:#ffae00"
                                                            href="{{ route('teacher.evaluate.view', ['group_id' => Crypt::encrypt($group_id), 'student_id' => Crypt::encrypt($group->student_id)]) }}"
                                                            title="Exam Dates" class="btn btn-primary btn-sm">
                                                            <i class="bi bi-star-fill"></i></a>
                                                            @else
                                                            <strong
                                                            style="color:#002147">evaluated</strong>
                                                            @endif
                                                            <a href="{{ route('teacher.remove.student', ['group_id' => Crypt::encrypt($group_id), 'student_id' => Crypt::encrypt($group->student_id)]) }}"
                                                                title="Exam Dates" class="btn btn-danger btn-sm">
                                                                <i class="bi bi-trash-fill"></i></a>
                                                        
                                                    </td>

                                                

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
