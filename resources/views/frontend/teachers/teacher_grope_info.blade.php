<div class="info-card" style="padding: 0; overflow: hidden; border: none; background: transparent; box-shadow: none;">
    <!-- Modern Header -->
    <div class="detail-header" style="background: linear-gradient(135deg, #1a2744 0%, #2d3748 100%); padding: 18px 22px; border-radius: 12px 12px 0 0; color: white;">
        <div class="d-flex justify-content-between align-items-center mb-10">
            <div style="font-size:16px; font-weight:700; color:white;"><i class="fa fa-graduation-cap" style="color:var(--accent);"></i> {{ $data->group->program->title }}</div>
            <button href="#Courses" class="btn btn-sm modern-back-btn" id="go-back" data-toggle="tab">
                <i class="fa fa-arrow-left"></i> Back
            </button>
        </div>
        <div class="d-flex align-items-center gap-15">
            <span class="badge" style="background: rgba(245, 197, 24, 0.2); color: #f5c518; border: 1px solid rgba(245, 197, 24, 0.3);">{{ $data->group->name }}</span>
            <span class="opacity-70"><i class="fa fa-clock-o"></i> {{ $data->group->ctime->times }}</span>
        </div>
    </div>

    <div class="dashboard-content" style="margin-top: 0; border-radius: 0 0 12px 12px;">
        <div class="row">
            <div class="col-md-4">
                <div class="info-card" style="background: var(--bg-light); border: 1px solid #e1e4e8; height: 100%;">
                    <h4 class="mb-20 text-primary" style="font-weight: 700; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
                        <i class="fa fa-info-circle"></i> Group Logistics
                    </h4>
                    <div class="mb-15">
                        <label class="text-muted small d-block">Schedule Days</label>
                        <p class="m-0" style="font-weight: 600; color: var(--primary);">{{ $data->group->ctime->days }}</p>
                    </div>
                    <div class="mb-15">
                        <label class="text-muted small d-block">Time Slot</label>
                        <p class="m-0" style="font-weight: 600; color: var(--primary);">{{ $data->group->ctime->times }}</p>
                    </div>
                    <div class="mb-15">
                        <label class="text-muted small d-block">Duration</label>
                        <p class="m-0" style="font-weight: 600; color: var(--primary);">36 Sessions (72 Hours)</p>
                    </div>
                    <div class="mb-15">
                        <label class="text-muted small d-block">Start Date</label>
                        <p class="m-0" style="font-weight: 600; color: var(--primary);">{{ date('d M Y', strtotime($data->group->start_date)) }}</p>
                    </div>
                    <div class="mb-20">
                        <label class="text-muted small d-block">Estimated End</label>
                        <p class="m-0" style="font-weight: 600; color: var(--primary);">{{ date('d M Y', strtotime($data->group->end_date)) }}</p>
                    </div>
                    
                    <hr style="border-top: 1px solid #ddd;">
                    
                    <a href="javascript:void(0);" data-id="{{ $data->group->id }}" data-user="{{ $data->group->name }}" 
                       class="chat-toggle btn btn-primary w-100" style="padding: 12px; border-radius: 8px; font-weight: 700;">
                        <i class="fa fa-comments"></i> Launch Group Chat
                    </a>
                </div>
            </div>
            
            <div class="col-md-8">
                <div style="font-size:14px; font-weight:700; color:var(--primary); margin-bottom:14px;"><i class="fa fa-cogs" style="color:var(--accent);"></i> Instructor Tools</div>
                <div class="action-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                    @if($data->group->zoom)
                        <a href="{{ $data->group->zoom }}" target="_blank" class="action-card zoom" style="text-decoration: none;">
                            <i class="fa fa-video-camera" style="color: #2D8CFF;"></i>
                            <span style="display: block; font-weight: 700; color: #1a2744;">Start Zoom Class</span>
                        </a>
                    @endif

                    @if($data->group->teacher_lib)
                        <a href="{{ $data->group->teacher_lib }}" download="{{ $data->group->name }}" class="action-card download" style="text-decoration: none;">
                            <i class="fa fa-folder-open" style="color: #FFC107;"></i>
                            <span style="display: block; font-weight: 700; color: #1a2744;">Media Library</span>
                        </a>
                    @endif

                    <a href="{{ route('teacher.showGroueStudents', ['group_id' => Crypt::encrypt($data->group_id), 'teacher_id' => Crypt::encrypt($data->group->teacher_id)]) }}" class="action-card drive" style="text-decoration: none;">
                        <i class="fa fa-users" style="color: #1FA463;"></i>
                        <span style="display: block; font-weight: 700; color: #1a2744;">Students List</span>
                    </a>

                    <a href="{{ url('group/' . $data->group_id) }}" class="action-card zoom" style="text-decoration: none; border-color: #f5c518;">
                        <i class="fa fa-check-square-o" style="color: #f5c518;"></i>
                        <span style="display: block; font-weight: 700; color: #1a2744;">Record Marks</span>
                    </a>

                    <a href="{{ url('group/attendance/' . $data->group_id) }}" class="action-card drive" style="text-decoration: none;">
                        <i class="fa fa-calendar-check-o" style="color: #1FA463;"></i>
                        <span style="display: block; font-weight: 700; color: #1a2744;">Attendance Sheet</span>
                    </a>

                    <a href="{{ url('examDate/' . $data->group_id) }}" class="action-card download" style="text-decoration: none;">
                        <i class="fa fa-file-text-o" style="color: #FFC107;"></i>
                        <span style="display: block; font-weight: 700; color: #1a2744;">Exams Schedule</span>
                    </a>
                </div>
                
                <div class="info-card mt-20" style="background: rgba(245, 197, 24, 0.05); border: 1px solid rgba(245, 197, 24, 0.2); border-radius: 10px;">
                    <p class="m-0 small" style="color: #856404; line-height: 1.6;">
                        <i class="fa fa-info-circle"></i> 
                        <strong>Pro Tip:</strong> Ensure you record attendance within the first 15 minutes of the session. Students can view their marks and progress in real-time once you save them.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
