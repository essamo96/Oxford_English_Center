<div class="form-check form-switch form-check-custom form-check-solid justify-content-center">
    <input class="form-check-input h-20px w-30px @can('admin.evaluate_items.status') status @endcan" type="checkbox" 
           data-href="{{ Crypt::encrypt($id) }}" 
           {{ $status == 1 ? 'checked' : '' }} 
           @cannot('admin.evaluate_items.status') disabled @endcannot />
</div>
