<!DOCTYPE html>
<html lang="en">

<head>
    <title>RH nube</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.theme.default.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{
            URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')
            }}" rel="stylesheet" />
    <link rel="shortcut icon" href="{{asset('landing/images/ICONO-LOGO-NUBE-RH.ico')}}">
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
            <div class="container pb-2 pt-2" style="color: #ffffff;">
                <div class="col-md-2 col-xl-2 mr-2 pl-5">
                    <div class="navbar-brand-wrapper d-flex w-100">
                        <img src="{{asset('landing/images/NUBE_SOLA.png')}}" height="69">
                    </div>
                </div>
                <div class="col-md-4 pt-2 text-left">
                    <h5 style="color: #ffffff;font-size: 15px!important">Download RH box.</h5>
                </div>
                <div class="col-md-6 text-left"></div>
            </div>
        </nav>
    </header>
    <div class="account-pages my-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-12 p-5">
                                    <div class="mx-auto mb-2 text-center">
                                        <a href="{{route('logout')}}">
                                            <img src="{{asset('landing/images/ICONO-LOGO-NUBE-RH.ico')}}" height="90" />
                                        </a>
                                    </div>
                                    <form action="javascript:enviarInstrucciones()" class="authentication-form">
                                        @csrf
                                        <div class="form-group">
                                            <label class="form-control-label" style="font-weight: 80;">Código de
                                                descarga</label>
                                            <div class="input-group mb-3">
                                                <input id="licencia" type="text" class="form-control" name="licencia"
                                                    required autofocus>
                                                <div class="input-group-prepend">
                                                    <button type="button" class="btn  btn-sm"
                                                        style="background-color: #163552;color:#ffffff;font-size: 12px;border-bottom-right-radius: 5px; border-top-right-radius: 5px;"
                                                        aria-label="Default"
                                                        aria-describedby="inputGroup-sizing-default"
                                                        id="enviarLicencia">Enviar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="col-xl-12 col-md-12">
                                        <div class="p-1 border rounded mb-2">
                                            <div class="media" id="media">
                                                <div class="avatar-sm font-weight-bold mr-3">
                                                    <span class="avatar-title rounded bg-soft-primary text-primary">
                                                        <i class="uil-file-plus-alt font-size-18"></i>
                                                    </span>
                                                </div>
                                                <div class="media-body">
                                                    <a href="#" class="d-inline-block mt-2">RH box.exe</a>
                                                </div>
                                                <div class="float-right mt-1">
                                                    <a href="#" class="p-2">
                                                        <img src="{{asset('landing/images/bandeja-de-entrada.svg')}}"
                                                            height="25" class="mr-1">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->
                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <script src="{{asset('landing/js/landingpage.js')}}"></script>
    <script src="{{asset('landing/js/descargaRHbox.js')}}"></script>
</body>