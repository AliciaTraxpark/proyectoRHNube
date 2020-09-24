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
<script src="{{asset('landing/js/app-menu.js')}}"></script>
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
        width: 140px;
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

    .classic-tabs>ul.nav>li.nav-item>a {
        color: #dce4eb !important;
    }

    .classic-tabs>ul.nav>li.nav-item>a.active {
        color: #85a2b6 !important;
        border-bottom: 2px solid #f1f2f3 !important;
        font-weight: bold !important;
        /* add background-color to active links */
    }
</style>
<div class="row justify-content-center">
    <div class="classic-tabs">

        <ul class="nav nav-pills navtab-bg justify-content-center" role="tablist"
            style="margin-left: 2%;margin-right: 2%">
            <li class="nav-item">
                <a href="#profile1" data-toggle="tab" aria-expanded="true" class="nav-link active" role="tab">
                    <span class="d-block d-sm-none"><i class="uil-user"></i></span>
                    <span class="d-none d-sm-block"><img src="{{asset('admin/images/growth (2).svg')}}" height="20"
                            class="mr-1">General</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#messages1" data-toggle="tab" aria-expanded="false" class="nav-link" role="tab">
                    <span class="d-block d-sm-none"><i class="uil-envelope"></i></span>
                    <span class="d-none d-sm-block"><img src="{{asset('admin/images/growth (2).svg')}}" height="20"
                            class="mr-1">Control Remoto</span>
                </a>
            </li>
        </ul>
        <div class="tab-content text-muted">
            <div class="tab-pane show active" id="profile1" role="tabpanel">
                <div class="row justify-content-center">
                    <div class="row">
                        <div class="col-md-6" id="divarea" style="min-height: 460px">
                            <div class="card chart-card">
                                <div class="card-body pb-0 text-center">
                                    <h5 class="card-title font-weight-bold mb-2 mt-2" style="color: #163552"><img
                                            src="{{asset('landing/images/bookmark.svg')}}" height="20"
                                            class="mr-2">Área(s)
                                    </h5>
                                    <div class="d-flex justify-content-center">
                                        <p class="align-self-end mt-2" id="fechaArea"></p>
                                    </div>
                                </div>
                                <div class="classic-tabs">
                                    <!-- Nav tabs -->
                                    <ul class="nav tabs-white nav-fill" role="tablist">
                                        <li class="nav-item ml-0">
                                            <a class="nav-link active" data-toggle="tab" href="#panel1001A"
                                                role="tab">Grafico</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " data-toggle="tab" href="#panel1002A"
                                                role="tab">Información
                                                detallada</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content rounded-bottom">
                                        <div class="tab-pane fade in show active" id="panel1001A" role="tabpanel">
                                            <div class="float-right" style="width:100%">
                                                <div class="float-right" style="width:70%">
                                                    <canvas id="area" height="300" width="300"></canvas>
                                                </div>
                                                <div id="js-legendArea" class="chart-legend"></div>
                                            </div>
                                        </div>
                                        <div class="tab-pane ml-2" id="panel1002A" role="tabpanel"
                                            style="max-height: 460px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="divnivel" style="min-height: 460px">
                            <div class="card chart-card">
                                <div class="card-body pb-0 text-center">
                                    <h5 class="card-title font-weight-bold mb-2 mt-2" style="color: #163552"><img
                                            src="{{asset('landing/images/bookmark.svg')}}" height="20"
                                            class="mr-2">Nivel(es)
                                        del
                                        colaborador</h5>
                                    <div class="d-flex justify-content-center">
                                        <p class="align-self-end mt-2" id="fechaNivel"></p>
                                    </div>
                                </div>
                                <div class="classic-tabs">
                                    <!-- Nav tabs -->
                                    <ul class="nav tabs-white nav-fill" role="tablist">
                                        <li class="nav-item ml-0">
                                            <a class="nav-link active" data-toggle="tab" href="#panel1001N"
                                                role="tab">Grafico</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " data-toggle="tab" href="#panel1002N"
                                                role="tab">Información
                                                detallada</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content rounded-bottom">
                                        <div class="tab-pane fade in show active" id="panel1001N" role="tabpanel">
                                            <div class="float-right" style="width:100%">
                                                <div class="float-right" style="width:70%">
                                                    <canvas id="nivel" height="300" width="300"></canvas>
                                                </div>
                                                <div id="js-legendNivel" class="chart-legend"></div>
                                            </div>
                                        </div>
                                        <div class="tab-pane ml-2" id="panel1002N" role="tabpanel"
                                            style="max-height: 460px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" id="divcontrato" style="min-height: 460px">
                            <div class="card chart-card">
                                <div class="card-body pb-0 text-center">
                                    <h5 class="card-title font-weight-bold mb-2 mt-2" style="color: #163552"><img
                                            src="{{asset('landing/images/bookmark.svg')}}" height="20" class="mr-2">Tipo
                                        de
                                        Contrato
                                    </h5>
                                    <div class="d-flex justify-content-center">
                                        <p class="align-self-end mt-2" id="fechaContrato"></p>
                                    </div>
                                </div>
                                <div class="classic-tabs">
                                    <!-- Nav tabs -->
                                    <ul class="nav tabs-white nav-fill" role="tablist">
                                        <li class="nav-item ml-0">
                                            <a class="nav-link active" data-toggle="tab" href="#panel1001C"
                                                role="tab">Grafico</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " data-toggle="tab" href="#panel1002C"
                                                role="tab">Información
                                                detallada</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content rounded-bottom">
                                        <div class="tab-pane fade in show active" id="panel1001C" role="tabpanel">
                                            <div class="float-right" style="width:100%">
                                                <div class="float-right" style="width:70%">
                                                    <canvas id="contrato" height="300" width="300"></canvas>
                                                </div>
                                                <div id="js-legendContrato" class="chart-legend"></div>
                                            </div>
                                        </div>
                                        <div class="tab-pane ml-2" id="panel1002C" role="tabpanel"
                                            style="max-height: 460px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="divcentro" style="min-height: 460px">
                            <div class="card chart-card">
                                <div class="card-body pb-0 text-center">
                                    <h5 class="card-title font-weight-bold mb-2 mt-2" style="color: #163552"><img
                                            src="{{asset('landing/images/bookmark.svg')}}" height="20"
                                            class="mr-2">Centro
                                        de
                                        Costos
                                    </h5>
                                    <div class="d-flex justify-content-center">
                                        <p class="align-self-end mt-2" id="fechaCentro"></p>
                                    </div>
                                </div>
                                <div class="classic-tabs">
                                    <!-- Nav tabs -->
                                    <ul class="nav tabs-white nav-fill" role="tablist">
                                        <li class="nav-item ml-0">
                                            <a class="nav-link active" data-toggle="tab" href="#panel1001CC"
                                                role="tab">Grafico</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " data-toggle="tab" href="#panel1002CC"
                                                role="tab">Información
                                                detallada</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content rounded-bottom">
                                        <div class="tab-pane fade in show active" id="panel1001CC" role="tabpanel">
                                            <div class="float-right" style="width:100%">
                                                <div class="float-right" style="width:70%">
                                                    <canvas id="centro" height="300" width="300"></canvas>
                                                </div>
                                                <div id="js-legendCentro" class="chart-legend"></div>
                                            </div>
                                        </div>
                                        <div class="tab-pane ml-2" id="panel1002CC" role="tabpanel"
                                            style="max-height: 460px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" id="divlocal" style="min-height: 460px">
                            <div class="card chart-card">
                                <div class="card-body pb-0 text-center">
                                    <h5 class="card-title font-weight-bold mb-2 mt-2" style="color: #163552"><img
                                            src="{{asset('landing/images/bookmark.svg')}}" height="20"
                                            class="mr-2">Local(es)
                                    </h5>
                                    <div class="d-flex justify-content-center">
                                        <p class="align-self-end mt-2" id="fechaLocal"></p>
                                    </div>
                                </div>
                                <div class="classic-tabs">
                                    <!-- Nav tabs -->
                                    <ul class="nav tabs-white nav-fill" role="tablist">
                                        <li class="nav-item ml-0">
                                            <a class="nav-link active" data-toggle="tab" href="#panel1001L"
                                                role="tab">Grafico</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " data-toggle="tab" href="#panel1002L"
                                                role="tab">Información
                                                detallada</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content rounded-bottom">
                                        <div class="tab-pane fade in show active" id="panel1001L" role="tabpanel">
                                            <div class="float-right" style="width:100%">
                                                <div class="float-right" style="width:70%">
                                                    <canvas id="local" height="300" width="300"></canvas>
                                                </div>
                                                <div id="js-legendLocal" class="chart-legend"></div>
                                            </div>
                                        </div>
                                        <div class="tab-pane ml-2" id="panel1002L" role="tabpanel"
                                            style="max-height: 460px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="divdepartamento" style="min-height: 460px">
                            <div class="card chart-card">
                                <div class="card-body pb-0 text-center">
                                    <h5 class="card-title font-weight-bold mb-2 mt-2" style="color: #163552"><img
                                            src="{{asset('landing/images/bookmark.svg')}}" height="20"
                                            class="mr-2">Ciudad
                                        domiciliaria
                                    </h5>
                                    <div class="d-flex justify-content-center">
                                        <p class="align-self-end mt-2" id="fechaDepartamento"></p>
                                    </div>
                                </div>
                                <div class="classic-tabs">
                                    <!-- Nav tabs -->
                                    <ul class="nav tabs-white nav-fill" role="tablist">
                                        <li class="nav-item ml-0">
                                            <a class="nav-link active" data-toggle="tab" href="#panel1001D"
                                                role="tab">Grafico</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " data-toggle="tab" href="#panel1002D"
                                                role="tab">Información
                                                detallada</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content rounded-bottom">
                                        <div class="tab-pane fade in show active" id="panel1001D" role="tabpanel">
                                            <div class="float-right" style="width:100%">
                                                <div class="float-right" style="width:70%">
                                                    <canvas id="departamento" height="300" width="300"></canvas>
                                                </div>
                                                <div id="js-legendDep" class="chart-legend"></div>
                                            </div>
                                        </div>
                                        <div class="tab-pane ml-2" id="panel1002D" role="tabpanel"
                                            style="max-height: 460px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="divedades" style="min-height:460px">
                        <div class="card chart-card">
                            <div class="card-body pb-0 text-center">
                                <h5 class="card-title font-weight-bold mb-2 mt-2" style="color: #163552"><img
                                        src="{{asset('landing/images/bookmark.svg')}}" height="20" class="mr-2">Rangos
                                    de
                                    Edades
                                </h5>
                                <div class="d-flex justify-content-center">
                                    <p class="align-self-end mt-2" id="fechaEdades"></p>
                                </div>
                            </div>
                            <div class="classic-tabs">
                                <!-- Nav tabs -->
                                <ul class="nav tabs-white nav-fill" role="tablist">
                                    <li class="nav-item ml-0">
                                        <a class="nav-link active" data-toggle="tab" href="#panel1001E"
                                            role="tab">Grafico</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#panel1002E" role="tab">Información
                                            detallada</a>
                                    </li>
                                </ul>
                                <div class="tab-content rounded-bottom">
                                    <div class="tab-pane fade in show active" id="panel1001E" role="tabpanel">
                                        <div class="float-right" style="width:100%">
                                            <div class="float-right" style="width:70%">
                                                <canvas id="edades" height="300" width="300"></canvas>
                                            </div>
                                            <div id="js-legendEdades" class="chart-legend"></div>
                                        </div>
                                    </div>
                                    <div class="tab-pane ml-2" id="panel1002E" role="tabpanel"
                                        style="max-height: 460px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="messages1" role="tabpanel">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title font-weight-bold mb-2 mt-2" style="color: #163552">Detalle General
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="foo"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img src="{{asset('landing/images/notification.svg')}}" height="100">
                <h4 class="text-danger mt-4">Su sesión expiró</h4>
                <p class="w-75 mx-auto text-muted">Por favor inicie sesión nuevamente.</p>
                <div class="mt-4">
                    <a href="{{('/')}}" class="btn btn-outline-primary btn-rounded width-md"><i
                            class="uil uil-arrow-right mr-1"></i> Iniciar sesión</a>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@if (Auth::user())
<script>
    $(function() {
    setInterval(function checkSession() {
      $.get('/check-session', function(data) {
        // if session was expired
        if (data.guest==false) {
            $('.modal').modal('hide');
           $('#modal-error').modal('show');

        }
      });
    },7202000);
  });
</script>
@endif
@endsection
@endif
@section('script')
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
<script src="{{asset('landing/js/app-menu.js')}}"></script>
<!-- optional plugins -->
<script src="{{ URL::asset('admin/assets/libs/moment/moment.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/chart/Chart.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/chart/Chart.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/chart/chartjs-plugin-datalabels.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/chart/chartjs-plugin-datalabels.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/gauge/gauge.js') }}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
<script src="{{asset('landing/js/dashboard.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
@endsection