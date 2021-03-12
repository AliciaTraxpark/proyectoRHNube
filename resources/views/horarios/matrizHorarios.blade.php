@extends('layouts.vertical')

@section('css')
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
    {{-- <link href="{{ URL::asset('admin/assets/libs/alertify/bootstrap.css') }}" rel="stylesheet" type="text/css" /> --}}
    <!-- Semantic UI theme -->
    <link href="{{ URL::asset('admin/assets/libs/alertify/default.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.6/jspdf.plugin.autotable.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
@endsection

@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0" style="font-weight: bold">
            Matriz de Horarios
        </h4>
    </div>
</div>
@endsection

@section('content')
<style>
    .flex-wrap {
        text-align: left !important;
        display: block !important;
        margin-bottom: 10px;
    }

    .drp-selected {
        display: none !important;
    }

    .datepicker,
    .table-condensed {
        width: 280px !important;
        height: 250px !important;
        min-width: 50% !important;
        min-height: 50% !important;
        font-size: small !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #ced0d3;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #52565b;
    }

    .select2-container--default .select2-selection--multiple {
        overflow-y: scroll;
    }

    .datetimepicker table tr td span.active,
    .datetimepicker table tr td span.active:hover {
        background-image: linear-gradient(to bottom, #1f4068, #1f4068);
    }

    .page-link{
        font-size: 13px;
    }

    div#tablaSinActividadD {
        padding: 0;
    }

    /* RESPONSIVE */
    @media (max-width: 767.98px) {
        .colBtnR {
            text-align: center !important;
            margin-top: 5% !important;
        }

        .colR {
            padding-left: 18% !important;
            padding-right: 18% !important;
        }

        .imgR {
            margin-left: 25% !important;
        }

        .datepicker,
        .table-condensed {
            width: 200px !important;
            height: 150px !important;
            font-size: small !important;
        }

    }

    @media (max-width: 1194px) {
        .colBtnR {
            text-align: center !important;
            margin-top: 5% !important;
        }

        .datepicker,
        .table-condensed {
            width: 200px !important;
            height: 150px !important;
            font-size: small !important;
        }
    }

    @media (max-width: 566px) {
        .colR {
            padding-left: 10% !important;
            padding-right: 10% !important;
        }

        .imgR {
            margin-left: 19% !important;
        }

        .dataTables_info{
            display: none;
        }
    }

    @media (min-width: 1200px){
        .lbl_empleado{
            padding-left: 95px !important;
        }
    }
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        opacity: .8;
        background: rgb(252,252,252);
    }
    .selectorColumn  {
        padding: 0.0rem 0.6rem !important;
        color: #6c757d !important;
    }
    .allow-focus {
        padding: 0rem 0;
        min-width: 10em !important;
        height: auto;
        max-height: 250px;
        overflow: auto;
        position: absolute;
    }
    
    /* FINALIZACION */
</style>
<div class="loader" class="text-center"  style="display: flex !important; justify-content: center !important; align-items: center;">
    <img src="https://rhnube.com.pe/landing/images/logo_animado.gif" height="300" class="img-load" style="display: none">
</div>
<div class="card">
    <div class="card-header" style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1">
        <div class="row">
            <h4 class="header-title col-12 mt-0" style="margin-bottom: 0px;">{{$organizacion}}</h4>
        </div>
    </div>
</div>
<div class="row pl-3 pr-3">
    <div class="col-md-4">
        <div class="row">
            <label class="col-lg-12 col-form-label">Rango de fechas:</label>
            <input type="hidden" id="ID_START">
            <input type="hidden" id="ID_END">
            <div class="input-group col-10 text-center" style="padding-bottom: 5px;" id="fechaSelecH">
                <input type="text" id="fechaInput" {{-- onchange="cambiarF()" --}} class="form-control" data-input>
                <div class="input-group-prepend">
                    <div class="input-group-text form-control flatpickr">
                        <a class="input-button" data-toggle>
                            <i class="uil uil-calender"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-2" style="align-content: center;">
                <button type="button" id="btnRecargaTabla" class="btn btn-sm" style="background-color: #163552;" onclick="javascript:cambiarFCR()"> <img src="{{asset('landing/images/loupe (1).svg')}}" height="18" class="text-center mb-1"></button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group row busquedaP" id="" style="display: none">
            <label class="col-lg-12 col-form-label">Seleccionar por:</label>
            <div class="col-lg-12 ">
                <select id="areaT" data-plugin="customselect" class="form-control" multiple="multiple">
                    @foreach ($areas as $area)
                    <option value="{{$area->area_id}}">Área :
                        {{$area->area_descripcion}}</option>
                    @endforeach
                    @foreach ($cargos as $cargo)
                        <option value="{{ $cargo->idcargo }}">Cargo :
                            {{ $cargo->descripcion }}.</option>
                    @endforeach
                    @foreach ($locales as $local)
                        <option value="{{ $local->idlocal }}">Local :
                            {{ $local->descripcion }}.</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group row busquedaP" style="display: none">
            <label class="col-lg-12 col-form-label">Empleado:</label>
            <div class="col-lg-12">
                <select id="empleadoLT" data-plugin="customselect" class="form-control" multiple="multiple">
                    @foreach ($empleado as $emple)
                    <option value="{{$emple->emple_id}}">
                        {{$emple->perso_nombre}} {{$emple->perso_apPaterno}}
                        {{$emple->perso_apMaterno}}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="row" id="VacioImg" style="display: block;">
            <div class="col-xl-12 justify-content-center">
                <img style="margin-left:35%" src="{{URL::asset('admin/images/search-file.svg') }}" class="mr-2 imgR" height="220" /> <br> 
                <label for="" style="margin-left:35%;color:#7d7d7d" class="imgR">Realize una búsqueda para ver Actividad</label>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header" style="background-color: #ffffff">
                        
                        <div class="row" id="busquedaA" style="display: none">
                            <div class="col-md-12">
                                
                            </div>
                        </div>
                        <div class="dropdown mt-4" id="dropSelector" style="display: none">
                            <a class="dropdown-toggle dropReporte" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" style="cursor: pointer">
                                <div class="custom-control custom-switch mb-2">
                                    <input type="checkbox" class="custom-control-input" id="switchO" checked
                                        style="cursor: pointer">
                                    <label class="custom-control-label" for="switchO"
                                        style="font-weight: bold;font-size:13px">
                                        <img src="{{asset('landing/images/insert.svg')}}" height="18">
                                        Selector de columnas
                                    </label>
                                </div>
                            </a>
                            <div class="dropdown-menu allow-focus" style="margin: 0">
                                <h6 class="dropdown-header text-left"
                                    style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                    <img src="{{asset('landing/images/configuracionesD.svg')}}" class="mr-1"
                                        height="12" />
                                    Opciones
                                </h6>
                                <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                <ul class="dropdown-item dropdown-itemSelector selectorColumn" style="font-size: 12.5px; padding-top: 15px; margin: 0">
                                    <li class="liContenido">
                                        <input type="checkbox" checked id="" disabled="">
                                        <label for="">Código</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector selectorColumn" style="font-size: 12.5px; margin: 0">
                                    <li class="liContenido">
                                        <input type="checkbox" checked id="" disabled="">
                                        <label for="">Número de documento</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector selectorColumn" style="font-size: 12.5px; margin: 0">
                                    <li class="liContenido">
                                        <input type="checkbox" checked id="" disabled="">
                                        <label for="">Nombres y Apellidos</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector selectorColumn" style="font-size: 12.5px; margin: 0">
                                    <li class="liContenido">
                                        <input type="checkbox" id="colArea">
                                        <label for="colArea">Área</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector selectorColumn" style="font-size: 12.5px; margin: 0">
                                    <li class="liContenido">
                                        <input type="checkbox" id="colCargo">
                                        <label for="colCargo">Cargo</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector selectorColumn" style="font-size: 12.5px; margin: 0">
                                    <li class="liContenido">
                                        <input type="checkbox" id="colNivel">
                                        <label for="colNivel">Nivel</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector selectorColumn" style="font-size: 12.5px; margin: 0">
                                    <li class="liContenido">
                                        <input type="checkbox" id="colLocal">
                                        <label for="colLocal">Local</label>
                                    </li>
                                </ul>
                                <ul class="dropdown-item dropdown-itemSelector selectorColumn" style="font-size: 12.5px; margin: 0">
                                    <li class="liContenido">
                                        <input type="checkbox" id="colCentroCosto">
                                        <label for="colCentroCosto">Centro de costo</label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="row" id="fotmatoCampos" style="display: none;">
                            <div class="col-md-4">
                                <div class="form-group row rowPersonalizado">
                                    <div class="col-lg-5">
                                        <img src="{{asset('landing/images/fuenteR.svg')}}" height="18">
                                        <label for="formatoC" class="col-form-label pt-0 pb-0"
                                            style="font-weight: bold;font-size:13px">
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
                    </div>
                    <div class="card-body" id="tablaSinActividadD">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tablaHorario" class="table nowrap" style="font-size: 13px!important;width: 100%;">
                                    <thead style="background: #fafafa;" id="theadDHorario">
                                        <tr>
                                            <th>#</th>
                                            <th>Código</th>
                                            <th>Número de documento</th>
                                            <th>
                                                <img src="{{ URL::asset('admin/assets/images/users/empleado.png') }}" class="mr-2" alt="" />Nombres y apellidos
                                            </th>
                                            <th>Área</th>
                                            <th>Cargo</th>
                                            <th>Nivel</th>
                                            <th>Local</th>
                                            <th>Centro de Costo</th>
                                            <th>LUN.</th>
                                            <th>MAR.</th>
                                            <th>MIÉ.</th>
                                            <th>JUE.</th>
                                            <th>VIE.</th>
                                            <th>SÁB.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDHorario">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="tablaConActividadD" style="display: none">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="actividadDM" class="table nowrap"
                                    style="font-size: 13px!important;width:100%;">
                                    <thead style="background: #fafafa;" id="diasActvidad" style="width:100%!important">
                                    </thead>
                                    <tbody id="empleadoActividad">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div> <!-- end col-->
</div>
<!-- end row -->
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
    <script src="{{ URL::asset('admin/assets/libs/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')}}"></script>
    <script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')}}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('landing/js/reporteDispo.js') }}"></script>
    <script src="{{ asset('landing/js/reporteHorarios.js') }}"></script>
@endsection
@section('script-bottom')
    <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection