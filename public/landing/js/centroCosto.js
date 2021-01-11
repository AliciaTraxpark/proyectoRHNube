$.fn.select2.defaults.set('language', 'es');
var table;
function tablaCentroCosto() {
    table = $("#centroC").DataTable({
        scrollX: true,
        responsive: true,
        retrieve: true,
        "searching": true,
        "lengthChange": true,
        scrollCollapse: false,
        "bAutoWidth": true,
        columnDefs: [
            { targets: 2, sortable: false },
            { targets: 3, sortable: false },
            { targets: 4, sortable: false }
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
function centroCostoOrganizacion() {
    if ($.fn.DataTable.isDataTable("#centroC")) {
        $('#centroC').DataTable().destroy();
    }
    $('#centroOrg').empty();
    $.ajax({
        async: false,
        url: "/centroCOrga",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                tr += `<tr>
                        <td>${(index + 1)}</td>
                        <td>${data[index].descripcion}</td>`;
                tr += `<td><a class="badge badge-soft-primary"><i class="uil-users-alt"></i>&nbsp;${data[index].contar} emp.</a></td>`;
                if (data[index].respuesta == "Si") {
                    tr += `<td><img src="/admin/images/checkH.svg" height="13" class="mr-2">${data[index].respuesta}</td>`;
                } else {
                    tr += `<td><img src="/admin/images/borrarH.svg" height="11" class="mr-2">${data[index].respuesta}</td>`;
                }
                tr += `<td>
                        <a onclick="javascript:editarCentro(${data[index].id})" style="cursor: pointer">
                            <img src="/admin/images/edit.svg" height="15">
                        </a>
                        &nbsp;&nbsp;&nbsp;
                        <a onclick="javascript:eliminarCentro(${data[index]})" style="cursor: pointer">
                            <img src="/admin/images/delete.svg" height="15">
                        </a>
                    </td>
                </tr>`;
            }
            $('#centroOrg').html(tr);
            tablaCentroCosto();
        },
        error: function () { }
    });
}
centroCostoOrganizacion();
// ? ************************************* FORMULARIO EDITAR **************************************
$('#e_empleadosCentro').select2({
    tags: "true"
});
// * MODAL DE EDITAR
function editarCentro(id) {
    $('#e_idCentro').val(id);
    $('#e_centrocmodal').modal();
    datosCentro(id);
}
// * OBTENER DATOS DE CENTRO COSTO
var e_empleadosS;
function datosCentro(id) {
    $('#e_empleadosCentro').empty();
    $.ajax({
        async: false,
        url: "/idCentroCosto",
        method: "POST",
        data: {
            id: id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            $('#e_descripcion').val(data[0].centro.descripcion);
            if (data[0].select.length != 0) {
                var option = "";
                data[0].select.forEach(element => {
                    option += `<option value="${element.emple_id}" selected="selected">${element.nombre} ${element.apPaterno} ${element.apMaterno} </option>`;
                });
                $('#e_empleadosCentro').append(option);
            }
            if (data[0].noSelect.length != 0) {
                var optionN = "";
                data[0].noSelect.forEach(element => {
                    optionN += `<option value="${element.emple_id}">${element.nombre} ${element.apPaterno} ${element.apMaterno} </option>`;
                });
                $('#e_empleadosCentro').append(optionN);
            }
            if (data[0].noSelect.length == 0 && data[0].select.length != 0) {
                $('#e_todosEmpleados').prop("checked", true);
            } else {
                $('#e_todosEmpleados').prop("checked", false);
            }
            e_empleadosS = $('#e_empleadosCentro').val();
        },
        error: function () { }
    });
}
//* TODOS LOS EMPLEADOS EN EDITAR
$('#e_todosEmpleados').click(function () {
    if ($(this).is(':checked')) {
        $("#e_empleadosCentro > option").prop("selected", "selected");
        $('#e_empleadosCentro').trigger("change");
    } else {
        $('#e_empleadosCentro').val(e_empleadosS).trigger('change');
    }
});
//* SELECT DE EMPLEADOS
$("#e_empleadosCentro").on("change", function (e) {
    if ($("#e_empleadosCentro").select2('data').length === $("#e_empleadosCentro >option").length) {
        $('#e_todosEmpleados').prop("checked", true);
    } else {
        $('#e_todosEmpleados').prop("checked", false);
    }
});
// * ACTUALIZAR DATOS CENTRO COSTO
function actualizarCentroC() {
    var id = $('#e_idCentro').val();
    var empleados = $('#e_empleadosCentro').val();
    $.ajax({
        async: false,
        url: "/actualizarCentroC",
        method: "POST",
        data: {
            id: id,
            empleados: empleados
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            centroCostoOrganizacion();
            $('#e_centrocmodal').modal('toggle');
        },
        error: function () { }
    });
}
// ? *********************************** FINALIZACION **********************************************
// ? *********************************** ASIGNAR CENTRO COSTO **************************************
$('#a_centro').select2({
    matcher: matchStart
});
$('#a_empleadosCentro').select2({
    tags: "true"
});
// ! ABRIR MODAL DE ASIGNACION
function asignarCentroC() {
    $('#a_centrocmodal').modal();
    $('#a_empleadosCentro').prop("disabled", true);
    $('#a_todosEmpleados').prop("disabled", true);
    listasDeCentro();
}
// ! LISTA DE CENTRO DE COSTOS
function listasDeCentro() {
    $('#a_centro').empty();
    var container = $('#a_centro');
    $.ajax({
        async: false,
        url: "/listaCentro",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            data.forEach(element => {
                option += `<option value="${element.id}"> CC : ${element.descripcion} </option>`;
            });
            container.append(option);
        },
        error: function () { }
    });
}
var a_empleadosS;
$('#a_centro').on("change", function () {
    $('#a_empleadosCentro').empty();
    $('#a_empleadosCentro').prop("disabled", false);
    $('#a_todosEmpleados').prop("disabled", false);
    var id = $(this).val();
    $.ajax({
        async: false,
        url: "/empleadoCentro",
        method: "POST",
        data: {
            id: id
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            console.log(data);
            if (data[0].select.length != 0) {
                var option = "";
                data[0].select.forEach(element => {
                    option += `<option value="${element.emple_id}" selected="selected">${element.nombre} ${element.apPaterno} ${element.apMaterno} </option>`;
                });
                $('#a_empleadosCentro').append(option);
            }
            if (data[0].noSelect.length != 0) {
                var optionN = "";
                data[0].noSelect.forEach(element => {
                    optionN += `<option value="${element.emple_id}">${element.nombre} ${element.apPaterno} ${element.apMaterno} </option>`;
                });
                $('#a_empleadosCentro').append(optionN);
            }
            a_empleadosS = $('#a_empleadosCentro').val();
            if (data[0].noSelect.length == 0 && data[0].select.length != 0) {
                $('#a_todosEmpleados').prop("checked", true);
            } else {
                $('#a_todosEmpleados').prop("checked", false);
            }
        },
        error: function () { }
    });

});
// ! LIMPIAR INPUTS DE ASIGNACION
function limpiarAsignacion() {
    $('#a_centro').empty();
    $('#a_empleadosCentro').empty();
    $('#a_todosEmpleados').prop("checked", false);
}
// ! GUARDAR ASIGNACION
function guardarAsignacionCentro() {
    var id = $('#a_centro').val();
    var empleados = $('#a_empleadosCentro').val();
    $.ajax({
        async: false,
        url: "/asignacionCentro",
        method: "POST",
        data: {
            id: id,
            empleados: empleados
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            limpiarAsignacion();
            centroCostoOrganizacion();
            $('#a_centrocmodal').modal('toggle');
        },
        error: function () { }
    });
}
//! TODOS LOS EMPLEADOS EN EDITAR
$('#a_todosEmpleados').click(function () {
    if ($(this).is(':checked')) {
        $("#a_empleadosCentro > option").prop("selected", "selected");
        $('#a_empleadosCentro').trigger("change");
    } else {
        $('#a_empleadosCentro').val(a_empleadosS).trigger('change');
    }
});
//! SELECT DE EMPLEADOS
$("#a_empleadosCentro").on("change", function (e) {
    if ($("#a_empleadosCentro").select2('data').length === $("#a_empleadosCentro >option").length) {
        $('#a_todosEmpleados').prop("checked", true);
    } else {
        $('#a_todosEmpleados').prop("checked", false);
    }
});
// ? *********************************** FINALIZACION **********************************************
// ? *********************************** FORMULARIO REGISTRAR **************************************
function modalRegistrar() {
    $('#r_centrocmodal').modal();
    empleadosCC();
}
$('#r_empleadosCentro').select2({
    tags: "true"
});
// : LISTA DE EMPLEADOS
function empleadosCC() {
    $('#r_empleadosCentro').empty();
    $.ajax({
        async: false,
        url: "/listaEmpleadoCC",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            var option = "";
            data.forEach(element => {
                option += `<option value="${element.emple_id}">${element.nombre} ${element.apPaterno} ${element.apMaterno} </option>`;

            });
            $('#r_empleadosCentro').append(option)
        },
        error: function () { }
    });
}
// : GUARDAR UN CENTRO
function registrarCentroC() {
    var descripcion = $('#r_descripcion').val();
    var empleados = $('#r_empleadosCentro').val();
    $.ajax({
        async: false,
        url: "/registrarCentro",
        method: "POST",
        data: {
            descripcion: descripcion,
            empleados: empleados
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            if (data.estado == 1) {
                $("#r_descripcion").addClass("borderColor");
                $.notifyClose();
                $.notify(
                    {
                        message:
                            "\nYa existe un centro costo con este nombre.",
                        icon: "admin/images/warning.svg",
                    },
                    {
                        element: $('#r_centrocmodal'),
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
            } else {
                if (data.estado == 0) {
                    alertify
                        .confirm("Ya existe un centro costo inactivo con este nombre. ¿Desea recuperarla si o no?", function (
                            e
                        ) {
                            if (e) {
                                recuperarCentro(data.centro.centroC_id);
                            }
                        })
                        .setting({
                            title: "Modificar Centro Costo",
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
                            oncancel: function (closeEvent) {
                            },
                        });
                } else {
                    centroCostoOrganizacion();
                    $('#r_centrocmodal').modal('toggle');
                    limpiarCentro();
                    $.notifyClose();
                    $.notify(
                        {
                            message: "\nCentro Costo registrado.",
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
        error: function () { }
    });
}
// : FUNCTION DE RECUPERAR CENTRO
function recuperarCentro(id) {
    $.ajax({
        type: "GET",
        url: "/recuperarCentro",
        data: {
            id: id
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
            limpiarCentro();
            actividadesOrganizacion();
            $('#r_centrocmodal').modal('toggle');
            editarCentro(data.centroC_id);
        },
        error: function () { },
    });
}
$("#r_descripcion").keyup(function () {
    $(this).removeClass("borderColor");
});
function limpiarCentro() {
    $('#r_descripcion').val("");
    $('#r_empleadosCentro').empty();
}
// ? *********************************** FINALIZACION **********************************************
$(function () {
    $(window).on('resize', function () {
        $("#centroC").css('width', '100%');
        table.draw(true);
    });
});