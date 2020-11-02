
function calendario() {
    //


    var calendarEl = document.getElementById('calendar');
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;
     var añoCal= $('#AñoOrgani').val();


var idcalendarioF=$('#selectCalendario').val();
    $.ajax({
        type: "POST",
        url: "/calendario/mostrarFCalend",
        data: {idcale:idcalendarioF},
        async: false,
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

            $('#fechaEnviF').val(data);
            $('#fechaHasta').text(moment(data).subtract(1, 'day').format('DD/MM/YYYY'));

        },
        error: function () {}})
    //
    fechaFinalO=$('#fechaEnviF').val();


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
            left: 'prev,next, today,nuevoAño',
            center: 'title',
            right: 'Asignar'
        },
        validRange: {
            start: añoCal,
            end: fechaFinalO
          },
          customButtons: {
            nuevoAño: {
                text: "+ Nuevo año",

                click: function () {
                    añoNuevo=$('#fechaEnviF').val();
                    añoAc=new Date(añoNuevo);
                    añoEnviado=añoAc.getFullYear()+1;
                    $('#textoNuevoAño').val("¿Añadir año "+añoEnviado+" al calendario actual?");
                    $('#añotNuevo').val(añoEnviado);
                    $('#añadirNuevoa').modal('show');
                }
            },
            Asignar: {
                text: "+ Asignar empleados",

                click: function () {
                    $('#nombreEmpleado').load(location.href + " #nombreEmpleado>*");
                   var nombreca= $('select[id="selectCalendario"] option:selected').text();
                    $("#nombreEmpleado > option").prop("selected",false);
                    $("#nombreEmpleado").trigger("change");
                    $("#selectEmpresarial > option").prop("selected",false);
                    $("#selectEmpresarial").trigger("change");
                   $('#textCalend').text("Calendario: "+nombreca);
                    $("#tabEmpleado").dataTable().fnDestroy();
                    /* $("#tabEmpleado").dataTable(); */
                    listaempCal();

                    $('#calendarioEmple').modal('show');
                }
            }
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
            $.each( data, function( index, value ){
                successCallback(data);
                if(value.laborable==0){
                        var element = $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date]");

                        var a = moment(value.end);
                        c=a._i;
                        var b = moment(value.start);
                        d=b._i;

                        if(a.diff(b, 'days')>1){
                            $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='"+moment(a).subtract(1, 'day').format('YYYY-MM-DD')+"']").css("backgroundColor", "#ffefef");
                        }

                        $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='"+moment(value.start).format('YYYY-MM-DD')+"']").css("backgroundColor", "#ffefef");
                }

            });

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
    color='#e6bdbd';
    textColor= '#504545';
    start= $('#pruebaStar').val();
    end= $('#pruebaEnd').val();
    tipo= 1;
    laborable=0;
    id_calendario=$('#selectCalendario').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/eventos_usuario/store",
        data: {title,color,textColor,start,end,tipo,id_calendario,laborable},
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
            console.log(data);
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
                title:"Día de descanso",
                message: "¿Agregar los cambios a los empleados asignados a este calendario?",
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
    var ideventoF;
    title= $('#nombreFeriado').val(),
    color='#e6bdbd',
    textColor= '#775555',
    start= $('#pruebaStar').val();
    end= $('#pruebaEnd').val();
    tipo= 2;
    laborable=0;
    id_calendario=$('#selectCalendario').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/eventos_usuario/store",
        data: {title,color,textColor,start,end,tipo,id_calendario,laborable},
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
            ideventoF=data;
             $('#myModalFeriado').modal('hide');

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
                title:"Día feriado",
                message: "¿Agregar los cambios a los empleados asignados a este calendario?",
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
                            data: {idevento:ideventoF,id_calendario},
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
function registrarDnlaborables()  {
    $('#calendarioAsignar').modal('hide');
    var ideventonl;
    title= 'No laborable';
    color='#a34141';
    textColor=' #ffffff ';
    start= $('#pruebaStar').val();
    end= $('#pruebaEnd').val();
    tipo= 0;
    laborable=0;
    id_calendario=$('#selectCalendario').val();
    //$('#myModal').modal('show');
    $.ajax({
        type: "POST",
        url: "/eventos_usuario/store",
        data: {title,color,textColor,start,end,tipo,id_calendario,laborable},
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
            ideventonl=data;
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
                title:"Día no laborable",
                message: "¿Agregar los cambios a los empleados asignados a este calendario?",
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
                            data: {idevento:ideventonl,id_calendario},
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
        success: function (data) {
            $('#myModalEliminarD').modal('toggle');
            var a = moment(data.end);
            c=a._i;
            var b = moment(data.start);
            d=b._i;

            if(a.diff(b, 'days')>1){
                $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='"+moment(a).subtract(1, 'day').format('YYYY-MM-DD')+"']").css("backgroundColor", "#ffffff");
            }

            $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='"+moment(data.start).format('YYYY-MM-DD')+"']").css("backgroundColor", "#ffffff");
            calendar.refetchEvents();



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
        success: function (data) {
            $('#myModalEliminarN').modal('toggle');
            var a = moment(data.end);
            c=a._i;
            var b = moment(data.start);
            d=b._i;

            if(a.diff(b, 'days')>1){
                $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='"+moment(a).subtract(1, 'day').format('YYYY-MM-DD')+"']").css("backgroundColor", "#ffffff");
            }

            $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='"+moment(data.start).format('YYYY-MM-DD')+"']").css("backgroundColor", "#ffffff");
            calendar.refetchEvents();

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
        success: function (data) {
            $('#myModalEliminarFeriado').modal('toggle');
            var a = moment(data.end);
            c=a._i;
            var b = moment(data.start);
            d=b._i;

            if(a.diff(b, 'days')>1){
                $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='"+moment(a).subtract(1, 'day').format('YYYY-MM-DD')+"']").css("backgroundColor", "#ffffff");
            }

            $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='"+moment(data.start).format('YYYY-MM-DD')+"']").css("backgroundColor", "#ffffff");
            calendar.refetchEvents();

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
   if($("#clonarCheck").is(':checked') ){
    var idcalenda=$("#selectClonar").val();
    ///
    var allValsAños = [];
        $("input[name=añosCheck]:checked").each(function () {
            allValsAños.push($(this).attr('value'));
        });
        if (allValsAños.length <= 0) {

            bootbox.alert("Por favor seleccione al menos un año");
                    return false;
                }

    $.ajax({
        type:"POST",
        url: "/calendario/registrarnuevoClonado",
        data: {
            nombrecal,idcalenda,allValsAños
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
            $('#guardarCalm').prop('disabled',true);

            $('#nombreCalen').val('');
        $('#selectCalendario').append($('<option>', { //agrego los valores que obtengo de una base de datos
            value: data.calen_id,
            text: data.calendario_nombre,
            selected: true
        }));
        $('#selectClonar').append($('<option>', { //agrego los valores que obtengo de una base de datos
            value: data.calen_id,
            text: data.calendario_nombre,
            selected: false
        }));
        calendario();
        $('#agregarCalendarioN').modal('hide');
        },
        error: function () {}
    });
   }
   else{
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
            $('#guardarCalm').prop('disabled',true);
            $('#nombreCalen').val('');
        $('#selectCalendario').append($('<option>', { //agrego los valores que obtengo de una base de datos
            value: data.calen_id,
            text: data.calendario_nombre,
            selected: true
        }));
        $('#selectClonar').append($('<option>', { //agrego los valores que obtengo de una base de datos
            value: data.calen_id,
            text: data.calendario_nombre,
            selected: false
        }));
        calendario();
        $('#agregarCalendarioN').modal('hide');
        },
        error: function () {}
    });
   }

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
    $('#guardarCalm').prop('disabled',false);
    $('#nombreCalen').val('');
    $("#añosCalen").empty();
    $('#clonarCheck').prop('checked',false);
    $('#selectClonar').val('Seleccione calendario');
     $('#selectClonar').prop('disabled',true);
    $('#agregarCalendarioN').modal('show');
}

