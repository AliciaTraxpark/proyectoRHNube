$(document).ready(function () {
    function validateSteps(stepnumber) {
        var isStepValid = true;
        // validate step 1
        if (stepnumber == 0) {
            console.log("validar primer paso");
            if ($('#documento').val() == "") {
                isStepValid = false;
                $('#documento').addClass("is-invalid");
            } else {
                $('#documento').removeClass("is-invalid");
            }
            if ($('#apPaterno').val() == "") {
                isStepValid = false;
                $('#apPaterno').addClass("is-invalid");
            } else {
                $('#apPaterno').removeClass("is-invalid");
            }
            if ($('#numDocumento').val() == "") {
                isStepValid = false;
                $('#numDocumento').addClass("is-invalid");
            } else {
                $('#numDocumento').removeClass("is-invalid");
            }
            if ($('#apMaterno').val() == "") {
                isStepValid = false;
                $('#apMaterno').addClass("is-invalid");
            } else {
                $('#apMaterno').removeClass("is-invalid");
            }
            if ($('#fechaN').val() == "") {
                isStepValid = false;
                $('.day').addClass("is-invalid");
                $('.month').addClass("is-invalid");
                $('.year').addClass("is-invalid");
            } else {
                $('.day').removeClass("is-invalid");
                $('.month').removeClass("is-invalid");
                $('.year').removeClass("is-invalid");
            }
            if ($('#nombres').val() == "") {
                isStepValid = false;
                $('#nombres').addClass("is-invalid");
            } else {
                $('#nombres').removeClass("is-invalid");
            }
            if ($("input[type=radio]:checked").length == 0) {
                isStepValid = false;
            } else {

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
                $('#v_apPaterno').addClass("is-invalid");
            } else {
                $('#v_apPaterno').removeClass("is-invalid");
            }
            if ($('#v_numDocumento').val() == "") {
                isStepValid = false;
                $('#v_numDocumento').addClass("is-invalid");
            } else {
                $('#v_numDocumento').removeClass("is-invalid");
            }
            if ($('#v_apMaterno').val() == "") {
                isStepValid = false;
                $('#v_apMaterno').addClass("is-invalid");
            } else {
                $('#v_apMaterno').removeClass("is-invalid");
            }
            if ($('#v_nombres').val() == "") {
                isStepValid = false;
                $('#v_nombres').addClass("is-invalid");
            } else {
                $('#v_nombres').removeClass("is-invalid");
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
