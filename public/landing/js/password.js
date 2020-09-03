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
            if(data == 1){
                $('#alert').show();
            }
        }
    });
}
