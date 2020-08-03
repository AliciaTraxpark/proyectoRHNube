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
        border-top: 1px solid #edf0f1;
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
                                    <div class="col-xl-5 col-sm-12 text-center">
                                        <!-- stat 1 -->
                                        <div class="media p-2">
                                            <div class="media-body">
                                                <img id="imgsmEmpleado"
                                                    src="{{URL::asset('admin/assets//images/users/avatar-7.png') }}"
                                                    class="avatar avatar-128 rounded-circle mr-2 img-thumbnail" />
                                                <span class="text-muted" id="colaborador"
                                                    style="text-transform:uppercase;"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-2">
                                            <div class="media-body mt-3">
                                                <span class="text-muted" style="font-weight: 600">Total
                                                    Dispositivos:</span>
                                                <span class="text-muted" id="totalPC"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-12 text-center">
                                        <!-- stat 2 -->
                                        <div class="media p-2">
                                            <div class="media-body mt-3">
                                                <span class="text-muted" style="font-weight: 600">Captura(min):</span>
                                                <span class="text-muted" id="totalCorteCap"></span>
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
                                <div class="row">
                                    <div class="col-lg-8 col-md-6">
                                        <div class="mt-2" id="detalleLicencia">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="mt-2" id="estadoLicencia">
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
                                                <img id="imgsmEmpleadoAndroid"
                                                    src="{{URL::asset('admin/assets//images/users/avatar-7.png') }}"
                                                    class="avatar avatar-128 rounded-circle mr-2 img-thumbnail" />
                                                <span class="text-muted" id="colaboradorA"
                                                    style="text-transform:uppercase;"></span>
                                            </div>
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
                    </div>
                </div>
                <!-- details-->
                <!--<div class="row" id="notifEnlace">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 text-center">
                                        <div class="mt-2">
                                            <img src="{{asset('landing/images/playstore.svg')}}" height="20"
                                                class="mr-2" alt="" />
                                            <p class="mb-2">Play Store</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 text-center">
                                        <div class="mt-2">
                                            <a href="javascript:getlink();" data-toggle="tooltip" data-placement="right"
                                                title="copiar enlace" data-original-title="">
                                                <img src="{{asset('landing/images/document.svg')}}" height="30">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>-->
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
<!---->
<input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">
<div class="row">
    <div class="col-md-4" id="filter_global">
        <td align="center"><input type="text" class="global_filter form-control
                form-control-sm" id="global_filter">
        </td>
    </div>
    <div class="col-md-2">
        <td align="center">
            <select class="form-control" name="select" id="select">
                <option value="-1">PERSONALIZADO</option>
                <option value="2">Nombre</option>
                <option value="3">Apellidos</option>
                <option value="4">Cargo</option>
                <option value="5">Área</option>
                <option value="6">Costo</option>
            </select>
        </td>
    </div>
</div>

