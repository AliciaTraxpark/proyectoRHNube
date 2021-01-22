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

//PASSWORD RESET
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

/* Route::get('/', 'HomeController@principal')->name('principal'); */
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
//MENU
Route::name('dashboard')->get('dashboard', 'HomeController@index')->middleware('verified');
//Route::name('dashboard')->get('dashboard', 'HomeController@index')->middleware('auth');
Route::name('elegirorganizacion')->get('/elegirorganizacion', 'HomeController@elegirEmpresa');
Route::post('/enviarIDorg', 'HomeController@enviarIDorg');
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
Route::get('calendario/show', 'calendarioController@show')->name('calendarioShow');
Route::get('calendario/showDep', 'calendarioController@showDep')->name('calendarioShowDep');
Route::get('calendario/showDep/confirmar', 'calendarioController@showDepconfirmar')->name('calendarioShowDepc');
Route::post('/calendarioe', 'calendarioController@destroy');
Route::get('eliminarE/{id}', 'EventosUsuarioController@destroy');
Route::post('/calendario/registrarnuevo', 'calendarioController@registrarnuevo');
Route::post('/calendario/registrarnuevoClonado', 'calendarioController@registrarnuevoClonado');
Route::post('/calendario/cargarcalendario', 'calendarioController@cargarcalendario');
Route::post('/calendario/verificarID', 'calendarioController@verificarID');
Route::post('/calendario/copiarevenEmpleado', 'calendarioController@copiarevenEmpleado');
Route::post('/calendario/mostrarFCalend', 'calendarioController@mostrarFCalend');
Route::post('/calendario/añadirFinCalenda', 'calendarioController@añadirFinCalenda');
Route::post('/calendario/listaEmplCa', 'calendarioController@listaEmplCa');
Route::post('/calendario/asignarCalendario', 'calendarioController@asignarCalendario');
Route::post('/calendario/seleccionados', 'calendarioController@empSeleccionados');
Route::post('/calendario/yearCale', 'calendarioController@yearCale');
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
Route::post('/empleado/storeDocumentoBaja/{data}', 'EmpleadoController@storeDocumentoBaja');
Route::post('/empleado/storeCalendario/{idE}', 'EmpleadoController@storeCalendario');
Route::post('/empleado/storeHorario/{idE}', 'EmpleadoController@storeHorario');
Route::get('/empleado', 'EmpleadoController@index');
Route::get('/empleado/cargar', 'EmpleadoController@cargarDatos');
Route::post('/empleado/file', 'EmpleadoController@upload');
Route::get('tablaempleado/ver', 'EmpleadoController@tabla')->middleware('auth');
Route::get('empleado/show', 'EmpleadoController@show')->middleware('auth');
Route::post('/empleadoA/{idE}', 'EmpleadoController@update');
Route::post('/empleadoAE/{idE}', 'EmpleadoController@updateEmpresarial');
Route::post('/empleadoAF/{idE}', 'EmpleadoController@updateFoto');
Route::post('/empleado/eliminar', 'EmpleadoController@destroy');
Route::post('/eliminarFoto/{v_id}', 'EmpleadoController@eliminarFoto');
Route::post('/eliminarEmpleado', 'EmpleadoController@bajaEmpleado');
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
Route::get('tablaempleado/refresh', 'EmpleadoController@refresTabla');
Route::get('/empleado/agregarCorreo', 'EmpleadoController@agregarCorreoE');
Route::get('/empleado/agregarCelular', 'EmpleadoController@agregarCelularE');
Route::post('/empleado/asisPuerta', 'EmpleadoController@asisPuerta');
Route::post('/empleado/modoTareo', 'EmpleadoController@modoTareo');
Route::post('tablaempleado/refreshArea', 'EmpleadoController@refresTablaAre');
Route::get('/empleadosdeBaja', 'EmpleadoController@empleadosBaja')->middleware('auth');
Route::get('tablaempleado/refreshBaja', 'EmpleadoController@refresTablaEmpBaja');
Route::post('tablaempleado/refreshAreaBaja', 'EmpleadoController@refresTablaAreBaja');
Route::post('empleado/darAlta', 'EmpleadoController@darAltaEmpleado');
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

