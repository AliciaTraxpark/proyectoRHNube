@php
use App\organizacion;
use App\usuario_organizacion;
use App\persona;
@endphp
<!-- Topbar Start -->
<link href="{{ URL::asset('landing/home/css/modos.css') }}" rel="stylesheet" type="text/css" />
<style>
    .navbar-custom .topnav-menu .nav-link:hover svg,
    .navbar-custom .topnav-menu .nav-link:focus svg,
    .navbar-custom .topnav-menu .nav-link:active svg {
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .pro-user-name {
            display: contents !important;
        }

        .notifiResponsive {}

        .notifiResponsive {
            width: 250px !important;
        }

        .badgeResponsive {
            margin-left: 10% !important;
        }
    }

    .dropdown-item {
        padding: 0.15rem 1.5rem !important;
    }

    .dropdown-menu-right>a:hover {
        background: rgb(236, 236, 236) !important;
    }

    jdiv#jvlabelWrap {
        display: none !important;
    }

    .wrap_12d7._orientationRight_ac72.__jivoMobileButton {
        display: none;
    }

    .button_b0cc {
        display: none;
    }

    .wrap_b0c8._orientationRight_3898.__jivoMobileButton {
        display: none;
    }

    @media(max-width: 767px) {
        .modos_header {
            margin-top: 8px !important;
        }
    }

