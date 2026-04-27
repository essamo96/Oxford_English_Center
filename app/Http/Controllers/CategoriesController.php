<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use App\Models\Categories;
use App\Models\News;
use App\Models\Pages;
use Request;

class CategoriesController extends Controller {

    public function __construct() {
        parent::__construct();
    }

///////////////////////////
    public function getCommunity() {
        $category_id = 3;

//////////////////////////////////////////////
        parent::$data['category_info'] = Cache::remember('category_info_' . $category_id, parent::$data['minutes'], function () use($category_id) {
                    $categories = new Categories();
                    return $categories->getActiveCategories($category_id);
                });
//////////////////////////////////////////////
        $page = new Pages();
        parent::$data['page'] = $page->getPageBySlug('community');
        $news = new News();
        parent::$data['category_news'] = $news->getCategoryNews($category_id, 15);
///////////////////////////////////////////////////////
        return view('frontend.categories.view', parent::$data);
    }

    public function getIndex($category_id) {
        if (ctype_digit($category_id)) {
//////////////////////////////////////////////
            parent::$data['category_info'] = Cache::remember('category_info_' . $category_id, parent::$data['minutes'], function () use($category_id) {
                        $categories = new Categories();
                        return $categories->getActiveCategories($category_id);
                    });
//////////////////////////////////////////////
            parent::$data['page'] = Null;
            $news = new News();
            parent::$data['category_news'] = $news->getCategoryNews($category_id, 15);


            parent::$data['view'] = 1;
///////////////////////////////////////////////////////
            return view('frontend.categories.view', parent::$data);
        }
        echo '<center><h1>';
        echo 'حدث خطا';
        echo '</h1>';
    }
    public function search(Request $request) {
        $searchTerms = Request::input('txtsearch');
        $section = new \stdClass();
        $section->name = 'نتائج البحث';
        parent::$data['category_info'] = $section;
        $news = new News();
        parent::$data['category_news'] = $news->getSearchNews($searchTerms);
        return view('frontend.categories.view', parent::$data);
    }

}
