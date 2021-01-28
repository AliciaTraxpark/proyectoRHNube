$(document).ready(function () {
    // $(".bt_plus").each(function (el) {
    //     $(this).bind("click", addField);
    // });
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

            var event = calendar.getEventById(id);
            if (info.event.textColor == '111111') {
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
                    if (info.event.extendedProps.horaAdic == 1) {
                        $(info.el).tooltip({ title: 'Horario ' + info.event.title + ' :  ' + info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' + '     Marca horas adicionales' });
                    } else {
                        $(info.el).tooltip({ title: 'Horario ' + info.event.title + ' :  ' + info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF + '  Trabaja fuera de horario' });
                    }

                }
                else {
                    $(info.el).tooltip({ title: 'Horario ' + info.event.title + ' :  ' + info.event.extendedProps.horaI + '-' + info.event.extendedProps.horaF });
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
    $('#tablaEmpleadoExcel').DataTable().destroy();

    if ($("*").hasClass("fc-highlight")) {
        $('#guardarTodoHorario').prop('disabled', false);
    } else {
        $('#guardarTodoHorario').prop('disabled', false);
        bootbox.alert({
            message: "Primero debe asignar dia(s) de calendario.",

        })
        return false;
    }
    $('#guardarTodoHorario').prop('disabled', true);
    idemps = $('#nombreEmpleado').val();

    if (idemps == '') {

        bootbox.alert({
            title: "Seleccionar empleado",
            message: "Seleccione empleado",

        });
        $('#guardarTodoHorario').prop('disabled', false);
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
            $('#tablaEmpleado').DataTable().ajax.reload();
            $('#guardarTodoHorario').prop('disabled', false);

            $('#asignarHorario').modal('toggle');
            calendar.refetchEvents();
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
        data: { idempleadoI, descripcionI, descuentoI, fechaI, fechaF },
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
    $('#btnGuardaHorario').prop('disabled', false);
    $('#errorenPausas').hide();
    $('#fueraRango').hide();
    $('#divOtrodia').hide();
    $('#divPausa').hide();
    $('#horaOblig').prop("disabled", "disabled");
    $('#inputPausa').empty();
    $('#inputPausa').append('<div id="div_100" class="row col-md-12" style=" margin-bottom: 8px;">' +
        '<input type="text"  class="form-control form-control-sm col-sm-5" name="descPausa[]" id="descPausa" >' +
        '<input type="text"  class="form-control form-control-sm col-sm-3" name="InicioPausa[]"  id="InicioPausa" >' +
        '<input type="text"  class="form-control form-control-sm col-sm-3" name="FinPausa[]"  id="FinPausa" disabled >' +
        '&nbsp; <a style="cursor: pointer" id="btnPb_100" ><img src="/admin/images/delete.svg" height="15"></a><button class="btn btn-sm bt_plus" id="100" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
        ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>' +
        '</div>');
    $("#btnPb_100").bind("click", delRow);
    $("#idPausaMayor").val(100);
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
        defaultHour: null
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
            title: "Seleccionar dias",
            message: "Primero debe asignar dia(s) de calendario.",

        })
    }

}
function registrarHorario() {
    var variablesiPau = 1;
    var esperandoP = 0;


    /////////////////////////////////////////
    $('#InicioPausa').prop('required', true);
    $('#FinPausa').prop('required', true);

    if ($('#SwitchPausa').is(":checked")) {
        //PAUSAS PRIMERA   VALIDADAS
        let horaF = $('#FinPausa').val();
        let horaI = $('#InicioPausa').val();
        if ($('#horaI').val() > $('#horaF').val()) {
            if (horaF < $('#horaI').val() && horaF > $('#horaF').val()) {
                $('#FinPausa').val('');
                $('#fueraRango').show();
                event.stopPropagation();
            } else {
                $('#fueraRango').hide();
                if (horaI >= horaF && horaF <= $('#horaI').val() && horaF > $('#horaF').val()) {
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
            if (horaF <= horaI) {
                $('#FinPausa').val('');
                $('#errorenPausas').show();
                event.stopPropagation();
            } else {
                $('#errorenPausas').hide();
            }
        }



        let horaF2 = $('#FinPausa').val();
        let horaI2 = $('#InicioPausa').val();
        $('#FinPausa').prop("disabled", false);


        if ($('#horaI').val() > $('#horaF').val()) {

            if (horaI2 < $('#horaI').val() && horaI2 > $('#horaF').val()) {

                $('#InicioPausa').val('');
                $('#fueraRango').show();

                event.stopPropagation();
            } else {
                $('#fueraRango').hide();
            }

        } else {
            if (horaI2 < $('#horaI').val() || horaI2 > $('#horaF').val()) {

                $('#InicioPausa').val('');
                $('#fueraRango').show();
                event.stopPropagation();
            } else {
                $('#fueraRango').hide();
            }
        }


        if (horaF2 == null || horaF2 == '') {
            var horafinal1 = $('#horaF').val();
            splih1 = horafinal1.split(":");

            $('#FinPausa').val('').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: splih1[0]
            });

        }
        else {

            if ($('#horaI').val() < $('#horaF').val()) {
                if (horaF2 <= horaI2) {
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

        $('#FinPausa').on('focus', function () {
            $(this).blur();
        })
        $('#FinPausa').removeAttr("readonly");

        /////////////////////////////////////


        //////////PAUSAS2 VALIDADAS//
        for (newID = 100; newID < 130; newID++) {
            /*  console.log('soyNuw'+ newID); */
            let horaF3 = $('#FinPausa' + newID).val();
            let horaI3 = $('#InicioPausa' + newID).val();
            $('#FinPausa' + newID).prop("disabled", false);
            if ($('#horaI').val() > $('#horaF').val()) {

                if (horaI3 < $('#horaI').val() && horaI3 > $('#horaF').val()) {

                    $('#InicioPausa' + newID).val('');
                    $('#fueraRango').show();

                    event.stopPropagation();
                } else {
                    $('#fueraRango').hide();
                }

            } else {
                if (horaI3 < $('#horaI').val() || horaI3 > $('#horaF').val()) {

                    $('#InicioPausa' + newID).val('');
                    $('#fueraRango').show();
                    event.stopPropagation();
                } else {
                    $('#fueraRango').hide();
                }
            }


            if (horaF3 == null || horaF3 == '') {

            } else {
                if ($('#horaI').val() < $('#horaF').val()) {
                    if (horaF3 <= horaI3) {
                        $('#InicioPausa' + newID).val('');
                        $('#errorenPausas').show();
                        event.stopPropagation();
                    } else {
                        $('#errorenPausas').hide();
                    }
                } else {
                    $('#errorenPausas').hide();
                }
            }

            ///////////


            if ($('#horaI').val() > $('#horaF').val()) {
                if (horaF3 < $('#horaI').val() && horaF3 > $('#horaF').val()) {
                    $('#FinPausa' + newID).val('');
                    $('#fueraRango').show();
                    event.stopPropagation();
                } else {
                    $('#fueraRango').hide();
                    if (horaI3 >= horaF3 && horaF3 <= $('#horaI').val() && horaF3 > $('#horaF').val()) {
                        $('#errorenPausas').show();
                        $('#FinPausa' + newID).val('');
                    }
                    else {
                        $('#errorenPausas').hide();
                    }
                }

                if (horaI3 > horaF3) {
                    /*  $('#FinPausa').val('');
                     $('#errorenPausas').show();
                     event.stopPropagation(); */
                } else {
                    $('#errorenPausas').hide();
                }
            } else {
                if (horaF3 < $('#horaI').val() || horaF3 > $('#horaF').val()) {
                    $('#FinPausa' + newID).val('');
                    $('#fueraRango').show();
                    event.stopPropagation();
                } else {
                    $('#fueraRango').hide();
                }

                if (horaF3 <= horaI3) {
                    $('#FinPausa' + newID).val('');
                    $('#errorenPausas').show();
                    event.stopPropagation();
                } else {
                    $('#errorenPausas').hide();
                }
            }




        }
    }
    //////////////////////////////



    descripcion = $('#descripcionCa').val();
    toleranciaH = $('#toleranciaH').val();
    toleranciaF = $('#toleranciaSalida').val();
    horaOblig = $('#horaOblig').val();
    inicio = $('#horaI').val();
    fin = $('#horaF').val();
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
    if ($("#SwitchPausa").is(":checked")) {
        $.each(pausaInicio, function (ind, elem) {
            if (ind > 0) {
                if (pausaInicio.length > 0) {
                    if (pausaInicio[ind] > pausaInicio[ind - 1] && pausaInicio[ind] > finPausa[ind - 1]) {
                        variablesiPau = 1;
                        $('#errorenPausasCruzadas').hide();
                    }
                    else {
                        if (pausaInicio[ind] < pausaInicio[ind - 1] && pausaInicio[ind] < finPausa[ind - 1]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();
                            console.log('error400')

                        }

                    }
                    if (finPausa[ind] > pausaInicio[ind - 1] && finPausa[ind] > finPausa[ind - 1]) {
                        if (pausaInicio[ind] > pausaInicio[ind - 1] && pausaInicio[ind] > finPausa[ind - 1]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();
                            console.log('error200')

                        }

                    }
                    else {
                        if (finPausa[ind] < pausaInicio[ind - 1] && finPausa[ind] < finPausa[ind - 1]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();
                            console.log('error300')
                        }
                    }
                }
                if (pausaInicio.length > 2) {
                    if (pausaInicio[ind] > pausaInicio[ind - 1] && pausaInicio[ind] > finPausa[ind - 1]) {
                        variablesiPau = 1;
                        $('#errorenPausasCruzadas').hide();
                    }
                    else {
                        if (pausaInicio[ind] < pausaInicio[ind - 1] && pausaInicio[ind] < finPausa[ind - 1]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show(); console.log('error300'); console.log(pausaInicio[ind] + '<' + pausaInicio[ind - 1] + '<' + pausaInicio[ind] + '<' + finPausa[ind - 1])

                        }

                    }
                    if (finPausa[ind] > pausaInicio[ind - 1] && finPausa[ind] > finPausa[ind - 1]) {
                        if (pausaInicio[ind] > pausaInicio[ind - 1] && pausaInicio[ind] > finPausa[ind - 1]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }

                    }
                    else {
                        if (finPausa[ind] < pausaInicio[ind - 1] && finPausa[ind] < finPausa[ind - 1]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }
                    }
                    ///////////////////////////////
                    if (pausaInicio[ind] > pausaInicio[ind - 2] && pausaInicio[ind] > finPausa[ind - 2]) {
                        variablesiPau = 1;
                        $('#errorenPausasCruzadas').hide();
                    }
                    else {
                        if (pausaInicio[ind] < pausaInicio[ind - 2] && pausaInicio[ind] < finPausa[ind - 2]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }

                    }
                    if (finPausa[ind] > pausaInicio[ind - 2] && finPausa[ind] > finPausa[ind - 2]) {
                        if (pausaInicio[ind] > pausaInicio[ind - 2] && pausaInicio[ind] > finPausa[ind - 2]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }

                    }
                    else {
                        if (finPausa[ind] < pausaInicio[ind - 2] && finPausa[ind] < finPausa[ind - 2]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }
                    }
                }

                if (pausaInicio.length > 3) {
                    if (pausaInicio[ind] > pausaInicio[ind - 1] && pausaInicio[ind] > finPausa[ind - 1]) {
                        variablesiPau = 1;
                        $('#errorenPausasCruzadas').hide();
                    }
                    else {
                        if (pausaInicio[ind] < pausaInicio[ind - 1] && pausaInicio[ind] < finPausa[ind - 1]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }

                    }
                    if (finPausa[ind] > pausaInicio[ind - 1] && finPausa[ind] > finPausa[ind - 1]) {
                        if (pausaInicio[ind] > pausaInicio[ind - 1] && pausaInicio[ind] > finPausa[ind - 1]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }

                    }
                    else {
                        if (finPausa[ind] < pausaInicio[ind - 1] && finPausa[ind] < finPausa[ind - 1]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }
                    }
                    ///////////////////////////////
                    if (pausaInicio[ind] > pausaInicio[ind - 2] && pausaInicio[ind] > finPausa[ind - 2]) {
                        variablesiPau = 1;
                        $('#errorenPausasCruzadas').hide();
                    }
                    else {
                        if (pausaInicio[ind] < pausaInicio[ind - 2] && pausaInicio[ind] < finPausa[ind - 2]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }

                    }
                    if (finPausa[ind] > pausaInicio[ind - 2] && finPausa[ind] > finPausa[ind - 2]) {
                        if (pausaInicio[ind] > pausaInicio[ind - 2] && pausaInicio[ind] > finPausa[ind - 2]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }

                    }
                    else {
                        if (finPausa[ind] < pausaInicio[ind - 2] && finPausa[ind] < finPausa[ind - 2]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }
                    }

                    if (pausaInicio[ind] > pausaInicio[ind - 3] && pausaInicio[ind] > finPausa[ind - 3]) {
                        variablesiPau = 1;
                        $('#errorenPausasCruzadas').hide();
                    }
                    else {
                        if (pausaInicio[ind] < pausaInicio[ind - 3] && pausaInicio[ind] < finPausa[ind - 3]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }

                    }
                    if (finPausa[ind] > pausaInicio[ind - 3] && finPausa[ind] > finPausa[ind - 3]) {
                        if (pausaInicio[ind] > pausaInicio[ind - 3] && pausaInicio[ind] > finPausa[ind - 3]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }

                    }
                    else {
                        if (finPausa[ind] < pausaInicio[ind - 3] && finPausa[ind] < finPausa[ind - 3]) {
                            variablesiPau = 1;
                            $('#errorenPausasCruzadas').hide();
                        } else {
                            variablesiPau = 0;
                            $('#errorenPausasCruzadas').show();

                        }
                    }
                }



            }
            esperandoP = 1;
        });
        if (variablesiPau == 1 && esperandoP == 1) {
            $('#btnGuardaHorario').prop('disabled', true);
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
    }
    else {

        $('#btnGuardaHorario').prop('disabled', true);
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
                            alert('Ocurrio un error');
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
                            alert('Ocurrio un error');
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
                            alert('Ocurrio un error');
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
                            alert('Ocurrio un error');
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
    bootbox.confirm({
        title: "Elminar horario",
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
            var valorObli = data[0].horasObliga;

            if (valorObli != null || valorObli != ' ') {
                $('#horaOblig_ed').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultDate: data[0].horasObliga.substr(-20, 5)
                });
                $('#horaOblig_ed').val(data[0].horasObliga.substr(-20, 5));
            } else {

                $('#horaOblig_ed').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultDate: "08:00"
                });
            }


            //HORA IINICIO
            $('#horaI_ed').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate: data[0].horaI.substr(-20, 5)
            });
            $('#horaI_ed').val(data[0].horaI.substr(-20, 5));
            //HORA FIN
            $('#horaF_ed').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate: data[0].horaF.substr(-20, 5)
            });
            $('#horaF_ed').val(data[0].horaF.substr(-20, 5));
            $('#idhorario_ed').val(data[0].horario_id);
            $('#descripcionCa_ed').val(data[0].horario_descripcion);
            $('#toleranciaH_ed').val(data[0].horario_tolerancia);
            $('#toleranciaSalida_ed').val(data[0].horario_toleranciaF);
            /*  $('#horaOblig_ed').val(data[0].horasObliga); */


            if (data[0].hora_contTardanza == 1) {
                $('#SwitchTardanza_ed').prop('checked', true);
            } else {
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
            else {/*  */
                var arrayidPau = [];
                $.each(data[1], function (key, item) {
                    arrayidPau.push(item.idpausas_horario);

                }
                );
                var maxCadenaId = Math.max(...arrayidPau);
                var maxInput = Math.max(...arrayidPau);
                $('#maxIn').val(maxInput);
                var minInput = Math.min(...arrayidPau);
                $('#minIn').val(minInput);


                $('#pausas_edit').show();
                $.each(data[1], function (key, item) {
                    $('#SwitchPausa_ed').prop('checked', true);
                    $('#SwitchPausa_ed').prop('disabled', false);



                    if (item.idpausas_horario == maxCadenaId) {
                        $("#PausasHorar_ed").append('<div  id="divEdReg_' + item.idpausas_horario + '" class="row col-md-12" style=" margin-bottom: 8px;">' +
                            '<input type="text"  value="' + item.pausH_descripcion + '"  class="form-control form-control-sm col-sm-5" name="descPausa_edRegist[]" required >' +
                            '<input type="text"  value="' + item.pausH_Inicio + '"   class="form-control form-control-sm col-sm-3" name="InicioPausa_edReg[]" id="InicioPausa_edReg' + item.idpausas_horario + '" required  >' +
                            '<input type="text" value="' + item.pausH_Fin + '"   class="form-control form-control-sm col-sm-3" name="FinPausa_edReg[]" id="FinPausa_edReg' + item.idpausas_horario + '" required >' +
                            '<input type="hidden" value="' + item.idpausas_horario + '"   class="form-control form-control-sm col-sm-3" name="idPausasRegistradas_ed[]" required  > &nbsp;  <a id="btnEli_edReg' + item.idpausas_horario + '" onclick="" style="cursor: pointer;     margin-top: 5px;">' +
                            '<img src="/admin/images/delete.svg" onclick="eliminarPH(' + item.idpausas_horario + ')" height="15"></a>  <button class="btn btn-sm" id="agregarMP" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
                            ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 10px">+</button>' +

                            '</div>' +
                            '<div id="divEdRN_100" class="row col-md-12" style=" margin-bottom: 8px;  display:none">' +
                            '<input type="text"  class="form-control form-control-sm col-sm-5" name="descPausa_edRN[]" id="descPausa_edRN" >' +
                            '<input type="text"   class="form-control form-control-sm col-sm-3" name="InicioPausa_edRN[]"  id="InicioPausa_edRN" >' +
                            '<input type="text" class="form-control form-control-sm col-sm-3" name="FinPausa_edRN[]"  id="FinPausa_edRN" >' +
                            '&nbsp; <a style="cursor: pointer" id="btnPbedA_100" ><img src="/admin/images/delete.svg" height="15"></a> <button class="btn btn-sm bt_plus_edAgr" id="edA_100" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
                            ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>' +
                            '</div>');

                    } else {
                        $("#PausasHorar_ed").append('<div  id="divEdReg_' + item.idpausas_horario + '" class="row col-md-12" style=" margin-bottom: 8px;">' +
                            '<input type="text"  value="' + item.pausH_descripcion + '"  class="form-control form-control-sm col-sm-5" name="descPausa_edRegist[]" required >' +
                            '<input type="text"  value="' + item.pausH_Inicio + '"   class="form-control form-control-sm col-sm-3" name="InicioPausa_edReg[]" id="InicioPausa_edReg' + item.idpausas_horario + '" required  >' +
                            '<input type="text" value="' + item.pausH_Fin + '"   class="form-control form-control-sm col-sm-3" name="FinPausa_edReg[]" id="FinPausa_edReg' + item.idpausas_horario + '" required >' +
                            '<input type="hidden" value="' + item.idpausas_horario + '"   class="form-control form-control-sm col-sm-3" name="idPausasRegistradas_ed[]" required  > &nbsp;  <a id="btnEli_edReg' + item.idpausas_horario + '" onclick="" style="cursor: pointer;     margin-top: 5px;">' +
                            '<img src="/admin/images/delete.svg" onclick="eliminarPH(' + item.idpausas_horario + ')" height="15"></a>' +

                            '</div>');
                    }

                    $("#btnPbedA_100").bind("click", delRow_edRN);
                    /*  */
                    $(document).on('change', '#FinPausa_edRN', function (event) {
                        let horaF = $('#FinPausa_edRN').val();
                        let horaI = $('#InicioPausa_edRN').val();
                        if (horaF < $('#horaI_ed').val() || horaF > $('#horaF_ed').val()) {
                            $('#FinPausa_edRN').val('');
                            $('#fueraRango_ed').show();
                            event.stopPropagation();
                        } else {
                            $('#fueraRango_ed').hide();
                            if (horaI >= horaF && horaF <= $('#horaI_ed').val() && horaF > $('#horaF_ed').val()) {
                                $('#errorenPausas_ed').show();
                                $('#FinPausa_edRN').val('');
                            }
                            else {
                                $('#errorenPausas_ed').hide();
                            }
                        }
                        if (horaF <= horaI) {
                            $('#FinPausa_edRN').val('');
                            $('#errorenPausas_ed').show();
                            event.stopPropagation();
                        } else {
                            $('#errorenPausas_ed').hide();
                        }


                    });

                    $(function () {
                        $(document).on('change', '#InicioPausa_edReg' + item.idpausas_horario + '', function (event) {
                            let horaF = $('#FinPausa_edReg' + item.idpausas_horario + '').val();
                            let horaI = $('#InicioPausa_edReg' + item.idpausas_horario + '').val();
                            $('#FinPausa_edReg' + item.idpausas_horario + '').prop("disabled", false);

                            if (horaI < $('#horaI_ed').val() || horaI > $('#horaF_ed').val()) {

                                $('#InicioPausa_edReg' + item.idpausas_horario + '').val('');
                                $('#fueraRango_ed').show();
                                event.stopPropagation();
                            } else {
                                $('#fueraRango_ed').hide();
                            }

                            if (horaF == null || horaF == '') {

                            }
                            else {

                                if (horaF <= horaI) {
                                    $('#InicioPausa_edReg' + item.idpausas_horario + '').val('');
                                    $('#errorenPausas_ed').show();
                                    event.stopPropagation();
                                } else {
                                    $('#errorenPausas_ed').hide();
                                }
                            }


                        });
                    });
                    $(function () {
                        $(document).on('change', '#FinPausa_edReg' + item.idpausas_horario + '', function (event) {
                            let horaF = $('#FinPausa_edReg' + item.idpausas_horario + '').val();
                            let horaI = $('#InicioPausa_edReg' + item.idpausas_horario + '').val();
                            if (horaF < $('#horaI_ed').val() || horaF > $('#horaF_ed').val()) {
                                $('#FinPausa_edReg' + item.idpausas_horario + '').val('');
                                $('#fueraRango_ed').show();
                                event.stopPropagation();
                            } else {
                                $('#fueraRango_ed').hide();
                                if (horaI >= horaF && horaF <= $('#horaI_ed').val() && horaF > $('#horaF_ed').val()) {
                                    $('#errorenPausas_ed').show();
                                    $('#FinPausa_edReg' + item.idpausas_horario + '').val('');
                                }
                                else {
                                    $('#errorenPausas_ed').hide();
                                }
                            }
                            if (horaF <= horaI) {
                                $('#FinPausa_edReg' + item.idpausas_horario + '').val('');
                                $('#errorenPausas_ed').show();
                                event.stopPropagation();
                            } else {
                                $('#errorenPausas_ed').hide();
                            }


                        });
                    });
                });
                $(function () {
                    $(document).on('change', '#InicioPausa_edRN', function (event) {
                        let horaF = $('#FinPausa_edRN').val();
                        let horaI = $('#InicioPausa_edRN').val();
                        $('#FinPausa_edRN').prop("disabled", false);

                        if (horaI < $('#horaI_ed').val() || horaI > $('#horaF_ed').val()) {

                            $('#InicioPausa_edRN').val('');
                            $('#fueraRango_ed').show();
                            event.stopPropagation();
                        } else {
                            $('#fueraRango_ed').hide();
                        }

                        if (horaF == null || horaF == '') {

                        }
                        else {

                            if (horaF < horaI) {
                                $('#InicioPausa_edRN').val('');
                                $('#errorenPausas_edRN').show();
                                event.stopPropagation();
                            } else {
                                $('#errorenPausas_ed').hide();
                            }
                        }


                    });
                });
                $('#InicioPausa_edRN').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true
                });
                let horafinal = $('#horaF_ed').val();
                splih = horafinal.split(":");

                $('#FinPausa_edRN').flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultHour: splih[0]
                });
                ///ACCION BOTON
                $("#agregarMP").click(function () {
                    $('#agregarMP').hide();
                    $('#divEdRN_100').show();
                });
                $(".bt_plus_edAgr").each(function (el) {
                    $(this).bind("click", addField_edNR);
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
    var variablesiPau_ed = 1;
    var esperandoP_ed = 0;
    /*   console.log('varia'+variablesiPau+'-'+esperandoP); */
    ///////////////////////////////////
    if ($('#SwitchPausa_ed').is(":checked")) {
        //PAUSAS PRIMERA   VALIDADAS
        let horaF = $('#FinPausa_ed').val();
        let horaI = $('#InicioPausa_ed').val();
        if ($('#horaI_ed').val() > $('#horaF_ed').val()) {
            if (horaF < $('#horaI_ed').val() && horaF > $('#horaF_ed').val()) {
                $('#FinPausa_ed').val('');
                $('#fueraRango_ed').show();
                event.stopPropagation();
            } else {
                $('#fueraRango_ed').hide();
                if (horaI >= horaF && horaF <= $('#horaI_ed').val() && horaF > $('#horaF_ed').val()) {
                    $('#errorenPausas_ed').show();
                    $('#FinPausa_ed').val('');
                }
                else {
                    $('#errorenPausas_ed').hide();
                }

            }

            if (horaI > horaF) {
                /*  $('#FinPausa').val('');
                 $('#errorenPausas').show();
                 event.stopPropagation(); */
            } else {
                $('#errorenPausas_ed').hide();
            }
        }
        else {
            if (horaF < $('#horaI_ed').val() || horaF > $('#horaF_ed').val()) {
                $('#FinPausa_ed').val('');
                $('#fueraRango_ed').show();
                event.stopPropagation();
            } else {
                $('#fueraRango_ed').hide();
            }
            if (horaF <= horaI) {
                $('#FinPausa_ed').val('');
                $('#errorenPausas_ed').show();
                event.stopPropagation();
            } else {
                $('#errorenPausas_ed').hide();
            }
        }



        let horaF2 = $('#FinPausa_ed').val();
        let horaI2 = $('#InicioPausa_ed').val();
        $('#FinPausa_ed').prop("disabled", false);


        if ($('#horaI_ed').val() > $('#horaF_ed').val()) {

            if (horaI2 < $('#horaI_ed').val() && horaI2 > $('#horaF_ed').val()) {

                $('#InicioPausa_ed').val('');
                $('#fueraRango_ed').show();

                event.stopPropagation();
            } else {
                $('#fueraRango_ed').hide();
            }

        } else {
            if (horaI2 < $('#horaI_ed').val() || horaI2 > $('#horaF_ed').val()) {

                $('#InicioPausa_ed').val('');
                $('#fueraRango_ed').show();
                event.stopPropagation();
            } else {
                $('#fueraRango_ed').hide();
            }
        }


        if (horaF2 == null || horaF2 == '') {
            var horafinal1 = $('#horaF_ed').val();
            splih1 = horafinal1.split(":");

            $('#FinPausa_ed').val('').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: splih1[0]
            });

        }
        else {

            if ($('#horaI_ed').val() < $('#horaF_ed').val()) {
                if (horaF2 <= horaI2) {
                    $('#InicioPausa_ed').val('');
                    $('#errorenPausas_ed').show();
                    event.stopPropagation();
                } else {
                    $('#errorenPausas_ed').hide();
                }
            } else {
                $('#errorenPausas_ed').hide();
            }
        }

        $('#FinPausa_ed').on('focus', function () {
            $(this).blur();
        })
        $('#FinPausa_ed').removeAttr("readonly");

        /////////////////////////////////////
        //////////PAUSAS2 VALIDADAS//
        for (newID = 100; newID < 130; newID++) {
            /*  console.log('soyNuw'+ newID); */
            let horaF3 = $('#FinPausa_ed' + newID).val();
            let horaI3 = $('#InicioPausa_ed' + newID).val();
            $('#FinPausa_ed' + newID).prop("disabled", false);
            if ($('#horaI_ed').val() > $('#horaF_ed').val()) {

                if (horaI3 < $('#horaI_ed').val() && horaI3 > $('#horaF_ed').val()) {

                    $('#InicioPausa_ed' + newID).val('');
                    $('#fueraRango_ed').show();

                    event.stopPropagation();
                } else {
                    $('#fueraRango_ed').hide();
                }

            } else {
                if (horaI3 < $('#horaI_ed').val() || horaI3 > $('#horaF_ed').val()) {

                    $('#InicioPausa_ed' + newID).val('');
                    $('#fueraRango_ed').show();
                    event.stopPropagation();
                } else {
                    $('#fueraRango_ed').hide();
                }
            }


            if (horaF3 == null || horaF3 == '') {

            } else {
                if ($('#horaI_ed').val() < $('#horaF_ed').val()) {
                    if (horaF3 <= horaI3) {
                        $('#InicioPausa_ed' + newID).val('');
                        $('#errorenPausas_ed').show();
                        event.stopPropagation();
                    } else {
                        $('#errorenPausas_ed').hide();
                    }
                } else {
                    $('#errorenPausas_ed').hide();
                }
            }

            ///////////


            if ($('#horaI_ed').val() > $('#horaF_ed').val()) {
                if (horaF3 < $('#horaI_ed').val() && horaF3 > $('#horaF_ed').val()) {
                    $('#FinPausa_ed' + newID).val('');
                    $('#fueraRango_ed').show();
                    event.stopPropagation();
                } else {
                    $('#fueraRango_ed').hide();
                    if (horaI3 >= horaF3 && horaF3 <= $('#horaI_ed').val() && horaF3 > $('#horaF_ed').val()) {
                        $('#errorenPausas_ed').show();
                        $('#FinPausa_ed' + newID).val('');
                    }
                    else {
                        $('#errorenPausas_ed').hide();
                    }
                }

                if (horaI3 > horaF3) {
                    /*  $('#FinPausa').val('');
                     $('#errorenPausas').show();
                     event.stopPropagation(); */
                } else {
                    $('#errorenPausas_ed').hide();
                }
            } else {
                if (horaF3 < $('#horaI_ed').val() || horaF3 > $('#horaF_ed').val()) {
                    $('#FinPausa_ed' + newID).val('');
                    $('#fueraRango_ed').show();
                    event.stopPropagation();
                } else {
                    $('#fueraRango_ed').hide();
                }

                if (horaF3 <= horaI3) {
                    $('#FinPausa_ed' + newID).val('');
                    $('#errorenPausas_ed').show();
                    event.stopPropagation();
                } else {
                    $('#errorenPausas_ed').hide();
                }
            }




        }
    }
    //////////////////////////VALIDACIONES EN EDITAR
    if ($("#SwitchPausa_ed").is(":checked")) {
        //PAUSAS de editar  PRIMERA   VALIDADAS
        let horaF4 = $("#FinPausa_edReg").val();
        let horaI4 = $("#InicioPausa_edReg").val();
        if ($("#horaI_ed").val() > $("#horaF_ed").val()) {
            if (horaF4 < $("#horaI_ed").val() && horaF4 > $("#horaF_ed").val()) {
                $("#FinPausa_edReg").val("");
                $("#fueraRango_ed").show();
                event.stopPropagation();
            } else {
                $("#fueraRango_ed").hide();
                if (
                    horaI4 >= horaF4 &&
                    horaF4 <= $("#horaI_ed").val() &&
                    horaF4 > $("#horaF_ed").val()
                ) {
                    $("#errorenPausas_ed").show();
                    $("#FinPausa_edReg").val("");
                } else {
                    $("#errorenPausas_ed").hide();
                }
            }

            if (horaI4 > horaF4) {
                /*  $('#FinPausa').val('');
                      $('#errorenPausas').show();
                      event.stopPropagation(); */
            } else {
                $("#errorenPausas_ed").hide();
            }
        } else {
            if (horaF4 < $("#horaI_ed").val() || horaF4 > $("#horaF_ed").val()) {
                $("#FinPausa_edReg").val("");
                $("#fueraRango_ed").show();
                event.stopPropagation();
            } else {
                $("#fueraRango_ed").hide();
            }
            if (horaF4 <= horaI4) {
                $("#FinPausa_edReg").val("");
                $("#errorenPausas_ed").show();
                event.stopPropagation();
            } else {
                $("#errorenPausas_ed").hide();
            }
        }

        let horaF5 = $("#FinPausa_edReg").val();
        let horaI5 = $("#InicioPausa_edReg").val();
        $("#FinPausa_edReg").prop("disabled", false);

        if ($("#horaI_ed").val() > $("#horaF_ed").val()) {
            if (horaI5 < $("#horaI_ed").val() && horaI5 > $("#horaF_ed").val()) {

                $("#InicioPausa_edReg").val("");
                $("#fueraRango_ed").show();

                event.stopPropagation();
            } else {
                $("#fueraRango_ed").hide();
            }
        } else {
            if (horaI5 < $("#horaI_ed").val() || horaI5 > $("#horaF_ed").val()) {
                $("#InicioPausa_edReg").val("");
                $("#fueraRango_ed").show();
                event.stopPropagation();
            } else {
                $("#fueraRango_ed").hide();
            }
        }


        if (horaF5 == null || horaF5 == "") {
            var horafinal1 = $("#horaF_ed").val();
            splih1 = horafinal1.split(":");

            $("#FinPausa_edReg").val("").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: splih1[0],
            });
        } else {

            if ($("#horaI_ed").val() < $("#horaF_ed").val()) {
                if (horaF5 <= horaI5) {
                    $("#InicioPausa_edReg").val("");
                    $("#errorenPausas_ed").show();
                    event.stopPropagation();
                } else {
                    $("#errorenPausas_ed").hide();
                }
            } else {
                $("#errorenPausas_ed").hide();
            }
        }

        $("#FinPausa_edReg").on("focus", function () {
            $(this).blur();
        });
        $("#FinPausa_edReg").removeAttr("readonly");

        /////////////////////////////////////
        //////////PAUSAS2 VALIDADAS//
        var maximoL = $('#maxIn').val() + 1;
        var minimoL = $('#minIn').val();
        for (newID2 = minimoL; newID2 < maximoL; newID2++) {

            let horaF6 = $("#FinPausa_edReg" + newID2).val();
            let horaI6 = $("#InicioPausa_edReg" + newID2).val();
            $("#FinPausa_edReg" + newID2).prop("disabled", false);
            if ($("#horaI_ed").val() > $("#horaF_ed").val()) {
                if (horaI6 < $("#horaI_ed").val() && horaI6 > $("#horaF_ed").val()) {

                    $("#InicioPausa_edReg" + newID2).val("");
                    $("#fueraRango_ed").show();

                    event.stopPropagation();
                } else {
                    $("#fueraRango_ed").hide();
                }
            } else {
                if (horaI6 < $("#horaI_ed").val() || horaI6 > $("#horaF_ed").val()) {
                    $("#InicioPausa_edReg" + newID2).val("");
                    $("#fueraRango_ed").show();
                    event.stopPropagation();
                } else {
                    $("#fueraRango_ed").hide();
                }
            }

            if (horaF6 == null || horaF6 == "") {
            } else {
                if ($("#horaI_ed").val() < $("#horaF_ed").val()) {
                    if (horaF6 <= horaI6) {
                        $("#InicioPausa_edReg" + newID2).val("");
                        $("#errorenPausas_ed").show();
                        event.stopPropagation();
                    } else {
                        $("#errorenPausas_ed").hide();
                    }
                } else {
                    $("#errorenPausas_ed").hide();
                }
            }

            ///////////

            if ($("#horaI_ed").val() > $("#horaF_ed").val()) {
                if (horaF6 < $("#horaI_ed").val() && horaF6 > $("#horaF_ed").val()) {
                    $("#FinPausa_edReg" + newID2).val("");
                    $("#fueraRango_ed").show();
                    event.stopPropagation();
                } else {
                    $("#fueraRango_ed").hide();
                    if (
                        horaI6 >= horaF6 &&
                        horaF6 <= $("#horaI_ed").val() &&
                        horaF6 > $("#horaF_ed").val()
                    ) {
                        $("#errorenPausas_ed").show();
                        $("#FinPausa_edReg" + newID2).val("");
                    } else {
                        $("#errorenPausas_ed").hide();
                    }
                }

                if (horaI6 > horaF6) {
                    /*  $('#FinPausa').val('');
                             $('#errorenPausas').show();
                             event.stopPropagation(); */
                } else {
                    $("#errorenPausas_ed").hide();
                }
            } else {
                if (horaF6 < $("#horaI_ed").val() || horaF6 > $("#horaF_ed").val()) {
                    $("#FinPausa_edReg" + newID2).val("");
                    $("#fueraRango_ed").show();
                    event.stopPropagation();
                } else {
                    $("#fueraRango_ed").hide();
                }

                if (horaF6 <= horaI6) {
                    $("#FinPausa_edReg" + newID2).val("");
                    $("#errorenPausas_ed").show();
                    event.stopPropagation();
                } else {
                    $("#errorenPausas_ed").hide();
                }
            }
        }
    }

    //////////////////////////////////3ro
    if ($("#SwitchPausa_ed").is(":checked")) {
        //PAUSAS de editar  PRIMERA   VALIDADAS
        let horaF8 = $("#FinPausa_edRN").val();
        let horaI8 = $("#InicioPausa_edRN").val();
        if ($("#horaI_ed").val() > $("#horaF_ed").val()) {
            if (horaF8 < $("#horaI_ed").val() && horaF8 > $("#horaF_ed").val()) {
                $("#FinPausa_edRN").val("");
                $("#fueraRango_ed").show();
                event.stopPropagation();
            } else {
                $("#fueraRango_ed").hide();
                if (
                    horaI8 >= horaF8 &&
                    horaF8 <= $("#horaI_ed").val() &&
                    horaF8 > $("#horaF_ed").val()
                ) {
                    $("#errorenPausas_ed").show();
                    $("#FinPausa_edRN").val("");
                } else {
                    $("#errorenPausas_ed").hide();
                }
            }

            if (horaI8 > horaF8) {
                /*  $('#FinPausa').val('');
                      $('#errorenPausas').show();
                      event.stopPropagation(); */
            } else {
                $("#errorenPausas_ed").hide();
            }
        } else {
            if (horaF8 < $("#horaI_ed").val() || horaF8 > $("#horaF_ed").val()) {
                $("#FinPausa_edRN").val("");
                $("#fueraRango_ed").show();
                /*  event.stopPropagation(); */
            } else {
                $("#fueraRango_ed").hide();
            }
            if (horaF8 <= horaI8) {
                $("#FinPausa_edRN").val("");
                $("#errorenPausas_ed").show();
                /* event.stopPropagation(); */
            } else {
                $("#errorenPausas_ed").hide();
            }
        }

        let horaF9 = $("#FinPausa_edRN").val();
        let horaI9 = $("#InicioPausa_edRN").val();
        $("#FinPausa_edRN").prop("disabled", false);

        if ($("#horaI_ed").val() > $("#horaF_ed").val()) {
            if (horaI9 < $("#horaI_ed").val() && horaI9 > $("#horaF_ed").val()) {

                $("#InicioPausa_edRN").val("");
                $("#fueraRango_ed").show();

                /* event.stopPropagation(); */
            } else {
                $("#fueraRango_ed").hide();
            }
        } else {
            if (horaI9 < $("#horaI_ed").val() || horaI9 > $("#horaF_ed").val()) {
                $("#InicioPausa_edRN").val("");
                $("#fueraRango_ed").show();
                /*  event.stopPropagation(); */
            } else {
                $("#fueraRango_ed").hide();
            }
        }


        if (horaF9 == null || horaF9 == "") {
            var horafinal1 = $("#horaF_ed").val();
            splih1 = horafinal1.split(":");

            $("#FinPausa_edRN").val("").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: splih1[0],
            });
        } else {

            if ($("#horaI_ed").val() < $("#horaF_ed").val()) {
                if (horaF9 <= horaI9) {
                    $("#InicioPausa_edRN").val("");
                    $("#errorenPausas_ed").show();
                    /* event.stopPropagation(); */
                } else {
                    $("#errorenPausas_ed").hide();
                }
            } else {
                $("#errorenPausas_ed").hide();
            }
        }

        $("#FinPausa_edRN").on("focus", function () {
            $(this).blur();
        });
        $("#FinPausa_edRN").removeAttr("readonly");

        /////////////////////////////////////
        //////////PAUSAS2 VALIDADAS//

        for (newID3 = 100; newID3 < 130; newID3++) {

            let horaF10 = $("#FinPausa_edRN" + newID3).val();
            let horaI10 = $("#InicioPausa_edRN" + newID3).val();
            $("#FinPausa_edRN" + newID3).prop("disabled", false);
            if ($("#horaI_ed").val() > $("#horaF_ed").val()) {
                if (horaI10 < $("#horaI_ed").val() && horaI10 > $("#horaF_ed").val()) {

                    $("#InicioPausa_edRN" + newID3).val("");
                    $("#fueraRango_ed").show();

                    /*  event.stopPropagation(); */
                } else {
                    $("#fueraRango_ed").hide();
                }
            } else {
                if (horaI10 < $("#horaI_ed").val() || horaI10 > $("#horaF_ed").val()) {
                    $("#InicioPausa_edRN" + newID3).val("");
                    $("#fueraRango_ed").show();
                    /*   event.stopPropagation(); */
                } else {
                    $("#fueraRango_ed").hide();
                }
            }

            if (horaF10 == null || horaF10 == "") {
            } else {
                if ($("#horaI_ed").val() < $("#horaF_ed").val()) {
                    if (horaF10 <= horaI10) {
                        $("#InicioPausa_edRN" + newID3).val("");
                        $("#errorenPausas_ed").show();
                        /*  event.stopPropagation(); */
                    } else {
                        $("#errorenPausas_ed").hide();
                    }
                } else {
                    $("#errorenPausas_ed").hide();
                }
            }

            ///////////

            if ($("#horaI_ed").val() > $("#horaF_ed").val()) {
                if (horaF10 < $("#horaI_ed").val() && horaF10 > $("#horaF_ed").val()) {
                    $("#FinPausa_edRN" + newID3).val("");
                    $("#fueraRango_ed").show();
                    /* event.stopPropagation(); */
                } else {
                    $("#fueraRango_ed").hide();
                    if (
                        horaI10 >= horaF10 &&
                        horaF10 <= $("#horaI_ed").val() &&
                        horaF10 > $("#horaF_ed").val()
                    ) {
                        $("#errorenPausas_ed").show();
                        $("#FinPausa_edRN" + newID3).val("");
                    } else {
                        $("#errorenPausas_ed").hide();
                    }
                }

                if (horaI10 > horaF10) {
                    /*  $('#FinPausa').val('');
                             $('#errorenPausas').show();
                             event.stopPropagation(); */
                } else {
                    $("#errorenPausas_ed").hide();
                }
            } else {
                if (horaF10 < $("#horaI_ed").val() || horaF10 > $("#horaF_ed").val()) {
                    $("#FinPausa_edRN" + newID3).val("");
                    $("#fueraRango_ed").show();
                    /* event.stopPropagation(); */
                } else {
                    $("#fueraRango_ed").hide();
                }

                if (horaF10 <= horaI10) {
                    $("#FinPausa_edRN" + newID3).val("");
                    $("#errorenPausas_ed").show();
                    /* event.stopPropagation(); */
                } else {
                    $("#errorenPausas_ed").hide();
                }
            }
        }
    }

    ///////////////////////////////////
    var idhorario = $('#idhorario_ed').val();

    var descried = $('#descripcionCa_ed').val();
    var toleed = $('#toleranciaH_ed').val();
    var horaIed = $('#horaI_ed').val();
    var horaFed = $('#horaF_ed').val();
    var toleranciaFed = $('#toleranciaSalida_ed').val();

    var horaObed = $('#horaOblig_ed').val();
    var tardanza_ed;
    if ($('#SwitchTardanza_ed').is(":checked")) {
        tardanza_ed = 1;
    } else {
        tardanza_ed = 0;
    }
    if ($('#SwitchPausa_ed').is(":checked")) {
        var descPausa_ed = [];
        var pausaInicio_ed = [];
        var finPausa_ed = [];

        var ID_edReg = [];
        var descPausa_edReg = [];
        var pausaInicio_edReg = [];
        var finPausa_edReg = [];

        var descPausa_edRN = [];
        var pausaInicio_edRN = [];
        var finPausa_edRN = [];
        //NUEVOS EN EDICION
        $('input[name="descPausa_ed[]"]').each(function () {
            descPausa_ed.push($(this).val());
        });
        $('input[name="InicioPausa_ed[]"]').each(function () {
            if ($(this).val() != ' ' || $(this).val() != null) {
                pausaInicio_ed.push($(this).val());
            }

        });
        $('input[name="FinPausa_ed[]"]').each(function () {
            if ($(this).val() != ' ' || $(this).val() != null) {
                finPausa_ed.push($(this).val());
            }
        });
        //ANTIGUOS EDITADO
        $('input[name="idPausasRegistradas_ed[]"]').each(function () {
            ID_edReg.push($(this).val());
        });
        $('input[name="descPausa_edRegist[]"]').each(function () {
            descPausa_edReg.push($(this).val());
        });
        $('input[name="InicioPausa_edReg[]"]').each(function () {
            if ($(this).val() != ' ' || $(this).val() != null) {
                pausaInicio_edReg.push($(this).val());
            }
        });
        $('input[name="FinPausa_edReg[]"]').each(function () {
            if ($(this).val() != ' ' || $(this).val() != null) {
                finPausa_edReg.push($(this).val());
            }
        });
        //NUEVO AGREGADAS EN EDICION
        $('input[name="descPausa_edRN[]"]').each(function () {

            descPausa_edRN.push($(this).val());
        });
        $('input[name="InicioPausa_edRN[]"]').each(function () {
            var valorRNI = $(this).val().length;
            if (valorRNI != 0) {
                pausaInicio_edRN.push($(this).val());
            }
        });
        $('input[name="FinPausa_edRN[]"]').each(function () {
            var valorRNF = $(this).val().length;
            if (valorRNF != 0) {

                finPausa_edRN.push($(this).val());
            }
        });

    }


    /*  COMPARANDO RANGO DE PAUSAS SI ESTA ACTIVADO PAUSAS */
    if ($("#SwitchPausa_ed").is(":checked")) {
        console.log('ENTRANDO');
        /*  UNIENDO ARRAYS DE INICIO DE PAUSAS */
        var unionP1 = pausaInicio_ed.concat(pausaInicio_edReg);
        var unionP2 = unionP1.concat(pausaInicio_edRN);
        console.log('union' + unionP2);

        /*  UNIENDO ARRAYS DE INICIO DE PAUSAS */
        var unionF1 = finPausa_ed.concat(finPausa_edReg);
        var unionF2 = unionF1.concat(finPausa_edRN);
        console.log('union' + unionF2);

        /*  COMPARANDO PAUSAS*/
        $.each(unionP2, function (ind, elem) {
            if (ind > 0) {
                if (unionP2.length > 0) {
                    console.log('1' + unionP2[ind] + '2' + unionP2[ind - 1] + '3' + unionF2[ind - 1]);
                    if (unionP2[ind] > unionP2[ind - 1] && unionP2[ind] > unionF2[ind - 1]) {
                        variablesiPau_ed = 1;
                        $('#errorenPausasCruzadas_ed').hide();
                    }
                    else {
                        if (unionP2[ind] < unionP2[ind - 1] && unionP2[ind] < unionF2[ind - 1]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }

                    }
                    if (unionF2[ind] > unionP2[ind - 1] && unionF2[ind] > unionF2[ind - 1]) {
                        if (unionP2[ind] > unionP2[ind - 1] && unionP2[ind] > unionF2[ind - 1]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }

                    }
                    else {
                        if (unionF2[ind] < unionP2[ind - 1] && unionF2[ind] < unionF2[ind - 1]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }
                    }
                }
                if (unionP2.length > 2) {
                    if (unionP2[ind] > unionP2[ind - 1] && unionP2[ind] > unionF2[ind - 1]) {
                        variablesiPau_ed = 1;
                        $('#errorenPausasCruzadas_ed').hide();
                    }
                    else {
                        if (unionP2[ind] < unionP2[ind - 1] && unionP2[ind] < unionF2[ind - 1]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }

                    }
                    if (unionF2[ind] > unionP2[ind - 1] && unionF2[ind] > unionF2[ind - 1]) {
                        if (unionP2[ind] > unionP2[ind - 1] && unionP2[ind] > unionF2[ind - 1]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }

                    }
                    else {
                        if (unionF2[ind] < unionP2[ind - 1] && unionF2[ind] < unionF2[ind - 1]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }
                    }
                    ///////////////////////////////
                    if (unionP2[ind] > unionP2[ind - 2] && unionP2[ind] > unionF2[ind - 2]) {
                        variablesiPau_ed = 1;
                        $('#errorenPausasCruzadas_ed').hide();
                    }
                    else {
                        if (unionP2[ind] < unionP2[ind - 2] && unionP2[ind] < unionF2[ind - 2]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }

                    }
                    if (unionF2[ind] > unionP2[ind - 2] && unionF2[ind] > unionF2[ind - 2]) {
                        if (unionP2[ind] > unionP2[ind - 2] && unionP2[ind] > unionF2[ind - 2]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }

                    }
                    else {
                        if (unionF2[ind] < unionP2[ind - 2] && unionF2[ind] < unionF2[ind - 2]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }
                    }
                }

                if (unionP2.length > 3) {
                    if (unionP2[ind] > unionP2[ind - 1] && unionP2[ind] > unionF2[ind - 1]) {
                        variablesiPau_ed = 1;
                        $('#errorenPausasCruzadas_ed').hide();
                    }
                    else {
                        if (unionP2[ind] < unionP2[ind - 1] && unionP2[ind] < unionF2[ind - 1]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }

                    }
                    if (unionF2[ind] > unionP2[ind - 1] && unionF2[ind] > unionF2[ind - 1]) {
                        if (unionP2[ind] > unionP2[ind - 1] && unionP2[ind] > unionF2[ind - 1]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }

                    }
                    else {
                        if (unionF2[ind] < unionP2[ind - 1] && unionF2[ind] < unionF2[ind - 1]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }
                    }
                    ///////////////////////////////
                    if (unionP2[ind] > unionP2[ind - 2] && unionP2[ind] > unionF2[ind - 2]) {
                        variablesiPau_ed = 1;
                        $('#errorenPausasCruzadas_ed').hide();
                    }
                    else {
                        if (unionP2[ind] < unionP2[ind - 2] && unionP2[ind] < unionF2[ind - 2]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }

                    }
                    if (unionF2[ind] > unionP2[ind - 2] && unionF2[ind] > unionF2[ind - 2]) {
                        if (unionP2[ind] > unionP2[ind - 2] && unionP2[ind] > unionF2[ind - 2]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }

                    }
                    else {
                        if (unionF2[ind] < unionP2[ind - 2] && unionF2[ind] < unionF2[ind - 2]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }
                    }

                    if (unionP2[ind] > unionP2[ind - 3] && unionP2[ind] > unionF2[ind - 3]) {
                        variablesiPau_ed = 1;
                        $('#errorenPausasCruzadas_ed').hide();
                    }
                    else {
                        if (unionP2[ind] < unionP2[ind - 3] && unionP2[ind] < unionF2[ind - 3]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }

                    }
                    if (unionF2[ind] > unionP2[ind - 3] && unionF2[ind] > unionF2[ind - 3]) {
                        if (unionP2[ind] > unionP2[ind - 3] && unionP2[ind] > unionF2[ind - 3]) {
                            variablesiPau_ed = 1;
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }

                    }
                    else {
                        if (unionF2[ind] < unionP2[ind - 3] && unionF2[ind] < unionF2[ind - 3]) {
                            console.log(unionF2[ind] + '-' + unionP2[ind - 3] + '-' + unionF2[ind] + '-' + unionF2[ind - 3])
                            variablesiPau_ed = 1;
                            console.log('leng' + unionF2.length)
                            $('#errorenPausasCruzadas_ed').hide();
                        } else {
                            variablesiPau_ed = 0;
                            $('#errorenPausasCruzadas_ed').show();

                        }
                    }
                }



            }
            esperandoP_ed = 1;
        });
        console.log('sila' + variablesiPau_ed);
        if (variablesiPau_ed == 1 && esperandoP_ed == 1) {
            $.ajax({
                type: "post",
                url: "/horario/actualizarhorario",
                data: {
                    idhorario, descried, toleed, horaIed, horaFed, tardanza_ed,
                    toleranciaFed, horaObed, descPausa_ed, pausaInicio_ed, finPausa_ed, ID_edReg, descPausa_edReg,
                    pausaInicio_edReg, finPausa_edReg, descPausa_edRN, pausaInicio_edRN, finPausa_edRN
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
    }
    else {
        $.ajax({
            type: "post",
            url: "/horario/actualizarhorario",
            data: {
                idhorario, descried, toleed, horaIed, horaFed, tardanza_ed,
                toleranciaFed, horaObed, descPausa_ed, pausaInicio_ed, finPausa_ed, ID_edReg, descPausa_edReg,
                pausaInicio_edReg, finPausa_edReg, descPausa_edRN, pausaInicio_edRN, finPausa_edRN
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
// $(function () {
//     $(document).on('change', '#horaF', function (event) {
//         let horaF = $('#horaF').val();
//         let horaI = $('#horaI').val();

//         if (horaF < horaI) {
//             $('#divOtrodia').show();
//             $('#horaOblig').prop("disabled", false);
//             $('#horaOblig').flatpickr({
//                 enableTime: true,
//                 noCalendar: true,
//                 dateFormat: "H:i",
//                 time_24hr: true
//             });
//             $('#horaOblig').val('');
//             event.stopPropagation();
//         } else {
//             var dateDesde = newDate(horaI.split(":"));
//             var dateHasta = newDate(horaF.split(":"));

//             var minutos = (dateHasta - dateDesde) / 1000 / 60;
//             var horas = Math.floor(minutos / 60);
//             minutos = minutos % 60;

//             $('#horaOblig').prop("disabled", false);

//             if ($('#horaOblig').val() == null || $('#horaOblig').val() == '') {
//                 $('#horaOblig').flatpickr({
//                     enableTime: true,
//                     noCalendar: true,
//                     dateFormat: "H:i",
//                     time_24hr: true,
//                     defaultHour: "8"
//                 });
//                 $('#horaOblig').val("08:00");

//             }

//             $('#divOtrodia').hide();
//         }

//     });
// });
function newDate(partes) {
    var date = new Date(0);
    date.setHours(partes[0]);
    date.setMinutes(partes[1]);
    return date;
}

function prefijo(num) {
    return num < 10 ? ("0" + num) : num;
}
// $(function () {
//     $(document).on('change', '#horaI', function (event) {
//         let horaF = $('#horaF').val();
//         let horaI = $('#horaI').val();

//         if (horaF < horaI) {
//             $('#divOtrodia').show();
//             $('#horaOblig').flatpickr({
//                 enableTime: true,
//                 noCalendar: true,
//                 dateFormat: "H:i",
//                 time_24hr: true
//             });
//             $('#horaOblig').prop("disabled", false);
//             $('#horaOblig').val('');
//             event.stopPropagation();
//         } else {
//             var dateDesde = newDate(horaI.split(":"));
//             var dateHasta = newDate(horaF.split(":"));

//             var minutos = (dateHasta - dateDesde) / 1000 / 60;
//             var horas = Math.floor(minutos / 60);
//             minutos = minutos % 60;

//             $('#horaOblig').prop("disabled", false);
//             /* $('#horaOblig').val(prefijo(horas)); */
//             if ($('#horaOblig').val() == null || $('#horaOblig').val() == '') {
//                 $('#horaOblig').flatpickr({
//                     enableTime: true,
//                     noCalendar: true,
//                     dateFormat: "H:i",
//                     time_24hr: true,
//                     defaultHour: "8"
//                 });
//                 $('#horaOblig').val("08:00");

//             }
//             $('#divOtrodia').hide();
//         }

//     });
// });

// $(function () {
//     $(document).on('change', '#horaF_ed', function (event) {
//         let horaF = $('#horaF_ed').val();
//         let horaI = $('#horaI_ed').val();

//         if (horaF < horaI) {
//             $('#divOtrodia_ed').show();
//             event.stopPropagation();
//         } else {
//             $('#divOtrodia_ed').hide();
//         }

//     });
// });
// $(function () {
//     $(document).on('change', '#horaI_ed', function (event) {
//         let horaF = $('#horaF_ed').val();
//         let horaI = $('#horaI_ed').val();

//         if (horaF < horaI) {
//             $('#divOtrodia_ed').show();
//             event.stopPropagation();
//         } else {
//             $('#divOtrodia_ed').hide();
//         }

//     });
// });
// $('#SwitchPausa').change(function (event) {
//     if ($('#SwitchPausa').prop('checked')) {
//         $('input[name="descPausa[]"]').prop('required', true);
//         $('#InicioPausa').prop('required', true);
//         $('#FinPausa').prop('required', true);
//         $('input[name="descPausa[]"]').prop('required', true);
//         $('input[name="InicioPausa[]"]').prop('required', true);
//         $('input[name="FinPausa[]"]').prop('required', true);
//         $('#divPausa').show();
//         $('.flatpickr-input[readonly]').on('focus', function () {
//             $(this).blur()
//         })
//         $('.flatpickr-input[readonly]').prop('readonly', false);

//     }
//     else {

//         $('input[name="descPausa[]"]').val('');
//         $('input[name="InicioPausa[]"]').val('');
//         $('input[name="FinPausa[]"]').val('');
//         $('#divPausa').hide();
//         $('input[name="descPausa[]"]').prop('required', false);
//         $('input[name="InicioPausa[]"]').prop('required', false);
//         $('input[name="FinPausa[]"]').prop('required', false);
//     }
//     event.preventDefault();
// });

function addField() {

    // ID del elemento div quitandole la palabra "div_" de delante. Pasi asi poder aumentar el número.
    // Esta parte no es necesaria pero yo la utilizaba ya que cada campo de mi formulario tenia un autosuggest,
    // así que dejo como seria por si a alguien le hace falta.
    var clickID = parseInt($(this).parent('div').attr('id').replace('div_', ''));
    /* var clickIDDelete = parseInt($(this).parent('a').attr('id').replace('btnPb_', '')); */
    // Genero el nuevo numero id
    var newID = (clickID + 1);
    var newIDelete = (clickID + 1);
    // Creo un clon del elemento div que contiene los campos de texto
    $newClone = $('#div_' + clickID).clone(true);
    /*  $newCloneDelete = $('#btnPb_' + clickID).clone(true); */
    //Le asigno el nuevo numero id
    $newClone.attr("id", 'div_' + newID);
    /*  $newCloneDelete.attr("id", 'btnPb_' + newIDelete); */
    //Asigno nuevo id al primer campo input dentro del div y le borro cualquier valor
    // que tenga asi no copia lo ultimo que hayas escrito.(igual que antes no es necesario tener un id)
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

            if ($('#horaI').val() > $('#horaF').val()) {
                if (horaF < $('#horaI').val() && horaF > $('#horaF').val()) {
                    $('#FinPausa' + newID).val('');
                    $('#fueraRango').show();
                    event.stopPropagation();
                } else {
                    $('#fueraRango').hide();
                    if (horaI >= horaF && horaF <= $('#horaI').val() && horaF > $('#horaF').val()) {
                        $('#errorenPausas').show();
                        $('#FinPausa' + newID).val('');
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
            } else {
                if (horaF < $('#horaI').val() || horaF > $('#horaF').val()) {
                    $('#FinPausa' + newID).val('');
                    $('#fueraRango').show();
                    event.stopPropagation();
                } else {
                    $('#fueraRango').hide();
                }

                if (horaF <= horaI) {
                    $('#FinPausa' + newID).val('');
                    $('#errorenPausas').show();
                    event.stopPropagation();
                } else {
                    $('#errorenPausas').hide();
                }
            }





        });
    });
    $(function () {
        $(document).on('change', '#InicioPausa' + newID, function (event) {
            let horaF = $('#FinPausa' + newID).val();
            let horaI = $('#InicioPausa' + newID).val();
            $('#FinPausa' + newID).prop("disabled", false);
            if ($('#horaI').val() > $('#horaF').val()) {

                if (horaI < $('#horaI').val() && horaI > $('#horaF').val()) {

                    $('#InicioPausa' + newID).val('');
                    $('#fueraRango').show();

                    event.stopPropagation();
                } else {
                    $('#fueraRango').hide();
                }

            } else {
                if (horaI < $('#horaI').val() || horaI > $('#horaF').val()) {

                    $('#InicioPausa' + newID).val('');
                    $('#fueraRango').show();
                    event.stopPropagation();
                } else {
                    $('#fueraRango').hide();
                }
            }


            if (horaF == null || horaF == '') {

            } else {
                if ($('#horaI').val() < $('#horaF').val()) {
                    if (horaF <= horaI) {
                        $('#InicioPausa' + newID).val('');
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

    /*  $newClone.children("input").eq(3).attr("id",'PROVECONT_email'+newID).val(''); */
    //Asigno nuevo id al boton
    $newClone.children("button").attr("id", newID)
    $newClone.children("a").attr("id", 'btnPb_' + newID)
    //Inserto el div clonado y modificado despues del div original
    $newClone.insertAfter($('#div_' + clickID));
    //Cambio el signo "+" por el signo "-" y le quito el evento addfield
    //$("#"+clickID-1).remove();
    /*  $("#" + clickID).css("backgroundColor", "#f6cfcf");
     $("#" + clickID).css("border-Color", "#f6cfcf");
     $("#" + clickID).css("color", "#d11010");
     $("#" + clickID).css("height", "22px");
     $("#" + clickID).css("font-weight", "600");
     $("#" + clickID).css("margin-top", "5px");
     $("#" + clickID).css("font-size", "12px");*/
    //////////////

    $("#btnPb_" + clickID - 1).remove();
    var clicmas = clickID;
    $("#" + clicmas + ".bt_plus").remove();
    $("#" + clickID).css("width", "19px");
    $("#" + clickID).css("margin-left", "20-px");
    $('input[name="descPausa[]"]').prop('required', true);
    $('input[name="InicioPausa[]"]').prop('required', true);
    $('input[name="FinPausa[]"]').prop('required', true);
    /*  $("#" + clickID).html('<img src="admin/images/delete.svg" height="15">').unbind("click", addField); */
    $('.flatpickr-input[readonly]').on('focus', function () {
        $(this).blur()
    })
    $('.flatpickr-input[readonly]').prop('readonly', false)
    //Ahora le asigno el evento delRow para que borre la fial en caso de hacer click
    $("#btnPb_" + clickID).bind("click", delRow);
    $("#idPausaMayor").val(clickID + 1);
}
function delRow() {
    // Funcion que destruye el elemento actual una vez echo el click
    $(this).parent('div').remove();
    var elementoborradoID = $(this).parent('button').attr("id");
    var elementoborradoIDInput = $("#idPausaMayor").val();
    var eleMasuno = elementoborradoIDInput + 1;

    if (elementoborradoIDInput = elementoborradoID) {

        $('#inputPausa').append('<button class="btn btn-sm" id="btnnuevoDivPausas" onclick="nuevoDivPausas(' + eleMasuno + ')" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
            ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>');

    }


    if ($('#inputPausa').is(':empty')) {
        $('#inputPausa').append('<button class="btn btn-sm" id="btnnuevoDivPausas" onclick="nuevoDivPausas(100)" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
            ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>');
    }
    $(".bt_plus").each(function (el) {
        $(this).bind("click", addField);
    });
}
function nuevoDivPausas(npa) {
    $('#btnnuevoDivPausas').hide();
    $('#inputPausa').append('<div id="div_' + npa + '" class="row col-md-12" style=" margin-bottom: 8px;">' +
        '<input type="text"  class="form-control form-control-sm col-sm-5" name="descPausa[]" id="descPausa" >' +
        '<input type="text"  class="form-control form-control-sm col-sm-3" name="InicioPausa[]"  id="InicioPausa" >' +
        '<input type="text"  class="form-control form-control-sm col-sm-3" name="FinPausa[]"  id="FinPausa" disabled >' +
        '&nbsp; <a style="cursor: pointer" id="btnPb_' + npa + '" ><img src="/admin/images/delete.svg" height="15"></a><button class="btn btn-sm bt_plus" id="' + npa + '" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
        ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>' +
        '</div>');
    $("#btnPb_" + npa).bind("click", delRow);
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
        defaultHour: null
    });

    $('input[name="descPausa[]"]').prop('required', false);
    $('input[name="InicioPausa[]"]').prop('required', false);
    $('input[name="FinPausa[]"]').prop('required', false);
}
$('#SwitchPausa_ed').change(function (event) {
    if ($('input[name="descPausa_edRegist[]"]').length) {
        $('#SwitchPausa_ed').prop('checked', true);
        bootbox.confirm({
            title: "Eliminar pausas de horario",
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
                    var valorHorario = $('#idhorario_ed').val();
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
        if ($('input[name="descPausa_edRegist[]"]').length) { }
        else {
            $("#PausasHorar_ed").append('<div id="divEd_100" class="row col-md-12" style=" margin-bottom: 8px;">' +
                '<input type="text"  class="form-control form-control-sm col-sm-5" name="descPausa_ed[]" id="descPausa_ed" >' +
                '<input type="text"   class="form-control form-control-sm col-sm-3" name="InicioPausa_ed[]"  id="InicioPausa_ed" >' +
                '<input type="text" class="form-control form-control-sm col-sm-3" name="FinPausa_ed[]"  id="FinPausa_ed" >' +
                '&nbsp; <a style="cursor: pointer" id="btnPbed_100" ><img src="/admin/images/delete.svg" height="15"></a> <button class="btn btn-sm bt_plus_ed" id="ed_100" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
                ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>' +
                '</div>'
            );
            $("#btnPbed_100").bind("click", delRow_ed);
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
            let horafinal = $('#horaF_ed').val();
            splih = horafinal.split(":");

            $('#FinPausa_ed').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: splih[0]
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
    var horafinalED = $('#horaF_ed').val();
    splihED = horafinalED.split(":");

    $newClone.children("input").eq(2).attr("id", 'FinPausa_ed' + newID).val('').prop('required', true).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour: splihED[0]
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
                if (horaI >= horaF && horaF <= $('#horaI_ed').val() && horaF > $('#horaF_ed').val()) {
                    $('#errorenPausas_ed').show();
                    $('#FinPausa_ed' + newID).val('');
                }
                else {
                    $('#errorenPausas_ed').hide();
                }
            }

            if (horaF <= horaI) {
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
                $('#fueraRango_ed').show();
                event.stopPropagation();
            } else {
                $('#fueraRango_ed').hide();
            }

            if (horaF == null || horaF == '') {

            } else {
                if (horaF <= horaI) {
                    $('#InicioPausa_ed' + newID).val('');
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
    $newClone.children("button").attr("id", 'ed_' + newID)
    $newClone.children("a").attr("id", 'btnPbed_' + newID)
    //Inserto el div clonado y modificado despues del div original
    $newClone.insertAfter($('#divEd_' + clickID));
    //Cambio el signo "+" por el signo "-" y le quito el evento addfield
    //$("#"+clickID-1).remove();
    /*  $("#ed_" + clickID).css("backgroundColor", "#f6cfcf");
     $("#ed_" + clickID).css("border-Color", "#f6cfcf");
     $("#ed_" + clickID).css("color", "#d11010");
     $("#ed_" + clickID).css("height", "22px");
     $("#ed_" + clickID).css("font-weight", "600");
     $("#ed_" + clickID).css("margin-top", "5px");
     $("#ed_" + clickID).css("font-size", "12px"); */
    $("#ed_" + clickID).css("width", "19px");
    $("#ed_" + clickID).css("margin-left", "20-px");
    $('input[name="descPausa_ed[]"]').prop('required', true);
    $('input[name="InicioPausa_ed[]"]').prop('required', true);
    $('input[name="FinPausa_ed[]"]').prop('required', true);
    $("#btnPbed_" + clickID - 1).remove();
    var clicmased = clickID;
    $("#ed_" + clicmased + ".bt_plus_ed").remove();
    /* $("#ed_" + clickID).html('<img src="admin/images/delete.svg" height="15">').unbind("click", addField_ed); */
    $('.flatpickr-input[readonly]').on('focus', function () {
        $(this).blur()
    })
    $('.flatpickr-input[readonly]').prop('readonly', false)
    //Ahora le asigno el evento delRow para que borre la fial en caso de hacer click
    $("#ed_" + clickID).bind("click", delRow_ed);
    $("#btnPbed_" + clickID).bind("click", delRow_ed);
    $("#idPausaMayor_ed").val(clickID + 1);
}
function delRow_ed() {

    // Funcion que destruye el elemento actual una vez echo el click
    $(this).parent('div').remove();
    var idDiv_ed = 'idDiv' + $(this).parent('div').attr("id");
    var nuevoId_edit = idDiv_ed.slice(-3);
    var idpausa_editM = $("#idPausaMayor_ed").val();


    if ($('#PausasHorar_ed').is(':empty')) {
        $('#PausasHorar_ed').append('<button class="btn btn-sm" id="btnnuevoDivPausas_ed" onclick="nuevoDivPausas_ed(100)" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
            ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>');
    }
    else {
        if (idpausa_editM == nuevoId_edit) {
            $('#PausasHorar_ed').append('<button class="btn btn-sm" id="btnnuevoDivPausas_ed" onclick="nuevoDivPausas_ed(' + nuevoId_edit + ')" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
                ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>');
        }
    }

    $(".bt_plus").each(function (el) {
        $(this).bind("click", addField);
    });
}
function nuevoDivPausas_ed(npa) {
    $('#btnnuevoDivPausas_ed').hide();
    $("#PausasHorar_ed").append('<div id="divEd_' + npa + '" class="row col-md-12" style=" margin-bottom: 8px;">' +
        '<input type="text"  class="form-control form-control-sm col-sm-5" name="descPausa_ed[]" id="descPausa_ed" >' +
        '<input type="text"   class="form-control form-control-sm col-sm-3" name="InicioPausa_ed[]"  id="InicioPausa_ed" >' +
        '<input type="text" class="form-control form-control-sm col-sm-3" name="FinPausa_ed[]"  id="FinPausa_ed" >' +
        '&nbsp; <a style="cursor: pointer" id="btnPbed_' + npa + '" ><img src="/admin/images/delete.svg" height="15"></a> <button class="btn btn-sm bt_plus_ed" id="ed_' + npa + '" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
        ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>' +
        '</div>'
    );
    $("#btnPbed_" + npa).bind("click", delRow_ed);
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
    let horafinal = $('#horaF_ed').val();
    splih = horafinal.split(":");

    $('#FinPausa_ed').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour: splih[0]
    });

    $('input[name="descPausa_ed[]"]').prop('required', true);
    $('#InicioPausa_ed').prop('required', true);
    $('#FinPausa_ed').prop('required', true);
}
/////////////////////////
function addField_edNR() {


    // ID del elemento div quitandole la palabra "div_" de delante. Pasi asi poder aumentar el número.
    // Esta parte no es necesaria pero yo la utilizaba ya que cada campo de mi formulario tenia un autosuggest,
    // así que dejo como seria por si a alguien le hace falta.
    var clickID = parseInt($(this).parent('div').attr('id').replace('divEdRN_', ''));
    // Genero el nuevo numero id
    var newID = (clickID + 1);
    // Creo un clon del elemento div que contiene los campos de texto
    $newClone = $('#divEdRN_' + clickID).clone(true);
    //Le asigno el nuevo numero id
    $newClone.attr("id", 'divEdRN_' + newID);

    //Asigno nuevo id al primer campo input dentro del div y le borro cualquier valor
    // que tenga asi no copia lo ultimo que hayas escrito.(igual que antes no es necesario tener un id)
    $newClone.children("input").eq(0).attr("id", 'descPausa_edRN' + newID).val('');
    //Borro el valor del segundo campo input(este caso es el campo de cantidad)
    $newClone.children("input").eq(1).attr("id", 'InicioPausa_edRN' + newID).val('').prop('required', true).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });
    var horafinalEDRN = $('#horaF_ed').val();
    splihEDRN = horafinalEDRN.split(":");

    $newClone.children("input").eq(2).attr("id", 'FinPausa_edRN' + newID).val('').prop('required', true).flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultHour: splihEDRN[0]
    });;
    $(function () {
        $(document).on('change', '#FinPausa_edRN' + newID, function (event) {
            let horaF = $('#FinPausa_edRN' + newID).val();
            let horaI = $('#InicioPausa_edRN' + newID).val();

            if (horaF < $('#horaI_ed').val() || horaF > $('#horaF_ed').val()) {
                $('#FinPausa_edRN' + newID).val('');
                $('#fueraRango_ed').show();
                event.stopPropagation();
            } else {
                $('#fueraRango_ed').hide();
                if (horaI >= horaF && horaF <= $('#horaI_ed').val() && horaF > $('#horaF_ed').val()) {
                    $('#errorenPausas_ed').show();
                    $('#FinPausa_edRN' + newID).val('');
                }
                else {
                    $('#errorenPausas_ed').hide();
                }
            }

            if (horaF <= horaI) {
                $('#FinPausa_edRN' + newID).val('');
                $('#errorenPausas_ed').show();
                event.stopPropagation();
            } else {
                $('#errorenPausas_ed').hide();
            }



        });
    });
    $(function () {
        $(document).on('change', '#InicioPausa_edRN' + newID, function (event) {
            let horaF = $('#FinPausa_edRN' + newID).val();
            let horaI = $('#InicioPausa_edRN' + newID).val();
            $('#FinPausa_edRN' + newID).prop("disabled", false);
            if (horaI < $('#horaI_ed').val() || horaI > $('#horaF_ed').val()) {

                $('#InicioPausa_edRN' + newID).val('');
                $('#fueraRango_ed').show();
                event.stopPropagation();
            } else {
                $('#fueraRango_ed').hide();
            }

            if (horaF == null || horaF == '') {

            } else {
                if (horaF <= horaI) {
                    $('#InicioPausa_edRN' + newID).val('');
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
    $newClone.children("button").attr("id", 'edA_' + newID)
    $newClone.children("a").attr("id", 'btnPbedA_' + newID)
    //Inserto el div clonado y modificado despues del div original
    $newClone.insertAfter($('#divEdRN_' + clickID));
    //Cambio el signo "+" por el signo "-" y le quito el evento addfield
    //$("#"+clickID-1).remove();
    /*  $("#edA_" + clickID).css("backgroundColor", "#f6cfcf");
     $("#edA_" + clickID).css("border-Color", "#f6cfcf");
     $("#edA_" + clickID).css("color", "#d11010");
     $("#edA_" + clickID).css("height", "22px");
     $("#edA_" + clickID).css("font-weight", "600");
     $("#edA_" + clickID).css("margin-top", "5px");
     $("#edA_" + clickID).css("font-size", "12px"); */
    $("#edA_" + clickID).css("width", "19px");
    $("#edA_" + clickID).css("margin-left", "20-px");
    $('input[name="descPausa_edRN[]"]').prop('required', true);
    $('input[name="InicioPausa_edRN[]"]').prop('required', true);
    $('input[name="FinPausa_edRN[]"]').prop('required', true);
    $("#btnPbedA_" + clickID - 1).remove();
    var clicmased = clickID;
    $("#edA_" + clicmased + ".bt_plus_edAgr").remove();

    $('.flatpickr-input[readonly]').on('focus', function () {
        $(this).blur()
    })
    $('.flatpickr-input[readonly]').prop('readonly', false)
    //Ahora le asigno el evento delRow para que borre la fial en caso de hacer click
    $("#edA_" + clickID).bind("click", delRow_edRN);
    $("#btnPbedA_" + clickID).bind("click", delRow_edRN);
    $("#idPausaMayor_edA").val(clickID + 1);
}
function delRow_edRN() {
    // Funcion que destruye el elemento actual una vez echo el click
    $(this).parent('div').remove();
    var idDiv_ed = 'idDiv' + $(this).parent('div').attr("id");
    var nuevoId_edit = idDiv_ed.slice(-3);
    var idpausa_editM = $("#idPausaMayor_edA").val();


    /*   if($('#PausasHorar_ed').is(':empty')){
       $('#PausasHorar_ed').append('<button class="btn btn-sm" id="btnnuevoDivPausas_ed" onclick="nuevoDivPausas_ed(100)" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;padding-top: 0px;' +
       ' padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px; margin-top: 5px;margin-left: 20px">+</button>');
   } */
}
////////////////////////
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
                if (horaI >= horaF && horaF <= $('#horaI').val() && horaF > $('#horaF').val()) {
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
            if (horaF <= horaI) {
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


        if (horaF == null || horaF == '') {
            var horafinal1 = $('#horaF').val();
            splih1 = horafinal1.split(":");

            $('#FinPausa').val('').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: splih1[0]
            });

        }
        else {

            if ($('#horaI').val() < $('#horaF').val()) {
                if (horaF <= horaI) {
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

        $('#FinPausa').on('focus', function () {
            $(this).blur();
        })
        $('#FinPausa').removeAttr("readonly");
    });
});


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
            if (horaI >= horaF && horaF <= $('#horaI_ed').val() && horaF > $('#horaF_ed').val()) {
                $('#errorenPausas_ed').show();
                $('#FinPausa_ed').val('');
            }
            else {
                $('#errorenPausas_ed').hide();
            }
        }
        if (horaF <= horaI) {
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

        if (horaF == null || horaF == '') {

        }
        else {

            if (horaF <= horaI) {
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


        } else {
            $('#nHorasAdic').hide();

        }

    });
});
function eliminarPH(idpausa) {
    bootbox.confirm({
        title: "Eliminar pausa",
        message: "¿Desea eliminar la pausa del horario?",
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
                    url: "/eliminarPausaHorario",
                    data: {
                        idpausa
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
                        $("#divEdReg_" + idpausa + "> input.form-control").remove();
                        $("#btnEli_edReg" + idpausa).remove();
                        ///cambiar id de pausa o eliminar para q no registree
                    },
                    error: function (data) {
                        alert('Ocurrio un error');
                    }


                });
            }
        }
    });


}


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
        `<div class="row pb-3" id="rowP${r_cont}">
                <input type="hidden" class="rowInputs" value="${r_cont}">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text"  class="form-control form-control-sm descP" id="descPausa${r_cont}"
                              onkeyup="javascript:$(this).removeClass('borderColor');$('#btnGuardaHorario').prop('disabled', false);">
                        </div>
                        <div class="col-md-2">
                            <input type="text"  class="form-control form-control-sm inicioP" id="InicioPausa${r_cont}" name="inicioP"
                                onchange="javascript:$(this).removeClass('borderColor');$('#btnGuardaHorario').prop('disabled', false);">
                        </div>
                        <div class="col-md-2">
                            <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                <input type="number"  class="form-control form-control-sm" id="toleranciaIP${r_cont}" value="0"
                                    oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;"
                                    onchange="javascript:toleranciasValidacion()">
                                <div class="input-group-prepend  ">
                                    <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                        min.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <input type="text"  class="form-control form-control-sm finP" id="FinPausa${r_cont}" name="finP"
                                onchange="javascript:$(this).removeClass('borderColor');$('#btnGuardaHorario').prop('disabled', false);">
                        </div>
                        <div class="col-md-2">
                            <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                <input type="number"  class="form-control form-control-sm" id="ToleranciaFP${r_cont}" value="0"
                                    oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;"
                                    onchange="javascript:toleranciasValidacion()">
                                <div class="input-group-prepend  ">
                                    <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                        min.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <input type="checkbox" id="inactivarPausa${r_cont}" class="mt-2 ml-2">
                        </div>
                        <div class="col-md-1">
                            <a style="cursor: pointer" onclick="javascript:eliminarContenido(${r_cont})">
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
            // ? -> TIEMPOS DE INPUTS
            var horaI = moment($('#InicioPausa' + idI).val(), ["HH:mm"]);
            var horaF = moment($('#FinPausa' + idI).val(), ["HH:mm"]);
            // ? -> TIEMPOS DE HORARIO
            var momentInicio = moment($('#horaI').val(), ["HH:mm"]);
            var momentFin = moment($('#horaF').val(), ["HH:mm"]);
            // * VALIDACION CON TIEMPOS DE HORARIO
            // * VALIDACION ENTRE PAUSAS
            $('.rowInputs').each(function () {
                var idC = $(this).val();
                if (idI != idC) {
                    if ($('#descPausa' + idC).val() != "" && $('#InicioPausa' + idC).val() != "" && $('#FinPausa' + idC).val() != "") {
                        var horaCompararI = moment($('#InicioPausa' + idC).val(), ["HH:mm"]);
                        var horaCompararF = moment($('#FinPausa' + idC).val(), ["HH:mm"]);
                        if (momentFin.isSameOrBefore(momentInicio)) {   //: -> <=
                            var nuevoF = momentFin.add(1, 'day');    //: -> NUEVA FECHA
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
    $('.rowInputs').each(function () {
        var id = $(this).val();
        if ($('#rowP' + id).is(":visible")) {
            resp = false;
        }
    });
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
        if ($('#inactivarPausa' + id).is(":checked")) {
            inactivarP = 1;
        } else {
            inactivarP = 0;
        }
        var objPausa = {
            "id": $(this).val(),
            "descripcion": descripcion,
            "inicioPausa": inicioPausa,
            "toleranciaI": toleranciaPI,
            "finPausa": finPausa,
            "toleranciaF": toleranciaPF,
            "inactivar": inactivarP
        };
        resultado.push(objPausa);
    });

    return resultado;
}
function registrarNuevoHorario() {
    var descripcion = $('#descripcionCa').val();
    var horaInicio = $('#horaI').val();
    var horaFin = $('#horaF').val();
    var toleranciaI = $('#toleranciaH').val();
    var toleranciaF = $('#toleranciaSalida').val();
    var horasO = $('#horaOblig').val();
    var tardanza;
    var pausas = obtenerPausas();
    console.log(pausas);
    if ($('#SwitchTardanza').is(":checked")) {
        tardanza = 1;
    } else {
        tardanza = 0;
    }
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
            tardanza: tardanza,
            pausas: pausas
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
            if ($('#horarioAsignar_ed').is(':visible')) {
                obtenerHorarios();
                $('#selectHorario').val(data).trigger('change');
            }
            $('#horarioAgregar').modal('toggle');
            limpiarHorario();
            $('#tablaEmpleado').DataTable().ajax.reload();
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
    $('#SwitchTardanza').prop("checked", false);
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
            console.log(data);
            $('#idhorario_ed').val(data.horario[0].horario_id);
            $('#descripcionCa_ed').val(data.horario[0].horario_descripcion);
            e_horaInicio.setDate(data.horario[0].horaI);
            e_horaFin.setDate(data.horario[0].horaF);
            e_horaOb.setDate(data.horario[0].horasObliga);
            $('#toleranciaH_ed').val(data.horario[0].horario_tolerancia);
            $('#toleranciaSalida_ed').val(data.horario[0].horario_toleranciaF);
            // * TARDANZA
            if (data.horario[0].hora_contTardanza == 1) {
                $('#SwitchTardanza_ed').prop("checked", true);
            } else {
                $('#SwitchTardanza_ed').prop("checked", false);
            }
            // * PAUSAS
            if (data.pausas.length != 0) {
                $('#SwitchPausa_ed').prop("checked", true);
                $('#PausasHorar_ed').empty();
                var contenido = "";
                for (let index = 0; index < data.pausas.length; index++) {
                    var pausa = data.pausas[index];
                    console.log(pausa);
                    contenido +=
                        `<div class="row pb-3" id="e_rowP${pausa.idpausas_horario}">
                            <input type="hidden" class="e_rowInputs" value="${pausa.idpausas_horario}">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2">
                                        <input type="text"  class="form-control form-control-sm descP" id="e_descPausa${pausa.idpausas_horario}"
                                          value="${pausa.pausH_descripcion}"onkeyup="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text"  class="form-control form-control-sm inicioP" id="e_InicioPausa${pausa.idpausas_horario}" name="inicioP"
                                          value="${pausa.pausH_Inicio}"  onchange="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                            <input type="number"  class="form-control form-control-sm" id="e_toleranciaIP${pausa.idpausas_horario}" value="${pausa.tolerancia_inicio}" 
                                               oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;" onchange="javascript:e_toleranciasValidacion()">
                                            <div class="input-group-prepend  ">
                                                <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                    min.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text"  class="form-control form-control-sm finP" id="e_FinPausa${pausa.idpausas_horario}" name="finP"
                                           value="${pausa.pausH_Fin}" onchange="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                            <input type="number"  class="form-control form-control-sm" id="e_ToleranciaFP${pausa.idpausas_horario}" value="${pausa.tolerancia_fin}" 
                                               oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;" onchange="javascript:e_toleranciasValidacion()">
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
                                        <input type="checkbox" id="e_inactivarPausa${pausa.idpausas_horario}" class="mt-2 ml-2">
                                    </div>`;
                    } else {
                        contenido += `
                                    <div class="col-md-1">
                                        <input type="checkbox" id="e_inactivarPausa${pausa.idpausas_horario}" class="mt-2 ml-2" checked>
                                    </div>`;
                    }
                    contenido += `
                                    <div class="col-md-1">
                                        <a style="cursor: pointer" onclick="javascript:e_eliminarContenido(${pausa.idpausas_horario})">
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
        `<div class="row pb-3" id="e_rowPNew${e_cont}">
                <input type="hidden" class="e_rowInputs" value="New${e_cont}">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text"  class="form-control form-control-sm descP" id="e_descPausaNew${e_cont}"
                              onkeyup="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                        </div>
                        <div class="col-md-2">
                            <input type="text"  class="form-control form-control-sm inicioP" id="e_InicioPausaNew${e_cont}" name="inicioP"
                                onchange="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                        </div>
                        <div class="col-md-2">
                            <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                <input type="number"  class="form-control form-control-sm" id="e_toleranciaIPNew${e_cont}" value="0"
                                    oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;" 
                                    onchange="javascript:e_toleranciasValidacion()">
                                <div class="input-group-prepend  ">
                                    <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                        min.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <input type="text"  class="form-control form-control-sm finP" id="e_FinPausaNew${e_cont}" name="finP"
                                onchange="javascript:$(this).removeClass('borderColor');$('#btnEditarHorario').prop('disabled', false);">
                        </div>
                        <div class="col-md-2">
                            <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                <input type="number"  class="form-control form-control-sm" id="e_ToleranciaFPNew${e_cont}" value="0"
                                    oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;" 
                                    onchange="javascript:e_toleranciasValidacion()">
                                <div class="input-group-prepend">
                                    <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                        min.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <input type="checkbox" id="e_inactivarPausaNew${e_cont}" class="mt-2 ml-2">
                        </div>
                        <div class="col-md-1">
                            <a style="cursor: pointer" onclick="javascript:e_eliminarContenido('New${e_cont}')">
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
            console.log(horaI, horaF);
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
    $('.e_rowInputs').each(function () {
        var id = $(this).val();
        if ($('#e_rowP' + id).is(":visible")) {
            resp = false;
        }
    });
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
                        `<div class="row pb-3" id="e_rowP${pausa.idpausas_horario}">
                        <input type="hidden" class="e_rowInputs" value="${pausa.idpausas_horario}">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2">
                                    <input type="text"  class="form-control form-control-sm descP" id="e_descPausa${pausa.idpausas_horario}"
                                      value="${pausa.pausH_descripcion}"onkeyup="javascript:$(this).removeClass('borderColor');">
                                </div>
                                <div class="col-md-2">
                                    <input type="text"  class="form-control form-control-sm inicioP" id="e_InicioPausa${pausa.idpausas_horario}" name="inicioP"
                                      value="${pausa.pausH_Inicio}"  onchange="javascript:$(this).removeClass('borderColor');">
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                        <input type="number"  class="form-control form-control-sm" id="e_toleranciaIP${pausa.idpausas_horario}" value="0"
                                           value="${pausa.tolerancia_inicio}" oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;">
                                        <div class="input-group-prepend  ">
                                            <div class="input-group-text form-control-sm" style="height: calc(1.5em + 0.43em + 5.2px)!important; font-size: 12px">
                                                min.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <input type="text"  class="form-control form-control-sm finP" id="e_FinPausa${pausa.idpausas_horario}" name="finP"
                                       value="${pausa.pausH_Fin}" onchange="javascript:$(this).removeClass('borderColor');">
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group form-control-sm" style="bottom: 3.8px;padding-left: 0px; padding-right: 0px;">
                                        <input type="number"  class="form-control form-control-sm" id="e_ToleranciaFP${pausa.idpausas_horario}" value="0"
                                           value="${pausa.tolerancia_fin}" oninput="javascript: if (this.value >= 60 || this.value < 0) this.value = 59;">
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
                                    <input type="checkbox" id="e_inactivarPausa${pausa.idpausas_horario}" class="mt-2 ml-2">
                                </div>`;
                    } else {
                        contenido += `
                                <div class="col-md-1">
                                    <input type="checkbox" id="e_inactivarPausa${pausa.idpausas_horario}" class="mt-2 ml-2" checked>
                                </div>`;
                    }
                    contenido += `
                                <div class="col-md-1">
                                    <a style="cursor: pointer" onclick="javascript:e_eliminarContenido(${pausa.idpausas_horario})">
                                        <img src="/admin/images/delete.svg" height="15">
                                    </a>
                                </div>
                            </div>
                            <button class="btn btn-sm bt_plus" type="button" style="background-color:#e2e7f1; color:#546483;font-weight: 600;
                                padding-top: 0px;padding-bottom: 0px; font-size: 12px; padding-right: 5px; padding-left: 5px;height: 22px;
                                    margin-top: 5px;margin-left: 20px" onclick="javascript:e_contenidoInput(${pausa.idpausas_horario})" id="e_agregar${pausa.idpausas_horario}">
                                +
                            </button>
                        </div>
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
        } else {
            $('#pausas_edit').hide();
            $('#PausasHorar_ed').empty();
            $('#vacioHoraF_ed').show();
            $('#SwitchPausa_ed').prop("checked", false);
        }
    } else {
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
        if ($('#e_inactivarPausa' + id).is(":checked")) {
            inactivarP = 1;
        } else {
            inactivarP = 0;
        }
        var objPausa = {
            "id": $(this).val(),
            "descripcion": descripcion,
            "inicioPausa": inicioPausa,
            "toleranciaI": toleranciaPI,
            "finPausa": finPausa,
            "toleranciaF": toleranciaPF,
            "inactivar": inactivarP
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
    var tardanza;
    var pausas = e_obtenerPausas();
    if ($('#SwitchTardanza_ed').is(":checked")) {
        tardanza = 1;
    } else {
        tardanza = 0;
    }
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
            tardanza: tardanza,
            pausas: pausas
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
            $('#tablaEmpleado').DataTable().ajax.reload();
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
