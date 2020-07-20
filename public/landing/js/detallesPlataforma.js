function getlink() {
    var link = "https://play.google.com/store/apps/details?id=pe.trax.park&hl=es";
    var aux = document.createElement("div");
    aux.setAttribute("contentEditable", true);
    aux.innerHTML = link;
    aux.setAttribute("onfocus", "document.execCommand('selectAll',false,null)");
    document.body.appendChild(aux);
    aux.focus();
    document.execCommand("copy");
    document.body.removeChild(aux);
    $.notify({
        message: 'Enlace copiado',
        icon: 'admin/images/checked.svg'
    }, {
        element: $('#notifEnlace'),
        position: 'fixed',
        icon_type: 'image',
        allow_dismiss: true,
        newest_on_top: true,
        delay: 2000,
        template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
            '<span data-notify="title">{1}</span> ' +
            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
            '</div>',
        spacing: 35
    });
}
$('.detalleW').on('click', function () {
    $('#detallesWindows').modal();
});
$('.detalleA').on('click', function () {
    $('#detallesAndroid').modal();
});

function cambiarEstadoLicencia() {
    var idEmpleado = $('#idDetalle').val();
    var idLicencia = $("#idLicenciaND" + idEmpleado).val();
    //NOTIFICACION
    $.ajax({
        async: false,
        type: "get",
        url: "/cambiarEstadoLicencia",
        data: {
            idE: idEmpleado,
            idL: idLicencia
        },
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
            leertabla();
            $('#estadoLicenciaC').modal('toggle');
            $('#detallesWindows').modal('toggle');
            $.notify({
                message: "\nCorreo enviado.",
                icon: 'admin/images/checked.svg'
            }, {
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function () {
            $('#estadoLicenciaC').modal('toggle');
            $('#detallesWindows').modal('toggle');
            $.notify({
                message: "\nAún no ha registrado correo a empleado.",
                icon: 'admin/images/warning.svg'
            }, {
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
        }
    });
}
$('#CambiarEstadoL').on("click", cambiarEstadoLicencia);
