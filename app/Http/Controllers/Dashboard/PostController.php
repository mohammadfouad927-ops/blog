<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $posts = Post::all();
        return view('dashboard.posts.index', ['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\View
    {
        //
        return view('dashboard.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request):RedirectResponse
    {
        $requests = $request->validated();
        Post::create($requests);
        if(Auth::user()->is_admin){

            return redirect()->route('posts.index');
        }
        return redirect()->route('blog');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): View
    {
        //
        return view('dashboard.posts.show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): JsonResponse
    {
        //
//        return view('posts.edit', ['posts' => $post]);
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post):RedirectResponse
    {
        //
        $requestPost = $request->validated();
        $post->update($requestPost);
        return redirect()->route('posts.show',['post' =>$post])->with('success', 'Post updated successfully');;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
            if(Auth::user()->can('delete', $post)){
                $post->delete();
                return redirect()->route('posts.index');
            }
            return abort(403);
    }
}
