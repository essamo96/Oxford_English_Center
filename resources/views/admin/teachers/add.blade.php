@extends('admin.layout.master')

@section('title', 'إضافة مدرس جديد')

@section('page-title')
    إضافة مدرس جديد
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
    <li class="breadcrumb-item text-muted">إضافة مدرس</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">إضافة بيانات المدرس الجديد</span>
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
                        <label class="fs-6 fw-semibold mb-2 required">الاسم</label>
                        <input type="text" value="{{ old('name') }}" name="name" id="name"
                            class="form-control form-control-solid" placeholder="الاسم الكامل">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2 required">رقم الجوال</label>
                        <input type="text" value="{{ old('mobile') }}" name="mobile" id="mobile"
                            class="form-control form-control-solid" placeholder="رقم الجوال">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">تاريخ الميلاد</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-solid date-picker" name="dob"
                                value="{{ old('dob') }}" placeholder="اختر التاريخ">
                            <span class="input-group-text"><i class="ki-duotone ki-calendar-8 fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i></span>
                        </div>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2 required">البريد الإلكتروني</label>
                        <input type="email" value="{{ old('email') }}" name="email" id="email"
                            class="form-control form-control-solid" placeholder="example@domain.com">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">تاريخ الانضمام</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-solid date-picker" name="join_date"
                                value="{{ old('join_date') }}" placeholder="اختر التاريخ">
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
                            placeholder="اختر ملف السيرة الذاتية" />
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
                            placeholder="اختر الصورة الشخصية" />
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">الحالة</label>
                        <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                            <input class="form-check-input" type="checkbox" value="1" name="status" {{ old('status') == 1 ? 'checked' : '' }} />
                            <label class="form-check-label">تفعيل</label>
                        </div>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">أجر المحاضرة الواحدة (ILS)</label>
                        <input type="number" step="0.01" min="0" name="lecture_rate" id="lecture_rate"
                            class="form-control form-control-solid" placeholder="0.00" value="{{ old('lecture_rate', 0) }}">
                        <div class="form-text">يُستخدم لاحتساب الراتب: أجر المحاضرة × عدد المحاضرات المسجّلة.</div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('teachers.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ البيانات</button>
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
