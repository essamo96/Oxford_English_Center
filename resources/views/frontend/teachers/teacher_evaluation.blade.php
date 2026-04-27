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
                    <li>{{ $student_info->student->name }} Evaluate</li>
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
                        <h3 class="title-section title-bar-high mb-40"><span
                                style="color:rgb(255, 187, 0)">{{ $student_info->group->name }}</span> Course - Students
                            <span style="color:rgb(255, 187, 0)"> {{ $student_info->student->name }}</span> Evaluate Page
                        </h3>
                        <div class="orders-info">
                            <form class="form-horizontal" id="checkout-form" action="{{ route('teacher.evaluate.post') }}"
                                method="post" enctype="multipart/form-data">
                                <div class="row" style="justify-content: space-between ; display: flex;">
                                    <select class="form-select form-control activeG  col-5" name="evaluation_sort" style="margin-block: 10px; width:50% ;margin: 2%;"
                                        aria-label="Default select example">
                                        <option value="1" selected>التقييم الاول - الاسبوع 1-3</option>
                                        <option value="2">التقييم الثاني  - الاسبوع 4-6</option>
                                        <option value="3">التقييم الثالث  - الاسبوع 7-9</option>
                                        <option value="4">التقييم النهائي - الاسبوع 10-12</option>

                                    </select>
                                    <select class="form-select form-control activeG  col-5" name="progress" style="margin-block: 10px; width:50%;  margin: 2%;"
                                        aria-label="Default select example">
                                        <option value="30" selected>الوحدات من - 1-3</option>
                                        <option value="60">الوحدات من  -  4-6</option>
                                        <option value="90">الوحدات من  -  7-9</option>
                                        <option value="100">الوحدة الاخيرة</option>

                                    </select>

                                </div>
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
                                                        {{ $question->name_en }}
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
                                            <input type="hidden" name="g_id"
                                                value="{{ Crypt::encrypt($student_info->group_id) }} ">
                                            <input type="hidden" name="s_id"
                                                value="{{ Crypt::encrypt($student_info->student_id) }} ">

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
