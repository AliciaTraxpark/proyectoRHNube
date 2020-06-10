$('#fecha').flatpickr({
    mode: "range",
    inline: false,
    locale:{
        weekdays: {
            shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
            longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],         
        }, 
        months: {
            shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
            longhand: ['Enero', 'Febreo', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        }
    }
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
                html_tr += '<tr><td>'+ data[i].perso_nombre + '</td><td>' + data[i].Total_Envio +'</td></tr>';
            }
            container.append(html_tr);
        },
        error:function(data){
        }
    })
}