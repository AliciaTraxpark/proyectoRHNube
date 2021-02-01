@extends('layouts.vertical')


@section('css')
<link href="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet"
    type="text/css" />
<link href="{{URL::asset('admin/assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('admin/assets/libs/leaflet/leaflet.css')}}" rel="stylesheet"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin="" />
<link href="{{URL::asset('admin/assets/libs/leaflet/leaflet-routing-machine.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/leaflet/leaflet-search.src.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/leaflet/easy-button.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/leaflet/Control.FullScreen.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/fancybox-master/jquery.fancybox.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/fancybox-master/jquery.fancybox.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{URL::asset('admin/assets/css/notify.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/css/prettify.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/css/zoom.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet"
    type="text/css" />
{{-- plugin de ALERTIFY --}}
<link href="{{URL::asset('admin/assets/libs/alertify/alertify.css')}}" rel="stylesheet" type="text/css" />
<!-- Semantic UI theme -->
<link href="{{URL::asset('admin/assets/libs/alertify/default.css')}}" rel="stylesheet" type="text/css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0" style="font-weight: bold">Detalle diario</h4>
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
<style>
    .carousel-control-prev-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='403555' viewBox='0 0 8 8'%3E%3Cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3E%3C/svg%3E") !important;
    }

    .carousel-control-next-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='403555' viewBox='0 0 8 8'%3E%3Cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3E%3C/svg%3E");
    }

    .close:hover {
        color: #ffff;
    }

    .close {
        color: #ffffff;
    }

    .mapid {
        width: 100%;
        height: 125px;
    }

    .leaflet-control-attribution {
        pointer-events: none !important;
    }

    .leaflet-right .leaflet-routing-container.leaflet-routing-container-hide {
        display: none;
    }

    .leaflet-pane {
        z-index: 0;
    }

    .leaflet-top,
    .leaflet-bottom {
        z-index: 0;
    }

    .leaflet-control {
        z-index: 0;
    }

    #mapRecorrido {
        padding: 0;
        width: auto;
        height: 480px;
    }

    @media (max-width: 767.98px) {
        .colR {
            padding-left: 0% !important;
            padding-right: 2% !important;
        }

        .btnR {
            text-align: center !important;
        }

        .imgR {
            margin-left: 15% !important;
        }

        .alertR {
            width: 60%;
        }

        .containerR {
            overflow: auto !important;
            display: flex !important;
        }

        .rowResp {
            overflow: auto !important;
            white-space: normal !important;
            max-width: 100% !important;
            display: flex !important;
        }

        .columResponsiva {
            padding-right: 2% !important;
            padding-left: 2% !important;
            max-width: 50% !important;
            flex: 100% !important;
        }

        .columnTextR {
            padding-right: 0% !important;
            padding-left: 0% !important;
            max-width: 100% !important;
        }

        .h5Responsive {
            font-size: 14px !important;
        }

        .rowResponsivo {
            justify-content: center !important;
        }

        .mbResponsivo {
            padding-top: 15% !important;
        }

    }
</style>
{{-- MODAL DE UBICACION --}}
<div id="modalRuta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalRuta" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" style="color:#ffffff;font-size:15px">
                    Recorrido de las <label id="horaIRecorrido"></label> - <label id="horaFRecorrido"></label>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="col-md-12 col-sm-12 p-0" id="bodyMap">
                    {{-- <div id="mapRecorrido" class="mapRecorrido"></div> --}}
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
{{-- FINALIZACION --}}
{{-- MODAL DE ZOOM DE IMAGENES --}}
<div id="modalZoom" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" style="color:#ffffff;font-size:15px">Colección
                    de Imagenes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="esperaImg" class="text-center" style="display: none">
                    <img src="{{asset('landing/images/punt.gif')}}" height="80">
                </div>
                <div class="row">
                    <div id="zoom" class="col-xl-12 text-center album">
                        <hr class="my-5" />
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
{{-- FINALIZACION --}}
{{-- CONTENIDO DE VISTA --}}
<div class="row">
    <a id="googleOculto"></a>
    <div class="col-md-12">
        <div class="row pt-4">
            <div class="col-xl-5">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label pl-5 colR">Fecha:</label>
                    <div class="input-group col-md-7 text-center colR" style="padding-left: 0px;padding-right: 0px;"
                        id="fechaSelec">
                        <input type="text" id="fecha" class="form-control" data-input>
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
            <div class="col-xl-1"></div>
            <div class="col-xl-5">
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label colR">Empleado:</label>
                    <div class="col-lg-10 colR">
                        <select id="empleado" data-plugin="customselect" class="form-control">
                            <option value="" disabled selected>Seleccionar</option>
                        </select>
                    </div>

                </div>
            </div>
            <div class="col-xl-1 text-left btnR" style="padding-left: 0%">
                <button type="button" class="btn btn-sm mt-1" style="background-color: #163552;"
                    onclick="javascript:buscarUbicaciones()"> <img src="{{asset('landing/images/loupe (1).svg')}}"
                        height="18"></button>
            </div>
        </div>
        <div id="espera" class="text-center" style="display: none">
            <img src="{{asset('landing/images/loading.gif')}}" height="100">
        </div>
    </div>
    <div class="col-xl-12" id="card">
        <br>
        <img id="VacioImg" style="margin-left:28%" src="{{URL::asset('admin/images/search-file.svg')}}"
            class="mr-2 imgR" height="220" />
        <br>
        <label for="" style="margin-left:30%;color:#7d7d7d" class="imgR">Realize una
            búsqueda para ver Actividad</label>
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
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/moment.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/es.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/fancybox-master/jquery.fancybox.js') }}"></script>
<script src="{{URL::asset('admin/assets/libs/fancybox-master/jquery.fancybox.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/leaflet.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/leaflet-src.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/ActiveLayers.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/SelectLayers.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/leaflet-routing-machine.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/easy-button.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/Control.FullScreen.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{URL::asset('admin/assets/libs/CryptoJS/md5.js') }}"></script>
<script src="{{URL::asset('admin/assets/libs/CryptoJS/enc-base64.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/alertify/alertify.js') }}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
<script src="{{asset('landing/js/ubicacion.js')}}"></script>
@endsection
@section('script-bottom')
<!-- init js -->
@endsection