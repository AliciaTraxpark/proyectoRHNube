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
        $('#idempleado').load(location.href+" #idempleado>*");
        $('#myModal1').modal('toggle');
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


            },
            error:function(){ alert("Hay un error");}
        });
         var $select=$('#idempleado').select2();
       $.ajax({

            type:"POST",
            url:"/proyecto/selectValidar",
            data:{id},
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(data){
                //$('#prue').val(null).trigger('change');
                var array = [];
               /*  $.each(data, function (i, json) {
                    $select.append('<option value="' + json.emple_id + '">' + json.perso_nombre + '</option>');
                  }); */
                  $.each(data,function(i,json){
                    array[json.empleado_emple_id]=(parseInt(json.empleado_emple_id));

                       $('#idempleado').find('option[value="'+json.empleado_emple_id+'"]').remove();




                  })
                  $('#idempleado').select2({});;
                 //alert(array);
               /*    for(i=0;i<array.length;i++){
                    if(array[i]!=undefined){
                        $('#prue').val(null).trigger('change');
                       //$("#prue option[value=" + i+  "]").hide();
                       $('#prue').find('option[value="'+array[i]+'"]').hide();
                     }
                     if(array[i]==undefined){
                        $('#prue').val(null).trigger('change');
                        $("#prue option[value=" + i + "]").show();
                     }
                   } */



            },
            error:function(){ alert("Hay un error");}
        });

    };

function registrarPE(){

    var proyecto=$('#id1').val();
    var empleado= $('#idempleado').val();


    $.ajax({
        type:"POST",
        url:"/proyecto/registrarPrEm",
        data:{proyecto,empleado},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            $('#myModal1').modal('toggle');
            $('#tablaProyecto').load(location.href+" #tablaProyecto>*");
            $.notify("empleado registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});

        },
        error:function(){ alert("Hay un error, Datos no validos");}
    });



}
