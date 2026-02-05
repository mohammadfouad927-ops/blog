<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
   
    /**
     * Store a newly created resource in storage.
     */
    public function store(Post $post)
    {
        $user = auth()->user();

        $post->likes()->createOrFirst(["user_id" => $user->id]);

        return response()->json([
            'Message' => 'Post Liked'
        ],201);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $user = auth()->user();

        $post->likes()->where('user_id',$user->id)->delete();

        return response()->json([
            'Message' => "Post Unliked"
        ]);

    }

    public function count(Post $post){
        return response()->json([
            'likes' => $post->likes()->count()
        ]);
    }

    public function mostLike(){
        return response()->json([
            "post" => Post::withCount("likes")->orderBy('likes_count','desc')->take(5)->get()
        ]);
    }
}
