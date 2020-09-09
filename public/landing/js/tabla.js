$(document).ready(function () {
    //$('#form-ver').hide();
    leertabla();
});

function leertabla() {
    $("#tabladiv").hide();
    $("#espera").show();
    $.get("tablaempleado/ver", {}, function (data, status) {
        $("#tabladiv").html(data);
        $("#espera").hide();
        $("#tabladiv").show();
    });
}

function RefreshTablaEmpleado() {
    if ($.fn.DataTable.isDataTable("#tablaEmpleado")) {
        $("#tablaEmpleado").DataTable().destroy();
    }
    $("#tbodyr").empty();
    $.ajax({
        async: false,
        type: "get",
        url: "tablaempleado/refresh",
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
                            <input type="checkbox" id="tdC" style="margin-right:5.6px!important"\
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
                                <a id="formNuevoEd" onclick="javascript:editarEmpleado(' +
                    data[i].emple_id +
                    ')" style="cursor: pointer">\
                                <img src="/admin/images/edit.svg" height="15">\
                                </a>\
                                &nbsp;&nbsp;&nbsp;\
                                <a onclick="javascript:marcareliminar(' +
                    data[i].emple_id +
                    ')" style="cursor: pointer">\
                                    <img src="/admin/images/delete.svg" height="15">\
                                </a>\
                                &nbsp;&nbsp;\
                                <a class="verEmpleado" onclick="javascript:verDEmpleado(' +
                    data[i].emple_id +
                    ')" data-toggle="tooltip"\
                                    data-placement="right" title="Ver Detalles" data-original-title="Ver Detalles" style="cursor:pointer">\
                                    <img src="/landing/images/see.svg" height="18">\
                                </a>\
                            </td>\
                            <td class="text-center">&nbsp; <input type="hidden" id="codE" value=' +
                    data[i].emple_id +
                    '>\
                                <img src="/admin/assets/images/users/empleado.png"/>\
                            </td>\
                            <td class="text-center"> <div class="text-wrap width-400">' +
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
                if (data[i].dispositivos.includes(1) == false) {
                    tbody +=
                        '<td class="text-center">\
                            <div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCR' +
                        data[i].emple_id +
                        '"\
                                    onchange="javascript:controlRemoto(' +
                        data[i].emple_id +
                        ')">\
                                <label class="custom-control-label" for="customSwitchCR' +
                        data[i].emple_id +
                        '"\
                                    style="font-weight: bold"></label>\
                            </div>\
                        </td>';
                } else {
                    tbody +=
                        '<td class="text-center">\
                                        <div class="dropdown" id="w' +
                        data[i].emple_id +
                        '">\
                            <a class="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer">\
                                <div class="custom-control custom-switch mb-2">\
                                    <input type="checkbox" class="custom-control-input" id="customSwitchCRW' +
                        data[i].emple_id +
                        '">\
                                    <label class="custom-control-label" for="customSwitchCRW' +
                        data[i].emple_id +
                        '" style="font-weight: bold"></label>\
                                </div>\
                            </a>\
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    for (var j = 0; j < data[i].vinculacion.length; j++) {
                        if (data[i].vinculacion[j].dispositivoD == "WINDOWS") {
                            tbody +=
                                '<a class="dropdown-item" onclick="javascript:enviarWindowsTabla(' +
                                data[i].emple_id +
                                "," +
                                data[i].vinculacion[j].idVinculacion +
                                ')">PC ' +
                                (j + 1) +
                                "</a>";
                        }
                    }
                    tbody +=
                        "</ul>\
                        </div>\
                         </td>";
                }
                if (data[i].dispositivos.includes(2) == false) {
                    tbody +=
                        '<td class="text-center">\
                                <div class="custom-control custom-switch mb-2">\
                                    <input type="checkbox" class="custom-control-input"\
                                        id="customSwitchCP' +
                        data[i].emple_id +
                        '">\
                                    <label class="custom-control-label" for="customSwitchCP' +
                        data[i].emple_id +
                        '"\
                                        style="font-weight: bold"></label>\
                                </div>\
                    </td>';
                } else {
                    tbody +=
                        '<td class="text-center">\
                                        <div class="dropdown" id="a' +
                        data[i].emple_id +
                        '">\
                            <a class="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer">\
                                <div class="custom-control custom-switch mb-2">\
                                    <input type="checkbox" class="custom-control-input" id="customSwitchCRP' +
                        data[i].emple_id +
                        '">\
                                    <label class="custom-control-label" for="customSwitchCRP' +
                        data[i].emple_id +
                        '" style="font-weight: bold"></label>\
                                </div>\
                            </a>\
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    for (var j = 0; j < data[i].vinculacion.length; j++) {
                        if (data[i].vinculacion[j].dispositivoD == "ANDROID") {
                            tbody +=
                                '<a class="dropdown-item" \
                                                onclick="javascript:enviarAndroidTabla(' +
                                data[i].emple_id +
                                "," +
                                data[i].vinculacion[j].idVinculacion +
                                ')">PC ' +
                                j +
                                1 +
                                "\
                                            </a>";
                        }
                    }
                    tbody +=
                        "</ul>\
                        </div>\
                         </td>";
                }
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
            console.log(tbody);
            $("#tbodyr").html(tbody);
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
                initComplete: function () {
                    this.api()
                        .columns()
                        .every(function () {
                            var that = this;
                            var i;
                            var val1;
                            $("#select").on("keyup change", function () {
                                i = $.fn.dataTable.util.escapeRegex(this.value);
                                console.log(i);
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
            var seleccionarTodos = $("#selectT");
            var table = $("#tablaEmpleado");
            var CheckBoxs = table.find("tbody input:checkbox");
            var CheckBoxMarcados = 0;

            seleccionarTodos.on("click", function () {
                if (seleccionarTodos.is(":checked")) {
                    CheckBoxs.prop("checked", true);
                } else {
                    CheckBoxs.prop("checked", false);
                }
            });

            CheckBoxs.on("change", function (e) {
                CheckBoxMarcados = table.find("tbody input:checkbox:checked")
                    .length;
                seleccionarTodos.prop(
                    "checked",
                    CheckBoxMarcados === CheckBoxs.length
                );
            });
        },
    });
}
