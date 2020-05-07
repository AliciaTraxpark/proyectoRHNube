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

Auth::routes();

//PERSONA

Route::get('/home', 'HomeController@index')->name('home');
//persona
 Route::get('registro/persona', 'registroPController@index')->name('registroPersona');
Route::post('/persona/store','registroPController@registrarDatos')->name('persona');
Route::POST('persona/create', 'RegistroPController@create')->name('registerPersona');


//ORGANIZACION

Route::get('registro/organizacion', 'registroEmpresa@index')->name('registroorganizacion');
Route::post('organizacion/store','registroEmpresa@registrarDatos')->name('organizacion');
Route::POST('organizacion/create', 'registroEmpresa@create')->name('registerOrganizacion');
