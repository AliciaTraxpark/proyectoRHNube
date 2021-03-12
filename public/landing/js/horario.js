var dataDeempleado={};
var dataDeempleadoInc={};
$.fn.dataTable.ext.errMode = 'throw';
$(document).ready(function () {
    var table = $("#tablaEmpleado").DataTable({
        "searching": true,
        "processing": true,
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando registros del _START_ al _END_ ",
            sInfoEmpty:
                "Mostrando 0 de un total de 0 registros",
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
            data: {
                "_token": $("meta[name='csrf-token']").attr("content")
                },
            statusCode: {
                401: function () {
                    location.reload();
                },
                402: function () {
                    location.reload();
                },
                419: function () {
                    location.reload();
                },
                403: function () {
                    location.reload();
                },
                302: function () {
                    location.reload();
                }
            },
            "error": function() {
                console.log("se recarga en 401");

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
            {
                data: "horaI",
                "render": function (data, type, row) {
                    return "&nbsp;&nbsp;&nbsp" + row.horaI;
                }
            },
            {
                data: "horaF",
                "render": function (data, type, row) {
                    return "&nbsp;&nbsp;" + row.horaF;
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

                    return '<a onclick="modalEditar(' + row.horario_id + ')" style="cursor: pointer"><img src="/admin/images/edit.svg" height="15"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="" style="cursor: pointer">' +
                        '<img src="/admin/images/delete.svg" onclick="eliminarHorario(' + row.horario_id + ')" height="15"></a>';

                }
            },

        ]


    });
    table.on('order.dt search.dt', function () {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
    $("#tablaEmpleado tbody tr").hover(function () {
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

                $('#idobtenidoE').val(idempleado);
                depart = data[0][0].ubigeo_peru_departments_id;
                if (depart == null) {
                    $('#departamentoHorario').val('Ninguno');
                } else {
                    $('#departamentoHorario').val(depart);
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
                console.log('Ocurrio un error');
            }
        });
    });
}

$('#horaIen').flatpickr({
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

    $('#guardarHorarioEventos').prop('disabled', false);
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
        $(".loader").hide();
        $(".img-load").hide();
        $('#asignarHorario').modal('toggle');

    });
    num = $('#nombreEmpleado').val().length;
    idemplesH = $('#nombreEmpleado').val();
    var ideHor = [];
    ideHor.push(idemplesH);

    var allVals = [];
    $(".sub_chk:checked").each(function () {
        allVals.push($(this).attr('data-id'));
    });

    $(".loader").hide();
    $(".img-load").hide();

    $("#divCambios").hide();

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
        contentHeight: 500,
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

            $("#selectTipoIn").val("");
            $("#selectTipoIn").trigger("change");
            $("#incidenciaSelect").val("");
            $("#incidenciaSelect").trigger("change");
            $('#incidenciaSelect').prop('disabled', true);

            $('#verhorarioEmpleado').modal('show');

        },
        eventClick: function (info) {
            id = info.event.id;

            var event = calendar.getEventById(id);

            //*CUANDO ES HORARIO NUEVO ASIGNADO
            if (info.event.textColor == '111111') {
               if(info.event.backgroundColor=='#e2e2e2'){
                $('#tipoHorario').val('0');
               } else{
                $('#tipoHorario').val('1');
               }
                /* UNBIND SOLO UNA VEZ */
                $('#eliminaHorarioDia_re').unbind().click(function () {
                    $('#editarConfigHorario_re').modal('hide');
                    bootbox.confirm({
                        title: "Eliminar horario",
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
                                var tipoEliminar= $('#tipoHorario').val();
                                $.ajax({
                                    type: "post",
                                    url: "/eliminarHora",
                                    data: {
                                        idHora: info.event.id,tipoEliminar
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
                                        console.log('Ocurrio un error');
                                    }


                                });
                            }
                        }
                    });
                });

                //*seteando datos amodal
                $('#idHoraEmpleado_re').val(info.event.id);
                if (info.event.borderColor == '#5369f8') {
                    $('#fueraHSwitch_Actualizar_re').prop("checked", true);
                }
                else {
                    $('#fueraHSwitch_Actualizar_re').prop("checked", false);
                }
                if (info.event.extendedProps.horaAdic == 1) {
                    $('#horAdicSwitch_Actualizar_re').prop("checked", true);
                    $('#nHorasAdic_Actualizar_re').show();

                    $("#nHorasAdic_Actualizar_re").val(info.event.extendedProps.nHoraAdic);


                }
                else {
                    $('#horAdicSwitch_Actualizar_re').prop("checked", false);
                    $('#nHorasAdic_Actualizar_re').hide();
                }

                $('#editarConfigHorario_re').modal('show');
            }
            else{
                //*SI ES ASIGNADO
                if(info.event.textColor=='#000'){
                    let diadeHorario=moment(info.event.start).format('YYYY-MM-DD HH:mm:ss');
                    let empleados=$('#nombreEmpleado').val();
                    datosModalHorarioEmpleado(diadeHorario,empleados);

                } else{
                    //*PARA INCIDENCIAS MODAL
                    if(info.event.textColor=='#bd6767'){
                        let diadeIncidencia=moment(info.event.start).format('YYYY-MM-DD HH:mm:ss');
                        let empleados=$('#nombreEmpleado').val();
                        datosModalIncidenciaEmpleado(diadeIncidencia,empleados);

                    } else{
                        //*PARA BORRAR INCIDENCIAS

                        let nuevaIncidencia;

                        // si es nuevo
                        if(info.event.textColor == '#313131'){
                            nuevaIncidencia=1;
                        } else{
                            nuevaIncidencia=0;
                        }
                        bootbox.confirm({
                            title: "Eliminar Incidencias",
                            message: "¿Desea eliminar: " + info.event.title + " del calendario?",
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
                                        url: "/eliminarIncidenciaHorario",
                                        data: {
                                            idHora: info.event.id,nuevaIncidencia
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
                                            console.log('Ocurrio un error');
                                        }


                                    });
                                }
                            }
                        });
                    }

                }
            }


            //info.event.remove();
        },
        editable: false,
        eventLimit: true,
        header: {
            left: 'prev,next',
            center: 'title',
            right: "Clonar,borrarHorarios,borrarIncidencias",
        },
        eventRender: function (info) {
            $('.tooltip').remove();
            if(info.event.textColor=='111111'){
              if (info.event.extendedProps.horaI === null) {
                $(info.el).tooltip({ title: info.event.title });
            } else {


                if (info.event.extendedProps.pausas != '') {
                    var cadenaPausas = [];
                    $.each(info.event.extendedProps.pausas, function (index, value2) {

                        variableResult1 = '   <br>   ' + value2.pausH_descripcion + ':  ' + value2.pausH_Inicio + '-' + value2.pausH_Fin + '                                                                                          ';
                        cadenaPausas.push(variableResult1);
                    })
                    if (info.event.borderColor == '#5369f8') {
                        if (info.event.extendedProps.horaAdic == 1) {
                            $(info.el).tooltip({
                                template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner large"></div></div>',
                                html: true, title: 'Horario ' + info.event.title + ' :  ' + info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF +
                                    '<br> Horas adicionales:' + info.event.extendedProps.nHoraAdic + ' horas' +
                                    '<br> Horas obligadas: ' + info.event.extendedProps.horasObliga +
                                    ' <br> Trabaja fuera de horario' +
                                    ' <br> Pausas programadas:    ' + cadenaPausas
                            });
                        } else {
                            $(info.el).tooltip({
                                template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner large"></div></div>',
                                html: true, title: 'Horario ' + info.event.title + ' :  ' + info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF +
                                    '<br> Horas obligadas: ' + info.event.extendedProps.horasObliga +
                                    ' <br> Trabaja fuera de horario' +
                                    '  <br>  Pausas programadas:     ' + cadenaPausas
                            });
                        }
                    }
                    else {
                        $(info.el).tooltip({
                            template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner large"></div></div>',
                            html: true, title: 'Horario ' + info.event.title + ' :  ' + info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF +
                                '<br> Horas obligadas: ' + info.event.extendedProps.horasObliga +
                                '<br>   Pausas programadas:     ' + cadenaPausas
                        });
                    }
                }
                else {
                    /* HORARIO CUANDO NO TIENE PAUSAS */
                    if (info.event.borderColor == '#5369f8') {
                        if (info.event.extendedProps.horaAdic == 1) {
                            $(info.el).tooltip({
                                template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner large"></div></div>',
                                html: true, title: 'Horario ' + info.event.title + ' :  ' + info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF +
                                    ' <br> Horas adicionales:' + info.event.extendedProps.nHoraAdic + ' horas' +
                                    '<br> Horas obligadas: ' + info.event.extendedProps.horasObliga +
                                    ' <br> Trabaja fuera de horario'
                            });
                        } else {
                            $(info.el).tooltip({
                                template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner large"></div></div>',
                                html: true, title: 'Horario ' + info.event.title + ' :  ' + info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '<br>  Trabaja fuera de horario' + '<br> Horas obligadas: ' + info.event.extendedProps.horasObliga
                            });
                        }
                    }
                    else {
                        $(info.el).tooltip({
                            template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner large"></div></div>',
                            html: true, title: 'Horario ' + info.event.title + ' :  ' + info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF +
                                '<br> Horas obligadas: ' + info.event.extendedProps.horasObliga
                        });
                    }
                }

            }
            } else{
                $(info.el).tooltip({ title: info.event.title });
            }


        },
        customButtons: {
            borrarHorarios: {
                text: "Borrar horarios",
                click: function () {
                    vaciarhor();
                }
            },

            borrarIncidencias: {
                text: "Borrar Incidencias",
                click: function () {
                    vaciarIncid();
                }
            },

            Clonar: {
                text: "Clonar horarios",
                click: function () {
                    ClonarHorarios();
                }
            },
        },
        events: function (info, successCallback, failureCallback) {


            var idempleado = $('#nombreEmpleado').val();
            num=$('#nombreEmpleado').val().length;

            if(num==1){
                $.ajax({
                    type: "POST",
                    url: "/horario/horariosAsignar",
                   /*  async:false, */
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


                        successCallback(data[0]);

                        $(".loader").hide();
                        $(".img-load").hide();
                        if(data[1]==1){
                            $("#divCambios").show();
                        } else{
                            $("#divCambios").hide();
                        }
                    },
                    error: function () {}
                });
            }
            else{

                 if(num>1){
                    $.ajax({
                    type: "POST",
                    url: "/horario/horariosVariosEmps",
                   /*  async:false, */
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

                        successCallback(data[0]);
                        $(".loader").hide();
                        $(".img-load").hide();
                        if(data[1]==1){
                            $("#divCambios").show();
                        } else{
                            $("#divCambios").hide();
                        }

                    },
                    error: function () {}
                });
                 }

            }


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
document.addEventListener('DOMContentLoaded', calendario);
function agregarHorarioSe() {
    if ($("*").hasClass("fc-highlight")) {

        textSelec1 = $('select[name="selectHorario"] option:selected').text();
        separador = "(";
        textSelec2 = textSelec1.split(separador);
        textSelec = textSelec2[0];

        var idhorar = $('#selectHorario').val();

        if (idhorar == null) {
            $('#errorSel').show();
            return false;
        } else {
            $('#errorSel').hide();
        }
        var fueraHora;
        if ($('#fueraHSwitch').prop('checked')) {
            fueraHora = 1;

        } else {
            fueraHora = 0;

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
            var nHoraAdic = $('#nHorasAdic').val();
        } else {
            horarioA = 0;
            var nHoraAdic = null;
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



         $.ajax({
            type: "post",
            url: "/guardarEventos",
            data: {
                fechasArray: fechastart,
                hora: textSelec,
                idhorar: idhorar, fueraHora,
                horaC: horarioC,
                horaA: horarioA, nHoraAdic

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
                $('#verhorarioEmpleado').modal('hide');
                $("#selectHorario").val("Asignar horario");
                $("#selectHorario").trigger("change");
                $('#horCompSwitch').prop("checked", false);
                $('#horAdicSwitch').prop("checked", false);
                var mesAg = $('#fechaDa').val();
                var d = mesAg;
                var fechasM = new Date(d);
                calendar.refetchEvents();

                if (data == 'Horario asignado') {

                } else {
                    bootbox.alert(data);
                }



            },
            error: function (data) {
                console.log('Ocurrio un error');
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
            $('#tablaEmpleado').DataTable().ajax.reload(null, false);
            $('#verhorarioEmpleado').modal('toggle');
            calendar.refetchEvents();


        },
        error: function () {
            console.log('Ocurrio un error');
        }
    });
});
////////////
$('#guardarTodoHorario').click(function () {
    $(".loader").show();
    $(".img-load").show();
    $('#guardarTodoHorario').prop('disabled', true);
    $('#tablaEmpleadoExcel').DataTable().destroy();

    /* if ($("*").hasClass("fc-highlight")) {
        $('#guardarTodoHorario').prop('disabled', false);
    } else {
        $('#guardarTodoHorario').prop('disabled', false);
        bootbox.alert({
            message: "Primero debe asignar dia(s) de calendario.",

        })
        return false;
    } */
  /*   $('#guardarTodoHorario').prop('disabled', true); */
    idemps = $('#nombreEmpleado').val();

    if (idemps == '') {

        bootbox.alert({
            title: "Seleccionar empleado",
            message: "Seleccione empleado",

        });
        $('#guardarTodoHorario').prop('disabled', false);
        $(".loader").hide();
        $(".img-load").hide();
        return false;
    }
    $.ajax({
        type: "post",
        url: "/guardarHorarioC",
        async: false,
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
            $('#tbodyExcel').empty();
            $('#tablaEmpleado').DataTable().ajax.reload(null, false);
            $('#guardarTodoHorario').prop('disabled', false);

           /*  $('#asignarHorario').modal('toggle'); */

            if (data.length > 0) {

                var tbodyTabla = [];
                for (var i = 0; i < data.length; i++) {
                    tbody = '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td>' + data[i].emple_nDoc + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>' +
                        '<td>' + data[i].nombre + ' ' + data[i].apPaterno + ' ' + data[i].apMaterno + '&nbsp;&nbsp;</td></tr>';
                    tbodyTabla.push(tbody);
                }
                $('#tbodyExcel').html(tbodyTabla);
                $('#modalEmpleadosHo').modal('show');

                table =
                    $("#tablaEmpleadoExcel").DataTable({

                        "searching": false,
                        "scrollX": true,

                        "ordering": false,
                        "autoWidth": true,

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

                        dom: 'Bfrtip',
                        buttons: [{
                            extend: 'excel',
                            className: 'btn btn-sm mt-1',
                            text: "<i><img src='admin/images/excel.svg' height='20'></i> Descargar",
                            customize: function (xlsx) {
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            },
                            sheetName: 'Exported data',
                            autoFilter: false
                        }],
                        paging: true

                    });

            }
            else {

            }


        },
        error: function () {
            console.log('Ocurrio un error');
        }
    });


    $.ajax({

        url: "/vaciartemporal",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            401: function () {
                location.reload();
            },
            /*419: function () {
                location.reload();
            }*/
        },
        success: function (data) {
            $('#guardarTodoHorario').prop('disabled', false);
            calendar.refetchEvents();

            $.notifyClose();
                $.notify(
                    {
                        message: "\nHorarios registrados a empleado(s).",
                        icon: "admin/images/checked.svg",
                    },
                    {   element: $('#asignarHorario'),
                        position: "fixed",
                        icon_type: "image",
                        newest_on_top: true,
                        delay: 5000,
                        template:
                            '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                            "</div>",
                        spacing: 50,
                    }
                );

                $(".loader").hide();
                $(".img-load").hide();
        },
        error: function () { }
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

    let idIncidencia = $('#incidenciaSelect').val();

    //*VALIDAMOS QUE SE HAYA ESCOGIDO INCIDENCIA
    if(!idIncidencia){
        console.log('primero seleccione incidencia');
        return false;
    }

    let textSelec =  $('select[name="incidenciaSelect"] option:selected').text();

    //*CALUCLOAMOS DIAS ENTRE FECHAS SELECCIONADAS
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
    //******************************************** */

    //*AQUI AÑADIREMOS LOS ARRAY CON FECHA Y NOMBRE INCIDENCIA*/
    var fechasArray = [];
    var fechastart = [];
    var objeto = [ ];

    $.each(results, function (key, value) {
        fechasArray.push(textSelec);
        fechastart.push(value);

        objeto.push({
            "title": textSelec,
            "start": value
        });
    });
    //*********************************************************/
    $.ajax({
        type: "post",
        url: "/horario/registrarInciTemp",
        data: { idIncidencia, fechasArray: fechastart,nombreIncidencia:textSelec },
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
            $('#verhorarioEmpleado').modal('toggle');
            calendar.refetchEvents();
        },
        error: function (data) {
            console.log('Ocurrio un error');
        }
    });


    ;
}

$('#cerrarHorario').click(function () {
    $('#tablaEmpleado').DataTable().ajax.reload(null, false);
    $('#verhorarioEmpleado').modal('toggle');
});

function abrirHorarioen() {
    if ($("*").hasClass("fc-highlight")) {
        $("#frmHoren")[0].reset();
        $('#horarioAgregaren').modal('show');
    } else {
        bootbox.alert({
            title: "Seleccionar dias",
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }

}

function registrarhDias(idhorar) {
    H1 = $('#horario1').val();
    H2 = $('#horario2').val();

    var fueraHora;
    if ($('#fueraHSwitch').prop('checked')) {
        fueraHora = 1;

    } else {
        fueraHora = 0;

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
            if (data == 'Horario asignado') {

            } else {
                bootbox.alert(data);
            }



        },
        error: function (data) {
            console.log('Ocurrio un error');
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
                            console.log('Ocurrio un error');
                        }

                    });

                },
                error: function (data) {
                    console.log('Ocurrio un error');
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
            console.log('Ocurrio un error');
        }
    });
}
function asignarlabo() {
    var H1 = $('#horario1').val();
    var H2 = $('#horario2').val();
    if ($("*").hasClass("fc-highlight")) {
        bootbox.confirm({
            title: "Agregar dias",
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
                            console.log('Ocurrio un error');
                        }


                    });
                }
            }
        });
    } else {
        bootbox.alert({
            title: "Asignar dias",
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
                            console.log('Ocurrio un error');
                        }


                    });
                }
            }
        });
    } else {
        bootbox.alert({
            title: "Asignar dias",
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }


}
function asignarNolabo() {
    H1 = $('#horario1').val();
    H2 = $('#horario2').val();
    if ($("*").hasClass("fc-highlight")) {
        bootbox.confirm({
            title: "Agregar dias",
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
                            console.log('Ocurrio un error');
                        }


                    });
                }
            }
        });
    }
    else {
        bootbox.alert({
            title: "Asignar dias",
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
            title: "Asignar dias",
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
                            console.log('Ocurrio un error');
                        }


                    });
                }
            }
        });
    } else {
        bootbox.alert({
            title: "Asignar dias",
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
            title: "Asginar dias",
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
            title: "Asignar dias",
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
            console.log('Ocurrio un error');
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
            console.log('Ocurrio un error');
        }


    });

}
function vaciarcalendario() {
    bootbox.confirm({
        title: "Vacear calendario",
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

       //*INICIO Y FIN DE MES OBTENIDO DE CALENDARIO

    fmes = calendar.getDate();
   /*  mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear(); */
   var inicioC=  moment(fmes).startOf('month').format('YYYY-MM-DD');
   var finC=moment(fmes).endOf('month').format('YYYY-MM-DD');

   $('#ID_START_EHF').val(inicioC);
   $('#ID_END_EHF').val(finC);
    //*

//* ELEGIR FECHA PARA ELIMINAR HORARIOS */
var fechaValue = $("#fechaSelecElimH").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "j F",
    locale: "es",
    wrap: true,
    allowInput: true,
    conjunction: " a ",
    minRange: 1,

    onChange: function (selectedDates) {
        var _this = this;
        var dateArr = selectedDates.map(function (date) { return _this.formatDate(date, 'Y-m-d'); });
        $('#ID_START_EHF').val(dateArr[0]);
        $('#ID_END_EHF').val(dateArr[1]);



    },
    defaultDate: [inicioC,finC],
    onClose: function (selectedDates, dateStr, instance) {
        if (selectedDates.length == 1) {
            var fm = moment(selectedDates[0]).add("day", -1).format("YYYY-MM-DD");
            instance.setDate([fm, selectedDates[0]], true);
        }
    }
});
$('#modalEliminarHorarioF').modal('show');


}
//*FUNCION QUE ELIMINA HORARIOS FECHA
function eliminarHorariosFecha(){

    //* obtengo empleados, mes y año de calendario
    fmes = calendar.getDate();
    let inicio = $('#ID_START_EHF').val();
    let fin =  $('#ID_END_EHF').val();
    let empleados=$('#nombreEmpleado').val();
    $.ajax({
        type: "get",
        url: "/vaciarhor",
        data: {
            inicio,
            fin,
            empleados
        },
        async:false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            419: function () {
                location.reload();
            }
        },
        success: function (data) {

            var mesAg = $('#fechaDa').val();
            var d = mesAg;
            var fechasM = new Date(d);
            calendar.refetchEvents();
            $('#modalEliminarHorarioF').modal('hide');
            $.notifyClose();
            $.notify(
                {
                    message: "\nHorarios borrados",
                    icon: "admin/images/checked.svg",
                },
                {   element: $('#asignarHorario'),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 5000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 50,
                }
            );

        },
        error: function () {}
    });


}
//*VACIAR INCIDENCIAS
function vaciarIncid() {

    //*INICIO Y FIN DE MES OBTENIDO DE CALENDARIO

    fmes = calendar.getDate();

   var inicioC=  moment(fmes).startOf('month').format('YYYY-MM-DD');
   var finC=moment(fmes).endOf('month').format('YYYY-MM-DD');

   $('#ID_START_EIF').val(inicioC);
   $('#ID_END_EIF').val(finC);
    //*

//* ELEGIR FECHA PARA ELIMINAR HORARIOS */
var fechaValue = $("#fechaSelecElimI").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "j F",
    locale: "es",
    wrap: true,
    allowInput: true,
    conjunction: " a ",
    minRange: 1,

    onChange: function (selectedDates) {
        var _this = this;
        var dateArr = selectedDates.map(function (date) { return _this.formatDate(date, 'Y-m-d'); });
        $('#ID_START_EIF').val(dateArr[0]);
        $('#ID_END_EIF').val(dateArr[1]);



    },
    defaultDate: [inicioC,finC],
    onClose: function (selectedDates, dateStr, instance) {
        if (selectedDates.length == 1) {
            var fm = moment(selectedDates[0]).add("day", -1).format("YYYY-MM-DD");
            instance.setDate([fm, selectedDates[0]], true);
        }
    }
});
$('#modalEliminarIncidenciaF').modal('show');


}

