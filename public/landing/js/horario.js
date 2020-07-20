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

function verhorarioEmpleado(idempleado) {
    $("*").removeClass("fc-highlight");
     $.get("/vaciartemporal", {}, function (data, status) {

    });

    $('#verhorarioEmpleado').modal('toggle');
    $.ajax({
        type: "post",
        url: "/verDataEmpleado",
        data: 'ids=' + idempleado,
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
        success: function (data) {
            $("#tablahorarios>tbody>tr").remove();
            var fechah = new Date();
            var ano3 = fechah. getFullYear();
            var mes3=fechah.getMonth()+1;
             fechas1=ano3+'-'+mes3+'-01';
            var fechasMh=new Date(fechas1);
            calendarioHorario(data[1],fechasMh);
            $('#idEmHorario').val(data[0][0].perso_nombre + ' ' + data[0][0].perso_apPaterno + ' ' + data[0][0].perso_apMaterno);
            $('#docEmpleado').val(data[0][0].emple_nDoc);
            $('#correoEmpleado').val(data[0][0].emple_Correo);
            $('#celEmpleado').val(data[0][0].emple_celular);
            $('#areaEmpleado').val(data[0][0].area_descripcion);
            $('#cargoEmpleado').val(data[0][0].cargo_descripcion);
            $('#ccEmpleado').val(data[0][0].centroC_descripcion);
            $('#localEmpleado').val(data[0][0].local_descripcion);
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

            $.each(data[2], function (key, item) {
                $("#tablahorarios>tbody").append(
                    "<tr ><td style='padding: 4px;'>"+item.title+
                    "</td> <td style='padding: 4px;'>"+item.horaI+"</td><td style='padding: 4px;'>"+item.horaF+"</td></tr>"
                    );
               });
        },
        error: function (data) {
            alert('Ocurrio un error');
        }

    });
}
//CALENDARIO HORARIO
function calendarioHorario(eventosEmpleado,fechasMh) {
    var calendarElH = document.getElementById('calendarHorario');
    calendarElH.innerHTML = "";
    var fechasMh=fechasMh;
    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendarioH = {
        locale: 'es',
        //defaultDate: ano + '-01-01',
        defaultDate: fechasMh,
        height: "auto",
        contentHeight: 490,
        unselectAuto:false,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],
        eventClick: function (info) {
            id = info.event.id;

            console.log(info.event.id);

           var event = calendar.getEventById(id);
           idempleadoEli=$('#idobtenidoE').val();
           if(info.event.textColor=='111111' || info.event.textColor=='#945353' || info.event.textColor=='#fff7f7' || info.event.textColor=='#775555' || info.event.textColor=='#ffffff' || info.event.textColor=='#0b1b29' ){
            bootbox.confirm({
                message: "¿Desea eliminar: "+info.event.title+" del horario?",
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
                            url: "/eliminarHorarBD",
                            data: {
                                idHora: info.event.id,textcolor:info.event.textColor,ide:idempleadoEli
                            },
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
                            success: function (data) {
                            info.event.remove();
                            },
                            error: function (data) {
                                alert('Ocurrio un error');
                            }


                        });}
                }
            });


           } else{
            bootbox.confirm({
                message: "¿Desea eliminar: "+info.event.title+" del horario?",
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
                            url: "/eliminarIncidBD",
                            data: {
                                idHora: info.event.id,textcolor:info.event.textColor,ide:idempleadoEli
                            },
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
                            success: function (data) {
                            info.event.remove();
                            },
                            error: function (data) {
                                alert('Ocurrio un error');
                            }


                        });}
                }
            });

               };

           //info.event.remove();
        },
        selectable: true,
        selectMirror: true,
        select: function (arg) {
            var date1 = calendar.getDate();
            $('#fechaDa2').val(date1);
             $('#horario1em').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
            $('#horario2em').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));


            },

        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        customButtons: {
            /* Descanso: {
                text: "Asignar días de Descanso",
                click:function(){
                    $.notify("Seleccione Dias", {
                        align: "right",
                        verticalAlign: "top",
                        type: "info"

                    });

                }

            } */
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
$('#horaIen').flatpickr({
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
$('#horaFen').flatpickr({
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
$('#horaIncidenHo').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
$('#horaIncidenHoEm').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
$('#btnasignar').on('click', function(e) {
    $("*").removeClass("fc-highlight");
  /*   $("#formulario")[0].reset(); */
    $.get("/copiarEventos", {}, function (data, status) {

    });
    $('#nombreEmpleado').empty();
    $('#Datoscalendar').show();
    $('#Datoscalendar1').hide();
    $('#asignarHorario').modal('toggle');
    num=$('#nombreEmpleado').val().length;
    idemplesH = $('#nombreEmpleado').val();
                    var ideHor=[];
                    ideHor.push(idemplesH);

    $.get("/eventosHorario", {}, function (data, status) {


        var fechac = new Date();
         var ano = fechac. getFullYear();
         var mesc=fechac.getMonth()+1;
         fechas1=ano+'-'+mesc+'-01';

         var fechasM=new Date(fechas1);

        calendario(data,fechasM);

    });
    var allVals = [];
    $(".sub_chk:checked").each(function () {
        allVals.push($(this).attr('data-id'));
    });
    $.ajax({
        type: "post",
        url: "/horarioVerTodEmp",
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

            }

        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });
});
//CALENDARIO//

function calendario(data,fechasM) {
    var calendarEl = document.getElementById('calendar');
    calendarEl.innerHTML = "";
    var fechasM=fechasM;
    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        //defaultDate: ano + '-01-01',
        defaultDate: fechasM,
        height: "auto",
        contentHeight: 440,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],
        unselectAuto:false,
        selectable: true,
        selectMirror: true,
        select: function (arg) {
            idemps = $('#nombreEmpleado').val();
            if (idemps == '') {
                calendar.unselect();
                bootbox.alert({
                    message: "Seleccione empleado",

                });

                return false;
            }
            finH=moment(arg.end).format('YYYY-MM-DD HH:mm:ss');
           startH=moment(arg.start).format('YYYY-MM-DD HH:mm:ss');
            console.log(arg);
            var date1 = calendar.getDate();
            $('#fechaDa').val(date1);
            $('#horario1').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
            $('#horario2').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));


        },
        eventClick: function (info) {
            id = info.event.id;
            console.log(info);
            console.log(info.event.id);
            console.log(info.event.title);
           var event = calendar.getEventById(id);

            bootbox.confirm({
                message: "¿Desea eliminar: "+info.event.title+" del horario?",
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
                            url: "/eliminarHora",
                            data: {
                                idHora: info.event.id
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

           //info.event.remove();
        },
        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next',
            center: 'title',
            right: ''
        },

        events: data,
    }

    var calendar = new FullCalendar.Calendar(calendarEl, configuracionCalendario);


    calendar.setOption('locale', "Es");
    ////
    calendar.render();
   /*  $("#calendar > div.fc-toolbar.fc-header-toolbar > div.fc-right").html(); */
}
$('#selectHorario').change(function(e){
    if($("*").hasClass("fc-highlight")){
    e.stopPropagation();
    idpais = $('#pais').val();
    iddepartamento = $('#departamento').val();
    textSelec=$('select[name="selectHorario"] option:selected').text();
    var idhorar = $('#selectHorario').val();

    var diasEntreFechas = function (desde, hasta) {
        var dia_actual = desde;
        var fechas = [];
        while (dia_actual.isSameOrBefore(hasta)) {
            fechas.push(dia_actual.format('YYYY-MM-DD'));
            dia_actual.add(1, 'days');
        }
        return fechas;
    };

    desde = moment(startH);
    hasta = moment(finH);
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
        url: "/guardarEventos",
        data: {
            fechasArray: fechastart,
            hora: textSelec,
            pais: idpais,
            departamento: iddepartamento,
            idhorar:idhorar

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
            $("#selectHorario").val("Asignar horario");
            var mesAg= $('#fechaDa').val();
        var d  =mesAg;
        var fechasM=new Date(d);
            calendario(data,fechasM);


        },
        error: function (data) {
            alert('Ocurrio un error');
        }


    });
   } else
   {  $("#selectHorario").val("Asignar horario");
    bootbox.alert({
        message: "Primero debe asignar dia(s) de calendario.",

    })
}
     });


document.addEventListener('DOMContentLoaded', calendario);

///////////////////////////////

$('#departamento').change(function(){
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
        success: function (dataA) {
            if (dataA[0] == 1) {

                $('#Datoscalendar').show();

                //alert('ya esta creado');
            } else {


                bootbox.alert({
                    message: "No existe calendario",

                });
                return false;
            }
            var mesAg= $('#fechaDa').val();
        var d  =mesAg;
        var fechasM=new Date(d);
            calendario(dataA[1],fechasM);

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
            if ($("#calendar1 > div.fc-toolbar.fc-header-toolbar > div.fc-right > button").is(":focus"))
            {

          idpaisDescanso = $('#pais').val();
          iddepartamentoDescanso = $('#departamento').val();
          $.ajax({
            type: "post",
            url: "/storeDescanso",
            data: {
                start: moment(arg.start).format('YYYY-MM-DD HH:mm:ss'),
                title: 'Descanso Emp.',
                pais: idpaisDescanso,
                departamento: iddepartamentoDescanso,
                end: moment(arg.end).format('YYYY-MM-DD HH:mm:ss')

            },
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
            success: function (data) {
                //alert(fechastart);
                calendar1.addEventSource(data);
                calendar1.addEvent({
                    title: 'Descanso Emp.',
                    id:data.id,
                    color:'#e5e5e5',
                    textColor:'#3f51b5',
                    start:  moment(arg.start).format('YYYY-MM-DD HH:mm:ss'),
                    end:moment(arg.end).format('YYYY-MM-DD HH:mm:ss'),

                  })
            },
            error: function (data) {
                alert('Ocurrio un error');
            }


        });
          $('#calendar1 > div.fc-toolbar.fc-header-toolbar > div.fc-right > button').blur();
           return false;
            };
            if (inicio == '' || fin == '') {

                bootbox.alert({
                    message: "Indique hora de inicio y fin",

                });
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
            id = info.event.id;
            console.log(info);
            console.log(info.event.id);
            console.log(info.event.title);
           var event = calendar1.getEventById(id);
           if(info.event.textColor=='000000' ||info.event.textColor=='111111' || info.event.textColor=='#3f51b5' ){
            bootbox.confirm({
                message: "¿Desea eliminar: "+info.event.title+" del horario?",
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
                            url: "/eliminarHora",
                            data: {
                                idHora: info.event.id,textcolor:info.event.textColor
                            },
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

           } else{bootbox.alert({
            message: "No se puede eliminar, evento pertenece a calendario general",

        })};

           //info.event.remove();
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
                click:function(){
                    $.notify("Seleccione Dias", {
                        align: "right",
                        verticalAlign: "top",
                        type: "info"

                    });

                }

            }
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
    nuevaTolerancia=$('#nuevaTolerancia').val();
    $.ajax({
        type: "post",
        url: "/guardarHorario",
        data: {
            idemps,descripcion,toleranciaH:nuevaTolerancia
        },
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
        success: function (data) {

            $('#guardarHorarioEventos').prop('disabled', false);
            leertabla();
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

        bootbox.alert({
            message: "Seleccione empleado",

        });
        $('#guardarTodoHorario').prop('disabled', false);
        return false;
    }
    $.ajax({
        type: "post",
        url: "/guardarHorarioC",
        data: {
            idemps,

        },
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
        success: function (data) {
            leertabla();
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
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                }
            },
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
$('#cerrarHorario').click(function () {
    leertabla();
    $('#verhorarioEmpleado').modal('toggle');
});
function abrirHorario(){
    if($("*").hasClass("fc-highlight")){
    $("#frmHor")[0].reset();
    $('#horarioAgregar').modal('show');
    } else{
        bootbox.alert({
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }

}
function abrirHorarioen(){
    if($("*").hasClass("fc-highlight")){
    $("#frmHoren")[0].reset();
    $('#horarioAgregaren').modal('show');
    } else{
        bootbox.alert({
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }

}
function registrarHorario(){
    if ($('#exampleCheck1').prop('checked')) {
        sobretiempo = 1;
    } else {
        sobretiempo = 0;
    }
    tipHorario = $('#tipHorario').val();
    descripcion = $('#descripcionCa').val();
    toleranciaH = $('#toleranciaH').val();
    inicio = $('#horaI').val();
    fin = $('#horaF').val();

    $.ajax({
        type: "post",
        url: "/guardarHorario",
        data: {
            sobretiempo,
            tipHorario,
            descripcion,
            toleranciaH,inicio,fin
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
            H1=$('#horario1').val();
            H2=$('#horario2').val();
            idhorar=data.horario_id;
            idpais = $('#pais').val();
            iddepartamento = $('#departamento').val();
        textSelec=$('#descripcionCa').val();
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
        var objeto = [  ];

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
                url: "/guardarEventos",
                data: {
                    fechasArray: fechastart,
                    hora: textSelec,
                    pais: idpais,
                    departamento: iddepartamento,
                    idhorar:idhorar
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
                    $("#selectHorario").val("Asignar horario");
                    var mesAg= $('#fechaDa').val();
                    var d  =mesAg;
                    var fechasM=new Date(d);
                    calendario(data,fechasM);


                },
                error: function (data) {
                    alert('Ocurrio un error');
                }


            });

            $('#horarioAgregar').modal('hide');
            $('#selectHorario').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.horario_id,
                text: data.horario_descripcion,
                selected: true
            }));
        },
        error: function () {
            alert("Hay un error");
        }
    });
}
function registrarHorarioen(){
    if ($('#exampleCheck1en').prop('checked')) {
      var  sobretiempo = 1;
    } else {
       var  sobretiempo = 0;
    }
   var  tipHorario = $('#tipHorarioen').val();
    var descripcion = $('#descripcionCaen').val();
    var toleranciaH = $('#toleranciaHen').val();
    var inicio = $('#horaIen').val();
   var  fin = $('#horaFen').val();

    $.ajax({
        type: "post",
        url: "/guardarHorario",
        data: {
            sobretiempo,
            tipHorario,
            descripcion,
            toleranciaH,inicio,fin
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
            H1=$('#horario1em').val();
            H2=$('#horario2em').val();
            idhorar=data.horario_id;
            /* idpais = $('#pais').val();
            iddepartamento = $('#departamento').val(); */
        textSelec=$('#descripcionCaen').val();
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
        var objeto = [  ];

        $.each(results, function (key, value) {
            //alert( value );
            fechasArray.push(textSelec);
            fechastart.push(value);

            objeto.push({
                "title": textSelec,
                "start": value
            });
        });
        idempl=$('#idobtenidoE').val();
        console.log(fechasArray);
            $.ajax({
                type: "post",
                url: "/storeHorarioEmBD",
                data: {
                    fechasArray: fechastart,
                    hora: textSelec,
                   /*  pais: idpais,
                    departamento: iddepartamento, */
                    idhorar:idhorar,idempl
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
                    $("#selectHorarioen").val("Asignar horario");
                    var mesAg2= $('#fechaDa2').val();
                    var d2  =mesAg2;
                    var fechasMh=new Date(d2);
                    calendarioHorario(data,fechasMh);
                    $('#horarioAgregaren').modal('hide');


                },
                error: function (data) {
                    alert('Ocurrio un error');
                }


            });

            $('#horarioAgregar').modal('hide');
            $('#selectHorarioen').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.horario_id,
                text: data.horario_descripcion,
                selected: true
            }));
        },
        error: function () {
            alert("Hay un error");
        }
    });
}
function asignarlabo(){
   var  H1=$('#horario1').val();
  var   H2=$('#horario2').val();
    if($( "*").hasClass("fc-highlight")){
        bootbox.confirm({
            message: "¿Desea agregar dias laborables?",
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
                   var idpais = $('#pais').val();
                   var  iddepartamento = $('#departamento').val();
                    $.ajax({
                      type: "post",
                      url: "/storeLaborable",
                      data: {
                          start: H1,
                          title: 'Dia laborable.',
                          pais: idpais,
                          departamento: iddepartamento,
                          end: H2

                      },
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
                      success: function (data) {
                          //alert(fechastart);
                          var mesAg= $('#fechaDa').val();
                        var d  =mesAg;
                        var fechasM=new Date(d);
                          calendario(data,fechasM);

                      },
                      error: function (data) {
                          alert('Ocurrio un error');
                      }


                  });
                }
            }
        });
    } else {bootbox.alert({
        message: "Primero debe asignar dia(s) de calendario.",

    })}


}
function asignarlaboen(){
    var  H1=$('#horario1em').val();
   var   H2=$('#horario2em').val();
 var   idempl=$('#idobtenidoE').val();
     if($( "*").hasClass("fc-highlight")){
         bootbox.confirm({
             message: "¿Desea agregar dias laborables?",
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
                       url: "/storeLaborHorarioBD",
                       data: {
                           start: H1,
                           title: 'Dia laborable.',
                           end: H2,idempl

                       },
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
                       success: function (data) {
                           //alert(fechastart);
                           var mesAg2= $('#fechaDa2').val();
                           var d2  =mesAg2;
                           var fechasMh=new Date(d2);
                           calendarioHorario(data,fechasMh);


                       },
                       error: function (data) {
                           alert('Ocurrio un error');
                       }


                   });
                 }
             }
         });
     } else {bootbox.alert({
         message: "Primero debe asignar dia(s) de calendario.",

     })}


 }
