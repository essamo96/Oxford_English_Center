@extends('admin.layout.master')

@section('title')
    إدارة القائمة الجانبية
@stop

@section('page-breadcrumb')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
        </li>
        <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
        <li class="breadcrumb-item text-dark">إدارة القائمة الجانبية</li>
    </ul>
@stop

@section('page-content')

@php
/**
 * Metronic 8 full color palette
 * name = label shown in UI, hex = stored value, var = CSS variable (for display)
 */
$metronicColors = [
    // Semantic colors
    ['name' => 'Primary',   'hex' => '#009ef7', 'label' => 'أزرق (Primary)'],
    ['name' => 'Success',   'hex' => '#50cd89', 'label' => 'أخضر (Success)'],
    ['name' => 'Info',      'hex' => '#7239ea', 'label' => 'بنفسجي (Info)'],
    ['name' => 'Warning',   'hex' => '#ffc700', 'label' => 'أصفر (Warning)'],
    ['name' => 'Danger',    'hex' => '#f1416c', 'label' => 'أحمر (Danger)'],
    ['name' => 'Dark',      'hex' => '#181c32', 'label' => 'داكن (Dark)'],
    // Extended palette
    ['name' => 'Cyan',      'hex' => '#0dcaf0', 'label' => 'سماوي (Cyan)'],
    ['name' => 'Teal',      'hex' => '#20c997', 'label' => 'زمردي (Teal)'],
    ['name' => 'Orange',    'hex' => '#fd7e14', 'label' => 'برتقالي (Orange)'],
    ['name' => 'Pink',      'hex' => '#d63384', 'label' => 'وردي (Pink)'],
    ['name' => 'Indigo',    'hex' => '#6610f2', 'label' => 'نيلي (Indigo)'],
    // Gray scale
    ['name' => 'Gray 500',  'hex' => '#a1a5b7', 'label' => 'رمادي فاتح'],
    ['name' => 'Gray 700',  'hex' => '#5e6278', 'label' => 'رمادي متوسط'],
    ['name' => 'White',     'hex' => '#ffffff', 'label' => 'أبيض (White)'],
];

$defaultColor   = '#009ef7';
// Per-user sidebar preferences (fall back to global settings)
$authUser = auth()->guard('admin')->user();
$currentLayout      = $authUser->sidebar_layout       ?? ($siteSettings->sidebar_layout       ?? 'dark-sidebar');
$currentBgColor     = $authUser->sidebar_bg_color     ?? ($siteSettings->sidebar_bg_color     ?? '');
$currentTextColor   = $authUser->sidebar_text_color   ?? ($siteSettings->sidebar_text_color   ?? '');
$currentActiveColor = $authUser->sidebar_active_color ?? ($siteSettings->sidebar_active_color ?? '');

// Metronic sidebar text color presets
// dark-sidebar default: #C5C5D8 (title), #9D9DA6 (link), active: white
$textColorPresets = [
    ''        => ['label' => 'افتراضي',       'bg' => null],
    '#ffffff' => ['label' => 'أبيض',          'bg' => '#ffffff'],
    '#C5C5D8' => ['label' => 'فاتح (افتراضي داكن)', 'bg' => '#C5C5D8'],
    '#9D9DA6' => ['label' => 'رمادي متوسط',   'bg' => '#9D9DA6'],
    '#646477' => ['label' => 'رمادي غامق',    'bg' => '#646477'],
    '#5e6278' => ['label' => 'رمادي (فاتح)',  'bg' => '#5e6278'],
    '#009ef7' => ['label' => 'أزرق',          'bg' => '#009ef7'],
    '#50cd89' => ['label' => 'أخضر',          'bg' => '#50cd89'],
    '#ffc700' => ['label' => 'أصفر',          'bg' => '#ffc700'],
    '#f1416c' => ['label' => 'أحمر',          'bg' => '#f1416c'],
    '#7239ea' => ['label' => 'بنفسجي',        'bg' => '#7239ea'],
];

$layoutOptions = [
    'dark-sidebar'  => ['label' => 'سايدبار داكن',  'icon' => 'ki-duotone ki-moon',         'desc' => 'قائمة جانبية بخلفية داكنة'],
    'light-sidebar' => ['label' => 'سايدبار فاتح',  'icon' => 'ki-duotone ki-sun',          'desc' => 'قائمة جانبية بخلفية فاتحة'],
    'dark-header'   => ['label' => 'هيدر داكن',     'icon' => 'ki-duotone ki-abstract-26',  'desc' => 'تنقل في الأعلى بخلفية داكنة'],
    'light-header'  => ['label' => 'هيدر فاتح',     'icon' => 'ki-duotone ki-abstract-26',  'desc' => 'تنقل في الأعلى بخلفية فاتحة'],
];
@endphp

