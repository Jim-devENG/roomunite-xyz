<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Mobile API v1 routes
Route::group(['namespace' => 'Mobile\V1', 'prefix' => 'mobile/v1'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
    });
});
