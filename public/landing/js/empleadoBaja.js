//* *********************SMARTWIZARD**********************
$("#smartwizardVer").smartWizard({
    selected: 0,
    keyNavigation: false,
    showStepURLhash: false,
    lang: {
        // Language variables
        next: "Siguiente",
        previous: "Anterior",
    },
    justified: true,
    autoAdjustHeight: true,
    anchorSettings: {
        anchorClickable: true, // Enable/Disable anchor navigation
        enableAllAnchors: true,
        markDoneStep: true,
        enableAllAnchorOnDoneStep: true,
    },
    toolbarSettings: {
        toolbarPosition: "bottom", // none, top, bottom, both
        toolbarButtonPosition: "right", // left, right, center
        toolbarExtraButtons: [
            $(`<button></button>`)
                .text("Finalizar")
                .addClass("btn btn-secondary sw-btn-finish")
                .attr("id", "FinalizarEmpleadoVer")
                .on("click", function () {
                    cerrarVer();
                    $("#verEmpleadoDetalles").modal("toggle");
                }),
        ],
    },
});
$("#smartwizardVer").on("showStep", function (
    e,
    anchorObject,
    stepNumber,
    stepDirection
) {
    if (stepNumber == 0 || stepNumber == 1 || stepNumber == 2) {
        $("button.sw-btn-prev").show();
        $("button.sw-btn-next").show();
        $("#FinalizarEmpleadoVer").hide();
        $("#smartwizardVer :input").attr("disabled", true);
        $("button.sw-btn-prev").attr("disabled", false);
        $("button.sw-btn-next").attr("disabled", false);
    }

    if (stepNumber == 3 || stepNumber == 4) {
        $("button.sw-btn-prev").show();
        $("button.sw-btn-next").show();
        $("#FinalizarEmpleadoVer").hide();
        $("#disab").css("pointer-events", "none");
        $("button.sw-btn-prev").attr("disabled", false);
        $("button.sw-btn-next").attr("disabled", false);
        $("#smartwizardVer :input").attr("disabled", false);
    }
    if (stepNumber == 5) {
        $("button.sw-btn-prev").show();
        $("button.sw-btn-next").show();
        $("#FinalizarEmpleadoVer").hide();
        $("#smartwizardVer :input").attr("disabled", false);
        actividadEmpVer();
    }
    if (stepNumber == 6) {
        $("button.sw-btn-prev").show();
        $("button.sw-btn-next").show();
        $("#FinalizarEmpleadoVer").hide();
        dispositivoWindowsVer();
        dispositivosAndroidVer();
        $("#smartwizardVer :input").attr("disabled", false);
    }
    if (stepNumber == 7) {
        $("button.sw-btn-prev").hide();
        $("button.sw-btn-next").hide();
        $("#FinalizarEmpleadoVer").show();
    }
});
// * ********************CRUD DE CONTRATO******************
function limpiarBaja() {
    $("#textContratoB").val("");
    $("#editarContratoB").hide();
    $("#textCondicionB").val("");
    $("#editarCondicionB").hide();
}
$("#buscarContratoB").on("click", function () {
    $("#editarContratoB").empty();
    var container = $("#editarContratoB");
    var select = "";
    $.ajax({
        type: "GET",
        url: "/contrato",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            select += `<select class="form-control" name="contrato" id="editarCOB">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].contrato_id}">${data[i].contrato_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $("#editarCOB").on("change", function () {
                var id = $("#editarCOB").val();
                $.ajax({
                    type: "GET",
                    url: "/buscarContrato",
                    data: {
                        id: id,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $("#textContratoB").val(data);
                    },
                    error: function () {
                        $("#textContratoB").val("");
                    },
                });
            });
        },
        error: function () { },
    });
    $("#editarContratoB").show();
});
function agregarContratoB() {
    objContrato = datosContratoB("POST");
    enviarContratoB("", objContrato);
}

function datosContratoB(method) {
    nuevoContrato = {
        contrato_descripcion: $("#textContratoB").val(),
        _method: method,
    };
    return nuevoContrato;
}

function enviarContratoB(accion, objContrato) {
    var id = $("#editarCOB").val();
    if (id == "" || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/contrato" + accion,
            data: objContrato,
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
                $("#contratoB").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.contrato_id,
                        text: data.contrato_descripcion,
                        selected: true,
                    })
                );
                $("#contratoB").val(data.contrato_id).trigger("change"); //lo selecciona
                $("#textContratoB").val("");
                $("#editarContratoB").hide();
                limpiarBaja();
                $("#contratomodalB").modal("toggle");
                $("#modalAlta").modal("show");
                $.notify(
                    {
                        message: "\nContrato Registrado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#modalAlta"),
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
    } else {
        $.ajax({
            type: "POST",
            url: "/editarContrato" + accion,
            data: {
                id: id,
                objContrato,
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
                $("#contratoB").empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/contrato",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].contrato_id}">${data[i].contrato_descripcion}</option>`;
                        }
                        $("#contratoB").append(select);
                    },
                    error: function () { },
                });
                $("#contratoB").val(data.contrato_id).trigger("change"); //lo selecciona
                $("#textContratoB").val("");
                $("#editarContratoB").hide();
                limpiarBaja();
                $("#contratomodalB").modal("toggle");
                $("#modalAlta").modal("show");
                $.notify(
                    {
                        message: "\nContrato Modificado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#modalAlta"),
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
        });
    }
}
// * ********************CONDICION DE PAGO******************
$("#buscarCondicionB").on("click", function () {
    $("#editarCondicionB").empty();
    var container = $("#editarCondicionB");
    var select = "";
    $.ajax({
        type: "GET",
        url: "/condicion",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            select += `<select class="form-control" id="editarCPB">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].id}">${data[i].condicion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $("#editarCPB").on("change", function () {
                var id = $("#editarCPB").val();
                $.ajax({
                    type: "GET",
                    url: "/buscarCondicion",
                    data: {
                        id: id,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (data) {
                        $("#textCondicionB").val(data);
                    },
                    error: function () {
                        $("#textCondicionB").val("");
                    },
                });
            });
        },
        error: function () { },
    });
    $("#editarCondicionB").show();
});
function agregarCondicionB() {
    objCondicion = datosCondicionB("POST");
    enviarCondicionB("", objCondicion);
}

function datosCondicionB(method) {
    nuevoCondicion = {
        condicion: $("#textCondicionB").val(),
        _method: method,
    };
    return nuevoCondicion;
}

function enviarCondicionB(accion, objCondicion) {
    var id = $("#editarCPB").val();
    if (id == "" || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/condicion" + accion,
            data: objCondicion,
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
                $("#condicionB").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.id,
                        text: data.condicion,
                        selected: true,
                    })
                );
                $("#condicionB").val(data.id).trigger("change"); //lo selecciona
                $("#textCondicionB").val("");
                $("#editarCondicionB").hide();
                limpiarBaja();
                $("#condicionmodalB").modal("toggle");
                $("#modalAlta").modal("show");
                $.notify(
                    {
                        message: "\nCondición de Pago Registrado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#modalAlta"),
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
    } else {
        $.ajax({
            type: "POST",
            url: "/editarCondicion" + accion,
            data: {
                id: id,
                objCondicion,
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
                $("#condicionB").empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/condicion",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].id}">${data[i].condicion}</option>`;
                        }
                        $("#condicionB").append(select);
                    },
                    error: function () { },
                });
                $("#condicionB").val(data.id).trigger("change");
                $("#textCondicionB").val("");
                $("#editarCondicionB").hide();
                limpiarBaja();
                $("#condicionmodalB").modal("toggle");
                $("#modalAlta").modal("show");
                $.notify(
                    {
                        message: "\nCondicion de Pago Modificado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#modalAlta"),
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
        });
    }
}

//* *****************FILTAR DATOS DE TABLA
function filterGlobal() {
    $('#tablaEmpleado').DataTable().search(
        $('#global_filter').val(),

    ).draw();
}
//* ***************************************
function maxLengthCheck(object) {
    if (object.value.length > object.maxLength)
        object.value = object.value.slice(0, object.maxLength)
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
//* ************************************************
$(function () {

    RefreshTablaEmpleado();
});
//* REFRESH TABLA
function RefreshTablaEmpleado() {
    if ($.fn.DataTable.isDataTable("#tablaEmpleado")) {
        $("#tablaEmpleado").DataTable().destroy();
    }
    $("#tbodyr").empty();
    $.ajax({
        async: false,
        type: "get",
        url: "tablaempleado/refreshBaja",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            var tbody = "";
            for (var i = 0; i < data.length; i++) {
                tbody +=
                    "<tr id=" +
                    data[i].emple_id +
                    " value=" +
                    data[i].emple_id +
                    ">";
                tbody +=
                    '<td class="text-center">\
                    <input type="checkbox" name="selec" id="tdC" class="form-check-input sub_chk" style="margin-left:1em !important" data-id=' +
                    data[i].emple_id +
                    " " +
                    this +
                    "" +
                    this +
                    "" +
                    this +
                    ">\
                        </td>";
                tbody +=
                    '<td class="text-center">\
                                \
                                <a data-toggle="tooltip" data-placement="right" data-original-title="Dar de alta" onclick="javascript:darAltaEmpleado(' +
                    data[i].emple_id +
                    ')" style="cursor: pointer">\
                                    <img src="/landing/images/arriba.svg" height="17">\
                                </a>\
                               \
                                <a class="verEmpleado" onclick="javascript:verDEmpleado(' +
                    data[i].emple_id +
                    ')" data-toggle="tooltip"\
                                    data-placement="right" title="Ver Detalles" data-original-title="Ver Detalles" style="cursor:pointer">\
                                    <img src="/landing/images/see.svg" height="18">\
                                </a>\
                                <input type="hidden" id="codE" value=' +
                    data[i].emple_id +
                    "> </td>";

                tbody +=
                    '<td class="text-center"> <div class="text-wrap width-400">' +
                    data[i].emple_nDoc +
                    '</div></td>\
                            <td> <div class="text-wrap width-400">' +
                    data[i].perso_nombre +
                    '</div></td>\
                            <td> <div class="text-wrap width-400">' +
                    data[i].perso_apPaterno +
                    " " +
                    data[i].perso_apMaterno +
                    "</div></td>";

                if (data[i].cargo_descripcion == null) {
                    tbody += '<td><div class="text-wrap width-400"></div></td>';
                } else {
                    tbody +=
                        '<td><div class="text-wrap width-400">' +
                        data[i].cargo_descripcion +
                        "</div></td>";
                }
                if (data[i].area_descripcion == null) {
                    tbody +=
                        '<td><div class="text-wrap width-400"></div></td></tr>';
                } else {
                    tbody +=
                        '<td><div class="text-wrap width-400">' +
                        data[i].area_descripcion +
                        "</div></td></tr>";
                }
            }
            $("#tbodyr").html(tbody);
            $('[data-toggle="tooltip"]').tooltip();
            $("#tablaEmpleado").DataTable({
                scrollX: true,
                responsive: true,
                retrieve: true,
                searching: true,
                lengthChange: false,
                scrollCollapse: false,
                pageLength: 30,
                bAutoWidth: true,
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
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 3 }
                ],
                initComplete: function () {
                    this.api()
                        .columns()
                        .every(function () {
                            var that = this;
                            var i;
                            var val1;
                            $("#select").on("keyup change", function () {
                                i = $.fn.dataTable.util.escapeRegex(this.value);

                                var val = $("#global_filter").val();
                                if (that.column(i).search() !== this.value) {
                                    that.column(this.value).search(val).draw();
                                }
                                val1 = $.fn.dataTable.util.escapeRegex(
                                    this.value
                                );
                                $("#global_filter").on(
                                    "keyup change clear",
                                    function () {
                                        var val = $(this).val();
                                        if (that.column(i).search() !== val1) {
                                            that.column(val1)
                                                .search(val)
                                                .draw();
                                        }
                                    }
                                );
                            });
                        });
                },
            });
            var seleccionarTodos = $('#selectT');
            var table = $('#tablaEmpleado');
            var CheckBoxs = table.find('tbody input:checkbox[name=selec]');
            var CheckBoxMarcados = 0;

            seleccionarTodos.on('click', function () {
                if (seleccionarTodos.is(":checked")) {
                    CheckBoxs.prop('checked', true);
                } else {
                    CheckBoxs.prop('checked', false);
                };

            });
            CheckBoxs.on('change', function (e) {
                CheckBoxMarcados = table.find('tbody input:checkbox[name=selec]:checked').length;
                seleccionarTodos.prop('checked', (CheckBoxMarcados === CheckBoxs.length));
            });
        },
    });
    $('#tablaEmpleado').on('shown.bs.collapse', function () {
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
    $('#tablaEmpleado tbody #tdC').css('display', 'block');

    $('input.global_filter').on('keyup click', function () {
        filterGlobal();
    });

    $('input.column_filter').on('keyup click', function () {
        filterColumn($(this).parents('div').attr('data-column'));
    });

    // SELECT DEFECTO PARA BUSQUEDA
    $('#select').val(4).trigger('change');
}
$('#selectarea').on("change", function (e) {
    console.log($('#selectarea').val());
    RefreshTablaEmpleadoBajaArea();
});
//* REFRESH TABLA CON BUSQUEDA AREA
function RefreshTablaEmpleadoBajaArea() {
    if ($.fn.DataTable.isDataTable("#tablaEmpleado")) {
        $("#tablaEmpleado").DataTable().destroy();
    }
    $("#tbodyr").empty();
    var areaselect = $('#selectarea').val();
    $.ajax({
        async: false,
        type: "post",
        url: "tablaempleado/refreshAreaBaja",
        data: { idarea: areaselect },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            var tbody = "";
            for (var i = 0; i < data.length; i++) {
                tbody +=
                    "<tr id=" +
                    data[i].emple_id +
                    " value=" +
                    data[i].emple_id +
                    ">";
                tbody +=
                    '<td class="text-center">\
                        <input type="checkbox" name="selec" id="tdC" style="margin-left:1em !important"\
                            class="form-check-input sub_chk" data-id=' +
                    data[i].emple_id +
                    " " +
                    this +
                    "" +
                    this +
                    "" +
                    this +
                    ">\
                        </td>";
                tbody +=
                    '<td class="text-center">\
                                \
                                <a data-toggle="tooltip" data-placement="right" data-original-title="Dar de alta" onclick="javascript:darAltaEmpleado(' +
                    data[i].emple_id +
                    ')" style="cursor: pointer">\
                                    <img src="/landing/images/arriba.svg" height="17">\
                                </a>\
                                \
                                <a class="verEmpleado" onclick="javascript:verDEmpleado(' +
                    data[i].emple_id +
                    ')" data-toggle="tooltip"\
                                    data-placement="right" title="Ver Detalles" data-original-title="Ver Detalles" style="cursor:pointer">\
                                    <img src="/landing/images/see.svg" height="18">\
                                </a>\
                                <input type="hidden" id="codE" value=' +
                    data[i].emple_id +
                    "> </td>";

                tbody += "</td>";
                tbody +=
                    '<td class="text-center"> <div class="text-wrap width-400">' +
                    data[i].emple_nDoc +
                    '</div></td>\
                            <td> <div class="text-wrap width-400">' +
                    data[i].perso_nombre +
                    '</div></td>\
                            <td> <div class="text-wrap width-400">' +
                    data[i].perso_apPaterno +
                    " " +
                    data[i].perso_apMaterno +
                    "</div></td>";


                if (data[i].cargo_descripcion == null) {
                    tbody += '<td><div class="text-wrap width-400"></div></td>';
                } else {
                    tbody +=
                        '<td><div class="text-wrap width-400">' +
                        data[i].cargo_descripcion +
                        "</div></td>";
                }
                if (data[i].area_descripcion == null) {
                    tbody +=
                        '<td><div class="text-wrap width-400"></div></td></tr>';
                } else {
                    tbody +=
                        '<td><div class="text-wrap width-400">' +
                        data[i].area_descripcion +
                        "</div></td></tr>";
                }
            }
            $("#tbodyr").html(tbody);
            $('[data-toggle="tooltip"]').tooltip();
            $("#tablaEmpleado").DataTable({
                scrollX: true,
                responsive: true,
                retrieve: true,
                searching: true,
                lengthChange: false,
                scrollCollapse: false,
                pageLength: 30,
                bAutoWidth: true,
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
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 3 }
                ],
                initComplete: function () {
                    this.api()
                        .columns()
                        .every(function () {
                            var that = this;
                            var i;
                            var val1;
                            $("#select").on("keyup change", function () {
                                i = $.fn.dataTable.util.escapeRegex(this.value);

                                var val = $("#global_filter").val();
                                if (that.column(i).search() !== this.value) {
                                    that.column(this.value).search(val).draw();
                                }
                                val1 = $.fn.dataTable.util.escapeRegex(
                                    this.value
                                );
                                $("#global_filter").on(
                                    "keyup change clear",
                                    function () {
                                        var val = $(this).val();
                                        if (that.column(i).search() !== val1) {
                                            that.column(val1)
                                                .search(val)
                                                .draw();
                                        }
                                    }
                                );
                            });
                        });
                },
            });
            var seleccionarTodos = $('#selectT');
            var table = $('#tablaEmpleado');
            var CheckBoxs = table.find('tbody input:checkbox[name=selec]');
            var CheckBoxMarcados = 0;

            seleccionarTodos.on('click', function () {
                if (seleccionarTodos.is(":checked")) {
                    CheckBoxs.prop('checked', true);
                } else {
                    CheckBoxs.prop('checked', false);
                };

            });


            CheckBoxs.on('change', function (e) {
                CheckBoxMarcados = table.find('tbody input:checkbox[name=selec]:checked').length;
                seleccionarTodos.prop('checked', (CheckBoxMarcados === CheckBoxs.length));
            });
        },
    });
}

//* FUNCION DE CALENDARIO 3
function calendario3() {
    var calendarEl = document.getElementById("calendar3");
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: "es",
        defaultDate: fecha,
        height: 400,
        fixedWeekCount: false,
        plugins: ["dayGrid", "interaction", "timeGrid"],

        selectable: true,
        selectMirror: true,
        eventClick: function (info) { },
        editable: false,
        eventLimit: true,
        header: {
            left: "prev,next today",
            center: "title",
            right: "",
        },
        eventRender: function (info) {
            $('.tooltip').remove();
            if (info.event.extendedProps.horaI === null) {
                $(info.el).tooltip({ title: info.event.title });
            } else {
                if (info.event.borderColor == '#5369f8') {
                    $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' });
                }
                else {
                    $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF });
                }
            }
        },
        events: function (info, successCallback, failureCallback) {
            var idempleado = $("#idempleado").val();
            var datoscal;
            $.ajax({
                type: "POST",
                url: "/empleado/vercalendario",
                data: {
                    idempleado,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    },
                },
                success: function (data) {
                    successCallback(data);
                },
                error: function () { },
            });
        },

        /*  events: "calendario/show", */
    };
    calendar3 = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendar3.setOption("locale", "Es");

    calendar3.render();
}

//* FUNCION DE CALENDARIO 4
function calendario4() {
    var calendarEl = document.getElementById("calendar4");
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: "es",
        defaultDate: fecha,
        height: 400,
        fixedWeekCount: false,
        plugins: ["dayGrid", "interaction", "timeGrid"],

        selectable: true,
        selectMirror: true,
        eventClick: function (info) { },
        editable: false,
        eventLimit: true,
        header: {
            left: "prev,next today",
            center: "title",
            right: "",
        },
        eventRender: function (info) {
            $('.tooltip').remove();
            if (info.event.extendedProps.horaI === null) {
                $(info.el).tooltip({ title: info.event.title });
            } else {
                if (info.event.borderColor == '#5369f8') {
                    $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' });

                }
                else {
                    $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF });
                }
            }
        },
        events: function (info, successCallback, failureCallback) {
            var idempleado = $("#idempleado").val();
            var datoscal;
            $.ajax({
                type: "POST",
                url: "/empleado/vercalendario",
                data: {
                    idempleado,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    },
                },
                success: function (data) {
                    successCallback(data);
                },
                error: function () { },
            });
        },
    };
    calendar4 = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendar4.setOption("locale", "Es");

    calendar4.render();
}

//* VER DATOS DE EMPLEADO
function verDEmpleado(idempleadoVer) {
    $('#verEmpleadoDetalles').modal();
    $("#detallehorario_ed").empty();
    $('#smartwizardVer').smartWizard("reset");
    $('#MostrarCa_e').hide();
    $('#calendarInv_ed').hide();
    $('#divescond1').hide();
    $('#divescond1_ver').hide();
    $('#divescond2').hide();
    $('#calendar_ed').hide();
    $('#h5Ocultar').show();
    $('#v_fotoV').attr("src", "landing/images/png.svg");
    var value = idempleadoVer
    $('#selectCalendario').val("Asignar calendario");
    $('#selectHorario').val("Seleccionar horario");
    $('#selectCalendario_ed').val("Asignar calendario");
    $('#idempleado').val(value);
    $('#formNuevoEl').show();
    $.ajax({
        async: false,
        type: "get",
        url: "empleado/show",
        data: {
            value: value
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            401: function () {
                location.reload();
            }
        },
        success: function (data) {
            calendario3();
            calendario4();
            $.ajax({
                type: "POST",
                url: "/empleado/calendarioEditar",
                data: {
                    idempleado: value
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    }
                },
                success: function (data) {
                    if (data == 1) {
                        $('#MostrarCa_e').show();
                        $('#calendarInv_ed').show();
                    }
                    else {
                        $('#calendar_ed').show();
                        $('#mensajeOc_ed').hide();
                        $('#calendar2_ed').show();
                        $('#divescond1').show();
                        $('#divescond1_ver').show();
                        $('#divescond2').show();
                        $('#detallehorario_ed2').empty();
                    }
                },
                error: function () { }
            });
            $('#selectCalendario_edit3_ver').val(data[0].idcalendar);
            $('#idselect3').val(data[0].idcalendar);
            //VER
            $('#v_tipoDocV').val(data[0].tipoDoc_descripcion);
            $('#v_apPaternoV').val(data[0].perso_apPaterno);
            $('#v_direccionV').val(data[0].perso_direccion);
            $('#v_idV').val(data[0].emple_id);
            //* FECHA NACIMIENTO
            var VFechaDaVer = moment(data[0].perso_fechaNacimiento).format('YYYY-MM-DD');
            var VFechaDiaVer = new Date(moment(VFechaDaVer));
            $('#v_dia_fechaV').val(VFechaDiaVer.getDate());
            $('#v_mes_fechaV').val(moment(VFechaDaVer).month() + 1);
            $('#v_ano_fechaV').val(moment(VFechaDaVer).year());
            //* *************************************************************************
            $('#v_apMaternoV').val(data[0].perso_apMaterno);
            $('#v_numDocumentoV').val(data[0].emple_nDoc);
            $('#v_emailV').val(data[0].emple_Correo);
            $('#v_celularV').val(data[0].emple_celular);
            $('#v_nombresV').val(data[0].perso_nombre);
            $('#v_telefonoV').val(data[0].emple_telefono);
            $('#v_depV').val(data[0].deparNo);
            $('#v_departamentoV').val(data[0].depaN);
            $("[name=v_tipoV]").val([data[0].perso_sexo]);
            $('#v_provV').val(data[0].provi);
            $('#v_provinciaV').val(data[0].proviN);
            $('#v_distV').val(data[0].distNo)
            $('#v_distritoV').val(data[0].distN)
            $('#v_cargoV').val(data[0].cargo_descripcion);
            $('#v_areaV').val(data[0].area_descripcion);
            $('#v_centrocV').val(data[0].centroC_descripcion);
            $('#v_nivelV').val(data[0].nivel_descripcion);
            $('#v_localV').val(data[0].local_descripcion);
            $('#v_codigoEmpleadoV').val(data[0].emple_codigo);
            if (data[0].foto != '') {
                $('#v_fotoV').attr("src", "fotosEmpleado/" + data[0].foto);
                $('#h5Ocultar').hide();
            }
            $('#detalleContratoVer').hide();
            if (data[0].contrato.length >= 1) {
                $('#detalleContratoVer').show();
                $('#v_contratoV').val(data[0].contrato[0].contrato_descripcion);
                $('#v_idContratoV').val(data[0].contrato[0].idC);
                $('#v_montoV').val(data[0].contrato[0].monto);
                $('#v_condicionV').val(data[0].contrato[0].idCond);
                var VFechaDaIE = moment(data[0].contrato[0].fechaInicio).format('YYYY-MM-DD');
                var VFechaDiaIE = new Date(moment(VFechaDaIE));
                $('#m_dia_fechaIEV').val(VFechaDiaIE.getDate());
                $('#m_mes_fechaIEV').val(moment(VFechaDaIE).month() + 1);
                $('#m_ano_fechaIEV').val(moment(VFechaDaIE).year());
                if (data[0].contrato[0].fechaFinal == null || data[0].contrato[0].fechaFinal == "0000-00-00") {
                    $("#checkboxFechaIEV").prop('checked', true);
                    $('#ocultarFechaEV').hide();
                }
                var VFechaDaFE = moment(data[0].contrato[0].fechaFinal).format('YYYY-MM-DD');
                var VFechaDiaFE = new Date(moment(VFechaDaFE));
                $('#m_dia_fechaFEV').val(VFechaDiaFE.getDate());
                $('#m_mes_fechaFEV').val(moment(VFechaDaFE).month() + 1);
                $('#m_ano_fechaFEV').val(moment(VFechaDaFE).year());
            }
            $('#ver_tbodyDispositivo').css('pointer-events', 'none');
            $("#formContratoVer :input").prop('disabled', true);
        },
        error: function () { }
    });
}
'use strict';
; (function (document, window, index) {
    var inputs = document.querySelectorAll('.inputfile');
    Array.prototype.forEach.call(inputs, function (input) {
        var label = input.nextElementSibling,
            labelVal = label.innerHTML;

        input.addEventListener('change', function (e) {
            var fileName = '';
            if (this.files && this.files.length > 1)
                fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
            else
                fileName = e.target.value.split('\\').pop();

            if (fileName)
                label.querySelector('span').innerHTML = fileName;
            else
                label.innerHTML = labelVal;
        });
    });
}(document, window, 0));