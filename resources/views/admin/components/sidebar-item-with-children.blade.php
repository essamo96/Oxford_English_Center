{{--
    Props:
      $item        — PermissionsGroup model (name, name_ar, icon, mychild)
      $active_menu — current page key
--}}
@php
    $permission  = 'admin.' . $item->name . '.view';
    $childNames  = $item->mychild->pluck('name')->toArray();
    $isOpen      = in_array($active_menu ?? '', $childNames);
@endphp

@can($permission)
    <div data-kt-menu-trigger="click"
         class="menu-item {{ $isOpen ? 'here show' : '' }} menu-accordion">
        <span class="menu-link">
            <span class="menu-icon">
                <i class="{{ $item->icon ?? 'ki-duotone ki-element-plus' }} fs-1"
                   style="color: {{ $item->color ?? 'var(--bs-info)' }}">
                    <span class="path1"></span><span class="path2"></span>
                    <span class="path3"></span><span class="path4"></span>
                </i>
            </span>
            <span class="menu-title">
                {{ $item->name_ar ?? $item->name }}
                @if($item->name == 'combo_requests')
                    @php
                        $combo_unread = \App\Models\StudentCompo::where('is_read', false)->count();
                    @endphp
                    <span class="menu-badge" style="{{ $combo_unread > 0 ? '' : 'display:none' }}">
                        <span class="badge badge-danger badge-circle mx-2" data-live-counter="combo_requests_total">{{ $combo_unread }}</span>
                    </span>
                @endif
            </span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion">
            @foreach($item->mychild as $child)
                @php
                    $childRoute      = $child->name . '.view';
                    $childPermission = 'admin.' . $child->name . '.view';
                    $childActive     = ($active_menu ?? '') === $child->name;
                    $childRouteExists = \Illuminate\Support\Facades\Route::has($childRoute);
                @endphp
                @can($childPermission)
                    @if($childRouteExists)
                        <div class="menu-item">
                            <a class="menu-link {{ $childActive ? 'active' : '' }}" href="{{ route($childRoute) }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">
                                    {{ $child->name_ar ?? $child->name }}
                                    @if($child->name == 'standalone_registrations')
                                        @php
                                            $standalone_unread = \App\Models\StudentCompo::where('is_read', false)->count();
                                        @endphp
                                        <span class="menu-badge" style="{{ $standalone_unread > 0 ? '' : 'display:none' }}">
                                            <span class="badge badge-sm badge-circle badge-light-danger ms-2" data-live-counter="unread_combo_registrations">{{ $standalone_unread }}</span>
                                        </span>
                                    @endif
                                    @if($child->name == 'combo_parents')
                                        @php
                                            $parents_unread = \App\Models\StudentCompo::where('is_read', false)->whereRaw('TIMESTAMPDIFF(YEAR, dob, CURDATE()) <= 15')->count();
                                        @endphp
                                        @if($parents_unread > 0)
                                            <span class="badge badge-sm badge-circle badge-light-danger ms-2">{{ $parents_unread }}</span>
                                        @endif
                                    @endif
                                </span>
                            </a>
                        </div>
                    @endif
                @endcan
            @endforeach
        </div>
    </div>
@endcan
