function enviar() {
    var idEmpleado = $('#empleadoAndroid').val();
    var idVinculacion = $('#vinculaciónAndroid').val();
    //NOTIFICACION
    $.ajax({
        async: false,
        type: "get",
        url: "correoAndroid",
        data: {
            idEmpleado: idEmpleado,
            idVinculacion: idVinculacion
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
            $('#modalCorreo').modal('toggle');
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
            $('#modalCorreo').modal('toggle');
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
$('#enviarCorreo').on("click", enviar);

function agregarEscritorio() {
    var idEmpleado = $('#v_id').val();
    //NOTIFICACION
    $.ajax({
        async: false,
        type: "get",
        url: "asignarEscritorio",
        data: {
            idEmpleado: idEmpleado
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
            $('#modalNuevoE').modal('toggle');
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
        error: function (data) {
            console.log(data);
            if (data.responseJSON[0].correo == false) {
                $('#modalNuevoE').modal('toggle');
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
            if (data.responseJSON[0].limite == false) {
                $('#modalNuevoE').modal('toggle');
                $.notify({
                    message: "\nLlego al limite de agregar PC.",
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
        }
    });
}
$('#agregarEscritorio').on("click", agregarEscritorio);
