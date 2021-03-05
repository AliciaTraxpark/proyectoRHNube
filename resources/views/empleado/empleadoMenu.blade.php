@php
use App\proyecto_empleado;
@endphp

@extends('layouts.vertical')

@section('css')
<style>
    .pace {
        -webkit-pointer-events: none;
        pointer-events: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    .pace-inactive {
        display: none;
    }

    .pace .pace-progress {
        background: #545474;
        position: fixed;
        z-index: 2000;
        top: 0;
        /* right: 100%; */
        width: 100%;
        height: 6 px;
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

    .form-control:disabled {
        background-color: #f1f0f0;
    }



    .inputfile {
        width: 0.1px !important;
        height: 0.1px !important;
        opacity:  !important overflow: hidden !important;
        position: absolute !important;
        z-index: -1 !important;
    }

    .inputfile+label {
        max-width: 80% !important;
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        cursor: pointer !important;
        display: inline;
        overflow: hidden !important;
        padding: 0.30rem 0.8rem !important;
    }

    .inputfile+label svg {
        width: 1em !important;
        height: 1em !important;
        vertical-align: middle !important;
        fill: currentColor !important;

        margin-right: 0.25em !important;
    }

    .iborrainputfile {
        font-size: 13.8px !important;
        font-weight: normal !important;

    }

    /* style 1 */

    .inputfile-1+label {
        color: #59687d !important;
        background-color: #e3eaef !important;
    }

    .inputfile-1:focus+label,
    .inputfile-1.has-focus+label,
    .inputfile-1+label:hover {
        background-color: #e3eaef !important;
    }

    .large.tooltip-inner {
        max-width: 185px;
        width: 185px;
    }
</style>


<script type="text/javascript" src="{{asset('admin/assets/pace/pace.min.js')}}"></script>
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('admin/assets/libs/bootstrap-fileinput/fileinput.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Plugin css  CALENDAR-->
<link href="{{ asset('admin/packages/core/main.css') }}" rel="stylesheet" />
<link href="{{ asset('admin/packages/daygrid/main.css') }}" rel="stylesheet" />
<link href="{{ asset('admin/packages/timegrid/main.css') }}" rel="stylesheet" />
{{-- Plugin SELECT2 --}}
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
{{-- plugin DATATABLE --}}
<link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
{{-- plugin SMARTWIZARD --}}
<link href="{{ asset('admin/assets/libs/smartwizard/smart_wizard.min.css') }}" type="text/css" />
<link href="{{ asset('admin/assets/libs/smartwizard/smart_wizard_theme_arrows.min.css') }}" type="text/css" />
<link href="{{ asset('admin/assets/libs/smartwizard/smart_wizard_theme_circles.min.css') }}" type="text/css" />
<link href="{{ asset('admin/assets/libs/smartwizard/smart_wizard_theme_dots.min.css') }}" type="text/css" />
{{-- plugin MULTISELECT --}}
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css')}}" rel="stylesheet" type="text/css" />
{{-- plugin de ALERTIFY --}}
<link href="{{ URL::asset('admin/assets/libs/alertify/alertify.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ URL::asset('admin/assets/libs/alertify/bootstrap.css') }}" rel="stylesheet" type="text/css" /> --}}
<!-- Semantic UI theme -->
<link href="{{ URL::asset('admin/assets/libs/alertify/default.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/zoom.css') }}" rel="stylesheet" type="text/css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('breadcrumb')

<style>
    #calendarInv>div.fc-view-container>div>table>tbody {
        background: #f4f4f4;
    }

    #calendarInv_ed>div.fc-view-container>div>table>tbody {
        background: #f4f4f4;
    }

    .page-item.active .page-link {
        background-color: #e3eaef;
        border-color: #e3eaef;
        color: #3d3d3d;
    }

    .fc-event,
    .fc-event-dot {
        font-size: 12.2px !important;
        margin: 2px 2px;
    }

    .form-control {
        font-size: 12px;
    }

    tr:first-child>td>.fc-day-grid-event {
        margin-top: 0px;
        padding-top: 0px;
        padding-bottom: 0px;
        margin-bottom: 0px;
        margin-left: 2px;
        margin-right: 2px;
    }

    .flatpickr-calendar {
        max-width: 90%;
    }


    .btn-outline-secondary {
        border-color: #e3eaef;
        background: #ffffff;
    }

    .container {
        margin-left: 40px;
        margin-right: 28px;
    }

    .fc-time {
        display: none;
    }

    .v-divider {
        border-right: 5px solid #4C5D73;
    }

    .sw-theme-default>ul.step-anchor>li.active>a {
        color: #1c68b1 !important;
    }

    .sw-theme-default>ul.step-anchor>li.done>a,
    .sw-theme-default>ul.step-anchor>li>a {
        color: #0b1b29 !important;
    }

    .day {
        max-width: 30%;
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

    .sw-btn-group-extra {
        justify-content: flex-end !important;
    }

    .btn-secondary {
        max-width: 9em;
    }

    .form-control:disabled {
        background-color: #fcfcfc;
    }

    .fc-event,
    .fc-event-dot {
        /*  background-color: #d1c3c3; */
        font-size: 12.2px !important;
        margin: 2px 2px;
        cursor: url("../landing/images/cruz1.svg"), auto !important;
        /*  cursor: url("../landing/images/configs-m.svg"), auto !important; */

    }

    .fc-event-container>a {
        border: 1px solid #fff;
    }

    a:not([href]):not([tabindex]) {
        color: #000;
        cursor: pointer;
        font-size: 12px;
    }

    body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-light.bootbox-cancel {
        background: #e2e1e1;
        color: #000000;
        border-color: #e2e1e1;
        zoom: 85%;
    }

    body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-footer>button,
    body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-success.bootbox-accept {
        background-color: #163552;
        border-color: #163552;
        zoom: 85%;
    }

    .scroll {
        max-height: 100px;
        overflow-y: auto;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #52565b;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fdfdfd;
    }

    .col-lg-10 .select2-container .select2-selection {
        height: 20px;
        font-size: 12.2px;
        overflow-y: scroll;
    }

    .custom-select:disabled {
        color: #3f3a3a;
        background-color: #fcfcfc;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #ced0d3;
    }

    .btn-file {
        background-color: #1e2139 !important;
        border-color: #1e2139 !important;
    }

    .file-caption {
        height: calc(2.5em + 0.6rem + 2px) !important;
    }

    .borderColor {
        border-color: red;
    }

    /* CURSOR DE TABLA DISPOSITIVO */
    .cursorDispositivo {
        cursor: url("../landing/images/pencil.svg"), auto !important;
    }

    /* MODIFICAR ESTILOS DE ALERTIFY */
    .alertify .ajs-header {
        font-weight: normal;
    }

    .ajs-body {
        padding: 0px !important;
    }

    .alertify .ajs-footer {
        background: #ffffff;
    }

    .alertify .ajs-footer .ajs-buttons .ajs-button {
        min-height: 28px;
        min-width: 75px;
    }

    .ajs-cancel {
        font-size: 12px !important;
    }

    .ajs-ok {
        font-size: 12px !important;
    }

    .alertify .ajs-dialog {
        max-width: 450px;
    }

    .ajs-footer {
        padding: 12px !important;
    }

    .alertify .ajs-footer .ajs-buttons .ajs-button.ajs-ok {
        text-transform: none;
    }

    .alertify .ajs-footer .ajs-buttons.ajs-primary .ajs-button {
        text-transform: none;
    }

    /* FINALIZACION DE ALERTIFY */

    .select2-container .select2-selection--multiple {
        overflow-y: scroll;
    }

    /* RESPONSIVE */

    @media (max-width: 767.98px) {

        .btnResponsive {
            text-align: right !important;
            padding-right: 0% !important;
        }

        .btnPResponsive {
            text-align: center !important;
            display: flex !important;
            justify-content: space-between !important;
        }

        .titleResponsive {
            padding-right: 10px !important;
        }

        .inputResponsive {
            padding-right: 0% !important;
            padding-bottom: 5px !important;
        }

        .divTableResponsive {
            padding-right: 0% !important;
        }

        .table {
            width: 100% !important;
        }

        .dataTables_scrollHeadInner {
            width: 100% !important;
        }

        .dataTable,
        .dataTables_scrollHeadInner,
        .dataTables_scrollBody {
            width: 100% !important;
        }

        #v_dia_fecha,
        #v_dia_fechaV,
        #dia_fecha {
            max-width: 100%;
            margin: 0% !important;
            padding: 0% !important;
        }

        #v_mes_fecha,
        #v_mes_fechaV,
        #mes_fecha {
            max-width: 100%;
            margin: 0% !important;
            padding: 0% !important;
        }

        #v_ano_fecha,
        #v_ano_fechaV,
        #ano_fecha {
            max-width: 100%;
            margin: 0% !important;
            padding: 0% !important;
        }

        .fechasResponsive {
            flex-wrap: unset !important;
        }

        .col-4 {
            padding-right: 5px !important;
            padding-left: 5px !important;
        }

        .pAnio {
            padding-right: 1%;
        }

        .prigth {
            padding-right: 3px !important;
        }

        .pleft {
            padding-left: 3px !important;
        }

        .selectResp {
            padding: 0 !important;
        }

        .pselect {
            padding-left: 10px !important;
            padding-right: 0 !important;
        }

        .custom-control {
            padding-left: 1rem !important;
        }

        .labelNivel {
            display: flex !important;
            font-size: smaller !important;
        }

        li.paginate_button.previous,
        li.paginate_button.next {
            font-size: 0.9rem !important;
        }
    }

    @media (max-width: 447px) {
        .radio_rsp {
            padding: 0 0 !important;
        }
    }

    @media (max-width: 799px) and (min-width: 447px) {
        .radio_rsp {
            padding: 0 5% !important;
        }
    }

    @media (max-width: 578px) {
        .label_select {
            padding: 0px 0px 0px 0px !important;
        }
    }

    .radio_rsp {
        padding: 0 10%;
    }

    .label_txt {
        padding: 10px 0px 0px 0px !important;
        font-weight: 200 !important;
    }

    .label_select {
        padding: 10px 0px 0px 0px;
    }

    @media(min-width: 386px) and (max-width: 487px) {
        .th_rh {
            padding: 0 35px !important;
        }
    }

    /* FINALIZACION DE RESPONSIVE */
