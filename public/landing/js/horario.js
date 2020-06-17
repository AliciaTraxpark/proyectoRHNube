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
         contentHeight: 430,
         fixedWeekCount:false,
        plugins: [ 'dayGrid','interaction','timeGrid'],

        selectable: true,
        selectMirror: true,
        select: function(arg) {
        console.log(arg);

        $('#horarioEnd').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
        $('#horarioStart').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
        f1= $('#horarioStart').val();
        f2= $('#horarioEnd').val();
        inicio=$('#horaI').val();
        fin=$('#horaF').val();
        if(inicio=='' || fin=='' ){
        alert('Indique hora de inicio y fin');}else{
            agregarHoras();
        }
        function agregarHoras() {
          //sacar dias entre fecha
          var diasEntreFechas = function(desde, hasta) {
            var dia_actual = desde;
          var fechas = [];
            while (dia_actual.isSameOrBefore(hasta)) {
              fechas.push(dia_actual.format('YYYY-MM-DD'));
                 dia_actual.add(1, 'days');
            }
            return fechas;
          };

         desde=moment(f1);
         hasta=moment(f2);
         var results = diasEntreFechas(desde, hasta);
        results.pop();
        //console.log(results);
        var fechasArray =[];
        var fechastart=[];

        var objeto = [

        ];
        $.each( results, function( key, value ) {
            //alert( value );
            fechasArray.push(inicio+'-'+fin
            );
            fechastart.push(value
            );


            objeto.push({"title": inicio+'-'+fin, "start":value});

            calendar.addEvent({
                title: inicio+'-'+fin,
                start: value,
                //end:f2,
                color:'#ffffff',
                textColor:' #000000',
                allDay: true
            })

                //

        });
        console.log(fechasArray);
        //alert(fechasArray);
        //alert(fechastart);
        $.ajax({
            type:"post",
            url:"/guardarEventos",
            data:{fechasArray:fechastart,hora:inicio+'-'+fin},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {

            },
            error: function (data) {
                alert('Ocurrio un error');
            }


        });



          };

          $('#guardarTodoHorario').click(function(){

            //alert(fechasArray);
        $.ajax({
            url:"/guardarEventos",
            data:{departamento:departamento,pais:pais},
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(data){

                },
            error:function(){ alert("Hay un error");}
            });
        })
      },
      eventClick:function(info){
        id = info.event.id;
        console.log(info);
        console.log(info.event.id);
        console.log(info.event.title);
        //var event = calendar.getEventById(id);
        info.event.remove();
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
    $('#Datoscalendar1').css("display","none");
    $('#aplicarHorario').prop('disabled', true);

    $ ('.flatpickr-input[readonly]'). on ('focus', function () {
        $ (this) .blur ()
        })
        $ ('.flatpickr-input[readonly]'). prop ('readonly', false)
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
//////////////////////


