@extends('admin.layout.master')

@section('title', 'إدارة اشعارات المجموعات المنتهية')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">اشعارات المجموعات المنتهية</li>
@stop

@section('page-content')
@php $active_menu = 'closed_classes'; @endphp

<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> البحث
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form role="form" class="form" id="search_form">
            <div class="row gx-5">
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">البحث العام</label>
                    <input type="text" id="name" name="name" class="form-control form-control-solid searchable" placeholder="اسم المجموعة أو المدرس..." />
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">المدرس</label>
                    <select id="teacher_id" name="teacher_id" class="form-select form-select-solid searchable" data-control="select2" data-placeholder="اختر المدرس...">
                        <option value=""></option>
                        @foreach($teachers ?? [] as $teacher)
                            @php
                                $displayName = $teacher->name ?: ($teacher->username ?: ($teacher->email ?: 'مدرس #' . $teacher->id));
                            @endphp
                            <option value="{{ $teacher->id }}">{{ $displayName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">تاريخ الإغلاق</label>
                    <input type="date" id="closed_date" name="closed_date" class="form-control form-control-solid searchable" placeholder="اختر التاريخ" />
                </div>
                <div class="col-lg-3 col-md-6 mb-4 d-flex align-items-end pb-1">
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
                <i class="ki-duotone ki-time fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> المجموعات المنتهية
            </span>
        </div>
        <div class="card-toolbar gap-2">
            <a href="{{ URL::previous() }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center text-nowrap" id="closed_classes_table">
                <thead>
                    <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="text-center w-50px"> # </th>
                        <th class="text-center min-w-150px"> اسم المدرس </th>
                        <th class="text-center min-w-150px"> اسم الكورس </th>
                        <th class="text-center min-w-150px"> تاريخ العملية </th>
                        <th class="text-center min-w-100px"> الإجراءات </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center"></tbody>
            </table>
        </div>
    </div>
</div>

@include('admin.layout.masterLayouts.modal')
@stop

@section('js')
<script>
    var table;
    var tableId = 'closed_classes_table';
    var columns = [
        { data: 'id', name: 'id', render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "teacher_name", name: "teacher_name" },
        { data: "group_name", name: "group_name" },
        { data: "closed_date", name: "closed_date" },
        { data: "actions", name: "actions", orderable: false, searchable: false }
    ];

    var filterFields = ['#name', '#teacher_id', '#closed_date'];

    $(document).on('click', '#reset_button', function(e) {
        e.preventDefault();
        $('#search_form')[0].reset();
        $('#teacher_id').val('').trigger('change');
        table.ajax.reload();
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'closed_classes'])
@stop
