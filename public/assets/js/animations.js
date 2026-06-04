/* ============================================================================
   Oxford English Centre — animations.js
   Scroll-reveal via IntersectionObserver + animated number counters.
   No dependencies. Respects prefers-reduced-motion.
   ============================================================================ */
(function () {
    'use strict';

    var reduce = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---- Scroll reveal ---- */
    function initReveal() {
        var els = document.querySelectorAll('[data-reveal]');
        if (!els.length) return;

        if (reduce || !('IntersectionObserver' in window)) {
            els.forEach(function (el) { el.classList.add('is-in'); });
            return;
        }

        var io = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var el = entry.target;
                var delay = el.getAttribute('data-reveal-delay');
                if (delay) el.style.setProperty('--d', delay);
                el.classList.add('is-in');
                obs.unobserve(el);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

        els.forEach(function (el) { io.observe(el); });
    }

    /* ---- Number counters ---- */
    function animateCount(el) {
        var target = parseFloat(el.getAttribute('data-count') || el.textContent) || 0;
        var dur = 1600, start = null;
        var prefix = el.getAttribute('data-prefix') || '';
        var suffix = el.getAttribute('data-suffix') || '';

        function fmt(n) { return prefix + Math.floor(n).toLocaleString('en-US') + suffix; }

        if (reduce) { el.textContent = fmt(target); return; }

        function step(ts) {
            if (start === null) start = ts;
            var p = Math.min((ts - start) / dur, 1);
            var eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
            el.textContent = fmt(target * eased);
            if (p < 1) requestAnimationFrame(step);
            else el.textContent = fmt(target);
        }
        requestAnimationFrame(step);
    }

    function initCounters() {
        var nums = document.querySelectorAll('[data-count]');
        if (!nums.length) return;
        if (!('IntersectionObserver' in window)) {
            nums.forEach(animateCount); return;
        }
        var io = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                animateCount(entry.target);
                obs.unobserve(entry.target);
            });
        }, { threshold: 0.5 });
        nums.forEach(function (n) { io.observe(n); });
    }

    /* ---- Parallax on [data-parallax] (subtle) ---- */
    function initParallax() {
        if (reduce) return;
        var els = document.querySelectorAll('[data-parallax]');
        if (!els.length) return;
        var ticking = false;
        function update() {
            var y = window.pageYOffset;
            els.forEach(function (el) {
                var speed = parseFloat(el.getAttribute('data-parallax')) || 0.15;
                el.style.transform = 'translate3d(0,' + (y * speed) + 'px,0)';
            });
            ticking = false;
        }
        window.addEventListener('scroll', function () {
            if (!ticking) { requestAnimationFrame(update); ticking = true; }
        }, { passive: true });
    }

    function init() { initReveal(); initCounters(); initParallax(); }

    if (document.readyState === 'loading')
        document.addEventListener('DOMContentLoaded', init);
    else init();

    window.OxAnim = { reveal: initReveal, counters: initCounters };
})();
