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
                    <li>Attendance</li>
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
                        <h3 class="title-section title-bar-high mb-40">Attendance <a id="go-back" href="/teacher"
                                class="btn btn-success btn-sm"> back
                                <i class="bi bi-skip-backward-fill"></i></a></h3>
                        <div class="orders-info">
                            <form class="form-horizontal" action="{{ route('post.group.attendance', $group_info->id) }}"
                                id="checkout-form" method="post" enctype="multipart/form-data">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-responsive">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" class="check" id="check-all" style="display: inline-block;"> Check All </th>
                                                <th>Student Name</th>
                                                <th>Attendance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                            @endphp
                                            @foreach ($data as $group)
                                                <tr>
                                                    <td>{{ $i++ }}
                                                        <input hidden name="student_id[{{$group->student_id }}]" value="{{$group->student_id }}">
                                                    </td>
                                                    <td>
                                                        {{ $group->student ? $group->student->name:'-' }}

                                                    </td>
                                                    <td>
                                                        <input type="checkbox" name="attendance[{{ $group->student_id }}]"
                                                          id="check"  class="check" value="1" style="display: inline-block;">
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="save-tbl-btn" style=" text-align: center;view-all-accent-btn">
                                        <button class="view-all-accent-btn" id="add-attendance" type="submit"
                                            value="save" style="display:inline-block">Save</button>
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
@section('js')

    <script>
        $(document).ready(function() {
            $('#check-all').click(function() {
                $('.check').prop('checked', this.checked);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#add-attendance').submit(function() {
                const submitButton = document.getElementById('add-attendance');
                submitButton.style.display = 'none !important';
                $('#add-attendance-btn').attr('disabled', true);
                
            });

            setTimeout(function() {
                $('#add-attendance-btn').removeAttr('disabled');
                  submitButton.style.display = 'inline-block';
            }, 24 * 60 * 60 * 1000); // 24 hours in milliseconds
            
        });
    </script>



@stop
