<div class="info-card animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-20">
        <h3 class="m-0"><i class="fa fa-check-square-o"></i> Academic Performance</h3>
    </div>

    <div class="table-responsive">
        @if($data->count() > 0)
            <table class="table-modern">
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
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px; text-align: left; min-width: 200px;">
                                    <img src="{{ isset($group->group->image) && $group->group->image ? url($group->group->image) : url('assets/oxford/img/logo.png') }}" alt="{{ $group->group->name ?? 'Group' }}" style="width: 45px; height: 45px; border-radius: 6px; object-fit: cover; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                    <div>
                                        <div style="font-weight: 700; color: var(--primary); font-size: 14px;">{{ $group->group->name ?? 'N/A' }}</div>
                                        <div style="font-size: 11px; color: var(--light-text); margin-top: 2px;">
                                            <i class="fa fa-user" style="color: var(--accent);"></i> {{ isset($group->group->teacher) ? $group->group->teacher->name : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">{!! $group->exam1_degree !== null ? '<span class="badge status-active">'.$group->exam1_degree.'</span>' : '<span class="text-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                            <td class="text-center">{!! $group->exam2_degree !== null ? '<span class="badge status-active">'.$group->exam2_degree.'</span>' : '<span class="text-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                            <td class="text-center">{!! $group->exam3_degree !== null ? '<span class="badge status-active">'.$group->exam3_degree.'</span>' : '<span class="text-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                            <td class="text-center">{!! $group->exam4_degree !== null ? '<span class="badge bg-success">'.$group->exam4_degree.'</span>' : '<span class="text-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                            <td class="text-center">{{ $group->activity_degree ?? '-' }}</td>
                            <td class="text-center">{{ $group->workbook_degree ?? '-' }}</td>
                            <td class="text-center"><strong>{{ $group->total_degree ?? '-' }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center p-50">
                <i class="fa fa-folder-open-o fa-5x text-muted mb-20" style="display: block;"></i>
                <h4 class="text-muted">No marks available yet.</h4>
                <p>Your performance data will appear here once exams are completed.</p>
            </div>
        @endif
    </div>
</div>
