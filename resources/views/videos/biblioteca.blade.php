@extends('layouts.vertical')


@section('css')
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet"
    type="text/css" />
<link href="{{URL::asset('admin/assets/libs/fancybox-master/jquery.fancybox.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/fancybox-master/jquery.fancybox.min.css')}}" rel="stylesheet"
    type="text/css" />
@endsection
@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0">Biblioteca</h4>
    </div>
</div>
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

{{-- CONTENIDO DE GALERIAS --}}
<div class="row justify-content-center p-5">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <h5>
                    <img src="{{asset('landing/images/play-button.svg')}}" height="25" class="mr-2">
                    1. ¿Cómo registrar tu organización en 3 sencillos pasos?
                </h5>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <a data-fancybox data-ratio="2" href="https://vimeo.com/191947042">
                            <img class="card-img-top img-fluid" src="https://i.vimeocdn.com/video/604514162_640.jpg" />
                        </a>

                        <div class="card-body">
                            <p class="card-text">Custom aspect ratio</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- FINALIZACION --}}

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
<!-- optional plugins -->
<script src="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js')}}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
<script src="{{URL::asset('admin/assets/libs/fancybox-master/jquery.fancybox.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/fancybox-master/jquery.fancybox.min.js') }}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
@endsection