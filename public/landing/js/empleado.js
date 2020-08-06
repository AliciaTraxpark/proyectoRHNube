$('#horaIncidenCa').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
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
$('#horaIncidenCa_ed').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});

function calendarioInv() {
    var calendarElInv = document.getElementById('calendarInv');
    calendarElInv.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        defaultDate: fecha,
        height: 360,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],

        selectable: false,
        selectMirror: true,

        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
    }
    var calendarInv = new FullCalendar.Calendar(calendarElInv, configuracionCalendario);
    calendarInv.setOption('locale', "Es");

    calendarInv.render();
}
document.addEventListener('DOMContentLoaded', calendarioInv);

function calendario() {
    var calendarEl = document.getElementById('calendar');
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        /* defaultDate: fecha, */
        height: 400,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],

        selectable: true,
        selectMirror: true,
        select: function (arg) {
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
            var event = calendar.getEventById(id);

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
                            url: "/empleado/eliminarEte",
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
                                calendar2.refetchEvents();

                            },
                            error: function (data) {
                                alert('Ocurrio un error');
                            }


                        });
                    }
                }
            });

        },
        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        events: function (info, successCallback, failureCallback) {
            var idcalendario = $('#selectCalendario').val();
            var datoscal;
            $.ajax({
                type: "POST",
                url: "/empleado/calendarioEmpTemp",
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

        /*  events: "calendario/show", */

    }
    calendar = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendar.setOption('locale', "Es");

    calendar.render();
}
document.addEventListener('DOMContentLoaded', calendario);
///calendario e n edit
function calendario_edit() {
    var calendarEl = document.getElementById('calendar_ed');
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        defaultDate: fecha,
        height: 400,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],

        selectable: true,
        selectMirror: true,
        select: function (arg) {
            $('#pruebaEnd_ed').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
            $('#pruebaStar_ed').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
            console.log(arg);
            $('#calendarioAsignar_ed').modal('show');
        },
        eventClick: function (info) {
            id = info.event.id;
            console.log(info);
            console.log(info.event.id);
            console.log(info.event.title);
            console.log(info.event.textColor);
            var event = calendarioedit.getEventById(id);
            if (info.event.textColor == '111111' || info.event.textColor == '1' || info.event.textColor == '0') {
                if (info.event.textColor == '111111') {
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
                                    url: "/empleado/eliminarHorariosEdit",
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
                                        calendar2_ed.refetchEvents();

                                    },
                                    error: function (data) {
                                        alert('Ocurrio un error');
                                    }


                                });
                            }
                        }
                    });
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
                                    url: "/empleado/eliminarInciEdit",
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
                                        calendar2_ed.refetchEvents();

                                    },
                                    error: function (data) {
                                        alert('Ocurrio un error');
                                    }


                                });
                            }
                        }
                    });

                }
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
                                    calendar2_ed.refetchEvents();

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
function laborable_ed() {
    $('#calendarioAsignar_ed').modal('hide');
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
            calendar2_ed.refetchEvents();

            console.log(msg);
        },
        error: function () {}
    });
};
/////////////
function nolaborable_ed() {
    $('#calendarioAsignar_ed').modal('hide');
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
            calendar2_ed.refetchEvents();

            console.log(msg);
        },
        error: function () {}
    });
};
//////////////////
function agregarinciden_ed() {
    $('#calendarioAsignar_ed').modal('hide');
    $("#frmIncidenciaCa_ed")[0].reset();
    $('#modalIncidencia_ed').modal('show');
}
//////////////////
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
        url: "/empleado/storeIncidempleado",
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
            calendarioedit.refetchEvents();
            calendar2_ed.refetchEvents();
            $('#modalIncidencia_ed').modal('hide');

        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });
}
//////////////////////////
$('#selectHorario_ed').change(function (e) {
    var H1 = $('#pruebaStar_ed').val();
    var H2 = $('#pruebaEnd_ed').val();
    var textSelec = $('select[name="selectHorario_ed"] option:selected').text();
    var idhorar = $('#selectHorario_ed').val();
    var idempleado = $('#idempleado').val();
    var diasEntreFechas = function (desde, hasta) {
        var dia_actual = desde;
        var fechas = [];
        while (dia_actual.isSameOrBefore(hasta)) {
            fechas.push(dia_actual.format('YYYY-MM-DD'));
            dia_actual.add(1, 'days');
        }
        return fechas;
    };

    desde = moment(H1);
    hasta = moment(H2);
    var results = diasEntreFechas(desde, hasta);
    results.pop();
    //console.log(results);
    var fechasArray = [];
    var fechastart = [];

    var objeto = [

    ];
    $.each(results, function (key, value) {
        //alert( value );
        fechasArray.push(textSelec);
        fechastart.push(value);

        objeto.push({
            "title": textSelec,
            "start": value
        });
    });
    console.log(fechasArray);



    $.ajax({
        type: "post",
        url: "/empleado/guardarhorarioempleado",
        data: {
            fechasArray: fechastart,
            hora: textSelec,
            idhorar: idhorar,
            idempleado

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

            calendarioedit.refetchEvents();
            calendar2_ed.refetchEvents();
            $("#selectHorario_ed").val("Seleccionar horario");
            $('#horarioAsignar_ed').modal('hide');

        },
        error: function (data) {
            alert('Ocurrio un error');
        }


    });

});

////////////////////////////
function abrirHorario_ed() {
    $("#frmHor_ed")[0].reset();
    $('#horarioAgregar_ed').modal('show');
}

function registrarHorario_ed() {
    if ($('#exampleCheck1_ed').prop('checked')) {
        sobretiempo = 1;
    } else {
        sobretiempo = 0;
    }

    var descripcion = $('#descripcionCa_ed').val();
    var toleranciaH = $('#toleranciaH_ed').val();
    var inicio = $('#horaI_ed').val();
    var fin = $('#horaF_ed').val();

    $.ajax({
        type: "post",
        url: "/empleado/registrarHorario",
        data: {
            sobretiempo,

            descripcion,
            toleranciaH,
            inicio,
            fin
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
            H1 = $('#pruebaStar_ed').val();
            H2 = $('#pruebaEnd_ed').val();
            idhorar = data.horario_id;
            textSelec = $('#descripcionCa_ed').val();
            var idempleado = $('#idempleado').val();
            var diasEntreFechas = function (desde, hasta) {
                var dia_actual = desde;
                var fechas = [];
                while (dia_actual.isSameOrBefore(hasta)) {
                    fechas.push(dia_actual.format('YYYY-MM-DD'));
                    dia_actual.add(1, 'days');
                }
                return fechas;
            };

            desde = moment(H1);
            hasta = moment(H2);
            var results = diasEntreFechas(desde, hasta);
            results.pop();
            //console.log(results);
            var fechasArray = [];
            var fechastart = [];
            var objeto = [];

            $.each(results, function (key, value) {
                //alert( value );
                fechasArray.push(textSelec);
                fechastart.push(value);

                objeto.push({
                    "title": textSelec,
                    "start": value
                });
            });
            console.log(fechasArray);
            $.ajax({
                type: "post",
                url: "/empleado/guardarhorarioempleado",
                data: {
                    fechasArray: fechastart,
                    hora: textSelec,
                    idhorar: idhorar,
                    idempleado
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


                    calendarioedit.refetchEvents();
                    calendar2_ed.refetchEvents();


                },
                error: function (data) {
                    alert('Ocurrio un error');
                }


            });

            $('#horarioAgregar_ed').modal('hide');
            $('#horarioAsignar_ed').modal('hide');
            $('#selectHorario_ed').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.horario_id,
                text: data.horario_descripcion,
                selected: true
            }));
            $("#selectHorario_ed").val("Seleccionar horario");
            /* $('#selectHorarioen').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.horario_id,
                text: data.horario_descripcion

            })); */
        },
        error: function () {
            alert("Hay un error");
        }
    });
}

////////////////////7
function laborableTem() {
    $('#calendarioAsignar').modal('hide');

    title = 'Descanso';
    color = '#4673a0';
    textColor = '#ffffff';
    start = $('#pruebaStar').val();
    end = $('#pruebaEnd').val();
    tipo = 3;
    id_calendario = $('#selectCalendario').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/empleado/storeCalendarioTem",
        data: {
            title,
            color,
            textColor,
            start,
            end,
            tipo,
            id_calendario
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

            calendar.refetchEvents();
            calendar2.refetchEvents();

            console.log(msg);
        },
        error: function () {}
    });
};
/////////////////////////////////
function diaferiadoTem() {
    $('#calendarioAsignar').modal('hide');
    title = $('#nombreFeriado').val(),
        color = '#e6bdbd',
        textColor = '#775555',
        start = $('#pruebaStar').val();
    end = $('#pruebaEnd').val();
    tipo = 2;
    id_calendario = $('#selectCalendario').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/empleado/storeCalendarioTem",
        data: {
            title,
            color,
            textColor,
            start,
            end,
            tipo,
            id_calendario
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

            $('#myModalFeriado').modal('hide');
            calendar.refetchEvents();
            calendar2.refetchEvents();

            console.log(msg);
        },
        error: function () {}
    });
};
/////////////////////////////////
function nolaborableTem() {
    $('#calendarioAsignar').modal('hide');

    title = 'No laborable';
    color = '#a34141';
    textColor = ' #ffffff ';
    start = $('#pruebaStar').val();
    end = $('#pruebaEnd').val();
    tipo = 0;
    id_calendario = $('#selectCalendario').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/empleado/storeCalendarioTem",
        data: {
            title,
            color,
            textColor,
            start,
            end,
            tipo,
            id_calendario
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

            calendar.refetchEvents();
            calendar2.refetchEvents();

            console.log(msg);
        },
        error: function () {}
    });
};

function agregarinciden() {
    $('#calendarioAsignar').modal('hide');
    $("#frmIncidenciaCa")[0].reset();
    $('#modalIncidencia').modal('show');
}

function modalIncidencia() {
    var id_calendario = $('#selectCalendario').val();
    descripcionI = $('#descripcionInciCa').val();
    var descuentoI;
    if ($('#descuentoCheckCa').prop('checked')) {
        descuentoI = 1;
    } else {
        descuentoI = 0
    }
    fechaI = $('#pruebaStar').val();
    fechaFin = $('#pruebaEnd').val();
    horaIn = $('#horaIncidenCa').val();

    $.ajax({
        type: "post",
        url: "/empleado/storeIncidTem",
        data: {
            start: fechaI,
            title: descripcionI,
            descuentoI: descuentoI,
            end: fechaFin,
            horaIn,
            id_calendario

        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            calendar.refetchEvents();
            calendar2.refetchEvents();
            $('#modalIncidencia').modal('hide');

        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });
}

$('#selectCalendario').change(function () {
    $("#detallehorario").empty();
    idca = $('#selectCalendario').val();
    $.ajax({
        type: "post",
        url: "/empleado/vaciarcalendId",
        data: {
            idca

        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            calendar.refetchEvents();
            calendario2();
            $('#calendarInv').hide();
            $('#calendar').show();
            $('#opborrar').show()
            $('#mensajeOc').hide();
            $('#calendar2').show();


            $("#detallehorario").append("<div class='form-group row'><div class='col-md-1'></div><label class='col-lg-3 col-form-label' style='color:#163552;margin-top: 5px;'>Se muestra calendario de: </label>" +
                "<div class='col-md-5'><select disabled style='margin-top: 9px;' class='form-control col-lg-6 form-control-sm'><option>" + $('select[id="selectCalendario"] option:selected').text() + "</option></select></div>"+
                "<div class='col-md-2' ><div class='btn-group mt-2 mr-1'> <button type='button' onclick='eliminarhorariosTem()' class='btn btn-primary btn-sm dropdown-toggle' style='color: #fff; background-color: #4a5669;"+
                "border-color: #485263;' > <img src='admin/images/borrador.svg' height='15'>"+
                " Borrar horarios </button> </div></div></div>");
        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });

    var dialog = bootbox.dialog({
        message: "Ahora esta en el calendario de " + $('select[id="selectCalendario"] option:selected').text(),
        closeButton: false
    });
    setTimeout(function () {
        dialog.modal('hide')
    }, 1400);

})

///edit select
$('#selectCalendario_ed').change(function () {
    $("#detallehorario_ed").empty();
    var idempleado = $('#idempleado').val();
    $.ajax({
        type: "post",
        url: "/empleado/vaciarcalendempleado",
        data: {
            idempleado

        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {

            $('#calendarInv_ed').hide();
            $('#calendar_ed').show();
            $('#mensajeOc_ed').hide();
            $('#calendar2_ed').show();

            calendario_edit()
            calendario2_ed();
            $("#detallehorario_ed").append("<div class='form-group row'><div class='col-md-5 text-right'><label style='color:#163552;margin-top: 5px;'>Se muestra calendario de: </label> </div>" +
                "<div class='col-md-5'><select disabled class='form-control form-control-sm'><option>" + $('select[id="selectCalendario_ed"] option:selected').text() + "</option></select></div></div>");

        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });

    var dialog = bootbox.dialog({
        message: "Ahora esta en el calendario de " + $('select[id="selectCalendario_ed"] option:selected').text(),
        closeButton: false
    });
    setTimeout(function () {
        dialog.modal('hide')
    }, 1400);

})
///////////////////
function eliminarhorariosTem(){
    fmes = calendar2.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        message: "¿Esta seguro que desea eliminar feriados del calendario?",
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
                    url: "/empleado/vaciarhorarioTem",
                    data: {
                        mescale,
                        aniocalen
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
                        calendar.refetchEvents();
                        calendar2.refetchEvents();

                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }
                });


            }
        }
    });
}
////////
function calendario2() {
    var calendarEl = document.getElementById('calendar2');
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        defaultDate: fecha,
        height: 400,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],

        selectable: true,
        selectMirror: true,
        select: function (arg) {
            $('#pruebaEnd').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
            $('#pruebaStar').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
            console.log(arg);
            $('#horarioAsignar').modal('show');
        },
        eventClick: function (info) {
            id = info.event.id;
            console.log(info);
            console.log(info.event.id);
            console.log(info.event.title);
            var event = calendar2.getEventById(id);

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
                            url: "/empleado/eliminarEte",
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
                                calendar.refetchEvents();

                            },
                            error: function (data) {
                                alert('Ocurrio un error');
                            }


                        });
                    }
                }
            });

        },
        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        events: function (info, successCallback, failureCallback) {
            var idcalendario = $('#selectCalendario').val();
            var datoscal;
            $.ajax({
                type: "POST",
                url: "/empleado/calendarioEmpTemp",
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

        /*  events: "calendario/show", */

    }
    calendar2 = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendar2.setOption('locale', "Es");

    calendar2.render();
}
document.addEventListener('DOMContentLoaded', calendario2);

function abrirHorario() {
    $("#frmHor")[0].reset();
    $('#horarioAgregar').modal('show');
}

function registrarHorario() {
    if ($('#exampleCheck1').prop('checked')) {
        sobretiempo = 1;
    } else {
        sobretiempo = 0;
    }

    var descripcion = $('#descripcionCa').val();
    var toleranciaH = $('#toleranciaH').val();
    var inicio = $('#horaI').val();
    var fin = $('#horaF').val();

    $.ajax({
        type: "post",
        url: "/empleado/registrarHorario",
        data: {
            sobretiempo,

            descripcion,
            toleranciaH,
            inicio,
            fin
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
            H1 = $('#pruebaStar').val();
            H2 = $('#pruebaEnd').val();
            idhorar = data.horario_id;
            textSelec = $('#descripcionCa').val();
            idca = $('#selectCalendario').val();
            var diasEntreFechas = function (desde, hasta) {
                var dia_actual = desde;
                var fechas = [];
                while (dia_actual.isSameOrBefore(hasta)) {
                    fechas.push(dia_actual.format('YYYY-MM-DD'));
                    dia_actual.add(1, 'days');
                }
                return fechas;
            };

            desde = moment(H1);
            hasta = moment(H2);
            var results = diasEntreFechas(desde, hasta);
            results.pop();
            //console.log(results);
            var fechasArray = [];
            var fechastart = [];
            var objeto = [];

            $.each(results, function (key, value) {
                //alert( value );
                fechasArray.push(textSelec);
                fechastart.push(value);

                objeto.push({
                    "title": textSelec,
                    "start": value
                });
            });
            console.log(fechasArray);
            $.ajax({
                type: "post",
                url: "/empleado/guardarhorarioTem",
                data: {
                    fechasArray: fechastart,
                    hora: textSelec,
                    idhorar: idhorar,
                    idca
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

                    calendar.refetchEvents();
                    calendar2.refetchEvents();


                },
                error: function (data) {
                    alert('Ocurrio un error');
                }


            });

            $('#horarioAgregar').modal('hide');
            $('#horarioAsignar').modal('hide');
            $('#selectHorario').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.horario_id,
                text: data.horario_descripcion,
                selected: true
            }));
            $("#selectHorario").val("Seleccionar horario");
            /* $('#selectHorarioen').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.horario_id,
                text: data.horario_descripcion

            })); */
        },
        error: function () {
            alert("Hay un error");
        }
    });
}
$('#selectHorario').change(function (e) {
    var H1 = $('#pruebaStar').val();
    var H2 = $('#pruebaEnd').val();
    var textSelec = $('select[name="selectHorario"] option:selected').text();
    var idhorar = $('#selectHorario').val();
    var idca = $('#selectCalendario').val();
    var diasEntreFechas = function (desde, hasta) {
        var dia_actual = desde;
        var fechas = [];
        while (dia_actual.isSameOrBefore(hasta)) {
            fechas.push(dia_actual.format('YYYY-MM-DD'));
            dia_actual.add(1, 'days');
        }
        return fechas;
    };

    desde = moment(H1);
    hasta = moment(H2);
    var results = diasEntreFechas(desde, hasta);
    results.pop();
    //console.log(results);
    var fechasArray = [];
    var fechastart = [];

    var objeto = [

    ];
    $.each(results, function (key, value) {
        //alert( value );
        fechasArray.push(textSelec);
        fechastart.push(value);

        objeto.push({
            "title": textSelec,
            "start": value
        });
    });
    console.log(fechasArray);



    $.ajax({
        type: "post",
        url: "/empleado/guardarhorarioTem",
        data: {
            fechasArray: fechastart,
            hora: textSelec,

            idhorar: idhorar,
            idca

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
            calendar.refetchEvents();
            calendar2.refetchEvents();
            $("#selectHorario").val("Seleccionar horario");
            $('#horarioAsignar').modal('hide');

        },
        error: function (data) {
            alert('Ocurrio un error');
        }


    });

});



