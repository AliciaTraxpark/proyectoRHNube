$.ajax({
    type: "GET",
    url: "/notificacionesUser",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
        
    },
    error:function(){

    }
});