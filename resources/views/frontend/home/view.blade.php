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
                
                {{-- Dynamic Program Brochures --}}
                @foreach($programsWithBrochures as $prog)
                <div class="ox-action-wrapper" style="position: relative; display: block;" data-reveal="up" data-reveal-delay=".3s">
                    <a class="ox-action" href="{{ route('brochure.show', \Illuminate\Support\Facades\Crypt::encrypt($prog->id)) }}" style="padding-right: 60px;">
                        <span class="ox-action__icon"><i class="bi bi-file-earmark-pdf-fill"></i></span>
                        <div>
                            <h3>{{ $prog->title }}</h3>
                            <span>تحميل بروشور البرنامج</span>
                        </div>
                    </a>
                    <button type="button" class="ox-qr-btn" onclick="event.preventDefault(); event.stopPropagation(); showQrModal('{{ Crypt::encrypt($prog->id) }}')" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: #1e3a5f; color: white; border: none; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; z-index: 2; box-shadow: 0 4px 10px rgba(0,0,0,0.1);" title="مشاركة عبر QR Code">
                        <i class="bi bi-qr-code"></i>
                    </button>
                </div>
                @endforeach
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

    {{-- QR Code Modal --}}
    <div id="oxQrModal" class="ox-modal" style="display: none;">
        <div class="ox-modal__backdrop" onclick="closeQrModal()"></div>
        <div class="ox-modal__content">
            <button class="ox-modal__close" onclick="closeQrModal()">&times;</button>
            <div class="ox-modal__header">
                <h3>مشاركة البروشور</h3>
                <p id="qrProgramTitle" style="color: #666; font-size: 17.2px; margin-top: 5px;"></p>
            </div>
            <div class="ox-modal__body" style="text-align: center; padding: 30px;">
                <div id="qrLoading" style="display: none;">
                    <i class="bi bi-arrow-repeat" style="font-size: 35.2px; animation: spin 1s linear infinite; color: #1e3a5f;"></i>
                </div>
                <div id="qrSvgContainer" style="position: relative; display: inline-block; padding: 15px; background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #eee;">
                    <!-- SVG gets injected here -->
                </div>
            </div>
            <div class="ox-modal__footer" style="display: flex; gap: 10px; justify-content: center; padding: 20px; border-top: 1px solid #eee;">
                <button onclick="copyQrUrl()" class="ox-btn" style="flex: 1; background: #f8f9fa; color: #1e3a5f; border: 2px solid #eee; display: flex; align-items: center; justify-content: center; gap: 8px;"><i class="bi bi-link-45deg"></i> نسخ الرابط</button>
                <button onclick="downloadQrImage()" class="ox-btn" style="flex: 1; background: #28a745; color: white; border: none; display: flex; align-items: center; justify-content: center; gap: 8px;"><i class="bi bi-download"></i> تحميل الصورة</button>
            </div>
        </div>
    </div>
    <style>
        .ox-modal { position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .ox-modal__backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(5px); }
        .ox-modal__content { position: relative; background: #fff; width: 100%; max-width: 400px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); animation: oxModalIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); overflow: hidden; }
        .ox-modal__header { padding: 25px 25px 15px; text-align: center; border-bottom: 1px solid #f0f0f0; }
        .ox-modal__header h3 { margin: 0; color: #1e3a5f; font-size: 23.2px; font-weight: 700; }
        .ox-modal__close { position: absolute; top: 15px; right: 15px; width: 30px; height: 30px; border: none; background: #f8f9fa; border-radius: 50%; font-size: 23.2px; color: #666; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
        .ox-modal__close:hover { background: #fee; color: #e74c3c; }
        @keyframes oxModalIn { from { opacity: 0; transform: translateY(30px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        .ox-qr-btn:hover { background: #f39c12 !important; transform: translateY(-50%) scale(1.1) !important; }
        #qrSvgContainer svg { width: 260px !important; height: 260px !important; display: block; margin: 0 auto; }
    </style>
    <script>
        let currentBrochureUrl = '';

        function showQrModal(encryptedId) {
            document.getElementById('oxQrModal').style.display = 'flex';
            document.getElementById('qrLoading').style.display = 'block';
            document.getElementById('qrSvgContainer').style.visibility = 'hidden';
            
            fetch("{{ url('brochure') }}/" + encryptedId + "/qr", {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('qrLoading').style.display = 'none';
                if (data.status === 'success') {
                    document.getElementById('qrProgramTitle').innerText = data.title;
                    document.getElementById('qrSvgContainer').innerHTML = data.qr_svg + `
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 45px; height: 45px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 5px;">
                        <img src="{{ asset('assets/oxford/img/logo.png') }}" style="width: 100%; height: auto;" alt="Oxford">
                    </div>`;
                    document.getElementById('qrSvgContainer').style.visibility = 'visible';
                    currentBrochureUrl = data.url;
                } else {
                    alert(data.message || 'حدث خطأ أثناء تحميل QR');
                    closeQrModal();
                }
            })
            .catch(err => {
                console.error(err);
                alert('حدث خطأ في الاتصال');
                closeQrModal();
            });
        }

        function closeQrModal() {
            document.getElementById('oxQrModal').style.display = 'none';
        }

        function copyQrUrl() {
            if(currentBrochureUrl) {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(currentBrochureUrl).then(() => {
                        alert('تم نسخ الرابط بنجاح!');
                    });
                } else {
                    var dummy = document.createElement("textarea");
                    document.body.appendChild(dummy);
                    dummy.value = currentBrochureUrl;
                    dummy.select();
                    document.execCommand("copy");
                    document.body.removeChild(dummy);
                    alert('تم نسخ الرابط بنجاح!');
                }
            }
        }

        function downloadQrImage() {
            var container = document.getElementById('qrSvgContainer');
            var svgElement = container.querySelector('svg');
            if(!svgElement) return;

            var svgData = new XMLSerializer().serializeToString(svgElement);
            var canvas = document.createElement("canvas");
            canvas.width = 300;
            canvas.height = 300;
            var ctx = canvas.getContext("2d");

            ctx.fillStyle = "white";
            ctx.fillRect(0, 0, 300, 300);

            var img = new Image();
            img.onload = function() {
                ctx.drawImage(img, 0, 0, 300, 300);

                var logo = new Image();
                logo.crossOrigin = "Anonymous";
                logo.src = "{{ asset('assets/oxford/img/logo.png') }}";
                logo.onload = function() {
                    // Draw white background for logo
                    ctx.fillStyle = "white";
                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(120, 120, 60, 60, 10);
                    } else {
                        ctx.arc(150, 150, 30, 0, 2 * Math.PI);
                    }
                    ctx.fill();
                    
                    ctx.drawImage(logo, 125, 125, 50, 50);
                    
                    var url = canvas.toDataURL("image/png");
                    var link = document.createElement("a");
                    var title = document.getElementById('qrProgramTitle').innerText || 'brochure';
                    link.download = title + "_QR.png";
                    link.href = url;
                    link.click();
                };
                logo.onerror = function() {
                    var url = canvas.toDataURL("image/png");
                    var link = document.createElement("a");
                    var title = document.getElementById('qrProgramTitle').innerText || 'brochure';
                    link.download = title + "_QR.png";
                    link.href = url;
                    link.click();
                };
            };
            img.src = "data:image/svg+xml;base64," + btoa(unescape(encodeURIComponent(svgData)));
        }
    </script>
</div>
@stop
