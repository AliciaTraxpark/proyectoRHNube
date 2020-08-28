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
//EDITAR PERFIL
Route::get('perfil', 'editarPerfilController@index');
Route::get('perfilMostrar', 'editarPerfilController@show');
Route::post('editarUser', 'editarPerfilController@actualizarDP');
Route::post('editarEmpresa', 'editarPerfilController@actualizarDE');
Route::post('foto', 'editarPerfilController@actualizarFoto');
Route::post('/perfil/cambiarCont', 'editarPerfilController@cambiarCont');
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
Route::post('/calendarioe', 'calendarioController@destroy');
Route::get('eliminarE/{id}', 'EventosUsuarioController@destroy');
Route::post('/calendario/registrarnuevo', 'calendarioController@registrarnuevo');
Route::post('/calendario/cargarcalendario', 'calendarioController@cargarcalendario');
Route::post('/calendario/verificarID', 'calendarioController@verificarID');
Route::post('/calendario/copiarevenEmpleado', 'calendarioController@copiarevenEmpleado');

//PERSONA


//persona
Route::get('registro/persona', 'registroPController@index')->name('registroPersona');
Route::get('registroInvitado/{idinEncr}', 'delegarInvController@vistaRegistroInv');
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

/*
Route::get('/eventos_usuario/store', 'EventosUsuarioController@store'); */

//EMPLEADOS
Route::post('/empleado/store', 'EmpleadoController@store');
Route::post('/empleado/storeEmpleado/{idE}', 'EmpleadoController@storeEmpleado');
Route::post('/empleado/storeEmpresarial/{idE}', 'EmpleadoController@storeEmpresarial');
Route::post('/empleado/storeFoto/{idE}', 'EmpleadoController@storeFoto');
Route::post('/empleado/storeCalendario/{idE}', 'EmpleadoController@storeCalendario');
Route::post('/empleado/storeHorario/{idE}', 'EmpleadoController@storeHorario');
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
Route::get('numDocStore', 'EmpleadoController@comprobarNumDocumentoStore');
Route::get('email', 'EmpleadoController@comprobarCorreo');
Route::get('emailE', 'EmpleadoController@comprobarCorreoEditar');
Route::post('/empleado/calendarioEmpTemp', 'EmpleadoController@calendarioEmpTemp');
Route::post('/empleado/storeCalendarioTem', 'EmpleadoController@storeCalendarioTem');
Route::post('/empleado/storeIncidTem', 'EmpleadoController@storeIncidTem');
Route::get('/empleado/vaciarcalend', 'EmpleadoController@vaciarcalend');
Route::post('/empleado/vaciarcalendId', 'EmpleadoController@vaciarcalendId');
Route::post('/empleado/registrarHorario', 'EmpleadoController@registrarHorario');
Route::post('/empleado/guardarhorarioTem', 'EmpleadoController@guardarhorarioTem');
Route::post('/empleado/vercalendario', 'EmpleadoController@vercalendarioEmpl');
Route::post('/empleado/calendarioEditar', 'EmpleadoController@calendarioEditar');
Route::post('/empleado/eliminarEte', 'EmpleadoController@eliminarEte');
Route::post('/empleado/calendarioEmpleado', 'EmpleadoController@calendarioEmp');
Route::post('/empleado/vaciarcalendempleado', 'EmpleadoController@vaciarcalendempleado');
Route::post('/empleado/storeCalendarioempleado', 'EmpleadoController@storeCalendarioempleado');
Route::post('/empleado/storeIncidempleado', 'EmpleadoController@storeIncidempleado');
Route::post('/empleado/guardarhorarioempleado', 'EmpleadoController@guardarhorarioempleado');
Route::post('/empleado/vaciardfTem', 'EmpleadoController@vaciardfTem');
Route::get('/empleado/vaciardlabTem', 'EmpleadoController@vaciardlabTem');
Route::post('/empleado/vaciardNlabTem', 'EmpleadoController@vaciardNlabTem');
Route::post('/empleado/vaciardIncidTem', 'EmpleadoController@vaciardIncidTem');
Route::post('/empleado/vaciardescansoTem', 'EmpleadoController@vaciardescansoTem');
Route::post('/empleado/vaciarhorarioTem', 'EmpleadoController@vaciarhorarioTem');
Route::post('/empleado/eliminareventBD', 'EmpleadoController@eliminareventBD');
Route::post('/empleado/eliminarHorariosEdit', 'EmpleadoController@eliminarHorariosEdit');
Route::post('/empleado/eliminarInciEdit', 'EmpleadoController@eliminarInciEdit');
Route::post('/empleado/vaciarFerBD', 'EmpleadoController@vaciarFerBD');
Route::post('/empleado/vaciarFdescansoBD', 'EmpleadoController@vaciarFdescansoBD');
Route::post('/empleado/vaciardnlaBD', 'EmpleadoController@vaciardnlaBD');
Route::post('/empleado/vaciarincidelaBD', 'EmpleadoController@vaciarincidelaBD');
Route::post('/empleado/eliminarhorariosBD', 'EmpleadoController@eliminarhorariosBD');
Route::post('/empleado/vaciarbdempleado', 'EmpleadoController@vaciarbdempleado');
Route::post('/empleado/vaciarhorariosBD', 'EmpleadoController@vaciarhorariosBD');
Route::post('/empleado/cambiarEstado', 'EmpleadoController@cambiarEstadoEmp');

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

