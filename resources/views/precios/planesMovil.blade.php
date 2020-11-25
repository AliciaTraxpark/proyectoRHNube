@extends('layouts.vertical')


@section('css')
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet"
    type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('content')
<style>
    table {
        border: none;
    }

    .table thead th {
        border-bottom: none;
        vertical-align: top;
        padding-bottom: 0.2rem;
        padding-top: 0.2rem;
    }

    .table th {
        padding-bottom: 0.2rem;
        padding-top: 0.2rem;
    }

    .table th,
    .table td {
        border-top: none;
    }

    .divContainer {
        border-radius: 5px;
        /* box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02); */
    }

    .pyme {
        border-top: 5px solid rgb(34, 193, 195);
    }

    .profesional {
        border-top: 5px solid rgb(43, 89, 187);
    }

    .enterprise {
        border-top: 5px solid rgb(51, 75, 125);
    }

    .titulo {
        padding-top: 0.5rem;
        font-weight: bold;
    }

    .tituloPyme {
        color: rgba(68, 177, 204, 1);
    }

    .tituloProfesional {
        color: rgba(60, 111, 156, 1);
    }

    .tituloEnterprise {
        color: rgba(64, 112, 136, 1);
    }

    .tituloPrincipal {
        background: rgb(2, 0, 36);
        background: linear-gradient(90deg, rgba(2, 0, 36, 1) 0%, rgba(9, 9, 121, 1) 100%, rgba(0, 212, 255, 1) 100%);
        color: #ffffff;
        /* border-radius: 5px; */
        width: 1%;
        vertical-align: middle !important;
    }

    .tituloRotar {
        writing-mode: vertical-lr;
        transform: rotate(180deg);
    }

    .table td {
        padding-bottom: 0.2rem;
        padding-top: 0.2rem;
    }

    .btn-custom {
        background: rgb(34, 193, 195);
        background: linear-gradient(0deg, rgba(34, 193, 195, 1) 0%, rgba(68, 177, 204, 1) 100%);
        color: #fff;
        border-radius: 20px
    }

    .btn-custom:hover {
        background: #fff;
        color: #44b1cc;
        border-color: #44b1cc;
        border-radius: 20px;
        -webkit-transition: color 0.5s ease-in-out;
        transition: color 0.5s ease-in-out;
    }

    .btn-customD {
        background: rgb(43, 89, 187);
        background: linear-gradient(0deg, rgba(43, 89, 187, 1) 0%, rgba(60, 111, 156, 1) 100%);
        color: #fff;
        border-radius: 20px
    }

    .btn-customD:hover {
        color: #3c6f9c;
        background: #fff;
        border-radius: 20px;
        border-color: #3c6f9c;
    }

    .btn-customT {
        background: rgb(51, 75, 125);
        background: linear-gradient(0deg, rgba(51, 75, 125, 1) 0%, rgba(64, 112, 136, 1) 100%);
        color: #fff;
        border-radius: 20px
    }

    .btn-customT:hover {
        color: #407088;
        background: #fff;
        border-radius: 20px;
        border-color: #407088;
    }

    @media (max-width: 767.98px) {
        table {
            font-size: 0.8rem !important;
        }

        .rowResponsive {
            padding: 0% !important;
            padding-top: 5% !important;
        }

        .imgIc {
            height: 13px !important;
        }

        .textR {
            font-weight: 500 !important;
        }

        .table th {
            padding: 0.2rem !important;
        }

        .thTitulo {
            padding: 0% !important;
        }

        .imgU {
            height: 25px !important;
        }

        .pImg {
            margin: 0% !important;
        }
        .btn-sm{
            font-size: 9px !important;
        }

        .p-btn{
            margin-top: 0.2rem !important;
        }
    }
