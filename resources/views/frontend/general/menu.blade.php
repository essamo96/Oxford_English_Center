<!-- Header Area Start Here -->
<header>
    <div id="header2" class="header2-area">
        <div class="header-top-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="header-top-left">
                            <ul>
                                <li><i class="fa fa-phone" aria-hidden="true"></i><a href="Tel:+{{$mysettings->mobile}}"> + {{$mysettings->mobile}} </a></li>
                                <li><i class="fa fa-envelope" aria-hidden="true"></i><a href="mailto:{{$mysettings->contact_email}}">{{$mysettings->contact_email}}</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-center">
                        <img src="{{ url('assets/oxford/img/OTE-Approved-Test-Centre-Logo.png') }}" alt="" style="max-width: 50%"/>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="header-top-right">
                            <ul class="navbar-nav">
                                <li>
                                    <div class="apply-btn-area">
                                        <a href="{{ url('book')}}" class="apply-now-btn">Apply Now</a>
                                    </div>
                                </li>

                                @if(!Auth::guard('students')->check() && !Auth::guard('teachers')->check())
                                    <li>
                                        <div class="apply-btn-area">
                                            <a href="{{ url('login')}}" class="apply-now-btn">Student Gate</a>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="apply-btn-area">
                                            <a href="{{ url('login/teacher')}}" class="apply-now-btn">Teacher Gate</a>
                                        </div>
                                    </li>
                                @else
                                    <li>
                                        <a class="login-btn-area open" href="{{ url('logout')}}"><i class="fa fa-lock" aria-hidden="true"></i> Logout</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-menu-area bg-textPrimary" id="sticker">
            <div class="container">
                <div class="row">
                    <div class="col-lg-1 col-md-1 col-sm-3">
                        <div class="logo-area">
                            @if(Auth::guard('students')->check())
                                <a href="{{ url('/student')}}">
                                    <img class="img-responsive student-profile-nav" src="{{ Auth::guard('students')->user()->image ? url(Auth::guard('students')->user()->image) : url('assets/oxford/img/students/avatar.png') }}" style="height: 58px; border-radius: 50%; object-fit: cover;" alt="student">
                                </a>
                            @elseif(Auth::guard('teachers')->check())
                                <a href="{{ url('/teacher')}}">
                                    <img class="img-responsive student-profile-nav" src="{{ Auth::guard('teachers')->user()->image ? url(Auth::guard('teachers')->user()->image) : url('assets/oxford/img/students/avatar.png') }}" style="height: 58px; border-radius: 50%; object-fit: cover;" alt="teacher">
                                </a>
                            @else
                                <a href="{{ url('/')}}"><img class="img-responsive" src="{{ url('assets/oxford/img/logo.png')}}" style="height: 58px;" alt="logo"></a>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-9">
                        <nav id="desktop-nav">
                            <ul>
                                @if(Auth::guard('students')->check())
                                    <!-- Student Dashboard Menu -->
                                    <li class="{{ Request::is('student') ? 'active' : '' }}"><a href="{{ Request::is('student') ? '#Welcome' : url('/student#Welcome') }}" @if(Request::is('student')) data-toggle="tab" @endif>Welcome</a></li>
                                    <li><a href="{{ Request::is('student') ? '#Courses' : url('/student#Courses') }}" @if(Request::is('student')) data-toggle="tab" @endif>Courses</a></li>
                                    <li><a href="{{ Request::is('student') ? '#Exam' : url('/student#Exam') }}" @if(Request::is('student')) data-toggle="tab" @endif>Exam Data</a></li>
                                    <li><a href="{{ Request::is('student') ? '#Teacher_Evaluations' : url('/student#Teacher_Evaluations') }}" @if(Request::is('student')) data-toggle="tab" @endif>Evaluations</a></li>
                                    <li><a href="{{ Request::is('student') ? '#Profile' : url('/student#Profile') }}" @if(Request::is('student')) data-toggle="tab" @endif>Profile</a></li>
                                    <li><a href="{{ Request::is('student') ? '#Password' : url('/student#Password') }}" @if(Request::is('student')) data-toggle="tab" @endif>Password</a></li>
                                    <li><a href="{{ url('/logout') }}">Logout</a></li>

                                @elseif(Auth::guard('teachers')->check())
                                    <!-- Teacher Dashboard Menu -->
                                    <li class="{{ Request::is('teacher') ? 'active' : '' }}"><a href="{{ Request::is('teacher') ? '#Welcome' : url('/teacher#Welcome') }}" @if(Request::is('teacher')) data-toggle="tab" @endif>Welcome</a></li>
                                    <li><a href="{{ Request::is('teacher') ? '#Courses' : url('/teacher#Courses') }}" @if(Request::is('teacher')) data-toggle="tab" @endif>Courses</a></li>
                                    <li class="{{ (Request::is('grope/Exam*') || Request::is('examDate*')) ? 'active' : '' }}"><a href="{{ route('teacher.ExamDates', Auth::guard('teachers')->user()->id) }}">Exam Schedule</a></li>
                                    <li class="{{ Request::is('progress*') ? 'active' : '' }}"><a href="{{ route('teacher.progress', Auth::guard('teachers')->user()->id) }}">Progress</a></li>
                                    <li><a href="{{ Request::is('teacher') ? '#Profile' : url('/teacher#Profile') }}" @if(Request::is('teacher')) data-toggle="tab" @endif>Profile</a></li>
                                    <li><a href="{{ Request::is('teacher') ? '#Password' : url('/teacher#Password') }}" @if(Request::is('teacher')) data-toggle="tab" @endif>Password</a></li>
                                    <li><a href="{{ url('/logout') }}">Logout</a></li>

                                @else
                                    <!-- Main Website Menu -->
                                    <li class="active"><a href="{{ url('/')}}">Home</a></li>
                                    <li><a href="#">About</a>
                                        <ul>
                                            <li><a href="{{ url('page/about')}}">About us</a></li>
                                            <li><a href="{{ url('page/promise')}}">Oxford Promise </a></li>
                                            <li><a href="{{ url('family')}}">Oxford Family</a></li>
                                            <li><a href="{{ url('page/methods')}}">Teaching Methods</a></li>
                                            <li><a href="{{ url('partners')}}">Partners </a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{{ url('test-of-english')}}">OXFORD TEST OF ENGLISH</a></li>
                                    <li><a href="#">Courses</a>
                                        <ul>
                                            <li><a href="{{ url('page/ielts')}}">IELTS Preparation Course</a></li>
                                            <li><a href="{{ url('page/general')}}">General English Levels</a></li>
                                            <li><a href="{{ url('page/speaking')}}">Speaking Course</a></li>
                                            <li><a href="{{ url('page/writing')}}">Academic Writing Course</a></li>
                                            <li><a href="{{ url('page/business')}}">Business English Course</a></li>
                                            <li><a href="{{ url('page/esp')}}">ESP Course</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{{ url('page/prize')}}">IELTS Prize </a></li>
                                    <li><a href="{{ url('community')}}">Community</a></li>
                                    <li><a href="#">Gallery</a>
                                        <ul>
                                            <li><a href="{{ url('photos')}}">Photos</a></li>
                                            <li><a href="{{ url('videos')}}">Videos</a></li>
                                            <li><a href="{{ url('certificates')}}">Certificates</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{{ url('jobs')}}">Jobs</a></li>
                                    <li><a href="{{ url('contact')}}">Contact Us</a></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <div class="col-lg-1 col-md-1 hidden-sm">
                        <div class="header-search">
                            <form>
                                <input type="text" class="search-form" placeholder="Search...." required="">
                                <a href="#" class="search-button" id="search-button"><i class="fa fa-search" aria-hidden="true"></i></a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mobile Menu Area Start -->
    <div class="mobile-menu-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="mobile-menu">
                        <nav id="dropdown">
                            <ul>
                                @if(Auth::guard('students')->check())
                                    <!-- Student Mobile Menu -->
                                    <li class="{{ Request::is('student') ? 'active' : '' }}"><a href="{{ Request::is('student') ? '#Welcome' : url('/student#Welcome') }}" @if(Request::is('student')) data-toggle="tab" @endif>Welcome</a></li>
                                    <li><a href="{{ Request::is('student') ? '#Courses' : url('/student#Courses') }}" @if(Request::is('student')) data-toggle="tab" @endif>Courses</a></li>
                                    <li><a href="{{ Request::is('student') ? '#Exam' : url('/student#Exam') }}" @if(Request::is('student')) data-toggle="tab" @endif>Exam Data</a></li>
                                    <li><a href="{{ Request::is('student') ? '#Teacher_Evaluations' : url('/student#Teacher_Evaluations') }}" @if(Request::is('student')) data-toggle="tab" @endif>Evaluations</a></li>
                                    <li><a href="{{ Request::is('student') ? '#Profile' : url('/student#Profile') }}" @if(Request::is('student')) data-toggle="tab" @endif>Profile</a></li>
                                    <li><a href="{{ Request::is('student') ? '#Password' : url('/student#Password') }}" @if(Request::is('student')) data-toggle="tab" @endif>Password</a></li>
                                    <li><a href="{{ url('/logout') }}">Logout</a></li>

                                @elseif(Auth::guard('teachers')->check())
                                    <!-- Teacher Mobile Menu -->
                                    <li class="{{ Request::is('teacher') ? 'active' : '' }}"><a href="{{ Request::is('teacher') ? '#Welcome' : url('/teacher#Welcome') }}" @if(Request::is('teacher')) data-toggle="tab" @endif>Welcome</a></li>
                                    <li><a href="{{ Request::is('teacher') ? '#Courses' : url('/teacher#Courses') }}" @if(Request::is('teacher')) data-toggle="tab" @endif>Courses</a></li>
                                    <li class="{{ (Request::is('grope/Exam*') || Request::is('examDate*')) ? 'active' : '' }}"><a href="{{ route('teacher.ExamDates', Auth::guard('teachers')->user()->id) }}">Exam Schedule</a></li>
                                    <li class="{{ Request::is('progress*') ? 'active' : '' }}"><a href="{{ route('teacher.progress', Auth::guard('teachers')->user()->id) }}">Progress</a></li>
                                    <li><a href="{{ Request::is('teacher') ? '#Profile' : url('/teacher#Profile') }}" @if(Request::is('teacher')) data-toggle="tab" @endif>Profile</a></li>
                                    <li><a href="{{ Request::is('teacher') ? '#Password' : url('/teacher#Password') }}" @if(Request::is('teacher')) data-toggle="tab" @endif>Password</a></li>
                                    <li><a href="{{ url('/logout') }}">Logout</a></li>

                                @else
                                    <!-- Main Website Mobile Menu -->
                                    <li class="active"><a href="{{ url('/')}}">Home</a></li>
                                    <li><a href="#">About Oxford</a>
                                        <ul>
                                            <li><a href="{{ url('page/about')}}">About us</a></li>
                                            <li><a href="{{ url('page/promise')}}">Oxford Promise </a></li>
                                            <li><a href="{{ url('family')}}">Oxford Family</a></li>
                                            <li><a href="{{ url('page/methods')}}">Teaching Methods</a></li>
                                            <li><a href="{{ url('partners')}}">Partners </a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{{ url('test-of-english')}}">OXFORD TEST OF ENGLISH</a></li>
                                    <li><a href="#">Our Courses</a>
                                        <ul>
                                            <li><a href="{{ url('page/ielts')}}">IELTS Preparation Course</a></li>
                                            <li><a href="{{ url('page/general')}}">General English Levels</a></li>
                                            <li><a href="{{ url('page/speaking')}}">Speaking Course</a></li>
                                            <li><a href="{{ url('page/writing')}}">Academic Writing Course</a></li>
                                            <li><a href="{{ url('page/business')}}">Business English Course</a></li>
                                            <li><a href="{{ url('page/esp')}}">ESP Course</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{{ url('page/prize')}}">Oxford IELTS Prize </a></li>
                                    <li><a href="{{ url('community')}}">Community</a></li>
                                    <li><a href="#">Gallery</a>
                                        <ul>
                                            <li><a href="{{ url('photos')}}">Photos</a></li>
                                            <li><a href="{{ url('videos')}}">Videos</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="{{ url('jobs')}}">Jobs</a></li>
                                    <li><a href="{{ url('contact')}}">Contact Us</a></li>
                                    <li><a href="{{ url('book')}}">Apply Now</a></li>
                                    @if(!Auth::guard('students')->check() && !Auth::guard('teachers')->check())
                                        <li>
                                            <a class="login-btn-area open" href="{{ url('login')}}"><i class="fa fa-lock" aria-hidden="true"></i> Login</a>
                                            <a class="login-btn-area open" href="{{ url('login/teacher')}}"><i class="fa fa-lock" aria-hidden="true"></i> Teacher Login</a>
                                        </li>
                                    @else
                                        <li>
                                            <a class="login-btn-area open" href="{{ url('logout')}}"><i class="fa fa-lock" aria-hidden="true"></i> Logout</a>
                                        </li>
                                    @endif
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mobile Menu Area End -->
</header>
