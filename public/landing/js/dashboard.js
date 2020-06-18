//AREA
$.ajax({
    url: "totalA",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#eb4559', '#ffd31d', '#21bf73'];
        var suma = 0;
        if (data.length != 0) {
            for (var i = 0; i < data.length; i++) {
                suma += data[i].Total;
                nombre.push(data[i].area_descripcion);
                total.push(data[i].Total);
            }
            var chartdata = {
                labels: nombre,
                datasets: [{
                    data: total,
                    backgroundColor: color,
                    borderWidth: 0
                }]
            };
            var mostrar = $('#area');
            var grafico = new Chart(mostrar, {
                type: 'doughnut',
                data: chartdata,
                options: {
                    responsive: true,
                    cutoutPercentage: 70,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            formatter: function (value, context) {
                                var label = context.chart.data.labels[context.dataIndex];
                                var mostrar = [];
                                mostrar.push(label);
                                return mostrar;
                            },
                            color: '#323232',
                            anchor: 'center',
                            align: 'center',
                            font: {
                                weight: 'bold',
                                fontSize: 20
                            }
                        }
                    },
                    elements: {
                        center: {
                            text: suma + ' por Área',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 35,
                        }
                    }
                }
            });
        } else {
            $.notify(" Aún no has asignado empleados a una área.", {
                align: "right",
                verticalAlign: "top",
                type: "warning",
                icon: "warning",
                delay: 3000
            });
        }
    },
    error: function (data) {
        $.notify(" Aún no has asignado empleados a una área.", {
            align: "right",
            verticalAlign: "top",
            type: "warning",
            icon: "warning",
            delay: 3000
        });
    }
});
//NIVEL
$.ajax({
    url: "totalN",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#eb4559', '#ffd31d', '#21bf73'];
        var suma = 0;
        if (data.length != 0) {
            for (var i = 0; i < data.length; i++) {
                nombre.push(data[i].nivel_descripcion);
                total.push(data[i].Total);
                suma += data[i].Total;
            }
            var chartdata = {
                labels: nombre,
                datasets: [{
                    data: total,
                    backgroundColor: color,
                    borderWidth: 0
                }]
            };
            var mostrar = $('#nivel');
            var grafico = new Chart(mostrar, {
                type: 'doughnut',
                data: chartdata,
                options: {
                    responsive: true,
                    cutoutPercentage: 70,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            formatter: function (value, context) {
                                var label = context.chart.data.labels[context.dataIndex];
                                var mostrar = [];
                                mostrar.push(label);
                                return mostrar;
                            },
                            color: '#323232',
                            anchor: 'center',
                            align: 'center',
                            font: {
                                weight: 'bold',
                                fontSize: 20
                            }
                        }
                    },
                    elements: {
                        center: {
                            text: suma + 'por Nivel',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 35,
                        }
                    }
                }
            });
        } else {
            $.notify(" Aún no has asignado empleados a un nivel.", {
                align: "right",
                verticalAlign: "top",
                type: "warning",
                icon: "warning",
                delay: 3000
            });
        }
    },
    error: function (data) {
        $.notify(" Aún no has asignado empleados a un nivel.", {
            align: "right",
            verticalAlign: "top",
            type: "warning",
            icon: "warning",
            delay: 3000
        });
    }
});
//CONTRATO
$.ajax({
    url: "totalC",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#eb4559', '#ffd31d', '#21bf73'];
        var suma = 0;
        if (data.length != 0) {
            for (var i = 0; i < data.length; i++) {
                nombre.push(data[i].contrato_descripcion);
                total.push(data[i].Total);
                suma += data[i].Total;
            }
            var chartdata = {
                labels: nombre,
                datasets: [{
                    data: total,
                    backgroundColor: color,
                    borderWidth: 0
                }]
            };
            var mostrar = $('#contrato');
            var grafico = new Chart(mostrar, {
                type: 'doughnut',
                data: chartdata,
                options: {
                    responsive: true,
                    cutoutPercentage: 70,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            formatter: function (value, context) {
                                var label = context.chart.data.labels[context.dataIndex];
                                var mostrar = [];
                                mostrar.push(label);
                                return mostrar;
                            },
                            color: '#323232',
                            anchor: 'center',
                            align: 'center',
                            font: {
                                weight: 'bold',
                                fontSize: 20
                            }
                        }
                    },
                    elements: {
                        center: {
                            text: suma + 'por Nivel',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 35,
                        }
                    }
                }
            });
        } else {
            $.notify(" Aún no has asignado empleados a un tipo de contrato.", {
                align: "right",
                verticalAlign: "top",
                type: "warning",
                icon: "warning",
                delay: 3000
            });
        }
    },
    error: function (data) {
        $.notify(" Aún no has asignado empleados a un tipo de contrato.", {
            align: "right",
            verticalAlign: "top",
            type: "warning",
            icon: "warning",
            delay: 3000
        });
    }
});