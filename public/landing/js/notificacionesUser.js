function notification() {
    $.ajax({
        type: "GET",
        url: "/notificacionesUser",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {},
        error: function () {},
    });
}
notification();
function showNotificaciones() {
    $.ajax({
        type: "GET",
        url: "/showNotificaciones",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            console.log(data);
            $("#notificacionesUser").empty();
            var grupo = ``;
            var container = $("#notificacionesUser");
            var contador = 0;
            if (data.length == 0) {
                img = `<div class="badge float-center mt-3" style="margin-left:30%;">
                    <img src="/landing/images/bell_notification.gif" height="100">
                     <br> <label for=""
                    style="font-size:12px;color:#7d7d7d">Aún no tienes notificaciones nuevas</label> </div>`;
                container.append(img);
            } else {
                // grupo += `<div class="badge float-center mt-3" style="margin-left:30%;">
                // <img src="/landing/images/bell_notification.gif" height="100"></div>`;
                for (var i = 0; i < data.length; i++) {
                    if (data[i].read_at == null) {
                        contador++;
                        a = `<a class="dropdown-item
                notify-item border-bottom" style="background: #f1f2f3;">
                    <div class="badge float-right mt-0 mr-1">
                        <button class="btn btn-sm" style="background-color: #163552;color:#fdfdfd;" onclick="javascript:agregarCorreoNotificacion('${data[i].data[0].idEmpleado}')">
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            <img src="/landing/images/flecha (1).svg" height="20">
                        </button>
                    </div>
                    <div class="notify-icon" style="background: #163552;">
                        <img src="/landing/images/campana.svg" height="20">
                    </div>
                    <p class="notify-details mb-1 mt-0" style="font-weight:bold;color:#85a2b6"> ${data[i].data[0].empleado[0]} ${data[i].data[0].empleado[1]} ${data[i].data[0].empleado[2]}
                        <span style="font-weight:200;color:#28292f">${data[i].data[0].mensaje}</span>
                    </p>
                </a>`;
                    } else {
                        a = `<a class="dropdown-item
                notify-item border-bottom">
                    <div class="badge float-right mt-0 mr-1">
                        <button class="btn btn-sm" style="background-color: #163552;color:#ffffff;">
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            <img src="/landing/images/flecha (1).svg" height="20">
                        </button>
                    </div>
                    <div class="notify-icon" style="background: #163552;">
                        <img src="/landing/images/campana.svg" height="20">
                    </div>
                    <p class="notify-details mb-1 mt-0" style="font-weight:bold;color:#85a2b6"> ${data[i].data[0].empleado[0]} ${data[i].data[0].empleado[1]} ${data[i].data[0].empleado[2]}
                        <span style="font-weight:200;color:#28292f">${data[i].data[0].mensaje}</span>
                    </p>
                </a>`;
                    }
                    grupo += a;
                }
                container.append(grupo);
                $("#totalNotifNL").text(contador);
            }
        },
        error: function () {},
    });
}
showNotificaciones();
function agregarCorreoNotificacion(id) {
    console.log(id);
    $("#modalCorreoElectronicoHeader").modal();
    $("#idEmpleCorreoH").val(id);
}
function guardarCorreoEH() {
    console.log("ingreso");
    idEmpleado = $("#idEmpleCorreoH").val();
    descripcion = $("#textCorreoH").val();
    console.log(descripcion, $("#textCorreoH").val());
    $.ajax({
        async: false,
        type: "get",
        url: "/empleado/agregarCorreo",
        data: {
            idEmpleado: idEmpleado,
            correo: descripcion,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            console.log(data);
            $("#modalCorreoElectronicoHeader").modal("toggle");
            notification();
            showNotificaciones();
            $.notifyClose();
            $.notify(
                {
                    message: "\nCorreo electrónico registrado.",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
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
        },
        error: function () {},
    });
}
function limpiarCorreoEH() {
    $("#textCorreoH").val("");
}
