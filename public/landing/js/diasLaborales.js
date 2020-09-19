$( "#idempleado" ).change(function() {
    num=$('#idempleado').val().length;
    console.log(num);
    $('#imgV').hide();
    $('#btnLabo').hide();
    $('#calendar_ed').show();
    $('#calendar_ed_bt').show();
    $('#verinfo').show();
    calendario_edit();
  });




function calendario_edit() {
    var calendarEl = document.getElementById('calendar_ed');
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        defaultDate: fecha,
        height: 460,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],

        selectable: true,
        selectMirror: true,
        select: function (arg) {
            $('#pruebaEnd_ed').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
            $('#pruebaStar_ed').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
            console.log(arg);
            $('#btnLabo').show();
        },
        eventClick: function (info) {
            id = info.event.id;
            console.log(info);
            console.log(info.event.id);
            console.log(info.event.title);
            console.log(info.event.textColor);
            var event = calendarioedit.getEventById(id);
           if(info.event.textColor=='111111' || info.event.textColor=='1' || info.event.textColor=='0'){

            } else {
               bootbox.confirm({
                message: "¿Desea eliminar: " + info.event.title + " del horario?",
                buttons: {
                    confirm: {
                        label: 'Aceptar',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'Cancelar',
                        className: 'btn-light'
                    }
                },
                callback: function (result) {
                    if (result == true) {
                        $.ajax({
                            type: "post",
                            url: "/dias/delete",
                            data: {
                                ideve: info.event.id
                            },
                            statusCode: {

                                419: function () {
                                    location.reload();
                                }
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (data) {
                                info.event.remove();

                            },
                            error: function (data) {
                                alert('Ocurrio un error');
                            }


                        });
                    }
                }
            });
            }


        },
        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        eventRender: function(info) {
            /* $(info.el).tooltip({  title: info.event.extendedProps.horaI+'-'+info.event.extendedProps.horaF}); */
            if(info.event.extendedProps.horaI===null){
                $(info.el).tooltip({  title: info.event.title});
           } else{
            if(info.event.borderColor=='#5369f8'){
                $(info.el).tooltip({  title: info.event.extendedProps.horaI+'-'+info.event.extendedProps.horaF+'  Trabaja fuera de horario'});

            }
                else{
                    $(info.el).tooltip({  title: info.event.extendedProps.horaI+'-'+info.event.extendedProps.horaF});
               }
           }

        },
        events: function (info, successCallback, failureCallback) {
            var idcalendario = $('#selectCalendario_ed').val();
            var idempleado = $('#idempleado').val();
            num=$('#idempleado').val().length;
            console.log(num);
            if(num==1){
                $.ajax({
                    type: "POST",
                    url: "/empleado/calendarioEmpleado",
                    data: {
                        idcalendario,
                        idempleado
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        }
                    },
                    success: function (data) {

                        successCallback(data);

                    },
                    error: function () {}
                });
            }
            else{
                successCallback([{}]);
            }



        },

        /*  events: "calendario/show", */

    }
    calendarioedit = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendarioedit.setOption('locale', "Es");

    calendarioedit.render();
}
document.addEventListener('DOMContentLoaded', calendario_edit); ///////////

