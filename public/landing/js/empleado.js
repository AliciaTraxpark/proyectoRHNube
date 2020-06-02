$('#numDocumento').prop('disabled',true);
$('#apPaterno').prop('disabled',true);
$('#apMaterno').prop('disabled',true);
$('#departamento').prop('disabled',true);
$('#provincia').prop('disabled',true);
$('#distrito').prop('disabled',true);
$('#dep').prop('disabled',true);
$('#prov').prop('disabled',true);
$('#dist').prop('disabled',true);
$('#nombres').prop('disabled',true);
$('#direccion').prop('disabled',true);
$('input:radio[name=tipo]').prop('disabled',true);
$('#celular').prop('disabled',true);
$('#telefono').prop('disabled',true);
$('day').attr('disabled','disabled')
.siblings().removeAttr('disabled');
$('#documento').on('change',function(){
    $('#numDocumento').prop('disabled',false);
    $('#apPaterno').prop('disabled',false);
    $('#apMaterno').prop('disabled',false);
    $('#departamento').prop('disabled',false);
    $('#provincia').prop('disabled',false);
    $('#distrito').prop('disabled',false);
    $('#dep').prop('disabled',false);
    $('#prov').prop('disabled',false);
    $('#dist').prop('disabled',false);
    $('#nombres').prop('disabled',false);
    $('#direccion').prop('disabled',false);
    $('input:radio[name=tipo]').prop('disabled',false);
    $('#celular').prop('disabled',false);
    $('#telefono').prop('disabled',false);
});
//********************** */
$('#fechaN').combodate({
    minYear: 1960,
    yearDescending: false,
}); 
$('#m_fechaI').combodate({
    value: new Date(),
    minYear: 2000,
    maxYear: moment().format('YYYY')+1,
    yearDescending: false
});
$('#m_fechaF').combodate({
    value: new Date(),
    minYear: 1960,
    maxYear: moment().format('YYYY') + 1,
    yearDescending: false,
});
$('#v_fechaN').combodate({
    minYear: 1900,
    yearDescending: false,
}); 
console.log('fechaN');
$(document).ready(function() {
    $("#file").fileinput({
        allowedFileExtensions: ['jpg','jpeg','png'],
        uploadAsync: false,
        overwriteInitial: false,
        validateInitialCount: true,
        showUpload:false,
        minFileCount:0,
        maxFileCount: 1,
        initialPreviewAsData: true ,// identify if you are sending preview data only and not the markup
        language: 'es',
        showBrowse: false,
        browseOnZoneClick: true,
        theme: "fa"
    });
});
//AREA
function agregarArea(){
    objArea=datos("POST");
    enviarArea('',objArea);
};

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
               $('#v_area').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.area_id,
                text: data.area_descripcion,
                selected: true
               }));
             $('#area').val(data.area_id).trigger("change"); //lo selecciona
             $('#v_area').val(data.area_id).trigger("change");
             $('#textArea').val('');
            $('#areamodal').modal('toggle');
            $.notify("Área registrada", {align:"right", verticalAlign:"top",type: "success", icon:"check"});

        },
        error:function(){ alert("Hay un error");}
    });
}

///CARGO
function agregarcargo(){
    objCargo=datosCargo("POST");
    enviarCargo('',objCargo);
};

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
               $('#v_cargo').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.cargo_id,
                text: data.cargo_descripcion,
                selected: true
               }));
            $('#cargo').val(data.cargo_id).trigger("change"); //lo selecciona
            $('#v_cargo').val(data.cargo_id).trigger("change"); //lo selecciona
            $('#textCargo').val('');
            $('#cargomodal').modal('toggle');
            $.notify("Cargo registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});
        },
        error:function(){ alert("Hay un error");}
    });
}

//centro costo
function agregarcentro(){
    objCentroC=datosCentro("POST");
    enviarCentro('',objCentroC);
};

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
               $('#v_centroc').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.centroC_id,
                text: data.centroC_descripcion,
                selected: true
               }));
            $('#centroc').val(data.centroC_id).trigger("change"); //lo selecciona
            $('#v_centroc').val(data.centroC_id).trigger("change"); //lo selecciona
            $('#textCentro').val('');
            $('#centrocmodal').modal('toggle');
            $.notify("Centro de costo registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});
        },
        error:function(){ alert("Hay un error");}
    });
}
//LOCAL
function agregarlocal(){
    objLocal=datosLocal("POST");
    enviarLocal('',objLocal);
};

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
               $('#v_local').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.local_id,
                text: data.local_descripcion,
                selected: true
               }));
            $('#local').val(data.local_id).trigger("change"); //lo selecciona
            $('#v_local').val(data.local_id).trigger("change"); //lo selecciona
            $('#textLocal').val('');
            $('#localmodal').modal('toggle');
            $.notify("local registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});

        },
        error:function(){alert("Hay un error");}
    });
}
//NIVEL
function agregarnivel(){
    objNivel=datosNivel("POST");
    enviarNivel('',objNivel);
};

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
               $('#v_nivel').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.nivel_id,
                text: data.nivel_descripcion,
                selected: true
               }));
            $('#nivel').val(data.nivel_id).trigger("change"); //lo selecciona
            $('#v_nivel').val(data.nivel_id).trigger("change"); //lo selecciona
            $('#textNivel').val('');
            $('#nivelmodal').modal('toggle');
            $.notify("nivel registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});

        },
        error:function(){alert("Hay un error");}
    });
}

