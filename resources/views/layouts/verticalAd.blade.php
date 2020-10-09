@php
use App\organizacion;
use App\usuario_organizacion;
use App\persona;
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />

    <title>RH nube</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="{{asset('landing/images/ICONO-LOGO-NUBE-RH.ico')}}">

    @if(isset($isDark) && $isDark)
        @include('layouts.shared.head', ['isDark' => true])
    @elseif(isset($isRTL) && $isRTL)
        @include('layouts.shared.head', ['isRTL' => true])
    @else
        @include('layouts.shared.head')
    @endif

</head>

@if(isset($isScrollable) && $isScrollable)
    <body class="scrollable-layout">
@elseif(isset($isBoxed) && $isBoxed)
    <body class="left-side-menu-condensed boxed-layout" data-left-keep-condensed="true">
@elseif(isset($isDarkSidebar) && $isDarkSidebar)
    <body class="left-side-menu-dark">
@elseif(isset($isCondensedSidebar) && $isCondensedSidebar)
    <body class="left-side-menu-condensed" data-left-keep-condensed="true">
@else
    <body>
@endif

@if(isset($withLoader) && $withLoader)
<!-- Pre-loader -->
<div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="circle1"></div>
            <div class="circle2"></div>
            <div class="circle3"></div>
        </div>
    </div>
</div>
<!-- End Preloader-->
@endif
<style>
    .navbar-custom .topnav-menu .nav-link:hover svg,
    .navbar-custom .topnav-menu .nav-link:focus svg,
    .navbar-custom .topnav-menu .nav-link:active svg {
        color: #fff;
    }
</style>
    <div id="wrapper">

        <div class="navbar navbar-expand flex-column flex-md-row navbar-custom" style="padding-left: 0px;">
            <div class="container-fluid pb-3 pt-3">
                <ul class="navbar-nav bd-navbar-nav flex-row list-unstyled menu-left
                    mb-0">
                    <li class="" style="width: 80px;">
                        <button class="button-menu-mobile open-left">
                            <i data-feather="menu" class="menu-icon"></i>
                            <i data-feather="x" class="close-icon"></i>
                        </button>
                    </li>
                </ul>
                <!-- LOGO -->
                <a href="/" class="navbar-brand mr-0 mr-md-2 logo">
                    <span class="logo-lg text-center">
                        <img src="{{asset('landing/images/NUBE_SOLA.png')}}" alt="" height="60" />

                    </span>
                    <span class="logo-sm">
                        <img src="{{asset('landing/images/NUBE_SOLA.png')}}" alt="" height="45">
                    </span>
                </a>

                @php
                $usuario=Auth::user();
                /* $usuario_organizacion=usuario_organizacion::where('user_id','=',$usuario->id)->first(); */

                $persona=persona::where('perso_id','=',$usuario->perso_id)->first();
                @endphp

                <ul class="navbar-nav flex-row ml-auto d-flex list-unstyled topnav-menu
                    float-right mb-0">

                   {{--  <li class="dropdown d-none d-lg-block" data-toggle="tooltip" data-placement="left" title="Organizacion">
                        <a class="nav-link dropdown-toggle mr-0" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <span style="color: aliceblue;font-size:
                            12px" ;></span>&nbsp; <strong id="strongOrganizacion" style="color:
                            rgb(255, 255, 255)">{{$organizacion->organi_razonSocial}}
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; |</strong> &nbsp;&nbsp;&nbsp;
                            <span class="badge badge-pill"
                                style="background-color: #617be3;color: #ffffff;font-size: 12px;font-weight: normal"><img
                                    src="{{asset('landing/images/modo.svg')}}" height="20" class="mr-1">Beta
                                &nbsp;&nbsp;&nbsp;</span>
                        </a>
                    </li> --}}
                    <li class="dropdown d-none d-lg-block" data-toggle="tooltip" data-placement="left" title="">
                        <a class="nav-link dropdown-toggle mr-0" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <span class="pro-user-name ml-1">
                                <strong id="strongNombre" style="color:
                                    aliceblue;font-size:
                                    13px">Bienvenido(a), {{$persona->perso_nombre}}
                                </strong>
                                &nbsp;
                                <img id="imgxs2" src="{{URL::asset('admin/assets//images/users/avatar-7.png')}}"
                                    class="avatar-xs rounded-circle mr-2" alt="Shreyu" />
                            </span>
                        </a>
                    </li>
                    <li class="dropdown notification-list">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                            aria-expanded="false">
                            <i data-feather="bell"></i>
                            <span class="noti-icon-badge"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-xl" style="width: 400px">
                            <!-- item-->
                            <div class="dropdown-item noti-title border-bottom" style="background-color: #163552;">
                                <div class="col-lg-12 col-sm-12 col-12 m-0">
                                    <h5 class="m-0 font-size-16" style="font-weight:
                                        bold;color: #fff;">
                                        Notificaciones <span class="badge float-right
                                            mt-0 mr-1" style="background-color:
                                            #fdfdfd;color: #28292f;" id="totalNotifNL">0</span>
                                    </h5>
                                </div>
                            </div>

                            <div class="slimscroll noti-scroll" id="notificacionesUser" style="height: 150px!important;">
                            </div>
                        </div>
                    </li>
                    <li class="dropdown d-none d-lg-block" data-toggle="tooltip" data-placement="left" title="">
                        <a class="nav-link dropdown-toggle mr-0" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <span class="pro-user-name ml-1">
                                <!-- <strong id="strongNombre" style="color:
                                    aliceblue;font-size:
                                    13px">Bienvenido(a), {{$persona->perso_nombre}}
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
                            @php
                            $usuario=DB::table('users')
                            ->where('id','=',Auth::user()->id)->get();
                            $usuario_organizacion=DB::table('usuario_organizacion')
                            ->where('user_id','=',Auth::user()->id)
                            ->where('organi_id','=',null)
                            ->get();
                            @endphp
                            <!-- item-->
                            <a href="{{ route('logout') }}" class="dropdown-item
                                notify-item">
                                <i data-feather="log-out" class="icon-dual icon-xs
                                    mr-2" style="color: #163552"></i>
                                <span>Cerrar sesión</span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">
    <div class="media user-profile mt-2 mb-2">
        <img id="imgsm" src="{{
            URL::asset('admin/assets//images/users/avatar-7.png') }}"
            class="avatar-sm rounded-circle mr-2" alt="Shreyu" />
        <img id="imgxs" src="{{
            URL::asset('admin/assets//images/users/avatar-7.png') }}"
            class="avatar-xs rounded-circle mr-2" alt="Shreyu" />

        <div class="media-body">
            @php
            $usuario=Auth::user();
            $persona=persona::where('perso_id','=',$usuario->perso_id)->first();
            @endphp
            <h6 class="pro-user-name mt-0 mb-0" id="h6Nombres">{{$persona->perso_nombre}}
                {{$persona->perso_apPaterno}}</h6>
            <span class="pro-user-desc">Administrador </span>
        </div>

    </div>
    <div class="sidebar-content">
        <!--- Sidemenu -->
        <div id="sidebar-menu" class="slimscroll-menu">
            @include('layouts.shared.app-menuAd')
        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left -->

