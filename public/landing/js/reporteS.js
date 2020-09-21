$('#graficaReporte').hide();
$('#fecha').datetimepicker({
    language: 'es',
    format: 'dd/mm/yyyy',
    minView: 2,
    pickTime: false,
    autoclose: true,
    weekStart: 1,
    todayBtn: false,
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
    let suma = parseInt(a[0]) + parseInt(b[0]);
    sumaAcumulado = acumular60(suma, acumulado);
    resultado.push(sumaAcumulado[0]);
    suma = parseInt(a[1]) + parseInt(b[1]) + sumaAcumulado[1];
    acumulado = 0;
    sumaAcumulado = acumular60(suma, acumulado);
    resultado.push(sumaAcumulado[0]);
    suma = parseInt(a[2]) + parseInt(b[2]) + sumaAcumulado[1];
    resultado.push(suma);
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

function onSelectFechas() {
    var fecha = $('#fecha').val();
    if ($.fn.DataTable.isDataTable("#Reporte")) {
        $('#Reporte').DataTable().destroy();
    }
    if ($.fn.DataTable.isDataTable("#actividadD")) {
        $('#actividadD').DataTable().destroy();
    }
    $('#empleado').empty();
    $('#dias').empty();
    $('#diasActvidad').empty();
    $('#empleadoActividad').empty();
    $('#myChartD').empty();
    $("#myChart").show();
    if (grafico.config != undefined) grafico.destroy();
    $.ajax({
        url: "reporte/empleado",
        method: "GET",
        data: {
            fecha: fecha
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
                console.log(data);
                $('#myChartD').hide();
                var nombre = [];
                var horas = [];
                var prom = [];
                var color = ['rgb(63,77,113)'];
                var borderColor = ['rgb(63,77,113)'];
                var html_tr = "";
                var html_trA = "";
                var html_trD = "<tr><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
                var html_trAD = "<tr><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
                for (var i = 0; i < data.length; i++) {
                    html_tr += '<tr><td>' + data[i].nombre + ' ' + data[i].apPaterno + ' ' + data[i].apMaterno + '</td>';
                    html_trA += '<tr><td>' + data[i].nombre + ' ' + data[i].apPaterno + ' ' + data[i].apMaterno + '</td>';
                    nombre.push(data[i].nombre.split('')[0] + data[i].apPaterno.split('')[0] + data[i].apMaterno.split('')[0]);
                    var total = data[i].horas.reduce(function (a, b) {
                        return sumarHora(a, b);
                    });
                    // var promedio = data[i].promedio.reduce(function (a, b) {
                    //     return calcularPromedio(a, b);
                    // });
                    // var contar = data[i].total.reduce(function (a, b) {
                    //     return totalContar(a, b);
                    // });
                    var sumaATotal = data[i].sumaActividad.reduce(function (a, b) {
                        return sumarTotales(a, b);
                    });

                    var sumaRTotal = data[i].sumaRango.reduce(function (a, b) {
                        return sumarTotales(a, b);
                    });
                    console.log(sumaATotal, sumaRTotal);
                    for (let j = 0; j < data[i].horas.length; j++) {
                        // TABLA DEFAULT
                        html_tr += '<td>' + data[i].horas[j] + '</td>';
                        // TABLA CON ACTIVIDAD DIARIA
                        html_trA += '<td>' + data[i].horas[j] + '</td>';
                        var sumaA = data[i].sumaActividad[j];
                        var sumaR = data[i].sumaRango[j];
                        if (sumaR != 0) {
                            var promedioD = (sumaA / sumaR).toFixed(2);
                            html_trA += '<td>' + promedioD + '%' + '</td>';
                        } else {
                            var promedioD = (0).toFixed(2);
                            html_trA += '<td>' + promedioD + '%' + '</td>';
                        }
                    }
                    if (sumaRTotal[0] != 0) {
                        var p1 = (sumaATotal[0] / sumaRTotal[0]).toFixed(2);
                        var sumaP = p1;
                    } else {
                        var sumaP = 0;
                    }
                    // TABLA DEFAULT
                    html_tr += '<td>' + total + '</td>';
                    html_tr += '<td>' + sumaP + '%' + '</td>';
                    // TABLA CON ACTIVIDADES
                    html_trA += '<td>' + total + '</td>';
                    html_trA += '<td>' + sumaP + '%' + '</td>';
                    // ********************
                    var decimal = parseFloat(total.split(":")[0] + "." + total.split(":")[1] + total.split(":")[2]);
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
                    html_trAD += '<th><img src="landing/images/velocimetro (1).svg" class="mr-2" height="17"/>DIARIA</th>';
                }
                // TABLA DEFAULT
                html_trD += '<th>TOTAL</th>';
                html_trD += '<th>ACTIV.</th></tr>';
                // TABLA CON ACTIVIDAD DIARIA
                html_trAD += '<th>TOTAL</th>';
                html_trAD += '<th>ACTIV.</th></tr>';
                // TABLA DEFAULT
                $("#dias").html(html_trD);
                $("#empleado").html(html_tr);
                //TABLA CON ACTIVIDAD DIARIA
                $('#diasActvidad').html(html_trAD);
                $('#empleadoActividad').html(html_trA);

                $("#Reporte").DataTable({
                    "searching": false,
                    "scrollX": true,
                    retrieve: true,
                    "ordering": false,
                    "pageLength": 15,
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
                        pageSize: 'LEGAL',
                        title: 'RH nube REPORTE SEMANAL'
                    }],
                    paging: true
                });
                $("#actividadD").DataTable({
                    "searching": false,
                    "scrollX": true,
                    retrieve: true,
                    "ordering": false,
                    "pageLength": 15,
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
                        pageSize: 'LEGAL',
                        title: 'RH nube REPORTE SEMANAL'
                    }],
                    paging: true
                });
                var chartdata = {
                    labels: nombre,
                    datasets: [{
                        label: nombre,
                        backgroundColor: color,
                        borderColor: color,
                        borderWidth: 2,
                        hoverBackgroundColor: color,
                        hoverBorderColor: borderColor,
                        data: horas
                    }]
                };
                var mostrar = $("#myChart");
                grafico = new Chart(mostrar, {
                    type: 'bar',
                    data: chartdata,
                    theme: "light2",
                    options: {
                        responsive: true,
                        scales: {
                            xAxes: [{
                                stacked: true,
                                gridLines: {
                                    display: false
                                }
                            }],
                            yAxes: [{
                                stacked: true
                            }]
                        },
                        tooltips: {
                            callbacks: {
                                title: function (tooltipItem, data) {
                                    return data.labels[tooltipItem[0].index];
                                },
                                label: function (tooltipItem, data) {
                                    var amount = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                    // return amount + ' / ' + total + ' ( ' + parseFloat(amount * 100 / total).toFixed(2) + '% )';
                                },
                                //footer: function(tooltipItem, data) { return 'Total: 100 planos.'; }
                            }
                        },
                    }
                });
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

$(function () {
    $('#zonaHoraria').empty();
    var zona = Intl.DateTimeFormat().resolvedOptions().timeZone;
    var split = zona.split("/");
    nombre = `${split[0]} - ${split[1]}`;
    $('#zonaHoraria').append(nombre);
});

$(function () {
    $('#fecha').on('change.dp', function (e) {
        dato = $('#fecha').val();
        value = moment(dato, ["DD-MM-YYYY"]).format("YYYY-MM-DD");
        firstDate = moment(value, 'YYYY-MM-DD').day(1).format('YYYY-MM-DD');
        lastDate = moment(value, 'YYYY-MM-DD').day(7).format('YYYY-MM-DD');
        $('#fecha').val(firstDate + "   a   " + lastDate);
        onSelectFechas();
    });
});

function fechaDefecto() {
    dato = $('#fecha').val();
    value = moment(dato, ["DD-MM-YYYY"]).format("YYYY-MM-DD");
    firstDate = moment(value, 'YYYY-MM-DD').day(1).format('YYYY-MM-DD');
    lastDate = moment(value, 'YYYY-MM-DD').day(7).format('YYYY-MM-DD');
    $('#fecha').val(firstDate + "   a   " + lastDate);
    onSelectFechas();
}
$(function () {
    var hoy = moment().format("DD/MM/YYYY");
    $('#fecha').val(hoy);
    $('#fecha').trigger("change.dp");
    $('#fecha').val(hoy);
});

function mostrarGrafica() {
    $('#graficaReporte').toggle();
}

function cambiarTabla() {
    $("#customSwitchD").on("change.bootstrapSwitch", function (
        event
    ) {
        console.log(event.target.checked);
        if (event.target.checked == true) {
            dato = $('#fecha').val();
            $('#fecha').val(dato);
            $('#fecha').trigger("change.dp");
            $('#fecha').val(dato);
            $('#tablaConActividadD').show();
            $('#tablaSinActividadD').hide();
        } else {
            dato = $('#fecha').val();
            $('#fecha').val(dato);
            $('#fecha').trigger("change.dp");
            $('#fecha').val(dato);
            $('#tablaConActividadD').hide();
            $('#tablaSinActividadD').show();
        }
    });
}