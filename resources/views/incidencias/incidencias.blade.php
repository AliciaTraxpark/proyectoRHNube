@extends('layouts.vertical')

@section('css')
    <!-- Plugin css  CALENDAR-->


    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
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
            <h4 class="header-title mt-0 "></i>Incidencias</h4>
        </div>
    </div>
@endsection


@section('content')

    <style>
        body {
            background-color: #ffffff;
        }
        .botonsms{
            background-color: #ffffff;
            border-color: #ffffff;
            color: #62778c;
            padding-top: 0px;
            padding-bottom: 0px;
            border-top-width: 0px;
            border-bottom-width: 0px;
            padding-right: 0px;
            padding-left: 0px;
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
        .badge{
            font-size: 11.5px!important;
            font-weight: 500!important;
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


        @media (max-width: 767px) {

            #btnNDis {
                text-align: center !important;
            }


            .fullscreen-modal .modal-dialog .modal-lg {
                width: 400px!important;

            }
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
                        <div id="btnNDis" class=" col-md-6 col-12 text-left">
                            <button class="btn btn-sm btn-primary" onclick="nuevaIncidencia()"
                                style="background-color: #183b5d;border-color:#62778c">+ Nueva incidencia</button>

                            {{-- <button class="btn btn-sm btn-primary"
                                id="btnasignarIncidencia" style="background-color: #183b5d;border-color:#62778c">Asignar
                                incidencias</button> --}}
                        </div>
                    </div>

                    <div id="tabladiv"> <br>
                        <table id="tablaDips" class="table dt-responsive nowrap" style="font-size: 12.8px;">
                            <thead style=" background: #edf0f1;color: #6c757d;">

                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Descrip. de ubicación</th>
                                    <th>Móvil vinculado</th>
                                    <th>Enviar SMS</th>
                                    <th>Código Disp.</th>
                                    <th>Estado</th>
                                    <th>Sig. marcación</th>
                                    <th>T. de sincron.</th>
                                    <th></th>
                                </tr>
                            </thead>
                          {{--  <tbody>
                                <tr>
                                    <td></td>
                                    <td>Vigilancia Condor</td>
                                    <td>+51968009336</td>
                                    <td><img src="landing/images/note.svg" height="20"></td>
                                    <td>(devuelde android)</td>
                                    <td>SI</td>
                                    <td>Creado</td>
                                    <td>5 min</td>
                                    <td>12 min</td>
                                </tr>
                            </tbody> --}}

                        </table>
                    </div><br><br><br><br>
                </div> <!-- end card body-->
            </div> <!-- end card -->


            {{-- Modal registro incidencia --}}
            <div id="registroIncidencia" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true" data-backdrop="static">
                <div id="classMo" class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 640px;">

                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#163552;">
                            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Nueva incidencia
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="font-size:12px!important">
                            <div class="row">

                                <div class="col-md-12 col-12">
                                    <form id="frmHorNuevo" action="javascript">
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="">Seleccione tipo de incidencia:</label>
                                                    <select data-plugin="customselect"  id="selecttipoIncide"
                                                    class="form-control" data-placeholder="seleccione"  required>
                                                    @foreach ($tipo_incidencia as $tipoI)
                                                    <option value=""></option>
                                                    <option value="{{$tipoI->idtipo_incidencia}}">{{$tipoI->tipoInc_descripcion}}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6"></div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="">Descripción:</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="descripcionIncid" maxlength="80" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="">Código:</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="codigoIncid" >
                                                </div>
                                            </div>

                                            <div class="col-md-4 col-12">
                                                <div class="form-group">
                                                    <label for="">Tiempo de sincronización(Min):</label> <span id="errorSincro" style="color: #690f0f;display: none">El valor min es 15.</span>
                                                    <input type="number" id="tiempoSin" min="15" value="15"  required class="form-control form-control-sm"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-12">

                                                <div class="form-group">
                                                    <label for="">Siguiente marcación(Min):</label> <span id="errorMarca" style="color: #690f0f;display: none">El valor min es 5.</span>
                                                    <input type="number" id="smarcacion" min="5" value="5" required class="form-control form-control-sm"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <div class="form-group">
                                                    <label for="">Mantener data por(Hr):</label> <span id="errorData" style="color: #690f0f;display: none">El valor min es 24.</span>
                                                    <input type="number" id="tiempoData" min="24" value="48"  required class="form-control form-control-sm"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <label for=""><br></label>
                                                <div class="form-check">
                                                    <input type="checkbox"  class="form-check-input" id="smsCheck" checked>
                                                    <label class="form-check-label" for="smsCheck" style="margin-top: 2px;">Enviar SMS ahora.</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="">Seleccione tipo de lectura:</label>
                                                    <select data-plugin="customselect" multiple="multiple" id="selectLectura"
                                                    class="form-control"  required>
                                                    <option class="" value="1">Manual</option>
                                                    <option class="" value="2">Escáner</option>
                                                    <option class="" value="3">Cámara</option>
                                                </select>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12 col-12">
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
    <script src="{{ asset('landing/js/incidencias.js') }}"></script>

    <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>

    <script src="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <script> $("#tablaDips").css("width","100%");     </script>


@endsection

@section('script-bottom')
    <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection
