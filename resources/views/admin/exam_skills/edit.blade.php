@extends('admin.layout.master')
@section('title', 'تعديل تصنيف')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">
    <a href="{{ route('exam_skills.view') }}" class="text-muted text-hover-info">تصنيفات الأسئلة</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">تعديل تصنيف</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">تعديل التصنيف</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ route('exam_skills.view') }}" class="btn btn-sm btn-light btn-active-light-primary">
                <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <form role="form" method="post" action="{{ route('exam_skills.edit', ['id' => Crypt::encrypt($info->id)]) }}" class="form d-flex flex-column gap-7">
            {{ csrf_field() }}
            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الاسم (عربي)</label>
                    <input type="text" value="{{ old('name_ar', $info->name_ar) }}" name="name_ar" class="form-control form-control-solid">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الاسم (إنجليزي)</label>
                    <input type="text" value="{{ old('name_en', $info->name_en) }}" name="name_en" class="form-control form-control-solid">
                </div>
            </div>
            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">المعرف (slug)</label>
                    <input type="text" value="{{ old('slug', $info->slug) }}" name="slug" class="form-control form-control-solid">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الحالة</label>
                    <select name="status" class="form-select form-select-solid">
                        <option value="1" {{ old('status', $info->status) == '1' ? 'selected' : '' }}>فعال</option>
                        <option value="0" {{ old('status', $info->status) == '0' ? 'selected' : '' }}>غير فعال</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                <a href="{{ route('exam_skills.view') }}" class="btn btn-light ms-2">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop
