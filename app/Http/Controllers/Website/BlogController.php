<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Post;


class BlogController extends Controller
{
    public function index(){
        $posts = Post::all();
        return view('website.index',[
            'posts' => $posts->sortByDesc('created_at'),
        ]);
    }
}
