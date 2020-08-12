$.ajax({
    type: "GET",
    url: "/notificacionesUser",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {},
    error: function () {

    }
});

$.ajax({
    type: "GET",
    url: "/showNotificaciones",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
        console.log(data["notificaciones"]);
        $('#notificacionesUser').empty();
        var grupo = ``;
        var container = $('#notificacionesUser');
        var url;
        var contador = 0;
        for (var i = 0; i < data["notificaciones"].length; i++) {
            console.log(data["notificaciones"][i].data[0].id);
            if (data["user"]["user_estado"] == 0) {
                if (data["notificaciones"][i].data[0].id == 1) {
                    url = "calendario";
                }
                if (data["notificaciones"][i].data[0].id == 2) {
                    url = "empleado";
                }
                if (data["notificaciones"][i].data[0].id == 3) {
                    url = "horario";
                }
            } else {
                if (data["notificaciones"][i].data[0].id == 1) {
                    url = "calendarios";
                }
                if (data["notificaciones"][i].data[0].id == 2) {
                    url = "empleados";
                }
                if (data["notificaciones"][i].data[0].id == 3) {
                    url = "horarios";
                }
            }
            if (data["notificaciones"][i].read_at == null) {
                contador++;
                a = `<li class="dropdown-item
                notify-item border-bottom" style="background: #f8f8f8;">
                    <div class="badge float-right mt-0 mr-1">
                        <a class="btn btn-sm" style="background-color: #163552;" onclick="javascript:pagina('${url}')">
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            <img src="landing/images/flecha (1).svg" height="20">
                        </a>
                    </div>
                    <div class="notify-icon" style="background: #a6b1e1;">
                        <img src="landing/images/reloj.svg" height="20">
                    </div>
                    <p class="notify-details mb-1 mt-0"> ${data["user"]["nombre"]} ${data["user"]["apPaterno"]} ${data["user"]["apMaterno"]}
                        <span>${data["notificaciones"][i].data[0].mensaje}</span>
                    </p>
                </li>`;
            } else {
                a = `<li class="dropdown-item
                notify-item border-bottom">
                    <div class="badge float-right mt-0 mr-1">
                        <a class="btn btn-sm" style="background-color: #163552;" onclick="javascript:pagina('${url}')">
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            <img src="landing/images/flecha (1).svg" height="20">
                        </a>
                    </div>
                    <div class="notify-icon" style="background: #a6b1e1;">
                        <img src="landing/images/reloj.svg" height="20">
                    </div>
                    <p class="notify-details mb-1 mt-0"> ${data["user"]["nombre"]} ${data["user"]["apPaterno"]} ${data["user"]["apMaterno"]}
                        <span>${data["notificaciones"][i].data[0].mensaje}</span>
                    </p>
                </li>`;
            }
            grupo += a;
        }
        container.append(grupo);
        $('#totalNotifNL').text(contador);
    },
    error: function () {

    }
});

function pagina(url) {
    console.log(url);
    window.location.replace(
        location.origin + "/" + url
    );
}