//*FUNCION QUE ELIMINA INCIDENCIAS POR FECHAS
function eliminarIncidenciasFecha(){
   //* obtengo empleados, mes y año de calendario
   let inicio = $('#ID_START_EIF').val();
   let fin =  $('#ID_END_EIF').val();
   let empleados=$('#nombreEmpleado').val();
   $.ajax({
       type: "get",
       url: "/vaciarIncid",
       data: {
           inicio,
           fin,
           empleados
       },
       async:false,
       headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       statusCode: {
           419: function () {
               location.reload();
           }
       },
       success: function (data) {

           calendar.refetchEvents();
           $('#modalEliminarIncidenciaF').modal('hide');
           $.notifyClose();
           $.notify(
               {
                   message: "\nIncidencias borradas",
                   icon: "admin/images/checked.svg",
               },
               {   element: $('#asignarHorario'),
                   position: "fixed",
                   icon_type: "image",
                   newest_on_top: true,
                   delay: 5000,
                   template:
                       '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                       '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                       '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                       '<span data-notify="title">{1}</span> ' +
                       '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                       "</div>",
                   spacing: 50,
               }
           );

       },
       error: function () {}
   });
}
function vaciardl() {
    bootbox.confirm({
        title: "Eliminar dias",
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
        title: "Eliminar dias no laborales",
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
            console.log('Ocurrio un error');
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
              console.log('Ocurrio un error');
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
                          console.log('Ocurrio un error');
                    }

                });


            },
            error: function (data) {
                  console.log('Ocurrio un error');
            }


        });
    } else {
        $("#selectHorarioen").val("Asignar horario");
        $("#selectHorarioen").trigger("change");
        bootbox.alert({
            title: "Asignar dias",
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
                    title: "Eliminar horario",
                    message: "No se puede eliminar un horario en uso.",

                });

                return false;
            }
            else {
                bootbox.confirm({
                    title: "Eliminar horario",
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
                                    $('#tablaEmpleado').DataTable().ajax.reload(null, false);
                                },
                                error: function (data) {
                                      console.log('Ocurrio un error');
                                }


                            });
                        }
                    }
                });

            }
        },
        error: function (data) {
              console.log('Ocurrio un error');
        }

    });

}
///////////////////////
//select todo empleado
$("#selectTodoCheck").click(function () {
    if ($("#selectTodoCheck").is(':checked')) {
        $(".loader").show();
        $(".img-load").show();
        $("#nombreEmpleado > option").prop("selected", "selected");
        $("#nombreEmpleado").trigger("change");

    } else {
        $("#nombreEmpleado > option").prop("selected", false);
        $("#nombreEmpleado").trigger("change");
        $(".loader").hide();
        $(".img-load").hide();
    }
});

