$(document).ready(function () {
    function validateSteps(stepnumber) {
        var isStepValid = true;
        // validate step 1
        if (stepnumber == 0) {
            console.log("validar primer paso");
            if ($('#documento').val() == "") {
                isStepValid = false;
                $('#validDocumento').show();
            } else {
                //VALIDAR NUMERO DOCUMENTO
                var numeroD = $('#documento').val();
                $.ajax({
                    type: "GET",
                    url: "numDoc",
                    data: {
                        numeroD: numeroD
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            if ($('#fechaN').val() == "") {
                isStepValid = false;
                $('#validFechaN').show();
            } else {
                $('#validFechaN').hide();
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
            if ($('#v_fechaN').val() == "") {
                isStepValid = false;
                $('#v_validFechaN').show();
            } else {
                $('#v_validFechaN').hide();
            }
            console.log(isStepValid)
        }
        return isStepValid;
    }
    $('#smartwizard').smartWizard({
        selected: 0,
        showStepURLhash: false,
        toolbarSettings: {
            showNextButton: false,
            showPreviousButton: false
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
        },
        leaveStep: function () {
            alert("aaa");
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
