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
      var promedio = ((data.totalActividad / data.totalRango) * 100).toFixed(2);
      console.log(promedio);
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

// apex
$(function () {
  $('#fechaO').empty();
  var hoy = moment().format("YYYY-MM-DD");
  $.ajax({
    url: "/fechaOD",
    method: "GET",
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
      console.log(data);
      var fecha = `${data.created_at}`;
      $('#fechaO').append(fecha);
    }
  });
});
function fechas() {
  var respuesta = [];
  var hoy = moment().format("DD/MM/YYYY");
  value = moment(hoy, ["DD-MM-YYYY"]).format("YYYY-MM-DD");
  for (let index = 1; index < 8; index++) {
    fecha = moment(value, 'YYYY-MM-DD').day(index).format('DD-MMM');
    respuesta.push(fecha);
  }
  return respuesta;
}
function fechasSemanal() {
  var respuesta = [];
  var hoy = moment().format("DD/MM/YYYY");
  value = moment(hoy, ["DD-MM-YYYY"]).format("YYYY-MM-DD");
  for (let index = 1; index < 8; index++) {
    fecha = moment(value, 'YYYY-MM-DD').day(index).format('YYYY-MM-DD');
    respuesta.push(fecha);
  }
  return respuesta;
}
function dataFechas() {
  var resp = [];
  var respuesta = fechasSemanal();
  $.ajax({
    async: false,
    url: "/fechasDataDashboard",
    data: {
      fechas: respuesta
    },
    method: "GET",
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
      if (data.length == 0) {
        $('#divArea').hide();
      } else {
        $('#divArea').show();
        for (let index = 0; index < data.length; index++) {
          let result = data[index].data.map(a => a.toFixed(2));
          let serie = { "name": data[index].area, "data": result }
          resp.push(serie);
        }
      }
    }
  });

  return resp;
}
var options = {
  series: dataFechas(),
  chart: {
    height: 350,
    type: 'line',
    dropShadow: {
      enabled: true,
      color: '#000',
      top: 18,
      left: 7,
      blur: 10,
      opacity: 0.2
    },
    toolbar: {
      show: false
    }
  },
  colors: ['#77B6EA', '#545454'],
  dataLabels: {
    enabled: true,
    style: {
      fontSize: '10px',
    },
  },
  stroke: {
    curve: 'smooth'
  },
  grid: {
    borderColor: '#e7e7e7',
    row: {
      colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
      opacity: 0.5
    },
  },
  markers: {
    size: 1
  },
  xaxis: {
    categories: fechas(),
    title: {
      text: 'Día'
    }
  },
  yaxis: {
    title: {
      offsetX: 3,
      text: 'Actividad'
    },
    min: 0,
    max: 100,
    labels: {
      offsetX: -8
    },
  },
  title: {
    text: 'Actividad y Día',
    align: 'left'
  },
  legend: {
    position: 'top',
    horizontalAlign: 'right',
    floating: true,
    offsetY: -25,
    offsetX: -5
  }
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();

dataFechas();