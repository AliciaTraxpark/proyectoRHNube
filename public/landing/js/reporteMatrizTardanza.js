/*   VARIABLES GLOBALES    */
var razonSocial;
var direccion;
var ruc;
var fecha;
var fechas;
var table = {};
var tableActividad = {};
var datos = {};
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*    SELECCIONA MES PARA EL REPORTE     */
$('#fechaMensual').datetimepicker({
    language: 'es',
    format: 'MM - yyyy',
    startView: 3,
    minView: 3,
    pickTime: false,
    autoclose: true,
    todayBtn: false,
    pickerPosition: "bottom-left"
});
/*   FIN DE FECHA    */
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*   NOTIFICACIÓN    */
var notify = $.notifyDefaults({
    icon_type: 'image',
    newest_on_top: true,
    delay: 4000,
    template: '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
        '<span data-notify="title">{1}</span> ' +
        '<span style="color:#8a6d3b" data-notify="message">{2}</span>' +
        '</div>'
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*    TABLA DE REPORTE INICIALIZADA      */
$(function () {
    $("#ReporteMensual").DataTable({
        "searching": false,
        "scrollX": true,
        retrieve: true,
        "ordering": false,
        "pageLength": 10,
        "autoWidth": false,
        "lengthChange": false,
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ ",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": ">",
                "sPrevious": "<"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad"
            }
        },
    });
});
/*   FIN DE TABLA   */
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*   OBTIENE TARDANZAS POR DÍA  */
function getTardanzas() {
    var fecha = $('#fechaMensual').val();
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
        url: "cargarMatrizTardanzas",
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
        success: function (data) {
            /*  OBTIENE TODAS LAS TARDANZAS SEGÚN EL EMPLEADO Y DÍA  */
            datos = data;
            reporteMatriz();
        },
        error: function (data) { }
    })
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function reporteMatriz() {
    $('#VacioImg').hide();
    /*   ELIMNA LA TABLA DE REPORTE INICIALIZADA  */
    if ($.fn.DataTable.isDataTable("#ReporteMensual")) {
        $('#ReporteMensual').DataTable().destroy();
    }
    /********* CUERPO DE LA TABLA REPORTE ***********/
    $('#empleadoMensual').empty();
    /********* CABECERA DE LA TABLA REPORTE ***********/
    $('#diasMensual').empty();

    if (datos.length > 0) {
        var nombre = [];
        var html_tr = "";
        var html_trD = "<tr><th>#</th><th>Código</th><th>Número de documento</th><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Nombres y apellidos</>";
        let html_tr_temp = "";
        fecha = datos[0].fecha;
        fechas = "Desde: "+datos[0].fechaD+" Hasta: "+datos[0].fechaH;
        ruc = datos[0].ruc;
        razonSocial = datos[0].razonSocial;
        direccion = datos[0].direccion;
        for (var i = 0; i < datos.length; i++) {
            html_tr_temp = "";
            html_tr += '<tr><td>' + (i + 1) + '</td><td>' + datos[i].codigo + '</td><td>' + datos[i].documento + '</td><td>' + datos[i].nombre + ' ' + datos[i].apPaterno + ' ' + datos[i].apMaterno + '</td>';
            var total = 1;
            for (let j = 0; j < datos[i].horas.length; j++) {
                // DIAS DE TARDANZAS
                if(datos[i].horas[j] == 0 )
                    html_tr_temp += '<td style="border-left:1px solid #aaaaaa !important; text-align: center;">' + datos[i].horas[j] + '</td>';
                else   
                    html_tr_temp += '<td style="border-left:1px solid #aaaaaa !important; text-align: center;"><div class="badge badge-soft-danger">' + datos[i].horas[j] + '</div></td>';
            }
            
            html_tr = html_tr + '<td style="text-align: center; background-color: #fafafa;">' + datos[i].cantidadTardanza + '</td>' + html_tr_temp + '</tr>';
        }
        html_trD += '<th>TOTAL</th>';
        for (var m = 0; m < datos[0].fechaF.length; m++) {
            var momentValue = moment(datos[0].fechaF[m]);
            momentValue.toDate();
            momentValue.format("ddd DD/MM");
            // DÍAS DEL MES
            html_trD += '<th style="border-left:1px solid #aaaaaa!important; text-align: center;">' + momentValue.format("ddd DD/MM") + '</th>';
        }
        
        html_trD += '</tr>';

        // TABLA DE REPORTE DE TARDANZAS
        $("#diasMensual").html(html_trD);
        $("#empleadoMensual").html(html_tr);

        table = $("#ReporteMensual").DataTable({
            "searching": false,
            "scrollX": true,
            retrieve: true,
            "ordering": false,
            "autoWidth": true,
            paging: false,
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ ",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": ">",
                    "sPrevious": "<"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },
            dom: 'Blfrtip',
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
                        msg = `<row r="${index}">`;
                        for (i = 0; i < data.length; i++) {
                            var key = data[i].k;
                            var value = data[i].v;
                            var bold = data[i].s;
                            msg += `<c t="inlineStr" r="${key} ${index}" s="${bold}" wpx="149">`;
                            msg += `<is>`;
                            msg += `<t>${value}</t>`;
                            msg += `</is>`;
                            msg += `</c>`;
                        }
                        msg += `</row>`;
                        return msg;
                    }
                    
                    //insert
                    var r1 = Addrow(1, [{ k: 'A', v: 'Matriz de Tardanzas', s: 51 }]);
                    var r2 = Addrow(2, [{ k: 'A', v: fechas, s: 2 }])
                    var r3 = Addrow(3, [{ k: 'A', v: 'Razón Social:', s: 2 }, { k: 'C', v: razonSocial, s: 0 }]);
                    var r4 = Addrow(4, [{ k: 'A', v: 'Dirección:', s: 2 }, { k: 'C', v: direccion, s: 0 }]);
                    var r5 = Addrow(5, [{ k: 'A', v: 'Número de Ruc:', s: 2 }, { k: 'C', v: ruc, s: 0 }]);
                    sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2 + r3 + r4 + r5 + sheet.childNodes[0].childNodes[1].innerHTML;
                },
                sheetName: 'Matriz de Tardanzas',
                title: 'Matriz de Tardanzas',
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

                            return $.trim(cambiar);
                        }
                    }
                },
            }, {
                extend: "pdfHtml5",
                className: 'btn btn-sm mt-1',
                text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                orientation: 'landscape',
                pageSize: 'A1',
                title: 'Matriz Tardanzas',
                exportOptions: {
                    columns: ":visible:not(.noExport)",
                },
                customize: function (doc) {
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
                            alignment: 'center'
                        }
                    };
                    doc.pageMargins = [20, 150, 20, 30];
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
                                bodyNuevo.push({ text: cambiar, style: 'defaultStyle' });
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
                                        { text: '\nMatriz Tardanzas\n', bold: true },
                                        { text: fechas, bold: false },
                                        { text: '\n\nRazón Social :\t\t\t', bold: false }, { text: razonSocial, bold: false },
                                        { text: '\nDirección:\t\t\t\t\t', bold: false }, { text: direccion, bold: false },
                                        { text: '\nNúmero de Ruc :\t\t', bold: false }, { text: ruc, bold: false },
                                        { text: '\nFecha  :\t\t\t\t\t\t', bold: false }, { text: fecha, bold: false },
                                    ],
                                    fontSize: 10,
                                    margin: [30, 10]
                                },
                            ],
                            margin: 20
                        };
                    };
                }
            }],
            paging: true
        });
        
    } else {
        $.notify({
            message: "No se encontraron datos.",
            icon: 'admin/images/warning.svg'
        });
        var html_trD = "<tr><th>#</th><th>Código</th><th>Número de documento</th><th><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Nombres y apellidos</th>";
        html_trD += '<th>TOTAL</th>';
        html_trD += '<th>LUN.</th>';
        html_trD += '<th>MAR.</th>';
        html_trD += '<th>MIÉ.</th>';
        html_trD += '<th>JUE.</th>';
        html_trD += '<th>VIE.</th>';
        html_trD += '<th>SÁB.</th>';
        // TABLA DEFAULT
        $('#diasMensual').html(html_trD);
        $("#empleadoMensual").html('<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty text-center"> &nbsp;&nbsp;&nbsp;&nbsp; No hay registros</td></tr>');
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*   FECHA DE BÚSQUEDA DEL REPORTE   */
function changeFechaT() {
    dato = $('#fechaMensual').val();
    value = moment(dato, ["MMMM-YYYY", "MMM-YYYY", "MM-YYYY"]).format("MM-YYYY");
    firstDate = moment(value, 'MM-YYYY').startOf('month').format('YYYY-MM-DD');
    lastDate = moment(value, 'MM-YYYY').endOf('month').format('YYYY-MM-DD');
    $('#fechaMensual').val(firstDate + "   a   " + lastDate);
    getTardanzas();
    $('#fechaMensual').val(dato)
}
/*   FIN FECHA DE BÚSQUEDA DEL REPORTE   */
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*         */
function fechaDefectoT() {
    dato = $('#fechaMensual').val();
    value = moment(dato, ["MMMM-YYYY", "MMM-YYYY", "MM-YYYY"]).format("MM-YYYY");
    firstDate = moment(value, 'MM-YYYY').startOf('month').format('YYYY-MM-DD');
    lastDate = moment(value, 'MM-YYYY').endOf('month').format('YYYY-MM-DD');
    $('#fechaMensual').val(firstDate + "   a   " + lastDate);
    getTardanzas();
    $('#fechaMensual').val(dato);
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
        console.log(area);
        $('#empleadoLT').empty();
        $.ajax({
            async: false,
            url: "/selectMatrizTardanzas",
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

/*     MUESTRA EL MES ACTUAL     */
$(function () {
    var hoy = moment().format("MMMM - YYYY");
    $('#fechaMensual').val(hoy);
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*    INICA EL PROCESO DE BUSCAR REPORTE      */
function buscarReporteT() {
    changeFechaT();
    $('#busquedaP').show();
    $('#busquedaA').show();
}
/*    FIN DE PROCESO      */
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$(function () {
    $('table thead th.next').empty();
    var thIconoN = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 17 17">\
    <g></g><path d="M13.207 8.472l-7.854 7.854-0.707-0.707 7.146-7.146-7.146-7.148 0.707-0.707 7.854 7.854z"></path></svg>';
    $('table thead th.next').html(thIconoN);
    $('table thead th.prev').empty();
    var thIconoP = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 17 17">\
    <g></g><path d="M5.207 8.471l7.146 7.147-0.707 0.707-7.853-7.854 7.854-7.853 0.707 0.707-7.147 7.146z"></path></svg>';
    $('table thead th.prev').html(thIconoP);
});
