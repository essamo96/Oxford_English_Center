<a href="{{ route('photos.images',[ 'id' => Crypt::encrypt($id)]) }}" class="btn btn-sm btn-light-info album">
    <i class="bi bi-images me-1"></i> {{ $album }} صورة
</a>
