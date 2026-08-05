@extends('admin.layout.master')
@section('title', 'إضافة سؤال')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4/dist/tagify.css" rel="stylesheet" type="text/css">
<style>
    /* Match Metronic's form-control-solid look */
    .tagify { --tag-bg: #f1faff; --tag-text-color: #009ef7; --tag-remove-btn-color: #009ef7; border-color: var(--bs-gray-300); background-color: var(--bs-gray-100); border-radius: 0.475rem; }
    .tagify:hover { border-color: var(--bs-gray-300); }
    .tagify--focus { border-color: #009ef7; box-shadow: none; }
</style>
@stop

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">
    <a href="{{ route('exam_questions.view') }}" class="text-muted text-hover-info">بنك الأسئلة</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">إضافة سؤال</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">إضافة سؤال جديد</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ route('exam_questions.view') }}" class="btn btn-sm btn-light btn-active-light-primary">
                <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <form role="form" method="post" action="{{ route('exam_questions.add') }}" enctype="multipart/form-data" class="form d-flex flex-column gap-7">
            {{ csrf_field() }}
            @include('admin.exam_questions.parts.form')
            <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-primary">حفظ</button>
                <a href="{{ route('exam_questions.view') }}" class="btn btn-light ms-2">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('vendor/laravel-filemanager/js/lfm.js') }}?v=2"></script>
<script src="{{ asset('assets/admin/ckeditor/ckeditor.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    if ($('#question_text').length) {
        CKEDITOR.replace('question_text', { language: 'ar', height: 180 });
    }
    if ($('#explanation').length) {
        CKEDITOR.replace('explanation', { language: 'ar', height: 140 });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4"></script>
<script type="text/javascript">
    if (document.getElementById('question_tags')) {
        var tagifyInput = document.getElementById('question_tags');
        var tagify = new Tagify(tagifyInput, {
            delimiters: ',',
            trim: true,
            originalInputValueFormat: function (valuesArr) {
                return valuesArr.map(function (item) { return item.value; }).join(',');
            }
        });
    }
</script>
@stop
