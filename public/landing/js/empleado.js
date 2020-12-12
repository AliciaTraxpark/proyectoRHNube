
$("#horaI").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
});
$("#horaF").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
});

$("#horaI_ed").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
});
$("#horaF_ed").flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
});
$(document).ready(function () {
    $(".flatpickr-input[readonly]").on("focus", function () {
        $(this).blur();
    });
    $(".flatpickr-input[readonly]").prop("readonly", false);

});

function calendarioInv() {
    var calendarElInv = document.getElementById("calendarInv");
    calendarElInv.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: "es",
        defaultDate: fecha,
        height: 360,
        fixedWeekCount: false,
        plugins: ["dayGrid", "interaction", "timeGrid"],

        selectable: false,
        selectMirror: true,

        editable: false,
        eventLimit: true,
        header: {
            left: "prev,next today",
            center: "title",
            right: "",
        },
    };
    var calendarInv = new FullCalendar.Calendar(
        calendarElInv,
        configuracionCalendario
    );
    calendarInv.setOption("locale", "Es");

    calendarInv.render();
}
document.addEventListener("DOMContentLoaded", calendarioInv);

function calendario() {
    var calendarEl = document.getElementById("calendar");
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: "es",
        /* defaultDate: fecha, */
        height: 400,
        fixedWeekCount: false,
        plugins: ["dayGrid", "interaction", "timeGrid"],

        selectable: true,
        selectMirror: true,
        select: function (arg) {
            $("#pruebaEnd").val(moment(arg.end).format("YYYY-MM-DD HH:mm:ss"));
            $("#pruebaStar").val(
                moment(arg.start).format("YYYY-MM-DD HH:mm:ss")
            );

            $("#calendarioAsignar").modal("show");
        },
        eventClick: function (info) {
            id = info.event.id;

            var event = calendar.getEventById(id);

            bootbox.confirm({
                title: "Eliminar evento del calendario",
                message:
                    "¿Desea eliminar: " + info.event.title + " del calendario?",
                buttons: {
                    confirm: {
                        label: "Aceptar",
                        className: "btn-success",
                    },
                    cancel: {
                        label: "Cancelar",
                        className: "btn-light",
                    },
                },
                callback: function (result) {
                    if (result == true) {
                        $.ajax({
                            type: "post",
                            url: "/empleado/eliminarEte",
                            data: {
                                ideve: info.event.id,
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
                                info.event.remove();
                                calendar2.refetchEvents();
                            },
                            error: function (data) {
                                alert("Ocurrio un error");
                            },
                        });
                    }
                },
            });
        },
        editable: false,
        eventLimit: true,
        header: {
            left: "prev,next today",
            center: "title",
            right: "",
        },
        eventRender: function (info) {
            $('.tooltip').remove();
            if (info.event.extendedProps.horaI === null) {
                $(info.el).tooltip({ title: info.event.title });
            } else {
                if (info.event.borderColor == '#5369f8') {
                    if (info.event.extendedProps.horaAdic == 1) {
                        $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' + '     Marca horas adicionales' });
                    } else {
                        $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' });
                    }

                }
                else {
                    $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF });
                }
            }

        },
        events: function (info, successCallback, failureCallback) {
            var idcalendario = $("#selectCalendario").val();
            var datoscal;
            $.ajax({
                type: "POST",
                url: "/empleado/calendarioEmpTemp",
                data: {
                    idcalendario,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    },
                },
                success: function (data) {
                    successCallback(data);
                },
                error: function () { },
            });
        },

        /*  events: "calendario/show", */
    };
    calendar = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendar.setOption("locale", "Es");

    calendar.render();
}
document.addEventListener("DOMContentLoaded", calendario);
///calendario e n edit
function calendario_edit() {
    var calendarEl = document.getElementById("calendar_ed");
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: "es",
        defaultDate: fecha,
        height: 400,
        fixedWeekCount: false,
        plugins: ["dayGrid", "interaction", "timeGrid"],

        selectable: true,
        selectMirror: true,
        select: function (arg) {
            $("#pruebaEnd_ed").val(
                moment(arg.end).format("YYYY-MM-DD HH:mm:ss")
            );
            $("#pruebaStar_ed").val(
                moment(arg.start).format("YYYY-MM-DD HH:mm:ss")
            );

            $("#calendarioAsignar_ed").modal("show");
        },
        eventClick: function (info) {
            id = info.event.id;

            var event = calendarioedit.getEventById(id);
            if (
                info.event.textColor == "111111" ||
                info.event.textColor == "1" ||
                info.event.textColor == "0"
            ) {
                if (info.event.textColor == "111111") {
                    bootbox.alert({
                        title: "Info",
                        message: "Puede eliminar horarios en la pestaña Horarios",

                    })
                    /*  bootbox.confirm({
                         message:
                             "¿Desea eliminar: " +
                             info.event.title +
                             " del calendario?",
                         buttons: {
                             confirm: {
                                 label: "Aceptar",
                                 className: "btn-success",
                             },
                             cancel: {
                                 label: "Cancelar",
                                 className: "btn-light",
                             },
                         },
                         callback: function (result) {
                             if (result == true) {
                                 $.ajax({
                                     type: "post",
                                     url: "/empleado/eliminarHorariosEdit",
                                     data: {
                                         ideve: info.event.id,
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
                                         info.event.remove();
                                         calendar2_ed.refetchEvents();
                                     },
                                     error: function (data) {
                                         alert("Ocurrio un error");
                                     },
                                 });
                             }
                         },
                     }); */
                } else {
                    bootbox.confirm({
                        title: "Eliminar incidencia",
                        message:
                            "¿Desea eliminar: " +
                            info.event.title +
                            " del calendario?",
                        buttons: {
                            confirm: {
                                label: "Aceptar",
                                className: "btn-success",
                            },
                            cancel: {
                                label: "Cancelar",
                                className: "btn-light",
                            },
                        },
                        callback: function (result) {
                            if (result == true) {
                                $.ajax({
                                    type: "post",
                                    url: "/empleado/eliminarInciEdit",
                                    data: {
                                        ideve: info.event.id,
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
                                        info.event.remove();
                                        var a = moment(data.inciden_dias_fechaF);
                                        c = a._i;
                                        var b = moment(data.inciden_dias_fechaI);
                                        d = b._i;

                                        if (a.diff(b, 'days') > 1) {
                                            $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(a).subtract(1, 'day').format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffffff");
                                        }

                                        $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(data.inciden_dias_fechaI).format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffffff");
                                        calendar2_ed.refetchEvents();
                                    },
                                    error: function (data) {
                                        alert("Ocurrio un error");
                                    },
                                });
                            }
                        },
                    });
                }
            } else {
                bootbox.confirm({
                    title: "Eliminar evento del calendario",
                    message:
                        "¿Desea eliminar: " +
                        info.event.title +
                        " del calendario?",
                    buttons: {
                        confirm: {
                            label: "Aceptar",
                            className: "btn-success",
                        },
                        cancel: {
                            label: "Cancelar",
                            className: "btn-light",
                        },
                    },
                    callback: function (result) {
                        if (result == true) {
                            $.ajax({
                                type: "post",
                                url: "/empleado/eliminareventBD",
                                data: {
                                    ideve: info.event.id,
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
                                    info.event.remove();
                                    var a = moment(data.end);
                                    c = a._i;
                                    var b = moment(data.start);
                                    d = b._i;

                                    if (a.diff(b, 'days') > 1) {
                                        $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(a).subtract(1, 'day').format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffffff");
                                    }

                                    $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(data.start).format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffffff");

                                    calendar2_ed.refetchEvents();
                                },
                                error: function (data) {
                                    alert("Ocurrio un error");
                                },
                            });
                        }
                    },
                });
            }
        },
        editable: false,
        eventLimit: true,
        header: {
            left: "prev,next today",
            center: "title",
            right: "",
        },
        eventRender: function (info) {
            $('.tooltip').remove();
            if (info.event.extendedProps.horaI === null) {
                $(info.el).tooltip({ title: info.event.title });
            } else {
                if (info.event.borderColor == '#5369f8') {
                    if (info.event.extendedProps.horaAdic == 1) {
                        $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' + '     Marca horas adicionales' });
                    } else {
                        $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' });
                    }
                }
                else {
                    $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF });
                }
            }
        },
        events: function (info, successCallback, failureCallback) {
            var idcalendario = $("#selectCalendario_ed").val();
            var idempleado = $("#idempleado").val();
            $.ajax({
                type: "POST",
                url: "/empleado/calendarioEmpleado",
                data: {
                    idcalendario,
                    idempleado,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    },
                },
                success: function (data) {
                    successCallback(data);
                    $.each(data, function (index, value) {

                        if (value.laborable == 0) {
                            var element = $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date]");

                            var a = moment(value.end);
                            c = a._i;
                            var b = moment(value.start);
                            d = b._i;

                            if (a.diff(b, 'days') > 1) {
                                $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(a).subtract(1, 'day').format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffefef");
                            }

                            $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(value.start).format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffefef");
                        }


                    });
                },
                error: function () { },
            });
        },

        /*  events: "calendario/show", */
    };
    calendarioedit = new FullCalendar.Calendar(
        calendarEl,
        configuracionCalendario
    );
    calendarioedit.setOption("locale", "Es");

    calendarioedit.render();
}
/* document.addEventListener("DOMContentLoaded", calendario_edit); */ ///////////
function laborable_ed() {
    $("#calendarioAsignar_ed").modal("hide");
    title = "Descanso";
    color = "#4673a0";
    textColor = "#ffffff";
    start = $("#pruebaStar_ed").val();
    end = $("#pruebaEnd_ed").val();
    tipo = 3;
    var idempleado = $("#idempleado").val();
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
            idempleado,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (msg) {
            //var date = calendar1.getDate();
            //alert("The current date of the calendar is " + date.toISOString());

            calendarioedit.refetchEvents();
            calendar2_ed.refetchEvents();


        },
        error: function () { },
    });
}
/////////////
function nolaborable_ed() {
    $("#calendarioAsignar_ed").modal("hide");
    title = "No laborable";
    color = "#a34141";
    textColor = " #ffffff ";
    start = $("#pruebaStar_ed").val();
    end = $("#pruebaEnd_ed").val();
    tipo = 0;
    var idempleado = $("#idempleado").val();
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
            idempleado,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (msg) {
            //var date = calendar1.getDate();
            //alert("The current date of the calendar is " + date.toISOString());

            calendarioedit.refetchEvents();
            calendar2_ed.refetchEvents();


        },
        error: function () { },
    });
}
//////////////////
function agregarinciden_ed() {
    $("#calendarioAsignar_ed").modal("hide");
    $("#frmIncidenciaCa_ed")[0].reset();
    $("#modalIncidencia_ed").modal("show");
}
//////////////////
function modalIncidencia_ed() {
    var idempleado = $("#idempleado").val();
    descripcionI = $("#descripcionInciCa_ed").val();
    var descuentoI;
    if ($("#descuentoCheckCa_ed").prop("checked")) {
        descuentoI = 1;
    } else {
        descuentoI = 0;
    }
    fechaI = $("#pruebaStar_ed").val();
    fechaFin = $("#pruebaEnd_ed").val();


    $.ajax({
        type: "post",
        url: "/empleado/storeIncidempleado",
        data: {
            start: fechaI,
            title: descripcionI,
            descuentoI: descuentoI,
            end: fechaFin,

            idempleado,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            calendarioedit.refetchEvents();
            calendar2_ed.refetchEvents();
            $("#modalIncidencia_ed").modal("hide");
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
}
//////////////////////////
function agregarHorarioSe() {
    var H1 = $("#pruebaStar_ed").val();
    var H2 = $("#pruebaEnd_ed").val();
    textSelec1 = $('select[name="selectHorario_ed"] option:selected').text();
    separador = "(";
    textSelec2 = textSelec1.split(separador);
    textSelec = textSelec2[0];
    var idhorar = $("#selectHorario_ed").val();
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
        var nHoraAdic = $('#nHorasAdic').val();
    } else {
        horarioA = 0;
        var nHoraAdic = null;
    }
    var idempleado = $("#idempleado").val();
    var diasEntreFechas = function (desde, hasta) {
        var dia_actual = desde;
        var fechas = [];
        while (dia_actual.isSameOrBefore(hasta)) {
            fechas.push(dia_actual.format("YYYY-MM-DD"));
            dia_actual.add(1, "days");
        }
        return fechas;
    };

    desde = moment(H1);
    hasta = moment(H2);
    var results = diasEntreFechas(desde, hasta);
    results.pop();

    var fechasArray = [];
    var fechastart = [];

    var objeto = [];
    $.each(results, function (key, value) {
        //alert( value );
        fechasArray.push(textSelec);
        fechastart.push(value);

        objeto.push({
            title: textSelec,
            start: value,
        });
    });


    $.ajax({
        type: "post",
        url: "/empleado/guardarhorarioempleado",
        data: {
            fechasArray: fechastart,
            hora: textSelec,
            idhorar: idhorar,
            idempleado, fueraHora, horarioC, horarioA, nHoraAdic
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
            calendarioedit.refetchEvents();
            calendar2_ed.refetchEvents();
            $("#selectHorario_ed").val("Seleccionar horario");
            $("#selectHorario_ed").trigger("change");
            $("#horarioAsignar_ed").modal("hide");
            $.notify(
                {
                    message: "\nCambios guardados\n",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-ver"),
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
                    spacing: 35,
                }
            );
        },
        // error: function (data) {
        // },
    });
};

////////////////////////////
function abrirHorario_ed() {
    $('#errorenPausas_ed').hide();
    $('#divOtrodia_ed').hide();
    $('#divPausa_ed').hide();
    $('#inputPausa_ed').empty();
    $('#horaOblig_ed').prop("disabled", "disabled");
    $('#inputPausa_ed').append('<div id="divEd_100" class="row col-md-12" style=" margin-bottom: 8px;">' +
        '<input type="text"  class="form-control form-control-sm col-sm-5" name="descPausa_ed[]" id="descPausa_ed" >' +
        '<input type="text"  class="form-control form-control-sm col-sm-3" name="InicioPausa_ed[]"  id="InicioPausa_ed" >' +
        '<input type="text"  class="form-control form-control-sm col-sm-3" name="FinPausa_ed[]"  id="FinPausa_ed" disabled >' +
        '&nbsp; <button class="btn btn-sm bt_ed" id="100" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
        ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>' +
        '</div>');
    $('.flatpickr-input[readonly]').on('focus', function () {
        $(this).blur()
    })
    $('.flatpickr-input[readonly]').prop('readonly', false)
    $(".bt_ed").each(function (el) {
        $(this).bind("click", addField);
    });
    $('#InicioPausa_ed').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour: null
    });

    $('input[name="descPausa_ed[]"]').prop('required', false);
    $('input[name="InicioPausa_ed[]"]').prop('required', false);
    $('input[name="FinPausa_ed[]"]').prop('required', false);
    $("#frmHor_ed")[0].reset();
    $("#horarioAgregar_ed").modal("show");
}

