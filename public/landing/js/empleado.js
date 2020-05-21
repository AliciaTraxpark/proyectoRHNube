$(document).ready(function() {

    leertabla();
});
function leertabla() {
    $.get("tablaempleado/ver", {}, function (data, status) {
        $('#tabladiv').html(data);


    $("#tablaEmpleado").DataTable(
        {    "language" :    {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ ",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad"
            }
        }
        }
    );



    });;
}
$('#guardarArea').click(function(){
    objArea=datos("POST");
    enviarArea('',objArea);
});

function datos(method){
    nuevoArea={
        area_descripcion: $('#textArea').val(),
        '_method':method
    }
    return(nuevoArea);
}

function enviarArea(accion,objArea){
    $.ajax({
        type:"POST",
        url:"/registrar/area"+accion,
        data:objArea,
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            $('#area').load(location.href+" #area>*");//actualiza
             $('#area').val(data.area_id).trigger("change"); //lo selecciona
             $('#textArea').val('');
            $('#areamodal').modal('toggle');
            $.notify("Área registrada", {align:"right", verticalAlign:"top",type: "success", icon:"check"});

        },
        error:function(){ alert("Hay un error");}
    });
}

///CARGO
$('#guardarCargo').click(function(){
    objCargo=datosCargo("POST");
    enviarCargo('',objCargo);
});

function datosCargo(method){
    nuevoCargo={
        cargo_descripcion:$('#textCargo').val(),
        '_method':method
    }
    return(nuevoCargo);
}

function enviarCargo(accion,objCargo){
    $.ajax({
        type:"POST",
        url:"/registrar/cargo"+accion,
        data:objCargo,
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            $('#cargo').load(location.href+" #cargo>*");//actualiza
            $('#cargo').val(data.cargo_id).trigger("change"); //lo selecciona
            $('#textCargo').val('');
            $('#cargomodal').modal('toggle');
            $.notify("Cargo registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});
        },
        error:function(){ alert("Hay un error");}
    });
}

//centro costo
$('#guardarCentro').click(function(){
    objCentroC=datosCentro("POST");
    enviarCentro('',objCentroC);
});

function datosCentro(method){
    nuevoCentro={
        centroC_descripcion:$('#textCentro').val(),
        '_method':method
    }
    return(nuevoCentro);
}

function enviarCentro(accion,objCentroC){
    $.ajax({
        type:"POST",
        url:"/registrar/centro"+accion,
        data:objCentroC,
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            $('#centroc').load(location.href+" #centroc>*");//actualiza
            $('#centroc').val(data.centroC_id).trigger("change"); //lo selecciona
            $('#textCentro').val('');
            $('#centrocmodal').modal('toggle');
            $.notify("Centro de costo registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});
        },
        error:function(){ alert("Hay un error");}
    });
}
//LOCAL
$('#guardarLocal').click(function(){
    objLocal=datosLocal("POST");
    enviarLocal('',objLocal);
});

function datosLocal(method){
    nuevoLocal={
        local_descripcion:$('#textLocal').val(),
        '_method':method
    }
    return(nuevoLocal);
}

function enviarLocal(accion,objLocal){
    $.ajax({
        type:"POST",
        url:"/registrar/local"+accion,
        data:objLocal,
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            $('#local').load(location.href+" #local>*");//actualiza
            $('#local').val(data.local_id).trigger("change"); //lo selecciona
            $('#textLocal').val('');
            $('#localmodal').modal('toggle');
            $.notify("local registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});

        },
        error:function(){alert("Hay un error");}
    });
}
//NIVEL
$('#guardarNivel').click(function(){
    objNivel=datosNivel("POST");
    enviarNivel('',objNivel);
});

function datosNivel(method){
    nuevoNivel={
        nivel_descripcion:$('#textNivel').val(),
        '_method':method
    }
    return(nuevoNivel);
}

function enviarNivel(accion,objNivel){
    $.ajax({
        type:"POST",
        url:"/registrar/nivel"+accion,
        data:objNivel,
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            $('#nivel').load(location.href+" #nivel>*");//actualiza
            $('#nivel').val(data.nivel_id).trigger("change"); //lo selecciona
            $('#textNivel').val('');
            $('#nivelmodal').modal('toggle');
            $.notify("nivel registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});

        },
        error:function(){alert("Hay un error");}
    });
}
//EMPLEADO
$('#guardarEmpleado').click(function(){
    //objEmpleado=datosPersona("POST");
    //enviarEmpleado('',objEmpleado);
    $('#Dropzone').get(0).dropzone.processQueue();
    $('#smartwizard').smartWizard("reset");
            $('input[type="text"]').val("");
            $('input[type="radio"]').val("");

            $('select').val("");
});

function datosPersona(method){
    nuevoEmpleado={
        nombres:$('#nombres').val(),
        apPaterno:$('#apPaterno').val(),
        apMaterno:$('#apMaterno').val(),
        fechaN:$('#fechaN').val(),
        tipo:$('#tipo').val(),
        documento:$('#documento').val(),
        numDocumento:$('#numDocumento').val(),
        departamento:$('#departamento').val(),
        provincia:$('#provincia').val(),
        distrito:$('#distrito').val(),
        cargo:$('#cargo').val(),
        area:$('#area').val(),
        centroc:$('#centroc').val(),
        dep:$('#dep').val(),
        prov:$('#prov').val(),
        dist:$('#dist').val(),
        contrato:$('#contrato').val(),
        direccion:$('#direccion').val(),
        nivel:$('#nivel').val(),
        local:$('#local').val(),
        '_method':method
    }
    return(nuevoEmpleado);
}

function enviarEmpleado(accion,objEmpleado){

    $.ajax({

        type:"POST",
        url:"/empleado/store"+accion,
        data:objEmpleado,
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(msg){
            leertabla();
            $('#smartwizard').smartWizard("reset");
            $('input[type="text"]').val("");
            $('input[type="radio"]').val("");
            $('select').val("");


        },
        error:function(){ alert("Hay un error");console.log(objEmpleado);}
    })
}
$("#tablaEmpleado tr").click(function(){
    $(this).addClass('selected').siblings().removeClass('selected');
    var value=$(this).find('td:first').html();
    alert(value);
 });

 $('.ok').on('click', function(e){
     alert($("#tablaEmpleado tr.selected td:first").html());
 });


