

 $( document ).ready(function() {
    $('#calendar .fc-Descanso-button').prop('disabled', true);
    $('#calendar .fc-NoLaborales-button').prop('disabled', true);
    $('#calendar .fc-Feriado-button').prop('disabled', true);
    $("#calendar .fc-left").on("click",myFuncion);


    function myFuncion(){
        $('#calendar .fc-Descanso-button').prop('disabled', true);
        $('#calendar .fc-NoLaborales-button').prop('disabled', true);
        $('#calendar .fc-Feriado-button').prop('disabled', true);
        $("#calendar .fc-left").on("click",myFuncion);
    }


});


function calendario() {
    var calendarEl = document.getElementById('calendar');
    calendarEl.innerHTML="";

    var fecha = new Date();
    var ano = fecha. getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        defaultDate: ano+'-01-01',
         height: 550,
         fixedWeekCount:false,
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
          $('#calendar .fc-Descanso-button').prop('disabled', false);
          $('#calendar .fc-NoLaborales-button').prop('disabled', false);
          $('#calendar .fc-Feriado-button').prop('disabled', false);
          $('#pruebaEnd').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
          $('#pruebaStar').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
        console.log(arg);
      },
      eventClick:function(info){
        id = info.event.id;
        console.log(info);
        console.log(info.event.id);
        console.log(info.event.title);
        console.log(info.event.textColor);
        if(info.event.title == 'Descanso'){
          $('#myModalEliminarD').modal();
        }
        if(info.event.title == 'No laborable'){
          $('#myModalEliminarN').modal();
        }
        if(info.event.textColor=='#775555'){
            $('#myModalEliminarFeriado').modal();
        }
      },
      editable: false,
      eventLimit: true,
        header:{
          left:'prev,next today',
          center:'title',
          right:''
        },
        footer:{
          left:'Descanso',
          center:'Feriado',
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
          Feriado:{
            text:"Asignar dia Feriado",
            click:function(){
                var start=  $('#pruebaStar').val();
                var end=  $('#pruebaEnd').val();
                $('#startFeriado').val(start);
                $('#endFeriado').val(end);
                $('#nombreFeriado').val('');
                $('#myModalFeriado').modal('toggle');
                $('#calendar .fc-Descanso-button').prop('disabled', true);
                $('#calendar .fc-NoLaborales-button').prop('disabled', true);
                $('#calendar .fc-Feriado-button').prop('disabled', true);
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
                $('#calendar .fc-Descanso-button').prop('disabled', true);
                $('#calendar .fc-NoLaborales-button').prop('disabled', true);
                $('#calendar .fc-Feriado-button').prop('disabled', true);

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
                //var date = calendar1.getDate();
                //alert("The current date of the calendar is " + date.toISOString());
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
                   var mesAg= $('#fechaDa').val();
                    var d  =mesAg;
                    var fechas=new Date(d);
                 console.log(fechas);
                  calendario1(data,fechas);
                  $('#calendar1 .fc-Descanso-button').prop('disabled', true);
                  $('#calendar1 .fc-NoLaborales-button').prop('disabled', true);
                  $('#calendar1 .fc-Feriado-button').prop('disabled', true);
                  $("#calendar1 .fc-left").on("click",myFuncion1);
                  function myFuncion1(){
                  $('#calendar1 .fc-Descanso-button').prop('disabled', true);
                  $('#calendar1 .fc-NoLaborales-button').prop('disabled', true);
                  $('#calendar1 .fc-Feriado-button').prop('disabled', true);
                  $("#calendar1 .fc-left").on("click",myFuncion1);
                  }

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
            error:function(){ alert("No puede eliminar dia feriado");}
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

    $('#eliminarDiaferi').click(function(){
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
                    var mesAg= $('#fechaDa2').val();
                    var d  =mesAg;
                    var fechas=new Date(d);
                 console.log(fechas);
                  calendario1(data,fechas);
                $('#calendar1 .fc-Descanso-button').prop('disabled', true);
                $('#calendar1 .fc-NoLaborales-button').prop('disabled', true);
                $('#calendar1 .fc-Feriado-button').prop('disabled', true);
                $("#calendar1 .fc-left").on("click",myFuncion1);
                function myFuncion1(){
                $('#calendar1 .fc-Descanso-button').prop('disabled', true);
                $('#calendar1 .fc-NoLaborales-button').prop('disabled', true);
                $('#calendar1 .fc-Feriado-button').prop('disabled', true);
                $("#calendar1 .fc-left").on("click",myFuncion1);
                }

                  },
                error:function(){ alert("Hay un error");}
                }
            );
              console.log(msg); },
            error:function(){ alert("Hay un error");}
            }
        );
    }
    //

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
            $('#myModalEliminarN').modal('hide');
            $('#myModalEliminarFeriado').modal('hide');
            calendar.refetchEvents();
            console.log(msg); },
          error:function(){ alert("No puede eliminar dia ");}
          }
      );
  }
    ////

    $('#guardarFeriado').click(function(){
        nombre=$('#nombreFeriado').val();

     if(nombre ==''){
            alert('Dia feriado vacio');
            return false;
        }
        objEventoFer=datosFeri("POST");
        EnviarFeriado('',objEventoFer);
      });


      function datosFeri(method){
        nuevoEvento1={
          title: $('#nombreFeriado').val(),
          color:'#e6bdbd',
          textColor:'#775555',
          start: $('#startFeriado').val(),
          end: $('#endFeriado').val(),
          tipo: 2,
          pais:$('#pais').val(),
          departamento:$('#departamento').val(),

          '_method':method
        }
        return(nuevoEvento1);
    }
    function EnviarFeriado(accion,objEventoFer){
        var departamento =$('#departamento').val();
        var pais =$('#pais').val();
        $.ajax(
            {
            type: "POST",
            url:"/eventos_usuario/store" +accion,
            data:objEventoFer,
            headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
            success:function(msg){
            $('#myModalFeriado').modal('toggle');
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
                   var mesAg= $('#fechaDa').val();
                    var d  =mesAg;
                    var fechas=new Date(d);
                 console.log(fechas);
                  calendario1(data,fechas);
                  $('#calendar1 .fc-Descanso-button').prop('disabled', true);
                  $('#calendar1 .fc-NoLaborales-button').prop('disabled', true);
                  $('#calendar1 .fc-Feriado-button').prop('disabled', true);
                  $("#calendar1 .fc-left").on("click",myFuncion1);
                  function myFuncion1(){
                  $('#calendar1 .fc-Descanso-button').prop('disabled', true);
                  $('#calendar1 .fc-NoLaborales-button').prop('disabled', true);
                  $('#calendar1 .fc-Feriado-button').prop('disabled', true);
                  $("#calendar1 .fc-left").on("click",myFuncion1);
                  }

                  },
                error:function(){ alert("Hay un error");}
                }
            );
            },
            error:function(){ alert("Hay un error");}
            }
        );
    }


    calendar.render();
}
document.addEventListener('DOMContentLoaded',calendario);

