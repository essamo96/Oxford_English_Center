@extends('admin.layout.master')

@section('title', 'إضافة فيديو')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted"><a href="{{ route('videos.view') }}" class="text-muted text-hover-info">الفيديوهات</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">إضافة فيديو</li>
@stop

@section('page-content')
@php $active_menu = 'videos'; @endphp

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm">
            <div class="card-header border-0 pt-6">
                <div class="card-title"><h3 class="fw-bold"><i class="bi bi-camera-video-fill text-danger me-2"></i>إضافة فيديو جديد</h3></div>
            </div>
            <div class="card-body py-4">
                @include('admin.layout.masterLayouts.error')

                <form method="post" action="{{ route('videos.add') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-5">
                        <div class="col-md-12">
                            <label class="form-label fw-bold required">عنوان الفيديو</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control form-control-solid" placeholder="اكتب عنوانًا واضحًا للفيديو">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold required">رابط الفيديو</label>
                            <input type="text" name="url" value="{{ old('url') }}" class="form-control form-control-solid" placeholder="https://www.youtube.com/watch?v=...">
                            <div class="form-text">ضع رابط يوتيوب أو رابط التضمين (Embed) للفيديو.</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold d-block mb-2">الحالة</label>
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" name="status" {{ old('status') == 1 ? 'checked' : '' }}>
                                <span class="form-check-label fw-semibold ms-3">تفعيل الفيديو وعرضه في الموقع</span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-6 mt-6 border-top">
                        <a href="{{ route('videos.view') }}" class="btn btn-light me-3">إلغاء</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> حفظ الفيديو</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
