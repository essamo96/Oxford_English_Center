/* ============================================================================
   Oxford English Centre — sliders.js
   Self-contained cross-fade hero slider (replaces nivo-slider on redesigned
   pages) + a generic horizontal-scroll carousel for testimonials.
   No dependencies.
   ============================================================================ */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState === 'loading')
            document.addEventListener('DOMContentLoaded', fn);
        else fn();
    }

    /* ---- Hero cross-fade slider (syncs optional captions) ---- */
    function initHero(root) {
        var slides = root.querySelectorAll('.ox-hero__slide');
        var caps = root.querySelectorAll('.ox-hero__cap');
        function activate(idx) {
            slides.forEach(function (s, k) { s.classList.toggle('is-active', k === idx); });
            if (caps.length) caps.forEach(function (c, k) { c.classList.toggle('is-active', k === idx); });
        }
        if (slides.length <= 1) { activate(0); return; }
        var i = 0, interval = parseInt(root.getAttribute('data-hero-interval'), 10) || 6000;
        activate(0);
        setInterval(function () { i = (i + 1) % slides.length; activate(i); }, interval);
    }

    /* ---- Generic carousel (testimonials) ---- */
    function initCarousel(root) {
        var track = root.querySelector('[data-carousel-track]');
        if (!track) return;
        var prev = root.querySelector('[data-carousel-prev]');
        var next = root.querySelector('[data-carousel-next]');
        var dotsWrap = root.querySelector('[data-carousel-dots]');

        function page() { return Math.max(track.clientWidth * 0.9, 280); }
        if (next) next.addEventListener('click', function () { track.scrollBy({ left: page(), behavior: 'smooth' }); });
        if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: -page(), behavior: 'smooth' }); });

        // build dots from children
        if (dotsWrap) {
            var items = track.children.length;
            for (var d = 0; d < items; d++) {
                var b = document.createElement('button');
                b.className = 'ox-dot';
                b.setAttribute('aria-label', 'Go to slide ' + (d + 1));
                (function (idx) {
                    b.addEventListener('click', function () {
                        var child = track.children[idx];
                        if (child) track.scrollTo({ left: child.offsetLeft - track.offsetLeft, behavior: 'smooth' });
                    });
                })(d);
                dotsWrap.appendChild(b);
            }
            var dots = dotsWrap.querySelectorAll('.ox-dot');
            track.addEventListener('scroll', function () {
                var idx = Math.round(track.scrollLeft / (track.scrollWidth / items));
                dots.forEach(function (dot, k) { dot.classList.toggle('is-active', k === idx); });
            }, { passive: true });
            if (dots[0]) dots[0].classList.add('is-active');
        }
    }

    ready(function () {
        document.querySelectorAll('[data-hero]').forEach(initHero);
        document.querySelectorAll('[data-carousel]').forEach(initCarousel);
    });
})();
