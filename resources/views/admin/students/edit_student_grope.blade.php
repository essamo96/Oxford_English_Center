@extends('admin.layout.master')

@section('title')
تعديل مجموعة  الطالب
@stop

@section('css')

@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-muted">الطلاب</li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-dark">تعديل مجموعة الطالب</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1">
                    <i class="ki-duotone ki-profile-circle fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    تعديل مجموعة الطالب: <span class="text-primary">{{ $group_students->student->name ?? 'N/A' }}</span>
                </span>
            </div>
            <div class="card-toolbar gap-2">
                @can('admin.groups.add')
                    <a href="{{ route('groups.add') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> مجموعة جديدة
                    </a>
                @endcan
                <a href="{{ URL::previous() }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        <div class="card-body py-8">
            @include('admin.layout.error')

            <form role="form" method="post" action="{{ route('change.student.grope') }}" class="form">
                @csrf
                <div class="row mb-8" style="display: none" id="warning_div">
                    <div class="col-12">
                        <div class="alert alert-dismissible bg-light-warning d-flex flex-column flex-sm-row p-5 mb-10">
                            <i class="bi bi-exclamation-triangle fs-2hx text-warning me-4 mb-5 mb-sm-0"></i>
                            <div class="d-flex flex-column pe-0 pe-sm-10" id="warning_alert">
                                <span>يرجى الانتباه بأن حالة هذه الشعبة معطلة لذلك ستبقى حالة الطالب معطلة في هذه الشعبة!</span>
                            </div>
                            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                                <i class="bi bi-x fs-1 text-warning"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-10">
                    <div class="col-md-6 offset-md-3">
                        <label class="form-label fw-bold fs-6 mb-2">المجموعات المتاحة <span class="text-danger">*</span></label>
                        <select name="grope" id="classes" class="form-select form-select-solid select2" data-control="select2" data-placeholder="اختر المجموعة...">
                            <option></option>
                            @foreach ($grope as $item)
                                <option value="{{ $item->id }}" data-foo="{{ $item->status }}"
                                    {{ $group_id == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <input type="hidden" value="{{ Crypt::encrypt($group_id) }}" name="group_id">
                <input type="hidden" value="{{ Crypt::encrypt($group_students->id) }}" name="id">
                <input type="hidden" value="{{ Crypt::encrypt($student_id) }}" name="student_id">

                <div class="separator separator-dashed my-8"></div>

                <div class="d-flex justify-content-center gap-3">
                    <button type="submit" class="btn btn-primary px-10">
                        <span class="indicator-label">حفظ التعديلات</span>
                    </button>
                    <a href="{{ URL::previous() }}" class="btn btn-light px-10">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@stop
<style>
    #thumbnail_image,#thumbnail {
        direction: ltr;
    }
</style>
@stop

@section('js')
<link href="{{ asset('assets/admin/global/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/admin/global/plugins/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
<script src="{{asset('assets/admin/global/plugins/select2/js/select2.full.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/admin/pages/scripts/components-select2.js')}}" type="text/javascript"></script>
<script>


    $( document ).ready(function() {
    var status =  $("#class_status").val();
    var course_status =  $("#course_status").val();
    var course_name =  $("#course_name").val();
    // alert(status);
    if(course_status == 0){
        $("#warning_div").css('visibility','revert');
        // $("#warning_alert").html('<h5>يرجى الانتباه بأن حالة هذه الشعبة معطلة لذلك ستبقى حالة الطالب  معطلة في هذه الشعبة!</h5>');
    }
    if(status == 0){
        $("#warning_div").css('visibility','revert');
        $("#warning_alert").html('<h5>يرجى الانتباه بأن حالة هذه الشعبة معطلة لذلك ستبقى حالة الطالب  معطلة في هذه الشعبة!</h5>');
    }
});
</script>
@stop 