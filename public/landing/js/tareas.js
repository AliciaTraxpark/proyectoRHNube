var notify = $.notifyDefaults({
    icon_type: 'image',
    newest_on_top: true,
    delay: 4000,
    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
        '<span data-notify="title">{1}</span> ' +
        '<span style="color:#8a6d3b" data-notify="message">{2}</span>' +
        '</div>'
});
//FECHA
$('#fecha').flatpickr({
    locale: "es",
    maxDate: "today"
});
//CAPTURAS
$(function () {
    $('#empleado').on('change', onMostrarPantallas);
    $('#fecha').on('change', onMostrarPantallas);
    $('#proyecto').on('change', onMostrarPantallas);
});
var datos;

function onMostrarPantallas() {
    var value = $('#empleado').val();
    var fecha = $('#fecha').val();
    var proyecto = $('#proyecto').val();
    $('#card').empty();
    $.ajax({
        url: "tareas/show",
        method: "GET",
        data: {
            value: value,
            fecha: fecha,
            proyecto: proyecto
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            //data = data.reverse();
            datos = data;
            if (data.length != 0) {
                var container = $('#card');
                $.notifyClose();
                for (const hora in data) {
                    var horaDelGrupo = hora;
                    var labelDelGrupo = horaDelGrupo + ":00:00" + " - " + (parseInt(horaDelGrupo) + 1) + ":00:00";
                    var grupo = `<span style="font-weight: bold;color:#507394;">${labelDelGrupo}</span><br><br><div class="row">`;
                    for (var j = 0; j < 6; j++) {
                        if (data[hora][j] != undefined) {
                            var horaP = data[hora][j][data[hora][j].length - 1].promedio.split(":");
                            var segundos = parseInt(horaP[0]) * 3600 + parseInt(horaP[1]) * 60 + parseInt(horaP[2]);
                            var totalE = data[hora][j][data[hora][j].length - 1].Total_Envio.split(":");
                            var segundosT = parseInt(totalE[0]) * 3600 + parseInt(totalE[1]) * 60 + parseInt(totalE[2]);
                            var promedio = Math.round((segundos * 100) / segundosT);
                            var nivel;
                            if (promedio >= 50) nivel = "green";
                            else if (promedio > 35) nivel = "#f3c623";
                            else nivel = "red";
                            if (j < 5) {
                                var capturas = "";
                                for (let index = 1; index < data[hora][j].length; index++) {
                                    capturas += `<div class = "carousel-item">
                                    <img src="data:image/jpeg;base64,${data[hora][j][index].imagen}" height="120" width="200" class="img-responsive">
                                    <div class="overlay">
                                    <a class="info" onclick="zoom('${hora + "," + j}')" style="color:#fdfdfd">
                                    <i class="fa fa-eye"></i> Colección</a>
                                    </div>
                                </div>`;
                                }
                                card = `<div class="col-2" style="margin-left: 0px!important;">
                                        <div class="mb-0 text-center" style="padding-left: 0px;">
                                            <a href="" class="col text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                                aria-expanded="true" aria-controls="customaccorcollapseOne">
                                            </a>
                                            <div class="collapse show" aria-labelledby="customaccorheadingOne" data-parent="#customaccordion_exa">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class=" text-center col-md-12 col-sm-6" style="background:#1f4068; border-color:#1f4068;padding-top: 4px;
                                                    padding-bottom: 4px;">
                                                        <h5 class="m-0 font-size-16" style="color:#fafafa">${data[hora][j][0].Proye_Nombre} </h5>
                                                    </div>  <br>
                                                    <div class="col-md-12 col-sm-6" style="padding-left: 0px;">
                                                    <div class="hovereffect">
                                                        <div  id="myCarousel${hora + j}" class = "carousel carousel-fade" data-ride = "carousel">
                                                            <div class = "carousel-inner">
                                                                <div class = "carousel-item active">
                                                                    <img src="data:image/jpeg;base64,${data[hora][j][0].imagen}" height="120" width="200" class="img-responsive">
                                                                    <div class="overlay">
                                                                    <a class="info" onclick="zoom('${hora + "," + j}')" style="color:#fdfdfd">
                                                                    <i class="fa fa-eye"></i> Colección</a>
                                                                    </div>
                                                                </div>
                                                                ${capturas}
                                                            </div>
                                                            <a class = "carousel-control-prev" href = "#myCarousel${hora + j}" role = "button" data-slide = "prev">
                                                                <span class = "carousel-control-prev-icon" aria-hidden = "true"></span>
                                                                <span class = "sr-only">Previous</span>
                                                            </a>
                                                            <a class = "carousel-control-next" href = "#myCarousel${hora + j}" role = "button" data-slide = "next">
                                                                <span class = "carousel-control-next-icon" aria-hidden = "true"></span>
                                                                <span class = "sr-only">Next</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    &nbsp;
                                                    <label style="font-size: 12px" for="">${hora + ":" + j + "0" + " - " + hora + 
                                                    ":" + (j+1) + "0"}</label>
                                                    <div class="progress" style="background-color: #d4d4d4;">
                                                        <div class="progress-bar" role="progressbar" style="width:${promedio}%;background:${nivel}" aria-valuenow=${promedio}
                                                            aria-valuemin="0" aria-valuemax="100">${promedio + "%"}</div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>`
                            } else {
                                card = `<div class="col-2" style="margin-left: 0px!important;">
                                            <div class="mb-0 text-center" style="padding-left: 0px;">
                                                <a href="" class="col text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                                    aria-expanded="true" aria-controls="customaccorcollapseOne">
                                                </a>
                                                <div class="collapse show" aria-labelledby="customaccorheadingOne"
                                                    data-parent="#customaccordion_exa">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class=" text-center col-md-12 col-sm-6" style="background:#1f4068; border-color:#1f4068;padding-top: 4px;
                                                        padding-bottom: 4px;">
                                                            <h5 class="m-0 font-size-16" style="color:#fafafa">${data[hora][j][0].Proye_Nombre} </h5>
                                                        </div>  <br>
                                                        <div class="col-md-12 col-sm-6" style="padding-left: 0px;">
                                                        <div class="hovereffect">
                                                        <div  id="myCarousel${hora + j}" class = "carousel carousel-fade" data-ride = "carousel">
                                                            <div class = "carousel-inner">
                                                                <div class = "carousel-item active">
                                                                    <img src="data:image/jpeg;base64,${data[hora][j][0].imagen}" height="120" width="200" class="img-responsive">
                                                                    <div class="overlay">
                                                                    <a class="info" onclick="zoom('${hora + "," + j}')" style="color:#fdfdfd">
                                                                    <i class="fa fa-eye"></i> Colección</a>
                                                                    </div>
                                                                </div>
                                                                ${capturas}
                                                            </div>
                                                            <a class = "carousel-control-prev" href = "#myCarousel" role = "button" data-slide = "prev">
                                                                <span class = "carousel-control-prev-icon" aria-hidden = "true"></span>
                                                                <span class = "sr-only">Previous</span>
                                                            </a>
                                                            <a class = "carousel-control-next" href = "#myCarousel" role = "button" data-slide = "next">
                                                                <span class = "carousel-control-next-icon" aria-hidden = "true"></span>
                                                                <span class = "sr-only">Next</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                        &nbsp;
                                                        <label style="font-size: 12px" for="">${hora + ":" + j + "0" + " - " + (parseInt(hora)+1) + 
                                                        ":" + "00"}</label>
                                                        <div class="progress" style="background-color: #d4d4d4;">
                                                            <div class="progress-bar" role="progressbar" style="width:${promedio}%;background:${nivel}" aria-valuenow=${promedio}
                                                                aria-valuemin="0" aria-valuemax="100">${promedio + "%"}</div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>`
                            }
                            grupo += card;
                        } else {
                            card = `<div class="col-2" style="margin-left: 0px!important;justify-content:center;!important">
                        <br><br><br><br><br>
                                <div class="mb-0">
                                    <a href="" class="text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                        aria-expanded="true" aria-controls="customaccorcollapseOne">
                                    </a>
                                    <div class="collapse show" aria-labelledby="customaccorheadingOne"
                                        data-parent="#customaccordion_exa">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class=" text-center col-md-12 col-sm-12" style="background:#888888; border-color:#888888;padding-top: 4px;
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
                    grupo += `</div><br>`;
                    container.append(grupo);
                }
            } else {
                $.notify({
                    message: "Falta elegir campos o No se encontrado capturas.",
                    icon: 'admin/images/warning.svg'
                });
            }
        },
        error: function (data) {
            alert("Hay un error");
        }
    })
}
//PROYECTO
$(function () {
    $('#empleado').on('change', onMostrarProyecto);
});

function onMostrarProyecto() {
    var value = $('#empleado').val();
    $.ajax({
        url: "tareas/proyecto",
        method: "GET",
        data: {
            value: value
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            var html_select = '<option value="">Seleccionar</option>';
            for (var i = 0; i < data.length; i++)
                html_select += '<option value="' + data[i].Proye_id + '">' + data[i].Proye_Nombre + '</option>';
            $('#proyecto').html(html_select);
        }
    })
}

function zoom(horayJ) {
    var hora = horayJ.split(",")[0];
    var j = horayJ.split(",")[1];
    capturas = datos[hora][j];
    var carusel = `<p class="imglist" style="max-width: 1000px;">`;
    for (let index = 0; index < capturas.length; index++) {
        const element = capturas[index];
        carusel += `<a href="data:image/jpeg;base64,${element.imagen}" data-fancybox="images" data-caption="Collección de capturas" data-width="2048" data-height="1365"><img src="data:image/jpeg;base64,${element.imagen}" width="240" height="240"></a>`
    }
    carusel += `</p>`
    document.getElementById("zoom").innerHTML = carusel;
    $('#modalZoom').modal();
}
$("#myCarousel").carousel({
    interval: 2000,
});
$('[data-fancybox]').fancybox({
    protect: true
});
