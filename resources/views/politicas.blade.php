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
    <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <!-- App css -->
    <link href="{{asset('admin/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />

</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">

    <style>
        /*  body > div.bootbox.modal.fade.show > div > div > div{
        background: #131313;
    color: #fbfbfb;
    }
    body > div.bootbox.modal.fade.show > div{
        top: 100px;
    left: 75px;
    } */

        body{
            font-family: Poppins, sans-serif;
        }

        .card .card-body {
            padding: 20px 20px;
        }

        .body {
            background-color: #fbfbfb;
        }

        label{
            font-weight: 100;
        }

        .nav{
            background-color: #e7e7e7 !important;
            margin: 0;
            padding: 0;
        }

        .nav-link{
            color: black !important;
            border: black 1px solid !important;
            margin: 0px;
        }

        .container{
            max-width: 90% !important;
        }

        @media (max-width: 767px) {
            .navbar {
                padding: 0% !important;
            }
            #cuerpoPo{
                padding-left: 12px!important;
            }
            .container {
                padding-bottom: 3% !important;
                padding-left: 10px !important;
                padding-right: 10px !important;
            }

            .colResp {
                justify-content: center !important;
                padding: 10px 0px !important;
            }

            .textResp {
                text-align: center !important;
            }

            .content-page {
                margin-right: 10px !important;
                margin-left: 10px !important;
                margin-top: 10px !important;
            }

            .align-items-center {
                text-align: center !important;
            }

            .logout{
                display: none;
            }

            .logo_rh{
                padding: 0;
            }
        }

        @media (min-width: 767px) and (max-width: 1200px) {
            .logo_rh{
                padding-left: 160px;
            }
            .title_rh{
                padding-left: 40px;
            }
            .logout{
                padding-left: 40px;
            }
        }

        @media (min-width: 1200px) and (max-width: 1800px) {
            .logo_rh{
                padding-left: 180px;
            }
            .title_rh{
                padding-left: 120px;
            }
            .logout{
                padding-left: 80px;
            }
        }

        @media (min-width: 1800px) {
            .logo_rh{
                padding-left: 200px;
            }
            .title_rh{
                padding-left: 120px;
            }
            .logout{
                padding-left: 130px;
            }
        }

        @media (min-width: 0px) {
            footer {
              font-size: 15px;
              color: #555;
              background: #eee;
              text-align: center;
              position: fixed;
              display: block;
              width: 100%;
              bottom: 0;
            }
        }
    </style>
    <header id="header-section">
        <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
                <div class="col-md-5 col-xl-4 logo_rh">
                    <div class="navbar-brand-wrapper d-flex w-100 colResp">
                        <img src="{{asset('landing/images/NUBE_SOLA.png')}}" class="" height="69">
                    </div>
                </div>
                <div class="col-md-5 col-xl-6 text-left textResp title_rh">
                    <strong id="" style="color:
                    rgb(255, 255, 255)">Política de privacidad
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </div>

                <div class="col-md-2 col-xl-2 text-left textResp logout">
                    <strong id="" style="color:
                    rgb(255, 255, 255)"> <a href="\"><img src="{{asset('landing/images/logout.png')}}" style="color: white;" width="30px"></a> 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
                </div>
        </nav>
    </header>

    <div class="container">
        <div class="content-page" style="margin-top: 40px; margin-left: 100px; margin-right: 55px;padding-left: 0px;">
        <div class="content">
            <div class="row" style="color: black !important">
                <div class=" col-12 col-md-4" id="navPo">
                  <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#introduccion" role="tab" aria-controls="introduccion" aria-selected="true">Introducción</a>
                    <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#tipo-informacion" role="tab" aria-controls="tipo-informacion" aria-selected="false">¿Qué tipo de información recopilamos?</a>
                    <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#tratamiento-datos" role="tab" aria-controls="tratamiento-datos" aria-selected="false">Legitimación del tratamiento de datos</a>
                    <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill" href="#transferencia-datos" role="tab" aria-controls="transferencia-datos" aria-selected="false">Transferencias y cesiones de datos</a>
                    <a class="nav-link" id="v-pills-messages-tab1" data-toggle="pill" href="#informacion-personal" role="tab" aria-controls="informacion-personal" aria-selected="false">Control de su información personal</a>
                    <a class="nav-link" id="v-pills-settings-tab1" data-toggle="pill" href="#cambios-politica" role="tab" aria-controls="cambios-politica" aria-selected="false">Cambios en la política de privacidad</a>
                  </div>
                </div>

                <div class="col-12 col-md-8" id="cuerpoPo" style=" padding-left: 50px;   ">
                  <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="introduccion" role="tabpanel" aria-labelledby="v-pills-home-tab">
                        <label for="" style="text-align: justify;">  
                            La presente Política de Privacidad establece los términos en que RH NUBE usa y protege la información que proporcionan los usuarios al momento de manipular nuestra plataforma. <br><br>

                            Al utilizar nuestro sitio web nos confías tus datos y entendemos nuestra responsabilidad, siendo conscientes de la rigurosa privacidad de nuestros usuarios y la información personal que nos confían, por eso nos esforzamos al máximo para proteger y controlar la información brindada. <br><br>

                             El objetivo de esta Política de Privacidad es informarte sobre qué datos recogemos, por qué los recogemos y cómo puedes actualizarlos, gestionarlos, exportarlos y eliminarlos. 
                        </label>


                    </div>
                    <div class="tab-pane fade" id="tipo-informacion" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                        <label for="" style="text-align: justify">
                            En nuestros sitios web, existen unos apartados específicos donde anotas tus datos en el proceso de registro y a la hora de realizar pagos y/o pedidos. Nosotros te aseguramos que la información que nos facilites será gestionada de forma totalmente confidencial. <br><br>
                            En nuestra plataforma recogemos información para proporcionar las funcionalidades necesarias. <br><br>

                            Nuestro sitio web podrá recoger información personal, por ejemplo: nombre, apellidos, información de contacto como su dirección de correo electrónica o número de teléfono e información demográfica. Así mismo cuando sea necesario podrá ser requerida información específica para procesar algún pedido o realizar una entrega o facturación. <br><br>

                            Los datos se almacenarán mientras exista previsión de su uso para el fin por el que fueron recabados. La web puede utilizar cookies, consulta nuestra política de cookies. <br><br>

                        </label>
                    </div>
                    <div class="tab-pane fade" id="tratamiento-datos" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                        <label for="" style="text-align: justify">
                            El uso de tus datos se realiza porque nos das tu consentimiento para usar los que nos proporcionas en los formularios para un uso específico que se indica en cada uno de ellos. Tus datos solo son necesarios para los usos concretos por los que se te solicitan.
                        </label>
                    </div>
                    <div class="tab-pane fade" id="transferencia-datos" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                        <label for="" style="text-align: justify">
                            Existe un compromiso firme por nuestra parte de que los datos que proporcione a RH NUBE CORP, no serán vendidos ni cedidos a terceras personas sin el previo consentimiento del interesado bajo ningún concepto o circunstancia, salvo consentimiento expreso u obligación legal.  <br><br>

                            En caso la plataforma web contenga enlaces hacia sitios web de terceros, RH NUBE CORP no se hace responsable por las políticas y prácticas de privacidad de estos otros sitios. Dichos sitios están sujetos a sus propias políticas de privacidad por lo cual es recomendable que los consulte para confirmar que usted está de acuerdo con estas.

                        </label>
                    </div>
                    <div class="tab-pane fade" id="informacion-personal" role="tabpanel" aria-labelledby="v-pills-messages-tab1">
                        <label for="" style="text-align: justify">
                            Tienes el derecho de acceder a tu información almacenada en nuestras bases de datos, rectificarla si existiera alguna errata, suprimirla, limitarla, oponerte a su tratamiento y retirar tu consentimiento si ese es tu deseo. Para ello simplemente debes escribir un e-mail a la dirección de correo electrónico info@rhnube.com.pe donde te atenderemos gustosamente cualquier consulta, comentario o aclaración requerida al respecto. 
                        </label>
                    </div>
                    <div class="tab-pane fade" id="cambios-politica" role="tabpanel" aria-labelledby="v-pills-settings-tab1">
                        <label for="" style="text-align: justify">
                            RH NUBE CORP se reserva el derecho de cambiar los términos de la presente Política de Privacidad en el momento que se amerite. Modificamos esta Política de Privacidad de forma periódica.  <br><br>

                            No limitaremos los derechos que se te hayan concedido de acuerdo con esta Política de Privacidad sin tu consentimiento explícito. Si los cambios son significativos, te lo comunicaremos de forma destacada enviando una notificación de los cambios en la Política de Privacidad por correo electrónico.

                        </label><br>
                        <label for="" style="text-align: justify">Actualizado el 01/10/2020 17:25</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
    </div>

    

    <footer class="border-top" style="background:#163552; margin-top: 15px;">
        <div class="col-md-12 text-center"
            style="margin-top: 10px;border-top: 1.5px solid #ded9d9;padding-top: 10px;bottom: 10px;">
            <span style="color: #faf3f3;font-size: 12px!important">
                © <?php echo date("
                    Y" ); ?> - RH nube Corp - USA | Todos los derechos
                reservados &nbsp; |
            </span>
            <a style="font-size: 12px!important; color:#faf3f3;" href="/politicas">Política de privacidad | </a>
            <span style="color: #faf3f3;font-size: 12px!important">Central Perú: 017482415 | +51 914480786 | info@rhnube.com.pe</span>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>

    <!-- Vendor js -->
    {{-- <script src="{{asset('admin/assets/js/vendor.min.js')}}"></script> --}}

    <!-- plugin js -->

    <script src="{{ URL::asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>

</body>

</html>
