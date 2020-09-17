var notify = $.notifyDefaults({
    icon_type: "image",
    newest_on_top: true,
    delay: 4000,
    template:
        '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
        '<span data-notify="title">{1}</span> ' +
        '<span style="color:#8a6d3b" data-notify="message">{2}</span>' +
        "</div>",
});
//FECHA
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "single",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "D, j F",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: true,
});

$("#empleado").select2();
$("#empleado").on("select2:opening", function () {
    var value = $("#empleado").val();
    $("#empleado").empty();
    var container = $("#empleado");
    $.ajax({
        async: false,
        url: "/tareas/empleadoR",
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
        },
        success: function (data) {
            var option = `<option value="" disabled selected>Seleccionar</option>`;
            for (var $i = 0; $i < data.length; $i++) {
                option += `<option value="${data[$i].emple_id}">${data[$i].perso_nombre} ${data[$i].perso_apPaterno} ${data[$i].perso_apMaterno}</option>`;
            }
            container.append(option);
            $("#empleado").val(value);
        },
        error: function () {},
    });
});

$("#empleado").on("select2:close", function () {
    if ($(this).val() != "") {
        onMostrarPantallas();
    }
});

function fechaHoy() {
    f = moment().format("YYYY-MM-DD");
    fechaValue.setDate(f);
    onMostrarPantallas();
}

function enteroTime(tiempo) {
    var hora = Math.floor( tiempo / 3600 ); 
    var minutos = Math.floor(tiempo / 60);
    var segundos = tiempo % 60;
    var resultado = ("0" + hora).slice(-2) + ":" + ("0" + minutos).slice(-2) + ":" + ("0" + segundos).slice(-2);
    return resultado;
}

function refreshCapturas() {
    onMostrarPantallas();
    var value = $("#empleado").val();
    $("#empleado").empty();
    var container = $("#empleado");
    $.ajax({
        async: false,
        url: "/tareas/empleadoR",
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
        },
        success: function (data) {
            var option = `<option value="" disabled selected>Seleccionar</option>`;
            for (var $i = 0; $i < data.length; $i++) {
                option += `<option value="${data[0].emple_id}">${data[0].perso_nombre} ${data[0].perso_apPaterno} ${data[0].perso_apMaterno}</option>`;
            }
            container.append(option);
            $("#empleado").val(value);
        },
        error: function () {},
    });
}
//CAPTURAS
$(function () {
    // $('#empleado').on('change', onMostrarPantallas);
    $("#fecha").on("change", onMostrarPantallas);
    $("#proyecto").on("change", onMostrarPantallas);
});
var datos;
var promedioHoras = 0;

