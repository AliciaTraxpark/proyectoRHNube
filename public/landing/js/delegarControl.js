//modal
function abrirRegist(){
    $('#frmInvi')[0].reset();
    $('#spanEm').hide();
    $('#nombreEmpleado').prop('required',true);
    $('#divInvitado').show();
    $("#nombreEmpleado > option").prop("selected", false);
    $("#nombreEmpleado").trigger("change");
    $("#selectArea > option").prop("selected", false);
    $("#selectArea").trigger("change");
    $('#btnGu').prop('disabled',false);
    $('#agregarInvitado').modal('show');
}

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
                $('#btnGu').prop('disabled',true);
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
                        var dialog = bootbox.dialog({
                            message: "Invitado registrado con exito",
                            closeButton: false
                        });
                        setTimeout(function(){
                            dialog.modal('hide')
                        }, 1000);
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
                        var dialog = bootbox.dialog({
                            message: "Invitado registrado con exito",
                            closeButton: false
                        });
                        setTimeout(function(){
                            dialog.modal('hide')
                        }, 1000);
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
///ver datos de invitado en editar
function editarInv(idi){
    $('#btnGu_edit').prop('disabled',false);
    $.ajax({
        type: "post",
        url: "/datosInvitado",
        data: {
            idi
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
            $('#idInv').val(data[0].idinvitado);
            $('#frmInvi_edit')[0].reset();
            $("#nombreEmpleado_edit > option").prop("selected", false);
            $("#nombreEmpleado_edit").trigger("change");
            $("#selectArea_edit > option").prop("selected", false);
            $("#selectArea_edit").trigger("change");
            $('#emailInvi_edit').val(data[0].email_inv);
             if(data[0].rol_id==1){
                $('#adminCheck_edit').prop('checked', true);
                $("#divInvitado_edit").hide();
                $("#nombreEmpleado_edit").prop("required", false);

            }
            else{
                $('#adminCheck_edit').prop('checked', false);
                $("#divInvitado_edit").show();
                $("#nombreEmpleado_edit").prop("required", true);
                $.each( data, function( index, value ){
                    $("#nombreEmpleado_edit option[value='"+ value.emple_id +"']").prop("selected","selected");
                    $("#nombreEmpleado_edit").trigger("change");
                });

            }
            if(data[0].dashboard==1){
                $('#dashboardCheck_edit').prop('checked', true);
            }else{
                $('#dashboardCheck_edit').prop('checked', false);
            }
            $("#agregarInvitado_edit").modal('show');
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });


}
//admin_edit
$("#adminCheck_edit").click(function () {
    if ($("#adminCheck_edit").is(":checked")) {
        $("#divInvitado_edit").hide();
        $("#nombreEmpleado_edit").prop("required", false);
    } else {
        $("#nombreEmpleado_edit").prop("required", true);
        $("#divInvitado_edit").show();
    }
});
//select all empleados_edit
$("#selectTodoCheck_edit").click(function () {
    if ($("#selectTodoCheck_edit").is(":checked")) {
        $("#nombreEmpleado_edit > option").prop("selected", "selected");
        $("#nombreEmpleado_edit").trigger("change");
    } else {
        $("#nombreEmpleado_edit > option").prop("selected", false);
        $("#nombreEmpleado_edit").trigger("change");
    }
});

//selct all area_edi
$("#selectAreaCheck_edit").click(function () {
    if ($("#selectAreaCheck_edit").is(":checked")) {
        $("#selectArea_edit > option").prop("selected", "selected");
        $("#selectArea_edit").trigger("change");
    } else {
        $("#selectArea_edit > option").prop("selected", false);
        $("#selectArea_edit").trigger("change");
    }
});

///////////////seleccionar empleado por area_edit
$("#selectArea_edit").change(function (e) {
    var idempresarial = [];
    idempresarial = $("#selectArea_edit").val();
    textSelec = $('select[name="selectArea_edit"] option:selected:last').text();
    textSelec2 = $('select[name="selectArea_edit"] option:selected:last').text();

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
                $("#nombreEmpleado_edit > option").prop("selected", false);
                $("#nombreEmpleado_edit").trigger("change");
                $.each(data, function (index, value) {
                    $.each(value, function (index, value1) {
                        $(
                            "#nombreEmpleado_edit > option[value='" +
                                value1.emple_id +
                                "']"
                        ).prop("selected", "selected");
                        $("#nombreEmpleado_edit").trigger("change");
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
function registrarInvit_edit()
{
   var idinvitado=  $('#idInv').val();
   var idEmpleado = $("#nombreEmpleado_edit").val();
   if ($("#adminCheck_edit").is(":checked")) {
    $.ajax({
        type: "post",
        url: "/editarInviAdm",
        data: {
            idinvitado
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
            $('#btnGu_edit').prop('disabled',true);
            $('#tablaInvit').load(location.href + " #tablaInvit>*");
            $('#agregarInvitado_edit').modal('hide');
            var dialog = bootbox.dialog({
                message: "Invitado editado correctamente",
                closeButton: false
            });
            setTimeout(function(){
                dialog.modal('hide')
            }, 1000);
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
}  else {

    var dash_ed;
    if ($("#dashboardCheck_edit").is(":checked")) {
        dash_ed=1;} else{
            dash_ed=0;
        }
    $.ajax({
        type: "post",
        url: "/editarInviI",
        data: { idinvitado,
            idEmpleado,dash_ed
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
            $('#agregarInvitado_edit').modal('hide');
            var dialog = bootbox.dialog({
                message: "Invitado editado correctamente",
                closeButton: false
            });
            setTimeout(function(){
                dialog.modal('hide')
            }, 1000);
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
}



}
////////switch estado
function cambioswitch(idinvitado){
    var estadosw;
    if( $('#activaSwitch'+idinvitado).is(':checked')) {

        estadosw=1;
        $.ajax({
            type: "post",
            url: "/cambInvitadoswit",
            data: { idinvitado,
                estadosw
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
                $('#lblActiva'+idinvitado).text("Activado");
            },
            error: function (data) {
                alert("Ocurrio un error");
            },
        });

    }
    else{

        estadosw=0;

        $.ajax({
            type: "post",
            url: "/cambInvitadoswit",
            data: { idinvitado,
                estadosw
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
                $('#lblActiva'+idinvitado).text("Desactivado");
            },
            error: function (data) {
                alert("Ocurrio un error");
            },
        });
    }

}
