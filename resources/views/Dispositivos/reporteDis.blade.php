@extends('layouts.vertical')

@section('css')
<!-- Plugin css  CALENDAR-->

<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/alertify/alertify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/alertify/default.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="{{asset('admin/assets/js/html2pdf.bundle.min.js')}}"></script>
<link href="{{ URL::asset('admin/assets/css/zoom.css') }}" rel="stylesheet" type="text/css" />
{{-- plugin de ALERTIFY --}}
<link href="{{ URL::asset('admin/assets/libs/alertify/alertify.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ URL::asset('admin/assets/libs/alertify/bootstrap.css') }}" rel="stylesheet" type="text/css" /> --}}
<!-- Semantic UI theme -->
<link href="{{ URL::asset('admin/assets/libs/alertify/default.css') }}" rel="stylesheet" type="text/css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('breadcrumb')
<div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="circle1"></div>
            <div class="circle2"></div>
            <div class="circle3"></div>
        </div>
    </div>
</div>
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="header-title mt-0 "></i>Detalle de asistencia</h4>
    </div>
</div>
@endsection


@section('content')
<style>
    body {
        background-color: #ffffff;
    }

    .flatpickr-calendar.static.open {
        width: 124px !important;
    }

    .botonsms {
        background-color: #ffffff;
        border-color: #ffffff;
        color: #62778c;
        padding-top: 0px;
        padding-bottom: 0px;
        border-top-width: 0px;
        border-bottom-width: 0px;
        padding-right: 0px;
        padding-left: 0px;
    }

    .badge-soft-secondary {
        background-color: rgb(207 209 223 / 20%);
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #52565b;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fdfdfd;
    }

    .custom-select:disabled {
        color: #3f3a3a;
        background-color: #fcfcfc;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #ced0d3;
    }

    .badge {
        font-size: 11.5px !important;
        font-weight: 500 !important;
    }

    body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-light.bootbox-cancel {
        background: #e2e1e1;
        color: #000000;
        border-color: #e2e1e1;
        zoom: 85%;
    }

    body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-footer>button,
    body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-success.bootbox-accept {
        background-color: #163552;
        border-color: #163552;
        zoom: 85%;
    }

    .col-md-6 .select2-container .select2-selection {
        height: 50px;
        font-size: 12.2px;
        overflow-y: scroll;
    }

    .select2-container .select2-selection--single {
        height: 34px !important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 31px;
    }

    .table {
        width: 100% !important;
    }

    table.dataTable {
        margin-bottom: 0px !important;
        margin-top: 0px !important;
    }

    table.DTFC_Cloned tbody {
        background-color: white;
    }

    /* .dataTables_scrollHeadInner {
            width: 100% !important;
        } */
    .dataTables_scrollBody {
        overflow-y: hidden !important;
    }

    .table th,
    .table td {
        padding: 0.4rem;
        border-top: 1px solid #edf0f1;
        white-space: nowrap;
    }

    .borderColor {
        border-color: red;
    }

    /* MODIFICAR ESTILOS DE ALERTIFY */
    .alertify .ajs-header {
        font-weight: normal;
    }

    .ajs-body {
        padding: 0px !important;
    }

    .alertify .ajs-footer {
        background: #ffffff;
    }

    .alertify .ajs-footer .ajs-buttons .ajs-button {
        min-height: 28px;
        min-width: 75px;
    }

    .ajs-cancel {
        font-size: 12px !important;
    }

    .ajs-ok {
        font-size: 12px !important;
    }

    .alertify .ajs-dialog {
        max-width: 450px;
    }

    .ajs-footer {
        padding: 12px !important;
    }

    .alertify .ajs-footer .ajs-buttons .ajs-button.ajs-ok {
        text-transform: none;
    }

    .alertify .ajs-footer .ajs-buttons.ajs-primary .ajs-button {
        text-transform: none;
    }

    /* FINALIZACION */
    .scrollable-menu {
        height: auto;
        max-height: 142px;
        overflow: auto;
        position: absolute;
    }

    .dropdown-itemM {
        padding: 0.1rem 0.1rem !important;
        color: #6c757d !important;
    }

    /* SYYLE DE GROUP */
    .select2-container--default .select2-results__group {
        color: #62778c;
    }

    .form-control:disabled {
        background-color: #fcfcfc;
    }

    .ulHijo,
    .ulHijoPadre {
        list-style: none;
        padding-left: 1rem;
    }

    .liContenido {
        list-style: none;
    }

    .dropdown-itemSelector {
        padding: 0.1rem 1rem !important;
        margin: 0.1rem 0 !important;
    }

    .celdaTransparente {
        background: #ffffff !important;
        border-top: #ffffff !important;
    }

    div.dataTables_processing {
        z-index: 1;
    }

    .dataTables_wrapper .dataTables_processing {
        box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);
    }

    /* SEPARACION ENTRE COLUMNAS */
    .separacion {
        padding-right: 15em;
        padding-left: 1em
    }

    .flatpickr-calendar {
        width: auto !important;
    }

    .allow-focus {
        padding: 0rem 0;
        min-width: 19em !important;
        height: auto;
        max-height: 250px;
        overflow: auto;
        position: absolute;
    }

    @media (max-width: 767.98px) {
        .separacion {
            padding-right: 1em !important;
            padding-left: 1em !important;
        }
    }
