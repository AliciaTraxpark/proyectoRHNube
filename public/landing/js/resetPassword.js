function enviarReset() {
    var email = $('#email').val();
    var password = $('#password').val();
    var password_confirmation = $('#password-confirm').val();
    var token = $('#token').val();
    $.ajax({
        type: "post",
        url: "/password/reset",
        data: {
            email: email,
            password: password,
            password_confirmation: password_confirmation,
            token: token
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data, textStatus, xhr) {
            console.log(data);
        },
        error: function (error) {
            $('#alertPaswword').empty();
            //valido que llegue errors
            if (error.responseJSON.hasOwnProperty('errors')) {
                //valido que tenga el error nombre
                console.log(error.responseJSON.errors.password);
                if (error.responseJSON.errors.password) {
                    mensaje = `<strong><img src="/landing/images/alert.svg" height="25"
                                class="mr-1 mt-1"></strong> Confirmar contraseña y contraseña no coinciden.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>`;
                    $('#alertPaswword').append(mensaje);
                    $('#alertPaswword').show();
                    $('#password').addClass("error");
                    $('#password-confirm').addClass("error");
                    $('#password').keyup(function () {
                        $('#password').removeClass("error");
                        $('#password-confirm').removeClass("error");
                    });
                    $('#password-confirm').keyup(function () {
                        $('#password').removeClass("error");
                        $('#password-confirm').removeClass("error");
                    });
                }
            }
        }
    });
}
