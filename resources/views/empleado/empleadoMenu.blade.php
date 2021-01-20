@php
use App\proyecto_empleado;
@endphp

@extends('layouts.vertical')

@section('css')
<style>
    .pace {
        -webkit-pointer-events: none;
        pointer-events: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    .pace-inactive {
        display: none;
    }

    .pace .pace-progress {
        background: #545474;
        position: fixed;
        z-index: 2000;
        top: 0;
        /* right: 100%; */
        width: 100%;
        height: 6 px;
    }

    body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-header {
        background-color: #163552;
    }

    body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-header>h5 {
        color: #fff;
        font-size: 15px !important;
    }

    body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-header {
        background-color: #163552;
    }

    body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-header>h5 {
        color: #fff;
        font-size: 15px !important;
    }

    .form-control:disabled {
        background-color: #f1f0f0;
    }



    .inputfile {
        width: 0.1px !important;
        height: 0.1px !important;
        opacity:  !important overflow: hidden !important;
        position: absolute !important;
        z-index: -1 !important;
    }

    .inputfile+label {
        max-width: 80% !important;
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        cursor: pointer !important;
        display: inline;
        overflow: hidden !important;
        padding: 0.30rem 0.8rem !important;
    }

    .inputfile+label svg {
        width: 1em !important;
        height: 1em !important;
        vertical-align: middle !important;
        fill: currentColor !important;

        margin-right: 0.25em !important;
    }

    .iborrainputfile {
        font-size: 13.8px !important;
        font-weight: normal !important;

    }

    /* style 1 */

    .inputfile-1+label {
        color: #59687d !important;
        background-color: #e3eaef !important;
    }

    .inputfile-1:focus+label,
    .inputfile-1.has-focus+label,
    .inputfile-1+label:hover {
        background-color: #e3eaef !important;
    }
</style>


<script type="text/javascript" src="{{asset('admin/assets/pace/pace.min.js')}}"></script>
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('admin/assets/libs/bootstrap-fileinput/fileinput.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Plugin css  CALENDAR-->
<link href="{{ asset('admin/packages/core/main.css') }}" rel="stylesheet" />
<link href="{{ asset('admin/packages/daygrid/main.css') }}" rel="stylesheet" />
<link href="{{ asset('admin/packages/timegrid/main.css') }}" rel="stylesheet" />
{{-- Plugin SELECT2 --}}
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
{{-- plugin DATATABLE --}}
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
{{-- plugin SMARTWIZARD --}}
<link href="{{ asset('admin/assets/libs/smartwizard/smart_wizard.min.css') }}" type="text/css" />
<link href="{{ asset('admin/assets/libs/smartwizard/smart_wizard_theme_arrows.min.css') }}" type="text/css" />
<link href="{{ asset('admin/assets/libs/smartwizard/smart_wizard_theme_circles.min.css') }}" type="text/css" />
<link href="{{ asset('admin/assets/libs/smartwizard/smart_wizard_theme_dots.min.css') }}" type="text/css" />
{{-- plugin MULTISELECT --}}
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css')}}" rel="stylesheet" type="text/css" />
{{-- plugin de ALERTIFY --}}
<link href="{{ URL::asset('admin/assets/libs/alertify/alertify.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ URL::asset('admin/assets/libs/alertify/bootstrap.css') }}" rel="stylesheet" type="text/css" /> --}}
<!-- Semantic UI theme -->
<link href="{{ URL::asset('admin/assets/libs/alertify/default.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/zoom.css') }}" rel="stylesheet" type="text/css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('breadcrumb')
{{-- MODAL DE REGISTRAR CORREO --}}
<div id="modalCorreoElectronico" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCorreoElectronico"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Registrar correo electrónico empleado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:RefreshTablaEmpleado();javascript:limpiarCorreoE()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="idEmpleCorreo">
                    <form class="form-horizontal col-lg-12" action="javascript:guardarCorreoE()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" style="font-weight: 500;">
                                        Correo electrónico
                                    </label>
                                    <div class="input-group input-group-merge">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="background-color: #ffffff;">
                                                <img src="{{asset('landing/images/at.svg')}}" height="13">
                                            </span>
                                        </div>
                                        <input type="email" class="form-control" id="textCorreo" name="email" required
                                            autofocus>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal"
                    onclick="javascript:RefreshTablaEmpleado()">Cerrar</button>
                <button type="submit" style="background-color:#163552; border-color: #163552;"
                    class="btn btn-sm btn-primary">Registrar</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
{{-- FINALIZACION MODAL --}}
{{-- MODAL DE REGISTRAR CELULAR --}}
<div id="modalCelular" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCelular" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Registrar celular empleado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:RefreshTablaEmpleado();$('#textCelular').val('')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="idEmpleCelular">
                    <form class="form-horizontal col-lg-12" action="javascript:guardarCelularE()"
                        id="FormguardarCelularE">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" style="font-weight: 500;">
                                        Celular
                                    </label>
                                    <div class="input-group input-group-merge">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                style="background-color: #ffffff;font-size: 9px">
                                                +51
                                            </span>
                                        </div>
                                        <input type="number"
                                            oninput="javascript: if (this.value.length >= this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                            class="form-control" id="textCelular" name="celular" required autofocus
                                            maxlength="9" minlength="9">
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal"
                    onclick="javascript:RefreshTablaEmpleado();$('#textCelular').val('')">Cerrar</button>
                <button type="submit" style="background-color:#163552; border-color: #163552;"
                    class="btn btn-sm btn-primary">Registrar</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
{{-- FINALIZACION MODAL --}}
{{-- MODAL DE AGREGAR ACTIVIDADES EN FORMULARIO EDITAR--}}
<div id="RegActividadTareaGE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="RegActividadTareaGE"
    aria-hidden="true" data-backdrop="static">
    <div div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Registrar actividad
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="$('#form-ver').modal('show');javascript:limpiarModo()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important">
                <div class="row">
                    <div class="col-md-12">
                        <form action="javascript:registrarActividad()" id="FormregistrarActividad">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Nombre:</label>
                                        <input type="text" class="form-control form-control-sm" id="nombreTarea"
                                            maxlength="40" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Código:</label>
                                        <input type="text" class="form-control form-control-sm" id="codigoTarea"
                                            maxlength="40">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 text-left ml-4">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="customCRGE">
                                        <label class="custom-control-label" for="customCRGE" style="font-weight: bold">
                                            <i data-feather="activity"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>&nbsp;&nbsp;
                                            Control Remoto
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-left ml-4">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="customCRTGE">
                                        <label class="custom-control-label" for="customCRTGE" style="font-weight: bold">
                                            Control en Ruta
                                            <i data-feather="map-pin"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>&nbsp;&nbsp;
                                        </label>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal"
                                onclick="$('#form-ver').modal('show');javascript:limpiarModo()">Cancelar</button>
                            <button type="submit" name="" style="background-color: #163552;"
                                class="btn btn-sm ">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
{{-- FINALIZACIÓN DE MODAL --}}
{{-- MODAL DE AGREGAR ACTIVIDADES EN FORMULARIO AGREGAR--}}
<div id="ActividadTareaGE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ActividadTareaGE"
    aria-hidden="true" data-backdrop="static">
    <div div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Registrar actividad
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="$('#form-registrar').modal('show');javascript:limpiarModo()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important">
                <div class="row">
                    <div class="col-md-12">
                        <form action="javascript:registrarActividadFR()" id="FormregistrarActividadFR">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Nombre:</label>
                                        <input type="text" class="form-control form-control-sm" id="reg_nombreTarea"
                                            maxlength="40" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Código:</label>
                                        <input type="text" class="form-control form-control-sm" id="reg_codigoTarea"
                                            maxlength="40">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 text-left">
                                    <div class="custom-control custom-switch ml-4">
                                        <input type="checkbox" class="custom-control-input" id="customCRFR">
                                        <label class="custom-control-label" for="customCRFR" style="font-weight: bold">
                                            <i data-feather="activity"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>&nbsp;&nbsp;
                                            Control Remoto
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-left">
                                    <div class="custom-control custom-switch ml-4">
                                        <input type="checkbox" class="custom-control-input" id="customCRTFR">
                                        <label class="custom-control-label" for="customCRTFR" style="font-weight: bold">
                                            <i data-feather="map-pin"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>&nbsp;&nbsp;
                                            Control en Ruta
                                        </label>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal"
                                onclick="$('#form-registrar').modal('show');javascript:limpiarModo()">Cancelar</button>
                            <button type="submit" name="" style="background-color: #163552;"
                                class="btn btn-sm ">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
{{-- FINALIZACIÓN DE MODAL --}}
{{-- MODAL DE VIDEO DATOS PERSONALES --}}
<div class="modal fade" id="modal-video" tabindex="-1" role="dialog" aria-labelledby="modal-video" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg d-flex justify-content-center">
        <div class="modal-content" style="width: 800px">
            <div class="modal-header" style="background-color:#163552;padding:0.5rem">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:stopVideo()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- VIDEO --}}
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-lg-12  p-0 img-digital grid-margin grid-margin-lg-0" data-aos="fade-left">
                        <div style="padding:53.91% 0 0 0;position:relative;" id="contenidoIframe">
                            <iframe id="contenidoIframe"
                                src="https://player.vimeo.com/video/477794877?title=0&byline=0&portrait=0"
                                style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0"
                                allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>
                        <script src="https://player.vimeo.com/api/player.js"></script>
                    </div>
                </div>
                {{-- FINALIZAR VIDEO --}}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
{{-- FINAL DE MODAL --}}
{{-- MODAL DE VIDEO DATOS EMPRESARIAL --}}
<div class="modal fade" id="modal-videoE" tabindex="-1" role="dialog" aria-labelledby="modal-videoE" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg d-flex justify-content-center">
        <div class="modal-content" style="width: 800px">
            <div class="modal-header" style="background-color:#163552;padding:0.5rem">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:stopVideoE()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- VIDEO --}}
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-lg-12  p-0 img-digital grid-margin grid-margin-lg-0" data-aos="fade-left">
                        <div style="padding:53.91% 0 0 0;position:relative;" id="contenidoIframeE">
                            <iframe id="contenidoIframe"
                                src="https://player.vimeo.com/video/477794018?title=0&byline=0&portrait=0"
                                style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0"
                                allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>
                        <script src="https://player.vimeo.com/api/player.js"></script>
                    </div>
                </div>
                {{-- FINALIZAR VIDEO --}}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
{{-- FINAL DE MODAL --}}
{{-- MODAL DE VIDEO FOTO--}}
<div class="modal fade" id="modal-videoF" tabindex="-1" role="dialog" aria-labelledby="modal-videoF" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg d-flex justify-content-center">
        <div class="modal-content" style="width: 800px">
            <div class="modal-header" style="background-color:#163552;padding:0.5rem">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:stopVideoF()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- VIDEO --}}
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-lg-12  p-0 img-digital grid-margin grid-margin-lg-0" data-aos="fade-left">
                        <div style="padding:53.91% 0 0 0;position:relative;" id="contenidoIframeF">
                            <iframe id="contenidoIframe"
                                src="https://player.vimeo.com/video/477793806?title=0&byline=0&portrait=0"
                                style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0"
                                allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>
                        <script src="https://player.vimeo.com/api/player.js"></script>
                    </div>
                </div>
                {{-- FINALIZAR VIDEO --}}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
{{-- FINAL DE MODAL --}}
{{-- MODAL DE VIDEO CALENDARIO--}}
<div class="modal fade" id="modal-videoC" tabindex="-1" role="dialog" aria-labelledby="modal-videoC" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg d-flex justify-content-center">
        <div class="modal-content" style="width: 800px">
            <div class="modal-header" style="background-color:#163552;padding:0.5rem">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:stopVideoC()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- VIDEO --}}
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-lg-12  p-0 img-digital grid-margin grid-margin-lg-0" data-aos="fade-left">
                        <div style="padding:53.91% 0 0 0;position:relative;" id="contenidoIframeC">
                            <iframe id="contenidoIframe"
                                src="https://player.vimeo.com/video/477793882?title=0&byline=0&portrait=0"
                                style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0"
                                allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>
                        <script src="https://player.vimeo.com/api/player.js"></script>
                    </div>
                </div>
                {{-- FINALIZAR VIDEO --}}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
{{-- FINAL DE MODAL --}}
{{-- MODAL DE VIDEO HORARIO--}}
<div class="modal fade" id="modal-videoH" tabindex="-1" role="dialog" aria-labelledby="modal-videoH" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg d-flex justify-content-center">
        <div class="modal-content" style="width: 800px">
            <div class="modal-header" style="background-color:#163552;padding:0.5rem">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:stopVideoH()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- VIDEO --}}
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-lg-12  p-0 img-digital grid-margin grid-margin-lg-0" data-aos="fade-left">
                        <div style="padding:53.91% 0 0 0;position:relative;" id="contenidoIframeH">
                            <iframe id="contenidoIframe"
                                src="https://player.vimeo.com/video/477792484?title=0&byline=0&portrait=0"
                                style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0"
                                allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>
                        <script src="https://player.vimeo.com/api/player.js"></script>
                    </div>
                </div>
                {{-- FINALIZAR VIDEO --}}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
{{-- FINAL DE MODAL --}}
{{-- MODAL DE VIDEO ACTIVIDADES--}}
<div class="modal fade" id="modal-videoA" tabindex="-1" role="dialog" aria-labelledby="modal-videoA" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg d-flex justify-content-center">
        <div class="modal-content" style="width: 800px">
            <div class="modal-header" style="background-color:#163552;padding:0.5rem">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:stopVideoA()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- VIDEO --}}
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-lg-12  p-0 img-digital grid-margin grid-margin-lg-0" data-aos="fade-left">
                        <div style="padding:53.91% 0 0 0;position:relative;" id="contenidoIframeA">
                            <iframe id="contenidoIframeA"
                                src="https://player.vimeo.com/video/477792220?title=0&byline=0&portrait=0"
                                style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0"
                                allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>
                        <script src="https://player.vimeo.com/api/player.js"></script>
                    </div>
                </div>
                {{-- FINALIZAR VIDEO --}}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
{{-- FINAL DE MODAL --}}
{{-- MODAL DE VIDEO DISPOSITIVO--}}
<div class="modal fade" id="modal-videoD" tabindex="-1" role="dialog" aria-labelledby="modal-videoD" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg d-flex justify-content-center">
        <div class="modal-content" style="width: 800px">
            <div class="modal-header" style="background-color:#163552;padding:0.5rem">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:stopVideoD()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- VIDEO --}}
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-lg-12  p-0 img-digital grid-margin grid-margin-lg-0" data-aos="fade-left">
                        <div style="padding:53.91% 0 0 0;position:relative;" id="contenidoIframeD">
                            <iframe id="contenidoIframeA"
                                src="https://player.vimeo.com/video/477791531?title=0&byline=0&portrait=0"
                                style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0"
                                allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>
                        <script src="https://player.vimeo.com/api/player.js"></script>
                    </div>
                </div>
                {{-- FINALIZAR VIDEO --}}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
{{-- FINAL DE MODAL --}}
<style>
    #calendarInv>div.fc-view-container>div>table>tbody {
        background: #f4f4f4;
    }

    #calendarInv_ed>div.fc-view-container>div>table>tbody {
        background: #f4f4f4;
    }

    .page-item.active .page-link {
        background-color: #e3eaef;
        border-color: #e3eaef;
        color: #3d3d3d;
    }

    .fc-event,
    .fc-event-dot {
        font-size: 12.2px !important;
        margin: 2px 2px;
    }

    .form-control {
        font-size: 12px;
    }

    tr:first-child>td>.fc-day-grid-event {
        margin-top: 0px;
        padding-top: 0px;
        padding-bottom: 0px;
        margin-bottom: 0px;
        margin-left: 2px;
        margin-right: 2px;
    }

    .flatpickr-calendar {
        max-width: 90%;
    }


    .btn-outline-secondary {
        border-color: #e3eaef;
        background: #ffffff;
    }

    .container {
        margin-left: 40px;
        margin-right: 28px;
    }

    .fc-time {
        display: none;
    }

    .v-divider {
        border-right: 5px solid #4C5D73;
    }

    .sw-theme-default>ul.step-anchor>li.active>a {
        color: #1c68b1 !important;
    }

    .sw-theme-default>ul.step-anchor>li.done>a,
    .sw-theme-default>ul.step-anchor>li>a {
        color: #0b1b29 !important;
    }

    .day {
        max-width: 30%;
    }

    .month {
        max-width: 35%;
    }

    .year {
        max-width: 40%;
    }

    .btn-group {
        width: 100%;
        justify-content: space-between;
    }

    .sw-btn-group-extra {
        justify-content: flex-end !important;
    }

    .btn-secondary {
        max-width: 9em;
    }

    .form-control:disabled {
        background-color: #fcfcfc;
    }

    .fc-event,
    .fc-event-dot {
        /*  background-color: #d1c3c3; */
        font-size: 12.2px !important;
        margin: 2px 2px;
        cursor: url("../landing/images/cruz1.svg"), auto !important;

    }

    .fc-event-container>a {
        border: 1px solid #fff;
    }

    a:not([href]):not([tabindex]) {
        color: #000;
        cursor: pointer;
        font-size: 12px;
    }

    body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-light.bootbox-cancel {
        background: #e2e1e1;
        color: #000000;
        border-color: #e2e1e1;
        zoom: 85%;
    }

    body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-footer>button,
    body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-success.bootbox-accept {
        background-color: #163552;
        border-color: #163552;
        zoom: 85%;
    }

    .scroll {
        max-height: 100px;
        overflow-y: auto;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #52565b;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fdfdfd;
    }

    .col-lg-10 .select2-container .select2-selection {
        height: 20px;
        font-size: 12.2px;
        overflow-y: scroll;
    }

    .custom-select:disabled {
        color: #3f3a3a;
        background-color: #fcfcfc;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #ced0d3;
    }

    .btn-file {
        background-color: #1e2139 !important;
        border-color: #1e2139 !important;
    }

    .file-caption {
        height: calc(2.5em + 0.6rem + 2px) !important;
    }

    .borderColor {
        border-color: red;
    }

    /* CURSOR DE TABLA DISPOSITIVO */
    .cursorDispositivo {
        cursor: url("../landing/images/pencil.svg"), auto !important;
    }

    /* MODIFICAR ESTILOS DE ALERTIFY */
    .alertify .ajs-header {
        font-weight: normal;
    }

    .ajs-body {
        padding: 0px !important;
    }

    .alertify .ajs-footer {
        background: #ffffff;
    }

    .alertify .ajs-footer .ajs-buttons .ajs-button {
        min-height: 28px;
        min-width: 75px;
    }

    .ajs-cancel {
        font-size: 12px !important;
    }

    .ajs-ok {
        font-size: 12px !important;
    }

    .alertify .ajs-dialog {
        max-width: 450px;
    }

    .ajs-footer {
        padding: 12px !important;
    }

    .alertify .ajs-footer .ajs-buttons .ajs-button.ajs-ok {
        text-transform: none;
    }

    .alertify .ajs-footer .ajs-buttons.ajs-primary .ajs-button {
        text-transform: none;
    }

    /* RESPONSIVE */

    @media (max-width: 767.98px) {

        .btnResponsive {
            text-align: right !important;
            padding-right: 0% !important;
        }

        .btnPResponsive {
            text-align: center !important;
            display: flex !important;
            justify-content: space-between !important;
        }

        .titleResponsive {
            padding-right: 10px !important;
        }

        .inputResponsive {
            padding-right: 0% !important;
            padding-bottom: 5px !important;
        }

        .divTableResponsive {
            padding-right: 0% !important;
        }

        .table {
            width: 100% !important;
        }

        .dataTables_scrollHeadInner {
            width: 100% !important;
        }

        .dataTable,
        .dataTables_scrollHeadInner,
        .dataTables_scrollBody {
            width: 100% !important;
        }

        #v_dia_fecha,
        #v_dia_fechaV,
        #dia_fecha {
            max-width: 100%;
            margin: 0% !important;
            padding: 0% !important;
        }

        #v_mes_fecha,
        #v_mes_fechaV,
        #mes_fecha {
            max-width: 100%;
            margin: 0% !important;
            padding: 0% !important;
        }

        #v_ano_fecha,
        #v_ano_fechaV,
        #ano_fecha {
            max-width: 100%;
            margin: 0% !important;
            padding: 0% !important;
        }

        .fechasResponsive {
            flex-wrap: unset !important;
        }

        .col-4 {
            padding-right: 5px !important;
            padding-left: 5px !important;
        }

        .pAnio {
            padding-right: 1%;
        }

        .prigth {
            padding-right: 3px !important;
        }

        .pleft {
            padding-left: 3px !important;
        }

        .selectResp {
            padding: 0 !important;
        }

        .pselect {
            padding-left: 10px !important;
            padding-right: 0 !important;
        }

        .custom-control {
            padding-left: 1rem !important;
        }

        .labelNivel {
            display: flex !important;
            font-size: smaller !important;
        }

        li.paginate_button.previous,
        li.paginate_button.next {
            font-size: 0.9rem !important;
        }
    }

    @media (max-width: 447px) {
        .radio_rsp{
            padding: 0 0 !important;
        }
    }

    @media (max-width: 799px) and (min-width: 447px){
        .radio_rsp{
            padding: 0 5% !important;
        }
    }

    @media (max-width: 578px) {
        .label_select{
        padding: 0px 0px 0px 0px !important;
    }
    }

    .radio_rsp{
        padding: 0 10%;
    }

    .label_txt{
        padding: 10px 0px 0px 0px !important;
        font-weight: 200 !important;
    }

    .label_select{
        padding: 10px 0px 0px 0px;
    }

    @media(min-width: 386px) and (max-width: 487px){
        .th_rh{
            padding: 0 35px !important;
        }
    }

    /* FINALIZACION DE RESPONSIVE */
