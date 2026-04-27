@extends('admin.layout.master')

@section('title', 'إعدادات الموقع')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">إعدادات الموقع</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-setting-2 fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> إدارة إعدادات الموقع
                </span>
            </div>
        </div>
        <div class="card-body py-10">
            @include('admin.layout.error')
            <form method="post" action="" role="form" class="form" enctype="multipart/form-data">
                @csrf
                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الإسم</label>
                        <input type="text" value="{{ $info->title }}" name="title" id="title" class="form-control form-control-solid" placeholder="الإسم">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">بريد التواصل</label>
                        <input type="email" value="{{ $info->contact_email }}" name="contact_email" id="contact_email" class="form-control form-control-solid" placeholder="contact_email">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الوصف</label>
                        <textarea name="description" id="description" class="form-control form-control-solid" rows="4" style="resize: none">{{ $info->description }}</textarea>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">تعريف اضافي</label>
                        <textarea name="more_desc" id="more_desc" class="form-control form-control-solid" rows="4" style="resize: none">{{ $info->more_desc }}</textarea>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الكلمات الدلالية</label>
                        <input type="text" value="{{ $info->tags }}" name="tags" id="tags" class="form-control form-control-solid" data-role="tagsinput">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الموبيل</label>
                        <input type="text" value="{{ $info->mobile }}" name="mobile" id="mobile" class="form-control form-control-solid" placeholder="mobile">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">العنوان</label>
                        <input type="text" value="{{ $info->address }}" name="address" id="address" class="form-control form-control-solid" placeholder="address">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">ساعات التدريب</label>
                        <input type="text" value="{{ $info->donars }}" name="donars" id="donars" class="form-control form-control-solid" placeholder="donars">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-4 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">عدد الكورسات</label>
                        <input type="text" value="{{ $info->clients }}" name="clients" id="clients" class="form-control form-control-solid" placeholder="clients">
                    </div>
                    <div class="col-md-4 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">عدد الطلاب</label>
                        <input type="text" value="{{ $info->happy }}" name="happy" id="happy" class="form-control form-control-solid" placeholder="happy">
                    </div>
                    <div class="col-md-4 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">طلاب الايلتس</label>
                        <input type="text" value="{{ $info->tickects }}" name="tickects" id="tickects" class="form-control form-control-solid" placeholder="tickects">
                    </div>
                </div>

                <div class="text-center pt-15">
                    <button type="submit" class="btn btn-primary px-15">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')

@stop

@section('modals')

@stop
