@extends('layouts.vertical')


@section('css')
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
@endsection

@section('breadcrumb')

<div class="row page-title align-items-center">
</div>
@endsection
    @if ($variable==0)
        @section('content')
        <div class="row">
            <div class="col-md-12  text-center">
                <a href="{{route('calendario')}}"><button class="boton btn btn-default mr-1" ><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                    COMIENZA AHORA</button></a>
            </div>
        </div>
        <!-- end row -->
        @endsection
    @else
        @section('content')
        <div class="row">
            <div class="col-md-4"  id="chart-container"></div>
            <div class="col-md-4"  id="chart-container1"></div>
            <div class="col-md-4"  id="chart-container2"></div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-4"  id="chart-container3"></div>
            <div class="col-md-4"  id="chart-container4"></div>
            <div class="col-md-4"  id="chart-container5"></div>
        </div>
        <!-- products -->

        @endsection
    @endif
@section('script')
<!-- optional plugins -->
<script src="{{ URL::asset('admin/assets/libs/moment/moment.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{asset('admin/assets/js/chart.js')}}"></script>
<script>
    FusionCharts.ready(function() {
  var revenueChart = new FusionCharts({
    type: 'doughnut2d',
    renderAt: 'chart-container',
    width: '350',
    height: '350',
    dataFormat: 'json',
    dataSource: {
      "chart": {
        "pieRadius": "50",
        "doughnutRadius": "40",
        "bgColor": "#ffffff",
        "startingAngle": "310",
        "showLegend": "1",
        "defaultCenterLabel": "350 Por área",
        "centerLabel": "$label: $value",
        "centerLabelBold": "1",
        "showTooltip": "0",
        "decimals": "0",
        "theme": "fusion"
      },
      "data": [{
          "label": "Contabilidad",
          "value": "285"
        },
        {
          "label": "Logística",
          "value": "146"
        },
        {
          "label": "Administración",
          "value": "105"
        },
        {
          "label": "Producción",
          "value": "491"
        },
        {
          "label": "Comerciales",
          "value": "49"
        }
      ]
    }
  }).render();
    var revenueChart = new FusionCharts({
    type: 'doughnut2d',
    renderAt: 'chart-container1',
    width: '350',
    height: '350',
    dataFormat: 'json',
    dataSource: {
      "chart": {
        "pieRadius": "50",
        "doughnutRadius": "40",
        "bgColor": "#ffffff",
        "startingAngle": "310",
        "showLegend": "1",
        "defaultCenterLabel": "350 Por nivel",
        "centerLabel": "$label: $value",
        "centerLabelBold": "1",
        "showTooltip": "0",
        "decimals": "0",
        "theme": "fusion"
      },
      "data": [{
          "label": "Operarios de Prod.",
          "value": "285"
        },
        {
          "label": "Jefaturas",
          "value": "146"
        },
        {
          "label": "Ejecutivos I",
          "value": "105"
        },
        {
          "label": "Ejecutivos II",
          "value": "491"
        }
      ]
    }
  }).render();
  var revenueChart = new FusionCharts({
    type: 'doughnut2d',
    renderAt: 'chart-container2',
    width: '350',
    height: '350',
    dataFormat: 'json',
    dataSource: {
      "chart": {
        "pieRadius": "50",
        "doughnutRadius": "40",
        "bgColor": "#ffffff",
        "startingAngle": "310",
        "showLegend": "1",
        "defaultCenterLabel": "350 Por contrato",
        "centerLabel": "$label: $value",
        "centerLabelBold": "1",
        "showTooltip": "0",
        "decimals": "0",
        "theme": "fusion"
      },
      "data": [{
          "label": "Por servicio",
          "value": "285"
        },
        {
          "label": "Planilla",
          "value": "146"
        }
      ]
    }
  }).render();
  var revenueChart = new FusionCharts({
    type: 'doughnut2d',
    renderAt: 'chart-container3',
    width: '350',
    height: '350',
    dataFormat: 'json',
    dataSource: {
      "chart": {
        "pieRadius": "50",
        "doughnutRadius": "40",
        "bgColor": "#ffffff",
        "startingAngle": "310",
        "showLegend": "1",
        "defaultCenterLabel": "350 Por Centro Costo",
        "centerLabel": "$label: $value",
        "centerLabelBold": "1",
        "showTooltip": "0",
        "decimals": "0",
        "theme": "fusion"
      },
      "data": [{
          "label": "CCosto FIN",
          "value": "285"
        },
        {
          "label": "CCosto I + D",
          "value": "146"
        },
        {
          "label": "CCosto COM",
          "value": "146"
        },
        {
          "label": "CCosto ADM",
          "value": "146"
        },
        {
          "label": "CCosto PDR",
          "value": "146"
        }
      ]
    }
  }).render();
  var revenueChart = new FusionCharts({
    type: 'doughnut2d',
    renderAt: 'chart-container4',
    width: '350',
    height: '350',
    dataFormat: 'json',
    dataSource: {
      "chart": {
        "pieRadius": "50",
        "doughnutRadius": "40",
        "bgColor": "#ffffff",
        "startingAngle": "310",
        "showLegend": "1",
        "defaultCenterLabel": "350 Por Local",
        "centerLabel": "$label: $value",
        "centerLabelBold": "1",
        "showTooltip": "0",
        "decimals": "0",
        "theme": "fusion"
      },
      "data": [{
          "label": "Local Lima",
          "value": "285"
        },
        {
          "label": "Planta",
          "value": "146"
        }
      ]
    }
  }).render();
  var revenueChart = new FusionCharts({
    type: 'doughnut2d',
    renderAt: 'chart-container5',
    width: '350',
    height: '350',
    dataFormat: 'json',
    dataSource: {
      "chart": {
        "pieRadius": "50",
        "doughnutRadius": "40",
        "bgColor": "#ffffff",
        "startingAngle": "310",
        "showLegend": "1",
        "defaultCenterLabel": "350 Por rango de edad",
        "centerLabel": "$label: $value",
        "centerLabelBold": "1",
        "showTooltip": "0",
        "decimals": "0",
        "theme": "fusion"
      },
      "data": [{
          "label": "19 a 24",
          "value": "285"
        },
        {
          "label": "24 a 30",
          "value": "146"
        },
        {
          "label": "30 a 40",
          "value": "146"
        },
        {
          "label": "40 a 50",
          "value": "146"
        },
        {
          "label": "50 a 70",
          "value": "146"
        }
      ]
    }
  }).render();
});

</script>
@endsection
@section('script-bottom')
<!-- init js -->
<script src="{{ URL::asset('admin/assets/js/pages/dashboard.init.js') }}"></script>
@endsection
