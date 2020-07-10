
@extends('layouts.vertical')

@section('css')

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

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        {{-- <h4 class="mb-1 mt-0">Horarios</h4> --}}
        <h4 class="header-title mt-0 "></i>Horarios</h4>
    </div>
</div>
@endsection


@section('content')
<style>
  div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content.fc-sun{

background-color: rgb(255, 239, 239) !important;
}
div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content.fc-mon, td.fc-day.fc-widget-content.fc-tue, td.fc-day.fc-widget-content.fc-wed,
td.fc-day.fc-widget-content.fc-thu, td.fc-day.fc-widget-content.fc-fri, td.fc-day.fc-widget-content.fc-sat{

background-color: #f9f9f9 !important;
}
    .fc-event, .fc-event-dot {
    background-color: #d1c3c3;
    font-size: 12.2px!important;
    margin: 2px 2px;
    cursor:url("../landing/images/cruz1.svg"), auto;
    font-weight: 600;





}
.fc-toolbar.fc-header-toolbar{
    zoom:80%;
}


    .fc-time{
        display: none;
    }


    .sw-theme-default > ul.step-anchor > li.active > a{
        color: #1c68b1 !important;
    }
    .sw-theme-default > ul.step-anchor > li.done > a, .sw-theme-default > ul.step-anchor > li > a {
        color: #0b1b29!important;
    }

    .btn-group{
        width: 100%;
        justify-content: space-between;
    }
    .btn-secondary{
        max-width: 9em;
    }

    body{
        background-color: #ffffff;
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
    tr:first-child > td > .fc-day-grid-event{
        margin-top: 0px;
    padding-top: 0px;
    padding-bottom: 0px;
    margin-bottom: 0px;
        margin-left: 2px;
        margin-right: 2px;
    }

    .fc th.fc-widget-header{
        background: #dfe6f2;
    font-size: 13px;
    color: #163552;
    line-height: 20px;
    padding: 5px 0;
    text-transform: uppercase;
    font-weight: 600;
    }
    .custom-select:disabled {
    color: #3f3a3a;
    background-color: #fcfcfc;
    }
    .select2-container--default .select2-results__option[aria-selected=true]{
        background: #ced0d3;
    }
    body > div.bootbox.modal.fade.bootbox-confirm.show > div > div > div.modal-footer > button.btn.btn-light.bootbox-cancel{
        background: #e2e1e1;
        color: #000000;
        border-color:#e2e1e1;
        zoom: 85%;
    }
    body > div.bootbox.modal.fade.bootbox-alert.show > div > div > div.modal-footer > button,body > div.bootbox.modal.fade.bootbox-confirm.show > div > div > div.modal-footer > button.btn.btn-success.bootbox-accept{
        background-color: #163552;
        border-color: #163552;
        zoom: 85%;
    }
</style>
<div class="row row-divided">
    <div class="col-md-12 col-xl-12">
        <div class="card">
            <div class="card-body" style="padding-top: 0px; background: #ffffff; font-size: 12.8px;
            color: #222222;   padding-left:0px; padding-right: 20px; ">
                <!--<h4 class="header-title mt-0 mb-1">Basic Data Table</h4>-->
                <div class="row">
                    <div class="col-md-6">
                        <h4 style="font-weight: 500" class="header-title mt-0 "></i>Búsqueda de empleado</h4>
                    </div>
                    <div class=" col-md-6 col-xl-6 text-right">
                        <button class="btn btn-sm btn-primary" id="btnasignar" style="background-color: #183b5d;border-color:#62778c">Asignar horarios</button>
                        <button class="btn btn-sm btn-primary" id="btnasignarIncidencia" style="background-color: #183b5d;border-color:#62778c">Asignar incidencias</button>
                    </div>
                </div>
                    <div id="tabladiv">
                    </div><br><br><br><br>
            </div> <!-- end card body-->
        </div> <!-- end card -->
        <div id="asignarHorario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog  modal-xl d-flex justify-content-center" style="margin-top: 5px;">

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
            <div class="modal-dialog  modal-xl d-flex justify-content-center" style="margin-top: 5px;">

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
                                   <input type="hidden" id="docEmpleado">
                                   <input type="hidden" id="correoEmpleado">
                                   <input type="hidden" id="celEmpleado">
                                   <input type="hidden" id="areaEmpleado">
                                   <input type="hidden" id="cargoEmpleado">
                                   <input type="hidden" id="ccEmpleado">
                                   <input type="hidden" id="localEmpleado">

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

                                        <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo"
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
                          <div class="col-md-12 text-right" id="DatoscalendarH">

                            <div id="calendarHorario">
                            </div>
                          </div>
                          <input type="hidden" id="horarioEndH">
                          <input type="hidden" id="horarioStartH">

                          <div class="col-md-12 text-right" id="DatoscalendarH1" style=" max-width: 96%;">
                            <div id="calendar1Horario">
                            </div>
                          </div>
                          <div class="col-md-12" >
                              <br>
                              <div class="row" style="padding-left:2px;">
                                <div class="col-md-6 text-left">
                                    <button style="background-color: #dcc3c3; border-color: #ffffff; color: #44444c"  class="btn btn-sm  btn-primary" onclick="screenshot();"><img src="{{asset('admin/images/pdf2.svg')}}" height="24" ></i>  Descargar</button>
                                  </div>
                               {{-- <button type="button" id="" class="btn btn-light " data-dismiss="modal">Cancelar</button> --}}
                                  <div class="col-md-6 text-right">
                                      <button type="button" id="cerrarHorario" name="" style="background-color: #d9dee2;color: #171413;" class="btn ">Cerrar</button>
                                      <button type="button" id="guardarHorarioEventos" name="guardarHorarioEventos"  style="background-color: #163552; display: none;" class="btn ">Guardar</button>
                                  </div>
                              </div>
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
@endsection
@section('script')
<!-- Plugins Js -->

<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
<script src="{{asset('landing/js/horario.js')}}"></script>

<script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{asset('admin/assets/libs/moment/moment.min.js')}}"></script>
<script src="{{asset('admin/packages/core/main.js')}}"></script>
<script src="{{asset('admin/packages/core/locales/es.js')}}"></script>
<script src="{{asset('admin/packages/daygrid/main.js')}}"></script>
<script src="{{asset('admin/packages/timegrid/main.js')}}"></script>
<script src="{{asset('admin/packages/interaction/main.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>

