@php
    $stAge = null;
    if (!empty($student->dob)) {
        try { $stAge = \Carbon\Carbon::parse($student->dob)->age; } catch (\Exception $e) {}
    }
    $isChild = ($student->program_type === 'kids') || (!is_null($stAge) && $stAge <= 15);

    $avatar = ($student->image && file_exists(public_path($student->image)))
              ? asset($student->image)
              : (($student->img && file_exists(public_path($student->img)))
                 ? asset($student->img)
                 : asset('assets/media/avatars/blank.png'));

    $genderLabel = ($student->gender == 'male' || $student->gender == '1') ? 'ذكر'
                 : (($student->gender == 'female' || $student->gender == '2' || $student->gender == '0') ? 'أنثى' : '—');
    $statusLabel = $student->status == 1 ? 'نشط' : 'غير نشط';
    $statusClass = $student->status == 1 ? 'success' : 'danger';
    $programLabel = $student->program_type === 'kids' ? 'الأطفال (Kids)' : ($student->program_type === 'adult' ? 'الكبار (Adult)' : '—');

    $joinDate = $student->join_date ?: ($student->created_at ? $student->created_at->format('Y-m-d') : '—');
@endphp

<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* ===== Editorial theme tokens ===== */
    .sd-wrap {
        --sd-ink:        #003366;
        --sd-ink-2:      #002a55;
        --sd-gold:       #ffcc00;
        --sd-gold-soft:  #fff9e0;
        --sd-cream:      #faf6ec;
        --sd-paper:      #ffffff;
        --sd-rule:       #e5e9f0;
        --sd-rule-2:     #eef0f5;
        --sd-muted:      #6c7689;
        --sd-faint:      #b8c0cf;
        --sd-text:       #1f2937;
        --sd-radius:     14px;
        --sd-radius-lg:  18px;

        --sd-display: 'Fraunces', 'Playfair Display', Georgia, serif;
        --sd-body:    'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;

        font-family: var(--sd-body);
        color: var(--sd-text);
        padding: 0 4px;
    }

    /* Dark-mode token overrides */
    [data-bs-theme="dark"] .sd-wrap,
    [data-theme-mode="dark"] .sd-wrap,
    .dark-mode .sd-wrap,
    html.dark .sd-wrap {
        --sd-paper:   #1c1d2b;
        --sd-cream:   #1f2030;
        --sd-rule:    #2b2d3c;
        --sd-rule-2:  #262737;
        --sd-text:    #e7e9ef;
        --sd-muted:   #9aa0b4;
        --sd-faint:   #5a5f76;
    }

    .sd-hero {
        background: linear-gradient(135deg, var(--sd-ink) 0%, var(--sd-ink-2) 100%);
        border-radius: var(--sd-radius-lg);
        padding: 26px 28px;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .sd-hero::after {
        content: '';
        position: absolute;
        right: -50px; top: -50px;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(255,204,0,0.18) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .sd-hero::before {
        content: '';
        position: absolute;
        top: 12px; right: 12px;
        width: 22px; height: 22px;
        border: 1.5px solid var(--sd-gold);
        border-left: none; border-bottom: none;
        opacity: 0.6;
    }
    .sd-hero-inner { display: flex; align-items: center; gap: 20px; position: relative; z-index: 1; flex-wrap: wrap; }
    .sd-avatar {
        width: 88px; height: 88px; border-radius: 50%;
        object-fit: cover; border: 3px solid rgba(255,255,255,0.95);
        box-shadow: 0 10px 26px rgba(0,0,0,0.28);
        flex-shrink: 0;
    }
    .sd-hero h3 {
        font-family: var(--sd-display);
        font-style: italic;
        font-weight: 600;
        font-size: 1.65rem;
        letter-spacing: -0.02em;
        color: #fff;
        margin: 0 0 4px;
        line-height: 1.15;
    }
    .sd-hero .sd-name-en {
        font-family: var(--sd-display);
        font-size: 0.92rem;
        opacity: 0.7;
        font-style: italic;
        font-weight: 500;
        letter-spacing: -0.005em;
    }
    .sd-hero .sd-email {
        color: rgba(255,255,255,0.85);
        font-size: 0.9rem;
        word-break: break-all;
        margin-top: 6px;
        font-family: var(--sd-body);
    }
    .sd-chips { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 12px; }
    .sd-chip {
        background: rgba(255,255,255,0.16);
        border: 1px solid rgba(255,255,255,0.28);
        color: #fff;
        font-size: 0.74rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 999px;
        backdrop-filter: blur(4px);
        display: inline-flex; align-items: center; gap: 5px;
        letter-spacing: 0.02em;
    }
    .sd-chip i { font-size: 0.78rem; }
    .sd-chip.gold { background: var(--sd-gold); color: var(--sd-ink); border-color: transparent; font-weight: 800; }

    .sd-section {
        background: var(--sd-paper);
        border: 1px solid var(--sd-rule);
        border-radius: var(--sd-radius);
        padding: 20px 22px;
        margin-bottom: 16px;
    }
    .sd-section-title {
        font-family: var(--sd-display);
        font-style: italic;
        font-weight: 600;
        font-size: 1.1rem;
        color: var(--sd-ink);
        margin: 0 0 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--sd-rule);
        display: flex; align-items: center; gap: 12px;
        letter-spacing: -0.015em;
        position: relative;
    }
    .sd-section-title::after {
        content: '';
        position: absolute;
        bottom: -1px;
        width: 38px;
        height: 2px;
        background: var(--sd-gold);
        right: 0;       /* RTL natural */
    }
    .sd-section-title i {
        color: var(--sd-ink);
        font-size: 1rem;
        background: var(--sd-gold-soft);
        border: 1px solid var(--sd-gold);
        border-radius: 8px;
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        font-style: normal;
    }

    .sd-field { margin-bottom: 14px; }
    .sd-field-label {
        font-family: var(--sd-body);
        font-size: 0.68rem; font-weight: 700; color: var(--sd-muted);
        text-transform: uppercase; letter-spacing: 0.09em;
        margin-bottom: 5px;
        display: flex; align-items: center; gap: 7px;
    }
    .sd-field-label i { color: var(--sd-gold); font-size: 0.82rem; }
    .sd-field-value {
        font-family: var(--sd-body);
        font-size: 0.95rem; font-weight: 600; color: var(--sd-text);
        word-break: break-word; line-height: 1.5;
    }
    .sd-field-value.empty { color: var(--sd-faint); font-weight: 500; font-style: italic; }

    .sd-guardian-card {
        background: linear-gradient(135deg, var(--sd-gold-soft) 0%, var(--sd-paper) 100%);
        border: 1px solid var(--sd-gold);
        border-left-width: 4px;
        border-radius: var(--sd-radius);
        padding: 20px 22px;
        margin-bottom: 16px;
    }
    .sd-guardian-card .sd-section-title { color: var(--sd-ink); border-bottom-color: rgba(255,204,0,0.4); }

    .sd-groups-table { width: 100%; border-collapse: separate; border-spacing: 0 6px; font-family: var(--sd-body); }
    .sd-groups-table th {
        font-size: 0.68rem; font-weight: 700; color: var(--sd-muted);
        text-transform: uppercase; letter-spacing: 0.08em;
        padding: 8px 12px; text-align: start;
    }
    .sd-groups-table td {
        padding: 12px 14px;
        background: var(--sd-cream);
        border-top: 1px solid var(--sd-rule-2);
        border-bottom: 1px solid var(--sd-rule-2);
        font-size: 0.9rem;
    }
    .sd-groups-table td:first-child { border-right: 1px solid var(--sd-rule-2); border-radius: 0 8px 8px 0; }
    .sd-groups-table td:last-child  { border-left:  1px solid var(--sd-rule-2); border-radius: 8px 0 0 8px; }
    .sd-groups-table .group-name {
        font-family: var(--sd-display); font-style: italic; font-weight: 600;
        font-size: 1.02rem; color: var(--sd-ink); letter-spacing: -0.01em;
    }

    .sd-empty-row {
        text-align: center; color: var(--sd-faint); font-style: italic;
        padding: 24px; background: var(--sd-cream); border-radius: 10px;
        font-family: var(--sd-body); font-size: 0.9rem;
    }

    .sd-note {
        background: var(--sd-gold-soft);
        border-right: 4px solid var(--sd-gold);
        border-radius: 10px;
        padding: 16px 18px;
        font-size: 0.9rem;
        color: var(--sd-text);
        line-height: 1.6;
        font-family: var(--sd-body);
    }
    .sd-note .sd-note-title {
        font-family: var(--sd-display);
        font-style: italic;
        font-weight: 600;
        font-size: 1rem;
        color: var(--sd-ink);
        margin-bottom: 4px;
    }

    @media (max-width: 576px) {
        .sd-hero { padding: 20px; }
        .sd-avatar { width: 70px; height: 70px; }
        .sd-hero h3 { font-size: 1.3rem; }
        .sd-section { padding: 16px 18px; }
        .sd-section-title { font-size: 1rem; }
    }
</style>

<div class="sd-wrap">
    {{-- HERO HEADER --}}
    <div class="sd-hero">
        <div class="sd-hero-inner">
            <img src="{{ $avatar }}" alt="{{ $student->name }}" class="sd-avatar">
            <div class="flex-grow-1" style="min-width:200px;">
                <h3>{{ $student->name }}</h3>
                @if($student->name_en)
                    <div class="sd-name-en">{{ $student->name_en }}</div>
                @endif
                <div class="sd-email"><i class="bi bi-envelope-fill me-1"></i> {{ $student->email ?: 'بدون بريد إلكتروني' }}</div>
                <div class="sd-chips">
                    <span class="sd-chip {{ $student->status == 1 ? 'gold' : '' }}">
                        <i class="bi bi-{{ $student->status == 1 ? 'check-circle-fill' : 'x-circle-fill' }}"></i> {{ $statusLabel }}
                    </span>
                    <span class="sd-chip">{{ $genderLabel }}</span>
                    @if(!is_null($stAge))
                        <span class="sd-chip"><i class="bi bi-cake2"></i> {{ $stAge }} سنة</span>
                    @endif
                    @if($student->current_level)
                        <span class="sd-chip"><i class="bi bi-bar-chart-fill"></i> {{ $student->current_level }}</span>
                    @endif
                    <span class="sd-chip">{{ $programLabel }}</span>
                    @if($student->enrollment_type === 'test')
                        <span class="sd-chip" style="background: rgba(255,255,255,0.95); color: #92400e;">
                            <i class="bi bi-clipboard-check"></i> Placement Test
                        </span>
                    @elseif($student->enrollment_type === 'course')
                        <span class="sd-chip" style="background: rgba(255,255,255,0.95); color: #1e3a8a;">
                            <i class="bi bi-mortarboard-fill"></i> Direct
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- LEFT COLUMN — Personal info --}}
        <div class="col-lg-7">
            <div class="sd-section">
                <div class="sd-section-title">
                    <i class="bi bi-person-vcard-fill"></i> البيانات الشخصية
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="sd-field">
                            <div class="sd-field-label"><i class="bi bi-phone"></i> رقم الجوال</div>
                            <div class="sd-field-value {{ !$student->mobile ? 'empty' : '' }}">{{ $student->mobile ?: 'غير محدد' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="sd-field">
                            <div class="sd-field-label"><i class="bi bi-calendar-event"></i> تاريخ الميلاد</div>
                            <div class="sd-field-value {{ !$student->dob ? 'empty' : '' }}">{{ $student->dob ?: 'غير محدد' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="sd-field">
                            <div class="sd-field-label"><i class="bi bi-briefcase"></i> الوظيفة / التخصص</div>
                            <div class="sd-field-value {{ !($student->job || $student->major) ? 'empty' : '' }}">{{ $student->major ?: ($student->job ?: 'غير محدد') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="sd-field">
                            <div class="sd-field-label"><i class="bi bi-calendar-check"></i> تاريخ الانضمام</div>
                            <div class="sd-field-value">{{ $joinDate }}</div>
                        </div>
                    </div>
                    @if($student->address)
                    <div class="col-md-12">
                        <div class="sd-field">
                            <div class="sd-field-label"><i class="bi bi-geo-alt-fill"></i> العنوان</div>
                            <div class="sd-field-value">{{ $student->address }}</div>
                        </div>
                    </div>
                    @endif
                    @if($student->health_conditions)
                    <div class="col-md-12">
                        <div class="sd-field">
                            <div class="sd-field-label"><i class="bi bi-heart-pulse-fill" style="color:#dc3545;"></i> الحالة الصحية</div>
                            <div class="sd-field-value">{{ $student->health_conditions }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Groups table --}}
            <div class="sd-section">
                <div class="sd-section-title">
                    <i class="bi bi-collection-fill"></i> المجموعات المسجل بها
                    <span class="badge badge-light-primary fw-bold ms-auto">{{ $student->gropes ? $student->gropes->count() : 0 }}</span>
                </div>
                @if($student->gropes && $student->gropes->count())
                    <table class="sd-groups-table">
                        <thead>
                            <tr>
                                <th>المجموعة</th>
                                <th>البرنامج</th>
                                <th style="text-align:center;">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->gropes as $sg)
                                <tr>
                                    <td><span class="group-name">{{ $sg->group->name ?? '—' }}</span></td>
                                    <td>{{ $sg->group->program->title ?? '—' }}</td>
                                    <td style="text-align:center;">
                                        @if($sg->group && $sg->group->status == 1)
                                            <span class="badge badge-light-success fw-bold">نشطة</span>
                                        @else
                                            <span class="badge badge-light-secondary fw-bold">منتهية</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="sd-empty-row">
                        <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                        لا يوجد مجموعات مسجل بها بعد
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN — Guardian + Notes --}}
        <div class="col-lg-5">
            @if($isChild && $student->parent)
                <div class="sd-guardian-card">
                    <div class="sd-section-title" style="border-bottom-color: rgba(255,204,0,0.4);">
                        <i class="bi bi-people-fill"></i> ولي الأمر
                    </div>
                    <div class="sd-field">
                        <div class="sd-field-label"><i class="bi bi-person"></i> الاسم</div>
                        <div class="sd-field-value {{ !$student->parent->name ? 'empty' : '' }}">{{ $student->parent->name ?: '—' }}</div>
                    </div>
                    <div class="sd-field">
                        <div class="sd-field-label"><i class="bi bi-phone"></i> الجوال</div>
                        <div class="sd-field-value {{ !$student->parent->phone ? 'empty' : '' }}">{{ $student->parent->phone ?: '—' }}</div>
                    </div>
                    <div class="sd-field">
                        <div class="sd-field-label"><i class="bi bi-envelope"></i> البريد</div>
                        <div class="sd-field-value {{ !$student->parent->email ? 'empty' : '' }}" style="word-break:break-all;">{{ $student->parent->email ?: '—' }}</div>
                    </div>
                    <div class="sd-field" style="margin-bottom:0;">
                        <div class="sd-field-label"><i class="bi bi-link-45deg"></i> صلة القرابة</div>
                        <div>
                            <span class="badge badge-warning fw-bold" style="background:#003366; color:#ffcc00;">{{ $student->parent->relationship ?: '—' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if($student->note)
                <div class="sd-note">
                    <div class="sd-note-title"><i class="bi bi-pin-angle-fill me-1"></i> ملاحظات</div>
                    {{ $student->note }}
                </div>
            @endif

            @if(!$isChild && !$student->note)
                <div class="sd-section text-center text-muted" style="padding:30px 18px;">
                    <i class="bi bi-info-circle fs-3 d-block mb-2" style="color:#b8c0cf;"></i>
                    <div style="font-size:0.88rem; font-style:italic;">لا توجد بيانات إضافية</div>
                </div>
            @endif
        </div>
    </div>
</div>
