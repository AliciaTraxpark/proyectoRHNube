@extends('layouts.vertical')


@section('css')
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet"
    type="text/css" />
@endsection
@section('content')
<style>
    table {
        border: none;
    }

    .table thead th {
        /* border-bottom: none; */
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
        background: #111d5e;
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

    @media (max-width: 767.98px) {
        table {
            font-size: 0.8rem !important;
        }

        .rowResponsive {
            padding: 0% !important;
            padding-top: 5% !important;
        }

        img {
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
    }
</style>
<div class="row p-5 rowResponsive">
    <div class="col-md-12">
        <table id="planesMovil" class="table nowrap" style="font-size: 13px;width:100%;">
            <thead>
                <tr class="pt-3" style="border-bottom: 1.8px dashed #5369F8;">
                    <th class="tituloPrincipal thTitulo">
                        <span class="tituloRotar">
                            Planes y precios
                            <img src="{{asset('/landing/images/peru (1).svg')}}" height="17" class="mr-1">
                        </span>
                    </th>
                    <th class="text-center">
                        <div class="divContainer pyme">
                            <p class="titulo tituloPyme">PYME</p>
                            <h4>$3</h4>
                            <span class="textR">Hasta 200 empleados al mes</span>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="divContainer profesional">
                            <p class="titulo tituloProfesional">PROFESIONAL</p>
                            <h4>$3</h4>
                            <span class="textR">Hasta 200 empleados al mes</span>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="divContainer enterprise">
                            <p class="titulo tituloEnterprise">ENTERPRISE</p>
                            <h4>$3</h4>
                            <span class="textR">Hasta 200 empleados al mes</span>
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
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">Administrador de
                        personal</th>
                </tr>
                <tr>
                    <td class="text-center"><span>Ilimitado</span></td>
                    <td class="text-center"><span>Ilimitado</span></td>
                    <td class="text-center"><span>Ilimitado</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="8" class="text-center" style="background:#f5f5f5">Multiempresa</th>
                </tr>
                <tr align="center" style="border-bottom: 1.8px dashed #5369F8;">
                    <td class="text-center">
                        <span>5$</span>
                    </td>
                    <td class="text-center"><span>Ilimitado</span></td>
                    <td class="text-center"><span>Ilimitado</span></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th rowspan="23" class="tituloPrincipal thTitulo">
                        <span class="tituloRotar">Modo: Control Remoto / Home and office
                        </span>
                    </th>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">
                        Captura de actividad diaria
                    </th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">Control normal</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">Calidad de captura
                    </th>
                </tr>
                <tr>
                    <td class="text-center"><span>Estándar</span></td>
                    <td class="text-center"><span>Estándar</span></td>
                    <td class="text-center"><span>Estándar</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">Eliminación de
                        capturas (*e)</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mb-1">
                        </span>
                    </td>
                    <td class="text-center"><span>50$ x empresa</span></td>
                    <td class="text-center"><span>50$ x empresa</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">Permitir actividad
                        fuera de horario</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">Control intensivo
                        (cada 5min)</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mb-1">
                        </span>
                    </td>
                    <td class="text-center"><span>2 emp.</span></td>
                    <td class="text-center"><span>10 emp.</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">Control
                        superintensivo (cada1min)</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mb-1">
                        </span>
                    </td>
                    <td class="text-center"><span>1 emp.</span></td>
                    <td class="text-center"><span>1 emp.</span></td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">Control de tareas
                        diarias</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">Control de
                        justificaciones por horas</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">Control de
                        justificaciones por días</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                        </span>
                    </td>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/tick (4).svg')}}" height="22" class="mt-1">
                        </span>
                    </td>
                </tr>
                <tr>
                    <th scope="rowgroup" colspan="3" class="text-center" style="background:#f5f5f5">Capturas en video
                        basic (*v)Aleatorio</th>
                </tr>
                <tr>
                    <td class="text-center">
                        <span>
                            <img src="{{asset('landing/images/close (6).svg')}}" height="15" class="mb-1">
                        </span>
                    </td>
                    <td class="text-center"><span>2 emp.</span></td>
                    <td class="text-center"><span>4 emp.</span></td>
                </tr>
            </tbody>
        </table>
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