</style>
<div class="row p-5 rowResponsive">
    <div class="col-md-12">
        <table id="planesMovil" class="table nowrap" style="font-size: 13px;width:100%;">
            <thead>
                <tr class="pt-3">
                    <th class="tituloPrincipal thTitulo">
                        <span class="tituloRotar">
                            Planes y precios
                            <img src="{{asset('/landing/images/peru (1).svg')}}" height="17" class="mr-1">
                        </span>
                    </th>
                    <th class="text-center" style="border-bottom: 2px solid rgb(34, 193, 195);">
                        <div class="divContainer pyme">
                            <p class="titulo tituloPyme">PYME</p>
                            <h4>$3</h4>
                            <p class="pImg">
                                <img src="{{asset('/landing/images/multitud-de-usuarios.svg')}}" height="30"
                                    class="imgU">
                            </p>
                            <p class="p-btn">
                                <button type="button" class="btn btn-sm btn-custom"
                                    style="font-size: 11px;font-weight: bold">SUSCRIBIRSE</button>
                            </p>
                            <span class="textR">Hasta 200 emp. &nbsp;&nbsp; (*c)</span>
                        </div>
                    </th>
                    <th class="text-center" style="border-bottom: 2px solid rgb(43, 89, 187);">
                        <div class="divContainer profesional">
                            <p class="titulo tituloProfesional">PROFESIONAL</p>
                            <h4>$2.5</h4>
                            <p class="pImg">
                                <img src="{{asset('/landing/images/multitud-de-usuarios (1).svg')}}" height="30"
                                    class="imgU">
                            </p>
                            <p class="p-btn">
                                <button type="button" class="btn btn-sm btn-customD"
                                    style="font-size: 11px;font-weight: bold">SUSCRIBIRSE</button>
                            </p>
                            <span class="textR">De 200 a 5000 emp.(*c)</span>
                        </div>
                    </th>
                    <th class="text-center" style="border-bottom: 2px solid rgb(51, 75, 125)">
                        <div class="divContainer enterprise">
                            <p class="titulo tituloEnterprise">ENTERPRISE</p>
                            <h4>$2</h4>
                            <p class="pImg">
                                <img src="{{asset('/landing/images/multitud-de-usuarios (2).svg')}}" height="30"
                                    class="imgU">
                            </p>
                            <p class="p-btn">
                                <button type="button" class="btn btn-sm btn-customT"
                                    style="font-size: 11px;font-weight: bold">SUSCRIBIRSE</button>
                            </p>
                            <span class="textR">Desde 5001 emp. &nbsp; (*c)</span>
                        </div>
                    </th>
                </tr>
                <tr align="center">
                    <th rowspan="7" class="tituloPrincipal thTitulo">
                        <span class="tituloRotar">
                            Modalidad de control
                        </span>
                    </th>
                </tr>
                <tr>
                    <td class="text-center"><span>$2.50 un pago semestral</span></td>
                    <td class="text-center"><span>$2.00 un pago semestral</span></td>
                    <td class="text-center"><span>$1.80 un pago semestral</span></td>
                </tr>
                <tr>
                    <td class="text-center"><span>$2.00 un pago anual</span></td>
                    <td class="text-center"><span>$1.80 un pago anual</span></td>
                    <td class="text-center"><span>$1.50 un pago anual</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Administrador de
                        personal</th>
                </tr>
                <tr>
                    <td class="text-center"><span>Ilimitado</span></td>
                    <td class="text-center"><span>Ilimitado</span></td>
                    <td class="text-center"><span>Ilimitado</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="8" class="text-center" style="background:#f1f6f9;color:#394867">
                        Multiempresa</th>
                </tr>
                <tr align="center">
                    <td class="text-center" style="border-bottom: 2px solid rgb(34, 193, 195);">
                        <span>5$</span>
                    </td>
                    <td class="text-center" style="border-bottom: 2px solid rgb(43, 89, 187);"><span>Ilimitado</span>
                    </td>
                    <td class="text-center" style="border-bottom: 2px solid rgb(51, 75, 125)"><span>Ilimitado</span>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th rowspan="23" class="tituloPrincipal thTitulo">
                        <span class="tituloRotar">
                            Modo: Control Remoto / Home and office
                        </span>
                    </th>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Captura de actividad diaria
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Control normal</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Calidad de captura
                    </th>
                </tr>
                <tr>
                    <td class="text-center"><span>Estándar</span></td>
                    <td class="text-center"><span>Estándar</span></td>
                    <td class="text-center"><span>Estándar</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Eliminación de
                        capturas (*e)</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mb-1" class="imgIc">
                        </span>
                    </td>
                    <td class="text-center"><span>50$ x empresa</span></td>
                    <td class="text-center"><span>50$ x empresa</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Permitir actividad
                        fuera de horario</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Control intensivo
                        (cada 5min)</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mb-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center"><span>2 emp.</span></td>
                    <td class="text-center"><span>10 emp.</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Control
                        superintensivo (cada1min)</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mb-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center"><span>1 emp.</span></td>
                    <td class="text-center"><span>1 emp.</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Control de tareas
                        diarias</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Control de
                        justificaciones por horas</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Control de
                        justificaciones por días</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Capturas en video
                        basic (*v)Aleatorio</th>
                </tr>
                <tr>
                    <td class="text-center" style="border-bottom: 2px solid rgb(34, 193, 195);">
                        <span>
                            <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mb-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center" style="border-bottom: 2px solid rgb(43, 89, 187);"><span>2 emp.</span></td>
                    <td class="text-center" style="border-bottom: 2px solid rgb(51, 75, 125)"><span>4 emp.</span></td>
                </tr>
                <tr>
                    <th rowspan="17" class="tituloPrincipal thTitulo">
                        <span class="tituloRotar">
                            Modo: Asistencia en puerta
                        </span>
                    </th>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Registro de ingresos y salidas
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Escaneo del DNI o fotocheck
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">
                        Función manual
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">
                        Función por cámara
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Función para escaner de barras bluetooth (*L)
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="imgIc">
                        </span>
                    </td>
                    <td class="text-center"><span>Inc. 1 escáner BT (*L)</span></td>
                    <td class="text-center"><span>Inc. 3 escáner BT (*L)</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Función biométrica (reconocimiento facial)
                    </th>
                </tr>
                <tr>
                    <td class="text-center"><span>Próximamente</span></td>
                    <td class="text-center"><span>Próximamente</span></td>
                    <td class="text-center"><span>Próximamente</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Ubicación GPS del control
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Fijación de la ubicación de control, con GEOMALLAS tipo radial
                    </th>
                </tr>
                <tr>
                    <td class="text-center" style="border-bottom: 2px solid rgb(34, 193, 195);">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center" style="border-bottom: 2px solid rgb(43, 89, 187);">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center" style="border-bottom: 2px solid rgb(51, 75, 125)">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th rowspan="21" class="tituloPrincipal thTitulo">
                        <span class="tituloRotar">
                            Modo: Asistencia y actividad en ruta
                        </span>
                    </th>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Captura de actividad diaria
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Control normal (cada 15min)
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Captura ubicación GPS
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Permitir actividad fuera de horario
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="imgIc">
                        </span>
                    </td>
                    <td class="text-center"><span>Inc. 1 escáner BT (*L)</span></td>
                    <td class="text-center"><span>Inc. 3 escáner BT (*L)</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Comprobación de identidad x DNI x fotocheck
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">
                        Función manual
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">
                        Función por cámara
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Ubicación GPS del control
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Fijación de la ubicación de control, con GEOMALLAS tipo radial
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Muestreo biométrico (reconocimiento facial)
                    </th>
                </tr>
                <tr>
                    <td class="text-center"><span>Próximamente</span></td>
                    <td class="text-center"><span>Próximamente</span></td>
                    <td class="text-center"><span>Próximamente</span></td>
                </tr>
                <tr>
                    <th class="tituloPrincipal thTitulo">
                        <span class="tituloRotar">
                            Modo:Control en ruta
                        </span>
                    </th>
                    <th class="text-center" style="border-bottom: 2px solid rgb(34, 193, 195);">
                        <div class="divContainer pyme">
                            <p class="titulo tituloPyme">Proximamente</p>
                            <p class="pImg">
                                <img src="{{asset('/landing/images/peru.svg')}}" height="30" class="imgU">
                            </p>
                            <h6 class="titulo tituloPyme">Disponible en Android</h6>
                            <span class="textR">Disponible a partir de diciembre</span>
                        </div>
                    </th>
                    <th class="text-center" style="border-bottom: 2px solid rgb(43, 89, 187);">
                        <div class="divContainer profesional">
                            <p class="titulo tituloProfesional">Proximamente</p>
                            <p class="pImg">
                                <img src="{{asset('/landing/images/peru.svg')}}" height="30" class="imgU">
                            </p>
                            <h6 class="titulo tituloProfesional">Disponible en Android</h6>
                            <span class="textR">Disponible a partir de diciembre</span>
                        </div>
                    </th>
                    <th class="text-center" style="border-bottom: 2px solid rgb(51, 75, 125)">
                        <div class="divContainer enterprise">
                            <p class="titulo tituloEnterprise">Proximamente</p>
                            <p class="pImg">
                                <img src="{{asset('/landing/images/peru.svg')}}" height="30" class="imgU">
                            </p>
                            <h6 class="titulo tituloEnterprise">Disponible en Android</h6>
                            <span class="textR">Disponible a partir de diciembre</span>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th rowspan="11" class="tituloPrincipal thTitulo">
                        <span class="tituloRotar">
                            Modo: Asistencia en Puerta
                        </span>
                    </th>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        En dispositivos Android
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Biométricos ZKTECO
                    </th>
                </tr>
                <tr>
                    <td class="text-center"><span>Próximamente</span></td>
                    <td class="text-center"><span>Próximamente</span></td>
                    <td class="text-center"><span>Próximamente</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Biométricos Suprema V1 y V2
                    </th>
                </tr>
                <tr>
                    <td class="text-center"><span>Próximamente</span></td>
                    <td class="text-center"><span>Próximamente</span></td>
                    <td class="text-center"><span>Próximamente</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center"></th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center"></th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th rowspan="12" class="tituloPrincipal thTitulo">
                        <span class="tituloRotar">
                            Opciones de pago
                        </span>
                    </th>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background: rgb(63,94,251);
                    background: radial-gradient(circle, rgba(63,94,251,1) 63%, rgba(254,254,254,1) 100%);
                        color: #ffffff;">
                        CUANDO REQUIERE MAYOR AUDITORÍA A UN EMPLEADO ESPECÍFICO (MENSUAL)
                    </th>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Control intensivo
                    </th>
                </tr>
                <tr>
                    <td class="text-center"><span>$1.50</span></td>
                    <td class="text-center"><span>$1.50</span></td>
                    <td class="text-center"><span>$1.50</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Control superintensivo
                    </th>
                </tr>
                <tr>
                    <td class="text-center"><span>$3.00</span></td>
                    <td class="text-center"><span>$3.00</span></td>
                    <td class="text-center"><span>$3.00</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Capturas en video basic (*) Aleatorio
                    </th>
                </tr>
                <tr>
                    <td class="text-center"><span>$20</span></td>
                    <td class="text-center"><span>$20</span></td>
                    <td class="text-center"><span>$20</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Calidad de captura HD
                    </th>
                </tr>
                <tr>
                    <td class="text-center"><span>$1.99</span></td>
                    <td class="text-center"><span>$1.99</span></td>
                    <td class="text-center"><span>$1.99</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Reconocimiento facial
                    </th>
                </tr>
                <tr>
                    <td class="text-center" style="border-bottom: 2px solid rgb(34, 193, 195);"><span>$3.00</span></td>
                    <td class="text-center" style="border-bottom: 2px solid rgb(43, 89, 187);"><span>$2.50</span></td>
                    <td class="text-center" style="border-bottom: 2px solid rgb(51, 75, 125)"><span>$2.00</span></td>
                </tr>
                <tr>
                    <th rowspan="6" class="tituloPrincipal thTitulo">
                        <span class="tituloRotar">
                            Facturación
                        </span>
                    </th>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Factura de origen, USA
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f1f6f9;color:#394867">
                        Facturación local (*f)
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1 imgIc">
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row p-2 rowResponsive">
    <div class="col-md-12 pl-5 pr-5">
        <p style="color:#797a7e;text-align: justify">
            <strong style="color: #000839;">(*c)</strong>&nbsp;Cantidad de empleados al mes.
        </p>
    </div>
    <div class="col-md-12 pl-5 pr-5">
        <p style="color:#797a7e;text-align: justify">
            <strong style="color: #000839;">(*e)</strong>&nbsp;La eliminación de capturas está permitido a través de una
            transacción auditada en donde se indica el motivo de la eliminación del registro. La eliminación de un
            registro se sustentado en la protección de los derechos de la intimidad de los empleados, ya que la
            plataforma pudo haber captado de manera fortuita una actividad de índole personal dentro del horario
            laboral.
        </p>
    </div>
    <div class="col-md-12 pl-5 pr-5">
        <p style="color:#797a7e;text-align: justify">
            <strong style="color: #000839;">(*v)</strong>&nbsp;Las tomas de video tienen una duración de 1 minuto por
            cada 10min y el muestreo es aleatorio.
        </p>
    </div>
    <div class="col-md-12 pl-5 pr-5">
        <p style="color:#797a7e;text-align: justify">
            <strong style="color: #000839;">(*L)</strong>&nbsp; Las lectoras bluetooth de códigos de barra, son equipos
            recomendados para manejar altos volúmenes de personal, el usuario puede comprarlas localmente y anexarlas al
            celular. RH nube incluye lectoras en los siguientes casos:

        </p>
        <p style="color:#797a7e;text-align: justify" class="ml-4">
            <img src="{{asset('landing/images/right-arrow (1).svg')}}" height="15" class="mr-1 ml-2">
            <strong style="color: #000839;">Caso 1:</strong>&nbsp; Suscripción Profresional, incluye 1unidad por una
            suscripción semestral o anual.

        </p>
        <p style="color:#797a7e;text-align: justify" class="ml-4">
            <img src="{{asset('landing/images/right-arrow (1).svg')}}" height="15" class="mr-1 ml-2">
            <strong style="color: #000839;">Caso 2:</strong>&nbsp; Suscripción Enterprise, incluye 3 unidades.

        </p>
    </div>
    <div class="col-md-12 pl-5 pr-5">
        <p style="color:#797a7e;text-align: justify">
            <strong style="color: #000839;">(*u)</strong>&nbsp;La facturación de RH nube es reconocida como invoice
            comercial válido en la declaración de impuestos en Perú.
            <span style="font-style: oblique">(consultar con su departamento de contabilidad)</span>
        </p>
    </div>
    <div class="col-md-12 pl-5 pr-5">
        <p style="color:#797a7e;text-align: justify">
            <strong style="color: #000839;">(*f)</strong>&nbsp; Facturación local, está disponible a través de un
            partnert en Perú. Los conceptos que se adicionan son:
        </p>
        <p style="color:#797a7e;text-align: justify" class="ml-4">
            <img src="{{asset('landing/images/right-arrow (1).svg')}}" height="15" class="mr-1 ml-2">
            Gastos administrativos (10%)
        </p>
        <p style="color:#797a7e;text-align: justify" class="ml-4">
            <img src="{{asset('landing/images/right-arrow (1).svg')}}" height="15" class="mr-1 ml-2">
            I.G.V(18%)
        </p>
    </div>
</div>
@endsection
@section('script')
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
<script src="{{asset('landing/js/app-menu.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<!-- optional plugins -->
<script src="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js')}}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
@endsection
