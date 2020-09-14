actualizarDatos();

function Datos() {
    $.ajax({
        async: false,
        url: "/perfilMostrar",
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#id").val(data.id);
            $("#nombre").val(data.perso_nombre);
            $("#fechaNacimiento").val(data.perso_fechaNacimiento);
            $("#apPaternoP").val(data.perso_apPaterno);
            $("#direccion").val(data.perso_direccion);
            $("#apMaternoP").val(data.perso_apMaterno);
            if (data.perso_sexo != "Mujer" && data.perso_sexo != "Hombre" && data.perso_sexo != "Personalizado") {
                $('#genero').append($('<option>', {
                    value: data.perso_sexo,
                    text: data.perso_sexo,
                    selected: true
                }));
                valueGenero = data.perso_sexo;
            }
            $("#genero").val(data.perso_sexo);
            $("#idE").val(data.id);
            $("#ruc").val(data.organi_ruc);
            $("#razonS").val(data.organi_razonSocial);
            $("#direccionE").val(data.organi_direccion);
            $("#numE").val(data.organi_nempleados);
            $("#pagWeb").val(data.organi_pagWeb);
            if (data.organi_tipo != "Empresa" && data.organi_tipo != "Gobierno" && data.organi_tipo != "ONG" && data.organi_tipo != "Asociación" && data.organi_tipo != "Otros") {
                $('#organizacion').append($('<option>', {
                    value: data.organi_tipo,
                    text: data.organi_tipo,
                    selected: true
                }));
                valueGenero = data.organi_tipo;
            } 
            $("#organizacion").val(data.organi_tipo);
            if (data.foto != null) {
                $("#preview").attr("src", "/fotosUser/" + data.foto);
                $("#imgsm").attr("src", "/fotosUser/" + data.foto);
                $("#imgxs").attr("src", "/fotosUser/" + data.foto);
                $("#imgxs2").attr("src", "/fotosUser/" + data.foto);
            }
            $("#depE").val(data.organi_departamento);
            onSelectDepartamentoOrgani("#depE").then(function () {
                $("#provE").val(data.organi_provincia);
                onSelectProvinciaOrgani("#provE").then((result) =>
                    $("#distE").val(data.organi_distrito)
                );
            });
        },
        error: function (data) {},
    });
}
Datos();
$("#fechaNacimiento").combodate({
    yearDescending: false,
});
$('[data-toggle="tooltip"]').tooltip();
$("#disabledDatosP :input").attr("disabled", true);
$('#disabledDatosP button[type="button"]').hide();
$("#guardarPersonalizarSexo").prop("disabled", true);
$("#guardarPersonalizarOrganizacion").prop("disabled", true);
$("#editarDatosP").on("click", function () {
    $("#disabledDatosP :input").attr("disabled", false);
    $('#disabledDatosP button[type="button"]').show();
    if ($("#genero").val() == "Personalizado") {
        $("#generoPersonalizado").show();
    } else {
        $("#generoPersonalizado").hide();
    }
});
$("#nombre").keyup(function () {
    if ($("#nombre").val() == "") {
        $("#actualizarDatosPersonales").attr("disabled", true);
    } else {
        $("#actualizarDatosPersonales").attr("disabled", false);
    }
});
$("#fechaN").change(function () {
    if ($("#fechaN").val() == "") {
        $("#actualizarDatosPersonales").attr("disabled", true);
    } else {
        $("#actualizarDatosPersonales").attr("disabled", false);
    }
});
$("#apPaternoP").keyup(function () {
    if ($("#apPaternoP").val() == "") {
        $("#actualizarDatosPersonales").attr("disabled", true);
    } else {
        $("#actualizarDatosPersonales").attr("disabled", false);
    }
});
$("#direccion").keyup(function () {
    if ($("#direccion").val() == "") {
        $("#actualizarDatosPersonales").attr("disabled", true);
    } else {
        $("#actualizarDatosPersonales").attr("disabled", false);
    }
});
$("#apMaternoP").keyup(function () {
    if ($("#apMaternoP").val() == "") {
        $("#actualizarDatosPersonales").attr("disabled", true);
    } else {
        $("#actualizarDatosPersonales").attr("disabled", false);
    }
});
$("#genero").change(function () {
    console.log("ingreso");
    if ($("#genero").val() == "Personalizado") {
        $("#generoPersonalizado").show();
    } else {
        $("#generoPersonalizado").hide();
    }
});
$("#disabledDatosE :input").attr("disabled", true);
$('#disabledDatosE button[type="button"]').hide();
$("#editarDatosE").on("click", function () {
    $("#disabledDatosE :input").attr("disabled", false);
    $("#ruc").attr("disabled", true);
    $('#disabledDatosE button[type="button"]').show();
    if ($("#organizacion").val() == "Otros") {
        $("#organizacionPersonalizado").show();
    } else {
        $("#organizacionPersonalizado").hide();
    }
});
$("#razonS").keyup(function () {
    if ($("#razonS").val() == "") {
        $("#actualizarDatosEmpresa").attr("disabled", true);
    } else {
        $("#actualizarDatosEmpresa").attr("disabled", false);
    }
});
$("#numE").keyup(function () {
    if ($("#numE").val() == "") {
        $("#actualizarDatosEmpresa").attr("disabled", true);
    } else {
        $("#actualizarDatosEmpresa").attr("disabled", false);
    }
});
$("#depE").change(function () {
    if ($("#depE").val() == "") {
        $("#provE").attr("disabled", true);
        $("#distE").attr("disabled", true);
        $("#actualizarDatosEmpresa").attr("disabled", true);
    } else {
        $("#provE").attr("disabled", false);
        $("#distE").attr("disabled", false);
        $("#actualizarDatosEmpresa").attr("disabled", false);
    }
});
$("#provE").change(function () {
    if ($("#provE").val() == "") {
        $("#distE").attr("disabled", true);
        $("#actualizarDatosEmpresa").attr("disabled", true);
    } else {
        $("#distE").attr("disabled", false);
        $("#actualizarDatosEmpresa").attr("disabled", false);
    }
});
$("#distE").change(function () {
    if ($("#distE").val() == "") {
        $("#actualizarDatosEmpresa").attr("disabled", true);
    } else {
        $("#actualizarDatosEmpresa").attr("disabled", false);
    }
});
$("#organizacion").change(function () {
    if ($("#organizacion").val() == "Otros") {
        $("#organizacionPersonalizado").show();
    } else {
        $("#organizacionPersonalizado").hide();
    }
});
function limpiartextSexo() {
    $("#textSexo").val("");
    $("#guardarPersonalizarSexo").prop("disabled", true);
}

