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
//VERIFICACION
Route::name('verification.notice')->get('email/verify', 'VerifyMailController@index');
Route::get('reenvioCorreo', 'VerifyMailController@verificarReenvio')->name('reenvioCorreo');
Route::get('comprobarCodigo', 'ComprobarSmsController@comprobar');
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
Route::get('register/verify/{code}', 'GuestController@verify');

//ORGANIZACION

Route::get('registro/organizacion/{user1}', 'registroEmpresaController@index')->name('registroorganizacion');
Route::post('organizacion/store', 'registroEmpresaController@registrarDatos')->name('organizacion');
Route::POST('organizacion/create', 'registroEmpresaController@create')->name('registerOrganizacion');
Route::POST('/organizacion/busquedaRuc', 'registroEmpresaController@busquedaRuc');

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
Route::get('tablaempleado/ver', 'EmpleadoController@tabla')->middleware('auth');
Route::get('empleado/show', 'EmpleadoController@show')->middleware('auth');
Route::post('/empleadoA/{idE}', 'EmpleadoController@update');
Route::post('/empleado/eliminar', 'EmpleadoController@destroy');
Route::post('/eliminarFoto/{v_id}', 'EmpleadoController@eliminarFoto');
Route::delete('/eliminarEmpleados', 'EmpleadoController@deleteAll');
Route::get('numDoc', 'EmpleadoController@comprobarNumD');
Route::get('email', 'EmpleadoController@comprobarCorreo');
Route::get('emailE', 'EmpleadoController@comprobarCorreoEditar');

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
Route::get('/tareas', 'ControlController@index');
Route::get('/tareas/show', 'ControlController@show');
Route::get('/tareas/proyecto', 'ControlController@proyecto');
Route::get('/reporteSemanal', 'ControlController@reporteS');
Route::get('/reporte/empleado', 'ControlController@EmpleadoReporte');


//probando excel
Route::get('/export', 'MyController@export')->name('export');
Route::get('importExportView', 'MyController@importExportView');
Route::post('import', 'MyController@import')->name('import');

//EXCEL EMPLEADO
Route::post('/importEmpleado', 'excelEmpleadoController@import')->name('importEmpleado');
Route::post('/importBDExcel', 'excelEmpleadoController@guardarBD');
//PROYECTO
Route::get('/proyecto', 'ProyectoController@index');
Route::post('/proyecto/registrar', 'ProyectoController@store');
Route::post('/proyecto/proyectoV', 'ProyectoController@proyectoV');
Route::post('/proyecto/registrarPrEm', 'ProyectoController@registrarPrEm');
Route::post('/proyecto/selectValidar', 'ProyectoController@selectValidar');
Route::post('/proyecto/eliminar', 'ProyectoController@eliminar');
Route::post('/proyecto/tablaEmpleados', 'ProyectoController@empleadosTabla');
Route::post('/proyecto/eliminarEmpleado', 'ProyectoController@eliminarEmpleado');
Route::post('/proyecto/editarPro', 'ProyectoController@editarProyecto');

//carga masiva de fotos
Route::post('/subirfoto', 'CargaMasivaFotoController@subirfoto');

