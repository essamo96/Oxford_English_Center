@extends('admin.layout.master')

@section('title')
    تغيير كلمة المرور
@stop

@section('css')

@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('students.view') }}" class="text-muted text-hover-info">إدارة الطلاب</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">{{ $info->name }}</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">تغيير كلمة المرور</li>
@stop

{{-- Metronic 8 handles page title via header --}}

@section('page-content')
    <div class="card card-flush shadow-sm border-0">
        <!--begin::Card header-->
        <div class="card-header pt-7">
            <!--begin::Title-->
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-800">تغيير كلمة المرور</span>
                <span class="text-gray-400 mt-1 fw-semibold fs-6">تحديث بيانات الدخول للطالب: {{ $info->name }}</span>
            </h3>
            <!--end::Title-->
            <div class="card-toolbar">
                <a href="{{ URL::previous() }}" class="btn btn-sm btn-light-info">
                    <i class="ki-duotone ki-black-left fs-3 me-1"></i>رجوع
                </a>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body">
            @include('admin.layout.error')
            
            <form method="post" action="" class="form">
                @csrf
                <div class="row mb-8">
                    <div class="col-xl-3">
                        <div class="fs-6 fw-bold mt-2 mb-3">الاسم</div>
                    </div>
                    <div class="col-xl-9">
                        <div class="input-group input-group-solid border border-secondary rounded">
                            <span class="input-group-text">
                                <i class="ki-duotone ki-user fs-2"><span class="path1"></span><span class="path2"></span></i>
                            </span>
                            <input type="text" value="{{ $info->name }}" class="form-control form-control-solid" readonly />
                        </div>
                    </div>
                </div>

                <div class="row mb-8">
                    <div class="col-xl-3">
                        <div class="fs-6 fw-bold mt-2 mb-3 text-required">كلمة المرور الجديدة</div>
                    </div>
                    <div class="col-xl-9">
                        <div class="position-relative">
                            <i class="ki-duotone ki-key fs-2 position-absolute top-50 translate-middle-y ms-4"><span class="path1"></span><span class="path2"></span></i>
                            <input type="password" name="password" class="form-control form-control-solid ps-12" placeholder="أدخل كلمة المرور (6-16 حرفاً)" required />
                        </div>
                    </div>
                </div>

                <div class="row mb-8">
                    <div class="col-xl-3">
                        <div class="fs-6 fw-bold mt-2 mb-3 text-required">تأكيد كلمة المرور</div>
                    </div>
                    <div class="col-xl-9">
                        <div class="position-relative">
                            <i class="ki-duotone ki-shield-search fs-2 position-absolute top-50 translate-middle-y ms-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <input type="password" name="password_confirmation" class="form-control form-control-solid ps-12" placeholder="أعد إدخال كلمة المرور" required />
                        </div>
                    </div>
                </div>

                <div class="separator separator-dashed my-10"></div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('students.view') }}" class="btn btn-light me-3">إلغاء</a>
                    <button type="submit" class="btn btn-primary">
                        <span class="indicator-label">حفظ التغييرات</span>
                    </button>
                </div>
            </form>
        </div>
        <!--end::Card body-->
    </div>
@stop
