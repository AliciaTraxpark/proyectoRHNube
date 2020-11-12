$(document).ready(function () {
    $(".bt_plus").each(function (el) {
        $(this).bind("click", addField);
    });
    var table = $("#tablaEmpleado").DataTable({
        "searching": true,
        /* "lengthChange": false,
         "scrollX": true, */
        "processing": true,

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
            url: "/horario/listar",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            "dataSrc": ""
        },

        "columnDefs": [{
            "searchable": false,
            "orderable": false,
            "targets": 0
        }
        ],
        "order": [[1, 'asc']],
        columns: [
            { data: null },
            { data: "horario_descripcion" },
            {
                data: "horario_tolerancia",
                "render": function (data, type, row) {

                    return row.horario_tolerancia + '&nbsp;&nbsp; minutos';

                }
            },
            { data: "horaI",
            "render": function (data, type, row) {
                return "&nbsp;&nbsp;&nbsp"+ row.horaI;
            }
             },
            { data: "horaF",
            "render": function (data, type, row) {
                return "&nbsp;&nbsp;"+ row.horaF;
            }
             },
            {
                data: "horario_horario_id",
                "render": function (data, type, row) {
                    if (row.horario_horario_id == null) {
                        return '<img src="admin/images/borrarH.svg" height="11" />&nbsp;&nbsp;No';
                    }
                    else {
                        return '<img src="admin/images/checkH.svg" height="13" />&nbsp;&nbsp;Si';
                    }
                }
            },
            {
                data: "horario_id",
                "render": function (data, type, row) {

                    return '<a onclick=" editarHorarioLista(' + row.horario_id + ')" style="cursor: pointer"><img src="/admin/images/edit.svg" height="15"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="" style="cursor: pointer">' +
                        '<img src="/admin/images/delete.svg" onclick="eliminarHorario(' + row.horario_id + ')" height="15"></a>';

                }
            },

        ]


    });
    //$('#verf1').hide();
    //$('#tablaEmpleado tbody #tdC').css('display', 'none');
    table.on('order.dt search.dt', function () {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
    $("#tablaEmpleado tbody tr").hover(function () {
        //$('#verf1').css('display', 'block');
        $('#tablaEmpleado tbody #tdC').css('display', 'block');

    }, function () {

    });

    $('#form-ver').hide();
    $('#divFfin').hide();


    $('#Datoscalendar1').css("display", "none");
    $('#aplicarHorario').prop('disabled', true);

    $('.flatpickr-input[readonly]').on('focus', function () {
        $(this).blur()
    })
    $('.flatpickr-input[readonly]').prop('readonly', false)
});





function verhorarioEmpleado(idempleado) {
    $("*").removeClass("fc-highlight");
    $.get("/vaciartemporal", {}, function (data, status) {
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
                var ano3 = fechah.getFullYear();
                var mes3 = fechah.getMonth() + 1;
                fechas1 = ano3 + '-' + mes3 + '-01';
                var fechasMh = new Date(fechas1);
                calendarioHorario(data[1], fechasMh);
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
                if (data[2] != 0) {
                    $.each(data[2], function (key, item) {
                        $("#tablahorarios>tbody").append(
                            "<tr ><td style='padding: 4px;'>" + item.title +
                            "</td> <td style='padding: 4px;'>" + item.horaI + "</td><td style='padding: 4px;'>" + item.horaF + "</td></tr>"
                        );
                    });
                }

            },
            error: function (data) {
                alert('Ocurrio un error');
            }

        });

    });



}
//CALENDARIO HORARIO
/* function calendarioHorario(eventosEmpleado,fechasMh) {
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

        },

        events: eventosEmpleado,
    }
    var calendar = new FullCalendar.Calendar(calendarElH, configuracionCalendarioH);
    calendar.setOption('locale', "Es");
    ////
    calendar.render();

}
document.addEventListener('DOMContentLoaded', calendarioHorario); */
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





$('#btnasignar').on('click', function (e) {
    $('#divOtrodia').hide();
    $('input[type=checkbox]').prop('checked', false);
    $.get("/vaciartemporal", {}, function (data, status) {
        calendar.refetchEvents();
        $("#nombreEmpleado > option").prop("selected", false);
        $("#nombreEmpleado").trigger("change");
        $("#selectEmpresarial > option").prop("selected", false);
        $("#selectEmpresarial").trigger("change");
        $('#Datoscalendar').show();
        $('#Datoscalendar1').hide();
        $('#asignarHorario').modal('toggle');

    });
    /*   $("#formulario")[0].reset(); */

    num = $('#nombreEmpleado').val().length;
    idemplesH = $('#nombreEmpleado').val();
    var ideHor = [];
    ideHor.push(idemplesH);

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
            /*  json1 = JSON.parse(JSON.stringify(data));

             for (var i in json1) {

             $('#nombreEmpleado').append('<option value="' + json1[i].emple_id + '" >' + json1[i].perso_nombre + " " + json1[i].perso_apPaterno + '</option>');
              }


              if (allVals.length > 0) {

                 $.each( allVals, function( index, value ){
                     $("#nombreEmpleado option[value='"+ value +"']").attr("selected",true);
                 });
                 num2=$('#nombreEmpleado').val().length;

             } */

        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });
});
//CALENDARIO//

function calendario() {
    var calendarEl = document.getElementById('calendar');
    calendarEl.innerHTML = "";
    var fechasM = fechasM;
    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: 'es',
        //defaultDate: ano + '-01-01',
        defaultDate: fecha,
        height: "auto",
        contentHeight: 440,
        fixedWeekCount: false,
        plugins: ['dayGrid', 'interaction', 'timeGrid'],
        unselectAuto: false,
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
            finH = moment(arg.end).format('YYYY-MM-DD HH:mm:ss');
            startH = moment(arg.start).format('YYYY-MM-DD HH:mm:ss');
            console.log(arg);
            var date1 = calendar.getDate();
            $('#fechaDa').val(date1);
            $('#horario1').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));
            $('#horario2').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
            $("#selectHorario").val("Asignar horario");
            $('#errorSel').hide();
            $("#selectHorario").trigger("change");
            $('#fueraHSwitch').prop('checked', true)
            $('#nHorasAdic').hide();
            $('#fueraHSwitch').prop('disabled', false);
            $('#horAdicSwitch').prop('checked', false)
            $('#horCompSwitch').prop('checked', true)
            $('#horarioAsignar_ed').modal('show');

        },
        eventClick: function (info) {
            id = info.event.id;
            console.log(info);
            console.log(info.event.id);
            console.log(info.event.textColor);
            console.log(info.event.borderColor);
            var event = calendar.getEventById(id);
            if (info.event.textColor == '111111') {
                bootbox.confirm({
                    title:"Eliminar horario",
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
            }


            //info.event.remove();
        },
        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next',
            center: 'title',
            right: ''
        },
        eventRender: function (info) {
            $('.tooltip').remove();
            if (info.event.extendedProps.horaI === null) {
                $(info.el).tooltip({ title: info.event.title });
            } else {
                if (info.event.borderColor == '#5369f8') {
                    if(info.event.extendedProps.horaAdic==1){
                        $(info.el).tooltip({  title: info.event.extendedProps.horaI+'-'+info.event.extendedProps.horaF+'  Trabaja fuera de horario'+'     Marca horas adicionales'});
                    } else{
                        $(info.el).tooltip({  title: info.event.extendedProps.horaI+'-'+info.event.extendedProps.horaF+'  Trabaja fuera de horario'});
                    }

                }
                else {
                    $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF });
                }
            }
            /*if(info.event.borderColor=='#5369f8'){
             $(info.el).tooltip({  title: info.event.extendedProps.horaI+'-'+info.event.borderColor});
          }*/
        },
        events: function (info, successCallback, failureCallback) {

            $.ajax({
                type: "get",
                url: "/eventosHorario",
                data: {

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
                error: function () { }
            });

        },
    }

    calendar = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    var date1 = calendar.getDate();
    $('#fechaDa').val(date1);

    calendar.setOption('locale', "Es");
    ////
    calendar.render();
    /*  $("#calendar > div.fc-toolbar.fc-header-toolbar > div.fc-right").html(); */
}
function agregarHorarioSe() {
    if ($("*").hasClass("fc-highlight")) {

        textSelec1 = $('select[name="selectHorario"] option:selected').text();
        separador = "(";
        textSelec2 = textSelec1.split(separador);
        textSelec = textSelec2[0];

        var idhorar = $('#selectHorario').val();
        console.log(idhorar);
        if (idhorar == null) {
            $('#errorSel').show();
            return false;
        } else {
            $('#errorSel').hide();
        }
        var fueraHora;
        if ($('#fueraHSwitch').prop('checked')) {
            fueraHora = 1;
            console.log(fueraHora);
        } else {
            fueraHora = 0;
            console.log(fueraHora);
        }

        // HORARIO COMPENSABLE
        var horarioC;
        if ($('#horCompSwitch').prop('checked')) {
            horarioC = 1;

        } else {
            horarioC = 0;

        }

        // HORA ADICIONAL
        var horarioA;
        if ($('#horAdicSwitch').prop('checked')) {
            horarioA = 1;
            var nHoraAdic=$('#nHorasAdic').val();
        } else {
            horarioA = 0;
            var nHoraAdic=null;
        }
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
        console.log(horarioC);
        console.log(fechasArray);



        $.ajax({
            type: "post",
            url: "/guardarEventos",
            data: {
                fechasArray: fechastart,
                hora: textSelec,
                idhorar: idhorar, fueraHora,
                horaC: horarioC,
                horaA:horarioA,nHoraAdic

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
                $('#horarioAsignar_ed').modal('hide');
                $("#selectHorario").val("Asignar horario");
                $("#selectHorario").trigger("change");
                $('#horCompSwitch').prop("checked", false);
                $('#horAdicSwitch').prop("checked", false);
                var mesAg = $('#fechaDa').val();
                var d = mesAg;
                var fechasM = new Date(d);
                calendar.refetchEvents();


            },
            error: function (data) {
                alert('Ocurrio un error');
            }


        });
    } else {
        $("#selectHorario").val("Asignar horario");
        $("#selectHorario").trigger("change");
        bootbox.alert({
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }
};


document.addEventListener('DOMContentLoaded', calendario);

///////////////////////////////


//////////////////////
$('#guardarHorarioEventos').click(function () {
    $('#guardarHorarioEventos').prop('disabled', true);
    var idemps = [];
    idempleads = $('#idobtenidoE').val();
    idemps.push(idempleads);
    descripcion = $('#descripcionCaHorario').val();
    nuevaTolerancia = $('#nuevaTolerancia').val();
    $.ajax({
        type: "post",
        url: "/guardarHorario",
        data: {
            idemps, descripcion, toleranciaH: nuevaTolerancia
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {

            $('#guardarHorarioEventos').prop('disabled', false);
            $('#tablaEmpleado').DataTable().ajax.reload();
            $('#verhorarioEmpleado').modal('toggle');
            calendar.refetchEvents();


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
            title:"Seleccionar empleado",
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
            401: function () {
                location.reload();
            },
            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            $('#tablaEmpleado').DataTable().ajax.reload();
            $('#guardarTodoHorario').prop('disabled', false);

            $('#asignarHorario').modal('toggle');
            calendar.refetchEvents();


        },
        error: function () {
            alert("Hay un error");
        }
    });

})

$('#customSwitch1').change(function (event) {
    if ($('#customSwitch1').prop('checked')) {
        $('#divFfin').show();
        $('#divhora').hide();

    }
    else {
        $('#divFfin').hide();
        $('#divhora').show();
    }
    event.preventDefault();
});
$('#btnasignarIncidencia').on('click', function (e) {
    $("#frmIncidencia")[0].reset();
    $('#divFfin').hide();
    $('#divhora').show();
    $('#empIncidencia').empty();
    $('#asignarIncidencia').modal('toggle');
    $.get("empleadoIncHorario", {}, function (data, status) {
        jsonIn = JSON.parse(JSON.stringify(data));
        for (var i in jsonIn) {

            $('#empIncidencia').append('<option value="' + jsonIn[i].emple_id + '" >' + jsonIn[i].perso_nombre + " " + jsonIn[i].perso_apPaterno + '</option>');

        }

    });
});
function registrarIncidencia() {
    idempleadoI = $('#empIncidencia').val();
    descripcionI = $('#descripcionInci').val();
    var descuentoI;
    if ($('#descuentoCheck').prop('checked')) {
        descuentoI = 1;
    } else { descuentoI = 0 }
    fechaI = $('#fechaI').val();
    fechaFin = $('#fechaF').val();
    fechaMoment = moment(fechaFin).add(1, 'day');
    fechaF = fechaMoment.format('YYYY-MM-DD');

    var horaIn;
    if ($('#customSwitch1').prop('checked')) {
        horaIn = null;
    } else {

        fechaF = null;
    }
    $.ajax({
        type: "post",
        url: "/registrarInci",
        data: { idempleadoI, descripcionI, descuentoI, fechaI, fechaF  },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            }
        },
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (data) {
            $('#asignarIncidencia').modal('hide');
        },
        error: function (data) {
            alert('Ocurrio un error');
        }
    });


    ;
}
function marcarAsignacion(data) {
    $('input:checkbox').prop('checked', false);

    $('input:checkbox[data-id=' + data + ']').prop('checked', true);
    $('#btnasignar').click();
}
$('#cerrarHorario').click(function () {
    $('#tablaEmpleado').DataTable().ajax.reload();
    $('#verhorarioEmpleado').modal('toggle');
});
function abrirHorario() {
    $('#errorenPausas').hide();
      $('#fueraRango').hide();
    $('#divOtrodia').hide();
    $('#divPausa').hide();
    $('#horaOblig').prop("disabled","disabled");
    $('#inputPausa').empty();
    $('#inputPausa').append('<div id="div_100" class="row col-md-12" style=" margin-bottom: 8px;">' +
        '<input type="text"  class="form-control form-control-sm col-sm-5" name="descPausa[]" id="descPausa" >' +
        '<input type="text"  class="form-control form-control-sm col-sm-3" name="InicioPausa[]"  id="InicioPausa" >' +
        '<input type="text"  class="form-control form-control-sm col-sm-3" name="FinPausa[]"  id="FinPausa" disabled >' +
        '&nbsp; <button class="btn btn-sm bt_plus" id="100" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
        ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>' +
        '</div>');
    $('.flatpickr-input[readonly]').on('focus', function () {
        $(this).blur()
    })
    $('.flatpickr-input[readonly]').prop('readonly', false)
    $(".bt_plus").each(function (el) {
        $(this).bind("click", addField);
    });
    $('#InicioPausa').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour:null
    });

    $('input[name="descPausa[]"]').prop('required', false);
    $('input[name="InicioPausa[]"]').prop('required', false);
    $('input[name="FinPausa[]"]').prop('required', false);
    $("#frmHor")[0].reset();

    $('#horarioAgregar').modal('show');


}
function abrirHorarioen() {
    if ($("*").hasClass("fc-highlight")) {
        $("#frmHoren")[0].reset();
        $('#horarioAgregaren').modal('show');
    } else {
        bootbox.alert({
            title:"Seleccionar dias",
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }

}
function registrarHorario() {


    descripcion = $('#descripcionCa').val();
    toleranciaH = $('#toleranciaH').val();
    toleranciaF = $('#toleranciaSalida').val();
    horaOblig = $('#horaOblig').val();
    inicio = $('#horaI').val();
    fin = $('#horaF').val();
    var tardanza;
    if ($('#SwitchTardanza').is(":checked")) {
        tardanza=1;
    } else{
        tardanza=0;
    }

    if ($('#SwitchPausa').is(":checked")) {
        var descPausa = [];
        var pausaInicio = [];
        var finPausa = [];
        $('input[name="descPausa[]"]').each(function () {
            descPausa.push($(this).val());
        });
        $('input[name="InicioPausa[]"]').each(function () {
            pausaInicio.push($(this).val());
        });
        $('input[name="FinPausa[]"]').each(function () {
            finPausa.push($(this).val());
        });
    }



    $.ajax({
        type: "post",
        url: "/guardarHorario",
        data: {

            descripcion,
            toleranciaH, inicio, fin, descPausa, pausaInicio, finPausa, toleranciaF, horaOblig, tardanza
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {

            $('#horarioAgregar').modal('hide');
            $('#tablaEmpleado').DataTable().ajax.reload();

            if ($('#asignarHorario').is(':visible')) {
                $('#selectHorario').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.horario_id,
                    text: data.horario_descripcion + ' (' + data.horaI + '-' + data.horaF + ')',
                    selected: true

                }));
            } else {
                $('#selectHorario').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: data.horario_id,
                    text: data.horario_descripcion + ' (' + data.horaI + '-' + data.horaF + ')'

                }));
            }


        },
        error: function () {
            alert("Hay un error");
        }
    });
}
function registrarhDias(idhorar) {
    H1 = $('#horario1').val();
    H2 = $('#horario2').val();

    var fueraHora;
    if ($('#fueraHSwitch').prop('checked')) {
        fueraHora = 1;
        console.log(fueraHora);
    } else {
        fueraHora = 0;
        console.log(fueraHora);
    }
    textSelec = $('#descripcionCa').val();
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
        url: "/guardarEventos",
        data: {
            fechasArray: fechastart,
            hora: textSelec,

            idhorar: idhorar, fueraHora
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



        },
        error: function (data) {
            alert('Ocurrio un error');
        }


    });

}
function registrarHorarioen() {
    if ($('#exampleCheck1en').prop('checked')) {
        var sobretiempo = 1;
    } else {
        var sobretiempo = 0;
    }
    var tipHorario = $('#tipHorarioen').val();
    var descripcion = $('#descripcionCaen').val();
    var toleranciaH = $('#toleranciaHen').val();
    var inicio = $('#horaIen').val();
    var fin = $('#horaFen').val();

    $.ajax({
        type: "post",
        url: "/guardarHorario",
        data: {
            sobretiempo,
            tipHorario,
            descripcion,
            toleranciaH, inicio, fin
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            419: function () {
                location.reload();
            }
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            H1 = $('#horario1em').val();
            H2 = $('#horario2em').val();
            idhorar = data.horario_id;
            /* idpais = $('#pais').val();
            iddepartamento = $('#departamento').val(); */
            textSelec = $('#descripcionCaen').val();
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
            idempl = $('#idobtenidoE').val();
            console.log(fechasArray);
            $.ajax({
                type: "post",
                url: "/storeHorarioEmBD",
                data: {
                    fechasArray: fechastart,
                    hora: textSelec,
                    /*  pais: idpais,
                     departamento: iddepartamento, */
                    idhorar: idhorar, idempl
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
                    $("#selectHorarioen").trigger("change");
                    var mesAg2 = $('#fechaDa2').val();
                    var d2 = mesAg2;
                    var fechasMh = new Date(d2);
                    calendarioHorario(data, fechasMh);
                    $('#horarioAgregaren').modal('hide');
                    $.ajax({
                        type: "post",
                        url: "/verDataEmpleado",
                        data: 'ids=' + idempl,
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
                            $.each(data[2], function (key, item) {
                                $("#tablahorarios>tbody").append(
                                    "<tr ><td style='padding: 4px;'>" + item.title +
                                    "</td> <td style='padding: 4px;'>" + item.horaI + "</td><td style='padding: 4px;'>" + item.horaF + "</td></tr>"
                                );
                            });
                        },
                        error: function (data) {
                            alert('Ocurrio un error');
                        }

                    });

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
            $('#selectHorario').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.horario_id,
                text: data.horario_descripcion

            }));
        },
        error: function () {
            alert("Hay un error");
        }
    });
}
function asignarlabo() {
    var H1 = $('#horario1').val();
    var H2 = $('#horario2').val();
    if ($("*").hasClass("fc-highlight")) {
        bootbox.confirm({
            title:"Agregar dias",
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
                    var iddepartamento = $('#departamento').val();
                    $.ajax({
                        type: "post",
                        url: "/storeLaborable",
                        data: {
                            start: H1,
                            title: 'Día laborable.',
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
                            var mesAg = $('#fechaDa').val();
                            var d = mesAg;
                            var fechasM = new Date(d);
                            calendar.refetchEvents();


                        },
                        error: function (data) {
                            alert('Ocurrio un error');
                        }


                    });
                }
            }
        });
    } else {
        bootbox.alert({
            title:"Asignar dias",
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }


}
function asignarlaboen() {
    var H1 = $('#horario1em').val();
    var H2 = $('#horario2em').val();
    var idempl = $('#idobtenidoE').val();
    if ($("*").hasClass("fc-highlight")) {
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
                            title: 'Día laborable.',
                            end: H2, idempl

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
                            var mesAg2 = $('#fechaDa2').val();
                            var d2 = mesAg2;
                            var fechasMh = new Date(d2);
                            calendarioHorario(data, fechasMh);


                        },
                        error: function (data) {
                            alert('Ocurrio un error');
                        }


                    });
                }
            }
        });
    } else {
        bootbox.alert({
            title:"Asignar dias",
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }


}
function asignarNolabo() {
    H1 = $('#horario1').val();
    H2 = $('#horario2').val();
    if ($("*").hasClass("fc-highlight")) {
        bootbox.confirm({
            title:"Agregar dias",
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
                            var mesAg = $('#fechaDa').val();
                            var d = mesAg;
                            var fechasM = new Date(d);
                            calendar.refetchEvents();

                        },
                        error: function (data) {
                            alert('Ocurrio un error');
                        }


                    });
                }
            }
        });
    }
    else {
        bootbox.alert({
            title:"Asignar dias",
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }

}
function asignarNolaboen() {
    var H1 = $('#horario1em').val();
    var H2 = $('#horario2em').val();
    var idempl = $('#idobtenidoE').val();
    if ($("*").hasClass("fc-highlight")) {
        bootbox.confirm({
            title:"Asignar dias",
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
                            end: H2, idempl

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
                            var mesAg2 = $('#fechaDa2').val();
                            var d2 = mesAg2;
                            var fechasMh = new Date(d2);
                            calendarioHorario(data, fechasMh);


                        },
                        error: function (data) {
                            alert('Ocurrio un error');
                        }


                    });
                }
            }
        });
    } else {
        bootbox.alert({
            title:"Asignar dias",
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }


}
function asignarInci() {
    if ($("*").hasClass("fc-highlight")) {
        $("#frmIncidenciaHo")[0].reset();
        $('#divFfin').hide();
        $('#divhora').show();
        $('#empIncidencia').empty();
        $('#asignarIncidenciaHorario').modal('toggle');
    }
    else {
        bootbox.alert({
            title:"Asginar dias",
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }

}
function asignarInciEmp() {
    if ($("*").hasClass("fc-highlight")) {
        $("#frmIncidenciaHoEm")[0].reset();

        $('#divhora').show();
        $('#empIncidencia').empty();
        $('#asignarIncidenciaHorarioEmp').modal('toggle');
    }
    else {
        bootbox.alert({
            title:"Asignar dias",
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }

}
function registrarIncidenciaHoEm() {
    var descripcionI = $('#descripcionInciHoEm').val();
    var descuentoI;
    var idempl = $('#idobtenidoE').val();
    if ($('#descuentoCheckHoEm').prop('checked')) {
        descuentoI = 1;
    } else { descuentoI = 0 }
    var fechaI = $('#horario1em').val();
    var fechaFin = $('#horario2em').val();

    /*  idpais = $('#pais').val();
     iddepartamento = $('#departamento').val(); */
    $.ajax({
        type: "post",
        url: "/storeIncidenciaEmpleado",
        data: {
            start: fechaI,
            title: descripcionI, descuentoI: descuentoI,
            /* pais: idpais,
            departamento: iddepartamento, */
            end: fechaFin,

            idempl


        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            var mesAg2 = $('#fechaDa2').val();
            var d2 = mesAg2;
            var fechasMh = new Date(d2);
            calendarioHorario(data, fechasMh);

            $('#asignarIncidenciaHorarioEmp').modal('toggle');

        },
        error: function (data) {
            alert('Ocurrio un error');
        }


    });

}
function registrarIncidenciaHo() {
    descripcionI = $('#descripcionInciHo').val();
    var descuentoI;
    if ($('#descuentoCheckHo').prop('checked')) {
        descuentoI = 1;
    } else { descuentoI = 0 }
    fechaI = $('#horario1').val();
    fechaFin = $('#horario2').val();

    idpais = $('#pais').val();
    iddepartamento = $('#departamento').val();
    $.ajax({
        type: "post",
        url: "/storeIncidencia",
        data: {
            start: fechaI,
            title: descripcionI,
            pais: idpais, descuentoI: descuentoI,
            departamento: iddepartamento,
            end: fechaFin,


        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            var mesAg = $('#fechaDa').val();
            var d = mesAg;
            var fechasM = new Date(d);
            calendar.refetchEvents();
            $('#asignarIncidenciaHorario').modal('toggle');

        },
        error: function (data) {
            alert('Ocurrio un error');
        }


    });

}
function vaciarcalendario() {
    bootbox.confirm({
        title:"Vacear calendario",
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
                    var mesAg = $('#fechaDa').val();
                    var d = mesAg;
                    var fechasM = new Date(d);
                    calendar.refetchEvents();
                });

            }
        }
    });

}
function vaciarhor() {
    bootbox.confirm({
        title:"Elminar horario",
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
                    var mesAg = $('#fechaDa').val();
                    var d = mesAg;
                    var fechasM = new Date(d);
                    calendar.refetchEvents();
                });

            }
        }
    });

}
function vaciardl() {
    bootbox.confirm({
        title:"Eliminar dias",
        message: "¿Esta seguro que desea eliminar dias laborables del calendario?",
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
                $.get("/vaciardl", {}, function (data, status) {
                    var mesAg = $('#fechaDa').val();
                    var d = mesAg;
                    var fechasM = new Date(d);
                    calendar.refetchEvents();
                });

            }
        }
    });

}
function vaciarndl() {
    bootbox.confirm({
        title:"Eliminar dias no laborales",
        message: "¿Esta seguro que desea eliminar dias no laborables del calendario?",
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
                $.get("/vaciarndl", {}, function (data, status) {
                    var mesAg = $('#fechaDa').val();
                    var d = mesAg;
                    var fechasM = new Date(d);
                    calendar.refetchEvents();
                });

            }
        }
    });

}
function vaciarinH() {
    $.ajax({
        type: "get",
        url: "/horario/incidenciatemporal",

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
            $("#tablaBorrarI>tbody>tr").remove();
            if (data != '') {
                $.each(data, function (key, item) {
                    if (item.temp_horaF == 0) {
                        $("#tablaBorrarI>tbody").append(
                            "<tr id='r" + item.id + "'><td style='padding: 4px;'>" + item.title +
                            " </td><td style='padding: 4px;'>Sin descuento</td><td style='padding: 4px;'><a style='cursor: pointer' onclick='eliminarinctemporal(" + item.id + ")' ><img src='admin/images/delete.svg' height='15'></a> </td></tr>"
                        );
                    } else {
                        $("#tablaBorrarI>tbody").append(
                            "<tr id='r" + item.id + "'><td style='padding: 4px;'>" + item.title +
                            " </td><td style='padding: 4px;'>Con descuento</td><td style='padding: 4px;'><a style='cursor: pointer' onclick='eliminarinctemporal(" + item.id + ")' ><img src='admin/images/delete.svg' height='15'></a> </td></tr>"
                        );
                    }

                });
            } else {
                $("#tablaBorrarI>tbody").append(
                    "<tr><td style='padding: 4px;'>No hay incidencias  asignadas.<td></tr>"
                );
            }

        },
        error: function (data) {
            alert('Ocurrio un error');
        }

    });
    $('#borrarincide').modal('show');




}

