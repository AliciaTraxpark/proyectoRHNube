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
    $("#idempleado").select2({
        placeholder: "Seleccionar",
        language: {
            inputTooShort: function (e) {
                return "Escribir nombre o apellido";
            },
            loadingMore: function () {
                return "Cargando más resultados…";
            },
            noResults: function () {
                return "No se encontraron resultados";
            },
        },
        minimumInputLength: 2,
    });
    f = moment().format("YYYY-MM-DD");
    fechaValue.setDate(f);
    $("#fechaInput").change();
    cambiarF();
});

function cargartabla(fecha) {
    var idemp = $("#idempleado").val();
    $.ajax({
        type: "GET",
        url: "/tablaTareo",
        data: {
            fecha,
            idemp,
        },
        async: false,
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
            if (data.length != 0) {
                if ($.fn.DataTable.isDataTable("#tablaReport")) {
                    $("#tablaReport").DataTable().destroy();
                }
                // ! *********** CABEZERA DE TABLA**********
                $("#theadD").empty();
                $("#btnsDescarga").show();
                //* CANTIDAD MININO VALOR DE COLUMNAS PARA HORAS
                var cantidadColumnasHoras = 0;
                for (let i = 0; i < data.length; i++) {
                    //* OBTENER CANTIDAD TOTAL DE COLUMNAS
                    if (cantidadColumnasHoras < data.length) {
                        cantidadColumnasHoras = data.length;
                    }
                }
                //*---------------------------- ARMAR CABEZERA-----------------------------------------
                var theadTabla = `<tr>
                                    <th>CC&nbsp;</th>
                                    <th>Código</th>
                                    <th>DNI&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th>Nombre&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th>Cargo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>`;


                    theadTabla += `<th>Cod.</th>
                                    <th>Actividad</th>
                                    <th>Cod.</th>
                                    <th>Subactividad</th>
                                    <th>Hora de entrada</th>
                                    <th>Hora de salida</th>
                                    <th id="tSitio" name="tiempoSitHi">Tiempo en sitio</th>`;


                theadTabla += `<th>Tiempo total</th>
                                   <th>Punto de control</th>
                                   <th>Controlador</th></tr>`;

                //* DIBUJAMOS CABEZERA
                $("#theadD").html(theadTabla);
                /* --------------------------------FIN DE CABECERA------------------------- */

                // ! *********** BODY DE TABLA**********
                /* ---------------CUERPO DE TABLA TBODY -----------------------------------------*/
                $("#tbodyD").empty();
                var tbody = "";
                //* ARMAMOS BODY DE TABLA
                for (let index = 0; index < data.length; index++) {
                    tbody += `<tr>

                                <td>${index + 1}&nbsp;</td>

                                <td>${
                                    data[index].emple_codigo
                                }&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                                <td>${
                                    data[index].emple_nDoc
                                }&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                                <td>${data[index].perso_nombre} ${
                                    data[index].perso_apPaterno
                                } ${
                                    data[index].perso_apMaterno
                                }&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;

                                if (data[index].cargo_descripcion != null) {
                                    tbody += `<td>${data[index].cargo_descripcion}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                } else {
                                    tbody += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                }


                    //* ARMAR Y ORDENAR MARCACIONES
                    var tbodyEntradaySalida = "";
                    var sumaTiempos = moment("00:00:00", "HH:mm:ss");
                    /* -------------------------------------------- */
                    //: HORA
                    for (let h = 0; h < 24; h++) {

                            var marcacionData = data[index];

                            /* SI TENGO ENTRADA */
                            if (marcacionData.entrada != 0) {
                                if (
                                    h ==
                                    moment(marcacionData.entrada).format("HH")
                                ) {

                                    if (marcacionData.codigoActividad != 0) {
                                        tbodyEntradaySalida += `<td >${marcacionData.codigoActividad} </td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                    }

                                    if (marcacionData.Activi_Nombre != 0) {
                                        tbodyEntradaySalida += `<td>${marcacionData.Activi_Nombre}</td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                    }

                                    if (marcacionData.codigoSubactiv != 0) {
                                        tbodyEntradaySalida += `<td >${marcacionData.codigoSubactiv} </td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                    }

                                    if (marcacionData.subAct_nombre != 0) {
                                        tbodyEntradaySalida += `<td>${marcacionData.subAct_nombre}</td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                    }

                                    tbodyEntradaySalida += `<td><img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>${moment(
                                        marcacionData.entrada
                                    ).format("HH:mm:ss")}</td>`;

                                    /* SI  TENGO SALIDA */
                                    if (marcacionData.salida != 0) {
                                        tbodyEntradaySalida += `<td><img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> ${moment(
                                            marcacionData.salida
                                        ).format("HH:mm:ss")}</td>`;

                                        var horaFinal = moment(
                                            marcacionData.salida
                                        );
                                        var horaInicial = moment(
                                            marcacionData.entrada
                                        );
                                        if (
                                            horaFinal.isSameOrAfter(horaInicial)
                                        ) {
                                            var tiempoRestante =
                                                horaFinal - horaInicial;
                                            var segundosTiempo = moment
                                                .duration(tiempoRestante)
                                                .seconds();
                                            var minutosTiempo = moment
                                                .duration(tiempoRestante)
                                                .minutes();
                                            var horasTiempo = Math.trunc(
                                                moment
                                                    .duration(tiempoRestante)
                                                    .asHours()
                                            );
                                            if (horasTiempo < 10) {
                                                horasTiempo = "0" + horasTiempo;
                                            }
                                            if (minutosTiempo < 10) {
                                                minutosTiempo =
                                                    "0" + minutosTiempo;
                                            }
                                            if (segundosTiempo < 10) {
                                                segundosTiempo =
                                                    "0" + segundosTiempo;
                                            }
                                            tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                                    <input type="hidden" value= "${horasTiempo}:${minutosTiempo}:${segundosTiempo}" name="tiempoSit${data[index].emple_id}[]" id="tiempoSit${data[index].emple_id}">
                                                                    <a class="badge badge-soft-primary mr-2"><img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                                        ${horasTiempo}:${minutosTiempo}:${segundosTiempo}
                                                                    </a>
                                                                </td>`;
                                            sumaTiempos = moment(
                                                sumaTiempos
                                            ).add(segundosTiempo, "seconds");
                                            sumaTiempos = moment(
                                                sumaTiempos
                                            ).add(minutosTiempo, "minutes");
                                            sumaTiempos = moment(
                                                sumaTiempos
                                            ).add(horasTiempo, "hours");
                                        }
                                    } else {
                                        /* SI NO TENGO SALIDA */
                                        tbodyEntradaySalida += `<td><span class="badge badge-soft-secondary"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>No tiene salida</span></td>`;

                                        tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                            <span class="badge badge-soft-secondary">
                                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                --:--:--
                                                            </span>
                                                        </td>`;
                                    }
                                }
                            } else {
                                /* SI NO TENGO ENTRADA Y SI TENGO SALIDA */
                                if (marcacionData.salida != 0) {
                                    if (
                                        h ==
                                        moment(marcacionData.salida).format(
                                            "HH"
                                        )
                                    ) {

                                        if (marcacionData.codigoActividad != 0) {
                                            tbodyEntradaySalida += `<td >${marcacionData.codigoActividad} </td>`;
                                        } else {
                                            tbodyEntradaySalida += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                        }

                                        if (marcacionData.Activi_Nombre != 0) {
                                            tbodyEntradaySalida += `<td>${marcacionData.Activi_Nombre}</td>`;
                                        } else {
                                            tbodyEntradaySalida += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                        }

                                        if (marcacionData.codigoSubactiv != 0) {
                                            tbodyEntradaySalida += `<td >${marcacionData.codigoSubactiv} </td>`;
                                        } else {
                                            tbodyEntradaySalida += `<td >---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                        }

                                        if (marcacionData.subAct_nombre != 0) {
                                            tbodyEntradaySalida += `<td>${marcacionData.subAct_nombre}</td>`;
                                        } else {
                                            tbodyEntradaySalida += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                                        }

                                         //* COLUMNA DE ENTRADA
                                        tbodyEntradaySalida += `<td><span class="badge badge-soft-warning"><img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>No tiene entrada</span></td>`;

                                        //* COLUMNA DE SALIDA

                                        tbodyEntradaySalida += `<td><img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> ${moment(
                                            marcacionData.salida
                                        ).format("HH:mm:ss")}</td>`;

                                        tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                            <span class="badge badge-soft-secondary">
                                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                --:--:--
                                                            </span>
                                                        </td>`;
                                    }
                                }
                            }

                    }

                        /*------- N DE COLUMNAS DE REPETICION---------------- */
                       /*  tbodyEntradaySalida += `<td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td name="tiempoSitHi">---</td>`; */
                        /* --------------------------------------------------- */

                    tbody += tbodyEntradaySalida;

                    tbody += `<td id="TiempoTotal${data[index].emple_id}">
                               <a class="badge badge-soft-primary mr-2">
                                 <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                            ${sumaTiempos.format("HH:mm:ss")}
                               </a>
                              </td>

                              <td> ${marcacionData.puntoControl} </td>
                                <td>  ${marcacionData.contrT_nombres}  ${marcacionData.contrT_ApPaterno}  ${marcacionData.contrT_ApMaterno}</td></tr>`;
                }
                $("#tbodyD").html(tbody);

                /* DATOS PARA TABLA */
                var razonSocial=$('#nameOrganizacion').val();
                var direccion=$('#direccionO').val();
                var ruc=$('#rucOrg').val();

                var fechaAsisteDH = moment($('#pasandoV').val()).format('DD/MM/YYYY')

                /* ------------------------ */

                table = $("#tablaReport").DataTable({
                    searching: false,
                    scrollX: true,
                    ordering: false,
                    autoWidth: false,
                    bInfo: false,
                    bLengthChange: false,
                    fixedHeader: true,
                    language: {
                        sProcessing: "Procesando...",
                        sLengthMenu: "Mostrar _MENU_ registros",
                        sZeroRecords: "No se encontraron resultados",
                        sEmptyTable: "Ningún dato disponible en esta tabla",
                        sInfo: "Mostrando registros del _START_ al _END_ ",
                        sInfoEmpty:
                            "Mostrando registros del 0 al 0 de un total de 0 registros",
                        sInfoFiltered:
                            "(filtrado de un total de _MAX_ registros)",
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
                            var downrows = 5;
                            var clRow = $('row', sheet);
                            clRow[0].children[0].remove();
                            //update Row
                            clRow.each(function () {
                                var attr = $(this).attr('r');
                                var ind = parseInt(attr);
                                ind = ind + downrows;
                                $(this).attr("r", ind);
                            });

                            // Update  row > c
                            $('row c ', sheet).each(function () {
                                var attr = $(this).attr('r');
                                var pre = attr.substring(0, 1);
                                var ind = parseInt(attr.substring(1, attr.length));
                                ind = ind + downrows;
                                $(this).attr("r", pre + ind);
                            });

                            function Addrow(index, data) {
                                msg = '<row r="' + index + '">'
                                for (i = 0; i < data.length; i++) {
                                    var key = data[i].k;
                                    var value = data[i].v;
                                    var bold = data[i].s;
                                    msg += '<c t="inlineStr" r="' + key + index + '" s="' + bold + '" >';
                                    msg += '<is>';
                                    msg += '<t>' + value + '</t>';
                                    msg += '</is>';
                                    msg += '</c>';
                                }
                                msg += '</row>';
                                return msg;
                            }
                            var now = new Date();
                            var jsDate = now.getDate() + "/" + (now.getMonth() + 1) + "/" + now.getFullYear();
                            //insert
                            var r1 = Addrow(1, [{ k: 'A', v: 'CONTROL REGISTRO DE ASISTENCIA', s: 2 }]);
                            var r2 = Addrow(2, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                            var r3 = Addrow(3, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                            var r4 = Addrow(4, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                            var r5 = Addrow(5, [{ k: 'A', v: 'Fecha:', s: 2 }, { k: 'C', v: fechaAsisteDH, s: 0 }]);
                            sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + sheet.childNodes[0].childNodes[1].innerHTML;
                        },
                        sheetName: 'Asistencia',
                        title: 'Asistencia',
                        autoFilter: false,
                        exportOptions: {
                            columns: ":visible:not(.noExport)",
                            format: {
                                body: function (data, row, column, node) {
                                    var cont = $.trim($(node).text());
                                    var cont1 = cont.replace('Cambiar a entrada', '');
                                    var cont2 = cont1.replace('Cambiar a salida', '');
                                    var cont3 = cont2.replace('No tiene entrada', '---');
                                    var cont4 = cont3.replace('No tiene salida', '---');

                                    return $.trim(cont4);
                                }
                            }
                        },
                    }, {
                        extend: "pdfHtml5",
                        className: 'btn btn-sm mt-1',
                        text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                        orientation: 'landscape',
                        pageSize: 'A1',
                        title: 'Asistencia',
                        exportOptions: {
                            columns: ":visible:not(.noExport)"
                        },
                        customize: function (doc) {
                            doc['styles'] = {
                                table: {
                                    width: '100%'
                                },
                                tableHeader: {
                                    bold: true,
                                    fontSize: 11,
                                    color: '#6c757d',
                                    fillColor: '#ffffff',
                                    alignment: 'left'
                                },
                                defaultStyle: {
                                    fontSize: 10,
                                    alignment: 'center'
                                }
                            };
                            doc.pageMargins = [20, 120, 20, 30];
                            doc.content[1].margin = [30, 0, 30, 0];
                            var colCount = new Array();
                            var tr = $('#tablaReport tbody tr:first-child');
                            var trWidth = $(tr).width();
                            $('#tablaReport').find('tbody tr:first-child td').each(function () {
                                var tdWidth = $(this).width();
                                var widthFinal = parseFloat(tdWidth * 130);
                                widthFinal = widthFinal.toFixed(2) / trWidth.toFixed(2);
                                if ($(this).attr('colspan')) {
                                    for (var i = 1; i <= $(this).attr('colspan'); $i++) {
                                        colCount.push('*');
                                    }
                                } else {
                                    colCount.push(parseFloat(widthFinal.toFixed(2)) + '%');
                                }
                            });
                            var bodyCompleto = [];
                            doc.content[1].table.body.forEach(function (line, i) {
                                var bodyNuevo = [];
                                if (i >= 1) {
                                    line.forEach(element => {
                                        var textOriginal = element.text;
                                        var cambiar = textOriginal.replace('Cambiar a entrada', '');
                                        var cambiar2 = cambiar.replace('Cambiar a salida', '');
                                        var cambiar3 = cambiar2.replace('No tiene entrada', '---');
                                        var cambiar4 = cambiar3.replace('No tiene salida', '---');
                                        var cambiar5 = cambiar4.trim();
                                        bodyNuevo.push({ text: cambiar5, style: 'defaultStyle' });
                                    });
                                    bodyCompleto.push(bodyNuevo);
                                } else {
                                    bodyCompleto.push(line);
                                }
                            });
                            doc.content.splice(0, 1);
                            doc.content[0].table.body = bodyCompleto;
                            var objLayout = {};
                            objLayout['hLineWidth'] = function (i) { return .2; };
                            objLayout['vLineWidth'] = function (i) { return .2; };
                            objLayout['hLineColor'] = function (i) { return '#aaa'; };
                            objLayout['vLineColor'] = function (i) { return '#aaa'; };
                            doc.content[0].layout = objLayout;
                            var now = new Date();
                            var jsDate = now.getDate() + "/" + (now.getMonth() + 1) + "/" + now.getFullYear();
                            doc["header"] = function () {
                                return {
                                    columns: [
                                        {
                                            alignment: 'left',
                                            italics: false,
                                            text: [
                                                { text: '\nCONTROL REGISTRO DE ASISTENCIA', bold: true },
                                                { text: '\n\nRazon Social:\t\t\t\t\t\t', bold: false }, { text: razonSocial, bold: false },
                                                { text: '\nDireccion:\t\t\t\t\t\t\t', bold: false }, { text: '\t' + direccion, bold: false },
                                                { text: '\nNumero de Ruc:\t\t\t\t\t', bold: false }, { text: ruc, bold: false },
                                                { text: '\nFecha:\t\t\t\t\t\t\t\t\t', bold: false }, { text: fechaAsisteDH, bold: false }
                                            ],

                                            fontSize: 10,
                                            margin: [30, 0]
                                        },
                                    ],
                                    margin: 20
                                };
                            };
                        }
                    }],
                    initComplete: function () {
                        setTimeout(function () {
                            $("#tablaReport").DataTable().draw();
                        }, 200);
                    },
                });
                $(window).on("resize", function () {
                    $("#tablaReport").css("width", "100%");
                    table.draw(true);
                });
                if ($("#customSwitDetalles").is(":checked")) {
                    $('[name="tiempoSitHi"]').show();
                    setTimeout(function () {
                        $("#tablaReport").css("width", "100%");
                        $("#tablaReport").DataTable().draw(true);
                    }, 200);
                } else {
                    $('[name="tiempoSitHi"]').hide();
                    setTimeout(function () {
                        $("#tablaReport").css("width", "100%");
                        $("#tablaReport").DataTable().draw(true);
                    }, 200);
                }
            } else {
                $("#btnsDescarga").hide();
                $("#tbodyD").empty();
                $("#tbodyD").append(
                    '<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>'
                );
            }


        },
        error: function () {},
    });
    $(".horasEntrada").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",

        time_24hr: true,
        enableSeconds: true,
        /*  inline:true, */
        static: true,
    });

    $(".horasSalida").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",

        time_24hr: true,
        enableSeconds: true,
        /*  inline:true, */
        static: true,
    });
}

