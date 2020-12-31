var table = {};
//* INICIALIZACION DE TABLA
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
//* CARGAR DATOS DE TABLA
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
// ! ******************* FORMULARIO DE EDITAR **********************
$('#e_empleadosPunto').select2({
    tags: "true"
});
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
                empleadosPuntos(data[0].id);
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
function empleadosPuntos(id) {
    $('#e_empleadosPunto').empty();
    $.ajax({
        async: false,
        url: "/puntoControlxEmpleados",
        method: "POST",
        data: {
            idPunto: id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            var itemsSelect = "";
            //* EMPLEADOS SELECCIONADOS
            for (let index = 0; index < data[0].select.length; index++) {
                itemsSelect += `<option value="${data[0].select[index].emple_id}" selected="selected">${data[0].select[index].nombre} ${data[0].select[index].apPaterno} ${data[0].select[index].apMaterno}</option>`;
            }
            //* EMPLEADOS NO SELECCIONADOS
            for (let item = 0; item < data[0].noSelect.length; item++) {
                itemsSelect += `<option value="${data[0].noSelect[item].emple_id}">${data[0].noSelect[item].nombre} ${data[0].noSelect[item].apPaterno} ${data[0].noSelect[item].apMaterno}</option>`;
            }
            $('#e_empleadosPunto').append(itemsSelect);
            if (data[0].select.length != 0 && data[0].noSelect.length == 0) {
                $('#e_todosEmpleados').prop("checked", true);
            } else {
                $('#e_todosEmpleados').prop("checked", false);
            }
        },
        error: function () { }
    });
}
//* EDITAR PUNTO DE CONTROL
function editarPuntoControl() {
    var idPunto = $('#e_idPuntoC').val();
    var codigo = $('#e_codigoPunto').val();
    var empleados = $('#e_empleadosPunto').val();
    var areas = $('#e_areasPunto').val();
    var porEmpleados;
    var porAreas;
    var controlRuta;
    var asistenciaPuerta;
    // * CONTROL EN RUTA
    if ($('#e_puntoCRT').is(":checked")) {
        controlRuta = 1;
    } else {
        controlRuta = 0;
    }
    //* ASISTENCIA EN PUERTA
    if ($('#e_puntoAP').is(":checked")) {
        asistenciaPuerta = 1;
    } else {
        asistenciaPuerta = 0;
    }
    //* POR EMPLEADOS
    if ($('#e_puntosPorE').is(":checked")) {
        porEmpleados = 1;
    } else {
        porEmpleados = 0;
    }
    //* POR AREAS
    if ($('#e_puntosPorA').is(":checked")) {
        porAreas = 1;
    } else {
        porAreas = 0;
    }
    $.ajax({
        async: false,
        type: "POST",
        url: "/editPuntoControl",
        data: {
            id: idPunto,
            cr: controlRuta,
            ap: asistenciaPuerta,
            codigo: codigo,
            empleados: empleados,
            areas: areas,
            porEmpleados: porEmpleados,
            porAreas: porAreas
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            limpiarPuntoEnEditar();
            puntosControlOrganizacion();
            $.notifyClose();
            $.notify(
                {
                    message: "\nPunto Control modificado.",
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
            $('#editarPuntoControl').modal("toggle")
        },
        error: function () { }
    });
}
function limpiarPuntoEnEditar() {
    $('#e_idPuntoC').val("");
    $('#e_codigoPunto').val("");
}
// ! ****************** FINALIZACION *****************************
$(function () {
    $(window).on('resize', function () {
        $('#puntosC').css('width', '100%');
        table.draw(true);
    });
});
