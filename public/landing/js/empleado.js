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
            if(data==1){
            $('#areamodal').modal('toggle');
            $.notify("Área registrada", {align:"right", verticalAlign:"top",type: "success", icon:"check"});
        }


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
        success:function(msg){
            $('#cargomodal').modal('toggle');
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
        success:function(msg){
            $('#centrocmodal').modal('toggle');
        },
        error:function(){ alert("Hay un error");}
    });
}
