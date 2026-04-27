@extends('admin.layout.master')

@section('title')
    تغيير كلمة المرور
@stop

@section('css')

@stop

@section('page-breadcrumb')
    <ul class="page-breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="{{ route('dashboard.view') }}">الرئيسية</a>
            <i class="fa fa-angle-left"></i>
        </li>
        <li>
            <a href="{{ route('teachers.view') }}">إدارة المعلمين</a>
            <i class="fa fa-angle-left"></i>
        </li>
        <li>
            <strong> {{  $info->name }}</strong>
            <i class="fa fa-angle-left"></i>
        </li>
        <li>
            <span>تغيير كلمة المرور</span>
        </li>
    </ul>
@stop

@section('page-title')
    <h1 class="page-title">إدارة المعلمين
        <small></small>
    </h1>
@stop

@section('page-content')
    <div class="portlet box {{ $form_class }}">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-key"></i>تغيير كلمة المرور</div>
                <div class="actions">
                    <a href="{{ URL::previous() }}" class="btn btn-default btn-sm" style="color: #fffff">
                        <i class="fa fa-backward" style="color: #ffc038"></i> <strong style="color: #ffffff"> رجوع
                        </strong> </a>
                </div>
        </div>
        <div class="portlet-body form">
            @include('admin.layout.error')
            <form method="post" action="" role="form" class="form-horizontal">
                <div class="form-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="control-label col-md-3">الاسم</label>
                            <div class="col-md-6">
                                <input type="text" value="{{ $info->name }}" name="name" id="name" class="form-control" placeholder="الاسم" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">كلمة المرور</label>
                            <div class="col-md-6">
                                <input type="password" value="" name="password" id="password" class="form-control" placeholder="كلمة المرور">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">تأكيد كلمة المرور</label>
                            <div class="col-md-6">
                                <input type="password" value="" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="تأكيد كلمة المرور">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="col-md-offset-3 col-md-6 text-center">
                        <button type="submit" class="btn default {{ $btn_class }}">حفظ</button>
                        <a href="{{ route('teachers.view') }}" type="button" class="btn default">إلغاء</a>
                        {{ csrf_field() }}
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
