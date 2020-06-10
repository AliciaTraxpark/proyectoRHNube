flatpickr("#fecha");
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
            var html_td = '';
            for(var i=0; i<data.length; i++){
                html_td += '<td >'+ data[i].perso_nombre +'</td>';
            }
            container.append(html_td);
        },
        error:function(data){
            alert("Hay un error");
        }
    })
}