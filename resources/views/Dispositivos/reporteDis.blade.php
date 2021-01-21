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
        {{-- <h4 class="mb-1 mt-0">Horarios</h4> --}}
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

    /* .dataTables_scrollHeadInner {
            width: 100% !important;
        } */

    .table th,
    .table td {
        padding: 0.4rem;
        border-top: 1px solid #edf0f1;
    }

    .borderColor {
        border-color: red;
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
                    <div class="col-md-12 pb-2">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitDetalles"
                                onclick="javascript:cambiartabla()">
                            <label class="custom-control-label" for="customSwitDetalles" style="font-weight: bold">
                                Mostrar detalles
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12 pb-2">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="switPausas"
                                onclick="javascript:togglePausas()">
                            <label class="custom-control-label" for="switPausas" style="font-weight: bold">
                                Mostrar pausas
                            </label>
                        </div>
                    </div>
                    {{-- GIF DE ESPERA --}}
                    <div id="espera" class="row justify-content-center" style="display: none">
                        <div class="col-md-4">
                            <img src="{{ asset('landing/images/loading.gif') }}" height="100">
                        </div>
                    </div>
                    <div id="tableZoom" class="col-md-12">
                        <table id="tablaReport" class="table  nowrap" style="font-size: 12.8px;">
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
    <div class="modal-dialog  modal-lg d-flex modal-dialog-centered justify-content-center " style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-header" style="font-size:12px!important;background: #f3f3f3;"></div>
            <div class="modal-body" style="font-size:12px!important;background: #f3f3f3;">
                <div class="col-md-12">
                    <form action="javascript:cambiarEntradaM()">
                        <div class="row">
                            <input type="hidden" id="idMarcacion">
                            <div class="col-md-12">
                                <h6 style="color:#62778c;font-weight: bold">
                                    Cambiar a entrada
                                    <img src="{{asset('landing/images/entradaD.svg') }}" height="12" />
                                    &nbsp;
                                    <span id="c_horaS" style="color:#62778c;font-weight: bold"></span>
                                </h6>
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
            <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;background: #f3f3f3;">
                <div class="col-md-12 text-right" style="padding-right: 0px;">
                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm" style="background: #183b5d;;border-color:#62778c;">
                        Registrar
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
    <div class="modal-dialog  modal-lg d-flex modal-dialog-centered justify-content-center " style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-header" style="font-size:12px!important;background: #f3f3f3;"></div>
            <div class="modal-body" style="font-size:12px!important;background: #f3f3f3;">
                <div class="col-md-12">
                    <form action="javascript:cambiarSalidaM()">
                        <div class="row">
                            <input type="hidden" id="idMarcacionE">
                            <div class="col-md-12">
                                <h6 style="color:#62778c;font-weight: bold">
                                    Cambiar a salida
                                    <img src="{{asset('landing/images/salidaD.svg') }}" height="12" />
                                    &nbsp;
                                    <span id="c_horaE" style="color:#62778c;font-weight: bold"></span>
                                </h6>
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
            <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;background: #f3f3f3;">
                <div class="col-md-12 text-right" style="padding-right: 0px;">
                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm" style="background: #183b5d;;border-color:#62778c;">
                        Registrar
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