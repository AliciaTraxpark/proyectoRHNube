//Define gauge options here...
var opts = {
    lines: 12,
    angle: 0.15,
    lineWidth: 0.44,
    pointer: {
        length: 0.5,
        strokeWidth: 0.035,
        color: '#444444'
    },
    limitMax: 'false',
    percentColors: [[0.0, "#ff0000"], [0.50, "#f9c802"], [1.0, "#a9d70b"]],
    strokeColor: '#E0E0E0',
    generateGradient: true,
    highDpiSupport: true,
    staticLabels: {
        font: "14px sans-serif",  // Specifies font
        labels: [0, 50, 100],  // Print labels at these values
        color: "#000000",  // Optional: Label text color
        fractionDigits: 0  // Optional: Numerical precision. 0=round off.
    },
};

function resultadoCR() {
    var resultado = 0;
    $.ajax({
        async: false,
        url: "/dashboardCR",
        method: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            var promedio = parseFloat((data.totalActividad / data.totalRango) * 100).toFixed(2);
            resultado = promedio;
        }
    });

    return resultado;
}
function myTimer() {
    var valor = resultadoCR();
    gauge.setMinValue(0);
    gauge.maxValue = 100;
    gauge.animationSpeed = 50;
    gauge.set(valor);
}

var target = document.getElementById('foo');
var gauge = new Gauge(target).setOptions(opts);
gauge.setTextField(document.getElementById("gauge-value"));

//---------------------------------------------------------------------
myTimer();