function registrarHorario_ed() {

    var descripcion = $("#descripcionCa_ed").val();
    var toleranciaH = $("#toleranciaH_ed").val();
    var inicio = $("#horaI_ed").val();
    var fin = $("#horaF_ed").val();
    toleranciaF = $('#toleranciaSalida_ed').val();
    horaOblig = $('#horaOblig_ed').val();
    var tardanza;
    if ($('#SwitchTardanza_ed').is(":checked")) {
        tardanza = 1;
    } else {
        tardanza = 0;
    }
    if ($('#SwitchPausa_ed').is(":checked")) {
        var descPausa = [];
        var pausaInicio = [];
        var finPausa = [];
        $('input[name="descPausa_ed[]"]').each(function () {
            descPausa.push($(this).val());
        });
        $('input[name="InicioPausa_ed[]"]').each(function () {
            pausaInicio.push($(this).val());
        });
        $('input[name="FinPausa_ed[]"]').each(function () {
            finPausa.push($(this).val());
        });
    }

    $.ajax({
        type: "post",
        url: "/empleado/registrarHorario",
        data: {
            descripcion,
            toleranciaH,
            inicio,
            fin, descPausa, pausaInicio, finPausa, toleranciaF, horaOblig, tardanza
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

            idhorar = data.horario_id;

            $("#selectHorario_ed").append(
                $("<option>", {
                    //agrego los valores que obtengo de una base de datos
                    value: data.horario_id,
                    text: data.horario_descripcion + ' (' + data.horaI + '-' + data.horaF + ')',
                    selected: true,
                })
            );
            $("#horarioAgregar_ed").modal("hide");

            $("#selectHorario_ed").trigger("change");
            /* $('#selectHorarioen').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.horario_id,
                text: data.horario_descripcion

            })); */
        },
        error: function () {
            alert("Hay un error");
        },
    });
}

////////////////////7
function laborableTem() {
    $("#calendarioAsignar").modal("hide");

    title = "Descanso";
    color = "#4673a0";
    textColor = "#ffffff";
    start = $("#pruebaStar").val();
    end = $("#pruebaEnd").val();
    tipo = 3;
    id_calendario = $("#selectCalendario").val();
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
            id_calendario,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (msg) {
            //var date = calendar1.getDate();
            //alert("The current date of the calendar is " + date.toISOString());

            calendar.refetchEvents();
            calendar2.refetchEvents();


        },
        error: function () { },
    });
}
/////////////////////////////////
function diaferiadoTem() {
    $("#calendarioAsignar").modal("hide");
    (title = $("#nombreFeriado").val()),
        (color = "#e6bdbd"),
        (textColor = "#775555"),
        (start = $("#pruebaStar").val());
    end = $("#pruebaEnd").val();
    tipo = 2;
    id_calendario = $("#selectCalendario").val();
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
            id_calendario,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (msg) {
            $("#myModalFeriado").modal("hide");
            calendar.refetchEvents();
            calendar2.refetchEvents();


        },
        error: function () { },
    });
}
/////////////////////////////////
function nolaborableTem() {
    $("#calendarioAsignar").modal("hide");

    title = "No laborable";
    color = "#a34141";
    textColor = " #ffffff ";
    start = $("#pruebaStar").val();
    end = $("#pruebaEnd").val();
    tipo = 0;
    id_calendario = $("#selectCalendario").val();
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
            id_calendario,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (msg) {
            //var date = calendar1.getDate();
            //alert("The current date of the calendar is " + date.toISOString());

            calendar.refetchEvents();
            calendar2.refetchEvents();


        },
        error: function () { },
    });
}

function agregarinciden() {
    $("#calendarioAsignar").modal("hide");
    $("#frmIncidenciaCa")[0].reset();
    $("#modalIncidencia").modal("show");
}

function modalIncidencia() {
    var id_calendario = $("#selectCalendario").val();
    descripcionI = $("#descripcionInciCa").val();
    var descuentoI;
    if ($("#descuentoCheckCa").prop("checked")) {
        descuentoI = 1;
    } else {
        descuentoI = 0;
    }
    fechaI = $("#pruebaStar").val();
    fechaFin = $("#pruebaEnd").val();


    $.ajax({
        type: "post",
        url: "/empleado/storeIncidTem",
        data: {
            start: fechaI,
            title: descripcionI,
            descuentoI: descuentoI,
            end: fechaFin,

            id_calendario,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            calendar.refetchEvents();
            calendar2.refetchEvents();
            $("#modalIncidencia").modal("hide");
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
}

$("#selectCalendario").change(function () {
    var idempleado = $("#idEmpleado").val();

    $.ajax({
        type: "post",
        url: "/empleado/vaciarbdempleado",
        data: {
            idempleado,
        },
        async: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) { },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });
    $("#detallehorario").empty();
    idca = $("#selectCalendario").val();
    $.ajax({
        type: "post",
        url: "/empleado/vaciarcalendId",
        data: {
            idca,
        },
        async: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            calendar.refetchEvents();
            calendar2.refetchEvents();
            $("#calendarInv").hide();
            $("#calendar").show();
            $("#opborrar").show();
            $("#mensajeOc").hide();
            $("#calendar2").show();

            $("#detallehorario").append(
                "<div class='form-group row'><div class='col-md-1'></div><label class='col-lg-3 col-form-label' style='color:#163552;margin-top: 5px;'>Se muestra calendario de: </label>" +
                "<div class='col-md-5'><select disabled style='margin-top: 9px;' class='form-control col-lg-6 form-control-sm'><option>" +
                $('select[id="selectCalendario"] option:selected').text() +
                "</option></select></div>" +
                "<div class='col-md-2' ><div class='btn-group mt-2 mr-1'> <button type='button' onclick='eliminarhorariosTem()' class='btn btn-primary btn-sm dropdown-toggle' style='color: #fff; background-color: #4a5669;" +
                "border-color: #485263;' > <img src='admin/images/borrador.svg' height='15'>" +
                " Borrar</button> </div></div></div>"
            );
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });

    var dialog = bootbox.dialog({
        message:
            "Ahora esta en el calendario de " +
            $('select[id="selectCalendario"] option:selected').text(),
        closeButton: false,
    });
    setTimeout(function () {
        dialog.modal("hide");
    }, 1400);
});

///edit select
$("#selectCalendario_ed").change(function () {
    $("#detallehorario_ed").empty();
    var idempleado = $("#idempleado").val();
    $.ajax({
        type: "post",
        url: "/empleado/vaciarcalendempleado",
        data: {
            idempleado,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $("#calendarInv_ed").hide();
            $("#calendar_ed").show();
            $("#mensajeOc_ed").hide();
            $("#calendar2_ed").show();

            calendario_edit();
            calendario2_ed();
            $("#detallehorario_ed").append(
                "<div class='form-group row'><div class='col-md-5 text-right'><label style='color:#163552;margin-top: 5px;'>Se muestra calendario de: </label> </div>" +
                "<div class='col-md-5'><select disabled class='form-control form-control-sm'><option>" +
                $(
                    'select[id="selectCalendario_ed"] option:selected'
                ).text() +
                "</option></select></div></div>"
            );
        },
        error: function (data) {
            alert("Ocurrio un error");
        },
    });

    var dialog = bootbox.dialog({
        message:
            "Ahora esta en el calendario de " +
            $('select[id="selectCalendario_ed"] option:selected').text(),
        closeButton: false,
    });
    setTimeout(function () {
        dialog.modal("hide");
    }, 1400);
});
///////////////////
function eliminarhorariosTem() {
    fmes = calendar2.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        title: "Eliminar horario",
        message: "¿Esta seguro que desea eliminar horarios del calendario?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/empleado/vaciarhorarioTem",
                    data: {
                        mescale,
                        aniocalen,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        calendar.refetchEvents();
                        calendar2.refetchEvents();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}
////////
function calendario2() {
    var calendarEl = document.getElementById("calendar2");
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: "es",
        defaultDate: fecha,
        height: 400,
        fixedWeekCount: false,
        plugins: ["dayGrid", "interaction", "timeGrid"],

        selectable: true,
        selectMirror: true,
        select: function (arg) {
            $("#pruebaEnd").val(moment(arg.end).format("YYYY-MM-DD HH:mm:ss"));
            $("#pruebaStar").val(
                moment(arg.start).format("YYYY-MM-DD HH:mm:ss")
            );
            $("#selectHorario").val("Seleccionar horario");
            $('#errorSel_re').hide();
            $('#nHorasAdic').hide();
            $('#nHorasAdic_re').hide();
            $("#selectHorario").trigger("change");
            $('#fueraHSwitch_re').prop('checked', true)
            $('#fueraHSwitch_re').prop('disabled', false)
            $('#horAdicSwitch_re').prop('checked', false)
            $('#horCompSwitch_re').prop('checked', true)
            $("#horarioAsignar").modal("show");
        },
        eventClick: function (info) {
            id = info.event.id;

            var event = calendar2.getEventById(id);

            bootbox.confirm({
                title: "Eliminar evento del calendario",
                message:
                    "¿Desea eliminar: " + info.event.title + " del calendario?",
                buttons: {
                    confirm: {
                        label: "Aceptar",
                        className: "btn-success",
                    },
                    cancel: {
                        label: "Cancelar",
                        className: "btn-light",
                    },
                },
                callback: function (result) {
                    if (result == true) {
                        $.ajax({
                            type: "post",
                            url: "/empleado/eliminarEte",
                            data: {
                                ideve: info.event.id,
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
                                info.event.remove();
                                calendar.refetchEvents();
                            },
                            error: function (data) {
                                alert("Ocurrio un error");
                            },
                        });
                    }
                },
            });
        },
        editable: false,
        eventLimit: true,
        header: {
            left: "prev,next today",
            center: "title",
            right: "",
        },
        eventRender: function (info) {
            $('.tooltip').remove();
            if (info.event.extendedProps.horaI === null) {
                $(info.el).tooltip({ title: info.event.title });
            } else {
                if (info.event.borderColor == '#5369f8') {
                    if (info.event.extendedProps.horaAdic == 1) {
                        $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' + '     Marca horas adicionales' });
                    } else {
                        $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' });
                    }

                }
                else {
                    $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF });
                }
            }
        },
        events: function (info, successCallback, failureCallback) {
            var idcalendario = $("#selectCalendario").val();
            var datoscal;
            $.ajax({
                type: "POST",
                url: "/empleado/calendarioEmpTemp",
                data: {
                    idcalendario,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    },
                },
                success: function (data) {
                    successCallback(data);
                },
                error: function () { },
            });
        },

        /*  events: "calendario/show", */
    };
    calendar2 = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendar2.setOption("locale", "Es");

    calendar2.render();
}
document.addEventListener("DOMContentLoaded", calendario2);

function abrirHorario() {
    $('#errorenPausas').hide();
    $('#divOtrodia').hide();
    $('#divPausa').hide();
    $('#inputPausa').empty();
    $('#horaOblig').prop("disabled", "disabled");
    $('#inputPausa').append('<div id="div_100" class="row col-md-12" style=" margin-bottom: 8px;">' +
        '<input type="text"  class="form-control form-control-sm col-sm-5" name="descPausa[]" id="descPausa" >' +
        '<input type="text"  class="form-control form-control-sm col-sm-3" name="InicioPausa[]"  id="InicioPausa" >' +
        '<input type="text"  class="form-control form-control-sm col-sm-3" name="FinPausa[]"  id="FinPausa" disabled >' +
        '&nbsp; <button class="btn btn-sm bt_re" id="400" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
        ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>' +
        '</div>');
    $('.flatpickr-input[readonly]').on('focus', function () {
        $(this).blur()
    })
    $('.flatpickr-input[readonly]').prop('readonly', false)
    $(".bt_re").each(function (el) {
        $(this).bind("click", addFieldRe);
    });
    $('#InicioPausa').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour: null
    });

    $('input[name="descPausa[]"]').prop('required', false);
    $('input[name="InicioPausa[]"]').prop('required', false);
    $('input[name="FinPausa[]"]').prop('required', false);
    $("#frmHor")[0].reset();
    $("#horarioAgregar").modal("show");
}

function registrarHorario() {

    var descripcion = $("#descripcionCa").val();
    var toleranciaH = $("#toleranciaH").val();
    var inicio = $("#horaI").val();
    var fin = $("#horaF").val();
    toleranciaF = $('#toleranciaSalida').val();
    horaOblig = $('#horaOblig').val();
    var tardanza;
    if ($('#SwitchTardanza').is(":checked")) {
        tardanza = 1;
    } else {
        tardanza = 0;
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
        url: "/empleado/registrarHorario",
        data: {
            descripcion,
            toleranciaH,
            inicio,
            fin, descPausa, pausaInicio, finPausa, toleranciaF, horaOblig, tardanza
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

            idhorar = data.horario_id;



            $("#selectHorario").append(
                $("<option>", {
                    //agrego los valores que obtengo de una base de datos
                    value: data.horario_id,
                    text: data.horario_descripcion + ' (' + data.horaI + '-' + data.horaF + ')',
                    selected: true,
                })
            );

            $("#selectHorario").trigger("change");
            /* $('#selectHorarioen').append($('<option>', { //agrego los valores que obtengo de una base de datos
                value: data.horario_id,
                text: data.horario_descripcion

            })); */
            $("#horarioAgregar").modal("hide");
        },
        error: function () {
            alert("Hay un error");
        },
    });
}
function agregarHorarioSe_regis() {
    var H1 = $("#pruebaStar").val();
    var H2 = $("#pruebaEnd").val();
    textSelec1 = $('select[name="selectHorario"] option:selected').text();
    separador = "(";
    textSelec2 = textSelec1.split(separador);
    textSelec = textSelec2[0];

    var fueraHora;
    if ($('#fueraHSwitch_re').prop('checked')) {
        fueraHora = 1;
        console.log(fueraHora);
    } else {
        fueraHora = 0;
        console.log(fueraHora);
    }
    // HORARIO COMPENSABLE
    var horarioC;
    if ($('#horCompSwitch_re').prop('checked')) {
        horarioC = 1;
    } else {
        horarioC = 0;
    }

    // HORA ADICIONAL
    var horarioA;
    if ($('#horAdicSwitch_re').prop('checked')) {
        horarioA = 1;
        var nHoraAdic = $('#nHorasAdic_re').val();
    } else {
        horarioA = 0;
        var nHoraAdic = null;
    }
    var idhorar = $("#selectHorario").val();
    if (idhorar == null) {
        $('#errorSel_re').show();
        return false;
    } else {
        $('#errorSel_re').hide();
    }
    var idca = $("#selectCalendario").val();
    var diasEntreFechas = function (desde, hasta) {
        var dia_actual = desde;
        var fechas = [];
        while (dia_actual.isSameOrBefore(hasta)) {
            fechas.push(dia_actual.format("YYYY-MM-DD"));
            dia_actual.add(1, "days");
        }
        return fechas;
    };

    desde = moment(H1);
    hasta = moment(H2);
    var results = diasEntreFechas(desde, hasta);
    results.pop();

    var fechasArray = [];
    var fechastart = [];

    var objeto = [];
    $.each(results, function (key, value) {
        //alert( value );
        fechasArray.push(textSelec);
        fechastart.push(value);

        objeto.push({
            title: textSelec,
            start: value,
        });
    });


    $.ajax({
        type: "post",
        url: "/empleado/guardarhorarioTem",
        data: {
            fechasArray: fechastart,
            hora: textSelec,

            idhorar: idhorar,
            idca, fueraHora, horarioC, horarioA, nHoraAdic
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
            calendar.refetchEvents();
            calendar2.refetchEvents();
            $("#selectHorario").val("Seleccionar horario");
            $("#selectHorario").trigger("change");
            $("#horarioAsignar").modal("hide");
        },
        // error: function (data) {
        // },
    });
};

//vercal

