//* FECHA
var fechaValue = $("#fechaSelec").flatpickr({
    mode: "single",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "D, j F",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: false,
    disableMobile: true
});
var fechaGlobal = {};
var dataT = {};
var sent = false;
var paginaGlobal = 10;
// * INICIALIZAR TABLA
var table;
function inicializarTabla() {
    table = $("#tablaReport").DataTable({
        "searching": false,
        "scrollX": true,
        "ordering": false,
        "autoWidth": false,
        "lengthChange": true,
        processing: true,
        retrieve: true,
        lengthMenu: [10, 25, 50, 75, 100],
        pageLength: paginaGlobal,
        language: {
            sProcessing: "Generando informe...",
            processing: "<img src='landing/images/logoR.gif' height='60'>\n&nbsp;&nbsp;&nbsp;&nbsp;Generando informe...",
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
                pageLength: {
                    "_": "Mostrar %d registros"
                },
            },

        },
        dom: 'Bfrtip',
        lengthMenu: [10, 25, 50, 100],
        buttons: [
            {
                extend: 'pageLength',
                className: 'btn btn-sm mt-1',
            },
            {
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
                sheetName: 'CONTROL REGISTRO DE ASISTENCIA',
                title: 'MODO ASISTENCIA EN PUERTA - CONTROL REGISTRO DE ASISTENCIA',
                autoFilter: false,
                exportOptions: {
                    columns: ":visible:not(.noExport)",
                    format: {
                        body: function (data, row, column, node) {
                            var cont = $.trim($(node).text());
                            var cambiar = cont.replace('Cambiar a entrada', '');
                            cambiar = cambiar.replace('Cambiar a salida', '');
                            cambiar = cambiar.replace('No tiene entrada', '---');
                            cambiar = cambiar.replace('No tiene salida', '---');
                            cambiar = cambiar.replace('Opciones', '');
                            cambiar = cambiar.replace('Convertir orden', '');
                            cambiar = cambiar.replace('Asignar a nueva marc.', '');
                            cambiar = cambiar.replace('Eliminar marc.', '');
                            cambiar = cambiar.replace('Actualizar horario', '');
                            cambiar = cambiar.replace('Insertar salida', '');
                            cambiar = cambiar.replace('Insertar entrada', '');
                            cambiar = cambiar.split("/");
                            cambiar = cambiar.map(s => s.trim()).join("/")
                            return $.trim(cambiar);
                        }
                    }
                },
            },
            {
                extend: "pdfHtml5",
                className: 'btn btn-sm mt-1',
                text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                orientation: 'landscape',
                pageSize: 'A1',
                title: 'MODO ASISTENCIA EN PUERTA - CONTROL REGISTRO DE ASISTENCIA',
                exportOptions: {
                    columns: ":visible:not(.noExport)"
                },
                customize: function (doc) {
                    var bodyCompleto = [];
                    doc.content[1].table.body.forEach(function (line, i) {
                        var bodyNuevo = [];
                        if (i >= 1) {
                            line.forEach(element => {
                                var textOriginal = element.text;
                                var cambiar = textOriginal.replace('Cambiar a entrada', '');
                                cambiar = cambiar.replace('Cambiar a salida', '');
                                cambiar = cambiar.replace('No tiene entrada', '---');
                                cambiar = cambiar.replace('No tiene salida', '---');
                                cambiar = cambiar.replace('Opciones', '');
                                cambiar = cambiar.replace('Convertir orden', '');
                                cambiar = cambiar.replace('Asignar a nueva marc.', '');
                                cambiar = cambiar.replace('Eliminar marc.', '');
                                cambiar = cambiar.replace('Actualizar horario', '');
                                cambiar = cambiar.replace('Insertar salida', '');
                                cambiar = cambiar.replace('Insertar entrada', '');
                                cambiar = $.trim(cambiar);
                                cambiar = cambiar.split("/");
                                cambiar = cambiar.map(s => s.trim()).join("/")
                                bodyNuevo.push({ text: cambiar, style: 'defaultStyle' });
                            });
                            bodyCompleto.push(bodyNuevo);
                        } else {
                            bodyCompleto.push(line);
                        }
                    });
                    doc['styles'] = {
                        table: {
                            width: '100%'
                        },
                        tableHeader: {
                            bold: true,
                            fontSize: 11,
                            color: '#ffffff',
                            fillColor: '#14274e',
                            alignment: 'left'
                        },
                        defaultStyle: {
                            fontSize: 10,
                            alignment: 'left'
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
                                        { text: '\n\nRazón Social:\t\t\t\t\t\t', bold: false }, { text: razonSocial, bold: false },
                                        { text: '\nDirección:\t\t\t\t\t\t\t', bold: false }, { text: '\t' + direccion, bold: false },
                                        { text: '\nNúmero de Ruc:\t\t\t\t\t', bold: false }, { text: ruc, bold: false },
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
            }
        ],
        paging: true,
        initComplete: function () {
            dataT = this;
            setTimeout(function () {
                $("#tablaReport").DataTable().draw();
            }, 1);
            this.api().page.len(paginaGlobal).draw(false);
        },
        drawCallback: function () {
            var api = this.api();
            var len = api.page.len();
            paginaGlobal = len;
        },
        rowCallback: function (row, data, index) {
            console.log($(this.api().row(index).node()).hasClass('prueba'), index);
        }
    }).draw();
}
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
    inicializarTabla();
    cambiarF();
    // * HORARIO PADRE
    $('.horarioPadre').find('input[type=checkbox]').prop({
        indeterminate: true,
        checked: false
    });
    // * CALCULO DE TIEMPOS PADRE
    $('.detallePadre').find('input[type=checkbox]').prop({
        indeterminate: true,
        checked: false
    });
    // * INCIDENCIA
    $('.incidenciaPadre').find('input[type=checkbox]').prop({
        indeterminate: true,
        checked: false
    });
    // * POR HOTAS TOTALES
    $('#porTotal').prop({
        indeterminate: true,
        checked: false
    });
});
// * VARIABLES PARA EXCEL Y PDF
var razonSocial;
var direccion;
var ruc;
// * HORAS PARA INSERTAR ENTRADA Y SALIDA
var horasE = {};
var horasS = {};
// * ESTADO DE HORARIO EMPLEADO
var contenidoHorario = [];
function cargartabla(fecha) {
    var idemp = $('#idempleado').val();
    $.ajax({
        type: "GET",
        url: "/reporteTablaMarca",
        data: {
            fecha, idemp
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
        beforeSend: function () {
            $("#tablaReport").css('opacity', .3);
            $('div.dataTables_processing').show();
        },
    }).then(function (data) {
        $('div.dataTables_processing').hide();
        $("#tablaReport").css('opacity', 1);
        fechaGlobal = fecha;
        contenidoHorario.length = 0;
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
            theadTabla += `<th>#&nbsp;</th>
                            <th class="text-center">Fecha</th>
                            <th>Número de documento</th>
                            <th name="colCodigo" class="colCodigo">Código de trabajador</th>
                            <th>Nombres y apellidos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <th name="colCargo" class="colCargo">Cargo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>`;
            //* GRUPO DE HORARIOS
            for (let m = 0; m < cantidadGruposHorario; m++) {
                // ************************************ DATOS DEL GRUPO HORARIO **************************************
                // !HORARIO
                theadTabla += `<th class="text-center descripcionHorario" style="border-left: 2px solid #383e56!important;font-weight: 600 !important" name="descripcionHorario">
                                    <span>
                                        Descripción del horario <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${m + 1}</b>
                                    </span>
                                </th>
                                <th class="text-center horarioHorario" name="horarioHorario">
                                    <span>
                                        Horario <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${m + 1}</b>
                                    </span>
                                </th>
                                <th name="toleranciaIHorario" class="toleranciaIHorario">Tolerancia en el ingreso</th>
                                <th name="toleranciaFHorario" class="toleranciaFHorario">Tolerancia en la salida</th>
                                <th name="colTiempoEntreH" class="text-center colTiempoEntreH">Tiempo total</th>
                                <th name="colHoraNormal" class="text-center colHoraNormal">Horario normal</th>
                                <th name="colSobreTNormal" class="text-center colSobreTNormal">Sobretiempo normal</th>
                                <th name="colHoraNocturna" class="text-center colHoraNocturna">Horario nocturno</th>
                                <th name="colSobreTNocturno" class="text-center colSobreTNocturno">Sobretiempo nocturno</th>
                                <th name="colSobreTiempo" class="text-center colSobreTiempo">Sobretiempo</th>
                                <th name="colFaltaJornada" class="text-center colFaltaJornada">Jornada incompleta</th>
                                <th name="colTardanza" class="text-center colTardanza">Tardanza</th>
                                <th name="faltaHorario" class="faltaHorario">Falta</th>
                                <th name="colHE25D" class="text-center colHE25D">H.E. 25% Diurnas</th>
                                <th name="colHE35D" class="text-center colHE35D">H.E. 35% Diurnas</th>
                                <th name="colHE100D" class="text-center colHE100D">H.E. 100% Diurnas</th>`;
                // ! MARCACION
                var cantidadColumnasM = arrayHorario[m].split(",")[0];
                for (let j = 0; j < cantidadColumnasM; j++) {
                    theadTabla += `<th style="border-left:1px dashed #aaaaaa!important;" class="text-center colMarcaciones" name="colMarcaciones">
                                        <span>
                                            Entrada <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${j + 1}</b>
                                        </span>
                                    </th>
                                    <th class="text-center colMarcaciones" name="colMarcaciones">
                                        <span>
                                            Salida <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${j + 1}</b>
                                        </span>
                                    </th>
                                    <th id="tSitio" name="colTiempoS" class="colTiempoS">
                                        <span>
                                            Tiempo total <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${j + 1}</b>
                                        </span>
                                    </th>`;
                }
                // ! PAUSAS
                var cantidadColumnasP = arrayHorario[m].split(",")[1];
                for (let p = 0; p < cantidadColumnasP; p++) {
                    theadTabla += `<th style="border-left: 1px dashed #aaaaaa!important;" name="descripcionPausa" class="descripcionPausa">
                                        <span>Pausa<b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${p + 1}</b></span>
                                    </th>
                                    <th name="horarioPausa" class="horarioPausa">
                                        <span>Horario de pausa <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${p + 1}</b></span>
                                    </th>
                                    <th name="tiempoPausa" class="tiempoPausa">
                                        <span>Tiempo de pausa <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${p + 1}</b></span>
                                    </th>
                                    <th name="excesoPausa" class="excesoPausa">
                                        <span>Exceso de pausa <b style="font-size: 12px !important;color: #383e56;font-weight: 600 !important">${p + 1}</b></span>
                                    </th>`;
                }
            }
            theadTabla += `<th style="border-left: 2px solid #383e56!important;" name="colTiempoTotal" class="colTiempoTotal">Tiempo total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colHoraNormalTotal" class="colHoraNormalTotal">Horario normal total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colHoraNocturnaTotal" class="colHoraNocturnaTotal">Horario nocturno total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colSobreTiempoTotal" class="colSobreTiempoTotal">Sobretiempo total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colFaltaJornadaTotal" class="colFaltaJornadaTotal">Jornada incompleta total</th>  
                            <th style="border-left: 1px dashed #aaaaaa!important" name="colTardanzaTotal" class="colTardanzaTotal">Tardanza total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="faltaTotal" class="faltaTotal">Falta total</th>
                            <th style="border-left: 1px dashed #aaaaaa!important" name="incidencia" class="incidencia">Incidencias</th>`;
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
                tbody += `<tr class="prueba">`;
                if (permisoModificar == 1) {
                    tbody += `<td class="noExport text-center">
                                <a onclick="javascript:modalAgregarMarcacion(${data[index].emple_id},'${fecha}')" data-toggle="tooltip" data-placement="left" title="Agregar marcaciones."
                                    data-html="true" style="cursor:pointer">
                                    <img style="margin-bottom: 3px;" src="landing/images/addD.svg"  height="17" />
                                </a>
                            </td>`;
                }
                tbody += `<td>${(index + 1)}&nbsp;</td>
                            <td>${fechaGlobal}</td>
                            <td class="text-center">${data[index].emple_nDoc}</td>
                            <td class="text-center" name="colCodigo">${data[index].emple_codigo}</td>
                            <td>${data[index].perso_nombre} ${data[index].perso_apPaterno} ${data[index].perso_apMaterno}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;
                tbody += `<td name="colCargo">${data[index].cargo_descripcion}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>`;

                // * ARMAR GRUPO DE HORARIOS
                var grupoHorario = "";
                //* ARMAR Y ORDENAR MARCACIONES
                var sumaTiempos = moment("00:00:00", "HH:mm:ss");       //: SUMANDO LOS TIEMPOS
                var sumaTardanzas = moment("00:00:00", "HH:mm:ss");     //: SUMANDO TARDANZAS
                var sumaSobreTiempo = moment("00:00:00", "HH:mm:ss");   //: SUMANDO SOBRE TIEMPO
                var sumaFaltaJornada = moment("00:00:00", "HH:mm:ss");  //: SUMANDO FALTA JORNADA
                var sumaHorasNormalesT = moment("00:00:00", "HH:mm:ss"); //: SUMANDO TOTALES DE HORAS NORMALES
                var sumaHorasNocturnasT = moment("00:00:00", "HH:mm:ss"); //: SUMANDO TOTALES DE HORAS NOCTURNAS
                // * CANTIDAD DE FALTAS
                var sumaFaltas = 0;
                for (let m = 0; m < cantidadGruposHorario; m++) {
                    // : HORARIO
                    var idHorarioM = [];
                    // : PAUSA
                    var idPausas = [];
                    // * PAUSAS
                    var tiempoHoraPausa = "00";        //: HORAS TARDANZA
                    var tiempoMinutoPausa = "00";      //: MINUTOS TARDANZA
                    var tiempoSegundoPausa = "00";     //: SEGUNDOS TARDANZA
                    var sumaTiemposEntreHorarios = moment("00:00:00", "HH:mm:ss");       //: SUMANDO LOS TIEMPOS ENTRE HORARIOS
                    var estadoTiempoHorario = true;
                    //* EXCESO
                    var tiempoHoraExceso = "00";
                    var tiempoMinutoExceso = "00";
                    var tiempoSegundoExceso = "00";
                    // * TARDANZA
                    var segundosTardanza = "00";
                    var minutosTardanza = "00";
                    var horasTardanza = "00";
                    // * SOBRE TIEMPO
                    var segundosSobreT = "00";
                    var minutosSobreT = "00";
                    var horasSobreT = "00";
                    // * FALTA JORNADA
                    var segundosFaltaJ = "00";
                    var minutosFaltaJ = "00";
                    var horasFaltaJ = "00";
                    // * HORARIO NORMAL
                    var sumaHorasNormales = moment("00:00:00", "HH:mm:ss");
                    var sobretiempoNormales = moment("00:00:00", "HH:mm:ss");
                    // * HORARIO NOCTURNO
                    var sumaHorasNocturnas = moment("00:00:00", "HH:mm:ss");
                    var sobretiempoNocturnos = moment("00:00:00", "HH:mm:ss");
                    // * HORAS EXTRAS - DIURNAS
                    var diurnas25 = moment("00:00:00", "HH:mm:ss").format("HH:mm:ss");
                    var diurnas35 = moment("00:00:00", "HH:mm:ss").format("HH:mm:ss");
                    var diurnas100 = moment("00:00:00", "HH:mm:ss").format("HH:mm:ss");
                    // * HORAS EXTRAS - NOCTURNAS
                    var nocturnas25 = moment("00:00:00", "HH:mm:ss").format("HH:mm:ss");
                    var noctunas35 = moment("00:00:00", "HH:mm:ss").format("HH:mm:ss");
                    var noctunas100 = moment("00:00:00", "HH:mm:ss").format("HH:mm:ss");
                    if (data[index].data[m] != undefined) {
                        // ! ******************************************* COLUMNAS DE HORARIOS **************************************************
                        var horarioData = data[index].data[m].horario;
                        contenidoHorario.push({ "idEmpleado": data[index].emple_id, "idHorarioE": horarioData.idHorarioE, "estado": horarioData.estado });
                        // ! ******************************************* CALCULOS ENTRE HORARIOS ***********************************************
                        // * DATA PARA TARDANZA
                        if (horarioData.idHorario != 0) {
                            if (data[index].data[m].marcaciones[0] != undefined) {
                                if (data[index].data[m].marcaciones[0].entrada != 0) {
                                    // * TARDANZA
                                    var dataParaTardanza = data[index].data[m].marcaciones[0];
                                    if (dataParaTardanza.idH != 0) {
                                        var horaInicial = moment(dataParaTardanza.entrada);
                                        // ******************************* TARDANZA ***************************************
                                        // ! PARA QUE TOME SOLO TARDANZA EN LA PRIMERA MARCACION
                                        if (!idHorarioM.includes(dataParaTardanza.idH)) {
                                            idHorarioM.push(dataParaTardanza.idH);  // : AGREGAMOS EL ID AL ARRAY
                                            var horaInicioHorario = moment(horarioData.horarioIni);
                                            var horaConTolerancia = horaInicioHorario.clone().add({ "minutes": horarioData.toleranciaI });
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
                                                sumaTardanzas = sumaTardanzas.add({ "hours": horasTardanza, "minutes": minutosTardanza, "seconds": segundosTardanza });
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // * DATA PARA ENTRE HORARIO
                        for (let res = 0; res < data[index].data[m].marcaciones.length; res++) {
                            var dataM = data[index].data[m].marcaciones[res];
                            // * CALCULAR TIEMPO TOTAL , TIEMPO DE PAUSA Y EXCESO DE PAUSAS
                            if (dataM.entrada != 0 && dataM.salida != 0) {
                                var horaFinalData = moment(dataM.salida);
                                var horaInicialData = moment(dataM.entrada);
                                if (horaFinalData.isSameOrAfter(horaInicialData)) {
                                    // * HORAS NORMALES
                                    var entradaNormal = horaInicialData.clone().format("HH:mm");
                                    if (entradaNormal > "06:00" && entradaNormal < "22:00") {
                                        var tiempoNormal = horaFinalData - horaInicialData;
                                        var segundosNormal = moment.duration(tiempoNormal).seconds();
                                        var minutosNormal = moment.duration(tiempoNormal).minutes();
                                        var horasNormal = Math.trunc(moment.duration(tiempoNormal).asHours());
                                        sumaHorasNormales = sumaHorasNormales.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                        sumaHorasNormalesT = sumaHorasNormalesT.add({ "hours": horasNormal, "minutes": minutosNormal, "seconds": segundosNormal });
                                    } else {
                                        // * HORAS NOCTURNAS
                                        var tiempoNocturno = horaFinalData - horaInicialData;
                                        var segundosNocturno = moment.duration(tiempoNocturno).seconds();
                                        var minutosNocturno = moment.duration(tiempoNocturno).minutes();
                                        var horasNocturno = Math.trunc(moment.duration(tiempoNocturno).asHours());
                                        sumaHorasNocturnas = sumaHorasNocturnas.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                        sumaHorasNocturnasT = sumaHorasNocturnasT.add({ "hours": horasNocturno, "minutes": minutosNocturno, "seconds": segundosNocturno });
                                    }
                                    // * TIEMPO TOTAL TRABAJADA
                                    var tiempoRestanteD = horaFinalData - horaInicialData;
                                    var segundosTiempoD = moment.duration(tiempoRestanteD).seconds();
                                    var minutosTiempoD = moment.duration(tiempoRestanteD).minutes();
                                    var horasTiempoD = Math.trunc(moment.duration(tiempoRestanteD).asHours());
                                    if (horasTiempoD < 10) {
                                        horasTiempoD = '0' + horasTiempoD;
                                    }
                                    if (minutosTiempo < 10) {
                                        minutosTiempoD = '0' + minutosTiempoD;
                                    }
                                    if (segundosTiempoD < 10) {
                                        segundosTiempoD = '0' + segundosTiempoD;
                                    }
                                    sumaTiemposEntreHorarios = sumaTiemposEntreHorarios.add({ "hours": horasTiempoD, "minutes": minutosTiempoD, "seconds": segundosTiempoD });
                                }
                            }
                        }
                        // * SOBRETIEMPO ENTRE HORARIO Y FALTA DE JORNADA
                        if (horarioData.idHorario != 0) {
                            var horasObligadas = moment(horarioData.horasObligadas, ["HH:mm:ss"]);
                            var tiempoEntreH = moment(sumaTiemposEntreHorarios.format("HH:mm:ss"), ["HH:mm:ss"]);
                            // * SOBRETIEMPO
                            if (tiempoEntreH > horasObligadas) {
                                // * SOBRE TIEMPO 
                                var tiempoSobreT = tiempoEntreH - horasObligadas;
                                segundosSobreT = moment.duration(tiempoSobreT).seconds();
                                minutosSobreT = moment.duration(tiempoSobreT).minutes();
                                horasSobreT = Math.trunc(moment.duration(tiempoSobreT).asHours());
                                if (horasSobreT < 10) {
                                    horasSobreT = '0' + horasSobreT;
                                }
                                if (minutosSobreT < 10) {
                                    minutosSobreT = '0' + minutosSobreT;
                                }
                                if (segundosSobreT < 10) {
                                    segundosSobreT = '0' + segundosSobreT;
                                }
                                sumaSobreTiempo = sumaSobreTiempo.add({ "hours": horasSobreT, "minutes": minutosSobreT, "seconds": segundosSobreT });
                            } else {
                                // * FALTA JORNADA
                                var tiempoFaltaJ = horasObligadas - tiempoEntreH;
                                segundosFaltaJ = moment.duration(tiempoFaltaJ).seconds();
                                minutosFaltaJ = moment.duration(tiempoFaltaJ).minutes();
                                horasFaltaJ = Math.trunc(moment.duration(tiempoFaltaJ).asHours());
                                if (horasFaltaJ < 10) {
                                    horasFaltaJ = '0' + horasFaltaJ;
                                }
                                if (minutosFaltaJ < 10) {
                                    minutosFaltaJ = '0' + minutosFaltaJ;
                                }
                                if (segundosFaltaJ < 10) {
                                    segundosFaltaJ = '0' + segundosFaltaJ;
                                }
                                sumaFaltaJornada = sumaFaltaJornada.add({ "hours": horasFaltaJ, "minutes": minutosFaltaJ, "seconds": segundosFaltaJ });
                            }
                            // * HORAS NORMALES
                            if (sumaHorasNormales > horasObligadas) {
                                // * HORAS EXTRAS
                                var tiempoExtraResta = sumaHorasNormales - horasObligadas;
                                var segundosExtra = moment.duration(tiempoExtraResta).seconds();
                                var minutosExtra = moment.duration(tiempoExtraResta).minutes();
                                var horasExtra = Math.trunc(moment.duration(tiempoExtraResta).asHours());
                                var tiempoExtra = moment({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra }).format("HH:mm:ss");
                                sobretiempoNormales = moment({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra }).format("HH:mm:ss");
                                var tiempoSobrante = {};
                                if (moment(tiempoExtra, "HH:mm:ss").isAfter(moment("02:00:00", "HH:mm:ss"))) {
                                    diurnas25 = moment("02:00:00", "HH:mm:ss").format("HH:mm:ss");
                                    var restaDe25 = moment(tiempoExtra, "HH:mm:ss") - moment("02:00:00", "HH:mm:ss");
                                    var horasDe25 = Math.trunc(moment.duration(restaDe25).asHours());
                                    var minutosDe25 = moment.duration(restaDe25).minutes();
                                    var segundosDe25 = moment.duration(restaDe25).seconds();
                                    tiempoSobrante = moment({ "hours": horasDe25, "minutes": minutosDe25, "seconds": segundosDe25 }).format("HH:mm:ss");
                                    if (moment(tiempoSobrante, "HH:mm:ss").isAfter(moment("02:00:00", "HH:mm:ss"))) {
                                        diurnas35 = moment("02:00:00", "HH:mm:ss").format("HH:mm:ss");
                                        var restaDe35 = moment(tiempoSobrante, "HH:mm:ss") - moment("02:00:00", "HH:mm:ss");
                                        var horasDe35 = Math.trunc(moment.duration(restaDe35).asHours());
                                        var minutosDe35 = moment.duration(restaDe35).minutes();
                                        var segundosDe35 = moment.duration(restaDe35).seconds();
                                        tiempoSobrante = moment({ "hours": horasDe35, "minutes": minutosDe35, "seconds": segundosDe35 }).format("HH:mm:ss");
                                        if (moment(tiempoSobrante, "HH:mm:ss").isAfter(moment("00:00:00", "HH:mm:ss"))) {
                                            diurnas100 = moment({ "hours": horasDe35, "minutes": minutosDe35, "seconds": segundosDe35 }).format("HH:mm:ss");
                                        }
                                    } else {
                                        if (moment(tiempoSobrante, "HH:mm:ss").isAfter(moment("00:00:00", "HH:mm:ss"))) {
                                            diurnas35 = moment({ "hours": horasDe25, "minutes": minutosDe25, "seconds": segundosDe25 }).format("HH:mm:ss");
                                        }
                                    }
                                } else {
                                    diurnas25 = moment({ "hours": horasExtra, "minutes": minutosExtra, "seconds": segundosExtra }).format("HH:mm:ss");
                                }
                            }
                            // * HORAS NOCTURNAS
                            if (sumaHorasNocturnas > horasObligadas) {
                                // * HORAS EXTRAS
                                var tiempoExtraRestaN = sumaHorasNocturnas - horasObligadas;
                                var segundosExtraN = moment.duration(tiempoExtraRestaN).seconds();
                                var minutosExtraN = moment.duration(tiempoExtraRestaN).minutes();
                                var horasExtraN = Math.trunc(moment.duration(tiempoExtraRestaN).asHours());
                                var tiempoExtraN = moment({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN }).format("HH:mm:ss");
                                sobretiempoNocturnos = moment({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN }).format("HH:mm:ss");
                                var tiempoSobranteN = {};
                                if (moment(tiempoExtraN, "HH:mm:ss").isAfter(moment("02:00:00", "HH:mm:ss"))) {
                                    noctunas25 = moment("02:00:00", "HH:mm:ss").format("HH:mm:ss");
                                    var restaDe25N = moment(tiempoExtraN, "HH:mm:ss") - moment("02:00:00", "HH:mm:ss");
                                    var horasDe25N = Math.trunc(moment.duration(restaDe25N).asHours());
                                    var minutosDe25N = moment.duration(restaDe25N).minutes();
                                    var segundosDe25N = moment.duration(restaDe25N).seconds();
                                    tiempoSobranteN = moment({ "hours": horasDe25N, "minutes": minutosDe25N, "seconds": segundosDe25N }).format("HH:mm:ss");
                                    if (moment(tiempoSobranteN, "HH:mm:ss").isAfter(moment("02:00:00", "HH:mm:ss"))) {
                                        noctunas35 = moment("02:00:00", "HH:mm:ss").format("HH:mm:ss");
                                        var restaDe35N = moment(tiempoSobranteN, "HH:mm:ss") - moment("02:00:00", "HH:mm:ss");
                                        var horasDe35N = Math.trunc(moment.duration(restaDe35N).asHours());
                                        var minutosDe35N = moment.duration(restaDe35N).minutes();
                                        var segundosDe35N = moment.duration(restaDe35N).seconds();
                                        tiempoSobranteN = moment({ "hours": horasDe35N, "minutes": minutosDe35N, "seconds": segundosDe35N }).format("HH:mm:ss");
                                        if (moment(tiempoSobranteN, "HH:mm:ss").isAfter(moment("00:00:00", "HH:mm:ss"))) {
                                            noctunas100 = moment({ "hours": horasDe35N, "minutes": minutosDe35N, "seconds": segundosDe35N }).format("HH:mm:ss");
                                        }
                                    } else {
                                        if (moment(tiempoSobranteN, "HH:mm:ss").isAfter(moment("00:00:00", "HH:mm:ss"))) {
                                            noctunas35 = moment({ "hours": horasDe25N, "minutes": minutosDe25N, "seconds": segundosDe25N }).format("HH:mm:ss");
                                        }
                                    }
                                } else {
                                    noctunas25 = moment({ "hours": horasExtraN, "minutes": minutosExtraN, "seconds": segundosExtraN }).format("HH:mm:ss");
                                }
                            }
                        }
                        if (permisoModificar == 1) {
                            if (horarioData.horario != null) {
                                if (horarioData.estado == 1) {
                                    grupoHorario += `<td style="border-left: 2px solid #383e56!important;background: #fafafa;" class="text-center" name="descripcionHorario">
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
                                                                <div class="dropdown-item dropdown-itemM">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a onclick="modalCambiarHorario('${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img src="landing/images/calendarioAD.svg" height="15" />
                                                                            Actualizar horario
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td name="horarioHorario" style="background: #fafafa;">
                                                        <div class="dropdown">
                                                            <a class="btn dropdown" type="button" data-toggle="dropdown" id="dropdownHorarioH${horarioData.idHorario}" aria-haspopup="true" aria-expanded="false" 
                                                                style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                ${moment(horarioData.horarioIni).format("HH:mm:ss")} - ${moment(horarioData.horarioFin).format("HH:mm:ss")}
                                                            </a>
                                                            <ul class="dropdown-menu scrollable-menu"  aria-labelledby="dropdownHorarioH${horarioData.idHorario}" style="padding: 0rem 0rem;">
                                                                <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                    <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                    Opciones
                                                                </h6>
                                                                <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                <div class="dropdown-item dropdown-itemM">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a onclick="modalCambiarHorario('${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img src="landing/images/calendarioAD.svg" height="15" />
                                                                            Actualizar horario
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td class="text-center" name="toleranciaIHorario" style="background: #fafafa;">${horarioData.toleranciaI} min.</td>
                                                    <td class="text-center" name="toleranciaFHorario" style="background: #fafafa;">${horarioData.toleranciaF} min.</td>
                                                    <td name="colTiempoEntreH" class="text-center" style="background: #fafafa;">
                                                        <a class="badge badge-soft-primary mr-2">
                                                            <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                            ${moment(sumaTiemposEntreHorarios).format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colHoraNormal" class="text-center colHoraNormal" style="background: #fafafa;">
                                                        <a class="badge badge-soft-warning mr-2">
                                                            <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                            ${moment(sumaHorasNormales).format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colSobreTNormal" class="text-center colSobreTNormal" style="background: #fafafa;">
                                                        <a class="badge badge-soft-warning mr-2">
                                                            <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                            ${moment(sobretiempoNormales, "HH:mm:ss").format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colHoraNocturna" class="text-center colHoraNocturna" style="background: #fafafa;">
                                                        <a class="badge badge-soft-info mr-2">
                                                            <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                            ${moment(sumaHorasNocturnas).format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colSobreTNocturno" class="text-center colSobreTNocturno" style="background: #fafafa;">
                                                        <a class="badge badge-soft-info mr-2">
                                                            <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                            ${moment(sobretiempoNocturnos, "HH:mm:ss").format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colSobreTiempo" class="text-center" style="background: #fafafa;">
                                                        <a class="badge badge-soft-primary mr-2">
                                                            <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                            ${horasSobreT}:${minutosSobreT}:${segundosSobreT}
                                                        </a>
                                                    </td>
                                                    <td name="colFaltaJornada" class="text-center" style="background: #fafafa;">
                                                        <a class="badge badge-soft-danger mr-2">
                                                            <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                            ${horasFaltaJ}:${minutosFaltaJ}:${segundosFaltaJ}
                                                        </a>
                                                    </td>
                                                    <td name="colTardanza" style="background: #fafafa;">
                                                        <a class="badge badge-soft-danger mr-2">
                                                            <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                            ${horasTardanza}:${minutosTardanza}:${segundosTardanza}
                                                        </a>
                                                    </td>`;
                                    if (data[index].data[m].marcaciones.length == 0 && data[index].incidencias.length == 0) {
                                        sumaFaltas++;
                                        grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;">
                                                            <span class="badge badge-soft-danger mr-2" class="text-center">
                                                                Falta
                                                            </span>
                                                        </td>`;
                                    } else {
                                        grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;">---</td>`;
                                    }
                                    grupoHorario += `<td name="colHE25D" class="text-center colHE25D" style="background: #fafafa;">${diurnas25}</td>
                                                    <td name="colHE35D" class="text-center colHE35D" style="background: #fafafa;">${diurnas35}</td>
                                                    <td name="colHE100D" class="text-center colHE100D" style="background: #fafafa;">${diurnas100}</td>`;
                                } else {
                                    grupoHorario += `<td style="border-left: 2px solid #383e56!important;background: #fafafa;" class="text-center" name="descripcionHorario">
                                                        <div class="dropdown">
                                                            <a class="btn dropdown" type="button" data-toggle="dropdown" id="dropdownHorario${horarioData.idHorario}" aria-haspopup="true" aria-expanded="false" 
                                                                style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                <span class="badge badge-soft-danger mr-2" class="text-center" data-toggle="tooltip" data-placement="left" title="Actualizar horario" data-html="true">
                                                                    <img style="margin-bottom: 3px;" src="admin/images/warning.svg" class="mr-2" height="12"/>
                                                                    ${horarioData.horario}
                                                                </span>
                                                            </a>
                                                            <ul class="dropdown-menu scrollable-menu"  aria-labelledby="dropdownHorario${horarioData.idHorario}" style="padding: 0rem 0rem;">
                                                                <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                    <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                    Opciones
                                                                </h6>
                                                                <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                <div class="dropdown-item dropdown-itemM noExport">
                                                                    <div class="form-group noExport pl-3 mt-1" style="margin-bottom: 0.5rem;">
                                                                        <a onclick="modalCambiarHorario('${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img src="landing/images/calendarioAD.svg" height="15" />
                                                                            Actualizar horario
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td name="horarioHorario" style="background: #fafafa;">
                                                        <div class="dropdown">
                                                            <a class="btn dropdown" type="button" data-toggle="dropdown" id="dropdownHorarioH${horarioData.idHorario}" aria-haspopup="true" aria-expanded="false" 
                                                                style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                <img style="margin-bottom: 3px;" src="admin/images/warning.svg" class="mr-2" height="12"/>
                                                                ${moment(horarioData.horarioIni).format("HH:mm:ss")} - ${moment(horarioData.horarioFin).format("HH:mm:ss")}
                                                            </a>
                                                            <ul class="dropdown-menu scrollable-menu"  aria-labelledby="dropdownHorarioH${horarioData.idHorario}" style="padding: 0rem 0rem;">
                                                                <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                    <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                    Opciones
                                                                </h6>
                                                                <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                <div class="dropdown-item dropdown-itemM">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a onclick="modalCambiarHorario('${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img src="landing/images/calendarioAD.svg" height="15" />
                                                                            Actualizar horario
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td class="text-center" name="toleranciaIHorario" style="background: #fafafa;">${horarioData.toleranciaI} min.</td>
                                                    <td class="text-center" name="toleranciaFHorario" style="background: #fafafa;">${horarioData.toleranciaF} min.</td>
                                                    <td name="colTiempoEntreH" class="text-center" style="background: #fafafa;">
                                                        <a class="badge badge-soft-primary mr-2">
                                                            <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                            ${moment(sumaTiemposEntreHorarios).format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colHoraNormal" class="text-center colHoraNormal" style="background: #fafafa;">
                                                        <a class="badge badge-soft-warning mr-2">
                                                            <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                            ${moment(sumaHorasNormales).format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colSobreTNormal" class="text-center colSobreTNormal" style="background: #fafafa;">
                                                        <a class="badge badge-soft-warning mr-2">
                                                            <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                            ${moment(sobretiempoNormales, "HH:mm:ss").format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colHoraNocturna" class="text-center colHoraNocturna" style="background: #fafafa;">
                                                        <a class="badge badge-soft-info mr-2">
                                                            <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                            ${moment(sumaHorasNocturnas).format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colSobreTNocturno" class="text-center colSobreTNocturno" style="background: #fafafa;">
                                                        <a class="badge badge-soft-info mr-2">
                                                            <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                            ${moment(sobretiempoNocturnos, "HH:mm:ss").format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colSobreTiempo" class="text-center" style="background: #fafafa;">
                                                        <a class="badge badge-soft-primary mr-2">
                                                            <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                            ${horasSobreT}:${minutosSobreT}:${segundosSobreT}
                                                        </a>
                                                    </td>
                                                    <td name="colFaltaJornada" class="text-center" style="background: #fafafa;">
                                                        <a class="badge badge-soft-danger mr-2">
                                                            <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                            ${horasFaltaJ}:${minutosFaltaJ}:${segundosFaltaJ}
                                                        </a>
                                                    </td>
                                                    <td name="colTardanza" style="background: #fafafa;">
                                                        <a class="badge badge-soft-danger mr-2">
                                                            <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                            ${horasTardanza}:${minutosTardanza}:${segundosTardanza}
                                                        </a>
                                                    </td>`;
                                    if (data[index].data[m].marcaciones.length == 0 && data[index].incidencias.length == 0) {
                                        sumaFaltas++;
                                        grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;">
                                                            <span class="badge badge-soft-danger mr-2" class="text-center">
                                                                Falta
                                                            </span>
                                                        </td>`;
                                    } else {
                                        grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;">---</td>`;
                                    }
                                    grupoHorario += `<td name="colHE25D" class="text-center colHE25D" style="background: #fafafa;">${diurnas25}</td>
                                                    <td name="colHE35D" class="text-center colHE35D" style="background: #fafafa;">${diurnas35}</td>
                                                    <td name="colHE100D" class="text-center colHE100D" style="background: #fafafa;">${diurnas100}</td>`;
                                }
                            } else {
                                grupoHorario += `<td style="border-left: 2px solid #383e56!important;background: #fafafa;" class="text-center" name="descripcionHorario">
                                                    <div class="dropdown">
                                                        <a class="btn dropdown" type="button" data-toggle="dropdown" id="dropdownHorario${horarioData.idHorario}" aria-haspopup="true" aria-expanded="false" 
                                                            style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                            <span class="badge badge-soft-danger mr-2" class="text-center">
                                                                Sin horario
                                                            </span>
                                                        </a>
                                                        <ul class="dropdown-menu scrollable-menu noExport"  aria-labelledby="dropdownHorario${horarioData.idHorario}" style="padding: 0rem 0rem;">
                                                            <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                Opciones
                                                            </h6>
                                                            <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                            <div class="dropdown-item dropdown-itemM noExport">
                                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                    <a onclick="modalCambiarHorario('${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/calendarioAD.svg" height="15" />
                                                                        Actualizar horario
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td class="text-center" name="horarioHorario" style="background: #fafafa;">
                                                    <div class="dropdown">
                                                        <a class="btn dropdown" type="button" data-toggle="dropdown" id="dropdownHorario${horarioData.idHorario}" aria-haspopup="true" aria-expanded="false" 
                                                            style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                            <span class="badge badge-soft-danger mr-2" class="text-center">
                                                                Sin horario
                                                            </span>
                                                        </a>
                                                        <ul class="dropdown-menu scrollable-menu noExport"  aria-labelledby="dropdownHorario${horarioData.idHorario}" style="padding: 0rem 0rem;">
                                                            <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                Opciones
                                                            </h6>
                                                            <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                            <div class="dropdown-item dropdown-itemM noExport">
                                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                    <a onclick="modalCambiarHorario('${fecha}',${data[index].emple_id})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/calendarioAD.svg" height="15" />
                                                                        Actualizar horario
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td class="text-center" name="toleranciaIHorario" style="background: #fafafa;">---</td>
                                                <td class="text-center" name="toleranciaFHorario" style="background: #fafafa;">---</td>
                                                <td class="text-center" name="colTiempoEntreH" style="background: #fafafa;">
                                                    <a class="badge badge-soft-primary mr-2">
                                                        <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                        ${moment(sumaTiemposEntreHorarios).format("HH:mm:ss")}
                                                    </a>
                                                </td>
                                                <td name="colHoraNormal" class="text-center colHoraNormal" style="background: #fafafa;">
                                                    <a class="badge badge-soft-warning mr-2">
                                                        <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                        ${moment(sumaHorasNormales).format("HH:mm:ss")}
                                                    </a>
                                                </td>
                                                <td name="colSobreTNormal" class="text-center colSobreTNormal" style="background: #fafafa;">
                                                        <a class="badge badge-soft-warning mr-2">
                                                            <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                            ${moment(sobretiempoNormales, "HH:mm:ss").format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                <td name="colHoraNocturna" class="text-center colHoraNocturna" style="background: #fafafa;">
                                                    <a class="badge badge-soft-info mr-2">
                                                        <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                        ${moment(sumaHorasNocturnas).format("HH:mm:ss")}
                                                    </a>
                                                </td>
                                                <td name="colSobreTNocturno" class="text-center colSobreTNocturno" style="background: #fafafa;">
                                                        <a class="badge badge-soft-info mr-2">
                                                            <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                            ${moment(sobretiempoNocturnos, "HH:mm:ss").format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                <td class="text-center" name="colSobreTiempo" style="background: #fafafa;">
                                                    <a class="badge badge-soft-primary mr-2">
                                                        <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                        ${horasSobreT}:${minutosSobreT}:${segundosSobreT}
                                                    </a>
                                                </td>
                                                <td name="colFaltaJornada" class="text-center" style="background: #fafafa;">
                                                    <a class="badge badge-soft-danger mr-2">
                                                        <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                        ${horasFaltaJ}:${minutosFaltaJ}:${segundosFaltaJ}
                                                    </a>
                                                </td>
                                                <td class="text-center" name="colTardanza" style="background: #fafafa;">
                                                    <a class="badge badge-soft-danger mr-2">
                                                        <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                        ${horasTardanza}:${minutosTardanza}:${segundosTardanza}
                                                    </a>
                                                </td>`;
                                if (data[index].data[m].marcaciones.length == 0 && data[index].incidencias.length == 0) {
                                    sumaFaltas++;
                                    grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;">
                                                        <span class="badge badge-soft-danger mr-2" class="text-center">
                                                            Falta
                                                        </span>
                                                    </td>`;
                                } else {
                                    grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;">---</td>`;
                                }
                                grupoHorario += `<td name="colHE25D" class="text-center colHE25D" style="background: #fafafa;">${diurnas25}</td>
                                                <td name="colHE35D" class="text-center colHE35D" style="background: #fafafa;">${diurnas35}</td>
                                                <td name="colHE100D" class="text-center colHE100D" style="background: #fafafa;">${diurnas100}</td>`;
                            }
                        } else {
                            if (horarioData.horario != null) {
                                if (horarioData.estado == 1) {
                                    grupoHorario += `<td style="border-left: 2px solid #383e56!important;background: #fafafa;" class="text-center" name="descripcionHorario">
                                                        <a class="btn" type="button" style="padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                            <span class="badge badge-soft-primary mr-2" class="text-center">
                                                                 ${horarioData.horario}
                                                            </span>
                                                        </a>
                                                    </td>
                                                    <td name="horarioHorario" style="background: #fafafa;">
                                                        ${moment(horarioData.horarioIni).format("HH:mm:ss")} - ${moment(horarioData.horarioFin).format("HH:mm:ss")}
                                                    </td>
                                                    <td class="text-center" name="toleranciaIHorario" style="background: #fafafa;">${horarioData.toleranciaI} min.</td>
                                                    <td class="text-center" name="toleranciaFHorario" style="background: #fafafa;">${horarioData.toleranciaF} min.</td>
                                                    <td name="colTiempoEntreH" class="text-center" style="background: #fafafa;">
                                                        <a class="badge badge-soft-primary mr-2">
                                                            <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                            ${moment(sumaTiemposEntreHorarios).format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colHoraNormal" class="text-center colHoraNormal" style="background: #fafafa;">
                                                        <a class="badge badge-soft-warning mr-2">
                                                            <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                            ${moment(sumaHorasNormales).format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colSobreTNormal" class="text-center colSobreTNormal" style="background: #fafafa;">
                                                        <a class="badge badge-soft-warning mr-2">
                                                            <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                            ${moment(sobretiempoNormales, "HH:mm:ss").format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colHoraNocturna" class="text-center colHoraNocturna" style="background: #fafafa;">
                                                        <a class="badge badge-soft-info mr-2">
                                                            <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                            ${moment(sumaHorasNocturnas).format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colSobreTNocturno" class="text-center colSobreTNocturno" style="background: #fafafa;">
                                                        <a class="badge badge-soft-info mr-2">
                                                            <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                            ${moment(sobretiempoNocturnos, "HH:mm:ss").format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colSobreTiempo" class="text-center" style="background: #fafafa;">
                                                        <a class="badge badge-soft-primary mr-2">
                                                            <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                            ${horasSobreT}:${minutosSobreT}:${segundosSobreT}
                                                        </a>
                                                    </td>
                                                    <td name="colFaltaJornada" class="text-center" style="background: #fafafa;">
                                                        <a class="badge badge-soft-danger mr-2">
                                                            <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                            ${horasFaltaJ}:${minutosFaltaJ}:${segundosFaltaJ}
                                                        </a>
                                                    </td>
                                                    <td name="colTardanza" style="background: #fafafa;">
                                                        <a class="badge badge-soft-danger mr-2">
                                                            <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                            ${horasTardanza}:${minutosTardanza}:${segundosTardanza}
                                                        </a>
                                                    </td>
                                                    `;
                                    if (data[index].data[m].marcaciones.length == 0 && data[index].incidencias.length == 0) {
                                        sumaFaltas++;
                                        grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;">
                                                            <span class="badge badge-soft-danger mr-2" class="text-center">
                                                                Falta
                                                            </span>
                                                        </td>`;
                                    } else {
                                        grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;">---</td>`;
                                    }
                                    grupoHorario += `<td name="colHE25D" class="text-center colHE25D" style="background: #fafafa;">${diurnas25}</td>
                                                    <td name="colHE35D" class="text-center colHE35D" style="background: #fafafa;">${diurnas35}</td>
                                                    <td name="colHE100D" class="text-center colHE100D" style="background: #fafafa;">${diurnas100}</td>`;
                                } else {
                                    grupoHorario += `<td style="border-left: 2px solid #383e56!important;background: #fafafa;" class="text-center" name="descripcionHorario">
                                                        <a class="btn" type="button" style="padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                            <span class="badge badge-soft-danger mr-2" class="text-center">
                                                                 ${horarioData.horario}
                                                            </span>
                                                        </a>
                                                    </td>
                                                    <td name="horarioHorario" style="background: #fafafa;">
                                                        ${moment(horarioData.horarioIni).format("HH:mm:ss")} - ${moment(horarioData.horarioFin).format("HH:mm:ss")}
                                                    </td>
                                                    <td class="text-center" name="toleranciaIHorario" style="background: #fafafa;">${horarioData.toleranciaI} min.</td>
                                                    <td class="text-center" name="toleranciaFHorario" style="background: #fafafa;">${horarioData.toleranciaF} min.</td>
                                                    <td name="colTiempoEntreH" class="text-center" style="background: #fafafa;">
                                                        <a class="badge badge-soft-primary mr-2">
                                                            <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                            ${moment(sumaTiemposEntreHorarios).format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colHoraNormal" class="text-center colHoraNormal" style="background: #fafafa;">
                                                        <a class="badge badge-soft-warning mr-2">
                                                            <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                            ${moment(sumaHorasNormales).format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colSobreTNormal" class="text-center colSobreTNormal" style="background: #fafafa;">
                                                        <a class="badge badge-soft-warning mr-2">
                                                            <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                            ${moment(sobretiempoNormales, "HH:mm:ss").format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colHoraNocturna" class="text-center colHoraNocturna" style="background: #fafafa;">
                                                        <a class="badge badge-soft-info mr-2">
                                                            <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                            ${moment(sumaHorasNocturnas).format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colSobreTNocturno" class="text-center colSobreTNocturno" style="background: #fafafa;">
                                                        <a class="badge badge-soft-info mr-2">
                                                            <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                            ${moment(sobretiempoNocturnos, "HH:mm:ss").format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                    <td name="colSobreTiempo" class="text-center" style="background: #fafafa;">
                                                        <a class="badge badge-soft-primary mr-2">
                                                            <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                            ${horasSobreT}:${minutosSobreT}:${segundosSobreT}
                                                        </a>
                                                    </td>
                                                    <td name="colFaltaJornada" class="text-center" style="background: #fafafa;">
                                                        <a class="badge badge-soft-danger mr-2">
                                                            <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                            ${horasFaltaJ}:${minutosFaltaJ}:${segundosFaltaJ}
                                                        </a>
                                                    </td>
                                                    <td name="colTardanza" style="background: #fafafa;">
                                                        <a class="badge badge-soft-danger mr-2">
                                                            <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                            ${horasTardanza}:${minutosTardanza}:${segundosTardanza}
                                                        </a>
                                                    </td>`;
                                    if (data[index].data[m].marcaciones.length == 0 && data[index].incidencias.length == 0) {
                                        sumaFaltas++;
                                        grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;">
                                                            <span class="badge badge-soft-danger mr-2" class="text-center">
                                                                Falta
                                                            </span>
                                                        </td>`;
                                    } else {
                                        grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;">---</td>`;
                                    }
                                    grupoHorario += `<td name="colHE25D" class="text-center colHE25D" style="background: #fafafa;">${diurnas25}</td>
                                                    <td name="colHE35D" class="text-center colHE35D" style="background: #fafafa;">${diurnas35}</td>
                                                    <td name="colHE100D" class="text-center colHE100D" style="background: #fafafa;">${diurnas100}</td>`;
                                }
                            } else {
                                grupoHorario += `<td style="border-left: 2px solid #383e56!important;background: #fafafa;" class="text-center" name="descripcionHorario">
                                                    <a class="btn" type="button" style="padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                        <span class="badge badge-soft-danger mr-2" class="text-center">
                                                             Sin horario
                                                        </span>
                                                    </a>
                                                </td>
                                                <td class="text-center" name="horarioHorario" style="background: #fafafa;">
                                                    <a class="btn" type="button" style="padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                        <span class="badge badge-soft-danger mr-2" class="text-center">
                                                             Sin horario
                                                        </span>
                                                    </a>
                                                </td>
                                                <td class="text-center" name="toleranciaIHorario" style="background: #fafafa;">---</td>
                                                <td class="text-center" name="toleranciaFHorario" style="background: #fafafa;">---</td>
                                                <td name="colTiempoEntreH" class="text-center" style="background: #fafafa;">
                                                    <a class="badge badge-soft-primary mr-2">
                                                        <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                        ${moment(sumaTiemposEntreHorarios).format("HH:mm:ss")}
                                                    </a>
                                                </td>
                                                <td name="colHoraNormal" class="text-center colHoraNormal" style="background: #fafafa;">
                                                    <a class="badge badge-soft-warning mr-2">
                                                        <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                        ${moment(sumaHorasNormales).format("HH:mm:ss")}
                                                    </a>
                                                </td>
                                                <td name="colSobreTNormal" class="text-center colSobreTNormal" style="background: #fafafa;">
                                                        <a class="badge badge-soft-warning mr-2">
                                                            <img src="landing/images/sun.svg" height="12" class="mr-2">
                                                            ${moment(sobretiempoNormales, "HH:mm:ss").format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                <td name="colHoraNocturna" class="text-center colHoraNocturna" style="background: #fafafa;">
                                                    <a class="badge badge-soft-info mr-2">
                                                        <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                        ${moment(sumaHorasNocturnas).format("HH:mm:ss")}
                                                    </a>
                                                </td>
                                                <td name="colSobreTNocturno" class="text-center colSobreTNocturno" style="background: #fafafa;">
                                                        <a class="badge badge-soft-info mr-2">
                                                            <img src="landing/images/moon.svg" height="12" class="mr-2">
                                                            ${moment(sobretiempoNocturnos, "HH:mm:ss").format("HH:mm:ss")}
                                                        </a>
                                                    </td>
                                                <td name="colSobreTiempo" class="text-center" style="background: #fafafa;">
                                                    <a class="badge badge-soft-primary mr-2">
                                                        <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                        ${horasSobreT}:${minutosSobreT}:${segundosSobreT}
                                                    </a>
                                                </td>
                                                <td name="colFaltaJornada" class="text-center" style="background: #fafafa;">
                                                    <a class="badge badge-soft-danger mr-2">
                                                        <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                        ${horasFaltaJ}:${minutosFaltaJ}:${segundosFaltaJ}
                                                    </a>
                                                </td>
                                                <td name="colTardanza" class="text-center" style="background: #fafafa;">
                                                    <a class="badge badge-soft-danger mr-2">
                                                        <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                                        ${horasTardanza}:${minutosTardanza}:${segundosTardanza}
                                                    </a>
                                                </td>`;
                                if (data[index].data[m].marcaciones.length == 0 && data[index].incidencias.length == 0) {
                                    sumaFaltas++;
                                    grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;">
                                                        <span class="badge badge-soft-danger mr-2" class="text-center">
                                                            Falta
                                                        </span>
                                                    </td>`;
                                } else {
                                    grupoHorario += `<td class="text-center" name="faltaHorario" style="background: #fafafa;">---</td>`;
                                }
                                grupoHorario += `<td name="colHE25D" class="text-center colHE25D" style="background: #fafafa;">${diurnas25}</td>
                                                <td name="colHE35D" class="text-center colHE35D" style="background: #fafafa;">${diurnas35}</td>
                                                <td name="colHE100D" class="text-center colHE100D" style="background: #fafafa;">${diurnas100}</td>`;
                            }
                        }
                        // ! MARCACIONES
                        var tbodyEntradaySalida = "";
                        for (let j = 0; j < data[index].data[m].marcaciones.length; j++) {
                            // * TIEMPO EN SITIO
                            var segundosTiempo = "00";
                            var minutosTiempo = "00";
                            var horasTiempo = "00";
                            var marcacionData = data[index].data[m].marcaciones[j];
                            if (marcacionData.entrada != 0) {
                                if (permisoModificar == 1) {
                                    tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important" name="colMarcaciones">
                                                                <div class="dropdown" data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo:${marcacionData.dispositivoEntrada}" data-html="true">
                                                                    <a class="btn dropdown-toggle" type="button" id="dropdownEntrada${marcacionData.idMarcacion}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                                        style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>
                                                                        ${moment(marcacionData.entrada).format("HH:mm:ss")}
                                                                    </a>
                                                                    <ul class="dropdown-menu scrollable-menu noExport"  aria-labelledby="dropdownEntrada${marcacionData.idMarcacion}" style="padding: 0rem 0rem;">
                                                                        <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                            <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                            Opciones
                                                                        </h6>
                                                                        <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                        <div class="dropdown-item dropdown-itemM noExport">
                                                                            <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                                <a onclick="listaSalida(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.entrada).format("HH:mm:ss")}',1,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                    <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" height="12" />
                                                                                    Cambiar a entrada
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <div class="dropdown-item dropdown-itemM noExport">
                                                                            <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                                <a onclick="listaEntrada(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.entrada).format("HH:mm:ss")}',1,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg"  height="12" />
                                                                                    Cambiar a salida
                                                                                </a>
                                                                            </div>
                                                                        </div>`;
                                    if (marcacionData.salida != 0) {
                                        tbodyEntradaySalida += `<div class=" dropdown-item dropdown-itemM noExport">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a onclick="convertirOrden(${marcacionData.idMarcacion},${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/flechasD.svg"  height="12" />
                                                                            Convertir orden
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="dropdown-item dropdown-itemM noExport">
                                                                    <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                        <a onclick="asignarNuevaM(${marcacionData.idMarcacion},'${moment(marcacionData.entrada).format("HH:mm:ss")}',1,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                                            Asignar a nueva marc.
                                                                        </a>
                                                                    </div>
                                                                </div>`;
                                    }
                                    tbodyEntradaySalida += ` <div class="dropdown-item dropdown-itemM noExport">
                                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                    <a onclick="modalActualizarHorarioMarc(${marcacionData.idHE},${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${marcacionData.entrada}','${marcacionData.salida}','${horarioData.horario}','${horarioData.horarioIni}','${horarioData.horarioFin}',${horarioData.estado})" 
                                                                        style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/calendarioAD.svg" height="15" />
                                                                        Actualizar horario
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="dropdown-item dropdown-itemM noExport">
                                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                    <a onclick="eliminarM(${marcacionData.idMarcacion},1,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/borrarD.svg"  height="12" />
                                                                        Eliminar marc.
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </ul></div></td>`;
                                }
                                else {
                                    tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;" name="colMarcaciones" data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo:${marcacionData.dispositivoEntrada}">
                                                                        <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>
                                                                        ${moment(marcacionData.entrada).format("HH:mm:ss")}
                                                                    </td>`;
                                }
                                if (marcacionData.salida != 0) {
                                    if (permisoModificar == 1) {
                                        tbodyEntradaySalida += `<td name="colMarcaciones">
                                                                    <div class="dropdown" data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo:${marcacionData.dispositivoSalida}">
                                                                        <a class="btn dropdown" type="button" data-toggle="dropdown" id="dropdownSalida${marcacionData.idMarcacion}" aria-haspopup="true" aria-expanded="false" 
                                                                            style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                            <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>
                                                                            ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                        </a>
                                                                        <ul class="dropdown-menu scrollable-menu noExport"  aria-labelledby="dropdownSalida${marcacionData.idMarcacion}" style="padding: 0rem 0rem;">
                                                                            <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                                <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                                Opciones
                                                                            </h6>
                                                                            <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                            <div class="dropdown-item dropdown-itemM noExport">
                                                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                                    <a onclick="listaSalida(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.salida).format("HH:mm:ss")}',2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                        <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" height="12" />
                                                                                        Cambiar a entrada
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="dropdown-item dropdown-itemM noExport">
                                                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                                    <a onclick="listaEntrada(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.salida).format("HH:mm:ss")}',2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                    <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg"  height="12" />
                                                                                        Cambiar a salida
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="dropdown-item dropdown-itemM noExport">
                                                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                                    <a onclick="convertirOrden(${marcacionData.idMarcacion},${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                        <img style="margin-bottom: 3px;" src="landing/images/flechasD.svg"  height="12" />
                                                                                        Convertir orden
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="dropdown-item dropdown-itemM noExport">
                                                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                                    <a onclick="asignarNuevaM(${marcacionData.idMarcacion},'${moment(marcacionData.salida).format("HH:mm:ss")}',2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                        <img style="margin-bottom: 3px;" src="landing/images/plusD.svg"  height="12" />
                                                                                        Asignar a nueva marc.
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="dropdown-item dropdown-itemM noExport">
                                                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                                    <a onclick="modalActualizarHorarioMarc(${marcacionData.idHE},${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${marcacionData.entrada}','${marcacionData.salida}','${horarioData.horario}','${horarioData.horarioIni}','${horarioData.horarioFin}',${horarioData.estado})" 
                                                                                        style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                        <img style="margin-bottom: 3px;" src="landing/images/calendarioAD.svg" height="15" />
                                                                                        Actualizar horario
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="dropdown-item dropdown-itemM noExport">
                                                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                                    <a onclick="eliminarM(${marcacionData.idMarcacion},2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                        <img style="margin-bottom: 3px;" src="landing/images/borrarD.svg"  height="12" />
                                                                                        Eliminar marc.
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </ul>
                                                                    </div>
                                                                </td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td name="colMarcaciones" data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo:${marcacionData.dispositivoSalida}">
                                                                    <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> 
                                                                    ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                </td>`;

                                    }
                                    // * CALCULAR TIEMPO TOTAL
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
                                    }
                                    // * FINALIZACION
                                    tbodyEntradaySalida += `<td name="colTiempoS">
                                                                <input type="hidden" value= "${horasTiempo}:${minutosTiempo}:${segundosTiempo}" name="tiempoSit${data[index].emple_id}[]" id="tiempoSit${data[index].emple_id}">
                                                                <a class="badge badge-soft-primary mr-2">
                                                                    <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                                    ${horasTiempo}:${minutosTiempo}:${segundosTiempo}
                                                                </a>
                                                            </td>`;
                                } else {
                                    if (permisoModificar == 1) {
                                        tbodyEntradaySalida += `<td name="colMarcaciones">
                                                                    <div class="dropdown noExport" id="dropSalida${marcacionData.idMarcacion}">
                                                                        <a type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" 
                                                                            aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                                            <span class="badge badge-soft-secondary" data-toggle="tooltip" data-placement="left" title="Agregar hora" data-html="true">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                                No tiene salida
                                                                            </span>
                                                                        </a>
                                                                        <ul class="dropdown-menu"  style="padding: 0rem 0rem;">
                                                                            <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                                <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                                Opciones
                                                                            </h6>
                                                                            <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                            <div class="dropdown-item dropdown-itemM noExport">
                                                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
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
                                        tbodyEntradaySalida += `<td name="colMarcaciones">
                                                                    <span class="badge badge-soft-secondary noExport"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                                        No tiene salida
                                                                    </span>
                                                                </td>`;
                                    }

                                    tbodyEntradaySalida += `<td name="colTiempoS">
                                                                <input type="hidden" value= "${horasTiempo}:${minutosTiempo}:${segundosTiempo}" name="tiempoSit${data[index].emple_id}[]" id="tiempoSit${data[index].emple_id}">
                                                                <a class="badge badge-soft-primary mr-2">
                                                                    <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                                    ${horasTiempo}:${minutosTiempo}:${segundosTiempo}
                                                                </a>
                                                            </td>`;
                                }

                            } else {
                                if (marcacionData.salida != 0) {
                                    //* COLUMNA DE ENTRADA
                                    if (permisoModificar == 1) {
                                        tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;" name="colMarcaciones">
                                                                    <div class=" dropdown">
                                                                        <a class="btn dropdown-toggle" type="button" id="dropEntrada${marcacionData.idMarcacion}" data-toggle="dropdown" aria-haspopup="true" 
                                                                            aria-expanded="false" style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">
                                                                            <span class="badge badge-soft-warning" data-toggle="tooltip" data-placement="left" title="Agregar hora" data-html="true">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                                                No tiene entrada
                                                                            </span>
                                                                        </a>
                                                                        <ul class="dropdown-menu noExport" aria-labelledby="dropEntrada${marcacionData.idMarcacion}" style="padding: 0rem 0rem;">
                                                                            <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                                <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                                Opciones
                                                                            </h6>
                                                                            <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                            <div class="dropdown-item dropdown-itemM noExport">
                                                                                <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
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
                                        tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;" name="colMarcaciones">
                                                                            <span class="badge badge-soft-warning noExport">
                                                                                <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                                                No tiene entrada
                                                                            </span>
                                                                        </td>`;
                                    }

                                    //* COLUMNA DE SALIDA
                                    var permisoModificarCE2 = $('#modifReporte').val();
                                    if (permisoModificarCE2 == 1) {
                                        tbodyEntradaySalida += `<td name="colMarcaciones">
                                                                            <div class="dropdown" data-toggle="tooltip" data-placement="left" data-html="true" title="Dispositivo:${marcacionData.dispositivoSalida}">
                                                                                <a class="btn dropdown" type="button" data-toggle="dropdown" id="dropdownSalida${marcacionData.idMarcacion}" aria-haspopup="true" aria-expanded="false"
                                                                                    style="cursor: pointer;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;color:#6c757d!important">
                                                                                    <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/>
                                                                                    ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                                </a>
                                                                                <ul class="dropdown-menu scrollable-menu noExport"  aria-labelledby="dropdownSalida${marcacionData.idMarcacion}" style="padding: 0rem 0rem;">
                                                                                    <h6 class="dropdown-header text-left" style="padding: 0.5rem 0.5rem;margin-top: 0;background: #edf0f1;color: #6c757d;font-weight: bold">
                                                                                        <img src="landing/images/configuracionesD.svg" class="mr-1" height="12"/>    
                                                                                        Opciones
                                                                                    </h6>
                                                                                    <div class="dropdown-divider" style="margin: 0rem 0rem;"></div>
                                                                                    <div class="dropdown-item dropdown-itemM noExport">
                                                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                                            <a onclick="listaSalida(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.salida).format("HH:mm:ss")}',2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                                <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" height="12" />
                                                                                                Cambiar a entrada
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="dropdown-item dropdown-itemM noExport">
                                                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                                            <a onclick="listaEntrada(${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${moment(marcacionData.salida).format("HH:mm:ss")}',2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                            <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg"  height="12" />
                                                                                                Cambiar a salida
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="dropdown-item dropdown-itemM noExport">
                                                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                                            <a onclick="modalActualizarHorarioMarc(${marcacionData.idHE},${marcacionData.idMarcacion},'${fecha}',${data[index].emple_id},'${marcacionData.entrada}','${marcacionData.salida}','${horarioData.horario}','${horarioData.horarioIni}','${horarioData.horarioFin}',${horarioData.estado})" 
                                                                                                style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                                <img style="margin-bottom: 3px;" src="landing/images/calendarioAD.svg" height="15" />
                                                                                                Actualizar horario
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="dropdown-item dropdown-itemM noExport">
                                                                                        <div class="form-group noExport pl-3" style="margin-bottom: 0.5rem;">
                                                                                            <a onclick="eliminarM(${marcacionData.idMarcacion},2,${marcacionData.idHE})" style="cursor:pointer; font-size:12px;padding-top: 2px;">
                                                                                                <img style="margin-bottom: 3px;" src="landing/images/borrarD.svg"  height="12" />
                                                                                                Eliminar marc.
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </ul>
                                                                            </div>
                                                                        </td>`;
                                    } else {
                                        tbodyEntradaySalida += `<td name="colMarcaciones">
                                                                    <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> 
                                                                    ${moment(marcacionData.salida).format("HH:mm:ss")}
                                                                </td>`;
                                    }

                                    tbodyEntradaySalida += `<td name="colTiempoS">
                                                                <input type="hidden" value= "${horasTiempo}:${minutosTiempo}:${segundosTiempo}" name="tiempoSit${data[index].emple_id}[]" id="tiempoSit${data[index].emple_id}">
                                                                <a class="badge badge-soft-primary mr-2">
                                                                    <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                                    ${horasTiempo}:${minutosTiempo}:${segundosTiempo}
                                                                </a>
                                                            </td>`;

                                }
                            }
                        }
                        for (let mr = data[index].data[m].marcaciones.length; mr < arrayHorario[m].split(",")[0]; mr++) {
                            tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;" class="text-center" name="colMarcaciones">---</td>
                                                    <td class="text-center" name="colMarcaciones">---</td>
                                                    <td name="colTiempoS" class="text-center">---</td>`;
                        }
                        grupoHorario += tbodyEntradaySalida;
                        // ! PAUSAS
                        var tbodyPausas = "";
                        for (let p = 0; p < data[index].data[m].pausas.length; p++) {
                            // * PAUSAS
                            tiempoHoraPausa = "00";        //: HORAS TARDANZA
                            tiempoMinutoPausa = "00";      //: MINUTOS TARDANZA
                            tiempoSegundoPausa = "00";     //: SEGUNDOS TARDANZA
                            estadoTiempoHorario = true;
                            //* EXCESO
                            tiempoHoraExceso = "00";
                            tiempoMinutoExceso = "00";
                            tiempoSegundoExceso = "00";
                            var pausaData = data[index].data[m].pausas[p];
                            for (let mp = 0; mp < data[index].data[m].marcaciones.length; mp++) {
                                var dataMarcacionP = data[index].data[m].marcaciones[mp];
                                if (dataMarcacionP.idH != 0 && pausaData.horario_id == dataMarcacionP.idH) {
                                    if (!idPausas.includes(pausaData.id)) {
                                        var horaInicialM = moment(dataMarcacionP.entrada);
                                        var horaFinalM = moment(dataMarcacionP.salida);
                                        // ****************************************** PAUSAS ****************************************
                                        var fechaI = horaInicialM.clone().format("YYYY-MM-DD");
                                        var fechaF;
                                        if (pausaData.inicio > pausaData.fin) {
                                            fechaF = horaInicialM.clone().add(1, 'day').format("YYYY-MM-DD");
                                        } else {
                                            fechaF = horaInicialM.clone().format("YYYY-MM-DD");
                                        }
                                        var pausaI = moment(fechaI + " " + pausaData.inicio);
                                        var pausaF = moment(fechaF + " " + pausaData.fin);
                                        // ! INICIO DE PAUSA
                                        var sumaToleranciaPausa = moment(
                                            pausaI.clone().add(
                                                { "minutes": pausaData.tolerancia_inicio }    // : CLONAMOS EL TIEMPO Y SUMAR CON TOLERANCIA
                                            ).toString());
                                        var restaToleranciaPausa = moment(
                                            pausaI.clone().subtract(
                                                { "minutes": pausaData.tolerancia_inicio }
                                            ).toString()); //: CLONAMOS EL TIEMPO Y RESTAR CON TOLERANCIA
                                        // ! FIN DE PAUSA
                                        var sumaToleranciaPausaFinal = moment(pausaF.clone().add({ "minutes": pausaData.tolerancia_fin }).toString());
                                        // ! CONDICIONALES QUE SI HORA FINAL DE LA MARCACION ESTA ENTRE LA RESTA CON LA TOLERANCIA Y LA SUMA CON LA TOLERANCIA
                                        if (horaFinalM.isSameOrAfter(restaToleranciaPausa) && horaFinalM.isSameOrBefore(sumaToleranciaPausa)) {
                                            // * VERIFICAR SI YA TENEMOS OTRA MARCACION SIGUIENTE
                                            if (data[index].data[m].marcaciones[mp + 1] != undefined) {
                                                if (data[index].data[m].marcaciones[mp + 1].entrada != undefined) {
                                                    if (data[index].data[m].marcaciones[mp + 1].entrada != 0) {
                                                        var horaEntradaDespues = moment(data[index].data[m].marcaciones[mp + 1].entrada);    //: -> OBTENER ENTRADA DE LA MARCACION SIGUIENTE
                                                        var restarTiempoMarcacion = horaEntradaDespues - horaFinalM;                //: -> RESTAR PARA OBTENER LA CANTIDAD EN PAUSA               
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
                                                        }
                                                    }
                                                }
                                            }
                                            idPausas.push(pausaData.id);
                                        } else {
                                            if (horaFinalM.isSameOrAfter(restaToleranciaPausa) && horaFinalM.isSameOrBefore(sumaToleranciaPausaFinal)) {
                                                // * VERIFICAR SI YA TENEMOS OTRA MARCACION SIGUIENTE
                                                if (data[index].data[m].marcaciones[mp + 1] != undefined) {
                                                    if (data[index].data[m].marcaciones[mp + 1].entrada != undefined) {
                                                        if (data[index].data[m].marcaciones[mp + 1].entrada != 0) {
                                                            estadoTiempoHorario = false;
                                                            var horaEntradaDespues = moment(data[index].data[m].marcaciones[mp + 1].entrada);    //: -> OBTENER ENTRADA DE LA MARCACION SIGUIENTE
                                                            var restarTiempoMarcacion = horaEntradaDespues - horaFinalM;                //: -> RESTAR PARA OBTENER LA CANTIDAD EN PAUSA               
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
                                                            }
                                                        }
                                                    }
                                                }
                                                idPausas.push(pausaData.id);
                                            }
                                        }
                                    }
                                }
                            }
                            tbodyPausas += `<td style="border-left: 1px dashed #aaaaaa!important;" name="descripcionPausa">${pausaData.descripcion}</td>
                                    <td name="horarioPausa">${pausaData.inicio} - ${pausaData.fin}</td>`;
                            if (estadoTiempoHorario) {
                                tbodyPausas += `<td name="tiempoPausa" class="text-center">
                                                    <a class="badge badge-soft-primary mr-2">
                                                        <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                                        ${tiempoHoraPausa}:${tiempoMinutoPausa}:${tiempoSegundoPausa}
                                                    </a>
                                                </td>`;
                            } else {
                                tbodyPausas += `<td name="tiempoPausa" class="text-center">
                                                    <a class="badge badge-soft-warning mr-2" rel="tooltip" data-toggle="tooltip" data-placement="left" 
                                                        title="El colaborador marcó tarde su ${pausaData.descripcion}.\nSalida:${pausaData.inicio}\n
                                                        Tolerancia ${pausaData.tolerancia_inicio} min.\nRegreso:${pausaData.fin}\nTolerancia ${pausaData.tolerancia_fin} min." 
                                                        data-html="true">
                                                        <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                        ${tiempoHoraPausa}:${tiempoMinutoPausa}:${tiempoSegundoPausa}
                                                    </a>
                                                </td>`;
                            }
                            tbodyPausas += `<td name="excesoPausa" class="text-center">
                                        <a class="badge badge-soft-danger mr-2">
                                            <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                            ${tiempoHoraExceso}:${tiempoMinutoExceso}:${tiempoSegundoExceso}
                                        </a>
                                    </td>`;
                        }
                        for (let cp = data[index].data[m].pausas.length; cp < arrayHorario[m].split(",")[1]; cp++) {
                            tbodyPausas += `<td style="border-left: 1px dashed #aaaaaa!important" name="descripcionPausa" class="text-center">----</td>
                                            <td name="horarioPausa" class="text-center">-----</td>
                                            <td name="tiempoPausa" class="text-center">-----</td>
                                            <td name="excesoPausa" class="text-center">------</td>`;
                        }
                        grupoHorario += tbodyPausas;
                    } else {
                        grupoHorario += `<td style="border-left: 2px solid #383e56!important;background:#fafafa;" class="text-center" name="descripcionHorario">
                                            ----
                                        </td>
                                        <td class="text-center" name="horarioHorario" style="background: #fafafa;">---</td>
                                        <td class="text-center" name="toleranciaIHorario" style="background: #fafafa;">---</td>
                                        <td class="text-center" name="toleranciaFHorario" style="background: #fafafa;">---</td>
                                        <td name="colTiempoEntreH" class="text-center" style="background: #fafafa;">---</td>
                                        <td name="colHoraNormal" class="text-center colHoraNormal" style="background: #fafafa;">---</td>
                                        <td name="colSobreTNormal" class="text-center colSobreTNormal" style="background: #fafafa;">---</td>
                                        <td name="colHoraNocturna" class="text-center colHoraNocturna" style="background: #fafafa;">---</td>
                                        <td name="colSobreTNocturno" class="text-center colSobreTNocturno" style="background: #fafafa;">---</td>
                                        <td name="colSobreTiempo" class="text-center" style="background: #fafafa;">---</td>
                                        <td name="colFaltaJornada" class="text-center" style="background: #fafafa;">---</td>
                                        <td name="colTardanza" class="text-center" style="background: #fafafa;">---</td>
                                        <td name="faltaHorario" class="text-center" style="background: #fafafa;">---</td>
                                        <td name="colHE25D" class="text-center" style="background: #fafafa;">---</td>
                                        <td name="colHE35D" class="text-center colHE35D" style="background: #fafafa;">---</td>
                                        <td name="colHE100D" class="text-center colHE100D" style="background: #fafafa;">---</td>`;
                        // ! MARCACIONES
                        var tbodyEntradaySalida = "";
                        for (let mr = 0; mr < arrayHorario[m].split(",")[0]; mr++) {
                            tbodyEntradaySalida += `<td style="border-left: 1px dashed #aaaaaa!important;" class="text-center" name="colMarcaciones">---</td>
                                                    <td class="text-center" name="colMarcaciones">---</td>
                                                    <td name="colTiempoS" class="text-center">---</td>`;
                        }
                        grupoHorario += tbodyEntradaySalida;
                        // ! PAUSAS
                        var tbodyPausas = "";
                        for (let cp = 0; cp < arrayHorario[m].split(",")[1]; cp++) {
                            tbodyPausas += `<td style="border-left: 1px dashed #aaaaaa!important" name="descripcionPausa" class="text-center">----</td>
                                            <td name="horarioPausa" class="text-center">-----</td>
                                            <td name="tiempoPausa" class="text-center">-----</td>
                                            <td name="excesoPausa" class="text-center">------</td>`;
                        }
                        grupoHorario += tbodyPausas;
                    }
                }
                tbody += grupoHorario;
                // * COLUMNAS DE TIEMPO TOTAL TARDANAZA ETC
                tbody += `<td name="colTiempoTotal" style="border-left: 2px solid #383e56!important;">
                            <a class="badge badge-soft-primary mr-2">
                                <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                ${sumaTiempos.format("HH:mm:ss")}
                            </a>
                        </td>
                        <td name="colHoraNormalTotal" class="text-center colHoraNormalTotal" style="border-left: 1px dashed #aaaaaa!important">
                            <a class="badge badge-soft-warning mr-2">
                                <img src="landing/images/sun.svg" height="12" class="mr-2">
                                ${sumaHorasNormalesT.format("HH:mm:ss")}
                            </a>
                        </td>
                        <td name="colHoraNocturnaTotal" class="text-center colHoraNocturnaTotal" style="border-left: 1px dashed #aaaaaa!important">
                            <a class="badge badge-soft-info mr-2">
                                <img src="landing/images/moon.svg" height="12" class="mr-2">
                                ${sumaHorasNocturnasT.format("HH:mm:ss")}
                            </a>
                        </td>
                        <td name="colSobreTiempoTotal" class="text-center" style="border-left: 1px dashed #aaaaaa!important">
                            <a class="badge badge-soft-primary mr-2">
                                <img src="landing/images/wall-clock (1).svg" height="12" class="mr-2">
                                ${sumaSobreTiempo.format("HH:mm:ss")}
                            </a>
                        </td>
                        <td name="colFaltaJornadaTotal" class="text-center" style="border-left: 1px dashed #aaaaaa!important">
                            <a class="badge badge-soft-danger mr-2">
                                <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                ${sumaFaltaJornada.format("HH:mm:ss")}
                            </a>
                        </td>
                        <td name="colTardanzaTotal" style="border-left: 1px dashed #aaaaaa!important">
                            <a class="badge badge-soft-danger mr-2">
                                <img src="landing/images/tiempo-restante.svg" height="12" class="mr-2">
                                ${sumaTardanzas.format("HH:mm:ss")}
                            </a>
                        </td>`;
                // ******************************* CANTIDAD DE FALTAS **************************
                if (sumaFaltas != 0) {
                    tbody += `<td class="text-center" name="faltaTotal" style="border-left: 1px dashed #aaaaaa!important">`;
                    for (let f = 0; f < sumaFaltas; f++) {
                        if (f == 0) {
                            tbody += `<span class="badge badge-soft-danger mr-1" class="text-center">
                                        Falta
                                    </span>`;
                        } else {
                            tbody += `<b>/</b><span class="badge badge-soft-danger ml-1" class="text-center">
                                        Falta
                                    </span>`;
                        }
                    }
                    tbody += `</td>`;
                } else {
                    tbody += `<td class="text-center" name="faltaTotal" style="border-left: 1px dashed #aaaaaa!important">--</td>`;
                }
                // * ********************** FINALIZACION *************************************
                if (data[index].incidencias.length == 0) {
                    tbody += `<td class="text-center" style="border-left: 1px dashed #aaaaaa!important" name="incidencia">--</td>`;
                } else {
                    tbody += `<td class="text-center" style="border-left: 1px dashed #aaaaaa!important;" name="incidencia">`;
                    for (let item = 0; item < data[index].incidencias.length; item++) {
                        var dataIncidencia = data[index].incidencias[item];
                        if (item == 0) {
                            tbody += `<span class="badge badge-soft-info ml-1" class="text-center">${dataIncidencia.descripcion}</span>`;
                        } else {
                            tbody += `<b>/</b><span class="badge badge-soft-info ml-1" class="text-center">${dataIncidencia.descripcion}</span>`;
                        }
                    }
                    tbody += `</td>`;
                }
                tbody += `</tr>`;
            }
            $('#tbodyD').html(tbody);
            $('[data-toggle="tooltip"]').tooltip();
            $('.dropdown-toggle').dropdown();
            // * PARA PODER MENUS CUANDO SOLO HAY UNA COLUMNA
            if (data.length == 1) {
                var tbodyTR = '';
                tbodyTR += '<tr>';
                if (permisoModificar == 1) {
                    tbodyTR += `<td></td>`;
                }
                tbodyTR += `<td><br><br><br><br><br><br><br><br><br><br></td>
                            <td></td>
                            <td></td>
                            <td name="colCodigo"></td>
                            <td></td>
                            <td name="colCargo"></td>`;
                for (let m = 0; m < cantidadGruposHorario; m++) {
                    tbodyTR += `<td name="descripcionHorario"></td>
                                <td name="horarioHorario"></td>
                                <td name="toleranciaIHorario"></td>
                                <td name="toleranciaFHorario"></td>
                                <td name="colTiempoEntreH"></td>
                                <td name="colHoraNormal"></td>
                                <td name="colSobreTNormal"></td>
                                <td name="colHoraNocturna"></td>
                                <td name="colSobreTNocturno"></td>
                                <td name="colSobreTiempo"></td>
                                <td name="colFaltaJornada"></td>
                                <td name="colTardanza"></td>
                                <td name="faltaHorario"></td>
                                <td name="colHE25D"></td>
                                <td name="colHE35D"></td>
                                <td name="colHE100D"></td>`;
                    // ! MARCACIONES
                    for (let mr = 0; mr < arrayHorario[m].split(",")[0]; mr++) {
                        tbodyTR += '<td name="colMarcaciones"><br></td><td name="colMarcaciones"></td><td name="colTiempoS"></td>';
                    }
                    // ! PAUSAS
                    for (let cp = 0; cp < arrayHorario[m].split(",")[1]; cp++) {
                        tbodyTR += `<td name="descripcionPausa"></td>
                                <td name="horarioPausa"></td>
                                <td name="tiempoPausa"></td>
                                <td name="excesoPausa"></td>`;
                    }
                }
                tbodyTR += `<td name="colTiempoTotal"><br><br></td>
                            <td name="colHoraNormalTotal"></td>
                            <td name="colHoraNocturnaTotal"></td>
                            <td name="colSobreTiempoTotal"></td>
                            <td name="colFaltaJornadaTotal"></td>
                            <td name="colTardanzaTotal"></td>
                            <td name="faltaTotal"></td>
                            <td name="incidencia"></td>
                            </tr>`;
                $('#tbodyD').append(tbodyTR);
            }
            inicializarTabla();
            $(window).on('resize', function () {
                $("#tablaReport").css('width', '100%');
                table.draw(true);
            });
            // * SWITCH DE MOSTRAR DETALLES
            toggleColumnas();
        } else {
            $('#customSwitDetalles').prop("disabled", true);
            $('#switPausas').prop("disabled", true);
            $('#tbodyD').empty();
            $('#tbodyD').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        $.notify({
            message: '\nSurgio un error.',
            icon: 'landing/images/bell.svg',
        }, {
            icon_type: 'image',
            allow_dismiss: true,
            newest_on_top: true,
            delay: 6000,
            template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #f2dede;" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<img data-notify="icon" class="img-circle pull-left" height="15">' +
                '<span data-notify="title">{1}</span> ' +
                '<span style="color:#a94442;" data-notify="message">{2}</span>' +
                '</div>',
            spacing: 35
        });
    });
}
function cambiarF() {
    f1 = $("#fechaInput").val();
    f2 = moment(f1).format("YYYY-MM-DD");
    cargartabla(f2);
    setTimeout(function () { $("#tablaReport").DataTable().draw(); }, 200);
}
// TODO *********************************** FUNCIONALIDAD PARA MARCACIONES *******************************
// ! *********************************** NUEVA MARCACION ********************************************
// * FUNCION DE AGREGAR MARCACION
function modalAgregarMarcacion(idEmpleado, fecha) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idEmpleado == idEmpleado) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
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
            if (data.respuesta == undefined) {
                if ($('#dropSalida' + data) != undefined) {
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
                } else {
                    if ($('#dropEntrada' + data) != undefined) {
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
                    }
                }
            } else {
                $('#modalAgregar').modal();
                $('#a_fecha').val(fecha);
                $('#a_idE').val(idEmpleado);
            }
        },
        error: function () { }
    });
    listaHorariosD(idEmpleado, fecha);
    $('a').css('pointer-events', 'auto');
    sent = false;
}
var AM_datosHorario = {};
//* LISTA DE HORARIOS DISPONIBLES
function listaHorariosD(idEmpleado, fecha) {
    $('#r_horarioXE').empty();
    $.ajax({
        type: "POST",
        url: "/horariosxEmpleado",
        data: {
            idHE: 0,
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
            AM_datosHorario = data;
            var container = `<option value="" disabled selected>Seleccionar horario</option>
                                <option value="0">Sin horario</option>`;
            for (let index = 0; index < data.length; index++) {
                container += `<option value="${data[index].idHorarioE}">${data[index].descripcion} (${data[index].horaI} - ${data[index].horaF})</option>`;
            }
            $('#r_horarioXE').append(container);
        },
        error: function () { }
    });
}
// * MOSTRAR DETALLES DE HORARIO
$('#r_horarioXE').on("change", function () {
    $('#AM_detalleHorarios').empty();
    $('#AM_detalleHorarios').hide();
    if ($(this).val() != 0) {
        var contenido = `<div class="col-md-12"><span style="color:#183b5d;font-weight: bold">Detalles de Horario</span></div>`;
        AM_datosHorario.forEach(element => {
            if (element.idHorarioE == $('#r_horarioXE').val()) {
                contenido += `<div class="col-md-12">
                                    <div class="row pt-2 pb-2 pl-3 pr-3">
                                        <div class="col-md-12" style="border: 1px dashed #aaaaaa!important;border-radius:5px">
                                            <div class="table-responsive">
                                                <table>
                                                    <tr>
                                                        <td><span style="color:#62778c;">Horario:</span></td>
                                                        <td class="separacion"><span>${element.descripcion}</span></td>
                                                        <td><span style="color:#62778c;">Permitir trabajar fuera horario:</span></td>
                                                        <td style="padding-left: 1em"><span>${(element.fueraH == 1) ? "Si" : "No"}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><span style="color:#62778c;">Hora inicio:</span></td>
                                                        <td class="separacion"><span>${element.horaI}</span></td>
                                                        <td><span style="color:#62778c;">Tolerancia inicio (minutos):</span></td>
                                                        <td style="padding-left: 1em"><span>${element.toleranciaI}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><span style="color:#62778c;">Hora fin:</span></td>
                                                        <td class="separacion"><span>${element.horaF}</span></td>
                                                        <td><span style="color:#62778c;">Tolerancia fin (minutos):</span></td>
                                                        <td style="padding-left: 1em"><span>${element.toleranciaF}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><span style="color:#62778c;">Horas obligadas:</span></td>
                                                        <td class="separacion"><span>${element.horasObligadas}</span></td>
                                                        <td><span style="color:#62778c;">Horas adicionales:</span></td>
                                                        <td style="padding-left: 1em"><span>${element.horasAdicionales}</span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                            </div>`;
            }
        });
        $('#AM_detalleHorarios').append(contenido);
        $('#AM_detalleHorarios').show();
    }
});
// * VARIABLES DE MARCACIONES
var newEntrada = {};
var newSalida = {};
var newFechaEntrada = {};
var newFechaSalida = {};
// * MOSTRAR BOTON DE AGREGAR MARCACION
$('#r_horarioXE').on("change", function () {
    $('#am_valid').empty();
    $('#am_valid').hide();
    $('#rowDatosM').show();
    // * INPUT DE ENTRADA
    newEntrada = $('#nuevaEntrada').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",
        time_24hr: true,
        enableSeconds: true,
        static: false,
        allowInput: false,
        disableMobile: true
    });
    var minDat = moment($("#fechaInput").val()).format("YYYY-MM-DD");
    if ($(this).val() == 0) {
        var maxDat = moment($("#fechaInput").val()).format("YYYY-MM-DD");
    } else {
        var maxDat = moment($("#fechaInput").val()).add("day", 1).format("YYYY-MM-DD");
    }
    var defaultDat = moment($("#fechaInput").val()).format("YYYY-MM-DD");
    // * FECHA DE ENTRADA
    newFechaEntrada = $('#fechaNuevaEntrada').flatpickr({
        mode: "single",
        dateFormat: "Y-m-d",
        locale: "es",
        defaultDate: defaultDat,
        maxDate: maxDat,
        minDate: minDat,
        enableTime: false,
        allowInput: false,
        disableMobile: true
    });
    // * INPUT DE SALIDA
    newSalida = $('#nuevaSalida').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i:s",
        defaultDate: "00:00:00",
        time_24hr: true,
        enableSeconds: true,
        static: false,
        allowInput: false,
        disableMobile: true
    });
    // * FECHA DE SALIDA
    newFechaSalida = $('#fechaNuevaSalida').flatpickr({
        mode: "single",
        dateFormat: "Y-m-d",
        locale: "es",
        defaultDate: defaultDat,
        maxDate: maxDat,
        minDate: minDat,
        enableTime: false,
        allowInput: false,
        disableMobile: true
    });
});
// * SIN ENTRADA
$('#v_entrada').on("change", function (event) {
    if (event.target.checked) {
        newEntrada.setDate("00:00:00");
        $('#nuevaEntrada').prop("disabled", true);
        $('#fechaNuevaEntrada').prop("disabled", true);
        $('#nuevaSalida').prop("disabled", false);
        $('#v_salida').prop("checked", false);
        $('#fechaNuevaSalida').prop("disabled", false);
    } else {
        $('#nuevaEntrada').prop("disabled", false);
        $('#fechaNuevaEntrada').prop("disabled", false);
    }
});
// * SIN SALIDA
$('#v_salida').on("change", function (event) {
    if (event.target.checked) {
        newSalida.setDate("00:00:00");
        $('#nuevaSalida').prop("disabled", true);
        $('#fechaNuevaSalida').prop("disabled", true);
        $('#v_entrada').prop("checked", false);
        $('#nuevaEntrada').prop("disabled", false);
        $('#fechaNuevaEntrada').prop("disabled", false);
    } else {
        $('#nuevaSalida').prop("disabled", false);
        $('#fechaNuevaSalida').prop("disabled", false);
    }
});
// * REGISTRAR MARCACION
function registrarMar() {
    var fecha = $('#a_fecha').val();
    var idEmpleado = $('#a_idE').val();
    var idHorarioE = $('#r_horarioXE').val();
    if ($('#v_entrada').is(":checked")) {
        var horaI = null;
    } else {
        var horaI = $('#fechaNuevaEntrada').val() + " " + $('#nuevaEntrada').val();
    }
    if ($('#v_salida').is(":checked")) {
        var horaF = null;
    } else {
        var horaF = $('#fechaNuevaSalida').val() + " " + $('#nuevaSalida').val();
    }
    $.ajax({
        async: false,
        type: "POST",
        url: "/agregarM",
        data: {
            fecha: fecha,
            idEmpleado: idEmpleado,
            horaI: horaI,
            horaF: horaF,
            idHE: idHorarioE
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
                $('#am_valid').empty();
                $('#am_valid').hide();
                $('#modalAgregar').modal('toggle');
                $('button[type="submit"]').attr("disabled", false);
                fechaValue.setDate(fechaGlobal);
                $('#btnRecargaTabla').click();
                limpiarAtributos();
                $.notifyClose();
                $.notify(
                    {
                        message: "\nMarcación registrada.",
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
                sent = false;
                $('button[type="submit"]').attr("disabled", false);
                $('#am_valid').empty();
                $('#am_valid').append(data.respuesta);
                $('#am_valid').show();
            }
        },
        error: function () { }
    });
}
$('#formRegistrarMar').attr('novalidate', true);
$('#formRegistrarMar').submit(function (e) {
    e.preventDefault();
    if ($('#r_horarioXE').val() == "" || $('#r_horarioXE').val() == null) {
        $('#am_valid').empty();
        $('#am_valid').append("Seleccionar horario.");
        $('#am_valid').show();
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if (!$('#v_entrada').is(":checked")) {
        if ($('#nuevaEntrada').val() == "00:00:0" || $('#nuevaEntrada').val() == "00:00:00") {
            $('#am_valid').empty();
            $('#am_valid').append("Ingresar entrada.");
            $('#am_valid').show();
            $('button[type="submit"]').attr("disabled", false);
            sent = false;
            return;
        }
    }
    if (!$('#v_salida').is(":checked")) {
        if ($('#nuevaSalida').val() == "00:00:0" || $('#nuevaSalida').val() == "00:00:00") {
            $('#am_valid').empty();
            $('#am_valid').append("Ingresar salida.");
            $('#am_valid').show();
            $('button[type="submit"]').attr("disabled", false);
            sent = false;
            return;
        }
    }
    if (!sent) {
        sent = true;
        $('#am_valid').empty();
        $('#am_valid').hide();
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});
// ! *********************************** CAMBIAR A ENTRADA ********************************************
// * FUNCION DE LISTA DE SALIDAS CON ENTRADAS NULL
function listaSalida(id, fecha, idEmpleado, hora, tipo, idHE) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == idHE) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
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
                    container += `</optgroup>`;
                }
            } else {
                var container = `<option value="" disabled selected>No hay marcaciónes disponibles</option>`;
            }
            $('#salidaM').append(container);
            imagenesSalida();
        },
        error: function () { }
    });
    sent = false;
    $('a').css('pointer-events', 'auto');
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
            if (data.respuesta == undefined) {
                $('#s_valid').hide();
                $('#listaSalidasMarcacion').modal('toggle');
                $('button[type="submit"]').attr("disabled", false);
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
                sent = false;
                $('button[type="submit"]').attr("disabled", false);
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
    if ($("#salidaM").val() == "" || $("#salidaM").val() == null) {
        $('#s_valid').empty();
        $('#s_valid').append("Seleccionar marcación.");
        $('#s_valid').show();
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if (!sent) {
        sent = true;
        $('#s_valid').empty();
        $('#s_valid').hide();
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});
// ! *********************************** CAMBIAR A SALIDA ****************************************************
// * FUNCION DE LISTA DE ENTRADAS CON SALIDAS NULL
function listaEntrada(id, fecha, idEmpleado, hora, tipo, idHE) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == idHE) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
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
                var container = `<option value="" disabled selected>Seleccionar entrada</option>`;
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
                    container += `</optgroup>`;
                }
            } else {
                var container = `<option value="" disabled selected>No hay marcaciónes disponibles</option>`;
            }
            $('#entradaM').append(container);
            imagenesEntrada();
        },
        error: function () { }
    });
    sent = false;
    $('a').css('pointer-events', 'auto');
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
                $('button[type="submit"]').attr("disabled", false);
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
                $('button[type="submit"]').attr("disabled", false);
                sent = false;
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
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if (!sent) {
        sent = true;
        $('#e_valid').empty();
        $('#e_valid').hide();
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});
// ! *********************************** CONVERTIR ORDEN ******************************************************
// * CONVERTIR ORDEN
function convertirOrden(id, idHE) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == idHE) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
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
    $('a').css('pointer-events', 'auto');
}
// ! ********************************** ASIGNAR A NUEVA MARCACIÓN *********************************************
// * ASIGNAR NUEVA MARCACION
function asignarNuevaM(id, hora, tipo, horario) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == horario) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
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
    $('a').css('pointer-events', 'auto');
    sent = false;
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
                $('button[type="submit"]').attr("disabled", false);
                limpiarAtributos();
            } else {
                $('#a_valid').empty();
                $('#a_valid').append(data.respuesta);
                $('#a_valid').show();
                sent = false;
                $('button[type="submit"]').attr("disabled", false);
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
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if ($("#asignacionM").val() == "" || $("#asignacionM").val() == null) {
        $('#a_valid').empty();
        $('#a_valid').append("Seleccionar marcación.");
        $('#a_valid').show();
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if (!sent) {
        sent = true;
        $('#a_valid').empty();
        $('#a_valid').hide();
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});
// ! ********************************** ELIMINAR MARCACIÓN *****************************************************
// * ELIMINAR MARCACION
function eliminarM(id, tipo, idHE) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == idHE) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
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
    $('a').css('pointer-events', 'auto');
}
// ! ******************************** INSERTAR SALIDA **********************************************************
// * MODAL DE INSERTAR SALIDA
function insertarSalidaModal(hora, id, idH) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == idH) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
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
        static: false,
        allowInput: false,
        disableMobile: true
    });
    $('a').css('pointer-events', 'auto');
    sent = false;
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
                $('button[type="submit"]').attr("disabled", false);
                sent = false;
            } else {
                $('#i_validS').empty();
                $('#i_validS').hide();
                $('#insertarSalida').modal('toggle');
                $('button[type="submit"]').attr("disabled", false);
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
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if (!sent) {
        sent = true;
        $('#i_validS').empty();
        $('#i_validS').hide();
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});
// ! ********************************* INSERTAR ENTRADA ********************************************************
function insertarEntradaModal(hora, id, idH) {
    $('a').css('pointer-events', 'none');
    var estadoH = false;
    contenidoHorario.forEach(element => {
        if (element.idHorarioE == idH) {
            if (element.estado == 0) {
                $('#actualizarH').modal();
                estadoH = true;
                return;
            }
        }
    });
    if (estadoH) { $('a').css('pointer-events', 'auto'); return };
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
        static: false,
        allowInput: false,
        disableMobile: true
    });
    $('a').css('pointer-events', 'auto');
    sent = false;
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
                $('button[type="submit"]').attr("disabled", false);
                sent = false;
            } else {
                $('#i_validE').empty();
                $('#i_validE').hide();
                $('#insertarEntrada').modal('toggle');
                $('button[type="submit"]').attr("disabled", false);
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
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if (!sent) {
        sent = true;
        $('#i_validE').empty();
        $('#i_validE').hide();
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});
// ! ********************************************** CAMBIAR DE HORARIO ***********************************************************
var datosHorario = {};
var dataMarcaciones = {};
// * MODAL CON LISTA DE HORARIOS
function modalCambiarHorario(fecha, id) {
    $('a').css('pointer-events', 'none');
    $('#idEmpleadoCH').val(id);
    $('#modalCambiarHorario').modal();
    $('#horarioXE').empty();
    $.ajax({
        async: false,
        type: "POST",
        url: "/horariosxEmpleado",
        data: {
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
            var container = `<option value="" disabled selected>Seleccionar horario</option>
                                <option value="0">Sin horario</option>`;
            for (let index = 0; index < data.length; index++) {
                container += `<option value="${data[index].idHorarioE}">${data[index].descripcion} (${data[index].horaI} - ${data[index].horaF})</option>`;
            }
            $('#horarioXE').append(container);
        },
        error: function () { }
    });
    $('a').css('pointer-events', 'auto');
    sent = false;
    listaMarcaciones(fecha, id);
}
// * MOSTRAR MARCACIONES
function listaMarcaciones(fecha, id) {
    // : MARCACIONES
    $.ajax({
        type: "POST",
        url: "/marcacionHorario",
        data: {
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
            dataMarcaciones = data;
        },
        error: function () { }
    });
}
// * MOSTRAR DETALLES DE HORARIO
$('#horarioXE').on("change", function () {
    $('#detalleHorarios').empty();
    $('#detalleHorarios').hide();
    if ($(this).val() != 0) {
        var contenido = `<div class="col-md-12"><span style="color:#183b5d;font-weight: bold">Detalles de Horario</span></div>`;
        datosHorario.forEach(element => {
            if (element.idHorarioE == $('#horarioXE').val()) {
                contenido += `<div class="col-md-12">
                                <div class="row pt-2 pb-2 pl-3 pr-3">
                                    <div class="col-md-12" style="border: 1px dashed #aaaaaa!important;border-radius:5px">
                                        <div class="table-responsive">
                                            <table>
                                                <tr>
                                                    <td><span style="color:#62778c;">Horario:</span></td>
                                                    <td class="separacion"><span>${element.descripcion}</span></td>
                                                    <td><span style="color:#62778c;">Permitir trabajar fuera horario:</span></td>
                                                    <td style="padding-left: 1em"><span>${(element.fueraH == 1) ? "Si" : "No"}</span></td>
                                                </tr>
                                                <tr>
                                                    <td><span style="color:#62778c;">Hora inicio:</span></td>
                                                    <td class="separacion"><span>${element.horaI}</span></td>
                                                    <td><span style="color:#62778c;">Tolerancia inicio (minutos):</span></td>
                                                    <td style="padding-left: 1em"><span>${element.toleranciaI}</span></td>
                                                </tr>
                                                <tr>
                                                    <td><span style="color:#62778c;">Hora fin:</span></td>
                                                    <td class="separacion"><span>${element.horaF}</span></td>
                                                    <td><span style="color:#62778c;">Tolerancia fin (minutos):</span></td>
                                                    <td style="padding-left: 1em"><span>${element.toleranciaF}</span></td>
                                                </tr>
                                                <tr>
                                                    <td><span style="color:#62778c;">Horas obligadas:</span></td>
                                                    <td class="separacion"><span>${element.horasObligadas}</span></td>
                                                    <td><span style="color:#62778c;">Horas adicionales:</span></td>
                                                    <td style="padding-left: 1em"><span>${element.horasAdicionales}</span></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
            }
        });
        $('#detalleHorarios').append(contenido);
        $('#detalleHorarios').show();
    }
    var containerMarcaciones = `<div class="col-md-12">
                                    <span style="color:#183b5d;font-weight: bold">Marcaciones</span>
                                </div>
                                <div class="col-md-12"><div class="row mt-2 mb-2">`;
    // : MARCACIONES
    var estadoM = false;
    $('#detalleMarcaciones').empty();
    for (let index = 0; index < dataMarcaciones.length; index++) {
        var dataM = dataMarcaciones[index];
        if ($('#horarioXE').val() != "") {
            if (dataM.idHorarioE != $('#horarioXE').val()) {
                estadoM = true;
                containerMarcaciones += `<div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table>
                                                            <tr>
                                                                <th colspan="3"><span style="color:#62778c;">${(dataM.descripcion == 0) ? 'Sin horario' : dataM.descripcion}</span></th>
                                                            </tr>`;
                dataM.data.forEach(element => {
                    containerMarcaciones += `<tr>
                                                <td><input type="checkbox" class="idMarcacion mt-2" value="${element.id}"></td>`;
                    if (element.entrada != 0) {
                        containerMarcaciones += `<td>
                                                    <img src="landing/images/entradaD.svg" height="12" class="ml-1 mr-1" />
                                                    ${moment(element.entrada).format("HH:mm:ss")}
                                                </td>`;
                        if (element.salida != 0) {
                            containerMarcaciones += `<td>
                                                        <img src="landing/images/salidaD.svg" height="12" class="ml-1 mr-1" />
                                                        ${moment(element.salida).format("HH:mm:ss")}
                                                    </td>`;
                        } else {
                            containerMarcaciones += `<td>
                                                        <span class="badge badge-soft-secondary noExport">
                                                            <img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                                                            No tiene salida
                                                        </span>
                                                    </td>`;
                        }
                    } else {
                        if (element.salida != 0) {
                            containerMarcaciones += `<td>
                                                        <span class="badge badge-soft-warning noExport">
                                                            <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                                                            No tiene entrada
                                                        </span>
                                                    </td>`;
                            containerMarcaciones += `<td>
                                                        <img src="landing/images/salidaD.svg" height="12" class="ml-1 mr-1" />
                                                        ${moment(element.salida).format("HH:mm:ss")}
                                                    </td>`;
                        }
                    }
                    containerMarcaciones += `</tr>`;
                });
                containerMarcaciones += `</table>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
            }
        }
    }
    if (estadoM) {
        $('button[type="submit"]').attr("disabled", false);
    } else {
        containerMarcaciones += `<div class="col-md-12">
                                    <div class="row justify-content-center">
                                        <div class="col-md-6 text-center">
                                            <img src='landing/images/escaneoM.svg' height='80'>
                                            <p>No se encontraron marcaciones disponibles</p>
                                        </div>
                                    </div>
                                </div>`;
        $('button[type="submit"]').attr("disabled", true);
    }
    containerMarcaciones += `</div></div>`;
    $('#detalleMarcaciones').append(containerMarcaciones);
    $('#detalleMarcaciones').show();
    $('#ch_valid').empty();
    $('#ch_valid').hide();
});
// * IDS DE CHECBOCK
function contenidoCheckboxM() {
    var resultado = [];
    $('.idMarcacion').each(function () {
        if ($(this).is(":checked")) {
            var idI = $(this).val();
            resultado.push(idI);
        }
    });
    return resultado;
}
// * FUNCION DE CAMBIAR DE HORARIO
function cambiarHorarioM() {
    var newH = $('#horarioXE').val();
    var idEmpleado = $('#idEmpleadoCH').val();
    var idsMarcaciones = contenidoCheckboxM();
    $.ajax({
        async: false,
        type: "POST",
        url: "/cambiarHorarioM",
        data: {
            idNuevo: newH,
            idEmpleado: idEmpleado,
            idsMarcaciones: idsMarcaciones
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
                $('button[type="submit"]').attr("disabled", false);
                fechaValue.setDate(fechaGlobal);
                $('#btnRecargaTabla').click();
                limpiarAtributos();
            } else {
                $('#ch_valid').empty();
                $('#ch_valid').append(data.respuesta);
                $('#ch_valid').show();
                $('button[type="submit"]').attr("disabled", false);
                sent = false;
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
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if ($('.idMarcacion').length != 0) {
        var estadoM = true;
        $('.idMarcacion').each(function () {
            if ($(this).is(":checked")) {
                estadoM = false;
            }
        });
        if (estadoM) {
            $('#ch_valid').empty();
            $('#ch_valid').append("Seleccionar marcación.");
            $('#ch_valid').show();
            sent = false;
            return;
        }
    }
    if (!sent) {
        sent = true;
        $('#ch_valid').empty();
        $('#ch_valid').hide();
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
});
// ! ************************************* ACTUALIZAR HORARIO EN MARCACION ****************************************************************
// * MODAL DE INFORMACION
function modalActualizarHorarioMarc(idHE, idM, fecha, id, entrada, salida, horario, inicioH, finH, estado) {
    $('#tbodyDetalleHM').empty();
    var tbodyHM = `<tr>`;
    // * ENTRADA
    if (entrada != 0) {
        tbodyHM += `<td class="text-center">
                        <img style="margin-bottom: 3px;" src="landing/images/entradaD.svg" class="mr-2" height="12"/>
                        ${moment(entrada).format("HH:mm:ss")}
                    </td>`;
    } else {
        tbodyHM += `<td class="text-center">
                        <span class="badge badge-soft-warning noExport">
                            <img style="margin-bottom: 3px;" src="landing/images/warning.svg" class="mr-2" height="12"/>
                            No tiene entrada
                        </span>
                    </td>`;
    }
    // * SALIDA 
    if (salida != 0) {
        tbodyHM += `<td class="text-center">
                        <img style="margin-bottom: 3px;" src="landing/images/salidaD.svg" class="mr-2" height="12"/> 
                        ${moment(salida).format("HH:mm:ss")}
                    </td>`;
    } else {
        tbodyHM += `<td class="text-center">
                        <span class="badge badge-soft-secondary noExport"><img style="margin-bottom: 3px;" src="landing/images/wall-clock (1).svg" class="mr-2" height="12"/>
                            No tiene salida
                        </span>
                    </td>`;
    }
    // * HORARIO
    if (horario == 'null') {
        tbodyHM += `<td class="text-center">
                        <span class="badge badge-soft-danger mr-2" class="text-center">
                            Sin horario
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-soft-danger mr-2" class="text-center">
                            Sin horario
                        </span>
                    </td>`;
    } else {
        if (estado == 1) {
            tbodyHM += `<td class="text-center">
                            <span class="badge badge-soft-primary mr-2" class="text-center">
                                ${horario}
                            </span>
                    </td>
                    <td class="text-center">
                        ${moment(inicioH).format("HH:mm:ss")} - ${moment(finH).format("HH:mm:ss")}
                    </td>`;
        } else {
            tbodyHM += `<td class="text-center">
                            <span class="badge badge-soft-danger mr-2" class="text-center">
                                <img style="margin-bottom: 3px;" src="admin/images/warning.svg" class="mr-2" height="12"/>
                                ${horario}
                            </span>
                        </td>
                        <td class="text-center">
                            <img style="margin-bottom: 3px;" src="admin/images/warning.svg" class="mr-2" height="12"/>
                            ${moment(inicioH).format("HH:mm:ss")} - ${moment(finH).format("HH:mm:ss")}
                        </td>`;
        }
    }
    $('#tbodyDetalleHM').append(tbodyHM);
    $('#idMarcacionHM').val(idM);
    $('#modalActualizarHM').modal();
    sent = false;
    listaDeHorarios(idHE, fecha, id);
}
var dataHorarioM = {};
// * COMBOX DE HORARIO
function listaDeHorarios(idHE, fecha, id) {
    $('#horarioXM').empty();
    $.ajax({
        async: false,
        type: "POST",
        url: "/listaHorarioM",
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
            dataHorarioM = data;
            var container = `<option value="" disabled selected>Seleccionar horario</option>`;
            if (idHE != 0) {
                container += `<option value="0">Sin horario</option>`;
            } else {
                if (data.length == 0) {
                    var container = `<option value="" disabled selected>No se encontraron horarios disponibles</option>`;
                }
            }
            for (let index = 0; index < data.length; index++) {
                container += `<option value="${data[index].idHorarioE}">${data[index].descripcion} (${data[index].horaI} - ${data[index].horaF})</option>`;
            }
            $('#horarioXM').append(container);
        },
        error: function () { }
    });
}
// * MOSTRAR DETALLES DE HORARIO
$('#horarioXM').on("change", function () {
    $('#detalleHorariosEM').empty();
    $('#detalleHorariosEM').hide();
    if ($(this).val() != 0) {
        var contenido = `<div class="col-md-12"><span style="color:#183b5d;font-weight: bold">Detalles de Horario</span></div>`;
        console.log(dataHorarioM);
        dataHorarioM.forEach(element => {
            if (element.idHorarioE == $('#horarioXM').val()) {
                contenido += `<div class="col-md-12">
                                    <div class="row p-2">
                                        <div class="col-md-12" style="border: 1px dashed #aaaaaa!important;border-radius:5px">
                                            <div class="table-responsive">
                                                <table>
                                                    <tr>
                                                        <td><span style="color:#62778c;">Horario:</span></td>
                                                        <td class="separacion"><span>${element.descripcion}</span></td>
                                                        <td><span style="color:#62778c;">Permitir trabajar fuera horario:</span></td>
                                                        <td style="padding-left: 1em"><span>${(element.fueraH == 1) ? "Si" : "No"}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><span style="color:#62778c;">Hora inicio:</span></td>
                                                        <td class="separacion"><span>${element.horaI}</span></td>
                                                        <td><span style="color:#62778c;">Tolerancia inicio (minutos):</span></td>
                                                        <td style="padding-left: 1em"><span>${element.toleranciaI}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><span style="color:#62778c;">Hora fin:</span></td>
                                                        <td class="separacion"><span>${element.horaF}</span></td>
                                                        <td><span style="color:#62778c;">Tolerancia fin (minutos):</span></td>
                                                        <td style="padding-left: 1em"><span>${element.toleranciaF}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><span style="color:#62778c;">Horas obligadas:</span></td>
                                                        <td class="separacion"><span>${element.horasObligadas}</span></td>
                                                        <td><span style="color:#62778c;">Horas adicionales:</span></td>
                                                        <td style="padding-left: 1em"><span>${element.horasAdicionales}</span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
            }
        });
        $('#detalleHorariosEM').append(contenido);
        $('#detalleHorariosEM').show();
    }
});
// * ACTUALIZAR HORARIO EN MARCACIÓN
function actualizacionMarcacionH() {
    var idM = $('#idMarcacionHM').val();
    var idHE = $('#horarioXM').val();
    $.ajax({
        async: false,
        type: "POST",
        url: "/actualizarHorarioM",
        data: {
            idHE: idHE,
            idM: idM
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
                $('#hm_valid').hide();
                $('#modalActualizarHM').modal("toggle");
                $('button[type="submit"]').attr("disabled", false);
                fechaValue.setDate(fechaGlobal);
                $('#btnRecargaTabla').click();
                limpiarAtributos();
            } else {
                $('#hm_valid').empty();
                $('#hm_valid').append(data.respuesta);
                $('#hm_valid').show();
                $('button[type="submit"]').attr("disabled", false);
                sent = false;
            }
        },
        error: function () { }
    });
}
// * VALIDACION
$('#formActualizacionMarcacionH').attr('novalidate', true);
$('#formActualizacionMarcacionH').submit(function (e) {
    e.preventDefault();
    if ($('#horarioXM').val() == null || $('#horarioXM').val() == "") {
        $('#hm_valid').empty();
        $('#hm_valid').append("Seleccionar horario.");
        $('#hm_valid').show();
        $('button[type="submit"]').attr("disabled", false);
        sent = false;
        return;
    }
    if (!sent) {
        sent = true;
        $('#hm_valid').empty();
        $('#hm_valid').hide();
        $('button[type="submit"]').attr("disabled", true);
        this.submit();
    }
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
    $('#detalleMarcaciones').empty();
    $('#detalleMarcaciones').hide();
    // ? MODAL DE NUEVA MARCACION
    $('#AM_detalleHorarios').empty();
    $('#AM_detalleHorarios').hide();
    $('#am_valid').empty();
    $('#am_valid').hide();
    $('#v_entrada').prop("checked", false);
    $('#v_salida').prop("checked", false);
    $('#nuevaEntrada').prop("disabled", false);
    $('#nuevaSalida').prop("disabled", false);
    $('#fechaNuevaEntrada').prop("disabled", false);
    $('#fechaNuevaSalida').prop("disabled", false);
    $('button[type="submit"]').attr("disabled", false);
    if (newSalida.config != undefined) {
        newSalida.setDate("00:00:00");
    }
    if (newEntrada.config != undefined) {
        newEntrada.setDate("00:00:00");
    }
    $('#rowDatosM').hide();
    $('#r_horarioXE').empty();
    $('[data-toggle="tooltip"]').tooltip("hide");
    // ? MODAL DE ACTUALIZAR HORARIO EN MARCACIÓN
    $('#tbodyDetalleHM').empty();
    $('#horarioXM').empty();
    $('#detalleHorariosEM').empty();
    $('#detalleHorariosEM').hide();
    $('#hm_valid').empty();
    $('#hm_valid').hide();
}
// ! ********************************* SELECTOR DE COLUMNAS ****************************************************
// * FUNCION PARA QUE NO SE CIERRE DROPDOWN
$('#dropSelector').on('hidden.bs.dropdown', function () {
    $('#contenidoDetalle').hide();
    $('#contenidoPausas').hide();
    $('#contenidoHorarios').hide();
    $('#contenidoIncidencias').hide();
    $('#contenidoPorH').hide();
    $('#contenidoPorT').hide();
});
$(document).on('click', '.allow-focus', function (e) {
    e.stopPropagation();
});
// : ************************************** COLUMNAS DE CALCULOS DE TIEMPO ***********************************************
// * TOGGLE DE DETALLES
function toggleD() {
    $('#contenidoDetalle').toggle();
}
// * TOGGLE POR HORARIO
function togglePorHorario() {
    $('#contenidoPorH').toggle();
}
// * TOGGLE POR TOTALES
function togglePorTotales() {
    $('#contenidoPorT').toggle();
}
// * HIJOS DE POR HORARIO Y TOTAL
$('.detalleHijoDeHijo input[type=checkbox]').change(function () {
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.detalleHijo').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.detalleHijo').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.detalleHijo').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    toggleColumnas();
});
// * PADRE DE HIJOS DE POR HORARIO Y TOTAL
$('.detalleHijo input[type=checkbox]').change(function () {
    $(this).closest('.detalleHijo').next('ul').find('.detalleHijoDeHijo input[type=checkbox]').prop('checked', this.checked);
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.detallePadre').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.detallePadre').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.detallePadre').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    toggleColumnas();
});
// * FUNCIONN DE CHECKBOX DE PADRE DETALLES
$('.detallePadre input[type=checkbox]').change(function () {
    $(this).closest('.detallePadre').next('ul').find('.detalleHijo input[type=checkbox]').prop('checked', this.checked);
    var contenido = $('.detalleHijo').next('ul').find('.detalleHijoDeHijo input[type=checkbox]').prop('checked', this.checked);
    console.log(contenido);
    toggleColumnas();
});
// : ************************************** COLUMNAS DE PAUSAS ***********************************************
function toggleP() {
    $('#contenidoPausas').toggle();
}
// * FUNCION DE CHECKBOX HIJOS PAUSAS
$('.pausaHijo input[type=checkbox]').change(function () {
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.pausaPadre').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.pausaPadre').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.pausaPadre').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    toggleColumnas();
});
// * FUNCIONN DE CHECKBOX DE PADRE DETALLES
$('.pausaPadre input[type=checkbox]').change(function () {
    $(this).closest('.pausaPadre').next('ul').find('.pausaHijo input[type=checkbox]').prop('checked', this.checked);
    toggleColumnas();
});
// : ************************************** COLUMNA DE CARGO ***************************************************
$('#colCargo').change(function (event) {
    if (event.target.checked) {
        dataT.api().columns('.colCargo').visible(true);
    } else {
        dataT.api().columns('.colCargo').visible(false);
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
});
// : ************************************ COLUMNA DE MARCACIONES ************************************************
$('#colMarcaciones').change(function (event) {
    if (event.target.checked) {
        dataT.api().columns('.colMarcaciones').visible(true);
    } else {
        dataT.api().columns('.colMarcaciones').visible(false);
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
});
// : *********************************** COLUMNA DE HORARIOS ***************************************************
function toggleH() {
    $('#contenidoHorarios').toggle();
}
// * FUNCION DE CHECKBOX HIJOS DE HORARIO
$('.horarioHijo input[type=checkbox]').change(function () {
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.horarioPadre').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.horarioPadre').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.horarioPadre').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    toggleColumnas();
});
// * FUNCIONN DE CHECKBOX DE PADRE DETALLES
$('.horarioPadre input[type=checkbox]').change(function () {
    $(this).closest('.horarioPadre').next('ul').find('.horarioHijo input[type=checkbox]').prop('checked', this.checked);
    toggleColumnas();
});
// : ********************************* COLUMNA DE CODIGO ********************************************************
$('#colCodigo').change(function (event) {
    if (event.target.checked) {
        dataT.api().columns('.colCodigo').visible(true);
    } else {
        dataT.api().columns('.colCodigo').visible(false);
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
});
// : ********************************* COLUMNA DE INCIDENCIAS ********************************************************
function toggleI() {
    $('#contenidoIncidencias').toggle();
}
// * FUNCION DE CHECKBOX HIJOS DE HORARIO
$('.incidenciaHijo input[type=checkbox]').change(function () {
    var contenido = $(this).closest('ul');
    if (contenido.find('input[type=checkbox]:checked').length == contenido.find('input[type=checkbox]').length) {
        contenido.prev('.incidenciaPadre').find('input[type=checkbox]').prop({
            indeterminate: false,
            checked: true
        });
    } else {
        if (contenido.find('input[type=checkbox]:checked').length != 0) {
            contenido.prev('.incidenciaPadre').find('input[type=checkbox]').prop({
                indeterminate: true,
                checked: false
            });
        } else {
            contenido.prev('.incidenciaPadre').find('input[type=checkbox]').prop({
                indeterminate: false,
                checked: false
            });
        }
    }
    toggleColumnas();
});
// * FUNCIONN DE CHECKBOX DE PADRE DETALLES
$('.incidenciaPadre input[type=checkbox]').change(function () {
    $(this).closest('.incidenciaPadre').next('ul').find('.incidenciaHijo input[type=checkbox]').prop('checked', this.checked);
    toggleColumnas();
});
// : ********************************* FINALIZACION *************************************************************
// * FUNCION DE MOSTRAR COLUMNAS
function toggleColumnas() {
    // * ***************** COLUMNAS DE CALCULOS DE TIEMPO ****************
    // ? TIEMPO ENTRE MARCACIONES
    if ($('#colTiempoSitio').is(":checked")) {
        dataT.api().columns('.colTiempoS').visible(true);
    } else {
        dataT.api().columns('.colTiempoS').visible(false);
    }
    // ? TIEMPO TOTAL
    if ($('#colTiempoTotal').is(":checked")) {
        dataT.api().columns('.colTiempoTotal').visible(true);
    } else {
        dataT.api().columns('.colTiempoTotal').visible(false);
    }
    // ? TIEMPO ENTRE HORARIOS
    if ($('#colTiempoEntreH').is(":checked")) {
        dataT.api().columns('.colTiempoEntreH').visible(true);
    } else {
        dataT.api().columns('.colTiempoEntreH').visible(false);
    }
    // ? SOBRE TIEMPO ENTRE HORARIOS
    if ($('#colSobreTiempo').is(":checked")) {
        dataT.api().columns('.colSobreTiempo').visible(true);
    } else {
        dataT.api().columns('.colSobreTiempo').visible(false);
    }
    // ? SOBRE TIEMPO TOTAL
    if ($('#colSobreTiempoTotal').is(":checked")) {
        dataT.api().columns('.colSobreTiempoTotal').visible(true);
    } else {
        dataT.api().columns('.colSobreTiempoTotal').visible(false);
    }
    // ? FALTA JORNADA ENTRE HORARIOS
    if ($('#colFaltaJornada').is(":checked")) {
        dataT.api().columns('.colFaltaJornada').visible(true);
    } else {
        dataT.api().columns('.colFaltaJornada').visible(false);
    }
    // ? FALTA JORNADA TOTAL
    if ($('#colFaltaJornadaTotal').is(":checked")) {
        dataT.api().columns('.colFaltaJornadaTotal').visible(true);
    } else {
        dataT.api().columns('.colFaltaJornadaTotal').visible(false);
    }
    // ? HORARIO NORMAL
    if ($('#colHoraNormal').is(":checked")) {
        dataT.api().columns('.colHoraNormal').visible(true);
    } else {
        dataT.api().columns('.colHoraNormal').visible(false);
    }
    // ? HORARIO NOCTURNO
    if ($('#colHoraNocturna').is(":checked")) {
        dataT.api().columns('.colHoraNocturna').visible(true);
    } else {
        dataT.api().columns('.colHoraNocturna').visible(false);
    }
    // ? HORARIO NORMAL TOTAL
    if ($('#colHoraNormalTotal').is(":checked")) {
        dataT.api().columns('.colHoraNormalTotal').visible(true);
    } else {
        dataT.api().columns('.colHoraNormalTotal').visible(false);
    }
    // ? HORARIO NOCTURNO TOTAL
    if ($('#colHoraNocturnaTotal').is(":checked")) {
        dataT.api().columns('.colHoraNocturnaTotal').visible(true);
    } else {
        dataT.api().columns('.colHoraNocturnaTotal').visible(false);
    }
    // ? HORAS EXTRAS 25% DIURNAS
    if ($('#colHE25D').is(":checked")) {
        dataT.api().columns('.colHE25D').visible(true);
    } else {
        dataT.api().columns('.colHE25D').visible(false);
    }
    // ? HORAS EXTRAS 35% DIURNAS
    if ($('#colHE35D').is(":checked")) {
        dataT.api().columns('.colHE35D').visible(true);
    } else {
        dataT.api().columns('.colHE35D').visible(false);
    }
    // ? HORAS EXTRAS 100% DIURNAS
    if ($('#colHE100D').is(":checked")) {
        dataT.api().columns('.colHE100D').visible(true);
    } else {
        dataT.api().columns('.colHE100D').visible(false);
    }
    // ? SOBRETIEMPO NORMAL POR HORARIO
    if ($('#colSobreTNormal').is(":checked")) {
        dataT.api().columns('.colSobreTNormal').visible(true);
    } else {
        dataT.api().columns('.colSobreTNormal').visible(false);
    }
    // * ****************** COLUMNAS DE PAUSAS *********************
    // ? DESCRION PAUSA
    if ($('#descripcionPausa').is(":checked")) {
        dataT.api().columns('.descripcionPausa').visible(true);
    } else {
        dataT.api().columns('.descripcionPausa').visible(false);
    }
    // ? HORARIO PAUSA
    if ($('#horarioPausa').is(":checked")) {
        dataT.api().columns('.horarioPausa').visible(true);
    } else {
        dataT.api().columns('.horarioPausa').visible(false);
    }
    // ? TIEMPO DE PAUSA
    if ($('#tiempoPausa').is(":checked")) {
        dataT.api().columns('.tiempoPausa').visible(true);
    } else {
        dataT.api().columns('.tiempoPausa').visible(false);
    }
    // ? EXCESO DE PAUSA
    if ($('#excesoPausa').is(":checked")) {
        dataT.api().columns('.excesoPausa').visible(true);
    } else {
        dataT.api().columns('.excesoPausa').visible(false);
    }
    // * *************** COLUMNA CARGO ******************************
    if ($('#colCargo').is(":checked")) {
        dataT.api().columns('.colCargo').visible(true);
    } else {
        dataT.api().columns('.colCargo').visible(false);
    }
    // * **************** COLUMNA MARCACION ***************************
    if ($('#colMarcaciones').is(":checked")) {
        dataT.api().columns('.colMarcaciones').visible(true);
    } else {
        dataT.api().columns('.colMarcaciones').visible(false);
    }
    // * *************** COLUMNA HORARIOS *****************************
    // ? DESCRIPCION DE HORARIO
    if ($('#descripcionHorario').is(":checked")) {
        dataT.api().columns('.descripcionHorario').visible(true);
    } else {
        dataT.api().columns('.descripcionHorario').visible(false);
    }
    // ? HORARIO
    if ($('#horarioHorario').is(":checked")) {
        dataT.api().columns('.horarioHorario').visible(true);
    } else {
        dataT.api().columns('.horarioHorario').visible(false);
    }
    // ? TOLERANCIA INICIO
    if ($('#toleranciaIHorario').is(":checked")) {
        dataT.api().columns('.toleranciaIHorario').visible(true);
    } else {
        dataT.api().columns('.toleranciaIHorario').visible(false);
    }
    // ? TOLERANCIA FIN
    if ($('#toleranciaFHorario').is(":checked")) {
        dataT.api().columns('.toleranciaFHorario').visible(true);
    } else {
        dataT.api().columns('.toleranciaFHorario').visible(false);
    }
    // * *************** COLUMNA CODIGO ******************************
    if ($('#colCodigo').is(":checked")) {
        dataT.api().columns('.colCodigo').visible(true);
    } else {
        dataT.api().columns('.colCodigo').visible(false);
    }
    // * *************** COLUMNA INCIDENCIAS ******************************
    // ? TARDANZA ENTRE HORARIOS
    if ($('#colTardanza').is(":checked")) {
        dataT.api().columns('.colTardanza').visible(true);
    } else {
        dataT.api().columns('.colTardanza').visible(false);
    }
    // ? FALTA ENTRE HORARIOS
    if ($('#faltaHorario').is(":checked")) {
        dataT.api().columns('.faltaHorario').visible(true);
    } else {
        dataT.api().columns('.faltaHorario').visible(false);
    }
    // ? TARDANZA TOTAL
    if ($('#colTardanzaTotal').is(":checked")) {
        dataT.api().columns('.colTardanzaTotal').visible(true);
    } else {
        dataT.api().columns('.colTardanzaTotal').visible(false);
    }
    // ? FALTA TOTAL
    if ($('#faltaTotal').is(":checked")) {
        dataT.api().columns('.faltaTotal').visible(true);
    } else {
        dataT.api().columns('.faltaTotal').visible(false);
    }
    // ? INCIDENCIAS
    if ($('#incidencia').is(":checked")) {
        dataT.api().columns('.incidencia').visible(true);
    } else {
        dataT.api().columns('.incidencia').visible(false);
    }
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
}