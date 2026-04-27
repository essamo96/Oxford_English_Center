@extends('admin.layout.master')

@section('title')
اضافة رسوم طالب 
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
        <a href="{{ route('fees.view') }}">إدارة الرسوم</a>
        <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <a href="{{ route('fees.add') }}">اضافة رسوم طالب </a>
    </li>
</ul>
@stop

@section('page-title')
<h1 class="page-title"> إدارة الرسوم
    <small>اضافة رسوم طالب </small>
</h1>
@stop

@section('page-content')
<div class="portlet box {{ $form_class }}">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-grid"></i>اضافة رسوم طالب  </div>
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
                            <div class="student">
                                <label class="control-label  col-md-3">اسم الطالب</label>
                                <div class="col-md-6">
                                    <input type="text" value="" class="form-control student_name" placeholder="اسم الطالب" autocomplete="off">
                                    <input type="hidden" value="" name="student_id" class="search-val s">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">المجموعة</label>
                            <div class="col-md-6">
                                <select name="group_id" id="group_id" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">نوع الرسوم</label>
                            <div class="col-md-6">
                                <select name="student_paid_type" id="student_paid_type" class="form-control">
                                    <option value="0">رسوم دورة</option>
                                    <option value="1">رسوم كتب</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">دفعة رسوم</label>
                            <div class="col-md-6">
                                <input type="text" value="" class="form-control" name="student_fee_paid" placeholder="دفعة رسوم">
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
@section('js')
<link href="assets/admin/global/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
<script src="assets/admin/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script>
function get_student_ajax_group(id) {
    var token = '{{csrf_token()}}';
    $.ajax({
        url: "<?php echo route('groups.ajax.search') ?>",
        method: 'POST',
        data: {student_id: id, _token: token},
        success: function (response) {
            $.each(response, function (key, value) {
                $('#group_id').append("<option " + (value.id == '<?= 1 ?>' ? 'selected' : '') + " value='" + value.id + "'>" + value.name + "</option>");
            });
        }
    });
}
var options1 = {
    source: "{{ route('groups.student.search') }}",
    minLength: 1,
    focus: function (event, ui) {
        bval = $(this).closest('.student').find('.search-val');
        bname = $(this).closest('.student').find('.student_name');
        bname.val(ui.item.label);
        bval.val(ui.item.value);
        return false;
    },
    select: function (event, ui) {
        $('#group_id').empty();
        bval = $(this).closest('.student').find('.search-val');
        bname = $(this).closest('.student').find('.student_name');
        bname.val(ui.item.label);
        bval.val(ui.item.value);
        get_student_ajax_group(ui.item.value);
        return false;
    }
}

$(".student_name").autocomplete(options1);
</script>
@stop
