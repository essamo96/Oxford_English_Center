@extends('admin.layout.master')

@section('title')
تعديل مادة تعليمية
@stop

@section('css')

@stop

@section('page-breadcrumb')
<ul class="page-breadcrumb">
    <li>
        <a href="{{ route('dashboard.view') }}">الرئيسية</a>
        <i class="fa fa-angle-left"></i>
    </li>
    <li>
        <a href="{{ route('files.view') }}">إدارة المواد التعليمية</a>
        <i class="fa fa-angle-left"></i>
    </li>
    <li>
        <strong> {{ $info->title }}</strong>
        <i class="fa fa-angle-left"></i>
    </li>
    <li>
        <a href="{{ route('files.edit',['id' => Crypt::encrypt($info->id)]) }}">تعديل مادة تعليمية</a>
    </li>
</ul>
@stop

@section('page-title')
<h1 class="page-title"> المواد التعليمية
    <small>تعديل مادة تعلمية</small>
</h1>
@stop

@section('page-content')
<div class="portlet box {{ $form_class }}">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-grid"></i>تعديل مادة تعليمية </div>
    </div>
    <div class="portlet-body form">
        @include('admin.layout.error')
        <form role="form" method="post" action="" class="form-horizontal" enctype="multipart/form-data">
            <div class="form-body">
                <div class="row">
                     <div class="form-group">
                        <label class="control-label col-md-3">البرنامج</label>
                        <div class="col-md-6">
                            <select name="program_id" id="program_id" class="form-control">                               
                                @foreach($programs as $item)
                                <option value="{{ $item->id }}" {{ $info->program_id  == $item->id ? 'selected' : '' }}> {{ $item->title }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">العنوان</label>
                        <div class="col-md-6">
                            <input type="text" value="{{ $info->title }}" name="title" id="title" class="form-control" placeholder="العنوان">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">الوصف</label>
                        <div class="col-md-6">
                            <input type="text" value="{{ $info->descs }}" name="descs" id="descs" class="form-control" placeholder="الوصف">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">الملف</label>
                        <div class="col-md-6">
                            <div class="fileinput fileinput-new" data-provides="fileinput">

                                <div>
                                    <span class="fileinput-new btn green">
                                        <a href="File/Images/files/{{ $info->image }}" target="_blank">
                                            معاينة الملف
                                        </a>
                                    </span>
                                    <span class="btn default btn-file">
                                        <span class="fileinput-new">
                                            تحديد ملف 
                                        </span>
                                        <span class="fileinput-exists">
                                            تغيير الملف </span>
                                        <input id="image_file" type="file" name="image">
                                    </span>
                                    <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput">
                                        حذف الملف </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">الحالة</label>
                        <div class="col-md-6">
                            <input type="checkbox" value="1" name="status" class="make-switch" data-on-text="&nbsp;تفعيل&nbsp;" data-off-text="&nbsp;تعطيل&nbsp;" {{ $info->status == 1 ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="col-md-offset-3 col-md-6">
                    <button type="submit" class="btn default {{ $btn_class }}">حفظ</button>
                    <a href="{{ route('files.view') }}" type="button" class="btn default">إلغاء</a>
                    {{ csrf_field() }}
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')

@stop

@section('modals')

@stop
