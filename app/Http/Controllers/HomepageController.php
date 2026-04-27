<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Pages;
use App\Models\News;
use App\Models\Partners;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\QrCode;
use Illuminate\Http\Request;

class HomepageController extends Controller {

    public function __construct() {
        parent::__construct();
        self::$data['home'] = 1;
    }

    ///////////////////////////
    public function getIndex() {
        parent::$data['sliders'] = Cache::rememberForever('sliders', function () {
                    $news = new News();
                    return $news->getCategoryNews(1, 3);
                });
        parent::$data['news'] = Cache::rememberForever('news', function () {
                    $news = new News();
                    return $news->getCategoryNews(2, 3);
                });
        $page = new Pages();
        parent::$data['about'] = $page->getPageBySlug('intro');
        $partner = new Partners();
        parent::$data['partners'] = $partner->getLastPartners(0, 10, 3);

        $page = new Pages();
        parent::$data['timetable'] = $page->getPageBySlug('timetable');
        $page = new Pages();
        parent::$data['teachers'] = $page->getPageBySlug('teachers');
        $page = new Pages();
        parent::$data['value'] = $page->getPageBySlug('value');
        $page = new Pages();
        parent::$data['students'] = $page->getPageBySlug('students');
        return view('frontend.home.view', parent::$data);
    }
    
    public function generateQRCode(Request $request)
    {
        $renderer = new ImageRenderer(
           new RendererStyle(400),
           new ImagickImageBackEnd()
       );
       $writer = new Writer($renderer);
       $writer->writeFile('Hello World!', 'qrcode.png');
    
    }

}
