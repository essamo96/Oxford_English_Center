@foreach ($student_groups as $group)
<div class="course-card">
    <div class="course-img-wrapper">
        <img src="{{ $group->group->image ? url($group->group->image) : url('assets/oxford/img/logo.png') }}" class="course-img" alt="Course">
        <div class="course-overlay">
            <a href="#Info" data-toggle="tab" class="joinG btn-join-circle" 
               data-group_id="{{ Crypt::encrypt($group->group_id) }}" 
               data-student_id="{{ Crypt::encrypt($group->student_id) }}">
                <i class="fa fa-external-link"></i>
                <span>Join</span>
            </a>
        </div>
    </div>
    
    <div class="course-body">
        <div class="d-flex justify-content-between align-items-start mb-10">
            <h4 class="course-title">{{ $group->group->program->title }}</h4>
            <span class="badge {{ $group->group->status == 1 ? 'status-active' : 'status-delayed' }}">
                {{ $group->group->status == 1 ? 'Active' : 'Finished' }}
            </span>
        </div>
        
        <div class="course-meta">
            <div class="meta-item" title="Lecturer">
                <i class="fa fa-user"></i> {{ $group->group->teacher->name }}
            </div>
            <div class="meta-item" title="Group Name">
                <i class="fa fa-tag"></i> {{ $group->group->name }}
            </div>
            @if($group->group->ctime)
            <div class="meta-item" title="Class Days">
                <i class="fa fa-calendar-check-o"></i> {{ $group->group->ctime->days }}
            </div>
            @endif
            <div class="meta-item" title="Start Date">
                <i class="fa fa-clock-o"></i> {{ date('d M Y', strtotime($group->group->start_date)) }}
            </div>
        </div>
        
        <div class="mt-auto">
            <div class="progress-info d-flex justify-content-between small">
                <span>Progress</span>
                <span>{{ $group->progress ?: 0 }}%</span>
            </div>
            <div class="custom-progress">
                <div class="progress-fill" style="width: {{ $group->progress ?: 0 }}%"></div>
            </div>

            <div class="d-flex gap-10">
                <a href="#Info" data-toggle="tab" class="joinG btn btn-sm btn-primary flex-grow-1" 
                   data-group_id="{{ Crypt::encrypt($group->group_id) }}" 
                   data-student_id="{{ Crypt::encrypt($group->student_id) }}">
                    <i class="fa fa-info-circle"></i> Details
                </a>
                @if($group->cer_code)
                <a href="{{ route('student.certificate.download', Crypt::encrypt($group->id)) }}" 
                   target="_blank" class="btn btn-sm btn-success" title="Preview Certificate">
                    <i class="fa fa-eye"></i>
                </a>
                @endif
                <a href="javascript:void(0);" data-id="{{ $group->group->id }}" data-user="{{ $group->group->name }}" 
                   class="chat-toggle btn btn-sm btn-outline-light" title="Course Chat">
                    <i class="fa fa-comments"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach
