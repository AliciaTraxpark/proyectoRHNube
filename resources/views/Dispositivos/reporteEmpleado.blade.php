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
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="{{asset('admin/assets/js/html2pdf.bundle.min.js')}}"></script>
<script src="{{asset('admin/assets/js/Blob.js')}}"></script>
<script src="{{asset('admin/assets/js/FileSaver.js')}}"></script>
<script src="{{asset('admin/assets/js/Shim.min.js')}}"></script>
<script src="{{asset('admin/assets/js/xlsx.full.min.js')}}"></script>
<link href="{{ URL::asset('admin/assets/css/zoom.css') }}" rel="stylesheet" type="text/css" />
{{-- plugin de ALERTIFY --}}
<link href="{{ URL::asset('admin/assets/libs/alertify/alertify.css') }}" rel="stylesheet" type="text/css" />
<!-- Semantic UI theme -->
<link href="{{ URL::asset('admin/assets/libs/alertify/default.css') }}" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.6/jspdf.plugin.autotable.min.js"></script>
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
        <h4 class="header-title mt-0 "></i>Reporte de asistencia por empleado</h4>
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

    /* .dataTables_scrollHeadInner {
            width: 100% !important;
        } */

    .table th,
    .table td {
        padding: 0.4rem;
        border-top: 1px solid #edf0f1;
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

    .allow-focus {
        padding: 0rem 0;
        min-width: 19em !important;
        height: auto;
        max-height: 250px;
        overflow: auto;
        position: absolute;
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
                <div class="row rowPersonalizado">
                    <div class="col-md-3 pr-3 pl-3">
                        <div class="form-group">
                            <label class="col-form-label pt-0 pb-0">Rango de fechas:</label>
                            <input type="hidden" id="ID_START">
                            <input type="hidden" id="ID_END">
                            <div class="input-group text-center" style="padding-left: 0px;padding-right: 0px;"
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
                    <div class="col-md-4 pr-3 pl-3">
                        <div class="form-group">
                            <label class="col-form-label pt-0 pb-0">Seleccionar por:</label>
                            <select id="selectPor" data-plugin="customselect" class="form-control form-control-lg">
                                <option value="0" selected>Búsqueda general</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 pr-3 pl-3">
                        <div class="form-group">
                            <label class="col-form-label pt-0 pb-0">Empleado:</label>
                            <select id="empleadoPor" data-plugin="customselect" class="form-control form-control-lg">
                                <option value="0" selected>Seleccionar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 text-right btnR" style="padding-left: 0%">
                        <button type="button" id="btnRecargaTabla" class="btn btn-sm mt-1"
                            style="background-color: #1e2139;" onclick="javascript:cambiarF()">
                            <img src="{{asset('landing/images/loupe (1).svg')}}" height="15">
                        </button>
                    </div>
                </div>
                <div class="row justify-content-left">
                    <div class="col-md-4 pb-1">
                        <div class="dropdown" id="dropSelector">
                            <a class="dropdown-toggle dropReporte" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" style="cursor: pointer">
                                <div class="custom-control custom-switch mb-2">
                                    <input type="checkbox" class="custom-control-input" id="switchO" checked
                                        style="cursor: pointer">
                                    <label class="custom-control-label" for="switchO"
                                        style="font-weight: bold;font-size:12px">
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
                                        <input type="checkbox" checked id="colMarcaciones">
                                        <label for="">Entradas y Salidas</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido tiemposPadre">
                                        <input type="checkbox">
                                        <label for="">Cálculos de tiempos</label>
                                        <img class="float-right mt-1 ml-2"
                                            src="{{asset('landing/images/chevron-arrow-down.svg')}}" height="9"
                                            style="cursor: pointer;" onclick="javascript:toggleTiempos()">
                                    </li>
                                    <ul class="ulHijo" style="display: none" id="contenidoTiempos">
                                        <li class="liContenido tiemposHijo">
                                            <input type="checkbox" id="colDiurnas25">
                                            <label for="">H.E. 25% Diurnas</label>
                                        </li>
                                        <li class="liContenido tiemposHijo">
                                            <input type="checkbox" id="colDiurnas35">
                                            <label for="">H.E. 35% Diurnas</label>
                                        </li>
                                        <li class="liContenido tiemposHijo">
                                            <input type="checkbox" id="colDiurnas100">
                                            <label for="">H.E. 100% Diurnas</label>
                                        </li>
                                        <li class="liContenido tiemposHijo">
                                            <input type="checkbox" id="colNocturnas25">
                                            <label for="">H.E. 25% Nocturnas</label>
                                        </li>
                                        <li class="liContenido tiemposHijo">
                                            <input type="checkbox" id="colNocturnas35">
                                            <label for="">H.E. 35% Nocturnas</label>
                                        </li>
                                        <li class="liContenido tiemposHijo">
                                            <input type="checkbox" id="colNocturnas100">
                                            <label for="">H.E. 100% Nocturnas</label>
                                        </li>
                                        <li class="liContenido tiemposHijo">
                                            <input type="checkbox" id="colHorarioNormal">
                                            <label for="">Horario normal</label>
                                        </li>
                                        <li class="liContenido tiemposHijo">
                                            <input type="checkbox" id="colHorarioNocturno">
                                            <label for="">Horario nocturno</label>
                                        </li>
                                        <li class="liContenido tiemposHijo">
                                            <input type="checkbox" id="colTiempoTotal" checked>
                                            <label for="">Tiempo total</label>
                                        </li>
                                        <li class="liContenido tiemposHijo">
                                            <input type="checkbox" id="colSobretiempo">
                                            <label for="">Sobretiempo</label>
                                        </li>
                                        <li class="liContenido tiemposHijo">
                                            <input type="checkbox" id="colSobretiempoNormal">
                                            <label for="">Sobretiempo normal</label>
                                        </li>
                                        <li class="liContenido tiemposHijo">
                                            <input type="checkbox" id="colSobretiempoNocturno">
                                            <label for="">Sobretiempo nocturno</label>
                                        </li>
                                    </ul>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido incidenciaPadre">
                                        <input type="checkbox" checked>
                                        <label for="">Incidencias</label>
                                        <img class="float-right mt-1 ml-2"
                                            src="{{asset('landing/images/chevron-arrow-down.svg')}}" height="9"
                                            style="cursor: pointer;" onclick="javascript:toggleI()">
                                    </li>
                                    <ul class="ulHijo" style="display: none" id="contenidoIncidencias">
                                        <li class="liContenido incidenciaHijo">
                                            <input type="checkbox" id="colTardanza" checked>
                                            <label for="">Tardanza</label>
                                        </li>
                                        <li class="liContenido incidenciaHijo">
                                            <input type="checkbox" id="faltaHorario" checked>
                                            <label for="">Falta</label>
                                        </li>
                                        <li class="liContenido incidenciaHijo">
                                            <input type="checkbox" id="incidencia" checked>
                                            <label for="">Incidencias</label>
                                        </li>
                                    </ul>
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
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido">
                                        <input type="checkbox" id="tiempoSitHi">
                                        <label for="">Tiempo entre marcaciones</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector" style="font-size: 12.5px">
                                    <li class="liContenido tiemposMPadre">
                                        <input type="checkbox" name="porTiemposMuertos" id="porTiemposMuertos">
                                        <label for="">Tiempos muertos</label>
                                        <img class="float-right mt-1 ml-2" height="9" style="cursor: pointer;"
                                            src="{{asset('landing/images/chevron-arrow-down.svg')}}"
                                            onclick="javascript:togglePorTiemposMuertos()">
                                    </li>
                                    <ul class="ulHijo" style="display: none" id="contenidoPorTM">
                                        <li class="liContenido tiemposMHijo">
                                            <input type="checkbox" id="totalTiempoMuertoE">
                                            <label for="">Por horario - entrada</label>
                                        </li>
                                        <li class="liContenido tiemposMHijo">
                                            <input type="checkbox" id="totalTiempoMuertoS">
                                            <label for="">Por horario - salida</label>
                                        </li>
                                        <li class="liContenido tiemposMHijo">
                                            <input type="checkbox" id="tiempoMuertoE">
                                            <label for="">Por marcación - entrada</label>
                                        </li>
                                        <li class="liContenido tiemposMHijo">
                                            <input type="checkbox" id="tiempoMuertoS">
                                            <label for="">Por marcación - salida</label>
                                        </li>
                                    </ul>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    {{-- GIF DE ESPERA --}}
                    <div id="espera" class="text-center" style="display: none">
                        <img src="{{ asset('landing/images/loading.gif') }}" height="100">
                    </div>
                    <div id="tableZoom" class="col-md-12">
                        <table id="tablaReport" class="table  nowrap" style="font-size: 12.8px;">
                            <thead id="theadD" style=" background: #edf0f1;color: #6c757d;">
                                <tr>
                                    <th>CC</th>
                                    <th>Fecha</th>
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
{{-- modificar --}}
@if (isset($modifReporte))
@if ($modifReporte==1)
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
<script src="{{asset('admin/assets/libs/combodate-1.0.7/moment.js')}}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/es.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/buttons.html5.min.js')}}"></script>

<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/pdfmake.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/vfs_fonts.js') }}"></script>
<script src="{{ asset('landing/js/reporteDispoEmpleado.js') }}"></script>

@endsection

@section('script-bottom')
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection