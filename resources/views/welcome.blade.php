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
    <link rel="shortcut icon" href="{{asset('landing/images/logo_v2_ico.svg')}}">
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

        @media (min-width: 992px) {
            footer {
              font-size: 15px;
              color: #555;
              background: #eee;
              text-align: center;
              position: fixed;
              display: block;
              width: 100%;
              bottom: 0;
              margin-top: 20px;
            }
            body {
                padding-bottom: 50px;
            } 
            .txt_home{
                padding-left: 65px !important;
            }
            
        }
        .modal{
                top: 5% !important;
            }

        @media (max-width: 576px) {
            /*.col-*/
            .iframe_rh{
                height: 85%;
            } 

            .iframe_rh_div{
                padding:60% 0 0 0;
            }
            .img_rh{
                padding: 5px 20px !important;
            }
        }

        @media (min-width: 576px) and (max-width: 767px) {
            /*.col-sm-*/
            .iframe_rh{
                height: 85%;
            } 

            .iframe_rh_div{
                padding:55% 0 0 0;
            }

            .img_rh{
                padding: 10px 40px !important;
            }
        }

        @media (min-width: 767px) and (max-width: 921px) {
            /*.col-sm-*/
            .iframe_rh{
                height: 85%;
            } 

            .iframe_rh_div{
                padding:50% 40% 0 0;
            }

            .video_rsp{
                 width: 500px;
            }
        }

        @media (min-width: 921px) and (max-width: 992px) {
            /*.col-sm-*/
            .iframe_rh{
                height: 85%;
            } 

            .iframe_rh_div{
                padding:50% 0 0 0;
            }

            .video_rsp{
                 width: 500px;
            }
        }

        @media (min-width: 992px) and (max-width: 1050px) {
            /*.col-xl-*/
           .iframe_rh{
                height: 85%;
                padding-right: 11% !important;
            } 

            .iframe_rh_div{
                padding:70% 0 0 0;
            }
            .container_img{
                padding-left: 35px !important;
                padding-right: 35px !important;
            }
            .video_rsp{
                 width: 800px;
            }
        }

        @media (min-width: 1050px) and (max-width: 1200px) {
            /*.col-xl-*/
           .iframe_rh{
                height: 85%;
                padding-right: 8% !important;
            } 

            .iframe_rh_div{
                padding:64% 0 0 0;
            }
            .container_img{
                padding-left: 50px !important;
                padding-right: 50px !important;
            }
            .video_rsp{
                 width: 800px;
            }
        }

        @media (min-width: 1200px) and (max-width: 1300px) {
            /*.col-*/
            .iframe_rh{
                height: 85%;

            }
            .iframe_rh_div{
                padding:58% 0 0 0;
            }
            .container_img{
                padding-left: 60px !important;
                padding-right: 60px !important;
            }
            .video_rsp{
                 width: 800px;
            }
            .credential_rh{
                margin-left: 70px !important;
            }
            .user_rh{
                max-width: 200px !important;
            }
            .pass_rh{
                max-width: 200px !important;
            }
            .span_rh{
                padding-left: 30px !important;
            }
        }

        @media (min-width: 1300px) and (max-width: 1400px) {
            /*.col-*/
            .iframe_rh{
                height: 85%;
                padding-left: 9% !important;
            }
            .iframe_rh_div{
                padding:50% 0 0 0;
            }
            .container_img{
                padding-left: 75px !important;
                padding-right: 75px !important;
            }
            .video_rsp{
                 width: 800px;
            }
            .credential_rh{
                margin-left: 115px !important;
            }
            .user_rh{
                max-width: 200px !important;
            }
            .pass_rh{
                max-width: 200px !important;
            }
            .span_rh{
                padding-left: 50px !important;
            }
        }

        @media (min-width: 1400px) and (max-width: 1500px) {
            /*.col-*/
            .iframe_rh{
                height: 87%;
                padding-left: 135px !important;
            }
            .iframe_rh_div{
                padding:40% 0 0 0;
            }
            .container_img{
                padding-left: 70px !important;
                padding-right: 70px !important;
            }
            .video_rsp{
                 width: 800px;
            }
            .credential_rh{
                margin-left: 180px !important;
            }
            .user_rh{
                max-width: 200px !important;
            }
            .pass_rh{
                max-width: 200px !important;
            }
            .span_rh{
                padding-left: 100px !important;
            }
            
        }

        @media (min-width: 1500px){
            /*.col-xl-*/
            .iframe_rh{
                height: 87%;
                padding-left: 135px !important;
            }
            .iframe_rh_div{
                padding:40% 0 0 0;
            }

            .container_img{
                padding-left: 80px !important;
                padding-right: 80px !important;
            }
            .video_rsp{
                 width: 800px;
            }
            .credential_rh{
                margin-left: 220px !important;
            }
            .user_rh{
                max-width: 200px !important;
            }
            .pass_rh{
                max-width: 200px !important;
            }
            .span_rh{
                padding-left: 110px !important;
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
            .video_rsp{
                 width: 400px;
            }
        }
        @media(min-width: 508px) and (max-width: 857px){
            .colResp{
                padding-left: 0px !important;
            }
        }
        @media(max-width: 575px){
            .colResp{
                display: flex !important;
                justify-content: center !important;
                padding: 0% !important;
            }
        }
    </style>
    <header id="header-section">
        <nav class="navbar  pl-3 pl-sm-0" id="navbar">
            <div class="container pb-0 pt-2">
                <div class="col-md-5 col-sm-4 col-12 colResp" style="margin-bottom: 0px;padding-left: 40px; height: 80px !important;">
                    <div class="navbar-brand-wrapper colResp" >
                        <img src="{{asset('landing/images/NUBE_SOLA.png')}}" height="85">
                    </div>
                </div>
                {{-- FORMULARIO LOGIN --}}
                <div class="col-md-7 col-sm-8 col-12">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="row" style="margin-top: 18px;">
                            <div class="col-md-0 col-lg-2  credential_rh"></div>
                            <div class="col-md-5 col-lg-4  col-12 form-group mb-0 pb-2 user_rh" style="padding:0px 4px;">
                                <!--<label class="blanco">Correo electrónico o teléfono </label>-->
                                <input id="email"
                                    class="form-control form-control-sm @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Correo">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-5 col-lg-4 col-12 form-group mb-0 pb-2 pass_rh" style="padding:0px 4px;" >
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
                            <div class="col-md-2 col-lg-1 col-12 form-group mb-0 colBtn start_rh" style="display:flex; align-items: center; top: -4px; padding-left: 4px; padding-right: 4px">
                                <button type="submit" style="font-size: 11px;padding-bottom: 2px;padding-top: 6px;"
                                    class="botonIs"><img src="{{asset('landing/images/log-in.png')}}" style="color: white;" width="18px"></button>
                            </div>
                            <div class="col-md-12 form-group row p-0 m-0 text-left">
                                <div class="col-md-7 col-lg-6 offset-md-5 offset-lg-6 p-0 colResetResp span_rh">
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
    <div class="modal fade" id="modal-video" tabindex="-1" role="dialog" aria-labelledby="modal-video" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg ">
        <div class="modal-content video_rsp">
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
        </div>
      </div>
    </div>


    <div class="modal fade" id="modal-video1" tabindex="-1" role="dialog" aria-labelledby="modal-video1"
        aria-hidden="true" data-backdrop="static" data-keyboard="false" >
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
    <div class="container" style="margin-top: 5%; margin-bottom: 5%;">
        <div class="row">
            <div class="col-lg-6 txt_home" data-aos="fade-right">
                <h3 class="text-center" style="font-size: 23px; font-weight: 500 !important;">¿Por qué registrar tu organización en RH nube?</h3>
                <div class="col-lg-12 col-xl-12 text-center p-0">
                    <p class="py-4 m-0 text-muted " style="font-size: 16px">
                        Porque ahora "Administrar personal" será más sencillo y eficaz. El registro es fácil,
                        gratuito y el costo de administración
                        tiene un retorno de inversión (ROI) de sólo 3 días. <br><br>
                    </p>
                    <div class="row justify-content-center">
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
                            <a href="#" style="text-decoration: underline;  " data-toggle="modal" data-target="#modal_saveMeet">
                              Agenda reunión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
              <div class="iframe_rh_div">
                    <iframe src="https://player.vimeo.com/video/460820175?color=ffffff&title=0&portrait=0" style="position:absolute;top:0;left:0;width:100%;" class="iframe_rh" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                </div>
                <script src="https://player.vimeo.com/api/player.js"></script>  
            </div>
        </div>
    </div>


    <!--<div class="content-wrapper" style="padding-bottom: 0px;padding-top: 0px;background-color: #ffffff">
        <div class="container">
            <section class="digital-marketing-service" id="digital-marketing-section">
                <div class="row align-items-center marginRow" style="margin-top: 55px;">
                    <div class="col-12 col-lg-1 p-0 img-digital grid-margin"></div>
                    <div class="col-12 col-lg-5 text-center grid-margin grid-margin-lg-0" data-aos="fade-right">
                        <h3 class=" m-0" style="font-size: 23px; font-weight: 500 !important;">¿Por qué registrar tu organización en RH nube?</h3>
                        <div class="col-lg-12 col-xl-12 text-center p-0">
                            <p class="py-4 m-0 text-muted " style="font-size: 16px">
                                Porque ahora "Administrar personal" será más sencillo y eficaz. El registro es fácil,
                                gratuito y el costo de administración
                                tiene un retorno de inversión (ROI) de sólo 3 días.
                            </p>
                            <div class="row justify-content-center">
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
                                    <a href="#" style="text-decoration: underline;  " data-toggle="modal" data-target="#modal_saveMeet">
                                      Agenda reunión
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- VIDEO --}}
                    <div class="col-12 col-lg-6 p-0 img-digital grid-margin grid-margin-lg-0" data-aos="fade-left">
                        <div class="iframe_rh_div">
                            <iframe
                                src="https://player.vimeo.com/video/460820175?color=ffffff&title=0&portrait=0"
                                style="position:absolute;top:0;left:0;width:100%;" class="iframe_rh" frameborder="0"
                                allow="autoplay; fullscreen" allowfullscreen></iframe>
                        </div>
                        <script src="https://player.vimeo.com/api/player.js"></script>
                    </div>
                    {{-- FINALIZAR VIDEO --}}
                </div>
            </section>
            {{-- INICIO DE CARRUSEL --}}
            
            {{-- FINALIZACION DE CARRUSEL --}}
        </div>
    </div>  -->
    <div class="container mb-4" style="padding: 0 80px !important;">
        <div class="row d-flex justify-content-around">
            <div class="col-md-4 img_rh" style="max-width: 400px;">
                <img  class="card-img-top " src="{{ URL::asset('admin/images/remoto1.jpg') }}" style="border: black 3px solid;">
            </div>
            <div class="col-md-4 img_rh" style="max-width: 400px;">
                <img  class="card-img-top " src="{{ URL::asset('admin/images/puerta1.jpg') }}" style="border: black 3px solid;">
            </div>
            <div class="col-md-4 img_rh" style="max-width: 400px;">
                <img  class="card-img-top " src="{{ URL::asset('admin/images/rutas1.jpg') }}" style="border: black 3px solid;">
            </div>
        </div>
    </div>
<!--
    <div class="container container_img">
        <div class="row align-items-center marginRow" >
          <div class="col-md-4 col-12 col-xs-12 p-4 img_rh">
            <img  class="card-img-top " src="{{ URL::asset('admin/images/remoto1.jpg') }}" style="border: black 3px solid;">
          </div>
          <div class="col-md-4 col-12 col-xs-12 p-4 img_rh">
            <img  class="card-img-top " src="{{ URL::asset('admin/images/puerta1.jpg') }}"  style="border: black 3px solid;">
          </div>
          <div class="col-md-4 col-12 col-xs-12 p-4 img_rh">
            <img  class="card-img-top " src="{{ URL::asset('admin/images/rutas1.jpg') }}"  style="border: black 3px solid;">
          </div>  
        </div>
    </div> -->


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
              <span aria-hidden="true" >&times;</span>
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