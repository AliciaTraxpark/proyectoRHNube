<!DOCTYPE html>
<html lang="es">

<head>
    <title>RH nube</title>
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
    <link rel="shortcut icon" href="https://i.ibb.co/r4SyJxm/CUADRADO-RH.png">
</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100" style="background: #f7f8fa;">
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
            <div class="container pb-3 pt-3" style="color: #ffffff;">
                <div class="col-md-2 col-xl-2 mr-4 p-0">
                    <div class="navbar-brand-wrapper d-flex w-100">
                        <img src="{{asset('landing/images/Recurso_23.png')}}" height="50">
                    </div>
                </div>
                <div class="col-md-4 text-left">
                    <h5 style="color: #ffffff;font-size: 15px!important">Por
                        favor verifica tu cuenta para poder
                        empezar.</h5>
                </div>
                <div class="col-md-6 text-left"></div>
            </div>
            </div>
        </nav>
    </header>
    <div class="container"> <br>
        <div class="row">
            <div class="col-md-12 text-center" style="padding-top:
                30px;padding-bottom: 30px">
                <h4 class="font-weight-semibold">Organicemos tu equipo de
                    trabajo en 10 minutos: Controla, mide y
                    gestiona.
                </h4>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center">
                            <div class="mx-auto">
                                <a href="index.html">
                                    <h3 class="d-inline align-middle ml-1
                                        text-logo">RH nube</h3>
                                </a>
                            </div>
                            <br>
                            <div class="mx-auto">
                                <img src="{{asset('landing/images/login.svg')}}" alt="" height="80" />
                            </div>
                            <h6 class="h5 mb-0 mt-5" style="text-transform:
                                uppercase;">Confirma tu email
                                {{$persona[0]->perso_nombre}}
                                {{$persona[0]->perso_apPaterno}}</h6>
                            <p class="text-muted mt-3 mb-3">Su cuenta ha sido
                                registrada exitosamente. Para
                                completar el proceso de verificación, debe
                                verficar su cuenta.
                                <a href="{{route('reenvioCorreo')}}" class="text-primary font-weight-bold ml-1">Reenviar
                                    Correo</a>
                            </p>
                        </div>
                    </div> <!-- end card-body -->
                </div>
                <!-- end card -->

                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p class="text-muted">Regresar a <a href="{{route('logout') }}"
                                class="text-primary font-weight-bold ml-1">Inicio</a></p>
                    </div> <!-- end col -->
                </div>
                <!-- end row -->

            </div> <!-- end col -->
        </div>
    </div>
    <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <script src="{{asset('landing/js/landingpage.js')}}"></script>
</body>