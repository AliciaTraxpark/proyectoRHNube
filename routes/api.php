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
//API
Route::middleware('apilogger')->post('/test', function () {
    return response()->json("test");
});
Route::get('/departamento/{id}/niveles', 'registroEmpresaController@provincias');
Route::get('/provincia/{id}/niveles', 'registroEmpresaController@distritos');

Route::get('/departamento/{id}/niveles', 'EmpleadoController@provincias');
Route::get('/provincia/{id}/niveles', 'EmpleadoController@distritos');

Route::get("empleado", "apiController@api");
Route::post("tarea", "apiController@apiTarea")->middleware('token')->middleware('apilogger');
Route::post("logueo", "apiController@logueoEmpleado")->middleware('apilogger');
Route::post("editarTarea", "apiController@editarApiTarea")->middleware('token')->middleware('apilogger');
Route::post("control", "apiController@control")->middleware('token')->middleware('apilogger');
Route::post("captura", "apiController@captura")->middleware('token')->middleware('apilogger');
// PROYECTO
Route::post("proyecto", "apiController@selectProyecto")->middleware('token')->middleware('apilogger');
Route::post("agregarProyecto", "apiController@agregarProyecto")->middleware('token')->middleware('apilogger');
Route::post("editarProyecto", "apiController@editarProyecto")->middleware('token')->middleware('apilogger');
Route::post("eliminarProyecto", "apiController@cambiarEstadoProyecto")->middleware('token')->middleware('apilogger');
// ACTIVIDAD
Route::post("actividad", "apiController@selectActividad")->middleware('token')->middleware('apilogger');
Route::post("actividadesEliminadas", "apiController@selectActividadEliminada")->middleware('token')->middleware('apilogger');
Route::post("agregarActividad", "apiController@apiActividad")->middleware('token')->middleware('apilogger');
Route::post("editarActividad", "apiController@editarApiActividad")->middleware('token')->middleware('apilogger');
Route::post("editarEstadoActividad", "apiController@editarEstadoApiActividad")->middleware('token')->middleware('apilogger');
Route::post("eliminarActividad", "apiController@cambiarEstadoActividad")->middleware('token')->middleware('apilogger');
//HORARIO
Route::post("horario", "apiController@horario")->middleware('token')->middleware('apilogger');
Route::post("ultimoHorario", "apiController@ultimoHorario")->middleware('token')->middleware('apilogger');
///LOGUEO CON CORREO
Route::post("logueoV", "apiController@verificacion")->middleware('apilogger');

//LICENCIA
Route::post("licencia", "apiController@licenciaProducto")->middleware('apilogger');

//SEGUNDA VERSION
Route::post("listaActividad", "apiVersionDosController@selectActividad")->middleware('token')->middleware('apilogger');
Route::post("captura2", "apiVersionDosController@captura")->middleware('token')->middleware('apilogger');
Route::post("capturaArray", "apiVersionDosController@capturaArray")->middleware('token')->middleware('apilogger');
Route::post("actividad2", "apiVersionDosController@actividad")->middleware('token')->middleware('apilogger');

// TICKET DE SOPORTE Y SUGERENCIA
Route::post("ticketSoporte","apiVersionDosController@ticketSoporte")->middleware('token')->middleware('apilogger');

//API MOVILES
Route::post("verificacionMovil","apimovilController@apiActivacion")->middleware('apilogger');
Route::post("EmpleadoMovil","apimovilController@EmpleadoMovil")->middleware('token')->middleware('apilogger');
