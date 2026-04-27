<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Partners;

class PartnersController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function getFamily() {
        $partner = new Partners();
        parent::$data['partners'] = $partner->getAllPartners(1);
        return view('frontend.partner.family', parent::$data);
    }

    public function getIndex() {
        $partner = new Partners();
        parent::$data['partners'] = $partner->getAllPartners(2);
        return view('frontend.partner.index', parent::$data);
    }

    ///////////////////////////////////////
}
