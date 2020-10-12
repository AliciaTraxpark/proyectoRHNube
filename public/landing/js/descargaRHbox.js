$("#enviarLicencia").prop("disabled", true);
$("#enlace32").css('pointer-events', 'none');
$("#enlace64").css('pointer-events', 'none');
$("#licencia").keyup(function () {
    if ($('#licencia').val() != "") {
        $("#enviarLicencia").prop("disabled", false);
    } else {
        $("#enviarLicencia").prop("disabled", true);
    }
});

function enviarLicencia() {
    console.log($('#licencia').val());
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
            $('#btnDownload').hide();
        },
        success: function (data) {
            $('#btnDownload').show();
            var code = data.descarga;
            document.getElementById('enlace64').setAttribute('href', location.origin + '/download/' + code);
            document.getElementById('enlace32').setAttribute('href', location.origin + '/downloadx32/' + code);
            // document.getElementById('enlace1').setAttribute('href', location.origin + '/download/' + code);
            $('#alertSuccess').show();
            $("#enlace64").css('pointer-events', 'auto');
            $("#enlace32").css('pointer-events', 'auto');
            $('#licencia').prop("disabled", true);
        },
        error: function (error) {
            console.log(error.responseJSON);
            $('#alertError').empty();
            if (error.responseJSON === "lic_no_disponible") {
                mensaje = `<strong><img src="/landing/images/alert1.svg" height="20" class="mr-1 mt-0"></strong> 
                    <span style="font-size: 14px;">La licencia esta en uso, comunicate con tu administrador para generar una nueva licencia.</span>`;
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
                <span style="font-size: 14px;">La licencia esta inactiva, comunicate con tu administrador para generar una nueva licencia..</span>`;
                $('#alertError').append(mensaje);
                $('#alertError').show();
            }
        }
    });
}

$('#enviarLicencia').click(function () {
    enviarLicencia();
});