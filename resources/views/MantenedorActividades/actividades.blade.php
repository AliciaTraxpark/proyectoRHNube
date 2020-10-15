@extends('layouts.vertical')


@section('css')
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')}}" rel="stylesheet" />
<link href="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0">Actividades</h4>
    </div>
</div>
@endsection
@section('content')
<div class="row pr-4">
    <div class="col-xl-12 text-right">
        <button type="button" class="btn btn-sm mt-1" style="background-color: #163552;"
            onclick="$('#regactividadTarea').modal()">+ Nueva
            Actividad
        </button>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <table id="actividades" class="table nowrap" style="font-size: 13px!important;width:100%;">
                    <thead style="background: #fafafa;" style="width:100%!important">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Actividad</th>
                            <th class="text-center">Control remoto</th>
                            <th class="text-center">Asistencia en puerta</th>
                        </tr>
                    </thead>
                    <tbody id="actividOrga"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="regactividadTarea" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="regactividadTarea"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 550px;">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Registrar Actividad
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important">
                <div class="row">
                    <div class="col-md-12">
                        <form action="javascript:registrarNuevaActividadTarea()">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Nombre:</label>
                                        <input type="text" class="form-control form-control-sm" id="regnombreTarea"
                                            required>
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
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
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
<script src="{{asset('landing/js/actividades.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection