@php
use Illuminate\Support\Facades\Auth;
use App\User;

use Illuminate\Support\Facades\DB;
@endphp

<style>
    .left-side-menu {
        background: #fafafa;
        width: 260px;
    }

    #sidebar-menu>ul>li>a {
        padding: 9px 30px;
        font-size: 12px;
    }

    #sidebar-menu>ul>li>ul>li>a {
        padding: 7px 30px;
        font-size: 12px;
    }

    #sidebar-menu>ul>li>ul>li>ul>li>a {
        padding: 7px 30px;
        font-size: 12px;
    }

    #sidebar-menu .menu-arrow {
        top: 9px;
    }

    .li-plan {
        display: none;
    }

    @media (max-width: 767.98px) {
        .liNone {
            display: none;
        }

        .li-plan {
            display: initial !important;
        }
    }
</style>
@php
$usuario=DB::table('users')
->where('id','=',Auth::user()->id)->get();
$usuario_organizacion=DB::table('usuario_organizacion')
->where('user_id','=',Auth::user()->id)
->where('organi_id','=',session('sesionidorg'))
->get();
@endphp
{{-- MENU CUANDO ES ADMIN  --}}
@if ($usuario_organizacion[0]->rol_id==1)
<div class="justify-content-center" style="margin-bottom: 20px !important;">
    <div class="row">
        <div class="col-10 m-0 pr-2">
            <div id="search1" style="display: none;">
                <input class="form-control text-center" onInput="cambiar(this.value)" id="search" type="search" placeholder="Buscar" aria-label="Buscar" >
            </div>
        </div>
        <div class="col-2 m-0 pl-1 text-right">
            <div id="lupa" style="display: block;">
                <a type="button" class="btn btn-sm" onClick="cf1()"> 
                    <img src="{{asset('landing/images/lupa.png')}}" height="18">
                </a>
            </div>
            <div id="x" style="display: none;">
                <a type="button" class="btn btn-sm" onClick="cf2()"> 
                    <img src="{{asset('landing/images/x.png')}}" height="18">
                </a>
            </div>
        </div>
    </div>
