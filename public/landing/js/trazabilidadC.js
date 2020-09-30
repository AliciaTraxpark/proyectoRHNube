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
$('#horaI').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    defaultDate: "09:00"
});
$('#horaF').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    defaultDate: "10:00"
});
$('#empresa').select2({
    placeholder: 'Seleccionar empresa'
});
$('#empleado').select2({
    placeholder: 'Seleccionar empleado'
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
                html_th += "<th>" + data["horas"][index] + "</th>"
            }
            html_th += "</tr>";
            $('#horas').html(html_th);
        }
    });
}
$('#empresa').on("change", function (e) {
    datosOrganizacion();
});