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
            for (let index = 0; index < data.length; index++) {
                var tardanza = 0;
                var diasTrabajdos = 0;
                // : HORAS NORMALES
                var horasNormales = moment("00:00:00", "HH:mm:ss");
                var diurnas25 = 0;
                var diurnas35 = 0;
                var diurnas100 = 0;
                // : FINALIZACION
                var descansoM = 0;
                var faltas = 0;
                var fi = 0;
                var fj = 0;
                var per = 0;
                var sme = 0;
                var suspension = 0;
                var vacaciones = 0;
                // : HORAS NOCTURNAS
                var horasNocturnas = moment("00:00:00", "HH:mm:ss");
                var nocturnas25 = 0;
                var nocturnas35 = 0;
                var nocturnas100 = 0;
                // : RECORRER DATA PARA CALCULAR DATOS
                for (let item = 0; item < data[index].data.length; item++) {
                    var dataCompleta = data[index].data[item];
                    if (dataCompleta["normal"] != undefined) {
                        dataCompleta["normal"].forEach(element => {
                            if (element.idHorario != 0) {
                                // : FALTAS
                                if (element.totalT == "00:00:00" && element.entrada == null) {
                                    faltas++;
                                } else {
                                    diasTrabajdos++;
                                    // : TARDANZA
                                    if (element.entrada != 0) {
                                        var horarioInicio = moment(element.horarioIni).add({ "minutes": element.toleranciaI });
                                        var entrada = moment(element.entrada);
                                        if (!entrada.isSameOrBefore(horarioInicio)) {
                                            tardanza++;
                                        }
                                    }
                                    console.log(horasNormales);
                                    // : HORAS TRABAJADOS
                                    var horaT = moment(element.totalT, "HH:mm:ss");
                                    var sumaDeTiempos = horasNormales + horaT;
                                    var horasTotal = Math.trunc(moment.duration(sumaDeTiempos).asHours());
                                    var minutosTotal = moment.duration(sumaDeTiempos).minutes();
                                    var segundosTotal = moment.duration(sumaDeTiempos).seconds();
                                    console.log(horasTotal, minutosTotal, segundosTotal);
                                    horasNormales = horasNormales.add({ "hours": horasTotal, "minutes": minutosTotal, "seconds": segundosTotal });
                                    console.log(horasNormales.format("HH:mm:ss"));
                                }
                            }
                        });
                    }
                }
            }
        },
        error: function (data) { }
    })
}