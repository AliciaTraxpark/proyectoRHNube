<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.theme.default.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
</head>

<body>
    <header>
        <div class="container">
            <div class="col-md-5">
                <div class="navbar-brand-wrapper d-flex ">
                    <img src="{{asset('landing/images/logo.png')}}" width="30%" height="30%">
                </div>
            </div>
            <div class="col-md-7">
                <h5>Cuenta Registrada</h5>
            </div>
        </div>
        <div class="banner">
            <div class="container" style="padding-top: 60px"> <br>
                <h4>Hola </h4>
                <span>Creaste una cuenta con RH SOLUTION</span>
                <span>¡Gracias por registrarte!</span>
                <div>
                    <div class="col-md-12 text-center">
                        <span>Para mayor seguridad necesitamos que verifiques tu correo electornico
                            antes de continuar en nustra plataforma.
                        </span>
                    </div>
                </div>
                <div>
                    <div class="col-md-12"> <br><br>
                        <a href="{{ url('/register/verify/' . $confirmation_code) }}"><button
                                class="btn btn-opacity-primary mr-1">
                                VERIFICA TU CORREO ELECTRONICO </button></a>
                    </div>
                </div>
            </div>
        </div>
    </header>
</body>

</html>
