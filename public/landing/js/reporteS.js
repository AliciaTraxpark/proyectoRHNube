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
            var dias =[];
            var html_tr = "";
            var html_trD = "<tr><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
            console.log(data);
            for(var i=0; i<data.length; i++){
                html_tr += '<tr><td>'+ data[i].nombre + ' ' + data[i].apPaterno + ' ' + data[i].apMaterno + '</td>';
                for(let j = 0; j < data[i].horas.length; j++){
                    if(data[i].horas[j] == null){
                        html_tr += '<td>No Trabajo</td>';
                    }else{
                        html_tr += '<td>'+ data[i].horas[j] + '</td>';
                    }
                }
                html_tr += '</tr>';
            }
            for(var m = 0; m < data[0].fechaF.length; m++){
                var momentValue = moment(data[0].fechaF[m]);
                    momentValue.toDate();
                    momentValue.format("ddd");
                    html_trD += '<th>'+momentValue.format("ddd")+'</th>';
                    dias[m] += momentValue.format("ddd");
                    console.log(momentValue.format("ddd"));
            }
            html_trD += '</tr>';
            container.append(html_tr);
            containerD.append(html_trD);
        },
        error:function(data){
        }
    })
}
var ctx = $('#myChart');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
            label: 'My First dataset',
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: [0, 10, 5, 2, 20, 30, 45]
        }]
    },

    // Configuration options go here
    options: {}
});