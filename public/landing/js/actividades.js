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
                    tr += "<td><a onclick=\"javascript:eliminarActividad(" + data[index].Activi_id + ")\" style=\"cursor: pointer\">\
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

function registrarActividadTarea() {
    var nombre = $("#nombreTarea").val();
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
        url: "/registrarActvE",
        data: {
            nombre: nombre,
            cr: controlRemoto,
            ap: asistenciaPuerta
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
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
                    element: $("#form-ver"),
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
            $("#actividadTarea").modal("toggle");
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
    $('#customCR').prop("checked", false);
    $('#customAP').prop("checked", false);
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
