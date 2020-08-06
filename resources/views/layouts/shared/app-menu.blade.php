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
<ul class="metismenu" id="menu-bar">
    <li class="menu-title">Navigation</li>

    <li>
        <a href="/dashboard">
            <i data-feather="home"></i>
            <span class="badge badge-success float-right">1</span>
            <span> Dashboard </span>
        </a>
    </li>
    @php
     $usuario=DB::table('users')
            ->where('id','=',Auth::user()->id)->get();
        @endphp
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
    <li>
        <a href="/proyecto">
            <i data-feather="briefcase"></i>
            <span>Asignar tarea</span>
        </a>
    </li>

    <!--<li>
        <a href="javascript: void(0);">
            <i data-feather="inbox"></i>
            <span> Email </span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/apps/email/inbox">Inbox</a>
            </li>
            <li>
                <a href="/apps/email/read">Read</a>
            </li>
            <li>
                <a href="/apps/email/compose">Compose</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript: void(0);">
            <i data-feather="briefcase"></i>
            <span> Projects </span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/apps/project/list">List</a>
            </li>
            <li>
                <a href="/apps/project/detail">Detail</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript: void(0);">
            <i data-feather="bookmark"></i>
            <span> Tasks </span>
            <span class="menu-arrow"></span>
        </a>

        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/apps/task/list">List</a>
            </li>
            <li>
                <a href="/apps/task/board">Kanban Board</a>
            </li>
        </ul>
    </li>
    <li class="menu-title">Custom</li>
    <li>
        <a href="javascript: void(0);">
            <i data-feather="file-text"></i>
            <span> Pages </span>
            <span class="menu-arrow"></span>
        </a>

    </li>

    <li>
        <a href="javascript: void(0);">
            <i data-feather="layout"></i>
            <span> Layouts </span>
            <span class="menu-arrow"></span>
        </a>
        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/layout-example/horizontal">Horizontal Nav</a>
            </li>
            <li>
                <a href="/layout-example/rtl">RTL</a>
            </li>
            <li>
                <a href="/layout-example//dark">Dark</a>
            </li>
            <li>
                <a href="/layout-example/scrollable">Scrollable</a>
            </li>
            <li>
                <a href="/layout-example/boxed">Boxed</a>
            </li>
            <li>
                <a href="/layout-example/loader">With Pre-loader</a>
            </li>
            <li>
                <a href="/layout-example/dark-sidebar">Dark Side Nav</a>
            </li>
            <li>
                <a href="/layout-example/condensed-sidebar">Condensed Nav</a>
            </li>
        </ul>
    </li>

    <li class="menu-title">Components</li>

    <li>
        <a href="javascript: void(0);">
            <i data-feather="package"></i>
            <span> UI Elements </span>
            <span class="menu-arrow"></span>
        </a>
        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/ui/bootstrap">Bootstrap UI</a>
            </li>
            <li>
                <a href="javascript: void(0);" aria-expanded="false">Icons
                    <span class="menu-arrow"></span>
                </a>
                <ul class="nav-third-level" aria-expanded="false">
                    <li>
                        <a href="/ui/icons-feather">Feather Icons</a>
                    </li>
                    <li>
                        <a href="/ui/icons-unicons">Unicons Icons</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="/ui/widgets">Widgets</a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" aria-expanded="false">
            <i data-feather="file-text"></i>
            <span> Forms </span>
            <span class="menu-arrow"></span>
        </a>
        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/forms/basic">Basic Elements</a>
            </li>
            <li>
                <a href="/forms/advanced">Advanced</a>
            </li>
            <li>
                <a href="/forms/validation">Validation</a>
            </li>
            <li>
                <a href="/forms/wizard">Wizard</a>
            </li>
            <li>
                <a href="/forms/editor">Editor</a>
            </li>
            <li>
                <a href="/forms/fileupload">File Uploads</a>
            </li>
        </ul>
    </li>

    <li>
        <a href="/charts" aria-expanded="false">
            <i data-feather="pie-chart"></i>
            <span> Charts </span>
        </a>
    </li>

    <li>
        <a href="javascript: void(0);" aria-expanded="false">
            <i data-feather="grid"></i>
            <span> Tables </span>
            <span class="menu-arrow"></span>
        </a>
        <ul class="nav-second-level" aria-expanded="false">
            <li>
                <a href="/table/basic">Basic</a>
            </li>
            <li>
                <a href="/table/datatables">Advanced</a>
            </li>
        </ul>
    </li>-->
</ul>
