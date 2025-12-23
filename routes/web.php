<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\PostController;
use App\Http\Controllers\Website\PostController as WebsitePostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Website\ProfileController as WebsiteProfileController;
use App\Http\Controllers\Website\BlogController;
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
    Route::get('/', [BlogController::class,'index'])->middleware('auth')->name('blog');
    Route::middleware('auth')->group(function () {
            Route::get('/profile', [WebsiteProfileController::class, 'edit'])->name('blog.profile.edit');
            Route::patch('/profile', [WebsiteProfileController::class, 'update'])->name('blog.profile.update');
            Route::delete('/profile', [WebsiteProfileController::class, 'destroy'])->name('blog.profile.destroy');
        });
    Route::resource('/posts', WebsitePostController::class)->middleware('auth')->names('blog.posts');

});

require __DIR__.'/auth.php';

