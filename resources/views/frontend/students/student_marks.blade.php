<div class="dash-card dash-card--flush animate__animated animate__fadeIn ajax-content">
    <div class="dash-card__head">
        <h3 class="dash-card__title"><i class="fa fa-check-square-o"></i> Academic Performance</h3>
    </div>

    <div class="dash-card__body">
        <div class="table-responsive">
            @if($data->count() > 0)
                <table class="dash-table dash-table--sticky" data-responsive data-enhance>
                    <thead>
                        <tr>
                            <th>Group Name</th>
                            <th class="text-center">P.T 1<br><small>(Units 1-3)</small></th>
                            <th class="text-center">P.T 2<br><small>(Units 4-6)</small></th>
                            <th class="text-center">P.T 3<br><small>(Units 7-9)</small></th>
                            <th class="text-center">Final Exam<br><small>(Units 1-12)</small></th>
                            <th class="text-center">Activity</th>
                            <th class="text-center">Workbook</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $group)
                            <tr>
                                <td data-label="Group Name">
                                    <div class="dash-list__main" style="display:flex;align-items:center;gap:12px;min-width:180px;">
                                        <img src="{{ isset($group->group->image) && $group->group->image ? url($group->group->image) : url('assets/oxford/img/logo.png') }}" alt="{{ $group->group->name ?? 'Group' }}" style="width:45px;height:45px;border-radius:6px;object-fit:cover;">
                                        <div>
                                            <div class="dash-list__title">{{ $group->group->name ?? 'N/A' }}</div>
                                            <div class="dash-list__sub"><i class="fa fa-user"></i> {{ isset($group->group->teacher) ? $group->group->teacher->name : 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center" data-label="P.T 1">{!! $group->exam1_degree !== null ? '<span class="badge-success">'.$group->exam1_degree.'</span>' : '<span class="badge-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                                <td class="text-center" data-label="P.T 2">{!! $group->exam2_degree !== null ? '<span class="badge-success">'.$group->exam2_degree.'</span>' : '<span class="badge-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                                <td class="text-center" data-label="P.T 3">{!! $group->exam3_degree !== null ? '<span class="badge-success">'.$group->exam3_degree.'</span>' : '<span class="badge-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                                <td class="text-center" data-label="Final Exam">{!! $group->exam4_degree !== null ? '<span class="badge-success">'.$group->exam4_degree.'</span>' : '<span class="badge-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                                <td class="text-center" data-label="Activity">{{ $group->activity_degree ?? '-' }}</td>
                                <td class="text-center" data-label="Workbook">{{ $group->workbook_degree ?? '-' }}</td>
                                <td class="text-center" data-label="Total"><strong>{{ $group->total_degree ?? '-' }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <i class="fa fa-folder-open-o"></i>
                    <div class="empty-state__title">No marks available yet.</div>
                    <p>Your performance data will appear here once exams are completed.</p>
                </div>
            @endif
        </div>
    </div>
</div>
