@php
use Illuminate\Support\Facades\Auth;
use App\User;

use Illuminate\Support\Facades\DB;
@endphp

<style>
    .left-side-menu {
        background: #fafafa;
    }

    #sidebar-menu>ul>li>a {
        padding: 9px 30px;
        font-size: 14.5px;
    }

</style>
@php
$usuario=DB::table('users')
->where('id','=',Auth::user()->id)->get();
$usuario_organizacion=DB::table('usuario_organizacion')
->where('user_id','=',Auth::user()->id)
->where('organi_id','=',null)
->get();
@endphp
@if ($usuario_organizacion[0]->rol_id==4)
<ul class="metismenu" id="menu-bar">
    <li>
        <a href="/superadmin" >
            <i data-feather="home"></i>
            <span class="badge badge-success float-right">1</span>
            <span> Dashboard </span>
        </a>
    </li>
    <li>
        <a href="/organizaciones" >
            <i data-feather="shopping-bag"></i>
            <span> Organizaciones </span>
        </a>
    </li>
    <!-- <li>
        <a href="/proyecto">
            <i data-feather="briefcase"></i>
            <span>Asignar tarea</span>
        </a>
    </li> -->
    <li>

        <a href="javascript: void(0);">
            <i data-feather="image"></i>
            <span>Reporte de capturas</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/reportePersonalizado">
                    <span>Reporte personalizado</span>
                </a>
                <a href="/trazabilidadCapturas">
                    <span>Trazabilidad capturas</span>
                </a>
            </li>
        </ul>
    </li>
</ul>

@endif
