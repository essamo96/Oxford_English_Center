@extends('admin.layout.master')

@section('title', 'إعدادات الملف الشخصي')

@section('page-breadcrumb')
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-primary">الرئيسية</a>
    <span class="bullet bg-gray-400 w-5px h-2px mx-2"></span>
    <span class="text-muted">تحديث الملف الشخصي</span>
@endsection

@section('page-content')
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 cursor-pointer bgi-no-repeat bgi-position-center bgi-size-cover" style="background-image:url('{{ asset('assets/media/misc/menu-header-bg.jpg') }}')">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0 text-white">تفاصيل الملف الشخصي</h3>
                </div>
            </div>

            <div id="kt_account_settings_profile_details" class="collapse show">
                <form id="kt_profile_details_form" class="form" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body border-top p-9">
                        <!--begin::Input group-->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">الصورة الشخصية</label>
                            <div class="col-lg-8">
                                <!--begin::Image input-->
                                <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('{{ asset('assets/media/svg/avatars/blank.svg') }}')">
                                    <!--begin::Preview existing avatar-->
                                    <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ $user->image ? asset('assets/media/avatars/'.$user->image) : asset('assets/media/avatars/blank.png') }})"></div>
                                    <!--begin::Label-->
                                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="تغيير الصورة">
                                        <i class="ki-duotone ki-pencil fs-7"><span class="path1"></span><span class="path2"></span></i>
                                        <input type="file" name="image" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="avatar_remove" />
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Cancel-->
                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="إلغاء">
                                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                    <!--end::Cancel-->
                                    <!--begin::Remove-->
                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="حذف الصورة">
                                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                    <!--end::Remove-->
                                </div>
                                <!--end::Image input-->
                                <div class="form-text">أنواع الملفات المسموحة: png, jpg, jpeg.</div>
                            </div>
                        </div>
                        <!--end::Input group-->

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">الاسم الكامل</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="name" class="form-control form-control-lg form-control-solid" placeholder="الاسم" value="{{ $user->name }}" required />
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">اسم المستخدم (Username)</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="username" id="username_input" class="form-control form-control-lg form-control-solid" placeholder="اسم المستخدم" value="{{ $user->username }}" required />
                                <div id="username_feedback" class="form-text"></div>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">البريد الإلكتروني</label>
                            <div class="col-lg-8 fv-row">
                                <input type="email" name="email" id="email_input" class="form-control form-control-lg form-control-solid" placeholder="البريد الإلكتروني" value="{{ $user->email }}" required />
                                <div id="email_feedback" class="form-text"></div>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">رقم الجوال</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="mobile" id="mobile_input" class="form-control form-control-lg form-control-solid" placeholder="رقم الجوال" value="{{ $user->mobile }}" />
                                <div id="mobile_feedback" class="form-text"></div>
                            </div>
                        </div>

                        <!--begin::Password Input group-->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">تغيير كلمة المرور</label>
                            <div class="col-lg-8 fv-row" data-kt-password-meter="true">
                                <div class="position-relative mb-3">
                                    <input class="form-control form-control-lg form-control-solid" type="password" placeholder="اتركه فارغاً إذا لم ترد تغييره" name="password" id="password_input" autocomplete="off" />
                                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                        <i class="ki-duotone ki-eye-slash fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                        <i class="ki-duotone ki-eye d-none fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                </div>
                                <div class="text-muted">استخدم 8 أحرف أو أكثر مع مزيج من الأحرف والأرقام والرموز.</div>
                            </div>
                        </div>
                        <!--end::Input group-->

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">تأكيد كلمة المرور</label>
                            <div class="col-lg-8 fv-row">
                                <input class="form-control form-control-lg form-control-solid" type="password" placeholder="تأكيد كلمة المرور" name="password_confirmation" id="password_confirmation_input" autocomplete="off" />
                                <div id="password_match_feedback" class="form-text"></div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="button" class="btn btn-primary" id="kt_profile_submit">
                            <span class="indicator-label">حفظ التغييرات</span>
                            <span class="indicator-progress">الرجاء الانتظار... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

@endsection

