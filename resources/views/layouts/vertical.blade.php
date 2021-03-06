@php
    use Carbon\Carbon;
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
    <link rel="shortcut icon" href="{{asset('landing/images/logo_v2_ico.svg')}}">
    @php
    $fecha=Auth::user()->created_at->toDateTimeString();
         $dt=Carbon::create($fecha);
        $dt->isoFormat('YYYY-MM-DD');
        $actual=Carbon::now();
        $actual->modify('-1 months')->isoFormat('YYYY-MM-DD');
    @endphp
    @if ($dt> $actual)
    <script src="//code.jivosite.com/widget/OqxplJ3nCh" async></script>
    @endif

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

    <div id="wrapper">

        @include('layouts.shared.header')
        @include('layouts.shared.sidebar')

        <div class="content-page">
            <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modal-errorLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <img src="{{asset('landing/images/notification.svg')}}" height="100" >
                            <h4 class="text-danger mt-4">Su sesi??n expir??</h4>
                            <p class="w-75 mx-auto text-muted">Por favor inicie sesi??n nuevamente.</p>
                            <div class="mt-4">
                                <a href="{{('/')}}" class="btn btn-outline-primary btn-rounded width-md"><i class="uil uil-arrow-right mr-1"></i> Iniciar sesi??n</a>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="modal fade" id="modal-errorAler" role="dialog"
        aria-hidden="true" data-keyboard="false">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header text-center" style="padding-top: 8px;
                            padding-bottom: 20px;background-color:
                            #163552;color:#ffffff">
                    <h6 style="font-size: 14px" class="modal-title"></h6>
                </div>
                <div class="modal-body text-center">

                    <img src="{{asset('landing/images/NUBE_SOLA.png')}}" height="70">

                    <p class="w-75 mx-auto text-muted" style="color: black!important;font-weight: 600">Disponible en Per?? a partir del 25 de octubre del 2020.</p>
                    <div class="mt-4">
                        <button class="btn  mr-1" data-dismiss="modal" style="background-color:
                        #163552;color:#ffffff"> OK</button>
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
