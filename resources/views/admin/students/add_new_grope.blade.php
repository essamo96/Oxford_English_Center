@extends('admin.layout.master')

@section('title', 'إضافة مجموعة للطالب')

@section('page-title', 'إدارة مجموعة الطالب')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('students.view') }}" class="text-muted text-hover-info">إدارة الطلاب</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('students.gropes', ['id' => $student_id]) }}" class="text-muted text-hover-info">مجموعات الطالب</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">إضافة مجموعة</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1">
                    إضافة مجموعة للطالب : <span class="text-primary">{{ $group_students->name ?? '' }}</span>
                </span>
            </div>
            <div class="card-toolbar gap-3">
                @can('admin.groups.add')
                <a href="{{ route('groups.add') }}" class="btn btn-sm btn-light-primary">
                    <i class="ki-duotone ki-plus fs-3"></i> مجموعة جديدة
                </a>
                @endcan
                <a href="{{ URL::previous() }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-10">
            @include('admin.layout.masterLayouts.error')

            <form role="form" method="post" action="{{ route('students.groups.post.addnew') }}" class="form d-flex flex-column gap-7">
                {{ csrf_field() }}
                <input type="hidden" value="{{ Crypt::encrypt($student_id) }}" name ="student_id">

                <div class="row g-9 mb-8 justify-content-center">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">اختر المجموعة</span>
                        </label>
                        <select name="new_grope" id="classes" class="form-select form-select-solid" data-control="select2" data-placeholder="اختر مجموعة...">
                            <option></option>
                            @foreach($grope as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="separator mb-4"></div>

                <div class="d-flex justify-content-center gap-3">
                    <button type="submit" class="btn btn-primary min-w-100px">
                        <i class="ki-duotone ki-check fs-2"></i> حفظ البيانات
                    </button>
                    <a href="{{ URL::previous() }}" class="btn btn-light min-w-100px">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Metronic 8 handles Select2 automatically with data-control="select2"
    });
</script>
@stop