///////////////////////////
////////////////////////
function laborable_ed() {
    $('#btnLabo').hide();
    title = 'Descanso';
    color = '#4673a0';
    textColor = '#ffffff';
    start = $('#pruebaStar_ed').val();
    end = $('#pruebaEnd_ed').val();
    tipo = 3;
    var idempleado = $('#idempleado').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/dias/storeCalendario",
        data: {
            title,
            color,
            textColor,
            start,
            end,
            tipo,
            idempleado
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            num=$('#idempleado').val().length;
            console.log(num);
            if(num==1){

            calendarioedit.refetchEvents();}
            else{

             calendarioedit.addEvent({
                 id:
                 data,
                title: 'Laborable',
                start: $('#pruebaStar_ed').val(),
                end: $('#pruebaEnd_ed').val(),
                color : '#dfe6f2',
                textColor:'#0b1b29'

              });

            }



        },
        error: function () {}
    });
};
/////////////
function nolaborable_ed() {
    $('#btnLabo').hide();
    title = 'No laborable';
    color = '#a34141';
    textColor = '#ffffff';
    start = $('#pruebaStar_ed').val();
    end = $('#pruebaEnd_ed').val();
    tipo = 0;
    var idempleado = $('#idempleado').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/dias/storeCalendario",
        data: {
            title,
            color,
            textColor,
            start,
            end,
            tipo,
            idempleado
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            //var date = calendar1.getDate();
            //alert("The current date of the calendar is " + date.toISOString());

            num=$('#idempleado').val().length;
            console.log(num);
            if(num==1){

            calendarioedit.refetchEvents();}
            else{

             calendarioedit.addEvent({
                 id:
                 data,
                title: 'No laborable',
                start: $('#pruebaStar_ed').val(),
                end: $('#pruebaEnd_ed').val(),
                color : '#a34141',
                textColor:'#ffffff'

              });

            }


        },
        error: function () {}
    });
};
///////////////////////////////
$('#horaIncidenCa_ed').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
//////////////////7
function IncidenciaEmpleados(){
    $('#btnLabo').hide();
    $("#frmIncidenciaCa_ed")[0].reset();
    $('#modalIncidencia_ed').modal('show');
}
function modalIncidencia_ed() {
    var idempleado = $('#idempleado').val();
    descripcionI = $('#descripcionInciCa_ed').val();
    var descuentoI;
    if ($('#descuentoCheckCa_ed').prop('checked')) {
        descuentoI = 1;
    } else {
        descuentoI = 0
    }
    fechaI = $('#pruebaStar_ed').val();
    fechaFin = $('#pruebaEnd_ed').val();
    horaIn = $('#horaIncidenCa_ed').val();

    $.ajax({
        type: "post",
        url: "/dias/diasIncidempleado",
        data: {
            start: fechaI,
            title: descripcionI,
            descuentoI: descuentoI,
            end: fechaFin,
            horaIn,
            idempleado

        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            num=$('#idempleado').val().length;
            console.log(num);
            if(num==1){

            calendarioedit.refetchEvents();}
            else{

             calendarioedit.addEvent({
                 id:
                 data.inciden_id,
                title: data.inciden_descripcion,
                start: $('#pruebaStar_ed').val(),
                end: $('#pruebaEnd_ed').val(),
                color : '#d1c3c3',
                textColor:'#000000'

              });

            }
            $('#modalIncidencia_ed').modal('hide');

        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });
}
/////////////////////////////////////////
////////////////////////////////////////
//selct all area
$("#selectAreaCheck").click(function () {
    if ($("#selectAreaCheck").is(":checked")) {
        $("#selectArea > option").prop("selected", "selected");
        $("#selectArea").trigger("change");
    } else {
        $("#selectArea > option").prop("selected", false);
        $("#selectArea").trigger("change");
    }
});

///////////////seleccionar empleado por area
$("#selectArea").change(function (e) {
    var idempresarial = [];
    idempresarial = $("#selectArea").val();
    textSelec = $('select[name="selectArea"] option:selected:last').text();
    textSelec2 = $('select[name="selectArea"] option:selected:last').text();

    palabraEmpresarial = textSelec.split(" ")[0];
    if (palabraEmpresarial == "Area") {
        $.ajax({
            type: "post",
            url: "/empleAreaIn",
            data: {
                idarea: idempresarial,
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
                $("#idempleado > option").prop("selected", false);
                $("#idempleado").trigger("change");
                $.each(data, function (index, value) {
                    $.each(value, function (index, value1) {
                        $(
                            "#idempleado > option[value='" +
                                value1.emple_id +
                                "']"
                        ).prop("selected", "selected");
                        $("#idempleado").trigger("change");
                    });
                });
                console.log(data);
            },
            error: function (data) {
                alert("Ocurrio un error");
            },
        });
    }
});
