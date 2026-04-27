@props([
    'id' => 'file',
    'name' => 'file',
    'value' => '',
    'label' => 'اختر ملف',
    'type' => 'file', // 'image' or 'document'
    'placeholder' => 'لم يتم اختيار ملف..'
])

<div class="input-group metronic-file-picker">
    <input id="{{ $id }}" value="{{ old($name) ?? $value }}" class="form-control form-control-solid" type="text"
        name="{{ $name }}" readonly placeholder="{{ $placeholder }}" data-preview="{{ $id }}_holder">
    <button onclick="openMetronicFileManager('{{ $type }}', '{{ $id }}')" class="btn btn-primary" type="button">
        @if($type == 'image')
            <i class="ki-duotone ki-picture fs-2"><span class="path1"></span><span class="path2"></span></i> {{ $label }}
        @else
            <i class="ki-duotone ki-file fs-2"><span class="path1"></span><span class="path2"></span></i> {{ $label }}
        @endif
    </button>
</div>
@if($type == 'image')
<div id="{{ $id }}_holder" style="margin-top:15px;max-height:100px;{{ empty(old($name) ?? $value) ? 'display:none;' : '' }}">
    @php $cval = old($name) ?? $value; @endphp
    <img src="{{ $cval ? asset($cval) : '' }}" style="max-height: 100px; border-radius: 5px;">
</div>
@endif


