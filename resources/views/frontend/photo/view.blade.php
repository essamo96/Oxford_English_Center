@extends('frontend.layouts.master')
@section('title', 'معرض الصور')

@section('css')
<style>
/* ── PAGE-LEVEL TOKENS ──────────────────────────────────────── */
:root{
  --gal-primary:  #0F4C81;
  --gal-teal:     #2C9AB7;
  --gal-gold:     #F7B733;
  --gal-dark:     #060b14;
  --gal-radius:   16px;
}

/* ── INNER BANNER ───────────────────────────────────────────── */
.gal-banner{
  position:relative;
  min-height:360px;
  display:flex;
  align-items:center;
  background-size:cover;
  background-position:center;
  overflow:hidden;
}
.gal-banner::before{
  content:'';
  position:absolute;
  inset:0;
  background:linear-gradient(135deg,
    rgba(6,11,20,.82) 0%,
    rgba(15,76,129,.70) 55%,
    rgba(44,154,183,.55) 100%);
}
.gal-banner__shapes{
  position:absolute;
  inset:0;
  pointer-events:none;
  overflow:hidden;
}
.gal-banner__shapes span{
  position:absolute;
  border-radius:50%;
  opacity:.08;
  background:var(--gal-gold);
}
.gal-banner__shapes span:nth-child(1){width:380px;height:380px;top:-100px;left:-80px;}
.gal-banner__shapes span:nth-child(2){width:200px;height:200px;bottom:-60px;right:12%;background:var(--gal-teal);opacity:.12;}
.gal-banner__shapes span:nth-child(3){width:120px;height:120px;top:30%;right:6%;opacity:.15;}

.gal-banner__content{
  position:relative;
  z-index:2;
  padding:80px 0 60px;
}
.gal-banner__eyebrow{
  display:inline-flex;
  align-items:center;
  gap:8px;
  background:rgba(247,183,51,.18);
  border:1px solid rgba(247,183,51,.35);
  color:var(--gal-gold);
  font-size:.82rem;
  font-weight:700;
  letter-spacing:.08em;
  text-transform:uppercase;
  padding:5px 14px;
  border-radius:999px;
  margin-bottom:18px;
}
.gal-banner__eyebrow .dot{
  width:6px;height:6px;
  background:var(--gal-gold);
  border-radius:50%;
  animation:pulse-dot 1.8s ease-in-out infinite;
}
@keyframes pulse-dot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(1.4)}}

.gal-banner h1{
  font-family:'Cairo',sans-serif;
  font-size:clamp(2rem,4vw,3.2rem);
  font-weight:900;
  color:#fff;
  line-height:1.15;
  margin:0 0 12px;
}
.gal-banner h1 em{
  font-style:normal;
  color:var(--gal-gold);
}
.gal-banner__sub{color:rgba(255,255,255,.65);font-size:1rem;margin:0;}

.gal-breadcrumb{
  display:flex;
  align-items:center;
  gap:8px;
  color:rgba(255,255,255,.45);
  font-size:.85rem;
  margin-bottom:16px;
}
.gal-breadcrumb a{color:rgba(255,255,255,.7);text-decoration:none;transition:color .2s;}
.gal-breadcrumb a:hover{color:var(--gal-gold);}
.gal-breadcrumb i{font-size:.7rem;color:rgba(255,255,255,.3);}

/* ── STAT STRIP ─────────────────────────────────────────────── */
.gal-stats{
  background:#fff;
  border-bottom:1px solid #e8edf4;
  padding:18px 0;
}
.gal-stats__inner{
  display:flex;
  align-items:center;
  gap:32px;
  flex-wrap:wrap;
}
.gal-stat{
  display:flex;
  align-items:center;
  gap:10px;
  color:var(--gal-primary);
}
.gal-stat__icon{
  width:36px;height:36px;
  background:rgba(15,76,129,.08);
  border-radius:10px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:1rem;
}
.gal-stat__num{font-size:1.15rem;font-weight:800;}
.gal-stat__lbl{font-size:.78rem;color:#64748b;}

/* ── SECTION ────────────────────────────────────────────────── */
.gal-section{
  padding:64px 0 80px;
  background:var(--ox-gray-50,#f8fafc);
}
.gal-section__head{
  display:flex;
  align-items:flex-end;
  justify-content:space-between;
  margin-bottom:40px;
  flex-wrap:wrap;
  gap:12px;
}
.gal-section__title-block .eyebrow{
  font-size:.78rem;
  font-weight:700;
  letter-spacing:.1em;
  text-transform:uppercase;
  color:var(--gal-teal);
  display:flex;
  align-items:center;
  gap:8px;
  margin-bottom:8px;
}
.gal-section__title-block .eyebrow::before{
  content:'';
  display:block;
  width:24px;
  height:2px;
  background:var(--gal-teal);
  border-radius:999px;
}
.gal-section__title-block h2{
  font-family:'Cairo',sans-serif;
  font-size:clamp(1.6rem,2.5vw,2.2rem);
  font-weight:900;
  color:var(--gal-primary);
  margin:0;
}

/* ── ALBUM GRID ─────────────────────────────────────────────── */
.gal-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
  gap:24px;
}

.album-card{
  background:#fff;
  border-radius:var(--gal-radius);
  overflow:hidden;
  text-decoration:none;
  color:inherit;
  display:block;
  border:1.5px solid #e8edf4;
  transition:transform .35s cubic-bezier(.175,.885,.32,1.275),
             box-shadow .35s ease,
             border-color .25s ease;
  will-change:transform;
}
.album-card:hover{
  transform:translateY(-6px) scale(1.01);
  box-shadow:0 20px 56px -12px rgba(15,76,129,.22);
  border-color:rgba(15,76,129,.25);
  color:inherit;
  text-decoration:none;
}

.album-card__thumb{
  position:relative;
  aspect-ratio:4/3;
  overflow:hidden;
  background:linear-gradient(135deg,#e8edf4 0%,#d4dde9 100%);
}
.album-card__thumb img{
  width:100%;
  height:100%;
  object-fit:cover;
  transition:transform .55s cubic-bezier(.25,.46,.45,.94);
}
.album-card:hover .album-card__thumb img{transform:scale(1.07);}

.album-card__empty{
  width:100%;
  height:100%;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:8px;
  background:linear-gradient(135deg,#dde6f0,#c8d8ea);
  color:#8ca4bc;
}
.album-card__empty i{font-size:2.5rem;}
.album-card__empty span{font-size:.8rem;font-weight:600;}

/* Count pill */
.album-card__count{
  position:absolute;
  top:12px;
  left:12px;
  background:rgba(6,11,20,.68);
  backdrop-filter:blur(6px);
  -webkit-backdrop-filter:blur(6px);
  color:#fff;
  font-size:.75rem;
  font-weight:700;
  padding:4px 11px;
  border-radius:999px;
  display:flex;
  align-items:center;
  gap:5px;
}

/* Overlay CTA */
.album-card__overlay{
  position:absolute;
  inset:0;
  background:linear-gradient(to top,
    rgba(15,76,129,.82) 0%,
    rgba(15,76,129,.2) 50%,
    transparent 100%);
  opacity:0;
  transition:opacity .3s ease;
  display:flex;
  align-items:flex-end;
  justify-content:center;
  padding-bottom:24px;
}
.album-card:hover .album-card__overlay{opacity:1;}
.album-card__overlay-btn{
  background:rgba(255,255,255,.15);
  backdrop-filter:blur(8px);
  border:1.5px solid rgba(255,255,255,.5);
  color:#fff;
  font-size:.82rem;
  font-weight:700;
  padding:8px 24px;
  border-radius:999px;
  display:flex;
  align-items:center;
  gap:7px;
  transform:translateY(10px);
  transition:transform .3s ease,background .2s;
}
.album-card:hover .album-card__overlay-btn{transform:translateY(0);}

/* Cover badge */
.album-card__cover-badge{
  position:absolute;
  top:12px;
  right:12px;
  background:var(--gal-gold);
  color:#fff;
  font-size:.68rem;
  font-weight:800;
  letter-spacing:.04em;
  padding:3px 9px;
  border-radius:999px;
  display:none;
}

/* Body */
.album-card__body{
  padding:18px 20px 20px;
  border-top:1px solid #f0f4f8;
}
.album-card__title{
  font-family:'Cairo',sans-serif;
  font-size:1.02rem;
  font-weight:800;
  color:var(--gal-primary);
  margin:0 0 6px;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}
.album-card__desc{
  font-size:.82rem;
  color:#64748b;
  margin:0;
  display:-webkit-box;
  -webkit-line-clamp:2;
  -webkit-box-orient:vertical;
  overflow:hidden;
  line-height:1.55;
}
.album-card__meta{
  display:flex;
  align-items:center;
  gap:14px;
  margin-top:12px;
  padding-top:12px;
  border-top:1px solid #f0f4f8;
}
.album-card__meta-item{
  display:flex;
  align-items:center;
  gap:5px;
  font-size:.77rem;
  color:#94a3b8;
  font-weight:600;
}
.album-card__meta-item i{font-size:.85rem;color:var(--gal-teal);}

/* ── EMPTY STATE ────────────────────────────────────────────── */
.gal-empty{
  text-align:center;
  padding:80px 20px;
  color:#94a3b8;
}
.gal-empty i{font-size:4rem;display:block;margin-bottom:20px;opacity:.4;}
.gal-empty h4{font-size:1.2rem;color:#64748b;font-weight:700;margin-bottom:8px;}
.gal-empty p{font-size:.9rem;}

/* ── PAGINATION ─────────────────────────────────────────────── */
.gal-pagination{margin-top:48px;display:flex;justify-content:center;}
.gal-pagination .pagination .page-link{
  border-radius:8px !important;
  margin:0 3px;
  border-color:#e2e8f0;
  color:var(--gal-primary);
  font-weight:600;
  padding:8px 16px;
}
.gal-pagination .pagination .page-item.active .page-link{
  background:var(--gal-primary);
  border-color:var(--gal-primary);
  color:#fff;
}

/* ── REVEAL ANIMATIONS ──────────────────────────────────────── */
.gal-reveal{
  opacity:0;
  transform:translateY(28px);
  transition:opacity .55s ease, transform .55s ease;
}
.gal-reveal.is-visible{
  opacity:1;
  transform:translateY(0);
}
</style>
@endsection

@section('content')

{{-- ════════════════════════════════════ HERO BANNER ════ --}}
<div class="gal-banner"
     style="background-image:url('{{ url('assets/oxford/img/banner/gallary.jpg') }}')">
  <div class="gal-banner__shapes">
    <span></span><span></span><span></span>
  </div>
  <div class="container">
    <div class="gal-banner__content">

      <div class="gal-breadcrumb">
        <a href="{{ url('/') }}">Home</a>
        <i class="bi bi-chevron-left"></i>
        <span>Photo Gallery</span>
      </div>

      <div class="gal-banner__eyebrow">
        <span class="dot"></span>
        Oxford Gallery
      </div>

      <h1>Photo <em>Gallery</em></h1>
      <p class="gal-banner__sub">Browse our albums and explore the most memorable moments</p>

    </div>
  </div>
</div>

{{-- ════════════════════════════════════ STAT STRIP ════ --}}
@if($pics->count() > 0)
<div class="gal-stats">
  <div class="container">
    <div class="gal-stats__inner">
      <div class="gal-stat">
        <div class="gal-stat__icon"><i class="bi bi-collection"></i></div>
        <div>
          <div class="gal-stat__num">{{ $pics->total() }}</div>
          <div class="gal-stat__lbl">Albums</div>
        </div>
      </div>
      <div class="gal-stat">
        <div class="gal-stat__icon"><i class="bi bi-images"></i></div>
        <div>
          <div class="gal-stat__num">{{ $pics->sum(fn($p) => $p->images->count()) }}</div>
          <div class="gal-stat__lbl">Photos</div>
        </div>
      </div>
      <div class="gal-stat" style="margin-right:auto;">
        <div class="gal-stat__icon"><i class="bi bi-camera2"></i></div>
        <div>
          <div class="gal-stat__num">HD</div>
          <div class="gal-stat__lbl">High Quality</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

{{-- ════════════════════════════════════ ALBUMS ════ --}}
<section class="gal-section">
  <div class="container">

    <div class="gal-section__head gal-reveal">
      <div class="gal-section__title-block">
        <div class="eyebrow"><span></span>Photo Gallery</div>
        <h2>Our Albums</h2>
      </div>
    </div>

    @if($pics->count() > 0)

      <div class="gal-grid">
        @foreach($pics as $i => $album)
        @php
          $imgCount  = $album->images->count();
          // skip albums with no images
          if($imgCount === 0) continue;

          // 1) try feature image first
          $coverUrl  = \App\Helpers\Helper::get_client_img($album->id);
          $hasFeatured = $coverUrl && !str_ends_with($coverUrl, 'noimage.jpg');

          // 2) fall back to first image in album
          if(!$hasFeatured) {
              $firstImg = $album->images->first();
              $coverUrl = $firstImg ? asset('File/Images/photo/'.$firstImg->image) : null;
          }
        @endphp
        <a href="{{ url('gallary/'.$album->id) }}"
           class="album-card gal-reveal"
           style="transition-delay:{{ ($i % 8) * 60 }}ms">

          <div class="album-card__thumb">
            <img src="{{ $coverUrl }}" alt="{{ $album->title }}" loading="lazy">

            <div class="album-card__count">
              <i class="bi bi-camera"></i>
              {{ $imgCount }}
            </div>

            <div class="album-card__overlay">
              <span class="album-card__overlay-btn">
                <i class="bi bi-eye"></i>
                View Album
              </span>
            </div>
          </div>

          <div class="album-card__body">
            <h3 class="album-card__title">{{ $album->title }}</h3>
            @if($album->descs)
              <p class="album-card__desc">{{ $album->descs }}</p>
            @endif
            <div class="album-card__meta">
              <span class="album-card__meta-item">
                <i class="bi bi-images"></i>
                {{ $imgCount }} Photos
              </span>
              @if($hasFeatured)
              <span class="album-card__meta-item">
                <i class="bi bi-star-fill" style="color:var(--gal-gold)!important;"></i>
                Cover Set
              </span>
              @endif
            </div>
          </div>

        </a>
        @endforeach
      </div>

      <div class="gal-pagination">
        {{ $pics->links() }}
      </div>

    @else

      <div class="gal-empty">
        <i class="bi bi-images"></i>
        <h4>No albums yet</h4>
        <p>Albums will be added soon</p>
      </div>

    @endif

  </div>
</section>

@endsection

@section('js')
<script>
(function(){
  var els = document.querySelectorAll('.gal-reveal');
  if(!els.length) return;
  var io = new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if(e.isIntersecting){
        e.target.classList.add('is-visible');
        io.unobserve(e.target);
      }
    });
  },{threshold:.12});
  els.forEach(function(el){ io.observe(el); });
})();
</script>
@endsection
