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
//NOTIFICACION
$.notifyDefaults({
    icon_type: 'image',
    newest_on_top: true,
    delay: 5000,
    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
        '<span data-notify="title">{1}</span> ' +
        '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
        '</div><br><br>'
});
//COLORES
function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
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
        var color = ['#21bf73', '#ffd31d', '#eb4559'];
        var suma = 0;
        var totalP = 0;
        if (data[0].area.length != 0) {
            for (var i = 0; i < data[0].area.length; i++) {
                suma += data[0].area[i].Total;
                nombre.push(data[0].area[i].area_descripcion);
                total.push(data[0].area[i].Total);
            }
            for (var j = 3; j < data[0].area.length; j++) {
                color.push(getRandomColor());
            }
            var promedio = (suma * 100) / data[0].empleado[0].totalE;
            totalP = Math.round(promedio);
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
                    layout: {
                        padding: {
                            bottom: 50,
                            top: 50,
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 80,
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            afterTitle: function (tooltipItem, data) {
                                var label = 'Cantidad de Empleados';
                                return label;
                            },
                            afterLabel: function (tooltipItem, data) {
                                var dataset = data["datasets"][0];
                                var percent = Math.round((dataset["data"][tooltipItem["index"]] * 100 /
                                    suma));
                                grafico.options.elements.center.text = percent + "%" + data["labels"][tooltipItem["index"]];
                            }
                        }
                    },
                    elements: {
                        center: {
                            text: suma + '\nempleados en área',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        },
                    },
                    plugins: {
                        display: true,
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
                                fontSize: 20
                            },
                            padding: 10,
                        }
                    }
                },
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = suma + '\nempleados en área';
            });
        } else {
            $('#divarea').hide();
            $.notify({
                message: "\nAún no has asignado empleados a una área.",
                icon: 'admin/images/warning.svg'
            });
        }
    },
    error: function (data) {
        $.notify("\nEror en área.");
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
        var color = ['#21bf73', '#ffd31d', '#eb4559'];
        var suma = 0;
        var totalP = 0;
        if (data[0].nivel.length != 0) {
            for (var i = 0; i < data[0].nivel.length; i++) {
                nombre.push(data[0].nivel[i].nivel_descripcion);
                total.push(data[0].nivel[i].Total);
                suma += data[0].nivel[i].Total;
            }
            for (var j = 3; j < data[0].nivel.length; j++) {
                color.push(getRandomColor());
            }
            var promedio = (suma * 100) / data[0].empleado[0].totalE;
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
                    layout: {
                        padding: {
                            bottom: 50,
                            top: 50
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 80,
                    legend: {
                        display: false
                    },
                    plugins: {
                        display: true,
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
                                fontSize: 20
                            },
                            padding: 5,
                        }
                    },
                    tooltips: {
                        intersect: false,
                        callbacks: {
                            afterTitle: function (tooltipItem, data) {
                                var label = 'Cantidad de Empleados';
                                return label;
                            },
                            afterLabel: function (tooltipItem, data) {
                                var dataset = data["datasets"][0];
                                var percent = Math.round((dataset["data"][tooltipItem["index"]] * 100 / suma));
                                grafico.options.elements.center.text = percent + "%" + data["labels"][tooltipItem["index"]];
                            },
                        }
                    },
                    elements: {
                        center: {
                            text: suma + '\nempleados en nivel',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    },
                }
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = suma + '\nempleados en nivel';
            });
        } else {
            $('#divnivel').hide();
            $.notify({
                message: "\nAún no has asignado empleados a un nivel.",
                icon: 'admin/images/warning.svg'
            });
        }
    },
    error: function (data) {
        $.notify("Error en nivel.");
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
        var color = ['#21bf73', '#ffd31d', '#eb4559'];
        var suma = 0;
        var totalP = 0;
        if (data[0].contrato.length != 0) {
            for (var i = 0; i < data[0].contrato.length; i++) {
                nombre.push(data[0].contrato[i].contrato_descripcion);
                total.push(data[0].contrato[i].Total);
                suma += data[0].contrato[i].Total;
            }
            for (var j = 3; j < data[0].contrato.length; j++) {
                color.push(getRandomColor());
            }
            var promedio = (suma * 100) / data[0].empleado[0].totalE;
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
                    layout: {
                        padding: {
                            bottom: 50,
                            top: 50
                        }
                    },
                    responsive: true,
                    cutoutPercentage: 80,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            display: true,
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
                                fontSize: 20
                            },
                            padding: 5,
                        }
                    },
                    tooltips: {
                        callbacks: {
                            afterTitle: function (tooltipItem, data) {
                                var label = 'Cantidad de Empleados';
                                return label;
                            },
                            afterLabel: function (tooltipItem, data) {
                                var dataset = data["datasets"][0];
                                var percent = Math.round((dataset["data"][tooltipItem["index"]] * 100 / suma));
                                grafico.options.elements.center.text = percent + "%" + data["labels"][tooltipItem["index"]];
                            }
                        }
                    },
                    elements: {
                        center: {
                            text: suma + '\nempleados en contrato',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = suma + '\nempleados en contrato';
            });
        } else {
            $('#divcontrato').hide();
            $.notify({
                message: "\nAún no has asignado empleados a un tipo de contrato.",
                icon: 'admin/images/warning.svg'
            });
        }
    },
    error: function (data) {
        $.notify("Error en  tipo de contrato.");
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
        var color = ['#21bf73', '#ffd31d', '#eb4559'];
        var suma = 0;
        if (data[0].centro.length != 0) {
            for (var i = 0; i < data[0].centro.length; i++) {
                nombre.push(data[0].centro[i].centroC_descripcion);
                total.push(data[0].centro[i].Total);
                suma += data[0].centro[i].Total;
            }
            for (var j = 3; j < data[0].centro.length; j++) {
                color.push(getRandomColor());
            }
            var promedio = (suma * 100) / data[0].empleado[0].totalE;
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
                    layout: {
                        padding: {
                            bottom: 50,
                            top: 50
                        }
                    },
                    responsive: true,
                    cutoutPercentage: 80,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            display: true,
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
                                fontSize: 20
                            },
                            padding: 5,
                        }
                    },
                    tooltips: {
                        callbacks: {
                            afterTitle: function (tooltipItem, data) {
                                var label = 'Cantidad de Empleados';
                                return label;
                            },
                            afterLabel: function (tooltipItem, data) {
                                var dataset = data["datasets"][0];
                                var percent = Math.round((dataset["data"][tooltipItem["index"]] * 100 / suma));
                                grafico.options.elements.center.text = percent + "%" + data["labels"][tooltipItem["index"]];
                            }
                        }
                    },
                    elements: {
                        center: {
                            text: suma + '\nempleados en CC',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = suma + '\nempleados en CC';
            });
        } else {
            $('#divcentro').hide();
            $.notify({
                message: "\nAún no has asignado empleados a un tipo de centro costo.",
                icon: 'admin/images/warning.svg'
            });
        }
    },
    error: function (data) {
        $.notify("Error en tipo de centro costo.");
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
        var color = ['#21bf73', '#ffd31d', '#eb4559'];
        var suma = 0;
        var totalP = 0;
        if (data[0].local.length != 0) {
            for (var i = 0; i < data[0].local.length; i++) {
                nombre.push(data[0].local[i].local_descripcion);
                total.push(data[0].local[i].Total);
                suma += data[0].local[i].Total;
            }
            for (var j = 3; j < data[0].local.length; j++) {
                color.push(getRandomColor());
            }
            var promedio = (suma * 100) / data[0].empleado[0].totalE;
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
                    layout: {
                        padding: {
                            bottom: 50,
                            top: 50
                        }
                    },
                    responsive: true,
                    cutoutPercentage: 80,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            display: true,
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
                                fontSize: 20
                            },
                            padding: 5,
                        }
                    },
                    tooltips: {
                        callbacks: {
                            afterTitle: function (tooltipItem, data) {
                                var label = 'Cantidad de Empleados';
                                return label;
                            },
                            afterLabel: function (tooltipItem, data) {
                                var dataset = data["datasets"][0];
                                var percent = Math.round((dataset["data"][tooltipItem["index"]] * 100 / suma));
                                grafico.options.elements.center.text = percent + "%" + data["labels"][tooltipItem["index"]];
                            }
                        }
                    },
                    elements: {
                        center: {
                            text: suma + '\nempleados en local',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = suma + '\nempleados en local';
            });
        } else {
            $('#divlocal').hide();
            $.notify({
                message: "\nAún no has asignado empleados a un local.",
                icon: 'admin/images/warning.svg'
            });
        }
    },
    error: function (data) {
        $.notify("Error en local.");
    }
});
//EDAD
/*$.ajax({
    url: "totalE",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#21bf73', '#ffd31d', '#eb4559'];
        var suma = 0;
        var totalP = 0;
        if (data[0].edad.length != 0) {
            for (var i = 0; i < data[0].edad.length; i++) {
                nombre.push(data[0].edad[i].edad);
                total.push(data[0].edad[i].total);
                suma += data[0].edad[i].total;
            }
            for (var j = 3; j < data[0].edad.length; j++) {
                color.push(getRandomColor());
            }
            var promedio = (suma * 100) / data[0].empleado[0].totalE;
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
                    layout: {
                        padding: {
                            bottom: 40,
                            top: 40
                        }
                    },
                    responsive: true,
                    cutoutPercentage: 80,
                    maintainAspectRatio: false,
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
                    tooltips: {
                        callbacks: {
                            afterLabel: function (tooltipItem, data) {
                                var dataset = data["datasets"][0];
                                var percent = Math.round((dataset["data"][tooltipItem["index"]] * 100 / suma));
                                grafico.options.elements.center.text = percent + "%" + data["labels"][tooltipItem["index"]];
                            }
                        }
                    },
                    elements: {
                        center: {
                            text: suma + '\nempleados por rango',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
        } else {
            $('#divedades').hide();
            $.notify({
                message: " Aún no has asignado empleados a un local.",
                icon: 'admin/images/warning.svg'
            });
        }
    },
    error: function (data) {
        $.notify(" Aún no has asignado empleados a un local.");
    }
});*/
//DEPARTAMENTO
$.ajax({
    url: "totalDepartamento",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#21bf73', '#ffd31d', '#eb4559'];
        var suma = 0;
        var totalP = 0;
        if (data[0].departamento.length != 0) {
            for (var i = 0; i < data[0].departamento.length; i++) {
                nombre.push(data[0].departamento[i].name);
                total.push(data[0].departamento[i].total);
                suma += data[0].departamento[i].total;
            }
            for (var j = 3; j < data[0].departamento.length; j++) {
                color.push(getRandomColor());
            }
            var promedio = (suma * 100) / data[0].empleado[0].totalE;
            totalP = Math.round(promedio);
            var chartdata = {
                labels: nombre,
                datasets: [{
                    data: total,
                    backgroundColor: color,
                    borderWidth: 0
                }]
            };
            var mostrar = $('#departamento');
            var grafico = new Chart(mostrar, {
                type: 'doughnut',
                data: chartdata,
                options: {
                    layout: {
                        padding: {
                            bottom: 50,
                            top: 50
                        }
                    },
                    responsive: true,
                    cutoutPercentage: 80,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            display: true,
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
                                fontSize: 20
                            },
                            padding: 5,
                        }
                    },
                    tooltips: {
                        callbacks: {
                            afterTitle: function (tooltipItem, data) {
                                var label = 'Cantidad de Empleados';
                                return label;
                            },
                            afterLabel: function (tooltipItem, data) {
                                var dataset = data["datasets"][0];
                                var percent = Math.round((dataset["data"][tooltipItem["index"]] * 100 / suma));
                                grafico.options.elements.center.text = percent + "%" + data["labels"][tooltipItem["index"]];
                            }
                        }
                    },
                    elements: {
                        center: {
                            text: suma + '\nempleados en ciudad',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = suma + '\nempleados en ciudad';
            });
        } else {
            $('#divdepartamento').hide();
            $.notify({
                message: "\nAún no has asignado empleados a una ciudad.",
                icon: 'admin/images/warning.svg'
            });
        }
    },
    error: function (data) {
        $.notify("Error en edad.");
    }
});
//RANGO DE EDAD
$.ajax({
    url: "totalRE",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#21bf73', '#ffd31d', '#eb4559'];
        var suma = 0;
        var totalP = 0;
        if (data[0].edad.length != 0) {
            for (var i = 0; i < data[0].edad.length; i++) {
                nombre.push(data[0].edad[i].rango);
                total.push(data[0].edad[i].total);
                suma += data[0].edad[i].total;
            }
            for (var j = 3; j < data[0].edad.length; j++) {
                color.push(getRandomColor());
            }
            var promedio = (suma * 100) / data[0].empleado[0].totalE;
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
                    layout: {
                        padding: {
                            bottom: 50,
                            top: 50
                        }
                    },
                    responsive: true,
                    cutoutPercentage: 80,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    plugins: {
                        display: true,
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
                                fontSize: 20
                            },
                            padding: 10,
                        }
                    },
                    tooltips: {
                        callbacks: {
                            afterTitle: function (tooltipItem, data) {
                                var label = 'Cantidad de Empleados';
                                return label;
                            },
                            afterLabel: function (tooltipItem, data) {
                                var dataset = data["datasets"][0];
                                var percent = Math.round((dataset["data"][tooltipItem["index"]] * 100 / suma));
                                grafico.options.elements.center.text = percent + "%" + data["labels"][tooltipItem["index"]];
                            }
                        }
                    },
                    elements: {
                        center: {
                            text: suma + '\nempleados por rango',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = suma + '\nempleados por rango';
            });
        } else {
            $('#divedades').hide();
            $.notify({
                message: "\nAún no has asignado empleados a un local.",
                icon: 'admin/images/warning.svg'
            });
        }
    },
    error: function (data) {
        $.notify("Error en local.");
    }
});
