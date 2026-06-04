@extends('admin.layout.master')

@section('title', 'المركز المالي — Financial Center')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">المركز المالي</li>
@stop

@section('css')
<style>
    .fc-card{ transition:transform .25s ease, box-shadow .25s ease; }
    .fc-card:hover{ transform:translateY(-4px); box-shadow:0 14px 34px rgba(0,0,0,.10) !important; }
    .fc-glass{
        background:linear-gradient(135deg, rgba(var(--bs-primary-rgb,.08),.06), rgba(var(--bs-body-bg-rgb),.4));
        backdrop-filter:saturate(140%) blur(6px);
        border:1px solid var(--bs-border-color);
    }
    .fc-ico{ width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center; }
    .fc-metric{ font-size:1.7rem; font-weight:800; line-height:1.1; }
    .fc-skel{ color:transparent !important;border-radius:8px;
        background:linear-gradient(90deg,var(--bs-gray-200) 25%,var(--bs-gray-100) 37%,var(--bs-gray-200) 63%);
        background-size:400% 100%; animation:fcshimmer 1.4s ease infinite; display:inline-block; min-width:90px; min-height:1.2em; }
    .fc-skel-line{ height:14px;border-radius:6px;margin:6px 0;
        background:linear-gradient(90deg,var(--bs-gray-200) 25%,var(--bs-gray-100) 37%,var(--bs-gray-200) 63%);
        background-size:400% 100%; animation:fcshimmer 1.4s ease infinite; }
    @keyframes fcshimmer{0%{background-position:100% 0}100%{background-position:-100% 0}}
    .fc-feed-line:not(:last-child){ border-bottom:1px dashed var(--bs-border-color); }
    [dir="rtl"] .nav-line-tabs .nav-item{ margin-left:1.5rem; margin-right:0; }
</style>
@stop

@section('page-content')
@include('admin.dashboard._tabs', ['tab' => 'financial'])

