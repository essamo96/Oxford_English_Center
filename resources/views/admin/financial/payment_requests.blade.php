@extends('admin.layout.master')
@section('title', 'طلبات الدفع من الطلاب')
@php $active_menu = 'student_payments'; @endphp

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">الإدارة المالية</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">طلبات الدفع من الطلاب</li>
@stop

@section('page-content')
<div class="alert alert-info d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-info-circle-fill fs-4"></i>
    <div>هذه الشاشة مدمجة أيضاً ضمن <a href="{{ url('admin/financial/pending') }}" class="fw-bold">الطلبات المالية العالقة</a> — تبويب "طلبات دفع الطلاب".</div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="bi bi-credit-card-2-front text-primary fs-4"></i>
                    <span class="caption-subject font-blue bold uppercase ms-2">طلبات الدفع من الطلاب</span>
                </div>
            </div>
            <div class="portlet-body">
                @include('admin.financial.partials.student_payments_panel')
            </div>
        </div>
    </div>
</div>
@endsection
