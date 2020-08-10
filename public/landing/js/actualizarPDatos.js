function actualizarDatos() {
    $.ajax({
        async: false,
        url: "/perfilMostrar",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            if (data.foto != null) {
                $('#preview').attr("src", "/fotosUser/" + data.foto);
                $('#imgsm').attr("src", "/fotosUser/" + data.foto);
                $('#imgxs').attr("src", "/fotosUser/" + data.foto);
                $('#imgxs2').attr("src", "/fotosUser/" + data.foto);
            }
        },
        error: function (data) {}
    });
}
actualizarDatos();