//HORARIO
Route::get('/horario', 'horarioController@index');
Route::post('/horarioVerTodEmp', 'horarioController@verTodEmpleado');
Route::post('/guardarEventos', 'horarioController@guardarEventos');
Route::get('/eventosHorario', 'horarioController@eventos');
Route::post('/guardarHorario', 'horarioController@guardarHorarioBD');
Route::get('tablahorario/ver', 'horarioController@tablaHorario');
Route::post('/verDataEmpleado', 'horarioController@verDataEmpleado');
Route::get('/vaciartemporal', 'horarioController@vaciartemporal');
Route::get('/copiarEventos', 'horarioController@copiarEventos');
Route::post('/horario/confirmarDepartamento', 'horarioController@confirmarDepartamento');
Route::get('/empleadoIncHorario', 'horarioController@empleadosIncidencia');
Route::post('/registrarInci', 'horarioController@registrarIncidencia');
Route::post('/eliminarHora', 'horarioController@eliminarHora');
Route::post('/cambiarEstado', 'horarioController@cambiarEstado');
Route::post('/storeDescanso', 'horarioController@storeDescanso');
Route::post('/storeLaborable', 'horarioController@storeLabor');
Route::post('/storeNoLaborable', 'horarioController@storeNoLabor');
Route::post('/storeIncidencia', 'horarioController@storeIncidencia');
Route::get('/vaciarhor', 'horarioController@vaciarhor');
Route::get('/vaciardl', 'horarioController@vaciardl');
Route::get('/vaciarndl', 'horarioController@vaciarndl');
Route::post('/guardarHorarioC', 'horarioController@guardarHorarioC');
Route::post('/eliminarHorarBD', 'horarioController@eliminarHorarBD');
Route::post('/eliminarIncidBD', 'horarioController@eliminarIncidBD');
Route::post('/storeIncidenciaEmpleado', 'horarioController@storeIncidenciaEmpleado');
Route::post('/storeHorarioEmBD', 'horarioController@storeHorarioEmBD');
Route::post('/storeLaborHorarioBD', 'horarioController@storeLaborHorarioBD');
Route::post('/storeNoLaborHorarioBD', 'horarioController@storeNoLaborHorarioBD');
Route::get('/horario/incidenciatemporal', 'horarioController@incidenciatemporal');
Route::post('/eliminarinctempotal', 'horarioController@eliminarinctempotal');
Route::post('/verDatahorario', 'horarioController@verDatahorario');
Route::post('/horario/actualizarhorario', 'horarioController@actualizarhorarioed');

//DASHBOARD
Route::get('/eventosU', 'dashboardController@eventosUsuario');
Route::get('/totalA', 'dashboardController@area');
Route::get('/totalN', 'dashboardController@nivel');
Route::get('/totalC', 'dashboardController@contrato');
Route::get('/totalCC', 'dashboardController@centro');
Route::get('/totalL', 'dashboardController@local');
Route::get('/totalDepartamento', 'dashboardController@departamento');
Route::get('/totalE', 'dashboardController@edad');
Route::get('/totalRE', 'dashboardController@rangoE');

//MENU
Route::get('/empleados', 'EmpleadoController@indexMenu');
Route::get('/calendarios', 'calendarioController@indexMenu');
Route::get('/horarios', 'horarioController@indexMenu');

//CORREO EMPLEADO
Route::get('empleadoCorreo', 'correosEmpleadoController@encode');
Route::get('comprobR', 'correosEmpleadoController@reenvio');
Route::get('envioMasivo', 'correosEmpleadoController@encodeMasivo');
Route::get('asignarEscritorio', 'correosEmpleadoController@nuevoEncode');
Route::get('ambasPlataformas', 'correosEmpleadoController@ambasPlataformas');
//ANDROID
Route::get('empleadoAndroid', 'correosEmpleadoController@envioA');
Route::get('empleadoAndroidMasivo', 'correosEmpleadoController@envioAndroidM');
//DOWNLOAD
Route::get('download/{code}', 'downloadController@download');
///verif
Route::get('check-session', 'verificarUsuarioController@checkSession');

//EDITAR ATRIBUTOS
//*****AREA */
Route::get('area', 'editarAtributosController@area');
Route::get('buscarArea', 'editarAtributosController@buscarArea');
Route::post('editarArea', 'editarAtributosController@editarArea');
//*****CARGO */
Route::get('cargo', 'editarAtributosController@cargo');
Route::get('buscarCargo', 'editarAtributosController@buscarCargo');
Route::post('editarCargo', 'editarAtributosController@editarCargo');
//******CENTRO***/
Route::get('centro', 'editarAtributosController@centro');
Route::get('buscarCentro', 'editarAtributosController@buscarCentro');
Route::post('editarCentro', 'editarAtributosController@editarCentro');
//******LOCAL***/
Route::get('local', 'editarAtributosController@local');
Route::get('buscarLocal', 'editarAtributosController@buscarLocal');
Route::post('editarLocal', 'editarAtributosController@editarLocal');
//******NIVEL***/
Route::get('nivel', 'editarAtributosController@nivel');
Route::get('buscarNivel', 'editarAtributosController@buscarNivel');
Route::post('editarNivel', 'editarAtributosController@editarNivel');
//******CONTRATO***/
Route::get('contrato', 'editarAtributosController@contrato');
Route::get('buscarContrato', 'editarAtributosController@buscarContrato');
Route::post('editarContrato', 'editarAtributosController@editarContrato');
//*************************************************************************/
//ESTADO LICENCIA
Route::get('cambiarEstadoLicencia', 'detallesActivacionController@cambiarEstadoLicencia');
