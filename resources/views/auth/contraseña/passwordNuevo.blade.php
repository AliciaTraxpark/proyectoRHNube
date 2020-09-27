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

        .error {
            box-shadow: 0 0 8px red !important;
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
                    <h5 style="color: #ffffff;font-size: 15px!important">Restablecer Contraseña.</h5>
                </div>
                <div class="col-md-6 text-left"></div>
            </div>
            </div>
        </nav>
    </header>
    <div class="account-pages my-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-md-12 p-5">
                                    <div class="mx-auto mb-3 text-center">
                                        <img src="{{asset('landing/images/LogoAzul.png')}}" alt="" height="120" />
                                    </div>
                                    <div class="alert alert-danger" role="alert" style="display: none;"
                                        id="alertPaswword">
                                    </div>
                                    <div class="alert alert-success" style="display: none;" id="alertSuccess"
                                        style="display: none;" role="alert">
                                        <strong><img src="{{asset('admin/images/checked.svg')}}" height="20"
                                                class="mr-1 mt-1"></strong><span
                                            style="font-size: 14px;">Reestablecimiento de contraseña con éxito.</span>
                                    </div>
                                    <form action="javascript:enviarReset()" class="authentication-form">
                                        @csrf
                                        <input id="token" type="hidden" name="token" value="{{ $token }}">
                                        <div class="form-group">
                                            <label class="form-control-label" style="font-weight: 500;">Usuario</label>
                                            <div class="input-group input-group-merge">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" style="background-color: #ffffff;">
                                                        <img src="{{asset('landing/images/iconfinder-icon (1).svg')}}"
                                                            height="20">
                                                    </span>
                                                </div>
                                                <input id="email" type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    name="email" value="{{ $email ?? old('email') }}" required
                                                    autocomplete="email" disabled>
                                            </div>
                                        </div>

                                        <div class="form-group mt-4">
                                            <label for="password" class="form-control-label"
                                                style="font-weight: 500;">Contraseña</label>
                                            <div class="input-group input-group-merge">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" style="background-color: #ffffff;">
                                                        <img src="{{asset('landing/images/iconfinder-icon (4).svg')}}"
                                                            height="20">
                                                    </span>
                                                </div>
                                                <input id="password" type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    name="password" required autocomplete="new-password">
                                            </div>
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="form-group mt-4">
                                            <label for="password-confirm" class="form-control-label"
                                                style="font-weight: 500;">Confirmar
                                                contraseña</label>
                                            <div class="input-group input-group-merge">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" style="background-color: #ffffff;">
                                                        <img src="{{asset('landing/images/iconfinder-icon (4).svg')}}"
                                                            height="20">
                                                    </span>
                                                </div>
                                                <input id="password-confirm" type="password" class="form-control"
                                                    name="password_confirmation" required autocomplete="new-password">
                                            </div>
                                        </div>

                                        <div class="form-group mb-0 text-center" id="ocultarbtn">
                                            <button class="btn btn-primary btn-block" type="submit"> Reestablecer
                                                Contraseña
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Regresar a <a href="{{route('logout')}}"
                                    class="text-primary font-weight-bold ml-1">Inicio</a></p>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->
    <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <script src="{{asset('landing/js/landingpage.js')}}"></script>
    <script src="{{asset('landing/js/resetPassword.js')}}"></script>
</body>