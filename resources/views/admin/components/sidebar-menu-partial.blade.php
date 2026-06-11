{{-- Partial: only the menu items inside #kt_app_sidebar_menu --}}
{{-- Used by sidebar-manager AJAX live reload --}}

@php $dashColor = $dashboardGroup->color ?? '#009ef7'; @endphp

<div class="menu-item">
    <a class="menu-link {{ ($active_menu ?? '') == 'dashboard' ? 'active' : '' }}"
       href="{{ route('dashboard.view') }}">
        <span class="menu-icon">
            <i class="ki-duotone ki-home fs-1" style="color: {{ $dashColor }}">
                <span class="path1"></span><span class="path2"></span>
                <span class="path3"></span><span class="path4"></span>
            </i>
        </span>
        <span class="menu-title">الرئيسية</span>
    </a>
</div>

@foreach($sidebar ?? [] as $item)
    @if($item->mychild->count() > 0)
        @include('admin.components.sidebar-item-with-children', [
            'item'        => $item,
            'active_menu' => $active_menu ?? '',
        ])
    @else
        @include('admin.components.sidebar-item-single', [
            'item'        => $item,
            'active_menu' => $active_menu ?? '',
        ])
    @endif
@endforeach
