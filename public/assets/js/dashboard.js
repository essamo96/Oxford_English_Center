/* ============================================================================
   Oxford Dashboard Shell behaviour — student & teacher portals.
   Vanilla JS (no jQuery dependency for the shell). Hooks into the Bootstrap-3
   tab plugin only to keep the sidebar active-state in sync; it never hijacks
   tab clicks, so existing dashboard logic is untouched.
   ============================================================================ */
(function () {
    'use strict';

    var LS_THEME = 'ox_dash_theme';
    var LS_SIDEBAR = 'ox_dash_sidebar';
    var DESKTOP = 992;

    function root() { return document.querySelector('.ox-dash'); }
    function isDesktop() { return window.innerWidth >= DESKTOP; }

    /* ---- Theme (light / dark) ---- */
    function applyTheme(theme) {
        var el = root(); if (!el) return;
        el.setAttribute('data-dash-theme', theme === 'light' ? 'light' : 'dark');
    }
    function toggleTheme() {
        var el = root(); if (!el) return;
        var next = el.getAttribute('data-dash-theme') === 'dark' ? 'light' : 'dark';
        applyTheme(next);
        try { localStorage.setItem(LS_THEME, next); } catch (e) {}
        // let charts / other widgets re-read theme colors
        try { window.dispatchEvent(new CustomEvent('ox-theme-change', { detail: { theme: next } })); } catch (e) {}
    }

    /* ---- Sidebar collapse (desktop) / drawer (mobile) ---- */
    function toggleSidebar() {
        var el = root(); if (!el) return;
        if (isDesktop()) {
            var collapsed = el.classList.toggle('is-collapsed');
            try { localStorage.setItem(LS_SIDEBAR, collapsed ? 'collapsed' : 'expanded'); } catch (e) {}
        } else {
            el.classList.toggle('is-drawer-open');
        }
    }
    function closeDrawer() {
        var el = root(); if (el) el.classList.remove('is-drawer-open');
    }

    /* ---- Sidebar active-state sync ---- */
    function syncActive(hash) {
        var el = root(); if (!el) return;
        hash = hash || window.location.hash;
        var links = el.querySelectorAll('.ox-dash__navlink[data-tab-target]');
        if (!links.length) return;
        var matched = false;
        links.forEach(function (a) {
            var target = a.getAttribute('data-tab-target');
            var on = !!hash && target === hash;
            a.classList.toggle('is-active', on);
            if (on) matched = true;
        });
        // default: first nav link active when no hash / no match
        if (!matched && (!hash || hash === '#')) {
            links[0].classList.add('is-active');
        }
    }

    function init() {
        var el = root(); if (!el) return;

        // delegated clicks
        document.addEventListener('click', function (e) {
            var t = e.target;

            if (t.closest('[data-dash-toggle]')) { e.preventDefault(); toggleSidebar(); return; }
            if (t.closest('[data-theme-toggle]')) { e.preventDefault(); toggleTheme(); return; }
            if (t.closest('[data-dash-backdrop]')) { closeDrawer(); return; }

            // profile dropdown
            var pBtn = t.closest('[data-profile-toggle]');
            var profile = el.querySelector('.ox-dash__profile');
            if (pBtn && profile) { e.preventDefault(); profile.classList.toggle('is-open'); return; }
            if (profile && !t.closest('.ox-dash__menu')) profile.classList.remove('is-open');

            // close mobile drawer after tapping a sidebar nav link
            if (!isDesktop() && t.closest('.ox-dash__navlink, .ox-dash__logout')) {
                setTimeout(closeDrawer, 80);
            }

            // immediate active feedback for tab links
            var navLink = t.closest('.ox-dash__navlink[data-tab-target]');
            if (navLink) { syncActive(navLink.getAttribute('data-tab-target')); }
        });

        window.addEventListener('hashchange', function () { syncActive(); });
        window.addEventListener('resize', function () { if (isDesktop()) closeDrawer(); });

        // Bootstrap-3 tab events (jQuery) — keep sidebar in sync without hijacking
        if (window.jQuery) {
            window.jQuery(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (ev) {
                var href = ev.target.getAttribute('href');
                if (href && href.charAt(0) === '#') syncActive(href);
            });
        }

        syncActive();
    }

    // initial theme/sidebar state are also set inline in the layout to avoid FOUC;
    // re-assert here in case the inline guard was stripped.
    try {
        var theme = localStorage.getItem(LS_THEME);
        if (theme) applyTheme(theme);
        if (isDesktop() && localStorage.getItem(LS_SIDEBAR) === 'collapsed') {
            var r = root(); if (r) r.classList.add('is-collapsed');
        }
    } catch (e) {}

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
