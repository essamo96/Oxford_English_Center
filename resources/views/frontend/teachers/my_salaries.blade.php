@php
    $monthsEn = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
    $statusMap = [
        'draft'  => ['Draft', '#94a3b8'],
        'open'   => ['Open (calculating)', '#d97706'],
        'closed' => ['Closed (received)', '#16a34a'],
    ];
    $totalReceived = $forms->where('status','closed')->sum('net_amount');
    $totalLectures = $forms->sum('lectures_count');
@endphp

<div class="ajax-content" style="font-family:inherit; color:var(--text-primary);">
    {{-- Summary --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:18px;">
        <div style="background:linear-gradient(135deg,#065f46,#10b981); color:#fff; border-radius:12px; padding:14px; text-align:center;">
            <div style="font-size:12px; opacity:.85;">Total Received</div>
            <div style="font-size:20px; font-weight:800;">{{ number_format($totalReceived,2) }} <small>ILS</small></div>
        </div>
        <div style="background:linear-gradient(135deg,#1e3a8a,#3b82f6); color:#fff; border-radius:12px; padding:14px; text-align:center;">
            <div style="font-size:12px; opacity:.85;">Total Lectures</div>
            <div style="font-size:20px; font-weight:800;">{{ $totalLectures }}</div>
        </div>
        <div style="background:linear-gradient(135deg,#7c3aed,#a78bfa); color:#fff; border-radius:12px; padding:14px; text-align:center;">
            <div style="font-size:12px; opacity:.85;">Forms Count</div>
            <div style="font-size:20px; font-weight:800;">{{ $forms->count() }}</div>
        </div>
    </div>

    @forelse($forms as $form)
        @php $st = $statusMap[$form->status] ?? [$form->status,'#94a3b8']; @endphp
        <div style="border:1px solid var(--border-color); border-radius:12px; margin-bottom:14px; overflow:hidden;">
            {{-- Form header --}}
            <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:10px; padding:12px 16px; background:var(--surface-2); border-bottom:1px solid var(--border-color);">
                <div style="font-weight:800; color:var(--text-primary);">
                    <i class="fa fa-calendar-o" style="color:var(--d-accent);"></i>
                    {{ $monthsEn[$form->month] ?? $form->month }} {{ $form->year }}
                    <span style="background:{{ $st[1] }}1a; color:{{ $st[1] }}; font-size:11px; font-weight:700; padding:3px 10px; border-radius:999px; margin-inline-start:8px;">{{ $st[0] }}</span>
                </div>
                <div style="font-size:18px; font-weight:800; color:#16a34a;">{{ number_format($form->net_amount,2) }} <small style="color:var(--text-secondary);">ILS</small></div>
            </div>

            {{-- Figures --}}
            <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:8px; padding:12px 16px; text-align:center; font-size:12px;">
                <div><div style="color:var(--text-secondary);">Lectures</div><div style="font-weight:700;">{{ $form->lectures_count }}</div></div>
                <div><div style="color:var(--text-secondary);">Lecture Rate</div><div style="font-weight:700;">{{ number_format($form->lecture_rate,2) }}</div></div>
                <div><div style="color:var(--text-secondary);">Gross</div><div style="font-weight:700;">{{ number_format($form->gross_amount,2) }}</div></div>
                <div><div style="color:var(--text-secondary);">Bonus</div><div style="font-weight:700; color:#16a34a;">{{ number_format($form->bonus,2) }}</div></div>
                <div><div style="color:var(--text-secondary);">Deduction</div><div style="font-weight:700; color:#dc2626;">{{ number_format($form->deduction,2) }}</div></div>
            </div>

            {{-- Per-lecture details (collapsible) --}}
            @if($form->details->count())
                <details style="border-top:1px dashed var(--border-color);">
                    <summary style="cursor:pointer; padding:10px 16px; font-size:13px; font-weight:700; color:var(--d-accent);">
                        <i class="fa fa-list-ul"></i> Calculated lecture details ({{ $form->details->count() }})
                    </summary>
                    <div style="padding:0 16px 14px;">
                        <table style="width:100%; border-collapse:collapse; font-size:12.5px;">
                            <thead>
                                <tr style="color:var(--text-secondary); text-align:center;">
                                    <th style="padding:6px; border-bottom:1px solid var(--border-color);">#</th>
                                    <th style="padding:6px; border-bottom:1px solid var(--border-color);">Date</th>
                                    <th style="padding:6px; border-bottom:1px solid var(--border-color);">Group</th>
                                    <th style="padding:6px; border-bottom:1px solid var(--border-color);">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($form->details as $i => $d)
                                    <tr style="text-align:center;">
                                        <td style="padding:6px; border-bottom:1px solid var(--border-color);">{{ $i+1 }}</td>
                                        <td style="padding:6px; border-bottom:1px solid var(--border-color);">{{ \Carbon\Carbon::parse($d->lecture_date)->format('M j, Y') }}</td>
                                        <td style="padding:6px; border-bottom:1px solid var(--border-color);">{{ optional($d->group)->name ?? '—' }}</td>
                                        <td style="padding:6px; border-bottom:1px solid var(--border-color); font-weight:700;">{{ number_format($d->amount,2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <i class="fa fa-money"></i>
            <p>No salary forms yet.</p>
        </div>
    @endforelse
</div>
