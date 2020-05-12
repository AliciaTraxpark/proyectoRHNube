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


Route::get('/','Auth\LoginController@principal', function () {
    return view('welcome');
})->name('principal');
Auth::routes();


/* Route::get('/', 'HomeController@principal')->name('principal'); */
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
//MENU
Route::name('dashboard')->get('dashboard','HomeController@index');
//CALENDARIO
Route::name('calendario')->get('calendario','calendarioController@index');
Route::post('/calendario/store','calendarioController@store');
Route::get('calendario/show', 'calendarioController@show')->name('calendarioShow');
//PERSONA


//persona
Route::get('registro/persona', 'registroPController@index')->name('registroPersona');
Route::post('/persona/store','registroPController@registrarDatos')->name('persona');
Route::POST('persona/create', 'RegistroPController@create')->name('registerPersona');


//ORGANIZACION

Route::get('registro/organizacion/{user1}', 'registroEmpresaController@index')->name('registroorganizacion');
Route::post('organizacion/store','registroEmpresaController@registrarDatos')->name('organizacion');
Route::POST('organizacion/create', 'registroEmpresaController@create')->name('registerOrganizacion');

