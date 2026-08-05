@extends('admin.layout.master')

@section('title', 'بنك الأسئلة')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">بنك الأسئلة</li>
@stop

@section('page-content')
<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> البحث في الأسئلة
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form role="form" class="form">
            <div class="row gx-5">
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">نص السؤال</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="بحث...">
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">المهارة</label>
                    <select name="skill_id" id="skill_id" class="form-select form-select-solid searchable" data-control="select2">
                        <option value="">الكل</option>
                        @foreach($skills as $skill)
                            <option value="{{ $skill->id }}">{{ $skill->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <label class="form-label fw-semibold">النوع</label>
                    <select name="type" id="type" class="form-select form-select-solid searchable">
                        <option value="">الكل</option>
                        <option value="mcq">اختيار من متعدد</option>
                        <option value="true_false">صح/خطأ</option>
                        <option value="text">إجابة نصية</option>
                        <option value="voice">إجابة صوتية</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <label class="form-label fw-semibold">الصعوبة</label>
                    <select name="difficulty" id="difficulty" class="form-select form-select-solid searchable">
                        <option value="">الكل</option>
                        <option value="easy">سهل</option>
                        <option value="medium">متوسط</option>
                        <option value="hard">صعب</option>
                        <option value="custom">مخصص</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6 mb-4 d-flex align-items-end">
                    <button type="reset" id="reset_button" class="btn btn-light-danger btn-icon w-40px h-40px shadow-sm" title="إعادة تعيين البحث">
                        <i class="bi bi-arrow-clockwise fs-3"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-book-open fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> بنك الأسئلة
            </span>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.exam_questions.add')
                <a href="{{ route('exam_questions.add') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة سؤال
                </a>
                <a href="{{ route('exam_questions.bulk_add') }}" class="btn btn-light-primary btn-sm">
                    <i class="bi bi-list-check"></i> إضافة عدة أسئلة
                </a>
            @endcan
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="exam_questions_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-300px text-center"> نص السؤال </th>
                        <th class="min-w-100px text-center"> النوع </th>
                        <th class="min-w-100px text-center"> الصعوبة </th>
                        <th class="min-w-100px text-center"> المهارة </th>
                        <th class="min-w-100px text-center"> الحالة </th>
                        <th class="text-center min-w-100px"> العمليات </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center"></tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('modal')
    @include('admin.layout.masterLayouts.modal')
@stop

@section('js')
<script>
    var table;
    var tableId = 'exam_questions_table';
    var customAjaxUrl = "{{ route('exam_questions.list') }}";
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "question_text", name: "question_text", className: "text-start" },
        { data: "type", name: "type" },
        { data: "difficulty", name: "difficulty" },
        { data: "skill", name: "skill" },
        { data: "status", name: "status" },
        { data: "actions", name: "actions", orderable: false, searchable: false }
    ];

    var filterFields = ['#title', '#skill_id', '#type', '#difficulty'];

    $(document).ready(function() {
        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            $('.searchable').trigger('change');
            table.ajax.reload();
        });

        $(document).on('click', ".status", function () {
            var id = $(this).data('href');
            var item = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('exam_questions.status') }}",
                data: {'id': id, '_token': '{{ csrf_token() }}'},
                success: function (data) {
                    if (data.type == 'yes') {
                        item.removeClass("btn-light-danger").addClass("btn-light-success").html('<i class="bi bi-check-circle fs-5"></i> فعال');
                    } else if (data.type == 'no') {
                        item.removeClass("btn-light-success").addClass("btn-light-danger").html('<i class="bi bi-x-circle fs-5"></i> غير فعال');
                    }
                    toastr[data.status](data.message);
                }
            });
        });

        $(document).on('click', ".delete-confirm", function () {
            var id = $("#delete_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('exam_questions.delete') }}",
                data: {'id': id, '_token': '{{ csrf_token() }}'},
                success: function (data) {
                    $('#confirm').modal('hide');
                    toastr[data.status](data.message);
                    table.ajax.reload();
                }
            });
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'exam_questions'])
@stop
