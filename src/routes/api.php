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

/**
 *  Endpoint middleware and prefix hierarchy:
 * 
 *  prefix
 *    auth
 *      verification
 */

/** Endpoints for handling authentication and session */
Route::prefix('/auth')->group(function() {
    Route::post('/login', 'AuthController@login')->name('login');
    Route::post('/refresh', 'AuthController@refresh')->name('refresh');
    Route::post('/register', 'AuthController@register')->name('register');
});

/** Endpoints for handling user`s information */
Route::prefix('/users')->name('users.')->group(function() {

    /** For these endpoints user must be authenticated */
    Route::middleware(['auth:api'])->group(function() {

        /** Endpoints for unverified users */
        Route::middleware(['verified:no'])->group(function() {

            /** Endpoints for handling user`s verification */
            Route::prefix('/verify')->group(function() {

                /** Verify user`s email address */
                Route::post('/', 'UserController@verify')->name('verify');

                /** Resend verification code to user`s email address */
                Route::get('/resend', 'UserController@resendVerification')->name('verify.resend');
            }); 
    
            /** Get common user`s information */
            Route::get('/details', 'UserController@details')->name('details');

        });

        /** Endpoints for verified users */
        Route::middleware(['verified:yes'])->group(function() {

            /** Another endpoints only for verified users */

        });

    });

});