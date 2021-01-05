@extends('layouts.vertical')
@section('css')
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/alertify/alertify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/leaflet/leaflet.css')}}" rel="stylesheet"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin="" />
<link href="{{URL::asset('admin/assets/libs/leaflet/leaflet-routing-machine.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/leaflet/leaflet-search.src.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/leaflet/easy-button.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/leaflet/Control.FullScreen.css')}}" rel="stylesheet" type="text/css" />
<!-- Semantic UI theme -->
<link href="{{ URL::asset('admin/assets/libs/alertify/default.css') }}" rel="stylesheet" type="text/css" />

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0 pl-3" style="font-weight: bold">Puntos de control</h4>
    </div>
</div>
@endsection
@section('content')
{{-- STYLOS --}}
<style>
    .borderColor {
        border-color: red;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #ced0d3;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #52565b;
    }

    .select2-container--default .select2-selection--multiple {
        overflow-y: scroll;
    }

    .form-control:disabled {
        background-color: #fcfcfc;
    }

    #mapid {
        padding: 0;
        width: auto;
        height: 380px;
    }
</style>
{{-- FINALIZACION --}}
{{-- BOTONOS DE PANEL --}}
<div class="row pr-3 pl-3 pt-3">
    <div class="col-md-6 text-left colResponsive">
        <button type="button" class="btn btn-sm mt-1"
            style="background-color: #e3eaef;border-color:#e3eaef;color:#37394b"
            onclick="javascript:asignarActividadMasiso()">
            <img src="{{asset('landing/images/placeholder.svg')}}" class="mr-1" height="18">
            Asignar Punto de control
        </button>
    </div>
    <div class="col-md-6 text-right colResponsive">
        <button type="button" class="btn btn-sm mt-1" style="background-color: #163552;"
            onclick="$('#regactividadTarea').modal();javascript:empleadoListaReg()">
            + Nuevo Punto de control
        </button>
    </div>
</div>
{{-- FINALIZACION --}}
{{-- TABLA DE PUNTOS DE CONTROL --}}
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table id="puntosC" class="table nowrap" style="font-size: 13px!important;width:100%;">
                    <thead style="background: #fafafa;" style="width:100%!important">
                        <tr>
                            <th>#</th>
                            <th>Punto control</th>
                            <th>Código</th>
                            <th class="text-center">Control en ruta</th>
                            <th class="text-center">Asistencia en puerta</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="puntoOrganizacion" style="width:100%!important"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- FINALIZACION --}}
{{-- MODAL DE EDITAR --}}
<div id="modaleditarPuntoControl" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="modaleditarPuntoControl" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-lg d-flex justify-content-center modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Editar Punto de Control
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="e_idPuntoC">
                        <form action="javascript:editarPuntoControl()" id="FormEditarPuntoControl">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Punto Control:</label>
                                        <input type="text" class="form-control form-control-sm" id="e_descripcionPunto"
                                            maxlength="100" required disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Código:</label>
                                        <input type="text" class="form-control form-control-sm" id="e_codigoPunto"
                                            maxlength="40">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="e_puntoCRT">
                                        <label class="custom-control-label" for="e_puntoCRT" style="font-weight: bold">
                                            <i data-feather="map-pin"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                            &nbsp;&nbsp;
                                            Control en Ruta
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="e_puntoAP">
                                        <label class="custom-control-label" for="e_puntoAP" style="font-weight: bold">
                                            <i data-feather="check-circle"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                            &nbsp;&nbsp;
                                            Asistencia en Puerta
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row pt-2 border-top rowEmpleadosEditar">
                                <div class="col-md-12 text-left">
                                    <label for="">Asignar por:</label>
                                </div>
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="e_puntosPorE">
                                        <label class="custom-control-label" for="e_puntosPorE"
                                            style="font-weight: bold">
                                            Seleccionar por empleados
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-right colxEmpleados">
                                    <div class="form-group mb-0 mt-3">
                                        <input type="checkbox" id="e_todosEmpleados">
                                        <label for="" class="mb-0">Seleccionar a todos</label>
                                        <div class="float-left mb-0">
                                            <span style="font-size: 11px;">
                                                *Se visualizara empleados con esta actividad asignada
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 colxEmpleados">
                                    <select id="e_empleadosPunto" data-plugin="customselect" class="form-control"
                                        multiple="multiple">
                                    </select>
                                </div>
                            </div>
                            <div class="row pt-2 pb-2 border-bottom rowAreasEditar">
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="e_puntosPorA">
                                        <label class="custom-control-label" for="e_puntosPorA"
                                            style="font-weight: bold">
                                            Seleccionar por áreas
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-right colxAreas">
                                    <div class="form-group mb-0 mt-3">
                                        <input type="checkbox" id="e_todasAreas">
                                        <label for="" class="mb-0">Seleccionar todos</label>
                                        <div class="float-left mb-0">
                                            <span style="font-size: 11px;">
                                                *Se visualizara áreas con esta actividad asignada
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left colxAreas">
                                    <select id="e_areasPunto" data-plugin="customselect"
                                        class="form-control form-control-sm select2Multiple" multiple="multiple">
                                    </select>
                                </div>
                            </div>
                            <div class="row pt-2">
                                <div class="col-md-12 text-left">
                                    <label for="">
                                        Geolocalización
                                        <img src="{{asset('landing/images/placeholder.svg')}}" class="mr-1" height="18">
                                    </label>
                                </div>
                                <div class="col-md-12">
                                    <div class="row" id="e_rowGeo"></div>
                                </div>
                                <div class="col-md-12" id="e_colMapa">
                                    <div id="mapid"></div>
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
                                onclick="javascript:limpiarPuntoEnEditar()">
                                Cancelar
                            </button>
                            <button type="submit" name="" style="background-color: #163552;" class="btn btn-sm">
                                Guardar
                            </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- FINALIZACION --}}
{{-- MODAL DE SESSION --}}
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
{{-- FINALIZACION --}}
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
<script src="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js')}}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/es.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/alertify/alertify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/leaflet.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/leaflet-src.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/ActiveLayers.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/SelectLayers.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/leaflet-routing-machine.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/easy-button.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/Control.FullScreen.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/leaflet/editablecirclemarker.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
<script src="{{asset('landing/js/puntoControl.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection