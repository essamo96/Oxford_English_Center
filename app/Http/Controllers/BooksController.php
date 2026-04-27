<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Books;

class BooksController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    ///////////////////////////
    public function getIndex() {
        $news = new Books();
        $info = $news->getBooks(0, 20);
        parent::$data['books'] = $info;

        parent::$data['view'] = 0;
        return view('frontend.books.view', parent::$data);
    }

}