</style>
{{-- BOTONES DE CARGAS MASIVAS --}}
<div class="row page-title titleResponsive" style="padding-right: 20px;">
    <div class="col-md-7">
        <h4 class="header-title mt-0 "></i>Empleados</h4>
    </div>
    @if (isset($agregarEmp))
    @if ($agregarEmp==1)
    <div class="col-sm-12 col-md-12 col-xl-5 text-right btnPResponsive">
        <a href="{{ '/empleado/cargar' }}">
            <button class="btn btn-outline-secondary btn-sm m-1">
                <img src="{{ asset('admin/images/subir.ico') }}" height="20" class="mr-1">
                Carga masiva emp.
            </button>
        </a>
        &nbsp;&nbsp;
        <button class="btn btn-outline-secondary btn-sm m-1" id="cargaMasivaF">
            <img src="{{ asset('admin/images/image.ico') }}" height="20" class="mr-1">
            Carga masiva fotos
        </button>
    </div>
    @else

    @endif
    @else
    <div class="col-sm-12 col-md-12 col-xl-5 text-right btnPResponsive">
        <a href="{{ '/empleado/cargar' }}">
            <button class="btn btn-outline-secondary btn-sm m-1">
                <img src="{{ asset('admin/images/subir.ico') }}" height="20" class="mr-1">
                Carga masiva emp.
            </button>
        </a>
        &nbsp;&nbsp;
        <button class="btn btn-outline-secondary btn-sm m-1" id="cargaMasivaF">
            <img src="{{ asset('admin/images/image.ico') }}" height="20" class="mr-1">
            Carga masiva fotos
        </button>
    </div>
    @endif

