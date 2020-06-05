$(function(){
    $('#empleado').on('change',onMostrarPantallas);
});

function onMostrarPantallas(){
    var value = $('#empleado').val();
    $.ajax({
        url:"tareas/show",
        method: "GET",
        data:{value:value},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(data){
            var container = $('#card');
            console.log(data)
            for(var i=0; i<data.length; i++){
                card = `<div class="card-body" style="padding-left: 0px;">
                        <label>${data[i].hora_ini} ${data[i].hora_fin}</label>
                            <div class="custom-accordion accordion ml-4" id="customaccordion_exa" style="margin-left: 0px!important;">
                                    <div class="card mb-1" style="padding-left: 20px;">
                                        <a href="" class="text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                            aria-expanded="true" aria-controls="customaccorcollapseOne">
                                        </a>
                                        <div id="customaccorcollapseOne" class="collapse show" aria-labelledby="customaccorheadingOne"
                                            data-parent="#customaccordion_exa">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class=" text-center col-md-12 col-sm-6" style="background-color: rgb(234, 234, 234);padding-top: 8px;
                                                padding-bottom: 8px;">
                                                    <h5 class="m-0 font-size-14" > Área 1  </h5>
                                                </div>  <br>
                                                <div class="col-md-12 col-sm-6 border" style="padding-left: 0px;">
                                                <img src="data:image/jpeg;base64,${data[i].imagen}" height="225" width="225">
                                                &nbsp;  <label style="font-size: 12px" for="">9:00 am - 9:10 am</label>
                                                <div class="progress" style="background-color: #d4d4d4;">
                                                    &nbsp;  <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25"
                                                        aria-valuemin="0" aria-valuemax="100">25%</div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>`
                    container.append(card);
            }
        },
        error:function(data){
            alert("Hay un error");
            console.log(data)
        }
    })
}