//CONTRATO
function agregarContrato(){
    objContrato=datos("POST");
    enviarContrato('',objContrato);
};

function datos(method){
    nuevoArea={
        contrato_descripcion: $('#textContrato').val(),
        '_method':method
    }
    return(nuevoArea);
}

function enviarContrato(accion,objArea){
    $.ajax({
        type:"POST",
        url:"/registrar/contrato"+accion,
        data:objContrato,
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            $('#contrato').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.contrato_id,
                text: data.contrato_descripcion,
                selected: true
               }));
               $('#v_contrato').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.contrato_id,
                text: data.contrato_descripcion,
                selected: true
               }));
             $('#contrato').val(data.contrato_id).trigger("change"); //lo selecciona
             $('#v_contrato').val(data.contrato_id).trigger("change");
             $('#textcontrato').val('');
            $('#contratomodal').modal('toggle');
            $.notify("Contrato registrado", {align:"right", verticalAlign:"top",type: "success", icon:"check"});

        },
        error:function(){ alert("Hay un error");}
    });
}
//FECHAS
$(function(){
    $('#contrato').on('change',onSelectFecha);
});
function onSelectFecha(){
    $('#fechasmodal').modal();
}
$('#guardarFechas').click(function(){
    fechaI = $('#m_fechaI').val();
    fechaF = $('#m_fechaF').val();
    if($('#m_fechaI').val() != "" && $('#m_fechaF') != ""){
        $('#c_fechaI').text(fechaI);
        $('#c_fechaF').text(fechaF);
        $('#fechasmodal').modal('toggle');
    }
    $('#m_fechaI').combodate("clearValue");
    $('#m_fechaF').combodate("clearValue");
});
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
        celular:$('#celular').val(),
        telefono:$('#telefono').val(),
        fechaI:$('#c_fechaI').text(),
        fechaF:$('#c_fechaF').text(),
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
            $('input:radio[name=tipo]:checked').prop('checked',false);
            $('input[type="date"]').val("");
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
        fechaN_v:$('#v_fechaN').val(),
        tipo_v:$('input:radio[name=v_tipo]:checked').val(),
        departamento_v:$('#v_departamento').val(),
        provincia_v:$('#v_provincia').val(),
        distrito_v:$('#v_distrito').val(),
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
        celular_v:$('#v_celular').val(),
        telefono_v:$('#v_telefono').val(),
        '_method':method
    }
    return(nuevoEmpleadoA);
}

function actualizarEmpleado(accion,objEmpleadoA){

    var formDataA = new FormData();
    formDataA.append('file',$('#file2').prop('files')[0]);
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
            $('#form-ver').hide();
            $('#form-registrar').show();
            $('#smartwizard').smartWizard("reset");
            $('input[type="file"]').val("");
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
    $('#tablaEmpleado tbody tr').removeClass('selected');
    $('#form-registrar').smartWizard("reset");
    $('input[type="text"]').val("");
    $('input:radio[name=tipo]:checked').prop('checked',false);
    $('input[type="date"]').val("");
    $('input[type="file"]').val("");
    $('select').val("");
    $('#form-registrar').show();
}

//eliminar foto
function cargarFile2(){
    $("#file2").fileinput({
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        uploadAsync: false,
        overwriteInitial: false,
        showUpload:false,
        validateInitialCount: true,
        showRemove:true,
        minFileCount:0,
        maxFileCount: 1,
        ...(hayFoto &&{
            initialPreview:
            [
                "<img  id=v_foto src='{{asset('/fotosEmpleado')}}/'"+
                urlFoto + "'style='width:120px'>"
            ] ,
            initialPreviewConfig: [{
                width: "120px",
                url: "/eliminarFoto/"+id_empleado,
                showDelete: true,
                key:id_empleado
            }]
        }),
        language: 'es',
        deleteExtraData:{_token:$("#csrf_token").val()},
        showBrowse: false,
        browseOnZoneClick: true,
        theme: "fa",
        fileActionSettings:{"showDrag":false, 'showZoom':false},
    })
}
