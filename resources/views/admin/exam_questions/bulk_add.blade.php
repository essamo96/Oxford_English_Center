@extends('admin.layout.master')
@section('title', 'إضافة عدة أسئلة')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">
    <a href="{{ route('exam_questions.view') }}" class="text-muted text-hover-info">بنك الأسئلة</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">إضافة عدة أسئلة</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">إضافة عدة أسئلة دفعة واحدة</span>
            <span class="text-muted fs-7">شاشة مبسطة لإدخال سريع لعدة أسئلة معاً. لإضافة صورة/صوت أو خيارات أكثر من 4، استخدم "إضافة سؤال" العادية.</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ route('exam_questions.view') }}" class="btn btn-sm btn-light btn-active-light-primary">
                <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')

        <form role="form" method="post" action="{{ route('exam_questions.bulk_add') }}" class="form">
            {{ csrf_field() }}

            <div class="questions_repeater">
                {{-- template row (index 0), cloned by JS with incrementing index --}}
                <div class="question_row card bg-light-primary border-0 mb-5" data-index="0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold fs-5 row-number">السؤال 1</span>
                            <button type="button" class="btn btn-icon btn-light-danger btn-sm remove-row" title="حذف هذا السؤال">
                                <i class="bi bi-trash fs-4"></i>
                            </button>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <label class="fs-7 fw-semibold mb-1">النوع</label>
                                <select name="rows[0][type]" class="form-select form-select-solid row-type">
                                    <option value="mcq">اختيار من متعدد</option>
                                    <option value="true_false">صح / خطأ</option>
                                    <option value="text">إجابة نصية</option>
                                    <option value="voice">إجابة صوتية</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-7 fw-semibold mb-1">الصعوبة</label>
                                <select name="rows[0][difficulty]" class="form-select form-select-solid">
                                    <option value="easy">سهل</option>
                                    <option value="medium" selected>متوسط</option>
                                    <option value="hard">صعب</option>
                                    <option value="custom">مخصص</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-7 fw-semibold mb-1">المهارة</label>
                                <select name="rows[0][skill_id]" class="form-select form-select-solid">
                                    <option value="">بدون تصنيف</option>
                                    @foreach($skills as $skill)
                                        <option value="{{ $skill->id }}">{{ $skill->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-7 fw-semibold mb-1">الدرجة</label>
                                <input type="number" step="0.5" min="0.5" value="1" name="rows[0][marks]" class="form-control form-control-solid">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="fs-7 fw-semibold mb-1">نص السؤال</label>
                            <textarea name="rows[0][question_text]" class="form-control form-control-solid" rows="2" placeholder="اكتب نص السؤال هنا..."></textarea>
                        </div>

                        {{-- MCQ options (4 fixed slots) --}}
                        <div class="mcq_options">
                            <label class="fs-7 fw-semibold mb-2 d-block">الخيارات (حدد الإجابة الصحيحة)</label>
                            <div class="row g-2">
                                @for($i = 0; $i < 4; $i++)
                                <div class="col-md-6 mb-2 d-flex align-items-center gap-2">
                                    <input type="radio" name="rows[0][correct_option]" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }}>
                                    <input type="text" name="rows[0][options][{{ $i }}]" class="form-control form-control-solid" placeholder="الخيار {{ $i + 1 }}">
                                </div>
                                @endfor
                            </div>
                        </div>

                        {{-- True/False options (fixed) --}}
                        <div class="tf_options d-none">
                            <label class="fs-7 fw-semibold mb-2 d-block">الإجابة الصحيحة</label>
                            <div class="d-flex gap-4">
                                <label><input type="radio" name="rows[0][tf_correct]" value="true" checked> صح</label>
                                <label><input type="radio" name="rows[0][tf_correct]" value="false"> خطأ</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-6">
                <button type="button" id="add_row_btn" class="btn btn-light-primary">
                    <i class="bi bi-plus-lg"></i> إضافة سؤال آخر
                </button>
                <span class="text-muted" id="rows_count_label">عدد الأسئلة: 1</span>
            </div>

            <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-primary">حفظ جميع الأسئلة</button>
                <a href="{{ route('exam_questions.view') }}" class="btn btn-light ms-2">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    (function () {
        var rowIndex = 1; // next index to assign (row 0 already exists)

        function toggleOptions($row) {
            var type = $row.find('.row-type').val();
            $row.find('.mcq_options').toggleClass('d-none', type !== 'mcq');
            $row.find('.tf_options').toggleClass('d-none', type !== 'true_false');
        }

        function renumberRows() {
            $('.question_row').each(function (i) {
                $(this).find('.row-number').text('السؤال ' + (i + 1));
            });
            $('#rows_count_label').text('عدد الأسئلة: ' + $('.question_row').length);
        }

        // initial state for the template row
        toggleOptions($('.question_row[data-index="0"]'));

        $(document).on('change', '.row-type', function () {
            toggleOptions($(this).closest('.question_row'));
        });

        $('#add_row_btn').on('click', function () {
            var $template = $('.questions_repeater .question_row').first();
            var $clone = $template.clone();
            var newIndex = rowIndex++;

            // rewrite all name="rows[0][...]" attributes to rows[newIndex][...]
            $clone.find('[name]').each(function () {
                var name = $(this).attr('name').replace(/rows\[\d+\]/, 'rows[' + newIndex + ']');
                $(this).attr('name', name);
            });
            $clone.attr('data-index', newIndex);

            // reset values on the clone
            $clone.find('textarea').val('');
            $clone.find('input[type="text"]').val('');
            $clone.find('input[type="number"]').val(1);
            $clone.find('select.row-type').val('mcq');
            $clone.find('select').not('.row-type').prop('selectedIndex', 0);
            $clone.find('input[type="radio"]').prop('checked', false);
            $clone.find('input[type="radio"][value="0"], input[type="radio"][value="true"]').prop('checked', true);

            $('.questions_repeater').append($clone);
            toggleOptions($clone);
            renumberRows();
        });

        $(document).on('click', '.remove-row', function () {
            if ($('.question_row').length <= 1) {
                toastr.warning('يجب أن يبقى سؤال واحد على الأقل في القائمة.');
                return;
            }
            $(this).closest('.question_row').fadeOut(200, function () {
                $(this).remove();
                renumberRows();
            });
        });

        // client-side check before submit, mirrors the server-side Arabic messages
        $('form').on('submit', function (e) {
            var errors = [];
            $('.question_row').each(function (i) {
                var rowNum = i + 1;
                var $row = $(this);
                var type = $row.find('.row-type').val();
                var text = $row.find('textarea').val().trim();

                if (text === '') {
                    errors.push('السؤال رقم ' + rowNum + ': نص السؤال مطلوب.');
                }
                if (type === 'mcq') {
                    var filled = $row.find('.mcq_options input[type="text"]').filter(function () {
                        return $(this).val().trim() !== '';
                    });
                    if (filled.length < 2) {
                        errors.push('السؤال رقم ' + rowNum + ': يجب إدخال خيارين على الأقل.');
                    }
                }
            });

            if (errors.length) {
                e.preventDefault();
                toastr.error(errors.join('<br>'));
            }
        });
    })();
</script>
@stop
