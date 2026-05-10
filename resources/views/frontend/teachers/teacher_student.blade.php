<div class="info-card" style="padding: 0; overflow: hidden; border: none; background: transparent; box-shadow: none;">
    <!-- Modern Header -->
    <div class="detail-header" style="background: linear-gradient(135deg, #1a2744 0%, #2d3748 100%); padding: 25px; border-radius: 12px 12px 0 0; color: white;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="m-0" style="color: white; font-weight: 700;"><i class="fa fa-users text-accent"></i> Group Students & Marks</h3>
            <button id="go-back" href="#Courses" data-toggle="tab" class="btn btn-sm modern-back-btn">
                <i class="fa fa-arrow-left"></i> Back to Courses
            </button>
        </div>
    </div>

    <div class="dashboard-content" style="margin-top: 0; border-radius: 0 0 12px 12px;">
        <div class="table-responsive">
            <table class="table-modern">
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
                            <td>
                                <img src="{{ $group->student->image ? url($group->student->image) : url('assets/oxford/img/students/avatar.png') }}"
                                     style="width: 45px; height: 45px; border-radius: 50%; border: 2px solid #eee; object-fit: cover;">
                            </td>
                            <td>
                                <strong style="color: var(--primary);">{{ $group->student->name }}</strong>
                                <div class="small text-muted">ID: #{{ $group->student->id }}</div>
                            </td>
                            <td class="text-center">
                                <span style="font-weight: 600; color: {{ $group->exam1_degree ? 'var(--primary)' : '#f5222d' }}">
                                    {!! $group->exam1_degree ?: '<i class="fa fa-minus-circle"></i>' !!}
                                </span>
                            </td>
                            <td class="text-center">
                                <span style="font-weight: 600; color: {{ $group->exam2_degree ? 'var(--primary)' : '#f5222d' }}">
                                    {!! $group->exam2_degree ?: '<i class="fa fa-minus-circle"></i>' !!}
                                </span>
                            </td>
                            <td class="text-center">
                                <span style="font-weight: 600; color: {{ $group->exam3_degree ? 'var(--primary)' : '#f5222d' }}">
                                    {!! $group->exam3_degree ?: '<i class="fa fa-minus-circle"></i>' !!}
                                </span>
                            </td>
                            <td class="text-center">
                                <span style="font-weight: 700; color: {{ $group->exam4_degree ? '#28a745' : '#f5222d' }}">
                                    {!! $group->exam4_degree ?: '<i class="fa fa-minus-circle"></i>' !!}
                                </span>
                            </td>
                            <td class="text-center">
                                <span style="font-weight: 600;">{!! $group->activity_degree ?: '-' !!}</span>
                            </td>
                            <td class="text-center">
                                <span style="font-weight: 600;">{!! $group->workbook_degree ?: '-' !!}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge" style="background: var(--primary); color: white;">{{ $group->total_degree ?: 0 }}</span>
                            </td>
                            <td class="text-center">
                                @if ($group->has_evaluation == 0)
                                    <a href="{{ route('teacher.evaluate.view',[ 'group_id' => Crypt::encrypt($group_id),'student_id'=>Crypt::encrypt($group->student_id)])}}" 
                                       class="btn btn-xs btn-warning" style="font-weight: 700; border-radius: 4px;">
                                        <i class="fa fa-star"></i> Evaluate
                                    </a>
                                @else
                                    <span class="text-success small" style="font-weight: 700;"><i class="fa fa-check-circle"></i> Done</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
