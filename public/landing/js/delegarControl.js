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
    $('#switchEmpS').prop('checked', true);
    $('#nombreEmpleado').prop('disabled', false);
    $('#switchAreaS').prop('checked', false);
    $('#selectArea').prop('disabled', true);
    $('#divArea').hide();
    $('#divEmpleado').show();
    $("#selectTodoCheck").prop('checked', false);
    $("#selectAreaCheck").prop('checked', false);
    $("#AlcaAdminCheck").prop('checked', false);
    $("#divDash").show();
    $('#switchEmpS').prop('disabled', false);
    $('#switchAreaS').prop('disabled', false);

        $("#selectArea").prop("required", false);
        $("#divAdminPersona").show();
        $('#opcionesGE').hide();
        $('#opcionesActiv').hide();
        $('#opcionesAPuerta').hide();
        $('#verCheckPuerta').prop('required',false);
        $("#divAsisPu").show();
        $("#divControlRe").show();
        $("#divReporteAsis").show();
        $("#divGestActivi").show();
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
                 var permisoEmp;
                 if ($("#AlcaAdminCheck").is(":checked")) {
                    permisoEmp=1;} else{
                        permisoEmp=0;
                   }
                   var  switchActividades;
                   if ($("#gestActiCheck").is(":checked")) {
                    switchActividades=1;} else{
                        switchActividades=0;
                   }

                    var switchasisPuerta;
                    if ($("#asistPuertaCheck").is(":checked")) {
                        switchasisPuerta=1;} else{
                            switchasisPuerta=0;
                       }

                    var switchCRemo;
                    if ($("#ControlReCheck").is(":checked")) {
                        switchCRemo=1;} else{
                            switchCRemo=0;
                       }

                    var swReporteAsis;
                    if ($("#ReporteAsistCheck").is(":checked")) {
                        swReporteAsis=1;} else{
                            swReporteAsis=0;
                       }

                    var checkTodoEmp;
                    if ($("#TodoECheck").is(":checked")) {
                        checkTodoEmp=1;} else{
                            checkTodoEmp=0;
                       }
                   /////////////////////////////////////
                   var agregarEmp;
                    var modifEmp;
                     var bajaEmp;
                      var gActiEmp;
                   var agregarActi;
                    var modifActi;
                    var bajaActi;
                   var verPuerta;
                    var agregPuerta;
                     var ModifPuerta;

                   if ($("#AgregarCheckG").is(":checked")) {
                    agregarEmp=1;} else{
                        agregarEmp=0;
                   }

                   if ($("#ModifCheckG").is(":checked")) {
                    modifEmp=1;} else{
                        modifEmp=0;
                   }

                   if ($("#BajaCheckG").is(":checked")) {
                    bajaEmp=1;} else{
                        bajaEmp=0;
                   }

                   if ($("#ActivCheckG").is(":checked")) {
                    gActiEmp=1;} else{
                        gActiEmp=0;
                   }

                   if ($("#AgregarCheckActiv").is(":checked")) {
                    agregarActi=1;} else{
                        agregarActi=0;
                   }

                   if ($("#ModifCheckActiv").is(":checked")) {
                    modifActi=1;} else{
                        modifActi=0;
                   }

                   if ($("#BajaCheckActiv").is(":checked")) {
                    bajaActi=1;} else{
                        bajaActi=0;
                   }

                   if ($("#verCheckPuerta").is(":checked")) {
                    verPuerta=1;} else{
                        verPuerta=0;
                   }

                   if ($("#AgregarCheckPuerta").is(":checked")) {
                    agregPuerta=1;} else{
                        agregPuerta=0;
                   }

                   if ($("#ModifCheckPuerta").is(":checked")) {
                    ModifPuerta=1;} else{
                        ModifPuerta=0;
                   }
                   /////////////////////////////////////
                    if ($('#switchEmpS').prop('checked')) {
                            $.ajax({
                                type: "post",
                                url: "/registrarInvitado",
                                data: {
                                    emailInv,
                                    idEmpleado,dash,permisoEmp,agregarEmp, modifEmp, bajaEmp, gActiEmp, agregarActi, modifActi,
                                    bajaActi, verPuerta, agregPuerta, ModifPuerta, switchActividades, switchasisPuerta,switchCRemo,
                                    checkTodoEmp,swReporteAsis
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
                        else{
                            idareas = $("#selectArea").val();
                            console.log(idareas);
                            $.ajax({
                                type: "post",
                                url: "/registrarInvitadoArea",
                                data: {
                                    emailInv,
                                    idareas,dash,permisoEmp, agregarEmp, modifEmp, bajaEmp, gActiEmp, agregarActi, modifActi,
                                    bajaActi, verPuerta, agregPuerta, ModifPuerta, switchActividades, switchasisPuerta,
                                    switchCRemo, checkTodoEmp,swReporteAsis
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
         $("#selectArea").prop("required", false);
        $("#divDash").hide();
        $("#divAdminPersona").hide();
        $("#opcionesGE").hide();
        $("#divGestActivi").hide();
        $("#opcionesActiv").hide();
        $("#divAsisPu").hide();
        $("#divControlRe").hide();
        $("#divReporteAsis").hide();
        $("#opcionesAPuerta").hide();
        $('#verCheckPuerta').prop('required',false);
    } else {
         $("#nombreEmpleado").prop("required", true);
        $("#divInvitado").show();
        $("#divDash").show();
        $("#divAdminPersona").show();
        $("#opcionesGE").show();
        $("#opcionesActiv").show();
        $("#divAsisPu").show();
        $("#divControlRe").show();
        $("#divReporteAsis").show();
        $("#divGestActivi").show();
        $("#opcionesAPuerta").show();
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
                $("#divAdminPersona_edit").hide();
                $("#nombreEmpleado_edit").prop("required", false);
                $("#divDash_edit").hide();
                $("#divGestActivi_edit").hide();
                $("#opcionesActiv_edit").hide();
                $("#divAsisPu_edit").hide();
                $("#opcionesAPuerta_edit").hide();
                $("#divControlRe_edit").hide();
                $("#divReporteAsis_edit").hide();

            }
            else{
                $('#adminCheck_edit').prop('checked', false);
                $("#divInvitado_edit").show();
                $("#divAdminPersona_edit").show();
                $("#nombreEmpleado_edit").prop("required", true);
                if(data[0].emple_id!=null){
                    $('#divEmpleado_edit').show();
                    $('#switchEmpS_edit').prop('checked', true);
                    $('#switchAreaS_edit').prop('checked', false);
                $.each( data, function( index, value ){
                $("#nombreEmpleado_edit option[value='"+ value.emple_id +"']").prop("selected","selected");
                $("#nombreEmpleado_edit").trigger("change");
                $("#nombreEmpleado_edit").prop('disabled',false);
                 });
                  $('#divArea_edit').hide();
                }
                else{
                    $('#switchEmpS_edit').prop('checked', false);
                    $('#switchAreaS_edit').prop('checked', true);
                    $('#divArea_edit').show();
                    $.each( data, function( index, value ){
                        $("#selectArea_edit option[value='"+ value.area_id +"']").prop("selected","selected");
                        $("#selectArea_edit").trigger("change");
                         });
                    $('#divEmpleado_edit').hide();
                }


            }
            if(data[0].dashboard==1){
                $('#dashboardCheck_edit').prop('checked', true);
            }else{
                $('#dashboardCheck_edit').prop('checked', false);
            }
            if(data[0].permiso_Emp==1){
                $('#AlcaAdminCheck_edit').prop('checked', true);
            }else{
                $('#AlcaAdminCheck_edit').prop('checked', false);
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
        $("#selectArea_edit").prop("required", false);
        $("#divAdminPersona_edit").hide();
        $("#divEmpleado_edit").hide();
        $("#divDash_edit").hide();
        $("#divGestActivi_edit").hide();
        $("#opcionesActiv_edit").hide();
        $("#divAsisPu_edit").hide();
        $("#opcionesAPuerta_edit").hide();
        $("#divControlRe_edit").hide();
        $("#divReporteAsis_edit").hide();

    } else {
        if($("#switchAreaS_edit").is(":checked")){
            $("#switchAreaS_edit").prop("checked", true);
            $('#divArea_edit').show();
            $("#switchEmpS_edit").prop("checked", false);
                $('#divEmpleado_edit').hide();
        }
        else{
            $("#switchAreaS_edit").prop("checked", false);
            $('#divArea_edit').hide();
            if($("#switchEmpS_edit").is(":checked")){
                $("#switchEmpS_edit").prop("checked", true);
                $('#divEmpleado_edit').show();
            }
            else{
                $('#divEmpleado_edit').hide();
            }

        }
        $("#nombreEmpleado_edit").prop("required", true);
        $("#divInvitado_edit").show();
        $("#divAdminPersona_edit").show();




        $("#nombreEmpleado_edit").prop("disabled", false);
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
    var permisoEmp_ed;
    if ($("#dashboardCheck_edit").is(":checked")) {
        dash_ed=1;} else{
            dash_ed=0;
        }
        if ($("#AlcaAdminCheck_edit").is(":checked")) {
            permisoEmp_ed=1;} else{
                permisoEmp_ed=0;
           }
           if ($('#switchEmpS_edit').prop('checked')) {
            $.ajax({
                type: "post",
                url: "/editarInviI",
                data: { idinvitado,
                    idEmpleado,dash_ed,permisoEmp_ed
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
           } else{
            if ($('#switchAreaS_edit').prop('checked')) {
                idareas_edit = $("#selectArea_edit").val();
                $.ajax({
                    type: "post",
                    url: "/editarInviArea",
                    data: { idinvitado,
                        idareas_edit,dash_ed,permisoEmp_ed
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

        bootbox.confirm({
            message: "¿Deseas  notificar por correo al invitado?",
            buttons: {
                confirm: {
                    label: 'Enviar',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-light'
                }
            },
            callback: function (result) {
                if (result == true) {
                    $.ajax({
                        type: "post",
                        url: "/notificarInv",
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

                        },
                        error: function (data) {
                            alert("Ocurrio un error");
                        },
                    });
                   }
            }
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

/////////cambios en switch
$('#switchEmpS').change(function (event) {
    if ($('#switchEmpS').prop('checked')) {
        $('#switchAreaS').prop('checked', false);
        $('#selectArea').prop('disabled', true);
        $('#nombreEmpleado').prop('disabled', false);
        $("#selectArea > option").prop("selected", false);
        $("#selectArea").trigger("change");
        $('#divArea').hide();
        $('#divEmpleado').show();
        $("#nombreEmpleado > option").prop("selected", false);
        $("#nombreEmpleado").trigger("change");
        $("#selectTodoCheck").prop('checked', false);
        $('#TodoECheck').prop('checked', false);
        $('#nombreEmpleado').prop('required',true);
        $('#divTodoECheck').show();


    }
    else{
        $('#selectArea').prop('disabled', false);
        $('#divEmpleado').hide();
        $('#divTodoECheck').hide();

    }
});

$('#switchAreaS').change(function (event) {
    if ($('#switchAreaS').prop('checked')) {
        $('#switchEmpS').prop('checked', false);
        $('#nombreEmpleado').prop('disabled', true);
        $('#selectArea').prop('disabled', false);
        $("#nombreEmpleado > option").prop("selected", false);
        $("#nombreEmpleado").trigger("change");
        $('#divEmpleado').hide();
        $('#divArea').show();
        $("#selectAreaCheck").prop('checked', false);
        $("#nombreEmpleado").prop("required", false);
        $("#selectArea").prop("required", true);
    }
    else{
        $('#nombreEmpleado').prop('disabled', false);
        $('#divArea').hide();
    }
});

/////////////////////////////
/////////cambios en switch en editar
$('#switchEmpS_edit').change(function (event) {
    if ($('#switchEmpS_edit').prop('checked')) {
        $('#switchAreaS_edit').prop('checked', false);
        $('#selectArea_edit').prop('disabled', true);
        $('#nombreEmpleado_edit').prop('disabled', false);
        $("#selectArea_edit > option").prop("selected", false);
        $("#selectArea_edit").trigger("change");
        $('#divArea_edit').hide();
        $('#divEmpleado_edit').show();
        $("#nombreEmpleado_edit > option").prop("selected", false);
        $("#nombreEmpleado_edit").trigger("change");
        $("#selectTodoCheck_edit").prop('checked', false);


    }
    else{
        $('#selectArea_edit').prop('disabled', false);
        $('#divEmpleado_edit').hide();
    }
});

$('#switchAreaS_edit').change(function (event) {
    if ($('#switchAreaS_edit').prop('checked')) {
        $('#switchEmpS_edit').prop('checked', false);
        $('#nombreEmpleado_edit').prop('disabled', true);
        $('#selectArea_edit').prop('disabled', false);
        $("#nombreEmpleado_edit > option").prop("selected", false);
        $("#nombreEmpleado_edit").trigger("change");
        $('#divEmpleado_edit').hide();
        $('#divArea_edit').show();
        $("#selectAreaCheck_edit").prop('checked', false);
        $("#nombreEmpleado_edit").prop("required", false);
        $("#selectArea_edit").prop("required", true);
    }
    else{
        $('#nombreEmpleado_edit').prop('disabled', false);
        $('#divArea_edit').hide();
    }
});
////////////////////////////////////////
$('#AlcaAdminCheck').change(function (event) {
    if ($('#AlcaAdminCheck').prop('checked')) {
        $('#opcionesGE').show();
    }
    else{
        $('#opcionesGE').hide();
    }
});
////////////////////////////////////////////

////////////////////////////////////////
$('#gestActiCheck').change(function (event) {
    if ($('#gestActiCheck').prop('checked')) {
        $('#opcionesActiv').show();
    }
    else{
        $('#opcionesActiv').hide();
    }
});
////////////////////////////////////////////
////////////////////////////////////////
$('#asistPuertaCheck').change(function (event) {
    if ($('#asistPuertaCheck').prop('checked')) {
        $('#opcionesAPuerta').show();
        $('#verCheckPuerta').prop('required',true);

    }
    else{
        $('#opcionesAPuerta').hide();
        $('#verCheckPuerta').prop('required',false);

    }
});
////////////////////////////////////////////
$("#TodoECheck").click(function () {
    if ($("#TodoECheck").is(":checked")) {
        $('#nombreEmpleado').prop('required',false);
        $("#divEmpleado").hide();
    } else {
        $('#nombreEmpleado').prop('required',true);
        $("#divEmpleado").show();
    }
});
////////////////////////////////////////////


////////////////////////////////////////
$('#AlcaAdminCheck_edit').change(function (event) {
    if ($('#AlcaAdminCheck_edit').prop('checked')) {
        $('#opcionesGE_edit').show();
    }
    else{
        $('#opcionesGE_edit').hide();
    }
});
////////////////////////////////////////////

////////////////////////////////////////
$('#gestActiCheck_edit').change(function (event) {
    if ($('#gestActiCheck_edit').prop('checked')) {
        $('#opcionesActiv_edit').show();
    }
    else{
        $('#opcionesActiv_edit').hide();
    }
});
////////////////////////////////////////////
////////////////////////////////////////
$('#asistPuertaCheck_edit').change(function (event) {
    if ($('#asistPuertaCheck_edit').prop('checked')) {
        $('#opcionesAPuerta_edit').show();
        $('#verCheckPuerta_edit').prop('required',true);

    }
    else{
        $('#opcionesAPuerta_edit').hide();
        $('#verCheckPuerta_edit').prop('required',false);

    }
});
////////////////////////////////////////////
$("#TodoECheck_edit").click(function () {
    if ($("#TodoECheck_edit").is(":checked")) {
        $('#nombreEmpleado_edit').prop('required',false);
        $("#divEmpleado_edit").hide();
    } else {
        $('#nombreEmpleado_edit').prop('required',true);
        $("#divEmpleado_edit").show();
    }
});
////////////////////////////////////////////

$(function() {

});

