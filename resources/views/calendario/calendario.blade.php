<!DOCTYPE html>
<html lang="en">
<head>
  <title>Simple landing page</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.carousel.min.css')}}">
  <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.theme.default.css')}}">
  <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
  <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
  <!-- App favicon -->
  <link rel="shortcut icon" href="{{asset('admin/assets/images/favicon.ico')}}">

  <!-- Plugin css -->
  <link href="{{asset('admin/assets/libs/fullcalendar-core/fullcalendar-core.min.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('admin/assets/libs/fullcalendar-daygrid/fullcalendar-daygrid.min.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('admin/assets/libs/fullcalendar-bootstrap/fullcalendar-bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('admin/assets/libs/fullcalendar-timegrid/fullcalendar-timegrid.min.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('admin/assets/libs/fullcalendar-list/fullcalendar-list.min.css')}}" rel="stylesheet" type="text/css" />
  <!-- App css -->
  <link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
</head>
<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">

  <header id="header-section">
    <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
    <div class="container">
        <div class="col-md-2">
            <div class="navbar-brand-wrapper d-flex w-100">
                <img src="{{asset('landing/images/logo.png')}}" height="100" >
              </div>
        </div>
        <div class="col-md-6 text-left">
          <h5 style="color: #ffffff">Gestión de Calendarios</h5>
          <label for="" class="blanco">Calendario de Perú, puedes crear calendarios regionales o personalizados</label>
        </div>
        <div class="col-md-2 text-left">
          <select  class="form-control" placeholder="Departamento " name="departamento" id="departamento" required>
            <option value="">PAIS</option>
          </select>
        </div>
        <div class=" col-md-2">
          <button   type="submit"class="boton">Nuevo</button>
        </div>
    </div>
    </nav>
  </header>

    <div class="content-page">
        <div class="content">
          <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div>
            <!-- end col-12 -->
        </div> <!-- end row -->
        <footer class="border-top">
            <p class="text-center text-muted pt-4">© <?php echo date("Y"); ?> - RH Solution | Todos los derechos reservados.</p>
        </footer>
      </div>
    </div>
  <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
  <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
  <script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
  <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
  <script src="{{asset('landing/js/landingpage.js')}}"></script>
  <script src="{{asset('landing/js/seleccionarDepProv.js')}}"></script>

  <!-- Vendor js -->
  <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>

  <!-- plugin js -->
  <script src="{{asset('admin/assets/libs/moment/moment.min.js')}}"></script>
  <script src="{{asset('admin/assets/libs/fullcalendar-core/fullcalendar-core.min.js')}}"></script>
  <script src="{{asset('admin/assets/libs/fullcalendar-daygrid/fullcalendar-daygrid.min.js')}}"></script>
  <script src="{{asset('admin/assets/libs/fullcalendar-bootstrap/fullcalendar-bootstrap.min.js')}}"></script>
  <script src="{{asset('admin/assets/libs/fullcalendar-timegrid/fullcalendar-timegrid.min.js')}}"></script>
  <script src="{{asset('admin/assets/libs/fullcalendar-list/fullcalendar-list.min.js')}}"></script>
  <script src="{{asset('admin/assets/libs/fullcalendar-interaction/fullcalendar-interaction.min.js')}}"></script>
  <script src="{{asset('admin/assets/libs/fullcalendar-bootstrap/locales/es.js')}}"></script>

  <script>

    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        plugins: [ 'dayGrid','interaction','timeGrid','list','interaction'],
        header:{
          left:'prev,next today',
          center:'title',
          right:'dayGridMonth,timeGridWeek,timeGridDay'
        },
        footer:{
          left:'Descanso',
          right:'NoLaborales'
        },
        customButtons:{
          Descanso:{
            text:"Asignar días de Descanso"
          },
          NoLaborales:{
            text:"Asignar días no Laborales"
          }
        },
        dateClick:function(info){
          console.log(info);
        }

      });
      calendar.setOption('locale',"Es");

      calendar.render();
    });

  </script>
</body>
</html>