function calendario3() {
    var calendarEl = document.getElementById("calendar3");
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: "es",
        defaultDate: fecha,
        height: 400,
        fixedWeekCount: false,
        plugins: ["dayGrid", "interaction", "timeGrid"],

        selectable: true,
        selectMirror: true,
        /* select: function (arg) {
            $('#pruebaEnd').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
            $('#pruebaStar').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));

            $('#horarioAsignar').modal('show');
        }, */
        eventClick: function (info) { },
        editable: false,
        eventLimit: true,
        header: {
            left: "prev,next today",
            center: "title",
            right: "",
        },
        eventRender: function (info) {
            $('.tooltip').remove();
            /*  $(info.el).tooltip({  title: info.event.extendedProps.horaI+'-'+info.event.extendedProps.horaF}); */
            if (info.event.extendedProps.horaI === null) {
                $(info.el).tooltip({ title: info.event.title });
            } else {
                if (info.event.borderColor == '#5369f8') {
                    if (info.event.extendedProps.horaAdic == 1) {
                        $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' + '     Marca horas adicionales' });
                    } else {
                        $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' });
                    }

                }
                else {
                    $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF });
                }
            }
        },
        events: function (info, successCallback, failureCallback) {
            var idempleado = $("#idempleado").val();
            var datoscal;
            $.ajax({
                type: "POST",
                url: "/empleado/vercalendario",
                data: {
                    idempleado,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    },
                },
                success: function (data) {
                    successCallback(data);
                },
                error: function () { },
            });
        },

        /*  events: "calendario/show", */
    };
    calendar3 = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendar3.setOption("locale", "Es");

    calendar3.render();
}
/* document.addEventListener("DOMContentLoaded", calendario3); */
////////////////
function calendario4() {
    var calendarEl = document.getElementById("calendar4");
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: "es",
        defaultDate: fecha,
        height: 400,
        fixedWeekCount: false,
        plugins: ["dayGrid", "interaction", "timeGrid"],

        selectable: true,
        selectMirror: true,
        /* select: function (arg) {
            $('#pruebaEnd').val(moment(arg.end).format('YYYY-MM-DD HH:mm:ss'));
            $('#pruebaStar').val(moment(arg.start).format('YYYY-MM-DD HH:mm:ss'));

            $('#horarioAsignar').modal('show');
        }, */
        eventClick: function (info) { },
        editable: false,
        eventLimit: true,
        header: {
            left: "prev,next today",
            center: "title",
            right: "",
        },
        eventRender: function (info) {
            $('.tooltip').remove();
            /*  $(info.el).tooltip({  title: info.event.extendedProps.horaI+'-'+info.event.extendedProps.horaF}); */
            if (info.event.extendedProps.horaI === null) {
                $(info.el).tooltip({ title: info.event.title });
            } else {
                if (info.event.borderColor == '#5369f8') {
                    if (info.event.extendedProps.horaAdic == 1) {
                        $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' + '     Marca horas adicionales' });
                    } else {
                        $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' });
                    }
                }
                else {
                    $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF });
                }
            }
        },
        events: function (info, successCallback, failureCallback) {
            var idempleado = $("#idempleado").val();
            var datoscal;
            $.ajax({
                type: "POST",
                url: "/empleado/vercalendario",
                data: {
                    idempleado,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    },
                },
                success: function (data) {
                    successCallback(data);
                },
                error: function () { },
            });
        },

        /*  events: "calendario/show", */
    };
    calendar4 = new FullCalendar.Calendar(calendarEl, configuracionCalendario);
    calendar4.setOption("locale", "Es");

    calendar4.render();
}
/* document.addEventListener("DOMContentLoaded", calendario4); */
//************* */
$("#checkboxFechaI").on("click", function () {
    if ($("#checkboxFechaI").is(":checked")) {
        $("#labelfechaF").hide();
        $("#mf_dia_fecha").val("0");
        $("#mf_mes_fecha").val("0");
        $("#mf_ano_fecha").val("0");
        $("#mf_dia_fecha").hide();
        $("#mf_mes_fecha").hide();
        $("#mf_ano_fecha").hide();
    } else {
        $("#labelfechaF").show();
        $("#mf_dia_fecha").show();
        $("#mf_mes_fecha").show();
        $("#mf_ano_fecha").show();
    }
});
/* document.addEventListener("DOMContentLoaded", calendario3); */

///inv
function calendarioInv_ed() {
    var calendarElInv_ed = document.getElementById("calendarInv_ed");
    calendarElInv_ed.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: "es",
        defaultDate: fecha,
        height: 360,
        fixedWeekCount: false,
        plugins: ["dayGrid", "interaction", "timeGrid"],

        selectable: false,
        selectMirror: true,

        editable: false,
        eventLimit: true,
        header: {
            left: "prev,next today",
            center: "title",
            right: "",
        },
    };
    var calendarInv_ed = new FullCalendar.Calendar(
        calendarElInv_ed,
        configuracionCalendario
    );
    calendarInv_ed.setOption("locale", "Es");

    calendarInv_ed.render();
}
/* document.addEventListener("DOMContentLoaded", calendarioInv_ed); */
////////////////////////////
function calendario2_ed() {
    var calendarEl = document.getElementById("calendar2_ed");
    calendarEl.innerHTML = "";

    var fecha = new Date();
    var ano = fecha.getFullYear();
    var id;

    var configuracionCalendario = {
        locale: "es",
        defaultDate: fecha,
        height: 400,
        fixedWeekCount: false,
        plugins: ["dayGrid", "interaction", "timeGrid"],

        selectable: true,
        selectMirror: true,
        select: function (arg) {
            $("#pruebaEnd_ed").val(
                moment(arg.end).format("YYYY-MM-DD HH:mm:ss")
            );
            $("#pruebaStar_ed").val(
                moment(arg.start).format("YYYY-MM-DD HH:mm:ss")
            );
            $("#selectHorario_ed").val("Seleccionar horario");
            $('#errorSel').hide();
            $("#selectHorario_ed").trigger("change");
            $('#fueraHSwitch').prop('checked', true)
            $('#fueraHSwitch').prop('disabled', false)
            $('#horAdicSwitch').prop('checked', false)
            $('#horCompSwitch').prop('checked', true)
            $("#horarioAsignar_ed").modal("show");
        },
        eventClick: function (info) {
            id = info.event.id;

            var event = calendarioedit.getEventById(id);
            if (
                info.event.textColor == "111111" ||
                info.event.textColor == "1" ||
                info.event.textColor == "0"
            ) {
                if (info.event.textColor == "111111") {
                    bootbox.confirm({
                        title: "Eliminar horario",
                        message:
                            "¿Desea eliminar: " +
                            info.event.title +
                            " del calendario?",
                        buttons: {
                            confirm: {
                                label: "Aceptar",
                                className: "btn-success",
                            },
                            cancel: {
                                label: "Cancelar",
                                className: "btn-light",
                            },
                        },
                        callback: function (result) {
                            if (result == true) {
                                $.ajax({
                                    type: "post",
                                    url: "/empleado/eliminarHorariosEdit",
                                    data: {
                                        ideve: info.event.id,
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
                                        info.event.remove();
                                        calendar2_ed.refetchEvents();
                                    },
                                    error: function (data) {
                                        alert("Ocurrio un error");
                                    },
                                });
                            }
                        },
                    });
                } else {
                    bootbox.confirm({
                        title: "Eliminar incidencia",
                        message:
                            "¿Desea eliminar: " +
                            info.event.title +
                            " del calendario?",
                        buttons: {
                            confirm: {
                                label: "Aceptar",
                                className: "btn-success",
                            },
                            cancel: {
                                label: "Cancelar",
                                className: "btn-light",
                            },
                        },
                        callback: function (result) {
                            if (result == true) {
                                $.ajax({
                                    type: "post",
                                    url: "/empleado/eliminarInciEdit",
                                    data: {
                                        ideve: info.event.id,
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
                                        info.event.remove();
                                        var a = moment(data.inciden_dias_fechaF);
                                        c = a._i;
                                        var b = moment(data.inciden_dias_fechaI);
                                        d = b._i;

                                        if (a.diff(b, 'days') > 1) {
                                            $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(a).subtract(1, 'day').format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffffff");
                                        }

                                        $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(data.inciden_dias_fechaI).format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffffff");
                                        calendar2_ed.refetchEvents();
                                    },
                                    error: function (data) {
                                        alert("Ocurrio un error");
                                    },
                                });
                            }
                        },
                    });
                }
            } else {
                bootbox.confirm({
                    title: "Eliminar evento del calendario",
                    message:
                        "¿Desea eliminar: " +
                        info.event.title +
                        " del calendario?",
                    buttons: {
                        confirm: {
                            label: "Aceptar",
                            className: "btn-success",
                        },
                        cancel: {
                            label: "Cancelar",
                            className: "btn-light",
                        },
                    },
                    callback: function (result) {
                        if (result == true) {
                            $.ajax({
                                type: "post",
                                url: "/empleado/eliminareventBD",
                                data: {
                                    ideve: info.event.id,
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
                                    info.event.remove();
                                    var a = moment(data.end);
                                    c = a._i;
                                    var b = moment(data.start);
                                    d = b._i;

                                    if (a.diff(b, 'days') > 1) {
                                        $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(a).subtract(1, 'day').format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffffff");
                                    }

                                    $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(data.start).format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffffff");

                                    calendar2_ed.refetchEvents();
                                },
                                error: function (data) {
                                    alert("Ocurrio un error");
                                },
                            });
                        }
                    },
                });
            }
        },
        editable: false,
        eventLimit: true,
        header: {
            left: "prev,next today",
            center: "title",
            right: "borrarHorarios",
        },
        eventRender: function (info) {
            $('.tooltip').remove();
            if (info.event.extendedProps.horaI === null) {
                $(info.el).tooltip({ title: info.event.title });
            } else {
                if (info.event.borderColor == '#5369f8') {
                    if (info.event.extendedProps.horaAdic == 1) {
                        $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' + '     Marca horas adicionales' });
                    } else {
                        $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' });
                    }

                }
                else {
                    $(info.el).tooltip({ title: info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF });
                }
            }
        },
        customButtons: {
            borrarHorarios: {
                text: "Borrar H.",
                /*  icon:"right-double-arrow", */

                click: function () {
                    eliminarhorariosBD();
                }
            },
        },
        events: function (info, successCallback, failureCallback) {
            var idcalendario = $("#selectCalendario_ed").val();
            var idempleado = $("#idempleado").val();
            var datoscal;
            $.ajax({
                type: "POST",
                url: "/empleado/calendarioEmpleado",
                data: {
                    idcalendario,
                    idempleado,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                statusCode: {
                    419: function () {
                        location.reload();
                    },
                },
                success: function (data) {
                    $.each(data, function (index, value) {
                        successCallback(data);
                        if (value.laborable == 0) {
                            var element = $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date]");

                            var a = moment(value.end);
                            c = a._i;
                            var b = moment(value.start);
                            d = b._i;

                            if (a.diff(b, 'days') > 1) {
                                $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(a).subtract(1, 'day').format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffefef");
                            }

                            $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(value.start).format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffefef");
                        }

                    });

                },
                error: function () { },
            });
        },

        /*  events: "calendario/show", */
    };
    calendar2_ed = new FullCalendar.Calendar(
        calendarEl,
        configuracionCalendario
    );
    calendar2_ed.setOption("locale", "Es");

    calendar2_ed.render();
}
/* document.addEventListener("DOMContentLoaded", calendario2_ed); */
//************* */
$("#checkboxFechaI").on("click", function () {
    if ($("#checkboxFechaI").is(":checked")) {
        $("#labelfechaF").hide();
        $("#mf_dia_fecha").val("0");
        $("#mf_mes_fecha").val("0");
        $("#mf_ano_fecha").val("0");
        $("#mf_dia_fecha").hide();
        $("#mf_mes_fecha").hide();
        $("#mf_ano_fecha").hide();
    } else {
        $("#labelfechaF").show();
        $("#mf_dia_fecha").show();
        $("#mf_mes_fecha").show();
        $("#mf_ano_fecha").show();
    }
});
////////////////////////////
$("#file").fileinput({
    allowedFileExtensions: ["jpg", "jpeg", "png"],
    uploadAsync: false,
    showRemove: true,
    minFileCount: 0,
    maxFileCount: 1,
    initialPreviewAsData: true, // identify if you are sending preview data only and not the markup
    language: "es",
    browseOnZoneClick: true,
    theme: "fa",
    showUpload: false,
    showBrowse: false,
});

//AREA
function agregarArea() {
    objArea = datosArea("POST");
    enviarArea("", objArea);
}

function datosArea(method) {
    nuevoArea = {
        area_descripcion: $("#textArea").val(),
        _method: method,
    };
    return nuevoArea;
}

function enviarArea(accion, objArea) {
    var id = $("#editarA").val();
    if (id == "" || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/area" + accion,
            data: objArea,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#area").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.area_id,
                        text: data.area_descripcion,
                        selected: true,
                    })
                );
                $("#v_area").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.area_id,
                        text: data.area_descripcion
                    })
                );
                $("#area").val(data.area_id).trigger("change"); //lo selecciona
                $("#textArea").val("");
                $("#editarArea").hide();
                limpiar();
                $("#areamodal").modal("toggle");
                $("#form-registrar").modal("show");
                $.notify(
                    {
                        message: "\nÁrea Registrada\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#form-registrar"),
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
                        spacing: 35,
                    }
                );
            },
            error: function () { },
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarArea" + accion,
            data: {
                id: id,
                objArea,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#area").empty();
                $("#v_area").empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/area",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].area_id}">${data[i].area_descripcion}</option>`;
                        }
                        $("#area").append(select);
                        $("#v_area").append(select);
                    },
                    error: function () { },
                });
                $("#area").val(data.area_id).trigger("change"); //lo selecciona
                $("#textArea").val("");
                $("#editarArea").hide();
                limpiar();
                $("#areamodal").modal("toggle");
                $("#form-registrar").modal("show");
                $.notify(
                    {
                        message: "\nÁrea Modificada\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#form-registrar"),
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
                        spacing: 35,
                    }
                );
            },
        });
    }
}
///CARGO
function agregarcargo() {
    objCargo = datosCargo("POST");
    enviarCargo("", objCargo);
}

function datosCargo(method) {
    nuevoCargo = {
        cargo_descripcion: $("#textCargo").val(),
        _method: method,
    };
    return nuevoCargo;
}

function enviarCargo(accion, objCargo) {
    var id = $("#editarC").val();
    if (id == "" || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/cargo" + accion,
            data: objCargo,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#cargo").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.cargo_id,
                        text: data.cargo_descripcion,
                        selected: true,
                    })
                );
                $("#v_cargo").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.cargo_id,
                        text: data.cargo_descripcion
                    })
                );
                $("#cargo").val(data.cargo_id).trigger("change"); //lo selecciona
                $("#textCargo").val("");
                $("#editarCargo").hide();
                limpiar();
                $("#cargomodal").modal("toggle");
                $("#form-registrar").modal("show");
                $.notify(
                    {
                        message: "\nCargo Registrado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#form-registrar"),
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
                        spacing: 35,
                    }
                );
            },
            error: function () { },
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarCargo" + accion,
            data: {
                id: id,
                objCargo,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#cargo").empty();
                $("#v_cargo").empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/cargo",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {

                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].cargo_id}">${data[i].cargo_descripcion}</option>`;
                        }
                        $("#cargo").append(select);
                        $("#v_cargo").append(select);
                    },
                    error: function () { },
                });
                $("#cargo").val(data.cargo_id).trigger("change"); //lo selecciona
                $("#textCargo").val("");
                $("#editarCargo").hide();
                limpiar();
                $("#cargomodal").modal("toggle");
                $("#form-registrar").modal("show");
                $.notify(
                    {
                        message: "\nCargo Modificado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#form-registrar"),
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
                        spacing: 35,
                    }
                );
            },
        });
    }
}
//centro costo
function agregarcentro() {
    objCentroC = datosCentro("POST");
    enviarCentro("", objCentroC);
}