</style>
{{-- BOTONES DE CARGAS MASIVAS --}}
<div class="row page-title titleResponsive" style="padding-right: 20px;">
    <div class="col-md-7">
        <h4 class="header-title mt-0 "></i>Empleados</h4>
    </div>
    @if (isset($agregarEmp))
    @if ($agregarEmp==1)
    <div class="col-sm-12 col-md-12 col-xl-5 text-right btnPResponsive">
        <a href="{{ '/empleado/cargar' }}">
            <button class="btn btn-outline-secondary btn-sm m-1">
                <img src="{{ asset('admin/images/subir.ico') }}" height="20" class="mr-1">
                Carga masiva emp.
            </button>
        </a>
        &nbsp;&nbsp;
        <button class="btn btn-outline-secondary btn-sm m-1" id="cargaMasivaF">
            <img src="{{ asset('admin/images/image.ico') }}" height="20" class="mr-1">
            Carga masiva fotos
        </button>
    </div>
    @else

    @endif
    @else
    <div class="col-sm-12 col-md-12 col-xl-5 text-right btnPResponsive">
        <a href="{{ '/empleado/cargar' }}">
            <button class="btn btn-outline-secondary btn-sm m-1">
                <img src="{{ asset('admin/images/subir.ico') }}" height="20" class="mr-1">
                Carga masiva emp.
            </button>
        </a>
        &nbsp;&nbsp;
        <button class="btn btn-outline-secondary btn-sm m-1" id="cargaMasivaF">
            <img src="{{ asset('admin/images/image.ico') }}" height="20" class="mr-1">
            Carga masiva fotos
        </button>
    </div>
    @endif

