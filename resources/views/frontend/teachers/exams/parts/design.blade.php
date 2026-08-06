{{-- Shared design system for the teacher Examination Center pages (My Exams, Grading/Review,
     Attempts, Groups Report). Keeps visual language consistent across all four screens. --}}
<style>
    :root {
        --tex-navy: #14213d;
        --tex-navy-light: #1c2d54;
        --tex-accent: #ffcc00;
        --tex-border: #e7eaf0;
        --tex-muted: #7a8296;
        --tex-bg: #f4f6fb;
        --tex-success: #1e8e5a;
        --tex-success-bg: #e6f6ec;
        --tex-danger: #c0392b;
        --tex-danger-bg: #fdeaea;
        --tex-warning: #a16207;
        --tex-warning-bg: #fff7e0;
        --tex-info: #0891b2;
        --tex-info-bg: #e0f7fa;
    }

    /* ── Page header ── */
    .tex-page-head { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px; margin-bottom: 22px; }
    .tex-page-head__icon {
        width: 46px; height: 46px; border-radius: 12px; background: var(--tex-navy);
        color: var(--tex-accent); display: inline-flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .tex-page-head__title { display: flex; align-items: center; gap: 12px; }
    .tex-page-head h4 { margin: 0 0 2px; font-weight: 800; color: var(--tex-navy); }
    .tex-page-head p { margin: 0; color: var(--tex-muted); font-size: 13.5px; }
    .tex-btn-primary {
        background: var(--tex-navy); color: #fff; border: none; border-radius: 10px;
        padding: 10px 22px; font-weight: 700; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;
        transition: background .15s, transform .1s; text-decoration: none;
    }
    .tex-btn-primary:hover { background: var(--tex-navy-light); color: #fff; transform: translateY(-1px); }

    /* ── Stat chips row ── */
    .tex-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 14px; margin-bottom: 24px; }
    .tex-stat { background: #fff; border: 1px solid var(--tex-border); border-radius: 12px; padding: 16px 18px; display: flex; align-items: center; gap: 12px; }
    .tex-stat__icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
    .tex-stat__value { font-size: 20px; font-weight: 800; color: var(--tex-navy); line-height: 1.1; }
    .tex-stat__label { font-size: 12px; color: var(--tex-muted); margin-top: 2px; }

    /* ── Section card ── */
    .tex-card { background: #fff; border: 1px solid var(--tex-border); border-radius: 14px; margin-bottom: 22px; overflow: hidden; }
    .tex-card__header { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 16px 20px; background: #f9fafc; border-bottom: 1px solid var(--tex-border); flex-wrap: wrap; }
    .tex-card__header-title { display: flex; align-items: center; gap: 10px; font-weight: 700; color: var(--tex-navy); margin: 0; }
    .tex-card__header-title i { color: var(--tex-navy); font-size: 18px; }
    .tex-card__body { padding: 20px; }

    /* ── Table ── */
    .tex-table thead th { color: var(--tex-muted); font-size: 12px; text-transform: uppercase; font-weight: 700; border-bottom: 2px solid var(--tex-border); padding: 10px 12px; }
    .tex-table tbody td { padding: 12px; vertical-align: middle; border-bottom: 1px solid var(--tex-border); font-size: 14px; }
    .tex-table tbody tr:hover { background: #f9fafc; }
    .tex-table tbody tr:last-child td { border-bottom: none; }

    /* ── Badges/pills ── */
    .tex-pill { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .tex-pill--success { background: var(--tex-success-bg); color: var(--tex-success); }
    .tex-pill--danger { background: var(--tex-danger-bg); color: var(--tex-danger); }
    .tex-pill--warning { background: var(--tex-warning-bg); color: var(--tex-warning); }
    .tex-pill--info { background: var(--tex-info-bg); color: var(--tex-info); }
    .tex-pill--muted { background: #eef1f5; color: #4b5568; }
    .tex-pill-btn { border: none; cursor: pointer; }

    /* ── Action buttons ── */
    .tex-icon-btn {
        width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--tex-border); background: #fff; color: var(--tex-navy); text-decoration: none; transition: background .15s, border-color .15s;
    }
    .tex-icon-btn:hover { background: #f2f4fa; color: var(--tex-navy); }
    .tex-icon-btn--danger { color: var(--tex-danger); }
    .tex-icon-btn--danger:hover { background: var(--tex-danger-bg); border-color: #f5c6cb; }
    .tex-icon-btn--success { color: var(--tex-success); }
    .tex-icon-btn--success:hover { background: var(--tex-success-bg); border-color: #b7e4c7; }

    /* ── Empty state ── */
    .tex-empty { text-align: center; padding: 48px 20px; color: var(--tex-muted); }
    .tex-empty i { font-size: 40px; color: #c7ccd6; display: block; margin-bottom: 12px; }
    .tex-empty p { margin: 0; font-size: 14px; }

    /* ── Search/filter bar ── */
    .tex-toolbar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .tex-toolbar .form-control, .tex-toolbar .form-select { border-radius: 8px; font-size: 13.5px; }

    /* ── Loading spinner (shared by modals) ── */
    .tex-spinner-wrap { text-align: center; padding: 40px 0; }
</style>
