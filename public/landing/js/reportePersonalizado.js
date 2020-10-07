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
$('#empleado').select2();
$('#empresa').on("change", function () {
    console.log($('#empresa').val());
    var $idOrganizacion = $('#empresa').val();
    $("#empleado").select2({
        ajax: {
            url: function () {
                var $idOrganizacion = $('#empresa :selected').val();
                console.log($idOrganizacion);
                return '/empleadosOrg/' + $idOrganizacion;
            },
            dataType: 'json',
            processResults: function (data, params) {
                return {
                    results: data,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            }
        },
    });
});
$(function () {
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
});

function reporteEmpleado() {
    console.log("ingreso");
    var fecha = $('#fecha').val();
    var idEmpleado = $('#empleado').val();
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
            if (data.length != 0) {
                var html_tr = '';
                for (var i = 0; i < data.length; i++) {
                    html_tr += '<tr><td>' + data[i].idCaptura + '</td>';
                    html_tr += '<td>' + data[i].hora_ini + '</td>';
                    html_tr += '<td>' + data[i].hora_fin + '</td>';
                    html_tr += '<td>' + data[i].actividad + '</td></tr>';
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