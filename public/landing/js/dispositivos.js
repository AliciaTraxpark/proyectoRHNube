//EDITAR
$('#customSwitchC1').prop('checked', true);
$('#bodyModoControlR').show();
$('#bodyModoControlRuta').hide();
$('#customSwitchC1').on('change.bootstrapSwitch', function (event) {
    if (event.target.checked == true) {
        $('#bodyModoControlR').show();
    } else {
        $('#bodyModoControlR').hide();
    }
});
$('#customSwitchC2').on('change.bootstrapSwitch', function (event) {
    if (event.target.checked == true) {
        $('#bodyModoControlRuta').show();
    } else {
        $('#bodyModoControlRuta').hide();
    }
});
// EN REGISTRAR
$('#customSwitchCR1').prop('checked', true);
$('#bodyModoControlRR').show();
$('#bodyModoControlAR').hide();
$('#customSwitchCR1').on('change.bootstrapSwitch', function (event) {
    if (event.target.checked == true) {
        $('#bodyModoControlRR').show();
    } else {
        $('#bodyModoControlRR').hide();
    }
});
$('#customSwitchCR2').on('change.bootstrapSwitch', function (event) {
    if (event.target.checked == true) {
        $('#bodyModoControlAR').show();
    } else {
        $('#bodyModoControlAR').hide();
    }
});
// VER
$('#customSwitchCV1').prop('checked', true);
$('#bodyModoControlRV').show();
$('#bodyModoControlAV').hide();
$('#customSwitchCV1').on('change.bootstrapSwitch', function (event) {
    if (event.target.checked == true) {
        $('#bodyModoControlRV').show();
    } else {
        $('#bodyModoControlRV').hide();
    }
});
$('#customSwitchCV2').on('change.bootstrapSwitch', function (event) {
    if (event.target.checked == true) {
        $('#bodyModoControlAV').show();
    } else {
        $('#bodyModoControlAV').hide();
    }
});
// FUNCIONES DE DISPOSITIVOS
function inactivarLicenciaW(id) {
    $('#estadoLicenciaW').val(id);
    $('#form-registrar').hide();
    $('#estadoLicenciaW').modal();
}

