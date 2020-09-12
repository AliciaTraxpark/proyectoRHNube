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
<div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="circle1"></div>
            <div class="circle2"></div>
            <div class="circle3"></div>
        </div>
    </div>
</div>
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

    .rowAlert {
        background-color: #ffffff;
        box-shadow: 3px 3px 20px rgba(48, 48, 48, 0.5);
    }


    body>div.bootbox.modal.fade.bootbox-alert.show>div>div>div.modal-footer>button {
        background-color: #163552;
        border-color: #163552;
        zoom: 85%;
    }
</style>

<!--MODAL GENERO-->
<div id="generoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="generoModal" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:3px;
                padding-top:10px;background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Personalizar sexo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:limpiartextSexo()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ csrf_field() }}
                <div class="col-md-12">
                    <label for="">Sexo</label>
                </div>
                <div class="col-md-12">
                    <input type="text" class="form-control" id="textSexo" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal" onclick="javascript:limpiartextSexo()">Cerrar</button>
                <button type="button" class="btn btn-sm" style="background:
                    #163552;color: #ecebeb" class="btn
                    btn-sm" onclick="javascript:personalizadoGenero()" id="guardarPersonalizarSexo">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--MODAL ORGANIZACION-->
<div id="organizacionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="organizacionModal"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:3px;
                padding-top:10px;background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">
                    Personalizar organización</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:limpiartextOrganizacion()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ csrf_field() }}
                <div class="col-md-12">
                    <label for="">Organización</label>
                </div>
                <div class="col-md-12">
                    <input type="text" class="form-control" id="textOrganizacion" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal"
                    onclick="javascript:limpiartextOrganizacion()">Cerrar</button>
                <button type="button" class="btn btn-sm" style="background:
                    #163552;color: #ecebeb" class="btn
                    btn-sm" onclick="javascript:personalizadoOrganizacion()"
                    id="guardarPersonalizarOrganizacion">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- modal cambiar contraseña --}}
