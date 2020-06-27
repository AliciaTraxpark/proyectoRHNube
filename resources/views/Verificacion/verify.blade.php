<!DOCTYPE html>
<html lang="en">

<head>
    <title>Registrate</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.theme.default.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>
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
            <div class="container">
                <div class="col-md-2">
                    <div class="navbar-brand-wrapper d-flex w-100">
                        <img src="{{asset('landing/images/logo.png')}}" height="80">
                    </div>
                </div>
                <div class="col-md-4 text-left">
                    <h5 style="color: #ffffff;font-size: 15px!important">Por favor verifica tu cuenta para poder empezar.</h5>
                </div>
                <div class="col-md-5 text-right">
                    <label class="pro-user-name mt-0 mb-0" style="color: #ffffff;font-size: 14px!important;">
                        <img src="{{ URL::asset('admin/assets//images/users/avatar-7.png') }}"
                            class="avatar-sm rounded-circle mr-2" height="35" alt="Shreyu" />
                        {{$usuario[0]->email}}</label>
                </div>
                <div class="dropdown align-self-center profile-dropdown-menu">
                    <a class="dropdown-toggle mr-0" data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                        aria-expanded="false">
                        <span data-feather="chevron-down"></span>
                    </a>
                    <div class="dropdown-menu profile-dropdown">
                        <a href="/pages/profile" class="dropdown-item notify-item">
                            <i data-feather="user" class="icon-dual icon-xs mr-2"></i>
                            <span>My Account</span>
                        </a>

                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i data-feather="settings" class="icon-dual icon-xs mr-2"></i>
                            <span>Settings</span>
                        </a>

                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i data-feather="help-circle" class="icon-dual icon-xs mr-2"></i>
                            <span>Support</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <a href="{{ route('logout') }}" class="dropdown-item notify-item">
                            <i data-feather="log-out" class="icon-dual icon-xs mr-2"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div class="container" style="padding-top: 60px;padding-left: 400px;"> <br>
        <div class="row" style="background-color:#f5f5f5 ;padding-top: 15px;padding-bottom: 15px;width: 400px;border: 1px solid #eae3e3;">
            <div class="col-md-12 text-center" style="padding-bottom: 15px;">
                <span>Solicitar el reenvio de la verificación.
                </span>
            </div>
            <div class="col-md-12 text-center" style="padding-bottom: 15px;">
                <a href="{{route('reenvioCorreo')}}"><button
                        class="btn btn-opacity-primary mr-1">Click Aqui</button></a>
            </div>
        </div>
    </div>

</body>
