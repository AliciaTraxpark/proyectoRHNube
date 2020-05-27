$(document).ready(function(){
    function validateSteps(stepnumber){
        var isStepValid = true;
        // validate step 1
        if(stepnumber == 0){
            console.log("validar primer paso");
            if($('#documento').val() == ""||$('#apPaterno').val() == "" || 
                $('#departamento').val() == "" || $('#dep').val() == "" ||
                $('#numDocumento').val() == "" || $('#apMaterno').val() == "" ||
                $('#provincia').val() == "" || $('#prov').val() == "" || $('#fechaN').val() == "" ||
                $('#nombres').val() == "" || $('#distrito').val() == "" || $('#dist').val() == "" ||
                $('#direccion').val() == ""){
                isStepValid = false;
            }
            console.log(isStepValid)
        }
        if(stepnumber == 1){
            if($('#contrato').val() == ""){
                isStepValid = false;
            }
        }
        return isStepValid;    
    }
    $('#smartwizard').smartWizard({
        selected: 0,
        showStepURLhash: false,
        lang: {  // Language variables
            next: 'Siguiente',
            previous: 'Anterior'
        },
        leaveStep:function(){
            alert("aaa");
            return true;
        }
    });
    $('#smartwizard1').smartWizard({
        selected: 0,
        showStepURLhash: false,
        lang: {  // Language variables
            next: 'Siguiente',
            previous: 'Anterior'
        },
    });
    //************Validacion********//
    $('#smartwizard').on("leaveStep",function leaveAStepCallback(event,obj, indice){
        console.log(indice);
        return validateSteps(indice);
    })         
  });
