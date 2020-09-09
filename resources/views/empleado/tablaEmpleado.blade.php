<style>
    div.dataTables_wrapper div.dataTables_filter {
        display: none;
    }

    .table {
        width: 100% !important;
    }

    .dataTables_scrollHeadInner {
        margin: 0 auto !important;
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
        /* Default */
    }

    .text-wrap {
        white-space: normal;
    }

    .width-400 {
        width: 150px !important;
    }

    .table-responsive,
    .dataTables_scrollBody {
        overflow: visible !important;
    }
</style>
<div id="modalControlR" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalControlR"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#163552;">
                <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Registrar Dispositivo
                    para Modo Control Remoto
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="empleadoControlR">
                <form class="form-horizontal">
                    <h5 class="modal-title" id="myModalLabel" style="font-size:
                        15px">
                        <img src="{{asset('admin/images/tick.svg')}}" height="25" class="mr-1 mt-2">
                        Se registrara un dispositivo para control remoto y enviaremos un correo electronico al
                        empleado con dicha información.</h5>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7 text-right">
                            <button type="button" class="btn btn-light btn-sm cancelar" data-dismiss="modal"
                                onclick="javascript:RefreshTablaEmpleado()">Cancelar</button>
                        </div>
                        <div class="col-md-5 text-right" style="padding-right:
                            38px;">
                            <button type="button" style="background-color: #163552;" class="btn btn-sm"
                                onclick="javascript:agregarControlR($('#empleadoControlR').val());">Enviar</button>
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
                <option value="3">Número Documento</option>
                <option value="4">Nombre</option>
                <option value="5">Apellidos</option>
                <option value="8">Cargo</option>
                <option value="9">Área</option>
            </select>
        </td>
    </div>
</div>
<table id="tablaEmpleado" class="table table-drop dt-responsive nowrap" style="width:100%!important">
    <thead style="background: #edf0f1;color: #6c757d;">
        <tr style="background: #ffffff">
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
            <th style="border-top: 1px solid #fdfdfd;"></th>
        </tr>
        <tr>
            <th class="text-center">&nbsp;<input type="checkbox" class="ml-4" name="" id="selectT"></th>
            <th class="text-center"></th>
            <th class="text-center"></th>
            <th class="text-center">Número Documento</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Control Remoto</th>
            <th class="text-center">Asistencia en puerta</th>
            <th>Cargo</th>
            <th>Área</th>
        </tr>
    </thead>
    <tbody style="background:#ffffff;color: #585858;font-size: 12.5px" id="tbodyr">
        @foreach ($tabla_empleado as $tabla_empleados)
        <tr id="{{$tabla_empleados->emple_id}}" value="{{$tabla_empleados->emple_id}}">
            <td class="text-center"><input type="checkbox" name="selec" id="tdC" style="margin-left:5.5px!important"
                    class="form-check-input sub_chk" data-id="{{$tabla_empleados->emple_id}}" $(this)$(this)$(this)>
            </td>
            <td class="text-center"><a id="formNuevoEd" onclick="editarEmpleado({{$tabla_empleados->emple_id}})"
                    style="cursor: pointer"><img src="{{asset('admin/images/edit.svg')}}"
                        height="15"></a>&nbsp;&nbsp;&nbsp;<a onclick="marcareliminar({{$tabla_empleados->emple_id}})"
                    style="cursor: pointer"><img src="{{asset('admin/images/delete.svg')}}" height="15"></a>&nbsp;&nbsp;
                <a class="verEmpleado" onclick="verDEmpleado({{$tabla_empleados->emple_id}})" data-toggle="tooltip"
                    data-placement="right" title="Ver Detalles" data-original-title="Ver Detalles" style="cursor:
                    pointer">
                    <img src="{{asset('landing/images/see.svg')}}" height="18">
                </a>
            </td>
            <td class="text-center">&nbsp; <input type="hidden" id="codE" value="{{$tabla_empleados->emple_id}}"><img
                    src="{{ URL::asset('admin/assets/images/users/empleado.png')
                    }}" alt="" /></td>
            <td class="text-center">
                <div class="text-wrap width-400">{{$tabla_empleados->emple_nDoc}}</div>
            </td>
            <td>
                <div class="text-wrap width-400">{{$tabla_empleados->perso_nombre}}</div>
            </td>
            <td>
                <div class="text-wrap width-400">{{$tabla_empleados->perso_apPaterno}}
                    {{$tabla_empleados->perso_apMaterno}}
                </div>
            </td>
            @if(!in_array("1",$tabla_empleados->dispositivos))
            <td class="text-center">
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" class="custom-control-input"
                        id="customSwitchCR{{$tabla_empleados->emple_id}}"
                        onchange="javascript:controlRemoto({{$tabla_empleados->emple_id}})">
                    <label class="custom-control-label" for="customSwitchCR{{$tabla_empleados->emple_id}}"
                        style="font-weight: bold"></label>
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
                                <input type="checkbox" class="custom-control-input"
                                    id="customSwitchCRDisp{{$tablaV['idVinculacion']}}" checked
                                    onclick="javasscript:estadoDispositivoCR({{$tablaV['idVinculacion']}},{{$loop->index}})">
                                <label class="custom-control-label" for="customSwitchCRDisp{{$tablaV['idVinculacion']}}"
                                    style="font-weight: bold">PC{{$loop->index}}</label>
                            </div>
                            @else
                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input"
                                    id="customSwitchCRDisp{{$tablaV['idVinculacion']}}">
                                <label class="custom-control-label" for="customSwitchCRDisp{{$tablaV['idVinculacion']}}"
                                    style="font-weight: bold">PC{{$loop->index}}</label>
                            </div>
                            @endif
                            {{-- onclick="javascript:enviarWindowsTabla({{$tabla_empleados->emple_id}},{{$tablaV['idVinculacion']}})">PC
                            {{$loop->index}} --}}
                        </div>
                        @endif
                        @endforeach
                    </ul>
                </div>
            </td>
            @endif
            @if(!in_array("2",$tabla_empleados->dispositivos))
            <td class="text-center">
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" class="custom-control-input"
                        id="customSwitchCP{{$tabla_empleados->emple_id}}">
                    <label class="custom-control-label" for="customSwitchCP{{$tabla_empleados->emple_id}}"
                        style="font-weight: bold"></label>
                </div>
            </td>
            @else
            <td class="text-center">
                <div class="dropdown" id="a{{$tabla_empleados->emple_id}}">
                    <a class="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        style="cursor: pointer">
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input"
                                id="customSwitchCRP{{$tabla_empleados->emple_id}}">
                            <label class="custom-control-label" for="customSwitchCRP{{$tabla_empleados->emple_id}}"
                                style="font-weight: bold"></label>
                        </div>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @foreach($tabla_empleados->vinculacion as $tablaV)
                        @if($tablaV["dispositivoD"] == "ANDROID")
                        <a class="dropdown-item"
                            onclick="enviarAndroidTabla({{$tabla_empleados->emple_id}},{{$tablaV['idVinculacion']}})">ANDROID</a>
                        @endif
                        @endforeach
                    </ul>
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
        };

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
                console.log(data[0].iddepaN);
                if(data[0].iddepaN != null){
                    onSelectVDepartamento('#v_departamento').then(function () {
                        $('#v_provincia').val(data[0].idproviN);
                        onSelectVProvincia('#v_provincia').then((result) => $('#v_distrito')
                            .val(data[0].iddistN))
                    });
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
                        $("#detallehorario_ed2").append("<div class='form-group row'><div class='col-md-1'></div><label class='col-lg-4 col-form-label' style='color:#163552;margin-top: 5px;'>Se muestra calendario de empleado </label>" +
                "<div class='col-md-3'></div>"+
                "<div class='col-md-3' ><div class='btn-group mt-2 mr-1'> <button type='button' onclick='eliminarhorariosBD()' class='btn btn-primary btn-sm dropdown-toggle' style='color: #fff; background-color: #4a5669;"+
                "border-color: #485263;' > <img src='admin/images/borrador.svg' height='15'>"+
                " Borrar horarios </button> </div></div></div>");
                    }
                },
                error: function () {}
            });
                $('#v_dep').val(data[0].depar);

                console.log(data[0].depar);
                if(data[0].depar != null){
                    onSelectVDepart('#v_dep').then(function () {
                        $('#v_prov').val(data[0].proviId);
                        onSelectVProv('#v_prov').then((result) => $('#v_dist').val(data[0]
                            .distId))
                    });
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
                if(data[0].contrato.length >= 1){
                    $('#detalleContratoE').show();
                    $('#v_contrato').val(data[0].contrato[0].idTipoC);
                    $('#v_idContrato').val(data[0].contrato[0].idC);
                    $('#v_monto').val(data[0].contrato[0].monto);
                    $('#v_condicion').val(data[0].contrato[0].idCond);
                    var VFechaDaIE=moment(data[0].contrato[0].fechaInicio).format('YYYY-MM-DD');
                    var VFechaDiaIE = new Date(moment(VFechaDaIE));
                    $('#m_dia_fechaIE').val(VFechaDiaIE.getDate());
                    $('#m_mes_fechaIE').val(moment(VFechaDaIE).month()+1);
                    $('#m_ano_fechaIE').val(moment(VFechaDaIE).year());
                        if (data[0].contrato[0].fechaFinal == null || data[0].contrato[0].fechaFinal == "0000-00-00") {
                            $("#checkboxFechaIE").prop('checked', true);
                            $('#ocultarFechaE').hide();
                        }
                    var VFechaDaFE=moment(data[0].contrato[0].fechaFinal ).format('YYYY-MM-DD');
                    var VFechaDiaFE = new Date(moment(VFechaDaFE));
                    $('#m_dia_fechaFE').val(VFechaDiaFE.getDate());
                    $('#m_mes_fechaFE').val(moment(VFechaDaFE).month()+1);
                    $('#m_ano_fechaFE').val(moment(VFechaDaFE).year());
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
                $('#v_tbodyDispositivo').empty();
                var container = $('#v_tbodyDispositivo');

                for (var i = 0; i < data[0].vinculacion.length; i++) {
                    if(data[0].vinculacion[i].dispositivoD == 'WINDOWS'){
                            var tr = `<tr id="tr${data[0].vinculacion[i].idVinculacion}">
                            <td>${data[0].vinculacion[i].dispositivoD}</td>
                            <td>${data[0].vinculacion[i].licencia}</td>
                            <td class="hidetext">${data[0].vinculacion[i].codigo}</td>
                            <td id="enviadoW${data[0].vinculacion[i].idVinculacion}">${data[0].vinculacion[i].envio}</td>
                            <td id="estado${data[0].vinculacion[i].idVinculacion}"></td>
                            <td id="correo${data[0].vinculacion[i].idVinculacion}">
                                <a  onclick="javascript:modalWindowsEditar(${data[0].vinculacion[i].idVinculacion});$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                                    correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                                    src="landing/images/note.svg" height="20">
                                </a>
                            </td>
                            <td id="inactivar${data[0].vinculacion[i].idVinculacion}"><a onclick="javascript:inactivarLicenciaWEditar(${data[0].vinculacion[i].idVinculacion})" class="badge badge-soft-danger mr-2">Inactivar</a></td>
                            </tr>`;
                    }else{
                            var tr = `<tr id="tr${data[0].vinculacion[i].idVinculacion}">
                                <td>${data[0].vinculacion[i].dispositivoD}</td>
                                <td>${data[0].vinculacion[i].licencia}</td>
                                <td class="hidetext">${data[0].vinculacion[i].codigo}</td>
                                <td id="enviado${data[0].vinculacion[i].idVinculacion}">${data[0].vinculacion[i].envio}</td>
                                <td id="estado${data[0].vinculacion[i].idVinculacion}"></td>
                                <td id="correo${data[0].vinculacion[i].idVinculacion}">
                                    <input style="display: none;" id="android${data[0].emple_id}" value="${data[0].vinculacion[i].idVinculacion}">
                                    <a  onclick="$('#v_androidEmpleado').modal();$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                                    correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                                        src="landing/images/note.svg" height="20">
                                    </a>
                                </td>
                                <td id="inactivar${data[0].vinculacion[i].idVinculacion}"><a onclick="javascript:inactivarLicenciaEditar(${data[0].vinculacion[i].idVinculacion})" class="badge badge-soft-danger mr-2">Inactivar</a></td>
                                </tr>`;

                    }
                    container.append(tr);

                    if(data[0].vinculacion[i].disponible == 'c'){
                        $("#tr"+data[0].vinculacion[i].idVinculacion).find("td:eq(4)").text("Creado");
                    }
                    if(data[0].vinculacion[i].disponible == 'e'){
                        $("#tr"+data[0].vinculacion[i].idVinculacion).find("td:eq(4)").text("Enviado");
                    }
                    if(data[0].vinculacion[i].disponible == 'a'){
                        $("#tr"+data[0].vinculacion[i].idVinculacion).find("td:eq(4)").text("Activado");
                    }
                    if(data[0].vinculacion[i].disponible == 'i'){
                        $("#tr"+data[0].vinculacion[i].idVinculacion).find("td:eq(4)").text("Inactivo");
                        $('#inactivar'+data[0].vinculacion[i].idVinculacion).empty();
                        $('#correo' + data[0].vinculacion[i].idVinculacion).empty();
                        if(data[0].vinculacion[i].dispositivoD == 'WINDOWS'){
                                var td = `<a  onclick="javascript:modalWindowsEditar(${data[0].vinculacion[i].idVinculacion});$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                                            correo empleado" data-original-title="Habilitar activación" style="cursor: pointer"><img
                                                src="landing/images/email (4).svg" height="20">
                                            </a>`;
                        }else{
                            var td = `<input style="display: none;" id="android${data[0].emple_id}" value="${data[0].vinculacion[i].idVinculacion}">
                                        <a  onclick="$('#v_androidEmpleado').modal();$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                                        correo empleado" data-original-title="Habilitar activación" style="cursor: pointer"><img
                                            src="landing/images/email (4).svg" height="20">
                                        </a>`;
                        }
                        $('#correo' + data[0].vinculacion[i].idVinculacion).append(td);
                    }
                }
            },
            error: function () {}
        });
}
//////////////////////verdatos
function verDEmpleado(idempleadoVer){
    $('#verEmpleadoDetalles').modal();
    $( "#detallehorario_ed" ).empty();
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
                console.log(data);
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
                        $("#detallehorario_ed2").append("<div class='form-group row'><div class='col-md-1'></div><label class='col-lg-4 col-form-label' style='color:#163552;margin-top: 5px;'>Se muestra calendario de empleado </label>" +
                "<div class='col-md-3'></div>"+
                "<div class='col-md-3' ><div class='btn-group mt-2 mr-1'> <button type='button' onclick='eliminarhorariosBD()' class='btn btn-primary btn-sm dropdown-toggle' style='color: #fff; background-color: #4a5669;"+
                "border-color: #485263;' > <img src='admin/images/borrador.svg' height='15'>"+
                " Borrar horarios </button> </div></div></div>");
                    }
                },
                error: function () {}
            });
                $('#selectCalendario_edit3_ver').val(data[0].idcalendar);
                $('#idselect3').val(data[0].idcalendar);
                calendario_edit();
                calendario2_ed();
                $('#ver_tbodyDispositivo').empty();
                var containerVer = $('#ver_tbodyDispositivo');
                for (var i = 0; i < data[0].vinculacion.length; i++) {
                    if(data[0].vinculacion[i].dispositivoD == 'WINDOWS'){
                            var trVer = `<tr id="trVer${data[0].vinculacion[i].idVinculacion}">
                            <td>${data[0].vinculacion[i].dispositivoD}</td>
                            <td>${data[0].vinculacion[i].licencia}</td>
                            <td class="hidetext">${data[0].vinculacion[i].codigo}</td>
                            <td id="enviadoW${data[0].vinculacion[i].idVinculacion}">${data[0].vinculacion[i].envio}</td>
                            <td id="estado${data[0].vinculacion[i].idVinculacion}"></td>
                            <td id="correoVer${data[0].vinculacion[i].idVinculacion}">
                                <a><img src="landing/images/note.svg" height="20">
                                </a>
                            </td>
                            <td id="inactivarVer${data[0].vinculacion[i].idVinculacion}"><a class="badge badge-soft-danger mr-2">Inactivar</a></td>
                            </tr>`;
                    }else{
                            var trVer = `<tr id="trVer${data[0].vinculacion[i].idVinculacion}">
                                <td>${data[0].vinculacion[i].dispositivoD}</td>
                                <td>${data[0].vinculacion[i].licencia}</td>
                                <td class="hidetext">${data[0].vinculacion[i].codigo}</td>
                                <td id="enviado${data[0].vinculacion[i].idVinculacion}">${data[0].vinculacion[i].envio}</td>
                                <td id="estado${data[0].vinculacion[i].idVinculacion}"></td>
                                <td id="correoVer${data[0].vinculacion[i].idVinculacion}">
                                    <input style="display: none;" id="android${data[0].emple_id}" value="${data[0].vinculacion[i].idVinculacion}">
                                    <a><img src="landing/images/note.svg" height="20">
                                    </a>
                                </td>
                                <td id="inactivarVer${data[0].vinculacion[i].idVinculacion}"><a class="badge badge-soft-danger mr-2">Inactivar</a></td>
                                </tr>`;

                    }
                    containerVer.append(trVer);
                    if(data[0].vinculacion[i].disponible == 'c'){
                        $("#trVer"+data[0].vinculacion[i].idVinculacion).find("td:eq(4)").text("Creado");
                    }
                    if(data[0].vinculacion[i].disponible == 'e'){
                        $("#trVer"+data[0].vinculacion[i].idVinculacion).find("td:eq(4)").text("Enviado");
                    }
                    if(data[0].vinculacion[i].disponible == 'a'){
                        $("#trVer"+data[0].vinculacion[i].idVinculacion).find("td:eq(4)").text("Activado");
                    }
                    if(data[0].vinculacion[i].disponible == 'i'){
                        $("#trVer"+data[0].vinculacion[i].idVinculacion).find("td:eq(4)").text("Inactivo");
                        $('#inactivarVer'+data[0].vinculacion[i].idVinculacion).empty();
                        $('#correoVer' + data[0].vinculacion[i].idVinculacion).empty();
                        if(data[0].vinculacion[i].dispositivoD == 'WINDOWS'){
                                var tdV = `<a><img src="landing/images/email (4).svg" height="20">
                                            </a>`;
                        }else{
                            var tdV = `<input style="display: none;" id="android${data[0].emple_id}" value="${data[0].vinculacion[i].idVinculacion}">
                                        <a><img src="landing/images/email (4).svg" height="20">
                                        </a>`;
                        }
                        $('#correoVer' + data[0].vinculacion[i].idVinculacion).append(tdV);
                    }
                }
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
            "bAutoWidth": true,
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
    });
</script>
{{-- ELIMINAR VARIOS ELEMENTOS --}}
<script>
    function eliminarEmpleado() {
        var allVals = [];
        console.log(allVals);

        $(".sub_chk:checked").each(function () {
            allVals.push($(this).attr('data-id'));
        });

        if (allVals.length <= 0) {

    bootbox.alert("Por favor seleccione una fila");
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
                RefreshTablaEmpleado();
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
                console.log(data);
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
                RefreshTablaEmpleado();
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
                RefreshTablaEmpleado();
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