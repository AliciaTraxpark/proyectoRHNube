
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

    <link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="header-title mt-0 "></i>Asignación masiva de días no laborales</h4>
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

      background-color: #ffffff !important;
  }
      .fc-event, .fc-event-dot {
      background-color: #d1c3c3;
      font-size: 12.2px!important;
      margin: 2px 2px;
      cursor:url("../landing/images/cruz1.svg"), auto;

  }
  a:not([href]):not([tabindex]){
      color: #000;
      cursor: pointer;
      font-size: 12px;
  }
  .fc-event-container> a{
    border: 1px solid #fff;
}
  .fc-toolbar.fc-header-toolbar{
      zoom:80%;
  }
  #calendar > div.fc-toolbar.fc-footer-toolbar > div.fc-left > button,#calendar > div.fc-toolbar.fc-footer-toolbar > div.fc-center, #calendar > div.fc-toolbar.fc-footer-toolbar > div.fc-right > button{
      zoom:90%;
  }
  .buttonc{
          color: #121b7a;
      background-color: #e7e1f7;
      border-color: #e7e1f7;
      }
      #calendar > div.fc-toolbar.fc-header-toolbar > div.fc-center{
          margin-right: 200px;
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
      .select2-container .select2-selection {
    height: 50px;
    font-size: 12.5px;
    overflow-y: scroll;
}
body > div.bootbox.modal.fade.bootbox-alert.show > div > div > div.modal-header{
        background-color: #163552;
    }
    body > div.bootbox.modal.fade.bootbox-alert.show > div > div > div.modal-header > h5{
        color: #fff;
        font-size: 15px!important;
    }
    body > div.bootbox.modal.fade.bootbox-confirm.show > div > div > div.modal-header{
        background-color: #163552;
    }
    body > div.bootbox.modal.fade.bootbox-confirm.show > div > div > div.modal-header > h5{
        color: #fff;
        font-size: 15px!important;
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
<div class="row row-divided">
    <div class="col-md-12 col-xl-12">
        <div class="card">
            <div class="card-body" style="padding-top: 0px; background: #ffffff; font-size: 12.8px;
            color: #222222;   padding-left:0px; padding-right: 20px; ">
                <!--<h4 class="header-title mt-0 mb-1">Basic Data Table</h4>-->
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-9 form-check" style="padding-left: 4px;">
                                    <label for="" class="col-md-8">Seleccionar por area(s):</label>


                                </div>
                                <div class="col-md-3 text-right">
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
                    <div class="col-md-6">
                        <div class="col-xl-12 col-sm-8">
                            <div class="form-group mt-3 mt-sm-0">
                                <label>Empleado</label>
                                <select id="idempleado" data-plugin="customselect" multiple class="form-control form-control-sm" data-placeholder="Seleccione empleado">
                                    <option></option>
                                    @foreach ($empleado as  $empleados)
                                      <option value="{{$empleados->emple_id}}">{{$empleados->perso_nombre}} {{$empleados->perso_apPaterno}} {{$empleados->perso_apMaterno}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>



                    <div class="col-xl-12" id="imgV">
                        <br><br><br>
                        <img id="VacioImg" style="margin-left:33%" src="{{
                            URL::asset('admin/images/undraw_calendar_dutt.svg') }}"
                            class="mr-2" height="200" /> <br> <label for=""
                            style="margin-left:30%;color:#7d7d7d">Realize una
                            búsqueda para ver calendario de empleado</label>
                    </div>
                    <input type="hidden" id="pruebaEnd_ed">
                    <input type="hidden" id="pruebaStar_ed">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-12" id="verinfo" style="display: none">
                                <br><label style="font-weight: 600;color: #03345f">Seleccione y asigne días no laborables, descansos e incidencias.</label>
                            </div>
                        <div id="calendar_ed" style="display: none" class="col-xl-10"> </div>
                        <div class="col-xl-2" id="calendar_ed_bt" style="display: none">
                            <div class="col-md-10" style="top:86px">
                                <div class="row">
                                    <div class="col-md-6" style="  background: #f9e9e9;
                                    height: 35px;"><h1>&nbsp;</h1></div>
                                    <div class="col-md-6"><label for="">Días de Descanso</label></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6" style="  background: #ffffff;border: 1px solid #d4d4d4;
                                    height: 35px;"><h1>&nbsp;</h1></div>
                                      <div class="col-md-6"><label for="">Días laborables</label></div>
                                </div>

                            </div>
                        </div>
                        </div>

                    </div>

                </div>

            </div> <!-- end card body-->
        </div> <!-- end card -->
        <div id="modalIncidencia_ed" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Agregar nueva incidencia
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12">
                        <form id="frmIncidenciaCa_ed" action="javascript:modalIncidencia_ed()">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Descripcion:</label>
                                        <input type="text" class="form-control form-control-sm" id="descripcionInciCa_ed"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6"><label for=""><br></label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="descuentoCheckCa_ed">
                                        <label class="form-check-label" for="descuentoCheckCa_ed">Aplicar descuento</label>
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
                                <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">Cancelar</button>
                                <button type="submit" name="" style="background-color: #163552;"
                                    class="btn btn-sm">Guardar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div id="btnLabo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 720px;  margin-top: 185px; left: 94px;" >

        <div class="modal-content">
           <div class="modal-header" style="background-color:#163552; padding-bottom: 4px;
           padding-top: 4px;">
               <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Asignar día a calendario</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div>
           <div class="modal-body" style="font-size:12px!important;background: #f3f3f3;">
            <div class="col-md-12 " id="btnLabo" >
                <div class="row">
                    <div class="col-md-3">
                    <button type="button"
                    style=" max-width: 18em!important;"
                    class="btn btn-secondary btn-sm"
                    onclick="laborable_ed()"><img
                        src="{{asset('admin/images/dormir.svg')}}"
                        height="20"> Descanso</button>
                    </div>
                    <div class="col-md-3">
                        <button type="button"
                        style=" max-width: 18em!important;"
                        class="btn btn-secondary btn-sm"
                        onclick="nolaborable_ed()"><img
                            src="{{asset('admin/images/evento.svg')}}"
                            height="20"> Día no laborable</button>
                    </div>
                    <div class="col-md-3 text-center">
                        <button type="button" style=" max-width: 18em!important;"
                            class="btn btn-secondary btn-sm"
                            onclick="$('#nombreFeriado_ed').val('');$('#btnLabo').modal('hide'); $('#myModalFeriado_ed').modal('show')"><img
                                src="{{ asset('admin/images/calendario.svg') }}"
                                height="20"> Día feriado</button>
                    </div>
                    <div class="col-md-3">
                        <button type="button"
                        style=" max-width: 18em!important;"
                        class="btn btn-secondary btn-sm"
                        onclick="IncidenciaEmpleados()"><img
                        src="{{asset('admin/images/calendarioInc.svg')}}"
                            height="20"> Incidencia</button>
                    </div>
                </div>
            </div>
           </div>

       </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    </div>
