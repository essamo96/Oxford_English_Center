@extends('admin.layout.master')

@section('title', 'اختبارات تحديد المستوى')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">اختبارات تحديد المستوى</li>
@stop

@section('page-content')
@php $active_menu = 'placement_tests'; @endphp

<div class="card shadow-sm mb-8">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-filter fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> فلاتر البحث
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-5">
            <div class="col-md-3">
                <label class="form-label fw-bold text-gray-700">البحث (الاسم، الجوال، الإيميل)</label>
                <input type="text" id="search_text" class="form-control" placeholder="ادخل اسم الطالب أو الجوال...">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold text-gray-700">تاريخ الاختبار</label>
                <input type="date" id="test_date" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold text-gray-700">الوقت (المجدول)</label>
                <input type="text" id="test_time" class="form-control" placeholder="مثال: 10:00 AM">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold text-gray-700">نوع البرنامج</label>
                <select id="program_type" class="form-select">
                    <option value="">الكل</option>
                    <option value="adult">الكبار (Adult)</option>
                    <option value="kids">الأطفال (Kids)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold text-gray-700">الجنس</label>
                <select id="gender" class="form-select">
                    <option value="">الكل</option>
                    <option value="male">ذكر (Male)</option>
                    <option value="female">أنثى (Female)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold text-gray-700">
                    <i class="bi bi-stopwatch text-warning me-1"></i> الفئة العمرية
                </label>
                <select id="age_group" class="form-select">
                    <option value="">الكل</option>
                    <option value="kids">أطفال (≤ 15 سنة)</option>
                    <option value="adult">كبار (&gt; 15 سنة)</option>
                </select>
            </div>
            @if(!($isBranchScoped ?? false))
            <div class="col-md-2">
                <label class="form-label fw-bold text-gray-700">الفرع</label>
                <select id="branch_id_filter" class="form-select" data-control="select2">
                    <option value="">كل الفروع</option>
                    @foreach($allBranches ?? [] as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-light-danger w-100" onclick="resetFilters()" title="مسح الفلاتر">
                    <i class="ki-duotone ki-trash fs-4"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                إدارة اختبارات تحديد المستوى
            </span>
        </div>
        <div class="card-toolbar gap-2">
            <button type="button" class="btn btn-primary btn-sm" id="btn-send-batch-email">
                <i class="bi bi-envelope-plus me-1"></i> إرسال إيميل للمحددين
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="placement_tests_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#placement_tests_table .form-check-input" value="1" />
                            </div>
                        </th>
                        <th class="min-w-150px"> الطالب </th>
                        <th class="min-w-100px"> تاريخ الاختبار </th>
                        <th class="min-w-100px"> الوقت </th>
                        <th class="min-w-100px"> الحالة </th>
                        <th class="min-w-100px"> طريقة الدفع </th>
                        <th class="min-w-120px"> إيصال الدفع </th>
                        <th class="min-w-80px"> العلامة </th>
                        <th class="text-center min-w-150px pe-4"> العمليات </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Score Modal -->
<div class="modal fade" id="scoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">رصد نتيجة اختبار المستوى للطالب: <span id="student-name-modal" class="text-info"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="scoreForm">
                @csrf
                <input type="hidden" id="test-id-modal">
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">العلامة (Score) *</label>
                        <input type="text" name="score" class="form-control" required placeholder="مثال: 85/100">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">المستوى المقترح (Assigned Level) *</label>
                        <select name="assigned_level" class="form-control" required>
                            <option value="">اختر المستوى</option>
                            @php $levels = ['A0', 'A1', 'A2', 'A2+', 'B1', 'B1+', 'B2', 'C1']; @endphp
                            @foreach($levels as $lvl)
                            <option value="{{ $lvl }}">{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" id="save-score-btn">حفظ النتيجة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إرسال إيميل تأكيد الموعد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="emailForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center p-5 mb-5">
                        <i class="ki-duotone ki-notification-on fs-2hx text-info me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-info">تنبيه الموعد</h4>
                            <span>سيتم إرسال الإيميل للطلاب المحددين في الجدول. يمكنك تعديل التاريخ والوقت إذا لزم الأمر.</span>
                        </div>
                    </div>
                    
                    <div class="row g-5 mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">تاريخ الامتحان</label>
                            <input type="date" name="test_date" id="modal_test_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">وقت الامتحان</label>
                            <input type="text" name="test_time" id="modal_test_time" class="form-control" placeholder="مثال: 10:00 AM">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold">موضوع الإيميل</label>
                        <input type="text" name="subject" class="form-control" value="تأكيد موعد اختبار تحديد المستوى - مركز أكسفورد">
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold">رسالة الإيميل</label>
                        <textarea name="message" class="form-control" rows="6">نود إعلامكم بأنه تم تأكيد حجزكم لاختبار تحديد المستوى بنجاح. 

يسعدنا تأكيد الموعد الخاص بكم كما هو موضح أدناه. يرجى التواجد في مقر المركز قبل الموعد بـ 15 دقيقة.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-info" id="send-email-btn">إرسال الآن</button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@section('modal')
    <!-- Modern Details Modal -->
    <div class="modal fade" id="details_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-800px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body scroll-y pt-0 pb-15" id="modal_content">
                    <div class="text-center py-10">
                        <span class="spinner-border w-50px h-50px" role="status"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Global functions for student details
    window.showStudentModal = function(id) {
        fetchModalContent('{{ route('students.details') }}', { id: id });
    }

    function fetchModalContent(url, data) {
        $('#modal_content').html('<div class="text-center py-10"><span class="spinner-border w-50px h-50px" role="status"></span></div>');
        $('#details_modal').modal('show');
        $.ajax({
            url: url,
            type: 'POST',
            data: { ...data, _token: '{{ csrf_token() }}' },
            success: function(response) {
                $('#modal_content').html(response);
            },
            error: function() {
                $('#modal_content').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل البيانات</div>');
            }
        });
    }
    var table;
    var tableId = 'placement_tests_table';
    var filterFields = ['#search_text', '#test_date', '#test_time', '#program_type', '#gender', '#age_group', '#branch_id_filter'];
    
    var columns = [
        { 
            data: "id", 
            orderable: false, 
            searchable: false, 
            render: function(data, type, row) { 
                return `<div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="${data}" />
                        </div>`;
            } 
        },
        { data: "student.name", name: "students.name", orderable: true },
        { data: "test_date", name: "test_date", orderable: true },
        { data: "test_time", name: "test_time", orderable: true },
        { data: "status", name: "status", orderable: true },
        { data: "payment_method.name", name: "payment_method.name", defaultContent: "N/A" },
        { data: "payment_receipt", name: "payment_receipt", orderable: false, searchable: false },
        { data: "score", name: "score", defaultContent: "-" },
        { data: "action", name: "action", orderable: false, searchable: false }
    ];

    function resetFilters() {
        $(filterFields.join(',')).val('');
        table.draw();
    }

    $(document).ready(function() {
        $(document).on('click', '.confirm-payment-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'تأكيد الدفع؟',
                text: "سيتم تغيير حالة الاختبار إلى 'دفع مؤكد'",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، أكد الدفع',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("admin/placement_tests/confirm-payment") }}/' + id,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                Swal.fire('تم!', response.message, 'success');
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.score-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#test-id-modal').val(id);
            $('#student-name-modal').text(name);
            $('#scoreModal').modal('show');
        });

        $('#scoreForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#test-id-modal').val();
            var btn = $('#save-score-btn');
            btn.attr('disabled', true).text('جاري الحفظ...');

            $.ajax({
                url: '{{ url("admin/placement_tests/score") }}/' + id,
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#scoreModal').modal('hide');
                    if (response.success) {
                        table.ajax.reload();
                        Swal.fire('تم!', response.message, 'success');
                    }
                    btn.attr('disabled', false).text('حفظ النتيجة');
                },
                error: function() {
                    btn.attr('disabled', false).text('حفظ النتيجة');
                    Swal.fire('خطأ!', 'حدث خطأ أثناء حفظ البيانات', 'error');
                }
            });
        });

        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع عن هذا!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("placement_tests.delete") }}',
                        type: 'POST',
                        data: { id: id, _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                Swal.fire('تم الحذف!', 'تم حذف السجل بنجاح.', 'success');
                            }
                        }
                    });
                }
            });
        });

        // Batch Email Logic
        $('#btn-send-batch-email').on('click', function() {
            var selected = [];
            $('#placement_tests_table tbody input[type="checkbox"]:checked').each(function() {
                selected.push($(this).val());
            });

            if (selected.length === 0) {
                Swal.fire('تنبيه', 'يرجى تحديد طلاب أولاً من الجدول', 'warning');
                return;
            }

            $('#emailModal').modal('show');
        });

        $('#emailForm').on('submit', function(e) {
            e.preventDefault();
            var selected = [];
            $('#placement_tests_table tbody input[type="checkbox"]:checked').each(function() {
                selected.push($(this).val());
            });

            var btn = $('#send-email-btn');
            btn.attr('disabled', true).text('جاري الإرسال...');

            var formData = $(this).serializeArray();
            selected.forEach(id => formData.push({ name: 'test_ids[]', value: id }));

            $.ajax({
                url: '{{ route("placement_tests.send_email") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#emailModal').modal('hide');
                    if (response.success) {
                        Swal.fire('تم!', response.message, 'success');
                        table.ajax.reload();
                    }
                    btn.attr('disabled', false).text('إرسال الآن');
                },
                error: function() {
                    btn.attr('disabled', false).text('إرسال الآن');
                    Swal.fire('خطأ!', 'فشل إرسال الإيميلات', 'error');
                }
            });
        });
    });
</script>
@include('admin.layout.masterLayouts.datatableMaster', ['active_menu' => 'placement_tests'])
@stop
