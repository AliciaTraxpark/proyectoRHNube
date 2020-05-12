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

        <!-- products -->
        <div id="chart-container"></div>
        @endsection
    @endif
@section('script')
<!-- optional plugins -->
<script src="{{ URL::asset('admin/assets/libs/moment/moment.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{asset('admin/assets/js/chart.js')}}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
<script src="{{ URL::asset('admin/assets/js/pages/dashboard.init.js') }}"></script>
<script>
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

</script>
@endsection
