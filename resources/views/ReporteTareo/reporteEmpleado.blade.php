@extends('layouts.vertical')

@section('css')
    <!-- Plugin css  CALENDAR-->
    <link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}"
        rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="{{ asset('admin/assets/js/html2pdf.bundle.min.js') }}"></script>
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
            {{-- <h4 class="mb-1 mt-0">Horarios</h4> --}}
            <h4 class="header-title mt-0 "></i>Reporte de tareo por empleado</h4>
        </div>
    </div>
@endsection


@section('content')
    <style>
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
        <style>body {
            background-color: #ffffff;
        }

        .flatpickr-calendar.static.open {
            width: 124px !important;
        }
        .scrollable-menu {
        height: auto;
        max-height: 142px;
        overflow: auto;
        position: absolute;
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

        .dropdown-itemM {
            padding: 0.1rem 0.1rem !important;
            color: #6c757d !important;
        }

        /* SYYLE DE GROUP */
        .select2-container--default .select2-results__group {
            color: #62778c;
        }


        .ulHijo {
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

        .dt-button-collection {
            min-width: 12rem !important;
        }

        .dt-button {
            padding: 0.15rem 0.15rem !important;
        }
        .col-xl-4 .select2-container .select2-selection {
        max-height: 40px;
        font-size: 12.2px;
        overflow-y: scroll;
    }

    </style>
    <style>
        .table {
            width: 100% !important;
        }

        /* .dataTables_scrollHeadInner {
                                                                width: 100% !important;
                                                            } */

        .table th,
        .table td {
            padding: 0.4rem;
            border-top: 1px solid #edf0f1;
        }

        .dt-button-collection a.buttons-columnVisibility:before,
        .dt-button-collection a.buttons-columnVisibility.active span:before {
            display: block;
            position: absolute;
            top: 1.2em;
            left: 0;
            width: 12px;
            height: 12px;
            box-sizing: border-box;
        }

        .dt-button-collection a.buttons-columnVisibility:before {
            content: ' ';
            margin-top: -6px;
            margin-left: 10px;
            border: 1px solid black;
            border-radius: 3px;
        }

        .dt-button-collection a.buttons-columnVisibility.active span:before {
            content: '\2714';
            margin-top: -11px;
            margin-left: 12px;
            text-align: center;
            text-shadow: 1px 1px #DDD, -1px -1px #DDD, 1px -1px #DDD, -1px 1px #DDD;
        }

        .dt-button-collection a.buttons-columnVisibility span {
            margin-left: 20px;
        }

        .dataTables_length {
            margin-top: 10px;
        }

    </style>
    <div class="row justify-content-center pt-5" style="padding-top: 20px!important;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"
                    style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1">
                    <div class="row">
                        <h4 class="header-title col-12 mt-0" style="margin-bottom: 0px;">{{ $organizacion }}</h4>
                    </div>
                </div>
                <input type="hidden" id="pasandoV">
                <div class="card-body border">
                    <div class="row justify-content-center">
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-xl-5">

                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label">Rango de fechas:</label>
                                    <input type="hidden" id="ID_START">
                                    <input type="hidden" id="ID_END">
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

                        <div class="col-xl-6 col-sm-6">
                            <div class="form-group   row">
                                <label class="col-lg-3 col-form-label">Empleado</label>
                                <div class="col-lg-9">
                                    <select id="idempleado" style="height: 50px!important" data-plugin="customselect"
                                        class="form-control form-control-sm" data-placeholder="Seleccione empleado">
                                        <option value="0" selected disabled>Seleccionar Empleado</option>
                                        @foreach ($empleado as $empleados)
                                        <option value="{{$empleados->emple_id}}">{{$empleados->perso_nombre}}
                                            {{$empleados->perso_apPaterno}} {{$empleados->perso_apMaterno}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>


                        </div>
                        <div class="col-xl-1 text-center btnR" style="padding-left: 0%">
                            <label for=""></label>
                            <button type="button" id="btnRecargaTabla" class="btn btn-sm mt-1"
                                style="background-color: #163552;" onclick="javascript:cambiarF()"> <img
                                    src="{{ asset('landing/images/loupe (1).svg') }}" height="15"></button>
                        </div>


                    </div>

                    <div class="row">

                        <div class="col-md-7" id="MostarDetalles" style="display: none">

                            <div class="dropdown" id="dropSelector">
                                <br>
                                <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                    style="cursor: pointer">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchO" checked
                                            style="cursor: pointer">
                                        <label class="custom-control-label" for="switchO" style="font-weight: bold">
                                            <img src="{{ asset('landing/images/insert.svg') }}" height="18">
                                            Selector de columnas
                                        </label>
                                    </div>
                                </a>
                                <div class="dropdown-menu allow-focus" style="padding: 0rem 0;min-width: 16em!important;">
                                    <h6 class="dropdown-header text-left"
                                        style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                        <img src="{{ asset('landing/images/configuracionesD.svg') }}" class="mr-1"
                                            height="12" />
                                        Opciones
                                    </h6>
                                    <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="fechaSwitch" checked>
                                            <label class="form-check-label" for="fechaSwitch">Fecha
                                            </label>
                                        </li>
                                    </ul>

                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checCodigo" checked>
                                            <label class="form-check-label" for="checCodigo">C??digo
                                            </label>
                                        </li>
                                    </ul>
                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checnumdoc" checked>
                                            <label class="form-check-label" for="checnumdoc">N??mero de documento
                                            </label>
                                        </li>
                                    </ul>

                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checSexo">
                                            <label class="form-check-label" for="checSexo">Sexo
                                            </label>
                                        </li>
                                    </ul>

                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checArea">
                                            <label class="form-check-label" for="checArea">??rea
                                        </li>
                                    </ul>

                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checCargo">
                                            <label class="form-check-label" for="checCargo">Cargo
                                        </li>
                                    </ul>
                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checCentroc">
                                            <label class="form-check-label" for="checCentroc">Centro de costo
                                        </li>
                                    </ul>
                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checPuntoc">
                                            <label class="form-check-label" for="checPuntoc">Punto de control
                                        </li>
                                    </ul>
                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checPuntocDescrip">
                                            <label class="form-check-label" for="checPuntocDescrip">Descripcion(es) de punto de
                                                control
                                        </li>
                                    </ul>

                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checControlEn">
                                            <label class="form-check-label" for="checControlEn">Controlador de entrada
                                        </li>
                                    </ul>

                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checControlSa">
                                            <label class="form-check-label" for="checControlSa">Controlador de salida
                                        </li>
                                    </ul>

                                </div>
                            </div>
                        </div>


                        {{-- GIF DE ESPERA --}}
                        <div id="espera" class="text-center" style="display: none">
                            <img src="{{ asset('landing/images/loading.gif') }}" height="100">
                        </div>
                        {{-- <div class="col-md-12">
                            <div class="dt-buttons btn-group flex-wrap" id="btnsDescarga">
                                <button class="btn btn-secondary   btn-sm mt-1" type="button" onclick="toExcel()">
                                    <span><i><img src="admin/images/excel.svg" height="20"></i> Descargar</span>
                                </button>
                                <button class="btn btn-secondary  btn-sm mt-1" type="button" onclick="generatePDF()">
                                    <span><i><img src="admin/images/pdf.svg" height="20"></i> Descargar</span>
                                </button>
                            </div>
                        </div> --}}
                        {{-- MODAL DE INSERTAR SALIDA --}}
                        <div id="insertarSalida" class="modal fade" role="dialog" aria-labelledby="insertarSalida"
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
                                            <form action="javascript:insertarSalida()" id="formInsertarSalida">
                                                <div class="row">
                                                    {{-- ID DE MARCACION --}}
                                                    <input type="hidden" id="idMarcacionIS">
                                                    {{-- ID DE HORARIO --}}
                                                    <input type="hidden" id="idHorarioIS">
                                                    <div class="col-md-12">
                                                        <span style="color:#62778c;font-weight: bold">Agregar salida</span>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <span id="i_validS" style="color: #8b3a1e;display:none">
                                                            Seleccionar marcaci??n
                                                        </span>
                                                    </div>
                                                    <div class="col-xl-5 mt-2">
                                                        <label>
                                                            Entrada
                                                            &nbsp;
                                                            <img src="{{ asset('landing/images/entradaD.svg') }}"
                                                                height="12" />
                                                            &nbsp;
                                                            <span id="i_hora"
                                                                style="color:#383e56;font-weight: bold"></span>
                                                        </label>
                                                    </div>
                                                    <div class="col-xl-7">
                                                        <div class="form-group row">
                                                            <label class="col-lg-5 col-form-label text-right">Salida &nbsp;
                                                                <img src="{{ asset('landing/images/salidaD.svg') }}"
                                                                    height="12" />
                                                            </label>
                                                            <div class="col-lg-7 mt-1">
                                                                <input type="text"
                                                                    class="form-control form-control-sm horasEntrada"
                                                                    onchange="$(this).removeClass('borderColor');"
                                                                    id="horaSalidaNueva">
                                                            </div>
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
                                            <button type="submit" class="btn btn-sm"
                                                style="background: #183b5d;border-color:#62778c;">
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
                        <div id="insertarEntrada" class="modal fade" role="dialog" aria-labelledby="insertarEntrada"
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
                                            <form action="javascript:insertarEntrada()" id="formInsertarEntrada">
                                                <div class="row">
                                                    {{-- ID DE MARCACION --}}
                                                    <input type="hidden" id="idMarcacionIE">
                                                    {{-- ID DE HORARIO --}}
                                                    <input type="hidden" id="idHorarioIE">
                                                    <div class="col-md-12">
                                                        <span style="color:#62778c;font-weight: bold">Insertar
                                                            entrada</span>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <span id="i_validE" style="color: #8b3a1e;display:none">
                                                            Ingresar entrada
                                                        </span>
                                                    </div>
                                                    <div class="col-xl-7">
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label text-left">Entrada &nbsp;
                                                                <img src="{{ asset('landing/images/entradaD.svg') }}"
                                                                    height="12" />
                                                            </label>
                                                            <div class="col-lg-8 mt-1 text-left">
                                                                <input type="text"
                                                                    class="form-control form-control-sm horasEntrada"
                                                                    onchange="$(this).removeClass('borderColor');"
                                                                    id="horasEntradaNueva">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-5 mt-2">
                                                        <label>
                                                            Salida
                                                            &nbsp;
                                                            <img src="{{ asset('landing/images/salidaD.svg') }}"
                                                                height="12" />
                                                            &nbsp;
                                                            <span id="ie_hora"
                                                                style="color:#62778c;font-weight: bold"></span>
                                                        </label>
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
                                            <button type="submit" class="btn btn-sm"
                                                style="background: #183b5d;border-color:#62778c;">
                                                Guardar
                                            </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- FINALIZACION --}}
                        <div id="datosEmpresa" class="col-md-12 " style="display: none">
                            <input type="text" id="nameOrganizacion" value="{{ $organizacion }}">
                            <input type="text" id="direccionO" value="{{ $direccion }}">
                            <input type="text" id="rucOrg" value="{{ $ruc }}">
                            <style>
                                .tableHi {
                                    border: 0.2px solid rgb(182, 182, 182) !important;
                                    border-collapse: collapse !important;
                                }

                            </style>

                        </div>
                        <div id="tableZoom" class="col-md-12">

                            <table id="tablaReport" class="table  nowrap" style="font-size: 12.8px;">
                                <thead id="theadD" style=" background: #edf0f1;color: #6c757d;">
                                    <tr>
                                        <th>#</th>
                                        <th>C??digo</th>
                                        <th>N??mero de documento </th>
                                        <th>Nombres y apellidos</th>
                                        {{-- <th name="tiempoSitHi">Sexo</th>
                                        <th name="tiempoSitHi">Cargo</th> --}}
                                       {{--  <th>C??d. Act.</th> --}}
                                        <th>Actividad</th>
                                       {{--  <th>C??d. Sub.</th> --}}
                                        <th>Subactividad</th>
                                        <th id="hEntrada">Hora de entrada</th>
                                        <th id="hSalida">Hora de salida</th>
                                        <th id="tSitio">Tiempo en sitio</th>
                                        <th>Punto de control</th>
                                       {{--  <th>Controlador</th> --}}


                                    </tr>
                                </thead>
                                <tbody id="tbodyD">
                                    <tr><td></td></tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL DE INSERTAR PUNTO CONTROL --}}
    <div id="insertarPuntoC" class="modal fade" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog d-flex modal-dialog-centered justify-content-center">
            <div class="modal-content">
                <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                    <h6 class="modal-title" style="color:#ffffff;">
                        Agregar punto de control
                    </h6>
                </div>
                <div class="modal-body" style="font-size:12px!important;">
                    <div class="col-md-12">
                        <form action="javascript:insertarPuntoC()" id="formInsertarPuntoC">
                            <div class="row">
                                {{-- ID DE MARCACION --}}
                                <input type="hidden" id="idMarcacionPC">

                                <div class="col-md-12">
                                    <span style="color:#62778c;font-weight: bold">Seleccione punto
                                        de control: </span>
                                </div>


                                <div class="col-md-12">
                                    <span style="color:#111111;font-weight: bold; font-size: 10px!important">Se visualizar??
                                        puntos de control con modo tareo </span>
                                    <select id="selectPuntoC" data-plugin="customselect" class="form-control" required>
                                        <option value="" disabled selected>Seleccione punto de control</option>
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

    {{-- MODAL DE INSERTAR ACTIVIDAD --}}
    <div id="insertarActivMo" class="modal fade" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog d-flex modal-dialog-centered justify-content-center">
            <div class="modal-content">
                <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                    <h6 class="modal-title" style="color:#ffffff;">
                        Agregar actividad
                    </h6>
                </div>
                <div class="modal-body" style="font-size:12px!important;">
                    <div class="col-md-12">
                        <form action="javascript:insertarActiv()" id="formInsertarActi">
                            <div class="row">
                                {{-- ID DE MARCACION --}}
                                <input type="hidden" id="idMarcacionACT">

                                <div class="col-md-12">
                                    <label style="color:#62778c;font-weight: bold">Seleccione Actividad: </label>
                                    <span style="font-style: oblique;font-size: 11px">*Se visualizar?? las actividades con
                                        modo tareo.</span>
                                    <br><br>
                                    <span style="font-style: oblique;font-size: 11px">*Las actividad sin subactiviades estan
                                        deshabilitadas.</span>
                                </div>

                                <div class="col-md-12">

                                    <select id="selectActiv" data-plugin="customselect" class="form-control" required>
                                        <option value="" disabled selected>Seleccione actividad</option>
                                    </select>
                                    <br><br>
                                </div>


                                <div class="col-md-12"><label for="">Seleccione Subactividad</label></div>
                                <div class="col-md-12">

                                    <select id="selectSubActiv" data-plugin="customselect" class="form-control" required
                                        disabled>
                                        <option value="" disabled selected>Seleccione subactividad</option>
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

    {{-- MODAL DE INSERTAR SUBACTIVIDAD --}}
    <div id="insertarSubMo" class="modal fade" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog d-flex modal-dialog-centered justify-content-center">
            <div class="modal-content">
                <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                    <h6 class="modal-title" style="color:#ffffff;">
                        Agregar subactividad
                    </h6>
                </div>
                <div class="modal-body" style="font-size:12px!important;">
                    <div class="col-md-12">
                        <form action="javascript:insertarSubac()" id="formInsertarSubac">
                            <div class="row">
                                {{-- ID DE MARCACION --}}
                                <input type="hidden" id="idMarcacionSACT">

                                <div class="col-md-12"><label for="">Seleccione Subactividad</label></div>
                                <div class="col-md-12">

                                    <select id="selectSubActiv2" data-plugin="customselect" class="form-control" required>
                                        <option value="" disabled selected>Seleccione subactividad</option>
                                    </select>
                                    <br><br>
                                </div>

                                <div class="col-md-12" style="display: none" id="divActi">
                                    <label for="">Actividad:</label>
                                    <input type="hidden" id="idActi">
                                    <span id="actividadSub" style="color:#62778c;font-weight: bold"></span>
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

    {{-- MODAL DE INSERTAR CONTROLADOR DE ENTRADA --}}
    <div id="insertarContEntradaModal" class="modal fade" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog d-flex modal-dialog-centered justify-content-center">
            <div class="modal-content">
                <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                    <h6 class="modal-title" style="color:#ffffff;">
                        Controlador de entrada
                    </h6>
                </div>
                <div class="modal-body" style="font-size:12px!important;">
                    <div class="col-md-12">
                        <form action="javascript:insertarContEntrada()">
                            <div class="row">
                                {{-- ID DE MARCACION --}}
                                <input type="hidden" id="idMarcacionContEntrada">

                                <div class="col-md-12">
                                    <span style="color:#62778c;font-weight: bold">Agregar controlador de entrada</span>
                                </div>

                                <div class="col-xl-12 mt-2">
                                    <label>
                                        Entrada
                                        &nbsp;
                                        <img src="{{ asset('landing/images/entradaD.svg') }}" height="12" />
                                        &nbsp;
                                        <span id="i_horaContEntrada" style="color:#383e56;font-weight: bold"></span>
                                    </label>
                                </div>
                                <div class="col-md-12"><label for="">Seleccione Controlador</label></div>
                                <div class="col-md-12">

                                    <select id="selectContEntrada" data-plugin="customselect" class="form-control" required>
                                        <option value="" disabled selected>Seleccione controlador</option>
                                    </select>
                                    <br><br>
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

    {{-- MODAL DE INSERTAR CONTROLADOR DE SALIDA --}}
    <div id="insertarContSalidaModal" class="modal fade" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog d-flex modal-dialog-centered justify-content-center">
            <div class="modal-content">
                <div class="modal-header" style="font-size:12px!important;background-color:#163552;">
                    <h6 class="modal-title" style="color:#ffffff;">
                        Controlador de salida
                    </h6>
                </div>
                <div class="modal-body" style="font-size:12px!important;">
                    <div class="col-md-12">
                        <form action="javascript:insertarContSalida()">
                            <div class="row">
                                {{-- ID DE MARCACION --}}
                                <input type="hidden" id="idMarcacionContSalida">

                                <div class="col-md-12">
                                    <span style="color:#62778c;font-weight: bold">Agregar controlador de salida</span>
                                </div>

                                <div class="col-xl-12 mt-2">
                                    <label>
                                        Entrada
                                        &nbsp;
                                        <img src="{{ asset('landing/images/entradaD.svg') }}" height="12" />
                                        &nbsp;
                                        <span id="i_horaContSalida" style="color:#383e56;font-weight: bold"></span>
                                    </label>
                                </div>
                                <div class="col-md-12"><label for="">Seleccione Controlador</label></div>
                                <div class="col-md-12">

                                    <select id="selectContSalida" data-plugin="customselect" class="form-control" required>
                                        <option value="" disabled selected>Seleccione controlador</option>
                                    </select>
                                    <br><br>
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

    {{-- ACTUALIZAR HORARIO --}}
    <div class="modal fade" id="actualizarH" tabindex="-1" role="dialog" aria-labelledby="actualizarH" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="{{ asset('landing/images/calendarioA.svg') }}" height="50" class="mt-1">
                    <h6 class="text-danger font-weight-bold mt-3">Actualizar horario</h6>
                    <span>
                        <img src="{{ asset('admin/images/warning.svg') }}" height="18">&nbsp;
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
                                    <span style="color:#62778c;font-weight: bold">Hora de marcaci??n</span>
                                    &nbsp;
                                    <img src="{{ asset('landing/images/salidaD.svg') }}" height="12" id="img_a" />
                                    &nbsp;
                                    <span id="a_hora"></span>
                                    <span id="a_valid" style="color: #8b3a1e;display:none">
                                        Seleccionar marcaci??n
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
                                    <img src="{{ asset('landing/images/salidaD.svg') }}" height="12" class="ml-1 mr-1" />
                                    <span id="c_horaE"></span>
                                </div>
                                <div class="col-md-12 pt-1">
                                    <span id="e_valid" style="color: #8b3a1e;display:none">
                                        Seleccionar marcaci??n
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
                                    <img src="{{ asset('landing/images/entradaD.svg') }}" height="12" class="ml-1 mr-1" />
                                    <span id="c_horaS"></span>
                                </div>
                                <div class="col-md-12 pt-1">
                                    <span id="s_valid" style="color: #8b3a1e;display:none">
                                        Seleccionar marcaci??n
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
    {{-- modificar --}}
    @if (isset($modifModoTareo))
        @if ($modifModoTareo == 1)
            <input type="hidden" id="modifReporte" value="0">
        @else
            <input type="hidden" id="modifReporte" value="0">
        @endif
    @else
        <input type="hidden" id="modifReporte" value="0">
    @endif
@endsection
@section('script')
    <script src="{{ asset('landing/js/actualizarPDatos.js') }}"></script>
    <!-- Plugins Js -->


    <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>


    <script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>

    <script src="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/combodate-1.0.7/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/combodate-1.0.7/es.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/buttons.html5.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.colVis.min.js"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/alertify/alertify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>




    <script src="{{ URL::asset('admin/assets/libs/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('landing/js/reporteTareoEmp.js') }}"></script>

@endsection

@section('script-bottom')
    <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection
