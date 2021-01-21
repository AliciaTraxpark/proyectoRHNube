$(document).ready(function () {
    /* ---------------------------TABLA CONTROLADORES --------------------------------*/
    var table = $("#tablaContr").DataTable({
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
            url: "/listaControladoresTa",
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
                data: "contrT_estado",
                render: function (data, type, row) {
                    var variablePermiso = $("#modifContPer").val();
                    if (variablePermiso == 1) {
                        return (
                            '<a onclick="editarContra(' +
                            row.idcontroladores_tareo +
                            ')" style="cursor: pointer"><img src="/admin/images/edit.svg" height="15"></a>'
                        );
                    } else {
                        return "";
                    }
                },
            },
            { data: null },
            { data: "contrT_codigo" },
            { data: "contrT_nombres" },
            {
                data: "contrT_ApPaterno",
                render: function (data, type, row) {
                    return row.contrT_ApPaterno + " " + row.contrT_ApMaterno;
                },
            },

            {
                data: "ids",
                render: function (data, type, row) {
                    if (row.ids != null) {
                        var valores = row.ids;
                        idsV = valores.split(",");
                        var variableResult = [];
                        $.each(idsV, function (index, value) {
                            variableResult1 =
                                '<img src="landing/images/telefono-inteligente.svg" height="14">' +
                                value;

                            variableResult.push(variableResult1);
                        });
                        return variableResult;
                    } else {
                        return "No tiene dispositivos";
                    }
                },
            },
            { data: "contrT_correo" },
            {
                data: "contrT_estado",
                render: function (data, type, row) {
                    if (row.contrT_estado == 0) {
                        return '<span class="badge badge-soft-danger">Inactivo</span>';
                    }
                    if (row.contrT_estado == 1) {
                        return '<span class="badge badge-soft-info">Activo</span>';
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

    /* -------------------------------------------------------------------------------- */
});

function NuevoContr() {

    /*  RESETEAMOS SELECR2 PARA DISPOSITIVO*/

    $("#frmConNuevo")[0].reset();
    $("#selectDispo").select2({
        placeholder: "Seleccione dispositivo",
    });
    $("#selectDispo").val("").trigger("change");
    $("#nuevoControlador").modal("show");
}

function RegistraContro() {
    var codigoCon = $("#codContro").val();
    var correoCon = $("#codCorreo").val();
    var nombresCon = $("#codNombres").val();
    var paternoCon = $("#codPaterno").val();
    var maternoCon = $("#codMaterno").val();
    var dispoCon = $("#selectDispo").val();
    $.ajax({
        type: "post",
        url: "/controladTaStore",
        data: {
            codigoCon,
            correoCon,
            nombresCon,
            paternoCon,
            maternoCon,
            dispoCon,
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
            $("#tablaContr").DataTable().ajax.reload();
            $("#nuevoControlador").modal("hide");
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
}
function editarContra(id) {
    $("#selectDispo_ed").val("").trigger("change");
    $.ajax({
        type: "post",
        url: "/datosControTaEditar",
        data: {
            id,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#idControladorEdit").val(data.idcontroladores_tareo);
            $("#codContro_ed").val(data.contrT_codigo);
            $("#codCorreo_ed").val(data.contrT_correo);
            $("#codNombres_ed").val(data.contrT_nombres);
            $("#codPaterno_ed").val(data.contrT_ApPaterno);
            $("#codMaterno_ed").val(data.contrT_ApMaterno);
            if (data.ids != null) {
                var valores_ed = data.ids;
                idsV_ed = valores_ed.split(",");

                $.each(idsV_ed, function (index, value) {
                    $("#selectDispo_ed > option[value='" + value + "']").prop(
                        "selected",
                        "selected"
                    );
                    $("#selectDispo_ed").trigger("change");
                });
            }

            $("#editarControlador").modal("show");
        },
    });
}
function EditarContro() {
    var idcontr_ed = $("#idControladorEdit").val();
    var codigoCon_ed = $("#codContro_ed").val();
    var correoCon_ed = $("#codCorreo_ed").val();
    var nombresCon_ed = $("#codNombres_ed").val();
    var paternoCon_ed = $("#codPaterno_ed").val();
    var maternoCon_ed = $("#codMaterno_ed").val();
    var dispoCon_ed = $("#selectDispo_ed").val();
    $.ajax({
        type: "post",
        url: "/controladTarUpdate",
        data: {
            codigoCon_ed,
            correoCon_ed,
            nombresCon_ed,
            paternoCon_ed,
            maternoCon_ed,
            dispoCon_ed,
            idcontr_ed,
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
            $("#tablaContr").DataTable().ajax.reload();
            $("#editarControlador").modal("hide");
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
}
