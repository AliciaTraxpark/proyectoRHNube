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

// * INICIALIZAR TABLA
var table;
function inicializarTabla() {
    table = $("#tablaReport").DataTable({
        "searching": false,
        "scrollX": true,
        "ordering": false,
        "autoWidth": false,
        "bInfo": false,
        "bLengthChange": false,
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
                var r5 = Addrow(5, [{ k: 'A', v: 'Fecha:', s: 2 }, { k: 'C', v: jsDate, s: 0 }]);
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
                                    { text: '\nFecha:\t\t\t\t\t\t\t\t\t', bold: false }, { text: jsDate, bold: false }
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
        paging: true,
        initComplete: function () {
            setTimeout(function () { $("#tablaReport").DataTable().draw(); }, 200);
        },
    });
}

var razonSocial;
var direccion;
var ruc;
function cargartabla(fecha) {
    var idemp = $('#idempleado').val();
    $.ajax({
        async: false,
        type: "GET",
        url: "/reporteTablaMarca",
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
                var permisoModificar = $('#modifReporte').val();  // * PERMISO DE MODIFICAR
                $('#customSwitDetalles').prop("disabled", false);
                $('#switPausas').prop("disabled", false);
                $('#theadD').empty();
                // ? ************************************** CANTIDAD DE COLUMNAS DE LA TABLA*****************************
                // * ARRAY COMPLETO DE DETALLES, CANTIDAD DE PAUSAS Y MARCACIONES POR EL GRUPO DE HORARIOS
                var arrayHorario = [];
                // * CANTIDAD MAXIMA DE GRUPOS DE HORARIOS
                var cantidadGruposHorario = 1;
                for (let i = 0; i < data.length; i++) {
                    // * OBTENER EL MAXIMO DE GRUPOS DE HORARIOS
                    if (cantidadGruposHorario < data[i].data.length) {
                        cantidadGruposHorario = data[i].data.length;
                    }
                }
                // * BUSCAMOS LA CANTIDAD MAXIMA DE PAUSAS Y MARCACIONES POR GRUPO
                for (let busqueda = 0; busqueda < cantidadGruposHorario; busqueda++) {
                    // * MAXIMO DE PAUSAS POR GRUPO
                    var cantidadPausaG = 1;
                    // * MAXIMO DE MARCACIONES POR GRUPO
                    var cantidadMarcacionG = 1;
                    for (let i = 0; i < data.length; i++) {
                        // * OBTENER EL MAXIMO DE MARCACIONES Y PAUSAS POR GRUPO
                        if (data[i].data[busqueda] != undefined) {
                            if (cantidadPausaG < data[i].data[busqueda].pausas.length) {
                                cantidadPausaG = data[i].data[busqueda].pausas.length;
                            }
                            if (cantidadMarcacionG < data[i].data[busqueda].marcaciones.length) {
                                cantidadMarcacionG = data[i].data[busqueda].marcaciones.length;
                            }
                        }
                    }
                    arrayHorario.push(cantidadMarcacionG + "," + cantidadPausaG);
                }
                // ? ************************************************* ARMAR CABEZERA **********************************************
                var theadTabla = `<tr>`;
                // * CONDICIONAL DE PERMISOS
                if (permisoModificar == 1) {
                    theadTabla += `<th class="noExport">Agregar</th>`;
                }
                theadTabla += `<th>CC&nbsp;</th>
                                <th>DNI&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th>Nombre&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th>Cargo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>`;
                //* GRUPO DE HORARIOS
                for (let m = 0; m < cantidadGruposHorario; m++) {
                    // ************************************ DATOS DEL GRUPO HORARIO **************************************
                    // !HORARIO
                    theadTabla += `<th class="text-center" style="border-left: 2px solid #383e56!important;">Horario</th><th>Tiempo horario</th>`;
                    // ! MARCACION
                    var cantidadColumnasM = arrayHorario[m].split(",")[0];
                    for (let j = 0; j < cantidadColumnasM; j++) {
                        theadTabla += `<th style="border-left:1px dashed #aaaaaa!important;">Marca de entrada</th>
                                        <th>Marca de salida</th>
                                        <th id="tSitio" name="tiempoSitHi">Tiempo en sitio</th><th  name="tiempoSitHi">Tardanza</th>
                                        <th name="tiempoSitHi">Faltas</th><th  name="tiempoSitHi">Incidencias</th>`;
                    }
                    // ! PAUSAS
                    var cantidadColumnasP = arrayHorario[m].split(",")[1];
                    for (let p = 0; p < cantidadColumnasP; p++) {
                        theadTabla += `<th style="border-left: 1px dashed #aaaaaa!important;" name="datosPausa">Pausa</th>
                                        <th name="datosPausa">Horario pausa</th>
                                        <th name="datosPausa">Tiempo pausa</th>
                                        <th name="datosPausa">Exceso pausa</th>`;
                    }
                }
                theadTabla += `<th style="border-left:1px dashed #aaaaaa!important;">Marcación T.</th> 
                                <th>Tardanza T.</th>
                                <th>Faltas T.</th>
                                <th>Incidencias T.</th>
                                <th style="border-left:2px solid #f05454!important;">Tiempo T.</th>`;
                theadTabla += `</tr>`;
                //* DIBUJAMOS CABEZERA
                $('#theadD').html(theadTabla);
                // ! ********************************************************* BODY DE TABLA**************************************************
                $('#tbodyD').empty();
                var tbody = "";
                //* ARMAMOS BODY DE TABLA
                for (let index = 0; index < data.length; index++) {
                    razonSocial = data[index].organi_razonSocial;   // : -> VARIABLES PARA EXCEL Y PDF
                    direccion = data[index].organi_direccion;       // : -> VARIABLES PARA EXCEL Y PDF
                    ruc = data[index].organi_ruc;                   // : -> VARIABLES PARA EXCEL Y PDF
                    tbody += `<tr>`;
                    if (permisoModificar == 1) {
                        tbody += `<td class="noExport text-center">
                                    <a onclick="javascript:modalAgregarMarcacion(${data[index].emple_id},'${fecha}')">
                                        <img style="margin-bottom: 3px;" src="landing/images/plusM.svg"  height="17" />
                                    </a>
                                </td>`;
                    }
                    tbody += `<td>${(index + 1)}&nbsp;</td>
                            <td>${data[index].emple_nDoc}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>${data[index].perso_nombre} ${data[index].perso_apPaterno} ${data[index].perso_apMaterno}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    if (data[index].cargo_descripcion != null) {
                        tbody += `<td>${data[index].cargo_descripcion}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    } else {
                        tbody += `<td>---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                    }
                    // * ARMAR GRUPO DE HORARIOS
                    var grupoHorario = "";
                    //* ARMAR Y ORDENAR MARCACIONES
                    var sumaTiempos = moment("00:00:00", "HH:mm:ss");       //: SUMANDO LOS TIEMPOS
                    var tiempoTotal = moment("00:00:00", "HH:mm:ss");       //: SUMAS DE TIEMPOS Y RESTA DE PAUSAS
                    var sumaTardanzas = moment("00:00:00", "HH:mm:ss");     //: SUMANDO TARDANZAS
                    for (let m = 0; m < cantidadGruposHorario; m++) {
                        if (data[index].data[m] != undefined) {
                            // ! HORARIO
                            var horarioData = data[index].data[m].horario;
                            if (horarioData.horario != null) {
                                grupoHorario += `<td style="border-left: 2px solid #383e56!important;">
                                                    ${horarioData.horario}
                                                </td>
                                                <td>
                                                    ${moment(horarioData.horarioIni).format("HH:mm:ss")} - ${moment(horarioData.horarioFin).format("HH:mm:ss")}
                                                </td>`;
                            } else {
                                grupoHorario += `<td style="border-left: 2px solid #383e56!important;" class="text-center">
                                                    <a class="badge badge-soft-danger mr-2">
                                                        Sin horario
                                                    </a>
                                                </td>
                                                <td>---</td>`;
                            }
                            // * PAUSAS
                            var tiempoHoraPausa = "00";        //: HORAS TARDANZA
                            var tiempoMinutoPausa = "00";      //: MINUTOS TARDANZA
                            var tiempoSegundoPausa = "00";     //: SEGUNDOS TARDANZA
                            //* EXCESO
                            var tiempoHoraExceso = "00";
                            var tiempoMinutoExceso = "00";
                            var tiempoSegundoExceso = "00";
                            // ! MARCACIONES
                            var tbodyEntradaySalida = "";
                            // : HORA
                            var idHorarioM = [];
                            for (let j = 0; j < data[index].data[m].marcaciones.length; j++) {
                                var marcacionData = data[index].data[m].marcaciones[j];
                                if (marcacionData.entrada != 0) {
                                    if (permisoModificar == 1) {
                                        tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important">
                                                                    <div class="dropdown">
                                                                        <button class="btn dropdown-toggle" type="button" id="dropdownEntrada${marcacionData.idMarcacion}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                                            style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>
                                                                            ${moment(marcacionData.entrada).format("HH:mm:ss")}
                                                                        </button>
                                                                        <ul class="dropdown-menu"  aria-labelledby="dropdownEntrada${marcacionData.idMarcacion}">
                                                                            <h6 class="dropdown-header text-left">Opciones</h6>
                                                                            <a class="dropdowm-item">
                                                                                <div class="form-group noExport pl-3">
                                                                                    <a onclick="listaSalida(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                        <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12" />
                                                                                        Cambiar a entrada
                                                                                    </a>
                                                                                </div>
                                                                            </a>
                                                                            <a class="dropdowm-item">
                                                                                <div class="form-group noExport pl-3">
                                                                                    <a onclick="listaEntrada(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                    <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg"  height="12" />
                                                                                        Cambiar a salida
                                                                                    </a>
                                                                                </div>
                                                                            </a>
                                                                        </ul>
                                                                    </div></td>`;
                                    }
                                    else {
                                        tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>
                                                                            ${moment(marcacionData.entrada).format("HH:mm:ss")}
                                                                        </td>`;
                                    }

                                    if (marcacionData.salida != 0) {
                                        if (permisoModificar == 1) {
                                            tbodyEntradaySalida += `<td>
                                                                        <div class="dropdown" id="">
                                                                            <a class="dropdown" data-toggle="dropdown" id="dropdownSalida${marcacionData.idMarcacion}" aria-haspopup="true" aria-expanded="false style="cursor: pointer">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>
                                                                                ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                            </a>
                                                                            <ul class="dropdown-menu"  aria-labelledby="dropdownSalida${marcacionData.idMarcacion}">
                                                                                <h6 class="dropdown-header text-left">Opciones</h6>
                                                                                <a class="dropdowm-item">
                                                                                    <div class="form-group noExport pl-3">
                                                                                        <a onclick="listaSalida(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                            <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12" />
                                                                                            Cambiar a entrada
                                                                                        </a>
                                                                                    </div>
                                                                                </a>
                                                                                <a class="dropdowm-item">
                                                                                    <div class="form-group noExport pl-3">
                                                                                        <a onclick="listaEntrada(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                        <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg"  height="12" />
                                                                                            Cambiar a salida
                                                                                        </a>
                                                                                    </div>
                                                                                </a>
                                                                            </ul>
                                                                        </div>
                                                                    </td>`;
                                        } else {
                                            tbodyEntradaySalida += `<td>
                                                                        <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> 
                                                                        ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                    </td>`;

                                        }
                                        // * CALCULAR TIEMPO TOTAL , TIEMPO DE TARDANZA Y EXCESO DE PAUSAS
                                        var horaFinal = moment(marcacionData.salida);
                                        var horaInicial = moment(marcacionData.entrada);
                                        // * TIEMPO DE TARDANZA
                                        var segundosTardanza = "00";
                                        var minutosTardanza = "00";
                                        var horasTardanza = "00";
                                        if (horaFinal.isSameOrAfter(horaInicial)) {
                                            // * TIEMPO TOTAL TRABAJADA
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
                                            sumaTiempos = sumaTiempos.add({ "hours": horasTiempo, "minutes": minutosTiempo, "seconds": segundosTiempo });
                                            tiempoTotal = tiempoTotal.add({ "hours": horasTiempo, "minutes": minutosTiempo, "seconds": segundosTiempo });
                                            // * TARDANZA Y PAUSAS
                                            if (marcacionData.idH != 0) {
                                                // ******************************* TARDANZA ***************************************
                                                // ! PARA QUE TOME SOLO TARDANZA EN LA PRIMERA MARCACION
                                                if (!idHorarioM.includes(marcacionData.idH)) {
                                                    idHorarioM.push(marcacionData.idH);  // : AGREGAMOS EL ID AL ARRAY
                                                    var horaInicioHorario = moment(horarioData.horarioIni);
                                                    //: COMPARAMOS SI ES MAYOR A LA HORA DE INICIO DEL HORARIO
                                                    if (horaInicial.isAfter(horaInicioHorario)) {
                                                        var tardanza = horaInicial - horaInicioHorario;
                                                        segundosTardanza = moment.duration(tardanza).seconds();
                                                        minutosTardanza = moment.duration(tardanza).minutes();
                                                        horasTardanza = Math.trunc(moment.duration(tardanza).asHours());
                                                        if (horasTardanza < 10) {
                                                            horasTardanza = '0' + horasTardanza;
                                                        }
                                                        if (minutosTardanza < 10) {
                                                            minutosTardanza = '0' + minutosTardanza;
                                                        }
                                                        if (segundosTardanza < 10) {
                                                            segundosTardanza = '0' + segundosTardanza;
                                                        }
                                                    }
                                                }
                                                sumaTardanzas = sumaTardanzas.add({ "hours": horasTardanza, "minutes": minutosTardanza, "seconds": segundosTardanza });
                                                // ****************************************** PAUSAS ****************************************
                                                for (let indice = 0; indice < data[index].data[m].pausas.length; indice++) {
                                                    var contenidoP = data[index].data[m].pausas[indice];
                                                    if (contenidoP.horario_id == marcacionData.idH) {
                                                        var fechaI = horaInicial.clone().format("YYYY-MM-DD");
                                                        var fechaF;
                                                        if (contenidoP.inicio > contenidoP.fin) {
                                                            fechaF = horaInicial.clone().add(1, 'day').format("YYYY-MM-DD");
                                                        } else {
                                                            fechaF = horaInicial.clone().format("YYYY-MM-DD");
                                                        }
                                                        var pausaI = moment(fechaI + " " + contenidoP.inicio);
                                                        var pausaF = moment(fechaF + " " + contenidoP.fin);
                                                        // ! COMPROBAR SI TIENE SALIDA PARA PAUSA
                                                        var sumaToleranciaPausa = moment(
                                                            pausaI.clone().add(
                                                                { "minutes": contenidoP.tolerancia_inicio }    // : CLONAMOS EL TIEMPO Y SUMAR CON TOLERANCIA
                                                            ).toString());
                                                        var restaToleranciaPausa = moment(
                                                            pausaI.clone().subtract(
                                                                { "minutes": contenidoP.tolerancia_inicio }
                                                            ).toString()); //: CLONAMOS EL TIEMPO Y RESTAR CON TOLERANCIA
                                                        // ! CONDICIONALES QUE SI HORA FINAL DE LA MARCACION ESTA ENTRE LA RESTA CON LA TOLERANCIA Y LA SUMA CON LA TOLERANCIA
                                                        if (horaFinal.isAfter(restaToleranciaPausa) && horaFinal.isBefore(sumaToleranciaPausa)) {
                                                            // * VERIFICAR SI YA TENEMOS OTRA MARCACION SIGUIENTE
                                                            if (data[index].data[m].marcaciones[j + 1].entrada != undefined) {
                                                                var horaEntradaDespues = moment(data[index].data[m].marcaciones[j + 1].entrada);    //: -> OBTENER ENTRADA DE LA MARCACION SIGUIENTE
                                                                var restarTiempoMarcacion = horaEntradaDespues - horaFinal;                 //: -> RESTAR PARA OBTENER LA CANTIDAD EN PAUSA
                                                                tiempoSegundoPausa = moment.duration(restarTiempoMarcacion).seconds();      //: -> TIEMPOS EN SEGUNDOS
                                                                tiempoMinutoPausa = moment.duration(restarTiempoMarcacion).minutes();       //: -> TIEMPOS EN MINUTOS
                                                                tiempoHoraPausa = Math.trunc(moment.duration(restarTiempoMarcacion).asHours()); //: -> TIEMPOS EN HORAS
                                                                if (tiempoHoraPausa < 10) {
                                                                    tiempoHoraPausa = '0' + tiempoHoraPausa;
                                                                }
                                                                if (tiempoMinutoPausa < 10) {
                                                                    tiempoMinutoPausa = '0' + tiempoMinutoPausa;
                                                                }
                                                                if (tiempoSegundoPausa < 10) {
                                                                    tiempoSegundoPausa = '0' + tiempoSegundoPausa;
                                                                }
                                                                // * RESTAR EL TIEMPO DE PAUSA
                                                                tiempoTotal = tiempoTotal.add(
                                                                    {
                                                                        "hours": -tiempoHoraPausa,
                                                                        "minutes": -tiempoMinutoPausa,
                                                                        "seconds": -tiempoSegundoPausa
                                                                    }
                                                                );
                                                                // * VERIFICAR TIEMPO DE EXCESO
                                                                var clonarPausaI = pausaI.clone();
                                                                var clonarPausaF = pausaF.clone();
                                                                var restaEntrePausa = clonarPausaF - clonarPausaI;
                                                                // * CONDICIONAL QUE DEBE SER MENOR O IGUAL CON EL TIEMPO PAUSA
                                                                if (restarTiempoMarcacion <= restaEntrePausa) {
                                                                    tiempoHoraExceso = "00";
                                                                    tiempoMinutoExceso = "00";
                                                                    tiempoSegundoExceso = "00";
                                                                } else {
                                                                    var restaParaExceso = restarTiempoMarcacion - restaEntrePausa;
                                                                    tiempoSegundoExceso = moment.duration(restaParaExceso).seconds();
                                                                    tiempoMinutoExceso = moment.duration(restaParaExceso).minutes();
                                                                    tiempoHoraExceso = Math.trunc(moment.duration(restaParaExceso).asHours());
                                                                    if (tiempoHoraExceso < 10) {
                                                                        tiempoHoraExceso = '0' + tiempoHoraExceso;
                                                                    }
                                                                    if (tiempoMinutoExceso < 10) {
                                                                        tiempoMinutoExceso = '0' + tiempoMinutoExceso;
                                                                    }
                                                                    if (tiempoSegundoPausa < 10) {
                                                                        tiempoSegundoExceso = '0' + tiempoSegundoExceso;
                                                                    }
                                                                    console.log(restarTiempoMarcacion, restaEntrePausa, restaParaExceso);
                                                                }
                                                            }
                                                        } else {
                                                            if (pausaI.isAfter(horaInicial) && pausaI.isBefore(horaFinal)) {
                                                                var momentpausainicio = moment(contenidoP.inicio, ["HH:mm"]);
                                                                var momentpausafin = moment(contenidoP.fin, ["HH:mm"]);
                                                                var restaPausa = momentpausafin - momentpausainicio;
                                                                tiempoSegundoPausa = moment.duration(restaPausa).seconds();
                                                                tiempoMinutoPausa = moment.duration(restaPausa).minutes();
                                                                tiempoHoraPausa = Math.trunc(moment.duration(restaPausa).asHours());
                                                                if (tiempoHoraPausa < 10) {
                                                                    tiempoHoraPausa = '0' + tiempoHoraPausa;
                                                                }
                                                                if (tiempoMinutoPausa < 10) {
                                                                    tiempoMinutoPausa = '0' + tiempoMinutoPausa;
                                                                }
                                                                if (tiempoSegundoPausa < 10) {
                                                                    tiempoSegundoPausa = '0' + tiempoSegundoPausa;
                                                                }
                                                                // ************************* RESTAR LA PAUSA ******************************
                                                                tiempoTotal = tiempoTotal.add({
                                                                    "hours": -tiempoHoraPausa,
                                                                    "minutes": -tiempoMinutoPausa,
                                                                    "seconds": -tiempoSegundoPausa
                                                                });
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            // * FINALIZACION
                                            tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                                            <input type="hidden" value= "${horasTiempo}:${minutosTiempo}:${segundosTiempo}" name="tiempoSit${data[index].emple_id}[]" id="tiempoSit${data[index].emple_id}">
                                                                            <a class="badge badge-soft-primary mr-2">
                                                                                <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                                                ${horasTiempo}:${minutosTiempo}:${segundosTiempo}
                                                                            </a>
                                                                        </td>
                                                                        <td name="tiempoSitHi">
                                                                            <a class="badge badge-soft-danger mr-2">
                                                                                <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                                                ${horasTardanza}:${minutosTardanza}:${segundosTardanza}
                                                                            </a>
                                                                        </td>
                                                                        <td name="tiempoSitHi">--</td>
                                                                        <td name="tiempoSitHi">--</td>`;
                                        }
                                    } else {
                                        if (permisoModificar == 1) {
                                            tbodyEntradaySalida += `<td>
                                                                        <div class="dropdown noExport">
                                                                            <button type="button" class="btn dropdown-toggle" id="dropSalida${marcacionData.idMarcacion}" data-toggle="dropdown" aria-haspopup="true" 
                                                                                aria-expanded="false"
                                                                                style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                                                <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar hora">
                                                                                    <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                                    No tiene salida
                                                                                </span>
                                                                            </button>
                                                                            <ul class="dropdown-menu"  aria-labelledby="dropSalida${marcacionData.idMarcacion}">
                                                                                <form class="pr-3 pl-3 dropdown-item">
                                                                                    <div class="form-group">
                                                                                        <h6 class="dropdown-header text-left">
                                                                                            Hora salida
                                                                                            &nbsp; 
                                                                                            <a onclick="insertarSalida(${marcacionData.idMarcacion}) " style="cursor: pointer">
                                                                                                <img src="admin/images/checkH.svg" height="15">
                                                                                            </a>
                                                                                        </h6> 
                                                                                        <input type="text" id="horaSalidaN${marcacionData.idMarcacion}" class="form-control form-control-sm horasSalida" onchange="$(this).removeClass('borderColor');">
                                                                                    </div>
                                                                                </form>
                                                                            </ul>
                                                                        </div>
                                                                    </td>`;
                                        }
                                        else {
                                            tbodyEntradaySalida += `<td>
                                                                                <span class="badge badge-soft-secondary noExport"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                                    No tiene salida
                                                                                </span>
                                                                            </td>`;
                                        }

                                        tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                                            <span class="badge badge-soft-secondary">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                                --:--:--
                                                                            </span>
                                                                        </td>
                                                                        <td name="tiempoSitHi">--</td>
                                                                        <td name="tiempoSitHi">--</td>
                                                                        <td name="tiempoSitHi">--</td>`;
                                    }

                                } else {
                                    if (marcacionData.salida != 0) {
                                        //* COLUMNA DE ENTRADA
                                        if (permisoModificar == 1) {
                                            tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;">
                                                                        <div class=" dropdown noExport">
                                                                            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                                                style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;" id="dropEntrada${marcacionData.idMarcacion}">
                                                                                <span class="badge badge-soft-warning" data-toggle="tooltip" data-placement="left" title="Agregar hora">
                                                                                    <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                                                    No tiene entrada
                                                                                </span>
                                                                            </button>
                                                                            <ul class="dropdown-menu dropdown p-3"  id="UlE${marcacionData.idMarcacion}">
                                                                                <form class="pr-3 pl-3">
                                                                                    <div class="form-group">
                                                                                        <h6 class="dropdown-header text-left">
                                                                                            Hora entrada
                                                                                            &nbsp;
                                                                                            <a onclick="insertarEntrada(${marcacionData.idMarcacion})" style="cursor: pointer">
                                                                                                <img src="admin/images/checkH.svg" height="15">
                                                                                            </a>
                                                                                        </h6>
                                                                                        <input type="text" id="horaEntradaN${marcacionData.idMarcacion}" class="form-control form-control-sm horasEntrada" onchange="$(this).removeClass('borderColor');">
                                                                                    </div>
                                                                                </form>
                                                                            </ul>
                                                                        </div>
                                                                    </td>`;
                                        }
                                        else {
                                            tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;">
                                                                                <span class="badge badge-soft-warning noExport">
                                                                                    <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                                                    No tiene entrada
                                                                                </span>
                                                                            </td>`;
                                        }

                                        //* COLUMNA DE SALIDA
                                        var permisoModificarCE2 = $('#modifReporte').val();
                                        if (permisoModificarCE2 == 1) {
                                            tbodyEntradaySalida += `<td>
                                                                                <div class="dropdown" id="">
                                                                                    <a class="dropdown" data-toggle="dropdown" id="dropdownSalida${marcacionData.idMarcacion}" aria-haspopup="true" aria-expanded="false"
                                                                                        style="cursor: pointer">
                                                                                        <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>
                                                                                        ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                                    </a>
                                                                                    <ul class="dropdown-menu"  aria-labelledby="dropdownSalida${marcacionData.idMarcacion}">
                                                                                        <h6 class="dropdown-header text-left">Opciones</h6>
                                                                                        <a class="dropdowm-item">
                                                                                            <div class="form-group noExport pl-3">
                                                                                                <a onclick="listaSalida(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                                    <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12" />
                                                                                                    Cambiar a entrada
                                                                                                </a>
                                                                                            </div>
                                                                                        </a>
                                                                                        <a class="dropdowm-item">
                                                                                            <div class="form-group noExport pl-3">
                                                                                                <a onclick="listaEntrada(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                                <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg"  height="12" />
                                                                                                    Cambiar a salida
                                                                                                </a>
                                                                                            </div>
                                                                                        </a>
                                                                                    </ul>
                                                                                </div>
                                                                            </td>`;
                                        } else {
                                            tbodyEntradaySalida += `<td>
                                                                        <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> 
                                                                        ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                    </td>`;
                                        }

                                        tbodyEntradaySalida += `<td name="tiempoSitHi">
                                                                            <span class="badge badge-soft-secondary">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                                --:--:--
                                                                            </span>
                                                                        </td>
                                                                <td name="tiempoSitHi">--</td>
                                                                <td name="tiempoSitHi">--</td>
                                                                <td name="tiempoSitHi">--</td>`;

                                    }
                                }
                            }
                            for (let mr = data[index].data[m].marcaciones.length; mr < arrayHorario[m].split(",")[0]; mr++) {
                                tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;">---</td><td>---</td><td name="tiempoSitHi">---</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                            }
                            grupoHorario += tbodyEntradaySalida;
                            // ! PAUSAS
                            var tbodyPausas = "";
                            for (let p = 0; p < data[index].data[m].pausas.length; p++) {
                                var pausaData = data[index].data[m].pausas[p];
                                tbodyPausas += `<td style="border-left: 1px dashed #aaaaaa!important;" name="datosPausa">${pausaData.descripcion}</td>
                                        <td name="datosPausa">${pausaData.inicio} - ${pausaData.fin}</td>
                                        <td name="datosPausa">
                                            <a class="badge badge-soft-primary mr-2">
                                                <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                ${tiempoHoraPausa}:${tiempoMinutoPausa}:${tiempoSegundoPausa}
                                            </a>
                                        </td>
                                        <td name="datosPausa">
                                            <a class="badge badge-soft-danger mr-2">
                                                <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                ${tiempoHoraExceso}:${tiempoMinutoExceso}:${tiempoSegundoExceso}
                                            </a>
                                        </td>`;
                            }
                            for (let cp = data[index].data[m].pausas.length; cp < arrayHorario[m].split(",")[1]; cp++) {
                                tbodyPausas += `<td style="border-left: 1px dashed #aaaaaa!important" name="datosPausa">----</td>
                                                <td name="datosPausa">-----</td>
                                                <td name="datosPausa">-----</td>
                                                <td name="datosPausa">------</td>`;
                            }
                            grupoHorario += tbodyPausas;
                        } else {
                            grupoHorario += `<td style="border-left: 2px solid #383e56!important;">
                                            ----
                                        </td>
                                        <td>---</td>`;
                            // ! MARCACIONES
                            var tbodyEntradaySalida = "";
                            for (let mr = 0; mr < arrayHorario[m].split(",")[0]; mr++) {
                                tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;">---</td><td>---</td><td name="tiempoSitHi">---</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td><td name="tiempoSitHi">--</td>`;
                            }
                            grupoHorario += tbodyEntradaySalida;
                            // ! PAUSAS
                            var tbodyPausas = "";
                            for (let cp = 0; cp < arrayHorario[m].split(",")[1]; cp++) {
                                tbodyPausas += `<td style="border-left: 1px dashed #aaaaaa!important" name="datosPausa">----</td>
                                                <td name="datosPausa">-----</td>
                                                <td name="datosPausa">-----</td>
                                                <td name="datosPausa">------</td>`;
                            }
                            grupoHorario += tbodyPausas;
                        }
                    }
                    tbody += grupoHorario;
                    // * COLUMNAS DE TIEMPO TOTAL TARDANAZA ETC
                    tbody += `<td id="TiempoTotal${data[index].emple_id}" style="border-left:1px dashed #aaaaaa!important;">
                                <a class="badge badge-soft-primary mr-2">
                                    <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                    ${sumaTiempos.format("HH:mm:ss")}
                                </a>
                            </td>
                            <td>
                                <a class="badge badge-soft-danger mr-2">
                                    <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                    ${sumaTardanzas.format("HH:mm:ss")}
                                </a>
                            </td>
                            <td>--</td>
                            <td>--</td>
                            <td style="border-left:2px solid #f05454!important;" data-toggle="tooltip" data-placement="right" title="Marcación total - (Tiempo pausa)"
                            data-original-title="">
                                ${tiempoTotal.format("HH:mm:ss")}
                            </td>`;
                    tbody += `</tr>`;
                }
                $('#tbodyD').html(tbody);
                $('[data-toggle="tooltip"]').tooltip();
                // * PARA PODER MENUS CUANDO SOLO HAY UNA COLUMNA
                if (data.length == 1) {
                    var tbodyTR = '';
                    tbodyTR += '<tr style=" height: 5px;">';
                    if (permisoModificar == 1) {
                        tbodyTR += `<td></td>`;
                    }
                    tbodyTR += '<td></td><td></td><td></td><td></td>';
                    for (let m = 0; m < cantidadGruposHorario; m++) {
                        tbodyTR += '<td></td><td></td>';
                        // ! MARCACIONES
                        for (let mr = 0; mr < arrayHorario[m].split(",")[0]; mr++) {
                            tbodyTR += '<td><br></td><td></td><td name="tiempoSitHi"></td><td name="tiempoSitHi"></td><td name="tiempoSitHi"></td><td name="tiempoSitHi"></td>';
                        }
                        // ! PAUSAS
                        for (let cp = 0; cp < arrayHorario[m].split(",")[1]; cp++) {
                            tbodyTR += `<td name="datosPausa"></td>
                                    <td name="datosPausa"></td>
                                    <td name="datosPausa"></td>
                                    <td name="datosPausa"></td>`;
                        }
                    }
                    tbodyTR += '<td><br><br></td><td></td><td></td><td></td><td></td></tr>';
                    $('#tbodyD').append(tbodyTR);
                }
                inicializarTabla();
                $(window).on('resize', function () {
                    $("#tablaReport").css('width', '100%');
                    table.draw(true);
                });
                // * SWITCH DE MOSTRAR DETALLES
                if ($('#customSwitDetalles').is(':checked')) {
                    $('[name="tiempoSitHi"]').show();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                }
                else {
                    $('[name="tiempoSitHi"]').hide();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                }
                // * SWITCH DE PAUSAS
                if ($('#switPausas').is(':checked')) {
                    $('[name="datosPausa"]').show();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                } else {
                    $('[name="datosPausa"]').hide();
                    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
                }
            } else {
                $('#customSwitDetalles').prop("disabled", true);
                $('#switPausas').prop("disabled", true);
                $('#tbodyD').empty();
                $('#tbodyD').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
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
    cargartabla(f2);

}
// * FUNCION DE SWITCH DE MOSTRAR DETALLES
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
// * FUNCION DE SWITCH DE MOSTRAR PAUSAS
function togglePausas() {
    if ($('#switPausas').is(':checked')) {
        $('[name="datosPausa"]').show();
        setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
    } else {
        $('[name="datosPausa"]').hide();
        setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(true); }, 200);
    }
}
function cambiarEntrada(idMarca, respuesta) {
    $.ajax({
        type: "post",
        url: "/cambiarEntrada",
        data: {
            idMarca: idMarca,
            respuesta: respuesta
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
            if (data == 0) {
                alertify
                    .confirm("¿Desea generar nueva marcación si o no?", function (
                        e
                    ) {
                        if (e) {
                            cambiarEntrada(idMarca, true);
                        }
                    })
                    .setting({
                        title: "Nueva marcación",
                        labels: {
                            ok: "Si",
                            cancel: "No",
                        },
                        modal: true,
                        startMaximized: false,
                        reverseButtons: true,
                        resizable: false,
                        closable: false,
                        transition: "zoom",
                        oncancel: function (closeEvent) {
                        },
                    });
            } else {
                $('#btnRecargaTabla').click();
                $('#espera').hide();
                $('#tableZoom').show();
            }
        },
        error: function () {
            alert("Hay un error");
        },
    });
}
function cambiarSalida(idMarca, respuesta) {
    $.ajax({
        type: "post",
        url: "/cambiarSalida",
        data: {
            idMarca: idMarca,
            respuesta: respuesta
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
            if (data == 0) {
                alertify
                    .confirm("¿Desea generar nueva marcación si o no?", function (
                        e
                    ) {
                        if (e) {
                            cambiarEntrada(idMarca, true);
                        }
                    })
                    .setting({
                        title: "Nueva marcación",
                        labels: {
                            ok: "Si",
                            cancel: "No",
                        },
                        modal: true,
                        startMaximized: false,
                        reverseButtons: true,
                        resizable: false,
                        closable: false,
                        transition: "zoom",
                        oncancel: function (closeEvent) {
                        },
                    });
            } else {
                $('#btnRecargaTabla').click();
                $('#espera').hide();
                $('#tableZoom').show();
            }
        },
        error: function () {
            alert("Hay un error");
        },
    });
}