function onMostrarPantallas() {
    var value = $("#empleado").val();
    var fecha = $("#fecha").val();
    var proyecto = $("#proyecto").val();
    $("#card").empty();
    $("#espera").show();
    $.ajax({
        url: "tareas/show",
        method: "GET",
        data: {
            value: value,
            fecha: fecha,
            proyecto: proyecto,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
        },
        success: function (data) {
            var vacio = `<img id="VacioImg" style="margin-left:28%" src="admin/images/search-file.svg"
                class="mr-2" height="220" /> <br> <label for=""
                style="margin-left:30%;color:#7d7d7d">Realize una búsqueda para ver Actividad</label>`;
            $("#espera").hide();
            datos = data;
            if (data.length != 0) {
                $.notifyClose();
                $.notify(
                    {
                        message: "\nCapturas encontradas.",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        icon_type: "image",
                        newest_on_top: true,
                        delay: 5000,
                        template:
                            '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            "</div>",
                        spacing: 35,
                    }
                );
                var container = $("#card");
                var $i = 0;
                for (let index = 0; index < data.length; index++) {
                    $("#promHoras" + $i).empty();
                    var horaDelGrupo = data[index].horaCaptura;
                    var hora = data[index].horaCaptura;
                    var promedios = 0;
                    var promedio = 0;
                    var prom = 0;
                    var sumaRangos = 0;
                    var totalCM = 0;
                    var hora_inicial = "";
                    var hora_final = "";
                    var labelDelGrupo =
                        horaDelGrupo +
                        ":00:00" +
                        " - " +
                        (parseInt(horaDelGrupo) + 1) +
                        ":00:00";
                    var grupo = `<span style="font-weight: bold;color:#6c757d;cursor:default">${labelDelGrupo}</span>&nbsp;&nbsp;<img src="landing/images/punt.gif" height="70">&nbsp;&nbsp;
                    <span class="promHoras" style="font-weight: bold;color:#6c757d;cursor:default" id="promHoras${$i}" data-toggle="tooltip" data-placement="right" title="Actividad por Hora"
                    data-original-title=""></span><br><br><div class="row">`;
                    for (var j = 0; j < 6; j++) {
                        if (data[index].minutos[j] != undefined) {
                            var capturas = "";
                            for (
                                let indexMinutos = 0;
                                indexMinutos < data[index].minutos[j].length;
                                indexMinutos++
                            ) {
                                if (data[index].minutos[j].length > 1) {
                                    promedios =
                                        promedios +
                                        data[index].minutos[j][indexMinutos]
                                            .prom;
                                    sumaRangos =
                                        sumaRangos +
                                        data[index].minutos[j][indexMinutos]
                                            .rango;
                                    hora_inicial =
                                        data[index].minutos[j][0].hora_ini;
                                    hora_final =
                                        data[index].minutos[j][
                                            data[index].minutos[j].length - 1
                                        ].hora_fin;
                                    if (
                                        data[index].minutos[j][indexMinutos]
                                            .imagen != null
                                    ) {
                                        capturas += `<div class = "carousel-item">
                                        <img src="data:image/jpeg;base64,${
                                            data[index].minutos[j][indexMinutos]
                                                .imagen
                                        }" height="120" width="200" class="img-responsive">
                                        <div class="overlay">
                                        <a class="info" onclick="zoom('${
                                            hora + "," + j
                                        }')" style="color:#fdfdfd">
                                        <i class="fa fa-eye"></i> Colección</a>
                                        </div>
                                    </div>`;
                                    }
                                }
                            }
                            if (data[index].minutos[j].length == 1) {
                                hora_inicial =
                                    data[index].minutos[j][0].hora_ini;
                                hora_final = data[index].minutos[j][0].hora_fin;
                                // var totalR = parseFloat(
                                //     data[index].minutos[j][0].rango / 60
                                // );
                                var totalR = enteroTime(data[index].minutos[j][0].rango);
                                totalM = totalR;
                                if (totalM > 10) {
                                    totalCM = 10;
                                } else {
                                    totalCM = totalM;
                                }
                                promedio = data[index].minutos[j][0].prom;
                            } else {
                                if (sumaRangos == 0) {
                                    totalCM = 0;
                                } else {
                                    // var totalR = parseFloat(sumaRangos / 60);
                                    var totalR = enteroTime(sumaRangos);
                                    totalM = totalR;
                                    if (totalM > 10) {
                                        totalCM = 10;
                                    } else {
                                        totalCM = totalM;
                                    }
                                }
                                promedio = (
                                    promedios / data[index].minutos[j].length
                                ).toFixed(2);
                                if (promedios == 0) {
                                    promedio = 0;
                                }
                                promedios = 0;
                                sumaRangos = 0;
                            }
                            var nivel;
                            if (promedio >= 50) nivel = "green";
                            else if (promedio > 35) nivel = "#f3c623";
                            else nivel = "red";
                            if (data[index].minutos[j][0].imagen != null) {
                                card = `<div class="col-2" style="margin-left: 0px!important;">
                                        <div class="mb-0 text-center" style="padding-left: 0px;">
                                            <a href="" class="col text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                                aria-expanded="true" aria-controls="customaccorcollapseOne">
                                            </a>
                                            <div class="collapse show" aria-labelledby="customaccorheadingOne" data-parent="#customaccordion_exa">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class=" text-center col-md-12 col-sm-6" style="padding-top: 4px;
                                                    padding-bottom: 4px;">
                                                        <h5 class="m-0 font-size-16" style="color:#1f4068;font-weight:bold;"><img src="landing/images/2143150.png" class="mr-2" height="20"/>${
                                                            data[index].minutos[
                                                                j
                                                            ][0].Activi_Nombre
                                                        } </h5>
                                                    </div><br>
                                                    <div class="col-md-12 col-sm-6" style="padding-left: 0px;;padding-right: 0px">
                                                    <div class="hovereffect">
                                                        <div  id="myCarousel${
                                                            hora + j
                                                        }" class = "carousel carousel-fade" data-ride = "carousel">
                                                            <div class = "carousel-inner">
                                                                <div class = "carousel-item active">
                                                                    <img src="data:image/jpeg;base64,${
                                                                        data[
                                                                            index
                                                                        ]
                                                                            .minutos[
                                                                            j
                                                                        ][0]
                                                                            .imagen
                                                                    }" height="120" width="200" class="img-responsive">
                                                                    <div class="overlay">
                                                                    <a class="info" onclick="zoom('${
                                                                        hora +
                                                                        "," +
                                                                        j
                                                                    }')" style="color:#fdfdfd">
                                                                    <i class="fa fa-eye"></i> Colección</a>
                                                                    </div>
                                                                </div>
                                                                ${capturas}
                                                            </div>
                                                            <a class = "carousel-control-prev" href = "#myCarousel${
                                                                hora + j
                                                            }" role = "button" data-slide = "prev">
                                                                <span class = "carousel-control-prev-icon" aria-hidden = "true"></span>
                                                                <span class = "sr-only">Previous</span>
                                                            </a>
                                                            <a class = "carousel-control-next" href = "#myCarousel${
                                                                hora + j
                                                            }" role = "button" data-slide = "next">
                                                                <span class = "carousel-control-next-icon" aria-hidden = "true"></span>
                                                                <span class = "sr-only">Next</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    &nbsp;
                                                    <label style="font-size: 12px" for="">${hora_inicial} - ${hora_final}</label>
                                                    <div class="progress" style="background-color: #d4d4d4;" data-toggle="tooltip" data-placement="bottom" title="Actividad por Rango de Tiempo"
                                                    data-original-title="">
                                                        <div class="progress-bar" role="progressbar" style="width:${promedio}%;background:${nivel}" aria-valuenow=${promedio}
                                                            aria-valuemin="0" aria-valuemax="100">${
                                                                promedio + "%"
                                                            }</div>
                                                    </div>
                                                    </div>
                                                    <label style="font-size: 12px;font-style: italic; bold;color:#1f4068;" for="">Total de ${totalCM} minutos</label>
                                                    <br>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>`;
                            } else {
                                card = `<div class="col-2" style="margin-left: 0px!important;">
                                        <div class="mb-0 text-center" style="padding-left: 0px;">
                                            <a href="" class="col text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                                aria-expanded="true" aria-controls="customaccorcollapseOne">
                                            </a>
                                            <div class="collapse show" aria-labelledby="customaccorheadingOne" data-parent="#customaccordion_exa">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class=" text-center col-md-12 col-sm-6" style="padding-top: 4px;
                                                    padding-bottom: 4px;">
                                                        <h5 class="m-0 font-size-16" style="color:#1f4068;font-weight:bold;"><img src="landing/images/2143150.png" class="mr-2" height="20"/>${
                                                            data[index].minutos[
                                                                j
                                                            ][0].Activi_Nombre
                                                        } </h5>
                                                    </div><br>
                                                    <div class="col-md-12 col-sm-6" style="padding-left: 0px;;padding-right: 0px">
                                                    <img src="landing/images/3155773.png" height="100">
                                                    &nbsp;
                                                    <label style="font-size: 12px" for="">${hora_inicial} - ${hora_final}</label>
                                                    <div class="progress" style="background-color: #d4d4d4;" data-toggle="tooltip" data-placement="bottom" title="Actividad por Rango de Tiempo"
                                                    data-original-title="">
                                                        <div class="progress-bar" role="progressbar" style="width:${promedio}%;background:${nivel}" aria-valuenow=${promedio}
                                                            aria-valuemin="0" aria-valuemax="100">${
                                                                promedio + "%"
                                                            }</div>
                                                    </div>
                                                    </div>
                                                    <label style="font-size: 12px;font-style: italic; bold;color:#1f4068;" for="">Total de ${totalCM} minutos</label>
                                                    <br>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>`;
                            }
                            grupo += card;
                            prom = prom + parseFloat(promedio);
                        } else {
                            card = `<div class="col-2" style="margin-left: 0px!important;justify-content:center;!important">
                        <br><br><br>
                                <div class="mb-0">
                                    <a href="" class="text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                        aria-expanded="true" aria-controls="customaccorcollapseOne">
                                    </a>
                                    <div class="collapse show" aria-labelledby="customaccorheadingOne"
                                        data-parent="#customaccordion_exa">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class=" text-center col-md-12 col-sm-12" style="padding-top: 1px;
                                            padding-bottom: 4px;">
                                            <img src="landing/images/3155773.png" height="100">
                                                <h5 class="m-0 font-size-14" style="color:#8888">Vacio</h5>
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
                    promedioHoras = (prom / 6).toFixed(2);
                    var span = "";
                    span += `${promedioHoras}%`;
                    $("#promHoras" + $i).append(span);
                    $('[data-toggle="tooltip"]').tooltip();
                    $i = $i + 1;
                }
            } else {
                $("#card").empty();
                if ($("#empleado").val() == null) {
                    $("#card").append(vacio);
                    $.notifyClose();
                    $.notify({
                        message: "Falta elegir empleado.",
                        icon: "admin/images/warning.svg",
                    });
                }
                if ($("#fecha").val() == "") {
                    $("#card").append(vacio);
                    $.notifyClose();
                    $.notify({
                        message: "Falta elegir fecha a buscar.",
                        icon: "admin/images/warning.svg",
                    });
                }
                if (
                    data.length == 0 &&
                    $("#empleado").val() != null &&
                    $("#fecha").val() != ""
                ) {
                    $("#card").append(vacio);
                    $.notifyClose();
                    $.notify({
                        message: "No se encontratron capturas.",
                        icon: "admin/images/warning.svg",
                    });
                }
            }
        },
        error: function (data) {},
    });
}

//PROYECTO
$(function () {
    $("#empleado").on("change", onMostrarProyecto);
});

function onMostrarProyecto() {
    var value = $("#empleado").val();
    $.ajax({
        url: "tareas/proyecto",
        method: "GET",
        data: {
            value: value,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
        },
        success: function (data) {
            var html_select = '<option value="">Seleccionar</option>';
            for (var i = 0; i < data.length; i++)
                html_select +=
                    '<option value="' +
                    data[i].Proye_id +
                    '">' +
                    data[i].Proye_Nombre +
                    "</option>";
            $("#proyecto").html(html_select);
        },
    });
}

function zoom(horayJ) {
    var onlyHora = horayJ.split(",")[0];
    var j = horayJ.split(",")[1];
    capturas = [];
    datos.forEach((hora) => {
        if (hora.horaCaptura == onlyHora) {
            capturas = hora.minutos[j];
        }
    });
    var carusel = `<p class="imglist" style="max-width: 1000px;">`;
    for (let index = 0; index < capturas.length; index++) {
        const element = capturas[index];
        carusel += `<a href="data:image/jpeg;base64,${element.imagen}" data-fancybox="images" data-caption="Hora de captura a las ${element.hora_fin}"><img src="data:image/jpeg;base64,${element.imagen}" width="350" height="300" style="padding-right:10px;padding-bottom:10px"></a>`;
    }
    carusel += `</p>`;
    document.getElementById("zoom").innerHTML = carusel;
    $("#modalZoom").modal();
}
$("#myCarousel").carousel({
    interval: 2000,
});
$("[data-fancybox]").fancybox({
    protect: true,
});
