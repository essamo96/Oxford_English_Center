@extends('admin.layout.master')

@section('title')
    تعديل إعلان
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
            <a href="{{ route('advertisements.view') }}">إدارة الإعلانات</a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="{{ route('advertisements.add') }}">إضافة إعلان جديد</a>
        </li>
    </ul>
@stop

@section('page-title')
    <h1 class="page-title"> الإعلانات
        <small>إضافة إعلان</small>
    </h1>
@stop

@section('page-content')
    <div class="portlet box {{ $form_class }}">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-grid"></i>تعديل إعلان جديد </div>
        </div>
        <div class="portlet-body form">
            @include('admin.layout.error')
            <form role="form" method="post" action="" class="form-horizontal" enctype="multipart/form-data">
                <div class="form-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="control-label col-md-3">الإسم</label>
                            <div class="col-md-6">
                                <input type="text" value="{{ old('name') }}" name="name" id="name" class="form-control" placeholder="الإسم">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">صورة</label>
                            <div class="col-md-6">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                        <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt=""/>
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                    </div>
                                    <div>
                                            <span class="btn default btn-file">
                                            <span class="fileinput-new">
                                            تحديد صورة </span>
                                            <span class="fileinput-exists">
                                            تغيير الصورة </span>
                                            <input id="image_file" type="file" name="image">
                                            </span>
                                        <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput">
                                            حذف الصورة </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">النوع</label>
                            <div class="col-md-6">
                                <select name="type" id="type" class="bs-select form-control">
                                    <option value="1" {{ old('type') == 1 ? 'selected' : '' }}>الإعلان الأول</option>
                                    <option value="2" {{ old('type') == 2 ? 'selected' : '' }}>الإعلان الثاني</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">تاريخ الإنتهاء</label>
                            <div class="col-md-6">
                                <input id="expiry_date" name="expiry_date" class="form-control form-control-inline datepicker" data-date-format="yyyy-mm-dd" size="16" type="text" value="{{ old('expiry_date') }}" readonly placeholder="تاريخ الإنتهاء" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">الرابط</label>
                            <div class="col-md-6">
                                <input type="text" value="{{ old('url') }}" name="url" id="url" class="form-control" placeholder="الرابط">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">الحالة</label>
                            <div class="col-md-6">
                                <input type="checkbox" value="1" name="status" class="make-switch" data-on-text="&nbsp;تفعيل&nbsp;" data-off-text="&nbsp;تعطيل&nbsp;" {{ old('status') == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="col-md-offset-3 col-md-6">
                        <button type="submit" class="btn default {{ $btn_class }}">حفظ</button>
                        <a href="{{ route('categories.view') }}" type="button" class="btn default">إلغاء</a>
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
