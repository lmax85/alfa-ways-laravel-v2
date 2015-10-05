<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');
Route::get('ldaptest', 'WelcomeController@ldaptest');
Route::get('ldaplogin', 'WelcomeController@ldaplogin');
Route::post('ldapauth', 'WelcomeController@ldapauth');
Route::get('testauth', 'WelcomeController@testauth');

Route::get('api', 'ApiController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
