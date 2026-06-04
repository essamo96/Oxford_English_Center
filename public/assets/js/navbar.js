/* ============================================================================
   Oxford English Centre — navbar.js
   Sticky-on-scroll header, mobile drawer toggle, submenu accordion on mobile,
   search toggle, back-to-top button. No dependencies.
   ============================================================================ */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState === 'loading')
            document.addEventListener('DOMContentLoaded', fn);
        else fn();
    }

    ready(function () {
        var navbar = document.querySelector('[data-navbar]');
        var sentinel = document.querySelector('[data-nav-sentinel]');

        /* ---- Sticky on scroll ---- */
        if (navbar) {
            var threshold = 10;
            var spacer = null;

            function makeStuck(stuck) {
                if (stuck && !navbar.classList.contains('is-stuck')) {
                    spacer = document.createElement('div');
                    spacer.style.height = navbar.offsetHeight + 'px';
                    spacer.setAttribute('data-nav-spacer', '');
                    navbar.parentNode.insertBefore(spacer, navbar);
                    navbar.classList.add('is-stuck');
                } else if (!stuck && navbar.classList.contains('is-stuck')) {
                    navbar.classList.remove('is-stuck');
                    if (spacer && spacer.parentNode) spacer.parentNode.removeChild(spacer);
                    spacer = null;
                }
            }

            var triggerY = sentinel ? sentinel.offsetTop + sentinel.offsetHeight : 160;
            var ticking = false;
            window.addEventListener('scroll', function () {
                if (ticking) return;
                ticking = true;
                requestAnimationFrame(function () {
                    makeStuck(window.pageYOffset > triggerY - threshold);
                    ticking = false;
                });
            }, { passive: true });
        }

        /* ---- Mobile drawer ---- */
        var drawer = document.querySelector('[data-drawer]');
        var backdrop = document.querySelector('[data-backdrop]');
        var openBtns = document.querySelectorAll('[data-drawer-open]');
        var closeBtns = document.querySelectorAll('[data-drawer-close]');

        function openDrawer() {
            if (!drawer) return;
            drawer.classList.add('is-open');
            if (backdrop) backdrop.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
        function closeDrawer() {
            if (!drawer) return;
            drawer.classList.remove('is-open');
            if (backdrop) backdrop.classList.remove('is-open');
            document.body.style.overflow = '';
        }
        openBtns.forEach(function (b) { b.addEventListener('click', openDrawer); });
        closeBtns.forEach(function (b) { b.addEventListener('click', closeDrawer); });
        if (backdrop) backdrop.addEventListener('click', closeDrawer);
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeDrawer(); });

        /* ---- Mobile submenu accordion ---- */
        document.querySelectorAll('[data-submenu-toggle]').forEach(function (t) {
            t.addEventListener('click', function (e) {
                e.preventDefault();
                var li = t.closest('li');
                if (li) li.classList.toggle('is-open');
            });
        });

        /* ---- Search toggle ---- */
        var searchBtn = document.querySelector('[data-search-toggle]');
        var searchBox = document.querySelector('[data-search-box]');
        if (searchBtn && searchBox) {
            searchBtn.addEventListener('click', function (e) {
                e.preventDefault();
                searchBox.classList.toggle('is-open');
                var input = searchBox.querySelector('input');
                if (input && searchBox.classList.contains('is-open')) input.focus();
            });
        }

        /* ---- Back to top ---- */
        var toTop = document.querySelector('[data-totop]');
        if (toTop) {
            window.addEventListener('scroll', function () {
                toTop.classList.toggle('is-visible', window.pageYOffset > 500);
            }, { passive: true });
            toTop.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });
})();
