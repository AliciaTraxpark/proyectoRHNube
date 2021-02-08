// * VARIABLES GENERALES
var table;
var razonSocial;
var direccion;
var ruc;
var fecha;
var fechaD;
var fechaH;

//* FECHA
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "j F",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: true,
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
/*     COMBO BOX DINÁMICO PARA SELECCIONAR EMPLEADO     */
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
});
/*   FIN DE COMBO BOX  */

function cargartablaRuta(fecha1,fecha2) {

    var idemp = $('#idempleado').val();

    $.ajax({
        type: "GET",
        url: "/cargarTablaTardanzasRuta",
        data: {
            fecha1,fecha2, idemp
        },
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            if (data.length != 0) {
                razonSocial = data[0].organi_razonSocial;
                direccion = data[0].organi_direccion;
                ruc = data[0].organi_ruc;
                fecha = data[0].fecha;
                fechaD = data[0].fechaD;
                fechaH = data[0].fechaH;

                if ($.fn.DataTable.isDataTable("#tablaReport")) {
                    $("#tablaReport").DataTable().destroy();
                }
                
                // ! *********** CABEZERA DE TABLA**********
                $('#theadD').empty();
                //* ARMAR CABEZERA
                var theadTabla = `<tr>
                        <th>#&nbsp;</th>
                        <th>Código&nbsp;</th>
                        <th>Número de documento&nbsp;</th>
                        <th>Nombres y apellidos&nbsp;</th>
                        <th>Cargo&nbsp;</th>
                        <th>Área&nbsp;</th>
                        <th style="border-left-color: #c8d4de!important; border-left: 2px solid;">Tiempos de tardanza</th>
                        <th style="border-left-color: #c8d4de!important; border-left: 2px solid;">Cantidad de tardanzas</th></tr>`;
                
                //* DIBUJAMOS CABEZERA
                $('#theadD').html(theadTabla);
                // ! *********** BODY DE TABLA**********
                $('#tbodyD').empty();
                var tbody = "";
                //* ARMAMOS BODY DE TABLA
                for (let index = 0; index < data.length; index++) {
                    tbody += `<tr>
                    <td>${index+1}&nbsp;</td>
                    <td>${data[index].emple_code}&nbsp;</td>
                    <td>${data[index].emple_nDoc}&nbsp;</td>
                    <td>${data[index].perso_nombre} ${data[index].perso_apPaterno} ${data[index].perso_apMaterno} &nbsp;</td>
                    <td>${data[index].cargo_descripcion}&nbsp;</td>
                    <td>${data[index].area_descripcion}&nbsp;</td>
                    <td name="" style="border-left-color: #c8d4de!important; border-left: 2px solid;">
                        <input type="hidden" value= "${data[index].tiempoTardanzas}" name="tiempoSit${data[index].emple_id}[]" id="tiempoSit${data[index].emple_id}">
                        <a class="badge badge-soft-danger mr-2">
                            <img style="margin-bottom: 3px;" src="landing/images/clock.png" class="mr-2" height="12"/>${data[index].tiempoTardanzas}
                        </a>
                    </td>
                    <td name="" style="border-left-color: #c8d4de!important; border-left: 2px solid;">
                        <input type="hidden" value= "${data[index].cantTardanzas}" name="cantTardanzas${data[index].emple_id}[]" id="cantTardanzas${data[index].emple_id}">
                        <a class="badge badge-soft-danger mr-2">
                            ${data[index].cantTardanzas}
                        </a>
                    </td>`;
                    tbody += ` </tr>`;
                }
                $('#tbodyD').html(tbody);


            table = $("#tablaReport").DataTable({
                "searching": false,
                "scrollX": true,
                "ordering": false,
                "autoWidth": false,
                "bInfo": false,
                "bLengthChange": true,
                fixedHeader: true,
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
                dom: 'Blfrtip',
                buttons: [{
                    extend: 'excel',
                    className: 'btn btn-sm mt-1 mb-1',
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
                            msg = `<row r="${index}">`;
                            for (i = 0; i < data.length; i++) {
                                var key = data[i].k;
                                var value = data[i].v;
                                var bold = data[i].s;
                                msg += `<c t="inlineStr" r="${key} ${index}" s="${bold}" wpx="149">`;
                                msg += `<is>`;
                                msg += `<t>${value}</t>`;
                                msg += `</is>`;
                                msg += `</c>`;
                            }
                            msg += `</row>`;
                            return msg;
                        }
                        var fechas = 'Desde ' + $('#ID_START').val() + ' Hasta ' + $('#ID_END').val();
                        //insert
                        var r1 = Addrow(1, [{ k: 'A', v: 'Reporte de Tardanzas', s: 51 }]);
                        var r2 = Addrow(2, [{ k: 'K', v: fechas, s: 2 }]);
                        var r3 = Addrow(3, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                        var r4 = Addrow(4, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                        var r5 = Addrow(5, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                        sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + sheet.childNodes[0].childNodes[1].innerHTML;
                    },
                    sheetName: 'Tardanzas',
                    title: 'Tardanzas',
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

                                return $.trim(cambiar);
                            }
                        }
                    },
                }, {
                    extend: "pdfHtml5",
                    className: 'btn btn-sm mt-1 mb-1',
                    text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                    orientation: '',
                    pageSize: 'A1',
                    title: 'Tardanzas',
                    exportOptions: {
                        columns: ":visible:not(.noExport)",
                    },
                    customize: function (doc) {
                        doc['styles'] = {
                            table: {
                                width: '100%'
                            },
                            tableHeader: {
                                bold: true,
                                fontSize: 13,
                                color: '#ffffff',
                                fillColor: '#14274e',
                                alignment: 'left'
                            },
                            defaultStyle: {
                                fontSize: 11,
                                alignment: 'center'
                            }
                        };
                        doc.pageMargins = [20, 150, 20, 30];
                        doc.content[1].margin = [30, 0, 30, 0];
                        var colCount = new Array();
                        var tr = $('#tablaReport tbody tr:first-child');
                        var trWidth = $(tr).width();
                        $('#tablaReport').find('tbody tr:first-child td').each(function () {
                            var tdWidth = $(this).width();
                            var widthFinal = parseFloat(tdWidth * 130);
                            widthFinal = widthFinal.toFixed(2) / trWidth.toFixed(2);
                            if ($(this).attr('colspan')) {
                                for (var i = 1; i <= $(this).attr('colspan'); $i++) {
                                    colCount.push('*');
                                }
                            } else {
                                colCount.push(parseFloat(widthFinal.toFixed(2)) + '%');
                            }
                        });
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
                                    bodyNuevo.push({ text: cambiar, style: 'defaultStyle' });
                                });
                                bodyCompleto.push(bodyNuevo);
                            } else {
                                bodyCompleto.push(line);
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
                                            { text: '\nReporte de tardanzas', bold: true },
                                            { text: '\nDesde: ', bold: false }, { text: fechaD, bold: false }, { text: ' Hasta: ', bold: false }, { text: fechaH, bold: false },
                                            { text: '\n\nRazón Social :\t\t\t', bold: false }, { text: razonSocial, bold: false },
                                            { text: '\nDirección:\t\t\t\t\t', bold: false }, { text: direccion, bold: false },
                                            { text: '\nNúmero de Ruc :\t\t', bold: false }, { text: ruc, bold: false },
                                            { text: '\nFecha  :\t\t\t\t\t\t', bold: false }, { text: fecha, bold: false },
                                        ],
                                        fontSize: 10,
                                        margin: [30, 10]
                                    },
                                ],
                                margin: 20
                            };
                        };
                    }
                }],
                paging: true,
                initComplete: function () {
                    setTimeout(function () { $("#tablaReport").DataTable().draw(); }, 200);
                    console.log(this.api().data().length);
                    if (this.api().data().length == 0) {
                        $('.buttons-html5').prop("disabled", true);
                    } else {
                        $('.buttons-html5').prop("disabled", false);
                    }
                },
            });

                $(window).on('resize', function () {
                    $("#tablaReport").css('width', '100%');
                    table.draw(true);
                });
                
            } else {
                $('#btnsDescarga').hide();
                $('#tbodyD').empty();
                $('#tbodyD').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
            }
        },
        error: function () { }
    });
    $('.horasEntrada').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",

        time_24hr: true,
        enableSeconds: true,
        /*  inline:true, */
        static: true
    });

    $('.horasSalida').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",

        time_24hr: true,
        enableSeconds: true,
        /*  inline:true, */
        static: true
    });
}

