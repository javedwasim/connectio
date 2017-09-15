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

Route::group(['middleware' => ['role:admin,edit restricted']], function () {

    Route::get('admin', [
        'as' => 'home',
        'uses' => 'HomeController@AdminView'
    ]);

});

Route::group(['middleware' => ['role:superadmin,edit website']], function () {

    Route::get('superadmin', [
        'as' => 'home',
        'uses' => 'HomeController@SuperAdminView'
    ]);
});

Route::group(['middleware' => ['role:user,edit not allowed']], function () {
    Route::get('user', [
        'as' => 'home',
        'uses' => 'HomeController@UserView'
    ]);
});


Route::group(['middleware' => 'auth'], function () {
    //    Route::get('/link1', function ()    {
//        // Uses Auth Middleware
//    });

    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_routes
});

Route::get('register/verify/{confirmationCode}', [
    'as' => 'confirmation_path',
    'uses' => 'LoginController@confirm'
]);


