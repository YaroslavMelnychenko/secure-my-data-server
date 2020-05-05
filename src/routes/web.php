<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/minio', function () {
    Storage::disk(config('filesystems.cloud'))->put('file.txt', 'Test file write successful', 'public');

    return Storage::disk(config('filesystems.cloud'))->get('file.txt');
});