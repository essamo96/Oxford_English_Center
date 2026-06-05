@extends('frontend.layouts.master')
@section('title', optional($mysettings)->title ?? 'Oxford')
@section('content')
<div class="ox-scope">

    {{-- ============================ HERO ============================ --}}
    <section class="ox-hero" data-hero data-hero-interval="6000">
        <div class="ox-hero__slides">
            @forelse($sliders as $item)
                <div class="ox-hero__slide {{ $loop->first ? 'is-active' : '' }}" style="background-image:url('{{ url($item->image) }}')"></div>
            @empty
                <div class="ox-hero__slide is-active" style="background-image:url('{{ url('assets/oxford/img/banner/1.jpg') }}')"></div>
            @endforelse
        </div>
        <div class="ox-hero__overlay"></div>
        <div class="ox-hero__particles" data-particles data-particles-color="rgba(255,255,255,"></div>
        <div class="ox-hero__shapes">
            <span class="ox-shape ox-shape--1"></span>
            <span class="ox-shape ox-shape--2"></span>
            <span class="ox-shape ox-shape--3"></span>
        </div>

        <div class="ox-container">
            <div class="ox-hero__inner">
                <span class="ox-hero__badge"><span class="dot"></span> Approved Oxford Test of English Centre</span>

                <div class="ox-hero__caps">
                    @forelse($sliders as $item)
                        <div class="ox-hero__cap {{ $loop->first ? 'is-active' : '' }}">
                            <h1>{{ $item->title }}</h1>
                            <div class="ox-hero__text">{!! $item->sub !!}</div>
                        </div>
                    @empty
                        <div class="ox-hero__cap is-active">
                            <h1>Speak English with <span class="hl">Real Confidence</span></h1>
                            <div class="ox-hero__text"><p>Premium English training — IELTS, general levels, business English and more.</p></div>
                        </div>
                    @endforelse
                </div>

                <div class="ox-hero__cta">
                    <a id="heroBookNow" class="ox-btn ox-btn--white ox-btn--lg" href="{{ url('book') }}"><i class="bi bi-mortarboard-fill"></i> Book a Course</a>
                    <a class="ox-btn ox-btn--outline-white ox-btn--lg" href="{{ url('contact') }}"><i class="bi bi-envelope"></i> Contact Us</a>
                </div>
            </div>
        </div>

        <div class="ox-scroll-ind"><span class="mouse"></span> Scroll</div>
    </section>

    {{-- ============================ ABOUT ============================ --}}
    <section class="ox-section">
        <div class="ox-container">
            <div class="ox-grid ox-grid--split">
                <div data-reveal="right">
                    <span class="ox-eyebrow">Who we are</span>
                    <h2 class="ox-title">{{ optional($about)->title ?? 'About Us' }}</h2>
                    <div class="ox-prose">{!! optional($about)->details ?? '' !!}</div>
                </div>
                <div data-reveal="left">
                    <div class="ox-media">
                        <img src="{{ url('assets/oxford/img/banner/1.jpg') }}" alt="{{ optional($about)->title ?? 'About' }}">
                        <a class="ox-media__play popup-youtube" href="{{ optional($about)->url ?? '#' }}" aria-label="Play video">
                            <span class="ox-play-btn"><i class="bi bi-play-fill"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ FEATURES ============================ --}}
    <section class="ox-section ox-bg-dots">
        <div class="ox-container">
            <div style="text-align:center;max-width:640px;margin:0 auto 48px" data-reveal>
                <span class="ox-eyebrow" style="justify-content:center">Why Oxford</span>
                <h2 class="ox-title ox-title--center">Everything you need to succeed</h2>
            </div>
            <div class="ox-grid ox-grid--4">
                <div class="ox-feature" data-reveal="up">
                    <div class="ox-feature__icon"><i class="fa {{ optional($timetable)->url ?? 'fa-clock-o' }}"></i></div>
                    <h4>{{ optional($timetable)->title ?? 'Timetable' }}</h4>
                    <div>{!! optional($timetable)->details ?? '' !!}</div>
                </div>
                <div class="ox-feature" data-reveal="up" data-reveal-delay=".1s">
                    <div class="ox-feature__icon"><i class="fa {{ optional($teachers)->url ?? 'fa-users' }}"></i></div>
                    <h4>{{ optional($teachers)->title ?? 'Teachers' }}</h4>
                    <div>{!! optional($teachers)->details ?? '' !!}</div>
                </div>
                <div class="ox-feature" data-reveal="up" data-reveal-delay=".2s">
                    <div class="ox-feature__icon"><i class="fa {{ optional($value)->url ?? 'fa-line-chart' }}"></i></div>
                    <h4>{{ optional($value)->title ?? 'Value' }}</h4>
                    <div>{!! optional($value)->details ?? '' !!}</div>
                </div>
                <div class="ox-feature" data-reveal="up" data-reveal-delay=".3s">
                    <div class="ox-feature__icon"><i class="fa {{ optional($students)->url ?? 'fa-smile-o' }}"></i></div>
                    <h4>{{ optional($students)->title ?? 'Students' }}</h4>
                    <div>{!! optional($students)->details ?? '' !!}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ QUICK ACTIONS ============================ --}}
    <section class="ox-section ox-section--tight">
        <div class="ox-container">
            <div class="ox-grid ox-grid--3">
                <a class="ox-action" href="{{ url('/brochur.pdf') }}" target="_blank" data-reveal="up">
                    <span class="ox-action__icon"><i class="bi bi-file-earmark-pdf"></i></span>
                    <div><h3>Our Brochure</h3><span>Download the full programme</span></div>
                </a>
                <a class="ox-action" href="{{ url('exam') }}" data-reveal="up" data-reveal-delay=".1s">
                    <span class="ox-action__icon"><i class="bi bi-clipboard-check"></i></span>
                    <div><h3>Placement Test Booking</h3><span>Find your level in minutes</span></div>
                </a>
                <a class="ox-action" href="{{ url('book') }}" data-reveal="up" data-reveal-delay=".2s">
                    <span class="ox-action__icon"><i class="bi bi-journal-bookmark-fill"></i></span>
                    <div><h3>Book A Course</h3><span>Reserve your seat today</span></div>
                </a>
            </div>
        </div>
    </section>

    {{-- ============================ STATS ============================ --}}
    <section class="ox-section">
        <div class="ox-container">
            <div class="ox-stats-band" data-reveal="zoom">
                <div class="ox-stats-band__inner">
                    <div class="ox-grid ox-grid--3">
                        <div class="ox-stat">
                            <div class="ox-stat__num" data-count="{{ optional($mysettings)->donars ?? 0 }}" data-suffix="+">0</div>
                            <div class="ox-stat__label">Total Training Hours</div>
                        </div>
                        <div class="ox-stat">
                            <div class="ox-stat__num" data-count="{{ optional($mysettings)->clients ?? 0 }}" data-suffix="+">0</div>
                            <div class="ox-stat__label">Total Number of Courses</div>
                        </div>
                        <div class="ox-stat">
                            <div class="ox-stat__num" data-count="{{ optional($mysettings)->happy ?? 0 }}" data-suffix="+">0</div>
                            <div class="ox-stat__label">Total Number of Students</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ LATEST NEWS ============================ --}}
    <section class="ox-section ox-bg-soft">
        <div class="ox-container">
            <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:24px;margin-bottom:48px;flex-wrap:wrap" data-reveal>
                <div><span class="ox-eyebrow">From the centre</span><h2 class="ox-title" style="margin:0">Latest News</h2></div>
            </div>
            <div class="ox-grid ox-grid--3">
                @foreach($news as $item)
                    @php
                        $cimg = $item->thumb;
                        if (substr($cimg, 0, 1) != '/') { $cimg = '/' . $cimg; }
                        $nimg = 'assets/site/images/default.jpg';
                        $img = (File::exists(public_path() . $cimg)) ? $cimg : $nimg;
                    @endphp
                    <article class="ox-card" data-reveal="up" data-reveal-delay="{{ $loop->index * 0.1 }}s">
                        <div class="ox-card__media">
                            <img src="{{ URL::to(Helper::get_image($img)) }}" alt="{{ str_replace('"','',$item->title) }}">
                            <span class="ox-datechip">
                                <b>{{ date('d', strtotime($item->pub_date)) }}</b>
                                <span>{{ date('M Y', strtotime($item->pub_date)) }}</span>
                            </span>
                        </div>
                        <div class="ox-card__body">
                            <h3 class="ox-card__title"><a href="{{ URL::to('posts/'.$item->id) }}">{{ $item->title }}</a></h3>
                            <p class="ox-card__text">{{ $item->sub }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================ TESTIMONIALS ============================ --}}
    <section class="ox-section">
        <div class="ox-container">
            <div style="text-align:center;max-width:640px;margin:0 auto 48px" data-reveal>
                <span class="ox-eyebrow" style="justify-content:center">Testimonials</span>
                <h2 class="ox-title ox-title--center">What Our Students Say</h2>
            </div>
            <div class="ox-carousel" data-carousel data-reveal>
                <div class="ox-carousel__track" data-carousel-track>
                    @foreach($partners as $partner)
                        <div class="ox-quote">
                            <div class="ox-quote__stars">★★★★★</div>
                            <p class="ox-quote__text">{{ $partner->descs }}</p>
                            <div class="ox-quote__person">
                                <img src="{{ url($partner->image) }}" alt="{{ $partner->title }}">
                                <div><b>{{ $partner->title }}</b><span>{{ $partner->url }}</span></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="ox-carousel__nav">
                    <button class="ox-carousel__btn" data-carousel-prev aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                    <div class="ox-dots" data-carousel-dots></div>
                    <button class="ox-carousel__btn" data-carousel-next aria-label="Next"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </section>

</div>
@stop
