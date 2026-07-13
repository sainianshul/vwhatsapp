<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/v1/messages/send', [\App\Http\Controllers\Api\V1\MessageController::class, 'send']);
});
