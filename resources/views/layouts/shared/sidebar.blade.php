@php
use App\persona;

@endphp
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
            <span class="pro-user-desc">Administrador</span>
        </div>
        <div class="dropdown align-self-center profile-dropdown-menu">
            <a class="dropdown-toggle mr-0" data-toggle="dropdown" href="#"
                role="button" aria-haspopup="false"
                aria-expanded="false">
                <span data-feather="chevron-down"></span>
            </a>
            <div class="dropdown-menu profile-dropdown">
                <a href="/perfil" class="dropdown-item
                    notify-item">
                    <i data-feather="edit" class="icon-dual icon-xs mr-2"></i>
                    <span>Editar Perfil</span>
                </a>
                <!--<a href="{{ route('logout') }}" class="dropdown-item
                    notify-item">
                    <i data-feather="log-out" class="icon-dual icon-xs mr-2"></i>
                    <span>Cerrar sesion</span>
                </a>-->
            </div>
        </div>
    </div>
    <div class="sidebar-content">
        <!--- Sidemenu -->
        <div id="sidebar-menu" class="slimscroll-menu">
            @include('layouts.shared.app-menu')
        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
@section('script')
<script src="{{asset('landing/js/editarPerfil.js')}}"></script>
@endsection