// ? CONFIGURACION DE IDIOMA DE SELECT
$.fn.select2.defaults.set('language', 'es');
// ? CONFIGURACION DE FECHA
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "single",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "D, j F",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: true,
    disableMobile: "true"
});
$(function () {
    f = moment().format("YYYY-MM-DD");
    fechaValue.setDate(f);
});

// ? CONFIGURACION DE EMPLEADO
$("#empleado").select2({
    minimumInputLength: 1,
    language: {
        inputTooShort: function (e) {
            return "Escribir nombre o apellido";
        },
        loadingMore: function () { return "Cargando más resultados…" },
        noResults: function () { return "No se encontraron resultados" }
    }
});
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

// ? BOTON DE BUSQUEDA
function buscarUbicaciones() {
    var empleado = $("#empleado").val();
    if (empleado != null) {
        // onMostrarUbicaciones();
        onMostrarPantallas();
    } else {
        $.notifyClose();
        $.notify({
            message: "Elegir empleado.",
            icon: "admin/images/warning.svg",
        });
    }
}
// ? ENTERO TIEMPO
function enteroTime(tiempo) {
    var hour = Math.floor(tiempo / 3600);
    hour = (hour < 10) ? '0' + hour : hour;
    var minute = Math.floor((tiempo / 60) % 60);
    minute = (minute < 10) ? '0' + minute : minute;
    var second = tiempo % 60;
    second = (second < 10) ? '0' + second : second;
    return hour + ':' + minute + ':' + second;
}
var dato = {};
var promedioHoras = 0;
// ? FUNCION DE BUSQUEDA CAPTURAS
function onMostrarPantallas() {
    var value = $("#empleado").val();
    var fecha = $("#fecha").val();
    if (value != null) {
        $("#card").empty();
        $.ajax({
            url: "tareas/showP",
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
            var vacio = `<img id="VacioImg" style="margin-left:28%" src="admin/images/search-file.svg"
            class="mr-2 imgR" height="220" /> <br> <label for=""
            style="margin-left:30%;color:#7d7d7d" class="imgR">Realize una búsqueda para ver Actividad</label>`;
            $("#espera").hide();
            dato = data;
            if (data.length != 0) {
                var container = $("#card");
                var $i = 0;
                var actividadDiariaTotal = 0;
                var rangoDiarioTotal = 0;
                var promedioDiaria = 0;
                var actividadDiaria = `<div class="row justify-content-center p-3"><div class="col-xl-4 text-center"><span style="font-weight: bold;color:#163552;cursor:default;font-size:14px;"><img src="landing/images/velocimetro (1).svg" class="mr-2" height="20"/>Actividad Diaria | <span id="totalActivi"></span> - <span id="totalH"></span></span></div></div>`;
                container.append(actividadDiaria);
                for (let index = 0; index < data.length; index++) {
                    $("#promHoras" + $i).empty();
                    var horaDelGrupo = data[index].hora;
                    var hora = data[index].hora;
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
                    var grupo = `<div class="row pt-2 rowResponsivo"><span style="font-weight: bold;color:#163552;cursor:default">${labelDelGrupo}</span>&nbsp;&nbsp;<img src="landing/images/punt.gif" height="20">&nbsp;&nbsp;
                <span class="promHoras" style="font-weight: bold;color:#163552;cursor:default" id="totalHoras${$i}" data-toggle="tooltip" data-placement="right" title="Tiempo por Hora"
                data-original-title=""></span>&nbsp;&nbsp;-&nbsp;&nbsp;<span class="promHoras" style="font-weight: bold;color:#163552;cursor:default" id="promHoras${$i}" data-toggle="tooltip" data-placement="right" title="Actividad por Hora"
                data-original-title=""></span></div><br><br><div class="container-fluid containerR"><div class="row rowResp">`;
                    for (var j = 0; j < 6; j++) {
                        // TODO: Obtener datos del array minutos de dicha hora
                        if (data[index].minuto[j] != undefined) {
                            var capturas = "";
                            // ? RECORREMOS EL ARRAY DE CAPTURAS
                            for (let indexMinutos = 0; indexMinutos < data[index].minuto[j]["captura"].length; indexMinutos++) {
                                if (data[index].minuto[j]["captura"].length > 1) {
                                    promedios = promedios + data[index].minuto[j]["captura"][indexMinutos].tiempoA; //* suma de promedios de grupos de imagenes
                                    sumaRangos = sumaRangos + data[index].minuto[j]["captura"][indexMinutos].rango; //* suma de rangos de grupos de imagenes
                                    sumaActividad = sumaActividad + data[index].minuto[j]["captura"][indexMinutos].tiempoA; //* suma de actividad de grupos de imagenes
                                    hora_inicial = data[index].minuto[j]["captura"][0].hora_ini; //* hora inicial de la primera imagen del grupo de imagenes
                                    hora_final = data[index].minuto[j]["captura"][data[index].minuto[j].length - 1].hora_fin; //* hora final de la ultima imagen del grupo de imagenes
                                    for (let indexC = 0; indexC < data[index].minuto[j]["captura"][indexMinutos].imagen.length; indexC++) { //* recorrer imagenes para insertar en el carrusel
                                        if (data[index].minuto[j]["captura"][indexMinutos].imagen[indexC].imagen != null) { //* solo grupos que tienen imagenes
                                            var imgR = data[index].minuto[j]["captura"][indexMinutos].imagen[indexC].imagen; //* obtener ruta de la imagen
                                            var rspI = imgR.replace(/\//g, "-"); //* cambair carateres
                                            var encr = CryptoJS.enc.Utf8.parse(rspI); //* encriptar ruta
                                            var base64 = CryptoJS.enc.Base64.stringify(encr); //* convertir ruta en base 64
                                            // TODO: Colocar imagenes en carrusel 
                                            //* @parametro base64 tenemos la ruta de la imagen
                                            //* @hora y @j variables que enviamos para el modal zoom
                                            capturas += `<div class = "carousel-item">
                                                            <img src="mostrarMiniatura/${base64}" height="120" width="200" class="img-responsive">
                                                                <div class="overlay">
                                                                    <a class="info" onclick="zoom('${hora + "," + j}')" style="color:#fdfdfd">
                                                                    <i class="fa fa-eye"></i> Colección</a>
                                                                </div>
                                                        </div>`;
                                        }
                                    }
                                }
                            }
                            //: Colocar ubicaiones en carrusel
                            if (data[index].minuto[j]["captura"].length != 0) {
                                for (let indexMinutos = 0; indexMinutos < data[index].minuto[j]["ubicacion"].length; indexMinutos++) {
                                    capturas += `<div class = "carousel-item">
                                                    <img src="landing/images/map.svg" height="120" class="img-responsive">
                                                </div>`;
                                }
                            }
                            //: ******************************************************
                            if (data[index].minuto[j]["captura"].length == 1) { //* Solo encontramos una captura en el grupo de minutos
                                hora_inicial = data[index].minuto[j]["captura"][0].hora_ini; //* hora inicial de la imagen
                                hora_final = data[index].minuto[j]["captura"][0].hora_fin; //* hora final de la imagen
                                var totalR = enteroTime(data[index].minuto[j]["captura"][0].rango); //* convertimos el rango en time
                                sumaRangosTotal += data[index].minuto[j]["captura"][0].rango; //* sumar rangos
                                totalCM = totalR;
                                promedio = data[index].minuto[j]["captura"][0].prom; //* obtener promedio
                                sumaActividadTotal += data[index].minuto[j]["captura"][0].tiempoA; //* obtener suma de las actividades
                            } else {
                                if (data[index].minuto[j]["captura"].length != 0) { //: Validar solor tiene mas de unacaptura en el grupo de minutos
                                    sumaRangosTotal += sumaRangos;
                                    sumaActividadTotal += sumaActividad;
                                    var totalR = enteroTime(sumaRangos);
                                    totalCM = totalR;
                                    promedio = ((promedios / sumaRangos) * 100).toFixed(2);
                                    if (promedios == 0) {
                                        promedio = 0;
                                    }
                                    promedios = 0;
                                    sumaRangos = 0;
                                    sumaActividad = 0;
                                } else {
                                    promedio = data[index].minuto[j]["ubicacion"][0].actividad;
                                    var totalR = enteroTime(data[index].minuto[j]["ubicacion"][0].rango);
                                    totalCM = totalR;
                                }
                            }
                            //! Colores de las actividades
                            var nivel;
                            if (promedio >= 50) nivel = "green";
                            else if (promedio > 35) nivel = "#f3c623";
                            else nivel = "red";
                            //! **************************************
                            if (data[index].minuto[j]["captura"][0] != undefined) {
                                if (data[index].minuto[j]["captura"][0].imagen.length) { //* cuando tenemos imagenes 
                                    var imgR = data[index].minuto[j]["captura"][0].imagen[0].imagen; //* Obtener ruta de la imagen
                                    var rspI = imgR.replace(/\//g, "-"); //* Reemplazar caracteres de la ruta
                                    var encr = CryptoJS.enc.Utf8.parse(rspI); //* Encriptar ruta
                                    var base64 = CryptoJS.enc.Base64.stringify(encr); //* convertir en base 64
                                    card = `<div class="col-2 columResponsiva" style="margin-left: 0px!important;">
                                            <div class="mb-0 text-center" style="padding-left: 0px;">
                                                <a href="" class="col text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne"
                                                    aria-expanded="true" aria-controls="customaccorcollapseOne">
                                                </a>
                                                <div class="collapse show" aria-labelledby="customaccorheadingOne" data-parent="#customaccordion_exa">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class=" text-center col-md-12 col-sm-6 columnTextR" style="padding-top: 4px;padding-bottom: 4px;">
                                                                <h5 class="m-0 font-size-16 h5Responsive" style="color:#1f4068;font-weight:bold;">
                                                                    <img src="landing/images/2143150.png" class="mr-2" height="20"/>${data[index].minuto[j]["captura"][0].Activi_Nombre}
                                                                </h5>
                                                            </div><br>
                                                            <div class="col-md-12 col-sm-6" style="padding-left: 0px;;padding-right: 0px">
                                                                <div class="hovereffect">
                                                                    <div  id="myCarousel${hora + j}" class = "carousel carousel-fade" data-ride = "carousel">
                                                                        <div class = "carousel-inner">
                                                                            <div class = "carousel-item active">
                                                                                <img src="mostrarMiniatura/${base64}" height="120" width="200" class="img-responsive">
                                                                                <div class="overlay">
                                                                                    <a class="info" onclick="zoom('${hora + "," + j}')" style="color:#fdfdfd">
                                                                                        <i class="fa fa-eye"></i>Colección
                                                                                    </a>
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
                                                                <div class="progress" style="background-color: #d4d4d4;" data-toggle="tooltip" data-placement="bottom" title="Actividad por Rango de Tiempo" data-original-title="">
                                                                    <div class="progress-bar" role="progressbar" style="width:${promedio}%;background:${nivel}" aria-valuenow=${promedio} aria-valuemin="0" aria-valuemax="100">
                                                                        ${promedio + "%"}
                                                                    </div>
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
                                } else { //* obtenemos data PERO capturas no 
                                    card = `<div class="col-2 columResponsiva" style="margin-left: 0px!important;">
                                            <div class="mb-0 text-center" style="padding-left: 0px;">
                                                <a href="" class="col text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne" aria-expanded="true" aria-controls="customaccorcollapseOne"></a>
                                                <div class="collapse show" aria-labelledby="customaccorheadingOne" data-parent="#customaccordion_exa">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class=" text-center col-md-12 col-sm-6" style="padding-top: 4px;padding-bottom: 4px;">
                                                                <h5 class="m-0 font-size-16" style="color:#1f4068;font-weight:bold;">
                                                                    <img src="landing/images/2143150.png" class="mr-2" height="20"/>${data[index].minutos[j]["captura"][0].Activi_Nombre}
                                                                </h5>
                                                            </div><br>
                                                            <div class="col-md-12" style="padding-left: 0px;;padding-right: 0px">
                                                                <div class=" text-center col-md-12 col-sm-12" style="padding-top: 1px;padding-bottom: 4px;">
                                                                    <img src="landing/images/3155773.png" height="100">
                                                                </div>
                                                                &nbsp;
                                                                <label style="font-size: 12px" for="">${hora_inicial} - ${hora_final}</label>
                                                                <div class="progress" style="background-color: #d4d4d4;" data-toggle="tooltip" data-placement="bottom" title="Actividad por Rango de Tiempo" data-original-title="">
                                                                    <div class="progress-bar" role="progressbar" style="width:${promedio}%;background:${nivel}" aria-valuenow=${promedio} aria-valuemin="0" aria-valuemax="100">${promedio + "%"}</div>
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
                                }
                            } else {
                                if (data[index].minuto[j]["ubicacion"][0] != undefined) {
                                    card = `<div class="col-2 columResponsiva" style="margin-left: 0px!important;margin-right: 0px!important">
                                                <div class="mb-0 text-center" style="padding-left: 0px;padding-top: 15px;">
                                                    <div class="collapse show" aria-labelledby="customaccorheadingOne" data-parent="#customaccordion_exa">
                                                        <div class="row">
                                                            <div class=" text-center col-md-12 col-sm-6 columnTextR" style="padding-top: 8px;padding-bottom: 6px;">
                                                                <h5 class="m-0 font-size-16 h5Responsive" style="color:#1f4068;font-weight:bold;">
                                                                    <img src="landing/images/2143150.png" class="mr-2" height="20"/>${data[index].minuto[j]["ubicacion"][0].Activi_Nombre}
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <div class="col-md-12 col-sm-12" style="padding-left: 0px;padding-right: 0px">
                                                            <div id="mapid${hora + "," + j}" onchange="javascript:ubicacionesMapa('${hora + "," + j}')" class="mapid"></div>
                                                        </div>
                                                        &nbsp;
                                                        <label style="font-size: 12px" for="">${data[index].minuto[j]["ubicacion"][0].hora_ini} - ${data[index].minuto[j]["ubicacion"][0].hora_fin}</label>
                                                        <div class="progress" style="background-color: #d4d4d4;" data-toggle="tooltip" data-placement="bottom" title="Actividad por Rango de Tiempo" data-original-title="">
                                                            <div class="progress-bar" role="progressbar" style="width:${promedio}%;background:${nivel}" aria-valuenow=${promedio} aria-valuemin="0" aria-valuemax="100">
                                                                ${promedio + "%"}
                                                            </div>
                                                        </div>
                                                        <label style="font-size: 12px;font-style: italic; bold;color:#1f4068;" for="">Tiempo transcurrido ${totalCM} </label>
                                                    </div>
                                                </div>
                                            </div>`;
                                    grupo += card;

                                }
                            }
                        } else {
                            card = `<div class="col-2 columResponsiva" style="margin-left: 0px!important;justify-content:center!important">
                                        <div class="mb-0" style="padding-top:70px">
                                            <a href="" class="text-dark" data-toggle="collapse" data-target="#customaccorcollapseOne" aria-expanded="true" aria-controls="customaccorcollapseOne"></a>
                                            <div class="collapse show" aria-labelledby="customaccorheadingOne" data-parent="#customaccordion_exa">
                                                <div class="row">
                                                    <div class="col-md-12 col-sm-6 text-center">
                                                        <img src="landing/images/3155773.png" height="100" class="imgResponsiva">
                                                        <h5 class="m-0 font-size-14 mbResponsivo" style="color:#8888">Vacio</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                            grupo += card;
                        }
                    }
                    grupo += `</div></div><br>`;
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
                promedioDiaria = ((actividadDiariaTotal / rangoDiarioTotal) * 100).toFixed(2);
                var cont = `${promedioDiaria}%`;
                var enteroT = enteroTime(rangoDiarioTotal);
                var contE = `${enteroT}`;
                $("#totalActivi").append(cont);
                $("#totalH").append(contE);
                changeMapeo();
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
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
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
        });
    }
}
// ? FUNCION DE BUSQUEDA
function onMostrarUbicaciones() {
    var value = $("#empleado").val();
    var fecha = $("#fecha").val();
    if (value != null) {
        $("#card").empty();
        $.ajax({
            async: false,
            url: "rutaU",
            method: "POST",
            data: {
                value: value,
                fecha: fecha
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
                dato = data;
                if (data.length != 0) {
                    var container = $("#card");
                    for (let index = 0; index < data.length; index++) {
                        var horaDelGrupo = data[index].horaUbicacion;
                        var hora = data[index].horaUbicacion;
                        var labelDelGrupo = horaDelGrupo + ":00:00" + "-" + (parseInt(horaDelGrupo) + 1) + ":00:00";
                        var grupo = `<div class="row p-3"><div class="row col-12 pt-2"><span>${labelDelGrupo}</span></div>`;
                        card = `<div class="col-2"><div id="mapid${hora}" onchange="javascript:ubicacionesMapa('${hora}')" class="mapid">
                                        </div> <div id="images"></div></div>`;
                        grupo += card;
                        grupo += `</div>`;
                        container.append(grupo);
                    }
                    changeMapeo();
                }
            },
            error: function () { },
        });
    }
}

function changeMapeo() {
    $('.mapid').trigger("change");
}
var arrayRecorrido = [];
var mapGlobal = {};
var controlGlobal = {};
function ubicacionesMapa(horayJ) {

    var onlyHora = horayJ.split(",")[0];
    var min = horayJ.split(",")[1];
    var map = L.map('mapid' + horayJ).fitWorld();
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        tileSize: 512,
        minZoom: 10,
        zoomOffset: -1
    }).addTo(map);
    // ? RECORRED DATOS PARA POPUP
    arrayDatos = [];
    respuesta = [];
    for (let index = 0; index < dato.length; index++) {
        if (dato[index].hora == onlyHora) {
            for (var j = 0; j < 6; j++) {
                // console.log(dato[index].minuto[j], min);
                if (j == min) {
                    const ubicacion = dato[index].minuto[j].ubicacion;
                    for (var i = 0; i < ubicacion.length; i++) {
                        const valor = ubicacion[i].ubicaciones;
                        valor.forEach(element => {
                            arrayDatos.push(element.latitud_ini + "," + element.longitud_ini + "," + ubicacion[i].hora_ini, element.latitud_fin + "," + element.longitud_fin + "," + ubicacion[i].hora_fin);
                        });
                    }
                }
            }
        }
    }
    respuesta.push(arrayDatos);
    //: Guardar datos para recorrido por hora
    if (arrayRecorrido.length == 0) { //* agregar normal si el array esta vacio
        let recorrido = { "hora": onlyHora, "recorrido": arrayDatos };
        arrayRecorrido.push(recorrido);
    } else {
        arrayRecorrido.forEach(element => {
            if (!element.hora.includes(onlyHora)) {
                let recorrido = { "hora": onlyHora, "recorrido": arrayDatos };
                arrayRecorrido.push(recorrido);
            } else {
                arrayDatos.forEach(ubicacion => {
                    element.recorrido.push(ubicacion);
                });
            }
        });
    }
    //: **************************************************************
    var latlngArray = [];
    var popupArray = [];
    // ? DIBUJAR MAPA DEL USUARIO SEGUN SUS POSICIONES
    for (let index = 0; index < respuesta[0].length; index++) {
        var element = respuesta[0][index];
        var ltln = L.latLng(element.split(",")[0], element.split(",")[1]);
        latlngArray.push(ltln);
    }

    //: API DE ENRUTAMIENTO
    mapboxRouting = L.Routing.mapbox('pk.eyJ1IjoiZ2FieXJvc21lcmkiLCJhIjoiY2tobTVkazEyMTV5dDJ5bzc2MmE4OWZtZSJ9.2jqmQl43ljmcZSP02R4Rew', { profile: 'mapbox/walking' });
    //: Este es usando routes
    var control = L.Routing.control({
        createMarker: function (i, wp, nWps) {
            var popup = L.marker(wp.latLng)
                .bindPopup('Hora: ' + respuesta[0][i].split(",")[2]);
            popupArray.push(respuesta[0][i].split(",")[2]);
            return popup;
        },
        router: mapboxRouting,
        waypoints: latlngArray,
        lineOptions: {
            styles: [
                { color: '#ffe05d', opacity: 1, weight: 4 }
            ],
        },
        routeWhileDragging: true,
        show: false,
        draggableWaypoints: false,//to set draggable option to false
        addWaypoints: false, //disable adding new waypoints to the existing path
        fitSelectedRoutes: true,
        useZoomParameter: true
    }).addTo(map).on('routesfound', function (e) {
        console.log(e.routes); // e.routes have length 2
    });
    L.easyButton({
        states: [{
            stateName: 'zoom-to-modal',
            icon: 'fa-external-link',
            title: 'ver recorrido',
            onClick: function (btn, map) {
                recorrido(onlyHora);
            }
        }]

    }).addTo(map);
}
function initializingMap() // call this method before you initialize your map.
{
    var container = L.DomUtil.get('mapRecorrido');
    if (container != null) {
        container._leaflet_id = null;
    }
}
//: FUNCION MOSTRAR RECORRIDO
function recorrido(hora) {
    $('#bodyMap').empty();
    var container = $('#bodyMap');
    var divMap = `<div id="mapRecorrido" class="mapRecorrido"></div>`;
    container.append(divMap);
    $('#modalRuta').modal();
    //: Horas en modal
    $('#horaIRecorrido').text(parseInt(hora) + ":00:00");
    $('#horaFRecorrido').text((parseInt(hora) + 1) + ":00:00");
    //* buscar hora en el array
    var popupArray = [];
    var latlngArrayRecorrido = [];
    arrayRecorrido.forEach(element => {
        if (element.hora == hora) {
            element.recorrido.forEach(index => {
                var ltln = L.latLng(index.split(",")[0], index.split(",")[1]);
                latlngArrayRecorrido.push(ltln);
                popupArray.push(index.split(",")[2]);
            });
        }
    });
    initializingMap();
    mapGlobal = L.map('mapRecorrido', {
        minZoom: 12,
        zoomOffset: -1,
        center: [51.505, -0.09],
    });
    mapGlobal.invalidateSize();
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png').addTo(mapGlobal);
    //: API DE ENRUTAMIENTO
    mapboxRouting = L.Routing.mapbox('pk.eyJ1IjoiZ2FieXJvc21lcmkiLCJhIjoiY2tobTVkazEyMTV5dDJ5bzc2MmE4OWZtZSJ9.2jqmQl43ljmcZSP02R4Rew', { profile: 'mapbox/walking' });
    //: *********************************************************************************************************
    controlGlobal = L.Routing.control({
        createMarker: function (i, wp, nWps) {
            var popup = L.marker(wp.latLng)
                .bindPopup('Hora: ' + popupArray[i]);
            return popup;
        },
        router: mapboxRouting,
        waypoints: latlngArrayRecorrido,
        lineOptions: {
            styles: [
                { color: '#ffe05d', opacity: 1, weight: 8 }
            ],
        },
        show: false,
        draggableWaypoints: false,//to set draggable option to false
        addWaypoints: false, //disable adding new waypoints to the existing path
        fitSelectedRoutes: true,
        useZoomParameter: true,
        routeWhileDragging: true
    }).addTo(mapGlobal).on('routesfound', function (e) {
        console.log(e.routes); // e.routes have length 2
    }).on('routeselected', function (e) {
        var route = e.route;
        console.log(JSON.stringify(route.inputWaypoints, null, 2));
    });
    L.control.fullscreen({
        position: 'topleft', // change the position of the button can be topleft, topright, bottomright or bottomleft, defaut topleft
        title: 'Show me the fullscreen !', // change the title of the button, default Full Screen
        titleCancel: 'Exit fullscreen mode', // change the title of the button when fullscreen is on, default Exit Full Screen
        content: null, // change the content of the button, can be HTML, default null
        forceSeparateButton: true, // force seperate button to detach from zoom buttons, default false
        forcePseudoFullscreen: true, // force use of pseudo full screen even if full screen API is available, default false
        fullscreenElement: false // Dom element to render in full screen, false by default, fallback to map._container
    }).addTo(mapGlobal);
    mapGlobal.invalidateSize();
}
$('#modalRuta').on('shown.bs.modal', function () {
    window.setTimeout(function () {
        mapGlobal.invalidateSize();
    }, 1000);
});
//: ***************************
// ? MOSTRAR IMAGENES GRANDES
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
                    var imgR = data[0].imagen;
                    var rspI = imgR.replace(/\//g, "-");
                    var encr = CryptoJS.enc.Utf8.parse(rspI);
                    var base64 = CryptoJS.enc.Base64.stringify(encr);
                    carusel = `<a href="mostrarMiniatura/${base64}" data-fancybox="images" data-caption="Hora de captura a las ${data[0].hora_fin}" data-width="2048" data-height="1365"><img src="mostrarMiniatura/${base64}" width="350" height="300" style="padding-right:10px;padding-bottom:10px"></a>`;
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