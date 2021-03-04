@php
use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Gestion de empleados</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ asset('landing/images/ICONO-LOGO-NUBE-RH.ico') }}">
    @php
    $fecha = Auth::user()->created_at->toDateTimeString();
    $dt = Carbon::create($fecha);
    $dt->isoFormat('YYYY-MM-DD');
    $actual = Carbon::now();
    $actual->modify('-1 months')->isoFormat('YYYY-MM-DD');
    @endphp
    @if ($dt > $actual)
    <script src="//code.jivosite.com/widget/OqxplJ3nCh" async></script>
    @endif
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
            height: 6px;
        }

        .form-control:disabled {
            background-color: #f1f0f0;
        }

        .large.tooltip-inner {
            max-width: 185px;
            width: 185px;
        }
    </style>


    <script type="text/javascript" src="{{ asset('admin/assets/pace/pace.min.js') }}"></script>
    <script src="https://player.vimeo.com/api/player.js"></script>
    {{-- <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.theme.default.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}"> --}}
    {{-- <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}"> --}}
    <link rel="stylesheet" href="{{ asset('landing/css/style.min.css') }}">
    <link href="{{ URL::asset('admin/assets/css/zoom.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->


    <!-- App css -->
    <link href="{{ asset('admin/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin/assets/libs/bootstrap-fileinput/fileinput.min.css') }}" rel="stylesheet"
        type="text/css" />

    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Plugin css  CALENDAR-->
    <link href="{{ asset('admin/packages/core/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/packages/daygrid/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/packages/timegrid/main.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/smartwizard/smart_wizard.min.css') }}" type="text/css" />
    <link href="{{ asset('admin/assets/libs/smartwizard/smart_wizard_theme_arrows.min.css') }}" type="text/css" />
    <link href="{{ asset('admin/assets/libs/smartwizard/smart_wizard_theme_circles.min.css') }}" type="text/css" />
    <link href="{{ asset('admin/assets/libs/smartwizard/smart_wizard_theme_dots.min.css') }}" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/alertify/alertify.css') }}" rel="stylesheet" type="text/css" />
    {{-- <link href="{{ URL::asset('admin/assets/libs/alertify/bootstrap.css') }}" rel="stylesheet" type="text/css" />
    --}}
    <!-- Semantic UI theme -->
    <link href="{{ URL::asset('admin/assets/libs/alertify/default.css') }}" rel="stylesheet" type="text/css" />
</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100" style="background-color: #fdfdfd;">

    <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="{{ asset('landing/images/notification.svg') }}" height="100">
                    <h4 class="text-danger mt-4">Su sesión expiró</h4>
                    <p class="w-75 mx-auto text-muted">Por favor inicie sesión nuevamente.</p>
                    <div class="mt-4">
                        <a href="{{ '/' }}" class="btn btn-outline-primary btn-rounded width-md"><i
                                class="uil uil-arrow-right mr-1"></i> Iniciar sesión</a>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->




    <style>
        .form-control {
            font-size: 12px;
        }

        /*   .flatpickr-calendar {
        max-width: 130px!important;
    } */
        .container {}

        tr:first-child>td>.fc-day-grid-event {
            margin-top: 0px;
            padding-top: 0px;
            padding-bottom: 0px;
            margin-bottom: 0px;
            margin-left: 2px;
            margin-right: 2px;
        }

        #calendarInv_ed>div.fc-view-container>div>table>tbody {
            background: #f4f4f4;
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

        #calendarInv>div.fc-view-container>div>table>tbody {
            background: #f4f4f4;
        }

        .fc-event,
        .fc-event-dot {
            font-size: 12.2px !important;
            margin: 2px 2px;
        }

        .fc-time {
            display: none;
        }

        .v-divider {
            border-right: 5px solid #4C5D73;
        }

        .table th,
        .table td {
            padding: 0.55rem;
            border-top: 1px solid #c9c9c9;
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

        body {
            background-color: #f8f8f8;
        }

        .hidetext {
            -webkit-text-security: disc;
            /* Default */
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

        .borderColor {
            border-color: red;
        }

        .cursorDispositivo {
            cursor: url("../landing/images/pencil.svg"), auto !important;
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

        @media (max-width: 767px) {
            .colResp {
                justify-content: center !important;
                padding: 10px 0px !important;
            }
        }

        .radio_rsp {
            padding: 0 10%;
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

        @media(min-width: 386px) and (max-width: 487px) {
            .th_rh {
                padding: 0 35px !important;
            }
        }
    </style>

    <header id="header-section">
        <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
            <div class="col-sm-3 col-md-2 col-xl-2 logo_rh">
                <div class="navbar-brand-wrapper d-flex w-100 colResp">
                    <a href="{{ route('principal') }}"><img src="{{ asset('landing/images/NUBE_SOLA.png') }}" class=""
                            height="69"></a>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-xl-6 text-center">
                <h5 style="color: #ffffff">Gestión de empleados</h5>
                <p for="" class="blanco">Tienes 2 minutos para registrar tu primer empleado</p>
            </div>

            <div class="col-sm-3 col-md-4 col-xl-4 text-center">
                <a href="{{ '/empleado/cargar' }}">
                    <button class="btn btn-sm btn-primary"
                        style="background-color: #183b5d;border-color:#62778c; margin-bottom: 2px;">
                        <img src="{{ asset('admin/images/subir.ico') }}" height="25" class="">Carga masiva emp.
                    </button>
                </a>
                <button class="btn btn-sm btn-primary" style="background-color: #183b5d;border-color:#62778c"
                    id="cargaMasivaF">
                    <img src="{{ asset('admin/images/image.ico') }}" height="25" class="">Carga masiva fotos
                </button>
            </div>
        </nav>

    </header>
    <div class="content-page" style="margin-top: 20px;margin-left: 0px">
        <div class="content">
            <div class="row row-divided">
                <div class="col-md-12 col-xl-12">
                    <div class="card">
                        <div class="card-body" style="padding-top: 0px; background: #ffffff; font-size: 12.8px;
                            color: #222222;   padding-left: 0px;  ">
                            <!--<h4 class="header-title mt-0 mb-1">Basic Data Table</h4>-->
                            <div class="row">

                                <div class="col-md-6">
                                    <h5 style="font-size: 16px!important">Búsqueda personalizada</h5>
                                </div>
                                <div class=" col-md-6 col-xl-6 text-right">
                                    <button class="btn btn-sm btn-primary mb-2" id="formNuevoE"
                                        style="background-color: #e3eaef;border-color:#e3eaef;color:#3d3d3d">
                                        Nuevo</button>
                                </div>
                                <div class="col-xl-4">
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label">Área:</label>
                                        <div class="col-lg-10">
                                            <select id="selectarea" data-plugin="customselect"
                                                class="form-control form-control-sm" multiple="multiple"
                                                data-placeholder="Seleccionar áreas">

                                                @foreach ($area as $areas)
                                                <option class="" value="{{ $areas->area_id }}">
                                                    {{ $areas->area_descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                    <label class="col-md-6 col-form-label">Seleccionar por:</label>
                                    <td align="center">
                                        <select class="form-control col-md-6" name="select" id="select" style="height: 35.5px;">
                                            <option value="2">Documento</option>
                                            <option value="3">Nombre</option>
                                            <option value="4" selected>Apellidos</option>
                                            <option value="9">Cargo</option>
                                            <option value="10">Área</option>
                                        </select>
                                    </td>
                                    </div>
                                </div>
                                <div class="col-md-4" id="filter_global">
                                    <td align="center">
                                        <input type="text" class="global_filter form-control" id="global_filter"
                                            style="height: 35px;" placeholder="palabra a buscar...">
                                    </td>
                                </div>
                                <div class="col-md-1 text-right">
                                    <button type="button" id="selectBtn" class="btn btn-sm mt-1 col-md-8"
                                    style="background-color: #163552;" > <img
                                        src="{{ asset('landing/images/loupe (1).svg') }}" height="15"></button>
                                </div>




                            </div>
                            <div id="espera" class="text-center" style="display: none">

                                <img src="{{ asset('landing/images/loading.gif') }}" height="100">
                            </div>
                            <div id="tabladiv">
                            </div>
                            <div class="text-right"><br><br>
                                <a href="{{ '/horario' }}">
                                    @if (count($empleado) > 0)
                                    <button id="btnContinuar" class="boton btn btn-default mr-1">CONTINUAR</button>
                                    @else
                                    <button id="btnContinuar" disabled
                                        title="Registre al menos un empleado para poder continuar"
                                        class="boton btn btn-default mr-1">CONTINUAR</button>

                                    @endif
                                </a>
                            </div>
                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div>

            </div>

        </div>

        @include('empleado.plantillaEmpleado')
    </div>
    </div>
    </div>
    <footer class="border-top"
        style="background:#163552; position: fixed; width: 100%; display: block; bottom: 0; margin-top: 10px">
        <div class="col-md-12 text-center"
            style="margin-top: 10px; border-top: 1.5px solid #ded9d9;padding-top: 10px;bottom: 10px;">
            <span style="color: #faf3f3;font-size: 12px!important">
                © <?php echo date('Y'); ?> - RH nube Corp - USA | Todos los derechos
                reservados &nbsp; |
            </span>
            <a style="font-size: 12px!important; color:#faf3f3;" href="/politicas">Política de privacidad | </a>
            <span style="color: #faf3f3;font-size: 12px!important">Central Perú: 017482415 | +51 914480786 |
                info@rhnube.com.pe</span>
        </div>
    </footer>
    <script>
        var urlFoto = "";
        var hayFoto = false;
        var id_empleado = '';

    </script>
    <!-- Vendor js -->
    {{-- <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script> --}}
    <script src="{{ asset('admin/assets/js/vendor.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('admin/assets/js/app.min.js') }}"></script>
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

    <script src="{{ asset('admin/assets/libs/bootstrap-fileinput/piexif.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/bootstrap-fileinput/sortable.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/bootstrap-fileinput/purify.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/bootstrap-fileinput/fileinput.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/bootstrap-fileinput/theme.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/bootstrap-fileinput/es.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/combodate-1.0.7/es.js') }}"></script>
    <script src="{{ asset('admin/packages/core/main.js') }}"></script>
    <script src="{{ asset('admin/packages/core/locales/es.js') }}"></script>
    <script src="{{ asset('admin/packages/daygrid/main.js') }}"></script>
    <script src="{{ asset('admin/packages/timegrid/main.js') }}"></script>
    <script src="{{ asset('admin/packages/interaction/main.js') }}"></script>
    <script src="{{ asset('admin/assets/js/pages/form-wizard.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/alertify/alertify.js') }}"></script>
    <script src="{{ asset('landing/js/tabla.js') }}"></script>
    <script src="{{ asset('landing/js/smartwizard.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
    <script src="{{ asset('landing/js/seleccionarDepProv.js') }}"></script>
    <script src="{{ asset('landing/js/cargaMasivaF.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
    <script src="{{ asset('landing/js/empleado.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js') }}"></script>
    <script src="{{ asset('landing/js/empleadoA.js') }}"></script>
    <script src="{{ asset('landing/js/dispositivos.js') }}"></script>
    <script src="{{ asset('landing/js/modosEmpleado.js') }}"></script>
    <script src="{{ asset('landing/js/contrato.js') }}"></script>
    @if (Auth::user())
    <script>
        $(function() {
                setInterval(function checkSession() {
                    $.get('/check-session', function(data) {
                        // if session was expired
                        if (data.guest == false) {
                            $('.modal').modal('hide');
                            $('#modal-error').modal('show');
                        }
                    });
                }, 7202000);
            });

    </script>
    @endif
    <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
</body>

</html>