//* HISTORIAL DE CONTRATO
Route::post('/empleado/historial', 'contratoController@historialEmpleado');
Route::post('/bajaHistorial', 'contratoController@bajaHistorialEmpleado');
Route::get('/detalleC', 'contratoController@detallesContrato');
Route::post('/editDetalleC', 'contratoController@editarContrato');
Route::post('/archivosEditC/{id}', 'contratoController@agregarArchivosEdit');
Route::post('/nuevaAlta', 'contratoController@nuevaAlta');
Route::post('/eliminarHistorialC', 'contratoController@eliminarContrato');
Route::post('/dataHistorialE', 'contratoController@dataHistorialEmpleado');
Route::post('/nuevoDC', 'contratoController@nuevoDetalleC');
Route::post('/validFechaDetalle', 'contratoController@validacionFechaInicioDetalle');
Route::post('/validFechaAlta', 'contratoController@validacionFechaInicio');
//TAREAS
Route::get('/tareas', 'ControlController@index');
Route::get('/tareas/show', 'ControlController@show');
Route::get('/tareas/empleadoR', 'ControlController@empleadoRefresh');
Route::get('/tareas/proyecto', 'ControlController@proyecto');
Route::get('/reporteSemanal', 'ControlController@reporteS');
Route::get('/reporteMensual', 'ControlController@reporteM');
Route::get('/reporte/empleado', 'ControlController@EmpleadoReporte');
Route::get('/empleadosRep', 'ControlController@empledosReporteSemanalMensual');
Route::get('/mostrarCapturas', 'ControlController@mostrarCapturas');
//FUNCION PARA MOSTRAR CAPTURAS Y MINIATURAS
Route::get("mostrarMiniatura/{url}", "ControlController@apiMostrarCapturas");

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
// ACTIVIDADES
Route::get('/actividadEmpleado', 'ActividadesController@actividadesEmpleado');
Route::post('/registrarActvO', 'ActividadesController@registrarActividadE');
Route::get('/editarActvE', 'ActividadesController@editarActividadE');
Route::get('/editarEstadoA', 'ActividadesController@editarEstadoActividad');
Route::get('/actividad', 'ActividadesController@actividades');
Route::get('/actividadOrg', 'ActividadesController@actividadesOrganizaciones');
Route::post('/estadoActividadControl', 'ActividadesController@cambiarEstadoActividadControl');
Route::get('/estadoActividad', 'ActividadesController@cambiarEstadoActividad');
Route::get('/actividadOrga', 'ActividadesController@asignarActividadesE');
Route::post('/registrarAE', 'ActividadesController@registrarActividadEmpleado');
Route::post('/editarA', 'ActividadesController@editarActividad');
Route::get('/registrarEditar', 'ActividadesController@editarCambios');
Route::get('/recuperarA', 'ActividadesController@recuperarActividad');
Route::get('/datosActividad', 'ActividadesController@datosActividad');
Route::get('/empleadoActivReg', 'ActividadesController@listaEmpleadoReg');
Route::get('/listaAreasE', 'ActividadesController@listaAreasEdit');
Route::get('/listActivi', 'ActividadesController@listaActividades');
Route::post('/empleadoConAreas', 'ActividadesController@empleadosConAreas');
Route::post('/asignacionActividadE', 'ActividadesController@asignacionPorAreas');
Route::get('/datosPorAsignacionE', 'ActividadesController@asignacionEmpleadoActividad');
Route::get('/datosPorAsignacionA', 'ActividadesController@asignacionAreaActividad');
//carga masiva de fotos
Route::post('/subirfoto', 'CargaMasivaFotoController@subirfoto');

//HORARIO
Route::get('/horario', 'horarioController@index');
Route::post('/horarioVerTodEmp', 'horarioController@verTodEmpleado');
Route::post('/guardarEventos', 'horarioController@guardarEventos');
Route::get('/eventosHorario', 'horarioController@eventos');
Route::post('/guardarHorario', 'horarioController@guardarHorarioBD');
Route::post('/nuevoHorario', 'horarioController@guardarNuevoHorario');
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
Route::post('/horario/listar', 'horarioController@horarioListar');
Route::post('eliminarPausasEnEditar', 'horarioController@eliminarPausasEnEditar');
Route::post('/eliminarPausaHorario', 'horarioController@eliminarPausaHorario');
Route::post('/pausasHorario', 'horarioController@pausasHorario');
Route::post('/editarHorario', 'horarioController@editarHorario');
Route::get('/obtenerHorarios', 'horarioController@obtenerHorarios');
//DASHBOARD
Route::get('/respuestaC', 'dashboardController@respuestaCalendario');
Route::get('/totalA', 'dashboardController@area');
Route::get('/totalN', 'dashboardController@nivel');
Route::get('/totalC', 'dashboardController@contrato');
Route::get('/totalCC', 'dashboardController@centro');
Route::get('/totalL', 'dashboardController@local');
Route::get('/totalDepartamento', 'dashboardController@departamento');
Route::get('/totalE', 'dashboardController@edad');
Route::get('/totalRE', 'dashboardController@rangoE');
Route::get('/horarioU', 'dashboardController@horarioDias');
// DASHBOARD DE CONTROL REMOTO
Route::get('/controlRemoto', 'dashboardController@dashboardCR');
Route::get('/dashboardCR', 'dashboardController@globalControlRemoto');
Route::get('/fechasDataDashboard', 'dashboardController@actividadArea');
Route::get('/fechaOD', 'dashboardController@fechaOrganizacion');
Route::get('/empleadoCR', 'dashboardController@empleadosControlRemoto');
Route::get('/areasCR', 'dashboardController@selctAreas');

