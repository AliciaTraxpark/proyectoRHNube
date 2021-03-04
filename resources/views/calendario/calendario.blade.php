@php
use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Calendario</title>
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

    <!-- Plugin css  CALENDAR-->
    <link href="{{ asset('admin/packages/core/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/packages/daygrid/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/packages/timegrid/main.css') }}" rel="stylesheet" />
    <!-- App css -->
    <link href="{{ asset('admin/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet"
        type="text/css" />
    <script src="{{ asset('admin/assets/hopscotch/hopscotch.min.js') }}"></script>
    <link href="{{ asset('admin/assets/hopscotch/hopscotch.min.css') }}" rel="stylesheet" type="text/css">
</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
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
        /*  body > div.bootbox.modal.fade.show > div > div > div{
        background: #131313;
    color: #fbfbfb;
    }
    body > div.bootbox.modal.fade.show > div{
        top: 100px;
    left: 75px;
    } */

        div.fc-bg>table>tbody>tr>td.fc-day.fc-widget-content.fc-mon,
        td.fc-day.fc-widget-content.fc-tue,
        td.fc-day.fc-widget-content.fc-wed,
        td.fc-day.fc-widget-content.fc-thu,
        td.fc-day.fc-widget-content.fc-fri,
        td.fc-day.fc-widget-content.fc-sat {

            background-color: #ffffff;
        }

        .fc-time {
            display: none;
        }

        .fc-Descanso-button {
            color: #fff;
            background-color: #162029;
        }

        .fc-NoLaborales-button {
            color: #fff;
            background-color: #162029;
        }

        .fc-Feriado-button {
            color: #fff;
            background-color: #162029;
        }

        div.hopscotch-bubble .hopscotch-bubble-number {
            background: #575daf;
            padding: 0;
            border-radius: 50%;
        }

        div.hopscotch-bubble {
            border: 5px solid #788fa5;
            border-radius: 5px;
            margin-left: 63%;
        }

        @media(min-width: 525px) {
            div.hopscotch-bubble {
                margin-left: 43% !important;
            }
        }

        div.hopscotch-bubble .hopscotch-bubble-arrow-container.right .hopscotch-bubble-arrow-border {
            border-left: 17px solid #788fa5;
        }

        div.hopscotch-bubble h3 {

            font-size: 14px;
            font-weight: 600;
            margin: -1px 1px 0 0;
        }

        div.hopscotch-bubble .hopscotch-bubble-arrow-container.left .hopscotch-bubble-arrow-border {
            border-right: 17px solid rgb(120, 143, 165);
        }

        .fc-nuevoAño-button {
            left: 10px;
            font-size: 12px;
            padding-left: 6px;
            padding-right: 6px;

        }

        .fc-Asignar-button {
            left: 10px;
            font-size: 12px;
            padding-left: 6px;
            padding-right: 6px;
            padding-bottom: 7px;
            padding-top: 8px;

        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #52565b;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fdfdfd;
        }

        .col-md-6 .select2-container .select2-selection {
            height: 50px;
            font-size: 12.2px;
            overflow-y: scroll;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background: #ced0d3;
        }

        .table td {
            padding-top: 0.3rem;
            padding-bottom: 0.3rem;
        }

        .fc-button {
            background: #163552;
            color: #ffffff;
        }

        body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-footer>button.btn.btn-primary.bootbox-accept {
            background-color: #163552;
            border-color: #163552;
        }

        body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-header {
            background-color: #163552;
        }

        body>div.bootbox.modal.fade.bootbox-confirm.show>div>div>div.modal-header>h5 {
            color: #fff;
            font-size: 15px !important;
        }

        footer {
            font-size: 15px;
            color: #555;
            background: #eee;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
            margin: 0;
            padding: 0;
            z-index: 100;
        }

        @media(max-width: 867px) {
            .btn_rh {
                margin-bottom: 15px;
            }
        }

        @media(max-width: 446px) {
            .btn_rh {
                margin-bottom: 35px;
            }
        }

        @media(max-width: 306px) {
            .btn_rh {
                margin-bottom: 55px;
            }
        }

        @media(max-width: 767px) {
            .logo_rh {
                justify-content: center !important;
            }
        }

        @media(min-width: 768px) {
            .content_rh {
                margin-left: 55px !important;
                margin-right: 55px !important;
            }

            .text_rh {
                font-size: 10px !important;
            }
        }

        .botonesD {
            padding-bottom: 10px !important;
            padding-top: 10px !important;
            padding-right: 10px !important;
            padding-left: 10px !important;
        }

    </style>

    <header id="header-section">
        <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
            <div class="container pb-3">
                <div class="col-md-2 col-xl-2 p-0">
                    <div class="navbar-brand-wrapper d-flex w-200 logo_rh">
                        <a href="{{ route('principal') }}"><img src="{{ asset('landing/images/NUBE_SOLA.png') }}"
                                height="69"></a>
                    </div>
                </div>

                <div class="col-md-6 text-center">
                    <h5 style="color: #ffffff">Gestión de Calendarios</h5>
                    <label for="" class="blanco font-italic">Calendario de Perú, puedes crear calendarios regionales o
                        personalizados</label>
                </div>



                <div class="col-md-4 col-12">
                    <div class="row text-center">
                        <div class="col-md-6 col-6">
                            <select name="" id="selectCalendario" class="form-control">
                                @foreach ($calendario as $calendarios)
                                    <option class="" value="{{ $calendarios->calen_id }}">
                                        {{ $calendarios->calendario_nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 text-left col-6">
                            <button onclick="abrirNcalendario()" class="boton" style="font-size: 12px;padding: 4px">+
                                Nuevo calendario</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6 bg-primary">

                </div>
            </div>
        </nav>
    </header>

    <div class="content-page content_rh" style="margin-top: 40px; padding: 0px 5px">
        <div class="content">


            <div class="row ">
                <div class="col-md-1 col-0"></div>
                <div class="col-md-9 col-9" id="calendar">

                </div>




                <div class="col-md-12"><br></div>
                <div class="col-md-1"></div>
                <div class="col-md-5 col-6 pr-2">
                    <div class="row">
                        <div class="col-md-9">
                            <label style="font-size: 13px; font-weight:600 " for="">Programación de:
                                {{ $fechaEnvi }} hasta: <label style="font-size: 13px;font-weight:600" for=""
                                    id="fechaHasta"></label></label>
                        </div>
                        <div class="col-md-3 text-right">
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6 text-right pl-2">
                    <label for="" style="font-style:oblique">Creación de empresa:
                        {{ $fechaOrga->format('d/m/Y') }}</label>
                </div>
                <input type="hidden" id="pruebaStar">
                <input type="hidden" id="pruebaEnd">

                @include('calendario.calendarioPlantilla')

                <div class="col-md-12 text-right btn_rh">
                    <a href="{{ '/empleado' }}"><button class="boton btn btn-default mr-1">CONTINUAR</button></a>
                </div>
            </div>
        </div>
    </div>

    <footer class="border-top" style="background:#163552;">
        <div class="col-md-12 text-center"
            style="margin-top: 10px;border-top: 1.5px solid #ded9d9;padding-top: 10px;bottom: 10px;">
            <span style="color: #faf3f3;font-size: 12px!important">
                © <?php echo date('Y'); ?> - RH nube Corp - USA | Todos los derechos
                reservados &nbsp; |
            </span>
            <a style="font-size: 12px!important; color:#faf3f3;" href="/politicas">Política de privacidad | </a>
            <span style="color: #faf3f3;font-size: 12px!important">Central Perú: 017482415 | +51 914480786 |
                info@rhnube.com.pe</span>
        </div>
    </footer>
    <script src="{{ asset('landing/vendors/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('landing/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('landing/vendors/owl-carousel/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('landing/vendors/aos/js/aos.js') }}"></script>
    <script src="{{ asset('landing/js/landingpage.js') }}"></script>
    <script src="{{ asset('landing/js/SeleccionarPais.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
    <!-- Vendor js -->
    {{-- <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script> --}}

    <!-- plugin js -->
    <script src="{{ asset('admin/assets/libs/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admin/packages/core/main.js') }}"></script>
    <script src="{{ asset('admin/packages/core/locales/es.js') }}"></script>
    <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('admin/packages/daygrid/main.js') }}"></script>
    <script src="{{ asset('admin/packages/timegrid/main.js') }}"></script>
    <script src="{{ asset('admin/packages/interaction/main.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>

    <script src="{{ asset('landing/js/calendario.js') }}"></script>
    <script>
        $(document).ready(function() {

            hopscotch.startTour({
                id: "my-intro",
                i18n: {
                    nextBtn: ">",
                    prevBtn: "<",
                    doneBtn: "Entendido"
                },

                steps: [{
                        target: ".fc-view-container",
                        title: "Seleccione día(s)",
                        placement: "left",
                        width: 161,
                        yOffset: 30
                    }

                    /*  {target:"#calendarioAsignar",
          title:"Selecione el tipo de dia",
          placement:"right",
          width:200,
          yOffset:0,
          xOffset:0,
          arrowOffset:0


        }, */

                ]
            })

        });

    </script>
    @if (Auth::user())
        <script>
            $(function() {
                setInterval(function checkSession() {
                    $.get('/check-session', function(data) {

                        // if session was expired
                        if (data.guest == false) {
                            $('.modal').modal('hide');
                            $('#modal-error').modal('show');
                            $(".hopscotch-bubble-arrow-border").remove();
                            $(".hopscotch-bubble-container").remove();

                        }
                    });
                }, 7202000); // every minute
            });

        </script>
        <script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
    @endif
</body>

</html>