</div>
<div id="myModalFeriado_ed" class="modal fade" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #163552;">
                                                <h5 class="modal-title" id="myModalLabel"
                                                    style="color:#ffffff;font-size:15px">Agregar nuevo
                                                    feriado</h5>
                                                <button type="button" class="close" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <div class="col-md-6">
                                                            <label for="">Nombre de día feriado:</label>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <form action="javascript:diaferiadoRe_ed()">
                                                                <input class="form-control" type="text"
                                                                    id="nombreFeriado_ed" required>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-right">
                                                    <div class="">

                                                            <button type="button" class="btn btn-light"
                                                                data-dismiss="modal">Cancelar</button>


                                                            <button type="submit"
                                                                class="btn btn-secondary">Aceptar</button>



                                                    </div>
                                                </div>
                                            </div>  </form>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
@endsection
@section('script')


<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>

<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{asset('admin/assets/libs/moment/moment.min.js')}}"></script>
<script src="{{asset('admin/packages/core/main.js')}}"></script>
<script src="{{asset('admin/packages/core/locales/es.js')}}"></script>
<script src="{{asset('admin/packages/daygrid/main.js')}}"></script>
<script src="{{asset('admin/packages/timegrid/main.js')}}"></script>
<script src="{{asset('admin/packages/interaction/main.js')}}"></script>
<script src="{{asset('landing/js/diasLaborales.js')}}"></script>

@endsection

@section('script-bottom')
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
@endsection

