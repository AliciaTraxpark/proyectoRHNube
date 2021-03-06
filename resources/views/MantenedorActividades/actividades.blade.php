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
        <h4 class="mb-1 mt-0 pl-3" style="font-weight: bold">Actividades</h4>
    </div>
</div>
@endsection
@section('content')
<style>
    .table {
        width: 100% !important;
    }

    .form-control:disabled {
        background-color: #fcfcfc;
    }

    .borderColor {
        border-color: red;
    }

    .table td {
        padding-bottom: 0rem;
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

    /* FINALIZACION */
    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #ced0d3;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #52565b;
    }

    .select2-container--default .select2-selection--multiple {
        overflow-y: scroll;
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

        .rowResponsive {
            padding-top: 0rem !important;
        }

        .colResponsive {
            width: 50% !important;
        }

        .groupResp {
            text-align: left !important;
        }
    }
</style>
{{-- BOTONES DE ASIGNACION Y REGISTAR --}}
<div class="row pr-3 pl-3 pt-3 rowResponsive">
    @if (isset($agregarActi))
    @if ($agregarActi==1)
    <div class="col-md-6 text-left colResponsive">
        <button type="button" class="btn btn-sm mt-1"
            style="background-color: #e3eaef;border-color:#e3eaef;color:#37394b"
            onclick="javascript:asignarActividadMasiso()">
            <img src="{{asset('landing/images/capas.svg')}}" class="mr-1" height="18">
            Asignar actividad
        </button>
    </div>
    <div class="col-md-6 text-right colResponsive">
        <button type="button" class="btn btn-sm mt-1" style="background-color: #163552;"
            onclick="javascript:modalRegistrar();javascript:empleadoListaReg()">+ Nueva
            Actividad
        </button>
    </div>
    @else
    @endif
    @else
    <div class="col-md-6 text-left colResponsive">
        <button type="button" class="btn btn-sm mt-1"
            style="background-color: #e3eaef;border-color:#e3eaef;color:#37394b"
            onclick="javascript:asignarActividadMasiso()">
            <img src="{{asset('landing/images/capas.svg')}}" class="mr-1" height="18">
            Asignar actividad
        </button>
    </div>
    <div class="col-md-6 text-right colResponsive">
        <button type="button" class="btn btn-sm mt-1" style="background-color: #163552;"
            onclick="javascript:modalRegistrar();javascript:estadoAsignacionesReg()">+ Nueva
            Actividad
        </button>
    </div>
    @endif

