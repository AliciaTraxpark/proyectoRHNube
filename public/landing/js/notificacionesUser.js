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
                <div class="notify-icon bg-primary">
                    <i data-feather="bell"></i>
                </div>
                <p class="notify-details">${data[i].data[0].mensaje} </p>
            </a>`;
            grupo += a;
        }
        console.log(grupo);
        container.append(grupo);
    },
    error: function () {

    }
});