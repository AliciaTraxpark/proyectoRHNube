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
    <h4 class="mb-1 mt-0 pl-3" style="font-weight: bold">Centro Costos</h4>
  </div>
</div>
@endsection
@section('content')
<style>
  .table {
    width: 100% !important;
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
</style>
{{-- BOTONOS DE PANEL --}}
<div class="row pr-3 pl-3 pt-3">
  <div class="col-md-6 text-left">
    <button type="button" class="btn btn-sm mt-1" style="background-color: #e3eaef;border-color:#e3eaef;color:#37394b"
      onclick="javascript:asignarCentroC()">
      <img src="{{asset('landing/images/calculator.svg')}}" class="mr-1" height="18">
      Asignar Centro Costo
    </button>
  </div>
  <div class="col-md-6 text-right">
    <button type="button" class="btn btn-sm mt-1" style="background-color: #163552;"
      onclick="javascript:modalRegistrar()">
      + Nuevo Centro Costo
    </button>
  </div>
</div>
{{-- FINALIZACION --}}
{{-- TABLA DE CENTRO DE COSTOS --}}
<div class="row justify-content-center">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <table id="centroC" class="table nowrap" style="font-size: 13px!important;width:100%;">
          <thead style="background: #fafafa;" style="width:100%!important">
            <tr>
              <th>#</th>
              <th>Centro Costo</th>
              <th>N° empleados</th>
              <th>En uso</th>
              <th class="text-center"></th>
            </tr>
          </thead>
          <tbody id="centroOrg" style="width:100%!important"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
{{-- FINALIZACION --}}
{{-- EDITAR CENTRO COSTO --}}
<div id="e_centrocmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="e_centrocmodal"
  aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg d-flex justify-content-center">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#163552;">
        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
          Editar Centro Costo
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="font-size:12px!important">
        <input type="hidden" id="e_idCentro">
        <form action="javascript:actualizarCentroC()">
          {{ csrf_field() }}
          <div class="col-md-12">
            <label for="">Centro Costo</label>
            <input type="text" class="form-control" id="e_descripcion" required>
          </div>
          <div class="col-md-12 pt-2">
            <div class="float-right mb-0">
              <span style="font-size: 11px;">
                *Se visualizara empleados sin centro costo
              </span>
            </div>
            <label class="mb-0">Empleados</label>
            <select id="e_empleadosCentro" data-plugin="customselect" class="form-control" multiple="multiple"></select>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-sm" style="background-color:#163552;">Guardar</button>
      </div>
      </form>
    </div>
  </div>
</div>
{{-- FINALIZACION --}}
{{-- ASIGNACION DE CENTRO --}}
<div id="a_centrocmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="a_centrocmodal"
  aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg d-flex justify-content-center">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#163552;">
        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
          Asignar Centro Costo
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
          onclick="javascript:limpiarAsignacion()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="font-size:12px!important">
        <form action="javascript:guardarAsignacionCentro()">
          {{ csrf_field() }}
          <div class="col-md-12">
            <label for="">Centro Costo</label>
            <select id="a_centro" data-plugin="customselect" class="form-control" required>
              <option value="" disabled selected>Seleccionar</option>
            </select>
          </div>
          <div class="col-md-12 pt-2">
            <div class="float-right mb-0">
              <span style="font-size: 11px;">
                *Se visualizara empleados sin centro costo
              </span>
            </div>
            <label class="mb-0">Empleados</label>
            <select id="a_empleadosCentro" data-plugin="customselect" class="form-control" multiple="multiple"
              disabled></select>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal"
          onclick="javascript:limpiarAsignacion()">Cerrar</button>
        <button type="submit" class="btn btn-sm" style="background-color:#163552;">Guardar</button>
      </div>
      </form>
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
</div>
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
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
<script src="{{asset('js/select2search.js')}}"></script>
<script src="{{asset('landing/js/centroCosto.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection