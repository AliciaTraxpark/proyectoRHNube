@php
use App\proyecto_empleado;
@endphp

@extends('layouts.vertical')

@section('css')

<link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet"
    type="text/css" />

<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}"
    rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}"
    rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}"
    rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css')
    }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet"
    type="text/css" />
<link rel="stylesheet"
    href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('breadcrumb')
<style>
    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #ced0d3;
    }
    body > div.bootbox.modal.fade.bootbox-confirm.show > div > div > div.modal-footer > button.btn.btn-primary.bootbox-accept{
        background-color: #163552;
        border-color: #163552;
        zoom: 85%;
    }
    body > div.bootbox.modal.fade.bootbox-confirm.show > div > div > div.modal-footer > button.btn.btn-light.bootbox-cancel{
        background: #e2e1e1;
        color: #000000;
        border-color:#e2e1e1;
        zoom: 85%;
    }
    .table {
        width: 100% !important;
    }

    .dataTables_scrollHeadInner {
        width: 100% !important;
    }
</style>
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0">Tarea - Empleado</h4>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div id="myModal" class="modal fade" tabindex="-1" role="dialog"
                    aria-labelledby="myModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header"
                                style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel"
                                    style="color:#ffffff;font-size:15px">Tarea
                                    nueva</h5>
                                <button type="button" class="close"
                                    data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <form class="form-horizontal col-lg-12"
                                        action="javascript:agregarProyecto()">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group col-lg-12
                                                    row">
                                                    <label class="col-lg-4
                                                        col-form-label"
                                                        for="simpleinput">Nombre
                                                        de
                                                        tarea</label>
                                                    <div class="col-lg-8">
                                                        <input type="text"
                                                            class="form-control"
                                                            id="nombreProyecto"
                                                            value="" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group col-lg-12
                                                    row">
                                                    <label class="col-lg-4
                                                        col-form-label"
                                                        for="example-textarea">Descripcion</label>
                                                    <div class="col-lg-8">
                                                        <textarea
                                                            class="form-control"
                                                            rows="3"
                                                            id="detalleProyecto"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" style="zoom: 90%;"
                                        class="btn btn-light"
                                        data-dismiss="modal">Cerrar</button>
                                    <button type="" style="background-color:
                                        #163552; border-color: #163552; zoom:
                                        90%;" class="btn btn-primary">Guardar</button>
                                </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

                <div id="myModal1" class="modal fade" tabindex="-1"
                    role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" style="max-width: 550px;">
                        <div class="modal-content">
                            <div class="modal-header"
                                style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel"
                                    style="color:#ffffff;font-size:15px">Agregar
                                    miembros</h5>
                                <button type="button" class="close"
                                    data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group col-lg-12
                                                row">
                                                <label class="col-lg-6
                                                    col-form-label"
                                                    for="simpleinput">Nombre de
                                                    tarea:</label>
                                                <div class="col-lg-6">
                                                    <input type="text"
                                                        class="form-control-plaintext"
                                                        id="nombre1"
                                                        disabled>
                                                    <input type="hidden"
                                                        id="id1">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group col-lg-12
                                                row">
                                                <label class="col-lg-4
                                                    col-form-label"
                                                    for="simpleinput">Miembros
                                                    de
                                                    proyecto:</label>
                                                <div class="col-lg-8">

                                                    <select class="form-control
                                                        wide"
                                                        data-plugin="customselect"
                                                        multiple
                                                        id="idempleado">
                                                        @foreach ($empleado as
                                                        $empleados)
                                                        <option class=""
                                                            value="{{$empleados->emple_id}}">
                                                            {{$empleados->perso_nombre}}
                                                            {{$empleados->perso_apPaterno}}
                                                            {{$empleados->perso_apMaterno}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    {{-- <select name=""
                                                        id="prue" class="sel">
                                                        <option value="0">Selecciona</option>
                                                        @foreach ($empleado as
                                                        $empleados)
                                                        <option class=""
                                                            value="{{$empleados->emple_id}}">{{$empleados->perso_nombre}}
                                                            {{$empleados->perso_apPaterno}}
                                                            {{$empleados->perso_apMaterno}}
                                                        </option>
                                                        @endforeach
                                                    </select> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light"
                                    style="zoom:90%" data-dismiss="modal">Cerrar</button>
                                <button type="" style="background-color:
                                    #163552; border-color: #163552; zoom: 90%;"
                                    class="btn btn-primary"
                                    onclick="registrarPE()">Guardar</button>
                            </div>

                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div id="myModal2" class="modal fade" tabindex="-1"
                    role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" style="max-width: 550px;">
                        <div class="modal-content">
                            <div class="modal-header"
                                style="background-color:#163552;">
                                <h5 class="modal-title" id="myModalLabel"
                                    style="color:#ffffff;font-size:15px">Editar
                                    Proyecto</h5>
                                <button type="button" class="close"
                                    data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group col-lg-12
                                                row">
                                                <label class="col-lg-6
                                                    col-form-label"
                                                    for="simpleinput">Nombre de
                                                    tarea:</label>
                                                <div class="col-lg-6">
                                                    <input type="text"
                                                        class="form-control
                                                        form-control-sm"
                                                        id="nombre1_e">
                                                    <input type="hidden"
                                                        id="id1_e" name="id1_e">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group col-lg-12
                                                row">
                                                <label class="col-lg-6
                                                    col-form-label"
                                                    for="simpleinput">Descripcion
                                                    de
                                                    tarea:</label>
                                                <div class="col-lg-6">
                                                    <textarea
                                                        class="form-control"
                                                        rows="3"
                                                        id="detalleProyecto_e"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group col-lg-12
                                                row">
                                                <label class="col-lg-4
                                                    col-form-label"
                                                    for="simpleinput">Miembros
                                                    de
                                                    proyecto:</label>
                                                <div class="col-lg-10"><br>
                                                    <table id="tablaProyectoE"
                                                        class="table" border="1"
                                                        style="border-collapse:
                                                        collapse;border: #e2e7f1
                                                        1px solid;font-size:
                                                        12.5px;">
                                                        <thead>
                                                            <tr>
                                                                <th
                                                                    style="padding:
                                                                    4px;">Nombre
                                                                    de empleado</th>
                                                                <th
                                                                    style="padding:
                                                                    4px;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light"
                                    style="zoom: 90%" data-dismiss="modal">Cerrar</button>
                                <button type="" style="display:
                                    none;background-color: #163552;
                                    border-color: #163552; zoom: 90%;"
                                    class="btn btn-primary"
                                    onclick="guardarEdicion()" id="editarPro">Guardar</button>
                            </div>

                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <div class="row">
                    {{-- <div class="col-md-2">
                        <label for="" style="font-weight: 600">Lista de
                            Proyectos</label>
                    </div> --}}
                    <div class="col-md-5 text-left" style="bottom: 5px;">
                        <button type="button" class="btn btn-secondary btn-sm"
                            data-toggle="modal"
                            data-target="#myModal" style="background: #507394;
                            border-color: #507394;">+ Agregar
                            tarea</button>
                    </div>
                </div>
                <br>

                <table id="tablaProyecto" class="table table-drop dt-responsive nowrap"
                    style="font-size: 12.5px!important">
                    <thead style="background: #fafafa">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Detalle</th>
                            <th>Miembros</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proyecto as $proyectos)
                        @php
                        $proyectoEmp=proyecto_empleado::where('Proyecto_Proye_id','=',$proyectos->Proye_id)->get();
                        @endphp
                        <tr>
                            <th>{{$loop->index+1}}</th>
                            <td>{{$proyectos->Proye_Nombre}}</td>
                            <td>{{$proyectos->Proye_Detalle}}</td>
                            <td>
                                @foreach ( $proyectoEmp as $proyectoEmps)
                                @php
                                $empleado = DB::table('empleado as e')
                                ->join('persona as p', 'e.emple_persona', '=',
                                'p.perso_id')
                                ->select('e.emple_id','p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno')
                                ->where('e.emple_id','=',$proyectoEmps->empleado_emple_id)
                                ->where('e.emple_estado', '=', 1)
                                ->get();

                                @endphp

                                <span> <img src="{{
                                        URL::asset('admin/assets/images/users/empleado.png')
                                        }}"
                                        class="mr-2" alt="" />{{$empleado[0]->perso_nombre
                                    }}
                                    {{$empleado[0]->perso_apPaterno}} &nbsp;</span>
                                @endforeach

                            </td>
                            <td><button style="background:#f0f4fd;
                                    border-color:#f0f4fd; color:#a0add3"
                                    class="btn btn-secondary btn-sm"
                                    onclick="abrirM({{$proyectos->Proye_id}})">+
                                    Miembro </button>&nbsp;&nbsp;&nbsp;&nbsp; <a
                                    id="formNuevoEd"
                                    onclick="editarproyecto({{$proyectos->Proye_id}})"
                                    style="cursor: pointer"><img
                                        src="{{asset('admin/images/edit.svg')}}"
                                        height="15"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                <a
                                    onclick="eliminarp({{$proyectos->Proye_id}});"
                                    style="cursor: pointer"><img
                                        src="{{asset('admin/images/delete.svg')}}"
                                        height="15"></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{asset('landing/js/editarPerfil.js')}}"></script>
<!-- Plugins Js -->
<script src="{{
    URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{asset('landing/js/proyecto.js')}}"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
@endsection

@section('script-bottom')
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
