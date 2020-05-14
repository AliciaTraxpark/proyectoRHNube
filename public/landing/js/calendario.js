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
        $('#myModalEliminar').modal();
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
    });
    function datos(method){
        nuevoEvento={
          title: $('#title').val(),
          color:'#4673a0',
          textColor:' #ffffff ',
          start: $('#start').val(),
          end: $('#end').val(),
          tipo: 1,
          '_method':method
        }
        return(nuevoEvento);
    }
    function EnviarDescanso(accion,objEvento){
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
              $('#myModalEliminar').modal('toggle');
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
    function datos1(method){
        nuevoEvento1={
          title: $('#titleN').val(),
          color:'#a34141',
          textColor:' #ffffff ',
          start: $('#startF').val(),
          end: $('#endF').val(),
          tipo: 0,

          '_method':method
        }
        return(nuevoEvento1);
    }
    function EnviarNoL(accion,objEvento1){
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
              calendar.addEvent(nuevoEvento1);
              console.log(msg); },
            error:function(){ alert("Hay un error");}
            }
        );
    }
    ////
    calendar.render();
}
document.addEventListener('DOMContentLoaded',calendario);

$('#nuevoCalendario').click(function(){
    var departamento= $('#departamento').val();
    $.ajax(
      {
      type: "POST",
      url:"/calendario/store",
      data:'departamento='+departamento,
      headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
},
      success:function(data){
        $('#calendar').load(location.href+" #calendar>*");
        calendar.render();
        },
      error:function(){ alert("Hay un error");}
      }
  );
});
