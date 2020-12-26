$('#graficaReporte').hide();
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "single",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "D, j F",
    locale: "es",
    wrap: true,
    allowInput: true,
    disableMobile: "true",
    "plugins": [new weekSelect({})],
    "onChange": [function () {
        // extract the week number
        // note: "this" is bound to the flatpickr instance
        const weekNumber = this.selectedDates[0]
            ? this.config.getWeek(this.selectedDates[0])
            : null;
    }]
});
$(function () {
    f = moment().format("YYYY-MM-DD");
    fechaValue.setDate(f);
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
$(function () {
    $("#Reporte").DataTable({
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
        }
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
function onSelectFechas() {
    var fecha = $('#fecha').val();
    var area = $('#area').val();
    var empleadoL = $('#empleadoL').val();
    if ($.fn.DataTable.isDataTable("#Reporte")) {
        $('#Reporte').DataTable().destroy();
    }
    $('#empleado').empty();
    $('#dias').empty();
    $('#VacioImg').empty();
    $("#myChart").show();
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
            datos = data;
            $('#VacioImg').hide();
            tablaEnVista();
        },
        error: function (data) { }
    })
}
function conActividadesDiarias() {
    if ($.fn.DataTable.isDataTable("#actividadD")) {
        $("#actividadD").DataTable().destroy();
    }
    $('#diasActvidad').empty();
    $('#empleadoActividad').empty();
    $('#VacioImg').empty();
    $("#myChart").show();
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
                //* TABLA CON ACTIVIDAD DIARIA
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
            html_trA += '</tr>';
            var decimal = parseFloat(sumaP);
            horas.push(decimal);
        }
        for (var m = 0; m < datos[0].fechaF.length; m++) {
            var momentValue = moment(datos[0].fechaF[m]);
            momentValue.toDate();
            momentValue.format("ddd DD/MM");
            html_trAD += '<th>' + momentValue.format("ddd DD/MM") + '</th>';
            html_trAD += '<th class="text-center"><img src="landing/images/velocimetro (1).svg" class="mr-2" height="17"/></th>';
        }
        html_trAD += '<th>TOTAL</th>';
        html_trAD += '<th>ACTIV.</th></tr>';
        //* TABLA CON ACTIVIDAD DIARIA
        $('#diasActvidad').html(html_trAD);
        $('#empleadoActividad').html(html_trA);
        var table = $("#actividadD").DataTable({
            "searching": false,
            "scrollX": true,
            retrieve: true,
            "ordering": false,
            "autoWidth": false,
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
                },
                sheetName: 'Exported data',
                autoFilter: false
            }, {
                extend: "pdfHtml5",
                className: 'btn btn-sm mt-1',
                text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                orientation: 'landscape',
                pageSize: 'LEGAL',
                title: 'REPORTE SEMANAL',
                exportOptions: {
                    columns: ":visible",
                    orthogonal: 'export'
                },
                customize: function (doc) {
                    doc['styles'] = {
                        table: {
                            width: '100%'
                        },
                        tableHeader: {
                            bold: !0,
                            fontSize: 11,
                            color: '#ffffff',
                            fillColor: '#14274e',
                            alignment: 'left'
                        },
                        defaultStyle: {
                            fontSize: 10,
                            alignment: 'center'
                        }
                    };
                    doc.pageMargins = [20, 60, 20, 30];
                    doc.content[1].margin = [30, 0, 30, 0];
                    //* COLOR DE ROWS
                    age = table.column().data().toArray();
                    for (var i = 0; i < age.length; i++) {
                        if (age[i] % 2 === 0) {
                            var lengthC = $('#actividadD tbody tr:first-child td').length;
                            for (var j = 0; j < lengthC; j++) {
                                doc.content[1].table.body[i + 1][j].fillColor = '#f1f1f1';
                            }
                        }
                    }
                    //* LOGO
                    var logo = "data:image/png;base64,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";
                    doc.content.splice(0, 1);
                    var now = new Date();
                    var jsDate = now.getDate() + "-" + (now.getMonth() + 1) + "-" + now.getFullYear();
                    doc["header"] = function () {
                        return {
                            columns: [
                                {
                                    image: logo,
                                    width: 25
                                },
                                {
                                    alignment: "left",
                                    italics: false,
                                    fontSize: 14,
                                    text: 'RH nube - Reporte Semanal',
                                    color: '#14274e',
                                    bold: !0,
                                    margin: [0, 5]
                                },
                            ],
                            margin: 20
                        };
                    };
                    doc["footer"] = function (page, pages) {
                        return {
                            columns: [
                                {
                                    alignment: "left",
                                    text: ["Fecha: ", { text: jsDate.toString() }]
                                },
                                {
                                    alignment: "right",
                                    text: [
                                        "pagina ",
                                        { text: page.toString() },
                                        " de ",
                                        { text: pages.toString() }
                                    ]
                                }
                            ],
                            margin: 12
                        };
                    };
                }
            }],
            paging: true,
            initComplete: function () {
                setTimeout(function () { $("#actividadD").DataTable().draw(); }, 200);
            }
        });
        $(window).on('resize', function () {
            $("#actividadD").css('width', '100%');
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

        grafico = new ApexCharts(document.querySelector("#myChart"), options);
        grafico.render();

    } else {
        $.notify({
            message: "No se encontraron datos.",
            icon: 'admin/images/warning.svg'
        });
        var html_trAD = "<tr><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
        html_trAD += '<th>LUN.</th>';
        html_trAD += '<th>MAR.</th>';
        html_trAD += '<th>MIÉ.</th>';
        html_trAD += '<th>JUE.</th>';
        html_trAD += '<th>VIE.</th>';
        html_trAD += '<th>SÁB.</th>';
        html_trAD += '<th>TOTAL</th>';
        html_trAD += '<th>ACTIV.</th></tr>';
        $('#diasActvidad').html(html_trAD);
    }
}
function sinActividadesDiarias() {
    if ($.fn.DataTable.isDataTable("#Reporte")) {
        $('#Reporte').DataTable().destroy();
    }
    $('#empleado').empty();
    $('#dias').empty();
    $('#VacioImg').empty();
    $("#myChart").show();
    if (grafico.config != undefined) grafico.destroy();
    if (datos.length > 0) {
        $('#VacioImg').hide();
        var nombre = [];
        var horas = [];
        var html_tr = "";
        var html_trD = "<tr><th>#</th><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
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
                //* TABLA DEFAULT
                html_tr += '<td>' + datos[i].horas[j] + '</td>';
            }
            if (sumaRTotal[0] != 0) {
                var p1 = ((sumaATotal[0] / sumaRTotal[0]) * 100).toFixed(2);
                var sumaP = p1;
            } else {
                var sumaP = 0;
            }
            //* TABLA DEFAULT
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
            //* TABLA DEFAULT
            html_trD += '<th>' + momentValue.format("ddd DD/MM") + '</th>';
        }
        //* TABLA DEFAULT
        html_trD += '<th>TOTAL</th>';
        html_trD += '<th>ACTIV.</th></tr>';
        //* TABLA DEFAULT
        $("#dias").html(html_trD);
        $("#empleado").html(html_tr);

        table = $("#Reporte").DataTable({
            "searching": false,
            "scrollX": true,
            retrieve: true,
            "ordering": false,
            "pageLength": 15,
            "autoWidth": false,
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
                    $('c[r=A1] t', sheet).text('RH nube - Reporte Semanal').attr('s', '2');;
                },
                sheetName: 'Reporte Semanal',
                autoFilter: false
            }, {
                extend: "pdfHtml5",
                className: 'btn btn-sm mt-1',
                text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                orientation: 'landscape',
                pageSize: 'A4',
                title: 'REPORTE SEMANAL',
                customize: function (doc) {
                    doc['styles'] = {
                        table: {
                            width: '100%'
                        },
                        tableHeader: {
                            bold: !0,
                            fontSize: 11,
                            color: '#ffffff',
                            fillColor: '#14274e',
                            alignment: 'left'
                        },
                        defaultStyle: {
                            fontSize: 10,
                            alignment: 'center'
                        }
                    };
                    doc.pageMargins = [20, 60, 20, 30];
                    doc.content[1].margin = [60, 0, 60, 0];
                    //* COLOR DE ROWS
                    age = table.column().data().toArray();
                    for (var i = 0; i < age.length; i++) {
                        if (age[i] % 2 === 0) {
                            var lengthC = $('#Reporte tbody tr:first-child td').length;
                            for (var j = 0; j < lengthC; j++) {
                                doc.content[1].table.body[i + 1][j].fillColor = '#f1f1f1';
                            }
                        }
                    }
                    //* LOGO
                    var logo = "data:image/png;base64,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";
                    doc.content.splice(0, 1);
                    var now = new Date();
                    var jsDate = now.getDate() + "-" + (now.getMonth() + 1) + "-" + now.getFullYear();
                    doc["header"] = function () {
                        return {
                            columns: [
                                {
                                    image: logo,
                                    width: 25
                                },
                                {
                                    alignment: "left",
                                    italics: false,
                                    fontSize: 14,
                                    text: 'RH nube - Reporte Semanal',
                                    color: '#060930',
                                    bold: !0,
                                    margin: [0, 5]
                                },
                            ],
                            margin: 20
                        };
                    };
                    doc["footer"] = function (page, pages) {
                        return {
                            columns: [
                                {
                                    alignment: "left",
                                    text: ["Fecha: ", { text: jsDate.toString() }]
                                },
                                {
                                    alignment: "right",
                                    text: [
                                        "pagina ",
                                        { text: page.toString() },
                                        " de ",
                                        { text: pages.toString() }
                                    ]
                                }
                            ],
                            margin: 12
                        };
                    };
                }
            }],
            paging: true
        });
        $(window).on('resize', function () {
            $("#Reporte").css('width', '100%');
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

        grafico = new ApexCharts(document.querySelector("#myChart"), options);
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
        $("#dias").html(html_trD);
    }
}
function changeFecha() {
    dato = $('#fecha').val();
    value = moment(dato, ["YYYY-MM-DD"]).format("YYYY-MM-DD");
    numberWeek = moment(dato, ["YYYY-MM-DD"]).week();
    firstDate = moment().isoWeek(numberWeek).startOf("isoweek").format('YYYY-MM-DD');
    lastDate = moment().isoWeek(numberWeek).endOf("isoweek").format('YYYY-MM-DD');
    $('#fecha').val(firstDate + "   a   " + lastDate);
    onSelectFechas();
    $('#fecha').val(dato);
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
function fechaDefecto() {
    dato = $('#fecha').val();
    value = moment(dato, ["YYYY-MM-DD"]).format("YYYY-MM-DD");
    numberWeek = moment(dato, ["YYYY-MM-DD"]).week();
    firstDate = moment().isoWeek(numberWeek).startOf("isoweek").format('YYYY-MM-DD');
    lastDate = moment().isoWeek(numberWeek).endOf("isoweek").format('YYYY-MM-DD');
    $('#fecha').val(firstDate + "   a   " + lastDate);
    onSelectFechas();
    $('#fecha').val(dato);
}
function buscarReporte() {
    changeFecha();
    $('#busquedaP').show();
    $('#busquedaA').show();
}
function mostrarGrafica() {
    $('#VacioImg').toggle();
    $('#graficaReporte').toggle();
}

function cambiarTabla() {
    $("#customSwitchD").on("change.bootstrapSwitch", function (
        event
    ) {
        if (event.target.checked == true) {
            conActividadesDiarias();
            $('#tablaConActividadD').show();
            $('#tablaSinActividadD').hide();
        } else {
            sinActividadesDiarias();
            $('#tablaConActividadD').hide();
            $('#tablaSinActividadD').show();
        }
    });
}

function tablaEnVista() {
    if ($("#customSwitchD").is(":checked")) {
        conActividadesDiarias();
        $('#tablaConActividadD').show();
        $('#tablaSinActividadD').hide();
    } else {
        sinActividadesDiarias();
        $('#tablaConActividadD').hide();
        $('#tablaSinActividadD').show();
    }
}