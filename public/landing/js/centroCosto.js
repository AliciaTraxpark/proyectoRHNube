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
    var descripcion = $('#e_descripcion').val();
    var empleados = $('#e_empleadosCentro').val();
    $.ajax({
        async: false,
        url: "/actualizarCentroC",
        method: "POST",
        data: {
            id: id,
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
function asignarCentroC() {
    $('#a_centrocmodal').modal();
    $('#a_empleadosCentro').prop("disabled", true);
    listasDeCentro();
}
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
$('#a_centro').on("change", function () {
    $('#a_empleadosCentro').empty();
    $('#a_empleadosCentro').prop("disabled", false);
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
        },
        error: function () { }
    });

});
function limpiarAsignacion() {
    $('#a_centro').empty();
    $('#a_empleadosCentro').empty();
}
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
// ? *********************************** FINALIZACION **********************************************
$(function () {
    $(window).on('resize', function () {
        $("#centroC").css('width', '100%');
        table.draw(true);
    });
});