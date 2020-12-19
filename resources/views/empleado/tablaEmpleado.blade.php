<style>
    div.dataTables_wrapper div.dataTables_filter {
        display: none;
    }

    .table {
        width: 100% !important;
    }

    .dataTables_scrollHeadInner {
        margin: 0 auto !important;
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

    .hidetext {
        -webkit-text-security: disc;
    }

    .text-wrap {
        white-space: normal;
    }

    .width-400 {
        width: 120px !important;
    }

    .width-100 {
        width: 80px !important;
    }

    .dataTables_scrollBody {
        overflow: visible !important;
    }

    .alertify .ajs-body .ajs-content {
        padding: 16px 16px 16px 16px !important;
    }

    .ajs-body {
        font: 12.8px !important;
        padding: 0px !important;
        font-family: 'Roboto', sans-serif !important;
    }

    .alertify .ajs-footer {
        background: #ffffff;
        border-top: 1px solid #f6f6f7;
        border-radius: 0 0 4.8px 4.8px;
    }

    .alertify .ajs-footer .ajs-buttons .ajs-button {
        min-width: 88px;
        min-height: 35px;
        padding: 4px 8px 4px 8px;
    }

    /* RESPONSIVE */
    @media (max-width: 767.98px) {

        .dataTable,
        .dataTables_scrollHeadInner,
        .dataTables_scrollBody {
            margin: 0 auto !important;
            width: 100% !important;
        }

        table.dataTable>tbody>tr.child ul.dtr-details {
            display: flex !important;
            flex-flow: column !important;
        }

        .width-400 {
            width: 100% !important;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child,
        table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child {
            padding-left: 8% !important;
            width: 100% !important;
        }
    }

    @media (max-width: 406px) {

        table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child,
        table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child {
            padding-left: 8% !important;
            width: 100% !important;
        }
    }

    /* FINALIZACION DE RESPONSIVE */
</style>
{{-- MODAL DE CONTROL REMOTO --}}
<div id="modalControlR" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalControlR"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Activar dispositivo -
                    Modo Control Remoto
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:RefreshTablaEmpleado()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="empleadoControlR">
                <form class="form-horizontal text-center">
                    <h5 class="modal-title" id="myModalLabel" style="font-size:
                        15px"><img src="{{asset('admin/images/tick.svg')}}" height="25" class="mr-1">Activar
                        el computador de <span id="nombreECR"></span> y enviar un correo con sus credenciales</h5>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-10 text-right" style="padding-left: 50px">
                            <button type="button" class="btn btn-light btn-sm cancelar" data-dismiss="modal"
                                onclick="javascript:RefreshTablaEmpleado()">Cancelar</button>
                        </div>
                        <div class="col-md-2 text-right">
                            <button type="button" style="background-color: #163552;" class="btn btn-sm"
                                onclick="javascript:agregarControlR($('#empleadoControlR').val());">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
{{-- FINALIZACION DE MODAL --}}
{{-- MODAL DE CONTROL RUTA --}}
<div id="modalControlRT" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalControlRT"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Activar dispositivo -
                    Modo Control Ruta
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="javascript:RefreshTablaEmpleado()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="empleadoControlRT">
                <form class="form-horizontal text-center">
                    <h5 class="modal-title" id="myModalLabel" style="font-size:
                        15px"><img src="{{asset('admin/images/tick.svg')}}" height="25" class="mr-1">Activar
                        el celular de <span id="nombreECRT"></span> y enviar un sms con sus credenciales</h5>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-10 text-right" style="padding-left: 50px">
                            <button type="button" class="btn btn-light btn-sm cancelar" data-dismiss="modal"
                                onclick="javascript:RefreshTablaEmpleado()">Cancelar</button>
                        </div>
                        <div class="col-md-2 text-right">
                            <button type="button" style="background-color: #163552;" class="btn btn-sm"
                                onclick="javascript:agregarControlRT($('#empleadoControlRT').val());">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
{{-- FINALIZACION DE MODAL --}}
<input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">
<table id="tablaEmpleado" class="table nowrap" style="width:100%!important">
    <thead style="background: #edf0f1;color: #6c757d;" style="width:100%!important">
        <tr style="width:100%!important">
            <th class="text-center">&nbsp;<input type="checkbox" style="margin-left: 15px" id="selectT"></th>
            <th class="text-center"><label for="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></th>

            <th>
                <div class="width-100">Documento</div>
            </th>
            <th>
                <div class="width-100">Nombres</div>
            </th>
            <th>
                <div class="width-100">Apellidos</div>
            </th>
            <th>
                <div class="width-100">Control Remoto</div>
            </th>
            <th>
                <div class="width-100">Control Ruta</div>
            </th>
            <th>
                <div class="width-400">Asistencia en puerta</div>
            </th>
            <th>
                <div class="width-100">Cargo</div>
            </th>
            <th>
                <div class="width-100">Área</div>
            </th>
        </tr>
    </thead>
    <tbody style="background:#ffffff;color: #585858;font-size: 12.5px" id="tbodyr">
        @foreach ($tabla_empleado as $tabla_empleados)
        <tr id="{{$tabla_empleados->emple_id}}" value="{{$tabla_empleados->emple_id}}">
            {{-- CHECKBOX --}}
            <td class="text-center">
                <input type="checkbox" name="selec" id="tdC" style="margin-left:5.5px!important"
                    class="form-check-input sub_chk" data-id="{{$tabla_empleados->emple_id}}" $(this)$(this)$(this)>
            </td>
            {{-- EDITAR Y DAR DE BAJA --}}
            <td class="text-center">
                <a name="editarEName" onclick="editarEmpleado({{$tabla_empleados->emple_id}})" style="cursor: pointer">
                    <img src="{{asset('admin/images/edit.svg')}}" height="15">
                </a>
                &nbsp;
                <a data-toggle="tooltip" name="dBajaName" data-original-title="Dar de baja" data-placement="right"
                    onclick="marcareliminar({{$tabla_empleados->emple_id}})" style="cursor: pointer">
                    <img src="{{asset('landing/images/abajo.svg')}}" height="17">
                </a>
                &nbsp;
                <a class="verEmpleado" onclick="verDEmpleado({{$tabla_empleados->emple_id}})" data-toggle="tooltip"
                    data-placement="right" title="Ver Detalles" data-original-title="Ver Detalles"
                    style="cursor:pointer">
                    <img src="{{asset('landing/images/see.svg')}}" height="18">
                </a>
                <input type="hidden" id="codE" value="{{$tabla_empleados->emple_id}}">
            </td>
            {{-- NUMERO DE DOCUMENTO --}}
            <td>
                <div class="text-wrap width-100">{{$tabla_empleados->emple_nDoc}}</div>
            </td>
            {{-- NOMBRE --}}
            <td>
                <div class="text-wrap width-400">{{$tabla_empleados->perso_nombre}}</div>
            </td>
            {{-- APELLIDOS --}}
            <td>
                <div class="text-wrap width-400">{{$tabla_empleados->perso_apPaterno}}
                    {{$tabla_empleados->perso_apMaterno}}
                </div>
            </td>
            {{-- CONTROL REMOTO --}}
            @if(!in_array("1",$tabla_empleados->dispositivos))
            <td class="text-center">
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" class="custom-control-input"
                        id="customSwitchCR{{$tabla_empleados->emple_id}}"
                        onclick="javascript:controlRemoto({{$tabla_empleados->emple_id}},'{{$tabla_empleados->perso_nombre}}')">
                    <label class="custom-control-label" for="customSwitchCR{{$tabla_empleados->emple_id}}"
                        style="font-weight: bold">
                    </label>
                </div>
            </td>
            @else
            <td class="text-center">
                <div class="dropdown" id="w{{$tabla_empleados->emple_id}}">
                    <a class="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        style="cursor: pointer">
                        <div class="custom-control custom-switch mb-2">
                            @if($tabla_empleados->estadoCR == true)
                            <input type="checkbox" class="custom-control-input"
                                id="customSwitchCRW{{$tabla_empleados->emple_id}}" checked>
                            @else
                            <input type="checkbox" class="custom-control-input"
                                id="customSwitchCRW{{$tabla_empleados->emple_id}}">
                            @endif
                            <label class="custom-control-label" for="customSwitchCRW{{$tabla_empleados->emple_id}}"
                                style="font-weight: bold"></label>
                        </div>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @foreach($tabla_empleados->vinculacion as $tablaV)
                        @if($tablaV["dispositivoD"] == "WINDOWS")
                        <div class="dropdown-item">
                            @if($tablaV['disponible'] == 'c' || $tablaV['disponible'] == 'e' || $tablaV['disponible'] ==
                            'a')
                            <div class="custom-control custom-switch mb-2">
                                @if(empty($tablaV['pc']) === true)
                                <input type="checkbox" class="custom-control-input"
                                    id="customSwitchCRDisp{{$tablaV['idVinculacion']}}" checked
                                    onclick="javasscript:estadoDispositivoCR({{$tabla_empleados->emple_id}},{{$tablaV['idVinculacion']}},'PC {{$loop->index}}','{{$tabla_empleados->perso_nombre}}')">
                                <label class="custom-control-label" for="customSwitchCRDisp{{$tablaV['idVinculacion']}}"
                                    style="font-weight: bold">PC{{$loop->index}}</label>
                                @else
                                <input type="checkbox" class="custom-control-input"
                                    id="customSwitchCRDisp{{$tablaV['idVinculacion']}}" checked
                                    onclick="javasscript:estadoDispositivoCR({{$tabla_empleados->emple_id}},{{$tablaV['idVinculacion']}},'{{$tablaV['pc']}}','{{$tabla_empleados->perso_nombre}}')">
                                <label class="custom-control-label" for="customSwitchCRDisp{{$tablaV['idVinculacion']}}"
                                    style="font-weight: bold">{{$tablaV['pc']}}</label>
                                @endif
                            </div>
                            @else
                            <div class="custom-control custom-switch mb-2">
                                @if(empty($tablaV['pc']) === true)
                                <input type="checkbox" class="custom-control-input"
                                    id="customSwitchCRDisp{{$tablaV['idVinculacion']}}"
                                    onclick="javasscript:estadoDispositivoCR({{$tabla_empleados->emple_id}},{{$tablaV['idVinculacion']}},'PC {{$loop->index}}','{{$tabla_empleados->perso_nombre}}')">
                                <label class="custom-control-label" for="customSwitchCRDisp{{$tablaV['idVinculacion']}}"
                                    style="font-weight: bold">PC{{$loop->index}}</label>
                                @else
                                <input type="checkbox" class="custom-control-input"
                                    id="customSwitchCRDisp{{$tablaV['idVinculacion']}}"
                                    onclick="javasscript:estadoDispositivoCR({{$tabla_empleados->emple_id}},{{$tablaV['idVinculacion']}},'{{$tablaV['pc']}}','{{$tabla_empleados->perso_nombre}}')">
                                <label class="custom-control-label" for="customSwitchCRDisp{{$tablaV['idVinculacion']}}"
                                    style="font-weight: bold">{{$tablaV['pc']}}</label>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endif
                        @endforeach
                    </ul>
                </div>
            </td>
            @endif
            {{-- CONTROL RUTA --}}
            @if(!in_array("2",$tabla_empleados->dispositivos))
            <td class="text-center">
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" class="custom-control-input"
                        id="customSwitchCRT{{$tabla_empleados->emple_id}}"
                        onclick="javascript:controlRuta({{$tabla_empleados->emple_id}},'{{$tabla_empleados->perso_nombre}}')">
                    <label class="custom-control-label" for="customSwitchCRT{{$tabla_empleados->emple_id}}"
                        style="font-weight: bold">
                    </label>
                </div>
            </td>
            @else
            <td class="text-center">
                <div class="dropdown" id="a{{$tabla_empleados->emple_id}}">
                    <a class="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        style="cursor: pointer">
                        <div class="custom-control custom-switch mb-2">
                            {{-- ESTADO DE CONTROL EN RUTA --}}
                            @if($tabla_empleados->estadoCRT === true)
                            <input type="checkbox" class="custom-control-input"
                                id="customSwitchCRA{{$tabla_empleados->emple_id}}" checked>
                            @else
                            <input type="checkbox" class="custom-control-input"
                                id="customSwitchCRA{{$tabla_empleados->emple_id}}">
                            @endif
                            <label class="custom-control-label" for="customSwitchCRA{{$tabla_empleados->emple_id}}"
                                style="font-weight: bold">
                            </label>
                        </div>
                    </a>
                    {{-- MENU DE ANDROID --}}
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @foreach ($tabla_empleados->vinculacionRuta as $tablaVR)
                        @if ($tablaVR["dispositivoD"] == "ANDROID")
                        <div class="dropdown-item">
                            @if ($tablaVR['disponible'] == 'c' || $tablaVR['disponible'] == 'e' ||
                            $tablaVR['disponible'] ==
                            'a')
                            <div class="custom-control custom-switch mb-2">
                                @if (empty($tablaVR['modelo']) == true)
                                <input type="checkbox" class="custom-control-input"
                                    id="customSwitchCRTDisp{{$tablaVR['idVinculacion']}}" checked
                                    onclick="javascript:estadoDispositivoCRT({{$tabla_empleados->emple_id}},{{$tablaVR['idVinculacion']}},'CEL {{$loop->index}}','{{$tabla_empleados->perso_nombre}}')">
                                <label class="custom-control-label"
                                    for="customSwitchCRTDisp{{$tablaVR['idVinculacion']}}" style="font-weight: bold">
                                    CEL {{$loop->index}}
                                </label>
                                @else
                                <input type="checkbox" class="custom-control-input"
                                    id="customSwitchCRTDisp{{$tablaVR['idVinculacion']}}" checked
                                    onclick="javascript:estadoDispositivoCRT({{$tabla_empleados->emple_id}},{{$tablaVR['idVinculacion']}},'CEL {{$loop->index}}','{{$tabla_empleados->perso_nombre}}')">
                                <label class="custom-control-label"
                                    for="customSwitchCRTDisp{{$tablaVR['idVinculacion']}}" style="font-weight: bold">
                                    {{$tablaVR['modelo']}}
                                </label>
                                @endif
                            </div>
                            @else
                            <div class="custom-control custom-switch mb-2">
                                @if (empty($tablaVR['modelo']) == true)
                                <input type="checkbox" class="custom-control-input"
                                    id="customSwitchCRTDisp{{$tablaVR['idVinculacion']}}"
                                    onclick="javascript:estadoDispositivoCRT({{$tabla_empleados->emple_id}},{{$tablaVR['idVinculacion']}},'CEL {{$loop->index}}','{{$tabla_empleados->perso_nombre}}')">
                                <label class="custom-control-label"
                                    for="customSwitchCRTDisp{{$tablaVR['idVinculacion']}}" style="font-weight: bold">
                                    CEL {{$loop->index}}
                                </label>
                                @else
                                <input type="checkbox" class="custom-control-input"
                                    id="customSwitchCRTDisp{{$tablaVR['idVinculacion']}}"
                                    onclick="javascript:estadoDispositivoCRT({{$tabla_empleados->emple_id}},{{$tablaVR['idVinculacion']}},'CEL {{$loop->index}}','{{$tabla_empleados->perso_nombre}}')">
                                <label class="custom-control-label"
                                    for="customSwitchCRTDisp{{$tablaVR['idVinculacion']}}" style="font-weight: bold">
                                    {{$tablaVR['modelo']}}
                                </label>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endif
                        @endforeach
                    </ul>
                    {{-- FINALIZACION --}}
                </div>
            </td>
            @endif
            {{-- CONTROL ASISTENCIA EN PUERTA --}}
            @if($tabla_empleados->asistencia_puerta==1)
            <td class="text-center">
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" class="custom-control-input"
                        id="customSwitchCP{{$tabla_empleados->emple_id}}"
                        onclick="controlPuerta({{$tabla_empleados->emple_id}})" checked>
                    <label class="custom-control-label" for="customSwitchCP{{$tabla_empleados->emple_id}}"
                        style="font-weight: bold"></label>
                </div>
            </td>
            @else
            <td class="text-center">
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" class="custom-control-input"
                        id="customSwitchCP{{$tabla_empleados->emple_id}}"
                        onclick="controlPuerta({{$tabla_empleados->emple_id}})">
                    <label class="custom-control-label" for="customSwitchCP{{$tabla_empleados->emple_id}}"
                        style="font-weight: bold"></label>
                </div>
            </td>
            @endif

            <td>
                <div class="text-wrap width-400">{{$tabla_empleados->cargo_descripcion}}</div>
            </td>
            <td>
                <div class="text-wrap width-400">{{$tabla_empleados->area_descripcion}}</div>
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
    var CheckBoxs = table.find('tbody input:checkbox[name=selec]');
    var CheckBoxMarcados = 0;

    seleccionarTodos.on('click', function () {
    if (seleccionarTodos.is(":checked")) {
    CheckBoxs.prop('checked', true);
    } else {
    CheckBoxs.prop('checked', false);
    }
    });
    CheckBoxs.on('change', function (e) {
    CheckBoxMarcados = table.find('tbody input:checkbox[name=selec]:checked').length;
    seleccionarTodos.prop('checked', (CheckBoxMarcados === CheckBoxs.length));
    });
</script>
<script>
    function editarEmpleado(idempleado){
    $('#form-ver').modal();
    $( "#detallehorario_ed" ).empty();
    $( "#editar_tbodyHistorial" ).empty();
    $('#smartwizard1').smartWizard("reset");
    $('#MostrarCa_e').hide();
    $('#calendarInv_ed').hide();
    $('#divescond1').hide();
    $('#divescond2').hide();
    $('#calendar_ed').hide();
    $('#detalleContratoE').hide();
    var value = idempleado;
    $('#selectCalendario_ed').val("Asignar calendario");

    $('#idempleado').val(value);
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
                $('#v_tipoDoc').val(data[0].tipoDoc_descripcion);
                $('#v_apPaterno').val(data[0].perso_apPaterno);
                $('#v_departamento').val(data[0].iddepaN);

                if(data[0].iddepaN != null){
                    onSelectVDepartamento('#v_departamento').then(function () {
                        $('#v_provincia').val(data[0].idproviN);
                        onSelectVProvincia('#v_provincia').then((result) => $('#v_distrito')
                            .val(data[0].iddistN))
                    });
                }
                if(data[0].iddepaN == null){
                    if(data[0].iddistN!= null){
                $('#v_distrito').append($('<option>', {
                text : data[0].distN,
                selected:true,
                value:data[0].iddistN
                  }));}
                }

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
                        $('#divescond1').show();
                        $('#divescond2').show();
                       $('#detallehorario_ed2').empty();
                       /*  $("#detallehorario_ed2").append("<div class='form-group row'><div class='col-md-1'></div><label class='col-lg-4 col-form-label' style='color:#163552;margin-top: 5px;'> </label>" +
                "<div class='col-md-3'></div>"+
                "<div class='col-md-3' style='' ><div class='btn-group mt-2 mr-1'> <button type='button' onclick='eliminarhorariosBD()' class='btn btn-primary btn-sm dropdown-toggle' style='color: #fff; background-color: #4a5669;"+
                "border-color: #485263;' > <img src='admin/images/borrador.svg' height='15'>"+
                " Borrar </button> </div></div></div>"); */
                    }
                },
                error: function () {}
            });
                $('#v_dep').val(data[0].depar);
                if(data[0].depar != null){
                    onSelectVDepart('#v_dep').then(function () {
                        $('#v_prov').val(data[0].proviId);
                        onSelectVProv('#v_prov').then((result) => $('#v_dist').val(data[0]
                            .distId))
                    });
                }
                if(data[0].depar == null){
                    if(data[0].distId!= null){
                $('#v_dist').append($('<option>', {
                text : data[0].distNo,
                selected:true,
                value:data[0].distId
                  }));}
                }
                $('#selectCalendario_edit3').val(data[0].idcalendar);
                $('#idselect3').val(data[0].idcalendar);
                $('#v_numDocumento').val(data[0].emple_nDoc);
                $('#v_apMaterno').val(data[0].perso_apMaterno);
                $("[name=v_tipo]").val([data[0].perso_sexo]);
                ////////////////////////////////////////////////
               var VFechaDa=moment(data[0].perso_fechaNacimiento).format('YYYY-MM-DD');
               var VFechaDia = new Date(moment(VFechaDa));
                $('#v_dia_fecha').val(VFechaDia.getDate());
                $('#v_mes_fecha').val(moment(VFechaDa).month()+1);
                $('#v_ano_fecha').val(moment(VFechaDa).year());
               //////////////////////////////////////////////
                $('#v_nombres').val(data[0].perso_nombre);
                $('#v_direccion').val(data[0].perso_direccion);
                $('#v_cargo').val(data[0].cargo_id);
                $('#v_area').val(data[0].area_id);
                $('#v_centroc').val(data[0].centroC_id);
                id_empleado = data[0].emple_id;
                $('#v_id').val(data[0].emple_id);
                $('#v_nivel').val(data[0].emple_nivel);
                $('#v_local').val(data[0].emple_local);
                $('#v_codigoCelular').val("+51");
                $('#v_celular').val(data[0].emple_celular);
                if(data[0].emple_celular != '' ){
                    celularSplit = data[0].emple_celular.split("+51");
                    $('#v_celular').val(celularSplit[1]);
                }
                $('#v_codigoTelefono').val("01");
                $('#v_telefono').val(data[0].emple_telefono);
                if(data[0].emple_telefono != ''){
                    telefonoSplit = data[0].emple_telefono.split("");
                    $('#v_codigoTelefono').val(telefonoSplit[0] + telefonoSplit[1]);
                    $('#v_telefono').val(telefonoSplit[2] + telefonoSplit[3] + telefonoSplit[4] + telefonoSplit[5] + telefonoSplit[6] + telefonoSplit[7]);
                }
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

            },
            error: function () {}
        });
}
//////////////////////verdatos
function verDEmpleado(idempleadoVer){
    $('#verEmpleadoDetalles').modal();
    $( "#detallehorario_ed" ).empty();
    $( "#ver_tbodyHistorial" ).empty();
        $('#smartwizard1').smartWizard("reset");
        $('#smartwizardVer').smartWizard("reset");
        $('#MostrarCa_e').hide();
        $('#calendarInv_ed').hide();
        $('#divescond1').hide();
        $('#divescond1_ver').hide();
        $('#divescond2').hide();
        $('#calendar_ed').hide();
        $('#h5Ocultar').show();
        $('#v_fotoV').attr("src", "landing/images/png.svg");
        //$(this).addClass('selected').siblings().removeClass('selected');
        var value = idempleadoVer
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

                calendario3();
                calendario4();
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
                        $('#divescond1').show();
                        $('#divescond1_ver').show();
                        $('#divescond2').show();
                       $('#detallehorario_ed2').empty();
                    }
                },
                error: function () {}
            });
            $.ajax({
                type:"POST",
                url: "/empleado/historial",
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
                    var containerVer = $('#ver_tbodyHistorial');
            for (var i = 0; i < data.length; i++) {

                    var trVer = '<tr>';


                        if(data[i].fecha_historial!=null){
                            if(data[i].tipo_Hist==1){
                                trVer+=  '<td style="vertical-align:middle;"><img src="landing/images/arriba.svg" height="17"> &nbsp;'+moment(data[i].fecha_historial).format('DD/MM/YYYY')+'</td>';
                            } else
                            {
                                trVer+=  '<td style="vertical-align:middle;"><img src="landing/images/abajo.svg" height="17"> &nbsp;'+ moment(data[i].fecha_historial).format('DD/MM/YYYY') +'</td>';
                            }
                        } else{
                            trVer+=  '<td>--</td>';
                        }

                         if(data[i].rutaDocumento!=null){

                            var valores=data[i].rutaDocumento;
                            idsV=valores.split(',');
                            var variableResult=[];
                            trVer+=  '<td><div class="row">';
                            $.each( idsV, function( index, value ){
                                trVer+=
                                '<div class="col-xl-6 col-md-6" style="padding-left: 0px;">'+
                                    '<div class="p-2 border rounded" style="padding-top: 1px!important; padding-bottom: 1px!important;">'+
                                         '<div class="media">'+
                                            '<div class="avatar-sm font-weight-bold mr-3">'+
                                                '<span class="avatar-title rounded bg-soft-primary text-primary">'+
                                                 '<i class="uil-file-plus-alt font-size-18"></i>'+
                                               ' </span>'+
                                            '</div>'+
                                            '<div class="media-body">'+
                                                '<a href="documEmpleado/'+value+'" target="_blank" class="d-inline-block mt-2">'+value+'</a>'+
                                             '</div>'+
                                             '<div class="float-right mt-1">'+
                                                '<a href="documEmpleado/'+value+'" target="_blank" class="p-2"><i class="uil-download-alt font-size-18"></i></a>'+
                                            '</div>'+
                                         '</div>'+
                                     '</div>'+
                                '</div>';

                                /* variableResult.push(variableResult1); */

                            })

                            /*   trVer+=variableResult; */

                            trVer+=  '</div></td>';
                        } else{
                            trVer+=  '<td>--</td>';
                        }


                            trVer+= '</tr>';

                containerVer.append(trVer);

            }
                },
                error: function () {}
            });
                $('#selectCalendario_edit3_ver').val(data[0].idcalendar);
                $('#idselect3').val(data[0].idcalendar);
                calendario_edit();
                calendario2_ed();
                //VER
                $('#v_tipoDocV').val(data[0].tipoDoc_descripcion);
                $('#v_apPaternoV').val(data[0].perso_apPaterno);
                $('#v_direccionV').val(data[0].perso_direccion);
                $('#v_idV').val(data[0].emple_id);

                //////////////////////////////////////////////////////////////
                var VFechaDaVer=moment(data[0].perso_fechaNacimiento).format('YYYY-MM-DD');
                var VFechaDiaVer = new Date(moment(VFechaDaVer));
                $('#v_dia_fechaV').val(VFechaDiaVer.getDate());
                $('#v_mes_fechaV').val(moment(VFechaDaVer).month()+1);
                $('#v_ano_fechaV').val(moment(VFechaDaVer).year());
                /////////////////////////////////////////////////////////////////
                $('#v_apMaternoV').val(data[0].perso_apMaterno);
                $('#v_numDocumentoV').val(data[0].emple_nDoc);
                $('#v_emailV').val(data[0].emple_Correo);
                $('#v_celularV').val(data[0].emple_celular);
                $('#v_nombresV').val(data[0].perso_nombre);
                $('#v_telefonoV').val(data[0].emple_telefono);
                $('#v_depV').val(data[0].deparNo);
                $('#v_departamentoV').val(data[0].depaN);
                $("[name=v_tipoV]").val([data[0].perso_sexo]);
                $('#v_provV').val(data[0].provi);
                $('#v_provinciaV').val(data[0].proviN);
                $('#v_distV').val(data[0].distNo)
                $('#v_distritoV').val(data[0].distN)
                $('#v_cargoV').val(data[0].cargo_descripcion);
                $('#v_areaV').val(data[0].area_descripcion);
                $('#v_centrocV').val(data[0].centroC_descripcion);
                $('#v_nivelV').val(data[0].nivel_descripcion);
                $('#v_localV').val(data[0].local_descripcion);
                $('#v_codigoEmpleadoV').val(data[0].emple_codigo);
                if(data[0].foto != ''){
                    $('#v_fotoV').attr("src", "fotosEmpleado/" + data[0].foto);
                    $('#h5Ocultar').hide();
                }
                $('#detalleContratoVer').hide();
                if(data[0].contrato.length >= 1){
                    $('#detalleContratoVer').show();
                    $('#v_contratoV').val(data[0].contrato[0].contrato_descripcion);
                    $('#v_idContratoV').val(data[0].contrato[0].idC);
                    $('#v_montoV').val(data[0].contrato[0].monto);
                    $('#v_condicionV').val(data[0].contrato[0].idCond);
                    var VFechaDaIE=moment(data[0].contrato[0].fechaInicio).format('YYYY-MM-DD');
                    var VFechaDiaIE = new Date(moment(VFechaDaIE));
                    $('#m_dia_fechaIEV').val(VFechaDiaIE.getDate());
                    $('#m_mes_fechaIEV').val(moment(VFechaDaIE).month()+1);
                    $('#m_ano_fechaIEV').val(moment(VFechaDaIE).year());
                        if (data[0].contrato[0].fechaFinal == null || data[0].contrato[0].fechaFinal == "0000-00-00") {
                            $("#checkboxFechaIEV").prop('checked', true);
                            $('#ocultarFechaEV').hide();
                        }
                    var VFechaDaFE=moment(data[0].contrato[0].fechaFinal ).format('YYYY-MM-DD');
                    var VFechaDiaFE = new Date(moment(VFechaDaFE));
                    $('#m_dia_fechaFEV').val(VFechaDiaFE.getDate());
                    $('#m_mes_fechaFEV').val(moment(VFechaDaFE).month()+1);
                    $('#m_ano_fechaFEV').val(moment(VFechaDaFE).year());
                }
                $('#ver_tbodyDispositivo').css('pointer-events', 'none');
                $("#formContratoVer :input").prop('disabled', true);
            },
            error: function () {}
        });
}
</script>
<script>
    function filterGlobal() {
        $('#tablaEmpleado').DataTable().search(
            $('#global_filter').val(),

        ).draw();
    }
    $(document).ready(function () {

        var table = $("#tablaEmpleado").DataTable({
            scrollX: true,
            responsive:true,
            retrieve: true,
            "searching": true,
            "lengthChange": false,
            scrollCollapse : false,
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
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 4 },
            ],
            "autoWidth": false,
            initComplete: function(){
                this.api().columns().every(function(){
                    var that = this;
                    var i;
                    var val1;
                    $('#select').on("keyup change", function(){
                        i = $.fn.dataTable.util.escapeRegex(this.value);

                        var val = $('#global_filter').val();
                        if(that.column(i).search() !== this.value){
                            that.column(this.value).search( "^" + val, true, false, true).draw();
                        }
                        val1 = $.fn.dataTable.util.escapeRegex(this.value);
                        $('#global_filter').on("keyup change clear",function(){
                            var val = $(this).val();
                            if(that.column(i).search() !== val1){
                                that.column(val1).search("^" + val, true, false, true).draw();
                            }
                        });
                    });
                });
            }
        });
        $(window).on('resize', function() {
            $('#example').css('width', '100%');
            table.draw(true);
        });
        $('#tablaEmpleado').on('shown.bs.collapse', function () {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
        $('#tablaEmpleado tbody #tdC').css('display', 'block');

        $('input.global_filter').on('keyup click', function () {
            filterGlobal();
        });

        $('input.column_filter').on('keyup click', function () {
            filterColumn($(this).parents('div').attr('data-column'));
        });

        //* SELECT DEFECTO PARA BUSQUEDA
        $('#select').val(4).trigger('change');
    });
