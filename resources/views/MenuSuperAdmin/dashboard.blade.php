@extends('layouts.verticalAd')

@section('css')
    <!-- Plugin css  CALENDAR-->


    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />

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

        </div>
    </div>
@endsection


@section('content')
<style>
    #chartdiv {


      height: 500px;
    }

    </style>
    <style>
        body {
            background-color: #ffffff;
        }

    </style>
    <script src="{{ URL::asset('admin/assets/libs/amcharts/lib/4/core.js')}}" ></script>
    <script src="{{ URL::asset('admin/assets/libs/amcharts/lib/4/charts.js')}}" ></script>
    <script src="{{ URL::asset('admin/assets/libs/amcharts/lib/4/themes/frozen.js')}}" ></script>
    <script src="{{ URL::asset('admin/assets/libs/amcharts/lib/4/themes/animated.js')}}" ></script>

    <!-- Chart code -->

    <script>
        am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_frozen);
        am4core.useTheme(am4themes_animated);
        // Themes end



        var chart = am4core.create('chartdiv', am4charts.XYChart)
        chart.colors.step = 2;

        chart.legend = new am4charts.Legend()
        chart.legend.position = 'top'
        chart.legend.paddingBottom = 10 //barro de cuanto en cuento
        chart.legend.labels.template.maxWidth = 180

        var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
        xAxis.dataFields.category = 'category'
        xAxis.renderer.cellStartLocation = 0.1
        xAxis.renderer.cellEndLocation = 0.9
        xAxis.renderer.grid.template.location = 0;

        var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
        yAxis.min = 0;

        function createSeries(value, name) {
            var series = chart.series.push(new am4charts.ColumnSeries())
            series.dataFields.valueY = value
            series.dataFields.categoryX = 'category'
            series.name = name

            series.events.on("hidden", arrangeColumns);
            series.events.on("shown", arrangeColumns);

            var bullet = series.bullets.push(new am4charts.LabelBullet())
            bullet.interactionsEnabled = false
            bullet.dy = 30;
            bullet.label.text = '{valueY}'
            bullet.label.fill = am4core.color('#ffffff')

            return series;
        }
        $.ajax({
        type: "post",
        url: "/sAdminDaOrga",
        data: {

        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                "content"
            ),
        },
        success: function (data) {
            chart.data=data.data;
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
        createSeries('first', 'Empleados de organizacion');
        createSeries('second', 'Empleados registrados');

        function arrangeColumns() {

            var series = chart.series.getIndex(0);

            var w = 1 - xAxis.renderer.cellStartLocation - (1 - xAxis.renderer.cellEndLocation);
            if (series.dataItems.length > 1) {
                var x0 = xAxis.getX(series.dataItems.getIndex(0), "categoryX");
                var x1 = xAxis.getX(series.dataItems.getIndex(1), "categoryX");
                var delta = ((x1 - x0) / chart.series.length) * w;
                if (am4core.isNumber(delta)) {
                    var middle = chart.series.length / 2;

                    var newIndex = 0;
                    chart.series.each(function(series) {
                        if (!series.isHidden && !series.isHiding) {
                            series.dummyData = newIndex;
                            newIndex++;
                        }
                        else {
                            series.dummyData = chart.series.indexOf(series);
                        }
                    })
                    var visibleCount = newIndex;
                    var newMiddle = visibleCount / 2;

                    chart.series.each(function(series) {
                        var trueIndex = chart.series.indexOf(series);
                        var newIndex = series.dummyData;

                        var dx = (newIndex - trueIndex + middle - newMiddle) * delta

                        series.animate({ property: "dx", to: dx }, series.interpolationDuration, series.interpolationEasing);
                        series.bulletsContainer.animate({ property: "dx", to: dx }, series.interpolationDuration, series.interpolationEasing);
                    })
                }
            }
        }

        }); // end am4core.ready()
        </script>






