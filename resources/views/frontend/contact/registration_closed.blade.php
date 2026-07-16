@extends('frontend.layouts.master')
@section('title', 'التسجيل مغلق | Oxford Language Center')
@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center px-4 py-8" style="max-width: 580px;">
        <div class="mb-6">
            <i class="bi bi-lock-fill text-warning" style="font-size: 5.2rem;"></i>
        </div>
        <h2 class="fw-bold mb-4" style="font-size: 2.2rem;">التسجيل مغلق حالياً</h2>
        <p class="text-muted fs-5 lh-lg mb-6">{{ $message }}</p>
        <a href="{{ url('/') }}" class="btn btn-primary px-8">
            <i class="bi bi-house-fill me-2"></i> العودة للرئيسية
        </a>
    </div>
</div>
@stop
