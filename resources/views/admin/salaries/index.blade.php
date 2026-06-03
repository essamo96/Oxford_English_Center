@extends('admin.layout.master')

@section('title', 'رواتب المعلمين')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">رواتب المعلمين</li>
@stop

@section('page-content')
@php
    $active_menu = 'teacher_salaries';
    $monthsAr = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
    $statusMap = [
        'draft'  => ['مسودة', 'badge-light-secondary'],
        'open'   => ['مفتوح', 'badge-light-warning'],
        'closed' => ['مغلق', 'badge-light-success'],
    ];
    $grandNet = array_sum(array_column($rows, 'net_amount'));
    $grandLec = array_sum(array_column($rows, 'lectures_count'));
@endphp

<div class="card shadow-sm mb-5">
    <div class="card-header border-0 pt-6">
        <div class="card-title"><h3 class="fw-bold"><i class="bi bi-cash-coin text-success me-2"></i>رواتب المعلمين (حسب المحاضرات)</h3></div>
        <div class="card-toolbar">
            {{-- Period selector --}}
            <form method="get" class="d-flex gap-2 align-items-center flex-wrap">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" style="width:170px;" placeholder="🔍 بحث باسم المعلم">
                <select name="month" class="form-select form-select-sm" style="width:130px;">
                    @foreach($monthsAr as $m => $label)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="year" class="form-select form-select-sm" style="width:110px;">
                    @for($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <select name="group_id" class="form-select form-select-sm" style="width:170px;">
                    <option value="0">كل المجموعات</option>
                    @foreach($groups_for_filter ?? [] as $gid => $gname)
                        <option value="{{ $gid }}" {{ ($group_id ?? 0) == $gid ? 'selected' : '' }}>{{ $gname }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-light-primary"><i class="bi bi-funnel"></i> عرض</button>
            </form>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.masterLayouts.error')
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        {{-- Summary + actions --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div class="d-flex gap-3 flex-wrap">
                <span class="badge badge-light-primary fs-7 p-3">الفترة: {{ $monthsAr[$month] }} {{ $year }}</span>
                <span class="badge badge-light-info fs-7 p-3">إجمالي المحاضرات: {{ $grandLec }}</span>
                <span class="badge badge-light-success fs-7 p-3">إجمالي الصافي: {{ number_format($grandNet, 2) }} ILS</span>
                @if($is_closed)<span class="badge badge-light-danger fs-7 p-3"><i class="bi bi-lock-fill me-1"></i> الشهر مغلق</span>@endif
            </div>
            <div class="d-flex gap-2">
                @if(!$is_closed)
                    <form method="post" action="{{ route('admin.teacher_salaries.generate') }}">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}"><input type="hidden" name="month" value="{{ $month }}">
                        <button class="btn btn-sm btn-light-info"><i class="bi bi-arrow-repeat me-1"></i> احتساب/تحديث الاستمارات</button>
                    </form>
                    <button type="button" class="btn btn-sm btn-danger" id="btnCloseMonth"><i class="bi bi-lock-fill me-1"></i> إغلاق رواتب الشهر</button>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-4 text-center">
                <thead>
                    <tr class="text-muted fw-bold fs-7 text-uppercase">
                        <th>المدرس</th>
                        <th>المجموعات</th>
                        <th>عدد المحاضرات</th>
                        <th>أجر المحاضرة</th>
                        <th>الإجمالي</th>
                        <th>علاوة</th>
                        <th>خصم</th>
                        <th>الصافي</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-700">
                    @forelse($rows as $r)
                        @php $st = $statusMap[$r['status']] ?? [$r['status'],'badge-light-secondary']; @endphp
                        <tr>
                            <td class="text-start fw-bold">{{ $r['teacher_name'] }}</td>
                            <td class="text-start">
                                @forelse($r['groups'] as $g)
                                    <span class="badge badge-light-info fs-9 mb-1"><i class="bi bi-people-fill me-1"></i>{{ $g['name'] }} ({{ $g['lectures'] }})</span>
                                @empty
                                    <span class="text-muted fs-8">—</span>
                                @endforelse
                            </td>
                            <td><span class="badge badge-light-primary fw-bold">{{ $r['lectures_count'] }}</span></td>
                            <td>{{ number_format($r['lecture_rate'], 2) }}</td>
                            <td class="fw-bold">{{ number_format($r['gross_amount'], 2) }}</td>
                            <td class="text-success">{{ number_format($r['bonus'], 2) }}</td>
                            <td class="text-danger">{{ number_format($r['deduction'], 2) }}</td>
                            <td class="fw-bold text-dark fs-6">{{ number_format($r['net_amount'], 2) }}</td>
                            <td><span class="badge {{ $st[1] }}">{{ $st[0] }}</span></td>
                            <td>
                                <button class="btn btn-sm btn-icon btn-light-info" title="تفاصيل الراتب"
                                        onclick="showSalaryDetails({{ $r['teacher_id'] }}, '{{ $r['teacher_name'] }}')">
                                    <i class="bi bi-list-ul"></i>
                                </button>
                                @if(!$is_closed)
                                    <button class="btn btn-sm btn-icon btn-light-warning" title="علاوة/خصم"
                                            onclick='editSalaryForm(@json($r))'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-muted py-6">لا توجد محاضرات مسجّلة لهذه الفترة.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Close logs --}}
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title"><h3 class="fw-bold"><i class="bi bi-journal-text text-warning me-2"></i>سجل إغلاق الرواتب</h3></div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-7 gy-3 text-center">
                <thead><tr class="text-muted fw-bold text-uppercase">
                    <th>الفترة</th><th>عدد المدرسين</th><th>إجمالي المحاضرات</th><th>إجمالي المبلغ</th><th>أغلقها</th><th>التاريخ</th><th>ملاحظات</th>
                </tr></thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="fw-bold">{{ $monthsAr[$log->month] ?? $log->month }} {{ $log->year }}</td>
                            <td>{{ $log->teachers_count }}</td>
                            <td>{{ $log->total_lectures }}</td>
                            <td class="fw-bold text-success">{{ number_format($log->total_amount, 2) }} ILS</td>
                            <td>{{ optional($log->closedBy)->name ?? '—' }}</td>
                            <td>{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="text-muted">{{ $log->notes ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted py-5">لا يوجد سجل إغلاق بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Details modal --}}
<div class="modal fade" id="salaryDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">تفاصيل الراتب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body" id="salaryDetailsBody">
                <div class="text-center py-10"><span class="spinner-border text-primary"></span></div>
            </div>
        </div>
    </div>
</div>

{{-- Edit bonus/deduction modal --}}
<div class="modal fade" id="editSalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">علاوة / خصم — <span id="esTeacherName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="editSalaryForm">
                @csrf
                <input type="hidden" name="teacher_id" id="es_teacher_id">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label fw-bold text-success">علاوة (Bonus)</label>
                        <input type="number" step="0.01" min="0" name="bonus" id="es_bonus" class="form-control"></div>
                    <div class="mb-3"><label class="form-label fw-bold text-danger">خصم (Deduction)</label>
                        <input type="number" step="0.01" min="0" name="deduction" id="es_deduction" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">ملاحظات</label>
                        <textarea name="notes" id="es_notes" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    var SAL_YEAR = {{ $year }}, SAL_MONTH = {{ $month }};

    window.showSalaryDetails = function (teacherId, name) {
        $('#salaryDetailsBody').html('<div class="text-center py-10"><span class="spinner-border text-primary"></span></div>');
        $('#salaryDetailsModal').modal('show');
        $.get('{{ url("admin/teacher-salaries/details") }}/' + teacherId, { year: SAL_YEAR, month: SAL_MONTH }, function (html) {
            $('#salaryDetailsBody').html(html);
        }).fail(function () { $('#salaryDetailsBody').html('<div class="alert alert-danger m-4">تعذّر تحميل التفاصيل</div>'); });
    };

    window.editSalaryForm = function (row) {
        $('#es_teacher_id').val(row.teacher_id);
        $('#esTeacherName').text(row.teacher_name);
        $('#es_bonus').val(row.bonus);
        $('#es_deduction').val(row.deduction);
        $('#es_notes').val('');
        $('#editSalaryModal').modal('show');
    };

    $('#editSalaryForm').on('submit', function (e) {
        e.preventDefault();
        $.post('{{ route("admin.teacher_salaries.update_form") }}', $(this).serialize(), function (res) {
            $('#editSalaryModal').modal('hide');
            Swal.fire('تم', res.message, 'success').then(() => location.reload());
        }).fail(function (xhr) {
            Swal.fire('خطأ', (xhr.responseJSON && xhr.responseJSON.message) || 'تعذّر الحفظ', 'error');
        });
    });

    $('#btnCloseMonth').on('click', function () {
        Swal.fire({
            title: 'إغلاق رواتب الشهر؟',
            html: 'سيتم تجميد كل الاستمارات لهذه الفترة ولا يمكن تعديلها بعد الإغلاق.',
            input: 'textarea', inputPlaceholder: 'ملاحظات (اختياري)',
            icon: 'warning', showCancelButton: true, confirmButtonText: 'نعم، أغلق', cancelButtonText: 'إلغاء', confirmButtonColor: '#d33',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            var $f = $('<form method="post" action="{{ route("admin.teacher_salaries.close") }}">');
            $f.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
            $f.append('<input type="hidden" name="year" value="' + SAL_YEAR + '">');
            $f.append('<input type="hidden" name="month" value="' + SAL_MONTH + '">');
            $f.append($('<input type="hidden" name="notes">').val(result.value || ''));
            $f.appendTo('body').submit();
        });
    });
</script>
@stop
