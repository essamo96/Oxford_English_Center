@extends('frontend.layouts.master')
@section('title', $page->title)
@section('content')
<div class="ox-scope">

    {{-- ---------- Page banner + breadcrumb ---------- --}}
    <section class="ox-pagehero" style="background-image:url('{{ url($page->banner ? $page->banner : 'assets/oxford/img/banner/1.jpg') }}')">
        <div class="ox-pagehero__shapes">
            <span class="ox-shape ox-shape--2"></span>
            <span class="ox-shape ox-shape--3"></span>
        </div>
        <div class="ox-container ox-pagehero__inner" data-reveal="fade">
            <h1>{{ $page->title }}</h1>
            <ul class="ox-breadcrumb">
                <li><a href="{{ url('/') }}"><i class="bi bi-house-door-fill"></i> Home</a></li>
                <li>{{ $page->title }}</li>
            </ul>
        </div>
    </section>

    {{-- ---------- Content ---------- --}}
    <section class="ox-section">
        <div class="ox-container">
            <div class="ox-grid ox-grid--split" style="align-items:start">

                <div data-reveal="right">
                    <span class="ox-eyebrow">{{ $page->title }}</span>
                    <h2 class="ox-title">{{ $page->title }}</h2>
                    <div class="ox-prose">
                        {!! $page->details !!}
                    </div>
                </div>

                <div data-reveal="left">
                    @if($page->image)
                        <div class="ox-media" style="aspect-ratio:auto;margin-bottom:24px">
                            <img src="{{ url($page->image) }}" alt="{{ $page->title }}">
                        </div>
                    @endif

                    @if($page->age)
                        <div class="ox-sidecard">
                            <div class="ox-sidecard__head"><i class="bi bi-stars"></i> Course Features</div>
                            <ul class="ox-featurelist">
                                @if($page->price)<li><b>Price</b><span>{{ $page->price }}</span></li>@endif
                                @if($page->fees)<li><b>Book Fees</b><span>{{ $page->fees }}</span></li>@endif
                                @if($page->age)<li><b>Age Range</b><span>{{ $page->age }}</span></li>@endif
                                @if($page->level)<li><b>Level</b><span>{{ $page->level }}</span></li>@endif
                                @if($page->weeks)<li><b>Weeks</b><span>{{ $page->weeks }}</span></li>@endif
                                @if($page->hours)<li><b>Hours</b><span>{{ $page->hours }}</span></li>@endif
                                @if($page->mock)<li><b>Mocks Exam</b><span>{{ $page->mock }}</span></li>@endif
                                @if($page->duration)<li><b>Duration</b><span>{{ $page->duration }}</span></li>@endif
                                @if($page->class_size)<li><b>Class Size</b><span>{{ $page->class_size }}</span></li>@endif
                                @if($page->start)<li><b>Start</b><span>{{ $page->start }}</span></li>@endif
                                @if($page->days)<li><b>Days</b><span>{{ $page->days }}</span></li>@endif
                                @if($page->time)<li><b>Time</b><span>{{ $page->time }}</span></li>@endif
                            </ul>
                            <div style="padding:0 24px 24px">
                                <a class="ox-btn ox-btn--primary ox-btn--block" href="{{ url('book') }}"><i class="bi bi-journal-bookmark-fill"></i> Book this Course</a>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

</div>
@stop
