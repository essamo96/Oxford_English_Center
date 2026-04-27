<div class="form-check form-switch form-check-custom form-check-solid justify-content-center">
    <input class="form-check-input h-20px w-30px @can('admin.roles.status') status @endcan" type="checkbox" 
           data-href="{{ Crypt::encrypt($id) }}" 
           {{ $status == 1 ? 'checked' : '' }} 
           @cannot('admin.roles.status') disabled @endcannot />
</div>