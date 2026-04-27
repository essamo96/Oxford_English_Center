<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\News;

class RssController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    ///////////////////////////
    public function getRss() {
        $news = new News();
        parent::$data['news'] = $news->getSidebarNews();
        return view('frontend.rss.view', parent::$data);
    }

}