//ckeck clonar
$("#clonarCheck").click(function () {
    if ($("#clonarCheck").is(":checked")) {
        $('#selectClonar').prop('disabled',false);
    } else {
        $('#selectClonar').prop('disabled',true);
    }
});

function editarfinC(){
    var calendfEd=$('#selectCalendario').val();
    var añoFed=$('#añotNuevo').val();

    $.ajax({
        type: "POST",
        url: "/calendario/añadirFinCalenda",
        data: {calendfEd,añoFed},
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            419: function () {
                location.reload();
            }
        },
        success: function (data) {
            calendario();
            $('#añadirNuevoa').modal('hide');
        },
        error: function () {}})

}
///////////////////////
//select todo empleado
$("#selectTodoCheck").click(function(){
    if($("#selectTodoCheck").is(':checked') ){
        $("#nombreEmpleado > option").prop("selected","selected");
        $("#nombreEmpleado").trigger("change");
    }else{
        $("#nombreEmpleado > option").prop("selected",false);
         $("#nombreEmpleado").trigger("change");
     }
});
//////////////////////
//seleccionar por area, cargo, etc
$('#selectEmpresarial').change(function(e){
    var idempresarial=[];
 idempresarial=$('#selectEmpresarial').val();
 textSelec=$('select[name="selectEmpresarial"] option:selected:last').text();
 textSelec2=$('select[name="selectEmpresarial"] option:selected:last').text();
/*  palabrasepara=textSelec2.split('.')[0];
 alert(palabrasepara);
 return false; */
 palabraEmpresarial=textSelec.split(' ')[0];
 if(palabraEmpresarial=='Area'){
    $.ajax({
        type: "post",
        url: "/horario/empleArea",
        data: {
            idarea: idempresarial
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
            $("#nombreEmpleado > option").prop("selected",false);
            $("#nombreEmpleado").trigger("change");
            $.each( data, function( index, value ){
                 $("#nombreEmpleado > option[value='"+value.emple_id+"']").prop("selected","selected");
                 $("#nombreEmpleado").trigger("change");
            });
        console.log(data);
        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });
 }
 if(palabraEmpresarial=='Cargo'){
    $.ajax({
        type: "post",
        url: "/horario/empleCargo",
        data: {
            idcargo: idempresarial
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
            $("#nombreEmpleado > option").prop("selected",false);
            $("#nombreEmpleado").trigger("change");
            $.each( data, function( index, value ){
                 $("#nombreEmpleado > option[value='"+value.emple_id+"']").prop("selected","selected");
                 $("#nombreEmpleado").trigger("change");
            });
        console.log(data);
        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });
    }

if(palabraEmpresarial=='Local'){
    $.ajax({
        type: "post",
        url: "/horario/empleLocal",
        data: {
            idlocal: idempresarial
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
            $("#nombreEmpleado > option").prop("selected",false);
            $("#nombreEmpleado").trigger("change");
            $.each( data, function( index, value ){
                    $("#nombreEmpleado > option[value='"+value.emple_id+"']").prop("selected","selected");
                    $("#nombreEmpleado").trigger("change");
            });
        console.log(data);
        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });
    }

})
/////////////////////////////////
function listaempCal(){
    var  idcalenLista=$('#selectCalendario').val();
    // DataTable
    $('#tabEmpleado').DataTable({
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando registros del _START_ al _END_ ",
            sInfoEmpty:
                "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sInfoPostFix: "",
            sSearch: "Buscar:",
            sUrl: "",
            sInfoThousands: ",",
            sLoadingRecords: "Cargando...",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: ">",
                sPrevious: "<",
            },
            oAria: {
                sSortAscending:
                    ": Activar para ordenar la columna de manera ascendente",
                sSortDescending:
                    ": Activar para ordenar la columna de manera descendente",
            },
            buttons: {
                copy: "Copiar",
                colvis: "Visibilidad",
            },
        },

       ajax: {
        type: "post",
        complete: function (data) {
            dataD=data['responseJSON'];

            $.each(dataD, function (index, value1) {
                console.log(value1.emple_id);
                $('#nombreEmpleado').find('option[value="' + value1.emple_id + '"]').remove();


            $('#nombreEmpleado').select2({});
             })
        },
        url: "/calendario/listaEmplCa",
        data: {
            idcalendar: idcalenLista
        },
        statusCode: {

            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        "dataSrc": ""
       },

       columns: [
          { data: "perso_nombre" },
          { data: "perso_apPaterno" },
          { data: "perso_apMaterno" },

       ]
    });
}
function asignarCalendario(){
    var allVals = [];


        $("input[name=idEmSelecto]:checked").each(function () {
            allVals.push($(this).attr('data-id'));
        });
        var join_selected_values = allVals.join(",");
        var  idemples = allVals;
         console.log(allVals);
         console.log(join_selected_values);
         console.log(idemples);


         if (allVals.length <= 0) {

            bootbox.alert("Por favor seleccione una fila");
                    return false;
                }
    var  idcalenReg=$('#selectCalendario').val();
    $("#asignacionCa").hide();
    $("#espera").show();
    $.ajax({
        type: "post",
        url: "/calendario/asignarCalendario",
        data: {
            idcalenReg,idemples
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
            $("#tabEmpleado").dataTable().fnDestroy();
            listaempCal();
            $("#nombreEmpleado > option").prop("selected",false);
            $("#nombreEmpleado").trigger("change");
            $("#selectEmpresarial > option").prop("selected",false);
            $("#selectEmpresarial").trigger("change");
            $("#espera").hide();
            $("#asignacionCa").show();
            $("#empleadosSele>tbody>tr").remove();
            $("input[type=checkbox]").prop("checked",false);

        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });

}
/* function mostrarSelect() {
    var idselec=$( "#nombreEmpleado" ).val();
   alert(idselec);
        } */
$('#nombreEmpleado').change(function(){
    num=$('#nombreEmpleado').val().length;
    if(num<1){
        $("#empleadosSele>tbody>tr").remove();
    }
    var idsemp=$(this).val();
    $.ajax({
        type: "POST",
        url: "/calendario/seleccionados",
        dataType: "json",
        data: {ids:idsemp},
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
            $("#empleadosSele>tbody>tr").remove();
            $.each(data, function (key, item) {
                if(item[0].calendario_nombre==null){
                    $("#empleadosSele>tbody").append(
                        "<tr id='"+item[0].emple_id+"' ><td> <input type='checkbox' class='ml-4' name='idEmSelecto' id='idEmSelecto' data-id='"+item[0].emple_id+"'>&nbsp;&nbsp; <a style='cursor: pointer' class='borrar' onclick='quitars("+item[0].emple_id+")'  ><img style='margin-bottom: 6px;' src='admin/images/delete.svg' height='15'></a> </td>  <td >"+item[0].perso_nombre+
                        "</td> <td >"+item[0].perso_apPaterno+"</td><td>"+item[0].perso_apMaterno+"</td><td>Ninguno</td></tr>"
                        );
                }else{
                   $("#empleadosSele>tbody").append(
                    "<tr id='"+item[0].emple_id+"' ><td> <input type='checkbox' class='ml-4' name='idEmSelecto' id='idEmSelecto' data-id='"+item[0].emple_id+"'>&nbsp;&nbsp; <a style='cursor: pointer' onclick='quitars("+item[0].emple_id+")' class='borrar' ><img style='margin-bottom: 6px;' src='admin/images/delete.svg' height='15'></a> </td>  <td >"+item[0].perso_nombre+
                    "</td> <td >"+item[0].perso_apPaterno+"</td><td>"+item[0].perso_apMaterno+"</td><td>"+
                    item[0].calendario_nombre+"</td></tr>"
                    );
                }

            })



        },
        error: function () {}})
});

$('#selectEmps').click(function(event) {
    if(this.checked) {
        // Iterate each checkbox
        $('input[name=idEmSelecto]').each(function() {
            this.checked = true;
        });
    } else {
        $('input[name=idEmSelecto]').each(function() {
            this.checked = false;
        });
    }
});

   function quitars(data) {
    $('#'+data+'').remove();
    $("#nombreEmpleado option[value="+ data +"]").prop("selected",false);
      $("#nombreEmpleado").trigger("change");
        console.log(data);

    };

    $('#selectClonar').change(function (){
        $("#añosCalen").empty();
        var idcalendaSel=$("#selectClonar").val();
        $.ajax({
            type: "POST",
            url: "/calendario/yearCale",
            data: {idcale:idcalendaSel},
            async: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                419: function () {
                    location.reload();
                }
            },
            success: function (data) {
                $.each( data, function( index, value ){
                    $("#añosCalen").append(
                        " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox'  class='form-check-input' id='añosCheck' name='añosCheck' value='"+value+"'>"+
                        "<label class='form-check-label' for='' style='margin-top: 2px;' >"+value+"</label>&nbsp;&nbsp;&nbsp;"
                        );
               });


            },
            error: function () {}})
    })
