//? ********************FORMULARIO EDITAR ********************* *//
var modalA;
//* FUNCION ABRIR DE MODAL
function ModalAbiertoCondicion() {
    if ($('#contratoDetallesmodalE').is(':visible')) {
        $('#contratoDetallesmodalE').modal('hide');
        modalA = 1;
    } else {
        if ($('#contratoDetallesmodalEN').is(':visible')) {
            $('#contratoDetallesmodalEN').modal('hide');
            modalA = 2;
        } else {
            $('#NuevoContratoDetallesmodalE').modal('hide');
            modalA = 3;
        }
    }

}
//* FUNCION DE CERRAR DE MODAL
function ModalCerrarCondicion() {
    if (modalA === 1) {
        $('#contratoDetallesmodalE').modal('show');
    } else {
        if (modalA === 2) {
            $('#contratoDetallesmodalEN').modal('show');
        } else {
            $('#NuevoContratoDetallesmodalE').modal('show');
        }
    }
}
function modalCPEdit() {
    if (modalA === 1) {
        return $('#contratoDetallesmodalE');
    } else {
        if (modalA === 2) {
            return $('#contratoDetallesmodalEN');
        } else {
            return $('#NuevoContratoDetallesmodalE');
        }
    }
}
var altaEmpleado = true;
var BajaEmp = true;
//: FUNCION MOSTRAR DETALLES DE CONTRATO
function mostrarDetallesContrato(id) {
    $.ajax({
        async: false,
        type: "GET",
        url: "/detalleC",
        data: {
            id: id
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
            if (data.estado == 0) {
                $('.ocultarFechaIE').hide();
            } else {
                $('.ocultarFechaIE').show();
            }
            if (data.fechaBaja !== null) {
                BajaEmp = false;
            } else {
                BajaEmp = true;
            }
            $('#alertErrorFecha').hide();
            $('#fileDetalleE').val(null);
            $('.iborrainputfile').text('Adjuntar archivo');
            $('#v_contrato').val(data.tipoContrato);
            $('#v_condicion').val(data.condPago);
            $('#v_idContrato').val(data.idC);
            $('#v_monto').val(data.monto);
            $('#idContratoD').val(data.idC);
            var VFechaDaIE = moment(data.fechaInicio).format('YYYY-MM-DD');
            var VFechaDiaIE = new Date(moment(VFechaDaIE));
            $('#m_dia_fechaIE').val(VFechaDiaIE.getDate());
            $('#m_mes_fechaIE').val(moment(VFechaDaIE).month() + 1);
            $('#m_ano_fechaIE').val(moment(VFechaDaIE).year());
            $("#checkboxFechaIE").prop('checked', false);
            $('#ocultarFechaE').show();
            if (data.fechaFinal == null || data.fechaFinal == "0000-00-00") {
                $("#checkboxFechaIE").prop('checked', true);
                $('#ocultarFechaE').hide();
            }
            var VFechaDaFE = moment(data.fechaFinal).format('YYYY-MM-DD');
            var VFechaDiaFE = new Date(moment(VFechaDaFE));
            $('#m_dia_fechaFE').val(VFechaDiaFE.getDate());
            $('#m_mes_fechaFE').val(moment(VFechaDaFE).month() + 1);
            $('#m_ano_fechaFE').val(moment(VFechaDaFE).year());
            $('#documentosxDetalle').empty();
            if (data.rutaDocumento != null) {
                var dataD = data.rutaDocumento.split(',');
                var itemsD = "";
                $.each(dataD, function (index, value) {
                    var mostrarC = value.substr(13, value.length);
                    itemsD += `<div class="dropdown-item">
                                    <div class="col-xl-12" style="padding-left: 0px;">
                                        <div class="float-left mt-1">
                                            <a href="documEmpleado/${value}" target="_blank" class="p-2">
                                                <i class="uil-download-alt font-size-18"></i>
                                            </a>
                                            &nbsp;
                                            <a href="documEmpleado/${value}" target="_blank" class="d-inline-block mt-2" style="color:#000000">
                                                <span class="d-inline-block text-truncate" style="max-width: 100px;font-size:12px">${mostrarC}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>`;
                });
                $('#documentosxDetalle').append(itemsD);
            } else {
                itemsVacio = `<span class="p-2" style="font-size:12px">No hay documentos</span>`;
                $('#documentosxDetalle').append(itemsVacio);
            }
            $('#form-ver').modal('hide');
            $('#contratoDetallesmodalE').modal();

        },
        error: function () { }
    });
}
//: CARGAR DATA EN TABLA CONTRATO
function historialEmp() {
    var value = $('#v_id').val();
    $("#editar_tbodyHistorial").empty();
    $.ajax({
        async: false,
        type: "POST",
        url: "/empleado/historial",
        data: {
            idempleado: value
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
            var containerVer = $('#editar_tbodyHistorial');
            if (data.length != 0) {
                $('#gifAlta').hide();
                $("#validHE").hide();
                altaEmpleado = true;
                for (var i = 0; i < data.length; i++) {
                    var trVer = `<tr id="idHistorialE${data[i].id}">`;
                    trVer += `<td style="vertical-align:middle;">
                                        <img src="landing/images/arriba.svg" height="17"> &nbsp;${moment(data[i].fecha_alta).format('DD/MM/YYYY')}
                                        &nbsp;&nbsp;`;
                    if (data[i].fecha_baja != null) {
                        trVer += `<img src="landing/images/abajo.svg" height="17"> &nbsp;${moment(data[i].fecha_baja).format('DD/MM/YYYY')}`;
                    } else {
                        trVer += `<img src="landing/images/abajo.svg" height="17"> &nbsp;------`;
                    }
                    trVer += `</td>`;
                    if (data[i].contrato == null) {
                        trVer += `<td>--</td> `;
                    } else {
                        trVer += `<td> ${data[i].contrato}</td> `;
                    }
                    if (data[i].rutaDocumento != null) {
                        var valores = data[i].rutaDocumento;
                        //* SEPARAMOS CADENAS
                        idsV = valores.split(',');
                        trVer += `<td>
                        <div class="dropdown" id="documentos${i}">
                            <a class="dropdown" data-toggle="dropdown" aria-expanded="false"
                                style="cursor: pointer">
                                <span class="badge badge-soft-primary text-primary">
                                    <i class="uil-file-alt font-size-17"></i>
                                </span>
                                &nbsp;
                                Documentos
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">`;
                        $.each(idsV, function (index, value) {
                            var mostrarC = value.substr(13, value.length);
                            trVer += `<div class="dropdown-item">
                                        <div class="col-xl-12" style="padding-left: 0px;">
                                            <div class="float-left mt-1">
                                                <a href="documEmpleado/${value}" target="_blank" class="p-2">
                                                    <i class="uil-download-alt font-size-18"></i>
                                                </a>
                                                &nbsp;
                                                <a href="documEmpleado/${value}" target="_blank" class="d-inline-block mt-2" style="color:#000000">
                                                    <span class="d-inline-block text-truncate" style="max-width: 150px;">${mostrarC}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>`;
                        });
                        trVer += `</ul></div></td> `;
                    } else {
                        trVer += `<td> --</td> `;
                    }
                    if (data[i].idContrato != null) {
                        trVer += `<td>
                        <a onclick="javascript:mostrarDetallesContrato(${data[i].idContrato})" data-toggle="tooltip" data-placement="right"
                            title="Detalle de Contrato" data-original-title="Detalle de Contrato" style="cursor: pointer;">
                            <img src="landing/images/adaptive.svg" height="18">
                                </a>`;
                    } else {
                        trVer += `<td>
                        <a onclick="javascript:modalNuevoDetalle(${data[i].id})" data-toggle="tooltip" data-placement="right"
                            title="Detalle de Contrato" data-original-title="Detalle de Contrato" style="cursor: pointer;">
                            <img src="landing/images/adaptive.svg" height="18">
                                </a>`;
                    }
                    if (data[i].fecha_baja !== null && i != 0) {
                        trVer += `&nbsp;
                                <a data-toggle="tooltip" title="Eliminar contrato" data-placement="right"
                                    onclick="javascript:eliminarContrato(${data[i].id});" style="cursor: pointer">
                                    <img src="admin/images/delete.svg" height="15">
                                </a>`;
                    }
                    if (data[i].fecha_baja === null && data[i].contrato !== null) {
                        trVer += `&nbsp;
                                <a data-toggle="tooltip" name="dBajaName" title="Dar de baja" data-placement="right"
                                    onclick="javascript:bajaEmpleadoContrato(${data[i].id});$('#form-ver').modal('hide');" style="cursor: pointer">
                                    <img src="landing/images/abajo.svg" height="17">
                                </a>`;
                    }
                    if (data[i].fecha_baja === null || data[i].contrato === null) {
                        altaEmpleado = false;
                    }
                    trVer += '</td>';
                    trVer += '</tr>';
                    $('#idHistorialE' + data[i].id).css("background-color", "transparent");
                    $('#validDC').hide();
                    containerVer.append(trVer);
                }
                mostrarBoton();
            } else {
                $('#nuevaAltaEdit').show();
                $('#gifAlta').show();
            }
        },
        error: function () { }
    });
}
//: FUNCION PARA MOSTRAR BOTON @NUEVA ALTA
function mostrarBoton() {
    if (altaEmpleado) {
        $('#nuevaAltaEdit').show();
    } else {
        $('#nuevaAltaEdit').hide();
    }
}
//: CHECKBOX EN EDITAR DETALLES DE CONTROL
$("#checkboxFechaIE").on("click", function () {
    if ($("#checkboxFechaIE").is(':checked')) {
        $('#m_dia_fechaFE').val("0");
        $('#m_mes_fechaFE').val("0");
        $('#m_ano_fechaFE').val("0");
        $('#ocultarFechaE').hide();
    } else {
        $('#ocultarFechaE').show();
    }
});
//: VALIDACION DE ARCHIVOS EN EDITAR DETALLES DE CONTROL
async function validArchivosEdit() {
    var respuesta = true;
    $.each($('#fileDetalleE'), function (i, obj) {
        $.each(obj.files, function (j, file) {
            var fileSize = file.size;
            var sizeKiloBytes = parseInt(fileSize);
            if (sizeKiloBytes > parseInt($('#fileDetalleE').attr('size'))) {
                respuesta = false;
            }
        });
    });
    return respuesta;
}
//: LIMPIAR VALIDACION DE ARCHIVO
$('#fileDetalleE').on("click", function () {
    $('#validArchivoEdit').hide();
});
//: FUNCION PARA VALIDAR FECHA INICIO
async function validarFechaInicio(fechaInicio, idContrato) {
    var resultado = {};
    $.ajax({
        async: false,
        type: "POST",
        url: "/validFechaDetalle",
        data: {
            contrato: idContrato
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
            if (data.length != 0) {
                var momentFechaInicio = moment(fechaInicio);
                var momentFechaAnterior = moment(data[0].fechaFinal);
                if (momentFechaInicio.isBefore(momentFechaAnterior)) {
                    resultado = { "respuesta": false, "fecha": data[0].fechaFinal };
                }
            }
        },
        error: function () { }
    });

    return resultado;
}
//:EDITAR DETALLES DE CONTRATO
async function editarDetalleCE() {
    var idContrato = $('#idContratoD').val();
    var condicionPago = $('#v_condicion').val();
    var monto = $('#v_monto').val();
    var fechaInicial;
    var fechaFinal = "0000-00-00";

    //* VALIDACION DE FECHAS DE INICIO Y FINAL
    var m_AnioIE = parseInt($('#m_ano_fechaIE').val());
    var m_MesIE = parseInt($('#m_mes_fechaIE').val() - 1);
    var m_DiaIE = parseInt($('#m_dia_fechaIE').val());
    var m1_VFechaIE = moment([m_AnioIE, m_MesIE, m_DiaIE]);
    if (m1_VFechaIE.isValid()) {
        $('#m_validFechaCIE').hide();
    } else {
        $('#m_validFechaCIE').show();
        return false;
    }
    if (m_AnioIE != 0 && m_MesIE != -1 && m_DiaIE != 0) {
        fechaInicial = moment([m_AnioIE, m_MesIE, m_DiaIE]).format('YYYY-MM-DD');
    } else {
        fechaInicial = '0000-00-00';
    }
    if (!$("#checkboxFechaIE").is(':checked')) {
        var mf_AnioFE = parseInt($('#m_ano_fechaFE').val());
        var mf_MesFE = parseInt($('#m_mes_fechaFE').val() - 1);
        var mf_DiaFE = parseInt($('#m_dia_fechaFE').val());
        var m1f_VFechaFE = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]);
        if (m1f_VFechaFE.isValid()) {
            $('#m_validFechaCFE').hide();
        } else {
            $('#m_validFechaCFE').show();
            return false;

        }
        var momentInicio = moment([m_AnioIE, m_MesIE, m_DiaIE]);
        var momentFinal = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]);
        if (!momentInicio.isBefore(momentFinal)) {
            $('#m_validFechaCFE').show();
            return;
        } else {
            $('#m_validFechaCFE').hide();
        }
        if (mf_AnioFE != 0 && mf_MesFE != -1 && mf_DiaFE != 0) {
            fechaFinal = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]).format('YYYY-MM-DD');
        } else {
            fechaFinal = '0000-00-00';
        }
    }
    //* FUNCIONES DE VALIDAR ARCHIVO
    const result = await validArchivosEdit();
    if (!result) {
        $('#validArchivoEdit').show();
        return false;
    } else {
        $('#validArchivoEdit').hide();
    }
    //* ***********************************************
    //* FUNCIONES DE VALIDAR FECHA INICIO
    const respFecha = await validarFechaInicio(fechaInicial, idContrato);
    if (respFecha.respuesta != undefined) {
        if (!respFecha.respuesta) {
            $('#alertErrorFecha').empty();
            var errorAlert = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                                <span style="font-size: 14px;">Su fecha inicial debe ser mayor a la fecha de baja de su contrato anterior ${moment(respFecha.fecha).lang('es').format("DD MMMM YYYY")}</span>`;
            $('#alertErrorFecha').append(errorAlert);
            $('#alertErrorFecha').show();
            return false;
        } else {
            $('#alertErrorFecha').hide();
        }
    }
    //* *****************************************
    var fechaAlta = fechaInicial;
    var fechaBaja = (fechaFinal == '0000-00-00' || BajaEmp == true) ? null : fechaFinal;
    //* AJAX DE EDITAR
    $.ajax({
        type: "POST",
        url: "/editDetalleC",
        data: {
            idContrato: idContrato,
            fechaAlta: fechaAlta,
            fechaBaja: fechaBaja,
            condicionPago: condicionPago,
            monto: monto,
            fechaInicial: fechaInicial,
            fechaFinal: fechaFinal
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
            archivosDeEdit(idContrato);
            historialEmp();
            $.notifyClose();
            $.notify(
                {
                    message: "\nDatos Modificados.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    element: $('#form-ver'),
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () { }
    });

    $('#contratoDetallesmodalE').modal('toggle');
    $('#form-ver').modal('show');
}
//: REGISTRAR ARCHIVOS DE  EDIT DETALLE DE CONTROL
function archivosDeEdit(id) {
    //* AJAX DE ARCHICOS
    var formData = new FormData();
    $.each($("#fileDetalleE"), function (i, obj) {
        $.each(obj.files, function (j, file) {
            formData.append('file[' + j + ']', file);
        })
    });
    $.ajax({
        contentType: false,
        processData: false,
        type: "POST",
        url: "/archivosEditC/" + id,
        data: formData,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            historialEmp();
        },
        error: function () {
        },
    });
}
//: MODAL DE NUEVO DETALLE
function modalNuevoDetalle(id) {
    limpiarNuevosDatosDetalle();
    $('#idHistoE').val(id);
    $('#form-ver').modal('hide');
    $('#NuevoContratoDetallesmodalE').modal();
    validacionNuevoDetalle();
}
//: VALIDACIOM DE NUEVO DETALLE
function validacionNuevoDetalle() {
    if ($('#v_contratoND').val() != "") {
        $('#v_condicionND').prop("disabled", false);
        $('#v_montoND').prop("disabled", false);
        $('#m_dia_fechaIEND').prop("disabled", false);
        $('#m_mes_fechaIEND').prop("disabled", false);
        $('#m_ano_fechaIEND').prop("disabled", false);
        $('#fileArchivosNuevosD').prop("disabled", false);
        $('#checkboxFechaIEND').prop("disabled", false);
        $('#m_dia_fechaFEND').prop("disabled", false);
        $('#m_mes_fechaFEND').prop("disabled", false);
        $('#m_ano_fechaFEND').prop("disabled", false);
        $('#guardarAltaND').prop("disabled", false);
    } else {
        $('#v_condicionND').prop("disabled", true);
        $('#v_montoND').prop("disabled", true);
        $('#m_dia_fechaIEND').prop("disabled", true);
        $('#m_mes_fechaIEND').prop("disabled", true);
        $('#m_ano_fechaIEND').prop("disabled", true);
        $('#fileArchivosNuevosD').prop("disabled", true);
        $('#checkboxFechaIEND').prop("disabled", true);
        $('#m_dia_fechaFEND').prop("disabled", true);
        $('#m_mes_fechaFEND').prop("disabled", true);
        $('#m_ano_fechaFEND').prop("disabled", true);
        $('#guardarAltaND').prop("disabled", true);
    }
}
$('#fileArchivosNuevosD').on("click", function () {
    $('#validArchivoEditND').hide();
});
//: LIMPIAR FORMULARIO DE NUEVO DETALLE
function limpiarNuevosDatosDetalle() {
    $('#idHistoE').val(null);
    $('#v_contratoND').val("");
    $('#v_condicionND').val("");
    $('#v_montoND').val("");
    $('#m_dia_fechaIEND').val(0);
    $('#m_mes_fechaIEND').val(0);
    $('#m_ano_fechaIEND').val(0);
    $('#fileArchivosNuevosD').val(null);
    $('.iborrainputfile').val("Adjuntar archivo");
    $('#checkboxFechaIEND').prop("checked", false);
    $('#m_dia_fechaFEND').val(0);
    $('#m_mes_fechaFEND').val(0);
    $('#m_ano_fechaFEND').val(0);
}
//: REGISTRAR ARCHIVOS DE NUEVO DETALLE
async function validArchivosNuevoD() {
    var respuesta = true;
    $.each($('#fileArchivosNuevosD'), function (i, obj) {
        $.each(obj.files, function (j, file) {
            var fileSize = file.size;
            var sizeKiloBytes = parseInt(fileSize);
            if (sizeKiloBytes > parseInt($('#fileArchivosNuevosD').attr('size'))) {
                respuesta = false;
            }
        });
    });
    return respuesta;
}
//: REGISTRAR ARCHIVO EN DETALLE
function archivosDeNuevoDetalle(id) {
    //* AJAX DE ARCHICOS
    var formData = new FormData();
    $.each($('#fileArchivosNuevosD'), function (i, obj) {
        $.each(obj.files, function (j, file) {
            formData.append('file[' + j + ']', file);
        })
    });
    $.ajax({
        contentType: false,
        processData: false,
        type: "POST",
        url: "/archivosEditC/" + id,
        data: formData,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            historialEmp();
        },
        error: function () {
        },
    });
}
//: REGISTRAR NUEVO DETALLE
async function nuevoDetalleC() {
    var contrato = $('#v_contratoND').val();
    var condicionPago = $('#v_condicionND').val();
    var monto = $('#v_montoND').val();
    var fechaInicial;
    var fechaFinal = "0000-00-00";
    var idHistorial = $('#idHistoE').val();
    var idEmpleado = $('#v_id').val();

    //* FUNCIONES DE FECHAS
    var m_AnioIE = parseInt($('#m_ano_fechaIEND').val());
    var m_MesIE = parseInt($('#m_mes_fechaIEND').val() - 1);
    var m_DiaIE = parseInt($('#m_dia_fechaIEND').val());
    var m1_VFechaIE = moment([m_AnioIE, m_MesIE, m_DiaIE]);
    if (m1_VFechaIE.isValid()) {
        $('#m_validFechaCIEND').hide();
    } else {
        $('#m_validFechaCIEND').show();
        return false;
    }
    if (m_AnioIE != 0 && m_MesIE != -1 && m_DiaIE != 0) {
        fechaInicial = moment([m_AnioIE, m_MesIE, m_DiaIE]).format('YYYY-MM-DD');
    } else {
        fechaInicial = '0000-00-00';
    }
    if (!$("#checkboxFechaIEND").is(':checked')) {
        var mf_AnioFE = parseInt($('#m_ano_fechaFEND').val());
        var mf_MesFE = parseInt($('#m_mes_fechaFEND').val() - 1);
        var mf_DiaFE = parseInt($('#m_dia_fechaFEND').val());
        var m1f_VFechaFE = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]);
        if (m1f_VFechaFE.isValid()) {
            $('#m_validFechaCFEND').hide();
        } else {
            $('#m_validFechaCFEND').show();
            return false;

        }
        var momentInicio = moment([m_AnioIE, m_MesIE, m_DiaIE]);
        var momentFinal = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]);
        if (!momentInicio.isBefore(momentFinal)) {
            $('#m_validFechaCFEND').show();
            return;
        } else {
            $('#m_validFechaCFEND').hide();
        }
        if (mf_AnioFE != 0 && mf_MesFE != -1 && mf_DiaFE != 0) {
            fechaFinal = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]).format('YYYY-MM-DD');
        } else {
            fechaFinal = '0000-00-00';
        }
    }
    var fechaAlta = fechaInicial;
    //* FUNCIONES DE VALIDAR ARCHIVO
    const result = await validArchivosNuevoD();
    if (!result) {
        $('#validArchivoEditND').show();
        return false;
    } else {
        $('#validArchivoEditND').hide();
    }
    //* *******************************FINALIZACION ******************************************
    //* AJAX DE NUEVO DETALLE
    $.ajax({
        type: "POST",
        url: "/nuevoDC",
        data: {
            contrato: contrato,
            fechaAlta: fechaAlta,
            condicionPago: condicionPago,
            monto: monto,
            fechaInicial: fechaInicial,
            fechaFinal: fechaFinal,
            idHistorial: idHistorial,
            idEmpleado: idEmpleado
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
            //* REGISTRAR ARCHIVOS EN NUEVO DETALLE
            archivosDeNuevoDetalle(data);
            $('#NuevoContratoDetallesmodalE').modal('toggle');
            $('#form-ver').modal('show');
            historialEmp();
            limpiarNuevosDatosDetalle();
            $.notifyClose();
            $.notify(
                {
                    message: "\nRegisto exitoso.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    element: $('#form-ver'),
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () { }
    });
}
//: CHECKBOX DE FECHA INDEFINADA EN NUEVO DETALLE
$("#checkboxFechaIEND").on("click", function () {
    if ($("#checkboxFechaIEND").is(':checked')) {
        $('#m_dia_fechaFEND').val(0);
        $('#m_mes_fechaFEND').val(0);
        $('#m_ano_fechaFEND').val(0);
        $('#ocultarFechaEND').hide();
    } else {
        $('#ocultarFechaEND').show();
    }
});
//TODO -> ****************** NUEVA ALTA *************************************
//* MODAL DE NUEVA ALTA 
function modalNuevaAlta() {
    $('#contratoDetallesmodalEN').modal();
    $('#form-ver').modal('hide');
    limpiarNuevosDatosAlta();
    validacionNuevaAlta();
}
//* VALIDACION DE ARCHIVOS EN NUEVA ALTA EN EDITAR
async function validArchivosAltaEdit() {
    var respuesta = true;
    $.each($('#fileArchivosNuevos'), function (i, obj) {
        $.each(obj.files, function (j, file) {
            var fileSize = file.size;
            var sizeKiloBytes = parseInt(fileSize);
            if (sizeKiloBytes > parseInt($('#fileArchivosNuevos').attr('size'))) {
                respuesta = false;
            }
        });
    });
    return respuesta;
}
$('#fileArchivosNuevos').on("click", function () {
    $('#validArchivoEditN').hide();
});
//* REGISTRAR ARCHIVO EN NUEVA ALTA
function archivosDeNuevo(id) {
    //* AJAX DE ARCHICOS
    var formData = new FormData();
    $.each($("#fileArchivosNuevos"), function (i, obj) {
        $.each(obj.files, function (j, file) {
            formData.append('file[' + j + ']', file);
        })
    });
    $.ajax({
        contentType: false,
        processData: false,
        type: "POST",
        url: "/archivosEditC/" + id,
        data: formData,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            historialEmp();
        },
        error: function () {
        },
    });
}
//* FUNCION PARA VALIDAR FECHA INICIO
async function validarFechaInicioAlta(fechaInicio, idEmpleado) {
    var resultado = {};
    $.ajax({
        async: false,
        type: "POST",
        url: "/validFechaAlta",
        data: {
            empleado: idEmpleado
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
            if (data.length != 0) {
                var momentFechaInicio = moment(fechaInicio);
                var momentFechaAnterior = moment(data[0].fechaFinal);
                if (momentFechaInicio.isBefore(momentFechaAnterior)) {
                    resultado = { "respuesta": false, "fecha": data[0].fechaFinal };
                }
            }
        },
        error: function () { }
    });

    return resultado;
}
//* REGISTRAR NUEVA ALTA
async function nuevaAltaEditar() {
    var contrato = $('#v_contratoN').val();
    var condicionPago = $('#v_condicionN').val();
    var monto = $('#v_montoN').val();
    var fechaInicial;
    var fechaFinal = "0000-00-00";
    var idEmpleado = $('#v_id').val();

    //* FUNCIONES DE FECHAS
    var m_AnioIE = parseInt($('#m_ano_fechaIEN').val());
    var m_MesIE = parseInt($('#m_mes_fechaIEN').val() - 1);
    var m_DiaIE = parseInt($('#m_dia_fechaIEN').val());
    var m1_VFechaIE = moment([m_AnioIE, m_MesIE, m_DiaIE]);
    if (m1_VFechaIE.isValid()) {
        $('#m_validFechaCIEN').hide();
    } else {
        $('#m_validFechaCIEN').show();
        return false;
    }
    if (m_AnioIE != 0 && m_MesIE != -1 && m_DiaIE != 0) {
        fechaInicial = moment([m_AnioIE, m_MesIE, m_DiaIE]).format('YYYY-MM-DD');
    } else {
        fechaInicial = '0000-00-00';
    }
    if (!$("#checkboxFechaIEN").is(':checked')) {
        var mf_AnioFE = parseInt($('#m_ano_fechaFEN').val());
        var mf_MesFE = parseInt($('#m_mes_fechaFEN').val() - 1);
        var mf_DiaFE = parseInt($('#m_dia_fechaFEN').val());
        var m1f_VFechaFE = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]);
        if (m1f_VFechaFE.isValid()) {
            $('#m_validFechaCFEN').hide();
        } else {
            $('#m_validFechaCFEN').show();
            return false;

        }
        var momentInicio = moment([m_AnioIE, m_MesIE, m_DiaIE]);
        var momentFinal = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]);
        if (!momentInicio.isBefore(momentFinal)) {
            $('#m_validFechaCFEN').show();
            return;
        } else {
            $('#m_validFechaCFEN').hide();
        }
        if (mf_AnioFE != 0 && mf_MesFE != -1 && mf_DiaFE != 0) {
            fechaFinal = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]).format('YYYY-MM-DD');
        } else {
            fechaFinal = '0000-00-00';
        }
    }
    var fechaAlta = fechaInicial;
    //* FUNCIONES DE VALIDAR ARCHIVO
    const result = await validArchivosAltaEdit();
    if (!result) {
        $('#validArchivoEditN').show();
        return false;
    } else {
        $('#validArchivoEditN').hide();
    }
    //* FUNCIONES DE VALIDAR FECHA INICIO
    const respFecha = await validarFechaInicioAlta(fechaInicial, idEmpleado);
    if (respFecha.respuesta != undefined) {
        if (!respFecha.respuesta) {
            $('#alertErrorFechaAlta').empty();
            var errorAlert = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                                <span style="font-size: 14px;">Su fecha inicial debe ser mayor a la fecha de baja de su contrato anterior ${moment(respFecha.fecha).lang('es').format("DD MMMM YYYY")}</span>`;
            $('#alertErrorFechaAlta').append(errorAlert);
            $('#alertErrorFechaAlta').show();
            return false;
        } else {
            $('#alertErrorFechaAlta').hide();
        }
    }
    //* *****************************************
    //* *******************************FINALIZACION ******************************************
    //* AJAX DE NUEVA ALTA
    $.ajax({
        type: "POST",
        url: "/nuevaAlta",
        data: {
            contrato: contrato,
            fechaAlta: fechaAlta,
            condicionPago: condicionPago,
            monto: monto,
            fechaInicial: fechaInicial,
            fechaFinal: fechaFinal,
            idEmpleado: idEmpleado
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
            //* REGISTRAR ARCHIVOS EN NUEVA ALTA
            archivosDeNuevo(data);
            $('#contratoDetallesmodalEN').modal('toggle');
            $('#form-ver').modal('show');
            historialEmp();
            $.notifyClose();
            $.notify(
                {
                    message: "\nRegisto exitoso.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    element: $('#form-ver'),
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () { }
    });
}
//* CHECKBOX DE FECHA INDEFINADA EN NUEVA ALTA
$("#checkboxFechaIEN").on("click", function () {
    if ($("#checkboxFechaIEN").is(':checked')) {
        $('#m_dia_fechaFEN').val(0);
        $('#m_mes_fechaFEN').val(0);
        $('#m_ano_fechaFEN').val(0);
        $('#ocultarFechaEN').hide();
    } else {
        $('#ocultarFechaEN').show();
    }
});
//* LIMPIAR FORMULARIO EN NUEVA ALTA
function limpiarNuevosDatosAlta() {
    $('#v_contratoN').val("");
    $('#v_condicionN').val("");
    $('#v_montoN').val("");
    $('#m_dia_fechaIEN').val(0);
    $('#m_mes_fechaIEN').val(0);
    $('#m_ano_fechaIEN').val(0);
    $('#fileArchivosNuevos').val(null);
    $('.iborrainputfile').val(null);
    $('#checkboxFechaIEN').prop("checked", false);
    $('#m_dia_fechaFEN').val(0);
    $('#m_mes_fechaFEN').val(0);
    $('#m_ano_fechaFEN').val(0);
    $('#ocultarFechaEN').show();
}
//* VALIDACION DE NUEVA ALTA
function validacionNuevaAlta() {
    if ($('#v_contratoN').val() != "") {
        $('#v_condicionN').prop("disabled", false);
        $('#v_montoN').prop("disabled", false);
        $('#m_dia_fechaIEN').prop("disabled", false);
        $('#m_mes_fechaIEN').prop("disabled", false);
        $('#m_ano_fechaIEN').prop("disabled", false);
        $('#fileArchivosNuevos').prop("disabled", false);
        $('#checkboxFechaIEN').prop("disabled", false);
        $('#m_dia_fechaFEN').prop("disabled", false);
        $('#m_mes_fechaFEN').prop("disabled", false);
        $('#m_ano_fechaFEN').prop("disabled", false);
        $('#guardarAltaN').prop("disabled", false);
    } else {
        $('#v_condicionN').prop("disabled", true);
        $('#v_montoN').prop("disabled", true);
        $('#m_dia_fechaIEN').prop("disabled", true);
        $('#m_mes_fechaIEN').prop("disabled", true);
        $('#m_ano_fechaIEN').prop("disabled", true);
        $('#fileArchivosNuevos').prop("disabled", true);
        $('#checkboxFechaIEN').prop("disabled", true);
        $('#m_dia_fechaFEN').prop("disabled", true);
        $('#m_mes_fechaFEN').prop("disabled", true);
        $('#m_ano_fechaFEN').prop("disabled", true);
        $('#guardarAltaN').prop("disabled", true);
    }
}
//* ELIMINAR CONTRATO
function eliminarContrato(id) {
    //* DESICION DE ELIMINAR CONTRATO
    alertify
        .confirm("¿Desea eliminar contrato?", function (
            e
        ) {
            if (e) {
                $.ajax({
                    type: "POST",
                    url: "/eliminarHistorialC",
                    data: {
                        id: id
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (data) {
                        historialEmp();
                        $.notifyClose();
                        $.notify({
                            message: '\nContrato eliminado',
                            icon: 'landing/images/bell.svg',
                        }, {
                            element: $('#form-ver'),
                            icon_type: 'image',
                            allow_dismiss: true,
                            newest_on_top: true,
                            delay: 6000,
                            template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                                '</div>',
                            spacing: 35
                        });
                    },
                    error: function () { },
                });
            }
        })
        .setting({
            title: "Eliminar contrato",
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
                historialEmp();
            },
        });
}
//TODO -> ************************FINALIZACION********************** **//
//TODO -> ************************BAJA DESDE TABLA CONTRATO********************** **//
//? FUNCION DE MOSTRAR MODAL DE DECICION DE BAJA
function bajaEmpleadoContrato(id) {
    $('#modalBajaHistorial').modal();
    $('#alertFechaBaja').hide();
    $('#idHistorialEdit').val(id);
    //: FECHA DE BAJA EN TABLA CONTRATO
    var fechaValue = $("#fechaBajaEdit").flatpickr({
        mode: "single",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "Y-m-d",
        locale: "es",
        wrap: true,
        allowInput: true,
        disableMobile: "true"
    });
    $(function () {
        f = moment().format("YYYY-MM-DD");
        fechaValue.setDate(f);
    });
}
//? VALIDACION DE ARCHIVOS EN BAJA
async function validArchivosBajaEdit() {
    var respuesta = true;
    $.each($('#bajaFileEdit'), function (i, obj) {
        $.each(obj.files, function (j, file) {
            var fileSize = file.size;
            var sizeKiloBytes = parseInt(fileSize);
            if (sizeKiloBytes > parseInt($('#bajaFileEdit').attr('size'))) {
                respuesta = false;
            }
        });
    });
    return respuesta;
}
$('#bajaFileEdit').on("click", function () {
    $('#validArchivoBajaE').hide();
});
//? FUNCION DE GUARDAR ARCHIVOS DE BAJA 
function archivosDeBaja(id) {
    //* AJAX DE ARCHICOS
    var formData = new FormData();
    $.each($('#bajaFileEdit'), function (i, obj) {
        $.each(obj.files, function (j, file) {
            formData.append('file[' + j + ']', file);
        })
    });
    $.ajax({
        contentType: false,
        processData: false,
        type: "POST",
        url: "/archivosEditC/" + id,
        data: formData,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            historialEmp();
        },
        error: function () {
        },
    });
}
//? FUNCION GUARDAR DATOS DE BAJA
async function confirmarBajaHistorial() {
    const result = await validArchivosBajaEdit();
    if (!result) {
        $('#validArchivoBajaE').show();
        return false;
    } else {
        $('#validArchivoBajaE').hide();
    }
    var id = $('#idHistorialEdit').val();
    var fechaBaja = $('#fechaBajaInput').val();
    $("#editar_tbodyHistorial").empty();
    $.ajax({
        type: "POST",
        url: "/bajaHistorial",
        data: {
            id: id,
            fechaBaja: fechaBaja
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
            if (data.respuesta != false) {
                $('#alertFechaBaja').hide();
                archivosDeBaja(data);
                $('#modalBajaHistorial').modal('toggle');
                $('#form-ver').modal('show');
                historialEmp();
            } else {
                $('#alertFechaBaja').empty();
                var errorAlert = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                                <span style="font-size: 14px;">Su fecha de baja debe ser mayor a la fecha de alta de su contrato ${moment(data.fecha).lang('es').format("DD MMMM YYYY")}</span>`;
                $('#alertFechaBaja').append(errorAlert);
                $('#alertFechaBaja').show();
            }
        },
        error: function () { }
    });
}
function cerrarModalHistorial() {
    $('#modalBajaHistorial').modal('toggle');
    $('#form-ver').modal('show');
}
//? ********************FINALIZACION********************** *//
//? ********************FORMULARIO REGISTRAR ********************* *//
var modalReg;
//* FUNCION ABRIR DE MODAL
function ModalAbiertoCondicionReg() {
    if ($('#contratoDetallesmodal').is(':visible')) {
        $('#contratoDetallesmodal').modal('hide');
        modalReg = 1;
    } else {
        $('#detallesContratomodal').modal('hide');
        modalReg = 2;
    }

}
//* FUNCION DE CERRAR DE MODAL
function ModalCerrarCondicionReg() {
    if (modalReg === 1) {
        $('#contratoDetallesmodal').modal('show');
    } else {
        $('#detallesContratomodal').modal('show');
    }
}
function modalCPReg() {
    if (modalReg === 1) {
        return $('#contratoDetallesmodal');
    } else {
        return $('#detallesContratomodal');
    }
}
var altaEmpleadoReg = true;
//* CARGAR DATA EN TABLA CONTRATO
function historialEmpReg() {
    var value = $("#idEmpleado").val();
    $("#reg_tbodyHistorial").empty();
    $.ajax({
        async: false,
        type: "POST",
        url: "/empleado/historial",
        data: {
            idempleado: value
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
            var container = $('#reg_tbodyHistorial');
            if (data.length != 0) {
                $("#reg_validHE").hide();
                altaEmpleadoReg = true;
                for (var i = 0; i < data.length; i++) {
                    var trReg = `<tr>`;
                    trReg += `<td style="vertical-align:middle;">
                                        <img src="landing/images/arriba.svg" height="17"> &nbsp;${moment(data[i].fecha_alta).format('DD/MM/YYYY')}
                                        &nbsp;&nbsp;`;
                    if (data[i].fecha_baja != null) {
                        trReg += `<img src="landing/images/abajo.svg" height="17"> &nbsp;${moment(data[i].fecha_baja).format('DD/MM/YYYY')}`;
                    } else {
                        trReg += `<img src="landing/images/abajo.svg" height="17"> &nbsp;------`;
                    }
                    trReg += `</td>`;
                    if (data[i].contrato == null) {
                        trReg += `<td>--</td> `;
                    } else {
                        trReg += `<td> ${data[i].contrato}</td> `;
                    }
                    if (data[i].rutaDocumento != null) {
                        var valores = data[i].rutaDocumento;
                        //* SEPARAMOS CADENAS
                        idsV = valores.split(',');
                        trReg += `<td>
                        <div class="dropdown" id="documentosReg${i}">
                            <a class="dropdown" data-toggle="dropdown" aria-expanded="false"
                                style="cursor: pointer">
                                <span class="badge badge-soft-primary text-primary">
                                    <i class="uil-file-alt font-size-17"></i>
                                </span>
                                &nbsp;
                                Documentos
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">`;
                        $.each(idsV, function (index, value) {
                            var mostrarC = value.substr(13, value.length);
                            trReg += `<div class="dropdown-item">
                                        <div class="col-xl-12" style="padding-left: 0px;">
                                            <div class="float-left mt-1">
                                                <a href="documEmpleado/${value}" target="_blank" class="p-2">
                                                    <i class="uil-download-alt font-size-18"></i>
                                                </a>
                                                &nbsp;
                                                <a href="documEmpleado/${value}" target="_blank" class="d-inline-block mt-2" style="color:#000000">
                                                    <span class="d-inline-block text-truncate" style="max-width: 150px;">${mostrarC}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>`;
                        });
                        trReg += `</ul></div></td> `;
                    } else {
                        trReg += `<td> --</td> `;
                    }
                    trReg += `<td>
                                <a onclick="javascript:mostrarDetallesContratoReg(${data[i].idContrato})" data-toggle="tooltip" data-placement="right"
                                    title="Detalle de Contrato" data-original-title="Detalle de Contrato" style="cursor: pointer;">
                                    <img src="landing/images/adaptive.svg" height="18">
                                </a>`;
                    if (data[i].fecha_baja !== null && i != 0) {
                        trReg += `&nbsp;
                                <a data-toggle="tooltip" title="Eliminar contrato" data-placement="right"
                                    onclick="javascript:eliminarContratoReg(${data[i].id});" style="cursor: pointer">
                                    <img src="admin/images/delete.svg" height="15">
                                </a>`;
                    }
                    if (data[i].fecha_baja === null) {
                        altaEmpleadoReg = false;
                        trReg += `&nbsp;
                                <a data-toggle="tooltip" name="dBajaName" title="Dar de baja" data-placement="right"
                                    onclick="javascript:bajaEmpleadoContratoReg(${data[i].id});$('#form-registrar').modal('hide');" style="cursor: pointer">
                                    <img src="landing/images/abajo.svg" height="17">
                                </a>`;
                    }
                    trReg += '</td>';
                    trReg += '</tr>';
                    container.append(trReg);
                }
                mostrarBotonReg();
            }
        },
        error: function () { }
    });
}
//* FUNCION PARA MOSTRAR BOTON @NUEVA ALTA
function mostrarBotonReg() {
    if (altaEmpleadoReg) {
        $('#reg_nuevaAlta').show();
    } else {
        $('#reg_nuevaAlta').hide();
    }
}
var BajaEmpReg = true;
//* FUNCION MOSTRAR DETALLES DE CONTRATO
function mostrarDetallesContratoReg(id) {
    $.ajax({
        async: false,
        type: "GET",
        url: "/detalleC",
        data: {
            id: id
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
            if (data.estado == 0) {
                $('.ocultarFechaD').hide();
            } else {
                $('.ocultarFechaD').show();
            }
            if (data.fechaBaja !== null) {
                BajaEmpReg = false;
            } else {
                BajaEmpReg = true;
            }
            $('#alertErrorFechaDetalleReg').hide();
            $('#reg_fileArchivosD').val(null);
            $('.iborrainputfile').text('Adjuntar archivo');
            $('#contratoD').val(data.tipoContrato);
            $('#condicionD').val(data.condPago);
            $('#montoD').val(data.monto);
            $('#reg_idContratoD').val(data.idC);
            var VFechaDaIE = moment(data.fechaInicio).format('YYYY-MM-DD');
            var VFechaDiaIE = new Date(moment(VFechaDaIE));
            $('#m_dia_fechaD').val(VFechaDiaIE.getDate());
            $('#m_mes_fechaD').val(moment(VFechaDaIE).month() + 1);
            $('#m_ano_fechaD').val(moment(VFechaDaIE).year());
            $("#checkboxFechaID").prop('checked', false);
            $('#ocultarFechaD').show();
            if (data.fechaFinal == null || data.fechaFinal == "0000-00-00") {
                $("#checkboxFechaID").prop('checked', true);
                $('#ocultarFechaD').hide();
            }
            var VFechaDaFE = moment(data.fechaFinal).format('YYYY-MM-DD');
            var VFechaDiaFE = new Date(moment(VFechaDaFE));
            $('#mf_dia_fechaD').val(VFechaDiaFE.getDate());
            $('#mf_mes_fechaD').val(moment(VFechaDaFE).month() + 1);
            $('#mf_ano_fechaD').val(moment(VFechaDaFE).year());
            $('#reg_documentosxDetalle').empty();
            if (data.rutaDocumento != null) {
                var dataD = data.rutaDocumento.split(',');
                var itemsD = "";
                $.each(dataD, function (index, value) {
                    var mostrarC = value.substr(13, value.length);
                    itemsD += `<div class="dropdown-item">
                                    <div class="col-xl-12" style="padding-left: 0px;">
                                        <div class="float-left mt-1">
                                            <a href="documEmpleado/${value}" target="_blank" class="p-2">
                                                <i class="uil-download-alt font-size-18"></i>
                                            </a>
                                            &nbsp;
                                            <a href="documEmpleado/${value}" target="_blank" class="d-inline-block mt-2" style="color:#000000">
                                                <span class="d-inline-block text-truncate" style="max-width: 100px;font-size:12px">${mostrarC}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>`;
                });
                $('#reg_documentosxDetalle').append(itemsD);
            }
            $('#form-registrar').modal('hide');
            $('#detallesContratomodal').modal();

        },
        error: function () { }
    });
}
//* MODAL DE NUEVA ALTA 
function modalNuevaAltaReg() {
    $('#contratoDetallesmodal').modal();
    $('#form-registrar').modal('hide');
    limpiarNuevosDatosAltaReg();
    validacionNuevaAltaReg();
}
//* VALIDACION DE NUEVA ALTA
function validacionNuevaAltaReg() {
    if ($('#contrato').val() != "") {
        $('#condicion').prop("disabled", false);
        $('#monto').prop("disabled", false);
        $('#m_dia_fecha').prop("disabled", false);
        $('#m_mes_fecha').prop("disabled", false);
        $('#m_ano_fecha').prop("disabled", false);
        $('#reg_fileArchivos').prop("disabled", false);
        $('#checkboxFechaI').prop("disabled", false);
        $('#mf_dia_fecha').prop("disabled", false);
        $('#mf_mes_fecha').prop("disabled", false);
        $('#mf_ano_fecha').prop("disabled", false);
        $('#reg_guardarAlta').prop("disabled", false);
    } else {
        $('#condicion').prop("disabled", true);
        $('#monto').prop("disabled", true);
        $('#m_dia_fecha').prop("disabled", true);
        $('#m_mes_fecha').prop("disabled", true);
        $('#m_ano_fecha').prop("disabled", true);
        $('#reg_fileArchivos').prop("disabled", true);
        $('#checkboxFechaI').prop("disabled", true);
        $('#mf_dia_fecha').prop("disabled", true);
        $('#mf_mes_fecha').prop("disabled", true);
        $('#mf_ano_fecha').prop("disabled", true);
        $('#reg_guardarAlta').prop("disabled", true);
    }
}
//* LIMPIAR FORMULARIO EN NUEVA ALTA
function limpiarNuevosDatosAltaReg() {
    $('#contrato').val("");
    $('#condicion').val("");
    $('#monto').val("");
    $('#m_dia_fecha').val(0);
    $('#m_mes_fecha').val(0);
    $('#m_ano_fecha').val(0);
    $('#reg_fileArchivos').val(null);
    $('.iborrainputfile').val("Adjuntar archivo");
    $('#checkboxFechaI').prop("checked", false);
    $('#mf_dia_fecha').val(0);
    $('#mf_mes_fecha').val(0);
    $('#mf_ano_fecha').val(0);
    $('#ocultarFecha').show();
}
//* CHECKBOX DE FECHA INDEFINIDA
$("#checkboxFechaI").on("click", function () {
    if ($("#checkboxFechaI").is(':checked')) {
        $('#mf_dia_fecha').val(0);
        $('#mf_mes_fecha').val(0);
        $('#mf_ano_fecha').val(0);
        $('#ocultarFecha').hide();
    } else {
        $('#ocultarFecha').show();
    }
});
//* CHECKBOX DE FECHA INDEFINIDA EN DETALLES DE CONTRATO
$("#checkboxFechaID").on("click", function () {
    if ($("#checkboxFechaID").is(':checked')) {
        $('#mf_dia_fechaD').val(0);
        $('#mf_mes_fechaD').val(0);
        $('#mf_ano_fechaD').val(0);
        $('#ocultarFechaD').hide();
    } else {
        $('#ocultarFechaD').show();
    }
});
//* VALIDACION DE ARCHIVOS EN BAJA
async function validArchivosReg() {
    var respuesta = true;
    $.each($('#reg_fileArchivos'), function (i, obj) {
        $.each(obj.files, function (j, file) {
            var fileSize = file.size;
            var sizeKiloBytes = parseInt(fileSize);
            if (sizeKiloBytes > parseInt($('#reg_fileArchivos').attr('size'))) {
                respuesta = false;
            }
        });
    });
    return respuesta;
}
$('#reg_fileArchivos').on("click", function () {
    $('#validArchivoReg').hide();
});
//* REGISTRAR ARCHIVO EN NUEVA ALTA
function archivosRegistrar(id) {
    //* AJAX DE ARCHICOS
    var formData = new FormData();
    $.each($('#reg_fileArchivos'), function (i, obj) {
        $.each(obj.files, function (j, file) {
            formData.append('file[' + j + ']', file);
        })
    });
    $.ajax({
        contentType: false,
        processData: false,
        type: "POST",
        url: "/archivosEditC/" + id,
        data: formData,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            historialEmpReg();
        },
        error: function () {
        },
    });
}
//* FUNCION PARA VALIDAR FECHA INICIO
async function validarFechaInicioAltaReg(fechaInicio, idEmpleado) {
    var resultado = {};
    $.ajax({
        async: false,
        type: "POST",
        url: "/validFechaAlta",
        data: {
            empleado: idEmpleado
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
            if (data.length != 0) {
                var momentFechaInicio = moment(fechaInicio);
                var momentFechaAnterior = moment(data[0].fechaFinal);
                if (momentFechaInicio.isBefore(momentFechaAnterior)) {
                    resultado = { "respuesta": false, "fecha": data[0].fechaFinal };
                }
            }
        },
        error: function () { }
    });

    return resultado;
}
//* REGISTRAR NUEVA ALTA
async function nuevaAltaReg() {
    var contrato = $('#contrato').val();
    var condicionPago = $('#condicion').val();
    var monto = $('#monto').val();
    var fechaInicial;
    var fechaFinal = "0000-00-00";
    var idEmpleado = $("#idEmpleado").val();

    //* FUNCIONES DE FECHAS
    var m_AnioIE = parseInt($('#m_ano_fecha').val());
    var m_MesIE = parseInt($('#m_mes_fecha').val() - 1);
    var m_DiaIE = parseInt($('#m_dia_fecha').val());
    var m1_VFechaIE = moment([m_AnioIE, m_MesIE, m_DiaIE]);
    if (m1_VFechaIE.isValid()) {
        $('#m_validFechaC').hide();
    } else {
        $('#m_validFechaC').show();
        return false;
    }
    if (m_AnioIE != 0 && m_MesIE != -1 && m_DiaIE != 0) {
        fechaInicial = moment([m_AnioIE, m_MesIE, m_DiaIE]).format('YYYY-MM-DD');
    } else {
        fechaInicial = '0000-00-00';
    }
    if (!$("#checkboxFechaI").is(':checked')) {
        var mf_AnioFE = parseInt($('#mf_ano_fecha').val());
        var mf_MesFE = parseInt($('#mf_mes_fecha').val() - 1);
        var mf_DiaFE = parseInt($('#mf_dia_fecha').val());
        var m1f_VFechaFE = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]);
        if (m1f_VFechaFE.isValid()) {
            $('#mf_validFechaC').hide();
        } else {
            $('#mf_validFechaC').show();
            return false;

        }
        var momentInicio = moment([m_AnioIE, m_MesIE, m_DiaIE]);
        var momentFinal = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]);
        if (!momentInicio.isBefore(momentFinal)) {
            $('#mf_validFechaC').show();
            return;
        } else {
            $('#mf_validFechaC').hide();
        }
        if (mf_AnioFE != 0 && mf_MesFE != -1 && mf_DiaFE != 0) {
            fechaFinal = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]).format('YYYY-MM-DD');
        } else {
            fechaFinal = '0000-00-00';
        }
    }
    var fechaAlta = fechaInicial;
    //* FUNCIONES DE VALIDAR ARCHIVO
    const result = await validArchivosReg();
    if (!result) {
        $('#validArchivoReg').show();
        return false;
    } else {
        $('#validArchivoReg').hide();
    }
    //* FUNCIONES DE VALIDAR FECHA INICIO
    const respFecha = await validarFechaInicioAltaReg(fechaInicial, idEmpleado);
    if (respFecha.respuesta != undefined) {
        if (!respFecha.respuesta) {
            $('#alertErrorFechaReg').empty();
            var errorAlert = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                                <span style="font-size: 14px;">Su fecha inicial debe ser mayor a la fecha de baja de su contrato anterior ${moment(respFecha.fecha).lang('es').format("DD MMMM YYYY")}</span>`;
            $('#alertErrorFechaReg').append(errorAlert);
            $('#alertErrorFechaReg').show();
            return false;
        } else {
            $('#alertErrorFechaReg').hide();
        }
    }
    //* *******************************FINALIZACION ******************************************
    //* AJAX DE NUEVA ALTA
    $.ajax({
        type: "POST",
        url: "/nuevaAlta",
        data: {
            contrato: contrato,
            fechaAlta: fechaAlta,
            condicionPago: condicionPago,
            monto: monto,
            fechaInicial: fechaInicial,
            fechaFinal: fechaFinal,
            idEmpleado: idEmpleado
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
            //* REGISTRAR ARCHIVOS EN NUEVA ALTA
            archivosRegistrar(data);
            $('#contratoDetallesmodal').modal('toggle');
            $('#form-registrar').modal('show');
            historialEmpReg();
            $.notifyClose();
            $.notify(
                {
                    message: "\nRegisto exitoso.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    element: $('#form-registrar'),
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () { }
    });
}
//* ELIMINAR CONTRATO
function eliminarContratoReg(id) {
    //* DESICION DE ELIMINAR CONTRATO
    alertify
        .confirm("¿Desea eliminar contrato?", function (
            e
        ) {
            if (e) {
                $.ajax({
                    type: "POST",
                    url: "/eliminarHistorialC",
                    data: {
                        id: id
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (data) {
                        historialEmpReg();
                        $.notifyClose();
                        $.notify({
                            message: '\nContrato eliminado',
                            icon: 'landing/images/bell.svg',
                        }, {
                            element: $('#form-registrar'),
                            icon_type: 'image',
                            allow_dismiss: true,
                            newest_on_top: true,
                            delay: 6000,
                            template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                                '</div>',
                            spacing: 35
                        });
                    },
                    error: function () { },
                });
            }
        })
        .setting({
            title: "Eliminar contrato",
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
                historialEmpReg();
            },
        });
}
//* FUNCION DE MOSTRAR MODAL DE DECICION DE BAJA
function bajaEmpleadoContratoReg(id) {
    $('#modalBajaHistorialReg').modal();
    $('#idHistorialReg').val(id);
    var fechaValueReg = $("#fechaBajaReg").flatpickr({
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
        fechaValueReg.setDate(f);
    });
}
function cerrarModalHistorialReg() {
    $('#modalBajaHistorialReg').modal('toggle');
    $('#form-registrar').modal('show');
}
//* VALIDACION DE ARCHIVOS EN BAJA
async function validArchivosBajaReg() {
    var respuesta = true;
    $.each($('#bajaFileReg'), function (i, obj) {
        $.each(obj.files, function (j, file) {
            var fileSize = file.size;
            var sizeKiloBytes = parseInt(fileSize);
            if (sizeKiloBytes > parseInt($('#bajaFileReg').attr('size'))) {
                respuesta = false;
            }
        });
    });
    return respuesta;
}
$('#bajaFileReg').on("click", function () {
    $('#validArchivoBajaReg').hide();
});
//* FUNCION DE GUARDAR ARCHIVOS DE BAJA 
function archivosDeBajaReg(id) {
    //* AJAX DE ARCHICOS
    var formData = new FormData();
    $.each($('#bajaFileReg'), function (i, obj) {
        $.each(obj.files, function (j, file) {
            formData.append('file[' + j + ']', file);
        })
    });
    $.ajax({
        contentType: false,
        processData: false,
        type: "POST",
        url: "/archivosEditC/" + id,
        data: formData,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            historialEmpReg();
        },
        error: function () {
        },
    });
}
//* FUNCION GUARDAR DATOS DE BAJA
async function confirmarBajaHistorialReg() {
    const result = await validArchivosBajaReg();
    if (!result) {
        $('#validArchivoBajaReg').show();
        return false;
    } else {
        $('#validArchivoBajaReg').hide();
    }
    var id = $('#idHistorialReg').val();
    var fechaBaja = $('#fechaBajaInputReg').val();
    $.ajax({
        type: "POST",
        url: "/bajaHistorial",
        data: {
            id: id,
            fechaBaja: fechaBaja
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
            if (data.respuesta != false) {
                $('#alertFechaBajaReg').hide();
                archivosDeBajaReg(data);
                $('#modalBajaHistorialReg').modal('toggle');
                $('#form-registrar').modal('show');
                historialEmpReg();
            } else {
                $('#alertFechaBajaReg').empty();
                var errorAlert = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                                <span style="font-size: 14px;">Su fecha de baja debe ser mayor a la fecha de alta de su contrato ${moment(data.fecha).lang('es').format("DD MMMM YYYY")}</span>`;
                $('#alertFechaBajaReg').append(errorAlert);
                $('#alertFechaBajaReg').show();
            }
        },
        error: function () { }
    });
}
//* VALIDACION DE ARCHIVOS EN EDITAR DETALLES DE CONTROL
async function validArchivosDetalleReg() {
    var respuesta = true;
    $.each($('#reg_fileArchivosD'), function (i, obj) {
        $.each(obj.files, function (j, file) {
            var fileSize = file.size;
            var sizeKiloBytes = parseInt(fileSize);
            if (sizeKiloBytes > parseInt($('#reg_fileArchivosD').attr('size'))) {
                respuesta = false;
            }
        });
    });
    return respuesta;
}
//* LIMPIAR VALIDACION DE ARCHIVO
$('#reg_fileArchivosD').on("click", function () {
    $('#validArchivoD').hide();
});
//* FUNCION DE GUARDAR ARCHIVOS DE DETALLES DE CONTRATO
function archivosDeDetalleCReg(id) {
    //* AJAX DE ARCHICOS
    var formData = new FormData();
    $.each($('#reg_fileArchivosD'), function (i, obj) {
        $.each(obj.files, function (j, file) {
            formData.append('file[' + j + ']', file);
        })
    });
    $.ajax({
        contentType: false,
        processData: false,
        type: "POST",
        url: "/archivosEditC/" + id,
        data: formData,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            historialEmpReg();
        },
        error: function () {
        },
    });
}
//* FUNCION PARA VALIDAR FECHA INICIO
async function validarFechaInicioReg(fechaInicio, idContrato) {
    var resultado = {};
    $.ajax({
        async: false,
        type: "POST",
        url: "/validFechaDetalle",
        data: {
            contrato: idContrato
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
            if (data.length != 0) {
                var momentFechaInicio = moment(fechaInicio);
                var momentFechaAnterior = moment(data[0].fechaFinal);
                if (momentFechaInicio.isBefore(momentFechaAnterior)) {
                    resultado = { "respuesta": false, "fecha": data[0].fechaFinal };
                }
            }
        },
        error: function () { }
    });

    return resultado;
}
//* EDITAR DETALLES DE CONTRATO
async function editarDetalleCReg() {
    var idContrato = $('#reg_idContratoD').val();
    var condicionPago = $('#condicionD').val();
    var monto = $('#montoD').val();
    var fechaInicial;
    var fechaFinal = "0000-00-00";

    //* VALIDACION DE FECHAS DE INICIO Y FINAL
    var m_AnioIE = parseInt($('#m_ano_fechaD').val());
    var m_MesIE = parseInt($('#m_mes_fechaD').val() - 1);
    var m_DiaIE = parseInt($('#m_dia_fechaD').val());
    var m1_VFechaIE = moment([m_AnioIE, m_MesIE, m_DiaIE]);
    if (m1_VFechaIE.isValid()) {
        $('#m_validFechaCD').hide();
    } else {
        $('#m_validFechaCD').show();
        return false;
    }
    if (m_AnioIE != 0 && m_MesIE != -1 && m_DiaIE != 0) {
        fechaInicial = moment([m_AnioIE, m_MesIE, m_DiaIE]).format('YYYY-MM-DD');
    } else {
        fechaInicial = '0000-00-00';
    }
    if (!$("#checkboxFechaID").is(':checked')) {
        var mf_AnioFE = parseInt($('#mf_ano_fechaD').val());
        var mf_MesFE = parseInt($('#mf_mes_fechaD').val() - 1);
        var mf_DiaFE = parseInt($('#mf_dia_fechaD').val());
        var m1f_VFechaFE = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]);
        if (m1f_VFechaFE.isValid()) {
            $('#mf_validFechaCD').hide();
        } else {
            $('#mf_validFechaCD').show();
            return false;

        }
        var momentInicio = moment([m_AnioIE, m_MesIE, m_DiaIE]);
        var momentFinal = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]);
        if (!momentInicio.isBefore(momentFinal)) {
            $('#mf_validFechaCD').show();
            return;
        } else {
            $('#mf_validFechaCD').hide();
        }
        if (mf_AnioFE != 0 && mf_MesFE != -1 && mf_DiaFE != 0) {
            fechaFinal = moment([mf_AnioFE, mf_MesFE, mf_DiaFE]).format('YYYY-MM-DD');
        } else {
            fechaFinal = '0000-00-00';
        }
    }
    //* FUNCIONES DE VALIDAR ARCHIVO
    const result = await validArchivosDetalleReg();
    if (!result) {
        $('#validArchivoD').show();
        return false;
    } else {
        $('#validArchivoD').hide();
    }
    //* ***********************************************
    //* FUNCIONES DE VALIDAR FECHA INICIO
    const respFecha = await validarFechaInicioReg(fechaInicial, idContrato);
    if (respFecha.respuesta != undefined) {
        if (!respFecha.respuesta) {
            $('#alertErrorFechaDetalleReg').empty();
            var errorAlert = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                                <span style="font-size: 14px;">Su fecha inicial debe ser mayor a la fecha de baja de su contrato anterior ${moment(respFecha.fecha).lang('es').format("DD MMMM YYYY")}</span>`;
            $('#alertErrorFechaDetalleReg').append(errorAlert);
            $('#alertErrorFechaDetalleReg').show();
            return false;
        } else {
            $('#alertErrorFechaDetalleReg').hide();
        }
    }
    //* *****************************************
    var fechaAlta = fechaInicial;
    var fechaBaja = (fechaFinal == '0000-00-00' || BajaEmpReg == true) ? null : fechaFinal;
    //* AJAX DE EDITAR
    $.ajax({
        type: "POST",
        url: "/editDetalleC",
        data: {
            idContrato: idContrato,
            fechaAlta: fechaAlta,
            fechaBaja: fechaBaja,
            condicionPago: condicionPago,
            monto: monto,
            fechaInicial: fechaInicial,
            fechaFinal: fechaFinal
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
            archivosDeDetalleCReg(idContrato);
            historialEmpReg();
            $.notifyClose();
            $.notify(
                {
                    message: "\nDatos Modificados.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    element: $('#form-registrar'),
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () { }
    });

    $('#detallesContratomodal').modal('toggle');
    $('#form-registrar').modal('show');
}
//? ********************FINALIZACION********************* *//
//? ********************FORMULARIO VER EMPLEADO********************* *//
//* CARGAR DATA EN TABLA CONTRATO
function historialEmpVer() {
    var value = $('#v_idV').val();
    $("#reg_tbodyHistorial").empty();
    $.ajax({
        async: false,
        type: "POST",
        url: "/empleado/historial",
        data: {
            idempleado: value
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
            var container = $('#ver_tbodyHistorial');
            if (data.length != 0) {
                altaEmpleadoReg = true;
                for (var i = 0; i < data.length; i++) {
                    var trReg = `<tr>`;
                    trReg += `<td style="vertical-align:middle;">
                                        <img src="landing/images/arriba.svg" height="17"> &nbsp;${moment(data[i].fecha_alta).format('DD/MM/YYYY')}
                                        &nbsp;&nbsp;`;
                    if (data[i].fecha_baja != null) {
                        trReg += `<img src="landing/images/abajo.svg" height="17"> &nbsp;${moment(data[i].fecha_baja).format('DD/MM/YYYY')}`;
                    } else {
                        trReg += `<img src="landing/images/abajo.svg" height="17"> &nbsp;------`;
                    }
                    trReg += `</td>`;
                    if (data[i].contrato == null) {
                        trReg += `<td>--</td> `;
                    } else {
                        trReg += `<td> ${data[i].contrato}</td> `;
                    }
                    if (data[i].rutaDocumento != null) {
                        var valores = data[i].rutaDocumento;
                        //* SEPARAMOS CADENAS
                        idsV = valores.split(',');
                        trReg += `<td>
                        <div class="dropdown" id="documentosVer${i}">
                            <a class="dropdown" data-toggle="dropdown" aria-expanded="false"
                                style="cursor: pointer">
                                <span class="badge badge-soft-primary text-primary">
                                    <i class="uil-file-alt font-size-17"></i>
                                </span>
                                &nbsp;
                                Documentos
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">`;
                        $.each(idsV, function (index, value) {
                            var mostrarC = value.substr(13, value.length);
                            trReg += `<div class="dropdown-item">
                                        <div class="col-xl-12" style="padding-left: 0px;">
                                            <div class="float-left mt-1">
                                                <i class="uil-download-alt font-size-18"></i>
                                                &nbsp;
                                                <span class="d-inline-block text-truncate" style="max-width: 150px;">${mostrarC}</span>
                                            </div>
                                        </div>
                                    </div>`;
                        });
                        trReg += `</ul></div></td> `;
                    } else {
                        trReg += `<td> --</td> `;
                    }
                    trReg += `<td>
                                <a onclick="javascript:mostrarDetallesContratoVer(${data[i].idContrato})" data-toggle="tooltip" data-placement="right"
                                    title="Detalle de Contrato" data-original-title="Detalle de Contrato" style="cursor: pointer;">
                                    <img src="landing/images/adaptive.svg" height="18">
                                </a>`;
                    trReg += '</td>';
                    trReg += '</tr>';
                    container.append(trReg);
                }
            }
        },
        error: function () { }
    });
}
//* FUNCION MOSTRAR DETALLES DE CONTRATO
function mostrarDetallesContratoVer(id) {
    $.ajax({
        async: false,
        type: "GET",
        url: "/detalleC",
        data: {
            id: id
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
            if (data.estado == 0) {
                $('.ocultarFechaIV').hide();
            } else {
                $('.ocultarFechaIV').show();
            }
            $('#v_contratoV').val(data.tipoContrato);
            $('#v_condicionV').val(data.condPago);
            $('#v_montoV').val(data.monto);
            var VFechaDaIE = moment(data.fechaInicio).format('YYYY-MM-DD');
            var VFechaDiaIE = new Date(moment(VFechaDaIE));
            $('#m_dia_fechaIEV').val(VFechaDiaIE.getDate());
            $('#m_mes_fechaIEV').val(moment(VFechaDaIE).month() + 1);
            $('#m_ano_fechaIEV').val(moment(VFechaDaIE).year());
            $("#checkboxFechaIEV").prop('checked', false);
            $('#ocultarFechaEV').show();
            if (data.fechaFinal == null || data.fechaFinal == "0000-00-00") {
                $("#checkboxFechaIEV").prop('checked', true);
                $('#ocultarFechaEV').hide();
            }
            var VFechaDaFE = moment(data.fechaFinal).format('YYYY-MM-DD');
            var VFechaDiaFE = new Date(moment(VFechaDaFE));
            $('#m_dia_fechaFEV').val(VFechaDiaFE.getDate());
            $('#m_mes_fechaFEV').val(moment(VFechaDaFE).month() + 1);
            $('#m_ano_fechaFEV').val(moment(VFechaDaFE).year());
            $('#verEmpleadoDetalles').modal('hide');
            $('#fechasmodalVer').modal();

        },
        error: function () { }
    });
}