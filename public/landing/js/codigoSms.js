$('#errorC').hide();
$('#error').hide();
$('#ComprobarC').click(function () {
    var codigo = $('#codigoV').val();
    $.ajax({
        type: "GET",
        url: "/comprobarCodigo",
        data: {
            codigo: codigo
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            if (data[0].codigo == true && data[0].user) {
                window.location.replace(
                    location.origin + "/dashboard"
                );
            }
            if (data[0].codigo == false) {
                $('#errorC').show();
                $('#error').hide();
            }
            if (data[0].user == false) {
                $('#error').show();
                $('#errorC').hide();
            }
        },
        error: function (data) {}
    });
});
