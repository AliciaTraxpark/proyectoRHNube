<!DOCTYPE html>
<html lang="en">
<head>
  <title>Registrate</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.carousel.min.css')}}">
  <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.theme.default.css')}}">
  <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
  <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
</head>
<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
<style>
    .btn-group-sm>.btn, .btn-sm {
    padding: .25rem .5rem!important;
    font-size: 14px!important;}
</style>
  <header id="header-section">
    <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
    <div class="container">
        <div class="col-md-3">
            <div class="navbar-brand-wrapper d-flex w-100">
                <img src="{{asset('landing/images/logo.png')}}" height="100" >
              </div>
        </div>
        <div class="col-md-9 text-left">
        <h5 style="color: #ffffff">Crear una cuenta</h5>
        <label for="" class="blanco">Tienes 2 minutos</label>

        </div>
    </div>
    </nav>
  </header>

  <div class="content-wrapper">
    <div class="container">
      <section class="features-overview" id="features-section" >
          <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header" style="padding-bottom: 3px; padding-top: 10px;background: #ecebeb">
                            <h5 class="" id="myModalLabel" style="font-size: 14px">Confirma tu fecha de nacimiento</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <label class="col-lg-6 col-form-label" for="simpleinput">Naciste el<input type="text" id="diaN"> de  <input type="text" id="mesN"> de <input type="text" id="anoN"> ?</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" style="background: #f0f0f0" class="btn  btn-sm" data-dismiss="modal">No</button>
                            <button type="button" style="background: #302f56;color: #ecebeb" class="btn btn-sm">Sí</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

        <form method="POST" action="javascript:agregarempleado()">
            {{ csrf_field() }}
            <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-3">
                        <input  class="form-control " placeholder="Nombres" name="nombres" id="nombres" value="{{old ('nombres')}}" required>
                        {{$errors->first('nombres')}}
                       </div>
                    <div class="col-md-3">
                     <input  class="form-control" placeholder="Apellido Paterno" name="apPaterno" id="apPaterno" value="{{old ('apellidos')}}" required>
                     {{$errors->first('apellidos')}}
                    </div>
                    <div class="col-md-3">
                      <input  class="form-control" placeholder="Apellido Materno" name="apMaterno" id="apMaterno" value="{{old ('apellidos')}}" required>
                      {{$errors->first('apellidos')}}
                     </div> <br><br>

                     <div class="col-md-9">
                      <input  class="form-control " placeholder="Direccion" name="direccion" id="direccion" value="{{old ('direccion')}}" required>
                      {{$errors->first('direccion')}}
                     </div><br><br>
                    <div class="col-md-9">
                         <input  class="form-control " placeholder="Número de celular o correo electrónico" name="email" id="email" value="{{old ('email')}}" required>
                         {{$errors->first('email')}}
                        </div><br><br>
                    <div class="col-md-9">
                        <input  class="form-control" type="password" placeholder="Contraseña nueva" name="password" id="password" value="{{old ('password')}}" required>
                        {{$errors->first('password')}}
                    </div><br><br>
                 </div>
                     <div class="row">
                    <div class="col-md-9 mt-2">
                        <label class="normal" for="">Fecha de nacimiento:</label>
                    </div>
                    <div class="col-md-9">
                       {{--  <div class="datepicker date input-group p-0 shadow-sm">
                            <input type="text" placeholder="elegir fecha" class="form-control" id="fecha" name="fecha" value="{{old ('fecha')}}" required>
                            {{$errors->first('fecha')}}
                            <div class="input-group-append"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                        </div> --}}
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-control"  name="dia_fecha" id="dia_fecha" required="">
                                    <option value="">Dia</option>
                                    @for ($i = 1; $i < 32; $i++)
                                    <option class="" value="{{$i}}">{{$i}}</option>
                                    @endfor
                                 </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control"  name="mes_fecha" id="mes_fecha" required="">
                                    <option value="">Mes</option>
                                    <option class="" value="1">Enero</option>
                                    <option class="" value="2">Febrero</option>
                                    <option class="" value="3">Marzo</option>
                                    <option class="" value="4">Abril</option>
                                    <option class="" value="5">Mayo</option>
                                    <option class="" value="6">Junio</option>
                                    <option class="" value="7">Julio</option>
                                    <option class="" value="8">Agosto</option>
                                    <option class="" value="9">Setiembre</option>
                                    <option class="" value="9">Octubre</option>
                                    <option class="" value="9">Noviembre</option>
                                    <option class="" value="9">Diciembre</option>
                                 </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control"  name="ano_fecha" id="ano_fecha" required="">
                                    <option value="">Año</option>
                                    @for ($i = 1920; $i < 2011; $i++)
                                    <option class="" value="{{$i}}">{{$i}}</option>
                                    @endfor
                                 </select>
                            </div>

                        </div>

                    </div>
                    <div class="col-md-12">
                        <label class="normal" for="">Sexo:</label>
                    </div>
                    <div class="col-md-2">
                        <div class="control">
                            <label class="radio normal">
                              <input type="radio" name="sexo" value="Mujer" required>
                              Mujer
                            </label>
                      </div>
                    </div>
                    <div class="col-md-2">
                        <div class="control">
                            <label class="radio normal">
                              <input type="radio" name="sexo" value="Hombre" required>
                             Hombre
                            </label>
                      </div>
                    </div>
                    <div class="col-md-3">
                        <div class="control" >
                            <label class="radio normal" data-toggle="tooltip"
                            data-placement="right" title=""
                            data-original-title="Puedes elegir personalizado si no deseas especificar tu sexo.">
                              <input type="radio" name="sexo" value="Personalizado"  required>
                             Personalizado
                            </label>
                      </div>
                    </div>

                 </div>
                    <br><br>


                 <br><br>

            </div>
            <div class="container col-md-3">
                <img src="{{asset('landing/images/career.gif')}}" alt="" class="img-fluid"><br><br><br><br>
                <div class="col-md-12 text-center">

                    <button type="submit"  class="btn btn-opacity-primary mr-1" >Registrarme</button>
                    </div> <br><br>
            </div>
        </div>
       </form>
      </section>



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
  <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script>
  <!-- App js -->
  <script src="{{asset('admin/assets/js/app.min.js')}}"></script>

  <script src="{{asset('landing/js/ValidarRegistrarPersona.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
     $( document ).ready(function() {

 })
</script>
<script>
    $(function () {

// INITIALIZE DATEPICKER PLUGIN
$('.datepicker').datepicker({
    clearBtn: true,
    format: "dd/mm/yyyy",
    locale: 'es-es',
});


// FOR DEMO PURPOSE
$('#fecha').on('change', function () {
    var pickedDate = $('input').val();
    $('#pickedDate').html(pickedDate);
});
});
</script>
</body>
</html>
