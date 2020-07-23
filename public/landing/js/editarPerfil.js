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
        },
        error: function (data) {}
    });
}
actualizarDatos();
$('#fechaN').combodate({
    yearDescending: false,
});
$('[data-toggle="tooltip"]').tooltip();
$('#disabledDatosP :input').attr('disabled', true);
$('#disabledDatosP button[type="button"]').hide();
$('#editarDatosP').on("click", function () {
    $('#disabledDatosP :input').attr('disabled', false);
    $('#disabledDatosP button[type="button"]').show();
});
$('#disabledDatosE :input').attr('disabled', true);
$('#disabledDatosE button[type="button"]').hide();
$('#editarDatosE').on("click", function () {
    $('#disabledDatosE :input').attr('disabled', false);
    $('#disabledDatosE button[type="button"]').show();
});

function editarDatosPersonales() {
    objDatosPersonales = datosPersonales("POST");
    enviarDatosP('', objDatosPersonales);
};

function datosPersonales(method) {
    nuevoDatos = {
        id: $('#id').val(),
        nombre: $('#nombre').val(),
        fechaN: $('#fechaN').val(),
        apPaterno: $('#apPaterno').val(),
        direccion: $('#direccion').val(),
        apMaterno: $('#apMaterno').val(),
        genero: $('#genero').val(),
        '_method': method
    }
    return (nuevoDatos);
}

function enviarDatosP(accion, objDatosPersonales) {
    var idU = $('#id').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/editarUser" + accion,
        data: {
            objDatosPersonales: objDatosPersonales,
            id: idU
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
            var h5 = `${data.perso_nombre} ${data.perso_apPaterno} ${data.perso_apMaterno}`;
            actualizarDatos();
            $('#h5Nombres').empty();
            $('#h5Nombres').append(h5);
            $('#disabledDatosP :input').attr('disabled', true);
            $('#disabledDatosP button[type="button"]').hide();
            $.notify({
                message: "\nPerfil Editado\n",
                icon: 'admin/images/checked.svg'
            }, {
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function () {
            alert("error");
        }
    });
}
$('#actualizarDatosPersonales').on("click", editarDatosPersonales);