function datosCentro(method) {
    nuevoCentro = {
        centroC_descripcion: $("#textCentro").val(),
        _method: method,
    };
    return nuevoCentro;
}

function enviarCentro(accion, objCentroC) {
    var id = $("#editarCC").val();
    if (id == "" || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/centro" + accion,
            data: objCentroC,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#centroc").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.centroC_id,
                        text: data.centroC_descripcion,
                        selected: true,
                    })
                );
                $("#v_centroc").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.centroC_id,
                        text: data.centroC_descripcion
                    })
                );
                $("#centroc").val(data.centroC_id).trigger("change"); //lo selecciona
                $("#textCentro").val("");
                $("#editarCentro").hide();
                limpiar();
                $("#centrocmodal").modal("toggle");
                $("#form-registrar").modal("show");
                $.notify(
                    {
                        message: "\nCentro Costo Registrado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#form-registrar"),
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
                        spacing: 35,
                    }
                );
            },
            error: function () { },
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarCentro" + accion,
            data: {
                id: id,
                objCentroC,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#centroc").empty();
                $("#v_centroc").empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/centro",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].centroC_id}">${data[i].centroC_descripcion}</option>`;
                        }
                        $("#centroc").append(select);
                        $("#v_centroc").append(select);
                    },
                    error: function () { },
                });
                $("#centroc").val(data.centroC_id).trigger("change"); //lo selecciona
                $("#textCentro").val("");
                $("#editarCentro").hide();
                limpiar();
                $("#centrocmodal").modal("toggle");
                $("#form-registrar").modal("show");
                $.notify(
                    {
                        message: "\nCentro Costo Modificado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#form-registrar"),
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
                        spacing: 35,
                    }
                );
            },
        });
    }
}
//LOCAL
function agregarlocal() {
    objLocal = datosLocal("POST");
    enviarLocal("", objLocal);
}

function datosLocal(method) {
    nuevoLocal = {
        local_descripcion: $("#textLocal").val(),
        _method: method,
    };
    return nuevoLocal;
}

function enviarLocal(accion, objLocal) {
    var id = $("#editarL").val();
    if (id == "" || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/local" + accion,
            data: objLocal,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#local").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.local_id,
                        text: data.local_descripcion,
                        selected: true,
                    })
                );
                $("#v_local").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.local_id,
                        text: data.local_descripcion
                    })
                );
                $("#local").val(data.local_id).trigger("change"); //lo selecciona
                $("#textLocal").val("");
                $("#editarLocal").hide();
                limpiar();
                $("#localmodal").modal("toggle");
                $("#form-registrar").modal("show");
                $.notify(
                    {
                        message: "\nLocal Registrado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#form-registrar"),
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
                        spacing: 35,
                    }
                );
            },
            error: function () { },
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarLocal" + accion,
            data: {
                id: id,
                objLocal,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#local").empty();
                $("#v_local").empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/local",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].local_id}">${data[i].local_descripcion}</option>`;
                        }
                        $("#local").append(select);
                        $("#v_local").append(select);
                    },
                    error: function () { },
                });
                $("#local").val(data.local_id).trigger("change"); //lo selecciona
                $("#textLocal").val("");
                $("#editarLocal").hide();
                limpiar();
                $("#localmodal").modal("toggle");
                $("#form-registrar").modal("show");
                $.notify(
                    {
                        message: "\nLocal Modificado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#form-registrar"),
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
                        spacing: 35,
                    }
                );
            },
        });
    }
}
//NIVEL
function agregarnivel() {
    objNivel = datosNivel("POST");
    enviarNivel("", objNivel);
}

function datosNivel(method) {
    nuevoNivel = {
        nivel_descripcion: $("#textNivel").val(),
        _method: method,
    };
    return nuevoNivel;
}

function enviarNivel(accion, objNivel) {
    var id = $("#editarN").val();
    if (id == "" || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/nivel" + accion,
            data: objNivel,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#nivel").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.nivel_id,
                        text: data.nivel_descripcion,
                        selected: true,
                    })
                );
                $("#v_nivel").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.nivel_id,
                        text: data.nivel_descripcion
                    })
                );
                $("#nivel").val(data.nivel_id).trigger("change"); //lo selecciona
                $("#textNivel").val("");
                $("#editarNivel").hide();
                limpiar();
                $("#nivelmodal").modal("toggle");
                $("#form-registrar").modal("show");
                $.notify(
                    {
                        message: "\nNivel Registrado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#form-registrar"),
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
                        spacing: 35,
                    }
                );
            },
            error: function () { },
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarNivel" + accion,
            data: {
                id: id,
                objNivel,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#nivel").empty();
                $("#v_nivel").empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/nivel",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].nivel_id}">${data[i].nivel_descripcion}</option>`;
                        }
                        $("#nivel").append(select);
                        $("#v_nivel").append(select);
                    },
                    error: function () { },
                });
                $("#nivel").val(data.nivel_id).trigger("change"); //lo selecciona
                $("#textNivel").val("");
                $("#editarNivel").hide();
                limpiar();
                $("#nivelmodal").modal("toggle");
                $("#form-registrar").modal("show");
                $.notify(
                    {
                        message: "\nNivel Modificado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#form-registrar"),
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
                        spacing: 35,
                    }
                );
            },
        });
    }
}

//CONTRATO
function agregarContrato() {
    objContrato = datosContrato("POST");
    enviarContrato("", objContrato);
}

function datosContrato(method) {
    nuevoContrato = {
        contrato_descripcion: $("#textContrato").val(),
        _method: method,
    };
    return nuevoContrato;
}

function enviarContrato(accion, objContrato) {
    var id = $("#editarCO").val();
    if (id == "" || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/contrato" + accion,
            data: objContrato,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#contrato").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.contrato_id,
                        text: data.contrato_descripcion,
                        selected: true,
                    })
                );
                $("#v_contrato").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.contrato_id,
                        text: data.contrato_descripcion
                    })
                );
                $("#contrato").val(data.contrato_id).trigger("change"); //lo selecciona
                $("#textContrato").val("");
                $("#editarContrato").hide();
                limpiar();
                $("#contratomodal").modal("toggle");
                $("#form-registrar").modal("show");
                $.notify(
                    {
                        message: "\nContrato Registrado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#form-registrar"),
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
                        spacing: 35,
                    }
                );
            },
            error: function () { },
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarContrato" + accion,
            data: {
                id: id,
                objContrato,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#contrato").empty();
                $("#v_contrato").empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/contrato",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].contrato_id}">${data[i].contrato_descripcion}</option>`;
                        }
                        $("#contrato").append(select);
                        $("#v_contrato").append(select);
                    },
                    error: function () { },
                });
                $("#contrato").val(data.contrato_id).trigger("change"); //lo selecciona
                $("#textContrato").val("");
                $("#editarContrato").hide();
                limpiar();
                $("#contratomodal").modal("toggle");
                $("#form-registrar").modal("show");
                $.notify(
                    {
                        message: "\nContrato Modificado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#form-registrar"),
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
                        spacing: 35,
                    }
                );
            },
        });
    }
}
//CONDICION DE PAGO
function agregarCondicion() {
    objCondicion = datosCondicion("POST");
    enviarCondicion("", objCondicion);
}

function datosCondicion(method) {
    nuevoCondicion = {
        condicion: $("#textCondicion").val(),
        _method: method,
    };
    return nuevoCondicion;
}

function enviarCondicion(accion, objCondicion) {
    var id = $("#editarCP").val();
    if (id == "" || id == undefined) {
        $.ajax({
            type: "POST",
            url: "/registrar/condicion" + accion,
            data: objCondicion,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#condicion").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.id,
                        text: data.condicion,
                        selected: true,
                    })
                );
                $("#v_condicion").append(
                    $("<option>", {
                        //agrego los valores que obtengo de una base de datos
                        value: data.id,
                        text: data.condicion
                    })
                );
                $("#condicion").val(data.id).trigger("change"); //lo selecciona
                $("#textCondicion").val("");
                $("#editarCondicion").hide();
                limpiar();
                $("#condicionmodal").modal("toggle");
                $("#fechasmodal").modal("show");
                $.notify(
                    {
                        message: "\nCondición de Pago Registrado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#fechasmodal"),
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
                        spacing: 35,
                    }
                );
            },
            error: function () { },
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/editarCondicion" + accion,
            data: {
                id: id,
                objCondicion,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            statusCode: {
                /*401: function () {
                    location.reload();
                },*/
                419: function () {
                    location.reload();
                },
            },
            success: function (data) {
                $("#condicion").empty();
                $("#v_condicion").empty();
                var select = "";
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "/condicion",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        select += `<option value="">Seleccionar</option>`;
                        for (var i = 0; i < data.length; i++) {
                            select += `<option class="" value="${data[i].id}">${data[i].condicion}</option>`;
                        }
                        $("#condicion").append(select);
                        $("#v_condicion").append(select);
                    },
                    error: function () { },
                });
                $("#condicion").val(data.id).trigger("change"); //lo selecciona
                $("#textCondicion").val("");
                $("#editarCondicion").hide();
                limpiar();
                $("#condicionmodal").modal("toggle");
                $("#fechasmodal").modal("show");
                $.notify(
                    {
                        message: "\nCondicion de Pago Modificado\n",
                        icon: "admin/images/checked.svg",
                    },
                    {
                        element: $("#fechasmodal"),
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
                        spacing: 35,
                    }
                );
            },
        });
    }
}
//* FECHAS
function agregarFechas() {
    var m_Anio = parseInt($("#m_ano_fecha").val());
    var m_Mes = parseInt($("#m_mes_fecha").val() - 1);
    var m_Dia = parseInt($("#m_dia_fecha").val());
    var m1_VFecha = new Date(m_Anio, m_Mes, m_Dia);
    if (
        m1_VFecha.getFullYear() == m_Anio &&
        m1_VFecha.getMonth() == m_Mes &&
        m1_VFecha.getDate() == m_Dia
    ) {
        $("#m_validFechaC").hide();
    } else {
        $("#m_validFechaC").show();
        return false;
    }
    if (m_Anio != 0 && m_Mes != -1 && m_Dia != 0) {
        fechaI = new Date(m_Anio, m_Mes, m_Dia);
    } else {
        fechaI = "0000-00-00";
    }
    if (!$("#checkboxFechaI").is(":checked")) {
        var mf_Anio = parseInt($("#mf_ano_fecha").val());
        var mf_Mes = parseInt($("#mf_mes_fecha").val() - 1);
        var mf_Dia = parseInt($("#mf_dia_fecha").val());
        var m1f_VFecha = new Date(mf_Anio, mf_Mes, mf_Dia);
        if (
            m1f_VFecha.getFullYear() == mf_Anio &&
            m1f_VFecha.getMonth() == mf_Mes &&
            m1f_VFecha.getDate() == mf_Dia
        ) {
            $("#mf_validFechaC").hide();
        } else {
            $("#mf_validFechaC").show();
            return false;
        }
        //* VALIDACION DE FECHAS
        var momentInicio = moment([m_Anio, m_Mes, m_Dia]);
        var momentFinal = moment([mf_Anio, mf_Mes, mf_Dia]);
        if (!momentInicio.isBefore(momentFinal)) {
            $("#mf_validFechaC").show();
            return false;
        } else {
            $("#mf_validFechaC").hide();
        }
        //* ********************
        if (mf_Anio != 0 && mf_Mes != -1 && mf_Dia != 0) {
            fechaF = new Date(mf_Anio, mf_Mes, mf_Dia);
        } else {
            fechaF = "0000-00-00";
        }
    }
    $("#fechasmodal").modal("toggle");
    $("#form-registrar").modal("show");
}
//CODIGO EMPLEADO
function valorCodigoEmpleado() {
    var numDocumento = $("#numDocumento").val();
    $("#codigoEmpleado").val(numDocumento);
}
//EMPLEADO
/*$('#guardarEmpleado').click(function () {
    objEmpleado = datosPersona("POST");
    enviarEmpleado('', objEmpleado);
});*/

function datosPersona(method) {
    var celularC = "";
    var telefonoC = "";
    if ($("#celular").val() != "") {
        celularC = $("#codigoCelular").val() + $("#celular").val();
    }
    if ($("#telefono").val() != "") {
        telefonoC = $("#codigoTelefono").val() + $("#telefono").val();
    }
    var Anio = parseInt($("#ano_fecha").val()); // Extraemos en año
    var Mes = parseInt($("#mes_fecha").val() - 1); // Extraemos el mes
    var Dia = parseInt($("#dia_fecha").val()); // Extraemos el día

    // Con la función Date() de javascript evaluamos si la fecha existe
    if (Anio != 0 && Mes != -1 && Dia != 0) {
        var VFecha = new Date(Anio, Mes, Dia);
    } else {
        var VFecha = "0000-00-00";
    }

    nuevoEmpleado = {
        nombres: $("#nombres").val(),
        apPaterno: $("#apPaterno").val(),
        apMaterno: $("#apMaterno").val(),
        fechaN: VFecha,
        tipo: $("input[name=tipo]:checked").val(),
        documento: $("#documento").val(),
        numDocumento: $("#numDocumento").val(),
        departamento: $("#departamento").val(),
        provincia: $("#provincia").val(),
        distrito: $("#distrito").val(),
        dep: $("#dep").val(),
        prov: $("#prov").val(),
        dist: $("#dist").val(),
        direccion: $("#direccion").val(),
        celular: celularC,
        telefono: telefonoC,
        correo: $("#email").val(),
        _method: method,
    };
    return nuevoEmpleado;
}