</div>
{{-- FINAIZACION --}}
@endsection
@section('content')
<div class="row ">
    <div class="col-md-12 col-xl-12">
        <div class="card">
            <div class="card-body"
                style="padding-top: 0px; background: #ffffff; font-size: 12.8px;color: #222222; padding-left:0px;">
                <div class="row">
                    {{-- BOTONES --}}
                    <div class=" col-md-12 text-right btnResponsive">
                        @if (isset($agregarEmp))
                        @if ($agregarEmp==1)
                        <button class="btn btn-sm btn-primary" id="formNuevoE"
                            style="background-color: #e3eaef;border-color:#e3eaef;color:#3d3d3d">
                            Nuevo
                        </button>
                        @else

                        @endif
                        @else
                        <button class="btn btn-sm btn-primary" id="formNuevoE"
                            style="background-color: #e3eaef;border-color:#e3eaef;color:#3d3d3d">
                            Nuevo
                        </button>
                        @endif
                    </div>
                    {{-- FINALIZACION --}}
                    {{-- BUSQUEDA PARA TABLA --}}
                    <div class="col-md-12">
                        <h5 style="font-size: 16px!important">Búsqueda personalizada</h5>
                    </div>
                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 inputResponsive" id="filter_global">
                        <td align="center">
                            <input type="text" class="global_filter form-control" id="global_filter"
                                style="height: 35px;" placeholder="Buscar por...">
                        </td>
                    </div>
                    <div class="col-12 col-sm-2 col-md-2 col-lg-2 inputResponsive">
                        <td align="center">
                            <select class="form-control" name="select" id="select" style="height: 35.5px;">
                                <option value="2">Documento</option>
                                <option value="3">Nombre</option>
                                <option value="4" selected>Apellidos</option>
                                <option value="8">Cargo</option>
                                <option value="9">Área</option>
                            </select>
                        </td>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-6 inputResponsive">
                        <div class="form-group row">
                            <label class="col-lg-2 col-md-3 col-sm-3 col-form-label">Área:</label>
                            <div class="col-lg-10 col-md-9 col-sm-9 text-left">
                                <select id="selectarea" data-plugin="customselect" class="form-control form-control-sm"
                                    multiple="multiple" data-placeholder="Seleccionar áreas">
                                    @foreach ($area as $areas)
                                    <option value="{{ $areas->area_id }}">
                                        {{ $areas->area_descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- FINALZACION DE BUSQUEDA --}}
                </div>
                {{-- GIF DE BUSQUEDA --}}
                <div id="espera" class="text-center" style="display: none">
                    <img src="{{ asset('landing/images/loading.gif') }}" height="100">
                </div>
                {{-- FINALIZACION DE GIF --}}
                {{-- TABLA --}}
                <div id="tabladiv" class="divTableResponsive"></div>
                {{-- FINALIZACION DE TABLA --}}
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div>
    {{-- MODAL DE INFORMACION DE CARGA MASIVA DE FOTOS --}}
    <div id="modalInformacionF" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalInformacionF"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <span class="mr-2 text-center">
                            Después de haber registrado o cargado tus empleados, puedes cargar sus fotos de una manera
                            más simple.
                        </span>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xl-5 text-center">
                            <div class="row justify-content-center">
                                <img src="{{ asset('landing/images/photo (3).svg') }}" height="100">
                            </div>
                            <div class="row justify-content-center">
                                <span class="mr-2 text-center" style="color: #024079;font-weight: bold;">
                                    DNI empleado
                                </span>
                            </div>
                        </div>
                        <div class="col-xl-2 text-left">
                            <img src="{{ asset('landing/images/right-arrow.svg') }}" height="80">
                        </div>
                        <div class="col-xl-5 text-center">
                            <div class="row justify-content-center">
                                <img src="{{ asset('landing/images/photo (3).svg') }}" height="100">
                            </div>
                            <div class="row justify-content-center">
                                <span class="mr-2 text-center" style="color: #024079;font-weight: bold;">12345678</span>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xl-12 text-center">
                            <img src="{{ asset('landing/images/alert.svg') }}" height="25" class="mr-1">
                            <span>Puedes guardar tus fotos en una carpeta especifica.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 text-center">
                        <button style="background-color: #024079;color: white;" type="button" id="cerrarIF"
                            class="btn btn-light" data-dismiss="modal">Entendido</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    {{-- FINALIZACION DE MODAL --}}
    {{-- MODAL DE CARGA MASIVA DE FOTOS --}}
    <div id="modalMasivaFoto" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalMasivaFoto"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Carga Masiva de Fotos a Empleados
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row pt-2">
                        <div class="col-xl-12 text-center">
                            <div class="file-loading">
                                {{ csrf_field() }}
                                <input id="fileMasiva" name="fileMasiva[]" type="file" multiple webkitdirectory
                                    accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="button" id="cerrarMFotos" class="btn btn-light" data-dismiss="modal"
                                    style="background-color:#163552;color:#ffffff;">Finalizar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    {{-- FINALIZACION DE MODAL --}}
    {{-- MODAL DE ELIMINAR --}}
    <div id="modalEliminar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalEliminar"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Dar de baja
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="empleadoEliminacion">
                    <form action="javascript:confirmarBaja()">
                        <div class="col-xl-12">
                            <div class="form-group row">
                                <label class="col-lg-7 col-form-label" style="padding-top: 14px;">
                                    Fecha de baja o cese de empleado(s)
                                </label>
                                <div class="alert alert-danger" role="alert" style="display: none;"
                                    id="alertFechaBajaH">
                                </div>
                                <div class="input-group col-md-5 text-center"
                                    style="padding-left: 0px;padding-right: 0px;top: 10px;" id="fechaSelectBaja">
                                    <input type="text" id="fechaInputBaja" class="col-md-12 form-control" data-input>
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
                        <div class="col-md-12 pt-1" id="validArchivosBaja" style="display: none;">
                            <span style="color: red;">
                                *El tamaño supera el limite de 4 MB.
                            </span>
                        </div>
                        <div class="col-md 12" id="divbajaF">
                            <label for="">Adjuntar documento(s)</label>
                            <div class="form-group">
                                <input type="file" name="bajaFile" id="bajaFile"
                                    accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                                    class="inputfile inputfile-1" data-multiple-caption="{count} archivos seleccionados"
                                    multiple size="4194304" />
                                <label for="bajaFile">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
                                        <path
                                            d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z">
                                        </path>
                                    </svg>
                                    <span class="iborrainputfile">Seleccionar archivo</span>
                                </label>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="cerrarE" class="btn btn-light btn-sm" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" id="confirmarE" style="background-color: #163552;" class="btn btn-sm">
                        Confirmar
                    </button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION DE MODAL --}}
    {{-- MODAL DE BAJA --}}
    <div id="modalBajaHistorial" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalBajaHistorial"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Dar de baja
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="javascript:cerrarModalHistorial()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idHistorialEdit">
                    <form action="javascript:confirmarBajaHistorial()">
                        <div class="col-xl-12">
                            <div class="form-group row">
                                <label class="col-lg-7 col-form-label" style="padding-top: 14px;">
                                    Fecha de baja o cese de empleado(s)
                                </label>
                                <div class="alert alert-danger" role="alert" style="display: none;" id="alertFechaBaja">
                                </div>
                                <div class="input-group col-md-5 text-center"
                                    style="padding-left: 0px;padding-right: 0px;top: 10px;" id="fechaBajaEdit">
                                    <input type="text" id="fechaBajaInput" class="col-md-12 form-control" data-input>
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
                        <div class="col-md-12 pt-1" id="validArchivoBajaE" style="display: none;">
                            <span style="color: red;">
                                *El tamaño supera el limite de 4 MB.
                            </span>
                        </div>
                        <div class="col-md 12" id="divbajaEdit">
                            <label for="">
                                Adjuntar documento(s)
                            </label>
                            <div class="form-group">
                                <input type="file" name="bajaFileEdit" id="bajaFileEdit"
                                    accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                                    class="inputfile inputfile-1" data-multiple-caption="{count} archivos seleccionados"
                                    multiple size="4194304" />
                                <label for="bajaFileEdit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
                                        <path
                                            d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z">
                                        </path>
                                    </svg>
                                    <span class="iborrainputfile">Seleccionar archivo</span>
                                </label>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal"
                        onclick="javascript:cerrarModalHistorial()">
                        Cancelar
                    </button>
                    <button type="submit" style="background-color: #163552;" class="btn btn-sm">
                        Confirmar
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    {{-- FINALIZACION DE MODAL --}}
    {{-- MODAL DE BAJA EN REGISTRAR --}}
    <div id="modalBajaHistorialReg" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="modalBajaHistorialReg" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Dar de baja
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="javascript:cerrarModalHistorialReg()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idHistorialReg">
                    <form action="javascript:confirmarBajaHistorialReg()">
                        <div class="col-xl-12">
                            <div class="form-group row">
                                <label class="col-lg-7 col-form-label" style="padding-top: 14px;">
                                    Fecha de baja o cese de empleado(s)
                                </label>
                                <div class="alert alert-danger" role="alert" style="display: none;"
                                    id="alertFechaBajaReg">
                                </div>
                                <div class="input-group col-md-5 text-center"
                                    style="padding-left: 0px;padding-right: 0px;top: 10px;" id="fechaBajaReg">
                                    <input type="text" id="fechaBajaInputReg" class="col-md-12 form-control" data-input>
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
                        <div class="col-md-12 pt-1" id="validArchivoBajaReg" style="display: none;">
                            <span style="color: red;">
                                *El tamaño supera el limite de 4 MB.
                            </span>
                        </div>
                        <div class="col-md 12" id="divbajaEdit">
                            <label for="">
                                Adjuntar documento(s)
                            </label>
                            <div class="form-group">
                                <input type="file" name="bajaFileReg" id="bajaFileReg"
                                    accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                                    class="inputfile inputfile-1" data-multiple-caption="{count} archivos seleccionados"
                                    multiple size="4194304" />
                                <label for="bajaFileReg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
                                        <path
                                            d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z">
                                        </path>
                                    </svg>
                                    <span class="iborrainputfile">Seleccionar archivo</span>
                                </label>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal"
                        onclick="javascript:cerrarModalHistorialReg()">
                        Cancelar
                    </button>
                    <button type="submit" style="background-color: #163552;" class="btn btn-sm">
                        Confirmar
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    {{-- FINALIZACION DE MODAL --}}
    {{-- CRUD DE AREA EN REGISTRAR --}}
    <div id="areamodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="areamodal" aria-hidden="true"
        data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Agregar área
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#form-registrar').modal('show');javascript:limpiar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarArea()">
                        {{ csrf_field() }}
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="">Área</label>
                                </div>
                                <div id="editarArea" class="col-md-6"></div>
                                <div class="col-md-4">
                                    <a id="buscarArea" data-toggle="tooltip" data-placement="right" title="editar área."
                                        data-original-title="editar área." style="cursor: pointer;">
                                        <img src="{{ asset('landing/images/search.svg') }}" height="22">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 pt-2">
                            <input type="text" class="form-control" name="textArea" id="textArea" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#form-registrar').modal('show');javascript:limpiar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" id="guardarArea" class="btn btn-sm"
                        style="background-color: #163552;">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION DE MODAL --}}
    {{-- CRUD DE CARGO EN REGISTRAR --}}
    <div id="cargomodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="cargomodal" aria-hidden="true"
        data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Agregar cargo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#form-registrar').modal('show');javascript:limpiar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarcargo()">
                        {{ csrf_field() }}
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2">
                                    <label for="">Cargo</label>
                                </div>
                                <div id="editarCargo" class="col-md-6"></div>
                                <div class="col-md-4">
                                    <a id="buscarCargo" data-toggle="tooltip" data-placement="right"
                                        title="editar cargo" data-original-title="editar cargo"
                                        style="cursor: pointer;">
                                        <img src="{{ asset('landing/images/search.svg') }}" height="18">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 pt-2">
                            <input type="text" class="form-control" id="textCargo" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#form-registrar').modal('show');javascript:limpiar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm" id="guardarCargo"
                        style="background-color:#163552;">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION DE MODAL --}}
    {{-- CRUD DE CENTRO COSTO EN REGISTRAR --}}
    <div id="centrocmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="centrocmodal"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Agregar centro costo
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#form-registrar').modal('show');javascript:limpiar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarcentro()">
                        {{ csrf_field() }}
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Centro costo</label>
                                </div>
                                <div id="editarCentro" class="col-md-6"></div>
                                <div class="col-md-3">
                                    <a id="buscarCentro" data-toggle="tooltip" data-placement="right"
                                        title="editar centro costo" data-original-title="editar centro costo."
                                        style="cursor: pointer;">
                                        <img src="{{ asset('landing/images/search.svg') }}" height="18">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 pt-2">
                            <input type="text" class="form-control" id="textCentro" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#form-registrar').modal('show');javascript:limpiar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm" id="guardarCentro"
                        style="background-color:#163552;">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION DE MODAL --}}
    {{-- CRUD DE LOCAL EN REGISTRAR --}}
    <div id="localmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="localmodal" aria-hidden="true"
        data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Agregar local
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#form-registrar').modal('show');javascript:limpiar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarlocal()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-2">
                                <label for="">Local</label>
                            </div>
                            <div id="editarLocal" class="col-md-6"></div>
                            <div class="col-md-4">
                                <a id="buscarLocal" data-toggle="tooltip" data-placement="right" title="editar local"
                                    data-original-title="editar local" style="cursor: pointer;">
                                    <img src="{{ asset('landing/images/search.svg') }}" height="18">
                                </a>
                            </div>
                        </div>
                        <div class="col-md-12 pt-2">
                            <input type="text" class="form-control" name="textLocal" id="textLocal" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#form-registrar').modal('show');javascript:limpiar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" id="guardarLocal" class="btn btn-sm"
                        style="background-color:#163552;">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION DE MODAL --}}
    {{-- CRUD DE NIVEL EN REGISTRAR --}}
    <div id="nivelmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="nivelmodal" aria-hidden="true"
        data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Agregar nivel
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#form-registrar').modal('show');javascript:limpiar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarnivel()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-2">
                                <label for="">Nivel del colaborador</label>
                            </div>
                            <div id="editarNivel" class="col-md-6"></div>
                            <div class="col-md-4">
                                <a id="buscarNivel" data-toggle="tooltip" data-placement="right" title="editar nivel"
                                    data-original-title="editar nivel" style="cursor: pointer;">
                                    <img src="{{ asset('landing/images/search.svg') }}" height="18">
                                </a>
                            </div>
                        </div>
                        <div class="col-md-12 pt-2">
                            <input type="text" class="form-control" name="textNivel" id="textNivel" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#form-registrar').modal('show');javascript:limpiar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" id="guardarNivel" class="btn btn-sm"
                        style="background-color:#163552;">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION DE MODAL --}}
    {{-- CRUD DE CONTRATO EN REGISTRAR --}}
    <div id="contratomodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="contratomodal"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Agregar contrato
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#contratoDetallesmodal').modal('show');javascript:limpiar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarContrato()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-2">
                                <label for="">Contrato</label>
                            </div>
                            <div id="editarContrato" class="col-md-6"></div>
                            <div class="col-md-4">
                                <a id="buscarContrato" data-toggle="tooltip" data-placement="right"
                                    title="editar contrato" data-original-title="editar contrato"
                                    style="cursor: pointer;">
                                    <img src="{{ asset('landing/images/search.svg') }}" height="18">
                                </a>
                            </div>
                        </div>
                        <div class="col-md-12 pt-2">
                            <input type="text" class="form-control" name="textContrato" id="textContrato" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#contratoDetallesmodal').modal('show');javascript:limpiar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">
                        Cerrar
                    </button>
                    <button type="submit" id="guardarContrato" class="btn btn-sm" style="background-color:#163552;">
                        Guardar
                    </button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION DE MODAL --}}
    {{-- CRUD DE CONDICION DE PAGO EN REGISTRAR --}}
    <div id="condicionmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="condicionmodal"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Agregar condición de pago
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="javascript:ModalCerrarCondicionReg();javascript:limpiar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarCondicion()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-2">
                                <label for="">Condición</label>
                            </div>
                            <div id="editarCondicion" class="col-md-6"></div>
                            <div class="col-md-4">
                                <a id="buscarCondicion" data-toggle="tooltip" data-placement="right"
                                    title="editar condición de pago" data-original-title="editar condición de pago"
                                    style="cursor: pointer;">
                                    <img src="{{asset('landing/images/search.svg')}}" height="18">
                                </a>
                            </div>
                        </div>
                        <div class="col-md-12 pt-2">
                            <input type="text" class="form-control" name="textCondicion" id="textCondicion" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="javascript:ModalCerrarCondicionReg();javascript:limpiar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">
                        Cerrar
                    </button>
                    <button type="submit" id="guardarCondicion" class="btn btn-sm" style="background-color:#163552;">
                        Guardar
                    </button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION DE MODAL --}}
    {{-- CRUD DE DE CONTRATO EN REGISTRAR --}}
    <div id="contratoDetallesmodal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="contratoDetallesmodal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Detalles de contrato
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="javascript:limpiarNuevosDatosAlta();$('#form-registrar').modal('show');">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:nuevaAltaReg()" onsubmit="javascript:validate(event)">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label for="sw-default">
                                        Contrato
                                        <a onclick="$('#contratoDetallesmodal').modal('hide');" href="#contratomodal"
                                            data-toggle="modal" data-target="#contratomodal">
                                            <i class="uil uil-plus"></i>
                                        </a>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    </label>
                                    <span id="validContrato" style="color: red;display:none">*Campo Obligatorio</span>
                                    <select class="form-control" name="contrato" id="contrato" tabindex="5"
                                        onclick="javascript:validacionNuevaAltaReg()" required>
                                        <option value="">Seleccionar</option>
                                        @foreach ($tipo_cont as $tipo_conts)
                                        <option value="{{ $tipo_conts->contrato_id }}">
                                            {{ $tipo_conts->contrato_descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sw-default">
                                        Condición Pago
                                        <a onclick="javascript:ModalAbiertoCondicionReg()" href="#condicionmodal"
                                            data-toggle="modal" data-target="#condicionmodal">
                                            <i class="uil uil-plus"></i>
                                        </a>
                                    </label>
                                    <select class="form-control" name="condicion" id="condicion" required>
                                        <option value="">Seleccionar</option>
                                        @foreach ($condicionP as $condicion)
                                        <option class="" value="{{$condicion->id}}">
                                            {{$condicion->condicion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sw-default">Monto</label>
                                    <input type="number" step=".01" class="form-control" name="monto" id="monto">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="" style="font-weight: 600">Fecha Inicial</label>
                                <span id="m_validFechaC" style="color: red;display: none;">
                                    *Fecha incorrecta.
                                </span>
                                <div class="alert alert-danger" role="alert" style="display: none;"
                                    id="alertErrorFechaReg"></div>
                                <div class="row">
                                    <div class="col-4">
                                        <select class="form-control" name="m_dia_fecha" id="m_dia_fecha" required="">
                                            <option value="0">Día</option>
                                            @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                {{$i}}</option>
                                                @endfor
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-control" name="m_mes_fecha" id="m_mes_fecha" required>
                                            <option value="0">Mes</option>
                                            <option class="" value="1">Enero</option>
                                            <option class="" value="2">Febrero</option>
                                            <option class="" value="3">Marzo</option>
                                            <option class="" value="4">Abril</option>
                                            <option class="" value="5">Mayo</option>
                                            <option class="" value="6">Junio</option>
                                            <option class="" value="7">Julio</option>
                                            <option class="" value="8">Agosto</option>
                                            <option class="" value="9">Septiembre</option>
                                            <option class="" value="10">Octubre</option>
                                            <option class="" value="11">Noviembre</option>
                                            <option class="" value="12">Diciembre</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-control" style="padding-left: 5px;padding-right: 5px;"
                                            name="m_ano_fecha" id="m_ano_fecha" required>
                                            <option value="0">Año</option>
                                            @for ($i = 2000; $i <2100; $i++) <option class="" value="{{$i}}">{{$i}}
                                                </option>
                                                @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="row pb-2">
                                    <div class="col-md-12 pt-1" id="validArchivoReg" style="display: none;">
                                        <span style="color: red;">
                                            *El tamaño supera el limite de 4 MB.
                                        </span>
                                    </div>
                                    <div class="col-md 12">
                                        <div class="form-group" style="margin-top: 14px;margin-bottom: 0px;">
                                            <input type="file"
                                                accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                                                class="inputfile inputfile-1"
                                                data-multiple-caption="{count} archivos seleccionados" multiple
                                                id="reg_fileArchivos" size="4194304">
                                            <label for="reg_fileArchivos">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17"
                                                    viewBox="0 0 20 17">
                                                    <path
                                                        d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z">
                                                    </path>
                                                </svg>
                                                <span class="iborrainputfile">Adjuntar archivo</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="pb-2" id="labelfechaF" style="font-weight: 600">Fecha Final</label>
                                    <label for="">Fecha Indefinida</label>
                                    <input type="checkbox" id="checkboxFechaI" name="checkboxFechaI">
                                </div>
                                <div id="ocultarFecha">
                                    <span id="mf_validFechaC" style="color: red;display: none;">
                                        *Fecha incorrecta.
                                    </span>
                                    <div class="row">
                                        <div class="col-4">
                                            <select class="form-control" name="mf_dia_fecha" id="mf_dia_fecha"
                                                required="">
                                                <option value="0">Día</option>
                                                @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                    {{$i}}</option>
                                                    @endfor
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select class="form-control" name="mf_mes_fecha" id="mf_mes_fecha" required>
                                                <option value="0">Mes</option>
                                                <option class="" value="1">Enero</option>
                                                <option class="" value="2">Febrero</option>
                                                <option class="" value="3">Marzo</option>
                                                <option class="" value="4">Abril</option>
                                                <option class="" value="5">Mayo</option>
                                                <option class="" value="6">Junio</option>
                                                <option class="" value="7">Julio</option>
                                                <option class="" value="8">Agosto</option>
                                                <option class="" value="9">Septiembre</option>
                                                <option class="" value="10">Octubre</option>
                                                <option class="" value="11">Noviembre</option>
                                                <option class="" value="12">Diciembre</option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select class="form-control" style="padding-left: 5px;padding-right: 5px;"
                                                name="mf_ano_fecha" id="mf_ano_fecha" required>
                                                <option value="0">Año</option>
                                                @for ($i = 2014; $i <2100; $i++) <option class="" value="{{$i}}">{{$i}}
                                                    </option>
                                                    @endfor
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pt-3 d-flex inline">
                                <label class="col-md-10 pt-1">¿Cuántos días antes que finalice su contrato, le gustaría que se le notifique?</label>
                                <select class="form-control col-2" name="noti_dia_fecha" id="noti_dia_fecha" required="">
                                    <option value="0">Día</option>
                                @for ($i = 1; $i <32; $i++) 
                                    <option class="" value="{{$i}}"> {{ $i }} </option>
                                @endfor
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                        onclick="javascript:limpiarNuevosDatosAlta();$('#form-registrar').modal('show');"
                        class="btn btn-sm btn-light" data-dismiss="modal">
                        Cerrar
                    </button>
                    <button type="submit" class="btn btn-sm" style="background-color:#163552;"
                        id="reg_guardarAlta">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION DE MODAL --}}
    {{-- DETALLES DE CONTRATO EN REGISTRAR --}}
    <div id="detallesContratomodal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="detallesContratomodal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Detalles de contrato
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="javascript:limpiarNuevosDatosAlta();$('#form-registrar').modal('show');">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="reg_idContratoD">
                    <form action="javascript:editarDetalleCReg()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label for="sw-default">
                                        Contrato
                                    </label>
                                    <select class="form-control" id="contratoD" tabindex="5"
                                        onclick="javascript:validacionNuevaAltaReg()" required disabled>
                                        <option value="">Seleccionar</option>
                                        @foreach ($tipo_cont as $tipo_conts)
                                        <option value="{{ $tipo_conts->contrato_id }}">
                                            {{ $tipo_conts->contrato_descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sw-default">
                                        Condición Pago
                                        <a onclick="javascript:ModalAbiertoCondicionReg()" href="#condicionmodal"
                                            data-toggle="modal" data-target="#condicionmodal">
                                            <i class="uil uil-plus"></i>
                                        </a>
                                    </label>
                                    <select class="form-control" id="condicionD" required>
                                        <option value="">Seleccionar</option>
                                        @foreach ($condicionP as $condicion)
                                        <option class="" value="{{$condicion->id}}">
                                            {{$condicion->condicion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-6" style="padding-top: 2.2em">
                                <div class="dropdown" id="reg_documentosDetalles">
                                    <a class="dropdown" data-toggle="dropdown" aria-expanded="false"
                                        style="cursor: pointer">
                                        <span class="badge badge-soft-primary text-primary">
                                            <i class="uil-file-alt font-size-18"></i>
                                            &nbsp;
                                            Documentos
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                        id="reg_documentosxDetalle">
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sw-default">Monto</label>
                                    <input type="number" step=".01" class="form-control" id="montoD">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="" style="font-weight: 600">Fecha Inicial</label>
                                <span id="m_validFechaCD" style="color: red;display: none;">
                                    *Fecha incorrecta.
                                </span>
                                <div class="alert alert-danger" role="alert" style="display: none;"
                                    id="alertErrorFechaDetalleReg"></div>
                                <div class="row">
                                    <div class="col-4">
                                        <select class="form-control" name="m_dia_fechaD" id="m_dia_fechaD" required>
                                            <option value="0">Día</option>
                                            @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                {{$i}}</option>
                                                @endfor
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-control" name="m_mes_fechaD" id="m_mes_fechaD" required>
                                            <option value="0">Mes</option>
                                            <option class="" value="1">Enero</option>
                                            <option class="" value="2">Febrero</option>
                                            <option class="" value="3">Marzo</option>
                                            <option class="" value="4">Abril</option>
                                            <option class="" value="5">Mayo</option>
                                            <option class="" value="6">Junio</option>
                                            <option class="" value="7">Julio</option>
                                            <option class="" value="8">Agosto</option>
                                            <option class="" value="9">Septiembre</option>
                                            <option class="" value="10">Octubre</option>
                                            <option class="" value="11">Noviembre</option>
                                            <option class="" value="12">Diciembre</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-control" style="padding-left: 5px;padding-right: 5px;"
                                            name="m_ano_fechaD" id="m_ano_fechaD" required>
                                            <option value="0">Año</option>
                                            @for ($i = 2000; $i <2100; $i++) <option class="" value="{{$i}}">{{$i}}
                                                </option>
                                                @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="row pb-2">
                                    <div class="col-md-12 pt-1" id="validArchivoD" style="display: none;">
                                        <span style="color: red;">
                                            *El tamaño supera el limite de 4 MB.
                                        </span>
                                    </div>
                                    <div class="col-md 12">
                                        <div class="form-group" style="margin-top: 14px;margin-bottom: 0px;">
                                            <input type="file"
                                                accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                                                class="inputfile inputfile-1"
                                                data-multiple-caption="{count} archivos seleccionados" multiple
                                                id="reg_fileArchivosD" size="4194304">
                                            <label for="reg_fileArchivosD">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17"
                                                    viewBox="0 0 20 17">
                                                    <path
                                                        d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z">
                                                    </path>
                                                </svg>
                                                <span class="iborrainputfile">Adjuntar archivo</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="pb-2" id="labelfechaF" style="font-weight: 600">Fecha Final</label>
                                    <label class="ocultarFechaD">Fecha Indefinida</label>
                                    <input type="checkbox" class="ocultarFechaD" id="checkboxFechaID"
                                        name="checkboxFechaID">
                                </div>
                                <div id="ocultarFechaD">
                                    <span id="mf_validFechaCD" style="color: red;display: none;">
                                        *Fecha incorrecta.
                                    </span>
                                    <div class="row">
                                        <div class="col-4">
                                            <select class="form-control" name="mf_dia_fechaD" id="mf_dia_fechaD"
                                                required>
                                                <option value="0">Día</option>
                                                @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                    {{$i}}</option>
                                                    @endfor
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select class="form-control" name="mf_mes_fechaD" id="mf_mes_fechaD"
                                                required>
                                                <option value="0">Mes</option>
                                                <option class="" value="1">Enero</option>
                                                <option class="" value="2">Febrero</option>
                                                <option class="" value="3">Marzo</option>
                                                <option class="" value="4">Abril</option>
                                                <option class="" value="5">Mayo</option>
                                                <option class="" value="6">Junio</option>
                                                <option class="" value="7">Julio</option>
                                                <option class="" value="8">Agosto</option>
                                                <option class="" value="9">Septiembre</option>
                                                <option class="" value="10">Octubre</option>
                                                <option class="" value="11">Noviembre</option>
                                                <option class="" value="12">Diciembre</option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select class="form-control" style="padding-left: 5px;padding-right: 5px;"
                                                name="mf_ano_fechaD" id="mf_ano_fechaD" required>
                                                <option value="0">Año</option>
                                                @for ($i = 2014; $i <2100; $i++) <option class="" value="{{$i}}">{{$i}}
                                                    </option>
                                                    @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#form-registrar').modal('show');" class="btn btn-sm btn-light"
                        data-dismiss="modal">
                        Cerrar
                    </button>
                    <button type="submit" class="btn btn-sm" style="background-color:#163552;">
                        Guardar
                    </button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION DE MODAL --}}
    {{-- MODALES DE EDITAR --}}
    <div id="areamodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="areamodalE" aria-hidden="true"
        data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                        área</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#form-ver').modal('show');javascript:limpiarEditar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarAreaA()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-2">
                                <label for="">Área</label>
                            </div>
                            <div id="editarAreaA" class="col-md-6"></div>
                            <div class="col-md-4">
                                <a id="buscarAreaA" data-toggle="tooltip" data-placement="right" title="Editar Area."
                                    data-original-title="Editar Area." style="cursor: pointer;"><img
                                        src="{{ asset('landing/images/search.svg') }}" height="18">
                                </a>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <input type="text" class="form-control" name="textAreaE" id="textAreaE" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#form-ver').modal('show');javascript:limpiarEditar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm" style="background-color:#163552;">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div id="cargomodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="cargomodalE"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                        Cargo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#form-ver').modal('show');javascript:limpiarEditar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarcargoA()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-2">
                                <label for="">Cargo</label>
                            </div>
                            <div id="editarCargoA" class="col-md-6"></div>
                            <div class="col-md-4">
                                <a id="buscarCargoA" data-toggle="tooltip" data-placement="right" title="Editar Cargo."
                                    data-original-title="Editar Cargo." style="cursor: pointer;"><img
                                        src="{{ asset('landing/images/search.svg') }}" height="18">
                                </a>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <input type="text" class="form-control" id="textCargoE" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#form-ver').modal('show');javascript:limpiarEditar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm" style="background-color:#163552;">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div id="centrocmodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="centrocmodalE"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                        Centro Costo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#form-ver').modal('show');javascript:limpiarEditar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarcentroA()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Centro Costo</label>
                            </div>
                            <div id="editarCentroA" class="col-md-6"></div>
                            <div class="col-md-3">
                                <a id="buscarCentroA" data-toggle="tooltip" data-placement="right"
                                    title="Editar Centro Costo." data-original-title="Editar Centro Costo."
                                    style="cursor: pointer;"><img src="{{ asset('landing/images/search.svg') }}"
                                        height="18">
                                </a>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <input type="text" class="form-control" id="textCentroE" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#form-ver').modal('show');javascript:limpiarEditar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm" style="background-color:#163552;">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div id="localmodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="localmodalE"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                        Local</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#form-ver').modal('show');javascript:limpiarEditar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarlocalA()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-2">
                                <label for="">Local</label>
                            </div>
                            <div id="editarLocalA" class="col-md-6"></div>
                            <div class="col-md-4">
                                <a id="buscarLocalA" data-toggle="tooltip" data-placement="right" title="Editar Local."
                                    data-original-title="Editar Local." style="cursor: pointer;"><img
                                        src="{{ asset('landing/images/search.svg') }}" height="18">
                                </a>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <input type="text" class="form-control" name="textLocalE" id="textLocalE" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#form-ver').modal('show');javascript:limpiarEditar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm" style="background-color:#163552;">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div id="nivelmodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="nivelmodalE"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                        Nivel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#form-ver').modal('show');javascript:limpiarEditar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarnivelA()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-2">
                                <label for="">Nivel</label></div>
                            <div id="editarNivelA" class="col-md-6"></div>
                            <div class="col-md-4">
                                <a id="buscarNivelA" data-toggle="tooltip" data-placement="right" title="Editar Nivel."
                                    data-original-title="Editar Nivel." style="cursor: pointer;"><img
                                        src="{{ asset('landing/images/search.svg') }}" height="18">
                                </a>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <input type="text" class="form-control" name="textNivelE" id="textNivelE" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#form-ver').modal('show');javascript:limpiarEditar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm" style="background-color:#163552;">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div id="contratomodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="contratomodalE"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                        Contrato</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="javascript:ModalCerrarCondicion();javascript:limpiarEditar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarContratoA()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-2">
                                <label for="">Contrato</label>
                            </div>
                            <div id="editarContratoA" class="col-md-6"></div>
                            <div class="col-md-4">
                                <a id="buscarContratoA" data-toggle="tooltip" data-placement="right"
                                    title="Editar Contrato." data-original-title="Editar Contrato."
                                    style="cursor: pointer;"><img src="{{ asset('landing/images/search.svg') }}"
                                        height="18">
                                </a>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <input type="text" class="form-control" name="textAreaE" id="textContratoE" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="javascript:ModalCerrarCondicion();javascript:limpiarEditar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm" style="background-color:#163552;">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div id="condicionmodalE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="condicionmodalE"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar
                        Condición de Pago</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="javascript:ModalCerrarCondicion();javascript:limpiarEditar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:agregarCondicionA()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-2">
                                <label for="">Condición</label>
                            </div>
                            <div id="editarCondicionA" class="col-md-6"></div>
                            <div class="col-md-4">
                                <a id="buscarCondicionA" data-toggle="tooltip" data-placement="right"
                                    title="Editar Condición de Pago." data-original-title="Editar Condición de Pago."
                                    style="cursor: pointer;"><img src="{{asset('landing/images/search.svg')}}"
                                        height="18">
                                </a>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <input type="text" class="form-control" name="textCondicionE" id="textCondicionE" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="javascript:ModalCerrarCondicion();javascript:limpiarEditar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm" style="background-color:#163552;"
                        onclick="javascript:ModalCerrarCondicion();">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- DETALLES DE CONTRATO --}}
    <div id="contratoDetallesmodalE" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="contratoDetallesmodalE" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg d-flex justify-content-center">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Detalles de Contrato
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#form-ver').modal('show');javascript:limpiarEditar()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idContratoD">
                    <form action="javascript:editarDetalleCE()" onsubmit="javascript:validate(event)">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="sw-default">
                                        Contrato
                                    </label>
                                    <select class="form-control" name="v_contrato" id="v_contrato" tabindex="5" required
                                        disabled>
                                        <option value="">Seleccionar</option>
                                        @foreach ($tipo_cont as $tipo_conts)
                                        <option class="" value="{{ $tipo_conts->contrato_id }}">
                                            {{ $tipo_conts->contrato_descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-6" style="padding-top: 2.2em">
                                <div class="dropdown" id="documentosDetalles">
                                    <a class="dropdown" data-toggle="dropdown" aria-expanded="false"
                                        style="cursor: pointer">
                                        <span class="badge badge-soft-primary text-primary">
                                            <i class="uil-file-alt font-size-18"></i>
                                            &nbsp;
                                            Documentos
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                        id="documentosxDetalle">
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row pt-2 border-top">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sw-default">
                                                Condición Pago
                                                <a onclick="javascript:ModalAbiertoCondicion();" href="#condicionmodalE"
                                                    data-toggle="modal" data-target="#condicionmodalE"><i
                                                        class="uil uil-plus"></i>
                                                </a>
                                            </label>
                                            <select class="form-control" name="v_condicion" id="v_condicion" required>
                                                <option value="">Seleccionar</option>
                                                @foreach ($condicionP as $condicion)
                                                <option class="" value="{{$condicion->id}}">
                                                    {{$condicion->condicion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sw-default">Monto</label>
                                            <input type="number" step=".01" class="form-control" name="v_monto"
                                                id="v_monto">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="" style="font-weight: 600">Fecha Inicial</label>
                                        <span id="m_validFechaCIE" style="color: red;display: none;">
                                            *Fecha incorrecta.
                                        </span>
                                        <div class="alert alert-danger" role="alert" style="display: none;"
                                            id="alertErrorFecha"></div>
                                        <div class="row">
                                            <div class="col-4">
                                                <select class="form-control" name="m_dia_fechaIE" id="m_dia_fechaIE"
                                                    required="">
                                                    <option value="0">Día</option>
                                                    @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                        {{$i}}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-control" name="m_mes_fechaIE" id="m_mes_fechaIE"
                                                    required>
                                                    <option value="0">Mes</option>
                                                    <option class="" value="1">Enero</option>
                                                    <option class="" value="2">Febrero</option>
                                                    <option class="" value="3">Marzo</option>
                                                    <option class="" value="4">Abril</option>
                                                    <option class="" value="5">Mayo</option>
                                                    <option class="" value="6">Junio</option>
                                                    <option class="" value="7">Julio</option>
                                                    <option class="" value="8">Agosto</option>
                                                    <option class="" value="9">Septiembre</option>
                                                    <option class="" value="10">Octubre</option>
                                                    <option class="" value="11">Noviembre</option>
                                                    <option class="" value="12">Diciembre</option>
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-control" style="padding-left: 5px;
                                                        padding-right: 5px;" name="m_ano_fechaIE" id="m_ano_fechaIE"
                                                    required="">
                                                    <option value="0">Año</option>
                                                    @for ($i = 2000; $i <2100; $i++) <option class="" value="{{$i}}">
                                                        {{$i}}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 pt-1" id="validArchivoEdit" style="display: none;">
                                                <span style="color: red;">
                                                    *El tamaño supera el limite de 4 MB.
                                                </span>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group" style="margin-top: 14px;margin-bottom: 0px;">
                                                    <input type="file"
                                                        accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                                                        class="inputfile inputfile-1"
                                                        data-multiple-caption="{count} archivos seleccionados" multiple
                                                        id="fileDetalleE" size="4194304">
                                                    <label for="fileDetalleE">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17"
                                                            viewBox="0 0 20 17">
                                                            <path
                                                                d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z">
                                                            </path>
                                                        </svg>
                                                        <span class="iborrainputfile">Adjuntar archivo</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="" style="font-weight: 600">Fecha Final</label><br>
                                            <label for="" class="ocultarFechaIE">Fecha Indefinida</label>
                                            <input type="checkbox" id="checkboxFechaIE" name="checkboxFechaIE"
                                                class="ocultarFechaIE">
                                        </div>
                                        <div id="ocultarFechaE">
                                            <span id="m_validFechaCFE" style="color: red;display: none;">*Fecha
                                                incorrecta.</span>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <select class="form-control" name="m_dia_fechaFE"
                                                        id="m_dia_fechaFE">
                                                        <option value="0">Día</option>
                                                        @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                            {{$i}}</option>
                                                            @endfor
                                                    </select>
                                                </div>
                                                <div class="col-sm-4">
                                                    <select class="form-control" name="m_mes_fechaFE"
                                                        id="m_mes_fechaFE">
                                                        <option value="0">Mes</option>
                                                        <option class="" value="1">Enero</option>
                                                        <option class="" value="2">Febrero</option>
                                                        <option class="" value="3">Marzo</option>
                                                        <option class="" value="4">Abril</option>
                                                        <option class="" value="5">Mayo</option>
                                                        <option class="" value="6">Junio</option>
                                                        <option class="" value="7">Julio</option>
                                                        <option class="" value="8">Agosto</option>
                                                        <option class="" value="9">Septiembre</option>
                                                        <option class="" value="10">Octubre</option>
                                                        <option class="" value="11">Noviembre</option>
                                                        <option class="" value="12">Diciembre</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-4">
                                                    <select class="form-control" style="padding-left: 5px;
                                                            padding-right: 5px;" name="m_ano_fechaFE"
                                                        id="m_ano_fechaFE">
                                                        <option value="0">Año</option>
                                                        @for ($i = 2000; $i <2100; $i++) <option class=""
                                                            value="{{$i}}">
                                                            {{$i}}
                                                            </option>
                                                            @endfor
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pt-3 d-flex inline">
                                <label class="col-md-10 pt-1">¿Cuántos días antes que finalice su contrato, le gustaría que se le notifique?</label>
                                <select class="form-control col-2" name="noti_dia_fecha_editar" id="noti_dia_fecha_editar" required="">
                                    <option value="0">Día</option>
                                @for ($i = 1; $i <32; $i++) 
                                    <option class="" value="{{$i}}"> {{ $i }} </option>
                                @endfor
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#form-ver').modal('show');javascript:limpiarEditar()"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm" style="background-color:#163552;">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION --}}
    {{-- NUEVA ALTA --}}
    <div id="contratoDetallesmodalEN" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="contratoDetallesmodalEN" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg d-flex justify-content-center">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Dar de alta
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="javascript:limpiarNuevosDatosAlta();$('#form-ver').modal('show');">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="javascript:nuevaAltaEditar()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label for="sw-default">Contrato
                                        <a onclick="javascript:ModalAbiertoCondicion();$('#contratomodalE').modal('show');"
                                            data-toggle="modal">
                                            <i class="uil uil-plus" style="color: darkblue;cursor: pointer;"></i>
                                        </a>
                                    </label>
                                    <select class="form-control" name="v_contratoN" id="v_contratoN" tabindex="5"
                                        onclick="javascript:validacionNuevaAlta()" required>
                                        <option value="">Seleccionar</option>
                                        @foreach ($tipo_cont as $tipo_conts)
                                        <option class="" value="{{ $tipo_conts->contrato_id }}">
                                            {{ $tipo_conts->contrato_descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row pt-2 border-top">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sw-default">Condición Pago <a
                                                    onclick="javascript:ModalAbiertoCondicion();"
                                                    href="#condicionmodalE" data-toggle="modal"
                                                    data-target="#condicionmodalE"><i
                                                        class="uil uil-plus"></i></a></label>
                                            <select class="form-control" name="v_condicionN" id="v_condicionN" required>
                                                <option value="">Seleccionar</option>
                                                @foreach ($condicionP as $condicion)
                                                <option class="" value="{{$condicion->id}}">
                                                    {{$condicion->condicion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sw-default">Monto</label>
                                            <input type="number" step=".01" class="form-control" name="v_montoN"
                                                id="v_montoN">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="" style="font-weight: 600">Fecha Inicial</label>
                                        <span id="m_validFechaCIEN" style="color: red;display: none;">*Fecha
                                            incorrecta.</span>
                                        <div class="alert alert-danger" role="alert" style="display: none;"
                                            id="alertErrorFechaAlta"></div>
                                        <div class="row">
                                            <div class="col-4">
                                                <select class="form-control" name="m_dia_fechaIEN" id="m_dia_fechaIEN"
                                                    required="">
                                                    <option value="0">Día</option>
                                                    @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                        {{$i}}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-control" name="m_mes_fechaIEN" id="m_mes_fechaIEN"
                                                    required>
                                                    <option value="0">Mes</option>
                                                    <option class="" value="1">Enero</option>
                                                    <option class="" value="2">Febrero</option>
                                                    <option class="" value="3">Marzo</option>
                                                    <option class="" value="4">Abril</option>
                                                    <option class="" value="5">Mayo</option>
                                                    <option class="" value="6">Junio</option>
                                                    <option class="" value="7">Julio</option>
                                                    <option class="" value="8">Agosto</option>
                                                    <option class="" value="9">Septiembre</option>
                                                    <option class="" value="10">Octubre</option>
                                                    <option class="" value="11">Noviembre</option>
                                                    <option class="" value="12">Diciembre</option>
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-control" style="padding-left: 5px;
                                                        padding-right: 5px;" name="m_ano_fechaIEN" id="m_ano_fechaIEN"
                                                    required="">
                                                    <option value="0">Año</option>
                                                    @for ($i = 2000; $i <2100; $i++) <option class="" value="{{$i}}">
                                                        {{$i}}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 pt-1" id="validArchivoEditN" style="display: none;">
                                                <span style="color: red;">
                                                    *El tamaño supera el limite de 4 MB.
                                                </span>
                                            </div>
                                            <div class="col-md 12">
                                                <div class="form-group" style="margin-top: 14px;margin-bottom: 0px;">
                                                    <input type="file"
                                                        accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                                                        class="inputfile inputfile-1"
                                                        data-multiple-caption="{count} archivos seleccionados" multiple
                                                        id="fileArchivosNuevos" size="4194304">
                                                    <label for="fileArchivosNuevos">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17"
                                                            viewBox="0 0 20 17">
                                                            <path
                                                                d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z">
                                                            </path>
                                                        </svg>
                                                        <span class="iborrainputfile">Adjuntar archivo</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="" style="font-weight: 600">Fecha Final</label><br>
                                            <label for="">Fecha Indefinida</label>
                                            <input type="checkbox" id="checkboxFechaIEN" name="checkboxFechaIEN">
                                        </div>
                                        <div id="ocultarFechaEN">
                                            <span id="m_validFechaCFEN" style="color: red;display: none;">*Fecha
                                                incorrecta.</span>
                                            <div class="row">
                                                <div class="col-4">
                                                    <select class="form-control" name="m_dia_fechaFEN"
                                                        id="m_dia_fechaFEN">
                                                        <option value="0">Día</option>
                                                        @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                            {{$i}}</option>
                                                            @endfor
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <select class="form-control" name="m_mes_fechaFEN"
                                                        id="m_mes_fechaFEN">
                                                        <option value="0">Mes</option>
                                                        <option class="" value="1">Enero</option>
                                                        <option class="" value="2">Febrero</option>
                                                        <option class="" value="3">Marzo</option>
                                                        <option class="" value="4">Abril</option>
                                                        <option class="" value="5">Mayo</option>
                                                        <option class="" value="6">Junio</option>
                                                        <option class="" value="7">Julio</option>
                                                        <option class="" value="8">Agosto</option>
                                                        <option class="" value="9">Septiembre</option>
                                                        <option class="" value="10">Octubre</option>
                                                        <option class="" value="11">Noviembre</option>
                                                        <option class="" value="12">Diciembre</option>
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <select class="form-control" style="padding-left: 5px;
                                                            padding-right: 5px;" name="m_ano_fechaFEN"
                                                        id="m_ano_fechaFEN">
                                                        <option value="0">Año</option>
                                                        @for ($i = 2000; $i <2100; $i++) <option class=""
                                                            value="{{$i}}">
                                                            {{$i}}
                                                            </option>
                                                            @endfor
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="javascript:limpiarNuevosDatosAlta();$('#form-ver').modal('show');"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm" style="background-color:#163552;"
                        id="guardarAltaN">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION --}}
    {{-- NUEVO DETALLE --}}
    <div id="NuevoContratoDetallesmodalE" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="NuevoContratoDetallesmodalE" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg d-flex justify-content-center">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                        Dar de alta
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="javascript:limpiarNuevosDatosDetalle();$('#form-ver').modal('show');">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idHistoE">
                    <form action="javascript:nuevoDetalleC()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label for="sw-default">Contrato
                                        <a onclick="$('#NuevoContratoDetallesmodalE').modal('hide');$('#contratomodalE').modal('show');"
                                            data-toggle="modal"><i class="uil uil-plus"
                                                style="color: darkblue;cursor: pointer;"></i>
                                        </a>
                                    </label>
                                    <select class="form-control" name="v_contratoND" id="v_contratoND" tabindex="5"
                                        onclick="javascript:validacionNuevoDetalle()" required>
                                        <option value="">Seleccionar</option>
                                        @foreach ($tipo_cont as $tipo_conts)
                                        <option class="" value="{{ $tipo_conts->contrato_id }}">
                                            {{ $tipo_conts->contrato_descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row pt-2 border-top">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sw-default">Condición Pago
                                                <a onclick="javascript:ModalAbiertoCondicion();" href="#condicionmodalE"
                                                    data-toggle="modal" data-target="#condicionmodalE">
                                                    <i class="uil uil-plus"></i>
                                                </a>
                                            </label>
                                            <select class="form-control" name="v_condicionND" id="v_condicionND"
                                                required>
                                                <option value="">Seleccionar</option>
                                                @foreach ($condicionP as $condicion)
                                                <option class="" value="{{$condicion->id}}">
                                                    {{$condicion->condicion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sw-default">Monto</label>
                                            <input type="number" step=".01" class="form-control" name="v_montoND"
                                                id="v_montoND">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="" style="font-weight: 600">Fecha Inicial</label>
                                        <span id="m_validFechaCIEND" style="color: red;display: none;">*Fecha
                                            incorrecta.</span>
                                        <div class="row">
                                            <div class="col-4">
                                                <select class="form-control" name="m_dia_fechaIEND" id="m_dia_fechaIEND"
                                                    required>
                                                    <option value="0">Día</option>
                                                    @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                        {{$i}}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-control" name="m_mes_fechaIEND" id="m_mes_fechaIEND"
                                                    required>
                                                    <option value="0">Mes</option>
                                                    <option class="" value="1">Enero</option>
                                                    <option class="" value="2">Febrero</option>
                                                    <option class="" value="3">Marzo</option>
                                                    <option class="" value="4">Abril</option>
                                                    <option class="" value="5">Mayo</option>
                                                    <option class="" value="6">Junio</option>
                                                    <option class="" value="7">Julio</option>
                                                    <option class="" value="8">Agosto</option>
                                                    <option class="" value="9">Septiembre</option>
                                                    <option class="" value="10">Octubre</option>
                                                    <option class="" value="11">Noviembre</option>
                                                    <option class="" value="12">Diciembre</option>
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-control" style="padding-left: 5px;
                                                        padding-right: 5px;" name="m_ano_fechaIEND"
                                                    id="m_ano_fechaIEND" required="">
                                                    <option value="0">Año</option>
                                                    @for ($i = 2000; $i <2100; $i++) <option class="" value="{{$i}}">
                                                        {{$i}}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 pt-1" id="validArchivoEditND" style="display: none;">
                                                <span style="color: red;">
                                                    *El tamaño supera el limite de 4 MB.
                                                </span>
                                            </div>
                                            <div class="col-md 12">
                                                <div class="form-group" style="margin-top: 14px;margin-bottom: 0px;">
                                                    <input type="file"
                                                        accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                                                        class="inputfile inputfile-1"
                                                        data-multiple-caption="{count} archivos seleccionados" multiple
                                                        id="fileArchivosNuevosD" size="4194304">
                                                    <label for="fileArchivosNuevosD">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17"
                                                            viewBox="0 0 20 17">
                                                            <path
                                                                d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z">
                                                            </path>
                                                        </svg>
                                                        <span class="iborrainputfile">Adjuntar archivo</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="" style="font-weight: 600">Fecha Final</label><br>
                                            <label for="">Fecha Indefinida</label>
                                            <input type="checkbox" id="checkboxFechaIEND" name="checkboxFechaIEND">
                                        </div>
                                        <div id="ocultarFechaEND">
                                            <span id="m_validFechaCFEND" style="color: red;display: none;">*Fecha
                                                incorrecta.</span>
                                            <div class="row">
                                                <div class="col-4">
                                                    <select class="form-control" name="m_dia_fechaFEND"
                                                        id="m_dia_fechaFEND">
                                                        <option value="0">Día</option>
                                                        @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                            {{$i}}</option>
                                                            @endfor
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <select class="form-control" name="m_mes_fechaFEND"
                                                        id="m_mes_fechaFEND">
                                                        <option value="0">Mes</option>
                                                        <option class="" value="1">Enero</option>
                                                        <option class="" value="2">Febrero</option>
                                                        <option class="" value="3">Marzo</option>
                                                        <option class="" value="4">Abril</option>
                                                        <option class="" value="5">Mayo</option>
                                                        <option class="" value="6">Junio</option>
                                                        <option class="" value="7">Julio</option>
                                                        <option class="" value="8">Agosto</option>
                                                        <option class="" value="9">Septiembre</option>
                                                        <option class="" value="10">Octubre</option>
                                                        <option class="" value="11">Noviembre</option>
                                                        <option class="" value="12">Diciembre</option>
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <select class="form-control" style="padding-left: 5px;
                                                            padding-right: 5px;" name="m_ano_fechaFEND"
                                                        id="m_ano_fechaFEND">
                                                        <option value="0">Año</option>
                                                        @for ($i = 2000; $i <2100; $i++) <option class=""
                                                            value="{{$i}}">
                                                            {{$i}}
                                                            </option>
                                                            @endfor
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="javascript:limpiarNuevosDatosDetalle();$('#form-ver').modal('show');"
                        class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm" style="background-color:#163552;"
                        id="guardarAltaND">Guardar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION --}}
    {{-- DETALLES DE CONTRATO --}}
    <div id="fechasmodalVer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fechasmodalVer"
        aria-hidden=" true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Detalles de contrato
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#verEmpleadoDetalles').modal('show');">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="formContratoVer">
                    <form>
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label for="sw-default">
                                        Contrato
                                    </label>
                                    <select class="form-control" name="v_contratoV" id="v_contratoV" tabindex="5"
                                        required disabled>
                                        <option value="">Seleccionar</option>
                                        @foreach ($tipo_cont as $tipo_conts)
                                        <option class="" value="{{ $tipo_conts->contrato_id }}">
                                            {{ $tipo_conts->contrato_descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sw-default">Condición Pago</label>
                                    <select class="form-control" name="v_condicionV" id="v_condicionV" required
                                        disabled>
                                        <option value="">Seleccionar</option>
                                        @foreach ($condicionP as $condicion)
                                        <option class="" value="{{$condicion->id}}">
                                            {{$condicion->condicion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sw-default">Monto</label>
                                    <input type="number" step=".01" class="form-control" name="v_montoV" id="v_montoV"
                                        disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="" style="font-weight: 600">Fecha Inicial</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" name="m_dia_fechaIEV" id="m_dia_fechaIEV" disabled>
                                            <option value="0">Día</option>
                                            @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                {{$i}}
                                                </option>
                                                @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" name="m_mes_fechaIEV" id="m_mes_fechaIEV" disabled>
                                            <option value="0">Mes</option>
                                            <option class="" value="1">Enero</option>
                                            <option class="" value="2">Febrero</option>
                                            <option class="" value="3">Marzo</option>
                                            <option class="" value="4">Abril</option>
                                            <option class="" value="5">Mayo</option>
                                            <option class="" value="6">Junio</option>
                                            <option class="" value="7">Julio</option>
                                            <option class="" value="8">Agosto</option>
                                            <option class="" value="9">Septiembre</option>
                                            <option class="" value="10">Octubre</option>
                                            <option class="" value="11">Noviembre</option>
                                            <option class="" value="12">Diciembre</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" style="padding-left: 5px;
                                                    padding-right: 5px;" name="m_ano_fechaIEV" id="m_ano_fechaIEV"
                                            disabled>
                                            <option value="0">Año</option>
                                            @for ($i = 2000; $i <2100; $i++) <option class="" value="{{$i}}">
                                                {{$i}}
                                                </option>
                                                @endfor
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <label for="" style="font-weight: 600">Fecha Final</label><br>
                                    <label class="ocultarFechaIV">Fecha Indefinida</label>
                                    <input type="checkbox" class="ocultarFechaIV" id="checkboxFechaIEV"
                                        name="checkboxFechaIEV">
                                </div>
                                <div id="ocultarFechaEV">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select class="form-control" name="m_dia_fechaFEV" id="m_dia_fechaFEV"
                                                disabled>
                                                <option value="0">Día</option>
                                                @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                    {{$i}}</option>
                                                    @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-control" name="m_mes_fechaFEV" id="m_mes_fechaFEV"
                                                disabled>
                                                <option value="0">Mes</option>
                                                <option class="" value="1">Enero</option>
                                                <option class="" value="2">Febrero</option>
                                                <option class="" value="3">Marzo</option>
                                                <option class="" value="4">Abril</option>
                                                <option class="" value="5">Mayo</option>
                                                <option class="" value="6">Junio</option>
                                                <option class="" value="7">Julio</option>
                                                <option class="" value="8">Agosto</option>
                                                <option class="" value="9">Septiembre</option>
                                                <option class="" value="10">Octubre</option>
                                                <option class="" value="11">Noviembre</option>
                                                <option class="" value="12">Diciembre</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-control" style="padding-left: 5px;padding-right: 5px;"
                                                name="m_ano_fechaFEV" id="m_ano_fechaFEV" disabled>
                                                <option value="0">Año</option>
                                                @for ($i = 2000; $i <2100; $i++) <option class="" value="{{$i}}">{{$i}}
                                                    </option>
                                                    @endfor
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pt-3 d-flex inline">
                                <label class="col-md-10 pt-1">¿Cuántos días antes que finalice su contrato, le gustaría que se le notifique?</label>
                                <select class="form-control col-2" name="noti_dia_fecha_ver" id="noti_dia_fecha_ver">
                                    <option value="0">Día</option>
                                @for ($i = 1; $i <32; $i++) 
                                    <option class="" value="{{$i}}"> {{ $i }} </option>
                                @endfor
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#verEmpleadoDetalles').modal('show');" class="btn btn-sm"
                        style="background: #163552;" data-dismiss="modal">Cerrar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    {{-- FINALIZACION --}}
    <div class="modal fade" style="font-size: 13px" id="form-registrar" tabindex="-1" role="dialog"
        aria-labelledby="form-registrar" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" style="max-width: 850px;">
            <div class="modal-content">
                <div class="modal-header" style="background: #163552;">
                    <h4 class="header-title mt-0 " style="color: #f0f0f0"></i>Datos de empleado</h4>
                    <button type="button" class="close" id="cerrarModalEmpleado" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 0px">
                    <div class="setup-panel" id="smartwizard" style="background: #ffffff; color:#3d3d3d;">
                        <ul style="background: #fdfdfd!important;">
                            <li><a href="#sw-default-step-1">Personales</a></li>
                            <li><a href="#sw-default-step-2">Empresarial</a></li>
                            <li><a href="#sw-default-step-3">Contrato</a></li>
                            <li><a href="#sw-default-step-4">Foto</a></li>
                            <li><a href="#sw-default-step-5">Calendario</a></li>
                            <li><a href="#sw-default-step-6">Horario</a></li>
                            <li><a href="#sw-default-step-7">Actividades</a></li>
                            <li><a href="#sw-default-step-8">Dispositivo</a></li>
                        </ul>
                        <input type="hidden" id="estadoPR" value="false">
                        <input type="hidden" id="estadoPE" value="false">
                        <input type="hidden" id="estadoPF" value="false">
                        <input type="hidden" id="estadoPC" value="false">
                        <input type="hidden" id="estadoPH" value="false">
                        <div class="p-3">
                            <div id="sw-default-step-1" class="setup-content" style="font-size: 12px!important">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="float-md-right">
                                            <a onclick="javascript:mostrarContenido()" data-toggle="tooltip"
                                                data-placement="left" title="ver vídeo" data-original-title="ver vídeo">
                                                <img src="{{asset('landing/images/play.svg')}}" height="35">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="hidden" name="idEmpleado" id="idEmpleado">
                                        <label for="sw-default" class="label_txt" >Tipo Documento</label>
                                        <span id="validDocumento" style="color: red;">*Campo
                                            Obligatorio</span>
                                        <select class="form-control" placeholder="Tipo Documento " name="documento"
                                            id="documento" tabindex="1" required>
                                            <option value="">Seleccionar</option>
                                            @foreach ($tipo_doc as $tipo_docs)
                                            <option class="" value="{{ $tipo_docs->tipoDoc_id }}">
                                                {{ $tipo_docs->tipoDoc_descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_txt">Num. Documento</label>
                                        <span id="validNumDocumento" style="color: red;">*Campo
                                            Obligatorio</span>
                                        <span id="numR" style="color: red;">*Num. registrado</span>
                                        <input type="text" class="form-control" name="numDocumento"
                                            id="numDocumento" onkeypress="return isNumeric(event)"
                                            oninput="maxLengthCheck(this)" tabindex="2" required>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_txt">Fecha Nacimiento</label>
                                        <span id="validFechaC" style="color: red;display: none;">*Fecha incorrecta.</span>
                                        <div class="row fechasResponsive">
                                            <div class="col-md-4 prigth">
                                                <select class="form-control" name="dia_fecha" id="dia_fecha"
                                                    required="">
                                                    <option value="0">Día</option>
                                                    @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                        {{$i}}</option>
                                                        @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-4 prigth pleft">
                                                <select class="form-control" name="mes_fecha" id="mes_fecha"
                                                    required="">
                                                    <option value="0">Mes</option>
                                                    <option class="" value="1">Ene.</option>
                                                    <option class="" value="2">Feb.</option>
                                                    <option class="" value="3">Mar.</option>
                                                    <option class="" value="4">Abr.</option>
                                                    <option class="" value="5">May.</option>
                                                    <option class="" value="6">Jun.</option>
                                                    <option class="" value="7">Jul.</option>
                                                    <option class="" value="8">Ago.</option>
                                                    <option class="" value="9">Set.</option>
                                                    <option class="" value="10">Oct.</option>
                                                    <option class="" value="11">Nov.</option>
                                                    <option class="" value="12">Dic.</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 pAnio pleft">
                                                <select class="form-control" style="padding-left: 5px;
                                                padding-right: 5px;" name="ano_fecha" id="ano_fecha" required="">
                                                    <option value="0">Año</option>
                                                    @for ($i = 1950; $i <2011; $i++) <option class=""
                                                        value="{{$i}}">
                                                        {{$i}}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SEGUNDA FILA-->
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_txt">Apellido Paterno</label>
                                        <span id="validApPaterno" style="color: red;">*Campo Obligatorio</span>
                                        <input type="text" class="form-control" name="apPaterno" id="apPaterno" tabindex="4" required>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_txt">Apellido Materno</label>
                                        <span id="validApMaterno" style="color: red;">*Campo
                                            Obligatorio</span>
                                        <input type="text" class="form-control" name="apMaterno" id="apMaterno"
                                            tabindex="5" required>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_txt">Nombres</label>
                                        <span id="validNombres" style="color: red;">*Campo
                                            Obligatorio</span>
                                        <input type="text" class="form-control" name="nombres" id="nombres"
                                            tabindex="6" required>
                                    </div>

                                    <!-- TERCERA FILA -->
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_txt">Correo Electrónico</label>
                                        <span id="validCorreo" style="color: red;">*Campo
                                            Obligatorio</span>
                                        <span id="emailR" style="color: red;">*Correo registrado</span>
                                        <input type="email" class="form-control" id="email" name="email"
                                            tabindex="7">
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_txt">Celular</label>
                                        <span id="validCel" style="color: red;">*Número
                                            incorrecto.</span>
                                        <div class="row">
                                            <div class="col-4 pselect">
                                                <select class="form-control" id="codigoCelular">
                                                    <option value="+51" selected>+51</option>
                                                </select>
                                            </div>
                                            <div class="col-8">
                                                <input type="number" class="form-control" name="celular"
                                                    id="celular" tabindex="8" maxlength="9"
                                                    onkeypress="return isNumeric(event)"
                                                    oninput="maxLengthCheck(this)" pattern="/^9{1}|[0-9]{8,8}+">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_txt">Teléfono</label>
                                        <div class="row">
                                            <div class="col-4 pselect">
                                                <select class="form-control" id="codigoTelefono">
                                                    <option value="01" selected>01</option>
                                                    <option value="41">41</option>
                                                    <option value="43">43</option>
                                                    <option value="83">83</option>
                                                    <option value="54">54</option>
                                                    <option value="66">66</option>
                                                    <option value="76">76</option>
                                                    <option value="84">84</option>
                                                    <option value="67">67</option>
                                                    <option value="62">62</option>
                                                    <option value="56">56</option>
                                                    <option value="64">64</option>
                                                    <option value="44">44</option>
                                                    <option value="74">74</option>
                                                    <option value="65">65</option>
                                                    <option value="82">82</option>
                                                    <option value="53">53</option>
                                                    <option value="63">63</option>
                                                    <option value="73">73</option>
                                                    <option value="51">51</option>
                                                    <option value="42">42</option>
                                                    <option value="52">52</option>
                                                    <option value="72">72</option>
                                                    <option value="61">61</option>
                                                </select>
                                            </div>
                                            <div class="col-8">
                                                <input type="number" class="form-control" name="telefono"
                                                    id="telefono" tabindex="9" maxlength="9"
                                                    onkeypress="return isNumeric(event)"
                                                    oninput="maxLengthCheck(this)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="sw-default" class="label_txt">Dirección</label>
                                            <input type="text" class="form-control" name="direccion" id="direccion"
                                                tabindex="10" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_txt">Dirección Domiciliara</label>
                                        <select class="form-control" placeholder="Departamento" name="departamento"
                                            id="dep" tabindex="11" required>
                                            <option value="">Departamento</option>
                                            @foreach ($departamento as $departamentos)
                                            <option class="" value="{{ $departamentos->id }}">
                                                {{ $departamentos->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>  
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_select"><br></label>
                                        <select class="form-control " placeholder="Provincia " name="provincia"
                                            id="prov" tabindex="12" required>
                                            <option value="">Provincia</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_select"><br></label>
                                        <select class="form-control " placeholder="Distrito " name="distrito"
                                            id="dist" tabindex="13" required>
                                            <option value="">Distrito</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_txt">Lugar Nacimiento</label>
                                        <select class="form-control" placeholder="Departamento" name="departamento"
                                            id="departamento" tabindex="14" required>
                                            <option value="">Departamento</option>
                                            @foreach ($departamento as $departamentos)
                                            <option class="" value="{{ $departamentos->id }}">
                                                {{ $departamentos->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                       <label for="sw-default" class="label_select"><br></label>
                                        <select class="form-control " placeholder="Provincia " name="provincia"
                                            id="provincia" tabindex="15" required>
                                            <option value="">Provincia</option>
                                        </select> 
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="sw-default" class="label_select"><br></label>
                                        <select class="form-control " placeholder="Distrito " name="distrito"
                                            id="distrito" tabindex="16" required>
                                            <option value="">Distrito</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="" class="label_txt">Género</label>
                                        <div class="row">
                                            <div class="radio_rsp">
                                                <span id="validGenero" style="color: red;">*Campo Obligatorio</span>
                                                <label class="custom-control custom-radio">
                                                    <input type="radio" name="tipo" id="tipo" value="Femenino" required> Femenino
                                                </label>
                                            </div>
                                            <div class="radio_rsp">
                                                <label class="custom-control custom-radio">
                                                    <input type="radio" name="tipo" id="tipo" value="Masculino" required> Masculino
                                                </label>
                                            </div>
                                            <div class="radio_rsp">
                                               <label class="custom-control custom-radio" data-toggle="tooltip" data-placement="right" title="" data-original-title="Puedes elegir personalizado si no deseas especificar tu género.">
                                                <input type="radio" name="tipo" id="tipo" value="Personalizado" required>
                                                Personalizado
                                            </label> 
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end row -->
                            </div>
                            <div id="sw-default-step-2" class="setup-content" style="font-size: 12px!important">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="float-md-right">
                                            <a onclick="javascript:mostrarContenidoE()" data-toggle="tooltip"
                                                data-placement="left" title="ver vídeo" data-original-title="ver vídeo">
                                                <img src="{{asset('landing/images/play.svg')}}" height="40">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="sw-default">Cargo <a
                                                    onclick="$('#form-registrar').modal('hide');" href="#cargomodal"
                                                    data-toggle="modal" data-target="#cargomodal"><i
                                                        class="uil uil-plus"></i></a></label>
                                            <select class="form-control" name="cargo" id="cargo" tabindex="2">
                                                <option value="">Seleccionar</option>
                                                @foreach ($cargo as $cargos)
                                                <option class="" value="{{ $cargos->cargo_id }}">
                                                    {{ $cargos->cargo_descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Codigo Empleado</label>
                                            <input type="text" class="form-control" name="codigoEmpleado"
                                                id="codigoEmpleado" tabindex="1"
                                                onfocus="javascript:valorCodigoEmpleado()" data-toggle="tooltip"
                                                data-placement="right" maxlength="200"
                                                title="Número de documento por defecto o Ingrese un código interno"
                                                data-original-title="Número de documento por defecto o Ingrese un código interno">
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="sw-default">Área <a
                                                    onclick="$('#form-registrar').modal('hide');" href="#areamodal"
                                                    data-toggle="modal" data-target="#areamodal"><i
                                                        class="uil uil-plus"></i></a></label>
                                            <select class="form-control" name="area" id="area" tabindex="3">
                                                <option value="">Seleccionar</option>
                                                @foreach ($area as $areas)
                                                <option class="" value="{{ $areas->area_id }}">
                                                    {{ $areas->area_descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default" class="labelNivel">Nivel del Colaborador<a
                                                    onclick="$('#form-registrar').modal('hide');" href="#nivelmodal"
                                                    data-toggle="modal" data-target="#nivelmodal"><i
                                                        class="uil uil-plus"></i></a></label>
                                            <select class="form-control" name="nivel" id="nivel" tabindex="6">
                                                <option value="">Seleccionar</option>
                                                @foreach ($nivel as $niveles)
                                                <option class="" value="{{ $niveles->nivel_id }}">
                                                    {{ $niveles->nivel_descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="sw-default">Centro Costo <a
                                                    onclick="$('#form-registrar').modal('hide');" href="#centrocmodal"
                                                    data-toggle="modal" data-target="#centrocmodal"><i
                                                        class="uil uil-plus"></i></a></label>
                                            <select class="form-control" name="centroc" id="centroc" tabindex="4">
                                                <option value="">Seleccionar</option>
                                                @foreach ($centro_costo as $centro_costos)
                                                <option class="" value="{{ $centro_costos->centroC_id }}">
                                                    {{ $centro_costos->centroC_descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="sw-default">Local <a
                                                    onclick="$('#form-registrar').modal('hide');" href="#localmodal"
                                                    data-toggle="modal" data-target="#localmodal"><i
                                                        class="uil uil-plus"></i></a></label>
                                            <select class="form-control" name="local" id="local" tabindex="7">
                                                <option value="">Seleccionar</option>
                                                @foreach ($local as $locales)
                                                <option class="" value="{{ $locales->local_id }}">
                                                    {{ $locales->local_descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->

                            </div>
                            <div id="sw-default-step-3" class="setup-content" style="font-size: 12px!important">
                                {{-- CONTENIDO DE INICIO --}}
                                <div class="row pb-2" id="reg_nuevaAlta">
                                    {{-- VALIDACION PARA REGISTRAR NUEVA ALTA OBLIGATORIA --}}
                                    <div class="col-xl-12" style="display: none" id="reg_validHE">
                                        <span style="color: red;">
                                            *Campo Obligatorio
                                        </span>
                                    </div>
                                    {{-- BOTON DE NUEVA ALTA --}}
                                    <div class="col-xl-2 text-left">
                                        <button type="button" class="btn btn-sm mt-1" style="background-color: #383e56;"
                                            onclick="$(this).prop('disabled', true);javascript:modalNuevaAltaReg()">
                                            + Nueva alta
                                        </button>
                                    </div>
                                </div>
                                {{-- FINALIZACION --}}
                                {{-- CONTENIDO DE LA TABLA --}}
                                <div class="row">
                                    <div class="col-xl-12 col-sm-12">
                                        <div class="table-responsive-sm" style="height: 250px;overflow: auto;">
                                            <table class="table table-hover" id="reg_tablaHistorial"
                                                style="font-size: 13px!important;">
                                                <thead style="background: #fafafa;">
                                                    <tr>
                                                        <th>
                                                            Fecha de alta
                                                            &nbsp;
                                                            <img src="{{asset('landing/images/arriba.svg')}}"
                                                                height="17">
                                                        </th>
                                                        <th>
                                                            Fecha de baja
                                                            &nbsp;
                                                            <img src="{{asset('landing/images/abajo.svg')}}"
                                                                height="17">
                                                        </th>
                                                        <th>Contrato</th>
                                                        <th>Documento</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="reg_tbodyHistorial"
                                                    style="background:#ffffff;color: #585858;font-size: 12px">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{-- FINALIZACION --}}
                            </div>
                            <div id="sw-default-step-4" class="setup-content" style="font-size: 12px!important">
                                <div class="col-12 pb-2">
                                    <div class="float-md-right">
                                        <a onclick="javascript:mostrarContenidoF()" data-toggle="tooltip"
                                            data-placement="left" title="ver vídeo" data-original-title="ver vídeo">
                                            <img src="{{asset('landing/images/play.svg')}}" height="40">
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <input type="file" name="file" id="file" accept="image/*">
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->

                            </div>
                            <div id="sw-default-step-5" class="setup-content" style="font-size: 12px!important">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="float-md-right">
                                            <a onclick="javascript:mostrarContenidoC()" data-toggle="tooltip"
                                                data-placement="left" title="ver vídeo" data-original-title="ver vídeo">
                                                <img src="{{asset('landing/images/play.svg')}}" height="40">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @if (count($calendario) === 0)
                                        <div class="col-md-12 text-center">
                                            <h5>No existe calendarios registrados</h5>
                                        </div>
                                        <div style="display: none">
                                            <div class="col-md-12" id="calendarInv" style="display: none!important">
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="form-group row">
                                        <div class="col-md-1"></div>
                                        <label style="font-weight: 600;font-size: 14px;" class="col-lg-3 col-form-label"
                                            for="simpleinput">Calendario
                                            de empleado:</label>
                                        <div class="col-lg-5">
                                            <span id="vallidCalend" style="color: red;display:none">Eliga
                                                calendario</span>
                                            <select name="" id="selectCalendario"
                                                class="form-control col-lg-6 form-control-sm" style="margin-top: 4px;">
                                                <option hidden selected>Asignar calendario</option>
                                                @foreach ($calendario as $calendarios)
                                                <option class="" value="{{ $calendarios->calen_id }}">
                                                    {{ $calendarios->calendario_nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2" id="opborrar" style="display: none">

                                            <div class="btn-group mt-2 mr-1">
                                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle"
                                                    style="color: #fff;
                                                    background-color: #4a5669;
                                                    border-color: #485263;" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false"><img
                                                        src="{{ asset('admin/images/borrador.svg') }}" height="15">
                                                    Borrar <i class="icon"><span
                                                            data-feather="chevron-down"></span></i></button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" onclick="vaciardFeria()">Dias
                                                        feriados</a>
                                                    <a class="dropdown-item" onclick="vaciarddescanso()">Dias
                                                        descanso</a>
                                                    {{-- <a class="dropdown-item"
                                                            onclick="vaciardlabTem()">D.
                                                            laborables</a> --}}
                                                    <a class="dropdown-item" onclick="vaciardNlabTem()">D. no
                                                        laborables</a>
                                                    <a class="dropdown-item" onclick="vaciardIncidTem()">Incidencia</a>

                                                </div>
                                            </div><!-- /btn-group -->
                                        </div>

                                    </div>

                                </div>
                                <div class="col-md-1"></div>
                                <div class="col-md-10" id="calendarInv"></div>
                                @endif
                                <input type="hidden" id="pruebaEnd">
                                <input type="hidden" id="pruebaStar">
                                <div class="col-md-10" id="calendar" style="display: none"></div>
                                <div class="col-md-1"></div>
                                <div id="calendarioAsignar" class="modal fade" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                                    <div class="modal-dialog  modal-lg d-flex justify-content-center "
                                        style="max-width:670px;  margin-top: 150px; left:0px;">

                                        <div class="modal-content">

                                            <div class="modal-body"
                                                style="font-size:12px!important;background: #f3f3f3;">
                                                <div class="col-md-12">
                                                    <div class="row" style="padding: 0 5px">
                                                        <div class="col-md-3 text-center mt-1 mb-1">
                                                            <button type="button" style=" width: 20em!important; height: 43px !important;"
                                                                class="btn btn-secondary btn-sm"
                                                                onclick="laborableTem()"><img
                                                                    src="{{ asset('admin/images/dormir.svg') }}"
                                                                    height="20"> Descanso</button>
                                                        </div>
                                                        <div class="col-md-3 text-center mt-1 mb-1">
                                                            <button type="button" style=" width: 20em!important; height: 43px !important;"
                                                                class="btn btn-secondary btn-sm"
                                                                onclick="nolaborableTem()"><img
                                                                    src="{{ asset('admin/images/evento.svg') }}"
                                                                    height="20"> Día no laborable</button>
                                                        </div>
                                                        <div class="col-md-3 text-center mt-1 mb-1">
                                                            <button type="button" style=" width: 20em!important; height: 43px !important;"
                                                                class="btn btn-secondary btn-sm"
                                                                onclick="$('#nombreFeriado').val('');$('#calendarioAsignar').modal('hide'); $('#myModalFeriado').modal('show')"><img
                                                                    src="{{ asset('admin/images/calendario.svg') }}"
                                                                    height="20"> Día feriado</button>
                                                        </div>
                                                        <div class="col-md-3 text-center mt-1 mb-1">
                                                            {{-- <button type="button"
                                                                    style=" max-width: 18em!important;"
                                                                    class="btn btn-secondary btn-sm "
                                                                    onclick="registrarDdescanso()"><img
                                                                        src="{{ asset('admin/images/calendarioInc.svg') }}"
                                                            height="20"> Incidencia</button>
                                                            --}}
                                                            <button style=" width: 20em!important; height: 43px !important;"
                                                                class="btn btn-secondary btn-sm"
                                                                onclick="agregarinciden()"><img
                                                                    src="{{ asset('admin/images/calendarioInc.svg') }}"
                                                                    height="20"> Incidencia</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer"
                                                style="padding-top: 5px; padding-bottom: 5px;background: #f1f0f0;">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-12 text-right">
                                                            <button type="button" class="btn btn-soft-primary btn-sm "
                                                                onclick="$('#calendarioAsignar').modal('hide')">Cancelar</button>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                                <div id="myModalFeriado" class="modal fade" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #163552;">
                                                <h5 class="modal-title" id="myModalLabel"
                                                    style="color:#ffffff;font-size:15px">Agregar nuevo feriado</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <div class="col-md-6">
                                                            <label for="">Nombre de día feriado:</label>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <form action="javascript:diaferiadoTem()">
                                                                <input class="form-control" type="text"
                                                                    id="nombreFeriado" required>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light btn-sm" onclick="$('#myModalFeriado').modal('hide')">Cancelar</button>
                                                <button type="submit" class="btn btn-secondary btn-sm">Aceptar</button>
                                                </form>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            </div>
                        </div>
                        <div id="sw-default-step-6" class="setup-content" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="float-md-right">
                                        <a onclick="javascript:mostrarContenidoH()" data-toggle="tooltip"
                                            data-placement="left" title="ver vídeo" data-original-title="ver vídeo">
                                            <img src="{{asset('landing/images/play.svg')}}" height="40">
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-12 text-center" id="detallehorario"></div>
                                <div class="col-md-1"><br></div>
                                <div class="col-md-10" id="mensajeOc"><label for="">Aún no ha
                                        seleccionado un calendario en el paso anterior.</label></div>
                                <div class="col-md-10" id="calendar2" style="display: none"></div>
                                <div class="col-md-1"><br></div>
                            </div>
                            <div id="horarioAsignar" class="modal fade" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                                <div class="modal-dialog  modal-lg d-flex justify-content-center "
                                    style="max-width: 400px;margin-top: 150px;">
                                    <div class="modal-content">
                                        <div class="modal-body" style="font-size:12px!important;background: #f3f3f3;">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <span id="errorSel_re"
                                                            style="color: #8b3a1e;display:none">Seleccione un
                                                            horario</span>
                                                        <select data-plugin="customselect"
                                                            class="form-control custom-select custom-select-sm"
                                                            name="selectHorario" id="selectHorario">
                                                            <option hidden selected disabled>Seleccionar horario
                                                            </option>
                                                            @foreach ($horario as $horarios)
                                                            <option class="" value="{{$horarios->horario_id}}">
                                                                {{$horarios->horario_descripcion}} <span
                                                                    style="font-size: 11px;font-style: oblique">({{$horarios->horaI}}-{{$horarios->horaF}})</span>
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 text-left" style="padding-left: 0px;">
                                                        {{-- <button class="btn btn-primary btn-sm"
                                                            style="background-color: #183b5d;border-color:#62778c;margin-top: 5px"
                                                            onclick="abrirHorario()">+</button> --}}
                                                    </div>
                                                    <div class="col-md-12"><br>
                                                        <div class="custom-control custom-switch mb-2">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="fueraHSwitch_re">
                                                            <label class="custom-control-label"
                                                                for="fueraHSwitch_re">Trabajar fuera de horario</label>
                                                        </div>
                                                        {{--  <div class="custom-control custom-switch mb-2">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="horCompSwitch_re">
                                                        <label class="custom-control-label"
                                                            for="horCompSwitch_re">Horario compensable.</label>
                                                    </div> --}}
                                                        <div class="row">
                                                            <div class="custom-control custom-switch mb-2"
                                                                style="left: 12px;">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="horAdicSwitch_re">
                                                                <label class="custom-control-label"
                                                                    for="horAdicSwitch_re">Permite marcar horas
                                                                    adicionales.</label>

                                                            </div> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <select id="nHorasAdic_re"
                                                                style="display: none;bottom: 3px;"
                                                                class="form-control form-control-sm col-md-3">
                                                                <option value="0.5">0.5 hora </option>
                                                                <option value="1">1 hora </option>
                                                                <option value="2">2 horas </option>
                                                                <option value="3">3 horas </option>
                                                                <option value="4">4 horas </option>
                                                                <option value="5">5 horas </option>
                                                                <option value="6">6 horas </option>
                                                                <option value="7">7 horas </option>
                                                                <option value="8">8 horas </option>
                                                                <option value="9">9 horas </option>
                                                                <option value="10">10 horas </option>
                                                                <option value="11">11 horas </option>
                                                                <option value="12">12 horas </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer"
                                            style="padding-top: 5px; padding-bottom: 5px;background: #f1f0f0;">
                                            <div class="col-md-12 text-right" style="padding-right: 0px;">
                                                <button type="button" class="btn btn-light  btn-sm " style="background: #f3f3f3;
                                                border-color: #f3f3f3;"
                                                    onclick="$('#horarioAsignar').modal('hide')">Cancelar</button>
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    style="background-color: #183b5d;border-color:#62778c;"
                                                    onclick="agregarHorarioSe_regis()">Registrar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                            <div id="horarioAgregar" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
                                aria-hidden="true" data-backdrop="static">
                                <div class="modal-dialog  modal-lg d-flex justify-content-center "
                                    style="width: 600px;">

                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:#163552;">
                                            <h5 class="modal-title" id="myModalLabel"
                                                style="color:#ffffff;font-size:15px">
                                                Asignar horario</h5>
                                            <button type="button" class="close"
                                                onclick="$('#horarioAgregar').modal('hide')" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" style="font-size:12px!important">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <form id="frmHor" action="javascript:registrarHorario()">
                                                        <div class="row">

                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="">Descripción del horario:</label>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm"
                                                                        id="descripcionCa" maxlength="60" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Hora de
                                                                        inicio(24h):</label>
                                                                    <input type="text" id="horaI"
                                                                        class="form-control form-control-sm" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Hora de fin(24h):</label>
                                                                    <input type="text" id="horaF"
                                                                        class="form-control form-control-sm" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Horas obligadas:</label>
                                                                    <div class="input-group form-control-sm" style="bottom: 4px;
                                                                   padding-left: 0px; padding-right: 0px;">

                                                                        <input type="text"
                                                                            class="form-control form-control-sm"
                                                                            id="horaOblig" required>
                                                                        <div class="input-group-prepend ">
                                                                            <div class="input-group-text form-control-sm"
                                                                                style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                                                Horas</div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Tolerancia al ingreso(Min):</label>
                                                                    <div class="input-group form-control-sm " style="bottom: 4px;
                                                                    padding-left: 0px; padding-right: 0px;">
                                                                        <input type="number" value="0"
                                                                            class="form-control form-control-sm" min="0"
                                                                            max="59" id="toleranciaH" required>
                                                                        <div class="input-group-prepend  ">
                                                                            <div class="input-group-text form-control-sm "
                                                                                style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                                                Minutos</div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Tolerancia a la salida(Min):</label>
                                                                    <div class="input-group form-control-sm " style="bottom: 4px;
                                                                   padding-left: 0px; padding-right: 0px;">
                                                                        <input type="number" value="0"
                                                                            class="form-control form-control-sm" min="0"
                                                                            max="59" id="toleranciaSalida" required>
                                                                        <div class="input-group-prepend  ">
                                                                            <div class="input-group-text form-control-sm "
                                                                                style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                                                Minutos</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4" id="divOtrodia" style="display: none">
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        id="otroDCh" checked disabled>
                                                                    <label class="form-check-label" for="otroDCh"
                                                                        style="margin-top: 2px;font-weight: 700">La hora
                                                                        fin de este horario pertenece al siguiente
                                                                        día.</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="custom-control custom-switch mb-2">
                                                                    <input type="checkbox" class="custom-control-input"
                                                                        id="SwitchTardanza">
                                                                    <label class="custom-control-label"
                                                                        for="SwitchTardanza"
                                                                        style="font-weight: bold;padding-top: 1px">Controlar
                                                                        tardanza</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="custom-control custom-switch mb-2">
                                                                    <input type="checkbox" class="custom-control-input"
                                                                        id="SwitchPausa">
                                                                    <label class="custom-control-label"
                                                                        for="SwitchPausa"
                                                                        style="font-weight: bold;padding-top: 1px">Pausas
                                                                        en
                                                                        el horario</label> &nbsp; <span id="fueraRango"
                                                                        style="color: #80211e;display: none">Hora no
                                                                        esta
                                                                        dentro de rango de horario</span> <span
                                                                        id="errorenPausas"
                                                                        style="color: #80211e;display: none">- Fin de
                                                                        pausa
                                                                        debe ser mayor a inicio pausa</span>

                                                                </div>
                                                            </div>
                                                            <div id="divPausa" class="col-md-12" style="display: none">
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <label for=""
                                                                                style="font-weight:600">Descripción</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label for="" style="font-weight:600">Inicio
                                                                                pausa(24h)</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label for="" style="font-weight:600">Fin
                                                                                pausa(24h)</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="inputPausa">

                                                                </div>

                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-12 text-right">
                                                        <button type="button" class="btn btn-light btn-sm "
                                                            onclick="$('#horarioAgregar').modal('hide')">Cancelar</button>
                                                        <button type="submit" name="" style="background-color: #163552;"
                                                            class="btn btn-sm ">Guardar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                        </div>
                        <div id="sw-default-step-7" class="setup-content" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <div class="row">
                                                <div class="col-xl-12 text-left">
                                                    <div class="float-md-right">
                                                        <a onclick="javascript:mostrarContenidoA()"
                                                            data-toggle="tooltip" data-placement="left"
                                                            title="ver vídeo" data-original-title="ver vídeo">
                                                            <img src="{{asset('landing/images/play.svg')}}" height="40">
                                                        </a>
                                                    </div>
                                                    <button type="button" class="btn btn-sm mt-1"
                                                        style="background-color: #383e56;"
                                                        onclick="$('#regactividadTarea').modal();">+
                                                        Asignar actividad
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row pt-3">
                                                <div class="col-xl-12 col-sm-12">
                                                    <div class="table-responsive-sm"
                                                        style="height: 250px;overflow: auto;">
                                                        <table class="table table-hover"
                                                            style="font-size: 13px!important;">
                                                            <thead style="background: #fafafa;font-size: 14px">
                                                                <tr>
                                                                    <th>Actividad</th>
                                                                    <th class="text-center th_rh">
                                                                        <i data-feather="activity"
                                                                            style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                                                        &nbsp;
                                                                        Control Remoto
                                                                    </th>
                                                                    <th class="text-center th_rh">
                                                                        <i data-feather="map-pin"
                                                                            style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                                                        &nbsp;
                                                                        Control Ruta
                                                                    </th>
                                                                    <th>Estado</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="regtablaBodyTarea"
                                                                style="background:#ffffff;color: #585858;font-size: 12px">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="regactividadTarea" class="modal fade" tabindex="-1" role="dialog"
                                aria-labelledby="regactividadTarea" aria-hidden="true" data-backdrop="static">
                                <div class="modal-dialog  modal-lg d-flex justify-content-center "
                                    style="max-width: 550px;">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:#163552;">
                                            <h5 class="modal-title" id="myModalLabel"
                                                style="color:#ffffff;font-size:15px">
                                                Asignar Actividad
                                            </h5>
                                        </div>
                                        <div class="modal-body" style="font-size:12px!important">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <form action="javascript:registrarNuevaActividadTarea()"
                                                        id="formActvidadesReg">
                                                        <div class="row justify-content-center">
                                                            <div class="col-xl-12">
                                                                <label style="font-size: 14px">
                                                                    <i data-feather="layers"
                                                                        style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                                                    &nbsp;
                                                                    Actividades
                                                                </label>
                                                                <a class="mr-3" data-toggle="tooltip"
                                                                    data-placement="right"
                                                                    title="registrar nueva actividad"
                                                                    data-original-title="registrar nueva actividad"
                                                                    onclick="$('#form-registrar').modal('hide');$('#ActividadTareaGE').modal();">
                                                                    <i class="uil uil-plus"
                                                                        style="color: darkblue;cursor: pointer;font-weight: bold;font-size: 13px"></i>
                                                                </a>
                                                                <select multiple="multiple" data-plugin="customselect"
                                                                    class="form-control" multiple="multiple"
                                                                    id="regEmpleadoActiv"></select>
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-12 text-right">
                                                        <button type="button" class="btn btn-light btn-sm "
                                                            onclick="$('#regactividadTarea').modal('toggle');javascript:limpiarSelect()">Cancelar</button>
                                                        <button type="submit" name="" style="background-color: #163552;"
                                                            class="btn btn-sm ">Guardar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                        </div>
                        <div id="sw-default-step-8" class="setup-content" style="font-size: 12px!important">
                            <div class="row pb-3">
                                <div class="col-xl-12 col-sm-12">
                                    <button class="btn btn-sm dropdown-toggle mb-2" style="background-color:#383e56;"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">+
                                        Agregar
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <a class="dropdown-item" id="agregarWindows">WINDOWS</a>
                                        <a class="dropdown-item" id="agregarAndroid">ANDROID</a>
                                    </div>
                                    <div class="float-md-right">
                                        <a onclick="javascript:mostrarContenidoD()" data-toggle="tooltip"
                                            data-placement="left" title="ver vídeo" data-original-title="ver vídeo">
                                            <img src="{{asset('landing/images/play.svg')}}" height="40">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card border"
                                        style="border-radius: 5px;border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                                        <div class="card-header"
                                            style="padding: 0.2rem 1rem;background: #383e56!important;color:white !important;border-top-right-radius: 5px; border-top-left-radius: 5px;">
                                            <div class="row pb-1 pl-2">
                                                <div class="col">
                                                    <div class="custom-control custom-switch mb-2">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="SwitchCR1">
                                                        <label class="custom-control-label" for="SwitchCR1"
                                                            style="font-weight: bold">
                                                            <i data-feather="activity"
                                                                style="height: 15px !important;width: 15px !important;color:white !important">
                                                            </i>&nbsp;&nbsp;
                                                            Modo Control Remoto
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3" id="bodyModoControlRR">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table id="tablaDispositivo" class="table table-hover"
                                                            style="font-size: 13px!important;">
                                                            <thead style="background: #fafafa;">
                                                                <tr>
                                                                    <th>Dispositivo</th>
                                                                    <th>Nombre</th>
                                                                    <th>Activación</th>
                                                                    <th>Codigo</th>
                                                                    <th>Enviado</th>
                                                                    <th>Estado</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbodyDispositivo"
                                                                style="background:#ffffff;color: #585858;font-size: 12px">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card border"
                                        style="border-radius: 5px;border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                                        <div class="card-header"
                                            style="padding: 0.2rem 1rem;background: #383e56!important;color:white !important;border-top-right-radius: 5px; border-top-left-radius: 5px;">
                                            <div class="row pb-1 pl-2">
                                                <div class="col">
                                                    <div class="custom-control custom-switch mb-2">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="SwitchCR2">
                                                        <label class="custom-control-label" for="SwitchCR2"
                                                            style="font-weight: bold">
                                                            <i data-feather="map-pin"
                                                                style="height: 15px !important;width: 15px !important;color:white !important">
                                                            </i>&nbsp;&nbsp;
                                                            Modo Control en Ruta
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3" id="bodyModoControlAR">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table id="tablaDispositivoA" class="table table-hover"
                                                            style="font-size: 13px!important;width: 100% !important">
                                                            <thead
                                                                style="background: #fafafa;font-size: 14px;width: 100% !important">
                                                                <tr>
                                                                    <th>Dispositivo</th>
                                                                    <th>Nombre</th>
                                                                    <th>Codigo</th>
                                                                    <th>Número</th>
                                                                    <th>Actividad (%)</th>
                                                                    <th>Enviado</th>
                                                                    <th>Estado</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbodyDispositivoAR"
                                                                style="background:#ffffff;color: #585858;font-size: 12px;width: 100% !important">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- FINALIZACION --}}
</div>
<div class="modal fade" id="form-ver" style="font-size: 13px" tabindex="-1" role="dialog" aria-labelledby="form-ver"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" style="max-width: 850px;">
        <div class="modal-content">
            <div class="modal-header" style="background: #163552;">
                <h4 class="header-title mt-0 " style="color: #f0f0f0">Datos de empleado</h4><br>
                <button type="button" class="close" id="cerrarEd" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0px;">
                <div id="smartwizard1" style="background: #ffffff; color:#3d3d3d;">
                    <ul style="background: #fdfdfd!important;">
                        <li><a href="#persona-step-1">Personales</a></li>
                        <li><a href="#swE-default-step-2">Empresarial</a></li>
                        <li><a href="#sw-default-step-3">Contrato</a></li>
                        <li><a href="#swF-default-step-4">Foto</a></li>
                        <li><a href="#sw-default-step-5">Calendario</a></li>
                        <li><a href="#sw-default-step-6">Horario</a></li>
                        <li><a href="#sw-default-step-7">Actividades</a></li>
                        <li><a href="#sw-default-step-8">Dispositivo</a></li>
                    </ul>
                    <input type="hidden" id="estadoP" value="false">
                    <input type="hidden" id="estadoE" value="false">
                    <input type="hidden" id="estadoF" value="false">
                    <div class="p-3" id="form-registrar">
                        <div id="persona-step-1" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-12">
                                    <div class="float-md-right">
                                        <a onclick="javascript:mostrarContenido()" data-toggle="tooltip"
                                            data-placement="left" title="ver vídeo" data-original-title="ver vídeo">
                                            <img src="{{asset('landing/images/play.svg')}}" height="35">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                               <div class="col-sm-4">
                                    <input style="display: none;" name="v_id" id="v_id">
                                    <label for="sw-default" class="label_txt">Tipo Documento</label>
                                    <input type="text" class="form-control" name="v_tipoDoc" id="v_tipoDoc" disabled
                                        style="background-color: #fcfcfc;" tabindex="1">
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default" class="label_txt">Num. Documento</label>
                                    <span id="v_validNumDocumento" style="color: red;">*Campo Obligatorio</span>
                                    <input type="text" class="form-control" name="v_numDocumento" id="v_numDocumento" required disabled style="background-color: #fcfcfc;" tabindex="2">
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default" class="label_txt">Fecha Nacimiento</label>
                                    <span id="v_validFechaC" style="color: red;display: none;">
                                        *Fecha incorrecta.
                                    </span>
                                    <div class="row fechasResponsive">
                                        <div class="col-md-4 prigth">
                                            <select class="form-control" name="v_dia_fecha" id="v_dia_fecha"
                                                required="">
                                                <option value="0">Día</option>
                                                @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">{{$i}}
                                                    </option>
                                                    @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-4 prigth pleft">
                                            <select class="form-control" name="v_mes_fecha" id="v_mes_fecha"
                                                required="">
                                                <option value="0">Mes</option>
                                                <option class="" value="1">Ene.</option>
                                                <option class="" value="2">Feb.</option>
                                                <option class="" value="3">Mar.</option>
                                                <option class="" value="4">Abr.</option>
                                                <option class="" value="5">May.</option>
                                                <option class="" value="6">Jun.</option>
                                                <option class="" value="7">Jul.</option>
                                                <option class="" value="8">Ago.</option>
                                                <option class="" value="9">Set.</option>
                                                <option class="" value="10">Oct.</option>
                                                <option class="" value="11">Nov.</option>
                                                <option class="" value="12">Dic.</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 pAnio pleft">
                                            <select class="form-control" style="padding-left: 5px; padding-right: 5px;" name="v_mes_fecha" id="v_ano_fecha" required="">
                                                <option value="0">Año</option>
                                                @for ($i = 1950; $i <2011; $i++) 
                                                    <option class="" value="{{$i}}">{{$i}} </option>
                                                @endfor
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <label for="sw-default" class="label_txt">Apellido Paterno</label>
                                    <span id="v_validApPaterno" style="color: red;">*Campo Obligatorio</span>
                                    <input type="text" class="form-control" name="v_apPaterno" id="v_apPaterno" tabindex="4" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default" class="label_txt">Apellido Materno</label>
                                    <span id="v_validApMaterno" style="color: red;">*Campo Obligatorio</span>
                                    <input type="text" class="form-control" name="v_apMaterno" id="v_apMaterno" tabindex="5" required>
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default" class="label_txt">Nombres</label>
                                    <span id="v_validNombres" style="color: red;">*Campo Obligatorio</span>
                                    <input type="text" class="form-control" name="v_nombres" id="v_nombres" tabindex="6" required>
                                </div>

                                <div class="col-sm-4">
                                    <label for="sw-default" class="label_txt">Correo Electrónico</label>
                                    <span id="v_validCorreo" style="color: red;">*Campo Obligatorio</span>
                                    <span id="v_emailR" style="color: red;">*Correo registrado</span>
                                    <input type="email" class="form-control" id="v_email" name="email" tabindex="7">
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default" class="label_txt">Celular</label>
                                    <span id="v_validCel" style="color: red;">*Número
                                        incorrecto.</span>
                                    <div class="row">
                                        <div class="col-4 pselect">
                                            <select class="form-control selectResp" id="v_codigoCelular">
                                                <option value="+51" selected>+51</option>
                                            </select>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control" name="v_celular" id="v_celular"
                                                tabindex="8" maxlength="9" onkeypress="return isNumeric(event)"
                                                oninput="maxLengthCheck(this)">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default" class="label_txt">Teléfono</label>
                                    <div class="row">
                                        <div class="col-4 pselect">
                                            <select class="form-control selectResp" id="v_codigoTelefono">
                                                <option value="01" selected>01</option>
                                                <option value="41">41</option>
                                                <option value="43">43</option>
                                                <option value="83">83</option>
                                                <option value="54">54</option>
                                                <option value="66">66</option>
                                                <option value="76">76</option>
                                                <option value="84">84</option>
                                                <option value="67">67</option>
                                                <option value="62">62</option>
                                                <option value="56">56</option>
                                                <option value="64">64</option>
                                                <option value="44">44</option>
                                                <option value="74">74</option>
                                                <option value="65">65</option>
                                                <option value="82">82</option>
                                                <option value="53">53</option>
                                                <option value="63">63</option>
                                                <option value="73">73</option>
                                                <option value="51">51</option>
                                                <option value="42">42</option>
                                                <option value="52">52</option>
                                                <option value="72">72</option>
                                                <option value="61">61</option>
                                            </select>
                                        </div>
                                        <div class="col-8">
                                            <input type="number" class="form-control" name="telefono"
                                                id="v_telefono" tabindex="9" maxlength="9"
                                                onkeypress="return isNumeric(event)" oninput="maxLengthCheck(this)">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="sw-default" class="label_txt">Dirección</label>
                                        <input type="text" class="form-control" name="v_direccion" id="v_direccion"
                                            tabindex="10" required>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <label for="sw-default" class="label_txt">Dirección Domiciliara</label>
                                    <select class="form-control" placeholder="Departamento" name="v_dep" id="v_dep" tabindex="11" required>
                                        <option value="">Departamento</option>
                                        @foreach ($departamento as $departamentos)
                                        <option class="" value="{{ $departamentos->id }}">
                                            {{ $departamentos->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default"><br></label>
                                    <select class="form-control " placeholder="Provincia " name="v_prov" id="v_prov"
                                        tabindex="12" required>
                                        <option value="">Provincia</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default"><br></label>
                                    <select class="form-control " placeholder="Distrito " name="v_dist" id="v_dist"
                                        tabindex="13" required>
                                        <option value="">Distrito</option>
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label for="sw-default" class="label_txt">Lugar Nacimiento</label>
                                    <select class="form-control" placeholder="Departamento" name="v_departamento"
                                        id="v_departamento" tabindex="14">
                                        <option value="">Departamento</option>
                                        @foreach ($departamento as $departamentos)
                                        <option class="" value="{{ $departamentos->id }}">
                                            {{ $departamentos->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default"><br></label>
                                    <select class="form-control " placeholder="Provincia " name="v_provincia"
                                        id="v_provincia" tabindex="15">
                                        <option value="">Provincia</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default"><br></label>
                                    <select class="form-control " placeholder="Distrito " name="v_distrito"
                                        id="v_distrito" tabindex="16">
                                        <option value="">Distrito</option>
                                    </select>
                                </div>


                                <div class="col-sm-12">
                                    <label for="" class="label_txt">Género</label>
                                    <div class="row">
                                        <div class="radio_rsp">
                                            <span id="v_validGenero" style="color: red;">*Campo
                                                Obligatorio</span>
                                            <label class="custom-control custom-radio">
                                                <input type="radio" name="v_tipo" id="v_tipo" value="Femenino">
                                                Femenino
                                            </label>
                                        </div>
                                        <div class="radio_rsp">
                                            <label class="custom-control custom-radio">
                                                <input type="radio" name="v_tipo" id="v_tipo" value="Masculino">
                                                Masculino
                                            </label>
                                        </div>
                                        <div class="radio_rsp">
                                            <label class="custom-control custom-radio">
                                                <input type="radio" name="v_tipo" id="v_tipo" value="Personalizado">
                                                Personalizado
                                            </label>
                                        </div>
                                    </div>
                                </div> 
                            </div> <!-- end row -->
                        </div>
                        <div id="swE-default-step-2" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-12">
                                    <div class="float-md-right">
                                        <a onclick="javascript:mostrarContenidoE()" data-toggle="tooltip"
                                            data-placement="left" title="ver vídeo" data-original-title="ver vídeo">
                                            <img src="{{asset('landing/images/play.svg')}}" height="40">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="sw-default">Cargo <a
                                                onclick="$('#form-ver').modal('hide');$('#cargomodalE').modal('show')"
                                                data-toggle="modal"><i class="uil uil-plus"
                                                    style="color: darkblue;cursor: pointer;"></i></a></label>
                                        <select class="form-control" name="v_cargo" id="v_cargo" tabindex="2" required>
                                            <option value="">Seleccionar</option>

                                            @foreach ($cargo as $cargos)
                                            <option class="" value="{{ $cargos->cargo_id }}">
                                                {{ $cargos->cargo_descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="sw-default">Codigo Empleado</label>
                                            <input type="text" class="form-control" name="v_codigoEmpleado"
                                                maxlength="200" id="v_codigoEmpleado" tabindex="1" required>
                                        </div>
                                    </div>
                                </div> <!-- end col -->
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="sw-default">Área <a
                                                onclick="$('#form-ver').modal('hide');$('#areamodalE').modal('show');"
                                                data-toggle="modal"><i class="uil uil-plus"
                                                    style="color: darkblue;cursor: pointer;"></i></a></label>
                                        <select class="form-control" name="v_area" id="v_area" tabindex="3" required>
                                            <option value="">Seleccionar</option>
                                            @foreach ($area as $areas)
                                            <option class="" value="{{ $areas->area_id }}">
                                                {{ $areas->area_descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default" class="labelNivel">Nivel del Colaborador<a
                                                onclick="$('#form-ver').modal('hide');$('#nivelmodalE').modal('show');"
                                                data-toggle="modal"><i class="uil uil-plus"
                                                    style="color: darkblue;cursor: pointer;"></i></a></label>
                                        <select class="form-control" name="v_nivel" id="v_nivel" tabindex="6">
                                            <option value="">Seleccionar</option>
                                            @foreach ($nivel as $niveles)
                                            <option class="" value="{{ $niveles->nivel_id }}">
                                                {{ $niveles->nivel_descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> <!-- end col -->
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="sw-default">Centro Costo <a
                                                onclick="$('#form-ver').modal('hide');$('#centrocmodalE').modal('show');"
                                                data-toggle="modal"><i class="uil uil-plus"
                                                    style="color: darkblue;cursor: pointer;"></i></a></label>
                                        <select class="form-control" name="v_centroc" id="v_centroc" tabindex="4"
                                            required>
                                            <option value="">Seleccionar</option>
                                            @foreach ($centro_costo as $centro_costos)
                                            <option class="" value="{{ $centro_costos->centroC_id }}">
                                                {{ $centro_costos->centroC_descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Local <a
                                                onclick="$('#form-ver').modal('hide');$('#localmodalE').modal('show');"
                                                data-toggle="modal"><i class="uil uil-plus"
                                                    style="color: darkblue;cursor: pointer;"></i></a></label>
                                        <select class="form-control" name="v_local" id="v_local" tabindex="7">
                                            <option value="">Seleccionar</option>
                                            @foreach ($local as $locales)
                                            <option class="" value="{{ $locales->local_id }}">
                                                {{ $locales->local_descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div>
                        <div id="sw-default-step-3" class="setup-content" style="font-size: 12px!important">
                            {{-- CONTENIDO DE INICIO --}}
                            <div class="row pb-2" id="nuevaAltaEdit" style="display: none">
                                {{-- VALIDACION PARA REGISTRAR NUEVA ALTA OBLIGATORIA --}}
                                <div class="col-xl-12" style="display: none" id="validHE">
                                    <span style="color: red;">
                                        *Campo Obligatorio
                                    </span>
                                </div>
                                {{-- BOTON DE NUEVA ALTA --}}
                                <div class="col-xl-2 text-left">
                                    <button type="button" class="btn btn-sm mt-1" style="background-color: #383e56;"
                                        onclick="$(this).prop('disabled', true);javascript:modalNuevaAlta()">
                                        + Nueva alta
                                    </button>
                                    <div class="float-md-right" id="gifAlta" style="display: none">
                                        <img src="{{asset('landing/images/flecha.gif')}}" height="30">
                                    </div>
                                </div>
                            </div>
                            {{-- FINALIZACION --}}
                            {{-- CONTENIDO DE LA TABLA --}}
                            <div class="row">
                                <div class="col-xl-12 col-sm-12">
                                    <div class="table-responsive-sm" style="height: 250px;overflow: auto;">
                                        <table class="table table-hover" id="editar_tablaHistorial"
                                            style="font-size: 13px!important;">
                                            <thead style="background: #fafafa;">
                                                <tr>
                                                    <th>
                                                        Fecha de alta
                                                        &nbsp;
                                                        <img src="{{asset('landing/images/arriba.svg')}}" height="17">
                                                    </th>
                                                    <th>
                                                        Fecha de baja
                                                        &nbsp;
                                                        <img src="{{asset('landing/images/abajo.svg')}}" height="17">
                                                    </th>
                                                    <th>Contrato</th>
                                                    <th>Documento</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="editar_tbodyHistorial"
                                                style="background:#ffffff;color: #585858;font-size: 12px">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            {{-- FINALIZACION --}}
                            {{-- VALIDACION DE NUEVO DETALLE --}}
                            <div class="row pb-2" id="validDC" style="display: none">
                                <div class="col-xl-12">
                                    <span style="color: red;">
                                        *Columnas sombreadas debe culminar de registrar algunos detalles
                                        <img src="{{asset('landing/images/adaptive.svg')}}" height="14">
                                    </span>
                                </div>
                            </div>
                            {{-- FINALIZACION --}}
                        </div>
                        <div id="swF-default-step-4" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-12 pb-2">
                                    <div class="float-md-right">
                                        <a onclick="javascript:mostrarContenidoF()" data-toggle="tooltip"
                                            data-placement="left" title="ver vídeo" data-original-title="ver vídeo">
                                            <img src="{{asset('landing/images/play.svg')}}" height="40">
                                        </a>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group text-center">
                                        {{ csrf_field() }}
                                        <!--<img  alt="" id="v_foto" width="300" height="200">-->
                                        <input type="file" name="file" id="file2" accept="image/*">
                                    </div>
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                            <br>
                        </div>
                        <div id="sw-default-step-5" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="float-md-right">
                                        <a onclick="javascript:mostrarContenidoC()" data-toggle="tooltip"
                                            data-placement="left" title="ver vídeo" data-original-title="ver vídeo">
                                            <img src="{{asset('landing/images/play.svg')}}" height="40">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" id="MostrarCa_e" style="display: none">
                                    <div class="form-group row">
                                        <label style="font-weight: 600;font-size: 14px;"
                                            class="col-lg-5 col-form-label text-right" for="simpleinput">Calendario
                                            de empleado:</label>
                                        <div class="col-lg-5">
                                            <select name="" id="selectCalendario_ed"
                                                class="form-control form-control-sm" style="margin-top: 4px;">
                                                <option hidden selected>Asignar calendario</option>
                                                @foreach ($calendario as $calendarios)
                                                <option class="" value="{{ $calendarios->calen_id }}">
                                                    {{ $calendarios->calendario_nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-1"></div>
                                <div class="col-md-8" id="divescond1" style="display: none">
                                    <input type="hidden" id="idselect3">
                                    <select name="" id="selectCalendario_edit3"
                                        class="form-control col-lg-6 form-control-sm" style="margin-top: 4px;">
                                        <option hidden selected>Asignar calendario</option>
                                        @foreach ($calendario as $calendarios)
                                        <option class="" value="{{ $calendarios->calen_id }}">
                                            {{ $calendarios->calendario_nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2" id="divescond2" style="display: none">
                                    <div class="btn-group mt-2 mr-1">
                                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" style="color: #fff;
                                            background-color: #4a5669;
                                            border-color: #485263;" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false"><img src="{{ asset('admin/images/borrador.svg') }}"
                                                height="15">
                                            Borrar <i class="icon"><span
                                                    data-feather="chevron-down"></span></i></button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" onclick="vaciardFeriaBD()">Dias
                                                feriados</a>
                                            <a class="dropdown-item" onclick="vaciarddescansoBD()">Dias
                                                descanso</a>
                                            {{-- <a class="dropdown-item"
                                                    onclick="vaciardlabTem()">D.
                                                    laborables</a> --}}
                                            <a class="dropdown-item" onclick="vaciarNlabBD()">D. no
                                                laborables</a>
                                            <a class="dropdown-item" onclick="vaciardIncidBD()">Incidencia</a>

                                        </div>
                                    </div><!-- /btn-group -->
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-1"></div>
                                <div class="col-md-10" id="calendarInv_ed" style="display:none"></div>
                                <input type="hidden" id="pruebaEnd_ed">
                                <input type="hidden" id="pruebaStar_ed">
                                <div class="col-md-10" id="calendar_ed" style="display: none;"></div>
                                <div class="col-md-1"></div>
                                <div id="calendarioAsignar_ed" class="modal fade" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                                    <div class="modal-dialog  modal-lg d-flex justify-content-center "
                                        style="margin-top: 150px;">

                                        <div class="modal-content">

                                            <div class="modal-body"
                                                style="font-size:12px!important;background: #f3f3f3;">
                                                <div class="col-md-12">
                                                    <div class="row" style="padding: 0 5px">
                                                        <div class="col-md-3 text-center mt-1 mb-1">
                                                            <button type="button" style=" width: 20em!important; height: 43px !important;"
                                                                class="btn btn-secondary btn-sm"
                                                                onclick="laborable_ed()"><img
                                                                    src="{{ asset('admin/images/dormir.svg') }}"
                                                                    height="20"> Descanso</button>
                                                        </div>
                                                        <div class="col-md-3 text-center mt-1 mb-1">
                                                            <button type="button" style=" width: 20em!important; height: 43px !important;"
                                                                class="btn btn-secondary btn-sm"
                                                                onclick="nolaborable_ed()"><img
                                                                    src="{{ asset('admin/images/evento.svg') }}"
                                                                    height="20"> Día no laborable</button>
                                                        </div>
                                                        <div class="col-md-3 text-center mt-1 mb-1">
                                                            <button type="button" style=" width: 20em!important; height: 43px !important;"
                                                                class="btn btn-secondary btn-sm"
                                                                onclick="$('#nombreFeriado_ed').val('');$('#calendarioAsignar_ed').modal('hide'); $('#myModalFeriado_ed').modal('show')"><img
                                                                    src="{{ asset('admin/images/calendario.svg') }}"
                                                                    height="20"> Día feriado</button>
                                                        </div>
                                                        <div class="col-md-3 text-center mt-1 mb-1">
                                                            {{-- <button type="button"
                                                                    style=" max-width: 18em!important;"
                                                                    class="btn btn-secondary btn-sm "
                                                                    onclick="registrarDdescanso()"><img
                                                                        src="{{ asset('admin/images/calendarioInc.svg') }}"
                                                            height="20"> Incidencia</button>
                                                            --}}
                                                            <button style=" width: 20em!important; height: 43px !important;"
                                                                class="btn btn-secondary btn-sm"
                                                                onclick="agregarinciden_ed()"><img
                                                                    src="{{ asset('admin/images/calendarioInc.svg') }}"
                                                                    height="20"> Incidencia</button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer"
                                                style="padding-top: 5px; padding-bottom: 5px;background: #f1f0f0;">
                                                <div class="col-md-12 text-right">
                                                    <button type="button" style="margin-right: 21px;"
                                                        class="btn btn-soft-primary btn-sm "
                                                        onclick="$('#calendarioAsignar_ed').modal('hide')">Cancelar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                                <div id="myModalFeriado_ed" class="modal fade" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #163552;">
                                                <h5 class="modal-title" id="myModalLabel"
                                                    style="color:#ffffff;font-size:15px">Agregar nuevo
                                                    feriado</h5>
                                                <button type="button" class="close" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <div class="col-md-6">
                                                            <label for="">Nombre de día feriado:</label>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <form action="javascript:diaferiadoRe_ed()">
                                                                <input class="form-control" type="text"
                                                                    id="nombreFeriado_ed" required>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light btn-sm" onclick="$('#myModalFeriado_ed').modal('hide')">Cancelar</button>
                                                <button type="submit" class="btn btn-secondary btn-sm">Aceptar</button>
                                                </form>

                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            </div> <!-- end row -->
                        </div>
                        <div id="sw-default-step-6" class="setup-content" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="float-md-right">
                                        <a onclick="javascript:mostrarContenidoH()" data-toggle="tooltip"
                                            data-placement="left" title="ver vídeo" data-original-title="ver vídeo">
                                            <img src="{{asset('landing/images/play.svg')}}" height="40">
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-12 text-center" id="detallehorario_ed"></div>
                                <div id="detallehorario_ed2" class="col-md-12"></div>
                                <div class="col-md-1"><br></div>
                                <div class="col-md-10" id="mensajeOc_ed"><label for="">Aún no ha
                                        seleccionado un
                                        calendario en el paso anterior.</label></div>
                                <div class="col-md-10" id="calendar2_ed" style="display: none"></div>
                                <div class="col-md-1"><br></div>
                            </div>

                            <div id="horarioAsignar_ed" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
                                aria-hidden="true" data-backdrop="static">
                                <div class="modal-dialog  modal-lg d-flex justify-content-center "
                                    style="max-width: 400px; margin-top: 150px;">

                                    <div class="modal-content">

                                        <div class="modal-body" style="font-size:12px!important;background: #f3f3f3;">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <span id=errorSel style="color: #8b3a1e;display:none">Seleccione
                                                            un horario</span>
                                                        <select data-plugin="customselect"
                                                            class="form-control custom-select custom-select-sm"
                                                            name="selectHorario_ed" id="selectHorario_ed">
                                                            <option hidden selected disabled>Seleccionar horario
                                                            </option>
                                                            @foreach ($horario as $horarios)
                                                            <option class="" value="{{$horarios->horario_id}}">
                                                                {{$horarios->horario_descripcion}} <span
                                                                    style="font-size: 11px;font-style: oblique">({{$horarios->horaI}}-{{$horarios->horaF}})</span>
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 text-left" style="padding-left: 0px;">
                                                        {{-- <button class="btn btn-primary btn-sm"
                                                            style="background-color: #183b5d;border-color:#62778c;margin-top: 5px"
                                                            onclick="abrirHorario_ed()">+</button> --}}
                                                    </div>
                                                    <div class="col-md-12"><br>
                                                        <div class="custom-control custom-switch mb-2">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="fueraHSwitch">
                                                            <label class="custom-control-label"
                                                                for="fueraHSwitch">Trabajar
                                                                fuera de horario</label>
                                                        </div>
                                                        {{--   <div class="custom-control custom-switch mb-2">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="horCompSwitch">
                                                        <label class="custom-control-label" for="horCompSwitch">Horario
                                                            compensable.</label>
                                                    </div> --}}
                                                        <div class="row">
                                                            <div class="custom-control custom-switch mb-2"
                                                                style="left: 12px;">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="horAdicSwitch">
                                                                <label class="custom-control-label"
                                                                    for="horAdicSwitch">Permite marcar horas
                                                                    adicionales.</label>

                                                            </div> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <select id="nHorasAdic" style="display: none;bottom: 3px;"
                                                                class="form-control form-control-sm col-md-3">
                                                                <option value="0.5">0.5 hora </option>
                                                                <option value="1">1 hora </option>
                                                                <option value="2">2 horas </option>
                                                                <option value="3">3 horas </option>
                                                                <option value="4">4 horas </option>
                                                                <option value="5">5 horas </option>
                                                                <option value="6">6 horas </option>
                                                                <option value="7">7 horas </option>
                                                                <option value="8">8 horas </option>
                                                                <option value="9">9 horas </option>
                                                                <option value="10">10 horas </option>
                                                                <option value="11">11 horas </option>
                                                                <option value="12">12 horas </option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>
                                        <div class="modal-footer"
                                            style="padding-top: 5px; padding-bottom: 5px;background: #f1f0f0;">
                                            <div class="col-md-12 text-right" style="padding-right: 0px;">
                                                <button type="button" class="btn btn-light  btn-sm " style="background: #f3f3f3;
                                                border-color: #f3f3f3;"
                                                    onclick="$('#horarioAsignar_ed').modal('hide')">Cancelar</button>
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    style="background-color: #183b5d;border-color:#62778c;"
                                                    onclick="agregarHorarioSe()">Registrar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                            <div id="horarioAgregar_ed" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
                                aria-hidden="true" data-backdrop="static">
                                <div class="modal-dialog  modal-lg d-flex justify-content-center "
                                    style="width: 600px;">

                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:#163552;">
                                            <h5 class="modal-title" id="myModalLabel"
                                                style="color:#ffffff;font-size:15px">
                                                Asignar horario</h5>
                                            <button type="button" class="close"
                                                onclick="$('#horarioAgregar_ed').modal('hide')" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" style="font-size:12px!important">
                                            <div class="row">

                                                <div class="col-md-12">
                                                    <form id="frmHor_ed" action="javascript:registrarHorario_ed()">
                                                        <div class="row">



                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="">Descripción del horario:</label>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm"
                                                                        id="descripcionCa_ed" maxlength="60" required>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Hora de
                                                                        inicio(24h):</label>
                                                                    <input type="text" id="horaI_ed"
                                                                        class="form-control form-control-sm" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Hora de fin(24h):</label>
                                                                    <input type="text" id="horaF_ed"
                                                                        class="form-control form-control-sm" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Horas obligadas:</label>
                                                                    <div class="input-group form-control-sm" style="bottom: 4px;
                                                                   padding-left: 0px; padding-right: 0px;">

                                                                        <input type="text"
                                                                            class="form-control form-control-sm"
                                                                            id="horaOblig_ed" required>
                                                                        <div class="input-group-prepend ">
                                                                            <div class="input-group-text form-control-sm"
                                                                                style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                                                Horas</div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Tolerancia al ingreso(Min):</label>
                                                                    <div class="input-group form-control-sm " style="bottom: 4px;
                                                                    padding-left: 0px; padding-right: 0px;">
                                                                        <input type="number" value="0"
                                                                            class="form-control form-control-sm" min="0"
                                                                            max="59" id="toleranciaH_ed" required>
                                                                        <div class="input-group-prepend  ">
                                                                            <div class="input-group-text form-control-sm "
                                                                                style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                                                Minutos</div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="">Tolerancia a la salida(Min):</label>
                                                                    <div class="input-group form-control-sm " style="bottom: 4px;
                                                                   padding-left: 0px; padding-right: 0px;">
                                                                        <input type="number" value="0"
                                                                            class="form-control form-control-sm" min="0"
                                                                            max="59" id="toleranciaSalida_ed" required>
                                                                        <div class="input-group-prepend  ">
                                                                            <div class="input-group-text form-control-sm "
                                                                                style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                                                Minutos</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4" id="divOtrodia_ed"
                                                                style="display: none">
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        id="otroDC_ed" checked disabled>
                                                                    <label class="form-check-label" for="otroDC_ed"
                                                                        style="margin-top: 2px;font-weight: 700">La hora
                                                                        fin de este horario pertenece al siguiente
                                                                        día.</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="custom-control custom-switch mb-2">
                                                                    <input type="checkbox" class="custom-control-input"
                                                                        id="SwitchTardanza_ed">
                                                                    <label class="custom-control-label"
                                                                        for="SwitchTardanza_ed"
                                                                        style="font-weight: bold;padding-top: 1px">Controlar
                                                                        tardanza</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="custom-control custom-switch mb-2">
                                                                    <input type="checkbox" class="custom-control-input"
                                                                        id="SwitchPausa_ed">
                                                                    <label class="custom-control-label"
                                                                        for="SwitchPausa_ed"
                                                                        style="font-weight: bold;padding-top: 1px">Pausas
                                                                        en el horario</label> &nbsp; <span
                                                                        id="fueraRango_ed"
                                                                        style="color: #80211e;display: none">Hora no
                                                                        esta dentro de rango de horario</span> <span
                                                                        id="errorenPausas_ed"
                                                                        style="color: #80211e;display: none">- Fin de
                                                                        pausa debe ser mayor a inicio pausa</span>

                                                                </div>
                                                            </div>
                                                            <div id="divPausa_ed" class="col-md-12"
                                                                style="display: none">
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-5">
                                                                            <label for=""
                                                                                style="font-weight:600">Descripción</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label for="" style="font-weight:600">Inicio
                                                                                pausa(24h)</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label for="" style="font-weight:600">Fin
                                                                                pausa(24h)</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div id="inputPausa_ed">

                                                                </div>

                                                            </div>


                                                        </div>

                                                </div>



                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-12 text-right">
                                                        <button type="button" class="btn btn-light btn-sm "
                                                            onclick="$('#horarioAgregar_ed').modal('hide')">Cancelar</button>
                                                        <button type="submit" name="" style="background-color: #163552;"
                                                            class="btn btn-sm ">Guardar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                        </div>
                        <div id="sw-default-step-7" class="setup-content" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <div class="row">
                                                <div class="col-xl-12 text-left">
                                                    <div class="float-md-right">
                                                        <a onclick="javascript:mostrarContenidoA()"
                                                            data-toggle="tooltip" data-placement="left"
                                                            title="ver vídeo" data-original-title="ver vídeo">
                                                            <img src="{{asset('landing/images/play.svg')}}" height="40">
                                                        </a>
                                                    </div>
                                                    @if (isset($GestActEmp))
                                                    @if ($GestActEmp==1)
                                                    <button type="button" class="btn btn-sm mt-1"
                                                        style="background-color: #383e56;"
                                                        onclick="$('#actividadTarea').modal()">+Asignar actividad
                                                    </button>
                                                    @else
                                                    @endif
                                                    @else
                                                    <button type="button" class="btn btn-sm mt-1"
                                                        style="background-color: #383e56;"
                                                        onclick="$('#actividadTarea').modal()">+Asignar actividad
                                                    </button>
                                                    @endif

                                                </div>
                                            </div>
                                            <div class="row pt-3">
                                                <div class="col-xl-12 col-sm-12">
                                                    <div class="table-responsive-sm"
                                                        style="height: 250px;overflow: auto;">
                                                        <table class="table table-hover"
                                                            style="font-size: 13px!important;">
                                                            <thead style="background: #fafafa;font-size: 14px">
                                                                <tr>
                                                                    <th>Actividad</th>
                                                                    <th class="text-center th_rh">
                                                                        <i data-feather="activity"
                                                                            style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                                                        &nbsp;
                                                                        Control Remoto
                                                                    </th>
                                                                    <th class="text-center th_rh">
                                                                        <i data-feather="map-pin"
                                                                            style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                                                        &nbsp;
                                                                        Control Ruta
                                                                    </th>
                                                                    <th>Estado</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tablaBodyTarea"
                                                                style="background:#ffffff;color: #585858;font-size: 12px;">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- MODAL DE ASIGNAR ACTIVIDADES --}}
                            <div id="actividadTarea" class="modal fade" tabindex="-1" role="dialog"
                                aria-labelledby="actividadTarea" aria-hidden="true" data-backdrop="static">
                                <div class="modal-dialog  modal-lg d-flex justify-content-center "
                                    style="max-width: 550px;">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color:#163552;">
                                            <h5 class="modal-title" id="myModalLabel"
                                                style="color:#ffffff;font-size:15px">
                                                Asignar Actividad
                                            </h5>
                                        </div>
                                        <div class="modal-body" style="font-size:12px!important">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <form action="javascript:registrarActividadTarea()"
                                                        id="formActvidades">
                                                        <div class="row justify-content-center">
                                                            <div class="col-xl-12">
                                                                <label style="font-size: 14px">
                                                                    <i data-feather="layers"
                                                                        style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                                                    &nbsp;
                                                                    Actividades
                                                                </label>
                                                                <a class="mr-3" data-toggle="tooltip"
                                                                    data-placement="right"
                                                                    title="registrar nueva actividad"
                                                                    data-original-title="registrar nueva actividad"
                                                                    onclick="$('#form-ver').modal('hide');$('#RegActividadTareaGE').modal();">
                                                                    <i class="uil uil-plus"
                                                                        style="color: darkblue;cursor: pointer;font-weight: bold;font-size: 13px"></i>
                                                                </a>
                                                                <select multiple="multiple" data-plugin="customselect"
                                                                    class="form-control" multiple="multiple"
                                                                    id="empleadoActiv"></select>
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-12 text-right">
                                                        <button type="button" class="btn btn-light btn-sm "
                                                            onclick="$('#actividadTarea').modal('toggle');javascript:limpiarSelect()">Cancelar</button>
                                                        <button type="submit" name="" style="background-color: #163552;"
                                                            class="btn btn-sm ">Guardar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                            {{-- FINALIZACIÓN DE MODAL --}}
                        </div>
                        <div id="sw-default-step-8" class="setup-content" style="font-size: 12px!important">
                            <div class="row pb-3">
                                <div class="col-xl-12 col-sm-12">
                                    <button class="btn btn-sm dropdown-toggle mb-2" style="background-color:#383e56;"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        + Agregar
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <a class="dropdown-item" id="v_agregarWindows">WINDOWS</a>
                                        <a class="dropdown-item" id="v_agregarAndroid">ANDROID</a>
                                    </div>
                                    <div class="float-md-right">
                                        <a onclick="javascript:mostrarContenidoD()" data-toggle="tooltip"
                                            data-placement="left" title="ver vídeo" data-original-title="ver vídeo">
                                            <img src="{{asset('landing/images/play.svg')}}" height="40">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card border"
                                        style="border-radius: 5px;border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                                        <div class="card-header"
                                            style="padding: 0.2rem 1rem;background: #383e56!important;color:white !important;border-top-right-radius: 5px; border-top-left-radius: 5px;">
                                            <div class="row pb-1 pl-2">
                                                <div class="col">
                                                    <div class="custom-control custom-switch mb-2">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="customSwitchC1">
                                                        <label class="custom-control-label" for="customSwitchC1"
                                                            style="font-weight: bold">
                                                            <i data-feather="activity"
                                                                style="height: 15px !important;width: 15px !important;color:white !important">
                                                            </i>&nbsp;&nbsp;
                                                            Modo Control Remoto
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3" id="bodyModoControlR">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table id="v_tablaDispositivo" class="table table-hover"
                                                            style="font-size: 13px!important;width: 100% !important">
                                                            <thead
                                                                style="background: #fafafa;font-size: 14px;width: 100% !important">
                                                                <tr>
                                                                    <th>Dispositivo</th>
                                                                    <th>Nombre</th>
                                                                    <th>Activación</th>
                                                                    <th>Codigo</th>
                                                                    <th>Enviado</th>
                                                                    <th>Estado</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="v_tbodyDispositivo"
                                                                style="background:#ffffff;color: #585858;font-size: 12px;width: 100% !important">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card border"
                                        style="border-radius: 5px;border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                                        <div class="card-header"
                                            style="padding: 0.2rem 1rem;background: #383e56!important;color:white !important;border-top-right-radius: 5px; border-top-left-radius: 5px;">
                                            <div class="row pb-1 pl-2">
                                                <div class="col">
                                                    <div class="custom-control custom-switch mb-2">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="customSwitchC2">
                                                        <label class="custom-control-label" for="customSwitchC2"
                                                            style="font-weight: bold">
                                                            <i data-feather="map-pin"
                                                                style="height: 15px !important;width: 15px !important;color:white !important">
                                                            </i>&nbsp;&nbsp;
                                                            Modo Control en Ruta
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3" id="bodyModoControlRuta">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table id="v_tablaDispositivoA" class="table table-hover"
                                                            style="font-size: 13px!important;width: 100% !important">
                                                            <thead
                                                                style="background: #fafafa;font-size: 14px;width: 100% !important">
                                                                <tr>
                                                                    <th>Dispositivo</th>
                                                                    <th>Nombre</th>
                                                                    <th>Codigo</th>
                                                                    <th>Número</th>
                                                                    <th>Actividad (%)</th>
                                                                    <th>Enviado</th>
                                                                    <th>Estado</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="v_tbodyDispositivoA"
                                                                style="background:#ffffff;color: #585858;font-size: 12px;width: 100% !important">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- VER EMPLEADO --}}
<div id="verEmpleadoDetalles" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="verEmpleadoDetalles"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" style="max-width: 850px;">
        <div class="modal-content">
            <div class="modal-header" style="background: #163552;">
                <h4 class="header-title mt-0 " style="color: #f0f0f0">Datos de empleado</h4><br>
                <button type="button" class="close" id="cerrarEd" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:cerrarVer()">
                    @if (isset($modifEmp))
                    @if ($modifEmp==1)
                    <span class="badge float-left pr-4 pt-0">
                        <a style="color: #f0f0f0"
                            onclick="$('#verEmpleadoDetalles').modal('toggle');javascript:editarEmpleado($('#v_idV').val())">
                            <img src="{{asset('admin/images/edit.svg')}}" height="15">
                            <span style="font-weight: bold">Editar Empleado</span>
                        </a>
                    </span>
                    @else

                    @endif
                    @else
                    <span class="badge float-left pr-4 pt-0">
                        <a style="color: #f0f0f0"
                            onclick="$('#verEmpleadoDetalles').modal('toggle');javascript:editarEmpleado($('#v_idV').val())">
                            <img src="{{asset('admin/images/edit.svg')}}" height="15">
                            <span style="font-weight: bold">Editar Empleado</span>
                        </a>
                    </span>
                    @endif

                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0px;">
                <div id="smartwizardVer" style="background: #ffffff; color:#3d3d3d;">
                    <ul style="background: #fdfdfd!important;font-size: 13px;">
                        <li><a href="#persona-step-1">Personales</a></li>
                        <li><a href="#sw-default-step-2">Empresarial</a></li>
                        <li><a href="#sw-default-step-3">Contrato</a></li>
                        <li><a href="#sw-default-step-4">Foto</a></li>
                        <li><a href="#sw-default-step-5">Calendario</a></li>
                        <li><a href="#sw-default-step-6">Horario</a></li>
                        <li><a href="#sw-default-step-7">Actividades</a></li>
                        <li><a href="#sw-default-step-8">Dispositivo</a></li>
                    </ul>
                    <div class="p-3" id="form-registrar">
                        <div id="persona-step-1" style="font-size: 12px!important">
                            <div class="row">
                                <input style="display: none;" name="v_idV" id="v_idV">
                                <div class="col-sm-4">
                                    <label class="label_txt" for="sw-default">Tipo Documento</label>
                                    <input type="text" class="form-control" name="v_tipoDocV" id="v_tipoDocV" style="background-color: #fcfcfc;" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <label class="label_txt" for="sw-default">Num. Documento</label>
                                        <input type="text" class="form-control" name="v_numDocumentoV" id="v_numDocumentoV" style="background-color: #fcfcfc" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <label class="label_txt" for="sw-default">Fecha Nacimiento</label>
                                    <div class="row fechasResponsive">
                                        <div class="col-md-4 prigth">
                                            <select class="form-control" name="v_dia_fechaV" id="v_dia_fechaV"
                                                required="">
                                                <option value="0">Día</option>
                                                @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">{{$i}}
                                                    </option>
                                                    @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-4 prigth">
                                            <select class="form-control" name="v_mes_fechaV" id="v_mes_fechaV"
                                                required="">
                                                <option value="0">Mes</option>
                                                <option class="" value="1">Ene.</option>
                                                <option class="" value="2">Feb.</option>
                                                <option class="" value="3">Mar.</option>
                                                <option class="" value="4">Abr.</option>
                                                <option class="" value="5">May.</option>
                                                <option class="" value="6">Jun.</option>
                                                <option class="" value="7">Jul.</option>
                                                <option class="" value="8">Ago.</option>
                                                <option class="" value="9">Set.</option>
                                                <option class="" value="10">Oct.</option>
                                                <option class="" value="11">Nov.</option>
                                                <option class="" value="12">Dic.</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 pAnio pleft">
                                            <select class="form-control" style="padding-left: 5px;
                                            padding-right: 5px;" name="v_mes_fechaV" id="v_ano_fechaV" required="">
                                                <option value="0">Año</option>
                                                @for ($i = 1950; $i <2011; $i++) <option class="" value="{{$i}}">
                                                    {{$i}}
                                                    </option>
                                                    @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <label class="label_txt" for="sw-default">Apellido Paterno</label>
                                    <input type="text" class="form-control" name="v_apPaternoV" id="v_apPaternoV" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <label class="label_txt" for="sw-default">Apellido Materno</label>
                                    <input type="text" class="form-control" name="v_apMaternoV" id="v_apMaternoV" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <label class="label_txt" for="sw-default">Nombres</label>
                                    <input type="text" class="form-control" name="v_nombresV" id="v_nombresV" disabled>
                                </div>

                                <div class="col-sm-4">
                                    <label class="label_txt" for="sw-default">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="v_emailV" name="v_emailV" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <label class="label_txt" for="sw-default">Celular</label>
                                    <input type="text" class="form-control" name="v_celularV" id="v_celularV" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <label class="label_txt" for="sw-default">Teléfono</label>
                                    <input type="text" class="form-control" name="v_telefonoV" id="v_telefonoV" disabled>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="label_txt" for="sw-default">Dirección</label>
                                        <input type="text" class="form-control" name="v_direccionV" id="v_direccionV"
                                            disabled>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <label class="label_txt" for="sw-default">Dirección Domiciliara</label>
                                    <input type="text" class="form-control" placeholder="Departamento" name="v_depV" id="v_depV" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default"><br></label>
                                    <input type="text" class="form-control" placeholder="Provincia " name="v_provV" id="v_provV" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default"><br></label>
                                    <input type="text" class="form-control" placeholder="Distrito " name="v_distV" id="v_distV" disabled>
                                </div>

                                <div class="col-sm-4">
                                    <label class="label_txt" for="sw-default">Lugar Nacimiento</label>
                                    <input type="text" class="form-control" placeholder="Departamento" name="v_departamentoV" id="v_departamentoV" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default"><br></label>
                                    <input type="text" class="form-control" placeholder="Provincia " name="v_provinciaV" id="v_provinciaV" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <label for="sw-default"><br></label>
                                    <input type="text" class="form-control" placeholder="Distrito " name="v_distritoV" id="v_distritoV" disabled>
                                </div>

                                <div class="col-12">
                                    <label class="label_txt" for="">Género</label>
                                    <div class="row">
                                        <div class="radio_rsp">
                                            <label class="custom-control custom-radio">
                                                <input type="radio" name="v_tipoV" id="v_tipoV" value="Femenino" disabled>
                                                Femenino
                                            </label>
                                        </div>
                                        <div class="radio_rsp">
                                            <label class="custom-control custom-radio">
                                            <input type="radio" name="v_tipoV" id="v_tipoV" value="Masculino" disabled>
                                            Masculino
                                        </label>
                                        </div>
                                        <div class="radio_rsp">
                                            <label class="custom-control custom-radio">
                                                <input type="radio" name="v_tipoV" id="v_tipoV" value="Personalizado"
                                                    disabled>
                                                Personalizado
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end row -->
                        </div>
                        <div id="sw-default-step-2" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="sw-default">Cargo</label>
                                        <input type="text" class="form-control" name="v_cargoV" id="v_cargoV" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Codigo Empleado</label>
                                        <input type="text" class="form-control" name="v_codigoEmpleadoV" maxlength="200"
                                            id="v_codigoEmpleadoV" disabled>
                                    </div>
                                </div> <!-- end col -->
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="sw-default">Área</label>
                                        <input type="text" class="form-control" name="v_areaV" id="v_areaV" tabindex="3"
                                            disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Nivel del Colaborador</label>
                                        <input type="text" class="form-control" name="v_nivelV" id="v_nivelV"
                                            tabindex="6" disabled>
                                    </div>
                                </div> <!-- end col -->
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="sw-default">Centro Costo</label>
                                        <input type="text" class="form-control" name="v_centrocV" id="v_centrocV"
                                            tabindex="4" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Local</label>
                                        <input type="text" class="form-control" name="v_localV" id="v_localV"
                                            tabindex="7" disabled>
                                    </div>
                                </div> <!-- end col -->
                            </div>
                        </div>
                        <div id="sw-default-step-3" style="font-size: 12px!important">
                            {{-- CONTENIDO DE LA TABLA --}}
                            <div class="row">
                                <div class="col-xl-12 col-sm-12">
                                    <div class="table-responsive-sm" style="height: 250px;overflow: auto;">
                                        <table class="table table-hover" id="ver_tablaHistorial"
                                            style="font-size: 13px!important;">
                                            <thead style="background: #fafafa;">
                                                <tr>
                                                    <th>
                                                        Fecha de alta
                                                        &nbsp;
                                                        <img src="{{asset('landing/images/arriba.svg')}}" height="17">
                                                    </th>
                                                    <th>
                                                        Fecha de baja
                                                        &nbsp;
                                                        <img src="{{asset('landing/images/abajo.svg')}}" height="17">
                                                    </th>
                                                    <th>Contrato</th>
                                                    <th>Documento</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="ver_tbodyHistorial"
                                                style="background:#ffffff;color: #585858;font-size: 12px">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            {{-- FINALIZACION --}}
                        </div>
                        <div id="sw-default-step-4" style="font-size: 12px!important">
                            <br><br>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group text-center">
                                        <img src="landing/images/png.svg" height="150" id="v_fotoV">
                                        <br><br>
                                        <h5 id="h5Ocultar" class="m-0 font-size-14" style="color:#8888">
                                            No se encontro imagen</h5>
                                    </div>
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div>
                        <div id="sw-default-step-5" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-md-4 text-right"><label for=""
                                        style="margin-top: 7px;font-weight: 600">Calendario:</label></div>
                                <div class="col-md-4 text-center" id="divescond1_ver">
                                    <input type="hidden" id="idselect3_ver">
                                    <select name="" id="selectCalendario_edit3_ver" class="form-control form-control-sm"
                                        style="margin-top: 4px;" disabled>
                                        <option hidden selected>Asignar calendario</option>
                                        @foreach ($calendario as $calendarios)
                                        <option class="" value="{{ $calendarios->calen_id }}">
                                            {{ $calendarios->calendario_nombre }}</option>
                                        @endforeach
                                    </select><br><br>
                                </div>
                                <div class="col-md-4"></div>
                                <input type="hidden" name="" id="idempleado">
                                <div class="col-md-1"><br></div>

                                <div class="col-md-10" id="calendar3"></div>
                                <div class="col-md-1"><br></div>
                            </div>
                        </div>
                        <div id="sw-default-step-6" style="font-size: 12px!important">
                            <div class="row">

                                <div class="col-md-1"><br></div>
                                <div class="col-md-10" id="calendar4"></div>
                                <div class="col-md-1"><br></div>
                            </div>
                        </div>
                        <div id="sw-default-step-7" class="setup-content" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <div class="row pt-3">
                                                <div class="col-xl-12 col-sm-12">
                                                    <div class="table-responsive-sm"
                                                        style="height: 250px;overflow: auto;">
                                                        <table class="table table-hover"
                                                            style="font-size: 13px!important;">
                                                            <thead style="background: #fafafa;font-size: 14px">
                                                                <tr>
                                                                    <th>Actividad</th>
                                                                    <th class="text-center th_rh">
                                                                        <i data-feather="activity"
                                                                            style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                                                        &nbsp;
                                                                        Control Remoto
                                                                    </th>
                                                                    <th class="text-center th_rh">
                                                                        <i data-feather="map-pin"
                                                                            style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                                                        &nbsp;
                                                                        Control Ruta
                                                                    </th>
                                                                    <th>Estado</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tablaBodyTarea_ver"
                                                                style="background:#ffffff;color: #585858;font-size: 12px">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="sw-default-step-8" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card border"
                                        style="border-radius: 5px;border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                                        <div class="card-header"
                                            style="padding: 0.2rem 1rem;background: #383e56!important;color:white !important;border-top-right-radius: 5px; border-top-left-radius: 5px;">
                                            <div class="row pb-1 pl-2">
                                                <div class="col">
                                                    <div class="custom-control custom-switch mb-2">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="customSwitchCV1">
                                                        <label class="custom-control-label" for="customSwitchCV1"
                                                            style="font-weight: bold">
                                                            <i data-feather="activity"
                                                                style="height: 15px !important;width: 15px !important;color:white !important">
                                                            </i>&nbsp;&nbsp;
                                                            Modo Control Remoto
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3" id="bodyModoControlRV">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table id="ver_tablaDispositivo" class="table table-hover"
                                                            style="font-size: 13px!important;width: 100% !important">
                                                            <thead
                                                                style="background: #fafafa;font-size: 14px;width: 100% !important">
                                                                <tr>
                                                                    <th>Dispositivo</th>
                                                                    <th>Nombre</th>
                                                                    <th>Activación</th>
                                                                    <th>Codigo</th>
                                                                    <th>Enviado</th>
                                                                    <th>Estado</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="ver_tbodyDispositivo"
                                                                style="background:#ffffff;color: #585858;font-size: 12px">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card border"
                                        style="border-radius: 5px;border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                                        <div class="card-header"
                                            style="padding: 0.2rem 1rem;background: #383e56!important;color:white !important;border-top-right-radius: 5px; border-top-left-radius: 5px;">
                                            <div class="row pb-1 pl-2">
                                                <div class="col">
                                                    <div class="custom-control custom-switch mb-2">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="customSwitchCV2">
                                                        <label class="custom-control-label" for="customSwitchCV2"
                                                            style="font-weight: bold">
                                                            <i data-feather="map-pin"
                                                                style="height: 15px !important;width: 15px !important;color:white !important">
                                                            </i>&nbsp;&nbsp;
                                                            Modo Control en Ruta
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3" id="bodyModoControlAV">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table id="ver_tablaDispositivoA" class="table table-hover"
                                                            style="font-size: 13px!important;width: 100% !important">
                                                            <thead
                                                                style="background: #fafafa;font-size: 14px;width: 100% !important">
                                                                <tr>
                                                                    <th>Dispositivo</th>
                                                                    <th>Nombre</th>
                                                                    <th>Codigo</th>
                                                                    <th>Número</th>
                                                                    <th>Actividad (%)</th>
                                                                    <th>Enviado</th>
                                                                    <th>Estado</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="ver_tbodyDispositivoA"
                                                                style="background:#ffffff;color: #585858;font-size: 12px;width: 100% !important">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- FINALIZACION --}}
<div id="modalIncidencia" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Agregar
                    nueva incidencia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12">
                        <form id="frmIncidenciaCa" action="javascript:modalIncidencia()">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Descripcion:</label>
                                        <input type="text" class="form-control form-control-sm" id="descripcionInciCa"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6"><label for=""><br></label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="descuentoCheckCa">
                                        <label class="form-check-label" for="descuentoCheckCa">Aplicar
                                            descuento</label>
                                    </div>
                                </div>



                            </div>

                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">Cancelar</button>
                            <button type="submit" name="" style="background-color: #163552;"
                                class="btn btn-sm">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="modalIncidencia_ed" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar nueva
                    incidencia
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12">
                        <form id="frmIncidenciaCa_ed" action="javascript:modalIncidencia_ed()">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Descripcion:</label>
                                        <input type="text" class="form-control form-control-sm"
                                            id="descripcionInciCa_ed" required>
                                    </div>
                                </div>
                                <div class="col-md-6"><label for=""><br></label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="descuentoCheckCa_ed">
                                        <label class="form-check-label" for="descuentoCheckCa_ed">Aplicar
                                            descuento</label>
                                    </div>
                                </div>



                            </div>

                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">Cancelar</button>
                            <button type="submit" name="" style="background-color: #163552;"
                                class="btn btn-sm">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


{{-- visibilidad de editar --}}
@if (isset($modifEmp))
@if ($modifEmp==1)
<style>
    a[name="editarEName"] {
        display: inline;
    }
</style>
@else
<style>
    a[name="editarEName"] {
        display: none;
    }
</style>
{{-- <script>
document.getElementsByName("editarEName").remove();
</script> --}}
@endif
@else
<style>
    a[name="editarEName"] {
        display: inline;
    }
</style>
@endif

{{-- visibilidad de dar de baja --}}
@if (isset($bajaEmp))
@if ($bajaEmp==1)
<style>
    a[name="dBajaName"] {
        display: inline;
    }
</style>
@else
<style>
    a[name="dBajaName"] {
        display: none;
    }
</style>
@endif
@else
<style>
    a[name="dBajaName"] {
        display: inline;
    }
</style>
@endif

{{-- visibilidad de switch --}}
@if (isset($GestActEmp))
@if ($GestActEmp==1)
<input type="hidden" id="gestActI" value="1">
@else
<input type="hidden" id="gestActI" value="0">
@endif
@else
<input type="hidden" id="gestActI" value="1">
@endif
</div>
@endsection
@section('script')
<script>
    var urlFoto = "";
        var hayFoto = false;
        var id_empleado = '';
</script>
<script src="{{ asset('landing/js/actualizarPDatos.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>



<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ asset('admin/assets/libs/smartwizard/jquery.smartWizard.min.js') }}"></script>
<script>
    function maxLengthCheck(object) {
            if (object.value.length > object.maxLength)
                object.value = object.value.slice(0, object.maxLength)
        }
        function isNumeric(evt) {
            var theEvent = evt || window.event;
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode(key);
            var regex = /[0-9]|\./;
            if (!regex.test(key)) {
                theEvent.returnValue = false;
                if (theEvent.preventDefault) theEvent.preventDefault();
            }
        }
</script>
<script src="{{asset('landing/js/tabla.js')}}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap-fileinput/piexif.min.js') }} "></script>
<script src="{{ asset('admin/assets/libs/bootstrap-fileinput/sortable.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap-fileinput/purify.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap-fileinput/fileinput.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap-fileinput/theme.js') }}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap-fileinput/es.js') }}"></script>


<script src="{{ asset('admin/assets/libs/moment/moment.min.js') }}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/es.js')}}"></script>

<script src="{{ asset('admin/packages/core/main.js') }}"></script>
<script src="{{ asset('admin/packages/core/locales/es.js') }}"></script>
<script src="{{ asset('admin/packages/daygrid/main.js') }}"></script>
<script src="{{ asset('admin/packages/timegrid/main.js') }}"></script>
<script src="{{ asset('admin/packages/interaction/main.js') }}"></script>
<script src="{{ asset('admin/assets/js/pages/form-wizard.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js')}}"></script>
<script src="{{ asset('landing/js/smartwizard.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/alertify/alertify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ asset('landing/js/seleccionarDepProv.js') }}"></script>
<script src="{{ asset('landing/js/cargaMasivaF.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
<script src="{{ asset('landing/js/empleado.js') }}"></script>
<script src="{{ asset('landing/js/empleadoA.js') }}"></script>
<script src="{{ asset('landing/js/dispositivos.js') }}"></script>
<script src="{{asset('landing/js/modosEmpleado.js')}}"></script>
<script src="{{asset('landing/js/contrato.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
@endsection