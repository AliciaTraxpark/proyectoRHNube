$( "#idempleado" ).change(function() {
    $('#imgV').hide();
    $('#btnLabo').hide();
    $('#calendar_ed').show();
    $('#calendar_ed_bt').show();
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
        defaultDate: ano + '-01-01',
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
                            url: "/empleado/eliminareventBD",
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
        events: function (info, successCallback, failureCallback) {
            var idcalendario = $('#selectCalendario_ed').val();
            var idempleado = $('#idempleado').val();
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
    title = 'Laborable';
    color = '#dfe6f2';
    textColor = '#0b1b29';
    start = $('#pruebaStar_ed').val();
    end = $('#pruebaEnd_ed').val();
    tipo = 3;
    var idempleado = $('#idempleado').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/empleado/storeCalendarioempleado",
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
        success: function (msg) {
            //var date = calendar1.getDate();
            //alert("The current date of the calendar is " + date.toISOString());

            calendarioedit.refetchEvents();


            console.log(msg);
        },
        error: function () {}
    });
};
/////////////
function nolaborable_ed() {
    $('#btnLabo').hide();
    title = 'No laborable';
    color = '#a34141';
    textColor = ' #ffffff ';
    start = $('#pruebaStar_ed').val();
    end = $('#pruebaEnd_ed').val();
    tipo = 0;
    var idempleado = $('#idempleado').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/empleado/storeCalendarioempleado",
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
        success: function (msg) {
            //var date = calendar1.getDate();
            //alert("The current date of the calendar is " + date.toISOString());

            calendarioedit.refetchEvents();

            console.log(msg);
        },
        error: function () {}
    });
};
