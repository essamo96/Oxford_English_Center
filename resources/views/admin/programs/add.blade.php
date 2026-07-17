@extends('admin.layout.master')

@section('title')
    اضافة برنامج
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
<li class="breadcrumb-item text-muted">اضافة برنامج</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">اضافة برنامج</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ URL::previous() }}" class="btn btn-sm btn-light btn-active-light-primary">
                <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        <form id="program_form" role="form" method="post" action="{{ route('programs.add') }}" class="form d-flex flex-column gap-7">
            {{ csrf_field() }}

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">العنوان</label>
                    <input type="text" value="{{ old('title') }}" name="title" id="title" class="form-control form-control-solid" placeholder="العنوان">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">نبذة قصيرة</label>
                    <input type="text" value="{{ old('short') }}" name="short" id="short" class="form-control form-control-solid" placeholder="نبذة قصيرة">
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الصورة</label>
                    <div class="input-group">
                        <input id="thumbnail" value="{{ old('image') }}" class="form-control form-control-solid" type="text" name="image" readonly placeholder="لم يتم المرفق">
                        <button id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary" type="button">
                            <i class="ki-duotone ki-picture fs-2"><span class="path1"></span><span class="path2"></span></i> حدد صورة
                        </button>
                    </div>
                    <div class="mt-3">
                        <img id="holder" style="max-height:100px;">
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">له اختبار</label>
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="exam" {{ old('exam') == 1 ? 'checked' : '' }} />
                        <label class="form-check-label">نعم</label>
                    </div>
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الحالة</label>
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="status" {{ old('status') == 1 ? 'checked' : '' }} />
                        <label class="form-check-label">تفعيل</label>
                    </div>
                </div>
                <div class="col-md-6 fv-row mt-4">
                    <label class="fs-6 fw-semibold mb-2">برنامج اختبار تحديد المستوى الافتراضي</label>
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="is_placement_test_default" {{ old('is_placement_test_default') == 1 ? 'checked' : '' }} />
                        <label class="form-check-label">يُحدَّد تلقائيًا في خطوة «تحديد المستوى» بالتسجيل (برنامج واحد فقط)</label>
                    </div>
                </div>
                <div class="col-md-6 fv-row mt-4">
                    <label class="fs-6 fw-semibold mb-2">إخفاء من فورم التسجيل (برنامج تحديد مستوى)</label>
                    <div class="form-check form-switch form-check-custom form-check-solid mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="is_placement_test" {{ old('is_placement_test') == 1 ? 'checked' : '' }} />
                        <label class="form-check-label">تفعيل (لن يظهر في قائمة البرامج في فورم التسجيل)</label>
                    </div>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">نوع البرنامج (Program Type)</label>
                    <select name="program_type" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="اختر نوع البرنامج">
                        <option value=""></option>
                        <option value="kids" {{ old('program_type') == 'kids' ? 'selected' : '' }}>برنامج الصغار (Kids)</option>
                        <option value="adults" {{ old('program_type') == 'adults' ? 'selected' : '' }}>برنامج الكبار (Adults)</option>
                    </select>
                </div>
            </div>

            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">تاريخ بدء التسجيل</label>
                    <input type="date" name="registration_start" value="{{ old('registration_start') }}" class="form-control form-control-solid">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">تاريخ انتهاء التسجيل</label>
                    <input type="date" name="registration_end" value="{{ old('registration_end') }}" class="form-control form-control-solid">
                </div>
            </div>

            {{-- قسم خصائص الدورة --}}
            <h3 class="card-title align-items-start flex-column mt-5 mb-5">
                <span class="card-label fw-bold fs-4 mb-1">خصائص الدورة</span>
            </h3>
            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الفئة العمرية (Age Range)</label>
                    <input type="text" name="age" value="{{ old('age') }}" class="form-control form-control-solid" placeholder="مثال: 16 years old and older">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">المستوى (Level)</label>
                    <input type="text" name="level" value="{{ old('level') }}" class="form-control form-control-solid" placeholder="مثال: Intermediate and above">
                </div>
            </div>
            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الأسابيع (Weeks)</label>
                    <input type="text" name="weeks" value="{{ old('weeks') }}" class="form-control form-control-solid" placeholder="مثال: 5 weeks">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الساعات (Hours)</label>
                    <input type="text" name="hours" value="{{ old('hours') }}" class="form-control form-control-solid" placeholder="مثال: 30 hours">
                </div>
            </div>
            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الامتحانات التجريبية (Mocks Exam)</label>
                    <input type="text" name="mock" value="{{ old('mock') }}" class="form-control form-control-solid" placeholder="مثال: 3 (hours 10)">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">المدة (Duration)</label>
                    <input type="text" name="duration" value="{{ old('duration') }}" class="form-control form-control-solid" placeholder="مثال: 15 sessions (2 hours each)">
                </div>
            </div>
            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">حجم الفصل (Class Size)</label>
                    <input type="text" name="class_size" value="{{ old('class_size') }}" class="form-control form-control-solid" placeholder="مثال: Average 10, Maximum 15">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">البدء (Start)</label>
                    <input type="text" name="start" value="{{ old('start') }}" class="form-control form-control-solid" placeholder="مثال: Monthly">
                </div>
            </div>
            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الأيام (Days)</label>
                    <input type="text" name="days" value="{{ old('days') }}" class="form-control form-control-solid" placeholder="مثال: Saturday-Monday-Wednesday">
                </div>
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">الوقت (Time)</label>
                    <input type="text" name="time" value="{{ old('time') }}" class="form-control form-control-solid" placeholder="مثال: 02:00 PM - 04:00 PM">
                </div>
            </div>

            {{-- قسم البروشور PDF --}}
            <div class="row g-9 mb-8">
                <div class="col-md-6 fv-row">
                    <label class="fs-6 fw-semibold mb-2">
                        <i class="bi bi-file-earmark-pdf text-danger me-1"></i> بروشور البرنامج (PDF)
                    </label>
                    <input type="file" id="brochure_input" accept=".pdf,application/pdf" class="form-control form-control-solid">
                    <div class="form-text text-muted">اختياري — يُسمح بملفات PDF فقط (حتى 100 ميجابايت). الرفع يتم في الخلفية.</div>
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

        $(document).ready(function() {
            if (typeof Resumable !== 'undefined') {
                var r = new Resumable({
                    target: '{{ route("programs.brochure.chunk") }}',
                    chunkSize: 1 * 1024 * 1024,
                    simultaneousUploads: 3,
                    testChunks: true,
                    throttleProgressCallbacks: 1,
                    query: {
                        _token: '{{ csrf_token() }}'
                    }
                });

                var brochureInput = document.getElementById('brochure_input');
                if (brochureInput) {
                    r.assignBrowse(brochureInput);
                }
                
                var uploadWidget = $('#global_upload_widget');
                var progressBar = $('#upload_progress_bar');
                var progressText = $('#upload_percentage');
                var speedText = $('#upload_speed');
                var timeText = $('#upload_time_remaining');
                
                var lastProgressTime = 0;
                var lastProgressBytes = 0;

                r.on('fileAdded', function(file){
                    $('#upload_file_name').text(file.fileName);
                });

                r.on('fileProgress', function(file){
                    uploadWidget.show();
                    var progress = Math.floor(file.progress() * 100);
                    progressBar.css('width', progress + '%');
                    progressText.text(progress + '%');

                    var now = new Date().getTime();
                    if (lastProgressTime > 0) {
                        var timeDiff = (now - lastProgressTime) / 1000;
                        var bytesDiff = r.getSize() * file.progress() - lastProgressBytes;
                        if (timeDiff > 0 && bytesDiff > 0) {
                            var speed = bytesDiff / timeDiff;
                            var speedMB = (speed / (1024 * 1024)).toFixed(2);
                            speedText.text(speedMB + ' MB/s');
                            
                            var remainingBytes = r.getSize() - (r.getSize() * file.progress());
                            var remainingSeconds = remainingBytes / speed;
                            timeText.text(Math.round(remainingSeconds) + ' ثانية متبقية');
                        }
                    }
                    lastProgressTime = now;
                    lastProgressBytes = r.getSize() * file.progress();
                });

                r.on('fileSuccess', function(file, message){
                    toastr.success('تم رفع البروشور بنجاح');
                    setTimeout(function() {
                        uploadWidget.hide();
                        progressBar.css('width', '0%');
                        progressText.text('0%');
                    }, 3000);
                    window.isUploading = false;
                });

                r.on('fileError', function(file, message){
                    toastr.error('حدث خطأ أثناء رفع البروشور');
                    window.isUploading = false;
                });

                $('#cancel_global_upload').click(function() {
                    r.cancel();
                    uploadWidget.hide();
                    window.isUploading = false;
                    toastr.info('تم إلغاء الرفع');
                });

                window.addEventListener("beforeunload", function (e) {
                    if (window.isUploading) {
                        var confirmationMessage = 'جاري رفع الملف... هل أنت متأكد من رغبتك في مغادرة الصفحة وإلغاء الرفع؟';
                        (e || window.event).returnValue = confirmationMessage;
                        return confirmationMessage;
                    }
                });
            }

            $('#program_form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitBtn = form.find('button[type="submit"]');
                var hasFile = (typeof r !== 'undefined' && r.files.length > 0);
                
                submitBtn.prop('disabled', true);
                
                var formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        submitBtn.prop('disabled', false);
                        if (response.status === 'success') {
                            toastr.success('تم حفظ بيانات البرنامج بنجاح');
                            
                            if (hasFile) {
                                r.opts.query.program_id = response.program_id;
                                window.isUploading = true;
                                r.upload();
                                
                                Swal.fire({
                                    text: "تم الحفظ بنجاح، جاري رفع البروشور في الخلفية...",
                                    icon: "success",
                                    showConfirmButton: false,
                                    timer: 2500
                                }).then(() => {
                                    // optional: redirect if wanted, but upload must stay running
                                    // window.location.href = "{{ route('programs.view') }}";
                                });
                            } else {
                                window.location.href = "{{ route('programs.view') }}";
                            }
                        } else {
                            toastr.error(response.message || 'حدث خطأ أثناء الحفظ');
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false);
                        toastr.error('خطأ في الاتصال بالخادم');
                    }
                });
            });
        });
    </script>
@stop
