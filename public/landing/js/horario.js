$('#horaI').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
$('#horaF').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
$('#btnasignar').on('click', function(e) {
    $('#nombreEmpleado').load(location.href+" #nombreEmpleado>*");
    var allVals = [];
    $(".sub_chk:checked").each(function() {
        allVals.push($(this).attr('data-id'));
    });
    $('#asignarHorario').modal('toggle');
    if(allVals.length<=0){
        $.ajax({
            type:"post",
            url:"/horarioVerTodEmp",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {
                json1 = JSON.parse(JSON.stringify(data));

                for (var i in json1) {
                    //allVals4.push(json[i].perso_nombre+" "+json[i].perso_apPaterno);
                    $('#nombreEmpleado').append('<option value="'+json1[i].emple_id+'" >'+json1[i].perso_nombre+" "+json1[i].perso_apPaterno+'</option>');

                }
            },
            error: function (data) {
                alert('Ocurrio un error');
            }
        });

    }else{
        var idsempleados = allVals.join(",");
        $.ajax({
            type:"post",
            url:"/horarioVerEmp",
            data:'ids='+idsempleados,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {
            /*   UNO X UNO MUESTRA  $.each(data,function(i,json){
                    array[json.emple_nDoc]=(parseInt(json.emple_nDoc));
                    alert(json.emple_nDoc);
                  }); */
                  var allVals4 = [];
                  json = JSON.parse(JSON.stringify(data));

                for (var i in json) {
                    //allVals4.push(json[i].perso_nombre+" "+json[i].perso_apPaterno);
                    $('#nombreEmpleado').append('<option value="'+json[i].emple_id+'" selected="selected">'+json[i].perso_nombre+" "+json[i].perso_apPaterno+'</option>');

                }

                console.log(allVals4);
                //$('#nombreEmpleado').val(allVals4);
            },
            error: function (data) {
                alert('Ocurrio un error');
            }

        });
        }

});
//CALENDARIO//
 function calendario() {
    var calendarEl = document.getElementById('calendar');
    calendarEl.innerHTML="";

    var fecha = new Date();
    var ano = fecha. getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        defaultDate: ano+'-01-01',
         height:  "auto",
         contentHeight: 410,
         fixedWeekCount:false,
        plugins: [ 'dayGrid','interaction','timeGrid'],

        selectable: true,
        selectMirror: true,
        select: function(arg) {
        console.log(arg);
      },
      eventClick:function(info){
        id = info.event.id;
        console.log(info);
        console.log(info.event.id);
        console.log(info.event.title);
      },
      editable: false,
      eventLimit: true,
        header:{
          left:'prev,next today',
          center:'title',
          right:''
        },

        events:"calendario/show",
      }
    var calendar = new FullCalendar.Calendar(calendarEl,configuracionCalendario);
    calendar.setOption('locale',"Es");
    ////
    calendar.render();

}
document.addEventListener('DOMContentLoaded',calendario);

///////////////////////////////
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
         var fecha = new Date();
         var ano = fecha. getFullYear();
         fechas1=ano+'-01-02';
         var fechas=new Date(fechas1);

         calendario1(data,fechas);
         $('#calendar1 .fc-Descanso-button').prop('disabled', true);
         $('#calendar1 .fc-NoLaborales-button').prop('disabled', true);
         $("#calendar1 .fc-left").on("click",myFuncion1);
         function myFuncion1(){
            $('#calendar1 .fc-Descanso-button').prop('disabled', true);
            $('#calendar1 .fc-NoLaborales-button').prop('disabled', true);
            $("#calendar1 .fc-left").on("click",myFuncion1);
        }

         },
       error:function(){ alert("Hay un error");}
       }
   );
 });
//SEGUNDO CALENDAR
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
        height:  "auto",
        contentHeight: 450,
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

        console.log(arg);
      },
      eventClick:function(info){

        id1 = info.event.id;
        console.log(info);
        console.log(info.event.id);
        console.log(info.event.title);

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
          right:'NoLaborales'
        },


        events:data,




      }
    var calendar1 = new FullCalendar.Calendar(calendarEl1,configuracionCalendario1);
    calendar1.setOption('locale',"Es");
     //DESCANSO

    calendar1.render();
}
document.addEventListener('DOMContentLoaded',calendario1);
