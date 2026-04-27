@extends('admin.layout.master')

@section('title')
اضافة مادة تعليمية 
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
        <a href="{{ route('files.view') }}">إدارة المواد التعليمية </a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <a href="{{ route('files.add') }}">إضافة  مادة تعليمية</a>
    </li>
</ul>
@stop

@section('page-title')
<h1 class="page-title"> المواد التعليمية
    <small>إضافة مادة تعليمية</small>
</h1>
@stop

@section('page-content')
<div class="portlet box {{ $form_class }}">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-grid"></i>إضافة مادة تعليمية </div>
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
                                <option value="{{ $item->id }}" {{ old('program_id')  == $item->id ? 'selected' : '' }}> {{ $item->title }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">العنوان</label>
                        <div class="col-md-6">
                            <input type="text" value="{{ old('title') }}" name="title" id="title" class="form-control" placeholder="العنوان">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">وصف </label>
                        <div class="col-md-6">
                            <input type="text" value="{{ old('descs') }}" name="descs" id="descs" class="form-control" placeholder="وصف ">
                        </div>
                    </div>
<!--                    <div id="imageContainer">
                        <div class="row imageTemplate" style="margin-bottom: 20px;">
                            <label class="control-label col-md-3">ملف المادة التعليمية</label>
                            <div class="col-md-3">
                                <input id="thumbnail" class="form-control clear" type="text" name="image[]" style="direction: ltr">
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group">
                                    <button id="lfm" data-input="thumbnail" data-preview="holder" type="button" class="btn blue">
                                        <i class="icon-picture"></i> اختر ملف</button>
                                    <button type="button" class="btn green addProductImage" style="padding: 6px;">
                                        <i class="icon-puzzle"></i> ملف اضافي</button>
                                    <button type="button" class="btn red removeProductImage">
                                        <i class="icon-trash" style="width: 12px;"></i> حذف</button>
                                </div>
                            </div>
                        </div>
                    </div>-->
                    
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">ملف</label>
                                            <div class="col-md-6">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div>
                                                        <span class="btn default btn-file">
                                                            <span class="fileinput-new">
                                                                تحديد ملف </span>
                                                            <span class="fileinput-exists">
                                                                تغيير ملف </span>
                                                            <input id="image_file" type="file" name="image">
                                                        </span>
                                                        <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput">
                                                            حذف ملف </a>
                                                    </div>
                                                </div>
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
                    <a href="{{ route('files.view') }}" type="button" class="btn default">إلغاء</a>
                    {{ csrf_field() }}
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
@section('css')
<!-- BEGIN PAGE LEVEL PLUGINS --> 
<link href="assets/admin/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
<link href="assets/admin/global/plugins/jquery-multi-select/css/multi-select-rtl.css" rel="stylesheet" type="text/css" />
<link href="assets/admin/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="assets/admin/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@stop
@section('js')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="assets/admin/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script src="assets/admin/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
<script src="assets/admin/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="assets/admin/pages/scripts/components-multi-select.min.js?v=3" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script src="vendor/laravel-filemanager/js/lfm.js"></script>
<script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
<script src="{{asset('assets/admin/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>

<script>
$('.date-picker').datepicker();
var options = {
    filebrowserImageBrowseUrl: '{{ asset("admin/file_manager?type=Images") }}',
    filebrowserImageUploadUrl: '{{ asset("admin/file_manager/upload?type=Images& _token=") }}',
    filebrowserBrowseUrl: '{{ asset("admin/file_manager?type=Files") }}',
    filebrowserUploadUrl: '{{ asset("admin/file_manager/upload?type=Files&_token=") }}'
}
</script>
<script>
    CKEDITOR.replace('description', options);
    CKEDITOR.replace('details', options);
    CKEDITOR.replace('useway', options)
</script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    ////////////////////////////////////////////////////////////
    var domain = "{{ asset('/admin').'/file_manager' }}";
    var stdHtml = $("#imageContainer").children(".imageTemplate").first().clone('.addProductImage');
    $("#imageContainer").html('');
    var counter = 0;
    $(document).on('click', '.addProductImage', function () {
        append_image_controller();
    });
    function append_image_controller() {
        counter++;
        var objHtml = stdHtml.clone('.addProductImage');
        objHtml.find("#thumbnail").attr("id", "thumbnail" + counter);
        objHtml.find("#holder").attr("id", "holder" + counter);
        objHtml.find("#lfm").attr("data-input", "thumbnail" + counter);
        objHtml.find("#lfm").attr("data-preview", "holder" + counter);
        objHtml.find("#lfm").attr("id", "lfm" + counter);
        $("#imageContainer").append(objHtml);
        $('#lfm' + counter).filemanager('file', {prefix: domain});
    }
    /////////////////////////////////////////////
    $(document).on("click", ".removeProductImage", function () {
        if ($('#imageContainer .imageTemplate').length > 1)
        {
            $(this).closest(".imageTemplate").remove();
        } else
        {
            toastr.warning("Error, Can't delete last row");
        }
    });
    /////////////////////////////////////////////
    append_image_controller();
    ////////////////////////////////////////////
    $('#lfm3').filemanager('file', {prefix: domain});
    $('#lfm4').filemanager('file', {prefix: domain});
</script>
@stop

