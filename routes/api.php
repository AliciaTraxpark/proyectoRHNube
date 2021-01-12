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
//* SEGUNDA VERSION
Route::post("logueoV2", "apiVersionDosController@verificacion")->middleware('apilogger');
Route::post("listaActividad", "apiVersionDosController@selectActividad")->middleware('token')->middleware('apilogger');
Route::post("captura2", "apiVersionDosController@captura")->middleware('token')->middleware('apilogger');
Route::post("capturaArray", "apiVersionDosController@capturaArray")->middleware('token')->middleware('apilogger');
Route::post("actividad2", "apiVersionDosController@actividad")->middleware('token')->middleware('apilogger');
Route::get("downloadUpdate", "apiVersionDosController@downloadActualizacion")->middleware('apilogger');
Route::post("horario2", "apiVersionDosController@horario")->middleware('token')->middleware('apilogger');
Route::post("horario3", "apiVersionDosController@horarioV2")->middleware('token')->middleware('apilogger');
Route::get("logout", "apiVersionDosController@logoutToken")->middleware('token')->middleware('apilogger');
Route::get("updatex64", "apiVersionDosController@downloadActualizacionx64")->middleware('token')->middleware('apilogger');
Route::get("updateRHx64", "apiVersionDosController@updteDonwloand64")->middleware('apilogger');
//? MEJORAS DE LOGIN
Route::post("logueoV3", "apiVersionDosController@verificacionLogin")->middleware('apilogger');

//? TICKET DE SOPORTE Y SUGERENCIA
Route::post("ticketSoporte", "apiVersionDosController@ticketSoporte")->middleware('token')->middleware('apilogger');

//? TIEMPO DEL SERVIDOR
Route::get("tiempoRHbox", "apiVersionDosController@horaServidor")->middleware('token')->middleware('apilogger');
Route::post("tiempoEmpleadoRHbox", "apiVersionDosController@tiempoEmpleado")->middleware('token')->middleware('apilogger');

//* API MOVILES
Route::post("verificacionMovil", "apimovilController@apiActivacion")->middleware('apilogger');
Route::post("EmpleadoMovil", "apimovilController@EmpleadoMovil")->middleware('token')->middleware('apilogger');
Route::post("ActividadesMovil", "apimovilController@ActivMovil")->middleware('token')->middleware('apilogger');
Route::post("controladoresAct", "apimovilController@controladoresAct")->middleware('token')->middleware('apilogger');
Route::post("marcacionMovil", "apimovilController@marcacionMovil")->middleware('token')->middleware('apilogger');
Route::post("empleadoHorario", "apimovilController@empleadoHorario")->middleware('token')->middleware('apilogger');
Route::post("ticketSoporteMovil", "apimovilController@ticketSoporte")->middleware('token')->middleware('apilogger');

// * APIS DE MODO CONTROL RUTA
Route::post("loginRuta", 'apiSeguimientoRutaContoller@login')->middleware('apilogger');
Route::post("ubicacion", 'apiSeguimientoRutaContoller@registrarRuta')->middleware('token')->middleware('apilogger');
Route::post("listaActividadCRT", "apiSeguimientoRutaContoller@listaActividad")->middleware('token')->middleware('apilogger');
Route::post("tiempo", "apiSeguimientoRutaContoller@tiempoRuta")->middleware('token')->middleware('apilogger');
Route::post("horarioRuta", "apiSeguimientoRutaContoller@horario")->middleware('token')->middleware('apilogger');
Route::post("SoporteRuta", "apiSeguimientoRutaContoller@ticketSoporte")->middleware('token')->middleware('apilogger');

// * APIS DE PUNTO DE CONTROL
Route::post("centroCostos", "apimovilController@centroCostos")->middleware('token')->middleware('apilogger');
Route::post("puntoControl", "apimovilController@puntoControl")->middleware('token')->middleware('apilogger');

// * APIS DE BIOMETRICOS
Route::post("logueoBiometrico", "apiBiometricoController@logueoBiometrico")->middleware('apilogger');
Route::post("elegirOrganizacionBio", "apiBiometricoController@elegirOrganizacionBio")->middleware('token')->middleware('apilogger');
Route::post("editarDispositivo", "apiBiometricoController@editarDispositivo")->middleware('token')->middleware('apilogger');
Route::post("marcacionBiometrico", "apiBiometricoController@marcacionBiometrico");
