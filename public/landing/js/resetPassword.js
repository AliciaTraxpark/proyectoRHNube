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
            $('#alertSuccess').show();
            window.setTimeout(function () {
                // Move to a new location or you can do something else
                window.location.replace(
                    location.origin + "/"
                );

            }, 10000);
            $('#ocultarbtn').hide();
        },
        error: function (error) {
            $('#alertPaswword').empty();
            //valido que llegue errors
            if (error.responseJSON.hasOwnProperty('errors')) {
                //valido que tenga el error nombre
                if (error.responseJSON.errors.password) {
                    mensaje = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                    <span style="font-size: 14px;">Contraseña y  confimar contraseña no coinciden.</span>`;
                    $('#alertPaswword').append(mensaje);
                    $('#alertPaswword').show();
                    $('#password').addClass("error");
                    $('#password-confirm').addClass("error");
                }
            }
        }
    });
}
$('#password').keyup(function () {
    $('#alertPaswword').hide();
    $('#password').removeClass("error");
    $('#password-confirm').removeClass("error");
    $('#password-confirm').val("");
});
$('#password-confirm').keyup(function () {
    $('#alertPaswword').hide();
    $('#password').removeClass("error");
    $('#password-confirm').removeClass("error");
});
