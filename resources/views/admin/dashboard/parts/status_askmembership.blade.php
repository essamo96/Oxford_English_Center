<div class="form-check form-switch form-check-custom form-check-solid justify-content-center">
    <input class="form-check-input h-20px w-30px status" type="checkbox" 
           data-href="{{ Crypt::encrypt($id) }}" 
           {{ $status == 1 ? 'checked' : '' }} />
</div>