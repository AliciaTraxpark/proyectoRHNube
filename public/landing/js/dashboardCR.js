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
  strokeColor: '#f4f4f4',
  generateGradient: true,
  highDpiSupport: true,
  staticLabels: {
    font: "14px sans-serif",  // Specifies font
    labels: [0, 50, 100],  // Print labels at these values
    color: "#000000",  // Optional: Label text color
    fractionDigits: 0  // Optional: Numerical precision. 0=round off.
  },
};
// FECHA
var fechaG = $("#fechaSelecG").flatpickr({
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
  fechaG.setDate(f);
  myTimer();
});
function resultadoCR() {
  var resultado = 0;
  var fecha = $('#fechaInputG').val();
  $.ajax({
    async: false,
    url: "/dashboardCR",
    method: "GET",
    data: {
      fecha: fecha
    },
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    statusCode: {
      401: function () {
        location.reload();
      },
      // 419: function () {
      //   location.reload();
      // }
    },
    success: function (data) {
      var promedio = data.actvidadCR.resultado.toFixed(2);
      resultado = promedio;
      $('#avatars').empty();
      var li = "";
      for (let index = 0; index < data.empleado.length; index++) {
        if (data.empleado[index].foto === "") {
          li += `<img class="liImg" src="admin/assets//images/users/avatar-7.png"  data-toggle="tooltip" data-placement="right" title="${data.empleado[index].perso_nombre} ${data.empleado[index].perso_apPaterno}"/>`;
        } else {
          li += `<img class="liImg" src="/fotosEmpleado/${data.empleado[index].foto}"  data-toggle="tooltip" data-placement="right" title="${data.empleado[index].perso_nombre} ${data.empleado[index].perso_apPaterno}"/>`;
        }
      }
      $('#avatars').append(li);
      $('[data-toggle="tooltip"]').tooltip();
    }
  });

  return resultado;
}
$(function () {
  $("#fechaInputG").on("change", function () {
    resultadoCR();
    myTimer();
  });
});
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

// apex
$(function () {
  $('#fechaO').empty();
  $.ajax({
    url: "/fechaOD",
    method: "GET",
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (data) {
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
var colores = ['#77B6EA', '#545454'];
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
    statusCode: {
      401: function () {
        location.reload();
      },
      // 419: function () {
      //   location.reload();
      // }
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
        for (let j = 2; j < data.length; j++) {
          colores.push(getRandomColor());
        }
      }
    }
  });

  return resp;
}
function getRandomColor() {
  var letters = 'ABCDE'.split('');
  var color = '#';
  for (var i = 0; i < 3; i++) {
    color += letters[Math.floor(Math.random() * letters.length)];
  }
  return color;
}
var options = {
  series: dataFechas(),
  chart: {
    height: 400,
    widht: '100%',
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
      show: true
    }
  },
  colors: colores,
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
    horizontalAlign: 'center',
    floating: true,
    offsetY: -25,
    offsetX: -5
  },
  responsive: [
    {
      breakpoint: 767.98,
      options: {
        chart: {
          height: 350,
          toolbar: {
            show: false
          },
          zoom: {
            enabled: false,
          }
        },
        title: {
          text: 'Actividad y Día',
          align: 'center',
        },
        legend: {
          position: "top",
          horizontalAlign: 'center',
          floating: true,
          offsetY: -15,
        }
      }
    }
  ]
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
//*****************TABLA DASHBOARD**********
//TIEMPO
function enteroTime(tiempo) {
  var hour = Math.floor(tiempo / 3600);
  hour = (hour < 10) ? '0' + hour : hour;
  var minute = Math.floor((tiempo / 60) % 60);
  minute = (minute < 10) ? '0' + minute : minute;
  var second = tiempo % 60;
  second = (second < 10) ? '0' + second : second;
  return hour + ':' + minute + ':' + second;
}
// ÀREA 
$('#area').select2({
  // ajax: {
  //   url: '/areasCR',
  //   dataType: 'json',
  //   processResults: function (data, params) {
  //     return {
  //       results: data,
  //       pagination: {
  //         more: (params.page * 30) < data.total_count
  //       }
  //     };
  //   }
  // },
  placeholder: 'Seleccionar áreas'
});

$('#area').on("change", function (e) {
  empleadosControlRemoto();
});
// FECHA
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
  empleadosControlRemoto();
});
//DATOS PARA TABLA
var datos = {};
function empleadosControlRemoto() {
  var fecha = $("#fechaInput").val();
  var area = $('#area').val();
  $('#empleadosCR').empty();
  $.ajax({
    url: "/empleadoCR",
    method: "GET",
    data: {
      fecha: fecha,
      area: area
    },
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    statusCode: {
      401: function () {
        location.reload();
      },
      // 419: function () {
      //   location.reload();
      // }
    },
    success: function (data) {
      console.log(data);
      datos = data;
      var tr = "";
      for (let index = 0; index < data.length; index++) {
        var nivel;
        if (data[index].division.toFixed(2) >= 50) nivel = "green";
        else if (data[index].division.toFixed(2) > 35) nivel = "#f3c623";
        else nivel = "red";
        tr += "<tr><td class=\"text-center\" style=\"vertical-align: middle;\">" + (index + 1) + "</td><td style=\"vertical-align: middle;\">" + data[index].nombre + " " + data[index].apPaterno + " " + data[index].apMaterno + "</td>\
        <td class=\"text-center\" style=\"vertical-align: middle;\">"+ enteroTime(data[index].tiempoT) + "</td>\
        <td class=\"text-center\" style=\"vertical-align: middle;\"><a class=\"badge badge-soft-primary mr-2\"><img src=\"landing/images/wall-clock (1).svg\" height=\"12\" class=\"mr-2\">" + data[index].inicioA + "</a></td>\
        <td class=\"text-center\" style=\"vertical-align: middle;\"><a class=\"badge badge-soft-primary mr-2\"><img src=\"landing/images/wall-clock (1).svg\" height=\"12\" class=\"mr-2\">" + data[index].ultimaA + "</a></td><td>\
        <div class=\"progress\" style=\"background-color: #d9dee9;font-weight: bold\">\
          <div class=\"progress-bar\" role=\"progressbar\" style=\"width:"+ data[index].division.toFixed(2) + "%;background:" + nivel + "\" aria-valuenow=" + data[index].division.toFixed(2) + " aria-valuemin=\"0\" aria-valuemax=\"100\">" + data[index].division.toFixed(2) + "%\
          </div>\
          </div>\
        </td></tr>";

      }
      $('#empleadosCR').html(tr);
      $("#dashboardEmpleado").DataTable({
        scrollX: true,
        responsive: true,
        retrieve: true,
        "searching": false,
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
  $("#fechaInput").on("change", empleadosControlRemoto);
});

function refreshReporte() {
  resultadoCR();
  myTimer();
  chart.updateOptions({
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
  });
  empleadosControlRemoto();
}