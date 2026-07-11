<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TargetUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginHistoryController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('custom-login', [AuthController::class, 'customLogin'])->name('login.custom'); 
Route::match(['get', 'post'], 'signout', [AuthController::class, 'signOut'])->name('signout');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Admin Only Routes
    Route::middleware(['admin'])->group(function () {
        // User Management
        Route::get('users/trash', [UserController::class, 'trash'])->name('users.trash');
        Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::resource('users', UserController::class);
        
        // Login History
        Route::get('login-history', [LoginHistoryController::class, 'index'])->name('login_history.index');
        Route::post('login-history/empty', [LoginHistoryController::class, 'empty'])->name('login_history.empty');

        // Bot Management
        Route::get('bots/trash', [\App\Http\Controllers\BotController::class, 'trash'])->name('bots.trash');
        Route::post('bots/{id}/restore', [\App\Http\Controllers\BotController::class, 'restore'])->name('bots.restore');
        Route::post('bots/{bot}/cookie', [\App\Http\Controllers\BotController::class, 'updateCookie'])->name('bots.cookie.update');
        Route::post('bots/{bot}/health-check', [\App\Http\Controllers\BotController::class, 'healthCheck'])->name('bots.health-check');
        Route::resource('bots', \App\Http\Controllers\BotController::class);
    });

    // Available to all authenticated users (User + Admin)
    
    // Profiles (Subjects)
    Route::get('subjects/trash', [\App\Http\Controllers\SubjectController::class, 'trash'])->name('subjects.trash');
    Route::post('subjects/{id}/restore', [\App\Http\Controllers\SubjectController::class, 'restore'])->name('subjects.restore');
    Route::resource('subjects', \App\Http\Controllers\SubjectController::class);

    // Social Accounts
    Route::get('subjects/{subject}/accounts/create', [\App\Http\Controllers\SocialAccountController::class, 'create'])->name('subjects.accounts.create');
    Route::post('social-accounts/search', [\App\Http\Controllers\SocialAccountController::class, 'search'])->name('social-accounts.search');
    Route::post('social-accounts', [\App\Http\Controllers\SocialAccountController::class, 'store'])->name('social-accounts.store');
    Route::post('social-accounts/{id}/scrape', [\App\Http\Controllers\SocialAccountController::class, 'scrape'])->name('social-accounts.scrape');
    Route::post('social-accounts/{id}/sync-scrape', [\App\Http\Controllers\SocialAccountController::class, 'syncScrape'])->name('social-accounts.sync-scrape');
    Route::get('social-accounts/{id}/status', [\App\Http\Controllers\SocialAccountController::class, 'checkStatus'])->name('social-accounts.status');
    Route::delete('social-accounts/{id}', [\App\Http\Controllers\SocialAccountController::class, 'destroy'])->name('social-accounts.destroy');
    
    // Auto-Engage (Automation Rules)
    Route::post('social-accounts/{account}/auto-engage', [\App\Http\Controllers\AutomationRuleController::class, 'store'])->name('automation-rules.store');
    Route::delete('social-accounts/{account}/auto-engage', [\App\Http\Controllers\AutomationRuleController::class, 'destroy'])->name('automation-rules.destroy');

    // Comments & Engagement
    Route::post('posts/bulk-engage', [\App\Http\Controllers\PostEngagementController::class, 'store'])->name('posts.bulk-engage');
    Route::post('comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');

    // Automation & Command Center
    Route::resource('automation-templates', \App\Http\Controllers\AutomationTemplateController::class)->except(['show']);
    
    Route::get('command-center', [\App\Http\Controllers\ScheduledOperationController::class, 'index'])->name('command-center.index');
    Route::post('command-center/{operation}/cancel', [\App\Http\Controllers\ScheduledOperationController::class, 'cancel'])->name('command-center.cancel');
    Route::delete('command-center/{operation}', [\App\Http\Controllers\ScheduledOperationController::class, 'destroy'])->name('command-center.destroy');

    // Notifications
    Route::get('notifications/unread', [\App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('notifications/{id}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('notifications/mark-all-as-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
});
