@extends('frontend.layouts.master')
@section('title', 'Registration | Oxford Language Center')
@section('content')

    <!-- Editorial Fonts (Scholar's Atelier aesthetic) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght,SOFT,WONK@9..144,400;9..144,500;9..144,600;9..144,700;9..144,800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom Wizard Styles -->
    <link rel="stylesheet" href="{{ asset('assets/oxford/css/wizard.css') }}">
    <style>
        /* ========================================================
           Oxford Registration – Modern UI Design System
           Brand: Navy #003366 + Gold #ffcc00 (official palette)
           Unified scale: 4 radii, 3 shadows, 8-pt spacing.
           ======================================================== */
        :root {
            --ink: #003366;          /* Oxford Navy (brand) */
            --ink-2: #002a55;        /* Deeper navy for hover/active */
            --ink-3: #0a4486;        /* Lighter navy variant */
            --gold: #ffcc00;         /* Brand gold (accent only) */
            --gold-2: #ffd633;       /* Lighter gold for hover */
            --gold-soft: #fff9e0;    /* Gold tint background */

            --paper: #ffffff;
            --canvas: #f7f8fb;       /* Off-white page background */
            --surface: #fafbfd;      /* Card surface */
            --rule: #e5e9f0;         /* Borders / dividers */
            --rule-2: #eef1f5;       /* Softer rules */
            --ink-soft: #6c7689;     /* Muted body text */
            --ink-faint: #a1a8b8;    /* Placeholder text */

            /* Radius scale */
            --r-sm: 8px;
            --r-md: 14px;
            --r-lg: 20px;
            --r-pill: 999px;

            /* Elevation scale */
            --sh-1: 0 1px 2px rgba(0,51,102,0.04), 0 2px 6px rgba(0,51,102,0.04);
            --sh-2: 0 6px 16px rgba(0,51,102,0.08), 0 2px 4px rgba(0,51,102,0.04);
            --sh-3: 0 20px 50px rgba(0,51,102,0.14);

            /* Typography */
            --font-display: 'Fraunces', 'Playfair Display', Georgia, serif;
            --font-body: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;

            /* Type scale — bumped one notch for modern presence */
            --t-xs: 0.82rem;     /* 13px - eyebrow labels */
            --t-sm: 0.92rem;     /* 14.5px - secondary text */
            --t-base: 1rem;      /* 16px - body */
            --t-md: 1.08rem;     /* 17.2px - input text */
            --t-lg: 1.35rem;     /* 21.5px - h5 */
            --t-xl: 1.85rem;     /* 29.5px - h4 / step heading */
            --t-2xl: 2.35rem;    /* 37.5px - h3 */
            --t-3xl: 3rem;       /* 48px - hero */

            /* Spacing scale (4-pt base) */
            --sp-1: 4px;
            --sp-2: 8px;
            --sp-3: 12px;
            --sp-4: 16px;
            --sp-5: 20px;
            --sp-6: 28px;
            --sp-7: 36px;
            --sp-8: 48px;
            --danger: #d93025;
            --success: #1fa34a;
        }

        /* Z-index sanity */
        .app-header, header, nav { z-index: 1000 !important; }

        .registration-page-wrapper {
            position: relative;
            z-index: 1;
            background: var(--canvas);
            font-family: var(--font-body);
            color: var(--ink);
            font-size: var(--t-base);
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        /* Headings get serif display */
        .registration-page-wrapper h1,
        .registration-page-wrapper h2,
        .registration-page-wrapper h3,
        .registration-page-wrapper h4 {
            font-family: var(--font-display);
            font-weight: 600;
            letter-spacing: -0.015em;
            color: var(--ink);
            font-variation-settings: "opsz" 32, "SOFT" 30;
        }

        /* ============ HERO ============ */
        .book-course-hero { min-height: 380px; }
        .book-course-hero::before {
            background: linear-gradient(135deg, rgba(0,51,102,0.92) 0%, rgba(0,42,85,0.88) 100%);
        }
        /* Subtle gold grain pattern over hero */
        .book-course-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(255,204,0,0.08) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(255,204,0,0.06) 0%, transparent 40%);
            pointer-events: none;
            z-index: 1;
        }
        .hero-content h1 {
            font-family: var(--font-display) !important;
            font-size: clamp(2.25rem, 5vw, var(--t-3xl)) !important;
            font-weight: 500 !important;
            font-style: italic;
            letter-spacing: -0.025em !important;
            text-transform: none !important;
            color: #fff !important;
            margin-bottom: 14px !important;
            font-variation-settings: "opsz" 144;
        }
        .hero-content h1::after {
            content: '';
            display: block;
            width: 64px;
            height: 3px;
            background: var(--gold);
            margin: 18px auto 0;
            border-radius: 2px;
        }
        .hero-content p {
            font-family: var(--font-body) !important;
            font-weight: 400 !important;
            font-size: var(--t-md) !important;
            opacity: 0.88;
            letter-spacing: 0.005em;
            max-width: 540px;
            margin: 0 auto !important;
        }

        /* ============ Wizard card ============ */
        .wizard-main-card {
            background: var(--paper) !important;
            border: 1px solid var(--rule) !important;
            border-radius: var(--r-lg) !important;
            box-shadow: var(--sh-3) !important;
            max-width: 1060px !important;
            position: relative;
        }
        /* Subtle gold corner brackets — restraint, not noise */
        .wizard-main-card::before,
        .wizard-main-card::after {
            content: '';
            position: absolute;
            width: 24px;
            height: 24px;
            border: 2px solid var(--gold);
            opacity: 0.5;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .wizard-main-card::before { top: 12px; left: 12px; border-right: none; border-bottom: none; border-radius: 3px 0 0 0; }
        .wizard-main-card::after  { bottom: 12px; right: 12px; border-left: none; border-top: none; border-radius: 0 0 3px 0; }

        .wizard-body::before { display: none !important; }
        .wizard-body { padding: 64px 72px !important; min-height: 560px; }

        /* Equal-height row pairs — "elements facing each other" */
        .step-pane .row { row-gap: 1.75rem; }
        .step-pane { position: relative; }
        .step-pane .row > [class*="col-"] { display: flex; flex-direction: column; position: relative; }
        .step-pane .row > [class*="col-"] > .form-control,
        .step-pane .row > [class*="col-"] > textarea.form-control { flex: 1 0 auto; }

        /* ============ Step heading (bigger, more present) ============ */
        .step-pane h3.step-heading,
        .step-pane h4.step-heading,
        .step-pane h4 {
            font-family: var(--font-display) !important;
            font-weight: 500 !important;
            font-style: italic;
            font-size: var(--t-xl) !important;
            color: var(--ink) !important;
            border-bottom: 1px solid var(--rule) !important;
            padding-bottom: 20px !important;
            margin-bottom: 40px !important;
            display: flex;
            align-items: center;
            gap: 18px;
            letter-spacing: -0.02em !important;
            position: relative;
            line-height: 1.2;
        }
        .step-pane h3.step-heading::after,
        .step-pane h4.step-heading::after,
        .step-pane h4::after {
            width: 64px !important;
            height: 3px !important;
            background: var(--gold) !important;
            bottom: -2px !important;
            border-radius: 2px;
        }
        .step-pane h3.step-heading i,
        .step-pane h4.step-heading i,
        .step-pane h4 i {
            color: var(--ink) !important;
            font-size: 1.6rem !important;
            font-style: normal;
            filter: none !important;
            margin-right: 0 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            background: var(--gold-soft);
            border: 1.5px solid var(--gold);
            border-radius: var(--r-md);
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(255, 204, 0, 0.18);
        }

        /* ============ Form labels — bigger, crisp, easy to read ============
           Larger sentence-case bold navy + matching gold icon. Scannable at a glance. */
        .form-label {
            font-family: var(--font-body) !important;
            font-size: 1.1rem !important;        /* 17.6px — clearly readable */
            font-weight: 700 !important;
            color: var(--ink) !important;
            text-transform: none !important;
            letter-spacing: -0.005em !important;
            margin-bottom: 12px !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            line-height: 1.4;
        }
        .form-label i {
            color: var(--gold) !important;
            font-size: 1.25rem !important;       /* scales with label */
            width: 26px;
            height: 26px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent !important;
            border-radius: 0;
            margin-right: 0 !important;
            transition: color 0.2s ease, transform 0.2s ease;
            flex-shrink: 0;
        }
        [dir="rtl"] .form-label i { margin-left: 0 !important; }

        /* Required asterisk gets its own subtle treatment */
        .form-label::after {
            content: '';
        }

        /* Field icon reacts when its input is focused */
        .form-label:has(+ .form-control:focus) i,
        .form-label:has(+ * .form-control:focus) i {
            color: var(--ink) !important;
            transform: scale(1.1);
        }

        /* ============ Form controls — comfortable touch + clean type ============ */
        .form-control {
            height: 54px !important;
            font-family: var(--font-body) !important;
            font-size: 1.02rem !important;       /* 16.3px — easy to read */
            font-weight: 500 !important;
            background: var(--paper) !important;
            border: 1.5px solid var(--rule) !important;
            border-radius: var(--r-sm) !important;
            padding: 0 18px !important;
            color: var(--ink) !important;
            line-height: 1.5;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }
        textarea.form-control {
            height: auto !important;
            padding: 15px 18px !important;
            line-height: 1.6;
            min-height: 100px;
            font-weight: 400 !important;
        }
        .form-control::placeholder {
            color: var(--ink-faint);
            font-weight: 400 !important;
            opacity: 1;
        }

        /* Inputs that accept long content (address, notes, names) get a softer left bar */
        .form-control[name="address"],
        .form-control[name="general_notes"] { line-height: 1.6; }

        /* Date input — align browser-rendered calendar icon */
        input[type="date"].form-control { padding-right: 14px !important; }
        [dir="rtl"] input[type="date"].form-control { padding-right: 18px !important; padding-left: 14px !important; }
        .form-control:hover { border-color: #cfd5e0 !important; }
        .form-control:focus {
            background: var(--paper) !important;
            border-color: var(--ink) !important;
            box-shadow: 0 0 0 3px rgba(0,51,102,0.12) !important;
            outline: none !important;
        }
        .form-control::placeholder { color: var(--ink-faint); opacity: 1; }

        /* Invalid field styles without affecting layout */
        .form-control.is-invalid {
            border-color: var(--danger) !important;
            box-shadow: none !important;
        }
        .step-pane .row > [class*="col-"] .invalid-feedback {
            position: absolute;
            left: 0;
            top: 100%;
            margin-top: 8px;
            color: var(--danger);
            display: block;
            background: transparent;
            font-size: 0.95rem;
            line-height: 1.2;
            z-index: 70;
            padding-left: 10px; /* light left padding */
            border-left: 4px solid #e6ccb3; /* light brown accent */
            margin-bottom: 6px; /* small bottom margin */
        }
        .form-control.is-valid {
            border-color: var(--success) !important;
            box-shadow: none !important;
        }
        .step-pane .row > [class*="col-"] .valid-feedback {
            position: absolute;
            left: 0;
            top: 100%;
            margin-top: 8px;
            color: var(--success);
            display: block;
            background: transparent;
            font-size: 0.95rem;
            line-height: 1.2;
            z-index: 70;
            margin-bottom: 6px; /* small bottom margin */
        }
        /* Modern check icon inside input */
        .input-status-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            z-index: 80;
        }
        .input-status-icon.success {
            background: var(--success);
            color: #fff;
        }
        .input-status-icon.error {
            background: var(--danger);
            color: #fff;
        }

        select.form-control {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 12 12'%3E%3Cpath fill='%23003366' d='M6 8.825L1.175 4 2.59 2.585 6 6l3.415-3.415L10.825 4z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 16px center !important;
            padding-right: 38px !important;
        }
        [dir="rtl"] select.form-control { background-position: left 16px center !important; padding-right: 16px !important; padding-left: 38px !important; }

        .mb-4 { margin-bottom: 1.5rem !important; }

        /* Underage banner — sits inline in the grid, no absolute positioning */
        #underage-banner { display: none; }

        /* ============ Cards (interaction-card, scheduling, etc.) ============ */
        .interaction-card {
            background: var(--surface) !important;
            border: 1px solid var(--rule) !important;
            border-radius: var(--r-md) !important;
            box-shadow: none !important;
            padding: 22px !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .interaction-card:hover {
            transform: none !important;
            box-shadow: var(--sh-1) !important;
            border-color: #d4dae4 !important;
        }
        .interaction-card h5 {
            font-family: var(--font-display) !important;
            font-weight: 500 !important;
            font-style: italic;
            font-size: var(--t-lg) !important;
            color: var(--ink) !important;
            letter-spacing: -0.015em;
        }
        .interaction-card h5 i {
            margin-right: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ============ Radio pills (yes/no) ============ */
        .custom-radio-pill span {
            border: 1.5px solid var(--rule) !important;
            border-radius: var(--r-pill) !important;
            background: var(--paper) !important;
            font-family: var(--font-body) !important;
            font-weight: 600 !important;
            font-size: var(--t-sm) !important;
            color: var(--ink) !important;
            padding: 8px 22px !important;
            transition: all 0.2s ease;
        }
        .custom-radio-pill:hover span { border-color: var(--ink); }
        .custom-radio-pill input:checked + span {
            background: var(--ink) !important;
            color: #fff !important;
            border-color: var(--ink) !important;
            box-shadow: 0 4px 10px rgba(0,51,102,0.18);
        }

        /* ============ Level circles (A0, A1, ...) ============ */
        .level-radio-item {
            flex: 0 0 auto !important;
        }
        .level-radio-item label {
            width: 64px !important;
            height: 64px !important;
            border-radius: 50% !important;
            font-family: var(--font-display) !important;
            font-weight: 600 !important;
            font-style: italic;
            font-size: 1.05rem !important;
            padding: 0 !important;
            background: var(--paper) !important;
            border: 1.5px solid var(--rule) !important;
            color: var(--ink) !important;
            transition: all 0.2s ease;
            letter-spacing: -0.01em;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            position: relative;
            line-height: 1;
        }
        .level-radio-item label:hover {
            border-color: var(--ink);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,51,102,0.12);
        }
        .btn-check:checked + label {
            background: var(--ink) !important;
            border-color: var(--ink) !important;
            color: #fff !important;
            box-shadow: 0 6px 16px rgba(0,51,102,0.28) !important;
            transform: translateY(-2px);
        }
        .btn-check:checked + label::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 1.5px solid var(--gold);
            pointer-events: none;
        }
        .level-radio-group {
            justify-content: flex-start;
            gap: 12px !important;
        }
        @media (max-width: 576px) {
            .level-radio-item label { width: 54px !important; height: 54px !important; font-size: 0.95rem !important; }
        }

        /* ============ Schedule split card (Days | Time) ============ */
        .scheduling-card.scheduling-split {
            background: var(--paper);
            border: 1px solid var(--rule);
            border-radius: var(--r-md);
            box-shadow: var(--sh-1);
            padding: 28px 24px;
            display: grid;
            grid-template-columns: 1fr 1px 1fr;
            gap: 28px;
            align-items: stretch;
            position: relative;
            overflow: hidden;
        }
        .scheduling-card.scheduling-split::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,204,0,0.04) 0%, transparent 40%, transparent 60%, rgba(0,51,102,0.025) 100%);
            pointer-events: none;
        }
        .scheduling-split-col {
            display: flex;
            flex-direction: column;
            min-width: 0;
            position: relative;
            z-index: 1;
        }
        .scheduling-split-divider {
            width: 1px;
            background: linear-gradient(180deg, transparent 0%, var(--rule) 15%, var(--rule) 85%, transparent 100%);
            position: relative;
        }
        .scheduling-split-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            background: var(--gold);
            border: 2px solid var(--paper);
            border-radius: 50%;
            box-shadow: 0 0 0 1px var(--gold);
        }
        .scheduling-sub-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 14px;
            margin-bottom: 18px;
            border-bottom: 1px solid var(--rule);
        }
        .scheduling-sub-header i {
            width: 38px;
            height: 38px;
            border-radius: var(--r-sm);
            background: var(--gold-soft);
            border: 1px solid var(--gold);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--ink);
            font-size: 1.15rem;
            flex-shrink: 0;
        }
        .scheduling-sub-header span {
            font-family: var(--font-display);
            font-weight: 600;
            color: var(--ink);
            font-size: 1.1rem;
            font-style: italic;
            letter-spacing: -0.005em;
            text-transform: none;
        }
        .scheduling-sub-header span small {
            font-family: var(--font-body);
            font-style: normal;
            font-weight: 700;
            font-size: 0.72rem;
            color: var(--ink-soft);
            letter-spacing: 0.14em;
            margin-left: 6px;
            text-transform: uppercase;
        }

        .schedule-options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 10px;
        }
        .schedule-option {
            position: relative;
            background: var(--surface);
            border: 1.5px solid var(--rule);
            border-radius: var(--r-sm);
            padding: 12px 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 48px;
            margin: 0;
        }
        .schedule-option input {
            position: absolute;
            opacity: 0;
            inset: 0;
            cursor: pointer;
        }
        .schedule-option:hover {
            background: var(--paper);
            border-color: var(--ink);
            transform: translateY(-1px);
            box-shadow: var(--sh-1);
        }
        .schedule-option:has(input:checked) {
            background: var(--ink);
            border-color: var(--ink);
            box-shadow: 0 6px 16px rgba(0,51,102,0.22);
            transform: translateY(-1px);
        }
        .schedule-option:has(input:checked)::after {
            content: '\F26B';
            font-family: 'bootstrap-icons';
            position: absolute;
            top: 4px;
            right: 6px;
            color: var(--gold);
            font-size: 0.85rem;
            line-height: 1;
        }
        [dir="rtl"] .schedule-option:has(input:checked)::after { right: auto; left: 6px; }
        .schedule-option:has(input:checked) .option-text { color: #fff !important; }
        .option-text {
            font-family: var(--font-body);
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--ink);
            line-height: 1.3;
        }

        @media (max-width: 768px) {
            .scheduling-card.scheduling-split {
                grid-template-columns: 1fr;
                gap: 18px;
                padding: 22px 18px;
            }
            .scheduling-split-divider {
                width: 100%;
                height: 1px;
                background: linear-gradient(90deg, transparent 0%, var(--rule) 15%, var(--rule) 85%, transparent 100%);
            }
            .scheduling-split-divider::before { top: 50%; left: 50%; }
        }

        /* ============ Payment Method Cards ============ */
        .payment-methods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 14px;
        }
        .payment-method-card {
            border: 1.5px solid var(--rule) !important;
            border-radius: var(--r-md);
            padding: 26px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--paper);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .payment-method-card:hover {
            border-color: var(--ink) !important;
            box-shadow: var(--sh-2);
            transform: translateY(-3px);
        }
        .payment-method-card.active {
            border-color: var(--ink) !important;
            background: linear-gradient(180deg, var(--paper) 0%, var(--gold-soft) 100%);
            box-shadow: 0 10px 28px rgba(0,51,102,0.14);
        }
        .payment-method-card.active::before { display: none; }
        .payment-method-card.active::after {
            content: '\F272';
            font-family: 'bootstrap-icons';
            position: absolute;
            top: 10px; right: 10px;
            background: var(--gold);
            color: var(--ink);
            width: 24px; height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0,51,102,0.15);
        }
        [dir="rtl"] .payment-method-card.active::after { right: auto; left: 10px; }
        .payment-method-card i {
            font-size: 2.4rem;
            color: #b8c0cf;
            transition: all 0.25s;
        }
        .payment-method-card.active i { color: var(--ink); transform: scale(1.05); }
        .payment-method-card img.payment-logo {
            width: 56px; height: 56px;
            object-fit: contain;
            border-radius: var(--r-sm);
            padding: 4px;
            background: var(--paper);
            box-shadow: var(--sh-1);
            transition: all 0.25s;
        }
        .payment-method-card.active img.payment-logo {
            box-shadow: 0 6px 16px rgba(0,51,102,0.16);
        }
        .payment-method-card span {
            font-family: var(--font-body);
            font-weight: 700;
            color: var(--ink);
            font-size: 1rem;
            letter-spacing: 0.01em;
        }
        .payment-method-card i { font-size: 2.6rem !important; }

        /* ============ Payment Plan (mode selector) ============ */
        .payment-mode-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
        }
        .payment-mode-option {
            position: relative;
            background: var(--paper);
            border: 1.5px solid var(--rule);
            border-radius: var(--r-md);
            padding: 20px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            min-height: 130px;
        }
        .payment-mode-option:hover {
            border-color: var(--ink);
            transform: translateY(-2px);
            box-shadow: var(--sh-2);
        }
        .payment-mode-option i {
            font-size: 1.9rem;
            color: var(--ink-soft);
            transition: all 0.25s ease;
            margin-bottom: 4px;
        }
        .payment-mode-option .pm-title {
            font-family: var(--font-display);
            font-style: italic;
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--ink);
            line-height: 1.2;
        }
        .payment-mode-option .pm-sub {
            font-family: var(--font-body);
            font-size: 0.78rem;
            color: var(--ink-soft);
            letter-spacing: 0.04em;
        }
        .payment-mode-option.active {
            border-color: var(--ink);
            background: linear-gradient(180deg, var(--paper) 0%, var(--gold-soft) 100%);
            box-shadow: 0 10px 26px rgba(0,51,102,0.14);
        }
        .payment-mode-option.active i {
            color: var(--ink);
            transform: scale(1.08);
        }
        .payment-mode-option.active::after {
            content: '\F272';
            font-family: 'bootstrap-icons';
            position: absolute;
            top: 8px;
            right: 10px;
            width: 22px;
            height: 22px;
            background: var(--gold);
            color: var(--ink);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0,51,102,0.15);
        }
        [dir="rtl"] .payment-mode-option.active::after { right: auto; left: 10px; }

        /* ============ Payment notes / hints ============ */
        .payment-note {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px;
            background: linear-gradient(135deg, var(--gold-soft) 0%, var(--paper) 100%);
            border: 1.5px solid var(--gold);
            border-left-width: 5px;
            border-radius: var(--r-md);
            box-shadow: var(--sh-1);
        }
        .payment-note-icon {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--gold);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--ink);
            font-size: 1.35rem;
            box-shadow: 0 4px 10px rgba(255, 204, 0, 0.35);
        }
        .payment-note-body { flex: 1; min-width: 0; }
        .payment-note-title {
            font-family: var(--font-display);
            font-style: italic;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--ink);
            line-height: 1.3;
            margin-bottom: 4px;
        }
        .payment-note-amount {
            font-family: var(--font-display);
            font-style: italic;
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--ink);
            line-height: 1.1;
            letter-spacing: -0.01em;
            margin-bottom: 6px;
        }
        .payment-note-amount small {
            font-family: var(--font-body);
            font-style: normal;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--ink-soft);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-left: 4px;
        }
        .payment-note-desc {
            font-family: var(--font-body);
            font-size: 0.95rem;
            color: var(--ink-soft);
            line-height: 1.6;
        }
        .payment-note-danger {
            background: linear-gradient(135deg, #fef0f0 0%, var(--paper) 100%);
            border-color: var(--danger);
        }
        .payment-note-danger .payment-note-icon {
            background: var(--danger);
            color: #fff;
            box-shadow: 0 4px 10px rgba(217, 48, 37, 0.35);
        }
        .payment-note-danger .payment-note-title { color: var(--danger); }
        @media (max-width: 576px) {
            .payment-note { padding: 14px 14px; gap: 12px; }
            .payment-note-icon { width: 38px; height: 38px; font-size: 1.15rem; }
            .payment-note-title { font-size: 1rem; }
            .payment-note-amount { font-size: 1.35rem; }
            .payment-note-desc { font-size: 0.88rem; }
        }

        /* ============ Remaining Balance display ============ */
        .remaining-balance-display {
            height: 54px;
            background: linear-gradient(180deg, var(--surface) 0%, var(--gold-soft) 100%);
            border: 1.5px solid var(--gold);
            border-radius: var(--r-sm);
            padding: 0 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            box-shadow: inset 0 1px 2px rgba(0,51,102,0.04);
            text-align: center;
        }
        .remaining-balance-display .rb-amount {
            font-family: var(--font-display);
            font-style: italic;
            font-weight: 700;
            font-size: 1.45rem;
            color: var(--ink);
            letter-spacing: -0.01em;
            line-height: 1;
        }
        .remaining-balance-display .rb-currency {
            font-family: var(--font-body);
            font-weight: 700;
            font-size: 0.78rem;
            color: var(--ink-soft);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        @media (max-width: 576px) {
            .remaining-balance-display { height: 50px; }
            .remaining-balance-display .rb-amount { font-size: 1.25rem; }
        }

        /* ============ Credentials display ============ */
        .credentials-container {
            margin: 26px 0;
            padding: 22px 4px;
            border-top: 1px solid var(--rule);
            border-bottom: 1px solid var(--rule);
        }
        .credential-card {
            display: grid;
            grid-template-columns: 48px 1fr auto;
            align-items: center;
            gap: 16px;
            background: var(--surface);
            border: 1px solid var(--rule);
            border-radius: var(--r-md);
            padding: 14px 16px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease, background 0.2s ease;
        }
        .credential-card + .credential-card { margin-top: 10px; }
        .credential-card:hover {
            background: var(--paper);
            border-color: var(--ink);
            box-shadow: var(--sh-1);
        }
        .credential-card .credential-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--r-sm);
            background: var(--gold-soft);
            border: 1px solid var(--gold);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--ink);
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        .credential-card .credential-body {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
            overflow: hidden;
        }
        .credential-card .credential-label {
            color: var(--ink-soft) !important;
            font-family: var(--font-body) !important;
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 0.12em !important;
            line-height: 1.2;
        }
        .credential-card .credential-value {
            color: var(--ink) !important;
            font-family: var(--font-display) !important;
            font-weight: 600 !important;
            font-style: italic;
            font-size: 1.15rem !important;
            letter-spacing: -0.005em;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-all;
        }
        .credential-card .credential-copy-btn {
            background: var(--paper);
            border: 1.5px solid var(--rule);
            color: var(--ink);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .credential-card .credential-copy-btn:hover {
            background: var(--ink);
            border-color: var(--ink);
            color: var(--gold);
            transform: scale(1.05);
        }
        @media (max-width: 576px) {
            .credential-card { grid-template-columns: 40px 1fr auto; gap: 12px; padding: 12px 14px; }
            .credential-card .credential-icon { width: 38px; height: 38px; font-size: 1.05rem; }
            .credential-card .credential-value { font-size: 1rem !important; }
            .credential-card .credential-copy-btn { width: 36px; height: 36px; }
        }

        /* ============ Wizard footer / buttons ============ */
        .wizard-footer {
            background: var(--surface) !important;
            border-top: 1px solid var(--rule) !important;
            padding: 26px 72px !important;
        }
        .btn-wizard {
            font-family: var(--font-body) !important;
            font-weight: 700 !important;
            letter-spacing: 0.07em !important;
            text-transform: uppercase;
            padding: 16px 40px !important;
            border-radius: var(--r-pill) !important;
            font-size: 0.92rem !important;
            transition: all 0.25s ease !important;
            border: none;
            min-width: 160px;
        }
        .btn-next {
            background: var(--ink) !important;
            color: #fff !important;
            box-shadow: 0 8px 20px rgba(0,51,102,0.25) !important;
            position: relative;
            overflow: hidden;
        }
        .btn-next::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,204,0,0.15), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }
        .btn-next:hover::before { transform: translateX(100%); }
        .btn-next:hover {
            background: var(--ink-2) !important;
            color: #fff !important;
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(0,51,102,0.32) !important;
        }
        .btn-prev {
            background: var(--paper) !important;
            color: var(--ink) !important;
            border: 1.5px solid var(--rule) !important;
        }
        .btn-prev:hover {
            background: var(--surface) !important;
            color: var(--ink) !important;
            border-color: var(--ink) !important;
        }

        /* ============ Fees table ============ */
        .table { font-family: var(--font-body); }
        #fees-breakdown-body td { padding: 18px 22px !important; border-color: var(--rule-2) !important; }
        #fees-breakdown-body .fw-bold {
            font-family: var(--font-display);
            font-weight: 500;
            font-style: italic;
            font-size: var(--t-base);
            color: var(--ink);
        }
        #fees-breakdown-body small { color: var(--ink-soft); font-size: var(--t-xs); }
        tfoot { background: var(--gold-soft) !important; }
        tfoot td { padding: 20px 22px !important; border-color: var(--gold) !important; }
        tfoot .text-primary {
            color: var(--ink) !important;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1.25rem !important;
        }

        /* ============ File upload ============ */
        .file-upload-container {
            border: 2px dashed var(--gold) !important;
            border-radius: var(--r-md) !important;
            padding: 36px 28px !important;
            background: var(--gold-soft) !important;
            box-shadow: none !important;
            transition: all 0.25s ease;
        }
        .file-upload-container:hover {
            background: var(--paper) !important;
            border-color: var(--ink) !important;
            transform: translateY(-2px) !important;
            box-shadow: var(--sh-1) !important;
        }
        .file-upload-container .bi-cloud-arrow-up {
            color: var(--gold) !important;
            font-size: 2.3rem !important;
            transition: color 0.25s ease;
        }
        .file-upload-container:hover .bi-cloud-arrow-up { color: var(--ink) !important; }
        .file-upload-container p {
            font-family: var(--font-body);
            color: var(--ink-soft);
            font-size: 1rem;
            margin-top: 8px !important;
        }
        @keyframes receiptShake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        .receipt-required-highlight {
            animation: receiptShake 0.5s ease;
            border-color: var(--danger) !important;
            background: #fef0f0 !important;
            box-shadow: 0 0 0 4px rgba(217, 48, 37, 0.18) !important;
        }
        .receipt-required-highlight .bi-cloud-arrow-up { color: var(--danger) !important; }

        /* ============ Step 5 finalize ============ */
        .agreement-box {
            background: var(--surface) !important;
            border: 1px solid var(--rule) !important;
            border-radius: var(--r-md) !important;
            padding: 18px !important;
        }
        .agreement-box:hover {
            border-color: var(--ink);
            background: var(--paper) !important;
        }
        .agreement-text .fw-bold {
            font-family: var(--font-display);
            font-weight: 500 !important;
            font-style: italic;
            font-size: 1.1rem !important;
            color: var(--ink);
        }
        .agreement-text p { font-size: var(--t-sm); color: var(--ink-soft); }

        /* ============ Program selection (entry cards) ============ */
        .program-card {
            border: 2px solid #fff !important;
            border-radius: var(--r-lg) !important;
            box-shadow: var(--sh-3) !important;
        }
        .program-name {
            font-family: var(--font-display) !important;
            font-weight: 600 !important;
            font-style: italic !important;
            text-transform: none !important;
            letter-spacing: -0.015em !important;
            font-size: 1.65rem !important;
            color: var(--gold) !important;
        }

        /* ============ Enrollment type cards ============ */
        .enroll-type-card {
            border: 1.5px solid var(--rule) !important;
            border-radius: var(--r-lg) !important;
            box-shadow: var(--sh-1) !important;
            padding: 36px 28px !important;
        }
        .enroll-type-card:hover {
            border-color: var(--ink) !important;
            box-shadow: var(--sh-2) !important;
            transform: translateY(-8px) !important;
        }
        .enroll-type-card.active {
            border-color: var(--ink) !important;
            background: var(--gold-soft) !important;
            box-shadow: var(--sh-2) !important;
        }
        .enroll-type-card h4 {
            font-family: var(--font-display) !important;
            font-weight: 500 !important;
            font-style: italic;
            font-size: 1.5rem !important;
            color: var(--ink) !important;
            letter-spacing: -0.015em;
        }
        .enroll-type-card p {
            color: var(--ink-soft) !important;
            font-family: var(--font-body);
            font-size: var(--t-base);
        }
        .enroll-type-card .type-icon {
            background: var(--gold-soft) !important;
            border-radius: var(--r-md) !important;
            color: var(--ink) !important;
            width: 68px !important;
            height: 68px !important;
            font-size: 1.9rem !important;
        }
        .enroll-type-card:hover .type-icon {
            background: var(--ink) !important;
            color: var(--gold) !important;
            transform: rotate(-6deg) !important;
        }
        .type-badge {
            font-family: var(--font-body);
            background: var(--ink) !important;
            font-weight: 700;
            font-size: var(--t-xs) !important;
            letter-spacing: 0.08em;
            padding: 7px 18px !important;
        }
        .type-badge.secondary { background: var(--gold) !important; color: var(--ink) !important; }

        /* ============ Hero section heading for program-selection ============ */
        #enrollment-type-container h2 {
            font-family: var(--font-display) !important;
            font-weight: 500 !important;
            font-style: italic;
            font-size: var(--t-2xl) !important;
            color: var(--ink) !important;
            letter-spacing: -0.02em;
        }
        #enrollment-type-container p { color: var(--ink-soft) !important; font-size: var(--t-md) !important; }

        /* ============ Helper alerts ============ */
        .alert {
            font-family: var(--font-body);
            border-radius: var(--r-sm) !important;
            font-size: var(--t-base);
        }

        /* ============ Subtle background grid behind wizard ============ */
        .registration-page-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 1px 1px, rgba(0,51,102,0.04) 1px, transparent 0);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }
        .registration-page-wrapper > * { position: relative; z-index: 1; }

        /* RTL safety for corner brackets */
        [dir="rtl"] .wizard-main-card::before { left: auto; right: 12px; border: 2px solid var(--gold); border-right: none; border-bottom: none; border-radius: 3px 0 0 0; }
        [dir="rtl"] .wizard-main-card::after { right: auto; left: 12px; border: 2px solid var(--gold); border-left: none; border-top: none; border-radius: 0 0 3px 0; }

        /* ============ Responsive (granular tiers) ============ */

        /* Large desktops & wide screens */
        @media (min-width: 1400px) {
            .wizard-main-card { max-width: 1140px !important; }
            .wizard-body { padding: 72px 88px !important; }
        }

        /* Medium desktops */
        @media (max-width: 1199px) {
            .wizard-body { padding: 56px 56px !important; }
            .step-pane h3.step-heading, .step-pane h4 { font-size: 1.65rem !important; }
        }

        /* Tablets landscape */
        @media (max-width: 991px) {
            .wizard-body { padding: 44px 40px !important; min-height: auto; }
            .wizard-footer { padding: 20px 40px !important; }
            .step-pane h3.step-heading, .step-pane h4 {
                font-size: 1.5rem !important;
                gap: 14px;
                padding-bottom: 18px !important;
                margin-bottom: 32px !important;
            }
            .step-pane h3.step-heading i, .step-pane h4 i {
                width: 46px; height: 46px;
                font-size: 1.35rem !important;
            }
            .form-control { height: 52px !important; font-size: 1rem !important; }
        }

        /* Tablets portrait */
        @media (max-width: 768px) {
            .wizard-body { padding: 36px 24px !important; }
            .wizard-footer { padding: 18px 24px !important; flex-wrap: wrap; gap: 10px; }
            .wizard-footer .btn-wizard { flex: 1; }
            .step-pane h3.step-heading, .step-pane h4 {
                font-size: 1.35rem !important;
                gap: 12px;
                padding-bottom: 16px !important;
                margin-bottom: 26px !important;
            }
            .step-pane h3.step-heading i, .step-pane h4 i {
                width: 42px; height: 42px;
                font-size: 1.15rem !important;
                border-radius: 10px;
            }
            .step-pane .row { row-gap: 1.25rem; }
            .form-label { font-size: 1.02rem !important; gap: 9px; margin-bottom: 10px !important; }
            .form-label i { font-size: 1.1rem !important; width: 22px; height: 22px; }
            .form-control { height: 50px !important; font-size: 0.98rem !important; padding: 0 16px !important; }
            .btn-wizard { padding: 13px 22px !important; font-size: 0.78rem !important; letter-spacing: 0.05em !important; }
            .wizard-main-card::before, .wizard-main-card::after { width: 18px; height: 18px; }

            /* Stack scheduling card on small screens */
            .scheduling-card .col-md-6.pe-md-4 { padding-right: 0 !important; border-right: none !important; border-bottom: 1px solid var(--rule); padding-bottom: 18px; margin-bottom: 18px; }
            .scheduling-card .col-md-6.ps-md-4 { padding-left: 0 !important; }
            [dir="rtl"] .scheduling-card .col-md-6.pe-md-4 { padding-left: 0 !important; }
        }

        /* Mobile phones */
        @media (max-width: 576px) {
            .wizard-body { padding: 28px 18px !important; }
            .wizard-footer { padding: 16px 18px !important; }
            .step-pane h3.step-heading, .step-pane h4 {
                font-size: 1.2rem !important;
                gap: 10px;
            }
            .step-pane h3.step-heading i, .step-pane h4 i {
                width: 38px; height: 38px;
                font-size: 1.05rem !important;
            }
            .form-label { font-size: 0.98rem !important; gap: 8px; }
            .form-label i { font-size: 1.05rem !important; width: 20px; height: 20px; }
            .form-control { height: 48px !important; font-size: 0.95rem !important; padding: 0 14px !important; }
            textarea.form-control { min-height: 88px !important; padding: 13px 14px !important; }
            .payment-methods-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .payment-method-card { padding: 20px 12px; }
            .payment-method-card i { font-size: 1.9rem; }
            .payment-method-card img.payment-logo { width: 46px; height: 46px; }
            .interaction-card { padding: 18px !important; }
            .file-upload-container { padding: 28px 18px !important; }

            /* Enrollment cards stack with more breathing room */
            .enrollment-type-wrapper { gap: 22px !important; }
            .enroll-type-card { padding: 28px 22px !important; }
            .enroll-type-card h4 { font-size: 1.25rem !important; }
            .enroll-type-card .type-icon { width: 56px !important; height: 56px !important; font-size: 1.5rem !important; }

            /* Program selection cards become single column */
            .program-selection-wrapper { margin-top: -50px !important; padding: 0 16px 30px !important; gap: 16px !important; }
            .program-card { height: 180px !important; }
            .program-name { font-size: 1.4rem !important; }
        }

        /* Very small phones */
        @media (max-width: 380px) {
            .wizard-body { padding: 22px 14px !important; }
            .step-pane h3.step-heading, .step-pane h4 { font-size: 1.1rem !important; }
            .level-radio-group { grid-template-columns: repeat(4, 1fr) !important; }
        }
    </style>

    <div class="registration-page-wrapper">
        <div class="entry-screen">
            <!-- Hero Section -->
            <div class="book-course-hero">
                <canvas id="hero-particles"></canvas>
                <div class="hero-content" data-aos="fade-up">
                    <h1>Registration</h1>
                    <p>Start your English language learning journey with us today. Choose your program to begin.</p>
                </div>
            </div>

            <div id="program-selection-container" style="display: none;">
                <div class="program-selection-wrapper">
                    <!-- Adult Program -->
                    <div class="program-option animate-slide-right" onclick="handleProgramSelection('adult')">
                        <div class="program-card">
                            <img src="{{ asset('assets/oxford/program2.jfif') }}" alt="Adult Program">
                            <div class="overlay-info">
                                <h3 class="program-name">English For Adults</h3>
                                <div class="qr-circle" onclick="showProgramQRCode('adult', event)"><i class="bi bi-qr-code-scan"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Kids Program -->
                    <div class="program-option animate-slide-left" onclick="handleProgramSelection('kids')">
                        <div class="program-card">
                            <img src="{{ asset('assets/oxford/img/banner/WhatsApp Image 2026-05-08 at 10.54.15 PM.jpeg') }}"
                                alt="Kids Program">
                            <div class="overlay-info">
                                <h3 class="program-name">English For Young Learners</h3>
                                <div class="qr-circle" onclick="showProgramQRCode('kids', event)"><i class="bi bi-qr-code-scan"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollment Type Choice (Course vs Test) -->
            <div id="enrollment-type-container" style="display: none;" class="mt-5">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="fw-bold text-primary">How would you like to start?</h2>
                    <p class="text-muted">Choose your preferred enrollment path</p>
                </div>
                <div class="enrollment-type-wrapper d-flex justify-content-center gap-4">
                    <div class="enroll-type-card" onclick="selectEnrollmentType('course', this)">
                        <div class="type-icon"><i class="bi bi-book"></i></div>
                        <h4>Direct Enrollment</h4>
                        <p>I know my level and want to join a program.</p>
                        <span class="type-badge">Recommended</span>
                    </div>
                    <div class="enroll-type-card" onclick="selectEnrollmentType('test', this)">
                        <div class="type-icon"><i class="bi bi-pencil-square"></i></div>
                        <h4>Placement Test</h4>
                        <p>I want a professional evaluation of my level.</p>
                        <span class="type-badge secondary">Expert Choice</span>
                    </div>
                    </div>
                </div>
            </div>

        <!-- Wizard Card (Hidden initially) -->
        <div class="wizard-main-card" id="wizard-card">
            <!-- Progress Tracker -->
            <div class="wizard-header-steps">
                <div class="step-track-line"><div class="step-track-fill" id="step-track-fill"></div></div>
                <div class="step-indicator active" data-step-nav="1" data-step-for="1">
                    <div class="step-number"><i class="bi bi-person-fill step-icon-inner"></i><span class="step-num-text">1</span></div>
                    <div class="step-label">Profile</div>
                </div>
                <div class="step-indicator" data-step-nav="2" data-step-for="2">
                    <div class="step-number"><i class="bi bi-journal-text step-icon-inner"></i><span class="step-num-text">2</span></div>
                    <div class="step-label">Details</div>
                </div>
                <div class="step-indicator" data-step-nav="3" data-step-for="3">
                    <div class="step-number"><i class="bi bi-mortarboard-fill step-icon-inner"></i><span class="step-num-text">3</span></div>
                    <div class="step-label">Placement</div>
                </div>
                <div class="step-indicator" data-step-nav="4" data-step-for="4">
                    <div class="step-number"><i class="bi bi-credit-card-2-back-fill step-icon-inner"></i><span class="step-num-text">4</span></div>
                    <div class="step-label">Payment</div>
                </div>
                <div class="step-indicator" data-step-nav="5" data-step-for="5">
                    <div class="step-number"><i class="bi bi-check2-circle step-icon-inner"></i><span class="step-num-text">5</span></div>
                    <div class="step-label">Finalize</div>
                </div>
            </div>

            <form id="registration-wizard-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="program_type" id="program_type_hidden">
                <input type="hidden" name="enrollment_type" id="enrollment_type_hidden">

                <div class="wizard-body">
                    <!-- Step 1: Basic Information -->
                    <div class="step-pane active" id="pane-1">
                        <h4 class="mb-4 step-heading">
                            <i class="bi bi-person-circle"></i>
                            Step 1: Personal Information & Health
                        </h4>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-person-badge"></i>Full Name (Arabic) (الاسم الرباعي بالعربية) *</label>
                                <input type="text" name="name" class="form-control" placeholder="الاسم الرباعي بالعربية"
                                    required pattern="[\u0600-\u06FF\s]+" title="Please enter name in Arabic only">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-alphabet-uppercase"></i>Full Name (English) (الاسم الرباعي بالإنجليزية) *</label>
                                <input type="text" name="name_en" class="form-control" placeholder="English Quad Name"
                                    required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-phone"></i>Phone Number (رقم الجوال) *</label>
                                <input type="text" name="mobile" class="form-control" placeholder="05x xxxx xxx"
                                    required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-envelope"></i>Email Address (البريد الإلكتروني) *</label>
                                <input type="email" name="email" class="form-control" placeholder="example@gmail.com"
                                    required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-calendar-event"></i>Date of Birth (تاريخ الميلاد) *</label>
                                <input type="date" name="dob" class="form-control" required onchange="checkAgeRequirement(this.value)" onblur="checkAgeRequirement(this.value)">
                            </div>
                            <div class="col-md-6 mb-4" id="gender-group">
                                <label class="form-label"><i class="bi bi-gender-ambiguous"></i>Gender (الجنس) *</label>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-4" id="underage-banner" style="display: none;">
                                <div class="alert border-0" style="background: var(--gold-soft); border-left: 4px solid var(--gold) !important; color: var(--ink); border-radius: var(--r-sm); padding: 16px 20px;">
                                    <div class="d-flex align-items-center" style="gap:8px;">
                                        <i class="bi bi-info-circle-fill" style="color: var(--ink); font-size: 1.3rem;"></i>
                                        <strong style="font-family: var(--font-display); font-style: italic; font-weight: 500; font-size: 1.05rem; margin: 0;">Guardian Information Required</strong>
                                    </div>
                                    <div style="margin-top:6px;">
                                        <span style="font-size: var(--t-sm); color: var(--ink-soft);">المتقدم تحت سن 16 — سيتم طلب بيانات ولي الأمر في الخطوة التالية لإتمام التسجيل.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-geo-alt"></i>Address (العنوان) *</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Full Address Details" required></textarea>
                            </div>
                            <div class="col-md-6 mb-4" id="major-in-step1" style="display: none;">
                                <label class="form-label"><i class="bi bi-mortarboard"></i>Major/Profession (التخصص/المهنة) *</label>
                                <input type="text" name="major" class="form-control" placeholder="e.g. Engineering, Student">
                            </div>

                            <!-- Moved Health Status Section here -->
                            <div class="col-md-6 mb-4">
                                <div class="interaction-card mb-4">
                                    <div class="header-flex-force pb-3 mb-3" style="border-bottom: 1px solid var(--rule);">
                                        <h5 class="mb-0"><i class="bi bi-heart-pulse me-2" style="color: var(--gold);"></i>Health Information (المعلومات الصحية)</h5>
                                    </div>
                                    <p class="text-muted mb-3 small">Does the applicant have any health problems? (هل يعاني المتقدم من مشاكل صحية؟)</p>
                                    <div class="d-flex gap-4 mb-3">
                                        <label class="custom-radio-pill">
                                            <input type="radio" name="health_status" value="no" checked onchange="toggleHealthNotes(false)">
                                            <span>No (لا يوجد)</span>
                                        </label>
                                        <label class="custom-radio-pill">
                                            <input type="radio" name="health_status" value="yes" onchange="toggleHealthNotes(true)">
                                            <span>Yes (نعم يوجد)</span>
                                        </label>
                                    </div>
                                    <div id="health-notes-wrapper" style="display: none;">
                                        <label class="form-label small text-muted fw-bold">Health condition details (يرجى وصف الحالة الصحية)</label>
                                        <textarea name="health_notes" id="health_notes" class="form-control health-textarea" rows="2" placeholder="Describe here..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Parent / Guardian Information (kids program OR underage applicant) -->
                    <div class="step-pane" id="pane-2">
                        <h4 class="mb-4 step-heading">
                            <i class="bi bi-people"></i>
                            Step 2: Parent / Guardian Information
                        </h4>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="form-label"><i class="bi bi-journal-text"></i>Parent Name (اسم ولي الأمر) *</label>
                                <input type="text" name="parent_name" class="form-control"
                                    placeholder="Parent Full Name">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-telephone-plus"></i>Parent Phone (رقم جوال ولي الأمر) *</label>
                                <input type="text" name="parent_phone" class="form-control"
                                    placeholder="05x xxxx xxx">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-people"></i>Relationship (صلة القرابة) *</label>
                                <select name="parent_relationship" id="parent_relationship_select" class="form-control" onchange="handleRelationshipChange()">
                                    <option value="">— اختر صلة القرابة —</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-4" id="parent_relationship_other_wrap" style="display:none;">
                                <label class="form-label"><i class="bi bi-pencil"></i>اكتب صلة القرابة (Custom Relationship) *</label>
                                <input type="text" name="parent_relationship_other" id="parent_relationship_other" class="form-control" placeholder="مثال: قريب من العائلة">
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="form-label"><i class="bi bi-envelope-at"></i>Parent Email (البريد الإلكتروني لولي الأمر)</label>
                                <input type="email" name="parent_email" class="form-control"
                                    placeholder="Parent Email">
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Placement & Program Selection -->
                    <div class="step-pane" id="pane-3">
                        <div class="text-center mb-5">
                            <h3 class="fw-bold step-heading justify-content-center">
                                <i class="bi bi-calendar-check"></i>
                                Step 3: Placement & Program
                            </h3>
                            <p class="text-muted">Select your target program and placement options.</p>
                        </div>

                        <input type="hidden" name="take_test" id="take_test_hidden">
                        <div id="test-options-container" style="display: none;"></div>

                        <!-- Target Program & Level Selection -->
                        <div class="interaction-card p-5 mb-5">
                            <div class="row align-items-center">
                                <div class="col-lg-6 border-end-lg pe-lg-5">
                                    <label class="form-label fw-bold fs-5 mb-3"><i class="bi bi-mortarboard-fill me-3 text-primary"></i>Target Program (البرنامج المستهدف) *</label>
                                    <select name="program_id" id="program_id_select" class="form-control form-control-lg select2 shadow-none border-2" onchange="handleProgramChange()">
                                        <option value="">Choose your program...</option>
                                        @foreach($programs as $p)
                                            @php
                                                $title = strtolower($p->title);
                                                $isKids = str_contains($title, 'kids') || str_contains($p->title, 'أطفال');
                                                $isAdult = str_contains($title, 'adult') || str_contains($p->title, 'كبار');
                                                // If it's a general program like "Levels", show it for both
                                                $isGeneral = str_contains($title, 'مستويات') || str_contains($title, 'level');
                                                
                                                $type = 'Both';
                                                if ($isKids && !$isAdult) $type = 'Kids';
                                                elseif ($isAdult && !$isKids) $type = 'Adults';
                                                elseif ($isGeneral) $type = 'Both';
                                            @endphp
                                            <option value="{{ $p->id }}" data-type="{{ $type }}">
                                                {{ $p->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="program-info-preview" class="mt-3 small text-muted p-2 bg-light rounded" style="display: none;">
                                        <i class="bi bi-info-circle me-1"></i>
                                        <span id="program-preview-text"></span>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6 ps-lg-5 mt-4 mt-lg-0">
                                    <div id="skip-test-level-selection" style="display: none;">
                                        <label class="form-label fw-bold fs-5 mb-3 text-info"><i class="bi bi-graph-up-arrow"></i>Current Level (المستوى الحالي) *</label>
                                        <div class="level-radio-group d-flex flex-wrap gap-2">
                                            @foreach(['A0', 'A1', 'A2', 'A2+', 'B1', 'B1+', 'B2', 'C1'] as $lvl)
                                            <div class="level-radio-item">
                                                <input type="radio" name="current_level" value="{{ $lvl }}" id="lvl-{{ $lvl }}" class="btn-check">
                                                <label class="btn btn-outline-primary" for="lvl-{{ $lvl }}">{{ $lvl }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div id="test-date-selection" style="display: none;">
                                        <label class="form-label fw-bold fs-5 mb-3"><i class="bi bi-calendar-event me-3 text-primary"></i>Preferred Date (التاريخ المفضل) *</label>
                                        <input type="date" name="test_date" class="form-control"
                                            min="{{ date('Y-m-d') }}">
                                        <small class="d-block mt-2 text-muted"><i class="bi bi-info-circle me-1"></i>اختر اليوم الذي يناسبك لإجراء امتحان تحديد المستوى.</small>
                                    </div>
                                    <div id="test-info-placeholder" class="text-center p-4 border rounded-4 border-dashed bg-light text-muted">
                                        <i class="bi bi-clipboard-data fs-2 mb-2 d-block"></i>
                                        Select a program to see level options or scheduling
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="test-scheduling-fields" class="mt-5"
                            style="display: none; animation: fadeIn 0.5s ease;">
                            <div class="alert mb-4 border-0"
                                style="background: var(--gold-soft); border-left: 4px solid var(--gold) !important; color: var(--ink); border-radius: var(--r-sm); padding: 16px 20px;">
                                <div class="d-flex align-items-center" style="gap:8px;">
                                    <i class="bi bi-info-circle-fill" style="color: var(--ink); font-size: 1.3rem;"></i>
                                    <strong style="font-family: var(--font-display); font-style: italic; font-weight: 500; font-size: 1.05rem; margin: 0;">Important</strong>
                                </div>
                                <div style="margin-top:6px;">
                                    <span style="font-size: var(--t-sm); color: var(--ink-soft);">Placement test fee is <strong style="color: var(--ink);">100 ILS</strong>. Please choose your preferred slot. (رسوم اختبار تحديد المستوى)</span>
                                    <div class="mt-2" style="font-size:0.95rem; color:var(--ink-soft);">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        <em>Note: Choosing a preferred slot does not guarantee your test time — the center will allocate slots to suit all students.</em>
                                        <br>
                                        <em>ملاحظة: اختيار موعد مفضل لا يضمن إجراء الاختبار في نفس الوقت، ستقوم المؤسسة بتحديد الموعد الأنسب لجميع الطلاب.</em>
                                    </div>
                                </div>
                            </div>
                            @php
                                $placementTimes = $placement_times ?? collect();
                                $uniqueDays = $placementTimes->pluck('days')->filter()->unique()->values();
                                $uniqueTimes = $placementTimes->pluck('times')->filter()->unique()->values();
                            @endphp
                            <div class="scheduling-card scheduling-split">
                                <div class="scheduling-split-col">
                                    <div class="scheduling-sub-header">
                                        <i class="bi bi-calendar3"></i>
                                        <span>الأيام <small>(DAYS)</small></span>
                                    </div>
                                    <div class="schedule-options-grid">
                                        @forelse($uniqueDays as $d)
                                            <label class="schedule-option">
                                                <input type="radio" name="preferred_days" value="{{ $d }}">
                                                <span class="option-text">{{ $d }}</span>
                                            </label>
                                        @empty
                                            <div class="text-muted small fst-italic w-100 text-center py-3">
                                                <i class="bi bi-info-circle me-1"></i>
                                                لا توجد مواعيد متاحة حالياً.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="scheduling-split-divider" aria-hidden="true"></div>
                                <div class="scheduling-split-col">
                                    <div class="scheduling-sub-header">
                                        <i class="bi bi-clock"></i>
                                        <span>الوقت <small>(TIME)</small></span>
                                    </div>
                                    <div class="schedule-options-grid">
                                        @forelse($uniqueTimes as $t)
                                            <label class="schedule-option">
                                                <input type="radio" name="preferred_time" value="{{ $t }}">
                                                <span class="option-text">{{ $t }}</span>
                                            </label>
                                        @empty
                                            <div class="text-muted small fst-italic w-100 text-center py-3">
                                                <i class="bi bi-info-circle me-1"></i>
                                                لا توجد أوقات متاحة حالياً.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Secure Payment -->
                    <div class="step-pane" id="pane-4">
                        <div class="text-center mb-5">
                            <h3 class="fw-bold step-heading justify-content-center">
                                <i class="bi bi-credit-card-2-back"></i>
                                Step 4: Payment Summary
                            </h3>
                            <p class="text-muted">Review fees and finalize your payment.</p>
                        </div>

                        <!-- Fees Breakdown -->
                        <div class="mb-5">
                            <label class="form-label fw-bold mb-4 d-block">
                                <i class="bi bi-list-check me-3 text-primary"></i>
                                Fees Breakdown (تفاصيل الرسوم)
                            </label>
                            <div class="bg-white rounded-4 shadow-sm overflow-hidden border">
                                <table class="table table-hover mb-0">
                                    <tbody id="fees-breakdown-body">
                                        <!-- Dynamic rows here -->
                                    </tbody>
                                    <tfoot class="bg-light border-top">
                                        <tr>
                                            <td class="ps-4 py-3 fw-bold">Total Amount Due (إجمالي المستحق)</td>
                                            <td class="pe-4 py-3 text-end fw-bold fs-5 text-primary">
                                                <span id="display-total-due">0.00</span> ILS
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-12 mb-2">
                                <label class="form-label fw-bold mb-3 d-block"><i class="bi bi-wallet2 me-2 text-primary"></i>Payment Method (طريقة الدفع) *</label>
                                <div class="payment-methods-grid">
                                    @foreach ($payment_methods as $method)
                                        <div class="payment-method-card" 
                                             onclick="selectPaymentMethod('{{ $method->id }}', this)" 
                                             data-creds="{{ json_encode($method->credentials) }}">
                                            @if($method->image)
                                                <img src="{{ asset('uploads/' . $method->image) }}" class="payment-logo" alt="{{ $method->name }}">
                                            @else
                                                <i class="{{ $method->icon ?: 'bi bi-cash-coin' }}"></i>
                                            @endif
                                            <span>{{ $method->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="payment_method_id" id="payment_method_id_hidden">
                                
                                <!-- Payment Credentials Display (Simplified Style) -->
                                <div id="method-details-area" class="mt-5 mb-5" style="display: none;">
                                    <div class="credentials-container px-2">
                                        <label class="form-label fw-bold mb-4 d-block">
                                            <i class="bi bi-shield-lock-fill me-3 text-primary"></i>
                                            Payment Credentials (بيانات الدفع)
                                        </label>
                                        <div id="credentials-list" class="d-flex flex-column gap-3"></div>
                                        <div class="mt-4 text-center p-3 bg-light rounded-4">
                                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Please transfer the exact amount and upload the screenshot below.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="bi bi-cash-stack"></i>Paid Amount (المبلغ المدفوع) *</label>
                                <input type="number" name="student_fee_paid" class="form-control form-control-lg border-2"
                                    placeholder="0.00" required min="0" step="0.01"
                                    oninput="updateRemainingDue()" onblur="enforcePaidMinimum()">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-calculator"></i>Remaining Balance (المتبقي)</label>
                                <div class="remaining-balance-display">
                                    <span class="rb-amount" id="display-amount-due">0.00</span>
                                    <span class="rb-currency">ILS</span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div id="min-payment-hint" class="payment-note" style="display:none;">
                                    <div class="payment-note-icon"><i class="bi bi-info-circle-fill"></i></div>
                                    <div class="payment-note-body">
                                        <div class="payment-note-title">الحد الأدنى المطلوب للدفع</div>
                                        <div class="payment-note-amount"><span id="min-payment-value">0.00</span> <small>ILS</small></div>
                                        <div class="payment-note-desc">هذه القيمة مُحدَّدة من إدارة الأكاديمية على مستوى البرنامج، ولا يمكن إتمام التسجيل بمبلغ أقل منها.</div>
                                    </div>
                                </div>
                                <div id="min-payment-error" class="payment-note payment-note-danger mt-3" style="display:none;">
                                    <div class="payment-note-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                                    <div class="payment-note-body">
                                        <div class="payment-note-title">المبلغ المُدخَل غير كافٍ</div>
                                        <div class="payment-note-desc" id="min-payment-error-text"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Upload Payment Receipt (رفع إيصال الدفع) *</label>
                                <div class="file-upload-container p-4 border-2 border-dashed rounded-4 text-center cursor-pointer" id="receipt-dropzone" onclick="document.getElementById('receipt_input').click()">
                                    <input type="file" name="payment_receipt" id="receipt_input" accept="image/*,.pdf" style="display: none;" onchange="updateFileName(this)" required>
                                    <i class="bi bi-cloud-arrow-up fs-1 text-primary mb-2"></i>
                                    <p class="mb-1" style="font-size: 1.1rem; font-weight: 700; color: var(--ink); font-family: var(--font-display); font-style: italic;">اضغط لرفع إيصال الدفع</p>
                                    <p class="mb-0 text-muted" style="font-size: 0.95rem;">Click to upload — PNG, JPG, or PDF (الحد الأقصى 2MB)</p>
                                    <div id="file-name-preview" class="mt-3 fw-bold text-success" style="font-size: 1rem;"></div>
                                </div>
                            </div>
                        </div>


                        <input type="hidden" name="total_due_amount" id="total_due_hidden" value="0">
                    </div>

                    <!-- Step 5: Final Step -->
                    <div class="step-pane" id="pane-5">
                        <div class="text-center mb-5">
                            <div class="finalize-icon-circle" style="display: inline-flex; align-items: center; justify-content: center; width: 88px; height: 88px; min-width: 88px; min-height: 88px; aspect-ratio: 1 / 1; border-radius: 50%; background: var(--gold-soft); border: 2px solid var(--gold); position: relative; box-shadow: 0 8px 20px rgba(255,204,0,0.18); margin: 0 auto;">
                                <i class="bi bi-check2-circle" style="font-size: 2.4rem; color: var(--ink); line-height: 1;"></i>
                                <span style="position: absolute; top: -2px; right: -2px; width: 26px; height: 26px; aspect-ratio: 1 / 1; background: var(--ink); color: var(--gold); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.78rem; border: 2px solid var(--paper);"><i class="bi bi-check-lg"></i></span>
                            </div>
                            <h3 class="mt-4" style="font-family: var(--font-display); font-weight: 500; font-style: italic; font-size: var(--t-2xl); color: var(--ink); letter-spacing: -0.02em;">Finalize Registration</h3>
                            <p style="color: var(--ink-soft); font-family: var(--font-body); font-size: var(--t-md);">Review notes and confirm your agreement.</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3 d-block"><i class="bi bi-journal-text me-3 text-primary"></i>General Notes (ملاحظات عامة)</label>
                            <textarea name="general_notes" class="form-control shadow-sm border-2" rows="3" placeholder="Add any extra notes here..."></textarea>
                        </div>

                        <div class="mb-4 p-4" style="background: var(--surface); border: 1px solid var(--rule); border-left: 4px solid var(--gold); border-radius: var(--r-md);">
                            <label class="form-label mb-3 d-block" style="color: var(--ink);"><i class="bi bi-shield-lock-fill"></i>Terms & Privacy</label>
                            <div class="agreement-box d-flex align-items-center gap-3 p-3 bg-white rounded-3 border">
                                <input type="checkbox" id="agree-terms" required class="form-check-input m-0" style="width: 28px; height: 28px; cursor: pointer;">
                                <label for="agree-terms" class="agreement-text m-0 cursor-pointer">
                                    <span class="fw-bold d-block fs-6">I agree to the Terms & Privacy Policy</span>
                                    <p class="small text-muted mb-0">أوافق على جميع الشروط والأحكام الخاصة بالأكاديمية وسياسة الخصوصية.</p>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation -->
                <div class="wizard-footer">
                    <button type="button" class="btn-wizard btn-prev" id="btn-prev" style="display: none;"
                        onclick="changeStep(-1)">Back</button>
                    <button type="button" class="btn-wizard btn-next" id="btn-next" onclick="changeStep(1)">Next Step</button>
                </div>
            </form>
        </div>
    </div>
    </div>


@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/particles-hero.js') }}"></script>
    <script src="{{ asset('assets/oxford/js/wizard.js') }}"></script>
    <script>
        window.registrationRoute = '{{ route('contact.book.post') }}';
        window.apiCheckEmail = '{{ url("api/check-email") }}';
        window.apiCheckMobile = '{{ url("api/check-mobile") }}';


        document.addEventListener('DOMContentLoaded', function() {
            // Delay Program Selection Reveal
            setTimeout(() => {
                const container = document.getElementById('program-selection-container');
                if (container) {
                    container.style.display = 'block';
                    if (typeof AOS !== 'undefined') AOS.refresh();
                }
            }, 1500);

            // Initialize Hero Particles
            if (typeof initParticles === 'function') {
                initParticles('hero-particles', {
                    color1: 'rgba(245, 197, 24, 0.7)',
                    color2: 'rgba(255, 255, 255, 0.25)',
                    count: 100, // Increased count
                    speed: 0.4,
                    connectLines: false,
                    lineColor: 'rgba(245, 197, 24, 0.15)',
                    connectDistance: 150
                });
            }

            if (typeof AOS !== 'undefined') {
                AOS.init({ duration: 1000, once: true, offset: 100 });
            }

            @if (session('success'))
                const lang = document.documentElement.lang || 'en';
                const msgs = {
                    en: 'Your booking request has been submitted successfully.',
                    ar: 'تم إرسال طلب التسجيل بنجاح.'
                };
                
                Swal.fire({
                    icon: 'success',
                    title: lang === 'ar' ? 'تم التسجيل!' : 'Submitted!',
                    text: msgs[lang],
                    confirmButtonText: lang === 'ar' ? 'موافق' : 'Great!',
                    customClass: {
                        popup: 'swal-oxford-popup',
                        confirmButton: 'swal-oxford-confirm'
                    },
                    buttonsStyling: false,
                    timer: 5000
                }).then(() => window.location.href = '{{ url('/') }}');
            @endif

            // Handle URL parameter for program auto-selection
            const urlParams = new URLSearchParams(window.location.search);
            const programParam = urlParams.get('program');
            if (programParam === 'adult' || programParam === 'kids') {
                setTimeout(() => {
                    if (typeof handleProgramSelection === 'function') {
                        handleProgramSelection(programParam);
                    }
                }, 2000);
            }
        });

        // ----------- Client-side uniqueness checks (debounced) -----------
        (function(){
            const emailInput = document.querySelector('input[name="email"]');
            const mobileInput = document.querySelector('input[name="mobile"]');
            const nextBtn = document.getElementById('btn-next');
            let emailTimer = null, mobileTimer = null;
            window._emailExists = false; window._mobileExists = false;
            const pageLang = document.documentElement.lang === 'ar' ? 'ar' : 'en';
            const messages = {
                emailUsed: { ar: 'هذا البريد مستخدم من قبل.', en: 'This email is already used.' },
                mobileUsed: { ar: 'رقم الجوال مستخدم من قبل.', en: 'This mobile number is already used.' },
                cannotContinue: { ar: 'لا يمكن المتابعة: البريد أو رقم الجوال مستخدم من قبل.', en: 'Cannot continue: email or mobile is already used.' }
            };

            function setInvalid(el, msg) {
                // remove any valid state
                clearValid(el);
                el.classList.add('is-invalid');
                // create or reuse invalid-feedback and position it to start of input
                let f = el.parentNode.querySelector('.invalid-feedback');
                if (!f) {
                    f = document.createElement('div');
                    f.className = 'invalid-feedback';
                    el.parentNode.appendChild(f);
                }
                f.innerText = msg;
                // position under input start
                const col = el.closest('[class*="col-"]');
                if (col) {
                    const inputRect = el.getBoundingClientRect();
                    const colRect = col.getBoundingClientRect();
                    f.style.left = (el.offsetLeft) + 'px';
                    f.style.width = el.offsetWidth + 'px';
                }
                // show error icon inside input
                addStatusIcon(el, 'error');
            }

            function clearInvalid(el) {
                el.classList.remove('is-invalid');
                const f = el.parentNode.querySelector('.invalid-feedback');
                if (f) f.remove();
                removeStatusIcon(el);
            }

            function setValid(el, msg) {
                // remove invalid state
                clearInvalid(el);
                el.classList.add('is-valid');
                let f = el.parentNode.querySelector('.valid-feedback');
                if (!f) {
                    f = document.createElement('div');
                    f.className = 'valid-feedback';
                    el.parentNode.appendChild(f);
                }
                f.innerText = msg;
                // position under input start
                f.style.left = (el.offsetLeft) + 'px';
                f.style.width = el.offsetWidth + 'px';
                addStatusIcon(el, 'success');
            }

            function clearValid(el) {
                el.classList.remove('is-valid');
                const f = el.parentNode.querySelector('.valid-feedback');
                if (f) f.remove();
                removeStatusIcon(el);
            }

            function addStatusIcon(el, type) {
                removeStatusIcon(el);
                const wrap = el.parentNode;
                const icon = document.createElement('span');
                icon.className = 'input-status-icon ' + (type === 'success' ? 'success' : 'error');
                icon.setAttribute('aria-hidden','true');
                icon.innerHTML = type === 'success' ? '&#10003;' : '&#10007;';
                wrap.style.position = wrap.style.position || 'relative';
                wrap.appendChild(icon);
            }

            function removeStatusIcon(el) {
                const wrap = el.parentNode;
                const existing = wrap.querySelector('.input-status-icon');
                if (existing) existing.remove();
            }

            function updateNextState() {
                if (window._emailExists || window._mobileExists) {
                    nextBtn.setAttribute('disabled','disabled');
                } else {
                    nextBtn.removeAttribute('disabled');
                }
            }

            function checkEmailNow(value) {
                if (!value) { window._emailExists = false; clearInvalid(emailInput); clearValid(emailInput); updateNextState(); return; }
                fetch(window.apiCheckEmail + '?email=' + encodeURIComponent(value), { cache: 'no-store' })
                    .then(r => r.json()).then(d => {
                        window._emailExists = !!d.exists;
                        if (d.exists) {
                            setInvalid(emailInput, messages.emailUsed[pageLang]);
                        } else {
                            setValid(emailInput, pageLang === 'ar' ? 'البريد متاح' : 'Email available');
                        }
                        updateNextState();
                    }).catch(()=>{});
            }

            function checkMobileNow(value) {
                if (!value) { window._mobileExists = false; clearInvalid(mobileInput); clearValid(mobileInput); updateNextState(); return; }
                fetch(window.apiCheckMobile + '?mobile=' + encodeURIComponent(value), { cache: 'no-store' })
                    .then(r => r.json()).then(d => {
                        window._mobileExists = !!d.exists;
                        if (d.exists) {
                            setInvalid(mobileInput, messages.mobileUsed[pageLang]);
                        } else {
                            setValid(mobileInput, pageLang === 'ar' ? 'رقم الجوال متاح' : 'Mobile available');
                        }
                        updateNextState();
                    }).catch(()=>{});
            }

            if (emailInput) {
                emailInput.addEventListener('input', function(e){
                    clearTimeout(emailTimer);
                    const val = e.target.value.trim();
                    // simple client-side email pattern check to avoid unnecessary requests
                    const re = /^\S+@\S+\.\S+$/;
                    if (!re.test(val)) { window._emailExists = false; clearInvalid(emailInput); updateNextState(); return; }
                    emailTimer = setTimeout(()=> checkEmailNow(val), 450);
                });
                emailInput.addEventListener('blur', function(e){ checkEmailNow(e.target.value.trim()); });
            }

            if (mobileInput) {
                mobileInput.addEventListener('input', function(e){
                    clearTimeout(mobileTimer);
                    const val = e.target.value.replace(/[^0-9+]/g,'').trim();
                    if (!val) { window._mobileExists = false; clearInvalid(mobileInput); updateNextState(); return; }
                    mobileTimer = setTimeout(()=> checkMobileNow(val), 450);
                });
                mobileInput.addEventListener('blur', function(e){ checkMobileNow(e.target.value.replace(/[^0-9+]/g,'')); });
            }

            // Prevent final form submission if duplicate found
            const form = document.getElementById('registration-wizard-form');
            if (form) {
                form.addEventListener('submit', function(e){
                    if (window._emailExists || window._mobileExists) {
                        e.preventDefault();
                        e.stopPropagation();
                        const msg = messages.cannotContinue[pageLang];
                        Swal.fire({ icon: 'error', title: msg, timer: 3500, showConfirmButton: false });
                    }
                });
            }
        })();

        window.showProgramQRCode = function(type, event) {
            if (event) event.stopPropagation();
            
            const baseUrl = window.location.origin + window.location.pathname;
            const qrUrl = `${baseUrl}?program=${type}`;
            const qrImageSource = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrUrl)}`;
            
            const title = type === 'adult' ? 'English For Adults' : 'English For Young Learners';
            const titleAr = type === 'adult' ? 'برنامج اللغة الإنجليزية للكبار' : 'برنامج اللغة الإنجليزية للأطفال';
            const lang = document.documentElement.lang || 'en';

            Swal.fire({
                title: lang === 'ar' ? titleAr : title,
                html: `
                    <div class="qr-modal-content text-center py-4">
                        <img src="${qrImageSource}" alt="QR Code" class="img-fluid mb-4 shadow-sm rounded" style="width: 250px; border: 10px solid white;">
                        <div class="d-flex justify-content-center gap-3">
                            <button onclick="downloadQRCode('${qrImageSource}', '${type}')" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-download"></i> ${lang === 'ar' ? 'تحميل' : 'Download'}
                            </button>
                            <button onclick="shareQRCode('${qrUrl}')" class="btn btn-warning rounded-pill px-4 shadow-sm" style="background-color: var(--secondary-color); border: none; color: var(--primary-color); font-weight: bold;">
                                <i class="bi bi-share"></i> ${lang === 'ar' ? 'مشاركة' : 'Share'}
                            </button>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    popup: 'swal-oxford-popup qr-modal-popup'
                }
            });
        };

        window.downloadQRCode = function(url, type) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.responseType = "blob";
            xhr.onload = function() {
                const urlCreator = window.URL || window.webkitURL;
                const imageUrl = urlCreator.createObjectURL(this.response);
                const tag = document.createElement('a');
                tag.href = imageUrl;
                tag.download = `Oxford_${type}_Program_QR.png`;
                document.body.appendChild(tag);
                tag.click();
                document.body.removeChild(tag);
            };
            xhr.send();
        };

        window.shareQRCode = function(url) {
            if (navigator.share) {
                navigator.share({
                    title: 'Oxford English Center Registration',
                    url: url
                }).catch(err => console.log('Error sharing:', err));
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    const lang = document.documentElement.lang || 'en';
                    Swal.fire({
                        icon: 'success',
                        title: lang === 'ar' ? 'تم النسخ!' : 'Copied!',
                        text: lang === 'ar' ? 'تم نسخ الرابط إلى الحافظة.' : 'URL copied to clipboard.',
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: { popup: 'swal-oxford-popup' }
                    });
                });
            }
        };

    </script>
@endsection
