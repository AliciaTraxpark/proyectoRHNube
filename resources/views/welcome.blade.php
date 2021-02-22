<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>RH nube</title>

      <link rel="stylesheet" href="{{ asset('landing/home/css/lib/bootstrap.min.css') }}">
      <link rel="stylesheet" href="{{ asset('landing/home/css/lib/fontawesome-all.css') }}">
      <link rel="stylesheet" href="{{ asset('landing/home/css/style.css') }}">
      <link rel="shortcut icon" href="{{ asset('landing/home/images/Logo.png') }}">
      <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
      <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
   </head>
   <body>
      <div class="div-general">
         <nav class="navbar-1 navbar navbar-expand-lg navbar-dark bg-dark">
         </nav>
         <nav class="navbar-2 navbar navbar-expand-lg navbar-dark">
            <div class="navbar-collapse collapse w-100 dual-collapse2 order-1 order-md-0">
               <ul class="navbar-nav ml-auto text-center">
                  <li class="nav-item active">
                     <a class="nav-link informacion" href="#informacion">INFORMACIÓN</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link partner" href="#partner">PARTNER</a>
                  </li>
               </ul>
            </div>
            <div class="mx-auto my-2 order-0 order-md-1 position-relative">
               <a class="mx-auto logo" href="#inicio">
                  <img src="{{ asset('/landing/images/logo_animado.gif') }}" class="rounded-circle img-fluid imgResp logo" width="50" style="width: 250px !important;">
               </a>
               <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".dual-collapse2">
               <span class="navbar-toggler-icon"></span>
               </button>
            </div>
            <div class="navbar-collapse collapse w-100 dual-collapse2 order-2 order-md-2">
               <ul class="navbar-nav mr-auto text-center">
                  <li class="nav-item">
                     <a class="nav-link agendar-reunion" href="#agendar-reunion">AGENDAR UNA REUNIÓN</a>
                  </li>
                  <li class="nav-item" style="display: grid; align-items: center;">
                     <!--<a class="nav-link" target="_blank" download="Brochure RH nube" href="documentacion/BrochureRHnube .pdf">BROCHURE</a>-->
                     <!-- <a class="nav-link login" href="#login">LOGIN</a> -->
                     <a class="nav-link dropdown-toggle"  data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">LOGIN</a>
                      <div class="dropdown-menu justify-content-center" style="border-color: white;">
                       <form class="px-4 py-3" method="POST" action="{{ route('login') }}" >
                        @csrf
                         <div class="form-group">
                           <input id="email" class="form-control form-control-sm @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Correo electrónico" style="padding: 21px 16px;">
                           @error('email')
                             <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                             </span>
                           @enderror
                         </div>
                         <div class="form-group">
                           <input tid="password" type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Contraseña" style="padding: 21px 16px">
                           @error('password')
                             <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                             </span>
                           @enderror
                         </div>
                         <button type="submit" class="btn btn-rh btn-block">Iniciar Sesión</button>
                       </form>
                       <div class="dropdown-divider"></div>
                       <a class="dropdown-item text-center" href="{{ route('password.request') }}" style="font-size: 13px">¿Olvidaste tu contraseña?</a>
                     </div>
                  </li>
               </ul>
            </div>
         </nav>
      </div>
      <ul class="nav justify-content-center">
         <li class="nav-item general">
            <a class="nav-link active pc" href="#">PC</a>
         </li>
         <li class="nav-item general">
            <a class="nav-link movil" href="#">Móvil</a>
         </li>
      </ul>
      <div class="vc_empty_space" style="height: 50px"><span class="vc_empty_space_inner"></span></div>
      <div class="bg img-fluid"></div>
      <div class="vc_empty_space" style="height: 50px"><span class="vc_empty_space_inner"></span></div>
      <div class="container-fluid inicio p-10 text-center my-3" id="inicio">
         <div class="row mx-auto my-auto">
            <div id="recipeCarousel" class="carousel slide w-100" data-ride="carousel">
               <div class="carousel-inner w-100" role="listbox">
                  <div class="carousel-item active">
                     <div class="col-md-4">
                        <div class="card card-body">
                           <h5 class="h5 text-center">¿Qué es Modo Control Remoto?</h5>
                           <div class="embed-responsive embed-responsive-16by9">
                              <iframe class="embed-responsive-item" src="https://player.vimeo.com/video/512111026?byline=0&portrait=0&title=0" allowfullscreen></iframe>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="carousel-item">
                     <div class="col-md-4">
                        <div class="card card-body">
                           <h5 class="h5 text-center">¿Qué es Modo Control en Ruta?</h5>
                           <div class="embed-responsive embed-responsive-16by9">
                              <iframe class="embed-responsive-item" src="https://player.vimeo.com/video/512111206?byline=0&portrait=0&title=0" allowfullscreen></iframe>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="carousel-item">
                     <div class="col-md-4">
                        <div class="card card-body">
                           <h5 class="h5 text-center">¿Qué es Modo Asistencia en Puerta?</h5>
                           <div class="embed-responsive embed-responsive-16by9">
                              <iframe class="embed-responsive-item" src="https://player.vimeo.com/video/514307168?byline=0&portrait=0&title=0" allowfullscreen></iframe>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="carousel-item">
                     <div class="col-md-4">
                        <div class="card card-body">
                           <h5 class="h5 text-center">¿Qué es Modo Control de Tareo?</h5>
                           <div class="embed-responsive embed-responsive-16by9">
                              <iframe class="embed-responsive-item" src="https://player.vimeo.com/video/512111271?byline=0&portrait=0&title=0" allowfullscreen></iframe>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <a class="carousel-control-prev w-auto" href="#recipeCarousel" role="button" data-slide="prev">
               <span class="carousel-control-prev-icon bg-dark border border-dark rounded-circle" aria-hidden="true"></span>
               <span class="sr-only">Previous</span>
               </a>
               <a class="carousel-control-next w-auto" href="#recipeCarousel" role="button" data-slide="next">
               <span class="carousel-control-next-icon bg-dark border border-dark rounded-circle" aria-hidden="true"></span>
               <span class="sr-only">Next</span>
               </a>
            </div>
         </div>
      </div>
      <div class="container-fluid inicio p-10">
         <div class="row">
            <div class="col-sm-12 col-lg-4">
            </div>
            <div class="col-sm-12 col-lg-4">
               <div class="col text-center">
                  <div class="vc_empty_space" style="height: 40px"><span class="vc_empty_space_inner"></span></div>
                  <a href="{{route('registroPersona')}}" class="btn boton-enviar btn-default">¡Registrate en RH nube <b>GRATIS!</b> 
                     <img class="imagen-personalizada" src="{{ asset('landing/home/images/recurso_flecha.png') }}">
                  </a>
               </div>
            </div>
            <div class="col-sm-12 col-lg-4">
               <div class="vc_empty_space" style="height: 40px"><span class="vc_empty_space_inner"></span></div>
               <div class="col text-center">
                  <a href="#" id="chatJivo">
                     <span>
                        Chatea con nosotros
                        <picture>
                           <img src="{{ asset('landing/home/images/Recurso 1@3x.png') }}" width="40px" class="preguntas img-fluid img-thumbnail" alt="...">
                        </picture>
                     </span>
                  </a>
               </div>
               <div class="col text-center">
                  <a href="{{route('registroPersona')}}">
                     <span>
                        Regístrate
                        <picture>
                           <img src="{{ asset('landing/home/images/Recurso 1@2x.png') }}" width="40px" class="preguntas img-fluid img-thumbnail" alt="...">
                        </picture>
                     </span>
                  </a>
               </div>
               <div class="col text-center">
                  <span>
                     <a  href="https://rhnube.com.pe/files/BrochureRHnube .pdf" target="_blank" download="BrochureRHnube.pdf">
                        Nuestro brochure
                        <picture>
                           <img src="{{ asset('landing/home/images/Recurso 1@1x.png') }}" width="40px" class="preguntas img-fluid img-thumbnail" alt="...">
                        </picture>
                     </a>
                  </span>
                  <span>
                     <picture>
                        <img src="{{ asset('landing/home/images/Recurso 1@5x.png') }}" width="40px" class="preguntas img-fluid img-thumbnail" style="    width: 60px;
                           margin-top: -40px;" alt="...">
                     </picture>
                  </span>
               </div>
            </div>
         </div>
         <div class="vc_empty_space" style="height: 40px"><span class="vc_empty_space_inner"></span></div>
      </div>
      <div id="informacion" class="container informacion">
         <div id="accordion">
            <div class="card">
               <div class="card-header" id="headingOne">
                  <h5 class="mb-0">
                     <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                     Información
                     </button>
                  </h5>
               </div>
               <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                  <div class="card-body">
                     Finalmente hemos creado una plataforma única que permite administrar personal en sus 360° grados con una inversión increiblemente baja y con una facilidad increíble.
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header" id="headingTwo">
                  <h5 class="mb-0">
                     <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                     ¿A quién va dirigido?
                     </button>
                  </h5>
               </div>
               <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                  <div class="card-body">
                     RH nube está dirigida a todo empleador tipo empresa, gobierno, ong, club y cualquier organización o grupo que requiera administrar personal y optimizar sus costos e inversión de tiempo.
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header" id="headingThree">
                  <h5 class="mb-0">
                     <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                     ¿Es legal?
                     </button>
                  </h5>
               </div>
               <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                  <div class="card-body">
                     En el Perú los empleadores deben mantener un registro manual o digital, en este caso RH nube es totalmente digital.
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header" id="headingFour">
                  <h5 class="mb-0">
                     <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                     ¿Por cuánto tiempo debo mantener el registro?
                     </button>
                  </h5>
               </div>
               <div id="collapseFour" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                  <div class="card-body">
                     En el Perú el empleador está obligado a manejar el registro de asistencia de cada uno de sus trabajadores por un periodo de 5 años.
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header" id="headingFive">
                  <h5 class="mb-0">
                     <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                     ¿Qué seguridad tiene?
                     </button>
                  </h5>
               </div>
               <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion">
                  <div class="card-body">
                     La información es almacenada en un estado de USA y la seguridad se rige en base a las estrictas leyes americanas, además de garantizar la privacidad de las personas y organizaciones.
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header" id="headingSix">
                  <h5 class="mb-0">
                     <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                     ¿Qué medidas de seguridad se toman?
                     </button>
                  </h5>
               </div>
               <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordion">
                  <div class="card-body">
                     <ul class="list-group">
                        <li class="list-group-item">Las conexiones son seguras: Están cifradas mediante conexiones SSL.</li>
                        <li class="list-group-item">Aumentamos la seguridad a través de la generación de TOKEN's digitales.</li>
                        <li class="list-group-item">Los empleadores tienen su información a salvo, almacenada en servidores seguros en USA o JAPÓN según sea el caso.</li>
                        <li class="list-group-item">Realizamos backups diarios ante una posible falla de los discos sólidos.</li>
                     </ul>
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header" id="headingSeven">
                  <h5 class="mb-0">
                     <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                     ¿Cómo puedo adquirir un servicio de RH nube?
                     </button>
                  </h5>
               </div>
               <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion">
                  <div class="card-body">
                     <ul class="list-group">
                        <li class="list-group-item"><span>Opción 1:</span> Puedes registrarte de manera gratuita en la plataforma y un consultor asignado tomará contacto a tu correo y número corporativo para ayudarte a configurar tu cuenta.</li>
                        <li class="list-group-item"><span>Opción 2:</span> Agendar una reunión en la página principal</li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
         <div class="vc_empty_space" style="height: 50px"><span class="vc_empty_space_inner"></span></div>
      </div>
      <div id="partner" class="container partner">
         <div class="row">
            <div class="col-sm-12 col-lg-12">
               El Programa de Partnership de RH nube es para aquellas compañías que se encuentran en la actividad de brindar servicios de administración de personal, suministro de tecnología y software para control de personal. Si crees que RH nube puede ayudarte a crear valor agregado a tus clientes y brindarles así un mejor servicio. ¡Este programa es para tí!
               <br>
               Como Partner, tendrás acceso a los siguientes beneficios:
               <ul>
                  <li>Programa de distribución de la plataforma</li>
                  <li>Uso de la plataforma para la administración de tu personal (1 administrador por 1 empresa)</li>
                  <li>Formación y soporte continuo del uso y beneficios de la plataforma.</li>
                  <li>Prioridad en soporte</li>
                  <li>Pruebas de concepto y recepción de requerimientos nuevos</li>
               </ul>
               <h5><b>Tipos de partnert</b></h5>
               <ul>
                  <li>Partnert - Integrator: Compañías dedicadas a suministrar equipamiento biométrico y software de control de asistencia</li>
                  <li>Partnert - HR: Compañías que brindan servicio de gestión de planillas y/o administración de personal.</li>
                  <li>Partnert - Payroll: Compañías que brindan el software de control de planas o planillas de pago</li>
               </ul>
               <button class="btn boton-enviar btn-default" data-toggle="modal" data-target="#registrarPartner">CALIFICAR COMO <b>PARTNERT</b></button>
            </div>
         </div>
         <div class="vc_empty_space" style="height: 30px"><span class="vc_empty_space_inner"></span></div>
      </div>
      <div class="container agendar-reunion" id="agendar-reunion">
         <div class="row">
            <div class="col-sm-12 col-lg-6">
               <div class="progress">
                  <div class="progress-bar progress-bar-striped progress-bar-animated active" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
               </div>
               <div class="vc_empty_space" style="height: 30px"><span class="vc_empty_space_inner"></span></div>
               <div id="mensajeForm" class="alertas"><p>• Rellena todos los campos.</p></div>
               <form id="regiration_form" action="javascript:enviarAgenda()"  method="post">
                  @csrf
                  <fieldset class="colum-1">
                     <div class="form-group">
                        <input type="text" class="form-control" id="nombre_apellidos" name="nombre_apellidos" placeholder="Nombres y apellidos" onkeypress='return validaTexto(event)' required="">
                     </div>
                     <div class="form-group">
                        <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Teléfono" onkeypress='return validaNumericos(event)' required="">
                     </div>
                     <div class="form-group">
                        <span id="mensajeEmail" class="alertas"><p>• Ingresa un correo válido.</p></span>
                        <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo Electrónico" required="">
                     </div>
                     <div class="form-group">
                        <input type="text" class="form-control" id="empresa" name="empresa" placeholder="Empresa" required="">
                     </div>
                     <div class="form-group">
                        <input type="text" class="form-control" id="cargo" name="cargo" placeholder="Cargo" required="">
                     </div>
                     <div class="form-group">
                        <input type="number" class="form-control" id="colaborador" name="colaborador" placeholder="Cantidad de colaboradores" required="">
                     </div>
                     <div class="form-group">
                        <label>Fecha y hora<span id="mensajeDate" class="alertas"><p>• Elige un horario.</p></span></label>
                            <div class="form-row">
                               <div class="form-group col-6 col-sm-4">
                                 <input type="text" id="diaReunion" name="diaReunion" class="form-control form-control-sm"  readonly placeholder="Fecha">
                               </div>
                               <div class="form-group col-6 col-sm-4">
                                 <input type="text" id="horaReunion" name="horaReunion" class="form-control form-control-sm" readonly placeholder="Hora">
                               </div>
                               <div class="form-group col-12 col-sm-4">
                                 <button type="button" class="btn btn-sm btn-second-rh btn-block" data-toggle="modal" data-target="#horarioDisponibles"> <img src="landing/images/eyewhite.png" width="18"> Ver horarios</button>
                               </div>
                             </div>
                     </div>
                     <input type="button" id="gts" class="next btn btn-info" value="Siguiente" />
                     <div class="vc_empty_space" style="height: 50px"><span class="vc_empty_space_inner"></span></div>
                  </fieldset>
                  <fieldset class="colum-2">
                     <div class="form-group">
                        <label>Déjame tu comentario</label>
                        <textarea  class="form-control" name="comentario" id="comentario" placeholder="Comentario" required=""></textarea>
                     </div>
                     <input type="button" name="previous" class="previous btn btn-default" value="Anterior" />
                     <input type="submit" name="submit" class="submit btn btn-second-rh" value="Enviar" />
                     <div class="vc_empty_space" style="height: 50px"><span class="vc_empty_space_inner"></span></div>
                  </fieldset>
               </form>
            </div>
            <div class="col-sm-12 col-lg-6">
               <img src="https://rhnube.com.pe/landing/images/career.gif" alt="" class="img-fluid pb-5 imgResp">
            </div>
         </div>
      </div>
      <div class="container login text-center" id="login">
         
      </div>

      <!-- MODAL FECHAS DISPONIBLES -->
      <div class="modal fade" id="horarioDisponibles" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="padding-bottom:3px;padding-top:10px;background: #163552;color: #f8f9fa">
              <h5 class="modal-title" style="font-size:14px">Fechas de reuniones</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
               <div class="d-flex justify-content-center">
                  <div class="form-inline diaAgenda ">
                      <label for="diaAgenda">Dia:&nbsp;</label>
                      
                      <input type="text" id="diaAgenda" class="form-control form-control-sm ml-1" data-input> 
                      <a class="btn btn-light" data-toggle="tooltip" data-placement="top"  title="Elige una fecha" data-toggle>
                          <img src="landing/images/calendarioA.svg" width="22" >
                      </a>
                  </div>
               </div>
               </div>
               <table id="horarios" class="table nowrap" style="font-size: 13px!important;width: 100%;">
                  <thead style="background: #fafafa;" id="diasMensual">
                      <tr>
                          <th>Horario</th>
                          <th>Estado</th>
                          <th>Acción</th>
                      </tr>
                  </thead>
                  <tbody id="bodyHorarios">
                  </tbody>
               </table>
            </div>
          </div>
        </div>
      </div>
      <!-- FIN MODAL-->
      {{-- MODAL CONFIRMACIÓN DE AGENDA REUNIÓN --}}
       <div class="modal" tabindex="-1" id="confirmacion_correo">
         <div class="modal-dialog">
           <div class="modal-content">
             <div class="modal-header">
               <h5 class="modal-title">¡Correo enviado con éxito!</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
               </button>
             </div>
             <div class="modal-body">
               <p>Gracias por tu tiempo, hoy me pondré en contacto contigo.</p>
             </div>
             <div class="modal-footer">
               <button type="button" data-dismiss="modal" class="btn btn-sm" style="background: #163552; color: #ecebeb">Ok</button>
             </div>
           </div>
         </div>
       </div>
      {{-- FINALIZAR MODAL --}}
      
      </div>
      @if (session('error'))
       <div class="modal" id="modal1" role="dialog" style="display:block;">
           <div class="modal-dialog" role="document">
               <div class="modal-content">
                   <div class="modal-header text-center"
                       style="padding-top: 8px;padding-bottom: 5px;background-color:#163552;color:#ffffff">
                       <h6 style="font-size: 14px" class="modal-title">Advertencia</h6>
                   </div>
                   <div class="modal-body text-center">
                       <img src="{{asset('landing/images/alert.svg')}}" height="25" class="mr-1 mt-2">
                       <p>{{ session('error') }}</p>
                   </div>
                   <div class="modal-footer text-center" style="padding-top: 5px;padding-bottom: 5px;">
                       <button type="button" onclick="cerrarModalAdvertencia()" class="btn btn-sm" style="background-color:#163552;color:#ffffff" data-dismiss="modal">
                        OK
                       </button>
                   </div>
               </div>
           </div>
       </div>
       @endif


      <div class="modal fade" id="registrarPartner" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Calificar como Partner</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
               <form id="regirationPartner_form" action="javascript:enviarPartner()"  method="post">
                  @csrf
                  <div class="form-group">
                     <input type="text" class="form-control form-control-sm" id="rucP" name="rucP" onkeypress='return validaNumericos(event)' placeholder="RUC" required="">
                  </div>
                  <div class="form-group">
                      <input type="text" class="form-control form-control-sm" id="razonSocialP" name="razonSocialP" placeholder="Razón Social" required="">
                  </div>
                  <div class="form-group">
                      <input type="text" class="form-control form-control-sm" id="contactoP" name="contactoP" placeholder="Contacto" required="">
                  </div>
                  <div class="form-group">
                      <input type="email" class="form-control form-control-sm" id="correoP" name="correoP" placeholder="Correo electrónico" required="">
                  </div>
                  <div class="form-group">
                      <input type="text" class="form-control form-control-sm" id="telefonoP" name="telefonoP" onkeypress='return validaNumericos(event)' placeholder="Teléfono" required="">
                  </div>
                  <div class="form-group">
                      <textarea  class="form-control form-control-sm" name="mensajeP" id="mensajeP" placeholder="Mensaje" rows="2"></textarea>
                  </div> 
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-rh">Enviar</button>
                </div>
               </form>
            </div>
          </div>
        </div>
      </div>


       <footer class="text-center">
         <div>
            <span>© 2021 - RH nube Corp - USA | Todos los derechos reservados &nbsp; |</span>
            <a style="color:#faf3f3;" href="/politicas">Política de privacidad | </a>
            <span>Central Perú: <a style="color:#faf3f3;" href="tel:017482415">017482415</a> | <a style="color:#faf3f3;" href="mailto:info@rhnube.com.pe">info@rhnube.com.pe</a></span>
         </div>
      </footer>

      <script src="{{ asset('landing/home/js/lib/jquery-3.4.1.min.js') }}"></script>
      <script src="{{ asset('landing/home/js/lib/popper.min.js') }}"></script>
      <script src="{{ asset('landing/home/js/lib/bootstrap.min.js') }}"></script>
      <script src="{{ asset('landing/home/js/script.js') }}"></script>
      <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
      <script src="//code.jivosite.com/widget/OqxplJ3nCh" async></script>
      <script src="https://player.vimeo.com/api/player.js"></script>
      <script type="text/javascript">
         $('.dropdown-toggle').dropdown()
      </script>
   </body>
</html>