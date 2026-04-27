@extends('admin.layout.master')

@section('title')
    تحديد صلاحيات {{ $info->name }}
@stop

@section('page-breadcrumb')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-400 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('roles.view') }}" class="text-muted text-hover-info">إدارة الصلاحيات</a>
        </li>
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-400 w-5px h-2px"></span>
        </li>
        <li class="breadcrumb-item text-dark">
            تحديد صلاحيات المجموعة
        </li>
    </ul>
@stop

@section('page-content')
<div class="card shadow-sm border-0">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <h3 class="fw-bold m-0"><i class="ki-duotone ki-element-plus fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> تحديد صلاحيات: <span class="text-primary">{{ $info->name }}</span></h3>
        </div>
    </div>
    
    <div class="card-body p-9">
        @include('admin.layout.error')
        
        <form role="form" method="post" action="">
            @csrf
            
            <div class="row g-9">
                @foreach($permission_group as $row)
                    @if($row->permissions->count() > 0)
                    <div class="col-md-12 mb-5">
                        <div class="card border border-dashed border-gray-300">
                            <div class="card-header min-h-40px bg-light mb-3">
                                <h3 class="card-title fs-5 fw-bold text-gray-800 m-0">{{ $row->name_ar ?? $row->name }}</h3>
                            </div>
                            <div class="card-body p-5">
                                <div class="row g-4">
                                    @foreach($row->permissions as $item)
                                        <div class="col-md-3 col-sm-6">
                                            <label class="form-check form-check-custom form-check-solid">
                                                <input class="form-check-input" id="permissions[]" name="permissions[]" type="checkbox" value="{{ $item->id }}" {{ in_array($item->id, array_column($role_permissions, 'permission_id')) ? 'checked' : '' }} />
                                                <span class="form-check-label fw-semibold text-gray-700">
                                                    {{ trans('permissions.'.$item->name) }}
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            <div class="d-flex justify-content-end mt-10">
                <a href="{{ route('roles.view') }}" class="btn btn-light me-3">إلغاء</a>
                <button type="submit" class="btn btn-primary">
                    <span class="indicator-label">حفظ الصلاحيات</span>
                </button>
            </div>
        </form>
    </div>
</div>
@stop
