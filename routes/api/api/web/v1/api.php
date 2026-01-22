<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Web API v1 routes
Route::group(['namespace' => 'Web\V1', 'prefix' => 'web/v1'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');

        //forgot password
        Route::post('forgot-password', 'ForgotPasswordController@sendResetLink');
        Route::post('reset-password', 'ForgotPasswordController@resetPassword');
    });
});