<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SearchService;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        parent::__construct();
        $this->searchService = $searchService;
    }

    /**
     * Handle AJAX search requests (Dropdown).
     */
    public function ajaxSearch(Request $request)
    {
        $query = $request->input('q', '');
        
        $results = $this->searchService->search($query, 5);

        return response()->json([
            'status' => 'success',
            'data' => $results,
        ]);
    }

    /**
     * Handle Full-page search results.
     */
    public function fullSearch(Request $request)
    {
        $query = $request->input('q', '');
        
        // Use pagination
        $results = $this->searchService->fullSearchPaginated($query, 10);

        // Append query string to pagination links
        $results->appends(['q' => $query]);

        $data = array_merge(parent::$data, compact('results', 'query'));

        return view('frontend.search.results', $data);
    }
}
