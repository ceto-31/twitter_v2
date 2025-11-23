<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\TweetLikeController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect /dashboard to /tweets for a better flow
Route::get('/dashboard', function () {
    return redirect()->route('tweets.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Auth Group
Route::middleware('auth')->group(function () {
    
    // Tweet Routes (Create, Read, Update, Delete)
    Route::resource('tweets', TweetController::class);

    // Tweet Routes
    Route::resource('tweets', TweetController::class);

    // User Profile Route
    Route::get('/users/{user}', [UserProfileController::class, 'show'])->name('users.show');
    
    // Like Route (NEW)
    Route::post('/tweets/{tweet}/like', TweetLikeController::class)->name('tweets.like');

    // Profile Routes (Default Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';