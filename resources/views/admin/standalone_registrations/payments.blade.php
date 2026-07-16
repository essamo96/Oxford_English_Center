@extends('admin.layout.master')

@section('title', 'Student Payments (Combo)')

@section('css')
<style>
    .filter-label { font-weight: 600; color: var(--bs-gray-700); }
    td.details-control {
        background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
    }
</style>
@stop

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">
    <a href="{{ route('standalone_registrations.view') }}" class="text-muted text-hover-info">Oxford Registrations</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">Payments Tracker</li>
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
            <div class="col-md-4">
                <label class="filter-label">Program (البرنامج)</label>
                <select name="program_id" class="form-select form-select-solid" data-control="select2" data-placeholder="اختر البرنامج">
                    <option value=""></option>
                    @foreach($programs ?? [] as $program)
                        <option value="{{ $program->id }}">{{ $program->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="filter-label">Payment Date From (تاريخ الدفع من)</label>
                <input type="date" name="date_from" class="form-control form-control-solid">
            </div>
            <div class="col-md-4">
                <label class="filter-label">Payment Date To (تاريخ الدفع إلى)</label>
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
            <span class="card-label fw-bold fs-3 mb-1 text-success">
                <i class="ki-duotone ki-wallet fs-3 text-success me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> 
                Student Payments (متابعة دفعات الطلاب)
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center w-100" id="payments_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0 text-center">
                        <th class="w-30px text-center"> التفاصيل </th>
                        <th class="min-w-100px text-center">Student Name (EN)</th>
                        <th class="min-w-100px text-center">الاسم (عربي)</th>
                        <th class="min-w-100px text-center">Program</th>
                        <th class="min-w-100px text-center">Phone</th>
                        <th class="min-w-80px text-center">عدد الدفعات</th>
                        <th class="min-w-150px text-center">إجمالي الدفعات</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center">
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        var table = $('#payments_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('standalone_registrations.payments.view') }}",
                data: function (d) {
                    d.program_id = $('select[name="program_id"]').val();
                    d.date_from = $('input[name="date_from"]').val();
                    d.date_to = $('input[name="date_to"]').val();
                }
            },
            columns: [
                {
                    class: 'details-control',
                    orderable: false,
                    data: null,
                    defaultContent: ''
                },
                {data: 'full_name_en', name: 'full_name_en'},
                {data: 'full_name_ar', name: 'full_name_ar'},
                {data: 'program_title', name: 'program_id'},
                {data: 'phone', name: 'phone'},
                {data: 'payments_count', name: 'payments_count', searchable: false, orderable: false},
                {data: 'total_payments', name: 'total_payments', searchable: false, orderable: false},
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json"
            },
            order: [[1, 'asc']]
        });

        $('#btn-filter').click(function() {
            table.draw();
        });

        $('#btn-reset').click(function() {
            $('#filterForm')[0].reset();
            $('select[data-control="select2"]').val(null).trigger('change');
            table.draw();
        });

        // Add event listener for opening and closing details
        $('#payments_table tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                var rowData = row.data();
                
                // Show a loading placeholder
                row.child('<div class="text-center p-4"><span class="spinner-border text-primary" role="status"></span></div>').show();
                tr.addClass('shown');
                
                // Fetch details from server
                $.get(rowData.details_url, function(res) {
                    row.child(res).show();
                }).fail(function() {
                    row.child('<div class="alert alert-danger mb-0">حدث خطأ أثناء تحميل الدفعات.</div>').show();
                });
            }
        });
    });
</script>
@stop
