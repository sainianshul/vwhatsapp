<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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
    Route::get('dashboard/stats', [AuthController::class, 'dashboardStats'])->name('dashboard.stats');

    // Admin Only Routes (Middleware temporarily disabled as per user request)
    // Route::middleware(['admin'])->group(function () {


    // Bulk Messaging Routes
    Route::get('/bulk-campaigns/sample-csv', [\App\Http\Controllers\Admin\BulkCampaignController::class, 'downloadSampleCsv'])->name('admin.bulk_campaigns.sample_csv');
    Route::get('/bulk-campaigns/{bulkCampaign}/export', [\App\Http\Controllers\Admin\BulkCampaignController::class, 'exportCsv'])->name('admin.bulk_campaigns.export');
    Route::resource('bulk-campaigns', \App\Http\Controllers\Admin\BulkCampaignController::class, [
        'names' => 'admin.bulk_campaigns',
        'only' => ['index', 'create', 'store', 'show']
    ]);

    // Developer Settings
    Route::get('/developer-settings', [\App\Http\Controllers\Admin\DeveloperSettingController::class, 'index'])->name('admin.developer_settings.index');
    Route::get('/developer-settings/docs', [\App\Http\Controllers\Admin\DeveloperSettingController::class, 'docs'])->name('admin.developer_settings.docs');
    Route::post('/developer-settings/generate', [\App\Http\Controllers\Admin\DeveloperSettingController::class, 'generateToken'])->name('admin.developer_settings.generate');
    Route::post('/developer-settings/{id}/revoke', [\App\Http\Controllers\Admin\DeveloperSettingController::class, 'revokeToken'])->name('admin.developer_settings.revoke');

    Route::get('users/trash', [UserController::class, 'trash'])->name('users.trash');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::resource('users', UserController::class);

    // Login History
    Route::get('login-history', [LoginHistoryController::class, 'index'])->name('login_history.index');
    Route::post('login-history/empty', [LoginHistoryController::class, 'empty'])->name('login_history.empty');
    Route::delete('login-history/{id}', [LoginHistoryController::class, 'destroy'])->name('login_history.destroy');

    // WhatsApp Accounts
    Route::get('whatsapp-accounts/trash', [\App\Http\Controllers\Admin\WhatsAppAccountController::class, 'trash'])->name('whatsapp_accounts.trash');
    Route::delete('whatsapp-accounts/{id}/force-delete', [\App\Http\Controllers\Admin\WhatsAppAccountController::class, 'forceDelete'])->name('whatsapp_accounts.force_delete');
    Route::post('whatsapp-accounts/start-session', [\App\Http\Controllers\Admin\WhatsAppAccountController::class, 'startSession'])->name('whatsapp_accounts.start_session');
    Route::get('whatsapp-accounts/qr-status/{session_id}', [\App\Http\Controllers\Admin\WhatsAppAccountController::class, 'qrStatus'])->name('whatsapp_accounts.qr_status');
    Route::resource('whatsapp-accounts', \App\Http\Controllers\Admin\WhatsAppAccountController::class)->names('whatsapp_accounts');

    // WhatsApp Messages
    Route::post('whatsapp-messages/{id}/resend', [\App\Http\Controllers\Admin\WhatsAppMessageController::class, 'resend'])->name('whatsapp_messages.resend');
    Route::resource('whatsapp-messages', \App\Http\Controllers\Admin\WhatsAppMessageController::class)->only(['index', 'create', 'store', 'destroy'])->names('whatsapp_messages');
    // });

    // Comments & Engagement
    Route::post('comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');
});
