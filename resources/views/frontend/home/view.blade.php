@extends('frontend.layouts.master')
@section('title', optional($mysettings)->title ?? 'Oxford')
@section('content')
<!-- Slider 1 Area Start Here -->
<div class="slider1-area overlay-default">
    <div class="bend niceties preview-1">
        <div id="ensign-nivoslider-3" class="slides">
            @foreach($sliders as $item)
            <img src="{{ url($item->image)}}" alt="slider" title="#slider-direction-{{ $loop->iteration}}" />
            @endforeach
        </div>
        @foreach($sliders as $item)
        <div id="slider-direction-{{ $loop->iteration}}" class="t-cn slider-direction">
            <div class="slider-content s-tb slide-{{ $loop->iteration}}">
                <div class="title-container s-tb-c">
                    <div class="title1">{{ $item->title}}</div>
                    <p>
                        {!! $item->sub !!}
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<!-- Slider 1 Area End Here -->
<!-- About 2 Area Start Here -->
<div class="about2-area">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1 class="about-title">{{ optional($about)->title ?? 'About Us' }}</h1>
                <div class="about-sub-title">{!! optional($about)->details ?? '' !!}</div>
            </div>
            <div class="col-md-6">
                <div class="video-area2 overlay-video bg-common-style" style="background-image: url('{{ url('assets/oxford/img/banner/1.jpg')}}');">
                    <div class="video-content">
                        <a class="play-btn popup-youtube" href="{{ optional($about)->url ?? '#' }}"><i class="fa fa-play" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About 2 Area End Here -->
