/* ============================================================================
   Oxford English Centre — particles.js
   Lightweight canvas particle field with mouse interaction.
   Attaches to any element with [data-particles]. Capped + DPR-aware so it
   stays cheap. Skips entirely on reduced-motion or small screens.
   ============================================================================ */
(function () {
    'use strict';

    var reduce = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function Field(host) {
        var canvas = document.createElement('canvas');
        canvas.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;display:block;';
        host.appendChild(canvas);
        var ctx = canvas.getContext('2d');

        var DPR = Math.min(window.devicePixelRatio || 1, 2);
        var w = 0, h = 0, parts = [], mouse = { x: -9999, y: -9999 };
        var color = host.getAttribute('data-particles-color') || 'rgba(255,255,255,';
        var raf = null;

        function count() {
            var area = w * h / (DPR * DPR);
            return Math.min(60, Math.max(20, Math.round(area / 22000)));
        }

        function resize() {
            var r = host.getBoundingClientRect();
            w = canvas.width = Math.max(1, r.width * DPR);
            h = canvas.height = Math.max(1, r.height * DPR);
            seed();
        }

        function seed() {
            parts = [];
            var n = count();
            for (var i = 0; i < n; i++) {
                parts.push({
                    x: Math.random() * w,
                    y: Math.random() * h,
                    vx: (Math.random() - 0.5) * 0.25 * DPR,
                    vy: (Math.random() - 0.5) * 0.25 * DPR,
                    r: (Math.random() * 3 + 2.2) * DPR
                });
            }
        }

        function tick() {
            ctx.clearRect(0, 0, w, h);
            for (var i = 0; i < parts.length; i++) {
                var p = parts[i];
                p.x += p.vx; p.y += p.vy;
                if (p.x < 0 || p.x > w) p.vx *= -1;
                if (p.y < 0 || p.y > h) p.vy *= -1;

                // mouse repel
                var dx = p.x - mouse.x, dy = p.y - mouse.y;
                var d2 = dx * dx + dy * dy;
                var R = 120 * DPR;
                if (d2 < R * R && d2 > 0) {
                    var f = (R - Math.sqrt(d2)) / R * 0.6;
                    var ang = Math.atan2(dy, dx);
                    p.x += Math.cos(ang) * f * 2;
                    p.y += Math.sin(ang) * f * 2;
                }

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = color + '0.85)';
                ctx.fill();
            }
            raf = requestAnimationFrame(tick);
        }

        host.addEventListener('mousemove', function (e) {
            var r = host.getBoundingClientRect();
            mouse.x = (e.clientX - r.left) * DPR;
            mouse.y = (e.clientY - r.top) * DPR;
        });
        host.addEventListener('mouseleave', function () { mouse.x = mouse.y = -9999; });
        window.addEventListener('resize', debounce(resize, 200));

        // pause when off-screen
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (ent) {
                ent.forEach(function (e) {
                    if (e.isIntersecting) { if (!raf) tick(); }
                    else { cancelAnimationFrame(raf); raf = null; }
                });
            }, { threshold: 0 }).observe(host);
        }

        resize();
        tick();
    }

    function debounce(fn, ms) {
        var t; return function () { clearTimeout(t); t = setTimeout(fn, ms); };
    }

    function init() {
        if (reduce) return;
        if (window.innerWidth < 768) return; // skip on phones for perf
        document.querySelectorAll('[data-particles]').forEach(function (el) {
            if (getComputedStyle(el).position === 'static') el.style.position = 'relative';
            new Field(el);
        });
    }

    if (document.readyState === 'loading')
        document.addEventListener('DOMContentLoaded', init);
    else init();
})();
