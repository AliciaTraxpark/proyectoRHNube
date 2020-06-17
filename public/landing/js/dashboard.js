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
                cutoutPercentage: 80,
                tooltips: {
                    callbacks: {
                        title: function (tooltipItem, data) {
                            return data['labels'][tooltipItem[0]['index']];
                        },
                        label: function (tooltipItem, data) {
                            return data['datasets'][0]['data'][tooltipItem['index']];
                        },
                        afterLabel: function (tooltipItem, data) {
                            var dataset = data['datasets'][0];
                            var percent = Math.round((dataset['data'][tooltipItem['index']] / dataset["_meta"][0]['total']) * 100)
                            return '(' + percent + '%)';
                        }
                    },
                    
                }
            }
        })
    },
    error: function (data) {}
})
