$('#graficaReporteMensual').hide();
$('#fechaMensual').datetimepicker({
    language: 'es',
    format: 'mm/yyyy',
    startView: 3,
    minView: 3,
    pickTime: false,
    autoclose: true,
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
    console.log(suma);
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
    console.log(suma);
    resultado.push(suma);
    return resultado;
}
var grafico = {};

function onSelectFechasMensual() {
    var fecha = $('#fechaMensual').val();
    if ($.fn.DataTable.isDataTable("#ReporteMensual")) {
        $('#ReporteMensual').DataTable().destroy();
    }
    $('#empleadoMensual').empty();
    $('#diasMensual').empty();
    $('#myChartDMensual').empty();
    $("#myChartMensual").show();
    if (grafico.config != undefined) grafico.destroy();
    $.ajax({
        async: false,
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
                $('#myChartDMensual').hide();
                var nombre = [];
                var horas = [];
                var prom = [];
                var color = ['rgb(63,77,113)'];
                var borderColor = ['rgb(63,77,113)'];
                var html_tr = "";
                var html_trD = "<tr><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
                for (var i = 0; i < data.length; i++) {
                    html_tr += '<tr><td>' + data[i].nombre + ' ' + data[i].apPaterno + ' ' + data[i].apMaterno + '</td>';
                    nombre.push(data[i].nombre.split('')[0] + data[i].apPaterno.split('')[0] + data[i].apMaterno.split('')[0]);
                    var total = data[i].horas.reduce(function (a, b) {
                        return sumarHora(a, b);
                    });
                    /*var promedio = data[i].promedio.reduce(function (a, b) {
                        return sumarHora(a, b);
                    });*/
                    var promedio = data[i].promedio.reduce(function (a, b) {
                        return calcularPromedio(a, b);
                    });
                    console.log(data[i].total);
                    var contar = data[i].total.reduce(function (a, b) {
                        return totalContar(a, b);
                    });
                    for (let j = 0; j < data[i].horas.length; j++) {
                        html_tr += '<td>' + data[i].horas[j] + '</td>';
                    }
                    if (contar[0] != 0) {
                        var p1 = (promedio[0] / contar[0]).toFixed(2);
                        var sumaP = p1;
                    } else {
                        var sumaP = 0;
                    }
                    /*var t1 = total.split(":");
                    var sumaT = parseInt(t1[0]) * 3600 + parseInt(t1[1]) * 60 + parseInt(t1[2]);*/
                    /*var sumaTotalP = 0;
                    if (sumaT != 0) {
                        sumaTotalP = Math.round((sumaP * 100) / sumaT);
                        prom.push(sumaTotalP);
                    } else {
                        sumaTotalP = 0;
                        prom.push(sumaTotalP);
                    }*/
                    html_tr += '<td>' + total + '</td>';
                    html_tr += '<td>' + sumaP + '%' + '</td>';
                    console.log(total);
                    var decimal = parseFloat(total.split(":")[0] + "." + total.split(":")[1] + total.split(":")[2]);
                    horas.push(decimal);
                    html_tr += '</tr>';
                }
                for (var m = 0; m < data[0].fechaF.length; m++) {
                    var momentValue = moment(data[0].fechaF[m]);
                    momentValue.toDate();
                    momentValue.format("ddd DD/MM");
                    html_trD += '<th>' + momentValue.format("ddd DD/MM") + '</th>';
                }
                html_trD += '<th>TOTAL</th>';
                html_trD += '<th>ACTIV.</th></tr>';
                $("#diasMensual").html(html_trD);
                $("#empleadoMensual").html(html_tr);
                //container.append(html_tr);
                //containerD.append(html_trD);

                $("#ReporteMensual").DataTable({
                    "searching": true,
                    "scrollX": true,
                    retrieve: true,
                    "ordering": false,
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
                        title: 'RH SOLUTION REPORTE SEMANAL'
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
                var mostrar = $("#myChartMensual");
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
        error: function (data) {}
    })
}

$(function () {
    $('#fechaMensual').on('change.dp', function (e) {
        dato = $('#fechaMensual').val();
        value = moment(dato, ["MM-YYYY"]).format("YYYY-MM");
        firstDate = moment(value, 'YYYY-MM').startOf('month').format('YYYY-MM-DD');
        lastDate = moment(value, 'YYYY-MM-DD').endOf('month').format('YYYY-MM-DD');
        console.log(firstDate,lastDate);
        $('#fechaMensual').val(firstDate + "   a   " + lastDate);
        onSelectFechasMensual();
    });
});

function fechaDefecto() {
    dato = $('#fechaMensual').val();
    value = moment(dato, ["DD-YYYY"]).format("YYYY-MM-DD");
    firstDate = moment(value, 'YYYY-MM-DD').day(1).format('YYYY-MM-DD');
    lastDate = moment(value, 'YYYY-MM-DD').day(7).format('YYYY-MM-DD');
    $('#fechaMensual').val(firstDate + "   a   " + lastDate);
    onSelectFechasMensual();
}
$(function () {
    var hoy = moment().format("MM/YYYY");
    $('#fechaMensual').val(hoy);
    $('#fechaMensual').trigger("change.dp");
    $('#fechaMensual').val(hoy);
});

function mostrarGraficaMensual() {
    $('#graficaReporteMensual').toggle();
}
