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

        .tooltip-arrow,
        .red-tooltip+.tooltip>.tooltip-inner {
            background-color: rgb(0, 0, 0);
        }

        .inputResp {
            padding: 2px 15px !important;
        }

        .btn-group-sm>.btn,
        .btn-sm {
            padding: .25rem .5rem !important;
            font-size: 14px !important;
        }

        .container{
            padding: 20px 0px !important;
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

        @media (max-width: 261px){
            .img_rh{
                padding: 2px 20px !important;
            }
        }

        @media (min-width: 261px) and (max-width: 385px){
            .img_rh{
                padding: 5px 40px !important;
            }
        }

        @media (min-width: 385px) and (max-width: 580px){
            .img_rh{
                padding: 10px 110px !important;
            }
        }

        @media (min-width: 580px) and (max-width: 767px){
            .img_rh{
                padding: 20px 140px !important;
            }
        }

        @media (min-width: 767px) and (max-width: 990px) {
            .img_rh{
                padding: 0px 5px !important;
            }
            .container_img{
                padding-left: 80px !important;
                padding-right: 80px !important;
                padding-top: 0px !important;
                padding-bottom: 0px !important;
            }
        }

        @media (min-width: 990px) and (max-width: 1200px) {
            .img_rh{
                padding: 0px 20px 0px  0px !important;
            }
            .container_img{
                padding-left: 90px !important;
                padding-right: 90px !important;
                padding-top: 60px !important;
                padding-bottom: 0px !important;
            }
        }

        @media (min-width: 1200px) and (max-width: 1441px) {
            .img_rh{
                padding: 0px 20px 0px  0px !important;
            }
            .container_img{
                padding-left: 130px !important;
                padding-right: 130px !important;
                padding-top: 50px !important;
                padding-bottom: 0px !important;
            }
        }

        @media (min-width: 1441px) and (max-width: 1543px) {
            .img_rh{
                padding: 0px 20px 0px  0px !important;
            }
            .container_img{
                padding-left: 140px !important;
                padding-right: 140px !important;
                padding-top: 10px !important;
                padding-bottom: 0px !important;
            }
        }

        @media (min-width: 1543px) and (max-width: 1800px) {
            .img_rh{
                padding: 0px 60px 0px  0px !important;
            }
            .container_img{
                padding-left: 105px !important;
                padding-right: 105px !important;
                padding-top: 10px !important;
                padding-bottom: 0px !important;
            }
        }

        @media (min-width: 1800px) {
            .img_rh{
                padding: 0px 60px 0px  0px !important;
            }
            .container_img{
                padding-left: 105px !important;
                padding-right: 105px !important;
                padding-top: 10px !important;
                padding-bottom: 0px !important;
            }
        }
        
        @media (min-width: 1009px) {
            footer {
              font-size: 15px;
              color: #555;
              background: #eee;
              text-align: center;
              position: fixed;
              display: block;
              width: 100%;
              bottom: 0;
            }
        }
    </style>
    <header id="header-section">
        <nav class="navbar  pl-3 pl-sm-0" id="navbar">
            <div class="container" style="padding: 0 !important;">
                <div class="col-md-5 colResp" style="margin-bottom: 0px;padding-left: 60px">
                    <div class="navbar-brand-wrapper d-flex colResp">
                        <img src="{{asset('landing/images/NUBE_SOLA.png')}}" width="26%" height="26%">
                    </div>
                </div>
                {{-- FORMULARIO LOGIN --}}
                <div class="col-md-7">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="row" style="margin-top: 18px;">
                            <div class="col-md-0 col-lg-3"></div>
                            <div class="col-md-6 col-lg-4 form-group mb-0 pb-2" style="padding-right: 15px;">
                                <!--<label class="blanco">Correo electrónico o teléfono </label>-->
                                <input id="email"
                                    class="form-control form-control-sm @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="E-mail">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-4 col-lg-3  form-group mb-0 pb-2">
                                <!--<label class="blanco">Contraseña</label>-->
                                <input tid="password" type="password"
                                    class="form-control form-control-sm @error('password') is-invalid @enderror"
                                    name="password" required autocomplete="current-password" placeholder="Contraseña">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-2 col-lg-2 form-group mb-0 colBtn"
                                style="display:flex; align-items: center; top: -2px; padding-left: 8px;">
                                <button type="submit" style="font-size: 11px;padding-bottom: 2px;padding-top: 6px;"
                                    class="botonIs"><img src="{{asset('landing/images/log-in.png')}}" style="color: white;" width="20px"></button>
                            </div>
                            <div class="col-md-12 form-group row p-0 m-0 text-left">
                                <div class="col-md-6 col-lg-5 offset-md-6 offset-lg-7 p-0 colResetResp">
                                    @if (Route::has('password.request'))
                                    <a class="btn btn-link btnLinkResp" href="{{ route('password.request') }}"
                                        style="font-size:11.5px;color: #ffffff;padding-top: 0px;padding-bottom: 5px;padding-left: 14px;margin-left: 0px;">
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
                        17 de Diciembre 2020.
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
            <div class="modal-content" style="width: 800px">
                <div class="modal-header" style="background-color:#163552;padding:0.5rem">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="javascript:stopVideo()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- VIDEO --}}
                    <div class="row justify-content-center">
                        <div class="col-lg-12 col-lg-12  p-0 img-digital grid-margin grid-margin-lg-0"
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
    <div class="content-wrapper " style="padding-bottom: 0px;padding-top: 0px;background-color: #ffffff">
        <div class="container ">
            <section class="digital-marketing-service" id="digital-marketing-section">
                <div class="row align-items-center marginRow" style="margin-top: 55px; margin-bottom: 0px !important">
                    <div class="col-12 col-lg-1 p-0 img-digital grid-margin "></div>
                    <div class="col-12 col-lg-5 text-center grid-margin grid-margin-lg-0 " data-aos="fade-right">
                        <h3 class=" m-0" style="font-size: 23px; font-weight: 500 !important;">¿Por qué registrar tu organización en RH nube?</h3>
                        <div class="col-lg-12 col-xl-12 text-center p-0">
                            <p class="py-4 m-0 text-muted " style="font-size: 16px; color: black !important">
                                Porque ahora "Administrar personal" será más sencillo y eficaz. El registro es fácil,
                                gratuito y el costo de administración
                                tiene un retorno de inversión (ROI) de sólo 3 días.
                            </p>
                             <div class=" justify-content-center">
                                <div class="col-md-12 text-center btnResp btnVideoR p-2">
                                    <a data-toggle="tooltip" data-placement="bottom" title='ver video "crear mi cuenta"'
                                        data-original-title='ver video "crear mi cuenta"'
                                        onclick="$('#modal-video').modal()" style="cursor: pointer">
                                        <img src="{{asset('landing/images/play.svg')}}" height="45">
                                    </a>
                                </div>
                                <div class="col-md-12 text-center btnResp p-2">
                                    <a href="{{route('registroPersona')}} " >
                                        <button {{-- onclick=" $('#modal-error').modal('show')"  --}}
                                            class="btn btn-opacity-comienza mr-1">Crear mi cuenta
                                        </button>
                                    </a>
                                </div>
                                <div class="col-md-12 text-center btnResp p-2">

                                    <!-- Button trigger modal -->
                                    <a href="#" style="text-decoration: underline;  " data-toggle="modal" data-target="#modal_saveMeet">
                                      Agenda reunión
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- VIDEO --}}
                    <div class="col-12 col-lg-6 p-0" data-aos="fade-left">
                        <div style="padding:50.46% 0 0 0;">
                            <iframe width="590" height="360"
                                src="https://player.vimeo.com/video/460820175?color=ffffff&title=0&portrait=0"
                                style="position:absolute;top:0;left:0;width:100%;height:75%;" frameborder="0"
                                allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>
                        <script src="https://player.vimeo.com/api/player.js"></script>
                    </div>
                    {{-- FINALIZAR VIDEO --}}
                </div>
            </section>
            {{-- INICIO DE CARRUSEL --}}
            <!-- <section class="customer-feedback" id="feedback-section" style="margin-left: 50px;margin-right: 71px;">
                <div class="row" style="padding-top: 30px">
                    <div class="owl-carousel owl-theme grid-margin" style="margin-bottom: 10px;">
                        <div class="col-12 col-lg-12 p-0 img-digital grid-margin grid-margin-lg-0" data-aos="fade-left" >
                            <img src="{{ URL::asset('admin/images/remoto1.jpg') }}" width="220px" height="220px" style="border: black 3px solid;">
                        </div>

                        <div class="col-12 col-lg-12 p-0 img-digital grid-margin grid-margin-lg-0" data-aos="fade-left">
                            <img src="{{ URL::asset('admin/images/rutas1.jpg') }}" width="220px" height="220px" style="border: black 3px solid;">
                        </div>

                        <div class="col-12 col-lg-12 p-0 img-digital grid-margin grid-margin-lg-0" data-aos="fade-left">
                            <img src="{{ URL::asset('admin/images/puerta1.jpg') }}" width="220px" height="220px" style="border: black 3px solid;">
                        </div>
                    </div>
                </div>
            </section> -->

                <div class="container container_img">
                    <div class="row align-items-center marginRow" >
                      <div class="col-md-4 col-12 col-xs-12 p-4 img_rh">
                        <img  class="card-img-top " src="{{ URL::asset('admin/images/remoto1.jpg') }}" style="border: black 3px solid;">
                      </div>
                      <div class="col-md-4 col-12 col-xs-12 p-4 img_rh">
                        <img  class="card-img-top " src="{{ URL::asset('admin/images/rutas1.jpg') }}"  style="border: black 3px solid;">
                      </div>
                      <div class="col-md-4 col-12 col-xs-12 p-4 img_rh">
                        <img  class="card-img-top " src="{{ URL::asset('admin/images/puerta1.jpg') }}"  style="border: black 3px solid;">
                      </div>  
                    </div>
                </div>
              
 
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

    {{-- MODAL CONFIRMACIÓN DE AGENDA REUNIÓN --}}
    <div class="modal" tabindex="-1" id="confirmacion_correo">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" style="padding-bottom:3px;padding-top:10px;background: #163552;color: #f8f9fa">
            <h5 class="modal-title" style="font-size:14px">¡Correo enviado con éxito!</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Gracias por tu tiempo, hoy me pondré en contacto contigo.</p>
          </div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-sm" style="background: #163552; color: #ecebeb">Ok</button>
          </div>
        </div>
      </div>
    </div>
    {{-- FINALIZAR MODAL --}}

    {{-- MODAL AGENDA REUNIÓN --}}
    <div class="modal fade" id="modal_saveMeet" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header"  style="padding-bottom:3px;padding-top:10px;background: #163552;color: #f8f9fa">
            <h5 class="modal-title" id="" style="font-size:14px">Agendar reunión</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" style="color: white">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="form_agendarReunion" action="javascript:enviarAgenda()">
                @csrf
              <div class="form-row">
                <div class="form-group col-md-12 inputResp" style="">
                  <input type="text" class="form-control" id="modal_saveMeet_name" name="modal_saveMeet_name" required="" onkeypress='return validaTexto(event)' placeholder="Nombres y apellidos">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-12 inputResp">
                  <input type="text" class="form-control" id="modal_saveMeet_movil" name="modal_saveMeet_movil" onkeypress='return validaNumericos(event)' required="" maxlength="9" minlength="6" placeholder="Teléfono">
                </div>
                <div class="form-group col-md-12 inputResp">
                  <input type="email" class="form-control" id="modal_saveMeet_email" name="modal_saveMeet_email" required="" placeholder="Correo Electrónico">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-12 inputResp" >
                    <input type="text" class="form-control" id="modal_saveMeet_company" name="modal_saveMeet_company" required="" placeholder="¿En qué empresa trabajas?"> 
                </div>  
              </div>
              <div class="form-row">
                <div class="form-group col-md-12 inputResp">
                  <input type="text" class="form-control" id="modal_saveMeet_job" name="modal_saveMeet_job"  required="" placeholder="Cargo">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6 inputResp" >
                  <input type="text" class="form-control" id="modal_saveMeet_nWorkers" name="modal_saveMeet_nWorkers" onkeypress='return validaNumericos(event)' required="" placeholder="Cantidad de trabajadores">
                </div>
              </div>
                <div class="modal-footer">
                    
                    <button type="submit" class="btn btn-sm" style="background: #163552; color: #ecebeb">Enviar</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    {{-- FINALIZAR MODAL --}}

    <footer class="border-top" style="background:#163552; bottom: 0 !important; z-index: 100 !important;">
        <div class="col-md-12 text-center"
            style="margin-top: 10px;border-top: 1.5px solid #ded9d9;padding-top: 10px;bottom: 10px;">
            <span style="color: #faf3f3;font-size: 12px!important">
                © <?php echo date("
                    Y" ); ?> - RH nube Corp - USA | Todos los derechos
                reservados &nbsp; |
            </span>
            <a style="font-size: 12px!important; color:#faf3f3;" href="/politicas">Política de privacidad | </a>
            <span style="color: #faf3f3;font-size: 12px!important">Central Perú: 017482415 | +51 914480786 | info@rhnube.com.pe</span>
        </div>
    </footer>

    {{-- SCRIPT --}}
    <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <script src="{{asset('landing/js/landingpage.js')}}"></script>
    {{-- FINALIZACION DE SCRIPT --}}
    {{-- SCRIPT MODAL AGENDAR REUNION --}}
    <script type="text/javascript">
        function enviarAgenda(){
            var data = $('#form_agendarReunion').serialize();
             $.ajax({
                method: "POST",
                url: '/agendaReunion',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(e){
                    console.log(e);
                    $('#modal_saveMeet_name').val('');
                    $('#modal_saveMeet_lastname').val('');
                    $('#modal_saveMeet_movil').val('');
                    $('#modal_saveMeet_email').val('');
                    $('#modal_saveMeet_company').val('');
                    $('#modal_saveMeet_job').val('');
                    $('#modal_saveMeet_nWorkers').val('');
                    $('#modal_saveMeet').modal('hide');
                    $('#confirmacion_correo').modal('show');
                }
            });
        }

        function validaNumericos(event) {
            if(event.charCode >= 48 && event.charCode <= 57){
              return true;
             }
            return false;        
        }
        function validaTexto(event) {
            if(event.charCode >= 65 && event.charCode <= 90 || event.charCode >= 97 && event.charCode <= 122 || event.charCode == 32 || event.charCode == 46){
              return true;
             }
            return false;        
        }
    </script>
    {{-- FIN SCRIPT --}}

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