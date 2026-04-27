@extends('admin.layout.master')

@section('title', 'إدارة الأخبار')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة الأخبار</li>
@stop

@section('page-content')
@php $active_menu = 'news'; @endphp

<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> البحث والفلاتر
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form role="form" class="form">
            <div class="row gx-5">
                <div class="col-lg-4 col-md-6 mb-4">
                    <label class="form-label fw-semibold">عنوان الخبر</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="ابحث بعنوان الخبر...">
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">الحالة</label>
                    <select name="publish" id="publish" class="form-select form-select-solid searchable">
                        <option value="-1">الكل</option>
                        <option value="1">منشور</option>
                        <option value="0">غير منشور</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <label class="form-label fw-semibold">القسم</label>
                    <select name="category" id="category" class="form-select form-select-solid searchable">
                        <option value="-1">كل الأقسام</option>
                        @foreach($categories as $item)
                        <option value="{{ $item->id }}"> {{ $item->name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 d-flex align-items-end">
                    <button type="reset" id="reset_button" class="btn btn-light-info btn-icon w-40px h-40px shadow-sm" title="إعادة تعيين البحث">
                        <i class="ki-duotone ki-arrows-loop fs-3"><span class="path1"></span><span class="path2"></span></i>
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
                <i class="ki-duotone ki-element-plus fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> إدارة الأخبار
            </span>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.news.add')
                <a href="{{ route('news.cleaAllCache') }}" class="btn btn-light-danger btn-sm fw-bold">
                    <i class="ki-duotone ki-trash-square me-1 fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> حذف الكاش
                </a>
                <a href="{{ route('news.add') }}" class="btn btn-info btn-sm fw-bold">
                    <i class="ki-duotone ki-plus-square fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> إضافة خبر 
                </a>
            @endcan
            <a href="{{ url()->previous() }}" class="btn btn-light-info btn-sm fw-bold">
                <i class="ki-duotone ki-black-right me-1 fs-5"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="news_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-200px text-center"> الخبر </th>
                        <th class="min-w-125px text-center"> القسم </th>
                        @can('admin.news.publish')
                        <th class="min-w-100px text-center"> النشر </th>
                        @endcan
                        <th class="text-center min-w-125px"> العمليات </th>
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
    var tableId = 'news_table';
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "title", name: "title", orderable: true },
        { data: "category_name", name: "category_name", orderable: true },
        @can('admin.news.publish')
        { data: "publish", name: "publish", orderable: true, searchable: false },
        @endcan
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#title', '#publish', '#category'];

    $(document).ready(function() {
        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            table.ajax.reload();
        });

        $(document).on('click', ".publish", function () {
            var id = $(this).data('href');
            var item = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('news.publish') }}",
                data: {
                    'id': id,
                    '_token': '{{ csrf_token() }}'
                }
            }).done(function (data) {
                if(typeof toastr !== 'undefined') toastr[data.status](data.message);
                table.draw(false);
            });
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'news', 'status_route' => 'news.publish'])
@stop