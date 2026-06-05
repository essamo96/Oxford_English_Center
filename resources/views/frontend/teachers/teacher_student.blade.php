<div class="dash-card dash-card--flush ajax-content">
    <!-- Modern Header -->
    <div class="detail-header" style="padding: 22px; border-radius: 12px 12px 0 0;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="m-0" style="color:#fff; font-weight: 700;"><i class="fa fa-users" style="color:var(--gold-mid);"></i> Group Students &amp; Marks</h3>
            <button id="go-back" href="#Courses" data-toggle="tab" class="dash-btn dash-btn--sm">
                <i class="fa fa-arrow-left"></i> Back to Courses
            </button>
        </div>
    </div>

    <div style="padding: 18px;">
        <div class="table-responsive">
            <table class="dash-table dash-table--sticky" data-responsive data-enhance>
                <thead>
                    <tr>
                        <th style="width: 80px;">Student</th>
                        <th>Name</th>
                        <th class="text-center">PT 1</th>
                        <th class="text-center">PT 2</th>
                        <th class="text-center">PT 3</th>
                        <th class="text-center">Final</th>
                        <th class="text-center">Activity</th>
                        <th class="text-center">Workbook</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Evaluation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $group)
                        <tr>
                            <td data-label="Student">
                                <img src="{{ $group->student->image ? url($group->student->image) : url('assets/oxford/img/students/avatar.png') }}"
                                     style="width: 42px; height: 42px; border-radius: 50%; border: 2px solid var(--border-color); object-fit: cover;">
                            </td>
                            <td data-label="Name">
                                <strong>{{ $group->student->name }}</strong>
                                <div class="small text-muted">ID: #{{ $group->student->id }}</div>
                            </td>
                            <td class="text-center" data-label="PT 1">{!! $group->exam1_degree ? '<span class="badge-success">'.$group->exam1_degree.'</span>' : '<span class="badge-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                            <td class="text-center" data-label="PT 2">{!! $group->exam2_degree ? '<span class="badge-success">'.$group->exam2_degree.'</span>' : '<span class="badge-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                            <td class="text-center" data-label="PT 3">{!! $group->exam3_degree ? '<span class="badge-success">'.$group->exam3_degree.'</span>' : '<span class="badge-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                            <td class="text-center" data-label="Final">{!! $group->exam4_degree ? '<span class="badge-success">'.$group->exam4_degree.'</span>' : '<span class="badge-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                            <td class="text-center" data-label="Activity">{!! $group->activity_degree ?: '-' !!}</td>
                            <td class="text-center" data-label="Workbook">{!! $group->workbook_degree ?: '-' !!}</td>
                            <td class="text-center" data-label="Total"><span class="badge-info">{{ $group->total_degree ?: 0 }}</span></td>
                            <td class="text-center" data-label="Evaluation">
                                @if ($group->has_evaluation == 0)
                                    <a href="{{ route('teacher.evaluate.view',[ 'group_id' => Crypt::encrypt($group_id),'student_id'=>Crypt::encrypt($group->student_id)])}}"
                                       class="dash-btn dash-btn--primary dash-btn--sm">
                                        <i class="fa fa-star"></i> Evaluate
                                    </a>
                                @else
                                    <span class="badge-success"><i class="fa fa-check-circle"></i> Done</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
