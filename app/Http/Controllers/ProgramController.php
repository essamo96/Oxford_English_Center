<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Pages;

class ProgramController extends Controller {

    public function __construct() {
        parent::__construct();
        self::$data['home'] = 1;
    }

    ///////////////////////////
    public function getIndex() {
        return view('frontend.program.index', parent::$data);
    }

    public function getDalel() {
        return view('frontend.program.dalel', parent::$data);
    }

    public function getAims() {
        $page = new Pages();

        $info = $page->getPage(4);
        if (!$info) {
            return response()->view('errors.404', parent::$data, 500);
        }
        parent::$data['page'] = $info;
        return view('frontend.program.page', parent::$data);
    }

    public function getOuts() {
        $page = new Pages();

        $info = $page->getPage(5);
        if (!$info) {
            return response()->view('errors.404', parent::$data, 500);
        }
        parent::$data['page'] = $info;
        return view('frontend.program.page', parent::$data);
    }

    public function getLevel() {
        $page = new Pages();

        $info = $page->getPage(6);
        if (!$info) {
            return response()->view('errors.404', parent::$data, 500);
        }
        parent::$data['page'] = $info;
        return view('frontend.program.page', parent::$data);
    }

    public function getTable() {
        $page = new Pages();

        $info = $page->getPage(7);
        if (!$info) {
            return response()->view('errors.404', parent::$data, 500);
        }
        parent::$data['page'] = $info;
        return view('frontend.program.page', parent::$data);
    }

    public function getResource() {
        $page = new Pages();

        $info = $page->getPage(8);
        if (!$info) {
            return response()->view('errors.404', parent::$data, 500);
        }
        parent::$data['page'] = $info;
        return view('frontend.program.page', parent::$data);
    }

}
