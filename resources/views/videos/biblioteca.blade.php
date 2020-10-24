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
    .ulC .liC {
        margin-bottom: 1.4rem
    }

    .pricing-divider {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        background: rgb(34, 193, 195);
        background: linear-gradient(0deg, rgba(34, 193, 195, 1) 0%, rgba(68, 177, 204, 1) 100%);
        padding: 1em 0 4em;
        position: relative;
    }

    .pricing-dividerD {
        border-radius: 20px;
        background: rgb(43, 89, 187);
        background: linear-gradient(0deg, rgba(43, 89, 187, 1) 0%, rgba(60, 111, 156, 1) 100%);
        padding: 1em 0 4em;
        position: relative
    }

    .pricing-dividerT {
        border-radius: 20px;
        background: rgb(51, 75, 125);
        background: linear-gradient(0deg, rgba(51, 75, 125, 1) 0%, rgba(64, 112, 136, 1) 100%);
        padding: 1em 0 4em;
        position: relative
    }

    .pricing-divider-img {
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 80px
    }

    .deco-layer {
        -webkit-transition: -webkit-transform 0.5s;
        transition: transform 0.5s
    }

    .btn-custom {
        background: rgb(34, 193, 195);
        background: linear-gradient(0deg, rgba(34, 193, 195, 1) 0%, rgba(68, 177, 204, 1) 100%);
        color: #fff;
        border-radius: 20px
    }

    .btn-custom:hover {
        background: #fff;
        color: #44b1cc;
        border-color: #44b1cc;
        border-radius: 20px;
        -webkit-transition: color 0.5s ease-in-out;
        transition: color 0.5s ease-in-out;
    }

    .btn-custom::after {
        -webkit-transition: height 0.5s ease-in-out;
    }

    .btn-customD {
        background: rgb(43, 89, 187);
        background: linear-gradient(0deg, rgba(43, 89, 187, 1) 0%, rgba(60, 111, 156, 1) 100%);
        color: #fff;
        border-radius: 20px
    }

    .btn-customD:hover {
        color: #3c6f9c;
        background: #fff;
        border-radius: 20px;
        border-color: #3c6f9c;
    }

    .btn-customT {
        background: rgb(51, 75, 125);
        background: linear-gradient(0deg, rgba(51, 75, 125, 1) 0%, rgba(64, 112, 136, 1) 100%);
        color: #fff;
        border-radius: 20px
    }

    .btn-customT:hover {
        color: #407088;
        background: #fff;
        border-radius: 20px;
        border-color: #407088;
    }

    .img-float {
        width: 50px;
        position: absolute;
        top: -3.5rem;
        right: 1rem
    }

    .princing-item {
        transition: all 150ms ease-out
    }

    .princing-item:hover {
        transform: scale(1.05)
    }

    .princing-item:hover .deco-layer--1 {
        -webkit-transform: translate3d(15px, 0, 0);
        transform: translate3d(15px, 0, 0)
    }

    .princing-item:hover .deco-layer--2 {
        -webkit-transform: translate3d(-15px, 0, 0);
        transform: translate3d(-15px, 0, 0)
    }

    .botonF1 {
        width: 80px;
        height: 80px;
        border-radius: 100%;
        background: #f4eeff;
        right: 0;
        bottom: 0;
        position: absolute;
        top: -40px;
        left: 80px;
        border: none;
        outline: none;
        color: #30475e;
        font-size: 36px;
        box-shadow: 0 8px 6px rgba(0, 0, 0, 0.16), 0 8px 6px rgba(0, 0, 0, 0.23);
        transition: .3s;
    }

    .svgTriangulo {
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 40px
    }

    @media (max-width: 767.98px) {
        .containerResp {
            padding-left: 0% !important;
        }

        .rowPrincipalResp {
            padding: 0% !important;
            padding-top: 2% !important;
        }

        .preciosResponsive,
        .content-page {
            width: 100% !important;
            display: flex !important;
            overflow: auto !important;
            flex-wrap: initial !important;
        }

        .col-4 {
            flex: 100% !important;
            max-width: 100% !important;
            padding-top: 8% !important;
        }

        .pt-5 {
            padding-top: 2rem !important;
        }

        .pricing-divider,
        .pricing-dividerD,
        .pricing-dividerT,
        .bodyPrecio {
            width: 250px !important;
        }

        .row {
            flex-wrap: nowrap !important;
        }

        .tituloResponsive {
            max-width: 30% !important;
        }
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
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
@endsection