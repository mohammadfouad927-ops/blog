<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\Website\PostController as WebsitePostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Website\ProfileController as WebsiteProfileController;
use App\Http\Controllers\Website\SettingController as WebsiteSettingController;
use App\Http\Controllers\Website\BlogController;
use App\Http\Controllers\Website\SearchController;
use App\Http\Middleware\isadmin;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/dashboard')->middleware(['auth',isadmin::class])->group(function(){
    Route::get('/', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::resource('/posts', PostController::class);
    Route::get('/users',function(){return 'Hello Users';})->name('user');

    });
    
    Route::get('/posts/{post}/like/count', [LikeController::class, 'count']);
    Route::get('/posts/most-liked', [LikeController::class, 'mostLike']);



Route::prefix('/blog')->group(function(){

    Route::get('/', [BlogController::class,'index'])->name('blog');

    Route::middleware('auth')->group(function () {
            Route::get('/me/settings', [WebsiteSettingController::class, 'edit'])->name('blog.settings.edit');
            Route::patch('/me/settings', [WebsiteSettingController::class, 'update'])->name('blog.settings.update');
            Route::delete('/me/settings', [WebsiteSettingController::class, 'destroy'])->name('blog.settings.destroy');

            Route::delete('/post/{post}/like',[LikeController::class, 'destroy']);
            Route::get('/posts/{post}/like',[LikeController::class,'store']);
        });

    Route::get('/@{user:name}/{post:slug}',[WebsitePostController::class,'show'])->name('blog.post.show');
    Route::resource('posts', WebsitePostController::class)
        ->only(['create','edit', 'update', 'store', 'destroy'])
        ->middlewareFor(['create','edit', 'update', 'store', 'destroy'],'auth')
        ->names('blog.post');

    Route::get('/search', SearchController::class)->name('blog.search');
    Route::get('/search/users', [SearchController::class,'userSearch'])->name('blog.search.users');
    Route::get('/@{user:name}', [WebsiteProfileController::class, 'show'])->name('blog.profile.show');

});

require __DIR__.'/auth.php';

