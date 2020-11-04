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
var datos;
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
                        var grupo = `<div class="row p-3"><div class="row col-12 pt-2"><span>${labelDelGrupo}</span></div><div class="row col-12 pt-2">`;
                        for (var j = 0; j < 6; j++) {
                            if (data[index].minutos[j] != undefined) {
                                card = `<div id="mapid${hora + "," + j}" onchange="javascript:ubicacionesMapa('${hora + "," + j}')" class="mapid">
                                        </div>`;
                                grupo += card;
                            }
                        }
                        grupo += `</div></div>`;
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

function ubicacionesMapa(horayJ) {
    // var onlyHora = horayJ.split(",")[0];
    // var j = horayJ.split(",")[1];
    // ubicaciones = [];
    // datos.forEach((hora) => {
    //     if (hora.horaUbicacion == onlyHora) {
    //         ubicaciones = hora.minutos[j];
    //     }
    // });
    // for (let index = 0; index < ubicaciones.length; index++) {
    //     const element = ubicaciones[index].ubicaciones;
    //     element.forEach(point => {
    //         console.log(point);
    //     });
    // }

    var map = L.map('mapid' + horayJ).setView([51.505, -0.09]);
}