<table id="tablaEmpleado" class="table dt-responsive nowrap">
    <thead style="background: #edf0f1;color: #6c757d;">
        <tr style="background: #ffffff">
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
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
                <a class="verEmpleado" onclick="$('#verEmpleadoDetalles').modal();" data-toggle="tooltip"
                    data-placement="right" title="Ver Detalles" data-original-title="Ver Detalles" style="cursor:
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
        $( "#detallehorario_ed" ).empty();
        $('#smartwizard1').smartWizard("reset");
        $('#smartwizardVer').smartWizard("reset");
        $('#MostrarCa_e').hide();
        $('#calendarInv_ed').hide();
        $('#calendar_ed').hide();
        $('#h5Ocultar').show();
        $('#v_fotoV').attr("src", "landing/images/png.svg");
        //$(this).addClass('selected').siblings().removeClass('selected');
        var value = $(this).find('input[type=hidden]').val();
        $('#selectCalendario').val("Asignar calendario");
        $('#selectHorario').val("Seleccionar horario");
        $('#selectCalendario_ed').val("Asignar calendario");

        $('#idempleado').val(value);
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
                console.log(data);
                calendario3();

                $('#v_tipoDoc').val(data[0].tipoDoc_descripcion);
                $('#v_apPaterno').val(data[0].perso_apPaterno);
                $('#v_departamento').val(data[0].iddepaN);
                onSelectVDepartamento('#v_departamento').then(function () {
                    $('#v_provincia').val(data[0].idproviN);
                    onSelectVProvincia('#v_provincia').then((result) => $('#v_distrito')
                        .val(data[0].iddistN))
                });

                $.ajax({
                type:"POST",
                url: "/empleado/calendarioEditar",
                data: {
                    idempleado:value
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    }
                },
                success: function (data) {
                    if(data==1){
                        $('#MostrarCa_e').show();
                        $('#calendarInv_ed').show();
                    }
                    else{
                        $('#calendar_ed').show();
                        $('#mensajeOc_ed').hide();
                        $('#calendar2_ed').show();



                    }


                },
                error: function () {}
            });
                $('#v_dep').val(data[0].depar);
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
                $('#v_codigoCelular').val("+51");
                $('#v_celular').val(data[0].emple_celular);
                if(data[0].emple_celular != '' ){
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
                calendario_edit();
                calendario2_ed();
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
                $('#v_tbodyDispositivo').empty();
                var container = $('#v_tbodyDispositivo');
                for (var i = 0; i < data[0].vinculacion.length; i++) {
                    if(data[0].vinculacion[i].dispositivoD == 'WINDOWS'){
                        if(data[0].vinculacion[i].disponible == 'c'){
                            var tr = `<tr>
                            <td>${data[0].vinculacion[i].dispositivoD}</td>
                            <td>${data[0].vinculacion[i].licencia}</td>
                            <td>${data[0].vinculacion[i].codigo}</td>
                            <td id="enviadoW${data[0].vinculacion[i].idVinculacion}">${data[0].vinculacion[i].envio}</td>
                            <td id="estado${data[0].vinculacion[i].idVinculacion}">Creado</td>
                            <td>
                                <a  onclick="javascript:modalWindowsEditar(${data[0].vinculacion[i].idVinculacion});$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                                    correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                                    src="landing/images/note.svg" height="20">
                                </a>
                            </td>
                            </tr>`;
                        }
                        if(data[0].vinculacion[i].disponible == 'e'){
                            var tr = `<tr>
                            <td>${data[0].vinculacion[i].dispositivoD}</td>
                            <td>${data[0].vinculacion[i].licencia}</td>
                            <td>${data[0].vinculacion[i].codigo}</td>
                            <td id="enviadoW${data[0].vinculacion[i].idVinculacion}">${data[0].vinculacion[i].envio}</td>
                            <td id="estado${data[0].vinculacion[i].idVinculacion}">Enviado</td>
                            <td>
                                <a  onclick="javascript:modalWindowsEditar(${data[0].vinculacion[i].idVinculacion});$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                                    correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                                    src="landing/images/note.svg" height="20">
                                </a>
                            </td>
                            </tr>`;
                        }
                    }else{
                        if(data[0].vinculacion[i].disponible == 'c'){
                            var tr = `<tr>
                                <td>${data[0].vinculacion[i].dispositivoD}</td>
                                <td>${data[0].vinculacion[i].licencia}</td>
                                <td>${data[0].vinculacion[i].codigo}</td>
                                <td id="enviado${data[0].emple_id}">${data[0].vinculacion[i].envio}</td>
                                <td id="estado${data[0].emple_id}">Creado</td>
                                <td>
                                <input style="display: none;" id="android${data[0].emple_id}" value="${data[0].vinculacion[i].idVinculacion}">
                                <a  onclick="$('#v_androidEmpleado').modal();$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                                correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                                    src="landing/images/note.svg" height="20">
                            </a></td>
                                </tr>`;
                        }
                        if(data[0].vinculacion[i].disponible == 'e'){
                            var tr = `<tr>
                                <td>${data[0].vinculacion[i].dispositivoD}</td>
                                <td>${data[0].vinculacion[i].licencia}</td>
                                <td>${data[0].vinculacion[i].codigo}</td>
                                <td id="enviado${data[0].emple_id}">${data[0].vinculacion[i].envio}</td>
                                <td id="estado${data[0].emple_id}">Enviado</td>
                                <td>
                                <input style="display: none;" id="android${data[0].emple_id}" value="${data[0].vinculacion[i].idVinculacion}">
                                <a  onclick="$('#v_androidEmpleado').modal();$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                                correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                                    src="landing/images/note.svg" height="20">
                            </a></td>
                                </tr>`;
                        }
                    }
                container.append(tr);
                }
            },
            error: function () {}
        });
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
            "lengthChange": false,
            "scrollX": true,
            "pageLength": 30,
            fixedHeader: true,
            "processing": true,
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
            fixedHeader: true,
            "processing": true,
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
            initComplete: function(){
                this.api().columns().every(function(){
                    var that = this;
                    var i;
                    var val1;
                    $('#select').on("keyup change", function(){
                        i = $.fn.dataTable.util.escapeRegex(this.value);
                        console.log(i);
                        var val = $('#global_filter').val();
                        if(that.column(i).search() !== this.value){
                            that.column(this.value).search(val).draw();
                        }
                        val1 = $.fn.dataTable.util.escapeRegex(this.value);
                        $('#global_filter').on("keyup change clear",function(){
                            var val = $(this).val();
                            if(that.column(i).search() !== val1){
                                that.column(val1).search(val).draw();
                            }
                        });
                    });
                });
            }
        });
        /*table.columns().every(function(){
            var that = this;
            var i;
            var val1;
            $('#select').on("keyup change", function(){
                i = this.value;
                console.log(i);
                var val = $('#global_filter').val();
                if(that.column(i).search() !== this.value){
                    console.log(this.value);
                    that.column(this.value).search(val).draw();
                }
                val1 = this.value;
                $('#global_filter').on("keyup change clear",function(){
                    var val = $(this).val();
                    if(that.column(i).search() !== val1){
                        that.column(val1).search(val).draw();
                    }
                });
            });
        });*/
        //$('#verf1').hide();
        //$('#tablaEmpleado tbody #tdC').css('display', 'none');

        $("#tablaEmpleado tbody tr").hover(function () {
            //$('#verf1').css('display', 'block');
            $('#tablaEmpleado tbody #tdC').css('display', 'block');

        }, function () {

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