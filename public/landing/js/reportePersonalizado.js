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
            return "Escribit nombre o apellido";
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
            if (data.captura.length != 0) {
                // ? BOTON DE DISPOSITIVOS
                $('#listaD').show();
                // ?
                // ? DATOS DE CAPTURA PARA LA TABLA 
                var html_tr = '';
                for (var i = 0; i < data.captura.length; i++) {
                    html_tr += '<tr><td class="text-center">' + data.captura[i].idCaptura + '</td>';
                    html_tr += '<td class="text-center">' + data.captura[i].hora_ini + '</td>';
                    html_tr += '<td class="text-center">' + data.captura[i].hora_fin + '</td>';
                    if (data.captura[i].horario === '0') {
                        html_tr += '<td class="text-center"><a class=\"badge badge-soft-success\">Sin horario</a></td>';
                    } else {
                        html_tr += '<td class="text-center"> <i class="uil uil-calender"></i>' + data.captura[i].horario + '</td>';
                    }
                    html_tr += '<td class="text-center">' + data.captura[i].actividad + '</td>';
                    if (data.captura[i].respuestaI === 'SI') {
                        html_tr += '<td class="text-center"><a class=\"badge badge-soft-primary\">' + data.captura[i].respuestaI + '</a></td>';
                    } else {
                        html_tr += '<td class="text-center"><a class=\"badge badge-soft-danger\">' + data.captura[i].respuestaI + '</a></td>';
                    }
                    if (data.captura[i].respuestaM === 'SI') {
                        html_tr += '<td class="text-center"><a class=\"badge badge-soft-primary\">' + data.captura[i].respuestaM + '</a></td>';
                    } else {
                        html_tr += '<td class="text-center"><a class=\"badge badge-soft-danger\">' + data.captura[i].respuestaM + '</a></td>';
                    }
                    html_tr += '<td class="text-center">' + data.captura[i].cantidadI + '</td></tr>';
                }
                $('#datos').html(html_tr);
                // ? FINALIZACION
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
                // ? DATOS DE LISTA DE DISPOSITIVOS
                $('#listaD').empty();
                menuItem = `<div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenu2"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Dispositivos
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2" id="menuD">`;
                for (let index = 0; index < data.dispositivo.length; index++) {
                    if (data.dispositivo[index].nombrePC === '0') {
                        menuItem += `<a class="dropdown-item" data-toggle="tooltip" data-placement="right"
                        title="nombre PC y versión" data-original-title="nombre PC y versión"><strong> PC ${index}</strong> | ${data.dispositivo[index].version}</a>`;
                    } else {
                        menuItem += `<a class="dropdown-item" data-toggle="tooltip" data-placement="right"
                        title="nombre PC y versión" data-original-title="nombre PC y versión"><strong> ${data.dispositivo[index].nombrePC}</strong> | ${data.dispositivo[index].version}</a>`;
                    }
                }
                menuItem += `</div></div>`;
                $('#listaD').append(menuItem);
                $('[data-toggle="tooltip"]').tooltip();
                // $fecha = new Date();
                // console.log($fecha.toLocaleTimeString("es-ES", { timeZone: 'America/Lima' }));
                // console.log($fecha);
            } else {
                tablaR();
                $('#listaD').hide();
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