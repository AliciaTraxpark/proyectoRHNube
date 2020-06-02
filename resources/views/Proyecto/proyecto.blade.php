@php
  use App\proyecto_empleado;
@endphp

@extends('layouts.vertical')

@section('css')

    <link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0">Proyecto - Empleado</h4>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
             <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel">Proyecto nuevo</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <form class="form-horizontal col-lg-12" action="javascript:agregarProyecto()">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group col-lg-12 row">
                                                <label class="col-lg-4 col-form-label" for="simpleinput">Nombre de proyecto</label>
                                                <div class="col-lg-8">
                                                    <input type="text" class="form-control" id="nombreProyecto" value="" required>
                                                </div>
                                            </div>
                                        </div>
                                       <div class="col-lg-12">
                                        <div class="form-group col-lg-12 row">
                                            <label class="col-lg-4 col-form-label"
                                                for="example-textarea">Descripcion</label>
                                            <div class="col-lg-8">
                                                <textarea class="form-control" rows="3"
                                                    id="detalleProyecto"></textarea>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                              </div>
                           </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                            <button type="" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

                <div id="myModal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true">
                <div class="modal-dialog " style="max-width: 550px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel">Agregar miembros</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <form class="form-horizontal col-lg-12" action="javascript:registrarPE()">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group col-lg-12 row">
                                                <label class="col-lg-6 col-form-label" for="simpleinput">Nombre de proyecto</label>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control-plaintext" id="nombre1"  disabled>
                                                    <input type="hidden"  id="id1">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group col-lg-12 row">
                                                <label class="col-lg-4 col-form-label" for="simpleinput">Miembros de proyecto</label>
                                                <div class="col-lg-8">
                                                    <select data-plugin="customselect" id="idempleado" class="form-control" data-placeholder="Seleccione empleado">
                                                        <option></option>
                                                        @foreach ($empleado as $empleados)
                                                        <option class="" value="{{$empleados->emple_id}}">{{$empleados->perso_nombre}} {{$empleados->perso_apPaterno}} {{$empleados->perso_apMaterno}} </option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

                <div class="button-list">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#myModal">+ Agregar proyecto</button>
                </div><br><br>

                <table id="tablaProyecto" class="table dt-responsive nowrap" style="font-size: 14px!important">
                    <thead>
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
                                ->join('persona as p', 'e.emple_persona', '=', 'p.perso_id')
                                ->select('e.emple_id','p.perso_nombre','p.perso_apPaterno','p.perso_apMaterno')
                                ->where('e.emple_id','=',$proyectoEmps->empleado_emple_id)
                                ->get();

                                @endphp
                                <span>{{$empleado[0]->perso_nombre }} {{$empleado[0]->perso_apPaterno}} {{$empleado[0]->perso_apMaterno }}</span>
                                 @endforeach

                            </td>
                            <td><button  class="btn btn-success btn-sm" onclick="abrirM({{$proyectos->Proye_id}})"
                              >Agregar miembro </button></td>
                        </tr>
                       @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  {{--   <div class="col-xl-8">
        <div class="inbox-rightbar">
            <div class="">
                <ul class="message-list">
                    @foreach ($empleado as  $empleados)
                    <li class="unread">
                        <div class="col-mail col-mail-1">

                            <div class="checkbox-wrapper-mail">
                                <input type="checkbox" id="chk1">
                                <label for="chk1" class="toggle"></label>
                            </div>
                                <a class="title">{{$empleados->perso_nombre}} {{$empleados->perso_apPaterno}} {{$empleados->perso_apMaterno}}</a>
                        </div>
                        <div class="col-mail col-mail-1">
                            <a class="subject badge badge-success">Proyecto 1</a>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div> --}}
</div>
@endsection
@section('script')
<!-- Plugins Js -->


<script src="{{ URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{asset('landing/js/proyecto.js')}}"></script>

@endsection

@section('script-bottom')
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
@endsection