//////////////////////
//seleccionar por area, cargo, etc
$('#selectEmpresarial').on('select2:closing', function (e) {
    $(".loader").show();
    $(".img-load").show();
    var idempresarial = [];
    idempresarial = $('#selectEmpresarial').val();
    textSelec = $('select[name="selectEmpresarial"] option:selected:last').text();
    textSelec2 = $('select[name="selectEmpresarial"] option:selected:last').text();
    /*  palabrasepara=textSelec2.split('.')[0];
     alert(palabrasepara);
     return false; */
    palabraEmpresarial = textSelec.split(' ')[0];
    $("#nombreEmpleado > option").prop("selected", false);
    $("#nombreEmpleado").trigger("change");
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

                $.each(data, function (index, value) {
                    $("#nombreEmpleado > option[value='" + value.emple_id + "']").prop("selected", "selected");

                });
                $("#nombreEmpleado").trigger("change");


            },
            error: function (data) {
                  console.log('Ocurrio un error');
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

                $.each(data, function (index, value) {
                    $("#nombreEmpleado > option[value='" + value.emple_id + "']").prop("selected", "selected");

                });
                $("#nombreEmpleado").trigger("change");


            },
            error: function (data) {
                  console.log('Ocurrio un error');
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

                $.each(data, function (index, value) {
                    $("#nombreEmpleado > option[value='" + value.emple_id + "']").prop("selected", "selected");

                });
                $("#nombreEmpleado").trigger("change");


            },
            error: function (data) {
                  console.log('Ocurrio un error');
            }
        });
    }

    //*nivel
    if (palabraEmpresarial == 'Nivel') {
        $.ajax({
            type: "post",
            url: "/horario/empleNivel",
            data: {
                idnivel: idempresarial
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

                $.each(data, function (index, value) {
                    $("#nombreEmpleado > option[value='" + value.emple_id + "']").prop("selected", "selected");

                });
                $("#nombreEmpleado").trigger("change");


            },
            error: function (data) {
                  console.log('Ocurrio un error');
            }
        });
    }

    //*centro costo
    if (palabraEmpresarial == 'Centro') {
        $.ajax({
            type: "post",
            url: "/horario/empleCentro",
            data: {
                idcentro: idempresarial
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

                $.each(data, function (index, value) {
                    $("#nombreEmpleado > option[value='" + value.emple_id + "']").prop("selected", "selected");

                });
                $("#nombreEmpleado").trigger("change");


            },
            error: function (data) {
                  console.log('Ocurrio un error');
            }
        });
    }


})
/////////////////////////////////

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


        } else {
            $('#nHorasAdic').hide();

        }

    });
});
// ********************************** NUEVAS FUNCIONALIDAD ********************************
// ? FUNCION DE RETORNAR RESTA TIEMPO - MINUTOS TOLERANCIA
function sustraerMinutosHoras(tiempo, minuto) {
    var momentTiempo = moment(tiempo, ["HH:mm"]);
    var restaTolerancia = momentTiempo.subtract(minuto, 'minutes');
    var resultado = moment(restaTolerancia.toString()).format("HH:mm");

    return resultado;
}
// ? FUNCION DE RETORNAR SUMA TIEMPO - MINUTOS TOLERANCIA
function sumarMinutosHoras(tiempo, minuto) {
    var momentTiempo = moment(tiempo, ["HH:mm"]);
    var sumaTolerancia = momentTiempo.add(minuto, 'minutes');
    var resultado = moment(sumaTolerancia).format("HH:mm");

    return resultado;
}
// ! ******************************** FORMULARIO REGISTRAR ********************************
// * HORAS OBLIGADAS
var horasO = $('#horaOblig').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
// * HORA DE INICIO
$('#horaI').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    onOpen: function (selectedDates, dateStr, instance) {
        $('#btnGuardaHorario').prop("disabled", true);
    },
    onClose: function (selectedDates, dateStr, instance) {
        $('#btnGuardaHorario').prop("disabled", false);
        if ($('#horaF').val() != "") {
            var horaI = moment($('#horaI').val(), ["HH:mm"]);
            var horaF = moment($('#horaF').val(), ["HH:mm"]);
            if (horaF.isSameOrBefore(horaI)) {
                $('#divOtrodia').show();
                var fechaF = moment().add(1, 'days').format("YYYY-MM-DD");
                var HoraFF = horaF.format("HH:mm");
                var tiempoNuevo = moment(fechaF + " " + HoraFF, ["YYYY-MM-DD HH:mm"]);
                var tiempoSinM = horaI.format("HH:mm");
                var horaS = parseInt(tiempoSinM.split(":")[0]);
                var minuteS = parseInt(tiempoSinM.split(":")[1]);
                var substraccion = tiempoNuevo.subtract({ "hours": horaS, "minutes": minuteS });
                var respuesta = moment(substraccion.toString()).format("HH:mm");
                horasO.setDate(respuesta);
            } else {
                $('#divOtrodia').hide();
                var tiempoSinM = horaI.format("HH:mm");
                var horaS = parseInt(tiempoSinM.split(":")[0]);
                var minuteS = parseInt(tiempoSinM.split(":")[1]);
                var substraccion = horaF.subtract({ "hours": horaS, "minutes": minuteS });
                var respuesta = moment(substraccion.toString()).format("HH:mm");
                horasO.setDate(respuesta);
            }
        }
    },
    onChange: function (selectedDates, dateStr, instance) {
        if ($('#horaF').val() != "") {
            var validacionPH = validarHorasPausaHorario();
            if (!validacionPH) {
                return false;
            } else {
                var validacionEP = validarHorasEntrePausas();
                if (!validacionEP) {
                    return false;
                } else {
                    $('#fueraRango').hide();
                    $('#errorenPausas').hide();
                    $('#errorenPausasCruzadas').hide();
                }
            }
        }
    }
});
// * HORA DE FIN
$('#horaF').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    onOpen: function (selectedDates, dateStr, instance) {
        $('#btnGuardaHorario').prop("disabled", true);
    },
    onClose: function (selectedDates, dateStr, instance) {
        $('#btnGuardaHorario').prop("disabled", false);
        if ($('#horaI').val() != "") {
            var horaI = moment($('#horaI').val(), ["HH:mm"]);  //: -> CONVERTIR EN MOMENT HORA INICIO
            var horaF = moment($('#horaF').val(), ["HH:mm"]);  //: -> CONVERTIR EN MOMENT HORA FIN

            if (horaF.isSameOrBefore(horaI)) {    //: -> CONDICIONAL SI HORA FINAL ES MAYOR IGUAL AL AHORA INICIAL
                $('#divOtrodia').show();          //: -> MOSTRAR CHECH INDICADO QUE ES DEL SIGUIENTE DIA LA FECHA FINAL
                var fechaF = moment().add(1, 'days').format("YYYY-MM-DD"); //: -> OBTENER LA FECCHA DEL DIA SIGUIENTE
                var HoraFF = horaF.format("HH:mm");
                var tiempoNuevo = moment(fechaF + " " + HoraFF, ["YYYY-MM-DD HH:mm"]);
                var tiempoSinM = horaI.format("HH:mm");
                var horaS = parseInt(tiempoSinM.split(":")[0]);
                var minuteS = parseInt(tiempoSinM.split(":")[1]);
                var substraccion = tiempoNuevo.subtract({ "hours": horaS, "minutes": minuteS }); //: -> SUBTRAEMOS EL TIEMPO
                var respuesta = moment(substraccion.toString()).format("HH:mm");
                horasO.setDate(respuesta); //: INSERTAMOS EN PLUGIN
            } else {
                $('#divOtrodia').hide();
                var tiempoSinM = horaI.format("HH:mm");
                var horaS = parseInt(tiempoSinM.split(":")[0]);
                var minuteS = parseInt(tiempoSinM.split(":")[1]);
                var substraccion = horaF.subtract({ "hours": horaS, "minutes": minuteS });
                var respuesta = moment(substraccion.toString()).format("HH:mm");
                horasO.setDate(respuesta);
            }
        }
    },
    onChange: function (selectedDates, dateStr, instance) {
        if ($('#horaI').val() != "") {
            var validacionPH = validarHorasPausaHorario();
            if (!validacionPH) {
                return false;
            } else {
                var validacionEP = validarHorasEntrePausas();
                if (!validacionEP) {
                    return false;
                } else {
                    $('#fueraRango').hide();
                    $('#errorenPausas').hide();
                    $('#errorenPausasCruzadas').hide();
                }
            }
        }
    }
});
function modalRegistrar() {
    $('#horarioAgregar').modal();
    $('#vacioHoraF').hide();
    $('#btnGuardaHorario').prop("disabled", false);
    $('#fueraRango').hide();
    $('#errorenPausas').hide();
    $('#errorenPausasCruzadas').hide();
    $('#toleranciaH').val(0);
    $('#toleranciaSalida').val(0);
    $('#tmIngreso').prop('checked',false);
    $('#tmSalida').prop('checked',false);

}
// * SWITCH DE PAUSAS
var r_cont = 0;
$('#SwitchPausa').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        if ($('#horaF').val() != "" || $('#horaI').val() != "") {
            $('#vacioHoraF').hide();
            $('#divPausa').show();
            $('#inputPausa').empty();
            contenidoInput(undefined);
        } else {
            $('#inputPausa').empty();
            $('#vacioHoraF').show();
            $('#SwitchPausa').prop("checked", false);
        }
    } else {
        $('#divPausa').hide();
        $('#inputPausa').empty();
        r_cont = 0;
    }
});
// * FUNCION DE OINPUT DE TOLERANCIA
function toleranciasValidacion() {
    if ($('#horaF').val() != "" && $('#horaI').val() != "") {
        var validacionPH = validarHorasPausaHorario();
        if (!validacionPH) {
            return false;
        } else {
            var validacionEP = validarHorasEntrePausas();
            if (!validacionEP) {
                return false;
            } else {
                $('#fueraRango').hide();
                $('#errorenPausas').hide();
                $('#errorenPausasCruzadas').hide();
            }
        }
    }
}
// * FUNCION DE AGREGAR CONTENIDO
function contenidoInput(id) {
    if (id != undefined) { //* CUANDO SE REGISTRA POR PRIMERA VEZ UNA PAUSA
        if ($('#rowP' + id).is(":visible")) {
            // ! VALIDACION DE CAMPOS VACIOS
            if ($('#descPausa' + id).val() == "" || $('#InicioPausa' + id).val() == "" || $('#FinPausa' + id).val() == "") {
                $('#validP').show();
                $('#btnGuardaHorario').prop("disabled", true);
                if ($('#descPausa' + id).val() == "") {
                    $('#descPausa' + id).addClass("borderColor");   //: -> AGREGAR CLASE DE REFERENCIA
                }
                if ($('#InicioPausa' + id).val() == "") {
                    $('#InicioPausa' + id).addClass("borderColor");   //: -> AGREGAR CLASE DE REFERENCIA
                }
                if ($('#FinPausa' + id).val() == "") {
                    $('#FinPausa' + id).addClass("borderColor");   //: -> AGREGAR CLASE DE REFERENCIA
                }
                return false;
            }
        }
        $('#btnGuardaHorario').prop("disabled", false);
        $('#fueraRango').hide();
        $('#errorenPausas').hide();
        $('#errorenPausasCruzadas').hide();
        var validacionPH = validarHorasPausaHorario();
        if (!validacionPH) {
            $('#btnGuardaHorario').prop("disabled", true);
            return false;
        } else {
            var validacionEP = validarHorasEntrePausas();
            if (!validacionEP) {
                $('#btnGuardaHorario').prop("disabled", true);
                return false;
            }
            $('#btnGuardaHorario').prop("disabled", false);
        }
    }
    $('#agregar' + id).hide();
    $('#validP').hide();
    var cInputs =
        `<div class="row pb-3" id="rowP${r_cont}" style="border-top:1px dashed #aaaaaa!important;">
                <input type="hidden" class="rowInputs" value="${r_cont}">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Descripción de pausa</label>
                            <input type="text"  class="form-control form-control-sm descP" id="descPausa${r_cont}"
                              onkeyup="javascript:$(this).removeClass('borderColor');$('#btnGuardaHorario').prop('disabled', false);">
                        </div>
                    </div>
                    <div class="row pt-2">
                        <div class="col-md-2">
                            <label>Inicio pausa(24h)</label>
                            <input type="text"  class="form-control form-control-sm inicioP" id="InicioPausa${r_cont}" name="inicioP"
                                onchange="javascript:$(this).removeClass('borderColor');$('#btnGuardaHorario').prop('disabled', false);">
                        </div>
                        <div class="col-md-2">
                            <label>Tolerancia inicio</label>
                            <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                <input type="number"  class="form-control form-control-sm" id="toleranciaIP${r_cont}" value="0"
                                    oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59; if( this.value == '') this.value = 0"
                                    onchange="javascript:toleranciasValidacion()">
                                <div class="input-group-prepend  ">
                                    <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                        min.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>Fin pausa(24h)</label>
                            <input type="text"  class="form-control form-control-sm finP" id="FinPausa${r_cont}" name="finP"
                                onchange="javascript:$(this).removeClass('borderColor');$('#btnGuardaHorario').prop('disabled', false);">
                        </div>
                        <div class="col-md-2">
                            <label>Tolerancia salida</label>
                            <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                <input type="number"  class="form-control form-control-sm" id="ToleranciaFP${r_cont}" value="0"
                                    oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;if( this.value == '') this.value = 0"
                                    onchange="javascript:toleranciasValidacion()">
                                <div class="input-group-prepend  ">
                                    <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                        min.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label>Inactivar</label>
                            <br>
                            <input type="checkbox" id="inactivarPausa${r_cont}" class="text-center mt-2 ml-3">
                        </div>
                        <div class="col-md-2 text-center">
                            <label>Descontar aut.</label>
                            <br>
                            <input type="checkbox" id="descontarPausa${r_cont}" class="mt-2">
                        </div>
                        <div class="col-md-1">
                            <label>Eliminar</label>
                            <br>
                            <a style="cursor: pointer" onclick="javascript:eliminarContenido(${r_cont})" class="ml-3">
                                <img src="/admin/images/delete.svg" height="15">
                            </a>
                        </div>
                    </div>
                    <button class="btn btn-sm bt_plus" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;
                        padding-top: 0px;padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px;
                            margin-top: 5px;margin-left: 20px" onclick="javascript:contenidoInput(${r_cont})" id="agregar${r_cont}">
                        +
                    </button>
                </div>
            </div>`;
    $('#inputPausa').append(cInputs);
    inicializarHorasPausas(r_cont);
    r_cont++;
}
// * FUNCION DE INICIALIZAR INPUT DE HORAS Y MINUTOS
function inicializarHorasPausas(id) {
    // * HORA DE INICIO
    $('#InicioPausa' + id).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        onOpen: function (selectedDates, dateStr, instance) {
            $('#btnGuardaHorario').prop("disabled", true);
        },
        onClose: function (selectedDates, dateStr, instance) {
            $('#btnGuardaHorario').prop("disabled", false);
            $('#fueraRango').hide();
            $('#errorenPausas').hide();
            $('#errorenPausasCruzadas').hide();
            if ($('#horaF').val() != "" || $('#FinPausa' + id).val()) {
                var validacionPH = validarHorasPausaHorario();
                if (!validacionPH) {
                    return false;
                } else {
                    var validacionEP = validarHorasEntrePausas();
                    if (!validacionEP) {
                        return false;
                    } else {
                        $('#fueraRango').hide();
                        $('#errorenPausas').hide();
                        $('#errorenPausasCruzadas').hide();
                    }
                }
            }
        },
        onChange: function (selectedDates, dateStr, instance) {
            $('#fueraRango').hide();
            $('#errorenPausas').hide();
            $('#errorenPausasCruzadas').hide();
            if ($('#horaF').val() != "" || $('#FinPausa' + id).val()) {
                var validacionPH = validarHorasPausaHorario();
                if (!validacionPH) {
                    return false;
                } else {
                    var validacionEP = validarHorasEntrePausas();
                    if (!validacionEP) {
                        return false;
                    } else {
                        $('#fueraRango').hide();
                        $('#errorenPausas').hide();
                        $('#errorenPausasCruzadas').hide();
                    }
                }
            }
        }
    });
    // * HORA DE FIN
    $('#FinPausa' + id).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        onOpen: function (selectedDates, dateStr, instance) {
            $('#btnGuardaHorario').prop("disabled", true);
        },
        onClose: function (selectedDates, dateStr, instance) {
            $('#btnGuardaHorario').prop("disabled", false);
            $('#fueraRango').hide();
            $('#errorenPausas').hide();
            $('#errorenPausasCruzadas').hide();
            if ($('#horaF').val() != "" || $('#InicioPausa' + id).val()) {
                var validacionPH = validarHorasPausaHorario();
                if (!validacionPH) {
                    return false;
                } else {
                    var validacionEP = validarHorasEntrePausas();
                    if (!validacionEP) {
                        return false;
                    } else {
                        $('#fueraRango').hide();
                        $('#errorenPausas').hide();
                        $('#errorenPausasCruzadas').hide();
                    }
                }
            }
        },
        onChange: function (selectedDates, dateStr, instance) {
            $('#fueraRango').hide();
            $('#errorenPausas').hide();
            $('#errorenPausasCruzadas').hide();
            if ($('#horaF').val() != "" || $('#InicioPausa' + id).val()) {
                var validacionPH = validarHorasPausaHorario();
                if (!validacionPH) {
                    return false;
                } else {
                    var validacionEP = validarHorasEntrePausas();
                    if (!validacionEP) {
                        return false;
                    } else {
                        $('#fueraRango').hide();
                        $('#errorenPausas').hide();
                        $('#errorenPausasCruzadas').hide();
                    }
                }
            }
        }
    });
}
// * FUNCION DE VALIDAR HORAS DE PAUSAS CON HORAS DEL HORARIO
function validarHorasPausaHorario() {
    var estado = true;
    $('.rowInputs').each(function () {
        var idI = $(this).val();
        if ($('#descPausa' + idI).val() != "" && $('#InicioPausa' + idI).val() != "" && $('#FinPausa' + idI).val() != "") {
            // * -> ******************************************** TIEMPOS DE INPUTS ********************************************
            // : INICIO DE PAUSA MENOS LA TOLERANCIA DE INICIO PARA ENCONTRAR EL MINIMO DE INICIO
            var resultadoResta = sustraerMinutosHoras($('#InicioPausa' + idI).val(), parseInt($('#toleranciaIP' + idI).val()));
            var horaI = moment(resultadoResta, ["HH:mm"]);
            // : FIN DE PAUSA MAS LA TOLERANCIA DE FIN PARA ENCONTRAR EL MAXIMO DE FIN
            var resultadoSuma = sumarMinutosHoras($('#FinPausa' + idI).val(), parseInt($('#ToleranciaFP' + idI).val()));
            var horaF = moment(resultadoSuma, ["HH:mm"]);
            // * -> ********************************************** TIEMPOS DE HORARIO ******************************************
            // : INICIO DE HORARIO MENOS LA TOLERANCIA DE INICIO PARA ENCONTRAR EL MINIMO DE INICIO DE HORARIO
            var resultadoRestaHorario = sustraerMinutosHoras($('#horaI').val(), parseInt($('#toleranciaH').val()));
            var momentInicio = moment(resultadoRestaHorario, ["HH:mm"]);
            // : FIN DE HORARIO MAS LA TOLERANCIA DE FIN PARA ENCONTRAR EL MAXIMO DE FIN DE HORARIO
            var resultadoSumaHorario = sumarMinutosHoras($('#horaF').val(), parseInt($('#toleranciaSalida').val()));
            var momentFin = moment(resultadoSumaHorario, ["HH:mm"]);
            // * VALIDACION CON TIEMPOS DE HORARIO
            if (momentFin.isSameOrBefore(momentInicio)) {    //: -> <=
                // ! NUEVA FECHA DE HORA INICIO DE PAUSA
                var nuevoInicio;
                if (horaI.isBefore(momentInicio)) {        //: -> <
                    nuevoInicio = horaI.add(1, 'day');    //: -> NUEVA FECHA
                } else {
                    nuevoInicio = horaI;
                }
                // ! NUEVA FECHA DE HORA FIN
                var nuevoFin;
                if (horaF.isBefore(momentInicio)) {
                    nuevoFin = horaF.add(1, 'day');    //: -> NUEVA FECHA
                } else {
                    nuevoFin = horaF;
                }
                // ! VALIDACION DE HORA INICIO DE PAUSA
                var nuevoF = momentFin.add(1, 'day');    //: -> NUEVA FECHA
                if (!nuevoInicio.isAfter(momentInicio) || !nuevoInicio.isBefore(momentFin) || !nuevoFin.isBefore(nuevoF)) {
                    $('#fueraRango').show();
                    estado = false;
                }
            } else {
                if (!horaI.isAfter(momentInicio) || !horaI.isBefore(momentFin) || !horaF.isBefore(momentFin)) {
                    $('#fueraRango').show();
                    estado = false;
                } else {
                    if (horaF.isBefore(horaI)) {
                        $('#errorenPausas').show();
                        estado = false;
                    }
                }
            }
        }
    });
    return estado;
}
// * FUNCION DE VALIDAR HORAS ENTRE PAUSAS
function validarHorasEntrePausas() {
    var estado = true;
    $('.rowInputs').each(function () {
        var idI = $(this).val();
        if ($('#descPausa' + idI).val() != "" && $('#InicioPausa' + idI).val() != "" && $('#FinPausa' + idI).val() != "") {
            // * -> ******************************************** TIEMPOS DE INPUTS ********************************************
            // : INICIO DE PAUSA MENOS LA TOLERANCIA DE INICIO PARA ENCONTRAR EL MINIMO DE INICIO
            var resultadoResta = sustraerMinutosHoras($('#InicioPausa' + idI).val(), parseInt($('#toleranciaIP' + idI).val()));
            var horaI = moment(resultadoResta, ["HH:mm"]);
            // : FIN DE PAUSA MAS LA TOLERANCIA DE FIN PARA ENCONTRAR EL MAXIMO DE FIN
            var resultadoSuma = sumarMinutosHoras($('#FinPausa' + idI).val(), parseInt($('#ToleranciaFP' + idI).val()));
            var horaF = moment(resultadoSuma, ["HH:mm"]);
            // * -> ********************************************** TIEMPOS DE HORARIO ******************************************
            // : INICIO DE HORARIO MENOS LA TOLERANCIA DE INICIO PARA ENCONTRAR EL MINIMO DE INICIO DE HORARIO
            var resultadoRestaHorario = sustraerMinutosHoras($('#horaI').val(), parseInt($('#toleranciaH').val()));
            var momentInicio = moment(resultadoRestaHorario, ["HH:mm"]);
            // : FIN DE HORARIO MAS LA TOLERANCIA DE FIN PARA ENCONTRAR EL MAXIMO DE FIN DE HORARIO
            var resultadoSumaHorario = sumarMinutosHoras($('#horaF').val(), parseInt($('#toleranciaSalida').val()));
            var momentFin = moment(resultadoSumaHorario, ["HH:mm"]);
            // * VALIDACION ENTRE PAUSAS
            $('.rowInputs').each(function () {
                var idC = $(this).val();
                if (idI != idC) {
                    if ($('#descPausa' + idC).val() != "" && $('#InicioPausa' + idC).val() != "" && $('#FinPausa' + idC).val() != "") {
                        // : INICIO DE PAUSA MENOS LA TOLERANCIA DE INICIO PARA ENCONTRAR EL MINIMO DE INICIO
                        var resultadoRestaNuevo = sustraerMinutosHoras($('#InicioPausa' + idC).val(), parseInt($('#toleranciaIP' + idC).val()));
                        var horaCompararI = moment(resultadoRestaNuevo, ["HH:mm"]);
                        // : FIN DE PAUSA MAS LA TOLERANCIA DE FIN PARA ENCONTRAR EL MAXIMO DE FIN
                        var resultadoSumaNuevo = sumarMinutosHoras($('#FinPausa' + idC).val(), parseInt($('#ToleranciaFP' + idC).val()));
                        var horaCompararF = moment(resultadoSumaNuevo, ["HH:mm"]);
                        if (momentFin.isSameOrBefore(momentInicio)) {   //: -> <=
                            // ! NUEVA FECHA DE HORA INICIO DE PAUSA
                            var nuevoInicioC;
                            if (horaCompararI.isBefore(momentInicio)) {        //: -> <
                                nuevoInicioC = horaCompararI.add(1, 'day');    //: -> NUEVA FECHA
                            } else {
                                nuevoInicioC = horaCompararI;
                            }
                            // ! NUEVA FECHA DE HORA FIN
                            var nuevoFinC;
                            if (horaCompararF.isBefore(momentInicio)) {
                                nuevoFinC = horaCompararF.add(1, 'day');    //: -> NUEVA FECHA
                            } else {
                                nuevoFinC = horaCompararF;
                            }
                            // ! NUEVA FECHA DE HORA INICIO DE PAUSA
                            var nuevoInicio;
                            if (horaI.isBefore(momentInicio)) {        //: -> <
                                nuevoInicio = horaI.add(1, 'day');    //: -> NUEVA FECHA
                            } else {
                                nuevoInicio = horaI;
                            }
                            // ! NUEVA FECHA DE HORA FIN
                            var nuevoFin;
                            if (horaF.isBefore(momentInicio)) {
                                nuevoFin = horaF.add(1, 'day');    //: -> NUEVA FECHA
                            } else {
                                nuevoFin = horaF;
                            }
                            if (nuevoInicioC.isAfter(nuevoInicio) && nuevoInicioC.isBefore(nuevoFin)) {
                                $('#errorenPausasCruzadas').show();
                                estado = false;
                            } else {
                                if (horaF.isBefore(horaI)) {
                                    $('#errorenPausas').show();
                                    estado = false;
                                }
                            }
                        } else {
                            if (horaCompararI.isAfter(horaI) && horaCompararI.isBefore(horaF)) {
                                $('#errorenPausasCruzadas').show();
                                estado = false;
                            } else {
                                if (horaF.isBefore(horaI)) {
                                    $('#errorenPausas').show();
                                    estado = false;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
    return estado;
}
// * VALIDAR INPUTS
function validarInputs() {
    var estado = true;
    $('.rowInputs').each(function () {
        var id = $(this).val();
        if ($('#rowP' + id).is(":visible")) {
            // ! VALIDACION DE CAMPOS VACIOS
            if ($('#descPausa' + id).val() == "" || $('#InicioPausa' + id).val() == "" || $('#FinPausa' + id).val() == "") {
                $('#validP').show();
                $('#btnGuardaHorario').prop("disabled", true);
                if ($('#descPausa' + id).val() == "") {
                    $('#descPausa' + id).addClass("borderColor");   //: -> AGREGAR CLASE DE REFERENCIA
                }
                if ($('#InicioPausa' + id).val() == "") {
                    $('#InicioPausa' + id).addClass("borderColor");   //: -> AGREGAR CLASE DE REFERENCIA
                }
                if ($('#FinPausa' + id).val() == "") {
                    $('#FinPausa' + id).addClass("borderColor");   //: -> AGREGAR CLASE DE REFERENCIA
                }
                estado = false;
            }
        }
    });
    return estado;
}
// * FUNCION DE ELIMINAR CONTENIDO
function eliminarContenido(id) {
    $('#descPausa' + id).val("");
    $('#InicioPausa' + id).val("");
    $('#FinPausa' + id).val("");
    $('#toleranciaIP' + id).val("");
    $('#ToleranciaFP' + id).val("");
    $('#inactivarPausa' + id).prop("checked", false);
    $('#descontarPausa' + id).prop("checked", false);
    $('#rowP' + id).hide();
    $('#fueraRango').hide();
    $('#errorenPausas').hide();
    $('#errorenPausasCruzadas').hide();
    var validacionPH = validarHorasPausaHorario();
    if (!validacionPH) {
        $('#btnGuardaHorario').prop("disabled", true);
        return false;
    } else {
        var validacionEP = validarHorasEntrePausas();
        if (!validacionEP) {
            $('#btnGuardaHorario').prop("disabled", true);
            return false;
        }
        $('#btnGuardaHorario').prop("disabled", false);
        $('#fueraRango').hide();
        $('#errorenPausas').hide();
        $('#errorenPausasCruzadas').hide();
    }
    var resp = true;
    var ultimoVisible = undefined;
    $('.rowInputs').each(function () {
        var id = $(this).val();
        if ($('#rowP' + id).is(":visible")) {
            resp = false;
            ultimoVisible = id;
        }
    });
    if (ultimoVisible != undefined) $('#agregar' + ultimoVisible).show();
    if (resp) {
        contenidoInput(r_cont);
    }
}
// * OBTENER PAUSAS
function obtenerPausas() {
    var resultado = [];
    $('.rowInputs').each(function () {
        var id = $(this).val();
        var descripcion = $('#descPausa' + id).val();
        var inicioPausa = $('#InicioPausa' + id).val();
        var toleranciaPI = $('#toleranciaIP' + id).val();
        var finPausa = $('#FinPausa' + id).val();
        var toleranciaPF = $('#ToleranciaFP' + id).val();
        var inactivarP;
        var descontarP;
        if ($('#inactivarPausa' + id).is(":checked")) {
            inactivarP = 1;
        } else {
            inactivarP = 0;
        }
        if ($('#descontarPausa' + id).is(":checked")) {
            descontarP = 1;
        } else {
            descontarP = 0;
        }
        var objPausa = {
            "id": $(this).val(),
            "descripcion": descripcion,
            "inicioPausa": inicioPausa,
            "toleranciaI": toleranciaPI,
            "finPausa": finPausa,
            "toleranciaF": toleranciaPF,
            "inactivar": inactivarP,
            "descontar": descontarP
        };
        resultado.push(objPausa);
    });

    return resultado;
}
// * REGISTRAR NUEVO HORARIO
function registrarNuevoHorario() {
    var descripcion = $('#descripcionCa').val();
    var horaInicio = $('#horaI').val();
    var horaFin = $('#horaF').val();
    var toleranciaI = $('#toleranciaH').val();
    var toleranciaF = $('#toleranciaSalida').val();
    var horasO = $('#horaOblig').val();
    var idReglaHExtras=$('#idReglaHora').val();
    var idReglaHExtrasNoc=$('#idReglaHoraNocturna').val();

    //*Tiempos muertos
    var tmIngreso;
    var tmSalida;

    //tm ingreso
    if ($('#tmIngreso').is(":checked")) {
        tmIngreso = 1;
    } else {
        tmIngreso = 0;
    }

    //tm salida
    if ($('#tmSalida').is(":checked")) {
        tmSalida = 1;
    } else {
        tmSalida= 0;
    }

    var pausas = obtenerPausas();
    var validarInput = validarInputs();
    if (!validarInput) {
        return false;
    } else {
        $('#validP').hide();
    }
    var validacionPH = validarHorasPausaHorario();
    var validacionEP = validarHorasEntrePausas();
    if (!validacionPH) {
        return false;
    } else {
        if (!validacionEP) {
            return false;
        }
    }
    $.ajax({
        async: false,
        type: "POST",
        url: "/nuevoHorario",
        data: {
            descripcion: descripcion,
            toleranciaI: toleranciaI,
            toleranciaF: toleranciaF,
            horaInicio: horaInicio,
            horaFin: horaFin,
            horasO: horasO,
            pausas: pausas,
            tmIngreso,
            tmSalida,
            idReglaHExtras,
            idReglaHExtrasNoc
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
            if ($('#verhorarioEmpleado').is(':visible')) {
                obtenerHorarios();
                $('#selectHorario').val(data).trigger('change');
            }
            $('#horarioAgregar').modal('toggle');
            limpiarHorario();
            $('#tablaEmpleado').DataTable().ajax.reload(null, false);
        },
        error: function () { }
    });
}
function limpiarHorario() {
    $('#divPausa').hide();
    $('#inputPausa').empty();
    $('#descripcionCa').val("");
    $('#horaI').val("");
    $('#horaF').val("");
    $('#toleranciaH').val("");
    $('#toleranciaSalida').val("");
    $('#horaOblig').val("");
    $('#SwitchPausa').prop("checked", false);
}
// ! ******************************* FINALIZACION ****************************************
// ! ******************************* MODAL EDITAR ****************************************
// : HORA DE INICIO DE PAUSA
var e_horaInicio = $('#horaI_ed').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    onOpen: function (selectedDates, dateStr, instance) {
        $('#btnEditarHorario').prop("disabled", true);
    },
    onClose: function (selectedDates, dateStr, instance) {
        $('#btnEditarHorario').prop("disabled", false);
    },
    onChange: function (selectedDates, dateStr, instance) {
        if ($('#horaF_ed').val() != "") {
            var validacionPH = e_validarHorasPausaHorario();
            if (!validacionPH) {
                return false;
            } else {
                var validacionEP = e_validarHorasEntrePausas();
                if (!validacionEP) {
                    return false;
                } else {
                    $('#fueraRango_ed').hide();
                    $('#errorenPausas_ed').hide();
                    $('#errorenPausasCruzadas_ed').hide();
                }
            }
        }
    }
});
// : HORA DE FIN DE PAUSA
var e_horaFin = $('#horaF_ed').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    onOpen: function (selectedDates, dateStr, instance) {
        $('#btnEditarHorario').prop("disabled", true);
    },
    onClose: function (selectedDates, dateStr, instance) {
        $('#btnEditarHorario').prop("disabled", false);
    },
    onChange: function (selectedDates, dateStr, instance) {
        if ($('#horaI_ed').val() != "") {
            var validacionPH = e_validarHorasPausaHorario();
            if (!validacionPH) {
                return false;
            } else {
                var validacionEP = e_validarHorasEntrePausas();
                if (!validacionEP) {
                    return false;
                } else {
                    $('#fueraRango_ed').hide();
                    $('#errorenPausas_ed').hide();
                    $('#errorenPausasCruzadas_ed').hide();
                }
            }
        }
    }
});
// : HORAS OBLIGADAS
var e_horaOb = $('#horaOblig_ed').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
// : FUNCION DE OINPUT DE TOLERANCIA
function e_toleranciasValidacion() {
    if ($('#horaF_ed').val() != "" && $('#horaI_ed').val() != "") {
        var validacionPH = e_validarHorasPausaHorario();
        if (!validacionPH) {
            return false;
        } else {
            var validacionEP = e_validarHorasEntrePausas();
            if (!validacionEP) {
                return false;
            } else {
                $('#fueraRango_ed').hide();
                $('#errorenPausas_ed').hide();
                $('#errorenPausasCruzadas_ed').hide();
            }
        }
    }
}
var e_cont = 0;
function modalEditar(id) {
    e_cont = 0;
    $.ajax({
        async: false,
        type: "POST",
        url: "/verDatahorario",
        data: { id: id },
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
            $('#btnEditarHorario').prop("disabled", false);
            $('#fueraRango_ed').hide();
            $('#errorenPausas_ed').hide();
            $('#errorenPausasCruzadas_ed').hide();
            $('#vacioHoraF_ed').hide();
            $('#idhorario_ed').val(data.horario[0].horario_id);
            $('#descripcionCa_ed').val(data.horario[0].horario_descripcion);
            e_horaInicio.setDate(data.horario[0].horaI);
            e_horaFin.setDate(data.horario[0].horaF);
            e_horaOb.setDate(data.horario[0].horasObliga);
            $('#toleranciaH_ed').val(data.horario[0].horario_tolerancia);
            $('#toleranciaSalida_ed').val(data.horario[0].horario_toleranciaF);

            //tiempo muerto ingreso
            if(data.horario[0].tiempoMingreso==1){
                $('#tmIngreso_ed').prop('checked',true);
            } else{
                $('#tmIngreso_ed').prop('checked',false);
            }

            //tiempo muerto salida
            if(data.horario[0].tiempoMsalida==1){
                $('#tmSalida_ed').prop('checked',true);
            } else{
                $('#tmSalida_ed').prop('checked',false);
            }

            //regla
            $('#idReglaHora_ed').val(data.horario[0].idreglas_horasExtras);
            $("#idReglaHora_ed").trigger("change");

            //regla nocturna
            $('#idReglaHoraNocturna_ed').val(data.horario[0].idreglas_horasExtrasNoct);
            $("#idReglaHoraNocturna_ed").trigger("change");



            // ************************************** PAUSAS ***********************
            if (data.pausas.length != 0) {
                $('#SwitchPausa_ed').prop("checked", true);
                $('#PausasHorar_ed').empty();
                var contenido = "";
                for (let index = 0; index < data.pausas.length; index++) {
                    var pausa = data.pausas[index];

                    contenido +=
                        `<div class="row pb-3" id="e_rowP${pausa.idpausas_horario}" style="border-top:1px dashed #aaaaaa!important;">
                            <input type="hidden" class="e_rowInputs" value="${pausa.idpausas_horario}">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Descripción de pausa</label>
                                        <input type="text"  class="form-control form-control-sm descP" id="e_descPausa${pausa.idpausas_horario}"
                                          value="${pausa.pausH_descripcion}"onkeyup="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                                    </div>
                                </div>
                                <div class="row pt-2">
                                    <div class="col-md-2">
                                        <label>Inicio pausa(24h)</label>
                                        <input type="text"  class="form-control form-control-sm inicioP" id="e_InicioPausa${pausa.idpausas_horario}" name="inicioP"
                                          value="${pausa.pausH_Inicio}"  onchange="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Tolerancia inicio</label>
                                        <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                            <input type="number"  class="form-control form-control-sm" id="e_toleranciaIP${pausa.idpausas_horario}" value="${pausa.tolerancia_inicio}"
                                               oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;if( this.value == '') this.value = 0" onchange="javascript:e_toleranciasValidacion()">
                                            <div class="input-group-prepend  ">
                                                <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                    min.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Fin pausa(24h)</label>
                                        <input type="text"  class="form-control form-control-sm finP" id="e_FinPausa${pausa.idpausas_horario}" name="finP"
                                           value="${pausa.pausH_Fin}" onchange="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Tolerancia salida</label>
                                        <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                            <input type="number"  class="form-control form-control-sm" id="e_ToleranciaFP${pausa.idpausas_horario}" value="${pausa.tolerancia_fin}"
                                               oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59; if( this.value == '') this.value = 0" onchange="javascript:e_toleranciasValidacion()">
                                            <div class="input-group-prepend  ">
                                                <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                    min.
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                    if (pausa.inactivar == 0) {
                        contenido += `
                                    <div class="col-md-1">
                                        <label>Inactivar</label>
                                        <br>
                                        <input type="checkbox" id="e_inactivarPausa${pausa.idpausas_horario}" class="mt-2 ml-3">
                                    </div>`;
                    } else {
                        contenido += `<div class="col-md-1">
                                        <label>Inactivar</label>
                                        <br>
                                        <input type="checkbox" id="e_inactivarPausa${pausa.idpausas_horario}" class="mt-2 ml-3" checked>
                                    </div>`;
                    }
                    if (pausa.descontar == 0) {
                        contenido += `<div class="col-md-2 text-center">
                                        <label>Descontar aut.</label>
                                        <br>
                                        <input type="checkbox" id="e_descontarPausa${pausa.idpausas_horario}" class="mt-2">
                                    </div>`;
                    } else {
                        contenido += `<div class="col-md-2 text-center">
                                        <label>Descontar aut.</label>
                                        <br>
                                        <input type="checkbox" id="e_descontarPausa${pausa.idpausas_horario}" class="mt-2" checked>
                                    </div>`;
                    }
                    contenido += `
                                    <div class="col-md-1">
                                        <label>Eliminar</label>
                                        <br>
                                        <a style="cursor: pointer" onclick="javascript:e_eliminarContenido(${pausa.idpausas_horario})" class="ml-3">
                                            <img src="/admin/images/delete.svg" height="15">
                                        </a>
                                    </div>
                                </div>`;
                    if (index != (data.pausas.length - 1)) {
                        contenido += `
                                <button class="btn btn-sm bt_plus" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;
                                    padding-top: 0px;padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px;
                                        margin-top: 5px;margin-left: 20px; display:none" onclick="javascript:e_contenidoInput(${pausa.idpausas_horario})" id="e_agregar${pausa.idpausas_horario}">
                                    +
                                </button>`;
                    } else {
                        contenido += `
                                <button class="btn btn-sm bt_plus" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;
                                    padding-top: 0px;padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px;
                                        margin-top: 5px;margin-left: 20px;" onclick="javascript:e_contenidoInput(${pausa.idpausas_horario})" id="e_agregar${pausa.idpausas_horario}">
                                    +
                                </button>`;
                    }
                    contenido += `
                            </div>
                        </div>`;
                }
                $('#PausasHorar_ed').append(contenido);
                $('#pausas_edit').show();
                data.pausas.forEach(element => {
                    e_inicializarHorasPausas(element.idpausas_horario);
                });
            } else {
                $('#SwitchPausa_ed').prop("checked", false);
                $('#pausas_edit').hide();
            }
            // * COMPARAR TIEMPOS
            var horaI = moment(data.horario[0].horaI, ["HH:mm"]);
            var horaF = moment(data.horario[0].horaF, ["HH:mm"]);
            if (horaF.isBefore(horaI)) {
                $('#divOtrodia_ed').show();
            } else {
                $('#divOtrodia_ed').hide();
            }
            $('#horarioEditar').modal();
        },
        error: function () { }
    });
}
// : FUNCION DE INICIALIZAR TIEMPO EN EDITAR
function e_inicializarHorasPausas(id) {
    // ***************************************** INICIO DE PAUSA ********************************
    $('#e_InicioPausa' + id).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        onOpen: function (selectedDates, dateStr, instance) {
            $('#btnEditarHorario').prop("disabled", true);
        },
        onClose: function (selectedDates, dateStr, instance) {
            $('#btnEditarHorario').prop("disabled", false);
            $('#fueraRango_ed').hide();
            $('#errorenPausas_ed').hide();
            $('#errorenPausasCruzadas_ed').hide();
            if ($('#horaF_ed').val() != "" || $('#e_FinPausa' + id).val()) {
                var validacionPH = e_validarHorasPausaHorario();
                if (!validacionPH) {
                    return false;
                } else {
                    var validacionEP = e_validarHorasEntrePausas();
                    if (!validacionEP) {
                        return false;
                    } else {
                        $('#fueraRango_ed').hide();
                        $('#errorenPausas_ed').hide();
                        $('#errorenPausasCruzadas_ed').hide();
                    }
                }
            }
        },
        onChange: function (selectedDates, dateStr, instance) {
            $('#btnEditarHorario').prop("disabled", true);
            $('#fueraRango_ed').hide();
            $('#errorenPausas_ed').hide();
            $('#errorenPausasCruzadas_ed').hide();
            if ($('#horaF_ed').val() != "" || $('#e_FinPausa' + id).val()) {
                var validacionPH = e_validarHorasPausaHorario();
                if (!validacionPH) {
                    return false;
                } else {
                    var validacionEP = e_validarHorasEntrePausas();
                    if (!validacionEP) {
                        return false;
                    } else {
                        $('#fueraRango_ed').hide();
                        $('#errorenPausas_ed').hide();
                        $('#errorenPausasCruzadas_ed').hide();
                    }
                }
            }
        }
    });
    // ***************************************************** FIN DE PAUSA ***********************************
    $('#e_FinPausa' + id).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        onOpen: function (selectedDates, dateStr, instance) {
            $('#btnEditarHorario').prop("disabled", true);
        },
        onClose: function (selectedDates, dateStr, instance) {
            $('#btnEditarHorario').prop("disabled", false);
            $('#fueraRango_ed').hide();
            $('#errorenPausas_ed').hide();
            $('#errorenPausasCruzadas_ed').hide();
            if ($('#horaF_ed').val() != "" || $('#e_InicioPausa' + id).val()) {
                var validacionPH = e_validarHorasPausaHorario();
                if (!validacionPH) {
                    return false;
                } else {
                    var validacionEP = e_validarHorasEntrePausas();
                    if (!validacionEP) {
                        return false;
                    } else {
                        $('#fueraRango_ed').hide();
                        $('#errorenPausas_ed').hide();
                        $('#errorenPausasCruzadas_ed').hide();
                    }
                }
            }
        },
        onChange: function (selectedDates, dateStr, instance) {
            $('#btnEditarHorario').prop("disabled", true);
            $('#fueraRango_ed').hide();
            $('#errorenPausas_ed').hide();
            $('#errorenPausasCruzadas_ed').hide();
            if ($('#horaF_ed').val() != "" || $('#e_InicioPausa' + id).val()) {
                var validacionPH = e_validarHorasPausaHorario();
                if (!validacionPH) {
                    return false;
                } else {
                    var validacionEP = e_validarHorasEntrePausas();
                    if (!validacionEP) {
                        return false;
                    } else {
                        $('#fueraRango_ed').hide();
                        $('#errorenPausas_ed').hide();
                        $('#errorenPausasCruzadas_ed').hide();
                    }
                }
            }
        }
    });
}
// : FUNCION DE AGREGAR CONTENIDO
function e_contenidoInput(id) {
    if (id != undefined) { //* CUANDO SE REGISTRA POR PRIMERA VEZ UNA PAUSA
        // ! VALIDACION DE CAMPOS VACIOS
        if ($('#e_rowP' + id).is(":visible")) {
            if ($('#e_descPausa' + id).val() == "" || $('#e_InicioPausa' + id).val() == "" || $('#e_FinPausa' + id).val() == "") {
                $('#validP_ed').show();
                $('#btnEditarHorario').prop("disabled", true);
                if ($('#e_descPausa' + id).val() == "") {
                    $('#e_descPausa' + id).addClass("borderColor");   //: -> AGREGAR CLASE DE REFERENCIA
                }
                if ($('#e_InicioPausa' + id).val() == "") {
                    $('#e_InicioPausa' + id).addClass("borderColor");   //: -> AGREGAR CLASE DE REFERENCIA
                }
                if ($('#e_FinPausa' + id).val() == "") {
                    $('#e_FinPausa' + id).addClass("borderColor");   //: -> AGREGAR CLASE DE REFERENCIA
                }
                return false;
            }
        }
        $('#btnEditarHorario').prop("disabled", false);
        $('#fueraRango_ed').hide();
        $('#errorenPausas_ed').hide();
        $('#errorenPausasCruzadas_ed').hide();
        var validacionPH = e_validarHorasPausaHorario();
        if (!validacionPH) {
            $('#btnEditarHorario').prop("disabled", true);
            return false;
        } else {
            var validacionEP = e_validarHorasEntrePausas();
            if (!validacionEP) {
                $('#btnEditarHorario').prop("disabled", true);
                return false;
            }
            $('#btnEditarHorario').prop("disabled", false);
        }
    }
    $('#e_agregar' + id).hide();
    $('#validP_ed').hide();
    var cInputs =
        `<div class="row pb-3" id="e_rowPNew${e_cont}" style="border-top:1px dashed #aaaaaa!important;">
                <input type="hidden" class="e_rowInputs" value="New${e_cont}">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Descripción de pausa</label>
                            <input type="text"  class="form-control form-control-sm descP" id="e_descPausaNew${e_cont}"
                              onkeyup="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label>Inicio pausa(24h)</label>
                            <input type="text"  class="form-control form-control-sm inicioP" id="e_InicioPausaNew${e_cont}" name="inicioP"
                                onchange="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                        </div>
                        <div class="col-md-2">
                            <label>Tolerancia inicio</label>
                            <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                <input type="number"  class="form-control form-control-sm" id="e_toleranciaIPNew${e_cont}" value="0"
                                    oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59; if( this.value == '') this.value = 0"
                                    onchange="javascript:e_toleranciasValidacion()">
                                <div class="input-group-prepend  ">
                                    <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                        min.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>Fin pausa(24h)</label>
                            <input type="text"  class="form-control form-control-sm finP" id="e_FinPausaNew${e_cont}" name="finP"
                                onchange="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                        </div>
                        <div class="col-md-2">
                            <label>Tolerancia salida</label>
                            <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                <input type="number"  class="form-control form-control-sm" id="e_ToleranciaFPNew${e_cont}" value="0"
                                    oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59; if( this.value == '') this.value = 0"
                                    onchange="javascript:e_toleranciasValidacion()">
                                <div class="input-group-prepend">
                                    <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                        min.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label>Inactivar</label>
                            <br>
                            <input type="checkbox" id="e_inactivarPausaNew${e_cont}" class="text-center mt-2 ml-3">
                        </div>
                        <div class="col-md-2 text-center">
                            <label>Descontar aut.</label>
                            <br>
                            <input type="checkbox" id="e_descontarPausa${e_cont}" class="text-center mt-2 ml-3">
                        </div>
                        <div class="col-md-1">
                            <label>Eliminar</label>
                            <br>
                            <a style="cursor: pointer" onclick="javascript:e_eliminarContenido('New${e_cont}')" class="ml-3">
                                <img src="/admin/images/delete.svg" height="15">
                            </a>
                        </div>
                    </div>
                    <button class="btn btn-sm bt_plus" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;
                        padding-top: 0px;padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px;
                            margin-top: 5px;margin-left: 20px" onclick="javascript:e_contenidoInput('New${e_cont}')" id="e_agregarNew${e_cont}">
                        +
                    </button>
                </div>
            </div>`;
    $('#PausasHorar_ed').append(cInputs);
    e_inicializarHorasPausas('New' + e_cont);
    e_cont++;
}
// : FUNCION DE VALIDAR HORAS DE PAUSAS CON HORAS DEL HORARIO
function e_validarHorasPausaHorario() {
    var estado = true;
    $('.e_rowInputs').each(function () {
        var idI = $(this).val();
        if ($('#e_descPausa' + idI).val() != "" && $('#e_InicioPausa' + idI).val() != "" && $('#e_FinPausa' + idI).val() != "") {
            // * -> ******************************************** TIEMPOS DE INPUTS ********************************************
            // : INICIO DE PAUSA MENOS LA TOLERANCIA DE INICIO PARA ENCONTRAR EL MINIMO DE INICIO
            var resultadoResta = sustraerMinutosHoras($('#e_InicioPausa' + idI).val(), parseInt($('#e_toleranciaIP' + idI).val()));
            var horaI = moment(resultadoResta, ["HH:mm"]);
            // : FIN DE PAUSA MAS LA TOLERANCIA DE FIN PARA ENCONTRAR EL MAXIMO DE FIN
            var resultadoSuma = sumarMinutosHoras($('#e_FinPausa' + idI).val(), parseInt($('#e_ToleranciaFP' + idI).val()));
            var horaF = moment(resultadoSuma, ["HH:mm"]);

            // * -> ********************************************** TIEMPOS DE HORARIO ******************************************
            // : INICIO DE HORARIO MENOS LA TOLERANCIA DE INICIO PARA ENCONTRAR EL MINIMO DE INICIO DE HORARIO
            var resultadoRestaHorario = sustraerMinutosHoras($('#horaI_ed').val(), parseInt($('#toleranciaH_ed').val()));
            var momentInicio = moment(resultadoRestaHorario, ["HH:mm"]);
            // : FIN DE HORARIO MAS LA TOLERANCIA DE FIN PARA ENCONTRAR EL MAXIMO DE FIN DE HORARIO
            var resultadoSumaHorario = sumarMinutosHoras($('#horaF_ed').val(), parseInt($('#toleranciaSalida_ed').val()));
            var momentFin = moment(resultadoSumaHorario, ["HH:mm"]);
            // ************************************************** VALIDACION CON TIEMPOS DE HORARIO ****************************
            if (momentFin.isSameOrBefore(momentInicio)) {    //: -> <=
                // ! NUEVA FECHA DE HORA INICIO DE PAUSA
                var nuevoInicio;
                if (horaI.isBefore(momentInicio)) {        //: -> <
                    nuevoInicio = horaI.add(1, 'day');    //: -> NUEVA FECHA
                } else {
                    nuevoInicio = horaI;
                }
                // ! NUEVA FECHA DE HORA FIN
                var nuevoFin;
                if (horaF.isBefore(momentInicio)) {
                    nuevoFin = horaF.add(1, 'day');    //: -> NUEVA FECHA
                } else {
                    nuevoFin = horaF;
                }
                // ! VALIDACION DE HORA INICIO DE PAUSA
                var nuevoF = momentFin.add(1, 'day');    //: -> NUEVA FECHA
                if (!nuevoInicio.isAfter(momentInicio) || !nuevoInicio.isBefore(momentFin) || !nuevoFin.isBefore(nuevoF)) {
                    $('#fueraRango_ed').show();
                    estado = false;
                }
            } else {
                if (!horaI.isAfter(momentInicio) || !horaI.isBefore(momentFin) || !horaF.isBefore(momentFin)) {
                    $('#fueraRango_ed').show();
                    estado = false;
                } else {
                    if (horaF.isBefore(horaI)) {
                        $('#errorenPausas_ed').show();
                        estado = false;
                    }
                }
            }
        }
    });
    return estado;
}
// : FUNCION DE VALIDAR HORAS ENTRE PAUSAS
function e_validarHorasEntrePausas() {
    var estado = true;
    $('.e_rowInputs').each(function () {
        var idI = $(this).val();
        if ($('#e_descPausa' + idI).val() != "" && $('#e_InicioPausa' + idI).val() != "" && $('#e_FinPausa' + idI).val() != "") {
            // *************************************************** TIEMPOS DE INPUTS ********************************************
            // : INICIO DE PAUSA MENOS LA TOLERANCIA DE INICIO PARA ENCONTRAR EL MINIMO DE INICIO
            var resultadoResta = sustraerMinutosHoras($('#e_InicioPausa' + idI).val(), parseInt($('#e_toleranciaIP' + idI).val()));
            var horaI = moment(resultadoResta, ["HH:mm"]);
            // : FIN DE PAUSA MAS LA TOLERANCIA DE FIN PARA ENCONTRAR EL MAXIMO DE FIN
            var resultadoSuma = sumarMinutosHoras($('#e_FinPausa' + idI).val(), parseInt($('#e_ToleranciaFP' + idI).val()));
            var horaF = moment(resultadoSuma, ["HH:mm"]);
            // *************************************************** TIEMPOS DE HORARIO *******************************************
            // : INICIO DE HORARIO MENOS LA TOLERANCIA DE INICIO PARA ENCONTRAR EL MINIMO DE INICIO DE HORARIO
            var resultadoRestaHorario = sustraerMinutosHoras($('#horaI_ed').val(), parseInt($('#toleranciaH_ed').val()));
            var momentInicio = moment(resultadoRestaHorario, ["HH:mm"]);
            // : FIN DE HORARIO MAS LA TOLERANCIA DE FIN PARA ENCONTRAR EL MAXIMO DE FIN DE HORARIO
            var resultadoSumaHorario = sumarMinutosHoras($('#horaF_ed').val(), parseInt($('#toleranciaSalida_ed').val()));
            var momentFin = moment(resultadoSumaHorario, ["HH:mm"]);
            // * VALIDACION ENTRE PAUSAS
            $('.e_rowInputs').each(function () {
                var idC = $(this).val();
                if (idI != idC) {
                    if ($('#e_descPausa' + idC).val() != "" && $('#e_InicioPausa' + idC).val() != "" && $('#e_FinPausa' + idC).val() != "") {
                        // : INICIO DE PAUSA MENOS LA TOLERANCIA DE INICIO PARA ENCONTRAR EL MINIMO DE INICIO
                        var resultadoRestaNuevo = sustraerMinutosHoras($('#e_InicioPausa' + idC).val(), parseInt($('#e_toleranciaIP' + idC).val()));
                        var horaCompararI = moment(resultadoRestaNuevo, ["HH:mm"]);
                        // : FIN DE PAUSA MAS LA TOLERANCIA DE FIN PARA ENCONTRAR EL MAXIMO DE FIN
                        var resultadoSumaNuevo = sumarMinutosHoras($('#e_FinPausa' + idC).val(), parseInt($('#e_ToleranciaFP' + idC).val()));
                        var horaCompararF = moment(resultadoSumaNuevo, ["HH:mm"]);
                        if (momentFin.isSameOrBefore(momentInicio)) {   //: -> <=
                            // ! NUEVA FECHA DE HORA INICIO DE PAUSA
                            var nuevoInicioC;
                            if (horaCompararI.isBefore(momentInicio)) {        //: -> <
                                nuevoInicioC = horaCompararI.add(1, 'day');    //: -> NUEVA FECHA
                            } else {
                                nuevoInicioC = horaCompararI;
                            }
                            // ! NUEVA FECHA DE HORA FIN
                            var nuevoFinC;
                            if (horaCompararF.isBefore(momentInicio)) {
                                nuevoFinC = horaCompararF.add(1, 'day');    //: -> NUEVA FECHA
                            } else {
                                nuevoFinC = horaCompararF;
                            }
                            // ! NUEVA FECHA DE HORA INICIO DE PAUSA
                            var nuevoInicio;
                            if (horaI.isBefore(momentInicio)) {        //: -> <
                                nuevoInicio = horaI.add(1, 'day');    //: -> NUEVA FECHA
                            } else {
                                nuevoInicio = horaI;
                            }
                            // ! NUEVA FECHA DE HORA FIN
                            var nuevoFin;
                            if (horaF.isBefore(momentInicio)) {
                                nuevoFin = horaF.add(1, 'day');    //: -> NUEVA FECHA
                            } else {
                                nuevoFin = horaF;
                            }
                            if (nuevoInicioC.isAfter(nuevoInicio) && nuevoInicioC.isBefore(nuevoFin)) {
                                $('#errorenPausasCruzadas_ed').show();
                                estado = false;
                            } else {
                                if (horaF.isBefore(horaI)) {
                                    $('#errorenPausas_ed').show();
                                    estado = false;
                                }
                            }
                        } else {
                            if (horaCompararI.isSameOrAfter(horaI) && horaCompararI.isSameOrBefore(horaF)) {
                                $('#errorenPausasCruzadas_ed').show();
                                estado = false;
                            } else {
                                if (horaF.isBefore(horaI)) {
                                    $('#errorenPausas_ed').show();
                                    estado = false;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
    return estado;
}
// : VALIDAR INPUTS
async function e_validarInputs() {
    var estado = true;
    $('.e_rowInputs').each(function () {
        var id = $(this).val();
        if ($('#e_rowP' + id).is(":visible")) {
            if ($('#e_descPausa' + id).val() == "" || $('#e_InicioPausa' + id).val() == "" || $('#e_FinPausa' + id).val() == "") {
                $('#validP_ed').show();
                $('#btnEditarHorario').prop("disabled", true);
                if ($('#e_descPausa' + id).val() == "") {
                    $('#e_descPausa' + id).addClass("borderColor");   //: -> AGREGAR CLASE DE REFERENCIA
                }
                if ($('#e_InicioPausa' + id).val() == "") {
                    $('#e_InicioPausa' + id).addClass("borderColor");   //: -> AGREGAR CLASE DE REFERENCIA
                }
                if ($('#e_FinPausa' + id).val() == "") {
                    $('#e_FinPausa' + id).addClass("borderColor");   //: -> AGREGAR CLASE DE REFERENCIA
                }
                estado = false;
            }
        }
    });
    return estado;
}
// : FUNCION DE ELIMINAR CONTENIDO
function e_eliminarContenido(id) {
    $('#e_descPausa' + id).val("");
    $('#e_InicioPausa' + id).val("");
    $('#e_FinPausa' + id).val("");
    $('#e_toleranciaIP' + id).val("");
    $('#e_ToleranciaFP' + id).val("");
    $('#e_inactivarPausa' + id).prop("checked", false);
    $('#e_descontarPausa' + id).prop("checked", false);
    $('#e_rowP' + id).hide();
    $('#fueraRango_ed').hide();
    $('#errorenPausas_ed').hide();
    $('#errorenPausasCruzadas_ed').hide();
    var validacionPH = e_validarHorasPausaHorario();
    if (!validacionPH) {
        $('#btnEditarHorario').prop("disabled", true);
        return false;
    } else {
        var validacionEP = e_validarHorasEntrePausas();
        if (!validacionEP) {
            $('#btnEditarHorario').prop("disabled", true);
            return false;
        }
        $('#btnEditarHorario').prop("disabled", false);
        $('#fueraRango_ed').hide();
        $('#errorenPausas_ed').hide();
        $('#errorenPausasCruzadas_ed').hide();
    }
    var resp = true;
    var ultimoVisible = undefined;
    $('.e_rowInputs').each(function () {
        var id = $(this).val();
        if ($('#e_rowP' + id).is(":visible")) {
            resp = false;
            ultimoVisible = id;
        }
    });
    if (ultimoVisible != undefined) $('#e_agregar' + ultimoVisible).show();
    if (resp) {
        e_contenidoInput(e_cont);
    }
}
// : OBTENER PAUSAS DE HORARIO
function pausasHorario(id) {
    $.ajax({
        async: false,
        type: "POST",
        url: "/pausasHorario",
        data: { id: id },
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
            $('#PausasHorar_ed').empty();
            if (data.length != 0) {
                var contenido = "";
                for (let index = 0; index < data.length; index++) {
                    var pausa = data[index];
                    contenido +=
                        `<div class="row pb-3" id="e_rowP${pausa.idpausas_horario}" style="border-top:1px dashed #aaaaaa!important;">
                        <input type="hidden" class="e_rowInputs" value="${pausa.idpausas_horario}">
                        <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Descripción de pausa</label>
                                <input type="text"  class="form-control form-control-sm descP" id="e_descPausa${pausa.idpausas_horario}"
                                value="${pausa.pausH_descripcion}"onkeyup="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                            </div>
                        </div>
                        <div class="row pt-2">
                                <div class="col-md-2">
                                    <label>Inicio pausa(24h)</label>
                                    <input type="text" class="form-control form-control-sm inicioP" id="e_InicioPausa${pausa.idpausas_horario}" name="inicioP"
                                      value="${pausa.pausH_Inicio}"  onchange="javascript:$(this).removeClass('borderColor');">
                                </div>
                                <div class="col-md-2">
                                    <label>Tolerancia inicio</label>
                                    <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                        <input type="number"  class="form-control form-control-sm" id="e_toleranciaIP${pausa.idpausas_horario}" value="0"
                                           value="${pausa.tolerancia_inicio}" oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59; if( this.value == '') this.value = 0">
                                        <div class="input-group-prepend  ">
                                            <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                min.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Fin pausa(24h)</label>
                                    <input type="text"  class="form-control form-control-sm finP" id="e_FinPausa${pausa.idpausas_horario}" name="finP"
                                       value="${pausa.pausH_Fin}" onchange="javascript:$(this).removeClass('borderColor');">
                                </div>
                                <div class="col-md-2">
                                    <label>Tolerancia salida</label>
                                    <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                        <input type="number"  class="form-control form-control-sm" id="e_ToleranciaFP${pausa.idpausas_horario}" value="0"
                                           value="${pausa.tolerancia_fin}" oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59; if( this.value == '') this.value = 0">
                                        <div class="input-group-prepend  ">
                                            <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                min.
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                    if (pausa.inactivar == 0) {
                        contenido += `<div class="col-md-1">
                                        <label>Inactivar</label>
                                        <br>
                                        <input type="checkbox" id="e_inactivarPausa${pausa.idpausas_horario}" class="mt-2 ml-3">
                                    </div>`;
                    } else {
                        contenido += `<div class="col-md-1">
                                        <label>Inactivar</label>
                                        <br>
                                        <input type="checkbox" id="e_inactivarPausa${pausa.idpausas_horario}" class="mt-2 ml-3" checked>
                                    </div>`;
                    }
                    if (pausa.descontar == 0) {
                        contenido += `<div class="col-md-2 text-center">
                                        <label>Descontar aut.</label>
                                        <br>
                                        <input type="checkbox" id="e_descontarPausa${pausa.idpausas_horario}" class="mt-2">
                                    </div>`;
                    } else {
                        contenido += `<div class="col-md-2 text-center">
                                        <label>Descontar aut.</label>
                                        <br>
                                        <input type="checkbox" id="e_descontarPausa${pausa.idpausas_horario}" class="mt-2" checked>
                                    </div>`;
                    }
                    contenido += `<div class="col-md-1">
                                        <label>Eliminar</label>
                                        <br>
                                        <a style="cursor: pointer" onclick="javascript:e_eliminarContenido(${pausa.idpausas_horario})" class="ml-3">
                                            <img src="/admin/images/delete.svg" height="15">
                                        </a>
                                    </div>
                                </div>`;
                    if (index != (data.length - 1)) {
                        contenido += `<button class="btn btn-sm bt_plus" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;
                                        padding-top: 0px;padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px;
                                        margin-top: 5px;margin-left: 20px; display:none" onclick="javascript:e_contenidoInput(${pausa.idpausas_horario})" id="e_agregar${pausa.idpausas_horario}">
                                        +
                                    </button>`;
                    } else {
                        contenido += `<button class="btn btn-sm bt_plus" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;
                                        padding-top: 0px;padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px;
                                        margin-top: 5px;margin-left: 20px;" onclick="javascript:e_contenidoInput(${pausa.idpausas_horario})" id="e_agregar${pausa.idpausas_horario}">
                                        +
                                    </button>`;
                    }
                    contenido += `</div>
                                </div>`;
                }
                $('#PausasHorar_ed').append(contenido);
                data.forEach(element => {
                    e_inicializarHorasPausas(element.idpausas_horario);
                });
            } else {
                e_contenidoInput(e_cont);
            }
            $('#pausas_edit').show();
        },
        error: function () { }
    });
}
// : SWITCH DE PAUSA
$('#SwitchPausa_ed').on("change.bootstrapSwitch", function (event) {
    if (event.target.checked == true) {
        if ($('#horaF_ed').val() != "" || $('#horaI_ed').val() != "") {
            $('#vacioHoraF_ed').hide();
            $('#pausas_edit').show();
            $('#PausasHorar_ed').empty();
            var id = $('#idhorario_ed').val();
            pausasHorario(id);
            $('#btnEditarHorario').prop('disabled', false);
        } else {
            $('#pausas_edit').hide();
            $('#PausasHorar_ed').empty();
            $('#vacioHoraF_ed').show();
            $('#SwitchPausa_ed').prop("checked", false);
            $('#btnEditarHorario').prop('disabled', false);
        }
    } else {
        $('#btnEditarHorario').prop('disabled', false);
        $('#pausas_edit').hide();
        $('#PausasHorar_ed').empty();
        e_cont = 0;
    }
});
// : OBTENER PAUSAS
function e_obtenerPausas() {
    var resultado = [];
    $('.e_rowInputs').each(function () {
        var id = $(this).val();
        var descripcion = $('#e_descPausa' + id).val();
        var inicioPausa = $('#e_InicioPausa' + id).val();
        var toleranciaPI = $('#e_toleranciaIP' + id).val();
        var finPausa = $('#e_FinPausa' + id).val();
        var toleranciaPF = $('#e_ToleranciaFP' + id).val();
        var inactivarP;
        var descontarP;
        if ($('#e_inactivarPausa' + id).is(":checked")) {
            inactivarP = 1;
        } else {
            inactivarP = 0;
        }
        if ($('#e_descontarPausa' + id).is(":checked")) {
            descontarP = 1;
        } else {
            descontarP = 0;
        }
        var objPausa = {
            "id": $(this).val(),
            "descripcion": descripcion,
            "inicioPausa": inicioPausa,
            "toleranciaI": toleranciaPI,
            "finPausa": finPausa,
            "toleranciaF": toleranciaPF,
            "inactivar": inactivarP,
            "descontar": descontarP
        };
        resultado.push(objPausa);
    });

    return resultado;
}
// : GUARDAR CAMBIOS DE EDITAR
async function editarHorarioDatos() {
    var id = $('#idhorario_ed').val();
    var descripcion = $('#descripcionCa_ed').val();
    var horaInicio = $('#horaI_ed').val();
    var horaFin = $('#horaF_ed').val();
    var toleranciaI = $('#toleranciaH_ed').val();
    var toleranciaF = $('#toleranciaSalida_ed').val();
    var horasO = $('#horaOblig_ed').val();
    var idReglaHExtras=$('#idReglaHora_ed').val();
    var idReglaHExtrasNoc=$('#idReglaHoraNocturna_ed').val();

    //*Tiempos muertos
    var tmIngreso;
    var tmSalida;

    //tm ingreso
    if ($('#tmIngreso_ed').is(":checked")) {
        tmIngreso = 1;
    } else {
        tmIngreso = 0;
    }

    //tm salida
    if ($('#tmSalida_ed').is(":checked")) {
        tmSalida = 1;
    } else {
        tmSalida= 0;
    }
    var pausas = e_obtenerPausas();
    var validarInput = await e_validarInputs();
    if (!validarInput) {
        return false;
    } else {
        $('#validP').hide();
    }
    var validacionPH = e_validarHorasPausaHorario();
    var validacionEP = e_validarHorasEntrePausas();
    if (!validacionPH) {
        return false;
    } else {
        if (!validacionEP) {
            return false;
        }
    }
    $.ajax({
        async: false,
        type: "POST",
        url: "/editarHorario",
        data: {
            id: id,
            descripcion: descripcion,
            toleranciaI: toleranciaI,
            toleranciaF: toleranciaF,
            horaInicio: horaInicio,
            horaFin: horaFin,
            horasO: horasO,
            pausas: pausas,
            tmIngreso,
            tmSalida,
            idReglaHExtras,
            idReglaHExtrasNoc
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
            $('#horarioEditar').modal('toggle');
            $('#tablaEmpleado').DataTable().ajax.reload(null, false);
        },
        error: function () { }
    });
}
// ! ******************************* FINALIZACION ****************************************
// ! ******************************* SELECT DE HORARIO ****************************************
function obtenerHorarios() {
    $.ajax({
        async: false,
        type: "GET",
        url: "/obtenerHorarios",
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
            $('#selectHorario').empty();
            var option = `<option select="selected" disabled> Asignar horario</option>`;
            for (let index = 0; index < data.length; index++) {
                option += `<option value="${data[index].horario_id}">${data[index].horario_descripcion} (${data[index].horaI} - ${data[index].horaF})</option>`;
            }
            $('#selectHorario').append(option);
        },
        error: function () { }
    });
}
// ! ******************************* FINALIZACION ****************************************
/* EVENTO CAMBIAR SWITCH EN ACTUALLIZAR CONFIG HORARIO A DIA REGISTRAR EMPLEADO  */
$(function () {
    $(document).on('change', '#horAdicSwitch_Actualizar_re', function (event) {
        if ($('#horAdicSwitch_Actualizar_re').prop('checked')) {
            $('#nHorasAdic_Actualizar_re').show();

        } else {
            $('#nHorasAdic_Actualizar_re').hide();

        }

    });
});
/* ------------------------------------------------------------------ */

/* ---------ACTUALIZAR CONFIGURACION DE HORARIO------------- */
function actualizarConfigHorario_re() {
    let idHoraEmp = $('#idHoraEmpleado_re').val();
    let fueraHorario;
    let permiteHadicional;
    let nHorasAdic;

    //* Fuera de horario
    if ($("#fueraHSwitch_Actualizar_re").is(":checked")) {
        fueraHorario = 1;
    } else {
        fueraHorario = 0;
    }

    //* permitir horas adicionales
    if ($("#horAdicSwitch_Actualizar_re").is(":checked")) {
        permiteHadicional = 1;
        nHorasAdic = $('#nHorasAdic_Actualizar_re').val()
    } else {
        permiteHadicional = 0;
        nHorasAdic = null;
    }
    let tipHorarioC=$('#tipoHorario').val();
    $.ajax({
        type: "post",
        url: "/horario/actualizarConfigHorario",
        data: {
            idHoraEmp, fueraHorario, permiteHadicional, nHorasAdic,tipHorarioC
        },
        statusCode: {
            419: function () {
                location.reload();
            },
        },
        headers: {
            "X-CSRF-TOKEN": $(
                'meta[name="csrf-token"]'
            ).attr("content"),
        },
        success: function (data) {
            calendar.refetchEvents();
            $('#editarConfigHorario_re').modal('hide');
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
}
/* ---------------------------------------------------------------------------- */
//* select empleado cuando cambia
$( "#nombreEmpleado" ).change(function() {
    nempl=$( "#nombreEmpleado" ).val().length;

    if(nempl>0){

    $(".loader").show();
    $(".img-load").show();
    calendario();
    }
    else{
        $(".loader").hide();
        $(".img-load").hide();
        calendario();
    }

  });
//*

/* DATOS DE HORARIOS EN CALENDARIO */
function verDatosHorario(idempleado,idHorarioEmp){
    $('#dataHorarioElegido'+ idempleado).empty();
    $('#dataHorarioElegido'+ idempleado).css("background","#f3f3f3" );

    $('.mediaE'+ idempleado).css( "background","#fff" );
    var contenidoH= "";
    $.each(dataDeempleado, function (key, item) {
        //*empleado
        if(item.idempleado==idempleado){
            $.each(item.horario, function (key, horario) {

                //*datos de horario
                if(horario.idHorarioEmp==idHorarioEmp){
                    $('#media'+ idempleado+'EH'+idHorarioEmp).css( "background","#f3f3f3" );
                    contenidoH+=
                    `<div class='col-md-4'>
                      <div class='form-group'>
                       <span style="font-weight: 700;">Horario:</span>
                       <span>` + horario.horario_descripcion+`</span>
                      </div>
                    </div>`;
                    contenidoH+=
                    `<div class='col-md-4'>
                      <div class='form-group'>
                       <span style="font-weight: 700;">Hora de inicio:</span>
                       <span>` + horario.horaI+`</span>
                      </div>
                    </div>`;
                    contenidoH+=
                    `<div class='col-md-4'>
                      <div class='form-group'>
                       <span style="font-weight: 700;">Hora de fin:</span>
                       <span>` + horario.horaF+`</span>
                      </div>
                    </div>`;
                    contenidoH+=
                    `<div class='col-md-4'>
                      <div class='form-group'>
                       <span style="font-weight: 700;">Horas obligadas:</span>
                       <span>` + horario.horasObliga+`</span>
                      </div>
                    </div>`;
                    contenidoH+=
                    `<div class='col-md-4'>
                    <div class='form-group'>
                     <span style="font-weight: 700;">Tolerancia ingreso:</span>
                     <span>` + horario.toleranciaI+` minutos</span>
                    </div>
                  </div>`;
                  contenidoH+=
                  `<div class='col-md-4'>
                   <div class='form-group'>
                   <span style="font-weight: 700;">Tolerancia salida:</span>
                   <span>` + horario.toleranciaF+` minutos</span>
                   </div>
                  </div>`;
                  contenidoH+=
                  `<div class='col-md-4'>
                   <div class='form-group'>
                   <span style="font-weight: 700;">Trabaja fuera horario:</span>
                   <span>` + horario.fueraHorario+` </span>
                   </div>
                  </div>`;
                  contenidoH+=
                  `<div class='col-md-4'>
                   <div class='form-group'>
                   <span style="font-weight: 700;">Horas adicionales:</span>
                   <span>` + horario.horaAdicional+` hora(s)</span>
                   </div>
                  </div>`;

                    $('#dataHorarioElegido'+ idempleado).append(contenidoH);
                }

            });
        }
    });

}
//**ELIMINAR HORARIOS EMPLEADOS MASIVOS */
function eliminarMasivoHorarios(){
    bootbox.confirm({
        title: "Elminar horario",
        message: "¿Estás seguro que desea borrar los horarios seleccionados?",
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

                let valoresCheck = [];
                let empleados=$('#nombreEmpleado').val();
                let diadeHorario=$('#fechaSelectora').val();
                $(".chechHoraEmp:checked").each(function(){
                    valoresCheck.push($(this).attr('data-id'));
                });
                if(valoresCheck.length<1){
                    alert('seleccione horarios');
                    return false;
                }
                $.ajax({
                    type: "post",
                    url: "/elimarhoraEmps",
                    data:{
                        valoresCheck,empleados,diadeHorario
                    },
                    async:false,
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
                        if(data!=0){

                            /* ACTUALIZO LOS DATO DE MODAL */
                            datosModalHorarioEmpleado(diadeHorario,empleados);
                            $.notifyClose();
                        $.notify(
                            {
                                message: "\nHorarios borrados",
                                icon: "admin/images/checked.svg",
                            },
                            {   element: $('#modalHorarioEmpleados'),
                                position: "fixed",
                                icon_type: "image",
                                newest_on_top: true,
                                delay: 5000,
                                template:
                                    '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 50,
                            }
                        );
                            /* ............................. */

                        } else{

                            $('#modalHorarioEmpleados').modal('hide');
                            calendar.refetchEvents();
                            $.notifyClose();
                        $.notify(
                            {
                                message: "\nHorarios borrados",
                                icon: "admin/images/checked.svg",
                            },
                            {   element: $('#asignarHorario'),
                                position: "fixed",
                                icon_type: "image",
                                newest_on_top: true,
                                delay: 5000,
                                template:
                                    '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 50,
                            }
                        );
                        }

                    },
                    error: function (data) {
                          console.log('Ocurrio un error');
                    }
                });


            }
        }
    });



}
//*FUNCION PARA CREAR LOS DATOS DEL MODAL DE BOTON ASIGNADO PARA VER
//*EMPLEADOS Y HORARIOS
function datosModalHorarioEmpleado(diadeHorario,empleados){
    $.ajax({
        type: "post",
        url: "/datosHorarioEmpleado",
        data: {
            diadeHorario,empleados
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
            $("#rowdivs").empty();
            dataDeempleado=data;
            var contenido= "";

            //*boton para elimnar seleecionados
            contenido+=`<div class="col-md-12 row mb-2">

            <div class="col-md-6">
            <div class="form-check" style="padding-bottom: 10px;">
            <input type="checkbox" class="form-check-input" id="checkselectElim">
            <label class="form-check-label" for="checkselectElim"
                style="margin-top: 2px;font-weight: 600">Seleccionar todos</label>
            </div>
            </div>
            <div class="col-md-6 text-right">
                <button onclick="eliminarMasivoHorarios()" type="button" class="btn btn-soft-danger btn-sm"><i
                        class="uil uil-trash-alt mr-1"></i>Borrar seleccionados</button>
             </div>
             <div class="col-md-12">
             <label
             style="font-weight: 600">Total empleados: ` + data.length +` </label>
             </div>
             <div class="col-md-12 row">
             <div class="col-md-3" style="padding-top: 4px;">
             <label
             style="font-weight: 600">Buscar empleado: </label> </div>
             <div class="col-md-5">
             <input id="buscadorHorario" type="text" value="" onkeyup="buscadorEmpleadoH()" class="form-control form-control-sm"></div>
             </div>
             </div>`;

            $.each(data, function (key, item) {

            //*NOMBRE DE EMPLEADO
            contenido+=` <div class='col-md-12 row itemHorario'><div class='col-md-12 ' style="border-top: 1px dashed #aaaaaa!important;">
            <h5 class='header-title nombres' style='font-size: 13.4px;'>` + item.apellidos +` `+item.nombre+`</h5>
            <h5 class='header-title' style='font-size: 13px'>Horarios:</h5>
            </div>`;

            //*HORARIOS
            $.each(item.horario, function (key, horario) {
                contenido+=
                `<div class="col-md-6 mb-3" >
                <div class="row">
                <input type="checkbox" style="margin-top: 7px;" data-id="` + horario.idHorarioEmp+`" class="chechHoraEmp col-md-2" id="checkEliminarhoE` + horario.idHorarioEmp+`" >
                <div class="col-md-10 media mediaE` + item.idempleado+`" style="border:2px solid #e6e6e6;" id="media` + item.idempleado+`EH` + horario.idHorarioEmp+`">
                    <div class="media-body">
                    <h6 class="mt-1 mb-0 font-size-14"  style="
                    padding-bottom: 5px;">` + horario.horario_descripcion+`</h6>
                    </div>
                    <div class="dropdown align-self-center float-right">
                    <a  onclick="verDatosHorario(` + item.idempleado+`,` + horario.idHorarioEmp+`)"

                        class=""

                    >
                        <i class="uil uil-eye"></i>
                    </a>

                    </div>
                </div>
                </div>
                </div>
                `;

            });

            contenido+=
            `<div class="col-md-12 row" style="    margin-left: 0px;padding-top: 5px;
            padding-left: 0px;" id="dataHorarioElegido` + item.idempleado+`"></div></div>`;
            });

            $("#rowdivs").append(contenido);

            $("#fechaSelectora").val(diadeHorario);
            $("#modalidsEmpleado").val(empleados);

            $('#modalHorarioEmpleados').modal('show');


        },
        error: function (data) {
              console.log('Ocurrio un error');
        }


    });
}
//*CHECK SELECCIONAR TODOS LOS HORARIOS PARA BORRAR
$(function () {
    $(document).on('change', '#checkselectElim', function (event) {
        if ($('#checkselectElim').prop('checked')) {
            $('#nHorasAdic_Actualizar_re').show();
            $(".chechHoraEmp").prop("checked",true);
        } else {
            $(".chechHoraEmp").prop("checked",false);

        }

    });
});

//******FUNCION CLONAR HORARIOS******* */
function ClonarHorarios(){
    //*validar que al menos tenga un empleado seelect
    let idempSelect = $('#nombreEmpleado').val();
            if (idempSelect == '') {
                /* calendar.unselect(); */
                bootbox.alert({
                    message: "Seleccione empleado",

                });

                return false;
            }
    //***************************************** */  */
    $('#divClonacionElegir').hide();
    $('#alertReemplazar').hide();
    $('#modalHorarioClonar').modal('show');
}
//************************************* */
   //*INICIO Y FIN DE MES
   var inicioC=  moment().startOf('month').format('YYYY-MM-DD');
   var finC=moment().format('YYYY-MM-DD');

   $('#ID_START').val(inicioC);
   $('#ID_END').val(finC);
    //*

//* ELEGIR FECHA PARA CLOBAR HORARIOS */
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "j F",
    locale: "es",
    wrap: true,
    allowInput: true,
    conjunction: " a ",
    minRange: 1,

    onChange: function (selectedDates) {
        var _this = this;
        var dateArr = selectedDates.map(function (date) { return _this.formatDate(date, 'Y-m-d'); });
        $('#ID_START').val(dateArr[0]);
        $('#ID_END').val(dateArr[1]);



    },
    defaultDate: [inicioC,finC],
    onClose: function (selectedDates, dateStr, instance) {
        if (selectedDates.length == 1) {
            var fm = moment(selectedDates[0]).add("day", -1).format("YYYY-MM-DD");
            instance.setDate([fm, selectedDates[0]], true);
        }
    }
});

//*VALIDAR CHECK EN FORMULARIO CLONAR

//*asignar
$("#asignarNuevoHorarioC").change(function (event) {
    if ($("#asignarNuevoHorarioC").prop("checked")) {
        $("#reemplazarNuevoHorarioC").prop("checked", false);
        $("#alertReemplazar").hide();
    }
});

//*reemplazar
$("#reemplazarNuevoHorarioC").change(function (event) {
    if ($("#reemplazarNuevoHorarioC").prop("checked")) {
        $("#asignarNuevoHorarioC").prop("checked", false);
        $("#alertReemplazar").show();
    } else{
        $("#alertReemplazar").hide();
    }

});
//********************************** */

//* **********************FUNCION PARA REGISTRAR CLONACION DE HORARIOS****************************** */
function registrarClonacionH(){

 //**VALIDAR QUE AL MENOS ESTE CHECK UNA OPCION */
 if (!$("#asignarNuevoHorarioC").is(":checked") && !$("#reemplazarNuevoHorarioC").is(":checked") ){
     $('#divClonacionElegir').show();
     return false;
 } else{
    $('#divClonacionElegir').hide();
 }
 $('#modalHorarioClonar').modal('hide');
 //*MOSTRAR ESPERA
 $(".loader").show();
 $(".img-load").show();

 //*RECOGER DATOS
 let empleadosaClonar=$('#nombreEmpleado').val();
 let empleadoCLonar= $('#nombreEmpleadoClonar').val();
 let diaI=$("#ID_START").val();
 let diaF=$("#ID_END").val();
 let asigNuevo;
 let reempExistente;

 if ($("#asignarNuevoHorarioC").is(":checked")) {
    asigNuevo=1;
 } else{
    asigNuevo=0;
 }

 if ($("#reemplazarNuevoHorarioC").is(":checked")) {
    reempExistente=1;
 } else{
    reempExistente=0;
 }

  //*MANDAR AJAX
  $.ajax({
    type: "post",
    url: "/clonarHorarios",
    data: {
        empleadosaClonar,
        empleadoCLonar,diaI,diaF,
        asigNuevo,reempExistente
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

       //*SI SE ASIGNA NUEVO SE SUMA LOS HORARIOS
       if(asigNuevo==1){

        //*SE ENCONTRARON CRUCES, ASI QUE NO SE REGISTRA HORARIOS HASTA QUE CONFIRME SI REEMPLAZA
        if(data==0){

            $('#modalHorarioClonar').modal('show');
            bootbox.confirm({
                title: "Cruce de horarios",
                message: "¿Algunos horarios se cruzan con los horarios existentes, desea reemplazarlos ?",
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
                        $('#modalHorarioClonar').modal('hide');
                        //*MOSTRAR ESPERA
                        $(".loader").show();
                        $(".img-load").show();
                        $.ajax({
                            type: "post",
                            url: "/reemplazarHorariosClonacion",
                            data: {
                                empleadosaClonar,
                                empleadoCLonar,diaI,diaF,
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
                               $('#modalHorarioClonar').modal('hide');
                               $(".loader").hide();
                               $(".img-load").hide();
                                $.notifyClose();
                                $.notify(
                                    {
                                        message: "\nCambios guardados.",
                                        icon: "admin/images/checked.svg",
                                    },
                                    {   element: $('#asignarHorario'),
                                        position: "fixed",
                                        icon_type: "image",
                                        newest_on_top: true,
                                        delay: 5000,
                                        template:
                                            '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                            '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                            '<span data-notify="title">{1}</span> ' +
                                            '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                            "</div>",
                                        spacing: 50,
                                    }
                                );


                            },
                            error: function (data) {
                                  console.log('Ocurrio un error');
                            }


                        });
                    }
                    else{
                        $('#modalHorarioClonar').modal('show');
                    }
                }
            });
        } else{
            $.notifyClose();
           $.notify(
               {
                   message: "\nCambios guardados.",
                   icon: "admin/images/checked.svg",
               },
               {   element: $('#asignarHorario'),
                   position: "fixed",
                   icon_type: "image",
                   newest_on_top: true,
                   delay: 5000,
                   template:
                       '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                       '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                       '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                       '<span data-notify="title">{1}</span> ' +
                       '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                       "</div>",
                   spacing: 50,
               }
           );

        }
       } else{
           //* devuelve 1 en erreemplazar
           //*SE REEMPLAZA LOS HORARIOS
           $.notifyClose();
           $.notify(
               {
                   message: "\nCambios guardados.",
                   icon: "admin/images/checked.svg",
               },
               {   element: $('#asignarHorario'),
                   position: "fixed",
                   icon_type: "image",
                   newest_on_top: true,
                   delay: 5000,
                   template:
                       '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                       '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                       '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                       '<span data-notify="title">{1}</span> ' +
                       '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                       "</div>",
                   spacing: 50,
               }
           );


       }
       $(".loader").hide();
       $(".img-load").hide();
    },
    error: function (data) {
          console.log('Ocurrio un error');
    }


});

}
//* ******************************************FIN DE FUNCION ****************************************** */

