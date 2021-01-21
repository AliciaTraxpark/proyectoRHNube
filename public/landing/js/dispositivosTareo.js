$(document).ready(function () {
    /*-- TABLA DATATABLE DE DISPOSITIVOS---------- */
    var table = $("#tablaDips").DataTable({
        searching: true,
        /* "lengthChange": false,
       "scrollX": true, */
        processing: true,

        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando registros del _START_ al _END_ ",
            sInfoEmpty:
                "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sInfoPostFix: "",
            sSearch: "Buscar:",
            sUrl: "",
            sInfoThousands: ",",
            sLoadingRecords: "Cargando...",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: ">",
                sPrevious: "<",
            },
            oAria: {
                sSortAscending:
                    ": Activar para ordenar la columna de manera ascendente",
                sSortDescending:
                    ": Activar para ordenar la columna de manera descendente",
            },
            buttons: {
                copy: "Copiar",
                colvis: "Visibilidad",
            },
        },

        ajax: {
            type: "post",
            url: "/tablaDispositoTareo",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            dataSrc: "",
        },

        columnDefs: [
            {
                searchable: false,
                orderable: false,
                targets: 0,
            },
        ],
        order: [[1, "asc"]],
        columns: [
            {
                data: "dispoT_codigo",
                render: function (data, type, row) {
                    var variablePermiso = $("#modifDisPer").val();
                    if (variablePermiso == 1) {
                        return (
                            '<a onclick="editarDispo(' +
                            row.iddispositivos_tareo +
                            ')" style="cursor: pointer"><img src="/admin/images/edit.svg" height="15"></a>'
                        );
                    } else {
                        return "";
                    }
                },
            },

            { data: null },

            {
                data:
                    "dispoT_descripUbicacion"
            },

            { data: "dispoT_movil" },
            {
                data: "dispoT_estado",
                render: function (data, type, row) {
                    if (row.tipo_dispositivo_id == 2) {
                        if (row.dispoT_estado == 0) {
                            return (
                                '&nbsp; <button class="btn btn-sm  botonsms" onclick="enviarSMS(' +
                                row.iddispositivos_tareo +
                                ')" >Enviar <img src="landing/images/note.svg" height="20"  ></button>'
                            );
                        } else {
                            return (
                                '&nbsp; <button class="btn btn-sm botonsms" onclick="reenviarSMS(' +
                                row.iddispositivos_tareo +
                                ')">Reenviar <img src="landing/images/note.svg" height="20"  ></button>'
                            );
                        }
                    } else {
                        return "---";
                    }
                },
            },

            {
                data: "dispoT_estado",
                render: function (data, type, row) {
                    if (row.dispoT_estado == 0) {
                        return '<span class="badge badge-soft-primary">Creado</span>';
                    }
                    if (row.dispoT_estado == 1) {
                        return '<span class="badge badge-soft-info">Enviado</span>';
                    }
                    if (row.dispoT_estado == 2) {
                        return '<span class="badge badge-soft-success">Confirmado</span>';
                    }
                },
            },
            {
                data: "dispoT_tMarca",
                render: function (data, type, row) {
                    if (row.tipo_dispositivo_id == 2) {
                        return row.dispoT_tMarca + "&nbsp; minutos";
                    } else {
                        return "---";
                    }
                },
            },
            {
                data: "dispoT_tSincro",
                render: function (data, type, row) {
                    if (row.tipo_dispositivo_id == 2) {
                        return row.dispoT_tSincro + "&nbsp; minutos";
                    } else {
                        return "---";
                    }
                },
            },
            {
                data: "dispoT_tSincro",
                render: function (data, type, row) {
                    var variablePermiso2 = $("#modifDisPer").val();
                    if (variablePermiso2 == 1) {
                        if (row.dispoT_estadoActivo == 1) {
                            return (
                                '<div class="custom-control custom-switch">' +
                                '<input type="checkbox" class="custom-control-input" id="customSwitDetalles' +
                                row.iddispositivos_tareo +
                                '" checked>' +
                                '<label class="custom-control-label" for="customSwitDetalles' +
                                row.iddispositivos_tareo +
                                '" onclick="switchEleg(' +
                                row.iddispositivos_tareo +
                                ')" style="font-weight: bold"></label>' +
                                "</div>"
                            );
                        } else {
                            return (
                                '<div class="custom-control custom-switch">' +
                                '<input type="checkbox" class="custom-control-input" id="customSwitDetalles' +
                                row.iddispositivos_tareo +
                                '" >' +
                                '<label class="custom-control-label" for="customSwitDetalles' +
                                row.iddispositivos_tareo +
                                '" onclick="switchEleg(' +
                                row.iddispositivos_tareo +
                                ')" style="font-weight: bold"></label>' +
                                "</div>"
                            );
                        }
                    } else {
                        if (row.dispoT_estadoActivo == 1) {
                            return (
                                '<div class="custom-control custom-switch">' +
                                '<input type="checkbox" class="custom-control-input" id="customSwitDetalles' +
                                row.iddispositivos_tareo +
                                '" checked disabled>' +
                                '<label class="custom-control-label" for="customSwitDetalles' +
                                row.iddispositivos_tareo +
                                '"  style="font-weight: bold"></label>' +
                                "</div>"
                            );
                        } else {
                            return (
                                '<div class="custom-control custom-switch">' +
                                '<input type="checkbox" class="custom-control-input" id="customSwitDetalles' +
                                row.iddispositivos_tareo +
                                '" disabled>' +
                                '<label class="custom-control-label" for="customSwitDetalles' +
                                row.iddispositivos_tareo +
                                '"  style="font-weight: bold"></label>' +
                                "</div>"
                            );
                        }
                    }
                },
            },
        ],
    });

    table
        .on("order.dt search.dt", function () {
            table
                .column(1, { search: "applied", order: "applied" })
                .nodes()
                .each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
        })
        .draw();
    /*---------- FIN DE TABLA------------------- */
});

/* VALIDACIONES DE INT  */
function maxLengthCheck(object) {
    if (object.value.length > object.maxLength)
        object.value = object.value.slice(0, object.maxLength);
}

function isNumeric(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
    var regex = /[0-9]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}
/* ---------------------------------- */
function switchEleg(id) {
    if ($("#customSwitDetalles" + id + "").is(":checked")) {
        $("#customSwitDetalles" + id + "").prop("checked", false);
        desactivarDispo(id);
    } else {
        $("#customSwitDetalles" + id + "").prop("checked", true);
        activarDispo(id);
    }
}
$(function () {
    $(document).on("keyup", "#smarcacion", function (event) {
        let min = parseInt(this.min);
        let valor = parseInt(this.value);
        if (valor < min) {
            $("#errorMarca").show();
            this.value = min;
        } else {
            $("#errorMarca").hide();
        }
    });
});

$(function () {
    $(document).on("keyup", "#tiempoSin", function (event) {
        let minS = parseInt(this.min);
        let valorS = parseInt(this.value);
        if (valorS < minS) {
            $("#errorSincro").show();
            this.value = min;
        } else {
            $("#errorSincro").hide();
        }
    });
});

$(function () {
    $(document).on("keyup", "#tiempoData", function (event) {
        let minD = parseInt(this.min);
        let valorD = parseInt(this.value);
        if (valorD < minD) {
            $("#errorData").show();
            this.value = min;
        } else {
            $("#errorData").hide();
        }
    });
});

