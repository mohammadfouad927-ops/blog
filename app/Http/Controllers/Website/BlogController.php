<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;


class BlogController extends Controller
{
    public function index(Request $request){
        $posts = Post::with('user')->latest()->paginate(10);

        if(request()->ajax()){
            return view('website.partials.posts',[
                'posts' => $posts
            ])->render();
        }
        return view('website.index',[
            'posts' => $posts,
        ]);
    }
}
