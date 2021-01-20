@php
use App\organizacion;
use App\usuario_organizacion;
use App\persona;
@endphp
<!-- Topbar Start -->
<style>
    .navbar-custom .topnav-menu .nav-link:hover svg,
    .navbar-custom .topnav-menu .nav-link:focus svg,
    .navbar-custom .topnav-menu .nav-link:active svg {
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .pro-user-name {
            display: contents !important;
        }

        .notifiResponsive{

        }
        .notifiResponsive{
            width: 250px !important;
        }
        .badgeResponsive{
            margin-left: 10% !important;
        }
    }
    .dropdown-item{
        padding: 0.15rem 1.5rem!important;
    }
 .dropdown-menu-right> a:hover{
  background: rgb(236, 236, 236)!important;

}

</style>
<div class="navbar navbar-expand flex-column flex-md-row navbar-custom" style="padding-left: 0px;">
    <div class="container-fluid pb-3 pt-3 contResponsive">
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
        $organizacion=organizacion::where('organi_id','=',
        session('sesionidorg'))->first();
        $persona=persona::where('perso_id','=',$usuario->perso_id)->first();

        $istaOrganizacion = DB::table('organizacion as o')
            ->join('usuario_organizacion as uo', 'o.organi_id', '=', 'uo.organi_id')
            ->join('rol as r', 'uo.rol_id', '=', 'r.rol_id')
            ->where('uo.user_id','=',Auth::user()->id)
            ->where('o.organi_id','!=',session('sesionidorg'))
            ->get();
        @endphp

        <ul class="navbar-nav flex-row ml-auto d-flex list-unstyled topnav-menu
            float-right mb-0">

            @if (count($istaOrganizacion) > 0)
             <li class="dropdown d-none d-lg-block" data-toggle="tooltip" data-placement="left" title="cambiar organizacion">
                <div class="btn-group mt-3">
                <button type="button" class="btn  dropdown-toggle" style="font-size: 14px!important;
                font-weight: 700;     color: white; background-color: #163552!important; border-color: #163552!important;padding-top: 9px;"
                    data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">{{$organizacion->organi_razonSocial}} <i class="icon"><span data-feather="chevron-down"></span></i></button>
                <div class="dropdown-menu dropdown-menu-right">
                    @foreach ($istaOrganizacion as $istaOrganizaciones)
                    <a class="dropdown-item" style="font-size: 12px;cursor: pointer;" onclick="ingresarOrganiza({{$istaOrganizaciones->organi_id}})">
                        {{$istaOrganizaciones->organi_razonSocial}}</a>
                    @endforeach
                </div>
            </div><!-- /btn-group -->

            </li>


            @endif


            <li class="dropdown d-none d-lg-block" data-toggle="tooltip" data-placement="left" title="">

                <a class="nav-link dropdown-toggle mr-0" data-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <span style="color: aliceblue;font-size:
                    12px" ;></span>&nbsp;  @if (count($istaOrganizacion) == 0) <strong id="strongOrganizacion" style="color:
                    rgb(255, 255, 255)">{{$organizacion->organi_razonSocial}}
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; |</strong>
                        @else  <strong style="color:
                        rgb(255, 255, 255)"> |</strong>
                         @endif

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="badge badge-pill"
                        style="background-color: #617be3;color: #ffffff;font-size: 12px;font-weight: normal"><img
                            src="{{asset('landing/images/modo.svg')}}" height="20" class="mr-1">Beta
                        &nbsp;&nbsp;&nbsp;</span>
                </a>
            </li>
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
                <div class="dropdown-menu dropdown-menu-right dropdown-xl notifiResponsive" style="width: 400px">
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
            <li class="dropdown d-lg-block" data-toggle="tooltip" data-placement="left" title="">
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
                    ->where('organi_id','=',session('sesionidorg'))
                    ->get();
                    @endphp
                    @if ($usuario_organizacion[0]->rol_id==1)
                    <a href="/perfil" class="dropdown-item notify-item">
                        <i data-feather="edit" class="icon-dual icon-xs mr-2" style="color: #163552"></i>
                        <span>Editar perfil</span>
                    </a>
                    @endif
                    <a href="/soporte" class="dropdown-item notify-item">
                        <i data-feather="settings" class="icon-dual icon-xs mr-2" style="color: #163552"></i>
                        <span>Ticket de Soporte</span>
                    </a>
                    <a href="/sugerencia" class="dropdown-item notify-item">
                        <i data-feather="mail" class="icon-dual icon-xs mr-2" style="color: #163552"></i>
                        <span>Ticket de Sugerencia</span>
                    </a>
                    @if ($usuario_organizacion[0]->rol_id==1)
                    <a href="/delegarcontrol" class="dropdown-item notify-item">
                        <i data-feather="corner-up-right" class="icon-dual icon-xs mr-2" style="color: #163552"></i>
                        <span>Delegar control</span>
                    </a>
                    @endif

                    <a href="/elegirorganizacion" class="dropdown-item
                    notify-item">
                    <i data-feather="arrow-up-left" class="icon-dual icon-xs
                        mr-2" style="color: #163552"></i>
                    <span>cambiar organización</span>
                </a>
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
{{-- MODAL --}}
<div id="modalCorreoElectronicoHeader" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="modalCorreoElectronico" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Registrar Correo
                    Electrónico
                    Empleado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:limpiarCorreoEH()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="idEmpleCorreoH">
                    <form class="form-horizontal col-lg-12" action="javascript:guardarCorreoEH()">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" style="font-weight: 500;">Correo
                                        electrónico</label>
                                    <div class="input-group input-group-merge">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="background-color: #ffffff;">
                                                <img src="{{asset('landing/images/at.svg')}}" height="13">
                                            </span>
                                        </div>
                                        <input type="email" type="text" class="form-control" id="textCorreoH"
                                            name="email" required autofocus>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal"
                    onclick="javascript:limpiarCorreoEH()">Cerrar</button>
                <button type="submit" style="background-color:#163552; border-color: #163552;"
                    class="btn btn-sm btn-primary">Registrar</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- end Topbar -->
<script>
    function ingresarOrganiza(idorganiza){
    $.ajax({
        type: "post",
        async:false,
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
            location.reload();
            },
    });

}
</script>
@section('script')
<script src="{{asset('landing/js/editarPerfil.js')}}"></script>
@endsection
