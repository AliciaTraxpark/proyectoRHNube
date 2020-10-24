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
        <div class="row pt-3 pr-5 pl-5 pb-3">
            <div class="col-md-12">
                <div class="card border"
                    style="border-radius: 15px;border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4" style="border-right: 2px dashed #5369F8;">
                                <a data-fancybox data-ratio="2"
                                    href="https://player.vimeo.com/video/471447985?title=0&byline=0&portrait=0">
                                    <img class="card-img-top img-fluid"
                                        src="https://i.vimeocdn.com/video/980849960.webp?mw=1200&mh=675" />
                                </a>
                            </div>

                            <div class="col-md-8 pl-4">
                                <p class="card-text pt-3" style="font-weight: bold">Registra tus datos</p>
                                <a class="badge badge-soft-primary" onclick="javascript:registroDatos()">
                                    <img src="{{asset('landing/images/play (2).svg')}}" height="18" class="mr-2">
                                    01:31
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pr-5 pl-5 pb-3">
            <div class="col-md-12">
                <div class="card border"
                    style="border-radius: 15px;border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4" style="border-right: 2px dashed #5369F8;">
                                <a data-fancybox data-ratio="2"
                                    href="https://player.vimeo.com/video/471448607?title=0&byline=0&portrait=0">
                                    <img class="card-img-top img-fluid"
                                        src="https://i.vimeocdn.com/video/980849111.webp?mw=960&mh=540" />
                                </a>
                            </div>

                            <div class="col-md-8 pl-4">
                                <p class="card-text pt-3" style="font-weight: bold">Registra tu organización</p>
                                <a class="badge badge-soft-primary" onclick="javascript:registroOrgani()">
                                    <img src="{{asset('landing/images/play (2).svg')}}" height="18" class="mr-2">
                                    01:13
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pr-5 pl-5 pb-3">
            <div class="col-md-12">
                <div class="card border"
                    style="border-radius: 15px;border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4" style="border-right: 2px dashed #5369F8;">
                                <a data-fancybox data-ratio="2"
                                    href="https://player.vimeo.com/video/471449241?title=0&byline=0&portrait=0">
                                    <img class="card-img-top img-fluid"
                                        src="https://i.vimeocdn.com/video/980846947.webp?mw=960&mh=540" />
                                </a>
                            </div>

                            <div class="col-md-8 pl-4">
                                <p class="card-text pt-3" style="font-weight: bold">Valida tu cuenta</p>
                                <a class="badge badge-soft-primary" onclick="javascript:registroValidaC()">
                                    <img src="{{asset('landing/images/play (2).svg')}}" height="18" class="mr-2">
                                    01:13
                                </a>
                            </div>
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
<script src="{{asset('landing/js/biblioteca.js')}}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
@endsection