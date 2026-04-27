<div class="tab-pane fade active in" id="Courses2">
    <h3 class="title-section title-bar-high mb-40">Coursess
        <button href="#Courses" class="btn btn-success btn-sm" id="go-back" data-toggle="tab" aria-expanded="false"> back
            <i class="bi bi-grid-fill"></i></button>
    </h3>


    <!-- Courses Page 3 Area Start Here -->
    <div class="courses-page-area3">
        <div class="container">
            <div class="row">
                <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="course-details-inner">
                                <h2 class="title-default-left title-bar-high">{{ $data->group->name }}</h2>
                                <div class="row-12" style="height: 428px;">
                                    <div class="sidebar-course-price col-3" style="width: 323px; HEIGHT: 200PX;">
                                        <a href="{{ $data->group->zoom }}"><span href="#"
                                                style=""class="enroll-btn">zoom</span></a>
                                        @if ($data->group->subjects != null)
                                           <a href="{{ asset($data->group->subjects) }}" download="{{ $data->group->name }}"> <span
                                                style=""
                                                class="download-btn">Media</span></a>
                                                @else
                                            <span style="" class="download-btn">No Media </span>
                                        @endif
                                        @if ($data->group->drive != null)
                                           <a  href="{{ asset($data->group->drive) }}"> <span
                                                style="font-size: 16px;"
                                                class="download-btn bi bi-stack">Drive</span></a>
                                        @endif
                                        <a href="#StudentGroupsMarks"
                                            data-student_id="{{ Crypt::encrypt($data->student_id) }}"
                                            class="Markstudent" data-toggle="tab" aria-expanded="false"><span
                                                href="#" style="" class="enroll-btn">MARKS</span></a>
                                        <span href="#" style="" class="download-btn bi bi-trophy-fill">certificate</span>
                                    </div>
                                    <img alt="Comments"
                                        @if ($data->group->image != null) src="{{ url($data->group->image) }}" 
                                        @else
                                        src="{{ url('assets/oxford/img/logo.png') }}" @endif
                                        width="200px" height="200px" {{-- src="{{ url($data->group->teacher->image) }}" --}} {{-- src="{{ url($data->group->image) }}" width="200px" height="200px" --}}
                                        style="    position: relative;
                                                               top: -202px;
                                                               left: 519px;"
                                        class="media-object col-3">

                                </div>
                                <h3 class="sidebar-title">Course Features</h3>
                                @if ($data != null)

                                    <ul class="course-feature">
                                        <li>Lecturer: {{ $data->group->teacher->name }}</li>
                                        <li>Time: {{ $data->group->ctime->times }}</li>
                                        <li>Days: {{ $data->group->ctime->days }}</li>
                                        <li>Start: {{ date('d F Y', strtotime($data->group->start_date)) }}</li>
                                        <li>Course Duration: 72 Hours</li>
                                        <li>Total Credits: 150</li>
                                        <li>Sessions Per Week: 3</li>
                                        @if ($data->group->program->title == 'IELTS PRO')
                                            <li>Numper Of Week: 5</li>
                                            <li>Total Classes : 15</li>
                                        @elseif ($data->group->program->title == 'Writing Pro')
                                            <li>Numper Of Week: 6</li>
                                            <li>Total Classes : 18</li>
                                        @elseif ($data->group->program->title == 'Conversation Pro')
                                            <li>Numper Of Week: 5</li>
                                            <li>Total Classes : 15</li>
                                        @else
                                            <li>Numper Of Week: 12</li>
                                            <li>Total Classes : 36</li>
                                        @endif
                                        <a href="javascript:void(0);" data-id="{{ $data->group->id }}"
                                            data-user="{{ $data->group->name }}" title="Open course Chat"
                                            class="btn-view chat-toggle">Open</a>
                                    </ul>
                                @endif
                            </div>

                            <div class="section-divider"></div>
                            <div class="course-details-inner">
                                <div class="course-details-comments">
                                    <h3 class="sidebar-title">Student Grope</h3>
                                    <div class="media">
                                        <a href="#" class="pull-left">
                                            <img alt="Comments" src="{{ url('assets/oxford/img/logo.png') }}"
                                                width="50px" height="50px" class="media-object">
                                        </a>
                                        <div class="media-body" style="padding-top: 10px;">
                                            <h3><a href="#">Student name</a></h3>
                                            <h4>notes</h4>
                                            <p>Rimply dummy text of the printinwhen an unknown
                                                printer took eype and scramb relofeletog and
                                                typesetting industry. Lorem </p>
                                            <div class="replay-area">
                                                {{-- <ul>
                                                                            <li><i class="fa fa-star"
                                                                                    aria-hidden="true"></i></li>
                                                                            <li><i class="fa fa-star"
                                                                                    aria-hidden="true"></i></li>
                                                                            <li><i class="fa fa-star"
                                                                                    aria-hidden="true"></i></li>
                                                                            <li><i class="fa fa-star"
                                                                                    aria-hidden="true"></i></li>
                                                                            <li><i class="fa fa-star"
                                                                                    aria-hidden="true"></i></li>
                                                                        </ul> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="section-divider"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
