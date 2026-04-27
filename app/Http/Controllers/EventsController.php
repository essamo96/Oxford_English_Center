<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Events;

class EventsController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function getlist() {
        $events = new Events();
        parent::$data['cevents'] = $events->getTodayEvents(0, 9);
        parent::$data['pevents'] = $events->getPreEvents(0, 9);
        parent::$data['nevents'] = $events->getNextEvents(0, 9);


        return view('frontend.events.list', parent::$data);
    }

}