</div>
<ul class="metismenu" id="menu-bar">
    <li id="gestDashboard">
        <a href="/dashboard" id="menuD">
            <i data-feather="home"></i>
            <span class="badge badge-success float-right">1</span>
            <span> Dashboard </span>
        </a>
    </li>

    <li class="liNone" id="gestCalendario">
        @if ($usuario[0]->user_estado==0)
        <a href="/calendario"> @else <a href="/calendarios"> @endif
                <i data-feather="calendar"></i>
                <span> Calendarios </span>
            </a>

    </li>
    <li id="gestEmpleado">
        <a href="javascript: void(0);">
            <i data-feather="list"></i>
            <span>Gesti??n de empleado</span>
            <span class="menu-arrow"></span>
        </a>

        <ul id="gestEmpleado_ul" class="nav-second-level" aria-expanded="false">
            <li>
                @if ($usuario[0]->user_estado==0)
                <a href="/empleado">

                    <span>Empleados</span>
                </a>

                @else
                <a href="/empleados">

                    <span>Empleados</span>
                </a>
                @endif
                <a href="/empleadosdeBaja">Empleados de baja</a>


            </li>
        </ul>
    </li>

    <li class="liNone" id="gestHorarios">

        <a href="javascript: void(0);">
            <i data-feather="clipboard"></i>
            <span>Horarios</span>
            <span class="menu-arrow"></span>
        </a>

        <ul id="gestHorarios_ul" class="nav-second-level" aria-expanded="false">
            <li>

                @if ($usuario[0]->user_estado==0)
                <a href="/horario">

                    <span>Asignar horario</span>
                </a>

                @else
                <a href="/horarios">

                    <span>Asignar horario</span>
                </a>
                <a href="/incidencias">

                    <span>Incidencias</span>
                </a>
                <a href="/reporteMatrizHorarios">

                    <span>Matriz de horarios</span>
                </a>
                @endif

            </li>
        </ul>
    </li>
    <li id="gestActividades">
        <a href="javascript: void(0);">
            <i data-feather="layers"></i>
            <span>Gesti??n de actividades</span>
            <span class="menu-arrow"></span>
        </a>
        <ul id="gestActividades_ul" class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/actividad">
                    <span>Actividades</span>
                </a>
                <a href="/subactividad">
                    <span>Subactividades</span>
                </a>

            </li>
        </ul>
    </li>
    @if(colorLi()->Mruta == 1 || colorLi()->Mpuerta == 1 || colorLi()->Mtareo == 1)
        <li id="gestPuntosContol">
            <a href="/puntoControl">
                <img src="{{asset('landing/images/vectorpaint.svg')}}" height="18" style="margin: 0 10px 0 3px;">
                <span>Puntos de Control</span>
            </a>
        </li>
    @else
        <li id="gestPuntosContol" style="background-color: #D3D3D3" data-toggle="tooltip" data-placement="right" title="No seleccionaste el modo tareo o asistencia en puerta.">
            <a href="#" data-toggle="modal" data-target="#modos">
                <img src="{{asset('landing/images/vectorpaint.svg')}}" height="18" style="margin: 0 10px 0 3px;">
                <span>Puntos de Control</span>
            </a>
        </li>
    @endif
    @if(colorLi()->Mtareo == 1 || colorLi()->Mpuerta == 1)
        <li id="gestCentroCosto">
            <a href="/centroCosto">
                <img src="{{asset('landing/images/bolsa-de-dinero.svg')}}" height="20" style="margin: 0 10px 0 3px;">
                <span>Centro de costo</span>
            </a>
        </li>
    @else
        <li id="gestCentroCosto" style="background-color: #D3D3D3" data-toggle="tooltip" data-placement="right" title="No seleccionaste el modo tareo, ni asistencia en puerta ni control en ruta.">
            <a href="#" data-toggle="modal" data-target="#modos">
                <img src="{{asset('landing/images/bolsa-de-dinero.svg')}}" height="20" style="margin: 0 10px 0 3px;">
                <span>Centro de costo</span>
            </a>
        </li>
    @endif
    @if(colorLi()->Mremoto == 1)
        <li id="li_remoto">
            <a href="javascript: void(0);">
                <i data-feather="activity"></i>
                <span>Modo: Control Remoto</span>
                <span class="menu-arrow"></span>
            </a>

            <ul id="li_remoto_ul" class="nav-second-level" aria-expanded="false">
                <li>
                    <a href="/controlRemoto">Dashboard</a>
                    <a href="/tareas">Detalle Diario</a>
                </li>
                <li>
                    <a href="javascript: void(0);">
                        <span>Reportes</span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul id="li_remoto_ul_ul" class="nav-third-level" aria-expanded="false">
                        <li>
                            <a href="/reporteSemanal">Tiempos por semana</a>
                            <a href="/reporteMensual">Tiempos por mes</a>
                            <a href="/reporteTardanzas">Tardanzas</a>
                            <a href="/reporteMatrizTardanzas">Matriz de tardanzas</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
    @else
        <li id="li_remoto" style="background-color: #D3D3D3" data-toggle="tooltip" data-placement="right" title="No seleccionaste este modo.">
            <a href="javascript: void(0);">
                <i data-feather="activity"></i>
                <span>Modo: Control Remoto</span>
                <span class="menu-arrow"></span>
            </a>

            <ul id="li_remoto_ul" class="nav-second-level" aria-expanded="false">
                <li>
                    <a href="#" data-toggle="modal" data-target="#modos">Dashboard</a>
                    <a href="#" data-toggle="modal" data-target="#modos">Detalle Diario</a>
                </li>
                <li>
                    <a href="javascript: void(0);">
                        <span>Reportes</span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul id="li_remoto_ul_ul" class="nav-third-level" aria-expanded="false">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modos">Tiempos por semana</a>
                            <a href="#" data-toggle="modal" data-target="#modos">Tiempos por mes</a>
                            <a href="#" data-toggle="modal" data-target="#modos">Tardanzas</a>
                            <a href="#" data-toggle="modal" data-target="#modos">Matriz de tardanzas</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
    @endif

    @if(colorLi()->Mruta == 1)
    <li id="li_ruta" >
        <a href="javascript: void(0);">
            <i data-feather="map-pin"></i>
            <span>Modo: Control en Ruta</span>
            <span class="menu-arrow"></span>
        </a>

        <ul id="li_ruta_ul" class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/ruta">Detalle Diario</a>
                <a href="/rutaReporte">Reporte Semanal</a>
                <a href="/reporteTardanzasRuta">Tardanzas</a>
                <a href="/reporteMatrizTardanzasRuta">Matriz de tardanzas</a>
            </li>
        </ul>
    </li>
    @else
    <li id="li_ruta" style="background-color: #D3D3D3" data-toggle="tooltip" data-placement="right" title="No seleccionaste este modo.">
        <a href="javascript: void(0);">
            <i data-feather="map-pin"></i>
            <span>Modo: Control en Ruta</span>
            <span class="menu-arrow"></span>
        </a>

        <ul id="li_ruta_ul" class="nav-second-level" aria-expanded="false">
            <li>
                <a href="#" data-toggle="modal" data-target="#modos">Detalle Diario</a>
                <a href="#" data-toggle="modal" data-target="#modos">Reporte Semanal</a>
                <a href="#" data-toggle="modal" data-target="#modos">Tardanzas</a>
                <a href="#" data-toggle="modal" data-target="#modos">Matriz de tardanzas</a>
            </li>
        </ul>
    </li>
    @endif
    @if(colorLi()->Mpuerta == 1)
    <li id="li_puerta">
        <a href="javascript: void(0);">
            <i data-feather="check-circle"></i>
            <span>Modo: Asistencia en puerta</span>
            <span class="menu-arrow"></span>
        </a>
        <ul id="li_puerta_ul" class="nav-second-level" aria-expanded="false">
            <li>
                <a href="javascript: void(0);">
                    <span>Configuraci??n</span>
                    <span class="menu-arrow"></span>
                </a>

                <ul id="li_puerta_ul_ul" class="nav-third-level" aria-expanded="false">
                    <li>
                        <a href="/dispositivos">Dispositivos</a>
                        <a href="/controladores">Controladores</a>
                    </li>
                </ul>
            </li>
            <li><a href="/reporteAsistencia">Detalle de asistencia</a></li>
            <li>
                <a href="javascript: void(0);">
                    <span>Reportes e informes</span>
                    <span class="menu-arrow"></span>
                </a>

                <ul id="li_puerta_ul_ul2" class="nav-third-level" aria-expanded="false">
                    <li>
                        <a href="/ReporteFecha">Asistencia por fecha</a>
                        <a href="/ReporteEmpleado">Asistencia por empleado</a>
                        <a href="/indexTrazabilidad">Asistencia consolidada</a>
                        <a href="/reporteTardanzasPuerta">Tardanzas</a>
                        <a href="/reporteMatrizTardanzasPuerta">Matriz de tardanzas</a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    @else
    <li id="li_puerta" style="background-color: #D3D3D3" data-toggle="tooltip" data-placement="right" title="No seleccionaste este modo.">
        <a href="javascript: void(0);">
            <i data-feather="check-circle"></i>
            <span>Modo: Asistencia en puerta</span>
            <span class="menu-arrow"></span>
        </a>
        <ul id="li_puerta_ul" class="nav-second-level" aria-expanded="false">
            <li>
                <a href="javascript: void(0);">
                    <span>Configuraci??n</span>
                    <span class="menu-arrow"></span>
                </a>

                <ul id="li_puerta_ul_ul" class="nav-third-level" aria-expanded="false">
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modos">Dispositivos</a>
                        <a href="#" data-toggle="modal" data-target="#modos">Controladores</a>
                    </li>
                </ul>
            </li>
            <li><a href="#" data-toggle="modal" data-target="#modos">Detalle de asistencia</a></li>
            <li>
                <a href="javascript: void(0);">
                    <span>Reportes e informes</span>
                    <span class="menu-arrow"></span>
                </a>

                <ul id="li_puerta_ul_ul2" class="nav-third-level" aria-expanded="false">
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modos">Asistencia por fecha</a>
                        <a href="#" data-toggle="modal" data-target="#modos">Asistencia por empleado</a>
                        <a href="#" data-toggle="modal" data-target="#modos">Asistencia consolidada</a>
                        <a href="#" data-toggle="modal" data-target="#modos">Tardanzas</a>
                        <a href="#" data-toggle="modal" data-target="#modos">Matriz de tardanzas</a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    @endif

    @if(colorLi()->Mtareo == 1)
    <li id="li_tareo">
        <a href="javascript: void(0);">
            <i data-feather="pocket"></i>
            <span>Modo: Tareo</span>
            <span class="menu-arrow"></span>
        </a>

        <ul id="li_tareo_ul" class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/dispositivosTareo">Dispositivos</a>
                <a href="/controladoresTareo">Controladores</a>
                <a href="/reporteTareo">Detalle de tareo</a>
                <a href="/reporteFechaTareo">Reporte de tareo por fecha</a>
                <a href="/reporteEmpleadoTareo">Reporte de tareo por empleado</a>


            </li>
        </ul>
    </li>
    @else
    <li id="li_tareo" style="background-color: #D3D3D3" data-toggle="tooltip" data-placement="right" title="No seleccionaste este modo.">
        <a href="javascript: void(0);">
            <i data-feather="pocket"></i>
            <span>Modo: Tareo</span>
            <span class="menu-arrow"></span>
        </a>

        <ul id="li_tareo_ul" class="nav-second-level" aria-expanded="false">
            <li>
                <a href="#" data-toggle="modal" data-target="#modos">Dispositivos</a>
                <a href="#" data-toggle="modal" data-target="#modos">Controladores</a>
                <a href="#" data-toggle="modal" data-target="#modos">Detalle de tareo</a>
                <a href="#" data-toggle="modal" data-target="#modos">Reporte de tareo por fecha</a>
                <a href="#" data-toggle="modal" data-target="#modos">Reporte de tareo por empleado</a>


            </li>
        </ul>
    </li>
    @endif

    <li style="display: none">
        <a href="javascript: void(0);">
            <i data-feather="dollar-sign"></i>
            <span> Suscripciones </span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li class="liNone">
                <a href="/planes">Planes y precios</a>

            </li>
            <li class="li-plan" style="display: none">
                <a href="/plan">Planes y precios</a>

            </li>
        </ul>
    </li>
    <li>
        <a href="/biblioteca">
            <i data-feather="play-circle"></i>
            <span>Academia</span>
        </a>
    </li>
