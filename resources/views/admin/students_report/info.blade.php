@php
    $totalPaid     = $fees->sum('student_fee_paid');
    $totalRequired = $groups->sum('student_fee_total');
    $balance       = $totalPaid - $totalRequired;
    $absenceCount  = $absences->count();
    $lateCount     = $absences->where('is_late', 1)->count();
    $passedGroups  = $groups->filter(fn($g) => $g->total_degree >= 50)->count();
    $uniqueId      = 'srp_' . $info->id;
@endphp

{{-- ══════════════════════════════════════════════════════
     PROFILE HEADER
══════════════════════════════════════════════════════ --}}
<div class="px-7 pt-7 pb-5">

    {{-- Avatar + Name row --}}
    <div class="d-flex flex-wrap flex-sm-nowrap gap-5 mb-5 align-items-start">

        {{-- Avatar --}}
        <div class="symbol symbol-90px symbol-lg-110px flex-shrink-0 position-relative">
            <img src="{{ $info->image ? asset($info->image) : url('assets/oxford/img/students/avatar.png') }}"
                 alt="{{ $info->name }}"
                 class="symbol-label object-fit-cover rounded-3">
            <span class="position-absolute bottom-0 end-0 translate-middle-y me-n1 rounded-circle border border-3 border-body {{ $info->status == 1 ? 'bg-success' : 'bg-danger' }}"
                  style="width:14px;height:14px;">
            </span>
        </div>

        {{-- Name + meta --}}
        <div class="flex-grow-1 min-w-0">

            {{-- Name row --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h3 class="fs-3 fw-bolder mb-0">{{ $info->name }}</h3>
                    <span class="badge {{ $info->status == 1 ? 'badge-light-success' : 'badge-light-danger' }} fs-8">
                        {{ $info->status == 1 ? 'Active' : 'Inactive' }}
                    </span>
                    @if($info->delaying)
                        <span class="badge badge-light-warning fs-8">Delayed</span>
                    @endif
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <a href="{{ route('students.edit', Crypt::encrypt($info->id)) }}"
                       class="btn btn-sm btn-light-primary">
                        <i class="ki-duotone ki-pencil fs-5"><span class="path1"></span><span class="path2"></span></i>
                        Edit
                    </a>
                    <a href="{{ route('students.view') }}?search={{ $info->mobile }}"
                       target="_blank" class="btn btn-sm btn-light">
                        <i class="ki-duotone ki-exit-right-corner fs-5"><span class="path1"></span><span class="path2"></span></i>
                        Profile
                    </a>
                </div>
            </div>

            {{-- Contact meta --}}
            <div class="d-flex flex-wrap gap-4 fs-7 text-muted fw-semibold mb-4">
                @if($info->mobile)
                <a href="tel:{{ $info->mobile }}" class="d-flex align-items-center gap-1 text-muted text-hover-primary">
                    <i class="ki-duotone ki-phone fs-5"><span class="path1"></span><span class="path2"></span></i>
                    {{ $info->mobile }}
                </a>
                @endif
                @if($info->email)
                <a href="mailto:{{ $info->email }}" class="d-flex align-items-center gap-1 text-muted text-hover-primary">
                    <i class="ki-duotone ki-sms fs-5"><span class="path1"></span><span class="path2"></span></i>
                    {{ $info->email }}
                </a>
                @endif
                @if($info->gender)
                <span class="d-flex align-items-center gap-1">
                    <i class="ki-duotone ki-profile-user fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    {{ in_array($info->gender, ['male','1']) ? 'Male' : 'Female' }}
                </span>
                @endif
                @if($info->join_date)
                <span class="d-flex align-items-center gap-1">
                    <i class="ki-duotone ki-calendar fs-5"><span class="path1"></span><span class="path2"></span></i>
                    Joined {{ \Carbon\Carbon::parse($info->join_date)->format('d M Y') }}
                </span>
                @endif
            </div>

            {{-- Quick stats --}}
            <div class="d-flex flex-wrap gap-3">
                <div class="bg-light-primary rounded-2 px-4 py-2 text-center">
                    <div class="fs-3 fw-bolder text-primary">{{ $groups->count() }}</div>
                    <div class="fs-9 text-muted fw-semibold">Courses</div>
                </div>
                <div class="bg-light-success rounded-2 px-4 py-2 text-center">
                    <div class="fs-3 fw-bolder text-success">{{ $passedGroups }}</div>
                    <div class="fs-9 text-muted fw-semibold">Passed</div>
                </div>
                <div class="{{ $balance < 0 ? 'bg-light-danger' : 'bg-light-success' }} rounded-2 px-4 py-2 text-center">
                    <div class="fs-3 fw-bolder {{ $balance < 0 ? 'text-danger' : 'text-success' }}">{{ number_format(abs($balance), 0) }}</div>
                    <div class="fs-9 text-muted fw-semibold">{{ $balance < 0 ? 'Due' : 'Credit' }}</div>
                </div>
                <div class="{{ $absenceCount > 5 ? 'bg-light-danger' : 'bg-light-warning' }} rounded-2 px-4 py-2 text-center">
                    <div class="fs-3 fw-bolder {{ $absenceCount > 5 ? 'text-danger' : 'text-warning' }}">{{ $absenceCount }}</div>
                    <div class="fs-9 text-muted fw-semibold">Absences</div>
                </div>
                <div class="bg-light-info rounded-2 px-4 py-2 text-center">
                    <div class="fs-3 fw-bolder text-info">{{ $certificates->count() }}</div>
                    <div class="fs-9 text-muted fw-semibold">Certificates</div>
                </div>
                @if($info->exam_degree)
                <div class="bg-light-warning rounded-2 px-4 py-2 text-center">
                    <div class="fs-3 fw-bolder text-warning">{{ $info->exam_degree }}%</div>
                    <div class="fs-9 text-muted fw-semibold">Placement</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($info->note)
    <div class="notice d-flex bg-light-warning rounded-2 border-warning border border-dashed p-4">
        <i class="ki-duotone ki-information-5 fs-2tx text-warning me-3 flex-shrink-0">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <div>
            <h5 class="fw-bold mb-1">Internal Notes</h5>
            <div class="fs-7 text-muted">{{ $info->note }}</div>
        </div>
    </div>
    @endif

</div>

{{-- ══════════════════════════════════════════════════════
     TABS — scrollable on mobile
══════════════════════════════════════════════════════ --}}
<div class="px-7">
    <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x fs-7 fw-semibold flex-nowrap overflow-auto border-0 mb-0"
        id="{{ $uniqueId }}_tabs"
        style="scrollbar-width:none;">
        <li class="nav-item flex-shrink-0">
            <a class="nav-link active pb-4 px-3" data-bs-toggle="tab" href="#{{ $uniqueId }}_courses">
                <i class="ki-duotone ki-book fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <span class="d-none d-sm-inline">Courses &amp; Grades</span>
                <span class="d-sm-none">Courses</span>
                <span class="badge badge-light-primary ms-1 fs-9">{{ $groups->count() }}</span>
            </a>
        </li>
        <li class="nav-item flex-shrink-0">
            <a class="nav-link pb-4 px-3" data-bs-toggle="tab" href="#{{ $uniqueId }}_finance">
                <i class="ki-duotone ki-wallet fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                Financial
                @if($balance < 0)<span class="badge badge-light-danger ms-1 fs-9">Due</span>@endif
            </a>
        </li>
        <li class="nav-item flex-shrink-0">
            <a class="nav-link pb-4 px-3" data-bs-toggle="tab" href="#{{ $uniqueId }}_attendance">
                <i class="ki-duotone ki-calendar-2 fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                Attendance
                @if($absenceCount > 0)
                <span class="badge {{ $absenceCount > 5 ? 'badge-light-danger' : 'badge-light-warning' }} ms-1 fs-9">{{ $absenceCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item flex-shrink-0">
            <a class="nav-link pb-4 px-3" data-bs-toggle="tab" href="#{{ $uniqueId }}_certs">
                <i class="ki-duotone ki-medal-star fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                Certificates
                <span class="badge badge-light-info ms-1 fs-9">{{ $certificates->count() }}</span>
            </a>
        </li>
        <li class="nav-item flex-shrink-0">
            <a class="nav-link pb-4 px-3" data-bs-toggle="tab" href="#{{ $uniqueId }}_personal">
                <i class="ki-duotone ki-profile-circle fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <span class="d-none d-sm-inline">Personal Info</span>
                <span class="d-sm-none">Info</span>
            </a>
        </li>
    </ul>
</div>

{{-- ══════════════════════════════════════════════════════
     TAB CONTENT
══════════════════════════════════════════════════════ --}}
<div class="tab-content px-7 pt-6 pb-7" id="{{ $uniqueId }}_content">

    {{-- ══ TAB 1 — COURSES & GRADES ══ --}}
    <div class="tab-pane fade show active" id="{{ $uniqueId }}_courses">
        @if($groups->isEmpty())
            <div class="text-center py-10">
                <i class="ki-duotone ki-book fs-3x text-muted d-block mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <div class="fs-5 fw-bold text-muted">No courses enrolled yet</div>
            </div>
        @else
        <div class="row g-5">
            @foreach($groups as $gs)
            @php
                $grp     = $gs->group;
                $teacher = optional($grp)->teacher;
                $program = optional($grp)->program;
                $ctime   = optional($grp)->ctime;
                $total   = $gs->total_degree;
                $passed  = $total !== null && $total >= 50;
                $active  = optional($grp)->status == 1;
                $days    = optional($ctime)->days ?? '';
                $times   = optional($ctime)->times ?? '';
                $grpImg  = optional($grp)->image ? asset($grp->image) : null;
                $tchImg  = optional($teacher)->image ? asset($teacher->image) : null;
                $scoreCls   = $total !== null ? ($passed ? 'success' : 'danger') : 'warning';
                $scoreLabel = $total !== null ? ($passed ? 'PASS' : 'FAIL') : 'ONGOING';
            @endphp
            <div class="col-12 col-md-6">
                <div class="card card-flush h-100 border border-top-0 border-start-0 border-end-0 border-{{ $scoreCls }} border-3">

                    {{-- Cover band — always dark overlay so white text is safe --}}
                    <div class="position-relative overflow-hidden rounded-top" style="height:120px;">
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-75"></div>
                        @if($grpImg)
                        <img src="{{ $grpImg }}" alt="{{ optional($grp)->name }}"
                             class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover opacity-50">
                        @else
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-primary opacity-50"></div>
                        @endif
                        {{-- gradient fade to bottom --}}
                        <div class="position-absolute bottom-0 start-0 w-100 h-75 bg-gradient-to-top opacity-75"
                             style="background:linear-gradient(to top,rgba(0,0,0,.75),transparent);"></div>

                        {{-- Status chip —— colours are rgba so always visible on the dark band --}}
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge text-white fw-bold fs-9 {{ $active ? 'bg-success' : 'bg-danger' }} bg-opacity-90">
                                <span class="bullet bullet-dot bg-white me-1"></span>
                                {{ $active ? 'Active' : 'Ended' }}
                            </span>
                        </div>

                        {{-- Score circle --}}
                        <div class="position-absolute bottom-0 end-0 mb-3 me-3">
                            <div class="d-flex flex-column align-items-center justify-content-center rounded-circle border border-3 border-white bg-{{ $scoreCls }} bg-opacity-90"
                                 style="width:52px;height:52px;">
                                @if($total !== null)
                                <span class="text-white fw-bolder lh-1 fs-8">{{ $total }}%</span>
                                <span class="text-white fw-bold" style="font-size:.55rem;">{{ $scoreLabel }}</span>
                                @else
                                <i class="ki-duotone ki-time fs-5 text-white"><span class="path1"></span><span class="path2"></span></i>
                                <span class="text-white fw-bold" style="font-size:.5rem;">ONGOING</span>
                                @endif
                            </div>
                        </div>

                        {{-- Group name --}}
                        <div class="position-absolute bottom-0 start-0 ps-4 pb-3 pe-14">
                            @if($program)
                            <span class="badge mb-1 text-white fw-bold fs-9 bg-info bg-opacity-75">
                                {{ $program->name ?? $program->title ?? '' }}
                            </span>
                            @endif
                            <h5 class="text-white fw-bolder mb-0 fs-7 text-truncate">
                                {{ optional($grp)->name ?? 'Unknown Group' }}
                            </h5>
                        </div>
                    </div>

                    {{-- Card body --}}
                    <div class="card-body p-5">

                        {{-- Teacher + schedule --}}
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="symbol symbol-35px symbol-circle">
                                    @if($tchImg)
                                    <img src="{{ $tchImg }}" alt="{{ optional($teacher)->name }}" class="symbol-label object-fit-cover">
                                    @else
                                    <span class="symbol-label bg-light-info">
                                        <i class="ki-duotone ki-teacher fs-4 text-info"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                    @endif
                                </div>
                                <div>
                                    <div class="fs-9 text-muted fw-semibold">Teacher</div>
                                    <div class="fs-7 fw-bolder">{{ optional($teacher)->name ?? '—' }}</div>
                                </div>
                            </div>

                            @if($days || $times)
                            <div class="d-flex flex-column align-items-end gap-1">
                                @if($days)
                                <div class="d-flex align-items-center gap-1 fs-8 text-muted fw-semibold">
                                    <i class="ki-duotone ki-calendar-tick fs-5 text-primary">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                        <span class="path4"></span><span class="path5"></span><span class="path6"></span>
                                    </i>
                                    {{ $days }}
                                </div>
                                @endif
                                @if($times)
                                <div class="d-flex align-items-center gap-1 fs-8 text-muted fw-semibold">
                                    <i class="ki-duotone ki-time fs-5 text-info"><span class="path1"></span><span class="path2"></span></i>
                                    {{ $times }}
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>

                        {{-- Date range --}}
                        @if(optional($grp)->start_date)
                        <div class="d-flex align-items-center gap-2 bg-light rounded-2 px-3 py-2 mb-4 fs-8 text-muted fw-semibold">
                            <i class="ki-duotone ki-calendar-2 fs-5 text-muted"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            {{ \Carbon\Carbon::parse($grp->start_date)->format('d M Y') }}
                            @if(optional($grp)->end_date)
                            <span class="text-muted opacity-50 mx-1">→</span>
                            {{ \Carbon\Carbon::parse($grp->end_date)->format('d M Y') }}
                            @endif
                        </div>
                        @endif

                        {{-- Progress --}}
                        @if($total !== null)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between fs-8 text-muted fw-semibold mb-2">
                                <span>Overall Score</span>
                                <span class="text-{{ $scoreCls }} fw-bolder">{{ $total }}%</span>
                            </div>
                            <div class="progress h-6px rounded-pill">
                                <div class="progress-bar bg-{{ $scoreCls }} rounded-pill"
                                     style="width:{{ min($total,100) }}%;transition:width .8s ease;">
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Grade chips --}}
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach([
                                ['T1',$gs->exam1_degree],['T2',$gs->exam2_degree],['T3',$gs->exam3_degree],
                                ['FN',$gs->exam4_degree],['Act',$gs->activity_degree],['WB',$gs->workbook_degree]
                            ] as [$lbl,$val])
                            @php
                                $gc = $val !== null
                                    ? ($val >= 50 ? 'bg-light-success text-success border-success' : 'bg-light-danger text-danger border-danger')
                                    : 'bg-light text-muted border-secondary opacity-50';
                            @endphp
                            <div class="d-flex flex-column align-items-center border {{ $gc }} rounded-2 px-2 py-2"
                                 style="min-width:46px;">
                                <span class="fw-bolder fs-7 lh-1 mb-1">{{ $val ?? '—' }}</span>
                                <span class="fs-9 fw-semibold text-uppercase opacity-75">{{ $lbl }}</span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Certificate --}}
                        @if($gs->cer_code)
                        <div class="notice d-flex align-items-center bg-light-success border border-dashed border-success rounded-2 p-3 mt-1">
                            <i class="ki-duotone ki-medal-star fs-3 text-success me-2">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                            </i>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fs-9 text-success fw-bold">Certificate Issued</div>
                                <code class="fs-8 fw-bold">{{ $gs->cer_code }}</code>
                            </div>
                            <a href="{{ url('certificates') }}" target="_blank" class="btn btn-sm btn-light-success ms-2">
                                <i class="ki-duotone ki-eye fs-6"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                Verify
                            </a>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ══ TAB 2 — FINANCIAL ══ --}}
    <div class="tab-pane fade" id="{{ $uniqueId }}_finance">

        <div class="row g-4 mb-6">
            <div class="col-6 col-sm-3">
                <div class="bg-light-primary rounded-2 p-4 text-center">
                    <div class="fs-2 fw-bolder text-primary">{{ number_format($totalRequired,0) }}</div>
                    <div class="fs-9 text-muted fw-semibold">Total Required</div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="bg-light-success rounded-2 p-4 text-center">
                    <div class="fs-2 fw-bolder text-success">{{ number_format($totalPaid,0) }}</div>
                    <div class="fs-9 text-muted fw-semibold">Total Paid</div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="{{ $balance < 0 ? 'bg-light-danger' : 'bg-light-success' }} rounded-2 p-4 text-center">
                    <div class="fs-2 fw-bolder {{ $balance < 0 ? 'text-danger' : 'text-success' }}">{{ number_format(abs($balance),0) }}</div>
                    <div class="fs-9 text-muted fw-semibold">{{ $balance < 0 ? 'Outstanding' : 'Credit' }}</div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="bg-light-info rounded-2 p-4 text-center">
                    <div class="fs-2 fw-bolder text-info">{{ $fees->count() }}</div>
                    <div class="fs-9 text-muted fw-semibold">Payments</div>
                </div>
            </div>
        </div>

        @if($groups->isNotEmpty())
        <div class="fw-bold text-muted fs-8 text-uppercase ls-1 mb-3">Per Course</div>
        <div class="table-responsive mb-6">
            <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3 fs-7">
                <thead>
                    <tr class="fw-bold text-muted text-uppercase fs-8">
                        <th>Course</th>
                        <th class="d-none d-sm-table-cell">Teacher</th>
                        <th class="text-end">Required</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                        <th class="text-center d-none d-md-table-cell">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $gs)
                    @php
                        $cPaid = $fees->where('group_id', optional($gs->group)->id)->sum('student_fee_paid');
                        $cReq  = $gs->student_fee_total ?? 0;
                        $cBal  = $cPaid - $cReq;
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ optional($gs->group)->name ?? '—' }}</td>
                        <td class="text-muted d-none d-sm-table-cell">{{ optional(optional($gs->group)->teacher)->name ?? '—' }}</td>
                        <td class="text-end fw-bold">{{ number_format($cReq,0) }}</td>
                        <td class="text-end fw-bold text-success">{{ number_format($cPaid,0) }}</td>
                        <td class="text-end fw-bold {{ $cBal < 0 ? 'text-danger' : 'text-success' }}">
                            {{ $cBal < 0 ? '-' : '+' }}{{ number_format(abs($cBal),0) }}
                        </td>
                        <td class="text-center d-none d-md-table-cell">
                            <span class="badge {{ $cBal < 0 ? 'badge-light-danger' : 'badge-light-success' }}">
                                {{ $cBal < 0 ? 'Due' : 'Settled' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($fees->isEmpty())
            <div class="text-center py-8">
                <i class="ki-duotone ki-wallet fs-3x text-muted d-block mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <span class="fs-6 text-muted">No payment records found.</span>
            </div>
        @else
        <div class="fw-bold text-muted fs-8 text-uppercase ls-1 mb-3">Payment Log</div>
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3 fs-7">
                <thead>
                    <tr class="fw-bold text-muted text-uppercase fs-8">
                        <th class="w-30px">#</th>
                        <th>Course</th>
                        <th>Amount</th>
                        <th class="d-none d-sm-table-cell">Type</th>
                        <th class="d-none d-sm-table-cell">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fees as $i => $fee)
                    <tr>
                        <td class="text-muted fs-8">{{ $i+1 }}</td>
                        <td class="fw-semibold">{{ optional($fee->group)->name ?? '—' }}</td>
                        <td><span class="badge badge-light-success fw-bold">{{ number_format($fee->student_fee_paid,0) }}</span></td>
                        <td class="d-none d-sm-table-cell">
                            @if($fee->student_paid_type == 1)
                                <span class="badge badge-light-primary">Cash</span>
                            @elseif($fee->student_paid_type == 2)
                                <span class="badge badge-light-info">Transfer</span>
                            @else
                                <span class="badge badge-light-secondary">{{ $fee->student_paid_type ?? '—' }}</span>
                            @endif
                        </td>
                        <td class="text-muted d-none d-sm-table-cell">
                            {{ $fee->created_at ? \Carbon\Carbon::parse($fee->created_at)->format('d M Y') : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ══ TAB 3 — ATTENDANCE ══ --}}
    <div class="tab-pane fade" id="{{ $uniqueId }}_attendance">

        <div class="row g-4 mb-6">
            <div class="col-4">
                <div class="{{ $absenceCount > 5 ? 'bg-light-danger' : 'bg-light-warning' }} rounded-2 p-4 text-center">
                    <div class="fs-1 fw-bolder {{ $absenceCount > 5 ? 'text-danger' : 'text-warning' }}">{{ $absenceCount }}</div>
                    <div class="fs-9 text-muted fw-semibold">Absences</div>
                </div>
            </div>
            <div class="col-4">
                <div class="bg-light-warning rounded-2 p-4 text-center">
                    <div class="fs-1 fw-bolder text-warning">{{ $lateCount }}</div>
                    <div class="fs-9 text-muted fw-semibold">Late</div>
                </div>
            </div>
            <div class="col-4">
                @php $affectedGroups = $absences->groupBy('group_id')->count(); @endphp
                <div class="{{ $absenceCount == 0 ? 'bg-light-success' : 'bg-light-primary' }} rounded-2 p-4 text-center">
                    <div class="fs-1 fw-bolder {{ $absenceCount == 0 ? 'text-success' : 'text-primary' }}">{{ $affectedGroups }}</div>
                    <div class="fs-9 text-muted fw-semibold">Courses</div>
                </div>
            </div>
        </div>

        @if($absences->isEmpty())
            <div class="text-center py-10">
                <i class="ki-duotone ki-calendar-tick fs-3x text-success d-block mb-3">
                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    <span class="path4"></span><span class="path5"></span><span class="path6"></span>
                </i>
                <div class="fs-5 fw-bold text-success mb-1">Perfect Attendance!</div>
                <div class="fs-7 text-muted">No absences recorded.</div>
            </div>
        @else
            @foreach($absences->groupBy('group_id') as $groupId => $groupAbsences)
            @php
                $groupName = '—';
                foreach($groups as $gs) {
                    if(optional($gs->group)->id == $groupId) { $groupName = $gs->group->name; break; }
                }
            @endphp
            <div class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fw-bold fs-7 d-flex align-items-center gap-2">
                        <i class="ki-duotone ki-book fs-5 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        {{ $groupName }}
                    </div>
                    <span class="badge {{ $groupAbsences->count() > 3 ? 'badge-light-danger' : 'badge-light-warning' }}">
                        {{ $groupAbsences->count() }} absence{{ $groupAbsences->count() != 1 ? 's' : '' }}
                    </span>
                </div>
                <div class="d-flex flex-wrap gap-2 ps-2">
                    @foreach($groupAbsences as $abs)
                    <div class="d-flex flex-column align-items-center border border-dashed {{ $abs->is_late ? 'border-warning bg-light-warning text-warning' : 'border-danger bg-light-danger text-danger' }} rounded-2 px-3 py-2 text-center">
                        <span class="fw-bolder fs-7 lh-1">
                            {{ $abs->recorded_at ? \Carbon\Carbon::parse($abs->recorded_at)->format('d') : ($abs->days ?? '—') }}
                        </span>
                        <span class="fs-9 fw-semibold">
                            {{ $abs->recorded_at ? \Carbon\Carbon::parse($abs->recorded_at)->format('M Y') : '' }}
                        </span>
                        <span class="badge {{ $abs->is_late ? 'badge-light-warning' : 'badge-light-danger' }} fs-10 mt-1">
                            {{ $abs->is_late ? 'Late' : 'Absent' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        @endif
    </div>

    {{-- ══ TAB 4 — CERTIFICATES ══ --}}
    <div class="tab-pane fade" id="{{ $uniqueId }}_certs">
        @if($certificates->isEmpty())
            <div class="text-center py-10">
                <i class="ki-duotone ki-medal-star fs-3x text-muted d-block mb-3">
                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                </i>
                <div class="fs-6 text-muted">No certificates issued yet.</div>
                <div class="fs-8 text-muted opacity-75 mt-1">Certificates are issued when the student scores above 75%.</div>
            </div>
        @else
        <div class="row g-4">
            @foreach($certificates as $cert)
            @php $cGroup = $cert->group; @endphp
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card card-flush border border-dashed border-success bg-light-success h-100">
                    <div class="card-body p-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <span class="symbol symbol-45px symbol-circle">
                                <span class="symbol-label bg-success">
                                    <i class="ki-duotone ki-medal-star fs-2 text-white">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                                    </i>
                                </span>
                            </span>
                            <div class="min-w-0">
                                <div class="fw-bolder fs-6 text-truncate">{{ optional($cGroup)->name ?? 'Course' }}</div>
                                <div class="fs-8 text-muted">{{ optional(optional($cGroup)->teacher)->name ?? '' }}</div>
                            </div>
                        </div>

                        <div class="separator separator-dashed border-success mb-4 opacity-25"></div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <div class="fs-9 text-muted fw-semibold mb-1">Certificate Code</div>
                                <code class="fs-7 fw-bolder text-success bg-body border border-success rounded px-2 py-1">{{ $cert->cer_code }}</code>
                            </div>
                            @if($cert->total_degree)
                            <div class="text-end">
                                <div class="fs-2 fw-bolder text-success">{{ $cert->total_degree }}%</div>
                                <div class="fs-9 text-muted">Score</div>
                            </div>
                            @endif
                        </div>

                        <a href="{{ url('certificates') }}" target="_blank" class="btn btn-sm btn-light-success w-100">
                            <i class="ki-duotone ki-eye fs-5 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            Verify Certificate
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ══ TAB 5 — PERSONAL INFO ══ --}}
    <div class="tab-pane fade" id="{{ $uniqueId }}_personal">
        <div class="row g-5">

            <div class="col-12 col-lg-{{ $info->parent_id && $info->parent ? '6' : '12' }}">
                <div class="fw-bold text-muted fs-8 text-uppercase ls-1 mb-4">Student Details</div>
                <div class="row g-3">
                    @php
                    $fields = [
                        ['ki-profile-circle','Full Name',    $info->name],
                        ['ki-phone',         'Mobile',       $info->mobile],
                        ['ki-sms',           'Email',        $info->email],
                        ['ki-calendar',      'Date of Birth',$info->dob ? \Carbon\Carbon::parse($info->dob)->format('d M Y') : null],
                        ['ki-profile-user',  'Gender',       $info->gender ? (in_array($info->gender,['male','1'])?'Male':'Female') : null],
                        ['ki-teacher',       'Occupation',   $info->job],
                        ['ki-book',          'Major',        $info->major],
                        ['ki-graph-up',      'Level',        $info->current_level],
                        ['ki-calendar',      'Join Date',    $info->join_date ? \Carbon\Carbon::parse($info->join_date)->format('d M Y') : null],
                        ['ki-medal-star',    'Placement',    $info->exam_degree ? $info->exam_degree.'%' : null],
                        ['ki-map',           'Address',      $info->address],
                        ['ki-heart',         'Health Notes', $info->health_conditions],
                        ['ki-notification',  'Program Type', $info->program_type],
                    ];
                    @endphp
                    @foreach($fields as [$icon,$label,$val])
                    @if($val)
                    <div class="col-12 col-sm-6">
                        <div class="d-flex align-items-start gap-3 bg-light rounded-2 px-3 py-3">
                            <i class="ki-duotone {{ $icon }} fs-4 text-primary mt-1 flex-shrink-0">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                            </i>
                            <div class="min-w-0">
                                <div class="fs-9 text-muted fw-semibold">{{ $label }}</div>
                                <div class="fs-7 fw-bold text-break">{{ $val }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            @if($info->parent_id && $info->parent)
            <div class="col-12 col-lg-6">
                <div class="fw-bold text-muted fs-8 text-uppercase ls-1 mb-4 d-flex align-items-center gap-2">
                    <i class="ki-duotone ki-people fs-5 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                    Parent / Guardian
                </div>
                <div class="card card-flush border border-dashed border-warning bg-light-warning">
                    <div class="card-body p-5">
                        <div class="d-flex align-items-center gap-4 mb-5">
                            <span class="symbol symbol-50px symbol-circle">
                                <span class="symbol-label bg-warning">
                                    <i class="ki-duotone ki-people fs-2 text-white">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                    </i>
                                </span>
                            </span>
                            <div>
                                <div class="fw-bolder fs-5">{{ $info->parent->name }}</div>
                                @if($info->parent->relationship)
                                <span class="badge badge-warning fs-9">{{ $info->parent->relationship }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="row g-3">
                            @if($info->parent->phone)
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-3 bg-body rounded-2 px-3 py-3">
                                    <i class="ki-duotone ki-phone fs-3 text-warning"><span class="path1"></span><span class="path2"></span></i>
                                    <div>
                                        <div class="fs-9 text-muted">Phone</div>
                                        <a href="tel:{{ $info->parent->phone }}" class="fs-7 fw-bold">{{ $info->parent->phone }}</a>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($info->parent->email)
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-3 bg-body rounded-2 px-3 py-3">
                                    <i class="ki-duotone ki-sms fs-3 text-warning"><span class="path1"></span><span class="path2"></span></i>
                                    <div>
                                        <div class="fs-9 text-muted">Email</div>
                                        <a href="mailto:{{ $info->parent->email }}" class="fs-7 fw-bold">{{ $info->parent->email }}</a>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
