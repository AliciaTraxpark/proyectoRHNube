$('#graficaReporteMensual').hide();
$('#fechaMensual').datetimepicker({
    language: 'es',
    format: 'MM - yyyy',
    startView: 3,
    minView: 3,
    pickTime: false,
    autoclose: true,
    todayBtn: false,
    pickerPosition: "bottom-left"
});
var notify = $.notifyDefaults({
    icon_type: 'image',
    newest_on_top: true,
    delay: 4000,
    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
        '<span data-notify="title">{1}</span> ' +
        '<span style="color:#8a6d3b" data-notify="message">{2}</span>' +
        '</div>'
});
var razonSocial = {};
var direccion = {};
var ruc = {};
$(function () {
    $("#ReporteMensual").DataTable({
        "searching": false,
        "scrollX": true,
        retrieve: true,
        "ordering": false,
        "pageLength": 10,
        "autoWidth": false,
        "lengthChange": false,
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
        },
    });
});

function acumular60(suma, acumulado) {
    if (suma > 60) {
        suma = suma - 60;
        acumulado += 1;
        acumular60(suma, acumulado);
    }

    if (suma == 60) {
        suma = 0;
        acumulado += 1;
        acumular60(suma, acumulado);
    }
    return [suma, acumulado];
}

function sumarHora(a, b) {
    if (!a) return b;
    let resultado = [];
    a = a.split(":").reverse();
    b = b.split(":").reverse();
    let acumulado = 0;
    let sumaAcumulado;
    let tiempo;
    let suma = parseInt(a[0]) + parseInt(b[0]);
    sumaAcumulado = acumular60(suma, acumulado);
    tiempo = (sumaAcumulado[0] < 10) ? '0' + sumaAcumulado[0] : sumaAcumulado[0];
    resultado.push(tiempo);
    suma = parseInt(a[1]) + parseInt(b[1]) + sumaAcumulado[1];
    acumulado = 0;
    sumaAcumulado = acumular60(suma, acumulado);
    tiempo = (sumaAcumulado[0] < 10) ? '0' + sumaAcumulado[0] : sumaAcumulado[0];
    resultado.push(tiempo);
    suma = parseInt(a[2]) + parseInt(b[2]) + sumaAcumulado[1];
    tiempo = (suma < 10) ? '0' + suma : suma;
    resultado.push(tiempo);
    resultado.reverse();
    return resultado.join(":");
}

function calcularPromedio(a, b) {
    if (!a) return b;
    let resultado = [];
    let acumulado = 0;
    let promedio = 0;
    if (a != 0) {
        acumulado = acumulado + 1;
    }
    let suma = parseFloat(a) + parseFloat(b);
    promedio = suma;
    if (acumulado != 0) {
        promedio = suma / acumulado;
    }
    resultado.push(promedio);
    return resultado;
}

function totalContar(a, b) {
    if (!a) return b;
    let resultado = [];
    let suma = 0;
    suma += parseInt(a) + parseInt(b);
    resultado.push(suma);
    return resultado;
}

function sumarTotales(a, b) {
    if (!a) return b;
    let resultado = [];
    let suma = 0;
    suma += parseFloat(a) + parseFloat(b);
    resultado.push(suma);
    return resultado;
}
var grafico = {};
var table = {};
var tableActividad = {};
var datos = {};

