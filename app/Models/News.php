<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class News extends Model {

    use SoftDeletes;

    protected $table = 'news';
    protected $fillable = [
        'title', 'description', 'details', 'type', 'thumb', 'image', 'video', 'category_id', 'tags', 'special', 'sidebar', 'user_id'
    ];
    protected $hidden = [
        '',
    ];

    //////////////////////////////////////////////
    public function category() {
        return $this->belongsTo('App\Models\Categories', 'category_id');
    }

    //////////////////////////////////////////////
    public function comment() {
        return $this->hasMany('App\Models\Comments', 'news_id', 'id');
    }

/////
    function addNews($title, $onwer, $source, $sub, $descs, $thumb, $image, $img_notes, $category_id, $tags, $resort, $pub_date, $publish, $sidebar, $user_id) {
        if ($publish == 1) {
            $pub_date = date('Y-m-d H:i:s');
        }
        $this->title = $title;
        $this->onwer = $onwer;
        $this->source = $source;
        $this->sub = $sub;
        $this->descs = $descs;
        $this->thumb = $thumb;
        $this->image = $image;
        $this->img_notes = $img_notes;
        $this->category_id = $category_id;
        $this->tags = $tags;
        $this->resort = $resort;
        $this->pub_date = $pub_date;
        $this->publish = $publish;
        $this->sidebar = $sidebar;
        $this->user_id = $user_id;

        $this->save();
        $this->updateResort($this, 0);
        return $this;
    }

    //////////////////////////////////////////////
    function updateNews($obj, $title, $onwer, $source, $sub, $descs, $thumb, $image, $img_notes, $category_id, $tags, $resort, $pub_date, $publish, $sidebar) {
        $old_resort = $obj->resort;
        if ($obj->publish == 0 && $publish == 1) {
            $pub_date = date('Y-m-d H:i:s');
        }

        $obj->title = $title;
        $obj->onwer = $onwer;
        $obj->source = $source;
        $obj->sub = $sub;
        $obj->descs = $descs;
        $obj->thumb = $thumb;
        $obj->image = $image;
        $obj->img_notes = $img_notes;
        $obj->category_id = $category_id;
        $obj->tags = $tags;
        $obj->resort = $resort;
        $obj->pub_date = $pub_date;
        $obj->publish = $publish;
        $obj->sidebar = $sidebar;


        $obj->save();
        $this->updateResort($obj, $old_resort);
        return $obj;
    }

    //////////////////////////////////////////////
    function updatePublish($id, $publish) {
        return $this
                        ->where('id', '=', $id)
                        ->update([
                            'publish' => $publish,
                            'pub_date' => date('Y-m-d H:i:s')
        ]);
    }

    function updateResort($news, $old_resort) {

        if ($old_resort != 100) {
            $resort = $news->resort ? $news->resort : 1;
            $selectedNews = $this->with('category')
                    ->select('resort', 'id')
                    ->where('category_id', '=', $news->category_id)
                    ->where('id', '<>', $news->id)
                    ->where('resort', '>=', $resort)
                    ->orderBy('resort', 'asc')
                    ->orderBy('id', 'desc')
                    ->take(20)
                    ->get();
            $i = 1;
            foreach ($selectedNews as $sort) {
                $this->where('id', '=', $sort->id)->update(['resort' => $resort + $i++]);
            }
            $this->where('resort', '>', 20)->where('resort', '<>', 100)->where('category_id', $news->category_id)->update(['resort' => 100]);
        }
    }

    //////////////////////////////////////////////
    function deleteNews($obj) {
        return $obj->delete();
    }

    //////////////////////////////////////////////
    function getNews($id) {
        $item = $this->with('category')
                ->where('publish', '=', 1)
                ->where('id', '=', $id)
                ->first();
        if ($item) {
            $item->views = $item->views + 1;
            $item->save();
            return $item;
        } else {
            return false;
        }
    }

    function getNew($id) {
        return $this->find($id);
    }

    //////////////////////////////////
    function getAllNews() {
        return $this->get();
    }

    //////////////////////////////////
    function getSpecialNews() {
        return $this->with('category')
                        ->where('publish', '=', 1)
                        ->where('sidebar', '=', 1)
                        ->orderBy('id', 'desc')
                        ->take(3)
                        ->get();
    }

    //////////////////////////////////
    function getNormalNews($start, $limit) {
        return $this->with('category')
                        ->where('publish', 1)
                        ->orderBy('resort', 'asc')
                        ->orderBy('pub_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($limit)
                        ->get();
    }

    function getMixNews() {
        $uae = $this->with('category')->where('publish', 1)->where('category_id', 1)->orderBy('resort', 'asc')->orderBy('id', 'desc')->skip(9)->take(1)->get();
        $uae1 = $this->with('category')->where('publish', 1)->where('category_id', 7)->orderBy('resort', 'asc')->orderBy('id', 'desc')->skip(3)->take(1)->get();
        $socity = $this->with('category')->where('publish', 1)->where('category_id', 2)->orderBy('resort', 'asc')->orderBy('id', 'desc')->skip(7)->take(1)->get();
        $arab = $this->with('category')->where('publish', 1)->where('category_id', 3)->orderBy('resort', 'asc')->orderBy('id', 'desc')->skip(5)->take(1)->get();
        $under = $this->with('category')->where('publish', 1)->where('category_id', 6)->orderBy('resort', 'asc')->orderBy('id', 'desc')->skip(4)->take(1)->get();
        $moreview1 = $uae->merge($arab);
        $moreview2 = $socity->merge($under);
        $moreview3 = $moreview2->merge($moreview1);
        $moreview = $moreview3->merge($uae1);
        return $moreview;
    }

    //////////////////////////////////
    function getSidebarNews() {
        return $this->with('category')
                        ->where('publish', 1)
                        ->where('sidebar', 1)
                        ->orderBy('id', 'desc')
                        ->take(4)
                        ->get();
    }

    //////////////////////////////////
    function getNewsPrev($postsId, $category_id) {
        $id = $this->where('publish', 1)
                ->where('id', '<', $postsId)
                ->where('category_id', $category_id)
                ->max('id');
        return $this->getNew($id);
    }

    //////////////////////////////////
    function getNewsNext($postsId, $category_id) {
        $id = $this->where('publish', 1)
                ->where('id', '>', $postsId)
                ->where('category_id', $category_id)
                ->min('id');
        return $this->getNew($id);
    }

    //////////////////////////////////
    function getNewsByTags($tags, $postsId) {
        if (strpos($tags, ',')) {
            $tags = \explode(',', $tags);
        } else {
            $tags = \str_replace("-", "–", $tags);
            $tags = \explode('–', $tags);
        }
        $like = '';
        $like_last = '';
        $ind = 1;
        foreach ($tags as $tag) {
            if ($ind++ == sizeof($tags)) {
                $like_last = " tags LIKE '%" . $tag . "%' ";
            } else {
                $like .= " or tags LIKE '%" . $tag . "%' ";
            }
        }


        return $this->whereRaw("(" . $like_last . $like . ")")
                        ->where('publish', 1)
                        ->where('id', '!=', $postsId)
                        ->orderBy('id', 'desc')
                        ->take(4)
                        ->get();
    }

    function getBannerNews($tags) {
        return $this->where('tags', 'LIKE', '%' . $tags . '%')
                        ->where('publish', 1)
                        ->orderBy('id', 'asc')
                        ->paginate(30);
    }

    function getSearchNews($searchTerms) {
        return $this->where('publish', 1)
                        ->where('title', 'LIKE', '%' . $searchTerms . '%')
                        ->orwhere('descs', 'LIKE', '%' . $searchTerms . '%')
                        ->orderBy('id', 'desc')
                        ->orderBy('resort', 'asc')
                        ->paginate(30);
    }

    function getTagsNews($tag) {
        return $this->where('publish', 1)
                        ->where('tags', 'LIKE', '%' . $tag . '%')
                        ->orderBy('id', 'desc')
                        ->orderBy('resort', 'asc')
                        ->paginate(30);
    }

    //////////////////////////////////
    function getNewsByCategory($category_id, $start, $limit) {
        return $this->with('category')
                        ->where('publish', '=', 1)
                        ->where('category_id', $category_id)
                        ->orderBy('id', 'desc')
                        ->skip($start)
                        ->take($limit)
                        ->paginate($limit);
    }

    function getCategoryNews($category_id, $limit) {
        return $this->with('category')
                        ->where('publish', '=', 1)
                        ->where(function($query) use ($category_id) {
                            $category = DB::table('categories')
                                    ->select('id')
                                    ->where('id', $category_id)
                                    ->orWhere('category_id', $category_id)
                                    ->get();
                            if ($category) {
                                $id = array();
                                foreach ($category as $item) {
                                    $id[] = $item->id;
                                }
                            }
                            $query->whereIn('category_id', $id);
                        })
                        ->orderBy('id', 'desc')
                        ->paginate($limit);
    }

    //////////////////////////////////
    function getNewsByMostView($limit) {
        $today = new \DateTime();
        return $this->with('category')
                        ->where('publish', '=', 1)
                        ->where('created_at', '>', $today->modify('-7 days'))
                        ->orderBy('views', 'desc')
                        ->take($limit)
                        ->get();
    }

    function getLastNews($limit) {
        return $this->with('category')
                        ->where('publish', '=', 1)
                        ->orderBy('id', 'desc')
                        ->take($limit)
                        ->get();
    }

    function getRssNews() {
        return $this->with('category')
                        ->where('publish', '=', 1)
                        ->where("DATE_FORMAT(created_at,'%Y-%m-%d')", '=', date('Y-m-d'))
                        ->orderBy('pub_date', 'desc')
                        ->get();
    }

    //////////////////////////////////////////////
    function searchNews($News = null, $publish = -1, $category = -1, $start = 0, $length = 25) {
        return $this->with('category')->where(function($query) use ($News, $publish, $category) {
                    if ($News != "") {
                        $query->where('title', 'LIKE', '%' . $News . '%');
                    }
                    if ($publish != -1) {
                        $query->where('publish', '=', $publish);
                    }
                    if ($category != -1) {
                        $query->where('category_id', '=', $category);
                    }
                })->skip($start)->orderBy('id', 'desc')->take($length);
    }

    //////////////////////////////////////////////
    function searchNewsCount($News = null, $publish = -1, $category = -1) {
        return $this->where(function($query) use ($News, $publish, $category) {
                    if ($News != "") {
                        $query->where('title', 'LIKE', '%' . $News . '%');
                    }
                    if ($publish != -1) {
                        $query->where('publish', '=', $publish);
                    }
                    if ($category != -1) {
                        $query->where('category_id', '=', $category);
                    }
                })->count('id');
    }

}
