<?php

namespace App\Http\Controllers\Website\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\website\Auth\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class RegisterUserWebsiteController extends Controller
{
    public function create():View{
        return view('website.auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse|\Illuminate\Http\JsonResponse{
        try {

            $userCredentails = $request->validated();
            $userCredentails['password'] = Hash::make($userCredentails['password']);

            $user = User::create($userCredentails);

            Auth::login($user);

            return redirect(route('blog.home', absolute: false));
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