</style>
<div class="navbar navbar-expand flex-column flex-md-row navbar-custom" style="padding-left: 0px;">
    <div class="container-fluid pb-3 pt-3 contResponsive">
        <ul class="navbar-nav bd-navbar-nav flex-row list-unstyled menu-left
            mb-0">
            <li class="" style="width: 80px;">
                <button class="button-menu-mobile open-left">
                    <i data-feather="menu" class="menu-icon"></i>
                    <i data-feather="x" class="close-icon"></i>
                </button>
            </li>
        </ul>
        <!-- LOGO -->
        <a href="/" class="navbar-brand mr-0 mr-md-2 logo">
            <span class="logo-lg text-center">
                <img src="{{ asset('landing/home/images/logo_animado.gif') }}" alt="" height="65" />
            </span>
            <span class="logo-sm">
                <img src="{{ asset('landing/home/images/logo_animado.gif') }}" alt="" height="45">
            </span>
        </a>

        @php
            $usuario = Auth::user();
            /* $usuario_organizacion=usuario_organizacion::where('user_id','=',$usuario->id)->first(); */
            $organizacion = organizacion::where('organi_id', '=', session('sesionidorg'))->first();
            $persona = persona::where('perso_id', '=', $usuario->perso_id)->first();

            $istaOrganizacion = DB::table('organizacion as o')
                ->join('usuario_organizacion as uo', 'o.organi_id', '=', 'uo.organi_id')
                ->join('rol as r', 'uo.rol_id', '=', 'r.rol_id')
                ->where('uo.user_id', '=', Auth::user()->id)
                ->where('o.organi_id', '!=', session('sesionidorg'))
                ->where('o.organi_estado', '=', 1)
                ->get();

                $empleadoRepro = DB::table('empleado as e')
                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                ->join('calendario_empleado as eve', 'e.emple_id', '=', 'eve.emple_id')
                ->select('p.perso_nombre', 'p.perso_apPaterno', 'p.perso_apMaterno', 'e.emple_nDoc', 'p.perso_id', 'e.emple_id')
                ->where('e.organi_id', '=', session('sesionidorg'))
                ->where('eve.emple_id', '!=', null)
                ->where('e.emple_estado', '=', 1)
                ->groupBy('e.emple_id')
                ->get();
                //*AREA CUADNO TIENE ASIGNADO EMPLEADOS
            $areaRepro = DB::table('area as ar')
            ->join('empleado as em', 'ar.area_id', '=', 'em.emple_area')
            ->where('ar.organi_id','=',session('sesionidorg'))
            ->select(
                'ar.area_id as idarea',
                'area_descripcion as descripcion'
            )
            ->groupBy('ar.area_id')
            ->get();

            //*CARGO CUADNO TIENE ASIGNADO EMPLEADO
            $cargoRepro = DB::table('cargo as c')
            ->join('empleado as em', 'c.cargo_id', '=', 'em.emple_cargo')
            ->where('c.organi_id', '=', session('sesionidorg'))
            ->select('cargo_id as idcargo',
                'cargo_descripcion as descripcion')
            ->groupBy('c.cargo_id')
            ->get();

            //*LOCAL CUADNO TIENE ASIGNADO EMPLEADO
            $localRepro = DB::table('local as l')
            ->join('empleado as em', 'l.local_id', '=', 'em.emple_local')
            ->where('l.organi_id', '=', session('sesionidorg'))
            ->select('local_id as idlocal',
               'local_descripcion as descripcion')
            ->groupBy('l.local_id')
            ->get();

            //*NIVEL CUADNO TIENE ASIGNADO EMPLEADO
            $nivelRepro = DB::table('nivel as n')
            ->join('empleado as em', 'n.nivel_id', '=', 'em.emple_nivel')
            ->where('n.organi_id', '=', session('sesionidorg'))
            ->select('nivel_id as idnivel',
               'nivel_descripcion as descripcion')
            ->groupBy('n.nivel_id')
            ->get();

            //*CENTRO DE COSTOS CUANDO TIENE ASIGNADO EMPLEADOS
            $centrocRepro = DB::table('centro_costo as cc')
            ->join('centrocosto_empleado as ce', 'cc.centroC_id', '=', 'ce.idCentro')
            ->where('cc.organi_id', '=', session('sesionidorg'))
            ->where('ce.estado', '=', 1)
            ->select('cc.centroC_id as idcentro',
               'cc.centroC_descripcion as descripcion')
            ->groupBy('cc.centroC_id')
            ->get();
        @endphp
        <div id="content123" class="alert alert-success" role="alert"
            style="display: none;font-size:12px;color: #163552;position: fixed; right: 0; top: 70px; height: 40px;">
            <strong>Tienes nuevas notificaciones</strong>
        </div>
        <ul class="navbar-nav flex-row ml-auto d-flex list-unstyled topnav-menu
            float-right mb-0">

            <li class="dropdown d-lg-block" data-toggle="tooltip" data-placement="left" title="cambiar de modo">
                <div class="btn-group mt-3  modos_header">
                    <button type="button" class="btn" data-toggle="modal" data-target="#modos"
                        style="font-size: 14px!important; font-weight: 700; color: white; background-color: #163552!important; border-color: #163552!important;padding-top: 9px;">
                        <span class="badge badge-pill"
                            style="background-color: #617be3;color: #ffffff;font-size: 12px;font-weight: normal"><img
                                src="{{ asset('landing/images/seleccione.png') }}" height="20" class="mr-1">Selección
                            de
                            modos</span></button>

                </div><!-- /btn-group -->
            </li>

            @if (count($istaOrganizacion) > 0)
                <li class="dropdown d-none d-lg-block" data-toggle="tooltip" data-placement="left"
                    title="cambiar organización">
                    <div class="btn-group mt-3">
                        <button type="button" class="btn  dropdown-toggle"
                            style="font-size: 14px!important; font-weight: 700;     color: white; background-color: #163552!important; border-color: #163552!important;padding-top: 9px;"
                            data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">{{ $organizacion->organi_razonSocial }} <i class="icon"><span
                                    data-feather="chevron-down"></span></i></button>
                        <div class="dropdown-menu dropdown-menu-right">
                            @foreach ($istaOrganizacion as $istaOrganizaciones)
                                <a class="dropdown-item" style="font-size: 12px;cursor: pointer;"
                                    onclick="ingresarOrganiza({{ $istaOrganizaciones->organi_id }})">
                                    {{ $istaOrganizaciones->organi_razonSocial }}</a>
                            @endforeach
                        </div>
                    </div><!-- /btn-group -->
                </li>
            @endif

            <li class="dropdown d-none d-lg-block" data-toggle="tooltip" data-placement="left" title="">

                <a class="nav-link dropdown-toggle mr-0" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <span style="color: aliceblue;font-size: 12px" ;></span>&nbsp;
                    @if (count($istaOrganizacion) == 0)
                        <strong id="strongOrganizacion"
                            style="color: rgb(255, 255, 255)">{{ $organizacion->organi_razonSocial }}
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; |</strong>
                    @else
                        <strong style="color: rgb(255, 255, 255)"> |</strong>
                    @endif

                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="badge badge-pill"
                        style="background-color: #617be3;color: #ffffff;font-size: 12px;font-weight: normal"><img
                            src="{{ asset('landing/images/modo.svg') }}" height="20" class="mr-1">Beta
                        &nbsp;&nbsp;&nbsp;</span>
                </a>
            </li>
            <li class="dropdown d-none d-lg-block" data-toggle="tooltip" data-placement="left" title="">
                <a class="nav-link dropdown-toggle mr-0" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <span class="pro-user-name ml-1">
                        <strong id="strongNombre" style="color:
                            aliceblue;font-size:
                            13px">Bienvenido(a), {{ $persona->perso_nombre }}
                        </strong>
                        &nbsp;
                        <img id="imgxs2" src="{{ URL::asset('admin/assets//images/users/avatar-7.png') }}"
                            class="avatar-xs rounded-circle mr-2" alt="Shreyu" />
                    </span>
                </a>
            </li>
            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                    aria-expanded="false">
                    <i data-feather="bell"></i>
                    <span class="noti-icon-badge"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-xl notifiResponsive" style="width: 400px">
                    <!-- item-->
                    <div class="dropdown-item noti-title border-bottom" style="background-color: #163552;">
                        <div class="col-lg-12 col-sm-12 col-12 m-0">
                            <h5 class="m-0 font-size-16" style="font-weight:
                                bold;color: #fff;">
                                Notificaciones <span class="badge float-right
                                    mt-0 mr-1" style="background-color:
                                    #fdfdfd;color: #28292f;" id="totalNotifNL">0</span>
                            </h5>
                        </div>
                    </div>

                    <div class="slimscroll noti-scroll" id="notificacionesUser" style="height: 150px!important;">
                    </div>
                </div>
            </li>
            <li class="dropdown d-lg-block" data-toggle="tooltip" data-placement="left" title="">
                <a class="nav-link dropdown-toggle mr-0" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <span class="pro-user-name ml-1">
                        <!-- <strong id="strongNombre" style="color:
                            aliceblue;font-size:
                            13px">Bienvenido(a), {{ $persona->perso_nombre }}
                        </strong>
                        &nbsp;
                        <img id="imgxs2"
                            src="{{ URL::asset('admin/assets//images/users/avatar-7.png') }}"
                            class="avatar-xs rounded-circle mr-2" alt="Shreyu"
                            /> -->
                        <i data-feather="chevron-down"></i>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" style="font-size:
                    12.2px!important">
                    <!-- item-->
                    @php
                        $usuario = DB::table('users')
                            ->where('id', '=', Auth::user()->id)
                            ->get();
                        $usuario_organizacion = DB::table('usuario_organizacion')
                            ->where('user_id', '=', Auth::user()->id)
                            ->where('organi_id', '=', session('sesionidorg'))
                            ->get();
                    @endphp
                    @if ($usuario_organizacion[0]->rol_id == 1)
                        <a href="/perfil" class="dropdown-item notify-item">
                            <i data-feather="edit" class="icon-dual icon-xs mr-2" style="color: #163552"></i>
                            <span>Editar perfil</span>
                        </a>
                    @endif
                    <a href="/soporte" class="dropdown-item notify-item">
                        <i data-feather="settings" class="icon-dual icon-xs mr-2" style="color: #163552"></i>
                        <span>Ticket de Soporte</span>
                    </a>
                    <a href="/sugerencia" class="dropdown-item notify-item">
                        <i data-feather="mail" class="icon-dual icon-xs mr-2" style="color: #163552"></i>
                        <span>Ticket de Sugerencia</span>
                    </a>
                    @if ($usuario_organizacion[0]->rol_id == 1)
                        <a href="/delegarcontrol" class="dropdown-item notify-item">
                            <i data-feather="corner-up-right" class="icon-dual icon-xs mr-2" style="color: #163552"></i>
                            <span>Delegar control</span>
                        </a>
                    @endif

                    <a href="/elegirorganizacion" class="dropdown-item
                    notify-item">
                        <i data-feather="arrow-up-left" class="icon-dual icon-xs
                        mr-2" style="color: #163552"></i>
                        <span>cambiar organización</span>
                    </a>
                    <!-- item-->
                    <a href="#" id="chatJivo" data-toggle="modal" data-target="#exampleModal"
                        class="dropdown-item notify-item">
                        <i data-feather="message-square" class="icon-dual icon-xs mr-2" style="color: #163552"></i>
                        <span>Chatear con nosotros</span>
                    </a>

                   {{--  <a onclick="reporocesarHorario()" class="dropdown-item notify-item">
                        <i data-feather="message-square" class="icon-dual icon-xs mr-2" style="color: #163552"></i>
                        <span>Reprocesar horario</span>
                    </a> --}}
                    <a href="{{ route('logout') }}" class="dropdown-item
                        notify-item">
                        <i data-feather="log-out" class="icon-dual icon-xs
                            mr-2" style="color: #163552"></i>
                        <span>Cerrar sesión</span>
                    </a>

                </div>
            </li>
        </ul>
    </div>
