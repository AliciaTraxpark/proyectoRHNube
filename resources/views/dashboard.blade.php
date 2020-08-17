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
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
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
        width: 12px;
        height: 12px;
        margin-right: 3px;
        -moz-border-radius: 50px;
        -webkit-border-radius: 50px;
        border-radius: 50px;
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

    #wrapper>div.content-page>div.content>div {
        padding-left: 0px;
        padding-right: 45px;
    }

    .chart-card {
        background-color: #ffffff;
        box-shadow: 1px 1px 10px rgba(87, 87, 87, 0.5);
    }

    .classic-tabs>ul.nav>li.nav-item>a.active {
        color: #85a2b6 !important;
        border-bottom: 2px solid #f1f2f3 !important;
        /* add background-color to active links */
    }
</style>
<div class="row">
    <div class="col-md-6" id="divarea">
        <div class="card chart-card">
            <div class="card-body pb-0">
                <h5 class="card-title font-weight-bold mb-2 mt-2"><img src="{{asset('landing/images/bookmark.svg')}}"
                        height="25" class="mr-2">Área(s)</h5>
                <div class="d-flex justify-content-between">
                    <p class="align-self-end mt-2" id="fechaArea"></p>
                    <p class="align-self-end" id="cantidadArea"></p>
                </div>
            </div>
            <div class="classic-tabs">
                <!-- Nav tabs -->
                <ul class="nav tabs-white nav-fill" role="tablist">
                    <li class="nav-item ml-0">
                        <a class="nav-link active" data-toggle="tab" href="#panel1001" role="tab">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#panel1002" role="tab">Información
                            detallada</a>
                    </li>
                </ul>
                <div class="tab-content rounded-bottom">
                    <div class="tab-pane fade in show active" id="panel1001" role="tabpanel">
                        <div class="float-right" style="width:100%">
                            <div class="float-right" style="width:70%">
                                <canvas id="area" height="300" width="300"></canvas>
                            </div>
                            <div id="js-legendArea" class="chart-legend"></div>
                        </div>
                    </div>
                    <div class="tab-pane" id="panel1002" role="tabpanel">
                        <p align="justify" class="font-small text-muted mx-1">Lorem ipsum dolor sit amet, consectetur
                            adipisicing elit. Nihil odit magnam minima, soluta doloribus reiciendis molestiae placeat
                            unde eos molestias. Quisquam aperiam, pariatur. Tempora, placeat ratione porro voluptate
                            odit minima.</p>
                        <p align="justify" class="font-small text-muted mx-1">Lorem ipsum dolor sit amet, consectetur
                            adipisicing elit. Nihil odit magnam minima, soluta doloribus reiciendis molestiae placeat
                            unde eos molestias. Quisquam aperiam, pariatur. Tempora, placeat ratione porro voluptate
                            odit minima.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6" id="divnivel">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="nivel" height="300" width="300"></canvas>
            </div>
            <div id="js-legendNivel" class="chart-legend"></div>
        </div>
    </div>
    <br><br><br>
    <div class="col-md-6" id="divcontrato">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="contrato" height="300" width="300"></canvas>
            </div>
            <div id="js-legendContrato" class="chart-legend"></div>
        </div>
    </div>
    <div class="col-md-6" id="divcentro">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="centro" height="300" width="300"></canvas>
            </div>
            <div id="js-legendCentro" class="chart-legend"></div>
        </div>
    </div>
    <br><br><br>
    <div class="col-md-6" id="divlocal">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="local" height="300" width="300"></canvas>
            </div>
            <div id="js-legendLocal" class="chart-legend"></div>
        </div>
    </div>
    <div class="col-md-6" id="divdepartamento">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="departamento" height="300" width="300"></canvas>
            </div>
            <div id="js-legendDep" class="chart-legend"></div>
        </div>
    </div>
    <br><br><br>
    <div class="col-md-6" id="divedades">
        <div class="float-right" style="width:100%">
            <div class="float-right" style="width:45%">
                <canvas id="edades" height="300" width="300"></canvas>
            </div>
            <div id="js-legendEdades" class="chart-legend"></div>
        </div>
    </div>
</div>
@endsection
@endif
@section('script')
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
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
<script src="{{asset('landing/js/notificacionesCalendario.js')}}"></script>
<script src="{{asset('landing/js/dashboard.js')}}"></script>
<script src="{{asset('landing/js/notificacionesHorario.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
@endsection