</script>
{{-- ELIMINACION --}}
<script>
    // * ABRIR MODAL DE BAJA
    function marcareliminar(data) {
        $('input:checkbox[data-id=' + data + ']').prop('checked', true);
        $('#modalEliminar').modal();
        //* INICIALIZAR FECHA DE BAJA
        var fechaValue = $("#fechaSelectBaja").flatpickr({
            mode: "single",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "Y-m-d",
            locale: "es",
            maxDate: "today",
            wrap: true,
            allowInput: true,
            disableMobile: "true"
        });
        $(function () {
            f = moment().format("YYYY-MM-DD");
            fechaValue.setDate(f);
        });
        //* ***********************************
        //* VALOR A INPUT HIDDEN
        $('#empleadoEliminacion').val(data);
    }
    //* VALIDACION DE ARCHIVOS EN NUEVA BAJA
    async function validArchivosBajaTabla() {
        var respuesta = true;
        $.each($('#bajaFile'), function (i, obj) {
            $.each(obj.files, function (j, file) {
                var fileSize = file.size;
                var sizeKiloBytes = parseInt(fileSize);
                if (sizeKiloBytes > parseInt($('#bajaFile').attr('size'))) {
                    respuesta = false;
                }
            });
        });
        return respuesta;
    }
    $('#bajaFile').on("click",function(){
        $('#validArchivosBaja').hide();
    });
    // * FUNCION DE BAJA
    async function confirmarBaja(){
        //* FUNCIONES DE VALIDAR ARCHIVO
        const result = await validArchivosBajaTabla();
        console.log(result);
        if (!result) {
            $('#validArchivosBaja').show();
            return false;
        } else {
            $('#validArchivosBaja').hide();
        }
        var idEmpleado = $('#empleadoEliminacion').val();
        var fechaBaja = $('#fechaInputBaja').val();
        $.ajax({
            url: "/eliminarEmpleado",
            type: 'POST',
            data: {
                idEmpleado:idEmpleado,
                fechaBaja: fechaBaja
            },
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
                if(data != 0){
                    archivosDeBajaTabla(data);
                    RefreshTablaEmpleado();
                    $('#modalEliminar').modal('toggle');
                }else{
                    $('#modalEliminar').modal('toggle');
                    alertify
                    .confirm(
                        "Para poder dar de baja aun empleado, debe tener por lo menos un historial de contrao.",
                        function (e) {
                            if (e) {
                                editarEmpleado(idEmpleado);
                                $('#smartwizard1').smartWizard("next");
                                $('#smartwizard1').smartWizard("next");
                            }
                        }
                    )
                    .setting({
                        title: "Dar baja",
                        labels: {
                            ok: "Aceptar",
                            cancel: "Cancelar",
                        },
                        modal: true,
                        startMaximized: false,
                        reverseButtons: true,
                        resizable: false,
                        closable: false,
                        transition: "zoom",
                        oncancel: function (closeEvent) {
                            RefreshTablaEmpleado();
                        },
                    });
                }
            },
            error: function(){}
        });
    }
    function archivosDeBajaTabla(id){
        //* AJAX DE ARCHICOS
        var formData = new FormData();
        $.each($('#bajaFile'), function (i, obj) {
            $.each(obj.files, function (j, file) {
                formData.append('file[' + j + ']', file);
            })
        });
        $.ajax({
            contentType: false,
            processData: false,
            type: "POST",
            url: "/empleado/storeDocumentoBaja/" + id,
            data: formData,
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                $('#bajaFile').val(null);
                $('.iborrainputfile').text("Seleccionar archivo");
                RefreshTablaEmpleado();
            },
            error: function () {
            },
        });
    }
