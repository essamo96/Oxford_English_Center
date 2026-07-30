/**
 * Avatar-click moderation menu for group chat.
 *
 * Clicking a student's avatar inside a conversation opens a small popup offering
 * to mute them (blocks posting, keeps reading) or ban them (blocks posting and
 * reading), or to lift whatever restriction is already in force.
 *
 * Shared by the admin monitor and the teacher's chat box: both post to their own
 * endpoints, but the menu, the wording and the confirmation flow are identical,
 * so a teacher and an admin see exactly the same moderation UX.
 *
 * Configure once per page:
 *
 *   OxChatModeration.init({
 *       container: '#gc_messages',        // where bubbles live (delegated)
 *       avatar:    '[data-moderate-student]',
 *       urls: { state, restrict, lift },  // endpoints
 *       token:     '<csrf>',
 *       groupId:   12,
 *       onChange:  function (studentId, type) {}   // optional
 *   });
 */
(function (global) {
    'use strict';

    var cfg = null;
    var menu = null;

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
        });
    }

    function closeMenu() {
        if (menu) { menu.remove(); menu = null; }
    }

    // Any click outside the popup dismisses it, as with every other menu.
    document.addEventListener('click', function (e) {
        if (menu && !menu.contains(e.target) && !e.target.closest('[data-moderate-student]')) {
            closeMenu();
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeMenu(); }
    });

    function buildMenu(info, anchor, groupId) {
        closeMenu();

        var el = document.createElement('div');
        el.className = 'ox-mod-menu';

        var restricted = info.restricted;
        var isBan = info.type === 'ban';

        var head = '<div class="ox-mod-menu__head">'
                 + '<img class="ox-mod-menu__avatar" src="' + esc(info.avatar) + '" alt="">'
                 + '<div class="ox-mod-menu__id">'
                 + '<span class="ox-mod-menu__name">' + esc(info.name) + '</span>'
                 + (restricted
                     ? '<span class="ox-mod-menu__state ox-mod-menu__state--' + (isBan ? 'ban' : 'mute') + '">'
                       + (isBan ? 'محظور' : 'مُسكت') + '</span>'
                     : '<span class="ox-mod-menu__state ox-mod-menu__state--ok">مسموح</span>')
                 + '</div></div>';

        var reasonRow = '<input type="text" class="ox-mod-menu__reason" '
                      + 'placeholder="السبب (اختياري)" autocomplete="off">';

        var body = '';
        if (restricted) {
            body += (info.reason
                ? '<div class="ox-mod-menu__reason-shown">السبب: ' + esc(info.reason) + '</div>'
                : '<div class="ox-mod-menu__reason-shown ox-mod-menu__reason-shown--none">بدون ذكر سبب</div>');
            body += '<button type="button" class="ox-mod-menu__btn ox-mod-menu__btn--lift" data-action="lift">'
                  + '<span>✅</span> رفع القيد</button>';
            // Allow switching severity without lifting first.
            body += isBan
                ? '<button type="button" class="ox-mod-menu__btn ox-mod-menu__btn--mute" data-action="mute"><span>🔇</span> تحويل إلى إسكات</button>'
                : '<button type="button" class="ox-mod-menu__btn ox-mod-menu__btn--ban" data-action="ban"><span>⛔</span> ترقية إلى حظر كامل</button>';
        } else {
            body += reasonRow;
            body += '<button type="button" class="ox-mod-menu__btn ox-mod-menu__btn--mute" data-action="mute">'
                  + '<span>🔇</span> إسكات<small>يقرأ المحادثة ولا يرسل</small></button>';
            body += '<button type="button" class="ox-mod-menu__btn ox-mod-menu__btn--ban" data-action="ban">'
                  + '<span>⛔</span> حظر كامل<small>لا يرسل ولا يرى المحادثة</small></button>';
        }

        el.innerHTML = head + '<div class="ox-mod-menu__body">' + body + '</div>';
        document.body.appendChild(el);
        menu = el;

        position(el, anchor);

        el.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-action]');
            if (!btn) return;
            var action = btn.getAttribute('data-action');
            var reasonEl = el.querySelector('.ox-mod-menu__reason');
            var reason = reasonEl ? reasonEl.value.trim() : '';
            apply(info.student_id, action, reason, el, groupId);
        });
    }

    function position(el, anchor) {
        var r = anchor.getBoundingClientRect();
        var top = r.bottom + window.scrollY + 8;
        var left = r.left + window.scrollX;

        // Keep the popup inside the viewport on both axes.
        var w = el.offsetWidth || 260;
        if (left + w > window.innerWidth - 12) { left = window.innerWidth - w - 12; }
        if (left < 12) { left = 12; }

        var h = el.offsetHeight || 200;
        if (top + h > window.scrollY + window.innerHeight - 12) {
            top = r.top + window.scrollY - h - 8;
        }

        el.style.top = top + 'px';
        el.style.left = left + 'px';
    }

    function setBusy(el, busy) {
        el.classList.toggle('ox-mod-menu--busy', busy);
        Array.prototype.forEach.call(el.querySelectorAll('button'), function (b) {
            b.disabled = busy;
        });
    }

    /**
     * The group id may be fixed (admin monitor shows one group) or vary per
     * click (a teacher can have several chat boxes open at once), so it is
     * allowed to be a function of the clicked avatar.
     */
    function groupIdFor(anchor) {
        return typeof cfg.groupId === 'function' ? cfg.groupId(anchor) : cfg.groupId;
    }

    function apply(studentId, action, reason, el, groupId) {
        var lifting = action === 'lift';
        var url = lifting ? cfg.urls.lift : cfg.urls.restrict;

        if (action === 'ban' && !confirm('سيتم حظر الطالب نهائياً: لن يتمكن من إرسال الرسائل ولا حتى رؤية المحادثة. هل تريد المتابعة؟')) {
            return;
        }

        var payload = {
            _token: cfg.token,
            group_id: groupId,
            student_id: studentId
        };
        if (!lifting) {
            payload.type = action;      // 'mute' | 'ban'
            payload.reason = reason;    // blank => applied silently
        }

        setBusy(el, true);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': cfg.token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        }).then(function (r) {
            return r.json().then(function (data) { return { ok: r.ok, data: data }; });
        }).then(function (res) {
            if (res.ok && res.data.state === 1) {
                closeMenu();
                if (typeof cfg.onChange === 'function') {
                    cfg.onChange(studentId, lifting ? null : action, res.data);
                }
                toast(res.data.message || 'تم تنفيذ العملية', true);
            } else {
                setBusy(el, false);
                toast((res.data && res.data.message) || 'تعذر تنفيذ العملية', false);
            }
        }).catch(function () {
            setBusy(el, false);
            toast('تعذر تنفيذ العملية', false);
        });
    }

    function toast(text, ok) {
        var t = document.createElement('div');
        t.className = 'ox-mod-toast ' + (ok ? 'ox-mod-toast--ok' : 'ox-mod-toast--err');
        t.textContent = text;
        document.body.appendChild(t);
        requestAnimationFrame(function () { t.classList.add('is-in'); });
        setTimeout(function () {
            t.classList.remove('is-in');
            setTimeout(function () { if (t.parentNode) t.remove(); }, 300);
        }, 3200);
    }

    function openFor(anchor) {
        var studentId = anchor.getAttribute('data-moderate-student');
        if (!studentId) return;

        // Ask the server for the current state rather than trusting the DOM: the
        // restriction may have been changed from another screen since this bubble
        // was rendered.
        var groupId = groupIdFor(anchor);
        var url = cfg.urls.state
            + (cfg.urls.state.indexOf('?') === -1 ? '?' : '&')
            + 'group_id=' + encodeURIComponent(groupId)
            + '&student_id=' + encodeURIComponent(studentId);

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        }).then(function (r) { return r.json(); })
          .then(function (info) {
              if (info.state !== 1) { toast(info.message || 'تعذر جلب حالة الطالب', false); return; }
              buildMenu(info, anchor, groupId);
          })
          .catch(function () { toast('تعذر جلب حالة الطالب', false); });
    }

    function init(options) {
        cfg = options;
        var root = document.querySelector(options.container);
        if (!root) return;

        // Delegated: bubbles are appended live, long after init runs.
        root.addEventListener('click', function (e) {
            var anchor = e.target.closest(options.avatar || '[data-moderate-student]');
            if (!anchor || !root.contains(anchor)) return;
            e.preventDefault();
            e.stopPropagation();
            openFor(anchor);
        });
    }

    global.OxChatModeration = { init: init, close: closeMenu, toast: toast };
})(window);