function NuevoDispo() {
    $("#errorMovil").hide();
    $("#errorMarca").hide();
    $("#errorMovil").hide();
    $("#frmHorNuevo")[0].reset();
    $("#selectLectura").val("").trigger("change");
    $("#selectControlador").val("").trigger("change");
    $("#nuevoDispositivo").modal("show");
}
function RegistraDispo() {
    var descripccionUb = $("#descripcionDis").val();
    var numeroM = "51" + $("#numeroMovil").val();
    var tSincron = $("#tiempoSin").val();
    var tMarcac = $("#smarcacion").val();
    var tData = $("#tiempoData").val();
    var lectura = $("#selectLectura").val();
    var idContro = $("#selectControlador").val();
    var smsCh;

    if ($("#smsCheck").is(":checked")) {
        smsCh = 1;
    } else {
        smsCh = 0;
    }
    $.ajax({
        type: "post",
        url: "/comprobarMovilTa",
        data: {
            numeroM,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            if (data == 1) {
                $("#errorMovil").show();
                return false;
            } else {
                $("#errorMovil").hide();
                $.ajax({
                    type: "post",
                    url: "/dispoTareStore",
                    data: {
                        descripccionUb,
                        numeroM,
                        tSincron,
                        tMarcac,
                        smsCh,
                        tData,
                        lectura,
                        idContro,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $("#tablaDips").DataTable().ajax.reload();
                        $.notifyClose();
                        $.notify(
                            {
                                message: "\nDispositivo registrado.",
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
                        $("#nuevoDispositivo").modal("hide");
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}

function enviarSMS(idDis) {
    bootbox.confirm({
        message: "¿Enviar código al dispositivo?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/enviarMensajeTareo",
                    data: {
                        idDis,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $.notifyClose();
                        $.notify(
                            {
                                message: "\nMensaje enviado.",
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
                        $("#tablaDips").DataTable().ajax.reload();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}
function reenviarSMS(idDis) {
    bootbox.confirm({
        message: "¿Reenviar código al dispositivo?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/reenviarmensajeDisTareo",
                    data: {
                        idDis,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $.notifyClose();
                        $.notify(
                            {
                                message: "\nMensaje enviado.",
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
                        $("#tablaDips").DataTable().ajax.reload();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}
function comprobarMovil() {
    var numeroM = "51" + $("#numeroMovil").val();

    $.ajax({
        type: "post",
        url: "/comprobarMovilTa",
        data: {
            numeroM,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            if (data == 1) {
                $("#errorMovil").show();
                $("#numeroMovil").val("");
            } else {
                $("#errorMovil").hide();
            }
        },
    });
}
function editarDispo(id) {

    /*------------ VACEAMOS SELECTS2 ---------------------*/
    $("#selectLectura_ed").val("").trigger("change");
    $("#selectControlador_ed").val("").trigger("change");
    /* ------------------------------------------------- */

    $.ajax({
        type: "post",
        url: "/datosDispoTarEditar",
        data: {
            id,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#idDisposi").val(data[0].iddispositivos_tareo);
            $("#descripcionDis_ed").val(data[0].dispoT_descripUbicacion);
            $("#numeroMovil_ed").val(data[0].dispoT_movil.substr(2));
            $("#tiempoSin_ed").val(data[0].dispoT_tSincro);
            $("#smarcacion_ed").val(data[0].dispoT_tMarca);
            $("#tiempoData_ed").val(data[0].dispoT_Data);
            var seleccionadosLe = [];
            if (data[0].dispoT_Manu == 1) {
                seleccionadosLe.push("1");
            }
            if (data[0].dispoT_Scan == 1) {
                seleccionadosLe.push("2");
            }
            if (data[0].dispoT_Cam == 1) {
                seleccionadosLe.push("3");
            }
            $.each(seleccionadosLe, function (index, value) {
                $("#selectLectura_ed > option[value='" + value + "']").prop(
                    "selected",
                    "selected"
                );
                $("#selectLectura_ed").trigger("change");
            });
            $.each(data, function (index, value) {
                $(
                    "#selectControlador_ed > option[value='" +
                        value.idcontroladores_tareo +
                        "']"
                ).prop("selected", "selected");
                $("#selectControlador_ed").trigger("change");
            });
            /* SI ES DISPOSITIVO ANDROID */
            if (data[0].tipo_dispositivo_id == 2) {
                $("#editarDispositivo").modal("show");
            }

        },
    });
}
function reditarDispo() {
    var descripccionUb_ed = $("#descripcionDis_ed").val();
    var numeroM_ed = "51" + $("#numeroMovil_ed").val();
    var tSincron_ed = $("#tiempoSin_ed").val();
    var tMarca_ed = $("#smarcacion_ed").val();
    var tData_ed = $("#tiempoData_ed").val();
    var lectura_ed = $("#selectLectura_ed").val();
    var idcont_id = $("#selectControlador_ed").val();
    var idDisposEd_ed = $("#idDisposi").val();

    $.ajax({
        type: "post",
        url: "/actualizarDisposTareo",
        data: {
            descripccionUb_ed,
            numeroM_ed,
            tSincron_ed,
            tMarca_ed,
            tData_ed,
            lectura_ed,
            idDisposEd_ed,
            idcont_id,
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
            $("#tablaDips").DataTable().ajax.reload();
            $("#editarDispositivo").modal("hide");
            $.notifyClose();
            $.notify(
                {
                    message: "\nDispositivo editado correctamente.",
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
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
}
function desactivarDispo(idDisDesac) {
    bootbox.confirm({
        message: "¿Desea desactivar dispositivo?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/desactivarDisposiTar",
                    data: {
                        idDisDesac,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $("#tablaDips").DataTable().ajax.reload();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}
function activarDispo(idDisAct) {
    bootbox.confirm({
        message: "¿Desea volver activar dispositivo?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/activarDisposiTar",
                    data: {
                        idDisAct,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $("#tablaDips").DataTable().ajax.reload();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}
