$(document).ready(function() {
    $("#file").fileinput({
        allowedFileExtensions: ['jpg','jpeg','png'],
        uploadAsync: false,
        overwriteInitial: false,
        minFileCount:0,
        maxFileCount: 1,
        initialPreviewAsData: true ,// identify if you are sending preview data only and not the markup
        language: 'es',
        showBrowse: false,
        browseOnZoneClick: true,
        theme: "fa"
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
        tipo:$('input:radio[name=tipo]:checked').val(),
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
            $('#smartwizard').smartWizard("reset");
            $('input[type="text"]').val("");
            //$('input[type="radio"]').val("");
            $('input[type="file"]').val("");
            $('select').val("");
            leertabla();

        },
        error:function(data,errorThrown){
            alert("Hay un error");
            alert('request failed:'+errorThrown);
        }
    });
}

//EMPLEADO ACTUALIZAR
$('#actualizarEmpleado').click(function(){
    idE=$('#v_id').val();
    objEmpleadoA=datosPersonaA("PUT");
    actualizarEmpleado('/'+idE,objEmpleadoA);
});


function datosPersonaA(method){
    nuevoEmpleadoA={
        nombres_v:$('#v_nombres').val(),
        apPaterno_v:$('#v_apPaterno').val(),
        apMaterno_v:$('#v_apMaterno').val(),
        numDocumento_v:$('#v_numDocumento').val(),
        cargo_v:$('#v_cargo').val(),
        area_v:$('#v_area').val(),
        centroc_v:$('#v_centroc').val(),
        dep_v:$('#v_dep').val(),
        prov_v:$('#v_prov').val(),
        dist_v:$('#v_dist').val(),
        contrato_v:$('#v_contrato').val(),
        direccion_v:$('#v_direccion').val(),
        nivel_v:$('#v_nivel').val(),
        local_v:$('#v_local').val(),
        '_method':method
    }
    return(nuevoEmpleadoA);
}

function actualizarEmpleado(accion,objEmpleadoA){

    var formDataA = new FormData();
    formDataA.append('file',$('#file').prop('files')[0]);
    formDataA.append('objEmpleadoA',JSON.stringify(objEmpleadoA));
    $.ajax({

        type:"POST",
        url:"/empleadoA"+accion,
        data:formDataA,
        contentType:false,
        processData:false,
        dataType:"json",
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(msg){
            leertabla();
            $('#smartwizard1').smartWizard("reset");
            $('input[type="text"]').val("");
            $('input[type="radio"]').val("-1");
            $('input[type="file"]').val("");
            $('select').val("");
        },
        error:function(data,errorThrown){
            alert("Hay un error");
            console.log(formDataA.get('objEmpleadoA'));
        }
    });
}
///ELIMINAR EMPLEADO

   $('#confirmarE').click(function() {
       var id=$('#v_id').val()
   $.ajax({
    url:"/empleado/eliminar",
    method:"POST",
    data:{
        id:id,
    },
    headers:{
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success:function(data){
        $('#modalEliminar').modal('hide');
        $.notify(" Empleado eliminado", {align:"right", verticalAlign:"top",type: "danger", icon:"bell"});
        leertabla();
        $('#form-ver').hide();
        $('#form-registrar').show();



    },
    error:function(data,errorThrown){
        alert("Hay un error");

    }

});  });


//abrir nuevo form
function abrirnuevo(){
    $('#form-ver').hide();
    $('#form-registrar').show();
}


$(document).ready(function() {
    $('#v_id').val();
    $("#file2").fileinput({
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        uploadAsync: false,
        overwriteInitial: false,
        showCaption: true,
        showUpload: true,
        showRemove:true,
        validateInitialCount: true,
        autoReplace: true,
        minFileCount:0,
        maxFileCount: 1,
        initialPreviewFileType: 'image',
        initialPreview: ["<img  id=v_foto style='width:200px'>"] ,// identify if you are sending preview data only and not the markup
        initialPreviewConfig: [{
            width: "120px",
            url: "/eliminarFoto/"+v_id,
            showDelete: true
        }],
        language: 'es',
        uploadExtraData:{'_token:X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        showBrowse: false,
        browseOnZoneClick: true,
        theme: "fa",
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        fileActionSettings:{"showDrag":false, 'showZoom':false},
    });
});
