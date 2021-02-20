$(document).ready(function() {

    $("div.informacion").hide();
    $("div.agendar-reunion").hide();
    $("div.partner").hide();
    $("div.login").hide();

    $("a.logo").click(function() {
        $("div.inicio").show(500);
        $("div.agendar-reunion").hide(500);
        $("div.informacion").hide(500);
        $("div.partner").hide(500);
        $("div.login").hide(500);
    });

    $("a.informacion").click(function() {
        $("div.informacion").show(500);
        $("div.agendar-reunion").hide(500);
        $("div.inicio").hide(500);
        $("div.partner").hide(500);
        $("div.login").hide(500);
    });

    $("a.agendar-reunion").click(function() {
        $("div.agendar-reunion").show(500);
        $("div.informacion").hide(500);
        $("div.inicio").hide(500);
        $("div.partner").hide(500);
        $("div.login").hide(500);
    });

    $("a.partner").click(function() {
        $("div.partner").show(500); 
        $("div.informacion").hide(500);
        $("div.inicio").hide(500);
        $("div.agendar-reunion").hide(500);
        $("div.login").hide(500);
    });

    $("a.login").click(function() {
        $("div.login").show(500);
        $("div.partner").hide(500); 
        $("div.informacion").hide(500);
        $("div.inicio").hide(500);
        $("div.agendar-reunion").hide(500);
    });
    $("div#mensajeForm").hide();
    $("#mensajeEmail").hide();
    $("#mensajeDate").hide();
});
var correo = false;
document.getElementById('correo').addEventListener('input', function() {
  let campo = event.target;
  let emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
  //Se muestra un texto a modo de ejemplo, luego va a ser un icono
  if (emailRegex.test(campo.value)) {
    $("#mensajeEmail").hide();
    correo = true;
  } else {
    correo = false;
    $("#mensajeEmail").show();
  }
});

//Bloquear fechas pasadas

//let start = document.getElementById("fecha-hora");
//start.min = (new Date()).toISOString().substr(0, 19);

//Form sgte progress
  var current = 1, current_step, next_step, steps;
  steps = $("fieldset").length;

  $(".next").click(function() {
    current_step = $(this).parent();
    next_step = $(this).parent().next();
    // VALIDAR CADA CAMPO
    
    if($('#nombre_apellidos').val() == "" || $('#telefono').val() == "" || $('#correo').val() == "" || $('#cargo').val() == "" || $('#colaborador').val() == "" || $('#diaReunion').val() == "" || $('#horaReunion').val() == ""){
      $("div#mensajeForm").show();
      if($('#diaReunion').val() == "" || $('#horaReunion').val() == ""){
        $("#mensajeDate").show();
      } else 
        $("#mensajeDate").hide();
    }else {
      $("div#mensajeForm").hide();
      $("#mensajeDate").hide();
      if(correo){
        next_step.show();
        current_step.hide();
        setProgressBar(++current);
        $("img.img-fluid.pb-5.imgResp").attr("style", "padding-top:0px");
      }
    }
  });
  $(".previous").click(function() {
      current_step = $(this).parent();
      next_step = $(this).parent().prev();
      console.log(current_step);
      next_step.show();
      current_step.hide();
      setProgressBar(--current);
      $("img.img-fluid.pb-5.imgResp").attr("style", "padding-top:50px");
  });
  setProgressBar(current);
  // Change progress bar action
  function setProgressBar(curStep) {
      var percent = parseFloat(100 / steps) * curStep;
      percent = percent.toFixed();
      $(".progress-bar")
          .css("width", percent + "%")
          .html(percent + "%");
  }

//Carrousel video
$('#recipeCarousel').carousel({
    interval: 10000
})

$('.carousel .carousel-item').each(function() {
    var minPerSlide = 3;
    var next = $(this).next();
    if (!next.length) {
        next = $(this).siblings(':first');
    }
    next.children(':first-child').clone().appendTo($(this));

    for (var i = 0; i < minPerSlide; i++) {
        next = next.next();
        if (!next.length) {
            next = $(this).siblings(':first');
        }

        next.children(':first-child').clone().appendTo($(this));
    }
});

function validaNumericos(event) {
  if(event.charCode >= 48 && event.charCode <= 57){
    return true;
  }
  return false;        
}

