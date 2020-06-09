@extends('layouts.vertical')

@section('css')
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
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
                                <div id="chart-container"  dir="ltr"></div>
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
                                <table id="tablaReporte" class="table dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>Member</th>
                                            <th>Mon</th>
                                            <th>Tue</th>
                                            <th>Wed</th>
                                            <th>Thu</th>
                                            <th>Fri</th>
                                            <th>Sat</th>
                                            <th>Weekly Total</th>
                                            <th>Activity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      @foreach ($empleado as $empleados)
                                        <tr>
                                            <td>{{$empleados->perso_nombre}} {{$empleados->perso_apPaterno}} {{$empleados->perso_apMaterno}}</td>
                                            <td>{{$empleados->Total_Envio}}</td>
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
<script src="{{asset('landing/js/tablaReporte.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script>
    flatpickr("#fecha", {
        mode: "range"
    });

    const dataSource = {
  chart: {
    caption: "Horas trabajadas por Miembro",
    theme: "fusion"
  },
  data: [
    {
      label: "Venezuela",
      value: "290"
    },
    {
      label: "Saudi",
      value: "260"
    },
    {
      label: "Canada",
      value: "230"
    },
    {
      label: "Iran",
      value: "200"
    },
    {
      label: "Russia",
      value: "170"
    },
    {
      label: "UAE",
      value: "140"
    },
    {
      label: "US",
      value: "110"
    },
    {
      label: "China",
      value: "80"
    },
    {
      label: "Venezuela",
      value: "90"
    },
    {
      label: "Saudi",
      value: "60"
    },
    {
      label: "Canada",
      value: "80"
    },
    {
      label: "Iran",
      value: "140"
    },
    {
      label: "Russia",
      value: "115"
    },
    {
      label: "UAE",
      value: "100"
    },
    {
      label: "US",
      value: "30"
    },
    {
      label: "China",
      value: "30"
    }
  ]
};

FusionCharts.ready(function() {
  var myChart = new FusionCharts({
    type: "column2d",
    renderAt: "chart-container",
    width: "100%",
    height: "450%",
    dataFormat: "json",
    dataSource
  }).render();
});
</script>
@endsection
