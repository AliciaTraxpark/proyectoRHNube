//* FECHA
var fechaValue = $("#fechaTrazabilidad").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "j M",
    locale: "es",
    maxDate: "today",
    wrap: true,
    allowInput: true,
    conjunction: " a ",
    minRange: 1,
    onChange: function (selectedDates) {
        var _this = this;
        var dateArr = selectedDates.map(function (date) { return _this.formatDate(date, 'Y-m-d'); });
        $('#fechaInicio').val(dateArr[0]);
        $('#fechaFin').val(dateArr[1]);
    },
    onClose: function (selectedDates, dateStr, instance) {
        if (selectedDates.length == 1) {
            var fm = moment(selectedDates[0]).add("day", -1).format("YYYY-MM-DD");
            instance.setDate([fm, selectedDates[0]], true);
        }
    }
});
// * INICIALIZAR PLUGIN
$(function () {
    $('#idempleado').select2({
        placeholder: 'Todos los empleados',
        language: {
            inputTooShort: function (e) {
                return "Escribir nombre o apellido";
            },
            loadingMore: function () { return "Cargando más resultados…" },
            noResults: function () { return "No se encontraron resultados" }
        },
    });
    f = moment().format("YYYY-MM-DD");
    fechaAyer = moment().add("day", -1).format("YYYY-MM-DD");
    fechaValue.setDate([fechaAyer, f]);
    $("#fechaInput").change();
});
// * OBTENER DATA
function cargarDatos() {
    var fechaI = $('#fechaInicio').val();
    var fechaF = $('#fechaFin').val();
    var idsEmpleado = $('#idsEmpleado').val();
    $.ajax({
        async: false,
        url: "/dataTrazabilidad",
        method: "GET",
        data: {
            fechaI: fechaI,
            fechaF: fechaF,
            idsEmpleado: idsEmpleado
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
        },
        error: function (data) { }
    })
}