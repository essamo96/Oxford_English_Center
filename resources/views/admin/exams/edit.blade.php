@extends('admin.layout.master')
@section('title', 'تعديل الامتحان')

@php
    $viewRoute = $category === 'placement' ? 'exam_placement_tests.view' : 'group_exams.view';
    $editRoute = $category === 'placement' ? 'exam_placement_tests.edit' : 'group_exams.edit';
@endphp

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted"><a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a></li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted"><a href="{{ route($viewRoute) }}" class="text-muted text-hover-info">{{ $category === 'placement' ? 'اختبارات تحديد المستوى' : 'امتحانات المجموعات' }}</a></li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">تعديل</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">تعديل الامتحان</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ route($viewRoute) }}" class="btn btn-sm btn-light btn-active-light-primary">
                <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <form role="form" method="post" action="{{ route($editRoute, ['id' => Crypt::encrypt($info->id)]) }}" class="form d-flex flex-column gap-7">
            {{ csrf_field() }}
            @include('admin.exams.parts.form')
            <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                <a href="{{ route($viewRoute) }}" class="btn btn-light ms-2">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop
