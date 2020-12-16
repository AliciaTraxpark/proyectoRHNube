//* ********************FORMULARIO EDITAR ********************* *//
//: FECHA DE BAJA EN PESTAÑA DE CONTRATO
var fechaValue = $("#fechaBajaEdit").flatpickr({
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
//: FECHA DE ALTA EN PESTAÑA EDIT
var fechaValueAlta = $("#fechaAltaEdit").flatpickr({
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
    fechaValueAlta.setDate(f);
});
var altaEmpleado = true;
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

                    itemsD += `<div class="dropdown-item">
                                        <div class="col-xl-12" style="padding-left: 0px;">
                                            <div class="media-body">
                                                <a href="documEmpleado/${value}" target="_blank" class="d-inline-block mt-2"><span style="vertical-align:middle;">${value}</span></a>
                                            </div>
                                            <div class="float-right mt-1">
                                                <a href="documEmpleado/'${value}" target="_blank" class="p-2">
                                                    <i class="uil-download-alt font-size-18"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>`;
                });
                $('#documentosxDetalle').append(itemsD);
            }
            $('#form-ver').modal('hide');
            $('#contratoDetallesmodalE').modal();

        },
        error: function () { }
    });
}
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
                for (var i = 0; i < data.length; i++) {
                    var trVer = '<tr>';
                    trVer += `<td style="vertical-align:middle;">
                                        <img src="landing/images/arriba.svg" height="17"> &nbsp;${moment(data[i].fecha_alta).format('DD/MM/YYYY')}
                                        &nbsp;&nbsp;`;
                    if (data[i].fecha_baja != null) {
                        trVer += `<img src="landing/images/abajo.svg" height="17"> &nbsp;${moment(data[i].fecha_baja).format('DD/MM/YYYY')}`;
                    } else {
                        trVer += `<img src="landing/images/abajo.svg" height="17"> &nbsp;------`;
                    }
                    trVer += `</td>`;
                    trVer += `<td> ${data[i].contrato}</td> `;
                    if (data[i].rutaDocumento != null) {
                        var valores = data[i].rutaDocumento;
                        //* SEPARAMOS CADENAS
                        idsV = valores.split(',');
                        trVer += `<td>
                        <div class="dropdown" id="documentos${i}">
                            <a class="dropdown" data-toggle="dropdown" aria-expanded="false"
                                style="cursor: pointer">
                                <div class="avatar-sm font-weight-bold mr-3">
                                    <span class="avatar-title rounded bg-soft-primary text-primary">
                                        <i class="uil-file-plus-alt font-size-18"></i>
                                    </span>
                                </div>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">`;
                        $.each(idsV, function (index, value) {
                            trVer += `<div class="dropdown-item">
                                        <div class="col-xl-12" style="padding-left: 0px;">
                                            <div class="media-body">
                                                <a href="documEmpleado/${value}" target="_blank" class="d-inline-block mt-2"><span style="vertical-align:middle;">${value}</span></a>
                                            </div>
                                            <div class="float-right mt-1">
                                                <a href="documEmpleado/'${value}" target="_blank" class="p-2">
                                                    <i class="uil-download-alt font-size-18"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>`;
                        });
                        trVer += `</ul></div></td> `;
                    } else {
                        trVer += `<td> --</td> `;
                    }
                    trVer += `<td>
                        <a onclick="javascript:mostrarDetallesContrato(${data[i].idContrato})" data-toggle="tooltip" data-placement="right"
                            title="Detalle de Contrato" data-original-title="Detalle de Contrato" style="cursor: pointer;">
                            <img src="landing/images/adaptive.svg" height="18">
                                </a>`;
                    if (data[i].fecha_baja === null) {
                        altaEmpleado = false;
                        trVer += `&nbsp;
                                <a data-toggle="tooltip" name="dBajaName" data-original-title="Dar de baja" data-placement="right"
                                    onclick="javascript:bajaEmpleadoContrato(${data[i].id});$('#form-ver').modal('hide');" style="cursor: pointer">
                                    <img src="landing/images/abajo.svg" height="17">
                                </a>`;
                    }
                    trVer += '</td>';
                    trVer += '</tr>';
                    containerVer.append(trVer);
                    mostrarBoton();
                }
            }
        },
        error: function () { }
    });
}
//* MOSTRAR BOTON
function mostrarBoton() {
    if (altaEmpleado) {
        $('#nuevaAltaEdit').show();
    } else {
        $('#nuevaAltaEdit').hide();
    }
}
function bajaEmpleadoContrato(id) {
    $('#modalBajaHistorial').modal();
    $('#idHistorialEdit').val(id);
}

function confirmarBajaHistorial() {
    var id = $('#idHistorialEdit').val();
    var fechaBaja = $('#fechaBajaInput').val();
    var bajaFile = $('#bajaFileEdit').val();
    $("#editar_tbodyHistorial").empty();
    $.ajax({
        type: "POST",
        url: "/bajaHistorial",
        data: {
            id: id,
            fechaBaja: fechaBaja,
            bajaFile: bajaFile
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
            historialEmp();
            $('#modalBajaHistorial').modal('toggle');
            $('#form-ver').show();
        },
        error: function () { }
    });
}

function cerrarModalHistorial() {
    $('#form-ver').show();
    $('#modalBajaHistorial').modal('toggle');
}
//* EMPLEADO ACTUALIZAR
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
//TODO ->EDITAR DETALLES DE CONTRATO
function editarDetalleCE() {
    var idContrato = $('#idContratoD').val();
    var fechaAlta = $('#fechaAltaInput').val();
    var condicionPago = $('#v_condicion').val();
    var monto = $('#v_monto').val();
    var fechaInicial;
    var fechaFinal = "0000-00-00";

    //* FUNCIONES DE FECHAS
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
    //* AJAX DE EDITAR
    $.ajax({
        type: "POST",
        url: "/editDetalleC",
        data: {
            idContrato: idContrato,
            fechaAlta: fechaAlta,
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
// TODO -> ARCHIVO EDIT
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
//: FECHA ALTA DE NUEVA ALTA
var fechaValueA = $("#fechaAltaEditN").flatpickr({
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
    fechaValueA.setDate(f);
});
var modalA;
//TODO -> FUNCION DE CERRAR Y ABRIR DE MODAL
function ModalAbiertoCondicion() {
    if ($('#contratoDetallesmodalE').is(':visible')) {
        $('#contratoDetallesmodalE').modal('hide');
        modalA = 1;
    } else {
        $('#contratoDetallesmodalEN').modal('hide');
        modalA = 2;
    }

}
function ModalCerrarCondicion() {
    if (modalA === 1) {
        $('#contratoDetallesmodalE').modal('show');
    } else {
        $('#contratoDetallesmodalEN').modal('show');
    }
}
// TODO -> ARCHIVO NUEVO
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
//TODO -> NUEVA ALTA
function nuevaAltaEditar() {
    var contrato = $('#v_contratoN').val();
    var fechaAlta = $('#fechaAltaInputN').val();
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
//* NUEVA ALTA EN EMPLEADO ACTUALIZAR
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
//TODO -> LIMPIAR
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
    $('#contratoDetallesmodalEN').modal('toggle');
}
//TODO -> VALIDACION DE NUEVA ALTA
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

//* ************************FINALIZACION********************** **//