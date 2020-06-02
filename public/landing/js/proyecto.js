function agregarProyecto(){
    var nombre=$('#nombreProyecto').val();
    var descripcion=$('#detalleProyecto').val();
    $.ajax({
        type:"POST",
        url:"/proyecto/registrar",
        data:{nombre,descripcion},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            $('#nombreProyecto').val('');
            $('#detalleProyecto').val('');
            $('#myModal').modal('toggle');
            $('#tablaProyecto').load(location.href+" #tablaProyecto>*");
            $.notify("proyecto registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});

        },
        error:function(){ alert("Hay un error");}
    });
}
