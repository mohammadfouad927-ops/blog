<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(){
        $numberOfUsers = User::where('is_admin',0)->count();
        $numberOfAdmins = User::where('is_admin',1)->count();
        $numberOfPosts = Post::count();
        return view ('dashboard.index',[
            'users'=>$numberOfUsers,
            'admins' => $numberOfAdmins,
            'posts' => $numberOfPosts,
        ]);
    }
}
