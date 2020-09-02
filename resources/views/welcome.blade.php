<!DOCTYPE html>
<html lang="es">

<head>
    <title>RH nube</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/owl-carousel/css/owl.theme.default.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('landing/vendors/aos/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('landing/css/style.min.css')}}">
    <link rel="shortcut icon" href="https://i.ibb.co/b31CPDW/Recurso-13.png">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-169261172-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-169261172-1');

    </script>
</head>

<body id="body" data-spy="scroll" data-target=".navbar" data-offset="100">
    <header id="header-section">
        <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
            <div class="container pb-3 pt-2">
                <div class="col-md-5">
                    <div class="navbar-brand-wrapper d-flex">
                        <img src="{{asset('landing/images/Recurso 23.png')}}" width="45%" height="45%">
                    </div>
                </div>

                <div class="col-md-7">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="row">

                            <div class="col-md-5 form-group">
                                <label class="blanco">Correo electrónico o
                                    teléfono </label>
                                <input id="email" class="form-control form-control-sm
                                        @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"
                                    required autocomplete="email" autofocus>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="blanco">Contraseña</label>
                                <input tid="password" type="password" class="form-control form-control-sm
                                        @error('password') is-invalid @enderror" name="password" required
                                    autocomplete="current-password">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-3 form-group" style="display:
                                    flex; align-items: center; top: 15px;">
                                <button type="submit" class="boton">Iniciar
                                    sesión</button>

                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    <div class="banner">
        <div class="container" style="padding-top: 60px"> <br>
            <h4 class="font-weight-semibold">Organicemos tu equipo de
                trabajo en 10 minutos: Controla, mide y gestiona.
            </h4>

            <div>
                <div class="col-md-12"> <br><br>
                    <a href="{{route('registroPersona')}}"><button class="btn btn-opacity-primary mr-1">COMIENZA
                            AHORA</button></a>
                </div>

            </div>
            <img src="{{asset('landing/images/i')}}" alt="" class="img-fluid">
        </div>
    </div>
    <div class="content-wrapper">
        <div class="container">
            <section class="features-overview" id="features-section">
                <div class="content-header">
                    <h2>¿Cómo trabaja RHNube?</h2>
                    <h6 class="section-subtitle text-muted">Es la plataforma de control de personal
                         más sencilla y segura del mercado, contrata, agrega y controla personal en menos de 5 minutos.</h6>
                </div>
                <div class="d-md-flex justify-content-between">
                    <div class="grid-margin d-flex justify-content-start">
                        <div class="features-width">
                            <img src="{{asset('landing/images/personal.svg')}}" height="80" alt="" class="img-icons">
                            <h5 class="py-3">Agregar<br>personal</h5>
                            <p class="text-muted">Puedes agregar personal de forma individual o desde un archivo de carga en Excel y luego enviarles una invitación.</p>
                           {{--  <a href="#">
                                <p class="readmore-link">Readmore</p>
                            </a> --}}
                        </div>
                    </div>
                    <div class="grid-margin d-flex justify-content-center">
                        <div class="features-width">
                            <img src="{{asset('landing/images/plataforma.svg')}}" height="80" alt="" class="img-icons">
                            <h5 class="py-3">Dispositivos y <br>licencias</h5>
                            <p class="text-muted">Agrega un punto de control de personal en cualquier plataforma PC, móvil Android o equipo biométrico homologado.</p>
                           {{--  <a href="#">
                                <p class="readmore-link">Readmore</p>
                            </a> --}}
                        </div>
                    </div>
                    <div class="grid-margin d-flex justify-content-end">
                        <div class="features-width">
                            <img src="{{asset('landing/images/reporte.svg')}}" height="80" alt="" class="img-icons">
                            <h5 class="py-3">Monitorea y <br>controla</h5>
                            <p class="text-muted">Obtén información de valor y controla el tiempo invertido en tu personal.</p>
                            {{-- <a href="#">
                                <p class="readmore-link">Readmore</p>
                            </a> --}}
                        </div>
                    </div>
                </div>
            </section>
            {{-- <section class="digital-marketing-service" id="digital-marketing-section">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-7 grid-margin
                            grid-margin-lg-0" data-aos="fade-right">
                        <h3 class="m-0">We Offer a Full Range<br>of Digital
                            Marketing Services!</h3>
                        <div class="col-lg-7 col-xl-6 p-0">
                            <p class="py-4 m-0 text-muted">Lorem ipsum dolor
                                sit amet, tincidunt vestibulum. Fusce
                                egeabus consectetuer turpis, suspendisse.</p>
                            <p class="font-weight-medium text-muted">Lorem
                                ipsum dolor sit amet, tincidunt vestibulum.
                                Fusce egeabus consectetuer</p>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5 p-0 img-digital grid-margin
                            grid-margin-lg-0" data-aos="fade-left">
                        <img src="{{asset('landing/images/Group1.png')}}" alt="" class="img-fluid">
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-12 col-lg-7 text-center flex-item
                            grid-margin" data-aos="fade-right">
                        <img src="{{asset('landing/images/Group2.png')}}" alt="" class="img-fluid">
                    </div>
                    <div class="col-12 col-lg-5 flex-item grid-margin" data-aos="fade-left">
                        <h3 class="m-0">Leading Digital Agency<br>for
                            Business Solution.</h3>
                        <div class="col-lg-9 col-xl-8 p-0">
                            <p class="py-4 m-0 text-muted">Power-packed with
                                impressive features and well-optimized,
                                this template is designed to provide the
                                best performance in all circumstances.</p>
                            <p class="pb-2 font-weight-medium text-muted">Its
                                smart features make it a powerful
                                stand-alone website building tool.</p>
                        </div>
                        <button class="btn btn-info">Readmore</button>
                    </div>
                </div>
            </section>
            <section class="case-studies" id="case-studies-section">
                <div class="row grid-margin">
                    <div class="col-12 text-center pb-5">
                        <h2>Our case studies</h2>
                        <h6 class="section-subtitle text-muted">Lorem ipsum
                            dolor sit amet, tincidunt vestibulum.</h6>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 stretch-card mb-3
                            mb-lg-0" data-aos="zoom-in">
                        <div class="card color-cards">
                            <div class="card-body p-0">
                                <div class="bg-primary text-center
                                        card-contents">
                                    <div class="card-image">
                                        <img src="{{asset('landing/images/Group95.svg')}}" class="case-studies-card-img"
                                            alt="">
                                    </div>
                                    <div class="card-desc-box d-flex
                                            align-items-center
                                            justify-content-around">
                                        <div>
                                            <h6 class="text-white pb-2
                                                    px-3">Know more about Online
                                                marketing</h6>
                                            <button class="btn btn-white">Read
                                                More</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-details text-center pt-4">
                                    <h6 class="m-0 pb-1">Online Marketing</h6>
                                    <p>Seo, Marketing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 stretch-card mb-3
                            mb-lg-0" data-aos="zoom-in" data-aos-delay="200">
                        <div class="card color-cards">
                            <div class="card-body p-0">
                                <div class="bg-warning text-center
                                        card-contents">
                                    <div class="card-image">
                                        <img src="{{asset('landing/images/Group108.svg')}}"
                                            class="case-studies-card-img" alt="">
                                    </div>
                                    <div class="card-desc-box d-flex
                                            align-items-center
                                            justify-content-around">
                                        <div>
                                            <h6 class="text-white pb-2
                                                    px-3">Know more about Web
                                                Development</h6>
                                            <button class="btn btn-white">Read
                                                More</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-details text-center pt-4">
                                    <h6 class="m-0 pb-1">Web Development</h6>
                                    <p>Developing, Designing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 stretch-card mb-3
                            mb-lg-0" data-aos="zoom-in" data-aos-delay="400">
                        <div class="card color-cards">
                            <div class="card-body p-0">
                                <div class="bg-violet text-center
                                        card-contents">
                                    <div class="card-image">
                                        <img src="{{asset('landing/images/Group126.svg')}}"
                                            class="case-studies-card-img" alt="">
                                    </div>
                                    <div class="card-desc-box d-flex
                                            align-items-center
                                            justify-content-around">
                                        <div>
                                            <h6 class="text-white pb-2
                                                    px-3">Know more about Web
                                                Designing</h6>
                                            <button class="btn btn-white">Read
                                                More</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-details text-center pt-4">
                                    <h6 class="m-0 pb-1">Web Designing</h6>
                                    <p>Designing, Developing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 stretch-card" data-aos="zoom-in" data-aos-delay="600">
                        <div class="card color-cards">
                            <div class="card-body p-0">
                                <div class="bg-success text-center
                                        card-contents">
                                    <div class="card-image">
                                        <img src="{{asset('landing/images/Group115.svg')}}"
                                            class="case-studies-card-img" alt="">
                                    </div>
                                    <div class="card-desc-box d-flex
                                            align-items-center
                                            justify-content-around">
                                        <div>
                                            <h6 class="text-white pb-2
                                                    px-3">Know more about
                                                Software Development</h6>
                                            <button class="btn btn-white">Read
                                                More</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-details text-center pt-4">
                                    <h6 class="m-0 pb-1">Software
                                        Development</h6>
                                    <p>Developing, Designing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="customer-feedback" id="feedback-section">
                <div class="row">
                    <div class="col-12 text-center pb-5">
                        <h2>What our customers have to say</h2>
                        <h6 class="section-subtitle text-muted m-0">Lorem
                            ipsum dolor sit amet, tincidunt vestibulum.
                        </h6>
                    </div>
                    <div class="owl-carousel owl-theme grid-margin">
                        <div class="card customer-cards">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{asset('landing/images/face2.jpg')}}" width="89" height="89" alt=""
                                        class="img-customer">
                                    <p class="m-0 py-3 text-muted">Lorem
                                        ipsum dolor sit amet, tincidunt
                                        vestibulum.
                                        Fusce egeabus consectetuer turpis,
                                        suspendisse.</p>
                                    <div class="content-divider m-auto"></div>
                                    <h6 class="card-title pt-3">Tony
                                        Martinez</h6>
                                    <h6 class="customer-designation
                                            text-muted m-0">Marketing Manager</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card customer-cards">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{asset('landing/images/face3.jpg')}}" width="89" height="89" alt=""
                                        class="img-customer">
                                    <p class="m-0 py-3 text-muted">Lorem
                                        ipsum dolor sit amet, tincidunt
                                        vestibulum.
                                        Fusce egeabus consectetuer turpis,
                                        suspendisse.</p>
                                    <div class="content-divider m-auto"></div>
                                    <h6 class="card-title pt-3">Sophia
                                        Armstrong</h6>
                                    <h6 class="customer-designation
                                            text-muted m-0">Marketing Manager</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card customer-cards">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{asset('landing/images/face20.jpg')}}" width="89" height="89" alt=""
                                        class="img-customer">
                                    <p class="m-0 py-3 text-muted">Lorem
                                        ipsum dolor sit amet, tincidunt
                                        vestibulum.
                                        Fusce egeabus consectetuer turpis,
                                        suspendisse.</p>
                                    <div class="content-divider m-auto"></div>
                                    <h6 class="card-title pt-3">Cody Lambert</h6>
                                    <h6 class="customer-designation
                                            text-muted m-0">Marketing Manager</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card customer-cards">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{asset('landing/images/face15.jpg')}}" width="89" height="89" alt=""
                                        class="img-customer">
                                    <p class="m-0 py-3 text-muted">Lorem
                                        ipsum dolor sit amet, tincidunt
                                        vestibulum.
                                        Fusce egeabus consectetuer turpis,
                                        suspendisse.</p>
                                    <div class="content-divider m-auto"></div>
                                    <h6 class="card-title pt-3">Cody Lambert</h6>
                                    <h6 class="customer-designation
                                            text-muted m-0">Marketing Manager</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card customer-cards">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{asset('landing/images/face16.jpg')}}" width="89" height="89" alt=""
                                        class="img-customer">
                                    <p class="m-0 py-3 text-muted">Lorem
                                        ipsum dolor sit amet, tincidunt
                                        vestibulum.
                                        Fusce egeabus consectetuer turpis,
                                        suspendisse.</p>
                                    <div class="content-divider m-auto"></div>
                                    <h6 class="card-title pt-3">Cody Lambert</h6>
                                    <h6 class="customer-designation
                                            text-muted m-0">Marketing Manager</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card customer-cards">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{asset('landing/images/face1.jpg')}}" width="89" height="89" alt=""
                                        class="img-customer">
                                    <p class="m-0 py-3 text-muted">Lorem
                                        ipsum dolor sit amet, tincidunt
                                        vestibulum.
                                        Fusce egeabus consectetuer turpis,
                                        suspendisse.</p>
                                    <div class="content-divider m-auto"></div>
                                    <h6 class="card-title pt-3">Tony
                                        Martinez</h6>
                                    <h6 class="customer-designation
                                            text-muted m-0">Marketing Manager</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card customer-cards">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{asset('landing/images/face2.jpg')}}" width="89" height="89" alt=""
                                        class="img-customer">
                                    <p class="m-0 py-3 text-muted">Lorem
                                        ipsum dolor sit amet, tincidunt
                                        vestibulum.
                                        Fusce egeabus consectetuer turpis,
                                        suspendisse.</p>
                                    <div class="content-divider m-auto"></div>
                                    <h6 class="card-title pt-3">Tony
                                        Martinez</h6>
                                    <h6 class="customer-designation
                                            text-muted m-0">Marketing Manager</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card customer-cards">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{asset('landing/images/face3.jpg')}}" width="89" height="89" alt=""
                                        class="img-customer">
                                    <p class="m-0 py-3 text-muted">Lorem
                                        ipsum dolor sit amet, tincidunt
                                        vestibulum.
                                        Fusce egeabus consectetuer turpis,
                                        suspendisse.</p>
                                    <div class="content-divider m-auto"></div>
                                    <h6 class="card-title pt-3">Sophia
                                        Armstrong</h6>
                                    <h6 class="customer-designation
                                            text-muted m-0">Marketing Manager</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card customer-cards">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{asset('landing/images/face20.jpg')}}" width="89" height="89" alt=""
                                        class="img-customer">
                                    <p class="m-0 py-3 text-muted">Lorem
                                        ipsum dolor sit amet, tincidunt
                                        vestibulum.
                                        Fusce egeabus consectetuer turpis,
                                        suspendisse.</p>
                                    <div class="content-divider m-auto"></div>
                                    <h6 class="card-title pt-3">Cody Lambert</h6>
                                    <h6 class="customer-designation
                                            text-muted m-0">Marketing Manager</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="contact-us" id="contact-section">
                <div class="contact-us-bgimage grid-margin">
                    <div class="pb-4">
                        <h4 class="px-3 px-md-0 m-0" data-aos="fade-down">Do
                            you have any projects?</h4>
                        <h4 class="pt-1" data-aos="fade-down">Contact us</h4>
                    </div>
                    <div data-aos="fade-up">
                        <button class="btn btn-rounded btn-outline-danger">Contact
                            us</button>
                    </div>
                </div>
            </section> --}}
            {{-- <section class="contact-details" id="contact-details-section">
                <div class="row text-center text-md-left">
                    <div class="col-12 col-md-6 col-lg-3 grid-margin">
                        <img src="{{asset('landing/images/Group2.svg')}}" alt="" class="pb-2">
                        <div class="pt-2">
                            <p class="text-muted m-0">mikayla_beer@feil.name</p>
                            <p class="text-muted m-0">906-179-8309</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 grid-margin">
                        <h5 class="pb-2">Get in Touch</h5>
                        <p class="text-muted">Don’t miss any updates of our
                            new templates and extensions.!</p>
                        <form>
                            <input type="text" class="form-control" id="Email" placeholder="Email id">
                        </form>
                        <div class="pt-3">
                            <button class="btn btn-dark">Subscribe</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 grid-margin">
                        <h5 class="pb-2">Our Guidelines</h5>
                        <a href="#">
                            <p class="m-0 pb-2">Terms</p>
                        </a>
                        <a href="#">
                            <p class="m-0 pt-1 pb-2">Privacy policy</p>
                        </a>
                        <a href="#">
                            <p class="m-0 pt-1 pb-2">Cookie Policy</p>
                        </a>
                        <a href="#">
                            <p class="m-0 pt-1">Discover</p>
                        </a>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 grid-margin">
                        <h5 class="pb-2">Our address</h5>
                        <p class="text-muted">518 Schmeler Neck<br>Bartlett.
                            Illinois</p>
                        <div class="d-flex justify-content-center
                                justify-content-md-start">
                            <a href="#"><span class="mdi mdi-facebook"></span></a>
                            <a href="#"><span class="mdi mdi-twitter"></span></a>
                            <a href="#"><span class="mdi mdi-instagram"></span></a>
                            <a href="#"><span class="mdi mdi-linkedin"></span></a>
                        </div>
                    </div>
                </div>
            </section> --}}
            <footer class="border-top">
                <p class="text-center text-muted pt-4">© <?php echo date("
                            Y" ); ?> - RH Solution | Todos los derechos
                    reservados.</p>
            </footer>
            <!-- Modal for Contact - us Button -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel">Contact Us</h4>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label for="Name">Name</label>
                                    <input type="text" class="form-control" id="Name" placeholder="Name">
                                </div>
                                <div class="form-group">
                                    <label for="Email">Email</label>
                                    <input type="email" class="form-control" id="Email-1" placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <label for="Message">Message</label>
                                    <textarea class="form-control" id="Message" placeholder="Enter your
                                                Message"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                            <button type="button" class="btn
                                        btn-success">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (session('error'))
    <div class="modal" id="modal1" role="dialog" style="display:
                block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center" style="padding-top: 8px;
                            padding-bottom: 5px;background-color:
                            #163552;color:#ffffff">
                    <h6 style="font-size: 14px" class="modal-title">Advertencia</h6>
                </div>
                <div class="modal-body text-center">
                    <img src="{{asset('landing/images/alert.svg')}}" height="25" class="mr-1 mt-2">
                    <p>{{ session('error') }}</p>
                </div>
                <div class="modal-footer text-center" style="padding-top: 5px;
                            padding-bottom: 5px;">
                    <button type="button" onclick="cerrarModalAdvertencia()" class="btn
                                btn-sm" style="background-color:
                                #163552;color:#ffffff" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script src="{{asset('landing/vendors/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing/vendors/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{asset('landing/vendors/owl-carousel/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('landing/vendors/aos/js/aos.js')}}"></script>
    <script src="{{asset('landing/js/landingpage.js')}}"></script>



    @if (session('mensaje'))
    <div class="modal" id="modal" role="dialog" style="display: block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding-top: 8px;
                            padding-bottom: 5px;background-color:
                            #163552;color:#ffffff">
                    <h5 style="font-size: 14px" class="modal-title">CONFIRMACION</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="cerrarModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{asset('admin/images/tick.svg')}}" height="25" class="mr-1 mt-2">
                    <p>{{ session('mensaje') }}</p>
                </div>
                <div class="modal-footer" style="padding-top: 8px;
                            padding-bottom: 8px;">
                    <button type="button" class="btn
                    btn-sm" style="background-color:
                    #163552;color:#ffffff" onclick="cerrarModal()">OK</button>
                </div>
            </div>
        </div>
    </div>

    @endif
    <script>
        function cerrarModalAdvertencia(){
            document.getElementById("modal1").style.display = "none";
        }
    </script>
      <div class="modal" id="modalInv" role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding-top: 8px;
                            padding-bottom: 5px;background-color:
                            #163552;color:#ffffff">
                    <h5 style="font-size: 14px" class="modal-title">CONFIRMACION</h5>
                    <button type="button" class="close" data-dismiss="modal" onclick="cerrarModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{asset('admin/images/tick.svg')}}" height="25" class="mr-1 mt-2">
                    <p>Bien hecho, estas registrado! Te hemos enviado un correo de verificación.</p>
                </div>
                <div class="modal-footer" style="padding-top: 8px;
                            padding-bottom: 8px;">
                    <button type="button" class="btn
                    btn-sm" style="background-color:
                    #163552;color:#ffffff" onclick="cerrarModal()">OK</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function cerrarModal() {
            document.getElementById("modalInv").style.display = "none";
            document.getElementById("modal").style.display = "none";
        }

    </script>
</body>

</html>
