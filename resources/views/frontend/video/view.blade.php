@extends('frontend.layouts.master')
@section('title', 'معرض الفيديوهات')

@section('css')
<style>
:root{
  --vid-primary:#0F4C81;
  --vid-teal:#2C9AB7;
  --vid-gold:#F7B733;
  --vid-red:#E53935;
  --vid-dark:#060b14;
}

/* ── BANNER ─────────────────────────────────────────────────── */
.vid-banner{
  position:relative;min-height:340px;display:flex;align-items:center;
  background-size:cover;background-position:center;overflow:hidden;
}
.vid-banner::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,
    rgba(6,11,20,.88) 0%,
    rgba(15,76,129,.75) 55%,
    rgba(44,154,183,.55) 100%);
}
.vid-banner__shapes{position:absolute;inset:0;pointer-events:none;}
.vid-banner__shapes span{
  position:absolute;border-radius:50%;
  background:var(--vid-gold);opacity:.07;
}
.vid-banner__shapes span:nth-child(1){width:320px;height:320px;top:-90px;right:-50px;}
.vid-banner__shapes span:nth-child(2){width:180px;height:180px;bottom:-50px;left:18%;background:var(--vid-teal);opacity:.1;}
.vid-banner__shapes span:nth-child(3){
  width:200px;height:200px;top:20%;right:8%;
  background:none;border:2px solid rgba(247,183,51,.12);
}
.vid-banner__content{position:relative;z-index:2;padding:72px 0 52px;width:100%;}

.vid-breadcrumb{
  display:flex;align-items:center;gap:8px;
  color:rgba(255,255,255,.45);font-size:.82rem;margin-bottom:14px;
}
.vid-breadcrumb a{color:rgba(255,255,255,.7);text-decoration:none;transition:color .2s;}
.vid-breadcrumb a:hover{color:var(--vid-gold);}
.vid-breadcrumb i{font-size:.7rem;color:rgba(255,255,255,.3);}

