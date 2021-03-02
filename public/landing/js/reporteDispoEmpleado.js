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
// * INICIALIZAR TABLA
var table = {};
var dataT = {};
var razonSocial;
var direccion;
var ruc;
var dni;
var nombre;
var area;
var cargo;
var paginaGlobal = 10;
function inicializarTabla() {
    table = $("#tablaReport").DataTable({
        "searching": false,
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
            processing: "<img src='landing/images/logoR.gif' height='60'>\n&nbsp;&nbsp;&nbsp;&nbsp;Generando informe...",
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
                }
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
                    var r1 = Addrow(1, [{ k: 'A', v: 'REGISTRO PERMANENTE DE CONTROL DE ASISTENCIA', s: 51 }]);
                    var r2 = Addrow(2, [{ k: 'A', v: fechas, s: 2 }]);
                    var r3 = Addrow(3, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                    var r4 = Addrow(4, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                    var r5 = Addrow(5, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                    var r6 = Addrow(7, [{ k: 'A', v: 'DNI:', s: 2 }, { k: 'C', v: dni, s: 0 }]);
                    var r7 = Addrow(8, [{ k: 'A', v: 'Apellidos y Nombres:', s: 2 }, { k: 'C', v: nombre, s: 0 }]);
                    var r8 = Addrow(9, [{ k: 'A', v: 'Área:', s: 2 }, { k: 'C', v: area, s: 0 }]);
                    var r9 = Addrow(10, [{ k: 'A', v: 'Cargo:', s: 2 }, { k: 'C', v: cargo, s: 0 }]);
                    sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + r6 + r7 + r8 + r9 + sheet.childNodes[0].childNodes[1].innerHTML;
                },
                sheetName: 'REGISTRO PERMANENTE DE CONTROL DE ASISTENCIA',
                title: 'REGISTRO PERMANENTE DE CONTROL DE ASISTENCIA',
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
                title: 'REGISTRO PERMANENTE DE CONTROL DE ASISTENCIA',
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
                                cambiar = cambiar.split("/");
                                cambiar = cambiar.map(s => s.trim()).join("/")
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
                                        { text: '\nREGISTRO PERMANENTE DE CONTROL DE ASISTENCIA\n', bold: true },
                                        { text: '\nRazón Social:\t\t\t\t\t\t\t\t\t', bold: false }, { text: razonSocial, bold: false },
                                        { text: '\nDirección:\t\t\t\t\t\t\t\t\t\t\t', bold: false }, { text: direccion, bold: false },
                                        { text: '\nNúmero de Ruc:\t\t\t\t\t\t\t\t', bold: false }, { text: ruc, bold: false },
                                        { text: '\nDNI:\t\t\t\t\t\t\t\t\t\t\t\t\t', bold: false }, { text: dni, bold: false },
                                        { text: '\nApellidos y Nombres:\t\t\t\t\t\t', bold: false }, { text: nombre, bold: false },
                                        { text: '\nÁrea:\t\t\t\t\t\t\t\t\t\t\t\t\t', bold: false }, { text: area, bold: false },
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
            }
        ],
        paging: true,
        initComplete: function (settings, data, dataIndex) {
            dataT = this;
            setTimeout(function () { $("#tablaReport").DataTable().draw(); }, 200);
            vistasDeTabla();
            if (this.api().data().length == 0) {
                $('.buttons-page-length').prop("disabled", true);
                $('.buttons-html5').prop("disabled", true);
            } else {
                $('.buttons-page-length').prop("disabled", false);
                $('.buttons-html5').prop("disabled", false);
            }
            this.api().page.len(paginaGlobal).draw(false);
        },
        drawCallback: function () {
            var api = this.api();
            var len = api.page.len();
            paginaGlobal = len;
        }
    });
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
});
$('#customSwitDetalles').prop("disabled", true);
$('#switPausas').prop("disabled", true);
inicializarTabla();
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
        }
    }).then(function (data) {
        $('div.dataTables_processing').hide();
        $("#tablaReport").css('opacity', 1);
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
            var cantidadColumnasHoras = 1;
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
                                <th>#&nbsp;</th>
                                <th>Fecha&nbsp;</th>
                                <th>Horario&nbsp;</th>
                               `;
            // * MARCACIONES
            for (let j = 0; j < cantidadColumnasHoras; j++) {
                theadTabla += `<th class="colMarcaciones" style="border-left-color: #c8d4de!important;border-left: 2px solid;">
                                    <span>
                                        Entrada <b style="font-size: 12px !important;color: #383e56;font-weight: 600">${j + 1}</b>
                                    </span>
                                </th>
                                <th class="colMarcaciones">
                                    <span>
                                     Salida <b style="font-size: 12px !important;color: #383e56;font-weight: 600">${j + 1}</b>
                                    </span>
                                </th>
                                <th id="tSitio" name="tiempoSitHi" class="tiempoSitHi">
                                    <span>
                                        Tiempo total <b style="font-size: 12px !important;color: #383e56;font-weight: 600">${j + 1}</b>
                                    </span>
                                </th>`;
            }
            // * PAUSAS
            for (let p = 0; p < cantidadColumnasPausas; p++) {
                theadTabla += `<th style="border-left-color: #c8d4de!important;border-left: 2px solid;" name="datosPausa" class="datosPausa">
                                    <span>Pausa<b style="font-size: 12px !important;color: #383e56;font-weight: 600">${p + 1}</b></span>
                                </th>
                                <th name="datosPausa" class="datosPausa">
                                    <span>Horario pausa<b style="font-size: 12px !important;color: #383e56;font-weight: 600">${p + 1}</b></span>
                                </th>
                                <th name="datosPausa" class="datosPausa">
                                    <span>Tiempo pausa<b style="font-size: 12px !important;color: #383e56;font-weight: 600">${p + 1}</b></span>
                                </th>
                                <th name="datosPausa" class="datosPausa">
                                    <span>Exceso pausa<b style="font-size: 12px !important;color: #383e56;font-weight: 600">${p + 1}</b></span>
                                </th>`;
            }
            theadTabla += `<th class="text-center colTiempoTotal" style="border-left-color: #c8d4de!important;border-left: 2px solid;">Tiempo total</th>
                            <th class="text-center colSobretiempo">Sobretiempo</th>
                            <th class="text-center colHorarioNormal">Horario normal</th>
                            <th class="text-center colSobretiempoNormal">Sobretiempo normal</th>
                            <th class="text-center colDiurnas25">H.E. 25% Diurnas</th>
                            <th class="text-center colDiurnas35">H.E. 35% Diurnas</th>
                            <th class="text-center colDiurnas100">H.E. 100% Diurnas</th>
                            <th class="text-center colHorarioNocturno">Horario nocturno</th>
                            <th class="text-center">Sobretiempo nocturno</th>
                            <th class="text-center">H.E. 25% Nocturnas</th>
                            <th class="text-center">H.E. 35% Nocturnas</th>
                            <th class="text-center">H.E. 100% Nocturnas</th>
                            <th class="text-center">Tardanza total</th>
                            <th class="text-center">Faltas total</th>
                            <th class="text-center">Incidencias total</th>
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
                var tiempoTotal = moment.duration(0);
                var sumaTardanzas = moment.duration(0);
                var sumaHorariosNormales = moment.duration(0);
                var sumaHorariosNocturnas = moment.duration(0);
                var sumaSobretiempo = moment.duration(0);
                var sumaSobretiempoNormal = moment.duration(0);
                var sumaSobretiempoNocturno = moment.duration(0);
                var tiempoDiurnas25 = moment.duration(0);
                var tiempoDiurnas35 = moment.duration(0);
                var tiempoDiurnas100 = moment.duration(0);
                var tiempoNocturnas25 = moment.duration(0);
                var tiempoNocturnas35 = moment.duration(0);
                var tiempoNocturnas100 = moment.duration(0);
                // * VARIABLE QUE GUARDARA QUE TIPO DE MARCACIÓN FUE PRIMERO
                var primeraM = undefined;
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
                // ! ********************************************** TARDANZA ******************************************
                if (contenidoData.idHorario != 0) {
                    if (contenidoData.marcaciones[0] != undefined) {
                        if (contenidoData.marcaciones[0].entrada != 0) {
                            var horaInicial = moment(contenidoData.marcaciones[0].entrada);
                            // ******************************* TARDANZA ***************************************
                            // ! PARA QUE TOME SOLO TARDANZA EN LA PRIMERA MARCACION
                            if (!idHorarioM.includes(contenidoData.idHorario)) {
                                idHorarioM.push(contenidoData.idHorario);  // : AGREGAMOS EL ID AL ARRAY
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
                    }
                }
                // ! ********************************************** TIEMPO TOTAL, TIEMPO NORMAL Y TIEMPO NCOTURNO *****************
                for (let item = 0; item < contenidoData.marcaciones.length; item++) {
                    var element = contenidoData.marcaciones[item];
                    if (element.entrada != 0 && element.salida) {
                        var entradaMoment = moment(element.entrada);
                        var salidaMoment = moment(element.salida);
                        if (salidaMoment.isSameOrAfter(entradaMoment)) {
                            // ! ************************************* TIEMPO TOTAL *****************************************
                            var tiempoEntreMarcaciones = salidaMoment - entradaMoment;
                            tiempoTotal = tiempoTotal.add(tiempoEntreMarcaciones);
                            // ! ************************************ HORARIO NORMAL  Y HORARIO NOCTURNO ********************
                            // : GUARDAR EL TIEMPO ENTE MARCACIONES
                            var tiempoEntreM = moment.duration(salidaMoment.diff(entradaMoment));
                            // : ACUMULAR TIEMPO CALCULADOS
                            var acumuladorEntreM = moment.duration(0);
                            // : TIEMPOS MAXIMOS DE DIURNOS Y NOCTURNOS 
                            var tiempoMaximoDiurno = moment(entradaMoment.clone().format("YYYY-MM-DD") + " " + "22:00:00");
                            var tiempoMaximoNocturno = moment(entradaMoment.clone().format("YYYY-MM-DD") + " " + "06:00:00");
                            if (entradaMoment.isAfter(tiempoMaximoNocturno) && entradaMoment.isSameOrBefore(tiempoMaximoDiurno)) {
                                if (primeraM == undefined) primeraM = 0;
                                if (salidaMoment.clone().isSameOrBefore(tiempoMaximoDiurno)) {
                                    var tiempoNormal = salidaMoment - entradaMoment;
                                    var segundosNormal = moment.duration(tiempoNormal).seconds();
                                    var minutosNormal = moment.duration(tiempoNormal).minutes();
                                    var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                    sumaHorariosNormales = sumaHorariosNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                } else {
                                    var minuendoResta = tiempoMaximoDiurno.clone();
                                    var sustraendoResta = entradaMoment.clone();
                                    var contadorDias = 1;
                                    while (acumuladorEntreM < tiempoEntreM) {
                                        // : ************************************************* HORAS NORMALES *************************************************
                                        var tiempoNormal = minuendoResta - sustraendoResta;
                                        var segundosNormal = moment.duration(tiempoNormal).seconds();
                                        var minutosNormal = moment.duration(tiempoNormal).minutes();
                                        var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                        sumaHorariosNormales = sumaHorariosNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                        acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                        // : ************************************************* FINALIZACION *****************************************************
                                        var tiempoMaximoDiurnoAnterior = tiempoMaximoDiurno;
                                        tiempoMaximoDiurno = tiempoMaximoDiurno.clone().add("day", contadorDias);
                                        tiempoMaximoNocturno = tiempoMaximoNocturno.clone().add("day", contadorDias);
                                        if (acumuladorEntreM < tiempoEntreM) {
                                            if (salidaMoment.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                // : HORA NOCTURNA
                                                sustraendoResta = minuendoResta;
                                                minuendoResta = salidaMoment;
                                                var tiempoNocturno = minuendoResta - sustraendoResta;
                                                var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                sumaHorariosNocturnas = sumaHorariosNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                            } else {
                                                minuendoResta = tiempoMaximoNocturno;
                                                sustraendoResta = tiempoMaximoDiurnoAnterior;
                                                // : HORA NOCTURNA
                                                var tiempoNocturno = minuendoResta - sustraendoResta;
                                                var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                sumaHorariosNocturnas = sumaHorariosNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                if (salidaMoment.clone().isSameOrBefore(tiempoMaximoDiurno)) {
                                                    minuendoResta = salidaMoment.clone();
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
                                if (salidaMoment.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                    // : HORAS NOCTURNAS
                                    var tiempoNocturno = salidaMoment - entradaMoment;
                                    var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                    var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                    var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                    sumaHorariosNocturnas = sumaHorariosNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                } else {
                                    if (moment.duration(entradaMoment.clone().format("HH:mm:ss")) < moment.duration("06:00:00")) {
                                        tiempoMaximoDiurno = moment(entradaMoment.clone().format("YYYY-MM-DD") + " " + "22:00:00");
                                        tiempoMaximoNocturno = moment(entradaMoment.clone().format("YYYY-MM-DD") + " " + "06:00:00");
                                    } else {
                                        tiempoMaximoDiurno = moment(entradaMoment.clone().add("day", 1).format("YYYY-MM-DD") + " " + "22:00:00");
                                        tiempoMaximoNocturno = moment(entradaMoment.clone().add("day", 1).format("YYYY-MM-DD") + " " + "06:00:00");
                                    }
                                    if (salidaMoment.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                        // : HORAS NOCTURNAS
                                        var tiempoNocturno = salidaMoment - entradaMoment;
                                        var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                        var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                        var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                        sumaHorariosNocturnas = sumaHorariosNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                    } else {
                                        var minuendoResta = tiempoMaximoNocturno.clone();
                                        var sustraendoResta = entradaMoment.clone();
                                        var contadorDias = 1;
                                        while (acumuladorEntreM < tiempoEntreM) {
                                            // : HORAS NOCTURNAS
                                            var tiempoNocturno = minuendoResta - sustraendoResta;
                                            var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                            var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                            var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                            sumaHorariosNocturnas = sumaHorariosNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                            acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                            // : CALCULOS DE TIEMPO
                                            var tiempoMaximoDiurnoAnterior = tiempoMaximoDiurno;
                                            tiempoMaximoDiurno = moment(tiempoMaximoDiurno.clone().add("day", contadorDias));
                                            tiempoMaximoNocturno = moment(tiempoMaximoNocturno.clone().add("day", contadorDias));
                                            if (acumuladorEntreM < tiempoEntreM) {
                                                if (salidaMoment.clone().isSameOrBefore(tiempoMaximoDiurnoAnterior)) {
                                                    sustraendoResta = minuendoResta;
                                                    minuendoResta = salidaMoment.clone();
                                                    // : HORAS NORMALES
                                                    var tiempoNormal = minuendoResta - sustraendoResta;
                                                    var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                    var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                    var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                    sumaHorariosNormales = sumaHorariosNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                    acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                } else {
                                                    sustraendoResta = minuendoResta;
                                                    minuendoResta = tiempoMaximoDiurnoAnterior;
                                                    // : HORAS NORMALES
                                                    var tiempoNormal = minuendoResta - sustraendoResta;
                                                    var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                    var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                    var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                    sumaHorariosNormales = sumaHorariosNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                    acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                    if (salidaMoment.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                        minuendoResta = salidaMoment.clone();
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
                        }
                    }
                }
                // ! ************************************************* SOBRE TIEMPO EN MARCACIONES **********************************
                if (contenidoData.idHorario != 0) {
                    var horasObligadas = moment.duration(contenidoData.horasO);
                    // : SOBRETIEMPO
                    if (tiempoTotal > horasObligadas) {
                        var sobretiempoHorario = tiempoTotal - horasObligadas;
                        sumaSobretiempo = sumaSobretiempo.add(sobretiempoHorario);
                    }
                    // : SOBRE TIEMPO POR HORARIO, VERIFICAMOS QUE FUE PRIMERO
                    if (primeraM != undefined) {
                        var horasObligadasHorario = moment.duration(contenidoData.horasO);
                        var nuevaHorasObligadas = moment.duration(0);
                        if (primeraM == 0) {
                            if (sumaHorariosNormales > horasObligadasHorario) {
                                // : HORARIO NORMAL
                                nuevaHorasObligadas = moment.duration(0);
                                var tiempoExtraResta = sumaHorariosNormales - horasObligadasHorario;
                                sumaSobretiempoNormal = sumaSobretiempoNormal.add(tiempoExtraResta);
                                var tiempoSobrante = moment.duration(0);
                                if (tiempoExtraResta > moment.duration("02:00:00")) {
                                    tiempoDiurnas25 = moment.duration("02:00:00");
                                    var restaDe25 = tiempoExtraResta - moment.duration("02:00:00");
                                    tiempoSobrante = moment.duration(restaDe25);
                                    if (tiempoSobrante > moment.duration("02:00:00")) {
                                        tiempoDiurnas35 = moment.duration("02:00:00");
                                        var restaDe35 = tiempoSobrante - moment.duration("02:00:00");
                                        tiempoSobrante = moment.duration(restaDe35);
                                        if (tiempoSobrante > moment.duration(0)) {
                                            tiempoDiurnas100 = moment.duration(restaDe35);
                                        }
                                    } else {
                                        tiempoDiurnas35 = moment.duration(restaDe25);
                                    }
                                } else {
                                    tiempoDiurnas25 = moment.duration(tiempoExtraResta);
                                }
                                // : HORARIO NOCTURNO
                                if (sumaHorariosNocturnas > nuevaHorasObligadas) {
                                    var tiempoExtraRestaN = sumaHorariosNocturnas - nuevaHorasObligadas;
                                    sumaSobretiempoNocturno = sumaSobretiempoNocturno.add(tiempoExtraRestaN);
                                    if (tiempoExtraRestaN > moment.duration("02:00:00")) {
                                        tiempoNocturnas25 = moment.duration("02:00:00");
                                        var restaDe25N = tiempoExtraRestaN - moment.duration("02:00:00");
                                        tiempoSobranteN = moment.duration(restaDe25N);
                                        if (tiempoSobranteN > moment.duration("02:00:00")) {
                                            tiempoNocturnas35 = moment.duration("02:00:00");
                                            var restaDe35N = tiempoSobranteN - moment.duration("02:00:00");
                                            tiempoSobranteN = moment.duration(restaDe35N);
                                            if (tiempoSobranteN > moment.duration(0)) {
                                                tiempoNocturnas100 = moment.duration(restaDe35N);
                                            }
                                        } else {
                                            tiempoNocturnas35 = moment.duration(restaDe25N);
                                        }
                                    } else {
                                        tiempoNocturnas25 = moment.duration(tiempoExtraRestaN);
                                    }
                                }
                            } else {
                                var restaHorasO = horasObligadasHorario - sumaHorariosNormales;
                                nuevaHorasObligadas = moment.duration(restaHorasO);
                                // : HORARIO NOCTURNO
                                if (sumaHorariosNocturnas > nuevaHorasObligadas) {
                                    var tiempoExtraRestaN = sumaHorariosNocturnas - nuevaHorasObligadas;
                                    sumaSobretiempoNocturno = sumaSobretiempoNocturno.add(tiempoExtraRestaN);
                                    var tiempoSobranteN = {};
                                    if (tiempoExtraRestaN > moment.duration("02:00:00")) {
                                        tiempoNocturnas25 = moment.duration("02:00:00");
                                        var restaDe25N = tiempoExtraRestaN - moment.duration("02:00:00");
                                        tiempoSobranteN = moment.duration(restaDe25N);
                                        if (tiempoSobranteN > moment.duration("02:00:00")) {
                                            tiempoNocturnas35 = moment.duration("02:00:00");
                                            var restaDe35N = tiempoSobranteN - moment.duration("02:00:00");
                                            tiempoSobranteN = moment.duration(restaDe35N);
                                            if (tiempoSobranteN > moment.duration(0)) {
                                                tiempoNocturnas100 = moment.duration(restaDe35N);
                                            }
                                        } else {
                                            tiempoNocturnas35 = moment.duration(restaDe25N);
                                        }
                                    } else {
                                        tiempoNocturnas25 = moment.duration(tiempoExtraRestaN);
                                    }
                                }
                            }
                        } else {
                            if (sumaHorariosNocturnas > horasObligadasHorario) {
                                // : HORARIO NOCTURNO
                                nuevaHorasObligadas = moment.duration(0);
                                var tiempoExtraRestaN = sumaHorariosNocturnas - horasObligadasHorario;
                                sumaSobretiempoNocturno = sumaSobretiempoNocturno.add(tiempoExtraRestaN);
                                var tiempoSobranteN = moment.duration(0);
                                if (tiempoExtraRestaN > moment.duration("02:00:00")) {
                                    tiempoNocturnas25 = moment.duration("02:00:00");
                                    var restaDe25N = tiempoExtraRestaN - moment.duration("02:00:00");
                                    tiempoSobranteN = moment.duration(restaDe25N);
                                    if (tiempoSobranteN > moment.duration("02:00:00")) {
                                        tiempoNocturnas25 = moment.duration("02:00:00");
                                        var restaDe35N = tiempoSobranteN - moment.duration("02:00:00");
                                        tiempoSobranteN = moment.duration(restaDe35N);
                                        if (tiempoSobranteN > moment.duration(0)) {
                                            tiempoNocturnas100 = moment.duration(restaDe35N);
                                        }
                                    } else {
                                        tiempoNocturnas35 = moment.duration(restaDe25N);
                                    }
                                } else {
                                    tiempoNocturnas25 = moment.duration(tiempoExtraRestaN);
                                }
                                // : HORARIO NORMAL 
                                if (sumaHorariosNormales > nuevaHorasObligadas) {
                                    var tiempoExtraResta = sumaHorariosNormales - nuevaHorasObligadas;
                                    sumaSobretiempoNormal = sumaSobretiempoNormal.add(tiempoExtraResta);
                                    var tiempoSobrante = moment.duration(0);
                                    if (tiempoExtraResta > moment.duration("02:00:00")) {
                                        tiempoDiurnas25 = moment.duration("02:00:00");
                                        var restaDe25 = tiempoExtraResta - moment.duration("02:00:00");
                                        tiempoSobrante = moment.duration(restaDe25);
                                        if (tiempoSobrante > moment.duration("02:00:00")) {
                                            tiempoDiurnas35 = moment.duration("02:00:00");
                                            var restaDe35 = tiempoSobrante - moment.duration("02:00:00");
                                            tiempoSobrante = moment.duration(restaDe35);
                                            if (tiempoSobrante > moment.duration(0)) {
                                                tiempoDiurnas100 = moment.duration(restaDe35);
                                            }
                                        } else {
                                            tiempoDiurnas35 = moment.duration(restaDe25);
                                        }
                                    } else {
                                        tiempoDiurnas25 = moment.duration(tiempoExtraResta);
                                    }
                                }
                            } else {
                                var restaHorasO = horasObligadasHorario - sumaHorariosNocturnas;
                                nuevaHorasObligadas = moment.duration(restaHorasO);
                                // : HOARIO NORMAL
                                if (sumaHorariosNormales > nuevaHorasObligadas) {
                                    var tiempoExtraResta = sumaHorariosNormales - nuevaHorasObligadas;
                                    sumaSobretiempoNormal = sumaSobretiempoNormal.add(tiempoExtraResta);
                                    var tiempoSobrante = moment.duration(0);
                                    if (tiempoExtraResta > moment.duration("02:00:00")) {
                                        tiempoDiurnas25 = moment.duration("02:00:00");
                                        var restaDe25 = tiempoExtraResta - moment.duration("02:00:00");
                                        tiempoSobrante = moment.duration(restaDe25);
                                        if (tiempoSobrante > moment.duration("02:00:00")) {
                                            tiempoDiurnas35 = moment.duration("02:00:00");
                                            var restaDe35 = tiempoSobrante - moment.duration("02:00:00");
                                            tiempoSobrante = moment.duration(restaDe35);
                                            if (tiempoSobrante > moment.duration(0)) {
                                                tiempoDiurnas100 = moment.duration(restaDe35);
                                            }
                                        } else {
                                            tiempoDiurnas35 = moment.duration(restaDe25);
                                        }
                                    } else {
                                        tiempoDiurnas25 = moment.duration(tiempoExtraResta);
                                    }
                                }
                            }
                        }
                    }
                }
                // ! ********************************************** FINALIZACION ************************************** 
                for (let i = 0; i < contenidoData.marcaciones.length; i++) {
                    // * TIEMPO EN SITIO
                    var segundosTiempo = "00";
                    var minutosTiempo = "00";
                    var horasTiempo = "00";
                    var contenidoMarcacion = contenidoData.marcaciones[i];
                    // * SI TIENE TIEMPO DE ENTRADA
                    if (contenidoMarcacion.entrada != 0) {
                        tbodyEntradaySalida += `<td class="colMarcaciones" style="border-left-color: #c8d4de!important;border-left: 2px solid;">
                                                    <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>
                                                    ${moment(contenidoMarcacion.entrada).format("HH:mm:ss")}
                                                </td>`;
                        // * SI TIENE TIEMPO DE SALIDA
                        if (contenidoMarcacion.salida != 0) {
                            tbodyEntradaySalida += `<td class="colMarcaciones">
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
                            }
                        } else {
                            tbodyEntradaySalida += `<td class="colMarcaciones">
                                                        <span class="badge badge-soft-secondary">
                                                            <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                            No tiene salida
                                                        </span>
                                                    </td>`;
                        }
                    } else {
                        if (contenidoMarcacion.salida != 0) {
                            tbodyEntradaySalida += `<td class="colMarcaciones" style="border-left-color: #c8d4de!important;border-left: 2px solid;">
                                                        <span class="badge badge-soft-warning">
                                                            <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                            No tiene entrada
                                                        </span>
                                                    </td>`;
                            tbodyEntradaySalida += `<td class="colMarcaciones">
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
                                            </td>`;
                }
                for (let m = contenidoData.marcaciones.length; m < cantidadColumnasHoras; m++) {
                    tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;border-left: 2px solid;">
                                                ---
                                            </td>
                                            <td>---</td>
                                            <td name="tiempoSitHi">---</td>`;
                }
                tbody += tbodyEntradaySalida;
                // * ARMAR PAUSAS
                var tbodyPausas = "";
                for (let p = 0; p < contenidoData.pausas.length; p++) {
                    // * PAUSAS
                    tiempoHoraPausa = "00";        //: HORAS TARDANZA
                    tiempoMinutoPausa = "00";      //: MINUTOS TARDANZA
                    tiempoSegundoPausa = "00";     //: SEGUNDOS TARDANZA
                    //* EXCESO
                    tiempoHoraExceso = "00";
                    tiempoMinutoExceso = "00";
                    tiempoSegundoExceso = "00";
                    var pausaData = contenidoData.pausas[p];
                    if (contenidoData.idHorario == pausaData.idH && contenidoData.idHorario != 0) {
                        for (let mp = 0; mp < contenidoData.marcaciones.length; mp++) {
                            var contenidoMarcacionP = contenidoData.marcaciones[mp];
                            // * CALCULAR TIEMPO TOTAL , TIEMPO DE PAUSA Y EXCESO DE PAUSAS
                            var horaFinalM = moment(contenidoMarcacionP.salida);
                            var horaInicialM = moment(contenidoMarcacionP.entrada);
                            if (horaFinalM.isSameOrAfter(horaInicialM)) {
                                // * TIEMPO TOTAL TRABAJADA
                                if (contenidoMarcacionP.idHorario == pausaData.idH) {
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
                                            { "minutes": pausaData.toleranciaI }    // : CLONAMOS EL TIEMPO Y SUMAR CON TOLERANCIA
                                        ).toString());
                                    var restaToleranciaPausa = moment(
                                        pausaI.clone().subtract(
                                            { "minutes": pausaData.toleranciaI }
                                        ).toString()); //: CLONAMOS EL TIEMPO Y RESTAR CON TOLERANCIA
                                    // ! FIN DE PAUSA
                                    var sumaToleranciaPausaFinal = moment(pausaF.clone().add({ "minutes": pausaData.tolerancia_fin }).toString());
                                    if (!idPausas.includes(pausaData.id)) {
                                        // ! CONDICIONALES QUE SI HORA FINAL DE LA MARCACION ESTA ENTRE LA RESTA CON LA TOLERANCIA Y LA SUMA CON LA TOLERANCIA
                                        if (horaFinalM.isSameOrAfter(restaToleranciaPausa) && horaFinalM.isSameOrBefore(sumaToleranciaPausa)) {
                                            // * VERIFICAR SI YA TENEMOS OTRA MARCACION SIGUIENTE
                                            if (contenidoData.marcaciones[mp + 1] != undefined) {
                                                if (contenidoData.marcaciones[mp + 1].entrada != undefined) {
                                                    if (contenidoData.marcaciones[mp + 1].entrada != 0) {
                                                        var horaEntradaDespues = moment(contenidoData.marcaciones[mp + 1].entrada);    //: -> OBTENER ENTRADA DE LA MARCACION SIGUIENTE
                                                        var restarTiempoMarcacion = horaEntradaDespues - horaFinalM;                 //: -> RESTAR PARA OBTENER LA CANTIDAD EN PAUSA
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
                                                        idPausas.push(pausaData.id);
                                                    }
                                                }
                                            }
                                        } else {
                                            if (horaFinalM.isSameOrAfter(restaToleranciaPausa) && horaFinalM.isSameOrBefore(sumaToleranciaPausaFinal)) {
                                                // * VERIFICAR SI YA TENEMOS OTRA MARCACION SIGUIENTE
                                                if (contenidoData.marcaciones[mp + 1] != undefined) {
                                                    if (contenidoData.marcaciones[mp + 1].entrada != undefined) {
                                                        if (contenidoData.marcaciones[mp + 1].entrada != 0) {
                                                            estadoTiempoHorario = false;
                                                            var horaEntradaDespues = moment(contenidoData.marcaciones[mp + 1].entrada);    //: -> OBTENER ENTRADA DE LA MARCACION SIGUIENTE
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
                                                            idPausas.push(pausaData.id);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                }
                            }
                        }
                    }
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
                // : TIEMPO TOTAL
                var horaTiempoTotal = Math.trunc(moment.duration(tiempoTotal).asHours());
                var minutoTiempoTotal = moment.duration(tiempoTotal).minutes();
                var segundoTiempoTotal = moment.duration(tiempoTotal).seconds();
                if (horaTiempoTotal < 10) {
                    horaTiempoTotal = "0" + horaTiempoTotal;
                }
                if (minutoTiempoTotal < 10) {
                    minutoTiempoTotal = "0" + minutoTiempoTotal;
                }
                if (segundoTiempoTotal < 10) {
                    segundoTiempoTotal = "0" + segundoTiempoTotal;
                }
                // : SUMA TARDANZA
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
                // : HORARIO NORMAL
                var horaSumaHorariosNormales = Math.trunc(moment.duration(sumaHorariosNormales).asHours());
                var minutoSumaHorarioNormales = moment.duration(sumaHorariosNormales).minutes();
                var segundoSumaHorarioNormales = moment.duration(sumaHorariosNormales).seconds();
                if (horaSumaHorariosNormales < 10) {
                    horaSumaHorariosNormales = "0" + horaSumaHorariosNormales
                }
                if (minutoSumaHorarioNormales < 10) {
                    minutoSumaHorarioNormales = "0" + minutoSumaHorarioNormales;
                }
                if (segundoSumaHorarioNormales < 10) {
                    segundoSumaHorarioNormales = "0" + segundoSumaHorarioNormales;
                }
                // : HORARIO NOCTURNO
                var horaSumaHorariosNocturnos = Math.trunc(moment.duration(sumaHorariosNocturnas).asHours());
                var minutoSumaHorariosNocturnos = moment.duration(sumaHorariosNocturnas).minutes();
                var segundoSumaHorariosNocturnos = moment.duration(sumaHorariosNocturnas).seconds();
                if (horaSumaHorariosNocturnos < 10) {
                    horaSumaHorariosNocturnos = "0" + horaSumaHorariosNocturnos;
                }
                if (minutoSumaHorariosNocturnos < 10) {
                    minutoSumaHorariosNocturnos = "0" + minutoSumaHorariosNocturnos;
                }
                if (segundoSumaHorariosNocturnos < 10) {
                    segundoSumaHorariosNocturnos = "0" + segundoSumaHorariosNocturnos;
                }
                // : SOBRETIEMPO
                var horaSumaSobretiempo = Math.trunc(moment.duration(sumaSobretiempo).asHours());
                var minutoSumaSobretiempo = moment.duration(sumaSobretiempo).minutes();
                var segundoSumaSobretiempo = moment.duration(sumaSobretiempo).seconds();
                if (horaSumaSobretiempo < 10) {
                    horaSumaSobretiempo = "0" + horaSumaSobretiempo;
                }
                if (minutoSumaSobretiempo < 10) {
                    minutoSumaSobretiempo = "0" + minutoSumaSobretiempo;
                }
                if (segundoSumaSobretiempo < 10) {
                    segundoSumaSobretiempo = "0" + segundoSumaSobretiempo;
                }
                // : SOBRETIEMPO NORMAL
                var horaSobretiempoNormales = Math.trunc(moment.duration(sumaSobretiempoNormal).asHours());
                var minutoSobretiempoNormales = moment.duration(sumaSobretiempoNormal).minutes();
                var segundoSobretiempoNormales = moment.duration(sumaSobretiempoNormal).seconds();
                if (horaSobretiempoNormales < 10) {
                    horaSobretiempoNormales = "0" + horaSobretiempoNormales;
                }
                if (minutoSobretiempoNormales < 10) {
                    minutoSobretiempoNormales = "0" + minutoSobretiempoNormales;
                }
                if (segundoSobretiempoNormales < 10) {
                    segundoSobretiempoNormales = "0" + segundoSobretiempoNormales;
                }
                // : SOBRETIEMPO NOCTURNO
                var horaSobretiempoNocturnos = Math.trunc(moment.duration(sumaSobretiempoNocturno).asHours());
                var minutoSobretiempoNocturnos = moment.duration(sumaSobretiempoNocturno).minutes();
                var segundoSobretiempoNocturnos = moment.duration(sumaSobretiempoNocturno).seconds();
                if (horaSobretiempoNocturnos < 10) {
                    horaSobretiempoNocturnos = "0" + horaSobretiempoNocturnos;
                }
                if (minutoSobretiempoNocturnos < 10) {
                    minutoSobretiempoNocturnos = "0" + minutoSobretiempoNocturnos;
                }
                if (segundoSobretiempoNocturnos < 10) {
                    segundoSobretiempoNocturnos = "0" + segundoSobretiempoNocturnos;
                }
                // : HORAS EXTRAS DIURNAS 25%
                var horaTiempoDiurnas25 = Math.trunc(moment.duration(tiempoDiurnas25).asHours());
                var minutoTiempoDiurnas25 = moment.duration(tiempoDiurnas25).minutes();
                var segundoTiempoDiurnas25 = moment.duration(tiempoDiurnas25).seconds();
                if (horaTiempoDiurnas25 < 10) {
                    horaTiempoDiurnas25 = "0" + horaTiempoDiurnas25;
                }
                if (minutoTiempoDiurnas25 < 10) {
                    minutoTiempoDiurnas25 = "0" + minutoTiempoDiurnas25;
                }
                if (segundoTiempoDiurnas25 < 10) {
                    segundoTiempoDiurnas25 = "0" + segundoTiempoDiurnas25;
                }
                // : HORAS EXTRAS DIURNAS 35%
                var horaTiempoDiurnas35 = Math.trunc(moment.duration(tiempoDiurnas35).asHours());
                var minutoTiempoDiurnas35 = moment.duration(tiempoDiurnas35).minutes();
                var segundoTiempoDiurnas35 = moment.duration(tiempoDiurnas35).seconds();
                if (horaTiempoDiurnas35 < 10) {
                    horaTiempoDiurnas35 = "0" + horaTiempoDiurnas35;
                }
                if (minutoTiempoDiurnas35 < 10) {
                    minutoTiempoDiurnas35 = "0" + minutoTiempoDiurnas35;
                }
                if (segundoTiempoDiurnas35 < 10) {
                    segundoTiempoDiurnas35 = "0" + segundoTiempoDiurnas35;
                }
                // : HORAS EXTRAS DIURNAS 100%
                var horaTiempoDiurnas100 = Math.trunc(moment.duration(tiempoDiurnas100).asHours());
                var minutoTiempoDiurnas100 = moment.duration(tiempoDiurnas100).minutes();
                var segundoTiempoDiurnas100 = moment.duration(tiempoDiurnas100).seconds();
                if (horaTiempoDiurnas100 < 10) {
                    horaTiempoDiurnas100 = "0" + horaTiempoDiurnas100;
                }
                if (minutoTiempoDiurnas100 < 10) {
                    minutoTiempoDiurnas100 = "0" + minutoTiempoDiurnas100;
                }
                if (segundoTiempoDiurnas100 < 10) {
                    segundoTiempoDiurnas100 = "0" + segundoTiempoDiurnas100;
                }
                // : HORAS EXTRAS NOCTURNAS 25%
                var horaTiempoNocturnas25 = Math.trunc(moment.duration(tiempoNocturnas25).asHours());
                var minutoTiempoNocturnas25 = moment.duration(tiempoNocturnas25).minutes();
                var segundoTiempoNocturnas25 = moment.duration(tiempoNocturnas25).seconds();
                if (horaTiempoNocturnas25 < 10) {
                    horaTiempoNocturnas25 = "0" + horaTiempoNocturnas25;
                }
                if (minutoTiempoNocturnas25 < 10) {
                    minutoTiempoNocturnas25 = "0" + minutoTiempoNocturnas25;
                }
                if (segundoTiempoNocturnas25 < 10) {
                    segundoTiempoNocturnas25 = "0" + segundoTiempoNocturnas25;
                }
                // : HORAS EXTRAS NOCTURNAS 35%
                var horaTiempoNocturnas35 = Math.trunc(moment.duration(tiempoNocturnas35).asHours());
                var minutoTiempoNocturnas35 = moment.duration(tiempoNocturnas35).minutes();
                var segundoTiempoNocturnas35 = moment.duration(tiempoNocturnas35).seconds();
                if (horaTiempoNocturnas35 < 10) {
                    horaTiempoNocturnas35 = "0" + horaTiempoNocturnas35;
                }
                if (minutoTiempoNocturnas35 < 10) {
                    minutoTiempoNocturnas35 = "0" + minutoTiempoNocturnas35;
                }
                if (segundoTiempoNocturnas35 < 10) {
                    segundoTiempoNocturnas35 = "0" + segundoTiempoNocturnas35;
                }
                // : HORAS EXTRAS NOCTURNAS 100%
                var horaTiempoNocturnas100 = Math.trunc(moment.duration(tiempoNocturnas100).asHours());
                var minutoTiempoNocturnas100 = moment.duration(tiempoNocturnas100).minutes();
                var segundoTiempoNocturnas100 = moment.duration(tiempoNocturnas100).seconds();
                if (horaTiempoNocturnas100 < 10) {
                    horaTiempoNocturnas100 = "0" + horaTiempoNocturnas100;
                }
                if (minutoTiempoNocturnas100 < 10) {
                    minutoTiempoNocturnas100 = "0" + minutoTiempoNocturnas100;
                }
                if (segundoTiempoNocturnas100 < 10) {
                    segundoTiempoNocturnas100 = "0" + segundoTiempoNocturnas100;
                }
                tbody += `<td class="text-center colTiempoTotal" style="border-left-color: #c8d4de!important;border-left: 2px solid;">
                            <a class="badge badge-soft-primary mr-2">
                                <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                ${horaTiempoTotal}:${minutoTiempoTotal}:${segundoTiempoTotal}
                            </a>
                        </td>
                        <td class="text-center">
                            <a class="badge badge-soft-primary mr-2">
                                <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                ${horaSumaSobretiempo}:${minutoSumaSobretiempo}:${segundoSumaSobretiempo}
                            </a>
                        </td>
                        <td class="text-center">
                            <a class="badge badge-soft-warning mr-2">
                                <img src="landing/images/sun.svg" height="12" class="mr-2">
                                ${horaSumaHorariosNormales}:${minutoSumaHorarioNormales}:${segundoSumaHorarioNormales}
                            </a>
                        </td>
                        <td class="text-center">
                            <a class="badge badge-soft-warning mr-2">
                                <img src="landing/images/sun.svg" height="12" class="mr-2">
                                ${horaSobretiempoNormales}:${minutoSobretiempoNormales}:${segundoSobretiempoNormales}
                            </a>
                        </td>
                        <td class="text-center">
                            <img src="landing/images/timerD.svg" height="20" class="mr-2">
                            ${horaTiempoDiurnas25}:${minutoTiempoDiurnas25}:${segundoTiempoDiurnas25}
                        </td>
                        <td class="text-center">
                            <img src="landing/images/timerD.svg" height="20" class="mr-2">
                            ${horaTiempoDiurnas35}:${minutoTiempoDiurnas35}:${segundoTiempoDiurnas35}
                        </td>
                        <td class="text-center">
                            <img src="landing/images/timerD.svg" height="20" class="mr-2">
                            ${horaTiempoDiurnas100}:${minutoTiempoDiurnas100}:${segundoTiempoDiurnas100}
                        </td>
                        <td class="text-center">
                            <a class="badge badge-soft-info mr-2">
                                <img src="landing/images/moon.svg" height="12" class="mr-2">
                                ${horaSumaHorariosNocturnos}:${minutoSumaHorariosNocturnos}:${segundoSumaHorariosNocturnos}
                            </a>
                        </td>
                        <td class="text-center">
                            <a class="badge badge-soft-info mr-2">
                                <img src="landing/images/moon.svg" height="12" class="mr-2">
                                ${horaSobretiempoNocturnos}:${minutoSobretiempoNocturnos}:${segundoSobretiempoNocturnos}
                            </a>
                        </td>
                        <td class="text-center">
                            <img src="landing/images/timer.svg" height="20" class="mr-2">
                            ${horaTiempoNocturnas25}:${minutoTiempoNocturnas25}:${segundoTiempoNocturnas25}
                        </td>
                        <td class="text-center">
                            <img src="landing/images/timer.svg" height="20" class="mr-2">
                            ${horaTiempoNocturnas35}:${minutoTiempoNocturnas35}:${segundoTiempoNocturnas35}
                        </td>
                        <td class="text-center">
                            <img src="landing/images/timer.svg" height="20" class="mr-2">
                            ${horaTiempoNocturnas100}:${minutoTiempoNocturnas100}:${segundoTiempoNocturnas100}
                        </td>
                        <td class="text-center">
                            <a class="badge badge-soft-danger mr-2">
                                <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                ${horaSumaTardanzas}:${minutoSumaTardanzas}:${segundoSumaTardanzas}
                            </a>
                        </td>`;
                if (contenidoData.marcaciones.length == 0 && contenidoData.incidencias.length == 0) {
                    tbody += `<td class="text-center"><span class="badge badge-soft-danger mr-1" class="text-center">Falta</span></td>`;
                } else {
                    tbody += `<td class="text-center">--</td>`;
                }
                if (contenidoData.incidencias.length == 0) {
                    tbody += `<td class="text-center">--</td>`;
                } else {
                    tbody += `<td class="text-center">`;
                    for (let i = 0; i < contenidoData.incidencias.length; i++) {
                        var dataIncidencia = contenidoData.incidencias[i];
                        if (i == 0) {
                            tbody += `<span class="badge badge-soft-info ml-1 mr-1" class="text-center">${dataIncidencia.descripcion}</span>`;
                        } else {
                            tbody += `<b>/</b><span class="badge badge-soft-info ml-1" class="text-center">${dataIncidencia.descripcion}</span>`;
                        }
                    }
                    tbody += `</td>`;
                }
                tbody += `</tr>`;
            }
            $('#tbodyD').html(tbody);
            inicializarTabla();
            toggleColumnas();
            $(window).on('resize', function () {
                $("#tablaReport").css('width', '100%');
                table.draw(true);
            });
        } else {
            $('#customSwitDetalles').prop("disabled", true);
            $('#switPausas').prop("disabled", true);
            if ($.fn.DataTable.isDataTable("#tablaReport")) {
                $("#tablaReport").DataTable().destroy();
            }
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
        dataT.api().columns('.tiempoSitHi').visible(true);
    }
    else {
        dataT.api().columns('.tiempoSitHi').visible(false);
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
}
// * FUNCION DE SWITCH DE MOSTRAR PAUSAS
function togglePausas() {
    if ($('#switPausas').is(':checked')) {
        dataT.api().columns('.datosPausa').visible(true);
    } else {
        dataT.api().columns('.datosPausa').visible(false);
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
}
// * FUNCION DE TABLAS OCULTAS
function vistasDeTabla() {
    if ($('#customSwitDetalles').is(':checked')) {
        dataT.api().columns('.tiempoSitHi').visible(true);
    }
    else {
        dataT.api().columns('.tiempoSitHi').visible(false);
    }
    if ($('#switPausas').is(':checked')) {
        dataT.api().columns('.datosPausa').visible(true);
    } else {
        dataT.api().columns('.datosPausa').visible(false);
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
}
$('#tablaReport tbody').on('click', 'tr', function () {
    $(this).toggleClass('selected');
});
// *********************************** SELECTOR DE COLUMNAS ****************************
$(document).on('click', '.allow-focus', function (e) {
    e.stopPropagation();
});
function toggleTiempos() {
    $('#contenidoTiempos').toggle();
}
// : ************************** COLUMNAS DE MARCACIONES *************************************************************
$('#colMarcaciones').change(function (event) {
    if (event.target.checked) {
        dataT.api().columns('.colMarcaciones').visible(true);
    } else {
        dataT.api().columns('.colMarcaciones').visible(false);
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
});
// : *************************** COLUMNAS DE CÁLCULOS DE TIEMPO *******************************************************
// * FUNCION DE CHECKBOX HIJOS TIEMPOS
$('.tiemposHijo input[type=checkbox]').change(function () {
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.tiemposPadre').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.tiemposPadre').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.tiemposPadre').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    toggleColumnas();
});
// * FUNCIONN DE CHECKBOX DE PADRE TIEMPOS
$('.tiemposPadre input[type=checkbox]').change(function () {
    $(this).closest('.tiemposPadre').next('ul').find('.tiemposHijo input[type=checkbox]').prop('checked', this.checked);
    toggleColumnas();
});
function toggleColumnas() {
    // * COLUMNA DE MARCACIONES
    if ($('#colMarcaciones').is(":checked")) {
        dataT.api().columns('.colMarcaciones').visible(true);
    } else {
        dataT.api().columns('.colMarcaciones').visible(false);
    }
    // * COLUMNA DE TIEMPO TOTAL
    if ($('#colTiempoTotal').is(":checked")) {
        dataT.api().columns('.colTiempoTotal').visible(true);
    } else {
        dataT.api().columns('.colTiempoTotal').visible(false);
    }
    // * COLUMNA DE SOBRE TIEMPO
    if ($('#colSobretiempo').is(":checked")) {
        dataT.api().columns('.colSobretiempo').visible(true);
    } else {
        dataT.api().columns('.colSobretiempo').visible(false);
    }
    // * COLUMNA DE HORARIO NORMAL
    if ($('#colHorarioNormal').is(":checked")) {
        dataT.api().columns('.colHorarioNormal').visible(true);
    } else {
        dataT.api().columns('.colHorarioNormal').visible(false);
    }
    // * COLUMNA DE SOBRETIEMPO NORMAL
    if ($('#colSobretiempoNormal').is(":checked")) {
        dataT.api().columns('.colSobretiempoNormal').visible(true);
    } else {
        dataT.api().columns('.colSobretiempoNormal').visible(false);
    }
    // * COLUMNA H.E. DIURNAS 25%
    if ($('#colDiurnas25').is(":checked")) {
        dataT.api().columns('.colDiurnas25').visible(true);
    } else {
        dataT.api().columns('.colDiurnas25').visible(false);
    }
    // * COLUMNA H.E. DIURNAS 35%
    if ($('#colDiurnas35').is(":checked")) {
        dataT.api().columns('.colDiurnas35').visible(true);
    } else {
        dataT.api().columns('.colDiurnas35').visible(false);
    }
    // * COLUMNA H.E. DIURNAS 100%
    if ($('#colDiurnas100').is(":checked")) {
        dataT.api().columns('.colDiurnas100').visible(true);
    } else {
        dataT.api().columns('.colDiurnas100').visible(false);
    }
    // * COLUMNA DE HORARIO NOCTURNO
    if ($('#colHorarioNocturno').is(":checked")) {
        dataT.api().columns('.colHorarioNocturno').visible(true);
    } else {
        dataT.api().columns('.colHorarioNocturno').visible(false);
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
}
