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

            <li class="dropdown d-none d-lg-block" data-toggle="tooltip"
                data-placement="left" title="Organizacion">
                <a class="nav-link dropdown-toggle mr-0" data-toggle="dropdown"
                    href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <span style="color: aliceblue;font-size:
                        12px" ;></span>&nbsp; <strong id="strongOrganizacion"
                        style="color:
                        rgb(255, 255, 255)">{{$organizacion->organi_razonSocial}}
                        |</strong>
                </a>

            </li>
            <li class="dropdown d-none d-lg-block" data-toggle="tooltip"
                data-placement="left" title="">
                <a class="nav-link dropdown-toggle mr-0" data-toggle="dropdown"
                    href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <span class="pro-user-name ml-1">
                        <strong id="strongNombre" style="color:
                            aliceblue;font-size:
                            13px">Bienvenido(a), {{$persona->perso_nombre}}
                        </strong>
                        &nbsp;
                        <img id="imgxs2"
                            src="{{URL::asset('admin/assets//images/users/avatar-7.png')}}"
                            class="avatar-xs rounded-circle mr-2" alt="Shreyu"
                            />
                        <!-- <i data-feather="chevron-down"></i> -->
                    </span>
                </a>
                <!-- <div class="dropdown-menu dropdown-menu-right" style="font-size:
                    12.2px!important">
                    
                    <a href="/perfil" class="dropdown-item notify-item">
                        <i data-feather="edit" class="icon-dual icon-xs mr-2"></i>
                        <span>Editar perfil</span>
                    </a>
                    <a href="{{ route('logout') }}" class="dropdown-item
                        notify-item">
                        <i data-feather="log-out" class="icon-dual icon-xs
                            mr-2"></i>
                        <span>Cerrar sesion</span>
                    </a>
                </div> -->
            </li>
            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown"
                    href="#" role="button" aria-haspopup="false"
                    aria-expanded="false">
                    <i data-feather="bell"></i>
                    <span class="noti-icon-badge"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-lg">
                    <!-- item-->
                    <div class="dropdown-item noti-title border-bottom">
                        <h5 class="m-0 font-size-16">
                            <span class="float-right">
                                <a href="" class="text-dark">
                                    <small>Clear All</small>
                                </a>
                            </span>Notificaciones
                        </h5>
                    </div>

                    <div class="slimscroll noti-scroll">

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item
                            notify-item border-bottom">
                            <div class="notify-icon bg-primary"><i
                                    class="uil
                                    uil-user-plus"></i></div>
                            <p class="notify-details">New user registered.<small
                                    class="text-muted">5 hours ago</small>
                            </p>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item
                            notify-item border-bottom">
                            <div class="notify-icon">
                                <img src="assets/images/users/avatar-1.jpg"
                                    class="img-fluid rounded-circle" alt=""
                                    />
                            </div>
                            <p class="notify-details">Karen Robinson</p>
                            <p class="text-muted mb-0 user-msg">
                                <small>Wow ! this admin looks good and
                                    awesome
                                    design</small>
                            </p>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item
                            notify-item border-bottom">
                            <div class="notify-icon">
                                <img src="assets/images/users/avatar-2.jpg"
                                    class="img-fluid rounded-circle" alt=""
                                    />
                            </div>
                            <p class="notify-details">Cristina Pride</p>
                            <p class="text-muted mb-0 user-msg">
                                <small>Hi, How are you? What about our next
                                    meeting</small>
                            </p>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item
                            notify-item border-bottom active">
                            <div class="notify-icon bg-success"><i
                                    class="uil
                                    uil-comment-message"></i> </div>
                            <p class="notify-details">Jaclyn Brunswick
                                commented
                                on Dashboard<small class="text-muted">1
                                    min
                                    ago</small></p>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item
                            notify-item border-bottom">
                            <div class="notify-icon bg-danger"><i class="uil
                                    uil-comment-message"></i></div>
                            <p class="notify-details">Caleb Flakelar
                                commented
                                on Admin<small class="text-muted">4 days
                                    ago</small></p>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item
                            notify-item">
                            <div class="notify-icon bg-primary">
                                <i class="uil uil-heart"></i>
                            </div>
                            <p class="notify-details">Carlos Crouch liked
                                <b>Admin</b>
                                <small class="text-muted">13 days ago</small>
                            </p>
                        </a>
                    </div>

                    <!-- All-->
                    <a href="javascript:void(0);"
                        class="dropdown-item text-center text-primary
                        notify-item notify-all border-top">
                        View all
                        <i class="fi-arrow-right"></i>
                    </a>

                </div>
            </li>
            <li class="dropdown d-none d-lg-block" data-toggle="tooltip"
                data-placement="left" title="">
                <a class="nav-link dropdown-toggle mr-0" data-toggle="dropdown"
                    href="#" role="button"
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
                    <a href="/perfil" class="dropdown-item notify-item">
                        <i data-feather="edit" class="icon-dual icon-xs mr-2"></i>
                        <span>Editar perfil</span>
                    </a>
                    <!-- item-->
                    <a href="{{ route('logout') }}" class="dropdown-item
                        notify-item">
                        <i data-feather="log-out" class="icon-dual icon-xs
                            mr-2"></i>
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
