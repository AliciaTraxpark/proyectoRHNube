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
            console.log(data);
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
        onMostrarUbicaciones();
    } else {
        $.notifyClose();
        $.notify({
            message: "Elegir empleado.",
            icon: "admin/images/warning.svg",
        });
    }
}
var datos = {};
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
                console.log(data);
                datos = data;
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

function ubicacionesMapa(hora) {

    var map = L.map('mapid' + hora, {
        center: new L.LatLng(-12.0431800, -77.0282400),
        zoom: 15,
        minZoom: 10,
    });
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png').addTo(map);
    var respuesta = [];
    var arrayDatos = [];
    var val = {};
    // ? RECORRED DATOS PARA POPUP
    for (let index = 0; index < datos.length; index++) {
        for (var j = 0; j < 6; j++) {
            if (datos[index].minutos[j] != undefined) {
                const ub = datos[index].minutos[j];
                for (var i = 0; i < ub.length; i++) {
                    const valor = ub[i].ubicaciones;
                    valor.forEach(element => {
                        // ? DIBUJAR MAPA DEL USUARIO SEGUN SUS POSICIONES
                        arrayDatos.push(element.latitud_ini + "," + element.longitud_ini + "," + ub[i].hora_ini, element.latitud_fin + "," + element.longitud_fin + "," + ub[i].hora_fin);
                    });
                }
            }
        }
    }
    respuesta.push(arrayDatos);
    console.log(respuesta);
    var latlngArray = [];
    var popupArray = [];
    // ? DIBUJAR MAPA DEL USUARIO SEGUN SUS POSICIONES
    for (let index = 0; index < respuesta[0].length; index++) {
        var element = respuesta[0][index];
        var ltln = L.latLng(element.split(",")[0], element.split(",")[1]);
        latlngArray.push(ltln);
    }
    var control = L.Routing.control({
        createMarker: function (i, wp, nWps) {
            var popup = L.marker(wp.latLng)
                .bindPopup('Hora: ' + respuesta[0][i].split(",")[2]);
            popupArray.push(popup);
            return popup;
        },
        waypoints: latlngArray,
        lineOptions: {
            styles: [
                { color: '#892cdc', opacity: 0.8, weight: 4 }
            ],
        },
        routeWhileDragging: false,
        show: false,
        draggableWaypoints: false,//to set draggable option to false
        addWaypoints: false //disable adding new waypoints to the existing path
    }).addTo(map);
    // leafletImage(map, function (err, canvas) {
    //     // now you have canvas
    //     // example thing to do with that canvas:
    //     var img = document.createElement('img');
    //     var dimensions = map.getSize();
    //     img.width = dimensions.x / 5;
    //     img.height = dimensions.y / 5;
    //     img.src = canvas.toDataURL();
    //     document.getElementById('images').innerHTML = '';
    //     document.getElementById('images').appendChild(img);
    // });
}