<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Videos;
use App\Models\News;

class VideoController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    ///////////////////////////
    public function getIndex() {
        
    }

    ///////////////////////////////////////
    public function getVideos() {
        $video = new Videos();
        $info = $video->getVideos(0, 30);
        if (!$info) {
            return response()->view('errors.404', parent::$data, 500);
        }
        parent::$data['videos'] = $info;
        //////////////////////////////////////////////
        parent::$data['westnews'] = Cache::rememberForever('westnews', function () {
                    $news = new News();
                    return $news->getSidebarNews();
                });
        parent::$data['view'] = 0;
        parent::$data['vedio'] = Cache::rememberForever('vedio', function () {
                    $video = new Videos();
                    return $video->getLastVideo();
                });
//        parent::$data['vedio_other'] = Cache::rememberForever('vedio_other', function () {
//                    $video = new Videos();
//                    return $video->getVideos(1, 2);
//                });
        return view('frontend.video.view', parent::$data);
    }

}