//vercal

function calendario3() {
    var calendarEl = document.getElementById('calendar3');
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        defaultDate: fecha,
        height: 400,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],

        selectable: true,
        selectMirror: true,
        /* select: function (arg) {
            $('#pruebaEnd').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
            $('#pruebaStar').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
            console.log(arg);
            $('#horarioAsignar').modal('show');
        }, */
        eventClick: function (info) {

        },
        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        events: function (info, successCallback, failureCallback) {
            var idempleado = $('#idempleado').val();
            var datoscal;
            $.ajax({
                type: "POST",
                url: "/empleado/vercalendario",
                data: {
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
    calendar3 = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendar3.setOption('locale', "Es");

    calendar3.render();
}
document.addEventListener('DOMContentLoaded', calendario3);
////////////////
//************* */
$("#checkboxFechaI").on("click", function () {
    if ($("#checkboxFechaI").is(':checked')) {
        $('#ocultarFecha > .combodate').hide();
        $('#labelfechaF').hide();
        $('#m_fechaF').combodate("clearValue");
    } else {
        $('#labelfechaF').show();
        $('#ocultarFecha > .combodate').show();
    }
});
document.addEventListener('DOMContentLoaded', calendario3);

///inv
function calendarioInv_ed() {
    var calendarElInv_ed = document.getElementById('calendarInv_ed');
    calendarElInv_ed.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        defaultDate: fecha,
        height: 360,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],

        selectable: false,
        selectMirror: true,

        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
    }
    var calendarInv_ed = new FullCalendar.Calendar(calendarElInv_ed, configuracionCalendario);
    calendarInv_ed.setOption('locale', "Es");

    calendarInv_ed.render();
}
document.addEventListener('DOMContentLoaded', calendarioInv_ed);
////////////////////////////
function calendario2_ed() {
    var calendarEl = document.getElementById('calendar2_ed');
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        defaultDate: fecha,
        height: 400,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],

        selectable: true,
        selectMirror: true,
        select: function (arg) {
            $('#pruebaEnd_ed').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
            $('#pruebaStar_ed').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
            console.log(arg);
            $('#horarioAsignar_ed').modal('show');
        },
        eventClick: function (info) {
            id = info.event.id;
            console.log(info);
            console.log(info.event.id);
            console.log(info.event.title);
            console.log(info.event.textColor);
            var event = calendarioedit.getEventById(id);
            if (info.event.textColor == '111111' || info.event.textColor == '1' || info.event.textColor == '0') {
                if (info.event.textColor == '111111') {
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
                                    url: "/empleado/eliminarHorariosEdit",
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
                                        calendar2_ed.refetchEvents();

                                    },
                                    error: function (data) {
                                        alert('Ocurrio un error');
                                    }


                                });
                            }
                        }
                    });
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
                                    url: "/empleado/eliminarInciEdit",
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
                                        calendar2_ed.refetchEvents();

                                    },
                                    error: function (data) {
                                        alert('Ocurrio un error');
                                    }


                                });
                            }
                        }
                    });

                }
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
                                    calendar2_ed.refetchEvents();

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
            var datoscal;
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
    calendar2_ed = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendar2_ed.setOption('locale', "Es");

    calendar2_ed.render();
}
document.addEventListener('DOMContentLoaded', calendario2_ed);
//************* */
$("#checkboxFechaI").on("click", function () {
    if ($("#checkboxFechaI").is(':checked')) {
        $('#ocultarFecha > .combodate').hide();
        $('#labelfechaF').hide();
        $('#m_fechaF').combodate("clearValue");
    } else {
        $('#labelfechaF').show();
        $('#ocultarFecha > .combodate').show();
    }
});
////////////////////////////
$("#file").fileinput({
    allowedFileExtensions: ['jpg', 'jpeg', 'png'],
    uploadAsync: false,
    showRemove: true,
    minFileCount: 0,
    maxFileCount: 1,
    initialPreviewAsData: true, // identify if you are sending preview data only and not the markup
    language: 'es',
    browseOnZoneClick: true,
    theme: "fa",
    showUpload: false,
    showBrowse: false
});
$('#fechaN').combodate({
    minYear: 1960,
    yearDescending: false,
});
$('#m_fechaI').combodate({
    value: new Date(),
    minYear: 2000,
    maxYear: moment().format('YYYY') + 1,
    yearDescending: false
});
$('#m_fechaF').combodate({
    minYear: 2014,
    maxYear: moment().format('YYYY') + 1,
    yearDescending: false,
});
$('#v_fechaN').combodate({
    minYear: 1900,
    yearDescending: false,
});
$('#v_fechaNV').combodate({
    minYear: 1900,
    yearDescending: false,
});
//AREA
function agregarArea() {
    objArea = datosArea("POST");
    enviarArea('', objArea);
};