function limpiartextOrganizacion() {
    $("#textOrganizacion").val("");
    $("#guardarPersonalizarOrganizacion").prop("disabled", true);
}
$("#generoPersonalizado").on("click", function () {
    $("#generoModal").modal();
});
$("#organizacionPersonalizado").on("click", function () {
    $("#organizacionModal").modal();
});
$("#textSexo").keyup(function () {
    if ($(this).val() != "") {
        $("#guardarPersonalizarSexo").prop("disabled", false);
    } else {
        $("#guardarPersonalizarSexo").prop("disabled", true);
    }
});

$("#textOrganizacion").keyup(function () {
    if ($(this).val() != "") {
        $("#guardarPersonalizarOrganizacion").prop("disabled", false);
    } else {
        $("#guardarPersonalizarOrganizacion").prop("disabled", true);
    }
});

function personalizadoGenero() {
    var sexo = $("#textSexo").val();
    $("#genero").append(
        $("<option>", {
            value: sexo,
            text: sexo,
            selected: true,
        })
    );
    $("#genero").val(sexo);
    $("#generoModal").modal("toggle");
    limpiartextSexo();
}

function personalizadoOrganizacion() {
    var organi = $("#textOrganizacion").val();
    $("#organizacion").append(
        $("<option>", {
            value: organi,
            text: organi,
            selected: true,
        })
    );
    $("#organizacion").val(organi);
    $("#organizacionModal").modal("toggle");
    limpiartextOrganizacion();
}

function limpiarDatosPersonales() {
    refreshGenero();
    Datos();
    $("#disabledDatosP :input").attr("disabled", true);
    $("#guardarPersonalizarSexo").prop("disabled", true);
    $('#disabledDatosP button[type="button"]').hide();
    $("#generoPersonalizado").hide();
}

function limpiarDatosEmpresarial() {
    regreshOrgani();
    Datos();
    $("#disabledDatosE :input").attr("disabled", true);
    $('#disabledDatosE button[type="button"]').hide();
    $("#organizacionPersonalizado").hide();
}

function editarDatosPersonales() {
    objDatosPersonales = datosPersonales("POST");
    enviarDatosP("", objDatosPersonales);
}

function datosPersonales(method) {
    nuevoDatos = {
        id: $("#id").val(),
        nombre: $("#nombre").val(),
        fechaN: $("#fechaNacimiento").val(),
        apPaterno: $("#apPaternoP").val(),
        direccion: $("#direccion").val(),
        apMaterno: $("#apMaternoP").val(),
        genero: $("#genero").val(),
        _method: method,
    };
    return nuevoDatos;
}

