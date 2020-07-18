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
                $('#validCorreo').show();
                isStepValid = false;
            }
            console.log(isStepValid)
        }
        return isStepValid;

    }

    function validateSteps1(stepnumber) {
        var isStepValid = true;
        // validate step 1
        if (stepnumber == 0) {
            console.log("validar primer paso");
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
            console.log(isStepValid)
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
        }

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
        }
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
        if ($('button.sw-btn-next').hasClass('disabled')) {
            $('button.sw-btn-next').hide();
            $('button.sw-btn-prev').hide();
        } else {
            $('button.sw-btn-prev').show();
            $('button.sw-btn-next').show();
        }
    });
    $('#smartwizard1').on("showStep", function (e, anchorObject, stepNumber, stepDirection) {
        if ($('button.sw-btn-next').hasClass('disabled')) {
            $('button.sw-btn-next').hide();
            $('button.sw-btn-prev').hide();
        } else {
            $('button.sw-btn-prev').show();
            $('button.sw-btn-next').show();
        }
    })
});
