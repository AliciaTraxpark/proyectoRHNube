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
var fechaGlobal = {};
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
// * VARIABLES PARA EXCEL Y PDF
var razonSocial;
var direccion;
var ruc;
// * HORAS PARA INSERTAR ENTRADA Y SALIDA
var horasE = {};
var horasS = {};
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
            fechaGlobal = fecha;
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
                    theadTabla += `<th class="text-center" style="border-left: 2px solid #383e56!important;">
                                        Horario
                                    </th>
                                    <th class="text-center">Tiempo horario</th>
                                    <th>Tolerancia</th>`;
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
                        tbody += `<td class="text-center">---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
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
                                grupoHorario += `<td style="border-left: 2px solid #383e56!important;" class="text-center">
                                                    <div class="dropdown">
                                                        <a class="btn dropdown" type="button" data-toggle="dropdown" id="dropdownHorario${horarioData.idHorario}" aria-haspopup="true" aria-expanded="false" 
                                                            style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                            <span class="badge badge-soft-primary mr-2" class="text-center">
                                                                ${horarioData.horario}
                                                            </span>
                                                        </a>
                                                        <ul class="dropdown-menu scrollable-menu"  aria-labelledby="dropdownHorario${horarioData.idHorario}" style="padding: 0rem 0rem;">
                                                            <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                Opciones
                                                            </h6>
                                                            <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                            <div class="dropdown-item">
                                                                <div class="form-group noExport pl-3">
                                                                    <a onclick="modalCambiarHorario(${horarioData.idHorarioE},'${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/calendarioAD.svg" height="15" />
                                                                        Cambiar horario
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td>
                                                    ${moment(horarioData.horarioIni).format("HH:mm:ss")} - ${moment(horarioData.horarioFin).format("HH:mm:ss")}
                                                </td>
                                                <td class="text-center">${horarioData.tolerancia} min.</td>`;
                            } else {
                                grupoHorario += `<td style="border-left: 2px solid #383e56!important;" class="text-center">
                                                    <div class="dropdown">
                                                        <a class="btn dropdown" type="button" data-toggle="dropdown" id="dropdownHorario${horarioData.idHorario}" aria-haspopup="true" aria-expanded="false" 
                                                            style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                            <span class="badge badge-soft-danger mr-2" class="text-center">
                                                                Sin horario
                                                            </span>
                                                        </a>
                                                        <ul class="dropdown-menu scrollable-menu"  aria-labelledby="dropdownHorario${horarioData.idHorario}" style="padding: 0rem 0rem;">
                                                            <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                Opciones
                                                            </h6>
                                                            <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                            <div class="dropdown-item">
                                                                <div class="form-group noExport pl-3">
                                                                    <a onclick="modalCambiarHorario(${horarioData.idHorarioE},'${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/calendarioAD.svg" height="15" />
                                                                        Cambiar horario
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td class="text-center">---</td><td class="text-center">---</td>`;
                            }
                            // ! MARCACIONES
                            var tbodyEntradaySalida = "";
                            // : HORA
                            var idHorarioM = [];
                            for (let j = 0; j < data[index].data[m].marcaciones.length; j++) {
                                // * PAUSAS
                                var tiempoHoraPausa = "00";        //: HORAS TARDANZA
                                var tiempoMinutoPausa = "00";      //: MINUTOS TARDANZA
                                var tiempoSegundoPausa = "00";     //: SEGUNDOS TARDANZA
                                //* EXCESO
                                var tiempoHoraExceso = "00";
                                var tiempoMinutoExceso = "00";
                                var tiempoSegundoExceso = "00";
                                // * TIEMPO EN SITIO
                                var segundosTiempo = "00";
                                var minutosTiempo = "00";
                                var horasTiempo = "00";
                                // * TARDANZA
                                var segundosTardanza = "00";
                                var minutosTardanza = "00";
                                var horasTardanza = "00";
                                var marcacionData = data[index].data[m].marcaciones[j];
                                if (marcacionData.entrada != 0) {
                                    if (permisoModificar == 1) {
                                        tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important">
                                                                    <div class="dropdown">
                                                                        <a class="btn dropdown-toggle" type="button" id="dropdownEntrada${marcacionData.idMarcacion}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                                            style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>
                                                                            ${moment(marcacionData.entrada).format("HH:mm:ss")}
                                                                        </a>
                                                                        <ul class="dropdown-menu scrollable-menu"  aria-labelledby="dropdownEntrada${marcacionData.idMarcacion}" style="padding: 0rem 0rem;">
                                                                            <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                                <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                                Opciones
                                                                            </h6>
                                                                            <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                            <div class="dropdown-item">
                                                                                <div class="form-group noExport pl-3">
                                                                                    <a onclick="listaSalida(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.entrada).format("HH:mm:ss")}',1)" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                        <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" height="12" />
                                                                                        Cambiar a entrada
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="dropdown-item">
                                                                                <div class="form-group noExport pl-3">
                                                                                    <a onclick="listaEntrada(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.entrada).format("HH:mm:ss")}',1)" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                    <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg"  height="12" />
                                                                                        Cambiar a salida
                                                                                    </a>
                                                                                </div>
                                                                            </div>`;
                                        if (marcacionData.salida != 0) {
                                            tbodyEntradaySalida += `<div class="dropdown-item">
                                                                        <div class="form-group noExport pl-3">
                                                                            <a onclick="convertirOrden(${marcacionData.idMarcacion})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/flechasD.svg"  height="12" />
                                                                                Convertir orden
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dropdown-item">
                                                                        <div class="form-group noExport pl-3">
                                                                            <a onclick="asignarNuevaM(${marcacionData.idMarcacion},'${moment(marcacionData.entrada).format("HH:mm:ss")}',1,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                                                Asignar a nueva marc.
                                                                            </a>
                                                                        </div>
                                                                    </div>`;
                                        }
                                        tbodyEntradaySalida += ` <div class="dropdown-item">
                                                                    <div class="form-group noExport pl-3">
                                                                        <a onclick="eliminarM(${marcacionData.idMarcacion},1)" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/borrarD.svg"  height="12" />
                                                                            Eliminar marc.
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </ul></div></td>`;
                                    }
                                    else {
                                        tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>
                                                                            ${moment(marcacionData.entrada).format("HH:mm:ss")}
                                                                        </td>`;
                                    }

                                    // * TARDANZA
                                    if (marcacionData.idH != 0) {
                                        var horaInicial = moment(marcacionData.entrada);
                                        // ******************************* TARDANZA ***************************************
                                        // ! PARA QUE TOME SOLO TARDANZA EN LA PRIMERA MARCACION
                                        if (!idHorarioM.includes(marcacionData.idH)) {
                                            idHorarioM.push(marcacionData.idH);  // : AGREGAMOS EL ID AL ARRAY
                                            var horaInicioHorario = moment(horarioData.horarioIni);
                                            var horaConTolerancia = horaInicioHorario.clone().add({ "minutes": horarioData.tolerancia });
                                            //: COMPARAMOS SI ES MAYOR A LA HORA DE INICIO DEL HORARIO
                                            if (horaInicial.isAfter(horaConTolerancia)) {
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
                                    }
                                    if (marcacionData.salida != 0) {
                                        if (permisoModificar == 1) {
                                            tbodyEntradaySalida += `<td>
                                                                        <div class="dropdown" id="">
                                                                            <a class="btn dropdown" type="button" data-toggle="dropdown" id="dropdownSalida${marcacionData.idMarcacion}" aria-haspopup="true" aria-expanded="false" 
                                                                                style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>
                                                                                ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                            </a>
                                                                            <ul class="dropdown-menu scrollable-menu"  aria-labelledby="dropdownSalida${marcacionData.idMarcacion}" style="padding: 0rem 0rem;">
                                                                                <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                                    <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                                    Opciones
                                                                                </h6>
                                                                                <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                                <div class="dropdown-item">
                                                                                    <div class="form-group noExport pl-3">
                                                                                        <a onclick="listaSalida(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.salida).format("HH:mm:ss")}',2)" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                            <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" height="12" />
                                                                                            Cambiar a entrada
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="dropdown-item">
                                                                                    <div class="form-group noExport pl-3">
                                                                                        <a onclick="listaEntrada(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.salida).format("HH:mm:ss")}',2)" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                        <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg"  height="12" />
                                                                                            Cambiar a salida
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="dropdown-item">
                                                                                    <div class="form-group noExport pl-3">
                                                                                        <a onclick="convertirOrden(${marcacionData.idMarcacion})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                            <img style="margin-bottom: 3px;" src="landing/images/flechasD.svg"  height="12" />
                                                                                            Convertir orden
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="dropdown-item">
                                                                                    <div class="form-group noExport pl-3">
                                                                                        <a onclick="asignarNuevaM(${marcacionData.idMarcacion},'${moment(marcacionData.salida).format("HH:mm:ss")}',2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                            <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                                                            Asignar a nueva marc.
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="dropdown-item">
                                                                                    <div class="form-group noExport pl-3">
                                                                                        <a onclick="eliminarM(${marcacionData.idMarcacion},2)" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                            <img style="margin-bottom: 3px;" src="landing/images/borrarD.svg"  height="12" />
                                                                                            Eliminar marc.
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
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
                                        if (horaFinal.isSameOrAfter(horaInicial)) {
                                            // * TIEMPO TOTAL TRABAJADA
                                            var tiempoRestante = horaFinal - horaInicial;
                                            segundosTiempo = moment.duration(tiempoRestante).seconds();
                                            minutosTiempo = moment.duration(tiempoRestante).minutes();
                                            horasTiempo = Math.trunc(moment.duration(tiempoRestante).asHours());
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
                                            if (marcacionData.idH != 0) {
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
                                                            if (data[index].data[m].marcaciones[j + 1] != undefined) {
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
                                    } else {
                                        if (permisoModificar == 1) {
                                            tbodyEntradaySalida += `<td>
                                                                        <div class="dropdown noExport">
                                                                            <a type="button" class="btn dropdown-toggle" id="dropSalida${marcacionData.idMarcacion}" data-toggle="dropdown" aria-haspopup="true" 
                                                                                aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                                                <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar hora">
                                                                                    <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                                    No tiene salida
                                                                                </span>
                                                                            </a>
                                                                            <ul class="dropdown-menu"  aria-labelledby="dropSalida${marcacionData.idMarcacion}" style="padding: 0rem 0rem;">
                                                                                <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                                    <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                                    Opciones
                                                                                </h6>
                                                                                <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                                <div class="dropdown-item">
                                                                                    <div class="form-group noExport pl-3">
                                                                                        <a onclick="javascript:insertarSalidaModal('${moment(marcacionData.entrada).format("HH:mm:ss")}',${marcacionData.idMarcacion},${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                            <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                                                            Insertar salida
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
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
                                    if (marcacionData.salida != 0) {
                                        //* COLUMNA DE ENTRADA
                                        if (permisoModificar == 1) {
                                            tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;">
                                                                        <div class=" dropdown noExport">
                                                                            <a class="btn dropdown-toggle" type="button" id="dropEntrada${marcacionData.idMarcacion}" data-toggle="dropdown" aria-haspopup="true" 
                                                                                aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                                                <span class="badge badge-soft-warning" data-toggle="tooltip" data-placement="left" title="Agregar hora">
                                                                                    <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                                                    No tiene entrada
                                                                                </span>
                                                                            </a>
                                                                            <ul class="dropdown-menu" aria-labelledby="dropEntrada${marcacionData.idMarcacion}" style="padding: 0rem 0rem;">
                                                                                <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                                    <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                                    Opciones
                                                                                </h6>
                                                                                <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                                <div class="dropdown-item">
                                                                                    <div class="form-group noExport pl-3">
                                                                                        <a onclick="javascript:insertarEntradaModal('${moment(marcacionData.salida).format("HH:mm:ss")}',${marcacionData.idMarcacion},${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                            <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                                                            Insertar entrada
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
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
                                                                                    <a class="btn dropdown" type="button" data-toggle="dropdown" id="dropdownSalida${marcacionData.idMarcacion}" aria-haspopup="true" aria-expanded="false"
                                                                                        style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                                        <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>
                                                                                        ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                                    </a>
                                                                                    <ul class="dropdown-menu scrollable-menu"  aria-labelledby="dropdownSalida${marcacionData.idMarcacion}" style="padding: 0rem 0rem;">
                                                                                        <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                                            <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                                            Opciones
                                                                                        </h6>
                                                                                        <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                                        <div class="dropdown-item">
                                                                                            <div class="form-group noExport pl-3">
                                                                                                <a onclick="listaSalida(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.salida).format("HH:mm:ss")}',2)" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                                    <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" height="12" />
                                                                                                    Cambiar a entrada
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="dropdown-item">
                                                                                            <div class="form-group noExport pl-3">
                                                                                                <a onclick="listaEntrada(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.salida).format("HH:mm:ss")}',2)" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                                <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg"  height="12" />
                                                                                                    Cambiar a salida
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="dropdown-item">
                                                                                            <div class="form-group noExport pl-3">
                                                                                                <a onclick="eliminarM(${marcacionData.idMarcacion},2)" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                                    <img style="margin-bottom: 3px;" src="landing/images/borrarD.svg"  height="12" />
                                                                                                    Eliminar marc.
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
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
                                                                <td name="tiempoSitHi" class="text-center">--</td>
                                                                <td name="tiempoSitHi" class="text-center">--</td>`;

                                    }
                                }
                            }
                            for (let mr = data[index].data[m].marcaciones.length; mr < arrayHorario[m].split(",")[0]; mr++) {
                                tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;" class="text-center">---</td>
                                                        <td class="text-center">---</td>
                                                        <td name="tiempoSitHi" class="text-center">---</td>
                                                        <td name="tiempoSitHi" class="text-center">--</td>
                                                        <td name="tiempoSitHi" class="text-center">--</td>
                                                        <td name="tiempoSitHi" class="text-center">--</td>`;
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
                                tbodyPausas += `<td style="border-left: 1px dashed #aaaaaa!important" name="datosPausa" class="text-center">----</td>
                                                <td name="datosPausa" class="text-center">-----</td>
                                                <td name="datosPausa" class="text-center">-----</td>
                                                <td name="datosPausa" class="text-center">------</td>`;
                            }
                            grupoHorario += tbodyPausas;
                        } else {
                            grupoHorario += `<td style="border-left: 2px solid #383e56!important;" class="text-center">
                                            ----
                                        </td>
                                        <td class="text-center">---</td><td class="text-center">---</td>`;
                            // ! MARCACIONES
                            var tbodyEntradaySalida = "";
                            for (let mr = 0; mr < arrayHorario[m].split(",")[0]; mr++) {
                                tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;" class="text-center">---</td>
                                                        <td class="text-center">---</td>
                                                        <td name="tiempoSitHi" class="text-center">---</td>
                                                        <td name="tiempoSitHi" class="text-center">--</td>
                                                        <td name="tiempoSitHi" class="text-center">--</td>
                                                        <td name="tiempoSitHi" class="text-center">--</td>`;
                            }
                            grupoHorario += tbodyEntradaySalida;
                            // ! PAUSAS
                            var tbodyPausas = "";
                            for (let cp = 0; cp < arrayHorario[m].split(",")[1]; cp++) {
                                tbodyPausas += `<td style="border-left: 1px dashed #aaaaaa!important" name="datosPausa" class="text-center">----</td>
                                                <td name="datosPausa" class="text-center">-----</td>
                                                <td name="datosPausa" class="text-center">-----</td>
                                                <td name="datosPausa" class="text-center">------</td>`;
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
                            <td class="text-center">--</td>
                            <td class="text-center">--</td>
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
                        tbodyTR += '<td></td><td></td><td></td>';
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

// TODO *********************************** FUNCIONALIDAD PARA MARCACIONES *******************************
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
// ! *********************************** CAMBIAR A ENTRADA ********************************************
// * FUNCION DE LISTA DE SALIDAS CON ENTRADAS NULL
function listaSalida(id, fecha, idEmpleado, hora, tipo) {
    $('#salidaM').empty();
    $('#c_horaS').text(hora);
    $('#c_tipoS').val(tipo);
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
                                    ${moment(element.salida).format("HH:mm:ss")}
                                </option>`;
                        } else {
                            container += `<option value="${element.id}">
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
            imagenesSalida();
        },
        error: function () { }
    });
}
// * FUNCION DE CAMBIAR ENTRADA
function cambiarEntradaM() {
    var idCambiar = $('#idMarcacion').val();
    var idMarcacion = $('#salidaM').val();
    var tipo = $('#c_tipoS').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/cambiarEM",
        data: {
            idCambiar: idCambiar,
            idMarcacion: idMarcacion,
            tipo: tipo
        },
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
            if (data == undefined) {
                $('#s_valid').hide();
                $('#listaSalidasMarcacion').modal('toggle');
                fechaValue.setDate(fechaGlobal);
                $('#btnRecargaTabla').click();
                limpiarAtributos();
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
                $('#s_valid').append(data.respuesta);
                $('#s_valid').show();
            }
        },
        error: function () {
        }
    });
}
// * COMBOX DE SALIDA
function imagenesSalida() {
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "landing/images/salidaD.svg";
        var $state = $(
            `<span>Salida : <img src="${baseUrl}" height="12" class="ml-1 mr-1" /> ${state.text} </span>`
        );
        return $state;
    };
    $("#salidaM").select2({
        templateResult: formatState
    });
}
// * VALIDACION
$('#formCambiarEntradaM').attr('novalidate', true);
$('#formCambiarEntradaM').submit(function (e) {
    e.preventDefault();
    console.log($("#salidaM").val());
    if ($("#salidaM").val() == "" || $("#salidaM").val() == null) {
        $('#s_valid').empty();
        $('#s_valid').append("Seleccionar marcación.");
        $('#s_valid').show();
        return;
    }
    $('#s_valid').empty();
    $('#s_valid').hide();
    this.submit();
});
// ! *********************************** CAMBIAR A SALIDA ****************************************************
// * FUNCION DE LISTA DE ENTRADAS CON SALIDAS NULL
function listaEntrada(id, fecha, idEmpleado, hora, tipo) {
    $('#entradaM').empty();
    $('#c_horaE').text(hora);
    $('#c_tipoE').val(tipo);
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
                                    ${moment(element.entrada).format("HH:mm:ss")}
                                </option>`;
                        } else {
                            container += `<option value="${element.id}">
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
            imagenesEntrada();
        },
        error: function () { }
    });
}
// * FUNCION DE CAMBIAR SALIDA
function cambiarSalidaM() {
    var idCambiar = $('#idMarcacionE').val();
    var idMarcacion = $('#entradaM').val();
    var tipo = $('#c_tipoE').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/cambiarSM",
        data: {
            idCambiar: idCambiar,
            idMarcacion: idMarcacion,
            tipo: tipo
        },
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
            if (data.respuesta == undefined) {
                $('#e_valid').hide();
                $('#listaEntradasMarcacion').modal('toggle');
                fechaValue.setDate(fechaGlobal);
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
                $('#e_valid').empty();
                $('#e_valid').append(data.respuesta);
                $('#e_valid').show();
            }
        },
        error: function () {
        }
    });
}
// * COMBOX DE ENTRADA
function imagenesEntrada() {
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "landing/images/entradaD.svg";
        var $state = $(
            `<span>Entrada : <img src="${baseUrl}" height="12" class="ml-1 mr-1" /> ${state.text} </span>`
        );
        return $state;
    };
    $("#entradaM").select2({
        templateResult: formatState
    });
}
// * VALIDACION
$('#formCambiarSalidaM').attr('novalidate', true);
$('#formCambiarSalidaM').submit(function (e) {
    e.preventDefault();
    if ($("#entradaM").val() == "" || $("#entradaM").val() == null) {
        $('#e_valid').empty();
        $('#e_valid').append("Seleccionar marcación.");
        $('#e_valid').show();
        return;
    }
    $('#e_valid').empty();
    $('#e_valid').hide();
    this.submit();
});
// ! *********************************** CONVERTIR ORDEN ******************************************************
// * CONVERTIR ORDEN
function convertirOrden(id) {
    alertify
        .confirm("¿Desea Convertir orden si o no?", function (
            e
        ) {
            if (e) {
                $.ajax({
                    async: false,
                    type: "POST",
                    url: "/convertirM",
                    data: {
                        id: id
                    },
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
                        if (data.respuesta == undefined) {
                            fechaValue.setDate(fechaGlobal);
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
                            $.notifyClose();
                            $.notify(
                                {
                                    message: data.respuesta,
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
                    },
                    error: function () { }
                });
            }
        })
        .setting({
            title: "Modificar Marcación",
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
}
// ! ********************************** ASIGNAR A NUEVA MARCACIÓN *********************************************
// * ASIGNAR NUEVA MARCACION
function asignarNuevaM(id, hora, tipo, horario) {
    $('#idMarcacionA').val(id);
    $('#tipoM').val(tipo);
    $('#a_hora').text(hora);
    if (tipo == 1) {
        $('#img_a').attr("src", "landing/images/entradaD.svg");
    } else {
        $('#img_a').attr("src", "landing/images/salidaD.svg");
    }
    $('#horarioM').empty();
    $.ajax({
        async: false,
        type: "POST",
        url: "/horarioxM",
        data: {
            id: id,
            tipo: tipo
        },
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
            var option = `<option value="0" selected>Sin horario</option>`;
            for (let index = 0; index < data.length; index++) {
                option += `<option value="${data[index].id}">Horario: ${data[index].horario_descripcion} (${data[index].horaI} - ${data[index].horaF})</option>`;
            }
            $('#horarioM').append(option);
            $('#horarioM').val(horario).trigger("change");
        },
        error: function () { }
    });
    $('#asignacionMarcacion').modal();
    $('#asignacionM').val(tipo).trigger('change');
}
// * GUARDAR ASIGNACION
function guardarAsignacion() {
    var id = $('#idMarcacionA').val();
    var idHorario = $('#horarioM').val();
    var tipoM = $('#tipoM').val();
    var tipo = $('#asignacionM').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/asignacionNew",
        data: {
            id: id,
            idHorario: idHorario,
            tipoM: tipoM,
            tipo: tipo
        },
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
            if (data.respuesta == undefined) {
                $('#a_valid').empty();
                $('#a_valid').hide();
                fechaValue.setDate(fechaGlobal);
                $('#btnRecargaTabla').click();
                $('#asignacionMarcacion').modal('toggle');
                limpiarAtributos();
            } else {
                $('#a_valid').empty();
                $('#a_valid').append(data.respuesta);
                $('#a_valid').show();
            }
        },
        error: function () { }
    });
}
$('#formGuardarAsignacion').attr('novalidate', true);
$('#formGuardarAsignacion').submit(function (e) {
    e.preventDefault();
    if ($("#horarioM").val() == "" || $("#horarioM").val() == null) {
        $('#a_valid').empty();
        $('#a_valid').append("Seleccionar horario.");
        $('#a_valid').show();
        return;
    }
    if ($("#asignacionM").val() == "" || $("#asignacionM").val() == null) {
        $('#a_valid').empty();
        $('#a_valid').append("Seleccionar marcación.");
        $('#a_valid').show();
        return;
    }
    $('#a_valid').empty();
    $('#a_valid').hide();
    this.submit();
});
// ! ********************************** ELIMINAR MARCACIÓN *****************************************************
// * ELIMINAR MARCACION
function eliminarM(id, tipo) {
    alertify
        .confirm("¿Desea eliminar marcación si o no?", function (
            e
        ) {
            if (e) {
                $.ajax({
                    async: false,
                    type: "POST",
                    url: "/eliminarMarcacion",
                    data: {
                        id: id,
                        tipo: tipo
                    },
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
                        fechaValue.setDate(fechaGlobal);
                        $('#btnRecargaTabla').click();
                        $.notifyClose();
                        $.notify({
                            message: data,
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
                    },
                    error: function () { }
                });
            }
        })
        .setting({
            title: "Eliminar Marcación",
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
}
// ! ******************************** INSERTAR SALIDA **********************************************************
// * MODAL DE INSERTAR SALIDA
function insertarSalidaModal(hora, id, idH) {
    $('#idMarcacionIS').val(id);
    $('#i_hora').text(hora);
    $('#idHorarioIS').val(idH);
    $('#insertarSalida').modal();
    horasS = $('#horaSalidaNueva').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",
        time_24hr: true,
        enableSeconds: true,
        static: true
    });
}
// * INSERTAR SALIDA
function insertarSalida() {
    var id = $('#idMarcacionIS').val();
    var salida = $('#horaSalidaNueva').val();
    var horario = $('#idHorarioIS').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/registrarNSalida",
        data: {
            id: id,
            salida: salida,
            horario: horario
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
            if (data.respuesta != undefined) {
                $('#i_validS').empty();
                $('#i_validS').append(data.respuesta);
                $('#i_validS').show();
            } else {
                $('#i_validS').empty();
                $('#i_validS').hide();
                $('#insertarSalida').modal('toggle');
                fechaValue.setDate(fechaGlobal);
                $('#btnRecargaTabla').click();
                limpiarAtributos();
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
            }
        },
        error: function () {
        },
    });
}
// * VALIDACION
$('#formInsertarSalida').attr('novalidate', true);
$('#formInsertarSalida').submit(function (e) {
    e.preventDefault();
    if ($("#horaSalidaNueva").val() == "00:00:00" || $("#horaSalidaNueva").val() == "00:00:0") {
        $('#i_validS').empty();
        $('#i_validS').append("Ingresar salida.");
        $('#i_validS').show();
        return;
    }
    $('#i_validS').empty();
    $('#i_validS').hide();
    this.submit();
});
// ! ********************************* INSERTAR ENTRADA ********************************************************
function insertarEntradaModal(hora, id, idH) {
    $('#idMarcacionIE').val(id);
    $('#ie_hora').text(hora);
    $('#idHorarioIE').val(idH);
    $('#insertarEntrada').modal();
    horasE = $('#horasEntradaNueva').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",
        time_24hr: true,
        enableSeconds: true,
        static: true
    });
}
// * INSERTAR SALIDA
function insertarEntrada() {
    var id = $('#idMarcacionIE').val();
    var entrada = $('#horasEntradaNueva').val();
    var horario = $('#idHorarioIE').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/registrarNEntrada",
        data: {
            id: id,
            entrada: entrada,
            horario: horario
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
            if (data.respuesta != undefined) {
                $('#i_validE').empty();
                $('#i_validE').append(data.respuesta);
                $('#i_validE').show();
            } else {
                $('#i_validE').empty();
                $('#i_validE').hide();
                $('#insertarEntrada').modal('toggle');
                fechaValue.setDate(fechaGlobal);
                $('#btnRecargaTabla').click();
                limpiarAtributos();
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
            }
        },
        error: function () {
        },
    });
}
// * VALIDACION
$('#formInsertarEntrada').attr('novalidate', true);
$('#formInsertarEntrada').submit(function (e) {
    e.preventDefault();
    if ($("#horasEntradaNueva").val() == "00:00:00" || $("#horasEntradaNueva").val() == "00:00:0") {
        $('#i_validE').empty();
        $('#i_validE').append("Ingresar entrada.");
        $('#i_validE').show();
        return;
    }
    $('#i_validE').empty();
    $('#i_validE').hide();
    this.submit();
});
// ! ****************************** CAMBIAR DE HORARIO ***********************************************************
var datosHorario = {};
// * MODAL CON LISTA DE HORARIOS
function modalCambiarHorario(idHE, fecha, id) {
    $('#idHorarioECH').val(idHE);
    $('#fechaCH').val(fecha);
    $('#idEmpleadoCH').val(id);
    $('#modalCambiarHorario').modal();
    $('#horarioXE').empty();
    $.ajax({
        async: false,
        type: "POST",
        url: "/horariosxEmpleado",
        data: {
            idHE: idHE,
            fecha: fecha,
            idEmpleado: id
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
            datosHorario = data;
            if (data.length != 0) {
                var container = `<option value="" disabled selected>Seleccionar horario</option>
                                <option value="0">Sin horario</option>`;
                for (let index = 0; index < data.length; index++) {
                    container += `<option value="${data[index].idHorarioE}">${data[index].descripcion} (${data[index].horaI} - ${data[index].horaF})</option>`;
                }
            } else {
                var container = `<option value="" disabled selected>No hay horarios disponibles</option>`;
            }
            $('#horarioXE').append(container);
        },
        error: function () { }
    });
}
// * MOSTRAR DETALLES DE HORARIO
$('#horarioXE').on("change", function () {
    $('#detalleHorarios').empty();
    var contenido = `<div class="col-md-12"><span style="color:#183b5d;font-weight: bold">Detalles de Horario</span></div>`;
    datosHorario.forEach(element => {
        if (element.idHorarioE == $('#horarioXE').val()) {
            contenido += `<div class="row ml-3 mr-4" style="background: #ffffff;border-radius: 5px">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <span style="color:#62778c;">Horario:</span>
                                        <span>${element.descripcion}</span>
                                    </div>
                                    <div class="col-md-12">
                                        <span style="color:#62778c;">Hora inicio:</span>
                                        <span>${element.horaI}</span>
                                    </div>
                                    <div class="col-md-12">
                                        <span style="color:#62778c;">Hora fin:</span>
                                        <span>${element.horaF}</span>
                                    </div>
                                    <div class="col-md-12">
                                        <span style="color:#62778c;">Horas obligadas:</span>
                                        <span>${element.horasObligadas}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <span style="color:#62778c;">Permitir trabajar fuera horario:</span>
                                        <span>${(element.fueraH == 1) ? "Si" : "No"}</span>
                                    </div>
                                    <div class="col-md-12">
                                        <span style="color:#62778c;">Tolerancia inicio (minutos):</span>
                                        <span>${element.toleranciaI}</span>
                                    </div>
                                    <div class="col-md-12">
                                        <span style="color:#62778c;">Tolerancia fin (minutos):</span>
                                        <span>${element.toleranciaF}</span>
                                    </div>
                                    <div class="col-md-12">
                                        <span style="color:#62778c;">Horas adicionales:</span>
                                        <span>${element.horasAdicionales}</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
        }
    });
    $('#detalleHorarios').append(contenido);
    $('#detalleHorarios').show();
});
// * FUNCION DE CAMBIAR DE HORARIO
function cambiarHorarioM() {
    var newH = $('#horarioXE').val();
    var idHE = $('#idHorarioECH').val();
    var fecha = $('#fechaCH').val();
    var idEmpleado = $('#idEmpleadoCH').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/cambiarHorarioM",
        data: {
            idHE: idHE,
            idNuevo: newH,
            fecha: fecha,
            idEmpleado: idEmpleado
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
            if (data.respuesta == undefined) {
                $('#ch_valid').hide();
                $('#modalCambiarHorario').modal("toggle");
                fechaValue.setDate(fechaGlobal);
                $('#btnRecargaTabla').click();
                limpiarAtributos();
            } else {
                $('#ch_valid').empty();
                $('#ch_valid').append(data.respuesta);
                $('#ch_valid').show();
            }
        },
        error: function () { }
    });
}
// * VALIDACION
$('#formCambiarHorarioM').attr('novalidate', true);
$('#formCambiarHorarioM').submit(function (e) {
    e.preventDefault();
    if ($("#horarioXE").val() == null || $("#horarioXE").val() == "") {
        $('#ch_valid').empty();
        $('#ch_valid').append("Seleccionar horario.");
        $('#ch_valid').show();
        return;
    }
    $('#ch_valid').empty();
    $('#ch_valid').hide();
    this.submit();
});
// ! ********************************* FINALIZACION *************************************************************
// * LIMPIEZA DE CAMPOS
function limpiarAtributos() {
    // ? MODAL DE CAMBIAR ENTRADA
    $('#entradaM').empty();
    $('#e_valid').empty();
    $('#e_valid').hide();
    $('#c_horaE').empty();
    // ? MODAL DE CAMBIAR SALIDA
    $('#salidaM').empty();
    $('#s_valid').empty();
    $('#s_valid').hide();
    $('#c_horaS').empty();
    // ? MODAL DE ASIGNACION A NUEVA MARCACIÓN
    $('#a_valid').empty();
    $('#a_valid').hide();
    $('#horarioM').empty();
    $('#a_hora').empty();
    // ? MODAL DE INSERTAR SALIDA
    $('#i_validS').empty();
    $('#i_validS').hide();
    if (horasS.config != undefined) {
        horasS.setDate("00:00:00");
    }
    // ? MODAL DE INSERTAR ENTRADA
    $('#i_validE').empty();
    $('#i_validE').hide();
    if (horasE.config != undefined) {
        horasE.setDate("00:00:00");
    }
    // ? MODAL DE CAMBIAR HORARIO
    $('#ch_valid').empty();
    $('#ch_valid').hide();
    $('#horarioXE').empty();
    $('#detalleHorarios').empty();
    $('#detalleHorarios').hide();
}