/*   CARGAR TABLA DE CONTROL REMOTO   */
function cargartablaCR(fecha1,fecha2) {

    var idemp = $('#idempleado').val();

    $.ajax({
        type: "GET",
        url: "/cargarTablaTardanzas",
        data: {
            fecha1,fecha2, idemp
        },
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            if (data.length != 0) {
                razonSocial = data[0].organi_razonSocial;
                direccion = data[0].organi_direccion;
                ruc = data[0].organi_ruc;
                fecha = data[0].fecha;
                fechaD = data[0].fechaD;
                fechaH = data[0].fechaH;

                if ($.fn.DataTable.isDataTable("#tablaReport")) {
                    $("#tablaReport").DataTable().destroy();
                }
                
                // ! *********** CABEZERA DE TABLA**********
                $('#theadD').empty();
                //* ARMAR CABEZERA
                var theadTabla = `<tr>
                        <th>#&nbsp;</th>
                        <th>Código&nbsp;</th>
                        <th>Número de documento&nbsp;</th>
                        <th>Nombres y apellidos&nbsp;</th>
                        <th>Cargo&nbsp;</th>
                        <th>Área&nbsp;</th>
                        <th style="border-left-color: #c8d4de!important; border-left: 2px solid;">Tiempos de tardanza</th>
                        <th style="border-left-color: #c8d4de!important; border-left: 2px solid;">Cantidad de tardanzas</th></tr>`;
                
                //* DIBUJAMOS CABEZERA
                $('#theadD').html(theadTabla);
                // ! *********** BODY DE TABLA**********
                $('#tbodyD').empty();
                var tbody = "";
                //* ARMAMOS BODY DE TABLA
                for (let index = 0; index < data.length; index++) {
                    tbody += `<tr>
                    <td>${index+1}&nbsp;</td>
                    <td>${data[index].emple_code}&nbsp;</td>
                    <td>${data[index].emple_nDoc}&nbsp;</td>
                    <td>${data[index].perso_nombre} ${data[index].perso_apPaterno} ${data[index].perso_apMaterno} &nbsp;</td>
                    <td>${data[index].cargo_descripcion}&nbsp;</td>
                    <td>${data[index].area_descripcion}&nbsp;</td>
                    <td name="" style="border-left-color: #c8d4de!important; border-left: 2px solid;">
                        <input type="hidden" value= "${data[index].tiempoTardanzas}" name="tiempoSit${data[index].emple_id}[]" id="tiempoSit${data[index].emple_id}">
                        <a class="badge badge-soft-danger mr-2">
                            <img style="margin-bottom: 3px;" src="landing/images/clock.png" class="mr-2" height="12"/>${data[index].tiempoTardanzas}
                        </a>
                    </td>
                    <td name="" style="border-left-color: #c8d4de!important; border-left: 2px solid;">
                        <input type="hidden" value= "${data[index].cantTardanzas}" name="cantTardanzas${data[index].emple_id}[]" id="cantTardanzas${data[index].emple_id}">
                        <a class="badge badge-soft-danger mr-2">
                            ${data[index].cantTardanzas}
                        </a>
                    </td>`;
                    tbody += ` </tr>`;
                }
                $('#tbodyD').html(tbody);


            table = $("#tablaReport").DataTable({
                "searching": false,
                "scrollX": true,
                "ordering": false,
                "autoWidth": false,
                "bInfo": false,
                "bLengthChange": true,
                fixedHeader: true,
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
                dom: 'Blfrtip',
                buttons: [{
                    extend: 'excel',
                    className: 'btn btn-sm mt-1 mb-1',
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
                            msg = `<row r="${index}">`;
                            for (i = 0; i < data.length; i++) {
                                var key = data[i].k;
                                var value = data[i].v;
                                var bold = data[i].s;
                                msg += `<c t="inlineStr" r="${key} ${index}" s="${bold}" wpx="149">`;
                                msg += `<is>`;
                                msg += `<t>${value}</t>`;
                                msg += `</is>`;
                                msg += `</c>`;
                            }
                            msg += `</row>`;
                            return msg;
                        }
                        var fechas = 'Desde ' + $('#ID_START').val() + ' Hasta ' + $('#ID_END').val();
                        //insert
                        var r1 = Addrow(1, [{ k: 'A', v: 'Reporte de Tardanzas', s: 51 }]);
                        var r2 = Addrow(2, [{ k: 'K', v: fechas, s: 2 }]);
                        var r3 = Addrow(3, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                        var r4 = Addrow(4, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                        var r5 = Addrow(5, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                        sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + sheet.childNodes[0].childNodes[1].innerHTML;
                    },
                    sheetName: 'Tardanzas',
                    title: 'Tardanzas',
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

                                return $.trim(cambiar);
                            }
                        }
                    },
                }, {
                    extend: "pdfHtml5",
                    className: 'btn btn-sm mt-1 mb-1',
                    text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                    orientation: '',
                    pageSize: 'A1',
                    title: 'Tardanzas',
                    exportOptions: {
                        columns: ":visible:not(.noExport)",
                    },
                    customize: function (doc) {
                        doc['styles'] = {
                            table: {
                                width: '100%'
                            },
                            tableHeader: {
                                bold: true,
                                fontSize: 13,
                                color: '#ffffff',
                                fillColor: '#14274e',
                                alignment: 'left'
                            },
                            defaultStyle: {
                                fontSize: 11,
                                alignment: 'center'
                            }
                        };
                        doc.pageMargins = [20, 150, 20, 30];
                        doc.content[1].margin = [30, 0, 30, 0];
                        var colCount = new Array();
                        var tr = $('#tablaReport tbody tr:first-child');
                        var trWidth = $(tr).width();
                        $('#tablaReport').find('tbody tr:first-child td').each(function () {
                            var tdWidth = $(this).width();
                            var widthFinal = parseFloat(tdWidth * 130);
                            widthFinal = widthFinal.toFixed(2) / trWidth.toFixed(2);
                            if ($(this).attr('colspan')) {
                                for (var i = 1; i <= $(this).attr('colspan'); $i++) {
                                    colCount.push('*');
                                }
                            } else {
                                colCount.push(parseFloat(widthFinal.toFixed(2)) + '%');
                            }
                        });
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
                                    bodyNuevo.push({ text: cambiar, style: 'defaultStyle' });
                                });
                                bodyCompleto.push(bodyNuevo);
                            } else {
                                bodyCompleto.push(line);
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
                                            { text: '\nReporte de tardanzas', bold: true },
                                            { text: '\nDesde: ', bold: false }, { text: fechaD, bold: false }, { text: ' Hasta: ', bold: false }, { text: fechaH, bold: false },
                                            { text: '\n\nRazón Social :\t\t\t', bold: false }, { text: razonSocial, bold: false },
                                            { text: '\nDirección:\t\t\t\t\t', bold: false }, { text: direccion, bold: false },
                                            { text: '\nNúmero de Ruc :\t\t', bold: false }, { text: ruc, bold: false },
                                            { text: '\nFecha  :\t\t\t\t\t\t', bold: false }, { text: fecha, bold: false },
                                        ],
                                        fontSize: 10,
                                        margin: [30, 10]
                                    },
                                ],
                                margin: 20
                            };
                        };
                    }
                }],
                paging: true,
                initComplete: function () {
                    setTimeout(function () { $("#tablaReport").DataTable().draw(); }, 200);
                    console.log(this.api().data().length);
                    if (this.api().data().length == 0) {
                        $('.buttons-html5').prop("disabled", true);
                    } else {
                        $('.buttons-html5').prop("disabled", false);
                    }
                },
            });

                $(window).on('resize', function () {
                    $("#tablaReport").css('width', '100%');
                    table.draw(true);
                });
                
            } else {
                $('#btnsDescarga').hide();
                $('#tbodyD').empty();
                $('#tbodyD').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
            }
        },
        error: function () { }
    });
    $('.horasEntrada').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",

        time_24hr: true,
        enableSeconds: true,
        /*  inline:true, */
        static: true
    });

    $('.horasSalida').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",

        time_24hr: true,
        enableSeconds: true,
        /*  inline:true, */
        static: true
    });
}

function cambiarFCR(id) {

    f1 = $("#ID_START").val();
    f2 = moment(f1).format("YYYY-MM-DD");
    $('#pasandoV').val(f2);
    f3 = $("#ID_END").val();
    if ($.fn.DataTable.isDataTable("#tablaReport")) {

        /* $('#tablaReport').DataTable().destroy(); */
    }
    
    console.log("ID: "+id);
    if(id === 2){
        cargartablaRuta(f2,f3); 
    } else {
       cargartablaCR(f2,f3); 
    }
}


// * FUNCION DE MOSTRAR DETALLES
function cambiartabla() {
    if ($('#customSwitDetalles').is(':checked')) {
        $('[name="tiempoSitHi"]').show();
        setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
    }
    else {
        $('[name="tiempoSitHi"]').hide();
        setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
    }
}
// * FUNCION DE SWITCH DE MOSTRAR PAUSAS
function togglePausas() {
    if ($('#switPausas').is(':checked')) {
        $('[name="datosPausa"]').show();
        setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
    } else {
        $('[name="datosPausa"]').hide();
        setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
    }
}
