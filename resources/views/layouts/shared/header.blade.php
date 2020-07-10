@php
  use App\organizacion;
  use App\usuario_organizacion;
@endphp
<!-- Topbar Start -->
<div class="navbar navbar-expand flex-column flex-md-row navbar-custom" style="padding-left: 0px;">
    <div class="container-fluid">
        <ul class="navbar-nav bd-navbar-nav flex-row list-unstyled menu-left mb-0">
            <li class="" style="width: 80px;">
                <button class="button-menu-mobile open-left ">
                    <i data-feather="menu" class="menu-icon"></i>
                    <i data-feather="x" class="close-icon"></i>
                </button>
            </li>
        </ul>
        <!-- LOGO -->
        <a href="/" class="navbar-brand mr-0 mr-md-2 logo">
            <span class="logo-lg text-center">
                <img src="{{asset('landing/images/logo.png')}}" alt="" height="60" />

            </span>
            <span class="logo-sm">
                <img src="{{asset('landing/images/logo.png')}}" alt="" height="45">
            </span>
        </a>

        @php
        $usuario=Auth::user();
        $usuario_organizacion=usuario_organizacion::where('user_id','=',$usuario->id)->first();
        $organizacion=organizacion::where('organi_id','=', $usuario_organizacion->organi_id)->first();

        @endphp

        <ul class="navbar-nav flex-row ml-auto d-flex list-unstyled topnav-menu float-right mb-0">
            <li class="dropdown notification-list" data-toggle="tooltip" data-placement="left" title="Organizacion">
            <strong style="color: mintcream">{{$organizacion->organi_razonSocial}}</strong>
            </li>


        </ul>
    </div>
</div>
<!-- end Topbar -->
