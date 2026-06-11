{{-- Top navigation tabs: Dashboard | Financial Center.  $tab = 'dashboard' | 'financial' --}}
@php $tab = $tab ?? 'dashboard'; @endphp
<div class="card card-flush shadow-sm mb-5">
    <div class="card-body py-3 px-4">
        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-5 fw-semibold">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ $tab === 'dashboard' ? 'active' : '' }}"
                   href="{{ route('dashboard.view') }}">
                    <i class="ki-duotone ki-element-11 fs-2 me-2 {{ $tab === 'dashboard' ? 'text-primary' : '' }}"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    لوحة التحكم
                </a>
            </li>
            @can('admin.financial_dashboard.view')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center {{ $tab === 'financial' ? 'active' : '' }}"
                       href="{{ route('financial_dashboard.view') }}">
                        <i class="ki-duotone ki-chart-line-up fs-2 me-2 {{ $tab === 'financial' ? 'text-success' : '' }}"><span class="path1"></span><span class="path2"></span></i>
                        المركز المالي
                        <span class="badge badge-light-success ms-2 fs-9">Financial Center</span>
                    </a>
                </li>
            @endcan
        </ul>
    </div>
</div>
