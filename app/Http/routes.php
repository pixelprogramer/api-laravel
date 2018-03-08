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

Route::get('/', function () {
    return view('welcome');
});
//Rutas usuario-----------------------------------------------------------------

Route::post('/api/registro-Usuario',[
    'as'=>"registrarUsuario",
    'uses'=>"UserController@register"
]);
Route::post('/api/login-usuarios',[
    'as'=>"loginUsuario",
    'uses'=>"UserController@login"
]);
Route::resource('/api/cars','CarController');