function eliminarinctemporal(idinc) {

    idinc = idinc;
    $.ajax({
        type: "post",
        url: "/eliminarinctempotal",
        data: { idinc },
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

            $('#r' + idinc).remove();
            var mesAg = $('#fechaDa').val();
            var d = mesAg;
            var fechasM = new Date(d);
            calendar.refetchEvents();

        },
        error: function (data) {
            alert('Ocurrio un error');
        }

    });
}

// change select horariocalendario

$('#selectHorarioen').change(function (e) {
    if ($("*").hasClass("fc-highlight")) {
        e.stopPropagation();

        var textSelec = $('select[name="selectHorarioen"] option:selected').text();
        var idhorar = $('#selectHorarioen').val();
        var startHen = $('#horario1em').val();
        var finHen = $('#horario2em').val();
        var idempl = $('#idobtenidoE').val();
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
                idhorar: idhorar, idempl

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
                $("#selectHorarioen").trigger("change");
                var mesAg2 = $('#fechaDa2').val();
                var d2 = mesAg2;
                var fechasMh = new Date(d2);
                calendarioHorario(data, fechasMh);
                $.ajax({
                    type: "post",
                    url: "/verDataEmpleado",
                    data: 'ids=' + idempl,
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
                        $.each(data[2], function (key, item) {
                            $("#tablahorarios>tbody").append(
                                "<tr ><td style='padding: 4px;'>" + item.title +
                                "</td> <td style='padding: 4px;'>" + item.horaI + "</td><td style='padding: 4px;'>" + item.horaF + "</td></tr>"
                            );
                        });
                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }

                });


            },
            error: function (data) {
                alert('Ocurrio un error');
            }


        });
    } else {
        $("#selectHorarioen").val("Asignar horario");
        $("#selectHorarioen").trigger("change");
        bootbox.alert({
            title:"Asignar dias",
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }
})
/* function asignardomingo(){

    $( "div.fc-bg > table > tbody > tr > td" ).addClass( "fc-highlight" );
    $('#horarioEnd').val(moment(2020-07-05.end).format('YYYY-MM-DD HH:mm:ss'));
            $('#horarioStart').val(moment(2020-07-09.start).format('YYYY-MM-DD HH:mm:ss'));
            f1 = $('#horarioStart').val();
            f2 = $('#horarioEnd').val();
            inicio = $('#horaI').val();
            fin = $('#horaF').val();

}
 */