</div>
{{-- MODAL --}}
<div id="modalCorreoElectronicoHeader" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="modalCorreoElectronico" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Registrar Correo
                    Electrónico
                    Empleado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:limpiarCorreoEH()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="idEmpleCorreoH">
                    <form class="form-horizontal col-lg-12" action="javascript:guardarCorreoEH()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" style="font-weight: 500;">Correo
                                        electrónico</label>
                                    <div class="input-group input-group-merge">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="background-color: #ffffff;">
                                                <img src="{{ asset('landing/images/at.svg') }}" height="13">
                                            </span>
                                        </div>
                                        <input type="email" type="text" class="form-control" id="textCorreoH"
                                            name="email" required autofocus>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal"
                    onclick="javascript:limpiarCorreoEH()">Cerrar</button>
                <button type="submit" style="background-color:#163552; border-color: #163552;"
                    class="btn btn-sm btn-primary">Registrar</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- end Topbar -->
{{-- MODAL DE SELECCIÓN DE MODOS --}}
<div class="modal fade" id="modos" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 1000px ">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163652;">
                <h5 class="modal-title" id="staticBackdropLabel" style="color: white;">Elige los modos a usar</h5>
            </div>
            <div class="modal-body">
                <div id="msj" style="font-size: 13px; color: red; margin-bottom: 15px">
                    • Debes elegir almenos un modo para continuar.
                </div>
                <div class="row" style="justify-content: center;">
                    @if (getLastActivity()[1] == 1)
                        @if (colorLi()->Mremoto == 1)
                            <div class="col-lg-3 col-sm-6">
                                <div class="card sombra" id="cardSelect1">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><small>MODO:</small><br><strong>Control remoto</strong>
                                        </h5>
                                        <div id="imgSelect1">
                                            <img src="/landing/home/images/home-office-lila.png" width="80" class="p-1">
                                        </div>
                                        <button type="button" id="btnSelec1" class="btn btn-second-rh active"
                                            data-toggle="button" aria-pressed="true">
                                            <img src='landing/images/check.svg' width='16'> Seleccionado
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-lg-3 col-sm-6">
                                <div class="card" id="cardSelect1">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><small>MODO:</small><br><strong>Control remoto</strong>
                                        </h5>
                                        <div id="imgSelect1">
                                            <img src="/landing/home/images/home-office-lila.png" width="80" class="p-1">
                                        </div>
                                        <button type="button" id="btnSelec1" class="btn btn-second-rh"
                                            data-toggle="button" aria-pressed="false">
                                            Seleccionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div style="display: none;">
                            <button type="button" id="btnSelec1" class="btn btn-second-rh active" data-toggle="button"
                                aria-pressed="false">
                                Seleccionar
                            </button>
                        </div>
                    @endif

                    @if (getLastActivity()[3] == 1)
                        @if (colorLi()->Mruta == 1)
                            <div class="col-lg-3 col-sm-6">
                                <div class="card sombra" id="cardSelect2">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><small>MODO:</small><br><strong>Control en ruta</strong>
                                        </h5>
                                        <div id="imgSelect2">
                                            <img src="/landing/home/images/control-en-ruta-lila.png" width="80"
                                                class="p-1">
                                        </div>
                                        <button type="button" id="btnSelec2" class="btn btn-second-rh active"
                                            data-toggle="button" aria-pressed="true">
                                            <img src='landing/images/check.svg' width='16'> Seleccionado
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-lg-3 col-sm-6">
                                <div class="card" id="cardSelect2">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><small>MODO:</small><br><strong>Control en ruta</strong>
                                        </h5>
                                        <div id="imgSelect2">
                                            <img src="/landing/home/images/control-en-ruta-lila.png" width="80"
                                                class="p-1">
                                        </div>
                                        <button type="button" id="btnSelec2" class="btn btn-second-rh"
                                            data-toggle="button" aria-pressed="false">
                                            Seleccionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div style="display: none;">
                            <button type="button" id="btnSelec2" class="btn btn-second-rh active" data-toggle="button"
                                aria-pressed="false">
                                Seleccionar
                            </button>
                        </div>
                    @endif

                    @if (getLastActivity()[2] == 1)
                        @if (colorLi()->Mpuerta == 1)
                            <div class="col-lg-3 col-sm-6">
                                <div class="card sombra" id="cardSelect3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><small>MODO:</small><br><strong>Asistencia en
                                                puerta</strong></h5>
                                        <div id="imgSelect3">
                                            <img src="/landing/home/images/asistencia-en-puerta-lila.png" width="80"
                                                class="p-1">
                                        </div>
                                        <button type="button" id="btnSelec3" class="btn btn-second-rh active"
                                            data-toggle="button" aria-pressed="true">
                                            <img src='landing/images/check.svg' width='16'> Seleccionado
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-lg-3 col-sm-6">
                                <div class="card" id="cardSelect3">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><small>MODO:</small><br><strong>Asistencia en
                                                puerta</strong></h5>
                                        <div id="imgSelect3">
                                            <img src="/landing/home/images/asistencia-en-puerta-lila.png" width="80"
                                                class="p-1">
                                        </div>
                                        <button type="button" id="btnSelec3" class="btn btn-second-rh"
                                            data-toggle="button" aria-pressed="false">
                                            Seleccionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div style="display: none;">
                            <button type="button" id="btnSelec3" class="btn btn-second-rh active" data-toggle="button"
                                aria-pressed="false">
                                Seleccionar
                            </button>
                        </div>
                    @endif

                    @if (getLastActivity()[4] == 1)
                        @if (colorLi()->Mtareo == 1)
                            <div class="col-lg-3 col-sm-6">
                                <div class="card sombra" id="cardSelect4">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><small>MODO:</small><br><strong>Tareo</strong></h5>
                                        <div id="imgSelect4">
                                            <img src="/landing/home/images/Tareo-lila.png" width="80" class="p-1">
                                        </div>
                                        <button type="button" id="btnSelec4" class="btn btn-second-rh active"
                                            data-toggle="button" aria-pressed="true">
                                            <img src='landing/images/check.svg' width='16'> Seleccionado
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-lg-3 col-sm-6">
                                <div class="card" id="cardSelect4">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><small>MODO:</small><br><strong>Tareo</strong></h5>
                                        <div id="imgSelect4">
                                            <img src="/landing/home/images/Tareo-lila.png" width="80" class="p-1">
                                        </div>
                                        <button type="button" id="btnSelec4" class="btn btn-second-rh"
                                            data-toggle="button" aria-pressed="false">
                                            Seleccionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div style="display: none;">
                            <button type="button" id="btnSelec4" class="btn btn-second-rh active" data-toggle="button"
                                aria-pressed="false">
                                Seleccionar
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-rh" id="btnSelected">Continuar</button>
            </div>
        </div>
    </div>
