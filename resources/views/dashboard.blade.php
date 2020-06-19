@extends('layouts.vertical')


@section('css')
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<link href="{{ URL::asset('admin/assets/libs/chart/Chart.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')
    }}" rel="stylesheet" />
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
<div class="row page-title align-items-center">
</div>
@endsection
@if ($variable==0)
@section('content')
</style>
<div class="row">
    <div class="col-md-12  text-center">
        <a href="{{route('calendario')}}"><button class="boton btn btn-default mr-1"><span
                    class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                COMIENZA AHORA</button></a>
    </div>
</div>
<br><br><br>
<div class="row" style="opacity: 0.3;">
    <div class="col-md-4">
        <canvas id="areaD" height="180"></canvas>
    </div>
    <div class="col-md-4">
        <canvas id="nivelD" height="180"></canvas>
    </div>
    <div class="col-md-4">
        <canvas id="contratoD" height="180"></canvas>
    </div>
</div>
<br><br><br>
<div class="row" style="opacity: 0.3;">
    <div class="col-md-4">
        <canvas id="centroD" height="180"></canvas>
    </div>
    <div class="col-md-4">
        <canvas id="localD" height="180"></canvas>
    </div>
    <div class="col-md-4">
        <canvas id="edadD" height="180"></canvas>
    </div>
</div>
<br><br>
<!-- end row -->
@endsection
@else
@section('content')
<div class="row">
    <div class="col-md-4" id="divarea">
        <div class="chart-container" style="position: relative; height:30vh; width:30vw;">
            <canvas id="area"></canvas>
        </div>
    </div>
    <div class="col-md-4" id="divnivel">
        <div class="chart-container" style="position: relative; height:30vh; width:30vw;">
            <canvas id="nivel" height="180"></canvas>
        </div>
    </div>
    <div class="col-md-4" id="divcontrato">
        <div class="chart-container" style="position: relative; height:30vh; width:30vw;">
            <canvas id="contrato" height="180"></canvas>
        </div>
    </div>
    <br><br><br>
    <div class="col-md-4" id="divcentro">
        <div class="chart-container" style="position: relative; height:30vh; width:30vw;">
            <canvas id="centro" height="180"></canvas>
        </div>
    </div>
    <div class="col-md-4" id="divlocal">
        <div class="chart-container" style="position: relative; height:30vh; width:30vw;">
            <canvas id="local" height="180"></canvas>
        </div>
    </div>
    <div class="col-md-4" id="divedades">
        <div class="chart-container" style="position: relative; height:30vh; width:30vw;">
            <canvas id="edades" height="180"></canvas>
        </div>
    </div>
</div>
@endsection
@endif
@section('script')
<!-- optional plugins -->
<script src="{{ URL::asset('admin/assets/libs/moment/moment.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/chart/Chart.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/chart/Chart.js') }}"></script>
<!--<script src="{{ URL::asset('admin/assets/libs/chart/chartjs-plugin-datalabels.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/chart/chartjs-plugin-datalabels.min.js') }}"></script>-->
<script src="{{ URL::asset('admin/assets/libs/chart/chartjs-plugin-piechart-outlabels.js') }}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
<script src="{{asset('landing/js/dashboardD.js')}}"></script>
<script src="{{asset('landing/js/dashboard.js')}}"></script>
@endsection
