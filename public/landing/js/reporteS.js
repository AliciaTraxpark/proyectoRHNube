$('#fecha').flatpickr({
    mode: "range",
    inline: false
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
            var html_tr = '<tr><td></td></tr>';
            console.log(data);
            for(var i=0; i<data.length; i++){
                html_tr += '<tr><td>'+ data[i].Total_Envio +'</td></tr>';
            }
            container.append(html_tr);
            console.log(container);
        },
        error:function(data){
            $('#empleado').empty();
        }
    })
}