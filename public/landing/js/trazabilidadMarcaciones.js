//* FECHA
var fechaValue = $("#fechaTrazabilidad").flatpickr({
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
        $('#fechaInicio').val(dateArr[0]);
        $('#fechaFin').val(dateArr[1]);
    },
    onClose: function (selectedDates, dateStr, instance) {
        if (selectedDates.length == 1) {
            var fm = moment(selectedDates[0]).add("day", -1).format("YYYY-MM-DD");
            instance.setDate([fm, selectedDates[0]], true);
        }
    }
});
// * INICIALIZAR TABLA
var table;
var dataT = {};
var razonSocial = {};
var direccion = {};
var ruc = {};
var paginaGlobal = 10;
var checkedIncidencias = [];
function inicializarTabla() {
    table = $("#tablaTrazabilidad").DataTable({
        "searching": false,
        "scrollX": true,
        "ordering": false,
        "autoWidth": false,
        "lengthChange": true,
        retrieve: true,
        processing: true,
        lengthMenu: [10, 25, 50, 75, 100],
        pageLength: paginaGlobal,
        scrollCollapse: false,
        language: {
            sProcessing: "Generando informe...",
            processing: "<img src='landing/images/logoR.gif' height='180'>\n&nbsp;&nbsp;&nbsp;&nbsp;Generando informe...",
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
                    var fechaI = $('#fechaInicio').val();
                    var fechaF = $('#fechaFin').val();
                    //insert
                    var r1 = Addrow(1, [{ k: 'A', v: 'MODO ASISTENCIA EN PUERTA - ASISTENCIA CONSOLIDADA', s: 2 }]);
                    var r2 = Addrow(2, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                    var r3 = Addrow(3, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                    var r4 = Addrow(4, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                    var r5 = Addrow(5, [{ k: 'A', v: 'Fecha:', s: 2 }, { k: 'C', v: fechaI + "\t a \t" + fechaF, s: 0 }]);
                    sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + sheet.childNodes[0].childNodes[1].innerHTML;
                },
                sheetName: 'ASISTENCIA CONSOLIDADA',
                title: 'MODO ASISTENCIA EN PUERTA - ASISTENCIA CONSOLIDADA',
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
            }, {
                extend: "pdfHtml5",
                className: 'btn btn-sm mt-1',
                text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                orientation: 'landscape',
                pageSize: 'A1',
                title: 'MODO ASISTENCIA EN PUERTA - ASISTENCIA CONSOLIDADA',
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
                    var tr = $('#tablaTrazabilidad tbody tr:first-child');
                    var trWidth = $(tr).width();
                    $('#tablaTrazabilidad').find('tbody tr:first-child td').each(function () {
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
                    var fechaI = $('#fechaInicio').val();
                    var fechaF = $('#fechaFin').val();
                    doc["header"] = function () {
                        return {
                            columns: [
                                {
                                    alignment: 'left',
                                    italics: false,
                                    text: [
                                        { text: '\nMODO ASISTENCIA EN PUERTA - ASISTENCIA CONSOLIDADA', bold: true },
                                        { text: '\n\nRazón Social:\t\t\t\t\t\t', bold: false }, { text: razonSocial, bold: false },
                                        { text: '\nDirección:\t\t\t\t\t\t\t', bold: false }, { text: '\t' + direccion, bold: false },
                                        { text: '\nNúmero de Ruc:\t\t\t\t\t', bold: false }, { text: ruc, bold: false },
                                        { text: '\nFecha:\t\t\t\t\t\t\t\t\t', bold: false }, { text: fechaI + "\t a \t" + fechaF, bold: false }
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
            dataT = this;
            setTimeout(function () {
                $("#tablaTrazabilidad").DataTable().draw();
            }, 1);
            this.api().page.len(paginaGlobal).draw(false);
            if (this.api().data().length == 0) {
                $('.buttons-page-length').prop("disabled", true);
                $('.buttons-html5').prop("disabled", true);
                $('#switchO').prop("disabled", true);
                $('.dropReporte').prop("disabled", true);
                $('#formatoC').prop("disabled", true);
            } else {
                $('.buttons-page-length').prop("disabled", false);
                $('.buttons-html5').prop("disabled", false);
                $('#switchO').prop("disabled", false);
                $('.dropReporte').prop("disabled", false);
                $('#formatoC').prop("disabled", false);
            }
        },
        drawCallback: function () {
            var api = this.api();
            var len = api.page.len();
            paginaGlobal = len;
        }
    });
}
// * INICIALIZAR PLUGIN
$(function () {
    $('#idsEmpleado').select2({
        placeholder: 'Todos los empleados',
        language: {
            inputTooShort: function (e) {
                return "Escribir nombre o apellido";
            },
            loadingMore: function () { return "Cargando más resultados…" },
            noResults: function () { return "No se encontraron resultados" }
        },
    });
    f = moment().format("YYYY-MM-DD");
    fechaAyer = moment().add("day", -1).format("YYYY-MM-DD");
    fechaValue.setDate([fechaAyer, f]);
    $('#fechaInicio').val(fechaAyer);
    $('#fechaFin').val(f);
    inicializarTabla();
});
// * OBTENER DATA
function cargarDatos() {
    var fechaI = $('#fechaInicio').val();
    var fechaF = $('#fechaFin').val();
    var idsEmpleado = $('#empleadoPor').val();
    if (idsEmpleado.length == 0) {
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
        url: "/dataTrazabilidad",
        method: "GET",
        data: {
            fechaI: fechaI,
            fechaF: fechaF,
            idsEmpleado: idsEmpleado
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
        beforeSend: function () {
            $("#tablaTrazabilidad").css('opacity', .3);
            $('div.dataTables_processing').show();
        },
    }).then(function (data) {
        $('div.dataTables_processing').hide();
        $("#tablaTrazabilidad").css('opacity', 1);
        razonSocial = data.organizacion.organi_razonSocial;
        direccion = data.organizacion.organi_direccion;
        ruc = data.organizacion.organi_ruc;
        if ($.fn.DataTable.isDataTable("#tablaTrazabilidad")) {
            $("#tablaTrazabilidad").DataTable().destroy();
        }
        $('#menuIncidencias').empty();
        var listaI = "";
        for (let item = 0; item < data.incidencias.length; item++) {
            listaI += `<li class="liContenido incidenciaHijo" onclick="javascript:menuIncidencias(${data.incidencias[item].id})">
                            <input type="checkbox" id="incidencia${data.incidencias[item].id}">
                            <label for="">${data.incidencias[item].descripcion}</label>
                        </li>`;
        }
        $('#menuIncidencias').append(listaI);
        // ! ****************************************** CABEZERA DE TABLA **************************
        $('#theadT').empty();
        var thead = `<tr>
                        <th>#</th>
                        <th>DNI</th>
                        <th class="formatoNYA">Nombres y apellidos</th>
                        <th class="formatoAYN">Apellidos y nombres</th>
                        <th class="formatoNA">Nombres</th>
                        <th class="formatoNA">Apellidos</th>
                        <th>Departamento</th>
                        <th class="text-center">Tardanzas</th>
                        <th class="text-center">Días Trabajados</th>
                        <th class="text-center">Horas diurnas</th>
                        <th class="text-center">Horas nocturnas</th>
                        <th class="text-center tiempoMuertoE">Tiempo muerto - entrada</th>
                        <th class="text-center tiempoMuertoS">Tiempo muerto - salida</th>
                        <th class="text-center">Faltas</th>`;
        for (let item = 0; item < data.incidencias.length; item++) {
            thead += `<th class="text-center incidencia${data.incidencias[item].id}">${data.incidencias[item].descripcion}</th>`;
        }
        thead += `<th class="text-center">H.E. 25% Diurnas</th>
                    <th class="text-center">H.E. 35% Diurnas</th>
                    <th class="text-center">H.E. 100% Diurnas</th>
                    <th class="text-center">H.E. 25% Nocturnas</th>
                    <th class="text-center">H.E. 35% Nocturnas </th>
                    <th class="text-center">H.E. 100% Nocturnas</th>
                </tr>`;
        $('#theadT').append(thead);
        // ! ****************************************** BODY DE TABLA ******************************
        $('#tbodyT').empty();
        var tbody = "";
        for (let index = 0; index < data.marcaciones.length; index++) {
            var tardanza = 0;
            var diasTrabajdos = 0;
            // : HORAS NORMALES
            var horasNormales = moment.duration(0);
            var diurnas25 = 0;
            var diurnas35 = 0;
            var diurnas100 = 0;
            // : FINALIZACION
            var faltas = 0;
            // : HORAS NOCTURNAS
            var horasNocturnas = moment.duration(0);
            var nocturnas25 = 0;
            var nocturnas35 = 0;
            var nocturnas100 = 0;
            // : SUMA MUERTOS ENTRADA
            var sumaMuertosEntrada = moment.duration(0);
            // : SUMA MUERTOS SALIDA
            var sumaMuertosSalida = moment.duration(0);
            // : RECORRER DATA PARA CALCULAR DATOS
            for (let item = 0; item < data.marcaciones[index].data.length; item++) {
                var dataCompleta = data.marcaciones[index].data[item]["marcaciones"];
                var dataIncidencia = data.marcaciones[index].data[item]["incidencias"]
                // : ACCDER AL ID DE HORARIO
                dataCompleta.forEach(value => {
                    if (value["dataHorario"].idHorarioE != 0) {
                        // : ACUMULADOR DE TIEMPO DE HORAS NORMALES POR HORARIO
                        var horaNormalesPorHorario = moment.duration(0);
                        // : ACUMULADOR DE TIEMPO DE HORAS NOCTURNAS POR HORARIO
                        var horaNocturnasPorHorario = moment.duration(0);
                        var estado = true;
                        var entradaMenor = moment.duration(0);
                        value["dataMarcaciones"].forEach(function (element, index) {
                            // : BUSCAR DATA EN TIEMPOS NORMALES
                            if (element.entrada != null) {
                                estado = false;
                            }
                            if (index == 0 && element.entrada != null) {
                                entradaMenor = moment.duration(moment(element.entrada));
                            }
                        });
                        if (estado) {                // : FALTAS
                            if (dataIncidencia == 0) {
                                faltas++;
                            }
                        } else {
                            // : CONTANDOR DE TARDANZA
                            var entradaHorario = moment.duration(moment(value["dataHorario"].horarioIni)).add({ "minutes": value["dataHorario"].toleranciaI });
                            if (entradaMenor != 0) {
                                if (entradaMenor > entradaHorario) {
                                    tardanza++;
                                }
                            }
                            // : VARIABLE PARA SABER QUE TIPO DE MARCACIÓN FUE PRIMERA
                            var primeraM = undefined;
                            // : DIAS TRABAJADOS
                            diasTrabajdos++;
                            // : TIEMPO TOTALES CON HORARIO Y SOBRE TIEMPO
                            value["dataMarcaciones"].forEach(function (element) {
                                // : BUSCAR DATA EN TIEMPOS NORMALES
                                if (element.entrada != 0 && element.salida != 0) {
                                    var entradaData = moment(element.entrada);
                                    var salidaData = moment(element.salida);
                                    // : TIEMPOS MUERTOS
                                    if (value["dataHorario"].tiempoMuertoI == 1) {
                                        // : SI ENTRADA ES MENOR A LA HORA DE INICIO DE HORARIO
                                        if (entradaData.clone().isBefore(moment(value["dataHorario"].horarioIni))) {
                                            if (salidaData.clone().isAfter(moment(value["dataHorario"].horarioIni))) {
                                                // : HORA DE ENTRADA
                                                var tiempoMuerto = moment(value["dataHorario"].horarioIni) - entradaData.clone();
                                                sumaMuertosEntrada = sumaMuertosEntrada.add(tiempoMuerto);
                                                entradaData = moment(value["dataHorario"].horarioIni);
                                                // : HORA DE SALIDA
                                                if (value["dataHorario"].tiempoMuertoS == 1) {
                                                    if (salidaData.clone().isAfter(moment(value["dataHorario"].horarioFin))) {
                                                        var tiempoMuerto = moment.duration(parseInt(value["dataHorario"].toleranciaF), "minutes");
                                                        sumaMuertosSalida = sumaMuertosSalida.add(tiempoMuerto);
                                                        var NuevaSalida = salidaData.clone().subtract(value["dataHorario"].toleranciaF, "minutes").format("YYYY-MM-DD HH:mm:ss");
                                                        salidaData = moment(NuevaSalida);
                                                    }
                                                }
                                            } else {
                                                var tiempoMuerto = salidaData.clone() - entradaData.clone();
                                                sumaMuertosEntrada = sumaMuertosEntrada.add(tiempoMuerto);
                                                entradaData = moment.duration(0);
                                                salidaData = moment.duration(0);
                                            }
                                        } else {
                                            // : HORA DE SALIDA
                                            if (value["dataHorario"].tiempoMuertoS == 1) {
                                                if (salidaData.clone().isAfter(moment(value["dataHorario"].horarioFin))) {
                                                    var tiempoMuerto = moment.duration(parseInt(value["dataHorario"].toleranciaF), "minutes");
                                                    sumaMuertosSalida = sumaMuertosSalida.add(tiempoMuerto);
                                                    var NuevaSalida = salidaData.clone().subtract(value["dataHorario"].toleranciaF, "minutes").format("YYYY-MM-DD HH:mm:ss");
                                                    salidaData = moment(NuevaSalida);
                                                }
                                            }
                                        }
                                    } else {
                                        // : HORA DE SALIDA
                                        if (value["dataHorario"].tiempoMuertoS == 1) {
                                            if (salidaData.clone().isAfter(moment(value["dataHorario"].horarioFin))) {
                                                var tiempoMuerto = moment.duration(parseInt(value["dataHorario"].toleranciaF), "minutes");
                                                sumaMuertosSalida = sumaMuertosSalida.add(tiempoMuerto);
                                                var NuevaSalida = salidaData.clone().subtract(value["dataHorario"].toleranciaF, "minutes").format("YYYY-MM-DD HH:mm:ss");
                                                salidaData = moment(NuevaSalida);
                                            }
                                        }
                                    }
                                    if (entradaData != 0 && salidaData != 0) {
                                        // : TIEMPOS MÁXIMOS
                                        var tiempoMaximoDiurno = moment(entradaData.clone().format("YYYY-MM-DD") + " " + "22:00:00");
                                        var tiempoMaximoNocturno = moment(entradaData.clone().format("YYYY-MM-DD") + " " + "06:00:00");
                                        // : ACUMULAR TIEMPO CALCULADOS
                                        var acumuladorEntreM = moment.duration(0);
                                        // : TIEMPO ENTRE MARCACIONES
                                        var tiempoEntreM = moment.duration(salidaData.diff(entradaData));
                                        if (entradaData.isAfter(tiempoMaximoNocturno) && entradaData.isSameOrBefore(tiempoMaximoDiurno)) {
                                            if (primeraM == undefined) primeraM = 0;
                                            if (salidaData.clone().isSameOrBefore(tiempoMaximoDiurno)) {
                                                //: ************************************************** HORAS NORMALES **********************************************
                                                var tiempoNormal = salidaData - entradaData;
                                                var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                horasNormales = horasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                horaNormalesPorHorario = horaNormalesPorHorario.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                            } else {
                                                var minuendoResta = tiempoMaximoDiurno.clone();
                                                var sustraendoResta = entradaData.clone();
                                                var contadorDias = 1;
                                                while (acumuladorEntreM < tiempoEntreM) {
                                                    //: ************************************************** HORAS NORMALES **********************************************
                                                    var tiempoNormal = minuendoResta - sustraendoResta;
                                                    var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                    var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                    var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                    horasNormales = horasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                    horaNormalesPorHorario = horaNormalesPorHorario.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                    acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                    // : ************************************************* FINALIZACION *****************************************************
                                                    var tiempoMaximoDiurnoAnterior = tiempoMaximoDiurno;
                                                    tiempoMaximoDiurno = tiempoMaximoDiurno.clone().add("day", contadorDias);
                                                    tiempoMaximoNocturno = tiempoMaximoNocturno.clone().add("day", contadorDias);
                                                    if (acumuladorEntreM < tiempoEntreM) {
                                                        if (salidaData.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                            // : HORA NOCTURNA
                                                            sustraendoResta = minuendoResta;
                                                            minuendoResta = salidaData;
                                                            var tiempoNocturno = minuendoResta - sustraendoResta;
                                                            var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                            var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                            var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                            horasNocturnas = horasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                            horaNocturnasPorHorario = horaNocturnasPorHorario.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                            acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                        } else {
                                                            minuendoResta = tiempoMaximoNocturno;
                                                            sustraendoResta = tiempoMaximoDiurnoAnterior;
                                                            // : HORA NOCTURNA
                                                            var tiempoNocturno = minuendoResta - sustraendoResta;
                                                            var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                            var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                            var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                            horasNocturnas = horasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                            horaNocturnasPorHorario = horaNocturnasPorHorario.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                            acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                            if (salidaData.clone().isSameOrBefore(tiempoMaximoDiurno)) {
                                                                minuendoResta = salidaData.clone();
                                                                sustraendoResta = tiempoMaximoNocturno;
                                                            } else {
                                                                minuendoResta = tiempoMaximoDiurno;
                                                                sustraendoResta = tiempoMaximoNocturno;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            if (primeraM == undefined) primeraM = 1;
                                            if (salidaData.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                // : HORAS NOCTURNAS
                                                var tiempoNocturno = salidaData - entradaData;
                                                var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                horasNocturnas = horasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                horaNocturnasPorHorario = horaNocturnasPorHorario.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                            } else {
                                                if (moment.duration(entradaData.clone().format("HH:mm:ss")) < moment.duration("06:00:00")) {
                                                    tiempoMaximoDiurno = moment(entradaData.clone().format("YYYY-MM-DD") + " " + "22:00:00");
                                                    tiempoMaximoNocturno = moment(entradaData.clone().format("YYYY-MM-DD") + " " + "06:00:00");
                                                } else {
                                                    tiempoMaximoDiurno = moment(entradaData.clone().add("day", 1).format("YYYY-MM-DD") + " " + "22:00:00");
                                                    tiempoMaximoNocturno = moment(entradaData.clone().add("day", 1).format("YYYY-MM-DD") + " " + "06:00:00");
                                                }
                                                if (salidaData.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                    // : HORAS NOCTURNAS
                                                    var tiempoNocturno = salidaData - entradaData;
                                                    var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                    var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                    var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                    horasNocturnas = horasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                    horaNocturnasPorHorario = horaNocturnasPorHorario.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                } else {
                                                    var minuendoResta = tiempoMaximoNocturno.clone();
                                                    var sustraendoResta = entradaData.clone();
                                                    var contadorDias = 1;
                                                    while (acumuladorEntreM < tiempoEntreM) {
                                                        // : HORAS NOCTURNAS
                                                        var tiempoNocturno = minuendoResta - sustraendoResta;
                                                        var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                        var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                        var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                        horasNocturnas = horasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                        horaNocturnasPorHorario = horaNocturnasPorHorario.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                        acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                        // : CALCULOS DE TIEMPO
                                                        var tiempoMaximoDiurnoAnterior = tiempoMaximoDiurno;
                                                        tiempoMaximoDiurno = moment(tiempoMaximoDiurno.clone().add("day", contadorDias));
                                                        tiempoMaximoNocturno = moment(tiempoMaximoNocturno.clone().add("day", contadorDias));
                                                        if (acumuladorEntreM < tiempoEntreM) {
                                                            if (salidaData.clone().isSameOrBefore(tiempoMaximoDiurnoAnterior)) {
                                                                sustraendoResta = minuendoResta;
                                                                minuendoResta = salidaData.clone();
                                                                // : HORAS NORMALES
                                                                var tiempoNormal = minuendoResta - sustraendoResta;
                                                                var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                                var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                                var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                                horasNormales = horasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                                horaNormalesPorHorario = horaNormalesPorHorario.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                                acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                            } else {
                                                                sustraendoResta = minuendoResta;
                                                                minuendoResta = tiempoMaximoDiurnoAnterior;
                                                                // : HORAS NORMALES
                                                                var tiempoNormal = minuendoResta - sustraendoResta;
                                                                var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                                var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                                var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                                horasNormales = horasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                                horaNormalesPorHorario = horaNormalesPorHorario.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                                acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                                if (salidaData.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                                    minuendoResta = salidaData.clone();
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
                            });
                            // : SOBRE TIEMPO POR HORARIO, VERIFICAMOS QUE FUE PRIMERO
                            if (primeraM != undefined) {
                                var horasObligadasHorario = moment.duration(value["dataHorario"].horasObligadas);
                                var nuevaHorasObligadas = moment.duration(0);
                                if (primeraM == 0) {
                                    if (horaNormalesPorHorario > horasObligadasHorario) {
                                        // : HORARIO NORMAL
                                        nuevaHorasObligadas = moment.duration(0);
                                        var tiempoExtraResta = horaNormalesPorHorario - horasObligadasHorario;
                                        var tiempoSobrante = moment.duration(0);
                                        if (value["dataHorario"].idDiurna == null) {
                                            if (tiempoExtraResta > moment.duration("02:00:00")) {
                                                diurnas25++;
                                                var restaDe25 = tiempoExtraResta - moment.duration("02:00:00");
                                                tiempoSobrante = moment.duration(restaDe25);
                                                if (tiempoSobrante > moment.duration("02:00:00")) {
                                                    diurnas35++;
                                                    var restaDe35 = tiempoSobrante - moment.duration("02:00:00");
                                                    tiempoSobrante = moment.duration(restaDe35);
                                                    if (tiempoSobrante > moment.duration(0)) {
                                                        diurnas100++;
                                                    }
                                                } else {
                                                    if (tiempoSobrante > moment.duration(0)) {
                                                        diurnas35++;
                                                    }
                                                }
                                            } else {
                                                diurnas25++;
                                            }
                                        } else {
                                            // : CONDICIONAL DE 25% DIURNA
                                            // ! QUE NO LLENE EN EL 25
                                            if (!(value["dataHorario"].estado25D == 1)) {
                                                // ! QUE NO SEA VACIO
                                                if (value["dataHorario"].estado25D != 2) {
                                                    if (tiempoExtraResta > moment.duration("02:00:00")) {
                                                        diurnas25++;
                                                        tiempoExtraResta = tiempoExtraResta - moment.duration("02:00:00");
                                                    } else {
                                                        diurnas25++;
                                                        tiempoExtraResta = moment.duration(0);
                                                    }
                                                }
                                                // : CONDICIONAL DE 35% DIURNA
                                                // ! QUE NO LLENE EN EL 35
                                                if (!(value["dataHorario"].estado35D == 1)) {
                                                    if (value["dataHorario"].estado35D != 2) {
                                                        if (tiempoExtraResta > moment.duration("02:00:00")) {
                                                            diurnas35++;
                                                            tiempoExtraResta = tiempoExtraResta - moment.duration("02:00:00");
                                                        } else {
                                                            if (tiempoExtraResta != 0) {
                                                                diurnas35++;
                                                                tiempoExtraResta = moment.duration(0);
                                                            }
                                                        }
                                                    }
                                                    // : CONDICIONAL DE 100% DIURNA
                                                    // ! QUE NO LLENA
                                                    if (!(value["dataHorario"].estado100D == 1)) {
                                                        // ! QUE NO SEA VACIO
                                                        if (value["dataHorario"].estado100D != 2) {
                                                            if (tiempoExtraResta != 0) {
                                                                diurnas100++;
                                                            }
                                                        }
                                                    } else {
                                                        if (tiempoExtraResta != 0) {
                                                            diurnas100++;
                                                        }
                                                    }
                                                } else {
                                                    if (tiempoExtraResta != 0) {
                                                        diurnas35++;
                                                    }
                                                }
                                            } else {
                                                diurnas25++;
                                            }
                                        }
                                        // : HORARIO NOCTURNO
                                        if (horaNocturnasPorHorario > nuevaHorasObligadas) {
                                            var tiempoExtraRestaN = horaNocturnasPorHorario - nuevaHorasObligadas;
                                            if (value["dataHorario"].idNocturna == null) {
                                                if (tiempoExtraRestaN > moment.duration("02:00:00")) {
                                                    nocturnas25++;
                                                    var restaDe25N = tiempoExtraRestaN - moment.duration("02:00:00");
                                                    tiempoSobranteN = moment.duration(restaDe25N);
                                                    if (tiempoSobranteN > moment.duration("02:00:00")) {
                                                        nocturnas35++;
                                                        var restaDe35N = tiempoSobranteN - moment.duration("02:00:00");
                                                        tiempoSobranteN = moment.duration(restaDe35N);
                                                        if (tiempoSobranteN > moment.duration(0)) {
                                                            nocturnas100++;
                                                        }
                                                    } else {
                                                        if (tiempoSobranteN > moment.duration(0)) {
                                                            nocturnas35++;
                                                        }
                                                    }
                                                } else {
                                                    nocturnas25++;
                                                }
                                            } else {
                                                // : CONDICIONAL DE 25% NOCTURNA
                                                // ! QUE NO LLENE EN EL 25
                                                if (!(value["dataHorario"].estado25N == 1)) {
                                                    // ! QUE NO SEA VACIO
                                                    if (value["dataHorario"].estado25N != 2) {
                                                        if (tiempoExtraRestaN > moment.duration("02:00:00")) {
                                                            nocturnas25++;
                                                            tiempoExtraRestaN = tiempoExtraRestaN - moment.duration("02:00:00");
                                                        } else {
                                                            nocturnas25++;
                                                            tiempoExtraRestaN = moment.duration(0);
                                                        }
                                                    }
                                                    // : CONDICIONAL DE 35% NOCTURNA
                                                    // ! QUE NO LLENE 35%
                                                    if (!(value["dataHorario"].estado35N == 1)) {
                                                        // ! QUE NO SEA VACIO
                                                        if (value["dataHorario"].estado35N != 2) {
                                                            if (tiempoExtraRestaN > moment.duration("02:00:00")) {
                                                                nocturnas35++;
                                                                tiempoExtraRestaN = tiempoExtraRestaN - moment.duration("02:00:00");
                                                            } else {
                                                                if (tiempoExtraRestaN != 0) {
                                                                    nocturnas35++;
                                                                    tiempoExtraRestaN = moment.duration(0);
                                                                }
                                                            }
                                                        }
                                                        // : CONDICIONAL DE 100% NOCTURNA
                                                        // ! QUE NO LLENA
                                                        if (!(value["dataHorario"].estado100N == 1)) {
                                                            // ! QUE NO SEA VACIO
                                                            if (value["dataHorario"].estado100D != 2) {
                                                                if (tiempoExtraRestaN != 0) {
                                                                    nocturnas100++;
                                                                }
                                                            }
                                                        } else {
                                                            if (tiempoExtraRestaN != 0) {
                                                                nocturnas100++;
                                                            }
                                                        }
                                                    } else {
                                                        if (tiempoExtraRestaN != 0) {
                                                            nocturnas35++;
                                                        }
                                                    }
                                                } else {
                                                    nocturnas25++;
                                                }
                                            }
                                        }
                                    } else {
                                        var restaHorasO = horasObligadasHorario - horaNormalesPorHorario;
                                        nuevaHorasObligadas = moment.duration(restaHorasO);
                                        // : HORARIO NOCTURNO
                                        if (horaNocturnasPorHorario > nuevaHorasObligadas) {
                                            var tiempoExtraRestaN = horaNocturnasPorHorario - nuevaHorasObligadas;
                                            var tiempoSobranteN = {};
                                            if (value["dataHorario"].idNocturna == null) {
                                                if (tiempoExtraRestaN > moment.duration("02:00:00")) {
                                                    nocturnas25++;
                                                    var restaDe25N = tiempoExtraRestaN - moment.duration("02:00:00");
                                                    tiempoSobranteN = moment.duration(restaDe25N);
                                                    if (tiempoSobranteN > moment.duration("02:00:00")) {
                                                        nocturnas35++;
                                                        var restaDe35N = tiempoSobranteN - moment.duration("02:00:00");
                                                        tiempoSobranteN = moment.duration(restaDe35N);
                                                        if (tiempoSobranteN > moment.duration(0)) {
                                                            nocturnas100++;
                                                        }
                                                    } else {
                                                        if (tiempoSobranteN > moment.duration(0)) {
                                                            nocturnas35++;
                                                        }
                                                    }
                                                } else {
                                                    nocturnas25++;
                                                }
                                            } else {
                                                // : CONDICIONAL DE 25% NOCTURNA
                                                // ! QUE NO LLENE EN EL 25
                                                if (!(value["dataHorario"].estado25N == 1)) {
                                                    // ! QUE NO SEA VACIO
                                                    if (value["dataHorario"].estado25N != 2) {
                                                        if (tiempoExtraRestaN > moment.duration("02:00:00")) {
                                                            nocturnas25++;
                                                            tiempoExtraRestaN = tiempoExtraRestaN - moment.duration("02:00:00");
                                                        } else {
                                                            nocturnas25++;
                                                            tiempoExtraRestaN = moment.duration(0);
                                                        }
                                                    }
                                                    // : CONDICIONAL DE 35% NOCTURNA
                                                    // ! QUE NO LLENE 35%
                                                    if (!(value["dataHorario"].estado35N == 1)) {
                                                        // ! QUE NO SEA VACIO
                                                        if (value["dataHorario"].estado35N != 2) {
                                                            if (tiempoExtraRestaN > moment.duration("02:00:00")) {
                                                                nocturnas35++;
                                                                tiempoExtraRestaN = tiempoExtraRestaN - moment.duration("02:00:00");
                                                            } else {
                                                                if (tiempoExtraRestaN != 0) {
                                                                    nocturnas35++;
                                                                    tiempoExtraRestaN = moment.duration(0);
                                                                }
                                                            }
                                                        }
                                                        // : CONDICIONAL DE 100% NOCTURNA
                                                        // ! QUE NO LLENA
                                                        if (!(value["dataHorario"].estado100N == 1)) {
                                                            // ! QUE NO SEA VACIO
                                                            if (value["dataHorario"].estado100D != 2) {
                                                                if (tiempoExtraRestaN != 0) {
                                                                    nocturnas100++;
                                                                }
                                                            }
                                                        } else {
                                                            if (tiempoExtraRestaN != 0) {
                                                                nocturnas100++;
                                                            }
                                                        }
                                                    } else {
                                                        if (tiempoExtraRestaN != 0) {
                                                            nocturnas35++;
                                                        }
                                                    }
                                                } else {
                                                    nocturnas25++;
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if (horaNocturnasPorHorario > horasObligadasHorario) {
                                        // : HORARIO NOCTURNO
                                        nuevaHorasObligadas = moment.duration(0);
                                        var tiempoExtraRestaN = horaNocturnasPorHorario - horasObligadasHorario;
                                        var tiempoSobranteN = {};
                                        if (value["dataHorario"].idNocturna == null) {
                                            if (tiempoExtraRestaN > moment.duration("02:00:00")) {
                                                nocturnas25++;
                                                var restaDe25N = tiempoExtraRestaN - moment.duration("02:00:00");
                                                tiempoSobranteN = moment.duration(restaDe25N);
                                                if (tiempoSobranteN > moment.duration("02:00:00")) {
                                                    nocturnas35++;
                                                    var restaDe35N = tiempoSobranteN - moment.duration("02:00:00");
                                                    tiempoSobranteN = moment.duration(restaDe35N);
                                                    if (tiempoSobranteN > moment.duration(0)) {
                                                        nocturnas100++;
                                                    }
                                                } else {
                                                    if (tiempoSobranteN > moment.duration(0)) {
                                                        nocturnas35++;
                                                    }
                                                }
                                            } else {
                                                nocturnas25++;
                                            }
                                        } else {
                                            // : CONDICIONAL DE 25% NOCTURNA
                                            // ! QUE NO LLENE EN EL 25
                                            if (!(value["dataHorario"].estado25N == 1)) {
                                                // ! QUE NO SEA VACIO
                                                if (value["dataHorario"].estado25N != 2) {
                                                    if (tiempoExtraRestaN > moment.duration("02:00:00")) {
                                                        nocturnas25++;
                                                        tiempoExtraRestaN = tiempoExtraRestaN - moment.duration("02:00:00");
                                                    } else {
                                                        nocturnas25++;
                                                        tiempoExtraRestaN = moment.duration(0);
                                                    }
                                                }
                                                // : CONDICIONAL DE 35% NOCTURNA
                                                // ! QUE NO LLENE 35%
                                                if (!(value["dataHorario"].estado35N == 1)) {
                                                    // ! QUE NO SEA VACIO
                                                    if (value["dataHorario"].estado35N != 2) {
                                                        if (tiempoExtraRestaN > moment.duration("02:00:00")) {
                                                            nocturnas35++;
                                                            tiempoExtraRestaN = tiempoExtraRestaN - moment.duration("02:00:00");
                                                        } else {
                                                            if (tiempoExtraRestaN != 0) {
                                                                nocturnas35++;
                                                                tiempoExtraRestaN = moment.duration(0);
                                                            }
                                                        }
                                                    }
                                                    // : CONDICIONAL DE 100% NOCTURNA
                                                    // ! QUE NO LLENA
                                                    if (!(value["dataHorario"].estado100N == 1)) {
                                                        // ! QUE NO SEA VACIO
                                                        if (value["dataHorario"].estado100D != 2) {
                                                            if (tiempoExtraRestaN != 0) {
                                                                nocturnas100++;
                                                            }
                                                        }
                                                    } else {
                                                        if (tiempoExtraRestaN != 0) {
                                                            nocturnas100++;
                                                        }
                                                    }
                                                } else {
                                                    if (tiempoExtraRestaN != 0) {
                                                        nocturnas35++;
                                                    }
                                                }
                                            } else {
                                                nocturnas25++;
                                            }
                                        }
                                        // : HORARIO NORMAL 
                                        if (horaNormalesPorHorario > nuevaHorasObligadas) {
                                            var tiempoExtraResta = horaNormalesPorHorario - nuevaHorasObligadas;
                                            var tiempoSobrante = moment.duration(0);
                                            if (value["dataHorario"].idDiurna == null) {
                                                if (tiempoExtraResta > moment.duration("02:00:00")) {
                                                    diurnas25++;
                                                    var restaDe25 = tiempoExtraResta - moment.duration("02:00:00");
                                                    tiempoSobrante = moment.duration(restaDe25);
                                                    if (tiempoSobrante > moment.duration("02:00:00")) {
                                                        diurnas35++;
                                                        var restaDe35 = tiempoSobrante - moment.duration("02:00:00");
                                                        tiempoSobrante = moment.duration(restaDe35);
                                                        if (tiempoSobrante > moment.duration(0)) {
                                                            diurnas100++;
                                                        }
                                                    } else {
                                                        if (tiempoSobrante > moment.duration(0)) {
                                                            diurnas35++;
                                                        }
                                                    }
                                                } else {
                                                    diurnas25++;
                                                }
                                            } else {
                                                // : CONDICIONAL DE 25% DIURNA
                                                // ! QUE NO LLENE EN EL 25
                                                if (!(value["dataHorario"].estado25D == 1)) {
                                                    // ! QUE NO SEA VACIO
                                                    if (value["dataHorario"].estado25D != 2) {
                                                        if (tiempoExtraResta > moment.duration("02:00:00")) {
                                                            diurnas25++;
                                                            tiempoExtraResta = tiempoExtraResta - moment.duration("02:00:00");
                                                        } else {
                                                            diurnas25++;
                                                            tiempoExtraResta = moment.duration(0);
                                                        }
                                                    }
                                                    // : CONDICIONAL DE 35% DIURNA
                                                    // ! QUE NO LLENE EN EL 35
                                                    if (!(value["dataHorario"].estado35D == 1)) {
                                                        if (value["dataHorario"].estado35D != 2) {
                                                            if (tiempoExtraResta > moment.duration("02:00:00")) {
                                                                diurnas35++;
                                                                tiempoExtraResta = tiempoExtraResta - moment.duration("02:00:00");
                                                            } else {
                                                                if (tiempoExtraResta != 0) {
                                                                    diurnas35++;
                                                                    tiempoExtraResta = moment.duration(0);
                                                                }
                                                            }
                                                        }
                                                        // : CONDICIONAL DE 100% DIURNA
                                                        // ! QUE NO LLENA
                                                        if (!(value["dataHorario"].estado100D == 1)) {
                                                            // ! QUE NO SEA VACIO
                                                            if (value["dataHorario"].estado100D != 2) {
                                                                if (tiempoExtraResta != 0) {
                                                                    diurnas100++;
                                                                }
                                                            }
                                                        } else {
                                                            if (tiempoExtraResta != 0) {
                                                                diurnas100++;
                                                            }
                                                        }
                                                    } else {
                                                        if (tiempoExtraResta != 0) {
                                                            diurnas35++;
                                                        }
                                                    }
                                                } else {
                                                    diurnas25++;
                                                }
                                            }
                                        }
                                    } else {
                                        var restaHorasO = horasObligadasHorario - horaNocturnasPorHorario;
                                        nuevaHorasObligadas = moment.duration(restaHorasO);
                                        // : HOARIO NORMAL
                                        if (horaNormalesPorHorario > nuevaHorasObligadas) {
                                            var tiempoExtraResta = horaNormalesPorHorario - nuevaHorasObligadas;
                                            var tiempoSobrante = moment.duration(0);
                                            if (value["dataHorario"].idDiurna == null) {
                                                if (tiempoExtraResta > moment.duration("02:00:00")) {
                                                    diurnas25++;
                                                    var restaDe25 = tiempoExtraResta - moment.duration("02:00:00");
                                                    tiempoSobrante = moment.duration(restaDe25);
                                                    if (tiempoSobrante > moment.duration("02:00:00")) {
                                                        diurnas35++;
                                                        var restaDe35 = tiempoSobrante - moment.duration("02:00:00");
                                                        tiempoSobrante = moment.duration(restaDe35);
                                                        if (tiempoSobrante > moment.duration(0)) {
                                                            diurnas100++;
                                                        }
                                                    } else {
                                                        if (tiempoSobrante > moment.duration(0)) {
                                                            diurnas35++;
                                                        }
                                                    }
                                                } else {
                                                    diurnas25++;
                                                }
                                            } else {
                                                // : CONDICIONAL DE 25% DIURNA
                                                // ! QUE NO LLENE EN EL 25
                                                if (!(value["dataHorario"].estado25D == 1)) {
                                                    // ! QUE NO SEA VACIO
                                                    if (value["dataHorario"].estado25D != 2) {
                                                        if (tiempoExtraResta > moment.duration("02:00:00")) {
                                                            diurnas25++;
                                                            tiempoExtraResta = tiempoExtraResta - moment.duration("02:00:00");
                                                        } else {
                                                            diurnas25++;
                                                            tiempoExtraResta = moment.duration(0);
                                                        }
                                                    }
                                                    // : CONDICIONAL DE 35% DIURNA
                                                    // ! QUE NO LLENE EN EL 35
                                                    if (!(value["dataHorario"].estado35D == 1)) {
                                                        if (value["dataHorario"].estado35D != 2) {
                                                            if (tiempoExtraResta > moment.duration("02:00:00")) {
                                                                diurnas35++;
                                                                tiempoExtraResta = tiempoExtraResta - moment.duration("02:00:00");
                                                            } else {
                                                                if (tiempoExtraResta != 0) {
                                                                    diurnas35++;
                                                                    tiempoExtraResta = moment.duration(0);
                                                                }
                                                            }
                                                        }
                                                        // : CONDICIONAL DE 100% DIURNA
                                                        // ! QUE NO LLENA
                                                        if (!(value["dataHorario"].estado100D == 1)) {
                                                            // ! QUE NO SEA VACIO
                                                            if (value["dataHorario"].estado100D != 2) {
                                                                if (tiempoExtraResta != 0) {
                                                                    diurnas100++;
                                                                }
                                                            }
                                                        } else {
                                                            if (tiempoExtraResta != 0) {
                                                                diurnas100++;
                                                            }
                                                        }
                                                    } else {
                                                        if (tiempoExtraResta != 0) {
                                                            diurnas35++;
                                                        }
                                                    }
                                                } else {
                                                    diurnas25++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        var estado = true;
                        value["dataMarcaciones"].forEach(function (element, index) {
                            // : BUSCAR DATA EN TIEMPOS NORMALES
                            if (element.entrada != null) {
                                estado = false;
                            }
                            if (index == 0 && element.entrada != null) {
                                entradaMenor = moment.duration(element.entrada);
                            }
                        });
                        if (!estado) {
                            // : DIAS TRABAJADOS
                            diasTrabajdos++;
                            // : TIEMPO TOTALES SIN HORARIO Y SOBRE TIEMPO
                            value["dataMarcaciones"].forEach(function (element) {
                                // : BUSCAR DATA EN TIEMPOS NORMALES
                                if (element.entrada != 0 && element.salida != 0) {
                                    var entradaData = moment(element.entrada);
                                    var salidaData = moment(element.salida);
                                    // : TIEMPOS MÁXIMOS
                                    var tiempoMaximoDiurno = moment(entradaData.clone().format("YYYY-MM-DD") + " " + "22:00:00");
                                    var tiempoMaximoNocturno = moment(entradaData.clone().format("YYYY-MM-DD") + " " + "06:00:00");
                                    // : ACUMULAR TIEMPO CALCULADOS
                                    var acumuladorEntreM = moment.duration(0);
                                    // : TIEMPO ENTRE MARCACIONES
                                    var tiempoEntreM = moment.duration(salidaData.diff(entradaData));
                                    if (entradaData.isAfter(tiempoMaximoNocturno) && entradaData.isSameOrBefore(tiempoMaximoDiurno)) {
                                        if (primeraM == undefined) primeraM = 0;
                                        if (salidaData.clone().isSameOrBefore(tiempoMaximoDiurno)) {
                                            //: ************************************************** HORAS NORMALES **********************************************
                                            var tiempoNormal = salidaData - entradaData;
                                            var segundosNormal = moment.duration(tiempoNormal).seconds();
                                            var minutosNormal = moment.duration(tiempoNormal).minutes();
                                            var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                            horasNormales = horasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                        } else {
                                            var minuendoResta = tiempoMaximoDiurno.clone();
                                            var sustraendoResta = entradaData.clone();
                                            var contadorDias = 1;
                                            while (acumuladorEntreM < tiempoEntreM) {
                                                //: ************************************************** HORAS NORMALES **********************************************
                                                var tiempoNormal = minuendoResta - sustraendoResta;
                                                var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                horasNormales = horasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                // : ************************************************* FINALIZACION *****************************************************
                                                var tiempoMaximoDiurnoAnterior = tiempoMaximoDiurno;
                                                tiempoMaximoDiurno = tiempoMaximoDiurno.clone().add("day", contadorDias);
                                                tiempoMaximoNocturno = tiempoMaximoNocturno.clone().add("day", contadorDias);
                                                if (acumuladorEntreM < tiempoEntreM) {
                                                    if (salidaData.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                        // : HORA NOCTURNA
                                                        sustraendoResta = minuendoResta;
                                                        minuendoResta = salidaData;
                                                        var tiempoNocturno = minuendoResta - sustraendoResta;
                                                        var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                        var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                        var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                        horasNocturnas = horasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                        acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                    } else {
                                                        minuendoResta = tiempoMaximoNocturno;
                                                        sustraendoResta = tiempoMaximoDiurnoAnterior;
                                                        // : HORA NOCTURNA
                                                        var tiempoNocturno = minuendoResta - sustraendoResta;
                                                        var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                        var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                        var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                        horasNocturnas = horasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                        acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                        if (salidaData.clone().isSameOrBefore(tiempoMaximoDiurno)) {
                                                            minuendoResta = salidaData.clone();
                                                            sustraendoResta = tiempoMaximoNocturno;
                                                        } else {
                                                            minuendoResta = tiempoMaximoDiurno;
                                                            sustraendoResta = tiempoMaximoNocturno;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if (primeraM == undefined) primeraM = 1;
                                        if (salidaData.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                            // : HORAS NOCTURNAS
                                            var tiempoNocturno = salidaData - entradaData;
                                            var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                            var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                            var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                            horasNocturnas = horasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                        } else {
                                            if (moment.duration(entradaData.clone().format("HH:mm:ss")) < moment.duration("06:00:00")) {
                                                tiempoMaximoDiurno = moment(entradaData.clone().format("YYYY-MM-DD") + " " + "22:00:00");
                                                tiempoMaximoNocturno = moment(entradaData.clone().format("YYYY-MM-DD") + " " + "06:00:00");
                                            } else {
                                                tiempoMaximoDiurno = moment(entradaData.clone().add("day", 1).format("YYYY-MM-DD") + " " + "22:00:00");
                                                tiempoMaximoNocturno = moment(entradaData.clone().add("day", 1).format("YYYY-MM-DD") + " " + "06:00:00");
                                            }
                                            if (salidaData.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                // : HORAS NOCTURNAS
                                                var tiempoNocturno = salidaData - entradaData;
                                                var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                horasNocturnas = horasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                            } else {
                                                var minuendoResta = tiempoMaximoNocturno.clone();
                                                var sustraendoResta = entradaData.clone();
                                                var contadorDias = 1;
                                                while (acumuladorEntreM < tiempoEntreM) {
                                                    // : HORAS NOCTURNAS
                                                    var tiempoNocturno = minuendoResta - sustraendoResta;
                                                    var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                                    var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                                    var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                                    horasNocturnas = horasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                    acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                                    // : CALCULOS DE TIEMPO
                                                    var tiempoMaximoDiurnoAnterior = tiempoMaximoDiurno;
                                                    tiempoMaximoDiurno = moment(tiempoMaximoDiurno.clone().add("day", contadorDias));
                                                    tiempoMaximoNocturno = moment(tiempoMaximoNocturno.clone().add("day", contadorDias));
                                                    if (acumuladorEntreM < tiempoEntreM) {
                                                        if (salidaData.clone().isSameOrBefore(tiempoMaximoDiurnoAnterior)) {
                                                            sustraendoResta = minuendoResta;
                                                            minuendoResta = salidaData.clone();
                                                            // : HORAS NORMALES
                                                            var tiempoNormal = minuendoResta - sustraendoResta;
                                                            var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                            var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                            var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                            horasNormales = horasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                            acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                        } else {
                                                            sustraendoResta = minuendoResta;
                                                            minuendoResta = tiempoMaximoDiurnoAnterior;
                                                            // : HORAS NORMALES
                                                            var tiempoNormal = minuendoResta - sustraendoResta;
                                                            var segundosNormal = moment.duration(tiempoNormal).seconds();
                                                            var minutosNormal = moment.duration(tiempoNormal).minutes();
                                                            var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                                            horasNormales = horasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                            acumuladorEntreM = acumuladorEntreM.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                                            if (salidaData.clone().isSameOrBefore(tiempoMaximoNocturno)) {
                                                                minuendoResta = salidaData.clone();
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
                            });
                        }
                    }
                });
            }
            // : HORAS NORMALES 
            var horaHorasNormales = Math.trunc(moment.duration(horasNormales).asHours());
            var minutoHorasNormales = moment.duration(horasNormales).minutes();
            var segundoHorasNormales = moment.duration(horasNormales).seconds();
            if (horaHorasNormales < 10) {
                horaHorasNormales = "0" + horaHorasNormales;
            }
            if (minutoHorasNormales < 10) {
                minutoHorasNormales = "0" + minutoHorasNormales;
            }
            if (segundoHorasNormales < 10) {
                segundoHorasNormales = "0" + segundoHorasNormales;
            }
            // : HORAS NOCTURNAS
            var horaHorasNocturnas = Math.trunc(moment.duration(horasNocturnas).asHours());
            var minutoHorasNocturnas = moment.duration(horasNocturnas).minutes();
            var segundoHorasNocturnas = moment.duration(horasNocturnas).seconds();
            if (horaHorasNocturnas < 10) {
                horaHorasNocturnas = "0" + horaHorasNocturnas;
            }
            if (minutoHorasNocturnas < 10) {
                minutoHorasNocturnas = "0" + minutoHorasNocturnas;
            }
            if (segundoHorasNocturnas < 10) {
                segundoHorasNocturnas = "0" + segundoHorasNocturnas;
            }
            // : TIEMPO MUERTO - ENTRADA
            var horaTiempoMuertoEntrada = Math.trunc(moment.duration(sumaMuertosEntrada).asHours());
            var minutoTiempoMuertoEntrada = moment.duration(sumaMuertosEntrada).minutes();
            var segundoTiempoMuertoEntrada = moment.duration(sumaMuertosEntrada).seconds();
            if (horaTiempoMuertoEntrada < 10) {
                horaTiempoMuertoEntrada = "0" + horaTiempoMuertoEntrada;
            }
            if (minutoTiempoMuertoEntrada < 10) {
                minutoTiempoMuertoEntrada = "0" + minutoTiempoMuertoEntrada;
            }
            if (segundoTiempoMuertoEntrada < 10) {
                segundoTiempoMuertoEntrada = "0" + segundoTiempoMuertoEntrada;
            }
            // : TIEMPO MUERTO - SALIDA
            var horaTiempoMuertoSalida = Math.trunc(moment.duration(sumaMuertosSalida).asHours());
            var minutoTiempoMuertoSalida = moment.duration(sumaMuertosSalida).minutes();
            var segundoTiempoMuertoSalida = moment.duration(sumaMuertosSalida).seconds();
            if (horaTiempoMuertoSalida < 10) {
                horaTiempoMuertoSalida = "0" + horaTiempoMuertoSalida;
            }
            if (minutoTiempoMuertoSalida < 10) {
                minutoTiempoMuertoSalida = "0" + minutoTiempoMuertoSalida;
            }
            if (segundoTiempoMuertoSalida < 10) {
                segundoTiempoMuertoSalida = "0" + segundoTiempoMuertoSalida;
            }
            tbody += `<tr>
                        <td>${index + 1}</td>
                        <td>${data.marcaciones[index].emple_nDoc}</td>
                        <td>${data.marcaciones[index].perso_nombre} ${data.marcaciones[index].perso_apPaterno} ${data.marcaciones[index].perso_apMaterno}</td>
                        <td>${data.marcaciones[index].perso_apPaterno} ${data.marcaciones[index].perso_apMaterno} ${data.marcaciones[index].perso_nombre}</td>
                        <td>${data.marcaciones[index].perso_nombre}</td>
                        <td>${data.marcaciones[index].perso_apPaterno} ${data.marcaciones[index].perso_apMaterno}</td>
                        <td>${data.marcaciones[index].area_descripcion}</td>
                        <td class="text-center">${tardanza}</td>
                        <td class="text-center">${diasTrabajdos}</td>
                        <td class="text-center">${horaHorasNormales}:${minutoHorasNormales}:${segundoHorasNormales}</td>
                        <td class="text-center">${horaHorasNocturnas}:${minutoHorasNocturnas}:${segundoHorasNocturnas}</td>
                        <td class="text-center">${horaTiempoMuertoEntrada}:${minutoTiempoMuertoEntrada}:${segundoTiempoMuertoEntrada}</td>
                        <td class="text-center">${horaTiempoMuertoSalida}:${minutoTiempoMuertoSalida}:${segundoTiempoMuertoSalida}</td>
                        <td class="text-center">${faltas}</td>`;
            for (let i = 0; i < data.incidencias.length; i++) {
                var respuestaI = 0;
                var busqueda = data.marcaciones[index].incidencias.filter(resp => (resp.id == data.incidencias[i].id));
                if (busqueda.length != 0) {
                    busqueda.forEach(element => {
                        respuestaI = element.total;
                    });
                }
                tbody += `<td class="text-center">${respuestaI}</td>`;
            }
            tbody += `
                        <td class="text-center">${diurnas25}</td>
                        <td class="text-center">${diurnas35}</td>
                        <td class="text-center">${diurnas100}</td>
                        <td class="text-center">${nocturnas25}</td>
                        <td class="text-center">${nocturnas35}</td>
                        <td class="text-center">${nocturnas100}</td>
                    </tr>`;
        }
        $('#tbodyT').append(tbody);
        inicializarTabla();
        toggleColumnas();
        $(window).on('resize', function () {
            $("#tablaTrazabilidad").css('width', '100%');
            table.draw(false);
        });
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
// * CARGAR TABLA AL SELECCIONAR EMPLEADOS
$('#idsEmpleado').on("change", function () {
    cargarDatos();
});
// : ********************************* SELECTOR DE COLUMNAS *****************************
// * FUNCION PARA QUE NO SE CIERRE DROPDOWN
$('#dropSelector').on('hidden.bs.dropdown', function () {
    $('#menuIncidencias').hide();
});
$(document).on('click', '.allow-focus', function (e) {
    e.stopPropagation();
});
// : ***************************** MENU DE INCIDENCIAS ***********************************
function toggleI() {
    $('#menuIncidencias').toggle();
}
function menuIncidencias(id) {
    if ($('#incidencia' + id).is(":checked")) {
        dataT.api().columns('.incidencia' + id).visible(true);
    } else {
        dataT.api().columns('.incidencia' + id).visible(false);
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
    });
    var lengthChecked = $('.incidenciaPadre input[type=checkbox]').closest('.incidenciaPadre').next('ul').find('.incidenciaHijo input[type=checkbox]').length;
    $('.incidenciaPadre input[type=checkbox]').closest('.incidenciaPadre').next('ul').find('.incidenciaHijo input[type=checkbox]').each(function () {
        if (checkedIncidencias.length == lengthChecked) checkedIncidencias = [];
        checkedIncidencias.push({ "id": this.id, "valor": $(this).is(":checked") });
    });
    toggleColumnas();
}
// * FUNCIONN DE CHECKBOX DE PADRE DETALLES
$('.incidenciaPadre input[type=checkbox]').change(function () {
    $(this).closest('.incidenciaPadre').next('ul').find('.incidenciaHijo input[type=checkbox]').prop('checked', this.checked);
    var lengthChecked = $('.incidenciaPadre input[type=checkbox]').closest('.incidenciaPadre').next('ul').find('.incidenciaHijo input[type=checkbox]').length;
    $('.incidenciaPadre input[type=checkbox]').closest('.incidenciaPadre').next('ul').find('.incidenciaHijo input[type=checkbox]').each(function () {
        if (checkedIncidencias.length == lengthChecked) checkedIncidencias = [];
        checkedIncidencias.push({ "id": this.id, "valor": $(this).is(":checked") });
    });
    toggleColumnas();
});
// : ***************************** TIEMPO MUERTO ENTRADA *********************************
$('#tiempoMuertoE').on("change", function (event) {
    if (event.target.checked) {
        dataT.api().columns('.tiempoMuertoE').visible(true);
    } else {
        dataT.api().columns('.tiempoMuertoE').visible(false);
    }
});
// : ***************************** TIEMPO MUERTO SALIDA *********************************
$('#tiempoMuertoS').on("change", function (event) {
    if (event.target.checked) {
        dataT.api().columns('.tiempoMuertoS').visible(true);
    } else {
        dataT.api().columns('.tiempoMuertoS').visible(false);
    }
});
function toggleColumnas() {
    // : TIEMPO MUERTO ENTRADA
    if ($('#tiempoMuertoE').is(":checked")) {
        dataT.api().columns('.tiempoMuertoE').visible(true);
    } else {
        dataT.api().columns('.tiempoMuertoE').visible(false);
    }
    // : TIEMPO MUERTO SALIDA
    if ($('#tiempoMuertoS').is(":checked")) {
        dataT.api().columns('.tiempoMuertoS').visible(true);
    } else {
        dataT.api().columns('.tiempoMuertoS').visible(false);
    }
    chechIncidencias();
    // : INCIDENCIAS
    $('.incidenciaPadre input[type=checkbox]').closest('.incidenciaPadre').next('ul').find('.incidenciaHijo input[type=checkbox]').each(function () {
        if ($(this).is(":checked")) {
            dataT.api().columns("." + this.id).visible(true);
        } else {
            dataT.api().columns("." + this.id).visible(false);
        }
    });
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
function chechIncidencias() {
    if (checkedIncidencias.length == 0) {
        $('.incidenciaPadre input[type=checkbox]').closest('.incidenciaPadre').next('ul').find('.incidenciaHijo input[type=checkbox]').prop("checked", true);
        $('.incidenciaHijo input[type=checkbox]').closest('ul').prev('.incidenciaPadre').find('input[type=checkbox]').prop("checked", true);
    } else {
        checkedIncidencias.forEach(element => {
            $('#' + element.id).prop("checked", element.valor);
        });

    }
}
// * FINALIZACION
$('#tablaTrazabilidad tbody').on('click', 'tr', function () {
    $(this).toggleClass('selected');
});
$(window).on('resize', function () {
    $("#tablaTrazabilidad").css('width', '100%');
    table.draw(false);
});
$('#formatoC').on("change", function () {
    toggleColumnas();
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
                var estado = true;
                if (data.length != 0) {
                    return {
                        results: $.map(data, function (item, key) {
                            var children = [];
                            for (var k in item) {
                                var childItem = item[k];
                                childItem.id = item[k].id;
                                childItem.text = key + " : " + item[k].descripcion;
                                children.push(childItem);
                            }
                            if (estado) {
                                estado = false;
                                return [{
                                    id: "0",
                                    text: "Todos los empleados",
                                    selected: true
                                }, {
                                    text: key,
                                    children: children,
                                }
                                ]
                            } else {
                                return {
                                    text: key,
                                    children: children,
                                }
                            }
                        })
                    }
                } else {
                    return {
                        results: [{
                            id: "0",
                            text: "Todos los empleados",
                            selected: true
                        }]
                    }
                }
            },
            cache: true,
        },
        minimumResultsForSearch: 4
    });
    $('#empleadoPor').select2({
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
    // * CUANDO SELECIONA DENUEVO TODOS LOS EMPLEADOS
    var arrayResultado = $(this).val();
    if (arrayResultado.includes("0")) {
        var index = arrayResultado.indexOf("0");
        arrayResultado.splice(index, 1);
        arrayResultado.forEach(element => {
            $('#selectPor').find("option[value='" + element + "']").prop("selected", false);
        });
    }
    // * ************* FINALIZACION *******************
    var valueQuery = $(this).val();
    var cantidad = 0;
    if (valueQuery.length == 0) {
        return false;
    }
    $('#empleadoPor').empty();
    $.ajax({
        async: false,
        type: "GET",
        url: "/selectEmpleadoModoAP",
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
            cantidad = data.length;
            var contenidoData = ``;
            data.forEach(element => {
                contenidoData += `<option value="${element.emple_id}" selected>${element.perso_nombre} ${element.perso_apPaterno} ${element.perso_apMaterno}</option>`;
            });
            $('#empleadoPor').append(contenidoData);
            $('#cantidadE').text(cantidad + "\templeados seleccionados.");
        },
        error: function () { }
    });
});
// : MOSTRAR LA CANTIDAD DE EMPLEADOS SELECIONADOS
$('#empleadoPor').on('select2:close', function () {
    var cantidad = $('#empleadoPor').select2('data').length;
    $('#cantidadE').empty();
    $('#cantidadE').text(cantidad + "\templeados seleccionados.");
});
// : CUANDO EL SELECCIONAR POR QUEDE VACIO Y SE CIERRE
// : SIEMPRE TENER SELECCIONADO POR EMPLEADO
$('#selectPor').on('select2:closing', function () {
    if ($(this).val().length == 0) {
        $(this).val(0).trigger("change");
    }
});
// : CUANDO SELECIONE OTRA OPCION QUE NO SEA TODOS LOS EMPLEADOS
// : SE DESACTIVA TODOS LOS EMPLEADOS
$('#selectPor').on('select2:selecting', function () {
    var arrayResultado = $(this).val();
    if (arrayResultado.includes("0")) {
        $('#selectPor option[value="0"]').prop("selected", false);
    }
});