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

  <!-- Plugin css  CALENDAR-->
  <link href="{{asset('admin/packages/core/main.css')}}" rel="stylesheet" />
<link href="{{asset('admin/packages/daygrid/main.css')}}" rel="stylesheet" />
<link href="{{asset('admin/packages/timegrid/main.css')}}" rel="stylesheet" />
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

    <div class="content-page" style="margin-top: 40px; margin-left: 55px; margin-right: 55px;">
        <div class="content">
            <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel">Modal Heading</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6>Text in a modal</h6>
                            <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>
                            <hr>
                            <div class="form-group">
                                <label for="start" class="col-sm-2 control-label">Fecha Inicial</label>
                                <div class="col-sm-10">
                                  <input type="text" name="start" class="form-control" id="start" readonly>
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="end" class="col-sm-2 control-label">Fecha Final</label>
                                <div class="col-sm-10">
                                  <input type="text" name="end" class="form-control" id="end" readonly>
                                </div>
                              </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="row " >
                <div class="col-md-12 text-center">
                  <div class="col-md-8" style="left: 17.5%">
                      <div class="card">
                          <div class="card-body">
                              <div id="calendar"></div>
                          </div> <!-- end card body-->
                          <div class="card-footer">
                            <div class="row">
                              <div class="col-md-4 text-center">
                                <div id='external-events'>
                                  <div class='fc-event'>Asignar Días de Descanso</div>
                                </div>
                              </div>
                              <div class="col-md-4 text-center">
                                <div id='external-events'>
                                  <div class='fc-event'>Asignar Días no Laborales</div>
                                </div>
                              </div>
                            </div>
                          </div>
                      </div> <!-- end card -->
                  </div>

                </div>
            </div>
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
  <script src="{{asset('admin/packages/core/main.js')}}"></script>
  <script src="{{asset('admin/packages/core/locales/es.js')}}"></script>

  <script src="{{asset('admin/packages/daygrid/main.js')}}"></script>
  <script src="{{asset('admin/packages/timegrid/main.js')}}"></script>
  <script src="{{asset('admin/packages/interaction/main.js')}}"></script>
  <script>

    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      var fecha = new Date();
      var ano = fecha. getFullYear();
      var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        defaultDate: ano+'-01-01',

        plugins: [ 'dayGrid','interaction','timeGrid'],
        navLinks: true, // can click day/week names to navigate views
        selectable: true,
        selectMirror: true,

        select: function(arg) {
            calendar.addEvent({

            start: arg.start,
            end: arg.endStr,

          })
                $('#myModal #start').val(moment(start));

				$('#end').val(arg.endStr -1);
                $('#myModal').modal('show');
                console.log(arg);
			},
       
      editable: true,
      eventLimit: true,
        header:{
          left:'prev,next today',
          center:'title',
          right:'dayGridMonth'
        },
        footer:{
          left:'Descanso',
          right:'NoLaborales'
        },
        customButtons:{
          Descanso:{
            text:"Asignar días de Descanso",
            click:function(){
                $('#myModal').modal('toggle');

            }
          },
          NoLaborales:{
            text:"Asignar días no Laborales"
          }
        },
        dateClick:function(info){
            $('#myModal').modal('toggle');
          console.log(info);
        }

      });
      calendar.setOption('locale',"Es");

      calendar.render();
    });

  </script>
</body>
</html>
