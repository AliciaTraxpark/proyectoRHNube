function actualizarDatos() {
    $.ajax({
        async: false,
        url: "/perfilMostrar",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            $('#id').val(data.id);
            $('#nombre').val(data.perso_nombre);
            $('#fechaN').val(data.perso_fechaNacimiento);
            $('#apPaterno').val(data.perso_apPaterno);
            $('#direccion').val(data.perso_direccion);
            $('#apMaterno').val(data.perso_apMaterno);
            $('#genero').val(data.perso_sexo);
            $('#idE').val(data.id);
            $('#ruc').val(data.organi_ruc);
            $('#razonS').val(data.organi_razonSocial);
            $('#direccionE').val(data.organi_direccion);
            $('#numE').val(data.organi_nempleados);
            $('#pagWeb').val(data.organi_pagWeb);
            $('#organizacion').val(data.organi_tipo);
            if (data.foto != null) {
                $('#preview').attr("src", "/fotosUser/" + data.foto);
                $('#imgsm').attr("src", "/fotosUser/" + data.foto);
                $('#imgxs').attr("src", "/fotosUser/" + data.foto);
                $('#imgxs2').attr("src", "/fotosUser/" + data.foto);
            }
            $('#depE').val(data.organi_departamento);
            onSelectDepartamentoOrgani('#depE').then(function () {
                $('#provE').val(data.organi_provincia);
                onSelectProvinciaOrgani('#provE').then((result) => $('#distE')
                    .val(data.organi_distrito))
            });
        },
        error: function (data) {}
    });
}
actualizarDatos();
