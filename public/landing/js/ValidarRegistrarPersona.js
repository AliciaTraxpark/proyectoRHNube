function agregarempleado(){
    var dia=$('#dia_fecha').val();
    var mes=parseInt($('#mes_fecha').val());
    switch (mes) {
        case 1:
            $('#mesN').val('Enero');
          break;
        case 2:
            $('#mesN').val('Febrero');
          break;
        case 3:
            $('#mesN').val('Marzo');
          break;
        case 4:
            $('#mesN').val('Abril');
          break;
        case 5:
            $('#mesN').val('Mayo');
          break;
        case 6:
            $('#mesN').val('Junio');
          break;
        case  7:
            $('#mesN').val('Julio');
           break;
        case  8:
            $('#mesN').val('Agosto');
         break;
        case  9:
            $('#mesN').val('Setiembre');
           break;
        case  10:
            $('#mesN').val('Octubre');
           break;
        case  11:
            $('#mesN').val('Noviembre');
           break;
        case  12:
             $('#mesN').val('Diciembre');

      }
    var ano=$('#ano_fecha').val();
    $('#diaN').val(dia);

    $('#anoN').val(ano);
    $('#myModal').modal('toggle');
    //
 }
 function registerP() {
     var nombres= $('#nombres').val();
     var apPaterno=$('#apPaterno').val();
     var apMaterno=$('#apMaterno').val();
     var direccion=$('#direccion').val();
     var email=$('#email').val();
     var password=$('#password').val();
     var dia_fecha=$('#dia_fecha').val();
     var mes_fecha=$('#mes_fecha').val();
     var ano_fecha=$('#ano_fecha').val();
     var sexo=$('input:radio[name=sexo]:checked').val();

    $.ajax({
        type:"POST",
        url:"/persona/create",
        data:{nombres,apPaterno,apMaterno,direccion,email,password,dia_fecha,mes_fecha,ano_fecha,sexo},
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(user){
          window.location.replace(
            location.origin + "/registro/organizacion/" + user
          );
        }

    });

   }

