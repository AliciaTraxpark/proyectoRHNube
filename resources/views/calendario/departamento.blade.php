$( document ).ready(function() {
    $('#Datoscalendar1').hide();
 });
 $('#nuevoCalendario').click(function(){
     var departamento= $('#departamento').val();
     var pais= $('#pais').val();


     $.ajax(
       {

       //url:"/calendario/store",
       url:"/calendario/showDep/",
       data:{departamento:departamento,pais:pais},
       headers: {
     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 },
       success:function(data){
         $('#Datoscalendar').hide();
         $('#Datoscalendar1').show();

         $.ajax(
             {

             //url:"/calendario/store",
             url:"/calendario/showDep/confirmar",
             data:{departamento:departamento,pais:pais},
             headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
             success:function(dataA){
                if (dataA==1)
                {
                    alert('ya esta creado');
                }

                 },
             error:function(){ alert("Hay un error");}
             }
         );
         calendario1(data);

         },
       error:function(){ alert("Hay un error");}
       }
   );
 });

 function calendario1(data) {
     var calendarEl1 = document.getElementById('calendar1');
     calendarEl1.innerHTML="";
     var fecha = new Date();
     var ano = fecha. getFullYear();
     var id1;
     var data=data;

     var configuracionCalendario1 = {
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
         id1 = info.event.id;
         console.log(info);
         console.log(info.event.id);
         console.log(info.event.title);
         if(info.event.title == 'Descanso'){
           $('#myModalEliminarDdep').modal();
         }else{
           $('#myModalEliminarNdep').modal();
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


         events:data,



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
     var calendar1 = new FullCalendar.Calendar(calendarEl1,configuracionCalendario1);
     calendar1.setOption('locale',"Es");
      //DESCANSO

      $('#eliminarDescansodep').click(function(){
       objEvento1=datos1("DELETE");
       EliminarDescansoE('/'+id1,objEvento1);
     });
     function datos1(method){
         nuevoEvento1={
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
         return(nuevoEvento1);
     }
     function EliminarDescansoE(accion1,objEvento1){

         $.ajax(
             {
             type: "DELETE",
             url:"/calendario" +accion1,
             data:objEvento1,
             headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
             success:function(msg){
             $('#myModalEliminarDdep').modal('toggle');

            var event = calendar1.getEventById(id1);
            event.remove();


             },

             }
         );
      }

     ///
     //NO LABORABLE

     $('#eliminarNLaboraldep').click(function(){
       objEvento2=datos1("DELETE");
       EliminarNola('/'+id1,objEvento2);
     });

     function EliminarNola(accion2,objEvento2){

         $.ajax(
             {
             type: "DELETE",
             url:"/calendario" +accion2,
             data:objEvento2,
             headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
             success:function(msg){
             $('#myModalEliminarNdep').modal('toggle');

            var event = calendar1.getEventById(id1);
            event.remove();


             },

             }
         );
      }


     ////
     calendar1.render();
 }
 document.addEventListener('DOMContentLoaded',calendario1);
