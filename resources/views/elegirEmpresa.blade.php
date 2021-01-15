@php
use App\invitado;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Elegir organizacion</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

     {{--<link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">--}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
     <link rel="shortcut icon" href="{{asset('landing/images/logo_v2_ico.svg')}}">


    <!-- App css -->
    <link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />


</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
    <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="{{asset('landing/images/notification.svg')}}" height="100">
                    <h4 class="text-danger mt-4">Su sesión expiró</h4>
                    <p class="w-75 mx-auto text-muted">Por favor inicie sesión nuevamente.</p>
                    <div class="mt-4">
                        <a href="{{('/')}}" class="btn btn-outline-primary btn-rounded width-md"><i
                                class="uil uil-arrow-right mr-1"></i> Iniciar sesión</a>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <style>
        body{
            font-family: Poppins, sans-serif;
            padding-bottom: 0px !important;
        }
        
        .card .card-body {
            padding: 20px 20px;
        }

        .body {
            background-color: #fbfbfb;
        }
        .nav-link:hover svg,
        .nav-link:focus svg,
        .topnav-menu .nav-link:active svg {
            color: #fff;
        }

        .container{
            max-width: 90% !important;
        }

        .dropdown{
            display: block !important;
        }
        .pro-user-name.ml-1{
            display: block !important;
        }

        @media (min-width: 772px){
            footer{
              font-size: 15px;
              color: #555;
              background: #eee;
              text-align: center;
              position: fixed;
              display: block;
              width: 100%;
              bottom: 0;
            }
            body{
                padding-bottom: 25px !important;
            }
        }

        @media (min-width: 767px) and (max-width: 1028px){
             .container {
                padding-bottom: 0px !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
                margin-bottom: 0px !important;;
            }
            body{
                padding: 0px !important;
            }
        }

        @media (max-width: 767px) {
            .navbar {
                padding: 0% !important;
            }

            .container {
                padding-bottom: 20px !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
            }

            .colResp {
                justify-content: center !important;
                padding: 0% !important;
            }

            .textResp {
                text-align: center !important;
            }

            .content-page {
                margin-right: 10px !important;
                margin-left: 10px !important;
                margin-top: 10px !important;
                margin-bottom: 0px !important;
            }

            .align-items-center {
                text-align: center !important;
            }
        }

        @media (min-width: 1200px) {
            .logo_rh{
                padding-left: 100px;
            }
        }
        @media (min-width: 769px) and (max-width: 1200px) {
            .logo_rh{
                padding-left: 100px;
            }
        }
        @media (min-width: 832px){
            .dropdown{
                display: block !important;
            }
            .pro-user-name.ml-1{
                left: 70px !important;
            }
        }
        
    </style>
    <header id="header-section" style="background: #163552;">
        <nav class="navbar navbar-expand-lg " id="navbar">
            <div class="container">
                <div class="col-md-4 col-xl-3 col-12 logo_rh">
                    <div class="navbar-brand-wrapper d-flex w-100 colResp">
                        <a href="{{ route('principal') }}"><img src="{{asset('landing/images/NUBE_SOLA.png')}}" class="" height="69"></a>
                    </div>
                </div>
                <div class="col-md-7 col-xl-7 col-10 text-left textResp title_rh">
                    <h5 style="color: #ffffff">Elige una de tus organizaciones para gestionar.</h5>
                </div>

                <div class="col-md-1 col-xl-2 col-2 text-right" style="padding-right: 0px;left: 0px;">
                <li class="dropdown d-none d-lg-block" data-toggle="tooltip" data-placement="left" title="">
                    <a  style="color: #fff!important" class="nav-link dropdown-toggle mr-0" data-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false">
                        <span class="pro-user-name ml-1">
                            <!-- <strong id="strongNombre" style="color:
                                aliceblue;font-size:
                                13px">Bienvenido(a), fgfg
                            </strong>
                            &nbsp;
                            <img id="imgxs2"
                                src="{{URL::asset('admin/assets//images/users/avatar-7.png')}}"
                                class="avatar-xs rounded-circle mr-2" alt="Shreyu"
                                /> -->
                            <i data-feather="chevron-down"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" style="font-size:
                        12.2px!important">
                        <!-- item-->

                        <!-- item-->
                        <a href="{{ route('logout') }}" class="dropdown-item
                            notify-item">
                            <i data-feather="log-out" class="icon-dual icon-xs
                                mr-2" style="color: #163552"></i>
                            <span>Cerrar sesión</span>
                        </a>
                    </div>
                </li>
                    {{-- <div class="btn-group mt-2 mr-1">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" style=" border-color: #163552;   background-color: #163552;">
                            <span class="pro-user-name ml-1"> <i data-feather="chevron-down"></i></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="{{ route('logout') }}" class="dropdown-item
                            notify-item">
                            <i data-feather="log-out" class="icon-dual icon-xs
                                mr-2" style="color: #163552"></i>
                            <span>Cerrar sesión</span>
                        </a>

                        </div>
                    </div> --}}

           {{--  <li class="dropdown d-lg-block" data-toggle="tooltip" data-placement="left" title="">

                <div class="dropdown-menu dropdown-menu-right" style="font-size:
                    12.2px!important">



                    <!-- item-->
                    <a href="{{ route('logout') }}" class="dropdown-item
                        notify-item">
                        <i data-feather="log-out" class="icon-dual icon-xs
                            mr-2" style="color: #163552"></i>
                        <span>Cerrar sesión</span>
                    </a>
                </div>
            </li> --}}

                </div>
            </div>  
        </nav>
    </header>

    <div class="content-page" style="margin-top: 40px; margin-left: 120px; margin-right: 55px; margin-bottom: 0px;">
        <div class="container">
            <div class="row">
                @foreach ($organizacion as $organizaciones)
                <div class="col-xl-3 col-lg-4">
                    <div class="card" style="border: 1px solid #f1f1f1;">
                        <div class="card-body">
                            <div class="badge badge-info float-right" style="color: #85919c;
                            background-color: #e5eaf0;">{{$organizaciones->rol_nombre}}</div>
                            <p class=" text-uppercase font-size-12 mb-2"
                                style="font-weight: 600;color:#4a6d8d!important">{{$organizaciones->organi_tipo}}</p>
                            <h5><a class="text-dark">{{$organizaciones->organi_razonSocial}}</a></h5>
                            <p class="text-muted" style="font-size:12px!important "><label style="font-weight: 600"
                                    for=""> RUC/ID:</label> {{$organizaciones->organi_ruc}}</p>
                            @php

                            $invitado=invitado::where('user_Invitado','=', Auth::user()->id)
                            ->where('organi_id','=', $organizaciones->organi_id)
                            ->where('estado_condic','=', 0)->get()->first();
                            @endphp
                            @if ($invitado || $organizaciones->organi_estado==0)
                            <button class="btn btn-soft-primary btn-block btn-sm" style="color: #16588d;
                            background-color: #c1cee0;" disabled><i class="uil uil-arrow-right mr-1"></i>Acceso
                                desactivado</button>
                            @else
                            <button class="btn btn-soft-primary btn-block btn-sm" style="color: #16588d;
                             background-color: #c1cee0;" onclick="ingresarOrganiza({{$organizaciones->organi_id}})"><i
                                    class="uil uil-arrow-right mr-1"></i>Ingresar a organizacion</button>
                            @endif
                        </div>
                        <div class="card-body border-top" style="padding: 10px 20px;">
                            <div class="row align-items-center">
                                <div class="col-sm-auto">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item pr-2">
                                            <a class="text-muted d-inline-block" data-toggle="tooltip"
                                                data-placement="top" title="" >
                                                @php
                                                $timestamp = strtotime( $organizaciones->created_at);
                                                $fechaSola= date('d/m/Y', $timestamp );

                                                @endphp
                                                <i class="uil uil-calender mr-1"></i>Fecha de registro: {{$fechaSola}}
                                            </a>
                                        </li>


                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end card -->
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <footer class="border-top" style="background:#163552; bottom: 0 !important; z-index: 100 !important;">
        <div class="col-md-12 text-center"
            style="margin-top: 10px;border-top: 1.5px solid #ded9d9;padding-top: 10px;bottom: 10px;">
            <span style="color: #faf3f3;font-size: 12px!important">
                © <?php echo date("Y" ); ?> - RH nube Corp - USA | Todos los derechos reservados &nbsp; |
            </span>
            <a style="font-size: 12px!important; color:#faf3f3;" href="/politicas">Política de privacidad | </a>
            <span style="color: #faf3f3;font-size: 12px!important">Central Perú: 017482415 | +51 914480786 | info@rhnube.com.pe</span>
        </div>
    </footer>
   {{--  <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script> --}}
{{--     <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script> --}}
{{--     <script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <script src="{{asset('landing/js/landingpage.js')}}"></script> --}}


    <!-- Vendor js -->
    <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>
    <script src="{{asset('admin/assets/js/app.min.js')}}"></script>
    <!-- plugin js -->

    <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>

    <script>
        function ingresarOrganiza(idorganiza){
            $.ajax({
                type: "post",
                url: "/enviarIDorg",
                data: {
                    idorganiza
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    window.location.replace(
                            location.origin + "/dashboard"
                        );
                    },
            });
        }

    </script>
    @if (Auth::user())
        <script>
            $(function() {
          setInterval(function checkSession() {
            $.get('/check-session', function(data) {

              // if session was expired
              if (data.guest==false) {
                   $('.modal').modal('hide');
                 $('#modal-error').modal('show');


              }
            });
          },7202000); // every minute
        });
        </script>
    @endif
</body>

</html>
