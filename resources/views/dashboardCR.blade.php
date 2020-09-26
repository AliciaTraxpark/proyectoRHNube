@extends('layouts.vertical')


@section('css')
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<link href="{{ URL::asset('admin/assets/libs/apexcharts/apexcharts.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::asset('admin/assets/css/zoom.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="circle1"></div>
            <div class="circle2"></div>
            <div class="circle3"></div>
        </div>
    </div>
</div>
<div class="row justify-content-center p-5">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header"
                style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1">
                <h5 class="card-title font-weight-bold mb-2 mt-2" style="color: #163552">Actividad Total
                </h5>
            </div>
            <div class="card-body border">
                <div class="row justify-content-center">
                    <div class="col-xl-4">
                        <div class="col-md-12">
                            <div class="card bg-c-green order-card p-2"
                                style="background: linear-gradient(45deg, #2ed8b6, #59e0c5);">
                                <div class="card-block">
                                    <h6 class="m-b-20" style="color: #ffffff">Fecha Inicio</h6>
                                    <h2 class="text-right"><i class="fa fa-calendar f-left"
                                            style="color: #ffffff"></i><span></span></h2>
                                    <p class="m-b-0" style="color: #ffffff" id="fechaO"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="wrapper" style="display: flex;flex-flow: column;align-items: center">
                            <div id="gauge-value" style="font-size: 24px;font-weight: bold;padding-bottom: 5px"></div>
                            <canvas id="foo"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-12" id="divArea" style="display: none">
        <div class="card">
            <div class="card-header"
                style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1">
                <h5 class="card-title font-weight-bold mb-2 mt-2" style="color: #163552">Detalle diario por áreas
                </h5>
            </div>
            <div class="card-body border">
                <div class="row justify-content-center">
                    <div class="col-xl-12">
                        <div id="chart">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header"
                style="border-top-right-radius: 5px; border-top-left-radius: 5px;background: #edf0f1">
                <h5 class="card-title font-weight-bold mb-2 mt-2" style="color: #163552">Detalle diario por usuario
                </h5>
            </div>
            <div class="card-body border">
                <div class="row justify-content-center">
                    <div class="col-xl-4">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Fecha:</label>
                            <div class="input-group col-md-8 text-center" style="padding-left: 0px;padding-right: 0px;"
                                id="fechaSelec">
                                <input type="text" id="fechaInput" class="form-control" data-input>
                                <div class="input-group-prepend">
                                    <div class="input-group-text form-control flatpickr">
                                        <a class="input-button" data-toggle>
                                            <i class="uil uil-calender"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <table id="dashboardEmpleado" class="table nowrap" style="font-size: 13px!important;width:
                                        100%;">
                        <thead style="background: #fafafa;" id="dias" style="width:100%!important">
                            <tr>
                                <th>MIENBRO</th>
                                <th>TIEMPO</th>
                                <th>ACTIVIDAD</th>
                            </tr>
                        </thead>
                        <tbody id="empleados">
                        </tbody>
                    </table>
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
@section('script')
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
<script src="{{asset('landing/js/app-menu.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<!-- optional plugins -->
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/gauge/gauge.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/apexcharts/apexcharts.js') }}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/moment.js')}}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/es.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
<script src="{{asset('landing/js/dashboardCR.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
@endsection