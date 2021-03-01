    let valueBtn1 = "true", valueBtn2 = "true", valueBtn3 = "true", valueBtn4 = "true";
    $('#msj').hide();
    $("#btnSelec1").on("click",function(){
        valueBtn1 = document.getElementById('btnSelec1').attributes["aria-pressed"].value;
        if(valueBtn1 == "false"){
            document.getElementById("btnSelec1").innerHTML  = "<img src='landing/images/check.svg' width='16'> Seleccionado";
            $('#cardSelect1').addClass('sombra');
            $('#msj').hide();
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
            $('#msj').hide();
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
            $('#msj').hide();
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
            $('#msj').hide();
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
        
        var value1, value2, value3, value4;
        var cont = 0;

        if(valueBtn1 == "true")
            value1 = 1;
        else 
            value1 = 0;

        if(valueBtn2 == "true")
            value2 = 1;
        else 
            value2 = 0;

        if(valueBtn3 == "true")
            value3 = 1;
        else 
            value3 = 0;

        if(valueBtn4 == "true")
            value4 = 1;
        else 
            value4 = 0;

        $.ajax({
            async: false,
            type: "GET",
            url: "/modos",
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
                if(data.Mremoto == value1)
                    cont++;
                if(data.Mruta == value2)
                    cont++;
                if(data.Mpuerta == value3)
                    cont++;
                if(data.Mtareo == value4)
                    cont++;
            },
            error: function () {},
        });


        if(valueBtn1 == "true" || valueBtn2 == "true" || valueBtn3 == "true" || valueBtn4 == "true"){
            if (cont == 4) {
                $('#modos').modal('hide');
            } else {
                $('#modos').modal('hide');
                location.reload();
            }

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
            
        } else {
            $('#msj').show();
        }
    });