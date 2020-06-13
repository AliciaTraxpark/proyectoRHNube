@extends('layouts.vertical')

@section('css')
<link rel="shortcut icon" href="{{asset('admin/assets/images/favicon.ico')}}">
<link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.css') }}" rel="stylesheet" />
<link href="{{ URL::asset('admin/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css') }}" rel="stylesheet"
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
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label style="font-weight: 700">Búsqueda por fecha</label>
                    </div>
                    <div class="col-md-3">
                        <label><br> </label>
                        <div class="input-group col-md-10" style="padding-left: 0px;">
                            <input type="text" id="fecha" class="form-control">
                            <div class="input-group-prepend">
                                <div class="input-group-text form-control "><i class="uil uil-calender"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group col-md-10">
                            <label>Empleado</label>
                            <select id="empleado" data-plugin="customselect" class="form-control">
                                <option value="" disabled selected>Seleccionar</option>
                                @foreach ($empleado as $empleados)
                                    <option class="" value="{{$empleados->emple_id}}">{{$empleados->perso_nombre}} {{$empleados->perso_apPaterno}} {{$empleados->perso_apMaterno}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group  col-md-10">
                            <label>Proyecto</label>
                            <select data-plugin="customselect" class="form-control" id="proyecto">
                                <option value="">Seleccionar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group  col-md-10">
                            <label>Área</label>
                            <select data-plugin="customselect" class="form-control">
                                <option value="0">Shreyu</option>
                                <option value="1">Greeva</option>
                                <option value="2">Dhyanu</option>
                                <option value="3" disabled>Disabled</option>
                                <option value="4">Mannat</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12" id="card">
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>
<!-- end row -->
@endsection

@section('script')
<!-- Plugins Js -->
<script src="{{ URL::asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/multiselect/multiselect.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/flatpickr/es.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>
<script src="{{ URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.js') }}"></script>
@endsection

@section('script-bottom')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{{ URL::asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/notify.js') }}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{asset('landing/js/tareas.js')}}"></script>
@endsection

