// * VARIABLES GENERALES
var table;
var razonSocial;
var direccion;
var ruc;
var fecha;
var fechaD;
var fechaH;

$("div.loader").hide(0);
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
/*   MOSTRAR GIF DE CARGANDO  */
$("#btnRecargaTabla").click(function(){
    $(".loader").show();
    $(".img-load").show();
    console.log("click");
});
/*   CARGAR TABLA DE CONTROL EN RUTA   */
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
                "ordering": true,
                "autoWidth": false,
                "bInfo": false,
                "bLengthChange": true,
                fixedHeader: true,
                lengthMenu: [10, 25, 50, 75, 100],
                processing: true,
                retrieve: true,
                pageLength: 10,
                language: {
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
                        pageLength: {
                            "_": "Mostrar %d registros"
                        },
                    },
                },
                dom: 'Bfrtip',
                lengthMenu: [10, 25, 50, 100],
                buttons: [{
                        extend: 'pageLength',
                        className: 'btn btn-sm mt-1 mb-1',
                    },{
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
                                    image: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAqEAAAMUCAYAAACM0v4sAAAACXBIWXMAAB7CAAAewgFu0HU+AAAFFmlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDggNzkuMTY0MDM2LCAyMDE5LzA4LzEzLTAxOjA2OjU3ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgMjEuMCAoV2luZG93cykiIHhtcDpDcmVhdGVEYXRlPSIyMDIxLTAxLTE0VDEwOjUyOjMzLTA1OjAwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAyMS0wMS0xNFQxMDo1NjoyOS0wNTowMCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAyMS0wMS0xNFQxMDo1NjoyOS0wNTowMCIgZGM6Zm9ybWF0PSJpbWFnZS9wbmciIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDplYjhlZTIyZS03M2RlLTU2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',
                                    width: 60,
                                    height: 75,
                                    margin: [30, 20]
                                },
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
                                    }
                                ],
                                margin: 20
                            };
                        };
                    }
                }],
                paging: true,
                initComplete: function () {
                    setTimeout(function () { $("#tablaReport").DataTable().draw(); }, 200);
                    //console.log(this.api().data().length);
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
                
                setTimeout(function() {
                    $(".loader").hide();
                    $(".img-load").hide();
                }, 500);
            } else {
                //$('#btnsDescarga').hide();
                $('#tbodyD').empty();
                $('#tbodyD').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
                setTimeout(function() {
                    $(".loader").hide();
                    $(".img-load").hide();
                }, 500);
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
            console.log(data.length);
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
                "ordering": true,
                "autoWidth": false,
                "bInfo": false,
                "bLengthChange": true,
                fixedHeader: true,
                lengthMenu: [10, 25, 50, 75, 100],
                processing: true,
                retrieve: true,
                pageLength: 10,
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
                        pageLength: {
                            "_": "Mostrar %d registros"
                        },
                    },
                },
                dom: 'Bfrtip',
                lengthMenu: [10, 25, 50, 100],
                buttons: [{
                        extend: 'pageLength',
                        className: 'btn btn-sm mt-1 mb-1',
                    },{
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
                    //console.log(this.api().data().length);
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
                setTimeout(function() {
                    $(".loader").hide();
                    $(".img-load").hide();
                }, 500);
            } else {
                ///$('#btnsDescarga').hide();
                $('#tbodyD').empty();
                $('#tbodyD').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
                setTimeout(function() {
                    $(".loader").hide();
                    $(".img-load").hide();
                }, 500);
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

/*   CARGAR TABLA DE CONTROL EN PUERTA   */
function cargartablaPuerta(fecha1,fecha2) {
    var idemp = $('#idempleado').val();
    $.ajax({
        type: "GET",
        url: "/cargarTablaTardanzasPuerta",
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
                "ordering": true,
                "autoWidth": false,
                "bInfo": false,
                "bLengthChange": true,
                fixedHeader: true,
                lengthMenu: [10, 25, 50, 75, 100],
                processing: true,
                retrieve: true,
                pageLength: 10,
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
                        pageLength: {
                            "_": "Mostrar %d registros"
                        },
                    },
                },
                dom: 'Bfrtip',
                lengthMenu: [10, 25, 50, 100],
                buttons: [{
                        extend: 'pageLength',
                        className: 'btn btn-sm mt-1 mb-1',
                    },{
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
                    //console.log(this.api().data().length);
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
                setTimeout(function() {
                    $(".loader").hide();
                    $(".img-load").hide();
                }, 500);
            } else {
                ///$('#btnsDescarga').hide();
                $('#tbodyD').empty();
                $('#tbodyD').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
                setTimeout(function() {
                    $(".loader").hide();
                    $(".img-load").hide();
                }, 500);
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
    
    if(id === 2){
        cargartablaRuta(f2,f3); 
    } else {
        if(id === 3){
            cargartablaPuerta(f2,f3); 
        } else {
            cargartablaCR(f2,f3); 
        }
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
