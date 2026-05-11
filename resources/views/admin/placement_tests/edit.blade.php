@extends('admin.layout.master')

@section('title', 'تحديث اختبار تحديد المستوى')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted"><a href="{{ route('placement_tests.view') }}" class="text-muted text-hover-info">الاختبارات</a></li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">تحديث الاختبار</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">تحديث اختبار: {{ $test->student->name }}</span>
        </div>
    </div>
    <div class="card-body py-4">
        <form action="{{ route('placement_tests.edit', $test->id) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-10">
                    <label class="form-label">حالة الاختبار</label>
                    <select name="status" class="form-select form-select-solid" data-control="select2">
                        <option value="pending" {{ $test->status == 'pending' ? 'selected' : '' }}>Pending (قيد الانتظار)</option>
                        <option value="payment_confirmed" {{ $test->status == 'payment_confirmed' ? 'selected' : '' }}>Payment Confirmed (تم تأكيد الدفع)</option>
                        <option value="waiting_for_test" {{ $test->status == 'waiting_for_test' ? 'selected' : '' }}>Waiting for Test (بانتظار الاختبار)</option>
                        <option value="completed" {{ $test->status == 'completed' ? 'selected' : '' }}>Completed (مكتمل)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-10">
                    <label class="form-label">تاريخ الاختبار</label>
                    <input type="date" name="test_date" class="form-control" value="{{ $test->test_date }}">
                </div>
                <div class="col-md-6 mb-10">
                    <label class="form-label">وقت الاختبار</label>
                    <input type="text" name="test_time" class="form-control" value="{{ $test->test_time }}">
                </div>
                <div class="col-md-3 mb-10">
                    <label class="form-label">العلامة / السكور</label>
                    <input type="text" name="score" class="form-control" value="{{ $test->score }}" placeholder="مثلاً: 85/100">
                </div>
                <div class="col-md-3 mb-10">
                    <label class="form-label">المستوى المقترح</label>
                    <input type="text" name="assigned_level" class="form-control" value="{{ $test->assigned_level }}" placeholder="مثلاً: Intermediate A">
                </div>
            </div>

            @if($test->payment_receipt)
                <div class="mb-10">
                    <label class="form-label">إيصال الدفع</label>
                    <div class="mt-2">
                        <a href="{{ asset('uploads/' . $test->payment_receipt) }}" target="_blank" class="btn btn-secondary btn-sm">
                            <i class="fa fa-eye"></i> عرض الإيصال
                        </a>
                    </div>
                </div>
            @endif

            <div class="text-end mt-10">
                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                <a href="{{ route('placement_tests.view') }}" class="btn btn-light">رجوع</a>
            </div>
        </form>
    </div>
</div>
@stop