function editarHorarioLista(idsedit) {
    $('#SwitchPausa_ed').prop('disabled', false);
    $('#pausas_edit').hide();
    $("#PausasHorar_ed").empty();
    $('#divOtrodia_ed').hide();
    $('#errorenPausas_ed').hide();
      $('#fueraRango_ed').hide();
    $("#frmHorEditar")[0].reset();
    $.ajax({
        type: "post",
        url: "/verDatahorario",
        data: { idsedit },
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
            //HORAS OBLIGADAS
           var valorObli= data[0].horasObliga;

            if(valorObli!=null ||valorObli!=' '){
                $('#horaOblig_ed').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultDate:data[0].horasObliga.substr(-20,5)
                });
                $('#horaOblig_ed').val( data[0].horasObliga.substr(-20,5));
            } else{
                console.log('reconoce vacio');
                $('#horaOblig_ed').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultDate:"08:00"
                });
            }


            //HORA IINICIO
            $('#horaI_ed').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate:data[0].horaI.substr(-20,5)
            });
            $('#horaI_ed').val(data[0].horaI.substr(-20,5));
            //HORA FIN
            $('#horaF_ed').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate:data[0].horaF.substr(-20,5)
            });
            $('#horaF_ed').val(data[0].horaF.substr(-20,5));
            $('#idhorario_ed').val(data[0].horario_id);
            $('#descripcionCa_ed').val(data[0].horario_descripcion);
            $('#toleranciaH_ed').val(data[0].horario_tolerancia);
            $('#toleranciaSalida_ed').val(data[0].horario_toleranciaF);
           /*  $('#horaOblig_ed').val(data[0].horasObliga); */


            if(data[0].hora_contTardanza==1){
                $('#SwitchTardanza_ed').prop('checked', true);
            } else{
                $('#SwitchTardanza_ed').prop('checked', false);
            }
            $('#horarioEditar').modal('show');
            if (data[0].horaI > data[0].horaF) {
                $('#divOtrodia_ed').show();
            } else {
                $('#divOtrodia_ed').hide();
            }
            if (data[1] == null || data[1] == '') {

            }
            else {
                $('#pausas_edit').show();
                $.each(data[1], function (key, item) {
                    $('#SwitchPausa_ed').prop('checked', true);
                    $('#SwitchPausa_ed').prop('disabled', false);
                    $("#PausasHorar_ed").append('<div  id="divEdReg_'+ item.idpausas_horario +'" class="row col-md-12" style=" margin-bottom: 8px;">' +
                        '<input type="text"  value="' + item.pausH_descripcion + '"  class="form-control form-control-sm col-sm-5" name="descPausa_edRegist[]" required >' +
                        '<input type="text"  value="' + item.pausH_Inicio + '"   class="form-control form-control-sm col-sm-3" name="InicioPausa_edReg[]" id="InicioPausa_edReg'+ item.idpausas_horario +'" required  >' +
                        '<input type="text" value="' + item.pausH_Fin + '"   class="form-control form-control-sm col-sm-3" name="FinPausa_edReg[]" id="FinPausa_edReg'+ item.idpausas_horario +'" required >' +
                        '<input type="hidden" value="' + item.idpausas_horario + '"   class="form-control form-control-sm col-sm-3" name="idPausasRegistradas_ed[]" required  >' +

                        '</div>');
                      /*  */

                      $(function () {
                        $(document).on('change', '#InicioPausa_edReg'+ item.idpausas_horario +'', function (event) {
                            let horaF = $('#FinPausa_edReg'+ item.idpausas_horario +'').val();
                            let horaI = $('#InicioPausa_edReg'+ item.idpausas_horario +'').val();
                            $('#FinPausa_edReg'+ item.idpausas_horario +'').prop( "disabled",false);
                            console.log('esta funcionando editar')
                             if(horaI<$('#horaI_ed').val() ||horaI>$('#horaF_ed').val() ){

                                $('#InicioPausa_edReg'+ item.idpausas_horario +'').val('');
                                $('#fueraRango_ed').show();
                                event.stopPropagation();
                             } else{
                                $('#fueraRango_ed').hide();
                             }
                             console.log(horaF);
                             if(horaF==null || horaF==''){

                             }
                             else{
                                console.log('secumple');
                                if (horaF < horaI) {
                                    $('#InicioPausa_edReg'+ item.idpausas_horario +'').val('');
                                    $('#errorenPausas_ed').show();
                                    event.stopPropagation();
                                } else {
                                    $('#errorenPausas_ed').hide();
                                }
                             }


                        });
                    });
                    $(function () {
                        $(document).on('change', '#FinPausa_edReg'+ item.idpausas_horario +'', function (event) {
                            let horaF = $('#FinPausa_edReg'+ item.idpausas_horario +'').val();
                            let horaI = $('#InicioPausa_edReg'+ item.idpausas_horario +'').val();
                            if(horaF<$('#horaI_ed').val() ||horaF>$('#horaF_ed').val() ){
                                $('#FinPausa_edReg'+ item.idpausas_horario +'').val('');
                                $('#fueraRango_ed').show();
                                event.stopPropagation();
                             } else{
                                $('#fueraRango_ed').hide();
                             }
                            if (horaF < horaI) {
                                $('#FinPausa_edReg'+ item.idpausas_horario +'').val('');
                                $('#errorenPausas_ed').show();
                                event.stopPropagation();
                            } else {
                                $('#errorenPausas_ed').hide();
                            }


                        });
                    });
                });
                $('input[name="InicioPausa_edReg[]"]').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true
                });
                $('input[name="FinPausa_edReg[]"]').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true
                });


            }
        },
        error: function (data) {
            alert('Ocurrio un error');
        }

    });


}
function editarHorario() {
    var idhorario = $('#idhorario_ed').val();

    var descried = $('#descripcionCa_ed').val();
    var toleed = $('#toleranciaH_ed').val();
    var horaIed = $('#horaI_ed').val();
    var horaFed = $('#horaF_ed').val();
    var toleranciaFed = $('#toleranciaSalida_ed').val();

    var horaObed = $('#horaOblig_ed').val();
    var tardanza_ed;
    if ($('#SwitchTardanza_ed').is(":checked")) {
        tardanza_ed=1;
    } else{
        tardanza_ed=0;
    }
    if ($('#SwitchPausa_ed').is(":checked")) {
        var descPausa_ed = [];
        var pausaInicio_ed = [];
        var finPausa_ed = [];

        var ID_edReg = [];
        var descPausa_edReg = [];
        var pausaInicio_edReg = [];
        var finPausa_edReg = [];
        //NUEVOS EN EDICION
        $('input[name="descPausa_ed[]"]').each(function () {
            descPausa_ed.push($(this).val());
        });
        $('input[name="InicioPausa_ed[]"]').each(function () {
            pausaInicio_ed.push($(this).val());
        });
        $('input[name="FinPausa_ed[]"]').each(function () {
            finPausa_ed.push($(this).val());
        });
        //ANTIGUOS EDITADO
        $('input[name="idPausasRegistradas_ed[]"]').each(function () {
            ID_edReg.push($(this).val());
        });
        $('input[name="descPausa_edRegist[]"]').each(function () {
            descPausa_edReg.push($(this).val());
        });
        $('input[name="InicioPausa_edReg[]"]').each(function () {
            pausaInicio_edReg.push($(this).val());
        });
        $('input[name="FinPausa_edReg[]"]').each(function () {
            finPausa_edReg.push($(this).val());
        });

    }
    console.log(ID_edReg);
    console.log(descPausa_edReg);
    console.log(pausaInicio_edReg);
    console.log(finPausa_edReg);

    $.ajax({
        type: "post",
        url: "/horario/actualizarhorario",
        data: { idhorario, descried, toleed, horaIed, horaFed,tardanza_ed,
             toleranciaFed, horaObed,descPausa_ed,pausaInicio_ed, finPausa_ed,ID_edReg,descPausa_edReg,
             pausaInicio_edReg,finPausa_edReg },
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
            $('#tablaEmpleado').DataTable().ajax.reload();
            $('#selectHorario').empty();

            $.each(data, function (key, item) {
                $('#selectHorario').append($('<option>', { //agrego los valores que obtengo de una base de datos
                    value: item.horario_id,
                    text: item.horario_descripcion + "(" + item.horaI + "-" + item.horaF + ")"

                }));


            });

            $("#selectHorario").append($('<option >', { //agrego los valores que obtengo de una base de dato
                text: "Asignar horario",
                disabled: true

            }));
            $("#selectHorario").val("Asignar horario");
            $("#selectHorario").trigger("change");

            var mesAg = $('#fechaDa').val();
            var d = mesAg;
            var fechasM = new Date(d);
            calendar.refetchEvents();
            $('#horarioEditar').modal('hide');
        },
        error: function (data) {
            alert('Ocurrio un error');
        }

    });



}
function eliminarHorario(idhorario) {

    $.ajax({
        type: "post",
        url: "/horario/verificarID",
        data: { idhorario },
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
            if (data == 1) {
                bootbox.alert({
                    title:"Eliminar horario",
                    message: "No se puede eliminar un horario en uso.",

                });

                return false;
            }
            else {
                bootbox.confirm({
                    title:"Eliminar horario",
                    message: "¿Desea eliminar el horario?",
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
                                url: "/horario/eliminarHorario",
                                data: { idhorario },
                                statusCode: {

                                    419: function () {
                                        location.reload();
                                    }
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (data) {
                                    $('#tablaEmpleado').DataTable().ajax.reload();
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
        error: function (data) {
            alert('Ocurrio un error');
        }

    });

}
///////////////////////
//select todo empleado
$("#selectTodoCheck").click(function () {
    if ($("#selectTodoCheck").is(':checked')) {
        $("#nombreEmpleado > option").prop("selected", "selected");
        $("#nombreEmpleado").trigger("change");
    } else {
        $("#nombreEmpleado > option").prop("selected", false);
        $("#nombreEmpleado").trigger("change");
    }
});
//////////////////////
//seleccionar por area, cargo, etc
$('#selectEmpresarial').change(function (e) {
    var idempresarial = [];
    idempresarial = $('#selectEmpresarial').val();
    textSelec = $('select[name="selectEmpresarial"] option:selected:last').text();
    textSelec2 = $('select[name="selectEmpresarial"] option:selected:last').text();
    /*  palabrasepara=textSelec2.split('.')[0];
     alert(palabrasepara);
     return false; */
    palabraEmpresarial = textSelec.split(' ')[0];
    if (palabraEmpresarial == 'Area') {
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
                $("#nombreEmpleado > option").prop("selected", false);
                $("#nombreEmpleado").trigger("change");
                $.each(data, function (index, value) {
                    $("#nombreEmpleado > option[value='" + value.emple_id + "']").prop("selected", "selected");
                    $("#nombreEmpleado").trigger("change");
                });
                console.log(data);
            },
            error: function (data) {
                alert('Ocurrio un error');
            }
        });
    }
    if (palabraEmpresarial == 'Cargo') {
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
                $("#nombreEmpleado > option").prop("selected", false);
                $("#nombreEmpleado").trigger("change");
                $.each(data, function (index, value) {
                    $("#nombreEmpleado > option[value='" + value.emple_id + "']").prop("selected", "selected");
                    $("#nombreEmpleado").trigger("change");
                });
                console.log(data);
            },
            error: function (data) {
                alert('Ocurrio un error');
            }
        });
    }

    if (palabraEmpresarial == 'Local') {
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
                $("#nombreEmpleado > option").prop("selected", false);
                $("#nombreEmpleado").trigger("change");
                $.each(data, function (index, value) {
                    $("#nombreEmpleado > option[value='" + value.emple_id + "']").prop("selected", "selected");
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
$("#FeriadosCheck").click(function () {
    $('#Datoscalendar').hide();
    $('#DatoscalendarOculto').show();
    if ($("#FeriadosCheck").is(':checked')) {
        $.ajax({
            type: "post",
            url: "/horario/copiarferiados",
            data: {
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
                $('#DatoscalendarOculto').hide();
                $('#Datoscalendar').show();
            },
            error: function (data) {
                alert('Ocurrio un error');
            }
        });

    } else {
        $.ajax({
            type: "post",
            url: "/horario/borrarferiados",
            data: {
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
                $('#DatoscalendarOculto').hide();
                $('#Datoscalendar').show();
            },
            error: function (data) {
                alert('Ocurrio un error');
            }
        });

    }
});
$(function () {
    $(document).on('change', '#horaF', function (event) {
        let horaF = $('#horaF').val();
        let horaI = $('#horaI').val();

        if (horaF < horaI) {
            $('#divOtrodia').show();
            $('#horaOblig').prop("disabled",false);
            $('#horaOblig').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
            $('#horaOblig').val('');
            event.stopPropagation();
        } else {
            var dateDesde = newDate(horaI.split(":"));
            var dateHasta = newDate(horaF.split(":"));

            var minutos = (dateHasta - dateDesde)/1000/60;
            var horas = Math.floor(minutos/60);
            minutos = minutos % 60;
            console.log(prefijo(horas) + ':' + prefijo(minutos));
            $('#horaOblig').prop("disabled",false);

            if($('#horaOblig').val()==null || $('#horaOblig').val()==''){
                $('#horaOblig').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultHour:"8"
                });
                $('#horaOblig').val("08:00");

            }

            $('#divOtrodia').hide();
        }

    });
});
function newDate(partes) {
    var date = new Date(0);
    date.setHours(partes[0]);
    date.setMinutes(partes[1]);
    return date;
}

function prefijo(num) {
    return num < 10 ? ("0" + num) : num;
}
$(function () {
    $(document).on('change', '#horaI', function (event) {
        let horaF = $('#horaF').val();
        let horaI = $('#horaI').val();

        if (horaF < horaI) {
            $('#divOtrodia').show();
            $('#horaOblig').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
            $('#horaOblig').prop("disabled",false);
            $('#horaOblig').val('');
            event.stopPropagation();
        } else {
            var dateDesde = newDate(horaI.split(":"));
            var dateHasta = newDate(horaF.split(":"));

            var minutos = (dateHasta - dateDesde)/1000/60;
            var horas = Math.floor(minutos/60);
            minutos = minutos % 60;
            console.log(prefijo(horas) + ':' + prefijo(minutos));
            $('#horaOblig').prop("disabled",false);
            /* $('#horaOblig').val(prefijo(horas)); */
            if($('#horaOblig').val()==null || $('#horaOblig').val()==''){
                $('#horaOblig').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultHour:"8"
                });
                $('#horaOblig').val("08:00");

            }
            $('#divOtrodia').hide();
        }

    });
});

