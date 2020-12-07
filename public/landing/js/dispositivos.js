//TODO FORMUMARIO EDITAR
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
//: CARGAR DATOS DE DISPOSITIVOS WINDOWS EN EDITAR
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
                            <a  onclick="javascript:modalWindowsEditar(${data[i].idVinculacion});" data-toggle="tooltip" data-placement="right" title="Enviar
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
                        var td = `<a  onclick="javascript:modalWindowsEditar(${data[i].idVinculacion});" data-toggle="tooltip" data-placement="right" 
                                    title="Enviar correo empleado" data-original-title="Habilitar activación" style="cursor: pointer">
                                    <img src="landing/images/email (4).svg" height="20">
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
//: CARGAR DATOS DE DISPOSITIVOS ANDROID EN EDITAR
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
            $('#customSwitchC2').prop('checked', false);
            $('#bodyModoControlRuta').hide();
            for (let index = 0; index < data.length; index++) {
                if (data[index].dispositivoD == 'ANDROID') {
                    var trA = `<tr id="trA${data[index].idV}" onclick="javascript:modoAndroid(${data[index].idV})">
                                <td>${data[index].dispositivoD}</td>
                                <td>Android</td>
                                <td class="hidetext">${data[index].codigo}</td>
                                <td class="cursorDispositivo" id="tdNumero${data[index].idV}" 
                                    data-toggle="tooltip" data-placement="right" title="doble click para editar número">
                                    ${data[index].numero}
                                </td>
                                <td class="cursorDispositivo" data-toggle="tooltip" data-placement="right" title="doble click para editar actividad"
                                 id="tdActividad${data[index].idV}">${data[index].actividad}</td>
                                <td id="enviadoA${data[index].idV}">${data[index].envio}</td>
                                <td id="estadoA${data[index].idV}"></td>
                                <td id="sms${data[index].idV}">
                                    <a  onclick="javascript:smsAndroid(${data[index].idV});" data-toggle="tooltip" data-placement="right" title="Enviar
                                    sms empleado" data-original-title="Enviar sms empleado" style="cursor: pointer"><img
                                        src="landing/images/note.svg" height="20">
                                    </a>
                                </td>
                                <td id="inactivarA${data[index].idV}"><a onclick="javascript:inactivarDispositoAEditar(${data[index].idV})" class="badge badge-soft-danger mr-2">Inactivar</a></td>
                                </tr>`;
                }
                containerA.append(trA);
                // * MODELO DE DISPOSITIVO ANDROID
                if (data[index].modelo !== null) {
                    $("#trA" + data[index].idV).find("td:eq(1)").text(data[index].modelo);
                } else {
                    $("#trA" + data[index].idV).find("td:eq(1)").text("CEL " + index);
                }
                //* ESTADO DE VINCULACION
                if (data[index].disponible == 'c') {
                    $('#trA' + data[index].idV).find("td:eq(6)").text("Creado");
                }
                if (data[index].disponible == 'e') {
                    $('#trA' + data[index].idV).find("td:eq(6)").text("Enviado");
                }
                if (data[index].disponible == 'a') {
                    $('#trA' + data[index].idV).find("td:eq(6)").text("Activado");
                }
                if (data[index].disponible == 'i') {
                    $('#trA' + data[index].idV).find("td:eq(6)").text("Inactivo");
                    $('#inactivarA' + data[index].idV).empty();
                    $('#sms' + data[index].idV).empty();
                    var tdSms = `<a  onclick="javascript:smsAndroid(${data[index].idV});" data-toggle="tooltip" data-placement="right" 
                                    title="Enviar sms empleado" data-original-title="Enviar sms empleado" style="cursor: pointer">
                                    <img src="landing/images/email (4).svg" height="20">
                                </a>`;
                    $('#sms' + data[index].idV).append(tdSms);
                }
                //* *************************************************************
                $('#customSwitchC2').prop('checked', true);
                $('#bodyModoControlRuta').show();
            }
        },
        error: function () { }
    });

}
//: AGREGAR VINCULACIONES WINDOWS
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
//: FUNCIONES DE INACTIVAR LICENCIA EN WINDOWS
function inactivarLicenciaWEditar(id) {
    alertify
        .confirm(
            "<img src=\"landing/images/alert.svg\" height=\"20\" class=\"mr-1\">&nbsp;Al cambiar el estado de la licencia se inhabilitará información del empleado en su PC",
            function (e) {
                if (e) {
                    cambiarEstadoLicenciaWindowsEditar(id);
                }
            }
        )
        .setting({
            title: "Cambiar estado de activación de dispositivo",
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
                dispositivosWindows();
            },
        });
}
//: CAMBIAR ESTADO DE LICENCIA EN WINDOWS
function cambiarEstadoLicenciaWindowsEditar(id) {
    var idEmpleado = $('#v_id').val();
    //NOTIFICACION
    $.ajax({
        async: false,
        type: "get",
        url: "/cambiarEstadoLicencia",
        data: {
            idE: idEmpleado,
            idV: id
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
            dispositivosWindows();
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
            dispositivosWindows();
            $.notifyClose();
            $.notify({
                message: "\nProceso falló.",
                icon: 'admin/images/warning.svg'
            }, {
                element: $('#form-ver'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        }
    });
}
$('#v_CambiarEstadoLW').on("click", cambiarEstadoLicenciaWindowsEditar);
//: FUNCIONES DE ENVIAR CORREO
function modalWindowsEditar(id) {
    alertify
        .confirm(
            "¿Desea enviar correo al empleado?",
            function (e) {
                if (e) {
                    enviarCorreoWindowsEditar(id);
                }
            }
        )
        .setting({
            title: "Enviar correo a empleado",
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
                dispositivosWindows();
            },
        });
}
//: ENVIAR CORREO
function enviarCorreoWindowsEditar(id) {
    var idEmpleado = $('#v_id').val();
    $.ajax({
        async: false,
        type: "get",
        url: "correoWindows",
        data: {
            idEmpleado: idEmpleado,
            idVinculacion: id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
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
// : VINCULACION ANDROID
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
                dispositivosAndroid();
                $('#customSwitchC2').prop('checked', true);
                $('#bodyModoControlRuta').show();
            }
        },
        error: function () { }
    });
}
$('#v_agregarAndroid').on("click", vinculacionAndroidEditar);
//: FUNCION PARA EDITAR NUMERO DE CELULAR EN ANDORID
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
//: FUNCION DE EDITAR ACTIVIDAD  EN ANDROID
function editarActividadV(id, actividad) {
    $.ajax({
        async: false,
        type: "post",
        url: "/actividadVinculacion",
        data: {
            id: id,
            actividad: actividad
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
//: INPUT PARA EDITAR NUMERO
function modoAndroid(id) {
    $("#tdNumero" + id).on("click", function () {
        $(this).addClass("editable");
        $(this).html(
            '<input type="text" style="border-radius: 5px;border: 2px solid #8d93ab;" maxlength="9" />'
        );
        $(this).children().first().focus();
        $(this).children().first().keyup(function (event) {
            if ($(this).val().length === 9) {
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
            console.log($(this).val(), $(this).val().length);
        });

        $(this)
            .children()
            .first()
            .blur(function () {
                dispositivosAndroid();
            });
    });

    $('#tdActividad' + id).on("click", function () {
        $(this).addClass("editable");
        $(this).html(
            '<input type="number" step="any" style="border-radius: 5px;border: 2px solid #8d93ab;"/>'
        );
        $(this).children().first().focus();
        $(this).children().first().keyup(function (event) {
            if (event.keyCode != 13) {
                var regex = RegExp("^\\d{1,3}(\\.\\d{1,2})?$");
                if (!regex.test($(this).val())) {
                    $(this).off('keypress');
                    $.notifyClose();
                    $.notify({
                        message: "\nActividad máxima 100.",
                        icon: 'admin/images/warning.svg'
                    }, {
                        element: $('#form-ver'),
                        position: 'fixed',
                        placement: {
                            from: "top",
                            align: "center",
                        },
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
                    if ($(this).val() > 100) {
                        $.notifyClose();
                        $.notify({
                            message: "\nActividad máxima 100.",
                            icon: 'admin/images/warning.svg'
                        }, {
                            element: $('#form-ver'),
                            position: 'fixed',
                            placement: {
                                from: "top",
                                align: "center",
                            },
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
                        $.notifyClose();
                        $.notify({
                            message: "\nPresionar <strong>ENTER</strong> para guardar cambios.",
                            icon: 'landing/images/warningInfo.svg'
                        }, {
                            element: $('#form-ver'),
                            position: 'fixed',
                            placement: {
                                from: "top",
                                align: "center",
                            },
                            icon_type: 'image',
                            newest_on_top: true,
                            delay: 5000,
                            template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #d9edf7;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#31708f;" data-notify="message">{2}</span>' +
                                '</div>',
                            spacing: 35
                        });
                    }
                    $(this).on('keypress', function (e) {
                        if (e.which == 13) {
                            if ($(this).val() <= 100) {
                                var newContent = $(this).val();
                                $(this).parent().text(newContent);
                                $(this).parent().removeClass("editable");
                                alertify.confirm("¿Desea modificar el promedio de actividad del dispositivo?",
                                    function (e) {
                                        if (e) {
                                            editarActividadV(id, newContent);
                                        }
                                    }
                                ).setting({
                                    title: "Modificar",
                                    labels: {
                                        ok: "Aceptar",
                                        cancel: "Cancelar"
                                    },
                                    modal: true,
                                    startMaximized: false,
                                    reverseButtons: true,
                                    resizable: false,
                                    closable: false,
                                    oncancel: function (closeEvent) {
                                        dispositivosAndroid();
                                    }
                                });
                            } else {
                                $.notifyClose();
                                $.notify({
                                    message: "\nActividad máxima 100.",
                                    icon: 'admin/images/warning.svg'
                                }, {
                                    element: $('#form-ver'),
                                    position: 'fixed',
                                    placement: {
                                        from: "top",
                                        align: "center",
                                    },
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
                        }
                    });
                }
            }

        });
        $(this).children().first().keypress(function (e) {
            if ($(this).val() != '') {
                if (e.which == 13) {
                    if ($(this).val() <= 100) {
                        var newContent = $(this).val();
                        $(this).parent().text(newContent);
                        $(this).parent().removeClass("editable");
                        alertify.confirm("¿Desea modificar el promedio de actividad del dispositivo?",
                            function (e) {
                                if (e) {
                                    editarActividadV(id, newContent);
                                }
                            }
                        ).setting({
                            title: "Modificar",
                            labels: {
                                ok: "Aceptar",
                                cancel: "Cancelar"
                            },
                            modal: true,
                            startMaximized: false,
                            reverseButtons: true,
                            resizable: false,
                            closable: false,
                            oncancel: function (closeEvent) {
                                dispositivosAndroid();
                            }
                        });
                    } else {
                        $.notifyClose();
                        $.notify({
                            message: "\nActividad máxima 100.",
                            icon: 'admin/images/warning.svg'
                        }, {
                            element: $('#form-ver'),
                            position: 'fixed',
                            placement: {
                                from: "top",
                                align: "center",
                            },
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
                }
            }
        });

        $(this).children().first().blur(function () {
            dispositivosAndroid();
        });
    });
}
//: ENVIAR SMS
function enviarSms(id) {
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
                $("#tdNumero" + id).trigger("click");
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
                    dispositivosAndroid();
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

//: MODAL DE DECISION DE ENVIAR SMS
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
//: FUNCIONES DE INACTIVAR ANDROID
function inactivarDispositoAEditar(id) {
    alertify
        .confirm(
            "<img src=\"landing/images/alert.svg\" height=\"20\" class=\"mr-1\">&nbsp;Al cambiar el estado del dispositivo se inhabilitará información del empleado en su celular",
            function (e) {
                if (e) {
                    cambiarEstadoAndroidEditar(id);
                }
            }
        )
        .setting({
            title: "Cambiar estado de activación de dispositivo",
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
//: CAMBIAR ESTADO DE LICENCIA EN WINDOWS
function cambiarEstadoAndroidEditar(id) {
    var idEmpleado = $('#v_id').val();
    //NOTIFICACION
    $.ajax({
        async: false,
        type: "get",
        url: "/cambiarEstadoVinculacionRuta",
        data: {
            idE: idEmpleado,
            idV: id
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
            dispositivosAndroid();
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
            dispositivosAndroid();
            $.notifyClose();
            $.notify({
                message: "\nProceso falló.",
                icon: 'admin/images/warning.svg'
            }, {
                element: $('#form-ver'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        }
    });
}
// TODO ****** FINALIZACION DE FORMULARIO EDITAR *****//
// TODO EN FORMULARIO REGISTRAR
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
//* CARGAR DATOS DE DISPOSITIVOS WINDOWS
function dispositivosWindowsRegistrar() {
    var idEmpleado = $('#idEmpleado').val();
    $('#tbodyDispositivo').empty();
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
            var container = $('#tbodyDispositivo');
            for (var i = 0; i < data.length; i++) {
                if (data[i].dispositivoD == 'WINDOWS') {
                    var tr = `<tr id="trR${data[i].idVinculacion}">
                        <td>${data[i].dispositivoD}</td>
                        <td> PC ${i}</td>
                        <td>${data[i].licencia}</td>
                        <td class="hidetext">${data[i].codigo}</td>
                        <td id="enviadoWR${data[i].idVinculacion}">${data[i].envio}</td>
                        <td id="estadoR${data[i].idVinculacion}"></td>
                        <td id="correoR${data[i].idVinculacion}">
                            <a  onclick="javascript:modalWindows(${data[i].idVinculacion});" data-toggle="tooltip" data-placement="right" title="Enviar
                                correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                                src="landing/images/note.svg" height="20">
                            </a>
                        </td>
                        <td id="inactivarR${data[i].idVinculacion}"><a onclick="javascript:inactivarLicenciaW(${data[i].idVinculacion})" class="badge badge-soft-danger mr-2">Inactivar</a></td>
                        </tr>`;
                }
                container.append(tr);
                // ESTADO DE LICENCIAS

                if (data[i].disponible == 'c') {
                    $("#trR" + data[i].idVinculacion).find("td:eq(5)").text("Creado");
                }
                if (data[i].disponible == 'e') {
                    $("#trR" + data[i].idVinculacion).find("td:eq(5)").text("Enviado");
                }
                if (data[i].disponible == 'a') {
                    $("#trR" + data[i].idVinculacion).find("td:eq(5)").text("Activado");
                }
                if (data[i].disponible == 'i') {
                    $("#trR" + data[i].idVinculacion).find("td:eq(4)").text("Inactivo");
                    $('#inactivarR' + data[i].idVinculacion).empty();
                    $('#correoR' + data[i].idVinculacion).empty();
                    if (data[i].dispositivoD == 'WINDOWS') {
                        var td = `<a  onclick="javascript:modalWindows(${data[i].idVinculacion});" data-toggle="tooltip" data-placement="right" 
                                    title="Enviar correo empleado" data-original-title="Habilitar activación" style="cursor: pointer">
                                    <img src="landing/images/email (4).svg" height="20">
                                </a>`;
                    }
                    $('#correoR' + data[i].idVinculacion).append(td);
                }

                // NOMBRE DE PC
                if (data[i].pc != null) {
                    $("#trR" + data[i].idVinculacion).find("td:eq(1)").text(data[i].pc);
                } else {
                    $("#trR" + data[i].idVinculacion).find("td:eq(1)").text("PC " + i);
                }
            }
        },
        error: function () { }
    });
}
//* VINCULACION EN WINDOWS
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
                // var container = $('#tbodyDispositivo');
                // var tr = `<tr>
                // <td>${data.dispositivo_descripcion}</td>
                // <td> PC ${data.contar}</td>
                // <td>${data.licencia}</td>
                // <td class="hidetext">${data.codigo}</td>
                // <td id="enviadoW${data.idVinculacion}">${data.envio}</td>
                // <td id="estado${data.idVinculacion}">Creado</td>
                // <td id="correo${data.idVinculacion}">
                //     <a  onclick="javascript:modalWindows(${data.idVinculacion});" data-toggle="tooltip" data-placement="right" title="Enviar
                //     correo empleado" data-original-title="Enviar correo empleado" style="cursor: pointer"><img
                //         src="landing/images/note.svg" height="20">
                //     </a>
                // </td>
                // <td id="inactivar${data.idVinculacion}"><a onclick="javascript:inactivarLicenciaW(${data.idVinculacion})" class="badge badge-soft-danger mr-2">Inactivar</a></td>
                // </tr>`;
                // container.append(tr);
                dispositivosWindowsRegistrar();
            }
        },
        error: function () { }
    });
}
$('#agregarWindows').on("click", vinculacionWindows);
//* FUNCIONES DE INACTIVAR LICENCIA
function inactivarLicenciaW(id) {
    alertify
        .confirm(
            "<img src=\"landing/images/alert.svg\" height=\"20\" class=\"mr-1\">&nbsp;Al cambiar el estado de la licencia se inhabilitará información del empleado en su PC",
            function (e) {
                if (e) {
                    cambiarEstadoLicenciaWindows(id);
                }
            }
        )
        .setting({
            title: "Cambiar estado de activación de dispositivo",
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
                dispositivosWindowsRegistrar();
            },
        });
}
//* CAMBIAR ESTADO DE LICENCIA
function cambiarEstadoLicenciaWindows(id) {
    var idEmpleado = $('#idEmpleado').val();
    $.ajax({
        async: false,
        type: "get",
        url: "/cambiarEstadoLicencia",
        data: {
            idE: idEmpleado,
            idV: id
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
            dispositivosWindowsRegistrar();
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
            // $('#estadoLicenciaW').modal('toggle');
            // $('#form-registrar').show();
            dispositivosWindowsRegistrar();
            $.notifyClose();
            $.notify({
                message: "\nProceso falló.",
                icon: 'admin/images/warning.svg'
            }, {
                element: $('#form-registrar'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        }
    });
}
$('#CambiarEstadoLW').on("click", cambiarEstadoLicenciaWindows);
//* FUNCIONES DE ENVIAR CORREO WINDOWS
function modalWindows(id) {
    alertify
        .confirm(
            "¿Desea enviar correo al empleado?",
            function (e) {
                if (e) {
                    enviarCorreoWindows(id);
                }
            }
        )
        .setting({
            title: "Enviar correo a empleado",
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
                dispositivosWindowsRegistrar();
            },
        });
}
//* ENVIAR CORREO WINDOWS
function enviarCorreoWindows(id) {
    var idEmpleado = $('#idEmpleado').val();
    $.ajax({
        async: false,
        type: "get",
        url: "correoWindows",
        data: {
            idEmpleado: idEmpleado,
            idVinculacion: id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            dispositivosWindowsRegistrar();
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
// TODO ****** FINALIZACION DE FORMULARIO REGISTRAR *****//
// TODO EN FOMULARIO EN VER
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
$('#tbodyDispositivo').empty();
$('#v_tbodyDispositivo').empty();
$('#v_tbodyDispositivoA').empty();
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
// TODO ****** FINALIZACION DE FORMULARIO VER *****//
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