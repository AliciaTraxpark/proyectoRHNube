// * VARIABLES GENERALES
var table;
var razonSocial;
var direccion;
var ruc;
var fecha;
var fechaD;
var fechaH;
var imagen;
var dimensiones;
var fechaInicio;
var fechaFin;
var data;
var dataT = {};
$("div.loader").hide(0);
//* FECHA
var fechaValue = $("#fechaSelecH").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "j F",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: false,
    conjunction: " a ",
    minRange: 1,
    onChange: function (selectedDates, dateStr) {
        var _this = this;
        var dateArr = selectedDates.map(function (date) { return _this.formatDate(date, 'Y-m-d'); });
        $('#ID_START').val(dateArr[0]);
        $('#ID_END').val(dateArr[1]);
        
        // CANTIDAD DE DÍAS SELECCIONADOS
        let daysInRange = document.getElementsByClassName('inRange');
        let daysLengthTotal = daysInRange.length + 1;
        //console.log(daysLengthTotal);
    },
    onClose: function (selectedDates, dateStr, instance) {
        if (selectedDates.length == 1) {
            var fm = moment(selectedDates[0]).add("day", -1).format("YYYY-MM-DD");
            instance.setDate([fm, selectedDates[0]], true);
        }
    }
});
/*     COMBO BOX DINÁMICO PARA SELECCIONAR EMPLEADO     */
$(function () {
    $('#empleadoLT').select2({
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
    f = moment();
    fHoy = f.clone().format("YYYY-MM-DD");
    fAyer = f.clone().add("day", -1).format("YYYY-MM-DD");
    fechaValue.setDate([fAyer, fHoy]);
    $("#fechaInput").change();
    $('#ID_START').val(fAyer);
    $('#ID_END').val(fHoy);
});
/*   FIN DE COMBO BOX  */
/*   MOSTRAR GIF DE CARGANDO  */
$("#btnRecargaTabla").click(function(){
    //$(".loader").show();
    //$(".img-load").show();
});
/*  LLENAR TABLA DE HORARIOS   */
function llenartablaHorarios() {
    document.getElementById("colCargo").checked = false;
    document.getElementById("colArea").checked = false;
    document.getElementById("colNivel").checked = false;
    document.getElementById("colLocal").checked = false;
    document.getElementById("colCentroCosto").checked = false;
            var tam = data.length;
            if (tam != 0) {
                razonSocial = data[0][0].organi_razonSocial;
                direccion = data[0][0].organi_direccion;
                ruc = data[0][0].organi_ruc;
                fecha = data[0].fecha;
                fechaD = $('#ID_START').val();
                fechaH = $("#ID_END").val();

                if ($.fn.DataTable.isDataTable("#tablaHorario")) {
                    $("#tablaHorario").DataTable().destroy();
                }
                
                // ! *********** CABEZERA DE TABLA**********
                $('#theadDHorario').empty();
                //* ARMAR CABEZERA
                var theadTabla = `<tr>
                        <th>#&nbsp;</th>
                        <th >Código&nbsp;</th>
                        <th>Número de documento&nbsp;</th>
                        <th class="formatoNYA">Nombres y apellidos&nbsp;</th>
                        <th class="formatoAYN">Apellidos y nombres&nbsp;</th>
                        <th class="formatoNA">Nombres&nbsp;</th>
                        <th class="formatoNA">Apellidos&nbsp;</th>
                        <th name="colArea" class="colArea">Área&nbsp;</th>
                        <th name="colCargo" class="colCargo">Cargo&nbsp;</th>
                        <th name="colNivel" class="colNivel" >Nivel&nbsp;</th>
                        <th name="colLocal" class="colLocal">Local&nbsp;</th>
                        <th name="colCentroCosto" class="colCentroCosto">Centro de costo&nbsp;</th>`;

                for(let i = data[tam-1]; i < data.length - 1; i++){
                    var momentValue = moment(data[i]);
                    momentValue.toDate();
                    momentValue.format("ddd DD/MM");
                    theadTabla += '<th style="border-left:1px solid #aaaaaa!important; text-align: center;">' + momentValue.format("ddd DD/MM") + '</th>';
                }
                

                //* DIBUJAMOS CABEZERA
                $('#theadDHorario').html(theadTabla);
                // ! *********** BODY DE TABLA**********
                $('#tbodyDHorario').empty();
                var tbody = "";
                var tdata = "";
                var band = false;
                //* ARMAMOS BODY DE TABLA
                for (let index = 0; index < data[tam-1]; index++) {
                    tdata = "";
                    tbody += `<tr>
                            <td>${index+1}&nbsp;</td>`;

                    if(data[index][0].codigo == null || data[index][0].codigo.length == 0 )
                        tbody += `<td>${data[index][0].documento} &nbsp;</td>`;
                    else 
                        tbody += `<td>${data[index][0].codigo} &nbsp;</td>`;

                    tbody += `<td>${data[index][0].documento} &nbsp;</td>
                            <td class="formatoNYA">${data[index][0].perso_nombre} ${data[index][0].perso_apPaterno} ${data[index][0].perso_apMaterno} &nbsp;</td>
                            <td class="formatoAYN">${data[index][0].perso_apPaterno} ${data[index][0].perso_apMaterno} ${data[index][0].perso_nombre} &nbsp;</td>
                            <td class="formatoNA">${data[index][0].perso_nombre}&nbsp;</td>
                            <td class="formatoNA">${data[index][0].perso_apPaterno} ${data[index][0].perso_apMaterno} &nbsp;</td>`;

                    if(data[index][0].area == null || data[index][0].area.length == 0)
                        tbody += `<td> -- &nbsp;</td>`;
                    else 
                        tbody += `<td>${data[index][0].area} &nbsp;</td>`;

                    if(data[index][0].cargo == null || data[index][0].cargo.length == 0)
                        tbody += `<td> -- &nbsp;</td>`;
                    else 
                        tbody += `<td>${data[index][0].cargo} &nbsp;</td>`;

                    if(data[index][0].nivel == null || data[index][0].nivel.length == 0)
                        tbody += `<td> -- &nbsp;</td>`;
                    else 
                        tbody += `<td>${data[index][0].nivel} &nbsp;</td>`;

                    if(data[index][0].local == null || data[index][0].local.length == 0)
                        tbody += `<td> -- &nbsp;</td>`;
                    else 
                        tbody += `<td>${data[index][0].local} &nbsp;</td>`;

                    if(data[index][0].centroC_descripcion == null || data[index][0].centroC_descripcion.length == 0)
                        tbody += `<td> -- &nbsp;</td>`;
                    else 
                        tbody += `<td>${data[index][0].centroC_descripcion} &nbsp;</td>`;

                    for(let i = data[tam-1]; i < data.length - 1; i++){
                        tdata +=  `<td style="border-left:1px solid #aaaaaa!important;" class="text-center">`;
                        var momentDia = moment(data[i]).format("YYYY-MM-DD");
                        for(let indexI = 0; indexI < data[index].length; indexI++){
                            if(data[index][indexI].DP == momentDia){
                                band = true;
                               tdata += `<a class="badge badge-soft-primary mr-2" data-toggle="tooltip" data-placement="right" title="Hora de inicio: ${data[index][indexI].horaInicio} Hora fin: ${data[index][indexI].horaFinal} Horas obligadas: ${data[index][indexI].horasObligadas}"> ${data[index][indexI].horario} </a>`; 
                            }
                        }
                        if(band == false){
                            tdata += `<a class="badge badge-soft-danger mr-2" data-toggle="tooltip" data-placement="right" title="Horario no asignado"> Sin horario </a>`; 
                        }
                        tdata += `</td>`;
                        band = false;
                    }
                    tbody += tdata;
                    tbody += `</tr>`;
                }
                $('#tbodyDHorario').html(tbody);

            $(function () {
              $('[data-toggle="tooltip"]').tooltip();
            })

            table = $("#tablaHorario").DataTable({
        "searching": true,
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
            processing: "<img src='landing/images/logoR.gif' height='180'>\n&nbsp;&nbsp;&nbsp;&nbsp;Generando informe...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando registros del _START_ al _END_ ",
            sInfoEmpty:
                "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "",
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
                    var r1 = Addrow(1, [{ k: 'A', v: 'Matriz de Horarios', s: 2 }]);
                    var r2 = Addrow(2, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                    var r3 = Addrow(3, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                    var r4 = Addrow(4, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                    var r5 = Addrow(5, [{ k: 'A', v: 'Fecha:', s: 2 }, { k: 'C', v: jsDate, s: 0 }]);
                    sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + sheet.childNodes[0].childNodes[1].innerHTML;
                },
                sheetName: 'Matriz de Horarios',
                title: 'Matriz de Horarios',
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
                title: 'Matriz de Horarios',
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
                    var tr = $('#tablaHorario tbody tr:first-child');
                    var trWidth = $(tr).width();
                    $('#tablaHorario').find('tbody tr:first-child td').each(function () {
                        var tdWidth = $(this).width();
                        var widthFinal = parseFloat(tdWidth * 130);
                        widthFinal = widthFinal.toFixed(2) / trWidth.toFixed(2);
                        if ($(this).attr('colspan')) {
                            for (var i = 1; i <= $(this).attr('colspan'); i++) {
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
                                        { text: '\nMatriz de Horarios', bold: true },
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
                $("#tablaHorario").DataTable().draw();
            }, 1);
            if (this.api().data().length == 0) {
                $('.buttons-page-length').prop("disabled", true);
                $('.buttons-html5').prop("disabled", true);
                $('#switchO').prop("disabled", true);
                $('.dropReporte').prop("disabled", true);
                $('#colEmpleadosCM').prop("disabled", true);
                $('#formatoC').prop("disabled", true);
            } else {
                $('.buttons-page-length').prop("disabled", false);
                $('.buttons-html5').prop("disabled", false);
                $('#switchO').prop("disabled", false);
                $('.dropReporte').prop("disabled", false);
                $('#colEmpleadosCM').prop("disabled", false);
                $('#formatoC').prop("disabled", false);
            }
        },
        drawCallback: function () {
            var api = this.api();
            var len = api.page.len();
            paginaGlobal = len;
        }
    }).draw();

                $(window).on('resize', function () {
                    $("#tablaHorario").css('width', '100%');
                    table.draw(true);
                });
                setTimeout(function() {
                    $(".loader").hide();
                    $(".img-load").hide();
                }, 500);
            } else {
                ///$('#btnsDescarga').hide();
                $('#tbodyDHorario').empty();
                $('#tbodyDHorario').append('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
                setTimeout(function() {
                    $(".loader").hide();
                    $(".img-load").hide();
                }, 500);

            }
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
    toggleColumnas();
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*   OBTIENE TARDANZAS POR DÍA  */
function getHorarios() {
    var fecha = $('#fechaInput').val();
    /* ver si se seleccionó alguno de ... ['CARGO', 'ÁREA', 'LOCAL']*/
    let textSelec = $('select[id="areaT"] option:selected:last').text();
    let selector = textSelec.split(' ')[0];
    var area = $('#areaT').val();
    var empleadoL = $('#empleadoLT').val();
    if ($.fn.DataTable.isDataTable("#ReporteMensual")) {
        $('#ReporteMensual').DataTable().destroy();
    }
    $('#empleadoMensual').empty();
    $('#diasMensual').empty();
    $.ajax({
        async: false,
        url: "cargarMatrizHorario",
        method: "GET",
        data: {
            fecha: fecha,
            area: area,
            empleadoL: empleadoL,
            selector: selector
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            401: function () {
                location.reload();
            }
        },
        success: function (datos) {
            /*  OBTIENE TODAS LAS TARDANZAS SEGÚN EL EMPLEADO Y DÍA  */
            data = datos;
            llenartablaHorarios();
            setTimeout(function() {
                $(".loader").hide();
                $(".img-load").hide();
            }, 500);
        },
        error: function (data) { 
            setTimeout(function() {
                $(".loader").hide();
                    $(".img-load").hide();
            }, 500);
        }
    })
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*   CARGAR TABLA DE HORARIOS   */
function cargartablaHorarios(fecha1,fecha2) {
    $('.busquedaP').show();
    $('#busquedaA').show();
    $('#VacioImg').hide();
    $(".loader").show();
    $(".img-load").show();
    $("#dropSelector").show();
    $("#fotmatoCampos").show();
    var idemp = $('#empleadoLT').val();
    fechaInicio = fecha1;
    fechaFin = fecha2;
    $.ajax({
        type: "GET",
        url: "/cargarTablaHorarios",
        data: {
            fecha1,fecha2, idemp
        },
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        statusCode: {
            419: function () {
                location.reload();
            }
        },
        success: function (datos) {
           console.log(datos);
            var tam = datos.length;
            data = datos;
            llenartablaHorarios();
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

function toggleColumnas(){
    dataT.api().columns('.colCargo').visible(false);
    dataT.api().columns('.colArea').visible(false);
    dataT.api().columns('.colNivel').visible(false);
    dataT.api().columns('.colLocal').visible(false);
    dataT.api().columns('.colCentroCosto').visible(false);
    document.getElementById('colArea').checked = 0;
    document.getElementById('colCargo').checked = 0;
    document.getElementById('colNivel').checked = 0;
    document.getElementById('colLocal').checked = 0;
    document.getElementById('colCentroCosto').checked = 0;
    $('#colCargo').change(function (event) {
        if (event.target.checked) {
            dataT.api().columns('.colCargo').visible(true);
        } else {
            dataT.api().columns('.colCargo').visible(false);
        }
        setTimeout(function () { $("#tablaHorario").css('width', '100%'); $("#tablaHorario").DataTable().draw(false); }, 1);
    });
    $('#colArea').change(function (event) {
        if (event.target.checked) {
            dataT.api().columns('.colArea').visible(true);
        } else {
            dataT.api().columns('.colArea').visible(false);
        }
        setTimeout(function () { $("#tablaHorario").css('width', '100%'); $("#tablaHorario").DataTable().draw(false); }, 1);
    });
    $('#colNivel').change(function (event) {
        if (event.target.checked) {
            dataT.api().columns('.colNivel').visible(true);
        } else {
            dataT.api().columns('.colNivel').visible(false);
        }
        setTimeout(function () { $("#tablaHorario").css('width', '100%'); $("#tablaHorario").DataTable().draw(false); }, 1);
    });
    $('#colLocal').change(function (event) {
        if (event.target.checked) {
            dataT.api().columns('.colLocal').visible(true);
        } else {
            dataT.api().columns('.colLocal').visible(false);
        }
        setTimeout(function () { $("#tablaHorario").css('width', '100%'); $("#tablaHorario").DataTable().draw(false); }, 1);
    });
    $('#colCentroCosto').change(function (event) {
        if (event.target.checked) {
            dataT.api().columns('.colCentroCosto').visible(true);
        } else {
            dataT.api().columns('.colCentroCosto').visible(false);
        }
        setTimeout(function () { $("#tablaHorario").css('width', '100%'); $("#tablaHorario").DataTable().draw(false); }, 1);
    });
    $("#formatoC > option").each(function () {
        if (!$(this).is(":checked")) {
            dataT.api().columns('.' + $(this).val()).visible(false);
        }
    });
    var columnaVisibleFormato = $('#formatoC :selected').val();
    dataT.api().columns('.' + columnaVisibleFormato).visible(true);
    setTimeout(function () { $("#tablaReport").css('width', '100%'); $("#tablaReport").DataTable().draw(false); }, 1);
}


$('#formatoC').on("change", function () {
    toggleColumnas();
});

function cambiarFCR() {
    let f1 = $("#ID_START").val();
    let f2 = moment(f1).format("YYYY-MM-DD");
    $('#pasandoV').val(f2);
    let f3 = $("#ID_END").val();
    if ($.fn.DataTable.isDataTable("#tablaHorario")) {
        /* $('#tablaHorario').DataTable().destroy(); */
    }
    let area = $('#areaT').val();
    if(area == ""){
        cargartablaHorarios(f2,f3); 
    } else {
        fechaDefectoT();
    }
}

function fechaDefectoT() {
    dato = $('#fechaInput').val();
    f1 = $("#ID_START").val();
    f2 = moment(f1).format("YYYY-MM-DD");
    $('#pasandoV').val(f2);
    f3 = $("#ID_END").val();
    
    firstDate = moment(f1).format("YYYY-MM-DD");
    lastDate = moment(f3).format("YYYY-MM-DD");
    $('#fechaInput').val(firstDate + "   a   " + lastDate);
    getHorarios();
    $('#fechaInput').val(dato);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$(function () {
    $('#areaT').select2({
        placeholder: 'Seleccionar por:'
    });
    $('#empleadoLT').select2({
        placeholder: 'Seleccionar empleados',
        language: "es"
    });
    $('#areaT').on("change", function (e) {
        fechaDefectoT();
        var area = $(this).val();
        let textSelec = $('select[id="areaT"] option:selected:last').text();
        let selector = textSelec.split(' ')[0];
        $('#empleadoLT').empty();
        $.ajax({
            async: false,
            url: "/selectMatrizHorarios",
            method: "GET",
            data: {
                area: area,
                selector: selector
            },
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
                var select = "";
                for (let i = 0; i < data.length; i++) {
                    select += `<option value="${data[i].emple_id}">${data[i].nombre} ${data[i].apPaterno} ${data[i].apMaterno}</option>`
                }
                $('#empleadoLT').append(select);
            },
            error: function () { }
        });
    });
    $('#empleadoLT').on("change", function (e) {
        fechaDefectoT();
    });
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// * FUNCION DE MOSTRAR DETALLES
function cambiartabla() {
    if ($('#customSwitDetalles').is(':checked')) {
        $('[name="tiempoSitHi"]').show();
        setTimeout(function () { $("#tablaHorario").css('width', '100%'); $("#tablaHorario").DataTable().draw(true); }, 200);
    }
    else {
        $('[name="tiempoSitHi"]').hide();
        setTimeout(function () { $("#tablaHorario").css('width', '100%'); $("#tablaHorario").DataTable().draw(true); }, 200);
    }
}
// * FUNCION DE SWITCH DE MOSTRAR PAUSAS
function togglePausas() {
    if ($('#switPausas').is(':checked')) {
        $('[name="datosPausa"]').show();
        setTimeout(function () { $("#tablaHorario").css('width', '100%'); $("#tablaHorario").DataTable().draw(true); }, 200);
    } else {
        $('[name="datosPausa"]').hide();
        setTimeout(function () { $("#tablaHorario").css('width', '100%'); $("#tablaHorario").DataTable().draw(true); }, 200);
    }
}