function cambiarF() {
    f1 = $("#fechaInput").val();
    f2 = moment(f1).format("YYYY-MM-DD");
    $("#pasandoV").val(f2);
    if ($.fn.DataTable.isDataTable("#tablaReport")) {
        /* $('#tablaReport').DataTable().destroy(); */
    }

    cargartabla(f2);
}
function cambiartabla() {
    if ($("#customSwitDetalles").is(":checked")) {
        $('[name="tiempoSitHi"]').show();
        setTimeout(function () {
            $("#tablaReport").css("width", "100%");
            $("#tablaReport").DataTable().draw(true);
        }, 200);
    } else {
        $('[name="tiempoSitHi"]').hide();
        setTimeout(function () {
            $("#tablaReport").css("width", "100%");
            $("#tablaReport").DataTable().draw(true);
        }, 200);
    }
}

///////////////////
function s2ab(s) {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xff;
    return buf;
}
function toExcel() {
    let file = new Blob([$("#tableZoomI").html()], {
        type: "application/vnd.ms-excel",
    });
    let url = URL.createObjectURL(file);
    let a = $("<a />", {
        href: url,
        download: "Asistencia.xls",
    })
        .appendTo("body")
        .get(0)
        .click();
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
    var element = $("#tableZoomI").html();
    var opt = {
        margin: 0.5,
        filename: "Asistencia.pdf",
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: "in", format: "legal", orientation: "landscape" },
    };

    html2pdf().from(element).set(opt).save();
}