function asignarNolabo(){
    H1=$('#horario1').val();
    H2=$('#horario2').val();
    if($("*").hasClass("fc-highlight")){
    bootbox.confirm({
        message: "¿Desea agregar dias no laborables?",
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
                idpais = $('#pais').val();
                iddepartamento = $('#departamento').val();
                $.ajax({
                  type: "post",
                  url: "/storeNoLaborable",
                  data: {
                      start: H1,
                      title: 'No laborable.',
                      pais: idpais,
                      departamento: iddepartamento,
                      end: H2

                  },
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
                  success: function (data) {
                      //alert(fechastart);
                      var mesAg= $('#fechaDa').val();
                      var d  =mesAg;
                      var fechasM=new Date(d);
                      calendario(data,fechasM);

                  },
                  error: function (data) {
                      alert('Ocurrio un error');
                  }


              });
            }
        }
    });}
    else {bootbox.alert({
        message: "Primero debe asignar dia(s) de calendario.",

    })}

}
function asignarNolaboen(){
    var  H1=$('#horario1em').val();
   var   H2=$('#horario2em').val();
 var   idempl=$('#idobtenidoE').val();
     if($( "*").hasClass("fc-highlight")){
         bootbox.confirm({
             message: "¿Desea agregar dias no laborables?",
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
                       url: "/storeNoLaborHorarioBD",
                       data: {
                           start: H1,
                           title: 'No laborable.',
                           end: H2,idempl

                       },
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
                       success: function (data) {
                           //alert(fechastart);
                           var mesAg2= $('#fechaDa2').val();
                           var d2  =mesAg2;
                           var fechasMh=new Date(d2);
                           calendarioHorario(data,fechasMh);


                       },
                       error: function (data) {
                           alert('Ocurrio un error');
                       }


                   });
                 }
             }
         });
     } else {bootbox.alert({
         message: "Primero debe asignar dia(s) de calendario.",

     })}


 }
