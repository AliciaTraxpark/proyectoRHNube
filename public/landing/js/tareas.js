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
$(function () {
    f = moment().format("YYYY-MM-DD");
    fechaValue.setDate(f);
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
        error: function () { },
    });
});

// $("#empleado").on("select2:close", function () {
//     if ($(this).val() != "") {
//         onMostrarPantallas();
//     }
// });

function fechaHoy() {
    f = moment().format("YYYY-MM-DD");
    fechaValue.setDate(f);
    onMostrarPantallas();
}

function enteroTime(tiempo) {
    var hour = Math.floor(tiempo / 3600);
    hour = (hour < 10) ? '0' + hour : hour;
    var minute = Math.floor((tiempo / 60) % 60);
    minute = (minute < 10) ? '0' + minute : minute;
    var second = tiempo % 60;
    second = (second < 10) ? '0' + second : second;
    return hour + ':' + minute + ':' + second;
}

function refreshCapturas() {
    onMostrarPantallas();
}
function buscarCapturas() {
    var empleado = $("#empleado").val();
    if (empleado != null) {
        onMostrarPantallas();
    } else {
        $.notifyClose();
        $.notify({
            message: "Elegir empleado.",
            icon: "admin/images/warning.svg",
        });
    }
}
//CAPTURAS
// $(function () {
//     $("#fecha").on("change", onMostrarPantallas);
//     $("#proyecto").on("change", onMostrarPantallas);
// });
var datos;
var promedioHoras = 0;

