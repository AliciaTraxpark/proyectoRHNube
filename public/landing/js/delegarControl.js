//select all empleados
$("#selectTodoCheck").click(function () {
    if ($("#selectTodoCheck").is(":checked")) {
        $("#nombreEmpleado > option").prop("selected", "selected");
        $("#nombreEmpleado").trigger("change");
    } else {
        $("#nombreEmpleado > option").prop("selected", false);
        $("#nombreEmpleado").trigger("change");
    }
});

//selct all area
$("#selectAreaCheck").click(function () {
    if ($("#selectAreaCheck").is(":checked")) {
        $("#selectArea > option").prop("selected", "selected");
        $("#selectArea").trigger("change");
    } else {
        $("#selectArea > option").prop("selected", false);
        $("#selectArea").trigger("change");
    }
});

///////////////seleccionar empleado por area
$("#selectArea").change(function (e) {
    var idempresarial = [];
    idempresarial = $("#selectArea").val();
    textSelec = $('select[name="selectArea"] option:selected:last').text();
    textSelec2 = $('select[name="selectArea"] option:selected:last').text();

    palabraEmpresarial = textSelec.split(" ")[0];
    if (palabraEmpresarial == "Area") {
        $.ajax({
            type: "post",
            url: "/empleAreaIn",
            data: {
                idarea: idempresarial,
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
                $("#nombreEmpleado > option").prop("selected", false);
                $("#nombreEmpleado").trigger("change");
                $.each(data, function (index, value) {
                    $.each(value, function (index, value1) {
                        $(
                            "#nombreEmpleado > option[value='" +
                                value1.emple_id +
                                "']"
                        ).prop("selected", "selected");
                        $("#nombreEmpleado").trigger("change");
                    });
                });
                console.log(data);
            },
            error: function (data) {
                alert("Ocurrio un error");
            },
        });
    }
});

///funcion registrar invitado
function registrarInvit() {
    var emailInv = $("#emailInvi").val();
    var idEmpleado = $("#nombreEmpleado").val();
    $.ajax({
        type: "post",
        url: "/verificarEmaD",
        data: {
            email:emailInv
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
           if(data==1){
            $('#spanEm').show();
               return false;
           }
           else{   $('#spanEm').hide();
            if ($("#adminCheck").is(":checked")) {
                $.ajax({
                    type: "post",
                    url: "/registrarInvitadoAdm",
                    data: {
                        emailInv
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
                        $('#tablaInvit').load(location.href + " #tablaInvit>*");
                        $('#agregarInvitado').modal('hide');
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            } else {
                $('#btnGu').prop('disabled',true);
                var dash;
                if ($("#dashboardCheck").is(":checked")) {
                     dash=1;} else{
                     dash=0;
                    }
                $.ajax({
                    type: "post",
                    url: "/registrarInvitado",
                    data: {
                        emailInv,
                        idEmpleado,dash
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
                        $('#tablaInvit').load(location.href + " #tablaInvit>*");
                        $('#agregarInvitado').modal('hide');
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
           }
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });


}
//admin check
$("#adminCheck").click(function () {
    if ($("#adminCheck").is(":checked")) {
        $("#divInvitado").hide();
        $("#nombreEmpleado").prop("required", false);
    } else {
        $("#nombreEmpleado").prop("required", true);
        $("#divInvitado").show();
    }
});
