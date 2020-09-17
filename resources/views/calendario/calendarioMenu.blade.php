@php
use App\proyecto_empleado;
@endphp

@extends('layouts.vertical')

@section('css')
<link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet"
    type="text/css" />
<link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet"
    type="text/css" />
<link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet"
    type="text/css" />

{{-- <link
    href="{{asset('admin/assets/libs/bootstrap-fileinput/fileinput.min.css')}}"
    rel="stylesheet"
    type="text/css" /> --}}
    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/packages/core/main.css')}}" rel="stylesheet" />
<link href="{{asset('admin/packages/daygrid/main.css')}}" rel="stylesheet" />
<link href="{{asset('admin/packages/timegrid/main.css')}}" rel="stylesheet" />
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

<style>
   /*  body > div.bootbox.modal.fade.show > div > div > div{
        background: #131313;
    color: #fbfbfb;
    }
    body > div.bootbox.modal.fade.show > div{
        top: 100px;
    left: 75px;
    } */

div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content.fc-mon, td.fc-day.fc-widget-content.fc-tue, td.fc-day.fc-widget-content.fc-wed,
td.fc-day.fc-widget-content.fc-thu, td.fc-day.fc-widget-content.fc-fri, td.fc-day.fc-widget-content.fc-sat{

background-color: #ffffff;
}
    .fc-time{
        display: none;
    }
    .fc-Descanso-button{
    color: #fff;
    background-color: #162029;
    }
    .fc-NoLaborales-button{
    color: #fff;
    background-color: #162029;
    }
    .fc-Feriado-button{
    color: #fff;
    background-color: #162029;
    }
    .fc-nuevoAño-button{
    left: 10px;
    font-size: 12px;
    padding-left: 6px;
    padding-right: 6px;

    }
     .fc-Asignar-button{
    left: 10px;
    font-size: 12px;
    padding-left: 6px;
    padding-right: 6px;
    padding-bottom: 7px;
    padding-top: 8px;

    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice{
        background-color: #52565b;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
        color: #fdfdfd;
    }
    .col-md-6 .select2-container .select2-selection {
    height: 50px;
    font-size: 12.2px;
    overflow-y: scroll;
}
.select2-container--default .select2-results__option[aria-selected=true]{
        background: #ced0d3;
    }
    .table td{
        padding-top: 0.3rem;
    padding-bottom: 0.3rem;
    }
    .fc-button{
        background: #163552;
        color: #ffffff;
    }
</style>
<div class="row page-title" style="padding-right: 20px;">
    <div class="col-md-7">

        <h4 class="mb-1 mt-0">Calendarios</h4>
        <input type="hidden" name="idorgani" id="idorgani" value="{{session('sesionidorg')}}">
        <input type="hidden" name="" id="AñoOrgani" value="{{$fechaEnvi}}">

        <input type="hidden" id="fechaEnviF" >
    </div>

    <div class="col-md-3 ">
       <select name="" id="selectCalendario" class="form-control">
            @foreach ($calendario as $calendarios)
                <option class="" value="{{$calendarios->calen_id}}">{{$calendarios->calendario_nombre}}</option>
            @endforeach
        </select>

    </div>
    <div class="col-md-2">
        <button  onclick="abrirNcalendario()" class="boton" style="font-size: 12px;padding: 4px" >+ Nuevo calendario</button>
    </div>
    <br><br><br><br>
    <div class="col-md-1"></div>
    <div class="col-md-9" id="calendar">

    </div>
    &nbsp;&nbsp;&nbsp;<div class="col-md-1" style="top:86px">
        <div class="row">
            <div class="col-md-6" style="  background: #f9e9e9;
            height: 35px;"><h1>&nbsp;</h1></div>
            <div class="col-md-6"><label style="font-size: 12px" for="">Dias no laborales</label></div>
        </div>
        <div class="row">
            <div class="col-md-6" style="  background: #ffffff;border: 1px solid #d4d4d4;
            height: 35px;"><h1>&nbsp;</h1></div>
              <div class="col-md-6"><label style="font-size: 12px" for="">Dias laborables</label></div>
        </div><br><br>
        <div class="row">
        <div class="col-md-12"><label style="font-size: 12px;font-style:oblique;font-weight: 600" for="">Calendario programado</label></div>
        <div class="col-md-12"><label style="font-size: 12px;font-style:oblique;font-weight: 600" for="">De:</label></div>
        <div class="col-md-12"><label style="font-size: 12px" for="">{{$fechaEnvi}}</label></div>
        <div class="col-md-12"><label style="font-size: 12px;font-style:oblique;font-weight: 600" for="">Hasta:</label></div>
        <div class="col-md-12"><label style="font-size: 12px" for="" id="fechaHasta"></label></div>
        </div>


    </div>
    <div class="col-md-1"></div>
    <div class="col-md-11"><br>
    <label for="" style="font-style:oblique">Fecha de creacion de empresa: {{$fechaOrga->format('d/m/Y')}}</label>
    </div>
   {{--  <form action="javascript:agregarcalendario()"  class="col-md-5">
        <div class="row">
            <div class="col-md-8"> <input type="text" class="form-control" id="nombreCalen" placeholder="Nombre nuevo calendario" required></div>
        <div class=" col-md-3">
       <button  id="nuevoCalendario" type="submit"class="boton" style="font-size: 12px;padding: 4px" >Nuevo</button>
        </div>
        </div>
    </form> --}}
</div>
@endsection

@section('content')
<div id="myModal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel"
                    style="color:#ffffff;font-size:15px">Días de descanso</h5>
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <h5>¿Asignar dias de descanso?</h5>
                <input type="hidden" id="fechaDa" name="fechaDa">

                {{-- <label for="start" class="col-sm-4 col-form-label">Fecha
                    Inicial:</label> --}}

                <input type="hidden" name="start" class="form-control"
                    id="start" readonly>



                {{-- <label for="start" class="col-sm-4 col-form-label">Fecha
                    Final:</label> --}}

                <input type="hidden" name="end" class="form-control" id="end"
                    readonly>


                <input type="hidden" name="title" id="title" value="Descanso">
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light"
                                data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right:
                            38px;">
                            <button type="button" id="guardarDescanso"
                                name="guardarDescanso" class="btn
                                btn-secondary">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="myModalFestivo" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel"
                    style="color:#ffffff;font-size:15px">Días no laborales</h5>
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <h5>¿Asignar dias no laborales?</h5>
                <input type="hidden" id="fechaDa2" name="fechaDa2">

                {{-- <label for="start" class="col-sm-4 col-form-label">Fecha
                    Inicial:</label> --}}

                <input type="hidden" name="startF" class="form-control"
                    id="startF" readonly>

                {{-- <label for="start" class="col-sm-4 col-form-label">Fecha
                    Final:</label> --}}

                <input type="hidden" name="endF" class="form-control" id="endF"
                    readonly>

                <input type="hidden" name="titleN" id="titleN" value="No
                    laborable">

            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light"
                                data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right:
                            38px;">
                            <button type="button" id="guardarNoLab"
                                name="guardarNoLab" class="btn btn-secondary">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="myModalEliminarD" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header" style="background-color:#163552;">
              <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días de descanso</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
                      <form class="form-horizontal">
                        <h5 style="font-size: 14px" class="modal-title" id="myModalLabel">¿Desea eliminar días descanso?</h5>
                        <input type="hidden" id="idDescansoEl">
                      </form>


          </div>
          <div class="modal-footer">
              <div class="col-md-12">
                  <div class="row">
                      <div class="col-md-12 text-right">
                          <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                          <button type="button" onclick="EnviarDescansoE()" style="background-color: #163552;" class="btn btn-sm">Eliminar</button>
                      </div>

                  </div>
              </div>
          </div>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="myModalFeriado" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel"
                    style="color:#ffffff;font-size:15px">Agregar nuevo feriado</h5>
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <div class="col-md-6">
                            <label for="">Nombre de dia feriado:</label>
                        </div>
                         <div class="col-md-10">
                             <form action="javascript:registrarDferiado()">
                            <input class="form-control" type="text" id="nombreFeriado" required>
                         </div>

                    </div>
                </div>
                <input type="hidden" name="startFeriado" class="form-control"
                    id="startFeriado" readonly>

                <input type="hidden" name="endFeriado" class="form-control"
                    id="endFeriado" readonly>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light"
                                data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right: 38px; ">
                            <button type="submit"  class="btn btn-secondary">Aceptar</button>
                        </form>

                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="myModalEliminarN" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel"
                    style="color:#ffffff;font-size:15px">Días no Laborales</h5>
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">



                    <form class="form-horizontal">
                      <h5 style="font-size: 14px" class="modal-title" id="myModalLabel">¿Desea eliminar días no Laborales?</h5>
                      <input type="hidden" id="idnolabEliminar">
                    </form>


        </div>
        <div class="modal-footer">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                        <button type="button" onclick="eliminarEvNL()" style="background-color: #163552;" class="btn btn-sm">Eliminar</button>
                    </div>

                </div>
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="myModalEliminarFeriado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel"
                    style="color:#ffffff;font-size:15px">Días no Laborales</h5>
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <h5 style="font-size: 14px" class="modal-title" id="myModalLabel">¿Desea eliminar día feriado?</h5>
                    <input type="hidden" id="idFeriadoeliminar">
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                            <button type="button" onclick="eliminarEvF()" style="background-color: #163552;" class="btn btn-sm">Eliminar</button>
                        </div>

                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="agregarCalendarioN" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 550px;" >

        <div class="modal-content">
           <div class="modal-header" style="background-color:#163552;">
               <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Nuevo calendario</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div>
           <div class="modal-body" style="font-size:12px!important">
               <div class="row">

                   <div class="col-md-12">
                    <form id="" action="javascript:agregarcalendario()">
                       <div class="row">
                        <div class="col-md-12"> <input type="text" class="form-control" id="nombreCalen" placeholder="Nombre nuevo calendario" required><br></div>
                        <div class="col-md-5 form-check" style="padding-left: 32px; margin-top: 4px;">
                            <input type="checkbox"  class="form-check-input" id="clonarCheck">
                            <label class="form-check-label" for="clonarCheck" >Clonar calendario de:</label>
                        </div>
                        <div class="col-md-7">
                            <select name="" id="selectClonar" class="form-control form-control-sm" disabled >
                                <option hidden selected>Seleccione calendario</option>
                                @foreach ($calendario as $calendarios)
                                    <option class="" value="{{$calendarios->calen_id}}">{{$calendarios->calendario_nombre}}</option>
                                @endforeach
                            </select>

                        </div>
                       </div>

                   </div>
               </div>

           </div>
           <div class="modal-footer">
               <div class="col-md-12">
                   <div class="row">
                       <div class="col-md-12 text-right" >
                        <button type="button"  class="btn btn-light btn-sm " data-dismiss="modal">Cancelar</button>
                        <button type="submit"  name="" style="background-color: #163552;" class="btn btn-sm ">Guardar</button>
                    </form>
                       </div>
                   </div>
               </div>
           </div>
       </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="añadirNuevoa" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 550px;" >

        <div class="modal-content">
           <div class="modal-header" style="background-color:#163552;">
               <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Añadir año</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div>
           <div class="modal-body" style="font-size:12px!important">
               <div class="row">

                   <div class="col-md-12">
                    <form id="" action="javascript:editarfinC()">
                       <div class="row">
                        <div class="col-md-12" >
                            <input type="text" id="textoNuevoAño" class="col-md-12" style="font-size: 15px; background-color: rgb(255, 255, 255);
                            border: 0;">
                            <input type="hidden" id="añotNuevo">
                        </div>
                       </div>

                   </div>
               </div>

           </div>
           <div class="modal-footer">
               <div class="col-md-12">
                   <div class="row">
                       <div class="col-md-12 text-right" >
                        <button type="button"  class="btn btn-light  " data-dismiss="modal">Cancelar</button>
                        <button type="submit"  name="" style="background-color: #163552;" class="btn">Aceptar</button>
                    </form>
                       </div>
                   </div>
               </div>
           </div>
       </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="calendarioEmple" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog  modal-lg d-flex modal-dialog-scrollable justify-content-center " style="width: 790px;" >

        <div class="modal-content">
           <div class="modal-header" style="background-color:#163552;">
               <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Asignar empleados</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div>
           <div class="modal-body" style="font-size:12px!important">
               <div class="row">
                    <div class="col-md-12">
                        <label for="" id="textCalend" style="font-size: 14px;  font-weight: 600;"></label> &nbsp;
                        <button type="button" class="btn btn-sm mt-1" onclick="$( '#tableDataem' ).toggle();" style="background-color: #163552;color: #f9f9f9;margin-bottom: 6px;" ><i style="height: 16px" data-feather="eye"></i>ver empleados
                        </button>
                    </div>
                    <div class="col-md-12" id="tableDataem" style="display: none">
                        <br><label style="font-size: 14px; ">Lista de empleados:</label>
                       <div class="col-md-12" style="    padding-left: 0px;">
                        <table id='tabEmpleado' width='100%'  class="table  nowrap">
                            <thead>
                              <tr>
                                <td>Nombres</td>
                                <td>Apellido paterno </td>
                                <td>Apellido materno </td>
                              </tr>
                            </thead>
                          </table>
                       </div>

                   </div>
                   <div class="col-md-12" style="border-bottom: 1px solid #f1f1f1;
                   padding-bottom: 12px;">

                    <form id="asignacionCa"  action="javascript:asignarCalendario()">
                       <div class="row">
                        <div class="col-md-9" style="zoom:90%;">
                            <input type="hidden" id="fechaDa" name="fechaDa">
                            <label for="" style="font-weight: 600;">Seleccionar empleado(s):</label>
                        </div>
                        <div class="col-md-7" style="zoom:90%;">
                            <div class="row" style="margin-left: 6px;">
                                <div class="col-md-5 form-check">
                                    <input type="checkbox"  class="form-check-input" id="selectTodoCheck">
                                    <label class="form-check-label" for="selectTodoCheck" style="font-style: oblique;margin-top: 2px;">Seleccionar todos.</label>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-6">
                               <select class="form-control wide" data-plugin="customselect" multiple id="nombreEmpleado"  required>
                                @foreach ($empleado as  $empleados)
                                <option value="{{$empleados->emple_id}}">{{$empleados->perso_nombre}} {{$empleados->perso_apPaterno}} {{$empleados->perso_apMaterno}}</option>
                              @endforeach
                            </select>

                         </div>
                         <div class="col-md-2">
                             <label for="" style="margin-top: 9px;" >Seleccionar por:</label>
                         </div>
                         <div class="row col-md-4">
                            <select data-plugin="customselect"  id="selectEmpresarial" name="selectEmpresarial" class="form-control" data-placeholder="seleccione">
                                <option value=""></option>
                                @foreach ($area as $areas)
                                <option value="{{$areas->idarea}}">Area : {{$areas->descripcion}}.</option>
                                @endforeach
                                @foreach ($cargo as $cargos)
                                <option value="{{$cargos->idcargo}}">Cargo : {{$cargos->descripcion}}.</option>
                                @endforeach
                                @foreach ($local as $locales)
                                <option value="{{$locales->idlocal}}">Local : {{$locales->descripcion}}.</option>
                                @endforeach
                            </select>
                         </div><br>

                       </div>

                   </div>

                <div id="espera" class="col-md-12 text-center" style="display: none">

                    <img src="{{ asset('landing/images/loading.gif') }}" height="100">
                </div>
                <br><br>
                <div class="col-md-6"><br><label style="font-size: 14px; ">Empleados seleccionados:</label></div>
                  <div class="col-md-6 text-right" style="padding-right: 24px;margin-bottom: 10px;"><br><button type="submit" class="btn  btn-sm" style="background-color: #163552;color: #f9f9f9;">Asignar calendario</button></div>
                <br><br> </form>
                <div class="col-md-12">

                   <div class="col-md-12" style="    padding-left: 0px;">
                    <table id='empleadosSele' width='100%'  class="table  nowrap">
                        <thead>
                          <tr>
                              <td><input type="checkbox" class="ml-4" name="" id="selectEmps"></td>
                            <td>Nombres</td>
                            <td>Apellido paterno </td>
                            <td>Apellido materno </td>
                            <td>Calendario</td>
                          </tr>
                        </thead>
                        <tbody>

                        </tbody>
                      </table>
                   </div>


               </div>


               </div>

           </div>
           <div class="modal-footer">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-light"
                            data-dismiss="modal">Cerrar</button>
                    </div>

                </div>
            </div>
        </div>

       </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<div class="row " >





    <div id="calendarioAsignar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 720px;  margin-top: 185px; left: 94px;" >

        <div class="modal-content">
           {{-- <div class="modal-header" style="background-color:#163552;">
               <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Asignar</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div> --}}
           <div class="modal-body" style="font-size:12px!important;background: #f3f3f3;">
               <div class="row">
                   <div class="col-md-4">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="registrarDdescanso()" ><img src="{{asset('admin/images/dormir.svg')}}" height="20"> Dia de descanso</button>
                   </div>
                   <div class="col-md-4">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="$('#nombreFeriado').val('');$('#calendarioAsignar').modal('hide'); $('#myModalFeriado').modal('show')" ><img src="{{asset('admin/images/calendario.svg')}}" height="20">  Dia feriado</button>
                   </div>
                   <div class="col-md-4">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="registrarDnlaborables()" ><img src="{{asset('admin/images/evento.svg')}}" height="20">  Dia no laborable</button>
                   </div>
               </div>
           </div>
           <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;background: #f1f0f0;">
               <div class="col-md-12">
                   <div class="row">
                       <div class="col-md-12 text-right" >
                        <button type="button"  class="btn btn-soft-primary btn-sm " data-dismiss="modal">Cancelar</button>

                    </form>
                       </div>
                   </div>
               </div>
           </div>
       </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
       <input type="hidden" id="pruebaStar">
       <input type="hidden" id="pruebaEnd">


@endsection
@section('script')
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{asset('landing/js/SeleccionarPais.js')}}"></script>

<!-- Vendor js -->
{{-- <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>
 --}}
<!-- plugin js -->
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{asset('admin/assets/libs/moment/moment.min.js')}}"></script>
<script src="{{asset('admin/packages/core/main.js')}}"></script>
<script src="{{asset('admin/packages/core/locales/es.js')}}"></script>
<script src="{{asset('admin/packages/daygrid/main.js')}}"></script>
<script src="{{asset('admin/packages/timegrid/main.js')}}"></script>
<script src="{{asset('admin/packages/interaction/main.js')}}"></script>
 <script src="{{asset('landing/js/calendario.js')}}"></script>
 <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
 <script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
 <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
@endsection