{{-- ── Page header ────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-6">
    <div>
        <h2 class="fw-bold m-0">
            <i class="ki-duotone ki-menu fs-2 text-info me-2">
                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
            </i>
            إدارة القائمة الجانبية
        </h2>
        <p class="text-muted fs-7 mt-1 mb-0">اسحب العناصر لإعادة الترتيب — انقر على دائرة اللون لتغيير لون الأيقونة</p>
    </div>
    <div class="d-flex gap-3">
        <button type="button" id="btn-color-all" class="btn btn-light-warning fw-bold">
            <i class="ki-duotone ki-drop fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
            تغيير لون الكل
        </button>
        <button type="button" id="btn-save-order" class="btn btn-primary fw-bold">
            <i class="ki-duotone ki-check fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
            حفظ الترتيب
        </button>
    </div>
</div>

{{-- Toast notification --}}
<div id="save-toast" class="alert d-none fw-semibold mb-5" role="alert"></div>

{{-- ── Legend ──────────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center gap-4 mb-5 p-4 bg-light rounded">
    <span class="badge badge-light-warning fw-semibold">
        <i class="ki-duotone ki-arrows-loop fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
        اسحب من أيقونة ⠿ لإعادة الترتيب
    </span>
    <span class="badge badge-light-primary fw-semibold">
        <i class="ki-duotone ki-drop fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
        انقر على دائرة اللون لتغيير اللون (يُحفظ فوراً)
    </span>
    <span class="badge badge-light-info fw-semibold">
        <i class="ki-duotone ki-information fs-5 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
        الترتيب يحتاج "حفظ الترتيب"
    </span>
</div>

{{-- ── إعدادات المظهر ──────────────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-6">
    <div class="card-header min-h-50px bg-light-dark border-0 d-flex align-items-center">
        <h5 class="card-title fw-bold m-0 fs-6">
            <i class="ki-duotone ki-brush fs-3 text-warning me-2"><span class="path1"></span><span class="path2"></span></i>
            إعدادات مظهر القائمة الجانبية
        </h5>
        <span id="appearance-saved" class="badge badge-light-success ms-3 d-none">تم الحفظ ✓</span>
    </div>
    <div class="card-body pt-5 pb-6 px-6">

        {{-- Layout selector --}}
        <p class="fw-bold text-gray-700 mb-3">موضع القائمة ونمطها</p>
        <div class="row g-3 mb-7" id="layout-options">
            @foreach($layoutOptions as $value => $opt)
            <div class="col-6 col-md-3">
                <label class="layout-card d-block border rounded-2 p-4 text-center cursor-pointer
                              {{ $currentLayout === $value ? 'border-primary bg-light-primary' : 'border-dashed' }}"
                       data-value="{{ $value }}" style="cursor:pointer; transition: all .2s;">
                    <i class="{{ $opt['icon'] }} fs-2x mb-2 {{ $currentLayout === $value ? 'text-primary' : 'text-gray-500' }}">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                    <div class="fw-bold fs-7 {{ $currentLayout === $value ? 'text-primary' : 'text-gray-700' }}">
                        {{ $opt['label'] }}
                    </div>
                    <div class="text-muted fs-9">{{ $opt['desc'] }}</div>
                    <input type="radio" name="sidebar_layout" value="{{ $value }}" class="d-none layout-radio"
                           {{ $currentLayout === $value ? 'checked' : '' }}>
                </label>
            </div>
            @endforeach
        </div>

        {{-- Sidebar background color --}}
        <div id="sidebar-bg-section" class="{{ in_array($currentLayout, ['dark-header','light-header']) ? 'd-none' : '' }}">
            <p class="fw-bold text-gray-700 mb-3">لون خلفية القائمة الجانبية</p>
            <div class="d-flex align-items-center gap-3 flex-wrap mb-2">
                {{-- Preset bg colors --}}
                @foreach([
                    ''        => ['bg' => null,      'label' => 'افتراضي'],
                    '#1e1e2d' => ['bg' => '#1e1e2d',  'label' => 'داكن'],
                    '#ffffff' => ['bg' => '#ffffff',  'label' => 'أبيض'],
                    '#f5f8fa' => ['bg' => '#f5f8fa',  'label' => 'رمادي فاتح'],
                    '#009ef7' => ['bg' => '#009ef7',  'label' => 'أزرق'],
                    '#1b1b29' => ['bg' => '#1b1b29',  'label' => 'أسود'],
                    '#181c32' => ['bg' => '#181c32',  'label' => 'نيفي'],
                    '#50cd89' => ['bg' => '#50cd89',  'label' => 'أخضر'],
                    '#7239ea' => ['bg' => '#7239ea',  'label' => 'بنفسجي'],
                    '#f1416c' => ['bg' => '#f1416c',  'label' => 'أحمر'],
                ] as $hex => $info)
                <div class="d-flex flex-column align-items-center gap-1">
                    <button type="button"
                            class="bg-preset-btn btn p-0 rounded-2 border-2"
                            data-hex="{{ $hex }}"
                            title="{{ $info['label'] }}"
                            style="width:40px;height:40px;
                                   background: {{ $info['bg'] ?? 'linear-gradient(135deg,#f5f8fa 50%,#1e1e2d 50%)' }};
                                   border: 2px solid {{ $currentBgColor === $hex ? '#009ef7' : 'transparent' }};
                                   {{ !$hex ? 'border:2px dashed #b5b5c3;' : '' }}">
                    </button>
                    <span class="text-muted fs-9">{{ $info['label'] }}</span>
                </div>
                @endforeach

                {{-- Custom color --}}
                <div class="d-flex flex-column align-items-center gap-1">
                    <input type="color" id="bg-custom-input"
                           value="{{ $currentBgColor ?: '#1e1e2d' }}"
                           style="width:40px;height:40px;padding:2px;border-radius:6px;border:2px solid #e4e6ef;cursor:pointer;">
                    <span class="text-muted fs-9">مخصص</span>
                </div>
            </div>
            <p class="text-muted fs-8 mt-1">
                اللون المختار: <code id="bg-color-preview" style="font-family:monospace;">{{ $currentBgColor ?: 'افتراضي' }}</code>
            </p>
        </div>

        {{-- Sidebar text color --}}
        <div id="sidebar-text-section" class="{{ in_array($currentLayout, ['dark-header','light-header']) ? 'd-none' : '' }} mt-6">
            <p class="fw-bold text-gray-700 mb-1">لون نص القائمة الجانبية</p>
            <p class="text-muted fs-8 mb-3">يتحكم في لون عناوين وروابط القائمة الجانبية</p>
            <div class="d-flex align-items-center gap-3 flex-wrap mb-2">
                @foreach($textColorPresets as $hex => $info)
                <div class="d-flex flex-column align-items-center gap-1">
                    <button type="button"
                            class="text-preset-btn btn p-0 rounded-2 border-2"
                            data-hex="{{ $hex }}"
                            title="{{ $info['label'] }}"
                            style="width:40px;height:40px;
                                   background: {{ $info['bg'] ? $info['bg'] : 'linear-gradient(135deg,#f5f8fa 50%,#1e1e2d 50%)' }};
                                   border: 2px solid {{ $currentTextColor === $hex ? '#009ef7' : ($hex ? 'transparent' : '#b5b5c3') }};
                                   {{ !$hex ? 'border-style:dashed;' : '' }}">
                    </button>
                    <span class="text-muted fs-9 text-center" style="max-width:52px;line-height:1.2;">{{ $info['label'] }}</span>
                </div>
                @endforeach

                {{-- Custom text color --}}
                <div class="d-flex flex-column align-items-center gap-1">
                    <input type="color" id="text-custom-input"
                           value="{{ $currentTextColor ?: '#C5C5D8' }}"
                           style="width:40px;height:40px;padding:2px;border-radius:6px;border:2px solid #e4e6ef;cursor:pointer;">
                    <span class="text-muted fs-9">مخصص</span>
                </div>
            </div>
            <p class="text-muted fs-8 mt-1">
                اللون المختار: <code id="text-color-preview" style="font-family:monospace;">{{ $currentTextColor ?: 'افتراضي' }}</code>
            </p>
        </div>

        {{-- Active menu item color --}}
        <div id="sidebar-active-section" class="{{ in_array($currentLayout, ['dark-header','light-header']) ? 'd-none' : '' }} mt-6">
            <p class="fw-bold text-gray-700 mb-1">لون العنصر النشط (الصفحة الحالية)</p>
            <p class="text-muted fs-8 mb-3">يتحكم في لون العنصر المحدد في القائمة الجانبية</p>
            <div class="d-flex align-items-center gap-3 flex-wrap mb-2">
                @php
                $activeColorPresets = [
                    ''        => ['label' => 'افتراضي',  'bg' => null],
                    '#009ef7' => ['label' => 'أزرق',      'bg' => '#009ef7'],
                    '#50cd89' => ['label' => 'أخضر',      'bg' => '#50cd89'],
                    '#ffc700' => ['label' => 'أصفر',      'bg' => '#ffc700'],
                    '#f1416c' => ['label' => 'أحمر',      'bg' => '#f1416c'],
                    '#7239ea' => ['label' => 'بنفسجي',    'bg' => '#7239ea'],
                    '#ffffff' => ['label' => 'أبيض',      'bg' => '#ffffff'],
                    '#f5f8fa' => ['label' => 'فاتح جداً', 'bg' => '#f5f8fa'],
                ];
                @endphp
                @foreach($activeColorPresets as $hex => $info)
                <div class="d-flex flex-column align-items-center gap-1">
                    <button type="button"
                            class="active-preset-btn btn p-0 rounded-2 border-2"
                            data-hex="{{ $hex }}"
                            style="width:32px;height:32px;
                                   background: {{ $info['bg'] ?? 'linear-gradient(135deg,#f5f8fa 50%,#1e1e2d 50%)' }};
                                   border: 2px solid {{ $currentActiveColor === $hex ? '#009ef7' : ($hex ? 'transparent' : '#b5b5c3') }};
                                   {{ !$hex ? 'border-style:dashed;' : '' }}"
                            title="{{ $info['label'] }}"></button>
                    <span class="text-muted fs-9">{{ $info['label'] }}</span>
                </div>
                @endforeach
                {{-- Custom active color --}}
                <div class="d-flex flex-column align-items-center gap-1">
                    <input type="color" id="active-custom-input"
                           value="{{ $currentActiveColor ?: '#009ef7' }}"
                           style="width:40px;height:40px;padding:2px;border-radius:6px;border:2px solid #e4e6ef;cursor:pointer;">
                    <span class="text-muted fs-9">مخصص</span>
                </div>
            </div>
            <p class="text-muted fs-8 mt-1">
                اللون المختار: <code id="active-color-preview" style="font-family:monospace;">{{ $currentActiveColor ?: 'افتراضي' }}</code>
            </p>
        </div>

    </div>
</div>

{{-- ── الرئيسية: ثابتة الترتيب، قابلة لتغيير اللون ───────────────────────── --}}
@php
    $dashGroup = \App\Models\PermissionsGroup::where('name','dashboard')->first();
    $dashColor = $dashGroup->color ?? '#009ef7';
@endphp
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-0 px-0">
        <div class="d-flex align-items-center gap-4 px-5 py-4 parent-drag-zone"
             style="border-right: 4px solid {{ $dashColor }}; border-radius: 8px;">
            {{-- Drag handle disabled --}}
            <span class="text-gray-300" style="font-size:18px; user-select:none; cursor:not-allowed;" title="هذا العنصر ثابت دائماً في الأعلى">⠿</span>

            {{-- Color trigger --}}
            <button type="button"
                    class="color-trigger btn p-0 rounded-circle border-0 shadow-sm"
                    data-id="{{ $dashGroup->id }}"
                    data-current="{{ $dashColor }}"
                    style="width:28px;height:28px;background:{{ $dashColor }};flex-shrink:0;"
                    title="انقر لتغيير اللون">
            </button>

            <span class="menu-icon">
                <i class="ki-duotone ki-home fs-1 item-icon-{{ $dashGroup->id }}" style="color:{{ $dashColor }}">
                    <span class="path1"></span><span class="path2"></span>
                    <span class="path3"></span><span class="path4"></span>
                </i>
            </span>
            <div class="flex-grow-1">
                <span class="fw-bold text-gray-800 fs-6">الرئيسية</span>
                <code class="text-muted fs-8 ms-2 bg-light px-2 py-1 rounded">dashboard</code>
            </div>
            <span class="badge badge-light-secondary fs-8">
                <i class="ki-duotone ki-lock-2 fs-6 me-1"><span class="path1"></span><span class="path2"></span></i>
                ثابت — يظهر دائماً
            </span>
        </div>
    </div>
</div>

{{-- ── Sortable list ────────────────────────────────────────────────────── --}}
<div id="sortable-parents" class="d-flex flex-column gap-3">
    @foreach($groups as $group)
    @php $gc = $group->color ?: $defaultColor; @endphp
    <div class="card shadow-sm border-0 parent-card" data-id="{{ $group->id }}">

        {{-- Parent row --}}
        <div class="card-body py-0 px-0">
            <div class="d-flex align-items-center gap-4 px-5 py-4 parent-drag-zone"
                 style="border-right: 4px solid {{ $gc }}; border-radius: 8px 8px 0 0;">

                {{-- Drag handle --}}
                <span class="drag-handle text-gray-400" style="cursor:grab; font-size:18px; user-select:none;" title="اسحب لإعادة الترتيب">⠿</span>

                {{-- Color dot (click to open picker) --}}
                <button type="button"
                        class="color-trigger btn p-0 rounded-circle border-0 shadow-sm"
                        data-id="{{ $group->id }}"
                        data-current="{{ $gc }}"
                        style="width:28px;height:28px;background:{{ $gc }};flex-shrink:0;"
                        title="انقر لتغيير اللون">
                </button>

                {{-- Icon + Name --}}
                <span class="menu-icon">
                    <i class="{{ $group->icon ?? 'ki-duotone ki-element-plus' }} fs-1 item-icon-{{ $group->id }}"
                       style="color:{{ $gc }}">
                        <span class="path1"></span><span class="path2"></span>
                        <span class="path3"></span><span class="path4"></span>
                        <span class="path5"></span><span class="path6"></span>
                    </i>
                </span>
                <div class="flex-grow-1">
                    <span class="fw-bold text-gray-800 fs-6">{{ $group->name_ar ?? $group->name }}</span>
                    <code class="text-muted fs-8 ms-2 bg-light px-2 py-1 rounded">{{ $group->name }}</code>
                </div>

                @if($group->mychild->count())
                    <span class="badge badge-light-primary fs-8">{{ $group->mychild->count() }} عنصر فرعي</span>
                @endif
            </div>

            {{-- Children --}}
            @if($group->mychild->count() > 0)
            <ul class="sortable-children list-unstyled mb-0 px-6 pb-3 pt-1"
                data-parent="{{ $group->id }}">
                @foreach($group->mychild as $child)
                @php $cc = $child->color ?: $gc; @endphp
                <li class="d-flex align-items-center gap-3 py-2 ps-4 border-bottom border-dashed child-item"
                    data-id="{{ $child->id }}"
                    style="border-right: 3px solid {{ $cc }}; border-radius:4px; margin-bottom:2px;">

                    <span class="drag-handle child-handle text-gray-400" style="cursor:grab;font-size:14px;user-select:none;">⠿</span>

                    {{-- Color dot --}}
                    <button type="button"
                            class="color-trigger btn p-0 rounded-circle border-0"
                            data-id="{{ $child->id }}"
                            data-current="{{ $cc }}"
                            style="width:20px;height:20px;background:{{ $cc }};flex-shrink:0;"
                            title="انقر لتغيير اللون">
                    </button>

                    {{-- Sidebar bullet (same as real sidebar) --}}
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot" style="background:{{ $cc }};"></span>
                    </span>

                    {{-- Child icon (if set) --}}
                    @if($child->icon)
                    <i class="{{ $child->icon }} fs-3 item-icon-{{ $child->id }}"
                       style="color:{{ $cc }}">
                        <span class="path1"></span><span class="path2"></span>
                        <span class="path3"></span><span class="path4"></span>
                        <span class="path5"></span><span class="path6"></span>
                    </i>
                    @endif

                    <span class="fw-semibold text-gray-700 fs-7">{{ $child->name_ar ?? $child->name }}</span>
                    <code class="text-muted fs-8 bg-light px-2 py-1 rounded ms-auto">{{ $child->name }}</code>
                </li>
                @endforeach
            </ul>
            @endif

        </div>
    </div>
    @endforeach
</div>

{{-- Floating save bar (appears after dragging) --}}
<div id="floating-save" class="position-fixed bottom-0 start-0 end-0 d-none" style="z-index:1050;">
    <div class="bg-primary text-white d-flex align-items-center justify-content-between px-8 py-4 shadow-lg">
        <span class="fw-semibold fs-6">
            <i class="ki-duotone ki-information-5 fs-4 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
            لديك تغييرات في الترتيب غير محفوظة
        </span>
        <button type="button" id="btn-save-float" class="btn btn-light btn-sm fw-bold px-6">
            حفظ الترتيب الآن
        </button>
    </div>
</div>

{{-- ── Color Picker Modal ────────────────────────────────────────────────── --}}
<div class="modal fade" id="colorPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header py-4 px-6">
                <h5 class="modal-title fw-bold fs-6 m-0" id="colorPickerModalTitle">اختر لون الأيقونة</h5>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-6 pb-6 pt-4">

                {{-- Metronic color swatches --}}
                <p class="text-muted fs-8 mb-3 fw-semibold">ألوان Metronic</p>
                <div class="d-flex flex-wrap gap-2 mb-5" id="color-swatches-container">
                    @foreach($metronicColors as $mc)
                    <div class="color-swatch-item d-flex flex-column align-items-center gap-1"
                         style="width:52px;">
                        <button type="button"
                                class="metronic-swatch btn p-0 rounded border-2"
                                data-hex="{{ $mc['hex'] }}"
                                title="{{ $mc['label'] }}"
                                style="width:40px;height:40px;background:{{ $mc['hex'] }};
                                       border: 2px solid transparent;">
                        </button>
                        <span class="text-muted fs-9 text-center" style="font-size:9px;line-height:1.2;">
                            {{ $mc['name'] }}
                        </span>
                    </div>
                    @endforeach
                </div>

                {{-- Custom color --}}
                <p class="text-muted fs-8 mb-2 fw-semibold">لون مخصص</p>
                <div class="d-flex align-items-center gap-3">
                    <input type="color" id="custom-color-input" class="form-control form-control-sm p-1"
                           style="width:48px;height:38px;cursor:pointer;" value="#009ef7">
                    <input type="text" id="custom-color-hex" class="form-control form-control-sm"
                           placeholder="#009ef7" maxlength="7" style="font-family:monospace;">
                    <button type="button" id="btn-apply-custom" class="btn btn-sm btn-light-primary fw-bold">تطبيق</button>
                </div>

                {{-- Preview --}}
                <div class="mt-5 d-flex align-items-center gap-3 p-3 bg-light rounded">
                    <span class="text-muted fs-8">معاينة:</span>
                    <div id="color-preview-dot" class="rounded-circle"
                         style="width:32px;height:32px;background:#009ef7;"></div>
                    <span id="color-preview-hex" class="fw-bold fs-7" style="font-family:monospace;">#009ef7</span>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const appearanceUrl = "{{ route('sidebar_manager.appearance') }}";
    const reorderUrl = "{{ route('sidebar_manager.reorder') }}";
    const colorUrl   = "{{ route('sidebar_manager.color') }}";
    const partialUrl = "{{ route('sidebar_manager.partial') }}";
    const csrf       = document.querySelector('meta[name="csrf-token"]')?.content ?? "{{ csrf_token() }}";

    let hasChanges     = false;
    let activeItemId   = null;   // int id or 'all'
    let pickedColor    = null;

    // ── Appearance: layout + bg color ────────────────────────────────────────
    let currentLayout      = "{{ $currentLayout }}";
    let currentBgColor     = "{{ $currentBgColor }}";
    let currentTextColor   = "{{ $currentTextColor }}";
    let currentActiveColor = "{{ $currentActiveColor }}";

    // Apply saved colors immediately on page load
    (function() {
        if (currentBgColor)     applyBgColorLive(currentBgColor);
        if (currentTextColor)   applyTextColorLive(currentTextColor);
        if (currentActiveColor) applyActiveColorLive(currentActiveColor);
    })();

    function applyBgColorLive(bgColor) {
        let el = document.getElementById('sidebar-bg-color-style');
        if (!el) {
            el = document.createElement('style');
            el.id = 'sidebar-bg-color-style';
            document.head.appendChild(el);
        }
        el.textContent = bgColor
            ? `#kt_app_sidebar { background-color: ${bgColor} !important; }`
            : '';
    }

    function applyTextColorLive(textColor) {
        let el = document.getElementById('sidebar-text-color-style');
        if (!el) {
            el = document.createElement('style');
            el.id = 'sidebar-text-color-style';
            document.head.appendChild(el);
        }
        if (textColor) {
            el.textContent = `
                .app-sidebar .menu-title,
                .app-sidebar .menu-link .menu-title,
                .app-sidebar .menu-section .menu-content { color: ${textColor} !important; }
                .app-sidebar .menu-link:hover .menu-title,
                .app-sidebar .menu-link.active .menu-title { color: ${textColor} !important; opacity: .85; }
                .app-sidebar .menu-arrow:after { border-color: ${textColor} !important; }
            `;
        } else {
            el.textContent = '';
        }
    }

    function applyActiveColorLive(activeColor) {
        let el = document.getElementById('sidebar-active-color-style');
        if (!el) {
            el = document.createElement('style');
            el.id = 'sidebar-active-color-style';
            document.head.appendChild(el);
        }
        if (activeColor) {
            el.textContent = `
                .app-sidebar .menu-item.show > .menu-link .menu-title,
                .app-sidebar .menu-item .menu-link.active .menu-title { color: ${activeColor} !important; opacity:1; }
                .app-sidebar .menu-item.show > .menu-link .menu-icon i,
                .app-sidebar .menu-item.show > .menu-link .menu-icon svg path,
                .app-sidebar .menu-item .menu-link.active .menu-icon i,
                .app-sidebar .menu-item .menu-link.active .menu-icon svg path { color: ${activeColor} !important; fill: ${activeColor} !important; }
                .app-sidebar .menu-item .menu-link.active { background-color: ${activeColor}22 !important; }
            `;
        } else {
            el.textContent = '';
        }
    }

    function saveAppearance(layout, bgColor, textColor, activeColor) {
        fetch(appearanceUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({
                sidebar_layout:       layout,
                sidebar_bg_color:     bgColor,
                sidebar_text_color:   textColor,
                sidebar_active_color: activeColor,
            }),
        })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'success') {
                document.getElementById('kt_app_body').setAttribute('data-kt-app-layout', layout);

                applyBgColorLive(bgColor);
                applyTextColorLive(textColor);
                applyActiveColorLive(activeColor);

                const badge = document.getElementById('appearance-saved');
                badge.classList.remove('d-none');
                setTimeout(() => badge.classList.add('d-none'), 2500);

                toast(d.message, 'success');
            } else {
                toast(d.message, 'error');
            }
        })
        .catch(() => toast('حدث خطأ في الاتصال', 'error'));
    }

    // Layout card click
    document.querySelectorAll('.layout-card').forEach(card => {
        card.addEventListener('click', function () {
            const val = this.dataset.value;
            currentLayout = val;

            // Update UI
            document.querySelectorAll('.layout-card').forEach(c => {
                const isActive = c.dataset.value === val;
                c.classList.toggle('border-primary', isActive);
                c.classList.toggle('bg-light-primary', isActive);
                c.classList.toggle('border-dashed', !isActive);
                c.querySelector('i').classList.toggle('text-primary', isActive);
                c.querySelector('i').classList.toggle('text-gray-500', !isActive);
                c.querySelector('.fw-bold').classList.toggle('text-primary', isActive);
                c.querySelector('.fw-bold').classList.toggle('text-gray-700', !isActive);
            });
            this.querySelector('input').checked = true;

            // Show/hide bg + text color sections
            const isHeader = ['dark-header', 'light-header'].includes(val);
            document.getElementById('sidebar-bg-section').classList.toggle('d-none', isHeader);
            document.getElementById('sidebar-text-section').classList.toggle('d-none', isHeader);
            document.getElementById('sidebar-active-section').classList.toggle('d-none', isHeader);

            saveAppearance(currentLayout, currentBgColor, currentTextColor, currentActiveColor);
        });
    });

    // Preset bg color click
    document.querySelectorAll('.bg-preset-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            currentBgColor = this.dataset.hex;
            document.getElementById('bg-color-preview').textContent = currentBgColor || 'افتراضي';
            document.getElementById('bg-custom-input').value = currentBgColor || '#1e1e2d';

            document.querySelectorAll('.bg-preset-btn').forEach(b => {
                b.style.borderColor = b.dataset.hex === currentBgColor ? '#009ef7' : 'transparent';
                if (!b.dataset.hex) b.style.border = b.dataset.hex === currentBgColor ? '2px solid #009ef7' : '2px dashed #b5b5c3';
            });

            saveAppearance(currentLayout, currentBgColor, currentTextColor, currentActiveColor);
        });
    });

    // Custom bg color input
    document.getElementById('bg-custom-input').addEventListener('change', function () {
        currentBgColor = this.value;
        document.getElementById('bg-color-preview').textContent = currentBgColor;
        document.querySelectorAll('.bg-preset-btn').forEach(b => b.style.borderColor = 'transparent');
        saveAppearance(currentLayout, currentBgColor, currentTextColor, currentActiveColor);
    });

    // ── Text color presets ────────────────────────────────────────────────────
    document.querySelectorAll('.text-preset-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            currentTextColor = this.dataset.hex;
            document.getElementById('text-color-preview').textContent = currentTextColor || 'افتراضي';
            document.getElementById('text-custom-input').value = currentTextColor || '#C5C5D8';

            document.querySelectorAll('.text-preset-btn').forEach(b => {
                const isActive = b.dataset.hex === currentTextColor;
                b.style.border = isActive ? '2px solid #009ef7' : (b.dataset.hex ? '2px solid transparent' : '2px dashed #b5b5c3');
            });

            saveAppearance(currentLayout, currentBgColor, currentTextColor, currentActiveColor);
        });
    });

    // Custom text color input
    document.getElementById('text-custom-input').addEventListener('change', function () {
        currentTextColor = this.value;
        document.getElementById('text-color-preview').textContent = currentTextColor;
        document.querySelectorAll('.text-preset-btn').forEach(b => {
            b.style.border = '2px solid transparent';
            if (!b.dataset.hex) b.style.border = '2px dashed #b5b5c3';
        });
        saveAppearance(currentLayout, currentBgColor, currentTextColor, currentActiveColor);
    });

    // ── Active color presets ─────────────────────────────────────────────────
    document.querySelectorAll('.active-preset-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            currentActiveColor = this.dataset.hex;
            document.getElementById('active-color-preview').textContent = currentActiveColor || 'افتراضي';
            document.getElementById('active-custom-input').value = currentActiveColor || '#009ef7';

            document.querySelectorAll('.active-preset-btn').forEach(b => {
                const isActive = b.dataset.hex === currentActiveColor;
                b.style.border = isActive ? '2px solid #009ef7' : (b.dataset.hex ? '2px solid transparent' : '2px dashed #b5b5c3');
            });

            saveAppearance(currentLayout, currentBgColor, currentTextColor, currentActiveColor);
        });
    });

    document.getElementById('active-custom-input').addEventListener('change', function () {
        currentActiveColor = this.value;
        document.getElementById('active-color-preview').textContent = currentActiveColor;
        document.querySelectorAll('.active-preset-btn').forEach(b => {
            b.style.border = '2px solid transparent';
            if (!b.dataset.hex) b.style.border = '2px dashed #b5b5c3';
        });
        saveAppearance(currentLayout, currentBgColor, currentTextColor, currentActiveColor);
    });

    // ── Live sidebar reload ──────────────────────────────────────────────────
    function reloadSidebar() {
        fetch(partialUrl)
            .then(r => r.text())
            .then(html => {
                const menu = document.getElementById('kt_app_sidebar_menu');
                if (menu) menu.innerHTML = html;
            })
            .catch(() => {}); // silent — sidebar still works on next page load
    }

    // ── Toast ───────────────────────────────────────────────────────────────
    function toast(msg, type) {
        const el = document.getElementById('save-toast');
        el.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' fw-semibold';
        el.textContent = msg;
        el.classList.remove('d-none');
        clearTimeout(el._t);
        el._t = setTimeout(() => el.classList.add('d-none'), 3000);
    }

    // ── Collect order ────────────────────────────────────────────────────────
    function collectOrder() {
        const items = [];
        document.querySelectorAll('#sortable-parents .parent-card').forEach(card => {
            const childIds = [...card.querySelectorAll('.child-item')].map(li => li.dataset.id);
            items.push({ id: card.dataset.id, children: childIds });
        });
        return items;
    }

    // ── Save order ────────────────────────────────────────────────────────────
    function saveOrder() {
        fetch(reorderUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ items: collectOrder() }),
        })
        .then(r => r.json())
        .then(d => {
            toast(d.message, d.status);
            if (d.status === 'success') {
                hasChanges = false;
                document.getElementById('floating-save').classList.add('d-none');
                reloadSidebar();
            }
        })
        .catch(() => toast('حدث خطأ في الاتصال', 'error'));
    }

    document.getElementById('btn-save-order').addEventListener('click', saveOrder);
    document.getElementById('btn-save-float').addEventListener('click', saveOrder);

    // ── Sortable parents ─────────────────────────────────────────────────────
    Sortable.create(document.getElementById('sortable-parents'), {
        handle: '.drag-handle:not(.child-handle)',
        animation: 180,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd() {
            hasChanges = true;
            document.getElementById('floating-save').classList.remove('d-none');
        },
    });

    // ── Sortable children ─────────────────────────────────────────────────────
    document.querySelectorAll('.sortable-children').forEach(ul => {
        Sortable.create(ul, {
            handle: '.child-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd() {
                hasChanges = true;
                document.getElementById('floating-save').classList.remove('d-none');
            },
        });
    });

    // ── Open modal: single item ───────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.color-trigger');
        if (!btn) return;

        activeItemId = btn.dataset.id;
        pickedColor  = btn.dataset.current || '#009ef7';

        document.getElementById('colorPickerModalTitle').textContent = 'اختر لون الأيقونة';
        updatePreview(pickedColor);
        syncHexInput(pickedColor);
        markActiveSwatch(pickedColor);

        new bootstrap.Modal(document.getElementById('colorPickerModal')).show();
    });

    // ── Open modal: all items ─────────────────────────────────────────────────
    document.getElementById('btn-color-all').addEventListener('click', function () {
        activeItemId = 'all';
        pickedColor  = '#009ef7';

        document.getElementById('colorPickerModalTitle').innerHTML =
            '<span class="badge badge-light-warning me-2">الكل</span> تغيير لون جميع العناصر';
        updatePreview(pickedColor);
        syncHexInput(pickedColor);
        markActiveSwatch(null);

        new bootstrap.Modal(document.getElementById('colorPickerModal')).show();
    });

    // ── Metronic swatch click ─────────────────────────────────────────────────
    document.getElementById('color-swatches-container').addEventListener('click', function (e) {
        const btn = e.target.closest('.metronic-swatch');
        if (!btn) return;
        pickedColor = btn.dataset.hex;
        updatePreview(pickedColor);
        syncHexInput(pickedColor);
        markActiveSwatch(pickedColor);
        applyColor(activeItemId, pickedColor);
    });

    // ── Custom color picker (native) ──────────────────────────────────────────
    document.getElementById('custom-color-input').addEventListener('input', function () {
        pickedColor = this.value;
        updatePreview(pickedColor);
        syncHexInput(pickedColor);
        markActiveSwatch(null); // deselect swatches
    });

    // ── Hex text input sync ───────────────────────────────────────────────────
    document.getElementById('custom-color-hex').addEventListener('input', function () {
        let val = this.value.trim();
        if (!val.startsWith('#')) val = '#' + val;
        if (/^#[0-9a-fA-F]{6}$/.test(val)) {
            pickedColor = val;
            document.getElementById('custom-color-input').value = val;
            updatePreview(val);
            markActiveSwatch(null);
        }
    });

    // ── Apply custom button ───────────────────────────────────────────────────
    document.getElementById('btn-apply-custom').addEventListener('click', function () {
        if (activeItemId && pickedColor) {
            applyColor(activeItemId, pickedColor);
        }
    });

    // ── Helpers ───────────────────────────────────────────────────────────────
    function updatePreview(color) {
        document.getElementById('color-preview-dot').style.background = color;
        document.getElementById('color-preview-hex').textContent = color;
    }

    function syncHexInput(color) {
        document.getElementById('custom-color-hex').value  = color;
        document.getElementById('custom-color-input').value = color;
    }

    function markActiveSwatch(color) {
        document.querySelectorAll('.metronic-swatch').forEach(s => {
            const isActive = color && s.dataset.hex.toLowerCase() === color.toLowerCase();
            s.style.border      = isActive ? '2px solid #181c32' : '2px solid transparent';
            s.style.transform   = isActive ? 'scale(1.2)' : 'scale(1)';
            s.style.boxShadow   = isActive ? '0 0 0 3px rgba(0,0,0,0.15)' : '';
        });
    }

    // ── Apply color to a single element in the DOM ───────────────────────────
    function applyColorDOM(id, color) {
        document.querySelectorAll(`.color-trigger[data-id="${id}"]`).forEach(btn => {
            btn.style.background = color;
            btn.dataset.current  = color;
        });
        const card = document.querySelector(`.parent-card[data-id="${id}"]`);
        if (card) card.querySelector('.parent-drag-zone').style.borderRightColor = color;

        const childRow = document.querySelector(`.child-item[data-id="${id}"]`);
        if (childRow) {
            childRow.style.borderRightColor = color;
            const bullet = childRow.querySelector('.bullet-dot');
            if (bullet) bullet.style.background = color;
        }
        const icon = document.querySelector(`.item-icon-${id}`);
        if (icon) icon.style.color = color;
    }

    // ── Apply color: update DOM + save to server ──────────────────────────────
    function applyColor(id, color) {
        if (id === 'all') {
            // Update every element in the DOM
            document.querySelectorAll('.color-trigger').forEach(btn => {
                btn.style.background = color;
                btn.dataset.current  = color;
            });
            document.querySelectorAll('.parent-drag-zone').forEach(el => {
                el.style.borderRightColor = color;
            });
            document.querySelectorAll('.child-item').forEach(el => {
                el.style.borderRightColor = color;
                const bullet = el.querySelector('.bullet-dot');
                if (bullet) bullet.style.background = color;
            });
            document.querySelectorAll('[class*="item-icon-"]').forEach(el => {
                el.style.color = color;
            });
        } else {
            applyColorDOM(id, color);
        }

        // Save to server
        fetch(colorUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ id, color }),
        })
        .then(r => r.json())
        .then(d => {
            toast(d.message, d.status);
            if (d.status === 'success') reloadSidebar();
        })
        .catch(() => toast('حدث خطأ في الاتصال', 'error'));

        bootstrap.Modal.getInstance(document.getElementById('colorPickerModal'))?.hide();
    }

    // ── Warn on unsaved order changes ─────────────────────────────────────────
    window.addEventListener('beforeunload', e => {
        if (hasChanges) { e.preventDefault(); e.returnValue = ''; }
    });
})();
</script>

<style>
.sortable-ghost  { opacity: 0.35; border-radius: 8px; background: #f1f3f9 !important; }
.sortable-chosen { box-shadow: 0 6px 24px rgba(0,0,0,0.12) !important; }
.drag-handle:hover { color: #009ef7 !important; }
.metronic-swatch { transition: transform .15s, box-shadow .15s; }
.metronic-swatch:hover { transform: scale(1.18); box-shadow: 0 2px 8px rgba(0,0,0,0.18); }
.color-trigger { transition: transform .15s, box-shadow .15s; }
.color-trigger:hover { transform: scale(1.25); box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
.parent-drag-zone { transition: border-color .2s; }
</style>
@endsection
