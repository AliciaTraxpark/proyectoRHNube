$.fn.select2.defaults.set('language', 'es');
var table = {};
var sent = false;
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
    $(window).on('resize', function () {
        $('#puntosC').css('width', '100%');
        table.draw(true);
    });
}
// ! *************************************************** CARGAR DATOS DE TABLA ****************************************************
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
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
        },
        success: function (data) {
            $('#puntoOrganizacion').empty();
            if (data.length != 0) {
                var bodyTabla = $('#puntoOrganizacion');
                var tbody = "";
                for (let index = 0; index < data.length; index++) {
                    tbody += `<tr onclick="return cambiarEstadoPunto('${data[index].id}')">
                                <td>${(index + 1)}</td>
                                <td>${data[index].descripcion}</td>
                                <td>${data[index].codigoP}</td>
                                <td>${data[index].descripcionPcd}</td>`;
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
                                        <label class="custom-control-label" for="switchPuntoAPOrg${data[index].id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    } else {
                        tbody += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchPuntoAPOrg${data[index].id}">
                                        <label class="custom-control-label" for="switchPuntoAPOrg${data[index].id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    }

                    // * MODO TAREO
                    if (data[index].ModoTareo == 1) {
                        tbody += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchPuntoMTOrg${data[index].id}" checked>
                                        <label class="custom-control-label" for="switchPuntoMTOrg${data[index].id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    } else {
                        tbody += `<td class="text-center">
                                    <div class="custom-control custom-switch mb-2">
                                        <input type="checkbox" class="custom-control-input" id="switchPuntoMTOrg${data[index].id}">
                                        <label class="custom-control-label" for="switchPuntoMTOrg${data[index].id}" style="font-weight: bold"></label>
                                    </div>
                                </td>`;
                    }

                    tbody += `<td class="text-center">
                                <a onclick="javascript:editarPunto(${data[index].id})" style="cursor: pointer">
                                    <img src="/admin/images/edit.svg" height="15">
                                </a>
                                &nbsp;&nbsp;&nbsp;
                                <a onclick="javascript:eliminarPunto(${data[index].id})" style="cursor: pointer">
                                    <img src="/admin/images/delete.svg" height="15">
                                </a>
                            </td>`;
                    tbody += `</tr>`;
                }
                bodyTabla.append(tbody);
                tablaPuntos();
                $(window).on('resize', function () {
                    $('#puntosC').css('width', '100%');
                    table.draw(true);
                });
            } else {
                tablaPuntos();
            }
        },
        error: function () { }
    });
}
puntosControlOrganizacion();
// ! ************************************************* FORMULARIO DE EDITAR *******************************************************
var empleadosSelectEdit;
var areasSelectEdit;
$('#e_empleadosPunto').select2({
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
$('#e_areasPunto').select2({
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
$("#e_codigoPunto").keyup(function () {
    $(this).removeClass("borderColor");
});
function editarPunto(id) {
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
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
        },
        success: function (data) {
            $('#e_idPuntoC').val(data[0].id);
            $('#e_descripcionPunto').val(data[0].descripcion);
            if (data[0].codigoControl == null) {
                $('#e_codigoPunto').val("");
                $('#e_codigoPunto').prop("disabled", false);
            } else {
                $('#e_codigoPunto').val(data[0].codigoControl);
                $('#e_codigoPunto').prop("disabled", true);
            }
            //* CONTROL RUTA
            if (data[0].controlRuta == 1) {
                $('#e_puntoCRT').prop("checked", true);
                //* MOSTRAR ASIGNACION DE EMPLEADOS Y AREAS
                $('.rowEmpleadosEditar').show();
                $('.rowAreasEditar').show();
                $('#e_cardEA').show();
            } else {
                $('#e_puntoCRT').prop("checked", false);
                //* OCULTAR ASIGNACION DE EMPLEADOS Y AREAS
                $('.rowEmpleadosEditar').hide();
                $('.rowAreasEditar').hide();
                $('#e_cardEA').hide();
            }
            //* ASISTENCIA EN PUERTA
            if (data[0].asistenciaPuerta == 1) {
                $('#e_puntoAP').prop("checked", true);
            } else {
                $('#e_puntoAP').prop("checked", false);
            }
            //* MODO TAREO
            if (data[0].ModoTareo == 1) {
                $('#e_modoT').prop("checked", true);
            } else {
                $('#e_modoT').prop("checked", false);
            }
            // * POR EMPLEADOS
            if (data[0].porEmpleados == 1) {
                $('#e_puntosPorE').prop("checked", true);
                $('.colxEmpleados').show();
                $('.colxAreas').hide();
                empleadosPuntos(data[0].id);
                empleadosSelectEdit = $('#e_empleadosPunto').val();
            } else {
                $('#e_puntosPorE').prop("checked", false);
                $('.colxEmpleados').hide();
            }
            // * POR AREAS
            if (data[0].porAreas == 1) {
                $('#e_puntosPorA').prop("checked", true);
                $('.colxEmpleados').hide();
                $('.colxAreas').show();
                areasPuntos(data[0].id);
                areasSelectEdit = $('#e_areasPunto').val();
            } else {
                $('#e_puntosPorA').prop("checked", false);
                $('.colxAreas').hide();
            }
            // * VERIFICACION
            if (data[0].verificacion == 1) {
                $('#e_verificacion').prop("checked", true);
            } else {
                $('#e_verificacion').prop("checked", false);
            }
            // * GEOLOCALIZACIÓN
            $('#e_rowGeo').empty();
            var geo = data[0].geo;
            var colGeo = "";
            inicialiarMap(geo);
            // *GEOLICALIZACION
            for (let index = 0; index < geo.length; index++) {
                colGeo += `<div class="col-lg-12" id="colGeo${geo[index].idGeo}">
                            <div class="row">
                                <input type="hidden" class="rowIdGeo" value="${geo[index].idGeo}">
                                <div class="col-md-12">
                                    <span id="validGeo${geo[index].idGeo}" style="color: red;display:none"></span>
                                    <div class="card border"
                                        style="border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                                        <div class="card-header" style="padding: 0.25rem 1.25rem;">
                                            <span style="font-weight: bold;">Datos GPS</span>
                                            &nbsp;`;
                colGeo += `<a class="mr-1" onclick="javascript:eliminarGeo(${geo[index].idGeo})" style="cursor: pointer" data-toggle="tooltip"
                                data-placement="right" title="Eliminar GPS" data-original-title="Eliminar GPS">
                                <img src="/admin/images/delete.svg" height="13">
                            </a>`;
                colGeo += `<img class="float-right" src="/landing/images/chevron-arrow-down.svg" height="13" onclick="toggleBody(${geo[index].idGeo})"
                            style="cursor: pointer;">
                            </div>
                            <div class="card-body" style="padding:0.3rem" id="bodyGPS${geo[index].idGeo}">
                                <div class="col-md-12">
                                    <div class="form-group row" style="margin-bottom: 0.4rem;">
                                        <label class="col-lg-4 col-form-label">Latitud:</label>
                                        <input type="number" step="any" class="form-control form-control-sm col-6" id="e_latitud${geo[index].idGeo}"
                                            value="${geo[index].latitud}" onkeyup="javascript:changeLatitud(${geo[index].idGeo})">
                                        <a onclick="javascript:blurLatitud(${geo[index].idGeo})" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaLat${geo[index].idGeo}"
                                            data-toggle="tooltip" data-placement="right" title="Cambiar latitud" data-original-title="Cambiar latitud">
                                            <img src="admin/images/checkH.svg" height="15">
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row" style="margin-bottom: 0.4rem;">
                                        <label class="col-lg-4 col-form-label">Longitud:</label>
                                        <input type="number" step="any" class="form-control form-control-sm col-6" id="e_longitud${geo[index].idGeo}"
                                            value="${geo[index].longitud}" onkeyup="javascript:changeLongitud(${geo[index].idGeo})">
                                        <a onclick="javascript:blurLongitud(${geo[index].idGeo})" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaLng${geo[index].idGeo}"
                                            data-toggle="tooltip" data-placement="right" title="Cambiar longitud" data-original-title="Cambiar longitud">
                                            <img src="admin/images/checkH.svg" height="15">
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row" style="margin-bottom: 0.4rem;">
                                        <label class="col-lg-4 col-form-label">Radio (m):</label>
                                        <input type="number" class="form-control form-control-sm col-6" id="e_radio${geo[index].idGeo}"
                                            value="${geo[index].radio}" onkeyup="javascript:changeRadio(${geo[index].idGeo})">
                                        <a onclick="javascript:blurRadio(${geo[index].idGeo})" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaR${geo[index].idGeo}"
                                            data-toggle="tooltip" data-placement="right" title="Cambiar radio" data-original-title="Cambiar radio">
                                            <img src="admin/images/checkH.svg" height="15">
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row" style="margin-bottom: 0.4rem;">
                                        <label class="col-lg-4 col-form-label">Color:</label>
                                        <input type="color" class="form-control form-control-sm col-6" id="e_color${geo[index].idGeo}"
                                            value="${geo[index].color}" onchange="javascript:changeColor(${geo[index].idGeo})">
                                        <a onclick="javascript:blurColor(${geo[index].idGeo})" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaC${geo[index].idGeo}"
                                            data-toggle="tooltip" data-placement="right" title="Cambiar color" data-original-title="Cambiar color">
                                            <img src="admin/images/checkH.svg" height="15">
                                        </a>
                                    </div>
                                </div>
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>`;
            }
            $('#e_rowGeo').append(colGeo);
            // * DETALLES
            var det = data[0].detalles;
            $('#e_colDescipciones').empty();
            $('#e_colDescipciones').hide();
            var nuevoInput = "";
            for (let item = 0; item < det.length; item++) {
                nuevoInput += `<div class="form-group row" style="margin-bottom: 0.4rem;margin-left: 0.1rem">
                                    <input type="hidden" class="e_colD" value="${det[item].idDetalle}">
                                    <input type="text" class="form-control form-control-sm col-6 e_inp${det[item].idDetalle}" id="e_nuevaD${det[item].idDetalle}" maxlength="50"
                                        placeholder="Nueva Descripcion" value="${det[item].detalle}">
                                    <a onclick="javascript:e_eliminarI(${det[item].idDetalle})" style="cursor: pointer;" class="col-2 pt-1 e_inp${det[item].idDetalle}" id="e_cambiaC"
                                        data-toggle="tooltip" data-placement="right" title="Eliminar descripcion" data-original-title="Eliminar descripcion">
                                            <img src="/admin/images/delete.svg" height="13">
                                    </a>
                                </div>`;
                $('#e_colDescipciones').show();
                if (det.length < 3) {
                    $('#e_agregarD').show();
                } else {
                    $('#e_agregarD').hide();
                }
            }
            $('#e_colDescipciones').append(nuevoInput);
            $('[data-toggle="tooltip"]').tooltip();
        },
        error: function () { }
    });
    $('#modaleditarPuntoControl').modal();
    contenido();
    sent = false;
}
//* CHANGE DE SWITCH DE CONTROLES
$('#e_puntoCRT').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        $('.rowEmpleadosEditar').show();
        $('.rowAreasEditar').show();
        $('#e_cardEA').show();
    } else {
        $('.rowEmpleadosEditar').hide();
        $('.rowAreasEditar').hide();
        $('#e_cardEA').hide();
    }
});
//* SELECT DE EMPLEADOS
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
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
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
// * SELECT DE AREAS
function areasPuntos(id) {
    $('#e_areasPunto').empty();
    $.ajax({
        async: false,
        url: "/puntoControlxAreas",
        method: "POST",
        data: {
            idPunto: id
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
            var itemsSelect = "";
            //* EMPLEADOS SELECCIONADOS
            for (let index = 0; index < data[0].select.length; index++) {
                itemsSelect += `<option value="${data[0].select[index].area_id}" selected="selected"> Área: ${data[0].select[index].area_descripcion}</option>`;
            }
            //* EMPLEADOS NO SELECCIONADOS
            for (let item = 0; item < data[0].noSelect.length; item++) {
                itemsSelect += `<option value="${data[0].noSelect[item].area_id}"> Área: ${data[0].noSelect[item].area_descripcion}</option>`;
            }
            $('#e_areasPunto').append(itemsSelect);
            if (data[0].select.length != 0 && data[0].noSelect.length == 0) {
                $('#e_todasAreas').prop("checked", true);
            } else {
                $('#e_todasAreas').prop("checked", false);
            }
        },
        error: function () { }
    });
}
//* EDITAR PUNTO DE CONTROL
function editarPuntoControl() {
    var idPunto = $('#e_idPuntoC').val();
    var puntoControl = $('#e_descripcionPunto').val();
    var codigo = $('#e_codigoPunto').val();
    var empleados = $('#e_empleadosPunto').val();
    var areas = $('#e_areasPunto').val();
    var porEmpleados;
    var porAreas;
    var controlRuta;
    var asistenciaPuerta;
    var modoTareo;
    var verificacion;
    var puntosGeo = contenido();
    var descripciones = contenidoDes();
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
    //* MODO TAREO
    if ($('#e_modoT').is(":checked")) {
        modoTareo = 1;
    } else {
        modoTareo = 0;
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
    // * VERIFICACION
    if ($('#e_verificacion').is(":checked")) {
        verificacion = 1;
    } else {
        verificacion = 0;
    }
    $.ajax({
        async: false,
        type: "POST",
        url: "/editPuntoControl",
        data: {
            id: idPunto,
            puntoControl: puntoControl,
            cr: controlRuta,
            ap: asistenciaPuerta,
            codigo: codigo,
            empleados: empleados,
            areas: areas,
            porEmpleados: porEmpleados,
            porAreas: porAreas,
            puntosGeo: puntosGeo,
            verificacion: verificacion,
            descripciones: descripciones,
            modoTareo: modoTareo
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
            if (data != 0) {
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
                $('#modaleditarPuntoControl').modal("toggle");
                $('button[type="submit"]').attr("disabled", false);
            } else {
                $('button[type="submit"]').attr("disabled", false);
                sent = false;
                $("#e_codigoPunto").addClass("borderColor");
                $.notifyClose();
                $.notify(
                    {
                        message:
                            "\nYa existe un punto de control con este codigo.",
                        icon: "admin/images/warning.svg",
                    },
                    {
                        element: $('#modaleditarPuntoControl'),
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
        },
        error: function () { }
    });
}
function limpiarPuntoEnEditar() {
    $('#e_idPuntoC').val("");
    $('#e_codigoPunto').val("");
}
//* SELECT DE EMPLEADOS EN EDITAR
$("#e_empleadosPunto").on("change", function (e) {
    if ($("#e_empleadosPunto").select2('data').length === $("#e_empleadosPunto >option").length) {
        $('#e_todosEmpleados').prop("checked", true);
    } else {
        $('#e_todosEmpleados').prop("checked", false);
    }
});
//* SELECT DE AREAS EN EDITAR
$('#e_areasPunto').on("change", function (e) {
    if ($('#e_areasPunto').select2('data').length === $("#e_areasPunto >option").length) {
        $('#e_todasAreas').prop("checked", true);
    } else {
        $('#e_todasAreas').prop("checked", false);
    }
});
//* FUNCION PARA LIMPIAR POR AREAS
function limpiarxArea() {
    $('.colxAreas').hide();
    $('#e_puntosPorA').prop("checked", false);
    $('#e_todasAreas').prop("checked", false);
    $('#e_areasPunto').empty();
}
//* FUNCION PARA LIMPIAR POR EMPLEADO
function limpiarxEmpleado() {
    $('.colxEmpleados').hide();
    $('#e_puntosPorE').prop("checked", false);
    $('#e_todosEmpleados').prop("checked", false);
    $('#e_empleadosPunto').empty();
}
//* SWITCH DE SELECCIONAR POR EMPLEADO
$('#e_puntosPorE').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        limpiarxArea();
        $('.colxEmpleados').show();
        var id = $('#e_idPuntoC').val();
        empleadosPuntos(id);
    } else {
        $('.colxEmpleados').hide();
        limpiarxEmpleado();
    }
});
//* SWITCH DE SELECCIONAR POR ÁREAS
$('#e_puntosPorA').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        limpiarxEmpleado();
        $('.colxAreas').show();
        var id = $('#e_idPuntoC').val();
        areasPuntos(id);
    } else {
        limpiarxArea();
        $('.colxAreas').hide();
    }
});
//* VALIDACIONES EN EDITAR
$('#FormEditarPuntoControl').attr('novalidate', true);
$('#FormEditarPuntoControl').submit(function (e) {
    e.preventDefault();
    if ($('#e_descripcionPunto').val() == "") {
        sent = false;
        $.notifyClose();
        $.notify({
            message: '\nIngresar descripcion Punto control.',
            icon: 'landing/images/bell.svg',
        }, {
            element: $("#modaleditarPuntoControl"),
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
        $('button[type="submit"]').attr("disabled", false);
        return;
    }
    if ($('#e_puntosPorE').is(":checked")) {
        if ($('#e_empleadosPunto').val().length == 0) {
            sent = false;
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar empleados.',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#modaleditarPuntoControl"),
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
            $('button[type="submit"]').attr("disabled", false);
            return;
        }
    }
    if ($('#e_puntosPorA').is(":checked")) {
        if ($('#e_areasPunto').val().length == 0) {
            sent = false;
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar áreas.',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#modaleditarPuntoControl"),
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
            $('button[type="submit"]').attr("disabled", false);
            return;
        }
    }
    if ($('#e_verificacion').is(":checked")) {
        var respuesta = true;
        $('.rowIdGeo').each(function () {
            var idG = $(this).val();
            if ($('#e_latitud' + idG).val() != "") {
                respuesta = false;
            }
        });
        if (respuesta) {
            sent = false;
            $.notifyClose();
            $.notify({
                message: '\nAgregar una Geolocalización como mínimo.',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#modaleditarPuntoControl"),
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
            $('button[type="submit"]').attr("disabled", false);
            return;
        }
    }
    if (!sent) {
        sent = true;
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});
//* CHECKBOX DE TODOS LOS EMPLEADOS
$('#e_todosEmpleados').click(function () {
    if ($(this).is(':checked')) {
        $("#e_empleadosPunto > option").prop("selected", "selected");
        $('#e_empleadosPunto').trigger("change");
    } else {
        $('#e_empleadosPunto').val(empleadosSelectEdit).trigger('change');
    }
});
//* CHECKBOX DE TODAS LAS AREAS
$('#e_todasAreas').click(function () {
    if ($(this).is(':checked')) {
        $("#e_areasPunto > option").prop("selected", "selected");
        $('#e_areasPunto').trigger("change");
    } else {
        $('#e_areasPunto').val(areasSelectEdit).trigger('change');
    }
});
//* INICIALIZAR PLUGIN DE MAPA
var layerGroup = new L.layerGroup();
var circle = {};
var mapId = {};
function inicialiarMap(geo) {
    if (mapId.options != undefined) {
        layerGroup.eachLayer(function (layer) { layerGroup.removeLayer(layer); });
        mapId.remove();
        $('#mapid').html("");
        $('#e_colMapa').empty();
        var agregaridMapa = `<div id="mapid"></div>`;
        $('#e_colMapa').append(agregaridMapa);
    }
    mapId = new L.map('mapid', {
        center: [-9.189967, -75.015152],
        zoom: 10
    });
    mapId.invalidateSize();
    window.setTimeout(function () {
        mapId.invalidateSize();
    }, 1000);
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png').addTo(mapId);
    var arrayMarkerBounds = [];
    // ? RECORRER ARRAYS DE GEOLICALIZACION
    for (let index = 0; index < geo.length; index++) {
        var latlng = new L.latLng(geo[index].latitud, geo[index].longitud);
        // * POSICION DE POPUP
        var ecm = new L.editableCircleMarker(latlng, geo[index].radio, {
            color: geo[index].color,
            fillColor: geo[index].color,
            fillOpacity: 0.5,
            idCircle: geo[index].idGeo,
            metric: false,
            draggable: true
        })
            .on('move', function (e) {
                $('#e_latitud' + e.target.options.idCircle).val(parseFloat(e.latlng.lat).toFixed(8));
                $('#e_longitud' + e.target.options.idCircle).val(parseFloat(e.latlng.lng).toFixed(8));
            });
        layerGroup.addLayer(ecm);
        layerGroup.addTo(mapId);
        var markerBounds = new L.latLngBounds([latlng]);
        arrayMarkerBounds.push(markerBounds);
    }
    if (arrayMarkerBounds.length != 0) {
        // * POSICIONES PARA CENTRAR MAPA
        mapId.fitBounds(arrayMarkerBounds);
    }
    mapId.setZoom(1); //: -> ZOOM COMPLETO
    mapId.on("click", addMarker);
}
// * TOGGLE DE BODY
function e_toggleEA() {
    $('#e_bodyEA').toggle();
}
function e_toggleG() {
    $('#e_bodyG').toggle();
    if (mapId.options != undefined) {
        mapId.invalidateSize();
    }
}
e_toggleG();
// * BOTON DE ACEPTAR CAMBIOS
function changeLatitud(id) {
    $('#e_cambiaLat' + id).show();
}
function changeLongitud(id) {
    $('#e_cambiaLng' + id).show();
}
function changeRadio(id) {
    $('#e_cambiaR' + id).show();
}
function changeColor(id) {
    $('#e_cambiaC' + id).show();
}
// * CAMBIAR POSICIONES EN MAPA
function blurLatitud(id) {
    if ($('#e_latitud' + id).val() != "" && $('#e_longitud' + id).val() != "") {
        layerGroup.eachLayer(function (layer) {
            if (layer.options.idCircle == id) {
                var latlngActualizado = new L.latLng($('#e_latitud' + id).val(), $('#e_longitud' + id).val());
                layer.setLatLng(latlngActualizado);
            }
        });
        $('#e_cambiaLat' + id).hide();
        $('#validGeo' + id).empty();
        $('#validGeo' + id).hide();
    } else {
        if ($('#e_longitud' + id).val() == "" && $('#e_latitud' + id).val() == "") {
            $('#validGeo' + id).empty();
            var spanValid = `* Latitud y Longitud son obligatorios`;
            $('#validGeo' + id).append(spanValid);
            $('#validGeo' + id).show();
        } else {
            if ($('#e_longitud' + id).val() == "") {
                $('#validGeo' + id).empty();
                var spanValid = `* Longitud es obligatorio`;
                $('#validGeo' + id).append(spanValid);
                $('#validGeo' + id).show();
            } else {
                $('#validGeo' + id).empty();
                var spanValid = `* Latitud es obligatorio`;
                $('#validGeo' + id).append(spanValid);
                $('#validGeo' + id).show();
            }
        }
    }
}
function blurLongitud(id) {
    if ($('#e_latitud' + id).val() != "" && $('#e_longitud' + id).val() != "") {
        layerGroup.eachLayer(function (layer) {
            if (layer.options.idCircle == id) {
                var latlngActualizado = new L.latLng($('#e_latitud' + id).val(), $('#e_longitud' + id).val());
                layer.setLatLng(latlngActualizado);
            }
        });
        $('#e_cambiaLng' + id).hide();
        $('#validGeo' + id).empty();
        $('#validGeo' + id).hide();
    } else {
        if ($('#e_longitud' + id).val() == "" && $('#e_latitud' + id).val() == "") {
            $('#validGeo' + id).empty();
            var spanValid = `* Latitud y Longitud son obligatorios`;
            $('#validGeo' + id).append(spanValid);
            $('#validGeo' + id).show();
        } else {
            if ($('#e_longitud' + id).val() == "") {
                $('#validGeo' + id).empty();
                var spanValid = `* Longitud es obligatorio`;
                $('#validGeo' + id).append(spanValid);
                $('#validGeo' + id).show();
            } else {
                $('#validGeo' + id).empty();
                var spanValid = `* Latitud es obligatorio`;
                $('#validGeo' + id).append(spanValid);
                $('#validGeo' + id).show();
            }
        }
    }
}
function blurRadio(id) {
    if ($('#e_radio' + id).val() > 5) {
        layerGroup.eachLayer(function (layer) {
            if (layer.options.idCircle == id) {
                var radioActualizado = parseInt($('#e_radio' + id).val());
                layer.setRadius(radioActualizado);
            }
        });
        $('#e_cambiaR' + id).hide();
        $('#validGeo' + id).empty();
        $('#validGeo' + id).hide();
    } else {
        $('#validGeo' + id).empty();
        var spanValid = `* Radio debe ser mayor  5 metros`;
        $('#validGeo' + id).append(spanValid);
        $('#validGeo' + id).show();
    }
}
function blurColor(id) {
    layerGroup.eachLayer(function (layer) {
        if (layer.options.idCircle == id) {
            var colorActualizado = $('#e_color' + id).val();
            layer.setCircleStyle({
                color: colorActualizado,
                fillColor: colorActualizado
            });
        }
    });
    $('#e_cambiaC' + id).hide();
}
// * OBTENER DIVS DE GEO
function contenido() {
    var resultado = [];
    $('.rowIdGeo').each(function () {
        var idG = $(this).val();
        var latitudG = $('#e_latitud' + idG).val();
        var longitudG = $('#e_longitud' + idG).val();
        var radioG = $('#e_radio' + idG).val();
        var colorG = $('#e_color' + idG).val();
        var objGeo = { "idGeo": $(this).val(), "latitud": latitudG, "longitud": longitudG, "radio": radioG, "color": colorG };
        resultado.push(objGeo);
    });
    return resultado;
}
// * ELIMINAR GEO
function eliminarGeo(id) {
    $('#colGeo' + id).hide();
    layerGroup.eachLayer(function (layer) {
        if (layer.options.idCircle == id) {
            layerGroup.removeLayer(layer);
        }
    });
    $('#e_latitud' + id).val("");
    $('#e_radio' + id).val("");
    $('#e_longitud' + id).val("");
    $('#e_color' + id).val("");
    $('#colEliminar' + id).hide();
}
// * AGREGAR GPS MODAL
function e_agregarGPS() {
    $('#modaleditarPuntoControl').modal('hide');
    $('#modalGpsEditar').modal();
}
// * LIMPIAR AGREGAR GPS
function e_limpiarGPS() {
    $('#e_gpsLatitud').val("");
    $('#e_gpsLongitud').val("");
    $('#e_gpsRadio').val("");
    $('#modaleditarPuntoControl').modal('show');
}
var variableU = 1;
// * AGREGAR GPS
function edit_agregarGPS() {
    var container = $('#e_rowGeo');
    var latitud = parseFloat($('#e_gpsLatitud').val()).toFixed(8);
    var longitud = parseFloat($('#e_gpsLongitud').val()).toFixed(8);
    var radio = $('#e_gpsRadio').val();
    var color = $('#e_gpsColor').val();
    colGeo = `<div class="col-lg-12" id="colGeoNuevo${variableU}">
                <div class="row">
                    <input type="hidden" class="rowIdGeo" value="Nuevo${variableU}">
                    <div class="col-md-12">
                        <span id="validGeoNuevo${variableU}" style="color: red;display:none"></span>
                        <div class="card border"
                            style="border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                            <div class="card-header" style="padding: 0.25rem 1.25rem;">
                                <span style="font-weight: bold;">Datos GPS</span>
                                &nbsp;`;
    colGeo += `<a class="mr-1" onclick="javascript:eliminarGeo('Nuevo${variableU}')" style="cursor: pointer" data-toggle="tooltip"
                    data-placement="right" title="Eliminar GPS" data-original-title="Eliminar GPS">
                    <img src="/admin/images/delete.svg" height="13">
                </a>`;
    colGeo += `<img class="float-right" src="/landing/images/chevron-arrow-down.svg" height="13" onclick="toggleBody('Nuevo${variableU}')"
                    style="cursor: pointer;">
                </div>
                <div class="card-body" style="padding:0.3rem" id="bodyGPSNuevo${variableU}">
                    <div class="col-md-12">
                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                            <label class="col-lg-4 col-form-label">Latitud:</label>
                            <input type="number" step="any" class="form-control form-control-sm col-6" id="e_latitudNuevo${variableU}"
                                value="${latitud}" onkeyup="javascript:changeLatitud('Nuevo${variableU}')">
                            <a onclick="javascript:blurLatitud('Nuevo${variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaLatNuevo${variableU}"
                                data-toggle="tooltip" data-placement="right" title="Cambiar latitud" data-original-title="Cambiar latitud">
                                <img src="admin/images/checkH.svg" height="15">
                            </a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                            <label class="col-lg-4 col-form-label">Longitud:</label>
                            <input type="number" step="any" class="form-control form-control-sm col-6" id="e_longitudNuevo${variableU}"
                                value="${longitud}" onkeyup="javascript:changeLongitud('Nuevo${variableU}')">
                                <a onclick="javascript:blurLongitud('Nuevo${variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaLngNuevo${variableU}"
                                    data-toggle="tooltip" data-placement="right" title="Cambiar longitud" data-original-title="Cambiar longitud">
                                    <img src="admin/images/checkH.svg" height="15">
                                </a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                            <label class="col-lg-4 col-form-label">Radio (m):</label>
                            <input type="number" class="form-control form-control-sm col-6" id="e_radioNuevo${variableU}"
                                value="${radio}" onkeyup="javascript:changeRadio('Nuevo${variableU}')">
                                <a onclick="javascript:blurRadio('Nuevo${variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaRNuevo${variableU}"
                                    data-toggle="tooltip" data-placement="right" title="Cambiar radio" data-original-title="Cambiar radio">
                                    <img src="admin/images/checkH.svg" height="15">
                                </a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                            <label class="col-lg-4 col-form-label">Color:</label>
                            <input type="color" class="form-control form-control-sm col-6" id="e_colorNuevo${variableU}"
                                value="${color}" onchange="javascript:changeColor('Nuevo${variableU}')">
                            <a onclick="javascript:blurColor('Nuevo${variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaCNuevo${variableU}"
                                data-toggle="tooltip" data-placement="right" title="Cambiar color" data-original-title="Cambiar color">
                                <img src="admin/images/checkH.svg" height="15">
                            </a>
                        </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>`;
    container.append(colGeo);
    $('#modalGpsEditar').modal('toggle');
    e_limpiarGPS();
    $('#modaleditarPuntoControl').modal('show');
    $('[data-toggle="tooltip"]').tooltip();
    var arrayMarkerBounds = [];
    var nuevoLat = parseFloat($('#e_latitudNuevo' + variableU).val()).toFixed(8);
    var nuevoLng = parseFloat($('#e_longitudNuevo' + variableU).val()).toFixed(8);
    var nuevoRadio = parseInt($('#e_radioNuevo' + variableU).val());
    var nuevoColor = $('#e_colorNuevo' + variableU).val()
    var latlng = new L.latLng(nuevoLat, nuevoLng);
    var idNuevo = 'Nuevo' + variableU;
    // * POSICION DE POPUP
    var nuevo = new L.editableCircleMarker(latlng, nuevoRadio, {
        color: nuevoColor,
        fillColor: nuevoColor,
        fillOpacity: 0.5,
        idCircle: idNuevo,
        metric: false,
        draggable: true
    })
        .on('move', function (e) {
            $('#e_latitud' + e.target.options.idCircle).val(parseFloat(e.latlng.lat).toFixed(8));
            $('#e_longitud' + e.target.options.idCircle).val(parseFloat(e.latlng.lng).toFixed(8));
        });
    layerGroup.addLayer(nuevo);
    layerGroup.addTo(mapId);
    layerGroup.eachLayer(function (layer) {
        var nuevoLatLng = layer.getLatLng()
        var markerBounds = new L.latLngBounds([nuevoLatLng]);
        arrayMarkerBounds.push(markerBounds);
    });
    mapId.fitBounds(arrayMarkerBounds);
    mapId.setZoom(1); //: -> ZOOM COMPLETO
    variableU++;
}
// * AGREGAR GPS EN MAPA
function addMarker(e) {
    alertify
        .confirm("¿Desea agregar nuevo GPS?", function (
            event
        ) {
            if (event) {
                var idNuevo = 'Nuevo' + variableU;
                var nuevoLatitud;
                var nuevoLongitud;
                var nuevoxMapa = new L.editableCircleMarker(e.latlng, 100, {
                    color: '#FF0000',
                    fillColor: '#FF0000',
                    fillOpacity: 0.5,
                    idCircle: idNuevo,
                    metric: false,
                    draggable: true
                })
                    .on('move', function (e) {
                        $('#e_latitud' + e.target.options.idCircle).val(parseFloat(e.latlng.lat).toFixed(8));
                        $('#e_longitud' + e.target.options.idCircle).val(parseFloat(e.latlng.lng).toFixed(8));
                    });
                nuevoLatitud = parseFloat(e.latlng.lat).toFixed(8);
                nuevoLongitud = parseFloat(e.latlng.lng).toFixed(8);
                layerGroup.addLayer(nuevoxMapa);
                layerGroup.addTo(mapId);
                var arrayMarkerBounds = [];
                layerGroup.eachLayer(function (layer) {
                    var nuevoLatLng = layer.getLatLng()
                    var markerBounds = new L.latLngBounds([nuevoLatLng]);
                    arrayMarkerBounds.push(markerBounds);
                });
                mapId.fitBounds(arrayMarkerBounds);
                mapId.setZoom(1); //: -> ZOOM COMPLETO
                var container = $('#e_rowGeo');
                colGeo = `<div class="col-lg-12" id="colGeoNuevo${variableU}">
                                <div class="row">
                                    <input type="hidden" class="rowIdGeo" value="Nuevo${variableU}">
                                    <div class="col-md-12">
                                        <span id="validGeoNuevo${variableU}" style="color: red;display:none"></span>
                                        <div class="card border"
                                            style="border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                                            <div class="card-header" style="padding: 0.25rem 1.25rem;">
                                                <span style="font-weight: bold;">Datos GPS</span>
                                                &nbsp;`;
                colGeo += `<a class="mr-1" onclick="javascript:eliminarGeo('Nuevo${variableU}')" style="cursor: pointer" data-toggle="tooltip"
                                    data-placement="right" title="Eliminar GPS" data-original-title="Eliminar GPS">
                                    <img src="/admin/images/delete.svg" height="13">
                                </a>`;
                colGeo += `<img class="float-right" src="/landing/images/chevron-arrow-down.svg" height="13" onclick="toggleBody('Nuevo${variableU}')"
                                    style="cursor: pointer;">
                                </div>
                                <div class="card-body" style="padding:0.3rem" id="bodyGPSNuevo${variableU}">
                                    <div class="col-md-12">
                                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                                            <label class="col-lg-4 col-form-label">Latitud:</label>
                                            <input type="number" step="any" class="form-control form-control-sm col-6" id="e_latitudNuevo${variableU}"
                                                value="${nuevoLatitud}" onkeyup="javascript:changeLatitud('Nuevo${variableU}')">
                                            <a onclick="javascript:blurLatitud('Nuevo${variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaLatNuevo${variableU}"
                                                data-toggle="tooltip" data-placement="right" title="Cambiar latitud" data-original-title="Cambiar latitud">
                                                <img src="admin/images/checkH.svg" height="15">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                                            <label class="col-lg-4 col-form-label">Longitud:</label>
                                            <input type="number" step="any" class="form-control form-control-sm col-6" id="e_longitudNuevo${variableU}"
                                                value="${nuevoLongitud}" onkeyup="javascript:changeLongitud('Nuevo${variableU}')">
                                                <a onclick="javascript:blurLongitud('Nuevo${variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaLngNuevo${variableU}"
                                                    data-toggle="tooltip" data-placement="right" title="Cambiar longitud" data-original-title="Cambiar longitud">
                                                    <img src="admin/images/checkH.svg" height="15">
                                                </a>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                                            <label class="col-lg-4 col-form-label">Radio (m):</label>
                                            <input type="number" class="form-control form-control-sm col-6" id="e_radioNuevo${variableU}"
                                                value="100" onkeyup="javascript:changeRadio('Nuevo${variableU}')">
                                                <a onclick="javascript:blurRadio('Nuevo${variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaRNuevo${variableU}"
                                                    data-toggle="tooltip" data-placement="right" title="Cambiar radio" data-original-title="Cambiar radio">
                                                    <img src="admin/images/checkH.svg" height="15">
                                                </a>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                                            <label class="col-lg-4 col-form-label">Color:</label>
                                            <input type="color" class="form-control form-control-sm col-6" id="e_colorNuevo${variableU}"
                                                value="#FF0000" onchange="javascript:changeColor('Nuevo${variableU}')">
                                            <a onclick="javascript:blurColor('Nuevo${variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaCNuevo${variableU}"
                                                data-toggle="tooltip" data-placement="right" title="Cambiar color" data-original-title="Cambiar color">
                                                <img src="admin/images/checkH.svg" height="15">
                                            </a>
                                        </div>
                                    </div>
                                    </div>
                                    </div>
                                    </div>
                                    </div>
                                    </div>
                                    </div>`;
                container.append(colGeo);
                $('[data-toggle="tooltip"]').tooltip();
                variableU++;
            }
        })
        .setting({
            title: "Nuevo GPS",
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
// * TOGGLE BODY
function toggleBody(id) {
    $('#bodyGPS' + id).toggle();
}
// * NUEVAS DESCRIPCIONES
var contarInput = 0;
function e_nuevaDesc() {
    var estadoInput = true;
    $('.e_colD').each(function () {
        var idI = $(this).val();
        if ($('#e_nuevaD' + idI).val() == "" && $('#e_nuevaD' + idI).is(":visible")) {
            estadoInput = false;
            $('#e_nuevaD' + idI).addClass("borderColor");
        } else {
            $('#e_nuevaD' + idI).removeClass("borderColor");
        }
    });
    if (estadoInput) {
        var nuevoInput = `<div class="form-group row" style="margin-bottom: 0.4rem;margin-left: 0.1rem">
                        <input type="hidden" class="e_colD" value="New${contarInput}">
                        <input type="text" class="form-control form-control-sm col-6 e_inpNew${contarInput}" id="e_nuevaDNew${contarInput}" maxlength="50" placeholder="Nueva Descripcion">
                        <a onclick="javascript:e_eliminarI('New${contarInput}')" style="cursor: pointer;" class="col-2 pt-1 e_inpNew${contarInput}" id="e_cambiaC"
                            data-toggle="tooltip" data-placement="right" title="Eliminar descripcion" data-original-title="Eliminar descripcion">
                                <img src="/admin/images/delete.svg" height="13">
                        </a>
                    </div>`;
        $('#e_colDescipciones').append(nuevoInput);
    }
    $('#e_colDescipciones').show();
    var contarEstado = 0;
    $('.e_colD').each(function () {
        var idI = $(this).val();
        if ($('#e_nuevaD' + idI).is(":visible")) {
            contarEstado = contarEstado + 1;
        }
    });
    if (contarEstado < 3) {
        $('#e_agregarD').show();
    } else {
        $('#e_agregarD').hide();
    }
    contarInput = contarInput + 1;
}
// * CONTENIDO DE NUEVAS DESCRIPCIONES
function contenidoDes() {
    var resultado = [];
    $('.e_colD').each(function () {
        var idI = $(this).val();
        var descripcionI = $('#e_nuevaD' + idI).val();
        var objInput = { "id": $(this).val(), "descripcion": descripcionI };
        resultado.push(objInput);
    });

    return resultado;
}
// * ELIMINAR INPUT
function e_eliminarI(id) {
    $('#e_nuevaD' + id).val("");
    $('.e_inp' + id).hide();
    // ? CONTAR INPUTS VISIBLES
    var contarEstado = 0;
    $('.e_colD').each(function () {
        var idI = $(this).val();
        if ($('#e_nuevaD' + idI).val() == "" && $('#e_nuevaD' + idI).is(":visible")) {
            contarEstado = contarEstado + 1;
        }
    });
    if (contarEstado < 3) {
        $('#e_agregarD').show();
    } else {
        $('#e_agregarD').hide();
    }
}
// ! ************************************************************** FINALIZACION **********************************************
// ! ************************************************************** FORMULARIO DE ASIGNAR *************************************
var a_empleadosSelectEdit;
var a_areasSelectEdit;
$('#a_punto').select2({
    matcher: matchStart
});
$('#a_empleadosPunto').select2({
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
$('#a_areasPunto').select2({
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
function listaPuntos() {
    $('#a_punto').empty();
    var container = $('#a_punto');
    $.ajax({
        async: false,
        url: "/listaPunto",
        method: "GET",
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
            var option = `<option value="" disabled selected>Seleccionar</option>`;
            data.forEach(element => {
                option += `<option value="${element.idPunto}"> Punto Control : ${element.descripcion} </option>`;
            });
            container.append(option);
        },
        error: function () { },
    });
}
//: FUNCION PARA LIMPIAR POR EMPLEADO
function a_limpiarxEmpleado() {
    $('.colxEmpleados').hide();
    $('#a_puntosPorE').prop("checked", false);
    $('#a_todosEmpleados').prop("checked", false);
    $('#a_empleadosPunto').empty();
}
//: FUNCION PARA LIMPIAR POR AREAS
function a_limpiarxArea() {
    $('.colxAreas').hide();
    $('#a_puntosPorA').prop("checked", false);
    $('#a_todasAreas').prop("checked", false);
    $('#a_areasPunto').empty();
}
function asignacionPunto() {
    $('#modalAsignacionPunto').modal();
    a_limpiarxArea();
    a_limpiarxEmpleado();
    $('#a_puntosPorE').attr("disabled", true);
    $('#a_puntosPorA').attr("disabled", true);
    listaPuntos();
    sent = false;
}
//: POR EMPLEADOS
function a_empleadosPuntos(id) {
    $('#a_empleadosPunto').empty();
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
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
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
            $('#a_empleadosPunto').append(itemsSelect);
            if (data[0].select.length != 0 && data[0].noSelect.length == 0) {
                $('#a_todosEmpleados').prop("checked", true);
            } else {
                $('#a_todosEmpleados').prop("checked", false);
            }
        },
        error: function () { }
    });
}
//: POR ARES
function a_areasPuntos(id) {
    $('#a_areasPunto').empty();
    $.ajax({
        async: false,
        url: "/puntoControlxAreas",
        method: "POST",
        data: {
            idPunto: id
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
            var itemsSelect = "";
            //* EMPLEADOS SELECCIONADOS
            for (let index = 0; index < data[0].select.length; index++) {
                itemsSelect += `<option value="${data[0].select[index].area_id}" selected="selected"> Área: ${data[0].select[index].area_descripcion}</option>`;
            }
            //* EMPLEADOS NO SELECCIONADOS
            for (let item = 0; item < data[0].noSelect.length; item++) {
                itemsSelect += `<option value="${data[0].noSelect[item].area_id}"> Área: ${data[0].noSelect[item].area_descripcion}</option>`;
            }
            $('#a_areasPunto').append(itemsSelect);
            if (data[0].select.length != 0 && data[0].noSelect.length == 0) {
                $('#a_todasAreas').prop("checked", true);
            } else {
                $('#a_todasAreas').prop("checked", false);
            }
        },
        error: function () { }
    });
}
//: FUNCION DE CHANGE
$('#a_punto').on("change", function () {
    //: ACTIVAR FORMULARIO
    $('#a_puntosPorE').attr("disabled", false);
    $('#a_puntosPorA').attr("disabled", false);
    //: ******************************************
    var idP = $(this).val();
    $.ajax({
        async: false,
        url: "/datosPuntoC",
        method: "POST",
        data: {
            idP: idP
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
            if (data.porEmpleados == 1) {
                a_empleadosPuntos(data.id);
                $('.colxEmpleados').show();
                $('#a_puntosPorE').prop("checked", true);
                a_empleadosSelectEdit = $('#a_empleadosPunto').val();
            } else {
                a_limpiarxEmpleado();
            }
            if (data.porAreas == 1) {
                a_areasPuntos(data.id);
                $('.colxAreas').show();
                $('#a_puntosPorA').prop("checked", true);
                a_areasSelectEdit = $('#a_areasPunto').val();
            } else {
                a_limpiarxArea();
            }
        },
        error: function () { },
    });
});
//: SWITCH DE SELECCIONAR POR EMPLEADO
$('#a_puntosPorE').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        a_limpiarxArea();
        $('.colxEmpleados').show();
        var id = $('#a_punto').val();
        a_empleadosPuntos(id);
    } else {
        $('.colxEmpleados').hide();
        a_limpiarxEmpleado();
    }
});
//* SWITCH DE SELECCIONAR POR ÁREAS
$('#a_puntosPorA').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        a_limpiarxEmpleado();
        $('.colxAreas').show();
        var id = $('#a_punto').val();
        a_areasPuntos(id);
    } else {
        a_limpiarxArea();
        $('.colxAreas').hide();
    }
});
function limpiarAsignacion() {
    a_limpiarxArea();
    a_limpiarxEmpleado();
    $('#a_puntosPorE').attr("disabled", true);
    $('#a_puntosPorA').attr("disabled", true);
}
//: VALIDACIONES EN ASIGNAR
$('#FormAsignarPuntoControl').attr('novalidate', true);
$('#FormAsignarPuntoControl').submit(function (e) {
    e.preventDefault();
    if ($('#a_punto').val() == "" || $('#a_punto').val() == null) {
        $.notifyClose();
        $.notify({
            message: '\nSeleccionar punto control.',
            icon: 'landing/images/bell.svg',
        }, {
            element: $("#modalAsignacionPunto"),
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
        $('button[type="submit"]').attr("disabled", false);
        return;
    }
    if ($('#a_puntosPorE').is(":checked")) {
        if ($('#a_empleadosPunto').val().length == 0) {
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar empleados.',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#modalAsignacionPunto"),
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
            $('button[type="submit"]').attr("disabled", false);
            return;
        }
    }
    if ($('#a_puntosPorA').is(":checked")) {
        if ($('#a_areasPunto').val().length == 0) {
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar áreas.',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#modalAsignacionPunto"),
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
            $('button[type="submit"]').attr("disabled", false);
            return;
        }
    }
    if (!sent) {
        sent = true;
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});
function asignarPunto() {
    var empleados = $('#a_empleadosPunto').val();
    var punto = $('#a_punto').val();
    var areas = $('#a_areasPunto').val();
    var porEmpleados;
    var porAreas;
    if ($('#a_puntosPorE').is(":checked") == true) {
        porEmpleados = 1;
    } else {
        porEmpleados = 0;
    }
    if ($('#a_puntosPorA').is(":checked") == true) {
        porAreas = 1;
    } else {
        porAreas = 0;
    }
    $.ajax({
        async: false,
        url: "/asignacionPunto",
        method: "POST",
        data: {
            empleados: empleados,
            idPunto: punto,
            areas: areas,
            porAreas: porAreas,
            porEmpleados: porEmpleados
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
            $('#modalAsignacionPunto').modal('toggle');
            $('button[type="submit"]').attr("disabled", false);
            limpiarAsignacion();
            //: ************************************************
            $.notifyClose();
            $.notify(
                {
                    message: "\nAsignación exitosa.",
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
        },
        error: function () { },
    });
}
//: CHECKBOX DE TODOS LOS EMPLEADOS
$('#a_todosEmpleados').click(function () {
    if ($(this).is(':checked')) {
        $("#a_empleadosPunto > option").prop("selected", "selected");
        $('#a_empleadosPunto').trigger("change");
    } else {
        $('#a_empleadosPunto').val(a_empleadosSelectEdit).trigger('change');
    }
});
//: CHECKBOX DE TODAS LAS AREAS
$('#a_todasAreas').click(function () {
    if ($(this).is(':checked')) {
        $("#a_areasPunto > option").prop("selected", "selected");
        $('#a_areasPunto').trigger("change");
    } else {
        $('#a_areasPunto').val(a_areasSelectEdit).trigger('change');
    }
});
//: SELECT DE EMPLEADOS EN EDITAR
$("#a_empleadosPunto").on("change", function (e) {
    if ($("#a_empleadosPunto").select2('data').length === $("#a_empleadosPunto >option").length) {
        $('#a_todosEmpleados').prop("checked", true);
    } else {
        $('#a_todosEmpleados').prop("checked", false);
    }
});
//: SELECT DE AREAS EN EDITAR
$('#a_areasPunto').on("change", function (e) {
    if ($('#a_areasPunto').select2('data').length === $("#a_areasPunto >option").length) {
        $('#a_todasAreas').prop("checked", true);
    } else {
        $('#a_todasAreas').prop("checked", false);
    }
});
// ! *********************************************************** FINALIZACION *************************************************
// ! *********************************************************** FORMULARIO REGISTRAR *****************************************
$('#r_empleadosPunto').select2({
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
$('#r_areasPunto').select2({
    minimumResultsForSearch: 5,
    closeOnSelect: false,
    allowClear: false
});
var r_layerGroup = new L.layerGroup();
var r_circle = {};
var map = {};
function modalRegistrar() {
    $('#modalRegistrarPunto').modal();
    r_limpiarxEmpleado();
    r_limpiarxArea();
    $('#r_cardEA').hide();
    r_inicialiarMap();
    sent = false;
}
function r_inicialiarMap() {
    if (map.options != undefined) {
        r_layerGroup.eachLayer(function (layer) { layerGroup.removeLayer(layer); });
        map.remove();
        $('#map').html("");
        $('#r_colMapa').empty();
        var agregaridMapa = `<div id="map"></div>`;
        $('#r_colMapa').append(agregaridMapa);
    }
    map = new L.map('map', {
        center: [-9.189967, -75.015152],
        zoom: 9
    });
    map.invalidateSize();
    window.setTimeout(function () {
        map.invalidateSize();
    }, 1000);
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png').addTo(map);
    map.on("click", r_addMarker);
}
//? FUNCION PARA LIMPIAR POR EMPLEADO
function r_limpiarxEmpleado() {
    $('.colxEmpleados').hide();
    $('#r_puntosPorE').prop("checked", false);
    $('#r_todosEmpleados').prop("checked", false);
    $('#r_empleadosPunto').empty();
}
//? FUNCION PARA LIMPIAR POR AREAS
function r_limpiarxArea() {
    $('.colxAreas').hide();
    $('#r_puntosPorA').prop("checked", false);
    $('#r_todasAreas').prop("checked", false);
    $('#r_areasPunto').empty();
}
//? CHANGE DE SWITCH DE CONTROLES
$('#r_puntoCRT').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        $('.rowEmpleadosEditar').show();
        $('.rowAreasEditar').show();
        $('#r_cardEA').show();
    } else {
        $('.rowEmpleadosEditar').hide();
        $('.rowAreasEditar').hide();
        $('#r_cardEA').hide();
        r_limpiarxArea();
        r_limpiarxEmpleado();
    }
});
//? CHANGE DE SWITCH POR EMPLEADOS
$('#r_puntosPorE').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        $('.colxAreas').hide();
        $('.colxEmpleados').show();
        $('#r_puntosPorA').prop("checked", false);
        $('#r_areasPunto').empty();
        r_empleadosPuntos();
    } else {
        $('.colxEmpleados').hide();
        $('#r_puntosPorE').prop("checked", false);
        $('#r_empleadosPunto').empty();
        r_limpiarxEmpleado();
    }
});
//? CHANGE DE SWITCH POR AREAS
$('#r_puntosPorA').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        $('.colxEmpleados').hide();
        $('.colxAreas').show();
        $('#r_puntosPorE').prop("checked", false);
        $('#r_empleadosPunto').empty();
        r_areasPuntos();
    } else {
        $('.colxAreas').hide();
        $('#r_puntosPorA').prop("checked", false);
        $('#r_areasPunto').empty();
        r_limpiarxEmpleado();
    }
});
//? POR EMPLEADOS
function r_empleadosPuntos() {
    $('#r_empleadosPunto').empty();
    $.ajax({
        async: false,
        url: "/puntoEmpleado",
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
            var itemsSelect = "";
            for (let index = 0; index < data.length; index++) {
                itemsSelect += `<option value="${data[index].emple_id}">${data[index].nombre} ${data[index].apPaterno} ${data[index].apMaterno}</option>`;
            }
            $('#r_empleadosPunto').append(itemsSelect);
        },
        error: function () { }
    });
}
//? POR ARES
function r_areasPuntos() {
    $('#r_areasPunto').empty();
    $.ajax({
        async: false,
        url: "/puntoArea",
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
            var itemsSelect = "";
            //* EMPLEADOS SELECCIONADOS
            for (let index = 0; index < data.length; index++) {
                itemsSelect += `<option value="${data[index].area_id}"> Área: ${data[index].area_descripcion}</option>`;
            }
            $('#r_areasPunto').append(itemsSelect);
        },
        error: function () { }
    });
}
//? CHECKBOX DE TODOS LOS EMPLEADOS
$('#r_todosEmpleados').click(function () {
    if ($(this).is(':checked')) {
        $("#r_empleadosPunto > option").prop("selected", "selected");
        $('#r_empleadosPunto').trigger("change");
    } else {
        $('#r_empleadosPunto').val("").trigger('change');
    }
});
//? CHECKBOX DE TODAS LAS AREAS
$('#r_todasAreas').click(function () {
    if ($(this).is(':checked')) {
        $("#r_areasPunto > option").prop("selected", "selected");
        $('#r_areasPunto').trigger("change");
    } else {
        $('#r_areasPunto').val("").trigger('change');
    }
});
//? SELECT DE EMPLEADOS EN EDITAR
$("#r_empleadosPunto").on("change", function (e) {
    if ($("#r_empleadosPunto").select2('data').length === $("#r_empleadosPunto >option").length) {
        $('#r_todosEmpleados').prop("checked", true);
    } else {
        $('#r_todosEmpleados').prop("checked", false);
    }
});
//? SELECT DE AREAS EN EDITAR
$('#r_areasPunto').on("change", function (e) {
    if ($('#r_areasPunto').select2('data').length === $("#r_areasPunto >option").length) {
        $('#r_todasAreas').prop("checked", true);
    } else {
        $('#r_todasAreas').prop("checked", false);
    }
});
// ? TOGGLE EMPLEADO Y AREA
function r_toggleEA() {
    $('#r_bodyEA').toggle();
}
// ? TOGGLE GPS
function r_toggleG() {
    $('#r_bodyG').toggle();
    if (map.options != undefined) {
        map.invalidateSize();
    }
}
// ? NUEVOS INPUTS
var r_contarInput = 0;
function r_nuevaDesc() {
    var estadoInput = true;
    $('.r_colD').each(function () {
        var idI = $(this).val();
        if ($('#r_nuevaD' + idI).val() == "" && $('#r_nuevaD' + idI).is(":visible")) {
            estadoInput = false;
            $('#r_nuevaD' + idI).addClass("borderColor");
        } else {
            $('#r_nuevaD' + idI).removeClass("borderColor");
        }
    });
    if (estadoInput) {
        var nuevoInput = `<div class="form-group row" style="margin-bottom: 0.4rem;margin-left: 0.1rem">
                        <input type="hidden" class="r_colD" value="New${r_contarInput}">
                        <input type="text" class="form-control form-control-sm col-6 r_inpNew${r_contarInput}" id="r_nuevaDNew${r_contarInput}" maxlength="50" placeholder="Nueva Descripcion">
                        <a onclick="javascript:r_eliminarI('New${r_contarInput}')" style="cursor: pointer;" class="col-2 pt-1 r_inpNew${r_contarInput}" id="r_cambiaC"
                            data-toggle="tooltip" data-placement="right" title="Eliminar descripcion" data-original-title="Eliminar descripcion">
                                <img src="/admin/images/delete.svg" height="13">
                        </a>
                    </div>`;
        $('#r_colDescripciones').append(nuevoInput);
    }
    $('#r_colDescripciones').show();
    var contarEstado = 0;
    $('.r_colD').each(function () {
        var idI = $(this).val();
        if ($('#r_nuevaD' + idI).is(":visible")) {
            contarEstado = contarEstado + 1;
        }
    });
    if (contarEstado < 3) {
        $('#r_agregarD').show();
    } else {
        $('#r_agregarD').hide();
    }
    r_contarInput = r_contarInput + 1;
}
// ? ELIMINAR INPUTS
function r_eliminarI(id) {
    $('#r_nuevaD' + id).val("");
    $('.r_inp' + id).hide();
    // ? CONTAR INPUTS VISIBLES
    var contarEstado = 0;
    $('.r_colD').each(function () {
        var idI = $(this).val();
        if ($('#r_nuevaD' + idI).val() == "" && $('#r_nuevaD' + idI).is(":visible")) {
            contarEstado = contarEstado + 1;
        }
    });
    if (contarEstado < 3) {
        $('#r_agregarD').show();
    } else {
        $('#r_agregarD').hide();
    }
}
// ? MODAL AGREGAR GPS
function r_agregarGPS() {
    $('#modalRegistrarPunto').modal('hide');
    $('#modalGps').modal();
}
// ? AGREGAR GPS
var reg_variableU = 1;
function reg_agregarGPS() {
    var container = $('#r_rowGeo');
    var latitud = parseFloat($('#r_gpsLatitud').val()).toFixed(8);
    var longitud = parseFloat($('#r_gpsLongitud').val()).toFixed(8);
    var radio = $('#r_gpsRadio').val();
    var color = $('#r_gpsColor').val();
    colGeo = `<div class="col-lg-12" id="r_colGeoNuevo${reg_variableU}">
                <div class="row">
                    <input type="hidden" class="r_rowIdGeo" value="Nuevo${reg_variableU}">
                    <div class="col-md-12">
                        <span id="r_validGeoNuevo${reg_variableU}" style="color: red;display:none"></span>
                        <div class="card border"
                            style="border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                            <div class="card-header" style="padding: 0.25rem 1.25rem;">
                                <span style="font-weight: bold;">Datos GPS</span>
                                &nbsp;`;
    colGeo += `<a class="mr-1" onclick="javascript:r_eliminarGeo('Nuevo${reg_variableU}')" style="cursor: pointer" data-toggle="tooltip"
                    data-placement="right" title="Eliminar GPS" data-original-title="Eliminar GPS">
                    <img src="/admin/images/delete.svg" height="13">
                </a>`;
    colGeo += `<img class="float-right" src="/landing/images/chevron-arrow-down.svg" height="13" onclick="r_toggleBody('Nuevo${reg_variableU}')"
                    style="cursor: pointer;">
                </div>
                <div class="card-body" style="padding:0.3rem" id="r_bodyGPSNuevo${reg_variableU}">
                    <div class="col-md-12">
                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                            <label class="col-lg-4 col-form-label">Latitud:</label>
                            <input type="number" step="any" class="form-control form-control-sm col-6" id="r_latitudNuevo${reg_variableU}"
                                value="${latitud}" onkeyup="javascript:r_changeLatitud('Nuevo${reg_variableU}')">
                            <a onclick="javascript:r_blurLatitud('Nuevo${reg_variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="r_cambiaLatNuevo${reg_variableU}"
                                data-toggle="tooltip" data-placement="right" title="Cambiar latitud" data-original-title="Cambiar latitud">
                                <img src="admin/images/checkH.svg" height="15">
                            </a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                            <label class="col-lg-4 col-form-label">Longitud:</label>
                            <input type="number" step="any" class="form-control form-control-sm col-6" id="r_longitudNuevo${reg_variableU}"
                                value="${longitud}" onkeyup="javascript:r_changeLongitud('Nuevo${reg_variableU}')">
                                <a onclick="javascript:r_blurLongitud('Nuevo${reg_variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="r_cambiaLngNuevo${reg_variableU}"
                                    data-toggle="tooltip" data-placement="right" title="Cambiar longitud" data-original-title="Cambiar longitud">
                                    <img src="admin/images/checkH.svg" height="15">
                                </a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                            <label class="col-lg-4 col-form-label">Radio (m):</label>
                            <input type="number" class="form-control form-control-sm col-6" id="r_radioNuevo${reg_variableU}"
                                value="${radio}" onkeyup="javascript:r_changeRadio('Nuevo${reg_variableU}')">
                                <a onclick="javascript:r_blurRadio('Nuevo${reg_variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="r_cambiaRNuevo${reg_variableU}"
                                    data-toggle="tooltip" data-placement="right" title="Cambiar radio" data-original-title="Cambiar radio">
                                    <img src="admin/images/checkH.svg" height="15">
                                </a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                            <label class="col-lg-4 col-form-label">Color:</label>
                            <input type="color" class="form-control form-control-sm col-6" id="r_colorNuevo${reg_variableU}"
                                value="${color}" onchange="javascript:r_changeColor('Nuevo${reg_variableU}')">
                            <a onclick="javascript:r_blurColor('Nuevo${reg_variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="r_cambiaCNuevo${reg_variableU}"
                                data-toggle="tooltip" data-placement="right" title="Cambiar color" data-original-title="Cambiar color">
                                <img src="admin/images/checkH.svg" height="15">
                            </a>
                        </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>`;
    container.append(colGeo);
    $('#modalGps').modal('toggle');
    r_limpiarGPS();
    $('#modalRegistrarPunto').modal('show');
    $('[data-toggle="tooltip"]').tooltip();
    var arrayMarkerBounds = [];
    var nuevoLat = parseFloat($('#r_latitudNuevo' + reg_variableU).val()).toFixed(8);
    var nuevoLng = parseFloat($('#r_longitudNuevo' + reg_variableU).val()).toFixed(8);
    var nuevoRadio = parseInt($('#r_radioNuevo' + reg_variableU).val());
    var nuevoColor = $('#r_colorNuevo' + reg_variableU).val()
    var latlng = new L.latLng(nuevoLat, nuevoLng);
    var idNuevo = 'Nuevo' + reg_variableU;
    // * POSICION DE POPUP
    var r_nuevo = new L.editableCircleMarker(latlng, nuevoRadio, {
        color: nuevoColor,
        fillColor: nuevoColor,
        fillOpacity: 0.5,
        idCircle: idNuevo,
        metric: false,
        draggable: true
    })
        .on('move', function (e) {
            $('#r_latitud' + e.target.options.idCircle).val(parseFloat(e.latlng.lat).toFixed(8));
            $('#r_longitud' + e.target.options.idCircle).val(parseFloat(e.latlng.lng).toFixed(8));
        });
    r_layerGroup.addLayer(r_nuevo);
    r_layerGroup.addTo(map);
    r_layerGroup.eachLayer(function (layer) {
        var nuevoLatLng = layer.getLatLng()
        var markerBounds = new L.latLngBounds([nuevoLatLng]);
        arrayMarkerBounds.push(markerBounds);
    });
    map.fitBounds(arrayMarkerBounds);
    map.setZoom(1); //: -> ZOOM COMPLETO
    reg_variableU++;
}
// ? AGREGAR GPS EN MAPA
function r_addMarker(e) {
    alertify
        .confirm("¿Desea agregar nuevo GPS?", function (
            event
        ) {
            if (event) {
                var idNuevo = 'Nuevo' + reg_variableU;
                var nuevoLatitud;
                var nuevoLongitud;
                var r_nuevoxMapa = new L.editableCircleMarker(e.latlng, 100, {
                    color: '#FF0000',
                    fillColor: '#FF0000',
                    fillOpacity: 0.5,
                    idCircle: idNuevo,
                    metric: false,
                    draggable: true
                })
                    .on('move', function (e) {
                        $('#r_latitud' + e.target.options.idCircle).val(parseFloat(e.latlng.lat).toFixed(8));
                        $('#r_longitud' + e.target.options.idCircle).val(parseFloat(e.latlng.lng).toFixed(8));
                    });
                nuevoLatitud = parseFloat(e.latlng.lat).toFixed(8);
                nuevoLongitud = parseFloat(e.latlng.lng).toFixed(8);
                r_layerGroup.addLayer(r_nuevoxMapa);
                r_layerGroup.addTo(map);
                var arrayMarkerBounds = [];
                r_layerGroup.eachLayer(function (layer) {
                    var nuevoLatLng = layer.getLatLng()
                    var markerBounds = new L.latLngBounds([nuevoLatLng]);
                    arrayMarkerBounds.push(markerBounds);
                });
                map.fitBounds(arrayMarkerBounds);
                map.setZoom(1); //: -> ZOOM COMPLETO
                var container = $('#r_rowGeo');
                colGeo = `<div class="col-lg-12" id="r_colGeoNuevo${reg_variableU}">
                                <div class="row">
                                    <input type="hidden" class="r_rowIdGeo" value="Nuevo${reg_variableU}">
                                    <div class="col-md-12">
                                        <span id="r_validGeoNuevo${reg_variableU}" style="color: red;display:none"></span>
                                        <div class="card border"
                                            style="border-color: #e4e9f0;box-shadow: 0 4px 10px 0 rgba(20, 19, 34, 0.03), 0 0 10px 0 rgba(20, 19, 34, 0.02);">
                                            <div class="card-header" style="padding: 0.25rem 1.25rem;">
                                                <span style="font-weight: bold;">Datos GPS</span>
                                                &nbsp;`;
                colGeo += `<a class="mr-1" onclick="javascript:r_eliminarGeo('Nuevo${reg_variableU}')" style="cursor: pointer" data-toggle="tooltip"
                                    data-placement="right" title="Eliminar GPS" data-original-title="Eliminar GPS">
                                    <img src="/admin/images/delete.svg" height="13">
                                </a>`;
                colGeo += `<img class="float-right" src="/landing/images/chevron-arrow-down.svg" height="13" onclick="r_toggleBody('Nuevo${reg_variableU}')"
                                    style="cursor: pointer;">
                                </div>
                                <div class="card-body" style="padding:0.3rem" id="r_bodyGPSNuevo${reg_variableU}">
                                    <div class="col-md-12">
                                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                                            <label class="col-lg-4 col-form-label">Latitud:</label>
                                            <input type="number" step="any" class="form-control form-control-sm col-6" id="r_latitudNuevo${reg_variableU}"
                                                value="${nuevoLatitud}" onkeyup="javascript:r_changeLatitud('Nuevo${reg_variableU}')">
                                            <a onclick="javascript:r_blurLatitud('Nuevo${reg_variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="r_cambiaLatNuevo${reg_variableU}"
                                                data-toggle="tooltip" data-placement="right" title="Cambiar latitud" data-original-title="Cambiar latitud">
                                                <img src="admin/images/checkH.svg" height="15">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                                            <label class="col-lg-4 col-form-label">Longitud:</label>
                                            <input type="number" step="any" class="form-control form-control-sm col-6" id="r_longitudNuevo${reg_variableU}"
                                                value="${nuevoLongitud}" onkeyup="javascript:r_changeLongitud('Nuevo${reg_variableU}')">
                                                <a onclick="javascript:r_blurLongitud('Nuevo${reg_variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="r_cambiaLngNuevo${reg_variableU}"
                                                    data-toggle="tooltip" data-placement="right" title="Cambiar longitud" data-original-title="Cambiar longitud">
                                                    <img src="admin/images/checkH.svg" height="15">
                                                </a>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                                            <label class="col-lg-4 col-form-label">Radio (m):</label>
                                            <input type="number" class="form-control form-control-sm col-6" id="r_radioNuevo${reg_variableU}"
                                                value="100" onkeyup="javascript:r_changeRadio('Nuevo${reg_variableU}')">
                                                <a onclick="javascript:r_blurRadio('Nuevo${reg_variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="r_cambiaRNuevo${reg_variableU}"
                                                    data-toggle="tooltip" data-placement="right" title="Cambiar radio" data-original-title="Cambiar radio">
                                                    <img src="admin/images/checkH.svg" height="15">
                                                </a>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row" style="margin-bottom: 0.4rem;">
                                            <label class="col-lg-4 col-form-label">Color:</label>
                                            <input type="color" class="form-control form-control-sm col-6" id="r_colorNuevo${reg_variableU}"
                                                value="#FF0000" onchange="javascript:r_changeColor('Nuevo${reg_variableU}')">
                                            <a onclick="javascript:r_blurColor('Nuevo${reg_variableU}')" style="cursor: pointer;display:none" class="col-2 pt-1" id="r_cambiaCNuevo${reg_variableU}"
                                                data-toggle="tooltip" data-placement="right" title="Cambiar color" data-original-title="Cambiar color">
                                                <img src="admin/images/checkH.svg" height="15">
                                            </a>
                                        </div>
                                    </div>
                                    </div>
                                    </div>
                                    </div>
                                    </div>
                                    </div>
                                    </div>`;
                container.append(colGeo);
                $('[data-toggle="tooltip"]').tooltip();
                variableU++;
            }
        })
        .setting({
            title: "Nuevo GPS",
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
// ? ELIMINAR DATOS  GPS DE MODAL
function r_limpiarGPS() {
    $('#r_gpsLatitud').val("");
    $('#r_gpsLongitud').val("");
    $('#r_gpsRadio').val("");
    $('#modalRegistrarPunto').modal('show');
}
// ? ELIMINAR GPS
function r_eliminarGeo(id) {
    $('#r_colGeo' + id).hide();
    r_layerGroup.eachLayer(function (layer) {
        if (layer.options.idCircle == id) {
            r_layerGroup.removeLayer(layer);
        }
    });
    $('#r_latitud' + id).val("");
    $('#r_radio' + id).val("");
    $('#r_longitud' + id).val("");
    $('#r_color' + id).val("");
    $('#r_colEliminar' + id).hide();
}
// ? TOGGLE DE GPS
function r_toggleBody(id) {
    $('#r_bodyGPS' + id).toggle();
}
// ? BOTON DE ACEPTAR CAMBIOS
function r_changeLatitud(id) {
    $('#r_cambiaLat' + id).show();
}
function r_changeLongitud(id) {
    $('#r_cambiaLng' + id).show();
}
function r_changeRadio(id) {
    $('#r_cambiaR' + id).show();
}
function r_changeColor(id) {
    $('#r_cambiaC' + id).show();
}
// ? CAMBIAR POSICIONES EN MAPA
function r_blurLatitud(id) {
    if ($('#r_latitud' + id).val() != "" && $('#r_longitud' + id).val() != "") {
        r_layerGroup.eachLayer(function (layer) {
            if (layer.options.idCircle == id) {
                var latlngActualizado = new L.latLng($('#r_latitud' + id).val(), $('#r_longitud' + id).val());
                layer.setLatLng(latlngActualizado);
            }
        });
        $('#r_cambiaLat' + id).hide();
        $('#r_validGeo' + id).empty();
        $('#r_validGeo' + id).hide();
    } else {
        if ($('#r_longitud' + id).val() == "" && $('#r_latitud' + id).val() == "") {
            $('#r_validGeo' + id).empty();
            var spanValid = `* Latitud y Longitud son obligatorios`;
            $('#r_validGeo' + id).append(spanValid);
            $('#r_validGeo' + id).show();
        } else {
            if ($('#r_longitud' + id).val() == "") {
                $('#r_validGeo' + id).empty();
                var spanValid = `* Longitud es obligatorio`;
                $('#r_validGeo' + id).append(spanValid);
                $('#r_validGeo' + id).show();
            } else {
                $('#r_validGeo' + id).empty();
                var spanValid = `* Latitud es obligatorio`;
                $('#r_validGeo' + id).append(spanValid);
                $('#r_validGeo' + id).show();
            }
        }
    }
}
function r_blurLongitud(id) {
    if ($('#r_latitud' + id).val() != "" && $('#r_longitud' + id).val() != "") {
        r_layerGroup.eachLayer(function (layer) {
            if (layer.options.idCircle == id) {
                var latlngActualizado = new L.latLng($('#r_latitud' + id).val(), $('#r_longitud' + id).val());
                layer.setLatLng(latlngActualizado);
            }
        });
        $('#r_cambiaLng' + id).hide();
        $('#r_validGeo' + id).empty();
        $('#r_validGeo' + id).hide();
    } else {
        if ($('#r_longitud' + id).val() == "" && $('#r_latitud' + id).val() == "") {
            $('#r_validGeo' + id).empty();
            var spanValid = `* Latitud y Longitud son obligatorios`;
            $('#r_validGeo' + id).append(spanValid);
            $('#r_validGeo' + id).show();
        } else {
            if ($('#r_longitud' + id).val() == "") {
                $('#r_validGeo' + id).empty();
                var spanValid = `* Longitud es obligatorio`;
                $('#r_validGeo' + id).append(spanValid);
                $('#r_validGeo' + id).show();
            } else {
                $('#r_validGeo' + id).empty();
                var spanValid = `* Latitud es obligatorio`;
                $('#r_validGeo' + id).append(spanValid);
                $('#r_validGeo' + id).show();
            }
        }
    }
}
function r_blurRadio(id) {
    if ($('#r_radio' + id).val() > 5) {
        r_layerGroup.eachLayer(function (layer) {
            if (layer.options.idCircle == id) {
                var radioActualizado = parseInt($('#r_radio' + id).val());
                layer.setRadius(radioActualizado);
            }
        });
        $('#r_cambiaR' + id).hide();
        $('#r_validGeo' + id).empty();
        $('#r_validGeo' + id).hide();
    } else {
        $('#r_validGeo' + id).empty();
        var spanValid = `* Radio debe ser mayor  5 metros`;
        $('#r_validGeo' + id).append(spanValid);
        $('#r_validGeo' + id).show();
    }
}
function r_blurColor(id) {
    r_layerGroup.eachLayer(function (layer) {
        if (layer.options.idCircle == id) {
            var colorActualizado = $('#r_color' + id).val();
            layer.setCircleStyle({
                color: colorActualizado,
                fillColor: colorActualizado
            });
        }
    });
    $('#r_cambiaC' + id).hide();
}
// ? OBTENER DIVS DE GEO
function r_contenido() {
    var resultado = [];
    $('.r_rowIdGeo').each(function () {
        var idG = $(this).val();
        var latitudG = $('#r_latitud' + idG).val();
        var longitudG = $('#r_longitud' + idG).val();
        var radioG = $('#r_radio' + idG).val();
        var colorG = $('#r_color' + idG).val();
        var objGeo = { "idGeo": $(this).val(), "latitud": latitudG, "longitud": longitudG, "radio": radioG, "color": colorG };
        resultado.push(objGeo);
    });
    return resultado;
}
// ? CONTENIDO DE NUEVAS DESCRIPCIONES
function r_contenidoDes() {
    var resultado = [];
    $('.r_colD').each(function () {
        var idI = $(this).val();
        var descripcionI = $('#r_nuevaD' + idI).val();
        var objInput = { "id": $(this).val(), "descripcion": descripcionI };
        resultado.push(objInput);
    });

    return resultado;
}
// ? REGISTRAR NUEVO PUNTO
function registrarPunto() {
    var descripcion = $("#r_descripcionPunto").val();
    var codigo = $("#r_codigoPunto").val();
    var empleados = $('#r_empleadosPunto').val();
    var areas = $('#r_areasPunto').val();
    var porEmpleados;
    var porAreas;
    var controlRuta;
    var asistenciaPuerta;
    var modoTareo;
    var verificacion;
    var puntosGeo = r_contenido();
    var descripciones = r_contenidoDes();
    // * CONTROL EN RUTA
    if ($('#r_puntoCRT').is(":checked")) {
        controlRuta = 1;
    } else {
        controlRuta = 0;
    }
    //* ASISTENCIA EN PUERTA
    if ($('#r_puntoAP').is(":checked")) {
        asistenciaPuerta = 1;
    } else {
        asistenciaPuerta = 0;
    }
    //* MODO TAREO
    if ($('#r_modoT').is(":checked")) {
        modoTareo = 1;
    } else {
        modoTareo = 0;
    }
    //* POR EMPLEADOS
    if ($('#r_puntosPorE').is(":checked")) {
        porEmpleados = 1;
    } else {
        porEmpleados = 0;
    }
    //* POR AREAS
    if ($('#r_puntosPorA').is(":checked")) {
        porAreas = 1;
    } else {
        porAreas = 0;
    }
    // * VERIFICACION
    if ($('#r_verificacion').is(":checked")) {
        verificacion = 1;
    } else {
        verificacion = 0;
    }
    $.ajax({
        type: "POST",
        url: "/registrarPuntoC",
        data: {
            descripcion: descripcion,
            ap: asistenciaPuerta,
            cr: controlRuta,
            codigo: codigo,
            empleados: empleados,
            porEmpleados: porEmpleados,
            areas: areas,
            porAreas: porAreas,
            verificacion: verificacion,
            puntosGeo: puntosGeo,
            descripciones: descripciones,
            modoTareo: modoTareo
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
            if (data.estado === 1) {
                sent = false;
                $('button[type="submit"]').attr("disabled", false);
                if (data.punto.estado == 0) {
                    alertify
                        .confirm("Ya existe un punto de control inactivo con este nombre. ¿Desea recuperarla si o no?", function (
                            e
                        ) {
                            if (e) {
                                recuperarPunto(data.punto.id);
                            }
                        })
                        .setting({
                            title: "Modificar Punto Control",
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
                    $("#r_descripcionPunto").addClass("borderColor");
                    $.notifyClose();
                    $.notify(
                        {
                            message:
                                "\nYa existe una actividad con este nombre.",
                            icon: "admin/images/warning.svg",
                        },
                        {
                            element: $('#modalRegistrarPunto'),
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
            } else {
                if (data.estado === 0) {
                    $('button[type="submit"]').attr("disabled", false);
                    sent = false;
                    if (data.punto.estado == 0) {
                        alertify
                            .confirm("Ya existe un punto control inactivo con este código. ¿Desea recuperarla si o no?", function (
                                e
                            ) {
                                if (e) {
                                    recuperarPunto(data.punto.id);
                                }
                            })
                            .setting({
                                title: "Modificar Punto Control",
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
                        $("#r_codigoPunto").addClass("borderColor");
                        $.notifyClose();
                        $.notify(
                            {
                                message:
                                    "\nYa existe una actividad con este código.",
                                icon: "admin/images/warning.svg",
                            },
                            {
                                element: $('#modalRegistrarPunto'),
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
                } else {
                    limpiarPunto();
                    puntosControlOrganizacion();
                    $('#modalRegistrarPunto').modal('toggle');
                    $('button[type="submit"]').attr("disabled", false);
                    $.notifyClose();
                    $.notify(
                        {
                            message: "\nPunto control registrado.",
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
        error: function () { },
    });
}
// ? RECUPERAR PUNTO
function recuperarPunto(id) {
    $.ajax({
        type: "POST",
        url: "/recuperarPunto",
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
            limpiarPunto();
            puntosControlOrganizacion();
            $('#modalRegistrarPunto').modal('toggle');
            editarPunto(data.id);
        },
        error: function () { },
    });

}
//* VALIDACIONES EN EDITAR
$('#FormPuntoControl').attr('novalidate', true);
$('#FormPuntoControl').submit(function (e) {
    e.preventDefault();
    if ($('#r_verificacion').is(":checked")) {
        var resultado = true;
        $('.r_rowIdGeo').each(function () {
            var idG = $(this).val();
            if ($('#r_latitud' + idG).val() != "") {
                resultado = false;
            }
        });
        if (resultado) {
            sent = false;
            $.notifyClose();
            $.notify({
                message: '\nAgregar una Geolocalización como mínimo.',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#modalRegistrarPunto"),
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
            $('button[type="submit"]').attr("disabled", false);
            return;
        }
    }
    if ($('#r_descripcionPunto').val() == "" || $('#r_descripcionPunto').val() == null) {
        sent = false;
        $.notifyClose();
        $.notify({
            message: '\nIngresar punto control.',
            icon: 'landing/images/bell.svg',
        }, {
            element: $("#modalRegistrarPunto"),
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
        $('button[type="submit"]').attr("disabled", false);
        return;
    }
    if ($('#r_puntosPorE').is(":checked")) {
        if ($('#r_empleadosPunto').val().length == 0) {
            sent = false;
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar empleados.',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#modalRegistrarPunto"),
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
            $('button[type="submit"]').attr("disabled", false);
            return;
        }
    }
    if ($('#r_puntosPorA').is(":checked")) {
        if ($('#r_areasPunto').val().length == 0) {
            sent = false;
            $.notifyClose();
            $.notify({
                message: '\nSeleccionar áreas.',
                icon: 'landing/images/bell.svg',
            }, {
                element: $("#modalRegistrarPunto"),
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
            $('button[type="submit"]').attr("disabled", false);
            return;
        }
    }
    if (!sent) {
        sent = true;
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});
function limpiarPunto() {
    $('#r_descripcionPunto').val("");
    $('#r_codigoPunto').val("");
    $('#r_colDescripciones').empty();
    $('#r_rowGeo').empty();
    r_limpiarxEmpleado();
    r_limpiarxArea();
    $('#r_puntoCRT').prop("checked", false);
    $('#r_puntoAP').prop("checked", false);
    $('#r_modoT').prop("checked", false);
    $('#r_verificacion').prop("checked", false);
    $('#r_cardEA').hide();
}
// ! ****************** FINALIZACION *****************************
// * ELIMINAR PUNTO CONTROL
function eliminarPunto(id) {
    alertify
        .confirm("¿Desea eliminar punto Control?", function (
            e
        ) {
            if (e) {
                $.ajax({
                    type: "POST",
                    url: "/cambiarEstadoP",
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
                        puntosControlOrganizacion();
                        $.notifyClose();
                        $.notify({
                            message: '\nPunto control eliminado',
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
                    },
                    error: function () { },
                });
            }
        })
        .setting({
            title: "Eliminar punto control",
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
            },
        });
}
// * CAMBIAR ESTADOS DE SWITCH
function cambiarEstadoDeControles(id, valor, control) {
    $.ajax({
        type: "POST",
        url: "/cambiarEstadoControlesP",
        data: {
            id: id,
            valor: valor,
            control: control
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
            puntosControlOrganizacion();
            $.notifyClose();
            $.notify(
                {
                    message: "\nPunto control modificado.",
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
        },
        error: function () { },
    });
}

function cambiarEstadoPunto(id) {

    $("#switchPuntoCROrg" + id).on("change.bootstrapSwitch", function (event) {
        var control = "CR";
        if (event.target.checked == true) {
            var valor = 1;
        } else {
            var valor = 0;
        }
        alertify
            .confirm("¿Desea modificar el estado del punto control?", function (
                e
            ) {
                if (e) {
                    cambiarEstadoDeControles(id, valor, control);
                }
            })
            .setting({
                title: "Modificar Punto Control",
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
                    puntosControlOrganizacion();
                },
            });
    });

    $("#switchPuntoAPOrg" + id).on("change.bootstrapSwitch", function (event) {
        var control = "AP";
        if (event.target.checked == true) {
            var valor = 1;
        } else {
            var valor = 0;
        }
        alertify
            .confirm("¿Desea modificar el estado del punto control?", function (
                e
            ) {
                if (e) {
                    cambiarEstadoDeControles(id, valor, control);
                }
            })
            .setting({
                title: "Modificar Punto Control",
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
                    puntosControlOrganizacion();
                },
            });
    });

    /* MODO TAREO SWITCH */
    $("#switchPuntoMTOrg" + id).on("change.bootstrapSwitch", function (event) {
        var control = "MT";
        if (event.target.checked == true) {
            var valor = 1;
        } else {
            var valor = 0;
        }
        alertify
            .confirm("¿Desea modificar el estado del punto control?", function (
                e
            ) {
                if (e) {
                    cambiarEstadoDeControles(id, valor, control);
                }
            })
            .setting({
                title: "Modificar Punto Control",
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
                    puntosControlOrganizacion();
                },
            });
    });
}
$('#r_descripcionPunto').keyup(function () {
    $(this).removeClass("borderColor");
});
$('#r_codigoPunto').keyup(function () {
    $(this).removeClass("borderColor");
});
$(function () {
    $(window).on('resize', function () {
        $('#puntosC').css('width', '100%');
        table.draw(true);
    });
});