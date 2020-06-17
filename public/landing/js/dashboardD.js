//AREA
var ctx = document.getElementById('areaD');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'doughnut',

    // The data for our dataset
    data: {
        labels: ['Contabilidad', 'Logística', 'Administración', 'Producción', 'Comerciales'],
        datasets: [{
            data: [285, 146, 105, 491, 30],
            backgroundColor: ['#eb4559', '#ffd31d', '#21bf73', '#a6dcef', '#ff9c71']
        }]
    },

    // Configuration options go here
    options: {
        responsive: true,
        cutoutPercentage: 70,
        tooltips: {
            enabled: false
        },
        legend: {
            display: false
        },
        plugins: {
            datalabels: {
                formatter: function (value, context) {
                    var label = context.chart.data.labels[context.dataIndex];
                    var sum = 0;
                    var dataArr = context.chart.data.datasets[0].data;
                    dataArr.map(data => {
                        sum += data;
                    });
                    var percentage = (value * 100 / sum).toFixed(2) + "%"
                    var mostrar = [];
                    mostrar.push(label,percentage);
                    console.log(mostrar);
                    return mostrar;
                },
                color: '#323232',
                anchor: 'center',
                font: {
                    weight: 'bold',
                    fontSize: 24
                }
            }
        }
    }
});
//NIVEL
var ctx = document.getElementById('nivelD');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'doughnut',

    // The data for our dataset
    data: {
        labels: ['Operarios de Prod.', 'Jefaturas', 'Ejecutivos I', 'Ejecutivos II'],
        datasets: [{
            data: [285, 146, 105, 391],
            backgroundColor: ['#eb4559', '#ffd31d', '#21bf73', '#a6dcef', '#ff9c71']
        }]
    },

    // Configuration options go here
    options: {
        responsive: true,
        cutoutPercentage: 70,
        tooltips: {
            enabled: false
        },
        legend: {
            display: false
        },
        plugins: {
            datalabels: {
                formatter: function (value, context) {
                    var label = context.chart.data.labels[context.dataIndex];
                    var sum = 0;
                    var dataArr = context.chart.data.datasets[0].data;
                    dataArr.map(data => {
                        sum += data;
                    });
                    var percentage = (value * 100 / sum).toFixed(2) + "%"
                    var mostrar = [];
                    mostrar.push(label,percentage);
                    console.log(mostrar);
                    return mostrar;
                },
                color: '#323232',
                anchor: 'center',
                font: {
                    weight: 'bold',
                    fontSize: 24
                }
            }
        }
    }
});
//CONTRATO
var ctx = document.getElementById('contratoD');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'doughnut',

    // The data for our dataset
    data: {
        labels: ['Por servicio', 'Planilla', 'Administración', 'Producción', 'Comerciales'],
        datasets: [{
            data: [285, 146],
            backgroundColor: ['#eb4559', '#ffd31d', '#21bf73', '#a6dcef', '#ff9c71']
        }]
    },

    // Configuration options go here
    options: {
        responsive: true,
        cutoutPercentage: 70,
        tooltips: {
            enabled: false
        },
        legend: {
            display: false
        },
        plugins: {
            datalabels: {
                formatter: function (value, context) {
                    var label = context.chart.data.labels[context.dataIndex];
                    var sum = 0;
                    var dataArr = context.chart.data.datasets[0].data;
                    dataArr.map(data => {
                        sum += data;
                    });
                    var percentage = (value * 100 / sum).toFixed(2) + "%"
                    var mostrar = [];
                    mostrar.push(label,percentage);
                    console.log(mostrar);
                    return mostrar;
                },
                color: '#323232',
                anchor: 'center',
                font: {
                    weight: 'bold',
                    fontSize: 24
                }
            }
        }
    }
});

//CENTRO
var ctx = document.getElementById('centroD');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'doughnut',

    // The data for our dataset
    data: {
        labels: ['CCosto FIN', 'CCosto I + D', 'CCosto COM', 'CCosto ADM', 'CCosto PDR'],
        datasets: [{
            data: [285, 146, 105, 491, 30],
            backgroundColor: ['#eb4559', '#ffd31d', '#21bf73', '#a6dcef', '#ff9c71']
        }]
    },

    // Configuration options go here
    options: {
        responsive: true,
        cutoutPercentage: 70,
        tooltips: {
            enabled: false
        },
        legend: {
            display: false
        },
        plugins: {
            datalabels: {
                formatter: function (value, context) {
                    var label = context.chart.data.labels[context.dataIndex];
                    var sum = 0;
                    var dataArr = context.chart.data.datasets[0].data;
                    dataArr.map(data => {
                        sum += data;
                    });
                    var percentage = (value * 100 / sum).toFixed(2) + "%"
                    var mostrar = [];
                    mostrar.push(label,percentage);
                    console.log(mostrar);
                    return mostrar;
                },
                color: '#323232',
                anchor: 'center',
                font: {
                    weight: 'bold',
                    fontSize: 24
                }
            }
        }
    }
});
//LOCAL
var ctx = document.getElementById('localD');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'doughnut',

    // The data for our dataset
    data: {
        labels: ['Local Lima', 'Planta'],
        datasets: [{
            data: [285, 146],
            backgroundColor: ['#eb4559', '#ffd31d', '#21bf73', '#a6dcef', '#ff9c71']
        }]
    },

    // Configuration options go here
    options: {
        responsive: true,
        cutoutPercentage: 70,
        tooltips: {
            enabled: false
        },
        legend: {
            display: false
        },
        plugins: {
            datalabels: {
                formatter: function (value, context) {
                    var label = context.chart.data.labels[context.dataIndex];
                    var sum = 0;
                    var dataArr = context.chart.data.datasets[0].data;
                    dataArr.map(data => {
                        sum += data;
                    });
                    var percentage = (value * 100 / sum).toFixed(2) + "%"
                    var mostrar = [];
                    mostrar.push(label,percentage);
                    console.log(mostrar);
                    return mostrar;
                },
                color: '#323232',
                anchor: 'center',
                font: {
                    weight: 'bold',
                    fontSize: 24
                }
            }
        }
    }
});

//RANGO DE EDAD
var ctx = document.getElementById('edadD');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'doughnut',

    // The data for our dataset
    data: {
        labels: ['19 a 24', '24 a 30', '30 a 40', '40 a 50', '50 a 70'],
        datasets: [{
            data: [285, 146, 105, 491, 30],
            backgroundColor: ['#eb4559', '#ffd31d', '#21bf73', '#a6dcef', '#ff9c71']
        }]
    },

    // Configuration options go here
    options: {
        responsive: true,
        cutoutPercentage: 70,
        tooltips: {
            enabled: false
        },
        legend: {
            display: false
        },
        plugins: {
            datalabels: {
                formatter: function (value, context) {
                    var label = context.chart.data.labels[context.dataIndex];
                    var sum = 0;
                    var dataArr = context.chart.data.datasets[0].data;
                    dataArr.map(data => {
                        sum += data;
                    });
                    var percentage = (value * 100 / sum).toFixed(2) + "%"
                    var mostrar = [];
                    mostrar.push(label,percentage);
                    console.log(mostrar);
                    return mostrar;
                },
                color: '#323232',
                anchor: 'center',
                font: {
                    weight: 'bold',
                    fontSize: 24
                }
            }
        }
    }
});