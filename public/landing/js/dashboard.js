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
// 
$('#divarea').hide();
$('#divnivel').hide();
$('#divcontrato').hide();
$('#divcentro').hide();
$('#divlocal').hide();
$('#divdepartamento').hide();
$('#divedades').hide();
//NOTIFICACION
$.notifyDefaults({
    icon_type: 'image',
    delay: 12000,
    timer: 10000,
    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">??</button>' +
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
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
        var suma = 0;
        var totalP = 0;
        $('#fechaDepartamento').empty();
        $('#panel1002D').empty();
        var containerFecha = $('#fechaDepartamento');
        var containerDetalle = $('#panel1002D');
        if (data[0].departamento.length != 0) {
            $('#divdepartamento').show();
            for (var i = 0; i < data[0].departamento.length; i++) {
                if (data[0].departamento[i].name == null) {
                    nombre.push("No definido");
                } else {
                    nombre.push(data[0].departamento[i].name);
                }
                total.push(data[0].departamento[i].total);
                suma += data[0].departamento[i].total;
            }
            for (var j = 3; j < data[0].departamento.length; j++) {
                color.push(getRandomColor());
            }
            // CARD
            f = new Date();
            var options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var fecha = f.toLocaleDateString("es-PE", options)
            fechaF = `<img src="admin/images/calendarioHor.svg" height="20" class="mr-2"> ${fecha}`;
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
            var mostrar = $('#departamento');
            var grafico = new Chart(mostrar, {
                type: 'doughnut',
                data: chartdata,
                options: {
                    responsive: true,
                    layout: {
                        padding: {
                            bottom: 70,
                            top: 70
                        }
                    },
                    title: {
                        display: false
                    },
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
            document.getElementById('js-legendDep').innerHTML = grafico.generateLegend();
        } else {
            $('#divdepartamento').hide();
        }
    },
    error: function (data) { }
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
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
        var suma = 0;
        var totalP = 0;
        $('#fechaContrato').empty();
        $('#panel1002C').empty();
        var containerFecha = $('#fechaContrato');
        var containerDetalle = $('#panel1002C');
        if (data[0].contrato.length != 0) {
            $('#divcontrato').show();
            for (var i = 0; i < data[0].contrato.length; i++) {
                if (data[0].contrato[i].contrato_descripcion == null) {
                    nombre.push("No definido");
                } else {
                    nombre.push(data[0].contrato[i].contrato_descripcion);
                }
                total.push(data[0].contrato[i].Total);
                suma += data[0].contrato[i].Total;
            }
            for (var j = 3; j < data[0].contrato.length; j++) {
                color.push(getRandomColor());
            }
            // CARD
            f = new Date();
            var options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var fecha = f.toLocaleDateString("es-PE", options)
            fechaF = `<img src="admin/images/calendarioHor.svg" height="20" class="mr-2"> ${fecha}`;
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
        }
    },
    error: function (data) { }
});
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
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
        var suma = 0;
        var totalP = 0;
        $('#fechaArea').empty();
        $('#panel1002A').empty();
        var containerFecha = $('#fechaArea');
        var containerDetalle = $('#panel1002A');
        if (data[0].area.length != 0) {
            $('#divarea').show();
            for (var i = 0; i < data[0].area.length; i++) {
                suma += data[0].area[i].Total;
                if (data[0].area[i].area_descripcion == null) {
                    nombre.push("No definido");
                } else {
                    nombre.push(data[0].area[i].area_descripcion);
                }
                total.push(data[0].area[i].Total);
            }
            for (var j = 3; j < data[0].area.length; j++) {
                color.push(getRandomColor());
            }
            // CARD
            f = new Date();
            var options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var fecha = f.toLocaleDateString("es-PE", options)
            fechaF = `<img src="/admin/images/calendarioHor.svg" height="20" class="mr-2"> ${fecha}`;
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
        }
    },
    error: function (data) { }
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
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
        var suma = 0;
        var totalP = 0;
        $('#fechaEdades').empty();
        $('#panel1002E').empty();
        var containerFecha = $('#fechaEdades');
        var containerDetalle = $('#panel1002E');
        if (data[0].edad.length != 0) {
            $('#divedades').show();
            for (var i = 0; i < data[0].edad.length; i++) {
                if (data[0].edad[i].rango == null) {
                    nombre.push("No definido");
                } else {
                    nombre.push(data[0].edad[i].rango);
                }
                total.push(data[0].edad[i].total);
                suma += data[0].edad[i].total;
            }
            for (var j = 3; j < data[0].edad.length; j++) {
                color.push(getRandomColor());
            }
            // CARD
            f = new Date();
            var options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var fecha = f.toLocaleDateString("es-PE", options)
            fechaF = `<img src="admin/images/calendarioHor.svg" height="20" class="mr-2"> ${fecha}`;
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
            document.getElementById('js-legendEdades').innerHTML = grafico.generateLegend();
        } else {
            $('#divedades').hide();
        }
    },
    error: function (data) { }
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
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
        var suma = 0;
        var totalP = 0;
        $('#fechaCentro').empty();
        $('#panel1002CC').empty();
        var containerFecha = $('#fechaCentro');
        var containerDetalle = $('#panel1002CC');
        if (data[0].centro.length != 0) {
            $('#divcentro').show();
            for (var i = 0; i < data[0].centro.length; i++) {
                nombre.push(data[0].centro[i].centroC_descripcion);
                total.push(data[0].centro[i].Total);
                suma += data[0].centro[i].Total;
            }
            // : NO DEFINIDO
            var noDefinido = (data[0].empleado[0].totalE - suma);
            if (noDefinido != 0) {
                nombre.push("No definido");
                total.push(noDefinido);
                suma += noDefinido;
            }
            for (var j = 3; j < (data[0].centro.length + 1); j++) {
                color.push(getRandomColor());
            }
            // CARD
            f = new Date();
            var options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var fecha = f.toLocaleDateString("es-PE", options)
            fechaF = `<img src="admin/images/calendarioHor.svg" height="20" class="mr-2"> ${fecha}`;
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
        }
    },
    error: function (data) { }
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
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
        var suma = 0;
        var totalP = 0;
        $('#fechaLocal').empty();
        $('#panel1002L').empty();
        var containerFecha = $('#fechaLocal');
        var containerDetalle = $('#panel1002L');
        if (data[0].local.length != 0) {
            $('#divlocal').show();
            for (var i = 0; i < data[0].local.length; i++) {
                if (data[0].local[i].local_descripcion == null) {
                    nombre.push("No definido");
                } else {
                    nombre.push(data[0].local[i].local_descripcion);
                }
                total.push(data[0].local[i].Total);
                suma += data[0].local[i].Total;
            }
            for (var j = 3; j < data[0].local.length; j++) {
                color.push(getRandomColor());
            }
            // CARD
            f = new Date();
            var options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var fecha = f.toLocaleDateString("es-PE", options)
            fechaF = `<img src="admin/images/calendarioHor.svg" height="20" class="mr-2"> ${fecha}`;
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
            document.getElementById('js-legendLocal').innerHTML = grafico.generateLegend();
        } else {
            $('#divlocal').hide();
        }
    },
    error: function (data) { }
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
        var color = ['#b6eb7a', '#f9d56e', '#e84a5f'];
        var suma = 0;
        var totalP = 0;
        $('#fechaNivel').empty();
        $('#panel1002N').empty();
        var containerFecha = $('#fechaNivel');
        var containerDetalle = $('#panel1002N');
        if (data[0].nivel.length != 0) {
            $('#divnivel').show();
            for (var i = 0; i < data[0].nivel.length; i++) {
                if (data[0].nivel[i].nivel_descripcion == null) {
                    nombre.push("No definido");
                } else {
                    nombre.push(data[0].nivel[i].nivel_descripcion);
                }
                total.push(data[0].nivel[i].Total);
                suma += data[0].nivel[i].Total;
            }
            for (var j = 3; j < data[0].nivel.length; j++) {
                color.push(getRandomColor());
            }
            // CARD
            f = new Date();
            var options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            var fecha = f.toLocaleDateString("es-PE", options)
            fechaF = `<img src="admin/images/calendarioHor.svg" height="20" class="mr-2"> ${fecha}`;
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
        }
    },
    error: function (data) { }
});