@extends('admin.layout.master')

@section('title')
    الصفحة الرئيسية - تغيير كلمة المرور
@stop

@section('page-title')
    <h3 class="page-title"> الصفحة الرئيسية
        <small>تغيير كلمة المرور</small>
    </h3>
@stop

@section('page-breadcrumb')
    <ul class="page-breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="{{ route('dashboard.view') }}"> الصفحة الرئيسية</a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="{{ route('dashboard.password') }}"> تغيير كلمة المرور</a>
        </li>
    </ul>
@stop

@section('page-content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box {{ $form_class }}">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-key"></i> تغيير كلمة المرور
                    </div>
                </div>
                <div class="portlet-body form">

                    @include('admin.layout.error')

                    <form role="form" id="update_password" action="" class="form-horizontal" method="post">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-3 control-label">إسم المستخدم</label>
                                <div class="col-md-6">
                                    <input type="text" value="{{ Auth::user()->username }}" class="form-control" readonly disabled>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">كلمة المرور القديمة</label>
                                <div class="col-md-6">
                                    <input  name="old_password" value="" type="password" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">كلمة المرور الجديدة</label>
                                <div class="col-md-6">
                                    <input  name="password" value="" type="password" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">تأكيد كلمة المرور</label>
                                <div class="col-md-6">
                                    <input  name="password_confirmation" value="" type="password" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-6">
                                    <button type="submit" class="btn grey-gallery">حفظ</button>
                                    {{csrf_field()}}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('modal')

@stop

@section('js')

@stop