//MENU
Route::get('/empleados', 'EmpleadoController@indexMenu');
Route::get('/calendarios', 'calendarioController@indexMenu');
Route::get('/horarios', 'horarioController@indexMenu');
Route::get('/dias/laborales', 'diasLaborablesController@indexMenu');
//VINCULACION
Route::get('vinculacionAndroid', 'vinculacionDispositivoController@vinculacionAndroid');
Route::get('vinculacionWindows', 'vinculacionDispositivoController@vinculacionWindows');
Route::get('vinculacionControlRemoto', 'vinculacionDispositivoController@vinculacionWindowsTabla');
Route::post('celularVinculacion', 'vinculacionDispositivoController@editarNumeroV');
Route::post('actividadVinculacion', 'vinculacionDispositivoController@editarActividadV');
Route::get('listaVW', 'vinculacionDispositivoController@listaVinculacionW');
Route::get('listaVA', 'vinculacionDispositivoController@listaVinculacionA');
//WINDOWS
Route::get('correoWindows', 'correosEmpleadoController@envioWindows');
Route::get('envioMasivoW', 'correosEmpleadoController@envioMasivoWindows');
Route::get('ambasPlataformas', 'correosEmpleadoController@ambasPlataformas');
//ANDROID
Route::get('smsAndroid', 'correosEmpleadoController@smsAndroid');
Route::get('vinculacionControlRuta', 'vinculacionDispositivoController@vinculacionAndroidTabla');
//DOWNLOAD
Route::get('download/{code}', 'downloadController@download');
Route::get('downloadx32/{code}', 'downloadController@downloadx32');
Route::get('descarga', 'downloadController@vistaPrueba');
Route::get('verificarLicencia', 'downloadController@buscarLicencia');
// Route::get('updateD', 'downloadController@downloadActualizacion');
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
Route::get('cambiarEstadoVinculacionRuta', 'detallesActivacionController@cambiarEstadoAndroid');

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
Route::post('/checkNotification', 'NotificacionController@checkNotification');
//delegar
Route::get('/delegarcontrol', 'delegarInvController@index');
Route::post('/empleAreaIn', 'delegarInvController@empleAreaIn');
Route::post('/registrarInvitado', 'delegarInvController@registrarInvitado');
Route::post('/registrarInvitadoArea', 'delegarInvController@registrarInvitadoAreas');
Route::post('/registrarInvitadoAdm', 'delegarInvController@registrarInvitadoAdm');
Route::post('/registroinvitadoBD', 'delegarInvController@registroInvitado');
Route::post('/verificaremCla', 'delegarInvController@validaremailC');
Route::post('/validaremailCInvita', 'delegarInvController@validaremailCInvita');
Route::post('/registrarEmailBD', 'delegarInvController@registrarEmailBD');
Route::post('/verificarEmaD', 'delegarInvController@verificarEmaD');
Route::post('/verificarEmaDSiEdi', 'delegarInvController@verificarEmaDSiEdi');
Route::post('/verificarInvitadoreg', 'delegarInvController@verificarInvitadoreg');
Route::post('/datosInvitado', 'delegarInvController@datosInvitado');
Route::post('/editarInviAdm', 'delegarInvController@editarInviAdm');
Route::post('/editarInviI', 'delegarInvController@editarInviI');
Route::post('/editarInviArea', 'delegarInvController@editarInviArea');
Route::post('/cambInvitadoswit', 'delegarInvController@cambInvitadoswit');
Route::post('/notificarInv', 'delegarInvController@notificarInv');
Route::post('/reenviarEmail', 'delegarInvController@reenviarEmail');
//SOPORTE POR CORREOS
Route::get('/soporte', 'soportesPorCorreoController@soporte');
Route::post('/envioTicketCorreo', 'soportesPorCorreoController@envioTicketSoporte');
Route::get('/sugerencia', 'soportesPorCorreoController@sugerencia');
Route::post('/envioSugerenciaCorreo', 'soportesPorCorreoController@envioSugerencia');
Route::post('/agendaReunion', 'soportesPorCorreoController@envioAgendaReunion')->name('agendaReunionMail');

