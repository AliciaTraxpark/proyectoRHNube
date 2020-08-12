@php
use App\organizacion;
use App\usuario_organizacion;
use App\persona;
@endphp
<!-- Topbar Start -->
<style>
    .navbar-custom .topnav-menu .nav-link:hover svg, .navbar-custom .topnav-menu .nav-link:focus svg, .navbar-custom .topnav-menu .nav-link:active svg{
        color: #fff;
    }
</style>
<div class="navbar navbar-expand flex-column flex-md-row navbar-custom"
    style="padding-left: 0px;">
    <div class="container-fluid">
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
                <img src="{{asset('landing/images/logo.png')}}" alt=""
                    height="60" />

            </span>
            <span class="logo-sm">
                <img src="{{asset('landing/images/logo.png')}}" alt=""
                    height="45">
            </span>
        </a>

        @php
        $usuario=Auth::user();
        $usuario_organizacion=usuario_organizacion::where('user_id','=',$usuario->id)->first();
        $organizacion=organizacion::where('organi_id','=',
        $usuario_organizacion->organi_id)->first();
        $persona=persona::where('perso_id','=',$usuario->perso_id)->first();

        @endphp

        <ul class="navbar-nav flex-row ml-auto d-flex list-unstyled topnav-menu
            float-right mb-0">

            <li class="dropdown d-none d-lg-block" data-toggle="tooltip" data-placement="left" title="Organizacion">
                <a class="nav-link dropdown-toggle mr-0" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <span style="color: aliceblue;font-size:
                    12px" ;></span>&nbsp; <strong id="strongOrganizacion" style="color:
                    rgb(255, 255, 255)">{{$organizacion->organi_razonSocial}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  |</strong>
                </a>

            </li>
            <li class="dropdown d-none d-lg-block" data-toggle="tooltip" data-placement="left" title="">
                <a class="nav-link dropdown-toggle mr-0" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <span  class="pro-user-name ml-1">
                       <strong id="strongNombre" style="color: aliceblue;font-size:
                       13px">Bienvenido(a), {{$persona->perso_nombre}}</strong> &nbsp;<img id="imgxs2" src="{{
                        URL::asset('admin/assets//images/users/avatar-7.png') }}"
                        class="avatar-xs rounded-circle mr-2" alt="Shreyu" /> <i data-feather="chevron-down"></i>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" style="font-size: 12.2px!important">
                    <!-- item-->
                    <a href="/perfil" class="dropdown-item notify-item">
                        <i data-feather="edit" class="icon-dual icon-xs mr-2"></i>
                        <span>Editar perfil</span>
                    </a>
                    <!-- item-->
                    <a href="{{ route('logout') }}" class="dropdown-item notify-item">
                        <i data-feather="log-out" class="icon-dual icon-xs mr-2"></i>
                        <span>Cerrar sesion</span>
                    </a>
                </div>
            </li>



        </ul>
    </div>
</div>
<!-- end Topbar -->
@section('script')
<script src="{{asset('landing/js/editarPerfil.js')}}"></script>
@endsection
