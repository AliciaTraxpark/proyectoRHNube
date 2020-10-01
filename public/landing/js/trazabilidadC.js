$.fn.select2.defaults.set('language', 'es');
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
// HORAS
Date.prototype.addHours = function (h) {
    this.setHours(this.getHours() + h);
    return this;
}
h = new Date();
var hi = h.getHours();
var hf = h.addHours(1).getHours();
// *****************
function defaultHora() {
    Date.prototype.addHours = function (h) {
        this.setHours(this.getHours() + h);
        return this;
    }
    var hC = new Date($('#horaI').val());
    var horF = hC.addHours(1).getHours();

    return horF;
}
console.log(defaultHora());
$('#horaI').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    defaultDate: hi + ":00",
    onClose: function (selectedDates, dateStr, instance) {
        horaFinal.set("minTime", minHoraF());
        horaFinal.set("defaultDate", defaultHora());
        if ($('#empresa :selected').text() != '') {
            datosOrganizacion();
        }
    }
});
function minHoraF() {
    return $('#horaI').val();
}
console.log(minHoraF());
var horaFinal = $('#horaF').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    defaultDate: hf + ":00",
    minTime: minHoraF(),
    onClose: function (selectedDates, dateStr, instance) {
        console.log($('#empresa :selected').text());
        if ($('#empresa :selected').text() != '') {
            datosOrganizacion();
        }
    }
});
$('#empresa').select2({
    placeholder: 'Seleccionar empresa',
    tags: true,
    maximumSelectionLength: 1
});
function datosOrganizacion() {
    var fechaI = $('#fecha').val() + $('#horaI').val();
    var fechaF = $('#fecha').val() + $('#horaF').val();
    var organizacion = $('#empresa').val();
    if ($.fn.DataTable.isDataTable("#Reporte")) {
        $('#Reporte').DataTable().destroy();
    }
    $.ajax({
        url: "/datosCapturas",
        data: {
            fecha_horaI: fechaI,
            fecha_horaF: fechaF,
            organizacion: organizacion
        },
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            console.log(data);
            var html_th = "<tr><th>idEmpleado</th><th><img src='admin/assets/images/users/empleado.png' class='mr-2' alt='' />Miembro</th>";
            for (let index = 0; index < data["horas"].length; index++) {
                html_th += "<th class=\"text-center\">" + data["horas"][index] + "</th>"
            }
            html_th += "</tr>";
            $('#horas').html(html_th);
            html_td = "";
            for (let index = 0; index < data["datos"].length; index++) {
                html_td += "<tr><td>" + data["datos"][index].idEmpleado + "</td>"
                html_td += "<td>" + data["datos"][index].nombre_apellido + "</td>";
                data["datos"][index].cantidad.forEach(element => {
                    html_td += "<td class=\"text-center\">" + element + "</td>";
                });
                html_td += "</tr>"
            }
            console.log(html_td);
            $('#datos').html(html_td);
            $("#Reporte").DataTable({
                scrollX: true,
                responsive: false,
                retrieve: true,
                "searching": true,
                "lengthChange": false,
                scrollCollapse: false,
                "pageLength": 30,
                "bAutoWidth": true,
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
    });
}
$(function () {
    $('#empresa').on("change", function (e) {
        datosOrganizacion();
    });
    $('#fecha').on("change", function () {
        if ($('#empresa :selected').text() != '') {
            datosOrganizacion();
        }
    });
    // $('#horaI').onClose(function () {
    //     horaFinal.set("minTime", minHoraF());
    //     datosOrganizacion();
    // });
    // $('#horaF').onClose(function () {
    //     datosOrganizacion();
    // });
});