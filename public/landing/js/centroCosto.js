$.fn.select2.defaults.set('language', 'es');
var table;
var sent = false;
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
                if (data[index].porEmpleado == 1) {
                    tr += `<td>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="switchPorEmpleado${data[index].id}" checked>
                                    <label class="custom-control-label" for="switchPorEmpleado${data[index].id}" style="font-weight: bold"></label>
                                </div>
                            </td>`;
                } else {
                    tr += `<td>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="switchPorEmpleado${data[index].id}">
                                    <label class="custom-control-label" for="switchPorEmpleado${data[index].id}" style="font-weight: bold"></label>
                                </div>
                            </td>`;
                }
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
                        <a onclick="javascript:eliminarCentro(${data[index].id})" style="cursor: pointer">
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
    placeholder: "Empleados",
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
// * MODAL DE EDITAR
function editarCentro(id) {
    $('#e_idCentro').val(id);
    $('#e_centrocmodal').modal();
    datosCentro(id);
    sent = false;
}
// * OBTENER DATOS DE CENTRO COSTO
var e_empleadosS;
async function datosCentro(id) {
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
            $('#e_descripcion').val(data.descripcion);
            $('#e_codigo').val(data.codigo);
            // : ASISTENCIA EN PUERTA
            if (data.asistenciaPuerta == 1) {
                $('#e_switchAP').prop("checked", true);
            } else {
                $('#e_switchAP').prop("checked", false);
            }
            // : MODO TAREO
            if (data.modoTareo == 1) {
                $('#e_switchMT').prop("checked", true);
            } else {
                $('#e_switchMT').prop("checked", false);
            }
            // : ASIGNACION POR EMPLEADO
            if (data.porEmpleado == 1) {
                $('#switchPorEmpleado').prop("checked", true);
                $('#e_rowEmpleado').show();
                $('#e_empleadosCentro').empty();
                empleadosCentroCosto(data.id);
            } else {
                $('#e_rowEmpleado').hide();
                $('#e_empleadosCentro').empty();
                $('#e_todosEmpleados').prop("checked", false);
                $('#switchPorEmpleado').prop("checked", false);
            }
        },
        error: function () { }
    });
}
function empleadosCentroCosto(id) {
    $.ajax({
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
            if (data.select.length != 0) {
                var option = "";
                data.select.forEach(element => {
                    option += `<option value="${element.emple_id}" selected="selected">${element.nombre} ${element.apPaterno} ${element.apMaterno} </option>`;
                });
                $('#e_empleadosCentro').append(option);
            }
            if (data.noSelect.length != 0) {
                var optionN = "";
                data.noSelect.forEach(element => {
                    optionN += `<option value="${element.emple_id}">${element.nombre} ${element.apPaterno} ${element.apMaterno} </option>`;
                });
                $('#e_empleadosCentro').append(optionN);
            }
            if (data.noSelect.length == 0 && data.select.length != 0) {
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
    var codigo = $('#e_codigo').val();
    var empleados = $('#e_empleadosCentro').val();
    var porEmpleado;
    var asistenciaPuerta;
    var modoTareo;
    // : ************************* POR EMPLEADO ****************
    if ($('#switchPorEmpleado').is(":checked")) {
        porEmpleado = 1;
    } else {
        porEmpleado = 0;
    }
    // : ************************* ASISTENCIA EN PUERTA *********
    if ($('#e_switchAP').is(":checked")) {
        asistenciaPuerta = 1;
    } else {
        asistenciaPuerta = 0;
    }
    // : ************************* MODO DE TAREO ****************
    if ($('#e_switchMT').is(":checked")) {
        modoTareo = 1;
    } else {
        modoTareo = 0;
    }
    // : ************************* FINALIZACION *****************
    $.ajax({
        async: false,
        url: "/actualizarCentroC",
        method: "POST",
        data: {
            id: id,
            empleados: empleados,
            codigo: codigo,
            porEmpleado: porEmpleado,
            asistenciaPuerta: asistenciaPuerta,
            modoTareo: modoTareo
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
            if (data.respuesta == undefined) {
                centroCostoOrganizacion();
                $('#e_centrocmodal').modal('toggle');
                $.notifyClose();
                $.notify(
                    {
                        message: "\nCentro de costo modificado.",
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
                sent = false;
                if (data.respuesta == 1) {
                    $('#e_codigo').addClass("borderColor");
                    $.notifyClose();
                    $.notify(
                        {
                            message: data.mensaje,
                            icon: "admin/images/warning.svg",
                        },
                        {
                            element: $('#e_centrocmodal'),
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
            }
        },
        error: function () { }
    });
}
// * VALIDAR FORMULARIO
$('#formActualizarCentroC').attr('novalidate', true);
$('#formActualizarCentroC').submit(function (e) {
    e.preventDefault();
    // : SWITCH DE POR EMPLEADO
    if ($('#switchPorEmpleado').is(":checked")) {
        if ($("#e_empleadosCentro").val().length == 0) {
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar Empleado',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#e_centrocmodal"),
                position: "fixed",
                icon_type: 'image',
                placement: {
                    from: "top",
                    align: "center",
                },
                allow_dismiss: true,
                newest_on_top: true,
                delay: 6000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
            sent = false;
            return;
        }
    }
    // : EVITAR EL DOBLE ENVIO DE FORMULARIO
    if (!sent) {
        sent = true;
        this.submit();
    }
});
// * RETIRAR CLASE DE INPUT
$('#e_codigo').keyup(function () {
    $(this).removeClass("borderColor");
});
// * SWITCH POR EMPLEADO
$('#switchPorEmpleado').on("change.bootstrapSwitch", function (event) {
    $(this).prop("disabled", true);
    if (event.target.checked) {
        $('#e_rowEmpleado').show();
        $('#e_empleadosCentro').empty();
        var id = $('#e_idCentro').val();
        empleadosCentroCosto(id);
    } else {
        $('#e_rowEmpleado').hide();
        $('#e_todosEmpleados').prop("checked", false);
        $('#e_empleadosCentro').empty();
    }
    $(this).prop("disabled", false);
});
// ? *********************************** FINALIZACION **********************************************
// ? *********************************** ASIGNAR CENTRO COSTO **************************************
$('#a_centro').select2({
    matcher: matchStart
});
$('#a_empleadosCentro').select2({
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
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
            if (data.select.length != 0) {
                var option = "";
                data.select.forEach(element => {
                    option += `<option value="${element.emple_id}" selected="selected">${element.nombre} ${element.apPaterno} ${element.apMaterno} </option>`;
                });
                $('#a_empleadosCentro').append(option);
            }
            if (data.noSelect.length != 0) {
                var optionN = "";
                data.noSelect.forEach(element => {
                    optionN += `<option value="${element.emple_id}">${element.nombre} ${element.apPaterno} ${element.apMaterno} </option>`;
                });
                $('#a_empleadosCentro').append(optionN);
            }
            a_empleadosS = $('#a_empleadosCentro').val();
            if (data.noSelect.length == 0 && data[0].select.length != 0) {
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
    sent = false;
    $('#r_centrocmodal').modal();
    $('#r_rowEmpleado').hide();
}
$('#r_empleadosCentro').select2({
    minimumResultsForSearch: 5,
    allowClear: false,
    closeOnSelect: false
});
// : SWITCH DE POR EMPLEADO
$('#r_switchPorEmpleado').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked) {
        $('#r_rowEmpleado').show();
        $('#r_empleadosCentro').empty();
        empleadosCC();
    } else {
        $('#r_rowEmpleado').hide();
        $('#r_todosEmpleados').prop("checked", false);
        $('#r_empleadosCentro').empty();
    }
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
    var codigo = $('#r_codigo').val();
    var porEmpleado;
    var asistenciaPuerta;
    var modoTareo;
    // : ******************** POR EMPLEADO *********************
    if ($('#r_switchPorEmpleado').is(":checked")) {
        porEmpleado = 1;
    } else {
        porEmpleado = 0;
    }
    // : ******************** ASISTENCIA EN PUERTA **************
    if ($('#r_switchAP').is(":checked")) {
        asistenciaPuerta = 1;
    } else {
        asistenciaPuerta = 0;
    }
    // : ******************** MODO TAREO ************************
    if ($('#r_switchMT').is(":checked")) {
        modoTareo = 1;
    } else {
        modoTareo = 0;
    }
    // : ******************** FINALIZACION **********************
    $.ajax({
        async: false,
        url: "/registrarCentro",
        method: "POST",
        data: {
            descripcion: descripcion,
            empleados: empleados,
            codigo: codigo,
            porEmpleado: porEmpleado,
            asistenciaPuerta: asistenciaPuerta,
            modoTareo: modoTareo
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
            if (data.respuesta == undefined) {
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
            } else {
                sent = false;
                if (data.respuesta == 1) {
                    if (data.campo == 1) {
                        $("#r_descripcion").addClass("borderColor");
                    } else {
                        $("#r_codigo").addClass("borderColor");
                    }
                    $.notifyClose();
                    $.notify(
                        {
                            message: data.mensaje,
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
                    alertify
                        .confirm(data.mensaje, function (
                            e
                        ) {
                            if (e) {
                                recuperarCentro(data.centro.centroC_id);
                            }
                        })
                        .setting({
                            title: "Modificar centro de costo",
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
                }
            }
        },
        error: function () { }
    });
}
// : VALIDACION DE FORMULARIO
$('#formRegistrarCentroC').attr('novalidate', true);
$('#formRegistrarCentroC').submit(function (e) {
    e.preventDefault();
    if ($('#r_descripcion').val() == "" || $('#r_descripcion').val() == null) {
        $.notifyClose();
        $.notify({
            message: '\nIngresar un centro de costo',
            icon: 'landing/images/bell.svg',
        }, {
            element: $("#r_centrocmodal"),
            position: "fixed",
            icon_type: 'image',
            placement: {
                from: "top",
                align: "center",
            },
            allow_dismiss: true,
            newest_on_top: true,
            delay: 6000,
            template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
        return;
    }
    if ($('#r_switchPorEmpleado').is(":checked")) {
        if ($('#r_empleadosCentro').val().length == 0) {
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar empleado',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#r_centrocmodal"),
                position: "fixed",
                icon_type: 'image',
                placement: {
                    from: "top",
                    align: "center",
                },
                allow_dismiss: true,
                newest_on_top: true,
                delay: 6000,
                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
            return;
        }
    }
    if (!sent) {
        sent = true;
        this.submit();
    }
});
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
// : REMOVER CLASE
$("#r_descripcion").keyup(function () {
    $(this).removeClass("borderColor");
});
$('#r_codigo').keyup(function () {
    $(this).removeClass("borderColor");
});
// : FUNCION LIMPIAR
function limpiarCentro() {
    $('#r_descripcion').val("");
    $('#r_codigo').val("");
    $('#r_empleadosCentro').empty();
    $('#r_switchPorEmpleado').prop("checked", false);
    $('#r_rowEmpleado').hide();
}
//: TODOS LOS EMPLEADOS EN EDITAR
$('#r_todosEmpleados').click(function () {
    if ($(this).is(':checked')) {
        $("#r_empleadosCentro > option").prop("selected", "selected");
        $('#r_empleadosCentro').trigger("change");
    } else {
        $('#r_empleadosCentro').val("").trigger('change');
    }
});
//: SELECT DE EMPLEADOS
$("#r_empleadosCentro").on("change", function (e) {
    if ($("#r_empleadosCentro").select2('data').length === $("#r_empleadosCentro >option").length) {
        $('#r_todosEmpleados').prop("checked", true);
    } else {
        $('#r_todosEmpleados').prop("checked", false);
    }
});
// ? *********************************** FINALIZACION **********************************************
// ? *********************************** FORMULARIO DE ELIMINAR ************************************
function eliminarCentro(id) {
    alertify
        .confirm("¿Desea eliminar centro costo?", function (
            e
        ) {
            if (e) {
                $.ajax({
                    type: "POST",
                    url: "/eliminarCentro",
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
                        if (data == 0) {
                            $.notifyClose();
                            $.notify({
                                message: '\nCentro Costo en uso, no se puede eliminar.',
                                icon: '/landing/images/alert1.svg',
                            }, {
                                icon_type: 'image',
                                allow_dismiss: true,
                                newest_on_top: true,
                                delay: 6000,
                                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                                    '</div>',
                                spacing: 35
                            });
                        } else {
                            centroCostoOrganizacion();
                            $.notifyClose();
                            $.notify({
                                message: '\nCentro Costo eliminado',
                                icon: 'landing/images/bell.svg',
                            }, {
                                icon_type: 'image',
                                allow_dismiss: true,
                                newest_on_top: true,
                                delay: 6000,
                                template: '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #f2dede;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                                    '</div>',
                                spacing: 35
                            });
                        }
                    },
                    error: function () { },
                });
            }
        })
        .setting({
            title: "Eliminar Centro Costo",
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
                centroCostoOrganizacion();
            },
        });
}
// ? *********************************** FINALIZACION **********************************************
$(function () {
    $(window).on('resize', function () {
        $("#centroC").css('width', '100%');
        table.draw(true);
    });
});