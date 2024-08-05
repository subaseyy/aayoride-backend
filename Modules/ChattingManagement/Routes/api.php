<?php

use Illuminate\Support\Facades\Route;
use Modules\ChattingManagement\Http\Controllers\Api\New\ChattingController;


Route::group(['prefix' => 'customer'], function () {
    Route::group(['prefix' => 'chat', 'middleware' => ['auth:api', 'maintenance_mode']], function () {
        Route::controller(ChattingController::class)->group(function () {
            Route::get('find-channel', 'findChannel');
            Route::put('create-channel', 'createChannel');
            Route::put('send-message', 'sendMessage');
            Route::get('conversation', 'conversation');
            Route::get('channel-list', 'channelList');
        });
    });
});

Route::group(['prefix' => 'driver'], function () {
    Route::group(['prefix' => 'chat', 'middleware' => ['auth:api', 'maintenance_mode']], function () {
        Route::controller(ChattingController::class)->group(function () {
            Route::get('find-channel', 'findChannel');
            Route::put('create-channel', 'createChannel');
            Route::put('send-message', 'sendMessage');
            Route::get('conversation', 'conversation');
            Route::get('channel-list', 'channelList');
        });
    });
});
