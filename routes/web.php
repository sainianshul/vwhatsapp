<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\TargetUserController;

Route::get('/', function () {
    return redirect('login');
});

Route::get('login', [CustomAuthController::class, 'index'])->name('login');
Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom'); 
Route::get('register', [CustomAuthController::class, 'registration'])->name('register-user');
Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom'); 
Route::get('signout', [CustomAuthController::class, 'signOut'])->name('signout');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [CustomAuthController::class, 'dashboard'])->name('dashboard');

    // Target User - Search & Scrape
    Route::get('target', [TargetUserController::class, 'index'])->name('target.index');
    Route::post('target/store', [TargetUserController::class, 'store'])->name('target.store');
    Route::get('target/{id}', [TargetUserController::class, 'show'])->name('target.show');
    Route::post('target/{id}/deep-scrape/{accountId}', [TargetUserController::class, 'deepScrape'])->name('target.deep-scrape');
    Route::delete('target/{id}', [TargetUserController::class, 'destroy'])->name('target.destroy');
    
    Route::get('target/search/new', [TargetUserController::class, 'searchPage'])->name('target.search.page');
    Route::post('target/search/new', [TargetUserController::class, 'search'])->name('target.search');

    // Comment Bot System
    Route::get('comments/bank', [\App\Http\Controllers\CommentBotController::class, 'commentBank'])->name('comments.bank');
    Route::post('comments/bank', [\App\Http\Controllers\CommentBotController::class, 'storeComment'])->name('comments.store');
    Route::delete('comments/bank/{id}', [\App\Http\Controllers\CommentBotController::class, 'deleteComment'])->name('comments.delete');

    Route::get('comments/accounts', [\App\Http\Controllers\CommentBotController::class, 'botAccounts'])->name('comments.accounts');
    Route::post('comments/accounts', [\App\Http\Controllers\CommentBotController::class, 'storeBotAccount'])->name('comments.accounts.store');
    Route::delete('comments/accounts/{id}', [\App\Http\Controllers\CommentBotController::class, 'deleteBotAccount'])->name('comments.accounts.delete');

    Route::post('comments/execute', [\App\Http\Controllers\CommentBotController::class, 'executeComment'])->name('comments.execute');
    Route::post('comments/post-single', [\App\Http\Controllers\CommentBotController::class, 'postSingleComment'])->name('comments.post-single');
    Route::post('comments/like-post', [\App\Http\Controllers\CommentBotController::class, 'likeSinglePost'])->name('comments.like-post');
    Route::get('comments/history', [\App\Http\Controllers\CommentBotController::class, 'commentHistory'])->name('comments.history');
});
