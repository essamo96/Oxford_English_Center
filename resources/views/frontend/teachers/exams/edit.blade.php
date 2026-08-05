@extends('frontend.layouts.dashboard')
@section('title', 'تعديل امتحان')
@section('page-title', 'تعديل الامتحان')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-journal-text text-primary"></i> تعديل الامتحان</h4>
        <p class="text-muted mb-0">{{ $info->title }}</p>
    </div>
    <a href="{{ route('teacher.exams.view') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-right"></i> رجوع</a>
</div>

@if(session('danger'))
    <div class="alert alert-danger">{{ session('danger') }}</div>
@endif

<form method="post" action="{{ route('teacher.exams.edit', ['id' => Crypt::encrypt($info->id)]) }}">
    {{ csrf_field() }}
    @include('frontend.teachers.exams.parts.form')
    <div class="d-flex gap-2 mb-5">
        <button type="submit" class="btn btn-primary px-5"><i class="bi bi-check-circle"></i> حفظ التعديلات</button>
        <a href="{{ route('teacher.exams.view') }}" class="btn btn-light">إلغاء</a>
    </div>
</form>
@stop