function enviarEmpleado(accion, objEmpleado) {
    var formData = new FormData();
    formData.append("objEmpleado", JSON.stringify(objEmpleado));

    $.ajax({
        type: "POST",
        url: "/empleado/store" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (data) {
            $("#idEmpleado").val(data);
            console.log(data);
            $.notify(
                {
                    message: "\nDatos Guardados.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-registrar"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 1000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function (data, errorThrown) { },
    });
}
//GUARDAR CALENDARIO EN GUARDAR EMPLEADO
function enviarEmpleadoStore(accion, objEmpleado) {
    var formData = new FormData();
    formData.append("objEmpleado", JSON.stringify(objEmpleado));

    $.ajax({
        type: "POST",
        url: "/empleado/storeEmpleado" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (data) {
            $.notify(
                {
                    message: "\nDatos Modificados.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-registrar"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 1000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function (data, errorThrown) { },
    });
}
//GUARDAR DATOS EMPRESARIAL EN GUARDAR EMPLEADO
function datosEmpresaEmpleado(method) {
    var m_Anio = parseInt($("#m_ano_fecha").val());
    var m_Mes = parseInt($("#m_mes_fecha").val() - 1);
    var m_Dia = parseInt($("#m_dia_fecha").val());

    if (m_Anio != 0 && m_Mes != -1 && m_Dia != 0) {
        fechaIn = new Date(m_Anio, m_Mes, m_Dia);
    } else {
        fechaIn = "0000-00-00";
    }
    //////////////////////
    var mf_Anio = parseInt($("#mf_ano_fecha").val());
    var mf_Mes = parseInt($("#mf_mes_fecha").val() - 1);
    var mf_Dia = parseInt($("#mf_dia_fecha").val());

    if (mf_Anio != 0 && mf_Mes != -1 && mf_Dia != 0) {
        fechaFn = new Date(mf_Anio, mf_Mes, mf_Dia);
    } else {
        fechaFn = "0000-00-00";
    }

    nuevoEmpresa = {
        codigoEmpleado: $("#codigoEmpleado").val(),
        cargo: $("#cargo").val(),
        area: $("#area").val(),
        centroc: $("#centroc").val(),
        contrato: $("#contrato").val(),
        fechaI: fechaIn,
        fechaF: fechaFn,
        nivel: $("#nivel").val(),
        local: $("#local").val(),
        condicion: $("#condicion").val(),
        monto: $("#monto").val(),
        idContrato: $("#idContrato").val(),
        _method: method,
    };
    return nuevoEmpresa;
}

function enviarEmpresarialEmpleado(accion, objEmpleado) {
    ////////////////////////////////////

    var formData = new FormData();
    formData.append("objEmpleado", JSON.stringify(objEmpleado));
    $.ajax({
        type: "POST",
        url: "/empleado/storeEmpresarial" + accion,
        data: formData,
        contentType: false,
        processData: false,
        async: false,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (data) {

            $("#idContrato").val(data);
            $.notifyClose();
            $.notify(
                {
                    message: "\nDatos Guardados.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-registrar"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 1000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function (data, errorThrown) { },
    });
    var formData1 = new FormData();

    var num = document.getElementById('exampleFormControlFile1').files.length;

    for (var i = 0; i < num; i++) {
        formData1.append("exampleFormControlFile1[]", document.getElementById('exampleFormControlFile1').files[i]);
    }
    $.ajax({
        type: "POST",
        url: "/empleado/storeDocumento" + accion,
        data: formData1,
        contentType: false,
        processData: false,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (data) {
        },
        error: function (data, errorThrown) { },
    });
    ////////////////////////////////////
}

//GUARDAR FOTO EN GUARDAR EMPLEADO

function enviarFotoEmpleado(accion) {
    var formData = new FormData();
    formData.append("file", $("#file").prop("files")[0]);
    $.ajax({
        type: "POST",
        url: "/empleado/storeFoto" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (data) {
            $.notifyClose();
            $.notify(
                {
                    message: "\nDatos Guardados.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-registrar"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 1000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function (data, errorThrown) { },
    });
}
//GUARDAR CALENDARIO EN GUARDAR EMPLEADO
function datosCalendarioEmpleado(method) {
    nuevoCalendario = {
        idca: $("#selectCalendario").val(),
        _method: method,
    };
    return nuevoCalendario;
}

function enviarCalendarioEmpleado(accion, objEmpleado) {
    var formData = new FormData();
    formData.append("objEmpleado", JSON.stringify(objEmpleado));
    $.ajax({
        type: "POST",
        url: "/empleado/storeCalendario" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (data) {
            $.notifyClose();
            $.notify(
                {
                    message: "\nDatos Guardados.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-registrar"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 1000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function (data, errorThrown) { },
    });
}
//GUARDAR HORARIO EN GUARDAR EMPLEADO
function datosHorarioEmpleado(method) {
    nuevoHorario = {
        idca: $("#selectCalendario").val(),
        _method: method,
    };
    return nuevoHorario;
}

function enviarHorarioEmpleado(accion, objEmpleado) {
    var formData = new FormData();
    formData.append("objEmpleado", JSON.stringify(objEmpleado));
    $.ajax({
        type: "POST",
        url: "/empleado/storeHorario" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (data) {
            $.notifyClose();
            $.notify(
                {
                    message: "\nDatos Guardados.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-registrar"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 1000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function (data, errorThrown) { },
    });
}
//EMPLEADO STOREEMPLEADO
function enviarEmpleadoStore(accion, objEmpleado) {
    var formData = new FormData();
    formData.append("objEmpleado", JSON.stringify(objEmpleado));

    $.ajax({
        type: "POST",
        url: "/empleado/storeEmpleado" + accion,
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            $.notifyClose();
            $.notify(
                {
                    message: "\nDatos Modificados.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-registrar"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 1000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function (data, errorThrown) { },
    });
}
//EMPLEADO ACTUALIZAR
$("#checkboxFechaIE").on("click", function () {
    if ($("#checkboxFechaIE").is(":checked")) {
        $("#m_dia_fechaFE").val("0");
        $("#m_mes_fechaFE").val("0");
        $("#m_ano_fechaFE").val("0");
        $("#ocultarFechaE").hide();
    } else {
        $("#ocultarFechaE").show();
    }
});
// DATOS PERSONALES
function datosPersonaA(method) {
    var celularC = "";
    var telefonoC = "";
    if ($("#v_celular").val() != "") {
        celularC = $("#v_codigoCelular").val() + $("#v_celular").val();
    }
    if ($("#v_telefono").val() != "") {
        telefonoC = $("#v_codigoTelefono").val() + $("#v_telefono").val();
    }
    ////////////////////////////////////
    var v_Anio = parseInt($("#v_ano_fecha").val());
    var v_Mes = parseInt($("#v_mes_fecha").val() - 1);
    var v_Dia = parseInt($("#v_dia_fecha").val());

    if (v_Anio != 0 && v_Mes != -1 && v_Dia != 0) {
        var v_VFecha = new Date(v_Anio, v_Mes, v_Dia);
    } else {
        var v_VFecha = "0000-00-00";
    }
    nuevoEmpleadoA = {
        nombres_v: $("#v_nombres").val(),
        apPaterno_v: $("#v_apPaterno").val(),
        apMaterno_v: $("#v_apMaterno").val(),
        fechaN_v: v_VFecha,
        tipo_v: $("input:radio[name=v_tipo]:checked").val(),
        departamento_v: $("#v_departamento").val(),
        provincia_v: $("#v_provincia").val(),
        distrito_v: $("#v_distrito").val(),
        dep_v: $("#v_dep").val(),
        prov_v: $("#v_prov").val(),
        dist_v: $("#v_dist").val(),
        direccion_v: $("#v_direccion").val(),
        celular_v: celularC,
        telefono_v: telefonoC,
        correo_v: $("#v_email").val(),
        _method: method,
    };
    return nuevoEmpleadoA;
}

function actualizarEmpleado(accion, objEmpleadoA) {
    var formDataA = new FormData();
    formDataA.append("file", $("#file2").prop("files")[0]);
    formDataA.append("objEmpleadoA", JSON.stringify(objEmpleadoA));

    $.ajax({
        type: "POST",
        url: "/empleadoA" + accion,
        data: formDataA,
        contentType: false,
        processData: false,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (msg) {
            $.notifyClose();
            $.notify(
                {
                    message: "\nDatos Actualizado.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-ver"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 1000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function (data, errorThrown) {
            alert("Hay un error");

        },
    });
}
// DATOS EMPRESARIALES
function datosEmpresarialA(method) {
    //////////////////////////////////////
    var v_AnioIE = parseInt($("#m_ano_fechaIE").val());
    var v_MesIE = parseInt($("#m_mes_fechaIE").val() - 1);
    var v_DiaIE = parseInt($("#m_dia_fechaIE").val());

    if (v_AnioIE != 0 && v_MesIE != -1 && v_DiaIE != 0) {
        var v_VFechaIE = new Date(v_AnioIE, v_MesIE, v_DiaIE);
    } else {
        var v_VFechaIE = "0000-00-00";
    }
    var v_AnioFE = parseInt($("#m_ano_fechaFE").val());
    var v_MesFE = parseInt($("#m_mes_fechaFE").val() - 1);
    var v_DiaFE = parseInt($("#m_dia_fechaFE").val());

    if (v_AnioFE != 0 && v_MesFE != -1 && v_DiaFE != 0) {
        var v_VFechaFE = new Date(v_AnioFE, v_MesFE, v_DiaFE);
    } else {
        var v_VFechaFE = "0000-00-00";
    }
    /////////////////////////////////////////////

    nuevoEmpleadoEA = {
        codigoEmpleado_v: $("#v_codigoEmpleado").val(),
        cargo_v: $("#v_cargo").val(),
        centroc_v: $("#v_centroc").val(),
        contrato_v: $("#v_contrato").val(),
        idContrato_v: $("#v_idContrato").val(),
        monto_v: $("#v_monto").val(),
        condicion_v: $("#v_condicion").val(),
        fechaI_v: v_VFechaIE,
        fechaF_v: v_VFechaFE,
        nivel_v: $("#v_nivel").val(),
        local_v: $("#v_local").val(),
        area_v: $("#v_area").val(),
        _method: method,
    };
    return nuevoEmpleadoEA;
}
function actualizarEmpleadoEmpresarial(accion, objEmpleadoA) {
    var idcontratoEdit=$('#v_contratoTipoH').val();
    var idcontratoNuevo=$('#v_contrato').val();
    var formDataA = new FormData();
    formDataA.append("objEmpleadoA", JSON.stringify(objEmpleadoA));
    $.ajax({
        type: "POST",
        url: "/empleadoAE" + accion,
        data: formDataA,
        contentType: false,
        processData: false,
        async: false,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (msg) {
            $.notifyClose();
            $.notify(
                {
                    message: "\nDatos Actualizado.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-ver"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 1000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function (data, errorThrown) {
            alert("Hay un error");

        },
    });

    var formData1 = new FormData();

    var num = document.getElementById('exampleFormControlFile1_ed').files.length;

    for (var i = 0; i < num; i++) {
        formData1.append("exampleFormControlFile1_ed[]", document.getElementById('exampleFormControlFile1_ed').files[i]);
    }
    $.ajax({
        type: "POST",
        url: "/empleado/storeDocumentoEdi" + accion+"/" + idcontratoEdit + "/" +idcontratoNuevo,
        data: formData1,
        contentType: false,
        processData: false,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (data) {
        },
        error: function (data, errorThrown) { },
    });
    ////////////////////////////////////
}
// FOTO
function actualizarEmpleadoFoto(accion) {
    var formDataA = new FormData();
    formDataA.append("file", $("#file2").prop("files")[0]);
    $.ajax({
        type: "POST",
        url: "/empleadoAF" + accion,
        data: formDataA,
        contentType: false,
        processData: false,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (msg) {
            $.notifyClose();
            $.notify(
                {
                    message: "\nDatos Actualizado.",
                    icon: "admin/images/checked.svg",
                },
                {
                    element: $("#form-ver"),
                    position: "fixed",
                    icon_type: "image",
                    newest_on_top: true,
                    delay: 1000,
                    template:
                        '<div data-notify="container" class="col-xs-8 col-sm-2 text-center alert" style="background-color: #dff0d8;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#3c763d;" data-notify="message">{2}</span>' +
                        "</div>",
                    spacing: 35,
                }
            );
        },
        error: function (data, errorThrown) {
            alert("Hay un error");

        },
    });
}
///ELIMINAR EMPLEADO

//abrir nuevo form
function abrirnuevo() {
    $("#form-ver").hide();
    $("#tablaEmpleado tbody tr").removeClass("selected");
    $("#form-registrar").smartWizard("reset");
    $('input[type="text"]').val("");
    $("input:radio[name=tipo]:checked").prop("checked", false);
    $('input[type="date"]').val("");
    $('input[type="file"]').val("");
    $("select").val("");
    $("#form-registrar").show();
    $("#selectCalendario").val("Asignar calendario");
    $("#selectHorario").val("Seleccionar horario");
    $("#selectHorario").trigger("change");
}

//eliminar foto
function cargarFile2() {
    $("#file2").fileinput({
        allowedFileExtensions: ["jpg", "png", "gif"],
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
                urlFoto +
                " style='max-width:200px; max-height:200px; height:auto; width:auto'>",
            ],
            initialPreviewConfig: [
                {
                    width: "200px",
                    height: "200px",
                    url: "/eliminarFoto/" + id_empleado,
                    showDelete: true,
                    key: id_empleado,
                },
            ],
        }),
        language: "es",
        deleteExtraData: {
            _token: $("#csrf_token").val(),
        },
        showBrowse: false,
        browseOnZoneClick: true,
        theme: "fa",
        fileActionSettings: {
            showDrag: false,
            showZoom: false,
        },
    });
}
//********************** */
$("#documento").on("change", function () {
    $("#form-registrar :input").attr("disabled", false);
    if ($("#documento").val() == 1) {
        $("#numDocumento").attr("maxlength", "8");
    }
    if ($("#documento").val() == 2) {
        $("#numDocumento").attr("maxlength", "8");
    }
    if ($("#documento").val() == 3) {
        $("#numDocumento").attr("maxlength", "12");
    }
});
$("#telefono").attr("maxlength", "6");
$("#v_telefono").attr("maxlength", "6");
$("#smartwizardVer :input").attr("disabled", true);
$("#form-registrar :input").prop("disabled", true);
$("#documento").attr("disabled", false);
$("#cerrarModalEmpleado").attr("disabled", false);
$("#cerrarE").attr("disabled", false);
$("#cerrarEd").attr("disabled", false);
$("#documento").on("change", function () {
    $("#form-registrar :input").attr("disabled", false);
    if ($("#documento").val() == 1) {
        $("#numDocumento").attr("maxlength", "8");
    }
    if ($("#documento").val() == 2) {
        $("#numDocumento").attr("maxlength", "8");
    }
    if ($("#documento").val() == 3) {
        $("#numDocumento").attr("maxlength", "12");
    }
});
$("#formContrato :input").prop("disabled", true);
$("#condicion").prop("disabled", false);
$("#condicion").on("change", function () {
    $("#formContrato :input").prop("disabled", false);
});
$("#formNuevoE").click(function () {
    fechaActual = new Date();
    $("#m_ano_fecha").val(fechaActual.getFullYear());
    $("#m_mes_fecha").val(fechaActual.getMonth() + 1);
    $("#m_dia_fecha").val(fechaActual.getDate());
    $("#idEmpleado").val("");
    $("#dia_fecha").val("0");
    $("#mes_fecha").val("0");
    $("#ano_fecha").val("0");
    $("#mf_dia_fecha").show();
    $("#mf_mes_fecha").show();
    $("#mf_ano_fecha").show();
    $("#idContrato").val("");
    calendarioInv();
    $("#calendarInv").show();
    $("#calendar").hide();
    $("#opborrar").hide();
    $("#detallehorario").empty();
    $("#calendar2").hide();
    $("#FinalizarEmpleado").hide();
    $("#estadoPR").val("false");
    $("#estadoPE").val("false");
    $("#estadoPF").val("false");
    $("#estadoPC").val("false");
    $("#estadoPH").val("false");
    $("#estadoP").val("false");
    $("#estadoE").val("false");
    $("#estadoCond").val("false");
    $("#estadoF").val("false");
    $.get("/empleado/vaciarcalend", {}, function (data, status) {
        $("#form-registrar").modal();
        $("#cerrarModalEmpleado").attr("disabled", false);
    });
});
$("#formNuevoEd").click(function () {
    $("#FinalizarEmpleadoEditar").hide();
    $("#estadoPR").val("false");
    $("#estadoPE").val("false");
    $("#estadoPF").val("false");
    $("#estadoPC").val("false");
    $("#estadoPH").val("false");
    $("#estadoP").val("false");
    $("#estadoE").val("false");
    $("#estadoCond").val("false");
    $("#estadoF").val("false");
    $.get("/empleado/vaciarcalend", {}, function (data, status) {
        $("#form-ver").modal();
    });
});

$("#formNuevoEd").hide();
$("#formNuevoEl").hide();
$("#cerrarE").click(function () {
    $("#smartwizard1").smartWizard("reset");
    $("#formNuevoEd").hide();
    $("#formNuevoEl").hide();
    $("#selectCalendario").val("Asignar calendario");
    $("#selectHorario").val("Seleccionar horario");
    $("#selectHorario").trigger("change");
});
$("#cerrarEd").click(function () {
    RefreshTablaEmpleado();
    $("#smartwizard1").smartWizard("reset");
    $("#formNuevoEd").hide();
    $("#formNuevoEl").hide();
    $("#navActualizar").hide();
    $("#m_dia_fechaIE").val("0");
    $("#m_mes_fechaIE").val("0");
    $("#m_ano_fechaIE").val("0");

    $("#m_dia_fechaFE").val("0");
    $("#m_mes_fechaFE").val("0");
    $("#m_ano_fechaFE").val("0");
    $("#checkboxFechaIE").prop("checked", false);
    //************* */
    $("#v_validApPaterno").hide();
    $("#v_validNumDocumento").hide();
    $("#v_validApMaterno").hide();
    $("#v_validNombres").hide();
    $("#v_validCorreo").hide();
    $("#v_emailR").hide();
    $("#v_validCel").hide();
    $('input[type="date"]').val("");
    $('input[type="file"]').val("");
    $('input[type="email"]').val("");
    $('input[type="number"]').val("");
    $("#v_departamento").val("").trigger("change");
    $("#v_dep").val("").trigger("change");
    $("#v_prov").empty();
    $("#v_provincia").empty();
    $("#v_prov").append(`<option value="">Provincia</option>`);
    $("#v_provincia").append(`<option value="">Provincia</option>`);
    $("#v_dist").empty();
    $("#v_distrito").empty();
    $("#v_dist").append(`<option value="">Distrito</option>`);
    $("#v_distrito").append(`<<option value="">Distrito</option>`);
    $("#v_cargo").val("").trigger("change");
    $("#v_contrato").val("").trigger("change");
    $("#v_area").val("").trigger("change");
    $("#v_nivel").val("").trigger("change");
    $("#v_centroc").val("").trigger("change");
    $("#v_local").val("").trigger("change");
    $("#selectHorario_ed").val("Seleccionar horario");
    $("#selectHorario_ed").trigger("change");
    $("#v_codigoCelular").val("+51");
    $("#v_codigoTelefono").val("01");
    limpiar();
    $("#selectCalendario").val("Asignar calendario");
    $("#selectHorario").val("Seleccionar horario");
    $("#selectHorario").trigger("change");
    $("#estadoP").val("false");
    $("#estadoE").val("false");
    $("#estadoCond").val("false");
    $("#estadoF").val("false");
});
$("#cerrarModalEmpleado").click(function () {
    RefreshTablaEmpleado();
    $("#formNuevoEd").hide();
    $("#formNuevoEl").hide();
    $("#smartwizard").smartWizard("reset");
    $('input[type="text"]').val("");
    $("#idContrato").val("");
    $("#condicion").val("");
    $("input:radio[name=tipo]:checked").prop("checked", false);
    $('input[type="date"]').val("");
    $('input[type="file"]').val("");
    $('input[type="email"]').val("");
    $('input[type="number"]').val("");
    $("#documento").val("").trigger("change");
    $("#departamento").val("").trigger("change");
    $("#dep").val("").trigger("change");
    $("#prov").empty();
    $("#provincia").empty();
    $("#prov").append(`<option value="">Provincia</option>`);
    $("#provincia").append(`<option value="">Provincia</option>`);
    $("#dist").empty();
    $("#distrito").empty();
    $("#dist").append(`<option value="">Distrito</option>`);
    $("#distrito").append(`<<option value="">Distrito</option>`);
    $("#cargo").val("").trigger("change");
    $("#contrato").val("").trigger("change");
    $("#area").val("").trigger("change");
    $("#nivel").val("").trigger("change");
    $("#centroc").val("").trigger("change");
    $("#local").val("").trigger("change");
    $("#form-registrar :input").prop("disabled", true);
    $("#documento").attr("disabled", false);
    $("#cerrarMoadalEmpleado").attr("disabled", false);
    $("#checkboxFechaI").prop("checked", false);
    $("#codigoCelular").val("+51");
    $("#codigoTelefono").val("01");
    //********** */
    $("#v_emailR").hide();
    $("#validDocumento").hide();
    $("#validApPaterno").hide();
    $("#validNumDocumento").hide();
    $("#validApMaterno").hide();
    $("#validFechaN").hide();
    $("#validNombres").hide();
    $("#validFechaC").hide();
    $("#validGenero").hide();
    $("#validCel").hide();
    $("#emailR").hide();
    $("#validCorreo").hide();
    $("#detalleContrato").hide();
    $("#editarArea").hide();
    $("#form-registrar").modal("toggle");
    limpiar();
    $("#selectCalendario").val("Asignar calendario");
    $("#selectHorario").val("Seleccionar horario");
    $("#selectHorario").trigger("change");
    $("#estadoPR").val("false");
    $("#estadoPE").val("false");
    $("#estadoPF").val("false");
    $("#estadoPC").val("false");
    $("#estadoPH").val("false");
});

function cerrarVer() {
    $("#smartwizardVer").smartWizard("reset");
    $('#smartwizardVer :input[type="text"]').val("");
}
//*********************/
$("#numR").hide();
$("#emailR").hide();
$("#v_emailR").hide();
$("#validDocumento").hide();
$("#validApPaterno").hide();
$("#validNumDocumento").hide();
$("#validApMaterno").hide();
$("#validCorreo").hide();
$("#validNombres").hide();
$("#validGenero").hide();
$("#validFechaC").hide();
//************* */
$("#v_validApPaterno").hide();
$("#v_validNumDocumento").hide();
$("#v_validApMaterno").hide();
$("#v_validNombres").hide();
$("#v_validCorreo").hide();
$("#v_validFechaC").hide();
$("#v_validFechaC").hide();
$("#detalleContrato").hide();
$("#editarArea").hide();
$("#editarCargo").hide();
$("#editarCentro").hide();
$("#editarLocal").hide();
$("#editarNivel").hide();
$("#editarContrato").hide();
$("#editarCondicion").hide();
$("#editarAreaA").hide();
$("#editarCargoA").hide();
$("#editarCentroA").hide();
$("#editarLocalA").hide();
$("#editarNivelA").hide();
$("#editarContratoA").hide();
$("#editarCondicionA").hide();
$("#validCel").hide();
$("#v_validCel").hide();
$("#v_validGenero").hide();

function FinalizarEmpleado() {
    RefreshTablaEmpleado();
    $('input[type="text"]').val("");
    $("input:radio[name=tipo]:checked").prop("checked", false);
    $('input[type="date"]').val("");
    $('input[type="file"]').val("");
    $('input[type="email"]').val("");
    $('input[type="number"]').val("");
    $("#idContrato").val("");
    $("#condicion").val("");
    $("#documento").val("").trigger("change");
    $("#departamento").val("").trigger("change");
    $("#dep").val("").trigger("change");
    $("#prov").empty();
    $("#provincia").empty();
    $("#prov").append(`<option value="">Provincia</option>`);
    $("#provincia").append(`<option value="">Provincia</option>`);
    $("#dist").empty();
    $("#distrito").empty();
    $("#dist").append(`<option value="">Distrito</option>`);
    $("#distrito").append(`<<option value="">Distrito</option>`);
    $("#file").val("");
    $("#file").fileinput("refresh");
    $("#codigoCelular").val("+51");
    $("#codigoTelefono").val("01");
    $("#cargo").val("").trigger("change");
    $("#contrato").val("").trigger("change");
    $("#area").val("").trigger("change");
    $("#nivel").val("").trigger("change");
    $("#centroc").val("").trigger("change");
    $("#local").val("").trigger("change");
    $("#form-registrar :input").prop("disabled", true);
    $("#documento").attr("disabled", false);
    $("#cerrarMoadalEmpleado").attr("disabled", false);

    $("#mf_dia_fecha").val("0");
    $("#mf_mes_fecha").val("0");
    $("#mf_ano_fecha").val("0");
    $("#detalleContrato").hide();
    $("#checkboxFechaI").prop("checked", false);
    $("#selectCalendario").val("Asignar calendario");
    $("#selectHorario").val("Seleccionar horario");
    $("#selectHorario").trigger("change");
    $("#tbodyDispositivo").empty();
    $("#smartwizard").smartWizard("reset");
    $("#estadoPR").val("false");
    $("#estadoPE").val("false");
    $("#estadoPF").val("false");
    $("#estadoPC").val("false");
    $("#estadoPH").val("false");
    $("#form-registrar").modal("toggle");
}
// *******************************************************
$("#persona-step-1").on("keyup change", function () {
    $("#estadoP").val("true");

});
$("#swE-default-step-2").on("keyup change", function () {
    $("#estadoE").val("true");
    console.log($("#estadoE").val());

});
$("#formContrato_v").on("keyup change", function () {
    $("#estadoCond").val("true");

});
$("#swF-default-step-3").on("keyup change", function () {
    $("#estadoF").val("true");

});
$("#file2").on("fileselect", function () {
    $("#estadoF").val("true");

});
$("#file2").on("fileclear", function () {
    $("#estadoF").val("true");

});
$("#file2").on("filedeleted", function () {
    $("#estadoF").val("true");

});
$("#sw-default-step-1").on("keyup change", function () {
    $("#estadoPR").val("true");

});
$("#sw-default-step-2").on("keyup change click", function () {
    $("#estadoPE").val("true");
    console.log($("#estadoPE").val());

});
$("#sw-default-step-3").on("keyup change", function () {
    $("#estadoPF").val("true");

});
$("#sw-default-step-4").on("keyup change", function () {
    $("#estadoPC").val("true");

});
$("#sw-default-step-5").on("keyup change", function () {
    $("#estadoPH").val("true");

});
//************************Editar en los modal de agregar */
//*******AREA***/
$("#buscarArea").on("click", function () {
    $("#editarArea").empty();
    var container = $("#editarArea");
    var select = "";
    $.ajax({
        type: "GET",
        url: "/area",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            select += `<select class="form-control" name="area" id="editarA">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].area_id}">${data[i].area_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $("#editarA").on("change", function () {
                var id = $("#editarA").val();
                $.ajax({
                    type: "GET",
                    url: "/buscarArea",
                    data: {
                        id: id,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $("#textArea").val(data);
                    },
                    error: function () {
                        $("#textArea").val("");
                    },
                });
            });
        },
        error: function () { },
    });
    $("#editarArea").show();
});
//******CARGO*****/
$("#buscarCargo").on("click", function () {
    $("#editarCargo").empty();
    var container = $("#editarCargo");
    var select = "";
    $.ajax({
        type: "GET",
        url: "/cargo",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            select += `<select class="form-control" name="cargo" id="editarC">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].cargo_id}">${data[i].cargo_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $("#editarC").on("change", function () {
                var id = $("#editarC").val();
                $.ajax({
                    type: "GET",
                    url: "/buscarCargo",
                    data: {
                        id: id,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $("#textCargo").val(data);
                    },
                    error: function () {
                        $("#textCargo").val("");
                    },
                });
            });
        },
        error: function () { },
    });
    $("#editarCargo").show();
});
//******CENTRO***/
$("#buscarCentro").on("click", function () {
    $("#editarCentro").empty();
    var container = $("#editarCentro");
    var select = "";
    $.ajax({
        type: "GET",
        url: "/centro",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            select += `<select class="form-control" name="centro" id="editarCC">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].centroC_id}">${data[i].centroC_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $("#editarCC").on("change", function () {
                var id = $("#editarCC").val();
                $.ajax({
                    type: "GET",
                    url: "/buscarCentro",
                    data: {
                        id: id,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $("#textCentro").val(data);
                    },
                    error: function () {
                        $("#textCentro").val("");
                    },
                });
            });
        },
        error: function () { },
    });
    $("#editarCentro").show();
});
//******LOCAL***/
$("#buscarLocal").on("click", function () {
    $("#editarLocal").empty();
    var container = $("#editarLocal");
    var select = "";
    $.ajax({
        type: "GET",
        url: "/local",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            select += `<select class="form-control" name="Local" id="editarL">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].local_id}">${data[i].local_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $("#editarL").on("change", function () {
                var id = $("#editarL").val();
                $.ajax({
                    type: "GET",
                    url: "/buscarLocal",
                    data: {
                        id: id,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $("#textLocal").val(data);
                    },
                    error: function () {
                        $("#textLocal").val("");
                    },
                });
            });
        },
        error: function () { },
    });
    $("#editarLocal").show();
});
//******NIVEL***/
$("#buscarNivel").on("click", function () {
    $("#editarNivel").empty();
    var container = $("#editarNivel");
    var select = "";
    $.ajax({
        type: "GET",
        url: "/nivel",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            select += `<select class="form-control" name="nivel" id="editarN">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].nivel_id}">${data[i].nivel_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $("#editarN").on("change", function () {
                var id = $("#editarN").val();
                $.ajax({
                    type: "GET",
                    url: "/buscarNivel",
                    data: {
                        id: id,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $("#textNivel").val(data);
                    },
                    error: function () {
                        $("#textNivel").val("");
                    },
                });
            });
        },
        error: function () { },
    });
    $("#editarNivel").show();
});
//******CONTRATO***/
$("#buscarContrato").on("click", function () {
    $("#editarContrato").empty();
    var container = $("#editarContrato");
    var select = "";
    $.ajax({
        type: "GET",
        url: "/contrato",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            select += `<select class="form-control" name="contrato" id="editarCO">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].contrato_id}">${data[i].contrato_descripcion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $("#editarCO").on("change", function () {
                var id = $("#editarCO").val();
                $.ajax({
                    type: "GET",
                    url: "/buscarContrato",
                    data: {
                        id: id,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $("#textContrato").val(data);
                    },
                    error: function () {
                        $("#textContrato").val("");
                    },
                });
            });
        },
        error: function () { },
    });
    $("#editarContrato").show();
});
//******CONDICION DE PAGO***/
$("#buscarCondicion").on("click", function () {
    $("#editarCondicion").empty();
    var container = $("#editarCondicion");
    var select = "";
    $.ajax({
        type: "GET",
        url: "/condicion",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            select += `<select class="form-control" name="condicion" id="editarCP">
            <option value="">Seleccionar</option>`;
            for (var i = 0; i < data.length; i++) {
                select += `<option class="" value="${data[i].id}">${data[i].condicion}</option>`;
            }
            select += `</select>`;
            container.append(select);
            $("#editarCP").on("change", function () {
                var id = $("#editarCP").val();
                $.ajax({
                    type: "GET",
                    url: "/buscarCondicion",
                    data: {
                        id: id,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $("#textCondicion").val(data);
                    },
                    error: function () {
                        $("#textCondicion").val("");
                    },
                });
            });
        },
        error: function () { },
    });
    $("#editarCondicion").show();
});
//*****LIMPIAR***/
function limpiar() {
    $("#editarArea").hide();
    $("#editarCargo").hide();
    $("#editarCentro").hide();
    $("#editarLocal").hide();
    $("#editarNivel").hide();
    $("#editarContrato").hide();
    $("#editarCondicion").hide();
    $("#textArea").val("");
    $("#textCargo").val("");
    $("#textCentro").val("");
    $("#textLocal").val("");
    $("#textNivel").val("");
    $("#textContrato").val("");
    $("#textCondicion").val("");
    $("#editarA").val("");
    $("#editarC").val("");
    $("#editarCC").val("");
    $("#editarL").val("");
    $("#editarN").val("");
    $("#editarCO").val("");
    $("#editarCP").val("");
}

function vaciardFeria() {
    fmes = calendar.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        title: "Eliminar feriados",
        message:
            "¿Esta seguro que desea eliminar dias feriados del calendario?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/empleado/vaciardfTem",
                    data: {
                        mescale,
                        aniocalen,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        calendar.refetchEvents();
                        calendar2.refetchEvents();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}
/////////////////
function vaciarddescanso() {
    fmes = calendar.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        title: "Eliminar descansos",
        message:
            "¿Esta seguro que desea eliminar dias de descanso del calendario?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/empleado/vaciardescansoTem",
                    data: {
                        mescale,
                        aniocalen,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        calendar.refetchEvents();
                        calendar2.refetchEvents();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}
//////////////
function vaciardlabTem() {
    bootbox.confirm({
        title: "Eliminar dias laborales",
        message:
            "¿Esta seguro que desea eliminar dias laborales del calendario?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.get("/empleado/vaciardlabTem", {}, function (data, status) {
                    calendario();
                    calendario2();
                });
            }
        },
    });
}

function vaciardNlabTem() {
    fmes = calendar.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        title: "Eliminar dias no laborales",
        message:
            "¿Esta seguro que desea eliminar dias no laborales del calendario?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/empleado/vaciardNlabTem",
                    data: {
                        mescale,
                        aniocalen,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        calendar.refetchEvents();
                        calendar2.refetchEvents();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}

function vaciardIncidTem() {
    fmes = calendar.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        title: "Eliminar incidencias",
        message:
            "¿Esta seguro que desea eliminar todas las incidencias del calendario?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/empleado/vaciardIncidTem",
                    data: {
                        mescale,
                        aniocalen,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        calendar.refetchEvents();
                        calendar2.refetchEvents();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}
////////////////////////////////////////////////////////////
function diaferiadoRe_ed() {
    $("#calendarioAsignar_ed").modal("hide");
    (title = $("#nombreFeriado_ed").val()),
        (color = "#e6bdbd"),
        (textColor = "#775555"),
        (start = $("#pruebaStar_ed").val());
    end = $("#pruebaEnd_ed").val();
    tipo = 2;
    var idempleado = $("#idempleado").val();
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
            idempleado,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        statusCode: {
            /*401: function () {
                location.reload();
            },*/
            419: function () {
                location.reload();
            },
        },
        success: function (msg) {
            $("#myModalFeriado_ed").modal("hide");
            calendarioedit.refetchEvents();
            calendar2_ed.refetchEvents();


        },
        error: function () { },
    });
}
//////////////////////////////////////////////////////////
function vaciardFeriaBD() {
    var idempleado = $("#idempleado").val();
    fmes = calendarioedit.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        title: "Eliminar dias feriados",
        message:
            "¿Esta seguro que desea eliminar dias feriados del calendario?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/empleado/vaciarFerBD",
                    data: {
                        mescale,
                        aniocalen,
                        idempleado,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        calendarioedit.refetchEvents();
                        calendar2_ed.refetchEvents();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}

function vaciarddescansoBD() {
    var idempleado = $("#idempleado").val();
    fmes = calendarioedit.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        title: "Eliminar dias de descanso",
        message:
            "¿Esta seguro que desea eliminar dias de descanso  del calendario?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/empleado/vaciarFdescansoBD",
                    data: {
                        mescale,
                        aniocalen,
                        idempleado,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        calendarioedit.refetchEvents();
                        calendar2_ed.refetchEvents();
                        $.each(data, function (index, value) {
                            var a = moment(value.end);
                            c = a._i;
                            var b = moment(value.start);
                            d = b._i;
                            if (a.diff(b, 'days') > 1) {
                                $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(a).subtract(1, 'day').format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffffff");
                            }
                            $("div.fc-bg > table > tbody > tr > td.fc-day.fc-widget-content[data-date='" + moment(value.start).format('YYYY-MM-DD') + "']").css("backgroundColor", "#ffffff");
                        });
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}

function vaciarNlabBD() {
    var idempleado = $("#idempleado").val();
    fmes = calendarioedit.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        title: "Eliminar dias no laborales",
        message:
            "¿Esta seguro que desea eliminar dias no laborales del calendario?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/empleado/vaciardnlaBD",
                    data: {
                        mescale,
                        aniocalen,
                        idempleado,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        calendarioedit.refetchEvents();
                        calendar2_ed.refetchEvents();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}

function vaciardIncidBD() {
    var idempleado = $("#idempleado").val();
    fmes = calendarioedit.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        title: "Eliminar incidencias",
        message: "¿Esta seguro que desea eliminar incidencias del calendario?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/empleado/vaciarincidelaBD",
                    data: {
                        mescale,
                        aniocalen,
                        idempleado,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        calendarioedit.refetchEvents();
                        calendar2_ed.refetchEvents();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}

function eliminarhorariosBD() {
    var idempleado = $("#idempleado").val();
    fmes = calendar2_ed.getDate();
    mescale = fmes.getMonth() + 1;
    aniocalen = fmes.getFullYear();
    bootbox.confirm({
        title: "Eliminar horarios",
        message: "¿Esta seguro que desea eliminar horarios del calendario?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "post",
                    url: "/empleado/eliminarhorariosBD",
                    data: {
                        mescale,
                        aniocalen,
                        idempleado,
                    },
                    statusCode: {
                        419: function () {
                            location.reload();
                        },
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        calendarioedit.refetchEvents();
                        calendar2_ed.refetchEvents();
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            }
        },
    });
}
////////////////////////////////
$("#selectCalendario_edit3").change(function () {
    var antSe = $("#idselect3").val();
    bootbox.confirm({
        title: "Alerta",
        message:
            "Al cambiar de calendario se borrará horarios actuales, ¿Confirmar?",
        buttons: {
            confirm: {
                label: "Aceptar",
                className: "btn-success",
            },
            cancel: {
                label: "Cancelar",
                className: "btn-light",
            },
        },
        callback: function (result) {
            if (result == true) {
                var idempleado = $("#idempleado").val();
                var idcalendario = $("#selectCalendario_edit3").val();
                $("#idselect3").val(idcalendario);

                $.ajax({
                    type: "post",
                    url: "/empleado/vaciarbdempleado",
                    data: {
                        idempleado,
                    },
                    async: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (data) {
                        $("#calendar_ed").hide();
                        $.ajax({
                            type: "post",
                            url: "/empleado/vaciarhorariosBD",
                            data: {
                                idempleado,
                            },
                            headers: {
                                "X-CSRF-TOKEN": $(
                                    'meta[name="csrf-token"]'
                                ).attr("content"),
                            },
                        });
                        $.ajax({
                            type: "POST",
                            url: "/empleado/calendarioEmpleado",
                            data: {
                                idcalendario,
                                idempleado,
                            },
                            headers: {
                                "X-CSRF-TOKEN": $(
                                    'meta[name="csrf-token"]'
                                ).attr("content"),
                            },
                            statusCode: {
                                419: function () {
                                    location.reload();
                                },
                            },
                            success: function (data) {
                                calendarioedit.refetchEvents();
                                calendar2_ed.refetchEvents();
                                $("#calendar_ed").show();
                            },
                            error: function () { },
                        });
                    },
                    error: function (data) {
                        alert("Ocurrio un error");
                    },
                });
            } else {
                $("#selectCalendario_edit3").val(antSe);
            }
        },
    });
});
$(function () {
    $(document).on('change', '#horaF_ed', function (event) {
        let horaF = $('#horaF_ed').val();
        let horaI = $('#horaI_ed').val();

        if (horaF < horaI) {
            $('#divOtrodia_ed').show();
            $('#horaOblig_ed').prop("disabled", false);
            $('#horaOblig_ed').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
            $('#horaOblig_ed').val('');
            event.stopPropagation();
        } else {
            var dateDesde = newDate(horaI.split(":"));
            var dateHasta = newDate(horaF.split(":"));

            var minutos = (dateHasta - dateDesde) / 1000 / 60;
            var horas = Math.floor(minutos / 60);
            minutos = minutos % 60;
            /*  console.log(prefijo(horas) + ':' + prefijo(minutos)); */
            $('#horaOblig_ed').prop("disabled", false);

            if ($('#horaOblig_ed').val() == null || $('#horaOblig_ed').val() == '') {
                $('#horaOblig_ed').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultHour: "8"
                });
                $('#horaOblig_ed').val("08:00");

            }

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
            $('#horaOblig_ed').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
            $('#horaOblig_ed').prop("disabled", false);
            $('#horaOblig_ed').val('');
            event.stopPropagation();
        } else {
            var dateDesde = newDate(horaI.split(":"));
            var dateHasta = newDate(horaF.split(":"));

            var minutos = (dateHasta - dateDesde) / 1000 / 60;
            var horas = Math.floor(minutos / 60);
            minutos = minutos % 60;

            $('#horaOblig_ed').prop("disabled", false);
            /* $('#horaOblig').val(prefijo(horas)); */
            if ($('#horaOblig_ed').val() == null || $('#horaOblig_ed').val() == '') {
                $('#horaOblig_ed').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultHour: "8"
                });
                $('#horaOblig_ed').val("08:00");

            }
            $('#divOtrodia_ed').hide();
        }

    });
});
$(function () {
    $(document).on('change', '#horaF', function (event) {
        let horaF = $('#horaF').val();
        let horaI = $('#horaI').val();

        if (horaF < horaI) {
            $('#divOtrodia').show();
            $('#horaOblig').prop("disabled", false);
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

            var minutos = (dateHasta - dateDesde) / 1000 / 60;
            var horas = Math.floor(minutos / 60);
            minutos = minutos % 60;
            console.log(prefijo(horas) + ':' + prefijo(minutos));
            $('#horaOblig').prop("disabled", false);

            if ($('#horaOblig').val() == null || $('#horaOblig').val() == '') {
                $('#horaOblig').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultHour: "8"
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
            $('#horaOblig').prop("disabled", false);
            $('#horaOblig').val('');
            event.stopPropagation();
        } else {
            var dateDesde = newDate(horaI.split(":"));
            var dateHasta = newDate(horaF.split(":"));

            var minutos = (dateHasta - dateDesde) / 1000 / 60;
            var horas = Math.floor(minutos / 60);
            minutos = minutos % 60;
            console.log(prefijo(horas) + ':' + prefijo(minutos));
            $('#horaOblig').prop("disabled", false);
            /* $('#horaOblig').val(prefijo(horas)); */
            if ($('#horaOblig').val() == null || $('#horaOblig').val() == '') {
                $('#horaOblig').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultHour: "8"
                });
                $('#horaOblig').val("08:00");

            }
            $('#divOtrodia').hide();
        }

    });
});
$('#selectarea').on("change", function (e) {
    console.log($('#selectarea').val());
    RefreshTablaEmpleadoArea();
});

///PAUSAS HORARIO EDITAR
$('#SwitchPausa_ed').change(function (event) {
    if ($('#SwitchPausa_ed').prop('checked')) {
        $('input[name="descPausa_ed[]"]').prop('required', true);
        $('#InicioPausa_ed').prop('required', true);
        $('#FinPausa_ed').prop('required', true);
        $('#divPausa_ed').show();
    }
    else {

        $('input[name="descPausa_ed[]"]').val('');
        $('input[name="InicioPausa_ed[]"]').val('');
        $('input[name="FinPausa_ed[]"]').val('');
        $('#divPausa_ed').hide();
        $('input[name="descPausa_ed[]"]').prop('required', false);
        $('input[name="InicioPausa_ed[]"]').prop('required', false);
        $('input[name="FinPausa_ed[]"]').prop('required', false);
    }
    event.preventDefault();
});
function addField() {

    var clickID = parseInt($(this).parent('div').attr('id').replace('divEd_', ''));
    // Genero el nuevo numero id
    var newID = (clickID + 1);
    // Creo un clon del elemento div que contiene los campos de texto
    $newClone = $('#divEd_' + clickID).clone(true);
    //Le asigno el nuevo numero id
    $newClone.attr("id", 'divEd_' + newID);
    $newClone.children("input").eq(0).attr("id", 'descPausa_ed' + newID).val('');
    //Borro el valor del segundo campo input(este caso es el campo de cantidad)
    $newClone.children("input").eq(1).attr("id", 'InicioPausa_ed' + newID).val('').prop('required', true).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour: null
    });
    var horafinal = $('#horaF_ed').val();
    splih = horafinal.split(":");
    console.log(splih[0]);
    $newClone.children("input").eq(2).attr("id", 'FinPausa_ed' + newID).val('').prop('required', true).prop("disabled", true).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour: splih[0]
    });;
    $(function () {
        $(document).on('change', '#FinPausa_ed' + newID, function (event) {
            let horaF = $('#FinPausa_ed' + newID).val();
            let horaI = $('#InicioPausa_ed' + newID).val();

            if (horaF < $('#horaI_ed').val() || horaF > $('#horaF_ed').val()) {
                $('#FinPausa_ed' + newID).val('');
                $('#fueraRango_ed').show();
                event.stopPropagation();
            } else {
                $('#fueraRango_ed').hide();
                if (horaI > horaF && horaF <= $('#horaI_ed').val() && horaF > $('#horaF_ed').val()) {
                    $('#errorenPausas_ed').show();
                    $('#FinPausa_ed' + newID).val('');
                }
                else {
                    $('#errorenPausas_ed').hide();
                }
            }

            if (horaF < horaI) {
                $('#FinPausa_ed' + newID).val('');
                $('#errorenPausas_ed').show();
                event.stopPropagation();
            } else {
                $('#errorenPausas_ed').hide();
            }



        });
    });
    $(function () {
        $(document).on('change', '#InicioPausa_ed' + newID, function (event) {
            let horaF = $('#FinPausa_ed' + newID).val();
            let horaI = $('#InicioPausa_ed' + newID).val();
            $('#FinPausa_ed' + newID).prop("disabled", false);
            if (horaI < $('#horaI_ed').val() || horaI > $('#horaF_ed').val()) {

                $('#InicioPausa_ed' + newID).val('');
                $('#fueraRango').show();
                event.stopPropagation();
            } else {
                $('#fueraRango').hide();
            }

            if (horaF == null || horaF == '') {

            } else {
                if (horaF < horaI) {
                    $('#InicioPausa_ed' + newID).val('');
                    $('#errorenPausas').show();
                    event.stopPropagation();
                } else {
                    $('#errorenPausas').hide();
                }
            }

        });
    });



    $newClone.children("button").attr("id", newID)
    //Inserto el div clonado y modificado despues del div original
    $newClone.insertAfter($('#divEd_' + clickID));
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
    $('input[name="descPausa_ed[]"]').prop('required', true);
    $('input[name="InicioPausa_ed[]"]').prop('required', true);
    $('input[name="FinPausa_ed[]"]').prop('required', true);
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
///PAUSAS HORARIO AGREGAR
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
function addFieldRe() {

    var clickID = parseInt($(this).parent('div').attr('id').replace('div_', ''));
    // Genero el nuevo numero id
    var newID = (clickID + 1);
    // Creo un clon del elemento div que contiene los campos de texto
    $newClone = $('#div_' + clickID).clone(true);
    //Le asigno el nuevo numero id
    $newClone.attr("id", 'div_' + newID);
    $newClone.children("input").eq(0).attr("id", 'descPausa' + newID).val('');
    //Borro el valor del segundo campo input(este caso es el campo de cantidad)
    $newClone.children("input").eq(1).attr("id", 'InicioPausa' + newID).val('').prop('required', true).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour: null
    });
    var horafinal = $('#horaF').val();
    splih = horafinal.split(":");
    console.log(splih[0]);
    $newClone.children("input").eq(2).attr("id", 'FinPausa' + newID).val('').prop('required', true).prop("disabled", true).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour: splih[0]
    });;
    $(function () {
        $(document).on('change', '#FinPausa' + newID, function (event) {
            let horaF = $('#FinPausa' + newID).val();
            let horaI = $('#InicioPausa' + newID).val();

            if (horaF < $('#horaI').val() || horaF > $('#horaF').val()) {
                $('#FinPausa' + newID).val('');
                $('#fueraRango').show();
                event.stopPropagation();
            } else {
                $('#fueraRango').hide();
                if (horaI > horaF && horaF <= $('#horaI').val() && horaF > $('#horaF').val()) {
                    $('#errorenPausas').show();
                    $('#FinPausa' + newID).val('');
                }
                else {
                    $('#errorenPausas').hide();
                }
            }

            if (horaF < horaI) {
                $('#FinPausa' + newID).val('');
                $('#errorenPausas').show();
                event.stopPropagation();
            } else {
                $('#errorenPausas').hide();
            }



        });
    });
    $(function () {
        $(document).on('change', '#InicioPausa' + newID, function (event) {
            let horaF = $('#FinPausa' + newID).val();
            let horaI = $('#InicioPausa' + newID).val();
            $('#FinPausa' + newID).prop("disabled", false);
            if (horaI < $('#horaI').val() || horaI > $('#horaF').val()) {

                $('#InicioPausa' + newID).val('');
                $('#fueraRango').show();
                event.stopPropagation();
            } else {
                $('#fueraRango').hide();
            }

            if (horaF == null || horaF == '') {

            } else {
                if (horaF < horaI) {
                    $('#InicioPausa' + newID).val('');
                    $('#errorenPausas').show();
                    event.stopPropagation();
                } else {
                    $('#errorenPausas').hide();
                }
            }

        });
    });


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
//? CONTENIDO DE VIDEOS EN MODAL DE EDITAR
var modalOculto;
// * DATOS PERSONALES
function mostrarContenido() {
    // *OCULTAR FORMULARIO
    console.log($('#form-registrar').is(':visible'));
    if ($('#form-ver').is(':visible') === true) {
        modalOculto = 2;
        $('#form-ver').modal('hide');
    }
    if ($('#form-registrar').is(':visible') === true) {
        modalOculto = 1;
        $('#form-registrar').modal('hide');
    }
    //*ABRIR MODAL
    $('#modal-video').modal();
    // * PLAY DE VIDEO AL ABRIR MODAL
    var iframe = document.querySelector('#contenidoIframe');
    var player = new Vimeo.Player(iframe);
    player.play();
}

