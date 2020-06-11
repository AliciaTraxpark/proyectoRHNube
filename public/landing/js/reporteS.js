$('#fecha').flatpickr({
    mode: "range",
    inline: false,
    locale:"es",
    maxDate: "today"
});
$(function(){
    $("#fecha").on('change',onSelectFechas);
});

function acumular60(suma,acumulado){
    if(suma > 60){
        suma = suma - 60;
        acumulado += 1;
        acumular60(suma,acumulado);
    }

    if(suma == 60){
        suma = 0;
        acumulado += 1;
        acumular60(suma,acumulado);
    }
    return[suma,acumulado];
}

function sumarHora(a,b){
    if(!a) return b;
    let resultado = [];
    a = a.split(":").reverse();
    b = b.split(":").reverse();
    let acumulado = 0;
    let sumaAcumulado;
    let suma = parseInt(a[0]) + parseInt(b[0]);
    sumaAcumulado = acumular60(suma,acumulado);
    resultado.push(sumaAcumulado[0]);
    suma = parseInt(a[1]) + parseInt(b[1]) + sumaAcumulado[1];
    acumulado = 0;
    sumaAcumulado = acumular60(suma,acumulado);
    resultado.push(sumaAcumulado[0]);
    suma = parseInt(a[2]) + parseInt(b[2]) + sumaAcumulado[1];
    resultado.push(suma);
    resultado.reverse();
    return resultado.join(":");
}

function onSelectFechas(){
    var fecha = $('#fecha').val();
    $('#empleado').empty();
    $('#dias').empty();
    $.ajax({
        url:"reporte/empleado",
        method: "GET",
        data:{fecha:fecha},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            var container = $('#empleado');
            var containerD = $('#dias');
            var nombre =[];
            var horas=[];
            var color = ['rgb(160, 173, 211)'];
            var borderColor = ['rgb(160, 173, 211)'];
            var html_tr = "";
            var html_trD = "<tr><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
            for(var i=0; i<data.length; i++){
                html_tr += '<tr><td>'+ data[i].nombre + ' ' + data[i].apPaterno + ' ' + data[i].apMaterno + '</td>';
                var total = data[i].horas.reduce(function(a,b){
                    return sumarHora(a,b);
                });
                horas.push(total);
                for(let j = 0; j < data[i].horas.length; j++){
                    if(data[i].horas[j] == null){
                        html_tr += '<td>No Trabajo</td>';
                    }else{
                        html_tr += '<td>'+ data[i].horas[j] + '</td>';
                    }
                }
                html_tr += '<td>'+ total +'</td>';
                html_tr += '</tr>';
            }
            for(var m = 0; m < data[0].fechaF.length; m++){
                var momentValue = moment(data[0].fechaF[m]);
                    momentValue.toDate();
                    momentValue.format("ddd");
                    html_trD += '<th>'+momentValue.format("ddd")+'</th>';
                    nombre.push(momentValue.format("ddd"));
            }
            html_trD += '<th>TOTAL</th></tr>';
            container.append(html_tr);
            containerD.append(html_trD);

            var chartdata = {
                labels: nombre,
                datasets: [{
                    label: nombre,
                    backgroundColor: color,
                    borderColor: color,
                    borderWidth: 2,
                    hoverBackgroundColor: color,
                    hoverBorderColor: borderColor,
                    data:horas
                }]
            };
            var mostrar = $("#myChart");
            var grafico = new Chart(mostrar, {
                type: 'bar',
                data: chartdata,
                options: {
                    responsive: true,
                    scales: {
                        xAxes: [{
                            stacked: true
                        }],
                        yAxes: [{
                            type:       "time",
                            time:       {
                                tooltipFormat: 'HH:mm:ss'
                            },
                            scaleLabel: {
                                display:     true,
                                labelString: 'Date'
                            }
                        }]
                    }
                }
            });
        },
        error:function(data){
        }
    })
}