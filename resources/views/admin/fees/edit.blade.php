@extends('admin.layout.master')
@section('title')
تعديل رسوم طالب
@stop
@section('page-breadcrumb')
<ul class="page-breadcrumb">
    <li>
        <a href="{{ route('dashboard.view') }}">الرئيسية</a>
        <i class="fa fa-angle-left"></i>
    </li>
    <li>
        <a href="{{ route('fees.view') }}">إدارة الرسوم</a>
        <i class="fa fa-angle-left"></i>
    </li>
    <li>
        <strong> {{ $info->student->name }}</strong>
        <i class="fa fa-angle-left"></i>
    </li>
    <li>
        <a href="{{ route('fees.edit',['id' => Crypt::encrypt($info->id)]) }}">تعديل رسوم طالب</a>
    </li>
</ul>
@stop

@section('page-title')
<h1 class="page-title"> إدارة الرسوم
    <small>تعديل رسوم طالب</small>
</h1>
@stop

@section('page-content')
<div class="portlet box {{ $form_class }}">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-grid"></i>تعديل رسوم طالب </div>
            <div class="actions">
                <a href="{{ URL::previous() }}" class="btn btn-default btn-sm"  style="color: #ffffff">
                    <i class="fa fa-backward" style="color: #ffc038"></i>   <strong style="color: #ffffff">  رجوع </strong>  </a>
            </div>
    </div>
    <div class="portlet-body form">
        @include('admin.layout.error')
        <form role="form" method="post" action="" class="form-horizontal" enctype="multipart/form-data">
            <div class="form-body">
                <div class="row ">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-3">اسم الطالب</label>
                            <div class="col-md-6">
                                <input type="text" value="{{ $info->student->name }}" name="student_id" class="form-control" disabled="">
                            </div>
                        </div>                       
                        <div class="form-group">
                            <label class="control-label col-md-3">المجموعة</label>
                            <div class="col-md-6">
                                <input type="text" value="{{ $info->group->name }}" name="group_id" class="form-control" disabled="">
                            </div>
                        </div>                       
                        <div class="form-group">
                            <label class="control-label col-md-3">نوع الرسوم</label>
                            <div class="col-md-6">
                                <select name="student_paid_type" id="student_paid_type" class="form-control">
                                    <option value="0" {{ $info->student_paid_type == 0 ? 'selected' : '' }}>رسوم دورة</option>
                                    <option value="1" {{ $info->student_paid_type == 1 ? 'selected' : '' }}>رسوم كتب</option>
                                </select>                               
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">دفعة رسوم</label>
                            <div class="col-md-6">
                                <input type="text" value="{{ $info->student_fee_paid }}" class="form-control" name="student_fee_paid" placeholder="دفعة رسوم">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="col-md-offset-3 col-md-6">
                    <button type="submit" class="btn default {{ $btn_class }}">حفظ</button>
                    <a href="{{ route('fees.view') }}" type="button" class="btn default">إلغاء</a>
                    {{ csrf_field() }}
                </div>
            </div>
        </form>
    </div>
</div>
@stop