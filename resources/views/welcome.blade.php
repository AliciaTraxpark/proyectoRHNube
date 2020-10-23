<!DOCTYPE html>
<html lang="es">

<head>
    <title>RH nube</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.theme.default.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
    <link rel="shortcut icon" href="{{asset('landing/images/ICONO-LOGO-NUBE-RH.ico')}}">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-169261172-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-169261172-1');

    </script>
</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
    <style>
        body {
            background-color: #ffffff !important;
        }

        .pResp {
            padding-top: 16px !important;
            padding-bottom: 30px !important;
        }
        }

        @media (min-width: 1564px) and (max-width: 1579px) {
            .pResp {
                padding-top: 3% !important;
                padding-bottom: 12% !important;
            }
        }

        @media (max-width: 1563px) and (min-width: 1541px) {
            .pResp {
                padding-top: 16px !important;
                padding-bottom: 30px !important;
            }
        }

        @media (max-width: 1540px) and (min-width: 1449px) {
            .pResp {
                padding-top: 16px !important;
                padding-bottom: 5% !important;
            }
        }

        @media(max-width: 1300px) and (min-width: 1270px) {
            .pResp {
                padding-top: 16px !important;
                padding-bottom: 12% !important;
            }
        }

        @media(max-width:1260px) {
            .pResp {
                padding-top: 3vh !important;
                padding-bottom: 2.5vw !important;
            }
        }


        @media (max-width: 767px) {
            .navbar {
                padding: 0% !important;
            }

            .container {
                padding-bottom: 3% !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
            }

            .colResp {
                justify-content: center !important;
                padding: 0% !important;
            }

            .colBtn {
                justify-content: center !important;
            }

            .colResetResp {
                padding-top: 4% !important;
                justify-content: center !important;
                text-align: center !important;
            }

            .btnLinkResp {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .marginRow {
                margin-top: 1% !important;
            }

            .grid-margin {
                margin-bottom: 40px !important;
            }

            .customer-feedback {
                margin: 5% !important;
            }

            .modal {
                text-align: center;
                padding: 0 !important;
            }

            .modal:before {
                content: '';
                display: inline-block;
                height: 100%;
                vertical-align: middle;
                margin-right: -4px;
                /* Adjusts for spacing */
            }

            .modal-dialog {
                display: inline-block;
                text-align: left;
                vertical-align: middle;
            }

            .btnResp {
                text-align: center !important;
            }

            .btnVideoR {
                padding-top: 1rem !important;
            }
        }
    </style>
    <header id="header-section">
        <nav class="navbar  pl-3 pl-sm-0" id="navbar">
            <div class="container pb-0 pt-2">
                <div class="col-md-5 colResp" style="margin-bottom: 10px;padding-left: 60px">
                    <div class="navbar-brand-wrapper d-flex colResp">
                        <img src="{{asset('landing/images/NUBE_SOLA.png')}}" width="30%" height="30%">
                    </div>
                </div>
                {{-- FORMULARIO LOGIN --}}
                <div class="col-md-7">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-2 form-group mb-0"></div>
                            <div class="col-md-4 form-group mb-0">
                                <label class="blanco">Correo electrónico o teléfono </label>
                                <input id="email"
                                    class="form-control form-control-sm @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-3 form-group mb-0">
                                <label class="blanco">Contraseña</label>
                                <input tid="password" type="password"
                                    class="form-control form-control-sm @error('password') is-invalid @enderror"
                                    name="password" required autocomplete="current-password">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-3 form-group mb-0 colBtn"
                                style="display:flex; align-items: center; top: 15px;   padding-left: 28px;">
                                <button type="submit" style="font-size: 12px;padding-bottom: 5px;padding-top: 6px;"
                                    class="botonIs">Iniciar sesión</button>
                            </div>
                            <div class="col-md-12 form-group row p-0 m-0 text-left">
                                <div class="col-md-6 offset-md-6 p-0 colResetResp">
                                    @if (Route::has('password.request'))
                                    <a class="btn btn-link btnLinkResp" href="{{ route('password.request') }}"
                                        style="font-size:11.5px;color: #ffffff;padding-bottom: 0px;padding-top: 4px;padding-left: 14px;margin-left: 0px;">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                {{-- FINALIZA FORMULARIO LOGIN --}}
            </div>
        </nav>
    </header>
    {{-- MODAL DE LANZAMIENTO --}}
    <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center"
                    style="padding-top: 8px;padding-bottom: 20px;background-color:#163552;color:#ffffff">
                    <h6 style="font-size: 14px" class="modal-title"></h6>
                </div>
                <div class="modal-body text-center">
                    <img src="{{asset('landing/images/LogoAzul.png')}}" height="90">
                    <h5 style="color: #c51516!important;margin-top: 0px!important;" class="text-danger mt-4">
                        Próximo lanzamiento en Perú
                    </h5>
                    <p class="w-75 mx-auto text-muted" style="color: black!important">
                        10 de Octubre 2020.
                    </p>
                    <div class="mt-4">
                        <button class="btn btn-opacity-primary mr-1" data-dismiss="modal">
                            <i class="uil uil-arrow-right mr-1"></i>
                            OK
                        </button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    {{-- FINAL DE MODAL --}}
    {{-- MODAL DE VIDEO --}}
    <div class="modal fade" id="modal-video" tabindex="-1" role="dialog" aria-labelledby="modal-video"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog  modal-lg d-flex justify-content-center">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#163552;padding:0.5rem">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="javascript:stopVideo()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- VIDEO --}}
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-12  p-0 img-digital grid-margin grid-margin-lg-0"
                            data-aos="fade-left">
                            <div style="padding:50.46% 0 0 0;">
                                <iframe src="https://player.vimeo.com/video/471441178?title=0&byline=0&portrait=0"
                                    width="640" height="564"
                                    style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0"
                                    allow="autoplay; fullscreen" allowfullscreen></iframe>
                                    
                            </div>
                            <script src="https://player.vimeo.com/api/player.js"></script>
                        </div>
                    </div>
                    {{-- FINALIZAR VIDEO --}}
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    {{-- FINAL DE MODAL --}}
    <div class="content-wrapper" style="padding-bottom: 0px;padding-top: 0px;background-color: #ffffff">
        <div class="container">
            <section class="digital-marketing-service" id="digital-marketing-section">
                <div class="row align-items-center marginRow" style="margin-top: 55px;">
                    <div class="col-12 col-lg-1 p-0 img-digital grid-margin"></div>
                    <div class="col-12 col-lg-5 text-center grid-margin grid-margin-lg-0" data-aos="fade-right">
                        <h3 class=" m-0">¿Por qué usar RH Nube?</h3>
                        <div class="col-lg-12 col-xl-12 text-center p-0">
                            <p class="py-4 m-0 text-muted " style="font-size: 16px">
                                Porque ahora "Administrar personal" será más sencillo y eficaz. El registro es fácil,
                                gratuito y el costo de administración
                                tiene un retorno de inversión (ROI) de sólo 2 días.
                            </p>
                            <div class="row justify-content-center">
                                <div class="col-md-8 text-right btnResp">
                                    <a href="{{route('registroPersona')}} ">
                                        <button {{-- onclick=" $('#modal-error').modal('show')" --}}
                                            class="btn btn-opacity-comienza mr-1">Crear mi cuenta
                                        </button>
                                    </a>
                                </div>
                                <div class="col-md-4 text-left btnResp btnVideoR">
                                    <a onclick="$('#modal-video').modal()" style="cursor: pointer">
                                        <img src="{{asset('landing/images/play.svg')}}" height="45">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- VIDEO --}}
                    <div class="col-12 col-lg-6 p-0 img-digital grid-margin grid-margin-lg-0" data-aos="fade-left">
                        <div style="padding:50.46% 0 0 0;">
                            <iframe width="590" height="360"
                                src="https://player.vimeo.com/video/460820175?color=ffffff&title=0&portrait=0"
                                style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0"
                                allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>
                        <script src="https://player.vimeo.com/api/player.js"></script>
                    </div>
                    {{-- FINALIZAR VIDEO --}}
                </div>
            </section>
            {{-- INICIO DE CARRUSEL --}}
            <section class="customer-feedback" id="feedback-section" style="margin-left: 50px;margin-right: 71px;">
                <div class="row" style="padding-top: 30px">
                    <div class="owl-carousel owl-theme grid-margin" style="margin-bottom: 10px;">
                        <div class="card customer-cards" style="background: #38afff">
                            <div class="card-body" style="padding-top: 0px;padding-bottom: 0px;">
                                <div class="row">
                                    <div class="col-md-2" style="padding-left: 0px"><br><br>
                                        <img src="{{asset('landing/images/grafica.svg')}}" width="59" height="49"
                                            alt="">
                                    </div>
                                    <div class="col-md-10">
                                        <h6 class="card-title pt-3" style="font-size: 13.8px!important;color:#ffffff">
                                            Controla la manada o el grupo, Modo asistencia en puerta
                                        </h6>
                                        <p class="m-0 py-3 text-muted celesteResp"
                                            style="font-size: 11.5px!important;color:#ffffff!important;padding-top: 16px">
                                            La herramienta de esta modalidad puede supervisar y controlar la asistencia
                                            y actividad de un grupo de trabajadores en un punto determinado, ya sea en
                                            oficina o en el campo.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card customer-cards" style="background:  #013c64">
                            <div class="card-body" style="padding-top: 0px;padding-bottom: 0px;">
                                <div class="row">
                                    <div class="col-md-2" style="padding-left: 0px"><br><br>
                                        <img src="{{asset('landing/images/la-seguridad.svg')}}" width="59" height="49"
                                            alt="">
                                    </div>
                                    <div class="col-md-10">
                                        <h6 class="card-title pt-3" style="font-size: 14px!important;color:#ffffff">
                                            Controla el personal de campo, Modo en ruta
                                        </h6>
                                        <p class="m-0 py-3 text-muted pResp"
                                            style="font-size: 12.4px!important;color:#ffffff!important;padding-bottom: 30px">
                                            Herramienta móvil que permite el control de las rutas de personal, así como
                                            el detalle de las actividades que se realizan durante el día laboral.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card customer-cards" style="background: #003253">
                            <div class="card-body" style="padding-top: 3px;padding-bottom: 5px;">
                                <div class="row">
                                    <div class="col-md-2" style="padding-left: 0px"><br><br>
                                        <img src="{{asset('landing/images/api.svg')}}" width="59" height="49" alt="">
                                    </div>
                                    <div class="col-md-10">
                                        <h6 class="card-title pt-2" style="font-size: 13.4px!important;color:#ffffff">
                                            Control del tiempo efectivo de trabajo, Modo Remoto
                                        </h6>
                                        <p class="m-0 py-3 text-muted"
                                            style="font-size: 11.7px!important;color:#ffffff!important">
                                            Es una herramienta fácil y práctica de manejar, controla el trabajo en
                                            oficina o en
                                            casa. Reporta tiempo efectivo de trabajo y no sólo una marcación o fichaje
                                            de asistencia.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {{-- FINALIZACION DE CARRUSEL --}}
        </div>
    </div>
    {{-- MODAL DE ERRORES --}}
    @if (session('error'))
    <div class="modal" id="modal1" role="dialog" style="display:block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center"
                    style="padding-top: 8px;padding-bottom: 5px;background-color:#163552;color:#ffffff">
                    <h6 style="font-size: 14px" class="modal-title">Advertencia</h6>
                </div>
                <div class="modal-body text-center">
                    <img src="{{asset('landing/images/alert.svg')}}" height="25" class="mr-1 mt-2">
                    <p>{{ session('error') }}</p>
                </div>
                <div class="modal-footer text-center" style="padding-top: 5px;padding-bottom: 5px;">
                    <button type="button" onclick="cerrarModalAdvertencia()" class="btn btn-sm"
                        style="background-color:#163552;color:#ffffff" data-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    {{-- FINALIZAR MODAL --}}
    <footer class="border-top" style="background:#163552">
        <div class="col-md-12 text-center" style="margin-top: 20px">
            <img src="{{asset('landing/images/NUBE_SOLA.png')}}" width="10%" height="10%">
        </div>
        <div class="col-md-12 text-center" style="margin-top: 10px;margin-bottom: 20px">
            <img src="{{asset('landing/images/peru.svg')}}" height="17">
            <span style="color:#faf3f3;font-size: 12px!important"> 017482415| &nbsp;&nbsp;
                <span class="mdi mdi-whatsapp" style="color: #ffffff;">+51 914 480 786 |&nbsp;&nbsp;</span>
                <span class="mdi mdi-email-outline" style="color: #ffffff;"></span>
                info@rhnube.com.pe
            </span>
        </div>
        <div class="col-md-12 text-center"
            style="margin-top: 10px;border-top: 1.5px solid #ded9d9;padding-top: 10px;bottom: 10px;">
            <span style="color: #faf3f3;font-size: 12px!important">
                © <?php echo date("
                    Y" ); ?> - RH nube Corp - USA | Todos los derechos
                reservados.
            </span>
        </div>
    </footer>
    {{-- SCRIPT --}}
    <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <script src="{{asset('landing/js/landingpage.js')}}"></script>
    {{-- FINALIZACION DE SCRIPT --}}
    {{-- MODAL DE CONFIRMACION --}}
    @if (session('mensaje'))
    <div class="modal" id="modal" role="dialog" style="display: block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header"
                    style="padding-top: 8px;padding-bottom: 5px;background-color:#163552;color:#ffffff">
                    <h5 style="font-size: 14px" class="modal-title">CONFIRMACION</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="cerrarModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{asset('admin/images/tick.svg')}}" height="25" class="mr-1 mt-2">
                    <p>{{ session('mensaje') }}</p>
                </div>
                <div class="modal-footer" style="padding-top: 8px;padding-bottom: 8px;">
                    <button type="button" class="btn btn-sm" style="background-color:#163552;color:#ffffff"
                        onclick="cerrarModal()">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    {{-- FINAL MODAL --}}
    <script>
        function cerrarModalAdvertencia(){
            document.getElementById("modal1").style.display = "none";
        }
    </script>
    {{-- MODAL DE CONFIRMACION --}}
    <div class="modal" id="modalInv" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header"
                    style="padding-top: 8px;padding-bottom: 5px;background-color:#163552;color:#ffffff">
                    <h5 style="font-size: 14px" class="modal-title">CONFIRMACION</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="cerrarModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{asset('admin/images/tick.svg')}}" height="25" class="mr-1 mt-2">
                    <p>Bien hecho, estas registrado! Te hemos enviado un correo de verificación.</p>
                </div>
                <div class="modal-footer" style="padding-top: 8px;padding-bottom: 8px;">
                    <button type="button" class="btn btn-sm" style="background-color:#163552;color:#ffffff"
                        onclick="cerrarModal()">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://player.vimeo.com/api/player.js"></script>
    {{-- FINAL DE MODAL --}}
    <script>
        function stopVideo(){
            var iframe = document.querySelector('iframe');
            var player = new Vimeo.Player(iframe);
            player.unload();
        }
        function cerrarModal() {
            document.getElementById("modalInv").style.display = "none";
            document.getElementById("modal").style.display = "none";
        }
    </script>
    <script>
        $( ".botonAgru" ).click(function() {
            $(".botonAgru").css({
                "background-color": "#163552",
                "color": "#ffffff"
            });
            $( this ).css({
                "background-color": "#38afff",
                "color": "#ffffff"
            });
            var valor=$( this ).val();
           if(valor=="idPerso"){
               $('#divDispo').hide();
               $('#divMoni').hide();
               $('#divPerso').show();
           }
           if(valor=="idDisp"){
               $('#divMoni').hide();
               $('#divPerso').hide();
               $('#divDispo').show();
           }
           if(valor=="idMoni"){
               $('#divDispo').hide();
               $('#divPerso').hide();
               $('#divMoni').show();
           }
        });
    </script>
</body>

</html>