.vid-eyebrow{
  display:inline-flex;align-items:center;gap:7px;
  background:rgba(229,57,53,.15);border:1px solid rgba(229,57,53,.3);
  color:#ff6659;font-size:.78rem;font-weight:700;
  letter-spacing:.07em;text-transform:uppercase;
  padding:4px 13px;border-radius:999px;margin-bottom:14px;
}
.vid-eyebrow i{color:#ff6659;}
.vid-banner h1{
  font-family:'Cairo',sans-serif;
  font-size:clamp(1.8rem,3.5vw,2.8rem);
  font-weight:900;color:#fff;line-height:1.15;margin:0 0 10px;
}
.vid-banner h1 em{font-style:normal;color:var(--vid-gold);}
.vid-banner__sub{color:rgba(255,255,255,.62);font-size:.95rem;margin:0;}
.vid-banner__meta{
  display:flex;align-items:center;gap:20px;flex-wrap:wrap;
  color:rgba(255,255,255,.55);font-size:.82rem;margin-top:16px;
}
.vid-banner__meta span{display:flex;align-items:center;gap:6px;}
.vid-banner__meta span i{color:var(--vid-teal);}

/* ── SECTION ─────────────────────────────────────────────────── */
.vid-section{padding:60px 0 76px;background:#f8fafc;}

.vid-section__head{
  display:flex;align-items:flex-end;justify-content:space-between;
  flex-wrap:wrap;gap:12px;margin-bottom:36px;
}
.vid-section__title-block .eyebrow{
  font-size:.76rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
  color:var(--vid-teal);display:flex;align-items:center;gap:8px;margin-bottom:8px;
}
.vid-section__title-block .eyebrow::before{
  content:'';display:block;width:22px;height:2px;
  background:var(--vid-teal);border-radius:999px;
}
.vid-section__title-block h2{
  font-family:'Cairo',sans-serif;
  font-size:clamp(1.5rem,2.2vw,2rem);font-weight:900;
  color:var(--vid-primary);margin:0;
}

/* ── VIDEO GRID ──────────────────────────────────────────────── */
.vid-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
  gap:24px;
}

.vid-card{
  background:#fff;border-radius:16px;overflow:hidden;
  border:1.5px solid #e8edf4;
  transition:transform .35s cubic-bezier(.175,.885,.32,1.275),
             box-shadow .35s ease,border-color .25s;
  will-change:transform;
  cursor:pointer;
}
.vid-card:hover{
  transform:translateY(-6px) scale(1.01);
  box-shadow:0 22px 56px -10px rgba(229,57,53,.18);
  border-color:rgba(229,57,53,.25);
}

/* Thumb */
.vid-card__thumb{
  position:relative;aspect-ratio:16/9;overflow:hidden;
  background:linear-gradient(135deg,#1a1a2e,#0d0d0d);
}
.vid-card__thumb img{
  width:100%;height:100%;object-fit:cover;
  transition:transform .5s ease,filter .3s;
  filter:brightness(.92);
}
.vid-card:hover .vid-card__thumb img{
  transform:scale(1.06);filter:brightness(.72);
}

/* Play btn */
.vid-card__play{
  position:absolute;inset:0;
  display:flex;align-items:center;justify-content:center;
  pointer-events:none;
}
.vid-card__play-ring{
  width:58px;height:58px;border-radius:50%;
  background:rgba(229,57,53,.88);
  display:flex;align-items:center;justify-content:center;
  transition:transform .3s ease,box-shadow .3s;
  box-shadow:0 0 0 0 rgba(229,57,53,.4);
}
.vid-card:hover .vid-card__play-ring{
  transform:scale(1.14);
  box-shadow:0 0 0 14px rgba(229,57,53,.15);
}
.vid-card__play-ring i{
  font-size:1.4rem;color:#fff;
  margin-right:-2px;
}

/* Duration / YT badge */
.vid-card__yt-badge{
  position:absolute;top:12px;left:12px;
  background:rgba(229,57,53,.9);
  color:#fff;font-size:.72rem;font-weight:800;
  padding:3px 10px;border-radius:999px;
  display:flex;align-items:center;gap:5px;
}
.vid-card__yt-badge i{font-size:.85rem;}

/* Hover gradient */
.vid-card__shade{
  position:absolute;inset:0;
  background:linear-gradient(to top,
    rgba(229,57,53,.55) 0%,rgba(0,0,0,0) 50%);
  opacity:0;transition:opacity .3s;
}
.vid-card:hover .vid-card__shade{opacity:1;}

/* Body */
.vid-card__body{padding:16px 18px 18px;}
.vid-card__title{
  font-family:'Cairo',sans-serif;
  font-size:.98rem;font-weight:800;color:var(--vid-primary);
  margin:0 0 10px;
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
  line-height:1.45;
}
.vid-card__footer{
  display:flex;align-items:center;justify-content:space-between;
  padding-top:10px;border-top:1px solid #f0f4f8;
}
.vid-card__yt-link{
  display:inline-flex;align-items:center;gap:6px;
  color:var(--vid-red);font-size:.78rem;font-weight:700;text-decoration:none;
  transition:color .2s;
}
.vid-card__yt-link:hover{color:#c62828;}
.vid-card__watch{
  display:inline-flex;align-items:center;gap:6px;
  background:var(--vid-primary);color:#fff;
  font-size:.75rem;font-weight:700;
  padding:5px 14px;border-radius:999px;
  border:none;cursor:pointer;transition:background .2s;
}
.vid-card__watch:hover{background:#0d4373;}

/* ── MODAL ───────────────────────────────────────────────────── */
.vm-modal{
  display:none;position:fixed;inset:0;z-index:99999;
  background:rgba(6,11,20,.96);
  backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
  align-items:center;justify-content:center;padding:20px;
}
.vm-modal.is-open{display:flex;}
.vm-modal__wrap{position:relative;width:min(96vw,960px);}
.vm-modal__close{
  position:absolute;top:-44px;left:0;
  background:rgba(255,255,255,.12);border:none;color:#fff;
  width:40px;height:40px;border-radius:50%;
  font-size:1.1rem;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  transition:background .2s;
}
.vm-modal__close:hover{background:rgba(255,255,255,.22);}
.vm-modal__iframe{
  width:100%;aspect-ratio:16/9;border-radius:12px;border:none;
  box-shadow:0 40px 80px rgba(0,0,0,.7);
}

/* ── EMPTY ───────────────────────────────────────────────────── */
.vid-empty{text-align:center;padding:80px 20px;color:#94a3b8;}
.vid-empty i{font-size:4rem;display:block;margin-bottom:18px;opacity:.35;color:var(--vid-red);}

/* ── PAGINATION ──────────────────────────────────────────────── */
.vid-pagination{margin-top:48px;display:flex;justify-content:center;}
.vid-pagination .pagination .page-link{
  border-radius:8px!important;margin:0 3px;
  border-color:#e2e8f0;color:var(--vid-primary);
  font-weight:600;padding:8px 16px;
}
.vid-pagination .pagination .page-item.active .page-link{
  background:var(--vid-primary);border-color:var(--vid-primary);color:#fff;
}

/* ── REVEAL ──────────────────────────────────────────────────── */
.vid-reveal{opacity:0;transform:translateY(26px);transition:opacity .5s ease,transform .5s ease;}
.vid-reveal.is-visible{opacity:1;transform:translateY(0);}
</style>
@endsection

@section('content')

{{-- ══════════════════════════════ BANNER ════ --}}
<div class="vid-banner"
     style="background-image:url('{{ url('assets/oxford/img/banner/gallary.jpg') }}')">
  <div class="vid-banner__shapes">
    <span></span><span></span><span></span>
  </div>
  <div class="container">
    <div class="vid-banner__content">

      <div class="vid-breadcrumb">
        <a href="{{ url('/') }}">Home</a>
        <i class="bi bi-chevron-left"></i>
        <span>Video Gallery</span>
      </div>

      <div class="vid-eyebrow">
        <i class="bi bi-play-circle-fill"></i>
        Video Gallery
      </div>

      <h1>Video <em>Gallery</em></h1>
      <p class="vid-banner__sub">Watch our featured videos and memorable visual moments</p>

      <div class="vid-banner__meta">
        <span>
          <i class="bi bi-collection-play"></i>
          {{ $videos->total() }} Videos
        </span>
        <span>
          <i class="bi bi-youtube"></i>
          YouTube
        </span>
        <span>
          <i class="bi bi-play-btn"></i>
          Click to watch
        </span>
      </div>

    </div>
  </div>
</div>

{{-- ══════════════════════════════ VIDEOS ════ --}}
<section class="vid-section">
  <div class="container">

    <div class="vid-section__head vid-reveal">
      <div class="vid-section__title-block">
        <div class="eyebrow"><span></span>Video Gallery</div>
        <h2>Latest Videos</h2>
      </div>
      <span style="font-size:.82rem;color:#94a3b8;">
        <i class="bi bi-info-circle" style="color:var(--vid-teal);"></i>
        Click any video to watch
      </span>
    </div>

    @if($videos->count() > 0)

      <div class="vid-grid">
        @foreach($videos as $i => $video)
        @php
          preg_match('/(?:v=|youtu\.be\/|embed\/)([^&\?\s]+)/', $video->url ?? '', $m);
          $ytId = $m[1] ?? '';
          $embedUrl = $ytId ? 'https://www.youtube.com/embed/'.$ytId.'?autoplay=1&rel=0' : '';
          $thumbHd  = $ytId ? 'https://img.youtube.com/vi/'.$ytId.'/maxresdefault.jpg' : '';
          $thumbSd  = $ytId ? 'https://img.youtube.com/vi/'.$ytId.'/hqdefault.jpg' : '';
        @endphp
        <div class="vid-card vid-reveal"
             style="transition-delay:{{ ($i % 6) * 70 }}ms"
             data-embed="{{ $embedUrl }}"
             data-yt="{{ $video->url }}">

          <div class="vid-card__thumb">
            @if($ytId)
              <img src="{{ $thumbHd }}"
                   onerror="this.src='{{ $thumbSd }}'"
                   alt="{{ $video->title }}"
                   loading="lazy">
            @else
              <div style="width:100%;height:100%;background:linear-gradient(135deg,#1a1a2e,#0d0d0d);"></div>
            @endif
            <div class="vid-card__shade"></div>
            <div class="vid-card__play">
              <div class="vid-card__play-ring">
                <i class="bi bi-play-fill"></i>
              </div>
            </div>
            <div class="vid-card__yt-badge">
              <i class="bi bi-youtube"></i>
              YouTube
            </div>
          </div>

          <div class="vid-card__body">
            <h3 class="vid-card__title">{{ $video->title }}</h3>
            <div class="vid-card__footer">
              <a href="{{ $video->url }}" target="_blank"
                 rel="noopener nofollow"
                 class="vid-card__yt-link"
                 onclick="event.stopPropagation()">
                <i class="bi bi-box-arrow-up-right"></i>
                YouTube
              </a>
              <button class="vid-card__watch">
                <i class="bi bi-play-fill"></i>
                Watch
              </button>
            </div>
          </div>

        </div>
        @endforeach
      </div>

      <div class="vid-pagination">
        {{ $videos->links() }}
      </div>

    @else

      <div class="vid-empty">
        <i class="bi bi-collection-play"></i>
        <h4 style="color:#64748b;font-weight:700;">No videos yet</h4>
        <p>Videos will be added soon</p>
      </div>

    @endif

  </div>
</section>

{{-- ══════════════════════════════ MODAL ════ --}}
<div class="vm-modal" id="vmModal">
  <div class="vm-modal__wrap">
    <button class="vm-modal__close" id="vmClose" aria-label="Close">
      <i class="bi bi-x-lg"></i>
    </button>
    <iframe class="vm-modal__iframe"
            id="vmFrame"
            src=""
            allow="autoplay; fullscreen; picture-in-picture"
            allowfullscreen>
    </iframe>
  </div>
</div>

@endsection

@section('js')
<script>
(function(){
  var modal  = document.getElementById('vmModal');
  var frame  = document.getElementById('vmFrame');
  var btnClose = document.getElementById('vmClose');

  function openModal(embedUrl){
    frame.src = embedUrl;
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }
  function closeModal(){
    frame.src = '';
    modal.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  /* click on any card */
  document.querySelectorAll('.vid-card').forEach(function(card){
    card.addEventListener('click',function(e){
      if(e.target.closest('.vid-card__yt-link')) return;
      var embed = card.dataset.embed;
      if(embed) openModal(embed);
    });
  });

  btnClose.addEventListener('click', closeModal);
  modal.addEventListener('click', function(e){ if(e.target===modal) closeModal(); });
  document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeModal(); });

  /* reveal */
  var els = document.querySelectorAll('.vid-reveal');
  var io  = new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if(e.isIntersecting){ e.target.classList.add('is-visible'); io.unobserve(e.target); }
    });
  },{threshold:.1});
  els.forEach(function(el){ io.observe(el); });
})();
</script>
@endsection
