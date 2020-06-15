//FECHA
$('#fecha').flatpickr({
    locale:"es",
    maxDate: "today"
});
//CAPTURAS
$(function(){
    $('#empleado').on('change',onMostrarPantallas);
    $('#fecha').on('change',onMostrarPantallas);
    $('#proyecto').on('change',onMostrarPantallas);
});
function onMostrarPantallas(){
    var value = $('#empleado').val();
    var fecha = $('#fecha').val();
    var proyecto = $('#proyecto').val();
    $('#card').empty();
    $.ajax({
        url:"tareas/show",
        method: "GET",
        data:{value:value,fecha:fecha,proyecto:proyecto},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
           if(data.length != 0){
            var container = $('#card');
            var horaDelGrupo = parseInt(data[0].hora_ini.split(":")[0]);
            var labelDelGrupo = horaDelGrupo+":00:00" + " - " + (horaDelGrupo+1) + ":00:00";
            var grupo = `<span style="font-weight: bold;color:#507394;">${labelDelGrupo}</span><br><br><div class="row">`;
            for(var i=0; i<data.length; i++){
                for(var j=0; j<6; j++){
                    if(parseInt(data[i].hora_ini.split(":")[1].charAt(0)) == j && 
                    parseInt(data[i].hora_ini.split(":")[0]) == horaDelGrupo){
                        var horaP = data[i].promedio.split(":");
                        var segundos = parseInt(horaP[0])*3600 + parseInt(horaP[1])*60 + parseInt(horaP[2]);
                        var totalE = data[i].Total_Envio.split(":");
                        var segundosT = parseInt(totalE[0])*3600 + parseInt(totalE[1])*60 + parseInt(totalE[2]);
                        var promedio = Math.round((segundos*100)/segundosT);
                        console.log(promedio);
                        if(parseInt(data[i].hora_ini.split(":")[1].charAt(0)) <5){
                            card = `<div class="col-2" style="margin-left: 0px!important;">
                                        <div class="card mb-0 text-center" style="padding-left: 20px;">
                                            <a href="" class="col text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                                aria-expanded="true" aria-controls="customaccorcollapseOne">
                                            </a>
                                            <div class="collapse show" aria-labelledby="customaccorheadingOne" data-parent="#customaccordion_exa">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class=" text-center col-md-12 col-sm-6" style="background:#393e46; border-color:#393e46;padding-top: 4px;
                                                    padding-bottom: 4px;">
                                                        <h5 class="m-0 font-size-16" style="color:#fafafa">${data[i].Proye_Nombre} </h5>
                                                    </div>  <br>
                                                    <div class="col-md-12 col-sm-6" style="padding-left: 0px;">
                                                    <div class="hovereffect">
                                                    <img src="data:image/jpeg;base64,${data[i].imagen}" height="120" width="120" class="img-responsive">
                                                    <div class="overlay">
                                                    <h2>Vista</h2>
                                                    <a class="info" onclick="zoom('${data[i].imagen}')" style="color:#fdfdfd">Previa</a>
                                                    </div>
                                                    </div>
                                                    &nbsp;
                                                    <label style="font-size: 12px" for="">${data[i].hora_ini.split(":")[0].charAt(0)+
                                                    data[i].hora_ini.split(":")[0].charAt(1) + ":" +data[i].hora_ini.split(":")[1].charAt(0) + 
                                                    "0" + " - " + data[i].hora_ini.split(":")[0].charAt(0)+ data[i].hora_ini.split(":")[0].charAt(1) + 
                                                    ":" +(parseInt(data[i].hora_ini.split(":")[1].charAt(0))+1) + "0"}</label>
                                                    <div class="progress" style="background-color: #d4d4d4;">
                                                        <div class="progress-bar" role="progressbar" style="width:${promedio}%;background:#21bf73" aria-valuenow=${promedio}
                                                            aria-valuemin="0" aria-valuemax="100">${promedio + "%"}</div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>`
                        }else{
                            card = `<div class="col-2" style="margin-left: 0px!important;">
                                            <div class="card mb-0 text-center" style="padding-left: 20px;">
                                                <a href="" class="col text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                                    aria-expanded="true" aria-controls="customaccorcollapseOne">
                                                </a>
                                                <div class="collapse show" aria-labelledby="customaccorheadingOne"
                                                    data-parent="#customaccordion_exa">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class=" text-center col-md-12 col-sm-6" style="background:#393e46; border-color:#393e46;padding-top: 4px;
                                                        padding-bottom: 4px;">
                                                            <h5 class="m-0 font-size-16" style="color:#fafafa">${data[i].Proye_Nombre} </h5>
                                                        </div>  <br>
                                                        <div class="col-md-12 col-sm-6" style="padding-left: 0px;">
                                                        <div class="hovereffect">
                                                        <img src="data:image/jpeg;base64,${data[i].imagen}" height="120" width="120" class="img-responsive">
                                                        <div class="overlay">
                                                        <a class="info" onclick="zoom('${data[i].imagen}')" style="color:#fdfdfd">ZOOM</a>
                                                        </div>
                                                        </div>
                                                        &nbsp;
                                                        <label style="font-size: 12px" for="">${data[i].hora_ini.split(":")[0].charAt(0)+
                                                        data[i].hora_ini.split(":")[0].charAt(1) + ":" +data[i].hora_ini.split(":")[1].charAt(0) + 
                                                        "0" + " - " + data[i].hora_ini.split(":")[0].charAt(0)+ (parseInt(data[i].hora_ini.split(":")[0].charAt(1))+1) + 
                                                        ":00"}</label>
                                                        <div class="progress" style="background-color: #d4d4d4;">
                                                            <div class="progress-bar" role="progressbar" style="width:${promedio}%;background:#21bf73" aria-valuenow=${promedio}
                                                                aria-valuemin="0" aria-valuemax="100">${promedio + "%"}</div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>`
                        }
                        if(i!= data.length-1){
                            i++;
                        }
                        grupo+=card;
                    }else{
                        card = `<div class="col-2" style="margin-left: 0px!important;justify-content:center;!important">
                        <br><br><br><br><br>
                                <div class="card mb-0" style="padding-left: 20px;padding-right: 20px;">
                                    <a href="" class="text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                        aria-expanded="true" aria-controls="customaccorcollapseOne">
                                    </a>
                                    <div class="collapse show" aria-labelledby="customaccorheadingOne"
                                        data-parent="#customaccordion_exa">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class=" text-center col-md-12 col-sm-12" style="background:#393e46; border-color:#393e46;padding-top: 4px;
                                            padding-bottom: 4px;">
                                                <h5 class="m-0 font-size-14" style="color:#fafafa">Vacio</h5>
                                            </div>  <br>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>`;
                        grupo += card;
                    }
                }
                if(parseInt(data[i].hora_ini.split(":")[0] ) > horaDelGrupo){
                    i--;
                }
                grupo += `</div><br>`;
                container.append(grupo);
                horaDelGrupo = parseInt(data[i + 1].hora_ini.split(":")[0]);
                var labelDelGrupo = horaDelGrupo + ":00:00" + " - "  + (horaDelGrupo+1) + ":00:00";
                grupo = `<span style="font-weight: bold;color:#507394;">${labelDelGrupo}</span><br><br><div class="row">`;
            }
           }else{
            $.notify("Falta elegir campos o No se encontrado capturas.", {align:"right", verticalAlign:"top",type: "warning", icon:"warning"});
           }
        },
        error:function(data){
            alert("Hay un error");
        }
    })
}
//PROYECTO
$(function(){
    $('#empleado').on('change',onMostrarProyecto);
});
function onMostrarProyecto(){
    var value = $('#empleado').val();
    $.ajax({
        url:"tareas/proyecto",
        method: "GET",
        data:{value:value},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            var html_select = '<option value="">Seleccionar</option>';
            for(var i=0; i<data.length; i++)
                html_select += '<option value="'+ data[i].Proye_id +'">'+ data[i].Proye_Nombre +'</option>';
                $('#proyecto').html(html_select);
        }
    })
}

function zoom(img){
    $('#imagenZoom').attr("src",`data:image/jpeg;base64,${img}`);
    $('#zoom').zoom({
        on : 'click'
    });
    $('#modalZoom').modal();
    $('.close').on('click',function(){
        $('#zoom').trigger('zoom.destroy'); 
    });
}