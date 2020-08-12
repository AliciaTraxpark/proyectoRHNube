@extends('layouts.vertical')

@section('css')
<link rel="shortcut icon"
    href="https://rhsolution.com.pe/wp-content/uploads/2019/06/small-logo-rh-solution-64x64.png"
    sizes="32x32">
<link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet"
    type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.css')
    }}" rel="stylesheet" />
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}"
    rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css')
    }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}"
    rel="stylesheet" type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css')
    }}" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::asset('admin/assets/css/zoom.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/fancybox-master/jquery.fancybox.css') }}"
    rel="stylesheet"
    type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/fancybox-master/jquery.fancybox.min.css') }}"
    rel="stylesheet"
    type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')
    }}" rel="stylesheet"
    type="text/css" />
@endsection

@section('breadcrumb')
<div class="row page-title">
    <div class="col-md-12">
        <h4 class="mb-1 mt-0">Actividad de Captura de Pantalla</h4>
    </div>
</div>
@endsection

@section('content')
<style>
    .carousel-control-prev-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='403555' viewBox='0 0 8 8'%3E%3Cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3E%3C/svg%3E") !important;
    }

    .carousel-control-next-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='403555' viewBox='0 0 8 8'%3E%3Cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3E%3C/svg%3E");
    }

</style>
<div id="modalZoom" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" style="color:#ffffff;font-size:15px">Colección
                    de Imagenes</h5>
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="zoom" class="col-xl-12 text-center album">
                        <hr class="my-5" />
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="row">
    <div class="col-lg-12">
        <div>
            <div>
                <div class="row">
                    <div class="col-md-12">
                    </div>

                    <div class="col-md-6"> <label><br> </label>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Fecha:</label>
                            <div class="input-group col-md-7"
                                style="padding-left: 0px;">
                                <input type="text" id="fecha"
                                    class="form-control">
                                <div class="input-group-prepend">
                                    <div class="input-group-text form-control"><i
                                            class="uil uil-calender"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6"> <label><br> </label>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Empleado:</label>
                            <div class="col-lg-8">
                                <select id="empleado" data-plugin="customselect"
                                    class="form-control">
                                    <option value="" disabled selected>Seleccionar</option>
                                    @foreach ($empleado as $empleados)
                                    <option class="" value="{{$empleados->emple_id}}">{{$empleados->perso_nombre}}
                                        {{$empleados->perso_apPaterno}}
                                        {{$empleados->perso_apMaterno}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-2"></div>
                    <!--<div class="col-md-4">
                        <div class="form-group  col-md-10">
                            <label>Proyecto</label>
                            <select data-plugin="customselect" class="form-control" id="proyecto">
                                <option value="">Seleccionar</option>
                            </select>
                        </div>
                    </div>-->
                </div>
                <div id="espera" class="text-center" style="display: none">
                    <img src="{{asset('landing/images/loading.gif')}}"
                        height="100">
                </div>
                <div class="col-xl-12" id="card">
                    <br>
                    <img id="VacioImg" style="margin-left:28%" src="{{
                        URL::asset('admin/images/search-file.svg') }}"
                        class="mr-2" height="220" /> <br> <label for=""
                        style="margin-left:30%;color:#7d7d7d">Realize una
                        búsqueda para ver Actividad</label>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>
<!-- end row -->
@endsection
@section('script')
<!-- Plugins Js -->
<script src="{{
    URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
<script src="{{
    URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js')
    }}"></script>
<script src="{{
    URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.js')
    }}"></script>
@endsection
@section('script-bottom')
<link rel="stylesheet"
    href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{
    URL::asset('admin/assets/libs/fancybox-master/jquery.fancybox.js') }}"></script>
<script src="{{
    URL::asset('admin/assets/libs/fancybox-master/jquery.fancybox.min.js') }}"></script>
<script src="{{
    URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')
    }}"></script>
<script src="{{
    URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')
    }}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{asset('landing/js/tareas.js')}}"></script>
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
