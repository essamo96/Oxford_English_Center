@extends('admin.layout.master')

@section('title', 'إضافة/تعديل صلاحية')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('permissions.view') }}" class="text-muted text-hover-info">إدارة الصلاحيات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">إضافة/تعديل</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-key fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> إضافة/تعديل صلاحية
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
                        <input type="text" value="{{ $info ? $info->name : old('name') }}" name="name" class="form-control form-control-solid" placeholder="مثلاً: admin.users.view" required />
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">@lang('app.parent') (المجموعة)</span>
                        </label>
                        <select class="form-select form-select-solid" name="group_id" data-control="select2" required>
                            <option value="">@lang('app.choose')</option>
                            @php $data = $info ? $info->group_id : old('group_id'); @endphp
                            @foreach ($permissions as $item)
                                <option value="{{ $item->id }}" {{ $data == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">@lang('app.guard_name')</span>
                        </label>
                        <select class="form-select form-select-solid" name="guard_name" data-control="select2" required>
                            <option value="">@lang('app.choose')</option>
                            @php $data = $info ? $info->guard_name : old('guard_name'); @endphp
                            @foreach ($guards as $item)
                                <option value="{{ $item }}" {{ $data == $item ? 'selected' : '' }}>
                                    {{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="text-center pt-15">
                    <button type="submit" class="btn btn-primary px-15">@lang('app.save')</button>
                    <a href="{{ route($active_menu . '.view') }}" class="btn btn-light ms-3">@lang('app.cancel')</a>
                </div>
            </form>
        </div>
    </div>
@stop

