$.ajax({
    type: "GET",
    url: "/respuestaC",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    statusCode: {
        /*401: function () {
            location.reload();
        },*/
        419: function () {
            location.reload();
        }
    },
    success: function (data) {
        if (data == false) {
            $('#menu-bar').css('pointer-events', 'none');
            $('#menuD').css('pointer-events', 'auto');
        }else{
            $('#menu-bar').css('pointer-events', 'auto');
        }
    },
});
