//TODO -> ASIGNACION DE ACTIVIDAD A EMPLEADO A GESTION DE EMPLEADO
//* MOSTRAR DATOS EN TABLA DEL FORMULARIO GUARDAR
function actividad_empleado() {
    var id = $("#idEmpleado").val();
    $.ajax({
        type: "GET",
        url: "/actividadEmpleado",
        data: {
            id: id,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#regtablaBodyTarea").empty();
            if (data != 0) {
                var container = $("#regtablaBodyTarea");
                var td = "";
                for (var i = 0; i < data.length; i++) {
                    td += `<tr onclick="return RegeditarActE(${data[i].Activi_id})">
                    <input type="hidden" id="idActReg${data[i].Activi_id}" value="${data[i].Activi_Nombre}">
                    <td class="editable" id="tdActReg${data[i].Activi_id}">${data[i].Activi_Nombre}</td>`;
                    if (data[i].controlRemoto == 1) {
                        td += `<td class="text-center"><img src="/admin/images/checkH.svg" height="13" class="mr-2">&nbsp;Si</td>`;
                    } else {
                        td += `<td class="text-center"><img src="/admin/images/borrarH.svg" height="13" class="mr-2">&nbsp;No</td>`;
                    }
                    if (data[i].controlRuta == 1) {
                        td += `<td class="text-center"><img src="/admin/images/checkH.svg" height="13" class="mr-2">&nbsp;Si</td>`;
                    } else {
                        td += `<td class="text-center"><img src="/admin/images/borrarH.svg" height="13" class="mr-2">&nbsp;No</td>`;
                    }
                    if (data[i].estadoActividadEmpleado == 1) {
                        if (data[i].eliminacionActividadEmpleado == 0) {
                            td += `<td><div class="custom-control custom-switch" style="margin-left: 15px !important;">
                                <input type="checkbox" checked="" class="custom-control-input" id="customSwitchActReg${data[i].Activi_id}" disabled>
                                <label class="custom-control-label" for="customSwitchActReg${data[i].Activi_id}"></label>
                            </div></td></tr>`;
                        } else {
                            td += `<td><div class="custom-control custom-switch" style="margin-left: 15px !important;">
                                <input type="checkbox" checked="" class="custom-control-input" id="customSwitchActReg${data[i].Activi_id}">
                                <label class="custom-control-label" for="customSwitchActReg${data[i].Activi_id}"></label>
                            </div></td></tr>`;
                        }
                    } else {
                        td += `<td><div class="custom-control custom-switch" style="margin-left: 15px !important;">
                        <input type="checkbox" class="custom-control-input" id="customSwitchActReg${data[i].Activi_id}">
                        <label class="custom-control-label" for="customSwitchActReg${data[i].Activi_id}"></label>
                      </div></td></tr>`;
                    }
                }
                container.append(td);
            }
        },
        error: function () { },
    });
}
//* MOSTRAR DATOS EN TABLA DEL FORMULARIO EDITAR
function actividadEmp() {
    var id = $("#v_id").val();
    $.ajax({
        type: "GET",
        url: "/actividadEmpleado",
        data: {
            id: id,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#tablaBodyTarea").empty();
            if (data != 0) {
                var container = $("#tablaBodyTarea");
                var td = "";
                var valorIn = $('#gestActI').val();
                for (var i = 0; i < data.length; i++) {
                    td += `<tr onclick="return editarActE(${data[i].Activi_id})">
                    <input type="hidden" id="idAct${data[i].Activi_id}" value="${data[i].Activi_Nombre}">
                    <td class="editable" id="tdAct${data[i].Activi_id}">${data[i].Activi_Nombre}</td>`;
                    if (data[i].controlRemoto == 1) {
                        td += `<td class="text-center"><img src="/admin/images/checkH.svg" height="13" class="mr-2">&nbsp;Si</td>`;
                    } else {
                        td += `<td class="text-center"><img src="/admin/images/borrarH.svg" height="13" class="mr-2">&nbsp;No</td>`;
                    }
                    if (data[i].controlRuta == 1) {
                        td += `<td class="text-center"><img src="/admin/images/checkH.svg" height="13" class="mr-2">&nbsp;Si</td>`;
                    } else {
                        td += `<td class="text-center"><img src="/admin/images/borrarH.svg" height="13" class="mr-2">&nbsp;No</td>`;
                    }
                    if (data[i].estadoActividadEmpleado == 1) {
                        if (data[i].eliminacionActividadEmpleado == 0) {
                            td += `<td><div class="custom-control custom-switch" style="margin-left: 35px !important;">
                                <input type="checkbox" checked="" class="custom-control-input" id="customSwitchAct${data[i].Activi_id}" disabled>
                                <label class="custom-control-label" for="customSwitchAct${data[i].Activi_id}"></label>
                            </div></td></tr>`;
                        } else {
                            if (valorIn == 1) {
                                td += `<td><div class="custom-control custom-switch" style="margin-left: 35px !important;">
                                <input type="checkbox" checked="" class="custom-control-input" id="customSwitchAct${data[i].Activi_id}">
                                <label class="custom-control-label" for="customSwitchAct${data[i].Activi_id}"></label>
                            </div></td></tr>`;
                            }
                            else {
                                td += `<td><div class="custom-control custom-switch" style="margin-left: 35px !important;">
                                <input type="checkbox" checked="" class="custom-control-input" id="customSwitchAct${data[i].Activi_id}" disabled>
                                <label class="custom-control-label" for="customSwitchAct${data[i].Activi_id}"></label>
                            </div></td></tr>`;
                            }

                        }
                    } else {
                        if (valorIn == 1) {
                            td += `<td><div class="custom-control custom-switch" style="margin-left: 35px !important;">
                        <input type="checkbox" class="custom-control-input" id="customSwitchAct${data[i].Activi_id}">
                        <label class="custom-control-label" for="customSwitchAct${data[i].Activi_id}"></label>
                      </div></td></tr>`;
                        }
                        else {
                            td += `<td><div class="custom-control custom-switch" style="margin-left: 35px !important;">
                            <input type="checkbox" class="custom-control-input" id="customSwitchAct${data[i].Activi_id}" disabled>
                            <label class="custom-control-label" for="customSwitchAct${data[i].Activi_id}"></label>
                          </div></td></tr>`;
                        }
                    }
                }
                container.append(td);
            }
        },
        error: function () { },
    });
}
//* MOSTRAR DATOS EN TABLA DEL FORMULARIO VER
function actividadEmpVer() {
    var id = $("#v_idV").val();
    $.ajax({
        type: "GET",
        url: "/actividadEmpleado",
        data: {
            id: id,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#tablaBodyTarea_ver").empty();
            if (data != 0) {
                var container = $("#tablaBodyTarea_ver");
                var td = "";
                for (var i = 0; i < data.length; i++) {
                    td += `<tr><td>${data[i].Activi_Nombre}</td>`;
                    if (data[i].controlRemoto == 1) {
                        td += `<td class="text-center"><img src="/admin/images/checkH.svg" height="13" class="mr-2">&nbsp;Si</td>`;
                    } else {
                        td += `<td class="text-center"><img src="/admin/images/borrarH.svg" height="13" class="mr-2">&nbsp;No</td>`;
                    }
                    if (data[i].controlRuta == 1) {
                        td += `<td class="text-center"><img src="/admin/images/checkH.svg" height="13" class="mr-2">&nbsp;Si</td>`;
                    } else {
                        td += `<td class="text-center"><img src="/admin/images/borrarH.svg" height="13" class="mr-2">&nbsp;No</td>`;
                    }
                    if (data[i].estadoActividadEmpleado == 1) {
                        td += `<td><div class="custom-control custom-switch" style="margin-left: 35px !important;">
                        <input type="checkbox" checked="" class="custom-control-input" disabled id="customSwitchActV${data[i].Activi_id}">
                        <label class="custom-control-label" for="customSwitchActV${data[i].Activi_id}"></label>
                      </div></td></tr>`;
                    } else {
                        td += `<td><div class="custom-control custom-switch" style="margin-left: 35px !important;">
                        <input type="checkbox" class="custom-control-input" disabled id="customSwitchActV${data[i].Activi_id}">
                        <label class="custom-control-label" for="customSwitchActV${data[i].Activi_id}"></label>
                      </div></td></tr>`;
                    }
                }
                container.append(td);
            }
        },
        error: function () { },
    });
}
//* INICIALIZAR PLUGIN DE MILTI SELECT EN GUARDAR
$('#regEmpleadoActiv').select2({
    placeholder: "Seleccionar",
    closeOnSelect: false,
    minimumResultsForSearch: 5,
    allowClear: false
});
$('#regEmpleadoActiv').on("select2:opening", function () {
    var idE = $("#idEmpleado").val();
    var value = $('#regEmpleadoActiv').val();
    $('#regEmpleadoActiv').empty();
    $.ajax({
        async: false,
        type: "GET",
        url: "/actividadOrga",
        data: {
            idEmpleado: idE
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            var option = "";
            $.each(data, function (i, items) {
                option += `<option value="${items.value}"> ${items.text}</option>`;
            });
            $('#regEmpleadoActiv').append(option);
            $('#regEmpleadoActiv').val(value);
        },
        error: function () { },
    });
});
$('#formActvidadesReg').attr('novalidate', true);

$('#formActvidadesReg').submit(function (e) {
    e.preventDefault();
    if ($('#regEmpleadoActiv').val().length == 0) {
        $.notifyClose();
        $.notify({
            message: '\nSeleccionar Actividad',
            icon: 'landing/images/bell.svg',
        }, {
            element: $("#regactividadTarea"),
            position: "fixed",
            icon_type: 'image',
            placement: {
                from: "top",
                align: "center",
            },
            allow_dismiss: true,
            newest_on_top: true,
            delay: 6000,
            template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
        return;
    }
    this.submit();
});
//* INICIALIZAR PLUGIN DE MULTI SELECT EN EDITAR
$('#empleadoActiv').select2({
    placeholder: "Seleccionar",
    closeOnSelect: false,
    minimumResultsForSearch: 5,
    allowClear: false
});
//* SELECT EN MODAL EDITAR
$('#empleadoActiv').on("select2:opening", function () {
    var idE = $("#v_id").val();
    var value = $('#empleadoActiv').val();
    $('#empleadoActiv').empty();
    $.ajax({
        async: false,
        type: "GET",
        url: "/actividadOrga",
        data: {
            idEmpleado: idE
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            var option = "";
            $.each(data, function (i, items) {
                option += `<option value="${items.value}"> ${items.text}</option>`;
            });
            $('#empleadoActiv').append(option);
            $('#empleadoActiv').val(value);
        },
        error: function () { },
    });
});
$('#formActvidades').attr('novalidate', true);

$('#formActvidades').submit(function (e) {
    e.preventDefault();
    if ($('#empleadoActiv').val().length == 0) {
        $.notifyClose();
        $.notify({
            message: '\nSeleccionar Actividad',
            icon: 'landing/images/bell.svg',
        }, {
            element: $("#actividadTarea"),
            position: "fixed",
            icon_type: 'image',
            placement: {
                from: "top",
                align: "center",
            },
            allow_dismiss: true,
            newest_on_top: true,
            delay: 6000,
            template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
        return;
    }
    this.submit();
});
// ***********************************
$("#bodyModoProyecto").hide();
$("#regbodyModoProyecto").hide();
$("#bodyModoProyecto_ver").hide();
$("#customSwitch2").on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        $("#bodyModoProyecto").show();
    } else {
        $("#bodyModoProyecto").hide();
    }
});

$("#customSwitch4").on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        $("#regbodyModoProyecto").show();
    } else {
        $("#regbodyModoProyecto").hide();
    }
});
$("#customSwitch6").on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        $("#bodyModoProyecto_ver").show();
    } else {
        $("#bodyModoProyecto_ver").hide();
    }
});
// **************************************
//* ASIGNAR ACTIVIDAD - MODAL EDITAR
function registrarActividadTarea() {
    var idE = $("#v_id").val();
    var idA = $('#empleadoActiv').val();
    $.ajax({
        type: "POST",
        url: "/registrarAE",
        data: {
            idE: idE,
            idA: idA,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            limpiarSelect();
            actividadEmp();
            $.notifyClose();
            $.notify(
                {
                    message: "\nActividad registrada.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-ver"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
            $("#actividadTarea").modal("toggle");
        },
        error: function () { },
    });
}
//* ASIGNAR ACTIVIDAD - MODAL REGISTRAR
function registrarNuevaActividadTarea() {
    var idE = $("#idEmpleado").val();
    var idA = $('#regEmpleadoActiv').val();
    $.ajax({
        type: "POST",
        url: "/registrarAE",
        data: {
            idE: idE,
            idA: idA,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            limpiarSelect();
            actividad_empleado();
            $.notifyClose();
            $.notify(
                {
                    message: "\nActividad registrada.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-registrar"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
            $("#regactividadTarea").modal("toggle");
        },
        error: function () { },
    });
}
//* LIMPIAR SELECT
function limpiarSelect() {
    $('#empleadoActiv').val(null).trigger('change');
    $('#regEmpleadoActiv').val(null).trigger('change');
}
//  *******************************
//* EDITAR ESTADO MODAL EDITAR
function editarEstadoActividad(id, estado, idE) {
    $.ajax({
        type: "GET",
        url: "/editarEstadoA",
        data: {
            idA: id,
            estado: estado,
            idE: idE
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            actividadEmp();
            $.notifyClose();
            $.notify(
                {
                    message: "\nEstado Modificado.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-ver"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () { },
    });
}
//* FUNCION PRINCIAPL PARA EDITAR NOMBRE Y ESTADO - MODAL REGISTRAR
function RegeditarActE(idA) {
    var OriginalContent = $("#idActReg" + idA).val();

    $("#customSwitchActReg" + idA).on("change.bootstrapSwitch", function (
        event
    ) {
        if (event.target.checked == true) {
            var valor = 1;
        } else {
            var valor = 0;
        }
        alertify
            .confirm("??Desea modificar el estado de la  actividad?", function (
                e
            ) {
                if (e) {
                    editarEstadoActividadReg(idA, valor);
                }
            })
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
                    actividad_empleado();
                },
            });
    });
}

//* FUNCION PRINCIAPL PARA EDITAR NOMBRE Y ESTADO - MODAL EDITAR
function editarActE(idA) {
    var OriginalContent = $("#idAct" + idA).val();
    var idE = $("#v_id").val();

    $("#customSwitchAct" + idA).on("change.bootstrapSwitch", function (event) {
        if (event.target.checked == true) {
            var valor = 1;
        } else {
            var valor = 0;
        }
        alertify
            .confirm("??Desea modificar el estado de la  actividad?", function (
                e
            ) {
                if (e) {
                    editarEstadoActividad(idA, valor, idE);
                }
            })
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
                    actividadEmp();
                },
            });
    });
}
$('[data-toggle="tooltip"]').tooltip();
//: EVENTOS DE SWITCH EN FORMULARIO EDITAR
$('#customCRGE').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == false) {
        $('#customCRTGE').prop("checked", true);
    }
});
$('#customCRTGE').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == false) {
        $('#customCRGE').prop("checked", true);
    }
});
$('#FormregistrarActividad').attr('novalidate', true);
$('#FormregistrarActividad').submit(function (e) {
    e.preventDefault();
    if ($('#customCRGE').is(":checked") == false && $('#customCRTGE').is(":checked") == false) {
        $.notifyClose();
        $.notify({
            message: '\nSeleccionar un control',
            icon: 'landing/images/bell.svg',
        }, {
            element: $("#RegActividadTareaGE"),
            position: "fixed",
            icon_type: 'image',
            placement: {
                from: "top",
                align: "center",
            },
            allow_dismiss: true,
            newest_on_top: true,
            delay: 6000,
            template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
        return;
    }
    this.submit();
});
//* REGISTRAR ACTIVIADES EN FORMULARIO EDITAR
function registrarActividad() {
    var nombre = $("#nombreTarea").val();
    var codigo = $("#codigoTarea").val();
    var empleados = null;
    if ($('#customCRGE').is(":checked") == true) {
        var controlRemoto = 1;
    } else {
        var controlRemoto = 0;
    }
    if ($('#customCRTGE').is(":checked") == true) {
        var controlRuta = 1;
    } else {
        var controlRuta = 0;
    }

    $.ajax({
        type: "POST",
        url: "/registrarActvO",
        data: {
            nombre: nombre,
            cr: controlRemoto,
            crt: controlRuta,
            codigo: codigo,
            empleados: empleados,
            ap: 0,
            globalEmpleado: 0,
            globalArea: 0,
            asignacionEmpleado: 1,
            asignacionArea: 0
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            // BUSQUEDA POR NOMBRE
            if (data.estado === 1) {
                // RECUPERAR ACTIVIDAD INACTIVA
                if (data.actividad.estado == 0) {
                    alertify
                        .confirm("Ya existe una actividad inactiva con este nombre. ??Desea recuperarla si o no?", function (
                            e
                        ) {
                            if (e) {
                                recuperarActividad(data.actividad.Activi_id);
                            }
                        })
                        .setting({
                            title: "Modificar Actividad",
                            labels: {
                                ok: "Si",
                                cancel: "No",
                            },
                            modal: true,
                            startMaximized: false,
                            reverseButtons: true,
                            resizable: false,
                            closable: false,
                            transition: "zoom",
                            oncancel: function (closeEvent) {
                            },
                        });
                } else {
                    // ALERTA DE ACTIVIDAD ACTIVA EXISTENTE
                    $("#nombreTarea").addClass("borderColor");
                    $.notifyClose();
                    $.notify(
                        {
                            message:
                                "\nYa existe una actividad con este nombre.",
                            icon: "admin/images/warning.svg",
                        },
                        {
                            element: $('#RegActividadTareaGE'),
                            position: "fixed",
                            mouse_over: "pause",
                            placement: {
                                from: "top",
                                align: "center",
                            },
                            icon_type: "image",
                            newest_on_top: true,
                            delay: 2000,
                            template:
                                '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                "</div>",
                            spacing: 35,
                        }
                    );
                }
            } else {
                // BUSQUEDA POR CODIGO DE ACTIVIDAD
                if (data.estado === 0) {
                    if (data.actividad.estado == 0) {
                        alertify
                            .confirm("Ya existe una actividad inactiva con este c??digo. ??Desea recuperarla si o no?", function (
                                e
                            ) {
                                if (e) {
                                    recuperarActividad(data.actividad.Activi_id);
                                }
                            })
                            .setting({
                                title: "Modificar Actividad",
                                labels: {
                                    ok: "Si",
                                    cancel: "No",
                                },
                                modal: true,
                                startMaximized: false,
                                reverseButtons: true,
                                resizable: false,
                                closable: false,
                                transition: "zoom",
                                oncancel: function (closeEvent) {
                                },
                            });
                    } else {
                        $("#codigoTarea").addClass("borderColor");
                        $.notifyClose();
                        $.notify(
                            {
                                message:
                                    "\nYa existe una actividad con este c??digo.",
                                icon: "admin/images/warning.svg",
                            },
                            {
                                element: $('#RegActividadTareaGE'),
                                position: "fixed",
                                mouse_over: "pause",
                                placement: {
                                    from: "top",
                                    align: "center",
                                },
                                icon_type: "image",
                                newest_on_top: true,
                                delay: 2000,
                                template:
                                    '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 35,
                            }
                        );
                    }
                } else {
                    $('#RegActividadTareaGE').modal('toggle');
                    limpiarModo();
                    $('#form-ver').modal('show');
                    $('#empleadoActiv').trigger('select2:opening');
                    $('#empleadoActiv').val(data.Activi_id).trigger('change');
                    $.notifyClose();
                    $.notify(
                        {
                            message: "\nActividad registrada.",
                            icon: "admin/images/checked.svg",
                        },
                        {
                            element: $('#form-ver'),
                            position: "fixed",
                            icon_type: "image",
                            newest_on_top: true,
                            delay: 5000,
                            template:
                                '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                "</div>",
                            spacing: 35,
                        }
                    );
                }
            }
        },
        error: function () { },
    });
}
//* RECUPERAR ACTIVIDAD EN FORMULARIO EDITAR
function recuperarActividad(id) {
    $.ajax({
        type: "GET",
        url: "/recuperarA",
        data: {
            id: id
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            limpiarModo();
            $('#RegActividadTareaGE').modal('toggle');
            $('#form-ver').modal('show');
            actividadEmp();
            $.notifyClose();
            $.notify(
                {
                    message: "\nActividad Recuperada.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $('#form-ver'),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () { },
    });

}
// ***********************************************************************************************************
//* REGISTRAR ACTIVIDADES EN FORMULARIO REGISTRAR
//: EVENTOS DE SWITCH EN FORMULARIO REGISTRAR
$('#customCRFR').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == false) {
        $('#customCRTFR').prop("checked", true);
    }
});
$('#customCRTFR').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == false) {
        $('#customCRFR').prop("checked", true);
    }
});
$('#FormregistrarActividadFR').attr('novalidate', true);
$('#FormregistrarActividadFR').submit(function (e) {
    e.preventDefault();
    if ($('#customCRFR').is(":checked") == false && $('#customCRTFR').is(":checked") == false) {
        $.notifyClose();
        $.notify({
            message: '\nSeleccionar un control',
            icon: 'landing/images/bell.svg',
        }, {
            element: $("#ActividadTareaGE"),
            position: "fixed",
            icon_type: 'image',
            placement: {
                from: "top",
                align: "center",
            },
            allow_dismiss: true,
            newest_on_top: true,
            delay: 6000,
            template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
        return;
    }
    this.submit();
});
function registrarActividadFR() {
    var nombre = $("#reg_nombreTarea").val();
    var codigo = $("#reg_codigoTarea").val();
    var empleados = null;
    if ($('#customCRFR').is(":checked") == true) {
        var controlRemoto = 1;
    } else {
        var controlRemoto = 0;
    }
    if ($('#customCRTFR').is(":checked") == true) {
        var controlRuta = 1;
    } else {
        var controlRuta = 0;
    }
    $.ajax({
        type: "POST",
        url: "/registrarActvO",
        data: {
            nombre: nombre,
            cr: controlRemoto,
            crt: controlRuta,
            codigo: codigo,
            empleados: empleados,
            ap: 0,
            globalEmpleado: 0,
            globalArea: 0,
            asignacionEmpleado: 1,
            asignacionArea: 0
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            // BUSQUEDA POR NOMBRE
            if (data.estado === 1) {
                // RECUPERAR ACTIVIDAD INACTIVA
                if (data.actividad.estado == 0) {
                    alertify
                        .confirm("Ya existe una actividad inactiva con este nombre. ??Desea recuperarla si o no?", function (
                            e
                        ) {
                            if (e) {
                                recuperarActividadFR(data.actividad.Activi_id);
                            }
                        })
                        .setting({
                            title: "Modificar Actividad",
                            labels: {
                                ok: "Si",
                                cancel: "No",
                            },
                            modal: true,
                            startMaximized: false,
                            reverseButtons: true,
                            resizable: false,
                            closable: false,
                            transition: "zoom",
                            oncancel: function (closeEvent) {
                            },
                        });
                } else {
                    // ALERTA DE ACTIVIDAD ACTIVA EXISTENTE
                    $("#reg_nombreTarea").addClass("borderColor");
                    $.notifyClose();
                    $.notify(
                        {
                            message:
                                "\nYa existe una actividad con este nombre.",
                            icon: "admin/images/warning.svg",
                        },
                        {
                            element: $('#ActividadTareaGE'),
                            position: "fixed",
                            mouse_over: "pause",
                            placement: {
                                from: "top",
                                align: "center",
                            },
                            icon_type: "image",
                            newest_on_top: true,
                            delay: 2000,
                            template:
                                '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                "</div>",
                            spacing: 35,
                        }
                    );
                }
            } else {
                // BUSQUEDA POR CODIGO DE ACTIVIDAD
                if (data.estado === 0) {
                    if (data.actividad.estado == 0) {
                        alertify
                            .confirm("Ya existe una actividad inactiva con este c??digo. ??Desea recuperarla si o no?", function (
                                e
                            ) {
                                if (e) {
                                    recuperarActividadFR(data.actividad.Activi_id);
                                }
                            })
                            .setting({
                                title: "Modificar Actividad",
                                labels: {
                                    ok: "Si",
                                    cancel: "No",
                                },
                                modal: true,
                                startMaximized: false,
                                reverseButtons: true,
                                resizable: false,
                                closable: false,
                                transition: "zoom",
                                oncancel: function (closeEvent) {
                                },
                            });
                    } else {
                        $("#reg_codigoTarea").addClass("borderColor");
                        $.notifyClose();
                        $.notify(
                            {
                                message:
                                    "\nYa existe una actividad con este c??digo.",
                                icon: "admin/images/warning.svg",
                            },
                            {
                                element: $('#ActividadTareaGE'),
                                position: "fixed",
                                mouse_over: "pause",
                                placement: {
                                    from: "top",
                                    align: "center",
                                },
                                icon_type: "image",
                                newest_on_top: true,
                                delay: 2000,
                                template:
                                    '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 35,
                            }
                        );
                    }
                } else {
                    $('#ActividadTareaGE').modal('toggle');
                    limpiarModo();
                    $('#form-registrar').modal('show');
                    $('#regEmpleadoActiv').trigger('select2:opening');
                    $('#regEmpleadoActiv').val(data.Activi_id).trigger('change');
                    $.notifyClose();
                    $.notify(
                        {
                            message: "\nActividad registrada.",
                            icon: "admin/images/checked.svg",
                        },
                        {
                            element: $('#form-registrar'),
                            position: "fixed",
                            icon_type: "image",
                            newest_on_top: true,
                            delay: 5000,
                            template:
                                '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                "</div>",
                            spacing: 35,
                        }
                    );
                }
            }
        },
        error: function () { },
    });
}
// RECUPERAR ACTIVIDAD
function recuperarActividadFR(id) {
    $.ajax({
        type: "GET",
        url: "/recuperarA",
        data: {
            id: id
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            limpiarModo();
            $('#ActividadTareaGE').modal('toggle');
            $('#form-registrar').modal('show');
            actividad_empleado();
            $.notifyClose();
            $.notify(
                {
                    message: "\nActividad Recuperada.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $('#form-registrar'),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () { },
    });

}
// ***********************************************************************************************************
// LIMPIEZA DE MODAL
function limpiarModo() {
    $('#nombreTarea').val("");
    $('#codigoTarea').val("");
    $('#reg_nombreTarea').val("");
    $('#reg_codigoTarea').val("");
    $('#customCRTGE').prop("checked", false);
    $('#customCRGE').prop("checked", false);
    $('#customCRFR').prop("checked", false);
    $('#customCRTFR').prop("checked", false);
}
//REMOVER CLASES
$("#nombreTarea").keyup(function () {
    $(this).removeClass("borderColor");
});
$("#codigoTarea").keyup(function () {
    $(this).removeClass("borderColor");
});
$("#reg_nombreTarea").keyup(function () {
    $(this).removeClass("borderColor");
});
$("#reg_codigoTarea").keyup(function () {
    $(this).removeClass("borderColor");
});
//TODO -> MODOS EN VISTA TABLA
//: MODO CONTROL REMOTO
function controlRemoto(id, dato) {
    var idEmpleado = id;
    var empleado = dato;
    $("#customSwitchCR" + idEmpleado).on("change.bootstrapSwitch", function (
        event
    ) {
        if (event.target.checked == true) {
            $("#modalControlR").modal();
            $("#empleadoControlR").val(idEmpleado);
            $('#nombreECR').text(empleado);
        }
    });
}

function agregarControlR(id) {
    var idEmpleado = id;
    $.ajax({
        async: false,
        type: "get",
        url: "vinculacionControlRemoto",
        data: {
            idEmpleado: idEmpleado,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            if (data == 1) {
                $("#modalControlR").modal("toggle");
                RefreshTablaEmpleado();
                $.notifyClose();
                agregarCorreoE(idEmpleado);
            } else {
                $("#modalControlR").modal("toggle");
                RefreshTablaEmpleado();
                showNotificaciones();
                $.notify(
                    {
                        message: "\nRegistro de dispositivo y correo enviado con exito\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        icon_type: "image",
                        newest_on_top: true,
                        delay: 5000,
                        template:
                            '<div data-notify="container" class="col-xs-8 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            "</div>",
                        spacing: 35,
                    }
                );
            }
        },
        error: function () { },
    });
}

function agregarCorreoE(id) {
    $.notifyClose();
    $("#idEmpleCorreo").val(id);
    $("#modalCorreoElectronico").modal();
    $.notify(
        {
            message:
                "\nPara registrar un dispositivo de Control Remoto necesitamos el correo electr??nico del empleado.",
            icon: "admin/images/warning.svg",
        },
        {
            element: $("#modalCorreoElectronico"),
            position: "fixed",
            mouse_over: "pause",
            placement: {
                from: "top",
                align: "center",
            },
            icon_type: "image",
            newest_on_top: true,
            delay: 2000,
            template:
                '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                "</div>",
            spacing: 35,
        }
    );
}

function guardarCorreoE() {
    idEmpleado = $("#idEmpleCorreo").val();
    descripcion = $("#textCorreo").val();
    email = $("#textCorreo").val();
    $.ajax({
        async: false,
        type: "GET",
        url: "email",
        data: {
            email: email,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (data) {
            if (data == 1) {
                $.notify(
                    {
                        message:
                            "\nCorreo Electr??nico ya se encuentra registrado.",
                        icon: "admin/images/warning.svg",
                    },
                    {
                        element: $("#modalCorreoElectronico"),
                        position: "fixed",
                        placement: {
                            from: "top",
                            align: "center",
                        },
                        icon_type: "image",
                        mouse_over: "pause",
                        newest_on_top: true,
                        delay: 3000,
                        template:
                            '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                            "</div>",
                        spacing: 35,
                    }
                );
                return false;
            } else {
                $.ajax({
                    async: false,
                    type: "get",
                    url: "/empleado/agregarCorreo",
                    data: {
                        idEmpleado: idEmpleado,
                        correo: descripcion,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        RefreshTablaEmpleado();
                        $("#modalCorreoElectronico").modal("toggle");
                        $("#modalControlR").modal();
                        $("#empleadoControlR").val(idEmpleado);
                        $.notifyClose();
                        $.notify(
                            {
                                message: "\nCorreo electr??nico registrado.",
                                icon: "admin/images/checked.svg",
                            },
                            {
                                position: "fixed",
                                element: $("#modalControlR"),
                                icon_type: "image",
                                newest_on_top: true,
                                delay: 5000,
                                template:
                                    '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 35,
                            }
                        );
                    },
                    error: function () { },
                });
            }
        },
    });
}

//* INACTIVAR
function inactivarEstadoCR(idEmpleado, idVinculacion) {
    //NOTIFICACION
    $.ajax({
        async: false,
        type: "get",
        url: "/cambiarEstadoLicencia",
        data: {
            idE: idEmpleado,
            idV: idVinculacion,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (data) {
            RefreshTablaEmpleado();
            $.notifyClose();
            $.notify(
                {
                    message: "\nProceso con ??xito.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () {
            RefreshTablaEmpleado();
            $.notifyClose();
            agregarCorreoE(idEmpleado);
        },
    });
}
//* ACTIVAR
function activarEstadoCR(idEmpleado, idVinculacion) {
    $.ajax({
        async: false,
        type: "get",
        url: "correoWindows",
        data: {
            idEmpleado: idEmpleado,
            idVinculacion: idVinculacion,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            RefreshTablaEmpleado();
            $.notifyClose();
            $.notify(
                {
                    message: "\nProceso con ??xito.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2  text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () {
            $.notifyClose();
            $.notify({
                message: "\nA??n no ha registrado correo a empleado.",
                icon: 'admin/images/warning.svg'
            }, {
                position: "fixed",
                icon_type: "image",
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
    });
}
function estadoDispositivoCR(idEmpleado, id, pc, datos) {
    $("#customSwitchCRDisp" + id).on("change.bootstrapSwitch", function (
        event
    ) {
        if (event.target.checked == true) {
            alertify
                .confirm(
                    "<img src=\"admin/images/tick.svg\" height=\"20\" class=\"mr-1\">??Activar el computador <strong>" +
                    pc +
                    "</strong> de&nbsp;" +
                    datos +
                    "&nbsp;y enviar un correo con sus credenciales?",
                    function (e) {
                        if (e) {
                            activarEstadoCR(idEmpleado, id);
                        }
                    }
                )
                .setting({
                    title: "Activar dispositivo - Modo Control Remoto",
                    labels: {
                        ok: "Aceptar",
                        cancel: "Cancelar",
                    },
                    modal: true,
                    startMaximized: false,
                    reverseButtons: true,
                    resizable: false,
                    transition: "zoom",
                    closable: false,
                    oncancel: function (closeEvent) {
                        RefreshTablaEmpleado();
                    },
                });
        } else {
            alertify
                .confirm(
                    "<img src=\"/landing/images/alert1.svg\" height=\"20\" class=\"mr-1 mt-0\">El empleado no p??dra usar esta licencia.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                    Tiempo estimado: 60 minutos.",
                    function (e) {
                        if (e) {
                            inactivarEstadoCR(idEmpleado, id);
                        }
                    }
                )
                .setting({
                    title: "Desactivar dispositivo - Modo Control Remoto",
                    labels: {
                        ok: "Aceptar",
                        cancel: "Cancelar",
                    },
                    modal: true,
                    startMaximized: false,
                    reverseButtons: true,
                    resizable: false,
                    transition: "zoom",
                    closable: false,
                    oncancel: function (closeEvent) {
                        RefreshTablaEmpleado();
                    },
                });
        }
    });
}

function limpiarCorreoE() {
    $("#textCorreo").val("");
}

//: MODO CONTROL RUTA
function controlRuta(id, dato) {
    var idEmpleado = id;
    var empleado = dato;
    $("#customSwitchCRT" + idEmpleado).on("change.bootstrapSwitch", function (
        event
    ) {
        if (event.target.checked == true) {
            $("#modalControlRT").modal();
            $("#empleadoControlRT").val(idEmpleado);
            $('#nombreECRT').text(empleado);
        }
    });
}
$('#FormguardarCelularE').attr('novalidate', true);
$('#FormguardarCelularE').submit(function (e) {
    e.preventDefault();
    if ($('#textCelular').val().length != 9) {
        $.notifyClose();
        $.notify({
            message: '\nN??mero de celular incorrecto.',
            icon: 'landing/images/bell.svg',
        }, {
            element: $("#modalCelular"),
            position: "fixed",
            icon_type: 'image',
            placement: {
                from: "top",
                align: "center",
            },
            allow_dismiss: true,
            newest_on_top: true,
            delay: 6000,
            template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
        return;
    }
    this.submit();
});
function agregarControlRT(id) {
    var idEmpleado = id;
    $.ajax({
        async: false,
        type: "get",
        url: "/vinculacionControlRuta",
        data: {
            idEmpleado: idEmpleado,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    })
        .done(function (data) {
            if (data.respuesta != undefined) {
                if (data.respuesta == 1) {
                    $("#modalControlRT").modal("toggle");
                    RefreshTablaEmpleado();
                    $.notifyClose();
                    agregarCelularE(idEmpleado);
                } else {
                    $("#modalControlRT").modal("toggle");
                    RefreshTablaEmpleado();
                    showNotificaciones();
                    $.notify(
                        {
                            message: "\nRegistro de dispositivo con ??xito\n",
                            icon: "admin/images/checked.svg",
                        },
                        {
                            icon_type: "image",
                            newest_on_top: true,
                            delay: 5000,
                            template:
                                '<div data-notify="container" class="col-xs-8 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                "</div>",
                            spacing: 35,
                        }
                    );
                }
            }
        }).always(function (data) {
            if (data.respuesta != undefined) {
                if (data.respuesta == 0) {
                    if (data.estado == 1) {
                        $.notify(
                            {
                                message: data.mensaje,
                                icon: "admin/images/checked.svg",
                            },
                            {
                                icon_type: "image",
                                newest_on_top: true,
                                delay: 5000,
                                template:
                                    '<div data-notify="container" class="col-xs-8 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 35,
                            }
                        );
                    } else {
                        $.notify({
                            message: "\nTenemos problemas con el servidor mensajeria.Comunicarse con nosotros",
                            icon: 'admin/images/warning.svg'
                        }, {
                            position: 'fixed',
                            icon_type: 'image',
                            newest_on_top: true,
                            delay: 5000,
                            template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
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
}
function agregarCelularE(id) {
    $.notifyClose();
    $("#idEmpleCelular").val(id);
    $("#modalCelular").modal();
    $.notify(
        {
            message:
                "\nPara activar el Modo Ruta, tienes que agregar un n??mero de tel??fono al empleado.",
            icon: "admin/images/warning.svg",
        },
        {
            element: $("#modalCelular"),
            position: "fixed",
            mouse_over: "pause",
            placement: {
                from: "top",
                align: "center",
            },
            icon_type: "image",
            newest_on_top: true,
            delay: 2000,
            template:
                '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                "</div>",
            spacing: 35,
        }
    );
}
function guardarCelularE() {
    idEmpleado = $("#idEmpleCelular").val();
    celular = $("#textCelular").val();
    $.ajax({
        async: false,
        type: "get",
        url: "/empleado/agregarCelular",
        data: {
            idEmpleado: idEmpleado,
            celular: celular,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $('#textCelular').val("")
            RefreshTablaEmpleado();
            $("#modalCelular").modal("toggle");
            $("#modalControlRT").modal();
            $("#empleadoControlRT").val(idEmpleado);
            $.notifyClose();
            $.notify(
                {
                    message: "\nCelular registrado.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    element: $("#modalControlRT"),
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () { },
    });
}
function estadoDispositivoCRT(idEmpleado, id, cel, datos) {
    $("#customSwitchCRTDisp" + id).on("change.bootstrapSwitch", function (
        event
    ) {
        if (event.target.checked == true) {
            alertify
                .confirm(
                    "<img src=\"admin/images/tick.svg\" height=\"20\" class=\"mr-1\">??Activar el celular <strong>" +
                    cel +
                    "</strong> de&nbsp;" +
                    datos +
                    "&nbsp;y enviar un sms con sus credenciales?",
                    function (e) {
                        if (e) {
                            activarEstadoCRT(id);
                        }
                    }
                )
                .setting({
                    title: "Activar dispositivo - Modo Control Ruta",
                    labels: {
                        ok: "Aceptar",
                        cancel: "Cancelar",
                    },
                    modal: true,
                    startMaximized: false,
                    reverseButtons: true,
                    resizable: false,
                    transition: "zoom",
                    closable: false,
                    oncancel: function (closeEvent) {
                        RefreshTablaEmpleado();
                    },
                });
        } else {
            alertify
                .confirm(
                    "<img src=\"/landing/images/alert1.svg\" height=\"20\" class=\"mr-1 mt-0\">El empleado no p??dra usar este dispositivo.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
                    Tiempo estimado: 60 minutos.",
                    function (e) {
                        if (e) {
                            inactivarEstadoCRT(idEmpleado, id);
                        }
                    }
                )
                .setting({
                    title: "Desactivar dispositivo - Modo Control Ruta",
                    labels: {
                        ok: "Aceptar",
                        cancel: "Cancelar",
                    },
                    modal: true,
                    startMaximized: false,
                    reverseButtons: true,
                    resizable: false,
                    transition: "zoom",
                    closable: false,
                    oncancel: function (closeEvent) {
                        RefreshTablaEmpleado();
                    },
                });
        }
    });
}
//* INACTIVAR
function inactivarEstadoCRT(idEmpleado, idVinculacion) {
    //NOTIFICACION
    $.ajax({
        async: false,
        type: "get",
        url: "/cambiarEstadoVinculacionRuta",
        data: {
            idE: idEmpleado,
            idV: idVinculacion,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (data) {
            RefreshTablaEmpleado();
            $.notifyClose();
            $.notify(
                {
                    message: "\nProceso con ??xito.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () {
            RefreshTablaEmpleado();
            $.notifyClose();
            agregarCorreoE(idEmpleado);
        },
    });
}
//* ACTIVAR
function activarEstadoCRT(idVinculacion) {
    $.ajax({
        async: false,
        type: "get",
        url: "/smsAndroid",
        data: {
            id: idVinculacion,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            // if (data == 1) {
            //     RefreshTablaEmpleado();
            //     $.notifyClose();
            //     $.notify({
            //         message: "\nTenemos problemas con el servidor mensajeria.Comunicarse con nosotros",
            //         icon: 'admin/images/warning.svg'
            //     }, {
            //         position: 'fixed',
            //         icon_type: 'image',
            //         newest_on_top: true,
            //         delay: 5000,
            //         template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
            //             '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
            //             '<img data-notify="icon" class="img-circle pull-left" height="20">' +
            //             '<span data-notify="title">{1}</span> ' +
            //             '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
            //             '</div>',
            //         spacing: 35
            //     });
            // } else {
            //     RefreshTablaEmpleado();
            //     $.notifyClose();
            //     $.notify(
            //         {
            //             message: "\nProceso con ??xito.",
            //             icon: "admin/images/checked.svg",
            //         },
            //         {
            //             position: "fixed",
            //             icon_type: "image",
            //             newest_on_top: true,
            //             delay: 5000,
            //             template:
            //                 '<div data-notify="container" class="col-xs-8 col-sm-2  text-center alert" style="background-color: #dff0d8;" role="alert">' +
            //                 '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
            //                 '<img data-notify="icon" class="img-circle pull-left" height="20">' +
            //                 '<span data-notify="title">{1}</span> ' +
            //                 '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
            //                 "</div>",
            //             spacing: 35,
            //         }
            //     );
            // }
            if (data.respuesta != undefined) {
                if (data.respuesta == 1) {
                    RefreshTablaEmpleado();
                    $.notifyClose();
                    $.notify({
                        message: "\nTenemos problemas con el servidor mensajeria.Comunicarse con nosotros",
                        icon: 'admin/images/warning.svg'
                    }, {
                        position: 'fixed',
                        icon_type: 'image',
                        newest_on_top: true,
                        delay: 5000,
                        template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                            '</div>',
                        spacing: 35
                    });
                }
            } else {
                RefreshTablaEmpleado();
                $.notifyClose();
                $.notify(
                    {
                        message: "\nProceso con ??xito.",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        position: "fixed",
                        icon_type: "image",
                        newest_on_top: true,
                        delay: 5000,
                        template:
                            '<div data-notify="container" class="col-xs-8 col-sm-2  text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            "</div>",
                        spacing: 35,
                    }
                );
            }
        },
        error: function () { },
    });
}