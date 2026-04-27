@extends('admin.layout.master')

@section('title', 'تعديل الصفحة: ' . $info->title)

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('pages.view') }}" class="text-muted text-hover-info">إدارة الصفحات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">تعديل الصفحة</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-pencil fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> تعديل الصفحة الثابتة: {{ $info->title }}
            </span>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('pages.view') }}" class="btn btn-light-info btn-sm fw-bold">
                <i class="ki-duotone ki-black-right me-1 fs-5"></i> رجوع
            </a>
        </div>
    </div>
    
    <div class="card-body py-10">
        @include('admin.layout.error')
        <form role="form" method="post" action="" class="form" enctype="multipart/form-data">
            @csrf
            <div class="row g-9 mb-8">
                <!-- Name -->
                <div class="col-md-12 fv-row">
                    <label class="required fs-6 fw-semibold mb-2">اسم الصفحة</label>
                    <input type="text" value="{{ $info->title }}" name="title" id="title" class="form-control form-control-solid" placeholder="مثال: سياسة الخصوصية">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <!-- Keywords/Tags -->
                <div class="col-md-12 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الكلمات الدلالية (Meta Keywords)</label>
                    <input type="text" value="{{ $info->tags }}" name="tags" id="tags" class="form-control form-control-solid" data-role="tagsinput">
                </div>
            </div>

            <!-- Details -->
            <div class="fv-row mb-8">
                <label class="required fs-6 fw-semibold mb-2">محتوى الصفحة بالتفصيل</label>
                <textarea name="details" id="details" class="form-control ckeditor">{!! $info->details !!}</textarea>
            </div>

            <div class="row g-9 mb-8">
                <!-- Banner Image (File Manager) -->
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">صورة الصفحة (Banner)</label>
                    <div class="input-group">
                        <input id="thumbnail" value="{{ $info->banner }}" class="form-control form-control-solid" type="text" name="banner" readonly>
                        <button id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-info" type="button">
                            <i class="ki-duotone ki-picture fs-3"><span class="path1"></span><span class="path2"></span></i> اختيار صورة
                        </button>
                    </div>
                    <div id="holder" class="mt-3" style="max-height:100px;">
                        @if($info->banner)
                            <img src="{{ asset($info->banner) }}" class="rounded shadow-sm" style="max-height:100px;">
                        @endif
                    </div>
                </div>

                <!-- Video URL -->
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">رابط فيديو (YouTube/Vimeo)</label>
                    <input type="text" value="{{ $info->url }}" name="url" id="url" class="form-control form-control-solid" placeholder="https://www.youtube.com/watch?v=...">
                </div>
            </div>

            <div class="separator separator-dashed my-10"></div>

            <div class="row g-9 mb-8">
                <!-- Age & Level -->
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الفئة العمرية</label>
                    <input type="text" value="{{ $info->age }}" name="age" id="age" class="form-control form-control-solid">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">المستوى</label>
                    <input type="text" value="{{ $info->level }}" name="level" id="level" class="form-control form-control-solid">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <!-- Weeks & Hours -->
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">عدد الأسابيع</label>
                    <input type="text" value="{{ $info->weeks }}" name="weeks" id="weeks" class="form-control form-control-solid">
                </div>
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">عدد الساعات</label>
                    <input type="text" value="{{ $info->hours }}" name="hours" id="hours" class="form-control form-control-solid">
                </div>
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الامتحانات (Mock)</label>
                    <input type="text" value="{{ $info->mock }}" name="mock" id="mock" class="form-control form-control-solid">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <!-- Price & Fees -->
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">رسوم الدورة</label>
                    <input type="text" value="{{ $info->price }}" name="price" id="price" class="form-control form-control-solid">
                </div>
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">رسوم الكتب</label>
                    <input type="text" value="{{ $info->fees }}" name="fees" id="fees" class="form-control form-control-solid">
                </div>
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">عدد الطلاب (Class Size)</label>
                    <input type="text" value="{{ $info->class_size }}" name="class_size" id="class_size" class="form-control form-control-solid">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <!-- Start, Days, Time -->
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">تاريخ البداية</label>
                    <input type="text" value="{{ $info->start }}" name="start" id="start" class="form-control form-control-solid">
                </div>
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">أيام الدراسة</label>
                    <input type="text" value="{{ $info->days }}" name="days" id="days" class="form-control form-control-solid">
                </div>
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الوقت</label>
                    <input type="text" value="{{ $info->time }}" name="time" id="time" class="form-control form-control-solid">
                </div>
            </div>

            <div class="row g-9 mb-10">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">المدة (Duration)</label>
                    <input type="text" value="{{ $info->duration }}" name="duration" id="duration" class="form-control form-control-solid">
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-30px w-50px" type="checkbox" value="1" name="status" id="status_switch" {{ $info->status == 1 ? 'checked' : '' }} />
                        <label class="form-check-label fw-bold text-gray-700" for="status_switch">حالة الصفحة (نشط / معطل)</label>
                    </div>
                </div>
            </div>

            <div class="text-center pt-10">
                <button type="submit" class="btn btn-info px-15 me-3 fw-bold">
                    <i class="ki-duotone ki-save-2 fs-3"><span class="path1"></span><span class="path2"></span></i> حفظ التعديلات
                </button>
                <a href="{{ route('pages.view') }}" class="btn btn-light px-15 fw-bold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('vendor/laravel-filemanager/js/lfm.js') }}"></script>
<script src="{{ asset('assets/admin/ckeditor/ckeditor.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    if ($('#details').length) {
        CKEDITOR.replace('details', {
            language: 'ar',
            height: 400
        });
    }

    var domain = "{{ url('/admin/file_manager') }}";
    $('#lfm').filemanager('image', {prefix: domain});
</script>
@stop