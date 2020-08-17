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
    delay: 12000,
    timer: 10000,
    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
        '<span data-notify="title">{1}</span> ' +
        '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
        '</div>',
    spacing: 35
});
//COLORES
function getRandomColor() {
    var letters = 'ABCDE'.split('');
    var color = '#';
    for (var i = 0; i < 3; i++) {
        color += letters[Math.floor(Math.random() * letters.length)];
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
    statusCode: {
        /*401: function () {
            location.reload();
        },*/
        419: function () {
            location.reload();
        }
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
        var suma = 0;
        var totalP = 0;
        $('#cantidadArea').empty();
        $('#fechaArea').empty();
        $('#panel1002A').empty();
        var containerCantidadA = $('#cantidadArea');
        var containerFecha = $('#fechaArea');
        var containerDetalle = $('#panel1002A');
        if (data[0].area.length != 0) {
            for (var i = 0; i < data[0].area.length; i++) {
                suma += data[0].area[i].Total;
                nombre.push(data[0].area[i].area_descripcion);
                total.push(data[0].area[i].Total);
            }
            for (var j = 3; j < data[0].area.length; j++) {
                color.push(getRandomColor());
            }
            // CARD
            p = `<img src="landing/images/grupo.svg" height="18" class="mr-2"> Total de ${suma} empleado(s)`;
            f = new Date();
            var options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var fecha = f.toLocaleDateString("es-PE", options)
            fechaF = `<img src="admin/images/calendarioHor.svg" height="20" class="mr-2"> ${fecha}`;
            containerCantidadA.append(p);
            containerFecha.append(fechaF);
            var detalle = ``;
            for (var l = 0; l < nombre.length; l++) {
                detalle += `<p align="justify" class="font-small text-muted mx-1">\n 
                <img src="landing/images/2143150.png" class="mr-2" height="20"/>
                <span style="color:${color[l]};font-weight:bold;">${nombre[l]}</span> tiene un total de ${total[l]} empleado(s).
                </p>`;
            }
            containerDetalle.append(detalle);
            // GRAFICO
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
                            bottom: 70,
                            top: 70,
                        }
                    },
                    title: {
                        display: false
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 80,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            display: false,
                        }
                    },
                    tooltips: {
                        yAlign: 'bottom',
                        xAlign: 'center',
                        callbacks: {
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
                            text: '\n' + data[0].organizacion.organi_razonSocial,
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        },
                    },
                },
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = '\n' + data[0].organizacion.organi_razonSocial;
            });
            document.getElementById('js-legendArea').innerHTML = grafico.generateLegend();
        } else {
            $('#divarea').hide();
            $.notify({
                message: "\n\nAún no has asignado empleados a una área.<br><a id=\"empleadoA\" target=\"_blank\" style=\"cursor: pointer;\"><button class=\"boton btn btn-default mr-1 spinner-grow spinner-grow-sm\"></button></a>",
                icon: 'admin/images/warning.svg',
            }, {
                mouse_over: "pause"
            });
            $('#empleadoA').click(function () {
                window.location.replace(
                    location.origin + "/empleado"
                );
            });
        }
    },
    error: function (data) {}
});
//NIVEL
$.ajax({
    url: "totalN",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    statusCode: {
        /*401: function () {
            location.reload();
        },*/
        419: function () {
            location.reload();
        }
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
        var suma = 0;
        var totalP = 0;
        $('#cantidadNivel').empty();
        $('#fechaNivel').empty();
        $('#panel1002N').empty();
        var containerCantidadA = $('#cantidadNivel');
        var containerFecha = $('#fechaNivel');
        var containerDetalle = $('#panel1002N');
        if (data[0].nivel.length != 0) {
            for (var i = 0; i < data[0].nivel.length; i++) {
                nombre.push(data[0].nivel[i].nivel_descripcion);
                total.push(data[0].nivel[i].Total);
                suma += data[0].nivel[i].Total;
            }
            for (var j = 3; j < data[0].nivel.length; j++) {
                color.push(getRandomColor());
            }
            // CARD
            p = `<img src="landing/images/grupo.svg" height="18" class="mr-2"> Total de ${suma} empleado(s)`;
            f = new Date();
            var options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var fecha = f.toLocaleDateString("es-PE", options)
            fechaF = `<img src="admin/images/calendarioHor.svg" height="20" class="mr-2"> ${fecha}`;
            containerCantidadA.append(p);
            containerFecha.append(fechaF);
            var detalle = ``;
            for (var l = 0; l < nombre.length; l++) {
                detalle += `<p align="justify" class="font-small text-muted mx-1">\n 
                <img src="landing/images/2143150.png" class="mr-2" height="20"/>
                <span style="color:${color[l]};font-weight:bold;">${nombre[l]}</span> tiene un total de ${total[l]} empleado(s).
                </p>`;
            }
            containerDetalle.append(detalle);
            //GRAFICO
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
                            bottom: 70,
                            top: 70
                        }
                    },
                    title: {
                        display: false
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    cutoutPercentage: 80,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            display: false,
                        }
                    },
                    tooltips: {
                        yAlign: 'bottom',
                        xAlign: 'center',
                        callbacks: {
                            afterLabel: function (tooltipItem, data) {
                                var dataset = data["datasets"][0];
                                var percent = Math.round((dataset["data"][tooltipItem["index"]] * 100 / suma));
                                grafico.options.elements.center.text = percent + "%" + data["labels"][tooltipItem["index"]];
                            },
                        }
                    },
                    elements: {
                        center: {
                            text: '\n' + data[0].organizacion.organi_razonSocial,
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    },
                }
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = '\n' + data[0].organizacion.organi_razonSocial;
            });
            document.getElementById('js-legendNivel').innerHTML = grafico.generateLegend();
        } else {
            $('#divnivel').hide();
            $.notify({
                message: "\n\nAún no has asignado empleados a un nivel.<br><a id=\"empleadoN\" target=\"_blank\" style=\"cursor: pointer;\"><button class=\"boton btn btn-default mr-1 spinner-grow spinner-grow-sm\"></button></a>",
                icon: 'admin/images/warning.svg'
            }, {
                mouse_over: "pause"
            });
            $('#empleadoN').click(function () {
                window.location.replace(
                    location.origin + "/empleado"
                );
            });
        }
    },
    error: function (data) {}
});
//CONTRATO
$.ajax({
    url: "totalC",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    statusCode: {
        /*401: function () {
            location.reload();
        },*/
        419: function () {
            location.reload();
        }
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
        var suma = 0;
        var totalP = 0;
        $('#cantidadContrato').empty();
        $('#fechaContrato').empty();
        $('#panel1002C').empty();
        var containerCantidadA = $('#cantidadContrato');
        var containerFecha = $('#fechaContrato');
        var containerDetalle = $('#panel1002C');
        if (data[0].contrato.length != 0) {
            for (var i = 0; i < data[0].contrato.length; i++) {
                nombre.push(data[0].contrato[i].contrato_descripcion);
                total.push(data[0].contrato[i].Total);
                suma += data[0].contrato[i].Total;
            }
            for (var j = 3; j < data[0].contrato.length; j++) {
                color.push(getRandomColor());
            }
            // CARD
            p = `<img src="landing/images/grupo.svg" height="18" class="mr-2"> Total de ${suma} empleado(s)`;
            f = new Date();
            var options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var fecha = f.toLocaleDateString("es-PE", options)
            fechaF = `<img src="admin/images/calendarioHor.svg" height="20" class="mr-2"> ${fecha}`;
            containerCantidadA.append(p);
            containerFecha.append(fechaF);
            var detalle = ``;
            for (var l = 0; l < nombre.length; l++) {
                detalle += `<p align="justify" class="font-small text-muted mx-1">\n 
                <img src="landing/images/2143150.png" class="mr-2" height="20"/>
                <span style="color:${color[l]};font-weight:bold;">${nombre[l]}</span> tiene un total de ${total[l]} empleado(s).
                </p>`;
            }
            containerDetalle.append(detalle);
            //GRAFICO
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
                            bottom: 70,
                            top: 70
                        }
                    },
                    title: {
                        display: false,
                    },
                    responsive: true,
                    cutoutPercentage: 80,
                    maintainAspectRatio: false,
                    legend: {
                        display: false,
                    },
                    plugins: {
                        datalabels: {
                            display: false
                        }
                    },
                    tooltips: {
                        yAlign: 'bottom',
                        xAlign: 'center',
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
                            text: '\n' + data[0].organizacion.organi_razonSocial,
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = '\n' + data[0].organizacion.organi_razonSocial;
            });
            document.getElementById('js-legendContrato').innerHTML = grafico.generateLegend();
        } else {
            $('#divcontrato').hide();
            $.notify({
                message: "\n\nAún no has asignado empleados a un tipo de contrato.<br><a id=\"empleadoC\" target=\"_blank\" style=\"cursor: pointer;\"><button class=\"boton btn btn-default mr-1 spinner-grow spinner-grow-sm\"></button></a>",
                icon: 'admin/images/warning.svg'
            }, {
                mouse_over: "pause"
            });
            $('#empleadoC').click(function () {
                window.location.replace(
                    location.origin + "/empleado"
                );
            });
        }
    },
    error: function (data) {}
});
//CENTRO
$.ajax({
    url: "totalCC",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    statusCode: {
        /*401: function () {
            location.reload();
        },*/
        419: function () {
            location.reload();
        }
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
        var suma = 0;
        $('#cantidadCentro').empty();
        $('#fechaCentro').empty();
        $('#panel1002CC').empty();
        var containerCantidadA = $('#cantidadCentro');
        var containerFecha = $('#fechaCentro');
        var containerDetalle = $('#panel1002CC');
        if (data[0].centro.length != 0) {
            for (var i = 0; i < data[0].centro.length; i++) {
                nombre.push(data[0].centro[i].centroC_descripcion);
                total.push(data[0].centro[i].Total);
                suma += data[0].centro[i].Total;
            }
            for (var j = 3; j < data[0].centro.length; j++) {
                color.push(getRandomColor());
            }
            // CARD
            p = `<img src="landing/images/grupo.svg" height="18" class="mr-2"> Total de ${suma} empleado(s)`;
            f = new Date();
            var options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var fecha = f.toLocaleDateString("es-PE", options)
            fechaF = `<img src="admin/images/calendarioHor.svg" height="20" class="mr-2"> ${fecha}`;
            containerCantidadA.append(p);
            containerFecha.append(fechaF);
            var detalle = ``;
            for (var l = 0; l < nombre.length; l++) {
                detalle += `<p align="justify" class="font-small text-muted mx-1">\n 
                 <img src="landing/images/2143150.png" class="mr-2" height="20"/>
                 <span style="color:${color[l]};font-weight:bold;">${nombre[l]}</span> tiene un total de ${total[l]} empleado(s).
                 </p>`;
            }
            containerDetalle.append(detalle);
            //GRAFICO
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
                            bottom: 70,
                            top: 70
                        }
                    },
                    title: {
                        display: false
                    },
                    responsive: true,
                    cutoutPercentage: 80,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            display: false
                        }
                    },
                    tooltips: {
                        yAlign: 'bottom',
                        xAlign: 'center',
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
                            text: '\n' + data[0].organizacion.organi_razonSocial,
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = '\n' + data[0].organizacion.organi_razonSocial;
            });
            document.getElementById('js-legendCentro').innerHTML = grafico.generateLegend();
        } else {
            $('#divcentro').hide();
            $.notify({
                message: "\n\nAún no has asignado empleados a un tipo de centro costo.<br><a id=\"empleadoCe\" target=\"_blank\" style=\"cursor: pointer;\"><button class=\"boton btn btn-default mr-1 spinner-grow spinner-grow-sm\"></button></a>",
                icon: 'admin/images/warning.svg'
            }, {
                mouse_over: "pause"
            });
            $('#empleadoCe').click(function () {
                window.location.replace(
                    location.origin + "/empleado"
                );
            });
        }
    },
    error: function (data) {}
});
//LOCAL
$.ajax({
    url: "totalL",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    statusCode: {
        /*401: function () {
            location.reload();
        },*/
        419: function () {
            location.reload();
        }
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
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
                            bottom: 70,
                            top: 70
                        }
                    },
                    title: {
                        display: false
                    },
                    responsive: true,
                    cutoutPercentage: 80,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            display: false,
                            /*formatter: function (value, context) {
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
                            clamp: true*/
                        }
                    },
                    tooltips: {
                        yAlign: 'bottom',
                        xAlign: 'center',
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
                            text: '\nLOCAL',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = '\nLOCAL';
            });
            document.getElementById('js-legendLocal').innerHTML = grafico.generateLegend();
        } else {
            $('#divlocal').hide();
            $.notify({
                message: "\n\nAún no has asignado empleados a un local.<br><a id=\"empleadoL\" target=\"_blank\" style=\"cursor: pointer;\"><button class=\"boton btn btn-default mr-1 spinner-grow spinner-grow-sm\"></button></a>",
                icon: 'admin/images/warning.svg'
            }, {
                mouse_over: "pause"
            });
            $('#empleadoL').click(function () {
                window.location.replace(
                    location.origin + "/empleado"
                );
            });
        }
    },
    error: function (data) {}
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
        var color = ['#21bf73', '#5A7D9E', '#eb4559'];
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
    statusCode: {
        /*401: function () {
            location.reload();
        },*/
        419: function () {
            location.reload();
        }
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
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
                            bottom: 70,
                            top: 70
                        }
                    },
                    title: {
                        display: false
                    },
                    responsive: true,
                    cutoutPercentage: 80,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            display: false,
                            /*formatter: function (value, context) {
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
                            clamp: true*/
                        }
                    },
                    tooltips: {
                        yAlign: 'bottom',
                        xAlign: 'center',
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
                            text: '\nCIUDAD',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = '\nCIUDAD';
            });
            document.getElementById('js-legendDep').innerHTML = grafico.generateLegend();
        } else {
            $('#divdepartamento').hide();
            $.notify({
                message: "\n\nAún no has asignado empleados a una ciudad.<br><a id=\"empleadoD\" target=\"_blank\" style=\"cursor: pointer;\"><button class=\"boton btn btn-default mr-1 spinner-grow spinner-grow-sm\"></button></a>",
                icon: 'admin/images/warning.svg'
            }, {
                mouse_over: "pause"
            });
            $('#empleado').click(function () {
                window.location.replace(
                    location.origin + "/empleadoD"
                );
            });
        }
    },
    error: function (data) {}
});
//RANGO DE EDAD
$.ajax({
    url: "totalRE",
    method: "GET",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    statusCode: {
        /*401: function () {
            location.reload();
        },*/
        419: function () {
            location.reload();
        }
    },
    success: function (data) {
        var nombre = [];
        var total = [];
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
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
                            bottom: 70,
                            top: 70
                        }
                    },
                    title: {
                        display: false
                    },
                    responsive: true,
                    cutoutPercentage: 80,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    plugins: {
                        datalabels: {
                            display: false,
                            /*formatter: function (value, context) {
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
                            clamp: true*/
                        }
                    },
                    tooltips: {
                        yAlign: 'bottom',
                        xAlign: 'center',
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
                            text: '\nRANGO DE EDADES',
                            color: '#424874', //Default black
                            fontFamily: 'Arial', //Default Arial
                            sidePadding: 20,
                        }
                    }
                }
            });
            mostrar.mouseout(function (e) {
                grafico.options.elements.center.text = '\nRANGO DE EDADES';
            });
            document.getElementById('js-legendEdades').innerHTML = grafico.generateLegend();
        } else {
            $('#divedades').hide();
            $.notify({
                message: "\n\nAún no has asignado empleados fecha nacimiento.<br><a id=\"empleadoRE\" target=\"_blank\" style=\"cursor: pointer;\"><button class=\"boton btn btn-default mr-1 spinner-grow spinner-grow-sm\"></button></a>",
                icon: 'admin/images/warning.svg'
            }, {
                mouse_over: "pause"
            });
            $('#empleadoRE').click(function () {
                window.location.replace(
                    location.origin + "/empleado"
                );
            });
        }
    },
    error: function (data) {}
});
