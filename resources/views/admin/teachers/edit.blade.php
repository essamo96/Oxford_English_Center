@extends('admin.layout.master')

@section('title', 'تعديل بيانات المدرس')

@section('page-title')
    تعديل بيانات المدرس: {{ $info->name }}
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('teachers.view') }}" class="text-muted text-hover-info">إدارة المدرسين</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">{{ $info->name }}</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">تعديل بيانات المدرس</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('teachers.view') }}" class="btn btn-sm btn-light btn-active-light-primary">
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
                        <label class="fs-6 fw-semibold mb-2">الاسم</label>
                        <input type="text" value="{{ $info->name }}" name="name" id="name"
                            class="form-control form-control-solid" placeholder="الاسم الكامل">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">رقم الجوال</label>
                        <input type="text" value="{{ $info->mobile }}" name="mobile" id="mobile"
                            class="form-control form-control-solid" placeholder="رقم الجوال">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">تاريخ الميلاد</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-solid date-picker" name="dob"
                                value="{{ $info->dob }}" placeholder="اختر التاريخ">
                            <span class="input-group-text"><i class="ki-duotone ki-calendar-8 fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i></span>
                        </div>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">البريد الإلكتروني</label>
                        <input type="email" value="{{ $info->email }}" name="email" id="email"
                            class="form-control form-control-solid" placeholder="example@domain.com">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">تاريخ الانضمام</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-solid date-picker" name="join_date"
                                value="{{ $info->join_date }}" placeholder="اختر التاريخ">
                            <span class="input-group-text"><i class="ki-duotone ki-calendar-8 fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i></span>
                        </div>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">السيرة الذاتية (CV)</label>
                        <x-metronic-file-picker 
                            id="thumbnail_cv" 
                            name="cv" 
                            type="file" 
                            label="اختر ملف الـ CV" 
                            placeholder="اختر ملف السيرة الذاتية" 
                            :value="$info->cv" />
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">الصورة الشخصية</label>
                        <x-metronic-file-picker 
                            id="thumbnail_image" 
                            name="image" 
                            type="image" 
                            label="اختر صورة" 
                            placeholder="اختر الصورة الشخصية"
                            :value="$info->image" />
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">الحالة</label>
                        <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                            <input class="form-check-input" type="checkbox" value="1" name="status" {{ $info->status == 1 ? 'checked' : '' }} />
                            <label class="form-check-label">تفعيل</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('teachers.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(".date-picker").flatpickr({
            dateFormat: "Y-m-d",
        });
    </script>
    @stack('scripts')
@stop