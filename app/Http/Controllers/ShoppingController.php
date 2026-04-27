<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\ShoppingCategory;
use App\Models\Shopping;

class ShoppingController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function getCategory() {
        parent::$data['shoppingcategories'] = Cache::remember('shoppingcategories', self::$data['minutes'], function () {
                    $categories = new ShoppingCategory();
                    return $categories->getActiveCategories();
                });
        return view('frontend.shopping.index', parent::$data);
    }

    public function getCategoryProducts($cat_slug) {
        $categories = new ShoppingCategory();
        $cat = $categories->getCategoriesBySlug(str_replace("-", " ", $cat_slug));
        if ($cat) {
            $product = new Shopping();
            parent::$data['category'] = $cat;
            parent::$data['products'] = $product->getCategoryShoppings($cat->id);
        }
        return view('frontend.shopping.view', parent::$data);
    }

    ///////////////////////////////////////
}
