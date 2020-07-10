@php
  use App\proyecto_empleado;
@endphp

@extends('layouts.vertical')

@section('css')
<link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{asset('admin/assets/libs/bootstrap-fileinput/fileinput.min.css')}}" rel="stylesheet"
        type="text/css" />

<link href="{{asset('admin/packages/core/main.css')}}" rel="stylesheet" />
<link href="{{asset('admin/packages/daygrid/main.css')}}" rel="stylesheet" />
<link href="{{asset('admin/packages/timegrid/main.css')}}" rel="stylesheet" />

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
     div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content.fc-sun{

background-color: rgb(255, 239, 239) !important;
}
div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content.fc-mon, td.fc-day.fc-widget-content.fc-tue, td.fc-day.fc-widget-content.fc-wed,
td.fc-day.fc-widget-content.fc-thu, td.fc-day.fc-widget-content.fc-fri, td.fc-day.fc-widget-content.fc-sat{

background-color: #f9f9f9 !important;
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
</style>

<div class="row page-title" style="padding-right: 20px;">
    <div class="col-md-6">

        <h4 class="mb-1 mt-0">Calendarios</h4>
    </div>
    <div class="col-md-2 text-left">
        <select  class="form-control" placeholder="pais" name="pais" id="pais">
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
        <div class="col-md-2 text-left">
            @if(!empty($eventos_usuario))
                <h1>sdfg</h1>
            @endif
            <select  class="form-control" placeholder="Departamento " name="departamento" id="departamento" style="display: flex;">
            <option value="">DEPARTAMENTO</option>
            @foreach ($departamento as $departamentos)
                <option class="" value="{{$departamentos->id}}">{{$departamentos->name}}</option>
            @endforeach
        </select>
        </div>
        <div class=" col-md-2">
        <button  id="nuevoCalendario" type="submit"class="boton" style="font-size: 12px;padding: 4px" >Nuevo</button>
        </div>
</div>
@endsection

@section('content')
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días de descanso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                    <h5>¿Asignar dias de descanso?</h5>
                    <input type="hidden" id="fechaDa" name="fechaDa">

                            {{-- <label for="start" class="col-sm-4 col-form-label">Fecha Inicial:</label> --}}

                                    <input type="hidden" name="start" class="form-control" id="start" readonly>



                            {{-- <label for="start" class="col-sm-4 col-form-label">Fecha Final:</label> --}}

                                <input type="hidden" name="end" class="form-control" id="end" readonly>


                        <input type="hidden" name="title" id="title" value="Descanso">

            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                            <button type="button" id="guardarDescanso" name="guardarDescanso" class="btn btn-secondary">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="myModalFestivo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días no laborales</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                    <h5>¿Asignar dias no laborales?</h5>
                    <input type="hidden" id="fechaDa2" name="fechaDa2">

                            {{-- <label for="start" class="col-sm-4 col-form-label">Fecha Inicial:</label> --}}

                                <input type="hidden" name="startF" class="form-control" id="startF" readonly>

                            {{-- <label for="start" class="col-sm-4 col-form-label">Fecha Final:</label> --}}

                                <input type="hidden" name="endF" class="form-control" id="endF" readonly>

                    <input type="hidden" name="titleN" id="titleN" value="No laborable">

            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                            <button type="button" id="guardarNoLab" name="guardarNoLab" class="btn btn-secondary">Confirmar</button>
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
                        <h5 class="modal-title" id="myModalLabel">¿Desea eliminar días descanso?</h5>
                      </form>


          </div>
          <div class="modal-footer">
              <div class="col-md-12">
                  <div class="row">
                      <div class="col-md-7 text-right">
                          <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                      </div>
                      <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                          <button type="button" id="eliminarDescanso" name="eliminarDescanso" style="background-color: #163552;" class="btn ">Eliminar</button>
                      </div>
                  </div>
              </div>
          </div>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="myModalFeriado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar nuevo feriado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                            <input class="form-control" type="text" id="nombreFeriado">
                         </div>

                    </div>
                </div>
                <input type="hidden" name="startFeriado" class="form-control" id="startFeriado" readonly>

                <input type="hidden" name="endFeriado" class="form-control" id="endFeriado" readonly>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right: 38px; ">
                            <button type="button" id="guardarFeriado" name="guardarFeriado"  class="btn btn-secondary">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="myModalEliminarN" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header" style="background-color: #163552;">
            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días no Laborales</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">



                    <form class="form-horizontal">
                      <h5 class="modal-title" id="myModalLabel">¿Desea eliminar días no Laborales?</h5>
                    </form>


        </div>
        <div class="modal-footer">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-7 text-right">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    </div>
                    <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                        <button type="button" id="eliminarNLaboral" name="eliminarNLaboral" style="background-color: #163552;" class="btn ">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="myModalEliminarDdep" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header" style="background-color: #163552;">
            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días de descanso</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
                    <form class="form-horizontal">
                      <h5 class="modal-title" id="myModalLabel">¿Desea eliminar días descanso?</h5>
                    </form>

        </div>
        <div class="modal-footer">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-7 text-right">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    </div>
                    <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                        <button type="button"  style="background-color: #163552;" id="eliminarDescansodep" name="eliminarDescansodep" class="btn ">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="myModalEliminarNdep" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
  <div class="modal-content">
      <div class="modal-header" style="background-color: #163552;">
          <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días no Laborales</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <div class="modal-body">
                  <form class="form-horizontal">
                    <h5 class="modal-title" id="myModalLabel">¿Desea eliminar días no Laborales?</h5>
                  </form>
      </div>
      <div class="modal-footer">
          <div class="col-md-12">
              <div class="row">
                  <div class="col-md-7 text-right">
                      <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                  </div>
                  <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                      <button type="button" id="eliminarNLaboraldep" name="eliminarNLaboraldep" style="background-color: #163552;" class="btn ">Eliminar</button>
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
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Día feriado de usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <h5 class="modal-title" id="myModalLabel">¿Desea eliminar día feriado?</h5>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                            <button type="button" id="eliminarDiaferi" name="eliminarDiaferi" style="background-color: #163552;" class="btn ">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div id="myModalEliminarFeriadoDep" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Día feriado de usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <h5 class="modal-title" id="myModalLabel">¿Desea eliminar día feriado?</h5>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-7 text-right">
                                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                            </div>
                            <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                                <button type="button" id="eliminarDiaferiDep" name="eliminarDiaferiDep" style="background-color: #163552;" class="btn ">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
<div class="row " >

    <div class="col-md-12 text-center">
      <div class="col-md-7" style="left: 10%;max-width: 80%; " id="Datoscalendar">
          <div class="card">
            <div class="card-body">
                <div class="row">
                   <div class="col-md-11" id="calendar">

                   </div>
                   <div class="col-md-1" style="top:86px">
                       <div class="row">
                           <div class="col-md-6" style="  background: #f9e9e9;
                           height: 35px;"><h1>&nbsp;</h1></div>
                           <div class="col-md-6"><label for="">Dias de Descanso</label></div>
                       </div>
                       <div class="row">
                           <div class="col-md-6" style="  background: #f9f9f9;
                           height: 35px;"><h1>&nbsp;</h1></div>
                             <div class="col-md-6"><label for="">Dias laborables</label></div>
                       </div>

                   </div>
                </div>


             </div> <!-- end card body-->
              <div class="card-footer">
                <div class="row">
                </div>
              </div>
          </div> <!-- end card -->
      </div>
      <div class="col-md-7" id="Datoscalendar1" style="left: 10%;max-width: 80%;">
        <div class="card">
            <div class="card-body">
                <div class="row">
                   <div class="col-md-11" id="calendar1">

                   </div>
                   <div class="col-md-1" style="top:86px">
                       <div class="row">
                           <div class="col-md-6" style="  background: #f9e9e9;
                           height: 35px;"><h1>&nbsp;</h1></div>
                           <div class="col-md-6"><label for="">Dias de Descanso</label></div>
                       </div>
                       <div class="row">
                           <div class="col-md-6" style="  background: #f9f9f9;
                           height: 35px;"><h1>&nbsp;</h1></div>
                             <div class="col-md-6"><label for="">Dias laborables</label></div>
                       </div>

                   </div>
                </div>


             </div> <!-- end card body-->
            <div class="card-footer">
              <div class="row">
              </div>
            </div>
        </div> <!-- end card -->
    </div>
       <input type="hidden" id="pruebaStar">
       <input type="hidden" id="pruebaEnd">
    </div>

</div>

@endsection
@section('script')



<script src="{{asset('admin/assets/js/pages/form-wizard.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{asset('landing/js/SeleccionarPais.js')}}"></script>

<!-- Vendor js -->
<script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>

<!-- plugin js -->
<script src="{{asset('admin/assets/libs/moment/moment.min.js')}}"></script>
<script src="{{asset('admin/packages/core/main.js')}}"></script>
<script src="{{asset('admin/packages/core/locales/es.js')}}"></script>

<script src="{{asset('admin/packages/daygrid/main.js')}}"></script>
<script src="{{asset('admin/packages/timegrid/main.js')}}"></script>
<script src="{{asset('admin/packages/interaction/main.js')}}"></script>
 <script src="{{asset('landing/js/calendario.js')}}"></script>

@endsection



