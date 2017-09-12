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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function () {
    //    Route::get('/link1', function ()    {
//        // Uses Auth Middleware
//    });

    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_routes
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/aktiv8me/verify/{token}', '\App\Http\Controllers\Auth\RegisterController@verify')->name('register.verify');
Route::get('/aktiv8me/resend', '\App\Http\Controllers\Auth\RegisterController@resend');
Route::post('/aktiv8me/resend', '\App\Http\Controllers\Auth\RegisterController@resend')->name('register.resend');
