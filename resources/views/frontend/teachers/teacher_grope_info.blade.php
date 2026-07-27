<div class="dash-card dash-card--flush ajax-content">
    <!-- Modern Header -->
    <div class="detail-header" style="padding: 18px 22px; border-radius: 12px 12px 0 0;">
        <div class="d-flex justify-content-between align-items-center mb-10">
            <div style="font-size: 19.2px; font-weight:700; color:#fff;"><i class="fa fa-graduation-cap" style="color:var(--gold-mid);"></i> {{ $data->group->program->title }}</div>
            <button href="#Courses" class="dash-btn dash-btn--sm" id="go-back" data-toggle="tab">
                <i class="fa fa-arrow-left"></i> Back
            </button>
        </div>
        <div class="d-flex align-items-center gap-15">
            <span class="badge-warning">{{ $data->group->name }}</span>
            <span style="color:#fff;opacity:.75;"><i class="fa fa-clock-o"></i> {{ $data->group->ctime->times }}</span>
        </div>
    </div>

    <div style="padding: 22px;">
        <div class="row">
            <div class="col-md-4">
                <div class="dash-card" style="height: 100%;">
                    <h4 class="mb-20" style="font-weight:700;border-bottom:2px solid var(--border-color);padding-bottom:10px;">
                        <i class="fa fa-info-circle"></i> Group Logistics
                    </h4>
                    <div class="info-row"><div><div class="info-row__label">Schedule Days</div><div class="info-row__value">{{ $data->group->ctime->days }}</div></div></div>
                    <div class="info-row"><div><div class="info-row__label">Time Slot</div><div class="info-row__value">{{ $data->group->ctime->times }}</div></div></div>
                    <div class="info-row"><div><div class="info-row__label">Duration</div><div class="info-row__value">36 Sessions (72 Hours)</div></div></div>
                    <div class="info-row"><div><div class="info-row__label">Start Date</div><div class="info-row__value">{{ date('d M Y', strtotime($data->group->start_date)) }}</div></div></div>
                    <div class="info-row"><div><div class="info-row__label">Estimated End</div><div class="info-row__value">{{ date('d M Y', strtotime($data->group->end_date)) }}</div></div></div>

                    <a href="javascript:void(0);" data-id="{{ $data->group->id }}" data-user="{{ $data->group->name }}"
                       class="chat-toggle dash-btn dash-btn--primary dash-btn--block" style="margin-top:16px;">
                        <i class="fa fa-comments"></i> Launch Group Chat
                    </a>
                </div>
            </div>

            <div class="col-md-8">
                <div style="font-size: 17.2px; font-weight:700; margin-bottom:14px; color:var(--text-primary);"><i class="fa fa-cogs" style="color:var(--gold-mid);"></i> Instructor Tools</div>
                <div class="action-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                    @if($data->group->zoom)
                        <a href="{{ $data->group->zoom }}" target="_blank" class="action-card zoom">
                            <i class="fa fa-video-camera"></i>
                            <span>Start Zoom Class</span>
                        </a>
                    @endif

                    @if($data->group->teacher_lib)
                        <a href="{{ $data->group->teacher_lib }}" download="{{ $data->group->name }}" class="action-card download">
                            <i class="fa fa-folder-open"></i>
                            <span>Media Library</span>
                        </a>
                    @endif

                    <a href="{{ route('teacher.showGroueStudents', ['group_id' => Crypt::encrypt($data->group_id), 'teacher_id' => Crypt::encrypt($data->group->teacher_id)]) }}" class="action-card drive">
                        <i class="fa fa-users"></i>
                        <span>Students List</span>
                    </a>

                    <a href="{{ url('group/' . $data->group_id) }}" class="action-card zoom">
                        <i class="fa fa-check-square-o"></i>
                        <span>Record Marks</span>
                    </a>

                    <a href="{{ url('group/attendance/' . $data->group_id) }}" class="action-card drive">
                        <i class="fa fa-calendar-check-o"></i>
                        <span>Attendance Sheet</span>
                    </a>

                    <a href="{{ url('examDate/' . $data->group_id) }}" class="action-card download">
                        <i class="fa fa-file-text-o"></i>
                        <span>Exams Schedule</span>
                    </a>
                </div>

                <div class="dash-card mt-20" style="background: var(--c-warning-bg); border-color: var(--c-warning);">
                    <p class="m-0 small" style="color: var(--text-primary); line-height: 1.6;">
                        <i class="fa fa-info-circle"></i>
                        <strong>Pro Tip:</strong> Ensure you record attendance within the first 15 minutes of the session. Students can view their marks and progress in real-time once you save them.
                    </p>
                </div>

                {{-- Student notes recorded during evaluation — previously saved but never shown anywhere --}}
                <div class="dash-card mt-20">
                    <h4 class="mb-20" style="font-weight:700;border-bottom:2px solid var(--border-color);padding-bottom:10px;">
                        <i class="fa fa-sticky-note-o"></i> Student Notes
                    </h4>
                    @forelse(($student_notes ?? []) as $note)
                        <div class="d-flex gap-15 mb-15 pb-15" style="border-bottom:1px dashed var(--border-color);">
                            <div class="symbol symbol-40px flex-shrink-0" style="width:40px;height:40px;border-radius:50%;overflow:hidden;background:#2c3547;">
                                <img src="{{ $note->students && $note->students->image && file_exists(public_path($note->students->image)) ? asset($note->students->image) : asset('assets/oxford/images/user-avatar.png') }}"
                                     alt="{{ $note->students->name ?? '—' }}" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            <div class="flex-grow-1" style="min-width:0;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong style="color:var(--text-primary);">{{ $note->students->name ?? 'Unknown Student' }}</strong>
                                    <span class="small" style="color:var(--light-text);">{{ $note->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="m-0 small" style="color:var(--text-primary); line-height:1.6; white-space:pre-line;">{{ $note->notes }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="m-0 small" style="color: var(--light-text);">No notes recorded yet for this group's students.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
