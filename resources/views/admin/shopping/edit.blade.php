@extends('admin.layout.master')

@section('title')
تعديل صنف تسوق
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
        <a href="{{ route('shopping.view') }}">إدارة اصناف التسوق</a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <strong> {{ $info->title }}</strong>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <a href="{{ route('shopping.edit',['id' => Crypt::encrypt($info->id)]) }}">تعديل صنف تسوق</a>
    </li>
</ul>
@stop

@section('page-title')
<h1 class="page-title"> اصناف التسوق
    <small>تعديل صنف تسوق</small>
</h1>
@stop

@section('page-content')
<div class="portlet box {{ $form_class }}">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-book-open"></i>تعديل صنف تسوق </div>
    </div>
    <div class="portlet-body form">
        @include('admin.layout.error')
        <form role="form" method="post" action="" class="form-horizontal" enctype="multipart/form-data">
            <div class="form-body">
                <div class="row">
                    <div class="form-group">
                        <label class="control-label col-md-3">الأقسام</label>
                        <div class="col-md-6">
                            <select name="category_id" class="form-control" id="category_id">
                                @foreach($categories as $item)
                                <option value="{{ $item->id }}" {{ $info->category_id == $item->id ? 'selected' : '' }}> {{ $item->name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">العنوان</label>
                        <div class="col-md-6">
                            <textarea name="title" class="form-control">{!! $info->title !!}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">التفاصيل</label>
                        <div class="col-md-6">
                            <textarea name="descs" id="descs" class="form-control" rows="3">{!! $info->descs !!}</textarea>
                        </div>
                    </div>
                    <div id="image" class="form-group">
                        <label class="control-label col-md-3">صورة</label>
                        <div class="col-md-5">
                            <input id="thumbnail" value="{{ $info->image }}" class="form-control" type="text" name="image" readonly>
                            <img id="holder" src="{{ asset($info->thumb) }}" style="margin-top:15px;max-height:100px;">
                        </div>
                        <div class="col-md-1">
                            <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                                <i class="fa fa-picture-o"></i> حدد صورة
                            </a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">كلمات مفتاحية</label>
                        <div class="col-md-6">
                            <input type="text" value="{{ $info->tags }}" name="tags" id="tags" class="form-control input-large" data-role="tagsinput">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">نشر</label>
                        <div class="col-md-6">
                            <input type="checkbox" value="1" name="status" class="make-switch" data-on-text="&nbsp;نعم&nbsp;" data-off-text="&nbsp;لا&nbsp;" {{ $info->status == 1 ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="col-md-offset-3 col-md-6">
                    <button type="submit" class="btn default {{ $btn_class }}">حفظ</button>
                    <a href="{{ route('shopping.view') }}" type="button" class="btn default">إلغاء</a>
                    {{ csrf_field() }}
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="vendor/laravel-filemanager/js/lfm.js"></script>
<script src="{{asset('assets/admin/ckeditor/ckeditor.js')}}" type="text/javascript"></script>
<script type="text/javascript">
CKEDITOR.replace('descs');
</script>
<script type="text/javascript">
    var domain = "{{ asset('/admin').'/file_manager' }}";
    $('#lfm').filemanager('image', {prefix: domain});</script>
@stop