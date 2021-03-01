

    let valueBtn1 = "true", valueBtn2 = "true", valueBtn3 = "true", valueBtn4 = "true";
    
    $("#btnSelec1").on("click",function(){
        valueBtn1 = document.getElementById('btnSelec1').attributes["aria-pressed"].value;
        if(valueBtn1 == "false"){
            document.getElementById("btnSelec1").innerHTML  = "<img src='landing/images/check.svg' width='16'> Seleccionado";
            $('#cardSelect1').addClass('sombra');
        }
        else {
            document.getElementById("btnSelec1").textContent  = "Seleccionar";
            $('#cardSelect1').removeClass('sombra');
        }
    });
    $("#btnSelec2").on("click",function(){
        valueBtn2 = document.getElementById('btnSelec2').attributes["aria-pressed"].value;
        if(valueBtn2 == "false"){
            document.getElementById("btnSelec2").innerHTML  = "<img src='landing/images/check.svg' width='16'> Seleccionado";
            $('#cardSelect2').addClass('sombra');
        }
        else {
            document.getElementById("btnSelec2").textContent  = "Seleccionar";
            $('#cardSelect2').removeClass('sombra');
        }
    });
    $("#btnSelec3").on("click",function(){
        valueBtn3 = document.getElementById('btnSelec3').attributes["aria-pressed"].value;
        if(valueBtn3 == "false"){
            document.getElementById("btnSelec3").innerHTML  = "<img src='landing/images/check.svg' width='16'> Seleccionado";
            $('#cardSelect3').addClass('sombra');
        }
        else {
            document.getElementById("btnSelec3").textContent  = "Seleccionar";
            $('#cardSelect3').removeClass('sombra');
        }
    });
    $("#btnSelec4").on("click",function(){
        valueBtn4 = document.getElementById('btnSelec4').attributes["aria-pressed"].value;
        if(valueBtn4 == "false"){
            document.getElementById("btnSelec4").innerHTML  = "<img src='landing/images/check.svg' width='16'> Seleccionado";
            $('#cardSelect4').addClass('sombra');
        }
        else {
            document.getElementById("btnSelec4").textContent  = "Seleccionar";
            $('#cardSelect4').removeClass('sombra');
        }
    });

    $("#btnSelected").on("click",function(){
        valueBtn1 = document.getElementById('btnSelec1').attributes["aria-pressed"].value;
        valueBtn2 = document.getElementById('btnSelec2').attributes["aria-pressed"].value;
        valueBtn3 = document.getElementById('btnSelec3').attributes["aria-pressed"].value;
        valueBtn4 = document.getElementById('btnSelec4').attributes["aria-pressed"].value;
        console.log(valueBtn1+" "+valueBtn2+" "+valueBtn3+" "+valueBtn4);
        if(valueBtn1 == "true" || valueBtn2 == "true" || valueBtn3 == "true" || valueBtn4 == "true"){
            $.ajax({
                async: false,
                type: "POST",
                url: "/selectModos",
                data: {
                    valueBtn1, valueBtn2, valueBtn3, valueBtn4
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
                error: function () {},
            });
            $('#modos').modal('hide');
            location.reload();
        }
    });