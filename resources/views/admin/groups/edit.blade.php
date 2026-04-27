@extends('admin.layout.master')

@section('title')
    تعديل بيانات المجموعة
@stop

@section('page-title')
    تعديل بيانات المجموعة
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('groups.view') }}" class="text-muted text-hover-info">إدارة المجموعات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">تعديل بيانات المجموعة</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">تعديل بيانات المجموعة: {{ $info->name }}</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('groups.view') }}" class="btn btn-sm btn-light btn-active-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.masterLayouts.error')
            <form role="form" method="post" action="" class="form d-flex flex-column gap-7" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">اسم المجموعة</label>
                        <input type="text" value="{{ $info->name }}" name="name" id="name"
                            class="form-control form-control-solid" placeholder="اسم المجموعة">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">اسم البرنامج</label>
                        <select name="program_id" id="program_id" data-control="select2"
                            class="form-select form-select-solid">
                            @foreach ($programs as $item)
                                <option value="{{ $item->id }}" {{ $info->program_id == $item->id ? 'selected' : '' }}>
                                    {{ $item->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">المدرس</label>
                        <select name="teacher_id" id="teacher_id" data-control="select2"
                            class="form-select form-select-solid">
                            @foreach ($teachers as $item)
                                <option value="{{ $item->id }}" {{ $info->teacher_id == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">تاريخ و وقت الحصة</label>
                        <select name="date_id" id="date_id" data-control="select2" class="form-select form-select-solid">
                            @foreach ($times as $item)
                                <option value="{{ $item->id }}" {{ $info->date_id == $item->id ? 'selected' : '' }}>
                                    {{ $item->days . '::' . $item->times }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">رابط الزوم للجلسات</label>
                        <input type="text" value="{{ $info->zoom }}" name="zoom" id="zoom"
                            class="form-control form-control-solid" placeholder="رابط الزوم للجلسات">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">رابط ملفات جوجل درايف</label>
                        <input type="text" value="{{ $info->drive }}" name="drive" id="drive"
                            class="form-control form-control-solid" placeholder="رابط ملفات جوجل درايف">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">بدأ الحجز للمجموعة</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-solid date-picker" name="start_date"
                                value="{{ $info->start_date }}" placeholder="اختر تاريخ بدأ الحجز">
                            <span class="input-group-text"><i class="ki-duotone ki-calendar-8 fs-2"><span
                                        class="path1"></span><span class="path2"></span><span class="path3"></span><span
                                        class="path4"></span><span class="path5"></span><span
                                        class="path6"></span></i></span>
                        </div>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">نهاية الحجز للمجموعة</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-solid date-picker" name="end_date"
                                value="{{ $info->end_date }}" placeholder="اختر تاريخ نهاية الحجز">
                            <span class="input-group-text"><i class="ki-duotone ki-calendar-8 fs-2"><span
                                        class="path1"></span><span class="path2"></span><span class="path3"></span><span
                                        class="path4"></span><span class="path5"></span><span
                                        class="path6"></span></i></span>
                        </div>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">صورة المرجعية للمجموعة</label>
                        <div class="input-group">
                            <input id="thumbnail1" class="form-control form-control-solid" type="text" name="image"
                                value="{{ $info->image }}" readonly data-preview="holder" placeholder="اختر صورة المجموعة">
                            <button id="lfm_image" data-input="thumbnail1" data-preview="holder" class="btn btn-primary" type="button">
                                <i class="ki-duotone ki-picture fs-2"><span class="path1"></span><span
                                        class="path2"></span></i> تحميل صورة
                            </button>
                        </div>
                        <div id="holder" style="margin-top:15px;max-height:200px;">
                            @if($info->image)
                                <img src="{{ asset($info->image) }}" style="height: 5rem;">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">الملفات التعريفية للمجموعة</label>
                        <div class="input-group">
                            <input id="thumbnail2" class="form-control form-control-solid" type="text" name="subjects"
                                value="{{ $info->subjects }}" readonly placeholder="اختر ملف التعريف">
                            <button id="lfm_file" data-input="thumbnail2" class="btn btn-primary" type="button">
                                <i class="ki-duotone ki-file fs-2"><span class="path1"></span><span
                                        class="path2"></span></i> ملفات المنهج
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">الحالة</label>
                        <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                            <input class="form-check-input" type="checkbox" value="1" name="status"
                                {{ $info->status == 1 ? 'checked' : '' }} />
                            <label class="form-check-label">تفعيل</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('groups.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(".date-picker").flatpickr({
            dateFormat: "Y-m-d",
        });
        $('#lfm_image').on('click', function() {
            openMetronicFileManager('image', 'thumbnail1');
        });

        $('#lfm_file').on('click', function() {
            openMetronicFileManager('file', 'thumbnail2');
        });
    </script>
@stop