$(function () {
    $(document).on('change', '#horaF_ed', function (event) {
        let horaF = $('#horaF_ed').val();
        let horaI = $('#horaI_ed').val();

        if (horaF < horaI) {
            $('#divOtrodia_ed').show();
            event.stopPropagation();
        } else {
            $('#divOtrodia_ed').hide();
        }

    });
});
$(function () {
    $(document).on('change', '#horaI_ed', function (event) {
        let horaF = $('#horaF_ed').val();
        let horaI = $('#horaI_ed').val();

        if (horaF < horaI) {
            $('#divOtrodia_ed').show();
            event.stopPropagation();
        } else {
            $('#divOtrodia_ed').hide();
        }

    });
});
$('#SwitchPausa').change(function (event) {
    if ($('#SwitchPausa').prop('checked')) {
        $('input[name="descPausa[]"]').prop('required', true);
        $('#InicioPausa').prop('required', true);
        $('#FinPausa').prop('required', true);
        $('#divPausa').show();
    }
    else {

        $('input[name="descPausa[]"]').val('');
        $('input[name="InicioPausa[]"]').val('');
        $('input[name="FinPausa[]"]').val('');
        $('#divPausa').hide();
        $('input[name="descPausa[]"]').prop('required', false);
        $('input[name="InicioPausa[]"]').prop('required', false);
        $('input[name="FinPausa[]"]').prop('required', false);
    }
    event.preventDefault();
});

