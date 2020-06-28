<!DOCTYPE html>
<html lang="en">

<head>
    <title>Inicio</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{
        URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')
        }}" rel="stylesheet" />
</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100" style=" background: #f7f8fa;">
    <style>
        .btn-group-sm>.btn,
        .btn-sm {
            padding: .25rem .5rem !important;
            font-size: 14px !important;
        }

        .inp {
            border: 0;
            font-weight: 550;
            background: white;
            padding-left: 8px;
            padding-right: 8px;
            text-align: center;
        }

    </style>
    <header id="header-section">
        <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
            <div class="container" style="color: #ffffff;">
                <div class="col-md-2">
                    <div class="navbar-brand-wrapper d-flex w-100">
                        <img src="{{asset('landing/images/logo.png')}}" height="80">
                    </div>
                </div>
                <div class="col-md-4 text-left">
                    <h5 style="color: #ffffff;font-size: 15px!important">Por favor verifica tu cuenta para poder
                        empezar.</h5>
                </div>
                <div class="col-md-5 text-right">
                    <label class="pro-user-name mt-0 mb-0" style="color: #ffffff;text-transform: uppercase;">
                        <img src="{{ URL::asset('admin/assets//images/users/avatar-7.png') }}"
                            class="avatar-sm rounded-circle mr-2" height="35" alt="Shreyu" />
                        {{$persona[0]->perso_nombre}} {{$persona[0]->perso_apPaterno}}</label>
                </div>
                <div class="col-md-1 text-left">
                    <div class="dropdown align-self-right profile-dropdown-menu" style="padding-left: 0%;">
                        <a style="color: #ffffff;" class="dropdown-toggle mr-0" data-toggle="dropdown" href="#"
                            role="button" aria-haspopup="false" aria-expanded="false">
                            <span data-feather="chevron-down"></span>
                        </a>
                        <div class="dropdown-menu profile-dropdown">
                            <a href="{{ route('logout') }}" class="dropdown-item notify-item">
                                <i data-feather="log-out" class="icon-dual icon-xs mr-1"></i>
                                <span>Salir</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div class="container"> <br>
        <div class="row">
            <div class="col-md-12 text-center" style="padding-top: 30px">
                <h4 class="font-weight-semibold">Organicemos tu equipo de trabajo en 10 minutos: Controla, mide y
                    gestiona.
                </h4>
            </div>
        </div>
        <br><br><br>
        <div class="row">
            <div class="col-md-4 text-center">
                <img src="{{asset('landing/images/grafico (1).gif')}}" height="250">
            </div>
            <div class="col-md-4 text-center" style="padding-top: 30px;padding-bottom: 10px;height: 150px;">
                <span style="color:#163552;font-weight: 600;">Solicitar el reenvio de la verificación. </span> <br><br>
                <a href="{{route('reenvioCorreo')}}"><button class="btn btn-opacity-primary mr-1">Click
                        Aqui</button></a>
            </div>
            <div class="col-md-4 text-center">
                <img src="{{asset('landing/images/img2.gif')}}" height="200">
            </div>
        </div>
    </div>
    <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <script src="{{asset('landing/js/landingpage.js')}}"></script>
</body>
