<?php

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


Route::group(['prefix' => '/v1.0', 'middleware' => 'responseBodyProcess'], function () {

    Route::get('/index', 'ProductController@index');

    Route::get('/config', 'ProductController@config');

    Route::get('/news', 'ProductController@news');

    Route::post('/upload', 'CommonController@postUploadPicture');

});