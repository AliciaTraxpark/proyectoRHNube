$(document).ready(function () {
    $('#form-ver').hide();
    $('#divFfin').hide();

    leertabla();
    $('#Datoscalendar1').css("display", "none");
    $('#aplicarHorario').prop('disabled', true);

    $('.flatpickr-input[readonly]').on('focus', function () {
        $(this).blur()
    })
    $('.flatpickr-input[readonly]').prop('readonly', false)

});

function leertabla() {
    $.get("tablahorario/ver", {}, function (data, status) {
        $('#tabladiv').html(data);

    });
}
$('#nombreEmpleado').change(function () {
    ide=$('#nombreEmpleado').val();
    num=$('#nombreEmpleado').val().length;
    if(num==1){
        $.ajax({
            type: "post",
            url: "/verDataEmpleado",
            data: 'ids=' + ide,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
               calendario(data[1]);
            },
            error: function (data) {
                alert('Ocurrio un error');
            }

        });
    }
    else{     $.get("/eventosHorario", {}, function (data, status) {
        calendario(data);

    });  }

});
function verhorarioEmpleado(idempleado) {
    $('#horaIhorario').val('');
    $('#horaFhorario').val('');
    $('#verhorarioEmpleado').modal('toggle');
    $.ajax({
        type: "post",
        url: "/verDataEmpleado",
        data: 'ids=' + idempleado,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            calendarioHorario(data[1]);
            $('#idEmHorario').val(data[0][0].perso_nombre + ' ' + data[0][0].perso_apPaterno + ' ' + data[0][0].perso_apMaterno);
            $('#paisHorario').val(data[0][0].paises_id);
            $('#idobtenidoE').val(idempleado);
            depart = data[0][0].ubigeo_peru_departments_id;
            if (depart == null) {
                $('#departamentoHorario').val('Ninguno');
            } else {
                $('#departamentoHorario').val(depart);
            }
            if (data[0][0].horario_sobretiempo == 1) {
                $('#exampleCheck2').prop('checked', true);
            }
            $('#tipHorarioEmpleado').val(data[0][0].horario_tipo);
            $('#descripcionCaHorario').val(data[0][0].horario_descripcion);
            $('#toleranciaHorario').val(data[0][0].horario_tolerancia);
        },
        error: function (data) {
            alert('Ocurrio un error');
        }

    });
}
//CALENDARIO HORARIO
function calendarioHorario(eventosEmpleado) {
    var calendarElH = document.getElementById('calendarHorario');
    calendarElH.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendarioH = {
        locale: 'es',
        defaultDate: ano + '-01-01',
        height: "auto",
        contentHeight: 490,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],

        selectable: true,
        selectMirror: true,
        select: function (arg) {
            if($("#collapseTwo").is(':visible')){
                $('#horarioEndH').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
                $('#horarioStartH').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
                f1 = $('#horarioStartH').val();
                f2 = $('#horarioEndH').val();
                inicioHorario=$('#horaIhorario').val();
                finHorario=$('#horaFhorario').val();
                if (inicioHorario == '' || finHorario == '') {
                    alert('Indique hora de inicio y fin');
                } else {
                    agregarHorasHorario();
                }
                function agregarHorasHorario() {
                    //sacar dias entre fecha
                    var diasEntreFechas = function (desde, hasta) {
                        var dia_actual = desde;
                        var fechas = [];
                        while (dia_actual.isSameOrBefore(hasta)) {
                            fechas.push(dia_actual.format('YYYY-MM-DD'));
                            dia_actual.add(1, 'days');
                        }
                        return fechas;
                    };

                    desde = moment(f1);
                    hasta = moment(f2);
                    var results = diasEntreFechas(desde, hasta);
                    results.pop();
                    //console.log(results);
                    var fechasArray = [];
                    var fechastart = [];

                    var objeto = [

                    ];
                    $.each(results, function (key, value) {
                        //alert( value );
                        fechasArray.push(inicioHorario + '-' + finHorario);
                        fechastart.push(value);

                        objeto.push({
                            "title": inicioHorario + '-' + finHorario,
                            "start": value
                        });
                    });
                    console.log(fechasArray);

                   idpais = $('#paisHorario').val();
                    iddepartamento = $('#departamentoHorario').val();
                    if(iddepartamento=='Ninguno'){
                        iddepartamento=null;
                    }

                    $.ajax({
                        type: "post",
                        url: "/guardarEventos",
                        data: {
                            fechasArray: fechastart,
                            hora: inicioHorario + '-' + finHorario,
                            pais: idpais,
                            departamento: iddepartamento,
                            inicio: inicioHorario,
                            fin: finHorario
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                            //alert(fechastart);
                            $.each( fechastart, function( key, value ) {
                            calendar.addEvent(
                                { title: inicioHorario + '-' + finHorario, color: "#ffffff", textColor: "000000", start: value, end: null}
                            ) });
                            $('#guardarHorarioEventos').show();
                        },
                        error: function (data) {
                            alert('Ocurrio un error');
                        }


                    });



                };
            }
            },
        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },

        events: eventosEmpleado,
    }
    var calendar = new FullCalendar.Calendar(calendarElH, configuracionCalendarioH);
    calendar.setOption('locale', "Es");
    ////
    calendar.render();

}
document.addEventListener('DOMContentLoaded', calendarioHorario);
$('#horaI').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
$('#horaIhorario').flatpickr({
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
$('#horaFhorario').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
$('#horaInciden').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
$('#btnasignar').on('click', function(e) {
    $("#formulario")[0].reset();
    $.get("/vaciartemporal", {}, function (data, status) {

    });
    $('#nombreEmpleado').empty();
    $('#asignarHorario').modal('toggle');
    num=$('#nombreEmpleado').val().length;
    idemplesH = $('#nombreEmpleado').val();
                    var ideHor=[];
                    ideHor.push(idemplesH);
    if(num==1){
        $.ajax({
            type: "post",
            url: "/verDataEmpleado",
            data: 'ids=' + ideHor,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
               calendario(data[1]);
            },
            error: function (data) {
                alert('Ocurrio un error');
            }

        });
    }
    else{     $.get("/eventosHorario", {}, function (data, status) {
        calendario(data);

    });  }
    var allVals = [];
    $(".sub_chk:checked").each(function () {
        allVals.push($(this).attr('data-id'));
    });
    $.ajax({
        type: "post",
        url: "/horarioVerTodEmp",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            json1 = JSON.parse(JSON.stringify(data));

            for (var i in json1) {

            $('#nombreEmpleado').append('<option value="' + json1[i].emple_id + '" >' + json1[i].perso_nombre + " " + json1[i].perso_apPaterno + '</option>');
             }


             if (allVals.length > 0) {

                $.each( allVals, function( index, value ){
                    $("#nombreEmpleado option[value='"+ value +"']").attr("selected",true);
                });
                num2=$('#nombreEmpleado').val().length;
                if(num2==1){
                    idemps = $('#nombreEmpleado').val();


                    $.ajax({
                        type: "post",
                        url: "/verDataEmpleado",
                        data: 'ids=' + idemps,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {
                           calendario(data[1]);
                        },
                        error: function (data) {
                            alert('Ocurrio un error');
                        }

                    });

                } else{     $.get("/eventosHorario", {}, function (data, status) {
                    calendario(data);

                });  }

            }

        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });
});
//CALENDARIO//

function calendario(data) {
    var calendarEl = document.getElementById('calendar');
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        defaultDate: ano + '-01-01',
        height: "auto",
        contentHeight: 490,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],

        selectable: true,
        selectMirror: true,
        select: function (arg) {
            console.log(arg);

            $('#horarioEnd').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
            $('#horarioStart').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
            f1 = $('#horarioStart').val();
            f2 = $('#horarioEnd').val();
            inicio = $('#horaI').val();
            fin = $('#horaF').val();
            if (inicio == '' || fin == '') {
                alert('Indique hora de inicio y fin');
            } else {
                agregarHoras();
            }

            function agregarHoras() {
                //sacar dias entre fecha
                var diasEntreFechas = function (desde, hasta) {
                    var dia_actual = desde;
                    var fechas = [];
                    while (dia_actual.isSameOrBefore(hasta)) {
                        fechas.push(dia_actual.format('YYYY-MM-DD'));
                        dia_actual.add(1, 'days');
                    }
                    return fechas;
                };

                desde = moment(f1);
                hasta = moment(f2);
                var results = diasEntreFechas(desde, hasta);
                results.pop();
                //console.log(results);
                var fechasArray = [];
                var fechastart = [];

                var objeto = [

                ];
                $.each(results, function (key, value) {
                    //alert( value );
                    fechasArray.push(inicio + '-' + fin);
                    fechastart.push(value);

                    objeto.push({
                        "title": inicio + '-' + fin,
                        "start": value
                    });
                });
                console.log(fechasArray);

                idpais = $('#pais').val();
                iddepartamento = $('#departamento').val();

                $.ajax({
                    type: "post",
                    url: "/guardarEventos",
                    data: {
                        fechasArray: fechastart,
                        hora: inicio + '-' + fin,
                        pais: idpais,
                        departamento: iddepartamento,
                        inicio: inicio,
                        fin: fin
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        //alert(fechastart);
                        $.each( fechastart, function( key, value ) {
                        calendar.addEvent(
                            { title: inicio + '-' + fin, color: "#ffffff", textColor: "000000", start: value, end: null}
                        ) });
                     /*    $.each( fechasArray, function( key, value ) {
                            //alert( value );
                            calendar.addEvent({
                                title: inicio+'-'+fin,
                                color:'#ffffff',
                                textColor:' #000000',
                                start: value,
                                end:null,


                              })

                          }); */

                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }


                });



            };


        },
        eventClick: function (info) {
            id = info.event.id;
            console.log(info);
            console.log(info.event.id);
            console.log(info.event.title);
            //var event = calendar.getEventById(id);
            // elimina//info.event.remove();
        },
        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'Descanso'
        },
        customButtons: {
            Descanso: {
                text: "Asignar días de Descanso",
                click: function () {
                    alert('seleccione dias');
                }
            }
        },
        events: data,
    }
    var calendar = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendar.setOption('locale', "Es");
    ////
    calendar.render();

}
document.addEventListener('DOMContentLoaded', calendario);

///////////////////////////////

$('#nuevoCalendario').click(function () {
    var departamento = $('#departamento').val();
    var pais = $('#pais').val();
    if (pais == 173 && departamento == '') {
        $('#Datoscalendar').show();
        $('#Datoscalendar1').hide();
        return false;
    }

    $.ajax({
        type: "post",
        url: "/horario/confirmarDepartamento",
        data: {
            departamento: departamento,
            pais: pais
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (dataA) {
            if (dataA[0] == 1) {
                $('#Datoscalendar').hide();
                $('#Datoscalendar1').show();

                //alert('ya esta creado');
            } else {
                $('#Datoscalendar').hide();
                $('#Datoscalendar1').hide();
                alert('No existe calendario');
                return false;
            }
            calendario1(dataA[1]);

        },
        error: function () {
            alert("Hay un error");
        }
    });

});
//SEGUNDO CALENDAR
function calendario1(datadep) {
    var calendarEl1 = document.getElementById('calendar1');
    calendarEl1.innerHTML = "";
    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id1;
    datadep = datadep;

    var configuracionCalendario1 = {
        locale: 'es',
        defaultDate: ano + '-01-01',

        plugins: ['dayGrid', 'interaction', 'timeGrid'],
        height: "auto",
        contentHeight: 450,
        fixedWeekCount: false,
        selectable: true,
        selectMirror: true,
        select: function (arg) {
            console.log(arg);
            $('#horarioEnd').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
            $('#horarioStart').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
            f1 = $('#horarioStart').val();
            f2 = $('#horarioEnd').val();
            inicio = $('#horaI').val();
            fin = $('#horaF').val();
            if (inicio == '' || fin == '') {
                alert('Indique hora de inicio y fin');
            } else {
                agregarHoras2();
            }

            function agregarHoras2() {
                //sacar dias entre fecha
                var diasEntreFechas = function (desde, hasta) {
                    var dia_actual = desde;
                    var fechas = [];
                    while (dia_actual.isSameOrBefore(hasta)) {
                        fechas.push(dia_actual.format('YYYY-MM-DD'));
                        dia_actual.add(1, 'days');
                    }
                    return fechas;
                };
                desde = moment(f1);
                hasta = moment(f2);
                var results = diasEntreFechas(desde, hasta);
                results.pop();
                //console.log(results);
                var fechasArray = [];
                var fechastart = [];
                var objeto = [];
                $.each(results, function (key, value) {
                    //alert( value );
                    fechasArray.push(inicio + '-' + fin);
                    fechastart.push(value);
                    objeto.push({
                        "title": inicio + '-' + fin,
                        "start": value
                    });

                });
                console.log(fechasArray);
                idpais = $('#pais').val();
                iddepartamento = $('#departamento').val();
                $.ajax({
                    type: "post",
                    url: "/guardarEventos",
                    data: {
                        fechasArray: fechastart,
                        hora: inicio + '-' + fin,
                        pais: idpais,
                        departamento: iddepartamento,
                        inicio: inicio,
                        fin: fin
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        calendar1.addEventSource(data);
                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }

                });
            };
        },
        eventClick: function (info) {

            id1 = info.event.id;
            console.log(info);
            console.log(info.event.id);
            console.log(info.event.title);

        },
        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },

        events: datadep,
    }
    var calendar1 = new FullCalendar.Calendar(calendarEl1, configuracionCalendario1);
    calendar1.setOption('locale', "Es");
    //DESCANSO
    calendar1.render();
}
document.addEventListener('DOMContentLoaded', calendario1);
//////////////////////
$('#guardarHorarioEventos').click(function () {
    $('#guardarHorarioEventos').prop('disabled', true);
    var idemps=[];
    idempleads = $('#idobtenidoE').val();
    idemps.push(idempleads);
    descripcion=$('#descripcionCaHorario').val();

    $.ajax({
        type: "post",
        url: "/guardarEventosBD",
        data: {
            idemps,descripcion
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {

            $('#guardarHorarioEventos').prop('disabled', false);

            $('#verhorarioEmpleado').modal('toggle');
            calendario();

        },
        error: function () {
            alert("Hay un error");
        }
    });
});
////////////
$('#guardarTodoHorario').click(function () {
    $('#guardarTodoHorario').prop('disabled', true);
    idemps = $('#nombreEmpleado').val();
    if (idemps == '') {
        alert('Seleccione empleado');
        return false;
    }
    if ($('#exampleCheck1').prop('checked')) {
        sobretiempo = 1;
    } else {
        sobretiempo = 0;
    }
    tipHorario = $('#tipHorario').val();
    descripcion = $('#descripcionCa').val();
    toleranciaH = $('#toleranciaH').val();

    $.ajax({
        type: "post",
        url: "/guardarEventosBD",
        data: {
            idemps,
            sobretiempo,
            tipHorario,
            descripcion,
            toleranciaH
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            leertabla();
            $("#formulario")[0].reset();
            $('#guardarTodoHorario').prop('disabled', false);

            $('#asignarHorario').modal('toggle');
            calendario();

        },
        error: function () {
            alert("Hay un error");
        }
    });
})

     $('#customSwitch1').change(function (event) {
        if( $('#customSwitch1').prop('checked') ){
            $('#divFfin').show();
            $('#divhora').hide();

        }
        else{
            $('#divFfin').hide();
            $('#divhora').show();
        }
    event.preventDefault();
});
$('#btnasignarIncidencia').on('click', function(e) {
    $("#frmIncidencia")[0].reset();
    $('#divFfin').hide();
    $('#divhora').show();
    $('#empIncidencia').empty();
    $('#asignarIncidencia').modal('toggle');
    $.get("empleadoIncHorario", {}, function (data, status) {
        jsonIn = JSON.parse(JSON.stringify(data));
        for (var i in jsonIn) {

            $('#empIncidencia').append('<option value="'+jsonIn[i].emple_id+'" >'+jsonIn[i].perso_nombre+" "+jsonIn[i].perso_apPaterno+'</option>');

        }

    });
});
function registrarIncidencia(){
    idempleadoI=$('#empIncidencia').val();
     descripcionI=$('#descripcionInci').val();
    var descuentoI;
    if( $('#descuentoCheck').prop('checked') ) {
        descuentoI=1;} else{descuentoI=0}
    fechaI=$('#fechaI').val();
     fechaFin=$('#fechaF').val();
     fechaMoment = moment(fechaFin).add(1, 'day');
     fechaF= fechaMoment.format('YYYY-MM-DD');

    var horaIn;
    if( $('#customSwitch1').prop('checked') ) {
        horaIn=null;} else{
            horaIn=$('#horaInciden').val();
            fechaF=null;
          }
        $.ajax({
            type:"post",
            url:"/registrarInci",
            data:{idempleadoI, descripcionI,descuentoI,fechaI,fechaF,horaIn},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) {
                $('#asignarIncidencia').modal('hide');
            },
            error: function (data) {
                alert('Ocurrio un error');
            }
        });


    ;
}
function marcarAsignacion(data){
    $('input:checkbox').prop('checked', false);

    $('input:checkbox[data-id='+data+']').prop('checked', true);
    $('#btnasignar').click();
}
