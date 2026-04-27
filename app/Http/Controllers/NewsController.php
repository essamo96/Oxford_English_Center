<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\News;

class NewsController extends Controller {

    public function __construct() {
        parent::__construct();
        self::$data['home'] = 0;
    }

    ///////////////////////////
    public function getIndex() {
        
    }

    ///////////////////////////////////////
    public function getNews($cat_id = NULL, $id = 0) {
        $id = $id == 0 ? $cat_id : $id;
        $news = new News();
        $info = $news->getNews($id);
        if (!$info) {
            return response()->view('errors.404', parent::$data, 500);
        }
        parent::$data['post_news'] = $info;
        parent::$data['related'] = $news->getNewsByCategory(3, 0, 4);

        return view('frontend.news.details', parent::$data);
    }

}
