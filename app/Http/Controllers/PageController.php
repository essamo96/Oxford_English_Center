<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Pages;

class PageController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    ///////////////////////////
    public function getIndex() {
        
    }

    ///////////////////////////////////////
    public function getTest() {
//        $page = new Pages();
//        $info = $page->getPageBySlug($slug);
//        if (!$info) {
//            return response()->view('errors.404', parent::$data, 500);
//        }
//        parent::$data['page'] = $info;
        return view('frontend.page.test', parent::$data);
    }

    public function getFormat() {
//        $page = new Pages();
//        $info = $page->getPageBySlug($slug);
//        if (!$info) {
//            return response()->view('errors.404', parent::$data, 500);
//        }
//        parent::$data['page'] = $info;
        return view('frontend.page.format', parent::$data);
    }
    public function getDates () {
//        $page = new Pages();
//        $info = $page->getPageBySlug($slug);
//        if (!$info) {
//            return response()->view('errors.404', parent::$data, 500);
//        }
//        parent::$data['page'] = $info;
        return view('frontend.page.date', parent::$data);
    }

    public function getPage($slug) {
        $page = new Pages();
        $info = $page->getPageBySlug($slug);
        if (!$info) {
            return response()->view('errors.404', parent::$data, 500);
        }
        parent::$data['page'] = $info;
        return view('frontend.page.view', parent::$data);
    }

}