function addField() {

    // ID del elemento div quitandole la palabra "div_" de delante. Pasi asi poder aumentar el número.
    // Esta parte no es necesaria pero yo la utilizaba ya que cada campo de mi formulario tenia un autosuggest,
    // así que dejo como seria por si a alguien le hace falta.
    var clickID = parseInt($(this).parent('div').attr('id').replace('div_', ''));
    // Genero el nuevo numero id
    var newID = (clickID + 1);
    // Creo un clon del elemento div que contiene los campos de texto
    $newClone = $('#div_' + clickID).clone(true);
    //Le asigno el nuevo numero id
    $newClone.attr("id", 'div_' + newID);

    //Asigno nuevo id al primer campo input dentro del div y le borro cualquier valor
    // que tenga asi no copia lo ultimo que hayas escrito.(igual que antes no es necesario tener un id)
    $newClone.children("input").eq(0).attr("id", 'descPausa' + newID).val('');
    //Borro el valor del segundo campo input(este caso es el campo de cantidad)
    $newClone.children("input").eq(1).attr("id", 'InicioPausa' + newID).val('').prop('required', true).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour:null
    });
    var horafinal=$('#horaF').val();
    splih=horafinal.split(":");
    console.log(splih[0]);
    $newClone.children("input").eq(2).attr("id", 'FinPausa' + newID).val('').prop('required', true).prop( "disabled",true ).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour:splih[0]
    });;
    $(function () {
        $(document).on('change', '#FinPausa'+ newID, function (event) {
            let horaF = $('#FinPausa'+ newID).val();
            let horaI = $('#InicioPausa'+ newID).val();

            if($('#horaI').val() > $('#horaF').val()){
                if( horaF<$('#horaI').val() && horaF>$('#horaF').val()){
                    $('#FinPausa' + newID).val('');
                    $('#fueraRango').show();
                    event.stopPropagation();
                 } else{
                    $('#fueraRango').hide();
                 }

                 if (horaI > horaF) {
                   /*  $('#FinPausa').val('');
                    $('#errorenPausas').show();
                    event.stopPropagation(); */
                } else {
                    $('#errorenPausas').hide();
                }
            } else{
                if(horaF<$('#horaI').val() ||horaF>$('#horaF').val() ){
                $('#FinPausa' + newID).val('');
                $('#fueraRango').show();
                event.stopPropagation();
             } else{
                $('#fueraRango').hide();
             }

                if (horaF < horaI) {
                    $('#FinPausa'+ newID).val('');
                    $('#errorenPausas').show();
                    event.stopPropagation();
                } else {
                    $('#errorenPausas').hide();
                }
            }





        });
    });
    $(function () {
        $(document).on('change', '#InicioPausa'+ newID, function (event) {
            let horaF = $('#FinPausa'+ newID).val();
            let horaI = $('#InicioPausa'+ newID).val();
            $('#FinPausa'+ newID).prop( "disabled",false);
            if($('#horaI').val() > $('#horaF').val()){

                if( horaI<$('#horaI').val() && horaI>$('#horaF').val()){
                console.log('moostrando fuera rango 11/11');
                $('#InicioPausa'+ newID).val('');
                    $('#fueraRango').show();

                    event.stopPropagation();
                 } else{
                    $('#fueraRango').hide();
                 }

            } else{
                if(horaI<$('#horaI').val() ||horaI>$('#horaF').val() ){

                    $('#InicioPausa'+ newID).val('');
                    $('#fueraRango').show();
                    event.stopPropagation();
                 } else{
                    $('#fueraRango').hide();
                 }
            }


            if(horaF==null || horaF==''){

            } else{
                if($('#horaI').val() < $('#horaF').val()){
            if (horaF < horaI) {
                $('#InicioPausa'+ newID).val('');
                $('#errorenPausas').show();
                event.stopPropagation();
            } else {
                $('#errorenPausas').hide();
            }
        } else
        {
            $('#errorenPausas').hide();
        }
        }

        });
    });

    /*  $newClone.children("input").eq(3).attr("id",'PROVECONT_email'+newID).val(''); */
    //Asigno nuevo id al boton
    $newClone.children("button").attr("id", newID)
    //Inserto el div clonado y modificado despues del div original
    $newClone.insertAfter($('#div_' + clickID));
    //Cambio el signo "+" por el signo "-" y le quito el evento addfield
    //$("#"+clickID-1).remove();
    $("#" + clickID).css("backgroundColor", "#f6cfcf");
    $("#" + clickID).css("border-Color", "#f6cfcf");
    $("#" + clickID).css("color", "#d11010");
    $("#" + clickID).css("height", "22px");
    $("#" + clickID).css("font-weight", "600");
    $("#" + clickID).css("margin-top", "5px");
    $("#" + clickID).css("font-size", "12px");
    $("#" + clickID).css("width", "19px");
    $("#" + clickID).css("margin-left", "20-px");
    $('input[name="descPausa[]"]').prop('required', true);
    $('input[name="InicioPausa[]"]').prop('required', true);
    $('input[name="FinPausa[]"]').prop('required', true);
    $("#" + clickID).html('-').unbind("click", addField);
    $('.flatpickr-input[readonly]').on('focus', function () {
        $(this).blur()
    })
    $('.flatpickr-input[readonly]').prop('readonly', false)
    //Ahora le asigno el evento delRow para que borre la fial en caso de hacer click
    $("#" + clickID).bind("click", delRow);
}
function delRow() {
    // Funcion que destruye el elemento actual una vez echo el click
    $(this).parent('div').remove();
}
$('#SwitchPausa_ed').change(function (event) {
    if ( $('input[name="descPausa_edRegist[]"]').length) {
        $('#SwitchPausa_ed').prop('checked', true);
        bootbox.confirm({
            title:"Eliminar pausas de horario",
            message: "Se eliminaran todas las pausas de este horario",
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
                    var valorHorario=$('#idhorario_ed').val();
                     $.ajax({
                        type: "post",
                        url: "/eliminarPausasEnEditar",
                        data: {
                            valorHorario
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
                            $('#PausasHorar_ed').empty();
                            $('#pausas_edit').hide();
                            $('#SwitchPausa_ed').prop('checked', false);

                        },
                        error: function (data) {
                            alert('Ocurrio un error');
                        }


                    });
                }
            }
        });
    }
    if ($('#SwitchPausa_ed').prop('checked')) {
        if ( $('input[name="descPausa_edRegist[]"]').length) {}
        else{
            $("#PausasHorar_ed").append('<div id="divEd_100" class="row col-md-12" style=" margin-bottom: 8px;">' +
                        '<input type="text"  class="form-control form-control-sm col-sm-5" name="descPausa_ed[]" id="descPausa_ed" >' +
                        '<input type="text"   class="form-control form-control-sm col-sm-3" name="InicioPausa_ed[]"  id="InicioPausa_ed" >' +
                        '<input type="text" class="form-control form-control-sm col-sm-3" name="FinPausa_ed[]"  id="FinPausa_ed" >' +
                        '&nbsp; <button class="btn btn-sm bt_plus_ed" id="ed_100" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
                         ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>' +
                        '</div>'
                        );
                        $('.flatpickr-input[readonly]').on('focus', function () {
                            $(this).blur()
                        })
                        $('.flatpickr-input[readonly]').prop('readonly', false)
                        $(".bt_plus_ed").each(function (el) {
                            $(this).bind("click", addField_ed);
                        });
                        $('#InicioPausa_ed').flatpickr({
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: "H:i",
                            time_24hr: true
                        });
                        $('#FinPausa_ed').flatpickr({
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: "H:i",
                            time_24hr: true
                        });

        $('input[name="descPausa_ed[]"]').prop('required', true);
        $('#InicioPausa_ed').prop('required', true);
        $('#FinPausa_ed').prop('required', true);
        $('#pausas_edit').show();
        }

    }
    else {

        $('input[name="descPausa_ed[]"]').val('');
        $('input[name="InicioPausa_ed[]"]').val('');
        $('input[name="FinPausa_ed[]"]').val('');
        $('#PausasHorar_ed').empty();
        $('#pausas_edit').hide();
        $('input[name="descPausa_ed[]"]').prop('required', false);
        $('input[name="InicioPausa_ed[]"]').prop('required', false);
        $('input[name="FinPausa_ed[]"]').prop('required', false);
    }
    event.preventDefault();
});
function addField_ed() {

    // ID del elemento div quitandole la palabra "div_" de delante. Pasi asi poder aumentar el número.
    // Esta parte no es necesaria pero yo la utilizaba ya que cada campo de mi formulario tenia un autosuggest,
    // así que dejo como seria por si a alguien le hace falta.
    var clickID = parseInt($(this).parent('div').attr('id').replace('divEd_', ''));
    // Genero el nuevo numero id
    var newID = (clickID + 1);
    // Creo un clon del elemento div que contiene los campos de texto
    $newClone = $('#divEd_' + clickID).clone(true);
    //Le asigno el nuevo numero id
    $newClone.attr("id", 'divEd_' + newID);

    //Asigno nuevo id al primer campo input dentro del div y le borro cualquier valor
    // que tenga asi no copia lo ultimo que hayas escrito.(igual que antes no es necesario tener un id)
    $newClone.children("input").eq(0).attr("id", 'descPausa_ed' + newID).val('');
    //Borro el valor del segundo campo input(este caso es el campo de cantidad)
    $newClone.children("input").eq(1).attr("id", 'InicioPausa_ed' + newID).val('').prop('required', true).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });
    $newClone.children("input").eq(2).attr("id", 'FinPausa_ed' + newID).val('').prop('required', true).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });;
    $(function () {
        $(document).on('change', '#FinPausa_ed'+ newID, function (event) {
            let horaF = $('#FinPausa_ed'+ newID).val();
            let horaI = $('#InicioPausa_ed'+ newID).val();

            if(horaF<$('#horaI_ed').val() ||horaF>$('#horaF_ed').val() ){
                $('#FinPausa_ed' + newID).val('');
                $('#fueraRango_ed').show();
                event.stopPropagation();
             } else{
                $('#fueraRango_ed').hide();
             }

                if (horaF < horaI) {
                    $('#FinPausa_ed'+ newID).val('');
                    $('#errorenPausas_ed').show();
                    event.stopPropagation();
                } else {
                    $('#errorenPausas_ed').hide();
                }



        });
    });
    $(function () {
        $(document).on('change', '#InicioPausa_ed'+ newID, function (event) {
            let horaF = $('#FinPausa_ed'+ newID).val();
            let horaI = $('#InicioPausa_ed'+ newID).val();
            $('#FinPausa_ed'+ newID).prop( "disabled",false);
            if(horaI<$('#horaI_ed').val() ||horaI>$('#horaF_ed').val() ){

                $('#InicioPausa_ed'+ newID).val('');
                $('#fueraRango_ed').show();
                event.stopPropagation();
             } else{
                $('#fueraRango_ed').hide();
             }

            if(horaF==null || horaF==''){

            } else{
            if (horaF < horaI) {
                $('#InicioPausa_ed'+ newID).val('');
                $('#errorenPausas_ed').show();
                event.stopPropagation();
            } else {
                $('#errorenPausas_ed').hide();
            }
        }

        });
    });

    /*  $newClone.children("input").eq(3).attr("id",'PROVECONT_email'+newID).val(''); */
    //Asigno nuevo id al boton
    $newClone.children("button").attr("id",'ed_'+newID)
    //Inserto el div clonado y modificado despues del div original
    $newClone.insertAfter($('#divEd_' + clickID));
    //Cambio el signo "+" por el signo "-" y le quito el evento addfield
    //$("#"+clickID-1).remove();
    $("#ed_" + clickID).css("backgroundColor", "#f6cfcf");
    $("#ed_" + clickID).css("border-Color", "#f6cfcf");
    $("#ed_" + clickID).css("color", "#d11010");
    $("#ed_" + clickID).css("height", "22px");
    $("#ed_" + clickID).css("font-weight", "600");
    $("#ed_" + clickID).css("margin-top", "5px");
    $("#ed_" + clickID).css("font-size", "12px");
    $("#ed_" + clickID).css("width", "19px");
    $("#ed_" + clickID).css("margin-left", "20-px");
    $('input[name="descPausa_ed[]"]').prop('required', true);
    $('input[name="InicioPausa_ed[]"]').prop('required', true);
    $('input[name="FinPausa_ed[]"]').prop('required', true);
    $("#ed_" + clickID).html('-').unbind("click", addField_ed);
    $('.flatpickr-input[readonly]').on('focus', function () {
        $(this).blur()
    })
    $('.flatpickr-input[readonly]').prop('readonly', false)
    //Ahora le asigno el evento delRow para que borre la fial en caso de hacer click
    $("#ed_" + clickID).bind("click", delRow_ed);
}
function delRow_ed() {
    // Funcion que destruye el elemento actual una vez echo el click
    $(this).parent('div').remove();
}
$(function () {
    $(document).on('change', '#FinPausa', function (event) {
        let horaF = $('#FinPausa').val();
        let horaI = $('#InicioPausa').val();
        if($('#horaI').val() > $('#horaF').val()){
            if( horaF<$('#horaI').val() && horaF>$('#horaF').val()){
                $('#FinPausa').val('');
                $('#fueraRango').show();
                event.stopPropagation();
             } else{
                $('#fueraRango').hide();
             }

             if (horaI > horaF) {
               /*  $('#FinPausa').val('');
                $('#errorenPausas').show();
                event.stopPropagation(); */
            } else {
                $('#errorenPausas').hide();
            }
        }
        else{
           if(horaF<$('#horaI').val() ||horaF>$('#horaF').val() ){
            $('#FinPausa').val('');
            $('#fueraRango').show();
            event.stopPropagation();
         } else{
            $('#fueraRango').hide();
         }
         if (horaF < horaI) {
            $('#FinPausa').val('');
            $('#errorenPausas').show();
            event.stopPropagation();
        } else {
            $('#errorenPausas').hide();
        }
        }




    });
});
$(function () {
    $(document).on('change', '#InicioPausa', function (event) {
        let horaF = $('#FinPausa').val();
        let horaI = $('#InicioPausa').val();
        $('#FinPausa').prop( "disabled",false);
        if($('#horaI').val() > $('#horaF').val()){

            if( horaI<$('#horaI').val() && horaI>$('#horaF').val()){
            console.log('moostrando fuera rango 11/11');
                $('#InicioPausa').val('');
                $('#fueraRango').show();

                event.stopPropagation();
             } else{
                $('#fueraRango').hide();
             }

        } else
        {
            if(horaI<$('#horaI').val() || horaI>$('#horaF').val() ){

            $('#InicioPausa').val('');
            $('#fueraRango').show();
            event.stopPropagation();
         } else{
            $('#fueraRango').hide();
         }
        }

         console.log(horaF);
         if(horaF==null || horaF==''){
            var horafinal1=$('#horaF').val();
            splih1=horafinal1.split(":");
            console.log(splih1[0]);
            console.log('nada me da');
            $('#FinPausa').val('').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour:splih1[0]
            });

         }
         else{
            console.log('secumple');
            if($('#horaI').val() < $('#horaF').val()){
            if (horaF < horaI) {
                $('#InicioPausa').val('');
                $('#errorenPausas').show();
                event.stopPropagation();
            } else {
                $('#errorenPausas').hide();
            }
        } else
        {
            $('#errorenPausas').hide();
        }
         }


    });
});