<div class="service-bottom-area section-padding">
    <div class="service-bottom-area-bg" style="background-image: url({{ optional($timetable)->banner ?? '' }})"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-lg-6 col-md-offset-6 col-lg-offset-6 col-sm-12 col-xs-12">
                <div class="service-list wow fadeIn" style="visibility: visible; animation-name: fadeIn;">
                    <div class="single-service">
                        <div class="service-icon-hexagon">
                            <div class="hex">
                                <div class="service-icon">
                                    <i class="fa {{ optional($timetable)->url ?? 'fa-clock-o' }}"></i>
                                </div>
                            </div>
                        </div>
                        <div class="service-details">
                            <h4>{{ optional($timetable)->title ?? 'Timetable' }}</h4>
                            {!! optional($timetable)->details ?? '' !!}
                        </div>
                    </div>
                    <div class="single-service">
                        <div class="service-icon-hexagon">
                            <div class="hex">
                                <div class="service-icon"><i class="fa {{ optional($teachers)->url ?? 'fa-users' }}"></i></div>
                            </div>
                        </div>
                        <div class="service-details">
                            <h4>{{ optional($teachers)->title ?? 'Teachers' }}</h4>
                            {!! optional($teachers)->details ?? '' !!}
                        </div>
                    </div>
                    <div class="single-service">
                        <div class="service-icon-hexagon">
                            <div class="hex">
                                <div class="service-icon"><i class="fa {{ optional($value)->url ?? 'fa-line-chart' }}"></i></div>
                            </div>
                        </div>
                        <div class="service-details">
                            <h4>{{ optional($value)->title ?? 'Value' }}</h4>
                            {!! optional($value)->details ?? '' !!}
                        </div>
                    </div>
                    <div class="single-service">
                        <div class="service-icon-hexagon">
                            <div class="hex">
                                <div class="service-icon"><i class="fa {{ optional($students)->url ?? 'fa-smile-o' }}"></i></div>
                            </div>
                        </div>
                        <div class="service-details">
                            <h4>{{ optional($students)->title ?? 'Students' }}</h4>
                            {!! optional($students)->details ?? '' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About 2 Area Start Here -->
<div class="about2-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeIn" data-wow-duration="2s" data-wow-delay=".1s">
                <div class="service-box2">
                    <div class="service-box-icon">
                        <a href="{{ url('/brochur.pdf')}}" target="_blank"><i class="fa fa-graduation-cap" aria-hidden="true"></i></a>
                    </div>
                    <h3><a href="{{ url('/brochur.pdf')}}" target="_blank">Our Brochure</a></h3>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeIn" data-wow-duration="2s" data-wow-delay=".4s">
                <div class="service-box2">
                    <div class="service-box-icon">
                        <a href="{{ url('exam')}}"><i class="fa fa-user" aria-hidden="true"></i></a>
                    </div>
                    <h3><a href="{{ url('exam')}}">Placement Test Booking</a></h3>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 wow fadeIn" data-wow-duration="2s" data-wow-delay=".7s">
                <div class="service-box2">
                    <div class="service-box-icon">
                        <a href="{{ url('book')}}"><i class="fa fa-book" aria-hidden="true"></i></a>
                    </div>
                    <h3><a href="{{ url('book')}}">Book A Course</a></h3>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About 2 Area End Here -->
<!-- Counter Area Start Here -->
<div class="counter-area bg-primary-deep" style="background-image: url('{{ url('assets/oxford/img/banner/nos.jpg')}}');">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 counter1-box wow fadeInUp" data-wow-duration=".5s" data-wow-delay=".20s">
                <h2 class="about-counter title-bar-counter" data-num="{{ optional($mysettings)->donars ?? 0 }}">{{ optional($mysettings)->donars ?? 0 }}</h2>
                <p>Total Training Hours</p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 counter1-box wow fadeInUp" data-wow-duration=".5s" data-wow-delay=".40s">
                <h2 class="about-counter title-bar-counter" data-num="{{ optional($mysettings)->clients ?? 0 }}">{{ optional($mysettings)->clients ?? 0 }}</h2>
                <p>Total Number of Courses</p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 counter1-box wow fadeInUp" data-wow-duration=".5s" data-wow-delay=".60s">
                <h2 class="about-counter title-bar-counter" data-num="{{ optional($mysettings)->happy ?? 0 }}">{{ optional($mysettings)->happy ?? 0 }}</h2>
                <p>Total Number of Students</p>
            </div>
            <!--            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 counter1-box wow fadeInUp" data-wow-duration=".5s" data-wow-delay=".80s">
                            <h2 class="about-counter title-bar-counter" data-num="{{$mysettings->tickects}}">{{$mysettings->tickects}}</h2>
                            <p>Total Students Passed IELTS</p>
                        </div>-->
        </div>
    </div>
</div>
<!-- Counter Area End Here -->
<!-- News and Event Area Start Here -->
<div class="news-event-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h2 class="title-default-left">Latest News</h2>
                @foreach($news as $item)
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 ">
                    <?php
                    $cimg = $item->thumb;
                    if (substr($cimg, 0, 1) != '/') {
                        $cimg = '/' . $cimg;
                    }
                    $nimg = 'assets/site/images/default.jpg';
                    $img = (File::exists(public_path() . $cimg)) ? $cimg : $nimg;
                    ?>
                    <div class="news-box">
                        <div class="news-img-holder">
                            <img src="{{ URL::to(Helper::get_image($img)) }}" class="img-responsive" alt="{{ str_replace('"','',$item->title)}}">
                            <ul class="news-date2">
                                <li><?= date('d M', strtotime($item->pub_date)) ?></li>
                                <li><?= date('Y', strtotime($item->pub_date)) ?></li>
                            </ul>
                        </div>
                        <h3 class="title-news-left-bold"><a href="{{ URL::to('posts/'.$item->id) }}">{{$item->title}}</a></h3>
                        <p>{{$item->sub}}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<!-- News and Event Area End Here -->
<!-- Students Say Area Start Here -->
<div class="students-say-area">
    <h2 class="title-default-center">What Our Students Say</h2>
    <div class="container">
        <div class="rc-carousel" data-loop="true" data-items="2" data-margin="30" data-autoplay="false" data-autoplay-timeout="10000" data-smart-speed="2000" data-dots="true" data-nav="false" data-nav-speed="false" data-r-x-small="1" data-r-x-small-nav="false" data-r-x-small-dots="true" data-r-x-medium="2" data-r-x-medium-nav="false" data-r-x-medium-dots="true" data-r-small="2" data-r-small-nav="false" data-r-small-dots="true" data-r-medium="2" data-r-medium-nav="false" data-r-medium-dots="true" data-r-large="2" data-r-large-nav="false" data-r-large-dots="true">
            @foreach($partners as $partner)            
            <div class="single-item">
                <div class="single-item-wrapper">
                    <div class="profile-img-wrapper">
                        <a href="#" class="profile-img">

                            <img class="profile-img-responsive img-circle" src="{{ url($partner->image)}}" alt="Testimonial"></a>
                    </div>
                    <div class="tlp-tm-content-wrapper">
                        <h3 class="item-title"><a href="#">{{ $partner->title}}</a></h3>
                        <span class="item-designation">{{ $partner->url}}</span>
                        <ul class="rating-wrapper">
                            <li><i class="fa fa-star" aria-hidden="true"></i></li>
                            <li><i class="fa fa-star" aria-hidden="true"></i></li>
                            <li><i class="fa fa-star" aria-hidden="true"></i></li>
                            <li><i class="fa fa-star" aria-hidden="true"></i></li>
                            <li><i class="fa fa-star" aria-hidden="true"></i></li>
                        </ul>
                        <div class="item-content">{{ $partner->descs}}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Students Say Area End Here -->
@stop