</ul>
{{-- ----------------------------------- --}}
@endif
{{-- MENU CUANDO ES INVITADO --}}
@if ($usuario_organizacion[0]->rol_id==3)

<div class="justify-content-center" style="margin-bottom: 20px !important;">
    <div class="row">
        <div class="col-10 m-0 pr-2">
            <div id="search1Inv" style="display: none;">
                <input class="form-control text-center" onInput="cambiarInv(this.value)" id="searchInv" type="search" placeholder="Buscar" aria-label="Buscar" >
            </div>
        </div>
        <div class="col-2 m-0 pl-1 text-right">
            <div id="lupaInv" style="display: block;">
                <a type="button" class="btn btn-sm" onClick="cf1Inv()"> 
                    <img src="{{asset('landing/images/lupa.png')}}" height="18">
                </a>
            </div>
            <div id="xInv" style="display: none;">
                <a type="button" class="btn btn-sm" onClick="cf2Inv()"> 
                    <img src="{{asset('landing/images/x.png')}}" height="18">
                </a>
            </div>
        </div>
    </div>
</div>

<ul class="metismenu" id="menu-bar">
    @php
    $invitadod=DB::table('invitado')
    ->where('user_Invitado','=',Auth::user()->id)
    ->where('organi_id','=',session('sesionidorg'))
    ->get()->first();
    $permiso_invitado=DB::table('permiso_invitado')
    ->where('idinvitado','=',$invitadod->idinvitado)
    ->get()->first();
    @endphp
    @if ($invitadod->dashboard==1)
    <li id="gestDashboard">
        <a href="/dashboard" id="menuD">
            <i data-feather="home"></i>
            <span class="badge badge-success float-right">1</span>
            <span> Dashboard </span>
        </a>
    </li>


    @endif

    @if ($invitadod->gestCalendario==1)
    <li id="gestCalendario">
        <a href="/calendarios">
            <i data-feather="calendar"></i>
            <span> Calendarios </span>
        </a>

    </li>
    @endif

    @if ($invitadod->permiso_Emp==1)
    <li id="gestEmpleado">
        <a href="javascript: void(0);">
            <i data-feather="list"></i>
            <span>Gesti??n de empleado</span>
            <span class="menu-arrow"></span>
        </a>

        <ul id="gestEmpleado_ul" class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/empleados">

                    <span>Empleados</span>
                </a>

            </li>
        </ul>
    </li>


    @endif

    @if ($invitadod->gestionActiv==1)
    <li id="gestActividades">
        <a href="javascript: void(0);">
            <i data-feather="layers"></i>
            <span>Gesti??n de actividades</span>
            <span class="menu-arrow"></span>
        </a>
        <ul id="gestActividades_ul" class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/actividad">
                    <span>Actividades</span>
                </a>
                <a href="/subactividad">
                    <span>Subactividades</span>
                </a>

            </li>
        </ul>
    </li>
    @endif


    <!-- <li>
        <a href="/proyecto">
            <i data-feather="briefcase"></i>
            <span>Asignar tarea</span>
        </a>
    </li> -->
    @if ($invitadod->modoCR==1)
        @if(colorLi()->Mremoto == 1)
            <li id="li_remotoInv">
                <a href="javascript: void(0);">
                    <i data-feather="activity"></i>
                    <span>Modo: Control Remoto</span>
                    <span class="menu-arrow"></span>
                </a>
                <ul id="li_remotoInv_ul" class="nav-second-level" aria-expanded="false">
                    <li>
                        <a href="/controlRemoto">Dashboard</a>
                        <a href="/tareas">Detalle Diario</a>
                    </li>
                    <li>
                        <a href="javascript: void(0);">
                            <span>Reportes</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul id="li_remotoInv_ul_ul" class="nav-third-level" aria-expanded="false">
                            <li>
                                <a href="/reporteSemanal">Tiempos por semana</a>
                                <a href="/reporteMensual">Tiempos por mes</a>
                                <a href="/reporteTardanzas">Tardanzas</a>
                                <a href="/reporteMatrizTardanzas">Matriz de tardanzas</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        @else
            <li id="li_remotoInv" style="background-color: #D3D3D3">
                <a href="javascript: void(0);">
                    <i data-feather="activity"></i>
                    <span>Modo: Control Remoto</span>
                    <span class="menu-arrow"></span>
                </a>
                <ul id="li_remotoInv_ul" class="nav-second-level" aria-expanded="false">
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modos">Dashboard</a>
                        <a href="#" data-toggle="modal" data-target="#modos">Detalle Diario</a>
                    </li>
                    <li>
                        <a href="javascript: void(0);">
                            <span>Reportes</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul id="li_remotoInv_ul_ul" class="nav-third-level" aria-expanded="false">
                            <li>
                                <a href="#" data-toggle="modal" data-target="#modos">Tiempos por semana</a>
                                <a href="#" data-toggle="modal" data-target="#modos">Tiempos por mes</a>
                                <a href="#" data-toggle="modal" data-target="#modos">Tardanzas</a>
                                <a href="#" data-toggle="modal" data-target="#modos">Matriz de tardanzas</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        @endif

    @endif

    @if ($invitadod->ControlRuta==1)
        @if(colorLi()->Mruta == 1)
            <li id="li_rutaInv">
                <a href="javascript: void(0);">
                    <i data-feather="map-pin"></i>
                    <span>Modo: Control en Ruta</span>
                    <span class="menu-arrow"></span>
                </a>
                <ul id="li_rutaInv_ul" class="nav-second-level" aria-expanded="false">
                    <li>
                        <a href="/ruta">Detalle Diario</a>
                        <a href="/rutaReporte">Reporte Semanal</a>
                        <a href="/reporteTardanzasRuta">Tardanzas</a>
                        <a href="/reporteMatrizTardanzasRuta">Matriz de tardanzas</a>
                    </li>
                </ul>
            </li>
        @else
            <li id="li_rutaInv" style="background-color: #D3D3D3">
                <a href="javascript: void(0);">
                    <i data-feather="map-pin"></i>
                    <span>Modo: Control en Ruta</span>
                    <span class="menu-arrow"></span>
                </a>
                <ul id="li_rutaInv_ul" class="nav-second-level" aria-expanded="false">
                    <li>
                        <a href="#" data-toggle="modal" data-target="#modos">Detalle Diario</a>
                        <a href="#" data-toggle="modal" data-target="#modos">Reporte Semanal</a>
                        <a href="#" data-toggle="modal" data-target="#modos">Tardanzas</a>
                        <a href="#" data-toggle="modal" data-target="#modos">Matriz de tardanzas</a>
                    </li>
                </ul>
            </li>
        @endif

    @endif

    @if ($invitadod->asistePuerta==1)
    @if(colorLi()->Mpuerta == 1)
        <li id="li_puertaInv">
            <a href="javascript: void(0);">
                <i data-feather="check-circle"></i>
                <span>Modo: Asistencia en puerta</span>
                <span class="menu-arrow"></span>
            </a>

            <ul id="li_puertaInv_ul" class="nav-second-level" aria-expanded="false">
                @if ($permiso_invitado->verPuerta==1)
                <li>
                    <a href="javascript: void(0);">
                        <span>Configuraci??n</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul id="li_puertaInv_ul_ul" class="nav-third-level" aria-expanded="false">
                        <li>
                            <a href="/dispositivos">Dispositivos</a>
                            <a href="/controladores">Controladores</a>
                        </li>
                    </ul>
                </li>
                @endif
                @if ($invitadod->ModificarReportePuerta==1)
                <li><a href="/reporteAsistencia">Detalle de asistencia</a></li>
                @endif
                @if ($invitadod->reporteAsisten==1)
                <li>
                    <a href="javascript: void(0);">
                        <span>Reportes e informes</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul id="li_puertaInv_ul_ul2" class="nav-third-level" aria-expanded="false">
                        <li>
                            <a href="/ReporteFecha">Asistencia por fecha</a>
                            <a href="/ReporteEmpleado">Asistencia por empleado</a>
                            <a href="/indexTrazabilidad">Asistencia consolidada</a>
                            <a href="/reporteTardanzasPuerta">Tardanzas</a>
                            <a href="/reporteMatrizTardanzasPuerta">Matriz de tardanzas</a>
                        </li>
                    </ul>
                </li>
                @endif
            </ul>
        </li>
    @else
        <li id="li_puertaInv" style="background-color: #D3D3D3">
            <a href="javascript: void(0);">
                <i data-feather="check-circle"></i>
                <span>Modo: Asistencia en puerta</span>
                <span class="menu-arrow"></span>
            </a>

            <ul id="li_puertaInv_ul" class="nav-second-level" aria-expanded="false">
                @if ($permiso_invitado->verPuerta==1)
                <li>
                    <a href="javascript: void(0);">
                        <span>Configuraci??n</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul id="li_puertaInv_ul_ul" class="nav-third-level" aria-expanded="false">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modos">Dispositivos</a>
                            <a href="#" data-toggle="modal" data-target="#modos">Controladores</a>
                        </li>
                    </ul>
                </li>
                @endif
                @if ($invitadod->ModificarReportePuerta==1)
                <li><a href="#" data-toggle="modal" data-target="#modos">Detalle de asistencia</a></li>
                @endif
                @if ($invitadod->reporteAsisten==1)
                <li>
                    <a href="javascript: void(0);">
                        <span>Reportes e informes</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul id="li_puertaInv_ul_ul2" class="nav-third-level" aria-expanded="false">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#modos">Asistencia por fecha</a>
                            <a href="#" data-toggle="modal" data-target="#modos">Asistencia por empleado</a>
                            <a href="#" data-toggle="modal" data-target="#modos">Asistencia consolidada</a>
                            <a href="#" data-toggle="modal" data-target="#modos">Tardanzas</a>
                            <a href="#" data-toggle="modal" data-target="#modos">Matriz de tardanzas</a>
                        </li>
                    </ul>
                </li>
                @endif
            </ul>
        </li>
    @endif


    @endif

    @if ($invitadod->modoTareo==1)
        @if(colorLi()->Mtareo == 1)
            <li id="li_tareoInv">
                <a href="javascript: void(0);">
                    <i data-feather="pocket"></i>
                    <span>Modo: Tareo</span>
                    <span class="menu-arrow"></span>
                </a>

                <ul id="li_tareoInv_ul" class="nav-second-level" aria-expanded="false">
                    <li>
                        @if ($permiso_invitado->verModoTareo==1)
                        <a href="/dispositivosTareo">Dispositivos</a>
                        <a href="/controladoresTareo">Controladores</a>
                        @endif

                        @if ($permiso_invitado->modifModoTareo==1)
                        <a href="/reporteTareo">Detalle de tareo</a>
                        @endif
                        @if ($permiso_invitado->verModoTareo==1)
                        <a href="/reporteFechaTareo">Reporte de tareo por fecha</a>
                        <a href="/reporteEmpleadoTareo">Reporte de tareo por empleado</a>
                        @endif
                    </li>
                </ul>
            </li>
        @else
            <li id="li_tareoInv" style="background-color: #D3D3D3">
                <a href="javascript: void(0);">
                    <i data-feather="pocket"></i>
                    <span>Modo: Tareo</span>
                    <span class="menu-arrow"></span>
                </a>

                <ul id="li_tareoInv_ul" class="nav-second-level" aria-expanded="false">
                    <li>
                        @if ($permiso_invitado->verModoTareo==1)
                        <a href="#" data-toggle="modal" data-target="#modos">Dispositivos</a>
                        <a href="#" data-toggle="modal" data-target="#modos">Controladores</a>
                        @endif

                        @if ($permiso_invitado->modifModoTareo==1)
                        <a href="#" data-toggle="modal" data-target="#modos">Detalle de tareo</a>
                        @endif
                        @if ($permiso_invitado->verModoTareo==1)
                        <a href="#" data-toggle="modal" data-target="#modos">Reporte de tareo por fecha</a>
                        <a href="#" data-toggle="modal" data-target="#modos">Reporte de tareo por empleado</a>
                        @endif
                    </li>
                </ul>
            </li>
        @endif

    @endif

    <li>
        <a href="/biblioteca">
            <i data-feather="play-circle"></i>
            <span>Academia</span>
        </a>
    </li>
</ul>

@endif
