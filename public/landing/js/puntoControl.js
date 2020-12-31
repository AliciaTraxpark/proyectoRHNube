var table = {};

function tablaPuntos() {
    table = $("#puntosC").DataTable({
        scrollX: true,
        responsive: true,
        retrieve: true,
        "searching": true,
        "lengthChange": true,
        scrollCollapse: false,
        "bAutoWidth": true,
        columnDefs: [
            { targets: 3, sortable: false },
            { targets: 4, sortable: false },
            { targets: 5, sortable: false }
        ],
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
function puntosControlOrganizacion() {
    if ($.fn.DataTable.isDataTable("#puntosC")) {
        $('#puntosC').DataTable().destroy();
    }
    $.ajax({
        async: false,
        url: "/puntosControlOrg",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            if (data.length != 0) {
                $('#puntoOrganizacion').empty();
                var bodyTabla = $('#puntoOrganizacion');
                var tbody = "";
                for (let index = 0; index < data.length; index++) {
                    tbody += `<tr>
                                <td>${(index + 1)}</td>
                                <td>${data[index].descripcion}</td>
                                <td>${data[index].codigoP}</td>`;
                    // * SWITCH DE CONTROL RUTA
                    if (data[index].controlRuta == 1) {
                        tbody += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchPuntoCROrg${data[index].id}" checked>
                                        <label class="custom-control-label" for="switchPuntoCROrg${data[index].id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    } else {
                        tbody += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchPuntoCROrg${data[index].id}">
                                        <label class="custom-control-label" for="switchPuntoCROrg${data[index].id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    }
                    // * ASISTENCIA EN PUERTA
                    if (data[index].asistenciaPuerta == 1) {
                        tbody += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchPuntoAPOrg${data[index].id}" checked>
                                        <label class="custom-control-label" for="switchPuntoCROrg${data[index].id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    } else {
                        tbody += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchPuntoAPOrg${data[index].id}">
                                        <label class="custom-control-label" for="switchPuntoCROrg${data[index].id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    }

                    tbody += `<td class="text-center">
                                <a onclick="javascript:editarActividad(${data[index].id})" style="cursor: pointer">
                                    <img src="/admin/images/edit.svg" height="15">
                                </a>
                                &nbsp;&nbsp;&nbsp;
                                <a onclick="javascript:eliminarActividad(${data[index].id})" style="cursor: pointer">
                                    <img src="/admin/images/delete.svg" height="15">
                                </a>
                            </td>`;
                    tbody += `</tr>`;
                    bodyTabla.append(tbody);
                    tablaPuntos();
                    $(window).on('resize', function () {
                        $('#puntosC').css('width', '100%');
                        table.draw(true);
                    });
                }
            }
        },
        error: function () { }
    });
}
puntosControlOrganizacion();
function editarActividad(id) {
    $.ajax({
        async: false,
        url: "/puntoControlData",
        method: "POST",
        data: {
            idPunto: id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
            $('#e_idPuntoC').val(data[0].id);
            $('#e_descripcionPunto').val(data[0].descripcion);
            //* CONTROL RUTA
            if (data[0].controlRuta == 1) {
                $('#e_puntoCRT').prop("checked", true);
                //* MOSTRAR ASIGNACION DE EMPLEADOS Y AREAS
                $('.rowEmpleadosEditar').show();
                $('.rowAreasEditar').show();
            } else {
                $('#e_puntoCRT').prop("checked", false);
                //* OCULTAR ASIGNACION DE EMPLEADOS Y AREAS
                $('.rowEmpleadosEditar').hide();
                $('.rowAreasEditar').hide();
            }
            //* ASISTENCIA EN PUERTA
            if (data[0].asistenciaPuerta == 1) {
                $('#e_puntoAP').prop("checked", true);
            } else {
                $('#e_puntoAP').prop("checked", false);
            }
            if (data[0].porEmpleados == 1) {
                $('#e_puntosPorE').prop("checked", true);
                $('.colxEmpleados').show();
                $('.colxAreas').hide();
            } else {
                $('#e_puntosPorE').prop("checked", false);
                $('.colxEmpleados').hide();
            }
            if (data[0].porAreas == 1) {
                $('#e_puntosPorA').prop("checked", true);
                $('.colxEmpleados').hide();
                $('.colxAreas').show();
            } else {
                $('#e_puntosPorA').prop("checked", false);
                $('.colxAreas').hide();
            }
        },
        error: function () { }
    });
    $('#editarPuntoControl').modal();
}
//* CHANGE DE SWITCH DE CONTROLES
$('#e_puntoCRT').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        $('.rowEmpleadosEditar').show();
        $('.rowAreasEditar').show();
    } else {
        $('.rowEmpleadosEditar').hide();
        $('.rowAreasEditar').hide();
    }
});
$(function () {
    $(window).on('resize', function () {
        $('#puntosC').css('width', '100%');
        table.draw(true);
    });
});