//*CONFIGURACION SELECT INCIDENCIA
$(function () {
$("#incidenciaSelect").select2({
    language: {
        loadingMore: function () {
            return "Cargando más resultados…";
        },
        noResults: function () {
            return "No se encontraron incidencias";
        },
    },
});
});

//***SELECT TIPO DE INCIDENCIA */

$('#selectTipoIn').on('select2:select', function (e) {

    //*habilitamos incidencia
    $("#incidenciaSelect").prop("disabled", false);

    $("#incidenciaSelect").empty();

    let tipoInci=$('#selectTipoIn').val();
    //llenamos select de incidencia
         $.ajax({
            type: "post",
            async:false,
            url: "/horario/Incidenciasxtipo",
            data: {
                tipoInci
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
                var optionIncid = `<option value="" ></option>`;
                $.each(data, function (index, element) {
                    optionIncid += `<option value="${element.inciden_id}">${element.inciden_descripcion}</option>`;

                });
                $("#incidenciaSelect").append(optionIncid);


            },
            error: function (data) {
                  console.log('Ocurrio un error');
            }
        });









})


//* -------------------------------------INCIDENCIAS---------------------------- */
//*FUNCION PARA CREAR LOS DATOS DEL MODAL DE BOTON ASIGNADO PARA VER
//*EMPLEADOS E INCIDENCIAS
function datosModalIncidenciaEmpleado(diadeIncidencia,empleados){
    $.ajax({
        type: "post",
        url: "/datosIncidenciaEmpleado",
        data: {
            diadeIncidencia,empleados
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
            $("#rowdivsIncid").empty();
            dataDeempleadoInc=data;
            var contenido= "";

            //*boton para elimnar seleecionados
            contenido+=`<div class="col-md-12 row mb-2">

            <div class="col-md-6">
            <div class="form-check" style="padding-bottom: 10px;">
            <input type="checkbox" class="form-check-input" id="checkselectElimIncid">
            <label class="form-check-label" for="checkselectElimIncid"
                style="margin-top: 2px;font-weight: 600">Seleccionar todos</label>
            </div>
            </div>
            <div class="col-md-6 text-right">
                <button onclick="eliminarMasivoIncidencias()" type="button" class="btn btn-soft-danger btn-sm"><i
                        class="uil uil-trash-alt mr-1"></i>Borrar seleccionados</button>
             </div>
             <div class="col-md-12">
             <label
             style="font-weight: 600">Total empleados: ` + data.length +` </label>
             </div>
             <div class="col-md-12 row">
             <div class="col-md-3" style="padding-top: 4px;">
             <label
             style="font-weight: 600">Buscar empleado: </label> </div>
             <div class="col-md-5">
             <input id="buscadorIncidencia" type="text" value="" onkeyup="buscadorEmpleadoI()" class="form-control form-control-sm"></div>
             </div>
                      </div>`;

            $.each(data, function (key, item) {

            //*NOMBRE DE EMPLEADO
            contenido+=`<div class='col-md-12 row itemIncidencia'><div class='col-md-12' style="border-top: 1px dashed #aaaaaa!important;">
            <h5 class='header-title nombresInc' style='font-size: 13.4px;'>` + item.apellidos +` `+item.nombre+`</h5>
            <h5 class='header-title' style='font-size: 13px'>Incidencias:</h5>
            </div>`;

            //*INCIDENCIAS
            $.each(item.incidencias, function (key, incidencia) {
                contenido+=
                `<div class="col-md-6 mb-3" >
                <div class="row">
                <input type="checkbox" style="margin-top: 7px;" data-id="` + incidencia.idIncidencia+`" class="chechInciEmp col-md-2" id="checkEliminarInciE` + incidencia.idIncidencia+`" >
                <div class="col-md-10 media mediaEInc` + item.idempleado+`" style="border:2px solid #e6e6e6;" id="media` + item.idempleado+`EINC` + incidencia.idIncidencia+`">
                    <div class="media-body">
                    <h6 class="mt-1 mb-0 font-size-14"  style="
                    padding-bottom: 5px;">` + incidencia.title+`</h6>
                    </div>
                    <div class="dropdown align-self-center float-right">
                    <a  onclick="verDatosIncidencia(` + item.idempleado+`,` + incidencia.idIncidencia+`)"

                        class=""

                    >
                        <i class="uil uil-eye"></i>
                    </a>

                    </div>
                </div>
                </div>
                </div>
                `;

            });
            contenido+=
            `<div class="col-md-12 row" style="    margin-left: 0px;padding-top: 5px;
            padding-left: 0px;" id="dataInciElegido` + item.idempleado+`"></div></div>`;
            });
            $("#rowdivsIncid").append(contenido);

            $("#fechaSelectoraIncid").val(diadeIncidencia);
            $("#modalidsEmpleadoIncid").val(empleados);

            $('#modalIncidenciasEmpleados').modal('show');
        },
        error: function (data) {
              console.log('Ocurrio un error');
        }


    });
}
/* DATOS DE INCIDENCIA EN MODAL */
function verDatosIncidencia(idempleado,idIncidencia){
    $('#dataInciElegido'+ idempleado).empty();
    $('#dataInciElegido'+ idempleado).css("background","#f3f3f3" );

    $('.mediaEInc'+ idempleado).css( "background","#fff" );
    var contenidoH= "";
    $.each(dataDeempleadoInc, function (key, item) {
        //*empleado
        if(item.idempleado==idempleado){
            $.each(item.incidencias, function (key, incidencia) {

                //*datos de horario
                if(incidencia.idIncidencia==idIncidencia){
                    $('#media'+ idempleado+'EINC'+idIncidencia).css( "background","#f3f3f3" );
                    contenidoH+=
                    `<div class='col-md-4'>
                      <div class='form-group'>
                       <span style="font-weight: 700;">Incidencia:</span>
                       <span>` + incidencia.title+`</span>
                      </div>
                    </div>`;
                    if(incidencia.tipoInc_descripcion=='De sistema'){
                        contenidoH+=
                        `<div class='col-md-4'>
                        <div class='form-group'>
                        <span style="font-weight: 700;">Tipo de incidencia:</span>
                        <span>Incidencia</span>
                        </div>
                        </div>`;

                    } else{
                        contenidoH+=
                        `<div class='col-md-4'>
                        <div class='form-group'>
                        <span style="font-weight: 700;">Tipo de incidencia:</span>
                        <span>` + incidencia.tipoInc_descripcion+`</span>
                        </div>
                        </div>`;
                    }

                    if(incidencia.inciden_codigo!=null){
                        contenidoH+=
                        `<div class='col-md-4'>
                          <div class='form-group'>
                           <span style="font-weight: 700;">Codigo:</span>
                           <span>` + incidencia.inciden_codigo+`</span>
                          </div>
                        </div>`;
                    } else{
                        contenidoH+=
                        `<div class='col-md-4'>
                          <div class='form-group'>
                           <span style="font-weight: 700;">Codigo:</span>
                           <span>--</span>
                          </div>
                        </div>`;
                    }

                    if(incidencia.pagado==1){
                        contenidoH+=
                        `<div class='col-md-4'>
                          <div class='form-group'>
                           <span style="font-weight: 700;">Pagado:</span>
                           <span>SI</span>
                          </div>
                        </div>`;
                    } else{
                        contenidoH+=
                        `<div class='col-md-4'>
                          <div class='form-group'>
                           <span style="font-weight: 700;">Pagado:</span>
                           <span>NO</span>
                          </div>
                        </div>`;
                    }



                    $('#dataInciElegido'+ idempleado).append(contenidoH);
                }

            });
        }
    });

}

