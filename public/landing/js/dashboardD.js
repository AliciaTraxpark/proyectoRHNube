Chart.pluginService.register({
    beforeDraw: function (chart) {
        if (chart.config.options.elements.center) {
            //Get ctx from string
            var ctx = chart.chart.ctx;

            //Get options from the center object in options
            var centerConfig = chart.config.options.elements.center;
            var fontStyle = centerConfig.fontStyle || 'Arial';
            var txt = centerConfig.text;
            var color = centerConfig.color || '#000';
            var sidePadding = centerConfig.sidePadding || 20;
            var sidePaddingCalculated = (sidePadding / 100) * (chart.innerRadius * 2)
            //Start with a base font of 30px
            ctx.font = "30px " + fontStyle;

            //Get the width of the string and also the width of the element minus 10 to give it 5px side padding
            var stringWidth = ctx.measureText(txt).width;
            var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;

            // Find out how much the font can grow in width.
            var widthRatio = elementWidth / stringWidth;
            var newFontSize = Math.floor(30 * widthRatio);
            var elementHeight = (chart.innerRadius * 2);

            // Pick a new font size so it will not be larger than the height of label.
            var fontSizeToUse = Math.min(newFontSize, elementHeight);

            //Set font settings to draw it correctly.
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
            var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
            ctx.font = fontSizeToUse + "px " + fontStyle;
            ctx.fillStyle = color;

            //Draw text in center
            ctx.fillText(txt, centerX, centerY);
        }
    }
});
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
        layout: {
            padding: {
                bottom: 40,
                top: 40
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        cutoutPercentage: 80,
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
                    var mostrar = [];
                    mostrar.push(label);
                    return mostrar;
                },
                color: '#323232',
                anchor: 'end',
                align: 'end',
                font: {
                    weight: 'bold',
                    fontSize: 24
                }
            }
        },
        elements: {
            center: {
                text: '350 por Área',
                color: '#1f4068', //Default black
                fontFamily: 'Arial', //Default Arial
                sidePadding: 35,
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
        layout: {
            padding: {
                bottom: 40,
                top: 40,
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        cutoutPercentage: 80,
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
                    var mostrar = [];
                    mostrar.push(label);
                    return mostrar;
                },
                color: '#323232',
                anchor: 'end',
                align: 'end',
                font: {
                    weight: 'bold',
                    fontSize: 24
                }
            }
        },
        elements: {
            center: {
                text: '350 por Contrato',
                color: '#1f4068', //Default black
                fontStyle: 'Helvetica', //Default Arial
                sidePadding: 20
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
        layout: {
            padding: {
                bottom: 40,
                top: 40,
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        cutoutPercentage: 80,
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
                    var mostrar = [];
                    mostrar.push(label);
                    return mostrar;
                },
                color: '#323232',
                anchor: 'end',
                align: 'end',
                font: {
                    weight: 'bold',
                    fontSize: 24
                }
            }
        },
        elements: {
            center: {
                text: '350 por Nivel',
                color: '#1f4068', //Default black
                fontStyle: 'Helvetica', //Default Arial
                sidePadding: 35
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
        layout: {
            padding: {
                bottom: 40,
                top: 40,
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        cutoutPercentage: 80,
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
                    var mostrar = [];
                    mostrar.push(label);
                    return mostrar;
                },
                color: '#323232',
                anchor: 'end',
                align: 'end',
                font: {
                    weight: 'bold',
                    fontSize: 24
                }
            }
        },
        elements: {
            center: {
                text: '350 por Centro Costo',
                color: '#1f4068', //Default black
                fontStyle: 'Helvetica', //Default Arial
                sidePadding: 20
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
        layout: {
            padding: {
                bottom: 40,
                top: 40,
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        cutoutPercentage: 80,
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
                    var mostrar = [];
                    mostrar.push(label);
                    return mostrar;
                },
                color: '#323232',
                anchor: 'end',
                align: 'end',
                font: {
                    weight: 'bold',
                    fontSize: 24
                }
            }
        },
        elements: {
            center: {
                text: '350 por Local',
                color: '#1f4068', //Default black
                fontStyle: 'Helvetica', //Default Arial
                sidePadding: 35
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
        layout: {
            padding: {
                bottom: 40,
                top: 40,
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        cutoutPercentage: 80,
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
                    var mostrar = [];
                    mostrar.push(label);
                    return mostrar;
                },
                color: '#323232',
                anchor: 'end',
                align: 'end',
                font: {
                    weight: 'bold',
                    fontSize: 24
                }
            }
        },
        elements: {
            center: {
                text: '350 por Rango de Edad',
                color: '#1f4068', //Default black
                fontStyle: 'Helvetica', //Default Arial
                sidePadding: 20
            }
        }
    }
});
