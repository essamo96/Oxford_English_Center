/* ============================================================================
   Oxford English Centre — app.js
   Entry/init for the redesigned frontend. Boots the preloader fade-out and
   any small global behaviours. Module files (animations/particles/navbar/
   sliders/forms) self-init; this file ties global concerns together.
   ============================================================================ */
(function () {
    'use strict';

    /* ---- Preloader ---- */
    window.addEventListener('load', function () {
        var pre = document.querySelector('[data-preloader]');
        if (pre) {
            setTimeout(function () {
                pre.classList.add('is-done');
                setTimeout(function () { if (pre.parentNode) pre.remove(); }, 600);
            }, 250);
        }
    });

    /* ---- Smooth in-page anchor scrolling (offset for sticky header) ---- */
    document.addEventListener('click', function (e) {
        var a = e.target.closest('a[href^="#"]');
        if (!a) return;
        var id = a.getAttribute('href');
        if (id === '#' || id.length < 2) return;
        var target = document.querySelector(id);
        if (!target) return;
        e.preventDefault();
        var top = target.getBoundingClientRect().top + window.pageYOffset - 90;
        window.scrollTo({ top: top, behavior: 'smooth' });
    });

    /* ---- Mark current year (optional [data-year]) ---- */
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-year]').forEach(function (el) {
            el.textContent = new Date().getFullYear();
        });
    });
})();
