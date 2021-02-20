@php
use App\invitado;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Políticas de privacidad</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('landing/home/css/lib/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/home/css/lib/fontawesome-all.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/home/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('landing/home/images/Logo.png') }}">
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style type="text/css">
        #accordion{
            margin-top: 70px !important;
            margin-bottom: 120px !important;
        }
        footer{
            position: fixed;
            bottom: 0;
            width: 100%;

        }
    </style>
</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
    <header id="header-section">
        <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
            <div class="container">
                <div class="col-1 text-left">
                    <a href="{{ route('principal') }}"><img src="{{asset('landing/images/NUBE_SOLA.png')}}" class="" height="69"></a>
                </div>   
                <div class="col-7 text-left">
                    <strong id="" style="color:rgb(255, 255, 255)">Política de privacidad</strong>
                </div>   
                <div class="col-4 text-right">
                    <strong id="" style="color:rgb(255, 255, 255)"> 
                        <a href="\"><img src="{{asset('landing/images/logout.png')}}" style="color: white;" width="30px"></a> 
                    </strong>
                </div>   
            </div>
        </nav>
    </header>

    <div class="container mt-5 mb-5">
        <div id="accordion">
            <div class="card">
               <div class="card-header" id="headingOne">
                  <h5 class="mb-0">
                     <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                     Introducción
                     </button>
                  </h5>
               </div>
               <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                  <div class="card-body">
                     La presente Política de Privacidad establece los términos en que RH NUBE usa y protege la información que proporcionan los usuarios al momento de manipular nuestra plataforma. <br><br>

                    Al utilizar nuestro sitio web nos confías tus datos y entendemos nuestra responsabilidad, siendo conscientes de la rigurosa privacidad de nuestros usuarios y la información personal que nos confían, por eso nos esforzamos al máximo para proteger y controlar la información brindada. <br><br>

                    El objetivo de esta Política de Privacidad es informarte sobre qué datos recogemos, por qué los recogemos y cómo puedes actualizarlos, gestionarlos, exportarlos y eliminarlos.
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header" id="headingTwo">
                  <h5 class="mb-0">
                     <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                     ¿Qué tipo de información recopilamos?
                     </button>
                  </h5>
               </div>
               <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                  <div class="card-body">
                     En nuestros sitios web, existen unos apartados específicos donde anotas tus datos en el proceso de registro y a la hora de realizar pagos y/o pedidos. Nosotros te aseguramos que la información que nos facilites será gestionada de forma totalmente confidencial. <br><br>

                    En nuestra plataforma recogemos información para proporcionar las funcionalidades necesarias. <br><br>

                    Nuestro sitio web podrá recoger información personal, por ejemplo: nombre, apellidos, información de contacto como su dirección de correo electrónica o número de teléfono e información demográfica. Así mismo cuando sea necesario podrá ser requerida información específica para procesar algún pedido o realizar una entrega o facturación. <br><br>

                    Los datos se almacenarán mientras exista previsión de su uso para el fin por el que fueron recabados. La web puede utilizar cookies, consulta nuestra política de cookies. <br><br>

                    Para la aplicación móvil "Modo en ruta": Se obtiene la ubicación GPS en segundo plano cada una cantidad de minutos determinados por el administrador de la plataforma, esta información es almacenada con la finalidad de otorgar la funcionalidad de rastreo contratada por el o los usuarios administradores. 
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header" id="headingThree">
                  <h5 class="mb-0">
                     <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                     Legitimación de tratamiento de datos
                     </button>
                  </h5>
               </div>
               <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                  <div class="card-body">
                     El uso de tus datos se realiza porque nos das tu consentimiento para usar los que nos proporcionas en los formularios para un uso específico que se indica en cada uno de ellos. Tus datos solo son necesarios para los usos concretos por los que se te solicitan.
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header" id="headingFour">
                  <h5 class="mb-0">
                     <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                     Transferencias y cesión de datos
                     </button>
                  </h5>
               </div>
               <div id="collapseFour" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                  <div class="card-body">
                     Existe un compromiso firme por nuestra parte de que los datos que proporcione a RH NUBE CORP, no serán vendidos ni cedidos a terceras personas sin el previo consentimiento del interesado bajo ningún concepto o circunstancia, salvo consentimiento expreso u obligación legal. <br><br>

                    En caso la plataforma web contenga enlaces hacia sitios web de terceros, RH NUBE CORP no se hace responsable por las políticas y prácticas de privacidad de estos otros sitios. Dichos sitios están sujetos a sus propias políticas de privacidad por lo cual es recomendable que los consulte para confirmar que usted está de acuerdo con estas.
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header" id="headingFive">
                  <h5 class="mb-0">
                     <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                     Control de información personal
                     </button>
                  </h5>
               </div>
               <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion">
                  <div class="card-body">
                     Tienes el derecho de acceder a tu información almacenada en nuestras bases de datos, rectificarla si existiera alguna errata, suprimirla, limitarla, oponerte a su tratamiento y retirar tu consentimiento si ese es tu deseo. Para ello simplemente debes escribir un e-mail a la dirección de correo electrónico info@rhnube.com.pe donde te atenderemos gustosamente cualquier consulta, comentario o aclaración requerida al respecto.
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header" id="headingSix">
                  <h5 class="mb-0">
                     <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                     Cambios de política de privacidad
                     </button>
                  </h5>
               </div>
               <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordion">
                  <div class="card-body">
                     RH NUBE CORP se reserva el derecho de cambiar los términos de la presente Política de Privacidad en el momento que se amerite. Modificamos esta Política de Privacidad de forma periódica. <br><br>

                    No limitaremos los derechos que se te hayan concedido de acuerdo con esta Política de Privacidad sin tu consentimiento explícito. Si los cambios son significativos, te lo comunicaremos de forma destacada enviando una notificación de los cambios en la Política de Privacidad por correo electrónico. <br><br>
                    Actualizado el 01/10/2020 17:25
                  </div>
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
</body>

</html>
