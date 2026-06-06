@extends('frontend.layouts.master')
@section('title', 'ألبوم الصور')

@section('css')
<style>
:root{
  --gal-primary:#0F4C81;
  --gal-teal:#2C9AB7;
  --gal-gold:#F7B733;
  --gal-radius:12px;
}

/* ── BANNER ─────────────────────────────────────────────────── */
.galc-banner{
  position:relative;
  min-height:320px;
  display:flex;
  align-items:center;
  background-size:cover;
  background-position:center;
  overflow:hidden;
}
.galc-banner::before{
  content:'';
  position:absolute;
  inset:0;
  background:linear-gradient(135deg,
    rgba(6,11,20,.84) 0%,
    rgba(15,76,129,.72) 60%,
    rgba(44,154,183,.50) 100%);
}
.galc-banner__shapes{position:absolute;inset:0;pointer-events:none;}
.galc-banner__shapes span{
  position:absolute;border-radius:50%;
  background:var(--gal-gold);opacity:.07;
}
.galc-banner__shapes span:nth-child(1){width:300px;height:300px;top:-80px;right:-60px;}
.galc-banner__shapes span:nth-child(2){width:160px;height:160px;bottom:-40px;left:15%;background:var(--gal-teal);opacity:.1;}

.galc-banner__content{
  position:relative;z-index:2;
  padding:70px 0 50px;
  width:100%;
}
.galc-breadcrumb{
  display:flex;align-items:center;gap:8px;
  color:rgba(255,255,255,.45);font-size:.82rem;margin-bottom:14px;
}
.galc-breadcrumb a{color:rgba(255,255,255,.7);text-decoration:none;transition:color .2s;}
.galc-breadcrumb a:hover{color:var(--gal-gold);}
.galc-breadcrumb i{font-size:.7rem;color:rgba(255,255,255,.3);}
.galc-banner__eyebrow{
  display:inline-flex;align-items:center;gap:7px;
  background:rgba(247,183,51,.16);border:1px solid rgba(247,183,51,.32);
  color:var(--gal-gold);font-size:.78rem;font-weight:700;
  letter-spacing:.07em;text-transform:uppercase;
  padding:4px 13px;border-radius:999px;margin-bottom:14px;
}
.galc-banner h1{
  font-family:'Cairo',sans-serif;
  font-size:clamp(1.8rem,3.5vw,2.8rem);
  font-weight:900;color:#fff;line-height:1.15;margin:0 0 10px;
}
.galc-banner h1 em{font-style:normal;color:var(--gal-gold);}
.galc-banner__meta{
  display:flex;align-items:center;gap:20px;flex-wrap:wrap;
  color:rgba(255,255,255,.6);font-size:.85rem;margin-top:14px;
}
.galc-banner__meta span{
  display:flex;align-items:center;gap:6px;
}
.galc-banner__meta span i{color:var(--gal-teal);}
.galc-back{
  display:inline-flex;align-items:center;gap:7px;
  color:rgba(255,255,255,.75);font-size:.85rem;font-weight:600;
  border:1px solid rgba(255,255,255,.25);
  padding:7px 18px;border-radius:999px;text-decoration:none;
  transition:all .2s;margin-top:4px;
}
.galc-back:hover{color:#fff;background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.45);}

/* ── GALLERY SECTION ─────────────────────────────────────────── */
.galc-section{
  padding:56px 0 72px;
  background:#f8fafc;
}

/* Filter strip (visual only — all shown) */
.galc-toolbar{
  display:flex;align-items:center;justify-content:space-between;
  flex-wrap:wrap;gap:12px;margin-bottom:32px;
}
.galc-count{
  font-size:.88rem;color:#64748b;font-weight:600;
}
.galc-count strong{color:var(--gal-primary);}

/* ── MASONRY GRID ────────────────────────────────────────────── */
.galc-grid{
  columns:4;
  column-gap:14px;
}
@media(max-width:992px){.galc-grid{columns:3;}}
@media(max-width:640px){.galc-grid{columns:2;}}
@media(max-width:380px){.galc-grid{columns:1;}}

.galc-item{
  display:block;
  break-inside:avoid;
  margin-bottom:14px;
  position:relative;
  overflow:hidden;
  border-radius:var(--gal-radius);
  cursor:pointer;
  border:2px solid transparent;
  transition:border-color .25s,transform .3s;
}
.galc-item:hover{
  border-color:var(--gal-teal);
  transform:scale(1.02);
}
.galc-item img{
  display:block;
  width:100%;
  height:auto;
  transition:transform .5s cubic-bezier(.25,.46,.45,.94),filter .3s;
}
.galc-item:hover img{
  transform:scale(1.06);
  filter:brightness(.88);
}
.galc-item__overlay{
  position:absolute;
  inset:0;
  background:linear-gradient(to top,rgba(15,76,129,.75) 0%,transparent 55%);
  opacity:0;
  transition:opacity .3s;
  display:flex;
  align-items:center;
  justify-content:center;
}
.galc-item:hover .galc-item__overlay{opacity:1;}
.galc-item__overlay i{
  font-size:1.8rem;
  color:#fff;
  background:rgba(255,255,255,.15);
  backdrop-filter:blur(6px);
  -webkit-backdrop-filter:blur(6px);
  width:50px;height:50px;
  border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  border:2px solid rgba(255,255,255,.45);
}
.galc-item--cover::after{
  content:'غلاف';
  position:absolute;top:10px;right:10px;
  background:var(--gal-gold);color:#fff;
  font-size:.68rem;font-weight:800;
  padding:3px 10px;border-radius:999px;
  pointer-events:none;
}

/* ── EMPTY ───────────────────────────────────────────────────── */
.galc-empty{
  text-align:center;padding:80px 20px;color:#94a3b8;
}
.galc-empty i{font-size:4rem;display:block;margin-bottom:16px;opacity:.35;}

/* ── LIGHTBOX ────────────────────────────────────────────────── */
.lbx{
  display:none;
  position:fixed;inset:0;
  z-index:99999;
  background:rgba(6,11,20,.96);
  backdrop-filter:blur(10px);
  -webkit-backdrop-filter:blur(10px);
  align-items:center;
  justify-content:center;
  padding:20px;
}
.lbx.is-open{display:flex;}

.lbx__stage{
  position:relative;
  max-width:min(92vw,1100px);
  max-height:90vh;
  display:flex;
  align-items:center;
  justify-content:center;
}
.lbx__img{
  max-width:100%;
  max-height:85vh;
  object-fit:contain;
  border-radius:10px;
  box-shadow:0 40px 80px rgba(0,0,0,.6);
  transition:opacity .25s ease;
}
.lbx__img.is-loading{opacity:0;}

.lbx__close{
  position:fixed;top:18px;left:18px;
  background:rgba(255,255,255,.12);
  border:none;color:#fff;
  width:42px;height:42px;border-radius:50%;
  font-size:1.2rem;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  transition:background .2s;z-index:2;
}
.lbx__close:hover{background:rgba(255,255,255,.22);}

.lbx__nav{
  position:fixed;top:50%;transform:translateY(-50%);
  background:rgba(255,255,255,.1);
  border:1.5px solid rgba(255,255,255,.2);
  color:#fff;border-radius:50%;
  width:48px;height:48px;
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;font-size:1.1rem;transition:background .2s;
}
.lbx__nav:hover{background:rgba(255,255,255,.22);}
.lbx__prev{right:20px;}
.lbx__next{left:20px;}

.lbx__counter{
  position:fixed;bottom:22px;left:50%;transform:translateX(-50%);
  color:rgba(255,255,255,.55);font-size:.82rem;font-weight:600;
  background:rgba(255,255,255,.08);padding:5px 18px;border-radius:999px;
}

/* Dots strip */
.lbx__dots{
  position:fixed;bottom:60px;left:50%;transform:translateX(-50%);
  display:flex;gap:6px;align-items:center;
}
.lbx__dot{
  width:8px;height:8px;border-radius:50%;
  background:rgba(255,255,255,.25);
  transition:background .2s,transform .2s;cursor:pointer;
}
.lbx__dot.is-active{
  background:var(--gal-gold);
  transform:scale(1.4);
}

/* ── REVEAL ──────────────────────────────────────────────────── */
.galc-reveal{opacity:0;transform:translateY(22px);transition:opacity .5s ease,transform .5s ease;}
.galc-reveal.is-visible{opacity:1;transform:translateY(0);}
</style>
@endsection

@section('content')

{{-- ════════════════════════════ BANNER ════ --}}
<div class="galc-banner"
     style="background-image:url('{{ url('assets/oxford/img/banner/gallary.jpg') }}')">
  <div class="galc-banner__shapes">
    <span></span><span></span>
  </div>
  <div class="container">
    <div class="galc-banner__content">

      <div class="galc-breadcrumb">
        <a href="{{ url('/') }}">Home</a>
        <i class="bi bi-chevron-left"></i>
        <a href="{{ url('photos') }}">Photo Gallery</a>
        <i class="bi bi-chevron-left"></i>
        <span>Album</span>
      </div>

      <div class="galc-banner__eyebrow">
        <i class="bi bi-images"></i>
        Album View
      </div>

      <h1>Photo <em>Album</em></h1>

      <div class="galc-banner__meta">
        <span>
          <i class="bi bi-camera"></i>
          {{ count($pics) }} Photos
        </span>
        <span>
          <i class="bi bi-eye"></i>
          Click any photo to enlarge
        </span>
      </div>

      <a href="{{ url('photos') }}" class="galc-back" style="margin-top:16px;display:inline-flex;">
        <i class="bi bi-arrow-right"></i>
        Back to Albums
      </a>

    </div>
  </div>
</div>

{{-- ════════════════════════════ GRID ════ --}}
<section class="galc-section">
  <div class="container">

    @if(count($pics) > 0)

      <div class="galc-toolbar galc-reveal">
        <p class="galc-count">
          Showing <strong>{{ count($pics) }}</strong> Photos
        </p>
        <span style="font-size:.8rem;color:#94a3b8;">
          <i class="bi bi-arrows-fullscreen" style="color:var(--gal-teal);"></i>
          Click to view fullscreen
        </span>
      </div>

      <div class="galc-grid" id="galcGrid">
        @foreach($pics as $index => $img)
        <div class="galc-item galc-reveal {{ $img->feature ? 'galc-item--cover' : '' }}"
             data-index="{{ $index }}"
             style="transition-delay:{{ ($index % 12) * 40 }}ms">
          <img src="{{ asset('File/Images/photo/'.$img->image) }}"
               alt="Photo {{ $index + 1 }}"
               loading="lazy">
          <div class="galc-item__overlay">
            <i class="bi bi-arrows-fullscreen"></i>
          </div>
        </div>
        @endforeach
      </div>

    @else

      <div class="galc-empty">
        <i class="bi bi-images"></i>
        <h4 style="color:#64748b;font-weight:700;">No photos in this album</h4>
        <a href="{{ url('photos') }}" class="galc-back" style="margin-top:20px;">
          <i class="bi bi-arrow-right"></i>
          Back to Albums
        </a>
      </div>

    @endif

  </div>
</section>

{{-- ════════════════════════════ LIGHTBOX ════ --}}
<div class="lbx" id="lbx" role="dialog" aria-modal="true">
  <button class="lbx__close" id="lbxClose" aria-label="Close">
    <i class="bi bi-x-lg"></i>
  </button>

  <div class="lbx__stage">
    <img class="lbx__img" id="lbxImg" src="" alt="">
  </div>

  <button class="lbx__nav lbx__prev" id="lbxPrev" aria-label="السابق">
    <i class="bi bi-chevron-right"></i>
  </button>
  <button class="lbx__nav lbx__next" id="lbxNext" aria-label="التالي">
    <i class="bi bi-chevron-left"></i>
  </button>

  <div class="lbx__counter" id="lbxCounter"></div>

  <div class="lbx__dots" id="lbxDots"></div>
</div>

@endsection

@section('js')
<script>
(function(){
  var IMAGES = [
    @foreach($pics as $img)
    '{{ asset("File/Images/photo/".$img->image) }}',
    @endforeach
  ];

  var MAX_DOTS = 12;
  var lbx     = document.getElementById('lbx');
  var lbxImg  = document.getElementById('lbxImg');
  var lbxCtr  = document.getElementById('lbxCounter');
  var lbxDots = document.getElementById('lbxDots');
  var current = 0;

  /* build dots */
  if(IMAGES.length <= MAX_DOTS){
    IMAGES.forEach(function(_,i){
      var d = document.createElement('button');
      d.className = 'lbx__dot' + (i===0?' is-active':'');
      d.addEventListener('click',function(){ go(i); });
      lbxDots.appendChild(d);
    });
  }

  function updateDots(){
    var dots = lbxDots.querySelectorAll('.lbx__dot');
    dots.forEach(function(d,i){ d.classList.toggle('is-active',i===current); });
  }

  function go(idx){
    idx = (idx + IMAGES.length) % IMAGES.length;
    current = idx;
    lbxImg.classList.add('is-loading');
    var src = IMAGES[current];
    var tmp = new Image();
    tmp.onload = function(){
      lbxImg.src = src;
      lbxImg.classList.remove('is-loading');
    };
    tmp.src = src;
    lbxCtr.textContent = (current+1) + ' / ' + IMAGES.length;
    updateDots();
  }

  function open(idx){
    current = idx;
    lbxImg.src = IMAGES[current];
    lbxCtr.textContent = (current+1) + ' / ' + IMAGES.length;
    updateDots();
    lbx.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }
  function close(){
    lbx.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  /* click on grid items */
  document.getElementById('galcGrid').addEventListener('click',function(e){
    var item = e.target.closest('.galc-item');
    if(!item) return;
    open(parseInt(item.dataset.index));
  });

  document.getElementById('lbxClose').addEventListener('click', close);
  document.getElementById('lbxPrev').addEventListener('click', function(){ go(current+1); });
  document.getElementById('lbxNext').addEventListener('click', function(){ go(current-1); });

  lbx.addEventListener('click', function(e){ if(e.target===lbx) close(); });

  document.addEventListener('keydown',function(e){
    if(!lbx.classList.contains('is-open')) return;
    if(e.key==='Escape') close();
    if(e.key==='ArrowRight') go(current+1);
    if(e.key==='ArrowLeft')  go(current-1);
  });

  /* touch swipe */
  var tsX = null;
  lbx.addEventListener('touchstart', function(e){ tsX = e.changedTouches[0].clientX; }, {passive:true});
  lbx.addEventListener('touchend', function(e){
    if(tsX === null) return;
    var dx = e.changedTouches[0].clientX - tsX;
    if(Math.abs(dx) > 50){ dx > 0 ? go(current+1) : go(current-1); }
    tsX = null;
  }, {passive:true});

  /* reveal on scroll */
  var els = document.querySelectorAll('.galc-reveal');
  var io  = new IntersectionObserver(function(entries){
    entries.forEach(function(e){
      if(e.isIntersecting){ e.target.classList.add('is-visible'); io.unobserve(e.target); }
    });
  },{threshold:.1});
  els.forEach(function(el){ io.observe(el); });
})();
</script>
@endsection
