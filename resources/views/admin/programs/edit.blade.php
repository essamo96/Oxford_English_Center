@extends('admin.layout.master')

@section('title')
تعديل البرامج
@stop

@section('css')

@stop

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">
    <a href="{{ route('programs.view') }}" class="text-muted text-hover-info">إدارة البرامج</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">تعديل برنامج</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">تعديل برنامج: {{ $info->title }}</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ URL::previous() }}" class="btn btn-sm btn-light btn-active-light-primary">
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
                    <label class="fs-6 fw-semibold mb-2">العنوان</label>
                    <input type="text" value="{{ $info->title }}" name="title" id="title" class="form-control form-control-solid" placeholder="العنوان">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">نبذة قصيرة</label>
                    <input type="text" value="{{ $info->short }}" name="short" id="short" class="form-control form-control-solid" placeholder="نبذة قصيرة">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الصورة</label>
                    <div class="input-group">
                        <input id="thumbnail" value="{{ $info->image }}" class="form-control form-control-solid" type="text" name="image" readonly placeholder="لم يتم المرفق">
                        <button id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary" type="button">
                            <i class="ki-duotone ki-picture fs-2"><span class="path1"></span><span class="path2"></span></i> حدد صورة
                        </button>
                    </div>
                    <div class="mt-3">
                        <img id="holder" src="{{ url($info->image) }}" style="max-height:100px; {{ empty($info->image) ? 'display:none;' : '' }}">
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">له اختبار</label>
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="exam" {{ $info->exam == 1 ? 'checked' : '' }} />
                        <label class="form-check-label">نعم</label>
                    </div>
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الحالة</label>
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="status" {{ $info->status == 1 ? 'checked' : '' }} />
                        <label class="form-check-label">تفعيل</label>
                    </div>
                </div>
                <div class="col-md-6 fv-row mt-4">
                    <label class="fs-6 fw-semibold mb-2">برنامج اختبار تحديد المستوى الافتراضي</label>
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="is_placement_test_default" {{ ($info->is_placement_test_default ?? 0) == 1 ? 'checked' : '' }} />
                        <label class="form-check-label">يُحدَّد تلقائيًا في خطوة «تحديد المستوى» بالتسجيل (برنامج واحد فقط)</label>
                    </div>
                </div>
                <div class="col-md-6 fv-row mt-4">
                    <label class="fs-6 fw-semibold mb-2">إخفاء من فورم التسجيل (برنامج تحديد مستوى)</label>
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="is_placement_test" {{ ($info->is_placement_test ?? 0) == 1 ? 'checked' : '' }} />
                        <label class="form-check-label">تفعيل (لن يظهر في قائمة البرامج في فورم التسجيل)</label>
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">نوع البرنامج (Program Type)</label>
                    <select name="program_type" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="اختر نوع البرنامج">
                        <option value=""></option>
                        <option value="kids" {{ ($info->program_type ?? '') == 'kids' ? 'selected' : '' }}>برنامج الصغار (Kids)</option>
                        <option value="adults" {{ ($info->program_type ?? '') == 'adults' ? 'selected' : '' }}>برنامج الكبار (Adults)</option>
                    </select>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">تاريخ بدء التسجيل</label>
                    <input type="date" name="registration_start" value="{{ $info->registration_start }}" class="form-control form-control-solid">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">تاريخ انتهاء التسجيل</label>
                    <input type="date" name="registration_end" value="{{ $info->registration_end }}" class="form-control form-control-solid">
                </div>
            </div>

            {{-- قسم البروشور PDF --}}
            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">
                        <i class="bi bi-file-earmark-pdf text-danger me-1"></i> بروشور البرنامج (PDF)
                    </label>
                    <input type="file" name="brochure" accept=".pdf,application/pdf" class="form-control form-control-solid">
                    <div class="form-text text-muted">اختياري — يُسمح بملفات PDF فقط (حتى 100 ميجابايت)</div>
                </div>
                <div class="col-md-6 fv-row">
                    @if(!empty($info->brochure_path))
                        <label class="fs-6 fw-semibold mb-2">البروشور الحالي</label>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <a href="{{ $info->brochure_url }}" target="_blank" class="btn btn-sm btn-light-primary">
                                <i class="bi bi-eye me-1"></i> عرض
                            </a>
                            <a href="{{ $info->brochure_url }}" download class="btn btn-sm btn-light-success">
                                <i class="bi bi-download me-1"></i> تحميل
                            </a>
                            <button type="button" class="btn btn-sm btn-light-danger" onclick="deleteBrochure('{{ Crypt::encrypt($info->id) }}')">
                                <i class="bi bi-trash me-1"></i> حذف البروشور
                            </button>
                        </div>
                    @else
                        <label class="fs-6 fw-semibold mb-2">&nbsp;</label>
                        <div class="mt-2">
                            <span class="badge badge-light-warning fs-7 p-3">
                                <i class="bi bi-info-circle me-1"></i> لم يتم رفع بروشور بعد
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('programs.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                <button type="submit" class="btn btn-primary">حفظ</button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
    <script>
        $('#lfm').on('click', function() {
            openMetronicFileManager('image', 'thumbnail');
        });

        function deleteBrochure(encryptedId) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم حذف ملف البروشور نهائياً',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-light'
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('programs.brochure.delete') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: encryptedId
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                Swal.fire({
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'موافق',
                                    customClass: { confirmButton: 'btn btn-primary' }
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'موافق',
                                    customClass: { confirmButton: 'btn btn-primary' }
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                text: 'حدث خطأ أثناء حذف البروشور',
                                icon: 'error',
                                confirmButtonText: 'موافق',
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        }
                    });
                }
            });
        }
    </script>
@stop