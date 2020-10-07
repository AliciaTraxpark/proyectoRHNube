$.fn.select2.defaults.set('language', 'es');
var notify = $.notifyDefaults({
    icon_type: "image",
    newest_on_top: true,
    delay: 4000,
    template:
        '<div data-notify="container" class="col-xs-12 col-sm-3 text-center alert" style="background-color: #fcf8e3;" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '<img data-notify="icon" class="img-circle pull-left" height="20">' +
        '<span data-notify="title">{1}</span> ' +
        '<span style="color:#8a6d3b" data-notify="message">{2}</span>' +
        "</div>",
});
//FECHA
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
    f = moment().format("YYYY-MM-DD");
    fechaValue.setDate(f);
});
$('#empresa').select2({
    placeholder: 'Seleccionar empresa',
    tags: true,
    maximumSelectionLength: 1
});
$('#empleado').select2({
    placeholder: 'Seleccionar',
    minimumInputLength: 1,
    language: {
        inputTooShort: function (e) {
            return "Escribir coincidencias...";
        },
        loadingMore: function () { return "Cargando más resultados…" },
        noResults: function () { return "No se encontraron resultados" }
    }
});
$('#empresa').on("change", function () {
    $('#empleado').val(null).trigger("change");
    $("#empleado").on("select2:opening", function () {
        var value = $("#empleado").val();
        $("#empleado").empty();
        var container = $("#empleado");
        var $idOrganizacion = $('#empresa :selected').val();
        $.ajax({
            async: false,
            url: '/empleadosOrg/' + $idOrganizacion,
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
                if (data.length == 0) {
                    var option = `<option value="" disabled selected>No se encontraron datos</option>`;
                } else {
                    var option = `<option value="" disabled selected>Seleccionar</option>`;
                    for (var $i = 0; $i < data.length; $i++) {
                        option += `<option value="${data[$i].emple_id}">${data[$i].nombre} ${data[$i].apPaterno} ${data[$i].apMaterno}</option>`;
                    }
                }
                container.append(option);
                $("#empleado").val(value);
            },
            error: function () { },
        });
        // $("#empleado").select2({
        //     ajax: {
        //         url: function () {
        //             var $idOrganizacion = $('#empresa :selected').val();
        //             console.log($idOrganizacion);
        //             return '/empleadosOrg/' + $idOrganizacion;
        //         },
        //         dataType: 'json',
        //         processResults: function (data, params) {
        //             return {
        //                 results: data,
        //                 pagination: {
        //                     more: (params.page * 30) < data.total_count
        //                 }
        //             };
        //         },
        //         transport: function (params, success, failure) {
        //             var request = new AjaxRequest(params.url, params);
        //             request.on('success', success);
        //             request.on('failure', failure);
        //         },
        //         cache: false,
        //     },
        // });
    });
});
function tablaR() {
    $("#Reporte").DataTable({
        "searching": false,
        "scrollX": true,
        retrieve: true,
        "ordering": true,
        "pageLength": 15,
        "autoWidth": true,
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
        }
    });
}

tablaR();

function reporteEmpleado() {
    console.log("ingreso");
    var fecha = $('#fecha').val();
    var idEmpleado = $('#empleado').val();
    if ($.fn.DataTable.isDataTable("#Reporte")) {
        $('#Reporte').DataTable().destroy();
    }
    $('#datos').empty();
    $.ajax({
        async: false,
        url: "/capturasPersonalizadas",
        method: "GET",
        data: {
            idEmpleado: idEmpleado,
            fecha: fecha,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
            console.log(data);
            if (data.length != 0) {
                var html_tr = '';
                for (var i = 0; i < data.length; i++) {
                    html_tr += '<tr><td class="text-center">' + data[i].idCaptura + '</td>';
                    html_tr += '<td class="text-center">' + data[i].hora_ini + '</td>';
                    html_tr += '<td class="text-center">' + data[i].hora_fin + '</td>';
                    html_tr += '<td class="text-center">' + data[i].actividad + '</td>';
                    if(data[i].respuestaI === 'SI'){
                        html_tr += '<td class="text-center"><a class=\"badge badge-soft-primary\">' + data[i].respuestaI + '</a></td>';
                    }else{
                        html_tr += '<td class="text-center"><a class=\"badge badge-soft-danger\">' + data[i].respuestaI + '</a></td>';
                    }
                    if(data[i].respuestaM === 'SI'){
                        html_tr += '<td class="text-center"><a class=\"badge badge-soft-primary\">' + data[i].respuestaM + '</a></td>';
                    }else{
                        html_tr += '<td class="text-center"><a class=\"badge badge-soft-danger\">' + data[i].respuestaM + '</a></td>';
                    }
                    html_tr += '<td class="text-center">' + data[i].cantidadI + '</td></tr>';
                }
                $('#datos').html(html_tr);
                $("#Reporte").DataTable({
                    "searching": false,
                    "scrollX": true,
                    retrieve: true,
                    "ordering": true,
                    "pageLength": 15,
                    "autoWidth": true,
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
                    }, {
                        extend: "pdfHtml5",
                        className: 'btn btn-sm mt-1',
                        text: "<i><img src='admin/images/pdf.svg' height='20'></i> Descargar",
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: 'REPORTE SEMANAL',
                        customize: function (doc) {
                            doc['styles'] = {
                                userTable: {
                                    margin: [0, 15, 0, 15]
                                },
                                title: {
                                    color: '#163552',
                                    fontSize: '20',
                                    alignment: 'center'
                                },
                                tableHeader: {
                                    bold: !0,
                                    fontSize: 11,
                                    color: '#FFFFFF',
                                    fillColor: '#163552',
                                    alignment: 'center'
                                }
                            };
                        }
                    }],
                    paging: true
                });
            } else {
                tablaR();
                $.notify({
                    message: "No se encontraron datos.",
                    icon: 'admin/images/warning.svg'
                });
            }
        },
        error: function () { },
    });
}
//CAPTURAS
function buscarCapturas() {
    reporteEmpleado();
}