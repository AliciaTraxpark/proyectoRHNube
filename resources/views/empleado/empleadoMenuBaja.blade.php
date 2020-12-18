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

    /* FINALIZACION DE RESPONSIVE */

    div.dataTables_wrapper div.dataTables_filter {
        display: none;
    }

    .table {
        width: 100% !important;
    }

    .dataTables_scrollHeadInner {
        margin: 0 auto !important;
        width: 100% !important;
    }

    .table th,
    .table td {
        padding: 0.4rem;
        border-top: 1px solid #edf0f1;
    }

    .table th {
        font-size: 12.8px !important
    }

    .tooltip-arrow,
    .red-tooltip+.tooltip>.tooltip-inner {
        background-color: rgb(0, 0, 0);
    }

    .hidetext {
        -webkit-text-security: disc;
        /* Default */
    }

    .text-wrap {
        white-space: normal;
    }

    .width-400 {
        width: 150px !important;
    }

    .table-responsive,
    .dataTables_scrollBody {
        overflow: visible !important;
    }

    .alertify .ajs-body .ajs-content {
        padding: 16px 16px 16px 16px !important;
    }

    .ajs-body {
        font: 12.8px !important;
        padding: 0px !important;
        font-family: 'Roboto', sans-serif !important;
    }

    .alertify .ajs-footer {
        background: #ffffff;
        border-top: 1px solid #f6f6f7;
        border-radius: 0 0 4.8px 4.8px;
    }

    .alertify .ajs-footer .ajs-buttons .ajs-button {
        min-width: 88px;
        min-height: 35px;
        padding: 4px 8px 4px 8px;
    }

    @media (max-width: 767.98px) {

        .dataTable,
        .dataTables_scrollHeadInner,
        .dataTables_scrollBody {
            margin: 0 auto !important;
            width: 100% !important;
        }

        table.dataTable>tbody>tr.child ul.dtr-details {
            display: flex !important;
            flex-flow: column !important;
        }

        .width-400 {
            width: 100% !important;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child,
        table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child {
            padding-left: 8% !important;
            width: 100% !important;
        }
    }

    @media (max-width: 406px) {

        table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child,
        table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child {
            padding-left: 8% !important;
            width: 100% !important;
        }
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
{{-- BOTONES DE CARGAS MASIVAS --}}
<div class="row page-title titleResponsive" style="padding-right: 20px;">
    <div class="col-md-7">
        <h4 class="header-title mt-0 ">
            Empleados de baja
        </h4>
    </div>
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
                    {{-- BUSQUEDA PARA TABLA --}}
                    <div class="col-md-12">
                        <h5 style="font-size: 16px!important">Búsqueda personalizada</h5>
                    </div>
                    <div class="col-md-4 inputResponsive" id="filter_global">
                        <td align="center">
                            <input type="text" class="global_filter form-control" id="global_filter"
                                style="height: 35px;" placeholder="Buscar por...">
                        </td>
                    </div>
                    <div class="col-md-2 inputResponsive">
                        <td align="center">
                            <select class="form-control" name="select" id="select" style="height: 35.5px;">
                                <option value="2">Documento</option>
                                <option value="3">Nombre</option>
                                <option value="4" selected>Apellidos</option>
                                <option value="5">Cargo</option>
                                <option value="6">Área</option>
                            </select>
                        </td>
                    </div>
                    <div class="col-xl-6 inputResponsive">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Área:</label>
                            <div class="col-lg-10">
                                <select id="selectarea" data-plugin="customselect" class="form-control form-control-sm"
                                    multiple="multiple" data-placeholder="Seleccionar áreas">
                                    @foreach ($area as $areas)
                                    <option value="{{ $areas->area_id }}">
                                        {{ $areas->area_descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- FINALZACION DE BUSQUEDA --}}
                </div>
                {{-- GIF DE BUSQUEDA --}}
                <div id="espera" class="text-center" style="display: none">
                    <img src="{{ asset('landing/images/loading.gif') }}" height="100">
                </div>
                {{-- FINALIZACION DE GIF --}}
                {{-- TABLA --}}
                <div id="tabladiv" class="divTableResponsive">
                    <table id="tablaEmpleado" class="table nowrap" style="width:100%!important">
                        <thead style="background: #edf0f1;color: #6c757d;" style="width:100%!important">
                            <tr style="width:100%!important">
                                <th class="text-center">
                                    <input type="checkbox" style="margin-left: 15px" id="selectT">
                                </th>
                                <th class="text-center">
                                    <label for="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                </th>
                                <th class="text-center">Documento</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Cargo</th>
                                <th>Área</th>
                            </tr>
                        </thead>
                        <tbody style="background:#ffffff;color: #585858;font-size: 12.5px" id="tbodyr"></tbody>
                    </table>
                </div>
                {{-- FINALIZACION DE TABLA --}}
            </div>
        </div>
    </div>
    <div id="fechasmodalVer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fechasmodalVer"
        aria-hidden=" true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;">
                    <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Detalles de Contrato
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="$('#verEmpleadoDetalles').modal('show');">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="formContratoVer">
                    <form>
                        {{ csrf_field() }}
                        <input type="hidden" name="v_idContratoVer" id="v_idContratoVer">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sw-default">Condición Pago</label>
                                    <select class="form-control" name="v_condicionV" id="v_condicionV" required>
                                        <option value="">Seleccionar</option>
                                        @foreach ($condicionP as $condicion)
                                        <option class="" value="{{$condicion->id}}">
                                            {{$condicion->condicion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sw-default">Monto</label>
                                    <input type="number" step=".01" class="form-control" name="v_montoV" id="v_montoV">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="">Fecha Inicial</label>
                                <span id="m_validFechaCIE" style="color: red;display: none;">*Fecha
                                    incorrecta.</span>
                                <div class="row">
                                    <div class="col-md-4">
                                        <select class="form-control" name="m_dia_fechaIEV" id="m_dia_fechaIEV"
                                            required="">
                                            <option value="0">Día</option>
                                            @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                {{$i}}
                                                </option>
                                                @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" name="m_mes_fechaIEV" id="m_mes_fechaIEV"
                                            required="">
                                            <option value="0">Mes</option>
                                            <option class="" value="1">Ene.</option>
                                            <option class="" value="2">Feb.</option>
                                            <option class="" value="3">Mar.</option>
                                            <option class="" value="4">Abr.</option>
                                            <option class="" value="5">May.</option>
                                            <option class="" value="6">Jun.</option>
                                            <option class="" value="7">Jul.</option>
                                            <option class="" value="8">Ago.</option>
                                            <option class="" value="9">Set.</option>
                                            <option class="" value="10">Oct.</option>
                                            <option class="" value="11">Nov.</option>
                                            <option class="" value="12">Dic.</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" style="padding-left: 5px;
                                                    padding-right: 5px;" name="m_ano_fechaIEV" id="m_ano_fechaIEV"
                                            required="">
                                            <option value="0">Año</option>
                                            @for ($i = 2000; $i <2100; $i++) <option class="" value="{{$i}}">
                                                {{$i}}
                                                </option>
                                                @endfor
                                        </select>
                                    </div>

                                </div>
                                <br>
                                <div class="form-group">
                                    <label for="">Fecha Indefinida</label>
                                    <input type="checkbox" id="checkboxFechaIEV" name="checkboxFechaIEV">
                                </div>
                                <div id="ocultarFechaEV">
                                    <label for="">Fecha Final</label>
                                    <span id="m_validFechaCFE" style="color: red;display: none;">*Fecha
                                        incorrecta.</span>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select class="form-control" name="m_dia_fechaFEV" id="m_dia_fechaFEV">
                                                <option value="0">Día</option>
                                                @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                    {{$i}}</option>
                                                    @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-control" name="m_mes_fechaFEV" id="m_mes_fechaFEV">
                                                <option value="0">Mes</option>
                                                <option class="" value="1">Ene.</option>
                                                <option class="" value="2">Feb.</option>
                                                <option class="" value="3">Mar.</option>
                                                <option class="" value="4">Abr.</option>
                                                <option class="" value="5">May.</option>
                                                <option class="" value="6">Jun.</option>
                                                <option class="" value="7">Jul.</option>
                                                <option class="" value="8">Ago.</option>
                                                <option class="" value="9">Set.</option>
                                                <option class="" value="10">Oct.</option>
                                                <option class="" value="11">Nov.</option>
                                                <option class="" value="12">Dic.</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-control" style="padding-left: 5px;
                                                        padding-right: 5px;" name="m_ano_fechaFEV" id="m_ano_fechaFEV">
                                                <option value="0">Año</option>
                                                @for ($i = 2000; $i <2100; $i++) <option class="" value="{{$i}}">{{$i}}
                                                    </option>
                                                    @endfor
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('#verEmpleadoDetalles').modal('show');" class="btn btn-sm"
                        style="background: #163552;" data-dismiss="modal">Cerrar</button>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</div>
{{-- CRUD DE CONTRATO EN BAJA --}}
<div id="contratomodalB" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="contratomodalB"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Agregar contrato
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="$('#modalAlta').modal('show');javascript:limpiarBaja()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="javascript:agregarContratoB()">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-2">
                            <label for="">Contrato</label>
                        </div>
                        <div id="editarContratoB" class="col-md-6"></div>
                        <div class="col-md-4">
                            <a id="buscarContratoB" data-toggle="tooltip" data-placement="right" title="editar contrato"
                                data-original-title="editar contrato" style="cursor: pointer;">
                                <img src="{{ asset('landing/images/search.svg') }}" height="18">
                            </a>
                        </div>
                    </div>
                    <div class="col-md-12 pt-2">
                        <input type="text" class="form-control" name="textContratoB" id="textContratoB" required>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="$('#modalAlta').modal('show');javascript:limpiarBaja()"
                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-sm" style="background-color:#163552;">Guardar</button>
            </div>
            </form>
        </div>
    </div>
</div>
{{-- FINALIZACION DE MODAL --}}
{{-- CRUD DE CONDICION DE PAGO EN REGISTRAR --}}
<div id="condicionmodalB" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="condicionmodalB"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Agregar condición de pago
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="$('#modalAlta').modal('show');javascript:limpiarBaja()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="javascript:agregarCondicionB()">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-2">
                            <label for="">Condición</label>
                        </div>
                        <div id="editarCondicionB" class="col-md-6"></div>
                        <div class="col-md-4">
                            <a id="buscarCondicionB" data-toggle="tooltip" data-placement="right"
                                title="editar condición de pago" data-original-title="editar condición de pago"
                                style="cursor: pointer;">
                                <img src="{{asset('landing/images/search.svg')}}" height="18">
                            </a>
                        </div>
                    </div>
                    <div class="col-md-12 pt-2">
                        <input type="text" class="form-control" name="textCondicionB" id="textCondicionB" required>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="$('#modalAlta').modal('show');javascript:limpiarBaja()"
                    class="btn btn-sm btn-light" data-dismiss="modal">
                    Cerrar
                </button>
                <button type="submit" class="btn btn-sm" style="background-color:#163552;">
                    Guardar
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
{{-- FINALIZACION DE MODAL --}}
{{-- MODAL DE VER EMPLEADO --}}
<div id="verEmpleadoDetalles" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="verEmpleadoDetalles"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" style="max-width: 850px;">
        <div class="modal-content">
            <div class="modal-header" style="background: #163552;">
                <h4 class="header-title mt-0 " style="color: #f0f0f0">Datos de empleado</h4><br>
                <button type="button" class="close" id="cerrarEd" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0px;">
                <div id="smartwizardVer" style="background: #ffffff; color:#3d3d3d;">
                    <ul style="background: #fdfdfd!important;font-size: 13px;">
                        <li><a href="#persona-step-1">Personales</a></li>
                        <li><a href="#sw-default-step-2">Empresarial</a></li>
                        <li><a href="#sw-default-step-3">Foto</a></li>
                        <li><a href="#sw-default-step-4">Calendario</a></li>
                        <li><a href="#sw-default-step-5">Horario</a></li>
                        <li><a href="#sw-default-step-6">Actividades</a></li>
                        <li><a href="#sw-default-step-7">Dispositivo</a></li>
                        <li><a href="#sw-default-step-8">Historial</a></li>
                    </ul>
                    <div class="p-3" id="form-registrar">
                        <div id="persona-step-1" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-4">
                                    <input style="display: none;" name="v_idV" id="v_idV">
                                    <div class="form-group">
                                        <label for="sw-default">Tipo Documento</label>
                                        <input type="text" class="form-control" name="v_tipoDocV" id="v_tipoDocV"
                                            style="background-color: #fcfcfc;" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Apellido Paterno</label>
                                        <input type="text" class="form-control" name="v_apPaternoV" id="v_apPaternoV"
                                            disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="v_emailV" name="v_emailV" disabled>
                                    </div>
                                </div> <!-- end col -->
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="sw-default">Num. Documento</label>
                                        <input type="text" class="form-control" name="v_numDocumentoV"
                                            id="v_numDocumentoV" style="background-color: #fcfcfc" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Apellido Materno</label>
                                        <input type="text" class="form-control" name="v_apMaternoV" id="v_apMaternoV"
                                            disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Celular</label>
                                        <input type="text" class="form-control" name="v_celularV" id="v_celularV"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="sw-default">Fecha Nacimiento</label>

                                        <div class="row fechasResponsive">
                                            <div class="col-md-4 prigth">
                                                <select class="form-control" name="v_dia_fechaV" id="v_dia_fechaV"
                                                    required="">
                                                    <option value="0">Día</option>
                                                    @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">{{$i}}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-4 prigth">
                                                <select class="form-control" name="v_mes_fechaV" id="v_mes_fechaV"
                                                    required="">
                                                    <option value="0">Mes</option>
                                                    <option class="" value="1">Ene.</option>
                                                    <option class="" value="2">Feb.</option>
                                                    <option class="" value="3">Mar.</option>
                                                    <option class="" value="4">Abr.</option>
                                                    <option class="" value="5">May.</option>
                                                    <option class="" value="6">Jun.</option>
                                                    <option class="" value="7">Jul.</option>
                                                    <option class="" value="8">Ago.</option>
                                                    <option class="" value="9">Set.</option>
                                                    <option class="" value="10">Oct.</option>
                                                    <option class="" value="11">Nov.</option>
                                                    <option class="" value="12">Dic.</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 pAnio pleft">
                                                <select class="form-control" style="padding-left: 5px;
                                                padding-right: 5px;" name="v_mes_fechaV" id="v_ano_fechaV" required="">
                                                    <option value="0">Año</option>
                                                    @for ($i = 1950; $i <2011; $i++) <option class="" value="{{$i}}">
                                                        {{$i}}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </div>

                                        </div>
                                        {{-- <input type="text" class="form-control" id="v_fechaNV"
                                            data-custom-class="form-control" data-format="YYYY-MM-DD"
                                            data-template="D MMM YYYY" name="date" disabled> --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Nombres</label>
                                        <input type="text" class="form-control" name="v_nombresV" id="v_nombresV"
                                            disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Teléfono</label>
                                        <input type="text" class="form-control" name="v_telefonoV" id="v_telefonoV"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="sw-default">Dirección</label>
                                        <input type="text" class="form-control" name="v_direccionV" id="v_direccionV"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="sw-default">Dirección
                                            Domiciliara</label>
                                        <input type="text" class="form-control" placeholder="Departamento" name="v_depV"
                                            id="v_depV" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Lugar Nacimiento</label>
                                        <input type="text" class="form-control" placeholder="Departamento"
                                            name="v_departamentoV" id="v_departamentoV" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label class="normal" for="">Género</label>
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="v_tipoV" id="v_tipoV" value="Femenino" disabled>
                                            Femenino
                                        </label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="sw-default"><br></label>
                                        <input type="text" class="form-control" placeholder="Provincia " name="v_provV"
                                            id="v_provV" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default"><br></label>
                                        <input type="text" class="form-control" placeholder="Provincia "
                                            name="v_provinciaV" id="v_provinciaV" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label class="normal" for=""><br></label>
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="v_tipoV" id="v_tipoV" value="Masculino" disabled>
                                            Masculino
                                        </label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="sw-default"><br></label>
                                        <input type="text" class="form-control" placeholder="Distrito " name="v_distV"
                                            id="v_distV" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default"><br></label>
                                        <input type="text" class="form-control" placeholder="Distrito "
                                            name="v_distritoV" id="v_distritoV" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label class="normal" for=""><br></label>
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="v_tipoV" id="v_tipoV" value="Personalizado"
                                                disabled>
                                            Personalizado
                                        </label>
                                    </div>
                                </div>
                            </div> <!-- end row -->
                        </div>
                        <div id="sw-default-step-2" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="sw-default">Codigo Empleado</label>
                                        <input type="text" class="form-control" name="v_codigoEmpleadoV" maxlength="200"
                                            id="v_codigoEmpleadoV" disabled>
                                    </div>
                                </div>
                                <div class="col-4"><br></div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="sw-default">Cargo</label>
                                        <input type="text" class="form-control" name="v_cargoV" id="v_cargoV" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Contrato
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a id="detalleContratoVer"
                                                onclick="$('#verEmpleadoDetalles').modal('hide');"
                                                href="#fechasmodalVer" data-toggle="modal" data-target="#fechasmodalVer"
                                                data-toggle="tooltip" data-placement="right"
                                                title="Detalle de Contrato." data-original-title="Detalle de Contrato."
                                                style="cursor: pointer;">
                                                <img src="{{asset('landing/images/adaptive.svg')}}" height="18">
                                            </a>
                                        </label>
                                        <input type="text" class="form-control" name="v_contratoV" id="v_contratoV"
                                            tabindex="5" disabled>
                                    </div>
                                </div> <!-- end col -->
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="sw-default">Área</label>
                                        <input type="text" class="form-control" name="v_areaV" id="v_areaV" tabindex="3"
                                            disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Nivel del Colaborador</label>
                                        <input type="text" class="form-control" name="v_nivelV" id="v_nivelV"
                                            tabindex="6" disabled>
                                    </div>
                                </div> <!-- end col -->
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="sw-default">Centro Costo</label>
                                        <input type="text" class="form-control" name="v_centrocV" id="v_centrocV"
                                            tabindex="4" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="sw-default">Local</label>
                                        <input type="text" class="form-control" name="v_localV" id="v_localV"
                                            tabindex="7" disabled>
                                    </div>
                                </div> <!-- end col -->
                            </div>
                        </div>
                        <div id="sw-default-step-3" style="font-size: 12px!important">
                            <br><br>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group text-center">
                                        <img src="landing/images/png.svg" height="150" id="v_fotoV">
                                        <br><br>
                                        <h5 id="h5Ocultar" class="m-0 font-size-14" style="color:#8888">
                                            No se encontro imagen</h5>
                                    </div>
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div>
                        <div id="sw-default-step-4" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-md-4 text-right"><label for=""
                                        style="margin-top: 7px;font-weight: 600">Calendario:</label></div>
                                <div class="col-md-4 text-center" id="divescond1_ver">
                                    <input type="hidden" id="idselect3_ver">
                                    <select name="" id="selectCalendario_edit3_ver" class="form-control form-control-sm"
                                        style="margin-top: 4px;" disabled>
                                        <option hidden selected>Asignar calendario</option>
                                        @foreach ($calendario as $calendarios)
                                        <option class="" value="{{ $calendarios->calen_id }}">
                                            {{ $calendarios->calendario_nombre }}</option>
                                        @endforeach
                                    </select><br><br>
                                </div>
                                <div class="col-md-4"></div>
                                <input type="hidden" name="" id="idempleado">
                                <div class="col-md-1"><br></div>

                                <div class="col-md-10" id="calendar3"></div>
                                <div class="col-md-1"><br></div>
                            </div>
                        </div>
                        <div id="sw-default-step-5" style="font-size: 12px!important">
                            <div class="row">

                                <div class="col-md-1"><br></div>
                                <div class="col-md-10" id="calendar4"></div>
                                <div class="col-md-1"><br></div>
                            </div>
                        </div>
                        <div id="sw-default-step-6" class="setup-content" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card">
                                        <div class="card-body border p-2">
                                            <div class="row pt-3">
                                                <div class="col-xl-12 col-sm-12">
                                                    <div class="table-responsive-xl scroll">
                                                        <table class="table" style="font-size: 13px!important;">
                                                            <thead style="background: #fafafa;font-size: 14px">
                                                                <tr>
                                                                    <th>Actividad</th>
                                                                    <th>Estado</th>
                                                                    <th>Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tablaBodyTarea_ver"
                                                                style="background:#ffffff;color: #585858;font-size: 12px">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="col-xl-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row pb-1 pl-2">
                                                <div class="col">
                                                    <div class="custom-control custom-switch mb-2">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="customSwitch6">
                                                        <label class="custom-control-label" for="customSwitch6"
                                                            style="font-weight: bold">Modo Control de
                                                            Asistencia en Puerta</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body border p-2" id="bodyModoProyecto_ver">
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        <div id="sw-default-step-7" style="font-size: 12px!important">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card border"
                                        style="border-radius: 5px;border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                                        <div class="card-header"
                                            style="padding: 0.2rem 1rem;background: #383e56!important;color:white !important;border-top-right-radius: 5px; border-top-left-radius: 5px;">
                                            <div class="row pb-1 pl-2">
                                                <div class="col">
                                                    <div class="custom-control custom-switch mb-2">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="customSwitchCV1">
                                                        <label class="custom-control-label" for="customSwitchCV1"
                                                            style="font-weight: bold">
                                                            <i data-feather="activity"
                                                                style="height: 15px !important;width: 15px !important;color:white !important">
                                                            </i>&nbsp;&nbsp;
                                                            Modo Control Remoto
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3" id="bodyModoControlRV">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table id="ver_tablaDispositivo" class="table table-hover"
                                                            style="font-size: 13px!important;width: 100% !important">
                                                            <thead
                                                                style="background: #fafafa;font-size: 14px;width: 100% !important">
                                                                <tr>
                                                                    <th>Dispositivo</th>
                                                                    <th>Nombre</th>
                                                                    <th>Activación</th>
                                                                    <th>Codigo</th>
                                                                    <th>Enviado</th>
                                                                    <th>Estado</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="ver_tbodyDispositivo"
                                                                style="background:#ffffff;color: #585858;font-size: 12px">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card border"
                                        style="border-radius: 5px;border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                                        <div class="card-header"
                                            style="padding: 0.2rem 1rem;background: #383e56!important;color:white !important;border-top-right-radius: 5px; border-top-left-radius: 5px;">
                                            <div class="row pb-1 pl-2">
                                                <div class="col">
                                                    <div class="custom-control custom-switch mb-2">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="customSwitchCV2">
                                                        <label class="custom-control-label" for="customSwitchCV2"
                                                            style="font-weight: bold">
                                                            <i data-feather="map-pin"
                                                                style="height: 15px !important;width: 15px !important;color:white !important">
                                                            </i>&nbsp;&nbsp;
                                                            Modo Control en Ruta
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3" id="bodyModoControlAV">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table id="ver_tablaDispositivoA" class="table table-hover"
                                                            style="font-size: 13px!important;width: 100% !important">
                                                            <thead
                                                                style="background: #fafafa;font-size: 14px;width: 100% !important">
                                                                <tr>
                                                                    <th>Dispositivo</th>
                                                                    <th>Nombre</th>
                                                                    <th>Codigo</th>
                                                                    <th>Número</th>
                                                                    <th>Actividad (%)</th>
                                                                    <th>Enviado</th>
                                                                    <th>Estado</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="ver_tbodyDispositivoA"
                                                                style="background:#ffffff;color: #585858;font-size: 12px;width: 100% !important">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="sw-default-step-8" class="setup-content" style="font-size: 12px!important">
                            <div class="col-md-12">
                                <label for="">Historial de empleado de altas y bajas</label>
                            </div>

                            <div class="col-xl-12 col-sm-12">
                                <div class="table-responsive-xl">
                                    <table id="ver_tablaHistorial" class="table" style="font-size: 13px!important;">
                                        <thead style="background: #fafafa;">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Documento</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ver_tbodyHistorial"
                                            style="background:#ffffff;color: #585858;font-size: 12px">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
{{-- NUEVA ALTA --}}
<div id="modalAlta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalAlta" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog modal-lg d-flex justify-content-center">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Dar de alta
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:limpiarDatosAlta();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input type="hidden" id="idEmpleadoBaja">
            <div class="modal-body">
                <form action="javascript:nuevaAlta()">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label for="sw-default">Contrato
                                    <a onclick="$('#modalAlta').modal('hide');$('#contratomodalB').modal('show');"
                                        data-toggle="modal"><i class="uil uil-plus"
                                            style="color: darkblue;cursor: pointer;"></i>
                                    </a>
                                </label>
                                <select class="form-control" name="contratoB" id="contratoB" tabindex="5"
                                    onclick="javascript:validacionAlta()" required>
                                    <option value="">Seleccionar</option>
                                    @foreach ($tipo_cont as $tipo_conts)
                                    <option class="" value="{{ $tipo_conts->contrato_id }}">
                                        {{ $tipo_conts->contrato_descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-2 border-top">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sw-default">Condición Pago
                                            <a onclick="$('#modalAlta').modal('hide');" href="#condicionmodalB"
                                                data-toggle="modal" data-target="#condicionmodalB">
                                                <i class="uil uil-plus"></i>
                                            </a>
                                        </label>
                                        <select class="form-control" name="condicionB" id="condicionB" required>
                                            <option value="">Seleccionar</option>
                                            @foreach ($condicionP as $condicion)
                                            <option class="" value="{{$condicion->id}}">
                                                {{$condicion->condicion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sw-default">Monto</label>
                                        <input type="number" step=".01" class="form-control" name="montoB" id="montoB">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="" style="font-weight: 600">Fecha Inicial</label>
                                    <span id="validFechaCIB" style="color: red;display: none;">*Fecha
                                        incorrecta.</span>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select class="form-control" name="m_dia_fechaIB" id="m_dia_fechaIB"
                                                required="">
                                                <option value="0">Día</option>
                                                @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                    {{$i}}
                                                    </option>
                                                    @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-control" name="m_mes_fechaIB" id="m_mes_fechaIB"
                                                required="">
                                                <option value="0">Mes</option>
                                                <option class="" value="1">Ene.</option>
                                                <option class="" value="2">Feb.</option>
                                                <option class="" value="3">Mar.</option>
                                                <option class="" value="4">Abr.</option>
                                                <option class="" value="5">May.</option>
                                                <option class="" value="6">Jun.</option>
                                                <option class="" value="7">Jul.</option>
                                                <option class="" value="8">Ago.</option>
                                                <option class="" value="9">Set.</option>
                                                <option class="" value="10">Oct.</option>
                                                <option class="" value="11">Nov.</option>
                                                <option class="" value="12">Dic.</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-control" style="padding-left: 5px;padding-right: 5px;"
                                                name="m_ano_fechaIB" id="m_ano_fechaIB" required>
                                                <option value="0">Año</option>
                                                @for ($i = 2000; $i <2100; $i++) <option class="" value="{{$i}}">
                                                    {{$i}}
                                                    </option>
                                                    @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 pt-1" id="validArchivoB" style="display: none;">
                                            <span style="color: red;">
                                                *El tamaño supera el limite de 4 MB.
                                            </span>
                                        </div>
                                        <div class="col-md 12">
                                            <div class="form-group" style="margin-top: 14px;margin-bottom: 0px;">
                                                <input type="file"
                                                    accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                                                    class="inputfile inputfile-1"
                                                    data-multiple-caption="{count} archivos seleccionados" multiple
                                                    id="fileArchivosB" size="4194304">
                                                <label for="fileArchivosB">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17"
                                                        viewBox="0 0 20 17">
                                                        <path
                                                            d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z">
                                                        </path>
                                                    </svg>
                                                    <span class="iborrainputfile">Adjuntar archivo</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <label for="" style="font-weight: 600">Fecha Final</label><br>
                                        <label for="">Fecha Indefinida</label>
                                        <input type="checkbox" id="checkboxFechaIB" name="checkboxFechaIB">
                                    </div>
                                    <div id="ocultarFechaEN">
                                        <span id="m_validFechaCFB" style="color: red;display: none;">*Fecha
                                            incorrecta.</span>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <select class="form-control" name="m_dia_fechaFB" id="m_dia_fechaFB">
                                                    <option value="0">Día</option>
                                                    @for ($i = 1; $i <32; $i++) <option class="" value="{{$i}}">
                                                        {{$i}}</option>
                                                        @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control" name="m_mes_fechaFB" id="m_mes_fechaFB">
                                                    <option value="0">Mes</option>
                                                    <option class="" value="1">Ene.</option>
                                                    <option class="" value="2">Feb.</option>
                                                    <option class="" value="3">Mar.</option>
                                                    <option class="" value="4">Abr.</option>
                                                    <option class="" value="5">May.</option>
                                                    <option class="" value="6">Jun.</option>
                                                    <option class="" value="7">Jul.</option>
                                                    <option class="" value="8">Ago.</option>
                                                    <option class="" value="9">Set.</option>
                                                    <option class="" value="10">Oct.</option>
                                                    <option class="" value="11">Nov.</option>
                                                    <option class="" value="12">Dic.</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control"
                                                    style="padding-left: 5px;padding-right: 5px;" name="m_ano_fechaFB"
                                                    id="m_ano_fechaFEN">
                                                    <option value="0">Año</option>
                                                    @for ($i = 2000; $i <2100; $i++) <option class="" value="{{$i}}">
                                                        {{$i}}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="javascript:limpiarDatosAlta();$('#modalAlta').modal('toggle');"
                    class="btn btn-sm btn-light" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-sm" style="background-color:#163552;" id="guardarAltaB">Guardar</button>
            </div>
            </form>
        </div>
    </div>
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
<script src="{{ asset('admin/assets/libs/moment/moment.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
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
<script src="{{ asset('landing/js/empleadoBaja.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/alertify/alertify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')}}"></script>
<script src="{{ asset('landing/js/dispositivos.js')}}"></script>
<script src="{{asset('landing/js/modosEmpleado.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
@endsection