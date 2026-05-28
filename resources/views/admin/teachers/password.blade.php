@extends('admin.layout.master')

@section('title', 'تغيير كلمة المرور')

@section('page-title')
    تغيير كلمة المرور
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('teachers.view') }}" class="text-muted text-hover-info">إدارة المدرسين</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">تغيير كلمة المرور</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">تغيير كلمة المرور لـ {{ $info->name }}</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('teachers.view') }}" class="btn btn-sm btn-light btn-active-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.masterLayouts.error')
            <form role="form" method="post" action="" class="form d-flex flex-column gap-7">
                {{ csrf_field() }}

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">إسم المستخدم</label>
                        <input type="text" value="{{ old('username', $info->username ?? '') }}" name="username" id="username" class="form-control form-control-solid" placeholder="اسم المستخدم">
                        <div id="username-feedback" class="form-text mt-1 small"></div>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2 required">كلمة المرور الجديدة</label>
                        <input type="password" name="password" id="password" class="form-control form-control-solid" placeholder="كلمة المرور الجديدة">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2 required">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-solid" placeholder="تأكيد كلمة المرور">
                        <div id="password-feedback" class="form-text mt-1 small"></div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('teachers.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
            <script>
                (function(){
                    const usernameEl = document.getElementById('username');
                    const usernameFb = document.getElementById('username-feedback');
                    const pass = document.getElementById('password');
                    const pass2 = document.getElementById('password_confirmation');
                    const passFb = document.getElementById('password-feedback');
                    const form = document.querySelector('#update_password') || document.querySelector('form[role="form"]');
                    const teacherId = '{{ isset($info->id) ? Crypt::encrypt($info->id) : "" }}';

                    // debounce helper
                    function debounce(fn, wait){ let t; return function(){ clearTimeout(t); const args = arguments; t = setTimeout(() => fn.apply(this, args), wait); }; }

                    function checkUsername(){
                        const val = usernameEl.value.trim();
                        if (!val) { usernameFb.innerText = ''; usernameEl.classList.remove('is-invalid','is-valid'); return; }
                        fetch('/admin/teachers/check-username?username='+encodeURIComponent(val)+'&exclude='+encodeURIComponent(teacherId))
                            .then(r => r.json())
                            .then(data => {
                                if (data.exists) {
                                    usernameFb.innerText = 'اسم المستخدم مستخدم بالفعل.';
                                    usernameFb.classList.remove('text-success'); usernameFb.classList.add('text-danger');
                                    usernameEl.classList.add('is-invalid'); usernameEl.classList.remove('is-valid');
                                } else {
                                    usernameFb.innerText = 'اسم المستخدم متاح.';
                                    usernameFb.classList.remove('text-danger'); usernameFb.classList.add('text-success');
                                    usernameEl.classList.add('is-valid'); usernameEl.classList.remove('is-invalid');
                                }
                            }).catch(()=>{});
                    }

                    const debouncedCheck = debounce(checkUsername, 450);
                    usernameEl && usernameEl.addEventListener('input', debouncedCheck);

                    function checkPasswords(){
                        const a = pass.value || '';
                        const b = pass2.value || '';
                        if (!a && !b) { passFb.innerText = ''; pass.classList.remove('is-valid','is-invalid'); pass2.classList.remove('is-valid','is-invalid'); return; }
                        if (a.length < 6) {
                            passFb.innerText = 'يجب أن تكون كلمة المرور 6 أحرف على الأقل.'; passFb.classList.remove('text-success'); passFb.classList.add('text-danger');
                            pass.classList.add('is-invalid'); pass2.classList.remove('is-invalid'); return;
                        }
                        if (a && b && a === b) {
                            passFb.innerText = 'كلمة المرور متطابقة.'; passFb.classList.remove('text-danger'); passFb.classList.add('text-success');
                            pass.classList.add('is-valid'); pass2.classList.add('is-valid'); pass.classList.remove('is-invalid'); pass2.classList.remove('is-invalid');
                        } else if (b) {
                            passFb.innerText = 'كلمة المرور وتأكيدها غير متطابقين.'; passFb.classList.remove('text-success'); passFb.classList.add('text-danger');
                            pass.classList.add('is-invalid'); pass2.classList.add('is-invalid');
                        }
                    }
                    pass && pass.addEventListener('input', checkPasswords);
                    pass2 && pass2.addEventListener('input', checkPasswords);

                    // prevent submit if invalid
                    form && form.addEventListener('submit', function(e){
                        // run checks immediately
                        checkPasswords();
                        // sync username check (if pending, assume checked via class)
                        if (usernameEl && usernameEl.classList.contains('is-invalid')) { e.preventDefault(); usernameEl.focus(); }
                        if ((pass.classList && pass.classList.contains('is-invalid')) || (pass2.classList && pass2.classList.contains('is-invalid'))) { e.preventDefault(); pass.focus(); }
                    });
                })();
            </script>
        </div>
    </div>
@stop
