$(document).ready(function() {
    $("#file").fileinput({
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        uploadAsync: false,
        overwriteInitial: false,
        minFileCount:0,
        maxFileCount: 1,
        initialPreviewAsData: true ,// identify if you are sending preview data only and not the markup
        language: 'es',
        showBrowse: false,
        browseOnZoneClick: true
    });
});
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
            $('#area').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.area_id,
                text: data.area_descripcion,
                selected: true
               }));
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
            $('#cargo').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.cargo_id,
                text: data.cargo_descripcion,
                selected: true
               }));
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
            $('#centroc').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.centroC_id,
                text: data.centroC_descripcion,
                selected: true
               }));
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
            $('#local').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.local_id,
                text: data.local_descripcion,
                selected: true
               }));
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
            $('#nivel').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.nivel_id,
                text: data.nivel_descripcion,
                selected: true
               }));
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
    objEmpleado=datosPersona("POST");
    enviarEmpleado('',objEmpleado);
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

    var formData = new FormData();
    formData.append('file',$('#file').prop('files')[0]);
    formData.append('objEmpleado',JSON.stringify(objEmpleado));
    $.ajax({

        type:"POST",
        url:"/empleado/store"+accion,
        data:formData,
        contentType:false,
        processData:false,
        dataType:"json",
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(msg){
            leertabla();
            $('#smartwizard').smartWizard("reset");
            $('input[type="text"]').val("");
            $('input[type="radio"]').val("");
            $('input[type="file"]').val("");
            $('select').val("");


        },
        error:function(data,errorThrown){
            alert("Hay un error");
            alert('request failed:'+errorThrown);
        }
    });
}



