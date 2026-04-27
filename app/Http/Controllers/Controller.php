<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\ShoppingCategory;
use App\Models\Socials;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controller as BaseController;

//use App\Models\Advertisements;

class Controller extends BaseController {

    public static $data = [];

    public function __construct() {

        self::$data['minutes'] = 60 * 24;
        //////////////////////////////////////
        self::$data['home'] = 0;
        self::$data['mysettings'] = Cache::rememberForever('mysettings', function () {
                    $settings = new Settings();
                    return $settings->getSetting(1);
                });
        self::$data['social'] = Cache::remember('social', self::$data['minutes'], function () {
                    $social = new Socials();
                    return $social->getAllSocialActive();
                });
    }

}