@section('js')
<script>
    // Uniqueness validation function
    function checkUnique(field, value, elFeedback, elInput) {
        if (!value) {
            $(elFeedback).text('').removeClass('text-danger text-success');
            $(elInput).removeClass('is-valid is-invalid');
            return;
        }

        // don't check if value hasn't changed from original (you can add a data-original attr if you want)
        
        $.ajax({
            url: "{{ route('admin.profile.check_unique') }}",
            type: "GET",
            data: { field: field, value: value },
            success: function(res) {
                if(res.valid) {
                    $(elFeedback).text('متاح للاستخدام.').removeClass('text-danger').addClass('text-success');
                    $(elInput).removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(elFeedback).text(res.message).removeClass('text-success').addClass('text-danger');
                    $(elInput).removeClass('is-valid').addClass('is-invalid');
                }
            }
        });
    }

    $(document).ready(function() {
        
        // Debounce setup for inputs
        var timeout;
        $('#username_input').on('keyup', function() {
            clearTimeout(timeout);
            var val = $(this).val();
            var inputEl = $(this);
            timeout = setTimeout(function() { checkUnique('username', val, '#username_feedback', inputEl); }, 500);
        });

        $('#email_input').on('keyup', function() {
            clearTimeout(timeout);
            var val = $(this).val();
            var inputEl = $(this);
            timeout = setTimeout(function() { checkUnique('email', val, '#email_feedback', inputEl); }, 500);
        });

        $('#mobile_input').on('keyup', function() {
            clearTimeout(timeout);
            var val = $(this).val();
            var inputEl = $(this);
            timeout = setTimeout(function() { checkUnique('mobile', val, '#mobile_feedback', inputEl); }, 500);
        });

        // Password matching
        $('#password_input, #password_confirmation_input').on('keyup', function() {
            var p1 = $('#password_input').val();
            var p2 = $('#password_confirmation_input').val();
            if (p1 && p2) {
                if (p1 === p2) {
                    $('#password_match_feedback').text('كلمات المرور متطابقة.').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#password_match_feedback').text('كلمات المرور غير متطابقة!').removeClass('text-success').addClass('text-danger');
                }
            } else {
                $('#password_match_feedback').text('').removeClass('text-danger text-success');
            }
        });

        // Form Submit
        $('#kt_profile_submit').on('click', function(e) {
            e.preventDefault();
            
            var submitBtn = $(this);
            var form = $('#kt_profile_details_form')[0];
            var formData = new FormData(form);
            
            // Basic validation
            var p1 = $('#password_input').val();
            var p2 = $('#password_confirmation_input').val();
            if (p1 && p1 !== p2) {
                Swal.fire({
                    text: "كلمات المرور غير متطابقة!",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "حسناً",
                    customClass: { confirmButton: "btn btn-primary" }
                });
                return;
            }

            // check if there are danger texts
            var formErrors = $('#kt_profile_details_form .form-text.text-danger');
            if (formErrors.length > 0) {
                var errorsHtml = '<div class="text-start mt-4"><ul class="text-danger fs-5">';
                formErrors.each(function() {
                    var txt = $(this).text().trim();
                    if(txt !== '') {
                        errorsHtml += '<li class="mb-2">' + txt + '</li>';
                    }
                });
                errorsHtml += '</ul></div>';

                Swal.fire({
                    html: "<h4 class='text-danger'>يرجى حل الأخطاء التالية قبل الحفظ:</h4>" + errorsHtml,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "حسناً",
                    customClass: { confirmButton: "btn btn-primary" }
                });
                return;
            }

            submitBtn.attr('data-kt-indicator', 'on');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: "{{ route('admin.profile.update') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    submitBtn.removeAttr('data-kt-indicator');
                    submitBtn.prop('disabled', false);

                    if (res.status === 'success') {
                        Swal.fire({
                            text: res.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "حسناً",
                            customClass: { confirmButton: "btn btn-primary" }
                        }).then(function() {
                            // Update image in header
                            $('.menu-item .symbol img').attr('src', res.image_url);
                            $('#kt_header_user_menu_toggle img').attr('src', res.image_url);
                            $('#password_input').val('');
                            $('#password_confirmation_input').val('');
                            $('#password_match_feedback').text('');
                        });
                    }
                },
                error: function(err) {
                    submitBtn.removeAttr('data-kt-indicator');
                    submitBtn.prop('disabled', false);

                    var msg = 'حدث خطأ غير متوقع.';
                    if (err.responseJSON && err.responseJSON.message) {
                        msg = err.responseJSON.message;
                        if (err.responseJSON.errors) {
                            var errorsHtml = '<div class="text-start mt-4"><ul class="text-danger fs-5">';
                            $.each(err.responseJSON.errors, function(key, val) {
                                errorsHtml += '<li class="mb-2">' + val[0] + '</li>';
                            });
                            errorsHtml += '</ul></div>';
                            msg = errorsHtml;
                        }
                    }

                    Swal.fire({
                        html: msg,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "حسناً",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                }
            });
        });
    });
</script>
@endsection
