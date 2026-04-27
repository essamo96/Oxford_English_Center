<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Files;

class FilesController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    ///////////////////////////
    public function getIndex() {
        
    }

    ///////////////////////////////////////
    public function getFiles() {
        $video = new Files();
        $info = $video->getFiles();
        if (!$info) {
            return response()->view('errors.404', parent::$data, 500);
        }
        parent::$data['files'] = $info;
        //////////////////////////////////////////////
        return view('frontend.files.view', parent::$data);
    }

}
