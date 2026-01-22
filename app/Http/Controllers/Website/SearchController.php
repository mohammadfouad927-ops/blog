<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;

class SearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request):View|JsonResponse{
        $query  = $request->get('q');
        if($request->ajax()){
            $users = User::where('name', 'like', '%' . $query . '%')->orWhere('first_name', 'like', '%' . $query . '%')
                ->orWhere('last_name', 'like', '%' . $query . '%')
                ->latest()->limit(5)->get(['name','first_name','last_name']);

            return response()->json([
                'query' => $query,
                'users' => $users,
            ]);
        }
        $posts = Post::where('title', 'like', '%' . $query . '%')
            ->OrWhere('description', 'like' , '%' . $query . '%')
            ->with('user')->latest()->paginate(10);
        return view('website.search.index', [
            'query' => $query,
            'posts' => $posts,
        ]);

    }

    public function userSearch(Request $request):View{
        $query = $request->input('q');
        $users = User::where('name', 'like', '%' . $query . '%')->orWhere('first_name', 'like', '%' . $query . '%')
            ->orWhere('last_name', 'like', '%' . $query . '%')
            ->latest()->paginate(10);
        return view('website.search.user',[
            'query' => $query,
            'users' => $users,
        ]);
    }

}