function onSelectFechasMensual() {
    var fecha = $('#fechaMensual').val();
    var area = $('#area').val();
    var empleadoL = $('#empleadoL').val();
    if ($.fn.DataTable.isDataTable("#ReporteMensual")) {
        $('#ReporteMensual').DataTable().destroy();
    }
    $('#empleadoMensual').empty();
    $('#diasMensual').empty();
    $('#VacioImg').empty();
    $("#myChartMensual").show();
    if (grafico.config != undefined) grafico.destroy();
    $.ajax({
        async: false,
        url: "reporte/empleado",
        method: "GET",
        data: {
            fecha: fecha,
            area: area,
            empleadoL: empleadoL
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
            razonSocial = data.organizacion.razonSocial;
            direccion = data.organizacion.direccion;
            ruc = data.organizacion.ruc;
            datos = data.respuesta;
            $('#VacioImg').hide();
            tablaEnVista();
        },
        error: function (data) { }
    })
}
function sinActividadD() {
    if ($.fn.DataTable.isDataTable("#ReporteMensual")) {
        $('#ReporteMensual').DataTable().destroy();
    }
    $('#empleadoMensual').empty();
    $('#diasMensual').empty();
    $('#VacioImg').empty();
    $("#myChartMensual").show();
    if (grafico.config != undefined) grafico.destroy();
    if (datos.length > 0) {
        $('#VacioImg').hide();
        var nombre = [];
        var horas = [];
        var html_tr = "";
        var html_trD = "<tr><th>#</th><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</>";
        for (var i = 0; i < datos.length; i++) {
            html_tr += '<tr><td>' + (i + 1) + '</td><td>' + datos[i].nombre + ' ' + datos[i].apPaterno + ' ' + datos[i].apMaterno + '</td>';
            nombre.push(datos[i].nombre.split('')[0] + datos[i].apPaterno.split('')[0] + datos[i].apMaterno.split('')[0]);
            var total = datos[i].horas.reduce(function (a, b) {
                return sumarHora(a, b);
            });
            var sumaATotal = datos[i].sumaActividad.reduce(function (a, b) {
                return sumarTotales(a, b);
            });

            var sumaRTotal = datos[i].sumaRango.reduce(function (a, b) {
                return sumarTotales(a, b);
            });
            for (let j = 0; j < datos[i].horas.length; j++) {
                // TABLA DEFAULT
                html_tr += '<td>' + datos[i].horas[j] + '</td>';
            }
            if (sumaRTotal[0] != 0) {
                var p1 = ((sumaATotal[0] / sumaRTotal[0]) * 100).toFixed(2);
                var sumaP = p1;
            } else {
                var sumaP = 0;
            }
            // TABLA DEFAULT
            html_tr += '<td>' + total + '</td>';
            html_tr += '<td>' + sumaP + '%' + '</td>';
            var decimal = parseFloat(sumaP);
            horas.push(decimal);
            html_tr += '</tr>';
        }
        for (var m = 0; m < datos[0].fechaF.length; m++) {
            var momentValue = moment(datos[0].fechaF[m]);
            momentValue.toDate();
            momentValue.format("ddd DD/MM");
            // TABLA DEFAULT
            html_trD += '<th>' + momentValue.format("ddd DD/MM") + '</th>';
        }
        // TABLA DEFAULT
        html_trD += '<th>TOTAL</th>';
        html_trD += '<th>ACTIV.</th></tr>';
        // TABLA DEFAULT
        $("#diasMensual").html(html_trD);
        $("#empleadoMensual").html(html_tr);

        table = $("#ReporteMensual").DataTable({
            "searching": false,
            "scrollX": true,
            retrieve: true,
            "ordering": false,
            "autoWidth": true,
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
                    var r1 = Addrow(1, [{ k: 'A', v: 'MODO CONTROL REMOTO - TIEMPO POR MES', s: 2 }]);
                    var r2 = Addrow(2, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                    var r3 = Addrow(3, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                    var r4 = Addrow(4, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                    var r5 = Addrow(5, [{ k: 'A', v: 'Fecha:', s: 2 }, { k: 'C', v: jsDate, s: 0 }]);
                    sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + sheet.childNodes[0].childNodes[1].innerHTML;
                },
                sheetName: 'MODO CONTROL REMOTO - TIEMPO POR MES',
                title: 'MODO CONTROL REMOTO - TIEMPO POR MES',
                autoFilter: false,
                exportOptions: {
                    format: {
                        body: function (data, row, column, node) {
                            var cont = $.trim($(node).text());
                            return $.trim(cont);
                        }
                    }
                },
            }, {
                extend: "pdfHtml5",
                className: 'btn btn-sm mt-1',
                text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                orientation: 'landscape',
                pageSize: 'A1',
                exportOptions: {
                    columns: ":visible",
                    orthogonal: 'export'
                },
                title: 'MODO CONTROL REMOTO - TIEMPO POR MES',
                customize: function (doc) {
                    var bodyCompleto = [];
                    doc.content[1].table.body.forEach(function (line, i) {
                        var bodyNuevo = [];
                        if (i >= 1) {
                            line.forEach(element => {
                                var textOriginal = element.text;
                                var cambiar = $.trim(textOriginal);
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
                    doc.content[1].margin = [60, 0, 60, 0];
                    var colCount = new Array();
                    var tr = $('#ReporteMensual tbody tr:first-child');
                    var trWidth = $(tr).width();
                    //* WIDTH DE COLUMNAS 
                    var length = $('#ReporteMensual tbody tr:first-child td').length;
                    $('#ReporteMensual').find('tbody tr:first-child td').each(function () {
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
                    doc.content[1].table.widths = colCount;
                    doc.content.splice(0, 1);
                    var objLayout = {};
                    objLayout['hLineWidth'] = function (i) { return .2; };
                    objLayout['vLineWidth'] = function (i) { return .2; };
                    objLayout['hLineColor'] = function (i) { return '#aaa'; };
                    objLayout['vLineColor'] = function (i) { return '#aaa'; };
                    doc.content[0].layout = objLayout;
                    doc.content[0].table.body = bodyCompleto;
                    var now = new Date();
                    var jsDate = now.getDate() + "/" + (now.getMonth() + 1) + "/" + now.getFullYear();
                    doc["header"] = function () {
                        return {
                            columns: [
                                {
                                    alignment: 'left',
                                    italics: false,
                                    text: [
                                        { text: '\nMODO CONTROL REMOTO - TIEMPO POR MES', bold: true },
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
            paging: true
        });
        var options = {
            series: [{
                name: 'actividad',
                data: horas
            }],
            chart: {
                height: 350,
                type: 'bar',
                zoom: {
                    enabled: true
                }
            },
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    distributed: true
                }
            },
            colors: ['#00005c'],
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false
            },
            xaxis: {
                categories: nombre,
                labels: {
                    style: {
                        color: '#000000',
                        fontSize: '11px'
                    }
                },
                axisBorder: {
                    show: true,
                    color: '#000000',
                    height: 1,
                    width: '100%',
                    offsetX: 0,
                    offsetY: 0
                },
                axisTicks: {
                    show: false
                },
                title: {
                    text: "Empleados",
                    offsetX: 0,
                    offsetY: -2,
                    style: {
                        color: '#000000',
                    }
                },
                crosshairs: {
                    fill: {
                        type: 'gradient',
                        gradient: {
                            colorFrom: '#D8E3F0',
                            colorTo: '#BED1E6',
                            stops: [0, 100],
                            opacityFrom: 0.4,
                            opacityTo: 0.5,
                        }
                    }
                },
            },
            yaxis: {
                title: {
                    text: 'Actividad'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " %"
                    }
                }
            },
            responsive: [
                {
                    breakpoint: 767.98,
                    options: {
                        chart: {
                            height: 350,
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: true,
                            }
                        }
                    }
                }
            ]
        };

        grafico = new ApexCharts(document.querySelector("#myChartMensual"), options);
        grafico.render();
    } else {
        $.notify({
            message: "No se encontraron datos.",
            icon: 'admin/images/warning.svg'
        });
        var html_trD = "<tr><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
        html_trAD += '<th>LUN.</th>';
        html_trAD += '<th>MAR.</th>';
        html_trAD += '<th>MIÉ.</th>';
        html_trAD += '<th>JUE.</th>';
        html_trAD += '<th>VIE.</th>';
        html_trAD += '<th>SÁB.</th>';
        html_trAD += '<th>TOTAL</th>';
        html_trD += '<th>TOTAL</th>';
        html_trD += '<th>ACTIV.</th></tr>';
        // TABLA DEFAULT
        $('#diasMensual').html(html_trD);
    }
}
function conActividadD() {
    if ($.fn.DataTable.isDataTable("#actividadDM")) {
        $('#actividadDM').DataTable().destroy();
    }
    $('#diasActvidad').empty();
    $('#empleadoActividad').empty();
    $('#VacioImg').empty();
    $("#myChartMensual").show();
    if (grafico.config != undefined) grafico.destroy();
    if (datos.length > 0) {
        $('#VacioImg').hide();
        var nombre = [];
        var horas = [];
        var html_trA = "";
        var html_trAD = "<tr><th>#</th><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
        for (var i = 0; i < datos.length; i++) {
            html_trA += '<tr><td>' + (i + 1) + '</td><td>' + datos[i].nombre + ' ' + datos[i].apPaterno + ' ' + datos[i].apMaterno + '</td>';
            nombre.push(datos[i].nombre.split('')[0] + datos[i].apPaterno.split('')[0] + datos[i].apMaterno.split('')[0]);
            var total = datos[i].horas.reduce(function (a, b) {
                return sumarHora(a, b);
            });
            var sumaATotal = datos[i].sumaActividad.reduce(function (a, b) {
                return sumarTotales(a, b);
            });

            var sumaRTotal = datos[i].sumaRango.reduce(function (a, b) {
                return sumarTotales(a, b);
            });
            for (let j = 0; j < datos[i].horas.length; j++) {
                // TABLA CON ACTIVIDAD DIARIA
                html_trA += '<td>' + datos[i].horas[j] + '</td>';
                var sumaA = datos[i].sumaActividad[j];
                var sumaR = datos[i].sumaRango[j];
                if (sumaR != 0) {
                    var promedioD = ((sumaA / sumaR) * 100).toFixed(2);
                    html_trA += '<td>' + promedioD + '%' + '</td>';
                } else {
                    var promedioD = (0).toFixed(2);
                    html_trA += '<td>' + promedioD + '%' + '</td>';
                }
            }
            if (sumaRTotal[0] != 0) {
                var p1 = ((sumaATotal[0] / sumaRTotal[0]) * 100).toFixed(2);
                var sumaP = p1;
            } else {
                var sumaP = 0;
            }
            html_trA += '<td>' + total + '</td>';
            html_trA += '<td>' + sumaP + '%' + '</td>';
            var decimal = parseFloat(sumaP);
            horas.push(decimal);
            html_trA += '</tr>';
        }
        for (var m = 0; m < datos[0].fechaF.length; m++) {
            var momentValue = moment(datos[0].fechaF[m]);
            momentValue.toDate();
            momentValue.format("ddd DD/MM");
            // TABLA CON ACTIVIDAD DIARIA
            html_trAD += '<th>' + momentValue.format("ddd DD/MM") + '</th>';
            html_trAD += '<th class="text-center"><img src="landing/images/velocimetro (1).svg" class="mr-2" height="17"/></th>';
        }
        html_trAD += '<th>TOTAL</th>';
        html_trAD += '<th>ACTIV.</th></tr>';
        // TABLA CON ACTIVIDAD DIARIA
        $('#diasActvidad').html(html_trAD);
        $('#empleadoActividad').html(html_trA);

        $("#actividadDM").DataTable({
            "searching": false,
            "scrollX": true,
            retrieve: true,
            "ordering": false,
            "autoWidth": true,
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
                    var r1 = Addrow(1, [{ k: 'A', v: 'MODO CONTROL REMOTO - TIEMPO POR MES', s: 2 }]);
                    var r2 = Addrow(2, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                    var r3 = Addrow(3, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                    var r4 = Addrow(4, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                    var r5 = Addrow(5, [{ k: 'A', v: 'Fecha:', s: 2 }, { k: 'C', v: jsDate, s: 0 }]);
                    sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + sheet.childNodes[0].childNodes[1].innerHTML;
                },
                sheetName: 'MODO CONTROL REMOTO - TIEMPO POR MES',
                title: 'MODO CONTROL REMOTO - TIEMPO POR MES',
                autoFilter: false,
                exportOptions: {
                    format: {
                        body: function (data, row, column, node) {
                            var cont = $.trim($(node).text());
                            return $.trim(cont);
                        }
                    }
                },
            }, {
                extend: "pdfHtml5",
                className: 'btn btn-sm mt-1',
                text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                orientation: 'landscape',
                pageSize: 'A1',
                exportOptions: {
                    columns: ":visible",
                    orthogonal: 'export'
                },
                title: 'MODO CONTROL REMOTO - TIEMPO POR MES',
                customize: function (doc) {
                    var bodyCompleto = [];
                    doc.content[1].table.body.forEach(function (line, i) {
                        var bodyNuevo = [];
                        if (i >= 1) {
                            line.forEach(element => {
                                var textOriginal = element.text;
                                var cambiar = $.trim(textOriginal);
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
                    doc.content[1].margin = [60, 0, 60, 0];
                    var colCount = new Array();
                    var tr = $('#actividadDM tbody tr:first-child');
                    var trWidth = $(tr).width();
                    //* WIDTH DE COLUMNAS 
                    var length = $('#actividadDM tbody tr:first-child td').length;
                    $("#actividadDM").find('tbody tr:first-child td').each(function () {
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
                    doc.content[1].table.widths = colCount;
                    doc.content.splice(0, 1);
                    var objLayout = {};
                    objLayout['hLineWidth'] = function (i) { return .2; };
                    objLayout['vLineWidth'] = function (i) { return .2; };
                    objLayout['hLineColor'] = function (i) { return '#aaa'; };
                    objLayout['vLineColor'] = function (i) { return '#aaa'; };
                    doc.content[0].layout = objLayout;
                    doc.content[0].table.body = bodyCompleto;
                    var now = new Date();
                    var jsDate = now.getDate() + "/" + (now.getMonth() + 1) + "/" + now.getFullYear();
                    doc["header"] = function () {
                        return {
                            columns: [
                                {
                                    alignment: 'left',
                                    italics: false,
                                    text: [
                                        { text: '\nMODO CONTROL REMOTO - TIEMPO POR MES', bold: true },
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
                setTimeout(function () { $("#actividadDM").DataTable().draw(); }, 200);
            }
        });
        $(window).on('resize', function () {
            $("#actividadDM").css('width', '100%');
            table.draw(true);
        });
        var options = {
            series: [{
                name: 'actividad',
                data: horas
            }],
            chart: {
                height: 350,
                type: 'bar',
                zoom: {
                    enabled: true
                }
            },
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    distributed: true
                }
            },
            colors: ['#00005c'],
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false
            },
            xaxis: {
                categories: nombre,
                labels: {
                    style: {
                        color: '#000000',
                        fontSize: '11px'
                    }
                },
                axisBorder: {
                    show: true,
                    color: '#000000',
                    height: 1,
                    width: '100%',
                    offsetX: 0,
                    offsetY: 0
                },
                axisTicks: {
                    show: false
                },
                title: {
                    text: "Empleados",
                    offsetX: 0,
                    offsetY: -2,
                    style: {
                        color: '#000000',
                    }
                },
                crosshairs: {
                    fill: {
                        type: 'gradient',
                        gradient: {
                            colorFrom: '#D8E3F0',
                            colorTo: '#BED1E6',
                            stops: [0, 100],
                            opacityFrom: 0.4,
                            opacityTo: 0.5,
                        }
                    }
                },
            },
            yaxis: {
                title: {
                    text: 'Actividad'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " %"
                    }
                }
            },
            responsive: [
                {
                    breakpoint: 767.98,
                    options: {
                        chart: {
                            height: 350,
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: true,
                            }
                        }
                    }
                }
            ]
        };

        grafico = new ApexCharts(document.querySelector("#myChartMensual"), options);
        grafico.render();
    } else {
        $.notify({
            message: "No se encontraron datos.",
            icon: 'admin/images/warning.svg'
        });
        var html_trD = "<tr><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
        html_trAD += '<th>LUN.</th>';
        html_trAD += '<th>MAR.</th>';
        html_trAD += '<th>MIÉ.</th>';
        html_trAD += '<th>JUE.</th>';
        html_trAD += '<th>VIE.</th>';
        html_trAD += '<th>SÁB.</th>';
        html_trAD += '<th>TOTAL</th>';
        html_trD += '<th>TOTAL</th>';
        html_trD += '<th>ACTIV.</th></tr>';
        // TABLA DEFAULT
        $('#diasActvidad').html(html_trD);
    }
}
function changeFecha() {
    dato = $('#fechaMensual').val();
    value = moment(dato, ["MMMM-YYYY", "MMM-YYYY", "MM-YYYY"]).format("MM-YYYY");
    firstDate = moment(value, 'MM-YYYY').startOf('month').format('YYYY-MM-DD');
    lastDate = moment(value, 'MM-YYYY').endOf('month').format('YYYY-MM-DD');
    $('#fechaMensual').val(firstDate + "   a   " + lastDate);
    onSelectFechasMensual();
    $('#fechaMensual').val(dato)
}
function fechaDefecto() {
    dato = $('#fechaMensual').val();
    value = moment(dato, ["MMMM-YYYY", "MMM-YYYY", "MM-YYYY"]).format("MM-YYYY");
    firstDate = moment(value, 'MM-YYYY').startOf('month').format('YYYY-MM-DD');
    lastDate = moment(value, 'MM-YYYY').endOf('month').format('YYYY-MM-DD');
    $('#fechaMensual').val(firstDate + "   a   " + lastDate);
    onSelectFechasMensual();
    $('#fechaMensual').val(dato);
}
$(function () {
    $('#area').select2({
        placeholder: 'Seleccionar áreas'
    });
    $('#empleadoL').select2({
        placeholder: 'Seleccionar empleados',
        language: "es"
    });
    $('#area').on("change", function (e) {
        fechaDefecto();
        var area = $(this).val();
        $('#empleadoL').empty();
        $.ajax({
            async: false,
            url: "/empleadosRep",
            method: "GET",
            data: {
                area: area,
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
                var select = "";
                for (let i = 0; i < data.length; i++) {
                    select += `<option value="${data[i].emple_id}">${data[i].nombre} ${data[i].apPaterno} ${data[i].apMaterno}</option>`
                }
                $('#empleadoL').append(select);
            },
            error: function () { }
        });
    });
    $('#empleadoL').on("change", function (e) {
        fechaDefecto();
    });
});
$(function () {
    var hoy = moment().format("MMMM - YYYY");
    $('#fechaMensual').val(hoy);
});
function buscarReporte() {
    changeFecha();
    $('#busquedaP').show();
    $('#busquedaA').show();
}
function mostrarGraficaMensual() {
    $('#VacioImg').toggle();
    $('#graficaReporteMensual').toggle();
}

function cambiarTabla() {
    $("#customSwitchD").on("change.bootstrapSwitch", function (
        event
    ) {
        if (event.target.checked == true) {
            conActividadD();
            $('#tablaConActividadD').show();
            $('#tablaSinActividadD').hide();
        } else {
            sinActividadD();
            $('#tablaConActividadD').hide();
            $('#tablaSinActividadD').show();
        }
    });
}
function tablaEnVista() {
    if ($("#customSwitchD").is(":checked")) {
        conActividadD();
        $('#tablaConActividadD').show();
        $('#tablaSinActividadD').hide();
    } else {
        sinActividadD();
        $('#tablaConActividadD').hide();
        $('#tablaSinActividadD').show();
    }
}
$(function () {
    $('table thead th.next').empty();
    var thIconoN = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 17 17">\
    <g></g><path d="M13.207 8.472l-7.854 7.854-0.707-0.707 7.146-7.146-7.146-7.148 0.707-0.707 7.854 7.854z"></path></svg>';
    $('table thead th.next').html(thIconoN);
    $('table thead th.prev').empty();
    var thIconoP = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 17 17">\
    <g></g><path d="M5.207 8.471l7.146 7.147-0.707 0.707-7.853-7.854 7.854-7.853 0.707 0.707-7.147 7.146z"></path></svg>';
    $('table thead th.prev').html(thIconoP);
});