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
    <link rel="stylesheet" href="{{ asset('landing/vendors/aos/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/style.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
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

    <!-- App css -->
    <link href="{{ asset('admin/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Plugin css  CALENDAR-->
    <link href="{{ asset('admin/packages/core/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/packages/daygrid/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/packages/timegrid/main.css') }}" rel="stylesheet" />

    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet"
        type="text/css" />
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

        @media (max-width: 767px) {
            .colResp {
                justify-content: center !important;
                padding: 10px 0px !important;
            }
        }

        .colResp {
            padding-left: 40px;
        }

        .large.tooltip-inner {
            max-width: 185px;
            width: 185px;
        }
        .borderColor {
            border-color: red;
        }
        .loader {
        position: fixed;
         left: 40%;
        top: 30%;
      /*   width: 50%; */
        height: 30%; 
        z-index: 9999;
        opacity: .8;
        background: rgb(252,252,252);
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
        <nav class="navbar navbar-expand-lg" id="navbar">
            <div class="col-sm-2 col-md-2 col-xl-2">
                <div class="navbar-brand-wrapper d-flex w-100 colResp">
                    <img src="{{ asset('landing/images/NUBE_SOLA.png') }}" height="69">
                </div>
            </div>
            <div class="col-sm-10 col-md-10 col-xl-8 text-center">
                <h5 style="color: #ffffff">Gestión de Horarios</h5>
                <label for="" class="blanco font-italic">Ahora creamos y asignamos horarios</label>
            </div>
        </nav>
    </header>
    <div class="content-page" style="margin-top: 20px;margin-left: 0px">
        <div class="content">
            <div class="row row-divided">
                <div class="col-m-12 col-xl-12">
                    @include('horarios.horarioPlantilla')
                </div>
               
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

    <!-- Vendor js -->
    {{-- <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script> --}}
    <script src="{{ asset('admin/assets/js/vendor.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('admin/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
    <script src="{{ asset('landing/js/horario.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admin/packages/core/main.js') }}"></script>
    <script src="{{ asset('admin/packages/core/locales/es.js') }}"></script>
    <script src="{{ asset('admin/packages/daygrid/main.js') }}"></script>
    <script src="{{ asset('admin/packages/timegrid/main.js') }}"></script>
    <script src="{{ asset('admin/packages/interaction/main.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>

    <script>
        

    </script>
    <script>
        function finalizar() {
            $.ajax({
                type: "post",
                url: "/cambiarEstado",

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    $(location).attr('href', '/');
                },
                error: function(data) {
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
</body>

</html>
