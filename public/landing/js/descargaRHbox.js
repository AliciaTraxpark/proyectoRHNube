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
        },
        success: function (data) {
            $('#alertSuccess').show();
            $("#media").css('pointer-events', 'auto');
            $('#licencia').prop("disabled", true);
        }
    });
}

$('#enviarLicencia').click(function () {
    enviarLicencia();
});