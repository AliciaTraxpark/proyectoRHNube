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
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<style>
    .avatarsul {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }

    .liImg {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid white;
        display: inline-block;
        position: relative;
        box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.2);
        -webkit-transition: .2s ease;
        transition: .2s ease;
    }

    .liImg:nth-child(n+2) {
        margin-left: -50px;
    }

    .avatarsul .liImg:nth-child(n+2) {
        margin-left: -10px;
    }
</style>
<div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="circle1"></div>
            <div class="circle2"></div>
            <div class="circle3"></div>
        </div>
    </div>
</div>
<div class="row justify-content-center pt-5 pr-5 pl-5 pb-5">
    <div class="col-xl-12">
        <div class="row">
            <div class="col-md-3">
                <strong style="color: #163552">Modalidad de Control</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center">
                    <div class="card rounded" style="background-color: #a7d129;">
                        <div class="card-header"
                            style="background-color: #a7d129;border-bottom: 3px solid #ffffff;border-top-right-radius: 5px; border-top-left-radius: 5px;height: 40px;">
                            <h6 class="card-title font-weight-bold mb-2" style="color: #ffffff">Al mes - Hasta 200
                                emp.</h6>
                        </div>
                        <div class="card-body text-center mt-0" style="height: 50px;">
                            <strong style="color: #ffffff">PYME</strong>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <strong style="color: #a7d129;font-size: 18px">$3</strong>
                </div>
                <div class="row justify-content-center">
                    <p style="color: #a7d129;font-size: 14px"><strong style="font-size: 16px">$2,50</strong> Un pago
                        semestral</p>
                </div>
                <div class="row justify-content-center">
                    <p style="color: #a7d129;font-size: 14px"><strong style="font-size: 16px">$2</strong> Un pago anual
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center">
                    <div class="card rounded" style="background-color: #5fdde5;">
                        <div class="card-header"
                            style="background-color: #5fdde5;border-bottom: 3px solid #ffffff;border-top-right-radius: 5px; border-top-left-radius: 5px;height: 40px;">
                            <h6 class="card-title font-weight-bold mb-2" style="color: #ffffff">Al mes - de 200 a 5000
                                emp.</h6>
                        </div>
                        <div class="card-body text-center mt-0" style="height: 50px;">
                            <strong style="color: #ffffff">PROFESIONAL</strong>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <strong style="color: #5fdde5;font-size: 18px">$2,50</strong>
                </div>
                <div class="row justify-content-center">
                    <p style="color: #5fdde5;font-size: 14px"><strong style="font-size: 16px">$2</strong> Un pago
                        semestral</p>
                </div>
                <div class="row justify-content-center">
                    <p style="color: #5fdde5;font-size: 14px"><strong style="font-size: 16px">$1,80</strong> Un pago
                        anual</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center">
                    <div class="card rounded" style="background-color: #2c7873;">
                        <div class="card-header"
                            style="background-color: #2c7873;border-bottom: 3px solid #ffffff;border-top-right-radius: 5px; border-top-left-radius: 5px;height: 40px;">
                            <h6 class="card-title font-weight-bold mb-2" style="color: #ffffff">Al mes - Mayor a 5000
                                emp.</h6>
                        </div>
                        <div class="card-body text-center mt-0" style="height: 50px;">
                            <strong style="color: #ffffff">ENTERPRISE</strong>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <strong style="color: #2c7873;font-size: 18px">$2</strong>
                </div>
                <div class="row justify-content-center">
                    <p style="color: #2c7873;font-size: 14px"><strong style="font-size: 16px">$1,80</strong> Un pago
                        semestral</p>
                </div>
                <div class="row justify-content-center">
                    <p style="color: #2c7873;font-size: 14px"><strong style="font-size: 16px">$1,50</strong> Un pago
                        anual</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <strong style="color: #797a7e">Administradores de personal</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3 mb-3" style="background-color: #e1ffc2;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Ilimitado</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3 mb-3" style="background-color: #def4f0;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Ilimitado</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center mr-3 ml-3 mb-3" style="background-color: #dae1e7;height: 30px;">
                    <p class="mt-1" style="color:#797a7e">Ilimitado</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 pt-2">
                <strong style="color: #797a7e">Multiempresa</strong>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center pr-4 pl-4">
                    <p class="text-center" style="color: #797a7e;font-size: 13px">5$ Por cada empresa nueva administrada
                        por la misma cuenta
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center pr-4 pl-4 pt-2">
                    <p class="text-center" style="color: #797a7e;font-size: 13px">Ilimitado
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row justify-content-center pr-4 pl-4 pt-2">
                    <p class="text-center" style="color: #797a7e;font-size: 13px">Ilimitado
                    </p>
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
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js')}}"></script>
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