/* ============================================================================
   Oxford English Centre — forms.js
   Progressive enhancement for redesigned forms: floating-label state,
   basic required-field validation feedback, and submit guard.
   Does NOT change any form action / method — purely visual + UX.
   ============================================================================ */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState === 'loading')
            document.addEventListener('DOMContentLoaded', fn);
        else fn();
    }

    ready(function () {
        /* filled-state for floating labels */
        document.querySelectorAll('.ox-input, .ox-textarea, .ox-select').forEach(function (el) {
            function sync() { el.classList.toggle('is-filled', !!el.value); }
            el.addEventListener('input', sync);
            el.addEventListener('change', sync);
            sync();
        });

        /* lightweight client validation (visual only; server still validates) */
        document.querySelectorAll('form[data-ox-validate]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                var ok = true;
                form.querySelectorAll('[required]').forEach(function (field) {
                    var valid = field.value.trim() !== '';
                    if (field.type === 'email')
                        valid = valid && /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(field.value);
                    field.classList.toggle('is-invalid', !valid);
                    if (!valid) ok = false;
                });
                if (!ok) {
                    e.preventDefault();
                    var first = form.querySelector('.is-invalid');
                    if (first) first.focus();
                }
            });
        });
    });
})();
