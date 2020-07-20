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

Route::get('/departamento/{id}/niveles', 'registroEmpresaController@provincias');
Route::get('/provincia/{id}/niveles', 'registroEmpresaController@distritos');

Route::get('/departamento/{id}/niveles', 'EmpleadoController@provincias');
Route::get('/provincia/{id}/niveles', 'EmpleadoController@distritos');

Route::get("empleado", "apiController@api");
Route::post("tarea", "apiController@apiTarea")->middleware('token');
Route::post("actividad", "apiController@apiActividad")->middleware('token');
Route::post("logueo", "apiController@logueoEmpleado");
Route::post("editarTarea", "apiController@editarApiTarea")->middleware('token');
Route::post("editarActividad", "apiController@editarApiActividad")->middleware('token');
Route::post("envio", "apiController@envio")->middleware('token');
Route::post("control", "apiController@control")->middleware('token');
Route::post("captura", "apiController@captura")->middleware('token');
Route::post("proyecto", "apiController@selectProyecto")->middleware('token');
Route::post("agregarProyecto", "apiController@agregarProyecto")->middleware('token');
Route::post("editarProyecto", "apiController@editarProyecto")->middleware('token');
Route::post("eliminarProyecto", "apiController@eliminarProyecto")->middleware('token');
//HORARIO
Route::post("horario", "apiController@horario")->middleware('token');
///LOGUEO CON CORREO
Route::post("logueoV", "apiController@verificacion");

//LICENCIA
Route::post("licencia", "apiController@licenciaProducto");
