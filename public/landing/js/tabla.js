$(document).ready(function () {
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

function RefreshTablaEmpleadoArea() {
    if ($.fn.DataTable.isDataTable("#tablaEmpleado")) {
        $("#tablaEmpleado").DataTable().destroy();
    }
    $("#tbodyr").empty();
    var areaselect = $('#selectarea').val();

    let textSelec = $('select[id="selectarea"] option:selected:last').text();
    let selector = textSelec.split(' ');
    console.log("Selector "+selector[1]+" "+selector[3]);
    $.ajax({
        async: false,
        type: "post",
        url: "tablaempleado/refreshArea",
        data: { idarea: areaselect, selector: selector},
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
                            <input type="checkbox" name="selec" id="tdC" style="margin-right:5.7px!important"\
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
                                <a name="editarEName" onclick="javascript:editarEmpleado(' +
                    data[i].emple_id +
                    ')" style="cursor: pointer">\
                                <img src="/admin/images/edit.svg" height="15">\
                                </a>\
                                &nbsp;&nbsp;&nbsp;\
                                <a data-toggle="tooltip" name="dBajaName" data-placement="right" data-original-title="Dar de baja" onclick="javascript:marcareliminar(' +
                    data[i].emple_id +
                    ')" style="cursor: pointer">\
                                    <img src="/landing/images/abajo.svg" height="17">\
                                </a>\
                                &nbsp;&nbsp;\
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
                    '<td class="text-center">' +
                    data[i].emple_nDoc +
                    '</td>\
                            <td>' +
                    data[i].perso_nombre +
                    '</td>\
                            <td>' +
                    data[i].perso_apPaterno +
                    " " +
                    data[i].perso_apMaterno +
                    "</td>";
                if (data[i].dispositivos.includes(1) == false) {
                    tbody +=
                        '<td class="text-center">\
                            <div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCR' +
                        data[i].emple_id +
                        '"\
                                    onclick="javascript:controlRemoto(' +
                        data[i].emple_id + ",'" +
                        data[i].perso_nombre +
                        '\')">\
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
                                <div class="custom-control custom-switch mb-2">';
                    if (data[i].estadoCR == true) {
                        tbody +=
                            '<input type="checkbox" class="custom-control-input" id="customSwitchCRW' +
                            data[i].emple_id +
                            '" checked>';
                    } else {
                        tbody +=
                            '<input type="checkbox" class="custom-control-input" id="customSwitchCRW' +
                            data[i].emple_id +
                            '">';
                    }
                    tbody +=
                        '<label class="custom-control-label" for="customSwitchCRW' +
                        data[i].emple_id +
                        '" style="font-weight: bold"></label>\
                                </div>\
                            </a>\
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    for (var j = 0; j < data[i].vinculacion.length; j++) {
                        if (data[i].vinculacion[j].dispositivoD == "WINDOWS") {
                            tbody += '<div class="dropdown-item">';
                            if (
                                data[i].vinculacion[j].disponible == "c" ||
                                data[i].vinculacion[j].disponible == "e" ||
                                data[i].vinculacion[j].disponible == "a"
                            ) {
                                tbody +=
                                    '<div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCRDisp' +
                                    data[i].vinculacion[j].idVinculacion +
                                    '" checked\
                                    onclick="javasscript:estadoDispositivoCR(' +
                                    data[i].emple_id +
                                    "," +
                                    data[i].vinculacion[j].idVinculacion +
                                    "," +
                                    j +
                                    ",'" +
                                    data[i].perso_nombre +
                                    '\')">';

                                if (data[i].vinculacion[j].pc === null) {
                                    tbody += '<label class="custom-control-label" for="customSwitchCRDisp' +
                                        data[i].vinculacion[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">PC' +
                                        j +
                                        "</label>";
                                } else {
                                    tbody += '<label class="custom-control-label" for="customSwitchCRDisp' +
                                        data[i].vinculacion[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">' +
                                        data[i].vinculacion[j].pc +
                                        "</label>";
                                }
                                tbody += "</div>";
                            } else {
                                if (data[i].vinculacion[j].pc === null) {
                                    tbody +=
                                        '<div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCRDisp' +
                                        data[i].vinculacion[j].idVinculacion +
                                        '"\
                                    onclick="javasscript:estadoDispositivoCR(' +
                                        data[i].emple_id +
                                        "," +
                                        data[i].vinculacion[j].idVinculacion +
                                        "," + "'PC " +
                                        j + "'" +
                                        ",'" +
                                        data[i].perso_nombre +
                                        '\')">';
                                    tbody += '<label class="custom-control-label" for="customSwitchCRDisp' +
                                        data[i].vinculacion[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">PC' +
                                        j +
                                        "</label>";
                                } else {
                                    tbody +=
                                        '<div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCRDisp' +
                                        data[i].vinculacion[j].idVinculacion +
                                        '"\
                                    onclick="javasscript:estadoDispositivoCR(' +
                                        data[i].emple_id +
                                        "," +
                                        data[i].vinculacion[j].idVinculacion +
                                        "," + "'" +
                                        data[i].vinculacion[j].pc + "'" +
                                        ",'" +
                                        data[i].perso_nombre +
                                        '\')">';
                                    tbody += '<label class="custom-control-label" for="customSwitchCRDisp' +
                                        data[i].vinculacion[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">' +
                                        data[i].vinculacion[j].pc +
                                        "</label>";
                                }
                                tbody += "</div>";
                            }

                            tbody += "</div>";
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
                                    id="customSwitchCRT' +
                        data[i].emple_id +
                        '"\
                                    onclick="javascript:controlRuta(' +
                        data[i].emple_id + ",'" +
                        data[i].perso_nombre +
                        '\')">\
                                <label class="custom-control-label" for="customSwitchCRT' +
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
                                <div class="custom-control custom-switch mb-2">';
                    if (data[i].estadoCRT == true) {
                        tbody +=
                            '<input type="checkbox" class="custom-control-input" id="customSwitchCRA' +
                            data[i].emple_id +
                            '" checked>';
                    } else {
                        tbody +=
                            '<input type="checkbox" class="custom-control-input" id="customSwitchCRA' +
                            data[i].emple_id +
                            '">';
                    }
                    tbody +=
                        '<label class="custom-control-label" for="customSwitchCRA' +
                        data[i].emple_id +
                        '" style="font-weight: bold"></label>\
                                </div>\
                            </a>\
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    for (var j = 0; j < data[i].vinculacionRuta.length; j++) {
                        if (data[i].vinculacion[j].dispositivoD == "ANDROID") {
                            tbody += '<div class="dropdown-item">';
                            if (
                                data[i].vinculacionRuta[j].disponible == "c" ||
                                data[i].vinculacionRuta[j].disponible == "e" ||
                                data[i].vinculacionRuta[j].disponible == "a"
                            ) {
                                tbody +=
                                    '<div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCRTDisp' +
                                    data[i].vinculacionRuta[j].idVinculacion +
                                    '" checked\
                                    onclick="javasscript:estadoDispositivoCRT(' +
                                    data[i].emple_id +
                                    "," +
                                    data[i].vinculacionRuta[j].idVinculacion +
                                    "," +
                                    j +
                                    ",'" +
                                    data[i].perso_nombre +
                                    '\')">';

                                if (data[i].vinculacionRuta[j].modelo === null) {
                                    tbody += '<label class="custom-control-label" for="customSwitchCRTDisp' +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">CEL ' +
                                        j +
                                        "</label>";
                                } else {
                                    tbody += '<label class="custom-control-label" for="customSwitchCRTDisp' +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">' +
                                        data[i].vinculacionRuta[j].modelo +
                                        "</label>";
                                }
                                tbody += "</div>";
                            } else {
                                if (data[i].vinculacionRuta[j].modelo === null) {
                                    tbody +=
                                        '<div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCRTDisp' +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        '"\
                                    onclick="javasscript:estadoDispositivoCRT(' +
                                        data[i].emple_id +
                                        "," +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        "," + "'CEL " +
                                        j + "'" +
                                        ",'" +
                                        data[i].perso_nombre +
                                        '\')">';
                                    tbody += '<label class="custom-control-label" for="customSwitchCRTDisp' +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">CEL ' +
                                        j +
                                        "</label>";
                                } else {
                                    tbody +=
                                        '<div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCRTDisp' +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        '"\
                                    onclick="javasscript:estadoDispositivoCRT(' +
                                        data[i].emple_id +
                                        "," +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        "," + "'" +
                                        data[i].vinculacionRuta[j].modelo + "'" +
                                        ",'" +
                                        data[i].perso_nombre +
                                        '\')">';
                                    tbody += '<label class="custom-control-label" for="customSwitchCRTDisp' +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">' +
                                        data[i].vinculacionRuta[j].modelo +
                                        "</label>";
                                }
                                tbody += "</div>";
                            }

                            tbody += "</div>";
                        }
                    }
                    tbody +=
                        "</ul>\
                        </div>\
                         </td>";
                }
                if (data[i].asistencia_puerta == 1) {
                    tbody +=
                        '<td class="text-center">\
                                <div class="custom-control custom-switch mb-2">\
                                    <input type="checkbox" class="custom-control-input"\
                                        id="customSwitchCP' +
                        data[i].emple_id +
                        '" onclick="controlPuerta(' + data[i].emple_id + ')" checked>\
                                    <label class="custom-control-label" for="customSwitchCP' +
                        data[i].emple_id +
                        '"\
                                        style="font-weight: bold"></label>\
                                </div>\
                    </td>';
                } else {
                    tbody +=
                        '<td class="text-center">\
                            <div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCP' +
                        data[i].emple_id +
                        '" onclick="controlPuerta(' + data[i].emple_id + ')">\
                                <label class="custom-control-label" for="customSwitchCP' +
                        data[i].emple_id +
                        '"\
                                    style="font-weight: bold"></label>\
                            </div>\
                </td>';

                }
                if (data[i].modoTareo == 1) {
                    tbody +=
                        '<td class="text-center">\
                                <div class="custom-control custom-switch mb-2">\
                                    <input type="checkbox" class="custom-control-input"\
                                        id="customSwitchMT' +
                        data[i].emple_id +
                        '" onclick="modoTareo(' + data[i].emple_id + ')" checked>\
                                    <label class="custom-control-label" for="customSwitchMT' +
                        data[i].emple_id +
                        '"\
                                        style="font-weight: bold"></label>\
                                </div>\
                    </td>';
                } else {
                    tbody +=
                        '<td class="text-center">\
                            <div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchMT' +
                        data[i].emple_id +
                        '" onclick="modoTareo(' + data[i].emple_id + ')">\
                                <label class="custom-control-label" for="customSwitchMT' +
                        data[i].emple_id +
                        '"\
                                    style="font-weight: bold"></label>\
                            </div>\
                </td>';

                }
                if (data[i].cargo_descripcion == null) {
                    tbody += '<td></td>';
                } else {
                    tbody +=
                        '<td>' +
                        data[i].cargo_descripcion +
                        "</td>";
                }
                if (data[i].area_descripcion == null) {
                    tbody +=
                        '<td></td></tr>';
                } else {
                    tbody +=
                        '<td>' +
                        data[i].area_descripcion +
                        "</td></tr>";
                }
            }
            $("#tbodyr").html(tbody);
            $("#tablaEmpleado").DataTable({
                scrollX: true,
                responsive: true,
                retrieve: true,
                searching: true,
                scrollCollapse: false,
                pageLength: 30,
                bAutoWidth: true,
                "pageLength": 10,
                "lengthMenu": [ 10, 25, 50, 75, 100 ],
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
                            $("#selectBtn").on("click", function () {
                                //*reseteamos para que no coga con cache
                                that
                                .search( '' )
                                .columns().search( '' )
                                .draw();

                                var selector = $("#select").val();

                                i = $.fn.dataTable.util.escapeRegex(selector);

                                var val = $("#global_filter").val();

                                //*if valor es diferente null entonces buscamos
                                if(val!=null || val!=''){
                                    if (that.column(i).search() !== selector) {
                                        that.column(selector).search(val).draw();
                                    }
                                }

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
            if (data.length > 0) {
                $('#btnContinuar').prop("disabled", false);
                $('#btnContinuar').attr('title', 'Continuar');
            }
            else {
                $('#btnContinuar').prop("disabled", true);
                $('#btnContinuar').attr('title', 'Registre al menos un empleado para poder continuar');

            }
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
                            <input type="checkbox" name="selec" id="tdC" style="margin-right:5.7px!important"\
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
                                <a name="editarEName"  onclick="javascript:editarEmpleado(' +
                    data[i].emple_id +
                    ')" style="cursor: pointer">\
                                <img src="/admin/images/edit.svg" height="15">\
                                </a>\
                                &nbsp;&nbsp;&nbsp;\
                                <a data-toggle="tooltip" name="dBajaName" data-placement="right" data-original-title="Dar de baja" onclick="javascript:marcareliminar(' +
                    data[i].emple_id +
                    ')" style="cursor: pointer">\
                                    <img src="/landing/images/abajo.svg" height="17">\
                                </a>\
                                &nbsp;&nbsp;\
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
                    '<td> ' +
                    data[i].emple_nDoc +
                    '</td>\
                            <td> ' +
                    data[i].perso_nombre +
                    '</td>\
                            <td> ' +
                    data[i].perso_apPaterno +
                    " " +
                    data[i].perso_apMaterno +
                    "</td>";
                if (data[i].dispositivos.includes(1) == false) {
                    tbody +=
                        '<td class="text-center">\
                            <div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCR' +
                        data[i].emple_id +
                        '"\
                                    onclick="javascript:controlRemoto(' +
                        data[i].emple_id +
                        ",'" +
                        data[i].perso_nombre +
                        '\')">\
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
                                <div class="custom-control custom-switch mb-2">';
                    if (data[i].estadoCR == true) {
                        tbody +=
                            '<input type="checkbox" class="custom-control-input" id="customSwitchCRW' +
                            data[i].emple_id +
                            '" checked>';
                    } else {
                        tbody +=
                            '<input type="checkbox" class="custom-control-input" id="customSwitchCRW' +
                            data[i].emple_id +
                            '">';
                    }
                    tbody +=
                        '<label class="custom-control-label" for="customSwitchCRW' +
                        data[i].emple_id +
                        '" style="font-weight: bold"></label>\
                                </div>\
                            </a>\
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    for (var j = 0; j < data[i].vinculacion.length; j++) {
                        if (data[i].vinculacion[j].dispositivoD == "WINDOWS") {
                            tbody += '<div class="dropdown-item">';
                            if (
                                data[i].vinculacion[j].disponible == "c" ||
                                data[i].vinculacion[j].disponible == "e" ||
                                data[i].vinculacion[j].disponible == "a"
                            ) {
                                tbody +=
                                    '<div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCRDisp' +
                                    data[i].vinculacion[j].idVinculacion +
                                    '" checked\
                                    onclick="javasscript:estadoDispositivoCR(' +
                                    data[i].emple_id +
                                    "," +
                                    data[i].vinculacion[j].idVinculacion +
                                    "," +
                                    j +
                                    ",'" +
                                    data[i].perso_nombre +
                                    '\')">';

                                if (data[i].vinculacion[j].pc === null) {
                                    tbody += '<label class="custom-control-label" for="customSwitchCRDisp' +
                                        data[i].vinculacion[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">PC' +
                                        j +
                                        "</label>";
                                } else {
                                    tbody += '<label class="custom-control-label" for="customSwitchCRDisp' +
                                        data[i].vinculacion[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">' +
                                        data[i].vinculacion[j].pc +
                                        "</label>";
                                }
                                tbody += "</div>";
                            } else {
                                if (data[i].vinculacion[j].pc === null) {
                                    tbody +=
                                        '<div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCRDisp' +
                                        data[i].vinculacion[j].idVinculacion +
                                        '"\
                                    onclick="javasscript:estadoDispositivoCR(' +
                                        data[i].emple_id +
                                        "," +
                                        data[i].vinculacion[j].idVinculacion +
                                        "," + "'PC " +
                                        j + "'" +
                                        ",'" +
                                        data[i].perso_nombre +
                                        '\')">';
                                    tbody += '<label class="custom-control-label" for="customSwitchCRDisp' +
                                        data[i].vinculacion[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">PC' +
                                        j +
                                        "</label>";
                                } else {
                                    tbody +=
                                        '<div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCRDisp' +
                                        data[i].vinculacion[j].idVinculacion +
                                        '"\
                                    onclick="javasscript:estadoDispositivoCR(' +
                                        data[i].emple_id +
                                        "," +
                                        data[i].vinculacion[j].idVinculacion +
                                        "," + "'" +
                                        data[i].vinculacion[j].pc + "'" +
                                        ",'" +
                                        data[i].perso_nombre +
                                        '\')">';
                                    tbody += '<label class="custom-control-label" for="customSwitchCRDisp' +
                                        data[i].vinculacion[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">' +
                                        data[i].vinculacion[j].pc +
                                        "</label>";
                                }
                                tbody += "</div>";
                            }

                            tbody += "</div>";
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
                                    id="customSwitchCRT' +
                        data[i].emple_id +
                        '"\
                                    onclick="javascript:controlRuta(' +
                        data[i].emple_id + ",'" +
                        data[i].perso_nombre +
                        '\')">\
                                <label class="custom-control-label" for="customSwitchCRT' +
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
                                <div class="custom-control custom-switch mb-2">';
                    console.log(data[i].estadoCRT);
                    if (data[i].estadoCRT == true) {
                        tbody +=
                            '<input type="checkbox" class="custom-control-input" id="customSwitchCRA' +
                            data[i].emple_id +
                            '" checked>';
                    } else {
                        tbody +=
                            '<input type="checkbox" class="custom-control-input" id="customSwitchCRA' +
                            data[i].emple_id +
                            '">';
                    }
                    tbody +=
                        '<label class="custom-control-label" for="customSwitchCRA' +
                        data[i].emple_id +
                        '" style="font-weight: bold"></label>\
                                </div>\
                            </a>\
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    for (var j = 0; j < data[i].vinculacionRuta.length; j++) {
                        if (data[i].vinculacionRuta[j].dispositivoD == "ANDROID") {
                            tbody += '<div class="dropdown-item">';
                            if (
                                data[i].vinculacionRuta[j].disponible == "c" ||
                                data[i].vinculacionRuta[j].disponible == "e" ||
                                data[i].vinculacionRuta[j].disponible == "a"
                            ) {
                                tbody +=
                                    '<div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCRTDisp' +
                                    data[i].vinculacionRuta[j].idVinculacion +
                                    '" checked\
                                    onclick="javasscript:estadoDispositivoCRT(' +
                                    data[i].emple_id +
                                    "," +
                                    data[i].vinculacionRuta[j].idVinculacion +
                                    "," +
                                    j +
                                    ",'" +
                                    data[i].perso_nombre +
                                    '\')">';

                                if (data[i].vinculacionRuta[j].modelo === null) {
                                    tbody += '<label class="custom-control-label" for="customSwitchCRTDisp' +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">CEL ' +
                                        j +
                                        "</label>";
                                } else {
                                    tbody += '<label class="custom-control-label" for="customSwitchCRTDisp' +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">' +
                                        data[i].vinculacionRuta[j].modelo +
                                        "</label>";
                                }
                                tbody += "</div>";
                            } else {
                                if (data[i].vinculacionRuta[j].modelo === null) {
                                    tbody +=
                                        '<div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCRTDisp' +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        '"\
                                    onclick="javasscript:estadoDispositivoCRT(' +
                                        data[i].emple_id +
                                        "," +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        "," + "'CEL " +
                                        j + "'" +
                                        ",'" +
                                        data[i].perso_nombre +
                                        '\')">';
                                    tbody += '<label class="custom-control-label" for="customSwitchCRTDisp' +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">CEL ' +
                                        j +
                                        "</label>";
                                } else {
                                    tbody +=
                                        '<div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCRTDisp' +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        '"\
                                    onclick="javasscript:estadoDispositivoCRT(' +
                                        data[i].emple_id +
                                        "," +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        "," + "'" +
                                        data[i].vinculacionRuta[j].modelo + "'" +
                                        ",'" +
                                        data[i].perso_nombre +
                                        '\')">';
                                    tbody += '<label class="custom-control-label" for="customSwitchCRTDisp' +
                                        data[i].vinculacionRuta[j].idVinculacion +
                                        '"\
                                    style="font-weight: bold">' +
                                        data[i].vinculacionRuta[j].modelo +
                                        "</label>";
                                }
                                tbody += "</div>";
                            }

                            tbody += "</div>";
                        }
                    }
                    tbody +=
                        "</ul>\
                        </div>\
                         </td>";
                }
                if (data[i].asistencia_puerta == 1) {
                    tbody +=
                        '<td class="text-center">\
                                <div class="custom-control custom-switch mb-2">\
                                    <input type="checkbox" class="custom-control-input"\
                                        id="customSwitchCP' +
                        data[i].emple_id +
                        '" onclick="controlPuerta(' + data[i].emple_id + ')" checked>\
                                    <label class="custom-control-label" for="customSwitchCP' +
                        data[i].emple_id +
                        '"\
                                        style="font-weight: bold"></label>\
                                </div>\
                    </td>';
                } else {
                    tbody +=
                        '<td class="text-center">\
                            <div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchCP' +
                        data[i].emple_id +
                        '" onclick="controlPuerta(' + data[i].emple_id + ')">\
                                <label class="custom-control-label" for="customSwitchCP' +
                        data[i].emple_id +
                        '"\
                                    style="font-weight: bold"></label>\
                            </div>\
                </td>';

                }
                if (data[i].modoTareo == 1) {
                    tbody +=
                        '<td class="text-center">\
                                <div class="custom-control custom-switch mb-2">\
                                    <input type="checkbox" class="custom-control-input"\
                                        id="customSwitchMT' +
                        data[i].emple_id +
                        '" onclick="modoTareo(' + data[i].emple_id + ')" checked>\
                                    <label class="custom-control-label" for="customSwitchMT' +
                        data[i].emple_id +
                        '"\
                                        style="font-weight: bold"></label>\
                                </div>\
                    </td>';
                } else {
                    tbody +=
                        '<td class="text-center">\
                            <div class="custom-control custom-switch mb-2">\
                                <input type="checkbox" class="custom-control-input"\
                                    id="customSwitchMT' +
                        data[i].emple_id +
                        '" onclick="modoTareo(' + data[i].emple_id + ')">\
                                <label class="custom-control-label" for="customSwitchMT' +
                        data[i].emple_id +
                        '"\
                                    style="font-weight: bold"></label>\
                            </div>\
                </td>';

                }
                if (data[i].cargo_descripcion == null) {
                    tbody += '<td></td>';
                } else {
                    tbody +=
                        '<td>' +
                        data[i].cargo_descripcion +
                        "</td>";
                }
                if (data[i].area_descripcion == null) {
                    tbody +=
                        '<td></td></tr>';
                } else {
                    tbody +=
                        '<td>' +
                        data[i].area_descripcion +
                        "</td></tr>";
                }
            }
            $("#tbodyr").html(tbody);
            $("#tablaEmpleado").DataTable({
                scrollX: true,
                responsive: true,
                retrieve: true,
                searching: true,
                scrollCollapse: false,
                pageLength: 30,
                bAutoWidth: true,
                "pageLength": 10,
                "lengthMenu": [ 10, 25, 50, 75, 100 ],
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
                            $("#selectBtn").on("click", function () {
                                //*reseteamos para que no coga con cache
                                that
                                .search( '' )
                                .columns().search( '' )
                                .draw();

                                var selector = $("#select").val();

                                i = $.fn.dataTable.util.escapeRegex(selector);

                                var val = $("#global_filter").val();

                                //*if valor es diferente null entonces buscamos
                                if(val!=null || val!=''){
                                    if (that.column(i).search() !== selector) {
                                        that.column(selector).search(val).draw();
                                    }
                                }

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