// CONDICION DE PAGO
Route::post('/registrar/condicion', 'condicionPagoController@store');

//TAREAS
Route::get('/tareas', 'ControlController@index');
Route::get('/tareas/show', 'ControlController@show');
Route::get('/tareas/empleadoR', 'ControlController@empleadoRefresh');
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
Route::get('/actividadEmpleado', 'ProyectoController@actividadesEmpleado');
Route::get('/registrarActvE', 'ProyectoController@registrarActividadE');
Route::get('/editarActvE', 'ProyectoController@editarActividadE');
Route::get('/editarEstadoA', 'ProyectoController@editarEstadoActividad');

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
Route::post('/horario/verificarID', 'horarioController@verificarID');
Route::post('/horario/eliminarHorario', 'horarioController@eliminarHorario');
Route::post('/horario/empleArea', 'horarioController@empleArea');
Route::post('/horario/empleCargo', 'horarioController@empleCargo');
Route::post('/horario/empleLocal', 'horarioController@empleLocal');
Route::post('/horario/copiarferiados', 'horarioController@copiarferiados');
Route::post('/horario/borrarferiados', 'horarioController@borrarferiados');
//DASHBOARD
Route::get('/respuestaC', 'dashboardController@respuestaCalendario');
Route::get('/eventosU', 'dashboardController@eventosUsuario');
Route::get('/totalA', 'dashboardController@area');
Route::get('/totalN', 'dashboardController@nivel');
Route::get('/totalC', 'dashboardController@contrato');
Route::get('/totalCC', 'dashboardController@centro');
Route::get('/totalL', 'dashboardController@local');
Route::get('/totalDepartamento', 'dashboardController@departamento');
Route::get('/totalE', 'dashboardController@edad');
Route::get('/totalRE', 'dashboardController@rangoE');
Route::get('/horarioU', 'dashboardController@horarioDias');

//MENU
Route::get('/empleados', 'EmpleadoController@indexMenu');
Route::get('/calendarios', 'calendarioController@indexMenu');
Route::get('/horarios', 'horarioController@indexMenu');
Route::get('/dias/laborales', 'diasLaborablesController@indexMenu');
//VINCULACION
Route::get('vinculacionAndroid', 'vinculacionDispositivoController@vinculacionAndroid');
Route::get('vinculacionWindows', 'vinculacionDispositivoController@vinculacionWindows');
//WINDOWS
Route::get('correoWindows', 'correosEmpleadoController@envioWindows');
Route::get('envioMasivoW', 'correosEmpleadoController@envioMasivoWindows');
Route::get('ambasPlataformas', 'correosEmpleadoController@ambasPlataformas');
//ANDROID
Route::get('correoAndroid', 'correosEmpleadoController@envioAndroid');
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
// **********CONDICION DE PAGO*****/
Route::get('condicion', 'editarAtributosController@condicion');
Route::get('buscarCondicion', 'editarAtributosController@buscarCondicion');
Route::post('editarCondicion', 'editarAtributosController@editarCondicion');
//*************************************************************************/
//ESTADO LICENCIA
Route::get('cambiarEstadoLicencia', 'detallesActivacionController@cambiarEstadoLicencia');

//laborales
Route::post('/dias/storeCalendario', 'diasLaborablesController@storeCalendario');
Route::post('/dias/delete', 'diasLaborablesController@eliminarBD');
Route::post('/dias/diasIncidempleado', 'diasLaborablesController@diasIncidempleado');
///errores
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

//NOTIFICACIONES USER
Route::get('/notificacionesUser', 'NotificacionController@notificacionesUsuario');
Route::get('/leerNotificaciones', 'NotificacionController@cambiarestadoNotificacion');
Route::get('/showNotificaciones', 'NotificacionController@showNotificaciones');

//////delegar
Route::get('/delegarcontrol', 'delegarInvController@index');
Route::post('/empleAreaIn', 'delegarInvController@empleAreaIn');
Route::post('/registrarInvitado', 'delegarInvController@registrarInvitado');
Route::post('/registroinvitadoBD', 'delegarInvController@registroInvitado');
