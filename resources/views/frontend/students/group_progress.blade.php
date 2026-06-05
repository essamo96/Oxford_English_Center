<div class="dash-card dash-card--flush animate__animated animate__fadeIn ajax-content">
    <div class="dash-card__head">
        <h3 class="dash-card__title"><i class="fa fa-line-chart"></i> Learning Progress</h3>
    </div>

    <div class="dash-card__body">
        <div class="table-responsive">
            @if($data->count() > 0)
                <table class="dash-table dash-table--sticky" data-responsive data-enhance>
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
                                <td data-label="Group Name"><strong>{{ $group->group->name ?? 'N/A' }}</strong></td>
                                <td data-label="Progress">
                                    <div class="d-flex align-items-center" style="gap:10px;">
                                        <div class="custom-progress flex-grow-1">
                                            <div class="progress-fill" style="width: {{ $group->progress ?: 0 }}%"></div>
                                        </div>
                                        <span class="small font-weight-bold">{{ $group->progress ?: 0 }}%</span>
                                    </div>
                                </td>
                                <td class="text-center" data-label="Current Units">
                                    <span class="badge-info">
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
                <div class="empty-state">
                    <i class="fa fa-bar-chart"></i>
                    <div class="empty-state__title">No progress data found.</div>
                    <p>Stay tuned! Your course progress will be updated here soon.</p>
                </div>
            @endif
        </div>
    </div>
</div>
