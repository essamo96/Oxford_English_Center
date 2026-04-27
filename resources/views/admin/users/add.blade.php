@extends('admin.layout.master')

@section('title', 'إضافة مستخدم جديد')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('users.view') }}" class="text-muted text-hover-info">إدارة المستخدمين</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">إضافة مستخدم جديد</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-user-square fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> إضافة مستخدم جديد
                </span>
            </div>
        </div>
        <div class="card-body py-10">
            @include('admin.layout.error')
            <form action="" method="POST" class="form" enctype="multipart/form-data">
                @csrf
                <div class="row g-9 mb-8">
                    <!-- Profile Image -->
                    <div class="col-md-12 fv-row text-center mb-5">
                        <label class="d-block fs-6 fw-semibold mb-5">صورة الملف الشخصي</label>
                        <div class="image-input image-input-outline image-input-placeholder shadow-sm" data-kt-image-input="true">
                            <div class="image-input-wrapper w-125px h-125px rounded-circle" style="background-image: url('{{ $info && $info->image ? url($info->image) : asset('assets/media/avatars/blank.png') }}')"></div>
                            
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="تغيير الصورة">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="image" accept=".png, .jpg, .jpeg" />
                                <input type="hidden" name="avatar_remove" />
                            </label>

                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="إلغاء">
                                <i class="bi bi-x fs-2"></i>
                            </span>

                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="حذف الصورة">
                                <i class="bi bi-trash fs-7"></i>
                            </span>
                        </div>
                        <div class="form-text mt-3">الأنواع المسموحة: png, jpg, jpeg. الحد الأقصى: 2MB</div>
                    </div>

                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">@lang('app.full_name')</span>
                        </label>
                        <input type="text" value="{{ $info ? $info->name : old('name') }}" name="name"
                            class="form-control form-control-solid" placeholder="أدخل الإسم الكامل" required />
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">@lang('app.username')</span>
                        </label>
                        <input type="text" value="{{ $info ? $info->username : old('username') }}"
                            name="username" class="form-control form-control-solid" placeholder="أدخل اسم المستخدم" required />
                    </div>
                </div>

                @if (!isset($info))
                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">@lang('app.password')</span>
                        </label>
                        <input type="password" name="password" class="form-control form-control-solid" placeholder="********" required />
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">@lang('app.change-password')</span>
                        </label>
                        <input type="password" name="password_confirmation" class="form-control form-control-solid" placeholder="********" required />
                    </div>
                </div>
                @endif

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">@lang('app.email')</span>
                        </label>
                        <input type="email" placeholder="example@domain.com"
                            value="{{ $info ? $info->email : old('email') }}" name="email" class="form-control form-control-solid" required />
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">@lang('app.group')</span>
                        </label>
                        <select class="form-select form-select-solid" name="role" data-control="select2" data-placeholder="اختر المجموعة" required>
                            <option></option>
                            @php $data = $info ? $info->role : old('role'); @endphp
                            @foreach ($roles as $item)
                                <option value="{{ $item->id }}"
                                    {{ $data == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">@lang('app.status')</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            @php $data = $info ? $info->status : old('status'); @endphp
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input h-30px w-50px" name="status" type="checkbox" value="1"
                                {{ $data == 1 ? 'checked' : '' }}>
                            <label class="form-check-label ms-3 text-gray-700 fw-bold">تفعيل / تعطيل</label>
                        </div>
                    </div>
                </div>

                <div class="text-center pt-15">
                    <button type="submit" class="btn btn-primary px-15">
                        <span class="indicator-label">@lang('app.save')</span>
                    </button>
                    <a href="{{ route('users.view') }}" class="btn btn-light ms-3">@lang('app.cancel')</a>
                </div>
            </form>

        </div>
    </div>
@stop


