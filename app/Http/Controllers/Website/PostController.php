<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = $request->validated();
        $post['user_id'] = auth()->id();
        $post['slug'] = Str::slug($post['title']).'-' . uniqid();
        Post::create($post);
        return redirect()->route('blog');
    }

    /**
     * Display the specified resource.
     */

    public function show(User $user, Post $post):View{
        return view('website.post.show',[
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user,Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return redirect()->route('blog.profile.show',[Auth::user()->name])->with('success', 'Post deleted successfully');
    }

}