</div>

        <div class="content-page">
            <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <img src="{{asset('landing/images/notification.svg')}}" height="100" >
                            <h4 class="text-danger mt-4">Su sesión expiró</h4>
                            <p class="w-75 mx-auto text-muted">Por favor inicie sesión nuevamente.</p>
                            <div class="mt-4">
                                <a href="{{('/')}}" class="btn btn-outline-primary btn-rounded width-md"><i class="uil uil-arrow-right mr-1"></i> Iniciar sesión</a>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <div class="content">
                <!-- Start Content-->
                <div class="container-fluid">
                    @yield('breadcrumb')
                    @yield('content')
                </div>
            </div>

            @include('layouts.shared.footer')

        </div>
    </div>

    @include('layouts.shared.rightbar')

    @include('layouts.shared.footer-script')

  {{--   @if (getenv('APP_ENV') === 'local')
    <script id="__bs_script__">//<![CDATA[
        document.write("<script async src='http://HOST:3000/browser-sync/browser-sync-client.js?v=2.26.7'><\/script>".replace("HOST", location.hostname));
    //]]></script>
    @endif --}}
    @if (Auth::user())
  <script>
    $(function() {
      setInterval(function checkSession() {
        $.get('/check-session', function(data) {
          // if session was expired
          if (data.guest==false) {
            $('.modal').modal('hide');
             $('#modal-error').modal('show');
              //alert('expiro');
            // redirect to login page
            // location.assign('/auth/login');

            // or, may be better, just reload page
            //location.reload();
          }
        });
      },7202000); // every minute
    });
  </script>
@endif
</body>

</html>
