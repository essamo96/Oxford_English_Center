@extends('admin.layout.master')

@section('title')
    إضافة مجموعة جديدة
@stop

@section('page-title')
    إضافة مجموعة جديدة
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
    <li class="breadcrumb-item text-muted">إضافة مجموعة جديدة</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">إضافة مجموعة جديدة</span>
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

                @if(!$isBranchScoped)
                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">الفرع <span class="text-danger">*</span></label>
                        <select name="branch_id" id="branch_id" data-control="select2" data-placeholder="اختر الفرع..."
                            class="form-select form-select-solid">
                            <option></option>
                            @foreach($allBranches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name_ar }} ({{ $branch->name_en }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @else
                    <input type="hidden" name="branch_id" value="{{ auth()->guard('admin')->user()->branch_id }}">
                @endif

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">اسم المجموعة</label>
                        <input type="text" value="{{ old('name') }}" name="name" id="name"
                            class="form-control form-control-solid" placeholder="اسم المجموعة">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">اسم البرنامج</label>
                        <select name="program_id" id="program_id" data-control="select2"
                            class="form-select form-select-solid">
                            @foreach ($programs as $item)
                                <option value="{{ $item->id }}" {{ old('program_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">المدرس</label>
                        <select name="teacher_id" id="teacher_id" data-control="select2" data-placeholder="اختر المدرس..."
                            class="form-select form-select-solid">
                            <option></option>
                            @foreach ($teachers as $item)
                                <option value="{{ $item->id }}" {{ old('teacher_id') == $item->id ? 'selected' : '' }}
                                    data-kt-rich-content-subcontent="{{ $item->username ?? '' }}"
                                    data-kt-rich-content-icon="{{ $item->image ? asset($item->image) : asset('assets/media/avatars/blank.png') }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">تاريخ و وقت الحصة</label>
                        <select name="date_id" id="date_id" data-control="select2" data-placeholder="اختر الموعد..."
                            class="form-select form-select-solid">
                            <option></option>
                            @foreach ($times as $item)
                                <option value="{{ $item->id }}" {{ old('date_id') == $item->id ? 'selected' : '' }}>
                                    @if($item->days || $item->times)
                                        {{ ($item->days ?? '---') . ' :: ' . ($item->times ?? '---') }}
                                    @else
                                        موعد غير محدد (#{{ $item->id }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">رابط الزوم للجلسات</label>
                        <input type="text" value="{{ old('zoom') }}" name="zoom" id="zoom"
                            class="form-control form-control-solid" placeholder="رابط الزوم للجلسات">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">رابط ملفات جوجل درايف</label>
                        <input type="text" value="{{ old('drive') }}" name="drive" id="drive"
                            class="form-control form-control-solid" placeholder="رابط ملفات جوجل درايف">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">بدأ الحجز للمجموعة</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-solid date-picker" name="start_date"
                                value="{{ old('start_date') }}" placeholder="اختر تاريخ بدأ الحجز">
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
                                value="{{ old('end_date') }}" placeholder="اختر تاريخ نهاية الحجز">
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
                                readonly data-preview="holder" placeholder="اختر صورة المجموعة">
                            <button id="lfm_image" data-input="thumbnail1" class="btn btn-primary" type="button">
                                <i class="ki-duotone ki-picture fs-2"><span class="path1"></span><span
                                        class="path2"></span></i> تحميل صورة
                            </button>
                        </div>
                        <div id="holder" style="margin-top:15px;max-height:200px;"></div>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">الملفات التعريفية للمجموعة</label>
                        <div class="input-group">
                            <input id="thumbnail2" class="form-control form-control-solid" type="text" name="subjects"
                                readonly placeholder="اختر ملف المنهج">
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
                                {{ old('status') == 1 ? 'checked' : '' }} />
                            <label class="form-check-label">تفعيل</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('groups.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('vendor/laravel-filemanager/js/lfm.js') }}?v=2"></script>
    <script>
        $(document).ready(function() {
            // Format teacher option with image
            var formatTeacher = function(item) {
                if (!item.id) {
                    return item.text;
                }

                var icon = item.element.getAttribute('data-kt-rich-content-icon');
                var subcontent = item.element.getAttribute('data-kt-rich-content-subcontent');

                var $container = $(
                    '<div class="d-flex align-items-center">' +
                    '<div class="symbol symbol-30px me-3">' +
                    '<img src="' + icon + '" alt="" />' +
                    '</div>' +
                    '<div class="d-flex flex-column">' +
                    '<span class="fs-6 fw-bold">' + item.text + '</span>' +
                    '<span class="fs-7 text-muted">' + (subcontent || '') + '</span>' +
                    '</div>' +
                    '</div>'
                );

                return $container;
            }

            // Bind Select2 to teacher_id
            $('#teacher_id').select2({
                placeholder: "اختر المدرس...",
                allowClear: true,
                templateResult: formatTeacher,
                templateSelection: formatTeacher
            });

            $(".date-picker").flatpickr({
                dateFormat: "Y-m-d",
            });

            $('#lfm_image').on('click', function() {
                openMetronicFileManager('image', 'thumbnail1');
            });

            $('#lfm_file').on('click', function() {
                openMetronicFileManager('file', 'thumbnail2');
            });
        });
    </script>
@stop
