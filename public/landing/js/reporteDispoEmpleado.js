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
$('#customSwitDetalles').prop("disabled", true);
$('#switPausas').prop("disabled", true);
// * INICIALIZAR TABLA
var table;
var razonSocial;
var direccion;
var ruc;
var dni;
var nombre;
var area;
var cargo;
var fecha;
var fechaD;
var fechaH;
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
                var downrows = 10;
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
                var r1 = Addrow(1, [{ k: 'A', v: 'Registro Permanente de Control de Asistencia', s: 51 }]);
                var r2 = Addrow(2, [{ k: 'A', v: fechas, s: 2 }]);
                var r3 = Addrow(3, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                var r4 = Addrow(4, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                var r5 = Addrow(5, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                var r6 = Addrow(7, [{ k: 'A', v: 'DNI:', s: 2 }, { k: 'C', v: dni, s: 0 }]);
                var r7 = Addrow(8, [{ k: 'A', v: 'Apellidos y Nombres:', s: 2 }, { k: 'C', v: nombre, s: 0 }]);
                var r8 = Addrow(9, [{ k: 'A', v: 'Area:', s: 2 }, { k: 'C', v: area, s: 0 }]);
                var r9 = Addrow(10, [{ k: 'A', v: 'Cargo:', s: 2 }, { k: 'C', v: cargo, s: 0 }]);
                sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + r6 + r7 + r8 + r9 + sheet.childNodes[0].childNodes[1].innerHTML;
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
                columns: ":visible:not(.noExport)",
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
                                    { text: '\nRegistro Permanente de Control de Asistencia\n', bold: true },
                                    { text: '\nRazon Social:\t\t\t\t\t\t\t\t\t', bold: false }, { text: razonSocial, bold: false },
                                    { text: '\nDireccion:\t\t\t\t\t\t\t\t\t\t\t', bold: false }, { text: direccion, bold: false },
                                    { text: '\nNumero de Ruc:\t\t\t\t\t\t\t\t', bold: false }, { text: ruc, bold: false },
                                    { text: '\nDNI:\t\t\t\t\t\t\t\t\t\t\t\t\t', bold: false }, { text: dni, bold: false },
                                    { text: '\nApellidos y Nombres:\t\t\t\t\t\t', bold: false }, { text: nombre, bold: false },
                                    { text: '\nArea:\t\t\t\t\t\t\t\t\t\t\t\t\t', bold: false }, { text: area, bold: false },
                                    { text: '\nCargo:\t\t\t\t\t\t\t\t\t\t\t\t', bold: false }, { text: cargo, bold: false }
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
            console.log(this.api().data().length);
            if (this.api().data().length == 0) {
                $('.buttons-html5').prop("disabled", true);
            } else {
                $('.buttons-html5').prop("disabled", false);
            }
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
            if (data.length != 0) {
                $('#customSwitDetalles').prop("disabled", false);
                $('#switPausas').prop("disabled", false);
                razonSocial = data.organi_razonSocial;
                direccion = data.organi_direccion;
                ruc = data.organi_ruc;
                dni = data.nDoc;
                nombre = data.nombre + "\t" + data.apPaterno + "\t" + data.apMaterno;
                area = (data.area == null) ? "------" : data.area;
                cargo = (data.cargo == null) ? "------" : data.cargo;
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
                theadTabla += `<th style="border-left-color: #c8d4de!important;border-left: 2px solid;">Marcación T.</th><th >Tardanza T.</th>
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
                    var idPausas = [];
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
                    // * TIEMPO DE PAUSA
                    var tiempoHoraPausa = "00";
                    var tiempoMinutoPausa = "00";
                    var tiempoSegundoPausa = "00";
                    // * TIEMPO DE EXCESO DE PAUSA
                    var tiempoHoraExceso = "00";
                    var tiempoMinutoExceso = "00";
                    var tiempoSegundoExceso = "00";
                    for (let i = 0; i < contenidoData.marcaciones.length; i++) {
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
                                                if (!idPausas.includes(contenidoP.id)) {
                                                    // * PAUSAS
                                                    tiempoHoraPausa = "00";        //: HORAS TARDANZA
                                                    tiempoMinutoPausa = "00";      //: MINUTOS TARDANZA
                                                    tiempoSegundoPausa = "00";     //: SEGUNDOS TARDANZA
                                                    //* EXCESO
                                                    tiempoHoraExceso = "00";
                                                    tiempoMinutoExceso = "00";
                                                    tiempoSegundoExceso = "00";
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
                                                        idPausas.push(contenidoP.id);
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
                                                            idPausas.push(contenidoP.id);
                                                        }
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


function cargartablaCR(fecha1,fecha2) {

    var idemp = $('#idempleado').val();

    $.ajax({
        type: "GET",
        url: "/reporteTablaEmpCR",
        data: {
            fecha1,fecha2, idemp
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
            if (data.length != 0) {
                razonSocial = data[0].organi_razonSocial;
                direccion = data[0].organi_direccion;
                ruc = data[0].organi_ruc;
                dni = data[0].nDoc;
                fecha = data[0].fecha;
                fechaD = data[0].fechaD;
                fechaH = data[0].fechaH;

                if ($.fn.DataTable.isDataTable("#tablaReport")) {
                    $("#tablaReport").DataTable().destroy();
                }
                $('#btnsDescarga').show();
                // ! *********** CABEZERA DE TABLA**********
                $('#theadD').empty();
                //* CANTIDAD MININO VALOR DE COLUMNAS PARA HORAS
                var cantidadColumnasHoras = 1;
                for (let i = 0; i < data.length; i++) {
                    //* OBTENER CANTIDAD TOTAL DE COLUMNAS
                    if (cantidadColumnasHoras < data[i].marcaciones.length) {
                        cantidadColumnasHoras = data[i].marcaciones.length;
                    }
                }
                //* ARMAR CABEZERA
                var theadTabla = `<tr>
                                    <th>#&nbsp;</th>
                                    <th>Código&nbsp;</th>
                                    <th>Documento&nbsp;</th>
                                    <th>Nombres&nbsp;</th>
                                    <th>Cargo&nbsp;</th>
                                    <th>Área&nbsp;</th>`;
                for (let j = 0; j < cantidadColumnasHoras; j++) {
                    theadTabla += `<th style="border-left-color: #c8d4de!important;
                    border-left: 2px solid;">Tardanza tiempo</th>`;
                }
                theadTabla += `</tr>`;
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
                    <td>${data[index].perso_apPaterno} ${data[index].perso_apMaterno} ${data[index].perso_nombre}&nbsp;</td>
                    <td>${data[index].cargo_descripcion}&nbsp;</td>
                    <td>${data[index].area_descripcion}&nbsp;</td>`;

                    //* ARMAR Y ORDENAR MARCACIONES
                    var tbodyEntradaySalida = "";
                    var sumaTiempos = moment("00:00:00", "HH:mm:ss");
                    //: HORA
                    for (let h = 0; h < 24; h++) {
                        for (let j = 0; j < 1; j++) {
                            var marcacionData = data[index].marcaciones;
                            if (marcacionData.entrada != 0) {
                                if (h == moment(marcacionData.entrada).format("HH")) {
                                    var permisoModificarCS=$('#modifReporte').val();
                                    
                                    if (marcacionData.salida != 0) {
                                        var permisoModificarCE1=$('#modifReporte').val();
                                        
                                        var horaFinal = moment(marcacionData.salida);
                                        var horaInicial = moment(marcacionData.entrada);
                                        if (horaFinal.isSameOrAfter(horaInicial)) {
                                            var tiempoRestante = horaFinal - horaInicial;
                                            var segundosTiempo = moment.duration(tiempoRestante).seconds();
                                            var minutosTiempo = moment.duration(tiempoRestante).minutes();
                                            var horasTiempo = Math.trunc(moment.duration(tiempoRestante).asHours());
                                            if (horasTiempo < 10) {
                                                horasTiempo = '0' + horasTiempo;
                                            }
                                            if (minutosTiempo < 10) {
                                                minutosTiempo = '0' + minutosTiempo;
                                            }
                                            if (segundosTiempo < 10) {
                                                segundosTiempo = '0' + segundosTiempo;
                                            }
                                            tbodyEntradaySalida += `<td name="">
                                                                    <input type="hidden" value= "${data[index].tardanzas}" name="tiempoSit${data[index].emple_id}[]" id="tiempoSit${data[index].emple_id}">
                                                                    <a class="badge badge-soft-danger mr-2">
                                                                        ${data[index].tardanzas}
                                                                    </a>
                                                                </td>`;
                                            sumaTiempos = moment(sumaTiempos).add(segundosTiempo, 'seconds');
                                            sumaTiempos = moment(sumaTiempos).add(minutosTiempo, 'minutes');
                                            sumaTiempos = moment(sumaTiempos).add(horasTiempo, 'hours');
                                        }
                                    }
                                }
                            } else {
                                if (marcacionData.salida != 0) {
                                    if (h == moment(marcacionData.salida).format("HH")) {
                                        //* COLUMNA DE ENTRADA
                                        var permisoModificarE=$('#modifReporte').val();
                                        //* COLUMNA DE SALIDA
                                        var permisoModificarCE2=$('#modifReporte').val();
                                    }
                                }
                            }
                        }
                    }
                    for (let m = data[index].marcaciones.length; m < cantidadColumnasHoras; m++) {
                        tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;
                        border-left: 2px solid;">---</td><td>---</td><td>---</td><td name="tiempoSitHi">---</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                    }
                    tbody += tbodyEntradaySalida;
                    tbody += ` </tr>`;
                }
                $('#tbodyD').html(tbody);


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
                    className: 'btn btn-sm mt-1',
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
                if ($('#customSwitDetalles').is(':checked')) {
                    $('[name="tiempoSitHi"]').show();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                }
                else {
                    $('[name="tiempoSitHi"]').hide();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                }
            } else {
                $('#btnsDescarga').hide();
                $('#tbodyD').empty();
                $('#tbodyD').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
            }

            /* CREANDO TABLAS PARA IMPORTAR */
            if (data.length != 0) {

                // ! *********** CABEZERA DE TABLA**********
                $('#theadDI').empty();
                //* CANTIDAD MININO VALOR DE COLUMNAS PARA HORAS
                var cantidadColumnasHorasI = 1;
                for (let i = 0; i < data.length; i++) {
                    //* OBTENER CANTIDAD TOTAL DE COLUMNAS
                    if (cantidadColumnasHorasI < data[i].marcaciones.length) {
                        cantidadColumnasHorasI = data[i].marcaciones.length;
                    }
                }
                //* ARMAR CABEZERA
                var theadTablaI = `<tr><th>Código&nbsp;</th>
                                    <th>Documento&nbsp;</th>
                                    <th>Nombres&nbsp;</th>
                                    <th>Cargo&nbsp;</th>
                                    <th>Área&nbsp;</th>`;

                for (let j = 0; j < cantidadColumnasHorasI; j++) {
                    theadTablaI += `<th  class="" id="" name="">Tardanza tiempo</th>`;
                }
                theadTablaI += `</tr>`;


                 var inicioR=moment($('#ID_START').val()).format('DD/MM/YYYY');
                 var finR=moment($('#ID_END').val()).format('DD/MM/YYYY');
                $('#RangoFechas').html(' Desde '+inicioR+' '+' Hasta '+' '+finR)

                //* DIBUJAMOS CABEZERA
                $('#theadDI').html(theadTablaI);
                // ! *********** BODY DE TABLA**********
                $('#tbodyIDI').empty();
                var tbodyI = "";
                //* ARMAMOS BODY DE TABLA
                for (let index = 0; index < data.length; index++) {                    
                    tbodyI += `<tr>
                    <td>${data[index].emple_code}&nbsp;</td>
                    <td>${data[index].emple_nDoc}&nbsp;</td>
                    <td>${data[index].perso_apPaterno} ${data[index].perso_apMaterno} ${data[index].perso_nombre}&nbsp;</td>
                    <td>${data[index].cargo_descripcion}&nbsp;</td>
                    <td>${data[index].area_descripcion}&nbsp;</td>
                    <td style='text-align: center;'>${data[index].tardanzas}&nbsp;</td>`;

                    //* ARMAR Y ORDENAR MARCACIONES
                    var tbodyIEntradaySalida = "";
                    var sumaTiemposI = moment("00:00:00", "HH:mm:ss");
                    //: HORA
                }
                $('#tbodyIDI').html(tbodyI);


                if ($('#customSwitDetalles').is(':checked')) {
                    $('[name="tiempoSitHi"]').show();

                }
                else {
                    $('[name="tiempoSitHi"]').hide();

                }
            } else {
                $('#tbodyIDI').empty();
                $('#tbodyIDI').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
            }
            /*  */

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

function cambiarFCR() {

    f1 = $("#ID_START").val();
    f2 = moment(f1).format("YYYY-MM-DD");
    $('#pasandoV').val(f2);
    f3 = $("#ID_END").val();
    if ($.fn.DataTable.isDataTable("#tablaReport")) {

        /* $('#tablaReport').DataTable().destroy(); */
    }
    console.log(f2);
    cargartablaCR(f2,f3);
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
