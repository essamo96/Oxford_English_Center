@extends('frontend.layouts.dashboard')
@section('title', 'تعديل امتحان')
@section('page-title', 'تعديل الامتحان')

@section('css')
@include('frontend.teachers.exams.parts.design')
@include('frontend.teachers.exams.parts.form-design')
@stop

@section('content')
<div class="tex-page-head">
    <div class="tex-page-head__title">
        <span class="tex-page-head__icon"><i class="bi bi-journal-text"></i></span>
        <div>
            <h4>تعديل الامتحان</h4>
            <p>{{ $info->title }}</p>
        </div>
    </div>
    <a href="{{ route('teacher.exams.view') }}" class="tex-icon-btn" title="رجوع"><i class="bi bi-arrow-right"></i></a>
</div>

@if(session('danger'))
    <div class="alert alert-danger">{{ session('danger') }}</div>
@endif

<form method="post" action="{{ route('teacher.exams.edit', ['id' => Crypt::encrypt($info->id)]) }}" id="tex_exam_form">
    {{ csrf_field() }}
    @include('frontend.teachers.exams.parts.form')
    <div class="tex-form-actions">
        <button type="submit" class="tex-btn-primary tex-btn-lg"><i class="bi bi-check-circle"></i> حفظ التعديلات</button>
        <a href="{{ route('teacher.exams.view') }}" class="btn btn-light">إلغاء</a>
    </div>
</form>
@stop

@section('js')
<script src="{{ asset('assets/admin/ckeditor/ckeditor.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    if (typeof CKEDITOR !== 'undefined' && $('#exam_description').length) {
        CKEDITOR.replace('exam_description', { language: 'ar', height: 160 });
    }
</script>
@stop
