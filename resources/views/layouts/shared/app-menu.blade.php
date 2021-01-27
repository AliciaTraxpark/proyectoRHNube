@php
use Illuminate\Support\Facades\Auth;
use App\User;

use Illuminate\Support\Facades\DB;
@endphp

<style>
    .left-side-menu {
        background: #fafafa;
    }

    #sidebar-menu>ul>li>a {
        padding: 9px 30px;
        font-size: 14px;
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
<ul class="metismenu" id="menu-bar">
    <li>
        <a href="/dashboard" id="menuD">
            <i data-feather="home"></i>
            <span class="badge badge-success float-right">1</span>
            <span> Dashboard </span>
        </a>
    </li>

    <li class="liNone">
        @if ($usuario[0]->user_estado==0)
        <a href="/calendario"> @else <a href="/calendarios"> @endif
                <i data-feather="calendar"></i>
                <span> Calendarios </span>
            </a>

    </li>
    <li>

        <a href="javascript: void(0);">
            <i data-feather="list"></i>
            <span>Gestión de empleado</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
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

    <li class="liNone">

        <a href="javascript: void(0);">
            <i data-feather="clipboard"></i>
            <span>Horarios</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>

                @if ($usuario[0]->user_estado==0)
                <a href="/horario">

                    <span>Asignar horario</span>
                </a>

                @else
                <a href="/horarios">

                    <span>Asignar horario</span>
                </a>
                <a href="/dias/laborales">

                    <span>Asignar días no laborales</span>
                </a>
                @endif

            </li>
        </ul>
    </li>
    <li>
        <a href="javascript: void(0);">
            <i data-feather="layers"></i>
            <span>Gestion de actividades</span>
            <span class="menu-arrow"></span>
        </a>
        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/actividad">
                    <span>Actividades</span>
                </a>
                <a href="/subactividad">
                    <span>SubActividades</span>
                </a>

            </li>
        </ul>
    </li>
    <li>
        <a href="/puntoControl">
            <img src="{{asset('landing/images/vectorpaint.svg')}}" height="18" style="margin: 0 10px 0 3px;">
            <span>Puntos de Control</span>
        </a>
    </li>
    <li>
        <a href="/centroCosto">
            <img src="{{asset('landing/images/bolsa-de-dinero.svg')}}" height="20" style="margin: 0 10px 0 3px;">
            <span>Centro Costo</span>
        </a>
    </li>
    <li>

        <a href="javascript: void(0);">
            <i data-feather="activity"></i>
            <span>Modo: Control Remoto</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/controlRemoto">Dashboard</a>
                <a href="/tareas">Detalle Diario</a>
            </li>
            <li>
                <a href="javascript: void(0);">
                    <span>Reportes</span>
                    <span class="menu-arrow"></span>
                </a>

                <ul class="nav-third-level" aria-expanded="false">
                    <li>
                        <a href="/reporteSemanal">Tiempos por semana</a>
                        <a href="/reporteMensual">Tiempos por mes</a>
                        {{-- <a href="/reporteTardanzas">Tardanzas</a>
                        <a href="#">Matríz de tardanzas</a> --}}
                    </li>
                </ul>
            </li>
        </ul>
    </li>

    <li>

        <a href="javascript: void(0);">
            <i data-feather="map-pin"></i>
            <span>Modo: Control en Ruta</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/ruta">Detalle Diario</a>
                <a href="/rutaReporte">Reporte Semanal</a>
            </li>
        </ul>
    </li>

    <li>

        <a href="javascript: void(0);">
            <i data-feather="check-circle"></i>
            <span>Modo: Asistencia en puerta</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/dispositivos">Dispositivos</a>
                <a href="/controladores">Controladores</a>
                <a href="/reporteAsistencia">Detalle de asistencia</a>
                <a href="/ReporteFecha">Reporte de asistencia por fecha</a>
                <a href="/ReporteEmpleado">Reporte de asistencia por empleado</a>

            </li>
        </ul>
    </li>

    <li>

        <a href="javascript: void(0);">
            <i data-feather="pocket"></i>
            <span>Modo: Tareo</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/dispositivosTareo">Dispositivos</a>
                <a href="/controladoresTareo">Controladores</a>
                <a href="/reporteTareo">Detalle de tareo</a>


            </li>
        </ul>
    </li>

    <li>
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
    <li>
        <a href="/dashboard" id="menuD">
            <i data-feather="home"></i>
            <span class="badge badge-success float-right">1</span>
            <span> Dashboard </span>
        </a>
    </li>


    @endif

    @if ($invitadod->gestCalendario==1)
    <li>
        <a href="/calendarios">
            <i data-feather="calendar"></i>
            <span> Calendarios </span>
        </a>

    </li>
    @endif

    @if ($invitadod->permiso_Emp==1)
    <li>
        <a href="javascript: void(0);">
            <i data-feather="list"></i>
            <span>Gestión de empleado</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/empleados">

                    <span>Empleados</span>
                </a>

            </li>
        </ul>
    </li>


    @endif

    @if ($invitadod->gestionActiv==1)
    <li>
        <a href="javascript: void(0);">
            <i data-feather="layers"></i>
            <span>Gestion de actividades</span>
            <span class="menu-arrow"></span>
        </a>
        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/actividad">
                    <span>Actividades</span>
                </a>
                <a href="/subactividad">
                    <span>SubActividades</span>
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
    <li>

        <a href="javascript: void(0);">
            <i data-feather="activity"></i>
            <span>Modo: Control Remoto</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/controlRemoto">Dashboard</a>
                <a href="/tareas">Detalle Diario</a>
                <a href="/reporteSemanal">Reporte Semanal</a>
                <a href="/reporteMensual">Reporte Mensual</a>
            </li>
        </ul>
    </li>
    @endif

    @if ($invitadod->ControlRuta==1)

    <li>
        <a href="javascript: void(0);">
            <i data-feather="map-pin"></i>
            <span>Modo: Control en Ruta</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/ruta">Detalle Diario</a>
                <a href="/rutaReporte">Reporte Semanal</a>
            </li>
        </ul>
    </li>
    @endif

    @if ($invitadod->asistePuerta==1)
    <li>
        <a href="javascript: void(0);">
            <i data-feather="check-circle"></i>
            <span>Modo: Asistencia en puerta</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                @if ($permiso_invitado->verPuerta==1)
                <a href="/dispositivos">Dispositivos</a>
                <a href="/controladores">Controladores</a>
                @endif
                @if ($invitadod->ModificarReportePuerta==1)
                <a href="/reporteAsistencia">Detalle de asistencia</a>

                @endif
                @if ($invitadod->reporteAsisten==1)


                <a href="/ReporteFecha">Reporte de asistencia por fecha</a>
                <a href="/ReporteEmpleado">Reporte de asistencia por empleado</a>
                @endif


            </li>
        </ul>
    </li>

    @endif
    <li>
        <a href="/biblioteca">
            <i data-feather="play-circle"></i>
            <span>Academia</span>
        </a>
    </li>
</ul>
@endif