function asignarInci(){
    if($("*").hasClass("fc-highlight")){
   $("#frmIncidenciaHo")[0].reset();
    $('#divFfin').hide();
    $('#divhora').show();
    $('#empIncidencia').empty();
    $('#asignarIncidenciaHorario').modal('toggle');
    }
    else {bootbox.alert({
        message: "Primero debe asignar dia(s) de calendario.",

    })}

}
function asignarInciEmp(){
    if($("*").hasClass("fc-highlight")){
   $("#frmIncidenciaHoEm")[0].reset();

    $('#divhora').show();
    $('#empIncidencia').empty();
    $('#asignarIncidenciaHorarioEmp').modal('toggle');
    }
    else {bootbox.alert({
        message: "Primero debe asignar dia(s) de calendario.",

    })}

}
function registrarIncidenciaHoEm(){
    var descripcionI=$('#descripcionInciHoEm').val();
    var descuentoI;
   var  idempl=$('#idobtenidoE').val();
    if( $('#descuentoCheckHoEm').prop('checked') ) {
        descuentoI=1;} else{descuentoI=0}
   var  fechaI=$('#horario1em').val();
   var   fechaFin=$('#horario2em').val();
   var   horaIn=$('#horaIncidenHoEm').val();
    /*  idpais = $('#pais').val();
     iddepartamento = $('#departamento').val(); */
     $.ajax({
        type: "post",
        url: "/storeIncidenciaEmpleado",
        data: {
            start: fechaI,
            title: descripcionI,descuentoI:descuentoI,
            /* pais: idpais,
            departamento: iddepartamento, */
            end: fechaFin,
            horaIn,
            idempl


        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            var mesAg2= $('#fechaDa2').val();
            var d2  =mesAg2;
            var fechasMh=new Date(d2);
            calendarioHorario(data,fechasMh);

            $('#asignarIncidenciaHorarioEmp').modal('toggle');

        },
        error: function (data) {
            alert('Ocurrio un error');
        }


    });

}
function registrarIncidenciaHo(){
    descripcionI=$('#descripcionInciHo').val();
    var descuentoI;
    if( $('#descuentoCheckHo').prop('checked') ) {
        descuentoI=1;} else{descuentoI=0}
    fechaI=$('#horario1').val();
     fechaFin=$('#horario2').val();
     horaIn=$('#horaIncidenHo').val();
     idpais = $('#pais').val();
     iddepartamento = $('#departamento').val();
     $.ajax({
        type: "post",
        url: "/storeIncidencia",
        data: {
            start: fechaI,
            title: descripcionI,
            pais: idpais,descuentoI:descuentoI,
            departamento: iddepartamento,
            end: fechaFin,
            horaIn

        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            var mesAg= $('#fechaDa').val();
        var d  =mesAg;
        var fechasM=new Date(d);
            calendario(data,fechasM);
            $('#asignarIncidenciaHorario').modal('toggle');

        },
        error: function (data) {
            alert('Ocurrio un error');
        }


    });

}
function vaciarcalendario(){
    bootbox.confirm({
        message: "¿Esta seguro que desea vaciar calendario?",
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
                $.get("/vaciartemporal", {}, function (data, status) {
                    var mesAg= $('#fechaDa').val();
                    var d  =mesAg;
                    var fechasM=new Date(d);
                    calendario(data,fechasM);
                });

            }
        }
    });

}
function vaciarhor(){
    bootbox.confirm({
        message: "¿Esta seguro que desea eliminar horario(s) del calendario?",
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
                $.get("/vaciarhor", {}, function (data, status) {
                    var mesAg= $('#fechaDa').val();
                    var d  =mesAg;
                    var fechasM=new Date(d);
                    calendario(data,fechasM);
                });

            }
        }
    });

}
// change select horariocalendario

