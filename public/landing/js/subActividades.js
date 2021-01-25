$.fn.select2.defaults.set("language", "es");
var table;

/*----------------- DATATABLE ---------------------------- */
function datatableSubactividades() {
    table = $("#subActividades").DataTable({
        scrollX: true,
        responsive: true,
        retrieve: true,
        searching: true,
        lengthChange: true,
        scrollCollapse: false,
        bAutoWidth: true,
        /* le quito el sortable a columnas que es flechitas para ordenar */
        columnDefs: [
            { targets: 3, sortable: false },

            { targets: 5, sortable: false },
        ],
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
    });
}
/*---------------- FIN DATATABLE --------------------------*/

/* -------CONSTRUIMOS DATOS DE TABLA --------------------- */
function tablaSubactividades() {
    if ($.fn.DataTable.isDataTable("subActividades")) {
        $("subActividades").DataTable().destroy();
    }
    $("#actividOrga").empty();
    $.ajax({
        async: false,
        url: "/listaSubactividades",
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
            var tr = "";
            for (let index = 0; index < data.length; index++) {
                tr += "<tr ><td>" + (index + 1) + "</td>";
                tr += "<td>" + data[index].subAct_nombre + "</td>";

                if (data[index].subAct_codigo != null) {
                    tr += "<td>" + data[index].subAct_codigo + "</td>";
                } else {
                    tr += "<td>No definido</td>";
                }

                if (data[index].modoTareo == 1) {
                    tr +=
                        '<td class="text-center"><div class="custom-control custom-switch mb-2">\
                        <input type="checkbox" class="custom-control-input"\
                            id="switchActvMT' +
                        data[index].idsubActividad +
                        '" checked disabled >\
                        <label class="custom-control-label" for="switchActvMT' +
                        data[index].idsubActividad +
                        '"\
                            style="font-weight: bold"></label>\
                        </div></td>';
                } else {
                    tr +=
                        '<td class="text-center"><div class="custom-control custom-switch mb-2">\
                        <input type="checkbox" class="custom-control-input"\
                            id="switchActvMT' +
                        data[index].idsubActividad +
                        '" >\
                        <label class="custom-control-label" for="switchActvMT' +
                        data[index].idsubActividad +
                        '"\
                            style="font-weight: bold"></label>\
                        </div></td>';
                }
                if (data[index].uso == 1) {
                    tr +=
                        '<td class="text-center" style="font-size:12px"><img src="/admin/images/checkH.svg" height="13" class="mr-2">SI</td>';
                } else {
                    tr +=
                        '<td class="text-center" style="font-size:12px"><img src="/admin/images/borrarH.svg" height="11" class="mr-2">NO</td>';
                }
                tr +=
                    '<td class="text-center"><a name="aedit" onclick="javascript:editarSubactividad(' +
                    data[index].idsubActividad +
                    ')" style="cursor: pointer">\
                                <img src="/admin/images/edit.svg" height="15">\
                            </a>&nbsp;&nbsp;&nbsp;<a name="deletePermiso" onclick="javascript:eliminarSubacti(' +
                    data[index].idsubActividad +
                    ')" style="cursor: pointer">\
                                <img src="/admin/images/delete.svg" height="15">\
                                </a></td>';

                tr += "</tr>";
            }
            $("#actividOrga").html(tr);
            datatableSubactividades();
        },
        error: function () {},
    });
    var valorswitch = $("#modifActI").val();
    var valorBaja = $("#bajaActI").val();
    if (valorswitch == 0) {
        $("input[type=checkbox]").prop("disabled", true);
        $('[name="aedit"]').hide();
    }
    if (valorBaja == 0) {
        $('[name="deletePermiso"]').hide();
    }
}
tablaSubactividades();
/* ------------------------------------------------------- */
/* -----LIMPIAR FORMULARIO DE REGISTRAR ACTIVIDAD ---------*/
function limpiarModo() {
    //* FORMULARIO REGISTRAR
    $("#nombreTarea").val("");
    $("#codigoTarea").val("");
    $("#customCR").prop("checked", false);
    $("#customCRT").prop("checked", false);
    $("#customMT").prop("checked", false);
    $("#customAP").prop("checked", false);
    $(".rowEmpleados").hide();
    $("#customAE").prop("checked", false);
    $("#customAA").prop("checked", false);
}
/* ------------------------------------------------------- */

