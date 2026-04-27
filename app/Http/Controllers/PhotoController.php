<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Photos;
use App\Models\Images;

class PhotoController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    ///////////////////////////
    public function getIndex($id) {
        $photos = new Images();
        parent::$data['pics'] = $photos->getAllImages($id);
        return view('frontend.photo.client', parent::$data);
    }

///////////////////////////////////////
    public function getPics() {
        $news = new Photos();
        parent::$data['pics'] = $news->getPhotos(0, 30);
        return view('frontend.photo.view', parent::$data);
    }

}
