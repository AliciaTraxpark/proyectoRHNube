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
        var color = ['#21bf73','#ffd31d','#eb4559'];
        var suma = 0;
        var totalP = 0;
        if (data[0].area.length != 0) {
            for (var i = 0; i < data[0].area.length; i++) {
                suma += data[0].area[i].Total;
                nombre.push(data[0].area[i].area_descripcion);
                total.push(data[0].area[i].Total);
            }
            var promedio = (suma*100)/data[0].empleado[0].totalE;
            totalP = Math.round(promedio);
            console.log(totalP);
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
                            text: totalP + '% de empleados',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
        } else {
            $('#divarea').hide();
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
        var color = ['#21bf73','#ffd31d','#eb4559'];
        var suma = 0;
        var totalP = 0;
        if (data[0].nivel.length != 0) {
            for (var i = 0; i < data[0].nivel.length; i++) {
                nombre.push(data[0].nivel[i].nivel_descripcion);
                total.push(data[0].nivel[i].Total);
                suma += data[0].nivel[i].Total;
            }
            var promedio = (suma*100)/data[0].empleado[0].totalE;
            totalP = Math.round(promedio);
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
                            text: totalP + '% de empleados',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
        } else {
            $('#divnivel').hide();
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
        var color = ['#21bf73','#ffd31d','#eb4559'];
        var suma = 0;
        var totalP = 0;
        if (data[0].contrato.length != 0) {
            for (var i = 0; i < data[0].contrato.length; i++) {
                nombre.push(data[0].contrato[i].contrato_descripcion);
                total.push(data[0].contrato[i].Total);
                suma += data[0].contrato[i].Total;
            }
            var promedio = (suma*100)/data[0].empleado[0].totalE;
            totalP = Math.round(promedio);
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
                            text: totalP + '% de empleados',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
        } else {
            $('#divcontrato').hide();
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
//CENTRO
$.ajax({
    url: "totalCC",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#21bf73','#ffd31d','#eb4559'];
        var suma = 0;
        if (data[0].centro.length != 0) {
            for (var i = 0; i < data[0].centro.length; i++) {
                nombre.push(data[0].centro[i].centroC_descripcion);
                total.push(data[0].centro[i].Total);
                suma += data[0].centro[i].Total;
            }
            var promedio = (suma*100)/data[0].empleado[0].totalE;
            totalP = Math.round(promedio);
            var chartdata = {
                labels: nombre,
                datasets: [{
                    data: total,
                    backgroundColor: color,
                    borderWidth: 0
                }]
            };
            var mostrar = $('#centro');
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
                            text: totalP + '% de empleados',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
        } else {
            $('#divcentro').hide();
            $.notify(" Aún no has asignado empleados a un tipo de centro costo.", {
                align: "right",
                verticalAlign: "top",
                type: "warning",
                icon: "warning",
                delay: 3000
            });
        }
    },
    error: function (data) {
        $.notify(" Aún no has asignado empleados a un tipo de centro costo.", {
            align: "right",
            verticalAlign: "top",
            type: "warning",
            icon: "warning",
            delay: 3000
        });
    }
});
//LOCAL
$.ajax({
    url: "totalL",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#21bf73','#ffd31d','#eb4559'];
        var suma = 0;
        var totalP = 0;
        if (data[0].local.length != 0) {
            for (var i = 0; i < data[0].local.length; i++) {
                nombre.push(data[0].local[i].local_descripcion);
                total.push(data[0].local[i].Total);
                suma += data[0].local[i].Total;
            }
            var promedio = (suma*100)/data[0].empleado[0].totalE;
            totalP = Math.round(promedio);
            var chartdata = {
                labels: nombre,
                datasets: [{
                    data: total,
                    backgroundColor: color,
                    borderWidth: 0
                }]
            };
            var mostrar = $('#local');
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
                            text: totalP + '% de empleados',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
        } else {
            $('#divlocal').hide();
            $.notify(" Aún no has asignado empleados a un local.", {
                align: "right",
                verticalAlign: "top",
                type: "warning",
                icon: "warning",
                delay: 3000
            });
        }
    },
    error: function (data) {
        $.notify(" Aún no has asignado empleados a un local.", {
            align: "right",
            verticalAlign: "top",
            type: "warning",
            icon: "warning",
            delay: 3000
        });
    }
});
//EDAD
$.ajax({
    url: "totalE",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#21bf73','#ff9c71','#ffd31d','#eb4559'];
        var suma = 0;
        var totalP = 0;
        if (data[0].edad.length != 0) {
            for (var i = 0; i < data[0].edad.length; i++) {
                nombre.push(data[0].edad[i].edad);
                total.push(data[0].edad[i].total);
                suma += data[0].edad[i].total;
            }
            var promedio = (suma*100)/data[0].empleado[0].totalE;
            totalP = Math.round(promedio);
            var chartdata = {
                labels: nombre,
                datasets: [{
                    data: total,
                    backgroundColor: color,
                    borderWidth: 0
                }]
            };
            var mostrar = $('#edades');
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
                            text: totalP + '% de empleados',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
        } else {
            $('#divedades').hide();
            $.notify(" Aún no has asignado empleados a un local.", {
                align: "right",
                verticalAlign: "top",
                type: "warning",
                icon: "warning",
                delay: 3000
            });
        }
    },
    error: function (data) {
        $.notify(" Aún no has asignado empleados a un local.", {
            align: "right",
            verticalAlign: "top",
            type: "warning",
            icon: "warning",
            delay: 3000
        });
    }
});