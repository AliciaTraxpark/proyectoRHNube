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
<link href="{{ URL::asset('admin/assets/css/zoom.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ URL::asset('admin/assets/libs/alertify/bootstrap.css') }}" rel="stylesheet" type="text/css" /> --}}
<!-- Semantic UI theme -->
<link href="{{ URL::asset('admin/assets/libs/alertify/default.css') }}" rel="stylesheet" type="text/css" />
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
<style>
    .table {
        width: 100% !important;
    }

    div.dataTables_wrapper div.dataTables_filter {
        display: none;
    }

    .dataTables_scrollHeadInner {
        margin: 0 auto !important;
        width: 100% !important;
    }

    .form-control:disabled {
        background-color: #fcfcfc;
    }

    .borderColor {
        border-color: red;
    }

    @media (max-width: 767.98px) {

        li.paginate_button.previous,
        li.paginate_button.next {
            font-size: 0.9rem !important;
        }

        .pr-5 {
            padding-right: 1rem !important;
        }

        .dataTable,
        .dataTables_scrollHeadInner,
        .dataTables_scrollBody {
            margin: 0 auto !important;
            width: 100% !important;
        }

        table.dataTable>tbody>tr.child ul.dtr-details {
            display: flex !important;
            flex-flow: column !important;
        }
    }
</style>
<div class="row pr-5">
    <div class="col-md-12 text-right pr-5">
        <button type="button" class="btn btn-sm mt-1" style="background-color: #163552;"
            onclick="$('#regactividadTarea').modal();javascript:empleadoListaReg()">+ Nueva
            Actividad
        </button>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="card">
            <div class="card-body">
                {{-- BUSCAR PERSONALIZADO --}}
                <div class="col-md-4 inputResponsive" id="filter_global">
                    <td align="center">
                        <input type="text" class="global_filter form-control" id="global_filter" style="height: 35px;"
                            placeholder="Buscar">
                    </td>
                </div>
                {{-- FINALIZACION --}}
                <table id="actividades" class="table nowrap" style="font-size: 13px!important;width:100%;">
                    <thead style="background: #fafafa;" style="width:100%!important">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Actividad</th>
                            <th class="text-center">Código</th>
                            <th class="text-center">Control remoto</th>
                            <th class="text-center">Asistencia en puerta</th>
                            <th class="text-center">En uso</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="actividOrga" style="width:100%!important"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="regactividadTarea" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="regactividadTarea"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-xs d-flex justify-content-center">
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
                        <form action="javascript:registrarActividadTarea()">
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
                                <div class="col-md-6 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="customCR">
                                        <label class="custom-control-label" for="customCR"
                                            style="font-weight: bold">Control Remoto</label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="customAP">
                                        <label class="custom-control-label" for="customAP"
                                            style="font-weight: bold">Asistencia en Puerta</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Asignar empleado</label>
                                    <select id="reg_empleados" data-plugin="customselect" class="form-control"
                                        multiple="multiple">
                                        <option value="" disabled selected>Seleccionar Empleados</option>
                                    </select>
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
                                onclick="javascript:limpiarModo()">Cancelar</button>
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
<div id="editactividadTarea" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editactividadTarea"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 550px;">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Editar Actividad
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="idActiv">
                        <form action="javascript:editarActividadTarea()">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Nombre:</label>
                                        <input type="text" class="form-control form-control-sm" id="e_nombreTarea"
                                            maxlength="40" required disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Código:</label>
                                        <input type="text" class="form-control form-control-sm" id="e_codigoTarea"
                                            maxlength="40">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="e_customCR">
                                        <label class="custom-control-label" for="e_customCR"
                                            style="font-weight: bold">Control Remoto</label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="e_customAP">
                                        <label class="custom-control-label" for="e_customAP"
                                            style="font-weight: bold">Asistencia en Puerta</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Asignar empleado</label>
                                    <select id="empleados" data-plugin="customselect" class="form-control"
                                        multiple="multiple">
                                        <option value="" disabled selected>Seleccionar Empleados</option>
                                    </select>
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
                                onclick="javascript:limpiarModo()">Cancelar</button>
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
<script src="{{URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js')}}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/es.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/alertify/alertify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
<script src="{{asset('landing/js/actividades.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection