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
    $(window).on('resize', function () {
        $('#puntosC').css('width', '100%');
        table.draw(true);
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
                                <a onclick="javascript:editarPunto(${data[index].id})" style="cursor: pointer">
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
var empleadosSelectEdit;
var areasSelectEdit;
$('#e_empleadosPunto').select2({
    tags: "true"
});
$('#e_areasPunto').select2({
    tags: "true"
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
            // * GEOLOCALIZACIÓN
            $('#e_rowGeo').empty();
            var geo = data[0].geo;
            var colGeo = "";
            inicialiarMap(geo);
            for (let index = 0; index < geo.length; index++) {
                colGeo += `<div class="col-md-11" id="colGeo${geo[index].idGeo}">
                            <div class="row">
                            <input type="hidden" class="rowIdGeo" value="${geo[index].idGeo}">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Latitud:</label>
                                        <div class="form-group row pl-2">
                                            <input type="number" step="any" class="form-control form-control-sm col-10" id="e_latitud${geo[index].idGeo}" 
                                                value="${geo[index].latitud}" onkeyup="javascript:changeLatitud(${geo[index].idGeo})">
                                            <a onclick="javascript:blurLatitud(${geo[index].idGeo})" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaLat${geo[index].idGeo}"
                                                data-toggle="tooltip" data-placement="right" title="Cambiar latitud" data-original-title="Cambiar latitud">
                                                <img src="admin/images/checkH.svg" height="15">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Longitud:</label>
                                        <div class="form-group row pl-2">
                                            <input type="number" step="any" class="form-control form-control-sm col-10" id="e_longitud${geo[index].idGeo}" 
                                                value="${geo[index].longitud}" onkeyup="javascript:changeLongitud(${geo[index].idGeo})">
                                            <a onclick="javascript:blurLongitud(${geo[index].idGeo})" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaLng${geo[index].idGeo}"
                                                data-toggle="tooltip" data-placement="right" title="Cambiar longitud" data-original-title="Cambiar longitud">
                                                <img src="admin/images/checkH.svg" height="15">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Radio (m<sup>2</sup>):</label>
                                        <div class="form-group row pl-2">
                                            <input type="number" class="form-control form-control-sm col-10" id="e_radio${geo[index].idGeo}" 
                                                value="${geo[index].radio}" onkeyup="javascript:changeRadio(${geo[index].idGeo})">
                                            <a onclick="javascript:blurRadio(${geo[index].idGeo})" style="cursor: pointer;display:none" class="col-2 pt-1" id="e_cambiaR${geo[index].idGeo}"
                                                data-toggle="tooltip" data-placement="right" title="Cambiar radio" data-original-title="Cambiar radio">
                                                <img src="admin/images/checkH.svg" height="15">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                if (index != 0) {
                    colGeo += `<div class="col-md-1 text-left" style="padding-top:2.5em" id="colEliminar${geo[index].idGeo}">
                                    <a onclick="javascript:eliminarGeo(${geo[index].idGeo})" style="cursor: pointer" data-toggle="tooltip" 
                                        data-placement="right" title="Eliminar GPS" data-original-title="Eliminar GPS">
                                        <img src="/admin/images/delete.svg" height="15">
                                    </a>
                                </div>`;
                }
            }
            $('#e_rowGeo').append(colGeo);
            $('[data-toggle="tooltip"]').tooltip();
        },
        error: function () { }
    });
    $('#modaleditarPuntoControl').modal();
    contenido();
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
    var codigo = $('#e_codigoPunto').val();
    var empleados = $('#e_empleadosPunto').val();
    var areas = $('#e_areasPunto').val();
    var porEmpleados;
    var porAreas;
    var controlRuta;
    var asistenciaPuerta;
    var puntosGeo = contenido();
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
            porAreas: porAreas,
            puntosGeo: puntosGeo
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            } else {
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
    if ($('#e_puntosPorE').is(":checked")) {
        if ($('#e_empleadosPunto').val().length == 0) {
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
            return;
        }
    }
    if ($('#e_puntosPorA').is(":checked")) {
        if ($('#e_areasPunto').val().length == 0) {
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
            return;
        }
    }
    this.submit();
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
        $("#e_todasAreas > option").prop("selected", "selected");
        $('#e_todasAreas').trigger("change");
    } else {
        $('#e_todasAreas').val(areasSelectEdit).trigger('change');
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
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            idCircle: geo[index].idGeo,
            metric: false,
            draggable: true
        })
            .on('move', function (e) {
                $('#e_latitud' + e.target.options.idCircle).val(parseFloat(e.latlng.lat).toFixed(5));
                $('#e_longitud' + e.target.options.idCircle).val(parseFloat(e.latlng.lng).toFixed(5));
            });
        layerGroup.addLayer(ecm);
        layerGroup.addTo(mapId);
        var markerBounds = new L.latLngBounds([latlng]);
        arrayMarkerBounds.push(markerBounds);
    }
    // * POSICIONES PARA CENTRAR MAPA
    mapId.fitBounds(arrayMarkerBounds);
    mapId.setZoom(1); //: -> ZOOM COMPLETO
}
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
// * CAMBIAR POSICIONES EN MAPA
function blurLatitud(id) {
    layerGroup.eachLayer(function (layer) {
        if (layer.options.idCircle == id) {
            var latlngActualizado = new L.latLng($('#e_latitud' + id).val(), $('#e_longitud' + id).val());
            layer.setLatLng(latlngActualizado);
        }
    });
    $('#e_cambiaLat' + id).hide();
}
function blurLongitud(id) {
    layerGroup.eachLayer(function (layer) {
        if (layer.options.idCircle == id) {
            var latlngActualizado = new L.latLng($('#e_latitud' + id).val(), $('#e_longitud' + id).val());
            layer.setLatLng(latlngActualizado);
        }
    });
    $('#e_cambiaLng' + id).hide();
}
function blurRadio(id) {
    layerGroup.eachLayer(function (layer) {
        if (layer.options.idCircle == id) {
            var radioActualizado = parseInt($('#e_radio' + id).val());
            layer.setRadius(radioActualizado);
        }
    });
    $('#e_cambiaR' + id).hide();
}
// * OBTENER DIVS DE GEO
function contenido() {
    var resultado = [];
    $('.rowIdGeo').each(function () {
        console.log($(this).val());
        var idG = $(this).val();
        var latitudG = $('#e_latitud' + idG).val();
        var longitudG = $('#e_longitud' + idG).val();
        var radioG = $('#e_radio' + idG).val();
        var objGeo = { "idGeo": $(this).val(), "latitud": latitudG, "longitud": longitudG, "radio": radioG };
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
    $('#e_latitud' + id).val("")
    $('#e_radio' + id).val("");
    $('#e_longitud' + id).val("")
    $('#colEliminar' + id).hide();
}
// ! ****************** FINALIZACION *****************************
$(function () {
    $(window).on('resize', function () {
        $('#puntosC').css('width', '100%');
        table.draw(true);
    });
});
