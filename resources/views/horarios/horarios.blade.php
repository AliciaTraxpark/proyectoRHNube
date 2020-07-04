<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gestion de  empleados</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="https://rhsolution.com.pe/wp-content/uploads/2019/06/small-logo-rh-solution-64x64.png" sizes="32x32">

    <!-- App css -->
    <link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
     <!-- Plugin css  CALENDAR-->
    <link href="{{asset('admin/packages/core/main.css')}}" rel="stylesheet" />
    <link href="{{asset('admin/packages/daygrid/main.css')}}" rel="stylesheet" />
    <link href="{{asset('admin/packages/timegrid/main.css')}}" rel="stylesheet" />

    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
</head>
<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100" style="background-color: #fdfdfd;">
<style>
    .fc-event, .fc-event-dot {
    background-color: #d1c3c3;
    font-size: 11.2px!important;
    cursor:url("../landing/images/cruz1.svg"), auto;

}
.fc-toolbar.fc-header-toolbar{
    zoom:80%;
}

    .container{
        margin-left: 40px;
    margin-right: 28px;
    }
    .fc-time{
        display: none;
    }

    .table th, .table td{
        padding: 0.55rem;

    border-top: 1px solid #c9c9c9;

    }

    .sw-theme-default > ul.step-anchor > li.active > a{
        color: #1c68b1 !important;
    }
    .sw-theme-default > ul.step-anchor > li.done > a, .sw-theme-default > ul.step-anchor > li > a {
        color: #0b1b29!important;
    }

    .day{
        max-width: 25%;
    }
    .month{
        max-width: 35%;
    }
    .year{
        max-width: 40%;
    }
    .btn-group{
        width: 100%;
        justify-content: space-between;
    }
    .btn-secondary{
        max-width: 9em;
    }

    body{
        background-color: #f8f8f8;
    }
    .flatpickr-calendar{
        width: 240px!important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice{
        background-color: #52565b;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
        color: #fdfdfd;
    }
    .fc th.fc-widget-header{
        background: #dfe6f2;
    font-size: 13px;
    color: #163552;
    line-height: 20px;
    padding: 5px 0;
    text-transform: uppercase;
    font-weight: 500;
    }
    .custom-select:disabled {
    color: #3f3a3a;
    background-color: #fcfcfc;
    }
</style>
<div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="circle1"></div>
            <div class="circle2"></div>
            <div class="circle3"></div>
        </div>
    </div>
</div>
  <header id="header-section">
    <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
    <div class="container">
        <div class="col-md-2 col-xl-2" >
            <div class="navbar-brand-wrapper d-flex w-100">
                <img src="{{asset('landing/images/logo.png')}}" height="100" >
              </div>
        </div>
        <div class="col-md-8 col-xl-8">
          <h5 style="color: #ffffff">Gestión de empleados</h5>
          <label for="" class="blanco font-italic">Asignemos los turnos y horarios
        </label>

        </div>
        <div class="col-md-2 text-right">
            <button class="btn btn-sm btn-primary" onclick="finalizar()" style="background-color: #183b5d;border-color:#62778c">Finalizar</button>
        </div>
    </div>
    </nav>
  </header>
    <div class="content-page" style="margin-top: 20px;margin-left: 0px">
        <div class="content">
            <div class="row row-divided">
                <div class="col-md-12 col-xl-12">
                    <div class="card">
                        <div class="card-body" style="padding-top: 0px; background: #fdfdfd; font-size: 12.8px;
                        color: #222222;   padding-left: 60px; padding-right: 80px; ">
                            <!--<h4 class="header-title mt-0 mb-1">Basic Data Table</h4>-->
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="header-title mt-0 "></i>Búsqueda de empleado</h4>
                                </div>
                                <div class=" col-md-6 col-xl-6 text-right">
                                    <button class="btn btn-sm btn-primary" id="btnasignar" style="background-color: #183b5d;border-color:#62778c">Asignar horarios</button>
                                    <button class="btn btn-sm btn-primary" id="btnasignarIncidencia" style="background-color: #183b5d;border-color:#62778c">Asignar incidencias</button>
                                </div>
                            </div>
                                <div id="tabladiv">
                                </div>
                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                    <div id="asignarHorario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog  modal-xl d-flex justify-content-center" style="margin-top: 5px">

                        <div class="modal-content">
                           <div class="modal-header" style="background-color:#163552;">
                               <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Asignar horario</h5>
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                           </div>
                           <div class="modal-body" style="font-size: 13.5px!important">
                               <div class="row">

                                   <div class="col-md-6">
                                    <form id="formulario" action="javascript:agregarHoras()">
                                       <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                               <label for="">Asignar empleado(s):</label>
                                               {{-- <input type="text" class="form-control form-control-sm" id="nombreEmpleado"> --}}
                                               <select class="form-control wide" data-plugin="customselect" multiple id="nombreEmpleado" >
                                               {{--  @foreach ($empleado as $empleados)
                                                    <option class="" value="{{$empleados->emple_id}}">{{$paises->nombre}}</option>
                                            @endforeach --}}
                                            </select>
                                            </div>
                                         </div>
                                         <div class="col-md-12">
                                            <label for="">Calendario:</label>
                                         </div>
                                        <div class="col-md-4 ">
                                            <select  class="form-control custom-select custom-select-sm" placeholder="pais" name="pais" id="pais">
                                                <option value="">PAIS</option>
                                                    @foreach ($pais as $paises)
                                                        @if($paises->id==173)
                                                            <option class="" selected="true" value="{{$paises->id}}" >{{$paises->nombre}}</option>
                                                        @else
                                                            <option class="" value="{{$paises->id}}">{{$paises->nombre}}</option>
                                                        @endif
                                                    @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select  class="form-control custom-select custom-select-sm" placeholder="Departamento " name="departamento" id="departamento" style="display: flex;">
                                                <option value="">DEPARTAMENTO</option>
                                                 @foreach ($departamento as $departamentos)
                                                <option class="" value="{{$departamentos->id}}">{{$departamentos->name}}</option>
                                                 @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 text-left">
                                           <button  id="nuevoCalendario" class="btn btn-light btn-sm" type="button" style="padding-top: 5px; padding-bottom:5px;background: #d1e1ef;color:#1b4165;border: none;" >cambiar de calendario &nbsp; </button>
                                        </div>
                                        <div class="col-md-12"><br></div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                               <label for="">Tipo de horario:</label>
                                               <select class="form-control custom-select custom-select-sm" id="tipHorario">
                                                 <option>Normal</option>
                                                 <option>Guardía</option>
                                                 <option>Nocturno</option>
                                               </select>
                                            </div>
                                          </div>
                                        <div class="col-md-6"><label for=""><br></label>
                                            <div class="form-check">

                                              <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                              <label class="form-check-label" for="exampleCheck1">Aplicar sobretiempo</label>
                                            </div>
                                         </div>

                                         <div class="col-md-6">
                                            <div class="form-group">
                                               <label for="">Descripcion:</label>
                                               <input type="text" class="form-control form-control-sm" id="descripcionCa" required>
                                            </div>
                                         </div>
                                         <div class="col-md-6">
                                            <div class="form-group">
                                               <label for="">Hora de inicio(24h):</label>
                                               <input type="text" id="horaI" class="form-control form-control-sm" required>
                                            </div>
                                         </div>
                                         <div class="col-md-6">
                                            <div class="form-group">
                                               <label for="">Hora de fin(24h):</label>
                                               <input type="text" id="horaF" class="form-control form-control-sm" required>
                                            </div>
                                         </div>
                                         <div class="col-md-6">
                                            <div class="form-group">
                                               <label for="">Tolerancia(Min):</label>
                                               <input type="number"  class="form-control form-control-sm" id="toleranciaH" required>
                                            </div>
                                         </div>
                                         <div class="col-md-12 text-left">
                                             {{-- <button type="submit" class="btn btn-light btn-sm" id="aplicarHorario" style="background: #5f88a4; color: #fff;">Aplicar horario a seleccion</button> --}}
                                             <label for="" style="font-weight: 600">Seleccione dias para agregar el horario establecido -></label>
                                         </div>

                                       </div>
                                     </form>
                                   </div>

                                    <div class="col-md-6" >
                                     <div class="row">

                                      </div>
                                      <div class="col-md-12 text-right" id="Datoscalendar" style=" max-width: 100%;">
                                        <div id="calendar">
                                        </div>
                                      </div>
                                      <input type="hidden" id="horarioEnd">
                                      <input type="hidden" id="horarioStart">

                                      <div class="col-md-12 text-right" id="Datoscalendar1" style=" max-width: 100%;">
                                        <div id="calendar1">
                                        </div>
                                      </div>


                                    </div>

                               </div>

                           </div>
                           <div class="modal-footer">
                               <div class="col-md-12">
                                   <div class="row">
                                       <div class="col-md-12 text-right" >
                                        <button type="button" id="" class="btn btn-light " data-dismiss="modal">Cancelar</button>
                                        <button type="button" id="guardarTodoHorario" name="" style="background-color: #163552;" class="btn ">Guardar</button>

                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div><!-- /.modal-content -->
                     </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <div id="verhorarioEmpleado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog  modal-xl d-flex justify-content-center" style="margin-top: 5px">

                        <div class="modal-content">
                           <div class="modal-header" style="background-color:#163552;">
                               <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Horario de empleado</h5>
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                           </div>
                           <div class="modal-body">
                               <div class="row">

                                   <div class="col-md-5">

                                       <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                               <label for="">Empleado:</label>
                                               {{-- <input type="text" class="form-control form-control-sm" id="nombreEmpleado"> --}}
                                               <input type="text" class="form-control form-control-sm" id="idEmHorario" disabled>
                                               <input type="hidden" id="idobtenidoE">
                                            </div>
                                         </div>
                                         <div class="col-md-12">
                                            <label for="">Calendario:</label>
                                         </div>
                                        <div class="col-md-6 ">
                                            <label for="">Pais:</label>
                                            <select  class="form-control custom-select custom-select-sm" placeholder="pais" name="paisHorario" id="paisHorario" disabled>
                                                <option value="">PAIS</option>
                                                    @foreach ($pais as $paises)
                                                        @if($paises->id==173)
                                                            <option class="" selected="true" value="{{$paises->id}}" >{{$paises->nombre}}</option>
                                                        @else
                                                            <option class="" value="{{$paises->id}}">{{$paises->nombre}}</option>
                                                        @endif
                                                    @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="">Departamento:</label>
                                            <select  class="form-control custom-select custom-select-sm" placeholder="Departamento" name="departamentoHorario" id="departamentoHorario" style="display: flex;" disabled>
                                                <option >Ninguno</option>
                                                 @foreach ($departamento as $departamentos)
                                                <option class="" value="{{$departamentos->id}}">{{$departamentos->name}}</option>
                                                 @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-12"><br></div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                               <label for="">Tipo de horario:</label>
                                               <select class="form-control custom-select custom-select-sm" id="tipHorarioEmpleado" disabled>
                                                 <option>Normal</option>
                                                 <option>Guardía</option>
                                                 <option>Nocturno</option>
                                               </select>
                                            </div>
                                          </div>
                                        <div class="col-md-6"><label for=""><br></label>
                                            <div class="form-check">

                                              <input type="checkbox" class="form-check-input" id="exampleCheck2" >
                                              <label class="form-check-label" for="exampleCheck2">Sobretiempo</label>
                                            </div>
                                         </div>

                                         <div class="col-md-6">
                                            <div class="form-group">
                                               <label for="">Descripcion:</label>
                                               <input type="text" class="form-control form-control-sm" id="descripcionCaHorario" disabled>
                                            </div>
                                         </div>

                                         <div class="col-md-6">
                                            <div class="form-group">
                                               <label for="">Tolerancia(Min):</label>
                                               <input type="number"  class="form-control form-control-sm" id="toleranciaHorario" disabled>
                                            </div>
                                         </div>
                                         <div class="col-md-12">
                                            <div class="accordion custom-accordionwitharrow" id="accordionExample">

                                                <div class="card mb-1 shadow-none border" style="1px solid #b0bdcd !important;background-color: #f7f8f9;">
                                                    <a href="" class="text-dark collapsed" data-toggle="collapse"
                                                        data-target="#collapseTwo" aria-expanded="false"
                                                        aria-controls="collapseTwo">
                                                        <div class="card-header" id="headingTwo">
                                                            <label for="" style="font-weight: 600">
                                                                Asignar horario <i
                                                                    class="uil uil-angle-down float-right accordion-arrow"></i>
                                                            </label>
                                                        </div>
                                                    </a>

                                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                                        data-parent="#accordionExample">
                                                        <div class="card-body text-muted" style="padding-top: 0px; padding-bottom: 20px;">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                       <label for="">Hora de inicio(24h):</label>
                                                                       <input type="text" id="horaIhorario" class="form-control form-control-sm" required>
                                                                    </div>
                                                                 </div>
                                                                 <div class="col-md-6">
                                                                    <div class="form-group">
                                                                       <label for="">Hora de fin(24h):</label>
                                                                       <input type="text" id="horaFhorario" class="form-control form-control-sm" required>
                                                                    </div>
                                                                 </div>
                                                                 <div class="col-md-6">
                                                                    <div class="form-group">
                                                                       <label for="">Tolerancia(Min):</label>
                                                                       <input type="number"  class="form-control form-control-sm" id="nuevaTolerancia" >
                                                                    </div>
                                                                 </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- <div class="card mb-0 shadow-none border">

                                                    <a href="" class="text-dark collapsed" data-toggle="collapse"
                                                        data-target="#collapseThree" aria-expanded="false"
                                                        aria-controls="collapseThree">
                                                        <div class="card-header" id="headingThree">
                                                            <label for="" style="font-weight: 600">
                                                                Asignar incidencia <i
                                                                    class="uil uil-angle-down float-right accordion-arrow"></i>
                                                            </label>
                                                        </div>
                                                    </a>

                                                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                                        data-parent="#accordionExample">
                                                        <div class="card-body text-muted" style="padding-top: 0px; padding-bottom: 20px;">
                                                            <div class="row">
                                                                <div class="col-md-5">
                                                                    <div class="form-group">
                                                                       <label for="">Descripcion:</label>
                                                                       <input type="text" class="form-control form-control-sm" id="descripcionInci" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4"><label for=""><br></label>
                                                                    <div class="form-check">
                                                                      <input type="checkbox" class="form-check-input" id="descuentoCheck">
                                                                      <label class="form-check-label" for="descuentoCheck">Aplicar descuento</label>
                                                                    </div>
                                                                 </div>
                                                                 <div class="col-md-4">
                                                                    <label for=""><br></label>
                                                                    <div class="custom-control custom-switch mb-2">
                                                                        <input type="checkbox" class="custom-control-input" id="customSwitch1">
                                                                        <label class="custom-control-label" for="customSwitch1">Asignar mas de 1 dia</label>
                                                                    </div>
                                                                 </div>

                                                                 <div class="col-md-4">
                                                                    <div class="form-group">
                                                                       <label for="">Fecha inicio:</label>
                                                                       <input type="date" id="fechaI" class="form-control form-control-sm" required>
                                                                    </div>
                                                                 </div>
                                                                 <div class="col-md-4" id="divFfin">
                                                                    <div class="form-group">
                                                                       <label for="">fecha fin:</label>
                                                                       <input type="date" id="fechaF" class="form-control form-control-sm" >
                                                                    </div>
                                                                 </div>
                                                                 <div class="col-md-4" id="divhora">
                                                                    <div class="form-group">
                                                                       <label for="">Hora de salida(24h):</label>
                                                                       <input type="text" id="horaInciden" class="form-control form-control-sm" >
                                                                    </div>
                                                                 </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            </div>
                                         </div>






                                       </div>
                                   </div>

                                    <div class="col-md-7" >
                                     <div class="row">

                                      </div>
                                      <div class="col-md-12 text-right" id="DatoscalendarH" style=" max-width: 100%;">

                                        <div id="calendarHorario">
                                        </div>
                                      </div>
                                      <input type="hidden" id="horarioEndH">
                                      <input type="hidden" id="horarioStartH">

                                      <div class="col-md-12 text-right" id="DatoscalendarH1" style=" max-width: 96%;">
                                        <div id="calendar1Horario">
                                        </div>
                                      </div>


                                    </div>

                               </div>

                           </div>
                           <div class="modal-footer">
                               <div class="col-md-12">
                                   <div class="row">
                                       <div class="col-md-12 text-right" >
                                        {{-- <button type="button" id="" class="btn btn-light " data-dismiss="modal">Cancelar</button> --}}
                                        <button type="button" id="cerrarHorario" name="" style="background-color: #d9dee2;color: #171413;" class="btn ">Cerrar</button>
                                        <button type="button" id="guardarHorarioEventos" name="guardarHorarioEventos"  style="background-color: #163552; display: none;" class="btn ">Guardar</button>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div><!-- /.modal-content -->
                     </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <div id="asignarIncidencia" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog  modal-lg d-flex justify-content-center" >

                        <div class="modal-content">
                           <div class="modal-header" style="background-color:#163552;">
                               <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Asignar Incidencia</h5>
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                           </div>
                           <div class="modal-body">
                               <div class="row">

                                   <div class="col-md-12">
                                    <form id="frmIncidencia" action="javascript:registrarIncidencia()">
                                       <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                               <label for="">Asignar empleado(s):</label>
                                               {{-- <input type="text" class="form-control form-control-sm" id="nombreEmpleado"> --}}
                                               <select class="form-control wide" data-plugin="customselect" multiple id="empIncidencia" required>
                                                 {{-- <option value="">hj</option> --}}
                                            </select>
                                            </div>
                                         </div>

                                        <div class="col-md-5">
                                            <div class="form-group">
                                               <label for="">Descripcion:</label>
                                               <input type="text" class="form-control form-control-sm" id="descripcionInci" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4"><label for=""><br></label>
                                            <div class="form-check">
                                              <input type="checkbox" class="form-check-input" id="descuentoCheck">
                                              <label class="form-check-label" for="descuentoCheck">Aplicar descuento</label>
                                            </div>
                                         </div>
                                         <div class="col-md-4">
                                            <label for=""><br></label>
                                            <div class="custom-control custom-switch mb-2">
                                                <input type="checkbox" class="custom-control-input" id="customSwitch1">
                                                <label class="custom-control-label" for="customSwitch1">Asignar mas de 1 dia</label>
                                            </div>
                                         </div>

                                         <div class="col-md-4">
                                            <div class="form-group">
                                               <label for="">Fecha inicio:</label>
                                               <input type="date" id="fechaI" class="form-control form-control-sm" required>
                                            </div>
                                         </div>
                                         <div class="col-md-4" id="divFfin">
                                            <div class="form-group">
                                               <label for="">fecha fin:</label>
                                               <input type="date" id="fechaF" class="form-control form-control-sm" >
                                            </div>
                                         </div>
                                         <div class="col-md-4" id="divhora">
                                            <div class="form-group">
                                               <label for="">Hora de salida(24h):</label>
                                               <input type="text" id="horaInciden" class="form-control form-control-sm" >
                                            </div>
                                         </div>


                                       </div>

                                   </div>



                               </div>

                           </div>
                           <div class="modal-footer">
                               <div class="col-md-12">
                                   <div class="row">
                                       <div class="col-md-12 text-right" >
                                        <button type="button"  class="btn btn-light " data-dismiss="modal">Cancelar</button>
                                        <button type="submit"  name="" style="background-color: #163552;" class="btn ">Guardar</button>
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
        <footer class="border-top">
            <p class="text-center text-muted pt-4">© <?php echo date("Y"); ?> - RH Solution | Todos los derechos reservados.</p>
        </footer>
      </div>
    </div>


    <!-- Vendor js -->
    {{-- <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script> --}}
    <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>
    <!-- App js -->
    <script src="{{asset('admin/assets/js/app.min.js')}}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{asset('landing/js/horario.js')}}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{asset('admin/assets/libs/moment/moment.min.js')}}"></script>
    <script src="{{asset('admin/packages/core/main.js')}}"></script>
    <script src="{{asset('admin/packages/core/locales/es.js')}}"></script>
    <script src="{{asset('admin/packages/daygrid/main.js')}}"></script>
    <script src="{{asset('admin/packages/timegrid/main.js')}}"></script>
    <script src="{{asset('admin/packages/interaction/main.js')}}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
    <script>
        function finalizar(){
            $.ajax({
            type: "post",
            url: "/cambiarEstado",

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                $(location).attr('href','/');
            },
            error: function (data) {
                alert('Ocurrio un error');
            }

        });
        }
    </script>
</body>
</html>
