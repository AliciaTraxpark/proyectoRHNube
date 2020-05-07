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
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
</head>
<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">

  <header id="header-section">
    <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
    <div class="container">
        <div class="col-md-3">
            <div class="navbar-brand-wrapper d-flex w-100">
                <img src="{{asset('landing/images/rgh.png')}}" alt="">
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
        <form method="POST" action="{{ route('registerPersona') }}">
            @csrf
            <div class="row">
            <div class="col-md-9">
                <div class="row">

                   <div class="col-md-4">
                     <input  class="form-control " placeholder="Nombres" name="nombres" id="nombres">
                    </div>
                    <div class="col-md-5">
                     <input  class="form-control" placeholder="Apellidos" name="apellidos" id="apellidos">
                    </div> <br><br>
                    <div class="col-md-9">
                         <input  class="form-control " placeholder="Número de celular o correo electrónico" name="email" id="email">
                    </div><br><br>
                    <div class="col-md-9">
                        <input  class="form-control" type="password" placeholder="Contraseña nueva" name="password" id="password">
                   </div><br><br>
                 </div>
         {{--        <div class="row">
                    <div class="col-md-3 mt-2">
                        <label class="normal" for="">Fecha de nacimiento:</label>
                    </div>
                    <div class="col-md-3">
                        <div class="datepicker date input-group p-0 shadow-sm">
                            <input type="text" placeholder="elegir fecha" class="form-control" id="fecha" name="fecha">
                            <div class="input-group-append"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                        </div>
                    </div><!-- DEnd ate Picker Input -->
                    <div class="col-md-12">
                        <label class="normal" for="">Sexo:</label>
                    </div>
                    <div class="col-md-2">
                        <div class="control">
                            <label class="radio normal">
                              <input type="radio" name="sexo">
                              Mujer
                            </label>
                      </div>
                    </div>
                    <div class="col-md-2">
                        <div class="control">
                            <label class="radio normal">
                              <input type="radio" name="sexo">
                             Hombre
                            </label>
                      </div>
                    </div>
                    <div class="col-md-3">
                        <div class="control">
                            <label class="radio normal">
                              <input type="radio" name="sexo">
                             Personalizado
                            </label>
                      </div>
                    </div>
                 </div> --}}
                    <br><br>


                 <br><br>

            </div>
            <div class="container col-md-3">
                <img src="{{asset('landing/images/regisP.svg')}}" alt="" class="img-fluid"><br><br><br><br>
                <div class="col-md-12 text-right">
                    <button class="btn btn-opacity-primary mr-1" id="botonRegistrar">Registrarme</button>
                    </div> <br><br>
            </div>
        </div>
       </form>
        <div class="form-text text-danger" id="mensaje">

        </div>
        <div class="" id="mensajeRegistro"></div>
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
  {{-- <script src="{{asset('landing/js/ValidarRegistrarPersona.js')}}"></script> --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
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
