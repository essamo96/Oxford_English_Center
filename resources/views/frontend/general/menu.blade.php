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


                                <?php
                                $t = Auth::guard('teachers');
                                $s = Auth::guard('students');
                                if (!$s->check() && !$t->check()) {
                                    ?>
                                    {{-- <li>
                                        <a class="login-btn-area open" href="{{ url('login')}}"><i class="fa fa-lock" aria-hidden="true"></i> Login</a>
                                    </li> --}}

                                    <li>
                                        <div class="apply-btn-area">
                                            <a href="{{ url('login')}}" class="apply-now-btn">student Gate</a>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="apply-btn-area">
                                            <a href="{{ url('login/teacher')}}" class="apply-now-btn">teacher Gate</a>
                                        </div>
                                    </li>

                                <?php } else { ?>
                                    <li>
                                        <a class="login-btn-area open" href="{{ url('logout')}}"><i class="fa fa-lock" aria-hidden="true"></i> Logout</a>
                                    </li>
                                <?php } ?>
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
                            <a href="{{ url('/')}}"><img class="img-responsive" src="{{ url('assets/oxford/img/logo.png')}}" style="height: 58px;" alt="logo"></a>
                        </div>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-9">
                        <nav id="desktop-nav">
                            <ul>
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
                                {{-- <li><a href="{{ url('Certificates')}}">Certificates</a></li> --}}
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
    <!-- Mobile Menu Area Start                                                                                                                          -->
    <div class="mobile-menu-area">

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="mobile-menu">
                        <nav id="dropdown">

                            <ul>
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
                                <?php
                                $t = Auth::guard('teachers');
                                $s = Auth::guard('students');
                                if (!$s->check() && !$t->check()) {
                                    ?>
                                    <li>
                                        <a class="login-btn-area open" href="{{ url('login')}}"><i class="fa fa-lock" aria-hidden="true"></i> Login</a>
                                        <a class="login-btn-area open" href="{{ url('login/teacher')}}"><i class="fa fa-lock" aria-hidden="true"></i>teacher Login</a>
                                    </li>
                                <?php } else { ?>
                                    <li>
                                        <a class="login-btn-area open" href="{{ url('logout')}}"><i class="fa fa-lock" aria-hidden="true"></i> Logout</a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mobile Menu Area End -->
</header>
