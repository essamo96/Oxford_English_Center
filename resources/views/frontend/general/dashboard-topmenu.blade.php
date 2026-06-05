{{-- ============================================================
     Dashboard top menu bar: breadcrumb + page title + quick actions.
     English / LTR only. Screens feed it via @section('page-title'),
     @section('breadcrumb') and @section('quick-actions'). Expects $dashBase.
     ============================================================ --}}
<div class="ox-dash__topmenu">
    <div class="ox-dash__titlewrap">
        <h1 class="ox-dash__pagetitle">@yield('page-title', 'Dashboard')</h1>
        <nav class="ox-dash__breadcrumb" aria-label="breadcrumb">
            @hasSection('breadcrumb')
                @yield('breadcrumb')
            @else
                <a href="{{ url($dashBase) }}">Home</a>
                <span class="sep">/</span>
                <span>@yield('page-title', 'Dashboard')</span>
            @endif
        </nav>
    </div>

    <div class="ox-dash__quick">
        @yield('quick-actions')
    </div>
</div>