function enviarDatosP(accion, objDatosPersonales) {
    var idU = $("#id").val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/editarUser" + accion,
        data: {
            objDatosPersonales: objDatosPersonales,
            id: idU,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            console.log(data);
            var h5 = `${data.perso_nombre} ${data.perso_apPaterno} ${data.perso_apMaterno}`;
            var strong = `Bienvenido(a), ${data.perso_nombre}`;
            actualizarDatos();
            refreshGenero();
            regreshOrgani();
            Datos();
            $("#h5Nombres").empty();
            $("#h6Nombres").empty();
            $("#strongNombre").empty();
            $("#h5Nombres").append(h5);
            $("#h6Nombres").append(h5);
            $("#strongNombre").append(strong);
            $("#disabledDatosP :input").attr("disabled", true);
            $('#disabledDatosP button[type="button"]').hide();
            $("#generoPersonalizado").hide();
            $.notify(
                {
                    message: "\nPerfil Editado\n",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () {
            alert("error");
        },
    });
}

function editarDatosEmpresa() {
    objDatosEmpresa = datosEmpresa("POST");
    enviarDatosE("", objDatosEmpresa);
}

function datosEmpresa(method) {
    nuevoDatos = {
        id: $("#id").val(),
        razonSocial: $("#razonS").val(),
        direccion: $("#direccionE").val(),
        nempleados: $("#numE").val(),
        pagWeb: $("#pagWeb").val(),
        tipo: $("#organizacion").val(),
        departamento: $("#depE").val(),
        provincia: $("#provE").val(),
        distrito: $("#distE").val(),
        _method: method,
    };
    return nuevoDatos;
}

function enviarDatosE(accion, objDatosEmpresa) {
    var idU = $("#id").val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/editarEmpresa" + accion,
        data: {
            objDatosEmpresa: objDatosEmpresa,
            id: idU,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            console.log(data);
            var h6 = `${data.organi_razonSocial}`;
            actualizarDatos();
            refreshGenero();
            regreshOrgani();
            Datos();
            $("#h6Empresa").empty();
            $("#strongOrganizacion").empty();
            $("#h6Empresa").append(h6);
            $("#strongOrganizacion").append(h6);
            $("#disabledDatosE :input").attr("disabled", true);
            $('#disabledDatosE button[type="button"]').hide();
            $("#organizacionPersonalizado").hide();
            $.notify(
                {
                    message: "\nPerfil Editado\n",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () {
            alert("error");
        },
    });
}
$("#actualizarDatosPersonales").on("click", editarDatosPersonales);
$("#actualizarDatosEmpresa").on("click", editarDatosEmpresa);
$(document).on("click", ".browse", function () {
    var file = $(this).parents().find(".file");
    file.trigger("click");
});
$('input[type="file"]').change(function (e) {
    var fileName = e.target.files[0].name;
    $("#nameFoto").val(fileName);
    var reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById("preview").src = e.target.result;
    };
    reader.readAsDataURL(this.files[0]);
    $("#rowAlert").show();
});
$("#guardarFoto").on("click", function () {
    console.log($(".file").prop("files")[0]);
    var formData = new FormData();
    formData.append("file", $(".file").prop("files")[0]);
    console.log(formData);
    $.ajax({
        contentType: false,
        processData: false,
        type: "POST",
        url: "/foto",
        data: formData,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            console.log(data);
            $("#rowAlert").hide();
            $("#preview").attr("src", "fotosUser/" + data[0].foto);
            $("#imgsm").attr("src", "fotosUser/" + data[0].foto);
            $("#imgxs").attr("src", "fotosUser/" + data[0].foto);
            $("#imgxs2").attr("src", "fotosUser/" + data[0].foto);
            $.notify(
                {
                    message: "\nPerfil Editado\n",
                    icon: "admin/images/checked.svg",
                },
                {
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function () {
            alert("error");
        },
    });
});
function refreshGenero(){
    $('#genero').empty();
    genero = '<option class="" value="Mujer">Mujer</option>\
    <option class="" value="Hombre">Hombre</option>\
    <option class="" value="Personalizado">Personalizado</option>';
    $('#genero').append(genero);
}
function regreshOrgani(){
    $('#organizacion').empty();
    organizacion = '<option class="" value="Empresa">Empresa</option>\
    <option class="" value="Gobierno">Gobierno</option>\
    <option class="" value="ONG">ONG</option>\
    <option class="" value="Asociación">Asociación</option>\
    <option class="" value="Otros">Otros</option>';
    $('#organizacion').append(organizacion);
}
function cambiarCont() {
    $("#frmCamb")[0].reset();
    $("#cambiarContras").modal("show");
}
function cambioClave() {
    var cantigua = $("#contraAnti").val();
    var cnueva = $("#contraNue").val();
    $.ajax({
        type: "post",
        url: "perfil/cambiarCont",
        data: {
            cantigua,
            cnueva,
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            if (data == 1) {
                $("#cambiarContras").modal("hide");
                $.notify(
                    {
                        message: "\nContraseña cambiada\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        position: "fixed",
                        icon_type: "image",
                        newest_on_top: true,
                        delay: 4000,
                        template:
                            '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            "</div>",
                        spacing: 25,
                    }
                );
            } else {
                bootbox.alert({
                    message: "Contraseña actual incorrecta",
                });

                return false;
            }
        },
        error: function () {
            alert("Hay un error");
        },
    });
}
