@extends('admin.layout.master')

@section('title', 'Oxford Registrations')

@section('css')
<style>
    .filter-label { font-weight: 600; color: var(--bs-gray-700); }
    /* qrcodejs renders to a canvas internally, then swaps it for an <img> (data URI) which is the actual visible element */
    #qrcode img { width: 100% !important; height: 100% !important; display: block !important; }
</style>
@stop

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">Oxford Registrations</li>
@stop

@section('page-content')
<div class="card shadow-sm mb-5">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-filter fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> فلاتر البحث
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form id="filterForm" class="row g-3">
            <div class="col-md-3">
                <label class="filter-label">Program</label>
                <select name="program_id" class="form-select form-select-solid" data-control="select2" data-placeholder="اختر البرنامج">
                    <option value=""></option>
                    @foreach($programs ?? [] as $program)
                        <option value="{{ $program->id }}">{{ $program->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Program Type <span class="text-muted">(نوع البرنامج)</span></label>
                <select name="program_type" class="form-select form-select-solid">
                    <option value="">All</option>
                    <option value="Kids">Kids (برنامج الصغار)</option>
                    <option value="Adults">Adults (برنامج الكبار)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Gender</label>
                <select name="gender" class="form-select form-select-solid">
                    <option value="">All</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Branch</label>
                <select name="branch" class="form-select form-select-solid">
                    <option value="">All</option>
                    @foreach($branches ?? [] as $branch)
                        <option value="{{ $branch->name_en }}">{{ $branch->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Status</label>
                <select name="is_invoiced" class="form-select form-select-solid">
                    <option value="">All</option>
                    <option value="1">Invoiced</option>
                    <option value="0">Not Invoiced</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Contact Status <span class="text-muted">(حالة التواصل)</span></label>
                <select name="is_contacted" class="form-select form-select-solid">
                    <option value="">All</option>
                    <option value="1">Contacted (تم التواصل)</option>
                    <option value="0">Not Contacted (لم يتم التواصل)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-solid">
            </div>
            <div class="col-md-3">
                <label class="filter-label">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-solid">
            </div>
            <div class="col-md-12 text-end mt-4">
                <button type="button" id="btn-filter" class="btn btn-primary"><i class="ki-duotone ki-magnifier fs-2"><span class="path1"></span><span class="path2"></span></i> بحث</button>
                <button type="reset" id="btn-reset" class="btn btn-light"><i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i> مسح</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-people fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> طلبات التسجيل الجديدة
            </span>
        </div>
        <div class="card-toolbar">
            <button type="button" id="btn-sms" class="btn btn-sm btn-light-primary me-2">
                <i class="fa fa-envelope me-1"></i> رسائل SMS
            </button>
            @can('admin.standalone_registrations.export')
            <button type="button" id="btn-export-excel" class="btn btn-sm btn-light-success me-2">
                <i class="ki-duotone ki-file-up fs-2"><span class="path1"></span><span class="path2"></span></i> تصدير اكسل
            </button>
            @endcan
            <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#qrCodeModal">
                <i class="ki-duotone ki-scan-barcode fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span></i> إنشاء QR Code
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center w-100" id="registrations_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0 text-center">
                        <th class="w-30px text-center"> م </th>
                        <th class="min-w-100px text-center">Name (English)</th>
                        <th class="min-w-100px text-center">الاسم (عربي)</th>
                        <th class="min-w-100px text-center">Program</th>
                        <th class="min-w-80px text-center">Phone</th>
                        <th class="min-w-80px text-center">DOB</th>
                        <th class="min-w-80px text-center">Status</th>
                        <th class="min-w-80px text-center">Contacted <span class="d-block fs-8">(تم التواصل)</span></th>
                        <th class="min-w-100px text-center">Registered At</th>
                        <th class="text-center min-w-100px pe-4"> Actions </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registration Details <span class="text-muted fs-6 ms-2">(تفاصيل التسجيل)</span></h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-2x"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body" id="detailsContent">
                <div class="text-center py-10">
                    <span class="spinner-border text-primary" role="status"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-400px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registration Form QR Code</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-2x"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted mb-5" style="font-family:'Cairo'">امسح الرمز لفتح نموذج طلبات كومبو الخارجي</p>
                <div id="qrcode-container" style="position: relative; display: inline-block;">
                    <div id="qrcode" style="width: 250px; height: 250px; overflow: hidden;"></div>
                    <img src="{{ url('assets/oxford/img/logo.png') }}" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 60px; height: 60px; background: white; padding: 5px; border-radius: 8px;">
                </div>
                <div class="mt-6">
                    <button type="button" class="btn btn-primary mb-3" onclick="downloadQRCode()">
                        <i class="ki-duotone ki-down fs-2"><span class="path1"></span><span class="path2"></span></i> Download QR Code (تحميل الرمز)
                    </button>
                    <br>
                    <a href="{{ route('registration.standalone') }}" target="_blank" class="text-primary fw-bold" style="word-break: break-all;">{{ route('registration.standalone') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <form id="paymentForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Record Payment (تسجيل دفعة) - <span id="paymentStudentName" class="text-primary"></span></h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-2x"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="student_compo_id" id="payment_student_id">
                    
                    <div class="row g-5">
                        <div class="col-md-12 mb-3">
                            <label class="required form-label">Payer Name (اسم الدافع)</label>
                            <input type="text" name="payer_name" class="form-control form-control-solid" required placeholder="ادخل اسم الشخص الذي قام بالدفع">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="required form-label">Amount (المبلغ)</label>
                            <input type="number" step="0.01" name="amount" class="form-control form-control-solid" required placeholder="0.00">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="required form-label">Currency (العملة)</label>
                            <select name="currency" class="form-select form-select-solid" required>
                                <option value="NIS">شيكل (NIS)</option>
                                <option value="USD">دولار (USD)</option>
                                <option value="JOD">دينار (JOD)</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="required form-label">Payment Method (طريقة الدفع)</label>
                            <select name="payment_method" class="form-select form-select-solid" required>
                                <option value="">اختر طريقة الدفع</option>
                                @foreach($payment_methods ?? [] as $pm)
                                    <option value="{{ $pm->name }}">{{ $pm->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Receipt Attachment (إشعار الدفع) - اختياري</label>
                            <input type="file" name="receipt" class="form-control form-control-solid" accept="image/jpeg,image/png,image/jpg,application/pdf">
                            <div class="text-muted fs-7 mt-1">Allowed formats: jpeg, png, jpg, pdf. Max size: 5MB</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-payment">
                        <span class="indicator-label">Save Payment (حفظ)</span>
                        <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#registrations_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('standalone_registrations.view') }}",
                data: function (d) {
                    d.program_id = $('select[name="program_id"]').val();
                    d.program_type = $('select[name="program_type"]').val();
                    d.gender = $('select[name="gender"]').val();
                    d.branch = $('select[name="branch"]').val();
                    d.is_invoiced = $('select[name="is_invoiced"]').val();
                    d.is_contacted = $('select[name="is_contacted"]').val();
                    d.date_from = $('input[name="date_from"]').val();
                    d.date_to = $('input[name="date_to"]').val();
                }
            },
            columns: [
                {data: 'DT_Row_Index', name: 'DT_Row_Index', orderable: false, searchable: false},
                {data: 'full_name_en', name: 'full_name_en'},
                {data: 'full_name_ar', name: 'full_name_ar'},
                {data: 'program_title', name: 'program_id'},
                {data: 'phone', name: 'phone'},
                {data: 'dob', name: 'dob'},
                {data: 'is_read', name: 'is_read'},
                {data: 'is_contacted', name: 'is_contacted', orderable: false, searchable: false},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json"
            },
            order: [[8, 'desc']]
        });

        $('#btn-filter').click(function() {
            table.draw();
        });

        $('#btn-export-excel').click(function() {
            var params = $.param({
                program_id: $('select[name="program_id"]').val(),
                program_type: $('select[name="program_type"]').val(),
                gender: $('select[name="gender"]').val(),
                branch: $('select[name="branch"]').val(),
                is_invoiced: $('select[name="is_invoiced"]').val(),
                is_contacted: $('select[name="is_contacted"]').val(),
                date_from: $('input[name="date_from"]').val(),
                date_to: $('input[name="date_to"]').val()
            });
            window.location.href = "{{ route('admin.standalone_registrations.export') }}?" + params;
        });

        $('#btn-sms').click(function() {
            if (typeof window.openComboSmsModal === 'function') {
                window.openComboSmsModal();
            }
        });

        $('#btn-reset').click(function() {
            $('#filterForm')[0].reset();
            $('select[data-control="select2"]').val(null).trigger('change');
            table.draw();
        });

        // Delegate view details button
        $('#registrations_table').on('click', '.view-details', function() {
            var id = $(this).data('id');
            $('#detailsModal').modal('show');
            $('#detailsContent').html('<div class="text-center py-10"><span class="spinner-border text-primary" role="status"></span></div>');
            
            $.get("{{ url('admin/new-registrations/show') }}/" + id, function(res) {
                if (res.success) {
                    var data = res.data;
                    var parents = res.parents;
                    
                    var html = '<div class="row mb-5">';
                    html += '<div class="col-md-6"><label class="fw-bold text-muted">Name (AR):</label><div class="fs-6">' + (data.full_name_ar || '-') + '</div></div>';
                    html += '<div class="col-md-6"><label class="fw-bold text-muted">Name (EN):</label><div class="fs-6">' + (data.full_name_en || '-') + '</div></div>';
                    html += '</div>';

                    html += '<div class="row mb-5">';
                    html += '<div class="col-md-6"><label class="fw-bold text-muted">Phone:</label><div class="fs-6">' + (data.phone || '-') + '</div></div>';
                    html += '<div class="col-md-6"><label class="fw-bold text-muted">Email:</label><div class="fs-6">' + (data.email || '-') + '</div></div>';
                    html += '</div>';

                    html += '<div class="row mb-5">';
                    html += '<div class="col-md-6"><label class="fw-bold text-muted">Date of Birth:</label><div class="fs-6">' + (data.dob || '-') + '</div></div>';
                    html += '<div class="col-md-6"><label class="fw-bold text-muted">Gender:</label><div class="fs-6">' + (data.gender || '-') + '</div></div>';
                    html += '</div>';

                    html += '<div class="row mb-5">';
                    html += '<div class="col-md-6"><label class="fw-bold text-muted">Address:</label><div class="fs-6">' + (data.address || '-') + '</div></div>';
                    html += '<div class="col-md-6"><label class="fw-bold text-muted">Branch:</label><div class="fs-6">' + (data.branch || '-') + '</div></div>';
                    html += '</div>';

                    html += '<div class="row mb-5">';
                    html += '<div class="col-md-6"><label class="fw-bold text-muted">Major/Profession:</label><div class="fs-6">' + (data.major_profession || '-') + '</div></div>';
                    html += '<div class="col-md-6"><label class="fw-bold text-muted">Program:</label><div class="fs-6">' + (data.program ? data.program.title : '-') + '</div></div>';
                    html += '</div>';
                    
                    if (data.health_issues === 'Yes') {
                        html += '<div class="row mb-5">';
                        html += '<div class="col-md-12"><label class="fw-bold text-danger">Health Issues:</label><div class="fs-6 text-danger">' + (data.health_issues_details || '-') + '</div></div>';
                        html += '</div>';
                    }

                    if (parents && parents.length > 0) {
                        html += '<div class="separator my-5"></div>';
                        html += '<h4 class="text-info mb-4">Parents Details</h4>';
                        html += '<div class="table-responsive"><table class="table table-bordered text-center">';
                        html += '<thead class="bg-light"><tr><th>Name</th><th>Kinship</th><th>Phone</th><th>ID No</th><th>Job</th></tr></thead><tbody>';
                        $.each(parents, function(i, parent) {
                            html += '<tr>';
                            html += '<td>' + (parent.full_name || '-') + '</td>';
                            html += '<td>' + (parent.kinship || '-') + '</td>';
                            html += '<td>' + (parent.phone || '-') + '</td>';
                            html += '<td>' + (parent.id_no || '-') + '</td>';
                            html += '<td>' + (parent.job || '-') + '</td>';
                            html += '</tr>';
                        });
                        html += '</tbody></table></div>';
                    }

                    $('#detailsContent').html(html);
                    
                    // Reload table if status was updated
                    table.draw(false);
                }
            });
        });

        // Delegate toggle contact switch
        $('#registrations_table').on('change', '.contact-toggle', function() {
            var id = $(this).data('id');
            var is_contacted = $(this).is(':checked') ? 1 : 0;
            
            $.post("{{ route('admin.standalone_registrations.toggleContact') }}", {
                _token: '{{ csrf_token() }}',
                id: id,
                is_contacted: is_contacted
            }, function(res) {
                if (res.success) {
                    table.draw(false);
                } else {
                    alert('Error updating status');
                }
            });
        });

        // Generate QR Code (lazily, regenerated each time the modal is shown so a blocked/late CDN load doesn't leave it empty)
        var qrcodeInstance = null;
        function generateQrCode() {
            var qrUrl = "{{ route('registration.standalone') }}";
            var container = document.getElementById('qrcode');

            if (typeof QRCode === 'undefined') {
                var script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
                script.onload = generateQrCode;
                script.onerror = function() {
                    container.innerHTML = '<span class="text-danger">تعذر تحميل مكتبة QR Code، يرجى التحقق من الاتصال بالإنترنت.</span>';
                };
                document.head.appendChild(script);
                return;
            }

            if (qrcodeInstance) {
                qrcodeInstance.clear();
                qrcodeInstance.makeCode(qrUrl);
            } else {
                container.innerHTML = '';
                qrcodeInstance = new QRCode(container, {
                    text: qrUrl,
                    width: 1000,
                    height: 1000,
                    colorDark : "#0a2540",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H // High error correction needed when placing an image on top
                });
            }
        }

        $('#qrCodeModal').on('shown.bs.modal', function() {
            generateQrCode();
        });

        // Delete Logic
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من استرجاع هذا التسجيل!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("admin/new-registrations/delete") }}/' + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire('تم الحذف!', response.message, 'success');
                                table.draw();
                            }
                        }
                    });
                }
            })
        });

        $(document).on('click', '.view-details', function() {
            var id = $(this).data('id');
            viewDetails(id);
        });

        // Record Payment Button
        $(document).on('click', '.record-payment-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#payment_student_id').val(id);
            $('#paymentStudentName').text(name);
            $('#paymentForm')[0].reset();
            $('#paymentModal').modal('show');
        });

        // Submit Payment Form
        $('#paymentForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('#btn-save-payment');
            var id = $('#payment_student_id').val();
            
            btn.attr('data-kt-indicator', 'on');
            btn.prop('disabled', true);

            var formData = new FormData(this);

            $.ajax({
                url: '{{ url("admin/new-registrations") }}/' + id + '/payment',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    btn.removeAttr('data-kt-indicator');
                    btn.prop('disabled', false);
                    
                    if(response.success) {
                        $('#paymentModal').modal('hide');
                        Swal.fire('تم الحفظ!', response.message, 'success');
                    }
                },
                error: function(xhr) {
                    btn.removeAttr('data-kt-indicator');
                    btn.prop('disabled', false);
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = 'حدث خطأ يرجى المحاولة مرة أخرى.';
                    if(errors) {
                        errorMsg = Object.values(errors).map(val => val[0]).join('<br>');
                    }
                    Swal.fire('خطأ!', errorMsg, 'error');
                }
            });
        });
    });

    function viewDetails(id) {
        $('#detailsContent').html('<div class="text-center py-10"><span class="spinner-border text-primary" role="status"></span></div>');
        $('#detailsModal').modal('show');

        $.ajax({
            url: '{{ url("admin/new-registrations/show") }}/' + id,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    var data = response.data;
                    var parents = response.parents;
                    
                    var healthDetailsHtml = data.health_issues && data.health_issues_details 
                        ? `<p class="mt-2 p-3 bg-light-danger rounded text-danger" style="border: 1px solid #f1bcbc;"><strong>تفاصيل الإعاقة/المشكلة الصحية:</strong><br>${data.health_issues_details}</p>` 
                        : '';

                    var html = `
                        <div class="row g-5">
                            <div class="col-md-6">
                                <p class="mb-2"><i class="ki-duotone ki-user text-primary fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Full Name (En):</strong> ${data.full_name_en}</p>
                                <p class="mb-2"><i class="ki-duotone ki-text-align-right text-primary fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i><strong>الاسم (عربي):</strong> ${data.full_name_ar}</p>
                                <p class="mb-2"><i class="ki-duotone ki-phone text-success fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Phone:</strong> <span dir="ltr">${data.phone}</span></p>
                                <p class="mb-2"><i class="ki-duotone ki-sms text-warning fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Email:</strong> ${data.email}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><i class="ki-duotone ki-calendar-8 text-info fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i><strong>DOB:</strong> <span dir="ltr">${data.dob}</span></p>
                                <p class="mb-2"><i class="ki-duotone ki-abstract-26 text-dark fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Gender:</strong> ${data.gender}</p>
                                <p class="mb-2"><i class="ki-duotone ki-shop text-primary fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i><strong>Branch:</strong> ${data.branch}</p>
                                <p class="mb-2"><i class="ki-duotone ki-geolocation text-danger fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Address:</strong> ${data.address}</p>
                            </div>
                            
                            <div class="col-12"><hr class="my-3"></div>

                            <div class="col-md-6">
                                <p class="mb-2"><i class="ki-duotone ki-book text-success fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i><strong>Program:</strong> ${data.program ? data.program.title : '-'}</p>
                                <p class="mb-2"><i class="ki-duotone ki-abstract-14 text-warning fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Program Type:</strong> ${data.program_type === 'kids' ? 'Kids (صغار)' : (data.program_type === 'adults' ? 'Adults (كبار)' : '-')}</p>
                                <p class="mb-2"><i class="ki-duotone ki-briefcase text-info fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Major/Profession:</strong> ${data.major_profession}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><i class="ki-duotone ki-pulse text-danger fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Health Issues:</strong> ${data.health_issues ? '<span class="text-danger fw-bold">Yes</span>' : 'No'}</p>
                                ${healthDetailsHtml}
                                <p class="mb-2 mt-2"><i class="ki-duotone ki-pencil text-primary fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Placement Test:</strong> ${data.placement_test ? '<span class="text-success fw-bold">Requested</span>' : 'No'}</p>
                                ${data.placement_test ? `<p class="mb-2"><i class="ki-duotone ki-calendar-add text-dark fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i><strong>Test Date:</strong> <span dir="ltr">${data.placement_test_date || '-'}</span></p>` : ''}
                            </div>
                        </div>
                    `;

                    if(parents && parents.length > 0) {
                        html += `
                            <div class="col-12 mt-4">
                                <h5 class="text-primary mb-3"><i class="ki-duotone ki-profile-user text-primary fs-2 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>Parent Information (بيانات ولي الأمر)</h5>
                                <div class="bg-light p-4 rounded border border-light-primary">
                                    <p class="mb-2"><strong>Name:</strong> ${parents[0].parent_name}</p>
                                    <p class="mb-2"><strong>Phone:</strong> <span dir="ltr">${parents[0].parent_phone}</span></p>
                                    <p class="mb-0"><strong>Email:</strong> ${parents[0].parent_email || '-'}</p>
                                </div>
                            </div>
                        `;
                    }

                    $('#detailsContent').html(html);
                    $('#registrations_table').DataTable().ajax.reload(null, false); // Reload to clear "New" badge if we just read it
                }
            },
            error: function() {
                $('#detailsContent').html('<div class="alert alert-danger">Error loading details.</div>');
            }
        });
    }

    function downloadQRCode() {
        var qrCanvas = document.querySelector('#qrcode canvas');
        if(!qrCanvas) {
            alert('QR Code not generated yet.');
            return;
        }
        
        var size = 1000;
        var combinedCanvas = document.createElement('canvas');
        combinedCanvas.width = size;
        combinedCanvas.height = size;
        var ctx = combinedCanvas.getContext('2d');
        
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, size, size);
        ctx.drawImage(qrCanvas, 0, 0, size, size);
        
        var logoImg = new Image();
        logoImg.onload = function() {
            var logoSize = size * 0.24;
            var logoX = (size - logoSize) / 2;
            var logoY = (size - logoSize) / 2;
            
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            if (ctx.roundRect) {
                ctx.roundRect(logoX - 10, logoY - 10, logoSize + 20, logoSize + 20, 20);
            } else {
                ctx.arc(size/2, size/2, (logoSize/2) + 10, 0, 2 * Math.PI);
            }
            ctx.fill();
            
            ctx.drawImage(logoImg, logoX, logoY, logoSize, logoSize);
            
            var a = document.createElement('a');
            a.href = combinedCanvas.toDataURL("image/png", 1.0);
            a.download = 'registration-qr-code.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        };
        logoImg.src = "{{ url('assets/oxford/img/logo.png') }}";
    }
</script>
@stop
