/* ============================================================================
 *  Oxford English Centre — Real-time admin notifications
 *  Transport: Laravel Echo + pusher-js  ->  local laravel-websockets server
 *  Channel:   admin-notifications (public)
 *  Events:    .new.booking , .new.contact
 *  Depends on a global `window.OXFORD_RT` config object (injected by Blade)
 *  and the Echo / Pusher libraries loaded before this file.
 * ========================================================================== */
(function () {
    'use strict';

    var CFG = window.OXFORD_RT || {};

    /* ------------------------------------------------------------------ *
     * 🔊 Sound Manager — synthesized chimes via the Web Audio API
     * ------------------------------------------------------------------ */
    var SoundManager = {
        ctx: null,
        unlock: function () {
            // Browsers require a user gesture before audio can play.
            var resume = function () {
                if (!SoundManager.ctx) {
                    var AC = window.AudioContext || window.webkitAudioContext;
                    if (AC) { SoundManager.ctx = new AC(); }
                }
                if (SoundManager.ctx && SoundManager.ctx.state === 'suspended') {
                    SoundManager.ctx.resume();
                }
            };
            document.addEventListener('click', resume, { once: true });
            document.addEventListener('keydown', resume, { once: true });
        },
        play: function (type) {
            var AC = window.AudioContext || window.webkitAudioContext;
            if (!AC) { return; }
            if (!this.ctx) { this.ctx = new AC(); }
            if (this.ctx.state === 'suspended') { this.ctx.resume(); }

            var sounds = {
                booking: { freq: [523, 659, 784], duration: 0.15, wave: 'sine' },
                contact: { freq: [440, 550, 440], duration: 0.20, wave: 'triangle' },
                success: { freq: [784, 880],      duration: 0.12, wave: 'sine' }
            };
            var sound = sounds[type] || sounds.success;
            var ctx = this.ctx;

            sound.freq.forEach(function (freq, i) {
                setTimeout(function () {
                    var osc  = ctx.createOscillator();
                    var gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = sound.wave;
                    osc.frequency.setValueAtTime(freq, ctx.currentTime);
                    gain.gain.setValueAtTime(0.30, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + sound.duration);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + sound.duration);
                }, i * 150);
            });
        }
    };

    /* ------------------------------------------------------------------ *
     * 🔔 Notification Manager — RTL toasts + live counters
     * ------------------------------------------------------------------ */
    var NotificationManager = {
        container: null,

        init: function () {
            this.container = document.getElementById('rt-notification-container');
            if (!this.container) {
                this.container = document.createElement('div');
                this.container.id = 'rt-notification-container';
                this.container.style.cssText =
                    'position:fixed;top:20px;left:20px;z-index:99999;display:flex;' +
                    'flex-direction:column;gap:12px;max-width:380px;width:100%;direction:rtl;';
                document.body.appendChild(this.container);
            }
        },

        show: function (data) {
            SoundManager.play(data.sound || data.type);
            this.updateCounters(data);

            var toast = this.createToast(data);
            this.container.prepend(toast);
            requestAnimationFrame(function () {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0) scale(1)';
            });

            var self = this;
            setTimeout(function () { self.dismiss(toast); }, 6500);
        },

        createToast: function (data) {
            var colors = {
                booking: { bg: '#1e40af', border: '#3b82f6' },
                contact: { bg: '#065f46', border: '#10b981' }
            };
            var c = colors[data.type] || colors.booking;

            var toast = document.createElement('div');
            toast.setAttribute('data-toast', '');
            toast.style.cssText =
                'background:linear-gradient(135deg,' + c.bg + ',' + c.bg + 'dd);' +
                'border:1px solid ' + c.border + ';border-right:4px solid ' + c.border + ';' +
                'border-radius:12px;padding:16px;color:#fff;font-family:Cairo,sans-serif;' +
                'box-shadow:0 20px 60px rgba(0,0,0,.4);opacity:0;' +
                'transform:translateX(-100px) scale(.9);' +
                'transition:all .4s cubic-bezier(.175,.885,.32,1.275);' +
                'cursor:pointer;position:relative;overflow:hidden;';

            var meta = '';
            if (data.service) { meta += '<span>🏷️ ' + esc(data.service) + '</span> · '; }
            if (data.subject) { meta += '<span>📌 ' + esc(data.subject) + '</span> · '; }
            if (data.mobile)  { meta += '<span>📞 ' + esc(data.mobile) + '</span> · '; }
            meta += '<span>⏱️ ' + esc(data.created_at || '') + '</span>';

            var preview = data.preview
                ? '<div style="font-size:11px;opacity:.75;background:rgba(255,255,255,.12);' +
                  'padding:6px 10px;border-radius:6px;margin-top:6px;">' + esc(data.preview) + '</div>'
                : '';

            toast.innerHTML =
                '<div style="position:absolute;top:0;right:0;left:0;height:2px;background:' + c.border + ';' +
                'animation:rtProgress 6.5s linear forwards;"></div>' +
                '<div style="display:flex;align-items:flex-start;gap:12px;">' +
                  '<div style="font-size:28px;line-height:1;">' + (data.icon || '🔔') + '</div>' +
                  '<div style="flex:1;min-width:0;">' +
                    '<div style="font-weight:700;font-size:14px;margin-bottom:4px;">' + esc(data.message || '') + '</div>' +
                    '<div style="font-size:12px;opacity:.85;">' + meta + '</div>' +
                    preview +
                  '</div>' +
                  '<button type="button" data-close style="background:rgba(255,255,255,.15);border:none;' +
                  'color:#fff;width:24px;height:24px;border-radius:50%;cursor:pointer;font-size:12px;' +
                  'display:flex;align-items:center;justify-content:center;flex:none;">✕</button>' +
                '</div>';

            var self = this;
            toast.querySelector('[data-close]').addEventListener('click', function (e) {
                e.stopPropagation();
                self.dismiss(toast);
            });
            if (data.link) {
                toast.addEventListener('click', function () { window.location.href = data.link; });
            }
            return toast;
        },

        dismiss: function (toast) {
            if (!toast || toast._gone) { return; }
            toast._gone = true;
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-100px) scale(.9)';
            setTimeout(function () { if (toast.parentNode) { toast.remove(); } }, 400);
        },

        updateCounters: function (data) {
            var map = {
                'pending-bookings': data.pending_bookings,
                'today-bookings':   data.today_bookings,
                'unread-contacts':  data.unread_contacts,
                'notify-total':     data.total_notify
            };
            Object.keys(map).forEach(function (key) {
                var value = map[key];
                if (value === undefined || value === null) { return; }
                document.querySelectorAll('[data-counter="' + key + '"]').forEach(function (el) {
                    NotificationManager.animateCounter(el, parseInt(el.textContent || '0', 10) || 0, parseInt(value, 10));
                });
            });
            // Reveal / refresh the header bell badge.
            document.querySelectorAll('[data-counter="notify-total"]').forEach(function (el) {
                var n = parseInt(data.total_notify, 10);
                if (!isNaN(n)) { el.style.display = n > 0 ? '' : 'none'; }
            });
        },

        animateCounter: function (el, from, to) {
            if (from === to || isNaN(to)) { el.textContent = to; return; }
            el.style.transition = 'all .3s ease';
            el.style.transform = 'scale(1.4)';
            el.style.color = '#fbbf24';

            var duration = 600, start = performance.now();
            function step(now) {
                var p = Math.min((now - start) / duration, 1);
                var eased = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(from + (to - from) * eased);
                if (p < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = to;
                    setTimeout(function () { el.style.transform = 'scale(1)'; el.style.color = ''; }, 300);
                }
            }
            requestAnimationFrame(step);
        }
    };

    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
        });
    }

    /* ------------------------------------------------------------------ *
     * 📡 Bootstrap pusher-js + listeners (raw client — most reliable for
     *    a self-hosted laravel-websockets server, no Echo/wss ambiguity)
     * ------------------------------------------------------------------ */
    function initRealtime() {
        if (!CFG.key) {
            console.warn('[RT] Missing OXFORD_RT config — notifications disabled.');
            return;
        }
        if (typeof window.Pusher === 'undefined') {
            console.warn('[RT] pusher-js not loaded — notifications disabled.');
            return;
        }

        var useTLS = CFG.scheme === 'https';

        // Base options work for BOTH modes. We only add wsHost/wsPort when a
        // host is configured (self-hosted laravel-websockets). With no host the
        // client targets Pusher Cloud using the cluster — the right setup for
        // shared cPanel hosting where you cannot run a websocket daemon.
        // NOTE: never set `encrypted:true` for a local ws:// server — it forces
        // a wss:// handshake that silently fails. forceTLS drives TLS.
        var opts = {
            cluster: CFG.cluster || 'mt1',
            forceTLS: useTLS,
            disableStats: true
        };
        if (CFG.host) {
            opts.wsHost = CFG.host;
            opts.wsPort = CFG.port;
            opts.wssPort = CFG.port;
            opts.enabledTransports = useTLS ? ['ws', 'wss'] : ['ws'];
        }
        var pusher = new window.Pusher(CFG.key, opts);

        pusher.connection.bind('state_change', function (s) {
            console.log('[RT] connection:', s.previous, '->', s.current);
        });
        pusher.connection.bind('connected', function () {
            console.log('%c✅ Real-time connected (socket ' + pusher.connection.socket_id + ')',
                'color:#10b981;font-weight:bold');
        });
        pusher.connection.bind('error', function (err) {
            console.error('[RT] connection error — is `php artisan websockets:serve` running on port '
                + CFG.port + '?', err);
        });

        var channel = pusher.subscribe('admin-notifications');
        channel.bind('pusher:subscription_succeeded', function () {
            console.log('[RT] subscribed to admin-notifications');
        });
        // Raw pusher-js binds the exact broadcastAs name (no leading dot).
        channel.bind('new.booking', function (data) { NotificationManager.show(data); });
        channel.bind('new.contact', function (data) { NotificationManager.show(data); });

        window.OxfordPusher = pusher; // exposed for manual debugging

        // Manual UI self-test (sound + toast + counter) without sending real data:
        //   OxfordRTtest('contact')  or  OxfordRTtest('booking')
        window.OxfordRTtest = function (type) {
            NotificationManager.show(type === 'booking'
                ? { type: 'booking', icon: '📅', sound: 'booking', message: 'تجربة: حجز جديد',
                    service: 'برنامج الكبار', mobile: '0599000000', created_at: 'الآن',
                    pending_bookings: 1, today_bookings: 1, total_notify: 3 }
                : { type: 'contact', icon: '✉️', sound: 'contact', message: 'تجربة: رسالة جديدة',
                    subject: 'اختبار', preview: 'هذه رسالة تجريبية', created_at: 'الآن',
                    unread_contacts: 2, total_notify: 3 });
        };
    }

    function boot() {
        SoundManager.unlock();
        NotificationManager.init();
        initRealtime();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
