//EDITAR
$('#customSwitchC1').prop('checked', true);
$('#bodyModoControlR').show();
$('#bodyModoControlA').hide();
$('#customSwitchC1').on('change.bootstrapSwitch', function (event) {
    if (event.target.checked == true) {
        $('#bodyModoControlR').show();
    } else {
        $('#bodyModoControlR').hide();
    }
});
$('#customSwitchC2').on('change.bootstrapSwitch', function (event) {
    if (event.target.checked == true) {
        $('#bodyModoControlA').show();
    } else {
        $('#bodyModoControlA').hide();
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
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
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
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
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
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
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
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
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
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
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
        error: function () {}
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
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function () {}
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
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
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
        error: function () {}
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
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function () {}
    });
}
$('#v_enviarCorreoWindowsEmpleado').on("click", enviarCorreoWindowsEditar);
function controlPuerta(idPuerta){
    var estadoP;
    if( $('#customSwitchCP'+idPuerta).is(':checked')) {
        estadoP=1;

        $.ajax({
            type: "post",
            url: "/empleado/asisPuerta",
            data: { idPuerta,
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
                $('#customSwitchCP'+idPuerta).prop('checked',true);
            },
            error: function (data) {
                alert("Ocurrio un error");
            },
        });
    }
    else{
        estadoP=0;

        $.ajax({
            type: "post",
            url: "/empleado/asisPuerta",
            data: { idPuerta,
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
                $('#customSwitchCP'+idPuerta).prop('checked',false);
            },
            error: function (data) {
                alert("Ocurrio un error");
            },
        });

    }
}
