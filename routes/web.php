<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\PostController;
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

Route::prefix('/dashboard')->group(function(){
    Route::get('/', [DashboardController::class, 'index'])->middleware(['auth',isadmin::class, 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::resource('/posts', PostController::class)->middleware(['auth',isadmin::class]);
});



Route::prefix('/blog')->group(function(){

    Route::get('/', [BlogController::class,'index'])->name('blog');

    Route::middleware('auth')->group(function () {
            Route::get('/me/settings', [WebsiteSettingController::class, 'edit'])->name('blog.settings.edit');
            Route::patch('/me/settings', [WebsiteSettingController::class, 'update'])->name('blog.settings.update');
            Route::delete('/me/settings', [WebsiteSettingController::class, 'destroy'])->name('blog.settings.destroy');
        });

    Route::get('/@{user:name}/{post:slug}',[WebsitePostController::class,'show'])->name('blog.post.show');
    Route::resource('posts', WebsitePostController::class)
        ->only(['edit', 'update', 'store', 'destroy'])
        ->middlewareFor(['edit', 'update', 'store', 'destroy'],'auth')
        ->names('blog.post');

    Route::get('/search', SearchController::class)->name('blog.search');
    Route::get('/search/users', [SearchController::class,'userSearch'])->name('blog.search.users');
    Route::get('/@{user:name}', [WebsiteProfileController::class, 'show'])->name('blog.profile.show');

});

require __DIR__.'/auth.php';

