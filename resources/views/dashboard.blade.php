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
        <div class="col-md-4">
            <div id="chart-container">FusionCharts XT will load here!</div>
        </div>
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
<script type="text/javascript">
    FusionCharts.ready(function(){
        var chartObj = new FusionCharts({
type: 'doughnut2d',
renderAt: 'chart-container',
width: '500',
height: '400',
dataFormat: 'json',
dataSource: {
    "chart": {
        "caption": "Split of Revenue by Product Categories",
        "subCaption": "Last year",
        "numberPrefix": "$",
        "bgColor": "#ffffff",
        "startingAngle": "310",
        "showLegend": "1",
        "defaultCenterLabel": " $64.08K",
        "centerLabel": "Revenue from $label: $value",
        "centerLabelBold": "1",
        "showTooltip": "0",
        "decimals": "0",
        "theme": "fusion"
    },
    "data": [{
        "label": "Food",
        "value": "28504"
    }, {
        "label": "Apparels",
        "value": "14633"
    }, {
        "label": "Electronics",
        "value": "10507"
    }, {
        "label": "Household",
        "value": "4910"
    }]
}
}
);
        chartObj.render();
    });
</script>
@endsection