<script>
    am4core.ready(function() {

    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end

    // Create chart instance
    var chart = am4core.create("chartdiv2", am4charts.PieChart);

    // Set data
    var selected;
     var nEmp;
     var ngob;
     var nong;
     var nasoc;
     var notr;
    $.ajax({
        type: "post",
        url: "/sAdmintipoOrg",
        async: false,
        data: {

        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                "content"
            ),
        },
        success: function (data) {
            nEmp=data[0];
            ngob=data[1];
            nong=data[2];
            nasoc=data[3];
            notr=data[4];

            console.log(nEmp);
           return nEmp;
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
    console.log(nEmp);
    var types =
    [{
      type: "Empresa",
      percent: nEmp,
      color: chart.colors.getIndex(0)

    }, {
      type: "Gobierno",
      percent: ngob,
      color: chart.colors.getIndex(1)
    },
    {
      type: "ONG",
      percent: nong,
      color: chart.colors.getIndex(2)

    }, {
      type: "Asociación",
      percent: nasoc,
      color: chart.colors.getIndex(3)
    },
    {
      type: "Otros",
      percent: notr,
      color: chart.colors.getIndex(4)
    }

    ];

    // Add data
    chart.data = generateChartData();

    // Add and configure Series
    var pieSeries = chart.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "percent";
    pieSeries.dataFields.category = "type";
    pieSeries.slices.template.propertyFields.fill = "color";
    pieSeries.slices.template.propertyFields.isActive = "pulled";
    pieSeries.slices.template.strokeWidth = 0;

    function generateChartData() {
      var chartData = [];
      for (var i = 0; i < types.length; i++) {
        if (i == selected) {
          for (var x = 0; x < types[i].subs.length; x++) {
            chartData.push({
              type: types[i].subs[x].type,
              percent: types[i].subs[x].percent,
              color: types[i].color,
              pulled: true
            });
          }
        } else {
          chartData.push({
            type: types[i].type,
            percent: types[i].percent,
            color: types[i].color,
            id: i
          });
        }
      }
      return chartData;
    }

    pieSeries.slices.template.events.on("hit", function(event) {
      if (event.target.dataItem.dataContext.id != undefined) {
        selected = event.target.dataItem.dataContext.id;
      } else {
        selected = undefined;
      }
      chart.data = generateChartData();
    });

    }); // end am4core.ready()
    </script>

        <div class="row">
            <div class="col-md-6 col-xl-3">
                <div class="card" style="border: 0.6px solid #e8e9eb;">
                    <div class="card-body p-0">
                        <div class="media p-3">
                            <div class="media-body">
                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Organizaciones</span>
                                <h2 class="mb-0">{{$nOrganizaciones}}</h2>
                            </div>
                            <div class="align-self-center">
                                <span class="icon-lg icon-dual-primary" data-feather="shopping-bag"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card" style="border: 0.6px solid #e8e9eb;">
                    <div class="card-body p-0">
                        <div class="media p-3">
                            <div class="media-body">
                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Usuarios Admin.</span>
                            <h2 class="mb-0">{{$nusuAdmin}}</h2>
                            </div>
                            <div class="align-self-center">
                                <span class="icon-lg icon-dual-success" data-feather="user"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card" style="border: 0.6px solid #e8e9eb;">
                    <div class="card-body p-0">
                        <div class="media p-3">
                            <div class="media-body">
                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">Usuarios Inv.</span>
                            <h2 class="mb-0">{{$nusuInv}}</h2>
                            </div>
                            <div class="align-self-center">
                                <span class="icon-lg icon-dual-warning" data-feather="users"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card" style="border: 0.6px solid #e8e9eb;">
                    <div class="card-body p-0">
                        <div class="media p-3">
                            <div class="media-body">
                                <span class="text-muted text-uppercase font-size-12 font-weight-bold">
                                    Empleados {{-- activos  --}}</span>
                                <h2 class="mb-0">{{$nempleado}}</h2>
                            </div>
                            <div class="align-self-center">
                                <span class="icon-lg icon-dual-info" data-feather="briefcase"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label for="" class="font-weight-bold">Tipo de Organizacion</label>
            </div>
            <div class="col-md-6">
                <label for="" class="font-weight-bold">Ultimo empleados registrados</label>
            </div>
            <div style="height: 280px" class="col-md-6" id="chartdiv2"></div>
            <div class="col-xl-6">
                        <div class="table-responsive mt-4" style="margin-top: 7px!important">
                            <table class="table table-hover table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Nombres</th>
                                        <th scope="col">Apellidos</th>
                                        <th scope="col">Fecha de creacion</th>
                                        <th scope="col">Organizacion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listaempleado as $listaempleados)
                                    <tr>
                                        <td>{{$listaempleados->perso_nombre}}</td>
                                        <td>{{$listaempleados->perso_apPaterno}} {{$listaempleados->perso_apMaterno}}  </td>
                                        @php
                                            $fechac=$listaempleados->created_at;
                                            $fechaCo=date_create($fechac);
                                            $fechaCo2= date_format($fechaCo, 'd-m-Y');
                                        @endphp
                                        <td>{{$fechaCo2}} </td>
                                        <td><span class="badge badge-soft-primary py-1">{{$listaempleados->organi_razonSocial}}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

            </div> <!-- end col-->
        </div>
    <div class="row row-divided">

        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0"
                    style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1;padding-top: 0px;">
                    <h5 style="font-size: 16px" class="card-title font-weight-bold mb-2 mt-2" style="color: #163552">
                       Organizaciones
                    </h5>
                </div>
                <div class="card-body border">
                    <div class="row justify-content-center">

                                <div  class="col-md-12" id="chartdiv"></div>

                    </div>
                </div>
            </div>
        </div>

        </div>

@endsection
@section('script')

    <!-- Plugins Js -->
    <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>


    <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>

    <script src="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>



@endsection

@section('script-bottom')
    <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>

@endsection
