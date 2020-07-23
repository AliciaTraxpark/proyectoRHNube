@extends('layouts.vertical')
@section('css')
<link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
<link href="{{ URL::asset('admin/assets/libs/chart/Chart.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{
    URL::asset('admin/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css')
    }}" rel="stylesheet" />
<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('content')
<style>
    .form-control:disabled {
        background-color: #fcfcfc !important;
    }

    .combodate {
        display: flex;
        justify-content: space-between;
    }

    .day {
        max-width: 32%;
    }

    .month {
        max-width: 38%;
    }

    .year {
        max-width: 42%;
    }

    .file {
        visibility: hidden;
        position: absolute;
    }
</style>
<br><br>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="text-center mt-3">
                    <input style="display: none;" name="nameFoto" id="nameFoto">
                    <img src="https://placehold.it/80x80" id="preview" class="avatar-xl rounded-circle img-thumbnail">
                    <input type="file" name="img[]" class="file" accept="image/*">
                    <div class="mr-3 ml-3 mt-1">
                        <a class="browse" style="cursor: pointer" data-toggle="tooltip" data-placement="right"
                            title="Seleccionar una imagen" data-original-title="">
                            <img src="{{asset('landing/images/ui.svg')}}" height="30">
                        </a>
                        &nbsp;&nbsp;
                        <a id="guardarFoto" style="cursor: pointer" data-toggle="tooltip" data-placement="right"
                            title="Subir imagen" data-original-title="">
                            <img src="{{asset('landing/images/export.svg')}}" height="30">
                        </a>
                    </div>
                    <h5 id="h5Nombres" class="mt-2 mb-0"
                        style="text-transform: capitalize;color: #163552;font-weight: bold">
                        {{$persona->perso_nombre}}
                        {{$persona->perso_apPaterno}} {{$persona->perso_apMaterno}}</h5>
                    <h6 id="h6Empresa" class="text-muted font-weight-normal mt-2 mb-0">
                        {{$organizacion->organi_razonSocial}}
                    </h6>
                    <h6 class="text-muted font-weight-normal mt-1 mb-4" style="text-transform: capitalize;">
                        {{$organizacion->organi_ruc}}</h6>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body" id="disabledDatosP">
                <h4 class="mb-3 header-title mt-0" style="color: #163552">DATOS PERSONALES
                    &nbsp;&nbsp;&nbsp;
                    <a id="editarDatosP" data-toggle="tooltip" data-placement="right" title="Editar Datos"
                        data-original-title="" style="cursor: pointer;">
                        <img src="{{asset('admin/images/edit.svg')}}" height="15">
                    </a>
                </h4>
                <div class="row">
                    <input style="display: none;" name="id" id="id">
                    <div class="col-xl-4">
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Nombre</label>
                            <div class="col-7">
                                <input type="text" class="form-control text-center" id="nombre" required>
                            </div>
                        </div>
                        <div class="form-group row mb-3 mr-1">
                            <label for="" class="col-5 col-form-label">Fecha
                                Nacimiento</label>
                            <div class="col-7">
                                <input type="text" data-custom-class="form-control" id="fechaN" tabindex="3"
                                    data-format="YYYY-MM-DD" data-template="D MMM YYYY" name="date" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Apellido
                                paterno</label>
                            <div class="col-7">
                                <input type="text" id="apPaterno" class="form-control text-center" required>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Dirección</label>
                            <div class="col-7">
                                <input type="text" class="form-control text-center" type="text" id="direccion" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Apellido
                                Materno</label>
                            <div class="col-7">
                                <input type="text" class="form-control text-center" id="apMaterno" required>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Sexo</label>
                            <div class="col-7">
                                <select class="form-control text-center" name="genero" id="genero" required>
                                    <option class="" value="Mujer">Mujer</option>
                                    <option class="" value="Hombre">Hombre</option>
                                    <option class="" value="Personalizado">Personalizado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="form-group mb-0
                                justify-content-end row">
                            <div class="col-12 text-right">
                                <button type="button" id="actualizarDatosPersonales" class="btn btn-light btn-sm"
                                    style="background-color: #163552;color: #ffffff;">Actualizar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div> <!-- end col -->
</div>
<!-- end row -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body" id="disabledDatosE">
                <h4 class="mb-3 header-title mt-0" style="color: #163552">DATOS DE LA EMPRESA
                    &nbsp;&nbsp;&nbsp;
                    <a id="editarDatosE" data-toggle="tooltip" data-placement="right" title="Editar Datos"
                        data-original-title="" style="cursor: pointer;">
                        <img src="{{asset('admin/images/edit.svg')}}" height="15">
                    </a>
                </h4>
                <div class="row">
                    <div class="col-xl-4">
                        <input style="display: none;" name="idE" id="idE">
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">RUC</label>
                            <div class="col-7">
                                <input type="text" class="form-control text-center" id="ruc" required>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Número
                                de Empleados</label>
                            <div class="col-7">
                                <input type="number" class="form-control text-center" id="numE" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Razón Social</label>
                            <div class="col-7">
                                <input type="text" class="form-control text-center" id="razonS" required>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Página
                                Web</label>
                            <div class="col-7">
                                <input type="text" class="form-control text-center" type="text" id="pagWeb">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Dirección</label>
                            <div class="col-7">
                                <input type="text" class="form-control text-center" id="direccionE" required>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Tipo de organización</label>
                            <div class="col-7">
                                <select class="form-control" name="organizacion" id="organizacion" required>
                                    <option class="" value="Empresa">Empresa</option>
                                    <option class="" value="Gobierno">Gobierno</option>
                                    <option class="" value="ONG">ONG</option>
                                    <option class="" value="Asociación">Asociación</option>
                                    <option class="" value="Otros">Otros</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="form-group mb-0
                                justify-content-end row">
                            <div class="col-12 text-right">
                                <button type="button" class="btn btn-light btn-sm" id="actualizarDatosEmpresa"
                                    style="background-color: #163552;color: #ffffff;">Actualizar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div> <!-- end col -->
</div>
<!-- end row -->
@section('script')
<script src="{{asset('admin/assets/libs/combodate-1.0.7/combodate.js')}}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/moment.js')}}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/es.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{asset('landing/js/editarPerfil.js')}}"></script>
@endsection
@endsection