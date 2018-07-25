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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::prefix('api/alert')->group(function () {

	//CREATE
	Route::post('/create', 'AlertController@create');

	//READ
	Route::get('/', 'AlertController@getAll');

	//READ ONE
	Route::get('{id}/getOne', 'AlertController@getOne');

	//UPDATE
	Route::post('/update', 'AlertController@update');

	//DELETE
	Route::delete('{id}/delete', 'AlertController@delete');

	//GET ALL WITHOUT PAGINATE
	Route::get('/all', 'AlertController@getAllWithoutPaginate');

	//GET ALL ALERTS ONLY
	Route::get('/alerts', 'AlertController@getAlerts');

});