// ? PAUSAR VIDEO MANUALMENTE
function stopVideo() {
    var iframe = document.querySelector('#contenidoIframe');
    var player = new Vimeo.Player(iframe);
    player.unload();
    // ! MOSTRAR MODAL OCULTO
    if (modalOculto === 1) {
        $('#form-registrar').modal('show');
    }
    if (modalOculto === 2) {
        $('#form-ver').modal('show');
    }
}

// * DATOS EPRESARIAL
function mostrarContenidoE() {
    // *OCULTAR FORMULARIO
    console.log($('#form-registrar').is(':visible'));
    if ($('#form-ver').is(':visible') === true) {
        modalOculto = 2;
        $('#form-ver').modal('hide');
    }
    if ($('#form-registrar').is(':visible') === true) {
        modalOculto = 1;
        $('#form-registrar').modal('hide');
    }
    //*ABRIR MODAL
    $('#modal-videoE').modal();
    // * PLAY DE VIDEO AL ABRIR MODAL
    var iframe = document.querySelector('#contenidoIframeE');
    var player = new Vimeo.Player(iframe);
    player.play();
}
// ? PAUSAR VIDEO MANUALMENTE
function stopVideoE() {
    var iframe = document.querySelector('#contenidoIframeE');
    var player = new Vimeo.Player(iframe);
    player.unload();
    // ! MOSTRAR MODAL OCULTO
    if (modalOculto === 1) {
        $('#form-registrar').modal('show');
    }
    if (modalOculto === 2) {
        $('#form-ver').modal('show');
    }
}

// * FOTO
function mostrarContenidoF() {
    // *OCULTAR FORMULARIO
    console.log($('#form-registrar').is(':visible'));
    if ($('#form-ver').is(':visible') === true) {
        modalOculto = 2;
        $('#form-ver').modal('hide');
    }
    if ($('#form-registrar').is(':visible') === true) {
        modalOculto = 1;
        $('#form-registrar').modal('hide');
    }
    //*ABRIR MODAL
    $('#modal-videoF').modal();
    // * PLAY DE VIDEO AL ABRIR MODAL
    var iframe = document.querySelector('#contenidoIframeF');
    var player = new Vimeo.Player(iframe);
    player.play();
}
// ? PAUSAR VIDEO MANUALMENTE
function stopVideoF() {
    var iframe = document.querySelector('#contenidoIframeF');
    var player = new Vimeo.Player(iframe);
    player.unload();
    // ! MOSTRAR MODAL OCULTO
    if (modalOculto === 1) {
        $('#form-registrar').modal('show');
    }
    if (modalOculto === 2) {
        $('#form-ver').modal('show');
    }
}
//* CALENDARIO
function mostrarContenidoC() {
    // *OCULTAR FORMULARIO
    console.log($('#form-registrar').is(':visible'));
    if ($('#form-ver').is(':visible') === true) {
        modalOculto = 2;
        $('#form-ver').modal('hide');
    }
    if ($('#form-registrar').is(':visible') === true) {
        modalOculto = 1;
        $('#form-registrar').modal('hide');
    }
    //*ABRIR MODAL
    $('#modal-videoC').modal();
    // * PLAY DE VIDEO AL ABRIR MODAL
    var iframe = document.querySelector('#contenidoIframeC');
    var player = new Vimeo.Player(iframe);
    player.play();
}
// ? PAUSAR VIDEO MANUALMENTE
function stopVideoC() {
    var iframe = document.querySelector('#contenidoIframeC');
    var player = new Vimeo.Player(iframe);
    player.unload();
    // ! MOSTRAR MODAL OCULTO
    if (modalOculto === 1) {
        $('#form-registrar').modal('show');
    }
    if (modalOculto === 2) {
        $('#form-ver').modal('show');
    }
}
// * HORARIO
function mostrarContenidoH() {
    // *OCULTAR FORMULARIO
    console.log($('#form-registrar').is(':visible'));
    if ($('#form-ver').is(':visible') === true) {
        modalOculto = 2;
        $('#form-ver').modal('hide');
    }
    if ($('#form-registrar').is(':visible') === true) {
        modalOculto = 1;
        $('#form-registrar').modal('hide');
    }
    //*ABRIR MODAL
    $('#modal-videoH').modal();
    // * PLAY DE VIDEO AL ABRIR MODAL
    var iframe = document.querySelector('#contenidoIframeH');
    var player = new Vimeo.Player(iframe);
    player.play();
}
// ? PAUSAR VIDEO MANUALMENTE
function stopVideoH() {
    var iframe = document.querySelector('#contenidoIframeH');
    var player = new Vimeo.Player(iframe);
    player.unload();
    // ! MOSTRAR MODAL OCULTO
    if (modalOculto === 1) {
        $('#form-registrar').modal('show');
    }
    if (modalOculto === 2) {
        $('#form-ver').modal('show');
    }
}
// * ACTIVIDADES
function mostrarContenidoA() {
    // *OCULTAR FORMULARIO
    console.log($('#form-registrar').is(':visible'));
    if ($('#form-ver').is(':visible') === true) {
        modalOculto = 2;
        $('#form-ver').modal('hide');
    }
    if ($('#form-registrar').is(':visible') === true) {
        modalOculto = 1;
        $('#form-registrar').modal('hide');
    }
    //*ABRIR MODAL
    $('#modal-videoA').modal();
    // * PLAY DE VIDEO AL ABRIR MODAL
    var iframe = document.querySelector('#contenidoIframeA');
    var player = new Vimeo.Player(iframe);
    player.play();
}
// ? PAUSAR VIDEO MANUALMENTE
function stopVideoA() {
    var iframe = document.querySelector('#contenidoIframeA');
    var player = new Vimeo.Player(iframe);
    player.unload();
    // ! MOSTRAR MODAL OCULTO
    if (modalOculto === 1) {
        $('#form-registrar').modal('show');
    }
    if (modalOculto === 2) {
        $('#form-ver').modal('show');
    }
}
// * DISPOSITIVO
function mostrarContenidoD() {
    // *OCULTAR FORMULARIO
    console.log($('#form-registrar').is(':visible'));
    if ($('#form-ver').is(':visible') === true) {
        modalOculto = 2;
        $('#form-ver').modal('hide');
    }
    if ($('#form-registrar').is(':visible') === true) {
        modalOculto = 1;
        $('#form-registrar').modal('hide');
    }
    //*ABRIR MODAL
    $('#modal-videoD').modal();
    // * PLAY DE VIDEO AL ABRIR MODAL
    var iframe = document.querySelector('#contenidoIframeD');
    var player = new Vimeo.Player(iframe);
    player.play();
}
// ? PAUSAR VIDEO MANUALMENTE
function stopVideoD() {
    var iframe = document.querySelector('#contenidoIframeD');
    var player = new Vimeo.Player(iframe);
    player.unload();
    // ! MOSTRAR MODAL OCULTO
    if (modalOculto === 1) {
        $('#form-registrar').modal('show');
    }
    if (modalOculto === 2) {
        $('#form-ver').modal('show');
    }
}
//VALIDACION PAUSAS
$(function () {
    $(document).on('change', '#FinPausa', function (event) {
        let horaF = $('#FinPausa').val();
        let horaI = $('#InicioPausa').val();
        if ($('#horaI').val() > $('#horaF').val()) {
            if (horaF < $('#horaI').val() && horaF > $('#horaF').val()) {
                $('#FinPausa').val('');
                $('#fueraRango').show();
                event.stopPropagation();
            } else {
                $('#fueraRango').hide();
                if (horaI > horaF && horaF <= $('#horaI').val() && horaF > $('#horaF').val()) {
                    $('#errorenPausas').show();
                    $('#FinPausa').val('');
                }
                else {
                    $('#errorenPausas').hide();
                }
            }

            if (horaI > horaF) {
                /*  $('#FinPausa').val('');
                 $('#errorenPausas').show();
                 event.stopPropagation(); */
            } else {
                $('#errorenPausas').hide();
            }
        }
        else {
            if (horaF < $('#horaI').val() || horaF > $('#horaF').val()) {
                $('#FinPausa').val('');
                $('#fueraRango').show();
                event.stopPropagation();
            } else {
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
        $('#FinPausa').prop("disabled", false);
        if ($('#horaI').val() > $('#horaF').val()) {

            if (horaI < $('#horaI').val() && horaI > $('#horaF').val()) {
                console.log('moostrando fuera rango 11/11');
                $('#InicioPausa').val('');
                $('#fueraRango').show();

                event.stopPropagation();
            } else {
                $('#fueraRango').hide();
            }

        } else {
            if (horaI < $('#horaI').val() || horaI > $('#horaF').val()) {

                $('#InicioPausa').val('');
                $('#fueraRango').show();
                event.stopPropagation();
            } else {
                $('#fueraRango').hide();
            }
        }

        console.log(horaF);
        if (horaF == null || horaF == '') {
            var horafinal1 = $('#horaF').val();
            splih1 = horafinal1.split(":");
            console.log(splih1[0]);
            console.log('nada me da');
            $('#FinPausa').val('').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: splih1[0]
            });

        }
        else {
            console.log('secumple');
            if ($('#horaI').val() < $('#horaF').val()) {
                if (horaF < horaI) {
                    $('#InicioPausa').val('');
                    $('#errorenPausas').show();
                    event.stopPropagation();
                } else {
                    $('#errorenPausas').hide();
                }
            } else {
                $('#errorenPausas').hide();
            }
        }


    });
});

//VALIDACIONES PAUSAS EN EDITAR EMPLEADO
$(function () {
    $(document).on('change', '#FinPausa_ed', function (event) {
        let horaF = $('#FinPausa_ed').val();
        let horaI = $('#InicioPausa_ed').val();
        if (horaF < $('#horaI_ed').val() || horaF > $('#horaF_ed').val()) {
            $('#FinPausa_ed').val('');
            $('#fueraRango_ed').show();
            event.stopPropagation();
        } else {
            $('#fueraRango_ed').hide();
            if (horaI > horaF && horaF <= $('#horaI_ed').val() && horaF > $('#horaF_ed').val()) {
                $('#errorenPausas_ed').show();
                $('#FinPausa_ed').val('');
            }
            else {
                $('#errorenPausas_ed').hide();
            }
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
        $('#FinPausa_ed').prop("disabled", false);
        if (horaI < $('#horaI_ed').val() || horaI > $('#horaF_ed').val()) {

            $('#InicioPausa_ed').val('');
            $('#fueraRango_ed').show();
            event.stopPropagation();
        } else {
            $('#fueraRango_ed').hide();
        }
        console.log(horaF);
        if (horaF == null || horaF == '') {
            var horafinal1 = $('#horaF_ed').val();
            splih1 = horafinal1.split(":");
            console.log(splih1[0]);
            console.log('nada me da');
            $('#FinPausa_ed').val('').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: splih1[0]
            });

        }
        else {
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

            $('#fueraHSwitch').prop('checked', true);
            $('#fueraHSwitch').prop('disabled', true);

        } else {
            $('#nHorasAdic').hide();
            $('#fueraHSwitch').prop('disabled', false);
        }

    });
});

/////////////////cambiar sch registrar
$(function () {
    $(document).on('change', '#horAdicSwitch_re', function (event) {
        if ($('#horAdicSwitch_re').prop('checked')) {
            $('#nHorasAdic_re').show();

            $('#fueraHSwitch_re').prop('checked', true);
            $('#fueraHSwitch_re').prop('disabled', true);

        } else {
            $('#nHorasAdic_re').hide();
            $('#fueraHSwitch_re').prop('disabled', false);
        }

    });
});
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "single",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d/m/Y",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: true,
});
$(function () {
    f = moment().format("YYYY-MM-DD");
    fechaValue.setDate(f);
    $("#fechaInput").change();
})
$("#contrato").change(function () {
    $("#validContrato").hide();
    let varCont = $("#contrato").val();
    if (varCont != '') {
        $('#form-registrar').modal('hide');
        $('#fechasmodal').modal('show');
    }


})
'use strict';

; (function (document, window, index) {
    var inputs = document.querySelectorAll('.inputfile');
    Array.prototype.forEach.call(inputs, function (input) {
        var label = input.nextElementSibling,
            labelVal = label.innerHTML;

        input.addEventListener('change', function (e) {
            var fileName = '';
            if (this.files && this.files.length > 1)
                fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
            else
                fileName = e.target.value.split('\\').pop();

            if (fileName)
                label.querySelector('span').innerHTML = fileName;
            else
                label.innerHTML = labelVal;
        });
    });
}(document, window, 0));
$("#v_contrato").change(function () {
    console.log('aqui se cierra x1');
    console.log('esperando')
    /*  $("#validContrato").hide(); */
    let varCont = $("#v_contrato").val();
    if (varCont != '') {
        console.log('aqui se cierra x2');
        $('#form-ver').modal('hide');
        $('#fechasmodalE').modal('show');
    }


})
