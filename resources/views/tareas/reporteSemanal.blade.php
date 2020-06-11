@extends('layouts.vertical')

@section('css')
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/chart/Chart.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0">Reporte Semanal</h4>
    </div>
</div>
@endsection

@section('content')
<style>
     .flex-wrap{
    text-align: right!important;
    display: block!important;
    margin-bottom: 10px;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label style="font-weight: 700">Búsqueda por fecha</label>
                    </div>
                        <div class="col-md-8 text-right">
                            <button type="button" class="btn btn-light"><i class="uil uil-arrow-left"></i></button>
                            <button type="button" class="btn btn-light"><i class="uil uil-arrow-right"></i></button>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="input-group col-md-12 text-right">
                                <input type="date" name="date" class="form-control" id="fecha" style="min-width: 190px;" />
                                <div class="input-group-prepend">
                                  <div class="input-group-text form-control "><i class="uil uil-calender"></i></div>
                              </div>
                            </div>
                        </div>
                </div>
                <br>
                <div class="row">
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
                                <p class="sub-header" style="margin-bottom: 0px">
                                    America-Bogota
                                </p>
                                <br>
                                <table id="tablaReporte" class="table dt-responsive nowrap" style="font-size: 13px!important">
                                    <thead style="background: #fafafa;" id="dias">
                                        <tr>
                                            <th><img src="{{ URL::asset('admin/assets/images/users/empleado.png') }}" class=" mr-2" alt="" />Miembro</th>
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
                                        <td>{{$empleados->perso_nombre}} {{$empleados->perso_apPaterno}} {{$empleados->perso_apMaterno}}</td>
                                        <td id="td"></td>
                                      </tr>
                                      @endforeach
                                    </tbody>
                                </table>
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
<!-- Vendor js -->
<script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>
<!-- App js -->
<script src="{{asset('admin/assets/js/app.min.js')}}"></script>
<!-- datatable js -->
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/chart/Chart.min.js') }}"></script>
<script src="{{asset('landing/js/tablaReporte.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/moment.js')}}" ></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/es.js')}}" ></script>
<script src="{{asset('landing/js/reporteS.js')}}"></script>
<script>
    var tablaDefecto = $('#tablaReporte').html();

    var ctx = $('#myChartD');
    var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
        }]
    },

    // Configuration options go here
    options: {
        legend:{
            display:false
        }
    }
});
</script>
@endsection
