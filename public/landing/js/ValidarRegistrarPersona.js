function agregarempleado() {

    ///validar fecha
    var Anio = parseInt($('#ano_fecha').val()); // Extraemos en año
    var Mes = parseInt($('#mes_fecha').val() - 1); // Extraemos el mes
    var Dia = parseInt($('#dia_fecha').val()); // Extraemos el día

    // Con la función Date() de javascript evaluamos si la fecha existe
    var VFecha = new Date(Anio, Mes, Dia);

    // Si las partes de la fecha concuerdan con las que digitamos, es correcta
    if ((VFecha.getFullYear() == Anio) && (VFecha.getMonth() == Mes) && (VFecha.getDate() == Dia)) {
        Mensaje = 'Fecha correcta' + Anio + Mes + Dia;
        $('#Mensaje').hide();
    } else {
        Mensaje = 'Fecha incorrecta';

        document.getElementById('Mensaje').innerHTML = Mensaje;
        return false;

    }


    //NOTIFICACION
    var notify = $.notifyDefaults({
        icon_type: 'image',
        newest_on_top: true,
        delay: 4000,
        template: '<div data-notify="container" class="col-xs-6 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
            '<span data-notify="title">{1}</span> ' +
            '<span style="color:#8a6d3b" data-notify="message">{2}</span>' +
            '</div>',
        placement: {
            from: "top",
            align: "center"
        },
        animationType:"drop"
    });

    //validar usuario
    var email = $('#email').val();
    $.ajax({
        type: "get",
        url: "/persona/comprobar",
        data: {
            email: email
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            if (data == 1) {

                $.notify({
                    message: "Usuario ya registrado.",
                    icon: '../landing/images/warning.svg'
                });
                return false;

            } else {
                $.notifyClose();
                var dia = $('#dia_fecha').val();
                var mes = parseInt($('#mes_fecha').val());
                switch (mes) {
                    case 1:
                        $('#mesN').val('Enero');
                        break;
                    case 2:
                        $('#mesN').val('Febrero');
                        break;
                    case 3:
                        $('#mesN').val('Marzo');
                        break;
                    case 4:
                        $('#mesN').val('Abril');
                        break;
                    case 5:
                        $('#mesN').val('Mayo');
                        break;
                    case 6:
                        $('#mesN').val('Junio');
                        break;
                    case 7:
                        $('#mesN').val('Julio');
                        break;
                    case 8:
                        $('#mesN').val('Agosto');
                        break;
                    case 9:
                        $('#mesN').val('Setiembre');
                        break;
                    case 10:
                        $('#mesN').val('Octubre');
                        break;
                    case 11:
                        $('#mesN').val('Noviembre');
                        break;
                    case 12:
                        $('#mesN').val('Diciembre');

                }
                var ano = $('#ano_fecha').val();
                $('#diaN').val(dia);

                $('#anoN').val(ano);
                $('#myModal').modal('toggle');
            }
        },

    });



    //
}

function registerP() {
    var nombres = $('#nombres').val();
    var apPaterno = $('#apPaterno').val();
    var apMaterno = $('#apMaterno').val();
    var direccion = $('#direccion').val();
    var email = $('#email').val();
    var password = $('#password').val();
    var dia_fecha = $('#dia_fecha').val();
    var mes_fecha = $('#mes_fecha').val();
    var ano_fecha = $('#ano_fecha').val();
    var sexo = $('input:radio[name=sexo]:checked').val();

    $.ajax({
        type: "POST",
        url: "/persona/create",
        data: {
            nombres,
            apPaterno,
            apMaterno,
            direccion,
            email,
            password,
            dia_fecha,
            mes_fecha,
            ano_fecha,
            sexo
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (user) {
            window.location.replace(
                location.origin + "/registro/organizacion/" + user
            );
        }

    });

}
