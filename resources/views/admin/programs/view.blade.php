@extends('admin.layout.master')

@section('title', 'إدارة البرامج')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة البرامج</li>
@stop

@section('page-content')
@php $active_menu = 'programs'; @endphp

<div class="card mb-7 shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1">
                <i class="ki-duotone ki-magnifier fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> البحث والفلاتر
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form role="form" class="form">
            <div class="row gx-5">
                <div class="col-lg-3 col-md-4 mb-4">
                    <label class="form-label fw-semibold">اسم البرنامج</label>
                    <input type="text" name="title" id="title" class="form-control form-control-solid searchable" placeholder="ابحث باسم البرنامج...">
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <label class="form-label fw-semibold">اسم المجموعة</label>
                    <input type="text" name="group_name" id="group_name" class="form-control form-control-solid searchable" placeholder="ابحث باسم المجموعة...">
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <label class="form-label fw-semibold">الحالة</label>
                    <select name="status" id="status" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="اختر الحالة">
                        <option value="all">الكل</option>
                        <option value="1">مفعل</option>
                        <option value="0">غير مفعل</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-4 mb-4 d-flex align-items-end gap-2">
                    <button type="button" onclick="table.ajax.reload();" class="btn btn-primary btn-icon w-40px h-40px shadow-sm" title="بحث">
                        <i class="bi bi-search fs-3"></i>
                    </button>
                    <button type="reset" id="reset_button" class="btn btn-light-danger btn-icon w-40px h-40px shadow-sm" title="إعادة تعيين البحث">
                        <i class="bi bi-arrow-clockwise fs-3"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-book-open fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> إدارة البرامج الدراسية
            </span>
        </div>
        <div class="card-toolbar gap-2">
            @can('admin.programs.add')
                <a href="{{ route('programs.add') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> إضافة برنامج 
                </a>
            @endcan
            <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center text-nowrap" id="programs_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-6 text-uppercase gs-0">
                        <th class="w-50px text-center"> # </th>
                        <th class="min-w-200px text-center"> اسم البرنامج </th>
                        <th class="min-w-150px text-center"> المجموعات </th>
                        <th class="min-w-160px text-center"> الحد الأدنى للدفع </th>
                        <th class="min-w-100px text-center"> الحالة </th>
                        <th class="min-w-100px text-center"> إخفاء (تحديد مستوى) </th>
                        <th class="text-center min-w-150px"> العمليات </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center"></tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('modal')
    @include('admin.layout.masterLayouts.modal')
    
    <div class="modal bg-body fade" tabindex="-1" id="program_details_modal">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content shadow-none">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fs-4 fw-bold text-primary" id="modal_program_title">تفاصيل البرنامج</h5>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body bg-light">
                    <div id="program_details_content">
                        <!-- Content will be loaded here -->
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-light-primary fw-bold" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    {{-- QR Code Modal for Program Brochure --}}
    <div class="modal fade" tabindex="-1" id="brochure_qr_modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fs-4 fw-bold text-success">
                        <i class="bi bi-qr-code me-2"></i>
                        <span id="qr_modal_title">QR Code البروشور</span>
                    </h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body text-center">
                    {{-- QR Code SVG will be injected here --}}
                    <div id="qr_code_container" class="d-inline-block p-4 bg-white rounded shadow-sm mb-4 position-relative" style="min-width: 280px; min-height: 280px;">
                        <div class="d-flex justify-content-center align-items-center" style="height: 260px;">
                            <span class="spinner-border text-success"></span>
                        </div>
                    </div>

                    {{-- Brochure status badge --}}
                    <div id="qr_brochure_status" class="mb-3"></div>

                    {{-- URL display + copy --}}
                    <div class="input-group mb-4">
                        <input type="text" id="qr_brochure_url" class="form-control form-control-solid text-center fs-7" readonly>
                        <button class="btn btn-light-primary" type="button" onclick="copyBrochureUrl()" title="نسخ الرابط">
                            <i class="bi bi-clipboard me-1"></i> نسخ
                        </button>
                    </div>

                    {{-- Action buttons --}}
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-success" onclick="downloadQr()">
                            <i class="bi bi-download me-1"></i> تحميل PNG
                        </button>
                        <button type="button" class="btn btn-primary" onclick="printQr()">
                            <i class="bi bi-printer me-1"></i> طباعة
                        </button>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-sm btn-light fw-bold" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    var table;
    var tableId = 'programs_table';
    var customResponsive = false;
    var columns = [
        { data: "id", name: "id", orderable: false, searchable: false, render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: "title", name: "title", orderable: true, className: "text-start" },
        { data: "grope_no", name: "grope_no", className: "text-center" },
        { data: "min_payment", name: "min_payment", orderable: false, searchable: false, className: "text-center" },
        { data: "status", name: "status", orderable: true, searchable: false },
        { data: "is_placement_test", name: "is_placement_test", orderable: true, searchable: false, className: "text-center" },
        { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
    ];

    var filterFields = ['#title', '#status', '#group_name'];

    $(document).ready(function() {
        $(document).on('click', '#reset_button', function(e) {
            e.preventDefault();
            $(this).closest('form')[0].reset();
            table.ajax.reload();
        });

        // Add handler for placement-status toggle
        $(document).on('change', '.placement-status', function(e) {
            e.preventDefault();
            var id = $(this).data('href');
            var isChecked = $(this).is(':checked');
            var url = "{{ route('programs.placement_status') }}";
            
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    id: id,
                    status: isChecked ? 1 : 0,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status == 'success') {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                        table.ajax.reload(null, false); // Reload to revert UI state
                    }
                },
                error: function() {
                    toastr.error('حدث خطأ أثناء الاتصال بالخادم');
                    table.ajax.reload(null, false);
                }
            });
        });

        $(document).on('click', '.view-program-details', function(e) {
            var id = $(this).data('id');
            var url = "{{ route('programs.details', ':id') }}".replace(':id', id);
            
            // Show SweetAlert Loading 
            Swal.fire({
                title: 'جاري التحميل...',
                text: 'يرجى الانتظار أثناء جلب بيانات المجموعات',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.status == 'success') {
                        // Close loading alert
                        Swal.close();
                        
                        // Populate and open modal
                        $('#modal_program_title').text('مجموعات برنامج: ' + response.title);
                        $('#program_details_content').html(response.html);
                        $('#program_details_modal').modal('show');
                    } else if (response.status == 'empty') {
                        // Show info alert directly
                        Swal.fire({
                            text: response.message,
                            icon: "info",
                            buttonsStyling: false,
                            confirmButtonText: "موافق",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    } else {
                        // Show error alert
                        Swal.fire({
                            title: 'خطأ',
                            text: response.message || 'حدث خطأ أثناء تحميل البيانات',
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "موافق",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                },
                error: function() {
                    $('#program_details_modal').modal('hide');
                    Swal.fire({
                        text: "حدث خطأ أثناء تحميل البيانات",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "موافق",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
        });

        // Add handler for placement-status toggle
        $(document).on('change', '.placement-status', function(e) {
            e.preventDefault();
            var id = $(this).data('href');
            var isChecked = $(this).is(':checked');
            var url = "{{ route('programs.placement_status') }}";

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    is_placement_test: isChecked ? 1 : 0
                },
                success: function(response) {
                    if (response.status == 'success') {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                        table.ajax.reload(null, false);
                    }
                },
                error: function() {
                    toastr.error('حدث خطأ أثناء تغيير الحالة');
                    table.ajax.reload(null, false);
                }
            });
        });
    });

    // =================== QR Code Brochure Functions ===================
    var _currentQrProgramTitle = '';

    function showBrochureQr(encryptedId) {
        // Reset modal content
        $('#qr_code_container').html('<div class="d-flex justify-content-center align-items-center" style="height: 260px;"><span class="spinner-border text-success"></span></div>');
        $('#qr_brochure_url').val('');
        $('#qr_brochure_status').html('');
        $('#qr_modal_title').text('QR Code البروشور');

        // Show modal immediately with loading state
        $('#brochure_qr_modal').modal('show');

        var url = "{{ route('programs.brochure.qr', ':id') }}".replace(':id', encryptedId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.status == 'success') {
                    _currentQrProgramTitle = response.title;
                    $('#qr_modal_title').text('QR Code — ' + response.title);
                    
                    // Add SVG and Logo Overlay
                    var logoOverlay = '<img src="{{ asset('assets/oxford/img/logo.png') }}" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 55px; height: 55px; background: #fff; padding: 5px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">';
                    $('#qr_code_container').html(response.qr_svg + logoOverlay);
                    
                    $('#qr_brochure_url').val(response.url);

                    // Make SVG responsive
                    $('#qr_code_container svg').css({ width: '260px', height: '260px' });

                    if (response.has_brochure) {
                        $('#qr_brochure_status').html('<span class="badge badge-light-success fs-7 p-3"><i class="bi bi-check-circle me-1"></i> بروشور مرفوع — الرابط جاهز للمسح</span>');
                    } else {
                        $('#qr_brochure_status').html('<span class="badge badge-light-warning fs-7 p-3"><i class="bi bi-exclamation-triangle me-1"></i> لم يتم رفع بروشور بعد — يمكنك رفعه من صفحة تعديل البرنامج</span>');
                    }
                } else {
                    $('#brochure_qr_modal').modal('hide');
                    Swal.fire({
                        text: response.message || 'حدث خطأ',
                        icon: 'error',
                        confirmButtonText: 'موافق',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                }
            },
            error: function() {
                $('#brochure_qr_modal').modal('hide');
                Swal.fire({
                    text: 'حدث خطأ أثناء توليد QR Code',
                    icon: 'error',
                    confirmButtonText: 'موافق',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            }
        });
    }

    function copyBrochureUrl() {
        var urlInput = document.getElementById('qr_brochure_url');
        urlInput.select();
        urlInput.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(urlInput.value).then(function() {
            toastr.success('تم نسخ الرابط بنجاح!');
        }).catch(function() {
            document.execCommand('copy');
            toastr.success('تم نسخ الرابط!');
        });
    }

    function downloadQr() {
        var svgEl = document.querySelector('#qr_code_container svg');
        if (!svgEl) { toastr.error('لا يوجد QR Code لتحميله'); return; }

        var svgData = new XMLSerializer().serializeToString(svgEl);
        var canvas = document.createElement('canvas');
        canvas.width = 300;
        canvas.height = 350; // extra space for text at the bottom
        var ctx = canvas.getContext('2d');

        // White background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, 300, 350);

        var img = new Image();
        img.onload = function() {
            ctx.drawImage(img, 0, 0, 300, 300);

            var logoImg = new Image();
            logoImg.onload = function() {
                // Draw rounded white background for logo
                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                if (ctx.roundRect) {
                    ctx.roundRect(120, 120, 60, 60, 10);
                } else {
                    ctx.arc(150, 150, 30, 0, 2 * Math.PI);
                }
                ctx.fill();
                
                // Draw Oxford Logo
                ctx.drawImage(logoImg, 125, 125, 50, 50);

                // Add program title text at bottom
                ctx.fillStyle = '#333333';
                ctx.font = 'bold 14px Cairo, Arial, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText(_currentQrProgramTitle, 150, 330);

                var link = document.createElement('a');
                link.download = 'QR_' + (_currentQrProgramTitle || 'brochure') + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            };
            logoImg.src = "{{ asset('assets/oxford/img/logo.png') }}";
        };
        img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
    }

    function printQr() {
        var svgEl = document.querySelector('#qr_code_container svg');
        if (!svgEl) { toastr.error('لا يوجد QR Code للطباعة'); return; }

        var svgData = new XMLSerializer().serializeToString(svgEl);
        var logoSrc = "{{ asset('assets/oxford/img/logo.png') }}";
        
        var printWindow = window.open('', '_blank', 'width=500,height=600');
        printWindow.document.write('<html dir="rtl"><head><title>QR Code — ' + _currentQrProgramTitle + '</title>');
        printWindow.document.write('<style>body{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;margin:0;font-family:Cairo,Arial,sans-serif;}h2{margin-top:20px;color:#333;font-size:22px;}.url{color:#888;font-size:12px;margin-top:10px;word-break:break-all;max-width:400px;text-align:center;}.qr-wrap{position:relative;display:inline-block;padding:20px;background:#fff;}.qr-wrap img.logo{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:60px;height:60px;background:#fff;padding:5px;border-radius:10px;}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<div class="qr-wrap">');
        printWindow.document.write(svgData);
        printWindow.document.write('<img src="'+logoSrc+'" class="logo" />');
        printWindow.document.write('</div>');
        printWindow.document.write('<h2>' + _currentQrProgramTitle + '</h2>');
        printWindow.document.write('<p class="url">' + document.getElementById('qr_brochure_url').value + '</p>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.onload = function() {
            printWindow.print();
        };
    }
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'programs'])
@stop