function onMostrarPantallas() {
    var value = $("#empleado").val();
    var fecha = $("#fecha").val();
    if (value != null) {
        $("#card").empty();
        console.log("ingreso");
        $.ajax({
            url: "tareas/show",
            method: "GET",
            data: {
                value: value,
                fecha: fecha,
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
            beforeSend: function () {

                $("#espera").show();
            }
        }).then(function (data) {
            console.log(data);
            var vacio = `<img id="VacioImg" style="margin-left:28%" src="admin/images/search-file.svg"
            class="mr-2" height="220" /> <br> <label for=""
            style="margin-left:30%;color:#7d7d7d">Realize una búsqueda para ver Actividad</label>`;
            $("#espera").hide();
            datos = data;
            if (data.length != 0) {
                var container = $("#card");
                var $i = 0;
                var actividadDiariaTotal = 0;
                var rangoDiarioTotal = 0;
                var promedioDiaria = 0;
                var actividadDiaria = `<div class="row justify-content-center p-3"><div class="col-xl-4"><span style="font-weight: bold;color:#163552;cursor:default;font-size:14px;"><img src="landing/images/velocimetro (1).svg" class="mr-2" height="20"/>Actividad Diaria | <span id="totalActivi"></span> - <span id="totalH"></span></span></div></div>`;
                container.append(actividadDiaria);
                for (let index = 0; index < data.length; index++) {
                    $("#promHoras" + $i).empty();
                    var horaDelGrupo = data[index].horaCaptura;
                    var hora = data[index].horaCaptura;
                    var promedios = 0;
                    var promedio = 0;
                    var sumaRangosTotal = 0;
                    var sumaRangos = 0;
                    var sumaActividad = 0;
                    var sumaActividadTotal = 0;
                    var totalActividadRango = 0;
                    var totalCM = 0;
                    var hora_inicial = "";
                    var hora_final = "";
                    var labelDelGrupo =
                        horaDelGrupo +
                        ":00:00" +
                        " - " +
                        (parseInt(horaDelGrupo) + 1) +
                        ":00:00";
                    var grupo = `<span style="font-weight: bold;color:#163552;cursor:default">${labelDelGrupo}</span>&nbsp;&nbsp;<img src="landing/images/punt.gif" height="70">&nbsp;&nbsp;
                <span class="promHoras" style="font-weight: bold;color:#163552;cursor:default" id="totalHoras${$i}" data-toggle="tooltip" data-placement="right" title="Tiempo por Hora"
                data-original-title=""></span>&nbsp;&nbsp;-&nbsp;&nbsp;<span class="promHoras" style="font-weight: bold;color:#163552;cursor:default" id="promHoras${$i}" data-toggle="tooltip" data-placement="right" title="Actividad por Hora"
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
                                            .tiempoA;
                                    sumaRangos =
                                        sumaRangos +
                                        data[index].minutos[j][indexMinutos]
                                            .rango;
                                    sumaActividad = sumaActividad + data[index].minutos[j][indexMinutos].tiempoA;
                                    hora_inicial =
                                        data[index].minutos[j][0].hora_ini;
                                    hora_final =
                                        data[index].minutos[j][
                                            data[index].minutos[j].length - 1
                                        ].hora_fin;
                                    for (let indexC = 0; indexC < data[index].minutos[j][indexMinutos].imagen.length; indexC++) {
                                        if (data[index].minutos[j][indexMinutos].imagen[indexC].imagen != null) {
                                            capturas += `<div class = "carousel-item">
                                    <img src="data:image/jpeg;base64,${data[index].minutos[j][indexMinutos].imagen[indexC].imagen}" height="120" width="200" class="img-responsive">
                                    <div class="overlay">
                                    <a class="info" onclick="zoom('${hora + "," + j
                                                }')" style="color:#fdfdfd">
                                    <i class="fa fa-eye"></i> Colección</a>
                                    </div>
                                </div>`;
                                        }
                                    }
                                }
                            }
                            if (data[index].minutos[j].length == 1) {
                                hora_inicial =
                                    data[index].minutos[j][0].hora_ini;
                                hora_final = data[index].minutos[j][0].hora_fin;
                                var totalR = enteroTime(
                                    data[index].minutos[j][0].rango
                                );
                                sumaRangosTotal += data[index].minutos[j][0].rango;
                                totalCM = totalR;
                                promedio = data[index].minutos[j][0].prom;
                                sumaActividadTotal += data[index].minutos[j][0].tiempoA;
                            } else {
                                sumaRangosTotal += sumaRangos;
                                sumaActividadTotal += sumaActividad;
                                var totalR = enteroTime(sumaRangos);
                                totalCM = totalR;
                                promedio = (
                                    (promedios / sumaRangos) * 100
                                ).toFixed(2);
                                if (promedios == 0) {
                                    promedio = 0;
                                }
                                promedios = 0;
                                sumaRangos = 0;
                                sumaActividad = 0;
                            }
                            var nivel;
                            if (promedio >= 50) nivel = "green";
                            else if (promedio > 35) nivel = "#f3c623";
                            else nivel = "red";
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
                                                    <h5 class="m-0 font-size-16" style="color:#1f4068;font-weight:bold;"><img src="landing/images/2143150.png" class="mr-2" height="20"/>${data[index].minutos[
                                    j
                                ][0].Activi_Nombre
                                } </h5>
                                                </div><br>
                                                <div class="col-md-12 col-sm-6" style="padding-left: 0px;;padding-right: 0px">
                                                <div class="hovereffect">
                                                    <div  id="myCarousel${hora + j
                                }" class = "carousel carousel-fade" data-ride = "carousel">
                                                        <div class = "carousel-inner">
                                                            <div class = "carousel-item active"><img src="data:image/jpeg;base64,${data[index].minutos[j][0].imagen[0].imagen}" height="120" width="200" class="img-responsive">
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
                                                <label style="font-size: 12px" for="">${hora_inicial} - ${hora_final}</label>
                                                <div class="progress" style="background-color: #d4d4d4;" data-toggle="tooltip" data-placement="bottom" title="Actividad por Rango de Tiempo"
                                                data-original-title="">
                                                    <div class="progress-bar" role="progressbar" style="width:${promedio}%;background:${nivel}" aria-valuenow=${promedio}
                                                        aria-valuemin="0" aria-valuemax="100">${promedio + "%"
                                }</div>
                                                </div>
                                                </div>
                                                <label style="font-size: 12px;font-style: italic; bold;color:#1f4068;" for="">Tiempo transcurrido ${totalCM} </label>
                                                <br>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>`;
                            grupo += card;
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
                    totalActividadRango = ((sumaActividadTotal / sumaRangosTotal) * 100).toFixed(
                        2
                    );
                    var span = "";
                    span += `${totalActividadRango}%`;
                    $("#promHoras" + $i).append(span);
                    var spanH = "";
                    var valorH = enteroTime(sumaRangosTotal);
                    spanH += `${valorH}`;
                    $("#totalHoras" + $i).append(spanH);
                    actividadDiariaTotal = actividadDiariaTotal + sumaActividadTotal;
                    rangoDiarioTotal = rangoDiarioTotal + sumaRangosTotal;
                    sumaActividadTotal = 0;
                    sumaRangosTotal = 0;
                    $('[data-toggle="tooltip"]').tooltip();
                    $i = $i + 1;
                }
                $("#totalActivi").empty();
                $("#totalH").empty();
                console.log(actividadDiariaTotal, rangoDiarioTotal);
                promedioDiaria = ((actividadDiariaTotal / rangoDiarioTotal) * 100).toFixed(2);
                var cont = `${promedioDiaria}%`;
                var enteroT = enteroTime(rangoDiarioTotal);
                var contE = `${enteroT}`;
                $("#totalActivi").append(cont);
                $("#totalH").append(contE);
            } else {
                $("#card").empty();
                if ($("#empleado").val() == null) {
                    $("#card").append(vacio);
                    $.notifyClose();
                    $.notify({
                        message: "Elegir empleado.",
                        icon: "admin/images/warning.svg",
                    });
                }
                if ($("#fecha").val() == "") {
                    $("#card").append(vacio);
                    $.notifyClose();
                    $.notify({
                        message: "Elegir fecha.",
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
        }).fail(function () {
            $.notify({
                message: '\nSurgio un error.',
                icon: 'landing/images/bell.svg',
            }, {
                icon_type: 'image',
                allow_dismiss: true,
                newest_on_top: true,
                delay: 6000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #f2dede;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        }).always(function () {
            // $.notifyClose();
            // $.notify(
            //     {
            //         message: "\nCapturas encontradas.",
            //         icon: "admin/images/checked.svg",
            //     },
            //     {
            //         icon_type: "image",
            //         newest_on_top: true,
            //         delay: 5000,
            //         template:
            //             '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
            //             '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
            //             '<img data-notify="icon" class="img-circle pull-left" height="20">' +
            //             '<span data-notify="title">{1}</span> ' +
            //             '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
            //             "</div>",
            //         spacing: 35,
            //     }
            // );
        });
    }
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
    var carusel = "";
    for (let index = 0; index < capturas.length; index++) {
        const element = capturas[index].imagen;
        element.forEach(dato => {
            $.ajax({
                url: "/mostrarCapturas",
                method: "GET",
                data: {
                    idCaptura: dato.idImagen,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                statusCode: {
                    401: function () {
                        location.reload();
                    }
                },
                beforeSend: function () {

                    $("#esperaImg").show();
                },
            }).then(function (data) {
                $("#esperaImg").hide();
                if (data.length > 0) {
                    carusel = `<a href="data:image/jpeg;base64,${data[0].imagen}" data-fancybox="images" data-caption="Hora de captura a las ${data[0].hora_ini}" data-width="2048" data-height="1365"><img src="data:image/jpeg;base64,${data[0].imagen}" width="350" height="300" style="padding-right:10px;padding-bottom:10px"></a>`;
                    document.getElementById("zoom").innerHTML += carusel;
                }
            }).fail(function () {
                $("#esperaImg").hide();
                $.notify({
                    message: '\nSurgio un error.',
                    icon: 'landing/images/bell.svg',
                }, {
                    icon_type: 'image',
                    allow_dismiss: true,
                    newest_on_top: true,
                    delay: 6000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #f2dede;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            });
        });
    }
    document.getElementById("zoom").innerHTML = carusel;
    $("#modalZoom").modal();
}
$("#myCarousel").carousel({
    interval: 2000,
});
$("[data-fancybox]").fancybox({
    protect: true,
});