//REMOVER CLASES
$("#nombreTarea").keyup(function () {
    $(this).removeClass("borderColor");
});
$("#codigoTarea").keyup(function () {
    $(this).removeClass("borderColor");
});
$("#e_codigoTarea").keyup(function () {
    $(this).removeClass("borderColor");
});
//* ****************************************
// * BUSCAR PERSONALIZADO
function filterGlobal() {
    $("#subActividades").DataTable().search($("#global_filter").val()).draw();
}
$("input.global_filter").on("keyup click change clear", function () {
    filterGlobal();
});
// **********************************

/* -----------REGISTRAR SUBACTIVIDAD------------ */

// CLASE A SELECT
$("#actividadesAsignar").select2({
    placeholder: "Seleccionar actividad",
    matcher: matchStart,
});

$("#actividadesAsignar_ed").select2({
    placeholder: "Seleccionar actividad",
    matcher: matchStart,
});

//LISTAR ACTIVIDADES EN EL SELECT
function listaActividades() {
    /* PARA REGISTRO */
    $("#actividadesAsignar").empty();
    var container = $("#actividadesAsignar");

    /* PARA EDITAR */
    $("#actividadesAsignar_ed").empty();
    var container_ed = $("#actividadesAsignar_ed");

    $.ajax({
        async: false,
        url: "/listActiviTareo",
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
            var option = `<option value="" disabled selected>Seleccionar</option>`;

            /* PARA REGISTRO DE SUBACTIVIDAD */
            data.forEach((element) => {
                option += `<option value="${element.idActividad}"> Actividad : ${element.nombre} </option>`;
            });
            container.append(option);
            /* ----------------------------------- */

            /* PARA EDICION DE SUBACTIVIDAD */
            container_ed.append(option);
            /* --------------------------------- */
        },
        error: function () {},
    });
}

//ABRIR REGISTRO SUBACTIVIDAD
function abrirRegistroSubact() {
    //reseteamos formulario
    $("#FormRegistrarSubactividad")[0].reset();

    //removemos clases
    $("#nombreSubact").removeClass("borderColor");
    $("#codigoSubact").removeClass("borderColor");
    //listamos actividades en select2
    listaActividades();

    //abrimos modal de registro
    $("#regSubactividad").modal("show");
}

