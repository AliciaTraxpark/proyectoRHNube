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
                $('#preview').attr("src", "fotosUser/" + data.foto);
                $('#imgsm').attr("src", "fotosUser/" + data.foto);
                $('#imgxs').attr("src", "fotosUser/" + data.foto);
            }
        },
        error: function (data) {}
    });
}
actualizarDatos();
$('#fechaN').combodate({
    yearDescending: false,
});
$('[data-toggle="tooltip"]').tooltip();
$('#guardarFoto').hide();
$('#disabledDatosP :input').attr('disabled', true);
$('#disabledDatosP button[type="button"]').hide();
$('#editarDatosP').on("click", function () {
    $('#disabledDatosP :input').attr('disabled', false);
    $('#disabledDatosP button[type="button"]').show();
});
$('#nombre').change(function () {
    if ($('#nombre').val() == '') {
        $('#actualizarDatosPersonales').attr('disabled', true);
    } else {
        $('#actualizarDatosPersonales').attr('disabled', false);
    }
});
$('#fechaN').change(function () {
    if ($('#fechaN').val() == '') {
        $('#actualizarDatosPersonales').attr('disabled', true);
    } else {
        $('#actualizarDatosPersonales').attr('disabled', false);
    }
});
$('#apPaterno').change(function () {
    if ($('#apPaterno').val() == '') {
        $('#actualizarDatosPersonales').attr('disabled', true);
    } else {
        $('#actualizarDatosPersonales').attr('disabled', false);
    }
});
$('#direccion').change(function () {
    if ($('#direccion').val() == '') {
        $('#actualizarDatosPersonales').attr('disabled', true);
    } else {
        $('#actualizarDatosPersonales').attr('disabled', false);
    }
});
$('#apMaterno').change(function () {
    if ($('#apMaterno').val() == '') {
        $('#actualizarDatosPersonales').attr('disabled', true);
    } else {
        $('#actualizarDatosPersonales').attr('disabled', false);
    }
});
$('#disabledDatosE :input').attr('disabled', true);
$('#disabledDatosE button[type="button"]').hide();
$('#editarDatosE').on("click", function () {
    $('#disabledDatosE :input').attr('disabled', false);
    $('#ruc').attr('disabled', true);
    $('#disabledDatosE button[type="button"]').show();
});
$('#razonS').change(function () {
    if ($('#razonS').val() == '') {
        $('#actualizarDatosEmpresa').attr('disabled', true);
    } else {
        $('#actualizarDatosEmpresa').attr('disabled', false);
    }
});
$('#numE').change(function () {
    if ($('#numE').val() == '') {
        $('#actualizarDatosEmpresa').attr('disabled', true);
    } else {
        $('#actualizarDatosEmpresa').attr('disabled', false);
    }
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
            $('#h6Nombres').empty();
            $('#h5Nombres').append(h5);
            $('#h6Nombres').append(h5);
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

function editarDatosEmpresa() {
    objDatosEmpresa = datosEmpresa("POST");
    enviarDatosE('', objDatosEmpresa);
};

function datosEmpresa(method) {
    nuevoDatos = {
        id: $('#id').val(),
        razonSocial: $('#razonS').val(),
        direccion: $('#direccionE').val(),
        nempleados: $('#numE').val(),
        pagWeb: $('#pagWeb').val(),
        tipo: $('#organizacion').val(),
        '_method': method
    }
    return (nuevoDatos);
}

function enviarDatosE(accion, objDatosEmpresa) {
    var idU = $('#id').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/editarEmpresa" + accion,
        data: {
            objDatosEmpresa: objDatosEmpresa,
            id: idU
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
            var h6 = `${data.organi_razonSocial}`;
            actualizarDatos();
            $('#h6Empresa').empty();
            $('#strongOrganizacion').empty();
            $('#h6Empresa').append(h6);
            $('#strongOrganizacion').append(h6);
            $('#disabledDatosE :input').attr('disabled', true);
            $('#disabledDatosE button[type="button"]').hide();
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
$('#actualizarDatosEmpresa').on("click", editarDatosEmpresa);
$(document).on("click", ".browse", function () {
    var file = $(this).parents().find(".file");
    file.trigger("click");
});
$('input[type="file"]').change(function (e) {
    var fileName = e.target.files[0].name;
    $('#nameFoto').val(fileName);
    var reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById("preview").src = e.target.result;
    };
    reader.readAsDataURL(this.files[0]);
    $('#guardarFoto').show();
});
$('#guardarFoto').on("click", function () {
    console.log($('.file').prop('files')[0]);
    var formData = new FormData();
    formData.append('file', $('.file').prop('files')[0]);
    console.log(formData);
    $.ajax({
        contentType: false,
        processData: false,
        type: "POST",
        url: "/foto",
        data: formData,
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
            $('#preview').attr("src", "fotosUser/" + data[0].foto);
            $('#imgsm').attr("src", "fotosUser/" + data[0].foto);
            $('#imgxs').attr("src", "fotosUser/" + data[0].foto);
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
});
