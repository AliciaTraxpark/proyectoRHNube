const registrar = document.getElementById("botonRegistrar");
  registrar.addEventListener('click',function(e){
   e.preventDefault(); 
	
$.ajax({
  headers: {
          'X-CSRF-TOKEN': document.getElementsByName("_token")[0].value
          },
    url:"/persona",
    data:{
        'nombres': document.getElementById("nombres").value, 
        'apellidos': document.getElementById("apellidos").value, 
        'usuario': document.getElementById("usuario").value, 
        'password': document.getElementById("password").value, 
        'fecha': document.getElementById("reservationDate").value 
    },
    type:"post",
    success: function (datos) {
    
    },
    error:function (error) {
        //valido que llegue errors
        const mensaje = document.getElementById("mensaje");
            //valido que tenga el error nombre
            if(error.responseJSON.errors.nombres){
                mensaje.innerHTML = error.responseJSON.errors.nombres
            }
            if(error.responseJSON.errors.apellidos){
                mensaje.innerHTML = error.responseJSON.errors.apellidos
            }
            if(error.responseJSON.errors.usuarios){
                mensaje.innerHTML = error.responseJSON.errors.usuarios
            }
            if(error.responseJSON.errors.password){
                mensaje.innerHTML = error.responseJSON.errors.password
            }
            if(error.responseJSON.errors.fecha){
                mensaje.innerHTML = error.responseJSON.errors.fecha
            }
    }
});

}, false);