</script>
{{-- CORREO MASIVO--}}
<script>
    function CorreosMasivos() {
        var correoEmpleado = [];
        $(".sub_chk:checked").each(function () {
            correoEmpleado.push($(this).attr('data-id'));
        });

        var join_selected_values = correoEmpleado.join(",");
        $.ajax({
            async: false,
            type: "get",
            url: "envioMasivoW",
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

                for (var i = 0; i < data.length; i++) {
                    if (data[i].Correo == true && data[i].Vinculacion == true) {
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
                        if (data[i].Vinculacion!= true) {
                            $.notify({
                                message: data[i].Persona.perso_nombre + " " + data[i].Persona
                                    .perso_apPaterno + " " + data[i].Persona.perso_apMaterno +
                                    "\naún no tiene dispositivo Windows asignado.",
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
                RefreshTablaEmpleado();
            }
        });
    }
    $('#enviarCorreoM').on("click", CorreosMasivos);

</script>
{{-- ANDROID MASIVO--}}
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.min.js')}}"></script>
<script src="{{URL::asset('admin/assets/libs/bootstrap-notify-master/bootstrap-notify.js')}}"></script>
<script src="{{asset('landing/js/correoEmpleados.js')}}"></script>
<script src="{{asset('landing/js/correoAndroid.js')}}"></script>