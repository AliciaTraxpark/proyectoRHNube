//* FECHA
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "single",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "D, j F",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: true,
});
$(function () {
    $('#idempleado').select2({
        placeholder: 'Seleccionar',
        language: {
            inputTooShort: function (e) {
                return "Escribir nombre o apellido";
            },
            loadingMore: function () { return "Cargando más resultados…" },
            noResults: function () { return "No se encontraron resultados" }
        },
        minimumInputLength: 2
    });
    f = moment().format("YYYY-MM-DD");
    fechaValue.setDate(f);
    $("#fechaInput").change();
    cambiarF();
});


function cargartabla(fecha) {
    var idemp = $('#idempleado').val();
    $.ajax({
        type: "GET",
        url: "/tablaTareo",
        data: {
            fecha, idemp
        },
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
            if (data.length != 0) {
                if ($.fn.DataTable.isDataTable("#tablaReport")) {
                    $("#tablaReport").DataTable().destroy();
                }
                // ! *********** CABEZERA DE TABLA**********
                $('#theadD').empty();
                $('#btnsDescarga').show();
                //* CANTIDAD MININO VALOR DE COLUMNAS PARA HORAS
                var cantidadColumnasHoras = 0;
                for (let i = 0; i < data.length; i++) {
                    //* OBTENER CANTIDAD TOTAL DE COLUMNAS
                    if (cantidadColumnasHoras < data[i].marcaciones.length) {
                        cantidadColumnasHoras = data[i].marcaciones.length;
                    }
                }
                //* ARMAR CABEZERA
                var theadTabla = `<tr>
                                    <th>CC&nbsp;</th>
                                    <th>DNI&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th>Nombre&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th>Cargo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>`;
                for (let j = 0; j < cantidadColumnasHoras; j++) {
                    theadTabla += `<th style="border-left-color: #c8d4de!important;
                    border-left: 2px solid;">Horario</th><th>Actividad</th><th>Hora de entrada</th>
                                    <th>Hora de salida</th>
                                    <th id="tSitio" name="tiempoSitHi">Tiempo en sitio</th><th  name="tiempoSitHi">Tardanza</th>
                                    <th  name="tiempoSitHi">Faltas</th><th  name="tiempoSitHi">Incidencias</th>`;
                }
                theadTabla += `<th>Tiempo total</th> <th >Tardanza T.</th>
                <th >Faltas T.</th>
                <th >Incidencias T.</th> </tr>`;
                //* DIBUJAMOS CABEZERA
                $('#theadD').html(theadTabla);
                // ! *********** BODY DE TABLA**********
                $('#tbodyD').empty();
                var tbody = "";
                //* ARMAMOS BODY DE TABLA
                for (let index = 0; index < data.length; index++) {
                    tbody += `<tr>
                    <td>${(index + 1)}&nbsp;</td>
                    <td>${data[index].emple_nDoc}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>${data[index].perso_nombre} ${data[index].perso_apPaterno} ${data[index].perso_apMaterno}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    if (data[index].cargo_descripcion != null) {
                        tbody += `<td>${data[index].cargo_descripcion}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    } else {
                        tbody += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    }
                    //* ARMAR Y ORDENAR MARCACIONES
                    var tbodyEntradaySalida = "";
                    var sumaTiempos = moment("00:00:00", "HH:mm:ss");
                    //: HORA
                    for (let h = 0; h < 24; h++) {
                        for (let j = 0; j < data[index].marcaciones.length; j++) {
                            var marcacionData = data[index].marcaciones[j];
                            if (marcacionData.entrada != 0) {
                                if (h == moment(marcacionData.entrada).format("HH")) {

                                    if (marcacionData.horario != 0) {
                                        tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;
                                        border-left: 2px solid;">${marcacionData.horarioIni} -${marcacionData.horarioFin} </td>`;

                                    } else {
                                        tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;
                                        border-left: 2px solid;">---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                    }
                                    if (marcacionData.actividad != 0) {
                                        tbodyEntradaySalida += `<td>${marcacionData.actividad}</td>`;

                                    } else {
                                        tbodyEntradaySalida += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                    }

                                        tbodyEntradaySalida += `<td><img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>${moment(marcacionData.entrada).format("HH:mm:ss")}</td>`;


                                    if (marcacionData.salida != 0) {

                                        tbodyEntradaySalida += `<td><img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> ${moment(marcacionData.salida).format("HH:mm:ss")}</td>`;



                                        var horaFinal = moment(marcacionData.salida);
                                        var horaInicial = moment(marcacionData.entrada);
                                        if (horaFinal.isSameOrAfter(horaInicial)) {
                                            var tiempoRestante = horaFinal - horaInicial;
                                            var segundosTiempo = moment.duration(tiempoRestante).seconds();
                                            var minutosTiempo = moment.duration(tiempoRestante).minutes();
                                            var horasTiempo = Math.trunc(moment.duration(tiempoRestante).asHours());
                                            if (horasTiempo < 10) {
                                                horasTiempo = '0' + horasTiempo;
                                            }
                                            if (minutosTiempo < 10) {
                                                minutosTiempo = '0' + minutosTiempo;
                                            }
                                            if (segundosTiempo < 10) {
                                                segundosTiempo = '0' + segundosTiempo;
                                            }
                                            tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                                    <input type="hidden" value= "${horasTiempo}:${minutosTiempo}:${segundosTiempo}" name="tiempoSit${data[index].emple_id}[]" id="tiempoSit${data[index].emple_id}">
                                                                    <a class="badge badge-soft-primary mr-2"><img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                                        ${horasTiempo}:${minutosTiempo}:${segundosTiempo}
                                                                    </a>
                                                                </td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                                            sumaTiempos = moment(sumaTiempos).add(segundosTiempo, 'seconds');
                                            sumaTiempos = moment(sumaTiempos).add(minutosTiempo, 'minutes');
                                            sumaTiempos = moment(sumaTiempos).add(horasTiempo, 'hours');
                                        }
                                    } else {

                                        tbodyEntradaySalida +=`<td><span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>No tiene salida</span></td>`;


                                        tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                            <span class="badge badge-soft-secondary">
                                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                --:--:--
                                                            </span>
                                                        </td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                                    }
                                }
                            } else {
                                if (marcacionData.salida != 0) {
                                    if (h == moment(marcacionData.salida).format("HH")) {
                                        //* COLUMNA DE ENTRADA
                                        if (marcacionData.horario != 0) {
                                            tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;
                                            border-left: 2px solid;">${marcacionData.horarioIni} -${marcacionData.horarioFin} </td>`;
                                        } else {
                                            tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;
                                            border-left: 2px solid;">---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                        }
                                        if (marcacionData.actividad != 0) {
                                            tbodyEntradaySalida += `<td>${marcacionData.actividad}</td>`;

                                        } else {
                                            tbodyEntradaySalida += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                        }



                                        tbodyEntradaySalida += `<td><span class="badge badge-soft-warning"><img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>No tiene entrada</span></td>`;


                                        //* COLUMNA DE SALIDA


                                        tbodyEntradaySalida += `<td><img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> ${moment(marcacionData.salida).format("HH:mm:ss")}</td>`;


                                        tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                            <span class="badge badge-soft-secondary">
                                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                --:--:--
                                                            </span>
                                                        </td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                                    }
                                }
                            }
                        }
                    }
                    for (let m = data[index].marcaciones.length; m < cantidadColumnasHoras; m++) {
                        tbodyEntradaySalida += `<td style="border-left-color: #c8d4de!important;
                        border-left: 2px solid;">---</td><td>---</td><td>---</td><td>---</td><td name="tiempoSitHi">---</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                    }
                    tbody += tbodyEntradaySalida;


                        tbody += `<td id="TiempoTotal${data[index].emple_id}">
                        <a class="badge badge-soft-primary mr-2">
                            <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                            ${sumaTiempos.format("HH:mm:ss")}
                        </a>
                    </td> <td >--</td>
                    <td >--</td>
                    <td >--</td></tr>`;



                }
                $('#tbodyD').html(tbody);


                table = $("#tablaReport").DataTable({
                    "searching": false,
                    "scrollX": true,
                    "ordering": false,
                    "autoWidth": false,
                    "bInfo" : false ,
                    "bLengthChange" : false,
                    fixedHeader: true,
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
                    initComplete: function () {
                        setTimeout(function () { $("#tablaReport").DataTable().draw(); }, 200);
                    },




                });
                $(window).on('resize', function () {
                    $("#tablaReport").css('width', '100%');
                    table.draw(true);
                });
                if ($('#customSwitDetalles').is(':checked')) {
                    $('[name="tiempoSitHi"]').show();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                }
                else {
                    $('[name="tiempoSitHi"]').hide();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                }
            } else {
                $('#btnsDescarga').hide();
                $('#tbodyD').empty();
                $('#tbodyD').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
            }


/* CREANDO TABLAS PARA IMPORTAR */

if (data.length != 0) {

    // ! *********** CABEZERA DE TABLA**********
    $('#theadDI').empty();
    //* CANTIDAD MININO VALOR DE COLUMNAS PARA HORAS
    var cantidadColumnasHorasI = 0;
    for (let i = 0; i < data.length; i++) {
        //* OBTENER CANTIDAD TOTAL DE COLUMNAS
        if (cantidadColumnasHorasI < data[i].marcaciones.length) {
            cantidadColumnasHorasI = data[i].marcaciones.length;
        }
    }
    //* ARMAR CABEZERA
    var theadTablaI = `<tr class="tableHi">
                        <th class="tableHi" >CC&nbsp;</th>
                        <th class="tableHi" >DNI&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th class="tableHi">Nombre&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th class="tableHi">Cargo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>`;
    for (let j = 0; j < cantidadColumnasHorasI; j++) {
        theadTablaI += `<th class="tableHi">Horario </th> <th class="tableHi">Hora de entrada</th>
                        <th class="tableHi">Hora de salida</th>
                        <th class="tableHi" id="tSitioI" name="tiempoSitHi">Tiempo en sitio</th><th  name="tiempoSitHi">Tardanza</th>
                        <th  name="tiempoSitHi">Faltas</th><th  name="tiempoSitHi">Incidencias</th>`;
    }
    theadTablaI += `<th class="tableHi" >Tiempo total</th>  <th >Tardanza T.</th>
    <th >Faltas T.</th>
    <th >Incidencias T.</th> </tr>`;
    //* DIBUJAMOS CABEZERA
    $('#theadDI').html(theadTablaI);
    // ! *********** BODY DE TABLA**********
    $('#tbodyDI').empty();
    var tbodyI = "";
    //* ARMAMOS BODY DE TABLA
    for (let index = 0; index < data.length; index++) {
        tbodyI += `<tr class="tableHi">
        <td class="tableHi">${(index + 1)}&nbsp;</td>
        <td class="tableHi">${data[index].emple_nDoc}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td class="tableHi">${data[index].perso_nombre} ${data[index].perso_apPaterno} ${data[index].perso_apMaterno}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
        if (data[index].cargo_descripcion != null) {
            tbodyI += `<td class="tableHi">${data[index].cargo_descripcion}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
        } else {
            tbodyI += `<td class="tableHi">---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
        }
        //* ARMAR Y ORDENAR MARCACIONES
        var tbodyEntradaySalidaI = "";
        var sumaTiemposI = moment("00:00:00", "HH:mm:ss");
        //: HORA
        for (let h = 0; h < 24; h++) {
            for (let j = 0; j < data[index].marcaciones.length; j++) {
                var marcacionData = data[index].marcaciones[j];
                if (marcacionData.entrada != 0) {
                    if (h == moment(marcacionData.entrada).format("HH")) {

                        if (marcacionData.horario != 0) {
                            tbodyEntradaySalidaI += `<td>${marcacionData.horarioIni} -${marcacionData.horarioFin} </td>`;
                        } else {
                            tbodyEntradaySalidaI += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                        }


                        tbodyEntradaySalidaI += `<td class="tableHi">${moment(marcacionData.entrada).format("HH:mm:ss")}</td>`;


                        if (marcacionData.salida != 0) {

                                tbodyEntradaySalidaI += `<td class="tableHi">${moment(marcacionData.salida).format("HH:mm:ss")}</td>`;


                            var horaFinal = moment(marcacionData.salida);
                            var horaInicial = moment(marcacionData.entrada);
                            if (horaFinal.isSameOrAfter(horaInicial)) {
                                var tiempoRestante = horaFinal - horaInicial;
                                var segundosTiempo = moment.duration(tiempoRestante).seconds();
                                var minutosTiempo = moment.duration(tiempoRestante).minutes();
                                var horasTiempo = Math.trunc(moment.duration(tiempoRestante).asHours());
                                if (horasTiempo < 10) {
                                    horasTiempo = '0' + horasTiempo;
                                }
                                if (minutosTiempo < 10) {
                                    minutosTiempo = '0' + minutosTiempo;
                                }
                                if (segundosTiempo < 10) {
                                    segundosTiempo = '0' + segundosTiempo;
                                }
                                tbodyEntradaySalidaI += `<td class="tableHi" name="tiempoSitHi">

                                                            ${horasTiempo}:${minutosTiempo}:${segundosTiempo}

                                                    </td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                                sumaTiemposI = moment(sumaTiemposI).add(segundosTiempo, 'seconds');
                                sumaTiemposI = moment(sumaTiemposI).add(minutosTiempo, 'minutes');
                                sumaTiemposI = moment(sumaTiemposI).add(horasTiempo, 'hours');
                            }
                        } else {

                            tbodyEntradaySalidaI +=`<td class="tableHi">No tiene salida</td>`;

                            tbodyEntradaySalidaI += `<td class="tableHi" name="tiempoSitHi">

                                                    --:--:--

                                            </td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                        }
                    }
                } else {
                    if (marcacionData.salida != 0) {
                        if (h == moment(marcacionData.salida).format("HH")) {
                            //* COLUMNA DE ENTRADA
                            if (marcacionData.horario != 0) {
                                tbodyEntradaySalidaI += `<td>${marcacionData.horarioIni} -${marcacionData.horarioFin} </td>`;
                            } else {
                                tbodyEntradaySalidaI += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                            }

                            tbodyEntradaySalidaI += `<td class="tableHi">No tiene entrada</td>`;


                            //* COLUMNA DE SALIDA

                            tbodyEntradaySalidaI += `<td class="tableHi"> ${moment(marcacionData.salida).format("HH:mm:ss")}</td>`;


                            tbodyEntradaySalidaI += `<td class="tableHi" name="tiempoSitHi">

                                                    --:--:--

                                            </td><td class="tableHi" name="tiempoSitHi">---</td><td class="tableHi" name="tiempoSitHi">---</td><td class="tableHi" name="tiempoSitHi">---</td>`;
                        }
                    }
                }
            }
        }
        for (let m = data[index].marcaciones.length; m < cantidadColumnasHorasI; m++) {
            tbodyEntradaySalidaI += `<td class="tableHi">---</td><td class="tableHi">---</td><td class="tableHi" >---</td><td class="tableHi" name="tiempoSitHi">---</td><td class="tableHi" name="tiempoSitHi">---</td><td class="tableHi" name="tiempoSitHi">---</td><td class="tableHi" name="tiempoSitHi">---</td>`;
        }
        tbodyI += tbodyEntradaySalidaI;
        tbodyI += `<td class="tableHi" id="TiempoTotal${data[index].emple_id}">

                        ${sumaTiemposI.format("HH:mm:ss")}

                </td> <td >--</td>
                <td >--</td>
                <td >--</td></tr>`;
    }
    var fechaAsisteDH=moment($('#pasandoV').val()).format('DD/MM/YYYY')
    $('#fechaAsiste').html(fechaAsisteDH);
    $('#tbodyDI').html(tbodyI);
} else{
    $('#tbodyDI').empty();
}
        },
        error: function () { }
    });
    $('.horasEntrada').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",

        time_24hr: true,
        enableSeconds: true,
        /*  inline:true, */
        static: true
    });

    $('.horasSalida').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",

        time_24hr: true,
        enableSeconds: true,
        /*  inline:true, */
        static: true
    });
}

function cambiarF() {

    f1 = $("#fechaInput").val();
    f2 = moment(f1).format("YYYY-MM-DD");
    $('#pasandoV').val(f2);
    if ($.fn.DataTable.isDataTable("#tablaReport")) {

        /* $('#tablaReport').DataTable().destroy(); */
    }

    cargartabla(f2);

}
function cambiartabla() {
    if ($('#customSwitDetalles').is(':checked')) {
        $('[name="tiempoSitHi"]').show();
        setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
    }
    else {
        $('[name="tiempoSitHi"]').hide();
        setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
    }
}

///////////////////
function s2ab(s) {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
  }
function toExcel() {

   let file = new Blob([$('#tableZoomI').html()], {type:"application/vnd.ms-excel"});
let url = URL.createObjectURL(file);
let a = $("<a />", {
  href: url,
  download: "Asistencia.xls"}).appendTo("body").get(0).click();
 /*  e.preventDefault(); */
/*  var cuerpoexcel=$('#tableZoomI').html();
 atob(cuerpoexcel);
 var blob = new Blob([ s2ab(atob()), {type:"application/vnd.ms-excel"}]
  );
  const link = document.createElement("a");
  link.href = window.URL.createObjectURL(blob);
  link.download = `report_${new Date().getTime()}.xlsx`;
  link.click(); */
}
function generatePDF() {



    var element = $('#tableZoomI').html();
var opt = {
  margin:       0.5,
  filename:     'Asistencia.pdf',
  image:        { type: 'jpeg', quality: 0.98 },
  html2canvas:  { scale: 2 },
  jsPDF:        { unit: 'in', format: 'legal', orientation: 'landscape' }
};


html2pdf().from(element).set(opt).save();

  }

