<a href="{{ route('photos.images',[ 'id' => Crypt::encrypt($id)]) }}" class="btn btn-sm  @can('admin.photos.status') album @endcan">
    <i class="fa fa-image"></i>  <?= $album ?> صورة
</a>