function datosArea(method) {
    nuevoArea = {
        area_descripcion: $('#textArea').val().toUpperCase(),
        '_method': method
    }
    return (nuevoArea);
}

function enviarArea(accion, objArea) {
    var id = $('#editarA').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/area" + accion,
            data: objArea,
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
                $('#area').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.area_id,
                    text: data.area_descripcion,
                    selected: true
                }));
                $('#v_area').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.area_id,
                    text: data.area_descripcion,
                    selected: true
                }));
                $('#area').val(data.area_id).trigger("change"); //lo selecciona
                $('#v_area').val(data.area_id).trigger("change");
                $('#textArea').val('');
                $('#editarArea').hide();
                $('#areamodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nÁrea Registrada\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            },
            error: function () {}
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarArea" + accion,
            data: {
                id: id,
                objArea
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
                $('#area').empty();
                $('#v_area').empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/area",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].area_id}">${data[i].area_descripcion}</option>`;
                        }
                        $('#area').append(select);
                        $('#v_area').append(select);
                    },
                    error: function () {}
                });
                $('#area').val(data.area_id).trigger("change"); //lo selecciona
                $('#v_area').val(data.area_id).trigger("change");
                $('#textArea').val('');
                $('#editarArea').hide();
                $('#areamodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nÁrea Modificada\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }
        });
    }
}
///CARGO
function agregarcargo() {
    objCargo = datosCargo("POST");
    enviarCargo('', objCargo);
};

function datosCargo(method) {
    nuevoCargo = {
        cargo_descripcion: $('#textCargo').val().toUpperCase(),
        '_method': method
    }
    return (nuevoCargo);
}

function enviarCargo(accion, objCargo) {
    var id = $('#editarC').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/cargo" + accion,
            data: objCargo,
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
                $('#cargo').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.cargo_id,
                    text: data.cargo_descripcion,
                    selected: true
                }));
                $('#v_cargo').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.cargo_id,
                    text: data.cargo_descripcion,
                    selected: true
                }));
                $('#cargo').val(data.cargo_id).trigger("change"); //lo selecciona
                $('#v_cargo').val(data.cargo_id).trigger("change"); //lo selecciona
                $('#textCargo').val('');
                $('#editarCargo').hide();
                $('#cargomodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nCargo Registrado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            },
            error: function () {}
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarCargo" + accion,
            data: {
                id: id,
                objCargo
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
                $('#cargo').empty();
                $('#v_cargo').empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/cargo",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        console.log(data);
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].cargo_id}">${data[i].cargo_descripcion}</option>`;
                        }
                        $('#cargo').append(select);
                        $('#v_cargo').append(select);
                    },
                    error: function () {}
                });
                $('#cargo').val(data.cargo_id).trigger("change"); //lo selecciona
                $('#v_cargo').val(data.cargo_id).trigger("change");
                $('#textCargo').val('');
                $('#editarCargo').hide();
                $('#cargomodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nCargo Modificado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }
        });
    }
}
//centro costo
function agregarcentro() {
    objCentroC = datosCentro("POST");
    enviarCentro('', objCentroC);
};

function datosCentro(method) {
    nuevoCentro = {
        centroC_descripcion: $('#textCentro').val().toUpperCase(),
        '_method': method
    }
    return (nuevoCentro);
}

function enviarCentro(accion, objCentroC) {
    var id = $('#editarCC').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/centro" + accion,
            data: objCentroC,
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
                $('#centroc').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.centroC_id,
                    text: data.centroC_descripcion,
                    selected: true
                }));
                $('#v_centroc').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.centroC_id,
                    text: data.centroC_descripcion,
                    selected: true
                }));
                $('#centroc').val(data.centroC_id).trigger("change"); //lo selecciona
                $('#v_centroc').val(data.centroC_id).trigger("change"); //lo selecciona
                $('#textCentro').val('');
                $('#editarCentro').hide();
                $('#centrocmodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nCentro Costo Registrado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            },
            error: function () {}
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarCentro" + accion,
            data: {
                id: id,
                objCentroC
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
                $('#centroc').empty();
                $('#v_centroc').empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/centro",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].centroC_id}">${data[i].centroC_descripcion}</option>`;
                        }
                        $('#centroc').append(select);
                        $('#v_centroc').append(select);
                    },
                    error: function () {}
                });
                $('#centroc').val(data.centroC_id).trigger("change"); //lo selecciona
                $('#v_centroc').val(data.centroC_id).trigger("change"); //lo selecciona
                $('#textCentro').val('');
                $('#editarCentro').hide();
                $('#centrocmodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nCentro Costo Modificado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }
        });
    }
}
//LOCAL
function agregarlocal() {
    objLocal = datosLocal("POST");
    enviarLocal('', objLocal);
};

function datosLocal(method) {
    nuevoLocal = {
        local_descripcion: $('#textLocal').val().toUpperCase(),
        '_method': method
    }
    return (nuevoLocal);
}

function enviarLocal(accion, objLocal) {
    var id = $('#editarL').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/local" + accion,
            data: objLocal,
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
                $('#local').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.local_id,
                    text: data.local_descripcion,
                    selected: true
                }));
                $('#v_local').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.local_id,
                    text: data.local_descripcion,
                    selected: true
                }));
                $('#local').val(data.local_id).trigger("change"); //lo selecciona
                $('#v_local').val(data.local_id).trigger("change"); //lo selecciona
                $('#textLocal').val('');
                $('#editarLocal').hide();
                $('#localmodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nLocal Registrado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            },
            error: function () {}
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarLocal" + accion,
            data: {
                id: id,
                objLocal
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
                $('#local').empty();
                $('#v_local').empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/local",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].local_id}">${data[i].local_descripcion}</option>`;
                        }
                        $('#local').append(select);
                        $('#v_local').append(select);
                    },
                    error: function () {}
                });
                $('#local').val(data.local_id).trigger("change"); //lo selecciona
                $('#v_local').val(data.local_id).trigger("change"); //lo selecciona
                $('#textLocal').val('');
                $('#editarLocal').hide();
                $('#localmodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nLocal Modificado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }
        });
    }
}
//NIVEL
function agregarnivel() {
    objNivel = datosNivel("POST");
    enviarNivel('', objNivel);
};

