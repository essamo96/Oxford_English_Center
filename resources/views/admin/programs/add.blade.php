@extends('admin.layout.master')

@section('title')
    اضافة برنامج
@stop

@section('css')

@stop

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">
    <a href="{{ route('programs.view') }}" class="text-muted text-hover-info">إدارة البرامج</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">اضافة برنامج</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">اضافة برنامج</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ URL::previous() }}" class="btn btn-sm btn-light btn-active-light-primary">
                <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <form role="form" method="post" action="" class="form d-flex flex-column gap-7" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">العنوان</label>
                    <input type="text" value="{{ old('title') }}" name="title" id="title" class="form-control form-control-solid" placeholder="العنوان">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">نبذة قصيرة</label>
                    <input type="text" value="{{ old('short') }}" name="short" id="short" class="form-control form-control-solid" placeholder="نبذة قصيرة">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الصورة</label>
                    <div class="input-group">
                        <input id="thumbnail" value="{{ old('image') }}" class="form-control form-control-solid" type="text" name="image" readonly placeholder="لم يتم المرفق">
                        <button id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary" type="button">
                            <i class="ki-duotone ki-picture fs-2"><span class="path1"></span><span class="path2"></span></i> حدد صورة
                        </button>
                    </div>
                    <div class="mt-3">
                        <img id="holder" style="max-height:100px;">
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">له اختبار</label>
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="exam" {{ old('exam') == 1 ? 'checked' : '' }} />
                        <label class="form-check-label">نعم</label>
                    </div>
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الحالة</label>
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="status" {{ old('status') == 1 ? 'checked' : '' }} />
                        <label class="form-check-label">تفعيل</label>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('programs.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                <button type="submit" class="btn btn-primary">حفظ</button>
            </div>
        </form>
    </div>
</div>
@stop
@section('js')
    <script>
        $('#lfm').on('click', function() {
            openMetronicFileManager('image', 'thumbnail');
        });
    </script>
@stop
