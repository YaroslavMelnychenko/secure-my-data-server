<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', 'ApiController@index');

Route::prefix('/auth')->group(function() {
    Route::post('/login', 'AuthController@login')->name('login');
    Route::post('/refresh', 'AuthController@refresh')->name('refresh');
    Route::post('/register', 'AuthController@register')->name('register');
});

Route::prefix('/users')->group(function() {
    Route::group(['middleware' => 'auth:api'], function() {
        Route::name('users.')->group(function() {
            Route::get('/details', 'UserController@details')->name('details');
        });
    });
});