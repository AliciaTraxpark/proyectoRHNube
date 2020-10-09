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
    <!-- <li>
        <a href="/proyecto">
            <i data-feather="briefcase"></i>
            <span>Asignar tarea</span>
        </a>
    </li> -->
    <li>

        <a href="javascript: void(0);">
            <i data-feather="clipboard"></i>
            <span>Horarios</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>


                <a href="/horarios">

                    <span>Asignar horario</span>
                </a>
                <a href="/dias/laborales">

                    <span>Asignar días no laborales</span>
                </a>


            </li>
        </ul>
    </li>
</ul>

@endif