</div>
{{-- FIN DE SELECCIÓN DE MODOS --}}


{{-- MODAL REPOROCESAR HORARIO POR RANGO DE FECHA --}}
<div id="modalReprocesarHorario" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-scrollable" style="max-width: 900px; margin-top: 50px;">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" style="color:#ffffff;font-size:15px">Reprocesar horarios
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="font-size:12px!important;background: #ffffff;padding-bottom: 0px;">
                <form action="javascript:reprocesarHorariosFecha()">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-9" style="zoom:90%;">
                                    <label for="" style="font-weight: 600;">Seleccionar empleado(s):</label>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="" style="margin-top: 0px;margin-bottom: 1px;">
                                                Seleccionar por:
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row col-md-12">
                                        <select data-plugin="customselect" id="selectEmpresarialRepro"
                                            onchange="changeSelectRepro()" name="selectEmpresarialRepro"
                                            class="form-control" data-placeholder="seleccione">
                                            <option value=""></option>
                                            @foreach ($areaRepro as $areas)
                                                <option value="{{ $areas->idarea }}">Area :
                                                    {{ $areas->descripcion }}.
                                                </option>
                                            @endforeach
                                            @foreach ($cargoRepro as $cargos)
                                                <option value="{{ $cargos->idcargo }}">Cargo :
                                                    {{ $cargos->descripcion }}.
                                                </option>
                                            @endforeach
                                            @foreach ($localRepro as $locales)
                                                <option value="{{ $locales->idlocal }}">Local :
                                                    {{ $locales->descripcion }}.
                                                </option>
                                            @endforeach
                                            @foreach ($nivelRepro as $niveles)
                                                <option value="{{ $niveles->idnivel }}">Nivel :
                                                    {{ $niveles->descripcion }}.
                                                </option>
                                            @endforeach
                                            @foreach ($centrocRepro as $centrocs)
                                                <option value="{{ $centrocs->idcentro }}">Centro costo :
                                                    {{ $centrocs->descripcion }}.
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-5 form-check" style="padding-left: 55px;">
                                            <input type="checkbox" class="form-check-input" id="selectTodoCheckRepro">
                                            <label class="form-check-label" for="selectTodoCheckRepro"
                                                style="margin-top: 2px;font-size: 11px!important">
                                                Seleccionar todos.
                                            </label>
                                        </div>
                                        <div class="col-md-7 text-right">
                                            <span style="font-size: 11px!important">
                                                *Se visualizará empleados con calendario
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="padding-left: 24px;
                                    padding-right: 0px;">
                                        <select class="form-control wide" data-plugin="customselect" multiple
                                            id="nombreEmpleadoRepro">
                                            @foreach ($empleadoRepro as $empleados)
                                                <option value="{{ $empleados->emple_id }}">
                                                    {{ $empleados->perso_nombre }}
                                                    {{ $empleados->perso_apPaterno }}
                                                    {{ $empleados->perso_apMaterno }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                        <div class="col-md-12">
                            <label style="font-weight: 600; font-size: 13px">Seleccione rango de fechas a reprocesar
                                horarios.</label>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-lg-5 col-form-label">Rango de
                                    fechas:</label>
                                <input type="hidden" id="ID_START_Repro">
                                <input type="hidden" id="ID_END_Repro">
                                <div class="input-group col-md-7 text-center"
                                    style="padding-left: 0px;padding-right: 12px;" id="fechaSelecRepro">
                                    <input type="text" class="col-md-12 form-control" data-input>
                                    <div class="input-group-prepend">
                                        <div class="input-group-text form-control flatpickr">
                                            <a class="input-button" data-toggle>
                                                <i class="uil uil-calender"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

            </div>
            <div class="modal-footer" style="background: #ffffff;padding-bottom: 8px;
            padding-top: 8px;">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">Cerrar</button>
                            <button type="submit" name="" style="background-color: #163552;"
                                class="btn btn-sm">Aceptar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    function ingresarOrganiza(idorganiza) {
        $.ajax({
            type: "post",
            async: false,
            url: "/enviarIDorg",
            data: {
                idorganiza
            },
            statusCode: {
                419: function() {
                    location.reload();
                }
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                location.reload();
            },
        });

    }

</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
    let a = 0;
    a = {{ getLastActivity() }};
    console.log(a);
    $(document).ready(function() {
        if (a[0] == 1) {
            // MUESTRA EL MODAL
            $('#modos').modal('show');
        }
    });
    $('#chatJivo').addClass('enabled').click(function() {
        jivo_api.open();
    });

</script>
<script type="text/javascript">
    function cf1() {
        $('#search1').show(300);
        $('#x').show();
        $('#lupa').hide();
    }

    function cf2() {
        $('#search1').hide(300);
        $('#x').hide();
        $('#lupa').show();
        cambiar("");
        $('#search').val("");
    }

    function cf1Inv() {
        $('#search1Inv').show(300);
        $('#xInv').show();
        $('#lupaInv').hide();
    }

    function cf2Inv() {
        $('#search1Inv').hide(300);
        $('#xInv').hide();
        $('#lupaInv').show();
        cambiarInv("");
        $('#searchInv').val("");
    }

    let cadenaE = ["Gestión de empleados", "Gestion de empleados", "Empleados", "Baja", "Empleados de baja"];
    let cadenaH = ["Horarios", "Asignar horarios", "Incidencias", "Matriz de horarios"];
    let cadenaC = ["Calendarios"];
    let cadenaCC = ["Centro de Costo"];
    let cadenaPC = ["Puntos de Control"];
    let cadenaA = ["Gestion de Actividades", "Gestión de actividades", "Actividades", "Subactividades"];
    let cadenaD = ["Dashboard"];
    let cadenaCR = ["Control remoto", "Remoto", "Detalle diario", "Reportes", "Tiempos por semana", "Tiempos por mes",
        "Tardanzas", "Matriz por tandanzas", "Dashboard"
    ];
    let cadenaR = ["Control en ruta", "Control ruta", "Ruta", "Detalle diario", "Reporte semanal", "Tardanzas",
        "Matriz por tandanzas"
    ];
    let cadenaP = ["Asistencia en puerta", "Asistencia puerta", "Puerta", "Dispositivos", "Controladores",
        "Detalle de asistencia", "Asistencia por fecha", "Asistencia por empleado", "Asistencia consolidada",
        "Tardanzas", "Matriz tardanzas"
    ];
    let cadenaT = ["Modo Tareo", "Tareo", "Dispositivos", "Controladores", "Detalle Tareo",
        "Reporte de tareo por fecha", "Reporte de tareo por empleado"
    ];


    function cambiar(texto) {
        texto = texto.toUpperCase();
        let tam = texto.length;
        let bandE = false;
        let bandH = false;
        let bandC = false;
        let bandCC = false;
        let bandPC = false;
        let bandA = false;
        let bandD = false;
        let bandCR = false;
        let bandR = false;
        let bandAP = false;
        let bandT = false;
        let cadena = "";
        if (tam == 0) {
            $('#gestEmpleado').show();
            $('#gestCalendario').show();
            $('#gestHorarios').show();
            $('#gestCentroCosto').show();
            $('#gestPuntosContol').show();
            $('#gestActividades').show();
            $('#gestDashboard').show();
            $('#li_remoto').show();
            $('#li_ruta').show();
            $('#li_puerta').show();
            $('#li_tareo').show();
            $('#li_remotoInv').show();
            $('#li_rutaInv').show();
            $('#li_puertaInv').show();
            $('#li_tareoInv').show();

            $("#gestEmpleado").removeClass("mm-active");
            document.getElementById("gestEmpleado_ul").setAttribute("aria-expanded", false);
            $("#gestEmpleado_ul").removeClass("mm-show");

            $("#li_remoto").removeClass("mm-active");
            document.getElementById("li_remoto_ul").setAttribute("aria-expanded", false);
            $("#li_remoto_ul").removeClass("mm-show");

            $("#li_puerta").removeClass("mm-active");
            document.getElementById("li_puerta").setAttribute("aria-expanded", false);
            $("#li_puerta").removeClass("mm-show");

            $("#li_puerta_ul").removeClass("mm-active");
            document.getElementById("li_puerta_ul").setAttribute("aria-expanded", false);
            $("#li_puerta_ul").removeClass("mm-show");

            $("#li_puerta_ul_ul").removeClass("mm-active");
            document.getElementById("li_puerta_ul_ul").setAttribute("aria-expanded", false);
            $("#li_puerta_ul_ul").removeClass("mm-show");

            $("#li_puerta_ul_ul2").removeClass("mm-active");
            document.getElementById("li_puerta_ul_ul2").setAttribute("aria-expanded", false);
            $("#li_puerta_ul_ul2").removeClass("mm-show");

            $("#li_ruta").removeClass("mm-active");
            document.getElementById("li_ruta_ul").setAttribute("aria-expanded", false);
            $("#li_ruta_ul").removeClass("mm-show");

            $("#li_tareo").removeClass("mm-active");
            document.getElementById("li_tareo_ul").setAttribute("aria-expanded", false);
            $("#li_tareo_ul").removeClass("mm-show");
        } else {
            $('#gestEmpleado').hide();
            $('#gestCalendario').hide();
            $('#gestHorarios').hide();
            $('#gestCentroCosto').hide();
            $('#gestPuntosContol').hide();
            $('#gestActividades').hide();
            $('#gestDashboard').hide();
            $('#li_remoto').hide();
            $('#li_ruta').hide();
            $('#li_puerta').hide();
            $('#li_tareo').hide();

            for (let i = 0; cadenaE.length > i; i++) {
                cadena = cadenaE[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandE = true;
                }
            }

            for (let i = 0; cadenaH.length > i; i++) {
                cadena = cadenaH[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandH = true;
                }
            }

            for (let i = 0; cadenaC.length > i; i++) {
                cadena = cadenaC[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandC = true;
                }
            }

            for (let i = 0; cadenaCC.length > i; i++) {
                cadena = cadenaCC[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandCC = true;
                }
            }

            for (let i = 0; cadenaPC.length > i; i++) {
                cadena = cadenaPC[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandPC = true;
                }
            }

            for (let i = 0; cadenaA.length > i; i++) {
                cadena = cadenaA[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandA = true;
                }
            }

            for (let i = 0; cadenaD.length > i; i++) {
                cadena = cadenaD[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandD = true;
                }
            }

            for (let i = 0; cadenaCR.length > i; i++) {
                cadena = cadenaCR[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandCR = true;
                }
            }

            for (let i = 0; cadenaR.length > i; i++) {
                cadena = cadenaR[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandR = true;
                }
            }

            for (let i = 0; cadenaP.length > i; i++) {
                cadena = cadenaP[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandAP = true;
                }
            }

            for (let i = 0; cadenaT.length > i; i++) {
                cadena = cadenaT[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandT = true;
                }
            }

            if (bandE == true) {
                $("#gestEmpleado").addClass("mm-active");
                document.getElementById("gestEmpleado_ul").setAttribute("aria-expanded", true);
                $("#gestEmpleado_ul").addClass("mm-show");
                $('#gestEmpleado').show();
            }
            if (bandH == true) {
                $("#gestHorarios").addClass("mm-active");
                document.getElementById("gestHorarios_ul").setAttribute("aria-expanded", true);
                $("#gestHorarios_ul").addClass("mm-show");
                $('#gestHorarios').show();
            }
            if (bandC == true) {
                $('#gestCalendario').show();
            }
            if (bandCC == true) {
                $('#gestCentroCosto').show();
            }
            if (bandPC == true) {
                $('#gestPuntosContol').show();
            }
            if (bandA == true) {
                $("#gestActividades").addClass("mm-active");
                document.getElementById("gestActividades_ul").setAttribute("aria-expanded", true);
                $("#gestActividades_ul").addClass("mm-show");
                $('#gestActividades').show();
            }
            if (bandD == true) {
                $('#gestDashboard').show();
            }
            if (bandCR == true) {
                $("#li_remoto").addClass("mm-active");
                document.getElementById("li_remoto_ul").setAttribute("aria-expanded", true);
                $("#li_remoto_ul").addClass("mm-show");
                document.getElementById("li_remoto_ul_ul").setAttribute("aria-expanded", true);
                $("#li_remoto_ul_ul").addClass("mm-show");
                $('#li_remoto').show();
            }
            if (bandR == true) {
                $("#li_ruta").addClass("mm-active");
                document.getElementById("li_ruta_ul").setAttribute("aria-expanded", true);
                $("#li_ruta_ul").addClass("mm-show");
                $('#li_ruta').show();
            }
            if (bandAP == true) {
                $("#li_puerta").addClass("mm-active");
                document.getElementById("li_puerta_ul").setAttribute("aria-expanded", true);
                $("#li_puerta_ul").addClass("mm-show");
                document.getElementById("li_puerta_ul_ul").setAttribute("aria-expanded", true);
                $("#li_puerta_ul_ul").addClass("mm-show");
                document.getElementById("li_puerta_ul_ul2").setAttribute("aria-expanded", true);
                $("#li_puerta_ul_ul2").addClass("mm-show");
                $('#li_puerta').show();
            }
            if (bandT == true) {
                $("#li_tareo").addClass("mm-active");
                document.getElementById("li_tareo_ul").setAttribute("aria-expanded", true);
                $("#li_tareo_ul").addClass("mm-show");
                $('#li_tareo').show();
            }
        }
        console.log(texto.length);
    }

    function cambiarInv(texto) {
        texto = texto.toUpperCase();
        let tam = texto.length;
        let bandE = false;
        let bandC = false;
        let bandA = false;
        let bandD = false;
        let bandCR = false;
        let bandR = false;
        let bandAP = false;
        let bandT = false;
        let cadena = "";
        if (tam == 0) {
            $('#gestEmpleado').show();
            $('#gestCalendario').show();
            $('#gestActividades').show();
            $('#gestDashboard').show();
            $('#li_remotoInv').show();
            $('#li_rutaInv').show();
            $('#li_puertaInv').show();
            $('#li_tareoInv').show();

            $("#gestEmpleado").removeClass("mm-active");
            if ($("#gestEmpleado_ul").val() != null) {
                document.getElementById("gestEmpleado_ul").setAttribute("aria-expanded", false);
            }
            $("#gestEmpleado_ul").removeClass("mm-show");

            $("#li_remotoInv").removeClass("mm-active");
            if ($("#li_remotoInv_ul").val() != null) {
                document.getElementById("li_remotoInv_ul").setAttribute("aria-expanded", false);
            }
            $("#li_remotoInv_ul").removeClass("mm-show");

            $("#li_puertaInv").removeClass("mm-active");
            if ($("#li_puertaInv").val() != null) {
                document.getElementById("li_puertaInv").setAttribute("aria-expanded", false);
            }
            $("#li_puertaInv").removeClass("mm-show");

            $("#li_puertaInv_ul").removeClass("mm-active");
            if ($("#li_puertaInv_ul").val() != null) {
                document.getElementById("li_puertaInv_ul").setAttribute("aria-expanded", false);
            }
            $("#li_puertaInv_ul").removeClass("mm-show");

            $("#li_puertaInv_ul_ul").removeClass("mm-active");
            if ($("#li_puertaInv_ul_ul").val() != null) {
                document.getElementById("li_puertaInv_ul_ul").setAttribute("aria-expanded", false);
            }
            $("#li_puertaInv_ul_ul").removeClass("mm-show");

            $("#li_puertaInv_ul_ul2").removeClass("mm-active");
            if ($("#li_puertaInv_ul_ul2").val() != null) {
                document.getElementById("li_puertaInv_ul_ul2").setAttribute("aria-expanded", false);
            }
            $("#li_puertaInv_ul_ul2").removeClass("mm-show");

            $("#li_rutaInv").removeClass("mm-active");
            if ($("#li_rutaInv_ul").val() != null) {
                document.getElementById("li_rutaInv_ul").setAttribute("aria-expanded", false);
            }
            $("#li_rutaInv_ul").removeClass("mm-show");

            $("#li_tareoInv").removeClass("mm-active");
            if ($("#li_tareoInv_ul").val() != null) {
                document.getElementById("li_tareoInv_ul").setAttribute("aria-expanded", false);
            }
            $("#li_tareoInv_ul").removeClass("mm-show");
        } else {
            $('#gestEmpleado').hide();
            $('#gestCalendario').hide();
            $('#gestActividades').hide();
            $('#gestDashboard').hide();
            $('#li_remotoInv').hide();
            $('#li_rutaInv').hide();
            $('#li_puertaInv').hide();
            $('#li_tareoInv').hide();

            for (let i = 0; cadenaE.length > i; i++) {
                cadena = cadenaE[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandE = true;
                }
            }


            for (let i = 0; cadenaC.length > i; i++) {
                cadena = cadenaC[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandC = true;
                }
            }

            for (let i = 0; cadenaA.length > i; i++) {
                cadena = cadenaA[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandA = true;
                }
            }

            for (let i = 0; cadenaD.length > i; i++) {
                cadena = cadenaD[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandD = true;
                }
            }

            for (let i = 0; cadenaCR.length > i; i++) {
                cadena = cadenaCR[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandCR = true;
                }
            }

            for (let i = 0; cadenaR.length > i; i++) {
                cadena = cadenaR[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandR = true;
                }
            }

            for (let i = 0; cadenaP.length > i; i++) {
                cadena = cadenaP[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandAP = true;
                }
            }

            for (let i = 0; cadenaT.length > i; i++) {
                cadena = cadenaT[i].toUpperCase();
                let sub = cadena.substr(0, tam)
                if (texto == sub) {
                    bandT = true;
                }
            }

            if (bandE == true) {
                $("#gestEmpleado").addClass("mm-active");
                if ($("#gestEmpleado_ul").val() != null) {
                    document.getElementById("gestEmpleado_ul").setAttribute("aria-expanded", true);
                }
                $("#gestEmpleado_ul").addClass("mm-show");
                $('#gestEmpleado').show();
            }
            if (bandC == true) {
                $('#gestCalendario').show();
            }
            if (bandA == true) {
                $("#gestActividades").addClass("mm-active");
                if ($("#gestActividades_ul").val() != null) {
                    document.getElementById("gestActividades_ul").setAttribute("aria-expanded", true);
                }
                $("#gestActividades_ul").addClass("mm-show");
                $('#gestActividades').show();
            }
            if (bandD == true) {
                $('#gestDashboard').show();
            }
            if (bandCR == true) {
                $("#li_remotoInv").addClass("mm-active");
                if ($("#li_remotoInv").val() != null) {
                    document.getElementById("li_remotoInv_ul").setAttribute("aria-expanded", true);
                }
                $("#li_remotoInv_ul").addClass("mm-show");
                if ($("#li_remotoInv_ul_ul").val() != null) {
                    document.getElementById("li_remotoInv_ul_ul").setAttribute("aria-expanded", true);
                }
                $("#li_remotoInv_ul_ul").addClass("mm-show");
                $('#li_remotoInv').show();
            }
            if (bandR == true) {
                $("#li_rutaInv").addClass("mm-active");
                if ($("#li_rutaInv_ul").val() != null) {
                    document.getElementById("li_rutaInv_ul").setAttribute("aria-expanded", true);
                }
                $("#li_rutaInv_ul").addClass("mm-show");
                $('#li_rutaInv').show();
            }
            if (bandAP == true) {
                $("#li_puertaInv").addClass("mm-active");
                document.getElementById("li_puertaInv_ul").setAttribute("aria-expanded", true);
                $("#li_puertaInv_ul").addClass("mm-show");
                if ($("#li_puertaInv_ul_ul").val() != null) {
                    document.getElementById("li_puertaInv_ul_ul").setAttribute("aria-expanded", true);
                }
                $("#li_puertaInv_ul_ul").addClass("mm-show");
                if ($("#li_puertaInv_ul_ul2").val() != null) {
                    document.getElementById("li_puertaInv_ul_ul2").setAttribute("aria-expanded", true);
                }
                $("#li_puertaInv_ul_ul2").addClass("mm-show");
                $('#li_puertaInv').show();
            }
            if (bandT == true) {
                $("#li_tareoInv").addClass("mm-active");
                if ($("#li_tareoInv_ul").val() != null) {
                    document.getElementById("li_tareoInv_ul").setAttribute("aria-expanded", true);
                }
                $("#li_tareoInv_ul").addClass("mm-show");
                $('#li_tareoInv').show();
            }
        }
        console.log(texto.length);
    }

    function reporocesarHorario() {
        fmes = new Date();

        var inicioC = moment(fmes).startOf('month').format('YYYY-MM-DD');
        var finC = moment(fmes).endOf('month').format('YYYY-MM-DD');

        $('#ID_START_Repro').val(inicioC);
        $('#ID_END_Repro').val(finC);
        $("#selectTodoCheckRepro").prop("checked", false);
        $("#selectEmpresarialRepro > option").prop("selected", false);
        $("#selectEmpresarialRepro").trigger("change");

        //* ELEGIR FECHA PARA REPROCESAR HORARIOS */
        var fechaValue = $("#fechaSelecRepro").flatpickr({
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F",
            locale: "es",
            wrap: true,
            allowInput: true,
            conjunction: " a ",
            minRange: 1,

            onChange: function(selectedDates) {
                var _this = this;
                var dateArr = selectedDates.map(function(date) {
                    return _this.formatDate(date, 'Y-m-d');
                });
                $('#ID_START_Repro').val(dateArr[0]);
                $('#ID_END_Repro').val(dateArr[1]);



            },
            defaultDate: [inicioC, finC],
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length == 1) {
                    var fm = moment(selectedDates[0]).add("day", -1).format("YYYY-MM-DD");
                    instance.setDate([fm, selectedDates[0]], true);
                }
            }
        });
        $('#modalReprocesarHorario').modal('show');


    };

    function reprocesarHorariosFecha() {

        let inicio = $('#ID_START_Repro').val();
        let fin = $('#ID_END_Repro').val();
        let idEmpsRepro= $('#nombreEmpleadoRepro').val();
        $.ajax({
            type: "get",
            async: false,
            url: "/reprocesarHorario",
            data: {
                inicio,
                fin, idEmpsRepro
            },
            statusCode: {
                419: function() {
                    location.reload();
                }
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                $('#modalReprocesarHorario').modal('hide');

            },
        });

    };
    //seleccionar por area, cargo, etc

    $("#selectTodoCheckRepro").click(function() {
        if ($("#selectTodoCheckRepro").is(':checked')) {

            $("#nombreEmpleadoRepro > option").prop("selected", "selected");
            $("#nombreEmpleadoRepro").trigger("change");

        } else {
            $("#nombreEmpleadoRepro > option").prop("selected", false);
            $("#nombreEmpleadoRepro").trigger("change");

        }
    });
    function changeSelectRepro() {

            var idempresarial = [];
            idempresarial = $('#selectEmpresarialRepro').val();
            textSelec = $('select[name="selectEmpresarialRepro"] option:selected:last').text();
            textSelec2 = $('select[name="selectEmpresarialRepro"] option:selected:last').text();
            /*  palabrasepara=textSelec2.split('.')[0];
             alert(palabrasepara);
             return false; */
            palabraEmpresarial = textSelec.split(' ')[0];
            $("#nombreEmpleadoRepro > option").prop("selected", false);
            $("#nombreEmpleadoRepro").trigger("change");
            if (palabraEmpresarial == 'Area') {
                $.ajax({
                    type: "post",
                    url: "/horario/empleArea",
                    data: {
                        idarea: idempresarial
                    },
                    statusCode: {

                        419: function() {
                            location.reload();
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {

                        $.each(data, function(index, value) {
                            $("#nombreEmpleadoRepro > option[value='" + value.emple_id + "']").prop(
                                "selected", "selected");

                        });
                        $("#nombreEmpleadoRepro").trigger("change");


                    },
                    error: function(data) {
                        console.log('Ocurrio un error');
                    }
                });
            }
            if (palabraEmpresarial == 'Cargo') {
                $.ajax({
                    type: "post",
                    url: "/horario/empleCargo",
                    data: {
                        idcargo: idempresarial
                    },
                    statusCode: {

                        419: function() {
                            location.reload();
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {

                        $.each(data, function(index, value) {
                            $("#nombreEmpleadoRepro > option[value='" + value.emple_id + "']").prop(
                                "selected", "selected");

                        });
                        $("#nombreEmpleadoRepro").trigger("change");


                    },
                    error: function(data) {
                        console.log('Ocurrio un error');
                    }
                });
            }

            if (palabraEmpresarial == 'Local') {
                $.ajax({
                    type: "post",
                    url: "/horario/empleLocal",
                    data: {
                        idlocal: idempresarial
                    },
                    statusCode: {

                        419: function() {
                            location.reload();
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {

                        $.each(data, function(index, value) {
                            $("#nombreEmpleadoRepro > option[value='" + value.emple_id + "']").prop(
                                "selected", "selected");

                        });
                        $("#nombreEmpleadoRepro").trigger("change");


                    },
                    error: function(data) {
                        console.log('Ocurrio un error');
                    }
                });
            }

            //*nivel
            if (palabraEmpresarial == 'Nivel') {
                $.ajax({
                    type: "post",
                    url: "/horario/empleNivel",
                    data: {
                        idnivel: idempresarial
                    },
                    statusCode: {

                        419: function() {
                            location.reload();
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {

                        $.each(data, function(index, value) {
                            $("#nombreEmpleadoRepro > option[value='" + value.emple_id + "']").prop(
                                "selected", "selected");

                        });
                        $("#nombreEmpleadoRepro").trigger("change");


                    },
                    error: function(data) {
                        console.log('Ocurrio un error');
                    }
                });
            }

            //*centro costo
            if (palabraEmpresarial == 'Centro') {
                $.ajax({
                    type: "post",
                    url: "/horario/empleCentro",
                    data: {
                        idcentro: idempresarial
                    },
                    statusCode: {

                        419: function() {
                            location.reload();
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {

                        $.each(data, function(index, value) {
                            $("#nombreEmpleadoRepro > option[value='" + value.emple_id + "']").prop(
                                "selected", "selected");

                        });
                        $("#nombreEmpleadoRepro").trigger("change");


                    },
                    error: function(data) {
                        console.log('Ocurrio un error');
                    }
                });
            }


        };

    ///

</script>
<script src="{{ asset('landing/home/js/modos.js') }}"></script>
@section('script')
    <script src="{{ asset('landing/js/editarPerfil.js') }}"></script>
@endsection