function cambiarEstadoLicenciaWindows() {
    var idEmpleado = $('#idEmpleado').val();
    var idVinculacion = $('#estadoLicenciaW').val();
    //NOTIFICACION
    $.ajax({
        async: false,
        type: "get",
        url: "/cambiarEstadoLicencia",
        data: {
            idE: idEmpleado,
            idV: idVinculacion
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
            $('#correo' + idVinculacion).empty();
            $('#inactivar' + idVinculacion).empty();
            $('#estado' + idVinculacion).empty();
            var td = `<a  onclick="javascript:modalWindows(${idVinculacion});$('#form-registrar').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
            correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                src="landing/images/email (4).svg" height="20">
            </a>`;
            var tdE = `Inactivo`;
            var tdI = ``;
            $('#correo' + idVinculacion).append(td);
            $('#inactivar' + idVinculacion).append(tdI);
            $('#estado' + idVinculacion).append(tdE);
            $('#estadoLicenciaW').modal('toggle');
            $('#form-registrar').show();
            $.notifyClose();
            $.notify({
                message: "\nProceso con éxito.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-registrar'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function () {
            $('#estadoLicenciaW').modal('toggle');
            $('#form-registrar').show();
            $.notifyClose();
            $.notify({
                message: "\nAún no ha registrado correo a empleado.",
                icon: 'admin/images/warning.svg'
            }, {
                element: $('#form-registrar'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        }
    });
}
$('#CambiarEstadoLW').on("click", cambiarEstadoLicenciaWindows);
//EDITAR
function inactivarLicenciaWEditar(id) {
    $('#estadoLicenciaW').val(id);
    $('#form-ver').hide();
    $('#v_estadoLicenciaW').modal();
}

function cambiarEstadoLicenciaWindowsEditar() {
    var idEmpleado = $('#v_id').val();
    var idVinculacion = $('#estadoLicenciaW').val();
    //NOTIFICACION
    $.ajax({
        async: false,
        type: "get",
        url: "/cambiarEstadoLicencia",
        data: {
            idE: idEmpleado,
            idV: idVinculacion
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
            $('#correo' + idVinculacion).empty();
            $('#inactivar' + idVinculacion).empty();
            $('#estado' + idVinculacion).empty();
            var td = `<a  onclick="javascript:modalWindowsEditar(${idVinculacion});$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
            correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                src="landing/images/email (4).svg" height="20">
            </a>`;
            var tdE = `Inactivo`;
            $('#correo' + idVinculacion).append(td);
            $('#inactivar' + idVinculacion).append(``);
            $('#estado' + idVinculacion).append(tdE);
            $('#v_estadoLicenciaW').modal('toggle');
            $('#form-ver').show();
            $.notifyClose();
            $.notify({
                message: "\nProceso con éxito.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-ver'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function () {
            $('#v_estadoLicenciaW').modal('toggle');
            $('#form-ver').show();
            $.notifyClose();
            $.notify({
                message: "\nAún no ha registrado correo a empleado.",
                icon: 'admin/images/warning.svg'
            }, {
                element: $('#form-registrar'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        }
    });
}
$('#v_CambiarEstadoLW').on("click", cambiarEstadoLicenciaWindowsEditar);
//*************************************************************************** */
$('#tbodyDispositivo').empty();
$('#v_tbodyDispositivo').empty();
$('#v_tbodyDispositivoA').empty();
//WINDOWS
//MODAL WINDOWS
function modalWindows(id) {
    $('#windows').val(id);
    $('#windowsEmpleado').modal();

}

function vinculacionWindows() {
    var idEmpleado = $('#idEmpleado').val();
    $.ajax({
        async: false,
        type: "get",
        url: "vinculacionWindows",
        data: {
            idEmpleado: idEmpleado
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            if (data == 1) {
                $.notifyClose();
                $.notify({
                    message: "\nLlego al limite de dispositivos Windows",
                    icon: 'admin/images/warning.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            } else {
                var container = $('#tbodyDispositivo');
                var tr = `<tr>
                <td>${data.dispositivo_descripcion}</td>
                <td> PC ${data.contar}</td>
                <td>${data.licencia}</td>
                <td class="hidetext">${data.codigo}</td>
                <td id="enviadoW${data.idVinculacion}">${data.envio}</td>
                <td id="estado${data.idVinculacion}">Creado</td>
                <td id="correo${data.idVinculacion}">
                    <a  onclick="javascript:modalWindows(${data.idVinculacion});$('#form-registrar').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                    correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                        src="landing/images/note.svg" height="20">
                    </a>
                </td>
                <td id="inactivar${data.idVinculacion}"><a onclick="javascript:inactivarLicenciaW(${data.idVinculacion})" class="badge badge-soft-danger mr-2">Inactivar</a></td>
                </tr>`;
                container.append(tr);
            }
        },
        error: function () { }
    });
}
$('#agregarWindows').on("click", vinculacionWindows);

function enviarCorreoWindows() {
    var idEmpleado = $('#idEmpleado').val();

    var idVinculacion = $('#windows').val();
    $.ajax({
        async: false,
        type: "get",
        url: "correoWindows",
        data: {
            idEmpleado: idEmpleado,
            idVinculacion: idVinculacion
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            $('#windowsEmpleado').modal('toggle');
            $('#form-registrar').show();
            var container = $('#enviadoW' + idVinculacion);
            container.empty();
            var td = `<td>${data.envio}</td>`;
            if (data.disponible == 'e') {
                var cont = $('#estado' + idVinculacion);
                cont.empty();
                var tdE = `<td>Enviado</td>`
                cont.append(tdE);
            }
            container.append(td);
            $.notifyClose();
            $.notify({
                message: "\nCorreo Enviado.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-registrar'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function () { }
    });
}
$('#enviarCorreoWindowsEmpleado').on("click", enviarCorreoWindows);
//EDITAR
function modalWindowsEditar(id) {
    $('#windows').val(id);
    $('#v_windowsEmpleado').modal();

}

function vinculacionWindowsEditar() {
    var idEmpleado = $('#v_id').val();
    $.ajax({
        async: false,
        type: "get",
        url: "vinculacionWindows",
        data: {
            idEmpleado: idEmpleado
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            if (data == 1) {
                $.notifyClose();
                $.notify({
                    message: "\nLlego al limite de dispositivos Windows",
                    icon: 'admin/images/warning.svg'
                }, {
                    element: $('#form-ver'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            } else {
                var container = $('#v_tbodyDispositivo');
                var tr = `<tr>
                <td>${data.dispositivo_descripcion}</td>
                <td> PC ${data.contar}</td>
                <td>${data.licencia}</td>
                <td class="hidetext">${data.codigo}</td>
                <td id="enviadoW${data.idVinculacion}">${data.envio}</td>
                <td id="estado${data.idVinculacion}">Creado</td>
                <td id="correo${data.idVinculacion}">
                    <a  onclick="javascript:modalWindowsEditar(${data.idVinculacion});$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                    correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                        src="landing/images/note.svg" height="20">
                </a>
                </td>
                <td id="inactivar${data.idVinculacion}"><a onclick="javascript:inactivarLicenciaWEditar(${data.idVinculacion})" class="badge badge-soft-danger mr-2">Inactivar</a></td>
                </tr>`;
                container.append(tr);
            }
        },
        error: function () { }
    });
}
$('#v_agregarWindows').on("click", vinculacionWindowsEditar);

function enviarCorreoWindowsEditar() {
    var idEmpleado = $('#v_id').val();
    var idVinculacion = $('#windows').val();
    $.ajax({
        async: false,
        type: "get",
        url: "correoWindows",
        data: {
            idEmpleado: idEmpleado,
            idVinculacion: idVinculacion
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            $('#v_windowsEmpleado').modal('toggle');
            $('#form-ver').show();
            var container = $('#enviadoW' + idVinculacion);
            container.empty();
            var td = `<td>${data.envio}</td>`;
            if (data.disponible == 'e') {
                var cont = $('#estado' + idVinculacion);
                cont.empty();
                var tdE = `<td>Enviado</td>`
                cont.append(tdE);
            }
            container.append(td);
            dispositivosWindows();
            $.notifyClose();
            $.notify({
                message: "\nCorreo Enviado.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-ver'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function () { }
    });
}
$('#v_enviarCorreoWindowsEmpleado').on("click", enviarCorreoWindowsEditar);
function controlPuerta(idPuerta) {
    var estadoP;
    if ($('#customSwitchCP' + idPuerta).is(':checked')) {
        estadoP = 1;

        $.ajax({
            type: "post",
            url: "/empleado/asisPuerta",
            data: {
                idPuerta,
                estadoP
            },
            statusCode: {
                419: function () {
                    location.reload();
                },
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                $('#customSwitchCP' + idPuerta).prop('checked', true);
            },
            error: function (data) {
                alert("Ocurrio un error");
            },
        });
    }
    else {
        estadoP = 0;

        $.ajax({
            type: "post",
            url: "/empleado/asisPuerta",
            data: {
                idPuerta,
                estadoP
            },
            statusCode: {
                419: function () {
                    location.reload();
                },
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                $('#customSwitchCP' + idPuerta).prop('checked', false);
            },
            error: function (data) {
                alert("Ocurrio un error");
            },
        });

    }
}
// ? CARGAR DATOS DE DISPOSITIVOS WINDOWS EN EDITAR
function dispositivosWindows() {
    var idEmpleado = $('#v_id').val();

    $('#v_tbodyDispositivo').empty();
    $.ajax({
        async: false,
        type: "get",
        url: "/listaVW",
        data: {
            idE: idEmpleado
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            var container = $('#v_tbodyDispositivo');
            for (var i = 0; i < data.length; i++) {
                if (data[i].dispositivoD == 'WINDOWS') {
                    var tr = `<tr id="tr${data[i].idVinculacion}">
                        <td>${data[i].dispositivoD}</td>
                        <td> PC ${i}</td>
                        <td>${data[i].licencia}</td>
                        <td class="hidetext">${data[i].codigo}</td>
                        <td id="enviadoW${data[i].idVinculacion}">${data[i].envio}</td>
                        <td id="estado${data[i].idVinculacion}"></td>
                        <td id="correo${data[i].idVinculacion}">
                            <a  onclick="javascript:modalWindowsEditar(${data[i].idVinculacion});$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                                correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                                src="landing/images/note.svg" height="20">
                            </a>
                        </td>
                        <td id="inactivar${data[i].idVinculacion}"><a onclick="javascript:inactivarLicenciaWEditar(${data[i].idVinculacion})" class="badge badge-soft-danger mr-2">Inactivar</a></td>
                        </tr>`;
                }
                container.append(tr);
                // ESTADO DE LICENCIAS

                if (data[i].disponible == 'c') {
                    $("#tr" + data[i].idVinculacion).find("td:eq(5)").text("Creado");
                }
                if (data[i].disponible == 'e') {
                    $("#tr" + data[i].idVinculacion).find("td:eq(5)").text("Enviado");
                }
                if (data[i].disponible == 'a') {
                    $("#tr" + data[i].idVinculacion).find("td:eq(5)").text("Activado");
                }
                if (data[i].disponible == 'i') {
                    $("#tr" + data[i].idVinculacion).find("td:eq(4)").text("Inactivo");
                    $('#inactivar' + data[i].idVinculacion).empty();
                    $('#correo' + data[i].idVinculacion).empty();
                    if (data[i].dispositivoD == 'WINDOWS') {
                        var td = `<a  onclick="javascript:modalWindowsEditar(${data[i].idVinculacion});$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                                        correo empleado" data-original-title="Habilitar activación" style="cursor: pointer"><img
                                            src="landing/images/email (4).svg" height="20">
                                        </a>`;
                    } else {
                        var td = `<input style="display: none;" id="android${data[i].idEmpleado}" value="${data[i].idVinculacion}">
                                    <a  onclick="$('#v_androidEmpleado').modal();$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                                    correo empleado" data-original-title="Habilitar activación" style="cursor: pointer"><img
                                        src="landing/images/email (4).svg" height="20">
                                    </a>`;
                    }
                    $('#correo' + data[i].idVinculacion).append(td);
                }

                // NOMBRE DE PC
                if (data[i].pc != null) {
                    $("#tr" + data[i].idVinculacion).find("td:eq(1)").text(data[i].pc);
                } else {
                    $("#tr" + data[i].idVinculacion).find("td:eq(1)").text("PC " + i);
                }
            }
        },
        error: function () { }
    });
}
// ? CARGAR DATOS DE DISPOSITIVOS ANDROID EN EDITAR
function dispositivosAndroid() {
    var idEmpleado = $('#v_id').val();
    $('#v_tbodyDispositivoA').empty();
    $.ajax({
        async: false,
        type: "get",
        url: "/listaVA",
        data: {
            idE: idEmpleado
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            var containerA = $('#v_tbodyDispositivoA');
            for (let index = 0; index < data.length; index++) {
                if (data[index].dispositivoD == 'ANDROID') {
                    var trA = `<tr id="trA${data[index].idV}" onclick="javascript:modoAndroid(${data[index].idV})">
                                <td>${data[index].dispositivoD}</td>
                                <td>Android</td>
                                <td id="tdNumero${data[index].idV}">${data[index].numero}</td>
                                <td class="hidetext">${data[index].codigo}</td>
                                <td id="enviadoA${data[index].idV}">${data[index].envio}</td>
                                <td id="sms${data[index].idV}">
                                    <input style="display: none;" id="android${data[index].idEmpleado}" value="${data[index].idV}">
                                    <a  onclick="javascript:smsAndroid(${data[index].idV});" data-toggle="tooltip" data-placement="right" title="Enviar
                                    correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                                        src="landing/images/note.svg" height="20">
                                    </a>
                                </td>
                                </tr>`;
                }
                containerA.append(trA);
                if (data[index].modelo !== null) {
                    $("#trA" + data[index].idV).find("td:eq(1)").text(data[index].modelo);
                } else {
                    $("#trA" + data[index].idV).find("td:eq(1)").text("CEL " + index);
                }
                $('#customSwitchC2').prop('checked', true);
                $('#bodyModoControlRuta').show();
            }
        },
        error: function () { }
    });

}
// ? CARGAR DISPOSITIVOS WINDOWS EN MODAL VER
function dispositivoWindowsVer() {
    var idEmpleado = $('#v_idV').val();

    $('#v_tbodyDispositivo').empty();
    $.ajax({
        async: false,
        type: "get",
        url: "/listaVW",
        data: {
            idE: idEmpleado
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            var containerVer = $('#ver_tbodyDispositivo');
            for (var i = 0; i < data.length; i++) {
                if (data[i].dispositivoD == 'WINDOWS') {
                    var trVer = `<tr id="trVer${data[i].idVinculacion}">
                            <td>${data[i].dispositivoD}</td>
                            <td> PC ${i}</td>
                            <td>${data[i].licencia}</td>
                            <td class="hidetext">${data[i].codigo}</td>
                            <td id="enviadoW${data[i].idVinculacion}">${data[i].envio}</td>
                            <td id="estado${data[i].idVinculacion}"></td>
                            <td id="correoVer${data[i].idVinculacion}">
                                <a><img src="landing/images/note.svg" height="20">
                                </a>
                            </td>
                            <td id="inactivarVer${data[i].idVinculacion}"><a class="badge badge-soft-danger mr-2">Inactivar</a></td>
                            </tr>`;
                }
                containerVer.append(trVer);
                //ESTADO DE LICENCIA
                if (data[i].disponible == 'c') {
                    $("#trVer" + data[i].idVinculacion).find("td:eq(5)").text("Creado");
                }
                if (data[i].disponible == 'e') {
                    $("#trVer" + data[i].idVinculacion).find("td:eq(5)").text("Enviado");
                }
                if (data[i].disponible == 'a') {
                    $("#trVer" + data[i].idVinculacion).find("td:eq(5)").text("Activado");
                }
                if (data[i].disponible == 'i') {
                    $("#trVer" + data[i].idVinculacion).find("td:eq(5)").text("Inactivo");
                    $('#inactivarVer' + data[i].idVinculacion).empty();
                    $('#correoVer' + data[i].idVinculacion).empty();
                    if (data[i].dispositivoD == 'WINDOWS') {
                        var tdV = `<a><img src="landing/images/email (4).svg" height="20">
                                            </a>`;
                    } else {
                        var tdV = `<input style="display: none;" id="android${data[i].idEmpleado}" value="${data[i].idVinculacion}">
                                        <a><img src="landing/images/email (4).svg" height="20">
                                        </a>`;
                    }
                    $('#correoVer' + data[i].idVinculacion).append(tdV);
                }
                // NOMBRE DE PC
                if (data[i].pc != null) {
                    $("#trVer" + data[i].idVinculacion).find("td:eq(1)").text(data[i].pc);
                } else {
                    $("#trVer" + data[i].idVinculacion).find("td:eq(1)").text("PC " + i);
                }
            }
        },
        error: function () { }
    });
}
// ? VINCULACION DE MODO EN RUTA
function vinculacionAndroidEditar() {
    var idEmpleado = $('#v_id').val();
    $.ajax({
        async: false,
        type: "get",
        url: "vinculacionAndroid",
        data: {
            idEmpleado: idEmpleado
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            if (data == 1) {
                $.notifyClose();
                $.notify({
                    message: "\nLlego al limite de dispositivos Android",
                    icon: 'admin/images/warning.svg'
                }, {
                    element: $('#form-ver'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            } else {
                var container = $('#v_tbodyDispositivoA');
                var tr = `<tr onclick="javascript:modoAndroid(${data.idVinculacion})">
                <td>${data.dispositivo_descripcion}</td>
                <td> CEL ${data.contar}</td>
                <td id="tdNumero${data.idVinculacion}">${data.numero}</td>
                <td class="hidetext">${data.codigo}</td>
                <td id="enviadoA${data.idVinculacion}">${data.envio}</td>
                <td id="sms${data.idVinculacion}">
                    <a  onclick="javascript:smsAndroid(${data.idVinculacion});" data-toggle="tooltip" data-placement="right" title="Enviar
                    correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                        src="landing/images/note.svg" height="20">
                </a>
                </td>
                </tr>`;
                container.append(tr);
                $('#customSwitchC2').prop('checked', true);
                $('#bodyModoControlRuta').show();
            }
        },
        error: function () { }
    });
}
$('#v_agregarAndroid').on("click", vinculacionAndroidEditar);
// ? funcion para editar número
function editarNumero(id, numero) {
    $.ajax({
        async: false,
        type: "post",
        url: "/celularVinculacion",
        data: {
            id: id,
            numero: numero
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            $.notifyClose();
            $.notify({
                message: "\nActualización exitosa.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-ver'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
            dispositivosAndroid();
        },
        error: function () { }
    });
}
// ? abrir input para editar número
function modoAndroid(id) {
    $("#tdNumero" + id).on("click", function () {
        $(this).addClass("editable");
        $(this).html(
            '<input type="text" style="border-radius: 5px;border: 2px solid #8d93ab;" maxlength="9" />'
        );
        $(this).children().first().focus();
        $(this)
            .children()
            .first()
            .keypress(function (e) {
                if (e.which == 13) {
                    var newContent = $(this).val();
                    $(this).parent().text(newContent);
                    $(this).parent().removeClass("editable");
                    alertify
                        .confirm(
                            "¿Desea modificar número de celular?",
                            function (e) {
                                if (e) {
                                    editarNumero(id, newContent);
                                }
                            }
                        )
                        .setting({
                            title: "Modificar",
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
                                dispositivosAndroid();
                            },
                        });
                }
            });

        $(this)
            .children()
            .first()
            .blur(function () {
                dispositivosAndroid();
            });
    });
}

// ? ENVIAR SMS
function enviarSms(id) {
    console.log(id);
    $.ajax({
        type: "get",
        url: "/smsAndroid",
        data: {
            id: id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            if (data == 0) {
                $.notifyClose();
                $.notify({
                    message: "\nRegistrar número de celular del empleado.",
                    icon: 'admin/images/warning.svg'
                }, {
                    element: $('#form-ver'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            } else {
                if (data == 1) {
                    $.notifyClose();
                    $.notify({
                        message: "\nTenemos problemas con el servidor mensajeria.Comunicarse con nosotros",
                        icon: 'admin/images/warning.svg'
                    }, {
                        element: $('#form-ver'),
                        position: 'fixed',
                        icon_type: 'image',
                        newest_on_top: true,
                        delay: 5000,
                        template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                            '</div>',
                        spacing: 35
                    });
                } else {
                    dispositivosAndroid();
                    $.notifyClose();
                    $.notify({
                        message: "\nSMS enviado.",
                        icon: 'admin/images/checked.svg'
                    }, {
                        element: $('#form-ver'),
                        position: 'fixed',
                        icon_type: 'image',
                        newest_on_top: true,
                        delay: 5000,
                        template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            '</div>',
                        spacing: 35
                    });
                    dispositivosAndroid()
                }
            }
        },
        error: function () { }
    });
}

// ? MODAL DE DECISION DE ENVIAR SMS
function smsAndroid(id) {
    alertify
        .confirm(
            "¿Desea enviar sms a empleado?",
            function (e) {
                if (e) {
                    enviarSms(id);
                }
            }
        )
        .setting({
            title: "Modificar Actividad",
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
                dispositivosAndroid();
            },
        });
}