@extends('admin.layout.master')

@section('title')
    الرد ع رسالة
@stop

@section('css')

@stop

@section('page-breadcrumb')
    <ul class="page-breadcrumb">
        <li>
            <a href="{{ route('dashboard.view') }}">الرئيسية</a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="{{ route('contacts.view') }}">إدارة اتصل بنا</a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <strong> {{ $info->name }}</strong>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="{{ route('contacts.reply',['id' => Crypt::encrypt($info->id)]) }}">الرد ع رسالة</a>
        </li>
    </ul>
@stop

@section('page-title')
    <h1 class="page-title"> إدارة اتصل بنا
        <small></small>
    </h1>
@stop

@section('page-content')
    <div class="portlet box {{ $form_class }}">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-grid"></i>الرد ع رسالة </div>
        </div>
        <div class="portlet-body form">
            @include('admin.layout.error')
            <form role="form" method="post" action="" class="form-horizontal" enctype="multipart/form-data">
                <div class="form-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="control-label col-md-3">الرسالة</label>
                            <div class="col-md-6">
                                <textarea name="description" id="description" class="form-control" rows="3" readonly>{{ $info->message }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">الرد ع الرسالة</label>
                            <div class="col-md-6">
                                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="col-md-offset-3 col-md-6">
                        <button type="submit" class="btn default {{ $btn_class }}">حفظ</button>
                        <a href="{{ route('contacts.view') }}" type="button" class="btn default">إلغاء</a>
                        {{ csrf_field() }}
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')

@stop