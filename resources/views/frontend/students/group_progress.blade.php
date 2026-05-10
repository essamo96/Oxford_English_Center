<div class="info-card animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-20">
        <h3 class="m-0"><i class="fa fa-line-chart"></i> Learning Progress</h3>
    </div>

    <div class="table-responsive">
        @if($data->count() > 0)
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 30%;">Group Name</th>
                        <th style="width: 45%;">Progress</th>
                        <th style="width: 25%;" class="text-center">Current Units</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $group)
                        <tr>
                            <td><strong>{{ $group->group->name ?? 'N/A' }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="custom-progress flex-grow-1 mr-15">
                                        <div class="progress-fill" style="width: {{ $group->progress ?: 0 }}%"></div>
                                    </div>
                                    <span class="small font-weight-bold">{{ $group->progress ?: 0 }}%</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge status-active">
                                    @if ($group->progress <= 30 || $group->progress == null)
                                        Units 1-3
                                    @elseif ($group->progress <= 60)
                                        Units 4-6
                                    @elseif ($group->progress <= 90)
                                        Units 7-9
                                    @else
                                        Units 10-12
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center p-50">
                <i class="fa fa-bar-chart fa-5x text-muted mb-20" style="display: block;"></i>
                <h4 class="text-muted">No progress data found.</h4>
                <p>Stay tuned! Your course progress will be updated here soon.</p>
            </div>
        @endif
    </div>
</div>
