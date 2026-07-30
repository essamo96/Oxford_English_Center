/**
 * Voice-note player for group chat.
 *
 * Progressively enhances any `[data-voice-player]` block into a WhatsApp-style
 * player: one play/pause button, a seekable bar of waveform ticks that fill as
 * playback advances, and an elapsed/duration readout. The markup already holds a
 * plain <audio> element, so if this script never loads the note is still playable.
 *
 * Used by both the admin monitor and the student/teacher chat box, so the two
 * sides look and behave identically.
 */
(function (global) {
    'use strict';

    function fmt(seconds) {
        if (!isFinite(seconds) || seconds < 0) { seconds = 0; }
        var m = Math.floor(seconds / 60);
        var s = Math.floor(seconds % 60);
        return m + ':' + (s < 10 ? '0' + s : s);
    }

    // Only one note plays at a time — starting a second pauses the first, the way
    // every messaging app behaves.
    var current = null;

    function init(root) {
        if (!root || root.dataset.voiceReady === '1') { return; }
        root.dataset.voiceReady = '1';

        var audio    = root.querySelector('[data-voice-audio]');
        var button   = root.querySelector('[data-voice-toggle]');
        var icon     = root.querySelector('[data-voice-icon]');
        var progress = root.querySelector('[data-voice-progress]');
        var timeEl   = root.querySelector('[data-voice-time]');
        var seek     = root.querySelector('[data-voice-seek]');

        if (!audio || !button) { return; }

        // The admin monitor renders Bootstrap Icons while the student/teacher chat box
        // renders Font Awesome, so the glyph classes are declared on the element
        // rather than hard-coded here.
        var playClass  = (icon && icon.dataset.iconPlay)  || 'bi-play-fill';
        var pauseClass = (icon && icon.dataset.iconPause) || 'bi-pause-fill';

        function setIcon(playing) {
            if (!icon) { return; }
            icon.classList.remove(playClass, pauseClass);
            icon.classList.add(playing ? pauseClass : playClass);
        }

        function paint() {
            var d = audio.duration;
            var pct = (isFinite(d) && d > 0) ? (audio.currentTime / d) * 100 : 0;
            if (progress) { progress.style.width = pct + '%'; }
            if (timeEl) {
                // Show remaining-style elapsed while playing, total when idle.
                timeEl.textContent = (audio.currentTime > 0)
                    ? fmt(audio.currentTime)
                    : (isFinite(d) ? fmt(d) : '0:00');
            }
        }

        audio.addEventListener('loadedmetadata', paint);
        audio.addEventListener('timeupdate', paint);

        audio.addEventListener('play', function () {
            if (current && current !== audio) { current.pause(); }
            current = audio;
            setIcon(true);
        });
        //test
        audio.addEventListener('pause', function () { setIcon(false); });
        audio.addEventListener('ended', function () {
            setIcon(false);
            audio.currentTime = 0;
            paint();
        });

        button.addEventListener('click', function (e) {
            e.preventDefault();
            if (audio.paused) {
                var p = audio.play();
                if (p && p.catch) { p.catch(function () { /* autoplay/decode refusal */ }); }
            } else {
                audio.pause();
            }
        });

        if (seek) {
            seek.addEventListener('click', function (e) {
                var d = audio.duration;
                if (!isFinite(d) || d <= 0) { return; }
                var rect = seek.getBoundingClientRect();
                var x = e.clientX - rect.left;
                // The chat is RTL, but the waveform is laid out left-to-right, so the
                // ratio is measured from the visual left edge in both directions.
                var ratio = Math.min(Math.max(x / rect.width, 0), 1);
                audio.currentTime = ratio * d;
                paint();
            });
        }

        paint();
    }

    function initAll(scope) {
        var nodes = (scope || document).querySelectorAll('[data-voice-player]');
        Array.prototype.forEach.call(nodes, init);
    }

    global.OxVoicePlayer = { init: init, initAll: initAll };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { initAll(); });
    } else {
        initAll();
    }
})(window);
