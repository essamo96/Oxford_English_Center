@extends('admin.layout.master')

@section('title')
    الصفحة الرئيسية - الملف الشخصي
@stop

@section('page-title')
    <h3 class="page-title"> الصفحة الرئيسية
        <small>الملف الشخصي</small>
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
            <a href="{{ route('dashboard.profile') }}"> الملف الشخصي</a>
        </li>
    </ul>
@stop

@section('page-content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box {{ $form_class }}">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-folder-open-o"></i> الملف الشخصي
                    </div>
                </div>
                <div class="portlet-body form">
                    @include('admin.layout.error')
                    <form role="form" id="update_password" action="" class="form-horizontal" method="post">
                        <div class="form-body">
                            <h3 class="form-section">معلومات الحساب</h3>
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">إسم المستخدم</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static">
                                            {{ $info->username }}
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">الإسم الكامل</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static">
                                            {{ $info->name }}
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">كلمة المرور</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static">
                                            {{ $info->email }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop