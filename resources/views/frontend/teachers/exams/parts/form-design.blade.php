{{-- Extra styles for the exam add/edit form, layered on top of the shared tex-* design
     system (parts/design.blade.php) so the "إضافة/تعديل امتحان" screens match the rest
     of the Examination Center visually. --}}
<style>
    .tex-section { background: #fff; border: 1px solid var(--tex-border); border-radius: 14px; margin-bottom: 22px; overflow: hidden; }
    .tex-section__header { display: flex; align-items: center; gap: 10px; padding: 16px 20px; background: #f9fafc; border-bottom: 1px solid var(--tex-border); }
    .tex-section__header i { font-size: 19px; color: var(--tex-navy); }
    .tex-section__header h6 { margin: 0; font-weight: 700; color: var(--tex-navy); }
    .tex-section__body { padding: 20px; }
    .tex-label { font-weight: 600; font-size: 13.5px; color: #4a5268; margin-bottom: 6px; display: block; }

    .tex-question-row { border: 1px solid var(--tex-border); border-radius: 10px; padding: 10px 14px; margin-bottom: 8px; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: background .15s; }
    .tex-question-row:hover { background: #f9fafc; }
    .tex-question-row__text { flex: 1; font-size: 14px; }

    .tex-summary { background: var(--tex-info-bg); border: 1px solid #b9e9f2; border-radius: 10px; padding: 14px 18px; display: flex; gap: 30px; flex-wrap: wrap; align-items: center; }
    .tex-summary__item { text-align: center; }
    .tex-summary__value { font-weight: 800; font-size: 18px; color: var(--tex-navy); }
    .tex-summary__label { font-size: 12px; color: var(--tex-muted); }

    .tex-form-actions { display: flex; gap: 10px; margin-bottom: 40px; }
    .tex-btn-lg { padding: 12px 34px; font-size: 15px; }

    .tex-switch-group { border: 1px solid var(--tex-border); border-radius: 10px; padding: 12px 16px; background: #f9fafc; }
</style>
