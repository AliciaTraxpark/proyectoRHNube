@extends('layouts.vertical')

@section('css')
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/alertify/alertify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/alertify/default.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/zoom.css') }}" rel="stylesheet" type="text/css" />
{{-- plugin de ALERTIFY --}}
<link href="{{ URL::asset('admin/assets/libs/alertify/alertify.css') }}" rel="stylesheet" type="text/css" />
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
        <h4 class="header-title mt-0 "></i>Asistencia consolidada</h4>
    </div>
</div>
@endsection

@section('content')
{{-- STYLES --}}
<style>
    body {
        background-color: #ffffff;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #ced0d3;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #153e90;
    }

    .select2-container--default .select2-selection--multiple {
        overflow-y: scroll;
        max-height: calc(2.5em + 1rem + 2px);
        height: calc(2.5em + 1rem + 2px);
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        background-color: transparent;
        border: none;
        border-right: 1px solid white;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        color: white;
        cursor: pointer;
        font-size: 1em;
        font-weight: 700;
        padding-top: 0px;
        padding-right: 4px;
        padding-bottom: 0px;
        padding-left: 4px;
    }

    .select2-container .select2-selection--single {
        height: 34px !important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 31px;
    }


    div.dataTables_processing {
        position: fixed !important;
        margin-bottom: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-top: -26px;
        margin-left: 0px !important;
        text-align: center;
        padding: 1em 0;
        z-index: 1;
    }

    .dataTables_wrapper .dataTables_processing {
        box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);
    }

    .liContenido {
        list-style: none;
    }

    .dropdown-itemSelector {
        padding: 0.1rem 1rem !important;
        margin: 0.1rem 0 !important;
    }

    .rowPersonalizado {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
{{-- CONTENIDO --}}
<div class="row justify-content-center pt-5" style="padding-top: 20px!important;">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"
                style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1">
                <div class="row">
                    <h4 class="header-title col-12 mt-0" style="margin-bottom: 0px;">{{$organizacion}}</h4>
                </div>
            </div>
            <div class="card-body border p-2">
                <div class="row rowPersonalizado">
                    <div class="col-md-3 pr-3 pl-3">
                        <div class="form-group">
                            <label class="col-form-label pt-0 pb-0">Rango de fechas:</label>
                            <div class="input-group text-center" style="padding-left: 0px;padding-right: 0px;"
                                id="fechaTrazabilidad">
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
                    <div class="col-md-4 pr-3 pl-3">
                        <div class="form-group">
                            <label class="col-form-label pt-0 pb-0">Seleccionar por:</label>
                            <select id="selectPor" data-plugin="customselect" class="form-control form-control-lg">
                                <option value="0" selected>Búsqueda general</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 pr-3 pl-3 mt-3">
                        <label class="col-form-label pt-0 pb-0">Empleado:</label>
                        <select id="empleadoPor" data-plugin="customselect"
                            class="form-control form-control-sm select2Multiple" multiple="multiple" required>
                        </select>
                        <span id="cantidadE" style="font-size: 11px"></span>
                    </div>
                    <div class="col-md-1 text-right btnR" style="padding-left: 0%">
                        <button type="button" class="btn btn-sm mt-1" style="background-color: #163552;"
                            onclick="javascript:cargarDatos()">
                            <img src="{{asset('landing/images/loupe (1).svg')}}" height="15">
                        </button>
                    </div>
                </div>
                <div class="row">
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
                            <div class="dropdown-menu allow-focus"
                                style="padding: 0rem 0;min-width: 16em!important;height: auto;max-height: 250px;overflow: auto;position: absolute;">
                                <h6 class="dropdown-header text-left"
                                    style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                    <img src="{{asset('landing/images/configuracionesD.svg')}}" class="mr-1"
                                        height="12" />
                                    Opciones
                                </h6>
                                <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido incidenciaPadre">
                                        <input type="checkbox">
                                        <label for="">Incidencias</label>
                                        <img class="float-right mt-1 ml-2"
                                            src="{{asset('landing/images/chevron-arrow-down.svg')}}" height="9"
                                            style="cursor: pointer;" onclick="javascript:toggleI()">
                                    </li>
                                    <ul class="ulHijo" style="display: none" id="menuIncidencias"></ul>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido">
                                        <input type="checkbox" id="tiempoMuertoE">
                                        <label for="">Tiempo muerto - entrada</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido">
                                        <input type="checkbox" id="tiempoMuertoS">
                                        <label for="">Tiempo muerto - salida</label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group row rowPersonalizado">
                            <div class="col-lg-5">
                                <img src="{{asset('landing/images/fuenteR.svg')}}" height="18">
                                <label for="formatoC" class="col-form-label pt-0 pb-0"
                                    style="font-weight: bold;font-size:12px">
                                    Formato de celda
                                </label>
                            </div>
                            <div class="col-lg-7">
                                <select id="formatoC" class="form-control">
                                    <option value="formatoAYN">Apellidos y nombres</option>
                                    <option value="formatoNYA" selected>Nombres y apellidos</option>
                                    <option value="formatoNA">Nombres - Apellidos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- GIF DE ESPERA --}}
                    <div id="espera" class="row justify-content-center" style="display: none">
                        <div class="col-md-4">
                            <img src="{{ asset('landing/images/loading.gif') }}" height="100">
                        </div>
                    </div>
                    {{-- INPUTS DE FECHAS --}}
                    <input type="hidden" name="fechaInicio" id="fechaInicio">
                    <input type="hidden" name="fechaFin" id="fechaFin">
                    {{-- TABLA DE TRAZABILIDAD --}}
                    <div class="col-md-12">
                        <table id="tablaTrazabilidad" class="table nowrap" style="font-size: 12.8px;">
                            <thead id="theadT" style=" background: #edf0f1;color: #6c757d;">
                                <tr>
                                    <th>#</th>
                                    <th>DNI</th>
                                    <th>Empleado</th>
                                    <th>Departamento</th>
                                    <th class="text-center">Tardanzas</th>
                                    <th class="text-center">Días Trabajados</th>
                                    <th class="text-center">Hora normal</th>
                                    <th class="text-center">Hora nocturno</th>
                                    <th class="text-center">Descanso Médico</th>
                                    <th class="text-center">Faltas</th>
                                    <th class="text-center">FI</th>
                                    <th class="text-center">FJ</th>
                                    <th class="text-center">PER</th>
                                    <th class="text-center">SME</th>
                                    <th class="text-center">Suspensión</th>
                                    <th class="text-center">Vacaciones</th>
                                    <th class="text-center">H.E. 25% Diurnas</th>
                                    <th class="text-center">H.E. 35% Diurnas</th>
                                    <th class="text-center">H.E. 100% Diurnas</th>
                                    <th class="text-center">H.E. 25% Nocturnas</th>
                                    <th class="text-center">H.E. 35% Nocturnas </th>
                                    <th class="text-center">H.E. 100% Nocturnas</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyT"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/alertify/alertify.js') }}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/buttons.html5.min.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/pdfmake.min.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/vfs_fonts.js')}}"></script>
<script src="{{ asset('landing/js/trazabilidadMarcaciones.js')}}"></script>
<script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection