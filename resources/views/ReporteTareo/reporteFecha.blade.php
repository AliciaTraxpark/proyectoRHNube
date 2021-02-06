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
            <h4 class="header-title mt-0 "></i>Detalle de tareo por día</h4>
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

        .dataTables_length{
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
                        <div class="col-xl-4">
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label">Fecha:</label>
                                <div class="input-group col-md-8 text-center" style="padding-left: 0px;padding-right: 0px;"
                                    id="fechaSelec">
                                    <input type="text" id="fechaInput" {{-- onchange="cambiarF()" --}} class="form-control"
                                        data-input>
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
                                            <option value="{{ $empleados->emple_id }}">{{ $empleados->perso_nombre }}
                                                {{ $empleados->perso_apPaterno }} {{ $empleados->perso_apMaterno }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="col-xl-1 text-left btnR" style="padding-left: 0%">
                            <button type="button" id="btnRecargaTabla" class="btn btn-sm mt-1"
                                style="background-color: #163552;" onclick="javascript:cambiarF()"> <img
                                    src="{{ asset('landing/images/loupe (1).svg') }}" height="15"></button>
                        </div>

                        {{-- <div class="col-xl-6">
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label">Área:</label>
                                <div class="col-lg-10 colR">
                                    <select id="area" data-plugin="customselect" class="form-control" multiple="multiple">
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->area_id }}">
                                                {{ $area->area_descripcion }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div> --}}
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-12" id="MostarDetalles" style="display: none">
                            {{-- <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="customSwitDetalles"
                                    onclick="javascript:cambiartabla()">
                                <label class="custom-control-label" for="customSwitDetalles"
                                    style="font-weight: bold">Mostrar
                                    detalles</label>
                            </div> --}}
                            <div class="dropdown" id="dropSelector">
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
                                            <input type="checkbox" id="fechaSwitch">
                                            <label class="form-check-label" for="fechaSwitch">Fecha
                                            </label>
                                        </li>
                                    </ul>

                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checCodigo" checked>
                                            <label class="form-check-label" for="checCodigo">Código
                                            </label>
                                        </li>
                                    </ul>
                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checnumdoc" checked>
                                            <label class="form-check-label" for="checnumdoc">Número de documento
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
                                            <input type="checkbox" id="checCargo">
                                            <label class="form-check-label" for="checCargo">Cargo
                                        </li>
                                    </ul>

                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checPuntoc" >
                                            <label class="form-check-label" for="checPuntoc">Punto de control
                                        </li>
                                    </ul>

                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checControlEn" >
                                            <label class="form-check-label" for="checControlEn">Controlador de entrada
                                        </li>
                                    </ul>

                                    <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                        <li class="liContenido">
                                            <input type="checkbox" id="checControlSa" >
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
                                                            Seleccionar marcación
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
                                        <th>Código</th>
                                        <th>Número de documento </th>
                                        <th>Nombres y apellidos</th>
                                        <th name="tiempoSitHi">Sexo</th>
                                        <th name="tiempoSitHi">Cargo</th>
                                        <th>Cód. Act.</th>
                                        <th>Actividad</th>
                                        <th>Cód. Sub.</th>
                                        <th>Subactividad</th>
                                        <th id="hEntrada">Hora de entrada</th>
                                        <th id="hSalida">Hora de salida</th>
                                        <th id="tSitio">Tiempo en sitio</th>
                                        <th>Punto de control</th>
                                        <th>Controlador</th>


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
    {{-- MODAL DE INSERTAR PUNTO CONTROL --}}
    <div id="insertarPuntoC" class="modal fade" role="dialog"
    aria-hidden="true" data-backdrop="static">
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
                                    <span style="color:#111111;font-weight: bold; font-size: 10px!important">Se visualizará puntos de control con modo tareo </span>
                                    <select id="selectPuntoC" data-plugin="customselect" class="form-control"
                                            required >
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

    {{-- MODAL DE INSERTAR ACTIVIDAD--}}
    <div id="insertarActivMo" class="modal fade" role="dialog"
    aria-hidden="true" data-backdrop="static">
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
                                    <span style="font-style: oblique;font-size: 11px">*Se visualizará las actividades con modo tareo.</span>
                                        <br><br>
                                    <span style="font-style: oblique;font-size: 11px">*Las actividad sin subactiviades estan
                                    deshabilitadas.</span>
                                </div>

                                <div class="col-md-12">

                                    <select id="selectActiv" data-plugin="customselect" class="form-control"
                                            required >
                                            <option value="" disabled selected>Seleccione actividad</option>
                                        </select>
                                        <br><br>
                                </div>


                                    <div class="col-md-12"><label for="">Seleccione Subactividad</label></div>
                                    <div class="col-md-12">

                                        <select id="selectSubActiv" data-plugin="customselect" class="form-control"
                                                required disabled >
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

       {{-- MODAL DE INSERTAR SUBACTIVIDAD--}}
       <div id="insertarSubMo" class="modal fade" role="dialog"
       aria-hidden="true" data-backdrop="static">
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

                                           <select id="selectSubActiv2" data-plugin="customselect" class="form-control"
                                                   required  >
                                                   <option value="" disabled selected>Seleccione subactividad</option>
                                               </select>
                                               <br><br>
                                       </div>

                                       <div class="col-md-12" style="display: none" id="divActi">
                                           <label for="">Actividad:</label>
                                           <input type="hidden" id="idActi">
                                           <span id="actividadSub"
                                             style="color:#62778c;font-weight: bold"></span>
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
    {{-- modificar --}}
    @if (isset($modifReporte))
        @if ($modifReporte == 1)
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
    <script src="{{ asset('landing/js/reporteTareo.js') }}"></script>

@endsection

@section('script-bottom')
    <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection
