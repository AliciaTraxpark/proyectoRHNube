<link href="{{ URL::asset('admin/assets/css/notify.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('admin/assets/css/prettify.css') }}" rel="stylesheet" type="text/css" />
<style>
    div.dataTables_wrapper div.dataTables_filter {
        display: none;
    }

    .table {
        width: 100% !important;
    }

    .dataTables_scrollHeadInner {
        width: 100% !important;
    }

    .table th,
    .table td {
        padding: 0.4rem;
    }

    .tooltip-arrow,
    .red-tooltip+.tooltip>.tooltip-inner {
        background-color: rgb(0, 0, 0);
    }
</style>
<div id="modalCorreo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCorreo" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Enviar correo a
                    empleado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <h5 class="modal-title" id="myModalLabel" style="font-size:
                        15px">¿Desea enviar correo al empleado?</h5>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right:
                            38px;">
                            <button type="button" id="enviarCorreo" name="enviarCorreo"
                                style="background-color: #163552;" class="btn
                                btn-sm">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--NUEVO ESCRITORIO-->
<div id="modalNuevoE" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalNuevoE" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Enviar correo a
                    empleado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            <img src="{{asset('landing/images/alert.svg')}}" height="25" class="mr-1 mt-2">
                        </div>
                        <div class="col-md-8 text-center">
                            <h5 class="modal-title" id="myModalLabel" style="font-size:15px">Esta opción se usa para
                                asignar nueva PC en lo cual el empleado trabajara en más de una
                                PC.
                            </h5>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-6 text-center" style="padding-right:
                            38px;">
                            <button type="button" id="agregarEscritorio" name="agregarEscritorio"
                                style="background-color: #163552;" class="btn
                                btn-sm">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="modalCorreoM" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCorreo" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Enviar correo a
                    empleado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <h5 class="modal-title" id="myModalLabel" style="font-size:
                        15px">¿Desea enviar correo a los empleados seleccionados?</h5>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right:
                            38px;">
                            <button type="button" id="enviarCorreoM" name="enviarCorreo"
                                style="background-color: #163552;" class="btn
                                btn-sm">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Modal Ambas Plataformas-->
<div id="modalCorreoAmbos" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCorreo"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Enviar correo a
                    empleado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <h5 class="modal-title" id="myModalLabel" style="font-size:
                        15px">¿Desea enviar correo a los empleados seleccionados?</h5>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right:
                            38px;">
                            <button type="button" id="enviarAmbasP" name="enviarAmbasP"
                                style="background-color: #163552;" class="btn
                                btn-sm">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Modal Android-->
<div id="modalAndroid" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalAndroid" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Enviar correo a
                    empleado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <h5 class="modal-title" id="myModalLabel" style="font-size:
                        15px">¿Desea enviar correo al empleado?</h5>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right:
                            38px;">
                            <button type="button" id="enviarAndroid" name="enviarCorreo"
                                style="background-color: #163552;" class="btn
                                btn-sm">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="modalAndroidMasivo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalAndroidMasivo"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Enviar correo a
                    empleado
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <h5 class="modal-title" id="myModalLabel" style="font-size:
                        15px">¿Desea enviar correo a los empleados seleccionados?</h5>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right:
                            38px;">
                            <button type="button" id="enviarAndroidMasivo" name="enviarAndroidMasivo"
                                style="background-color: #163552;" class="btn
                                btn-sm">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--WINDOWS-->
<div id="detallesWindows" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="detallesWindows"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color:#fafafa">
                <div class="row">
                    <input style="display: none;" name="idDetalle" id="idDetalle">
                    <div class="col">
                        <div class="card">
                            <div class="card-body p-0">
                                <h6 class="card-title border-bottom p-3 mb-0 header-title" style="color: #163552;">
                                    Detalle de Plataforma Windows
                                </h6>
                                <div class="row py-1">
                                    <div class="col-xl-6 col-sm-12 text-center">
                                        <!-- stat 1 -->
                                        <div class="media p-4">
                                            <div class="media-body">
                                                <img src="{{ URL::asset('admin/assets/images/users/empleado.png')}}"
                                                    class="mr-2" alt="" height="20" />
                                                <span class="text-muted" id="colaborador"
                                                    style="text-transform:uppercase;"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-4">
                                            <div class="media-body">
                                                <img src="{{asset('landing/images/laptop.svg')}}" height="20"
                                                    class="mr-2" alt="" />
                                                <span class="text-muted" id="totalPC"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- details-->
                <div class="row" id="rowDetalles">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mt-0 header-title" id="pcPrincipal" style="color: #163552;">DETALLES DE PC
                                </h6>

                                <div class="row">
                                    <div class="col-lg-8 col-md-6">
                                        <div class="mt-3" id="detalleLicencia">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="mt-3" id="estadoLicencia">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                </div>
                <!-- end row -->
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal"
                                style="background-color: #163552;color: #ffffff;">Cerrar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right:
                            38px;">
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--ANDROID-->
<div id="detallesAndroid" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="detallesAndroid"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color:#fafafa">
                <div class="row">
                    <input style="display: none;" name="idDetalleA" id="idDetalleA">
                    <div class="col">
                        <div class="card">
                            <div class="card-body p-0">
                                <h6 class="card-title border-bottom p-3 mb-0 header-title">Detalle de Plataforma Android
                                </h6>
                                <div class="row py-1">
                                    <div class="col-xl-6 col-sm-12 text-center">
                                        <!-- stat 1 -->
                                        <div class="media p-4">
                                            <div class="media-body">
                                                <img src="{{ URL::asset('admin/assets/images/users/empleado.png')}}"
                                                    class="mr-2" alt="" height="20" />
                                                <span class="text-muted" id="colaboradorA"
                                                    style="text-transform:uppercase;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- details-->
                <div class="row" id="notifEnlace">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mt-0 header-title">DETALLES DE LA APLICACIÓN</h6>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6 text-center">
                                        <div class="mt-4">
                                            <img src="{{asset('landing/images/playstore.svg')}}" height="20"
                                                class="mr-2" alt="" />
                                            <p class="mb-2">Play Store</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 text-center">
                                        <div class="mt-4">
                                            <a href="javascript:getlink();" data-toggle="tooltip" data-placement="right"
                                                title="copiar enlace" data-original-title="">
                                                <img src="{{asset('landing/images/document.svg')}}" height="30">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                </div>
                <!-- end row -->
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal"
                                style="background-color: #163552;color: #ffffff;">Cerrar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right:
                            38px;">
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!------------CAMBIAR ESTADO LICENCIA-->
<div id="estadoLicenciaC" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="estadoLicenciaC"
    aria-hidden="true" data-backdrop="static">
    <br><br><br><br>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Cambiar Estado de
                    Activacion de Eliminada
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            <img src="{{asset('landing/images/alert.svg')}}" height="25" class="mr-1 mt-2">
                        </div>
                        <div class="col-md-8 text-center">
                            <h5 class="modal-title" id="myModalLabel" style="font-size:15px">
                                Al cambiar el estado de la licencia toda información del empleado en su PC será borrada.
                            </h5>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancelar</button>
                        </div>
                        <div class="col-md-6 text-center" style="padding-right:
                            38px;">
                            <button type="button" id="CambiarEstadoL" name="CambiarEstadoL"
                                style="background-color: #163552;" class="btn
                                btn-sm">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--VER EMPLEADO-->
<div id="verEmpleado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="verEmpleado" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="accordion custom-accordionwitharrow" id="accordionExample">
                <div class="modal-body" style="background-color:#fafafa">
                    <div class="col-xl-12 col-sm-12 text-right">
                        <a onclick="$('#form-ver').modal();$('#verEmpleado').modal('toggle');"
                            style="cursor: pointer;padding-bottom: 10px;">
                            <img src="{{asset('admin/images/edit.svg')}}" height="15"> Editar Empleado
                        </a>
                    </div>
                    <br>
                    <div class="card mb-1 shadow-none border">
                        <a href="" class="text-dark" data-toggle="collapse" data-target="#collapseOne"
                            aria-expanded="true" aria-controls="collapseOne">
                            <div class="card-header" id="headingOne">
                                <h5 class="m-0 font-size-16">Datos Personales<i
                                        class="uil uil-angle-down float-right accordion-arrow"></i>
                                </h5>
                            </div>
                        </a>
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                            data-parent="#accordionExample">
                            <div class="card-body text-muted">
                                <div class="row py-1">
                                    <div class="col-xl-4 col-sm-12 text-center">
                                        <div class="media p-3">
                                            <div class="media-body">
                                                <input style="display: none;" name="v_idV" id="v_idV">
                                                <div class="form-group">
                                                    <label for="sw-default">Tipo Documento</label>
                                                    <input type="text" class="form-control text-center"
                                                        name="v_tipoDocV" id="v_tipoDocV"
                                                        style="background-color: #fcfcfc;border: none" tabindex="1"
                                                        disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Apellido Paterno</label>
                                                    <input type="text" class="form-control text-center"
                                                        name="v_apPaternoV" id="v_apPaternoV" tabindex="4"
                                                        style="border: none" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Correo Electronico</label>
                                                    <input type="email" class="form-control text-center" id="v_emailV"
                                                        name="v_emailV" tabindex="7" style="border: none" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-3">
                                            <div class="media-body">
                                                <div class="form-group">
                                                    <label for="sw-default">Num. Documento</label>
                                                    <input type="text" class="form-control text-center"
                                                        name="v_numDocumentoV" id="v_numDocumentoV"
                                                        style="background-color: #fcfcfc;border: none" tabindex="2"
                                                        disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Apellido Materno</label>
                                                    <input type="text" class="form-control text-center"
                                                        name="v_apMaternoV" id="v_apMaternoV" tabindex="5"
                                                        style="border: none" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Celular</label>
                                                    <input type="text" class="form-control text-center"
                                                        name="v_celularV" id="v_celularV" tabindex="8" maxlength="9"
                                                        onkeypress="return isNumeric(event)"
                                                        oninput="maxLengthCheck(this)" style="border: none" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-3">
                                            <div class="media-body">
                                                <div class="form-group">
                                                    <label for="sw-default">Fecha Nacimiento</label>
                                                    <input type="text" class="form-control text-center" id="v_fechaNV"
                                                        name="date" tabindex="3" style="border: none" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Nombres</label>
                                                    <input type="text" class="form-control text-center"
                                                        name="v_nombresV" id="v_nombresV" tabindex="6"
                                                        style="border: none" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Telefono</label>
                                                    <input type="text" class="form-control text-center"
                                                        name="v_telefonoV" id="v_telefonoV" tabindex="9" maxlength="9"
                                                        onkeypress="return isNumeric(event)"
                                                        oninput="maxLengthCheck(this)" style="border: none" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-3">
                                            <div class="media-body">
                                                <div class="form-group">
                                                    <label for="sw-default">Dirección</label>
                                                    <input type="text" class="form-control text-center"
                                                        name="v_direccionV" id="v_direccionV" tabindex="10"
                                                        style="border: none" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-3">
                                            <div class="media-body">
                                                <div class="form-group">
                                                    <label for="sw-default">Dirección
                                                        Domiciliara</label>
                                                    <input class="form-control text-center" placeholder="Departamento"
                                                        name="v_depV" id="v_depV" tabindex="11" style="border: none"
                                                        disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Lugar Nacimiento</label>
                                                    <input class="form-control text-center" placeholder="Departamento"
                                                        name="v_departamentoV" id="v_departamentoV" tabindex="14"
                                                        style="border: none" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label class="normal" for="">Genero</label>
                                                    <label class="custom-control custom-radio text-center">
                                                        <input type="radio" name="v_tipoV" id="v_tipoV" value="Femenino"
                                                            disabled>
                                                        Femenino
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-3">
                                            <div class="media-body">
                                                <div class="form-group">
                                                    <label for="sw-default"><br></label>
                                                    <input class="form-control text-center" placeholder="Provincia "
                                                        name="v_provV" id="v_provV" tabindex="12" style="border: none"
                                                        disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default"><br></label>
                                                    <input class="form-control text-center" placeholder="Provincia "
                                                        name="v_provinciaV" id="v_provinciaV" tabindex="15"
                                                        style="border: none" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label class="normal" for=""><br></label>
                                                    <label class="custom-control custom-radio text-center">
                                                        <input type="radio" name="v_tipoV" id="v_tipoV"
                                                            value="Masculino" style="border: none" disabled>
                                                        Masculino
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-3">
                                            <div class="media-body">
                                                <div class="form-group">
                                                    <label for="sw-default"><br></label>
                                                    <input class="form-control text-center" placeholder="Distrito "
                                                        name="v_distV" id="v_distV" tabindex="13" style="border: none"
                                                        disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default"><br></label>
                                                    <input class="form-control text-center" placeholder="Distrito "
                                                        name="v_distritoV" id="v_distritoV" tabindex="16"
                                                        style="border: none" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label class="normal" for=""><br></label>
                                                    <label class="custom-control custom-radio text-center">
                                                        <input type="radio" name="v_tipoV" id="v_tipoV"
                                                            value="Personalizado" disabled>
                                                        Personalizado
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-1 shadow-none border">
                        <a href="" class="text-dark collapsed" data-toggle="collapse" data-target="#collapseTwo"
                            aria-expanded="false" aria-controls="collapseTwo">
                            <div class="card-header" id="headingTwo">
                                <h5 class="m-0 font-size-16">
                                    Datos Empresarial<i class="uil uil-angle-down float-right accordion-arrow"></i>
                                </h5>
                            </div>
                        </a>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                            data-parent="#accordionExample">
                            <div class="card-body text-muted">
                                <div class="row py-1">
                                    <div class="col-xl-12 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-3">
                                            <div class="media-body">
                                                <div class="form-group">
                                                    <label for="sw-default">Codigo Empleado</label>
                                                    <input type="text" class="form-control text-center"
                                                        name="v_codigoEmpleadoV" id="v_codigoEmpleadoV" tabindex="1"
                                                        style="border: none" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-3">
                                            <div class="media-body">
                                                <div class="form-group">
                                                    <label for="sw-default">Cargo</label>
                                                    <input class="form-control text-center" name="v_cargoV"
                                                        id="v_cargoV" tabindex="2" style="border: none" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Contrato</label>
                                                    <input class="form-control text-center" name="v_contratoV"
                                                        id="v_contratoV" tabindex="5" style="border: none" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-3">
                                            <div class="media-body">
                                                <div class="form-group">
                                                    <label for="sw-default">Área</label>
                                                    <input class="form-control text-center" name="v_areaV" id="v_areaV"
                                                        tabindex="3" style="border: none" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Nivel del Colaborador</label>
                                                    <input class="form-control text-center" name="v_nivelV"
                                                        id="v_nivelV" tabindex="6" style="border: none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-3">
                                            <div class="media-body">
                                                <div class="form-group">
                                                    <label for="sw-default">Centro Costo</label>
                                                    <input class="form-control text-center" name="v_centrocV"
                                                        id="v_centrocV" tabindex="4" style="border: none" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sw-default">Local</label>
                                                    <input class="form-control text-center" name="v_localV"
                                                        id="v_localV" tabindex="7" style="border: none" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal"
                                style="background-color: #163552;color: #ffffff;">Cerrar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right:
                            38px;">
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!---->
<input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">
<div class="row">

    <div class="col-md-6" id="filter_global">

        <td align="center"><input type="text" class="global_filter form-control
                form-control-sm" id="global_filter">
        </td>
    </div>
    <div class="col-md-6" id="filter_col2" data-column="2" style="display:
        none">
        <label for="">Nombre:</label>
        <td align="center"><input type="text" class="column_filter form-control
                form-control-sm" id="col2_filter"></td>
    </div>
    <div class="col-md-6" id="filter_col3" data-column="3" style="display:
        none">
        <label>Apellidos</label>
        <td align="center"><input type="text" class="column_filter form-control
                form-control-sm" id="col3_filter"></td>
    </div>
    <div class="col-md-6" id="filter_col4" data-column="4" style="display:
        none">
        <label for="">Cargo</label>
        <td align="center"><input type="text" class="column_filter form-control
                form-control-sm" id="col4_filter"></td>
    </div>
    <div class="col-md-6" id="filter_col5" data-column="5" style="display:
        none">
        <label for="">Área</label>
        <td align="center"><input type="text" class="column_filter form-control
                form-control-sm" id="col5_filter"></td>
    </div>
    <div class="col-md-6" id="filter_col6" data-column="6" style="display:
        none">
        <label for="">Costo</label>
        <td align="center"><input type="text" class="column_filter form-control
                form-control-sm" id="col6_filter"></td>
    </div>

</div>

<table id="tablaEmpleado" class="table dt-responsive nowrap">
    <thead style="background: #edf0f1;color: #6c757d;">
        <tr style="background: #ffffff">
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="inputR" id="i2"></th>
            <th style="border-top: 1px solid #fdfdfd;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio"
                    name="inputR" id="i3"></th>
            <th style="border-top: 1px solid #fdfdfd;">&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="inputR"
                    id="i4"></th>
            <th style="border-top: 1px solid #fdfdfd;">&nbsp;&nbsp;&nbsp;<input type="radio" name="inputR" id="i5">
            </th>
            <th style="border-top: 1px solid #fdfdfd;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="inputR" id="i6">
            </th>
            <th class="text-center" style="border-top: 1px solid #fdfdfd;" id="enviarCorreosMasivos">
                <button type="button" class="btn  btn-sm btn-rounded" onclick="$('#modalCorreoM').modal();"
                    style="color: #548ec7;border-color: #e7edf3; padding-left: 4px; padding-right: 4px;"
                    data-toggle="tooltip" data-placement="right" title="Enviar a todos los empleados
                    seleccionados." data-original-title="">Enviar&nbsp;&nbsp;<img
                        src="{{asset('landing/images/group.svg')}}" height="20"></button></th>
            <th class="text-center" style="border-top: 1px solid #fdfdfd;" id="enviarAndroidMasivos"> <button
                    type="button" class="btn  btn-sm btn-rounded" onclick="$('#modalAndroidMasivo').modal();"
                    style="color: #548ec7;border-color: #e7edf3; padding-left: 4px; padding-right: 4px;"
                    data-toggle="tooltip" data-placement="right" title="Enviar a todos los empleados
                    seleccionados." data-original-title="">Enviar&nbsp;&nbsp;<img
                        src="{{asset('landing/images/group.svg')}}" height="20"></button></th>
            <th class="text-center" style="border-top: 1px solid #fdfdfd;" id="enviarMasivo">
                <a style="cursor: pointer" data-toggle="tooltip" data-placement="right"
                    title="Enviar para ambas plataformas Windows y Android" data-original-title=""
                    onclick="$('#modalCorreoAmbos').modal();"><img src="{{asset('landing/images/mail (3).svg')}}"
                        height="30"></a>
            </th>
        </tr>
        <tr>
            <th></th>
            <th class="text-center"></th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Cargo</th>
            <th>Área</th>
            <th>Centro de Costo</th>
            <th class="text-center">Windows</th>
            <th class="text-center">Android</th>
            <th>&nbsp;<input type="checkbox" name="" id="selectT"></th>

        </tr>
    </thead>
    <tbody style="background:#ffffff;color: #585858;font-size: 12.5px" id="tbodyr">
        @foreach ($tabla_empleado as $tabla_empleados)
        <tr class="" id="{{$tabla_empleados->emple_id}}" value="{{$tabla_empleados->emple_id}}">

            <td><a id="formNuevoEd" onclick="$('#form-ver').modal();" style="cursor: pointer"><img
                        src="{{asset('admin/images/edit.svg')}}" height="15"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a
                    onclick="marcareliminar({{$tabla_empleados->emple_id}})" style="cursor: pointer"><img
                        src="{{asset('admin/images/delete.svg')}}" height="15"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a class="verEmpleado" data-toggle="tooltip" data-placement="right" title="Ver Detalles"
                    data-original-title="Ver Detalles" style="cursor:
                        pointer">
                    <img src="{{asset('landing/images/see.svg')}}" height="20">
                </a>
            </td>
            <td class="text-center">&nbsp; <input type="hidden" id="codE" value="{{$tabla_empleados->emple_id}}"><img
                    src="{{ URL::asset('admin/assets/images/users/empleado.png')
                    }}" class="" alt="" /></td>
            <td>{{$tabla_empleados->perso_nombre}}</td>
            <td>{{$tabla_empleados->perso_apPaterno}} {{$tabla_empleados->perso_apMaterno}}</td>
            <td>{{$tabla_empleados->cargo_descripcion}}</td>
            <td>{{$tabla_empleados->area_descripcion}}</td>
            <td>{{$tabla_empleados->centroC_descripcion}} </td>
            @if(!in_array("1",$tabla_empleados->dispositivos))
            <td></td>
            @else
            @if ($tabla_empleados->envio != null)
            @if($tabla_empleados->reenvio == null)
            <td class="text-center">
                <a id="reenviar" onclick="$('#modalCorreo').modal();" data-toggle="tooltip" data-placement="right"
                    title="Reenviar correo empleado" data-original-title="Reenviar correo empleado"
                    style="cursor: pointer"><img src="{{asset('landing/images/refresh.svg')}}" height="20">
                </a>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <a data-toggle="tooltip" data-placement="right" title="Nueva PC" data-original-title="Nueva PC"
                    style="cursor:pointer" onclick="$('#modalNuevoE').modal();">
                    <img src="{{asset('landing/images/plus.svg')}}" height="15">
                </a>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <a class="detalleW" data-toggle="tooltip" data-placement="right" title="Ver Detalles"
                    data-original-title="Ver Detalles" style="cursor:
                    pointer">
                    <img src="{{asset('landing/images/see.svg')}}" height="20">
                </a>
            </td>
            @else
            <td class="text-center">
                <a id="reenviar" onclick="$('#modalCorreo').modal();" disabled><img
                        src="{{asset('landing/images/refresh.svg')}}" height="20">
                </a>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <a data-toggle="tooltip" data-placement="right" title="Nueva PC" data-original-title="Nueva PC"
                    style="cursor: pointer" onclick="$('#modalNuevoE').modal();"><img
                        src="{{asset('landing/images/plus.svg')}}" height="15">
                </a>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <a class="detalleW" data-toggle="tooltip" data-placement="right" title="Ver Detalles"
                    data-original-title="Ver Detalles" style="cursor: pointer"><img
                        src="{{asset('landing/images/see.svg')}}" height="20">
                </a>
            </td>
            @endif
            @else
            <td class="text-center">
                <a onclick="$('#modalCorreo').modal();" data-toggle="tooltip" data-placement="right" title="Enviar
                    correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                        src="{{asset('landing/images/note.svg')}}" height="20">
                </a>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <a class="detalleW" data-toggle="tooltip" data-placement="right" title="Ver Detalles"
                    data-original-title="Ver Detalles" style="cursor: pointer"><img
                        src="{{asset('landing/images/see.svg')}}" height="20">
                </a>
            </td>
            @endif
            @endif
            @if(!in_array("2",$tabla_empleados->dispositivos))
            <td></td>
            @else
            <td class="text-center">
                <a onclick="$('#modalAndroid').modal();" data-toggle="tooltip" data-placement="right"
                    title="Enviar correo empleado" data-original-title="Enviar correo empleado"
                    style="cursor: pointer"><img src="{{asset('landing/images/note.svg')}}" height="20">
                </a>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <a class="detalleA" data-toggle="tooltip" data-placement="right" title="Ver Detalles"
                    data-original-title="Ver Detalles" style="cursor:
                    pointer">
                    <img src="{{asset('landing/images/see.svg')}}" height="20">
                </a>
            </td>
            @endif
            <td class="text-center"><input type="checkbox" id="tdC" style="margin-left:5.5px!important"
                    class="form-check-input sub_chk" data-id="{{$tabla_empleados->emple_id}}" $(this)$(this)$(this)>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<script>
    $('[data-toggle="tooltip"]').tooltip();
    $('#enviarCorreosMasivos').hide();
    $('#enviarAndroidMasivos').hide();
    $('#enviarMasivo').hide();
    $('#filter_col2').hide();
    $('#filter_col3').hide();
    $('#filter_col4').hide();
    $('#filter_col5').hide();
    $('#filter_col6').hide();
    var seleccionarTodos = $('#selectT');
    var table = $('#tablaEmpleado');
    var CheckBoxs = table.find('tbody input:checkbox');
    var CheckBoxMarcados = 0;

    seleccionarTodos.on('click', function () {
        if (seleccionarTodos.is(":checked")) {
            CheckBoxs.prop('checked', true);
            $('#enviarCorreosMasivos').show();
            $('#enviarAndroidMasivos').show();
            $('#enviarMasivo').show();
        } else {
            CheckBoxs.prop('checked', false);
            $('#enviarCorreosMasivos').hide();
            $('#enviarAndroidMasivos').hide();
            $('#enviarMasivo').hide();
        };

    });


    CheckBoxs.on('change', function (e) {
        CheckBoxMarcados = table.find('tbody input:checkbox:checked').length;
        if (CheckBoxMarcados > 0) {
            $('#enviarCorreosMasivos').show();
            $('#enviarAndroidMasivos').show();
            $('#enviarMasivo').show();
        } else {
            $('#enviarCorreosMasivos').hide();
            $('#enviarAndroidMasivos').hide();
            $('#enviarMasivo').hide();
        }
        seleccionarTodos.prop('checked', (CheckBoxMarcados === CheckBoxs.length));
    });

</script>
<script>
    $("#tablaEmpleado tbody tr").click(function () {
        $('#smartwizard1').smartWizard("reset");
        //$(this).addClass('selected').siblings().removeClass('selected');
        var value = $(this).find('input[type=hidden]').val();
        $('#formNuevoEl').show();
        $.ajax({
            async: false,
            type: "get",
            url: "empleado/show",
            data: {
                value: value
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                401: function () {
                    location.reload();
                }
            },
            success: function (data) {


                $('#v_tipoDoc').val(data[0].tipoDoc_descripcion);
                $('#v_apPaterno').val(data[0].perso_apPaterno);
                $('#v_departamento').val(data[0].depaN);
                onSelectVDepartamento('#v_departamento').then(function () {
                    $('#v_provincia').val(data[0].idproviN);
                    onSelectVProvincia('#v_provincia').then((result) => $('#v_distrito')
                        .val(data[0].iddistN))
                });


                $('#v_dep').val(data[0].deparNo);
                onSelectVDepart('#v_dep').then(function () {
                    $('#v_prov').val(data[0].proviId);
                    onSelectVProv('#v_prov').then((result) => $('#v_dist').val(data[0]
                        .distId))
                });

                $('#v_numDocumento').val(data[0].emple_nDoc);
                $('#v_apMaterno').val(data[0].perso_apMaterno);
                $("[name=v_tipo]").val([data[0].perso_sexo]);
                $('#v_fechaN').combodate('setValue', data[0].perso_fechaNacimiento);
                $('#v_nombres').val(data[0].perso_nombre);
                $('#v_direccion').val(data[0].perso_direccion);


                $('#v_cargo').val(data[0].cargo_id);
                $('#v_area').val(data[0].area_id);
                $('#v_centroc').val(data[0].centroC_id);
                id_empleado = data[0].emple_id;
                $('#v_id').val(data[0].emple_id);
                $('#v_contrato').val(data[0].emple_tipoContrato);
                $('#v_nivel').val(data[0].emple_nivel);
                $('#v_local').val(data[0].emple_local);
                if(data[0].emple_celular != '' || data[0].emple_celular != null){
                    celularSplit = data[0].emple_celular.split("+51");
                    console.log(celularSplit);
                    $('#v_celular').val(celularSplit[1]);
                }
                $('#v_telefono').val(data[0].emple_telefono);
                $('#m_fechaIE').combodate('setValue', data[0].emple_fechaIC);
                if (data[0].emple_fechaFC == null || data[0].emple_fechaFC == "0000-00-00") {
                    $("#checkboxFechaIE").prop('checked', true);
                    $('#ocultarFechaE > .combodate').hide();
                    $('#ocultarFechaE').hide();
                }
                $('#m_fechaFE').combodate('setValue', data[0].emple_fechaFC);
                $('#v_email').val(data[0].emple_Correo);
                $('#v_codigoEmpleado').val(data[0].emple_codigo);
                if (data[0].foto != "") {
                    urlFoto = data[0].foto;
                    hayFoto = true;
                    $('#file2').fileinput('destroy');
                    cargarFile2();
                    $('#v_foto').attr("src", "{{asset('/fotosEmpleado')}}" + "/" + data[0].foto);
                } else {
                    hayFoto = false;
                    urlFoto = "";
                    $('#file2').fileinput('destroy');
                    cargarFile2();
                }
                //DETALLES DE PLATAFORMAS
                $('#idDetalle').val(data[0].emple_id);
                $('#idDetalleA').val(data[0].emple_id);
                $('#colaborador').text(data[0].perso_apPaterno + ' ' + data[0].perso_apMaterno +
                    ' ' + data[0].perso_nombre);
                $('#colaboradorA').text(data[0].perso_apPaterno + ' ' + data[0].perso_apMaterno +
                    ' ' + data[0].perso_nombre);
                $('#totalPC').text(data[0].total);
                $('#licencia').text(data[0].licencia);
                $('#detalleLicencia').empty();
                $('#estadoLicencia').empty();
                var container = $('#detalleLicencia');
                var detalle = "";
                var containerDisponible = $('#estadoLicencia');
                var disponible = "";
                if (data[0].total == 0) {
                    $('#rowDetalles').hide();
                } else {
                    $('#rowDetalles').show();
                    for (var i = 0; i < data[0].licencia.length; i++) {
                        console.log(data[0].licencia[i]);
                        detalle +=
                            `<p class="mb-2"><img src="{{asset('landing/images/laptop.svg')}}" height="20"
                                                    class="mr-2" alt="" />  ${i+1}</p>
                            <h5 class="font-size-16" id="lic${data[0].licencia[i].id}">${data[0].licencia[i].licencia}</h5>`;
                        if (data[0].licencia[i].disponible == 1) {
                            disponible +=
                                `<p class="mb-2"><i class="uil-calendar-slash text-danger"></i>Estado</p>
                                            <h5 class="font-size-16"><a class="badge badge-soft-primary mr-2">Disponible</a></h5>`;
                        } else {
                            disponible +=
                                `<input style="display: none;" id="idLicenciaND${data[0].emple_id}" value="${data[0].licencia[i].id}"><p class="mb-2"><i class="uil-calendar-slash text-danger"></i>Estado</p>
                                            <h5 class="font-size-16"><a data-toggle="tooltip" data-placement="right"
                                            title=" Al cambiar el estado de la licencia toda información del empleado en su PC será borrada" data-original-title="" onclick="$('#estadoLicenciaC').modal()" style="cursor: pointer" class="badge badge-soft-danger mr-2">No Disponible</a></h5>`;
                        }
                    }
                    container.append(detalle);
                    containerDisponible.append(disponible);
                }

                $('#v_tipoDocV').val(data[0].tipoDoc_descripcion);
                $('#v_apPaternoV').val(data[0].perso_apPaterno);
                $('#v_direccionV').val(data[0].perso_direccion);
                $('#v_idV').val(data[0].emple_id);
                $('#v_fechaNV').val(data[0].perso_fechaNacimiento);
                $('#v_apMaternoV').val(data[0].perso_apMaterno);
                $('#v_numDocumentoV').val(data[0].emple_nDoc);
                $('#v_emailV').val(data[0].emple_Correo);
                $('#v_celularV').val(data[0].emple_celular);
                $('#v_nombresV').val(data[0].perso_nombre);
                $('#v_telefonoV').val(data[0].emple_telefono);
                $('#v_depV').val(data[0].deparNo);
                $('#v_departamentoV').val(data[0].depaN);
                $("[name=v_tipoV]").val([data[0].perso_sexo]);
                $('#v_provV').val(data[0].proviId);
                $('#v_provinciaV').val(data[0].idproviN);
                $('#v_distV').val(data[0].distId)
                $('#v_distritoV').val(data[0].iddistN)
                $('#v_cargoV').val(data[0].cargo_descripcion);
                $('#v_areaV').val(data[0].area_descripcion);
                $('#v_centrocV').val(data[0].centroC_descripcion);
                $('#v_contratoV').val(data[0].contrato_descripcion);
                $('#v_nivelV').val(data[0].nivel_descripcion);
                $('#v_localV').val(data[0].local_descripcion);
                $('#v_codigoEmpleadoV').val(data[0].emple_codigo);
            },
            error: function () {}
        });
    });

</script>
<script>
    $('.verEmpleado').on("click", function () {
        $('#disabledVer').find(":input").prop("disabled", true);
        $('#verEmpleado').modal();
    });

</script>
<script>
    function filterGlobal() {
        $('#tablaEmpleado').DataTable().search(
            $('#global_filter').val(),

        ).draw();
    }

    function filterColumn(i) {
        $("#tablaEmpleado").DataTable({
            retrieve: true,
            "searching": true,
            "scrollX": true,

            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ ",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": ">",
                    "sPrevious": "<"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },


        }).column(i).search(

            $('#col' + i + '_filter').val(),
        ).draw();
        $('#i' + i).prop('checked', true);
    }

    $(document).ready(function () {

        var table = $("#tablaEmpleado").DataTable({
            retrieve: true,
            "searching": true,
            "lengthChange": false,
            "scrollX": true,
            "pageLength": 30,

            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ ",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": ">",
                    "sPrevious": "<"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },


        });
        //$('#verf1').hide();
        //$('#tablaEmpleado tbody #tdC').css('display', 'none');

        $("#tablaEmpleado tbody tr").hover(function () {
            //$('#verf1').css('display', 'block');
            $('#tablaEmpleado tbody #tdC').css('display', 'block');

        }, function () {

        });


        $("#i2").click(function () {
            if ($("#i2").is(':checked')) {
                table
                    .search('')
                    .columns().search('')
                    .draw();
                $('#i2').prop('checked', true);
                $('#filter_global').hide()
                $('#filter_col2').show();
                $('#filter_col3').hide();
                $('#filter_col4').hide();
                $('#filter_col5').hide();
                $('#filter_col6').hide();

            } else {
                alert("No está activado");
            }
        });

        $("#i3").click(function () {
            if ($("#i3").is(':checked')) {
                table
                    .search('')
                    .columns().search('')
                    .draw();
                $('#i3').prop('checked', true);
                $('#filter_global').hide()
                $('#filter_col2').hide();
                $('#filter_col3').show();
                $('#filter_col4').hide();
                $('#filter_col5').hide();
                $('#filter_col6').hide();


            } else {
                alert("No está activado");
            }
        });

        $("#i4").click(function () {
            if ($("#i4").is(':checked')) {
                table
                    .search('')
                    .columns().search('')
                    .draw();
                $('#i4').prop('checked', true);
                $('#filter_global').hide()
                $('#filter_col2').hide();
                $('#filter_col3').hide();
                $('#filter_col4').show();
                $('#filter_col5').hide();
                $('#filter_col6').hide();

            } else {
                alert("No está activado");
            }
        });
        $("#i5").click(function () {
            if ($("#i5").is(':checked')) {
                table
                    .search('')
                    .columns().search('')
                    .draw();
                $('#i5').prop('checked', true);
                $('#filter_global').hide()
                $('#filter_col2').hide();
                $('#filter_col3').hide();
                $('#filter_col4').hide();
                $('#filter_col5').show();
                $('#filter_col6').hide();

            } else {
                alert("No está activado");
            }
        });
        $("#i6").click(function () {
            if ($("#i6").is(':checked')) {
                table
                    .search('')
                    .columns().search('')
                    .draw();
                $('#i6').prop('checked', true);
                table.columns([1, 2, 3, 4, 5]).deselect();
                $('#filter_global').hide()
                $('#filter_col2').hide();
                $('#filter_col3').hide();
                $('#filter_col4').hide();
                $('#filter_col5').hide();
                $('#filter_col6').show();

            } else {
                alert("No está activado");
            }
        });


        $('input.global_filter').on('keyup click', function () {
            filterGlobal();
        });

        $('input.column_filter').on('keyup click', function () {
            filterColumn($(this).parents('div').attr('data-column'));
        });
    });

