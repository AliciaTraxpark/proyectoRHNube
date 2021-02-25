$.fn.dataTable.ext.errMode = "throw";
$(document).ready(function () {
    var table = $("#tablaIncidencias").DataTable({
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
            url: "/listaControladores",
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
            },
            statusCode: {
                401: function () {
                    location.reload();
                },
                402: function () {
                    location.reload();
                },
                419: function () {
                    location.reload();
                },
                403: function () {
                    location.reload();
                },
                302: function () {
                    location.reload();
                },
            },
            error: function () {
                console.log("se recarga en 401");
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
                data: "cont_estado",
                render: function (data, type, row) {
                    var variablePermiso = $("#modifContPer").val();
                    if (variablePermiso == 1) {
                        return (
                            '<a onclick="editarContra(' +
                            row.idControladores +
                            ')" style="cursor: pointer"><img src="/admin/images/edit.svg" height="15"></a>'
                        );
                    } else {
                        return "";
                    }
                },
            },
            { data: null },
            { data: "cont_codigo" },
            { data: "cont_nombres" },
            {
                data: "cont_ApPaterno",
                render: function (data, type, row) {
                    return row.cont_ApPaterno + " " + row.cont_ApMaterno;
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
            { data: "cont_correo" },
            {
                data: "cont_estado",
                render: function (data, type, row) {
                    if (row.cont_estado == 0) {
                        return '<span class="badge badge-soft-danger">Inactivo</span>';
                    }
                    if (row.cont_estado == 1) {
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
});