function datosNivel(method) {
    nuevoNivel = {
        nivel_descripcion: $('#textNivel').val().toUpperCase(),
        '_method': method
    }
    return (nuevoNivel);
}

function enviarNivel(accion, objNivel) {
    var id = $('#editarN').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/nivel" + accion,
            data: objNivel,
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
                $('#nivel').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.nivel_id,
                    text: data.nivel_descripcion,
                    selected: true
                }));
                $('#v_nivel').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.nivel_id,
                    text: data.nivel_descripcion,
                    selected: true
                }));
                $('#nivel').val(data.nivel_id).trigger("change"); //lo selecciona
                $('#v_nivel').val(data.nivel_id).trigger("change"); //lo selecciona
                $('#textNivel').val('');
                $('#editarNivel').hide();
                $('#nivelmodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nNivel Registrado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });

            },
            error: function () {}
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarNivel" + accion,
            data: {
                id: id,
                objNivel
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
                $('#nivel').empty();
                $('#v_nivel').empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/nivel",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].nivel_id}">${data[i].nivel_descripcion}</option>`;
                        }
                        $('#nivel').append(select);
                        $('#v_nivel').append(select);
                    },
                    error: function () {}
                });
                $('#nivel').val(data.nivel_id).trigger("change"); //lo selecciona
                $('#v_nivel').val(data.nivel_id).trigger("change"); //lo selecciona
                $('#textNivel').val('');
                $('#editarNivel').hide();
                $('#nivelmodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nNivel Modificado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }
        });
    }
}

//CONTRATO
function agregarContrato() {
    objContrato = datosContrato("POST");
    enviarContrato('', objContrato);
};

function datosContrato(method) {
    nuevoContrato = {
        contrato_descripcion: $('#textContrato').val().toUpperCase(),
        '_method': method
    }
    return (nuevoContrato);
}

function enviarContrato(accion, objContrato) {
    var id = $('#editarCO').val();
    if (id == '' || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/contrato" + accion,
            data: objContrato,
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
                $('#contrato').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.contrato_id,
                    text: data.contrato_descripcion,
                    selected: true
                }));
                $('#v_contrato').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.contrato_id,
                    text: data.contrato_descripcion,
                    selected: true
                }));
                $('#contrato').val(data.contrato_id).trigger("change"); //lo selecciona
                $('#v_contrato').val(data.contrato_id).trigger("change"); //lo selecciona
                $('#textcontrato').val('');
                $('#editarContrato').hide();
                $('#contratomodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nContrato Registrado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });

            },
            error: function () {}
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarContrato" + accion,
            data: {
                id: id,
                objContrato
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
                $('#contrato').empty();
                $('#v_contrato').empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/contrato",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].contrato_id}">${data[i].contrato_descripcion}</option>`;
                        }
                        $('#contrato').append(select);
                        $('#v_contrato').append(select);
                    },
                    error: function () {}
                });
                $('#contrato').val(data.contrato_id).trigger("change"); //lo selecciona
                $('#v_contrato').val(data.contrato_id).trigger("change"); //lo selecciona
                $('#textcontrato').val('');
                $('#editarContrato').hide();
                $('#contratomodal').modal('toggle');
                $('#form-registrar').modal('show');
                $.notify({
                    message: "\nContrato Modificado\n",
                    icon: 'admin/images/checked.svg'
                }, {
                    element: $('#form-registrar'),
                    position: 'fixed',
                    icon_type: 'image',
                    newest_on_top: true,
                    delay: 5000,
                    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }
        });
    }
}
//FECHAS
function agregarFechas() {
    $('#form-registrar').modal('show');
    fechaI = $('#m_fechaI').val();
    fechaF = $('#m_fechaF').val();
    //$('#c_fechaI').text(fechaI);
    //$('#c_fechaF').text(fechaF);
    $('#fechasmodal').modal('toggle');
}
//CODIGO EMPLEADO
function valorCodigoEmpleado() {
    var numDocumento = $('#numDocumento').val();
    $('#codigoEmpleado').val(numDocumento);

}
//EMPLEADO
/*$('#guardarEmpleado').click(function () {
    objEmpleado = datosPersona("POST");
    enviarEmpleado('', objEmpleado);
});*/


function datosPersona(method) {
    var celularC = '';
    console.log($('input:radio[name=tipo]:checked').val());
    if ($('#celular').val() != '') {
        celularC = $('#codigoCelular').val() + $('#celular').val();
    }
    nuevoEmpleado = {
        nombres: $('#nombres').val(),
        apPaterno: $('#apPaterno').val(),
        apMaterno: $('#apMaterno').val(),
        fechaN: $('#fechaN').val(),
        tipo: $('input:radio[name=tipo]:checked').val(),
        documento: $('#documento').val(),
        numDocumento: $('#numDocumento').val(),
        departamento: $('#departamento').val(),
        provincia: $('#provincia').val(),
        distrito: $('#distrito').val(),
        dep: $('#dep').val(),
        prov: $('#prov').val(),
        dist: $('#dist').val(),
        direccion: $('#direccion').val(),
        celular: celularC,
        telefono: $('#telefono').val(),
        correo: $('#email').val(),
        '_method': method
    }
    return (nuevoEmpleado);
}

