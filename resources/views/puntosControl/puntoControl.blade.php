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
{{-- visibilidad de switch --}}
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
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
<script src="{{asset('landing/js/puntoControl.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection