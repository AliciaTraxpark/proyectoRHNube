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
    var allVals = [];
    $(".sub_chk:checked").each(function() {
        allVals.push($(this).attr('data-id'));
    });

    if(allVals.length<=0)
    {
        /* alert("Selecciona al menos un empleado.");
        return false; */
    }  else {
        $('#asignarHorario').modal('toggle');

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
                    allVals4.push(json[i].perso_nombre+" "+json[i].perso_apPaterno);

                }
                var idsv = allVals4.join(", ");
                console.log(allVals4);
                $('#nombreEmpleado').val((allVals4));
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
         contentHeight: "auto",
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

