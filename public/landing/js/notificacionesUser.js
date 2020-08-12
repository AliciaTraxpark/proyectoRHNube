$.ajax({
    type: "GET",
    url: "/notificacionesUser",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
    },
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
        console.log(data);
        $('#notificacionesUser').empty();
        var grupo = ``;
        var container = $('#notificacionesUser');
        for (var i = 0; i < data.length; i++) {
            console.log(data[i].data[0].mensaje);
            a = `<a href="javascript:void(0);" class="dropdown-item
                notify-item border-bottom">
                    <div class="notify-icon" style="background: #a6b1e1;">
                        <img src="landing/images/reloj.svg" height="20">
                    </div>
                    <p class="text-muted mb-2 mt-2">
                        <span>${data[i].data[0].mensaje}</span>
                    </p>
                </a>`;
            grupo += a;
        }
        console.log(grupo);
        container.append(grupo);
    },
    error: function () {

    }
});