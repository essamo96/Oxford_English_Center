{{--
    Props:
      $item        — PermissionsGroup model (name, name_ar, icon)
      $active_menu — current page key
--}}
@php
    $routeName  = $item->name . '.view';
    $permission = 'admin.' . $item->name . '.view';
    $isActive   = ($active_menu ?? '') === $item->name;
    $routeExists = \Illuminate\Support\Facades\Route::has($routeName);
@endphp

@can($permission)
    @if($routeExists)
        <div class="menu-item">
            <a class="menu-link {{ $isActive ? 'active' : '' }}" href="{{ route($routeName) }}">
                <span class="menu-icon">
                    <i class="{{ $item->icon ?? 'ki-duotone ki-element-plus' }} fs-1"
                           style="color: {{ $item->color ?? 'var(--bs-info)' }}">
                        <span class="path1"></span><span class="path2"></span>
                        <span class="path3"></span><span class="path4"></span>
                    </i>
                </span>
                <span class="menu-title">{{ $item->name_ar ?? $item->name }}</span>
            </a>
        </div>
    @endif
@endcan