// REPORTE PERSONALIZADO
Route::get('/reportePersonalizado', 'ControlController@vistaReporte');
Route::get('/empleadosOrg/{id}', 'ControlController@selctEmpleado');
Route::get('/empleadoPersonalizado', 'ControlController@vistaReporteEmpleado');
Route::get('/capturasPersonalizadas', 'ControlController@retornarDatos');
// TRAZABILIDAD DE CAPTURAS
Route::get('/trazabilidadCapturas', 'ControlController@vistaTrazabilidad');
Route::get('/datosCapturas', 'ControlController@capturasTrazabilidad');

//DISPOSITIVOS
Route::get('/dispositivos', 'dispositivosController@index');
Route::post('/enviarMensajePru', 'dispositivosController@enviarmensaje');
Route::post('/dispoStore', 'dispositivosController@store');
Route::post('/dispoStoreBiometrico', 'biometricoController@dispoStoreBiometrico');
Route::post('/actualizarBiometrico', 'biometricoController@actualizarBiometrico');
Route::post('/tablaDisposito', 'dispositivosController@tablaDisposit');
Route::post('/reenviarmensajeDis', 'dispositivosController@reenviarmensaje');
Route::post('/comprobarMovil', 'dispositivosController@comprobarMovil');
Route::get('/reporteAsistencia', 'dispositivosController@reporteMarcaciones');
Route::get('/reporteTablaMarca', 'dispositivosController@reporteTabla');
Route::post('/datosDispoEditar', 'dispositivosController@datosDispoEditar');
Route::post('/actualizarDispos', 'dispositivosController@actualizarDispos');
Route::post('/desactivarDisposi', 'dispositivosController@desactivarDisposi');
Route::post('/activarDisposi', 'dispositivosController@activarDisposi');
Route::post('/cambiarEntrada', 'dispositivosController@cambiarEntrada');
Route::post('/cambiarSalida', 'dispositivosController@cambiarSalida');
Route::post('/registrarNEntrada', 'dispositivosController@registrarNEntrada');
Route::post('/registrarNSalida', 'dispositivosController@registrarNSalida');
Route::get('/ReporteEmpleado', 'dispositivosController@reporteMarcacionesEmp');
Route::get('/ReporteFecha', 'dispositivosController@ReporteFecha');
Route::get('/reporteTablaEmp', 'dispositivosController@reporteTablaEmp');
Route::post('/registrarNTardanza', 'dispositivosController@registrarNTardanza');
Route::post('/editarRowEntrada', 'dispositivosController@editarRowEntrada');
//CONTROLADORES
Route::get('/controladores', 'controladoresController@index');
Route::post('/controladStore', 'controladoresController@store');
Route::post('/listaControladores', 'controladoresController@tablaControladores');
Route::post('/disposiControladores', 'controladoresController@disposiControladores');
Route::post('/datosControEditar', 'controladoresController@datosControEditar');
Route::post('/controladUpdate', 'controladoresController@controladUpdate');
// PRECIOS
Route::get('/planes', 'PrecioPlanesController@vistaPrecios');
Route::get('/plan', 'PrecioPlanesController@vistaMovil');

//RUTAS SUPERADMIN
Route::get('/superadmin', 'superAdmController@indexDashboard');
Route::post('/sAdminDaOrga', 'superAdmController@datosOrgani');
Route::post('/sAdmintipoOrg', 'superAdmController@tipoOrg');
Route::get('/organizaciones', 'OrganizacionesController@index');
Route::post('/listaoOrganiS', 'OrganizacionesController@listaOrganizaciones');
Route::post('/activacionOrg', 'OrganizacionesController@activacionOrg');
Route::post('/superAdUsuario', 'OrganizacionesController@superAdUsuario');
// BIBLIOTECA
Route::get('/biblioteca', 'bibliotecaController@vista');

Route::get('/politicas', function () {
    return view('politicas');
})->name('politicas');

