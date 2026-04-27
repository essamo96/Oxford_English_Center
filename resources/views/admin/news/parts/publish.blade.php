<div class="form-check form-switch form-check-custom form-check-solid justify-content-center">
    <input class="form-check-input h-20px w-30px @can('admin.news.publish') publish @endcan" type="checkbox" 
           data-href="{{ Crypt::encrypt($id) }}" 
           {{ $publish == 1 ? 'checked' : '' }} 
           @cannot('admin.news.publish') disabled @endcannot />
</div>