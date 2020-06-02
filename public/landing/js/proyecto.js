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
            $('#myModal').modal('hide');
            $('#tablaProyecto').load(location.href+" #tablaProyecto>*");
            $.notify("proyecto registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});

        },
        error:function(){ alert("Hay un error");}
    });
}


    function abrirM(id) {
        $.ajax({
            type:"POST",
            url:"/proyecto/proyectoV",
            data:{id},
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(data){
              $('#nombre1').val(data.Proye_Nombre);
              $('#id1').val(data.Proye_id);
              $('#myModal1').modal('toggle');

            },
            error:function(){ alert("Hay un error");}
        });

    };

function registrarPE(){
    var proyecto=$('#id1').val();
    var empleado= $('#idempleado').val();
    if(empleado==''){
        alert('Seleccione empleado')
        return false;
    }
    $.ajax({
        type:"POST",
        url:"/proyecto/registrarPrEm",
        data:{proyecto,empleado},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            $('#idempleado').val('');
            $('#myModal1').modal('hide');
            $('#tablaProyecto').load(location.href+" #tablaProyecto>*");
            $.notify("empleado registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});

        },
        error:function(){ alert("Hay un error");}
    });



}