</style>
<div class="row justify-content-center pt-5" style="padding-top: 20px!important;">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"
                style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1">
                <div class="row">
                    <h4 class="header-title col-12 mt-0" style="margin-bottom: 0px;">{{$organizacion}}</h4>
                </div>
            </div>
            <div class="card-body border">
                <div class="row justify-content-center">
                    <div class="col-xl-4">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Fecha:</label>
                            <div class="input-group col-md-8 text-center" style="padding-left: 0px;padding-right: 0px;"
                                id="fechaSelec">
                                <input type="text" id="fechaInput" class="form-control" data-input>
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
                    <div class="col-xl-7 col-sm-6">
                        <div class="form-group   row">
                            <label class="col-lg-3 col-form-label">Empleado</label>
                            <div class="col-lg-9">
                                <select id="idempleado" style="height: 50px!important" data-plugin="customselect"
                                    class="form-control form-control-sm" data-placeholder="Seleccione empleado">
                                    <option value="0" selected>Todos los empleados</option>
                                    @foreach ($empleado as $empleados)
                                    <option value="{{$empleados->emple_id}}">{{$empleados->perso_nombre}}
                                        {{$empleados->perso_apPaterno}} {{$empleados->perso_apMaterno}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="col-xl-1 text-left btnR" style="padding-left: 0%">
                        <button type="button" id="btnRecargaTabla" class="btn btn-sm mt-1"
                            style="background-color: #163552;" onclick="javascript:cambiarF()">
                            <img src="{{asset('landing/images/loupe (1).svg')}}" height="15">
                        </button>
                    </div>
                </div>
                <div class="row justify-content-left">
                    <div class="col-md-4 pb-2">
                        <div class="dropdown" id="dropSelector">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                style="cursor: pointer">
                                <div class="custom-control custom-switch mb-2">
                                    <input type="checkbox" class="custom-control-input" id="switchO" checked
                                        style="cursor: pointer">
                                    <label class="custom-control-label" for="switchO" style="font-weight: bold">
                                        <img src="{{asset('landing/images/insert.svg')}}" height="18">
                                        Selector de columnas
                                    </label>
                                </div>
                            </a>
                            <div class="dropdown-menu allow-focus">
                                <h6 class="dropdown-header text-left"
                                    style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                    <img src="{{asset('landing/images/configuracionesD.svg')}}" class="mr-1"
                                        height="12" />
                                    Opciones
                                </h6>
                                <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido">
                                        <input type="checkbox" checked id="colCargo">
                                        <label for="">Cargo</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido detallePadre">
                                        <input type="checkbox" name="detallePadre">
                                        <label for="">Cálculos de tiempos</label>
                                        <img class="float-right mt-1 ml-2" height="9" style="cursor: pointer;"
                                            src="{{asset('landing/images/chevron-arrow-down.svg')}}"
                                            onclick="javascript:toggleD()">
                                    </li>
                                    <ul class="ulHijo" style="display: none" id="contenidoDetalle">
                                        <li class="liContenido detalleHijo">
                                            <input type="checkbox" name="porHorario" id="porHorario">
                                            <label for="">Tiempos por Horario</label>
                                            <img class="float-right mt-1 ml-2" height="9" style="cursor: pointer;"
                                                src="{{asset('landing/images/chevron-arrow-down.svg')}}"
                                                onclick="javascript:togglePorHorario()">
                                        </li>
                                        <ul class="ulHijoPadre" style="display: none" id="contenidoPorH">
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colHE25D">
                                                <label for="">H.E. 25% Diurnas</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colHE35D">
                                                <label for="">H.E. 35% Diurnas</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colHE100D">
                                                <label for="">H.E. 100% Diurnas</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colHE25N">
                                                <label for="">H.E. 25% Nocturnas</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colHE35N">
                                                <label for="">H.E. 35% Nocturnas</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colHoraNormal">
                                                <label for="">Horario normal</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colHoraNocturna">
                                                <label for="">Horario nocturno</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colFaltaJornada">
                                                <label for="">Jornada incompleta</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colSobreTiempo">
                                                <label for="">Sobretiempo total</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colSobreTNormal">
                                                <label for="">Sobretiempo normal</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colSobreTNocturno">
                                                <label for="">Sobretiempo nocturno</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colTiempoEntreH">
                                                <label for="">Tiempo total</label>
                                            </li>
                                        </ul>
                                        <li class="liContenido detalleHijo">
                                            <input type="checkbox" id="colTiempoSitio">
                                            <label for="">Tiempo por marcaciones</label>
                                        </li>
                                        <li class="liContenido detalleHijo">
                                            <input type="checkbox" name="porTotal" id="porTotal">
                                            <label for="">Tiempos totales</label>
                                            <img class="float-right mt-1 ml-2" height="9" style="cursor: pointer;"
                                                src="{{asset('landing/images/chevron-arrow-down.svg')}}"
                                                onclick="javascript:togglePorTotales()">
                                        </li>
                                        <ul class="ulHijoPadre" style="display: none" id="contenidoPorT">
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colHoraNormalTotal">
                                                <label for="">Horario normal</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colHoraNocturnaTotal">
                                                <label for="">Horario nocturno</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colFaltaJornadaTotal">
                                                <label for="">Jornada incompleta</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colSobreTiempoTotal" checked>
                                                <label for="">Sobretiempo</label>
                                            </li>
                                            <li class="liContenido detalleHijoDeHijo">
                                                <input type="checkbox" id="colTiempoTotal" checked>
                                                <label for="">Tiempo total</label>
                                            </li>
                                        </ul>
                                    </ul>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido">
                                        <input type="checkbox" id="colCodigo">
                                        <label for="">Código de trabajador</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido">
                                        <input type="checkbox" checked id="colMarcaciones">
                                        <label for="">Entradas y Salidas</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido horarioPadre">
                                        <input type="checkbox">
                                        <label for="">Horarios</label>
                                        <img class="float-right mt-1 ml-2"
                                            src="{{asset('landing/images/chevron-arrow-down.svg')}}" height="9"
                                            style="cursor: pointer;" onclick="javascript:toggleH()">
                                    </li>
                                    <ul class="ulHijo" style="display: none" id="contenidoHorarios">
                                        <li class="liContenido horarioHijo">
                                            <input type="checkbox" id="descripcionHorario" checked>
                                            <label for="">Descripcion</label>
                                        </li>
                                        <li class="liContenido horarioHijo">
                                            <input type="checkbox" id="horarioHorario" checked>
                                            <label for="">Horario</label>
                                        </li>
                                        <li class="liContenido horarioHijo">
                                            <input type="checkbox" id="toleranciaIHorario">
                                            <label for="">Tolerancia en el ingreso</label>
                                        </li>
                                        <li class="liContenido horarioHijo">
                                            <input type="checkbox" id="toleranciaFHorario">
                                            <label for="">Tolerancia en la salida</label>
                                        </li>
                                    </ul>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido incidenciaPadre">
                                        <input type="checkbox">
                                        <label for="">Incidencias</label>
                                        <img class="float-right mt-1 ml-2"
                                            src="{{asset('landing/images/chevron-arrow-down.svg')}}" height="9"
                                            style="cursor: pointer;" onclick="javascript:toggleI()">
                                    </li>
                                    <ul class="ulHijo" style="display: none" id="contenidoIncidencias">
                                        <li class="liContenido incidenciaHijo">
                                            <input type="checkbox" id="colTardanza">
                                            <label for="">Tardanza entre horarios</label>
                                        </li>
                                        <li class="liContenido incidenciaHijo">
                                            <input type="checkbox" id="colTardanzaTotal" checked>
                                            <label for="">Tardanza total</label>
                                        </li>
                                        <li class="liContenido incidenciaHijo">
                                            <input type="checkbox" id="faltaHorario">
                                            <label for="">Falta entre horario</label>
                                        </li>
                                        <li class="liContenido incidenciaHijo">
                                            <input type="checkbox" id="faltaTotal" checked>
                                            <label for="">Falta total</label>
                                        </li>
                                        <li class="liContenido incidenciaHijo">
                                            <input type="checkbox" id="incidencia" checked>
                                            <label for="">Incidencias</label>
                                        </li>
                                    </ul>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido">
                                        <input type="checkbox" checked disabled>
                                        <label for="">Nombres y apellidos</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido">
                                        <input type="checkbox" checked disabled>
                                        <label for="">Número documento</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido pausaPadre">
                                        <input type="checkbox">
                                        <label for="">Pausas</label>
                                        <img class="float-right mt-1 ml-2"
                                            src="{{asset('landing/images/chevron-arrow-down.svg')}}" height="9"
                                            style="cursor: pointer;" onclick="javascript:toggleP()">
                                    </li>
                                    <ul class="ulHijo" style="display: none" id="contenidoPausas">
                                        <li class="liContenido pausaHijo">
                                            <input type="checkbox" id="descripcionPausa">
                                            <label for="">Pausa</label>
                                        </li>
                                        <li class="liContenido pausaHijo">
                                            <input type="checkbox" id="horarioPausa">
                                            <label for="">Horario de pausa</label>
                                        </li>
                                        <li class="liContenido pausaHijo">
                                            <input type="checkbox" id="tiempoPausa">
                                            <label for="">Tiempo de pausa</label>
                                        </li>
                                        <li class="liContenido pausaHijo">
                                            <input type="checkbox" id="excesoPausa">
                                            <label for="">Exceso de pausa</label>
                                        </li>
                                    </ul>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-left">
                    {{-- GIF DE ESPERA --}}
                    <div id="espera" class="row justify-content-center" style="display: none">
                        <div class="col-md-4">
                            <img src="{{ asset('landing/images/loading.gif') }}" height="100">
                        </div>
                    </div>
                    <div id="tableZoom" class="col-md-12">
                        <table id="tablaReport" class="table nowrap order-column" style="font-size: 12.8px;">
                            <thead id="theadD" style=" background: #edf0f1;color: #6c757d;">
                                <tr>
                                    <th>CC</th>
                                    <th>DNI</th>
                                    <th>Nombre</th>
                                    <th>Cargo</th>
                                    <th>Horario</th>
                                    <th id="hEntrada">Hora de entrada</th>
                                    <th id="hSalida">Hora de salida</th>
                                    <th id="tSitio">Tiempo en sitio</th>
                                    <th>Tardanza T.</th>
                                    <th>Faltas T.</th>
                                    <th>Incidencias T.</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyD"></tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- MODAL DE LISTA DE SALIDAS MARCACION --}}
<div id="listaSalidasMarcacion" class="modal fade" role="dialog" aria-labelledby="listaSalidasMarcacion"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog d-flex modal-dialog-centered justify-content-center">
        <div class="modal-content">
            <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                <h6 class="modal-title" style="color:#ffffff;">
                    Mantenimiento de Marcaciones
                </h6>
            </div>
            <div class="modal-body" style="font-size:12px!important;">
                <div class="col-md-12">
                    <form action="javascript:cambiarEntradaM()" id="formCambiarEntradaM">
                        <div class="row">
                            {{-- ID DE MARCACION --}}
                            <input type="hidden" id="idMarcacion">
                            {{-- EL TIPO SI ENTRADA O SALIDA --}}
                            <input type="hidden" id="c_tipoS">
                            <div class="col-md-12">
                                <span style="color:#62778c;font-weight: bold">Cambiar a entrada</span>
                                <img src="{{asset('landing/images/entradaD.svg') }}" height="12" class="ml-1 mr-1" />
                                <span id="c_horaS"></span>
                            </div>
                            <div class="col-md-12 pt-1">
                                <span id="s_valid" style="color: #8b3a1e;display:none">
                                    Seleccionar marcación
                                </span>
                                <select data-plugin="customselect" class="form-control custom-select custom-select-sm"
                                    id="salidaM" required>
                                </select>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;">
                <div class="col-md-12 text-right" style="padding-right: 0px;">
                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal"
                        onclick="javascript:limpiarAtributos()">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm" style="background: #183b5d;;border-color:#62778c;">
                        Guardar
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- FINALIZACION --}}
{{-- MODAL DE LISTA DE ENTRADAS MARCACION --}}
<div id="listaEntradasMarcacion" class="modal fade" role="dialog" aria-labelledby="listaEntradasMarcacion"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  d-flex modal-dialog-centered justify-content-center">
        <div class="modal-content">
            <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                <h6 class="modal-title" style="color:#ffffff;">
                    Mantenimiento de Marcaciones
                </h6>
            </div>
            <div class="modal-body" style="font-size:12px!important;">
                <div class="col-md-12">
                    <form action="javascript:cambiarSalidaM()" id="formCambiarSalidaM">
                        <div class="row">
                            {{-- ID DE MARCACION --}}
                            <input type="hidden" id="idMarcacionE">
                            {{-- EL TIPO SI FUE ENTRADA O SALIDA --}}
                            <input type="hidden" id="c_tipoE">
                            <div class="col-md-12">
                                <span style="color:#62778c;font-weight: bold">Cambiar a salida</span>
                                <img src="{{asset('landing/images/salidaD.svg') }}" height="12" class="ml-1 mr-1" />
                                <span id="c_horaE"></span>
                            </div>
                            <div class="col-md-12 pt-1">
                                <span id="e_valid" style="color: #8b3a1e;display:none">
                                    Seleccionar marcación
                                </span>
                                <select data-plugin="customselect" class="form-control custom-select custom-select-sm"
                                    id="entradaM" required>
                                </select>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;">
                <div class="col-md-12 text-right" style="padding-right: 0px;">
                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal"
                        onclick="javascript:limpiarAtributos()">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm" style="background: #183b5d;border-color:#62778c;">
                        Guardar
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- FINALIZACION --}}
{{-- MODAL DE LISTA DE ENTRADAS MARCACION --}}
<div id="asignacionMarcacion" class="modal fade" role="dialog" aria-labelledby="asignacionMarcacion" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered justify-content-center">
        <div class="modal-content">
            <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                <h6 class="modal-title" style="color:#ffffff;">
                    Mantenimiento de Marcaciones
                </h6>
            </div>
            <div class="modal-body" style="font-size:12px!important;">
                <div class="col-md-12">
                    <form action="javascript:guardarAsignacion()" id="formGuardarAsignacion">
                        <div class="row">
                            {{-- ID DE MARCACION --}}
                            <input type="hidden" id="idMarcacionA">
                            {{-- EL TIPO DE MARCACION SI FUE ENTRADA O SALIDA --}}
                            <input type="hidden" id="tipoM">
                            <div class="col-md-12">
                                <span style="color:#62778c;font-weight: bold">Hora de marcación</span>
                                &nbsp;
                                <img src="{{asset('landing/images/salidaD.svg') }}" height="12" id="img_a" />
                                &nbsp;
                                <span id="a_hora"></span>
                                <span id="a_valid" style="color: #8b3a1e;display:none">
                                    Seleccionar marcación
                                </span>
                            </div>
                            <div class="col-xl-8 mt-1">
                                <select data-plugin="customselect" class="form-control custom-select custom-select-sm"
                                    id="horarioM" required></select>
                            </div>
                            <div class="col-xl-4 mt-1">
                                <select data-plugin="customselect" class="form-control custom-select custom-select-sm"
                                    id="asignacionM" required>
                                    <option value="" disabled selected>Seleccionar</option>
                                    <option value="1">Entrada</option>
                                    <option value="2">Salida</option>
                                </select>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;">
                <div class="col-md-12 text-right" style="padding-right: 0px;">
                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal"
                        onclick="javascript:limpiarAtributos()">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm" style="background: #183b5d;;border-color:#62778c;">
                        Guardar
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- FINALIZACION --}}
{{-- MODAL DE INSERTAR SALIDA --}}
<div id="insertarSalida" class="modal fade" role="dialog" aria-labelledby="insertarSalida" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog d-flex modal-dialog-centered justify-content-center">
        <div class="modal-content">
            <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                <h6 class="modal-title" style="color:#ffffff;">
                    Mantenimiento de Marcaciones
                </h6>
            </div>
            <div class="modal-body" style="font-size:12px!important;">
                <div class="col-md-12">
                    <form action="javascript:insertarSalida()" id="formInsertarSalida">
                        <div class="row">
                            {{-- ID DE MARCACION --}}
                            <input type="hidden" id="idMarcacionIS">
                            {{-- ID DE HORARIO --}}
                            <input type="hidden" id="idHorarioIS">
                            <div class="col-md-12 pl-2">
                                <span style="color:#62778c;font-weight: bold">Agregar salida</span>
                            </div>
                            <div class="col-md-12">
                                <span id="i_validS" style="color: #8b3a1e;display:none">
                                    Seleccionar marcación
                                </span>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table cellpadding="8">
                                        <tr>
                                            <th style="color:#383e56;">
                                                Entrada
                                                <img src="{{asset('landing/images/entradaD.svg') }}" height="12"
                                                    class="ml-1" />
                                            </th>
                                            <td>
                                                <span id="i_hora" style="font-weight: bold"></span>
                                            </td>
                                            <th style="color:#383e56;">
                                                Salida
                                                <img src="{{asset('landing/images/salidaD.svg') }}" height="12"
                                                    class="ml-1" />
                                            </th>
                                            <td>
                                                <input type="text" class="form-control form-control-sm horasEntrada"
                                                    onchange="$(this).removeClass('borderColor');" id="horaSalidaNueva">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;">
                <div class="col-md-12 text-right" style="padding-right: 0px;">
                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal"
                        onclick="javascript:limpiarAtributos()">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm" style="background: #183b5d;border-color:#62778c;">
                        Guardar
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- FINALIZACION --}}
{{-- MODAL DE INSERTAR ENTRADA --}}
<div id="insertarEntrada" class="modal fade" role="dialog" aria-labelledby="insertarEntrada" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog d-flex modal-dialog-centered justify-content-center">
        <div class="modal-content">
            <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                <h6 class="modal-title" style="color:#ffffff;">
                    Mantenimiento de Marcaciones
                </h6>
            </div>
            <div class="modal-body" style="font-size:12px!important;">
                <div class="col-md-12">
                    <form action="javascript:insertarEntrada()" id="formInsertarEntrada">
                        <div class="row">
                            {{-- ID DE MARCACION --}}
                            <input type="hidden" id="idMarcacionIE">
                            {{-- ID DE HORARIO --}}
                            <input type="hidden" id="idHorarioIE">
                            <div class="col-md-12">
                                <span style="color:#62778c;font-weight: bold">Insertar entrada</span>
                            </div>
                            <div class="col-md-12">
                                <span id="i_validE" style="color: #8b3a1e;display:none">
                                    Ingresar entrada
                                </span>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table cellpadding="8">
                                        <tr>
                                            <th style="color:#383e56;">
                                                Entrada
                                                <img src="{{asset('landing/images/entradaD.svg') }}" height="12"
                                                    class="ml-1" />
                                            </th>
                                            <td>
                                                <input type="text" class="form-control form-control-sm horasEntrada"
                                                    onchange="$(this).removeClass('borderColor');"
                                                    id="horasEntradaNueva">
                                            </td>
                                            <th style="color:#383e56;">
                                                Salida
                                                <img src="{{asset('landing/images/salidaD.svg') }}" height="12"
                                                    class="ml-1" />
                                            </th>
                                            <td>
                                                <span id="ie_hora" style="font-weight: bold"></span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;">
                <div class="col-md-12 text-right" style="padding-right: 0px;">
                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal"
                        onclick="javascript:limpiarAtributos()">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm" style="background: #183b5d;border-color:#62778c;">
                        Guardar
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- FINALIZACION --}}
{{-- MODAL DE CAMBIAR DE HORARIO --}}
<div id="modalCambiarHorario" class="modal fade" role="dialog" aria-labelledby="modalCambiarHorario" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog modal-lg d-flex modal-dialog-centered justify-content-center modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                <h6 class="modal-title" style="color:#ffffff;">
                    Mantenimiento de Marcaciones
                </h6>
            </div>
            <div class="modal-body" style="font-size:12px!important;">
                <div class="col-md-12">
                    <form action="javascript:cambiarHorarioM()" id="formCambiarHorarioM">
                        {{-- EMPLEADO --}}
                        <input type="hidden" id="idEmpleadoCH">
                        <div class="row">
                            <div class="col-md-12">
                                <span style="color:#62778c;font-weight: bold">Horario</span>
                            </div>
                            <div class="col-md-12 pt-1">
                                <span id="ch_valid" style="color: #8b3a1e;display:none">
                                    Seleccionar horario
                                </span>
                                <select data-plugin="customselect" class="form-control custom-select custom-select-sm"
                                    id="horarioXE" required>
                                </select>
                            </div>
                        </div>
                        <div class="row pt-2" id="detalleHorarios" style="display: none"></div>
                        <div class="row pt-2" id="detalleMarcaciones" style="display: none"></div>
                </div>
            </div>
            <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;">
                <div class="col-md-12 text-right" style="padding-right: 0px;">
                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal"
                        onclick="javascript:limpiarAtributos()">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm" style="background: #183b5d;border-color:#62778c;">
                        Guardar
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- FINALIZACION --}}
{{-- ACTUALIZAR HORARIO --}}
<div class="modal fade" id="actualizarH" tabindex="-1" role="dialog" aria-labelledby="actualizarH" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img src="{{asset('landing/images/calendarioA.svg')}}" height="50" class="mt-1">
                <h6 class="text-danger font-weight-bold mt-3">Actualizar horario</h6>
                <span>
                    <img src="{{asset('admin/images/warning.svg')}}" height="18">&nbsp;
                    Horario asignado actualmente fue eliminado.<br>Recomendamos actualizar horario.
                </span>
                <div class="mt-4">
                    <a class="btn btn-rounded width-md" data-dismiss="modal"
                        style="background: #183b5d;color:#ffffff;cursor: pointer;">
                        Entendido
                    </a>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
{{-- FINALIZACION --}}
{{-- AGREGAR MARCACIONES --}}
<div id="modalAgregar" class="modal fade" role="dialog" aria-labelledby="modalAgregar" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog modal-lg d-flex modal-dialog-centered justify-content-center">
        <div class="modal-content" id="contentM">
            <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                <h6 class="modal-title" style="color:#ffffff;">
                    Mantenimiento de Marcaciones
                </h6>
            </div>
            <div class="modal-body" style="font-size:12px!important;">
                <div class="col-md-12">
                    {{-- FECHA --}}
                    <input type="hidden" id="a_fecha">
                    {{-- ID EMPLEADO --}}
                    <input type="hidden" id="a_idE">
                    <form action="javascript:registrarMar()" id="formRegistrarMar">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="mb-0" style="color:#62778c;font-weight: bold">Horario</label>
                                <span id="am_valid" style="color: #8b3a1e;display:none"></span>
                                <select data-plugin="customselect" class="form-control custom-select custom-select-sm"
                                    id="r_horarioXE" required>
                                </select>
                            </div>
                        </div>
                        <div class="row pt-2" id="AM_detalleHorarios" style="display: none"></div>
                        <div class="row justify-content-center" style="display: none" id="rowDatosM">
                            <div class="col-md-12 p-3">
                                <div class="table-responsive">
                                    <table cellpadding="6">
                                        <tr>
                                            <th><span style="color:#62778c;">Tipo</span></th>
                                            <th><span style="color:#62778c;">Fecha</span></th>
                                            <th><span style="color:#62778c;">Hora</span></th>
                                            <th><span style="color:#62778c;">Sin marcación</span></th>
                                        </tr>
                                        <tr>
                                            <td>
                                                <img src="{{asset('landing/images/entradaD.svg') }}" height="12"
                                                    class="mr-1" />
                                                Entrada
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="fechaNuevaEntrada" data-input>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="nuevaEntrada">
                                            </td>
                                            <td class="text-center">
                                                <div class="form-group mb-0 mt-2">
                                                    <input type="checkbox" id="v_entrada">
                                                    <label for="" class="mb-0"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <img src="{{asset('landing/images/salidaD.svg') }}" height="12"
                                                    class="mr-1 ml-1" />
                                                Salida
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="fechaNuevaSalida" data-input>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="nuevaSalida">
                                            </td>
                                            <td class="text-center">
                                                <div class="form-group mb-0 mt-2">
                                                    <input type="checkbox" id="v_salida">
                                                    <label for="" class="mb-0"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;">
                <div class="col-md-12 text-right" style="padding-right: 0px;">
                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal"
                        onclick="javascript:limpiarAtributos()">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm" style="background: #183b5d;border-color:#62778c;">
                        Guardar
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- FINALIZACION --}}
{{-- ACTUALIZAR HORARIO A UNA SOLA MARCACION --}}
<div id="modalActualizarHM" class="modal fade" role="dialog" aria-labelledby="modalActualizarHM" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog modal-lg d-flex modal-dialog-centered justify-content-center">
        <div class="modal-content">
            <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                <h6 class="modal-title" style="color:#ffffff;">
                    Mantenimiento de Marcaciones
                </h6>
            </div>
            <div class="modal-body" style="font-size:12px!important;">
                {{-- ID DE MARCACIÓN --}}
                <input type="hidden" id="idMarcacionHM">
                <div class="col-md-12">
                    <form action="javascript:actualizacionMarcacionH()" id="formActualizacionMarcacionH">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="table-responsive">
                                    <table id="detalleM" class="table nowrap order-column" style="font-size: 12.8px;">
                                        <thead style="background: #fafafa;">
                                            <tr>
                                                <th class="text-center">Entrada</th>
                                                <th class="text-center">Salida</th>
                                                <th class="text-center">Descripción del horario</th>
                                                <th class="text-center">Horario</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyDetalleHM"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row pt-1">
                            <div class="col-md-12">
                                <span style="color:#62778c;font-weight: bold">Horario</span>
                            </div>
                            <div class="col-md-12 pt-1">
                                <span id="hm_valid" style="color: #8b3a1e;display:none">
                                    Seleccionar horario
                                </span>
                                <select data-plugin="customselect" class="form-control custom-select custom-select-sm"
                                    id="horarioXM" required>
                                </select>
                            </div>
                        </div>
                        <div class="row pt-2" id="detalleHorariosEM" style="display: none"></div>
                </div>
            </div>
            <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;">
                <div class="col-md-12 text-right" style="padding-right: 0px;">
                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal"
                        onclick="javascript:limpiarAtributos()">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm" style="background: #183b5d;border-color:#62778c;">
                        Guardar
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- FINALIZACION --}}
{{-- MODIFICAR --}}
@if (isset($modifReporte))
@if ($modifReporte==1)
<input type="hidden" id="modifReporte" value="1">
@else
<input type="hidden" id="modifReporte" value="0">
@endif
@else
<input type="hidden" id="modifReporte" value="1">
@endif
@endsection
@section('script')
<script src="{{ asset('landing/js/actualizarPDatos.js') }}"></script>
<!-- Plugins Js -->
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/moment.js')}}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/es.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/buttons.html5.min.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/dataTables.fixedColumns.min.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/alertify/alertify.js') }}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/pdfmake.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/vfs_fonts.js') }}"></script>
<script src="{{ asset('landing/js/reporteDispo.js') }}"></script>
@endsection
@section('script-bottom')
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection