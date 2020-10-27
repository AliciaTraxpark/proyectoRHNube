$.fn.select2.defaults.set('language', 'es');
function tablaActividades() {
    $("#actividades").DataTable({
        scrollX: true,
        responsive: true,
        retrieve: true,
        "searching": false,
        "lengthChange": false,
        scrollCollapse: false,
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
        }
    });
}
function editarActividad(id) {
    $.ajax({
        async: false,
        type: "POST",
        url: "/editarA",
        data: {
            idA: id
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $('#idActiv').val(data.Activi_id);
            $('#e_nombreTarea').val(data.Activi_Nombre);
            $('#e_codigoTarea').val(data.codigoActividad);
            if (data.codigoActividad === null) {
                $('#e_codigoTarea').attr("disabled", false);
            } else {
                $('#e_codigoTarea').attr("disabled", true);
            }
            if (data.controlRemoto === 1) {
                $('#e_customCR').prop("checked", true);
            } else {
                $('#e_customCR').prop("checked", false);
            }
            if (data.asistenciaPuerta === 1) {
                $('#e_customAP').prop("checked", true);
            } else {
                $('#e_customAP').prop("checked", false);
            }
        },
        error: function () { },
    });
    $('#editactividadTarea').modal();
}
function editarActividadTarea() {
    var codigo = $("#e_codigoTarea").val();
    var idA = $('#idActiv').val();
    if ($('#e_customCR').is(":checked") == true) {
        var controlRemoto = 1;
    } else {
        var controlRemoto = 0;
    }
    if ($('#e_customAP').is(":checked") == true) {
        var asistenciaPuerta = 1;
    } else {
        var asistenciaPuerta = 0;
    }
    $.ajax({
        type: "GET",
        url: "/registrarEditar",
        data: {
            idA: idA,
            cr: controlRemoto,
            ap: asistenciaPuerta,
            codigo: codigo
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            limpiarModo();
            actividadesOrganizacion();
            $.notifyClose();
            $.notify(
                {
                    message: "\nActividad modificada.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
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
            $('#editactividadTarea').modal("toggle");
        },
        error: function () { },
    });
}
function eliminarActividad(id) {
    alertify
        .confirm("¿Desea eliminar actividad?", function (
            e
        ) {
            if (e) {
                $.ajax({
                    type: "GET",
                    url: "/estadoActividad",
                    data: {
                        id: id
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (data) {
                        if (data == 1) {
                            $.notifyClose();
                            $.notify({
                                message: '\nActividad en uso, no se puede eliminar.',
                                icon: '/landing/images/alert1.svg',
                            }, {
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
                        } else {
                            actividadesOrganizacion();
                            $.notifyClose();
                            $.notify({
                                message: '\nActividad eliminada',
                                icon: 'landing/images/bell.svg',
                            }, {
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
                        }
                    },
                    error: function () { },
                });
            }
        })
        .setting({
            title: "Eliminar Actividad",
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
                actividadesOrganizacion();
            },
        });
}
function actividadesOrganizacion() {
    if ($.fn.DataTable.isDataTable("#actividades")) {
        $('#actividades').DataTable().destroy();
    }
    $('#actividOrga').empty();
    $.ajax({
        async: false,
        url: "/actividadOrg",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
            var tr = "";
            for (let index = 0; index < data.length; index++) {
                tr += "<tr class=\"text-center\" onclick=\"return cambiarEstadoActividad(" + data[index].Activi_id + ")\"><td>" + (index + 1) + "</td>";
                tr += "<td>" + data[index].Activi_Nombre + "</td>";
                tr += "<td>" + data[index].codigoA + "</td>";
                if (data[index].eliminacion == 0) {
                    if (data[index].controlRemoto == 1) {
                        tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                        <input type=\"checkbox\" class=\"custom-control-input\"\
                            id=\"switchActvCR"+ data[index].Activi_id + "\" checked disabled>\
                        <label class=\"custom-control-label\" for=\"switchActvCR"+ data[index].Activi_id + "\"\
                            style=\"font-weight: bold\"></label>\
                        </div></td>";
                    } else {
                        tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                        <input type=\"checkbox\" class=\"custom-control-input\"\
                            id=\"switchActvCR"+ data[index].Activi_id + "\" disabled>\
                        <label class=\"custom-control-label\" for=\"switchActvCR"+ data[index].Activi_id + "\"\
                            style=\"font-weight: bold\"></label>\
                        </div></td>";
                    }
                    if (data[index].asistenciaPuerta == 1) {
                        tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                            <input type=\"checkbox\" class=\"custom-control-input\"\
                                id=\"switchActvAP"+ data[index].Activi_id + "\" checked disabled>\
                            <label class=\"custom-control-label\" for=\"switchActvAP"+ data[index].Activi_id + "\"\
                                style=\"font-weight: bold\"></label>\
                            </div></td>";
                    } else {
                        tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                            <input type=\"checkbox\" class=\"custom-control-input\"\
                                id=\"switchActvAP"+ data[index].Activi_id + "\" disabled>\
                            <label class=\"custom-control-label\" for=\"switchActvAP"+ data[index].Activi_id + "\"\
                                style=\"font-weight: bold\"></label>\
                            </div></td>";
                    }
                    if (data[index].respuesta === 'Si') {
                        tr += "<td style=\"font-size:12px\"><img src=\"/admin/images/checkH.svg\" height=\"13\" class=\"mr-2\">" + data[index].respuesta + "</td>";
                    } else {
                        tr += "<td style=\"font-size:12px\"><img src=\"/admin/images/borrarH.svg\" height=\"11\" class=\"mr-2\">" + data[index].respuesta + "</td>";
                    }
                    tr += "<td><a class=\"badge badge-soft-primary mr-2\">Predeterminado</a></td>";
                } else {
                    if (data[index].controlRemoto == 1) {
                        tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                        <input type=\"checkbox\" class=\"custom-control-input\"\
                            id=\"switchActvCR"+ data[index].Activi_id + "\" checked>\
                        <label class=\"custom-control-label\" for=\"switchActvCR"+ data[index].Activi_id + "\"\
                            style=\"font-weight: bold\"></label>\
                        </div></td>";
                    } else {
                        tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                        <input type=\"checkbox\" class=\"custom-control-input\"\
                            id=\"switchActvCR"+ data[index].Activi_id + "\">\
                        <label class=\"custom-control-label\" for=\"switchActvCR"+ data[index].Activi_id + "\"\
                            style=\"font-weight: bold\"></label>\
                        </div></td>";
                    }
                    if (data[index].asistenciaPuerta == 1) {
                        tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                            <input type=\"checkbox\" class=\"custom-control-input\"\
                                id=\"switchActvAP"+ data[index].Activi_id + "\" checked>\
                            <label class=\"custom-control-label\" for=\"switchActvAP"+ data[index].Activi_id + "\"\
                                style=\"font-weight: bold\"></label>\
                            </div></td>";
                    } else {
                        tr += "<td><div class=\"custom-control custom-switch mb-2\">\
                            <input type=\"checkbox\" class=\"custom-control-input\"\
                                id=\"switchActvAP"+ data[index].Activi_id + "\">\
                            <label class=\"custom-control-label\" for=\"switchActvAP"+ data[index].Activi_id + "\"\
                                style=\"font-weight: bold\"></label>\
                            </div></td>";
                    }
                    if (data[index].respuesta === 'Si') {
                        tr += "<td style=\"font-size:12px\"><img src=\"/admin/images/checkH.svg\" height=\"13\" class=\"mr-2\">" + data[index].respuesta + "</td>";
                    } else {
                        tr += "<td style=\"font-size:12px\"><img src=\"/admin/images/borrarH.svg\" height=\"11\" class=\"mr-2\">" + data[index].respuesta + "</td>";
                    }
                    tr += "<td><a onclick=\"javascript:editarActividad(" + data[index].Activi_id + ")\" style=\"cursor: pointer\">\
                                 <img src=\"/admin/images/edit.svg\" height=\"15\">\
                                </a>&nbsp;&nbsp;&nbsp;<a onclick=\"javascript:eliminarActividad(" + data[index].Activi_id + ")\" style=\"cursor: pointer\">\
                                    <img src=\"/admin/images/delete.svg\" height=\"15\">\
                                 </a></td>";
                }
                tr += "</tr>";
            }
            $('#actividOrga').html(tr);
            tablaActividades();
        },
        error: function () {

        }
    });
}
actividadesOrganizacion();
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
            // $.notifyClose();
            // $.notify(
            //     {
            //         message: "\nActividad recuperada.",
            //         icon: "admin/images/checked.svg",
            //     },
            //     {
            //         position: "fixed",
            //         icon_type: "image",
            //         newest_on_top: true,
            //         delay: 5000,
            //         template:
            //             '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
            //             '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
            //             '<img data-notify="icon" class="img-circle pull-left" height="20">' +
            //             '<span data-notify="title">{1}</span> ' +
            //             '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
            //             "</div>",
            //         spacing: 35,
            //     }
            // );
            limpiarModo();
            actividadesOrganizacion();
            $('#regactividadTarea').modal('toggle');
            editarActividad(data.Activi_id);
        },
        error: function () { },
    });

}
function registrarActividadTarea() {
    var nombre = $("#nombreTarea").val();
    var codigo = $("#codigoTarea").val();
    if ($('#customCR').is(":checked") == true) {
        var controlRemoto = 1;
    } else {
        var controlRemoto = 0;
    }
    if ($('#customAP').is(":checked") == true) {
        var asistenciaPuerta = 1;
    } else {
        var asistenciaPuerta = 0;
    }
    $.ajax({
        type: "POST",
        url: "/registrarActvO",
        data: {
            nombre: nombre,
            cr: controlRemoto,
            ap: asistenciaPuerta,
            codigo: codigo
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            if (data.estado === 1) {
                if (data.actividad.estado == 0) {
                    alertify
                        .confirm("Ya existe una actividad inactiva con este nombre. ¿Desea recuperarla si o no?", function (
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
                    $("#nombreTarea").addClass("borderColor");
                    $.notifyClose();
                    $.notify(
                        {
                            message:
                                "\nYa existe una actividad con este nombre.",
                            icon: "admin/images/warning.svg",
                        },
                        {
                            element: $('#regactividadTarea'),
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
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                "</div>",
                            spacing: 35,
                        }
                    );
                }
            } else {
                if (data.estado === 0) {
                    if (data.actividad.estado == 0) {
                        alertify
                            .confirm("Ya existe una actividad inactiva con este código. ¿Desea recuperarla si o no?", function (
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
                                    "\nYa existe una actividad con este código.",
                                icon: "admin/images/warning.svg",
                            },
                            {
                                element: $('#regactividadTarea'),
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
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 35,
                            }
                        );
                    }
                } else {
                    limpiarModo();
                    actividadesOrganizacion();
                    $('#regactividadTarea').modal('toggle');
                    $.notifyClose();
                    $.notify(
                        {
                            message: "\nActividad registrada.",
                            icon: "admin/images/checked.svg",
                        },
                        {
                            position: "fixed",
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
                }
            }
        },
        error: function () { },
    });
}

function cambiarEstadoParaControles(id, valor, control) {
    $.ajax({
        type: "POST",
        url: "/estadoActividadControl",
        data: {
            id: id,
            valor: valor,
            control: control
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            actividadesOrganizacion();
            $.notifyClose();
            $.notify(
                {
                    message: "\nActividad Modificada.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
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
        error: function () { },
    });
}
function limpiarModo() {
    $('#nombreTarea').val("");
    $('#codigoTarea').val("");
    $('#customCR').prop("checked", false);
    $('#customAP').prop("checked", false);
    $('#e_nombreTarea').val("");
    $('#e_codigoTarea').val("");
    $('#e_customCR').prop("checked", false);
    $('#e_customAP').prop("checked", false);
}

function cambiarEstadoActividad(id) {

    $("#switchActvCR" + id).on("change.bootstrapSwitch", function (event) {
        var control = "CR";
        if (event.target.checked == true) {
            var valor = 1;
        } else {
            var valor = 0;
        }
        alertify
            .confirm("¿Desea modificar el estado de la  actividad?", function (
                e
            ) {
                if (e) {
                    cambiarEstadoParaControles(id, valor, control);
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
                    actividadesOrganizacion();
                },
            });
    });

    $("#switchActvAP" + id).on("change.bootstrapSwitch", function (event) {
        var control = "AP";
        if (event.target.checked == true) {
            var valor = 1;
        } else {
            var valor = 0;
        }
        alertify
            .confirm("¿Desea modificar el estado de la  actividad?", function (
                e
            ) {
                if (e) {
                    cambiarEstadoParaControles(id, valor, control);
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
                    actividadesOrganizacion();
                },
            });
    });
}

//REMVER CLASES
$("#nombreTarea").keyup(function () {
    $(this).removeClass("borderColor");
});
$("#codigoTarea").keyup(function () {
    $(this).removeClass("borderColor");
});

// SELECT DE EMPLEADOS
$("#empleados").select2({
    placeholder: 'Seleccionar Empleados',
    tags: "true"
});
$("#empleados").on("select2:opening", function () {
    var value = $("#empleados").val();
    $("#empleados").empty();
    var container = $("#empleados");
    $.ajax({
        async: false,
        url: "/empleadoActiv",
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
        },
        success: function (data) {
            console.log(data);
            var option = `<option value="" disabled selected>Seleccionar</option>`;
            for (var $i = 0; $i < data.length; $i++) {
                option += `<option value="${data[$i].emple_id}">${data[$i].nombre} ${data[$i].apPaterno} ${data[$i].apMaterno}</option>`;
            }
            container.append(option);
            $("#empleados").val(value);
        },
        error: function () { },
    });
});