//////////////////
//////////////////////
//SEGUNDO CALENDARIO
//////////////////////
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
        $( ".hopscotch-bubble-arrow-border" ).remove();
        $( ".hopscotch-bubble-container" ).remove();

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
         var fecha = new Date();
         var ano = fecha. getFullYear();
         fechas1=ano+'-01-02';
         var fechas=new Date(fechas1);

         calendario1(data,fechas);
         $('#calendar1 .fc-Descanso-button').prop('disabled', true);
         $('#calendar1 .fc-NoLaborales-button').prop('disabled', true);
         $('#calendar1 .fc-Feriado-button').prop('disabled', true);
         $("#calendar1 .fc-left").on("click",myFuncion1);
         function myFuncion1(){
            $('#calendar1 .fc-Descanso-button').prop('disabled', true);
            $('#calendar1 .fc-NoLaborales-button').prop('disabled', true);
            $('#calendar1 .fc-Feriado-button').prop('disabled', true);
            $("#calendar1 .fc-left").on("click",myFuncion1);
        }

         },
       error:function(){ alert("Hay un error");}
       }
   );
 });

 function calendario1(data,fechas) {
     var calendarEl1 = document.getElementById('calendar1');
     calendarEl1.innerHTML="";
     var fecha = new Date();
     var ano = fecha. getFullYear();
     var id1;
     var data=data;
     var fechas=fechas;

     var configuracionCalendario1 = {
         locale: 'es',
         defaultDate: fechas,

         plugins: [ 'dayGrid','interaction','timeGrid'],
         height: 550,
         fixedWeekCount:false,
         selectable: true,
         selectMirror: true,
         select: function(arg) {


          /*  calendar.addEvent({
             title: 'title',
             start: arg.start,
             end: arg.end,
             allDay: arg.allDay
           }) */
           $('#calendar1 .fc-Descanso-button').prop('disabled', false);
           $('#calendar1 .fc-NoLaborales-button').prop('disabled', false);
           $('#calendar1 .fc-Feriado-button').prop('disabled', false);
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
          }
          if(info.event.title == 'No laborable'){
            $('#myModalEliminarNdep').modal();
          }
          if(info.event.textColor=='#775555'){
              $('#myModalEliminarFeriadoDep').modal();
          }
       },
       editable: false,
       eventLimit: true,
         header:{
           left:'prev,next today',
           center:'title',
           right:''
         },
         footer:{
           left:'Descanso',
           right:'NoLaborales',
           center:'Feriado'
         },


         events:data,



         customButtons:{
           Descanso:{
             text:"Asignar días de Descanso",
             click:function(){
                 var start=  $('#pruebaStar').val();
                 var end=  $('#pruebaEnd').val();
                 var date1 = calendar1.getDate();
                 $('#start').val(start);
                 $('#end').val(end);
                 $('#fechaDa').val(date1);
                 $('#myModal').modal('toggle');
             }
           },
           Feriado:{
            text:"Asignar dia Feriado",
            click:function(){
                var start=  $('#pruebaStar').val();
                var end=  $('#pruebaEnd').val();
                var date1 = calendar1.getDate();
                $('#startFeriado').val(start);
                $('#endFeriado').val(end);
                $('#fechaDa').val(date1);
                $('#nombreFeriado').val('');
                $('#myModalFeriado').modal('toggle');

            }
          },
           NoLaborales:{
             text:"Asignar días no Laborales",
             click:function(){
                 var start=  $('#pruebaStar').val();
                 var end=  $('#pruebaEnd').val();
                 var date2 = calendar1.getDate();
                 $('#startF').val(start);
                 $('#endF').val(end);
                 $('#fechaDa2').val(date2);
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
             $('#myModalEliminarNdep').modal('hide');
            var event = calendar1.getEventById(id1);
            event.remove();


             },

             }
         );
      }
      $('#eliminarDiaferiDep').click(function(){
        objEvento3=datos1("DELETE");
        EliminarFer('/'+id1,objEvento3);
      });
      function EliminarFer(accion3,objEvento3){

        $.ajax(
            {
            type: "DELETE",
            url:"/calendario" +accion3,
            data:objEvento3,
            headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
            success:function(msg){

            $('#myModalEliminarFeriadoDep').modal('hide');
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
