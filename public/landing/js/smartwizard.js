$(document).ready(function () {
    function validateSteps(stepnumber) {
        var isStepValid = true;
        // validate step 1
        if (stepnumber == 0) {
            if ($('#numDocumento').val() == "") {
                isStepValid = false;
                $('#validDocumento').show();
            } else {
                //VALIDAR NUMERO DOCUMENTO
                var numeroD = $('#numDocumento').val();
                idE = $('#idEmpleado').val();
                if (idE == '') {
                    $.ajax({
                        async: false,
                        type: "GET",
                        url: "numDoc",
                        data: {
                            numeroD: numeroD
                        },
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
                            if (data == 1) {
                                $('#numR').show();
                                isStepValid = false;
                                return false;
                            } else {
                                $('#numR').hide();
                            }
                        }
                    });
                } else {
                    $.ajax({
                        async: false,
                        type: "GET",
                        url: "numDocStore",
                        data: {
                            numeroD: numeroD,
                            idE: idE
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        statusCode: {
                            419: function () {
                                location.reload();
                            }
                        },
                        success: function (data) {
                            if (data == 1) {
                                $('#numR').show();
                                isStepValid = false;
                                return false;
                            } else {
                                $('#numR').hide();
                            }
                        }
                    });
                }
                $('#validDocumento').hide();
            }
            if ($('#apPaterno').val() == "") {
                isStepValid = false;
                $('#validApPaterno').show();
            } else {
                $('#validApPaterno').hide();
            }
            if ($('#numDocumento').val() == "") {
                isStepValid = false;
                $('#validNumDocumento').show();
            } else {
                $('#validNumDocumento').hide();
            }
            if ($('#apMaterno').val() == "") {
                isStepValid = false;
                $('#validApMaterno').show();
            } else {
                $('#validApMaterno').hide();
            }
            if ($('#nombres').val() == "") {
                isStepValid = false;
                $('#validNombres').show();
            } else {
                $('#validNombres').hide();
            }
            if ($("input[type=radio]:checked").length == 0) {
                isStepValid = false;
                $('#validGenero').show();
            } else {
                $('#validGenero').hide();
            }
            if ($("#email").val() != "") {
                //VALIDAR CORREO
                var email = $('#email').val();
                $('#validCorreo').hide();
                idE = $('#idEmpleado').val();
                if (idE == '') {
                    $.ajax({
                        async: false,
                        type: "GET",
                        url: "email",
                        data: {
                            email: email
                        },
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
                            if (data == 1) {
                                $('#emailR').show();
                                isStepValid = false;
                                return false;
                            } else {
                                $('#emailR').hide();
                            }
                        }
                    });
                } else {
                    $.ajax({
                        async: false,
                        type: "GET",
                        url: "emailE",
                        data: {
                            email: email,
                            idE: idE
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            if (data == 1) {
                                $('#v_emailR').show();
                                isStepValid = false;
                                return false;
                            } else {
                                $('#v_emailR').hide();
                            }
                        }
                    });
                }
            } else {
                $('#validCorreo').show();
                isStepValid = false;
            }
            if ($('#celular').val() != '') {
                var regex = RegExp("^9{1}[0-9]{8,8}");
                if (regex.test($('#celular').val())) {
                    console.log(regex.test($('#celular').val()));
                    $('#validCel').hide();
                } else {
                    isStepValid = false;
                    $('#validCel').show();
                    console.log(regex.test($('#celular').val()));
                }
            }
            if (isStepValid == true) {
                idE = $('#idEmpleado').val();
                if (idE == '') {
                    objEmpleado = datosPersona("POST");
                    enviarEmpleado('', objEmpleado);
                } else {
                    objEmpleado = datosPersona("POST");
                    enviarEmpleadoStore('/' + idE, objEmpleado);
                }
            }
        }
        if (stepnumber == 1) {
            if (isStepValid == true) {
                idE = $('#idEmpleado').val();
                objEmpleado = datosEmpresaEmpleado("POST");
                enviarEmpresarialEmpleado('/' + idE, objEmpleado);
            }
        }
        if (stepnumber == 2) {
            if (isStepValid == true) {
                idE = $('#idEmpleado').val();
                enviarFotoEmpleado('/' + idE);
            }
        }
        if (stepnumber == 3) {
            if (isStepValid == true) {
                idE = $('#idEmpleado').val();
                objEmpleado = datosCalendarioEmpleado("POST");
                enviarCalendarioEmpleado('/' + idE, objEmpleado);
            }
        }
        if (stepnumber == 4) {
            if (isStepValid == true) {
                idE = $('#idEmpleado').val();
                objEmpleado = datosHorarioEmpleado("POST");
                enviarHorarioEmpleado('/' + idE, objEmpleado);
            }
        }
        return isStepValid;

    }

    function validateSteps1(stepnumber) {
        var isStepValid = true;
        // validate step 1
        if (stepnumber == 0) {
            if ($('#v_apPaterno').val() == "") {
                isStepValid = false;
                $('#v_validApPaterno').show();
            } else {
                $('#v_validApPaterno').hide();
            }
            if ($('#v_numDocumento').val() == "") {
                isStepValid = false;
                $('#v_validNumDocumento').show();
            } else {
                $('#v_validNumDocumento').hide();
            }
            if ($('#v_apMaterno').val() == "") {
                isStepValid = false;
                $('#v_validApMaterno').show();
            } else {
                $('#v_validApMaterno').hide();
            }
            if ($('#v_nombres').val() == "") {
                isStepValid = false;
                $('#v_validNombres').show();
            } else {
                $('#v_validNombres').hide();
            }
            if ($("#v_email").val() != "") {
                //VALIDAR CORREO
                var email = $('#v_email').val();
                var idE = $('#v_id').val();
                $('#v_validCorreo').hide();
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "emailE",
                    data: {
                        email: email,
                        idE: idE
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        if (data == 1) {
                            $('#v_emailR').show();
                            isStepValid = false;
                            return false;
                        } else {
                            $('#v_emailR').hide();
                        }
                    }
                });
            } else {
                $('#v_validCorreo').show();
                isStepValid = false;
            }
            if ($('#v_celular').val() != '') {
                var regex = RegExp("^9{1}[0-9]{8,8}");
                if (regex.test($('#v_celular').val())) {
                    console.log(regex.test($('#v_celular').val()));
                    $('#v_validCel').hide();
                } else {
                    isStepValid = false;
                    $('#v_validCel').show();
                    console.log(regex.test($('#v_celular').val()));
                }
            }
            if ($("input[type=radio]:checked").length == 0) {
                isStepValid = false;
                $('#v_validGenero').show();
            } else {
                $('#v_validGenero').hide();
            }
        }
        if (isStepValid == true) {
            idE = $('#v_id').val();
            console.log($('#v_fechaFC').text());
            objEmpleadoA = datosPersonaA("PUT");
            actualizarEmpleado('/' + idE, objEmpleadoA);
        }
        return isStepValid;
    }
    $('#smartwizard').smartWizard({
        selected: 0,
        showStepURLhash: false,
        lang: { // Language variables
            next: 'Siguiente',
            previous: 'Anterior'
        },
        leaveStep: function () {
            return true;
        },
        justified: true,
        anchorSettings: {
            anchorClickable: true, // Enable/Disable anchor navigation
            enableAllAnchors: true,
            markDoneStep: true,
            enableAllAnchorOnDoneStep: true
        },
        toolbarSettings: {
            toolbarPosition: 'bottom', // none, top, bottom, both
            toolbarButtonPosition: 'right', // left, right, center
            toolbarExtraButtons: [
                $(`<button></button>`).text('Finalizar')
                .addClass('btn btn-secondary sw-btn-finish')
                .attr("id", "FinalizarEmpleado")
                .on('click', FinalizarEmpleado),
            ]
        },
    });
    $('#smartwizard1').smartWizard({
        selected: 0,
        showStepURLhash: false,
        lang: { // Language variables
            next: 'Siguiente',
            previous: 'Anterior'
        },
        justified: true,
        anchorSettings: {
            anchorClickable: true, // Enable/Disable anchor navigation
            enableAllAnchors: true,
            markDoneStep: true,
            enableAllAnchorOnDoneStep: true
        },
        leaveStep: function () {
            return true;
        },
        toolbarSettings: {
            toolbarPosition: 'bottom', // none, top, bottom, both
            toolbarButtonPosition: 'right', // left, right, center
            toolbarExtraButtons: [
                $(`<button></button>`).text('Finalizar')
                .addClass('btn btn-secondary sw-btn-finish')
                .attr("id", "FinalizarEmpleadoEditar")
                .on('click', function () {
                    leertabla();
                    $('#smartwizard').smartWizard("reset");
                    $('#navActualizar').hide();
                    $('input[type="file"]').val("");
                    $('input[typt="checkbox"]').val("");
                    $('#v_documento').val("").trigger("change");
                    $('#v_departamento').val("").trigger("change");
                    $('#v_dep').val("").trigger("change");
                    $('#v_prov').empty();
                    $('#v_provincia').empty();
                    $('#v_prov').append(`<option value="">Provincia</option>`);
                    $('#v_provincia').append(`<option value="">Provincia</option>`);
                    $('#v_dist').empty();
                    $('#v_distrito').empty();
                    $('#v_dist').append(`<option value="">Distrito</option>`);
                    $('#v_distrito').append(`<<option value="">Distrito</option>`);
                    $('#formNuevoEd').hide();
                    $('#formNuevoEl').hide();
                    $('#checkboxFechaIE').prop('checked', false);
                    $('#form-ver').modal('toggle');
                }),
            ]
        },
    });
    //************Validacion********//
    $('#smartwizard').on("leaveStep", function leaveAStepCallback(event, obj, indice) {
        return validateSteps(indice);
    });
    $('#smartwizard1').on("leaveStep", function leaveAStepCallback(event, obj, indice) {
        console.log(indice);
        return validateSteps1(indice);
    });
    $('#smartwizard').on("showStep", function (e, anchorObject, stepNumber, stepDirection) {
        console.log("ingreso");
        console.log(stepNumber);
        if (stepNumber == 5) {
            $('button.sw-btn-prev').hide();
            $('button.sw-btn-next').hide();
            $('#FinalizarEmpleado').show();
        }
    });
    $('#smartwizard1').on("showStep", function (e, anchorObject, stepNumber, stepDirection) {
        if (stepNumber == 0 || stepNumber == 1 || stepNumber == 2 || stepNumber == 3 || stepNumber == 4) {
            $('button.sw-btn-prev').show();
            $('button.sw-btn-next').show();
            $('#FinalizarEmpleadoEditar').hide();
        }
        if (stepNumber == 5) {
            $('button.sw-btn-prev').hide();
            $('button.sw-btn-next').hide();
            $('#FinalizarEmpleadoEditar').show();
        }
    })
});
$('#smartwizardVer').smartWizard({
    selected: 0,
    showStepURLhash: false,
    toolbarSettings: {
        showNextButton: false,
        showPreviousButton: false
    },
    justified: true,
    anchorSettings: {
        anchorClickable: true, // Enable/Disable anchor navigation
        enableAllAnchors: true,
        markDoneStep: true,
        enableAllAnchorOnDoneStep: true
    }

});