</div>
{{-- FINAIZACION --}}
@endsection
@section('content')
<div class="row ">
    <div class="col-md-12 col-xl-12">
        <div class="card">
            <div class="card-body"
                style="padding-top: 0px; background: #ffffff; font-size: 12.8px;color: #222222; padding-left:0px;">
                <div class="row">
                    {{-- BOTONES --}}
                    <div class=" col-md-12 text-right btnResponsive">
                        @if (isset($agregarEmp))
                        @if ($agregarEmp==1)
                        <button class="btn btn-sm btn-primary" id="formNuevoE"
                            style="background-color: #e3eaef;border-color:#e3eaef;color:#3d3d3d">
                            Nuevo
                        </button>
                        @else

                        @endif
                        @else
                        <button class="btn btn-sm btn-primary" id="formNuevoE"
                            style="background-color: #e3eaef;border-color:#e3eaef;color:#3d3d3d">
                            Nuevo
                        </button>
                        @endif
                    </div>
                    {{-- FINALIZACION --}}
                    {{-- BUSQUEDA PARA TABLA --}}
                    <div class="col-md-12">
                        <h5 style="font-size: 16px!important">Búsqueda personalizada</h5>
                    </div>
                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 inputResponsive">
                        <div class="form-group row">
                            <label class="col-lg-2 col-md-3 col-sm-3 col-form-label">Buscar:</label>
                            <div class="col-lg-10 col-md-9 col-sm-9 text-left">
                                <select id="selectarea" data-plugin="customselect" class="form-control form-control-sm"
                                    multiple="multiple" data-placeholder="Seleccionar">
                                    @foreach ($area as $areas)
                                    <option value="{{ $areas->area_id }}"> Área : {{ $areas->area_descripcion }}</option>
                                    @endforeach
                                    @foreach ($cargo as $cargos)
                                    <option value="{{ $cargos->cargo_id }}"> Cargo : {{ $cargos->cargo_descripcion }}</option>
                                    @endforeach
                                    @foreach ($local as $locales)
                                    <option value="{{ $locales->local_id }}"> Local : {{ $locales->local_descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-3 col-md-3 col-lg-3 inputResponsive">
                        <div class="form-group row">
                        <label class="col-lg-6 col-md-6 col-sm-6 col-form-label">Seleccionar por:</label>
                        <td align="center">
                            <select class="form-control col-lg-6 col-md-6 col-sm-6" name="select" id="select" style="height: 35.5px;">
                                <option value="2">Documento</option>
                                <option value="3">Nombre</option>
                                <option value="4" selected>Apellidos</option>
                                <option value="9">Cargo</option>
                                <option value="10">Área</option>
                            </select>
                        </td>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 inputResponsive" id="filter_global">
                        <td align="center">
                            <input type="text" class="global_filter form-control" id="global_filter"
                                style="height: 35px;" placeholder="palabra a buscar...">
                        </td>
                    </div>
                    <div class="col-12 col-sm-1 col-md-1 col-lg-1 inputResponsive text-right">
                        <button type="button" id="selectBtn" class="btn btn-sm mt-1 col-lg-8 col-md-8 col-sm-8"
                        style="background-color: #163552;" > <img
                            src="{{ asset('landing/images/loupe (1).svg') }}" height="15"></button>
                    </div>


                    {{-- FINALZACION DE BUSQUEDA --}}
                </div>
                {{-- GIF DE BUSQUEDA --}}
                <div id="espera" class="text-center" style="display: none">
                    <img src="{{ asset('landing/images/loading.gif') }}" height="100">
                </div>
                {{-- FINALIZACION DE GIF --}}
                {{-- TABLA --}}
                <div id="tabladiv" class="divTableResponsive"></div>
                {{-- FINALIZACION DE TABLA --}}
            </div> <!-- end card body-->
        </div> <!-- end card -->

        @include('empleado.plantillaEmpleado')
    </div>


{{-- ---------------------------- --}}
{{-- visibilidad de editar --}}
@if (isset($modifEmp))
@if ($modifEmp==1)
<style>
    a[name="editarEName"] {
        display: inline;
    }
</style>
@else
<style>
    a[name="editarEName"] {
        display: none;
    }
</style>
{{-- <script>
document.getElementsByName("editarEName").remove();
</script> --}}
@endif
@else
<style>
    a[name="editarEName"] {
        display: inline;
    }
</style>
@endif

{{-- visibilidad de dar de baja --}}
@if (isset($bajaEmp))
@if ($bajaEmp==1)
<style>
    a[name="dBajaName"] {
        display: inline;
    }
</style>
@else
<style>
    a[name="dBajaName"] {
        display: none;
    }
</style>
@endif
@else
<style>
    a[name="dBajaName"] {
        display: inline;
    }
</style>
@endif

{{-- visibilidad de switch --}}
@if (isset($GestActEmp))
@if ($GestActEmp==1)
<input type="hidden" id="gestActI" value="1">
@else
<input type="hidden" id="gestActI" value="0">
@endif
@else
<input type="hidden" id="gestActI" value="1">
@endif
</div>
@endsection
@section('script')
<script>
    var urlFoto = "";
        var hayFoto = false;
        var id_empleado = '';
</script>
<script src="{{ asset('landing/js/actualizarPDatos.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>



<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ asset('admin/assets/libs/smartwizard/jquery.smartWizard.min.js') }}"></script>
<script>
    function maxLengthCheck(object) {
            if (object.value.length > object.maxLength)
                object.value = object.value.slice(0, object.maxLength)
        }
        function isNumeric(evt) {
            var theEvent = evt || window.event;
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode(key);
            var regex = /[0-9]|\./;
            if (!regex.test(key)) {
                theEvent.returnValue = false;
                if (theEvent.preventDefault) theEvent.preventDefault();
            }
        }