</script>
{{-- ELIMINAR VARIOS ELEMENTOS --}}
<script>
    /*   $('.delete_all').click(function(e) {
        e.preventDefault();
        var allVals = [];
        allVals = [];
        console.log(allVals);

        $(".sub_chk:checked").each(function () {
            allVals.push($(this).attr('data-id'));
        });

        if (allVals.length <= 0) {
            alert("Por favor seleccione una fila.");
            return false;
        } else {
            $('#modalEliminar').modal();


        }


    });
    $('#confirmarE').click(function (e) {
            e.preventDefault();
            var allVals = [];
        allVals = [];
        console.log(allVals);

        $(".sub_chk:checked").each(function () {
            allVals.push($(this).attr('data-id'));
        });
        var join_selected_values = allVals.join(",");
        var table = $('#tablaEmpleado').DataTable();
        $.ajax({
            url: "/eliminarEmpleados",
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: 'ids=' + join_selected_values,
            success: function (data) {

                $('#modalEliminar').modal('hide');
                leertabla();
            },
            error: function (data) {
                alert(data.responseText);
            }
        });
}); */
    function eliminarEmpleado() {
        var allVals = [];
        console.log(allVals);

        $(".sub_chk:checked").each(function () {
            allVals.push($(this).attr('data-id'));
        });

        if (allVals.length <= 0) {
            alert("Por favor seleccione una fila.");
            return false;
        } else {
            $('#modalEliminar').modal();


        }

    }

    function confirmarEliminacion() {
        var allVals = [];
        console.log(allVals);

        $(".sub_chk:checked").each(function () {
            allVals.push($(this).attr('data-id'));
        });
        var join_selected_values = allVals.join(",");
        var table = $('#tablaEmpleado').DataTable();
        $.ajax({
            url: "/eliminarEmpleados",
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            data: 'ids=' + join_selected_values,
            success: function (data) {

                $('#modalEliminar').modal('hide');
                leertabla();
                $.notify({
                    message: '\nEmpleado eliminado',
                    icon: 'landing/images/bell.svg',
                }, {
                    icon_type: 'image',
                    allow_dismiss: true,
                    newest_on_top: true,
                    delay: 6000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #f2dede;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            },
            error: function (data) {
                alert(data.responseText);
            }
        });

    }

    function marcareliminar(data) {
        $('input:checkbox').prop('checked', false);

        $('input:checkbox[data-id=' + data + ']').prop('checked', true);
        $('.delete_all').click();
    }

</script>
{{-- CORREO MASIVO--}}
<script>
    function CorreosMasivos() {
        var correoEmpleado = [];
        $(".sub_chk:checked").each(function () {
            correoEmpleado.push($(this).attr('data-id'));
        });
        console.log(correoEmpleado);
        var join_selected_values = correoEmpleado.join(",");
        $.ajax({
            async: false,
            type: "get",
            url: "envioMasivo",
            data: 'ids=' + join_selected_values,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                console.log(data);
                for (var i = 0; i < data.length; i++) {
                    if (data[i].Correo == true && data[i].Reenvio == true) {
                        $.notify({
                            message: "\nCorreo enviado a\n" + data[i].Persona.perso_nombre + " " +
                                data[i].Persona.perso_apPaterno + " " + data[i].Persona
                                .perso_apMaterno,
                            icon: 'admin/images/checked.svg'
                        }, {
                            icon_type: 'image',
                            newest_on_top: true,
                            delay: 5000,
                            template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                '</div>',
                            spacing: 35
                        });
                    } else {
                        if (data[i].Correo != true) {
                            $.notify({
                                message: "\nAún no ha registrado correo a\n" + data[i].Persona
                                    .perso_nombre + " " + data[i].Persona.perso_apPaterno + " " +
                                    data[
                                        i].Persona.perso_apMaterno,
                                icon: 'admin/images/warning.svg'
                            }, {
                                icon_type: 'image',
                                newest_on_top: true,
                                delay: 5000,
                                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                    '</div>',
                                spacing: 35
                            });
                        }
                        if (data[i].Reenvio != true) {
                            $.notify({
                                message: data[i].Persona.perso_nombre + " " + data[i].Persona
                                    .perso_apPaterno + " " + data[i].Persona.perso_apMaterno +
                                    "\nllego al limite de envio de correo",
                                icon: 'admin/images/warning.svg'
                            }, {
                                icon_type: 'image',
                                newest_on_top: true,
                                delay: 5000,
                                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                    '</div>',
                                spacing: 35
                            });
                        }
                    }
                }
                $('#modalCorreoM').modal('toggle');
                leertabla();
            }
        });
    }
    $('#enviarCorreoM').on("click", CorreosMasivos);

</script>
{{-- ANDROID MASIVO--}}
<script>
    function androidMasivos() {
        var correoEmpleado = [];
        $(".sub_chk:checked").each(function () {
            correoEmpleado.push($(this).attr('data-id'));
        });
        var join_selected_values = correoEmpleado.join(",");
        $.ajax({
            async: false,
            type: "get",
            url: "empleadoAndroidMasivo",
            data: 'ids=' + join_selected_values,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                console.log(data);
                for (var i = 0; i < data.length; i++) {
                    if (data[i].Correo == true) {
                        $.notify({
                            message: "\nCorreo enviado a " + data[i].Persona.perso_nombre + " " +
                                data[i].Persona.perso_apPaterno + " " + data[i].Persona
                                .perso_apMaterno,
                            icon: 'admin/images/checked.svg'
                        }, {
                            icon_type: 'image',
                            newest_on_top: true,
                            delay: 5000,
                            template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                '</div>',
                            spacing: 35
                        });
                    } else {
                        if (data[i].Correo != true) {
                            $.notify({
                                message: "\nAún no ha registrado correo a " + data[i].Persona
                                    .perso_nombre + " " + data[i].Persona.perso_apPaterno + " " +
                                    data[
                                        i].Persona.perso_apMaterno,
                                icon: 'admin/images/warning.svg'
                            }, {
                                icon_type: 'image',
                                newest_on_top: true,
                                delay: 5000,
                                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                    '</div>',
                                spacing: 35
                            });
                        }
                    }
                }
                $('#modalAndroidMasivo').modal('toggle');
                leertabla();
            }
        });
    }
    $('#enviarAndroidMasivo').on("click", androidMasivos);

</script>
{{-- AMBAS PLATAFORMAS--}}
<script>
    function ambasPlataformas() {
        var correoEmpleado = [];
        $(".sub_chk:checked").each(function () {
            correoEmpleado.push($(this).attr('data-id'));
        });
        console.log(correoEmpleado);
        var join_selected_values = correoEmpleado.join(",");
        $.ajax({
            async: false,
            type: "get",
            url: "ambasPlataformas",
            data: 'ids=' + join_selected_values,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                console.log(data);
                for (var i = 0; i < data.length; i++) {
                    if (data[i].Correo == true && data[i].Reenvio == true) {
                        $.notify({
                            message: "\nCorreo enviado a\n" + data[i].Persona.perso_nombre + " " +
                                data[i].Persona.perso_apPaterno + " " + data[i].Persona
                                .perso_apMaterno,
                            icon: 'admin/images/checked.svg'
                        }, {
                            icon_type: 'image',
                            newest_on_top: true,
                            delay: 5000,
                            template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                '</div>',
                            spacing: 35
                        });
                    } else {
                        if (data[i].Correo != true) {
                            $.notify({
                                message: "\nAún no ha registrado correo a\n" + data[i].Persona
                                    .perso_nombre + " " + data[i].Persona.perso_apPaterno + " " +
                                    data[
                                        i].Persona.perso_apMaterno,
                                icon: 'admin/images/warning.svg'
                            }, {
                                icon_type: 'image',
                                newest_on_top: true,
                                delay: 5000,
                                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                    '</div>',
                                spacing: 35
                            });
                        }
                        if (data[i].Reenvio != true) {
                            $.notify({
                                message: data[i].Persona.perso_nombre + " " + data[i].Persona
                                    .perso_apPaterno + " " + data[i].Persona.perso_apMaterno +
                                    "\nllego al limite de envio de correo",
                                icon: 'admin/images/warning.svg'
                            }, {
                                icon_type: 'image',
                                newest_on_top: true,
                                delay: 5000,
                                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                    '</div>',
                                spacing: 35
                            });
                        }
                    }
                }
                $('#modalCorreoAmbos').modal('toggle');
                leertabla();
            }
        });
    }
    $('#enviarAmbasP').on("click", ambasPlataformas);

</script>
<script src="{{
        URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')
        }}"></script>
<script src="{{
        URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')
        }}"></script>
<script src="{{asset('landing/js/correoEmpleados.js')}}"></script>
<script src="{{asset('landing/js/correoAndroid.js')}}"></script>
<script src="{{asset('landing/js/detallesPlataforma.js')}}"></script>