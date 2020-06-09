$(function(){
    $('#empleado').on('change',onMostrarPantallas);
});

function onMostrarPantallas(){
    var value = $('#empleado').val();
    $('#card').empty();
    $.ajax({
        url:"tareas/show",
        method: "GET",
        data:{value:value},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            var container = $('#card');
            var horaDelGrupo = parseInt(data[0].hora_ini.split(":")[0]);
            var labelDelGrupo = horaDelGrupo+":00:00" + " - " + (horaDelGrupo+1) + ":00:00";
            var grupo = `<span style="font-weight: bold;">${labelDelGrupo}</span><br><br><div class="row">`;
            for(var i=0; i<data.length; i++){
                for(var j=0; j<6; j++){
                    if(parseInt(data[i].hora_ini.split(":")[1].charAt(0)) == j && 
                    parseInt(data[i].hora_ini.split(":")[0]) == horaDelGrupo){
                        card = `<div class="col-2" style="margin-left: 0px!important;">
                                            <div class="card mb-0 text-center" style="padding-left: 20px;">
                                                <a href="" class="col text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                                    aria-expanded="true" aria-controls="customaccorcollapseOne">
                                                </a>
                                                <div class="collapse show" aria-labelledby="customaccorheadingOne"
                                                    data-parent="#customaccordion_exa">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class=" text-center col-md-12 col-sm-6" style="background:#a6b1e1; border-color:#a6b1e1;padding-top: 4px;
                                                        padding-bottom: 4px;">
                                                            <h5 class="m-0 font-size-16" style="color:#fafafa">${data[i].Proye_Nombre} </h5>
                                                        </div>  <br>
                                                        <div class="col-md-12 col-sm-6" style="padding-left: 0px;">
                                                        <img src="data:image/jpeg;base64,${data[i].imagen}" height="120" width="120">
                                                        &nbsp;  
                                                        if(${parseInt(data[i].hora_ini.split(":")[1].charAt(0))!= 5}){
                                                            <label style="font-size: 12px" for="">${data[i].hora_ini.split(":")[0].charAt(0)+
                                                        data[i].hora_ini.split(":")[0].charAt(1) + ":" +data[i].hora_ini.split(":")[1].charAt(0) + 
                                                        "0" + " - " + data[i].hora_ini.split(":")[0].charAt(0)+ data[i].hora_ini.split(":")[0].charAt(1) + 
                                                        ":" +(parseInt(data[i].hora_ini.split(":")[1].charAt(0))+1) + "0"}</label>
                                                        }
                                                        <div class="progress" style="background-color: #d4d4d4;">
                                                            &nbsp;  <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25"
                                                                aria-valuemin="0" aria-valuemax="100">25%</div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>`
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
                                            <div class=" text-center col-md-12 col-sm-12" style="background:#a6b1e1; border-color:#a6b1e1;padding-top: 4px;
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
                grupo = `<span style="font-weight: bold;">${labelDelGrupo}</span><br><br><div class="row">`;
            }
        },
        error:function(data){
            alert("Hay un error");
        }
    })
}