<div id="cambiarContras" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog  modal-lg d-flex justify-content-center " style="width: 550px;">

        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Cambiar contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:12px!important">
                <div class="row">
                    <div class="col-md-12">
                        <form id="frmCamb" action="javascript:cambioClave()">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Contraseña actual:</label>
                                        <input type="password" class="form-control form-control-sm" id="contraAnti"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Contraseña nueva:</label>
                                        <input type="password" class="form-control form-control-sm" id="contraNue"
                                            required>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                            <button type="submit" name="" style="background-color: #163552;"
                                class="btn btn-sm ">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="text-center mt-3">
                    <input style="display: none;" name="nameFoto" id="nameFoto">
                    <img src="https://placehold.it/80x80" id="preview" class="avatar-xl rounded-circle img-thumbnail">
                    <input type="file" name="img[]" class="file" accept="image/*">
                    <div class="mr-6 ml-6 mt-1">
                        &nbsp;
                        <a class="browse" style="cursor: pointer" data-toggle="tooltip" data-placement="right"
                            title="Seleccionar una imagen" data-original-title="">
                            <img src="{{asset('landing/images/photograph.svg')}}" height="20">
                        </a>
                    </div>
                    <br>
                    <div class="row" id="rowAlert">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-4">
                            <div class="alert rowAlert" role="alert">
                                <h6>¿Desea guardar foto
                                    en Perfil?</h6>
                                <button type="button" class="btn btn-light
                                    btn-sm"
                                    onclick="$('#rowAlert').hide();javascript:actualizarDatos();">Cancelar</button>
                                <button id="guardarFoto" style="background-color: #163552;" class="btn
                                    btn-sm">Aceptar</button>
                            </div>
                        </div>

                    </div>
                    <h5 id="h5Nombres" class="mt-2 mb-0" style="text-transform:
                        capitalize;color:
                        #4B4B5A;font-weight: bold">
                        {{$persona->perso_nombre}}
                        {{$persona->perso_apPaterno}} {{$persona->perso_apMaterno}}</h5>
                    <h6 id="h6Empresa" class="text-muted font-weight-normal mt-2
                        mb-0">
                        {{$organizacion->organi_razonSocial}}
                    </h6>
                    <h6 class="text-muted font-weight-normal mt-1 mb-4" style="text-transform: capitalize;">
                        {{$organizacion->organi_ruc}}</h6>
                    <button class="btn  btn-sm" style="background-color: #163552" onclick="cambiarCont()">Cambiar
                        contraseña</button>
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
                <h4 class="mb-3 header-title mt-0" style="color: #4B4B5A">DATOS
                    PERSONALES
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
                                <input type="text" class="form-control
                                    text-center" id="nombre" required>
                            </div>
                        </div>
                        <div class="form-group row mb-3 mr-1">
                            <label for="" class="col-5 col-form-label">Fecha
                                Nacimiento</label>
                            <div class="col-7">
                                <input type="text" id="fechaNacimiento" data-custom-class="form-control"
                                    data-format="YYYY-MM-DD" data-template="D MMM YYYY" name="date" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Apellido
                                paterno</label>
                            <div class="col-7">
                                <input type="text" id="apPaternoP" class="form-control text-center" required>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Dirección</label>
                            <div class="col-7">
                                <input type="text" class="form-control
                                    text-center" type="text" id="direccion" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Apellido
                                Materno</label>
                            <div class="col-7">
                                <input type="text" class="form-control
                                    text-center" id="apMaternoP" required>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Sexo</label>
                            <div class="col-5">
                                <select class="form-control text-center" name="genero" id="genero" required>
                                    <option class="" value="Mujer">Mujer</option>
                                    <option class="" value="Hombre">Hombre</option>
                                    <option class="" value="Personalizado">Personalizado</option>
                                </select>
                            </div>
                            <div class="col-2 text-right">
                                <a data-toggle="modal" id="generoPersonalizado">
                                    <img class="mt-2" style="cursor: pointer" src="{{asset('landing/images/plus.svg')}}"
                                        height="15">
                                </a>
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
                                <button type="button" class="btn btn-light
                                    btn-sm" onclick="javascript:limpiarDatosPersonales();">Cancelar
                                </button>
                                &nbsp;&nbsp;
                                <button type="button" id="actualizarDatosPersonales" class="btn
                                    btn-light btn-sm" style="background-color:
                                    #163552;color:
                                    #ffffff;">Actualizar
                                </button>
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
                <h4 class="mb-3 header-title mt-0" style="color: #4B4B5A">DATOS
                    DE LA EMPRESA
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
                                <input type="text" class="form-control
                                    text-center" id="ruc" required>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Número
                                de Empleados</label>
                            <div class="col-7">
                                <input type="number" class="form-control
                                    text-center" id="numE" disabled>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Departamento</label>
                            <div class="col-7">
                                <select class="form-control text-center" name="depE" id="depE" required>
                                    @foreach ($departamentoOrgani as
                                    $departamentos)
                                    <option class="" value="{{$departamentos->id}}">
                                        {{$departamentos->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Razón
                                Social</label>
                            <div class="col-7">
                                <input type="text" class="form-control
                                    text-center" id="razonS" required>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Página
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                Web</label>
                            <div class="col-7">
                                <input type="text" class="form-control
                                    text-center" type="text" id="pagWeb">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Provincia</label>
                            <div class="col-7">
                                <select class="form-control text-center" name="provE" id="provE" required>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Dirección</label>
                            <div class="col-7">
                                <input type="text" class="form-control
                                    text-center" id="direccionE" required>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Tipo de
                                organización</label>
                            <div class="col-5">
                                <select class="form-control" name="organizacion" id="organizacion" required>
                                    <option class="" value="Empresa">Empresa</option>
                                    <option class="" value="Gobierno">Gobierno</option>
                                    <option class="" value="ONG">ONG</option>
                                    <option class="" value="Asociación">Asociación</option>
                                    <option class="" value="Otros">Otros</option>
                                </select>
                            </div>
                            <div class="col-2 text-right">
                                <a data-toggle="modal" id="organizacionPersonalizado">
                                    <img class="mt-2" style="cursor: pointer" src="{{asset('landing/images/plus.svg')}}"
                                        height="15">
                                </a>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="" class="col-5 col-form-label">Distrito</label>
                            <div class="col-7">
                                <select class="form-control text-center" name="distE" id="distE" required>
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
                                <button type="button" class="btn btn-light
                                    btn-sm" onclick="javascript:limpiarDatosEmpresarial();">Cancelar
                                </button>
                                &nbsp;&nbsp;
                                <button type="button" class="btn btn-light
                                    btn-sm" id="actualizarDatosEmpresa" style="background-color: #163552;color:
                                    #ffffff;">Actualizar</button>
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
<script>
    $('#rowAlert').hide();
    $('#generoPersonalizado').hide();
    $('#organizacionPersonalizado').hide();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/combodate.js')}}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/moment.js')}}"></script>
<script src="{{asset('admin/assets/libs/combodate-1.0.7/es.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')}}"></script>
<script src="{{ URL::asset('admin/assets/js/prettify.js') }}"></script>
<script src="{{asset('landing/js/app-menu.js')}}"></script>
<script src="{{asset('landing/js/seleccionarDepOrg.js')}}"></script>
<script src="{{asset('landing/js/actualizarPDatos.js')}}"></script>
<script src="{{asset('landing/js/editarPerfil.js')}}"></script>
<script src="{{asset('landing/js/notificacionesUser.js')}}"></script>
@endsection
@endsection