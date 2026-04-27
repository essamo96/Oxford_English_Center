@extends('admin.layout.master')

@section('title', 'تعديل أسئلة التقييم')

@section('page-title')
    تعديل أسئلة التقييم
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('evaluate_items.view') }}" class="text-muted text-hover-info">إدارة التقييمات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">تعديل أسئلة التقييم</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">تعديل بيانات التقييم</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('evaluate_items.view') }}" class="btn btn-sm btn-light btn-active-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.error')
            <form role="form" method="post" action="" class="form d-flex flex-column gap-7" enctype="multipart/form-data">
                {{ csrf_field() }}
                
                <div class="row g-9 mb-8">
                    <div class="col-md-12 fv-row">
                        <label class="fs-6 fw-semibold mb-2 required">ادخل نص السؤال</label>
                        <input type="text" value="{{ $info->name_en }}" class="form-control form-control-solid name_en" name="name_en" autocomplete="off" placeholder="التقييم..">   
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('evaluate_items.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
@stop
