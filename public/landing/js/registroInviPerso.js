function confirmarEmail(){
    $('#spanInc').hide();
    var emailC= $('#email2').val();
    var claveC=$('#claveCon').val();
    var idinvita=$('#idInvit').val();
    $.ajax({
        type: "post",
        url: "/validaremailCInvita",
        data: {
            email:emailC,clave:claveC
        },
        statusCode: {
            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {

            if(data[0]==1){
                $.ajax({
                    type: "POST",
                    url: "/registrarEmailBD",
                    data: {
                        idinvitado:idinvita,
                        iduser:data[1],

                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {

                            window.location.replace(
                                location.origin
                            );

                    }

                });

            }
            else{
                $('#spanInc').show();

            }


        },
        error: function () {
            alert("Hay un error");
        }
    });

}

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
        animationType: "drop"
    });
    //
    var regex = RegExp("^9{1}[0-9]{8,8}");
    var regex2 = RegExp("^[a-z0-9]+([-._][a-z0-9]+)*@([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,4}$");
    if (regex.test($('#email').val()) || regex2.test($('#email').val())) {
        console.log(regex.test($('#email').val()));
        console.log(regex2.test($('#email').val()));
        $('#validCelCorreo').hide();
    } else {
        console.log(regex.test($('#email').val()));
        console.log(regex2.test($('#email').val()));
        $('#validCelCorreo').show();
        return false;
    }
    //validar usuario
    var emailCelular = $('#email').val().split("@");
    var email;
    if (emailCelular.length == 2) {
        email = $('#email').val();
    } else {
        email = "51" + $('#email').val();
    }
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
            /* if (data == 1) {

                $.notify({
                    message: "Usuario ya registrado.",
                    icon: '../landing/images/warning.svg'
                });
                return false;

            } else { */
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
           /*  } */
        },

    });



    //
}

function personalizadoGenero() {

    $('#generoModal').modal("toggle");
}

function registerP() {
    $('#confirmar').prop( "disabled",true );
    var emailCelular = $('#email').val().split("@");
    var emailC;
    if (emailCelular.length == 2) {
        emailC = $('#email').val();
    } else {
        emailC = "51" + $('#email').val();
    }
    var sexoP;
    if ($('#textSexo').val() != '') {
        sexoP = $('#textSexo').val();
    } else {
        sexoP = $('input:radio[name=sexo]:checked').val();
    }
    var nombres = $('#nombres').val();
    var apPaterno = $('#apPaterno').val();
    var apMaterno = $('#apMaterno').val();
    var direccion = $('#direccion').val();
    var email = emailC;
    var password = $('#password').val();
    var dia_fecha = $('#dia_fecha').val();
    var mes_fecha = $('#mes_fecha').val();
    var ano_fecha = $('#ano_fecha').val();
    var sexo = sexoP;
    var idinvita=$('#idInvit').val();
    $.ajax({
        type: "POST",
        url: "/registroinvitadoBD",
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
            sexo,idinvita
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
                window.location.replace(
                    location.origin
                );
                $('#modalInv').modal('show');
        }

    });

}
