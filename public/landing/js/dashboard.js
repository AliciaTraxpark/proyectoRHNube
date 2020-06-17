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
        for (var i = 0; i < data.length; i++) {
            nombre.push(data[i].area_descripcion);
            total.push(data[i].Total);
        }
        var chartdata = {
            labels: nombre,
            datasets: [{
                data: total,
                backgroundColor: color,
                borderWidth: 1
            }]
        };
        var mostrar = $('#area');
        var grafico = new Chart(mostrar, {
            type: 'doughnut',
            data: chartdata,
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
                        text: '350 por Área',
                        color: '#1f4068', //Default black
                        fontFamily: 'Arial', //Default Arial
                        sidePadding: 35,
                    }
                }
            }
        })
    },
    error: function (data) {}
})