{{-- Header --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-5 gap-3">
    <div>
        <h2 class="fw-bold mb-1"><i class="ki-duotone ki-chart-line-up fs-1 text-success me-2"><span class="path1"></span><span class="path2"></span></i>المركز المالي</h2>
        <span class="text-muted fs-7">رؤية لحظية كاملة عن الإيرادات والذمم والرواتب والتدفقات النقدية</span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted fs-8">آخر تحديث: <span id="fc-updated" class="fw-bold">—</span></span>
        <button class="btn btn-sm btn-light-primary" id="fc-refresh"><i class="ki-duotone ki-arrows-circle fs-4"><span class="path1"></span><span class="path2"></span></i>تحديث</button>
    </div>
</div>

{{-- ============ SECTION 1 : Executive Overview ============ --}}
<div class="row g-5 mb-2">
    @php
        $cards = [
            ['k'=>'total_revenue','t'=>'إجمالي الإيرادات','s'=>'Total Revenue','i'=>'ki-chart-line-up','c'=>'success','money'=>true],
            ['k'=>'outstanding','t'=>'الذمم المدينة','s'=>'Outstanding Receivables','i'=>'ki-wallet','c'=>'danger','money'=>true],
            ['k'=>'collected_this_month','t'=>'تحصيل الشهر الحالي','s'=>'Collected This Month','i'=>'ki-dollar','c'=>'primary','money'=>true],
            ['k'=>'credit_balance','t'=>'أرصدة الطلاب الدائنة','s'=>'Student Credit Balance','i'=>'ki-bank','c'=>'info','money'=>true],
            ['k'=>'active_with_dues','t'=>'طلاب عليهم مستحقات','s'=>'Active Students With Dues','i'=>'ki-user','c'=>'warning','money'=>false],
            ['k'=>'transactions_this_month','t'=>'الحركات المالية (الشهر)','s'=>'Financial Transactions','i'=>'ki-arrows-loop','c'=>'dark','money'=>false],
            ['k'=>'expenses_this_month','t'=>'مصروفات الشهر','s'=>'Expenses This Month','i'=>'ki-minus-circle','c'=>'danger','money'=>true],
            ['k'=>'net_this_month','t'=>'صافي الشهر (تحصيل − مصروف)','s'=>'Net This Month','i'=>'ki-chart-pie-simple','c'=>'success','money'=>true],
        ];
    @endphp
    @foreach($cards as $c)
        <div class="col-md-6 col-xl-4">
            <div class="card card-flush fc-card fc-glass shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-4 py-5">
                    <div class="fc-ico bg-light-{{ $c['c'] }}">
                        <i class="ki-duotone {{ $c['i'] }} fs-2x text-{{ $c['c'] }}"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fc-metric text-gray-900 fc-skel"
                             id="fc-{{ $c['k'] }}" data-money="{{ $c['money'] ? 1 : 0 }}">0</div>
                        <div class="fw-semibold text-gray-700">{{ $c['t'] }}</div>
                        <div class="text-muted fs-8">{{ $c['s'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ============ SECTION 2 : Live Charts ============ --}}
<div class="row g-5 mt-2">
    <div class="col-xl-6">
        <div class="card card-flush fc-card shadow-sm h-100">
            <div class="card-header pt-5"><h3 class="card-title fw-bold"><i class="ki-duotone ki-chart-line-up fs-2 text-success me-2"><span class="path1"></span><span class="path2"></span></i>اتجاه الإيرادات الشهري</h3><div class="card-toolbar text-muted fs-8">آخر 12 شهر</div></div>
            <div class="card-body pt-2"><div id="fc-chart-revenue" style="min-height:300px;"></div></div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card card-flush fc-card shadow-sm h-100">
            <div class="card-header pt-5"><h3 class="card-title fw-bold"><i class="ki-duotone ki-chart-simple fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>المحصّل مقابل المستحق</h3></div>
            <div class="card-body pt-2"><div id="fc-chart-collections" style="min-height:300px;"></div></div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card card-flush fc-card shadow-sm h-100">
            <div class="card-header pt-5"><h3 class="card-title fw-bold"><i class="ki-duotone ki-arrows-loop fs-2 text-info me-2"><span class="path1"></span><span class="path2"></span></i>الإيرادات مقابل المصروفات</h3><div class="card-toolbar text-muted fs-8">المصروفات = رواتب المعلمين</div></div>
            <div class="card-body pt-2"><div id="fc-chart-ie" style="min-height:300px;"></div></div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card card-flush fc-card shadow-sm h-100">
            <div class="card-header pt-5"><h3 class="card-title fw-bold"><i class="ki-duotone ki-dollar fs-2 text-warning me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>التدفق النقدي اليومي</h3><div class="card-toolbar text-muted fs-8">الشهر الحالي</div></div>
            <div class="card-body pt-2"><div id="fc-chart-cashflow" style="min-height:300px;"></div></div>
        </div>
    </div>
</div>

{{-- ============ SECTION 3 : Teacher Payroll Center ============ --}}
<h3 class="fw-bold mt-10 mb-4"><i class="ki-duotone ki-wallet fs-1 text-primary me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>مركز رواتب المعلمين</h3>
<div class="row g-5">
    @php
        $pcards = [
            ['k'=>'paid','t'=>'الرواتب المصروفة','s'=>'Paid Salaries','i'=>'ki-dollar','c'=>'success','money'=>true],
            ['k'=>'pending','t'=>'الرواتب غير المصروفة','s'=>'Pending Salaries','i'=>'ki-time','c'=>'warning','money'=>true],
            ['k'=>'awaiting','t'=>'معلمون بانتظار الصرف','s'=>'Teachers Awaiting Payment','i'=>'ki-user-square','c'=>'danger','money'=>false],
            ['k'=>'this_month','t'=>'رواتب الشهر الحالي','s'=>'Payroll This Month','i'=>'ki-bank','c'=>'info','money'=>true],
        ];
    @endphp
    @foreach($pcards as $c)
        <div class="col-md-6 col-xl-3">
            <div class="card card-flush fc-card fc-glass shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-4 py-5">
                    <div class="fc-ico bg-light-{{ $c['c'] }}"><i class="ki-duotone {{ $c['i'] }} fs-2x text-{{ $c['c'] }}"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></div>
                    <div class="flex-grow-1">
                        <div class="fc-metric text-gray-900 fc-skel" id="fc-pay-{{ $c['k'] }}" data-money="{{ $c['money'] ? 1 : 0 }}">0</div>
                        <div class="fw-semibold text-gray-700">{{ $c['t'] }}</div>
                        <div class="text-muted fs-8">{{ $c['s'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="row g-5 mt-1">
    <div class="col-12">
        <div class="card card-flush fc-card shadow-sm">
            <div class="card-header pt-5"><h3 class="card-title fw-bold"><i class="ki-duotone ki-chart-simple-2 fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>اتجاه رواتب المعلمين الشهري</h3></div>
            <div class="card-body pt-2"><div id="fc-chart-payroll" style="min-height:280px;"></div></div>
        </div>
    </div>
</div>

{{-- ============ SECTION 4 : Academic Financial Status ============ --}}
<h3 class="fw-bold mt-10 mb-4"><i class="ki-duotone ki-calendar fs-1 text-success me-2"><span class="path1"></span><span class="path2"></span></i>الحالة المالية للأشهر</h3>
<div class="row g-5">
    @php
        $mcards = [
            ['k'=>'closed','t'=>'الأشهر المالية المغلقة','s'=>'Closed Financial Months','i'=>'ki-check-circle','c'=>'success'],
            ['k'=>'open','t'=>'الأشهر المالية المفتوحة','s'=>'Open Financial Months','i'=>'ki-folder','c'=>'warning'],
            ['k'=>'current_period','t'=>'الفترة المالية الحالية','s'=>'Current Fiscal Period','i'=>'ki-calendar','c'=>'primary'],
            ['k'=>'last_closing','t'=>'آخر إغلاق مالي','s'=>'Last Closing Date','i'=>'ki-calendar-8','c'=>'info'],
        ];
    @endphp
    @foreach($mcards as $c)
        <div class="col-md-6 col-xl-3">
            <div class="card card-flush fc-card fc-glass shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-4 py-5">
                    <div class="fc-ico bg-light-{{ $c['c'] }}"><i class="ki-duotone {{ $c['i'] }} fs-2x text-{{ $c['c'] }}"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></div>
                    <div class="flex-grow-1">
                        <div class="fc-metric text-gray-900 fc-skel" id="fc-mon-{{ $c['k'] }}" style="font-size:1.3rem;">0</div>
                        <div class="fw-semibold text-gray-700">{{ $c['t'] }}</div>
                        <div class="text-muted fs-8">{{ $c['s'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ============ SECTION 5 + 6 : Activity Feed + Tables ============ --}}
<div class="row g-5 mt-1">
    {{-- Activity timeline --}}
    <div class="col-xl-4">
        <div class="card card-flush fc-card shadow-sm h-100">
            <div class="card-header pt-5"><h3 class="card-title fw-bold"><i class="ki-duotone ki-pulse fs-2 text-danger me-2"><span class="path1"></span><span class="path2"></span></i>سجل الحركات المالية</h3></div>
            <div class="card-body pt-3" id="fc-activity">
                @for($i=0;$i<6;$i++)<div class="fc-skel-line w-100"></div><div class="fc-skel-line w-75"></div>@endfor
            </div>
        </div>
    </div>
    {{-- Tables --}}
    <div class="col-xl-8">
        <div class="card card-flush fc-card shadow-sm mb-5">
            <div class="card-header pt-5"><h3 class="card-title fw-bold text-danger"><i class="ki-duotone ki-wallet fs-2 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>طلاب عليهم رسوم مستحقة</h3></div>
            <div class="card-body pt-2 table-responsive">
                <table class="table align-middle table-row-dashed fs-7 gy-3 text-center mb-0">
                    <thead><tr class="text-muted fw-bold text-uppercase fs-8">
                        <th class="text-start">الطالب</th><th>البرنامج</th><th>الفاتورة</th><th>المدفوع</th><th>المتبقّي</th><th>الحالة</th>
                    </tr></thead>
                    <tbody id="fc-tbl-dues"><tr><td colspan="6" class="py-5"><div class="fc-skel-line w-100"></div></td></tr></tbody>
                </table>
            </div>
        </div>
        <div class="card card-flush fc-card shadow-sm mb-5">
            <div class="card-header pt-5"><h3 class="card-title fw-bold text-info"><i class="ki-duotone ki-bank fs-2 me-2"><span class="path1"></span><span class="path2"></span></i>طلاب أصحاب أرصدة دائنة</h3></div>
            <div class="card-body pt-2 table-responsive">
                <table class="table align-middle table-row-dashed fs-7 gy-3 text-center mb-0">
                    <thead><tr class="text-muted fw-bold text-uppercase fs-8">
                        <th class="text-start">الطالب</th><th>الرصيد الدائن</th><th>آخر حركة</th><th>الحالة</th>
                    </tr></thead>
                    <tbody id="fc-tbl-credit"><tr><td colspan="4" class="py-5"><div class="fc-skel-line w-100"></div></td></tr></tbody>
                </table>
            </div>
        </div>
        <div class="card card-flush fc-card shadow-sm">
            <div class="card-header pt-5"><h3 class="card-title fw-bold text-primary"><i class="ki-duotone ki-user-square fs-2 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>معلمون بانتظار صرف الرواتب</h3></div>
            <div class="card-body pt-2 table-responsive">
                <table class="table align-middle table-row-dashed fs-7 gy-3 text-center mb-0">
                    <thead><tr class="text-muted fw-bold text-uppercase fs-8">
                        <th class="text-start">المعلّم</th><th>الراتب</th><th>الشهر</th><th>الحالة</th>
                    </tr></thead>
                    <tbody id="fc-tbl-payroll"><tr><td colspan="4" class="py-5"><div class="fc-skel-line w-100"></div></td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
(function () {
    var DATA_URL = '{{ route('dashboard.financial.data') }}';
    var charts = {};
    var lastPayload = null;

    function cssVar(n, fb){ var v = getComputedStyle(document.documentElement).getPropertyValue(n).trim(); return v || fb; }
    function money(v){ return Number(v||0).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' ILS'; }
    function intf(v){ return Number(v||0).toLocaleString('en-US'); }

    // animated counter
    function countTo(el, target, isMoney){
        var start = 0, dur = 750, t0 = null;
        function step(ts){
            if(!t0) t0 = ts;
            var p = Math.min((ts - t0)/dur, 1);
            var val = start + (target - start) * (0.5 - Math.cos(p*Math.PI)/2);
            el.textContent = isMoney ? money(val) : intf(Math.round(val));
            if(p < 1) requestAnimationFrame(step);
            else el.textContent = isMoney ? money(target) : intf(target);
        }
        requestAnimationFrame(step);
    }
    function setMetric(id, value, isMoney){
        var el = document.getElementById(id);
        if(!el) return;
        el.classList.remove('fc-skel');
        if(typeof value === 'number'){ countTo(el, value, isMoney); }
        else { el.textContent = value; }
    }

    // chart helpers ------------------------------------------------------
    function palette(){
        return {
            label: cssVar('--bs-gray-500', '#99a1b7'),
            grid:  cssVar('--bs-border-color', '#eaecf0'),
            success: cssVar('--bs-success', '#50cd89'),
            primary: cssVar('--bs-primary', '#009ef7'),
            danger:  cssVar('--bs-danger', '#f1416c'),
            info:    cssVar('--bs-info', '#7239ea'),
            warning: cssVar('--bs-warning', '#ffc700'),
        };
    }
    function baseOpts(){
        var p = palette();
        return {
            chart:{ fontFamily:'inherit', toolbar:{show:false}, animations:{enabled:true}, background:'transparent' },
            theme:{ mode: (document.documentElement.getAttribute('data-bs-theme')==='dark'?'dark':'light') },
            dataLabels:{ enabled:false },
            grid:{ borderColor:p.grid, strokeDashArray:4 },
            tooltip:{ y:{ formatter:function(v){ return money(v); } } },
            xaxis:{ labels:{ style:{ colors:p.label, fontSize:'11px' } }, axisBorder:{show:false}, axisTicks:{show:false} },
            yaxis:{ labels:{ style:{ colors:p.label, fontSize:'11px' }, formatter:function(v){ return intf(Math.round(v)); } } },
            legend:{ labels:{ colors:p.label } }
        };
    }
    function render(key, el, opts){
        if(charts[key]){ charts[key].updateOptions(opts, false, true); }
        else { charts[key] = new ApexCharts(document.querySelector(el), opts); charts[key].render(); }
    }

    function drawCharts(c){
        var p = palette();
        // 1 — revenue area
        render('rev', '#fc-chart-revenue', Object.assign(baseOpts(), {
            series:[{ name:'الإيرادات', data:c.revenue }],
            chart:Object.assign(baseOpts().chart, {type:'area', height:300}),
            colors:[p.success], stroke:{curve:'smooth', width:3}, fill:{type:'gradient', gradient:{opacityFrom:.4, opacityTo:.05}},
            xaxis:Object.assign(baseOpts().xaxis, {categories:c.labels})
        }));
        // 2 — collections vs outstanding (stacked bar)
        render('col', '#fc-chart-collections', Object.assign(baseOpts(), {
            series:[{name:'محصّل', data:c.collected},{name:'مستحق/معلّق', data:c.outstanding}],
            chart:Object.assign(baseOpts().chart, {type:'bar', height:300, stacked:true}),
            colors:[p.success, p.warning], plotOptions:{bar:{borderRadius:5, columnWidth:'55%'}},
            xaxis:Object.assign(baseOpts().xaxis, {categories:c.labels})
        }));
        // 3 — income vs expenses (line)
        render('ie', '#fc-chart-ie', Object.assign(baseOpts(), {
            series:[{name:'الإيرادات', data:c.revenue},{name:'المصروفات', data:c.expenses}],
            chart:Object.assign(baseOpts().chart, {type:'line', height:300}),
            colors:[p.primary, p.danger], stroke:{curve:'smooth', width:3}, markers:{size:3},
            xaxis:Object.assign(baseOpts().xaxis, {categories:c.labels})
        }));
        // 4 — cash flow (smooth area)
        render('cf', '#fc-chart-cashflow', Object.assign(baseOpts(), {
            series:[{name:'التدفق النقدي', data:c.cashflow}],
            chart:Object.assign(baseOpts().chart, {type:'area', height:300}),
            colors:[p.warning], stroke:{curve:'smooth', width:2}, fill:{type:'gradient', gradient:{opacityFrom:.4, opacityTo:.05}},
            xaxis:Object.assign(baseOpts().xaxis, {categories:c.cashflow_labels})
        }));
        // 5 — payroll trend (bar)
        render('pay', '#fc-chart-payroll', Object.assign(baseOpts(), {
            series:[{name:'الرواتب', data:c.payroll_trend}],
            chart:Object.assign(baseOpts().chart, {type:'bar', height:280}),
            colors:[p.primary], plotOptions:{bar:{borderRadius:5, columnWidth:'45%'}},
            xaxis:Object.assign(baseOpts().xaxis, {categories:c.labels})
        }));
    }

    function drawActivity(items){
        var box = document.getElementById('fc-activity');
        if(!items.length){ box.innerHTML = '<div class="text-muted text-center py-10">لا توجد حركات.</div>'; return; }
        box.innerHTML = items.map(function(it){
            return ''+
            '<div class="fc-feed-line d-flex align-items-center py-3 gap-3">'+
              '<span class="fc-ico bg-light-'+it.color+'" style="width:40px;height:40px;border-radius:10px;">'+
                '<i class="ki-duotone '+it.icon+' fs-3 text-'+it.color+'"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>'+
              '</span>'+
              '<div class="flex-grow-1">'+
                '<div class="fw-bold text-gray-800 fs-7">'+it.title+'</div>'+
                '<div class="text-muted fs-8">'+it.desc+'</div>'+
              '</div>'+
              '<div class="text-end">'+
                '<div class="fw-bold text-'+it.color+' fs-7">'+(it.amount ? money(it.amount) : '')+'</div>'+
                '<div class="text-muted fs-9">'+(it.time||'')+'</div>'+
              '</div>'+
            '</div>';
        }).join('');
    }

    function badge(text){
        var map = {'غير مدفوع':'danger','مدفوع جزئياً':'warning','رصيد متاح':'info','بانتظار الصرف':'primary','قيد الإعداد':'secondary'};
        return '<span class="badge badge-light-'+(map[text]||'secondary')+'">'+text+'</span>';
    }
    function fillTable(id, rows, cols, empty){
        var tb = document.getElementById(id);
        if(!rows.length){ tb.innerHTML = '<tr><td colspan="'+cols+'" class="text-muted py-6">'+empty+'</td></tr>'; return; }
        tb.innerHTML = rows.join('');
    }

    function drawTables(t){
        fillTable('fc-tbl-dues', t.dues.map(function(r){
            return '<tr><td class="text-start fw-bold">'+r.student+'</td><td>'+r.program+'</td><td>'+money(r.invoice)+'</td>'+
                   '<td class="text-success">'+money(r.paid)+'</td><td class="text-danger fw-bold">'+money(r.remaining)+'</td><td>'+badge(r.status)+'</td></tr>';
        }), 6, 'لا يوجد طلاب عليهم مستحقات.');

        fillTable('fc-tbl-credit', t.credit.map(function(r){
            return '<tr><td class="text-start fw-bold">'+r.student+'</td><td class="text-info fw-bold">'+money(r.credit)+'</td><td>'+r.last+'</td><td>'+badge(r.status)+'</td></tr>';
        }), 4, 'لا يوجد طلاب أصحاب أرصدة دائنة.');

        fillTable('fc-tbl-payroll', t.payroll.map(function(r){
            return '<tr><td class="text-start fw-bold">'+r.teacher+'</td><td class="fw-bold">'+money(r.salary)+'</td><td>'+r.month+'</td><td>'+badge(r.status)+'</td></tr>';
        }), 4, 'لا يوجد معلمون بانتظار الصرف.');
    }

    function apply(d){
        lastPayload = d;
        var o = d.overview;
        setMetric('fc-total_revenue', o.total_revenue, true);
        setMetric('fc-outstanding', o.outstanding, true);
        setMetric('fc-collected_this_month', o.collected_this_month, true);
        setMetric('fc-credit_balance', o.credit_balance, true);
        setMetric('fc-active_with_dues', o.active_with_dues, false);
        setMetric('fc-transactions_this_month', o.transactions_this_month, false);
        setMetric('fc-expenses_this_month', o.expenses_this_month, true);
        setMetric('fc-net_this_month', o.net_this_month, true);

        var pr = d.payroll;
        setMetric('fc-pay-paid', pr.paid, true);
        setMetric('fc-pay-pending', pr.pending, true);
        setMetric('fc-pay-awaiting', pr.awaiting, false);
        setMetric('fc-pay-this_month', pr.this_month, true);

        var m = d.months;
        setMetric('fc-mon-closed', m.closed, false);
        setMetric('fc-mon-open', m.open, false);
        setMetric('fc-mon-current_period', m.current_period);
        setMetric('fc-mon-last_closing', m.last_closing);

        drawCharts(d.charts);
        drawActivity(d.activity);
        drawTables(d.tables);

        var u = document.getElementById('fc-updated'); if(u) u.textContent = d.generated_at;
    }

    function load(){
        fetch(DATA_URL, {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r){ return r.json(); })
            .then(apply)
            .catch(function(){ var u=document.getElementById('fc-updated'); if(u) u.textContent='تعذّر التحميل'; });
    }

    document.getElementById('fc-refresh').addEventListener('click', load);

    // Re-theme charts when dark/light is toggled (no reload).
    new MutationObserver(function(){ if(lastPayload) drawCharts(lastPayload.charts); })
        .observe(document.documentElement, {attributes:true, attributeFilter:['data-bs-theme']});

    load();
    setInterval(load, 60000); // auto-refresh every 60s
})();
</script>
@stop
