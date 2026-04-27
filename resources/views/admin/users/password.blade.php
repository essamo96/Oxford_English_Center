@extends('admin.layout.master')

@section('title', 'تغيير كلمة المرور')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('users.view') }}" class="text-muted text-hover-info">إدارة المستخدمين</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">تغيير كلمة المرور</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-key fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> تغيير كلمة المرور
                </span>
            </div>
        </div>
        <div class="card-body py-10">
            @include('admin.layout.error')
            <form action="" method="POST" class="form">
                @csrf
                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">@lang('app.password')</span>
                        </label>
                        <input type="password" name="password" class="form-control form-control-solid" placeholder="********" required />
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">@lang('app.password_confirmation')</span>
                        </label>
                        <input type="password" name="password_confirmation" class="form-control form-control-solid" placeholder="********" required />
                    </div>
                </div>

                <div class="text-center pt-15">
                    <button type="submit" class="btn btn-primary px-15">@lang('app.save')</button>
                    <a href="{{ route('users.view') }}" class="btn btn-light ms-3">@lang('app.cancel')</a>
                </div>
            </form>
        </div>
    </div>
@stop

