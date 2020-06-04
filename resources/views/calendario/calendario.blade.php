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
  <meta name="csrf-token" content="{{ csrf_token() }}">
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

<style>
    .fc-time{
        display: none;
    }
</style>

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
            <label for="" class="blanco font-italic">Calendario de Perú, puedes crear calendarios regionales o personalizados</label>
            </div>
            <div class="col-md-2 text-left">
            <select  class="form-control" placeholder="pais" name="pais" id="pais">
                <option value="">PAIS</option>
                    @foreach ($pais as $paises)
                        @if($paises->id==173)
                            <option class="" selected="true" value="{{$paises->id}}" >{{$paises->nombre}}</option>
                        @else
                            <option class="" value="{{$paises->id}}">{{$paises->nombre}}</option>
                        @endif
                    @endforeach
            </select>
            </div>
            <div class="col-md-2 text-left">
                @if(!empty($eventos_usuario))
                    <h1>sdfg</h1>
                @endif
                <select  class="form-control" placeholder="Departamento " name="departamento" id="departamento" style="display: flex;">
                <option value="">DEPARTAMENTO</option>
                @foreach ($departamento as $departamentos)
                    <option class="" value="{{$departamentos->id}}">{{$departamentos->name}}</option>
                @endforeach
            </select>
            </div>
            <div class=" col-md-2">
            <button  id="nuevoCalendario" type="submit"class="boton" >Nuevo</button>
            </div>
        </div>
    </nav>
  </header>

    <div class="content-page" style="margin-top: 40px; margin-left: 55px; margin-right: 55px;">
        <div class="content">
            <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #163552;">
                            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días de descanso</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">


                                <h5>¿Asignar dias de descanso?</h5>

                                        {{-- <label for="start" class="col-sm-4 col-form-label">Fecha Inicial:</label> --}}

                                                <input type="hidden" name="start" class="form-control" id="start" readonly>



                                        {{-- <label for="start" class="col-sm-4 col-form-label">Fecha Final:</label> --}}

                                            <input type="hidden" name="end" class="form-control" id="end" readonly>


                                    <input type="hidden" name="title" id="title" value="Descanso">

                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-7 text-right">
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                                    </div>
                                    <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                                        <button type="button" id="guardarDescanso" name="guardarDescanso" class="btn btn-secondary">Confirmar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div id="myModalFestivo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #163552;">
                            <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días no laborales</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                                <h5>¿Asignar dias no laborales?</h5>

                                        {{-- <label for="start" class="col-sm-4 col-form-label">Fecha Inicial:</label> --}}

                                            <input type="hidden" name="startF" class="form-control" id="startF" readonly>

                                        {{-- <label for="start" class="col-sm-4 col-form-label">Fecha Final:</label> --}}

                                            <input type="hidden" name="endF" class="form-control" id="endF" readonly>

                                <input type="hidden" name="titleN" id="titleN" value="No laborable">

                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-7 text-right">
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                                    </div>
                                    <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                                        <button type="button" id="guardarNoLab" name="guardarNoLab" class="btn btn-secondary">Confirmar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div id="myModalEliminarD" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header" style="background-color:#163552;">
                          <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días de descanso</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                      <div class="modal-body">



                                  <form class="form-horizontal">
                                    <h5 class="modal-title" id="myModalLabel">¿Desea eliminar días descanso?</h5>
                                  </form>


                      </div>
                      <div class="modal-footer">
                          <div class="col-md-12">
                              <div class="row">
                                  <div class="col-md-7 text-right">
                                      <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                                  </div>
                                  <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                                      <button type="button" id="eliminarDescanso" name="eliminarDescanso" style="background-color: #163552;" class="btn ">Eliminar</button>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div id="myModalEliminarN" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días no Laborales</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">



                                <form class="form-horizontal">
                                  <h5 class="modal-title" id="myModalLabel">¿Desea eliminar días no Laborales?</h5>
                                </form>


                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-7 text-right">
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                                </div>
                                <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                                    <button type="button" id="eliminarNLaboral" name="eliminarNLaboral" style="background-color: #163552;" class="btn ">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div id="myModalEliminarDdep" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #163552;">
                        <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días de descanso</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                                <form class="form-horizontal">
                                  <h5 class="modal-title" id="myModalLabel">¿Desea eliminar días descanso?</h5>
                                </form>

                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-7 text-right">
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                                </div>
                                <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                                    <button type="button"  style="background-color: #163552;" id="eliminarDescansodep" name="eliminarDescansodep" class="btn ">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div id="myModalEliminarNdep" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header" style="background-color: #163552;">
                      <h5 class="modal-title" id="myModalLabel" style="color:#ffffff;font-size:15px">Días no Laborales</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                              <form class="form-horizontal">
                                <h5 class="modal-title" id="myModalLabel">¿Desea eliminar días no Laborales?</h5>
                              </form>
                  </div>
                  <div class="modal-footer">
                      <div class="col-md-12">
                          <div class="row">
                              <div class="col-md-7 text-right">
                                  <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                              </div>
                              <div class="col-md-5 text-right" style="padding-right: 38px;  ">
                                  <button type="button" id="eliminarNLaboraldep" name="eliminarNLaboraldep" style="background-color: #163552;" class="btn ">Eliminar</button>
                              </div>
                          </div>
                      </div>
                  </div>
              </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="row " >

                <div class="col-md-12 text-center">
                  <div class="col-md-7" style="left: 10%;max-width: 80%; " id="Datoscalendar">
                      <div class="card">
                          <div class="card-body">
                              <div id="calendar">
                                  
                              </div>

                          </div> <!-- end card body-->
                          <div class="card-footer">
                            <div class="row">
                            </div>
                          </div>
                      </div> <!-- end card -->
                  </div>
                  <div class="col-md-7" id="Datoscalendar1" style="left: 10%;max-width: 80%;">
                    <div class="card">
                        <div class="card-body">
                            <div id="calendar1"></div>

                        </div> <!-- end card body-->
                        <div class="card-footer">
                          <div class="row">
                          </div>
                        </div>
                    </div> <!-- end card -->
                </div>
                   <input type="hidden" id="pruebaStar">
                   <input type="hidden" id="pruebaEnd">
                </div>

            </div>
            <div class="row">
                <div class="col-md-12  text-right">
                    <a href="{{('/empleado')}}"><button class="boton btn btn-default mr-1" >CONTINUAR</button></a>
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
  <script src="{{asset('landing/js/SeleccionarPais.js')}}"></script>

  <!-- Vendor js -->
  <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>

  <!-- plugin js -->
  <script src="{{asset('admin/assets/libs/moment/moment.min.js')}}"></script>
  <script src="{{asset('admin/packages/core/main.js')}}"></script>
  <script src="{{asset('admin/packages/core/locales/es.js')}}"></script>

  <script src="{{asset('admin/packages/daygrid/main.js')}}"></script>
  <script src="{{asset('admin/packages/timegrid/main.js')}}"></script>
  <script src="{{asset('admin/packages/interaction/main.js')}}"></script>
   <script src="{{asset('landing/js/calendario.js')}}"></script>
  <script>



  </script>

</body>
</html>
