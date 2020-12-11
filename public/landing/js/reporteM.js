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
    $("#actividadDM").DataTable({
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
    console.log(resultado);
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

function onSelectFechasMensual() {
    var fecha = $('#fechaMensual').val();
    var area = $('#area').val();
    var cargo = $('#cargo').val();
    if ($.fn.DataTable.isDataTable("#ReporteMensual")) {
        $('#ReporteMensual').DataTable().destroy();
    }
    if ($.fn.DataTable.isDataTable("#actividadDM")) {
        $('#actividadDM').DataTable().destroy();
    }
    $('#empleadoMensual').empty();
    $('#diasMensual').empty();
    $('#diasActvidad').empty();
    $('#empleadoActividad').empty();
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
            cargo: cargo
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
            if (data.length > 0) {
                $('#VacioImg').hide();
                var nombre = [];
                var horas = [];
                var prom = [];
                var color = ['rgb(63,77,113)'];
                var borderColor = ['rgb(63,77,113)'];
                var html_tr = "";
                var html_trA = "";
                var html_trD = "<tr><th>#</th><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</>";
                var html_trAD = "<tr><th>#</th><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
                for (var i = 0; i < data.length; i++) {
                    html_tr += '<tr><td>' + (i + 1) + '</td><td>' + data[i].nombre + ' ' + data[i].apPaterno + ' ' + data[i].apMaterno + '</td>';
                    html_trA += '<tr><td>' + (i + 1) + '</td><td>' + data[i].nombre + ' ' + data[i].apPaterno + ' ' + data[i].apMaterno + '</td>';
                    nombre.push(data[i].nombre.split('')[0] + data[i].apPaterno.split('')[0] + data[i].apMaterno.split('')[0]);
                    var total = data[i].horas.reduce(function (a, b) {
                        return sumarHora(a, b);
                    });
                    var sumaATotal = data[i].sumaActividad.reduce(function (a, b) {
                        return sumarTotales(a, b);
                    });

                    var sumaRTotal = data[i].sumaRango.reduce(function (a, b) {
                        return sumarTotales(a, b);
                    });
                    for (let j = 0; j < data[i].horas.length; j++) {
                        // TABLA DEFAULT
                        html_tr += '<td>' + data[i].horas[j] + '</td>';
                        // TABLA CON ACTIVIDAD DIARIA
                        html_trA += '<td>' + data[i].horas[j] + '</td>';
                        var sumaA = data[i].sumaActividad[j];
                        var sumaR = data[i].sumaRango[j];
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
                    // TABLA DEFAULT
                    html_tr += '<td>' + total + '</td>';
                    html_tr += '<td>' + sumaP + '%' + '</td>';
                    // TABLA CO ACTIVIDADES
                    html_trA += '<td>' + total + '</td>';
                    html_trA += '<td>' + sumaP + '%' + '</td>';
                    // *********************
                    var decimal = parseFloat(sumaP);
                    horas.push(decimal);
                    html_tr += '</tr>';
                }
                for (var m = 0; m < data[0].fechaF.length; m++) {
                    var momentValue = moment(data[0].fechaF[m]);
                    momentValue.toDate();
                    momentValue.format("ddd DD/MM");
                    // TABLA DEFAULT
                    html_trD += '<th>' + momentValue.format("ddd DD/MM") + '</th>';
                    // TABLA CON ACTIVIDAD DIARIA
                    html_trAD += '<th>' + momentValue.format("ddd DD/MM") + '</th>';
                    html_trAD += '<th><img src="landing/images/velocimetro (1).svg" class="mr-2" height="17"/></th>';
                }
                // TABLA DEFAULT
                html_trD += '<th>TOTAL</th>';
                html_trD += '<th>ACTIV.</th></tr>';
                // TABLA CON ACTIVIDAD DIARIA
                html_trAD += '<th>TOTAL</th>';
                html_trAD += '<th>ACTIV.</th></tr>';
                // TABLA DEFAULT
                $("#diasMensual").html(html_trD);
                $("#empleadoMensual").html(html_tr);
                // TABLA CON ACTIVIDAD DIARIA
                $('#diasActvidad').html(html_trAD);
                $('#empleadoActividad').html(html_trA);

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
                        },
                        sheetName: 'Exported data',
                        autoFilter: false
                    }],
                    paging: true
                });
                tableActividad = $("#actividadDM").DataTable({
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
                        },
                        sheetName: 'Exported data',
                        autoFilter: false
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
            }
        },
        error: function (data) { }
    })
}
// $(function () {
//     $('#fechaMensual').on('change.dp', function (e) {
//         dato = $('#fechaMensual').val();
//         value = moment(dato, ["MMMM-YYYY", "MMM-YYYY", "MM-YYYY"]).format("MM-YYYY");
//         firstDate = moment(value, 'MM-YYYY').startOf('month').format('YYYY-MM-DD');
//         lastDate = moment(value, 'MM-YYYY').endOf('month').format('YYYY-MM-DD');
//         $('#fechaMensual').val(firstDate + "   a   " + lastDate);
//         onSelectFechasMensual();
//     });
// });
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
    $('#cargo').select2({
        placeholder: 'Seleccionar cargos',
        language: "es"
    });
    $('#area').on("change", function (e) {
        fechaDefecto();
    });
    $('#cargo').on("change", function (e) {
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
            dato = $('#fechaMensual').val();
            $('#container').css('display', 'block');
            tableActividad.columns.adjust().draw(true);
            changeFecha();
            $('#fechaMensual').val(dato);
            $('#tablaConActividadD').show();
            $('#tablaSinActividadD').hide();
        } else {
            dato = $('#fecha').val();
            $('#container').css('display', 'block');
            table.columns.adjust().draw(true);
            changeFecha();
            $('#fechaMensual').val(dato);
            $('#tablaConActividadD').hide();
            $('#tablaSinActividadD').show();
        }
    });
}