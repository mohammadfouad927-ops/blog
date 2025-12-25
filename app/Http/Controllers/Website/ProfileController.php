<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{

    public function show(User $user): View{

        $posts = $user->posts()->latest()->get();


        return view('website.profile.show',[
            'posts' => $posts,
            'user' => $user,
        ]);
    }
}
