<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/test-orm', 'App\Http\Controllers\PruebasCOntroller@testOrm');

//Rutas del api
    //Rutas de Pruebas
    Route::get('/usuario/pruebas', 'App\Http\Controllers\UserController@pruebas');
    Route::get('/categoria/pruebas', 'App\Http\Controllers\CategoryController@pruebas');
    Route::get('/entrada/pruebas', 'App\Http\Controllers\PostController@pruebas');

    //Rutas User Controller
    Route::post('/api/register', 'App\Http\Controllers\UserController@register');
    Route::post('/api/login', 'App\Http\Controllers\UserController@login');
    Route::put('/api/user/update', 'App\Http\Controllers\UserController@update');
    Route::post('/api/user/upload', 'App\Http\Controllers\UserController@upload');

