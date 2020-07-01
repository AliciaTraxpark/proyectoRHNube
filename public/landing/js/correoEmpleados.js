function enviar() {
    var idEmpleado = $('#v_id').val();
    //NOTIFICACION
    $.notifyDefaults({
        icon_type: 'image',
        newest_on_top: true,
        delay: 5000,
        template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
            '<span data-notify="title">{1}</span> ' +
            '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
            '</div>',
        spacing: 35
    });
    $.ajax({
        async: false,
        type: "get",
        url: "empleadoCorreo",
        data: {
            idEmpleado: idEmpleado
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            $.notify({
                message: "\nCorreo enviado.",
                icon: 'admin/images/warning.svg'
            });
            $('#modalCorreo').modal('toggle');
        },
        error: function () {
            $('#modalCorreo').modal('toggle');
            $.notify({
                message: "\nAún no ha registrado correo a empleado.",
                icon: 'admin/images/warning.svg'
            });
        }
    });
}
$('#enviarCorreo').on("click", enviar);
