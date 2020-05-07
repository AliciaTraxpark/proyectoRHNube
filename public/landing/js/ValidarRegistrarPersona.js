const registrar = document.getElementById("botonRegistrar");
  registrar.addEventListener('click',function(e){
   e.preventDefault(); 
	
$.ajax({
  headers: {
          'X-CSRF-TOKEN': document.getElementsByName("_token")[0].value
          },
    url:"/persona",
    data:{
        'name': document.getElementById("name").value, 
        //'apellidos': document.getElementById("apellidos").value, 
        'email': document.getElementById("email").value, 
        'password': document.getElementById("password").value, 
        //'fecha': document.getElementById("reservationDate").value 
        'rol_id': 1
    },
    type:"post",
    success: function (datos) {
        const mensajeRegistro = document.getElementById("mensaje");{
            mensaje.innerHTML = "Registro con Exito"
        }
        document.forms.formularioPersona.reset();
    },
    error:function (error) {
        //valido que llegue errors
        const mensaje = document.getElementById("mensaje");
            //valido que tenga el error nombre
            if(error.responseJSON.errors.name){
                mensaje.innerHTML = error.responseJSON.errors.name
            }
            if(error.responseJSON.errors.email){
                mensaje.innerHTML = error.responseJSON.errors.email
            }
            if(error.responseJSON.errors.password){
                mensaje.innerHTML = error.responseJSON.errors.password
            }
    }
});

}, false);