@extends('admin.layout.master')

@section('title', 'إضافة مجموعة صلاحيات')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('roles.view') }}" class="text-muted text-hover-info">إدارة الصلاحيات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">إضافة مجموعة</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-shield-search fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> إضافة مجموعة صلاحيات جديدة
                </span>
            </div>
        </div>
        <div class="card-body py-10">
            @include('admin.layout.error')
            <form action="" method="POST" class="form">
                @csrf
                <div class="row g-9 mb-8">
                    <div class="col-md-12 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">@lang('app.name')</span>
                        </label>
                        <input type="text" value="{{ $info ? $info->name : old('name') }}" name="name" class="form-control form-control-solid" placeholder="أدخل اسم المجموعة" required />
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">@lang('app.status')</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            @php $data = $info ? $info->status : old('status'); @endphp
                            <input class="form-check-input h-30px w-50px" name="status" type="checkbox" value="1" {{ $data == 1 ? 'checked' : '' }}>
                            <label class="form-check-label ms-3 text-gray-700 fw-bold">تفعيل / تعطيل</label>
                        </div>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">@lang('app.is_user')</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            @php $data = $info ? $info->is_user : old('is_user'); @endphp
                            <input class="form-check-input h-30px w-50px" name="is_user" type="checkbox" value="1" {{ $data == 1 ? 'checked' : '' }}>
                            <label class="form-check-label ms-3 text-gray-700 fw-bold">مستخدم نظام</label>
                        </div>
                    </div>
                </div>

                <div class="text-center pt-15">
                    <button type="submit" class="btn btn-primary px-15">@lang('app.save')</button>
                    <a href="{{ route('roles.view') }}" class="btn btn-light ms-3">@lang('app.cancel')</a>
                </div>
            </form>
        </div>
    </div>
@stop