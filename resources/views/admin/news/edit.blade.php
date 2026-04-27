@extends('admin.layout.master')

@section('title', 'تعديل الخبر')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('news.view') }}" class="text-muted text-hover-info">إدارة الأخبار</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">تعديل الخبر</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-pencil fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> تعديل الخبر: {{ $info->title }}
            </span>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('news.view') }}" class="btn btn-light-info btn-sm fw-bold">
                <i class="ki-duotone ki-black-right me-1 fs-5"></i> رجوع
            </a>
        </div>
    </div>
    
    <div class="card-body py-10">
        @include('admin.layout.error')
        <form role="form" method="post" action="" class="form" enctype="multipart/form-data">
            @csrf
            <div class="row g-9 mb-8">
                <!-- Category -->
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">القسم</label>
                    <select name="category_id" id="category_id" class="form-select form-select-solid" data-control="select2">
                        @foreach($categories as $item)
                        <option value="{{ $item->id }}" {{ $info->category_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Title -->
                <div class="col-md-6 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">عنوان الخبر</label>
                    <input type="text" value="{{ $info->title }}" name="title" id="title" class="form-control form-control-solid" placeholder="أدخل العنوان هنا...">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <!-- Author -->
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الكاتب</label>
                    <input type="text" value="{{ $info->onwer }}" name="onwer" id="onwer" class="form-control form-control-solid" placeholder="اسم كاتب الخبر">
                </div>
                
                <!-- Source -->
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">مصدر الخبر</label>
                    <input type="text" value="{{ $info->source }}" name="source" id="source" class="form-control form-control-solid" placeholder="مصدر الخبر الأصلي">
                </div>
            </div>

            <!-- Summary -->
            <div class="fv-row mb-8">
                <label class="fs-6 fw-semibold mb-2">مقدمة الخبر</label>
                <textarea name="sub" id="sub" maxlength="130" class="form-control form-control-solid" rows="3">{{ $info->sub }}</textarea>
            </div>

            <!-- Details -->
            <div class="fv-row mb-8">
                <label class="required fs-6 fw-semibold mb-2">تفاصيل الخبر</label>
                <textarea name="descs" id="descs" class="form-control ckeditor">{!! $info->descs !!}</textarea>
            </div>

            <!-- Image -->
            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">صورة الخبر</label>
                    <div class="input-group">
                        <input id="thumbnail" value="{{ $info->image }}" class="form-control form-control-solid" type="text" name="image" readonly>
                        <button id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-info" type="button">
                            <i class="ki-duotone ki-picture fs-3"><span class="path1"></span><span class="path2"></span></i> تغيير الصورة
                        </button>
                    </div>
                    <div id="holder" class="mt-3" style="max-height:150px;">
                        @if($info->image)
                            <img src="{{ asset($info->image) }}" class="rounded shadow-sm mw-100" style="max-height:150px;">
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">التعليق على الصورة</label>
                    <input type="text" value="{{ $info->img_notes }}" name="img_notes" id="img_notes" class="form-control form-control-solid" placeholder="اكتب وصفاً للصورة">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <!-- Tags -->
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">كلمات مفتاحية</label>
                    <input type="text" value="{{ $info->tags }}" name="tags" id="tags" class="form-control form-control-solid" data-role="tagsinput">
                </div>
                
                <!-- Publish Date -->
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">تاريخ النشر</label>
                    <div class="position-relative d-flex align-items-center">
                        <i class="ki-duotone ki-calendar-8 fs-2 position-absolute mx-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                        <input type="text" name="pub_date" id="pub_date" value="{{ $info->pub_date }}" class="form-control form-control-solid ps-12 date-picker" placeholder="اختر التاريخ">
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-10">
                <!-- Resort -->
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الترتيب</label>
                    <select name="resort" id="resort" class="form-select form-select-solid">
                        @for($i = 1; $i < 11; $i++)
                        <option value="{{ $i }}" {{ $info->resort == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                
                <!-- Checks -->
                <div class="col-md-4 d-flex align-items-center">
                    @can('admin.news.publish')
                    <div class="form-check form-switch form-check-custom form-check-solid me-10">
                        <input class="form-check-input h-30px w-50px" type="checkbox" value="1" name="publish" id="publish_switch" {{ $info->publish == 1 ? 'checked' : '' }} />
                        <label class="form-check-label fw-bold text-gray-700" for="publish_switch">منشور</label>
                    </div>
                    @endcan
                </div>

                <div class="col-md-4 d-flex align-items-center">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" value="1" name="sidebar" id="sidebar_switch" {{ $info->sidebar == 1 ? 'checked' : '' }} />
                        <label class="form-check-label fw-bold text-gray-700" for="sidebar_switch">مثبت (Sidebar)</label>
                    </div>
                </div>
            </div>

            <div class="text-center pt-10">
                <button type="submit" class="btn btn-info px-15 me-3 fw-bold">
                    <i class="ki-duotone ki-save-2 fs-3"><span class="path1"></span><span class="path2"></span></i> حفظ التعديلات
                </button>
                <a href="{{ route('news.view') }}" class="btn btn-light px-15 fw-bold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('vendor/laravel-filemanager/js/lfm.js') }}"></script>
<script src="{{ asset('assets/admin/ckeditor/ckeditor.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    if ($('#descs').length) {
        CKEDITOR.replace('descs', {
            language: 'ar',
            height: 300
        });
    }

    var domain = "{{ url('/admin/file_manager') }}";
    $('#lfm').filemanager('image', {prefix: domain});

    $(document).ready(function () {
        $(".date-picker").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
        });
    });
</script>
@stop