function insertarEntrada(idMarca) {
    let hora = $('#horaEntradaN' + idMarca + '').val();
    let fecha = $('#pasandoV').val() + ' ' + hora;

    $.ajax({
        type: "post",
        url: "/registrarNEntrada",
        data: {
            idMarca, hora, fecha
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
            if (data == 1) {
                $('#tableZoom').hide();
                $('#espera').show();
                $('#btnRecargaTabla').click();
                $('#espera').hide();
                $('#tableZoom').show();
            } else {

                $.notifyClose();
                $.notify({
                    message: '\nHora de entrada debe ser menor que hora de salida.',
                    icon: '/landing/images/alert1.svg',
                }, {
                    icon_type: 'image',
                    allow_dismiss: true,
                    newest_on_top: true,
                    delay: 6000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-3 text-center alert" style="background-color: #f2dede;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }


        },
        error: function () {
            alert("Hay un error");
        },
    });
}

function insertarSalida(idMarca) {
    let hora = $('#horaSalidaN' + idMarca + '').val();
    let fecha = $('#pasandoV').val() + ' ' + hora;

    $.ajax({
        type: "post",
        url: "/registrarNSalida",
        data: {
            idMarca, hora, fecha
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
            console.log('data[0]' + data[0]);
            if (data[0] == 1) {
                $('#tableZoom').hide();
                $('#espera').show();
                $('#btnRecargaTabla').click();
                $('#espera').hide();
                $('#tableZoom').show();
            } else {

                $.notifyClose();
                $.notify({
                    message: data[1],
                    icon: '/landing/images/alert1.svg',
                }, {
                    icon_type: 'image',
                    allow_dismiss: true,
                    newest_on_top: true,
                    delay: 6000,
                    template: '<div data-notify="container" class="col-xs-8 col-sm-3 text-center alert" style="background-color: #f2dede;" role="alert">' +
                        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                        '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                        '<span data-notify="title">{1}</span> ' +
                        '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                        '</div>',
                    spacing: 35
                });
            }


        },
        error: function () {
            alert("Hay un error");
        },
    });
}
// ! *********************************** FUNCIONALIDAD PARA MARCACIONES *******************************
// * FUNCION DE AGREGAR MARCACION
function modalAgregarMarcacion(idEmpleado, fecha) {
    $.ajax({
        async: false,
        type: "POST",
        url: "/busquedaMXE",
        data: {
            fecha: fecha,
            idEmpleado: idEmpleado
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
            if (data != null) {
                if ($('#dropSalida' + data) != undefined) {
                    $('#dropSalida' + data).dropdown("toggle");
                    $('#horaSalidaN' + data).addClass("borderColor");
                    $.notifyClose();
                    $.notify(
                        {
                            message:
                                "\nRegistrar marcación de entrada.",
                            icon: "admin/images/warning.svg",
                        },
                        {
                            position: "fixed",
                            mouse_over: "pause",
                            placement: {
                                from: "top",
                                align: "center",
                            },
                            icon_type: "image",
                            newest_on_top: true,
                            delay: 2000,
                            template:
                                '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                '<span data-notify="title">{1}</span> ' +
                                '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                "</div>",
                            spacing: 35,
                        }
                    );
                } else {
                    if ($('#dropEntrada' + data) != undefined) {
                        $('#dropEntrada' + data).dropdown("toggle");
                        $('#horaEntradaN' + data).addClass("borderColor");
                        $.notifyClose();
                        $.notify(
                            {
                                message:
                                    "\nRegistrar marcación de salida.",
                                icon: "admin/images/warning.svg",
                            },
                            {
                                position: "fixed",
                                mouse_over: "pause",
                                placement: {
                                    from: "top",
                                    align: "center",
                                },
                                icon_type: "image",
                                newest_on_top: true,
                                delay: 2000,
                                template:
                                    '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
                                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                                    '<img data-notify="icon" class="img-circle pull-left" height="20">' +
                                    '<span data-notify="title">{1}</span> ' +
                                    '<span style="color:#8a6d3b;" data-notify="message">{2}</span>' +
                                    "</div>",
                                spacing: 35,
                            }
                        );
                    }
                }
            }
        },
        error: function () { }
    });
}
// * FUNCION DE LISTA DE SALIDAS CON ENTRADAS NULL
function listaSalida(id, fecha, idEmpleado) {
    $('#salidaM').empty();
    $('#listaSalidasMarcacion').modal();
    $('#idMarcacion').val(id);
    $.ajax({
        async: false,
        type: "POST",
        url: "/listaMarcacionS",
        data: {
            fecha: fecha,
            idEmpleado: idEmpleado
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
                var container = `<option value="" disabled selected>Seleccionar salida</option>`;
                for (let index = 0; index < data.length; index++) {
                    container += `<optgroup label="Horario ${data[index].horario}">`;
                    data[index].data.forEach(element => {
                        if (element.id == id) {
                            container += `<option value="${element.id}" selected="selected">
                                    Salida : 
                                    ${moment(element.salida).format("HH:mm:ss")}
                                </option>`;
                        } else {
                            container += `<option value="${element.id}">
                                    Salida : 
                                    ${moment(element.salida).format("HH:mm:ss")}
                                </option>`;
                        }
                    });
                    container += `</optgroup`;
                }
            } else {
                var container = `<option value="" disabled selected>No hay marcaciónes disponibles</option>`;
            }
            $('#salidaM').append(container);
        },
        error: function () { }
    });
}
// * FUNCION DE CAMBIAR ENTRADA
function cambiarEntradaM() {
    var idCambiar = $('#idMarcacion').val();
    var idMarcacion = $('#salidaM').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/cambiarEM",
        data: {
            idCambiar: idCambiar,
            idMarcacion: idMarcacion
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
            if (data != 0) {
                $('#s_validCruce').hide();
                $('#listaSalidasMarcacion').modal('toggle');
                $('#btnRecargaTabla').click();
                $.notifyClose();
                $.notify(
                    {
                        message: "\nMarcación modificada.",
                        icon: "admin/images/checked.svg",
                    },
                    {
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
            } else {
                $('#s_validCruce').show();
            }
        },
        error: function () {
        }
    });
}
// * FUNCION DE LISTA DE ENTRADAS CON SALIDAS NULL
function listaEntrada(id, fecha, idEmpleado) {
    $('#entradaM').empty();
    $('#listaEntradasMarcacion').modal();
    $('#idMarcacionE').val(id);
    $.ajax({
        async: false,
        type: "POST",
        url: "/listaMarcacionE",
        data: {
            fecha: fecha,
            idEmpleado: idEmpleado
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
                var container = `<option value="" disabled selected>Seleccionar salida</option>`;
                for (let index = 0; index < data.length; index++) {
                    container += `<optgroup label="Horario ${data[index].horario}">`;
                    data[index].data.forEach(element => {
                        if (element.id == id) {
                            container += `<option value="${element.id}" selected="selected">
                                    Entrada : 
                                    ${moment(element.entrada).format("HH:mm:ss")}
                                </option>`;
                        } else {
                            container += `<option value="${element.id}">
                                    Entrada : 
                                    ${moment(element.entrada).format("HH:mm:ss")}
                                </option>`;
                        }
                    });
                    container += `</optgroup`;
                }
            } else {
                var container = `<option value="" disabled selected>No hay marcaciónes disponibles</option>`;
            }
            $('#entradaM').append(container);
        },
        error: function () { }
    });
}
// * FUNCION DE CAMBIAR SALIDA
function cambiarSalidaM() {
    var idCambiar = $('#idMarcacionE').val();
    var idMarcacion = $('#entradaM').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/cambiarSM",
        data: {
            idCambiar: idCambiar,
            idMarcacion: idMarcacion
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
            if (data != 0) {
                $('#e_validCruce').hide();
                $('#listaEntradasMarcacion').modal('toggle');
                $('#btnRecargaTabla').click();
                $.notifyClose();
                $.notify(
                    {
                        message: "\nMarcación modificada.",
                        icon: "admin/images/checked.svg",
                    },
                    {
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
            } else {
                $('#e_validCruce').show();
            }
        },
        error: function () {
        }
    });
}