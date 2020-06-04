<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/departamento/{id}/niveles','registroEmpresaController@provincias');
Route::get('/provincia/{id}/niveles','registroEmpresaController@distritos');

Route::get('/departamento/{id}/niveles','EmpleadoController@provincias');
Route::get('/provincia/{id}/niveles','EmpleadoController@distritos');

Route::get("empleado","EmpleadoController@api");
Route::post("logueo","EmpleadoController@logueoEmpleado");
Route::post("control","EmpleadoController@apiControl");