@extends('admin.layout.master')

@section('title', 'إعدادات الموقع')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">إعدادات الموقع</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-setting-2 fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> إدارة إعدادات الموقع
                </span>
            </div>
        </div>
        <div class="card-body py-10">
            @include('admin.layout.error')
            <form method="post" action="" role="form" class="form" enctype="multipart/form-data">
                @csrf
                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الإسم</label>
                        <input type="text" value="{{ $info->title }}" name="title" id="title" class="form-control form-control-solid" placeholder="الإسم">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">بريد التواصل</label>
                        <input type="email" value="{{ $info->contact_email }}" name="contact_email" id="contact_email" class="form-control form-control-solid" placeholder="contact_email">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الوصف</label>
                        <textarea name="description" id="description" class="form-control form-control-solid" rows="4" style="resize: none">{{ $info->description }}</textarea>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">تعريف اضافي</label>
                        <textarea name="more_desc" id="more_desc" class="form-control form-control-solid" rows="4" style="resize: none">{{ $info->more_desc }}</textarea>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الكلمات الدلالية</label>
                        <input type="text" value="{{ $info->tags }}" name="tags" id="tags" class="form-control form-control-solid" data-role="tagsinput">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الموبيل</label>
                        <input type="text" value="{{ $info->mobile }}" name="mobile" id="mobile" class="form-control form-control-solid" placeholder="mobile">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">العنوان</label>
                        <input type="text" value="{{ $info->address }}" name="address" id="address" class="form-control form-control-solid" placeholder="address">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">ساعات التدريب</label>
                        <input type="text" value="{{ $info->donars }}" name="donars" id="donars" class="form-control form-control-solid" placeholder="donars">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-4 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">عدد الكورسات</label>
                        <input type="text" value="{{ $info->clients }}" name="clients" id="clients" class="form-control form-control-solid" placeholder="clients">
                    </div>
                    <div class="col-md-4 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">عدد الطلاب</label>
                        <input type="text" value="{{ $info->happy }}" name="happy" id="happy" class="form-control form-control-solid" placeholder="happy">
                    </div>
                    <div class="col-md-4 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">طلاب الايلتس</label>
                        <input type="text" value="{{ $info->tickects }}" name="tickects" id="tickects" class="form-control form-control-solid" placeholder="tickects">
                    </div>
                </div>

                {{-- ===== Registration & Payment Settings ===== --}}
                <div class="separator separator-dashed my-8"></div>
                <h4 class="fw-bold text-gray-700 mb-6 d-flex align-items-center gap-2">
                    <i class="ki-duotone ki-setting-3 fs-3 text-warning"><span class="path1"></span><span class="path2"></span></i>
                    إعدادات التسجيل والمدفوعات
                </h4>

                <div class="row g-9 mb-8">
                    <div class="col-md-4 fv-row">
                        <label class="fs-6 fw-semibold mb-3 d-block">حالة التسجيل</label>
                        <div class="d-flex flex-column gap-2">
                            <label class="form-check form-switch form-check-custom form-check-solid bg-light-success rounded px-4 py-3 border border-success border-dashed">
                                <input class="form-check-input h-25px w-45px" type="checkbox" name="registration_open" value="1" id="registration_open_toggle"
                                    {{ ($info->registration_open ?? 1) ? 'checked' : '' }}>
                                <span class="form-check-label fw-bold text-success fs-6 ms-3" id="reg_label">
                                    {{ ($info->registration_open ?? 1) ? 'التسجيل مفتوح' : 'التسجيل مغلق' }}
                                </span>
                            </label>
                            <small class="text-muted">عند الإغلاق، يرى الزوار رسالة مخصصة بدلاً من نموذج التسجيل</small>
                        </div>
                    </div>

                    <div class="col-md-4 fv-row">
                        <label class="fs-6 fw-semibold mb-3 d-block">إشعار الدفع</label>
                        <div class="d-flex flex-column gap-2">
                            <label class="form-check form-switch form-check-custom form-check-solid bg-light-info rounded px-4 py-3 border border-info border-dashed">
                                <input class="form-check-input h-25px w-45px" type="checkbox" name="payment_required" value="1" id="payment_required_toggle"
                                    {{ ($info->payment_required ?? 1) ? 'checked' : '' }}>
                                <span class="form-check-label fw-bold text-info fs-6 ms-3" id="pay_label">
                                    {{ ($info->payment_required ?? 1) ? 'إشعار الدفع مطلوب' : 'إشعار الدفع اختياري' }}
                                </span>
                            </label>
                            <small class="text-muted">عند التفعيل، يُجبر الطالب على رفع إيصال الدفع عند التسجيل</small>
                        </div>
                    </div>
                </div>

                <div class="row g-9 mb-8" id="closed_message_row" style="{{ ($info->registration_open ?? 1) ? 'display:none' : '' }}">
                    <div class="col-md-12 fv-row">
                        <label class="fs-6 fw-semibold mb-2">رسالة إغلاق التسجيل <span class="text-muted">(تظهر للزوار عند إغلاق التسجيل)</span></label>
                        <textarea name="registration_closed_message" class="form-control form-control-solid" rows="3"
                            placeholder="مثال: التسجيل مغلق حالياً، سيُعاد فتحه قريباً. شكراً لتواصلكم.">{{ $info->registration_closed_message ?? '' }}</textarea>
                    </div>
                </div>

                <div class="text-center pt-15">
                    <button type="submit" class="btn btn-primary px-15">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    $('#registration_open_toggle').on('change', function() {
        if ($(this).is(':checked')) {
            $('#reg_label').text('التسجيل مفتوح').removeClass('text-danger').addClass('text-success');
            $('#closed_message_row').hide();
        } else {
            $('#reg_label').text('التسجيل مغلق').removeClass('text-success').addClass('text-danger');
            $('#closed_message_row').show();
        }
    });
    $('#payment_required_toggle').on('change', function() {
        if ($(this).is(':checked')) {
            $('#pay_label').text('إشعار الدفع مطلوب').removeClass('text-warning').addClass('text-info');
        } else {
            $('#pay_label').text('إشعار الدفع اختياري').removeClass('text-info').addClass('text-warning');
        }
    });
</script>
@stop

@section('modals')

@stop
