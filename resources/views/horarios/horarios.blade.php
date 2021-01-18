@php
use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Horarios</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('landing/images/ICONO-LOGO-NUBE-RH.ico')}}">
    @php
    $fecha=Auth::user()->created_at->toDateTimeString();
    $dt=Carbon::create($fecha);
    $dt->isoFormat('YYYY-MM-DD');
    $actual=Carbon::now();
    $actual->modify('-1 months')->isoFormat('YYYY-MM-DD');
    @endphp
    @if ($dt> $actual)
    <script src="//code.jivosite.com/widget/OqxplJ3nCh" async></script>
    @endif

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
    <link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100" style="background-color: #fdfdfd;">
    <style>
        .fc-event,
        .fc-event-dot {
            background-color: #d1c3c3;
            font-size: 10.5px !important;
            cursor: url("../landing/images/cruz1.svg"), auto;

        }

        .fc-event-container>a {
            border: 1px solid #fff;
        }

        .fc-toolbar.fc-header-toolbar {
            zoom: 80%;
        }

        #calendar>div.fc-toolbar.fc-footer-toolbar>div.fc-left>button,
        #calendar>div.fc-toolbar.fc-footer-toolbar>div.fc-center,
        #calendar>div.fc-toolbar.fc-footer-toolbar>div.fc-right>button {
            zoom: 90%;
        }

        div.fc-bg>table>tbody>tr>td.fc-day.fc-widget-content.fc-sun {

            background-color: rgb(255, 239, 239) !important;
        }

        div.fc-bg>table>tbody>tr>td.fc-day.fc-widget-content.fc-mon,
        td.fc-day.fc-widget-content.fc-tue,
        td.fc-day.fc-widget-content.fc-wed,
        td.fc-day.fc-widget-content.fc-thu,
        td.fc-day.fc-widget-content.fc-fri,
        td.fc-day.fc-widget-content.fc-sat {

            background-color: #ffffff !important;
        }

        .container {
            margin-left: 40px;
            margin-right: 28px;
        }

        .fc-time {
            display: none;
        }

        .table th,
        .table td {
            padding: 0.55rem;

            border-top: 1px solid #c9c9c9;

        }

        a:not([href]):not([tabindex]) {
            color: #000;
            cursor: pointer;
            font-size: 12px;
        }

        .sw-theme-default>ul.step-anchor>li.active>a {
            color: #1c68b1 !important;
        }

        .sw-theme-default>ul.step-anchor>li.done>a,
        .sw-theme-default>ul.step-anchor>li>a {
            color: #0b1b29 !important;
        }

        .day {
            max-width: 25%;
        }

        .month {
            max-width: 35%;
        }

        .year {
            max-width: 40%;
        }

        .btn-group {
            width: 100%;
            justify-content: space-between;
        }

        .btn-secondary {
            max-width: 9em;
        }

        .buttonc {
            color: #121b7a;
            background-color: #e7e1f7;
            border-color: #e7e1f7;
        }

        body {
            background-color: #f8f8f8;
        }

        .flatpickr-calendar {
            width: 125px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #52565b;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fdfdfd;
        }

        .fc th.fc-widget-header {
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

        #body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-light.bootbox-cancel {
            background: #e2e1e1;
            color: #000000;
            border-color: #e2e1e1;
            zoom: 85%;
        }

        #body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-footer>button,
        #body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-success.bootbox-accept {
            background-color: #163552;
            border-color: #163552;
            zoom: 85%;
        }

        #calendar>div.fc-toolbar.fc-header-toolbar>div.fc-center {
            margin-right: 200px;
        }

        .col-md-6 .select2-container .select2-selection {
            height: 50px;
            font-size: 12.2px;
            overflow-y: scroll;
        }

        .form-control:disabled {
            background-color: #f1f0f0;
        }
    </style>
    <style>
        .btnhora {
            font-size: 12px;
            padding-top: 1px;
            padding-bottom: 1px;
        }

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

        body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-header {
            background-color: #163552;
        }

        body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-header>h5 {
            color: #fff;
            font-size: 15px !important;
        }

        body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-header {
            background-color: #163552;
        }

        body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-header>h5 {
            color: #fff;
            font-size: 15px !important;
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
            <div class="container pb-3">
                <div class="col-md-2 col-xl-2">
                    <div class="navbar-brand-wrapper d-flex w-100">
                        <img src="{{asset('landing/images/NUBE_SOLA.png')}}" height="69">
                    </div>
                </div>
                <div class="col-md-7 col-xl-7 text-left pl-5">
                    <h5 style="color: #ffffff">Gestión de Horarios</h5>
                    <label for="" class="blanco font-italic">Ahora creamos y asignamos horarios</label>
                </div>


            </div>
        </nav>
    </header>
    <div class="content-page" style="margin-top: 20px;margin-left: 0px">
        <div class="content">
            <div class="row row-divided">
                <div class="col-md-12 col-xl-12">
                    <div class="card">
                        <div class="card-body" style="padding-top: 0px; background: #ffffff; font-size: 12.8px;
                        color: #222222;   padding-left:60px; padding-right: 80px; ">
                            <!--<h4 class="header-title mt-0 mb-1">Basic Data Table</h4>-->
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-sm btn-primary" id="btnasignar"
                                        style="background-color: #e3eaef;border-color:#e3eaef;color:#37394b"
                                        onclick="javascript:obtenerHorarios()"><img
                                            src="{{asset('admin/images/calendarioHor.svg')}}" height="15">&nbsp; Asignar
                                        horarios</button>
                                </div>
                                <div class=" col-md-6 col-xl-6 text-right">
                                    <button class="btn btn-sm btn-primary" onclick="abrirHorario()" id="btnNuevoHorario"
                                        style="background-color: #183b5d;border-color:#62778c">+ Nuevo Horario</button>

                                    {{--  <button class="btn btn-sm btn-primary" id="btnasignarIncidencia" style="background-color: #183b5d;border-color:#62778c">Asignar incidencias</button> --}}
                                </div>
                            </div>
                            <div id="tabladiv"> <br>
                                <table id="tablaEmpleado" class="table dt-responsive nowrap" style="font-size: 12.8px;">
                                    <thead style=" background: #edf0f1;color: #6c757d;">

                                        <tr>
                                            <th></th>
                                            <th>Descripcion</th>
                                            <th>Tolerancia</th>
                                            <th>Hora de inicio</th>
                                            <th>Hora de fin</th>
                                            <th>En uso</th>
                                            <th></th>


                                        </tr>
                                    </thead>

                                </table>
                            </div>
                            <br><br><br><br>
                            <div class=" col-md-12 col-xl-12 text-right">
                                &nbsp;&nbsp;&nbsp;
                                <button class="btn btn-sm btn-primary" onclick="finalizar()"
                                    style="background-color: #183b5d;border-color:#62778c;font-size: 14px">Finalizar</button>
                            </div>
                        </div> <!-- end card body-->

                    </div> <!-- end card -->


                    <div id="asignarHorario" class="modal fade" tabindex="-1" role="dialog"
                        aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static"
                        style="overflow-y: auto;">
                        <div class="modal-dialog  modal-lg d-flex modal-dialog-scrollable justify-content-center"
                            style="margin-top: 15px;max-width:1000px!important;">

                            <div class="modal-content">
                                <div class="modal-header" style="background-color:#163552;">
                                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                                        Asignar horarios masivamente</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" style="font-size: 13x!important;padding-top: 4px;
                           padding-bottom: 8px;">
                                    <input type="hidden" id="horario1">
                                    <input type="hidden" id="horario2">
                                    <div class="row">
                                        <div class="col-md-12" style="padding-left: 24px;">

                                            <div class="row">
                                                <div class="col-md-9" style="zoom:90%;">
                                                    <input type="hidden" id="fechaDa" name="fechaDa">
                                                    <label for="" style="font-weight: 600;">Seleccionar
                                                        empleado(s):</label>
                                                </div>



                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label for="" style="margin-top: 0px;
                                                margin-bottom: 1px;">Seleccionar por:</label>
                                                        </div>
                                                    </div>

                                                    <div class="row col-md-12">
                                                        <select data-plugin="customselect" multiple
                                                            id="selectEmpresarial" name="selectEmpresarial"
                                                            class="form-control" data-placeholder="seleccione">
                                                            <option value=""></option>
                                                            @foreach ($area as $areas)
                                                            <option value="{{$areas->idarea}}">Area :
                                                                {{$areas->descripcion}}.</option>
                                                            @endforeach
                                                            @foreach ($cargo as $cargos)
                                                            <option value="{{$cargos->idcargo}}">Cargo :
                                                                {{$cargos->descripcion}}.</option>
                                                            @endforeach
                                                            @foreach ($local as $locales)
                                                            <option value="{{$locales->idlocal}}">Local :
                                                                {{$locales->descripcion}}.</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row" style="margin-left: 6px;">
                                                        <div class="col-md-5 form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="selectTodoCheck">
                                                            <label class="form-check-label" for="selectTodoCheck"
                                                                style="font-style: oblique;margin-top: 2px;">Seleccionar
                                                                todos.</label>

                                                        </div>
                                                        <div class="col-md-7">
                                                            <span style="font-size: 11px!important">*Se visualizará
                                                                empleados con calendario</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12" style="padding-left: 5px;">
                                                        <select class="form-control wide" data-plugin="customselect"
                                                            multiple id="nombreEmpleado">
                                                            @foreach ($empleado as $empleados)
                                                            <option value="{{$empleados->emple_id}}">
                                                                {{$empleados->perso_nombre}}
                                                                {{$empleados->perso_apPaterno}}
                                                                {{$empleados->perso_apMaterno}}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>

                                                </div>


                                            </div><br>
                                        </div>


                                        <div class="col-md-10">
                                            <div class="col-md-12 text-center" id="DatoscalendarOculto"
                                                style=" display: none">
                                                <img src="{{asset('landing/images/loading.gif')}}" height="100">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="" style="font-weight: 600">Seleccionar dias de
                                                    calendario</label>
                                            </div>

                                            <div class="col-md-12 text-center" id="Datoscalendar"
                                                style=" max-width: 100%;">

                                                <div id="calendar" style="">
                                                </div>

                                            </div>
                                            <input type="hidden" id="horarioEnd">
                                            <input type="hidden" id="horarioStart">
                                        </div>
                                        <div class="col-md-2" style="margin-top: 100px;">


                                            <div class="col-md-12" style="padding-left: 0px;">
                                                <div class="col-md-12 form-check" style="padding-left: 10px;">
                                                    <input type="checkbox" style="" class="form-check-input"
                                                        id="FeriadosCheck">
                                                    <label class="form-check-label" for="FeriadosCheck">Ver
                                                        feriados.</label>
                                                </div>
                                            </div>
                                            <br><br>
                                            {{-- <div class="row">
                                         <div class="col-6" style="padding-left: 0px;">
                                            <button style="background-color: #dddaee; border-color: #ffffff; color: #44444c;" onclick="vaciarcalendario()"  class="btn btn-xs btn-primary" ><img src="{{asset('admin/images/borrar.svg')}}"
                                            height="10" ></button>
                                        </div>
                                        <div class="col-md-6" style="padding-left: 0px;">
                                            <label style="font-size: 12px" for="">vaciar calendario</label>
                                        </div>
                                    </div>
                                    <br> --}}
                                    <div class="row">


                                        <div class="col-md-6" style="padding-left: 0px;">
                                            <div class="btn-group mt-2 mr-1">
                                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle"
                                                    style="color: #fff;
                                                background-color: #1c3763;
                                                border-color: #1c3763;" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false"><img
                                                        src="{{asset('admin/images/borrador.svg')}}" height="15"> Borrar
                                                    <i class="icon"><span
                                                            data-feather="chevron-down"></span></i></button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" onclick="vaciarhor()">Horarios</a>
                                                    {{--  <a class="dropdown-item"  onclick="vaciardl()" >D. laborables</a>
                                                    <a class="dropdown-item" onclick="vaciarndl()">D. no laborables</a>
                                                    <a class="dropdown-item" onclick="vaciarinH()">Incidencia</a> --}}

                                                </div>
                                            </div><!-- /btn-group -->
                                        </div><br><br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="padding-top: 8px;
                           padding-bottom: 8px;">
                            <div class="col-md-12">
                                <div class="row">

                                    <div class="col-md-12 text-right" style="padding-right: 0px;">
                                        <button type="button" id="" class="btn btn-light "
                                            data-dismiss="modal">Cancelar</button>
                                        <button type="button" id="guardarTodoHorario" name=""
                                            style="background-color: #163552;" class="btn ">Guardar</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal-->

            <div id="horarioAsignar_ed" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog  modal-lg d-flex justify-content-center "
                    style="width:390px;  margin-top: 150px; left: 30px;">
                    <div class="modal-content">
                        <div class="modal-body" style="font-size:12px!important;background: #f3f3f3;">
                            <div class="row">
                                <div class="col-md-9">
                                    <span id=errorSel style="color: #8b3a1e;display:none">Seleccione un horario</span>
                                    <select data-plugin="customselect"
                                        class="form-control custom-select custom-select-sm  col-md-10"
                                        name="selectHorario" id="selectHorario">
                                        <option hidden selected disabled>Asignar horario</option>
                                        @foreach ($horario as $horarios)
                                        <option class="" value="{{$horarios->horario_id}}">
                                            {{$horarios->horario_descripcion}}
                                            <span style="font-size: 11px;font-style: oblique">
                                                ({{$horarios->horaI}}-{{$horarios->horaF}})
                                            </span>
                                        </option>
                                        @endforeach
                                    </select>
                                    &nbsp;
                                </div>
                                <div class="col-md-3 text-left" style="padding-left: 0px;">
                                    <button class="btn btn-primary btn-sm"
                                        style="background-color: #183b5d;border-color:#62778c;margin-top: 5px;"
                                        onclick="modalRegistrar()">
                                        +
                                    </button>
                                </div>
                                <div class="col-md-12">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="fueraHSwitch">
                                        <label class="custom-control-label" for="fueraHSwitch">
                                            Permite marcar fuera del horario.
                                        </label>
                                    </div>
                                    <div class="row">
                                        <div class="custom-control custom-switch mb-2" style="left: 12px;">
                                            <input type="checkbox" class="custom-control-input" id="horAdicSwitch">
                                            <label class="custom-control-label" for="horAdicSwitch">
                                                Permite marcar horas adicionales.
                                            </label>
                                        </div>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <select id="nHorasAdic" style="display: none;bottom: 3px;"
                                            class="form-control form-control-sm col-md-3">
                                            <option value="0.5">0.5 hora </option>
                                            <option value="1">1 hora </option>
                                            <option value="2">2 horas </option>
                                            <option value="3">3 horas </option>
                                            <option value="4">4 horas </option>
                                            <option value="5">5 horas </option>
                                            <option value="6">6 horas </option>
                                            <option value="7">7 horas </option>
                                            <option value="8">8 horas </option>
                                            <option value="9">9 horas </option>
                                            <option value="10">10 horas </option>
                                            <option value="11">11 horas </option>
                                            <option value="12">12 horas </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="padding-top: 5px; padding-bottom: 5px;background: #f1f0f0;">
                            <div class="col-md-12 text-right" style="padding-right: 0px;">
                                <button type="button" class="btn btn-light  btn-sm"
                                    style="background:#f3f3f3;border-color: #f3f3f3;"
                                    onclick="$('#horarioAsignar_ed').modal('hide')">
                                    Cancelar
                                </button>
                                <button type="button" class="btn btn-primary btn-sm"
                                    style="background-color: #183b5d;border-color:#62778c;"
                                    onclick="agregarHorarioSe()">
                                    Registrar
                                </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="verhorarioEmpleado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true" data-backdrop="static" style="overflow-y: auto;">
                <div class="modal-dialog  modal-xl d-flex justify-content-center" style="margin-top: 5px">

                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#163552;">
                            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Horario de
                                empleado</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="hidden" id="fechaDa2" name="fechaDa2">
                                            <div class="form-group">
                                                <label for="">Empleado:</label>
                                                {{-- <input type="text" class="form-control form-control-sm" id="nombreEmpleado"> --}}
                                                <input type="text" class="form-control form-control-sm" id="idEmHorario"
                                                    disabled>
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
                                <div class="col-md-3" style="top: 150px;">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-1" style="  background: #f9e9e9;
                                            height: 25px;">
                                                <h1>&nbsp;</h1>
                                            </div>
                                            <div class="col-md-3"><label for="" style="font-size: 12px">Dias de
                                                    Descanso</label></div>
                                            <div class="col-md-1"><br></div>
                                            <div class="col-md-1" style="  background: #ffffff;
                                            height: 25px;border: 1px solid #d4d4d4;">
                                                <h1>&nbsp;</h1>
                                            </div>
                                            <div class="col-md-2"><label for="" style="font-size: 12px">Dias
                                                    laborables</label><br><br></div>

                                            <div class="col-md-12" style="padding-left: 0px;height: 220px;
                                              overflow-y: scroll; ">
                                                <label for="" style="font-weight: 600">Horarios de calendario</label>
                                                <table class="table" id="tablahorarios">
                                                    <thead>
                                                        <tr>
                                                            <th>Nombre</th>
                                                            <th>Inicio</th>
                                                            <th>Fin</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody style="font-size: 12px ">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>



                                </div>
                                <div class="col-md-12">
                                    <br>
                                    <div class="row" style="padding-left:2px;">
                                        <div class="col-md-9">
                                            <button
                                                style="background-color: #dcc3c3; border-color: #ffffff; color: #44444c"
                                                class="btn btn-sm  btn-primary" onclick="screenshot();"><img
                                                    src="{{asset('admin/images/pdf2.svg')}}" height="24"></i>
                                                Descargar</button>
                                            <select class="form-control custom-select custom-select-sm  col-md-3"
                                                name="selectHorarioen" id="selectHorarioen">
                                                <option hidden selected>Asignar horario</option>
                                                @foreach ($horario as $horarios)
                                                <option class="" value="{{$horarios->horario_id}}">
                                                    {{$horarios->horario_descripcion}}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm fc-button-primary"
                                                onclick="abrirHorarioen()">+</button>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-sm buttonc"
                                                onclick="asignarlaboen()">Asignar laborable</button>
                                            &nbsp;&nbsp; <button class="btn btn-sm buttonc"
                                                onclick="asignarNolaboen()">Asignar no laborable</button>
                                            &nbsp;&nbsp; <button class="btn btn-sm buttonc"
                                                onclick="asignarInciEmp()">Asignar Incidencia</button>
                                        </div>
                                        {{-- <button type="button" id="" class="btn btn-light " data-dismiss="modal">Cancelar</button> --}}
                                        <div class="col-md-3 text-right">

                                            <button type="button" id="cerrarHorario" name=""
                                                style="background-color: #d9dee2;color: #171413;"
                                                class="btn ">Cerrar</button>

                                        </div>
                                    </div>
                                </div>



                            </div>

                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <div id="asignarIncidencia" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog  modal-lg d-flex justify-content-center">

                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#163552;">
                            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Asignar
                                Incidencia</h5>
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
                                                    <select class="form-control wide" data-plugin="customselect"
                                                        multiple id="empIncidencia" required>
                                                        {{-- <option value="">hj</option> --}}
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="">Descripcion:</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="descripcionInci" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4"><label for=""><br></label>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="descuentoCheck">
                                                    <label class="form-check-label" for="descuentoCheck">Aplicar
                                                        descuento</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for=""><br></label>
                                                <div class="custom-control custom-switch mb-2">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="customSwitch1">
                                                    <label class="custom-control-label" for="customSwitch1">Asignar mas
                                                        de 1 día</label>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Fecha inicio:</label>
                                                    <input type="date" id="fechaI" class="form-control form-control-sm"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-4" id="divFfin">
                                                <div class="form-group">
                                                    <label for="">fecha fin:</label>
                                                    <input type="date" id="fechaF" class="form-control form-control-sm">
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
                                        <button type="button" class="btn btn-light "
                                            data-dismiss="modal">Cancelar</button>
                                        <button type="submit" name="" style="background-color: #163552;"
                                            class="btn ">Guardar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <div id="asignarIncidenciaHorario" class="modal fade" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog  modal-lg d-flex justify-content-center" style="width: 500px">

                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#163552;">
                            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Asignar
                                Incidencia</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">

                                <div class="col-md-12">
                                    <form id="frmIncidenciaHo" action="javascript:registrarIncidenciaHo()">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Descripcion:</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="descripcionInciHo" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6"><label for=""><br></label>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="descuentoCheckHo">
                                                    <label class="form-check-label" for="descuentoCheckHo">Aplicar
                                                        descuento</label>
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
                                        <button type="button" class="btn btn-light btn-sm "
                                            data-dismiss="modal">Cancelar</button>
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

            <div id="asignarIncidenciaHorarioEmp" class="modal fade" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog  modal-lg d-flex justify-content-center" style="width: 500px">

                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#163552;">
                            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Asignar
                                Incidencia</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" id="horario1em">
                                <input type="hidden" id="horario2em">

                                <div class="col-md-12">
                                    <form id="frmIncidenciaHoEm" action="javascript:registrarIncidenciaHoEm()">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">

                                                    <label for="">Descripcion:</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="descripcionInciHoEm" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6"><label for=""><br></label>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="descuentoCheckHoEm">
                                                    <label class="form-check-label" for="descuentoCheckHoEm">Aplicar
                                                        descuento</label>
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
                                        <button type="button" class="btn btn-light btn-sm "
                                            data-dismiss="modal">Cancelar</button>
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

            {{-- REGISTRAR NUEVO HORARIO --}}
            <div id="horarioAgregar" class="modal fade" role="dialog" aria-labelledby="horarioAgregar"
                aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog  modal-lg d-flex justify-content-center" style="width: 850px;">
                    <input type="hidden" id="idPausaMayor">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#163552;">
                            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                                Asignar horario
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="font-size:12px!important">
                            <div class="row">
                                <div class="col-md-12">
                                    <form id="frmHor" action="javascript:registrarNuevoHorario()">
                                        <div class="row">
                                            <br>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Descripción del horario:</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="descripcionCa" maxlength="60" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Hora de inicio(24h):</label>
                                                    <input type="text" id="horaI" class="form-control form-control-sm"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Hora de fin(24h):</label>
                                                    <input type="text" id="horaF" class="form-control form-control-sm"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Horas obligadas:</label>
                                                    <div class="input-group form-control-sm"
                                                        style="bottom: 4px;padding-left: 0px; padding-right: 0px;">
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="horaOblig" required>
                                                        <div class="input-group-prepend ">
                                                            <div class="input-group-text form-control-sm"
                                                                style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                                Horas
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Tolerancia al ingreso(Min):</label>
                                                    <div class="input-group form-control-sm "
                                                        style="bottom: 4px;padding-left: 0px; padding-right: 0px;">
                                                        <input type="number" value="0"
                                                            class="form-control form-control-sm"
                                                            oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;"
                                                            id="toleranciaH" required>
                                                        <div class="input-group-prepend  ">
                                                            <div class="input-group-text form-control-sm "
                                                                style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                                Minutos
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Tolerancia a la salida(Min):</label>
                                                    <div class="input-group form-control-sm "
                                                        style="bottom: 4px;padding-left: 0px; padding-right: 0px;">
                                                        <input type="number" value="0"
                                                            class="form-control form-control-sm"
                                                            oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;"
                                                            id="toleranciaSalida" required>
                                                        <div class="input-group-prepend  ">
                                                            <div class="input-group-text form-control-sm "
                                                                style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                                Minutos
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4" id="divOtrodia" style="display: none">
                                                <div class="form-check mt-4 mb-4">
                                                    <input type="checkbox" style="font-weight: 600"
                                                        class="form-check-input" id="smsCheck" checked disabled>
                                                    <label class="form-check-label" for="smsCheck"
                                                        style="margin-top: 2px;font-weight: 700">
                                                        La hora fin de este horario pertenece al siguiente día.
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="custom-control custom-switch mb-2">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="SwitchTardanza">
                                                    <label class="custom-control-label" for="SwitchTardanza"
                                                        style="font-weight: bold;padding-top: 1px">
                                                        Controlar tardanza
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="custom-control custom-switch mb-2">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="SwitchPausa">
                                                    <label class="custom-control-label" for="SwitchPausa"
                                                        style="font-weight: bold;padding-top: 1px">
                                                        Pausas en el horario
                                                    </label>
                                                    &nbsp;
                                                    <span id="fueraRango" style="color: #80211e;display: none">
                                                        Hora no esta dentro de rango de horario
                                                    </span>
                                                    <span id="errorenPausas" style="color: #80211e;display: none">
                                                        - Fin de pausa debe ser mayor a inicio pausa
                                                    </span>
                                                    <span id="errorenPausasCruzadas"
                                                        style="color: #80211e;display: none">
                                                        - Los rangos de pausas no pueden cruzarse, revísalo e inténtalo
                                                        nuevamente.
                                                    </span>
                                                    &nbsp;
                                                    <span id="vacioHoraF" style="color: #80211e;display: none">
                                                        Agregar Hora de inicio o Hora de fin
                                                    </span>
                                                </div>
                                            </div>
                                            <div id="divPausa" class="col-md-12" style="display: none">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label for="" style="font-weight:600">Descripción</label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="" style="font-weight:600">Inicio
                                                                pausa(24h)</label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for=""
                                                                style="font-weight:600">Tolerancia(Min)</label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="" style="font-weight:600">Fin pausa(24h)</label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for=""
                                                                style="font-weight:600">Tolerancia(Min)</label>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label for="" style="font-weight:600">Inactivar</label>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label for="" style="font-weight:600">&nbsp;</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="validP" style="color: red;display:none">
                                                    *Campos Obligatorios
                                                </span>
                                                <div id="inputPausa"></div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">
                                            Cancelar
                                        </button>
                                        <button type="submit" id="btnGuardaHorario" style="background-color: #163552;"
                                            class="btn btn-sm">
                                            Guardar
                                        </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- FINALIZAR --}}

            <div id="horarioAgregaren" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 550px;">

                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#163552;">
                            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Asignar
                                horario</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="font-size:12px!important">
                            <div class="row">

                                <div class="col-md-12">
                                    <form id="frmHoren" action="javascript:registrarHorarioen()">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Tipo de horario:</label>
                                                    <select class="form-control custom-select custom-select-sm"
                                                        id="tipHorarioen">
                                                        <option>Normal</option>
                                                        <option>Guardía</option>
                                                        <option>Nocturno</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6"><label for=""><br></label>
                                                <div class="form-check">

                                                    <input type="checkbox" class="form-check-input"
                                                        id="exampleCheck1en">
                                                    <label class="form-check-label" for="exampleCheck1en">Aplicar
                                                        sobretiempo</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Descripcion:</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="descripcionCaen" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Tolerancia(Min):</label>
                                                    <input type="number" value="0" class="form-control form-control-sm"
                                                        min="0" max="59" id="toleranciaHen" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Hora de inicio(24h):</label>
                                                    <input type="text" id="horaIen" class="form-control form-control-sm"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Hora de fin(24h):</label>
                                                    <input type="text" id="horaFen" class="form-control form-control-sm"
                                                        required>
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
            <div id="borrarincide" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog  d-flex justify-content-center">

                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#163552;">
                            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Incidencias
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="tablaBorrarI" class="table">
                                        <thead>
                                            <tr>
                                                <th>Nombre de incidencia</th>
                                                <th>Descuento</th>
                                                <th>*</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 12px">

                                        </tbody>
                                    </table>
                                </div>


                            </div>

                        </div>
                        <div class="modal-footer" style="padding-top: 6px;padding-bottom: 6px;">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="btn btn-light btn-sm"
                                            data-dismiss="modal">Cerrar</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            {{-- EDITAR HORARIO --}}
            <div id="horarioEditar" class="modal fade" role="dialog" aria-labelledby="horarioEditar" aria-hidden="true"
                data-backdrop="static">
                <div class="modal-dialog  modal-lg d-flex justify-content-center" style="width: 850px;">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#163552;">
                            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                                Editar horario
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="font-size:12px!important">
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="hidden" id="idhorario_ed">
                                    <form id="frmHorEditar" action="javascript:editarHorarioDatos()">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Descripción del horario:</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="descripcionCa_ed" maxlength="40" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Hora de inicio(24h):</label>
                                                    <input type="text" id="horaI_ed"
                                                        class="form-control form-control-sm" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Hora de fin(24h):</label>
                                                    <input type="text" id="horaF_ed"
                                                        class="form-control form-control-sm" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Horas obligadas:</label>
                                                    <div class="input-group form-control-sm"
                                                        style="bottom: 4px;padding-left: 0px; padding-right: 0px;">
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="horaOblig_ed" required>
                                                        <div class="input-group-prepend ">
                                                            <div class="input-group-text form-control-sm"
                                                                style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                                Horas
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Tolerancia al ingreso(Min):</label>
                                                    <div class="input-group form-control-sm "
                                                        style="bottom: 4px;padding-left: 0px; padding-right: 0px;">
                                                        <input type="number" class="form-control form-control-sm"
                                                            min="0"
                                                            oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;"
                                                            id="toleranciaH_ed" required>
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text form-control-sm"
                                                                style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                                Minutos
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Tolerancia a la salida(Min):</label>
                                                    <div class="input-group form-control-sm"
                                                        style="bottom: 4px;padding-left: 0px; padding-right: 0px;">
                                                        <input type="number" class="form-control form-control-sm"
                                                            min="0"
                                                            oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;"
                                                            id="toleranciaSalida_ed" required>
                                                        <div class="input-group-prepend  ">
                                                            <div class="input-group-text form-control-sm "
                                                                style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                                Minutos
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4" id="divOtrodia_ed" style="display: none">
                                                <label for=""><br></label>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="smsCheck_ed"
                                                        checked disabled>
                                                    <label class="form-check-label" for="smsCheck_ed"
                                                        style="margin-top: 2px;font-weight: 700">
                                                        La hora fin de este horario pertenece al siguiente día.
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="custom-control custom-switch mb-2">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="SwitchTardanza_ed">
                                                    <label class="custom-control-label" for="SwitchTardanza_ed"
                                                        style="font-weight: bold;padding-top: 1px">
                                                        Controlar tardanza
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="custom-control custom-switch mb-2">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="SwitchPausa_ed">
                                                    <label class="custom-control-label" for="SwitchPausa_ed"
                                                        style="font-weight: bold;padding-top: 1px">
                                                        Pausas en el horario
                                                    </label>
                                                    &nbsp;
                                                    <span id="fueraRango_ed" style="color: #80211e;display: none">
                                                        Hora no esta dentro de rango de horario
                                                    </span>
                                                    <span id="errorenPausas_ed" style="color: #80211e;display: none">
                                                        - Fin de pausa debe ser mayor a inicio pausa
                                                    </span>
                                                    <span id="errorenPausasCruzadas_ed"
                                                        style="color: #80211e;display: none">
                                                        - Los rangos de pausas no pueden cruzarse, revísalo e inténtalo
                                                        nuevamente.
                                                    </span>
                                                    &nbsp;
                                                    <span id="vacioHoraF_ed" style="color: #80211e;display: none">
                                                        Agregar Hora de inicio o Hora de fin
                                                    </span>
                                                </div>
                                            </div>
                                            <div id="pausas_edit" style="display: none" class="col-md-12">
                                                {{-- <label for="">Pausas de horario:</label> --}}
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <label for="" style="font-weight:600">Descripción</label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="" style="font-weight:600">Inicio
                                                                pausa(24h)</label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for=""
                                                                style="font-weight:600">Tolerancia(Min)</label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for="" style="font-weight:600">Fin pausa(24h)</label>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label for=""
                                                                style="font-weight:600">Tolerancia(Min)</label>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label for="" style="font-weight:600">Inactivar</label>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label for="" style="font-weight:600">&nbsp;</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="validP_ed" style="color: red;display:none">
                                                    *Campos Obligatorios
                                                </span>
                                                <div id="PausasHorar_ed"></div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="btn btn-light btn-sm " data-dismiss="modal">
                                            Cancelar
                                        </button>
                                        <button type="submit" style="background-color: #163552;" class="btn btn-sm"
                                            id="btnEditarHorario">
                                            Guardar
                                        </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- FINALIZAR --}}
        </div>
    </div>
    <footer class="border-top">
        <p class="text-center text-muted pt-4">© <?php echo date("Y"); ?> - RH nube Corp - USA | Todos los derechos
            reservados.</p>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
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
    //document.body.appendChild(canvas)
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

                  doc.addImage(image, 'JPG',2,60);
                  doc.save('horario.pdf');
              }
          });
  }
    </script>
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
    <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="{{asset('landing/images/notification.svg')}}" height="100">
                    <h4 class="text-danger mt-4">Su sesión expiró</h4>
                    <p class="w-75 mx-auto text-muted">Por favor inicie sesión nuevamente.</p>
                    <div class="mt-4">
                        <a href="{{('/')}}" class="btn btn-outline-primary btn-rounded width-md"><i
                                class="uil uil-arrow-right mr-1"></i> Iniciar sesión</a>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="modalEmpleadosHo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog  modal-lg d-flex justify-content-center" style="max-width:800px!important;">

            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" style="color:#ffffff;font-size:15px">Alerta de inconsistencia de horarios
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Los siguientes empleados ya presentan un horario asignado en este rango de
                                horas, revise y vuelva a intentar.</label>
                        </div>
                        <div class="col-md-12">
                            <table id="tablaEmpleadoExcel" class="table nowrap" style="font-size: 12.8px;">
                                <thead style=" background: #edf0f1;color: #6c757d;">
                                    <tr>
                                        <th></th>
                                        <th>DNI</th>
                                        <th>Nombres</th>

                                    </tr>
                                </thead>
                                <tbody id="tbodyExcel"></tbody>
                            </table>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-light " data-dismiss="modal">Cerrar</button>


                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    @if (Auth::user())
    <script>
        $(function() {
    setInterval(function checkSession() {
      $.get('/check-session', function(data) {
        // if session was expired
        if (data.guest==false) {
            $('.modal').modal('hide');
           $('#modal-error').modal('show');

        }
      });
    },7202000);
  });
    </script>
    @endif
</body>

</html>
