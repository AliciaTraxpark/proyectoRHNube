@extends('layouts.vertical')

@section('css')
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
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

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                        <div class="col-lg-8 text-right">
                            <button type="button" class="btn btn-light"><i class="uil uil-arrow-left"></i></button>
                            <button type="button" class="btn btn-light"><i class="uil uil-arrow-right"></i></button>
                        </div>
                        <div class="col-lg-4 text-right">
                            <div class="form-group mb-sm-0 mr-2">
                                <input type="date" name="date" class="form-control" id="fecha" style="min-width: 190px;" />
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
                                <p class="sub-header">
                                    America-Bogota
                                </p>
                                <table id="tablaReporte" class="table dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Office</th>
                                            <th>Age</th>
                                            <th>Start date</th>
                                            <th>Salary</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Tiger Nixon</td>
                                            <td>System Architect</td>
                                            <td>Edinburgh</td>
                                            <td>61</td>
                                            <td>2011/04/25</td>
                                            <td>$320,800</td>
                                        </tr>
                                        <tr>
                                            <td>Angelica Ramos</td>
                                            <td>Chief Executive Officer (CEO)</td>
                                            <td>London</td>
                                            <td>47</td>
                                            <td>2009/10/09</td>
                                            <td>$1,200,000</td>
                                        </tr>
                                        <tr>
                                            <td>Gavin Joyce</td>
                                            <td>Developer</td>
                                            <td>Edinburgh</td>
                                            <td>42</td>
                                            <td>2010/12/22</td>
                                            <td>$92,575</td>
                                        </tr>
                                        <tr>
                                            <td>Jennifer Chang</td>
                                            <td>Regional Director</td>
                                            <td>Singapore</td>
                                            <td>28</td>
                                            <td>2010/11/14</td>
                                            <td>$357,650</td>
                                        </tr>
                                        <tr>
                                            <td>Brenden Wagner</td>
                                            <td>Software Engineer</td>
                                            <td>San Francisco</td>
                                            <td>28</td>
                                            <td>2011/06/07</td>
                                            <td>$206,850</td>
                                        </tr>

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
