@extends('admin.layout.master')

@section('title', 'تعديل مجموعة صلاحيات')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('roles.view') }}" class="text-muted text-hover-info">إدارة الصلاحيات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">{{ $info->name }}</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-shield-search fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> تعديل مجموعة صلاحيات: {{ $info->name }}
                </span>
            </div>
        </div>
        <div class="card-body py-10">
            @include('admin.layout.error')
            <form method="post" action="" role="form" class="form">
                @csrf
                <div class="row g-9 mb-8">
                    <div class="col-md-12 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">إسم المجموعة</label>
                        <input type="text" value="{{ $info->name }}" name="name" id="name" class="form-control form-control-solid" placeholder="إسم المجموعة">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الحالة</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input h-30px w-50px" name="status" type="checkbox" value="1" {{ $info->status == 1 ? 'checked' : '' }}>
                            <label class="form-check-label ms-3 text-gray-700 fw-bold">تفعيل / تعطيل</label>
                        </div>
                    </div>
                </div>

                <div class="text-center pt-15">
                    <button type="submit" class="btn btn-primary px-15">حفظ</button>
                    <a href="{{ route('roles.view') }}" class="btn btn-light ms-3">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
@stop