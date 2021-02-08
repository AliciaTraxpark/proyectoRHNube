@extends('layouts.vertical')
@section('css')

{{-- <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    --}}
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
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

    #body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-light.bootbox-cancel {
        background: #e2e1e1;
        color: #000000;
        border-color: #e2e1e1;
        zoom: 85%;
    }

    body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-success.bootbox-accept {
        background-color: #163552;
        border-color: #163552;

    }

    body>div.bootbox.modal.fade.show>div {
        top: 100px;
        left: 75px;
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

    @media (max-width: 767px) {
        .colResp {
            text-align: center !important;
        }
        .bootbox.modal.fade.bootbox-confirm.show>div{
            width: 80%;
            left: 10%;
            padding: 0px !important;
            margin: 0px !important;
        }
    }
    table{
        table-layout: fixed;

    }

    #colTable1{
        width: 60px;
        word-wrap: break-word;
    }

    #colTable2, #colTable3{

        width: 180px;
        word-wrap: break-word;
    }

    #colTable4{
        width: 150px;
        word-wrap: break-word;
    }

    #colTable5{
        width: 130px;
        word-wrap: break-word;
    }

    #colTable6{
        width: 160px;
        word-wrap: break-word;
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
                        <button type="button" class="btn btn-secondary btn-sm" onclick="abrirRegist()" style="background: #163552;
                                        border-color: #163552;">+ Invitar miembro</button>

                    </div>
                </div>
                <br>
                <div class="col-xl-12 col-sm-12">
                    <div class="table-responsive-xl">
                        <table id="tablaInvit" class="table" style="font-size: 12.5px!important">
                            <thead style="background: #fafafa">
                                <tr>
                                    <th id="colTable1">#</th>
                                    <th id="colTable2">Correo</th>
                                    <th id="colTable3">Rol</th>
                                    <th id="colTable4">Invitacion</th>
                                    <th id="colTable5">Estado</th>
                                    <th id="colTable6">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invitado as $invitados)

                                <tr>
                                    <th id="colTable1">{{ $loop->index + 1 }}</th>
                                    <td id="colTable2">{{ $invitados->email_inv }}</td>
                                    <td id="colTable3">{{ $invitados->rol_nombre }}</td>
                                    <td id="colTable4"><button class="btn btn-sm botonsms" style="color: #6c757d" onclick="reenviarEmail({{ $invitados->idinvitado }})">Reenviar <img src="landing/images/note.svg" height="15"  ></button></td>
                                    <td id="colTable5">
                                        @if ($invitados->estado==0)
                                        <img src="{{ asset('admin/images/advertencia.svg') }}" height="15"> No
                                        confirmado
                                        @else
                                        <img src="{{ asset('admin/images/checkH.svg') }}" height="15"> Confirmado
                                        @endif
                                    </td>
                                    <td id="colTable6">
                                        <div class="row">
                                            <div class="col-md-2 col-2 colResp mr-1">
                                                <a id="" onclick="editarInv({{ $invitados->idinvitado }})" style="cursor: pointer">
                                                    <img src="{{ asset('admin/images/edit.svg') }}" height="15">
                                                </a>
                                            </div>
                                            <div class="custom-control custom-switch mb-2">
                                                @if ($invitados->estado_condic==0)
                                                <input type="checkbox" class="custom-control-input" name=checkAc
                                                    id="activaSwitch{{$invitados->idinvitado}}"
                                                    onchange="cambioswitch({{$invitados->idinvitado}})">
                                                <label class="custom-control-label"
                                                    id="lblActiva{{$invitados->idinvitado}}"
                                                    for="activaSwitch{{$invitados->idinvitado}}">Desactivado</label>
                                                @else
                                                <input type="checkbox" class="custom-control-input" name=checkAc
                                                    id="activaSwitch{{$invitados->idinvitado}}"
                                                    onchange="cambioswitch({{$invitados->idinvitado}})" checked>
                                                <label class="custom-control-label"
                                                    id="lblActiva{{$invitados->idinvitado}}"
                                                    for="activaSwitch{{$invitados->idinvitado}}">Activado</label>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="agregarInvitado" class="modal fade"  role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-lg d-flex modal-dialog-scrollable justify-content-center">
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
                                    <div class="form-group">
                                        <label for="">Email de invitado:</label> <span id="spanEm"
                                            style="display: none;color:#911818">*Email ya registrado como invitado o ya
                                            existe en la organizacion.</span>
                                        <input type="email" onblur="verificarSIEdito()" class="form-control form-control-sm" id="emailInvi"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-check" style="border-bottom: 1.5px solid #dedede;padding-bottom: 10px;">
                                        <input type="checkbox" class="form-check-input" id="adminCheck">
                                        <label class="form-check-label" for="adminCheck"
                                            style="margin-top: 2px;font-weight: 600">Invitar como
                                            administrador</label>
                                    </div>
                                </div>
                                <div class="col-md-12" style="top: 10px; display: none" id="divParaSelec">
                                   <span
                                    style="color:#911818">*Eliga al menos una opcion.</span>
                                </div>
                                <div class="col-md-8 " id="divDash">
                                   <br>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="dashboardCheck">
                                        <label class="custom-control-label" for="dashboardCheck"
                                            style="margin-top: 2px;"><i data-feather="home"  style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Ver Dashboard
                                            general</label>
                                    </div>
                                </div>
                                <div class="col-md-8 " id="divCalendario">

                                    <div class="custom-control custom-switch" style="top: 5px">
                                        <input type="checkbox" class="custom-control-input" id="calendarCheck">
                                        <label class="custom-control-label" for="calendarCheck"
                                            style="margin-top: 2px;"><i data-feather="calendar"  style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;">
                                            </i> &nbsp; Gestión de calendario</label>
                                    </div>
                                </div>

                                <div class="col-md-8 " id="divAdminPersona" style="padding-top: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="AlcaAdminCheck">
                                        <label class="custom-control-label" for="AlcaAdminCheck"
                                            style="margin-top: 2px;"><i data-feather="list"  style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Gestión de empleados</label><br>
                                    </div>
                                </div>
                                <div class="col-md-12" id="opcionesGE" style="padding-top: 10px;font-style: oblique;     padding-bottom: 15px; display: none;" >
                                    <div class="row">
                                        <div class="col-md-1 col-xl-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="verCheckG" checked disabled>
                                                <label class="form-check-label" for="verCheckG"
                                                    style="margin-top: 2px;">Ver</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="AgregarCheckG">
                                                <label class="form-check-label" for="AgregarCheckG"
                                                    style="margin-top: 2px;">Agregar</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="ModifCheckG">
                                                <label class="form-check-label" for="ModifCheckG"
                                                    style="margin-top: 2px;">Modificar</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="BajaCheckG">
                                                <label class="form-check-label" for="BajaCheckG"
                                                    style="margin-top: 2px;">Dar de baja</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="ActivCheckG">
                                                <label class="form-check-label" for="ActivCheckG"
                                                    style="margin-top: 2px;"> Gestionar actividades</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8 " id="divGestActivi" style="padding-top: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="gestActiCheck">
                                        <label class="custom-control-label" for="gestActiCheck"
                                            style="margin-top: 2px;"><i data-feather="layers" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Gestión de actividades</label><br>
                                    </div>
                                </div>
                                <div class="col-md-12" id="opcionesActiv" style="padding-top: 10px; font-style: oblique;    padding-bottom: 15px;" >
                                    <div class="row">
                                        <div class="col-md-1 col-xl-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="verCheckActiv" checked disabled>
                                                <label class="form-check-label" for="verCheckActiv"
                                                    style="margin-top: 2px;">Ver</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="AgregarCheckActiv">
                                                <label class="form-check-label" for="AgregarCheckActiv"
                                                    style="margin-top: 2px;">Agregar</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="ModifCheckActiv">
                                                <label class="form-check-label" for="ModifCheckActiv"
                                                    style="margin-top: 2px;">Modificar</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="BajaCheckActiv">
                                                <label class="form-check-label" for="BajaCheckActiv"
                                                    style="margin-top: 2px;">Dar de baja</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                <div class="col-md-8 " id="divControlRe"  style="padding-top: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="ControlReCheck">
                                        <label class="custom-control-label" for="ControlReCheck"
                                            style="margin-top: 2px;"><i data-feather="activity" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Modo: Control remoto</label><br>
                                    </div>
                                </div>
                                <div class="col-md-8 " id="divControlRuta"  style="padding-top: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="ControlRutaCheck">
                                        <label class="custom-control-label" for="ControlRutaCheck"
                                            style="margin-top: 2px;"><i data-feather="map-pin" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Modo: Control en Ruta</label><br>
                                    </div>
                                </div>

                                <div class="col-md-8 " id="divAsisPu"  style="padding-top: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="asistPuertaCheck">
                                        <label class="custom-control-label" for="asistPuertaCheck"
                                            style="margin-top: 2px;"> <i data-feather="check-circle" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Modo: Asistencia en puerta &nbsp; &nbsp; <span id="spanBooCk"
                                            style="display: none;color:#911818">Seleccione al menos una opcion.</span></label>  <br>
                                    </div>
                                </div>
                                <div class="col-md-12" id="opcionesAPuerta" style="padding-top: 10px; font-style: oblique;    padding-bottom: 15px;" >
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Dispositivos y controladores:</label>
                                        </div>
                                        <div class="col-md-1 col-xl-2" >
                                            <div class="form-check">
                                                <input type="checkbox" name="opPuertaN" class="form-check-input" id="verCheckPuerta">
                                                <label class="form-check-label" for="verCheckPuerta"
                                                    style="margin-top: 2px;">Ver</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox"  class="form-check-input" id="AgregarCheckPuerta">
                                                <label class="form-check-label" for="AgregarCheckPuerta"
                                                    style="margin-top: 2px;">Agregar</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="ModifCheckPuerta">
                                                <label class="form-check-label" for="ModifCheckPuerta"
                                                    style="margin-top: 2px;">Modificar</label>
                                            </div>
                                        </div>
                                        <br><br>
                                        <div class="col-md-10" id="divReporteAsis"  >
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label for="">Detalle de asistencia:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="ReporteAsistCheck">
                                                        <label class="form-check-label" for="ReporteAsistCheck"
                                                            style="margin-top: 2px;">Ver reporte de asistencia(por empleado y por fecha)  </label><br>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="MoReporteAsistCheck">
                                                        <label class="form-check-label" for="MoReporteAsistCheck"
                                                            style="margin-top: 2px;">Gestión de detalle de asistencia   </label><br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-12 " id="divExtractor"  style="padding-bottom: 10px; margin-top: 20px; border-bottom: 1.5px solid #dedede;bottom: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="ExtractorCheck">
                                        <label class="custom-control-label" for="ExtractorCheck"
                                            style="margin-top: 2px;"><i data-feather="server" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Extractor RHnube</label><br>
                                    </div>
                                </div>

                                {{-- INICIO CUMPLEAÑOS --}}
                                <label class="pl-2 pt-2"><strong>Notificaciones:</strong></label>
                                <div class="col-md-12 " id="divGestHb" style="padding-top: 5px;" >
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="gestHbCheck">
                                        <label class="custom-control-label" for="gestHbCheck"
                                            style="margin-top: 2px;"><i data-feather="gift" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Gestión de cumpleaños</label><br>
                                    </div>
                                </div>
                                {{-- FIN DE CUMPLEAÑOS --}}
                                {{-- INICIO CONTRATO --}}
                                <div class="col-md-8 " id="divGestContract" style="padding-top: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="gestContractCheck">
                                        <label class="custom-control-label" for="gestContractCheck" style="margin-top: 2px;"><i data-feather="clipboard" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Gestión de contratos</label><br>
                                    </div>
                                </div>
                                {{-- FIN DE CONTRATO --}}


                                <div id="divInvitado" class="col-md-12" style="padding-left: 0px;padding-right: 0px;padding-top: 10px;border-top: 1.5px solid #dedede;top: 10px;">

                                    <div class="col-md-12">
                                        <label for="">Limitar alcance a: &nbsp; <span id="spanChEmple"
                                            style="display: none;color:#911818">*Eliga un modo de seleccion de empleados.</span></label>
                                    </div>
                                    <div class="col-md-6 text-left">
                                        <div class="custom-control custom-switch mb-2">
                                            <input type="checkbox" class="custom-control-input" id="switchEmpS" checked
                                               >
                                            <label class="custom-control-label" for="switchEmpS"
                                                style="font-weight: bold">Seleccionar por empleado</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="divTodoECheck">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="TodoECheck">
                                            <label class="form-check-label" for="TodoECheck"
                                                style="margin-top: 2px;">Todos, incluyendo nuevos</label><br><br>
                                        </div>
                                    </div>
                                    <div id="divEmpleado">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 form-check"
                                                    style="display: flex;justify-content: space-between;">
                                                    <label>Empleado(s):</label>
                                                    <div class="form-check" style="padding-right: 10px">
                                                        <input type="checkbox" style="font-size: 11.4px"
                                                            class="form-check-input" id="selectTodoCheck">
                                                        <label class="form-check-label" for="selectTodoCheck"
                                                            style="font-style: oblique;margin-top: 2px;font-size: 11.4px">Seleccionar
                                                            todos.</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <select class="form-control wide" data-plugin="customselect" multiple
                                                id="nombreEmpleado">
                                                @foreach ($empleado as $empleados)
                                                <option value="{{ $empleados->emple_id }}">{{ $empleados->perso_nombre }}
                                                    {{ $empleados->perso_apPaterno }} {{ $empleados->perso_apMaterno }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <br><br>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-left">
                                        <div class="custom-control custom-switch mb-2">
                                            <input type="checkbox" class="custom-control-input" id="switchAreaS"
                                               >
                                            <label class="custom-control-label" for="switchAreaS"
                                                style="font-weight: bold">Seleccionar por area</label>
                                        </div>
                                    </div>
                                    <div id="divArea">

                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 form-check"
                                                    style="display: flex;justify-content: space-between;">
                                                    <label for="">Seleccionar por area(s):</label>
                                                    <div class="form-check" style="padding-right: 10px">
                                                        <input type="checkbox" style="font-size: 11.4px"
                                                            class="form-check-input" id="selectAreaCheck">
                                                        <label class="form-check-label" for="selectAreaCheck"
                                                            style="font-style: oblique;margin-top: 2px;font-size: 11.4px">Todas las áreas.</label>
                                                    </div>
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
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right" style="padding-right: 6px;">
                    <button type="button" class="btn btn-sm btn-light  " data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGu" style="background-color: #163552;"
                        class="btn  btn-sm">Guardar</button>
                    </form>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="agregarInvitado_edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-lg d-flex modal-dialog-scrollable justify-content-center " >
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Invitar miembro</h5>
                <input id="idInv" type="hidden"><button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important;padding-top: 0px;">
                <div class="row">
                    <div class="col-md-12">
                        <form id="frmInvi_edit" action="javascript:registrarInvit_edit()">
                            <div class="row">

                                <div class="col-md-12"><label for=""></label>
                                    <div class="form-group">
                                        <label for="">Email de invitado:</label> <span id="spanEm_edit"
                                            style="display: none;color:#911818">*Email ya registrado como
                                            invitado</span>
                                        <input type="email" disabled class="form-control form-control-sm"
                                            id="emailInvi_edit" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="adminCheck_edit">
                                        <label class="form-check-label" for="adminCheck_edit"
                                            style="margin-top: 2px;font-weight: 600">Invitar como
                                            administrador</label><br><br>
                                    </div>
                                </div>
                                <div class="col-md-12" style="
                                display: none" id="divParaSelec_edit">
                                    <span
                                     style="color:#911818">*Eliga al menos una opcion.</span>
                                 </div>
                                <div class="col-md-8 " id="divDash_edit">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="dashboardCheck_edit">
                                        <label class="custom-control-label" for="dashboardCheck_edit"
                                            style="margin-top: 2px;font-style: oblique;"><i data-feather="home"  style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Ver Dashboard
                                            general</label><br>
                                    </div>
                                </div>
                                <div class="col-md-8 " id="divCalendario_edit">
                                    <div class="custom-control custom-switch" style="top: 5px">
                                        <input type="checkbox" class="custom-control-input" id="calendarCheck_edit">
                                        <label class="custom-control-label" for="calendarCheck_edit"
                                            style="margin-top: 2px;"><i data-feather="calendar"  style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;">
                                            </i> &nbsp; Gestión de calendario</label>
                                    </div>
                                </div>
                                <div class="col-md-8 " id="divAdminPersona_edit" style="padding-top: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="AlcaAdminCheck_edit">
                                        <label class="custom-control-label" for="AlcaAdminCheck_edit"
                                            style="margin-top: 2px;font-style: oblique;"><i data-feather="list"  style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Gestión de empleados</label><br>
                                    </div>
                                </div>

                                <div class="col-md-12" id="opcionesGE_edit" style="padding-top: 10px;font-style: oblique;     padding-bottom: 15px; display: none;" >
                                    <div class="row">
                                        <div class="col-md-1 col-xl-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="verCheckG_edit" checked disabled>
                                                <label class="form-check-label" for="verCheckG_edit"
                                                    style="margin-top: 2px;">Ver</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="AgregarCheckG_edit">
                                                <label class="form-check-label" for="AgregarCheckG_edit"
                                                    style="margin-top: 2px;">Agregar</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="ModifCheckG_edit">
                                                <label class="form-check-label" for="ModifCheckG_edit"
                                                    style="margin-top: 2px;">Modificar</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="BajaCheckG_edit">
                                                <label class="form-check-label" for="BajaCheckG_edit"
                                                    style="margin-top: 2px;">Dar de baja</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="ActivCheckG_edit">
                                                <label class="form-check-label" for="ActivCheckG_edit"
                                                    style="margin-top: 2px;">Gestionar actividades</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8 " id="divGestActivi_edit" style="padding-top: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="gestActiCheck_edit">
                                        <label class="custom-control-label" for="gestActiCheck_edit"
                                            style="margin-top: 2px;"><i data-feather="layers" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Gestión de actividades</label><br>
                                    </div>
                                </div>
                                <div class="col-md-12" id="opcionesActiv_edit" style="padding-top: 10px; font-style: oblique;    padding-bottom: 15px;" >
                                    <div class="row">
                                        <div class="col-md-1 col-xl-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="verCheckActiv_edit" checked disabled>
                                                <label class="form-check-label" for="verCheckActiv_edit"
                                                    style="margin-top: 2px;">Ver</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="AgregarCheckActiv_edit">
                                                <label class="form-check-label" for="AgregarCheckActiv_edit"
                                                    style="margin-top: 2px;">Agregar</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="ModifCheckActiv_edit">
                                                <label class="form-check-label" for="ModifCheckActiv_edit"
                                                    style="margin-top: 2px;">Modificar</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="BajaCheckActiv_edit">
                                                <label class="form-check-label" for="BajaCheckActiv_edit"
                                                    style="margin-top: 2px;">Dar de baja</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-8 " id="divControlRe_edit"  style="padding-top: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="ControlReCheck_edit">
                                        <label class="custom-control-label" for="ControlReCheck_edit"
                                            style="margin-top: 2px;"><i data-feather="activity" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Modo: Control remoto</label><br>
                                    </div>
                                </div>
                                <div class="col-md-8 " id="divControlRuta_edit"  style="padding-top: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="ControlRutaCheck_edit">
                                        <label class="custom-control-label" for="ControlRutaCheck_edit"
                                            style="margin-top: 2px;"><i data-feather="map-pin" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Modo: Control en ruta</label><br>
                                    </div>
                                </div>

                                <div class="col-md-8 " id="divAsisPu_edit"  style="padding-top: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="asistPuertaCheck_edit">
                                        <label class="custom-control-label" for="asistPuertaCheck_edit"
                                            style="margin-top: 2px;"><i data-feather="check-circle" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Modo: Asistencia en puerta &nbsp; &nbsp; <span id="spanBooCk_edit"
                                            style="display: none;color:#911818">Seleccione al menos una opcion.</span></label><br>
                                    </div>
                                </div>

                                <div class="col-md-12" id="opcionesAPuerta_edit" style="padding-top: 10px; font-style: oblique; display:none ;  padding-bottom: 15px;" >
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Dispositivos y controladores</label>
                                        </div>
                                        <div class="col-md-1 col-xl-2" >
                                            <div class="form-check">
                                                <input type="checkbox"  class="form-check-input" id="verCheckPuerta_edit">
                                                <label class="form-check-label" for="verCheckPuerta_edit"
                                                    style="margin-top: 2px;">Ver</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="AgregarCheckPuerta_edit">
                                                <label class="form-check-label" for="AgregarCheckPuerta_edit"
                                                    style="margin-top: 2px;">Agregar</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="ModifCheckPuerta_edit">
                                                <label class="form-check-label" for="ModifCheckPuerta_edit"
                                                    style="margin-top: 2px;">Modificar</label>
                                            </div>
                                        </div>
                                        <br><br>
                                        <div class="col-md-10" id="divReporteAsis_edit"  >
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <label for="">Detalle de asistencia:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="ReporteAsistCheck_edit">
                                                        <label class="form-check-label" for="ReporteAsistCheck_edit"
                                                            style="margin-top: 2px;">Ver reporte de asistencia(por empleado y por fecha)</label><br>
                                                     </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="MoReporteAsistCheck_edit">
                                                        <label class="form-check-label" for="MoReporteAsistCheck_edit"
                                                            style="margin-top: 2px;">Gestión de detalle de asistencia  </label><br>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 " id="divExtractor_edit"  style="padding-bottom: 10px; margin-top: 20px; border-bottom: 1.5px solid #dedede;bottom: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="ExtractorCheck_edit">
                                        <label class="custom-control-label" for="ExtractorCheck_edit" style="margin-top: 2px;"><i data-feather="server" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Extractor RHnube</label><br>
                                    </div>
                                </div>

                                {{-- INICIO CUMPLEAÑOS --}}
                                <label class="pt-2 pl-2"><strong>Notificaciones: </strong></label>
                                <div class="col-md-12 " id="divGestHb_edit" style="padding-top: 5px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="gestHbCheck_edit">
                                        <label class="custom-control-label" for="gestHbCheck_edit" style="margin-top: 2px;"><i data-feather="gift" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Gestión de cumpleaños</label><br>
                                    </div>
                                </div>
                                {{-- FIN DE CUMPLEAÑOS --}}
                                {{-- INICIO CONTRATO --}}
                                <div class="col-md-8 " id="divGestContract_edit" style="padding-top: 10px;">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="gestContractCheck_edit">
                                        <label class="custom-control-label" for="gestContractCheck_edit"
                                            style="margin-top: 2px;"><i data-feather="clipboard" style="height: 15px !important;width: 15px !important;color:#53575a !important;margin-bottom: 2px;"></i> &nbsp; Gestión de contrato</label><br>
                                    </div>
                                </div>
                                {{-- FIN DE CONTRATO --}}

                                <div id="divInvitado_edit" class="col-md-12"
                                    style="padding-left: 0px;padding-right: 0px; padding-top: 10px;border-top: 1.5px solid #dedede;top: 10px;">
                                    <div class="col-md-12">
                                        <label for="">Limitar alcance a: &nbsp; <span id="spanChEmple_edit"
                                            style="display: none;color:#911818">*Eliga un modo de seleccion de empleados.</span></label>
                                    </div>
                                    <div class="col-md-6 text-left">
                                        <div class="custom-control custom-switch mb-2">
                                            <input type="checkbox" class="custom-control-input" id="switchEmpS_edit"
                                               >
                                            <label class="custom-control-label" for="switchEmpS_edit"
                                                style="font-weight: bold">Seleccionar por empleado</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="divTodoECheck_edit">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="TodoECheck_edit">
                                            <label class="form-check-label" for="TodoECheck_edit"
                                                style="margin-top: 2px;">Todos, incluyendo nuevos</label><br><br>
                                        </div>
                                    </div>
                                    <div id=divEmpleado_edit>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 form-check" style="padding-left: 4px;">
                                                    <label for="" class="col-md-10">Empleado(s):</label>
                                                    <input type="checkbox" style="font-size: 11.4px"
                                                        class="form-check-input" id="selectTodoCheck_edit">
                                                    <label class="form-check-label" for="selectTodoCheck_edit"
                                                        style="font-style: oblique;margin-top: 2px;font-size: 11.4px">Seleccionar
                                                        todos.</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <select class="form-control wide" data-plugin="customselect" multiple
                                                id="nombreEmpleado_edit">
                                                @foreach ($empleado as $empleados)
                                                <option value="{{ $empleados->emple_id }}">{{ $empleados->perso_nombre }}
                                                    {{ $empleados->perso_apPaterno }} {{ $empleados->perso_apMaterno }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <br><br>
                                        </div>
                                    </div>



                                    <div class="col-md-6 text-left">
                                        <div class="custom-control custom-switch mb-2">
                                            <input type="checkbox" class="custom-control-input" id="switchAreaS_edit"
                                               >
                                            <label class="custom-control-label" for="switchAreaS_edit"
                                                style="font-weight: bold">Seleccionar por area</label>
                                        </div>
                                    </div>

                                    <div  id="divArea_edit">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 form-check" style="display: flex;justify-content: space-between;">
                                                    <label for="" class="col-md-10">Seleccionar por area(s):</label>
                                                    <div class="form-check" style="padding-right: 10px">
                                                    <input type="checkbox" style="font-size: 11.4px"
                                                        class="form-check-input" id="selectAreaCheck_edit">
                                                    <label class="form-check-label" for="selectAreaCheck_edit"
                                                        style="font-style: oblique;margin-top: 2px;font-size: 11.4px">Todas las áreas.</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <select data-plugin="customselect" multiple id="selectArea_edit"
                                            name="selectArea_edit" class="form-control" data-placeholder="seleccione">

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
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right" style="padding-right: 6px;">
                    <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGu_edit" style="background-color: #163552;"
                        class="btn btn-sm ">Guardar</button>
                    </form>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection
@section('script')

<!-- Plugins Js -->

<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js') }}"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ asset('landing/js/delegarControl.js') }}"></script>

<script src="{{ asset('landing/js/actualizarPDatos.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
<script src="{{ asset('landing/js/notificacionesUser.js') }}"></script>
@endsection
