@php $waNumber = preg_replace('/\D/', '', $mobile ?? ''); @endphp
<div class="d-flex justify-content-center gap-2">
    @if(!empty($waNumber))
    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" class="btn btn-icon btn-light-success btn-sm" title="رد عبر واتساب">
        <i class="bi bi-whatsapp fs-4"></i>
    </a>
    @endif
    @can('admin.contact.reply')
    <a href="{{ route('contacts.reply',[ 'id' => Crypt::encrypt($id)]) }}" class="btn btn-icon btn-light-primary btn-sm" title="رد عبر البريد الإلكتروني">
        <i class="bi bi-envelope-paper fs-4"></i>
    </a>
    @endcan
    @if(!empty($email))
    <a href="mailto:{{ $email }}" class="btn btn-icon btn-light-info btn-sm" title="فتح بريد إلكتروني مباشر">
        <i class="bi bi-envelope fs-4"></i>
    </a>
    @endif
    @can('admin.contact.delete')
    <a href="javascript:;" data-href="{{ Crypt::encrypt($id) }}" class="btn btn-icon btn-light-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirm" title="حذف">
        <i class="bi bi-trash fs-4"></i>
    </a>
    @endcan
</div>
