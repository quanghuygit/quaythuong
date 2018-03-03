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

Route::get('/', ['uses' => 'WheelController@index', 'as' => 'wheel']);


Route::group(['prefix' => 'backend'], function () {
    Route::get('/', ['uses' => 'AdminController@index', 'as' => 'admin.index']);
    Route::group(['prefix' => 'contract'], function () {
        Route::get('/', ['uses' => 'ContractController@index', 'as' => 'contract.index']);

        Route::get('create', ['uses' => 'ContractController@create', 'as' => 'contract.create']);
        Route::post('store', ['uses' => 'ContractController@store', 'as' => 'contract.store']);

        Route::get('edit/{id}', ['uses' => 'ContractController@edit', 'as' => 'contract.edit']);
        Route::put('update/{id}', ['uses' => 'ContractController@update', 'as' => 'contract.update']);

        Route::delete('delete/{id}', ['uses' => 'ContractController@delete', 'as' => 'contract.delete']);
    });
    Route::resource('award', 'AwardController');
    Route::group(['prefix' => 'adward'], function () {
        Route::get('delete/{id}', ['uses' => 'AwardController@destroy', 'as' => 'award.delete']);
        Route::get('winners', ['uses' => 'AwardController@winners', 'as' => 'award.winners']);
        Route::get('create-winner/{id}', ['uses' => 'AwardController@createWinner', 'as' => 'award.createWinner']);
        Route::post('store-winner', ['uses' => 'AwardController@storeWinner', 'as' => 'award.storeWinner']);
        Route::post('ajaxtvkt', ['uses' => 'AwardController@ajaxtvkt', 'as' => 'award.ajaxtvkt']);
    });
});