@extends('admin.layout.master')

@section('title', 'إدارة أعياد الميلاد')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">أعياد الميلاد اليوم</li>
@stop

@section('page-content')
    @php $active_menu = 'birthdayes'; @endphp

    {{-- Filter Card --}}
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
                    {{-- Search --}}
                    <div class="col-lg-3 mb-4">
                        <label class="form-label fw-semibold">الاسم/الرقم/الإيميل</label>
                        <input type="text" name="name" id="name" class="form-control form-control-solid searchable" placeholder="البحث..." />
                    </div>

                    {{-- Status --}}
                    <div class="col-lg-2 mb-4">
                        <label class="form-label fw-semibold">الحالة (فعال)</label>
                        <select name="activeS" id="activeS" class="form-select form-select-solid searchable">
                            <option value="">الكل</option>
                            <option value="1">نشط</option>
                            <option value="0">غير نشط</option>
                        </select>
                    </div>

                    {{-- Delayed --}}
                    <div class="col-lg-2 mb-4">
                        <label class="form-label fw-semibold">مؤجلين</label>
                        <select name="delaying" id="delaying" class="form-select form-select-solid searchable">
                            <option value="">الكل</option>
                            <option value="1">نعم</option>
                            <option value="0">لا</option>
                        </select>
                    </div>

                    {{-- Groups --}}
                    <div class="col-lg-3 mb-4">
                        <label class="form-label fw-semibold">حسب المجموعة</label>
                        <select name="group_id" id="group_id" class="form-select form-select-solid searchable" data-control="select2" data-placeholder="اختر المجموعة">
                            <option value="">كل المجموعات</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-lg-2 mb-4 d-flex align-items-end gap-2">
                        <button type="button" class="btn btn-warning btn-icon w-40px h-40px shadow-sm" id="sms" title="إرسال SMS">
                            <i class="bi bi-chat-dots fs-3"></i>
                        </button>
                        <button type="button" class="btn btn-info btn-icon w-40px h-40px shadow-sm CEmail" title="إرسال بريد">
                            <i class="bi bi-envelope fs-3"></i>
                        </button>
                        <button type="reset" class="btn btn-light-danger btn-icon w-40px h-40px shadow-sm" id="reset_filters" title="إعادة تعيين">
                            <i class="bi bi-arrow-clockwise fs-3"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table Card --}}
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-gift fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> قائمة أعياد الميلاد
                </span>
            </div>
            <div class="card-toolbar gap-2">
                <a href="{{ URL::previous() }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.error')
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center" id="birthday_table">
                    <thead>
                        <tr class="text-center text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">#</th>
                            <th class="w-10px">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="selectAll" />
                                </div>
                            </th>
                            <th class="min-w-150px">الإسم</th>
                            <th>الجوال</th>
                            <th>الايميل</th>
                            <th class="min-w-150px">المجموعة</th>
                            <th>تاريخ الميلاد</th>
                            <th class="min-w-100px">العمليات</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold"></tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var oTable = $('#birthday_table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "{{ route('birthdayes.list') }}",
                    data: function(d) {
                        d.title = $('#name').val();
                        d.activeS = $('#activeS').val();
                        d.delaying = $('#delaying').val();
                        d.group_id = $('#group_id').val();
                    }
                },
                "columns": [
                    { data: 'id', name: 'id', render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
                    { data: "checkbox", name: "checkbox", orderable: false, searchable: false },
                    { data: "name", name: "name" },
                    { data: "mobile", name: "mobile" },
                    { data: "email", name: "email" },
                    { data: "group_names", name: "group_names", orderable: false },
                    { data: "dob", name: "dob" },
                    { data: "actions", name: "actions", orderable: false, searchable: false }
                ],
                "order": [[2, 'asc']],
                "language": {
                    "emptyTable": "لا يوجد بيانات متطابقة",
                    "info": "عرض _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                    "infoEmpty": "عرض 0 إلى 0 من أصل 0 مدخل",
                    "loadingRecords": "جارِ التحميل...",
                    "processing": "جارِ المعالجة...",
                    "search": "بحث:",
                    "zeroRecords": "لم يتم العثور على سجلات مطابقة"
                }
            });

            // Trigger search on input change
            $('.searchable').on('change keyup', function() {
                oTable.draw();
            });

            // Reset filters
            $('#reset_filters').on('click', function(e) {
                e.preventDefault();
                $('.form')[0].reset();
                $('.searchable').trigger('change');
            });

            // Select all checkbox
            $('#selectAll').on('change', function() {
                $('.checkboxes').prop('checked', $(this).is(':checked'));
            });

            // Email Handlers (using EmailComposer)
            $(document).on('click', '.Reply', function() {
                var id = $(this).data('id');
                window.EmailComposer.send({
                    type: 'single',
                    id: id,
                    url: '{{ route("send.message") }}',
                    title: '🎂 Celebration Update: Happy Birthday!',
                    defaultSubject: '🎉 Wishing you a wonderful Birthday!',
                    defaultBody: 'Dear Student,\n\nThe Oxford Family wishes you a very happy birthday filled with joy and success! 🎂'
                });
            });

            $(document).on('click', '.CEmail', function() {
                var emails = window.EmailComposer.collectEmails();
                if (emails.length === 0) {
                    Swal.fire({ text: 'Please select at least one student!', icon: 'warning', buttonsStyling: false, confirmButtonText: 'OK', customClass: { confirmButton: 'btn btn-primary' } });
                    return;
                }
                window.EmailComposer.send({
                    type: 'bulk',
                    recipients: emails,
                    url: '{{ route("send.CEmail.Birthday") }}',
                    title: '🎂 Send Birthday Greetings',
                    defaultSubject: '🎉 Happy Birthday from Oxford Family!',
                    defaultBody: 'Dear Student,\n\nWe are delighted to celebrate this special day with you. Happy Birthday! 🎂'
                });
            });
        });
    </script>
@stop
