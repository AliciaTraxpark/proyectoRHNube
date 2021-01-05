@extends('layouts.vertical')

@section('css')
<link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.css')}}" rel="stylesheet" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<link href="{{URL::asset('admin/assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/multiselect/multiselect.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-datetimepicker-master/bootstrap-datetimepicker.css')}}"
    rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-datetimepicker-master/bootstrap-datetimepicker.min.css')}}"
    rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/apexcharts/apexcharts.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/chart/Chart.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/css/notify.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/css/prettify.css')}}" rel="stylesheet" type="text/css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
@endsection

@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0" style="font-weight: bold">
            Reporte mensual - Búsqueda por fecha
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

    /* FINALIZACION */
</style>
<div class="row justify-content-center p-5">
    <div class="col-xl-4" style="padding-left: 2%;padding-right: 0%;">
        <div class="input-group col-xl-12 colR">
            <input type="text" id="fechaMensual" class="form-control text-center">
            <div class="input-group-prepend">
                <div class="input-group-text form-control"><i class="uil uil-calender"></i></div>
            </div>
            <div class="pl-2">
                <button type="button" class="btn btn-sm" style="background-color: #163552;"
                    onclick="javascript:buscarReporte()"> <img src="{{asset('landing/images/loupe (1).svg')}}"
                        height="18" class="text-center mb-1"></button>
            </div>
        </div>
    </div>
    <div class="col-xl-2 colBtnR" style="margin-right: 5%;">
        <button type="button" class="btn btn-sm pb-2" style="background-color: #163552;"
            onclick="javascript:mostrarGraficaMensual()"><i class="fa fa-eye mr-1"></i>VER GRAFICO
        </button>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="row" id="VacioImg" style="display: none">
            <div class="col-xl-12">
                <img style="margin-left:35%" src="{{
                        URL::asset('admin/images/search-file.svg') }}" class="mr-2 imgR" height="220" /> <br> <label
                    for="" style="margin-left:35%;color:#7d7d7d" class="imgR">Realize una
                    búsqueda para ver Actividad</label>
            </div>
        </div>
        <div class="row" id="graficaReporteMensual" style="display: none">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div id="myChartMensual"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header" style="background-color: #ffffff">
                        <div class="row pt-2" id="busquedaP" style="display: none">
                            <div class="col-xl-4">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Área:</label>
                                    <div class="col-lg-10 pl-0">
                                        <select id="area" data-plugin="customselect" class="form-control"
                                            multiple="multiple">
                                            @foreach ($areas as $area)
                                            <option value="{{$area->area_id}}">
                                                {{$area->area_descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="col-xl-8">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Empleado:</label>
                                    <div class="col-lg-10 pl-0">
                                        <select id="empleadoL" data-plugin="customselect" class="form-control"
                                            multiple="multiple">
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
                        <div class="row  mt-2" id="busquedaA" style="display: none">
                            <div class="col-md-12">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="customSwitchD"
                                        onclick="javascript:cambiarTabla()">
                                    <label class="custom-control-label" for="customSwitchD"
                                        style="font-weight: bold">Mostrar Actividad Diaria</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="tablaSinActividadD">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="ReporteMensual" class="table nowrap" style="font-size: 13px!important;width:
                                        100%;">
                                    <thead style="background: #fafafa;" id="diasMensual">
                                        <tr>
                                            <th>#</th>
                                            <th>
                                                <img src="{{
                                                        URL::asset('admin/assets/images/users/empleado.png')
                                                        }}" class="mr-2" alt="" />Miembro
                                            </th>
                                            <th>LUN.</th>
                                            <th>MAR.</th>
                                            <th>MIÉ.</th>
                                            <th>JUE.</th>
                                            <th>VIE.</th>
                                            <th>SÁB.</th>
                                            <th>TOTAL</th>
                                            <th>ACTIV.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="empleadoMensual">
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
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
<script src="{{
    URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js')
    }}"></script>
<!-- datatable js -->
<script src="{{ URL::asset('admin/assets/libs/chart/Chart.min.js') }}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{
    URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/apexcharts/apexcharts.js') }}"></script>
<script src="{{asset('admin/assets/libs/bootstrap-datetimepicker-master/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('admin/assets/libs/bootstrap-datetimepicker-master/bootstrap-datetimepicker.es.js')}}"></script>
<script src="{{asset('admin/assets/libs/bootstrap-datetimepicker-master/moment.js')}}"></script>
<script src="{{asset('admin/assets/libs/bootstrap-datetimepicker-master/es.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/buttons.html5.min.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/pdfmake.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/vfs_fonts.js') }}"></script>
<script src="{{asset('landing/js/reporteM.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection