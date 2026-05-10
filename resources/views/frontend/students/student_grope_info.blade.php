<div class="info-card" style="padding: 0; overflow: hidden; border: none;">
    <!-- Modern Header -->
    <div class="detail-header">
        <div class="d-flex justify-content-between align-items-center mb-20">
            <h2 class="m-0" style="color: white;"><i class="fa fa-graduation-cap"></i> {{ $data->group->program->title }}</h2>
            <button href="#Courses" class="btn btn-sm modern-back-btn" id="go-back" data-toggle="tab">
                <i class="fa fa-arrow-left"></i> Back to Courses
            </button>
        </div>
        <p class="m-0 opacity-70">Group Name: <strong>{{ $data->group->name }}</strong></p>
    </div>

    <div style="padding: 30px;">
        <div class="row">
            <div class="col-md-4">
                <div class="info-card" style="background: var(--bg-light); border: 1px dashed #ccc;">
                    <h4 class="mb-20 text-primary"><i class="fa fa-user"></i> Lecturer Info</h4>
                    <p class="mb-10"><strong>Name:</strong> {{ $data->group->teacher->name }}</p>
                    <p class="mb-10"><strong>Schedule:</strong> {{ $data->group->ctime->days }}</p>
                    <p class="mb-10"><strong>Time:</strong> {{ $data->group->ctime->times }}</p>
                    <p class="mb-0"><strong>Start Date:</strong> {{ date('d M Y', strtotime($data->group->start_date)) }}</p>
                    
                    <hr>
                    
                    <a href="javascript:void(0);" data-id="{{ $data->group->id }}" data-user="{{ $data->group->name }}" 
                       class="chat-toggle btn btn-primary w-100">
                        <i class="fa fa-comments"></i> Course Chat
                    </a>
                </div>
            </div>
            
            <div class="col-md-8">
                <h4 class="mb-20"><i class="fa fa-rocket"></i> Quick Actions & Resources</h4>
                <div class="action-grid">
                    @if($data->group->zoom)
                        <a href="{{ $data->group->zoom }}" target="_blank" class="action-card zoom">
                            <i class="fa fa-video-camera"></i>
                            <span>Join Zoom Class</span>
                        </a>
                    @endif

                    @if($data->group->drive)
                        <a href="{{ $data->group->drive }}" target="_blank" class="action-card drive">
                            <i class="fa fa-google"></i>
                            <span>Google Drive</span>
                        </a>
                    @endif

                    @if($data->group->subjects)
                        <a href="{{ asset($data->group->subjects) }}" download class="action-card download">
                            <i class="fa fa-download"></i>
                            <span>Download Materials</span>
                        </a>
                    @endif
                    
                    @if($data->cer_code)
                        <a href="{{ route('student.certificate.download', Crypt::encrypt($data->id)) }}" class="action-card certificate">
                            <i class="fa fa-certificate"></i>
                            <span>Download Certificate</span>
                        </a>
                    @endif
                </div>
                
                <div class="info-card mt-20" style="background: #fff8e1; border-color: #ffe082;">
                    <p class="m-0" style="color: #856404;">
                        <i class="fa fa-info-circle"></i> 
                        <strong>Note:</strong> All materials are updated weekly by your lecturer. Please check the Google Drive for recorded sessions.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