$('#selectHorarioen').change(function(e){
    if($("*").hasClass("fc-highlight")){
    e.stopPropagation();

    var textSelec=$('select[name="selectHorarioen"] option:selected').text();
    var idhorar = $('#selectHorarioen').val();
    var startHen=$('#horario1em').val();
    var finHen=$('#horario2em').val();
    var idempl=$('#idobtenidoE').val();
    var diasEntreFechasen = function (desde, hasta) {
        var dia_actual = desde;
        var fechas = [];
        while (dia_actual.isSameOrBefore(hasta)) {
            fechas.push(dia_actual.format('YYYY-MM-DD'));
            dia_actual.add(1, 'days');
        }
        return fechas;
    };

    var desde = moment(startHen);
    var hasta = moment(finHen);
    console.log(desde)
    var results = diasEntreFechasen(desde, hasta);
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
        url: "/storeHorarioEmBD",
        data: {
            fechasArray: fechastart,
            hora: textSelec,
           /*  pais: idpais,
            departamento: iddepartamento, */
            idhorar:idhorar,idempl

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
            $("#selectHorarioen").val("Asignar horario");
            var mesAg2= $('#fechaDa2').val();
            var d2  =mesAg2;
            var fechasMh=new Date(d2);
            calendarioHorario(data,fechasMh);
           


        },
        error: function (data) {
            alert('Ocurrio un error');
        }


    });
   } else
   {  $("#selectHorarioen").val("Asignar horario");
    bootbox.alert({
        message: "Primero debe asignar dia(s) de calendario.",

    })
}
     })
