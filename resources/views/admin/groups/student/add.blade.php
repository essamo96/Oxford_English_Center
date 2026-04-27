@extends('admin.layout.master')

@section('title')
    إضافة طلاب للمجموعة - {{ $info->name }}
@stop

@section('page-title')
    إضافة طلاب للمجموعة: {{ $info->name }}
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('groups.view') }}" class="text-muted text-hover-info">إدارة المجموعات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('groups.student.view', ['id' => Crypt::encrypt($info->id)]) }}"
            class="text-muted text-hover-info">{{ $info->name }}</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">إضافة طلاب</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">إضافة طلاب للمجموعة</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('groups.student.view', ['id' => Crypt::encrypt($info->id)]) }}"
                    class="btn btn-sm btn-light btn-active-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.masterLayouts.error')
            <form role="form" method="post" action="" class="form d-flex flex-column gap-7">
                {{ csrf_field() }}
                <div id="students_repeater">
                    <div class="form-group">
                        <div data-repeater-list="students_list">
                            <div data-repeater-item class="student-item mb-5 border-bottom pb-5">
                                <div class="row g-5 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">اسم الطالب</label>
                                        <input type="text" class="form-control form-control-solid student_name"
                                            placeholder="ابحث عن الطالب..." autocomplete="off">
                                        <input type="hidden" name="student_name[]" class="search-val">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">دفعة الرسوم</label>
                                        <input type="number" step="0.01" class="form-control form-control-solid"
                                            name="student_fee_paid[]" placeholder="0.00">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">إجمالي الرسوم</label>
                                        <input type="number" step="0.01" class="form-control form-control-solid"
                                            name="student_fee_total[]" placeholder="0.00">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">دفعة الكتب</label>
                                        <input type="number" step="0.01" class="form-control form-control-solid"
                                            name="student_book_paid[]" placeholder="0.00">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">إجمالي الكتب</label>
                                        <input type="number" step="0.01" class="form-control form-control-solid"
                                            name="student_book_total[]" placeholder="0.00">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-icon btn-light-danger removestudent">
                                            <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span
                                                    class="path2"></span><span class="path3"></span><span
                                                    class="path4"></span><span class="path5"></span></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-5">
                        <button type="button" class="btn btn-light-primary addstudent">
                            <i class="ki-duotone ki-plus fs-3"></i> إضافة سطر جديد
                        </button>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('groups.student.view', ['id' => Crypt::encrypt($info->id)]) }}"
                        class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <link href="{{ asset('assets/admin/global/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/admin/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            var options1 = {
                source: "{{ route('groups.student.search') }}",
                minLength: 1,
                focus: function(event, ui) {
                    $(this).val(ui.item.label);
                    return false;
                },
                select: function(event, ui) {
                    $(this).val(ui.item.label);
                    $(this).closest('.student-item').find('.search-val').val(ui.item.value);
                    return false;
                }
            };

            // Initialize existing
            $(".student_name").autocomplete(options1);

            // Add new student row
            $('.addstudent').on('click', function(e) {
                e.preventDefault();
                var $repeaterList = $('[data-repeater-list="students_list"]');
                var $newItem = $repeaterList.find('[data-repeater-item]').first().clone();

                // Clear values
                $newItem.find('input').val('');
                $newItem.find('.search-val').val('');

                // Append and re-init autocomplete
                $newItem.appendTo($repeaterList);
                $newItem.find('.student_name').autocomplete(options1);
            });

            // Remove student row
            $(document).on('click', '.removestudent', function(e) {
                e.preventDefault();
                var items = $('[data-repeater-item]');
                if (items.length > 1) {
                    $(this).closest('[data-repeater-item]').fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    // Just clear the first one if it's the only one
                    $(this).closest('[data-repeater-item]').find('input').val('');
                }
            });
        });
    </script>
@stop
