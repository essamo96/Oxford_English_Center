@extends('admin.layout.master')

@section('title', 'إضافة موعد جديد')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('times.view') }}" class="text-muted text-hover-info">إدارة المواعيد والاوقات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">إضافة موعد جديد</li>
@stop

@section('page-title')
    إضافة موعد جديد
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">إضافة موعد جديد</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('times.view') }}" class="btn btn-sm btn-light btn-active-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.masterLayouts.error')
            <form role="form" method="post" action="" class="form d-flex flex-column gap-7" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="row g-9 mb-8">
                    <!-- Days Field -->
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2 required">الأيام</label>
                        <select name="days" id="days" class="form-select form-select-solid" data-control="select2" data-tags="true" data-placeholder="اختر أو اكتب الأيام...">
                            <option></option>
                            <option value="السبت والاثنين والأربعاء" {{ old('days') == 'السبت والاثنين والأربعاء' ? 'selected' : '' }}>السبت والاثنين والأربعاء</option>
                            <option value="الأحد والثلاثاء والخميس" {{ old('days') == 'الأحد والثلاثاء والخميس' ? 'selected' : '' }}>الأحد والثلاثاء والخميس</option>
                            <option value="السبت والأربعاء" {{ old('days') == 'السبت والأربعاء' ? 'selected' : '' }}>السبت والأربعاء</option>
                            <option value="الأحد والثلاثاء" {{ old('days') == 'الأحد والثلاثاء' ? 'selected' : '' }}>الأحد والثلاثاء</option>
                            <option value="الاثنين والخميس" {{ old('days') == 'الاثنين والخميس' ? 'selected' : '' }}>الاثنين والخميس</option>
                            <option value="يومياً" {{ old('days') == 'يومياً' ? 'selected' : '' }}>يومياً</option>
                            @if(old('days') && !in_array(old('days'), ['السبت والاثنين والأربعاء', 'الأحد والثلاثاء والخميس', 'السبت والأربعاء', 'الأحد والثلاثاء', 'الاثنين والخميس', 'يومياً']))
                                <option value="{{ old('days') }}" selected>{{ old('days') }}</option>
                            @endif
                        </select>
                        <div class="text-muted fs-7 mt-2">اختر أياماً من القائمة، أو اكتب أياماً مخصصة ثم اضغط (Enter).</div>
                    </div>
                    
                    <!-- Times Field -->
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2 required">الوقت</label>
                        <select name="times" id="times" class="form-select form-select-solid" data-control="select2" data-tags="true" data-placeholder="اختر أو اكتب الوقت...">
                            <option></option>
                            <option value="08:00 صباحاً - 10:00 صباحاً" {{ old('times') == '08:00 صباحاً - 10:00 صباحاً' ? 'selected' : '' }}>08:00 صباحاً - 10:00 صباحاً</option>
                            <option value="10:00 صباحاً - 12:00 مساءً" {{ old('times') == '10:00 صباحاً - 12:00 مساءً' ? 'selected' : '' }}>10:00 صباحاً - 12:00 مساءً</option>
                            <option value="12:00 مساءً - 02:00 مساءً" {{ old('times') == '12:00 مساءً - 02:00 مساءً' ? 'selected' : '' }}>12:00 مساءً - 02:00 مساءً</option>
                            <option value="02:00 مساءً - 04:00 مساءً" {{ old('times') == '02:00 مساءً - 04:00 مساءً' ? 'selected' : '' }}>02:00 مساءً - 04:00 مساءً</option>
                            <option value="04:00 مساءً - 06:00 مساءً" {{ old('times') == '04:00 مساءً - 06:00 مساءً' ? 'selected' : '' }}>04:00 مساءً - 06:00 مساءً</option>
                            @if(old('times') && !in_array(old('times'), ['08:00 صباحاً - 10:00 صباحاً', '10:00 صباحاً - 12:00 مساءً', '12:00 مساءً - 02:00 مساءً', '02:00 مساءً - 04:00 مساءً', '04:00 مساءً - 06:00 مساءً']))
                                <option value="{{ old('times') }}" selected>{{ old('times') }}</option>
                            @endif
                        </select>
                        <div class="text-muted fs-7 mt-2">اختر وقتاً من القائمة، أو اكتب وقتاً مخصصاً ثم اضغط (Enter).</div>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">الحالة</label>
                        <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                            <input class="form-check-input" type="checkbox" value="1" name="status"
                                {{ old('status') == 1 ? 'checked' : '' }} />
                            <label class="form-check-label px-2">تفعيل / تعطيل</label>
                        </div>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">خاص باختبار تحديد المستوى؟</label>
                        <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                            <input class="form-check-input" type="checkbox" value="1" name="is_placement_test"
                                {{ old('is_placement_test') == 1 ? 'checked' : '' }} />
                            <label class="form-check-label px-2">نعم — موعد لاختبار تحديد المستوى فقط</label>
                        </div>
                        <div class="text-muted fs-7 mt-2">عند التفعيل، يظهر هذا الموعد لطلاب اختبار تحديد المستوى في صفحة التسجيل.</div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('times.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ الموعد</button>
                </div>
            </form>
        </div>
    </div>
@stop