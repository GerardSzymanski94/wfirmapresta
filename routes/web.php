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
    return redirect()->route('admin.index');
});

Route::namespace('Admin')->name('admin.')->prefix('administracja')->group(function () {

    Route::get('/', 'DashboardController@logs')->name('index');
    Route::get('/logs', 'DashboardController@logs')->name('logs');
    Route::get('/log/{log}', 'DashboardController@log')->name('log');
    //  Route::get('/getgoods', 'DashboardController@getWFirmaGoods')->name('getgoods');
    // Route::get('/getproducts', 'PrestaController@getProducts')->name('getproducts');
    // Route::get('/getstocks', 'PrestaController@getStockAvailable')->name('getstocks');
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', 'DashboardController@index')->name('index');
    });


    Route::get('login', 'LoginController@index')->name('login');

});
Auth::routes();