</script>
<script src="{{asset('landing/js/tabla.js')}}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap-fileinput/piexif.min.js') }} "></script>
<script src="{{ asset('admin/assets/libs/bootstrap-fileinput/sortable.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap-fileinput/purify.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap-fileinput/fileinput.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap-fileinput/theme.js') }}"></script>
<script src="{{ asset('admin/assets/libs/bootstrap-fileinput/es.js') }}"></script>


<script src="{{ asset('admin/assets/libs/moment/moment.min.js') }}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/es.js')}}"></script>

<script src="{{ asset('admin/packages/core/main.js') }}"></script>
<script src="{{ asset('admin/packages/core/locales/es.js') }}"></script>
<script src="{{ asset('admin/packages/daygrid/main.js') }}"></script>
<script src="{{ asset('admin/packages/timegrid/main.js') }}"></script>
<script src="{{ asset('admin/packages/interaction/main.js') }}"></script>
<script src="{{ asset('admin/assets/js/pages/form-wizard.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js')}}"></script>
<script src="{{ asset('landing/js/smartwizard.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/alertify/alertify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{ asset('landing/js/seleccionarDepProv.js') }}"></script>
<script src="{{ asset('landing/js/cargaMasivaF.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
<script src="{{ asset('landing/js/empleado.js') }}"></script>
<script src="{{ asset('landing/js/empleadoA.js') }}"></script>
<script src="{{ asset('landing/js/dispositivos.js') }}"></script>
<script src="{{asset('landing/js/modosEmpleado.js')}}"></script>
<script src="{{asset('landing/js/contrato.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
@endsection