//**ELIMINAR INCIDENCIAS EMPLEADOS MASIVOS */
function eliminarMasivoIncidencias(){
    bootbox.confirm({
        title: "Elminar incidencia",
        message: "¿Estás seguro que desea borrar las incidencia seleccionadas?",
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

                let valoresCheck = [];
                let empleados=$('#nombreEmpleado').val();
                let diadeIncidencia=$('#fechaSelectoraIncid').val();
                $(".chechInciEmp:checked").each(function(){
                    valoresCheck.push($(this).attr('data-id'));
                });
                if(valoresCheck.length<1){
                    alert('seleccione incidencias');
                    return false;
                }
                $.ajax({
                    type: "post",
                    url: "/elimarIncidiEmps",
                    data:{
                        valoresCheck,empleados,diadeIncidencia
                    },
                    async:false,
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
                        if(data!=0){

                            /* ACTUALIZO LOS DATO DE MODAL */
                            datosModalIncidenciaEmpleado(diadeIncidencia,empleados);
                            $.notifyClose();
                        $.notify(
                            {
                                message: "\nIncidencias borradas",
                                icon: "admin/images/checked.svg",
                            },
                            {   element: $('#modalHorarioEmpleados'),
                                position: "fixed",
                                icon_type: "image",
                                newest_on_top: true,
                                delay: 5000,
                                template:
                                    '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 50,
                            }
                        );
                            /* ............................. */

                        } else{
                            
                            $('#modalIncidenciasEmpleados').modal('hide');
                            calendar.refetchEvents();
                            $.notifyClose();
                        $.notify(
                            {
                                message: "\nIncidencias borradas",
                                icon: "admin/images/checked.svg",
                            },
                            {   element: $('#asignarHorario'),
                                position: "fixed",
                                icon_type: "image",
                                newest_on_top: true,
                                delay: 5000,
                                template:
                                    '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 50,
                            }
                        );
                        }

                    },
                    error: function (data) {
                          console.log('Ocurrio un error');
                    }
                });


            }
        }
    });



}

//*CHECK SELECCIONAR TODOS LAS INCIDENCIAS PARA BORRAR
$(function () {
    $(document).on('change', '#checkselectElimIncid', function (event) {
        if ($('#checkselectElimIncid').prop('checked')) {

            $(".chechInciEmp").prop("checked",true);
        } else {
            $(".chechInciEmp").prop("checked",false);

        }

    });


});

//* BUSCADOR DE EMPLEADO EN MODAL HORARIOS
function buscadorEmpleadoH(){
    var nombres = $('.nombres');

    var buscando = $('#buscadorHorario').val();
    var item='';
    for( var i = 0; i < nombres.length; i++ ){
        item = $(nombres[i]).html().toLowerCase();
         for(var x = 0; x < item.length; x++ ){
             if( buscando.length == 0 || item.indexOf( buscando ) > -1 ){
                 $(nombres[i]).parents('.itemHorario').show();
             }else{
                  $(nombres[i]).parents('.itemHorario').hide();
             }
         }
    }
}

//* BUSCADOR DE EMPLEADO EN MODAL INCIDENCIAS
function buscadorEmpleadoI(){
    var nombres = $('.nombresInc');

    var buscando = $('#buscadorIncidencia').val();
    var item='';
    for( var i = 0; i < nombres.length; i++ ){
        item = $(nombres[i]).html().toLowerCase();
         for(var x = 0; x < item.length; x++ ){
             if( buscando.length == 0 || item.indexOf( buscando ) > -1 ){
                 $(nombres[i]).parents('.itemIncidencia').show();
             }else{
                  $(nombres[i]).parents('.itemIncidencia').hide();
             }
         }
    }
}
