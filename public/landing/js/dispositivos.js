function inactivarLicencia(id) {
    $('#estadoLicencia').val(id);
    $('#form-registrar').hide();
    $('#estadoLicenciaC').modal();
}

function inactivarLicenciaW(id) {
    $('#estadoLicenciaW').val(id);
    $('#form-registrar').hide();
    $('#estadoLicenciaW').modal();
}

function cambiarEstadoLicenciaAndroid() {
    var idEmpleado = $('#idEmpleado').val();
    var idVinculacion = $('#estadoLicencia').val();
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
            var tdC = `<input style="display: none;" id="android${idEmpleado}" value="${idVinculacion}">
            <a  onclick="$('#androidEmpleado').modal();$('#form-registrar').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
            correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img src="landing/images/email (4).svg" height="20">
            </a>`;
            var tdE = `Inactivo`;
            var tdI = ``;
            $('#correo' + idVinculacion).append(tdC);
            $('#inactivar' + idVinculacion).append(tdI);
            $('#estado' + idVinculacion).append(tdE);
            $('#estadoLicenciaC').modal('toggle');
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
            $('#estadoLicenciaC').modal('toggle');
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
$('#CambiarEstadoL').on("click", cambiarEstadoLicenciaAndroid);

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
function inactivarLicenciaEditar(id) {
    $('#estadoLicencia').val(id);
    $('#form-ver').hide();
    $('#v_estadoLicenciaC').modal();
}

function inactivarLicenciaWEditar(id) {
    $('#estadoLicenciaW').val(id);
    $('#form-ver').hide();
    $('#v_estadoLicenciaW').modal();
}

function cambiarEstadoLicenciaAndroidEditar() {
    var idEmpleado = $('#v_id').val();
    var idVinculacion = $('#estadoLicencia').val();
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
            var td = `<input style="display: none;" id="android${idEmpleado}" value="${data.idVinculacion}">
            <a  onclick="$('#androidEmpleado').modal();$('#form-registrar').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
            correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                src="landing/images/note.svg" height="20">
            </a>`;
            var tdE = `Inactivo`;
            $('#correo' + idVinculacion).append(td);
            $('#inactivar' + idVinculacion).append(``);
            $('#estado' + idVinculacion).append(tdE);
            $('#estadoLicenciaC').modal('toggle');
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
            $('#estadoLicenciaC').modal('toggle');
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
$('#v_CambiarEstadoL').on("click", cambiarEstadoLicenciaAndroidEditar);

function cambiarEstadoLicenciaWindowsEditar() {
    var idEmpleado = $('#v_id').val();
    var idVinculacion = $('#estadoLicencia').val();
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
            var td = `<a  onclick="javascript:modalWindows(${data.idVinculacion});$('#form-registrar').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
            correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                src="landing/images/note.svg" height="20">
            </a>`;
            var tdE = `Inactivo`;
            $('#correo' + idVinculacion).append(td);
            $('#inactivar' + idVinculacion).append(``);
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
$('#v_CambiarEstadoLW').on("click", cambiarEstadoLicenciaWindowsEditar);
//*************************************************************************** */
$('#tbodyDispositivo').empty();
$('#v_tbodyDispositivo').empty();
//ANDROID
function vinculacionAndroid() {
    var idEmpleado = $('#idEmpleado').val();
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
                <td>${data.licencia}</td>
                <td class="hidetext">${data.codigo}</td>
                <td id="enviado${data.idVinculacion}">${data.envio}</td>
                <td id="estado${data.idVinculacion}">Creado</td>
                <td id="correo${data.idVinculacion}">
                    <input style="display: none;" id="android${idEmpleado}" value="${data.idVinculacion}">
                    <a  onclick="$('#androidEmpleado').modal();$('#form-registrar').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                    correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                        src="landing/images/note.svg" height="20">
                    </a>
                </td>
                <td id="inactivar${data.idVinculacion}"><a onclick="javascript:inactivarLicencia(${data.idVinculacion})" class="badge badge-soft-danger mr-2">Inactivar</a></td>
                </tr>`;
                container.append(tr);
            }
            console.log(data);
        },
        error: function () {}
    });
}
$('#agregarAndroid').on("click", vinculacionAndroid);


function enviarCorreoAndoid() {
    var idEmpleado = $('#idEmpleado').val();
    var idVinculacion = $('#android' + idEmpleado).val();
    $.ajax({
        async: false,
        type: "get",
        url: "correoAndroid",
        data: {
            idEmpleado: idEmpleado,
            idVinculacion: idVinculacion
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            $('#androidEmpleado').modal('toggle');
            $('#form-registrar').show();
            var container = $('#enviado' + idVinculacion);
            var cont = $('#estado' + idVinculacion);
            container.empty();
            cont.empty();
            $('#correo' + idVinculacion).empty();
            var td = `${data.envio}`;
            var tdE = `Enviado`;
            var tdC = `<input style="display: none;" id="android${idEmpleado}" value="${idVinculacion}">
            <a  onclick="$('#androidEmpleado').modal();$('#form-registrar').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
            correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                src="landing/images/note.svg" height="20">
            </a>`;
            container.append(td);
            cont.append(tdE);
            $('#correo' + idVinculacion).append(tdC);
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
$('#enviarCorreoAndroidEmpleado').on("click", enviarCorreoAndoid);
//WINDOWS
//MODAL WINDOWS
function modalWindows(id) {
    console.log(id);
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
            console.log(data);
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
            console.log(data);
        },
        error: function () {}
    });
}
$('#agregarWindows').on("click", vinculacionWindows);

function enviarCorreoWindows() {
    var idEmpleado = $('#idEmpleado').val();

    var idVinculacion = $('#windows').val();
    console.log(idVinculacion);
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
                <td>${data.licencia}</td>
                <td class="hidetext">${data.codigo}</td>
                <td id="enviado${data.idVinculacion}">${data.envio}</td>
                <td id="estado${data.idVinculacion}">Creado</td>
                <td id="correo${data.idVinculacion}">
                    <input style="display: none;" id="android${idEmpleado}" value="${data.idVinculacion}">
                    <a  onclick="$('#androidEmpleado').modal();$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                    correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                        src="landing/images/note.svg" height="20">
                    </a>
                </td>
                <td id="inactivar${data.idVinculacion}"><a onclick="javascript:inactivarLicenciaEditar(${data.idVinculacion})" class="badge badge-soft-danger mr-2">Inactivar</a></td>
                </tr>`;
                container.append(tr);
            }
            console.log(data);
        },
        error: function () {}
    });
}
$('#v_agregarAndroid').on("click", vinculacionAndroidEditar);

function enviarCorreoAndoidEditar() {
    var idEmpleado = $('#v_id').val();
    var idVinculacion = $('#android' + idEmpleado).val();
    $.ajax({
        async: false,
        type: "get",
        url: "correoAndroid",
        data: {
            idEmpleado: idEmpleado,
            idVinculacion: idVinculacion
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            $('#v_androidEmpleado').modal('toggle');
            $('#form-ver').show();
            var container = $('#enviado' + idVinculacion);
            var cont = $('#estado' + idVinculacion);
            container.empty();
            cont.empty();
            var td = `<td>${data.envio}</td>`;
            var tdE = `<td>Enviado</td>`;
            container.append(td);
            cont.append(tdE);
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
$('#v_enviarCorreoAndroidEmpleado').on("click", enviarCorreoAndoidEditar);

function modalWindowsEditar(id) {
    console.log(id);
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
                <td>${data.licencia}</td>
                <td class="hidetext">${data.codigo}</td>
                <td id="enviadoW${data.idVinculacion}">${data.envio}</td>
                <td id="estado${data.idVinculacion}">Creado</td>
                <td id="correo${data.idVinculacion}">
                    <a  onclick="javascript:modalWindows(${data.idVinculacion});$('#form-ver').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                    correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                        src="landing/images/note.svg" height="20">
                </a>
                </td>
                <td id="inactivar${data.idVinculacion}"><a onclick="javascript:inactivarLicenciaWEditar(${data.idVinculacion})" class="badge badge-soft-danger mr-2">Inactivar</a></td>
                </tr>`;
                container.append(tr);
            }
            console.log(data);
        },
        error: function () {}
    });
}
$('#v_agregarWindows').on("click", vinculacionWindowsEditar);

function enviarCorreoWindowsEditar() {
    var idEmpleado = $('#v_id').val();
    var idVinculacion = $('#windows').val();
    console.log(idVinculacion);
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
