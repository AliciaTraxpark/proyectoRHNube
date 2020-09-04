function enviarInstrucciones() {
    var email = $('#email').val();
    $.ajax({
        type: "post",
        url: "/password/email",
        data: {
            email: email
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data, textStatus, xhr) {
            console.log(data);
            if (data == 1) {
                $('#alert').show();
            }
            if (data == false) {
                $('#alertCorreo').show();
                $('#ocultar').hide();
                $('#alert').hide();
                window.setTimeout(function () {
                    // Move to a new location or you can do something else
                    window.location.replace(
                        location.origin + "/"
                    );

                }, 10000);
            }
        }
    });
}

$('#email').keyup(function () {
    $('#alert').hide();
});
