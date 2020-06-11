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
    $('#myChart').show();
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
            var color = ['rgb(185,204,237)'];
            var borderColor = ['rgb(185,204,237)'];
            var html_tr = "";
            var html_trD = "<tr><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";

            console.log(data[0].fechaF.length);
            if(data[0].fechaF.length == 0){
                $('#tablaReporte').html(tablaDefecto);
            }else{

                for(var i=0; i<data.length; i++){
                    html_tr += '<tr><td>'+ data[i].nombre + ' ' + data[i].apPaterno + ' ' + data[i].apMaterno + '</td>';
                    nombre.push(data[i].nombre.split('')[0]+data[i].apPaterno.split('')[0]+data[i].apMaterno.split('')[0]);
                    var total = data[i].horas.reduce(function(a,b){
                        return sumarHora(a,b);
                    });
                    for(let j = 0; j < data[i].horas.length; j++){
                        if(data[i].horas[j] == null){
                            html_tr += '<td>00:00:00</td>';
                        }else{
                            html_tr += '<td>'+ data[i].horas[j] + '</td>';
                        }
                    }
                    if(total == null){
                        html_tr += '<td>00:00:00</td>';
                    }else{
                        html_tr += '<td>'+ total +'</td>';
                        horas.push(total.split(":")[0]);
                    }
                    html_tr += '</tr>';
                }
                for(var m = 0; m < data[0].fechaF.length; m++){
                    var momentValue = moment(data[0].fechaF[m]);
                        momentValue.toDate();
                        momentValue.format("ddd");
                        html_trD += '<th>'+momentValue.format("ddd")+'</th>';
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
                                stacked: true
                            }]
                        }
                    }
                });
                $('#myChartD').hide();
            }
        },
        error:function(data){
            $('#tablaReporte').html(tablaDefecto);
            $('#myChart').hide();
            $('#myChartD').show();
        }
    })
}