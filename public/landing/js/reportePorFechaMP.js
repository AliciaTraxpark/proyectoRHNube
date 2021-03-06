//* FECHA
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "j F",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: false,
    disableMobile: true,
    conjunction: " a ",
    minRange: 1,
    onChange: function (selectedDates) {
        var _this = this;
        var dateArr = selectedDates.map(function (date) { return _this.formatDate(date, 'Y-m-d'); });
        $('#ID_START').val(dateArr[0]);
        $('#ID_END').val(dateArr[1]);
    },
    onClose: function (selectedDates, dateStr, instance) {
        if (selectedDates.length == 1) {
            var fm = moment(selectedDates[0]).add("day", -1).format("YYYY-MM-DD");
            instance.setDate([fm, selectedDates[0]], true);
        }
    }
});
var fechaGlobal = {};
var dataT = {};
var sent = false;
var paginaGlobal = 10;
// * INICIALIZAR TABLA
var table;
function inicializarTabla() {
    table = $("#tablaReport").DataTable({
        "searching": true,
        "scrollX": true,
        "ordering": false,
        "autoWidth": false,
        "lengthChange": true,
        processing: true,
        retrieve: true,
        lengthMenu: [10, 25, 50, 75, 100],
        pageLength: paginaGlobal,
        language: {
            sProcessing: "Generando informe...",
            processing: "<img src='landing/images/logoR.gif' height='180'>\n&nbsp;&nbsp;&nbsp;&nbsp;Generando informe...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando registros del _START_ al _END_ ",
            sInfoEmpty:
                "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "",
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
                pageLength: {
                    "_": "Mostrar %d registros"
                },
            },

        },
        dom: 'Bfrtip',
        lengthMenu: [10, 25, 50, 100],
        buttons: [
            {
                extend: 'pageLength',
                className: 'btn btn-sm mt-1',
            },
            {
                extend: 'excel',
                className: 'btn btn-sm mt-1',
                text: "<i><img src='admin/images/excel.svg' height='20'></i> Descargar",
                customize: function (xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var downrows = 5;
                    var clRow = $('row', sheet);
                    clRow[0].children[0].remove();
                    //update Row
                    clRow.each(function () {
                        var attr = $(this).attr('r');
                        var ind = parseInt(attr);
                        ind = ind + downrows;
                        $(this).attr("r", ind);
                    });

                    // Update  row > c
                    $('row c ', sheet).each(function () {
                        var attr = $(this).attr('r');
                        var pre = attr.substring(0, 1);
                        var ind = parseInt(attr.substring(1, attr.length));
                        ind = ind + downrows;
                        $(this).attr("r", pre + ind);
                    });

                    function Addrow(index, data) {
                        msg = '<row r="' + index + '">'
                        for (i = 0; i < data.length; i++) {
                            var key = data[i].k;
                            var value = data[i].v;
                            var bold = data[i].s;
                            msg += '<c t="inlineStr" r="' + key + index + '" s="' + bold + '" >';
                            msg += '<is>';
                            msg += '<t>' + value + '</t>';
                            msg += '</is>';
                            msg += '</c>';
                        }
                        msg += '</row>';
                        return msg;
                    }
                    var now = new Date();
                    var jsDate = now.getDate() + "/" + (now.getMonth() + 1) + "/" + now.getFullYear();
                    //insert
                    var r1 = Addrow(1, [{ k: 'A', v: 'CONTROL REGISTRO DE ASISTENCIA', s: 2 }]);
                    var r2 = Addrow(2, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                    var r3 = Addrow(3, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                    var r4 = Addrow(4, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                    var r5 = Addrow(5, [{ k: 'A', v: 'Fecha:', s: 2 }, { k: 'C', v: jsDate, s: 0 }]);
                    sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + sheet.childNodes[0].childNodes[1].innerHTML;
                },
                sheetName: 'CONTROL REGISTRO DE ASISTENCIA',
                title: 'MODO ASISTENCIA EN PUERTA - CONTROL REGISTRO DE ASISTENCIA',
                autoFilter: false,
                exportOptions: {
                    columns: ":visible:not(.noExport)",
                    format: {
                        body: function (data, row, column, node) {
                            var cont = $.trim($(node).text());
                            var cambiar = cont.replace('Cambiar a entrada', '');
                            cambiar = cambiar.replace('Cambiar a salida', '');
                            cambiar = cambiar.replace('No tiene entrada', '---');
                            cambiar = cambiar.replace('No tiene salida', '---');
                            cambiar = cambiar.replace('Opciones', '');
                            cambiar = cambiar.replace('Convertir orden', '');
                            cambiar = cambiar.replace('Asignar a nueva marc.', '');
                            cambiar = cambiar.replace('Eliminar marc.', '');
                            cambiar = cambiar.replace('Actualizar horario', '');
                            cambiar = cambiar.replace('Insertar salida', '');
                            cambiar = cambiar.replace('Insertar entrada', '');
                            cambiar = cambiar.split("/");
                            cambiar = cambiar.map(s => s.trim()).join("/")
                            return $.trim(cambiar);
                        }
                    }
                },
            },
            {
                extend: "pdfHtml5",
                className: 'btn btn-sm mt-1',
                text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                orientation: 'landscape',
                pageSize: 'A1',
                title: 'MODO ASISTENCIA EN PUERTA - CONTROL REGISTRO DE ASISTENCIA',
                exportOptions: {
                    columns: ":visible:not(.noExport)"
                },
                customize: function (doc) {
                    var bodyCompleto = [];
                    doc.content[1].table.body.forEach(function (line, i) {
                        var bodyNuevo = [];
                        if (i >= 1) {
                            line.forEach(element => {
                                var textOriginal = element.text;
                                var cambiar = textOriginal.replace('Cambiar a entrada', '');
                                cambiar = cambiar.replace('Cambiar a salida', '');
                                cambiar = cambiar.replace('No tiene entrada', '---');
                                cambiar = cambiar.replace('No tiene salida', '---');
                                cambiar = cambiar.replace('Opciones', '');
                                cambiar = cambiar.replace('Convertir orden', '');
                                cambiar = cambiar.replace('Asignar a nueva marc.', '');
                                cambiar = cambiar.replace('Eliminar marc.', '');
                                cambiar = cambiar.replace('Actualizar horario', '');
                                cambiar = cambiar.replace('Insertar salida', '');
                                cambiar = cambiar.replace('Insertar entrada', '');
                                cambiar = $.trim(cambiar);
                                cambiar = cambiar.split("/");
                                cambiar = cambiar.map(s => s.trim()).join("/")
                                bodyNuevo.push({ text: cambiar, style: 'defaultStyle' });
                            });
                            bodyCompleto.push(bodyNuevo);
                        } else {
                            bodyCompleto.push(line);
                        }
                    });
                    doc['styles'] = {
                        table: {
                            width: '100%'
                        },
                        tableHeader: {
                            bold: true,
                            fontSize: 11,
                            color: '#ffffff',
                            fillColor: '#14274e',
                            alignment: 'left'
                        },
                        defaultStyle: {
                            fontSize: 10,
                            alignment: 'left'
                        }
                    };
                    doc.pageMargins = [20, 120, 20, 30];
                    doc.content[1].margin = [30, 0, 30, 0];
                    var colCount = new Array();
                    var tr = $('#tablaReport tbody tr:first-child');
                    var trWidth = $(tr).width();
                    $('#tablaReport').find('tbody tr:first-child td').each(function () {
                        var tdWidth = $(this).width();
                        var widthFinal = parseFloat(tdWidth * 130);
                        widthFinal = widthFinal.toFixed(2) / trWidth.toFixed(2);
                        if ($(this).attr('colspan')) {
                            for (var i = 1; i <= $(this).attr('colspan'); i++) {
                                colCount.push('*');
                            }
                        } else {
                            colCount.push(parseFloat(widthFinal.toFixed(2)) + '%');
                        }
                    });
                    doc.content.splice(0, 1);
                    doc.content[0].table.body = bodyCompleto;
                    var objLayout = {};
                    objLayout['hLineWidth'] = function (i) { return .2; };
                    objLayout['vLineWidth'] = function (i) { return .2; };
                    objLayout['hLineColor'] = function (i) { return '#aaa'; };
                    objLayout['vLineColor'] = function (i) { return '#aaa'; };
                    doc.content[0].layout = objLayout;
                    var now = new Date();
                    var jsDate = now.getDate() + "/" + (now.getMonth() + 1) + "/" + now.getFullYear();
                    doc["header"] = function () {
                        return {
                            columns: [
                                {
                                    alignment: 'left',
                                    italics: false,
                                    text: [
                                        { text: '\nCONTROL REGISTRO DE ASISTENCIA', bold: true },
                                        { text: '\n\nRazón Social:\t\t\t\t\t\t', bold: false }, { text: razonSocial, bold: false },
                                        { text: '\nDirección:\t\t\t\t\t\t\t', bold: false }, { text: '\t' + direccion, bold: false },
                                        { text: '\nNúmero de Ruc:\t\t\t\t\t', bold: false }, { text: ruc, bold: false },
                                        { text: '\nFecha:\t\t\t\t\t\t\t\t\t', bold: false }, { text: jsDate, bold: false }
                                    ],

                                    fontSize: 10,
                                    margin: [30, 0]
                                },
                            ],
                            margin: 20
                        };
                    };
                }
            }
        ],
        paging: true,
        initComplete: function () {
            dataT = this;
            setTimeout(function () {
                $("#tablaReport").DataTable().draw();
            }, 1);
            if (this.api().data().length == 0) {
                $('.buttons-page-length').prop("disabled", true);
                $('.buttons-html5').prop("disabled", true);
                $('#switchO').prop("disabled", true);
                $('.dropReporte').prop("disabled", true);
                $('#colEmpleadosCM').prop("disabled", true);
                $('#formatoC').prop("disabled", true);
            } else {
                $('.buttons-page-length').prop("disabled", false);
                $('.buttons-html5').prop("disabled", false);
                $('#switchO').prop("disabled", false);
                $('.dropReporte').prop("disabled", false);
                $('#colEmpleadosCM').prop("disabled", false);
                $('#formatoC').prop("disabled", false);
            }
        },
        drawCallback: function () {
            var api = this.api();
            var len = api.page.len();
            paginaGlobal = len;
        }
    }).draw();
}
$(function () {
    $('#idempleado').select2({
        placeholder: 'Seleccionar',
        language: {
            inputTooShort: function (e) {
                return "Escribir nombre o apellido";
            },
            loadingMore: function () { return "Cargando más resultados…" },
            noResults: function () { return "No se encontraron resultados" }
        },
        minimumInputLength: 2
    });
    f = moment();
    fHoy = f.clone().format("YYYY-MM-DD");
    fAyer = f.clone().add("day", -1).format("YYYY-MM-DD");
    fechaValue.setDate([fAyer, fHoy]);
    $("#fechaInput").change();
    $('#ID_START').val(fAyer);
    $('#ID_END').val(fHoy);
    inicializarTabla();
    // * HORARIO PADRE
    $('.horarioPadre').find('input[type=checkbox]').prop({
        indeterminate: true,
        checked: false
    });
    // * CALCULO DE TIEMPOS PADRE
    $('.detallePadre').find('input[type=checkbox]').prop({
        indeterminate: true,
        checked: false
    });
    // * INCIDENCIA
    $('.incidenciaPadre').find('input[type=checkbox]').prop({
        indeterminate: true,
        checked: false
    });
    // * POR HOTAS TOTALES
    $('#porTotal').prop({
        indeterminate: true,
        checked: false
    });
});
// * VARIABLES PARA EXCEL Y PDF
var razonSocial;
var direccion;
var ruc;
// * ESTADO DE HORARIO EMPLEADO
var contenidoHorario = [];
function cargartabla(fechaI, fechaF) {
    var idemp = $('#empleadoPor').val();
    if (idemp.length == 0) {
        $.notifyClose();
        $.notify(
            {
                message:
                    "\nElegir empleado.",
                icon: "admin/images/warning.svg",
            },
            {
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
        return false;
    }
    $.ajax({
        type: "GET",
        url: "/datosReporteFecha",
        data: {
            fechaI: fechaI,
            fechaF: fechaF,
            idemp: idemp
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            }
        },
        beforeSend: function () {
            $("#tablaReport").css('opacity', .3);
            $('div.dataTables_processing').show();
            $('[data-toggle="tooltip"]').tooltip("hide");
        },
    }).then(function (data) {
        console.log(data);
        $('[data-toggle="tooltip"]').tooltip("hide");
        $('div.dataTables_processing').hide();
        $("#tablaReport").css('opacity', 1);
        contenidoHorario.length = 0;
        if (data.length != 0) {
            if ($.fn.DataTable.isDataTable("#tablaReport")) {
                $("#tablaReport").DataTable().destroy();
            }
            $('#customSwitDetalles').prop("disabled", false);
            $('#switPausas').prop("disabled", false);
            $('#theadD').empty();
            // ? ************************************** CANTIDAD DE COLUMNAS DE LA TABLA*****************************
            // * ARRAY COMPLETO DE DETALLES, CANTIDAD DE PAUSAS Y MARCACIONES POR EL GRUPO DE HORARIOS
            var arrayHorario = [];
            // * CANTIDAD MAXIMA DE GRUPOS DE HORARIOS
            var cantidadGruposHorario = 1;
            // * RECORREMOS POR FECHA
            for (const key in data) {
                for (let i = 0; i < data[key].length; i++) {
                    // * OBTENER EL MAXIMO DE GRUPOS DE HORARIOS
                    if (cantidadGruposHorario < data[key][i].data.length) {
                        cantidadGruposHorario = data[key][i].data.length;
                    }
                }
            }
            // * BUSCAMOS LA CANTIDAD MAXIMA DE PAUSAS Y MARCACIONES POR GRUPO
            for (let busqueda = 0; busqueda < cantidadGruposHorario; busqueda++) {
                // * MAXIMO DE PAUSAS POR GRUPO
                var cantidadPausaG = 1;
                // * MAXIMO DE MARCACIONES POR GRUPO
                var cantidadMarcacionG = 1;
                // * RECORREMOS POR FECHA
                for (const key in data) {
                    for (let i = 0; i < data[key].length; i++) {
                        // * OBTENER EL MAXIMO DE MARCACIONES Y PAUSAS POR GRUPO
                        if (data[key][i].data[busqueda] != undefined) {
                            if (cantidadPausaG < data[key][i].data[busqueda].pausas.length) {
                                cantidadPausaG = data[key][i].data[busqueda].pausas.length;
                            }
                            if (cantidadMarcacionG < data[key][i].data[busqueda].marcaciones.length) {
                                cantidadMarcacionG = data[key][i].data[busqueda].marcaciones.length;
                            }
                        }
                    }
                }
                arrayHorario.push(cantidadMarcacionG + "," + cantidadPausaG);
            }
            // ? ************************************************* ARMAR CABEZERA **********************************************
            var cantidadM = 0;
            var cantidadP = 0;
            arrayHorario.map((valor) => cantidadM = cantidadM + (valor.split(",")[0] * 7));
            arrayHorario.map((valor) => cantidadP = cantidadP + (valor.split(",")[1] * 4));
            var cantidadColSpan = (9 + (cantidadGruposHorario * 23) + cantidadM + cantidadP + 18);
            var theadTabla = `<tr>`;
            theadTabla += `<th>#&nbsp;</th>
                            <th class="text-center">Fecha</th>
                            <th>Número de documento</th>
                            <th name="colCodigo" class="colCodigo">Código de trabajador</th>
                            <th class="formatoNYA">Nombres y apellidos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th class="formatoAYN">Apellidos y nombres&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th class="formatoNA">Nombres&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th class="formatoNA">Apellidos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th name="colCargo" class="colCargo">Cargo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>`;
            //* GRUPO DE HORARIOS
            for (let m = 0; m < cantidadGruposHorario; m++) {
                // ************************************ DATOS DEL GRUPO HORARIO **************************************
                // !HORARIO
                theadTabla += `<th class="text-center descripcionHorario" style="border-left: 2px solid #383e56!important;border-right: 1px dashed #c8d4de!important;font-weight: 600 !important" name="descripcionHorario">
                                    <span>
                                        Descripción del horario <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${m + 1}</b>
                                    </span>
                                </th>
                                <th class="text-center horarioHorario" name="horarioHorario" style="border-right: 1px dashed #c8d4de!important;">
                                    <span>
                                        Horario <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${m + 1}</b>
                                    </span>
                                </th>
                                <th name="toleranciaIHorario" class="toleranciaIHorario" style="border-right: 1px dashed #c8d4de!important;">Tolerancia en el ingreso</th>
                                <th name="toleranciaFHorario" class="toleranciaFHorario" style="border-right: 1px dashed #c8d4de!important;">Tolerancia en la salida</th>
                                <th name="colTiempoEntreH" class="text-center colTiempoEntreH" style="border-right: 1px dashed #c8d4de!important;">Tiempo entre horario</th>
                                <th name="colTiempoMuertoEntrada" class="text-center colTiempoMuertoEntrada" style="border-right: 1px dashed #c8d4de!important;">Tiempo muerto - entrada</th>
                                <th name="colTiempoMuertoSalida" class="text-center colTiempoMuertoSalida" style="border-right: 1px dashed #c8d4de!important;">Tiempo muerto - salida</th>
                                <th name="colSobreTiempo" class="text-center colSobreTiempo" style="border-right: 1px dashed #c8d4de!important;">Sobretiempo</th>
                                <th name="colHoraNormal" class="text-center colHoraNormal" style="border-right: 1px dashed #c8d4de!important;">Horas diurnas</th>
                                <th name="colHoraDiurnaNormal" class="text-center colHoraDiurnaNormal" style="border-right: 1px dashed #c8d4de!important;">Horas normales - diurnas</th>
                                <th name="colSobreTNormal" class="text-center colSobreTNormal" style="border-right: 1px dashed #c8d4de!important;">Sobretiempo diurno</th>
                                <th name="colHE25D" class="text-center colHE25D" style="border-right: 1px dashed #c8d4de!important;">H.E. 25% Diurnas</th>
                                <th name="colHE35D" class="text-center colHE35D" style="border-right: 1px dashed #c8d4de!important;">H.E. 35% Diurnas</th>
                                <th name="colHE100D" class="text-center colHE100D" style="border-right: 1px dashed #c8d4de!important;">H.E. 100% Diurnas</th>
                                <th name="colHoraNocturna" class="text-center colHoraNocturna" style="border-right: 1px dashed #c8d4de!important;">Horas nocturnas</th>
                                <th name="colHoraNocturnaNormal" class="text-center colHoraNocturnaNormal" style="border-right: 1px dashed #c8d4de!important;">Horas normales - nocturnas</th>
                                <th name="colSobreTNocturno" class="text-center colSobreTNocturno" style="border-right: 1px dashed #c8d4de!important;">Sobretiempo nocturno</th>
                                <th name="colHE25N" class="text-center colHE25N" style="border-right: 1px dashed #c8d4de!important;">H.E. 25% Nocturnas</th>
                                <th name="colHE35N" class="text-center colHE35N" style="border-right: 1px dashed #c8d4de!important;">H.E. 35% Nocturnas</th>
                                <th name="colHE100N" class="text-center colHE100N" style="border-right: 1px dashed #c8d4de!important;">H.E. 100% Nocturnas</th>
                                <th name="colFaltaJornada" class="text-center colFaltaJornada" style="border-right: 1px dashed #c8d4de!important;">Jornada incompleta</th>
                                <th name="colTardanza" class="text-center colTardanza" style="border-right: 1px dashed #c8d4de!important;">Tardanza</th>
                                <th name="faltaHorario" class="faltaHorario" style="border-right: 1px dashed #c8d4de!important;">Falta</th>`;
                // ! MARCACION
                var cantidadColumnasM = arrayHorario[m].split(",")[0];
                for (let j = 0; j < cantidadColumnasM; j++) {
                    theadTabla += `<th style="border-left:1px dashed #aaaaaa!important;" class="text-center colMarcaciones" name="colMarcaciones">
                                        <span>
                                            Entrada <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${j + 1}</b>
                                        </span>
                                    </th>
                                    <th class="text-center colDispositivoE" name="colDispositivoE">
                                        Dispositivo de entrada <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${j + 1}</b>
                                    </th>
                                    <th class="text-center colMarcaciones" name="colMarcaciones">
                                        <span>
                                            Salida <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${j + 1}</b>
                                        </span>
                                    </th>
                                    <th class="text-center colDispositivoS" name="colDispositivoS">
                                        Dispositivo de salida <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${j + 1}</b>
                                    </th>
                                    <th id="tSitio" name="colTiempoS" class="colTiempoS">
                                        <span>
                                            Tiempo <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${j + 1}</b>
                                        </span>
                                    </th>
                                    <th class="text-center colTiempoMuertoEXM">
                                        <span>
                                            Tiempo muerto - entrada <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${j + 1}</b>
                                        </span>
                                    </th>
                                    <th class="text-center colTiempoMuertoSXM">
                                        <span>
                                            Tiempo muerto - salida <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${j + 1}</b>
                                        </span>
                                    </th>`;
                }
                // ! PAUSAS
                var cantidadColumnasP = arrayHorario[m].split(",")[1];
                for (let p = 0; p < cantidadColumnasP; p++) {
                    theadTabla += `<th style="border-left: 1px dashed #aaaaaa!important;" name="descripcionPausa" class="descripcionPausa">
                                        <span>Pausa<b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${p + 1}</b></span>
                                    </th>
                                    <th name="horarioPausa" class="horarioPausa">
                                        <span>Horario de pausa <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${p + 1}</b></span>
                                    </th>
                                    <th name="tiempoPausa" class="tiempoPausa">
                                        <span>Tiempo de pausa <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${p + 1}</b></span>
                                    </th>
                                    <th name="excesoPausa" class="excesoPausa">
                                        <span>Exceso de pausa <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${p + 1}</b></span>
                                    </th>`;
                }
            }
            theadTabla += `<th style="border-left: 2px solid #383e56!important;" name="colTiempoTotal" class="colTiempoTotal">Tiempo total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colTiempoMuertoTotalE" class="colTiempoMuertoTotalE">Tiempo muerto total - entrada</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colTiempoMuertoTotalS" class="colTiempoMuertoTotalS">Tiempo muerto total - salida</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colSobreTiempoTotal" class="colSobreTiempoTotal">Sobretiempo total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colHoraNormalTotal" class="colHoraNormalTotal">Horas diurnas total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colSobretiempoNormalT" class="colSobretiempoNormalT">Sobretiempo diurno total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colHE25DTotal" class="colHE25DTotal">H.E. 25% Diurnas total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colHE35DTotal" class="colHE35DTotal">H.E. 35% Diurnas total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colHE100DTotal" class="colHE100DTotal">H.E. 100% Diurnas total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colHoraNocturnaTotal" class="colHoraNocturnaTotal">Horas nocturnas total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colSobretiempoNocturnoT" class="colSobretiempoNocturnoT">Sobretiempo nocturno total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colHE25NTotal" class="colHE25NTotal">H.E. 25% Nocturnas total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colHE35NTotal" class="colHE35NTotal">H.E. 35% Nocturnas total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colHE100NTotal" class="colHE100NTotal">H.E. 100% Nocturnas total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colFaltaJornadaTotal" class="colFaltaJornadaTotal">Jornada incompleta total</th>  
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colTardanzaTotal" class="colTardanzaTotal">Tardanza total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="faltaTotal" class="faltaTotal">Falta total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="incidencia" class="incidencia">Incidencias</th>`;
            theadTabla += `</tr>`;
            //* DIBUJAMOS CABEZERA
            $('#theadD').html(theadTabla);
            // ! ********************************************************* BODY DE TABLA**************************************************
            $('#tbodyD').empty();
            var tbody = "";
            //* ARMAMOS BODY DE TABLA
            for (const key in data) {
                var valorEstado = 0;
                data[key].forEach(element => {
                    if (element.data.length != 0) {
                        element.data.forEach(value => {
                            if (value.marcaciones.length != 0) {
                                valorEstado = 1;
                            }
                        });
                    }
                });
                tbody += `<tr data-estado="${valorEstado}" style="background: #f6f5f1!important;color: #383e56 !important;">
                            <th colspan="${cantidadColSpan}" scope="colgroup" class="text-left font-weight-bold">
                                <img style="margin-bottom: 3px;" src="landing/images/calendarioReporte.svg" height="20" class="mr-2">
                                ${moment(key).format("ddd D, MM-YYYY")}
                            </th>`;
                // * COMPLEMENTAMOS COLUMNAS
                for (let completar = 0; completar < (cantidadColSpan - 1); completar++) {
                    tbody += `<th style="display:none"></th>`;
                }
                tbody += `</tr>`;
                for (let index = 0; index < data[key].length; index++) {
                    razonSocial = data[key][index].organi_razonSocial;   // : -> VARIABLES PARA EXCEL Y PDF
                    direccion = data[key][index].organi_direccion;       // : -> VARIABLES PARA EXCEL Y PDF
                    ruc = data[key][index].organi_ruc;                   // : -> VARIABLES PARA EXCEL Y PDF
                    var valorEstado = 0;
                    if (data[key][index].data.length != 0) {
                        data[key][index].data.forEach(element => {
                            if (element.marcaciones.length != 0) {
                                valorEstado = 1;
                            }
                        });
                    }
                    tbody += `<tr data-estado="${valorEstado}">`;
                    tbody += `<td>${(index + 1)}&nbsp;</td>
                            <td>${key}</td>
                            <td class="text-center">${data[key][index].emple_nDoc}</td>
                            <td class="text-center" name="colCodigo">${data[key][index].emple_codigo}</td>
                            <td class="formatoNYA">${data[key][index].perso_nombre} ${data[key][index].perso_apPaterno} ${data[key][index].perso_apMaterno}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td class="formatoAYN">${data[key][index].perso_apPaterno} ${data[key][index].perso_apMaterno} ${data[key][index].perso_nombre} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td class="formatoNA">${data[key][index].perso_nombre} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td class="formatoNA">${data[key][index].perso_apPaterno} ${data[key][index].perso_apMaterno}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    tbody += `<td name="colCargo">${data[key][index].cargo_descripcion}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;

                    // * ARMAR GRUPO DE HORARIOS
                    var grupoHorario = "";
                    //* ARMAR Y ORDENAR MARCACIONES
                    var sumaTiempos = moment.duration(0);                           //: SUMANDO LOS TIEMPOS
                    var sumaTardanzas = moment.duration(0);                         //: SUMANDO TARDANZAS
                    var sumaSobreTiempo = moment.duration(0);                       //: SUMANDO SOBRE TIEMPO
                    var sumaFaltaJornada = moment.duration(0);                      //: SUMANDO FALTA JORNADA
                    var sumaHorasNormalesT = moment.duration(0);                    //: SUMANDO TOTALES DE HORAS NORMALES
                    var sumaSobreTiempoNormalesT = moment.duration(0);              //: SUMANDO SOBRETIEMPO TOTALES DE HORAS NORMALES
                    var sumaHorasNocturnasT = moment.duration(0);                   //: SUMANDO TOTALES DE HORAS NOCTURNAS
                    var sumaSobreTiempoNocturnasT = moment.duration(0);             //: SUMANDO SOBRETIEMPO TOTALES DE HORAS NOCTURNAS
                    var sumaHorasE25D = moment.duration(0);                         //: SUMANDO TOTALES DE HORAS EXTRAS DE 25% DIURNAS
                    var sumaHorasE35D = moment.duration(0);                         //: SUMANDO TOTALES DE HORAS EXTRAS DE 35% DIURNAS
                    var sumaHorasE100D = moment.duration(0);                        //: SUMANDO TOTALES DE HORAS EXTRAS DE 100% DIURNAS
                    var sumaHorasE25N = moment.duration(0);                         //: SUMANDO TOTALES DE HORAS EXTRAS DE 25% NOCTURNAS
                    var sumaHorasE35N = moment.duration(0);                         //: SUMANDO TOTALES DE HORAS EXTRAS DE 35% NOCTURNAS
                    var sumaHorasE100N = moment.duration(0);                        //: SUMANDO TOTALES DE HORAS EXTRAS DE 100% NOCTURNAS
                    var sumaMuertosEntrada = moment.duration(0);                    //: SUMANDO LOS TIEMPOS MUERTOS TOTALES DE LA ENTRADA
                    var sumaMuertosSalida = moment.duration(0);                     //: SUMANDO LOS TIEMPOS MUERTOS TOTALES DE LA SALIDA
                    // * CANTIDAD DE FALTAS
                    var sumaFaltas = 0;
                    for (let m = 0; m < cantidadGruposHorario; m++) {
                        // * HORARIO
                        var idHorarioM = [];                                        //: GUARDAR IDS DE HORARIO
                        // * PAUSA
                        var idPausas = [];                                          //: GUARDAR IDS DE PAUSAS
                        // * PAUSAS
                        var tiempoHoraPausa = "00";                                 //: HORAS TARDANZA
                        var tiempoMinutoPausa = "00";                               //: MINUTOS TARDANZA
                        var tiempoSegundoPausa = "00";                              //: SEGUNDOS TARDANZA
                        // * TIEMPOE ENTRE HORARIOS 
                        var sumaTiemposEntreHorarios = moment.duration(0);          //: SUMANDO LOS TIEMPOS ENTRE HORARIOS
                        var estadoTiempoHorario = true;                             //: VARIABLE BANDERA DE ESTADO ENTRE TIEMPO DE HORARIOS
                        //* EXCESO
                        var tiempoHoraExceso = "00";                                //: HORAS DE EXCESO
                        var tiempoMinutoExceso = "00";                              //: MINUTOS DE EXCESO
                        var tiempoSegundoExceso = "00";                             //: SEGUNDOS DE EXCESO
                        // * TARDANZA
                        var segundosTardanza = "00";                                //: SEGUNDOS DE TARDANZA
                        var minutosTardanza = "00";                                 //: MINUTOS DE TARDANZA
                        var horasTardanza = "00";                                   //: HORAS DE TARDANZA
                        // * SOBRE TIEMPO
                        var segundosSobreT = "00";                                  //: SEGUNDOS DE SOBRETIEMPO
                        var minutosSobreT = "00";                                   //: MINUTOS DE SOBRETIEMPO
                        var horasSobreT = "00";                                     //: HORAS DE SOBRETIEMPO
                        // * FALTA JORNADA
                        var segundosFaltaJ = "00";                                  //: SEGUNDOS DE FALTA JORNADA
                        var minutosFaltaJ = "00";                                   //: MINUTOS DE FALTA JORNADA
                        var horasFaltaJ = "00";                                     //: HORAS DE FALTA JORNADA
                        // * HORAS DIURNAS
                        var sumaHorasNormales = moment.duration(0);                 //: TIEMPOS DE HORAS DIURNAS
                        var sobretiempoNormales = moment.duration(0);               //: SOBRETIEMPO DIURNO
                        var horasNormalesDiurnas = moment.duration(0);              //: TIEMPOS NORMALES DIURNAS
                        // * HORAS NOCTURNAS
                        var sumaHorasNocturnas = moment.duration(0);                //: TIEMPOS DE HORAS NOCTURNAS
                        var sobretiempoNocturnos = moment.duration(0);              //: SOBRETIEMPO NCOTUNO
                        var horasNormalesNocturnas = moment.duration(0);            //: TIEMPOS NORMALES NOCTURNAS
                        // * HORAS EXTRAS - DIURNAS
                        var diurnas25 = moment.duration(0);                         //: DIURNAS 25%
                        var diurnas35 = moment.duration(0);                         //: DIURNAS 35%
                        var diurnas100 = moment.duration(0);                        //: DIURNAS 100%
                        // * HORAS EXTRAS - NOCTURNAS
                        var nocturnas25 = moment.duration(0);                       //: NOCTURNAS 25%
                        var nocturnas35 = moment.duration(0);                       //: NOCTURNAS 35%
                        var nocturnas100 = moment.duration(0);                      //: NOCTURNAS 100%
                        // * TIEMPO MUERTO ENTRADA
                        var tiempoMuertoEntrada = moment.duration(0);               //: TIEMPO MUERTO ENTRADA
                        // * TIEMPO MUERTO SALIDA
                        var tiempoMuertoSalida = moment.duration(0);                //: TIEMPO MUERTO SALIDA
                        if (data[key][index].data[m] != undefined) {
                            // ! ******************************************* COLUMNAS DE HORARIOS **************************************************
                            var horarioData = data[key][index].data[m].horario;
                            contenidoHorario.push({ "idEmpleado": data[key][index].emple_id, "idHorarioE": horarioData.idHorarioE, "estado": horarioData.estado });
                            // ! ******************************************* CALCULOS ENTRE HORARIOS ***********************************************
                            // * DATA PARA TARDANZA
                            if (horarioData.idHorario != 0) {
                                if (data[key][index].data[m].marcaciones[0] != undefined) {
                                    if (data[key][index].data[m].marcaciones[0].entrada != 0) {
                                        // * TARDANZA
                                        var dataParaTardanza = data[key][index].data[m].marcaciones[0];
                                        if (dataParaTardanza.idH != 0) {
                                            var horaInicial = moment(dataParaTardanza.entrada);
                                            // ******************************* TARDANZA ***************************************
                                            // ! PARA QUE TOME SOLO TARDANZA EN LA PRIMERA MARCACION
                                            if (!idHorarioM.includes(dataParaTardanza.idH)) {
                                                idHorarioM.push(dataParaTardanza.idH);  // : AGREGAMOS EL ID AL ARRAY
                                                var horaInicioHorario = moment(horarioData.horarioIni);
                                                var horaConTolerancia = horaInicioHorario.clone().add({ "minutes": horarioData.toleranciaI });
                                                //: COMPARAMOS SI ES MAYOR A LA HORA DE INICIO DEL HORARIO
                                                if (horaInicial.isAfter(horaConTolerancia)) {
                                                    var tardanza = horaInicial - horaInicioHorario;
                                                    segundosTardanza = moment.duration(tardanza).seconds();
                                                    minutosTardanza = moment.duration(tardanza).minutes();
                                                    horasTardanza = Math.trunc(moment.duration(tardanza).asHours());
                                                    if (horasTardanza < 10) {
                                                        horasTardanza = '0' + horasTardanza;
                                                    }
                                                    if (minutosTardanza < 10) {
                                                        minutosTardanza = '0' + minutosTardanza;
                                                    }
                                                    if (segundosTardanza < 10) {
                                                        segundosTardanza = '0' + segundosTardanza;
                                                    }
                                                    sumaTardanzas = sumaTardanzas.add({ "hours": horasTardanza, "minutes": minutosTardanza, "seconds": segundosTardanza });
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            // * VARIABLE QUE GUARDARA QUE TIPO DE MARCACIÓN FUE PRIMERO
                            var primeraM = undefined;
                            // * DATA PARA ENTRE HORARIO
                            for (let res = 0; res < data[key][index].data[m].marcaciones.length; res++) {
                                var dataM = data[key][index].data[m].marcaciones[res];
                                // * CALCULAR TIEMPO TOTAL , TIEMPO DE PAUSA Y EXCESO DE PAUSAS
                                if (dataM.entrada != 0 && dataM.salida != 0) {
                                    var horaFinalData = moment(dataM.salida);
                                    var horaInicialData = moment(dataM.entrada);
                                    if (horaFinalData.isSameOrAfter(horaInicialData)) {
                                        if (horarioData.idHorario != 0) {
                                            if (horarioData.tiempoMuertoI == 1) {
                                                // : SI ENTRADA ES MENOR A LA HORA DE INICIO DE HORARIO
                                                if (horaInicialData.clone().isBefore(moment(horarioData.horarioIni))) {
                                                    if (horaFinalData.clone().isAfter(moment(horarioData.horarioIni))) {
                                                        // : HORA DE ENTRADA
                                                        var tiempoMuerto = moment(horarioData.horarioIni) - horaInicialData.clone();
                                                        tiempoMuertoEntrada = tiempoMuertoEntrada.add(tiempoMuerto);
                                                        sumaMuertosEntrada = sumaMuertosEntrada.add(tiempoMuerto);
                                                        horaInicialData = moment(horarioData.horarioIni);
                                                        // : HORA DE SALIDA
                                                        if (horarioData.tiempoMuertoS == 1) {
                                                            if (horaFinalData.clone().isAfter(moment(horarioData.horarioFin))) {
                                                                var tiempoMuerto = moment.duration(parseInt(horarioData.toleranciaF), "minutes");
                                                                tiempoMuertoSalida = tiempoMuertoSalida.add(tiempoMuerto);
                                                                sumaMuertosSalida = sumaMuertosSalida.add(tiempoMuerto);
                                                                var NuevaSalida = horaFinalData.clone().subtract(horarioData.toleranciaF, "minutes").format("YYYY-MM-DD HH:mm:ss");
                                                                horaFinalData = moment(NuevaSalida);
                                                            }
                                                        }
                                                    } else {
                                                        var tiempoMuerto = horaFinalData.clone() - horaInicialData.clone();
                                                        tiempoMuertoEntrada = tiempoMuertoEntrada.add(tiempoMuerto);
                                                        sumaMuertosEntrada = sumaMuertosEntrada.add(tiempoMuerto);
                                                        horaInicialData = moment.duration(0);
                                                        horaFinalData = moment.duration(0);
                                                    }
                                                } else {
                                                    // : HORA DE SALIDA
                                                    if (horarioData.tiempoMuertoS == 1) {
                                                        if (horaFinalData.clone().isAfter(moment(horarioData.horarioFin))) {
                                                            var tiempoMuerto = moment.duration(parseInt(horarioData.toleranciaF), "minutes");
                                                            tiempoMuertoSalida = tiempoMuertoSalida.add(tiempoMuerto);
                                                            sumaMuertosSalida = sumaMuertosSalida.add(tiempoMuerto);
                                                            var NuevaSalida = horaFinalData.clone().subtract(horarioData.toleranciaF, "minutes").format("YYYY-MM-DD HH:mm:ss");
                                                            horaFinalData = moment(NuevaSalida);
                                                        }
                                                    }
                                                }
                                            } else {
                                                // : HORA DE SALIDA
                                                if (horarioData.tiempoMuertoS == 1) {
                                                    if (horaFinalData.clone().isAfter(moment(horarioData.horarioFin))) {
                                                        var tiempoMuerto = moment.duration(parseInt(horarioData.toleranciaF), "minutes");
                                                        tiempoMuertoSalida = tiempoMuertoSalida.add(tiempoMuerto);
                                                        sumaMuertosSalida = sumaMuertosSalida.add(tiempoMuerto);
                                                        var NuevaSalida = horaFinalData.clone().subtract(horarioData.toleranciaF, "minutes").format("YYYY-MM-DD HH:mm:ss");
                                                        horaFinalData = moment(NuevaSalida);
                                                    }
                                                }
                                            }
                                        }
                                        if (horaInicialData != 0 && horaFinalData != 0) {
                                            // : GUARDAR EL TIEMPO ENTE MARCACIONES
                                            var tiempoEntreM = moment.duration(horaFinalData.diff(horaInicialData));
                                            // : ACUMULAR TIEMPO CALCULADOS
                                            var acumuladorEntreM = moment.duration(0);
                                            // : TIEMPOS MAXIMOS DE DIURNOS Y NOCTURNOS 
                                            var tiempoMaximoDiurno = moment(horaInicialData.clone().format("YYYY-MM-DD") + " " + "22:00:00");
                                            var tiempoMaximoNocturno = moment(horaInicialData.clone().format("YYYY-MM-DD") + " " + "06:00:00");
                                            // : HORAS NORMALES
                                            if (horaInicialData.isAfter(tiempoMaximoNocturno) && horaInicialData.isSameOrBefore(tiempoMaximoDiurno)) {
                                                if (primeraM == undefined) primeraM = 0;
                                                if (horaFinalData.clone().isSameOrBefore(tiempoMaximoDiurno)) {
                                                    var tiempoNormal = horaFinalData - horaInicialData;
                                                    var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                    var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                    var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                    sumaHorasNormales = sumaHorasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                    sumaHorasNormalesT = sumaHorasNormalesT.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                } else {
                                                    var minuendoResta = tiempoMaximoDiurno.clone();
                                                    var sustraendoResta = horaInicialData.clone();
                                                    var contadorDias = 1;
                                                    while (acumuladorEntreM < tiempoEntreM) {
                                                        // : ************************************************* HORAS NORMALES *************************************************
                                                        var tiempoNormal = minuendoResta - sustraendoResta;
                                                        var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                        var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                        var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                        sumaHorasNormales = sumaHorasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                        sumaHorasNormalesT = sumaHorasNormalesT.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                        acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                        // : ************************************************* FINALIZACION *****************************************************
                                                        var tiempoMaximoDiurnoAnterior = tiempoMaximoDiurno;
                                                        tiempoMaximoDiurno = tiempoMaximoDiurno.clone().add("day", contadorDias);
                                                        tiempoMaximoNocturno = tiempoMaximoNocturno.clone().add("day", contadorDias);
                                                        if (acumuladorEntreM < tiempoEntreM) {
                                                            if (horaFinalData.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                                // : HORA NOCTURNA
                                                                sustraendoResta = minuendoResta;
                                                                minuendoResta = horaFinalData;
                                                                var tiempoNocturno = minuendoResta - sustraendoResta;
                                                                var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                                var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                                var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                                sumaHorasNocturnas = sumaHorasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                                sumaHorasNocturnasT = sumaHorasNocturnasT.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                                acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                            } else {
                                                                minuendoResta = tiempoMaximoNocturno;
                                                                sustraendoResta = tiempoMaximoDiurnoAnterior;
                                                                // : HORA NOCTURNA
                                                                var tiempoNocturno = minuendoResta - sustraendoResta;
                                                                var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                                var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                                var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                                sumaHorasNocturnas = sumaHorasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                                sumaHorasNocturnasT = sumaHorasNocturnasT.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                                acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                                if (horaFinalData.clone().isSameOrBefore(tiempoMaximoDiurno)) {
                                                                    minuendoResta = horaFinalData.clone();
                                                                    sustraendoResta = tiempoMaximoNocturno;
                                                                } else {
                                                                    minuendoResta = tiempoMaximoDiurno;
                                                                    sustraendoResta = tiempoMaximoNocturno;
                                                                }
                                                            }
                                                        }
                                                        contadorDias++;
                                                        // debugger;
                                                    }
                                                }
                                            } else {
                                                if (primeraM == undefined) primeraM = 1;
                                                if (horaFinalData.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                    // : HORAS NOCTURNAS
                                                    var tiempoNocturno = horaFinalData - horaInicialData;
                                                    var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                    var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                    var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                    sumaHorasNocturnas = sumaHorasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                    sumaHorasNocturnasT = sumaHorasNocturnasT.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                } else {
                                                    if (moment.duration(horaInicialData.clone().format("HH:mm:ss")) < moment.duration("06:00:00")) {
                                                        tiempoMaximoDiurno = moment(horaInicialData.clone().format("YYYY-MM-DD") + " " + "22:00:00");
                                                        tiempoMaximoNocturno = moment(horaInicialData.clone().format("YYYY-MM-DD") + " " + "06:00:00");
                                                    } else {
                                                        tiempoMaximoDiurno = moment(horaInicialData.clone().add("day", 1).format("YYYY-MM-DD") + " " + "22:00:00");
                                                        tiempoMaximoNocturno = moment(horaInicialData.clone().add("day", 1).format("YYYY-MM-DD") + " " + "06:00:00");
                                                    }
                                                    if (horaFinalData.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                        // : HORAS NOCTURNAS
                                                        var tiempoNocturno = horaFinalData - horaInicialData;
                                                        var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                        var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                        var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                        sumaHorasNocturnas = sumaHorasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                        sumaHorasNocturnasT = sumaHorasNocturnasT.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                    } else {
                                                        var minuendoResta = tiempoMaximoNocturno.clone();
                                                        var sustraendoResta = horaInicialData.clone();
                                                        var contadorDias = 1;
                                                        while (acumuladorEntreM < tiempoEntreM) {
                                                            // : HORAS NOCTURNAS
                                                            var tiempoNocturno = minuendoResta - sustraendoResta;
                                                            var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                            var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                            var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                            sumaHorasNocturnas = sumaHorasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                            sumaHorasNocturnasT = sumaHorasNocturnasT.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                            acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                            // : CALCULOS DE TIEMPO
                                                            var tiempoMaximoDiurnoAnterior = tiempoMaximoDiurno;
                                                            tiempoMaximoDiurno = moment(tiempoMaximoDiurno.clone().add("day", contadorDias));
                                                            tiempoMaximoNocturno = moment(tiempoMaximoNocturno.clone().add("day", contadorDias));
                                                            if (acumuladorEntreM < tiempoEntreM) {
                                                                if (horaFinalData.clone().isSameOrBefore(tiempoMaximoDiurnoAnterior)) {
                                                                    sustraendoResta = minuendoResta;
                                                                    minuendoResta = horaFinalData.clone();
                                                                    // : HORAS NORMALES
                                                                    var tiempoNormal = minuendoResta - sustraendoResta;
                                                                    var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                                    var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                                    var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                                    sumaHorasNormales = sumaHorasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                                    sumaHorasNormalesT = sumaHorasNormalesT.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                                    acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                                } else {
                                                                    sustraendoResta = minuendoResta;
                                                                    minuendoResta = tiempoMaximoDiurnoAnterior;
                                                                    // : HORAS NORMALES
                                                                    var tiempoNormal = minuendoResta - sustraendoResta;
                                                                    var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                                    var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                                    var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                                    sumaHorasNormales = sumaHorasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                                    sumaHorasNormalesT = sumaHorasNormalesT.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                                    acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                                    if (horaFinalData.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                                        minuendoResta = horaFinalData.clone();
                                                                        sustraendoResta = tiempoMaximoDiurnoAnterior;
                                                                    } else {
                                                                        minuendoResta = tiempoMaximoNocturno;
                                                                        sustraendoResta = tiempoMaximoDiurnoAnterior;
                                                                    }
                                                                }
                                                            }
                                                            contadorDias++;
                                                            // debugger;
                                                        }
                                                    }
                                                }
                                            }
                                            // * TIEMPO TOTAL TRABAJADA
                                            var tiempoRestanteD = horaFinalData - horaInicialData;
                                            var segundosTiempoD = moment.duration(tiempoRestanteD).seconds();
                                            var minutosTiempoD = moment.duration(tiempoRestanteD).minutes();
                                            var horasTiempoD = Math.trunc(moment.duration(tiempoRestanteD).asHours());
                                            if (horasTiempoD < 10) {
                                                horasTiempoD = '0' + horasTiempoD;
                                            }
                                            if (minutosTiempo < 10) {
                                                minutosTiempoD = '0' + minutosTiempoD;
                                            }
                                            if (segundosTiempoD < 10) {
                                                segundosTiempoD = '0' + segundosTiempoD;
                                            }
                                            sumaTiemposEntreHorarios = sumaTiemposEntreHorarios.add({ "hours": horasTiempoD, "minutes": minutosTiempoD, "seconds": segundosTiempoD });
                                            // : SUMA DE TIEMPO TOTAL
                                            sumaTiempos = sumaTiempos.add({ "hours": horasTiempoD, "minutes": minutosTiempoD, "seconds": segundosTiempoD });
                                        }
                                    }
                                }
                            }
                            // * SOBRETIEMPO ENTRE HORARIO Y FALTA DE JORNADA
                            if (horarioData.idHorario != 0) {
                                var horasObligadas = moment.duration(horarioData.horasObligadas);
                                var tiempoEntreH = sumaTiemposEntreHorarios.clone();
                                // * SOBRETIEMPO
                                if (tiempoEntreH > horasObligadas) {
                                    // * SOBRE TIEMPO 
                                    var tiempoSobreT = tiempoEntreH - horasObligadas;
                                    segundosSobreT = moment.duration(tiempoSobreT).seconds();
                                    minutosSobreT = moment.duration(tiempoSobreT).minutes();
                                    horasSobreT = Math.trunc(moment.duration(tiempoSobreT).asHours());
                                    if (horasSobreT < 10) {
                                        horasSobreT = '0' + horasSobreT;
                                    }
                                    if (minutosSobreT < 10) {
                                        minutosSobreT = '0' + minutosSobreT;
                                    }
                                    if (segundosSobreT < 10) {
                                        segundosSobreT = '0' + segundosSobreT;
                                    }
                                    sumaSobreTiempo = sumaSobreTiempo.add({ "hours": horasSobreT, "minutes": minutosSobreT, "seconds": segundosSobreT });
                                } else {
                                    // * FALTA JORNADA
                                    var tiempoFaltaJ = horasObligadas - tiempoEntreH;
                                    segundosFaltaJ = moment.duration(tiempoFaltaJ).seconds();
                                    minutosFaltaJ = moment.duration(tiempoFaltaJ).minutes();
                                    horasFaltaJ = Math.trunc(moment.duration(tiempoFaltaJ).asHours());
                                    if (horasFaltaJ < 10) {
                                        horasFaltaJ = '0' + horasFaltaJ;
                                    }
                                    if (minutosFaltaJ < 10) {
                                        minutosFaltaJ = '0' + minutosFaltaJ;
                                    }
                                    if (segundosFaltaJ < 10) {
                                        segundosFaltaJ = '0' + segundosFaltaJ;
                                    }
                                    sumaFaltaJornada = sumaFaltaJornada.add({ "hours": horasFaltaJ, "minutes": minutosFaltaJ, "seconds": segundosFaltaJ });
                                }
                                if (primeraM != undefined) {
                                    if (primeraM == 0) {
                                        var nuevaHorasO = {};
                                        if (sumaHorasNormales > horasObligadas) {
                                            nuevaHorasO = moment.duration(0);
                                            // : ********************************* HORAS EXTRAS NORMALES *****************************
                                            var tiempoExtraResta = sumaHorasNormales - horasObligadas;
                                            var segundosExtra = moment.duration(tiempoExtraResta).seconds();
                                            var minutosExtra = moment.duration(tiempoExtraResta).minutes();
                                            var horasExtra = Math.trunc(moment.duration(tiempoExtraResta).asHours());
                                            var tiempoExtra = moment.duration({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                            sobretiempoNormales = moment.duration(tiempoExtraResta);
                                            horasNormalesDiurnas = horasObligadas.clone();
                                            sumaSobreTiempoNormalesT = sumaSobreTiempoNormalesT.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                            var tiempoSobrante = {};
                                            if (horarioData.idDiurna == null) {
                                                if (tiempoExtra > moment.duration("02:00:00")) {
                                                    diurnas25 = moment.duration("02:00:00");
                                                    sumaHorasE25D = sumaHorasE25D.add({ "hours": 2 });
                                                    var restaDe25 = tiempoExtra - moment.duration("02:00:00");
                                                    var horasDe25 = Math.trunc(moment.duration(restaDe25).asHours());
                                                    var minutosDe25 = moment.duration(restaDe25).minutes();
                                                    var segundosDe25 = moment.duration(restaDe25).seconds();
                                                    tiempoSobrante = moment.duration({ "hours": horasDe25, "minutes": minutosDe25, "seconds": segundosDe25 });
                                                    if (tiempoSobrante > moment.duration("02:00:00")) {
                                                        diurnas35 = moment.duration("02:00:00");
                                                        sumaHorasE35D = sumaHorasE35D.add({ "hours": 2 });
                                                        var restaDe35 = tiempoSobrante - moment.duration("02:00:00");
                                                        var horasDe35 = Math.trunc(moment.duration(restaDe35).asHours());
                                                        var minutosDe35 = moment.duration(restaDe35).minutes();
                                                        var segundosDe35 = moment.duration(restaDe35).seconds();
                                                        tiempoSobrante = moment.duration({ "hours": horasDe35, "minutes": minutosDe35, "seconds": segundosDe35 });
                                                        if (tiempoSobrante > moment.duration(0)) {
                                                            diurnas100 = moment.duration(restaDe35);
                                                            sumaHorasE100D = sumaHorasE100D.add({ "hours": horasDe35, "minutes": minutosDe35, "seconds": segundosDe35 });
                                                        }
                                                    } else {
                                                        if (tiempoSobrante > moment.duration(0)) {
                                                            diurnas35 = moment.duration(restaDe25);
                                                            sumaHorasE35D = sumaHorasE35D.add({ "hours": horasDe25, "minutes": minutosDe25, "seconds": segundosDe25 });
                                                        }
                                                    }
                                                } else {
                                                    diurnas25 = moment.duration(tiempoExtraResta);
                                                    sumaHorasE25D = sumaHorasE25D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                }
                                            } else {
                                                // : CONDICIONAL DE 25% DIURNA
                                                // ! QUE NO LLENE EN EL 25
                                                if (!(horarioData.estado25D == 1)) {
                                                    // ! QUE NO SEA VACIO
                                                    if (horarioData.estado25D != 2) {
                                                        if (tiempoExtra > moment.duration("02:00:00")) {
                                                            diurnas25 = moment.duration("02:00:00");
                                                            sumaHorasE25D = sumaHorasE25D.add({ "hours": 2 });
                                                            tiempoExtraResta = moment.duration(tiempoExtra) - moment.duration("02:00:00");
                                                            horasExtra = Math.trunc(moment.duration(tiempoExtraResta).asHours());
                                                            minutosExtra = moment.duration(tiempoExtraResta).minutes();
                                                            segundosExtra = moment.duration(tiempoExtraResta).seconds();
                                                            tiempoExtra = moment.duration({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                        } else {
                                                            diurnas25 = tiempoExtra;
                                                            sumaHorasE25D = sumaHorasE25D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                            tiempoExtra = moment.duration(0);
                                                        }
                                                    }
                                                    // : CONDICIONAL DE 35% DIURNA
                                                    // ! QUE NO LLENE EN EL 35
                                                    if (!(horarioData.estado35D == 1)) {
                                                        if (horarioData.estado35D != 2) {
                                                            if (tiempoExtra > moment.duration("02:00:00")) {
                                                                diurnas35 = moment.duration("02:00:00");
                                                                sumaHorasE35D = sumaHorasE35D.add({ "hours": 2 });
                                                                tiempoExtraResta = moment.duration(tiempoExtra) - moment.duration("02:00:00");
                                                                horasExtra = Math.trunc(moment.duration(tiempoExtraResta).asHours());
                                                                minutosExtra = moment.duration(tiempoExtraResta).minutes();
                                                                segundosExtra = moment.duration(tiempoExtraResta).seconds();
                                                                tiempoExtra = moment.duration({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                            } else {
                                                                diurnas35 = tiempoExtra;
                                                                sumaHorasE35D = sumaHorasE35D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                                tiempoExtra = moment.duration(0);
                                                            }
                                                        }
                                                        // : CONDICIONAL DE 100% DIURNA
                                                        // ! QUE NO LLENA
                                                        if (!(horarioData.estado100D == 1)) {
                                                            // ! QUE NO SEA VACIO
                                                            if (horarioData.estado100D != 2) {
                                                                diurnas100 = moment.duration(tiempoExtra);
                                                                sumaHorasE100D = sumaHorasE100D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                            }
                                                        } else {
                                                            diurnas100 = moment.duration(tiempoExtra);
                                                            sumaHorasE100D = sumaHorasE100D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                        }
                                                    } else {
                                                        diurnas35 = moment.duration(tiempoExtra);
                                                        sumaHorasE35D = sumaHorasE35D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                    }
                                                } else {
                                                    diurnas25 = moment.duration(tiempoExtra);
                                                    sumaHorasE25D = sumaHorasE25D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                }
                                            }
                                            // : ********************************** HORAS EXTRAS NOCTURNAS ******************************
                                            if (sumaHorasNocturnas > nuevaHorasO) {
                                                // * HORAS EXTRAS
                                                var tiempoExtraRestaN = sumaHorasNocturnas - nuevaHorasO;
                                                var segundosExtraN = moment.duration(tiempoExtraRestaN).seconds();
                                                var minutosExtraN = moment.duration(tiempoExtraRestaN).minutes();
                                                var horasExtraN = Math.trunc(moment.duration(tiempoExtraRestaN).asHours());
                                                var tiempoExtraN = moment.duration({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                sobretiempoNocturnos = moment.duration(tiempoExtraRestaN);
                                                sumaSobreTiempoNocturnasT = sumaSobreTiempoNocturnasT.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                var tiempoSobranteN = {};
                                                if (horarioData.idNocturna == null) {
                                                    if (tiempoExtraN > moment.duration("02:00:00")) {
                                                        nocturnas25 = moment.duration("02:00:00");
                                                        sumaHorasE25N = sumaHorasE25N.add({ "hours": 2 });
                                                        var restaDe25N = tiempoExtraN - moment.duration("02:00:00");
                                                        var horasDe25N = Math.trunc(moment.duration(restaDe25N).asHours());
                                                        var minutosDe25N = moment.duration(restaDe25N).minutes();
                                                        var segundosDe25N = moment.duration(restaDe25N).seconds();
                                                        tiempoSobranteN = moment.duration({ "hours": horasDe25N, "minutes": minutosDe25N, "seconds": segundosDe25N });
                                                        if (tiempoSobranteN > moment.duration("02:00:00")) {
                                                            nocturnas35 = moment.duration("02:00:00");
                                                            sumaHorasE35N = sumaHorasE35N.add({ "hours": 2 });
                                                            var restaDe35N = tiempoSobranteN - moment.duration("02:00:00");
                                                            var horasDe35N = Math.trunc(moment.duration(restaDe35N).asHours());
                                                            var minutosDe35N = moment.duration(restaDe35N).minutes();
                                                            var segundosDe35N = moment.duration(restaDe35N).seconds();
                                                            tiempoSobranteN = moment.duration({ "hours": horasDe35N, "minutes": minutosDe35N, "seconds": segundosDe35N });
                                                            if (tiempoSobranteN > moment.duration(0)) {
                                                                nocturnas100 = moment.duration(restaDe35N);
                                                                sumaHorasE100N = sumaHorasE100N.add({ "hours": horasDe35N, "minutes": minutosDe35N, "seconds": segundosDe35N });
                                                            }
                                                        } else {
                                                            if (tiempoSobranteN > moment.duration(0)) {
                                                                sumaHorasE35N = sumaHorasE35N.add({ "hours": horasDe25N, "minutes": minutosDe25N, "seconds": segundosDe25N });
                                                                nocturnas35 = moment.duration(restaDe25N);
                                                            }
                                                        }
                                                    } else {
                                                        nocturnas25 = moment.duration(tiempoExtraRestaN);
                                                        sumaHorasE25N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                    }
                                                } else {
                                                    // : CONDICIONAL DE 25% NOCTURNA
                                                    // ! QUE NO LLENE EN EL 25
                                                    if (!(horarioData.estado25N == 1)) {
                                                        // ! QUE NO SEA VACIO
                                                        if (horarioData.estado25N != 2) {
                                                            if (tiempoExtraN > moment.duration("02:00:00")) {
                                                                nocturnas25 = moment.duration("02:00:00");
                                                                sumaHorasE25N = sumaHorasE25N.add({ "hours": 2 });
                                                                tiempoExtraRestaN = tiempoExtraN - moment.duration("02:00:00");
                                                                segundosExtraN = moment.duration(tiempoExtraRestaN).seconds();
                                                                minutosExtraN = moment.duration(tiempoExtraRestaN).minutes();
                                                                horasExtraN = Math.trunc(moment.duration(tiempoExtraRestaN).asHours());
                                                                tiempoExtraN = moment.duration({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                            } else {
                                                                nocturnas25 = tiempoExtraN;
                                                                sumaHorasE25N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                                tiempoExtraN = moment.duration(0);
                                                            }
                                                        }
                                                        // : CONDICIONAL DE 35% NOCTURNA
                                                        // ! QUE NO LLENE 35%
                                                        if (!(horarioData.estado35N == 1)) {
                                                            // ! QUE NO SEA VACIO
                                                            if (horarioData.estado35N != 2) {
                                                                if (tiempoExtraN > moment.duration("02:00:00")) {
                                                                    nocturnas35 = moment.duration("02:00:00");
                                                                    sumaHorasE35N = sumaHorasE25N.add({ "hours": 2 });
                                                                    tiempoExtraRestaN = tiempoExtraN - moment.duration("02:00:00");
                                                                    segundosExtraN = moment.duration(tiempoExtraRestaN).seconds();
                                                                    minutosExtraN = moment.duration(tiempoExtraRestaN).minutes();
                                                                    horasExtraN = Math.trunc(moment.duration(tiempoExtraRestaN).asHours());
                                                                    tiempoExtraN = moment.duration({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                                } else {
                                                                    nocturnas35 = tiempoExtraN;
                                                                    sumaHorasE35N = sumaHorasE35N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                                    tiempoExtraN = moment.duration(0);
                                                                }
                                                            }
                                                            // : CONDICIONAL DE 100% NOCTURNA
                                                            // ! QUE NO LLENA
                                                            if (!(horarioData.estado100N == 1)) {
                                                                // ! QUE NO SEA VACIO
                                                                if (horarioData.estado100D != 2) {
                                                                    nocturnas100 = tiempoExtraN;
                                                                    sumaHorasE100N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                                }
                                                            } else {
                                                                nocturnas100 = tiempoExtraN;
                                                                sumaHorasE100N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                            }
                                                        } else {
                                                            nocturnas35 = tiempoExtraN;
                                                            sumaHorasE35N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                        }
                                                    } else {
                                                        nocturnas25 = tiempoExtraN;
                                                        sumaHorasE25N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                    }
                                                }
                                            }
                                        } else {
                                            var restaHorasO = horasObligadas - sumaHorasNormales;
                                            nuevaHorasO = moment.duration(restaHorasO);
                                            horasNormalesDiurnas = sumaHorasNormales.clone();
                                            // : ****************************************  HORAS NOCTURNAS ********************
                                            if (sumaHorasNocturnas > nuevaHorasO) {
                                                // * HORAS EXTRAS
                                                var tiempoExtraRestaN = sumaHorasNocturnas - nuevaHorasO;
                                                var segundosExtraN = moment.duration(tiempoExtraRestaN).seconds();
                                                var minutosExtraN = moment.duration(tiempoExtraRestaN).minutes();
                                                var horasExtraN = Math.trunc(moment.duration(tiempoExtraRestaN).asHours());
                                                var tiempoExtraN = moment.duration({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                sobretiempoNocturnos = moment.duration(tiempoExtraRestaN);
                                                sumaSobreTiempoNocturnasT = sumaSobreTiempoNocturnasT.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                var tiempoSobranteN = {};
                                                if (horarioData.idNocturna == null) {
                                                    if (tiempoExtraN > moment.duration("02:00:00")) {
                                                        nocturnas25 = moment.duration("02:00:00");
                                                        sumaHorasE25N = sumaHorasE25N.add({ "hours": 2 });
                                                        var restaDe25N = tiempoExtraN - moment.duration("02:00:00");
                                                        var horasDe25N = Math.trunc(moment.duration(restaDe25N).asHours());
                                                        var minutosDe25N = moment.duration(restaDe25N).minutes();
                                                        var segundosDe25N = moment.duration(restaDe25N).seconds();
                                                        tiempoSobranteN = moment.duration({ "hours": horasDe25N, "minutes": minutosDe25N, "seconds": segundosDe25N });
                                                        if (tiempoSobranteN > moment.duration("02:00:00")) {
                                                            nocturnas35 = moment.duration("02:00:00");
                                                            sumaHorasE35N = sumaHorasE35N.add({ "hours": 2 });
                                                            var restaDe35N = tiempoSobranteN - moment.duration("02:00:00");
                                                            var horasDe35N = Math.trunc(moment.duration(restaDe35N).asHours());
                                                            var minutosDe35N = moment.duration(restaDe35N).minutes();
                                                            var segundosDe35N = moment.duration(restaDe35N).seconds();
                                                            tiempoSobranteN = moment.duration({ "hours": horasDe35N, "minutes": minutosDe35N, "seconds": segundosDe35N });
                                                            if (tiempoSobranteN > moment.duration(0)) {
                                                                nocturnas100 = moment.duration(restaDe35N);
                                                                sumaHorasE100N = sumaHorasE100N.add({ "hours": horasDe35N, "minutes": minutosDe35N, "seconds": segundosDe35N });
                                                            }
                                                        } else {
                                                            if (tiempoSobranteN > moment.duration(0)) {
                                                                sumaHorasE35N = sumaHorasE35N.add({ "hours": horasDe25N, "minutes": minutosDe25N, "seconds": segundosDe25N });
                                                                nocturnas35 = moment.duration(restaDe25N);
                                                            }
                                                        }
                                                    } else {
                                                        nocturnas25 = moment.duration(tiempoExtraRestaN);
                                                        sumaHorasE25N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                    }
                                                } else {
                                                    // : CONDICIONAL DE 25% NOCTURNA
                                                    // ! QUE NO LLENE EN EL 25
                                                    if (!(horarioData.estado25N == 1)) {
                                                        // ! QUE NO SEA VACIO
                                                        if (horarioData.estado25N != 2) {
                                                            if (tiempoExtraN > moment.duration("02:00:00")) {
                                                                nocturnas25 = moment.duration("02:00:00");
                                                                sumaHorasE25N = sumaHorasE25N.add({ "hours": 2 });
                                                                tiempoExtraRestaN = tiempoExtraN - moment.duration("02:00:00");
                                                                segundosExtraN = moment.duration(tiempoExtraRestaN).seconds();
                                                                minutosExtraN = moment.duration(tiempoExtraRestaN).minutes();
                                                                horasExtraN = Math.trunc(moment.duration(tiempoExtraRestaN).asHours());
                                                                tiempoExtraN = moment.duration({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                            } else {
                                                                nocturnas25 = tiempoExtraN;
                                                                sumaHorasE25N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                                tiempoExtraN = moment.duration(0);
                                                            }
                                                        }
                                                        // : CONDICIONAL DE 35% NOCTURNA
                                                        // ! QUE NO LLENE 35%
                                                        if (!(horarioData.estado35N == 1)) {
                                                            // ! QUE NO SEA VACIO
                                                            if (horarioData.estado35N != 2) {
                                                                if (tiempoExtraN > moment.duration("02:00:00")) {
                                                                    nocturnas35 = moment.duration("02:00:00");
                                                                    sumaHorasE35N = sumaHorasE25N.add({ "hours": 2 });
                                                                    tiempoExtraRestaN = tiempoExtraN - moment.duration("02:00:00");
                                                                    segundosExtraN = moment.duration(tiempoExtraRestaN).seconds();
                                                                    minutosExtraN = moment.duration(tiempoExtraRestaN).minutes();
                                                                    horasExtraN = Math.trunc(moment.duration(tiempoExtraRestaN).asHours());
                                                                    tiempoExtraN = moment.duration({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                                } else {
                                                                    nocturnas35 = tiempoExtraN;
                                                                    sumaHorasE35N = sumaHorasE35N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                                    tiempoExtraN = moment.duration(0);
                                                                }
                                                            }
                                                            // : CONDICIONAL DE 100% NOCTURNA
                                                            // ! QUE NO LLENA
                                                            if (!(horarioData.estado100N == 1)) {
                                                                // ! QUE NO SEA VACIO
                                                                if (horarioData.estado100D != 2) {
                                                                    nocturnas100 = tiempoExtraN;
                                                                    sumaHorasE100N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                                }
                                                            } else {
                                                                nocturnas100 = tiempoExtraN;
                                                                sumaHorasE100N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                            }
                                                        } else {
                                                            nocturnas35 = tiempoExtraN;
                                                            sumaHorasE35N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                        }
                                                    } else {
                                                        nocturnas25 = tiempoExtraN;
                                                        sumaHorasE25N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        var nuevaHorasO = {};
                                        // : ******************************************** HORAS NOCTURNAS ******************************
                                        if (sumaHorasNocturnas > horasObligadas) {
                                            nuevaHorasO = moment.duration(0);
                                            // : ************************************** HORAS EXTRAS NOCTURNAS *******************
                                            var tiempoExtraRestaN = sumaHorasNocturnas - horasObligadas;
                                            var segundosExtraN = moment.duration(tiempoExtraRestaN).seconds();
                                            var minutosExtraN = moment.duration(tiempoExtraRestaN).minutes();
                                            var horasExtraN = Math.trunc(moment.duration(tiempoExtraRestaN).asHours());
                                            var tiempoExtraN = moment.duration({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                            sobretiempoNocturnos = moment.duration(tiempoExtraRestaN);
                                            sumaSobreTiempoNocturnasT = sumaSobreTiempoNocturnasT.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                            var tiempoSobranteN = {};
                                            if (horarioData.idNocturna == null) {
                                                if (tiempoExtraN > moment.duration("02:00:00")) {
                                                    nocturnas25 = moment.duration("02:00:00");
                                                    sumaHorasE25N = sumaHorasE25N.add({ "hours": 2 });
                                                    var restaDe25N = tiempoExtraN - moment.duration("02:00:00");
                                                    var horasDe25N = Math.trunc(moment.duration(restaDe25N).asHours());
                                                    var minutosDe25N = moment.duration(restaDe25N).minutes();
                                                    var segundosDe25N = moment.duration(restaDe25N).seconds();
                                                    tiempoSobranteN = moment.duration({ "hours": horasDe25N, "minutes": minutosDe25N, "seconds": segundosDe25N });
                                                    if (tiempoSobranteN > moment.duration("02:00:00")) {
                                                        nocturnas35 = moment.duration("02:00:00");
                                                        sumaHorasE35N = sumaHorasE35N.add({ "hours": 2 });
                                                        var restaDe35N = tiempoSobranteN - moment.duration("02:00:00");
                                                        var horasDe35N = Math.trunc(moment.duration(restaDe35N).asHours());
                                                        var minutosDe35N = moment.duration(restaDe35N).minutes();
                                                        var segundosDe35N = moment.duration(restaDe35N).seconds();
                                                        tiempoSobranteN = moment.duration({ "hours": horasDe35N, "minutes": minutosDe35N, "seconds": segundosDe35N });
                                                        if (tiempoSobranteN > moment.duration(0)) {
                                                            nocturnas100 = moment.duration(restaDe35N);
                                                            sumaHorasE100N = sumaHorasE100N.add({ "hours": horasDe35N, "minutes": minutosDe35N, "seconds": segundosDe35N });
                                                        }
                                                    } else {
                                                        if (tiempoSobranteN > moment.duration(0)) {
                                                            nocturnas35 = moment.duration(restaDe25N);
                                                            sumaHorasE35N = sumaHorasE35N.add({ "hours": horasDe25N, "minutes": minutosDe25N, "seconds": segundosDe25N });
                                                        }
                                                    }
                                                } else {
                                                    nocturnas25 = moment.duration(tiempoExtraRestaN);
                                                    sumaHorasE25N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                }
                                            } else {
                                                // : CONDICIONAL DE 25% NOCTURNA
                                                // ! QUE NO LLENE EN EL 25
                                                if (!(horarioData.estado25N == 1)) {
                                                    // ! QUE NO SEA VACIO
                                                    if (horarioData.estado25N != 2) {
                                                        if (tiempoExtraN > moment.duration("02:00:00")) {
                                                            nocturnas25 = moment.duration("02:00:00");
                                                            sumaHorasE25N = sumaHorasE25N.add({ "hours": 2 });
                                                            tiempoExtraRestaN = tiempoExtraN - moment.duration("02:00:00");
                                                            segundosExtraN = moment.duration(tiempoExtraRestaN).seconds();
                                                            minutosExtraN = moment.duration(tiempoExtraRestaN).minutes();
                                                            horasExtraN = Math.trunc(moment.duration(tiempoExtraRestaN).asHours());
                                                            tiempoExtraN = moment.duration({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                        } else {
                                                            nocturnas25 = tiempoExtraN;
                                                            sumaHorasE25N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                            tiempoExtraN = moment.duration(0);
                                                        }
                                                    }
                                                    // : CONDICIONAL DE 35% NOCTURNA
                                                    // ! QUE NO LLENE 35%
                                                    if (!(horarioData.estado35N == 1)) {
                                                        // ! QUE NO SEA VACIO
                                                        if (horarioData.estado35N != 2) {
                                                            if (tiempoExtraN > moment.duration("02:00:00")) {
                                                                nocturnas35 = moment.duration("02:00:00");
                                                                sumaHorasE35N = sumaHorasE25N.add({ "hours": 2 });
                                                                tiempoExtraRestaN = tiempoExtraN - moment.duration("02:00:00");
                                                                segundosExtraN = moment.duration(tiempoExtraRestaN).seconds();
                                                                minutosExtraN = moment.duration(tiempoExtraRestaN).minutes();
                                                                horasExtraN = Math.trunc(moment.duration(tiempoExtraRestaN).asHours());
                                                                tiempoExtraN = moment.duration({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                            } else {
                                                                nocturnas35 = tiempoExtraN;
                                                                sumaHorasE35N = sumaHorasE35N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                                tiempoExtraN = moment.duration(0);
                                                            }
                                                        }
                                                        // : CONDICIONAL DE 100% NOCTURNA
                                                        // ! QUE NO LLENA
                                                        if (!(horarioData.estado100N == 1)) {
                                                            // ! QUE NO SEA VACIO
                                                            if (horarioData.estado100D != 2) {
                                                                nocturnas100 = tiempoExtraN;
                                                                sumaHorasE100N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                            }
                                                        } else {
                                                            nocturnas100 = tiempoExtraN;
                                                            sumaHorasE100N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                        }
                                                    } else {
                                                        nocturnas35 = tiempoExtraN;
                                                        sumaHorasE35N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                    }
                                                } else {
                                                    nocturnas25 = tiempoExtraN;
                                                    sumaHorasE25N = sumaHorasE25N.add({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN });
                                                }
                                            }
                                            // : *************************************** HORAS NORMALES ********************************
                                            if (sumaHorasNormales > nuevaHorasO) {
                                                // * HORAS EXTRAS
                                                var tiempoExtraResta = sumaHorasNormales - nuevaHorasO;
                                                var segundosExtra = moment.duration(tiempoExtraResta).seconds();
                                                var minutosExtra = moment.duration(tiempoExtraResta).minutes();
                                                var horasExtra = Math.trunc(moment.duration(tiempoExtraResta).asHours());
                                                var tiempoExtra = moment.duration({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                sobretiempoNormales = moment.duration(tiempoExtraResta);
                                                horasNormalesDiurnas = nuevaHorasO.clone();
                                                sumaSobreTiempoNormalesT = sumaSobreTiempoNormalesT.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                var tiempoSobrante = {};
                                                if (horarioData.idDiurna == null) {
                                                    if (tiempoExtra > moment.duration("02:00:00")) {
                                                        diurnas25 = moment.duration("02:00:00");
                                                        sumaHorasE25D = sumaHorasE25D.add({ "hours": 2 });
                                                        var restaDe25 = tiempoExtra - moment.duration("02:00:00");
                                                        var horasDe25 = Math.trunc(moment.duration(restaDe25).asHours());
                                                        var minutosDe25 = moment.duration(restaDe25).minutes();
                                                        var segundosDe25 = moment.duration(restaDe25).seconds();
                                                        tiempoSobrante = moment.duration({ "hours": horasDe25, "minutes": minutosDe25, "seconds": segundosDe25 });
                                                        if (tiempoSobrante > moment.duration("02:00:00")) {
                                                            diurnas35 = moment.duration("02:00:00");
                                                            sumaHorasE35D = sumaHorasE35D.add({ "hours": 2 });
                                                            var restaDe35 = tiempoSobrante - moment.duration("02:00:00");
                                                            var horasDe35 = Math.trunc(moment.duration(restaDe35).asHours());
                                                            var minutosDe35 = moment.duration(restaDe35).minutes();
                                                            var segundosDe35 = moment.duration(restaDe35).seconds();
                                                            tiempoSobrante = moment.duration({ "hours": horasDe35, "minutes": minutosDe35, "seconds": segundosDe35 });
                                                            if (tiempoSobrante > moment.duration(0)) {
                                                                diurnas100 = moment.duration(restaDe35);
                                                                sumaHorasE100D = sumaHorasE100D.add({ "hours": horasDe35, "minutes": minutosDe35, "seconds": segundosDe35 });
                                                            }
                                                        } else {
                                                            if (tiempoSobrante > moment.duration(0)) {
                                                                diurnas35 = moment.duration(restaDe25);
                                                                sumaHorasE35D = sumaHorasE35D.add({ "hours": horasDe25, "minutes": minutosDe25, "seconds": segundosDe25 });
                                                            }
                                                        }
                                                    } else {
                                                        diurnas25 = moment.duration(tiempoExtraResta);
                                                        sumaHorasE25D = sumaHorasE25D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                    }
                                                } else {
                                                    // : CONDICIONAL DE 25% DIURNA
                                                    // ! QUE NO LLENE EN EL 25
                                                    if (!(horarioData.estado25D == 1)) {
                                                        // ! QUE NO SEA VACIO
                                                        if (horarioData.estado25D != 2) {
                                                            if (tiempoExtra > moment.duration("02:00:00")) {
                                                                diurnas25 = moment.duration("02:00:00");
                                                                sumaHorasE25D = sumaHorasE25D.add({ "hours": 2 });
                                                                tiempoExtraResta = moment.duration(tiempoExtra) - moment.duration("02:00:00");
                                                                horasExtra = Math.trunc(moment.duration(tiempoExtraResta).asHours());
                                                                minutosExtra = moment.duration(tiempoExtraResta).minutes();
                                                                segundosExtra = moment.duration(tiempoExtraResta).seconds();
                                                                tiempoExtra = moment.duration({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                            } else {
                                                                diurnas25 = tiempoExtra;
                                                                sumaHorasE25D = sumaHorasE25D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                                tiempoExtra = moment.duration(0);
                                                            }
                                                        }
                                                        // : CONDICIONAL DE 35% DIURNA
                                                        // ! QUE NO LLENE EN EL 35
                                                        if (!(horarioData.estado35D == 1)) {
                                                            if (horarioData.estado35D != 2) {
                                                                if (tiempoExtra > moment.duration("02:00:00")) {
                                                                    diurnas35 = moment.duration("02:00:00");
                                                                    sumaHorasE35D = sumaHorasE35D.add({ "hours": 2 });
                                                                    tiempoExtraResta = moment.duration(tiempoExtra) - moment.duration("02:00:00");
                                                                    horasExtra = Math.trunc(moment.duration(tiempoExtraResta).asHours());
                                                                    minutosExtra = moment.duration(tiempoExtraResta).minutes();
                                                                    segundosExtra = moment.duration(tiempoExtraResta).seconds();
                                                                    tiempoExtra = moment.duration({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                                } else {
                                                                    diurnas35 = tiempoExtra;
                                                                    sumaHorasE35D = sumaHorasE35D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                                    tiempoExtra = moment.duration(0);
                                                                }
                                                            }
                                                            // : CONDICIONAL DE 100% DIURNA
                                                            // ! QUE NO LLENA
                                                            if (!(horarioData.estado100D == 1)) {
                                                                // ! QUE NO SEA VACIO
                                                                if (horarioData.estado100D != 2) {
                                                                    diurnas100 = moment.duration(tiempoExtra);
                                                                    sumaHorasE100D = sumaHorasE100D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                                }
                                                            } else {
                                                                diurnas100 = moment.duration(tiempoExtra);
                                                                sumaHorasE100D = sumaHorasE100D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                            }
                                                        } else {
                                                            diurnas35 = moment.duration(tiempoExtra);
                                                            sumaHorasE35D = sumaHorasE35D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                        }
                                                    } else {
                                                        diurnas25 = moment.duration(tiempoExtra);
                                                        sumaHorasE25D = sumaHorasE25D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                    }
                                                }
                                            } else {
                                                horasNormalesDiurnas = nuevaHorasO.clone();
                                            }
                                        } else {
                                            var restaHorasO = horasObligadas - sumaHorasNocturnas;
                                            nuevaHorasO = moment.duration(restaHorasO);
                                            // : *************************************** HORAS NORMALES ********************************
                                            if (sumaHorasNormales > nuevaHorasO) {
                                                // * HORAS EXTRAS
                                                var tiempoExtraResta = sumaHorasNormales - nuevaHorasO;
                                                var segundosExtra = moment.duration(tiempoExtraResta).seconds();
                                                var minutosExtra = moment.duration(tiempoExtraResta).minutes();
                                                var horasExtra = Math.trunc(moment.duration(tiempoExtraResta).asHours());
                                                var tiempoExtra = moment.duration({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                sobretiempoNormales = moment.duration(tiempoExtraResta);
                                                horasNormalesDiurnas = sumaHorasNormales.clone();
                                                sumaSobreTiempoNormalesT = sumaSobreTiempoNormalesT.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                var tiempoSobrante = {};
                                                if (horarioData.idDiurna == null) {
                                                    if (tiempoExtra > moment.duration("02:00:00")) {
                                                        diurnas25 = moment.duration("02:00:00");
                                                        sumaHorasE25D = sumaHorasE25D.add({ "hours": 2 });
                                                        var restaDe25 = tiempoExtra - moment.duration("02:00:00");
                                                        var horasDe25 = Math.trunc(moment.duration(restaDe25).asHours());
                                                        var minutosDe25 = moment.duration(restaDe25).minutes();
                                                        var segundosDe25 = moment.duration(restaDe25).seconds();
                                                        tiempoSobrante = moment.duration({ "hours": horasDe25, "minutes": minutosDe25, "seconds": segundosDe25 });
                                                        if (tiempoSobrante > moment.duration("02:00:00")) {
                                                            diurnas35 = moment.duration("02:00:00");
                                                            sumaHorasE35D = sumaHorasE35D.add({ "hours": 2 });
                                                            var restaDe35 = tiempoSobrante - moment.duration("02:00:00");
                                                            var horasDe35 = Math.trunc(moment.duration(restaDe35).asHours());
                                                            var minutosDe35 = moment.duration(restaDe35).minutes();
                                                            var segundosDe35 = moment.duration(restaDe35).seconds();
                                                            tiempoSobrante = moment.duration({ "hours": horasDe35, "minutes": minutosDe35, "seconds": segundosDe35 });
                                                            if (tiempoSobrante > moment.duration(0)) {
                                                                diurnas100 = moment.duration(restaDe35);
                                                                sumaHorasE100D = sumaHorasE100D.add({ "hours": horasDe35, "minutes": minutosDe35, "seconds": segundosDe35 });
                                                            }
                                                        } else {
                                                            if (tiempoSobrante > moment.duration(0)) {
                                                                diurnas35 = moment.duration(restaDe25);
                                                                sumaHorasE35D = sumaHorasE35D.add({ "hours": horasDe25, "minutes": minutosDe25, "seconds": segundosDe25 });
                                                            }
                                                        }
                                                    } else {
                                                        diurnas25 = moment.duration(tiempoExtraResta);
                                                        sumaHorasE25D = sumaHorasE25D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                    }
                                                } else {
                                                    // : CONDICIONAL DE 25% DIURNA
                                                    // ! QUE NO LLENE EN EL 25
                                                    if (!(horarioData.estado25D == 1)) {
                                                        // ! QUE NO SEA VACIO
                                                        if (horarioData.estado25D != 2) {
                                                            if (tiempoExtra > moment.duration("02:00:00")) {
                                                                diurnas25 = moment.duration("02:00:00");
                                                                sumaHorasE25D = sumaHorasE25D.add({ "hours": 2 });
                                                                tiempoExtraResta = tiempoExtra - moment.duration("02:00:00");
                                                                horasExtra = Math.trunc(moment.duration(tiempoExtraResta).asHours());
                                                                minutosExtra = moment.duration(tiempoExtraResta).minutes();
                                                                segundosExtra = moment.duration(tiempoExtraResta).seconds();
                                                                tiempoExtra = moment.duration({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                            } else {
                                                                diurnas25 = tiempoExtra;
                                                                sumaHorasE25D = sumaHorasE25D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                                tiempoExtra = moment.duration(0);
                                                            }
                                                        }
                                                        // : CONDICIONAL DE 35% DIURNA
                                                        // ! QUE NO LLENE EN EL 35
                                                        if (!(horarioData.estado35D == 1)) {
                                                            if (horarioData.estado35D != 2) {
                                                                if (tiempoExtra > moment.duration("02:00:00")) {
                                                                    diurnas35 = moment.duration("02:00:00");
                                                                    sumaHorasE35D = sumaHorasE35D.add({ "hours": 2 });
                                                                    tiempoExtraResta = tiempoExtra - moment.duration("02:00:00");
                                                                    horasExtra = Math.trunc(moment.duration(tiempoExtraResta).asHours());
                                                                    minutosExtra = moment.duration(tiempoExtraResta).minutes();
                                                                    segundosExtra = moment.duration(tiempoExtraResta).seconds();
                                                                    tiempoExtra = moment.duration({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                                } else {
                                                                    diurnas35 = tiempoExtra;
                                                                    sumaHorasE35D = sumaHorasE35D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                                    tiempoExtra = moment.duration(0);
                                                                }
                                                            }
                                                            // : CONDICIONAL DE 100% DIURNA
                                                            // ! QUE NO LLENA
                                                            if (!(horarioData.estado100D == 1)) {
                                                                // ! QUE NO SEA VACIO
                                                                if (horarioData.estado100D != 2) {
                                                                    diurnas100 = tiempoExtra;
                                                                    sumaHorasE100D = sumaHorasE100D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                                }
                                                            } else {
                                                                diurnas100 = tiempoExtra;
                                                                sumaHorasE100D = sumaHorasE100D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                            }
                                                        } else {
                                                            diurnas35 = tiempoExtra;
                                                            sumaHorasE35D = sumaHorasE35D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                        }
                                                    } else {
                                                        diurnas25 = tiempoExtra;
                                                        sumaHorasE25D = sumaHorasE25D.add({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra });
                                                    }
                                                }
                                            } else {
                                                horasNormalesDiurnas = nuevaHorasO.clone();
                                            }
                                        }
                                    }
                                }
                            }
                            // : HORA SUMA DE HORAS NOCTURNAS
                            var horaSumaHorasNocturnas = Math.trunc(moment.duration(sumaHorasNocturnas).asHours());
                            var minutoSumaHorasNocturnas = moment.duration(sumaHorasNocturnas).minutes();
                            var segundoSumaHorasNocturnas = moment.duration(sumaHorasNocturnas).seconds();
                            if (horaSumaHorasNocturnas < 10) {
                                horaSumaHorasNocturnas = "0" + horaSumaHorasNocturnas;
                            }
                            if (minutoSumaHorasNocturnas < 10) {
                                minutoSumaHorasNocturnas = "0" + minutoSumaHorasNocturnas;
                            }
                            if (segundoSumaHorasNocturnas < 10) {
                                segundoSumaHorasNocturnas = "0" + segundoSumaHorasNocturnas;
                            }
                            // : HORA SUMA DE HORAS NORMALES
                            var horaSumaHorasNormales = Math.trunc(moment.duration(sumaHorasNormales).asHours());
                            var minutoSumaHorasNormales = moment.duration(sumaHorasNormales).minutes();
                            var segundoSumaHorasNormales = moment.duration(sumaHorasNormales).seconds();
                            if (horaSumaHorasNormales < 10) {
                                horaSumaHorasNormales = "0" + horaSumaHorasNormales;
                            }
                            if (minutoSumaHorasNormales < 10) {
                                minutoSumaHorasNormales = "0" + minutoSumaHorasNormales;
                            }
                            if (segundoSumaHorasNormales < 10) {
                                segundoSumaHorasNormales = "0" + segundoSumaHorasNormales;
                            }
                            // : SOBRE TIEMPO NORMALES
                            var horaSobretiempoNormales = Math.trunc(moment.duration(sobretiempoNormales).asHours());
                            var minutoSobretiempoNormales = moment.duration(sobretiempoNormales).minutes();
                            var segundoSobretiempoNormales = moment.duration(sobretiempoNormales).seconds();
                            if (horaSobretiempoNormales < 10) {
                                horaSobretiempoNormales = "0" + horaSobretiempoNormales;
                            }
                            if (minutoSobretiempoNormales < 10) {
                                minutoSobretiempoNormales = "0" + minutoSobretiempoNormales;
                            }
                            if (segundoSobretiempoNormales < 10) {
                                segundoSobretiempoNormales = "0" + segundoSobretiempoNormales;
                            }
                            // : SOBRE TIEMPO NOCTURNOS
                            var horaSobretiempoNocturnos = Math.trunc(moment.duration(sobretiempoNocturnos).asHours());
                            var minutoSobretiempoNocturnos = moment.duration(sobretiempoNocturnos).minutes();
                            var segundoSobretiempoNocturnos = moment.duration(sobretiempoNocturnos).seconds();
                            if (horaSobretiempoNocturnos < 10) {
                                horaSobretiempoNocturnos = "0" + horaSobretiempoNocturnos;
                            }
                            if (minutoSobretiempoNocturnos < 10) {
                                minutoSobretiempoNocturnos = "0" + minutoSobretiempoNocturnos;
                            }
                            if (segundoSobretiempoNocturnos < 10) {
                                segundoSobretiempoNocturnos = "0" + segundoSobretiempoNocturnos;
                            }
                            // : SUMA TIEMPOS ENTRE HORARIOS
                            var horaSumaTiemposEntreHorario = Math.trunc(moment.duration(sumaTiemposEntreHorarios).asHours());
                            var minutoSumaTiemposEntreHorario = moment.duration(sumaTiemposEntreHorarios).minutes();
                            var segundoSumaTiemposEntreHorario = moment.duration(sumaTiemposEntreHorarios).seconds();
                            if (horaSumaTiemposEntreHorario < 10) {
                                horaSumaTiemposEntreHorario = "0" + horaSumaTiemposEntreHorario;
                            }
                            if (minutoSumaTiemposEntreHorario < 10) {
                                minutoSumaTiemposEntreHorario = "0" + minutoSumaTiemposEntreHorario;
                            }
                            if (segundoSumaTiemposEntreHorario < 10) {
                                segundoSumaTiemposEntreHorario = "0" + segundoSumaTiemposEntreHorario;
                            }
                            // : DIURNAS 25
                            var horaDiurna25 = Math.trunc(moment.duration(diurnas25).asHours());
                            var minutoDiurna25 = moment.duration(diurnas25).minutes();
                            var segundoDiurna25 = moment.duration(diurnas25).seconds();
                            if (horaDiurna25 < 10) {
                                horaDiurna25 = "0" + horaDiurna25;
                            }
                            if (minutoDiurna25 < 10) {
                                minutoDiurna25 = "0" + minutoDiurna25;
                            }
                            if (segundoDiurna25 < 10) {
                                segundoDiurna25 = "0" + segundoDiurna25;
                            }
                            // : DIURNAS 35
                            var horaDiurna35 = Math.trunc(moment.duration(diurnas35).asHours());
                            var minutoDiurna35 = moment.duration(diurnas35).minutes();
                            var segundoDiurna35 = moment.duration(diurnas35).seconds();
                            if (horaDiurna35 < 10) {
                                horaDiurna35 = "0" + horaDiurna35;
                            }
                            if (minutoDiurna35 < 10) {
                                minutoDiurna35 = "0" + minutoDiurna35;
                            }
                            if (segundoDiurna35 < 10) {
                                segundoDiurna35 = "0" + segundoDiurna35;
                            }
                            // : DIURNAS 100
                            var horaDiurna100 = Math.trunc(moment.duration(diurnas100).asHours());
                            var minutoDiurna100 = moment.duration(diurnas100).minutes();
                            var segundoDiurna100 = moment.duration(diurnas100).seconds();
                            if (horaDiurna100 < 10) {
                                horaDiurna100 = "0" + horaDiurna100;
                            }
                            if (minutoDiurna100 < 10) {
                                minutoDiurna100 = "0" + minutoDiurna100;
                            }
                            if (segundoDiurna100 < 10) {
                                segundoDiurna100 = "0" + segundoDiurna100;
                            }
                            // : NOCTURNA 25
                            var horaNocturna25 = Math.trunc(moment.duration(nocturnas25).asHours());
                            var minutoNocturna25 = moment.duration(nocturnas25).minutes();
                            var segundoNocturna25 = moment.duration(nocturnas25).seconds();
                            if (horaNocturna25 < 10) {
                                horaNocturna25 = "0" + horaNocturna25;
                            }
                            if (minutoNocturna25 < 10) {
                                minutoNocturna25 = "0" + minutoNocturna25;
                            }
                            if (segundoNocturna25 < 10) {
                                segundoNocturna25 = "0" + segundoNocturna25;
                            }
                            // : NOCTURNA 35
                            var horaNocturna35 = Math.trunc(moment.duration(nocturnas35).asHours());
                            var minutoNocturna35 = moment.duration(nocturnas35).minutes();
                            var segundoNocturna35 = moment.duration(nocturnas35).seconds();
                            if (horaNocturna35 < 10) {
                                horaNocturna35 = "0" + horaNocturna35;
                            }
                            if (minutoNocturna35 < 10) {
                                minutoNocturna35 = "0" + minutoNocturna35;
                            }
                            if (segundoNocturna35 < 10) {
                                segundoNocturna35 = "0" + segundoNocturna35;
                            }
                            // : NOCTURNA 100
                            var horaNocturna100 = Math.trunc(moment.duration(nocturnas100).asHours());
                            var minutoNocturna100 = moment.duration(nocturnas100).minutes();
                            var segundoNocturna100 = moment.duration(nocturnas100).seconds();
                            if (horaNocturna100 < 10) {
                                horaNocturna100 = "0" + horaNocturna100;
                            }
                            if (minutoNocturna100 < 10) {
                                minutoNocturna100 = "0" + minutoNocturna100;
                            }
                            if (segundoNocturna100 < 10) {
                                segundoNocturna100 = "0" + segundoNocturna100;
                            }
                            // : TIEMPO MUERTO EN ENTRADA
                            var horaTiempoMuertoEntrada = Math.trunc(moment.duration(tiempoMuertoEntrada).asHours());
                            var minutoTiempoMuertoEntrada = moment.duration(tiempoMuertoEntrada).minutes();
                            var segundoTiempoMuertoEntrada = moment.duration(tiempoMuertoEntrada).seconds();
                            if (horaTiempoMuertoEntrada < 10) {
                                horaTiempoMuertoEntrada = "0" + horaTiempoMuertoEntrada;
                            }
                            if (minutoTiempoMuertoEntrada < 10) {
                                minutoTiempoMuertoEntrada = "0" + minutoTiempoMuertoEntrada;
                            }
                            if (segundoTiempoMuertoEntrada < 10) {
                                segundoTiempoMuertoEntrada = "0" + segundoTiempoMuertoEntrada;
                            }
                            // : TIEMPO MUERTO EN SALIDA
                            var horaTiempoMuertoSalida = Math.trunc(moment.duration(tiempoMuertoSalida).asHours());
                            var minutoTiempoMuertoSalida = moment.duration(tiempoMuertoSalida).minutes();
                            var segundoTiempoMuertoSalida = moment.duration(tiempoMuertoSalida).seconds();
                            if (horaTiempoMuertoSalida < 10) {
                                horaTiempoMuertoSalida = "0" + horaTiempoMuertoSalida;
                            }
                            if (minutoTiempoMuertoSalida < 10) {
                                minutoTiempoMuertoSalida = "0" + minutoTiempoMuertoSalida;
                            }
                            if (segundoTiempoMuertoSalida < 10) {
                                segundoTiempoMuertoSalida = "0" + segundoTiempoMuertoSalida;
                            }
                            // : TIEMPOS NORMALES DE DIURNAS
                            var horaHorasNormalesDiurnas = Math.trunc(moment.duration(horasNormalesDiurnas).asHours());
                            var minutoHorasNormalesDiurnas = moment.duration(horasNormalesDiurnas).minutes();
                            var segundoHorasNormalesDiurnas = moment.duration(horasNormalesDiurnas).seconds();
                            if (horaHorasNormalesDiurnas < 10) {
                                horaHorasNormalesDiurnas = "0" + horaHorasNormalesDiurnas;
                            }
                            if (minutoHorasNormalesDiurnas < 10) {
                                minutoHorasNormalesDiurnas = "0" + minutoHorasNormalesDiurnas;
                            }
                            if (segundoHorasNormalesDiurnas < 10) {
                                segundoHorasNormalesDiurnas = "0" + segundoHorasNormalesDiurnas;
                            }
                            // : TIEMPOS NORMALES DE NOCTURNAS
                            var horaHorasNormalesNocturnas = Math.trunc(moment.duration(horasNormalesNocturnas).asHours());
                            var minutoHorasNormalesNocturnas = moment.duration(horasNormalesNocturnas).minutes();
                            var segundoHorasNormalesNocturnas = moment.duration(horasNormalesNocturnas).seconds();
                            if (horaHorasNormalesNocturnas < 10) {
                                horaHorasNormalesNocturnas = "0" + horaHorasNormalesNocturnas;
                            }
                            if (minutoHorasNormalesNocturnas < 10) {
                                minutoHorasNormalesNocturnas = "0" + minutoHorasNormalesNocturnas;
                            }
                            if (segundoHorasNormalesNocturnas < 10) {
                                segundoHorasNormalesNocturnas = "0" + segundoHorasNormalesNocturnas;
                            }
                            if (horarioData.horario != null) {
                                if (horarioData.estado == 1) {
                                    grupoHorario += `<td style="border-left: 2px solid #383e56!important;background: #fafafa;border-right: 1px dashed #c8d4de!important;" class="text-center" name="descripcionHorario">
                                                    <a class="btn" type="button" style="padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                        <span class="badge badge-soft-primary mr-2" class="text-center">
                                                            ${horarioData.horario}
                                                        </span>
                                                    </a>
                                                </td>
                                                <td name="horarioHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    ${moment(horarioData.horarioIni).format("HH:mm:ss")} - ${moment(horarioData.horarioFin).format("HH:mm:ss")}
                                                </td>
                                                <td class="text-center" name="toleranciaIHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">${horarioData.toleranciaI} min.</td>
                                                <td class="text-center" name="toleranciaFHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">${horarioData.toleranciaF} min.</td>
                                                <td name="colTiempoEntreH" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-primary mr-2">
                                                        <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                        ${horaSumaTiemposEntreHorario}:${minutoSumaTiemposEntreHorario}:${segundoSumaTiemposEntreHorario}
                                                    </a>
                                                </td>
                                                <td name="colTiempoMuertoEntrada" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                                                    ${horaTiempoMuertoEntrada}:${minutoTiempoMuertoEntrada}:${segundoTiempoMuertoEntrada}
                                                </td>
                                                <td name="colTiempoMuertoSalida" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                                                    ${horaTiempoMuertoSalida}:${minutoTiempoMuertoSalida}:${segundoTiempoMuertoSalida}
                                                </td>
                                                <td name="colSobreTiempo" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-primary mr-2">
                                                        <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                        ${horasSobreT}:${minutosSobreT}:${segundosSobreT}
                                                    </a>
                                                </td>
                                                <td name="colHoraNormal" class="text-center colHoraNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-warning mr-2">
                                                        <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                        ${horaSumaHorasNormales}:${minutoSumaHorasNormales}:${segundoSumaHorasNormales}
                                                    </a>
                                                </td>
                                                <td name="colHoraDiurnaNormal" class="text-center colHoraDiurnaNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-warning mr-2">
                                                        <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                        ${horaHorasNormalesDiurnas}:${minutoHorasNormalesDiurnas}:${segundoHorasNormalesDiurnas}
                                                    </a>
                                                </td>
                                                <td name="colSobreTNormal" class="text-center colSobreTNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-warning mr-2">
                                                        <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                        ${horaSobretiempoNormales}:${minutoSobretiempoNormales}:${segundoSobretiempoNormales}
                                                    </a>
                                                </td>
                                                <td name="colHE25D" class="text-center colHE25D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/timerD.svg" height="20" class="mr-2">
                                                    ${horaDiurna25}:${minutoDiurna25}:${segundoDiurna25}
                                                </td>
                                                <td name="colHE35D" class="text-center colHE35D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/timerD.svg" height="20" class="mr-2">
                                                    ${horaDiurna35}:${minutoDiurna35}:${segundoDiurna35}
                                                </td>
                                                <td name="colHE100D" class="text-center colHE100D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/timerD.svg" height="20" class="mr-2">
                                                    ${horaDiurna100}:${minutoDiurna100}:${segundoDiurna100}
                                                </td>
                                                <td name="colHoraNocturna" class="text-center colHoraNocturna" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-info mr-2">
                                                        <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                        ${horaSumaHorasNocturnas}:${minutoSumaHorasNocturnas}:${segundoSumaHorasNocturnas}
                                                    </a>
                                                </td>
                                                <td name="colHoraNocturnaNormal" class="text-center colHoraNocturnaNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-info mr-2">
                                                        <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                        ${horaHorasNormalesNocturnas}:${minutoHorasNormalesNocturnas}:${segundoHorasNormalesNocturnas}
                                                    </a>
                                                </td>
                                                <td name="colSobreTNocturno" class="text-center colSobreTNocturno" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-info mr-2">
                                                        <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                        ${horaSobretiempoNocturnos}:${minutoSobretiempoNocturnos}:${segundoSobretiempoNocturnos}
                                                    </a>
                                                </td>
                                                <td name="colHE25N" class="text-center colHE25N" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/timer.svg" height="20" class="mr-2">
                                                    ${horaNocturna25}:${minutoNocturna25}:${segundoNocturna25}
                                                </td>
                                                <td name="colHE35N" class="text-center colHE35N" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/timer.svg" height="20" class="mr-2">
                                                    ${horaNocturna35}:${minutoNocturna35}:${segundoNocturna35}
                                                </td>
                                                <td name="colHE100N" class="text-center colHE100N" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/timer.svg" height="20" class="mr-2">
                                                    ${horaNocturna100}:${minutoNocturna100}:${segundoNocturna100}
                                                </td>
                                                <td name="colFaltaJornada" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-danger mr-2">
                                                        <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                        ${horasFaltaJ}:${minutosFaltaJ}:${segundosFaltaJ}
                                                    </a>
                                                </td>
                                                <td name="colTardanza" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-danger mr-2">
                                                        <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                        ${horasTardanza}:${minutosTardanza}:${segundosTardanza}
                                                    </a>
                                                </td>`;
                                    if (data[key][index].data[m].marcaciones.length == 0 && data[key][index].incidencias.length == 0) {
                                        sumaFaltas++;
                                        grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                        <span class="badge badge-soft-danger mr-2" class="text-center">
                                                            Falta
                                                        </span>
                                                    </td>`;
                                    } else {
                                        grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>`;
                                    }
                                } else {
                                    grupoHorario += `<td style="border-left: 2px solid #383e56!important;background: #fafafa;border-right: 1px dashed #c8d4de!important;" class="text-center" name="descripcionHorario">
                                                    <a class="btn" type="button" style="padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                        <span class="badge badge-soft-danger mr-2" class="text-center">
                                                            ${horarioData.horario}
                                                        </span>
                                                    </a>
                                                </td>
                                                <td name="horarioHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    ${moment(horarioData.horarioIni).format("HH:mm:ss")} - ${moment(horarioData.horarioFin).format("HH:mm:ss")}
                                                </td>
                                                <td class="text-center" name="toleranciaIHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">${horarioData.toleranciaI} min.</td>
                                                <td class="text-center" name="toleranciaFHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">${horarioData.toleranciaF} min.</td>
                                                <td name="colTiempoEntreH" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-primary mr-2">
                                                        <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                        ${horaSumaTiemposEntreHorario}:${minutoSumaTiemposEntreHorario}:${segundoSumaTiemposEntreHorario}
                                                    </a>
                                                </td>
                                                <td name="colTiempoMuertoEntrada" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                                                    ${horaTiempoMuertoEntrada}:${minutoTiempoMuertoEntrada}:${segundoTiempoMuertoEntrada}
                                                </td>
                                                <td name="colTiempoMuertoSalida" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                                                    ${horaTiempoMuertoSalida}:${minutoTiempoMuertoSalida}:${segundoTiempoMuertoSalida}
                                                </td>
                                                <td name="colSobreTiempo" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-primary mr-2">
                                                        <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                        ${horasSobreT}:${minutosSobreT}:${segundosSobreT}
                                                    </a>
                                                </td>
                                                <td name="colHoraNormal" class="text-center colHoraNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-warning mr-2">
                                                        <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                        ${horaSumaHorasNormales}:${minutoSumaHorasNormales}:${segundoSumaHorasNormales}
                                                    </a>
                                                </td>
                                                <td name="colHoraDiurnaNormal" class="text-center colHoraDiurnaNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-warning mr-2">
                                                        <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                        ${horaHorasNormalesDiurnas}:${minutoHorasNormalesDiurnas}:${segundoHorasNormalesDiurnas}
                                                    </a>
                                                </td>
                                                <td name="colSobreTNormal" class="text-center colSobreTNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-warning mr-2">
                                                        <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                        ${horaSobretiempoNormales}:${minutoSobretiempoNormales}:${segundoSobretiempoNormales}
                                                    </a>
                                                </td>
                                                <td name="colHE25D" class="text-center colHE25D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/timerD.svg" height="20" class="mr-2">
                                                    ${horaDiurna25}:${minutoDiurna25}:${segundoDiurna25}
                                                </td>
                                                <td name="colHE35D" class="text-center colHE35D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/timerD.svg" height="20" class="mr-2">
                                                    ${horaDiurna35}:${minutoDiurna35}:${segundoDiurna35}
                                                </td>
                                                <td name="colHE100D" class="text-center colHE100D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/timerD.svg" height="20" class="mr-2">
                                                    ${horaDiurna100}:${minutoDiurna100}:${segundoDiurna100}
                                                </td>
                                                <td name="colHoraNocturna" class="text-center colHoraNocturna" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-info mr-2">
                                                        <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                        ${horaSumaHorasNocturnas}:${minutoSumaHorasNocturnas}:${segundoSumaHorasNocturnas}
                                                    </a>
                                                </td>
                                                <td name="colHoraNocturnaNormal" class="text-center colHoraNocturnaNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-info mr-2">
                                                        <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                        ${horaHorasNormalesNocturnas}:${minutoHorasNormalesNocturnas}:${segundoHorasNormalesNocturnas}
                                                    </a>
                                                </td>
                                                <td name="colSobreTNocturno" class="text-center colSobreTNocturno" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-info mr-2">
                                                        <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                        ${horaSobretiempoNocturnos}:${minutoSobretiempoNocturnos}:${segundoSobretiempoNocturnos}
                                                    </a>
                                                </td>
                                                <td name="colHE25N" class="text-center colHE25N" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/timer.svg" height="20" class="mr-2">
                                                    ${horaNocturna25}:${minutoNocturna25}:${segundoNocturna25}
                                                </td>
                                                <td name="colHE35N" class="text-center colHE35N" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/timer.svg" height="20" class="mr-2">
                                                    ${horaNocturna35}:${minutoNocturna35}:${segundoNocturna35}
                                                </td>
                                                <td name="colHE100N" class="text-center colHE100N" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <img src="landing/images/timer.svg" height="20" class="mr-2">
                                                    ${horaNocturna100}:${minutoNocturna100}:${segundoNocturna100}
                                                </td>
                                                <td name="colFaltaJornada" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-danger mr-2">
                                                        <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                        ${horasFaltaJ}:${minutosFaltaJ}:${segundosFaltaJ}
                                                    </a>
                                                </td>
                                                <td name="colTardanza" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <a class="badge badge-soft-danger mr-2">
                                                        <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                        ${horasTardanza}:${minutosTardanza}:${segundosTardanza}
                                                    </a>
                                                </td>`;
                                    if (data[key][index].data[m].marcaciones.length == 0 && data[key][index].incidencias.length == 0) {
                                        sumaFaltas++;
                                        grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                        <span class="badge badge-soft-danger mr-2" class="text-center">
                                                            Falta
                                                        </span>
                                                    </td>`;
                                    } else {
                                        grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>`;
                                    }
                                }
                            } else {
                                grupoHorario += `<td style="border-left: 2px solid #383e56!important;background: #fafafa;border-right: 1px dashed #c8d4de!important;" class="text-center" name="descripcionHorario">
                                                <a class="btn" type="button" style="padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                    <span class="badge badge-soft-danger mr-2" class="text-center">
                                                        Sin horario
                                                    </span>
                                                </a>
                                            </td>
                                            <td class="text-center" name="horarioHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <a class="btn" type="button" style="padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                    <span class="badge badge-soft-danger mr-2" class="text-center">
                                                        Sin horario
                                                    </span>
                                                </a>
                                            </td>
                                            <td class="text-center" name="toleranciaIHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                            <td class="text-center" name="toleranciaFHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                            <td name="colTiempoEntreH" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <a class="badge badge-soft-primary mr-2">
                                                    <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                    ${horaSumaTiemposEntreHorario}:${minutoSumaTiemposEntreHorario}:${segundoSumaTiemposEntreHorario}
                                                </a>
                                            </td>
                                            <td name="colTiempoMuertoEntrada" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                                                ${horaTiempoMuertoEntrada}:${minutoTiempoMuertoEntrada}:${segundoTiempoMuertoEntrada}
                                            </td>
                                            <td name="colTiempoMuertoSalida" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                                                ${horaTiempoMuertoSalida}:${minutoTiempoMuertoSalida}:${segundoTiempoMuertoSalida}
                                            </td>
                                            <td name="colSobreTiempo" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <a class="badge badge-soft-primary mr-2">
                                                    <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                    ${horasSobreT}:${minutosSobreT}:${segundosSobreT}
                                                </a>
                                            </td>
                                            <td name="colHoraNormal" class="text-center colHoraNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <a class="badge badge-soft-warning mr-2">
                                                    <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                    ${horaSumaHorasNormales}:${minutoSumaHorasNormales}:${segundoSumaHorasNormales}
                                                </a>
                                            </td>
                                            <td name="colHoraDiurnaNormal" class="text-center colHoraDiurnaNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <a class="badge badge-soft-warning mr-2">
                                                    <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                    ${horaHorasNormalesDiurnas}:${minutoHorasNormalesDiurnas}:${segundoHorasNormalesDiurnas}
                                                </a>
                                            </td>
                                            <td name="colSobreTNormal" class="text-center colSobreTNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <a class="badge badge-soft-warning mr-2">
                                                    <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                    ${horaSobretiempoNormales}:${minutoSobretiempoNormales}:${segundoSobretiempoNormales}
                                                </a>
                                            </td>
                                            <td name="colHE25D" class="text-center colHE25D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <img src="landing/images/timerD.svg" height="20" class="mr-2">
                                                ${horaDiurna25}:${minutoDiurna25}:${segundoDiurna25}
                                            </td>
                                            <td name="colHE35D" class="text-center colHE35D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <img src="landing/images/timerD.svg" height="20" class="mr-2">
                                                ${horaDiurna35}:${minutoDiurna35}:${segundoDiurna35}
                                            </td>
                                            <td name="colHE100D" class="text-center colHE100D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <img src="landing/images/timerD.svg" height="20" class="mr-2">
                                                ${horaDiurna100}:${minutoDiurna100}:${segundoDiurna100}
                                            </td>
                                            <td name="colHoraNocturna" class="text-center colHoraNocturna" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <a class="badge badge-soft-info mr-2">
                                                    <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                    ${horaSumaHorasNocturnas}:${minutoSumaHorasNocturnas}:${segundoSumaHorasNocturnas}
                                                </a>
                                            </td>
                                            <td name="colHoraNocturnaNormal" class="text-center colHoraNocturnaNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <a class="badge badge-soft-info mr-2">
                                                    <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                    ${horaHorasNormalesNocturnas}:${minutoHorasNormalesNocturnas}:${segundoHorasNormalesNocturnas}
                                                </a>
                                            </td>
                                            <td name="colSobreTNocturno" class="text-center colSobreTNocturno" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <a class="badge badge-soft-info mr-2">
                                                    <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                    ${horaSobretiempoNocturnos}:${minutoSobretiempoNocturnos}:${segundoSobretiempoNocturnos}
                                                </a>
                                            </td>
                                            <td name="colHE25N" class="text-center colHE25N" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <img src="landing/images/timer.svg" height="20" class="mr-2">
                                                ${horaNocturna25}:${minutoNocturna25}:${segundoNocturna25}
                                            </td>
                                            <td name="colHE35N" class="text-center colHE35N" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <img src="landing/images/timer.svg" height="20" class="mr-2">
                                                ${horaNocturna35}:${minutoNocturna35}:${segundoNocturna35}
                                            </td>
                                            <td name="colHE100N" class="text-center colHE100N" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <img src="landing/images/timer.svg" height="20" class="mr-2">
                                                ${horaNocturna100}:${minutoNocturna100}:${segundoNocturna100}
                                            </td>
                                            <td name="colFaltaJornada" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <a class="badge badge-soft-danger mr-2">
                                                    <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                    ${horasFaltaJ}:${minutosFaltaJ}:${segundosFaltaJ}
                                                </a>
                                            </td>
                                            <td name="colTardanza" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                <a class="badge badge-soft-danger mr-2">
                                                    <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                    ${horasTardanza}:${minutosTardanza}:${segundosTardanza}
                                                </a>
                                            </td>`;
                                if (data[key][index].data[m].marcaciones.length == 0 && data[key][index].incidencias.length == 0) {
                                    sumaFaltas++;
                                    grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">
                                                    <span class="badge badge-soft-danger mr-2" class="text-center">
                                                        Falta
                                                    </span>
                                                </td>`;
                                } else {
                                    grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>`;
                                }
                            }

                            // ! MARCACIONES
                            var tbodyEntradaySalida = "";
                            for (let j = 0; j < data[key][index].data[m].marcaciones.length; j++) {
                                // * TIEMPO EN SITIO
                                var segundosTiempo = "00";
                                var minutosTiempo = "00";
                                var horasTiempo = "00";
                                // * TIEMPO MUERTO ENTRADA
                                var segundosMuertosE = "00";
                                var minutosMuertosE = "00";
                                var horasMuertosE = "00";
                                // * TIEMPO MUERTO SALIDA
                                var segundosMuertosS = "00";
                                var minutosMuertosS = "00";
                                var horasMuertosS = "00";
                                var marcacionData = data[key][index].data[m].marcaciones[j];
                                if (marcacionData.entrada != 0 && marcacionData.salida) {
                                    // * CALCULAR TIEMPO TOTAL
                                    var horaFinal = moment(marcacionData.salida);
                                    var horaInicial = moment(marcacionData.entrada);
                                    if (horaFinal.isSameOrAfter(horaInicial)) {
                                        // * TIEMPO ENTRE MARCACIONES
                                        if (horarioData.idHorario != 0) {
                                            if (horarioData.tiempoMuertoI == 1) {
                                                if (horaInicial.clone().isBefore(moment(horarioData.horarioIni))) {
                                                    if (horaFinal.clone().isAfter(moment(horarioData.horarioIni))) {
                                                        // : TIEMPO MUERTO ENTRADA
                                                        var tiempoMuertoM = moment(horarioData.horarioIni) - horaInicial;
                                                        segundosMuertosE = moment.duration(tiempoMuertoM).seconds();
                                                        minutosMuertosE = moment.duration(tiempoMuertoM).minutes();
                                                        horasMuertosE = Math.trunc(moment.duration(tiempoMuertoM).asHours());
                                                        if (horasMuertosE < 10) {
                                                            horasMuertosE = "0" + horasMuertosE;
                                                        }
                                                        if (minutosMuertosE < 10) {
                                                            minutosMuertosE = "0" + minutosMuertosE;
                                                        }
                                                        if (segundosMuertosE < 10) {
                                                            segundosMuertosE = "0" + segundosMuertosE;
                                                        }
                                                        // : HORA DE ENTRADA
                                                        horaInicial = moment(horarioData.horarioIni);
                                                        // : HORA DE SALIDA
                                                        if (horarioData.tiempoMuertoS == 1) {
                                                            if (horaFinal.clone().isAfter(moment(horarioData.horarioFin))) {
                                                                // : TIEMPO MUERTO SALIDA
                                                                var tiempoMuertoM = moment.duration(parseInt(horarioData.toleranciaF), "minutes");
                                                                segundosMuertosS = moment.duration(tiempoMuertoM).seconds();
                                                                minutosMuertosS = moment.duration(tiempoMuertoM).minutes();
                                                                horasMuertosS = Math.trunc(moment.duration(tiempoMuertoM).asHours());
                                                                if (horasMuertosS < 10) {
                                                                    horasMuertosS = "0" + horasMuertosS;
                                                                }
                                                                if (minutosMuertosS < 10) {
                                                                    minutosMuertosS = "0" + minutosMuertosS;
                                                                }
                                                                if (segundosMuertosS < 10) {
                                                                    segundosMuertosS = "0" + segundosMuertosS;
                                                                }
                                                                var NuevaSalida = horaFinal.clone().subtract(horarioData.toleranciaF, "minutes").format("YYYY-MM-DD HH:mm:ss");
                                                                horaFinal = moment(NuevaSalida);
                                                            }
                                                        }
                                                    } else {
                                                        // : TIEMPO MUERTO
                                                        var tiempoMuertoM = horaFinal - horaInicial;
                                                        segundosMuertosE = moment.duration(tiempoMuertoM).seconds();
                                                        minutosMuertosE = moment.duration(tiempoMuertoM).minutes();
                                                        horasMuertosE = Math.trunc(moment.duration(tiempoMuertoM).asHours());
                                                        if (horasMuertosE < 10) {
                                                            horasMuertosE = "0" + horasMuertosE;
                                                        }
                                                        if (minutosMuertosE < 10) {
                                                            minutosMuertosE = "0" + minutosMuertosE;
                                                        }
                                                        if (segundosMuertosE < 10) {
                                                            segundosMuertosE = "0" + segundosMuertosE;
                                                        }
                                                        horaInicial = moment.duration(0);
                                                        horaFinal = moment.duration(0);
                                                    }
                                                } else {
                                                    // : HORA DE SALIDA
                                                    if (horarioData.tiempoMuertoS == 1) {
                                                        if (horaFinal.clone().isAfter(moment(horarioData.horarioFin))) {
                                                            // : TIEMPO MUERTO SALIDA
                                                            var tiempoMuertoM = moment.duration(parseInt(horarioData.toleranciaF), "minutes");
                                                            segundosMuertosS = moment.duration(tiempoMuertoM).seconds();
                                                            minutosMuertosS = moment.duration(tiempoMuertoM).minutes();
                                                            horasMuertosS = Math.trunc(moment.duration(tiempoMuertoM).asHours());
                                                            if (horasMuertosS < 10) {
                                                                horasMuertosS = "0" + horasMuertosS;
                                                            }
                                                            if (minutosMuertosS < 10) {
                                                                minutosMuertosS = "0" + minutosMuertosS;
                                                            }
                                                            if (segundosMuertosS < 10) {
                                                                segundosMuertosS = "0" + segundosMuertosS;
                                                            }
                                                            var NuevaSalida = horaFinal.clone().subtract(horarioData.toleranciaF, "minutes").format("YYYY-MM-DD HH:mm:ss");
                                                            horaFinal = moment(NuevaSalida);
                                                        }
                                                    }
                                                }
                                            } else {
                                                // : HORA DE SALIDA
                                                if (horarioData.tiempoMuertoS == 1) {
                                                    if (horaFinal.clone().isAfter(moment(horarioData.horarioFin))) {
                                                        // : TIEMPO MUERTO SALIDA
                                                        var tiempoMuertoM = moment.duration(parseInt(horarioData.toleranciaF), "minutes");
                                                        segundosMuertosS = moment.duration(tiempoMuertoM).seconds();
                                                        minutosMuertosS = moment.duration(tiempoMuertoM).minutes();
                                                        horasMuertosS = Math.trunc(moment.duration(tiempoMuertoM).asHours());
                                                        if (horasMuertosS < 10) {
                                                            horasMuertosS = "0" + horasMuertosS;
                                                        }
                                                        if (minutosMuertosS < 10) {
                                                            minutosMuertosS = "0" + minutosMuertosS;
                                                        }
                                                        if (segundosMuertosS < 10) {
                                                            segundosMuertosS = "0" + segundosMuertosS;
                                                        }
                                                        var NuevaSalida = horaFinal.clone().subtract(horarioData.toleranciaF, "minutes").format("YYYY-MM-DD HH:mm:ss");
                                                        horaFinal = moment(NuevaSalida);
                                                    }
                                                }
                                            }
                                        }
                                        if (horaInicial != 0 && horaFinal != 0) {
                                            var tiempoRestante = horaFinal - horaInicial;
                                            segundosTiempo = moment.duration(tiempoRestante).seconds();
                                            minutosTiempo = moment.duration(tiempoRestante).minutes();
                                            horasTiempo = Math.trunc(moment.duration(tiempoRestante).asHours());
                                            if (horasTiempo < 10) {
                                                horasTiempo = '0' + horasTiempo;
                                            }
                                            if (minutosTiempo < 10) {
                                                minutosTiempo = '0' + minutosTiempo;
                                            }
                                            if (segundosTiempo < 10) {
                                                segundosTiempo = '0' + segundosTiempo;
                                            }
                                        }
                                    }
                                    // * FINALIZACION
                                }
                                if (marcacionData.entrada != 0) {
                                    tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;" name="colMarcaciones" data-toggle="tooltip" data-placement="left" data-html="true" title="Fecha:${moment(marcacionData.entrada).format("YYYY-MM-DD")}\nDispositivo:${marcacionData.dispositivoEntrada}">
                                                            <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>
                                                            ${moment(marcacionData.entrada).format("HH:mm:ss")}
                                                        </td>
                                                        <td class="text-center colDispositivoE" name="colDispositivoE">${marcacionData.dispositivoEntrada}</td>`;

                                    if (marcacionData.salida != 0) {
                                        tbodyEntradaySalida += `<td name="colMarcaciones" data-toggle="tooltip" data-placement="left" data-html="true" title="Fecha:${moment(marcacionData.salida).format("YYYY-MM-DD")}\nDispositivo:${marcacionData.dispositivoSalida}">
                                                                <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> 
                                                                ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                            </td>`;
                                        tbodyEntradaySalida += `<td class="text-center colDispositivoS" name="colDispositivoS">${marcacionData.dispositivoSalida}</td>`;

                                        tbodyEntradaySalida += `<td name="colTiempoS">
                                                                <input type="hidden" value= "${horasTiempo}:${minutosTiempo}:${segundosTiempo}">
                                                                <a class="badge badge-soft-primary mr-2">
                                                                    <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                                    ${horasTiempo}:${minutosTiempo}:${segundosTiempo}
                                                                </a>
                                                            </td>
                                                            <td class="text-center colTiempoMuertoEXM">
                                                                <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                                                                ${horasMuertosE}:${minutosMuertosE}:${segundosMuertosE}
                                                            </td>
                                                            <td class="text-center colTiempoMuertoSXM">
                                                                <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                                                                ${horasMuertosS}:${minutosMuertosS}:${segundosMuertosS}
                                                            </td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td name="colMarcaciones">
                                                                <span class="badge badge-soft-secondary noExport"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                    No tiene salida
                                                                </span>
                                                            </td>`;
                                        tbodyEntradaySalida += `<td class="text-center colDispositivoS" name="colDispositivoS">---</td>`;
                                        tbodyEntradaySalida += `<td name="colTiempoS">
                                                                <input type="hidden" value= "${horasTiempo}:${minutosTiempo}:${segundosTiempo}">
                                                                <a class="badge badge-soft-primary mr-2">
                                                                    <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                                    ${horasTiempo}:${minutosTiempo}:${segundosTiempo}
                                                                </a>
                                                            </td>
                                                            <td class="text-center colTiempoMuertoEXM">
                                                                <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                                                                ${horasMuertosE}:${minutosMuertosE}:${segundosMuertosE}
                                                            </td>
                                                            <td class="text-center colTiempoMuertoSXM">
                                                                <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                                                                ${horasMuertosS}:${minutosMuertosS}:${segundosMuertosS}
                                                            </td>`;
                                    }

                                } else {
                                    if (marcacionData.salida != 0) {
                                        //* COLUMNA DE ENTRADA
                                        tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;" name="colMarcaciones">
                                                                <span class="badge badge-soft-warning noExport">
                                                                    <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                                    No tiene entrada
                                                                </span>
                                                            </td>`;
                                        tbodyEntradaySalida += `<td class="text-center colDispositivoE" name="colDispositivoE">---</td>`;
                                        //* COLUMNA DE SALIDA
                                        tbodyEntradaySalida += `<td name="colMarcaciones" data-toggle="tooltip" data-placement="left" data-html="true" title="Fecha:${moment(marcacionData.salida).format("YYYY-MM-DD")}\nDispositivo:${marcacionData.dispositivoSalida}">
                                                                <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> 
                                                                ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                            </td>`;
                                        tbodyEntradaySalida += `<td class="text-center colDispositivoS" name="colDispositivoS">${marcacionData.dispositivoSalida}</td>`;
                                        tbodyEntradaySalida += `<td name="colTiempoS">
                                                                <input type="hidden" value= "${horasTiempo}:${minutosTiempo}:${segundosTiempo}">
                                                                <a class="badge badge-soft-primary mr-2">
                                                                    <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                                    ${horasTiempo}:${minutosTiempo}:${segundosTiempo}
                                                                </a>
                                                            </td>
                                                            <td class="text-center colTiempoMuertoEXM">
                                                                <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                                                                ${horasMuertosE}:${minutosMuertosE}:${segundosMuertosE}
                                                            </td>
                                                            <td class="text-center colTiempoMuertoSXM">
                                                                <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                                                                ${horasMuertosS}:${minutosMuertosE}:${segundosMuertosS}
                                                            </td>`;
                                    }
                                }
                            }
                            for (let mr = data[key][index].data[m].marcaciones.length; mr < arrayHorario[m].split(",")[0]; mr++) {
                                tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;" class="text-center" name="colMarcaciones">---</td>
                                                    <td class="text-center colDispositivoE" name="colDispositivoE">---</td>
                                                    <td class="text-center" name="colMarcaciones">---</td>
                                                    <td class="text-center colDispositivoS" name="colDispositivoS">---</td>
                                                    <td name="colTiempoS" class="text-center">---</td>
                                                    <td class="text-center colTiempoMuertoEXM">---</td>
                                                    <td class="text-center colTiempoMuertoSXM">---</td>`;
                            }
                            grupoHorario += tbodyEntradaySalida;
                            // ! PAUSAS
                            var tbodyPausas = "";
                            for (let p = 0; p < data[key][index].data[m].pausas.length; p++) {
                                // * PAUSAS
                                tiempoHoraPausa = "00";        //: HORAS TARDANZA
                                tiempoMinutoPausa = "00";      //: MINUTOS TARDANZA
                                tiempoSegundoPausa = "00";     //: SEGUNDOS TARDANZA
                                estadoTiempoHorario = true;
                                //* EXCESO
                                tiempoHoraExceso = "00";
                                tiempoMinutoExceso = "00";
                                tiempoSegundoExceso = "00";
                                var pausaData = data[key][index].data[m].pausas[p];
                                for (let mp = 0; mp < data[key][index].data[m].marcaciones.length; mp++) {
                                    var dataMarcacionP = data[key][index].data[m].marcaciones[mp];
                                    if (dataMarcacionP.idH != 0 && pausaData.horario_id == dataMarcacionP.idH) {
                                        if (!idPausas.includes(pausaData.id)) {
                                            var horaInicialM = moment(dataMarcacionP.entrada);
                                            var horaFinalM = moment(dataMarcacionP.salida);
                                            // ****************************************** PAUSAS ****************************************
                                            var fechaI = horaInicialM.clone().format("YYYY-MM-DD");
                                            var fechaF;
                                            if (pausaData.inicio > pausaData.fin) {
                                                fechaF = horaInicialM.clone().add(1, 'day').format("YYYY-MM-DD");
                                            } else {
                                                fechaF = horaInicialM.clone().format("YYYY-MM-DD");
                                            }
                                            var pausaI = moment(fechaI + " " + pausaData.inicio);
                                            var pausaF = moment(fechaF + " " + pausaData.fin);
                                            // ! INICIO DE PAUSA
                                            var sumaToleranciaPausa = moment(
                                                pausaI.clone().add(
                                                    { "minutes": pausaData.tolerancia_inicio }    // : CLONAMOS EL TIEMPO Y SUMAR CON TOLERANCIA
                                                ).toString());
                                            var restaToleranciaPausa = moment(
                                                pausaI.clone().subtract(
                                                    { "minutes": pausaData.tolerancia_inicio }
                                                ).toString()); //: CLONAMOS EL TIEMPO Y RESTAR CON TOLERANCIA
                                            // ! FIN DE PAUSA
                                            var sumaToleranciaPausaFinal = moment(pausaF.clone().add({ "minutes": pausaData.tolerancia_fin }).toString());
                                            // ! CONDICIONALES QUE SI HORA FINAL DE LA MARCACION ESTA ENTRE LA RESTA CON LA TOLERANCIA Y LA SUMA CON LA TOLERANCIA
                                            if (horaFinalM.isSameOrAfter(restaToleranciaPausa) && horaFinalM.isSameOrBefore(sumaToleranciaPausa)) {
                                                // * VERIFICAR SI YA TENEMOS OTRA MARCACION SIGUIENTE
                                                if (data[key][index].data[m].marcaciones[mp + 1] != undefined) {
                                                    if (data[key][index].data[m].marcaciones[mp + 1].entrada != undefined) {
                                                        if (data[key][index].data[m].marcaciones[mp + 1].entrada != 0) {
                                                            var horaEntradaDespues = moment(data[key][index].data[m].marcaciones[mp + 1].entrada);    //: -> OBTENER ENTRADA DE LA MARCACION SIGUIENTE
                                                            var restarTiempoMarcacion = horaEntradaDespues - horaFinalM;                //: -> RESTAR PARA OBTENER LA CANTIDAD EN PAUSA               
                                                            tiempoSegundoPausa = moment.duration(restarTiempoMarcacion).seconds();      //: -> TIEMPOS EN SEGUNDOS
                                                            tiempoMinutoPausa = moment.duration(restarTiempoMarcacion).minutes();       //: -> TIEMPOS EN MINUTOS
                                                            tiempoHoraPausa = Math.trunc(moment.duration(restarTiempoMarcacion).asHours()); //: -> TIEMPOS EN HORAS
                                                            if (tiempoHoraPausa < 10) {
                                                                tiempoHoraPausa = '0' + tiempoHoraPausa;
                                                            }
                                                            if (tiempoMinutoPausa < 10) {
                                                                tiempoMinutoPausa = '0' + tiempoMinutoPausa;
                                                            }
                                                            if (tiempoSegundoPausa < 10) {
                                                                tiempoSegundoPausa = '0' + tiempoSegundoPausa;
                                                            }
                                                            // * VERIFICAR TIEMPO DE EXCESO
                                                            var clonarPausaI = pausaI.clone();
                                                            var clonarPausaF = pausaF.clone();
                                                            var restaEntrePausa = clonarPausaF - clonarPausaI;
                                                            // * CONDICIONAL QUE DEBE SER MENOR O IGUAL CON EL TIEMPO PAUSA
                                                            if (restarTiempoMarcacion <= restaEntrePausa) {
                                                                tiempoHoraExceso = "00";
                                                                tiempoMinutoExceso = "00";
                                                                tiempoSegundoExceso = "00";
                                                            } else {
                                                                var restaParaExceso = restarTiempoMarcacion - restaEntrePausa;
                                                                tiempoSegundoExceso = moment.duration(restaParaExceso).seconds();
                                                                tiempoMinutoExceso = moment.duration(restaParaExceso).minutes();
                                                                tiempoHoraExceso = Math.trunc(moment.duration(restaParaExceso).asHours());
                                                                if (tiempoHoraExceso < 10) {
                                                                    tiempoHoraExceso = '0' + tiempoHoraExceso;
                                                                }
                                                                if (tiempoMinutoExceso < 10) {
                                                                    tiempoMinutoExceso = '0' + tiempoMinutoExceso;
                                                                }
                                                                if (tiempoSegundoPausa < 10) {
                                                                    tiempoSegundoExceso = '0' + tiempoSegundoExceso;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                idPausas.push(pausaData.id);
                                            } else {
                                                if (horaFinalM.isSameOrAfter(restaToleranciaPausa) && horaFinalM.isSameOrBefore(sumaToleranciaPausaFinal)) {
                                                    // * VERIFICAR SI YA TENEMOS OTRA MARCACION SIGUIENTE
                                                    if (data[key][index].data[m].marcaciones[mp + 1] != undefined) {
                                                        if (data[key][index].data[m].marcaciones[mp + 1].entrada != undefined) {
                                                            if (data[key][index].data[m].marcaciones[mp + 1].entrada != 0) {
                                                                estadoTiempoHorario = false;
                                                                var horaEntradaDespues = moment(data[key][index].data[m].marcaciones[mp + 1].entrada);    //: -> OBTENER ENTRADA DE LA MARCACION SIGUIENTE
                                                                var restarTiempoMarcacion = horaEntradaDespues - horaFinalM;                //: -> RESTAR PARA OBTENER LA CANTIDAD EN PAUSA               
                                                                tiempoSegundoPausa = moment.duration(restarTiempoMarcacion).seconds();      //: -> TIEMPOS EN SEGUNDOS
                                                                tiempoMinutoPausa = moment.duration(restarTiempoMarcacion).minutes();       //: -> TIEMPOS EN MINUTOS
                                                                tiempoHoraPausa = Math.trunc(moment.duration(restarTiempoMarcacion).asHours()); //: -> TIEMPOS EN HORAS
                                                                if (tiempoHoraPausa < 10) {
                                                                    tiempoHoraPausa = '0' + tiempoHoraPausa;
                                                                }
                                                                if (tiempoMinutoPausa < 10) {
                                                                    tiempoMinutoPausa = '0' + tiempoMinutoPausa;
                                                                }
                                                                if (tiempoSegundoPausa < 10) {
                                                                    tiempoSegundoPausa = '0' + tiempoSegundoPausa;
                                                                }
                                                                // * VERIFICAR TIEMPO DE EXCESO
                                                                var clonarPausaI = pausaI.clone();
                                                                var clonarPausaF = pausaF.clone();
                                                                var restaEntrePausa = clonarPausaF - clonarPausaI;
                                                                // * CONDICIONAL QUE DEBE SER MENOR O IGUAL CON EL TIEMPO PAUSA
                                                                if (restarTiempoMarcacion <= restaEntrePausa) {
                                                                    tiempoHoraExceso = "00";
                                                                    tiempoMinutoExceso = "00";
                                                                    tiempoSegundoExceso = "00";
                                                                } else {
                                                                    var restaParaExceso = restarTiempoMarcacion - restaEntrePausa;
                                                                    tiempoSegundoExceso = moment.duration(restaParaExceso).seconds();
                                                                    tiempoMinutoExceso = moment.duration(restaParaExceso).minutes();
                                                                    tiempoHoraExceso = Math.trunc(moment.duration(restaParaExceso).asHours());
                                                                    if (tiempoHoraExceso < 10) {
                                                                        tiempoHoraExceso = '0' + tiempoHoraExceso;
                                                                    }
                                                                    if (tiempoMinutoExceso < 10) {
                                                                        tiempoMinutoExceso = '0' + tiempoMinutoExceso;
                                                                    }
                                                                    if (tiempoSegundoPausa < 10) {
                                                                        tiempoSegundoExceso = '0' + tiempoSegundoExceso;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    idPausas.push(pausaData.id);
                                                }
                                            }
                                        }
                                    }
                                }
                                tbodyPausas += `<td style="border-left: 1px dashed #aaaaaa!important;" name="descripcionPausa">${pausaData.descripcion}</td>
                                    <td name="horarioPausa">${pausaData.inicio} - ${pausaData.fin}</td>`;
                                if (estadoTiempoHorario) {
                                    tbodyPausas += `<td name="tiempoPausa" class="text-center">
                                                    <a class="badge badge-soft-primary mr-2">
                                                        <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                        ${tiempoHoraPausa}:${tiempoMinutoPausa}:${tiempoSegundoPausa}
                                                    </a>
                                                </td>`;
                                } else {
                                    tbodyPausas += `<td name="tiempoPausa" class="text-center">
                                                    <a class="badge badge-soft-warning mr-2" rel="tooltip" data-toggle="tooltip" data-placement="left" 
                                                        title="El colaborador marcó tarde su ${pausaData.descripcion}.\nSalida:${pausaData.inicio}\n
                                                        Tolerancia ${pausaData.tolerancia_inicio} min.\nRegreso:${pausaData.fin}\nTolerancia ${pausaData.tolerancia_fin} min." 
                                                        data-html="true">
                                                        <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                        ${tiempoHoraPausa}:${tiempoMinutoPausa}:${tiempoSegundoPausa}
                                                    </a>
                                                </td>`;
                                }
                                tbodyPausas += `<td name="excesoPausa" class="text-center">
                                        <a class="badge badge-soft-danger mr-2">
                                            <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                            ${tiempoHoraExceso}:${tiempoMinutoExceso}:${tiempoSegundoExceso}
                                        </a>
                                    </td>`;
                            }
                            for (let cp = data[key][index].data[m].pausas.length; cp < arrayHorario[m].split(",")[1]; cp++) {
                                tbodyPausas += `<td style="border-left: 1px dashed #aaaaaa!important" name="descripcionPausa" class="text-center">----</td>
                                            <td name="horarioPausa" class="text-center">-----</td>
                                            <td name="tiempoPausa" class="text-center">-----</td>
                                            <td name="excesoPausa" class="text-center">------</td>`;
                            }
                            grupoHorario += tbodyPausas;
                        } else {
                            grupoHorario += `<td style="border-left: 2px solid #383e56!important;background:#fafafa;border-right: 1px dashed #c8d4de!important;" class="text-center" name="descripcionHorario">
                                            ----
                                        </td>
                                        <td class="text-center" name="horarioHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td class="text-center" name="toleranciaIHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td class="text-center" name="toleranciaFHorario" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colTiempoEntreH" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colTiempoMuertoEntrada" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colTiempoMuertoSalida" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colSobreTiempo" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colHoraNormal" class="text-center colHoraNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colHoraDiurnaNormal" class="text-center colHoraDiurnaNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colSobreTNormal" class="text-center colSobreTNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colHE25D" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colHE35D" class="text-center colHE35D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colHE100D" class="text-center colHE100D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colHoraNocturna" class="text-center colHoraNocturna" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colHoraNocturnaNormal" class="text-center colHoraNocturnaNormal" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colSobreTNocturno" class="text-center colSobreTNocturno" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colHE25N" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colHE35N" class="text-center colHE35D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colHE100N" class="text-center colHE100D" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colFaltaJornada" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="colTardanza" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>
                                        <td name="faltaHorario" class="text-center" style="background: #fafafa;border-right: 1px dashed #c8d4de!important;">---</td>`;
                            // ! MARCACIONES
                            var tbodyEntradaySalida = "";
                            for (let mr = 0; mr < arrayHorario[m].split(",")[0]; mr++) {
                                tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;" class="text-center" name="colMarcaciones">---</td>
                                                    <td class="text-center colDispositivoE" name="colDispositivoE">---</td>
                                                    <td class="text-center" name="colMarcaciones">---</td>
                                                    <td class="text-center colDispositivoS" name="colDispositivoS">---</td>
                                                    <td name="colTiempoS" class="text-center">---</td>
                                                    <td class="text-center colTiempoMuertoEXM">---</td>
                                                    <td class="text-center colTiempoMuertoSXM">---</td>`;
                            }
                            grupoHorario += tbodyEntradaySalida;
                            // ! PAUSAS
                            var tbodyPausas = "";
                            for (let cp = 0; cp < arrayHorario[m].split(",")[1]; cp++) {
                                tbodyPausas += `<td style="border-left: 1px dashed #aaaaaa!important" name="descripcionPausa" class="text-center">----</td>
                                            <td name="horarioPausa" class="text-center">-----</td>
                                            <td name="tiempoPausa" class="text-center">-----</td>
                                            <td name="excesoPausa" class="text-center">------</td>`;
                            }
                            grupoHorario += tbodyPausas;
                        }
                    }
                    tbody += grupoHorario;
                    // : TIEMPO TOTAL
                    var horaTiempoTotal = Math.trunc(moment.duration(sumaTiempos).asHours());
                    var minutoTiempoTotal = moment.duration(sumaTiempos).minutes();
                    var segundoTiempoTotal = moment.duration(sumaTiempos).seconds();
                    if (horaTiempoTotal < 10) {
                        horaTiempoTotal = "0" + horaTiempoTotal;
                    }
                    if (minutoTiempoTotal < 10) {
                        minutoTiempoTotal = "0" + minutoTiempoTotal;
                    }
                    if (segundoTiempoTotal < 10) {
                        segundoTiempoTotal = "0" + segundoTiempoTotal;
                    }
                    // : SOBRE TIEMPO TOTAL 
                    var horaSumaSobreTiempo = Math.trunc(moment.duration(sumaSobreTiempo).asHours());
                    var minutoSumaSobreTiempo = moment.duration(sumaSobreTiempo).minutes();
                    var segundoSumaSobreTiempo = moment.duration(sumaSobreTiempo).seconds();
                    if (horaSumaSobreTiempo < 10) {
                        horaSumaSobreTiempo = "0" + horaSumaSobreTiempo;
                    }
                    if (minutoSumaSobreTiempo < 10) {
                        minutoSumaSobreTiempo = "0" + minutoSumaSobreTiempo;
                    }
                    if (segundoSumaSobreTiempo < 10) {
                        segundoSumaSobreTiempo = "0" + segundoSumaSobreTiempo;
                    }
                    // : SUMA HORAS NOCTURNAS TOTAL
                    var horaSumaHorasNocturnasTotal = Math.trunc(moment.duration(sumaHorasNocturnasT).asHours());
                    var minutoSumaHorasNocturnasTotal = moment.duration(sumaHorasNocturnasT).minutes();
                    var segundoSumaHorasNocturnasTotal = moment.duration(sumaHorasNocturnasT).seconds();
                    if (horaSumaHorasNocturnasTotal < 10) {
                        horaSumaHorasNocturnasTotal = "0" + horaSumaHorasNocturnasTotal;
                    }
                    if (minutoSumaHorasNocturnasTotal < 10) {
                        minutoSumaHorasNocturnasTotal = "0" + minutoSumaHorasNocturnasTotal;
                    }
                    if (segundoSumaHorasNocturnasTotal < 10) {
                        segundoSumaHorasNocturnasTotal = "0" + segundoSumaHorasNocturnasTotal;
                    }
                    // : SUMA HORAS NORMALES TOTAL
                    var horaSumaHorasNormalesT = Math.trunc(moment.duration(sumaHorasNormalesT).asHours());
                    var minutoSumaHorasNormalesT = moment.duration(sumaHorasNormalesT).minutes();
                    var segundoSumaHorasNormalesT = moment.duration(sumaHorasNormalesT).seconds();
                    if (horaSumaHorasNormalesT < 10) {
                        horaSumaHorasNormalesT = "0" + horaSumaHorasNormalesT;
                    }
                    if (minutoSumaHorasNormalesT < 10) {
                        minutoSumaHorasNormalesT = "0" + minutoSumaHorasNormalesT;
                    }
                    if (segundoSumaHorasNormalesT < 10) {
                        segundoSumaHorasNormalesT = "0" + segundoSumaHorasNormalesT;
                    }
                    // : SOBRETIEMPO NORMAL TOTAL
                    var horaSumaSobretiempoNormalesT = Math.trunc(moment.duration(sumaSobreTiempoNormalesT).asHours());
                    var minutoSumaSobretiempoNormalesT = moment.duration(sumaSobreTiempoNormalesT).minutes();
                    var segundoSumaSobretiempoNormalesT = moment.duration(sumaSobreTiempoNormalesT).seconds();
                    if (horaSumaSobretiempoNormalesT < 10) {
                        horaSumaSobretiempoNormalesT = "0" + horaSumaSobretiempoNormalesT;
                    }
                    if (minutoSumaSobretiempoNormalesT < 10) {
                        minutoSumaSobretiempoNormalesT = "0" + minutoSumaSobretiempoNormalesT;
                    }
                    if (segundoSumaSobretiempoNormalesT < 10) {
                        segundoSumaSobretiempoNormalesT = "0" + segundoSumaSobretiempoNormalesT;
                    }
                    // : SOBRETIEMPO NOCTURNO TOTAL
                    var horaSumaSobretiempoNocturnasT = Math.trunc(moment.duration(sumaSobreTiempoNocturnasT).asHours());
                    var minutoSumaSobretiempoNocturnasT = moment.duration(sumaSobreTiempoNocturnasT).minutes();
                    var segundoSumaSobretiempoNocturnasT = moment.duration(sumaSobreTiempoNocturnasT).seconds();
                    if (horaSumaSobretiempoNocturnasT < 10) {
                        horaSumaSobretiempoNocturnasT = "0" + horaSumaSobretiempoNocturnasT;
                    }
                    if (minutoSumaSobretiempoNocturnasT < 10) {
                        minutoSumaSobretiempoNocturnasT = "0" + minutoSumaSobretiempoNocturnasT;
                    }
                    if (segundoSumaSobretiempoNocturnasT < 10) {
                        segundoSumaSobretiempoNocturnasT = "0" + segundoSumaSobretiempoNocturnasT;
                    }
                    // : SUMA DE TARDANZAS
                    var horaSumaTardanzas = Math.trunc(moment.duration(sumaTardanzas).asHours());
                    var minutoSumaTardanzas = moment.duration(sumaTardanzas).minutes();
                    var segundoSumaTardanzas = moment.duration(sumaTardanzas).seconds();
                    if (horaSumaTardanzas < 10) {
                        horaSumaTardanzas = "0" + horaSumaTardanzas;
                    }
                    if (minutoSumaTardanzas < 10) {
                        minutoSumaTardanzas = "0" + minutoSumaTardanzas;
                    }
                    if (segundoSumaTardanzas < 10) {
                        segundoSumaTardanzas = "0" + segundoSumaTardanzas;
                    }
                    // : SUMA FALTA JORNADA
                    var horaSumaFaltaJornada = Math.trunc(moment.duration(sumaFaltaJornada).asHours());
                    var minutoSumaFaltaJornada = moment.duration(sumaFaltaJornada).minutes();
                    var segundoSumaFaltaJornada = moment.duration(sumaFaltaJornada).seconds();
                    if (horaSumaFaltaJornada < 10) {
                        horaSumaFaltaJornada = "0" + horaSumaFaltaJornada;
                    }
                    if (minutoSumaFaltaJornada < 10) {
                        minutoSumaFaltaJornada = "0" + minutoSumaFaltaJornada;
                    }
                    if (segundoSumaFaltaJornada < 10) {
                        segundoSumaFaltaJornada = "0" + segundoSumaFaltaJornada;
                    }
                    // : SUMA DE HORAS EXTRAS DE 25% DIURNO
                    var horaSumaHorasE25D = Math.trunc(moment.duration(sumaHorasE25D).asHours());
                    var minutoSumaHorasE25D = moment.duration(sumaHorasE25D).minutes();
                    var segundoSumaHorasE25D = moment.duration(sumaHorasE25D).seconds();
                    if (horaSumaHorasE25D < 10) {
                        horaSumaHorasE25D = "0" + horaSumaHorasE25D;
                    }
                    if (minutoSumaHorasE25D < 10) {
                        minutoSumaHorasE25D = "0" + minutoSumaHorasE25D;
                    }
                    if (segundoSumaHorasE25D < 10) {
                        segundoSumaHorasE25D = "0" + segundoSumaHorasE25D;
                    }
                    // : SUMA DE HORAS EXTRAS DE 35% DIURNO
                    var horaSumaHorasE35D = Math.trunc(moment.duration(sumaHorasE35D).asHours());
                    var minutoSumaHorasE35D = moment.duration(sumaHorasE35D).minutes();
                    var segundoSumaHorasE35D = moment.duration(sumaHorasE35D).seconds();
                    if (horaSumaHorasE35D < 10) {
                        horaSumaHorasE35D = "0" + horaSumaHorasE35D;
                    }
                    if (minutoSumaHorasE35D < 10) {
                        minutoSumaHorasE35D = "0" + minutoSumaHorasE35D;
                    }
                    if (segundoSumaHorasE35D < 10) {
                        segundoSumaHorasE35D = "0" + segundoSumaHorasE35D;
                    }
                    // : SUMA DE HORAS EXTRAS DE 100% DIURNO
                    var horaSumaHorasE100D = Math.trunc(moment.duration(sumaHorasE100D).asHours());
                    var minutoSumaHorasE100D = moment.duration(sumaHorasE100D).minutes();
                    var segundoSumaHorasE100D = moment.duration(sumaHorasE100D).seconds();
                    if (horaSumaHorasE100D < 10) {
                        horaSumaHorasE100D = "0" + horaSumaHorasE100D;
                    }
                    if (minutoSumaHorasE100D < 10) {
                        minutoSumaHorasE100D = "0" + minutoSumaHorasE100D;
                    }
                    if (segundoSumaHorasE100D < 10) {
                        segundoSumaHorasE100D = "0" + segundoSumaHorasE100D;
                    }
                    // : SUMA DE HORAS EXTRAS DE 25% NOCTURNAS
                    var horaSumaHorasE25N = Math.trunc(moment.duration(sumaHorasE25N).asHours());
                    var minutoSumaHorasE25N = moment.duration(sumaHorasE25N).minutes();
                    var segundoSumaHorasE25N = moment.duration(sumaHorasE25N).seconds();
                    if (horaSumaHorasE25N < 10) {
                        horaSumaHorasE25N = "0" + horaSumaHorasE25N;
                    }
                    if (minutoSumaHorasE25N < 10) {
                        minutoSumaHorasE25N = "0" + minutoSumaHorasE25N;
                    }
                    if (segundoSumaHorasE25N < 10) {
                        segundoSumaHorasE25N = "0" + segundoSumaHorasE25N;
                    }
                    // : SUMA DE HORAS EXTRAS DE 35% NOCTURNAS 
                    var horaSumaHorasE35N = Math.trunc(moment.duration(sumaHorasE35N).asHours());
                    var minutoSumaHorasE35N = moment.duration(sumaHorasE35N).minutes();
                    var segundoSumaHorasE35N = moment.duration(sumaHorasE35N).seconds();
                    if (horaSumaHorasE35N < 10) {
                        horaSumaHorasE35N = "0" + horaSumaHorasE35N;
                    }
                    if (minutoSumaHorasE35N < 10) {
                        minutoSumaHorasE35N = "0" + minutoSumaHorasE35N;
                    }
                    if (segundoSumaHorasE35N < 10) {
                        segundoSumaHorasE35N = "0" + segundoSumaHorasE35N;
                    }
                    // : SUMA DE HORAS EXTRAS DE 100% NOCTURNAS
                    var horaSumaHorasE100N = Math.trunc(moment.duration(sumaHorasE100N).asHours());
                    var minutoSumaHorasE100N = moment.duration(sumaHorasE100N).minutes();
                    var segundoSumaHorasE100N = moment.duration(sumaHorasE100N).seconds();
                    if (horaSumaHorasE100N < 10) {
                        horaSumaHorasE100N = "0" + horaSumaHorasE100N;
                    }
                    if (minutoSumaHorasE100N < 10) {
                        minutoSumaHorasE100N = "0" + minutoSumaHorasE100N;
                    }
                    if (segundoSumaHorasE100N < 10) {
                        segundoSumaHorasE100N = "0" + segundoSumaHorasE100N;
                    }
                    // : SUMA DE TIEMPOS MUERTOS EN ENTRADA
                    var horaSumaTiemposMuertosTE = Math.trunc(moment.duration(sumaMuertosEntrada).asHours());
                    var minutoSumaTiemposMuertosTE = moment.duration(sumaMuertosEntrada).minutes();
                    var segundoSumaTiemposMuertosTE = moment.duration(sumaMuertosEntrada).seconds();
                    if (horaSumaTiemposMuertosTE < 10) {
                        horaSumaTiemposMuertosTE = "0" + horaSumaTiemposMuertosTE;
                    }
                    if (minutoSumaTiemposMuertosTE < 10) {
                        minutoSumaTiemposMuertosTE = "0" + minutoSumaTiemposMuertosTE;
                    }
                    if (segundoSumaTiemposMuertosTE < 10) {
                        segundoSumaTiemposMuertosTE = "0" + segundoSumaTiemposMuertosTE;
                    }
                    // : SUMA DE TIEMPOS MUERTOS EN SALIDA
                    var horaSumaTiemposMuertosTS = Math.trunc(moment.duration(sumaMuertosSalida).asHours());
                    var minutoSumaTiemposMuertosTS = moment.duration(sumaMuertosSalida).minutes();
                    var segundoSumaTiemposMuertosTS = moment.duration(sumaMuertosSalida).seconds();
                    if (horaSumaTiemposMuertosTS < 10) {
                        horaSumaTiemposMuertosTS = "0" + horaSumaTiemposMuertosTS;
                    }
                    if (minutoSumaTiemposMuertosTS < 10) {
                        minutoSumaTiemposMuertosTS = "0" + minutoSumaTiemposMuertosTS;
                    }
                    if (segundoSumaTiemposMuertosTS < 10) {
                        segundoSumaTiemposMuertosTS = "0" + segundoSumaTiemposMuertosTS;
                    }
                    // * COLUMNAS DE TIEMPO TOTAL TARDANAZA ETC
                    tbody += `<td name="colTiempoTotal" style="border-left: 2px solid #383e56!important;">
                            <a class="badge badge-soft-primary mr-2">
                                <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                ${horaTiempoTotal}:${minutoTiempoTotal}:${segundoTiempoTotal}
                            </a>
                        </td>
                        <td name="colTiempoMuertoTotalE" class="text-center" style="border-left: 1px dashed #aaaaaa!important">
                            <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                            ${horaSumaTiemposMuertosTE}:${minutoSumaTiemposMuertosTE}:${segundoSumaTiemposMuertosTE}
                        </td>
                        <td name="colTiempoMuertoTotalS" class="text-center" style="border-left: 1px dashed #aaaaaa!important">
                            <img src="landing/images/tiempoMuerto.svg" height="18" class="mr-2">
                            ${horaSumaTiemposMuertosTS}:${minutoSumaTiemposMuertosTS}:${segundoSumaTiemposMuertosTS}
                        </td>
                        <td name="colSobreTiempoTotal" class="text-center" style="border-left: 1px dashed #aaaaaa!important">
                            <a class="badge badge-soft-primary mr-2">
                                <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                ${horaSumaSobreTiempo}:${minutoSumaSobreTiempo}:${segundoSumaSobreTiempo}
                            </a>
                        </td>
                        <td name="colHoraNormalTotal" class="text-center colHoraNormalTotal" style="border-left: 1px dashed #aaaaaa!important">
                            <a class="badge badge-soft-warning mr-2">
                                <img src="landing/images/sun.svg" height="12" class="mr-2">
                                ${horaSumaHorasNormalesT}:${minutoSumaHorasNormalesT}:${segundoSumaHorasNormalesT}
                            </a>
                        </td>
                        <td name="colSobretiempoNormalT" class="text-center colSobretiempoNormalT" style="border-left: 1px dashed #aaaaaa!important">
                            <a class="badge badge-soft-warning mr-2">
                                <img src="landing/images/sun.svg" height="12" class="mr-2">
                                ${horaSumaSobretiempoNormalesT}:${minutoSumaSobretiempoNormalesT}:${segundoSumaSobretiempoNormalesT}
                            </a>
                        </td>
                        <td name="colHE25DTotal" class="text-center" style="border-left: 1px dashed #aaaaaa!important">
                            <img src="landing/images/timerD.svg" height="20" class="mr-2">
                            ${horaSumaHorasE25D}:${minutoSumaHorasE25D}:${segundoSumaHorasE25D}
                        </td>
                        <td name="colHE35DTotal" class="text-center" style="border-left: 1px dashed #aaaaaa!important">
                            <img src="landing/images/timerD.svg" height="20" class="mr-2">
                            ${horaSumaHorasE35D}:${minutoSumaHorasE35D}:${segundoSumaHorasE35D}
                        </td>
                        <td name="colHE100DTotal" class="text-center" style="border-left: 1px dashed #aaaaaa!important">
                            <img src="landing/images/timerD.svg" height="20" class="mr-2">
                            ${horaSumaHorasE100D}:${minutoSumaHorasE100D}:${segundoSumaHorasE100D}
                        </td>
                        <td name="colHoraNocturnaTotal" class="text-center colHoraNocturnaTotal" style="border-left: 1px dashed #aaaaaa!important">
                            <a class="badge badge-soft-info mr-2">
                                <img src="landing/images/moon.svg" height="12" class="mr-2">
                                ${horaSumaHorasNocturnasTotal}:${minutoSumaHorasNocturnasTotal}:${segundoSumaHorasNocturnasTotal}
                            </a>
                        </td>
                        <td name="colSobretiempoNocturnoT" class="text-center colSobretiempoNocturnoT" style="border-left: 1px dashed #aaaaaa!important">
                            <a class="badge badge-soft-info mr-2">
                                <img src="landing/images/moon.svg" height="12" class="mr-2">
                                ${horaSumaSobretiempoNocturnasT}:${minutoSumaSobretiempoNocturnasT}:${segundoSumaSobretiempoNocturnasT}
                            </a>
                        </td>
                        <td name="colHE25NTotal" class="text-center" style="border-left: 1px dashed #aaaaaa!important">
                            <img src="landing/images/timer.svg" height="20" class="mr-2">
                            ${horaSumaHorasE25N}:${minutoSumaHorasE25N}:${segundoSumaHorasE25N}
                        </td>
                        <td name="colHE35NTotal" class="text-center" style="border-left: 1px dashed #aaaaaa!important">
                            <img src="landing/images/timer.svg" height="20" class="mr-2">
                            ${horaSumaHorasE35N}:${minutoSumaHorasE35N}:${segundoSumaHorasE35N}
                        </td>
                        <td name="colHE100NTotal" class="text-center" style="border-left: 1px dashed #aaaaaa!important">
                            <img src="landing/images/timer.svg" height="20" class="mr-2">
                            ${horaSumaHorasE100N}:${minutoSumaHorasE100N}:${segundoSumaHorasE100N}
                        </td>
                        <td name="colFaltaJornadaTotal" class="text-center" style="border-left: 1px dashed #aaaaaa!important">
                            <a class="badge badge-soft-danger mr-2">
                                <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                ${horaSumaFaltaJornada}:${minutoSumaFaltaJornada}:${segundoSumaFaltaJornada}
                            </a>
                        </td>
                        <td name="colTardanzaTotal" style="border-left: 1px dashed #aaaaaa!important">
                            <a class="badge badge-soft-danger mr-2">
                                <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                ${horaSumaTardanzas}:${minutoSumaTardanzas}:${segundoSumaTardanzas}
                            </a>
                        </td>`;
                    // ******************************* CANTIDAD DE FALTAS **************************
                    if (sumaFaltas != 0) {
                        tbody += `<td class="text-center" name="faltaTotal" style="border-left: 1px dashed #aaaaaa!important">`;
                        for (let f = 0; f < sumaFaltas; f++) {
                            if (f == 0) {
                                tbody += `<span class="badge badge-soft-danger mr-1" class="text-center">
                                        Falta
                                    </span>`;
                            } else {
                                tbody += `<b>/</b><span class="badge badge-soft-danger ml-1" class="text-center">
                                        Falta
                                    </span>`;
                            }
                        }
                        tbody += `</td>`;
                    } else {
                        tbody += `<td class="text-center" name="faltaTotal" style="border-left: 1px dashed #aaaaaa!important">--</td>`;
                    }
                    // * ********************** FINALIZACION *************************************
                    if (data[key][index].incidencias.length == 0) {
                        tbody += `<td class="text-center" style="border-left: 1px dashed #aaaaaa!important" name="incidencia">--</td>`;
                    } else {
                        tbody += `<td class="text-center" style="border-left: 1px dashed #aaaaaa!important;" name="incidencia">`;
                        for (let item = 0; item < data[key][index].incidencias.length; item++) {
                            var dataIncidencia = data[key][index].incidencias[item];
                            if (item == 0) {
                                tbody += `<span class="badge badge-soft-info ml-1" class="text-center">${dataIncidencia.descripcion}</span>`;
                            } else {
                                tbody += `<b>/</b><span class="badge badge-soft-info ml-1" class="text-center">${dataIncidencia.descripcion}</span>`;
                            }
                        }
                        tbody += `</td>`;
                    }
                    tbody += `</tr>`;
                }
            }
            $('#tbodyD').html(tbody);
            $('[data-toggle="tooltip"]').tooltip();
            $('.dropdown-toggle').dropdown();
            inicializarTabla();
            $(window).on('resize', function () {
                $("#tablaReport").css('width', '100%');
                table.draw(false);
            });
            // * SWITCH DE MOSTRAR DETALLES
            toggleColumnas();
        } else {
            if ($.fn.DataTable.isDataTable("#tablaReport")) {
                $("#tablaReport").DataTable().destroy();
            }
            $('#theadD').empty();
            var headerT = `<tr>
                <th>CC</th>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Cargo</th>
                <th>Horario</th>
                <th id="hEntrada">Hora de entrada</th>
                <th id="hSalida">Hora de salida</th>
                <th id="tSitio">Tiempo en sitio</th>
                <th>Tardanza</th>
                <th>Faltas</th>
                <th>Incidencias</th>
            </tr>`;
            $('#theadD').append(headerT);
            $('#tbodyD').empty();
            inicializarTabla();
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        $.notify({
            message: '\nSurgio un error.',
            icon: 'landing/images/bell.svg',
        }, {
            icon_type: 'image',
            allow_dismiss: true,
            newest_on_top: true,
            delay: 6000,
            template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #f2dede;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
    });
}
function cambiarF() {
    f1 = $('#ID_START').val();
    f2 = $('#ID_END').val();
    cargartabla(f1, f2);
    setTimeout(function () { $("#tablaReport").DataTable().draw(); }, 200);
}
// TODO *********************************** FUNCIONALIDAD PARA MARCACIONES *******************************
// ! ********************************* SELECTOR DE COLUMNAS ****************************************************
// * FUNCION PARA QUE NO SE CIERRE DROPDOWN
$('#dropSelector').on('hidden.bs.dropdown', function () {
    $('#contenidoDetalle').hide();
    $('#contenidoPausas').hide();
    $('#contenidoHorarios').hide();
    $('#contenidoIncidencias').hide();
    $('#contenidoPorH').hide();
    $('#contenidoPorT').hide();
    $('#contenidoDispositivos').hide();
    $('#contenidoPorTM').hide();
    $('#contenidoPorTiemposDiurnosH').hide();
    $('#contenidoPorTiemposNocturnosH').hide();
});
$(document).on('click', '.allow-focus', function (e) {
    e.stopPropagation();
});
// : ************************************** COLUMNAS DE CALCULOS DE TIEMPO ***********************************************
// * TOGGLE DE DETALLES
function toggleD() {
    $('#contenidoDetalle').toggle();
}
// * TOGGLE POR HORARIO
function togglePorHorario() {
    $('#contenidoPorH').toggle();
}
// * TOGGLE POR TIEMPOS DIURNOS
function togglePorTiemposDiurnosH() {
    $('#contenidoPorTiemposDiurnosH').toggle();
}
// * TOGGLE POR TOTALES
function togglePorTotales() {
    $('#contenidoPorT').toggle();
}
// * TOGGLE POR TIEMPOS MUERTOS
function togglePorTiemposMuertos() {
    $('#contenidoPorTM').toggle();
}
// * TOGGLE POR TIEMPOS NOCTURNOS
function togglePorTiemposNocturnosH() {
    $('#contenidoPorTiemposNocturnosH').toggle();
}
// * HIJOS DE TIEMPO DIURNO ENTRE HORARIO
$('.detalleHijoDeHijoDeHijo input[type=checkbox]').change(function () {
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.detalleHijoDeHijo').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.detalleHijoDeHijo').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.detalleHijoDeHijo').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    // * PARA SEGUNDO PADRE TIEMPOS POR HORARIO
    var indeterminanteP = $(this).closest('ul').parent().find('.detalleHijoDeHijo input[type=checkbox]:indeterminate').length;
    var checkedP = $(this).closest('ul').parent().find('.detalleHijoDeHijo input[type=checkbox]:checked').length;
    var lengthP = $(this).closest('ul').parent().find('.detalleHijoDeHijo input[type=checkbox]').length;
    if (lengthP == checkedP) {
        $(this).closest('ul').prev('li').closest('ul').prev('li').closest('.detalleHijo').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (indeterminanteP != 0) {
            $(this).closest('ul').prev('li').closest('ul').prev('li').closest('.detalleHijo').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            if (checkedP != 0) {
                $(this).closest('ul').prev('li').closest('ul').prev('li').closest('.detalleHijo').find('input[type=checkbox]').prop({
                    indeterminate: true,
                    checked: false
                });
            } else {
                $(this).closest('ul').prev('li').closest('ul').prev('li').closest('.detalleHijo').find('input[type=checkbox]').prop({
                    indeterminate: false,
                    checked: false
                });
            }
        }
    }
    // * PARA PRIMER PADRE CALCULOS DE TIEMPOS
    var indeterminanteP1 = $(this).closest('ul').parent().closest('ul').parent().find('.detalleHijo input[type=checkbox]:indeterminate').length;
    var checkedP1 = $(this).closest('ul').parent().closest('ul').parent().find('.detalleHijo input[type=checkbox]:checked').length;
    var lengthP1 = $(this).closest('ul').parent().closest('ul').parent().find('.detalleHijo input[type=checkbox]').length;
    if (lengthP1 == checkedP1) {
        $(this).closest('ul').prev('li').closest('ul').prev('li').closest('ul').prev('li').closest('.detallePadre').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (indeterminanteP1 != 0) {
            $(this).closest('ul').prev('li').closest('ul').prev('li').closest('ul').prev('li').closest('.detallePadre').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            if (checkedP1 != 0) {
                $(this).closest('ul').prev('li').closest('ul').prev('li').closest('ul').prev('li').closest('.detallePadre').find('input[type=checkbox]').prop({
                    indeterminate: true,
                    checked: false
                });
            } else {
                $(this).closest('ul').prev('li').closest('ul').prev('li').closest('ul').prev('li').closest('.detallePadre').find('input[type=checkbox]').prop({
                    indeterminate: false,
                    checked: false
                });
            }
        }
    }
    toggleColumnas();
});
// * HIJOS DE POR HORARIO Y TOTAL
$('.detalleHijoDeHijo input[type=checkbox]').change(function () {
    $(this).closest('.detalleHijoDeHijo').next('ul').find('.detalleHijoDeHijoDeHijo input[type=checkbox]').prop('checked', this.checked);
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.detalleHijo').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.detalleHijo').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.detalleHijo').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    // * PARA SEGUNDO PADRE TIEMPOS POR HORARIO

    var indeterminanteP = $(this).closest('ul').parent().find('.detalleHijo input[type=checkbox]:indeterminate').length;
    var checkedP = $(this).closest('ul').parent().find('.detalleHijo input[type=checkbox]:checked').length;
    var lengthP = $(this).closest('ul').parent().find('.detalleHijo input[type=checkbox]').length;
    if (lengthP == checkedP) {
        $(this).closest('ul').prev('li').closest('ul').prev('li').closest('.detallePadre').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (indeterminanteP != 0) {
            $(this).closest('ul').prev('li').closest('ul').prev('li').closest('.detallePadre').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            if (checkedP != 0) {
                $(this).closest('ul').prev('li').closest('ul').prev('li').closest('.detallePadre').find('input[type=checkbox]').prop({
                    indeterminate: true,
                    checked: false
                });
            } else {
                $(this).closest('ul').prev('li').closest('ul').prev('li').closest('.detallePadre').find('input[type=checkbox]').prop({
                    indeterminate: false,
                    checked: false
                });
            }
        }
    }
    toggleColumnas();
});
// * PADRE DE HIJOS DE POR HORARIO Y TOTAL
$('.detalleHijo input[type=checkbox]').change(function () {
    $(this).closest('.detalleHijo').next('ul').find('.detalleHijoDeHijoDeHijo input[type=checkbox]').prop('checked', this.checked);
    $(this).closest('.detalleHijo').next('ul').find('.detalleHijoDeHijo input[type=checkbox]').prop('checked', this.checked);
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.detallePadre').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.detallePadre').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.detallePadre').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    toggleColumnas();
});
// * FUNCIONN DE CHECKBOX DE PADRE DETALLES
$('.detallePadre input[type=checkbox]').change(function () {
    $(this).closest('.detallePadre').next('ul').find('.detalleHijo input[type=checkbox]').prop({
        indeterminate: false,
        checked: this.checked
    });
    $('.detalleHijo').next('ul').find('.detalleHijoDeHijo input[type=checkbox]').prop('checked', this.checked);
    $('.detalleHijoDeHijo').next('ul').find('.detalleHijoDeHijoDeHijo input[type=checkbox]').prop('checked', this.checked);
    toggleColumnas();
});
// : ************************************** COLUMNAS DE PAUSAS ***********************************************
function toggleP() {
    $('#contenidoPausas').toggle();
}
// * FUNCION DE CHECKBOX HIJOS PAUSAS
$('.pausaHijo input[type=checkbox]').change(function () {
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.pausaPadre').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.pausaPadre').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.pausaPadre').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    toggleColumnas();
});
// * FUNCIONN DE CHECKBOX DE PADRE DETALLES
$('.pausaPadre input[type=checkbox]').change(function () {
    $(this).closest('.pausaPadre').next('ul').find('.pausaHijo input[type=checkbox]').prop('checked', this.checked);
    toggleColumnas();
});
// : ************************************** COLUMNA DE CARGO ***************************************************
$('#colCargo').change(function (event) {
    if (event.target.checked) {
        dataT.api().columns('.colCargo').visible(true);
    } else {
        dataT.api().columns('.colCargo').visible(false);
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
});
// : ************************************ COLUMNA DE MARCACIONES ************************************************
$('#colMarcaciones').change(function (event) {
    if (event.target.checked) {
        dataT.api().columns('.colMarcaciones').visible(true);
    } else {
        dataT.api().columns('.colMarcaciones').visible(false);
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
});
// : *********************************** COLUMNA DE HORARIOS ***************************************************
function toggleH() {
    $('#contenidoHorarios').toggle();
}
// * FUNCION DE CHECKBOX HIJOS DE HORARIO
$('.horarioHijo input[type=checkbox]').change(function () {
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.horarioPadre').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.horarioPadre').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.horarioPadre').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    toggleColumnas();
});
// * FUNCIONN DE CHECKBOX DE PADRE DETALLES
$('.horarioPadre input[type=checkbox]').change(function () {
    $(this).closest('.horarioPadre').next('ul').find('.horarioHijo input[type=checkbox]').prop('checked', this.checked);
    toggleColumnas();
});
// : ********************************* COLUMNA DE CODIGO ********************************************************
$('#colCodigo').change(function (event) {
    if (event.target.checked) {
        dataT.api().columns('.colCodigo').visible(true);
    } else {
        dataT.api().columns('.colCodigo').visible(false);
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
});
// : ********************************* COLUMNA DE INCIDENCIAS ********************************************************
function toggleI() {
    $('#contenidoIncidencias').toggle();
}
// * FUNCION DE CHECKBOX HIJOS DE HORARIO
$('.incidenciaHijo input[type=checkbox]').change(function () {
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.incidenciaPadre').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.incidenciaPadre').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.incidenciaPadre').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    toggleColumnas();
});
// * FUNCIONN DE CHECKBOX DE PADRE DETALLES
$('.incidenciaPadre input[type=checkbox]').change(function () {
    $(this).closest('.incidenciaPadre').next('ul').find('.incidenciaHijo input[type=checkbox]').prop('checked', this.checked);
    toggleColumnas();
});
// : ***************************************** COLUMNA DISPOSITIVO **********************************************
function toggleDisp() {
    $('#contenidoDispositivos').toggle();
}
// * FUNCION DE CHECKBOX HIJOS DE DISPOSITIVO
$('.dispositivoHijo input[type=checkbox]').change(function () {
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.dispositivoPadre').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.dispositivoPadre').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.dispositivoPadre').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    toggleColumnas();
});
// * FUNCIONN DE CHECKBOX DE PADRE DISPOSITIVO
$('.dispositivoPadre input[type=checkbox]').change(function () {
    $(this).closest('.dispositivoPadre').next('ul').find('.dispositivoHijo input[type=checkbox]').prop('checked', this.checked);
    toggleColumnas();
});
// : ***************************************** FILAS CON SOLO MARCACIONES ***************************************
$('#colEmpleadosCM').change(function (event) {
    // : ME COSTO CASI UN DIA COMPLETO GOOGLEANDO, CON UN GRACIAS GABY NO SERIA MALO JAJAJAJ
    if (event.target.checked) {
        $.fn.dataTable.ext.search.pop();
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                return $(table.row(dataIndex).node()).attr('data-estado') == 1;
            }
        );
    } else {
        $.fn.dataTable.ext.search.pop();
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(); }, 1);
});
// : ********************************* FINALIZACION *************************************************************
// * FUNCION DE MOSTRAR COLUMNAS
function toggleColumnas() {
    // * ***************** COLUMNAS DE CALCULOS DE TIEMPO ****************
    // ! ---------------- TIEMPO POR MARCACIONES -------------------------
    // ? TIEMPO ENTRE MARCACIONES
    if ($('#colTiempoSitio').is(":checked")) {
        dataT.api().columns('.colTiempoS').visible(true);
    } else {
        dataT.api().columns('.colTiempoS').visible(false);
    }
    // ! ----------------- POR HORARIO -----------------------------------
    // : TIEMPO ENTRE HORARIO
    if ($('#colTiempoEntreH').is(":checked")) {
        dataT.api().columns('.colTiempoEntreH').visible(true);
    } else {
        dataT.api().columns('.colTiempoEntreH').visible(false);
    }
    // : SOBRE TIEMPO ENTRE HORARIOS
    if ($('#colSobreTiempo').is(":checked")) {
        dataT.api().columns('.colSobreTiempo').visible(true);
    } else {
        dataT.api().columns('.colSobreTiempo').visible(false);
    }
    // : FALTA JORNADA ENTRE HORARIOS
    if ($('#colFaltaJornada').is(":checked")) {
        dataT.api().columns('.colFaltaJornada').visible(true);
    } else {
        dataT.api().columns('.colFaltaJornada').visible(false);
    }
    // : HORARIO NORMAL
    if ($('#colHoraNormal').is(":checked")) {
        dataT.api().columns('.colHoraNormal').visible(true);
    } else {
        dataT.api().columns('.colHoraNormal').visible(false);
    }
    // : HORAS NORMALES - DIURNAS
    if ($('#colHoraDiurnaNormal').is(":checked")) {
        dataT.api().columns('.colHoraDiurnaNormal').visible(true);
    } else {
        dataT.api().columns('.colHoraDiurnaNormal').visible(false);
    }
    // : HORAS NOCTURNAS
    if ($('#colHoraNocturna').is(":checked")) {
        dataT.api().columns('.colHoraNocturna').visible(true);
    } else {
        dataT.api().columns('.colHoraNocturna').visible(false);
    }
    // : HORAS NORMALES NOCTURNAS
    if ($('#colHoraNocturnaNormal').is(":checked")) {
        dataT.api().columns('.colHoraNocturnaNormal').visible(true);
    } else {
        dataT.api().columns('.colHoraNocturnaNormal').visible(false);
    }
    // : HORAS EXTRAS 25% DIURNAS
    if ($('#colHE25D').is(":checked")) {
        dataT.api().columns('.colHE25D').visible(true);
    } else {
        dataT.api().columns('.colHE25D').visible(false);
    }
    // : HORAS EXTRAS 35% DIURNAS
    if ($('#colHE35D').is(":checked")) {
        dataT.api().columns('.colHE35D').visible(true);
    } else {
        dataT.api().columns('.colHE35D').visible(false);
    }
    // : HORAS EXTRAS 100% DIURNAS
    if ($('#colHE100D').is(":checked")) {
        dataT.api().columns('.colHE100D').visible(true);
    } else {
        dataT.api().columns('.colHE100D').visible(false);
    }
    // : SOBRETIEMPO NORMAL POR HORARIO
    if ($('#colSobreTNormal').is(":checked")) {
        dataT.api().columns('.colSobreTNormal').visible(true);
    } else {
        dataT.api().columns('.colSobreTNormal').visible(false);
    }
    // : SOBRETIEMPO NOCTURNO POR HORARIO
    if ($('#colSobreTNocturno').is(":checked")) {
        dataT.api().columns('.colSobreTNocturno').visible(true);
    } else {
        dataT.api().columns('.colSobreTNocturno').visible(false);
    }
    // : HORAS EXTRAS 25% NOCTURNAS
    if ($('#colHE25N').is(":checked")) {
        dataT.api().columns('.colHE25N').visible(true);
    } else {
        dataT.api().columns('.colHE25N').visible(false);
    }
    // : HORAS EXTRAS 35% NOCTURNAS
    if ($('#colHE35N').is(":checked")) {
        dataT.api().columns('.colHE35N').visible(true);
    } else {
        dataT.api().columns('.colHE35N').visible(false);
    }
    // : HORAS EXTRAS 100% DIURNAS
    if ($('#colHE100N').is(":checked")) {
        dataT.api().columns('.colHE100N').visible(true);
    } else {
        dataT.api().columns('.colHE100N').visible(false);
    }
    // ! ----------------- POR TOTALES ------------------------------------
    // : TIEMPO TOTAL
    if ($('#colTiempoTotal').is(":checked")) {
        dataT.api().columns('.colTiempoTotal').visible(true);
    } else {
        dataT.api().columns('.colTiempoTotal').visible(false);
    }
    // : SOBRE TIEMPO TOTAL
    if ($('#colSobreTiempoTotal').is(":checked")) {
        dataT.api().columns('.colSobreTiempoTotal').visible(true);
    } else {
        dataT.api().columns('.colSobreTiempoTotal').visible(false);
    }
    // : FALTA JORNADA TOTAL
    if ($('#colFaltaJornadaTotal').is(":checked")) {
        dataT.api().columns('.colFaltaJornadaTotal').visible(true);
    } else {
        dataT.api().columns('.colFaltaJornadaTotal').visible(false);
    }
    // : HORARIO NORMAL TOTAL
    if ($('#colHoraNormalTotal').is(":checked")) {
        dataT.api().columns('.colHoraNormalTotal').visible(true);
    } else {
        dataT.api().columns('.colHoraNormalTotal').visible(false);
    }
    // : HORARIO NOCTURNO TOTAL
    if ($('#colHoraNocturnaTotal').is(":checked")) {
        dataT.api().columns('.colHoraNocturnaTotal').visible(true);
    } else {
        dataT.api().columns('.colHoraNocturnaTotal').visible(false);
    }
    // : HORAS EXTRAS 25% DIURNAS TOTALES
    if ($('#colHE25DTotal').is(":checked")) {
        dataT.api().columns('.colHE25DTotal').visible(true);
    } else {
        dataT.api().columns('.colHE25DTotal').visible(false);
    }
    // : HORAS EXTRAS 35% DIURNAS TOTALES
    if ($('#colHE35DTotal').is(":checked")) {
        dataT.api().columns('.colHE35DTotal').visible(true);
    } else {
        dataT.api().columns('.colHE35DTotal').visible(false);
    }
    // : HORAS EXTRAS 100% DIURNAS TOTALES
    if ($('#colHE100DTotal').is(":checked")) {
        dataT.api().columns('.colHE100DTotal').visible(true);
    } else {
        dataT.api().columns('.colHE100DTotal').visible(false);
    }
    // : HORAS EXTRAS 25% NOCTURNAS TOTALES
    if ($('#colHE25NTotal').is(":checked")) {
        dataT.api().columns('.colHE25NTotal').visible(true);
    } else {
        dataT.api().columns('.colHE25NTotal').visible(false);
    }
    // : HORAS EXTRAS 35% NOCTURNAS TOTALES
    if ($('#colHE35NTotal').is(":checked")) {
        dataT.api().columns('.colHE35NTotal').visible(true);
    } else {
        dataT.api().columns('.colHE35NTotal').visible(false);
    }
    // : HORAS EXTRAS 100% NOCTURNAS TOTALES
    if ($('#colHE100NTotal').is(":checked")) {
        dataT.api().columns('.colHE100NTotal').visible(true);
    } else {
        dataT.api().columns('.colHE100NTotal').visible(false);
    }
    // : SOBRETIEMPO NORMAL TOTAL
    if ($('#colSobretiempoNormalT').is(":checked")) {
        dataT.api().columns('.colSobretiempoNormalT').visible(true);
    } else {
        dataT.api().columns('.colSobretiempoNormalT').visible(false);
    }
    // : SOBRETIEMPO NOCTURNO TOTAL
    if ($('#colSobretiempoNocturnoT').is(":checked")) {
        dataT.api().columns('.colSobretiempoNocturnoT').visible(true);
    } else {
        dataT.api().columns('.colSobretiempoNocturnoT').visible(false);
    }
    // ! ----------------- POR TIEMPOS MUERTOS ----------------------------
    // : TIEMPO MUERTO EN ENTRADA POR HORARIO
    if ($('#colTiempoMuertoEntrada').is(":checked")) {
        dataT.api().columns('.colTiempoMuertoEntrada').visible(true);
    } else {
        dataT.api().columns('.colTiempoMuertoEntrada').visible(false);
    }
    // : TIEMPO MUERTO EN SALIDA POR HORARIO
    if ($('#colTiempoMuertoSalida').is(":checked")) {
        dataT.api().columns('.colTiempoMuertoSalida').visible(true);
    } else {
        dataT.api().columns('.colTiempoMuertoSalida').visible(false);
    }
    // : TIEMPO MUERTO EN ENTRADA POR MARCACION
    if ($('#colTiempoMuertoEXM').is(":checked")) {
        dataT.api().columns('.colTiempoMuertoEXM').visible(true);
    } else {
        dataT.api().columns('.colTiempoMuertoEXM').visible(false);
    }
    // : TIEMPO MUERTO EN SALIDA POR HORARIO
    if ($('#colTiempoMuertoSXM').is(":checked")) {
        dataT.api().columns('.colTiempoMuertoSXM').visible(true);
    } else {
        dataT.api().columns('.colTiempoMuertoSXM').visible(false);
    }
    // : TIEMPO MUERTO EN ENTRADA POR TOTAL
    if ($('#colTiempoMuertoTotalE').is(":checked")) {
        dataT.api().columns('.colTiempoMuertoTotalE').visible(true);
    } else {
        dataT.api().columns('.colTiempoMuertoTotalE').visible(false);
    }
    // : TIEMPO MUERTO EN SALIDA POR TOTAL
    if ($('#colTiempoMuertoTotalS').is(":checked")) {
        dataT.api().columns('.colTiempoMuertoTotalS').visible(true);
    } else {
        dataT.api().columns('.colTiempoMuertoTotalS').visible(false);
    }
    // * ****************** COLUMNAS DE PAUSAS *********************
    // ? DESCRION PAUSA
    if ($('#descripcionPausa').is(":checked")) {
        dataT.api().columns('.descripcionPausa').visible(true);
    } else {
        dataT.api().columns('.descripcionPausa').visible(false);
    }
    // ? HORARIO PAUSA
    if ($('#horarioPausa').is(":checked")) {
        dataT.api().columns('.horarioPausa').visible(true);
    } else {
        dataT.api().columns('.horarioPausa').visible(false);
    }
    // ? TIEMPO DE PAUSA
    if ($('#tiempoPausa').is(":checked")) {
        dataT.api().columns('.tiempoPausa').visible(true);
    } else {
        dataT.api().columns('.tiempoPausa').visible(false);
    }
    // ? EXCESO DE PAUSA
    if ($('#excesoPausa').is(":checked")) {
        dataT.api().columns('.excesoPausa').visible(true);
    } else {
        dataT.api().columns('.excesoPausa').visible(false);
    }
    // * *************** COLUMNA CARGO ******************************
    if ($('#colCargo').is(":checked")) {
        dataT.api().columns('.colCargo').visible(true);
    } else {
        dataT.api().columns('.colCargo').visible(false);
    }
    // * **************** COLUMNA MARCACION ***************************
    if ($('#colMarcaciones').is(":checked")) {
        dataT.api().columns('.colMarcaciones').visible(true);
    } else {
        dataT.api().columns('.colMarcaciones').visible(false);
    }
    // * *************** COLUMNA HORARIOS *****************************
    // ? DESCRIPCION DE HORARIO
    if ($('#descripcionHorario').is(":checked")) {
        dataT.api().columns('.descripcionHorario').visible(true);
    } else {
        dataT.api().columns('.descripcionHorario').visible(false);
    }
    // ? HORARIO
    if ($('#horarioHorario').is(":checked")) {
        dataT.api().columns('.horarioHorario').visible(true);
    } else {
        dataT.api().columns('.horarioHorario').visible(false);
    }
    // ? TOLERANCIA INICIO
    if ($('#toleranciaIHorario').is(":checked")) {
        dataT.api().columns('.toleranciaIHorario').visible(true);
    } else {
        dataT.api().columns('.toleranciaIHorario').visible(false);
    }
    // ? TOLERANCIA FIN
    if ($('#toleranciaFHorario').is(":checked")) {
        dataT.api().columns('.toleranciaFHorario').visible(true);
    } else {
        dataT.api().columns('.toleranciaFHorario').visible(false);
    }
    // * *************** COLUMNA CODIGO ******************************
    if ($('#colCodigo').is(":checked")) {
        dataT.api().columns('.colCodigo').visible(true);
    } else {
        dataT.api().columns('.colCodigo').visible(false);
    }
    // * *************** COLUMNA INCIDENCIAS ******************************
    // ? TARDANZA ENTRE HORARIOS
    if ($('#colTardanza').is(":checked")) {
        dataT.api().columns('.colTardanza').visible(true);
    } else {
        dataT.api().columns('.colTardanza').visible(false);
    }
    // ? FALTA ENTRE HORARIOS
    if ($('#faltaHorario').is(":checked")) {
        dataT.api().columns('.faltaHorario').visible(true);
    } else {
        dataT.api().columns('.faltaHorario').visible(false);
    }
    // ? TARDANZA TOTAL
    if ($('#colTardanzaTotal').is(":checked")) {
        dataT.api().columns('.colTardanzaTotal').visible(true);
    } else {
        dataT.api().columns('.colTardanzaTotal').visible(false);
    }
    // ? FALTA TOTAL
    if ($('#faltaTotal').is(":checked")) {
        dataT.api().columns('.faltaTotal').visible(true);
    } else {
        dataT.api().columns('.faltaTotal').visible(false);
    }
    // ? INCIDENCIAS
    if ($('#incidencia').is(":checked")) {
        dataT.api().columns('.incidencia').visible(true);
    } else {
        dataT.api().columns('.incidencia').visible(false);
    }
    // * *********************** DISPOSITIVOS **************************
    // ? ENTRADA
    if ($('#colDispositivoE').is(":checked")) {
        dataT.api().columns('.colDispositivoE').visible(true);
    } else {
        dataT.api().columns('.colDispositivoE').visible(false);
    }
    // ? SALIDA
    if ($('#colDispositivoS').is(":checked")) {
        dataT.api().columns('.colDispositivoS').visible(true);
    } else {
        dataT.api().columns('.colDispositivoS').visible(false);
    }
    // * ************************* TIPO FORMATO CELDA *********************
    // ? FORMATO DE NOMBRE Y APELLIDOS
    $("#formatoC > option").each(function () {
        if (!$(this).is(":checked")) {
            dataT.api().columns('.' + $(this).val()).visible(false);
        }
    });
    var columnaVisibleFormato = $('#formatoC :selected').val();
    dataT.api().columns('.' + columnaVisibleFormato).visible(true);
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
}
$('#formatoC').on("change", function () {
    toggleColumnas();
});
$("#tablaReport").on('show.bs.dropdown', function () {
    $('.dataTables_scrollBody').addClass('dropdown-visible');
})
    .on('hide.bs.dropdown', function () {
        $('.dataTables_scrollBody').removeClass('dropdown-visible');
    });
// ! ******************************* SELECT PERSONALIZADOS ****************************************
$(function () {
    // : INICIALIZAR PLUGIN
    $('#selectPor').select2({
        placeholder: 'Seleccionar',
        multiple: true,
        closeOnSelect: false,
        ajax: {
            async: false,
            type: "GET",
            url: "/selectPersonalizadoModoAP",
            dataType: 'json',
            delay: 250,
            data: function (params) {

                var queryParameters = {
                    term: params.term
                }
                return queryParameters;
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item, key) {
                        var children = [];
                        for (var k in item) {
                            var childItem = item[k];
                            childItem.id = item[k].id;
                            childItem.text = key + " : " + item[k].descripcion;
                            children.push(childItem);
                        }
                        return {
                            text: key,
                            children: children,
                        }
                    })
                }
            },
            cache: true,
        },
        minimumResultsForSearch: 4
    });
    $('#empleadoPor').select2({
        placeholder: 'Seleccionar empleado',
        multiple: true,
        closeOnSelect: false,
        minimumResultsForSearch: 4
    });
    // : INICIO DE SELECT POR 
    $('#selectPor').trigger({
        type: 'change'
    });
});
// : MOSTAR EMPLEADOS
$('#selectPor').on("change", function () {
    var valueQuery = $(this).val();
    var cantidad = 0;
    $('#empleadoPor').empty();
    $.ajax({
        async: false,
        type: "GET",
        url: "/selectEmpleadoGlobalAP",
        data: {
            query: valueQuery
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            var contenidoData = ``;
            data.empleado.forEach(element => {
                contenidoData += `<option value="${element.emple_id}">${element.perso_nombre} ${element.perso_apPaterno} ${element.perso_apMaterno}</option>`;
            });
            $('#empleadoPor').append(contenidoData);
            cantidad = data.select.length;
            $('#empleadoPor').val(data.select).trigger("change");
            $('#cantidadE').text(cantidad + "\templeados seleccionados.");
        },
        error: function () { }
    });
    if ($("#empleadoPor").select2('data').length === $("#empleadoPor >option").length) {
        $('#checkboxTodosEmpleados').prop("checked", true);
    } else {
        $('#checkboxTodosEmpleados').prop("checked", false);
    }
});
// : MOSTRAR LA CANTIDAD DE EMPLEADOS SELECIONADOS
$('#empleadoPor').on('select2:close', function () {
    var cantidad = $('#empleadoPor').select2('data').length;
    $('#cantidadE').empty();
    $('#cantidadE').text(cantidad + "\templeados seleccionados.");
});
$('#empleadoPor').on("change", function () {
    console.log($("#empleadoPor").select2('data').length, $("#empleadoPor >option").length);
    if ($("#empleadoPor").select2('data').length === $("#empleadoPor >option").length) {
        $('#checkboxTodosEmpleados').prop("checked", true);
    } else {
        $('#checkboxTodosEmpleados').prop("checked", false);
    }
});
$('#checkboxTodosEmpleados').on("change", function (event) {
    if (event.target.checked) {
        $("#empleadoPor > option").prop("selected", "selected");
        $('#empleadoPor').trigger("change");
    } else {
        $('#empleadoPor').val('').trigger('change');
    }
    $('#empleadoPor').trigger({
        type: 'select2:close'
    });
});