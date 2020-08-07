$(document).ready(function () {



});


function calendario() {
    var calendarEl = document.getElementById('calendar');
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        defaultDate: fecha,
        height: 550,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],

        selectable: true,
        selectMirror: true,
        select: function (arg) {

            /*  calendar.addEvent({
               title: 'title',
               start: arg.start,
               end: arg.end,
               allDay: arg.allDay
             }) */

            $('#pruebaEnd').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
            $('#pruebaStar').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
            console.log(arg);
            $('#calendarioAsignar').modal('show');
        },
        eventClick: function (info) {
            id = info.event.id;
            console.log(info);
            console.log(info.event.id);
            console.log(info.event.title);
            console.log(info.event.textColor);
            if (info.event.title == 'Descanso') {
                $('#myModalEliminarD').modal();
                $('#idDescansoEl').val(id);

            }
            if (info.event.title == 'No laborable') {
                $('#myModalEliminarN').modal();
                $('#idnolabEliminar').val(id);

            }
            if (info.event.textColor == '#775555' || info.event.textColor=='#945353') {
                $('#myModalEliminarFeriado').modal();
                $('#idFeriadoeliminar').val(id);
            }
        },
        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        footer: {
            left: 'Descanso',
            center: 'Feriado',
            right: 'NoLaborales'
        },
        events: function(info, successCallback, failureCallback) {
            var idcalendario=$('#selectCalendario').val();
            var datoscal;


    $.ajax({
        type:"POST",
        url: "/calendario/cargarcalendario",
        data: {
            idcalendario
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


    }
    calendar = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendar.setOption('locale', "Es");
    //DESCANSO

    $('#eliminarDescanso').click(function () {
        objEvento = datos("DELETE");
        EnviarDescansoE('/' + id, objEvento);

    });

    function datos(method) {
        nuevoEvento = {
            title: $('#title').val(),
            color: '#4673a0',
            textColor: ' #ffffff ',
            start: $('#start').val(),
            end: $('#end').val(),
            tipo: 1,
            pais: $('#pais').val(),
            departamento: $('#departamento').val(),
            '_method': method
        }
        return (nuevoEvento);
    }



    function EnviarDescansoE(accion, objEvento) {

        $.ajax({
            type: "DELETE",
            url: "/calendario" + accion,
            data: objEvento,
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
                $('#myModalEliminarD').modal('toggle');
                calendar.refetchEvents();
                console.log(msg);
            },
            error: function () {}
        });
    }
    ///
    //NO LABORABLE

    $('#eliminarNLaboral').click(function () {
        objEvento1 = datos1("DELETE");
        EnviarNoLE('/' + id, objEvento1);
    });

    $('#eliminarDiaferi').click(function () {
        objEvento1 = datos1("DELETE");
        EnviarNoLE('/' + id, objEvento1);
    });

    function datos1(method) {
        nuevoEvento1 = {
            title: $('#titleN').val(),
            color: '#a34141',
            textColor: ' #ffffff ',
            start: $('#startF').val(),
            end: $('#endF').val(),
            tipo: 0,
            pais: $('#pais').val(),
            departamento: $('#departamento').val(),

            '_method': method
        }
        return (nuevoEvento1);
    }


    //

    function EnviarNoLE(accion, objEvento1) {
        $.ajax({
            type: "DELETE",
            url: "/calendario" + accion,
            data: objEvento1,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                401: function () {
                    location.reload();
                },
                statusCode: {
                    /*401: function () {
                        location.reload();
                    },*/
                    419: function () {
                        location.reload();
                    }
                },
            },
            success: function (msg) {
                $('#myModalEliminarN').modal('hide');
                $('#myModalEliminarFeriado').modal('hide');
                calendar.refetchEvents();
                console.log(msg);
            },
            error: function () {
                alert("error");
            }
        });
    }
    ////



    calendar.render();

}

function registrarDdescanso()  {
    $('#calendarioAsignar').modal('hide');
   var idevento;
    title= 'Descanso';
    color='#4673a0';
    textColor= '#ffffff';
    start= $('#pruebaStar').val();
    end= $('#pruebaEnd').val();
    tipo= 1;
    id_calendario=$('#selectCalendario').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/eventos_usuario/store",
        data: {title,color,textColor,start,end,tipo,id_calendario},
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

            calendar.refetchEvents();
            idevento=data;


        },
        error: function () {}
    });
    $.ajax({
        type: "POST",
        url: "/calendario/verificarID",
        data: {id_calendario},
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
         if(data==1){

            bootbox.confirm({
                message: "¿Agregar a empleados con este calendario?",
                buttons: {
                    confirm: {
                        label: 'Si',
                        className: 'btn-primary'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-light'
                    }
                },
                callback: function (result) {
                    if (result == true) {

                        $.ajax({
                            type: "POST",
                            url: "/calendario/copiarevenEmpleado",
                            data: {idevento,id_calendario},
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
                               console.log(data);


                            },
                            error: function () {}
                        });

                    }
                }
            });

         }
        },
        error: function () {}
    });


};
function registrarDferiado()  {
    $('#calendarioAsignar').modal('hide');

    title= $('#nombreFeriado').val(),
    color='#e6bdbd',
    textColor= '#775555',
    start= $('#pruebaStar').val();
    end= $('#pruebaEnd').val();
    tipo= 2;
    id_calendario=$('#selectCalendario').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/eventos_usuario/store",
        data: {title,color,textColor,start,end,tipo,id_calendario},
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

            calendar.refetchEvents();
             $('#myModalFeriado').modal('hide');
            console.log(msg);
        },
        error: function () {}
    });
};
function registrarDnlaborables()  {
    $('#calendarioAsignar').modal('hide');

    title= 'No laborable';
    color='#a34141';
    textColor=' #ffffff ';
    start= $('#pruebaStar').val();
    end= $('#pruebaEnd').val();
    tipo= 0;
    id_calendario=$('#selectCalendario').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/eventos_usuario/store",
        data: {title,color,textColor,start,end,tipo,id_calendario},
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

            calendar.refetchEvents();

            console.log(msg);
        },
        error: function () {}
    });
};
function EnviarDescansoE() {

    idDeseli=$('#idDescansoEl').val();
    $.ajax({
        type: "post",
        url: "/calendarioe",
        data: {id:idDeseli},
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
            $('#myModalEliminarD').modal('toggle');
            calendar.refetchEvents();
            console.log(msg);
        },
        error: function () {}
    });
}
function eliminarEvNL() {

    var idDeseliNL=$('#idnolabEliminar').val();
    $.ajax({
        type: "post",
        url: "/calendarioe",
        data: {id:idDeseliNL},
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
            $('#myModalEliminarN').modal('toggle');
            calendar.refetchEvents();
            console.log(msg);
        },
        error: function () {}
    });
}

function eliminarEvF() {

  var  idDeseliF=$('#idFeriadoeliminar').val();
    $.ajax({
        type: "post",
        url: "/calendarioe",
        data: {id:idDeseliF},
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
            $('#myModalEliminarFeriado').modal('toggle');
            calendar.refetchEvents();
            console.log(msg);
        },
        error: function () {}
    });
}

document.addEventListener('DOMContentLoaded', calendario);

//////////////////
//////////////////////
//SEGUNDO CALENDARIO
//////////////////////

function agregarcalendario(){
   var nombrecal= $('#nombreCalen').val();
   $.ajax({
       type:"POST",
    url: "/calendario/registrarnuevo",
    data: {
        nombrecal
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
        $('#nombreCalen').val('');
    $('#selectCalendario').append($('<option>', { //agrego los valores que obtengo de una base de datos
        value: data.calen_id,
        text: data.calendario_nombre,
        selected: true
    }));
    calendario();
    $('#agregarCalendarioN').modal('hide');
    },
    error: function () {}
});
}



//////////////
$('#selectCalendario').change(function (){
    $('#pruebaEnd').val('');
            $('#pruebaStar').val('');
    idca=$('#selectCalendario').val();
   calendario();
  /*  bootbox.alert({

}) */
    var dialog = bootbox.dialog({
        message: "Ahora esta en el calendario de "+$('select[id="selectCalendario"] option:selected').text(),
        closeButton: false
    });
    setTimeout(function(){
        dialog.modal('hide')
    }, 1400);

})
//////////////////////////
function abrirNcalendario(){
    $('#agregarCalendarioN').modal('show');
}