function enviarEmpleado(accion, objEmpleado) {

    var formData = new FormData();
    formData.append('objEmpleado', JSON.stringify(objEmpleado));

    $.ajax({

        type: "POST",
        url: "/empleado/store" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
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
            $('#idEmpleado').val(data);
            $.notify({
                message: "\nDatos Guardados.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-registrar'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function (data, errorThrown) {}
    });
}
//GUARDAR CALENDARIO EN GUARDAR EMPLEADO
function enviarEmpleadoStore(accion, objEmpleado) {

    var formData = new FormData();
    formData.append('objEmpleado', JSON.stringify(objEmpleado));

    $.ajax({

        type: "POST",
        url: "/empleado/storeEmpleado" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
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
            $.notify({
                message: "\nDatos Modificados.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-registrar'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function (data, errorThrown) {}
    });
}
//GUARDAR DATOS EMPRESARIAL EN GUARDAR EMPLEADO
function datosEmpresaEmpleado(method) {
    nuevoEmpresa = {
        codigoEmpleado: $('#codigoEmpleado').val(),
        cargo: $('#cargo').val(),
        area: $('#area').val(),
        centroc: $('#centroc').val(),
        contrato: $('#contrato').val(),
        fechaI: $('#m_fechaI').val(),
        fechaF: $('#m_fechaF').val(),
        nivel: $('#nivel').val(),
        local: $('#local').val(),
        '_method': method
    }
    return (nuevoEmpresa);
}

function enviarEmpresarialEmpleado(accion, objEmpleado) {
    var formData = new FormData();
    formData.append('objEmpleado', JSON.stringify(objEmpleado));
    $.ajax({

        type: "POST",
        url: "/empleado/storeEmpresarial" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
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
            $.notifyClose();
            $.notify({
                message: "\nDatos Guardados.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-registrar'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function (data, errorThrown) {}
    });
}

//GUARDAR FOTO EN GUARDAR EMPLEADO

function enviarFotoEmpleado(accion) {
    var formData = new FormData();
    formData.append('file', $('#file').prop('files')[0]);
    $.ajax({

        type: "POST",
        url: "/empleado/storeFoto" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
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
            $.notifyClose();
            $.notify({
                message: "\nDatos Guardados.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-registrar'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function (data, errorThrown) {}
    });
}

function datosCalendarioEmpleado(method) {
    nuevoCalendario = {
        idca: $('#selectCalendario').val(),
        '_method': method
    }
    return (nuevoCalendario);
}

function enviarCalendarioEmpleado(accion, objEmpleado) {
    var formData = new FormData();
    formData.append('objEmpleado', JSON.stringify(objEmpleado));
    $.ajax({

        type: "POST",
        url: "/empleado/storeCalendario" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
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
            $.notifyClose();
            $.notify({
                message: "\nDatos Guardados.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-registrar'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function (data, errorThrown) {}
    });
}
//GUARDAR HORARIO EN GUARDAR EMPLEADO
function datosHorarioEmpleado(method) {
    nuevoHorario = {
        idca: $('#selectCalendario').val(),
        '_method': method
    }
    return (nuevoHorario);
}

function enviarHorarioEmpleado(accion, objEmpleado) {
    var formData = new FormData();
    formData.append('objEmpleado', JSON.stringify(objEmpleado));
    $.ajax({

        type: "POST",
        url: "/empleado/storeHorario" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
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
            $.notifyClose();
            $.notify({
                message: "\nDatos Guardados.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-registrar'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function (data, errorThrown) {}
    });
}
//EMPLEADO STOREEMPLEADO
function enviarEmpleadoStore(accion, objEmpleado) {

    var formData = new FormData();
    formData.append('objEmpleado', JSON.stringify(objEmpleado));

    $.ajax({

        type: "POST",
        url: "/empleado/storeEmpleado" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
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
            $.notify({
                message: "\nDatos Modificados.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-registrar'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function (data, errorThrown) {}
    });
}
//EMPLEADO ACTUALIZAR
$("#checkboxFechaIE").on("click", function () {
    if ($("#checkboxFechaIE").is(':checked')) {
        $('#m_fechaFE').combodate("clearValue");
        $('#ocultarFechaE > .combodate').hide();
        $('#ocultarFechaE').hide();
    } else {
        $('#ocultarFechaE').show();
        $('#ocultarFechaE > .combodate').show();
    }
});
/*$('#actualizarEmpleado').click(function () {
    idE = $('#v_id').val();
    console.log($('#v_fechaFC').text());
    objEmpleadoA = datosPersonaA("PUT");
    actualizarEmpleado('/' + idE, objEmpleadoA);
});*/


function datosPersonaA(method) {
    var celularC = '';
    if ($('#v_celular').val() != '') {
        celularC = $('#v_codigoCelular').val() + $('#v_celular').val();
    }
    nuevoEmpleadoA = {
        nombres_v: $('#v_nombres').val(),
        apPaterno_v: $('#v_apPaterno').val(),
        apMaterno_v: $('#v_apMaterno').val(),
        fechaN_v: $('#v_fechaN').val(),
        tipo_v: $('input:radio[name=v_tipo]:checked').val(),
        departamento_v: $('#v_departamento').val(),
        provincia_v: $('#v_provincia').val(),
        distrito_v: $('#v_distrito').val(),
        cargo_v: $('#v_cargo').val(),
        area_v: $('#v_area').val(),
        centroc_v: $('#v_centroc').val(),
        dep_v: $('#v_dep').val(),
        prov_v: $('#v_prov').val(),
        dist_v: $('#v_dist').val(),
        contrato_v: $('#v_contrato').val(),
        direccion_v: $('#v_direccion').val(),
        nivel_v: $('#v_nivel').val(),
        local_v: $('#v_local').val(),
        celular_v: celularC,
        telefono_v: $('#v_telefono').val(),
        correo_v: $('#v_email').val(),
        fechaI_v: $('#m_fechaIE').val(),
        fechaF_v: $('#m_fechaFE').val(),
        codigoEmpleado_v: $('#v_codigoEmpleado').val(),
        '_method': method
    }
    return (nuevoEmpleadoA);
}

function actualizarEmpleado(accion, objEmpleadoA) {

    var formDataA = new FormData();
    formDataA.append('file', $('#file2').prop('files')[0]);
    formDataA.append('objEmpleadoA', JSON.stringify(objEmpleadoA));
    console.log(objEmpleadoA);
    $.ajax({

        type: "POST",
        url: "/empleadoA" + accion,
        data: formDataA,
        contentType: false,
        processData: false,
        dataType: "json",
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (msg) {
            $.notifyClose();
            $.notify({
                message: "\nDatos Actualizado.",
                icon: 'admin/images/checked.svg'
            }, {
                element: $('#form-ver'),
                position: 'fixed',
                icon_type: 'image',
                newest_on_top: true,
                delay: 5000,
                template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                    '</div>',
                spacing: 35
            });
        },
        error: function (data, errorThrown) {
            alert("Hay un error");
            console.log(formDataA.get('objEmpleadoA'));
        }
    });
}
///ELIMINAR EMPLEADO

//abrir nuevo form
function abrirnuevo() {
    $('#form-ver').hide();
    $('#tablaEmpleado tbody tr').removeClass('selected');
    $('#form-registrar').smartWizard("reset");
    $('input[type="text"]').val("");
    $('input:radio[name=tipo]:checked').prop('checked', false);
    $('input[type="date"]').val("");
    $('input[type="file"]').val("");
    $('select').val("");
    $('#form-registrar').show();
    $('#selectCalendario').val("Asignar calendario");
    $('#selectHorario').val("Seleccionar horario");
}

//eliminar foto
function cargarFile2() {
    $("#file2").fileinput({
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        uploadAsync: false,
        overwriteInitial: false,
        showUpload: false,
        validateInitialCount: true,
        showRemove: true,
        minFileCount: 0,
        maxFileCount: 1,
        ...(hayFoto && {
            initialPreview: [
                "<img  id=v_foto src='{{asset('/fotosEmpleado')}}/'" +
                urlFoto + " style='max-width:200px; max-height:200px; height:auto; width:auto'>"
            ],
            initialPreviewConfig: [{
                width: "200px",
                height: "200px",
                url: "/eliminarFoto/" + id_empleado,
                showDelete: true,
                key: id_empleado
            }]
        }),
        language: 'es',
        deleteExtraData: {
            _token: $("#csrf_token").val()
        },
        showBrowse: false,
        browseOnZoneClick: true,
        theme: "fa",
        fileActionSettings: {
            "showDrag": false,
            'showZoom': false
        },
    })
}
//********************** */
$('#documento').on('change', function () {
    $("#form-registrar :input").attr('disabled', false);
    if($('#documento').val()==1){
        $( "#numDocumento" ).attr( "maxlength", "8" );
    }
    if($('#documento').val()==2){
        $( "#numDocumento" ).attr( "maxlength", "12" );
    }
    if($('#documento').val()==3){
        $( "#numDocumento" ).attr( "maxlength", "12" );
    }
});
$('#smartwizardVer :input').attr('disabled', true);
$("#form-registrar :input").prop('disabled', true);
$('#documento').attr('disabled', false);
$('#cerrarModalEmpleado').attr('disabled', false);
$('#cerrarE').attr('disabled', false);
$('#cerrarEd').attr('disabled', false);
$('#documento').on('change', function () {
    $("#form-registrar :input").attr('disabled', false);
    if($('#documento').val()==1){
        $( "#numDocumento" ).attr( "maxlength", "8" );
    }
    if($('#documento').val()==2){
        $( "#numDocumento" ).attr( "maxlength", "12" );
    }
    if($('#documento').val()==3){
        $( "#numDocumento" ).attr( "maxlength", "12" );
    }
});
$('#formNuevoE').click(function () {

    calendarioInv();
    $('#calendarInv').show();
    $('#calendar').hide();
    $('#opborrar').hide();
    $("#detallehorario").empty();
    $('#calendar2').hide();
    $('#FinalizarEmpleado').hide();
    $.get("/empleado/vaciarcalend", {}, function (data, status) {
        $('#form-registrar').modal();
        $('#cerrarModalEmpleado').attr('disabled', false);

    });

});
$('#formNuevoEd').click(function () {
    $('#FinalizarEmpleadoEditar').hide();
    $.get("/empleado/vaciarcalend", {}, function (data, status) {
        $('#form-ver').modal();
    })
});

$('#formNuevoEd').hide();
$('#formNuevoEl').hide();
$('#cerrarE').click(function () {
    //leertabla();
    $('#smartwizard1').smartWizard("reset");
    $('#formNuevoEd').hide();
    $('#formNuevoEl').hide();
    $('#selectCalendario').val("Asignar calendario");
    $('#selectHorario').val("Seleccionar horario");
});
$('#cerrarEd').click(function () {
    //leertabla();
    $('#smartwizard1').smartWizard("reset");
    $('#formNuevoEd').hide();
    $('#formNuevoEl').hide();
    $('#navActualizar').hide();
    $()
    $('#m_fechaIE').combodate("clearValue");
    $('#m_fechaFE').combodate("clearValue");
    $('#checkboxFechaIE').prop('checked', false);
    //************* */
    $('#v_validApPaterno').hide();
    $('#v_validNumDocumento').hide();
    $('#v_validApMaterno').hide();
    $('#v_validNombres').hide();
    $('#v_validCorreo').hide();
    $('#v_emailR').hide();
    $('#v_validCel').hide();
    $('input[type="date"]').val("");
    $('input[type="file"]').val("");
    $('input[type="email"]').val("");
    $('input[type="number"]').val("");
    $('#form-ver :input').val("");
    $('#selectHorario_ed').val("Seleccionar horario");
    $('#codigoCelular').val("+51");
    limpiar();
    $('#selectCalendario').val("Asignar calendario");
    $('#selectHorario').val("Seleccionar horario");

});
$('#cerrarModalEmpleado').click(function () {
    //leertabla();

    //************ */
    $('#formNuevoEd').hide();
    $('#formNuevoEl').hide();
    $('#smartwizard').smartWizard("reset");
    $('input[type="text"]').val("");
    $('input:radio[name=tipo]:checked').prop('checked', false);
    $('input[type="date"]').val("");
    $('input[type="file"]').val("");
    $('input[type="email"]').val("");
    $('input[type="number"]').val("");
    $('#form-registrar :input').val("");
    $("#form-registrar :input").prop('disabled', true);
    $('#documento').attr('disabled', false);
    $('#cerrarMoadalEmpleado').attr('disabled', false);
    $('#checkboxFechaI').prop('checked', false);
    $('#codigoCelular').val("+51");
    //********** */
    $('#v_emailR').hide();
    $('#validDocumento').hide();
    $('#validApPaterno').hide();
    $('#validNumDocumento').hide();
    $('#validApMaterno').hide();
    $('#validFechaN').hide();
    $('#validNombres').hide();
    $('#validGenero').hide();
    $('#validCel').hide();
    $('#emailR').hide();
    $('#validCorreo').hide();
    $('#detalleContrato').hide();
    $('#editarArea').hide();
    $('#form-registrar').modal('toggle');
    limpiar();
    $('#selectCalendario').val("Asignar calendario");
    $('#selectHorario').val("Seleccionar horario");
});
//*********************/
$('#numR').hide();
$('#emailR').hide();
$('#v_emailR').hide();
$('#validDocumento').hide();
$('#validApPaterno').hide();
$('#validNumDocumento').hide();
$('#validApMaterno').hide();
$('#validCorreo').hide();
$('#validNombres').hide();
$('#validGenero').hide();
//************* */
$('#v_validApPaterno').hide();
$('#v_validNumDocumento').hide();
$('#v_validApMaterno').hide();
$('#v_validNombres').hide();
$('#v_validCorreo').hide();
$('#detalleContrato').hide();
$('#editarArea').hide();
$('#editarCargo').hide();
$('#editarCentro').hide();
$('#editarLocal').hide();
$('#editarNivel').hide();
$('#editarContrato').hide();
$('#editarAreaA').hide();
$('#editarCargoA').hide();
$('#editarCentroA').hide();
$('#editarLocalA').hide();
$('#editarNivelA').hide();
$('#editarContratoA').hide();
$('#validCel').hide();
$('#v_validCel').hide();
$('#v_validGenero').hide();

function FinalizarEmpleado() {
    leertabla();
    $('#form-registrar :input').val("");
    $('input[type="text"]').val("");
    // $('input:radio[name=tipo]:checked').prop('checked', false);
    $('input:radio[name=tipo]:checked').removeProp('checked');
    $('input[type="date"]').val("");
    $('input[type="file"]').val("");
    $('input[type="email"]').val("");
    $('#form-registrar :input').val("");
    $('#file').val('');
    $('#file').fileinput('refresh');
    $('#codigoCelular').val("+51");
    $("#form-registrar :input").prop('disabled', true);
    $('#documento').attr('disabled', false);
    $('#cerrarMoadalEmpleado').attr('disabled', false);
    $('#m_fechaI').combodate("clearValue");
    $('#m_fechaF').combodate("clearValue");
    $('#detalleContrato').hide();
    $('#checkboxFechaI').prop('checked', false);
    $('#selectCalendario').val("Asignar calendario");
    $('#selectHorario').val("Seleccionar horario");
    $('#tbodyDispositivo').empty();
    $('#smartwizard').smartWizard("reset");
    $('#form-registrar').modal('toggle');
}
//************************Editar en los modal de agregar */
//*******AREA***/
$('#buscarArea').on("click", function () {
    $('#editarArea').empty();
    var container = $('#editarArea');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/area",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="area" id="editarA">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].area_id}">${data[i].area_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarA').on("change", function () {
                var id = $('#editarA').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarArea",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textArea').val(data);
                    },
                    error: function () {
                        $('#textArea').val("");
                    }
                })
            });
        },
        error: function () {}
    });
    $('#editarArea').show();
});
//******CARGO*****/
$('#buscarCargo').on("click", function () {
    $('#editarCargo').empty();
    var container = $('#editarCargo');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/cargo",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="cargo" id="editarC">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].cargo_id}">${data[i].cargo_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarC').on("change", function () {
                var id = $('#editarC').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarCargo",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textCargo').val(data);
                    },
                    error: function () {
                        $('#textCargo').val("");
                    }
                })
            });
        },
        error: function () {}
    });
    $('#editarCargo').show();
});
//******CENTRO***/
$('#buscarCentro').on("click", function () {
    $('#editarCentro').empty();
    var container = $('#editarCentro');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/centro",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="centro" id="editarCC">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].centroC_id}">${data[i].centroC_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarCC').on("change", function () {
                var id = $('#editarCC').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarCentro",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textCentro').val(data);
                    },
                    error: function () {
                        $('#textCentro').val("");
                    }
                })
            });
        },
        error: function () {}
    });
    $('#editarCentro').show();
});
//******LOCAL***/
$('#buscarLocal').on("click", function () {
    $('#editarLocal').empty();
    var container = $('#editarLocal');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/local",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="Local" id="editarL">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].local_id}">${data[i].local_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarL').on("change", function () {
                var id = $('#editarL').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarLocal",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textLocal').val(data);
                    },
                    error: function () {
                        $('#textLocal').val("");
                    }
                })
            });
        },
        error: function () {}
    });
    $('#editarLocal').show();
});
//******NIVEL***/
$('#buscarNivel').on("click", function () {
    $('#editarNivel').empty();
    var container = $('#editarNivel');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/nivel",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="nivel" id="editarN">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].nivel_id}">${data[i].nivel_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarN').on("change", function () {
                var id = $('#editarN').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarNivel",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textNivel').val(data);
                    },
                    error: function () {
                        $('#textNivel').val("");
                    }
                })
            });
        },
        error: function () {}
    });
    $('#editarNivel').show();
});
//******CONTRATO***/
$('#buscarContrato').on("click", function () {
    $('#editarContrato').empty();
    var container = $('#editarContrato');
    var select = "";
    $.ajax({
        type: "GET",
        url: "/contrato",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            select += `<select class="form-control" name="contrato" id="editarCO">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].contrato_id}">${data[i].contrato_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $('#editarCO').on("change", function () {
                var id = $('#editarCO').val();
                $.ajax({
                    type: "GET",
                    url: "/buscarContrato",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        $('#textContrato').val(data);
                    },
                    error: function () {
                        $('#textContrato').val("");
                    }
                })
            });
        },
        error: function () {}
    });
    $('#editarContrato').show();
});
//*****LIMPIAR***/
function limpiar() {
    $('#editarArea').hide();
    $('#editarCargo').hide();
    $('#editarCentro').hide();
    $('#editarLocal').hide();
    $('#editarNivel').hide();
    $('#editarContrato').hide();
    $('#textArea').val("");
    $('#textCargo').val("");
    $('#textCentro').val("");
    $('#textLocal').val("");
    $('#textNivel').val("");
    $('#textContrato').val("");
}

function vaciardFeria() {
    fmes = calendar.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        message: "¿Esta seguro que desea eliminar dias feriados del calendario?",
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
                    url: "/empleado/vaciardfTem",
                    data: {
                        mescale,
                        aniocalen
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
                        calendar.refetchEvents();
                        calendar2.refetchEvents();

                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }
                });


            }
        }
    });
}
/////////////////
function vaciarddescanso() {
    fmes = calendar.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        message: "¿Esta seguro que desea eliminar dias de descanso del calendario?",
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
                    url: "/empleado/vaciardescansoTem",
                    data: {
                        mescale,
                        aniocalen
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
                        calendar.refetchEvents();
                        calendar2.refetchEvents();

                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }
                });


            }
        }
    });
}
//////////////
function vaciardlabTem() {

    bootbox.confirm({
        message: "¿Esta seguro que desea eliminar dias laborales del calendario?",
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
                $.get("/empleado/vaciardlabTem", {}, function (data, status) {
                    calendario();
                    calendario2();;
                });

            }
        }
    });
}

function vaciardNlabTem() {
    fmes = calendar.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        message: "¿Esta seguro que desea eliminar dias no laborales del calendario?",
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
                    url: "/empleado/vaciardNlabTem",
                    data: {
                        mescale,
                        aniocalen
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
                        calendar.refetchEvents();
                        calendar2.refetchEvents();

                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }
                });


            }
        }
    });
}

function vaciardIncidTem() {
    fmes = calendar.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        message: "¿Esta seguro que desea eliminar todas las incidencias del calendario?",
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
                    url: "/empleado/vaciardIncidTem",
                    data: {
                        mescale,
                        aniocalen
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
                        calendar.refetchEvents();
                        calendar2.refetchEvents();

                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }
                });

            }
        }
    });
}
////////////////////////////////////////////////////////////
function diaferiadoRe_ed() {
    $('#calendarioAsignar_ed').modal('hide');
    title = $('#nombreFeriado_ed').val(),
        color = '#e6bdbd',
        textColor = '#775555',
        start = $('#pruebaStar_ed').val();
        end = $('#pruebaEnd_ed').val();
    tipo = 2;
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

            $('#myModalFeriado_ed').modal('hide');
            calendarioedit.refetchEvents();
            calendar2_ed.refetchEvents();

            console.log(msg);
        },
        error: function () {}
    });
};
//////////////////////////////////////////////////////////
function vaciardFeriaBD(){
    var idempleado = $('#idempleado').val();
    fmes = calendarioedit.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        message: "¿Esta seguro que desea eliminar dias feriados del calendario?",
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
                    url: "/empleado/vaciarFerBD",
                    data: {
                        mescale,
                        aniocalen,
                        idempleado
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
                        calendarioedit.refetchEvents();
                        calendar2_ed.refetchEvents();

                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }
                });


            }
        }
    });
}
function vaciarddescansoBD(){
    var idempleado = $('#idempleado').val();
    fmes = calendarioedit.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        message: "¿Esta seguro que desea eliminar dias de descanso  del calendario?",
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
                    url: "/empleado/vaciarFdescansoBD",
                    data: {
                        mescale,
                        aniocalen,
                        idempleado
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
                        calendarioedit.refetchEvents();
                        calendar2_ed.refetchEvents();

                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }
                });


            }
        }
    });
}
function vaciarNlabBD(){
    var idempleado = $('#idempleado').val();
    fmes = calendarioedit.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        message: "¿Esta seguro que desea eliminar dias no laborales del calendario?",
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
                    url: "/empleado/vaciardnlaBD",
                    data: {
                        mescale,
                        aniocalen,
                        idempleado
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
                        calendarioedit.refetchEvents();
                        calendar2_ed.refetchEvents();

                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }
                });


            }
        }
    });
}
function vaciardIncidBD(){
    var idempleado = $('#idempleado').val();
    fmes = calendarioedit.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        message: "¿Esta seguro que desea eliminar incidencias del calendario?",
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
                    url: "/empleado/vaciarincidelaBD",
                    data: {
                        mescale,
                        aniocalen,
                        idempleado
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
                        calendarioedit.refetchEvents();
                        calendar2_ed.refetchEvents();

                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }
                });


            }
        }
    });
}
function eliminarhorariosBD(){
    var idempleado = $('#idempleado').val();
    fmes = calendar2_ed.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        message: "¿Esta seguro que desea eliminar horarios del calendario?",
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
                    url: "/empleado/eliminarhorariosBD",
                    data: {
                        mescale,
                        aniocalen,
                        idempleado
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
                        calendarioedit.refetchEvents();
                        calendar2_ed.refetchEvents();

                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }
                });


            }
        }
    });

}
