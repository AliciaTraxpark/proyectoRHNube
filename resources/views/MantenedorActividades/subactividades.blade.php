@extends('layouts.vertical')


@section('css')
    <link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet"
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
            <h4 class="mb-1 mt-0 pl-3" style="font-weight: bold">Subactividades</h4>
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
            @if ($agregarActi == 1)
                <div class="col-md-6 text-left colResponsive">
                    <button type="button" class="btn btn-sm mt-1" style="background-color: #163552;"
                        onclick="abrirRegistroSubact()">+ Nueva
                        Subactividad
                    </button>
                </div>
            @else
            @endif
        @else

            <div class="col-md-6 text-left colResponsive">
                <button type="button" class="btn btn-sm mt-1" style="background-color: #163552;"
                    onclick="abrirRegistroSubact()">+ Nueva
                    Subactividad
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
                    <table id="subActividades" class="table nowrap" style="font-size: 13px!important;width:100%;">
                        <thead style="background: #fafafa;" style="width:100%!important">
                            <tr>
                                <th>#</th>
                                <th>Subctividad</th>
                                <th>Código</th>
                                <th class="text-center">Modo Tareo</th>
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

    {{-- MODAL DE REGISTRAR SUBACTIVIDAD --}}
    <div id="regSubactividad" class="modal fade" role="dialog" aria-labelledby="regSubactividad" aria-hidden="true"
        data-backdrop="static">
        <div class="modal-dialog  modal-lg d-flex justify-content-center">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" style="color:#ffffff;font-size:15px">
                        Registrar Subactividad
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="font-size:12px!important">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="javascript:registrarSubactividad()" id="FormRegistrarSubactividad">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Nombre:</label>
                                            <input type="text" class="form-control form-control-sm" id="nombreSubact"
                                                maxlength="100" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Código:</label>
                                            <input type="text" class="form-control form-control-sm" id="codigoSubact"
                                                maxlength="40">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="mb-0">Seleccionar Actividad: </label> &nbsp; &nbsp;
                                        <br><span style="font-size: 11px;">
                                            *Se visualizara actividades con modo tareo
                                        </span>
                                        <label for=""><br></label>
                                        <select id="actividadesAsignar" data-plugin="customselect" class="form-control"
                                            required>
                                            <option value="" disabled selected>Seleccionar actividad</option>
                                        </select>
                                    </div>
                                    <div id="divNAct" class="col-md-6 text-left colResponsive">
                                        <br> <br>
                                        <button type="button" class="btn btn-sm" title="Registrar nueva actividad"  style="background-color: #163552;margin-top: 12px"
                                         onclick="javascript:abrirNActividad()">+
                                        </button>
                                    </div>
                                    <div class="col-md-12  text-left">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customMTSubact" checked
                                                disabled>
                                            <label class="custom-control-label" for="customMTSubact"
                                                style="font-weight: bold;margin-top: 18px">
                                                <i data-feather="pocket"
                                                    style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                                &nbsp;&nbsp;
                                                Modo tareo
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
                                   >Cancelar</button>
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

    {{-- MODAL DE EDITAR SUBACTIVIDAD --}}
    <div id="editSubactividad" class="modal fade" role="dialog" aria-labelledby="editSubactividad" aria-hidden="true"
        data-backdrop="static">
        <div class="modal-dialog  modal-lg d-flex justify-content-center">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" style="color:#ffffff;font-size:15px">
                       Editar Subactividad
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="font-size:12px!important">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="idSubAct">
                            <form action="javascript:actualizarSubactividad()" id="FormEditarSubactividad">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Nombre:</label>
                                            <input type="text" class="form-control form-control-sm" id="nombreSubact_ed"
                                              disabled  maxlength="100" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Código:</label>
                                            <input type="text" class="form-control form-control-sm" id="codigoSubact_ed"
                                                maxlength="40">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="mb-0">Seleccionar Actividad: </label> &nbsp; &nbsp;
                                        <br><span style="font-size: 11px;">
                                            *Se visualizara actividades con modo tareo
                                        </span>
                                        <label for=""><br></label>
                                        <select id="actividadesAsignar_ed" data-plugin="customselect" class="form-control"
                                            required>
                                            <option value="" disabled selected>Seleccionar actividad</option>
                                        </select>
                                    </div>
                                    <div id="divNAct_ed" class="col-md-6 text-left colResponsive">
                                        <br> <br>
                                        <button type="button" class="btn btn-sm" title="Registrar nueva actividad"  style="background-color: #163552;margin-top: 12px"
                                         onclick="javascript:abrirNActividad()">+
                                        </button>
                                    </div>
                                    <div class="col-md-12  text-left">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customMTSubact_ed" checked
                                                disabled>
                                            <label class="custom-control-label" for="customMTSubact_ed"
                                                style="font-weight: bold;margin-top: 18px">
                                                <i data-feather="pocket"
                                                    style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                                &nbsp;&nbsp;
                                                Modo tareo
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
                                   >Cancelar</button>
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
                            <label for="">Registro de actividad para modo tareo, si desea registrar un actividad completa
                                registre desde el menu actividades.
                            </label>
                            {{-- SETEAMOS R SI SERVIRA PARA REGISTRAR O E PAR EDITAR --}}
                            <input type="hidden" id="TipoRoE">
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
                                            <label for="">Código:</label>
                                            <input type="text" class="form-control form-control-sm" id="codigoTarea"
                                                maxlength="40">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">


                                    <div class="col-md-12 text-left">
                                        <div class="custom-control custom-switch mb-2">
                                            <input type="checkbox" class="custom-control-input" id="customMT" checked disabled>
                                            <label class="custom-control-label" for="customMT" style="font-weight: bold">
                                                <i data-feather="pocket"
                                                    style="height: 15px !important;width: 15px !important;color:#163552 !important"></i>
                                                &nbsp;&nbsp;
                                                Modo tareo
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



    {{-- MODAL DE SESSION --}}
    <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="{{ asset('landing/images/notification.svg') }}" height="100">
                    <h4 class="text-danger mt-4">Su sesión expiró</h4>
                    <p class="w-75 mx-auto text-muted">Por favor inicie sesión nuevamente.</p>
                    <div class="mt-4">
                        <a href="{{ '/' }}" class="btn btn-outline-primary btn-rounded width-md"><i
                                class="uil uil-arrow-right mr-1"></i> Iniciar sesión</a>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    {{-- FINALIZACION --}}
    {{-- visibilidad de switch --}}
    @if (isset($modifActi))
        @if ($modifActi == 1)
            <input type="hidden" id="modifActI" value="1">
        @else
            <input type="hidden" id="modifActI" value="0">
        @endif
    @else
        <input type="hidden" id="modifActI" value="1">
    @endif
    {{-- permiso de dar de baja --}}
    @if (isset($bajaActi))
        @if ($bajaActi == 1)
            <input type="hidden" id="bajaActI" value="1">
        @else
            <input type="hidden" id="bajaActI" value="0">
        @endif
    @else
        <input type="hidden" id="bajaActI" value="1">
    @endif
    @if (Auth::user())
        <script>
            $(function() {
                setInterval(function checkSession() {
                    $.get('/check-session', function(data) {
                        // if session was expired
                        if (data.guest == false) {
                            $('.modal').modal('hide');
                            $('#modal-error').modal('show');

                        }
                    });
                }, 7202000);
            });

        </script>
    @endif
@endsection
@section('script')
    <script src="{{ asset('landing/js/actualizarPDatos.js') }}"></script>
    <script src="{{ asset('landing/js/app-menu.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <!-- optional plugins -->
    <script src="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/multiselect/es.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/alertify/alertify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js') }}"></script>
    <script src="{{ asset('js/select2search.js') }}"></script>
    <script src="{{ asset('landing/js/subActividades.js') }}"></script>
    <script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection
