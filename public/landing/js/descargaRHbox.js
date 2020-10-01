$("#enviarLicencia").prop("disabled", true);
$("#media").css('pointer-events', 'none');
$("#licencia").keyup(function () {
    if ($('#licencia').val() != "") {
        $("#enviarLicencia").prop("disabled", false);
    } else {
        $("#enviarLicencia").prop("disabled", true);
    }
});

function enviarLicencia() {
    var licencia = $('#licencia').val();
    $.ajax({
        url: "/verificarLicencia",
        method: "GET",
        data: {
            licencia: licencia
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            $("#enviarLicencia").prop("disabled", true);
            $('#alertError').hide();
        },
        success: function (data) {
            $('#alertSuccess').show();
            $("#media").css('pointer-events', 'auto');
            $('#licencia').prop("disabled", true);
        },
        error: function (error) {
            console.log(error.responseJSON);
            $('#alertError').empty();
            if (error.responseJSON === "lic_no_disponible") {
                mensaje = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                    <span style="font-size: 14px;">Licencia se encuentra activa.Si desea desargar denuevo el RH box, solicitar a su administrador reenvio de correo.</span>`;
                $('#alertError').append(mensaje);
                $('#alertError').show();
            }
            if (error.responseJSON === "lic_incorrecta") {
                mensaje = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                    <span style="font-size: 14px;">Licencia incorrecta.</span>`;
                $('#alertError').append(mensaje);
                $('#alertError').show();
            }
        }
    });
}

$('#enviarLicencia').click(function () {
    enviarLicencia();
});