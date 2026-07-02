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
    var IS_RTL = (CFG.locale || 'ar') !== 'en';
    var DIR    = IS_RTL ? 'rtl' : 'ltr';

    var I18N = {
        ar: {
            payment_title:  'دفعة جديدة',
            payment_review: 'بانتظار مراجعتك في قائمة الطلبات المالية',
            student_fallback: 'طالب',
            now: 'الآن'
        },
        en: {
            payment_title:  'New payment',
            payment_review: 'Awaiting your review in the payment requests list',
            student_fallback: 'Student',
            now: 'Now'
        }
    };
    function t(key) {
        var dict = I18N[IS_RTL ? 'ar' : 'en'];
        return dict[key] || key;
    }

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
                success: { freq: [784, 880],      duration: 0.12, wave: 'sine' },
                payment: { freq: [392, 523, 659, 784], duration: 0.16, wave: 'triangle' }
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
                var sideProp = IS_RTL ? 'left' : 'right';
                this.container.style.cssText =
                    'position:fixed;top:20px;' + sideProp + ':20px;z-index:99999;display:flex;' +
                    'flex-direction:column;gap:12px;max-width:380px;width:100%;direction:' + DIR + ';';
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
                contact: { bg: '#065f46', border: '#10b981' },
                payment: { bg: '#92400e', border: '#f59e0b' }
            };
            var c = colors[data.type] || colors.booking;
            var accentSide = IS_RTL ? 'border-right' : 'border-left';
            var offscreenX = IS_RTL ? '100px' : '-100px';

            var toast = document.createElement('div');
            toast.setAttribute('data-toast', '');
            toast.setAttribute('dir', DIR);
            toast.style.cssText =
                'background:linear-gradient(135deg,' + c.bg + ',' + c.bg + 'dd);' +
                'border:1px solid ' + c.border + ';' + accentSide + ':4px solid ' + c.border + ';' +
                'border-radius:12px;padding:16px;color:#fff;font-family:Cairo,sans-serif;' +
                'box-shadow:0 20px 60px rgba(0,0,0,.4);opacity:0;' +
                'transform:translateX(' + offscreenX + ') scale(.9);' +
                'transition:all .4s cubic-bezier(.175,.885,.32,1.275);' +
                'direction:' + DIR + ';' +
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
            toast.style.transform = 'translateX(' + (IS_RTL ? '100px' : '-100px') + ') scale(.9)';
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

            // Also update the sidebar data-live-counters where possible based on this event:
            if (data.type === 'booking') {
                var closedEl = document.querySelector('[data-live-counter="closed_classes"]');
                var currentClosed = closedEl ? (parseInt(closedEl.textContent || '0', 10) || 0) : 0;
                var newBookings = parseInt(data.pending_bookings, 10) || 0;
                var pendingRequests = newBookings + currentClosed;
                
                var updateMap = {
                    'unread_bookings': newBookings,
                    'pending_requests': pendingRequests
                };
                Object.keys(updateMap).forEach(function (key) {
                    var val = updateMap[key];
                    document.querySelectorAll('[data-live-counter="' + key + '"]').forEach(function (el) {
                        NotificationManager.animateCounter(el, parseInt(el.textContent || '0', 10) || 0, val);
                        var badgeWrapper = el.closest('.menu-badge');
                        if (badgeWrapper) {
                            badgeWrapper.style.display = val > 0 ? 'inline-block' : 'none';
                        }
                    });
                });
            }
        },

        updateLiveCounters: function (counters, branchBreakdown) {
            if (!counters) return;

            // 1. Update notify-total (header bell)
            var total = parseInt(counters.total, 10) || 0;
            document.querySelectorAll('[data-counter="notify-total"]').forEach(function (el) {
                NotificationManager.animateCounter(el, parseInt(el.textContent || '0', 10) || 0, total);
                el.style.display = total > 0 ? '' : 'none';
            });

            // Update header bell dot
            document.querySelectorAll('.app-navbar-item .bullet-dot').forEach(function (el) {
                el.style.display = total > 0 ? '' : 'none';
            });

            // Update title text in notifications dropdown
            var titleSpan = document.querySelector('.menu-sub-dropdown h3 span');
            if (titleSpan) {
                titleSpan.textContent = total + ' تقارير جديدة';
            }

            // 2. Map of sidebar counter elements
            var pendingRequests = (parseInt(counters.unread_bookings, 10) || 0) + (parseInt(counters.closed_classes, 10) || 0);
            var totalMessages = (parseInt(counters.student_messages, 10) || 0) + (parseInt(counters.teacher_messages, 10) || 0);

            var pendingPayments = parseInt(counters.pending_student_payments, 10) || 0;
            var pendingFinOrders = parseInt(counters.pending_financial_orders, 10) || 0;

            var map = {
                'pending_requests':        pendingRequests,
                'unread_bookings':         parseInt(counters.unread_bookings, 10) || 0,
                'closed_classes':          parseInt(counters.closed_classes, 10) || 0,
                'total_messages':          totalMessages,
                'student_messages':        parseInt(counters.student_messages, 10) || 0,
                'teacher_messages':        parseInt(counters.teacher_messages, 10) || 0,
                'pending_student_payments': pendingPayments,
                'pending_financial_orders': pendingFinOrders,
                // Aggregate badge on the "الإدارة المالية" parent menu item — sum of both
                // pending-fee-order and student-payment-submission counts, same scope as
                // its two children (branch-scoped for branch admins, grand total for super admin).
                'financial_total':          pendingPayments + pendingFinOrders,
                'unread_combo_registrations': parseInt(counters.unread_combo_registrations, 10) || 0,
                'combo_requests_total':       parseInt(counters.unread_combo_registrations, 10) || 0
            };

            Object.keys(map).forEach(function (key) {
                var value = map[key];
                document.querySelectorAll('[data-live-counter="' + key + '"]').forEach(function (el) {
                    NotificationManager.animateCounter(el, parseInt(el.textContent || '0', 10) || 0, value);

                    var badgeWrapper = el.closest('.menu-badge');
                    if (badgeWrapper) {
                        badgeWrapper.style.display = value > 0 ? 'inline-block' : 'none';
                    }
                });
            });

            // Super-admin tooltip: per-branch breakdown of the parent badge total, kept fresh
            // in real time so they know which branch a new pending item belongs to.
            if (branchBreakdown && branchBreakdown.length) {
                var parentBadge = document.querySelector('[data-financial-parent-badge]');
                if (parentBadge) {
                    parentBadge.title = branchBreakdown.map(function (b) {
                        return b.name + ': ' + b.count;
                    }).join(' | ');
                }
            }
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
     * 🔄 Dropdown refresh — fetches updated notification list from server
     * ------------------------------------------------------------------ */
    var _refreshTimer = null;
    function refreshDropdown() {
        // Debounce: avoid multiple rapid refreshes from concurrent events
        clearTimeout(_refreshTimer);
        _refreshTimer = setTimeout(function () {
            fetch('/admin/notifications/dropdown-partial', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            }).then(function (r) {
                if (!r.ok) return;
                return r.text();
            }).then(function (html) {
                if (!html) return;
                var body = document.getElementById('rt-notif-body');
                if (body) body.innerHTML = html;
            }).catch(function () {});
        }, 400);
    }

    /* ------------------------------------------------------------------ *
     * 📡 Bootstrap pusher-js + listeners (raw client — most reliable for
     *    a self-hosted laravel-websockets server, no Echo/wss ambiguity)
     * ------------------------------------------------------------------ */
    function initRealtime() {
        if (!CFG.key) {
            console.warn('[RT] OXFORD_RT.key is empty — check PUSHER_APP_KEY in .env');
            return;
        }
        if (CFG.broadcast_driver && CFG.broadcast_driver !== 'pusher') {
            // Don't bail — this flag is read from config() and can be stale if
            // `config:cache` was run before BROADCAST_DRIVER=pusher was set in .env
            // (the exact same class of bug that broke route caching on this server).
            // Attempting the connection anyway costs nothing: a real Pusher key still
            // works, and a genuine misconfiguration fails loudly via the connection
            // 'error' handler below instead of silently skipping setup entirely.
            console.warn(
                '[RT] BROADCAST_DRIVER reports "' + CFG.broadcast_driver + '" (possibly stale config cache) — ' +
                'attempting Pusher connection anyway since a key is present. ' +
                'If this is wrong, run: php artisan config:clear'
            );
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

        // Branch admins subscribe to their branch channel only.
        // Super admins (no branch_id) subscribe to the global channel.
        var channelName = CFG.branch_id
            ? 'admin-notifications-branch-' + CFG.branch_id
            : 'admin-notifications';

        var channel = pusher.subscribe(channelName);
        channel.bind('pusher:subscription_succeeded', function () {
            console.log('[RT] subscribed to ' + channelName);
        });
        channel.bind('new.booking', function (data) { NotificationManager.show(data); refreshDropdown(); });
        channel.bind('new.combo', function (data) { NotificationManager.show(data); refreshDropdown(); });
        channel.bind('new.contact', function (data) { NotificationManager.show(data); refreshDropdown(); });
        channel.bind('counters.updated', function (data) {
            if (data && data.counters) {
                // Branch admins use the branch-scoped counters (data.counters); the super
                // admin (no branch_id) uses the grand total across all branches instead —
                // both are bundled in every payload since broadcastWith() can't vary per
                // channel and this event now reaches both the global and branch channels.
                var scoped = CFG.branch_id ? data.counters : (data.counters_global || data.counters);
                console.log('[RT] counters updated', scoped);
                NotificationManager.updateLiveCounters(scoped, data.branch_breakdown);
                refreshDropdown();
            }
        });
        channel.bind('student.payment.submitted', function (data) {
            SoundManager.play('payment');
            var name   = data.student_name || t('student_fallback');
            var amount = data.amount ? '₪ ' + parseFloat(data.amount).toFixed(2) : '';
            NotificationManager.show({
                type:    'payment',
                sound:   null, // already played above
                icon:    '💳',
                message: t('payment_title') + ' — ' + esc(name) + (amount ? ' (' + amount + ')' : ''),
                preview: t('payment_review'),
                link:    data.link || null,
                created_at: t('now')
            });
            // Increment pending_student_payments AND the parent "financial_total" badge
            // immediately, before the slightly-later counters.updated event lands.
            ['pending_student_payments', 'financial_total'].forEach(function (key) {
                document.querySelectorAll('[data-live-counter="' + key + '"]').forEach(function (el) {
                    var n = (parseInt(el.textContent, 10) || 0) + 1;
                    NotificationManager.animateCounter(el, parseInt(el.textContent, 10) || 0, n);
                    var badge = el.closest('.menu-badge');
                    if (badge) badge.style.display = 'inline-block';
                });
            });
            refreshDropdown();
        });

        window.OxfordPusher = pusher; // exposed for manual debugging

        // Diagnostic: OxfordRTdiag() — prints full config + connection state to console
        window.OxfordRTdiag = function () {
            var state = pusher.connection.state;
            var ok = state === 'connected';
            console.group('%c[RT] Oxford Real-time Diagnostic', 'font-weight:bold;color:' + (ok ? '#10b981' : '#ef4444'));
            console.log('BROADCAST_DRIVER:', CFG.broadcast_driver || '(not set)');
            console.log('Pusher key:', CFG.key ? CFG.key.slice(0, 6) + '****' : '(empty)');
            console.log('Host:', CFG.host || '(Pusher Cloud)');
            console.log('Port:', CFG.port);
            console.log('Scheme:', CFG.scheme);
            console.log('Cluster:', CFG.cluster);
            console.log('Branch:', CFG.branch_id || '(super admin)');
            console.log('Channel:', channelName);
            console.log('Connection state:', state);
            if (!ok) {
                console.warn('⚠️  Not connected! Common fixes:');
                console.warn('  1. Set BROADCAST_DRIVER=pusher in .env');
                console.warn('  2. Set valid PUSHER_APP_KEY / PUSHER_APP_SECRET in .env');
                if (CFG.host) {
                    console.warn('  3. Run: php artisan websockets:serve (self-hosted mode)');
                } else {
                    console.warn('  3. Verify Pusher Cloud credentials at https://dashboard.pusher.com');
                }
                console.warn('  4. Run: php artisan config:clear && php artisan cache:clear');
            } else {
                console.log('%c✅ Connected — if events are not arriving, check BROADCAST_DRIVER=pusher in .env', 'color:#10b981');
            }
            console.groupEnd();
        };

        // Manual UI self-test (sound + toast + counter) without sending real data:
        //   OxfordRTtest('contact')  or  OxfordRTtest('booking')  or  OxfordRTtest('payment')
        window.OxfordRTtest = function (type) {
            if (type === 'payment') {
                NotificationManager.show({ type: 'payment', icon: '💳', sound: 'payment',
                    message: 'تجربة: دفعة جديدة — طالب تجريبي (₪ 50.00)',
                    preview: 'بانتظار مراجعتك في قائمة الطلبات المالية', created_at: 'الآن' });
                return;
            }
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
