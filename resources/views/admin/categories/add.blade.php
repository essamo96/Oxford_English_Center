@extends('admin.layout.master')

@section('title', 'إضافة تصنيف')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('categories.view') }}" class="text-muted text-hover-info">إدارة التصنيفات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">إضافة تصنيف</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">إضافة تصنيف جديد</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ URL::previous() }}" class="btn btn-sm btn-light btn-active-light-primary">
                <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <form role="form" method="post" action="" enctype="multipart/form-data" class="form d-flex flex-column gap-7">
            {{ csrf_field() }}
            
            <div class="row mb-6">
                <label class="col-lg-3 col-form-label fw-semibold fs-6">الإسم</label>
                <div class="col-lg-9">
                    <input type="text" value="{{ old('name') }}" name="name" class="form-control form-control-solid" placeholder="الإسم">
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-3 col-form-label fw-semibold fs-6">القسم الاب</label>
                <div class="col-lg-9">
                    <select name="category_id" class="form-select form-select-solid">
                        <option value="0" {{ old('category_id') == 0 ? 'selected' : '' }}>لا يوجد قسم اب</option>
                        @foreach($categories as $item)
                        <option value="{{ $item->id }}" {{ old('category_id') == $item->id ? 'selected' : '' }}> {{ $item->name }} </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-3 col-form-label fw-semibold fs-6">الترتيب</label>
                <div class="col-lg-9">
                    <input type="number" value="{{ old('sort') }}" name="sort" class="form-control form-control-solid" placeholder="الترتيب">
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-3 col-form-label fw-semibold fs-6">الكلمات الدلالية</label>
                <div class="col-lg-9">
                    <input type="text" value="{{ old('tags') }}" name="tags" class="form-control form-control-solid" data-role="tagsinput" placeholder="الكلمات الدلالية">
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-3 col-form-label fw-semibold fs-6">صورة</label>
                <div class="col-lg-9">
                    <div class="input-group">
                        <input id="thumbnail" value="{{ old('color') }}" class="form-control form-control-solid" type="text" name="color" readonly>
                        <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                            <i class="ki-duotone ki-picture fs-2"><span class="path1"></span><span class="path2"></span></i> حدد صورة
                        </a>
                    </div>
                    <img id="holder" style="margin-top:15px;max-height:100px;">
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-3 col-form-label fw-semibold fs-6">الحالة</label>
                <div class="col-lg-9 d-flex align-items-center">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="1" name="status" id="statusSwitch" {{ old('status') == 1 ? 'checked' : '' }}/>
                        <label class="form-check-label" for="statusSwitch">تفعيل</label>
                    </div>
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-3 col-form-label fw-semibold fs-6">يظهر في القائمة</label>
                <div class="col-lg-9 d-flex align-items-center">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="1" name="in_menu" id="inMenuSwitch" {{ old('in_menu') == 1 ? 'checked' : '' }}/>
                        <label class="form-check-label" for="inMenuSwitch">تفعيل</label>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('categories.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                <button type="submit" class="btn btn-primary">حفظ</button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('vendor/laravel-filemanager/js/lfm.js') }}?v=2"></script>
<script type="text/javascript">
    var domain = "{{ url('/admin/file_manager') }}";
    $('#lfm').filemanager('image', {prefix: domain});
</script>
@stop
