//* FECHA
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "D, j F",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: true,
    conjunction: " a ",
    onChange: function (selectedDates) {
        var _this = this;
        var dateArr = selectedDates.map(function (date) { return _this.formatDate(date, 'Y-m-d'); });
        $('#ID_START').val(dateArr[0]);
        $('#ID_END').val(dateArr[1]);
    },
});
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
// * INICIALIZAR TABLA
var table;
function inicializarTabla() {
    table = $("#tablaReport").DataTable({
        "searching": false,
        "scrollX": true,
        "ordering": false,
        "autoWidth": false,
        "bInfo": false,
        "bLengthChange": false,
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
        dom: 'Bfrtip',
        buttons: [{
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
            sheetName: 'Asistencia',
            title: 'Asistencia',
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
            className: 'btn btn-sm mt-1',
            text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
            orientation: 'landscape',
            pageSize: 'A1',
            title: 'Asistencia',
            exportOptions: {
                columns: ":visible:not(.noExport)"
            },
            customize: function (doc) {
                doc['styles'] = {
                    table: {
                        width: '100%'
                    },
                    tableHeader: {
                        bold: true,
                        fontSize: 11,
                        color: '#6c757d',
                        fillColor: '#ffffff',
                        alignment: 'left'
                    },
                    defaultStyle: {
                        fontSize: 10,
                        alignment: 'center'
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
                                    { text: '\nCONTROL REGISTRO DE ASISTENCIA', bold: true },
                                    { text: '\n\nRazon Social:\t\t\t\t\t\t', bold: false }, { text: razonSocial, bold: false },
                                    { text: '\nDireccion:\t\t\t\t\t\t\t', bold: false }, { text: '\t' + direccion, bold: false },
                                    { text: '\nNumero de Ruc:\t\t\t\t\t', bold: false }, { text: ruc, bold: false },
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
        }],
        paging: true,
        initComplete: function () {
            setTimeout(function () { $("#tablaReport").DataTable().draw(); }, 200);
        },
    });
}
function cargartabla(fecha1, fecha2) {

    var idemp = $('#idempleado').val();
    if (idemp == 0) {
        $.notifyClose();
        $.notify({
            message: '\nSeleccione empleado.',
            icon: '/landing/images/alert1.svg',
        }, {
            icon_type: 'image',
            allow_dismiss: true,
            newest_on_top: true,
            delay: 6000,
            template: '<div data-notify="container" class="col-xs-8 col-sm-3 text-center alert" style="background-color: #f2dede;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
        return false;
    }
    $.ajax({
        type: "GET",
        url: "/reporteTablaEmp",
        data: {
            fecha1, fecha2, idemp
        },
        async: false,
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
        success: function (data) {
            console.log(data);
            if (data.length != 0) {
                if ($.fn.DataTable.isDataTable("#tablaReport")) {
                    $("#tablaReport").DataTable().destroy();
                }
                // ! *********** CABEZERA DE TABLA**********
                $('#theadD').empty();
                //* CANTIDAD MININA DE COLUMNAS PARA MARACIONES
                var cantidadColumnasHoras = 0;
                // * CANTIDAD MINIMA DE PAUSAS
                var cantidadColumnasPausas = 1;
                data.datos.forEach(element => {
                    if (cantidadColumnasHoras < element.marcaciones.length) {
                        cantidadColumnasHoras = element.marcaciones.length;
                    }
                    if (cantidadColumnasPausas < element.pausas.length) {
                        cantidadColumnasPausas = element.pausas.length;
                    }
                });
                //* ARMAR CABEZERA
                var theadTabla = `<tr>
                                    <th>CC&nbsp;</th>
                                    <th>Fecha&nbsp;</th>
                                    <th>Horario&nbsp;</th>
                                   `;
                // * MARCACIONES
                for (let j = 0; j < cantidadColumnasHoras; j++) {
                    theadTabla += `<th style="border-left-color: #c8d4de!important;border-left: 2px solid;">
                                        Marca de entrada
                                    </th>
                                    <th>Marca de salida</th>
                                    <th id="tSitio" name="tiempoSitHi">Tiempo en sitio</th><th  name="tiempoSitHi">Tardanza</th>
                                    <th  name="tiempoSitHi">Faltas</th><th  name="tiempoSitHi">Incidencias</th>`;
                }
                // * PAUSAS
                for (let p = 0; p < cantidadColumnasPausas; p++) {
                    theadTabla += `<th style="border-left-color: #c8d4de!important;border-left: 2px solid;" name="datosPausa">Pausa</th>
                                        <th name="datosPausa">Horario pausa</th>
                                        <th name="datosPausa">Tiempo pausa</th>
                                        <th name="datosPausa">Exceso pausa</th>`;
                }
                theadTabla += `<th>Marcación T.</th><th >Tardanza T.</th>
                <th >Faltas T.</th>
                <th >Incidencias T.</th> 
                <th style="border-left:2px solid #f05454!important;">Tiempo T.</th>
                </tr>`;
                //* DIBUJAMOS CABEZERA
                $('#theadD').html(theadTabla);
                // ! *********** BODY DE TABLA**********
                $('#tbodyD').empty();
                var tbody = "";
                //* ARMAMOS BODY DE TABLA
                for (let index = 0; index < data.datos.length; index++) {
                    var idHorarioM = [];
                    var contenidoData = data.datos[index];
                    tbody += `<tr>
                    <td>${(index + 1)}&nbsp;</td>
                    <td>${moment(contenidoData.fecha).format('DD/MM/YYYY')}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    // * HORARIO
                    if (contenidoData.horario != 0) {
                        tbody += `<td>${contenidoData.horario}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    } else {
                        tbody += `<td>Sin horario&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    }
                    // * ARMAR MARCACIONES
                    var tbodyEntradaySalida = "";
                    var sumaTiempos = moment("00:00:00", "HH:mm:ss");
                    var tiempoTotal = moment("00:00:00", "HH:mm:ss");
                    var sumaTardanzas = moment("00:00:00", "HH:mm:ss");
                    for (let i = 0; i < contenidoData.marcaciones.length; i++) {
                        // * TIEMPO DE PAUSA
                        var tiempoHoraPausa = "00";
                        var tiempoMinutoPausa = "00";
                        var tiempoSegundoPausa = "00";
                        // * TIEMPO DE EXCESO DE PAUSA
                        var tiempoHoraExceso = "00";
                        var tiempoMinutoExceso = "00";
                        var tiempoSegundoExceso = "00";
                        // * TARDANZA
                        var segundosTardanza = "00";
                        var minutosTardanza = "00";
                        var horasTardanza = "00";
                        // * TIEMPO EN SITIO
                        var segundosTiempo = "00";
                        var minutosTiempo = "00";
                        var horasTiempo = "00";
                        var contenidoMarcacion = contenidoData.marcaciones[i];
                        // * SI TIENE TIEMPO DE ENTRADA
                        if (contenidoMarcacion.entrada != 0) {
                            tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;border-left: 2px solid;">
                                                        <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>
                                                        ${moment(contenidoMarcacion.entrada).format("HH:mm:ss")}
                                                    </td>`;
                            // * TARDANZA
                            if (contenidoMarcacion.idHorario != 0) {
                                var horaInicial = moment(contenidoMarcacion.entrada);
                                // ******************************* TARDANZA ***************************************
                                // ! PARA QUE TOME SOLO TARDANZA EN LA PRIMERA MARCACION
                                if (!idHorarioM.includes(contenidoMarcacion.idHorario)) {
                                    idHorarioM.push(contenidoMarcacion.idHorario);  // : AGREGAMOS EL ID AL ARRAY
                                    var horaInicioHorario = moment(contenidoData.horarioIni);
                                    var horaConTolerancia = horaInicioHorario.clone().add({ "minutes": contenidoData.tolerancia });
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
                                    }
                                }
                                sumaTardanzas = sumaTardanzas.add({ "hours": horasTardanza, "minutes": minutosTardanza, "seconds": segundosTardanza });
                            }
                            // * SI TIENE TIEMPO DE SALIDA
                            if (contenidoMarcacion.salida != 0) {
                                tbodyEntradaySalida += `<td>
                                                            <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> 
                                                            ${moment(contenidoMarcacion.salida).format("HH:mm:ss")}
                                                        </td>`;
                                // * CALCULAR TIEMPO TOTAL , TIEMPO DE PAUSA Y EXCESO DE PAUSAS
                                var horaFinal = moment(contenidoMarcacion.salida);
                                var horaInicial = moment(contenidoMarcacion.entrada);
                                if (horaFinal.isSameOrAfter(horaInicial)) {
                                    // * TIEMPO TOTAL TRABAJADA
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
                                    sumaTiempos = sumaTiempos.add({ "hours": horasTiempo, "minutes": minutosTiempo, "seconds": segundosTiempo });
                                    tiempoTotal = tiempoTotal.add({ "hours": horasTiempo, "minutes": minutosTiempo, "seconds": segundosTiempo });
                                    if (contenidoMarcacion.idHorario != 0) {
                                        // ****************************************** PAUSAS ****************************************
                                        for (let indice = 0; indice < contenidoData.pausas.length; indice++) {
                                            var contenidoP = contenidoData.pausas[indice];
                                            if (contenidoP.idH == contenidoMarcacion.idHorario) {
                                                var fechaI = horaInicial.clone().format("YYYY-MM-DD");
                                                var fechaF;
                                                if (contenidoP.inicio > contenidoP.fin) {
                                                    fechaF = horaInicial.clone().add(1, 'day').format("YYYY-MM-DD");
                                                } else {
                                                    fechaF = horaInicial.clone().format("YYYY-MM-DD");
                                                }
                                                var pausaI = moment(fechaI + " " + contenidoP.inicio);
                                                var pausaF = moment(fechaF + " " + contenidoP.fin);
                                                // ! COMPROBAR SI TIENE SALIDA PARA PAUSA
                                                var sumaToleranciaPausa = moment(
                                                    pausaI.clone().add(
                                                        { "minutes": contenidoP.toleranciaI }    // : CLONAMOS EL TIEMPO Y SUMAR CON TOLERANCIA
                                                    ).toString());
                                                var restaToleranciaPausa = moment(
                                                    pausaI.clone().subtract(
                                                        { "minutes": contenidoP.toleranciaI }
                                                    ).toString()); //: CLONAMOS EL TIEMPO Y RESTAR CON TOLERANCIA
                                                // ! CONDICIONALES QUE SI HORA FINAL DE LA MARCACION ESTA ENTRE LA RESTA CON LA TOLERANCIA Y LA SUMA CON LA TOLERANCIA
                                                if (horaFinal.isAfter(restaToleranciaPausa) && horaFinal.isBefore(sumaToleranciaPausa)) {
                                                    // * VERIFICAR SI YA TENEMOS OTRA MARCACION SIGUIENTE
                                                    if (contenidoData.marcaciones[i + 1] != undefined) {
                                                        if (contenidoData.marcaciones[i + 1].entrada != undefined) {
                                                            var horaEntradaDespues = moment(contenidoData.marcaciones[i + 1].entrada);    //: -> OBTENER ENTRADA DE LA MARCACION SIGUIENTE
                                                            var restarTiempoMarcacion = horaEntradaDespues - horaFinal;                 //: -> RESTAR PARA OBTENER LA CANTIDAD EN PAUSA
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
                                                            // * RESTAR EL TIEMPO DE PAUSA
                                                            tiempoTotal = tiempoTotal.add(
                                                                {
                                                                    "hours": -tiempoHoraPausa,
                                                                    "minutes": -tiempoMinutoPausa,
                                                                    "seconds": -tiempoSegundoPausa
                                                                }
                                                            );
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
                                                } else {
                                                    if (pausaI.isAfter(horaInicial) && pausaI.isBefore(horaFinal)) {
                                                        var momentpausainicio = moment(contenidoP.inicio, ["HH:mm"]);
                                                        var momentpausafin = moment(contenidoP.fin, ["HH:mm"]);
                                                        var restaPausa = momentpausafin - momentpausainicio;
                                                        tiempoSegundoPausa = moment.duration(restaPausa).seconds();
                                                        tiempoMinutoPausa = moment.duration(restaPausa).minutes();
                                                        tiempoHoraPausa = Math.trunc(moment.duration(restaPausa).asHours());
                                                        if (tiempoHoraPausa < 10) {
                                                            tiempoHoraPausa = '0' + tiempoHoraPausa;
                                                        }
                                                        if (tiempoMinutoPausa < 10) {
                                                            tiempoMinutoPausa = '0' + tiempoMinutoPausa;
                                                        }
                                                        if (tiempoSegundoPausa < 10) {
                                                            tiempoSegundoPausa = '0' + tiempoSegundoPausa;
                                                        }
                                                        // ************************* RESTAR LA PAUSA ******************************
                                                        tiempoTotal = tiempoTotal.add({
                                                            "hours": -tiempoHoraPausa,
                                                            "minutes": -tiempoMinutoPausa,
                                                            "seconds": -tiempoSegundoPausa
                                                        });
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                tbodyEntradaySalida += `<td>
                                                            <span class="badge badge-soft-secondary">
                                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                No tiene salida
                                                            </span>
                                                        </td>`;
                            }
                        } else {
                            if (contenidoMarcacion.salida != 0) {
                                tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;border-left: 2px solid;">
                                                            <span class="badge badge-soft-warning">
                                                                <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                                No tiene entrada
                                                            </span>
                                                        </td>`;
                                tbodyEntradaySalida += `<td>
                                                            <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> 
                                                            ${moment(contenidoMarcacion.salida).format("HH:mm:ss")}
                                                        </td>`;
                            }
                        }
                        tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                    <a class="badge badge-soft-primary mr-2">
                                                        <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                        ${horasTiempo}:${minutosTiempo}:${segundosTiempo}
                                                    </a>
                                                </td>
                                                <td name="tiempoSitHi">
                                                    <a class="badge badge-soft-danger mr-2">
                                                        <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                        ${horasTardanza}:${minutosTardanza}:${segundosTardanza}
                                                    </a>
                                                </td>
                                                <td name="tiempoSitHi">--</td>
                                                <td name="tiempoSitHi">--</td>`;
                    }
                    for (let m = contenidoData.marcaciones.length; m < cantidadColumnasHoras; m++) {
                        tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;border-left: 2px solid;">
                                                    ---
                                                </td>
                                                <td>---</td>
                                                <td name="tiempoSitHi">---</td>
                                                <td name="tiempoSitHi">--</td>
                                                <td name="tiempoSitHi">--</td>
                                                <td name="tiempoSitHi">--</td>`;
                    }
                    tbody += tbodyEntradaySalida;
                    // * ARMAR PAUSAS
                    var tbodyPausas = "";
                    for (let p = 0; p < contenidoData.pausas.length; p++) {
                        var pausaData = contenidoData.pausas[p];
                        tbodyPausas += `<td style="border-left-color: #c8d4de!important;border-left: 2px solid;" name="datosPausa">${pausaData.descripcion}</td>
                                <td name="datosPausa">${pausaData.inicio} - ${pausaData.fin}</td>
                                <td name="datosPausa">
                                    <a class="badge badge-soft-primary mr-2">
                                        <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                        ${tiempoHoraPausa}:${tiempoMinutoPausa}:${tiempoSegundoPausa}
                                    </a>
                                </td>
                                <td name="datosPausa">
                                    <a class="badge badge-soft-danger mr-2">
                                        <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                        ${tiempoHoraExceso}:${tiempoMinutoExceso}:${tiempoSegundoExceso}
                                    </a>
                                </td>`;
                    }
                    for (let cp = contenidoData.pausas.length; cp < cantidadColumnasPausas; cp++) {
                        tbodyPausas += `<td style="border-left-color: #c8d4de!important;border-left: 2px solid;" name="datosPausa" class="text-center">----</td>
                                        <td name="datosPausa" class="text-center">-----</td>
                                        <td name="datosPausa" class="text-center">-----</td>
                                        <td name="datosPausa" class="text-center">------</td>`;
                    }
                    tbody += tbodyPausas;
                    tbody += `<td style="border-left-color: #c8d4de!important;border-left: 2px solid;">
                                <a class="badge badge-soft-primary mr-2">
                                    <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                    ${sumaTiempos.format("HH:mm:ss")}
                                </a>
                            </td> 
                            <td>
                                <a class="badge badge-soft-danger mr-2">
                                    <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                    ${sumaTardanzas.format("HH:mm:ss")}
                                </a>
                            </td>
                            <td >--</td>
                            <td >--</td>
                            <td style="border-left:2px solid #f05454!important;" data-toggle="tooltip" data-placement="right" title="Marcación total - (Tiempo pausa)"
                                data-original-title="">
                                ${tiempoTotal.format("HH:mm:ss")}
                            </td>
                            </tr>`;
                }

                $('#tbodyD').html(tbody);
                inicializarTabla();
                $(window).on('resize', function () {
                    $("#tablaReport").css('width', '100%');
                    table.draw(true);
                });
                // * DETALLES
                if ($('#customSwitDetalles').is(':checked')) {
                    $('[name="tiempoSitHi"]').show();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                }
                else {
                    $('[name="tiempoSitHi"]').hide();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                }
                // * SWITCH DE PAUSAS
                if ($('#switPausas').is(':checked')) {
                    $('[name="datosPausa"]').show();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                } else {
                    $('[name="datosPausa"]').hide();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                }
            } else {
                $('#customSwitDetalles').prop("disabled", true);
                $('#switPausas').prop("disabled", true);
                if ($.fn.DataTable.isDataTable("#tablaReport")) {
                    $("#tablaReport").DataTable().destroy();
                }
                $('#tbodyD').empty();
                inicializarTabla();
            }
        },
        error: function () { }
    });
}

function cambiarF() {

    f1 = $("#ID_START").val();
    f2 = moment(f1).format("YYYY-MM-DD");
    $('#pasandoV').val(f2);
    f3 = $("#ID_END").val();
    if ($('#idempleado').val() == "" || $('#idempleado').val() == null) {
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
    } else {
        cargartabla(f2, f3);
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
/////////GENERAR EXCEL////////////
function s2ab(s) {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
}
function doexcel() {
    var wb = XLSX.utils.table_to_book(document.getElementById("tableZoomI"), { sheet: "Sheet 1" })	//my html table

    wb["Sheets"]["Sheet 1"]["!cols"] = [{ wpx: 149 }, { wpx: 130 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 },
    { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 },
    { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 },
    { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 }, { wpx: 100 },];

    console.log(wb);

    var wbout = XLSX.write(wb, { bookType: 'xlsx', bookSST: true, type: 'binary' });
    saveAs(new Blob([s2ab(wbout)], { type: "application/octet-stream" }), 'Asistencia.xlsx');
}
function generatePDF() {

    var doc = new jsPDF('l', 'pt', 'legal');
    var htmlstring = '';
    var tempVarToCheckPageHeight = 0;
    var pageHeight = 0;
    pageHeight = doc.internal.pageSize.height;
    specialElementHandlers = {
        // element with id of "bypass" - jQuery style selector
        '#bypassme': function (element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
        }
    };

    var y = 20;
    doc.setLineWidth(2);

    doc.autoTable({
        html: '#Encabezado',
        startY: 40,
        theme: 'plain'

    })
    doc.autoTable({
        html: '#tablaReportI',
        startY: 250

    })
    doc.save('Asistencia.pdf');

}

