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
    $.ajax({
        url:"reporte/empleado",
        method: "GET",
        data:{fecha:fecha},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            var container = $('#empleado');
            var html_tr = "";
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
            container.append(html_tr);
        },
        error:function(data){
        }
    })
}