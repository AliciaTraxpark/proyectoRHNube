$('#fecha').flatpickr({
    mode: "range",
    inline: false,
    locale:"es",
    maxDate: "today"
});
$(function(){
    $("#fecha").on('change',onSelectFechas);
});

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
            var color = ['rgb(255, 99, 132)'];
            var borderColor = ['rgb(255, 99, 132)'];
            var html_tr = "";
            var html_trD = "<tr><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
            for(var i=0; i<data.length; i++){
                html_tr += '<tr><td>'+ data[i].nombre + ' ' + data[i].apPaterno + ' ' + data[i].apMaterno + '</td>';
                for(let j = 0; j < data[i].horas.length; j++){
                    if(data[i].horas[j] == null){
                        html_tr += '<td>No Trabajo</td>';
                    }else{
                        html_tr += '<td>'+ data[i].horas[j] + '</td>';
                        horas.push(data[i].horas[j]);
                        console.log(horas);
                    }
                }
                html_tr += '</tr>';
            }
            for(var m = 0; m < data[0].fechaF.length; m++){
                var momentValue = moment(data[0].fechaF[m]);
                    momentValue.toDate();
                    momentValue.format("ddd");
                    html_trD += '<th>'+momentValue.format("ddd")+'</th>';
                    nombre.push(momentValue.format("ddd"));
            }
            html_trD += '</tr>';
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
                }
            });
        },
        error:function(data){
        }
    })
}