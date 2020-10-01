$("#enviarLicencia").prop("disabled", true);
$("#enlace").css('pointer-events', 'none');
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
            var code = data.descarga;
            document.getElementById('enlace').setAttribute('href', location.origin + '/download/' + code);
            // document.getElementById('enlace1').setAttribute('href', location.origin + '/download/' + code);
            $('#alertSuccess').show();
            $("#enlace").css('pointer-events', 'auto');
            $('#licencia').prop("disabled", true);
        },
        error: function (error) {
            console.log(error.responseJSON);
            $('#alertError').empty();
            if (error.responseJSON === "lic_no_disponible") {
                mensaje = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                    <span style="font-size: 14px;">Licencia activa.Si desea desargar denuevo el RH box, solicitar a su administrador reenvio de correo.</span>`;
                $('#alertError').append(mensaje);
                $('#alertError').show();
            }
            if (error.responseJSON === "lic_incorrecta") {
                mensaje = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                    <span style="font-size: 14px;">Licencia incorrecta.</span>`;
                $('#alertError').append(mensaje);
                $('#alertError').show();
            }
            if (error.responseJSON === "lic_inactiva") {
                mensaje = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                <span style="font-size: 14px;">Licencia inactiva.Si desea desargar denuevo el RH box, solicitar a su administrador reenvio de correo.</span>`;
                $('#alertError').append(mensaje);
                $('#alertError').show();
            }
        }
    });
}

$('#enviarLicencia').click(function () {
    enviarLicencia();
});