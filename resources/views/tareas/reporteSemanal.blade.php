@extends('layouts.vertical')

@section('css')
<link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.css')
    }}" rel="stylesheet" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{
        URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')
        }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/chart/Chart.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/zoom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')
    }}" rel="stylesheet" />
@endsection

@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0">Reporte semanal - Búsqueda por fecha</h4>
    </div>
</div>
@endsection

@section('content')
<style>
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
    }

    @media (max-width: 1194px) {
        .colBtnR {
            text-align: center !important;
            margin-top: 5% !important;
        }
    }
    @media (max-width: 566px) {
        .colR {
            padding-left: 10% !important;
            padding-right: 10% !important;
        }

        .imgR {
            margin-left: 20% !important;
        }
    }
</style>
<div class="row justify-content-center p-5">
    <div class="col-xl-3" style="padding-left: 2%;padding-right: 0%;">
        <div class="input-group col-xl-12 text-center colR" style="padding-left: 0px;padding-right: 0px;"
            id="fechaSelec">
            <input type="text" id="fecha" class="form-control" data-input>
            <div class="input-group-prepend">
                <div class="input-group-text form-control flatpickr">
                    <a class="input-button" data-toggle>
                        <i class="uil uil-calender"></i>
                    </a>
                </div>
            </div>
            <div class="pl-2">
                <button type="button" class="btn btn-sm" style="background-color: #163552;"
                    onclick="javascript:buscarReporte()">
                    <img src="{{asset('landing/images/loupe (1).svg')}}" height="18" class="text-center mb-1"></button>
            </div>
        </div>
    </div>

    <div class="col-xl-2 colBtnR" style="margin-right: 5%;">
        <button type="button" class="btn btn-sm pb-2" style="background-color: #163552;"
            onclick="javascript:mostrarGrafica()"><i class="fa fa-eye mr-1"></i>VER GRAFICO
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
        <div class="row" id="graficaReporte" style="display: none">
            <div class="col-lg-12">
                <!-- Portlet card -->
                <div class="card">
                    <div class="card-body">
                        <canvas id="myChart" height="35vh" width="85vw"></canvas>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header" style="background-color: #ffffff">
                        <div class="row" id="busquedaP" style="display: none">
                            <div class="col-xl-6">
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
                            <div class="col-xl-6">
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Cargo:</label>
                                    <div class="col-lg-10 pl-0">
                                        <select id="cargo" data-plugin="customselect" class="form-control"
                                            multiple="multiple">
                                            @foreach ($cargos as $cargo)
                                            <option value="{{$cargo->cargo_id}}">
                                                {{$cargo->cargo_descripcion}}</option>
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
                        <div class="table-responsive-xl">
                            <table id="Reporte" class="table nowrap" style="font-size: 13px!important;width:
                                        100%;">
                                <thead style="background: #fafafa;" id="dias" style="width:100%!important">
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
                                        <th class="text-center">ACTIV.</th>
                                    </tr>
                                </thead>
                                <tbody id="empleado">
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card body-->
                    <div class="card-body" id="tablaConActividadD" style="display: none">
                        <div class="table-responsive-xl">
                            <table id="actividadD" class="table nowrap" style="font-size: 13px!important;width:100%;">
                                <thead style="background: #fafafa;" id="diasActvidad" style="width:100%!important">
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
                                <tbody id="empleadoActividad">
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card body-->
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
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js')}}"></script>
<!-- datatable js -->
<script src="{{ URL::asset('admin/assets/libs/chart/Chart.min.js') }}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{
    URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
<script src="{{asset('admin/assets/libs/bootstrap-datetimepicker-master/moment.js')}}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/es.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/buttons.html5.min.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/pdfmake.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/vfs_fonts.js') }}"></script>
<script src="{{asset('landing/js/reporteS.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
<script>
    //     $('#graficaReporte').hide();
//     var empleadosDefecto = @json($empleado);
//     empleadosDefecto = empleadosDefecto.map(function(empleado){
//         return empleado.perso_nombre.charAt(0) + empleado.perso_apPaterno.charAt(0) + empleado.perso_apMaterno.charAt(0)
//     });
//     var tablaDefecto = $('#Reporte').html();

//     var ctx = $('#myChartD');
//     var chart = new Chart(ctx, {
//     // The type of chart we want to create
//     type: 'bar',

//     // The data for our dataset
//     data: {
//         labels: empleadosDefecto ,
//         datasets: [{
//         }]
//     },

//     // Configuration options go here
//     options: {
//         legend:{
//             display:false
//         },
//         scales: {
//             xAxes: [{
//                 stacked: true,
//                 gridLines: {
//                     display:false
//                 }
//             }],
//             yAxes: [{
//                 stacked: true
//             }]
//         }
//     }
// });
</script>
@endsection