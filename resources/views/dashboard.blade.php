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
        <canvas id="areaD" height="250" width="250"></canvas>
    </div>
    <div class="col-md-4">
        <canvas id="nivelD" height="250" width="250"></canvas>
    </div>
    <div class="col-md-4">
        <canvas id="contratoD" height="250" width="250"></canvas>
    </div>
</div>
<br><br><br>
<div class="row" style="opacity: 0.3;">
    <div class="col-md-4">
        <canvas id="centroD" height="250" width="250"></canvas>
    </div>
    <div class="col-md-4">
        <canvas id="localD" height="250" width="250"></canvas>
    </div>
    <div class="col-md-4">
        <canvas id="edadD" height="250" width="250"></canvas>
    </div>
</div>
<!-- end row -->
@endsection
@section('script')
<script src="{{ URL::asset('admin/assets/libs/chart/Chart.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/chart/Chart.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/chart/chartjs-plugin-datalabels.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/chart/chartjs-plugin-datalabels.min.js') }}"></script>
<!--<script src="{{ URL::asset('admin/assets/libs/chart/chartjs-plugin-piechart-outlabels.js') }}"></script>-->
<script src="{{asset('landing/js/dashboardD.js')}}"></script>
@endsection
@else
@section('content')
<style>
    .chart-legend li span {
        display: inline-block;
        width: 23px;
        height: 12px;
        margin-right: 3px;
    }

    .chart-legend ul {
        list-style: none;
    }

    .chart-legend {
        height: 250px;
        overflow: auto;
        margin-top: 15px;
        margin-bottom: 15px;
    }

</style>
<div class="row">
    <div class="col-md-4" id="divarea">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="area" height="350" width="350"></canvas>
            </div>
            <div id="js-legendArea" class="chart-legend"></div>
        </div>
    </div>
    <div class="col-md-4" id="divnivel">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="nivel" height="350" width="350"></canvas>
            </div>
            <div id="js-legendNivel" class="chart-legend"></div>
        </div>
    </div>
    <div class="col-md-4" id="divcontrato">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="contrato" height="350" width="350"></canvas>
            </div>
            <div id="js-legendContrato" class="chart-legend"></div>
        </div>
    </div>
    <br><br><br>
    <div class="col-md-4" id="divcentro">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="centro" height="350" width="350"></canvas>
            </div>
            <div id="js-legendCentro" class="chart-legend"></div>
        </div>
    </div>
    <div class="col-md-4" id="divlocal">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="local" height="350" width="350"></canvas>
            </div>
            <div id="js-legendLocal" class="chart-legend"></div>
        </div>
    </div>
    <div class="col-md-4" id="divdepartamento">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="departamento" height="350" width="350"></canvas>
            </div>
            <div id="js-legendDep" class="chart-legend"></div>
        </div>
    </div>
    <br><br><br>
    <div class="col-md-4" id="divedades">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="edades" height="350" width="350"></canvas>
            </div>
            <div id="js-legendEdades" class="chart-legend"></div>
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
<script src="{{ URL::asset('admin/assets/libs/chart/chartjs-plugin-datalabels.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/chart/chartjs-plugin-datalabels.min.js') }}"></script>
<!--<script src="{{ URL::asset('admin/assets/libs/chart/chartjs-plugin-piechart-outlabels.js') }}"></script>-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
<script src="{{asset('landing/js/dashboard.js')}}"></script>
@endsection
