@extends('frontend.layouts.master')
@section('title', 'Courses')
@section('content')
    <div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/gallary.jpg') }}');">
        <div class="container">
            <div class="pagination-area">
                <h1>Evaluate Area</h1>
                <ul>
                    <li><a href="{{ url('/') }}">Home</a> -</li>
                    <li><a href="{{ url('/teacher') }}">Teacher Area</a> -</li>
                    <li>{{ $teacher->name }} Evaluate</li>
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
                        <h3 class="title-section title-bar-high mb-40">
                            <span style="color:rgb(255, 187, 0)"> {{ $teacher->name }}</span> Evaluate Page
                        </h3>
                        <div class="orders-info">
                            <form class="form-horizontal" id="checkout-form" action="{{ route('teacher.evaluate.post') }}"
                                method="post" enctype="multipart/form-data">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px">#</th>
                                                <th>Question</th>
                                                <th class="deg-fld">
                                                    مقبول - 1
                                                    {{-- out of 15 Marks --}}
                                                </th>
                                                <th class="deg-fld">
                                                    جيد - 2
                                                    {{-- out of 15 Marks --}}
                                                </th>
                                                <th class="deg-fld">
                                                    جيد جدا - 3
                                                    {{-- out of 60 Marks --}}
                                                </th>
                                                <th class="deg-fld">
                                                    ممتاز - 4
                                                    {{-- out of 60 Marks --}}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $counter = 1; ?>
                                            @foreach ($questions as $key => $question)
                                                <tr>
                                                    <th class="text-center">{{ $counter }}</th>
                                                    <td>
                                                        {{ $question->name }}
                                                        <input type="hidden" name="question_ids[]"
                                                            value="{{ $question->id }}">
                                                    </td>
                                                    <td>
                                                        <input type="radio" value="1"
                                                            name="evaluate_degree[{{ $question->id }}][]"
                                                            id="evaluate_degree_" data_id="" required placeholder=""
                                                            class="deg-input tes form-check-input">

                                                    </td>
                                                    <td><input type="radio" value="2"
                                                            name="evaluate_degree[{{ $question->id }}][]"
                                                            id="evaluate_degree_" data_id="" placeholder=""
                                                            class="deg-input tes form-check-input"></td>
                                                    <td><input type="radio" value="3"
                                                            name="evaluate_degree[{{ $question->id }}][]"
                                                            id="evaluate_degree_" data_id="" placeholder=""
                                                            class="deg-input tes form-check-input"></td>
                                                    <td><input type="radio" value="4"
                                                            name="evaluate_degree[{{ $question->id }}][]"
                                                            id="evaluate_degree_" data_id="" placeholder=""
                                                            class="deg-input tes form-check-input"></td>

                                                </tr>
                                                <?php $counter++; ?>
                                            @endforeach
                                            <td colspan="2" style="text-align: right;" class="table-active"><input
                                                    type="text" value="" name="evaluate1_total" id="total"
                                                    data_id="" placeholder="" class="deg-input tes"></td>
                                            <td colspan="4" class="table-active"><strong>المجموع</strong></td>
                                        
                                            {{-- <input type="hidden" name="s_id"
                                                value="{{ Crypt::encrypt($student_info->student_id) }} "> --}}

                                        </tbody>
                                    </table>
                                    <table class="table table-bordered table-responsive">
                                        <thead>
                                            <tr>

                                                <th
                                                    style="text-align: right; padding-right: 29px; text-decoration: 2px underline; padding-bottom: 5px;">
                                                    ملاحظات مدرب/ة المستوي :</th>

                                            </tr>
                                        </thead>
                                        <tbody>


                                            <tr>
                                                <td>
                                                    <textarea style="  width: 100%;  box-sizing: border-box;" name="note" rows="6"></textarea>
                                                </td>
                                            </tr>


                                        </tbody>
                                    </table>
                                    <div class="save-tbl-btn">
                                        <button class="view-all-accent-btn" id="saveEvaluation" type="submit"
                                            value="Login">Save</button>
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
        .save-tbl-btn {
            text-align: center;
        }

        .view-all-accent-btn {
            width: 15%;
        }
    </style>
@stop
@section('js')
    <script>
        const radioButtons = document.querySelectorAll('input[type="radio"]');
        const resultInput = document.getElementById('total');

        radioButtons.forEach((radioButton) => {
            radioButton.addEventListener('click', () => {
                let sum = 0;
                radioButtons.forEach((radioButton) => {
                    if (radioButton.checked) {
                        sum += Number(radioButton.value);
                    }
                });
                resultInput.value = sum;
            });
        });
    </script>
@stop
