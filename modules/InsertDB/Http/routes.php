<?php

Route::group(['prefix' => 'insertdb', 'namespace' => 'Modules\InsertDB\Http\Controllers'], function()
{
	Route::get('/', 'InsertDBController@index');
	Route::get('/users', 'InsertDBController@insertusers');
});