// MOSTRAR DATOS EN TABLA DEL FORMULARIO GUARDAR
function actividad_empleado() {
    var id = $('#idEmpleado').val();
    $.ajax({
        type: "GET",
        url: "/actividadEmpleado",
        data: {
            id: id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
            $('#regtablaBodyTarea').empty();
            if (data != 0) {
                var container = $('#regtablaBodyTarea');
                var td = '';
                for (var $i = 0; $i < data.length; $i++) {
                    td += `<tr><td>${data[$i].Activi_Nombre}</td>`;
                    if (data[$i].estado == 1) {
                        td += `<td>Activo</td><td></td></tr>`;
                    } else {
                        td += `<td>Inactivo</td><td></td></tr>`;
                    }
                }
                container.append(td);
            }
        },
        error: function () {

        }
    });
}
// MOSTRAR DATOS EN TABLA DEL FORMULARIO EDITAR
function actividadEmp() {
    var id = $('#v_id').val();
    $.ajax({
        type: "GET",
        url: "/actividadEmpleado",
        data: {
            id: id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
            $('#tablaBodyTarea').empty();
            if (data != 0) {
                var container = $('#tablaBodyTarea');
                var td = '';
                for (var $i = 0; $i < data.length; $i++) {
                    td += `<tr onclick="return editarActE(${data[$i].Activi_id})">
                    <input type="hidden" id="idAct${data[$i].Activi_id}" value="${data[$i].Activi_Nombre}">
                    <td class="editable" id="tdAct${data[$i].Activi_id}">${data[$i].Activi_Nombre}</td>`;
                    if (data[$i].estado == 1) {
                        td += `<td><div class="custom-control custom-switch">
                        <input type="checkbox" checked="" class="custom-control-input" id="customSwitchAct${data[$i].Activi_id}">
                        <label class="custom-control-label" for="customSwitchAct${data[$i].Activi_id}"></label>
                      </div></td><td></td></tr>`;
                    } else {
                        td += `<td><div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="customSwitchAct${data[$i].Activi_id}">
                        <label class="custom-control-label" for="customSwitchAct${data[$i].Activi_id}"></label>
                      </div></td><td></td></tr>`;
                    }
                }
                container.append(td);
            }
        },
        error: function () {

        }
    });
}
// MOSTRAR DATOS EN TABLA DEL FORMULARIO VER
function actividadEmpVer() {
    var id = $('#v_idV').val();
    $.ajax({
        type: "GET",
        url: "/actividadEmpleado",
        data: {
            id: id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
            $('#tablaBodyTarea_ver').empty();
            if (data != 0) {
                var container = $('#tablaBodyTarea_ver');
                var td = '';
                for (var $i = 0; $i < data.length; $i++) {
                    td += `<tr><td>${data[$i].Activi_Nombre}</td>`;
                    if (data[$i].estado == 1) {
                        td += `<td>Activo</td><td></td></tr>`;
                    } else {
                        td += `<td>Inactivo</td><td></td></tr>`;
                    }
                }
                container.append(td);
            }
        },
        error: function () {

        }
    });
}
// ***********************************
$('#customSwitch1').prop('checked', true);
$('#bodyModoTarea').show();
$('#customSwitch3').prop('checked', true);
$('#regbodyModoTarea').show();
$('#customSwitch5').prop('checked', true);
$('#bodyModoTarea_ver').show();
$('#customSwitch1').on('change.bootstrapSwitch', function (event) {
    console.log(event.target.checked);
    if (event.target.checked == true) {
        $('#bodyModoTarea').show();
        actividadEmp();
    } else {
        $('#bodyModoTarea').hide();
    }
});
$('#customSwitch3').on('change.bootstrapSwitch', function (event) {
    console.log(event.target.checked);
    if (event.target.checked == true) {
        $('#regbodyModoTarea').show();
        actividad_empleado();
    } else {
        $('#regbodyModoTarea').hide();
    }
});
$('#customSwitch5').on('change.bootstrapSwitch', function (event) {
    console.log(event.target.checked);
    if (event.target.checked == true) {
        $('#bodyModoTarea_ver').show();
        actividadEmpVer();
    } else {
        $('#bodyModoTarea_ver').hide();
    }
});
$('#bodyModoProyecto').hide();
$('#regbodyModoProyecto').hide();
$('#bodyModoProyecto_ver').hide();
$('#customSwitch2').on('change.bootstrapSwitch', function (event) {
    console.log(event.target.checked);
    if (event.target.checked == true) {
        $('#bodyModoProyecto').show();
    } else {
        $('#bodyModoProyecto').hide();
    }
});

$('#customSwitch4').on('change.bootstrapSwitch', function (event) {
    console.log(event.target.checked);
    if (event.target.checked == true) {
        $('#regbodyModoProyecto').show();
    } else {
        $('#regbodyModoProyecto').hide();
    }
});
$('#customSwitch6').on('change.bootstrapSwitch', function (event) {
    console.log(event.target.checked);
    if (event.target.checked == true) {
        $('#bodyModoProyecto_ver').show();
    } else {
        $('#bodyModoProyecto_ver').hide();
    }
});
// **************************************
function registrarActividadTarea() {
    var idE = $('#v_id').val();
    var nombre = $('#nombreTarea').val();
    $.ajax({
        type: "GET",
        url: "/registrarActvE",
        data: {
            idE: idE,
            nombre: nombre
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            limpiarModo();
            actividadEmp();
            $.notifyClose();
            $.notify({
                message: "\nActividad registrada.",
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
            $('#actividadTarea').modal('toggle');
        },
        error: function () {

        }
    });
}

function registrarNuevaActividadTarea() {
    var idE = $('#idEmpleado').val();
    var nombre = $('#regnombreTarea').val();
    $.ajax({
        type: "GET",
        url: "/registrarActvE",
        data: {
            idE: idE,
            nombre: nombre
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            limpiarModo();
            actividad_empleado();
            $.notifyClose();
            $.notify({
                message: "\nActividad registrada.",
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
            $('#regactividadTarea').modal('toggle');
        },
        error: function () {

        }
    });
}

function limpiarModo() {
    $('#nombreTarea').val("");
    $('#regnombreTarea').val("");
}
//  *******************************
function editarActividad(id, actividad) {
    $.ajax({
        type: "GET",
        url: "/editarActvE",
        data: {
            idA: id,
            actividad: actividad
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            actividadEmp();
            $.notifyClose();
            $.notify({
                message: "\nActividad Modificada.",
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

        }
    });
}

function editarEstadoActividad(id, estado) {
    $.ajax({
        type: "GET",
        url: "/editarEstadoA",
        data: {
            idA: id,
            estado: estado
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            actividadEmp();
            $.notifyClose();
            $.notify({
                message: "\nEstado Modificado.",
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

        }
    });
}

function editarActE(idA) {
    var OriginalContent = $('#idAct' + idA).val();
    $("#tdAct" + idA).on('click', function () {
        console.log(OriginalContent);
        $(this).addClass("editable");
        $(this).html("<input type='text' style='border-radius: 5px;border: 1px solid #39c;' value='" + OriginalContent + "'/>");
        $(this).children().first().focus();
        $(this).children().first().keypress(function (e) {
            if (e.which == 13) {
                var newContent = $(this).val();
                alertify.confirm("¿Desea modificar nombre de la actividad?", function (e) {
                    if (e) {
                        editarActividad(idA, newContent);
                        $(this).parent().text(newContent);
                        $(this).parent().removeClass("editable")
                    } else {
                        $(this).parent().text(OriginalContent);
                        $(this).parent().removeClass("editable");
                    }
                }).setting({
                    'title': 'Modificar Actividad',
                    'labels': {
                        ok: 'Aceptar',
                        cancel: 'Cancelar'
                    },
                    'modal': true,
                    'startMaximized': false,
                    'reverseButtons': true,
                    'resizable': false,
                    'transition': 'zoom'
                });
            }
        });

        $(this).children().first().blur(function () {
            $(this).parent().text(OriginalContent);
            $(this).parent().removeClass("editable");
        });

    });

    $('#customSwitchAct' + idA).on('change.bootstrapSwitch', function (event) {
        if (event.target.checked == true) {
            var valor = 1;
        } else {
            var valor = 0;
        }
        console.log(valor);
        alertify.confirm("¿Desea modificar el estado de la  actividad?", function (e) {
            if (e) {
                editarEstadoActividad(idA, valor);
            } else {
                actividadEmp();
            }
        }).setting({
            'title': 'Modificar Actividad',
            'labels': {
                ok: 'Aceptar',
                cancel: 'Cancelar'
            },
            'modal': true,
            'startMaximized': false,
            'reverseButtons': true,
            'resizable': false,
            'transition': 'zoom'
        });
    });
}
