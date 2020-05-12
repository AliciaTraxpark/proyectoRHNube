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
            <div class="col-md-6"  id="chart-container"></div>
        <div class="col-md-6"  id="chart-container1"></div>
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
    width: '550',
    height: '450',
    dataFormat: 'json',
    dataSource: {
      "chart": {
        "caption": "Split of Revenue by Product Categories",
        "subCaption": "Last year",
        "numberPrefix": "$",
        "bgColor": "#ffffff",
        "startingAngle": "310",
        "showLegend": "1",
        "defaultCenterLabel": "Total revenue: $64.08K",
        "centerLabel": "Revenue from $label: $value",
        "centerLabelBold": "1",
        "showTooltip": "0",
        "decimals": "0",
        "theme": "fusion"
      },
      "data": [{
          "label": "Food",
          "value": "28504"
        },
        {
          "label": "Apparels",
          "value": "14633"
        },
        {
          "label": "Electronics",
          "value": "10507"
        },
        {
          "label": "Household",
          "value": "4910"
        }
      ]
    }
  }).render();
    var revenueChart = new FusionCharts({
    type: 'doughnut2d',
    renderAt: 'chart-container1',
    width: '550',
    height: '450',
    dataFormat: 'json',
    dataSource: {
      "chart": {
        "caption": "Split of Revenue by Product Categories",
        "subCaption": "Last year",
        "numberPrefix": "$",
        "bgColor": "#ffffff",
        "startingAngle": "310",
        "showLegend": "1",
        "defaultCenterLabel": "Total revenue: $64.08K",
        "centerLabel": "Revenue from $label: $value",
        "centerLabelBold": "1",
        "showTooltip": "0",
        "decimals": "0",
        "theme": "fusion"
      },
      "data": [{
          "label": "Food",
          "value": "28504"
        },
        {
          "label": "Apparels",
          "value": "14633"
        },
        {
          "label": "Electronics",
          "value": "10507"
        },
        {
          "label": "Household",
          "value": "4910"
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
{{-- <script>
    const dataSource = {
    chart: {
    showpercentvalues: "1",
    defaultcenterlabel: "Por área:",
    pieRadius: "90",
    doughnutRadius: "80",
    aligncaptionwithcanvas: "0",
    captionpadding: "0",
    decimals: "1",
    plottooltext:
      "<b>$percentValue</b><b>$label</b>",
    centerlabel: "Por área: $value",
    theme: "fusion"
  },
  data: [
    {
      label: "Contabilidad",
      value: "10"
    },
    {
      label: "Logística",
      value: "53"
    },
    {
      label: "Administración",
      value: "105"
    },
    {
      label: "Producción",
      value: "189"
    },
    {
      label: "Comerciales",
      value: "179"
    }
  ]
};

FusionCharts.ready(function() {
  var myChart = new FusionCharts({
    type: "doughnut2d",
    renderAt: "chart-container",
    width: "50%",
    height: "50%",
    dataFormat: "json",
    dataSource
  }).render();
});

</script> --}}

@endsection
