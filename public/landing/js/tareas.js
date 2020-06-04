$(function(){
    $('#empleado').on('change',onMostrarPantallas);
});

function onMostrarPantallas(){
    var value = $('#empleado').val();
    $.ajax({
        url:"tareas/show",
        method: "GET",
        data:{value:value},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            var container = $('#card');
            console.log(data)
            for(var i=0; i<data.length; i++){
                //label += '<label>'+data[i].hora_i+' '+data[i].hora_f+'</label>'
                //image += '<img src="data:image/jpeg;base64,'+data[i].Img+'">';
                //$('#hora').html(label);
                //$('#imagen').html(image);
                card = `<div class="card-body">
                    '<label>'data[i].hora_i+' '+data[i].hora_f+'</label>'
                    '<img src="data:image/jpeg;base64,'+data[i].Img+'">'
                </div>`
            }
            container.append(card);
        },
        error:function(data){
            alert("Hay un error");
            console.log(data)
        }
    })
}