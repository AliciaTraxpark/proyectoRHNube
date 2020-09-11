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
    <link rel="shortcut icon" href="https://i.ibb.co/b31CPDW/Recurso-13.png">
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
            background-color: #f5f3fb !important;
        }
    </style>
    <header id="header-section">
        <nav class="navbar  pl-3 pl-sm-0" id="navbar">
            <div class="container pb-0 pt-2">
                <div class="col-md-5" style="margin-bottom: 10px;padding-left: 50px">
                    <div class="navbar-brand-wrapper d-flex">
                        <img src="{{asset('landing/images/Recurso_23.png')}}" width="35%" height="35%">
                    </div>
                </div>

                <div class="col-md-7">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="row">

                            <div class="col-md-5 form-group mb-0">
                                <label class="blanco">Correo electrónico o
                                    teléfono </label>
                                <input id="email" class="form-control form-control-sm
                                        @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"
                                    required autocomplete="email" autofocus>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-0">
                                <label class="blanco">Contraseña</label>
                                <input tid="password" type="password" class="form-control form-control-sm
                                        @error('password') is-invalid @enderror" name="password" required
                                    autocomplete="current-password">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-3 form-group mb-0" style="display:
                                    flex; align-items: center; top: 15px;">
                                <button type="submit" class="boton">Iniciar
                                    sesión</button>
                            </div>
                            <div class="col-md-12 form-group row p-0 m-0 text-left">
                                <div class="col-md-6 offset-md-5 p-0">
                                    @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}"
                                        style="font-size:11.5px;color: #ffffff;padding-bottom: 0px;padding-top: 4px;padding-left: 14px;margin-left: 0px;">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    <div class="banner" style="background-color: #ffffff">
        <div class="container" style="padding-top: 60px;padding-bottom:30px">
            <h4 class="font-weight-semibold" style="color: #204051">Organicemos tu equipo de
                trabajo en 10 minutos: Controla, mide y gestiona.
            </h4>

            <div>
                <div class="col-md-12"> <br>
                    <a {{-- href="{{route('registroPersona')}} "--}}><button onclick=" $('#modal-error').modal('show')"
                        class="btn btn-opacity-primary mr-1">COMIENZA
                        AHORA</button></a>
                </div>
                <br><br>
            </div>

        </div>
    </div>
    <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header text-center" style="padding-top: 8px;
                            padding-bottom: 20px;background-color:
                            #163552;color:#ffffff">
                    <h6 style="font-size: 14px" class="modal-title"></h6>
                </div>
                <div class="modal-body text-center">
                    <img src="{{asset('landing/images/LogoAzul.png')}}" height="90">
                    <h5 style="color: #c51516!important;margin-top: 0px!important;" class="text-danger mt-4">Próximo
                        lanzamiento en Perú</h5>
                    <p class="w-75 mx-auto text-muted" style="color: black!important">Salida programada 20 de Septiembre
                        2020</p>
                    <div class="mt-4">
                        <button class="btn btn-opacity-primary mr-1" data-dismiss="modal"><i
                                class="uil uil-arrow-right mr-1"></i> OK</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div class="content-wrapper" style="padding-bottom: 0px;padding-top: 0px;background-color: #ffffff">
        <div class="container">

            <section class="digital-marketing-service" id="digital-marketing-section">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-1 p-0 img-digital grid-margin">

                    </div>
                    <div class="col-12 col-lg-5 text-center grid-margin
                            grid-margin-lg-0" data-aos="fade-right">
                        <h3 class=" m-0">¿Por qué usar RH Nube?</h3>
                        <div class="col-lg-12 col-xl-12 text-center p-0">
                            <p class="py-4 m-0 text-muted " style="font-size: 16px">Sencillamente porque ahorras mucho
                                en inversión
                                de personal y ahora puedes tomar decisiones más precisas.</p>

                        </div>
                    </div>

                    <div class="col-12 col-lg-6 p-0 img-digital grid-margin
                            grid-margin-lg-0" data-aos="fade-left">
                        <iframe width="590" height="360" src="https://www.youtube.com/embed/GfRqwR8d2wU" frameborder="0"
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                        {{-- <img src="{{asset('landing/images/Group1.png')}}" alt="" class="img-fluid"> --}}
                    </div>
                </div>

            </section>

            <br>

            <section class="customer-feedback" id="feedback-section">
                <div class="row" style="padding-top: 30px">
                    <div class="owl-carousel owl-theme grid-margin" style="margin-bottom: 10px;">
                        <div class="card customer-cards" style="background: #38afff">
                            <div class="card-body" style="padding-top: 0px;
                            padding-bottom: 0px;">
                                <div class="row">
                                    <div class="col-md-2" style="padding-left: 0px"><br><br>
                                        <img src="{{asset('landing/images/grafica.svg')}}" width="59" height="49"
                                            alt="">
                                    </div>
                                    <div class="col-md-10">
                                        <h6 class="card-title pt-3" style="font-size: 14px!important;color:#ffffff">¿Qué
                                            ventajas adicionales
                                            tienes ahora?</h6>
                                        <p class="m-0 py-3 text-muted"
                                            style="font-size: 11.5px!important;color:#ffffff!important">RH nube te
                                            permite contratar personal
                                            fuera de tu ciudad o país.
                                            Ya puedes comparar la productividad entre puestos similares
                                            y tomar decisiones.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card customer-cards" style="background:  #013c64">
                            <div class="card-body" style="padding-top: 0px;
                            padding-bottom: 0px;">
                                <div class="row">
                                    <div class="col-md-2" style="padding-left: 0px"><br><br>
                                        <img src="{{asset('landing/images/la-seguridad.svg')}}" width="59" height="49"
                                            alt="">
                                    </div>
                                    <div class="col-md-10">
                                        <h6 class="card-title pt-3" style="font-size: 14px!important;color:#ffffff">
                                            ¿Trabajadores de campo?</h6>
                                        <p class="m-0 py-3 text-muted"
                                            style="font-size: 11.5px!important;color:#ffffff!important">RH nube permite
                                            controlar la ruta de trabajo por GPS y marcar asistencia en campo.
                                            Ya puedes saber que tareas realiza tu personal fuera de oficina y tomar
                                            decisiones.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card customer-cards" style="background: #003253">
                            <div class="card-body" style="padding-top: 0px;
                            padding-bottom: 0px;">
                                <div class="row">
                                    <div class="col-md-2" style="padding-left: 0px"><br><br>
                                        <img src="{{asset('landing/images/api.svg')}}" width="59" height="49" alt="">
                                    </div>
                                    <div class="col-md-10">
                                        <h6 class="card-title pt-2" style="font-size: 13.2px!important;color:#ffffff">
                                            ¿Puedo usarlo con un software de planillas o de pagos?</h6>
                                        <p class="m-0 py-3 text-muted"
                                            style="font-size: 10.8px!important;color:#ffffff!important">Claro que
                                            puedes, disponemos de la API de integración para el sistema de planillas de
                                            tu preferencia. Nos debe contactar tu desarrollador de software.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <section class="features-overview" id="features-section" style="padding-top: 20px;">
                <div class="content-header" style="padding-top: 30px;padding-bottom: 20px;">
                    <h3>¿Cómo trabaja RH nube?</h3>
                </div>

            </section>
            <section class="features-overview" id="features-section">
                <div class="row text-center">
                    <div class="col-md-3"></div>
                    <div class="col-md-2">
                        <button class="btn botonAgru mr-1" value="idPerso"
                            style="background-color: #163552;color:#ffffff">Personal</button>
                    </div>

                    <div class="col-md-2">
                        <button class="btn botonAgru mr-1" value="idDisp"
                            style="background-color: #38afff;color:#ffffff">
                            Dispositivos</button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn botonAgru mr-1" value="idMoni"
                            style="background-color: #163552;color:#ffffff">Monitoreo</button>
                    </div>
                    <div class="col-md-3"></div>
                </div><br><br>
                <div class="col-md-12" id="divDispo">
                    <div class="row ">
                        <div class="col-md-2"></div>
                        <div class="col-md-4" style="padding-left: 30px;  margin-top: 15px;">
                            <h5 class=" m-0">Agregas dispositivos y puntos de control</h5>

                            <p style="font-size: 14px">Agrega un punto de control de personal en
                                cualquier plataforma Windows o móvil Android y a partir del 2021 estarán disponibles
                                para los equipos y relojes biométricos más usados de tu país.</p>


                        </div>
                        <div class="col-md-5 text-center">
                            <img src="{{asset('landing/images/img11.jpg')}}" width="300" height="170" alt="">
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                </div>
                <div class="col-md-12" id="divPerso" style="display: none">
                    <div class="row ">
                        <div class="col-md-2"></div>
                        <div class="col-md-4" style="padding-left: 30px;  margin-top: 15px;">
                            <h5 class=" m-0">Agregar personal de manera sencilla</h5>

                            <p style="font-size: 14px">Puedes agregar personal de forma individual o desde un archivo de
                                carga en Excel y luego enviarles una invitación.</p>


                        </div>
                        <div class="col-md-5 text-center">
                            <img src="{{asset('landing/images/personal11.png')}}" width="300" height="170" alt="">
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                </div>
                <div class="col-md-12" id="divMoni" style="display: none">
                    <div class="row ">
                        <div class="col-md-2"></div>
                        <div class="col-md-4" style="padding-left: 30px;  margin-top: 15px;">
                            <br>
                            <h5 class=" m-0">Monitorea y controla</h5>

                            <p style="font-size: 14px">Obtén información de valor como el control de asistencia,
                                porcentaje
                                de actividad y tareas que realiza tu personal.</p>


                        </div>
                        <div class="col-md-5 text-center">
                            <img src="{{asset('landing/images/moni11.jpg')}}" width="300" height="170" alt="">
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                </div><br><br>
            </section>


            <!-- Modal for Contact - us Button -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel">Contact Us</h4>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label for="Name">Name</label>
                                    <input type="text" class="form-control" id="Name" placeholder="Name">
                                </div>
                                <div class="form-group">
                                    <label for="Email">Email</label>
                                    <input type="email" class="form-control" id="Email-1" placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <label for="Message">Message</label>
                                    <textarea class="form-control" id="Message" placeholder="Enter your
                                                Message"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                            <button type="button" class="btn
                                        btn-success">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (session('error'))
    <div class="modal" id="modal1" role="dialog" style="display:
                block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center" style="padding-top: 8px;
                            padding-bottom: 5px;background-color:
                            #163552;color:#ffffff">
                    <h6 style="font-size: 14px" class="modal-title">Advertencia</h6>
                </div>
                <div class="modal-body text-center">
                    <img src="{{asset('landing/images/alert.svg')}}" height="25" class="mr-1 mt-2">
                    <p>{{ session('error') }}</p>
                </div>
                <div class="modal-footer text-center" style="padding-top: 5px;
                            padding-bottom: 5px;">
                    <button type="button" onclick="cerrarModalAdvertencia()" class="btn
                                btn-sm" style="background-color:
                                #163552;color:#ffffff" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    <footer class="border-top" style="background:#163552">
        <div class="col-md-12 text-center" style="margin-top: 20px">
            <img src="{{asset('landing/images/Recurso_23.png')}}" width="10%" height="10%">
        </div>
        <div class="col-md-12 text-center" style="margin-top: 10px;margin-bottom: 20px">
            <span class="mdi mdi-phone" style="color:#faf3f3;font-size: 12px!important"> (01) 238-8350 | <span
                    class="mdi mdi-whatsapp" style="color: #ffffff;">+51 944 721 061</span></span>
        </div>
        <div class="col-md-12 text-center" style="margin-top: 10px;border-top: 1.5px solid #ded9d9;
        padding-top: 10px;bottom: 10px;">
            <span style="color: #faf3f3;font-size: 12px!important">

                © <?php echo date("
                    Y" ); ?> - RH nube Corp - USA | Todos los derechos
                reservados.</span>
        </div>
    </footer>
    <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <script src="{{asset('landing/js/landingpage.js')}}"></script>



    @if (session('mensaje'))
    <div class="modal" id="modal" role="dialog" style="display: block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding-top: 8px;
                            padding-bottom: 5px;background-color:
                            #163552;color:#ffffff">
                    <h5 style="font-size: 14px" class="modal-title">CONFIRMACION</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="cerrarModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{asset('admin/images/tick.svg')}}" height="25" class="mr-1 mt-2">
                    <p>{{ session('mensaje') }}</p>
                </div>
                <div class="modal-footer" style="padding-top: 8px;
                            padding-bottom: 8px;">
                    <button type="button" class="btn
                    btn-sm" style="background-color:
                    #163552;color:#ffffff" onclick="cerrarModal()">OK</button>
                </div>
            </div>
        </div>
    </div>

    @endif
    <script>
        function cerrarModalAdvertencia(){
            document.getElementById("modal1").style.display = "none";
        }
    </script>
    <div class="modal" id="modalInv" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding-top: 8px;
                            padding-bottom: 5px;background-color:
                            #163552;color:#ffffff">
                    <h5 style="font-size: 14px" class="modal-title">CONFIRMACION</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="cerrarModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{asset('admin/images/tick.svg')}}" height="25" class="mr-1 mt-2">
                    <p>Bien hecho, estas registrado! Te hemos enviado un correo de verificación.</p>
                </div>
                <div class="modal-footer" style="padding-top: 8px;
                            padding-bottom: 8px;">
                    <button type="button" class="btn
                    btn-sm" style="background-color:
                    #163552;color:#ffffff" onclick="cerrarModal()">OK</button>
                </div>
            </div>
        </div>
    </div>
    <script>
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