//funcion que registra actividad
function registrarSubactividad() {
    //Obtemos valores
    let nombreSub = $("#nombreSubact").val();
    let codigoSub = $("#codigoSubact").val();
    let idActividad = $("#actividadesAsignar").val();

    let tareosub;
    if ($("#customMTSubact").is(":checked") == true) {
        tareosub = 1;
    } else {
        tareosub = 0;
    }

    $.ajax({
        url: "/registrarSubact",
        method: "POST",
        data: { nombreSub, codigoSub, idActividad, tareosub },
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
            if (data.estado === 1) {
                if (data.subactividad.estado == 0) {
                    alertify
                        .confirm(
                            "Ya existe una subactividad inactiva con este nombre. ¿Desea recuperarla si o no?",
                            function (e) {
                                if (e) {
                                    recuperarSubactividad(
                                        data.subactividad.idsubActividad
                                    );
                                }
                            }
                        )
                        .setting({
                            title: "Modificar subactividad",
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
                            oncancel: function (closeEvent) {},
                        });
                } else {
                    $("#nombreSubact").addClass("borderColor");
                    $.notifyClose();
                    $.notify(
                        {
                            message:
                                "\nYa existe una subactividad con este nombre.",
                            icon: "admin/images/warning.svg",
                        },
                        {
                            element: $("#regSubactividad"),
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
                    if (data.subactividad.estado == 0) {
                        alertify
                            .confirm(
                                "Ya existe una subactividad inactiva con este código. ¿Desea recuperarla si o no?",
                                function (e) {
                                    if (e) {
                                        recuperarSubactividad(
                                            data.subactividad.idsubActividad
                                        );
                                    }
                                }
                            )
                            .setting({
                                title: "Modificar subactividad",
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
                                oncancel: function (closeEvent) {},
                            });
                    } else {
                        $("#codigoSubact").addClass("borderColor");
                        $.notifyClose();
                        $.notify(
                            {
                                message:
                                    "\nYa existe una subactividad con este código.",
                                icon: "admin/images/warning.svg",
                            },
                            {
                                element: $("#regSubactividad"),
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
                    tablaSubactividades();
                    $("#regSubactividad").modal("hide");
                    $.notifyClose();
                    $.notify(
                        {
                            message: "\nSubactividad registrada.",
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
        error: function () {},
    });
}
/* -----------FIN DE REGISTRAR SUB------------- */

/*--------------------------- FUNCION ELIMINAR SUBACTIVIDAD -----------------------------------*/
function eliminarSubacti(id) {
    alertify
        .confirm("¿Desea eliminar subactividad?", function (e) {
            if (e) {
                $.ajax({
                    type: "GET",
                    url: "/eliminSubactiv",
                    data: {
                        id: id,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
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
                        if (data == 1) {
                            $.notifyClose();
                            $.notify(
                                {
                                    message:
                                        "\nSubactividad en uso, no se puede eliminar.",
                                    icon: "/landing/images/alert1.svg",
                                },
                                {
                                    icon_type: "image",
                                    allow_dismiss: true,
                                    newest_on_top: true,
                                    delay: 6000,
                                    template:
                                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                        '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                                        '<span data-notify="title">{1}</span> ' +
                                        '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                                        "</div>",
                                    spacing: 35,
                                }
                            );
                        } else {
                            tablaSubactividades();
                            $.notifyClose();
                            $.notify(
                                {
                                    message: "\nSubactividad eliminada",
                                    icon: "landing/images/bell.svg",
                                },
                                {
                                    icon_type: "image",
                                    allow_dismiss: true,
                                    newest_on_top: true,
                                    delay: 6000,
                                    template:
                                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                        '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                                        '<span data-notify="title">{1}</span> ' +
                                        '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                                        "</div>",
                                    spacing: 35,
                                }
                            );
                        }
                    },
                    error: function () {},
                });
            }
        })
        .setting({
            title: "Eliminar subactividad",
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
                tablaSubactividades();
            },
        });
}
/* ------------------------------------------------------------------------------------------ */

/* FUNCION EDITAR/VER DATOS SUACTIVIDAD */
function editarSubactividad(id) {
    $.ajax({
        async: false,
        type: "POST",
        url: "/editarSubactividad",
        data: {
            idSub: id,
        },
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

            //removemos clases
            $("#nombreSubact_ed").removeClass("borderColor");
            $("#codigoSubact_ed").removeClass("borderColor");
            //
            listaActividades();

            $("#idSubAct").val(data.idsubActividad);
            $("#nombreSubact_ed").val(data.subAct_nombre);
            $("#codigoSubact_ed").val(data.subAct_codigo);
            if (data.subAct_codigo === null) {
                $("#codigoSubact_ed").attr("disabled", false);
            } else {
                $("#codigoSubact_ed").attr("disabled", true);
            }

            $("#actividadesAsignar_ed").val(data.Activi_id);

            if (data.modoTareo === 1) {
                $("#customMTSubact_ed").prop("checked", true);
            } else {
                $("#customMTSubact_ed").prop("checked", false);
            }
        },
        error: function () {},
    });
    $.notifyClose();
    $("#editSubactividad").modal();
}
/* ------------------------------------------ */

/* FUNCION PARA ACTUALIZAR DATOS DE SUBACTIVIDAD */

function actualizarSubactividad() {
    let idSuactiv = $("#idSubAct").val();
    let codigo = $("#codigoSubact_ed").val();
    let idActividad = $("#actividadesAsignar_ed").val();

    let modoTareo;

    //* MODO TAREO
    if ($("#customMTSubact_ed").is(":checked") == true) {
        modoTareo = 1;
    } else {
        modoTareo = 0;
    }

    $.ajax({
        url: "/actualizarSubactividad",
        method: "POST",
        data: { idSuactiv, codigo, idActividad, modoTareo },
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
            if (data != 0) {
                tablaSubactividades();
                $("#editSubactividad").modal("hide");
                $.notifyClose();
                $.notify(
                    {
                        message: "\nSubactividad actualizada.",
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
            } else {
                $("#codigoSubact_ed").addClass("borderColor");
                $.notifyClose();
                $.notify(
                    {
                        message: "\nYa existe una subactividad con este código.",
                        icon: "admin/images/warning.svg",
                    },
                    {
                        element: $("#editSubactividad"),
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
        },
        error: function () {},
    });
}
/* --------------------------------------------- */

/* FUNCION PARA RECUERAR SUBACTIVIDAD */
function recuperarSubactividad(id) {
    $.ajax({
        type: "GET",
        url: "/recuperarSubact",
        data: {
            id: id,
        },
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
            tablaSubactividades();
            $("#regSubactividad").modal("toggle");
            editarSubactividad(data.idsubActividad);
        },
        error: function () {},
    });
}
/*  */
