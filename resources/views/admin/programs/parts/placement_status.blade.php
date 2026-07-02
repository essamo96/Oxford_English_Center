<div class="form-check form-switch form-check-custom form-check-solid justify-content-center">
    <input class="form-check-input h-20px w-30px @can('admin.programs.status') placement-status @endcan" type="checkbox" 
           data-href="{{ Crypt::encrypt($id) }}" 
           {{ $is_placement_test == 1 ? 'checked' : '' }} 
           @cannot('admin.programs.status') disabled @endcannot />
</div>