// ? MODO CONTROL EN RUTA
Route::get('ruta', 'controlRutaController@index');
Route::get('/tareas/showP', 'controlRutaController@showConRuta');
Route::get('rutaReporte', 'controlRutaController@indexReporte');
Route::get('/reporteConRuta', 'controlRutaController@reporte');
// * ***************REPORTE PERSONALIZADO DE RUTA*********************************
Route::get('/personalizadoRuta', 'controlRutaController@reportePersonalizadoRuta');
Route::post('/empleadosRutaOrg/{id}', 'controlRutaController@buscarEmpleado');
Route::post('/ubicacionesPersonalizadas', 'controlRutaController@obtenerUbicaciones');
//* *******************************************************************************
// * *************** REPORTE PERSONALIZADO DE RUTA SIN PROCESAR ********************
Route::get('/personalizadoRutaSP', 'controlRutaController@reportePersonalizadoProvicional');
Route::post('/ubicacionesPersonalizadasSP', 'controlRutaController@obtenerUbicacionesP');

//INCIDENCIAS
Route::get('/incidencias', 'incidenciasController@index');

//* PUNTOS DE CONTROL
Route::get('/puntoControl', 'PuntosControlController@index');
Route::get('/puntosControlOrg', 'PuntosControlController@puntosControlOrganizacion');
Route::post('/puntoControlData', 'PuntosControlController@puntoDeControl');
Route::post('/puntoControlxEmpleados', 'PuntosControlController@empleadosPorPuntos');
Route::post('/editPuntoControl', 'PuntosControlController@editarPuntoControl');
Route::post('/puntoControlxAreas', 'PuntosControlController@areasPorEmpleados');
Route::get('/listaPunto', 'PuntosControlController@listaPuntoControl');
Route::post('/datosPuntoC', 'PuntosControlController@datosPuntoControl');
Route::post('/asignacionPunto', 'PuntosControlController@asignacionDePuntos');
Route::get('/puntoEmpleado', 'PuntosControlController@empleadosPuntos');
Route::get('/puntoArea', 'PuntosControlController@areasPuntos');
Route::post('/registrarPuntoC', 'PuntosControlController@registrarPunto');
Route::post('/recuperarPunto', 'PuntosControlController@recuperarPunto');
Route::post('/cambiarEstadoP', 'PuntosControlController@cambiarEstadoPunto');
Route::post('/cambiarEstadoControlesP', 'PuntosControlController@cambiarEstadoActividadControl');

// * MANTENEDOR DE CENTRO COSTOS
Route::get('/centroCosto', 'centrocostoController@index');
Route::get('/centroCOrga', 'centrocostoController@listaCentroCosto');
Route::post('/idCentroCosto', 'centrocostoController@centroCosto');
Route::post('/actualizarCentroC', 'centrocostoController@actualizarCentro');
Route::get('/listaCentro', 'centrocostoController@listaCentroC');
Route::post('/empleadoCentro', 'centrocostoController@empleadosCentros');
Route::post('/asignacionCentro', 'centrocostoController@asignarCentros');
Route::get('/listaEmpleadoCC', 'centrocostoController@listaEmpleados');
Route::post('/registrarCentro', 'centrocostoController@agregarCentroC');
Route::post('/recuperarCentro', 'centrocostoController@recuperarCentro');
Route::post('/eliminarCentro', 'centrocostoController@eliminarCentro');


/* REPORTE PARA VER MARCACIONES DE BIOMETRICOS */
Route::get('/reporteBiometrico', 'biometricoController@vistaReporte');
Route::get('/empleadosOrgbio/{id}', 'biometricoController@selctEmpleado');
Route::get('/MarcacionesReporteBio', 'biometricoController@DatosReporte');


/* ---------------------MODO TAREO--------------------------------------- */
//Vista dispositivos
Route::get('/dispositivosTareo', 'DispositivoTareoController@index');
Route::post('/tablaDispositoTareo', 'DispositivoTareoController@show');
Route::post('/comprobarMovilTa', 'DispositivoTareoController@comprobarMovil');
Route::post('/dispoTareStore', 'DispositivoTareoController@store');
Route::post('/enviarMensajeTareo', 'DispositivoTareoController@enviarmensaje');
Route::post('/reenviarmensajeDisTareo', 'DispositivoTareoController@reenviarmensaje');
Route::post('/datosDispoTarEditar', 'DispositivoTareoController@edit');
Route::post('/actualizarDisposTareo', 'DispositivoTareoController@update');
Route::post('/desactivarDisposiTar', 'DispositivoTareoController@desactivarDisposi');
Route::post('/activarDisposiTar', 'DispositivoTareoController@activarDisposi');

//vista Controladores
Route::get('/controladoresTareo', 'controladores_tareoController@index');
Route::post('/listaControladoresTa', 'controladores_tareoController@show');
Route::post('/controladTaStore', 'controladores_tareoController@store');
Route::post('/datosControTaEditar', 'controladores_tareoController@edit');
Route::post('/controladTarUpdate', 'controladores_tareoController@update');
/* ------------------------------------------------------------------------ */

