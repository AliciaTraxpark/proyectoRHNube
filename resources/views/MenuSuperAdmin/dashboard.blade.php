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
            {{-- <h4 class="mb-1 mt-0">Horarios</h4> --}}
            <h4 class="header-title mt-0 "></i>Admin</h4>
        </div>
    </div>
@endsection


@section('content')
<style>
    #chartdiv {

      width: 100%;
      height: 300px;
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
                                <div class="col-md-1"></div>
                                <div class="col-md-12" id="chartdiv"></div>
                                <div class="col-md-1"></div>
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
