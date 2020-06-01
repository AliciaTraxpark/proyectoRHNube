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
            if($('#prov').val() == ""){
                isStepValid = false;
                $('#prov').addClass("is-invalid");
            }else{$('#prov').removeClass("is-invalid");}
            if($('#fechaN').val() == ""){
                isStepValid = false;
                $('.day').addClass("is-invalid");
                $('.month').addClass("is-invalid");
                $('.year').addClass("is-invalid");
            }else{
                $('.day').removeClass("is-invalid");
                $('.month').removeClass("is-invalid");
                $('.year').removeClass("is-invalid");
            }
            if($('#nombres').val() ==""){
                isStepValid = false;
                $('#nombres').addClass("is-invalid");
            }else{$('#nombres').removeClass("is-invalid");}
            if($('#dist').val() == "" ){
                isStepValid = false;
                $('#dist').addClass("is-invalid");
            }else{$('#dist').removeClass("is-invalid");}
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
    function validateSteps1(stepnumber){
        var isStepValid = true;
        // validate step 1
        if(stepnumber == 0){
            console.log("validar primer paso");
            if($('#v_apPaterno').val() == ""){
                isStepValid = false;
                $('#v_apPaterno').addClass("is-invalid");
            }else{$('#v_apPaterno').removeClass("is-invalid");}
            if($('#v_dep').val() == "" ){
                isStepValid = false;
                $('#v_dep').addClass("is-invalid");
            }else{$('#v_dep').removeClass("is-invalid");}
            if($('#v_numDocumento').val() == ""){
                isStepValid = false;
                $('#v_numDocumento').addClass("is-invalid");
            }else{$('#v_numDocumento').removeClass("is-invalid");}
            if($('#v_apMaterno').val() == ""){
                isStepValid = false;
                $('#v_apMaterno').addClass("is-invalid");
            }else{$('#v_apMaterno').removeClass("is-invalid");}
            if($('#v_prov').val() == ""){
                isStepValid = false;
                $('#v_prov').addClass("is-invalid");
            }else{$('#v_prov').removeClass("is-invalid");}
            if($('#v_nombres').val() ==""){
                isStepValid = false;
                $('#v_nombres').addClass("is-invalid");
            }else{$('#v_nombres').removeClass("is-invalid");}
            if($('#v_dist').val() == "" ){
                isStepValid = false;
                $('#v_dist').addClass("is-invalid");
            }else{$('#v_dist').removeClass("is-invalid");}
            console.log(isStepValid)
        }
        if(stepnumber == 1){
            if($('#v_contrato').val() == ""){
                isStepValid = false;
                $('#v_contrato').addClass("is-invalid");
            }else{$('#v_contrato').removeClass("is-invalid");}
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
        leaveStep:function(){
            alert("aaa");
            return true;
        }
    });
    //************Validacion********//
    $('#smartwizard').on("leaveStep",function leaveAStepCallback(event,obj, indice){
        console.log(indice);
        return validateSteps(indice);
    });
    $('#smartwizard1').on("leaveStep",function leaveAStepCallback(event,obj, indice){
        console.log(indice);
        return validateSteps1(indice);
    });
    $('#smartwizard').on("showStep",function(e,anchorObject,stepNumber,stepDirection){
        if($('button.sw-btn-next').hasClass('disabled')){
            $('button.sw-btn-next').hide();
            $('button.sw-btn-prev').hide();
        }
        else{
            $('button.sw-btn-prev').show();
            $('button.sw-btn-next').show();
        }
    })          
  });
