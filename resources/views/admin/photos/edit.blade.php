@extends('admin.layout.master')

@section('title', 'تعديل ألبوم')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted"><a href="{{ route('photos.view') }}" class="text-muted text-hover-info">الألبومات</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">تعديل: {{ $info->title }}</li>
@stop

@section('page-content')
@php $active_menu = 'photos'; @endphp

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm">
            <div class="card-header border-0 pt-6">
                <div class="card-title"><h3 class="fw-bold"><i class="bi bi-images text-primary me-2"></i>تعديل الألبوم</h3></div>
            </div>
            <div class="card-body py-4">
                @include('admin.layout.masterLayouts.error')

                <form method="post" action="{{ route('photos.edit', ['id' => Crypt::encrypt($info->id)]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-5">
                        <div class="col-md-12">
                            <label class="form-label fw-bold required">عنوان الألبوم</label>
                            <input type="text" name="title" value="{{ old('title', $info->title) }}" class="form-control form-control-solid" placeholder="عنوان الألبوم">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">الوصف</label>
                            <textarea name="descs" rows="5" class="form-control form-control-solid" style="resize:none;" placeholder="وصف مختصر للألبوم">{{ old('descs', $info->descs) }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">الكلمات الدلالية</label>
                            <input type="text" name="tags" value="{{ old('tags', $info->tags) }}" class="form-control form-control-solid" placeholder="افصل بين الكلمات بفاصلة (,)">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold d-block mb-2">الحالة</label>
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" name="status" {{ old('status', $info->status) == 1 ? 'checked' : '' }}>
                                <span class="form-check-label fw-semibold ms-3">تفعيل الألبوم وعرضه في الموقع</span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-6 mt-6 border-top">
                        <a href="{{ route('photos.view') }}" class="btn btn-light me-3">إلغاء</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
