@extends('layouts.vertical')

@section('css')

    <!-- App css -->
    <link href="{{ asset('admin/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Plugin css  CALENDAR-->


    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('breadcrumb')
    <div id="preloader">
        <div id="status">
            <div class="spinner">
                <div class="circle1"></div>
                <div class="circle2"></div>
                <div class="circle3"></div>
            </div>
        </div>
    </div>
    <div class="row page-title">
        <div class="col-md-12">
            {{-- <h4 class="mb-1 mt-0">Horarios</h4> --}}
            <h4 class="header-title mt-0 "></i>Controladores</h4>
        </div>
    </div>
@endsection


@section('content')
    <style>
        body {
            background-color: #ffffff;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #52565b;
        }
        .flatpickr-calendar{
        width: 240px!important;
         }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fdfdfd;
        }

        .custom-select:disabled {
            color: #3f3a3a;
            background-color: #fcfcfc;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background: #ced0d3;
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

        .col-md-6 .select2-container .select2-selection {
            height: 50px;
            font-size: 12.2px;
            overflow-y: scroll;
        }

    </style>
    <style>
        .table {
            width: 100% !important;
        }

        .dataTables_scrollHeadInner {
            width: 100% !important;
        }

        .table th,
        .table td {
            padding: 0.4rem;
            border-top: 1px solid #edf0f1;
        }

    </style>
    <div class="row row-divided">
        <div class="col-md-12 col-xl-12">
            <div class="card">
                <div class="card-body" style="padding-top: 0px; background: #ffffff; font-size: 12.8px;
                color: #222222;   padding-left:0px; padding-right: 20px; ">
                    <!--<h4 class="header-title mt-0 mb-1">Basic Data Table</h4>-->
                    <div class="row">
                        <div class=" col-md-6 col-xl-6 text-left">
                            <button class="btn btn-sm btn-primary" onclick="NuevoContr()"
                                style="background-color: #183b5d;border-color:#62778c">+ Nuevo Controlador</button>

                            {{-- <button class="btn btn-sm btn-primary"
                                id="btnasignarIncidencia" style="background-color: #183b5d;border-color:#62778c">Asignar
                                incidencias</button> --}}
                        </div>
                    </div>
                    <div id="tabladiv"> <br>
                        <table id="tablaContr" class="table dt-responsive nowrap" style="font-size: 12.8px;">
                            <thead style=" background: #edf0f1;color: #6c757d;">

                                <tr>
                                    <th></th>
                                    <th>Código</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Dispositivo(s)</th>
                                    <th>Correo</th>
                                    <th>Estado</th>

                                </tr>
                            </thead>
                           <tbody>
                                <tr>
                                    <td></td>
                                    <td>777666</td>
                                    <td>Juan Manuel</td>
                                    <td>Santos Cruz</td>
                                    <td><img
                                        src="{{asset('landing/images/telefono-inteligente.svg')}}" height="18">+51968009336</td>
                                    <td>juanmar@gmail.com</td>
                                    <td><button type="button" class="btn btn-soft-info btn-sm">Activo</button></td>

                                </tr>
                            </tbody>

                        </table>
                    </div><br><br><br><br>
                </div> <!-- end card body-->
            </div> <!-- end card -->


            {{-- Modal nuevoControlador --}}
            <div id="nuevoControlador" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 620px;">

                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#163552;">
                            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Nuevo Controlador
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="font-size:12px!important">
                            <div class="row">

                                <div class="col-md-12">
                                    <form id="frmHorNuevo" action="javascript:RegistraContro()">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label >Código de controlador:</label>
                                                    <input type="text" class="form-control form-control-sm" id="" >
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Correo:</label>
                                                    <input type="text"  class="form-control form-control-sm"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Nombres:</label>
                                                    <input type="text"  class="form-control form-control-sm"
                                                        id="" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Ap. Paterno:</label>
                                                    <input type="text"  class="form-control form-control-sm"
                                                        id="" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Ap. Materno:</label>
                                                    <input type="text"  class="form-control form-control-sm"
                                                        id="" required>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Seleccione dispositivo:</label>
                                                    <select data-plugin="customselect" multiple
                                                    class="form-control" data-placeholder="seleccione dispositivo">
                                                    <option></option>
                                                    <option >+51968009336 (Vigilancia Condor)</option>

                                                </select>
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
                                            data-dismiss="modal">Cancelar</button>
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
    </div>
@endsection
@section('script')
    <script src="{{ asset('landing/js/actualizarPDatos.js') }}"></script>
    <!-- Plugins Js -->
    <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
    <script src="{{ asset('landing/js/controladMenu.js') }}"></script>

    <script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/moment/moment.min.js') }}"></script>




@endsection

@section('script-bottom')
    <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection