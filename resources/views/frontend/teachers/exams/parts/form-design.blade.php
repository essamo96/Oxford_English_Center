{{-- Extra styles for the exam add/edit form, layered on top of the shared tex-* design
     system (parts/design.blade.php) so the "إضافة/تعديل امتحان" screens match the rest
     of the Examination Center visually. --}}
<style>
    .tex-section { background: #fff; border: 1px solid var(--tex-border); border-radius: 14px; margin-bottom: 22px; overflow: hidden; }
    .tex-section__header { display: flex; align-items: center; gap: 10px; padding: 16px 20px; background: #f9fafc; border-bottom: 1px solid var(--tex-border); }
    .tex-section__header i { font-size: 19px; color: var(--tex-navy); }
    .tex-section__header h6 { margin: 0; font-weight: 700; color: var(--tex-navy); }
    .tex-section__body { padding: 22px; }
    .tex-label { font-weight: 600; font-size: 13.5px; color: #4a5268; margin-bottom: 7px; display: block; }
    .tex-label .req { color: var(--tex-danger); margin-inline-start: 2px; }
    .tex-hint { font-size: 12px; color: var(--tex-muted); margin-top: 5px; }

    /* ── Inputs / selects: consistent sizing + clear focus state ── */
    .tex-section .form-control,
    .tex-section .form-select {
        min-height: 46px; border: 1.5px solid var(--tex-border); border-radius: 10px;
        font-size: 14.5px; padding: 10px 14px; color: var(--tex-navy);
        transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
    }
    .tex-section textarea.form-control { min-height: unset; }
    .tex-section .form-control::placeholder { color: #a7aec0; }
    .tex-section .form-control:hover,
    .tex-section .form-select:hover { border-color: #c7ccd9; }
    .tex-section .form-control:focus,
    .tex-section .form-select:focus {
        border-color: var(--tex-navy); box-shadow: 0 0 0 4px rgba(20, 33, 61, .10); outline: none;
    }
    .tex-section .form-control:disabled,
    .tex-section .form-select:disabled { background: #f2f4f8; color: var(--tex-muted); cursor: not-allowed; opacity: .85; }
    .tex-section .form-select { cursor: pointer; }
    .tex-section .form-control.is-invalid,
    .tex-section .form-select.is-invalid { border-color: var(--tex-danger); }

    /* ── Toggle switches ── */
    .tex-switch-group { border: 1.5px solid var(--tex-border); border-radius: 10px; padding: 13px 16px; background: #f9fafc; display: flex; align-items: center; gap: 10px; min-height: 46px; transition: border-color .15s, background-color .15s; }
    .tex-switch-group:has(.form-check-input:checked) { border-color: #cfd8ea; background: var(--tex-info-bg); }
    .tex-switch-group .form-check-input { width: 2.4em; height: 1.3em; cursor: pointer; flex-shrink: 0; margin-top: 0; }
    .tex-switch-group .form-check-input:checked { background-color: var(--tex-navy); border-color: var(--tex-navy); }
    .tex-switch-group .form-check-input:focus { box-shadow: 0 0 0 4px rgba(20, 33, 61, .10); }
    .tex-switch-group .form-check-label { font-size: 13.5px; font-weight: 600; color: var(--tex-navy); cursor: pointer; margin: 0; }

    /* ── Filter toolbar (question bank) ── */
    .tex-filter-bar { background: #f9fafc; border: 1px solid var(--tex-border); border-radius: 12px; padding: 14px; margin-bottom: 16px; }
    .tex-filter-bar .form-control,
    .tex-filter-bar .form-select { min-height: 42px; border: 1.5px solid var(--tex-border); border-radius: 9px; font-size: 13.5px; background: #fff; }
    .tex-filter-bar .form-control:focus,
    .tex-filter-bar .form-select:focus { border-color: var(--tex-navy); box-shadow: 0 0 0 3px rgba(20, 33, 61, .10); }
    .tex-search-wrap { position: relative; }
    .tex-search-wrap i { position: absolute; inset-inline-start: 14px; top: 50%; transform: translateY(-50%); color: var(--tex-muted); font-size: 15px; pointer-events: none; }
    .tex-search-wrap .form-control { padding-inline-start: 38px; }
    .tex-filter-count { font-size: 12.5px; color: var(--tex-muted); font-weight: 600; white-space: nowrap; }
    .tex-filter-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .tex-filter-actions .btn { min-height: 40px; border-radius: 9px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }

    /* ── Question picker: table-like rows ── */
    .tex-question-list { border: 1px solid var(--tex-border); border-radius: 12px; overflow: hidden; }
    .tex-question-list__head {
        display: flex; align-items: center; gap: 12px; padding: 10px 16px; background: #f4f6fb;
        border-bottom: 1px solid var(--tex-border); font-size: 11.5px; font-weight: 700; color: var(--tex-muted); text-transform: uppercase;
    }
    .tex-question-scroll { max-height: 380px; overflow-y: auto; }
    .tex-question-row {
        border: none; border-bottom: 1px solid var(--tex-border); border-radius: 0; padding: 13px 16px; margin-bottom: 0;
        display: flex; align-items: center; gap: 12px; cursor: pointer; transition: background-color .15s ease; min-height: 44px;
    }
    .tex-question-list .tex-question-row:last-child { border-bottom: none; }
    .tex-question-row:hover { background: #f9fafc; }
    .tex-question-row:has(.tex-question-checkbox:checked) { background: var(--tex-info-bg); }
    .tex-question-row:has(.tex-question-checkbox:focus-visible) { outline: 2px solid var(--tex-navy); outline-offset: -2px; }
    .tex-question-row__num { width: 26px; flex-shrink: 0; text-align: center; font-size: 12px; font-weight: 700; color: var(--tex-muted); }
    .tex-question-checkbox { width: 19px; height: 19px; flex-shrink: 0; cursor: pointer; accent-color: var(--tex-navy); }
    .tex-question-row__text { flex: 1; font-size: 14px; color: var(--tex-navy); min-width: 0; }
    .tex-question-row__badges { display: flex; gap: 6px; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }
    /* jQuery's show()/toggle() restores an inline "display" that fights with the flex
       layout above (label's browser default is "inline"), misaligning the row's
       checkbox/text/badges. Toggle via this class instead of jQuery show/hide. */
    .tex-question-row.tex-row-hidden { display: none !important; }

    .tex-summary { background: var(--tex-info-bg); border: 1px solid #b9e9f2; border-radius: 10px; padding: 14px 18px; display: flex; gap: 30px; flex-wrap: wrap; align-items: center; }
    .tex-summary__item { text-align: center; }
    .tex-summary__value { font-weight: 800; font-size: 18px; color: var(--tex-navy); }
    .tex-summary__label { font-size: 12px; color: var(--tex-muted); }

    .tex-form-actions { display: flex; gap: 10px; margin-bottom: 40px; }
    .tex-btn-lg { padding: 12px 34px; font-size: 15px; min-height: 48px; }

    @media (max-width: 767px) {
        .tex-section__body { padding: 16px; }
        .tex-question-list__head { display: none; }
    }
</style>
