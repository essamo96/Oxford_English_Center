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
                                <div class="row-12" style="height: 363px;">
                                    <div class="sidebar-course-price col-3" style="width: 323px; HEIGHT: 200PX;">
                                        <a href="{{ $data->group->zoom }}"><span href="#"
                                                style=""class="enroll-btn">zoom</span></a>
                                                @if ($data->group->teacher_lib)
                                                    
                                                <span href="{{$data->group->teacher_lib}}" download="{{$data->group->name}}" style="" class="download-btn">Media</span>
                                                @elseif ($data->group->teacher_lib != $data->group->teacher_lib)
                                                <span href="{{$data->group->teacher_lib}}" download="{{$data->group->name}}" style="" class="download-btn">New Media </span>
                                                @else
                                               <a href="{{route('teacher.showGroueStudents',['group_id' => Crypt::encrypt($data->group_id),'teacher_id' => Crypt::encrypt($data->group->teacher_id)])}}"> <span  class="download-btn bi bi-people-fill Tstudent">Students</span></a>
                                                @endif
                                               <a href="{{ url('group/' . $data->group_id) }}"> <span  style="" class="enroll-btn">Marks</span></a>
                                               <a href="{{ url('group/attendance/' . $data->group_id) }}"> <span  style="" class="download-btn bi bi-calendar-check">Attendance sheet</span></a>
                                               
                                               <a href="{{ url('examDate/' . $data->group_id) }}"> <span  style="" class="enroll-btn">Exams</span></a>
                                                {{-- <span href="#" style="" class="download-btn">certificate</span> --}}
                                    </div>
                                    <img alt="Comments"
                                        @if ($data->group->image != null)
                                         src="{{ url($data->group->image) }}" 
                                        @else
                                        src="{{ url('assets/oxford/img/logo.png') }}" 
                                        @endif width="200px" height="200px"
                                    
                                        style="    position: relative;
                                                               top: -202px;
                                                               left: 519px;"
                                        class="media-object col-3">
                                </div>
                                <h3 class="sidebar-title" style="margin-top: 50px;">Course Features</h3>
                                @if ($data != null)
                                    
                                <ul class="course-feature">
                                    <li>Lecturer: {{$data->group->teacher->name}}</li>
                                    <li>Course Duration: 72 Hours</li>
                                    <li>Time: {{$data->group->ctime->times}}</li>
                                    <li>Days: {{$data->group->ctime->days}}</li>
                                    <li>End: {{ date('d F Y', strtotime($data->group->end_date)) }}</li>
                                    <li>Sessions Per Week: 3</li>
                                    <li>Numper Of Weeks: 12</li>
                                    <li>Total Classes : 36</li>
                                    <li>Start: {{ date('d F Y', strtotime($data->group->start_date)) }}</li>
                                </ul>
                                @endif
                            </div>

                            <div class="section-divider"></div>
                            <div class="course-details-inner">
                                <div class="course-details-comments">
                                    <h3 class="sidebar-title">Student Grope</h3>
                                    <div class="media" >
                                        <a href="#" class="pull-left">
                                            <img alt="Comments" src="{{ url('assets/oxford/img/logo.png') }}"
                                                width="50px" height="50px" class="media-object">
                                        </a>
                                        <div class="media-body" style="padding-top: 10px;">
                                            <h3><a href="#" >Student name</a></h3>
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