function validaTexto(event) {
  if(event.charCode >= 65 && event.charCode <= 90 || event.charCode >= 97 && event.charCode <= 122 || event.charCode == 32 || event.charCode == 46 || event.charCode == 225 || event.charCode == 233 || event.charCode == 193 || event.charCode == 201 || event.charCode == 205 || event.charCode == 211 || event.charCode == 218 || event.charCode == 237 || event.charCode == 243 || event.charCode == 250){
    return true;
  }
  return false;        
}

function enviarAgenda(){
  var data = $('#regiration_form').serialize();
   $.ajax({
      method: "POST",
      url: '/agendaReunion',
      data: data,
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(e){
          $('#confirmacion_correo').modal('show');
          $('#nombre_apellidos').val("");
          $('#telefono').val("");
          $('#correo').val("");
          $('#empresa').val("");
          $('#cargo').val("");
          $('#colaborador').val("");
          $('#diaReunion').val("");
          $('#horaReunion').val("");
          $('#comentario').val("");
      }
  });
}
let arr = [];
function check(idHora){
  let hora = arr[idHora].inicio;
  let dia = $('#diaAgenda').val();
  $('#horarioDisponibles').modal('hide');
  document.getElementById("diaReunion").value = dia;
  document.getElementById("horaReunion").value = hora;
}

$(document).ready(function() {
  var f = new Date();
  var fecha = f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();
  $.ajax({
    method: "GET",
    url: '/getFechasAgenda',
    data: {day:fecha},
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(e){
        arr = e.slice();
        var html_tr = "";
        let hora = "";
        for (var i = 0; i < e.length; i++) {  
          hora = e[i].inicio;
          html_tr = html_tr + "<tr><td>"+e[i].inicio+" - "+e[i].fin+"</td><td>"+e[i].estado+"</td><td>"+e[i].html+"</td></tr>";
        }
        $("#bodyHorarios").html(html_tr);
    }
  });

  $('#horaAgenda').flatpickr({
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
  });

  $('.diaAgenda').flatpickr({
    dateFormat: "Y-m-d",
    wrap: true,
    altInput: true,
    altFormat: "F j, Y",
    minDate: 'today',
    defaultDate:'today',   
    onChange: function (selectedDates, dateStr, instance) {
      let fechas = dateStr.split('-');
      switch(fechas[1]){
        case '01': fechas[1] = 1; break;
        case '02': fechas[1] = 2; break;
        case '03': fechas[1] = 3; break;
        case '04': fechas[1] = 4; break;
        case '05': fechas[1] = 5; break;
        case '06': fechas[1] = 6; break;
        case '07': fechas[1] = 7; break;
        case '08': fechas[1] = 8; break;
        case '09': fechas[1] = 9; break;
      }
      let dateS = fechas[0]+"-"+fechas[1]+"-"+fechas[2];
      $.ajax({
        method: "GET",
        url: '/getFechasAgenda',
        data: {day:dateS},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(e){
            arr = e.slice();
            var html_tr = "";
            let hora = "";
            for (var i = 0; i < e.length; i++) {  
              hora = e[i].inicio;
              html_tr = html_tr + "<tr><td>"+e[i].inicio+" - "+e[i].fin+"</td><td>"+e[i].estado+"</td><td>"+e[i].html+"</td></tr>";
            }
            $("#bodyHorarios").html(html_tr);
        }
      });
    },  
    locale: {
         firstDayOfWeek: 1,
         weekdays: {
           shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
           longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],         
         }, 
         months: {
           shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
           longhand: ['Enero', 'Febrero', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
         },
       }
  });
});

$('#chatJivo').addClass('enabled').click(function() {
    jivo_api.open();  
});

function cerrarModalAdvertencia(){
  document.getElementById("modal1").style.display = "none";
}

$(function () {
 $('[data-toggle="tooltip"]').tooltip()
 $('.collapse').collapse()
})

function enviarPartner(){
  var data = $('#regirationPartner_form').serialize();
   $.ajax({
    method: "POST",
    url: '/partner',
    data: data,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(e){
      $('#rucP').val('');
      $('#razonSocialP').val('');
      $('#contactoP').val('');
      $('#telefonoP').val('');
      $('#mensajeP').val('');
      $('#correoP').val('');
      $('#registrarPartner').modal('hide');
    }
  });
}




