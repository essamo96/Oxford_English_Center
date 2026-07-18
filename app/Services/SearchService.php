<?php

namespace App\Services;

use App\Models\Pages;
use App\Models\News;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    /**
     * Perform a search across multiple models.
     * 
     * @param string $query
     * @param int $limit Per-model limit for quick search
     * @return \Illuminate\Support\Collection
     */
    public function search($query, $limit = 5)
    {
        $results = collect();

        if (empty(trim($query))) {
            return $results;
        }

        // 1. Search Pages (Courses / Static Pages)
        $pages = Pages::where('status', 1)
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', '%' . $query . '%')
                  ->orWhere('details', 'LIKE', '%' . $query . '%');
            })
            ->take($limit)
            ->get();

        foreach ($pages as $page) {
            $results->push([
                'id' => $page->id,
                'title' => $page->title,
                'url' => url('page/' . $page->slug),
                'type' => 'Course / Page',
                'description' => Str::limit(strip_tags($page->details), 80)
            ]);
        }

        // 2. Search News (Posts / Announcements)
        $news = News::where('publish', 1)
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', '%' . $query . '%')
                  ->orWhere('descs', 'LIKE', '%' . $query . '%');
            })
            ->take($limit)
            ->get();

        foreach ($news as $item) {
            $results->push([
                'id' => $item->id,
                'title' => $item->title,
                'url' => url('posts/' . $item->id),
                'type' => 'News / Event',
                'description' => Str::limit(strip_tags($item->descs), 80)
            ]);
        }

        // 3. Search Static Pages (Menu Items)
        $staticPages = [
            ['title' => 'Home', 'url' => url('/'), 'type' => 'Main Menu', 'description' => 'Go to the homepage'],
            ['title' => 'About Us', 'url' => url('/page/about-us'), 'type' => 'Main Menu', 'description' => 'Learn more about Oxford English Centre'],
            ['title' => 'Oxford Test of English', 'url' => url('/test-of-english'), 'type' => 'Main Menu', 'description' => 'Details about the Oxford Test of English'],
            ['title' => 'Courses', 'url' => url('/#courses'), 'type' => 'Main Menu', 'description' => 'Explore our available courses'],
            ['title' => 'IELTS Prize', 'url' => url('/page/ielts-prize'), 'type' => 'Main Menu', 'description' => 'Information about the IELTS Prize'],
            ['title' => 'Community', 'url' => url('/community'), 'type' => 'Main Menu', 'description' => 'Explore the Oxford community'],
            ['title' => 'Gallery / Photos', 'url' => url('/photos'), 'type' => 'Main Menu', 'description' => 'View our photo gallery'],
            ['title' => 'Jobs / Careers', 'url' => url('/jobs'), 'type' => 'Main Menu', 'description' => 'Job opportunities at Oxford'],
            ['title' => 'Contact Us', 'url' => url('/contact'), 'type' => 'Main Menu', 'description' => 'Get in touch with us'],
        ];

        foreach ($staticPages as $page) {
            // Simple case-insensitive search
            if (stripos($page['title'], $query) !== false || stripos($page['description'], $query) !== false) {
                $results->push([
                    'id' => 'static_' . md5($page['title']),
                    'title' => $page['title'],
                    'url' => $page['url'],
                    'type' => $page['type'],
                    'description' => $page['description']
                ]);
            }
        }

        // Return a combined, sorted collection
        return $results->sortByDesc('id')->values();
    }

    /**
     * Perform a paginated search for the full search results page.
     * 
     * @param string $query
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function fullSearchPaginated($query, $perPage = 10)
    {
        if (empty(trim($query))) {
            return new LengthAwarePaginator([], 0, $perPage);
        }

        // For cross-model pagination without Scout, we can use a raw UNION or fetch everything and paginate the collection.
        // For simplicity and to avoid complex UNIONs with different timestamps/deleted_at, we can paginate a collection
        // if the dataset isn't millions of rows. 
        // Here we build a manual union query for better performance.

        $pagesQuery = DB::table('pages')
            ->select('id', 'title', 'details as description', DB::raw("'Course / Page' as model_type"), 'created_at', 'slug as link_identifier')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', '%' . $query . '%')
                  ->orWhere('details', 'LIKE', '%' . $query . '%');
            });

        $newsQuery = DB::table('news')
            ->select('id', 'title', DB::raw('descs as description'), DB::raw("'News / Event' as model_type"), 'created_at', DB::raw("id as link_identifier"))
            ->where('publish', 1)
            ->whereNull('deleted_at')
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', '%' . $query . '%')
                  ->orWhere('descs', 'LIKE', '%' . $query . '%');
            });

        $combinedQuery = $pagesQuery->unionAll($newsQuery)->orderBy('created_at', 'desc');

        $paginator = $combinedQuery->paginate($perPage);

        // Prepend static pages on the first page
        if ($paginator->currentPage() == 1) {
            $staticPages = [
                ['id' => 'static_1', 'title' => 'Home', 'description' => 'Go to the homepage', 'model_type' => 'Main Menu', 'created_at' => now(), 'link_identifier' => '/'],
                ['id' => 'static_2', 'title' => 'About Us', 'description' => 'Learn more about Oxford English Centre', 'model_type' => 'Main Menu', 'created_at' => now(), 'link_identifier' => '/page/about-us'],
                ['id' => 'static_3', 'title' => 'Oxford Test of English', 'description' => 'Details about the Oxford Test of English', 'model_type' => 'Main Menu', 'created_at' => now(), 'link_identifier' => '/test-of-english'],
                ['id' => 'static_4', 'title' => 'Courses', 'description' => 'Explore our available courses', 'model_type' => 'Main Menu', 'created_at' => now(), 'link_identifier' => '/#courses'],
                ['id' => 'static_5', 'title' => 'IELTS Prize', 'description' => 'Information about the IELTS Prize', 'model_type' => 'Main Menu', 'created_at' => now(), 'link_identifier' => '/page/ielts-prize'],
                ['id' => 'static_6', 'title' => 'Community', 'description' => 'Explore the Oxford community', 'model_type' => 'Main Menu', 'created_at' => now(), 'link_identifier' => '/community'],
                ['id' => 'static_7', 'title' => 'Gallery / Photos', 'description' => 'View our photo gallery', 'model_type' => 'Main Menu', 'created_at' => now(), 'link_identifier' => '/photos'],
                ['id' => 'static_8', 'title' => 'Jobs / Careers', 'description' => 'Job opportunities at Oxford', 'model_type' => 'Main Menu', 'created_at' => now(), 'link_identifier' => '/jobs'],
                ['id' => 'static_9', 'title' => 'Contact Us', 'description' => 'Get in touch with us', 'model_type' => 'Main Menu', 'created_at' => now(), 'link_identifier' => '/contact'],
            ];

            $matchedStatic = collect();
            foreach ($staticPages as $page) {
                if (stripos($page['title'], $query) !== false || stripos($page['description'], $query) !== false) {
                    $matchedStatic->push((object) $page);
                }
            }

            if ($matchedStatic->isNotEmpty()) {
                // Prepend the matched static pages to the items collection
                $items = $paginator->getCollection();
                $merged = $matchedStatic->merge($items);
                $paginator->setCollection($merged);
            }
        }

        return $paginator;
    }
}
