//NOTIFICACION
$.notifyDefaults({
    icon_type: 'image',
    delay: 10000,
    timer: 8000,
    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
        '<span data-notify="title">{1}</span> ' +
        '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
        '</div>',
    spacing: 35
});

//EVENTOS_USUARIOS
$.ajax({
    type: "GET",
    url: "/eventosU",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    statusCode: {
        /*401: function () {
            location.reload();
        },*/
        419: function () {
            location.reload();
        }
    },
    success: function (data) {
        if (data == false) {
            $.notify({
                message: "\n\nAún no has personalizado tu calendario. <br><a id=\"calendario\" target=\"_blank\" style=\"cursor: pointer;\"><button class=\"boton btn btn-default mr-1 spinner-grow spinner-grow-sm\"></button></a>",
                icon: 'admin/images/warning.svg',
            }, {
                mouse_over: "pause"
            });
        }
        $('#calendario').click(function () {
            window.location.replace(
                location.origin + "/calendario"
            );
        });
    },
});
