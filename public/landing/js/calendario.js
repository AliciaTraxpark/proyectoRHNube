
function calendario() {
    var calendarEl = document.getElementById('calendar');
    calendarEl.innerHTML="";
    var fecha = new Date();
    var ano = fecha. getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        defaultDate: ano+'-01-01',

        plugins: [ 'dayGrid','interaction','timeGrid'],

        selectable: true,
        selectMirror: true,
        select: function(arg) {

         /*  calendar.addEvent({
            title: 'title',
            start: arg.start,
            end: arg.end,
            allDay: arg.allDay
          }) */

          $('#pruebaEnd').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
          $('#pruebaStar').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
        console.log(arg);
      },
      eventClick:function(info){
        id = info.event.id;
        console.log(info);
        console.log(info.event.id);
        console.log(info.event.title);
        if(info.event.title == 'Descanso'){
          $('#myModalEliminarD').modal();
        }else{
          $('#myModalEliminarN').modal();
        }
      },
      editable: false,
      eventLimit: true,
        header:{
          left:'prev,next today',
          center:'title',
          right:'dayGridMonth'
        },
        footer:{
          left:'Descanso',
          right:'NoLaborales'
        },

        events:"calendario/show",
        customButtons:{
          Descanso:{
            text:"Asignar días de Descanso",
            click:function(){
                var start=  $('#pruebaStar').val();
                var end=  $('#pruebaEnd').val();
                $('#start').val(start);
                $('#end').val(end);
                $('#myModal').modal('toggle');
            }
          },
          NoLaborales:{
            text:"Asignar días no Laborales",
            click:function(){
                var start=  $('#pruebaStar').val();
                var end=  $('#pruebaEnd').val();
                $('#startF').val(start);
                $('#endF').val(end);
                $('#myModalFestivo').modal('toggle');

            }
          }
        },
      }
    var calendar = new FullCalendar.Calendar(calendarEl,configuracionCalendario);
    calendar.setOption('locale',"Es");
     //DESCANSO
    $('#guardarDescanso').click(function(){
      objEvento=datos("POST");
      EnviarDescanso('',objEvento);
    });
    $('#eliminarDescanso').click(function(){
      objEvento=datos("DELETE");
      EnviarDescansoE('/'+id,objEvento);
      EnviarDescansoE('/'+id1,objEvento);
    });
    function datos(method){
        nuevoEvento={
          title: $('#title').val(),
          color:'#4673a0',
          textColor:' #ffffff ',
          start: $('#start').val(),
          end: $('#end').val(),
          tipo: 1,
         pais:$('#pais').val(),
          departamento:$('#departamento').val(),
          '_method':method
        }
        return(nuevoEvento);
    }
    function EnviarDescanso(accion,objEvento){
        var departamento =$('#departamento').val();
        var pais =$('#pais').val();
        $.ajax(
            {
            type: "POST",
            url:"/eventos_usuario/store" +accion,
            data:objEvento,
            headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
            success:function(msg){

              $('#myModal').modal('toggle');
              calendar.refetchEvents();
              $.ajax(
                {

                //url:"/calendario/store",
                url:"/calendario/showDep/",
                data:{departamento:departamento,pais:pais},
                headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
                success:function(data){


                  calendario1(data);

                  },
                error:function(){ alert("Hay un error");}
                }
            );
              console.log(msg); },
            error:function(){ alert("Hay un error");}
            }
        );
    }
    function EnviarDescansoE(accion,objEvento){

        $.ajax(
            {
            type: "DELETE",
            url:"/calendario" +accion,
            data:objEvento,
            headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
            success:function(msg){
              $('#myModalEliminarD').modal('toggle');
              calendar.refetchEvents();
              console.log(msg);
              },
            error:function(){ alert("Hay un error");}
            }
        );
    }
    ///
    //NO LABORABLE
    $('#guardarNoLab').click(function(){
      objEvento1=datos1("POST");
      EnviarNoL('',objEvento1);
    });
    $('#eliminarNLaboral').click(function(){
      objEvento1=datos1("DELETE");
      EnviarNoLE('/'+id,objEvento1);
    });
    function datos1(method){
        nuevoEvento1={
          title: $('#titleN').val(),
          color:'#a34141',
          textColor:' #ffffff ',
          start: $('#startF').val(),
          end: $('#endF').val(),
          tipo: 0,
          pais:$('#pais').val(),
          departamento:$('#departamento').val(),

          '_method':method
        }
        return(nuevoEvento1);
    }
    function EnviarNoL(accion,objEvento1){
        var departamento =$('#departamento').val();
        var pais =$('#pais').val();
        $.ajax(
            {
            type: "POST",
            url:"/eventos_usuario/store" +accion,
            data:objEvento1,
            headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
            success:function(msg){
              $('#myModalFestivo').modal('toggle');
              calendar.refetchEvents();
              $.ajax(
                {

                //url:"/calendario/store",
                url:"/calendario/showDep/",
                data:{departamento:departamento,pais:pais},
                headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
                success:function(data){


                  calendario1(data);

                  },
                error:function(){ alert("Hay un error");}
                }
            );
              console.log(msg); },
            error:function(){ alert("Hay un error");}
            }
        );
    }
    function EnviarNoLE(accion,objEvento1){
      $.ajax(
          {
          type: "DELETE",
          url:"/calendario" +accion,
          data:objEvento1,
          headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
          success:function(msg){
            $('#myModalEliminarN').modal('toggle');
            calendar.refetchEvents();
            console.log(msg); },
          error:function(){ alert("Hay un error");}
          }
      );
  }
    ////
    calendar.render();
}
document.addEventListener('DOMContentLoaded',calendario);

//////////////////
//////////////////////
//DEPARTAMENTO
//////////////////////