<script>
    function screenshot(){
        html2canvas(document.querySelector("#calendarHorario > div.fc-view-container > div > table"),{
            useCORS: true,
  allowTaint: true,
  letterRendering: true,
            onrendered: function(canvas) {
            var ctx = canvas.getContext('2d');
  ctx.webkitImageSmoothingEnabled = false;
  ctx.mozImageSmoothingEnabled = false;
  ctx.imageSmoothingEnabled = false;}
        }).then(canvas => {
    document.body.appendChild(canvas)
});

        console.log(html2canvas(document.querySelector('#calendarHorario > div.fc-view-container')));
        html2canvas(document.querySelector('#calendarHorario > div.fc-view-container'), {
          useCORS: true,
  allowTaint: true,
  letterRendering: true,
            onrendered: function(canvas) {
            var ctx = canvas.getContext('2d');
  ctx.webkitImageSmoothingEnabled = false;
  ctx.mozImageSmoothingEnabled = false;
  ctx.imageSmoothingEnabled = false;
            // console.log(canvas.toDataURL());
              var image = canvas.toDataURL("image/jpg");
              console.log("image => ",image); //image in base64
              var pHtml = "<img src="+image+" />";
             // $("#parent").append(pHtml); //you can append image tag anywhere
            var doc = new jsPDF();
            var specialElementHandlers = {
      '#getPDF': function(element, renderer){
        return true;
      },
      '.controls': function(element, renderer){
        return true;
      }
    };

    // All units are in the set measurement for the document
    // This can be changed to "pt" (points), "mm" (Default), "cm", "in"
    doc.setFontSize(11);
    doc.setTextColor(48, 47, 44);
    doc.text(80,10,'DATOS DE EMPLEADO')
    doc.text(25,25, 'Num. Documento: ' + $('#docEmpleado').val());
    doc.text(120,25, 'Área: ' + $('#areaEmpleado').val());
    doc.text(25,30, 'Nombre: ' + $('#idEmHorario').val());
    doc.text(120,30, 'Cargo: ' + $('#cargoEmpleado').val());
    doc.text(25,35, 'Correo: ' + $('#correoEmpleado').val());
    doc.text(120,35, 'Centro costo: ' + $('#ccEmpleado').val());
    doc.text(25,40, 'Celular: ' + $('#celEmpleado').val());
    doc.text(120,40, 'Local: ' + $('#localEmpleado').val());
    doc.fromHTML($('#calendarHorario > div.fc-toolbar.fc-header-toolbar > div.fc-center').get(0), 85, 45, {
      'width': 170,
      'elementHandlers': specialElementHandlers
    });

                  doc.addImage(image, 'JPG',25,60);
                  doc.save('horario.pdf');
              }
          });
  }
  </script>

@endsection

@section('script-bottom')
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
@endsection

