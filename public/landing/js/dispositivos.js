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
                <td>${data.codigo}</td>
                <td id="enviado${idEmpleado}">${data.envio}</td>
                <td id="estado${idEmpleado}">Creado</td>
                <td>
                <input style="display: none;" id="android${idEmpleado}" value="${data.idVinculacion}">
                <a  onclick="$('#androidEmpleado').modal();$('#form-registrar').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                    src="landing/images/note.svg" height="20">
            </a></td>
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
            var container = $('#enviado' + idEmpleado);
            var cont = $('#estado' + idEmpleado);
            container.empty();
            cont.empty();
            var td = `<td>${data.envio}</td>`;
            var tdE = `<td>Enviado</td>`;
            container.append(td);
            cont.append(tdE);
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
            var container = $('#tbodyDispositivo');
            var tr = `<tr>
                <td>${data.dispositivo_descripcion}</td>
                <td>${data.licencia}</td>
                <td>${data.codigo}</td>
                <td id="enviadoW${data.idVinculacion}">${data.envio}</td>
                <td id="estado${data.idVinculacion}">Creado</td>
                <td>
                <a  onclick="javascript:modalWindows(${data.idVinculacion});$('#form-registrar').hide();" data-toggle="tooltip" data-placement="right" title="Enviar
                correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                    src="landing/images/note.svg" height="20">
            </a></td>
                </tr>`;
            container.append(tr);
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
            var container = $('#enviado' + idEmpleado);
            var cont = $('#estado' + idEmpleado);
            container.empty();
            cont.empty();
            var td = `<td>${data.envio}</td>`;
            var tdE = `<td>Enviado</td>`;
            container.append(td);
            cont.append(tdE);
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
$('#v_enviarCorreoAndroidEmpleado').on("click", enviarCorreoAndoidEditar);

function modalWindowsEditar(id) {
    console.log(id);
    $('#windows').val(id);
    $('#v_windowsEmpleado').modal();

}

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
$('#v_enviarCorreoWindowsEmpleado').on("click", enviarCorreoWindowsEditar);
