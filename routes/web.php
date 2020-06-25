<?php

use Illuminate\Support\Facades\Auth;
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


Route::get('/', 'Auth\LoginController@principal', function () {
    return view('welcome');
})->name('principal');
Auth::routes(['verify' => true]);


/* Route::get('/', 'HomeController@principal')->name('principal'); */
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
//MENU
Route::name('dashboard')->get('dashboard', 'HomeController@index')->middleware('verified');
//Route::name('dashboard')->get('dashboard', 'HomeController@index')->middleware('auth');
//CALENDARIO
Route::name('calendario')->get('calendario', 'calendarioController@index');
Route::post('/calendario/store', 'calendarioController@store');
Route::get('calendario/show', 'calendarioController@show')->name('calendarioShow');
Route::get('calendario/showDep', 'calendarioController@showDep')->name('calendarioShowDep');
Route::get('calendario/showDep/confirmar', 'calendarioController@showDepconfirmar')->name('calendarioShowDepc');
Route::delete('calendario/{id}', 'calendarioController@destroy');
Route::get('eliminarE/{id}', 'EventosUsuarioController@destroy');

//PERSONA


//persona
Route::get('registro/persona', 'registroPController@index')->name('registroPersona');
Route::post('/persona/store', 'registroPController@registrarDatos')->name('persona');
Route::POST('persona/create', 'RegistroPController@create')->name('registerPersona');
Route::get('/persona/comprobar', 'registroPController@comprobar');
//
Route::get('/register/verify/{code}', 'GuestController@verify');

//ORGANIZACION

Route::get('registro/organizacion/{user1}', 'registroEmpresaController@index')->name('registroorganizacion');
Route::post('organizacion/store', 'registroEmpresaController@registrarDatos')->name('organizacion');
Route::POST('organizacion/create', 'registroEmpresaController@create')->name('registerOrganizacion');

//calendario_usuario
Route::post('eventos_usuario/store', 'EventosUsuarioController@store');


Route::get('/departamento', function () {
    return view('calendario.departamento');
})->name('depas');


Route::get('/eventos_usuario/store', 'EventosUsuarioController@store');

//EMPLEADOS
Route::post('/empleado/store', 'EmpleadoController@store');
Route::get('/empleado', 'EmpleadoController@index');
Route::get('/empleado/cargar', 'EmpleadoController@cargarDatos');
Route::post('/empleado/file', 'EmpleadoController@upload');
Route::get('tablaempleado/ver', 'EmpleadoController@tabla');
Route::get('empleado/show', 'EmpleadoController@show');
Route::post('/empleadoA/{idE}', 'EmpleadoController@update');
Route::post('/empleado/eliminar', 'EmpleadoController@destroy');
Route::post('/eliminarFoto/{v_id}', 'EmpleadoController@eliminarFoto');
Route::delete('/eliminarEmpleados', 'EmpleadoController@deleteAll');

//AREA
Route::post('/registrar/area', 'areaController@store');

//CARGO
Route::post('/registrar/cargo', 'cargoController@store');

//CENTRO
Route::post('/registrar/centro', 'centrocostoController@store');

//LOCAL
Route::post('/registrar/local', 'localController@store');

//NIVEL
Route::post('/registrar/nivel', 'nivelController@store');

//CONTRATO
Route::post('/registrar/contrato', 'contratoController@store');

//TAREAS
Route::get('/tareas', 'ControlController@index')->middleware('auth');
Route::get('/tareas/show', 'ControlController@show')->middleware('auth');
Route::get('/tareas/proyecto', 'ControlController@proyecto')->middleware('auth');
Route::get('/reporteSemanal', 'ControlController@reporteS')->middleware('auth');
Route::get('/reporte/empleado', 'ControlController@EmpleadoReporte')->middleware('auth');


//probando excel
Route::get('/export', 'MyController@export')->name('export');
Route::get('importExportView', 'MyController@importExportView');
Route::post('import', 'MyController@import')->name('import');

//EXCEL EMPLEADO
Route::post('/importEmpleado', 'excelEmpleadoController@import')->name('importEmpleado');
//PROYECTO
Route::get('/proyecto', 'ProyectoController@index');
Route::post('/proyecto/registrar', 'ProyectoController@store');
Route::post('/proyecto/proyectoV', 'ProyectoController@proyectoV');
Route::post('/proyecto/registrarPrEm', 'ProyectoController@registrarPrEm');
Route::post('/proyecto/selectValidar', 'ProyectoController@selectValidar');

//carga masiva de fotos
Route::post('/subirfoto', 'CargaMasivaFotoController@subirfoto');

//HORARIO
Route::get('/horario', 'horarioController@index');
Route::post('/horarioVerTodEmp', 'horarioController@verTodEmpleado');
Route::post('/guardarEventos', 'horarioController@guardarEventos');
Route::get('/eventosHorario', 'horarioController@eventos');
Route::post('/guardarEventosBD', 'horarioController@guardarEventosBD');
Route::get('tablahorario/ver', 'horarioController@tablaHorario');
Route::post('/verDataEmpleado', 'horarioController@verDataEmpleado');
Route::get('/vaciartemporal', 'horarioController@vaciartemporal');
Route::post('/horario/confirmarDepartamento', 'horarioController@confirmarDepartamento');
Route::get('/empleadoIncHorario', 'horarioController@empleadosIncidencia');
Route::post('/registrarInci', 'horarioController@registrarIncidencia');

//DASHBOARD
Route::get('/totalA', 'dashboardController@area');
Route::get('/totalN', 'dashboardController@nivel');
Route::get('/totalC', 'dashboardController@contrato');
Route::get('/totalCC', 'dashboardController@centro');
Route::get('/totalL', 'dashboardController@local');
Route::get('/totalDepartamento', 'dashboardController@departamento');
Route::get('/totalE', 'dashboardController@edad');
Route::get('/totalRE', 'dashboardController@rangoE');