$(function () {
    $(document).on('change', '#FinPausa_ed', function (event) {
        let horaF = $('#FinPausa_ed').val();
        let horaI = $('#InicioPausa_ed').val();
        if(horaF<$('#horaI_ed').val() ||horaF>$('#horaF_ed').val() ){
            $('#FinPausa_ed').val('');
            $('#fueraRango_ed').show();
            event.stopPropagation();
         } else{
            $('#fueraRango_ed').hide();
         }
        if (horaF < horaI) {
            $('#FinPausa_ed').val('');
            $('#errorenPausas_ed').show();
            event.stopPropagation();
        } else {
            $('#errorenPausas_ed').hide();
        }


    });
});
$(function () {
    $(document).on('change', '#InicioPausa_ed', function (event) {
        let horaF = $('#FinPausa_ed').val();
        let horaI = $('#InicioPausa_ed').val();
        $('#FinPausa_ed').prop( "disabled",false);
        console('esta funcionando editar')
         if(horaI<$('#horaI_ed').val() ||horaI>$('#horaF_ed').val() ){

            $('#InicioPausa_ed').val('');
            $('#fueraRango_ed').show();
            event.stopPropagation();
         } else{
            $('#fueraRango_ed').hide();
         }
         console.log(horaF);
         if(horaF==null || horaF==''){

         }
         else{
            console.log('secumple');
            if (horaF < horaI) {
                $('#InicioPausa_ed').val('');
                $('#errorenPausas_ed').show();
                event.stopPropagation();
            } else {
                $('#errorenPausas_ed').hide();
            }
         }


    });
});
/////////////////cambiar sch
$(function () {
    $(document).on('change', '#horAdicSwitch', function (event) {
        if ($('#horAdicSwitch').prop('checked')) {
            $('#nHorasAdic').show();
           /*  $('#nHorasAdic').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate:"02:00"
            });
            $('#nHorasAdic').val( "02:00"); */
            $('#fueraHSwitch').prop('checked', true);
            $('#fueraHSwitch').prop('disabled', true);

        } else{
            $('#nHorasAdic').hide();
            $('#fueraHSwitch').prop('disabled', false);
        }

    });
});
