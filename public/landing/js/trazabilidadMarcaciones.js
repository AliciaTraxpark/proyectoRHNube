//* FECHA
var fechaValue = $("#fechaTrazabilidad").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "j M",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: true,
    conjunction: " a ",
    minRange: 1,
    onChange: function (selectedDates) {
        console.log("ingreso");
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
function inicializarTabla() {
    table = $("#tablaTrazabilidad").DataTable({
        "searching": false,
        "scrollX": true,
        "ordering": false,
        "autoWidth": false,
        "lengthChange": true,
        "lengthMenu": [10, 25, 50, 75, 100],
        scrollCollapse: false,
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
            }, {
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
                    var tr = $('#tablaTrazabilidad tbody tr:first-child');
                    var trWidth = $(tr).width();
                    $('#tablaTrazabilidad').find('tbody tr:first-child td').each(function () {
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
            }],
        paging: true,
        initComplete: function () {
            dataT = this;
            setTimeout(function () {
                $("#tablaTrazabilidad").DataTable().draw();
            }, 1);
        }
    }).draw();
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
    cargarDatos();
    inicializarTabla();
});
// * OBTENER DATA
function cargarDatos() {
    var fechaI = $('#fechaInicio').val();
    var fechaF = $('#fechaFin').val();
    var idsEmpleado = $('#idsEmpleado').val();
    console.log(idsEmpleado);
    $.ajax({
        async: false,
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
        success: function (data) {
            $('#tbodyT').empty();
            var tbody = "";
            for (let index = 0; index < data.length; index++) {
                var tardanza = 0;
                var diasTrabajdos = 0;
                // : HORAS NORMALES
                var horasNormales = moment("00:00:00", "HH:mm:ss");
                var diurnas25 = 0;
                var diurnas35 = 0;
                var diurnas100 = 0;
                // : FINALIZACION
                var descansoM = 0;
                var faltas = 0;
                var fi = 0;
                var fj = 0;
                var per = 0;
                var sme = 0;
                var suspension = 0;
                var vacaciones = 0;
                // : HORAS NOCTURNAS
                var horasNocturnas = moment("00:00:00", "HH:mm:ss");
                var nocturnas25 = 0;
                var nocturnas35 = 0;
                var nocturnas100 = 0;
                // : ARRAY FECHA
                var arrayFecha = [];
                // : RECORRER DATA PARA CALCULAR DATOS
                for (let item = 0; item < data[index].data.length; item++) {
                    var dataCompleta = data[index].data[item];
                    // ! *************************** NORMAL **************************************
                    if (dataCompleta["normal"] != undefined) {
                        dataCompleta["normal"].forEach(element => {
                            if (element.idHorario != 0) {
                                // : FALTAS
                                if (element.totalT == "00:00:00" && element.entrada == null) {
                                    if (dataCompleta["incidencias"] != undefined) {
                                        if (dataCompleta["incidencias"].length == 0) {
                                            faltas++;
                                        }
                                    }
                                } else {
                                    // : TARDANZA
                                    if (element.entrada != 0) {
                                        var horarioInicio = moment(element.horarioIni).add({ "minutes": element.toleranciaI });
                                        var entrada = moment(element.entrada);
                                        if (!entrada.isSameOrBefore(horarioInicio)) {
                                            tardanza++;
                                        }
                                    }
                                    // : SOBRE TIEMPO NORMAL
                                    var tiempoTrabajado = moment(element.totalT, "HH:mm:ss");
                                    var horasObligadas = moment(element.horasObligadas, "HH:mm:ss");
                                    if (tiempoTrabajado.isAfter(horasObligadas)) {
                                        var sobreTiempo = tiempoTrabajado - horasObligadas;
                                        var horasSobreTiempo = Math.trunc(moment.duration(sobreTiempo).asHours());
                                        var minutosSobreTiempo = moment.duration(sobreTiempo).minutes();
                                        var segundosSobreTiempo = moment.duration(sobreTiempo).seconds();
                                        var tiempoSobreT = moment({ "hours": horasSobreTiempo, "minutes": minutosSobreTiempo, "seconds": segundosSobreTiempo }).format("HH:mm:ss");
                                        var tiempoSobreMoment = moment(tiempoSobreT, "HH:mm:ss");
                                        var tiempoSobrante = {};
                                        // : TIEMPO EXTRAS EL 25%
                                        if (tiempoSobreMoment.isAfter(moment("02:00:00", "HH:mm:ss"))) {
                                            diurnas25++;
                                            var restaDe25 = tiempoSobreMoment - moment("02:00:00", "HH:mm:ss");
                                            var horasDe25 = Math.trunc(moment.duration(restaDe25).asHours());
                                            var minutosDe25 = moment.duration(restaDe25).minutes();
                                            var segundosDe25 = moment.duration(restaDe25).seconds();
                                            tiempoSobrante = moment({ "hours": horasDe25, "minutes": minutosDe25, "seconds": segundosDe25 }).format("HH:mm:ss");
                                            if (moment(tiempoSobrante, "HH:mm:ss").isAfter(moment("02:00:00", "HH:mm:ss"))) {
                                                diurnas35++;
                                                var restaDe35 = moment(tiempoSobrante, "HH:mm:ss") - moment("02:00:00", "HH:mm:ss");
                                                var horasDe35 = Math.trunc(moment.duration(restaDe35).asHours());
                                                var minutosDe35 = moment.duration(restaDe35).minutes();
                                                var segundosDe35 = moment.duration(restaDe35).seconds();
                                                tiempoSobrante = moment({ "hours": horasDe35, "minutes": minutosDe35, "seconds": segundosDe35 }).format("HH:mm:ss");
                                                if (moment(tiempoSobrante, "HH:mm:ss").isAfter(moment("00:00:00", "HH:mm:ss"))) {
                                                    diurnas100++;
                                                }
                                            } else {
                                                if (moment(tiempoSobrante, "HH:mm:ss").isAfter(moment("00:00:00", "HH:mm:ss"))) {
                                                    diurnas35++;
                                                }
                                            }
                                        } else {
                                            diurnas25++;
                                        }
                                    }
                                }
                                if (element.entrada != null) {
                                    // : DIAS TRABAJADOS
                                    var fecha = moment(element.horarioIni).format("YYYY-MM-DD");
                                    if (!arrayFecha.includes(fecha)) {
                                        arrayFecha.push(fecha);
                                        diasTrabajdos++;
                                    }
                                }
                            }
                            // : DIAS TRABAJADOS
                            if (element.entrada != null) {
                                if (element.entrada != 0) {
                                    var fecha = moment(element.entrada).format("YYYY-MM-DD");
                                    if (!arrayFecha.includes(fecha)) {
                                        arrayFecha.push(fecha);
                                        diasTrabajdos++;
                                    }
                                } else {
                                    if (element.salida != 0) {
                                        var fecha = moment(element.salida).format("YYYY-MM-DD");
                                        if (!arrayFecha.includes(fecha)) {
                                            arrayFecha.push(fecha);
                                            diasTrabajdos++;
                                        }
                                    }
                                }
                            }
                            // : HORAS TRABAJADOS NORMALES
                            var horaT = moment.duration(element.totalT);
                            var sumaDeTiempos = moment.duration(horaT);
                            var horasTotal = Math.trunc(moment.duration(sumaDeTiempos).asHours());
                            var minutosTotal = moment.duration(sumaDeTiempos).minutes();
                            var segundosTotal = moment.duration(sumaDeTiempos).seconds();
                            horasNormales = horasNormales.add({ "hours": horasTotal, "minutes": minutosTotal, "seconds": segundosTotal });
                        });
                    }
                    // ! ***************************** NOCTURNO *******************************
                    if (dataCompleta["nocturno"] != undefined) {
                        dataCompleta["nocturno"].forEach(element => {
                            if (element.idHorario != 0) {
                                // : FALTAS
                                if (element.totalT == "00:00:00" && element.entrada == null) {
                                    if (dataCompleta["incidencias"] != undefined) {
                                        if (dataCompleta["incidencias"].length == 0) {
                                            faltas++;
                                        }
                                    }
                                } else {
                                    // : TARDANZA
                                    if (element.entrada != 0) {
                                        var horarioInicio = moment(element.horarioIni).add({ "minutes": element.toleranciaI });
                                        var entrada = moment(element.entrada);
                                        if (!entrada.isSameOrBefore(horarioInicio)) {
                                            tardanza++;
                                        }
                                    }
                                    // : SOBRE TIEMPO NOCTURNO
                                    var tiempoTrabajado = moment(element.totalT, "HH:mm:ss");
                                    var horasObligadas = moment(element.horasObligadas, "HH:mm:ss");
                                    if (tiempoTrabajado.isAfter(horasObligadas)) {
                                        var sobreTiempo = tiempoTrabajado - horasObligadas;
                                        var horasSobreTiempo = Math.trunc(moment.duration(sobreTiempo).asHours());
                                        var minutosSobreTiempo = moment.duration(sobreTiempo).minutes();
                                        var segundosSobreTiempo = moment.duration(sobreTiempo).seconds();
                                        var tiempoSobreT = moment({ "hours": horasSobreTiempo, "minutes": minutosSobreTiempo, "seconds": segundosSobreTiempo }).format("HH:mm:ss");
                                        var tiempoSobreMoment = moment(tiempoSobreT, "HH:mm:ss");
                                        var tiempoSobrante = {};
                                        // : TIEMPO EXTRAS EL 25%
                                        if (tiempoSobreMoment.isAfter(moment("02:00:00", "HH:mm:ss"))) {
                                            nocturnas25++;
                                            var restaDe25 = tiempoSobreMoment - moment("02:00:00", "HH:mm:ss");
                                            var horasDe25 = Math.trunc(moment.duration(restaDe25).asHours());
                                            var minutosDe25 = moment.duration(restaDe25).minutes();
                                            var segundosDe25 = moment.duration(restaDe25).seconds();
                                            tiempoSobrante = moment({ "hours": horasDe25, "minutes": minutosDe25, "seconds": segundosDe25 }).format("HH:mm:ss");
                                            if (moment(tiempoSobrante, "HH:mm:ss").isAfter(moment("02:00:00", "HH:mm:ss"))) {
                                                nocturnas35++;
                                                var restaDe35 = moment(tiempoSobrante, "HH:mm:ss") - moment("02:00:00", "HH:mm:ss");
                                                var horasDe35 = Math.trunc(moment.duration(restaDe35).asHours());
                                                var minutosDe35 = moment.duration(restaDe35).minutes();
                                                var segundosDe35 = moment.duration(restaDe35).seconds();
                                                tiempoSobrante = moment({ "hours": horasDe35, "minutes": minutosDe35, "seconds": segundosDe35 }).format("HH:mm:ss");
                                                if (moment(tiempoSobrante, "HH:mm:ss").isAfter(moment("00:00:00", "HH:mm:ss"))) {
                                                    nocturnas100++;
                                                }
                                            } else {
                                                if (moment(tiempoSobrante, "HH:mm:ss").isAfter(moment("00:00:00", "HH:mm:ss"))) {
                                                    nocturnas35++;
                                                }
                                            }
                                        } else {
                                            nocturnas25++;
                                        }
                                    }
                                }
                            }
                            // : DIAS TRABAJADOS
                            if (element.entrada != null) {
                                if (element.entrada != 0) {
                                    var fecha = moment(element.entrada).format("YYYY-MM-DD");
                                    if (!arrayFecha.includes(fecha)) {
                                        arrayFecha.push(fecha);
                                        diasTrabajdos++;
                                    }
                                } else {
                                    if (element.salida != 0) {
                                        var fecha = moment(element.salida).format("YYYY-MM-DD");
                                        if (!arrayFecha.includes(fecha)) {
                                            arrayFecha.push(fecha);
                                            diasTrabajdos++;
                                        }
                                    }
                                }
                            }
                            // : HORAS TRABAJADOS NOCTURNAS
                            var horaT = moment.duration(element.totalT);
                            var sumaDeTiempos = moment.duration(horaT);
                            var horasTotal = Math.trunc(moment.duration(sumaDeTiempos).asHours());
                            var minutosTotal = moment.duration(sumaDeTiempos).minutes();
                            var segundosTotal = moment.duration(sumaDeTiempos).seconds();
                            horasNocturnas = horasNocturnas.add({ "hours": horasTotal, "minutes": minutosTotal, "seconds": segundosTotal });
                        });
                    }
                }
                tbody += `<tr>
                            <td>${index + 1}</td>
                            <td>${data[index].emple_nDoc}</td>
                            <td>${data[index].nombres_apellidos}</td>
                            <td>${data[index].area_descripcion}</td>
                            <td class="text-center">${tardanza}</td>
                            <td class="text-center">${diasTrabajdos}</td>
                            <td class="text-center">${horasNormales.format("HH:mm:ss")}</td>
                            <td class="text-center">${horasNocturnas.format("HH:mm:ss")}</td>
                            <td class="text-center">${descansoM}</td>
                            <td class="text-center">${faltas}</td>
                            <td class="text-center">${fi}</td>
                            <td class="text-center">${fj}</td>
                            <td class="text-center">${per}</td>
                            <td class="text-center">${sme}</td>
                            <td class="text-center">${suspension}</td>
                            <td class="text-center">${vacaciones}</td>
                            <td class="text-center">${diurnas25}</td>
                            <td class="text-center">${diurnas35}</td>
                            <td class="text-center">${diurnas100}</td>
                            <td class="text-center">${nocturnas25}</td>
                            <td class="text-center">${nocturnas35}</td>
                            <td class="text-center">${nocturnas100}</td>
                </tr>`;
            }
            $('#tbodyT').append(tbody);
            $(window).on('resize', function () {
                $("#tablaTrazabilidad").css('width', '100%');
                table.draw(false);
            });
        },
        error: function (data) { }
    })
}