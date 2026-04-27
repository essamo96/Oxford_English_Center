<div class="form-check form-switch form-check-custom form-check-solid justify-content-center">
    <input class="form-check-input h-20px w-30px @can('admin.questions.status') status @endcan" type="checkbox" 
           data-href="{{ Crypt::encrypt($id) }}" 
           {{ $status == 1 ? 'checked' : '' }} 
           @cannot('admin.questions.status') disabled @endcannot />
</div>
