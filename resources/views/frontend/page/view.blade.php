@extends('frontend.layouts.master')
@section('title', $page->title)
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url($page->banner?$page->banner:'assets/oxford/img/banner/1.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>{{$page->title}}</h1>
            <ul>
                <li><a href="#">Home</a> - </li>
                <li>{{$page->title}}</li>
            </ul>
        </div>
    </div>
</div>
<div class="about-page1-area">
    <div class="container">
        <div class="row about-page1-inner">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="about-page-content-holder">
                    <div class="content-box">
                        <h2>{{$page->title}}</h2>
                        {!!$page->details!!}
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="about-page-img-holder">
                    <img src="{{url('uploads/image/'.$page->image)}}" class="img-responsive" alt="about">
                </div>
                <div>
                    @if($page->age)
                    <div class="course-details-inner">
                        <h3 class="sidebar-title">Course Features</h3>
                        <ul class="course-feature">
                            @if($page->price)
                            <li><b>Price</b>: {{ $page->price }}</li>
                            @endif
                            @if($page->fees)
                            <li><b>Book Fees</b>: {{ $page->fees }}</li>
                            @endif
                            @if($page->age)
                            <li><b>Age Range</b>: {{ $page->age }}</li>
                            @endif
                            @if($page->level)
                            <li><b>Level</b>: {{ $page->level }}</li>
                            @endif
                            @if($page->weeks)
                            <li><b>Weeks</b>: {{ $page->weeks }}</li>
                            @endif
                            @if($page->hours)
                            <li><b>Hours</b>: {{ $page->hours }}</li>
                            @endif
                            @if($page->mock)
                            <li><b>Mocks Exam</b>: {{ $page->mock }}</li>
                            @endif
                            @if($page->duration)
                            <li><b>Duration</b>: {{ $page->duration }}</li>
                            @endif
                            @if($page->class_size)
                            <li><b>Class Size</b>: {{ $page->class_size }}</li>
                            @endif

                            @if($page->start)
                            <li><b>Start</b>: {{ $page->start }}</li>
                            @endif

                            @if($page->days)
                            <li><b>Days</b>: {{ $page->days }}</li>
                            @endif
                            @if($page->time)
                            <li><b>Time</b>: {{ $page->time }}</li>
                            @endif
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--END CONTENT ABOUT-->
@stop