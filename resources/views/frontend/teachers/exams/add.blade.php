@extends('frontend.layouts.dashboard')
@section('title', 'إضافة امتحان')
@section('page-title', 'إضافة امتحان جديد')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">إضافة امتحان جديد</h5>
        <a href="{{ route('teacher.exams.view') }}" class="btn btn-light btn-sm">رجوع</a>
    </div>
    <div class="card-body">
        @if(session('danger'))
            <div class="alert alert-danger">{{ session('danger') }}</div>
        @endif
        <form method="post" action="{{ route('teacher.exams.add') }}">
            {{ csrf_field() }}
            @include('frontend.teachers.exams.parts.form')
            <button type="submit" class="btn btn-primary">حفظ</button>
            <a href="{{ route('teacher.exams.view') }}" class="btn btn-light">إلغاء</a>
        </form>
    </div>
</div>
@stop
