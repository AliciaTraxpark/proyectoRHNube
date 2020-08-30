@extends('layouts.vertical')
@section('css')

    <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    --}}
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('breadcrumb')
    <style>
        .select2-container--default .select2-results__option[aria-selected=true] {
            background: #ced0d3;
        }

        .select2-container--default .select2-results__option {
            font-size: 11.5px !important;
        }

        .select2-container--default .select2-results>.select2-results__options {
            max-height: 90px;
        }

        body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-primary.bootbox-accept {
            background-color: #163552;
            border-color: #163552;
            zoom: 85%;
        }

        body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-light.bootbox-cancel {
            background: #e2e1e1;
            color: #000000;
            border-color: #e2e1e1;
            zoom: 85%;
        }

        .table {
            width: 100% !important;
        }

        .dataTables_scrollHeadInner {
            width: 100% !important;
        }

        .select2-container .select2-selection {
            height: 40px;
            font-size: 12.2px;
            overflow-y: scroll;
        }

    </style>
    <div class="row page-title">
        <div class="col-md-12">
            <h4 class="mb-1 mt-0">Delegar Control</h4>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        {{-- <div class="col-md-2">
                            <label for="" style="font-weight: 600">Lista de
                                Proyectos</label>
                        </div> --}}
                        <div class="col-md-5 text-left" style="bottom: 5px;">
                            <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal"
                                data-target="#agregarInvitado" onclick="$('#frmInvi')[0].reset();$('#spanEm').hide();$('#nombreEmpleado').prop('required',true);
                                $('#divInvitado').show();" style="background: #507394;
                                        border-color: #507394;">+ Invitar miembro</button>

                        </div>
                    </div>
                    <br>


                     <table id="tablaInvit"
                        class="table table-drop dt-responsive nowrap" style="font-size: 12.5px!important">
                        <thead style="background: #fafafa">
                            <tr>
                                <th>#</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invitado as $invitados)

                                <tr>
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td>{{ $invitados->email_inv }}</td>
                                    <td>{{ $invitados->rol_nombre }}</td>
                                    <td>
                                        @if ($invitados->estado==0)
                                        No confirmado
                                        @else
                                        Confirmado
                                        @endif

                                    </td>
                                    <td> {{-- <a id=""
                                            onclick=""
                                            style="cursor: pointer"><img src="{{ asset('admin/images/edit.svg') }}"
                                                height="15"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a onclick=" style="cursor: pointer"><img
                                                src="{{ asset('admin/images/delete.svg') }}" height="15"></a> --}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="agregarInvitado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 650px;">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Invitar miembro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="font-size:12px!important;padding-top: 0px;">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="frmInvi" action="javascript:registrarInvit()">
                                <div class="row">

                                    <div class="col-md-12"><label for=""></label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="adminCheck">
                                            <label class="form-check-label" for="adminCheck" style="margin-top: 2px;">Invitar como
                                                administrador</label><br><br>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Email de invitado:</label> <span id="spanEm" style="display: none;color:#911818">*Email ya registrado como invitado</span>
                                            <input type="email" class="form-control form-control-sm" id="emailInvi"
                                                required>
                                        </div>
                                    </div>
                                    <div id="divInvitado" class="col-md-12" style="padding-left: 0px;padding-right: 0px">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 form-check" style="padding-left: 4px;">
                                                    <label for="" class="col-md-10">Empleado(s):</label>
                                                    <input type="checkbox" style="font-size: 11.4px" class="form-check-input"
                                                        id="selectTodoCheck">
                                                    <label class="form-check-label" for="selectTodoCheck"
                                                        style="font-style: oblique;margin-top: 2px;font-size: 11.4px">Seleccionar
                                                        todos.</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <select class="form-control wide" data-plugin="customselect" multiple
                                                id="nombreEmpleado" >
                                                @foreach ($empleado as $empleados)
                                                    <option value="{{ $empleados->emple_id }}">{{ $empleados->perso_nombre }}
                                                        {{ $empleados->perso_apPaterno }} {{ $empleados->perso_apMaterno }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <br><br>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 form-check" style="padding-left: 4px;">
                                                    <label for="" class="col-md-10">Seleccionar por area(s):</label>
                                                    <input type="checkbox" style="font-size: 11.4px" class="form-check-input"
                                                        id="selectAreaCheck">
                                                    <label class="form-check-label" for="selectAreaCheck"
                                                        style="font-style: oblique;margin-top: 2px;font-size: 11.4px">Seleccionar
                                                        todas.</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <select data-plugin="customselect" multiple id="selectArea" name="selectArea"
                                                class="form-control" data-placeholder="seleccione">

                                                @foreach ($area as $areas)
                                                    <option value="{{ $areas->idarea }}">Area : {{ $areas->descripcion }}.
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>



                                </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 text-right" style="padding-right: 6px;">
                        <button type="button" class="btn btn-light  " data-dismiss="modal">Cancelar</button>
                        <button type="submit" id="btnGu" style="background-color: #163552;" class="btn  ">Guardar</button>
                        </form>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('script')

    <!-- Plugins Js -->
    <script src="{{ URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('landing/js/delegarControl.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
    <script src="{{ asset('landing/js/actualizarPDatos.js') }}"></script>
@endsection

@section('script-bottom')
    <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection
