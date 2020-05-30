function agregarempleado(){
    var dia=$('#dia_fecha').val();
    var mes=$('#mes_fecha').val();

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
