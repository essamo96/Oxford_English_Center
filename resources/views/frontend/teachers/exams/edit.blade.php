@extends('frontend.layouts.dashboard')
@section('title', 'تعديل امتحان')
@section('page-title', 'تعديل الامتحان')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">تعديل الامتحان</h5>
        <a href="{{ route('teacher.exams.view') }}" class="btn btn-light btn-sm">رجوع</a>
    </div>
    <div class="card-body">
        @if(session('danger'))
            <div class="alert alert-danger">{{ session('danger') }}</div>
        @endif
        <form method="post" action="{{ route('teacher.exams.edit', ['id' => Crypt::encrypt($info->id)]) }}">
            {{ csrf_field() }}
            @include('frontend.teachers.exams.parts.form')
            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
            <a href="{{ route('teacher.exams.view') }}" class="btn btn-light">إلغاء</a>
        </form>
    </div>
</div>
@stop
