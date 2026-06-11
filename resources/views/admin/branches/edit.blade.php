@extends('admin.layout.master')

@section('title', 'تعديل الفرع')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">
    <a href="{{ route('branches.view') }}" class="text-muted text-hover-info">إدارة الفروع</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">تعديل فرع</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">تعديل فرع: {{ $info->name_ar }}</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ route('branches.view') }}" class="btn btn-sm btn-light btn-active-light-primary">
                <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <form role="form" method="post" action="" class="form d-flex flex-column gap-7">
            {{ csrf_field() }}

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2 required">اسم الفرع (عربي)</label>
                    <input type="text" value="{{ old('name_ar', $info->name_ar) }}" name="name_ar" class="form-control form-control-solid" placeholder="مثال: فرع المدينة المنورة">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2 required">اسم الفرع (إنجليزي)</label>
                    <input type="text" value="{{ old('name_en', $info->name_en) }}" name="name_en" class="form-control form-control-solid" placeholder="e.g. Madinah Branch">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الحالة</label>
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="status" {{ $info->status == 1 ? 'checked' : '' }} />
                        <label class="form-check-label">مفعل</label>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('branches.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>
@stop
