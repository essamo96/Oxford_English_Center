@extends('admin.layout.master')
@section('title', 'تعديل طالب')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">
    <a href="{{ route('students.view') }}" class="text-muted text-hover-info">إدارة الطلاب</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted"><strong>{{ $info->name }}</strong></li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">تعديل طالب</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ URL::previous() }}" class="btn btn-sm btn-light btn-active-light-primary">
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
                    <label class="fs-6 fw-semibold mb-2">الاسم</label>
                    <input type="text" value="{{ $info->name }}" name="name" class="form-control form-control-solid" placeholder="الاسم">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">رقم الجوال</label>
                    <input type="text" value="{{ $info->mobile }}" name="mobile" class="form-control form-control-solid" placeholder="رقم الجوال">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">تاريخ الميلاد</label>
                    <div class="input-group">
                        <input class="form-control form-control-solid date-picker" value="{{ $info->dob }}" placeholder="اختر تاريخ" name="dob"/>
                        <span class="input-group-text"><i class="ki-duotone ki-calendar-8 fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i></span>
                    </div>
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">المهنة</label>
                    <input type="text" value="{{ $info->job }}" name="job" class="form-control form-control-solid" placeholder="المهنة">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">البريد الالكتروني</label>
                    <input type="email" value="{{ $info->email }}" name="email" class="form-control form-control-solid" placeholder="البريد الالكتروني">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">تاريخ الانضمام</label>
                    <div class="input-group">
                        <input class="form-control form-control-solid date-picker" value="{{ $info->join_date }}" placeholder="اختر تاريخ" name="join_date"/>
                        <span class="input-group-text"><i class="ki-duotone ki-calendar-8 fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i></span>
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">تاريخ امتحان المستوى</label>
                    <div class="input-group">
                        <input class="form-control form-control-solid date-picker" value="{{ $info->exam_date }}" placeholder="اختر تاريخ" name="exam_date"/>
                        <span class="input-group-text"><i class="ki-duotone ki-calendar-8 fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i></span>
                    </div>
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">نتيجة امتحان المستوى</label>
                    <input type="text" value="{{ $info->exam_degree }}" name="exam_degree" class="form-control form-control-solid" placeholder="نتيجة امتحان المستوي">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الحالة</label>
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="status" {{ $info->status == 1 ? 'checked' : '' }} />
                        <label class="form-check-label">تفعيل</label>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('students.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                <button type="submit" class="btn btn-primary">حفظ</button>
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
@stop