@extends('layouts.vertical')

@section('css')
<link rel="shortcut icon" href="https://rhsolution.com.pe/wp-content/uploads/2019/06/small-logo-rh-solution-64x64.png"
    sizes="32x32">
<link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.css')
    }}" rel="stylesheet" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<link href="{{
    URL::asset('admin/assets/libs/bootstrap-datetimepicker-master/bootstrap-datetimepicker.css')
    }}" rel="stylesheet" type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/bootstrap-datetimepicker-master/bootstrap-datetimepicker.min.css')
    }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/chart/Chart.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')
    }}" rel="stylesheet" />
@endsection

@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0">Reporte Semanal - Búsqueda por fecha</h4>
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
        width: 300px !important;
        height: 300px !important;
        min-width: 50% !important;
        min-height: 50% !important;
        font-size: small !important;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row pb-2">
                    <div class="col-md-9">
                        <button type="button" class="btn btn-sm mt-1" style="background-color: #163552;"
                            onclick="javascript:mostrarGrafica()"><i class="fa fa-eye mr-1"></i>VER GRAFICO
                        </button>
                    </div>
                    <div class="col-md-3 text-right">
                        <div class="input-group col-md-12 pl-5">
                            <input type="text" id="fecha" class="form-control">
                            <div class="input-group-prepend">
                                <div class="input-group-text form-control"><i class="uil uil-calender"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row" id="graficaReporte" style="display: none">
                    <div class="col-lg-12">
                        <!-- Portlet card -->
                        <div class="card">
                            <div class="card-body">
                                <canvas id="myChartD" height="35vh" width="85vw"></canvas>
                                <canvas id="myChart" height="35vh" width="85vw"></canvas>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mt-0 mb-1">TRAXPARK</h4>
                                <p class="sub-header" style="margin-bottom:
                                    0px" id="zonaHoraria">
                                <br>
                                <div class="table-responsive-xl">
                                    <table id="Reporte" class="table nowrap" style="font-size: 13px!important;width:
                                        100%;">
                                        <thead style="background: #fafafa;" id="dias">
                                            <tr>
                                                <th><img src="{{
                                                        URL::asset('admin/assets/images/users/empleado.png')
                                                        }}" class="mr-2" alt="" />Miembro</th>
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
                                        <tbody id="empleado">
                                            @foreach ($empleado as $empleados)
                                            <tr>
                                                <td>{{$empleados->perso_nombre}}
                                                    {{$empleados->perso_apPaterno}}
                                                    {{$empleados->perso_apMaterno}}</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div> <!-- end card body-->
                        </div>
                    </div>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>
<!-- end row -->
@endsection
@section('script')
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
<script src="{{
    URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js')
    }}"></script>
<!-- Vendor js -->
{{-- <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script> --}}
<!-- App js -->
{{-- <script src="{{asset('admin/assets/js/app.min.js')}}"></script> --}}
<!-- datatable js -->
<script src="{{ URL::asset('admin/assets/libs/chart/Chart.min.js') }}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{
    URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{asset('admin/assets/libs/bootstrap-datetimepicker-master/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('admin/assets/libs/bootstrap-datetimepicker-master/bootstrap-datetimepicker.es.js')}}"></script>
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
    $('#graficaReporte').hide();
    var empleadosDefecto = @json($empleado);
    empleadosDefecto = empleadosDefecto.map(function(empleado){
        return empleado.perso_nombre.charAt(0) + empleado.perso_apPaterno.charAt(0) + empleado.perso_apMaterno.charAt(0)
    });
    console.log(empleadosDefecto);
    var tablaDefecto = $('#Reporte').html();

    var ctx = $('#myChartD');
    var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
        labels: empleadosDefecto ,
        datasets: [{
        }]
    },

    // Configuration options go here
    options: {
        legend:{
            display:false
        },
        scales: {
            xAxes: [{
                stacked: true,
                gridLines: {
                    display:false
                }
            }],
            yAxes: [{
                stacked: true
            }]
        }
    }
});
</script>
@endsection