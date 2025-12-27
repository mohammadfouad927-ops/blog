<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{

    public function show(User $user): View{

        $posts = Post::where('user_id',$user->id)->latest()->paginate(2);

        return view('website.profile.show',[
            'posts' => $posts,
            'user' => $user,
        ]);
    }
}
