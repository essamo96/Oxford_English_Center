@extends('admin.layout.master')

@section('title', 'مستوى تقدم المجموعات')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">
    <a href="{{ route('groups.view') }}" class="text-muted text-hover-info">المجموعات</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">مستوى التقدم</li>
@stop

@section('page-content')
@php $active_menu = 'progress_menu'; @endphp

<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> البحث والفلاتر
            </span>
        </div>
        <div class="card-toolbar">
            <button type="reset" id="reset_button" class="btn btn-light-danger btn-sm shadow-sm" title="إعادة تعيين البحث">
                <i class="bi bi-arrow-clockwise fs-4 me-1"></i> تصفية الفلاتر
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <form role="form" class="form" id="filter_form">
            <div class="row gx-5">
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">اسم المدرس</label>
                    <input type="text" name="teacher_name" id="teacher_name" class="form-control form-control-solid searchable" placeholder="البحث بالمدرس...">
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">اسم المجموعة</label>
                    <select name="group_name" id="group_name" class="form-select form-select-solid" data-control="select2" data-placeholder="اختر المجموعة">
                        <option value=""></option>
                        @foreach($groups as $group)
                            <option value="{{ $group->name }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">البرنامج</label>
                    <select name="program_id" id="program_id" class="form-select form-select-solid" data-control="select2" data-placeholder="اختر البرنامج">
                        <option value=""></option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}">{{ $program->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-graph-up fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i> مستوى تقدم المجموعات
            </span>
        </div>
        <div class="card-toolbar gap-2">
            <a href="{{ URL::previous() }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="progress_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-150px text-center"> اسم المجموعة </th>
                        <th class="min-w-150px text-center"> اسم المدرس </th>
                        <th class="min-w-150px text-center"> اسم البرنامج </th>
                        <th class="min-w-125px text-center"> تاريخ التقييم </th>
                        <th class="min-w-150px text-center"> مستوى التقدم </th>
                        <th class="min-w-100px text-center"> العمليات </th>
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

    <!-- Modern Details Modal -->
    <div class="modal fade" id="details_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-800px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body scroll-y pt-0 pb-15" id="modal_content">
                    <div class="text-center py-10">
                        <span class="spinner-border w-50px h-50px" role="status"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    var table;
    var tableId = 'progress_table';
    var customAjaxUrl = "{{ route('progress_menu.list') }}";
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "name", name: "name", className: "fw-bold" },
        { data: "teacher_name", name: "teacher_name" },
        { data: "program_name", name: "program_name" },
        { data: "progress_at", name: "progress_at", className: "text-center" },
        { data: "progress", name: "progress", className: "text-center" },
        { data: "actions", name: "actions", className: "text-center", orderable: false, searchable: false }
    ];

    var filterFields = ['#teacher_name', '#group_name', '#program_id'];

    $(document).ready(function() {
        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $('#filter_form')[0].reset();
            $('#filter_form select').val('').trigger('change');
            table.ajax.reload();
        });

        $('#filter_form input').on('keyup change', function() {
            table.draw();
        });

        $('#filter_form select').on('change', function() {
            table.draw();
        });
    });

    function deleteProgress(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم حذف مستوى التقدم لهذه المجموعة!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء',
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-light"
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('progress_menu.delete') }}", {
                    id: id,
                    _token: "{{ csrf_token() }}"
                }, function(data) {
                    if (data.status == 'success') {
                        Swal.fire({
                            text: data.message,
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'حسناً',
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            text: data.message,
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'حسناً',
                            customClass: { confirmButton: "btn btn-primary" }
                        });
                    }
                });
            }
        });
    }

    function showGroupModal(id) {
        fetchModalContent('{{ route('groups.details') }}', { id: id });
    }

    function showTeacherModal(id) {
        fetchModalContent('{{ route('teachers.details') }}', { id: id });
    }

    function showProgramModal(id) {
        fetchModalContent('{{ route('programs.details') }}', { id: id });
    }

    function fetchModalContent(url, data) {
        $('#modal_content').html('<div class="text-center py-10"><span class="spinner-border w-50px h-50px" role="status"></span></div>');
        $('#details_modal').modal('show');
        $.ajax({
            url: url,
            type: 'POST',
            data: { ...data, _token: '{{ csrf_token() }}' },
            success: function(response) {
                $('#modal_content').html(response);
            },
            error: function() {
                $('#modal_content').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل البيانات</div>');
            }
        });
    }
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'progress_menu'])
@stop

