$(document).ready(function(){
    function validateSteps(stepnumber){
        var isStepValid = true;
        // validate step 1
        if(stepnumber == 0){
            console.log("validar primer paso");
            if($('#documento').val() == ""){
                isStepValid = false;
                $('#documento').addClass("is-invalid");
            }else{$('#documento').removeClass("is-invalid");}
            if($('#apPaterno').val() == ""){
                isStepValid = false;
                $('#apPaterno').addClass("is-invalid");
            }else{$('#apPaterno').removeClass("is-invalid");}
            if($('#departamento').val() == ""){
                isStepValid = false;
                $('#departamento').addClass("is-invalid");
            }else{$('#departamento').removeClass("is-invalid");}
            if($('#dep').val() == "" ){
                isStepValid = false;
                $('#dep').addClass("is-invalid");
            }else{$('#dep').removeClass("is-invalid");}
            if($('#numDocumento').val() == ""){
                isStepValid = false;
                $('#numDocumento').addClass("is-invalid");
            }else{$('#numDocumento').removeClass("is-invalid");}
            if($('#apMaterno').val() == ""){
                isStepValid = false;
                $('#apMaterno').addClass("is-invalid");
            }else{$('#apMaterno').removeClass("is-invalid");}
            if($('#provincia').val() == ""){
                isStepValid = false;
                $('#provincia').addClass("is-invalid");
            }else{$('#provincia').removeClass("is-invalid");}
            if($('#prov').val() == ""){
                isStepValid = false;
                $('#prov').addClass("is-invalid");
            }else{$('#prov').removeClass("is-invalid");}
            if($('#fechaN').val() == ""){
                isStepValid = false;
                $('#fechaN').addClass("is-invalid");
            }else{$('#fechaN').removeClass("is-invalid");}
            if($('#nombres').val() ==""){
                isStepValid = false;
                $('#nombres').addClass("is-invalid");
            }else{$('#nombres').removeClass("is-invalid");}
            if($('#distrito').val() == ""){
                isStepValid = false;
                $('#distrito').addClass("is-invalid");
            }else{
                $('#distrito').removeClass("is-invalid");}
            if($('#dist').val() == "" ){
                isStepValid = false;
                $('#dist').addClass("is-invalid");
            }else{$('#dist').removeClass("is-invalid");}
            if($('#direccion').val() == ""){
                isStepValid = false;
                $('#direccion').addClass("is-invalid");
            }else{$('#direccion').removeClass("is-invalid");}
            console.log(isStepValid)
        }
        if(stepnumber == 1){
            if($('#contrato').val() == ""){
                isStepValid = false;
                $('#contrato').addClass("is-invalid");
            }else{$('#contrato').removeClass("is-invalid");}
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
