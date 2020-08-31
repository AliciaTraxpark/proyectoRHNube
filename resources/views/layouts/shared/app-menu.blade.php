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
        font-size: 14px;
    }

</style>
@php
     $usuario=DB::table('users')
            ->where('id','=',Auth::user()->id)->get();
      $usuario_organizacion=DB::table('usuario_organizacion')
      ->where('user_id','=',Auth::user()->id)
      ->where('organi_id','=',session('sesionidorg'))
      ->get();
        @endphp
 @if ($usuario_organizacion[0]->rol_id==1)
<ul class="metismenu" id="menu-bar">
    <li>
        <a href="/dashboard" id="menuD">
            <i data-feather="home"></i>
            <span class="badge badge-success float-right">1</span>
            <span> Dashboard </span>
        </a>
    </li>

    <li>
        @if ($usuario[0]->user_estado==0)
        <a href="/calendario"> @else <a href="/calendarios"> @endif
            <i data-feather="calendar"></i>
            <span> Calendarios </span>
        </a>

    </li>
    <li>

        @if ($usuario[0]->user_estado==0)
        <a href="/empleado">
            <i data-feather="list"></i>
            <span>Gestión de empleado</span>
        </a>

        @else
        <a href="/empleados">
            <i data-feather="list"></i>
            <span>Gestión de empleado</span>
        </a>
        @endif

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

                @if ($usuario[0]->user_estado==0)
                <a href="/horario">

                    <span>Asignar horario</span>
                </a>

                @else
                <a href="/horarios">

                    <span>Asignar horario</span>
                </a>
                <a href="/dias/laborales">

                    <span>Asignar dias no laborales</span>
                </a>
                @endif

            </li>
        </ul>
    </li>

    <li>

        <a href="javascript: void(0);">
            <i data-feather="activity"></i>
            <span>Modulo TASK: Detalle diario</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/tareas">Actividad de Captura de Pantalla</a>
            </li>
        </ul>
    </li>

    <li>
        <a href="/reporteSemanal">
            <img src="{{asset('admin/images/growth (2).svg')}}" height="25" class="mr-1" >
            <span>Horas trabajadas</span>
        </a>
    </li>
</ul>
@endif
@if ($usuario_organizacion[0]->rol_id==3)
<ul class="metismenu" id="menu-bar">
    <li>
        <a href="/dashboard" id="menuD">
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
            <i data-feather="activity"></i>
            <span>Modulo 1: Actividades</span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/tareas">Actividad de Captura de Pantalla</a>
            </li>
        </ul>
    </li>

    <li>
        <a href="/reporteSemanal">
            <img src="{{asset('admin/images/growth (2).svg')}}" height="25" class="mr-1" >
            <span>Horas trabajadas</span>
        </a>
    </li>
</ul>
@endif