</div>
{{-- FINALIZACION --}}
{{-- TABLA DE ACTIVIDADES --}}
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table id="actividades" class="table nowrap" style="font-size: 13px!important;width:100%;">
                    <thead style="background: #fafafa;" style="width:100%!important">
                        <tr>
                            <th>#</th>
                            <th>Actividad</th>
                            <th>C??digo</th>
                            <th class="text-center">Control remoto</th>
                            <th class="text-center">Control en ruta</th>
                            <th class="text-center">Asistencia en puerta</th>
                            <th class="">Modo Tareo</th>
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
{{-- FINALIZACION --}}
{{-- MODAL DE REGISTRAR ACTIVIDAD --}}
<div id="regactividadTarea" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="regactividadTarea"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-lg d-flex justify-content-center">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Registrar Actividad
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important">
                <div class="row">
                    <div class="col-md-12">
                        <form action="javascript:registrarActividadTarea()" id="FormRegistrarActividadTarea">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Nombre:</label>
                                        <input type="text" class="form-control form-control-sm" id="nombreTarea"
                                            maxlength="100" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">C??digo:</label>
                                        <input type="text" class="form-control form-control-sm" id="codigoTarea"
                                            maxlength="40">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="customCR">
                                        <label class="custom-control-label" for="customCR" style="font-weight: bold">
                                            <i data-feather="activity"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                            &nbsp;&nbsp;
                                            Control Remoto
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="customCRT">
                                        <label class="custom-control-label" for="customCRT" style="font-weight: bold">
                                            <i data-feather="map-pin"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                            &nbsp;&nbsp;
                                            Control en Ruta
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="customAP">
                                        <label class="custom-control-label" for="customAP" style="font-weight: bold">
                                            <i data-feather="check-circle"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                            &nbsp;&nbsp;
                                            Asistencia en Puerta
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="customMT">
                                        <label class="custom-control-label" for="customMT" style="font-weight: bold">
                                            <i data-feather="pocket"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                            &nbsp;&nbsp;
                                            Modo tareo
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row border-top rowEmpleados" style="display: none">
                                <div class="col-md-12 text-left">
                                    <label for="">Asignar por:</label>
                                </div>
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="customAE">
                                        <label class="custom-control-label" for="customAE" style="font-weight: bold">
                                            Seleccionar por empleados
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left" id="porEmpleadosReg">
                                    <div class="form-group mb-0 mt-2">
                                        <input type="checkbox" id="checkboxEmpleadosTodosReg">
                                        <label for="" class="mb-0">Todos, incluyendo nuevos</label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-right todosColReg aNuevosR">
                                    <div class="form-group mb-0 mt-3 groupResp">
                                        <input type="checkbox" id="checkboxEmpleadosReg">
                                        <label for="" class="mb-0">Seleccionar a todos</label>
                                        <div class="float-left mb-0">
                                            <span style="font-size: 11px;">
                                                *Se visualizara empleados con esta actividad asignada
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 todosColReg aNuevosR">
                                    <select id="reg_empleados" data-plugin="customselect" class="form-control"
                                        multiple="multiple">
                                        <option value="" disabled selected>Seleccionar Empleados</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row pt-2 rowEmpleados" style="display: none">
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="customAA">
                                        <label class="custom-control-label" for="customAA" style="font-weight: bold">
                                            Seleccionar por ??reas
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left colAreasReg">
                                    <div class="form-group mb-0 mt-2">
                                        <input type="checkbox" id="checkboxAreasTodosReg">
                                        <label for="" class="mb-0">Nuevos por ??rea</label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-right colAreasReg">
                                    <div class="form-group mb-0 mt-3 groupResp">
                                        <input type="checkbox" id="checkboxAreasReg">
                                        <label for="" class="mb-0">Seleccionar todos</label>
                                        <div class="float-left mb-0">
                                            <span style="font-size: 11px;">
                                                *Se visualizara ??reas con esta actividad asignada
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left colAreasReg">
                                    <select id="areaAsignarReg" data-plugin="customselect"
                                        class="form-control form-control-sm select2Multiple" multiple="multiple">
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
{{-- FINALIZACION DE MODAL --}}
{{-- MODAL DE EDITAR ACTIVIDAD --}}
<div id="editactividadTarea" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editactividadTarea"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-lg d-flex justify-content-center">
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
                        <form action="javascript:editarActividadTarea()" id="FormEditarActividadTarea">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Nombre:</label>
                                        <input type="text" class="form-control form-control-sm" id="e_nombreTarea"
                                            maxlength="100" required disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">C??digo:</label>
                                        <input type="text" class="form-control form-control-sm" id="e_codigoTarea"
                                            maxlength="40">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="e_customCR">
                                        <label class="custom-control-label" for="e_customCR" style="font-weight: bold">
                                            <i data-feather="activity"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                            &nbsp;&nbsp;
                                            Control Remoto
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="e_customCRT">
                                        <label class="custom-control-label" for="e_customCRT" style="font-weight: bold">
                                            <i data-feather="map-pin"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                            &nbsp;&nbsp;
                                            Control en Ruta
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="e_customAP">
                                        <label class="custom-control-label" for="e_customAP" style="font-weight: bold">
                                            <i data-feather="check-circle"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                            &nbsp;&nbsp;
                                            Asistencia en Puerta
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="e_customMT">
                                        <label class="custom-control-label" for="e_customMT" style="font-weight: bold">
                                            <i data-feather="pocket"
                                                style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                            &nbsp;&nbsp;
                                            Modo tareo
                                        </label>
                                        <img id="svgInfo" data-toggle='tooltip'
                                            data-original-title='Tiene asignado subactividades' data-placement='right'
                                            style="cursor: pointer;display:none" src='landing/images/info.svg'
                                            height='14'>
                                    </div>
                                </div>
                            </div>
                            <div class="row pt-2 border-top rowEmpleadosEditar">
                                <div class="col-md-12 text-left">
                                    <label for="">Asignar por:</label>
                                </div>
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="e_customAE">
                                        <label class="custom-control-label" for="e_customAE" style="font-weight: bold">
                                            Seleccionar por empleados
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left" id="porEmpleados">
                                    <div class="form-group mb-0 mt-2">
                                        <input type="checkbox" id="checkboxEmpleadosEditarTodos">
                                        <label for="" class="mb-0">Todos, incluyendo nuevos</label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-right todosCol aNuevosE">
                                    <div class="form-group mb-0 mt-3 groupResp">
                                        <input type="checkbox" id="checkboxEmpleadosEditar">
                                        <label for="" class="mb-0">Seleccionar a todos</label>
                                        <div class="float-left mb-0">
                                            <span style="font-size: 11px;">
                                                *Se visualizara empleados con esta actividad asignada
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 todosCol aNuevosE">
                                    <select id="empleados" data-plugin="customselect" class="form-control"
                                        multiple="multiple">
                                    </select>
                                </div>
                            </div>
                            <div class="row pt-2 rowEmpleadosEditar">
                                <div class="col-md-12 text-left">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="e_customAA">
                                        <label class="custom-control-label" for="e_customAA" style="font-weight: bold">
                                            Seleccionar por ??reas
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left colAreas">
                                    <div class="form-group mb-0 mt-2">
                                        <input type="checkbox" id="checkboxAreasEditarTodos">
                                        <label for="" class="mb-0">Nuevos por ??rea</label>
                                    </div>
                                </div>
                                <div class="col-md-12 text-right colAreas">
                                    <div class="form-group mb-0 mt-3 groupResp">
                                        <input type="checkbox" id="checkboxAreasEditar">
                                        <label for="" class="mb-0">Seleccionar todos</label>
                                        <div class="float-left mb-0">
                                            <span style="font-size: 11px;">
                                                *Se visualizara ??reas con esta actividad asignada
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 text-left colAreas">
                                    <select id="areaAsignarEditar" data-plugin="customselect"
                                        class="form-control form-control-sm select2Multiple" multiple="multiple">
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
{{-- FINALIZACION --}}
{{-- MODAL DE ASIGNACION POR AREAS --}}
<div id="asignarPorArea" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="asignarPorArea"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-lg justify-content-center">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Asignar actividad
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important">
                <div class="row">
                    <div class="col-md-12">
                        <form action="javascript:asignarActividadEmpleado()" id="FormAsignarActividadEmpleado">
                            <div class="row border-bottom pb-2">
                                <div class="col-md-12">
                                    <label class="mb-0">Seleccionar Actividad</label>
                                    <select id="actividadesAsignar" data-plugin="customselect" class="form-control"
                                        required>
                                        <option value="" disabled selected>Seleccionar actividad</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row pt-2">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 text-left">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="a_customAE"
                                                    disabled>
                                                <label class="custom-control-label" for="a_customAE"
                                                    style="font-weight: bold">
                                                    Seleccionar por empleados
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row pt-2 aEmpleado" style="display: none">
                                        <div class="col-md-12 text-left">
                                            <div class="form-group mb-0 mt-2">
                                                <input type="checkbox" id="checkboxEmpleadosTodos">
                                                <label for="" class="mb-0">Todos, incluyendo nuevos</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row pt-2 aEmpleado aNuevos" style="display: none">
                                        <div class="col-md-12 text-right">
                                            <div class="form-group mb-0 mt-3">
                                                <input type="checkbox" id="checkboxEmpleados">
                                                <label for="" class="mb-0">Todos los empleados</label>
                                                <div class="float-left mb-0">
                                                    <span style="font-size: 11px;">
                                                        *Se visualizara empleados con esta actividad asignada
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row aEmpleado aNuevos" style="display: none">
                                        <div class="col-md-12">
                                            <select id="empleAsignar" data-plugin="customselect"
                                                class="form-control form-control-sm select2Multiple"
                                                multiple="multiple">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row pt-3">
                                        <div class="col-md-12 text-left">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="a_customAA"
                                                    disabled>
                                                <label class="custom-control-label" for="a_customAA"
                                                    style="font-weight: bold">
                                                    Seleccionar por ??reas
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row aArea" style="display: none">
                                                <div class="col-md-12 text-left">
                                                    <div class="form-group mb-0 mt-2">
                                                        <input type="checkbox" id="checkboxAreasTodos">
                                                        <label for="" class="mb-0">Nuevos por ??rea</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row pt-2 aArea" style="display: none">
                                                <div class="col-md-12 text-right">
                                                    <div class="form-group mb-0 mt-3">
                                                        <input type="checkbox" id="checkboxAreas">
                                                        <label for="" class="mb-0">Todas las ??reas</label>
                                                        <div class="float-left mb-0">
                                                            <span style="font-size: 11px;">
                                                                *Se visualizara ??reas con esta actividad asignada
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row aArea" style="display: none;">
                                                <div class="col-md-12">
                                                    <select id="areaAsignar" data-plugin="customselect"
                                                        class="form-control form-control-sm select2Multiple"
                                                        multiple="multiple">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
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
                                onclick="javascript:limpiarAsignacion()">Cancelar</button>
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
{{-- FINALIZACION DE MODAL --}}
{{-- MODAL DE SESSION --}}
<div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img src="{{asset('landing/images/notification.svg')}}" height="100">
                <h4 class="text-danger mt-4">Su sesi??n expir??</h4>
                <p class="w-75 mx-auto text-muted">Por favor inicie sesi??n nuevamente.</p>
                <div class="mt-4">
                    <a href="{{('/')}}" class="btn btn-outline-primary btn-rounded width-md"><i
                            class="uil uil-arrow-right mr-1"></i> Iniciar sesi??n</a>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
{{-- FINALIZACION --}}
{{-- visibilidad de switch --}}
@if (isset($modifActi))
@if ($modifActi==1)
<input type="hidden" id="modifActI" value="1">
@else
<input type="hidden" id="modifActI" value="0">
@endif
@else
<input type="hidden" id="modifActI" value="1">
@endif
{{-- permiso de dar de baja --}}
@if (isset($bajaActi))
@if ($bajaActi==1)
<input type="hidden" id="bajaActI" value="1">
@else
<input type="hidden" id="bajaActI" value="0">
@endif
@else
<input type="hidden" id="bajaActI" value="1">
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
<script src="{{asset('landing/js/actividades.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
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