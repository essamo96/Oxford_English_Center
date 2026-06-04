@php
    $monthsAr = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
    $orgName  = $settings->name ?? 'Oxford English Center';
    // Stored form (invoice) number = the saved form id, zero-padded. Draft = not saved yet.
    $formNo   = $form ? ('SAL-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($form->id, 5, '0', STR_PAD_LEFT)) : null;
    $issuedAt = ($form && $form->created_at) ? $form->created_at : now();
@endphp

{{-- Theme-aware salary "document": colours follow dark/light via Bootstrap CSS vars on screen,
     and fall back to a clean light palette when printed (the print window has no theme vars). --}}
<div id="salaryPreviewDoc" dir="rtl">
    <style>
        #salaryPreviewDoc{
            --sp-bg:        var(--bs-body-bg, #ffffff);
            --sp-fg:        var(--bs-heading-color, var(--bs-body-color, #14213d));
            --sp-text:      var(--bs-body-color, #374151);
            --sp-muted:     var(--bs-secondary-color, #6b7280);
            --sp-line:      var(--bs-border-color, #e5e7eb);
            --sp-soft:      var(--bs-gray-100, #f3f5f9);
            --sp-soft-head: var(--bs-gray-200, #eef2f7);
            --sp-brand:#14213d; --sp-gold:#f5c518; --sp-green:#16a34a; --sp-red:#dc2626;
            background:var(--sp-bg); color:var(--sp-text);
            border:1px solid var(--sp-line); border-radius:12px; padding:28px;
            max-width:820px; margin:0 auto;
            font-family:'Segoe UI',Tahoma,Arial,sans-serif;
            box-shadow:0 4px 18px rgba(0,0,0,.06);
            position:relative; overflow:hidden;
        }
        #salaryPreviewDoc .sp-watermark{
            position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
            pointer-events:none; z-index:0;
        }
        #salaryPreviewDoc .sp-watermark img{
            width:70%; max-width:480px; opacity:.10; filter:grayscale(1);
            -webkit-print-color-adjust:exact; print-color-adjust:exact;
        }
        #salaryPreviewDoc .sp-body{ position:relative; z-index:1; }
        #salaryPreviewDoc .sp-strong{ color:var(--sp-fg); }
        #salaryPreviewDoc .sp-muted{ color:var(--sp-muted); }
        #salaryPreviewDoc table{ width:100%; border-collapse:collapse; }
        /* translucent fills so the watermark bleeds through (theme-neutral grey) */
        #salaryPreviewDoc .sp-cell{ border:1px solid var(--sp-line); }
        #salaryPreviewDoc .sp-soft{ background:rgba(128,128,128,.07); }
        #salaryPreviewDoc .sp-head-row th{ background:rgba(128,128,128,.10); color:var(--sp-text); border:1px solid var(--sp-line); }
        #salaryPreviewDoc .sp-divider{ border-bottom:3px solid var(--sp-gold); }
        #salaryPreviewDoc .sp-net td{ background:var(--sp-brand); color:#fff; border:1px solid var(--sp-gold); }
        #salaryPreviewDoc .sp-net .sp-net-val{ color:var(--sp-gold); }
        #salaryPreviewDoc .sp-sign{ border-top:1px solid var(--sp-muted); }
        #salaryPreviewDoc .sp-green{ color:var(--sp-green); }
        #salaryPreviewDoc .sp-red{ color:var(--sp-red); }
        #salaryPreviewDoc .sp-badge{ background:#dcfce7; color:#166534; font-weight:700; padding:2px 10px; border-radius:999px; font-size:12px; }
        @media print{
            /* Always print on clean white paper regardless of the active theme */
            #salaryPreviewDoc{
                --sp-bg:#fff; --sp-fg:#14213d; --sp-text:#1f2937; --sp-muted:#6b7280;
                --sp-line:#e5e7eb; --sp-soft:#f3f5f9; --sp-soft-head:#eef2f7;
                box-shadow:none; border:none;
            }
            /* Force backgrounds, translucent fills and the watermark to actually print */
            #salaryPreviewDoc, #salaryPreviewDoc *{
                -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important;
            }
            #salaryPreviewDoc .sp-watermark img{ opacity:.12 !important; }
        }
    </style>

    {{-- Oxford logo watermark (shows on screen & in print) --}}
    <div class="sp-watermark" aria-hidden="true">
        <img src="{{ url('assets/oxford/img/logo.png') }}" alt="">
    </div>

    <div class="sp-body">

    {{-- Header: logo + org + (number / date / period) --}}
    <div class="sp-divider" style="display:flex; align-items:center; justify-content:space-between; gap:16px; padding-bottom:16px; margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <img src="{{ url('assets/oxford/img/logo.png') }}" alt="logo" style="height:58px;">
            <div>
                <div class="sp-strong" style="font-size:18px; font-weight:800;">{{ $orgName }}</div>
                <div class="sp-muted" style="font-size:12px;">قسم الموارد البشرية — كشف راتب معلّم</div>
            </div>
        </div>
        <div style="text-align:left; font-size:13px;">
            <div><span class="sp-muted">رقم الاستمارة:</span>
                <strong class="sp-strong">{{ $formNo ?? 'مسودة (غير محفوظة)' }}</strong></div>
            <div><span class="sp-muted">التاريخ:</span>
                <strong class="sp-strong">{{ $issuedAt->format('Y-m-d') }}</strong></div>
            <div><span class="sp-muted">الفترة:</span>
                <strong class="sp-strong">{{ $monthsAr[$month] ?? $month }} {{ $year }}</strong></div>
        </div>
    </div>

    {{-- Teacher + status --}}
    <div style="display:flex; flex-wrap:wrap; justify-content:space-between; gap:12px; margin-bottom:18px; font-size:14px;">
        <div><span class="sp-muted">المعلّم:</span> <strong class="sp-strong">{{ $teacher->name ?? '—' }}</strong></div>
        <div>
            <span class="sp-muted">الحالة:</span>
            @if($form && $form->status === 'closed')
                <strong class="sp-green">مغلق</strong>
            @else
                <strong style="color:#d97706;">{{ $form ? 'مفتوح' : 'مسودة (غير محفوظة)' }}</strong>
            @endif
            @if($form && $form->is_received)
                <span class="sp-badge" style="margin-inline-start:8px;">✓ تم الاستلام</span>
            @endif
        </div>
    </div>

    {{-- Figures --}}
    <table style="margin-bottom:18px; font-size:14px;">
        <tbody>
            <tr>
                <td class="sp-cell sp-soft sp-strong" style="padding:10px 12px; font-weight:700; width:55%;">عدد المحاضرات</td>
                <td class="sp-cell" style="padding:10px 12px; text-align:center;">{{ $lectures }}</td>
            </tr>
            <tr>
                <td class="sp-cell sp-soft sp-strong" style="padding:10px 12px; font-weight:700;">أجر المحاضرة</td>
                <td class="sp-cell" style="padding:10px 12px; text-align:center;">{{ number_format($rate,2) }} ILS</td>
            </tr>
            <tr>
                <td class="sp-cell sp-soft sp-strong" style="padding:10px 12px; font-weight:700;">الإجمالي (المحاضرات × الأجر)</td>
                <td class="sp-cell" style="padding:10px 12px; text-align:center;">{{ number_format($gross,2) }} ILS</td>
            </tr>
            <tr>
                <td class="sp-cell sp-soft sp-strong" style="padding:10px 12px; font-weight:700;">علاوة</td>
                <td class="sp-cell sp-green" style="padding:10px 12px; text-align:center;">+ {{ number_format($bonus,2) }} ILS</td>
            </tr>
            <tr>
                <td class="sp-cell sp-soft sp-strong" style="padding:10px 12px; font-weight:700;">خصم</td>
                <td class="sp-cell sp-red" style="padding:10px 12px; text-align:center;">− {{ number_format($deduction,2) }} ILS</td>
            </tr>
            <tr class="sp-net">
                <td class="sp-cell" style="padding:12px; font-weight:800; font-size:15px;">صافي الراتب</td>
                <td class="sp-cell sp-net-val" style="padding:12px; text-align:center; font-weight:800; font-size:18px;">{{ number_format($net,2) }} ILS</td>
            </tr>
        </tbody>
    </table>

    {{-- Lecture details --}}
    @if($lines->count())
        <div class="sp-strong" style="font-weight:700; margin-bottom:8px;">تفاصيل المحاضرات المحتسبة ({{ $lines->count() }})</div>
        <table style="font-size:12.5px; margin-bottom:22px;">
            <thead>
                <tr class="sp-head-row">
                    <th style="padding:7px;">#</th>
                    <th style="padding:7px;">التاريخ</th>
                    <th style="padding:7px;">المجموعة</th>
                    <th style="padding:7px;">الأجر</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lines as $i => $ln)
                    <tr style="text-align:center;">
                        <td class="sp-cell" style="padding:6px;">{{ $i+1 }}</td>
                        <td class="sp-cell" style="padding:6px;">{{ \Carbon\Carbon::parse($ln['date'])->format('Y-m-d') }}</td>
                        <td class="sp-cell" style="padding:6px;">{{ $ln['group'] ?? '—' }}</td>
                        <td class="sp-cell" style="padding:6px;">{{ number_format($ln['amount'],2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Signatures --}}
    <div style="display:flex; justify-content:space-between; gap:24px; margin-top:30px; font-size:13px;">
        <div style="text-align:center;">
            <div class="sp-sign sp-text" style="width:180px; padding-top:6px;">توقيع المستلِم (المعلّم)</div>
            @if($form && $form->is_received && $form->received_at)
                <div class="sp-green" style="font-size:11px; margin-top:4px;">استُلم بتاريخ {{ $form->received_at->format('Y-m-d') }}@if($form->receivedBy) — سجّله {{ $form->receivedBy->name }}@endif</div>
            @endif
        </div>
        <div style="text-align:center;">
            <div class="sp-sign sp-text" style="width:180px; padding-top:6px;">توقيع الإدارة المالية</div>
            @if($form && $form->closedBy)
                <div class="sp-muted" style="font-size:11px; margin-top:4px;">أغلق الكشف: {{ $form->closedBy->name }}</div>
            @endif
        </div>
    </div>

    </div>{{-- /.sp-body --}}
</div>
