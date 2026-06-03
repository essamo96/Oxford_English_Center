@php
    $monthsAr = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
    $statusMap = [
        'draft'  => ['مسودة', '#94a3b8'],
        'open'   => ['مفتوح (قيد الاحتساب)', '#d97706'],
        'closed' => ['مغلق (مستلَم)', '#16a34a'],
    ];
    $totalReceived = $forms->where('status','closed')->sum('net_amount');
    $totalLectures = $forms->sum('lectures_count');
@endphp

<div style="font-family:inherit; color:#1f2937;">
    {{-- Summary --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:18px;">
        <div style="background:linear-gradient(135deg,#065f46,#10b981); color:#fff; border-radius:12px; padding:14px; text-align:center;">
            <div style="font-size:12px; opacity:.85;">إجمالي المستلَم</div>
            <div style="font-size:20px; font-weight:800;">{{ number_format($totalReceived,2) }} <small>ILS</small></div>
        </div>
        <div style="background:linear-gradient(135deg,#1e3a8a,#3b82f6); color:#fff; border-radius:12px; padding:14px; text-align:center;">
            <div style="font-size:12px; opacity:.85;">إجمالي المحاضرات</div>
            <div style="font-size:20px; font-weight:800;">{{ $totalLectures }}</div>
        </div>
        <div style="background:linear-gradient(135deg,#7c3aed,#a78bfa); color:#fff; border-radius:12px; padding:14px; text-align:center;">
            <div style="font-size:12px; opacity:.85;">عدد الاستمارات</div>
            <div style="font-size:20px; font-weight:800;">{{ $forms->count() }}</div>
        </div>
    </div>

    @forelse($forms as $form)
        @php $st = $statusMap[$form->status] ?? [$form->status,'#94a3b8']; @endphp
        <div style="border:1px solid #e5e9f0; border-radius:12px; margin-bottom:14px; overflow:hidden;">
            {{-- Form header --}}
            <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:10px; padding:12px 16px; background:#f8fafc; border-bottom:1px solid #eef2f7;">
                <div style="font-weight:800; color:#14213d;">
                    <i class="fa fa-calendar-o" style="color:#3b82f6;"></i>
                    {{ $monthsAr[$form->month] ?? $form->month }} {{ $form->year }}
                    <span style="background:{{ $st[1] }}1a; color:{{ $st[1] }}; font-size:11px; font-weight:700; padding:3px 10px; border-radius:999px; margin-inline-start:8px;">{{ $st[0] }}</span>
                </div>
                <div style="font-size:18px; font-weight:800; color:#16a34a;">{{ number_format($form->net_amount,2) }} <small style="color:#64748b;">ILS</small></div>
            </div>

            {{-- Figures --}}
            <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:8px; padding:12px 16px; text-align:center; font-size:12px;">
                <div><div style="color:#64748b;">المحاضرات</div><div style="font-weight:700;">{{ $form->lectures_count }}</div></div>
                <div><div style="color:#64748b;">أجر المحاضرة</div><div style="font-weight:700;">{{ number_format($form->lecture_rate,2) }}</div></div>
                <div><div style="color:#64748b;">الإجمالي</div><div style="font-weight:700;">{{ number_format($form->gross_amount,2) }}</div></div>
                <div><div style="color:#64748b;">علاوة</div><div style="font-weight:700; color:#16a34a;">{{ number_format($form->bonus,2) }}</div></div>
                <div><div style="color:#64748b;">خصم</div><div style="font-weight:700; color:#dc2626;">{{ number_format($form->deduction,2) }}</div></div>
            </div>

            {{-- Per-lecture details (collapsible) --}}
            @if($form->details->count())
                <details style="border-top:1px dashed #e5e9f0;">
                    <summary style="cursor:pointer; padding:10px 16px; font-size:13px; font-weight:700; color:#3b82f6;">
                        <i class="fa fa-list-ul"></i> تفاصيل المحاضرات المحتسبة ({{ $form->details->count() }})
                    </summary>
                    <div style="padding:0 16px 14px;">
                        <table style="width:100%; border-collapse:collapse; font-size:12.5px;">
                            <thead>
                                <tr style="color:#64748b; text-align:center;">
                                    <th style="padding:6px; border-bottom:1px solid #eef2f7;">#</th>
                                    <th style="padding:6px; border-bottom:1px solid #eef2f7;">التاريخ</th>
                                    <th style="padding:6px; border-bottom:1px solid #eef2f7;">المجموعة</th>
                                    <th style="padding:6px; border-bottom:1px solid #eef2f7;">الأجر</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($form->details as $i => $d)
                                    <tr style="text-align:center;">
                                        <td style="padding:6px; border-bottom:1px solid #f5f7fa;">{{ $i+1 }}</td>
                                        <td style="padding:6px; border-bottom:1px solid #f5f7fa;">{{ \Carbon\Carbon::parse($d->lecture_date)->format('Y-m-d') }}</td>
                                        <td style="padding:6px; border-bottom:1px solid #f5f7fa;">{{ optional($d->group)->name ?? '—' }}</td>
                                        <td style="padding:6px; border-bottom:1px solid #f5f7fa; font-weight:700;">{{ number_format($d->amount,2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            @endif
        </div>
    @empty
        <div style="text-align:center; padding:40px; color:#94a3b8;">
            <i class="fa fa-money fa-2x" style="opacity:.5;"></i>
            <p style="margin-top:10px; font-weight:600;">لا توجد استمارات رواتب بعد.</p>
        </div>
    @endforelse
</div>
