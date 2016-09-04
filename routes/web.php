<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/
Route::get('/', 'HomeController@index');

Auth::routes();

Route::auth();

Route::get('/home', 'HomeController@index');

Route::get('article/{id}', 'ArticleController@show');

Route::get('now', function () {
    return date('Y-m-d H:i:s');
});

Route::group(['middleware' => 'auth', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
    Route::get('/', 'HomeController@index');
    Route::resource('article', 'ArticleController');
});
